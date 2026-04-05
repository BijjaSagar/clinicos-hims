<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\IndianDrug;
use App\Models\LabOrder;
use App\Models\Patient;
use App\Models\PatientPhoto;
use App\Models\Prescription;
use App\Models\AuditLog;
use App\Models\PrescriptionDrug;
use App\Models\Visit;
use App\Models\VisitLesion;
use App\Models\VisitProcedure;
use App\Models\VisitScale;
use App\Services\AI\ClaudeService;
use App\Services\PrescriptionSafetyService;
use App\Services\WhatsAppService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class EmrWebController extends Controller
{
    private ?WhatsAppService $whatsAppService;

    public function __construct(?WhatsAppService $whatsAppService = null)
    {
        $this->whatsAppService = $whatsAppService;
    }

    public function index(): View
    {
        Log::info('EmrWebController@index');
        
        $clinicId = auth()->user()->clinic_id;
        
        // Get recent visits with patient info
        $recentVisits = Visit::with(['patient', 'doctor'])
            ->where('clinic_id', $clinicId)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // Get in-progress visits (today's consultations - draft status means in progress)
        $inProgressVisits = Visit::with(['patient', 'doctor', 'appointment'])
            ->where('clinic_id', $clinicId)
            ->whereDate('created_at', today())
            ->where('status', 'draft')
            ->orderByDesc('created_at')
            ->get();

        // Get today's appointments that need visits
        $todayAppointments = Appointment::with(['patient'])
            ->where('clinic_id', $clinicId)
            ->whereDate('scheduled_at', today())
            ->whereIn('status', ['checked_in', 'in_consultation', 'confirmed'])
            ->whereDoesntHave('visit', function ($q) {
                $q->whereDate('created_at', today());
            })
            ->orderBy('scheduled_at')
            ->get();

        // Stats
        $stats = [
            'today_visits' => Visit::where('clinic_id', $clinicId)->whereDate('created_at', today())->count(),
            'in_progress' => $inProgressVisits->count(),
            'completed_today' => Visit::where('clinic_id', $clinicId)->whereDate('created_at', today())->where('status', 'finalised')->count(),
            'pending_followups' => Visit::where('clinic_id', $clinicId)
                ->whereNotNull('followup_date')
                ->whereBetween('followup_date', [today(), today()->addDays(7)])
                ->count(),
        ];

        return view('emr.index', compact('recentVisits', 'inProgressVisits', 'todayAppointments', 'stats'));
    }

    public function show(Patient $patient, Visit $visit): View
    {
        Log::info('EmrWebController@show', ['patient_id' => $patient->id, 'visit_id' => $visit->id]);
        
        $clinicId = auth()->user()->clinic_id;
        abort_unless($patient->clinic_id === $clinicId && $visit->clinic_id === $clinicId, 403);
        abort_unless($visit->patient_id === $patient->id, 404);

        // Only eager-load relations whose backing tables exist (partial migrations on some hosts caused 500s).
        $visitRelations = ['doctor'];
        if (Schema::hasTable('visit_lesions')) {
            $visitRelations[] = 'lesions';
        }
        if (Schema::hasTable('visit_scales')) {
            $visitRelations[] = 'scales';
        }
        if (Schema::hasTable('visit_procedures')) {
            $visitRelations[] = 'procedures';
        }
        if (Schema::hasTable('prescriptions')) {
            $visitRelations[] = Schema::hasTable('prescription_drugs') ? 'prescriptions.drugs' : 'prescriptions';
        }
        if (Schema::hasTable('patient_photos')) {
            $visitRelations[] = 'photos';
        }
        if (Schema::hasTable('invoices') && Schema::hasTable('invoice_items')) {
            $visitRelations[] = 'invoice.items';
            Log::info('EmrWebController@show: loading invoice.items');
        }
        if (Schema::hasTable('physio_hep')) {
            $visitRelations[] = 'physioHep';
            Log::info('EmrWebController@show: loading physioHep relation');
        } else {
            Log::info('EmrWebController@show: physio_hep table absent, skipping relation');
        }

        try {
            $visit->load($visitRelations);
        } catch (\Throwable $e) {
            Log::error('EmrWebController@show: visit relation load failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            try {
                $visit->load(['doctor']);
            } catch (\Throwable $e2) {
                Log::error('EmrWebController@show: minimal visit load failed', ['error' => $e2->getMessage()]);
            }
        }

        $this->voidEmrVisitLazyLoadsForMissingTables($visit);

        if (Schema::hasTable('patient_photos')) {
            try {
                $patient->load(['photos']);
            } catch (\Throwable $e) {
                Log::warning('EmrWebController@show: patient photos load failed', ['error' => $e->getMessage()]);
            }
        }

        if (in_array(strtolower($visit->specialty ?? ''), ['dental', 'dentistry'], true) && Schema::hasTable('dental_teeth')) {
            $patient->dentalTeeth = DB::table('dental_teeth')
                ->where('patient_id', $patient->id)
                ->get();
        }

        $visitHistoryQuery = Visit::with(['doctor'])
            ->where('patient_id', $patient->id)
            ->where('clinic_id', $clinicId);
        if (Schema::hasTable('prescriptions')) {
            $visitHistoryQuery->with('prescriptions');
        }
        $visitHistory = $visitHistoryQuery->orderByDesc('created_at')->limit(10)->get();

        foreach ($visitHistory as $historyVisit) {
            $this->voidEmrVisitLazyLoadsForMissingTables($historyVisit);
        }

        $prescription = null;
        if (Schema::hasTable('prescriptions')) {
            try {
                $pq = $visit->prescriptions()->latest();
                $prescription = Schema::hasTable('prescription_drugs')
                    ? $pq->with('drugs')->first()
                    : $pq->first();
            } catch (\Throwable $e) {
                Log::warning('EmrWebController@show: prescription load failed', ['error' => $e->getMessage()]);
            }
        }

        $patientPhotos = collect();
        if (Schema::hasTable('patient_photos')) {
            try {
                $patientPhotos = PatientPhoto::where('patient_id', $patient->id)
                    ->where('clinic_id', $clinicId)
                    ->orderByDesc('created_at')
                    ->get()
                    ->groupBy('photo_type');
            } catch (\Throwable $e) {
                Log::warning('EmrWebController@show: patient photos query failed', ['error' => $e->getMessage()]);
            }
        }

        $labOrders = collect();
        if (Schema::hasTable('lab_orders')) {
            try {
                $labOrders = LabOrder::where('patient_id', $patient->id)
                    ->where('clinic_id', $clinicId)
                    ->orderByDesc('created_at')
                    ->limit(5)
                    ->get();
                if (Schema::hasTable('lab_order_tests')) {
                    try {
                        $labOrders->load('labOrderTests');
                        Log::info('EmrWebController@show: loaded labOrderTests', ['count' => $labOrders->count()]);
                    } catch (\Throwable $e) {
                        Log::warning('EmrWebController@show: labOrderTests relation failed', ['error' => $e->getMessage()]);
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('EmrWebController@show: lab orders query failed', ['error' => $e->getMessage()]);
            }
        }

        $previousPrescriptions = collect();
        if (Schema::hasTable('prescriptions')) {
            try {
                $pq = Prescription::where('patient_id', $patient->id)
                    ->where('clinic_id', $clinicId)
                    ->where('visit_id', '!=', $visit->id)
                    ->orderByDesc('created_at')
                    ->limit(3);
                $previousPrescriptions = Schema::hasTable('prescription_drugs')
                    ? $pq->with('drugs')->get()
                    : $pq->get();
            } catch (\Throwable $e) {
                Log::warning('EmrWebController@show: previous prescriptions query failed', ['error' => $e->getMessage()]);
            }
        }

        $previousVisit = null;
        if (Schema::hasTable('visit_scales')) {
            try {
                $previousVisit = Visit::where('patient_id', $patient->id)
                    ->where('clinic_id', $clinicId)
                    ->where('id', '<', $visit->id)
                    ->with('scales')
                    ->orderByDesc('created_at')
                    ->first();
            } catch (\Throwable $e) {
                Log::warning('EmrWebController@show: previous visit load failed', ['error' => $e->getMessage()]);
            }
        }

        $scaleChanges = [];
        if ($previousVisit && $previousVisit->scales->isNotEmpty()) {
            foreach ($visit->scales as $scale) {
                $prevScale = $previousVisit->scales->firstWhere('scale_name', $scale->scale_name);
                if ($prevScale) {
                    $scaleChanges[$scale->scale_name] = [
                        'previous' => $prevScale->score,
                        'current' => $scale->score,
                        'change' => $scale->score - $prevScale->score,
                    ];
                }
            }
        }

        // Available procedures for selection
        $availableProcedures = [
            'Chemical Peel', 'LASER', 'PRP', 'Botox', 'Fillers', 
            'Microneedling', 'Threads', 'Mesotherapy', 'Cryotherapy',
            'Excision', 'Biopsy', 'Cautery', 'RF Treatment'
        ];

        // Common complaints follow this visit's specialty (falls back to doctor profile)
        $complaintSpecialty = strtolower((string) ($visit->specialty ?? auth()->user()->specialty ?? 'general'));
        if ($complaintSpecialty === 'dentistry') {
            $complaintSpecialty = 'dental';
        }
        if (in_array($complaintSpecialty, ['orthopaedic', 'orthopedics', 'ortho'], true)) {
            $complaintSpecialty = 'orthopaedics';
        }
        $commonComplaints = $this->getCommonComplaints($complaintSpecialty);

        Log::info('EMR show loaded', [
            'visit_id' => $visit->id,
            'visit_history_count' => $visitHistory->count(),
            'photos_count' => $patientPhotos->flatten()->count(),
        ]);

        $patientAllergiesDisplay = [];
        try {
            $patientAllergiesDisplay = $patient->allergiesDisplayListForUi();
            Log::info('EMR show: merged patient allergies for prescription UI', [
                'patient_id' => $patient->id,
                'display_count' => count($patientAllergiesDisplay),
            ]);
        } catch (\Throwable $e) {
            Log::warning('EmrWebController@show: allergiesDisplayListForUi failed', ['error' => $e->getMessage(), 'patient_id' => $patient->id]);
        }

        // Pass scaleChanges as lastScales for specialty templates
        $lastScales = $scaleChanges;

        // Load custom EMR templates for this clinic/specialty
        $customTemplates = collect();
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('custom_emr_templates')) {
                $customQ = \DB::table('custom_emr_templates')
                    ->where('clinic_id', $clinicId)
                    ->where('is_active', true)
                    ->where(function($q) use ($visit) {
                        $q->whereNull('specialty')
                          ->orWhere('specialty', $visit->specialty ?? '');
                    });
                if (\Illuminate\Support\Facades\Schema::hasColumn('custom_emr_templates', 'sort_order')) {
                    $customQ->orderBy('sort_order');
                }
                $customTemplates = $customQ->orderBy('name')->get();
            }
        } catch (\Throwable $e) {
            \Log::warning('EmrWebController: custom templates load failed', ['error' => $e->getMessage()]);
        }

        return view('emr.show', compact(
            'patient', 'visit', 'visitHistory', 'prescription',
            'patientPhotos', 'labOrders', 'previousPrescriptions',
            'scaleChanges', 'previousVisit', 'availableProcedures', 'commonComplaints',
            'lastScales', 'customTemplates', 'patientAllergiesDisplay'
        ));
    }

    /**
     * When optional clinical tables are missing, Laravel would otherwise lazy-load relations in Blade and throw QueryExceptions (500).
     * Pre-set empty relations so the EMR view never triggers SQL for absent tables.
     */
    private function voidEmrVisitLazyLoadsForMissingTables(Visit $visit): void
    {
        if (! Schema::hasTable('visit_lesions')) {
            $visit->setRelation('lesions', collect());
        }
        if (! Schema::hasTable('visit_scales')) {
            $visit->setRelation('scales', collect());
        }
        if (! Schema::hasTable('visit_procedures')) {
            $visit->setRelation('procedures', collect());
        }
        if (! Schema::hasTable('prescriptions')) {
            $visit->setRelation('prescriptions', collect());
        } elseif (! Schema::hasTable('prescription_drugs')) {
            foreach ($visit->prescriptions as $rx) {
                if (! $rx->relationLoaded('drugs')) {
                    $rx->setRelation('drugs', collect());
                }
            }
        }
        if (! Schema::hasTable('patient_photos')) {
            $visit->setRelation('photos', collect());
        }
        if (! Schema::hasTable('invoices')) {
            $visit->setRelation('invoice', null);
        }
        if (! Schema::hasTable('physio_hep')) {
            $visit->setRelation('physioHep', collect());
        }

        Log::debug('EmrWebController: voidEmrVisitLazyLoadsForMissingTables', ['visit_id' => $visit->id]);
    }

    /**
     * Get common complaints based on specialty
     */
    private function getCommonComplaints(?string $specialty): array
    {
        $complaints = [
            'dermatology' => ['Acne', 'Rash', 'Pigmentation', 'Hair Loss', 'Psoriasis', 'Eczema', 'Itch', 'Warts', 'Fungal Infection'],
            'dental' => ['Tooth Pain', 'Cavity', 'Gum Problem', 'Cleaning', 'Root Canal', 'Extraction', 'Orthodontic'],
            'ophthalmology' => ['Vision Problem', 'Eye Pain', 'Redness', 'Watering', 'Cataract', 'Glaucoma Check', 'Refractive Error', 'Dry Eyes'],
            'general' => ['Fever', 'Cold', 'Cough', 'Body Pain', 'Headache', 'Stomach Pain', 'General Checkup'],
            'orthopedics' => ['Joint Pain', 'Back Pain', 'Fracture', 'Sprain', 'Arthritis', 'Sports Injury'],
            'orthopaedics' => ['Joint Pain', 'Back Pain', 'Knee Pain', 'Shoulder Pain', 'Fracture', 'Sprain', 'Arthritis', 'Sports Injury', 'Neck Pain', 'Hip Pain'],
            'ent' => ['Ear Pain', 'Hearing Loss', 'Tinnitus', 'Nasal Block', 'Sore Throat', 'Snoring', 'Sinusitis', 'Vertigo', 'Tonsillitis', 'Voice Change'],
            'gynaecology' => ['Routine Checkup', 'Menstrual Irregularity', 'PCOS', 'Pregnancy Confirmation', 'Infertility', 'White Discharge', 'Pelvic Pain', 'Antenatal Visit'],
            'gynecology' => ['Routine Checkup', 'Menstrual Problem', 'PCOS', 'Pregnancy', 'Infertility'],
            'physiotherapy' => ['Back Pain', 'Neck Pain', 'Knee Pain', 'Shoulder Pain', 'Post-Surgery Rehab', 'Sports Injury', 'Frozen Shoulder', 'Sciatica'],
        ];

        return $complaints[$specialty] ?? $complaints['general'];
    }

    /**
     * Resolve specialty for a new visit: explicit POST field → appointment → doctor → general.
     */
    private function resolveSpecialtyForVisit(?string $fromRequest, ?string $fromAppointment, ?string $fromDoctor): string
    {
        $raw = $fromRequest;
        if ($raw === null || trim((string) $raw) === '') {
            $raw = $fromAppointment;
        }
        if ($raw === null || trim((string) $raw) === '') {
            $raw = $fromDoctor;
        }
        if ($raw === null || trim((string) $raw) === '') {
            return 'general';
        }
        $s = strtolower(trim((string) $raw));
        if ($s === 'dentistry') {
            $s = 'dental';
        }

        return $s === '' ? 'general' : $s;
    }

    public function create(Request $request, Patient $patient): RedirectResponse
    {
        $clinicId = auth()->user()->clinic_id;
        abort_unless($patient->clinic_id === $clinicId, 403);

        try {
            // Find today's appointment for this patient, if any
            $appointment = Appointment::where('clinic_id', $clinicId)
                ->where('patient_id', $patient->id)
                ->whereDate('scheduled_at', today())
                ->whereIn('status', ['checked_in', 'in_consultation', 'confirmed'])
                ->latest('scheduled_at')
                ->first();

            // Generate visit number
            $visitNumber = Visit::where('patient_id', $patient->id)->max('visit_number') + 1;

            $resolvedSpecialty = $this->resolveSpecialtyForVisit(
                $request->input('specialty'),
                $appointment?->specialty,
                auth()->user()->specialty
            );

            $visitDoctorId = (int) ($appointment?->doctor_id ?? auth()->id());
            if ($visitDoctorId < 1) {
                $visitDoctorId = (int) auth()->id();
            }

            $visit = Visit::create([
                'clinic_id'      => $clinicId,
                'patient_id'     => $patient->id,
                'doctor_id'      => $visitDoctorId,
                'appointment_id' => $appointment?->id,
                'visit_number'   => $visitNumber,
                'status'         => 'draft',
                'specialty'      => $resolvedSpecialty,
                'started_at'     => now(),
            ]);

            // Update appointment status if linked
            if ($appointment) {
                $appointment->update(['status' => 'in_consultation']);
            }

            // Update patient visit tracking
            $patient->increment('visit_count');
            $patient->update(['last_visit_date' => today()]);

            Log::info('EMR visit created via web', [
                'visit_id' => $visit->id,
                'patient_id' => $patient->id,
                'visit_doctor_id' => $visitDoctorId,
                'auth_user_id' => auth()->id(),
                'resolved_specialty' => $resolvedSpecialty,
                'appointment_specialty' => $appointment?->specialty,
                'doctor_specialty' => auth()->user()->specialty,
            ]);

            return redirect()
                ->route('emr.show', [$patient, $visit])
                ->with('success', 'New visit started.');
        } catch (\Throwable $e) {
            Log::error('EMR create error', [
                'patient_id' => $patient->id, 
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            // In production, show generic error; in debug show actual error
            $errorMsg = config('app.debug') 
                ? 'Could not create visit: ' . $e->getMessage()
                : 'Could not create visit. Please try again.';

            return back()->with('error', $errorMsg);
        }
    }

    /**
     * Session-authenticated AI summary for the web EMR (Anthropic via ClaudeService).
     * The mobile API uses POST /api/v1/ai/summarise/{visitId} with Bearer tokens.
     *
     * Optional JSON body: { "draft": { ... same shape as PATCH emr.update from buildEmrPatchPayload } }
     * When present, draft is merged in-memory only (so summarisation uses unsaved notes even if autosave PATCH fails validation).
     */
    public function aiSummary(Request $request, Patient $patient, Visit $visit, ClaudeService $claude): JsonResponse
    {
        $clinicId = auth()->user()->clinic_id;
        abort_unless($patient->clinic_id === $clinicId && $visit->clinic_id === $clinicId, 403);
        abort_unless($visit->patient_id === $patient->id, 404);

        $draft = $request->input('draft');
        Log::info('EmrWebController@aiSummary', [
            'visit_id' => $visit->id,
            'patient_id' => $patient->id,
            'has_draft' => is_array($draft),
            'draft_keys' => is_array($draft) ? array_keys($draft) : [],
        ]);

        $relations = ['patient'];
        if (Schema::hasTable('prescriptions')) {
            $relations[] = 'prescriptions.drugs';
        }
        if (Schema::hasTable('visit_procedures')) {
            $relations[] = 'procedures';
        }
        if (Schema::hasTable('visit_scales')) {
            $relations[] = 'scales';
        }
        if (Schema::hasTable('visit_lesions')) {
            $relations[] = 'lesions';
        }
        $visit->load($relations);
        $visit->loadMissing('clinic');

        if (is_array($draft) && $draft !== []) {
            $this->applyEmrDraftForAiSummary($visit, $draft);
        }

        Log::info('EmrWebController@aiSummary visit snapshot', [
            'visit_id' => $visit->id,
            'history_len' => strlen((string) ($visit->history ?? '')),
            'chief_complaint_set' => (bool) ($visit->chief_complaint ?? ''),
            'merged_draft' => is_array($draft) && $draft !== [],
        ]);

        $summary = $claude->generateConsultationSummary($visit, 'en');

        $visit->refresh();
        $visit->ai_summary = $summary;
        $visit->save();

        Log::info('EmrWebController@aiSummary saved', ['visit_id' => $visit->id, 'summary_len' => strlen($summary)]);

        return response()->json(['success' => true, 'summary' => $summary]);
    }

    /**
     * Merge unsaved EMR form payload into the visit model in memory for AI summarisation only.
     * Does not persist draft fields (avoids validation rules that block PATCH autosave, e.g. empty followup_in_days).
     */
    private function applyEmrDraftForAiSummary(Visit $visit, array $draft): void
    {
        $specialty = $visit->specialty ?? 'general';

        foreach (['chief_complaint', 'history', 'diagnosis_text', 'diagnosis_code', 'plan'] as $k) {
            if (array_key_exists($k, $draft)) {
                $visit->setAttribute($k, $draft[$k]);
            }
        }

        if (array_key_exists('followup_in_days', $draft)) {
            $v = $draft['followup_in_days'];
            if ($v === '' || $v === null) {
                // leave DB value; do not coerce "" to 0
            } elseif (is_numeric($v)) {
                $visit->followup_in_days = (int) $v;
            }
        }

        if (array_key_exists('followup_date', $draft)) {
            $fd = $draft['followup_date'];
            $visit->followup_date = ($fd === '' || $fd === null) ? null : $fd;
        }

        $structuredData = array_merge($visit->structured_data ?? [], $draft['structured_data'] ?? []);

        $specialtyReq = Request::create('/', 'POST', $draft);
        $specialtyFields = $this->extractSpecialtyFields($specialtyReq, $specialty);
        if ($specialtyFields !== []) {
            $structuredData = array_merge($structuredData, $specialtyFields);
        }

        foreach ([
            'pasi_score', 'pasi_data', 'iga_score', 'dlqi_score', 'dlqi_data',
            'lesions_json', 'procedures_json',
        ] as $k) {
            if (array_key_exists($k, $draft)) {
                $structuredData[$k] = $draft[$k];
            }
        }

        $visit->structured_data = $structuredData;

        Log::info('EmrWebController@applyEmrDraftForAiSummary done', [
            'visit_id' => $visit->id,
            'history_len' => strlen((string) ($visit->history ?? '')),
            'structured_top_keys' => array_keys($structuredData),
        ]);
    }

    public function update(Request $request, Patient $patient, Visit $visit): JsonResponse
    {
        $clinicId = auth()->user()->clinic_id;
        abort_unless($patient->clinic_id === $clinicId && $visit->clinic_id === $clinicId, 403);
        abort_unless($visit->patient_id === $patient->id, 404);

        Log::info('EmrWebController@update', [
            'visit_id' => $visit->id,
            'specialty' => $visit->specialty,
            'fields' => array_keys($request->all())
        ]);

        $validated = $request->validate([
            'structured_data'  => ['nullable', 'array'],
            'chief_complaint'  => ['nullable', 'string', 'max:1000'],
            'history'          => ['nullable', 'string'],
            'diagnosis_code'   => ['nullable', 'string', 'max:20'],
            'diagnosis_text'   => ['nullable', 'string', 'max:500'],
            'plan'             => ['nullable', 'string'],
            'followup_in_days' => ['nullable', 'integer', 'min:1'],
            'followup_date'    => ['nullable', 'date'],
            'specialty'        => ['nullable', 'string', 'max:64'],
        ]);

        $prevSpecialty = $visit->specialty;

        if (array_key_exists('specialty', $validated)) {
            $incoming = $validated['specialty'];
            if ($incoming === null || trim((string) $incoming) === '') {
                unset($validated['specialty']);
            } else {
                $normalized = strtolower(trim((string) $incoming));
                if ($normalized === 'dentistry') {
                    $normalized = 'dental';
                }
                $validated['specialty'] = $normalized;
            }
        }

        if ($visit->status === 'finalised') {
            unset($validated['specialty']);
        }

        $specialtyForExtraction = $validated['specialty'] ?? $visit->specialty ?? 'general';

        try {
            // Merge validated structured_data with existing visit JSON, then apply specialty extraction from raw request
            $structuredData = array_merge($visit->structured_data ?? [], $validated['structured_data'] ?? []);
            $specialtyFields = $this->extractSpecialtyFields($request, $specialtyForExtraction);
            if (!empty($specialtyFields)) {
                $structuredData = array_merge($structuredData, $specialtyFields);
            }
            $validated['structured_data'] = $structuredData;

            // Handle lesions JSON (dermatology)
            if ($request->has('lesions_json')) {
                $this->saveLesionsFromJson($request->input('lesions_json'), $visit);
            }

            // Handle scales (dermatology)
            if ($request->has('pasi_score') || $request->has('iga_score') || $request->has('dlqi_score')) {
                $this->saveScalesFromRequest($request, $visit);
            }

            // Handle procedures JSON
            if ($request->has('procedures_json')) {
                $this->saveProceduresFromJson($request->input('procedures_json'), $visit, $clinicId);
            }

            // Handle dental teeth data
            if ($request->has('dental_teeth_data')) {
                $this->saveDentalTeethData($request->input('dental_teeth_data'), $patient, $clinicId);
            }

            // Handle physio HEP data
            if ($request->has('physio_hep_data')) {
                $this->savePhysioHepData($request->input('physio_hep_data'), $visit, $patient);
            }

            $visit->update($validated);

            if (isset($validated['specialty']) && $validated['specialty'] !== $prevSpecialty) {
                Log::info('EMR visit specialty changed', [
                    'visit_id' => $visit->id,
                    'from' => $prevSpecialty,
                    'to' => $validated['specialty'],
                ]);
            }

            return response()->json([
                'saved' => true,
                'at'    => now()->toIso8601String(),
                'specialty' => $visit->specialty,
            ]);
        } catch (\Throwable $e) {
            Log::error('EMR auto-save error', [
                'visit_id' => $visit->id, 
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['saved' => false, 'error' => 'Save failed.'], 500);
        }
    }

    /**
     * Extract specialty-specific fields from request (form POST or JSON body) into structured_data keys.
     */
    private function extractSpecialtyFields(Request $request, ?string $specialty): array
    {
        $fields = [];
        $specialty = strtolower($specialty ?? 'general');

        // Physiotherapy — scalar inputs
        $physioScalars = ['physio_mechanism', 'physio_body_part', 'physio_duration', 'physio_referring_doctor',
            'physio_previous_treatment', 'physio_vas_score', 'physio_pain_character',
            'physio_pain_pattern', 'physio_aggravating', 'physio_stg', 'physio_ltg', 'physio_session_current',
            'physio_sessions_total', 'physio_compliance', 'physio_progress'];
        foreach ($physioScalars as $field) {
            if ($request->has($field)) {
                $key = str_replace('physio_', 'physio.', $field);
                data_set($fields, $key, $request->input($field));
            }
        }
        if ($request->has('physio_rom_data')) {
            data_set($fields, 'physio.rom', $this->decodeJsonRequestValue($request->input('physio_rom_data')));
        }
        if ($request->has('physio_mmt_data')) {
            data_set($fields, 'physio.mmt', $this->decodeJsonRequestValue($request->input('physio_mmt_data')));
        }
        if ($request->has('physio_treatment_data')) {
            $treat = $this->decodeJsonRequestValue($request->input('physio_treatment_data'));
            if (is_array($treat)) {
                data_set($fields, 'physio.modalities', $treat['modalities'] ?? []);
                data_set($fields, 'physio.modality_params', $treat['params'] ?? []);
            }
        }
        foreach (['physio_fim_data' => 'physio.fim', 'physio_barthel_data' => 'physio.barthel', 'physio_womac_data' => 'physio.womac'] as $req => $dot) {
            if ($request->has($req)) {
                data_set($fields, $dot, $this->decodeJsonRequestValue($request->input($req)));
            }
        }

        // Dental fields
        $dentalFields = ['dental_proc_teeth', 'dental_proc_done', 'dental_anesthesia', 'dental_material',
            'dental_shade', 'dental_proc_notes', 'dental_xray_type', 'dental_xray_region',
            'dental_xray_findings', 'dental_treatment_plan', 'dental_lab_orders'];
        foreach ($dentalFields as $field) {
            if ($request->has($field)) {
                $key = str_replace('dental_', 'dental.', $field);
                $value = $request->input($field);
                if (in_array($field, ['dental_treatment_plan', 'dental_lab_orders'], true) && is_string($value)) {
                    $value = json_decode($value, true);
                }
                data_set($fields, $key, $value);
            }
        }

        // Ophthalmology (legacy prefixed fields, if present)
        $ophthalFields = [
            'ophthal_va_data', 'ophthal_iop_data', 'ophthal_refraction_data',
            'ophthal_slit_lamp_data', 'ophthal_fundus_data', 'ophthal_diagnosis_codes',
            'ophthal_spectacle_rx', 'ophthal_contact_lens_rx', 'ophthal_procedures',
        ];
        foreach ($ophthalFields as $field) {
            if ($request->has($field)) {
                $key = str_replace('ophthal_', 'ophthal.', $field);
                $value = $this->decodeJsonRequestValue($request->input($field));
                data_set($fields, $key, $value);
                Log::info('EMR ophthal field extracted', ['field' => $field]);
            }
        }

        // Orthopaedics — matches emr/specialty/orthopaedics.blade.php hidden names
        $orthoMap = [
            'ortho_joints' => 'ortho.joints',
            'ortho_pain' => 'ortho.pain',
            'ortho_rom' => 'ortho.rom',
            'ortho_power' => 'ortho.power',
            'ortho_special_tests' => 'ortho.specialTests',
            'ortho_fracture' => 'ortho.fracture',
            'ortho_treatment' => 'ortho.treatment',
            'ortho_exam' => 'ortho.exam',
        ];
        foreach ($orthoMap as $req => $dot) {
            if ($request->has($req)) {
                data_set($fields, $dot, $this->decodeJsonRequestValue($request->input($req)));
                Log::info('EMR ortho field extracted', ['field' => $req]);
            }
        }

        // ENT — matches ent.blade.php
        foreach (['ent_ear' => 'ent.ear', 'ent_nose' => 'ent.nose', 'ent_throat' => 'ent.throat', 'ent_larynx' => 'ent.larynx'] as $req => $dot) {
            if ($request->has($req)) {
                data_set($fields, $dot, $this->decodeJsonRequestValue($request->input($req)));
                Log::info('EMR ENT field extracted', ['field' => $req]);
            }
        }
        if ($request->has('ent_diagnoses')) {
            data_set($fields, 'ent.diagnoses', $this->decodeJsonRequestValue($request->input('ent_diagnoses')));
        }
        foreach (['ent_audiogram_data' => 'ent.audiogram', 'ent_tympanogram_data' => 'ent.tympanogram', 'ent_endoscopy_data' => 'ent.endoscopy', 'ent_vertigo_data' => 'ent.vertigo', 'ent_diagnosis_codes' => 'ent.diagnosis_codes'] as $req => $dot) {
            if ($request->has($req)) {
                data_set($fields, $dot, $this->decodeJsonRequestValue($request->input($req)));
            }
        }

        // Gynaecology — matches gynaecology.blade.php
        foreach ([
            'gynae_menstrual' => 'gynae.menstrual',
            'gynae_obs_history' => 'gynae.obsHistory',
            'gynae_pv' => 'gynae.pv',
            'gynae_screening' => 'gynae.screening',
            'obs_pregnancy' => 'obs.pregnancy',
            'obs_anc' => 'obs.anc',
            'obs_investigations' => 'obs.investigations',
            'obs_usg' => 'obs.usg',
        ] as $req => $dot) {
            if ($request->has($req)) {
                data_set($fields, $dot, $this->decodeJsonRequestValue($request->input($req)));
                Log::info('EMR gynae field extracted', ['field' => $req]);
            }
        }
        if ($request->has('gynae_diagnoses')) {
            data_set($fields, 'gynae.diagnoses', $this->decodeJsonRequestValue($request->input('gynae_diagnoses')));
        }

        // Form-based specialty templates: single JSON blob + ICD chips (same keys as getStructuredField in blades)
        $this->mergeGenericSpecialtyTemplateFields($request, $fields);

        if (in_array($specialty, ['ophthalmology', 'eye'], true)) {
            $this->mergeOphthalmologyTemplateFields($request, $fields);
        }
        if ($request->has('ophthal_systemic_data')) {
            $fields['ophthal_systemic_data'] = $this->decodeJsonRequestValue($request->input('ophthal_systemic_data'));
            Log::info('EMR ophthal_systemic_data merged');
        }

        if (!empty($fields)) {
            Log::info('EMR specialty fields extracted', [
                'specialty' => $specialty,
                'top_level_keys' => array_keys($fields),
            ]);
        }

        return $fields;
    }

    /**
     * Decode JSON strings from hidden Alpine fields; leave scalars/arrays as-is.
     */
    private function decodeJsonRequestValue(mixed $value): mixed
    {
        if (is_array($value)) {
            return $value;
        }
        if (!is_string($value)) {
            return $value;
        }
        $t = trim($value);
        if ($t === '' || ($t[0] !== '{' && $t[0] !== '[')) {
            return $value;
        }
        $decoded = json_decode($value, true);

        return $decoded !== null ? $decoded : $value;
    }

    /**
     * Merge *_data / *_diagnoses JSON blobs from internal-medicine style EMR templates into structured_data.
     */
    private function mergeGenericSpecialtyTemplateFields(Request $request, array &$fields): void
    {
        $keys = [
            'general_physician_data', 'general_physician_diagnoses',
            'cardiology_data', 'cardiology_diagnoses',
            'paediatrics_data', 'paediatrics_diagnoses',
            'gastroenterology_data', 'gastroenterology_diagnoses',
            'nephrology_data', 'nephrology_diagnoses',
            'endocrinology_data', 'endocrinology_diagnoses',
            'diabetology_data', 'diabetology_diagnoses',
            'pulmonology_data', 'pulmonology_diagnoses',
            'neurology_data', 'neurology_diagnoses',
            'oncology_data', 'oncology_diagnoses',
            'psychiatry_data', 'psychiatry_diagnoses',
            'rheumatology_data', 'rheumatology_diagnoses',
            'urology_data', 'urology_diagnoses',
            'general_surgery_data', 'general_surgery_diagnoses',
            'ayush_data', 'ayush_diagnoses',
            'homeopathy_data', 'homeopathy_diagnoses',
        ];
        foreach ($keys as $name) {
            if (!$request->has($name)) {
                continue;
            }
            $raw = $request->input($name);
            $fields[$name] = is_string($raw) ? $this->decodeJsonRequestValue($raw) : $raw;
            Log::info('EMR mergeGenericSpecialtyTemplateFields', ['name' => $name]);
        }
    }

    /**
     * Map short hidden field names from ophthalmology.blade.php into structured_data (ophthal.*).
     */
    private function mergeOphthalmologyTemplateFields(Request $request, array &$fields): void
    {
        $map = [
            'va_data' => 'ophthal.va',
            'iop_data' => 'ophthal.iop',
            'refraction_data' => 'ophthal.refraction',
            'slit_lamp_data' => 'ophthal.slitLamp',
            'fundus_data' => 'ophthal.fundus',
        ];

        foreach ($map as $reqKey => $dotKey) {
            if (!$request->has($reqKey)) {
                continue;
            }
            $v = $request->input($reqKey);
            $trim = is_string($v) ? trim($v) : '';
            $decoded = $v;
            if (is_string($v) && ($trim !== '' && ($trim[0] === '{' || $trim[0] === '['))) {
                $decoded = json_decode($v, true) ?? $v;
            }
            data_set($fields, $dotKey, $decoded);
            Log::info('EMR mergeOphthalmologyTemplateFields', ['from' => $reqKey, 'to' => $dotKey]);
        }

        if ($request->has('ophthal_diagnoses')) {
            $v = $request->input('ophthal_diagnoses');
            $decoded = is_string($v) ? json_decode($v, true) : $v;
            $list = $decoded ?? [];
            data_set($fields, 'ophthal.diagnosis_codes', $list);
            data_set($fields, 'ophthal.diagnoses', $list);
            Log::info('EMR mergeOphthalmologyTemplateFields', ['from' => 'ophthal_diagnoses', 'to' => 'ophthal.diagnosis_codes+diagnoses']);
        }
    }

    /**
     * Save lesions from JSON data (dermatology)
     */
    private function saveLesionsFromJson(string $json, Visit $visit): void
    {
        $lesions = json_decode($json, true);
        if (!is_array($lesions)) return;

        Log::info('Saving lesions from JSON', ['visit_id' => $visit->id, 'count' => count($lesions)]);

        // Delete existing lesions and insert new ones
        VisitLesion::where('visit_id', $visit->id)->delete();

        foreach ($lesions as $lesion) {
            VisitLesion::create([
                'visit_id' => $visit->id,
                'body_region' => $lesion['region'] ?? 'Unknown',
                'view' => $lesion['view'] ?? 'front',
                'x_pct' => $lesion['x'] ?? 50,
                'y_pct' => $lesion['y'] ?? 50,
                'lesion_type' => $lesion['type'] ?? 'Unspecified',
                'size_cm' => !empty($lesion['size']) ? $lesion['size'] : null,
                'colour' => $lesion['colour'] ?? null,
                'border' => $lesion['border'] ?? null,
                'surface' => $lesion['surface'] ?? null,
                'distribution' => $lesion['distribution'] ?? null,
                'notes' => $lesion['notes'] ?? null,
            ]);
        }
    }

    /**
     * Save scales from request (dermatology)
     */
    private function saveScalesFromRequest(Request $request, Visit $visit): void
    {
        Log::info('Saving scales from request', ['visit_id' => $visit->id]);

        if ($request->has('pasi_score') && $request->input('pasi_score') !== null) {
            VisitScale::updateOrCreate(
                ['visit_id' => $visit->id, 'scale_name' => 'PASI'],
                [
                    'score' => $request->input('pasi_score'),
                    'components' => $request->has('pasi_data') ? json_decode($request->input('pasi_data'), true) : null,
                    'interpretation' => $this->getPasiInterpretation($request->input('pasi_score')),
                ]
            );
        }

        if ($request->has('iga_score') && $request->input('iga_score') !== null) {
            $igaLabels = ['Clear', 'Almost Clear', 'Mild', 'Moderate', 'Severe'];
            VisitScale::updateOrCreate(
                ['visit_id' => $visit->id, 'scale_name' => 'IGA'],
                [
                    'score' => $request->input('iga_score'),
                    'interpretation' => $igaLabels[(int)$request->input('iga_score')] ?? 'Unknown',
                ]
            );
        }

        if ($request->has('dlqi_score') && $request->input('dlqi_score') !== null) {
            VisitScale::updateOrCreate(
                ['visit_id' => $visit->id, 'scale_name' => 'DLQI'],
                [
                    'score' => $request->input('dlqi_score'),
                    'components' => $request->has('dlqi_data') ? json_decode($request->input('dlqi_data'), true) : null,
                    'interpretation' => $this->getDlqiInterpretation($request->input('dlqi_score')),
                ]
            );
        }
    }

    private function getPasiInterpretation($score): string
    {
        if ($score < 5) return 'Mild';
        if ($score < 10) return 'Moderate';
        return 'Severe';
    }

    private function getDlqiInterpretation($score): string
    {
        if ($score <= 1) return 'No effect on quality of life';
        if ($score <= 5) return 'Small effect on quality of life';
        if ($score <= 10) return 'Moderate effect on quality of life';
        if ($score <= 20) return 'Large effect on quality of life';
        return 'Extremely large effect on quality of life';
    }

    /**
     * Save procedures from JSON data
     */
    private function saveProceduresFromJson(string $json, Visit $visit, int $clinicId): void
    {
        $procedures = json_decode($json, true);
        if (!is_array($procedures)) return;

        Log::info('Saving procedures from JSON', ['visit_id' => $visit->id, 'count' => count($procedures)]);

        // Delete existing procedures and insert new ones
        VisitProcedure::where('visit_id', $visit->id)->delete();

        foreach ($procedures as $proc) {
            VisitProcedure::create([
                'visit_id' => $visit->id,
                'clinic_id' => $clinicId,
                'procedure_code' => $proc['code'] ?? null,
                'procedure_name' => $proc['name'] ?? 'Unknown Procedure',
                'specialty' => $visit->specialty ?? 'general',
                'body_region' => $proc['region'] ?? null,
                'notes' => $proc['notes'] ?? null,
                'parameters' => array_filter([
                    'laserType' => $proc['laserType'] ?? null,
                    'settings' => $proc['settings'] ?? null,
                    'agent' => $proc['agent'] ?? null,
                    'concentration' => $proc['concentration'] ?? null,
                    'session' => $proc['session'] ?? null,
                    'units' => $proc['units'] ?? null,
                    'sites' => $proc['sites'] ?? null,
                ]),
            ]);
        }
    }

    /**
     * Save dental teeth data
     */
    private function saveDentalTeethData(string $json, Patient $patient, int $clinicId): void
    {
        $teeth = json_decode($json, true);
        if (!is_array($teeth)) return;

        Log::info('Saving dental teeth data', ['patient_id' => $patient->id, 'teeth_count' => count($teeth)]);

        foreach ($teeth as $toothCode => $toothData) {
            // Only save teeth that have been modified from defaults
            if ($toothData['status'] === 'present' && 
                $toothData['caries'] === 'none' && 
                $toothData['restoration'] === 'none' &&
                empty($toothData['notes'])) {
                continue;
            }

            DB::table('dental_teeth')->updateOrInsert(
                ['patient_id' => $patient->id, 'tooth_code' => (string)$toothCode],
                [
                    'clinic_id' => $clinicId,
                    'status' => $toothData['status'] ?? 'present',
                    'caries' => $toothData['caries'] ?? 'none',
                    'restoration' => $toothData['restoration'] ?? 'none',
                    'mobility_grade' => !empty($toothData['mobility']) ? (int)$toothData['mobility'] : null,
                    'recession_mm' => !empty($toothData['recession']) ? (float)$toothData['recession'] : null,
                    'bop' => !empty($toothData['bop']) ? (bool)$toothData['bop'] : null,
                    'notes' => $toothData['notes'] ?? null,
                    'last_updated_by' => auth()->id(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    /**
     * Save physio HEP data
     */
    private function savePhysioHepData(string $json, Visit $visit, Patient $patient): void
    {
        $exercises = json_decode($json, true);
        if (!is_array($exercises)) return;

        Log::info('Saving physio HEP data', ['visit_id' => $visit->id, 'exercises_count' => count($exercises)]);

        // Delete existing HEP and insert new ones
        DB::table('physio_hep')->where('visit_id', $visit->id)->delete();

        foreach ($exercises as $exercise) {
            if (empty($exercise['name'])) continue;
            
            DB::table('physio_hep')->insert([
                'visit_id' => $visit->id,
                'patient_id' => $patient->id,
                'exercise_name' => $exercise['name'],
                'sets' => !empty($exercise['sets']) ? (int)$exercise['sets'] : null,
                'reps' => !empty($exercise['reps']) ? (int)$exercise['reps'] : null,
                'hold_seconds' => !empty($exercise['hold']) ? (int)$exercise['hold'] : null,
                'frequency_per_day' => !empty($exercise['frequency']) ? (int)$exercise['frequency'] : null,
                'instructions' => $exercise['instructions'] ?? null,
                'created_at' => now(),
            ]);
        }
    }

    public function finalise(Request $request, Patient $patient, Visit $visit): RedirectResponse
    {
        $clinicId = auth()->user()->clinic_id;
        abort_unless($patient->clinic_id === $clinicId && $visit->clinic_id === $clinicId, 403);
        abort_unless($visit->patient_id === $patient->id, 404);

        Log::info('EMR finalise started', ['visit_id' => $visit->id, 'patient_id' => $patient->id]);

        try {
            $prescription = Prescription::where('visit_id', $visit->id)->with('drugs')->first();
            if ($prescription && $prescription->drugs->isNotEmpty() && Schema::hasColumn('prescriptions', 'safety_acknowledged_at')) {
                $drugs = $prescription->drugs->map(static fn ($d) => [
                    'name' => $d->drug_name,
                    'generic' => $d->generic_name ?? '',
                    'dose' => $d->dose,
                    'frequency' => $d->frequency,
                    'duration' => $d->duration,
                    'instructions' => $d->instructions,
                ])->all();
                $analysis = PrescriptionSafetyService::analyze($patient, $drugs);
                Log::info('EMR finalise: prescription safety check', [
                    'visit_id' => $visit->id,
                    'blocking' => $analysis['blocking'],
                    'has_ack' => (bool) $prescription->safety_acknowledged_at,
                ]);
                if ($analysis['blocking'] && !$prescription->safety_acknowledged_at) {
                    return back()->with(
                        'error',
                        'This prescription has a blocking allergy match or major drug–drug interaction. Open the Prescription tab, review alerts, and use “Acknowledge & record reason” before finalising.'
                    );
                }
            }

            // Mark visit as finalised (completed)
            $visit->update([
                'status'       => 'finalised',
                'finalised_at' => now(),
            ]);

            Log::info('Visit status updated to finalised', ['visit_id' => $visit->id]);

            // Update linked appointment
            if ($visit->appointment_id) {
                Appointment::where('id', $visit->appointment_id)
                    ->update(['status' => 'completed']);
                Log::info('Appointment status updated', ['appointment_id' => $visit->appointment_id]);
            }

            // Update patient follow-up date if set
            if ($visit->followup_date) {
                $patient->update(['next_followup_date' => $visit->followup_date]);
            }

            // Load prescription for PDF (optional - don't fail if no prescription)
            $visit->load(['prescriptions.drugs', 'doctor']);
            $clinic = auth()->user()->clinic;

            // Try to generate prescription PDF (non-blocking)
            try {
                if ($visit->prescriptions->isNotEmpty() && class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
                    $prescriptionPdf = Pdf::loadView('emr.prescription-pdf', compact('visit', 'patient', 'clinic'));
                    $pdfFilename = "prescription-{$visit->id}-{$patient->id}.pdf";
                    $pdfPath = storage_path("app/public/prescriptions/{$pdfFilename}");

                    // Ensure directory exists
                    if (!is_dir(dirname($pdfPath))) {
                        mkdir(dirname($pdfPath), 0755, true);
                    }
                    $prescriptionPdf->save($pdfPath);
                    Log::info('Prescription PDF generated', ['path' => $pdfPath]);
                }
            } catch (\Throwable $e) {
                Log::warning('PDF generation failed (non-blocking)', ['error' => $e->getMessage()]);
            }

            // Try to send WhatsApp (non-blocking)
            try {
                if ($this->whatsAppService && $clinic && $patient->phone) {
                    $this->whatsAppService->sendTemplate(
                        $clinic,
                        $patient,
                        'prescription_ready',
                        [
                            [
                                'type'       => 'body',
                                'parameters' => [
                                    ['type' => 'text', 'text' => $patient->name],
                                    ['type' => 'text', 'text' => $visit->diagnosis_text ?? 'your consultation'],
                                    ['type' => 'text', 'text' => $clinic->name ?? 'Clinic'],
                                ],
                            ],
                        ],
                        'prescription_ready',
                        $visit->id
                    );
                    Log::info('WhatsApp notification sent', ['patient_id' => $patient->id]);
                }
            } catch (\Throwable $e) {
                Log::warning('WhatsApp send failed (non-blocking)', ['error' => $e->getMessage()]);
            }

            Log::info('Visit finalised successfully', ['visit_id' => $visit->id, 'patient_id' => $patient->id]);

            // Doctor UX: return to the EMR workspace — not the patient profile (that felt like a confusing "second page").
            return redirect()
                ->route('emr.index')
                ->with('success', 'Visit finalised. Patient: '.$patient->name.'. You can open their profile from the list or Patients menu.');
        } catch (\Throwable $e) {
            Log::error('EMR finalise error', [
                'visit_id' => $visit->id, 
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $errorMsg = config('app.debug') 
                ? 'Could not finalise visit: ' . $e->getMessage()
                : 'Could not finalise visit. Please try again.';

            return back()->with('error', $errorMsg);
        }
    }

    /**
     * Add a lesion annotation to a visit
     */
    public function addLesion(Request $request, Patient $patient, Visit $visit): JsonResponse
    {
        $clinicId = auth()->user()->clinic_id;
        abort_unless($patient->clinic_id === $clinicId && $visit->clinic_id === $clinicId, 403);

        Log::info('EmrWebController@addLesion', ['visit_id' => $visit->id]);

        $validated = $request->validate([
            'body_region' => ['required', 'string', 'max:100'],
            'lesion_type' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:500'],
            'size_cm' => ['nullable', 'numeric', 'min:0', 'max:50'],
            'colour' => ['nullable', 'string', 'max:50'],
            'distribution' => ['nullable', 'string', 'max:100'],
            'surface' => ['nullable', 'string', 'max:100'],
            'x_pct' => ['nullable', 'numeric'],
            'y_pct' => ['nullable', 'numeric'],
            'view' => ['nullable', 'string', 'max:20'],
        ]);

        try {
            $lesion = VisitLesion::create([
                'visit_id' => $visit->id,
                'body_region' => $validated['body_region'],
                'lesion_type' => $validated['lesion_type'],
                'notes' => $validated['description'] ?? null,
                'size_cm' => $validated['size_cm'] ?? null,
                'colour' => $validated['colour'] ?? null,
                'distribution' => $validated['distribution'] ?? null,
                'surface' => $validated['surface'] ?? null,
                'x_pct' => $validated['x_pct'] ?? null,
                'y_pct' => $validated['y_pct'] ?? null,
                'view' => $validated['view'] ?? 'front',
            ]);

            Log::info('Lesion added', ['lesion_id' => $lesion->id, 'visit_id' => $visit->id]);

            return response()->json([
                'success' => true,
                'lesion' => $lesion,
            ]);
        } catch (\Throwable $e) {
            Log::error('Add lesion error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove a lesion annotation
     */
    public function removeLesion(Request $request, Patient $patient, Visit $visit, VisitLesion $lesion): JsonResponse
    {
        $clinicId = auth()->user()->clinic_id;
        abort_unless($visit->clinic_id === $clinicId && $lesion->visit_id === $visit->id, 403);

        Log::info('EmrWebController@removeLesion', ['lesion_id' => $lesion->id]);

        try {
            $lesion->delete();
            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            Log::error('Remove lesion error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Save grading scales for a visit
     */
    public function saveScales(Request $request, Patient $patient, Visit $visit): JsonResponse
    {
        $clinicId = auth()->user()->clinic_id;
        abort_unless($patient->clinic_id === $clinicId && $visit->clinic_id === $clinicId, 403);

        Log::info('EmrWebController@saveScales', ['visit_id' => $visit->id]);

        $validated = $request->validate([
            'scales' => ['required', 'array'],
            'scales.*.name' => ['required', 'string', 'max:50'],
            'scales.*.score' => ['required', 'numeric'],
            'scales.*.interpretation' => ['nullable', 'string', 'max:100'],
        ]);

        try {
            // Delete existing scales and insert new ones
            VisitScale::where('visit_id', $visit->id)->delete();

            foreach ($validated['scales'] as $scaleData) {
                VisitScale::create([
                    'visit_id' => $visit->id,
                    'scale_name' => $scaleData['name'],
                    'score' => $scaleData['score'],
                    'interpretation' => $scaleData['interpretation'] ?? null,
                ]);
            }

            Log::info('Scales saved', ['visit_id' => $visit->id, 'count' => count($validated['scales'])]);

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            Log::error('Save scales error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Save procedures for a visit
     */
    public function saveProcedures(Request $request, Patient $patient, Visit $visit): JsonResponse
    {
        $clinicId = auth()->user()->clinic_id;
        abort_unless($patient->clinic_id === $clinicId && $visit->clinic_id === $clinicId, 403);

        Log::info('EmrWebController@saveProcedures', ['visit_id' => $visit->id]);

        $validated = $request->validate([
            'procedures' => ['required', 'array'],
            'procedures.*.name' => ['required', 'string', 'max:100'],
            'procedures.*.agent' => ['nullable', 'string', 'max:100'],
            'procedures.*.areas' => ['nullable', 'string', 'max:200'],
            'procedures.*.session' => ['nullable', 'string', 'max:50'],
            'procedures.*.notes' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            // Delete existing and insert new
            VisitProcedure::where('visit_id', $visit->id)->delete();

            foreach ($validated['procedures'] as $procData) {
                VisitProcedure::create([
                    'visit_id' => $visit->id,
                    'clinic_id' => $clinicId,
                    'procedure_name' => $procData['name'],
                    'body_region' => $procData['areas'] ?? null,
                    'notes' => $procData['notes'] ?? null,
                    'parameters' => [
                        'agent' => $procData['agent'] ?? null,
                        'session_number' => $procData['session'] ?? null,
                    ],
                ]);
            }

            Log::info('Procedures saved', ['visit_id' => $visit->id, 'count' => count($validated['procedures'])]);

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            Log::error('Save procedures error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Save prescription for a visit
     */
    public function savePrescription(Request $request, Patient $patient, Visit $visit): JsonResponse
    {
        $clinicId = auth()->user()->clinic_id;
        abort_unless($patient->clinic_id === $clinicId && $visit->clinic_id === $clinicId, 403);

        Log::info('EmrWebController@savePrescription started', [
            'visit_id' => $visit->id,
            'patient_id' => $patient->id,
            'clinic_id' => $clinicId,
            'has_known_allergies' => !empty($patient->known_allergies),
        ]);

        $validated = $request->validate([
            'drugs' => ['required', 'array', 'min:1'],
            'drugs.*.name' => ['required', 'string', 'max:200'],
            'drugs.*.generic' => ['nullable', 'string', 'max:200'],
            'drugs.*.dose' => ['required', 'string', 'max:100'],
            'drugs.*.frequency' => ['required', 'string', 'max:100'],
            'drugs.*.duration' => ['required', 'string', 'max:100'],
            'drugs.*.instructions' => ['nullable', 'string', 'max:500'],
            'acknowledge_safety_warnings' => ['sometimes', 'boolean'],
            'override_reason' => ['nullable', 'string', 'max:2000'],
            'autosave' => ['sometimes', 'boolean'],
        ]);

        try {
            Log::info('EmrWebController@savePrescription payload validated', [
                'visit_id' => $visit->id,
                'drugs_count' => count($validated['drugs']),
                'autosave' => (bool) ($validated['autosave'] ?? false),
                'ack_requested' => (bool) ($validated['acknowledge_safety_warnings'] ?? false),
            ]);

            $analysis = PrescriptionSafetyService::analyze($patient, $validated['drugs']);
            $allergyWarnings = $analysis['allergy_warnings'];
            $interactionWarnings = $analysis['interaction_warnings'];
            $blocking = $analysis['blocking'];

            Log::info('EmrWebController@savePrescription safety check complete', [
                'visit_id' => $visit->id,
                'allergy_warning_count' => count($allergyWarnings),
                'interaction_warning_count' => count($interactionWarnings),
                'blocking' => $blocking,
            ]);

            if (! empty($allergyWarnings)) {
                Log::warning('EmrWebController@savePrescription allergy warnings detected', [
                    'visit_id' => $visit->id,
                    'patient_id' => $patient->id,
                    'allergy_warnings' => $allergyWarnings,
                ]);
            }

            if (! empty($interactionWarnings)) {
                Log::warning('EmrWebController@savePrescription interaction warnings detected', [
                    'visit_id' => $visit->id,
                    'patient_id' => $patient->id,
                    'interaction_warnings' => $interactionWarnings,
                ]);
            }

            $ack = ! empty($validated['acknowledge_safety_warnings']);
            $reason = trim((string) ($validated['override_reason'] ?? ''));

            DB::beginTransaction();

            $prescription = Prescription::firstOrNew(['visit_id' => $visit->id]);
            if (! $prescription->exists) {
                $prescription->clinic_id = $clinicId;
                $prescription->patient_id = $patient->id;
                $prescription->doctor_id = auth()->id();
            } else {
                $prescription->doctor_id = auth()->id();
            }

            if (Schema::hasColumn('prescriptions', 'safety_acknowledged_at')) {
                if ($blocking && $ack && strlen($reason) >= 10) {
                    $prescription->safety_acknowledged_at = now();
                    $prescription->safety_override_reason = $reason;
                    $prescription->safety_acknowledged_by = auth()->id();
                    Log::info('EmrWebController@savePrescription safety override recorded', [
                        'visit_id' => $visit->id,
                        'prescription_id' => $prescription->id,
                    ]);
                    AuditLog::log(
                        'prescription_safety_override',
                        'Prescription saved with documented override of blocking allergy or major interaction warnings.',
                        Prescription::class,
                        $prescription->id,
                        null,
                        [
                            'visit_id' => $visit->id,
                            'patient_id' => $patient->id,
                            'override_reason' => $reason,
                            'blocking_reasons' => $analysis['blocking_reasons'],
                        ]
                    );
                } elseif (! $blocking) {
                    $prescription->safety_acknowledged_at = null;
                    $prescription->safety_override_reason = null;
                    $prescription->safety_acknowledged_by = null;
                    Log::info('EmrWebController@savePrescription cleared safety acknowledgement (no longer blocking)', [
                        'visit_id' => $visit->id,
                    ]);
                }
            }

            $prescription->save();

            Log::info('EmrWebController@savePrescription upserted prescription', [
                'visit_id' => $visit->id,
                'prescription_id' => $prescription->id,
            ]);

            // Delete existing drugs and add new ones
            PrescriptionDrug::where('prescription_id', $prescription->id)->delete();
            Log::info('EmrWebController@savePrescription cleared old prescription drugs', [
                'prescription_id' => $prescription->id,
            ]);

            foreach ($validated['drugs'] as $index => $drugData) {
                PrescriptionDrug::create([
                    'prescription_id' => $prescription->id,
                    'drug_name' => $drugData['name'],
                    'generic_name' => $drugData['generic'] ?? null,
                    'dose' => $drugData['dose'],
                    'frequency' => $drugData['frequency'],
                    'duration' => $drugData['duration'],
                    'instructions' => $drugData['instructions'] ?? null,
                    'sort_order' => $index + 1,
                ]);
            }
            Log::info('EmrWebController@savePrescription inserted prescription drugs', [
                'prescription_id' => $prescription->id,
                'inserted_count' => count($validated['drugs']),
            ]);

            DB::commit();

            Log::info('Prescription saved with safety checks', [
                'prescription_id' => $prescription->id,
                'drugs_count' => count($validated['drugs']),
                'allergy_warning_count' => count($allergyWarnings),
                'interaction_warning_count' => count($interactionWarnings),
                'blocking' => $blocking,
                'safety_ack_on_record' => (bool) $prescription->safety_acknowledged_at,
            ]);

            $prescription->refresh();

            $hasSafetyCols = Schema::hasColumn('prescriptions', 'safety_acknowledged_at');

            return response()->json([
                'success' => true,
                'prescription_id' => $prescription->id,
                'blocking' => $blocking,
                'blocking_reasons' => $analysis['blocking_reasons'],
                'safety_ack_required' => $hasSafetyCols && $blocking && ! $prescription->safety_acknowledged_at,
                'safety_acknowledged_at' => $hasSafetyCols ? $prescription->safety_acknowledged_at?->toIso8601String() : null,
                'warnings' => [
                    'allergies' => $allergyWarnings,
                    'interactions' => $interactionWarnings,
                ],
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Save prescription error', [
                'visit_id' => $visit->id,
                'patient_id' => $patient->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Save custom EMR field data for a visit
     */
    public function saveCustomFields(Request $request, Patient $patient, Visit $visit): JsonResponse
    {
        $clinicId = auth()->user()->clinic_id;
        abort_unless($patient->clinic_id === $clinicId && $visit->clinic_id === $clinicId, 403);
        abort_unless($visit->patient_id === $patient->id, 404);

        Log::info('EmrWebController@saveCustomFields', ['visit_id' => $visit->id]);

        $validated = $request->validate([
            'custom_field_data' => ['required', 'string'],
        ]);

        try {
            $decoded = json_decode($validated['custom_field_data'], true);
            if (!is_array($decoded)) {
                return response()->json(['success' => false, 'error' => 'Invalid JSON in custom_field_data'], 422);
            }

            $structuredData = $visit->structured_data ?? [];
            $structuredData['custom'] = array_merge($structuredData['custom'] ?? [], $decoded);

            $visit->update(['structured_data' => $structuredData]);

            Log::info('Custom fields saved', ['visit_id' => $visit->id]);

            return response()->json(['success' => true, 'at' => now()->toIso8601String()]);
        } catch (\Throwable $e) {
            Log::error('Save custom fields error', ['visit_id' => $visit->id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => 'Save failed.'], 500);
        }
    }

    /**
     * Search drugs from Indian drug database
     */
    public function searchDrugs(Request $request): JsonResponse
    {
        $query = $request->input('q', '');
        
        Log::info('EmrWebController@searchDrugs', ['query' => $query]);

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        try {
            $drugs = IndianDrug::where('brand_name', 'like', "%{$query}%")
                ->orWhere('generic_name', 'like', "%{$query}%")
                ->orWhere('composition', 'like', "%{$query}%")
                ->select('id', 'brand_name', 'generic_name', 'composition', 'manufacturer', 'strength')
                ->limit(20)
                ->get();

            return response()->json($drugs);
        } catch (\Throwable $e) {
            // If IndianDrug table doesn't exist, return sample data
            Log::warning('Drug search failed, returning sample data', ['error' => $e->getMessage()]);
            
            $sampleDrugs = collect([
                ['id' => 1, 'brand_name' => 'Adapalene 0.1% gel', 'generic_name' => 'Adapalene', 'composition' => 'Adapalene 0.1%', 'manufacturer' => 'Differin', 'strength' => '0.1%'],
                ['id' => 2, 'brand_name' => 'Tretinoin 0.025%', 'generic_name' => 'Tretinoin', 'composition' => 'Tretinoin 0.025%', 'manufacturer' => 'Retin-A', 'strength' => '0.025%'],
                ['id' => 3, 'brand_name' => 'Clindamycin 1% lotion', 'generic_name' => 'Clindamycin', 'composition' => 'Clindamycin 1%', 'manufacturer' => 'Clindac A', 'strength' => '1%'],
                ['id' => 4, 'brand_name' => 'Doxycycline 100mg', 'generic_name' => 'Doxycycline', 'composition' => 'Doxycycline 100mg', 'manufacturer' => 'Doxt-SL', 'strength' => '100mg'],
                ['id' => 5, 'brand_name' => 'Azithromycin 500mg', 'generic_name' => 'Azithromycin', 'composition' => 'Azithromycin 500mg', 'manufacturer' => 'Azithral', 'strength' => '500mg'],
                ['id' => 6, 'brand_name' => 'Metronidazole 400mg', 'generic_name' => 'Metronidazole', 'composition' => 'Metronidazole 400mg', 'manufacturer' => 'Flagyl', 'strength' => '400mg'],
                ['id' => 7, 'brand_name' => 'Sunscreen SPF 50+', 'generic_name' => 'Sunscreen', 'composition' => 'Broad spectrum UVA+UVB', 'manufacturer' => 'Various', 'strength' => 'SPF 50+'],
                ['id' => 8, 'brand_name' => 'Betamethasone 0.05%', 'generic_name' => 'Betamethasone', 'composition' => 'Betamethasone 0.05%', 'manufacturer' => 'Betnovate', 'strength' => '0.05%'],
                ['id' => 9, 'brand_name' => 'Calcipotriol 0.005%', 'generic_name' => 'Calcipotriol', 'composition' => 'Calcipotriol 0.005%', 'manufacturer' => 'Daivonex', 'strength' => '0.005%'],
                ['id' => 10, 'brand_name' => 'Tacrolimus 0.1%', 'generic_name' => 'Tacrolimus', 'composition' => 'Tacrolimus 0.1%', 'manufacturer' => 'Protopic', 'strength' => '0.1%'],
            ])->filter(function ($drug) use ($query) {
                return str_contains(strtolower($drug['brand_name']), strtolower($query)) ||
                       str_contains(strtolower($drug['generic_name']), strtolower($query));
            })->values();

            return response()->json($sampleDrugs);
        }
    }
}
