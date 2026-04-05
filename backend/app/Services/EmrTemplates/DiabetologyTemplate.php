<?php

namespace App\Services\EmrTemplates;

use Illuminate\Support\Facades\Log;

class DiabetologyTemplate
{
    /**
     * Get diabetology EMR template fields.
     */
    public static function getFields(): array
    {
        Log::info('Loading Diabetology EMR template');
        Log::info('DiabetologyTemplate::getFields() - building sections array');

        return [
            'specialty' => 'diabetology',
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
                            'name' => 'visit_type',
                            'type' => 'select',
                            'label' => 'Visit Type',
                            'options' => ['new_diagnosis', 'routine_follow_up', 'urgent', 'annual_review', 'complication_assessment'],
                        ],
                    ],
                ],

                // Diabetes History
                [
                    'id' => 'diabetes_history',
                    'title' => 'Diabetes History',
                    'fields' => [
                        [
                            'name' => 'diabetes_type',
                            'type' => 'select',
                            'label' => 'Diabetes Type',
                            'options' => [
                                ['value' => 'type1', 'label' => 'Type 1 Diabetes'],
                                ['value' => 'type2', 'label' => 'Type 2 Diabetes'],
                                ['value' => 'gestational', 'label' => 'Gestational Diabetes'],
                                ['value' => 'mody', 'label' => 'MODY'],
                                ['value' => 'lada', 'label' => 'LADA'],
                                ['value' => 'secondary', 'label' => 'Secondary (pancreatitis, steroid-induced)'],
                                ['value' => 'other', 'label' => 'Other specified'],
                            ],
                        ],
                        [
                            'name' => 'diabetes_duration',
                            'type' => 'text',
                            'label' => 'Duration of Diabetes',
                            'placeholder' => 'e.g., 5 years',
                        ],
                        [
                            'name' => 'age_at_diagnosis',
                            'type' => 'number',
                            'label' => 'Age at Diagnosis (years)',
                        ],
                        [
                            'name' => 'current_oha',
                            'type' => 'multiselect',
                            'label' => 'Current OHA (Oral Hypoglycaemic Agents)',
                            'options' => [
                                'metformin', 'glimepiride', 'gliclazide', 'glipizide',
                                'sitagliptin', 'vildagliptin', 'linagliptin', 'teneligliptin',
                                'empagliflozin', 'dapagliflozin', 'canagliflozin',
                                'pioglitazone', 'voglibose', 'acarbose',
                            ],
                        ],
                        [
                            'name' => 'oha_details',
                            'type' => 'textarea',
                            'label' => 'OHA Details (Doses & Timing)',
                            'placeholder' => 'e.g., Metformin 500mg BD, Glimepiride 2mg OD before breakfast...',
                        ],
                        [
                            'name' => 'on_insulin',
                            'type' => 'boolean',
                            'label' => 'On Insulin',
                        ],
                        [
                            'name' => 'insulin_regimen',
                            'type' => 'select',
                            'label' => 'Insulin Regimen',
                            'options' => [
                                'basal_only', 'basal_bolus', 'premixed_twice_daily',
                                'premixed_thrice_daily', 'pump', 'sliding_scale',
                            ],
                        ],
                        [
                            'name' => 'insulin_types',
                            'type' => 'multiselect',
                            'label' => 'Insulin Types',
                            'options' => [
                                'glargine', 'detemir', 'degludec', 'nph',
                                'aspart', 'lispro', 'glulisine', 'regular',
                                'premix_30_70', 'premix_50_50', 'premix_25_75',
                            ],
                        ],
                        [
                            'name' => 'insulin_total_daily_dose',
                            'type' => 'number',
                            'label' => 'Total Daily Insulin Dose (units)',
                            'unit' => 'units',
                        ],
                        [
                            'name' => 'insulin_dose_details',
                            'type' => 'textarea',
                            'label' => 'Insulin Dose Breakdown',
                            'placeholder' => 'e.g., Glargine 20u HS, Aspart 8-10-8 AC meals...',
                        ],
                        [
                            'name' => 'injectable_non_insulin',
                            'type' => 'multiselect',
                            'label' => 'Non-Insulin Injectables',
                            'options' => ['liraglutide', 'semaglutide', 'dulaglutide', 'exenatide'],
                        ],
                        [
                            'name' => 'hypoglycemia_frequency',
                            'type' => 'select',
                            'label' => 'Hypoglycemia Frequency',
                            'options' => [
                                'none', 'rare_less_1_month', 'monthly', 'weekly', 'daily',
                            ],
                        ],
                        [
                            'name' => 'hypoglycemia_awareness',
                            'type' => 'select',
                            'label' => 'Hypoglycemia Awareness',
                            'options' => ['aware', 'impaired', 'unaware'],
                        ],
                        [
                            'name' => 'severe_hypoglycemia_episodes',
                            'type' => 'number',
                            'label' => 'Severe Hypoglycemia Episodes (last 12 months)',
                        ],
                        [
                            'name' => 'dka_history',
                            'type' => 'select',
                            'label' => 'DKA History',
                            'options' => ['none', 'single_episode', 'recurrent'],
                        ],
                        [
                            'name' => 'hhs_history',
                            'type' => 'select',
                            'label' => 'HHS History',
                            'options' => ['none', 'single_episode', 'recurrent'],
                        ],
                    ],
                ],

                // Complications Screening
                [
                    'id' => 'complications',
                    'title' => 'Complications Screening',
                    'fields' => [
                        [
                            'name' => 'retinopathy_last_eye_exam',
                            'type' => 'date',
                            'label' => 'Last Eye Exam Date',
                        ],
                        [
                            'name' => 'retinopathy_grading',
                            'type' => 'select',
                            'label' => 'Retinopathy Grading',
                            'options' => [
                                ['value' => 'none', 'label' => 'No retinopathy'],
                                ['value' => 'mild_npdr', 'label' => 'Mild NPDR'],
                                ['value' => 'moderate_npdr', 'label' => 'Moderate NPDR'],
                                ['value' => 'severe_npdr', 'label' => 'Severe NPDR'],
                                ['value' => 'pdr', 'label' => 'Proliferative DR'],
                                ['value' => 'macular_edema', 'label' => 'Diabetic Macular Edema'],
                                ['value' => 'laser_treated', 'label' => 'Previously laser-treated'],
                            ],
                        ],
                        [
                            'name' => 'retinopathy_notes',
                            'type' => 'textarea',
                            'label' => 'Eye Exam Notes',
                        ],
                        [
                            'name' => 'nephropathy_acr',
                            'type' => 'number',
                            'label' => 'Albumin-Creatinine Ratio (mg/g)',
                            'unit' => 'mg/g',
                            'step' => 0.1,
                        ],
                        [
                            'name' => 'nephropathy_acr_category',
                            'type' => 'select',
                            'label' => 'ACR Category',
                            'options' => [
                                ['value' => 'normal', 'label' => 'Normal (<30 mg/g)'],
                                ['value' => 'microalbuminuria', 'label' => 'Microalbuminuria (30-300 mg/g)'],
                                ['value' => 'macroalbuminuria', 'label' => 'Macroalbuminuria (>300 mg/g)'],
                            ],
                        ],
                        [
                            'name' => 'nephropathy_egfr',
                            'type' => 'number',
                            'label' => 'eGFR (mL/min/1.73m²)',
                            'unit' => 'mL/min/1.73m²',
                            'step' => 0.1,
                        ],
                        [
                            'name' => 'nephropathy_ckd_stage',
                            'type' => 'select',
                            'label' => 'CKD Stage',
                            'options' => [
                                ['value' => 'g1', 'label' => 'G1 - Normal (≥90)'],
                                ['value' => 'g2', 'label' => 'G2 - Mildly decreased (60-89)'],
                                ['value' => 'g3a', 'label' => 'G3a - Mild-moderate (45-59)'],
                                ['value' => 'g3b', 'label' => 'G3b - Moderate-severe (30-44)'],
                                ['value' => 'g4', 'label' => 'G4 - Severely decreased (15-29)'],
                                ['value' => 'g5', 'label' => 'G5 - Kidney failure (<15)'],
                            ],
                        ],
                        [
                            'name' => 'neuropathy_symptoms',
                            'type' => 'multiselect',
                            'label' => 'Neuropathy Symptoms',
                            'options' => [
                                'numbness', 'tingling', 'burning', 'pins_needles',
                                'sharp_pain', 'cramps', 'allodynia', 'loss_of_sensation',
                            ],
                        ],
                        [
                            'name' => 'neuropathy_symptom_score',
                            'type' => 'number',
                            'label' => 'Neuropathy Symptom Score',
                        ],
                        [
                            'name' => 'monofilament_test',
                            'type' => 'select',
                            'label' => '10g Monofilament Test',
                            'options' => [
                                ['value' => 'normal', 'label' => 'Normal (all sites felt)'],
                                ['value' => 'reduced_right', 'label' => 'Reduced - Right foot'],
                                ['value' => 'reduced_left', 'label' => 'Reduced - Left foot'],
                                ['value' => 'reduced_bilateral', 'label' => 'Reduced - Bilateral'],
                                ['value' => 'absent', 'label' => 'Absent'],
                            ],
                        ],
                        [
                            'name' => 'vibration_sense',
                            'type' => 'select',
                            'label' => 'Vibration Sense (128Hz Tuning Fork)',
                            'options' => ['normal', 'reduced_right', 'reduced_left', 'reduced_bilateral', 'absent'],
                        ],
                        [
                            'name' => 'autonomic_neuropathy',
                            'type' => 'multiselect',
                            'label' => 'Autonomic Neuropathy Signs',
                            'options' => [
                                'postural_hypotension', 'resting_tachycardia', 'gastroparesis',
                                'erectile_dysfunction', 'bladder_dysfunction', 'gustatory_sweating',
                                'hypoglycemia_unawareness',
                            ],
                        ],
                        [
                            'name' => 'macrovascular_cad',
                            'type' => 'select',
                            'label' => 'Coronary Artery Disease',
                            'options' => ['none', 'angina', 'previous_mi', 'cabg', 'pci_stent', 'heart_failure'],
                        ],
                        [
                            'name' => 'macrovascular_cva',
                            'type' => 'select',
                            'label' => 'Cerebrovascular Disease',
                            'options' => ['none', 'tia', 'previous_stroke', 'carotid_stenosis'],
                        ],
                        [
                            'name' => 'macrovascular_pvd',
                            'type' => 'select',
                            'label' => 'Peripheral Vascular Disease',
                            'options' => ['none', 'claudication', 'rest_pain', 'abi_abnormal', 'bypass_graft', 'amputation'],
                        ],
                        [
                            'name' => 'foot_exam_pulses',
                            'type' => 'select',
                            'label' => 'Foot Pulses',
                            'options' => [
                                'all_palpable', 'dorsalis_pedis_absent_right', 'dorsalis_pedis_absent_left',
                                'posterior_tibial_absent_right', 'posterior_tibial_absent_left',
                                'absent_bilateral',
                            ],
                        ],
                        [
                            'name' => 'foot_deformities',
                            'type' => 'multiselect',
                            'label' => 'Foot Deformities',
                            'options' => [
                                'none', 'charcot_foot', 'hammer_toes', 'claw_toes',
                                'bunion', 'callus', 'flat_foot', 'high_arch',
                                'amputated_toes',
                            ],
                        ],
                        [
                            'name' => 'foot_ulcer',
                            'type' => 'boolean',
                            'label' => 'Foot Ulcer Present',
                        ],
                        [
                            'name' => 'foot_ulcer_wagner',
                            'type' => 'select',
                            'label' => 'Wagner Classification',
                            'options' => [
                                ['value' => '0', 'label' => '0 - No ulcer, high-risk foot'],
                                ['value' => '1', 'label' => '1 - Superficial ulcer'],
                                ['value' => '2', 'label' => '2 - Deep ulcer to tendon/capsule/bone'],
                                ['value' => '3', 'label' => '3 - Deep ulcer with abscess/osteomyelitis'],
                                ['value' => '4', 'label' => '4 - Localised gangrene'],
                                ['value' => '5', 'label' => '5 - Extensive gangrene'],
                            ],
                        ],
                        [
                            'name' => 'foot_exam_notes',
                            'type' => 'textarea',
                            'label' => 'Foot Examination Notes',
                        ],
                    ],
                ],

                // Glycemic Control
                [
                    'id' => 'glycemic_control',
                    'title' => 'Glycemic Control',
                    'fields' => [
                        [
                            'name' => 'hba1c_current',
                            'type' => 'number',
                            'label' => 'Current HbA1c (%)',
                            'unit' => '%',
                            'step' => 0.1,
                        ],
                        [
                            'name' => 'hba1c_date',
                            'type' => 'date',
                            'label' => 'HbA1c Date',
                        ],
                        [
                            'name' => 'hba1c_previous',
                            'type' => 'number',
                            'label' => 'Previous HbA1c (%)',
                            'unit' => '%',
                            'step' => 0.1,
                        ],
                        [
                            'name' => 'hba1c_target',
                            'type' => 'number',
                            'label' => 'HbA1c Target (%)',
                            'unit' => '%',
                            'step' => 0.1,
                        ],
                        [
                            'name' => 'hba1c_trend',
                            'type' => 'select',
                            'label' => 'HbA1c Trend',
                            'options' => ['improving', 'stable', 'worsening'],
                        ],
                        [
                            'name' => 'fpg',
                            'type' => 'number',
                            'label' => 'Fasting Plasma Glucose (mg/dL)',
                            'unit' => 'mg/dL',
                        ],
                        [
                            'name' => 'ppg',
                            'type' => 'number',
                            'label' => 'Post-Prandial Glucose (mg/dL)',
                            'unit' => 'mg/dL',
                        ],
                        [
                            'name' => 'smbg_log_reviewed',
                            'type' => 'boolean',
                            'label' => 'SMBG Log Reviewed',
                        ],
                        [
                            'name' => 'smbg_pattern',
                            'type' => 'select',
                            'label' => 'SMBG Pattern',
                            'options' => [
                                'well_controlled', 'fasting_highs', 'post_meal_highs',
                                'nocturnal_hypos', 'erratic', 'dawn_phenomenon',
                                'not_monitoring',
                            ],
                        ],
                        [
                            'name' => 'smbg_notes',
                            'type' => 'textarea',
                            'label' => 'SMBG / Glucose Log Notes',
                        ],
                        [
                            'name' => 'cgm_available',
                            'type' => 'boolean',
                            'label' => 'CGM Data Available',
                        ],
                        [
                            'name' => 'cgm_time_in_range',
                            'type' => 'number',
                            'label' => 'Time in Range (70-180 mg/dL) %',
                            'unit' => '%',
                        ],
                        [
                            'name' => 'cgm_time_below_range',
                            'type' => 'number',
                            'label' => 'Time Below Range (<70 mg/dL) %',
                            'unit' => '%',
                        ],
                        [
                            'name' => 'cgm_time_above_range',
                            'type' => 'number',
                            'label' => 'Time Above Range (>180 mg/dL) %',
                            'unit' => '%',
                        ],
                        [
                            'name' => 'cgm_gmi',
                            'type' => 'number',
                            'label' => 'Glucose Management Indicator (GMI) %',
                            'unit' => '%',
                            'step' => 0.1,
                        ],
                        [
                            'name' => 'cgm_cv',
                            'type' => 'number',
                            'label' => 'Coefficient of Variation (CV) %',
                            'unit' => '%',
                            'step' => 0.1,
                        ],
                    ],
                ],

                // Metabolic Parameters
                [
                    'id' => 'metabolic_parameters',
                    'title' => 'Metabolic Parameters',
                    'fields' => [
                        [
                            'name' => 'bp_systolic',
                            'type' => 'number',
                            'label' => 'BP Systolic (mmHg)',
                            'unit' => 'mmHg',
                        ],
                        [
                            'name' => 'bp_diastolic',
                            'type' => 'number',
                            'label' => 'BP Diastolic (mmHg)',
                            'unit' => 'mmHg',
                        ],
                        [
                            'name' => 'bp_target_met',
                            'type' => 'boolean',
                            'label' => 'BP Target Met (<130/80)',
                        ],
                        [
                            'name' => 'weight',
                            'type' => 'number',
                            'label' => 'Weight (kg)',
                            'unit' => 'kg',
                            'step' => 0.1,
                        ],
                        [
                            'name' => 'height',
                            'type' => 'number',
                            'label' => 'Height (cm)',
                            'unit' => 'cm',
                        ],
                        [
                            'name' => 'bmi',
                            'type' => 'number',
                            'label' => 'BMI (kg/m²)',
                            'unit' => 'kg/m²',
                            'step' => 0.1,
                            'computed' => true,
                        ],
                        [
                            'name' => 'bmi_category',
                            'type' => 'select',
                            'label' => 'BMI Category (Asian)',
                            'options' => [
                                ['value' => 'underweight', 'label' => 'Underweight (<18.5)'],
                                ['value' => 'normal', 'label' => 'Normal (18.5-22.9)'],
                                ['value' => 'overweight', 'label' => 'Overweight (23-24.9)'],
                                ['value' => 'obese1', 'label' => 'Obese I (25-29.9)'],
                                ['value' => 'obese2', 'label' => 'Obese II (≥30)'],
                            ],
                        ],
                        [
                            'name' => 'waist_circumference',
                            'type' => 'number',
                            'label' => 'Waist Circumference (cm)',
                            'unit' => 'cm',
                        ],
                        [
                            'name' => 'total_cholesterol',
                            'type' => 'number',
                            'label' => 'Total Cholesterol (mg/dL)',
                            'unit' => 'mg/dL',
                        ],
                        [
                            'name' => 'ldl',
                            'type' => 'number',
                            'label' => 'LDL Cholesterol (mg/dL)',
                            'unit' => 'mg/dL',
                        ],
                        [
                            'name' => 'hdl',
                            'type' => 'number',
                            'label' => 'HDL Cholesterol (mg/dL)',
                            'unit' => 'mg/dL',
                        ],
                        [
                            'name' => 'triglycerides',
                            'type' => 'number',
                            'label' => 'Triglycerides (mg/dL)',
                            'unit' => 'mg/dL',
                        ],
                        [
                            'name' => 'lipid_target_met',
                            'type' => 'boolean',
                            'label' => 'Lipid Targets Met',
                        ],
                        [
                            'name' => 'sgot',
                            'type' => 'number',
                            'label' => 'SGOT/AST (U/L)',
                            'unit' => 'U/L',
                        ],
                        [
                            'name' => 'sgpt',
                            'type' => 'number',
                            'label' => 'SGPT/ALT (U/L)',
                            'unit' => 'U/L',
                        ],
                        [
                            'name' => 'serum_creatinine',
                            'type' => 'number',
                            'label' => 'Serum Creatinine (mg/dL)',
                            'unit' => 'mg/dL',
                            'step' => 0.01,
                        ],
                        [
                            'name' => 'blood_urea',
                            'type' => 'number',
                            'label' => 'Blood Urea (mg/dL)',
                            'unit' => 'mg/dL',
                        ],
                        [
                            'name' => 'tsh',
                            'type' => 'number',
                            'label' => 'TSH (mIU/L)',
                            'unit' => 'mIU/L',
                            'step' => 0.01,
                        ],
                    ],
                ],

                // Lifestyle Assessment
                [
                    'id' => 'lifestyle',
                    'title' => 'Lifestyle Assessment',
                    'fields' => [
                        [
                            'name' => 'diet_recall',
                            'type' => 'textarea',
                            'label' => 'Diet Recall (24h)',
                            'placeholder' => 'Breakfast, lunch, dinner, snacks...',
                        ],
                        [
                            'name' => 'diet_type',
                            'type' => 'select',
                            'label' => 'Diet Type',
                            'options' => ['vegetarian', 'non_vegetarian', 'vegan', 'eggetarian'],
                        ],
                        [
                            'name' => 'carb_intake_assessment',
                            'type' => 'select',
                            'label' => 'Carbohydrate Intake',
                            'options' => ['low', 'moderate', 'high', 'very_high'],
                        ],
                        [
                            'name' => 'dietitian_referral',
                            'type' => 'boolean',
                            'label' => 'Dietitian Referral Given',
                        ],
                        [
                            'name' => 'physical_activity',
                            'type' => 'select',
                            'label' => 'Physical Activity Level',
                            'options' => [
                                'sedentary', 'light_less_150_min', 'moderate_150_min',
                                'active_more_150_min', 'very_active',
                            ],
                        ],
                        [
                            'name' => 'exercise_type',
                            'type' => 'multiselect',
                            'label' => 'Exercise Type',
                            'options' => ['walking', 'jogging', 'swimming', 'cycling', 'yoga', 'gym', 'sports', 'none'],
                        ],
                        [
                            'name' => 'exercise_minutes_per_week',
                            'type' => 'number',
                            'label' => 'Exercise Minutes per Week',
                            'unit' => 'min/week',
                        ],
                        [
                            'name' => 'sleep_hours',
                            'type' => 'number',
                            'label' => 'Sleep Duration (hours/night)',
                            'unit' => 'hours',
                        ],
                        [
                            'name' => 'sleep_quality',
                            'type' => 'select',
                            'label' => 'Sleep Quality',
                            'options' => ['good', 'fair', 'poor', 'insomnia'],
                        ],
                        [
                            'name' => 'stress_level',
                            'type' => 'select',
                            'label' => 'Stress Level',
                            'options' => ['low', 'moderate', 'high', 'very_high'],
                        ],
                        [
                            'name' => 'smoking_status',
                            'type' => 'select',
                            'label' => 'Smoking Status',
                            'options' => ['never', 'current', 'ex_smoker'],
                        ],
                        [
                            'name' => 'alcohol_use',
                            'type' => 'select',
                            'label' => 'Alcohol Use',
                            'options' => ['none', 'occasional', 'regular', 'heavy'],
                        ],
                    ],
                ],

                // Insulin Adjustment
                [
                    'id' => 'insulin_adjustment',
                    'title' => 'Insulin Adjustment',
                    'fields' => [
                        [
                            'name' => 'basal_insulin_dose',
                            'type' => 'number',
                            'label' => 'Basal Insulin Dose (units)',
                            'unit' => 'units',
                        ],
                        [
                            'name' => 'basal_insulin_adjustment',
                            'type' => 'text',
                            'label' => 'Basal Dose Adjustment',
                            'placeholder' => 'e.g., Increase by 2 units if FPG >130',
                        ],
                        [
                            'name' => 'bolus_breakfast',
                            'type' => 'number',
                            'label' => 'Bolus - Breakfast (units)',
                            'unit' => 'units',
                        ],
                        [
                            'name' => 'bolus_lunch',
                            'type' => 'number',
                            'label' => 'Bolus - Lunch (units)',
                            'unit' => 'units',
                        ],
                        [
                            'name' => 'bolus_dinner',
                            'type' => 'number',
                            'label' => 'Bolus - Dinner (units)',
                            'unit' => 'units',
                        ],
                        [
                            'name' => 'correction_factor',
                            'type' => 'text',
                            'label' => 'Correction Factor (ISF)',
                            'placeholder' => 'e.g., 1 unit drops BG by 50 mg/dL',
                        ],
                        [
                            'name' => 'icr',
                            'type' => 'text',
                            'label' => 'Insulin-to-Carb Ratio (ICR)',
                            'placeholder' => 'e.g., 1:10 (1 unit per 10g carbs)',
                        ],
                        [
                            'name' => 'insulin_adjustment_notes',
                            'type' => 'textarea',
                            'label' => 'Insulin Adjustment Notes',
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
                            'autocomplete' => 'icd10_diabetology',
                        ],
                        [
                            'name' => 'differential_diagnosis',
                            'type' => 'tags',
                            'label' => 'Differential / Comorbid Diagnoses',
                        ],
                        [
                            'name' => 'icd_code',
                            'type' => 'text',
                            'label' => 'ICD-10 Code',
                            'placeholder' => 'e.g., E11.65',
                        ],
                        [
                            'name' => 'comorbidities',
                            'type' => 'multiselect',
                            'label' => 'Comorbidities',
                            'options' => [
                                'hypertension', 'dyslipidemia', 'obesity', 'nafld',
                                'ckd', 'cad', 'heart_failure', 'hypothyroidism',
                                'pcos', 'osa', 'depression',
                            ],
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
                        ],
                        [
                            'name' => 'medication_changes',
                            'type' => 'textarea',
                            'label' => 'Medication Changes',
                            'placeholder' => 'New medications, dose changes, stopped medications...',
                        ],
                        [
                            'name' => 'investigations_ordered',
                            'type' => 'multiselect',
                            'label' => 'Investigations Ordered',
                            'options' => [
                                'hba1c', 'fpg', 'ppg', 'rbs', 'ogtt',
                                'lipid_profile', 'lft', 'kft', 'urine_acr', 'serum_electrolytes',
                                'tsh', 'cbc', 'hb', 'iron_studies', 'vitamin_d', 'vitamin_b12',
                                'c_peptide', 'gad_antibodies', 'ecg', 'echo', 'fundoscopy',
                                'usg_abdomen', 'fibroscan', 'abi',
                            ],
                        ],
                        [
                            'name' => 'diabetes_education',
                            'type' => 'multiselect',
                            'label' => 'Diabetes Education Topics Covered',
                            'options' => [
                                'diet_counselling', 'exercise_advice', 'smbg_technique',
                                'insulin_injection_technique', 'site_rotation',
                                'hypo_management', 'sick_day_rules', 'foot_care',
                                'travel_advice', 'driving_advice',
                            ],
                        ],
                        [
                            'name' => 'referral',
                            'type' => 'multiselect',
                            'label' => 'Referrals',
                            'options' => [
                                'ophthalmology', 'nephrology', 'cardiology', 'neurology',
                                'podiatry', 'dietitian', 'diabetes_educator',
                                'psychologist', 'vascular_surgery',
                            ],
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
                            'name' => 'target_goals',
                            'type' => 'textarea',
                            'label' => 'Target Goals for Next Visit',
                            'placeholder' => 'e.g., HbA1c <7%, FPG <130, Weight loss 2kg...',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Get common diabetology diagnoses for autocomplete.
     */
    public static function getCommonDiagnoses(): array
    {
        Log::info('DiabetologyTemplate::getCommonDiagnoses() - returning ICD-10 codes');

        return [
            ['code' => 'E11.65', 'name' => 'Type 2 DM with hyperglycaemia'],
            ['code' => 'E11.9', 'name' => 'Type 2 DM without complications'],
            ['code' => 'E11.22', 'name' => 'Type 2 DM with diabetic chronic kidney disease'],
            ['code' => 'E11.21', 'name' => 'Type 2 DM with diabetic nephropathy'],
            ['code' => 'E11.319', 'name' => 'Type 2 DM with unspecified diabetic retinopathy without macular edema'],
            ['code' => 'E11.311', 'name' => 'Type 2 DM with unspecified diabetic retinopathy with macular edema'],
            ['code' => 'E11.3211', 'name' => 'Type 2 DM with mild NPDR with macular edema, right eye'],
            ['code' => 'E11.3491', 'name' => 'Type 2 DM with severe NPDR without macular edema'],
            ['code' => 'E11.3511', 'name' => 'Type 2 DM with PDR with macular edema, right eye'],
            ['code' => 'E11.40', 'name' => 'Type 2 DM with diabetic neuropathy, unspecified'],
            ['code' => 'E11.42', 'name' => 'Type 2 DM with diabetic polyneuropathy'],
            ['code' => 'E11.43', 'name' => 'Type 2 DM with diabetic autonomic neuropathy'],
            ['code' => 'E11.51', 'name' => 'Type 2 DM with diabetic peripheral angiopathy without gangrene'],
            ['code' => 'E11.52', 'name' => 'Type 2 DM with diabetic peripheral angiopathy with gangrene'],
            ['code' => 'E11.621', 'name' => 'Type 2 DM with foot ulcer'],
            ['code' => 'E11.622', 'name' => 'Type 2 DM with other skin ulcer'],
            ['code' => 'E11.69', 'name' => 'Type 2 DM with other specified complication'],
            ['code' => 'E11.8', 'name' => 'Type 2 DM with unspecified complications'],
            ['code' => 'E10.10', 'name' => 'Type 1 DM with ketoacidosis without coma'],
            ['code' => 'E10.65', 'name' => 'Type 1 DM with hyperglycaemia'],
            ['code' => 'E10.9', 'name' => 'Type 1 DM without complications'],
            ['code' => 'E10.40', 'name' => 'Type 1 DM with diabetic neuropathy, unspecified'],
            ['code' => 'E13.9', 'name' => 'Other specified DM without complications'],
            ['code' => 'E13.65', 'name' => 'Other specified DM with hyperglycaemia'],
            ['code' => 'O24.419', 'name' => 'Gestational DM in pregnancy, unspecified control'],
            ['code' => 'E11.10', 'name' => 'Type 2 DM with ketoacidosis without coma'],
            ['code' => 'E11.00', 'name' => 'Type 2 DM with hyperosmolarity without coma'],
        ];
    }
}
