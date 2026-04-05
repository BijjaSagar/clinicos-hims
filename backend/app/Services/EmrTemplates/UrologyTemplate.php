<?php

namespace App\Services\EmrTemplates;

use Illuminate\Support\Facades\Log;

class UrologyTemplate
{
    /**
     * Get the complete field schema for the Urology specialty EMR template.
     */
    public static function getFields(): array
    {
        Log::info('Loading Urology EMR template');
        Log::info('UrologyTemplate::getFields() - Building sections for urology specialty');

        return [
            'specialty' => 'urology',
            'sections' => [
                // Chief Complaint
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
                            'placeholder' => 'e.g., 2 weeks, 6 months',
                        ],
                        [
                            'name' => 'luts_type',
                            'type' => 'select',
                            'label' => 'LUTS Type',
                            'options' => ['Storage symptoms', 'Voiding symptoms', 'Post-micturition symptoms', 'Mixed'],
                        ],
                        [
                            'name' => 'storage_symptoms',
                            'type' => 'multiselect',
                            'label' => 'Storage Symptoms',
                            'options' => [
                                'Frequency', 'Urgency', 'Urge incontinence',
                                'Nocturia', 'Stress incontinence', 'Enuresis',
                                'Dysuria', 'Suprapubic pain', 'None',
                            ],
                        ],
                        [
                            'name' => 'voiding_symptoms',
                            'type' => 'multiselect',
                            'label' => 'Voiding Symptoms',
                            'options' => [
                                'Hesitancy', 'Poor stream', 'Intermittent stream',
                                'Straining', 'Splitting of stream', 'Terminal dribbling',
                                'Incomplete emptying', 'Retention', 'None',
                            ],
                        ],
                        [
                            'name' => 'hematuria',
                            'type' => 'select',
                            'label' => 'Haematuria',
                            'options' => ['None', 'Gross - initial', 'Gross - terminal', 'Gross - total', 'Microscopic'],
                        ],
                        [
                            'name' => 'renal_colic',
                            'type' => 'select',
                            'label' => 'Renal Colic',
                            'options' => ['None', 'Left loin', 'Right loin', 'Bilateral', 'Radiating to groin'],
                        ],
                        [
                            'name' => 'sexual_dysfunction',
                            'type' => 'multiselect',
                            'label' => 'Sexual Dysfunction',
                            'options' => [
                                'Erectile dysfunction', 'Premature ejaculation',
                                'Decreased libido', 'Retrograde ejaculation',
                                'Peyronie\'s disease', 'None',
                            ],
                        ],
                    ],
                ],

                // Urology History
                [
                    'id' => 'urology_history',
                    'title' => 'Urology History',
                    'fields' => [
                        [
                            'name' => 'previous_surgeries',
                            'type' => 'textarea',
                            'label' => 'Previous Urological Surgeries',
                            'placeholder' => 'TURP, URS, PCNL, circumcision, etc.',
                        ],
                        [
                            'name' => 'catheterisation_history',
                            'type' => 'select',
                            'label' => 'Catheterisation History',
                            'options' => ['None', 'Intermittent self-catheterisation', 'Previous indwelling', 'Current indwelling', 'Suprapubic catheter'],
                        ],
                        [
                            'name' => 'stone_history',
                            'type' => 'textarea',
                            'label' => 'Stone History',
                            'placeholder' => 'Previous stones, composition, recurrence, metabolic workup...',
                        ],
                        [
                            'name' => 'psa_trend',
                            'type' => 'textarea',
                            'label' => 'PSA Trend',
                            'placeholder' => 'Previous PSA values with dates...',
                        ],
                        [
                            'name' => 'medical_history',
                            'type' => 'multiselect',
                            'label' => 'Relevant Medical History',
                            'options' => [
                                'Diabetes', 'Hypertension', 'CKD',
                                'Neurological disease', 'Anticoagulation',
                                'BPH on medication', 'Prostate cancer',
                            ],
                        ],
                    ],
                ],

                // Examination
                [
                    'id' => 'examination',
                    'title' => 'Examination',
                    'fields' => [
                        [
                            'name' => 'abdomen',
                            'type' => 'textarea',
                            'label' => 'Abdomen',
                            'placeholder' => 'Tenderness, masses, renal angle tenderness...',
                        ],
                        [
                            'name' => 'external_genitalia',
                            'type' => 'textarea',
                            'label' => 'External Genitalia',
                            'placeholder' => 'Penis, meatus, testes, epididymis, spermatic cord, scrotal skin...',
                        ],
                        [
                            'name' => 'dre_prostate_size',
                            'type' => 'select',
                            'label' => 'DRE - Prostate Size',
                            'options' => ['Not done', 'Normal (~20g)', 'Grade I (20-30g)', 'Grade II (30-50g)', 'Grade III (50-80g)', 'Grade IV (>80g)'],
                        ],
                        [
                            'name' => 'dre_consistency',
                            'type' => 'select',
                            'label' => 'DRE - Consistency',
                            'options' => ['Not done', 'Soft/benign', 'Firm', 'Hard', 'Boggy (prostatitis)', 'Mixed'],
                        ],
                        [
                            'name' => 'dre_nodules',
                            'type' => 'select',
                            'label' => 'DRE - Nodules',
                            'options' => ['Not done', 'None', 'Right lobe nodule', 'Left lobe nodule', 'Bilateral nodules', 'Diffuse induration'],
                        ],
                        [
                            'name' => 'dre_median_sulcus',
                            'type' => 'select',
                            'label' => 'DRE - Median Sulcus',
                            'options' => ['Not done', 'Preserved', 'Obliterated'],
                        ],
                        [
                            'name' => 'dre_rectal_mucosa',
                            'type' => 'select',
                            'label' => 'DRE - Rectal Mucosa',
                            'options' => ['Not done', 'Free', 'Fixed'],
                        ],
                        [
                            'name' => 'bladder_palpation',
                            'type' => 'select',
                            'label' => 'Bladder Palpation',
                            'options' => ['Not palpable', 'Distended', 'Tender'],
                        ],
                    ],
                ],

                // Uroflowmetry
                [
                    'id' => 'uroflowmetry',
                    'title' => 'Uroflowmetry',
                    'fields' => [
                        [
                            'name' => 'max_flow_rate',
                            'type' => 'number',
                            'label' => 'Maximum Flow Rate - Qmax (mL/s)',
                            'help' => 'Normal >15 mL/s, Equivocal 10-15, Obstructed <10',
                        ],
                        [
                            'name' => 'average_flow_rate',
                            'type' => 'number',
                            'label' => 'Average Flow Rate - Qave (mL/s)',
                        ],
                        [
                            'name' => 'voided_volume',
                            'type' => 'number',
                            'label' => 'Voided Volume (mL)',
                            'help' => 'Minimum 150mL for reliable result',
                        ],
                        [
                            'name' => 'voiding_time',
                            'type' => 'number',
                            'label' => 'Voiding Time (seconds)',
                        ],
                        [
                            'name' => 'flow_pattern',
                            'type' => 'select',
                            'label' => 'Flow Pattern',
                            'options' => ['Normal bell-shaped', 'Plateau (stricture)', 'Intermittent (abdominal straining)', 'Irregular'],
                        ],
                        [
                            'name' => 'pvr',
                            'type' => 'number',
                            'label' => 'Post-Void Residual - PVR (mL)',
                            'help' => 'Normal <50mL, Significant >200mL',
                        ],
                    ],
                ],

                // Investigations Review
                [
                    'id' => 'investigations_review',
                    'title' => 'Investigations Review',
                    'fields' => [
                        [
                            'name' => 'psa',
                            'type' => 'number',
                            'label' => 'PSA (ng/mL)',
                            'step' => 0.01,
                        ],
                        [
                            'name' => 'psa_density',
                            'type' => 'number',
                            'label' => 'PSA Density',
                            'step' => 0.01,
                        ],
                        [
                            'name' => 'urine_routine',
                            'type' => 'textarea',
                            'label' => 'Urine Routine & Microscopy',
                        ],
                        [
                            'name' => 'urine_culture',
                            'type' => 'textarea',
                            'label' => 'Urine Culture & Sensitivity',
                        ],
                        [
                            'name' => 'serum_creatinine',
                            'type' => 'number',
                            'label' => 'Serum Creatinine (mg/dL)',
                            'step' => 0.01,
                        ],
                        [
                            'name' => 'usg_kub',
                            'type' => 'textarea',
                            'label' => 'USG KUB',
                            'placeholder' => 'Kidney size, hydronephrosis, stones, bladder wall, prostate volume...',
                        ],
                        [
                            'name' => 'ct_kub',
                            'type' => 'textarea',
                            'label' => 'CT KUB',
                            'placeholder' => 'Stone size/location, hydronephrosis, masses...',
                        ],
                        [
                            'name' => 'cystoscopy_findings',
                            'type' => 'textarea',
                            'label' => 'Cystoscopy Findings',
                            'placeholder' => 'Urethra, prostate, bladder mucosa, ureteric orifices...',
                        ],
                        [
                            'name' => 'urodynamics',
                            'type' => 'textarea',
                            'label' => 'Urodynamic Study',
                            'placeholder' => 'Filling cystometry, pressure-flow study, compliance...',
                        ],
                    ],
                ],

                // Scales
                [
                    'id' => 'scales',
                    'title' => 'Scoring Scales',
                    'fields' => [
                        [
                            'name' => 'ipss_score',
                            'type' => 'number',
                            'label' => 'IPSS Score (0-35)',
                            'min' => 0,
                            'max' => 35,
                            'help' => 'Mild 0-7, Moderate 8-19, Severe 20-35',
                        ],
                        [
                            'name' => 'ipss_incomplete_emptying',
                            'type' => 'select',
                            'label' => 'IPSS Q1 - Incomplete Emptying',
                            'options' => [
                                ['value' => 0, 'label' => '0 - Not at all'],
                                ['value' => 1, 'label' => '1 - Less than 1 in 5 times'],
                                ['value' => 2, 'label' => '2 - Less than half the time'],
                                ['value' => 3, 'label' => '3 - About half the time'],
                                ['value' => 4, 'label' => '4 - More than half the time'],
                                ['value' => 5, 'label' => '5 - Almost always'],
                            ],
                        ],
                        [
                            'name' => 'ipss_qol',
                            'type' => 'select',
                            'label' => 'IPSS QoL Score',
                            'options' => [
                                ['value' => 0, 'label' => '0 - Delighted'],
                                ['value' => 1, 'label' => '1 - Pleased'],
                                ['value' => 2, 'label' => '2 - Mostly satisfied'],
                                ['value' => 3, 'label' => '3 - Mixed'],
                                ['value' => 4, 'label' => '4 - Mostly dissatisfied'],
                                ['value' => 5, 'label' => '5 - Unhappy'],
                                ['value' => 6, 'label' => '6 - Terrible'],
                            ],
                        ],
                        [
                            'name' => 'iief5_score',
                            'type' => 'number',
                            'label' => 'IIEF-5 Score (5-25)',
                            'min' => 5,
                            'max' => 25,
                            'help' => 'Severe ED 5-7, Moderate 8-11, Mild-Moderate 12-16, Mild 17-21, No ED 22-25',
                        ],
                    ],
                ],

                // Diagnosis
                [
                    'id' => 'diagnosis',
                    'title' => 'Diagnosis',
                    'fields' => [
                        [
                            'name' => 'provisional_diagnosis',
                            'type' => 'text',
                            'label' => 'Provisional Diagnosis',
                            'required' => true,
                            'autocomplete' => 'icd10_urology',
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
                            'placeholder' => 'e.g., N40.1',
                        ],
                    ],
                ],

                // Plan
                [
                    'id' => 'plan',
                    'title' => 'Plan',
                    'fields' => [
                        [
                            'name' => 'treatment_plan',
                            'type' => 'textarea',
                            'label' => 'Treatment Plan',
                            'placeholder' => 'Medical management, surgical options, watchful waiting...',
                        ],
                        [
                            'name' => 'surgical_plan',
                            'type' => 'textarea',
                            'label' => 'Surgical Plan',
                            'placeholder' => 'Procedure planned, pre-op workup, consent...',
                        ],
                        [
                            'name' => 'investigations',
                            'type' => 'multiselect',
                            'label' => 'Investigations',
                            'options' => [
                                'urine_routine', 'urine_culture', 'urine_cytology',
                                'psa', 'serum_creatinine', 'cbc', 'coagulation',
                                'usg_kub', 'ct_kub', 'mri_pelvis',
                                'uroflowmetry', 'urodynamics', 'cystoscopy',
                                'renal_scan_dtpa', 'mcug', 'stone_analysis',
                            ],
                        ],
                        [
                            'name' => 'referral',
                            'type' => 'text',
                            'label' => 'Referral To',
                            'placeholder' => 'Nephrology, oncology, andrology...',
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
                                ['value' => 180, 'label' => '6 Months'],
                            ],
                        ],
                        [
                            'name' => 'follow_up_notes',
                            'type' => 'textarea',
                            'label' => 'Follow-up Instructions',
                        ],
                        [
                            'name' => 'patient_education',
                            'type' => 'textarea',
                            'label' => 'Patient Education Notes',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Get common urology diagnoses for autocomplete (ICD-10).
     */
    public static function getCommonDiagnoses(): array
    {
        Log::info('UrologyTemplate::getCommonDiagnoses() - Returning common urology ICD-10 codes');

        return [
            ['code' => 'N40', 'name' => 'Benign prostatic hyperplasia (BPH)'],
            ['code' => 'N40.1', 'name' => 'BPH with lower urinary tract symptoms'],
            ['code' => 'N20', 'name' => 'Calculus of kidney'],
            ['code' => 'N20.0', 'name' => 'Renal calculus'],
            ['code' => 'N20.1', 'name' => 'Ureteric calculus'],
            ['code' => 'N21', 'name' => 'Calculus of lower urinary tract'],
            ['code' => 'N21.0', 'name' => 'Bladder calculus'],
            ['code' => 'N30', 'name' => 'Cystitis'],
            ['code' => 'N30.0', 'name' => 'Acute cystitis'],
            ['code' => 'N30.1', 'name' => 'Interstitial cystitis'],
            ['code' => 'C61', 'name' => 'Malignant neoplasm of prostate'],
            ['code' => 'C67', 'name' => 'Malignant neoplasm of bladder'],
            ['code' => 'C67.9', 'name' => 'Bladder cancer, unspecified'],
            ['code' => 'N13', 'name' => 'Obstructive uropathy / Hydronephrosis'],
            ['code' => 'N13.3', 'name' => 'Hydronephrosis with ureteric stricture'],
            ['code' => 'N43', 'name' => 'Hydrocele'],
            ['code' => 'N43.3', 'name' => 'Hydrocele, unspecified'],
            ['code' => 'N44', 'name' => 'Torsion of testis'],
            ['code' => 'N44.0', 'name' => 'Torsion of testis, unspecified'],
            ['code' => 'N47', 'name' => 'Phimosis'],
            ['code' => 'N47.1', 'name' => 'Paraphimosis'],
            ['code' => 'N52', 'name' => 'Male erectile dysfunction'],
            ['code' => 'N52.9', 'name' => 'Erectile dysfunction, unspecified'],
            ['code' => 'N41.0', 'name' => 'Acute prostatitis'],
            ['code' => 'N41.1', 'name' => 'Chronic prostatitis'],
            ['code' => 'N35', 'name' => 'Urethral stricture'],
        ];
    }
}
