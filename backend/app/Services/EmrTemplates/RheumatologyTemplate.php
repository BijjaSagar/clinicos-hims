<?php

namespace App\Services\EmrTemplates;

use Illuminate\Support\Facades\Log;

class RheumatologyTemplate
{
    /**
     * Get the complete field schema for the Rheumatology specialty EMR template.
     */
    public static function getFields(): array
    {
        Log::info('Loading Rheumatology EMR template');
        Log::info('RheumatologyTemplate::getFields() - Building sections for rheumatology specialty');

        return [
            'specialty' => 'rheumatology',
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
                            'placeholder' => 'e.g., 6 weeks, 3 months',
                        ],
                        [
                            'name' => 'onset',
                            'type' => 'select',
                            'label' => 'Onset',
                            'options' => ['Acute', 'Subacute', 'Chronic', 'Insidious'],
                        ],
                    ],
                ],

                // Rheumatology History
                [
                    'id' => 'rheumatology_history',
                    'title' => 'Rheumatology History',
                    'fields' => [
                        [
                            'name' => 'morning_stiffness_duration',
                            'type' => 'select',
                            'label' => 'Morning Stiffness Duration',
                            'options' => ['<15 minutes', '15-30 minutes', '30-60 minutes', '1-2 hours', '>2 hours', 'All day'],
                        ],
                        [
                            'name' => 'joint_pain_pattern',
                            'type' => 'select',
                            'label' => 'Joint Pain Pattern',
                            'options' => ['Symmetric polyarthritis', 'Asymmetric oligoarthritis', 'Monoarthritis', 'Axial (spine/SI joints)', 'Large joint', 'Small joint', 'Migratory'],
                        ],
                        [
                            'name' => 'affected_joints',
                            'type' => 'multiselect',
                            'label' => 'Affected Joints',
                            'options' => [
                                'MCPs', 'PIPs', 'DIPs', 'Wrists', 'Elbows',
                                'Shoulders', 'Hips', 'Knees', 'Ankles', 'MTPs',
                                'Cervical spine', 'Lumbar spine', 'SI joints', 'TMJ',
                            ],
                        ],
                        [
                            'name' => 'skin_rashes',
                            'type' => 'multiselect',
                            'label' => 'Skin Rashes',
                            'options' => [
                                'Malar rash', 'Discoid rash', 'Psoriatic plaques',
                                'Gottron papules', 'Heliotrope rash', 'Skin thickening',
                                'Livedo reticularis', 'Purpura', 'Nodules', 'None',
                            ],
                        ],
                        [
                            'name' => 'raynauds',
                            'type' => 'select',
                            'label' => 'Raynaud\'s Phenomenon',
                            'options' => ['Absent', 'Primary', 'Secondary', 'With digital ulcers'],
                        ],
                        [
                            'name' => 'sicca_symptoms',
                            'type' => 'multiselect',
                            'label' => 'Sicca Symptoms',
                            'options' => ['Dry eyes', 'Dry mouth', 'Vaginal dryness', 'None'],
                        ],
                        [
                            'name' => 'photosensitivity',
                            'type' => 'boolean',
                            'label' => 'Photosensitivity',
                        ],
                        [
                            'name' => 'oral_ulcers',
                            'type' => 'boolean',
                            'label' => 'Oral Ulcers',
                        ],
                        [
                            'name' => 'family_history',
                            'type' => 'textarea',
                            'label' => 'Family History (Autoimmune Diseases)',
                        ],
                    ],
                ],

                // Joint Examination
                [
                    'id' => 'joint_examination',
                    'title' => 'Joint Examination',
                    'type' => 'joint_map',
                    'fields' => [
                        [
                            'name' => 'tender_joint_count_28',
                            'type' => 'number',
                            'label' => 'Tender Joint Count (TJC-28)',
                            'min' => 0,
                            'max' => 28,
                        ],
                        [
                            'name' => 'swollen_joint_count_28',
                            'type' => 'number',
                            'label' => 'Swollen Joint Count (SJC-28)',
                            'min' => 0,
                            'max' => 28,
                        ],
                        [
                            'name' => 'patient_global_assessment',
                            'type' => 'number',
                            'label' => 'Patient Global Assessment (VAS 0-100mm)',
                            'min' => 0,
                            'max' => 100,
                        ],
                        [
                            'name' => 'physician_global_assessment',
                            'type' => 'number',
                            'label' => 'Physician Global Assessment (VAS 0-100mm)',
                            'min' => 0,
                            'max' => 100,
                        ],
                        [
                            'name' => 'grip_strength_right',
                            'type' => 'number',
                            'label' => 'Grip Strength - Right (mmHg)',
                        ],
                        [
                            'name' => 'grip_strength_left',
                            'type' => 'number',
                            'label' => 'Grip Strength - Left (mmHg)',
                        ],
                        [
                            'name' => 'functional_class',
                            'type' => 'select',
                            'label' => 'ACR Functional Class',
                            'options' => [
                                ['value' => 'I', 'label' => 'Class I - No restriction'],
                                ['value' => 'II', 'label' => 'Class II - Moderate restriction'],
                                ['value' => 'III', 'label' => 'Class III - Marked restriction'],
                                ['value' => 'IV', 'label' => 'Class IV - Incapacitated'],
                            ],
                        ],
                    ],
                ],

                // Extra-articular Features
                [
                    'id' => 'extra_articular',
                    'title' => 'Extra-articular Features',
                    'fields' => [
                        [
                            'name' => 'skin_findings',
                            'type' => 'textarea',
                            'label' => 'Skin',
                            'placeholder' => 'Nodules, rash, sclerodactyly, calcinosis...',
                        ],
                        [
                            'name' => 'eye_findings',
                            'type' => 'textarea',
                            'label' => 'Eyes',
                            'placeholder' => 'Uveitis, episcleritis, scleritis, keratoconjunctivitis sicca...',
                        ],
                        [
                            'name' => 'lung_findings',
                            'type' => 'textarea',
                            'label' => 'Lungs',
                            'placeholder' => 'ILD, pleuritis, pulmonary hypertension, shrinking lung...',
                        ],
                        [
                            'name' => 'kidney_findings',
                            'type' => 'textarea',
                            'label' => 'Kidneys',
                            'placeholder' => 'Lupus nephritis, proteinuria, haematuria...',
                        ],
                        [
                            'name' => 'vasculitis',
                            'type' => 'textarea',
                            'label' => 'Vasculitis',
                            'placeholder' => 'Digital vasculitis, purpura, ulcers, nailfold capillaroscopy...',
                        ],
                        [
                            'name' => 'serositis',
                            'type' => 'multiselect',
                            'label' => 'Serositis',
                            'options' => ['Pleuritis', 'Pericarditis', 'Peritonitis', 'None'],
                        ],
                    ],
                ],

                // Lab Review
                [
                    'id' => 'lab_review',
                    'title' => 'Lab Review',
                    'fields' => [
                        [
                            'name' => 'esr',
                            'type' => 'number',
                            'label' => 'ESR (mm/hr)',
                        ],
                        [
                            'name' => 'crp',
                            'type' => 'number',
                            'label' => 'CRP (mg/L)',
                        ],
                        [
                            'name' => 'rf',
                            'type' => 'text',
                            'label' => 'Rheumatoid Factor (RF)',
                            'placeholder' => 'Positive/Negative, titre',
                        ],
                        [
                            'name' => 'anti_ccp',
                            'type' => 'text',
                            'label' => 'Anti-CCP',
                            'placeholder' => 'Positive/Negative, value',
                        ],
                        [
                            'name' => 'ana',
                            'type' => 'text',
                            'label' => 'ANA',
                            'placeholder' => 'Pattern, titre (e.g., Homogeneous 1:320)',
                        ],
                        [
                            'name' => 'anti_dsdna',
                            'type' => 'text',
                            'label' => 'Anti-dsDNA',
                            'placeholder' => 'Positive/Negative, value',
                        ],
                        [
                            'name' => 'ena_panel',
                            'type' => 'textarea',
                            'label' => 'ENA Panel',
                            'placeholder' => 'Anti-Sm, Anti-RNP, Anti-SSA/Ro, Anti-SSB/La, Anti-Scl70, Anti-Jo1',
                        ],
                        [
                            'name' => 'complement_c3',
                            'type' => 'text',
                            'label' => 'Complement C3',
                        ],
                        [
                            'name' => 'complement_c4',
                            'type' => 'text',
                            'label' => 'Complement C4',
                        ],
                        [
                            'name' => 'uric_acid',
                            'type' => 'number',
                            'label' => 'Serum Uric Acid (mg/dL)',
                        ],
                        [
                            'name' => 'hla_b27',
                            'type' => 'select',
                            'label' => 'HLA-B27',
                            'options' => ['Not tested', 'Positive', 'Negative'],
                        ],
                    ],
                ],

                // Disease Activity Scales
                [
                    'id' => 'scales',
                    'title' => 'Disease Activity Scales',
                    'fields' => [
                        [
                            'name' => 'das28_esr',
                            'type' => 'number',
                            'label' => 'DAS28-ESR',
                            'min' => 0,
                            'max' => 10,
                            'step' => 0.01,
                            'help' => 'Remission <2.6, Low ≤3.2, Moderate ≤5.1, High >5.1',
                        ],
                        [
                            'name' => 'das28_crp',
                            'type' => 'number',
                            'label' => 'DAS28-CRP',
                            'min' => 0,
                            'max' => 10,
                            'step' => 0.01,
                        ],
                        [
                            'name' => 'cdai',
                            'type' => 'number',
                            'label' => 'CDAI (Clinical Disease Activity Index)',
                            'min' => 0,
                            'max' => 76,
                            'help' => 'Remission ≤2.8, Low ≤10, Moderate ≤22, High >22',
                        ],
                        [
                            'name' => 'sdai',
                            'type' => 'number',
                            'label' => 'SDAI (Simplified Disease Activity Index)',
                            'min' => 0,
                            'max' => 86,
                        ],
                        [
                            'name' => 'sledai',
                            'type' => 'number',
                            'label' => 'SLEDAI-2K (SLE Disease Activity Index)',
                            'min' => 0,
                            'max' => 105,
                            'help' => 'No activity 0, Mild 1-5, Moderate 6-10, High 11-19, Very high ≥20',
                        ],
                        [
                            'name' => 'basdai',
                            'type' => 'number',
                            'label' => 'BASDAI (Bath AS Disease Activity Index)',
                            'min' => 0,
                            'max' => 10,
                            'step' => 0.1,
                            'help' => 'Active disease ≥4',
                        ],
                        [
                            'name' => 'basfi',
                            'type' => 'number',
                            'label' => 'BASFI (Bath AS Functional Index)',
                            'min' => 0,
                            'max' => 10,
                            'step' => 0.1,
                        ],
                        [
                            'name' => 'haq_di',
                            'type' => 'number',
                            'label' => 'HAQ Disability Index',
                            'min' => 0,
                            'max' => 3,
                            'step' => 0.125,
                            'help' => 'Mild 0-1, Moderate 1-2, Severe 2-3',
                        ],
                    ],
                ],

                // Imaging Review
                [
                    'id' => 'imaging_review',
                    'title' => 'Imaging Review',
                    'fields' => [
                        [
                            'name' => 'xray_findings',
                            'type' => 'textarea',
                            'label' => 'X-ray Findings',
                            'placeholder' => 'Erosions, joint space narrowing, periarticular osteoporosis, sacroiliitis grading...',
                        ],
                        [
                            'name' => 'xray_erosions',
                            'type' => 'boolean',
                            'label' => 'Erosions Present',
                        ],
                        [
                            'name' => 'mri_findings',
                            'type' => 'textarea',
                            'label' => 'MRI Findings',
                            'placeholder' => 'Synovitis, bone marrow oedema, erosions, enthesitis...',
                        ],
                        [
                            'name' => 'usg_joints',
                            'type' => 'textarea',
                            'label' => 'USG Joints',
                            'placeholder' => 'Synovial thickening, Power Doppler signal, effusion...',
                        ],
                        [
                            'name' => 'dexa_scan',
                            'type' => 'textarea',
                            'label' => 'DEXA Scan',
                            'placeholder' => 'T-score, Z-score, site...',
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
                            'autocomplete' => 'icd10_rheumatology',
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
                            'placeholder' => 'e.g., M05.79',
                        ],
                        [
                            'name' => 'disease_activity',
                            'type' => 'select',
                            'label' => 'Disease Activity',
                            'options' => ['Remission', 'Low', 'Moderate', 'High', 'Flare'],
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
                            'placeholder' => 'DMARDs, biologics, steroids, dose adjustments...',
                        ],
                        [
                            'name' => 'current_dmards',
                            'type' => 'textarea',
                            'label' => 'Current DMARDs / Biologics',
                            'placeholder' => 'Methotrexate, Hydroxychloroquine, Adalimumab, Rituximab...',
                        ],
                        [
                            'name' => 'investigations',
                            'type' => 'multiselect',
                            'label' => 'Investigations',
                            'options' => [
                                'cbc', 'esr', 'crp', 'rf', 'anti_ccp',
                                'ana', 'anti_dsdna', 'ena_panel', 'complement',
                                'uric_acid', 'hla_b27', 'urine_routine',
                                'lft', 'kft', 'xray_hands', 'xray_feet',
                                'xray_si_joints', 'mri', 'usg_joints', 'dexa',
                            ],
                        ],
                        [
                            'name' => 'referral',
                            'type' => 'text',
                            'label' => 'Referral To',
                            'placeholder' => 'Ophthalmology, nephrology, pulmonology...',
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
     * Get common rheumatology diagnoses for autocomplete (ICD-10).
     */
    public static function getCommonDiagnoses(): array
    {
        Log::info('RheumatologyTemplate::getCommonDiagnoses() - Returning common rheumatology ICD-10 codes');

        return [
            ['code' => 'M05', 'name' => 'Seropositive rheumatoid arthritis'],
            ['code' => 'M05.7', 'name' => 'Seropositive RA without organ involvement'],
            ['code' => 'M06.0', 'name' => 'Seronegative rheumatoid arthritis'],
            ['code' => 'M06.9', 'name' => 'Rheumatoid arthritis, unspecified'],
            ['code' => 'M32', 'name' => 'Systemic lupus erythematosus (SLE)'],
            ['code' => 'M32.1', 'name' => 'SLE with organ involvement'],
            ['code' => 'M45', 'name' => 'Ankylosing spondylitis'],
            ['code' => 'M45.0', 'name' => 'AS involving multiple sites'],
            ['code' => 'M10', 'name' => 'Gout'],
            ['code' => 'M10.0', 'name' => 'Idiopathic gout'],
            ['code' => 'M35.0', 'name' => 'Sjögren syndrome'],
            ['code' => 'M34', 'name' => 'Systemic sclerosis (scleroderma)'],
            ['code' => 'M34.0', 'name' => 'Progressive systemic sclerosis'],
            ['code' => 'M33', 'name' => 'Dermatomyositis / Polymyositis'],
            ['code' => 'M33.1', 'name' => 'Dermatomyositis'],
            ['code' => 'L40.5', 'name' => 'Psoriatic arthritis'],
            ['code' => 'M31.3', 'name' => 'Granulomatosis with polyangiitis (Wegener)'],
            ['code' => 'M31.1', 'name' => 'Thrombotic microangiopathy'],
            ['code' => 'M35.3', 'name' => 'Polymyalgia rheumatica'],
            ['code' => 'M15', 'name' => 'Polyosteoarthritis'],
            ['code' => 'M79.3', 'name' => 'Panniculitis'],
            ['code' => 'M35.9', 'name' => 'Systemic connective tissue disorder, unspecified'],
        ];
    }
}
