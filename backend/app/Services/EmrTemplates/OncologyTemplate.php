<?php

namespace App\Services\EmrTemplates;

use Illuminate\Support\Facades\Log;

class OncologyTemplate
{
    /**
     * Get the complete field schema for the Oncology specialty EMR template.
     */
    public static function getFields(): array
    {
        Log::info('Loading Oncology EMR template');
        Log::info('OncologyTemplate::getFields() - Building sections for oncology specialty');

        return [
            'specialty' => 'oncology',
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
                            'placeholder' => 'e.g., 2 weeks, 3 months',
                        ],
                        [
                            'name' => 'referral_source',
                            'type' => 'text',
                            'label' => 'Referred By',
                            'placeholder' => 'Referring physician / self',
                        ],
                    ],
                ],

                // Cancer History
                [
                    'id' => 'cancer_history',
                    'title' => 'Cancer History',
                    'fields' => [
                        [
                            'name' => 'primary_site',
                            'type' => 'text',
                            'label' => 'Primary Site',
                            'required' => true,
                            'placeholder' => 'e.g., Right breast, Left lung upper lobe',
                        ],
                        [
                            'name' => 'histology',
                            'type' => 'text',
                            'label' => 'Histology / Pathology',
                            'placeholder' => 'e.g., Invasive ductal carcinoma, Adenocarcinoma',
                        ],
                        [
                            'name' => 'stage_t',
                            'type' => 'select',
                            'label' => 'T Stage (Tumour)',
                            'options' => ['Tx', 'T0', 'Tis', 'T1', 'T1a', 'T1b', 'T1c', 'T2', 'T3', 'T4', 'T4a', 'T4b', 'T4c', 'T4d'],
                        ],
                        [
                            'name' => 'stage_n',
                            'type' => 'select',
                            'label' => 'N Stage (Nodes)',
                            'options' => ['Nx', 'N0', 'N1', 'N1a', 'N1b', 'N2', 'N2a', 'N2b', 'N3', 'N3a', 'N3b', 'N3c'],
                        ],
                        [
                            'name' => 'stage_m',
                            'type' => 'select',
                            'label' => 'M Stage (Metastasis)',
                            'options' => ['Mx', 'M0', 'M1', 'M1a', 'M1b', 'M1c'],
                        ],
                        [
                            'name' => 'overall_stage',
                            'type' => 'select',
                            'label' => 'Overall Stage',
                            'options' => ['Stage 0', 'Stage I', 'Stage IA', 'Stage IB', 'Stage II', 'Stage IIA', 'Stage IIB', 'Stage III', 'Stage IIIA', 'Stage IIIB', 'Stage IIIC', 'Stage IV', 'Stage IVA', 'Stage IVB'],
                        ],
                        [
                            'name' => 'grade',
                            'type' => 'select',
                            'label' => 'Grade',
                            'options' => ['Gx', 'G1 - Well differentiated', 'G2 - Moderately differentiated', 'G3 - Poorly differentiated', 'G4 - Undifferentiated'],
                        ],
                        [
                            'name' => 'date_of_diagnosis',
                            'type' => 'date',
                            'label' => 'Date of Diagnosis',
                        ],
                        [
                            'name' => 'biomarkers',
                            'type' => 'textarea',
                            'label' => 'Biomarkers / Molecular Profile',
                            'placeholder' => 'e.g., ER+/PR+/HER2−, EGFR mutation, PD-L1 TPS',
                        ],
                        [
                            'name' => 'prior_treatments',
                            'type' => 'textarea',
                            'label' => 'Prior Treatments',
                            'placeholder' => 'Previous surgery, chemo, radiation, immunotherapy...',
                        ],
                    ],
                ],

                // Performance Status
                [
                    'id' => 'performance_status',
                    'title' => 'Performance Status',
                    'fields' => [
                        [
                            'name' => 'ecog_ps',
                            'type' => 'select',
                            'label' => 'ECOG Performance Status',
                            'options' => [
                                ['value' => 0, 'label' => '0 - Fully active'],
                                ['value' => 1, 'label' => '1 - Restricted in strenuous activity'],
                                ['value' => 2, 'label' => '2 - Ambulatory, capable of self-care'],
                                ['value' => 3, 'label' => '3 - Limited self-care, confined to bed/chair >50%'],
                                ['value' => 4, 'label' => '4 - Completely disabled'],
                            ],
                        ],
                        [
                            'name' => 'karnofsky_score',
                            'type' => 'select',
                            'label' => 'Karnofsky Performance Score (%)',
                            'options' => [
                                ['value' => 100, 'label' => '100% - Normal, no complaints'],
                                ['value' => 90, 'label' => '90% - Able to carry on normal activity, minor symptoms'],
                                ['value' => 80, 'label' => '80% - Normal activity with effort'],
                                ['value' => 70, 'label' => '70% - Cares for self, unable to carry on normal activity'],
                                ['value' => 60, 'label' => '60% - Requires occasional assistance'],
                                ['value' => 50, 'label' => '50% - Requires considerable assistance and frequent medical care'],
                                ['value' => 40, 'label' => '40% - Disabled, requires special care'],
                                ['value' => 30, 'label' => '30% - Severely disabled, hospitalization indicated'],
                                ['value' => 20, 'label' => '20% - Very sick, active supportive treatment necessary'],
                                ['value' => 10, 'label' => '10% - Moribund'],
                                ['value' => 0, 'label' => '0% - Dead'],
                            ],
                        ],
                    ],
                ],

                // Current Assessment
                [
                    'id' => 'current_assessment',
                    'title' => 'Current Assessment',
                    'fields' => [
                        [
                            'name' => 'disease_status',
                            'type' => 'select',
                            'label' => 'Disease Status (RECIST)',
                            'options' => [
                                'CR - Complete Response',
                                'PR - Partial Response',
                                'SD - Stable Disease',
                                'PD - Progressive Disease',
                                'NE - Not Evaluable',
                            ],
                        ],
                        [
                            'name' => 'symptom_burden',
                            'type' => 'textarea',
                            'label' => 'Symptom Burden',
                            'placeholder' => 'Pain, fatigue, appetite, nausea, dyspnea...',
                        ],
                        [
                            'name' => 'weight_current',
                            'type' => 'number',
                            'label' => 'Current Weight (kg)',
                        ],
                        [
                            'name' => 'weight_trend',
                            'type' => 'select',
                            'label' => 'Weight Trend',
                            'options' => ['Stable', 'Gaining', 'Losing <5%', 'Losing 5-10%', 'Losing >10%'],
                        ],
                        [
                            'name' => 'bsa',
                            'type' => 'number',
                            'label' => 'BSA (m²)',
                            'placeholder' => 'Body Surface Area for dosing',
                        ],
                        [
                            'name' => 'lab_cbc',
                            'type' => 'textarea',
                            'label' => 'CBC Review',
                            'placeholder' => 'Hb, WBC, ANC, Platelets',
                        ],
                        [
                            'name' => 'lab_lft',
                            'type' => 'textarea',
                            'label' => 'LFT Review',
                            'placeholder' => 'Bilirubin, AST, ALT, ALP',
                        ],
                        [
                            'name' => 'lab_kft',
                            'type' => 'textarea',
                            'label' => 'KFT Review',
                            'placeholder' => 'Creatinine, BUN, eGFR',
                        ],
                        [
                            'name' => 'lab_ldh',
                            'type' => 'text',
                            'label' => 'LDH',
                        ],
                        [
                            'name' => 'tumor_markers',
                            'type' => 'textarea',
                            'label' => 'Tumour Markers',
                            'placeholder' => 'e.g., CEA, CA-125, CA 19-9, AFP, PSA, CA 15-3',
                        ],
                    ],
                ],

                // Chemotherapy Protocol
                [
                    'id' => 'chemotherapy_protocol',
                    'title' => 'Chemotherapy Protocol',
                    'fields' => [
                        [
                            'name' => 'regimen_name',
                            'type' => 'text',
                            'label' => 'Regimen Name',
                            'placeholder' => 'e.g., AC-T, FOLFOX, R-CHOP, Pembrolizumab',
                        ],
                        [
                            'name' => 'treatment_intent',
                            'type' => 'select',
                            'label' => 'Treatment Intent',
                            'options' => ['Curative', 'Neoadjuvant', 'Adjuvant', 'Palliative', 'Maintenance'],
                        ],
                        [
                            'name' => 'cycle_number',
                            'type' => 'number',
                            'label' => 'Cycle Number',
                        ],
                        [
                            'name' => 'total_planned_cycles',
                            'type' => 'number',
                            'label' => 'Total Planned Cycles',
                        ],
                        [
                            'name' => 'day_of_cycle',
                            'type' => 'number',
                            'label' => 'Day of Cycle',
                        ],
                        [
                            'name' => 'dose_modifications',
                            'type' => 'textarea',
                            'label' => 'Dose Modifications',
                            'placeholder' => 'Dose reductions, delays, omissions with reason...',
                        ],
                        [
                            'name' => 'pre_medications',
                            'type' => 'textarea',
                            'label' => 'Pre-medications',
                            'placeholder' => 'Antiemetics, steroids, antihistamines, hydration...',
                        ],
                        [
                            'name' => 'infusion_details',
                            'type' => 'textarea',
                            'label' => 'Infusion Details',
                            'placeholder' => 'Drug doses, infusion rates, duration, sequence...',
                        ],
                        [
                            'name' => 'infusion_reactions',
                            'type' => 'textarea',
                            'label' => 'Infusion Reactions',
                            'placeholder' => 'Any immediate reactions during administration',
                        ],
                    ],
                ],

                // Toxicity Assessment
                [
                    'id' => 'toxicity_assessment',
                    'title' => 'Toxicity Assessment (CTCAE)',
                    'fields' => [
                        [
                            'name' => 'nausea_grade',
                            'type' => 'select',
                            'label' => 'Nausea (CTCAE Grade)',
                            'options' => [
                                ['value' => 0, 'label' => 'Grade 0 - None'],
                                ['value' => 1, 'label' => 'Grade 1 - Loss of appetite without alteration in eating'],
                                ['value' => 2, 'label' => 'Grade 2 - Oral intake decreased without significant weight loss'],
                                ['value' => 3, 'label' => 'Grade 3 - Inadequate oral caloric/fluid intake'],
                            ],
                        ],
                        [
                            'name' => 'vomiting_grade',
                            'type' => 'select',
                            'label' => 'Vomiting (CTCAE Grade)',
                            'options' => [
                                ['value' => 0, 'label' => 'Grade 0 - None'],
                                ['value' => 1, 'label' => 'Grade 1 - 1-2 episodes/24h'],
                                ['value' => 2, 'label' => 'Grade 2 - 3-5 episodes/24h'],
                                ['value' => 3, 'label' => 'Grade 3 - ≥6 episodes/24h, IV fluids needed'],
                            ],
                        ],
                        [
                            'name' => 'mucositis_grade',
                            'type' => 'select',
                            'label' => 'Mucositis (CTCAE Grade)',
                            'options' => [
                                ['value' => 0, 'label' => 'Grade 0 - None'],
                                ['value' => 1, 'label' => 'Grade 1 - Asymptomatic or mild symptoms'],
                                ['value' => 2, 'label' => 'Grade 2 - Moderate pain, not interfering with oral intake'],
                                ['value' => 3, 'label' => 'Grade 3 - Severe pain, interfering with oral intake'],
                                ['value' => 4, 'label' => 'Grade 4 - Life-threatening, urgent intervention needed'],
                            ],
                        ],
                        [
                            'name' => 'neuropathy_grade',
                            'type' => 'select',
                            'label' => 'Peripheral Neuropathy (CTCAE Grade)',
                            'options' => [
                                ['value' => 0, 'label' => 'Grade 0 - None'],
                                ['value' => 1, 'label' => 'Grade 1 - Asymptomatic, clinical or diagnostic only'],
                                ['value' => 2, 'label' => 'Grade 2 - Moderate symptoms, limiting instrumental ADL'],
                                ['value' => 3, 'label' => 'Grade 3 - Severe symptoms, limiting self-care ADL'],
                            ],
                        ],
                        [
                            'name' => 'myelosuppression_anc',
                            'type' => 'select',
                            'label' => 'Neutropenia (ANC-based)',
                            'options' => [
                                ['value' => 0, 'label' => 'Grade 0 - ANC ≥2000/µL'],
                                ['value' => 1, 'label' => 'Grade 1 - ANC 1500-1999/µL'],
                                ['value' => 2, 'label' => 'Grade 2 - ANC 1000-1499/µL'],
                                ['value' => 3, 'label' => 'Grade 3 - ANC 500-999/µL'],
                                ['value' => 4, 'label' => 'Grade 4 - ANC <500/µL'],
                            ],
                        ],
                        [
                            'name' => 'thrombocytopenia_grade',
                            'type' => 'select',
                            'label' => 'Thrombocytopenia (CTCAE Grade)',
                            'options' => [
                                ['value' => 0, 'label' => 'Grade 0 - Platelets ≥150k'],
                                ['value' => 1, 'label' => 'Grade 1 - Platelets 75k-150k'],
                                ['value' => 2, 'label' => 'Grade 2 - Platelets 50k-75k'],
                                ['value' => 3, 'label' => 'Grade 3 - Platelets 25k-50k'],
                                ['value' => 4, 'label' => 'Grade 4 - Platelets <25k'],
                            ],
                        ],
                        [
                            'name' => 'hand_foot_syndrome',
                            'type' => 'select',
                            'label' => 'Hand-Foot Syndrome (CTCAE Grade)',
                            'options' => [
                                ['value' => 0, 'label' => 'Grade 0 - None'],
                                ['value' => 1, 'label' => 'Grade 1 - Minimal skin changes, painless'],
                                ['value' => 2, 'label' => 'Grade 2 - Painful skin changes, limiting instrumental ADL'],
                                ['value' => 3, 'label' => 'Grade 3 - Severe skin changes, limiting self-care ADL'],
                            ],
                        ],
                        [
                            'name' => 'other_toxicities',
                            'type' => 'textarea',
                            'label' => 'Other Toxicities',
                            'placeholder' => 'Diarrhoea, fatigue, alopecia, skin rash, cardiotoxicity...',
                        ],
                    ],
                ],

                // Supportive Care
                [
                    'id' => 'supportive_care',
                    'title' => 'Supportive Care',
                    'fields' => [
                        [
                            'name' => 'antiemetic_regimen',
                            'type' => 'textarea',
                            'label' => 'Antiemetic Regimen',
                            'placeholder' => 'Ondansetron, Aprepitant, Dexamethasone...',
                        ],
                        [
                            'name' => 'growth_factors',
                            'type' => 'textarea',
                            'label' => 'Growth Factors',
                            'placeholder' => 'G-CSF (Filgrastim/Pegfilgrastim), EPO...',
                        ],
                        [
                            'name' => 'pain_management',
                            'type' => 'textarea',
                            'label' => 'Pain Management',
                            'placeholder' => 'WHO ladder step, current analgesics, VAS score...',
                        ],
                        [
                            'name' => 'pain_vas_score',
                            'type' => 'select',
                            'label' => 'Pain VAS Score (0-10)',
                            'options' => [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
                        ],
                        [
                            'name' => 'nutrition_status',
                            'type' => 'textarea',
                            'label' => 'Nutrition Assessment',
                            'placeholder' => 'Dietary intake, supplements, PG-SGA score...',
                        ],
                        [
                            'name' => 'psychosocial',
                            'type' => 'textarea',
                            'label' => 'Psychosocial Support',
                            'placeholder' => 'Mood, anxiety, counselling, support group...',
                        ],
                        [
                            'name' => 'palliative_referral',
                            'type' => 'boolean',
                            'label' => 'Palliative Care Referral',
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
                            'autocomplete' => 'icd10_oncology',
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
                            'placeholder' => 'e.g., C50.9',
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
                            'placeholder' => 'Next cycle, imaging, MDT discussion...',
                        ],
                        [
                            'name' => 'investigations',
                            'type' => 'multiselect',
                            'label' => 'Investigations',
                            'options' => [
                                'cbc', 'lft', 'kft', 'ldh', 'tumor_markers',
                                'ct_scan', 'pet_ct', 'mri', 'bone_scan',
                                'echocardiography', 'pulmonary_function_test',
                                'biopsy', 'bone_marrow_biopsy',
                            ],
                        ],
                        [
                            'name' => 'mdt_discussion',
                            'type' => 'boolean',
                            'label' => 'Tumour Board / MDT Discussion',
                        ],
                        [
                            'name' => 'referral',
                            'type' => 'text',
                            'label' => 'Referral To',
                            'placeholder' => 'Radiation oncology, surgical oncology, palliative...',
                        ],
                        [
                            'name' => 'next_cycle_date',
                            'type' => 'date',
                            'label' => 'Next Cycle Date',
                        ],
                        [
                            'name' => 'followup_in_days',
                            'type' => 'select',
                            'label' => 'Follow-up',
                            'options' => [
                                ['value' => 7, 'label' => '1 Week'],
                                ['value' => 14, 'label' => '2 Weeks'],
                                ['value' => 21, 'label' => '3 Weeks'],
                                ['value' => 30, 'label' => '1 Month'],
                                ['value' => 60, 'label' => '2 Months'],
                                ['value' => 90, 'label' => '3 Months'],
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
     * Get common oncology diagnoses for autocomplete (ICD-10).
     */
    public static function getCommonDiagnoses(): array
    {
        Log::info('OncologyTemplate::getCommonDiagnoses() - Returning common oncology ICD-10 codes');

        return [
            ['code' => 'C50', 'name' => 'Malignant neoplasm of breast'],
            ['code' => 'C50.9', 'name' => 'Breast cancer, unspecified'],
            ['code' => 'C34', 'name' => 'Malignant neoplasm of bronchus and lung'],
            ['code' => 'C34.9', 'name' => 'Lung cancer, unspecified'],
            ['code' => 'C18', 'name' => 'Malignant neoplasm of colon'],
            ['code' => 'C18.9', 'name' => 'Colon cancer, unspecified'],
            ['code' => 'C16', 'name' => 'Malignant neoplasm of stomach'],
            ['code' => 'C16.9', 'name' => 'Stomach cancer, unspecified'],
            ['code' => 'C61', 'name' => 'Malignant neoplasm of prostate'],
            ['code' => 'C56', 'name' => 'Malignant neoplasm of ovary'],
            ['code' => 'C73', 'name' => 'Malignant neoplasm of thyroid gland'],
            ['code' => 'C91', 'name' => 'Lymphoid leukaemia'],
            ['code' => 'C91.0', 'name' => 'Acute lymphoblastic leukaemia (ALL)'],
            ['code' => 'C91.1', 'name' => 'Chronic lymphocytic leukaemia (CLL)'],
            ['code' => 'C81', 'name' => 'Hodgkin lymphoma'],
            ['code' => 'C83', 'name' => 'Non-follicular lymphoma (Non-Hodgkin)'],
            ['code' => 'C83.3', 'name' => 'Diffuse large B-cell lymphoma'],
            ['code' => 'C22', 'name' => 'Malignant neoplasm of liver'],
            ['code' => 'C22.0', 'name' => 'Hepatocellular carcinoma'],
            ['code' => 'C25', 'name' => 'Malignant neoplasm of pancreas'],
            ['code' => 'C20', 'name' => 'Malignant neoplasm of rectum'],
            ['code' => 'C53', 'name' => 'Malignant neoplasm of cervix uteri'],
            ['code' => 'C67', 'name' => 'Malignant neoplasm of bladder'],
            ['code' => 'C90.0', 'name' => 'Multiple myeloma'],
        ];
    }
}
