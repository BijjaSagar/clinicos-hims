<?php

namespace App\Services\EmrTemplates;

use Illuminate\Support\Facades\Log;

class GeneralPhysicianTemplate
{
    /**
     * Get General Physician / Internal Medicine EMR template fields.
     */
    public static function getFields(): array
    {
        Log::info('Loading General Physician EMR template');
        Log::info('GeneralPhysicianTemplate::getFields() - building history, examination and investigation sections');

        return [
            'specialty' => 'general_physician',
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
                            'placeholder' => 'e.g., 5 days, 2 weeks',
                        ],
                        [
                            'name' => 'onset',
                            'type' => 'select',
                            'label' => 'Onset',
                            'options' => ['sudden', 'gradual', 'insidious', 'acute_on_chronic'],
                        ],
                    ],
                ],

                // History of Present Illness
                [
                    'id' => 'hpi',
                    'title' => 'History of Present Illness',
                    'fields' => [
                        [
                            'name' => 'history_present_illness',
                            'type' => 'textarea',
                            'label' => 'Detailed History of Present Illness',
                            'required' => true,
                            'placeholder' => 'Detailed chronological account of the illness...',
                        ],
                        [
                            'name' => 'associated_symptoms',
                            'type' => 'multiselect',
                            'label' => 'Associated Symptoms',
                            'options' => [
                                'fever', 'chills', 'rigors', 'cough', 'cold', 'sore_throat',
                                'headache', 'body_ache', 'fatigue', 'weight_loss', 'weight_gain',
                                'nausea', 'vomiting', 'diarrhea', 'constipation', 'abdominal_pain',
                                'chest_pain', 'breathlessness', 'palpitations', 'dizziness',
                                'swelling', 'rash', 'joint_pain', 'burning_micturition',
                            ],
                        ],
                        [
                            'name' => 'progression',
                            'type' => 'select',
                            'label' => 'Progression',
                            'options' => ['worsening', 'improving', 'static', 'fluctuating', 'relapsing'],
                        ],
                        [
                            'name' => 'severity',
                            'type' => 'select',
                            'label' => 'Severity',
                            'options' => ['mild', 'moderate', 'severe'],
                        ],
                        [
                            'name' => 'treatment_taken',
                            'type' => 'textarea',
                            'label' => 'Treatment Taken Before Visit',
                        ],
                    ],
                ],

                // Past Medical History
                [
                    'id' => 'past_medical_history',
                    'title' => 'Past Medical History',
                    'fields' => [
                        [
                            'name' => 'diabetes',
                            'type' => 'select',
                            'label' => 'Diabetes Mellitus',
                            'options' => ['none', 'type_1', 'type_2', 'gestational', 'pre_diabetic'],
                        ],
                        [
                            'name' => 'diabetes_duration',
                            'type' => 'text',
                            'label' => 'DM Duration',
                            'placeholder' => 'e.g., 5 years',
                        ],
                        [
                            'name' => 'hypertension',
                            'type' => 'select',
                            'label' => 'Hypertension',
                            'options' => ['none', 'on_treatment', 'newly_diagnosed', 'uncontrolled'],
                        ],
                        [
                            'name' => 'hypertension_duration',
                            'type' => 'text',
                            'label' => 'HTN Duration',
                        ],
                        [
                            'name' => 'asthma_copd',
                            'type' => 'select',
                            'label' => 'Asthma / COPD',
                            'options' => ['none', 'asthma_mild', 'asthma_moderate', 'asthma_severe', 'copd'],
                        ],
                        [
                            'name' => 'tuberculosis',
                            'type' => 'select',
                            'label' => 'Tuberculosis',
                            'options' => ['none', 'pulmonary_treated', 'pulmonary_on_att', 'extrapulmonary', 'contact_history'],
                        ],
                        [
                            'name' => 'cardiac_disease',
                            'type' => 'select',
                            'label' => 'Cardiac Disease',
                            'options' => ['none', 'ihd', 'valvular', 'cardiomyopathy', 'arrhythmia', 'heart_failure', 'post_cabg', 'post_ptca'],
                        ],
                        [
                            'name' => 'thyroid',
                            'type' => 'select',
                            'label' => 'Thyroid',
                            'options' => ['none', 'hypothyroid', 'hyperthyroid', 'euthyroid_on_treatment', 'goiter'],
                        ],
                        [
                            'name' => 'liver_disease',
                            'type' => 'select',
                            'label' => 'Liver Disease',
                            'options' => ['none', 'hepatitis_b', 'hepatitis_c', 'fatty_liver', 'cirrhosis', 'alcoholic_liver_disease'],
                        ],
                        [
                            'name' => 'kidney_disease',
                            'type' => 'select',
                            'label' => 'Kidney Disease',
                            'options' => ['none', 'ckd_stage_1_2', 'ckd_stage_3', 'ckd_stage_4_5', 'on_dialysis', 'post_transplant', 'nephrotic_syndrome'],
                        ],
                        [
                            'name' => 'past_surgeries',
                            'type' => 'textarea',
                            'label' => 'Past Surgeries',
                            'placeholder' => 'List surgeries with year...',
                        ],
                        [
                            'name' => 'other_medical_history',
                            'type' => 'textarea',
                            'label' => 'Other Medical History',
                        ],
                    ],
                ],

                // Personal History
                [
                    'id' => 'personal_history',
                    'title' => 'Personal History',
                    'fields' => [
                        [
                            'name' => 'diet',
                            'type' => 'select',
                            'label' => 'Diet',
                            'options' => ['vegetarian', 'non_vegetarian', 'eggetarian', 'vegan', 'mixed'],
                        ],
                        [
                            'name' => 'sleep',
                            'type' => 'select',
                            'label' => 'Sleep',
                            'options' => ['adequate', 'inadequate', 'disturbed', 'insomnia', 'excessive'],
                        ],
                        [
                            'name' => 'bowel',
                            'type' => 'select',
                            'label' => 'Bowel Habits',
                            'options' => ['regular', 'constipation', 'diarrhea', 'alternating', 'irregular'],
                        ],
                        [
                            'name' => 'bladder',
                            'type' => 'select',
                            'label' => 'Bladder Habits',
                            'options' => ['normal', 'frequency', 'urgency', 'hesitancy', 'incontinence', 'nocturia', 'burning'],
                        ],
                        [
                            'name' => 'addictions',
                            'type' => 'multiselect',
                            'label' => 'Addictions',
                            'options' => [
                                'none', 'smoking', 'tobacco_chewing', 'alcohol',
                                'gutka_paan', 'cannabis', 'opioids', 'other',
                            ],
                        ],
                        [
                            'name' => 'addiction_details',
                            'type' => 'text',
                            'label' => 'Addiction Details',
                            'placeholder' => 'Quantity, duration, pack-years...',
                        ],
                        [
                            'name' => 'occupation',
                            'type' => 'text',
                            'label' => 'Occupation',
                        ],
                        [
                            'name' => 'exercise',
                            'type' => 'select',
                            'label' => 'Exercise / Physical Activity',
                            'options' => ['sedentary', 'mild', 'moderate', 'active', 'vigorous'],
                        ],
                    ],
                ],

                // Family History
                [
                    'id' => 'family_history',
                    'title' => 'Family History',
                    'fields' => [
                        [
                            'name' => 'family_diseases',
                            'type' => 'multiselect',
                            'label' => 'Family History of Diseases',
                            'options' => [
                                'diabetes', 'hypertension', 'ihd_cad', 'stroke', 'cancer',
                                'asthma', 'tuberculosis', 'thyroid', 'kidney_disease',
                                'epilepsy', 'psychiatric', 'autoimmune', 'hemoglobinopathy',
                            ],
                        ],
                        [
                            'name' => 'family_details',
                            'type' => 'textarea',
                            'label' => 'Family History Details',
                            'placeholder' => 'Relation, disease, age of onset...',
                        ],
                    ],
                ],

                // Drug History & Allergies
                [
                    'id' => 'drug_history',
                    'title' => 'Drug History & Allergies',
                    'fields' => [
                        [
                            'name' => 'current_medications',
                            'type' => 'textarea',
                            'label' => 'Current Medications',
                            'placeholder' => 'List all medications with doses...',
                        ],
                        [
                            'name' => 'drug_allergies',
                            'type' => 'tags',
                            'label' => 'Drug Allergies',
                            'suggestions' => ['penicillin', 'sulfa', 'nsaid', 'aspirin', 'iodine', 'latex', 'none_known'],
                        ],
                        [
                            'name' => 'allergy_type',
                            'type' => 'multiselect',
                            'label' => 'Allergy Type',
                            'options' => ['drug', 'food', 'environmental', 'insect', 'contact', 'none'],
                        ],
                        [
                            'name' => 'allergy_details',
                            'type' => 'textarea',
                            'label' => 'Allergy Details',
                        ],
                    ],
                ],

                // General Examination
                [
                    'id' => 'general_examination',
                    'title' => 'General Examination',
                    'fields' => [
                        [
                            'name' => 'vitals_bp',
                            'type' => 'text',
                            'label' => 'Blood Pressure (mmHg)',
                            'required' => true,
                            'placeholder' => 'e.g., 120/80',
                        ],
                        [
                            'name' => 'vitals_pulse',
                            'type' => 'number',
                            'label' => 'Pulse Rate (bpm)',
                            'required' => true,
                        ],
                        [
                            'name' => 'vitals_temp',
                            'type' => 'number',
                            'label' => 'Temperature (°F)',
                        ],
                        [
                            'name' => 'vitals_rr',
                            'type' => 'number',
                            'label' => 'Respiratory Rate (/min)',
                        ],
                        [
                            'name' => 'vitals_spo2',
                            'type' => 'number',
                            'label' => 'SpO2 (%)',
                        ],
                        [
                            'name' => 'vitals_weight',
                            'type' => 'number',
                            'label' => 'Weight (kg)',
                        ],
                        [
                            'name' => 'vitals_height',
                            'type' => 'number',
                            'label' => 'Height (cm)',
                        ],
                        [
                            'name' => 'vitals_bmi',
                            'type' => 'number',
                            'label' => 'BMI',
                            'calculated' => true,
                        ],
                        [
                            'name' => 'built',
                            'type' => 'select',
                            'label' => 'Built',
                            'options' => ['thin', 'average', 'obese', 'muscular'],
                        ],
                        [
                            'name' => 'nourishment',
                            'type' => 'select',
                            'label' => 'Nourishment',
                            'options' => ['well_nourished', 'moderately_nourished', 'poorly_nourished', 'malnourished'],
                        ],
                        [
                            'name' => 'pallor',
                            'type' => 'select',
                            'label' => 'Pallor',
                            'options' => ['absent', 'mild', 'moderate', 'severe'],
                        ],
                        [
                            'name' => 'icterus',
                            'type' => 'select',
                            'label' => 'Icterus',
                            'options' => ['absent', 'present_mild', 'present_moderate', 'present_deep'],
                        ],
                        [
                            'name' => 'cyanosis',
                            'type' => 'select',
                            'label' => 'Cyanosis',
                            'options' => ['absent', 'peripheral', 'central'],
                        ],
                        [
                            'name' => 'clubbing',
                            'type' => 'select',
                            'label' => 'Clubbing',
                            'options' => ['absent', 'grade_1', 'grade_2', 'grade_3', 'grade_4'],
                        ],
                        [
                            'name' => 'edema',
                            'type' => 'select',
                            'label' => 'Edema',
                            'options' => ['absent', 'pedal_pitting', 'pedal_non_pitting', 'generalized', 'sacral', 'periorbital'],
                        ],
                        [
                            'name' => 'lymphadenopathy',
                            'type' => 'select',
                            'label' => 'Lymphadenopathy',
                            'options' => ['absent', 'cervical', 'axillary', 'inguinal', 'generalized'],
                        ],
                        [
                            'name' => 'lymph_node_details',
                            'type' => 'text',
                            'label' => 'Lymph Node Details',
                            'placeholder' => 'Size, consistency, tenderness, mobility...',
                        ],
                        [
                            'name' => 'jvp',
                            'type' => 'select',
                            'label' => 'JVP (Jugular Venous Pressure)',
                            'options' => ['normal', 'raised', 'not_assessable'],
                        ],
                    ],
                ],

                // Systemic Examination
                [
                    'id' => 'systemic_examination',
                    'title' => 'Systemic Examination',
                    'fields' => [
                        [
                            'name' => 'cvs_heart_sounds',
                            'type' => 'select',
                            'label' => 'CVS - Heart Sounds',
                            'options' => ['s1_s2_normal', 's1_s2_soft', 's1_s2_loud', 's3_present', 's4_present'],
                        ],
                        [
                            'name' => 'cvs_murmur',
                            'type' => 'select',
                            'label' => 'CVS - Murmur',
                            'options' => [
                                'none', 'systolic_ejection', 'pansystolic', 'diastolic',
                                'continuous', 'early_systolic', 'late_systolic',
                            ],
                        ],
                        [
                            'name' => 'cvs_murmur_details',
                            'type' => 'text',
                            'label' => 'Murmur Details',
                            'placeholder' => 'Grade, area, radiation...',
                        ],
                        [
                            'name' => 'cvs_additional',
                            'type' => 'textarea',
                            'label' => 'CVS Additional Findings',
                        ],
                        [
                            'name' => 'rs_breath_sounds',
                            'type' => 'select',
                            'label' => 'RS - Breath Sounds',
                            'options' => ['normal_vesicular', 'decreased', 'absent', 'bronchial', 'bronchovesicular'],
                        ],
                        [
                            'name' => 'rs_added_sounds',
                            'type' => 'multiselect',
                            'label' => 'RS - Added Sounds',
                            'options' => [
                                'none', 'crackles_fine', 'crackles_coarse', 'wheeze',
                                'rhonchi', 'stridor', 'pleural_rub', 'aegophony',
                            ],
                        ],
                        [
                            'name' => 'rs_added_sounds_location',
                            'type' => 'multiselect',
                            'label' => 'RS - Added Sounds Location',
                            'options' => ['bilateral', 'right_upper', 'right_lower', 'left_upper', 'left_lower', 'bilateral_basal'],
                        ],
                        [
                            'name' => 'rs_additional',
                            'type' => 'textarea',
                            'label' => 'RS Additional Findings',
                        ],
                        [
                            'name' => 'pa_liver',
                            'type' => 'select',
                            'label' => 'PA - Liver',
                            'options' => ['not_palpable', 'palpable_soft', 'palpable_firm', 'palpable_hard', 'tender'],
                        ],
                        [
                            'name' => 'pa_liver_size',
                            'type' => 'text',
                            'label' => 'Liver Size',
                            'placeholder' => 'cm below costal margin...',
                        ],
                        [
                            'name' => 'pa_spleen',
                            'type' => 'select',
                            'label' => 'PA - Spleen',
                            'options' => ['not_palpable', 'just_palpable', 'grade_1', 'grade_2', 'grade_3', 'massive'],
                        ],
                        [
                            'name' => 'pa_tenderness',
                            'type' => 'multiselect',
                            'label' => 'PA - Tenderness',
                            'options' => [
                                'none', 'epigastric', 'right_hypochondrium', 'left_hypochondrium',
                                'umbilical', 'right_iliac_fossa', 'left_iliac_fossa',
                                'suprapubic', 'right_lumbar', 'left_lumbar', 'generalized',
                            ],
                        ],
                        [
                            'name' => 'pa_bowel_sounds',
                            'type' => 'select',
                            'label' => 'PA - Bowel Sounds',
                            'options' => ['present_normal', 'hyperactive', 'sluggish', 'absent'],
                        ],
                        [
                            'name' => 'pa_additional',
                            'type' => 'textarea',
                            'label' => 'PA Additional Findings',
                        ],
                        [
                            'name' => 'cns_consciousness',
                            'type' => 'select',
                            'label' => 'CNS - Consciousness',
                            'options' => ['alert_conscious', 'drowsy', 'stupor', 'coma', 'confused', 'delirious'],
                        ],
                        [
                            'name' => 'cns_orientation',
                            'type' => 'multiselect',
                            'label' => 'CNS - Orientation',
                            'options' => ['oriented_to_time', 'oriented_to_place', 'oriented_to_person', 'disoriented'],
                        ],
                        [
                            'name' => 'cns_gcs',
                            'type' => 'number',
                            'label' => 'GCS Score',
                            'min' => 3,
                            'max' => 15,
                        ],
                        [
                            'name' => 'cns_power_upper',
                            'type' => 'select',
                            'label' => 'CNS - Power Upper Limbs',
                            'options' => ['5_5_normal', '4_5', '3_5', '2_5', '1_5', '0_5', 'asymmetric'],
                        ],
                        [
                            'name' => 'cns_power_lower',
                            'type' => 'select',
                            'label' => 'CNS - Power Lower Limbs',
                            'options' => ['5_5_normal', '4_5', '3_5', '2_5', '1_5', '0_5', 'asymmetric'],
                        ],
                        [
                            'name' => 'cns_reflexes',
                            'type' => 'select',
                            'label' => 'CNS - Deep Tendon Reflexes',
                            'options' => ['normal', 'brisk', 'diminished', 'absent', 'asymmetric'],
                        ],
                        [
                            'name' => 'cns_plantars',
                            'type' => 'select',
                            'label' => 'CNS - Plantar Reflex',
                            'options' => ['flexor_bilateral', 'extensor_right', 'extensor_left', 'extensor_bilateral', 'equivocal'],
                        ],
                        [
                            'name' => 'cns_additional',
                            'type' => 'textarea',
                            'label' => 'CNS Additional Findings',
                        ],
                    ],
                ],

                // Investigations Ordered
                [
                    'id' => 'investigations',
                    'title' => 'Investigations Ordered',
                    'fields' => [
                        [
                            'name' => 'blood_panel',
                            'type' => 'multiselect',
                            'label' => 'Blood Panel',
                            'options' => [
                                'cbc', 'esr', 'crp', 'blood_sugar_fasting', 'blood_sugar_pp',
                                'hba1c', 'lipid_profile', 'lft', 'rft', 'serum_electrolytes',
                                'thyroid_profile', 'coagulation_profile', 'uric_acid',
                                'vitamin_d', 'vitamin_b12', 'iron_studies', 'blood_culture',
                                'dengue_ns1_igg_igm', 'malaria_parasite', 'widal',
                                'peripheral_smear', 'bnp_proBnp',
                            ],
                        ],
                        [
                            'name' => 'urine_tests',
                            'type' => 'multiselect',
                            'label' => 'Urine Tests',
                            'options' => [
                                'urine_routine', 'urine_culture', 'urine_microalbumin',
                                'urine_protein_creatinine_ratio', '24hr_urine_protein',
                            ],
                        ],
                        [
                            'name' => 'imaging',
                            'type' => 'multiselect',
                            'label' => 'Imaging',
                            'options' => [
                                'chest_xray', 'xray_other', 'usg_abdomen', 'usg_kub',
                                'ecg', '2d_echo', 'ct_scan', 'mri', 'doppler',
                                'tmt', 'pft', 'endoscopy', 'colonoscopy',
                            ],
                        ],
                        [
                            'name' => 'special_investigations',
                            'type' => 'textarea',
                            'label' => 'Special Investigations',
                            'placeholder' => 'Specify any special investigations required...',
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
                            'autocomplete' => 'icd10_general',
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
                            'placeholder' => 'e.g., J18.9',
                        ],
                    ],
                ],

                // Treatment Plan
                [
                    'id' => 'treatment_plan',
                    'title' => 'Treatment Plan',
                    'type' => 'prescription',
                    'fields' => [
                        [
                            'name' => 'medications',
                            'type' => 'textarea',
                            'label' => 'Medications',
                            'placeholder' => 'List medications with dose, frequency, duration...',
                        ],
                        [
                            'name' => 'lifestyle_advice',
                            'type' => 'textarea',
                            'label' => 'Lifestyle Advice',
                            'placeholder' => 'Diet, exercise, smoking cessation, etc...',
                        ],
                        [
                            'name' => 'referral',
                            'type' => 'multiselect',
                            'label' => 'Referral To',
                            'options' => [
                                'cardiologist', 'pulmonologist', 'gastroenterologist',
                                'nephrologist', 'endocrinologist', 'neurologist',
                                'rheumatologist', 'surgeon', 'ophthalmologist',
                                'ent', 'dermatologist', 'psychiatrist', 'physiotherapist',
                                'dietitian',
                            ],
                        ],
                        [
                            'name' => 'referral_reason',
                            'type' => 'textarea',
                            'label' => 'Referral Reason',
                        ],
                    ],
                    'common_drugs' => [
                        ['name' => 'Paracetamol 500mg', 'category' => 'Analgesic/Antipyretic', 'form' => 'tablet'],
                        ['name' => 'Ibuprofen 400mg', 'category' => 'NSAID', 'form' => 'tablet'],
                        ['name' => 'Amoxicillin 500mg', 'category' => 'Antibiotic', 'form' => 'capsule'],
                        ['name' => 'Azithromycin 500mg', 'category' => 'Antibiotic', 'form' => 'tablet'],
                        ['name' => 'Cefixime 200mg', 'category' => 'Antibiotic', 'form' => 'tablet'],
                        ['name' => 'Metformin 500mg', 'category' => 'Antidiabetic', 'form' => 'tablet'],
                        ['name' => 'Amlodipine 5mg', 'category' => 'Antihypertensive', 'form' => 'tablet'],
                        ['name' => 'Telmisartan 40mg', 'category' => 'Antihypertensive', 'form' => 'tablet'],
                        ['name' => 'Atorvastatin 10mg', 'category' => 'Statin', 'form' => 'tablet'],
                        ['name' => 'Pantoprazole 40mg', 'category' => 'PPI', 'form' => 'tablet'],
                        ['name' => 'Cetirizine 10mg', 'category' => 'Antihistamine', 'form' => 'tablet'],
                        ['name' => 'Montelukast 10mg', 'category' => 'LTRA', 'form' => 'tablet'],
                        ['name' => 'Levothyroxine 50mcg', 'category' => 'Thyroid', 'form' => 'tablet'],
                        ['name' => 'Salbutamol Inhaler', 'category' => 'Bronchodilator', 'form' => 'inhaler'],
                        ['name' => 'ORS Sachet', 'category' => 'Rehydration', 'form' => 'powder'],
                        ['name' => 'Multivitamin', 'category' => 'Supplement', 'form' => 'tablet'],
                    ],
                ],

                // Plan
                [
                    'id' => 'plan',
                    'title' => 'Plan',
                    'fields' => [
                        [
                            'name' => 'advice',
                            'type' => 'textarea',
                            'label' => 'Advice / Instructions',
                            'placeholder' => 'Patient counselling, precautions, warning signs...',
                        ],
                        [
                            'name' => 'follow_up_date',
                            'type' => 'date',
                            'label' => 'Follow-up Date',
                        ],
                        [
                            'name' => 'follow_up_interval',
                            'type' => 'select',
                            'label' => 'Follow-up',
                            'options' => [
                                ['value' => 3, 'label' => '3 Days'],
                                ['value' => 5, 'label' => '5 Days'],
                                ['value' => 7, 'label' => '1 Week'],
                                ['value' => 14, 'label' => '2 Weeks'],
                                ['value' => 30, 'label' => '1 Month'],
                                ['value' => 90, 'label' => '3 Months'],
                            ],
                        ],
                        [
                            'name' => 'follow_up_instructions',
                            'type' => 'textarea',
                            'label' => 'Follow-up Instructions',
                        ],
                        [
                            'name' => 'admission_required',
                            'type' => 'boolean',
                            'label' => 'Admission Required',
                        ],
                        [
                            'name' => 'admission_reason',
                            'type' => 'textarea',
                            'label' => 'Reason for Admission',
                        ],
                        [
                            'name' => 'prognosis',
                            'type' => 'select',
                            'label' => 'Prognosis',
                            'options' => ['good', 'fair', 'guarded', 'poor'],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Get common general physician diagnoses for autocomplete.
     */
    public static function getCommonDiagnoses(): array
    {
        Log::info('GeneralPhysicianTemplate::getCommonDiagnoses() - returning ICD-10 codes');

        return [
            ['code' => 'J06.9', 'name' => 'Acute upper respiratory infection (URTI)'],
            ['code' => 'J18.9', 'name' => 'Pneumonia, unspecified organism'],
            ['code' => 'J20.9', 'name' => 'Acute bronchitis, unspecified'],
            ['code' => 'A09', 'name' => 'Infectious gastroenteritis and colitis'],
            ['code' => 'E11.9', 'name' => 'Type 2 diabetes mellitus without complications'],
            ['code' => 'E11.65', 'name' => 'Type 2 DM with hyperglycemia'],
            ['code' => 'I10', 'name' => 'Essential (primary) hypertension'],
            ['code' => 'D50.9', 'name' => 'Iron deficiency anemia, unspecified'],
            ['code' => 'J45.9', 'name' => 'Asthma, unspecified'],
            ['code' => 'A01.0', 'name' => 'Typhoid fever'],
            ['code' => 'B50.9', 'name' => 'Plasmodium falciparum malaria, unspecified'],
            ['code' => 'B51.9', 'name' => 'Plasmodium vivax malaria'],
            ['code' => 'E05.9', 'name' => 'Thyrotoxicosis, unspecified'],
            ['code' => 'E03.9', 'name' => 'Hypothyroidism, unspecified'],
            ['code' => 'K29.7', 'name' => 'Gastritis, unspecified'],
            ['code' => 'K21.0', 'name' => 'GERD with esophagitis'],
            ['code' => 'N39.0', 'name' => 'Urinary tract infection, site not specified'],
            ['code' => 'M54.5', 'name' => 'Low back pain'],
            ['code' => 'R50.9', 'name' => 'Fever, unspecified (Fever of unknown origin)'],
            ['code' => 'A90', 'name' => 'Dengue fever'],
            ['code' => 'A91', 'name' => 'Dengue hemorrhagic fever'],
            ['code' => 'E78.5', 'name' => 'Dyslipidemia, unspecified'],
            ['code' => 'J44.1', 'name' => 'COPD with acute exacerbation'],
            ['code' => 'I25.1', 'name' => 'Atherosclerotic heart disease'],
            ['code' => 'E66.9', 'name' => 'Obesity, unspecified'],
            ['code' => 'K59.0', 'name' => 'Constipation'],
            ['code' => 'G43.9', 'name' => 'Migraine, unspecified'],
        ];
    }
}
