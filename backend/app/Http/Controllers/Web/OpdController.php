<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OpdController extends Controller
{
    public function queue(Request $request)
    {
        $clinicId = auth()->user()->clinic_id;
        $date = $request->get('date', today()->toDateString());

        Log::info('OpdController@queue', [
            'clinic_id' => $clinicId,
            'date' => $date,
            'doctor_id' => $request->input('doctor_id'),
        ]);

        $this->assignMissingTokensForDate($clinicId, $date);

        $query = Appointment::with(['patient', 'doctor'])
            ->where('clinic_id', $clinicId)
            ->whereDate('scheduled_at', $date);

        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->integer('doctor_id'));
        }

        $appointments = $query
            ->orderByRaw('token_number IS NULL, token_number ASC')
            ->orderBy('scheduled_at')
            ->get()
            ->map(function ($appt) {
                // Queue strip maps DB enum → UI bucket (see appointments.status enum in migrations)
                $appt->queue_status = match ($appt->status) {
                    'booked', 'confirmed', 'checked_in' => 'waiting',
                    'in_consultation' => 'in_progress',
                    'completed' => 'done',
                    default => is_string($appt->status) ? $appt->status : 'waiting',
                };

                return $appt;
            });

        $visitByAppointmentId = collect();
        if (Schema::hasTable('visits') && $appointments->isNotEmpty()) {
            $ids = $appointments->pluck('id')->all();
            $visitByAppointmentId = Visit::query()
                ->where('clinic_id', $clinicId)
                ->whereIn('appointment_id', $ids)
                ->orderByDesc('id')
                ->get()
                ->unique('appointment_id')
                ->keyBy('appointment_id');
            Log::debug('OpdController@queue visit map', [
                'appointment_ids' => count($ids),
                'visits_found' => $visitByAppointmentId->count(),
            ]);
        }

        $stats = [
            'total'       => $appointments->count(),
            'waiting'     => $appointments->where('queue_status', 'waiting')->count(),
            'in_progress' => $appointments->where('status', 'in_consultation')->count(),
            'completed'   => $appointments->where('queue_status', 'done')->count(),
            'cancelled'   => $appointments->whereIn('status', ['cancelled', 'no_show'])->count(),
        ];

        $doctors = User::where('clinic_id', $clinicId)
            ->whereIn('role', ['doctor', 'owner'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $patients = Patient::where('clinic_id', $clinicId)
            ->orderBy('name')
            ->get(['id', 'name', 'phone']);

        return view('opd.queue', compact('appointments', 'stats', 'date', 'doctors', 'patients', 'visitByAppointmentId'));
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'status' => 'required|in:booked,confirmed,checked_in,in_consultation,completed,cancelled,no_show,rescheduled',
        ]);

        $appointment->update(['status' => $validated['status']]);
        Log::info('OpdController@updateStatus', [
            'appointment_id' => $appointment->id,
            'status' => $validated['status'],
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'status' => $validated['status']]);
        }

        return back()->with('success', 'Status updated');
    }

    public function walkin(Request $request, WhatsAppService $whatsApp)
    {
        $clinicId = auth()->user()->clinic_id;

        if ($request->filled('appointment_date') && $request->filled('appointment_time')) {
            $time = (string) $request->appointment_time;
            if (strlen($time) === 5) {
                $time .= ':00';
            }
            $request->merge([
                'scheduled_at' => trim($request->appointment_date.' '.$time),
            ]);
        }

        Log::info('OpdController@walkin', [
            'has_scheduled_at' => $request->filled('scheduled_at'),
            'patient_id' => $request->input('patient_id'),
        ]);

        $validated = $request->validate([
            'patient_id'      => 'required|exists:patients,id',
            'doctor_id'       => 'required|exists:users,id',
            'chief_complaint' => 'nullable|string|max:500',
            'scheduled_at'    => 'required|date',
        ]);

        $doctor = User::find($validated['doctor_id']);
        $specialty = $doctor && filled($doctor->specialty)
            ? $doctor->specialty
            : 'general';

        // DB enum appointments.booking_source: clinic_staff, online_booking, whatsapp, phone, walk_in (not "walkin")
        // DB enum appointments.appointment_type: new, followup, procedure, teleconsultation
        $create = [
            'clinic_id'         => $clinicId,
            'patient_id'        => $validated['patient_id'],
            'doctor_id'         => $validated['doctor_id'],
            'scheduled_at'      => $validated['scheduled_at'],
            'status'            => 'confirmed',
            'appointment_type'  => 'new',
            'booking_source'    => 'walk_in',
            'specialty'         => $specialty,
            'notes'             => $validated['chief_complaint'] ?? null,
        ];

        if (Schema::hasColumn('appointments', 'chief_complaint') && !empty($validated['chief_complaint'])) {
            $create['chief_complaint'] = $validated['chief_complaint'];
        }

        try {
            $appointment = Appointment::create($create);
            $dateStr = \Carbon\Carbon::parse($appointment->scheduled_at)->toDateString();
            $this->assignMissingTokensForDate($clinicId, $dateStr);
            $appointment->refresh();
            $appointment->load(['patient', 'doctor']);
            Log::info('OpdController@walkin created appointment', [
                'clinic_id' => $clinicId,
                'specialty' => $specialty,
                'appointment_id' => $appointment->id,
                'token_number' => $appointment->token_number,
            ]);

            try {
                $clinic = auth()->user()->clinic;
                $whatsApp->notifyOpdQueue($appointment->patient, $appointment, $clinic);
            } catch (\Throwable $e) {
                Log::warning('OpdController@walkin: OPD WhatsApp failed', [
                    'appointment_id' => $appointment->id,
                    'error' => $e->getMessage(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('OpdController@walkin failed', ['error' => $e->getMessage(), 'create_keys' => array_keys($create)]);

            return redirect()
                ->route('opd.queue')
                ->withInput()
                ->with('error', 'Could not add walk-in: '.$e->getMessage());
        }

        return redirect()->route('opd.queue')->with('success', 'Walk-in patient added to queue');
    }

    /**
     * Phase C — daily OPD register (printable list + same data as queue).
     */
    public function register(Request $request)
    {
        $clinicId = auth()->user()->clinic_id;
        $date = $request->get('date', today()->toDateString());
        Log::info('OpdController@register', ['clinic_id' => $clinicId, 'date' => $date]);

        $this->assignMissingTokensForDate($clinicId, $date);

        $appointments = Appointment::with(['patient', 'doctor'])
            ->where('clinic_id', $clinicId)
            ->whereDate('scheduled_at', $date)
            ->orderByRaw('token_number IS NULL, token_number ASC')
            ->orderBy('scheduled_at')
            ->get();

        return view('opd.register', compact('appointments', 'date'));
    }

    /**
     * CSV export for OPD register (Phase C).
     */
    public function exportRegister(Request $request): StreamedResponse
    {
        $clinicId = auth()->user()->clinic_id;
        $date = $request->get('date', today()->toDateString());
        $this->assignMissingTokensForDate($clinicId, $date);

        $rows = Appointment::with(['patient', 'doctor'])
            ->where('clinic_id', $clinicId)
            ->whereDate('scheduled_at', $date)
            ->orderByRaw('token_number IS NULL, token_number ASC')
            ->orderBy('scheduled_at')
            ->get();

        $fn = 'opd-register-'.$date.'.csv';
        Log::info('OpdController@exportRegister', ['rows' => $rows->count(), 'date' => $date]);

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Token', 'Time', 'Patient', 'Phone', 'Doctor', 'Department', 'Status']);
            foreach ($rows as $a) {
                fputcsv($out, [
                    $a->token_number ?? '',
                    $a->scheduled_at ? $a->scheduled_at->format('H:i') : '',
                    $a->patient->name ?? '',
                    $a->patient->phone ?? '',
                    $a->doctor->name ?? '',
                    $a->opd_department ?? '',
                    $a->status ?? '',
                ]);
            }
            fclose($out);
        }, $fn, ['Content-Type' => 'text/csv']);
    }

    public function updateDepartment(Request $request, Appointment $appointment)
    {
        abort_unless(auth()->user()->clinic_id === $appointment->clinic_id, 403);

        if (! Schema::hasColumn('appointments', 'opd_department')) {
            return back()->with('error', 'OPD department column not migrated yet.');
        }

        $validated = $request->validate([
            'opd_department' => 'nullable|string|max:120',
        ]);
        $appointment->update(['opd_department' => $validated['opd_department']]);
        Log::info('OpdController@updateDepartment', ['appointment_id' => $appointment->id]);

        return back()->with('success', 'Department updated.');
    }

    private function assignMissingTokensForDate(int $clinicId, string $date): void
    {
        if (! Schema::hasTable('appointments') || ! Schema::hasColumn('appointments', 'token_number')) {
            return;
        }

        $ids = Appointment::query()
            ->where('clinic_id', $clinicId)
            ->whereDate('scheduled_at', $date)
            ->whereNull('token_number')
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->orderBy('scheduled_at')
            ->pluck('id');

        if ($ids->isEmpty()) {
            return;
        }

        $max = (int) Appointment::query()
            ->where('clinic_id', $clinicId)
            ->whereDate('scheduled_at', $date)
            ->whereNotNull('token_number')
            ->max('token_number');

        $n = $max;
        foreach ($ids as $id) {
            $n++;
            Appointment::whereKey($id)->update(['token_number' => $n]);
            Log::info('OpdController assignMissingTokensForDate', ['appointment_id' => $id, 'token_number' => $n]);
        }
    }
}
