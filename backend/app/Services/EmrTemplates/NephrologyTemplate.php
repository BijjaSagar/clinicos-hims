<?php

namespace App\Services\EmrTemplates;

use Illuminate\Support\Facades\Log;

class NephrologyTemplate
{
    /**
     * Get the complete field schema for the Nephrology specialty EMR template.
     */
    public static function schema(): array
    {
        return [
            'sections' => [
                'chief_complaint' => [
                    'label' => 'Chief Complaint & History',
                    'fields' => [
                        'chief_complaint' => ['type' => 'textarea', 'label' => 'Chief Complaint', 'required' => true],
                        'history_present_illness' => ['type' => 'textarea', 'label' => 'History of Present Illness'],
                        'duration' => ['type' => 'text', 'label' => 'Duration'],
                        'oliguria' => ['type' => 'boolean', 'label' => 'Oliguria'],
                        'anuria' => ['type' => 'boolean', 'label' => 'Anuria'],
                        'polyuria' => ['type' => 'boolean', 'label' => 'Polyuria'],
                        'nocturia' => ['type' => 'boolean', 'label' => 'Nocturia'],
                        'hematuria' => ['type' => 'select', 'label' => 'Hematuria', 'options' => ['None', 'Gross', 'Microscopic']],
                        'frothy_urine' => ['type' => 'boolean', 'label' => 'Frothy Urine'],
                        'dysuria' => ['type' => 'boolean', 'label' => 'Dysuria'],
                        'flank_pain' => ['type' => 'select', 'label' => 'Flank Pain', 'options' => ['None', 'Right', 'Left', 'Bilateral']],
                        'facial_puffiness' => ['type' => 'boolean', 'label' => 'Facial Puffiness'],
                        'pedal_edema' => ['type' => 'boolean', 'label' => 'Pedal Edema'],
                    ],
                ],
                'nephrology_history' => [
                    'label' => 'Nephrology History',
                    'fields' => [
                        'known_ckd' => ['type' => 'boolean', 'label' => 'Known CKD'],
                        'ckd_etiology' => ['type' => 'select', 'label' => 'CKD Etiology', 'options' => ['Diabetic nephropathy', 'Hypertensive nephrosclerosis', 'Glomerulonephritis', 'ADPKD', 'Obstructive uropathy', 'Reflux nephropathy', 'Lupus nephritis', 'IgA nephropathy', 'FSGS', 'Unknown', 'Other']],
                        'dialysis_status' => ['type' => 'select', 'label' => 'Dialysis Status', 'options' => ['Not on dialysis', 'Hemodialysis', 'Peritoneal dialysis (CAPD)', 'Peritoneal dialysis (APD)', 'Pre-dialysis']],
                        'dialysis_start_date' => ['type' => 'date', 'label' => 'Dialysis Initiation Date'],
                        'dialysis_frequency' => ['type' => 'text', 'label' => 'Dialysis Frequency'],
                        'transplant_history' => ['type' => 'select', 'label' => 'Transplant History', 'options' => ['No transplant', 'Living donor transplant', 'Deceased donor transplant', 'Listed for transplant', 'Transplant evaluation']],
                        'transplant_date' => ['type' => 'date', 'label' => 'Transplant Date'],
                        'immunosuppression_regimen' => ['type' => 'textarea', 'label' => 'Current Immunosuppression'],
                        'urine_output_24hr' => ['type' => 'number', 'label' => '24-hr Urine Output (mL)'],
                        'urine_output_status' => ['type' => 'select', 'label' => 'Urine Output Status', 'options' => ['Normal (>0.5 mL/kg/hr)', 'Oliguria (<400 mL/day)', 'Anuria (<100 mL/day)', 'Polyuria (>3000 mL/day)']],
                        'prior_kidney_biopsy' => ['type' => 'boolean', 'label' => 'Prior Kidney Biopsy'],
                        'biopsy_result' => ['type' => 'textarea', 'label' => 'Biopsy Result'],
                        'dm_history' => ['type' => 'boolean', 'label' => 'Diabetes Mellitus'],
                        'htn_history' => ['type' => 'boolean', 'label' => 'Hypertension'],
                    ],
                ],
                'examination' => [
                    'label' => 'Physical Examination',
                    'fields' => [
                        'bp_systolic' => ['type' => 'number', 'label' => 'BP Systolic (mmHg)'],
                        'bp_diastolic' => ['type' => 'number', 'label' => 'BP Diastolic (mmHg)'],
                        'weight' => ['type' => 'number', 'label' => 'Weight (kg)'],
                        'dry_weight' => ['type' => 'number', 'label' => 'Dry Weight (kg)'],
                        'edema_grading' => ['type' => 'select', 'label' => 'Edema Grading', 'options' => [
                            ['value' => 0, 'label' => 'No edema'],
                            ['value' => 1, 'label' => 'Grade 1+ (2mm pitting, immediate rebound)'],
                            ['value' => 2, 'label' => 'Grade 2+ (4mm pitting, 15s rebound)'],
                            ['value' => 3, 'label' => 'Grade 3+ (6mm pitting, 60s rebound)'],
                            ['value' => 4, 'label' => 'Grade 4+ (8mm pitting, >2min rebound)'],
                        ]],
                        'edema_location' => ['type' => 'multiselect', 'label' => 'Edema Location', 'options' => ['Pedal', 'Pretibial', 'Knee-level', 'Thigh', 'Sacral', 'Periorbital', 'Facial', 'Anasarca']],
                        'fluid_status' => ['type' => 'select', 'label' => 'Fluid Status', 'options' => ['Euvolemic', 'Hypovolemic/Dehydrated', 'Hypervolemic/Overloaded']],
                        'jvp' => ['type' => 'select', 'label' => 'JVP', 'options' => ['Normal', 'Raised', 'Not assessed']],
                        'lung_crepitations' => ['type' => 'select', 'label' => 'Lung Crepitations', 'options' => ['None', 'Bibasal', 'Up to mid-zone', 'Diffuse']],
                        'av_fistula_exam' => ['type' => 'select', 'label' => 'AV Fistula Examination', 'options' => ['Not applicable', 'Thrill present + Bruit present', 'Thrill weak', 'No thrill (thrombosed)', 'Aneurysmal dilatation', 'Signs of steal syndrome', 'Infection signs']],
                        'av_fistula_site' => ['type' => 'select', 'label' => 'AV Fistula Site', 'options' => ['Left radiocephalic', 'Right radiocephalic', 'Left brachiocephalic', 'Right brachiocephalic', 'Left brachiobasilic', 'Right brachiobasilic', 'AV Graft', 'Permcath', 'Not applicable']],
                        'pallor' => ['type' => 'select', 'label' => 'Pallor', 'options' => ['None', 'Mild', 'Moderate', 'Severe']],
                        'skin_excoriation' => ['type' => 'boolean', 'label' => 'Uremic Skin Excoriations'],
                        'asterixis' => ['type' => 'boolean', 'label' => 'Asterixis (Uremic Flap)'],
                    ],
                ],
                'lab_review' => [
                    'label' => 'Lab Review',
                    'fields' => [
                        'serum_creatinine' => ['type' => 'number', 'label' => 'Serum Creatinine (mg/dL)'],
                        'blood_urea' => ['type' => 'number', 'label' => 'Blood Urea (mg/dL)'],
                        'bun' => ['type' => 'number', 'label' => 'BUN (mg/dL)'],
                        'egfr' => ['type' => 'number', 'label' => 'eGFR (mL/min/1.73m²)', 'computed' => true],
                        'egfr_method' => ['type' => 'select', 'label' => 'eGFR Formula', 'options' => ['CKD-EPI 2021', 'MDRD', 'Cockcroft-Gault']],
                        'sodium' => ['type' => 'number', 'label' => 'Sodium (mEq/L)'],
                        'potassium' => ['type' => 'number', 'label' => 'Potassium (mEq/L)'],
                        'chloride' => ['type' => 'number', 'label' => 'Chloride (mEq/L)'],
                        'bicarbonate' => ['type' => 'number', 'label' => 'Bicarbonate (mEq/L)'],
                        'calcium' => ['type' => 'number', 'label' => 'Calcium (mg/dL)'],
                        'phosphorus' => ['type' => 'number', 'label' => 'Phosphorus (mg/dL)'],
                        'magnesium' => ['type' => 'number', 'label' => 'Magnesium (mg/dL)'],
                        'uric_acid' => ['type' => 'number', 'label' => 'Uric Acid (mg/dL)'],
                        'albumin' => ['type' => 'number', 'label' => 'Albumin (g/dL)'],
                        'hemoglobin' => ['type' => 'number', 'label' => 'Hemoglobin (g/dL)'],
                        'pth' => ['type' => 'number', 'label' => 'iPTH (pg/mL)'],
                        'vitamin_d' => ['type' => 'number', 'label' => '25-OH Vitamin D (ng/mL)'],
                        'urine_protein_creatinine_ratio' => ['type' => 'number', 'label' => 'Urine Protein-Creatinine Ratio (mg/g)'],
                        'urine_albumin_creatinine_ratio' => ['type' => 'number', 'label' => 'Urine Albumin-Creatinine Ratio (mg/g)'],
                        'twenty_four_hr_protein' => ['type' => 'number', 'label' => '24-hr Urine Protein (g/day)'],
                        'urine_routine_protein' => ['type' => 'select', 'label' => 'Urine Routine: Protein', 'options' => ['Nil', 'Trace', '1+', '2+', '3+', '4+']],
                        'urine_routine_rbc' => ['type' => 'select', 'label' => 'Urine Routine: RBC', 'options' => ['Nil', '1-5/hpf', '5-10/hpf', '10-20/hpf', '>20/hpf', 'Plenty']],
                        'urine_routine_wbc' => ['type' => 'select', 'label' => 'Urine Routine: WBC', 'options' => ['Nil', '1-5/hpf', '5-10/hpf', '10-20/hpf', '>20/hpf', 'Plenty']],
                        'urine_casts' => ['type' => 'multiselect', 'label' => 'Urine Casts', 'options' => ['None', 'Hyaline', 'Granular', 'RBC casts', 'WBC casts', 'Waxy', 'Broad/Renal failure casts']],
                        'urine_culture' => ['type' => 'textarea', 'label' => 'Urine Culture Result'],
                    ],
                ],
                'ckd_staging' => [
                    'label' => 'CKD Staging (KDIGO)',
                    'fields' => [
                        'ckd_gfr_stage' => ['type' => 'select', 'label' => 'GFR Category', 'options' => [
                            ['value' => 'G1', 'label' => 'G1 - Normal or high (≥90 mL/min)'],
                            ['value' => 'G2', 'label' => 'G2 - Mildly decreased (60-89 mL/min)'],
                            ['value' => 'G3a', 'label' => 'G3a - Mildly to moderately decreased (45-59 mL/min)'],
                            ['value' => 'G3b', 'label' => 'G3b - Moderately to severely decreased (30-44 mL/min)'],
                            ['value' => 'G4', 'label' => 'G4 - Severely decreased (15-29 mL/min)'],
                            ['value' => 'G5', 'label' => 'G5 - Kidney failure (<15 mL/min)'],
                        ]],
                        'ckd_albuminuria_stage' => ['type' => 'select', 'label' => 'Albuminuria Category', 'options' => [
                            ['value' => 'A1', 'label' => 'A1 - Normal to mildly increased (<30 mg/g)'],
                            ['value' => 'A2', 'label' => 'A2 - Moderately increased (30-300 mg/g)'],
                            ['value' => 'A3', 'label' => 'A3 - Severely increased (>300 mg/g)'],
                        ]],
                        'ckd_risk' => ['type' => 'select', 'label' => 'Risk of Progression', 'options' => ['Low risk', 'Moderately increased risk', 'High risk', 'Very high risk'], 'computed' => true],
                        'ckd_staging_notes' => ['type' => 'textarea', 'label' => 'CKD Staging Notes'],
                    ],
                ],
                'dialysis_prescription' => [
                    'label' => 'Dialysis Prescription',
                    'fields' => [
                        'dialysis_modality' => ['type' => 'select', 'label' => 'Modality', 'options' => ['Hemodialysis (HD)', 'Peritoneal Dialysis (CAPD)', 'Peritoneal Dialysis (APD)', 'CRRT (CVVHD)', 'CRRT (CVVHDF)', 'SLED', 'Not applicable']],
                        'hd_frequency' => ['type' => 'select', 'label' => 'HD Frequency', 'options' => ['2x/week', '3x/week', '4x/week', 'Daily', 'Alternate day', 'As needed']],
                        'hd_duration' => ['type' => 'select', 'label' => 'HD Duration', 'options' => ['3 hours', '3.5 hours', '4 hours', '4.5 hours', '5 hours', '6 hours (nocturnal)', '8 hours (nocturnal)']],
                        'target_dry_weight' => ['type' => 'number', 'label' => 'Target Dry Weight (kg)'],
                        'uf_goal' => ['type' => 'number', 'label' => 'UF Goal (L)'],
                        'dialyzer_type' => ['type' => 'text', 'label' => 'Dialyzer Type/Size'],
                        'blood_flow_rate' => ['type' => 'number', 'label' => 'Blood Flow Rate (mL/min)'],
                        'dialysate_flow_rate' => ['type' => 'number', 'label' => 'Dialysate Flow Rate (mL/min)'],
                        'access_type' => ['type' => 'select', 'label' => 'Vascular Access', 'options' => ['AVF (Arteriovenous Fistula)', 'AVG (Arteriovenous Graft)', 'Permcath (Tunneled CVC)', 'Temporary CVC (Internal Jugular)', 'Temporary CVC (Femoral)', 'Temporary CVC (Subclavian)']],
                        'heparin_dose' => ['type' => 'text', 'label' => 'Heparin/Anticoagulation'],
                        'dialysate_calcium' => ['type' => 'select', 'label' => 'Dialysate Calcium', 'options' => ['1.25 mEq/L', '1.5 mEq/L', '1.75 mEq/L', '2.5 mEq/L', '3.0 mEq/L']],
                        'dialysate_potassium' => ['type' => 'select', 'label' => 'Dialysate Potassium', 'options' => ['1 mEq/L', '2 mEq/L', '3 mEq/L', '4 mEq/L']],
                        'kt_v' => ['type' => 'number', 'label' => 'Kt/V'],
                        'urr' => ['type' => 'number', 'label' => 'URR (%)'],
                        'pd_exchanges' => ['type' => 'text', 'label' => 'PD Exchanges (if PD)'],
                        'pd_dwell_volume' => ['type' => 'text', 'label' => 'PD Dwell Volume (mL)'],
                        'pd_dextrose_concentration' => ['type' => 'text', 'label' => 'PD Dextrose Concentration'],
                        'dialysis_notes' => ['type' => 'textarea', 'label' => 'Dialysis Notes'],
                    ],
                ],
                'diagnosis' => [
                    'label' => 'Diagnosis',
                    'fields' => [
                        'provisional_diagnosis' => ['type' => 'text', 'label' => 'Provisional Diagnosis', 'required' => true, 'autocomplete' => 'icd10_nephrology'],
                        'differential_diagnosis' => ['type' => 'tags', 'label' => 'Differential Diagnosis'],
                        'icd10_code' => ['type' => 'text', 'label' => 'ICD-10 Code'],
                    ],
                ],
                'plan' => [
                    'label' => 'Plan & Follow-up',
                    'fields' => [
                        'treatment_plan' => ['type' => 'textarea', 'label' => 'Treatment Plan'],
                        'fluid_restriction' => ['type' => 'text', 'label' => 'Fluid Restriction (mL/day)'],
                        'diet_advice' => ['type' => 'textarea', 'label' => 'Renal Diet Advice (protein, K+, Na+, PO4)'],
                        'investigations' => ['type' => 'multiselect', 'label' => 'Investigations', 'options' => ['CBC', 'KFT', 'Electrolytes', 'Calcium/Phosphorus/PTH', 'Vitamin D', 'Iron studies', 'Lipid profile', 'HbA1c', 'Urine routine', 'Urine ACR', 'Urine PCR', '24hr urine protein', 'Urine culture', 'USG KUB', 'CT KUB', 'Renal Doppler', 'Kidney biopsy', 'ANA/dsDNA/C3/C4', 'ANCA', 'Serum electrophoresis', 'Urine electrophoresis']],
                        'follow_up_date' => ['type' => 'date', 'label' => 'Follow-up Date'],
                        'follow_up_notes' => ['type' => 'textarea', 'label' => 'Follow-up Instructions'],
                        'referral' => ['type' => 'text', 'label' => 'Referral To'],
                    ],
                ],
            ],
            'scales' => ['KDIGO CKD', 'KDIGO AKI', 'AKIN'],
            'procedures' => [
                'hemodialysis' => ['label' => 'Hemodialysis', 'sac_code' => '999311', 'params' => ['duration', 'uf_achieved', 'access', 'complications']],
                'peritoneal_dialysis' => ['label' => 'Peritoneal Dialysis', 'sac_code' => '999311', 'params' => ['exchanges', 'dwell_volume', 'drain_volume']],
                'kidney_biopsy' => ['label' => 'Kidney Biopsy', 'sac_code' => '999311', 'params' => ['approach', 'cores_obtained', 'complications']],
                'av_fistula_creation' => ['label' => 'AV Fistula Creation', 'sac_code' => '999311', 'params' => ['type', 'site', 'surgeon']],
                'permcath_insertion' => ['label' => 'Permcath Insertion', 'sac_code' => '999311', 'params' => ['site', 'catheter_type', 'tip_position']],
                'temporary_cvc' => ['label' => 'Temporary CVC', 'sac_code' => '999311', 'params' => ['site', 'indication']],
            ],
        ];
    }

    /**
     * Default data structure for a new nephrology visit.
     */
    public static function defaultData(): array
    {
        return [
            'chief_complaint' => '',
            'history_present_illness' => '',
            'nephrology_history' => ['dialysis_status' => 'Not on dialysis', 'transplant_history' => 'No transplant', 'urine_output' => null],
            'examination' => ['edema_grading' => 0, 'fluid_status' => 'Euvolemic', 'av_fistula' => 'Not applicable'],
            'labs' => ['creatinine' => null, 'bun' => null, 'egfr' => null, 'electrolytes' => [], 'urine_analysis' => []],
            'ckd_staging' => ['gfr_stage' => '', 'albuminuria_stage' => ''],
            'dialysis_prescription' => ['modality' => '', 'frequency' => '', 'duration' => '', 'dry_weight' => null, 'access_type' => ''],
            'diagnosis' => ['provisional' => '', 'differential' => [], 'icd10' => ''],
            'plan' => ['treatment' => '', 'diet' => '', 'follow_up_date' => '', 'follow_up_notes' => ''],
        ];
    }

    /**
     * Get nephrology EMR template fields.
     */
    public static function getFields(): array
    {
        Log::info('Loading Nephrology EMR template');
        Log::info('Nephrology template: building sections array');

        return [
            'specialty' => 'nephrology',
            'schema' => static::schema(),
            'sections' => [
                [
                    'id' => 'chief_complaint',
                    'title' => 'Chief Complaint',
                    'fields' => [
                        [
                            'name' => 'chief_complaint',
                            'type' => 'textarea',
                            'label' => 'Chief Complaint',
                            'required' => true,
                            'placeholder' => 'Main presenting complaint...',
                        ],
                        [
                            'name' => 'duration',
                            'type' => 'text',
                            'label' => 'Duration',
                            'placeholder' => 'e.g., 1 week, 6 months',
                        ],
                        [
                            'name' => 'urinary_symptoms',
                            'type' => 'multiselect',
                            'label' => 'Urinary Symptoms',
                            'options' => [
                                'oliguria', 'anuria', 'polyuria', 'nocturia',
                                'hematuria', 'frothy_urine', 'dysuria', 'frequency',
                            ],
                        ],
                        [
                            'name' => 'swelling',
                            'type' => 'multiselect',
                            'label' => 'Swelling',
                            'options' => ['pedal_edema', 'facial_puffiness', 'periorbital', 'generalized'],
                        ],
                    ],
                ],

                [
                    'id' => 'nephrology_history',
                    'title' => 'Nephrology History',
                    'fields' => [
                        [
                            'name' => 'dialysis_status',
                            'type' => 'select',
                            'label' => 'Dialysis Status',
                            'options' => ['not_on_dialysis', 'hemodialysis', 'capd', 'apd', 'pre_dialysis'],
                        ],
                        [
                            'name' => 'dialysis_start_date',
                            'type' => 'date',
                            'label' => 'Dialysis Initiation Date',
                        ],
                        [
                            'name' => 'dialysis_frequency',
                            'type' => 'text',
                            'label' => 'Dialysis Frequency',
                            'placeholder' => 'e.g., 3x/week',
                        ],
                        [
                            'name' => 'transplant_history',
                            'type' => 'select',
                            'label' => 'Transplant History',
                            'options' => ['no_transplant', 'living_donor', 'deceased_donor', 'listed', 'evaluation'],
                        ],
                        [
                            'name' => 'transplant_date',
                            'type' => 'date',
                            'label' => 'Transplant Date',
                        ],
                        [
                            'name' => 'urine_output_24hr',
                            'type' => 'number',
                            'label' => '24-hr Urine Output (mL)',
                        ],
                        [
                            'name' => 'ckd_etiology',
                            'type' => 'select',
                            'label' => 'CKD Etiology',
                            'options' => [
                                'diabetic_nephropathy', 'hypertensive_nephrosclerosis',
                                'glomerulonephritis', 'adpkd', 'obstructive_uropathy',
                                'lupus_nephritis', 'iga_nephropathy', 'fsgs', 'unknown',
                            ],
                        ],
                    ],
                ],

                [
                    'id' => 'examination',
                    'title' => 'Physical Examination',
                    'fields' => [
                        [
                            'name' => 'edema_grading',
                            'type' => 'select',
                            'label' => 'Edema Grading',
                            'options' => [
                                ['value' => 0, 'label' => 'No edema'],
                                ['value' => 1, 'label' => 'Grade 1+ (2mm, immediate rebound)'],
                                ['value' => 2, 'label' => 'Grade 2+ (4mm, 15s rebound)'],
                                ['value' => 3, 'label' => 'Grade 3+ (6mm, 60s rebound)'],
                                ['value' => 4, 'label' => 'Grade 4+ (8mm, >2min rebound)'],
                            ],
                        ],
                        [
                            'name' => 'edema_location',
                            'type' => 'multiselect',
                            'label' => 'Edema Location',
                            'options' => ['pedal', 'pretibial', 'knee_level', 'sacral', 'periorbital', 'anasarca'],
                        ],
                        [
                            'name' => 'fluid_status',
                            'type' => 'select',
                            'label' => 'Fluid Status',
                            'options' => ['euvolemic', 'hypovolemic', 'hypervolemic'],
                        ],
                        [
                            'name' => 'av_fistula_exam',
                            'type' => 'select',
                            'label' => 'AV Fistula Examination',
                            'options' => [
                                'not_applicable', 'thrill_bruit_present',
                                'thrill_weak', 'no_thrill_thrombosed',
                                'aneurysmal', 'steal_syndrome', 'infection',
                            ],
                        ],
                        [
                            'name' => 'av_fistula_site',
                            'type' => 'select',
                            'label' => 'AV Fistula Site',
                            'options' => ['left_radiocephalic', 'right_radiocephalic', 'left_brachiocephalic', 'right_brachiocephalic', 'av_graft', 'permcath', 'not_applicable'],
                        ],
                        [
                            'name' => 'bp_systolic',
                            'type' => 'number',
                            'label' => 'BP Systolic (mmHg)',
                        ],
                        [
                            'name' => 'bp_diastolic',
                            'type' => 'number',
                            'label' => 'BP Diastolic (mmHg)',
                        ],
                        [
                            'name' => 'weight',
                            'type' => 'number',
                            'label' => 'Current Weight (kg)',
                        ],
                        [
                            'name' => 'pallor',
                            'type' => 'select',
                            'label' => 'Pallor',
                            'options' => ['none', 'mild', 'moderate', 'severe'],
                        ],
                    ],
                ],

                [
                    'id' => 'lab_review',
                    'title' => 'Lab Review',
                    'fields' => [
                        [
                            'name' => 'serum_creatinine',
                            'type' => 'number',
                            'label' => 'Serum Creatinine (mg/dL)',
                        ],
                        [
                            'name' => 'bun',
                            'type' => 'number',
                            'label' => 'BUN (mg/dL)',
                        ],
                        [
                            'name' => 'egfr',
                            'type' => 'number',
                            'label' => 'eGFR (mL/min/1.73m²)',
                        ],
                        [
                            'name' => 'sodium',
                            'type' => 'number',
                            'label' => 'Sodium (mEq/L)',
                        ],
                        [
                            'name' => 'potassium',
                            'type' => 'number',
                            'label' => 'Potassium (mEq/L)',
                        ],
                        [
                            'name' => 'bicarbonate',
                            'type' => 'number',
                            'label' => 'Bicarbonate (mEq/L)',
                        ],
                        [
                            'name' => 'calcium',
                            'type' => 'number',
                            'label' => 'Calcium (mg/dL)',
                        ],
                        [
                            'name' => 'phosphorus',
                            'type' => 'number',
                            'label' => 'Phosphorus (mg/dL)',
                        ],
                        [
                            'name' => 'urine_pcr',
                            'type' => 'number',
                            'label' => 'Urine Protein-Creatinine Ratio (mg/g)',
                        ],
                        [
                            'name' => 'twenty_four_hr_protein',
                            'type' => 'number',
                            'label' => '24-hr Urine Protein (g/day)',
                        ],
                        [
                            'name' => 'urine_routine',
                            'type' => 'textarea',
                            'label' => 'Urine Routine/Microscopy',
                            'placeholder' => 'Protein, RBC, WBC, casts...',
                        ],
                        [
                            'name' => 'hemoglobin',
                            'type' => 'number',
                            'label' => 'Hemoglobin (g/dL)',
                        ],
                        [
                            'name' => 'pth',
                            'type' => 'number',
                            'label' => 'iPTH (pg/mL)',
                        ],
                    ],
                ],

                [
                    'id' => 'ckd_staging',
                    'title' => 'CKD Staging (KDIGO)',
                    'fields' => [
                        [
                            'name' => 'ckd_gfr_stage',
                            'type' => 'select',
                            'label' => 'GFR Category',
                            'options' => [
                                ['value' => 'G1', 'label' => 'G1 - ≥90 mL/min'],
                                ['value' => 'G2', 'label' => 'G2 - 60-89 mL/min'],
                                ['value' => 'G3a', 'label' => 'G3a - 45-59 mL/min'],
                                ['value' => 'G3b', 'label' => 'G3b - 30-44 mL/min'],
                                ['value' => 'G4', 'label' => 'G4 - 15-29 mL/min'],
                                ['value' => 'G5', 'label' => 'G5 - <15 mL/min'],
                            ],
                        ],
                        [
                            'name' => 'ckd_albuminuria_stage',
                            'type' => 'select',
                            'label' => 'Albuminuria Category',
                            'options' => [
                                ['value' => 'A1', 'label' => 'A1 - <30 mg/g'],
                                ['value' => 'A2', 'label' => 'A2 - 30-300 mg/g'],
                                ['value' => 'A3', 'label' => 'A3 - >300 mg/g'],
                            ],
                        ],
                        [
                            'name' => 'ckd_combined_stage',
                            'type' => 'text',
                            'label' => 'Combined Stage (e.g., G3b A2)',
                        ],
                    ],
                ],

                [
                    'id' => 'dialysis_prescription',
                    'title' => 'Dialysis Prescription',
                    'fields' => [
                        [
                            'name' => 'dialysis_modality',
                            'type' => 'select',
                            'label' => 'Modality',
                            'options' => ['hemodialysis', 'capd', 'apd', 'crrt', 'sled', 'not_applicable'],
                        ],
                        [
                            'name' => 'hd_frequency',
                            'type' => 'select',
                            'label' => 'Frequency',
                            'options' => ['2x_week', '3x_week', '4x_week', 'daily', 'alternate_day'],
                        ],
                        [
                            'name' => 'hd_duration',
                            'type' => 'select',
                            'label' => 'Duration',
                            'options' => ['3_hours', '3_5_hours', '4_hours', '4_5_hours', '5_hours'],
                        ],
                        [
                            'name' => 'target_dry_weight',
                            'type' => 'number',
                            'label' => 'Target Dry Weight (kg)',
                        ],
                        [
                            'name' => 'access_type',
                            'type' => 'select',
                            'label' => 'Vascular Access',
                            'options' => ['avf', 'avg', 'permcath', 'temporary_ij', 'temporary_femoral'],
                        ],
                        [
                            'name' => 'dialysis_adequacy',
                            'type' => 'text',
                            'label' => 'Kt/V or URR',
                        ],
                        [
                            'name' => 'dialysis_notes',
                            'type' => 'textarea',
                            'label' => 'Dialysis Notes',
                        ],
                    ],
                ],

                [
                    'id' => 'diagnosis',
                    'title' => 'Diagnosis',
                    'fields' => [
                        [
                            'name' => 'provisional_diagnosis',
                            'type' => 'text',
                            'label' => 'Provisional Diagnosis',
                            'required' => true,
                            'autocomplete' => 'icd10_nephrology',
                        ],
                        [
                            'name' => 'differential_diagnosis',
                            'type' => 'tags',
                            'label' => 'Differential Diagnosis',
                        ],
                        [
                            'name' => 'icd_code',
                            'type' => 'text',
                            'label' => 'ICD-10 Code',
                            'placeholder' => 'e.g., N18.3',
                        ],
                    ],
                ],

                [
                    'id' => 'plan',
                    'title' => 'Plan',
                    'fields' => [
                        [
                            'name' => 'treatment_plan',
                            'type' => 'textarea',
                            'label' => 'Treatment Plan',
                            'placeholder' => 'Medical management, dialysis plan...',
                        ],
                        [
                            'name' => 'fluid_restriction',
                            'type' => 'text',
                            'label' => 'Fluid Restriction (mL/day)',
                        ],
                        [
                            'name' => 'renal_diet',
                            'type' => 'textarea',
                            'label' => 'Renal Diet Advice',
                            'placeholder' => 'Protein, K+, Na+, PO4 restrictions...',
                        ],
                        [
                            'name' => 'investigations',
                            'type' => 'multiselect',
                            'label' => 'Investigations',
                            'options' => [
                                'cbc', 'kft', 'electrolytes', 'calcium_phos_pth',
                                'vitamin_d', 'iron_studies', 'lipid_profile', 'hba1c',
                                'urine_routine', 'urine_acr', '24hr_urine_protein',
                                'usg_kub', 'ct_kub', 'renal_doppler', 'kidney_biopsy',
                                'ana_dsdna_c3_c4', 'anca', 'serum_electrophoresis',
                            ],
                        ],
                        [
                            'name' => 'referral',
                            'type' => 'text',
                            'label' => 'Referral To',
                        ],
                        [
                            'name' => 'followup_in_days',
                            'type' => 'select',
                            'label' => 'Follow-up',
                            'options' => [
                                ['value' => 7, 'label' => '1 Week'],
                                ['value' => 14, 'label' => '2 Weeks'],
                                ['value' => 30, 'label' => '1 Month'],
                                ['value' => 60, 'label' => '2 Months'],
                                ['value' => 90, 'label' => '3 Months'],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Calculate eGFR using CKD-EPI 2021.
     */
    public static function calculateEgfr(float $creatinine, int $age, string $sex): array
    {
        Log::info('Calculating eGFR (CKD-EPI 2021)', [
            'creatinine' => $creatinine,
            'age' => $age,
            'sex' => $sex,
        ]);

        $isFemale = strtolower($sex) === 'female' || strtolower($sex) === 'f';

        if ($isFemale) {
            $kappa = 0.7;
            $alpha = $creatinine <= 0.7 ? -0.241 : -1.2;
            $sexMultiplier = 1.012;
        } else {
            $kappa = 0.9;
            $alpha = $creatinine <= 0.9 ? -0.302 : -1.2;
            $sexMultiplier = 1.0;
        }

        $egfr = 142 * pow(min($creatinine / $kappa, 1), $alpha) *
                pow(max($creatinine / $kappa, 1), -1.2) *
                pow(0.9938, $age) *
                $sexMultiplier;

        $egfr = round($egfr, 1);

        $stage = match (true) {
            $egfr >= 90 => 'G1',
            $egfr >= 60 => 'G2',
            $egfr >= 45 => 'G3a',
            $egfr >= 30 => 'G3b',
            $egfr >= 15 => 'G4',
            default => 'G5',
        };

        Log::info('eGFR calculated', [
            'egfr' => $egfr,
            'stage' => $stage,
        ]);

        return [
            'egfr' => $egfr,
            'stage' => $stage,
            'formula' => 'CKD-EPI 2021',
        ];
    }

    /**
     * Get KDIGO CKD risk from GFR and albuminuria stages.
     */
    public static function getCkdRisk(string $gfrStage, string $albStage): string
    {
        Log::info('Getting KDIGO CKD risk', [
            'gfr_stage' => $gfrStage,
            'albuminuria_stage' => $albStage,
        ]);

        $riskMatrix = [
            'G1' => ['A1' => 'Low', 'A2' => 'Moderate', 'A3' => 'High'],
            'G2' => ['A1' => 'Low', 'A2' => 'Moderate', 'A3' => 'High'],
            'G3a' => ['A1' => 'Moderate', 'A2' => 'High', 'A3' => 'Very high'],
            'G3b' => ['A1' => 'High', 'A2' => 'Very high', 'A3' => 'Very high'],
            'G4' => ['A1' => 'Very high', 'A2' => 'Very high', 'A3' => 'Very high'],
            'G5' => ['A1' => 'Very high', 'A2' => 'Very high', 'A3' => 'Very high'],
        ];

        $risk = $riskMatrix[$gfrStage][$albStage] ?? 'Unknown';

        Log::info('CKD risk determined', ['risk' => $risk]);

        return $risk;
    }

    /**
     * Get common nephrology diagnoses for autocomplete.
     */
    public static function getCommonDiagnoses(): array
    {
        Log::info('Fetching common nephrology diagnoses list');

        return [
            ['code' => 'N18.1', 'name' => 'Chronic kidney disease, stage 1'],
            ['code' => 'N18.2', 'name' => 'Chronic kidney disease, stage 2'],
            ['code' => 'N18.3', 'name' => 'Chronic kidney disease, stage 3'],
            ['code' => 'N18.30', 'name' => 'Chronic kidney disease, stage 3 unspecified'],
            ['code' => 'N18.31', 'name' => 'Chronic kidney disease, stage 3a'],
            ['code' => 'N18.32', 'name' => 'Chronic kidney disease, stage 3b'],
            ['code' => 'N18.4', 'name' => 'Chronic kidney disease, stage 4'],
            ['code' => 'N18.5', 'name' => 'Chronic kidney disease, stage 5'],
            ['code' => 'N18.6', 'name' => 'End stage renal disease (ESRD)'],
            ['code' => 'N04', 'name' => 'Nephrotic syndrome'],
            ['code' => 'N04.0', 'name' => 'Nephrotic syndrome with minimal change'],
            ['code' => 'N04.1', 'name' => 'Nephrotic syndrome with focal segmental glomerulosclerosis (FSGS)'],
            ['code' => 'N04.2', 'name' => 'Nephrotic syndrome with membranous nephropathy'],
            ['code' => 'N03', 'name' => 'Chronic nephritic syndrome'],
            ['code' => 'N03.8', 'name' => 'IgA nephropathy'],
            ['code' => 'N17', 'name' => 'Acute kidney injury (AKI)'],
            ['code' => 'N17.0', 'name' => 'Acute kidney failure with tubular necrosis'],
            ['code' => 'N17.9', 'name' => 'Acute kidney failure, unspecified'],
            ['code' => 'N10', 'name' => 'Acute pyelonephritis'],
            ['code' => 'N11.0', 'name' => 'Chronic obstructive pyelonephritis'],
            ['code' => 'E11.22', 'name' => 'Type 2 diabetes mellitus with diabetic chronic kidney disease (DM Nephropathy)'],
            ['code' => 'E10.22', 'name' => 'Type 1 diabetes mellitus with diabetic chronic kidney disease'],
            ['code' => 'I12.9', 'name' => 'Hypertensive chronic kidney disease'],
            ['code' => 'I12.0', 'name' => 'Hypertensive CKD with stage 5 CKD or ESRD'],
            ['code' => 'N20', 'name' => 'Calculus of kidney (Kidney stones)'],
            ['code' => 'N20.0', 'name' => 'Calculus of kidney'],
            ['code' => 'N20.1', 'name' => 'Calculus of ureter'],
            ['code' => 'Q61.2', 'name' => 'Polycystic kidney, autosomal dominant (ADPKD)'],
            ['code' => 'M32.14', 'name' => 'Lupus nephritis'],
            ['code' => 'N05', 'name' => 'Unspecified nephritic syndrome'],
            ['code' => 'T86.1', 'name' => 'Kidney transplant rejection/complication'],
        ];
    }
}
