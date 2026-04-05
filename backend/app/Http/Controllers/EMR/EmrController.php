<?php

namespace App\Http\Controllers\EMR;

use App\Http\Controllers\Controller;
use App\Models\Visit;
use App\Models\VisitLesion;
use App\Models\VisitProcedure;
use App\Models\DentalTooth;
use App\Models\Patient;
use App\Services\FhirBuilder;
use App\Services\AbdmService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class EmrController extends Controller
{
    public function __construct(
        private readonly FhirBuilder $fhirBuilder,
        private readonly AbdmService $abdmService,
    ) {}

    /**
     * GET /emr/visits/{visitId}
     * Returns full structured visit note with specialty-specific fields.
     */
    public function show(int $visitId): JsonResponse
    {
        Log::info('EmrController.show: Fetching visit', ['visit_id' => $visitId]);

        $visit = Visit::with([
            'patient',
            'doctor',
            'lesions',
            'scales',
            'procedures',
            'prescriptions.drugs',
            'photos',
        ])
        ->forClinic(auth()->user()->clinic_id)
        ->findOrFail($visitId);

        Log::info('EmrController.show: Visit fetched', ['visit_id' => $visitId, 'specialty' => $visit->specialty]);

        return response()->json([
            'visit'    => $visit,
            'template' => $this->getSpecialtyTemplate($visit->specialty),
        ]);
    }

    /**
     * POST /emr/visits
     * Creates a new visit draft linked to an appointment.
     */
    public function create(Request $request): JsonResponse
    {
        Log::info('EmrController.create: Creating new visit');
        $validated = $request->validate([
            'patient_id'     => 'required|integer',
            'appointment_id' => 'required|integer',
            'specialty'      => 'required|string|in:dermatology,physiotherapy,dental,ophthalmology,orthopaedics,ent,gynaecology,cardiology,gastroenterology,nephrology,endocrinology,diabetology,pulmonology,neurology,oncology,psychiatry,rheumatology,urology,paediatrics,general_surgery,ayush,homeopathy,general_physician',
            'doctor_id'      => 'required|integer',
        ]);

        $visit = Visit::create([
            ...$validated,
            'clinic_id' => auth()->user()->clinic_id,
            'status'    => 'draft',
            'started_at'=> now(),
        ]);

        return response()->json(['visit' => $visit], 201);
    }

    /**
     * PUT /emr/visits/{visitId}
     * Auto-saves structured visit fields. Accepts specialty-specific JSON payload.
     */
    public function update(Request $request, int $visitId): JsonResponse
    {
        Log::info('EmrController.update: Updating visit', ['visit_id' => $visitId]);
        $visit = Visit::forClinic(auth()->user()->clinic_id)->findOrFail($visitId);

        // Merge incoming fields into structured_data JSON column
        $visit->structured_data = array_merge(
            $visit->structured_data ?? [],
            $request->input('fields', [])
        );
        $visit->notes       = $request->input('notes', $visit->notes);
        $visit->diagnosis   = $request->input('diagnosis', $visit->diagnosis);
        $visit->plan        = $request->input('plan', $visit->plan);
        $visit->updated_at  = now();
        $visit->save();

        return response()->json(['visit' => $visit]);
    }

    /**
     * POST /emr/visits/{visitId}/finalise
     * Marks visit as complete, generates FHIR R4 record, pushes to ABDM if consented.
     */
    public function finalise(int $visitId): JsonResponse
    {
        Log::info('EmrController.finalise: Finalising visit', ['visit_id' => $visitId]);

        $visit = Visit::with(['patient', 'prescriptions.drugs', 'procedures'])
            ->forClinic(auth()->user()->clinic_id)
            ->where('status', 'draft')
            ->findOrFail($visitId);

        $visit->status       = 'finalised';
        $visit->finalised_at = now();
        $visit->save();

        // Build FHIR R4 Composition
        $fhirBundle = $this->abdmService->buildFhirBundle($visit);
        $visit->fhir_bundle = $fhirBundle;
        $visit->save();

        $fhirPushed = false;
        if ($visit->patient->abha_id && $visit->patient->abdm_consent_active) {
            $careContext = \App\Models\AbdmCareContext::where('visit_id', $visit->id)->first();
            if ($careContext) {
                $this->abdmService->pushHealthRecord($careContext->reference_number, $fhirBundle);
                $fhirPushed = true;
            }
            Log::info('EmrController.finalise: FHIR push attempted', ['pushed' => $fhirPushed]);
        }

        Log::info('EmrController.finalise: Visit finalised', ['visit_id' => $visitId]);

        return response()->json([
            'visit'       => $visit,
            'fhir_pushed' => $fhirPushed,
        ]);
    }

    /**
     * GET /emr/templates/{specialty}
     * Returns the field schema for a specialty template.
     */
    public function template(string $specialty): JsonResponse
    {
        Log::info('EmrController.template: Fetching template', ['specialty' => $specialty]);
        $template = $this->getSpecialtyTemplate($specialty);
        return response()->json(['template' => $template]);
    }

    /**
     * POST /emr/visits/{visitId}/lesions
     * Adds a body-map lesion annotation.
     */
    public function addLesion(Request $request, int $visitId): JsonResponse
    {
        $visit = Visit::forClinic(auth()->user()->clinic_id)->findOrFail($visitId);

        $validated = $request->validate([
            'body_region'  => 'required|string',
            'x_pct'        => 'required|numeric|min:0|max:100',
            'y_pct'        => 'required|numeric|min:0|max:100',
            'lesion_type'  => 'required|string', // macule, papule, plaque, vesicle…
            'size_cm'      => 'nullable|numeric',
            'colour'       => 'nullable|string',
            'border'       => 'nullable|string',
            'surface'      => 'nullable|string',
            'notes'        => 'nullable|string',
        ]);

        $lesion = $visit->lesions()->create($validated);

        return response()->json(['lesion' => $lesion], 201);
    }

    /**
     * POST /emr/visits/{visitId}/scales
     * Saves grading scale scores (PASI, IGA, DLQI, ROM, MMT, VAS, etc.)
     */
    public function saveScales(Request $request, int $visitId): JsonResponse
    {
        $visit = Visit::forClinic(auth()->user()->clinic_id)->findOrFail($visitId);

        $scales = $request->input('scales', []);
        // e.g. [['scale' => 'PASI', 'score' => 8.4, 'components' => {...}], ...]

        foreach ($scales as $scale) {
            $visit->scales()->updateOrCreate(
                ['scale_name' => $scale['scale']],
                ['score' => $scale['score'], 'components' => $scale['components'] ?? null]
            );
        }

        return response()->json(['message' => 'Scales saved', 'count' => count($scales)]);
    }

    /**
     * PUT /emr/dental/{patientId}/tooth/{toothCode}
     * Updates a single tooth record in the FDI dental chart.
     * toothCode: 11-18, 21-28, 31-38, 41-48 (permanent) | 51-55... (primary)
     */
    public function updateTooth(Request $request, int $patientId, string $toothCode): JsonResponse
    {
        Log::info('EmrController.updateTooth', ['patient_id' => $patientId, 'tooth_code' => $toothCode]);

        $validated = $request->validate([
            'status'          => 'nullable|string',
            'caries'          => 'nullable|string',
            'restoration'     => 'nullable|string',
            'mobility'        => 'nullable|integer|min:0|max:3',
            'pocketing_mm'    => 'nullable|array',
            'recession_mm'    => 'nullable|numeric',
            'bop'             => 'nullable|boolean',
            'procedure_today' => 'nullable|string',
            'notes'           => 'nullable|string',
        ]);

        $record = DentalTooth::updateOrCreate(
            ['patient_id' => $patientId, 'tooth_code' => $toothCode],
            [...$validated, 'updated_by' => auth()->id(), 'clinic_id' => auth()->user()->clinic_id]
        );

        return response()->json(['tooth' => $record]);
    }

    /**
     * DELETE /emr/visits/{visitId}/lesions/{lesionId}
     */
    public function removeLesion(int $visitId, int $lesionId): JsonResponse
    {
        Log::info('EmrController.removeLesion', ['visit_id' => $visitId, 'lesion_id' => $lesionId]);
        $visit = Visit::forClinic(auth()->user()->clinic_id)->findOrFail($visitId);
        $lesion = $visit->lesions()->findOrFail($lesionId);
        $lesion->delete();

        Log::info('EmrController.removeLesion: Lesion deleted', ['lesion_id' => $lesionId]);
        return response()->json(['message' => 'Lesion removed']);
    }

    /**
     * POST /emr/visits/{visitId}/procedures
     */
    public function addProcedure(Request $request, int $visitId): JsonResponse
    {
        Log::info('EmrController.addProcedure', ['visit_id' => $visitId]);
        $visit = Visit::forClinic(auth()->user()->clinic_id)->findOrFail($visitId);

        $validated = $request->validate([
            'procedure_name' => 'required|string|max:200',
            'procedure_code' => 'nullable|string|max:50',
            'body_site'      => 'nullable|string|max:100',
            'notes'          => 'nullable|string',
            'quantity'       => 'nullable|integer|min:1',
            'cost'           => 'nullable|numeric|min:0',
        ]);

        $procedure = $visit->procedures()->create($validated);
        Log::info('EmrController.addProcedure: Procedure added', ['procedure_id' => $procedure->id]);

        return response()->json(['procedure' => $procedure], 201);
    }

    /**
     * GET /emr/dental/{patientId}/chart
     */
    public function dentalChart(int $patientId): JsonResponse
    {
        Log::info('EmrController.dentalChart', ['patient_id' => $patientId]);
        $clinicId = auth()->user()->clinic_id;
        Patient::where('clinic_id', $clinicId)->findOrFail($patientId);

        $teeth = DentalTooth::where('patient_id', $patientId)
            ->where('clinic_id', $clinicId)
            ->get()
            ->keyBy('tooth_code');

        Log::info('EmrController.dentalChart: Chart fetched', ['patient_id' => $patientId, 'teeth_count' => $teeth->count()]);

        return response()->json(['chart' => $teeth]);
    }

    /**
     * Returns specialty template configuration (replaces EmrTemplate model).
     */
    private function getSpecialtyTemplate(string $specialty): array
    {
        $templates = [
            'dermatology' => [
                'specialty' => 'dermatology',
                'scales' => ['PASI', 'IGA', 'DLQI'],
                'has_body_map' => true,
                'has_lesion_annotations' => true,
                'procedures' => ['Chemical Peel', 'LASER', 'PRP', 'Cryotherapy', 'Biopsy'],
            ],
            'physiotherapy' => [
                'specialty' => 'physiotherapy',
                'scales' => ['ROM', 'MMT', 'VAS', 'Oswestry', 'NDI'],
                'has_body_map' => true,
                'has_hep' => true,
                'procedures' => ['Ultrasound', 'TENS', 'IFT', 'SWD', 'Traction'],
            ],
            'dental' => [
                'specialty' => 'dental',
                'has_odontogram' => true,
                'has_periodontal_chart' => true,
                'scales' => ['DMFT'],
                'procedures' => ['Filling', 'Extraction', 'RCT', 'Crown', 'Bridge', 'Scaling'],
            ],
            'ophthalmology' => [
                'specialty' => 'ophthalmology',
                'has_va_log' => true,
                'has_refraction' => true,
                'scales' => [],
                'procedures' => ['Tonometry', 'Fundoscopy', 'Slit Lamp', 'OCT'],
            ],
            'orthopaedics' => [
                'specialty' => 'orthopaedics',
                'scales' => ['ROM', 'MMT', 'VAS'],
                'has_body_map' => true,
                'procedures' => ['Joint Injection', 'Casting', 'Splinting'],
            ],
            'ent' => [
                'specialty' => 'ent',
                'scales' => [],
                'procedures' => ['Audiometry', 'Tympanometry', 'Nasal Endoscopy'],
            ],
            'gynaecology' => [
                'specialty' => 'gynaecology',
                'scales' => [],
                'procedures' => ['Pap Smear', 'Colposcopy', 'USG'],
            ],
            'cardiology' => [
                'specialty' => 'cardiology',
                'scales' => ['NYHA', 'CCS_Angina', 'TIMI', 'GRACE', 'CHA2DS2_VASc', 'HAS_BLED'],
                'has_ecg' => true,
                'has_echo' => true,
                'has_fitness_certificate' => true,
                'procedures' => ['ECG', '2D Echo', 'Stress Test/TMT', 'Holter Monitor', 'Ambulatory BP', 'Coronary Angiography'],
            ],
            'gastroenterology' => [
                'specialty' => 'gastroenterology',
                'scales' => ['Child_Pugh', 'MELD', 'Mayo_Score_UC', 'CDAI'],
                'procedures' => ['Upper GI Endoscopy', 'Colonoscopy', 'ERCP', 'Liver Biopsy', 'USG Abdomen', 'Fibroscan'],
            ],
            'nephrology' => [
                'specialty' => 'nephrology',
                'scales' => ['eGFR', 'CKD_Stage', 'KDIGO'],
                'procedures' => ['Dialysis', 'Renal Biopsy', 'AV Fistula Assessment', 'Peritoneal Dialysis'],
            ],
            'endocrinology' => [
                'specialty' => 'endocrinology',
                'scales' => ['WHO_BMI', 'Diabetes_Risk'],
                'procedures' => ['Thyroid USG', 'FNAC Thyroid', 'DEXA Scan', 'Insulin Pump Setup', 'CGM Review'],
            ],
            'diabetology' => [
                'specialty' => 'diabetology',
                'scales' => ['HbA1c_Target', 'Diabetes_Foot_Risk', 'FINDRISC'],
                'has_glucose_log' => true,
                'has_foot_exam' => true,
                'procedures' => ['Foot Screening', 'Monofilament Test', 'Fundus Photography', 'CGM Application', 'Insulin Dose Titration'],
            ],
            'pulmonology' => [
                'specialty' => 'pulmonology',
                'scales' => ['mMRC', 'CAT_COPD', 'GOLD_Stage', 'ACT_Asthma'],
                'has_spirometry' => true,
                'procedures' => ['Spirometry/PFT', 'Bronchoscopy', 'Pleural Tap', 'ABG Analysis', 'Sleep Study', '6MWT'],
            ],
            'neurology' => [
                'specialty' => 'neurology',
                'scales' => ['GCS', 'NIHSS', 'MRS', 'MMSE', 'MoCA', 'Barthel_Index'],
                'has_neuro_exam' => true,
                'procedures' => ['EEG', 'EMG/NCV', 'Lumbar Puncture', 'MRI Brain', 'Carotid Doppler'],
            ],
            'oncology' => [
                'specialty' => 'oncology',
                'scales' => ['ECOG_PS', 'Karnofsky', 'TNM_Staging'],
                'has_chemo_protocol' => true,
                'procedures' => ['Biopsy', 'FNAC', 'Bone Marrow', 'Port Placement', 'Chemotherapy', 'Immunotherapy'],
            ],
            'psychiatry' => [
                'specialty' => 'psychiatry',
                'scales' => ['PHQ9', 'GAD7', 'HAM_D', 'HAM_A', 'BPRS', 'YMRS', 'AUDIT', 'CAGE'],
                'has_mse' => true,
                'procedures' => ['Psychotherapy Session', 'CBT Session', 'ECT', 'rTMS'],
            ],
            'rheumatology' => [
                'specialty' => 'rheumatology',
                'scales' => ['DAS28', 'CDAI_RA', 'SLEDAI', 'BASDAI', 'HAQ'],
                'has_joint_map' => true,
                'procedures' => ['Joint Aspiration', 'Joint Injection', 'Synovial Biopsy', 'Nailfold Capillaroscopy'],
            ],
            'urology' => [
                'specialty' => 'urology',
                'scales' => ['IPSS', 'IIEF', 'QoL_Score'],
                'procedures' => ['Cystoscopy', 'Urodynamics', 'TRUS Biopsy', 'Uroflowmetry', 'DJ Stent', 'Lithotripsy'],
            ],
            'paediatrics' => [
                'specialty' => 'paediatrics',
                'scales' => ['Apgar', 'WHO_Growth', 'Denver_II', 'PEWS'],
                'has_growth_chart' => true,
                'has_vaccination' => true,
                'procedures' => ['Vaccination', 'Newborn Screening', 'Developmental Assessment', 'Nebulization'],
            ],
            'general_surgery' => [
                'specialty' => 'general_surgery',
                'scales' => ['ASA_Class', 'Wound_Class', 'P_POSSUM'],
                'has_op_note' => true,
                'procedures' => ['Incision & Drainage', 'Appendectomy', 'Hernia Repair', 'Cholecystectomy', 'Wound Debridement', 'Excision Biopsy'],
            ],
            'ayush' => [
                'specialty' => 'ayush',
                'scales' => [],
                'has_prakriti' => true,
                'procedures' => ['Panchakarma', 'Shirodhara', 'Basti', 'Nasya', 'Raktamokshana', 'Agnikarma'],
            ],
            'homeopathy' => [
                'specialty' => 'homeopathy',
                'scales' => [],
                'has_repertorization' => true,
                'procedures' => ['Case Taking', 'Repertorization', 'Potency Selection', 'Follow-up Assessment'],
            ],
            'general_physician' => [
                'specialty' => 'general_physician',
                'scales' => ['Framingham', 'Wells_DVT', 'Wells_PE', 'CURB65'],
                'procedures' => ['ECG', 'ABG', 'Pleural Tap', 'Ascitic Tap', 'Lumbar Puncture', 'Joint Aspiration'],
            ],
        ];

        return $templates[$specialty] ?? ['specialty' => $specialty, 'scales' => [], 'procedures' => []];
    }
}
