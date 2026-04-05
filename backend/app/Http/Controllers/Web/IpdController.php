<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Bed;
use App\Models\IpdAdmission;
use App\Models\IpdProgressNote;
use App\Models\IpdCarePlan;
use App\Models\IpdHandoverNote;
use App\Models\IpdMedicationAdministration;
use App\Models\IpdMedicationOrder;
use App\Models\IpdVital;
use App\Models\Patient;
use App\Models\User;
use App\Models\Ward;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Illuminate\Support\Collection;
use App\Support\IpdSchema;

class IpdController extends Controller
{
    /**
     * Ensure admit UI JSON has plain nested arrays (not objects) for rooms/beds — JS relies on Array.isArray.
     */
    private function normalizeAdmitWardsPayload(Collection $payload): Collection
    {
        return $payload->map(function ($w) {
            $rooms = collect($w['rooms'] ?? [])->map(function ($r) {
                return [
                    'id' => (int) ($r['id'] ?? 0),
                    'name' => (string) ($r['name'] ?? ''),
                    'beds' => collect($r['beds'] ?? [])->map(function ($b) {
                        return [
                            'id' => (int) ($b['id'] ?? 0),
                            'code' => $b['code'] ?? null,
                        ];
                    })->values()->all(),
                ];
            })->values()->all();

            return [
                'id' => (int) ($w['id'] ?? 0),
                'name' => (string) ($w['name'] ?? ''),
                'rooms' => $rooms,
            ];
        })->values();
    }

    // ─── Index ───────────────────────────────────────────────────────────────

    public function index(Request $request): View
    {
        $clinicId = auth()->user()->clinic_id;
        Log::info('IpdController@index', ['clinic_id' => $clinicId, 'user' => auth()->id()]);

        $admissions = collect();
        $stats = ['totalAdmitted' => 0, 'availableBeds' => 0, 'icuBedsAvailable' => 0, 'dischargesToday' => 0];
        $wards = collect();
        $ipdReady = true;

        try {
            if (!Schema::hasTable('ipd_admissions')) {
                Log::warning('IpdController@index: ipd_admissions table missing');
                $ipdReady = false;
                return view('ipd.index', compact('admissions', 'stats', 'wards', 'ipdReady'));
            }

            $query = IpdAdmission::with(['patient', 'bed.room.ward', 'primaryDoctor'])
                ->where('clinic_id', $clinicId)
                ->active();

            if ($search = $request->input('search')) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('patient', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    })->orWhere('admission_number', 'like', "%{$search}%");
                });
            }

            if ($wardId = $request->input('ward_id')) {
                $query->where('ward_id', $wardId);
            }

            $orderCol = Schema::hasColumn('ipd_admissions', 'admission_date')
                ? 'admission_date'
                : (Schema::hasColumn('ipd_admissions', 'admitted_at') ? 'admitted_at' : 'created_at');
            Log::info('IpdController@index order column', ['column' => $orderCol]);
            $admissions = $query->orderByDesc($orderCol)->paginate(20)->withQueryString();

            $totalAdmitted = IpdAdmission::where('clinic_id', $clinicId)->active()->count();

            $availableBeds = 0;
            $icuBedsAvailable = 0;
            if (Schema::hasTable('hospital_beds')) {
                $availableBeds = Bed::where('clinic_id', $clinicId)->available()->count();
                $icuBedsAvailable = Bed::where('clinic_id', $clinicId)
                    ->available()
                    ->whereHas('room.ward', fn ($q) => $q->where('is_icu', true))
                    ->count();
            }

            $dischargesToday = IpdAdmission::where('clinic_id', $clinicId)
                ->dischargedToday()
                ->count();

            $stats = compact('totalAdmitted', 'availableBeds', 'icuBedsAvailable', 'dischargesToday');

            if (Schema::hasTable('hospital_wards')) {
                $wards = Ward::where('clinic_id', $clinicId);
                if (Schema::hasColumn('hospital_wards', 'is_active')) {
                    $wards->where(function ($q) {
                        $q->where('is_active', true)->orWhereNull('is_active');
                    });
                }
                $wards = $wards->get();
            }

            Log::info('IpdController@index loaded', [
                'admissions_count' => $admissions->count(),
                'stats' => $stats,
            ]);
        } catch (\Throwable $e) {
            Log::error('IpdController@index error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        }

        return view('ipd.index', compact('admissions', 'stats', 'wards', 'ipdReady'));
    }

    // ─── Bed Map ─────────────────────────────────────────────────────────────

    public function bedMap(): View
    {
        $clinicId = auth()->user()->clinic_id;
        Log::info('IpdController@bedMap', ['clinic_id' => $clinicId]);

        $wards = collect();

        try {
            if (Schema::hasTable('hospital_wards')) {
                $wardQuery = Ward::with([
                    'rooms.beds.currentAdmission.patient',
                ])
                    ->where('clinic_id', $clinicId);
                if (Schema::hasColumn('hospital_wards', 'is_active')) {
                    $wardQuery->where(function ($q) {
                        $q->where('is_active', true)->orWhereNull('is_active');
                    });
                }
                $wards = $wardQuery->orderBy('name')->get();
                Log::info('IpdController@bedMap wards loaded', ['count' => $wards->count()]);
            }
        } catch (\Throwable $e) {
            Log::error('IpdController@bedMap error', ['error' => $e->getMessage()]);
        }

        return view('ipd.bed-map', compact('wards'));
    }

    // ─── Create ──────────────────────────────────────────────────────────────

    public function create(): View
    {
        $clinicId = auth()->user()->clinic_id;
        Log::info('IpdController@create', ['clinic_id' => $clinicId]);

        $patients = collect();
        $doctors = collect();
        $admitWardsPayload = collect();

        try {
            $patients = Patient::where('clinic_id', $clinicId)
                ->orderBy('name')
                ->get(['id', 'name', 'phone', 'age_years', 'sex']);

            $doctors = User::where('clinic_id', $clinicId)
                ->whereIn('role', ['doctor', 'admin', 'owner'])
                ->orderBy('name')
                ->get(['id', 'name', 'role']);
            Log::info('IpdController@create doctors loaded', ['count' => $doctors->count()]);

            if (Schema::hasTable('hospital_wards') && Schema::hasTable('hospital_rooms') && Schema::hasTable('hospital_beds')) {
                $wardQuery = Ward::where('clinic_id', $clinicId);
                // NULL is_active = legacy "active" — strict `where(true)` hid all wards and broke admit UI while stats still counted beds.
                if (Schema::hasColumn('hospital_wards', 'is_active')) {
                    $wardQuery->where(function ($q) {
                        $q->where('is_active', true)->orWhereNull('is_active');
                    });
                }
                Log::info('IpdController@create ward query', ['has_is_active_col' => Schema::hasColumn('hospital_wards', 'is_active')]);

                $wardOrderCol = Schema::hasColumn('hospital_wards', 'name')
                    ? 'name'
                    : (Schema::hasColumn('hospital_wards', 'code') ? 'code' : 'id');
                Log::info('IpdController@create hospital_wards order column', ['column' => $wardOrderCol]);

                $roomOrderCol = Schema::hasColumn('hospital_rooms', 'name')
                    ? 'name'
                    : (Schema::hasColumn('hospital_rooms', 'room_number') ? 'room_number' : 'id');
                Log::info('IpdController@create hospital_rooms order column', ['column' => $roomOrderCol]);

                $wardsLoaded = $wardQuery
                    ->with([
                        'rooms' => function ($q) use ($roomOrderCol) {
                            $q->orderBy($roomOrderCol);
                            if (Schema::hasColumn('hospital_rooms', 'is_active')) {
                                $q->where(function ($q2) {
                                    $q2->where('is_active', true)->orWhereNull('is_active');
                                });
                            }
                        },
                        'rooms.beds' => function ($q) {
                            $bedOrder = Schema::hasColumn('hospital_beds', 'bed_code')
                                ? 'bed_code'
                                : (Schema::hasColumn('hospital_beds', 'bed_number') ? 'bed_number' : 'id');
                            $q->available()->orderBy($bedOrder);
                        },
                    ])
                    ->orderBy($wardOrderCol)
                    ->get();

                $admitWardsPayload = $wardsLoaded->map(function ($w) {
                    $wardLabel = $w->name ?? $w->code ?? ('Ward #' . $w->id);
                    $rooms = $w->rooms->map(function ($r) {
                        $roomLabel = $r->name ?? $r->room_number ?? ('Room #' . $r->id);

                        return [
                            'id'   => $r->id,
                            'name' => $roomLabel,
                            'beds' => $r->beds->map(function ($b) {
                                return [
                                    'id'   => $b->id,
                                    'code' => $b->bed_code ?? $b->bed_number,
                                ];
                            })->values(),
                        ];
                    })->filter(fn ($r) => $r['beds']->count() > 0)->values();

                    return [
                        'id'    => $w->id,
                        'name'  => $wardLabel,
                        'rooms' => $rooms,
                    ];
                })->filter(fn ($w) => $w['rooms']->count() > 0)->values();

                Log::info('IpdController@create admitWardsPayload built', [
                    'wards'       => $admitWardsPayload->count(),
                    'total_rooms' => $admitWardsPayload->sum(fn ($w) => count($w['rooms'])),
                    'total_beds'  => $admitWardsPayload->sum(fn ($w) => collect($w['rooms'])->sum(fn ($r) => count($r['beds']))),
                ]);
            }

            if ($admitWardsPayload->isEmpty() && Schema::hasTable('hospital_beds')) {
                $bedOrderFlat = Schema::hasColumn('hospital_beds', 'bed_code')
                    ? 'bed_code'
                    : (Schema::hasColumn('hospital_beds', 'bed_number') ? 'bed_number' : 'id');
                $flatBeds = Bed::with('room.ward')
                    ->where('clinic_id', $clinicId)
                    ->available()
                    ->orderBy($bedOrderFlat)
                    ->get();
                if ($flatBeds->isNotEmpty()) {
                    $byWard = $flatBeds->groupBy(fn ($b) => $b->room?->ward_id ?? 0);
                    $admitWardsPayload = $byWard->map(function ($beds, $wid) {
                        $ward = $beds->first()->room?->ward;
                        $byRoom = $beds->groupBy('room_id');
                        $rooms = $byRoom->map(function ($roomBeds, $rid) {
                            $room = $roomBeds->first()->room;

                            return [
                                'id'   => (int) $rid,
                                'name' => $room?->name ?? $room?->room_number ?? 'Room',
                                'beds' => $roomBeds->map(fn ($b) => [
                                    'id'   => $b->id,
                                    'code' => $b->bed_code ?? $b->bed_number,
                                ])->values(),
                            ];
                        })->values();

                        return [
                            'id'    => (int) ($ward?->id ?? $wid),
                            'name'  => $ward?->name ?? 'Available beds',
                            'rooms' => $rooms,
                        ];
                    })->values();
                    Log::info('IpdController@create admitWardsPayload fallback from flat beds', [
                        'wards' => $admitWardsPayload->count(),
                    ]);
                }
            }

            Log::info('IpdController@create loaded', [
                'patients' => $patients->count(),
                'doctors'  => $doctors->count(),
                'wards_in_payload' => $admitWardsPayload->count(),
            ]);
            if ($admitWardsPayload->isEmpty()) {
                Log::warning('IpdController@create: admitWardsPayload empty — check hospital_wards/rooms/beds for clinic', [
                    'clinic_id' => $clinicId,
                    'beds_available_raw' => Schema::hasTable('hospital_beds')
                        ? Bed::where('clinic_id', $clinicId)->available()->count()
                        : null,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('IpdController@create error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        }

        $admitWardsPayload = $this->normalizeAdmitWardsPayload(collect($admitWardsPayload));
        $first = $admitWardsPayload->first();
        Log::info('IpdController@create payload normalized', [
            'ward_count' => $admitWardsPayload->count(),
            'first_ward_id' => $first['id'] ?? null,
            'first_ward_room_count' => isset($first['rooms']) ? count($first['rooms']) : 0,
        ]);

        return view('ipd.create', compact('patients', 'doctors', 'admitWardsPayload'));
    }

    // ─── Store ───────────────────────────────────────────────────────────────

    public function store(Request $request): RedirectResponse
    {
        Log::info('IpdController@store', ['user' => auth()->id()]);

        if (!Schema::hasTable('ipd_admissions') || !Schema::hasTable('hospital_beds')) {
            Log::error('IpdController@store: required tables missing');
            return redirect()->route('ipd.index')
                ->with('error', 'IPD module tables are not yet set up. Please run migrations.');
        }

        $validated = $request->validate([
            'patient_id'             => ['required', 'integer', 'exists:patients,id'],
            'bed_id'                 => ['required', 'integer', 'exists:hospital_beds,id'],
            'ward_id'                => ['nullable', 'integer'],
            'primary_doctor_id'      => ['required', 'integer', 'exists:users,id'],
            'admission_type'         => ['required', 'string', 'max:50'],
            'diagnosis_at_admission' => ['required', 'string', 'max:1000'],
            'diet_type'              => ['nullable', 'string', 'max:100'],
            'estimated_days'         => ['nullable', 'integer', 'min:1', 'max:365'],
        ]);

        $clinicId = auth()->user()->clinic_id;

        try {
            $bed = Bed::with('room')
                ->where('id', $validated['bed_id'])
                ->where('clinic_id', $clinicId)
                ->available()
                ->firstOrFail();

            $bedWardId = $bed->room?->ward_id;
            if (! empty($validated['ward_id']) && $bedWardId !== null
                && (int) $validated['ward_id'] !== (int) $bedWardId) {
                Log::warning('IpdController@store ward/bed mismatch', [
                    'ward_id' => $validated['ward_id'],
                    'bed_ward_id' => $bedWardId,
                    'bed_id' => $bed->id,
                ]);

                return redirect()->route('ipd.create')
                    ->withInput()
                    ->withErrors(['bed_id' => 'Selected bed does not belong to the selected ward.']);
            }

            $todayPrefix = 'IPD' . date('Ymd');
            $lastToday   = IpdAdmission::where('admission_number', 'like', "{$todayPrefix}%")
                ->max('admission_number');

            if ($lastToday) {
                $lastSeq = (int) substr($lastToday, -4);
                $nextSeq = $lastSeq + 1;
            } else {
                $nextSeq = 1;
            }

            $admissionNumber = $todayPrefix . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);

            $wardId = $bed->ward_id ?? ($validated['ward_id'] ?? null);
            $admissionType = match ($validated['admission_type']) {
                'planned' => 'elective',
                default   => $validated['admission_type'],
            };
            $dietType = $validated['diet_type'] ?? null;
            if ($dietType === 'low_sodium') {
                $dietType = 'low_salt';
            }

            Log::info('IpdController@store resolved admission fields', [
                'ward_id' => $wardId,
                'admission_type_raw' => $validated['admission_type'],
                'admission_type_db' => $admissionType,
                'diet_type' => $dietType,
            ]);

            $payload = [
                'clinic_id'              => $clinicId,
                'patient_id'             => $validated['patient_id'],
                'bed_id'                 => $bed->id,
                'ward_id'                => $wardId,
                'primary_doctor_id'      => $validated['primary_doctor_id'],
                'admitted_by'            => auth()->id(),
                'admission_number'       => $admissionNumber,
                'admission_type'         => $admissionType,
                'diagnosis_at_admission' => $validated['diagnosis_at_admission'],
                'diet_type'              => $dietType,
                'estimated_days'         => $validated['estimated_days'] ?? null,
                'admission_date'         => now(),
                'status'                 => 'admitted',
            ];
            if (Schema::hasColumn('ipd_admissions', 'admitted_at') && ! Schema::hasColumn('ipd_admissions', 'admission_date')) {
                unset($payload['admission_date']);
                $payload['admitted_at'] = now();
            }
            if (Schema::hasColumn('ipd_admissions', 'attending_doctor_id') && ! Schema::hasColumn('ipd_admissions', 'primary_doctor_id')) {
                $payload['attending_doctor_id'] = $validated['primary_doctor_id'];
                unset($payload['primary_doctor_id']);
            }
            $allowed = array_flip(Schema::getColumnListing('ipd_admissions'));
            $payload = array_intersect_key($payload, $allowed);
            Log::info('IpdController@store filtered payload keys', ['keys' => array_keys($payload)]);

            $admission = IpdAdmission::create($payload);

            $bed->update(['status' => 'occupied']);

            Log::info('IPD admission created via web', [
                'admission_id'     => $admission->id,
                'admission_number' => $admission->admission_number,
                'patient_id'       => $admission->patient_id,
                'bed_id'           => $bed->id,
                'clinic_id'        => $clinicId,
                'admitted_by'      => auth()->id(),
            ]);

            return redirect()
                ->route('ipd.show', $admission)
                ->with('success', "Admission {$admissionNumber} created successfully.");
        } catch (\Throwable $e) {
            Log::error('IpdController@store error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->route('ipd.create')
                ->with('error', 'Failed to create admission: ' . $e->getMessage())
                ->withInput();
        }
    }

    // ─── Show ────────────────────────────────────────────────────────────────

    public function show(IpdAdmission $admission): View
    {
        $this->authorizeClinic($admission->clinic_id);
        Log::info('IpdController@show', ['admission_id' => $admission->id]);

        $vitals = collect();
        $progressNotes = collect();
        $medicationOrders = collect();
        $handoverNotes = collect();
        $carePlans = collect();

        try {
            $admission->load([
                'patient',
                'bed.room.ward',
                'ward',
                'primaryDoctor',
                'admittedBy',
            ]);
        } catch (\Throwable $e) {
            Log::error('IpdController@show load admission error', ['error' => $e->getMessage(), 'admission_id' => $admission->id]);
        }

        $loadRelation = function (string $label, callable $fn) use ($admission) {
            try {
                return $fn();
            } catch (\Throwable $e) {
                Log::error('IpdController@show section failed', [
                    'section' => $label,
                    'admission_id' => $admission->id,
                    'error' => $e->getMessage(),
                ]);

                return collect();
            }
        };

        if (Schema::hasTable('ipd_vitals')) {
            $vitals = $loadRelation('vitals', function () use ($admission) {
                return $admission->vitals()
                    ->with('recordedBy')
                    ->latest('recorded_at')
                    ->limit(20)
                    ->get();
            });
        }

        if (Schema::hasTable('ipd_progress_notes')) {
            $progressNotes = $loadRelation('progress_notes', function () use ($admission) {
                $q = $admission->progressNotes()->with('author');
                if (Schema::hasColumn('ipd_progress_notes', 'note_at')) {
                    return $q->orderByDesc('note_at')->get();
                }

                return $q->orderByDesc('note_date')->get();
            });
        }

        if (Schema::hasTable('ipd_medication_orders')) {
            $medicationOrders = $loadRelation('medication_orders', function () use ($admission) {
                return $admission->medicationOrders()
                    ->with(['prescribedBy', 'administrations.administeredBy'])
                    ->orderByDesc('created_at')
                    ->get();
            });
        }

        if (Schema::hasTable('ipd_handover_notes')) {
            $handoverNotes = $loadRelation('handover_notes', function () use ($admission) {
                return $admission->handoverNotes()
                    ->with('handedOverBy')
                    ->orderByDesc('created_at')
                    ->get();
            });
        }

        if (Schema::hasTable('ipd_care_plans')) {
            $carePlans = $loadRelation('care_plans', function () use ($admission) {
                return $admission->carePlans()
                    ->orderByDesc('updated_at')
                    ->get();
            });
        }

        Log::info('IpdController@show loaded', [
            'admission_id' => $admission->id,
            'vitals' => $vitals->count(),
            'notes' => $progressNotes->count(),
            'meds' => $medicationOrders->count(),
            'handovers' => $handoverNotes->count(),
            'care_plans' => $carePlans->count(),
        ]);

        return view('ipd.show', compact('admission', 'vitals', 'progressNotes', 'medicationOrders', 'handoverNotes', 'carePlans'));
    }

    /**
     * GET /ipd/{id}/progress-notes — open IPD screen on Progress Notes tab (bookmark-friendly).
     */
    public function redirectProgressNotes(IpdAdmission $admission): RedirectResponse
    {
        $this->authorizeClinic($admission->clinic_id);
        Log::info('IpdController@redirectProgressNotes', ['admission_id' => $admission->id]);

        return redirect()->route('ipd.show', $admission)->with('open_ipd_tab', 'notes');
    }

    /**
     * GET /ipd/{id}/vitals — open IPD screen on Vitals tab.
     */
    public function redirectVitalsTab(IpdAdmission $admission): RedirectResponse
    {
        $this->authorizeClinic($admission->clinic_id);
        Log::info('IpdController@redirectVitalsTab', ['admission_id' => $admission->id]);

        return redirect()->route('ipd.show', $admission)->with('open_ipd_tab', 'vitals');
    }

    // ─── Discharge ───────────────────────────────────────────────────────────

    public function discharge(Request $request, IpdAdmission $admission): RedirectResponse
    {
        $this->authorizeClinic($admission->clinic_id);
        Log::info('IpdController@discharge', ['admission_id' => $admission->id]);

        $validated = $request->validate([
            'discharge_type'   => ['required', 'string', 'max:100'],
            'final_diagnosis'  => ['required', 'string', 'max:2000'],
            'discharge_notes'  => ['nullable', 'string'],
        ]);

        try {
            $dischargePayload = [
                'discharge_date'   => now(),
                'discharge_type'   => $validated['discharge_type'],
                'final_diagnosis'  => $validated['final_diagnosis'],
                'discharge_notes'  => $validated['discharge_notes'] ?? null,
                'status'           => 'discharged',
            ];
            $allowed = array_flip(Schema::getColumnListing('ipd_admissions'));
            $dischargePayload = array_intersect_key($dischargePayload, $allowed);
            Log::info('IpdController@discharge payload keys', ['keys' => array_keys($dischargePayload)]);
            $admission->update($dischargePayload);

            if ($admission->bed) {
                $admission->bed->update(['status' => 'cleaning']);
            }

            AuditLog::log(
                'discharged',
                "Patient {$admission->patient->name} discharged ({$validated['discharge_type']})",
                IpdAdmission::class,
                $admission->id,
                ['status' => 'admitted'],
                ['status' => 'discharged', 'discharge_type' => $validated['discharge_type']]
            );

            Log::info('Patient discharged', [
                'admission_id'     => $admission->id,
                'admission_number' => $admission->admission_number,
                'discharge_type'   => $validated['discharge_type'],
                'discharged_by'    => auth()->id(),
            ]);
        } catch (\Throwable $e) {
            Log::error('IpdController@discharge error', ['error' => $e->getMessage(), 'admission_id' => $admission->id]);
            return redirect()->route('ipd.show', $admission)
                ->with('error', 'Discharge failed: ' . $e->getMessage());
        }

        return redirect()
            ->route('ipd.index')
            ->with('success', "Patient {$admission->patient->name} discharged successfully.");
    }

    // ─── Record Vitals ───────────────────────────────────────────────────────

    public function recordVitals(Request $request, IpdAdmission $admission): JsonResponse
    {
        $this->authorizeClinic($admission->clinic_id);

        try {
            $validated = $request->validate([
                'temperature' => ['nullable', 'numeric', 'min:30', 'max:45'],
                'pulse' => ['nullable', 'integer', 'min:20', 'max:300'],
                'bp_systolic' => ['nullable', 'integer', 'min:50', 'max:250'],
                'bp_diastolic' => ['nullable', 'integer', 'min:30', 'max:150'],
                'respiratory_rate' => ['nullable', 'integer', 'min:4', 'max:60'],
                'spo2' => ['nullable', 'numeric', 'min:50', 'max:100'],
                'pain_score' => ['nullable', 'integer', 'min:0', 'max:10'],
                'gcs' => ['nullable', 'integer', 'min:3', 'max:15'],
                'weight' => ['nullable', 'numeric', 'min:1', 'max:500'],
                'height' => ['nullable', 'numeric', 'min:30', 'max:250'],
                'notes' => ['nullable', 'string', 'max:500'],
            ]);

            $row = IpdSchema::mapVitalsInsert(
                (int) $admission->clinic_id,
                (int) $admission->id,
                (int) auth()->id(),
                $validated
            );

            $vital = IpdVital::create($row);

            Log::info('IPD vitals recorded', [
                'admission_id' => $admission->id,
                'vital_id' => $vital->id,
                'recorded_by' => auth()->id(),
                'columns' => array_keys($row),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Vitals recorded successfully.',
                'vital' => $vital,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'errors' => $e->errors()], 422);
        } catch (\Throwable $e) {
            Log::error('IpdController@recordVitals failed', ['error' => $e->getMessage(), 'admission_id' => $admission->id]);

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ─── Add Progress Note ───────────────────────────────────────────────────

    public function addProgressNote(Request $request, IpdAdmission $admission): JsonResponse
    {
        $this->authorizeClinic($admission->clinic_id);

        try {
            $validated = $request->validate([
                'note_type' => ['required', 'string', 'max:50'],
                'note_date' => ['required', 'date'],
                'note_time' => ['required', 'string'],
                'subjective' => ['required', 'string'],
                'objective' => ['required', 'string'],
                'assessment' => ['required', 'string'],
                'plan' => ['required', 'string'],
                'notes' => ['nullable', 'string'],
            ]);

            $row = IpdSchema::mapProgressNoteInsert(
                (int) $admission->clinic_id,
                (int) $admission->id,
                (int) auth()->id(),
                $validated
            );

            $note = IpdProgressNote::create($row);

            Log::info('IPD progress note added', [
                'admission_id' => $admission->id,
                'note_id' => $note->id,
                'author_id' => auth()->id(),
                'keys' => array_keys($row),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Progress note added successfully.',
                'note' => $note->load('author'),
            ]);
        } catch (\Throwable $e) {
            Log::error('IpdController@addProgressNote failed', ['error' => $e->getMessage(), 'admission_id' => $admission->id]);

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ─── Print Prescription ─────────────────────────────────────────────────

    public function printPrescription(IpdAdmission $admission): View
    {
        $this->authorizeClinic($admission->clinic_id);
        Log::info('IpdController@printPrescription', ['admission_id' => $admission->id]);

        $admission->load(['patient', 'primaryDoctor', 'ward', 'bed']);

        $medicationOrders = collect();
        try {
            if (Schema::hasTable('ipd_medication_orders')) {
                $medFk = IpdSchema::admissionFkColumn('ipd_medication_orders');
                $medicationOrders = IpdMedicationOrder::where($medFk, $admission->id)
                    ->where('status', '!=', 'cancelled')
                    ->orderBy('created_at')
                    ->get();
            }
        } catch (\Throwable $e) {
            Log::error('IpdController@printPrescription error', ['error' => $e->getMessage()]);
        }

        return view('ipd.print-prescription', compact('admission', 'medicationOrders'));
    }

    // ─── Visiting Card / Admission Slip ──────────────────────────────────────

    public function printCard(IpdAdmission $admission): View
    {
        $admission->load(['patient', 'bed.room.ward', 'primaryDoctor', 'ward']);
        return view('ipd.visiting-card', compact('admission'));
    }

    // ─── Medication orders & MAR (Phase D) ───────────────────────────────────

    public function storeMedicationOrder(Request $request, IpdAdmission $admission): RedirectResponse
    {
        $this->authorizeClinic($admission->clinic_id);
        Log::info('IpdController@storeMedicationOrder', ['admission_id' => $admission->id, 'user' => auth()->id()]);

        if (! Schema::hasTable('ipd_medication_orders')) {
            return back()->with('error', 'Medication orders table is not available.');
        }

        $validated = $request->validate([
            'drug_name'     => ['required', 'string', 'max:255'],
            'route'         => ['required', Rule::in([
                'oral', 'iv', 'im', 'sc', 'topical', 'sublingual', 'inhalation', 'rectal',
            ])],
            'dosage'        => ['required', 'string', 'max:255'],
            'frequency'     => ['required', 'string', 'max:100'],
            'start_date'    => ['required', 'date'],
            'end_date'      => ['nullable', 'date', 'after_or_equal:start_date'],
            'instructions'  => ['nullable', 'string', 'max:2000'],
            'is_sos'        => ['nullable', 'boolean'],
        ]);

        $payload = IpdSchema::mapMedicationOrderInsert(
            (int) $admission->clinic_id,
            (int) $admission->id,
            (int) auth()->id(),
            $validated,
            $request->boolean('is_sos')
        );
        Log::info('IpdController@storeMedicationOrder payload', ['keys' => array_keys($payload)]);

        try {
            IpdMedicationOrder::create($payload);
        } catch (\Throwable $e) {
            Log::error('IpdController@storeMedicationOrder failed', ['error' => $e->getMessage()]);

            return back()->withInput()->with('error', 'Could not save order: '.$e->getMessage());
        }

        return back()->with('success', 'Medication order added.');
    }

    public function recordMar(Request $request, IpdAdmission $admission): RedirectResponse
    {
        $this->authorizeClinic($admission->clinic_id);
        Log::info('IpdController@recordMar', ['admission_id' => $admission->id]);

        if (! Schema::hasTable('ipd_medication_administrations')) {
            return back()->with('error', 'MAR table is not available.');
        }

        $validated = $request->validate([
            'order_id'                  => ['required', 'integer', 'exists:ipd_medication_orders,id'],
            'dose_given'                => ['required', 'string', 'max:255'],
            'administered_at'           => ['required', 'date'],
            'route_used'                => ['nullable', 'string', 'max:40'],
            'notes'                     => ['nullable', 'string', 'max:1000'],
            'not_administered'          => ['nullable', 'boolean'],
            'not_administered_reason'   => ['nullable', 'string', 'max:500'],
        ]);

        $medFk = IpdSchema::admissionFkColumn('ipd_medication_orders');
        $order = IpdMedicationOrder::query()
            ->where('id', $validated['order_id'])
            ->where($medFk, $admission->id)
            ->where('clinic_id', $admission->clinic_id)
            ->firstOrFail();

        $row = IpdSchema::mapMarInsert(
            $order,
            $admission,
            $validated,
            $request->boolean('not_administered')
        );

        try {
            IpdMedicationAdministration::create($row);
            Log::info('IpdController@recordMar saved', ['order_id' => $order->id]);
        } catch (\Throwable $e) {
            Log::error('IpdController@recordMar failed', ['error' => $e->getMessage()]);

            return back()->with('error', 'MAR save failed: '.$e->getMessage());
        }

        return back()->with('success', 'MAR entry recorded.');
    }

    public function storeHandover(Request $request, IpdAdmission $admission): RedirectResponse
    {
        $this->authorizeClinic($admission->clinic_id);
        Log::info('IpdController@storeHandover', ['admission_id' => $admission->id]);

        if (! Schema::hasTable('ipd_handover_notes')) {
            return back()->with('error', 'Handover notes are not available (run migrations).');
        }

        $validated = $request->validate([
            'shift'        => ['nullable', 'string', 'max:20'],
            'summary'      => ['required', 'string', 'max:10000'],
            'concerns'     => ['nullable', 'string', 'max:5000'],
            'received_by'  => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $payload = [
            'clinic_id'        => $admission->clinic_id,
            'admission_id'     => $admission->id,
            'shift'            => $validated['shift'] ?? null,
            'summary'          => $validated['summary'],
            'concerns'         => $validated['concerns'] ?? null,
            'handed_over_by'   => auth()->id(),
            'received_by'      => $validated['received_by'] ?? null,
        ];
        $allowed = array_flip(Schema::getColumnListing('ipd_handover_notes'));
        $payload = array_intersect_key($payload, $allowed);

        try {
            IpdHandoverNote::create($payload);
        } catch (\Throwable $e) {
            Log::error('IpdController@storeHandover failed', ['error' => $e->getMessage()]);

            return back()->withInput()->with('error', 'Could not save handover: '.$e->getMessage());
        }

        return back()->with('success', 'Handover note saved.');
    }

    public function storeCarePlan(Request $request, IpdAdmission $admission): RedirectResponse
    {
        $this->authorizeClinic($admission->clinic_id);
        Log::info('IpdController@storeCarePlan', ['admission_id' => $admission->id]);

        if (! Schema::hasTable('ipd_care_plans')) {
            return back()->with('error', 'Care plans are not available (run migrations).');
        }

        $validated = $request->validate([
            'goal'             => ['required', 'string', 'max:500'],
            'interventions'    => ['nullable', 'string', 'max:10000'],
            'outcome_review'   => ['nullable', 'string', 'max:10000'],
        ]);

        $payload = [
            'clinic_id'       => $admission->clinic_id,
            'admission_id'    => $admission->id,
            'goal'            => $validated['goal'],
            'interventions'   => $validated['interventions'] ?? null,
            'outcome_review'  => $validated['outcome_review'] ?? null,
            'updated_by'      => auth()->id(),
        ];
        $allowed = array_flip(Schema::getColumnListing('ipd_care_plans'));
        $payload = array_intersect_key($payload, $allowed);

        try {
            IpdCarePlan::create($payload);
        } catch (\Throwable $e) {
            Log::error('IpdController@storeCarePlan failed', ['error' => $e->getMessage()]);

            return back()->withInput()->with('error', 'Could not save care plan: '.$e->getMessage());
        }

        return back()->with('success', 'Care plan entry saved.');
    }

    /**
     * Phase A housekeeping: discharge sets bed → cleaning; staff marks available when ready.
     */
    public function markBedAvailable(Request $request, Bed $bed): RedirectResponse
    {
        $this->authorizeClinic($bed->clinic_id);
        Log::info('IpdController@markBedAvailable', [
            'bed_id' => $bed->id,
            'bed_code' => $bed->bed_code,
            'status' => $bed->status,
            'user' => auth()->id(),
        ]);

        if (! in_array($bed->status, ['cleaning', 'maintenance'], true)) {
            return back()->with('error', 'Only beds in cleaning or maintenance can be marked available.');
        }

        if (Schema::hasTable('ipd_admissions')) {
            $active = IpdAdmission::where('bed_id', $bed->id)->where('status', 'admitted')->exists();
            if ($active) {
                Log::warning('IpdController@markBedAvailable blocked: active admission on bed', ['bed_id' => $bed->id]);

                return back()->with('error', 'This bed still has an active admission.');
            }
        }

        $oldStatus = $bed->status;
        $bed->update(['status' => 'available']);

        AuditLog::log(
            'bed_housekeeping',
            "Bed {$bed->bed_code} marked available after housekeeping",
            Bed::class,
            $bed->id,
            ['status' => $oldStatus],
            ['status' => 'available']
        );

        Log::info('IpdController@markBedAvailable completed', ['bed_id' => $bed->id]);

        return back()->with('success', "Bed {$bed->bed_code} is now available.");
    }

    // ─── Private Helpers ─────────────────────────────────────────────────────

    private function authorizeClinic(int $resourceClinicId): void
    {
        abort_unless(auth()->user()->clinic_id === $resourceClinicId, 403);
    }
}
