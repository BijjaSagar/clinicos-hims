<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\EmergencyVisit;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class EmergencyController extends Controller
{
    public function index(Request $request): View
    {
        $clinicId = auth()->user()->clinic_id;
        Log::info('EmergencyController@index', ['clinic_id' => $clinicId]);

        $visits = EmergencyVisit::query()
            ->where('clinic_id', $clinicId)
            ->with(['patient', 'registeredBy'])
            ->orderByDesc('registered_at')
            ->limit(100)
            ->get();

        $patients = Patient::where('clinic_id', $clinicId)->orderBy('name')->limit(500)->get(['id', 'name', 'phone']);
        Log::info('EmergencyController@index loaded', ['visits' => $visits->count(), 'patient_choices' => $patients->count()]);

        return view('emergency.index', compact('visits', 'patients'));
    }

    public function store(Request $request): RedirectResponse
    {
        $clinicId = auth()->user()->clinic_id;

        $validated = $request->validate([
            'patient_id'      => 'nullable|integer|exists:patients,id',
            'patient_name'    => 'required_without:patient_id|string|max:200',
            'phone'           => 'nullable|string|max:30',
            'chief_complaint' => 'nullable|string|max:500',
            'triage_level'    => 'nullable|integer|min:1|max:5',
            'bay_number'      => 'nullable|string|max:40',
        ]);

        $row = [
            'clinic_id'       => $clinicId,
            'patient_id'      => $validated['patient_id'] ?? null,
            'patient_name'    => $validated['patient_name'] ?? null,
            'phone'           => $validated['phone'] ?? null,
            'chief_complaint' => $validated['chief_complaint'] ?? null,
            'triage_level'    => $validated['triage_level'] ?? null,
            'bay_number'      => $validated['bay_number'] ?? null,
            'status'          => ! empty($validated['triage_level']) ? 'triaged' : 'registered',
            'registered_by'   => auth()->id(),
            'registered_at'   => now(),
        ];

        if ($row['patient_id']) {
            $p = Patient::find($row['patient_id']);
            $row['patient_name'] = $p->name;
            $row['phone'] = $row['phone'] ?? $p->phone;
        }

        $visit = EmergencyVisit::create($row);
        Log::info('EmergencyController@store created', [
            'id' => $visit->id,
            'clinic_id' => $visit->clinic_id,
            'patient_id' => $visit->patient_id,
            'status' => $visit->status,
            'triage_level' => $visit->triage_level,
        ]);

        return redirect()->route('emergency.index')->with('success', 'ER visit registered.');
    }

    public function updateTriage(Request $request, EmergencyVisit $visit): RedirectResponse
    {
        abort_unless(auth()->user()->clinic_id === $visit->clinic_id, 403);

        $validated = $request->validate([
            'triage_level' => 'required|integer|min:1|max:5',
            'bay_number'   => 'nullable|string|max:40',
            'status'       => 'nullable|in:registered,triaged,in_treatment,discharged,admitted,left_ama',
        ]);

        $visit->update(array_merge($validated, [
            'status' => $validated['status'] ?? 'triaged',
        ]));
        Log::info('EmergencyController@updateTriage done', [
            'id' => $visit->id,
            'triage_level' => $visit->triage_level,
            'bay_number' => $visit->bay_number,
            'status' => $visit->status,
        ]);

        return back()->with('success', 'Triage updated.');
    }
}
