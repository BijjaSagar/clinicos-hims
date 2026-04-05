<?php

namespace App\Services\EmrTemplates;

use Illuminate\Support\Facades\Log;

class EndocrinologyTemplate
{
    /**
     * Get the complete field schema for the Endocrinology specialty EMR template.
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
                    ],
                ],
                'endocrine_history' => [
                    'label' => 'Endocrine History',
                    'fields' => [
                        'thyroid_symptoms' => ['type' => 'multiselect', 'label' => 'Thyroid Symptoms', 'options' => ['Weight gain', 'Weight loss', 'Heat intolerance', 'Cold intolerance', 'Fatigue', 'Tremor', 'Palpitations', 'Constipation', 'Diarrhea', 'Menstrual irregularity', 'Hair loss', 'Dry skin', 'Neck swelling', 'Hoarseness', 'Dysphagia', 'Eye symptoms']],
                        'adrenal_symptoms' => ['type' => 'multiselect', 'label' => 'Adrenal Symptoms', 'options' => ['Fatigue', 'Weight gain (central)', 'Skin darkening', 'Postural dizziness', 'Salt craving', 'Easy bruising', 'Striae', 'Moon facies', 'Buffalo hump', 'Proximal weakness', 'Hypertension', 'Hypotension']],
                        'pituitary_symptoms' => ['type' => 'multiselect', 'label' => 'Pituitary Symptoms', 'options' => ['Headache', 'Visual field defects', 'Galactorrhea', 'Amenorrhea', 'Decreased libido', 'Erectile dysfunction', 'Growth abnormality', 'Acral enlargement', 'Polyuria', 'Polydipsia']],
                        'calcium_symptoms' => ['type' => 'multiselect', 'label' => 'Calcium/Bone Symptoms', 'options' => ['Bone pain', 'Fracture history', 'Kidney stones', 'Muscle cramps', 'Tingling/Paraesthesia', 'Tetany', 'Fatigue', 'Abdominal pain', 'Constipation', 'Depression']],
                        'dm_symptoms' => ['type' => 'multiselect', 'label' => 'Diabetes Symptoms', 'options' => ['Polyuria', 'Polydipsia', 'Polyphagia', 'Weight loss', 'Blurred vision', 'Recurrent infections', 'Slow wound healing', 'Tingling/Numbness', 'Foot ulcers', 'Claudication']],
                        'family_history_endocrine' => ['type' => 'textarea', 'label' => 'Family History of Endocrine Diseases'],
                    ],
                ],
                'physical_exam' => [
                    'label' => 'Physical Examination',
                    'fields' => [
                        'bmi' => ['type' => 'number', 'label' => 'BMI (kg/m²)'],
                        'waist_circumference' => ['type' => 'number', 'label' => 'Waist Circumference (cm)'],
                        'bp_systolic' => ['type' => 'number', 'label' => 'BP Systolic (mmHg)'],
                        'bp_diastolic' => ['type' => 'number', 'label' => 'BP Diastolic (mmHg)'],
                        'thyroid_size' => ['type' => 'select', 'label' => 'Thyroid Size', 'options' => ['Normal', 'Grade 0 (Not palpable)', 'Grade 1 (Palpable, not visible)', 'Grade 2 (Visible on extension)', 'Grade 3 (Visible, large goitre)']],
                        'thyroid_consistency' => ['type' => 'select', 'label' => 'Thyroid Consistency', 'options' => ['Normal/Soft', 'Firm', 'Hard', 'Nodular']],
                        'thyroid_nodules' => ['type' => 'select', 'label' => 'Thyroid Nodules', 'options' => ['None', 'Single nodule - right lobe', 'Single nodule - left lobe', 'Single nodule - isthmus', 'Multinodular', 'Dominant nodule in MNG']],
                        'thyroid_bruit' => ['type' => 'boolean', 'label' => 'Thyroid Bruit'],
                        'thyroid_tenderness' => ['type' => 'boolean', 'label' => 'Thyroid Tenderness'],
                        'lymphadenopathy' => ['type' => 'select', 'label' => 'Cervical Lymphadenopathy', 'options' => ['None', 'Palpable', 'Multiple', 'Fixed']],
                        'ophthalmopathy' => ['type' => 'multiselect', 'label' => 'Ophthalmopathy (Graves)', 'options' => ['None', 'Lid retraction', 'Lid lag', 'Proptosis/Exophthalmos', 'Periorbital edema', 'Chemosis', 'Diplopia', 'Exposure keratopathy', 'Optic neuropathy']],
                        'ophthalmopathy_class' => ['type' => 'select', 'label' => 'Ophthalmopathy (NOSPECS Class)', 'options' => ['0 - No signs/symptoms', '1 - Only signs (lid retraction)', '2 - Soft tissue involvement', '3 - Proptosis', '4 - Extraocular muscle involvement', '5 - Corneal involvement', '6 - Sight loss (optic nerve)']],
                        'acromegaly_features' => ['type' => 'multiselect', 'label' => 'Acromegaly Features', 'options' => ['None', 'Coarsened facial features', 'Prognathism', 'Frontal bossing', 'Macroglossia', 'Enlarged hands', 'Enlarged feet', 'Increased ring/shoe size', 'Skin tags', 'Acanthosis nigricans', 'Hyperhidrosis', 'Carpal tunnel syndrome']],
                        'cushing_features' => ['type' => 'multiselect', 'label' => 'Cushing Features', 'options' => ['None', 'Moon facies', 'Buffalo hump', 'Central obesity', 'Thin extremities', 'Proximal myopathy', 'Striae (purple)', 'Easy bruising', 'Hirsutism', 'Acne', 'Thin skin', 'Dorsocervical fat pad', 'Supraclavicular fat pads']],
                        'acanthosis_nigricans' => ['type' => 'select', 'label' => 'Acanthosis Nigricans', 'options' => ['None', 'Mild (neck)', 'Moderate (neck + axillae)', 'Severe (neck + axillae + other)']],
                        'skin_exam' => ['type' => 'textarea', 'label' => 'Skin Examination Notes'],
                    ],
                ],
                'thyroid_assessment' => [
                    'label' => 'Thyroid Assessment',
                    'fields' => [
                        'tsh' => ['type' => 'number', 'label' => 'TSH (mIU/L)'],
                        'ft3' => ['type' => 'number', 'label' => 'Free T3 (pg/mL)'],
                        'ft4' => ['type' => 'number', 'label' => 'Free T4 (ng/dL)'],
                        'total_t3' => ['type' => 'number', 'label' => 'Total T3 (ng/dL)'],
                        'total_t4' => ['type' => 'number', 'label' => 'Total T4 (µg/dL)'],
                        'anti_tpo' => ['type' => 'number', 'label' => 'Anti-TPO Antibodies (IU/mL)'],
                        'anti_tg' => ['type' => 'number', 'label' => 'Anti-Thyroglobulin Antibodies (IU/mL)'],
                        'trab' => ['type' => 'number', 'label' => 'TSH Receptor Antibodies (TRAb)'],
                        'thyroglobulin' => ['type' => 'number', 'label' => 'Thyroglobulin (ng/mL)'],
                        'calcitonin' => ['type' => 'number', 'label' => 'Calcitonin (pg/mL)'],
                        'thyroid_usg' => ['type' => 'textarea', 'label' => 'Thyroid USG Findings'],
                        'nodule_size' => ['type' => 'text', 'label' => 'Nodule Size (mm)'],
                        'tirads_category' => ['type' => 'select', 'label' => 'TIRADS Category', 'options' => [
                            ['value' => 'TR1', 'label' => 'TR1 - Benign'],
                            ['value' => 'TR2', 'label' => 'TR2 - Not suspicious'],
                            ['value' => 'TR3', 'label' => 'TR3 - Mildly suspicious'],
                            ['value' => 'TR4', 'label' => 'TR4 - Moderately suspicious'],
                            ['value' => 'TR5', 'label' => 'TR5 - Highly suspicious'],
                        ]],
                        'fnac_result' => ['type' => 'select', 'label' => 'FNAC Result (Bethesda)', 'options' => [
                            ['value' => 'I', 'label' => 'Bethesda I - Non-diagnostic'],
                            ['value' => 'II', 'label' => 'Bethesda II - Benign'],
                            ['value' => 'III', 'label' => 'Bethesda III - AUS/FLUS'],
                            ['value' => 'IV', 'label' => 'Bethesda IV - Follicular neoplasm'],
                            ['value' => 'V', 'label' => 'Bethesda V - Suspicious for malignancy'],
                            ['value' => 'VI', 'label' => 'Bethesda VI - Malignant'],
                        ]],
                        'radioiodine_uptake' => ['type' => 'text', 'label' => 'Radioiodine Uptake (RAIU)'],
                        'thyroid_scan' => ['type' => 'textarea', 'label' => 'Thyroid Scan Findings'],
                    ],
                ],
                'diabetes_assessment' => [
                    'label' => 'Diabetes Assessment',
                    'fields' => [
                        'dm_type' => ['type' => 'select', 'label' => 'Diabetes Type', 'options' => ['Type 1', 'Type 2', 'GDM', 'LADA', 'MODY', 'Secondary', 'Prediabetes', 'Not diabetic']],
                        'dm_duration_years' => ['type' => 'number', 'label' => 'DM Duration (years)'],
                        'hba1c' => ['type' => 'number', 'label' => 'HbA1c (%)'],
                        'fasting_glucose' => ['type' => 'number', 'label' => 'Fasting Glucose (mg/dL)'],
                        'pp_glucose' => ['type' => 'number', 'label' => 'Post-Prandial Glucose (mg/dL)'],
                        'random_glucose' => ['type' => 'number', 'label' => 'Random Glucose (mg/dL)'],
                        'fasting_insulin' => ['type' => 'number', 'label' => 'Fasting Insulin (µIU/mL)'],
                        'c_peptide' => ['type' => 'number', 'label' => 'C-Peptide (ng/mL)'],
                        'gad_antibodies' => ['type' => 'select', 'label' => 'GAD Antibodies', 'options' => ['Not tested', 'Positive', 'Negative']],
                        'insulin_regimen' => ['type' => 'textarea', 'label' => 'Current Insulin Regimen'],
                        'oha_regimen' => ['type' => 'textarea', 'label' => 'Current OHA Regimen'],
                        'cgm_data' => ['type' => 'textarea', 'label' => 'CGM/SMBG Data Summary'],
                        'hypoglycemia_frequency' => ['type' => 'select', 'label' => 'Hypoglycemia Frequency', 'options' => ['None', 'Rare (<1/month)', 'Occasional (1-4/month)', 'Frequent (>4/month)', 'Severe (requiring assistance)']],
                        'retinopathy' => ['type' => 'select', 'label' => 'Diabetic Retinopathy', 'options' => ['None', 'Mild NPDR', 'Moderate NPDR', 'Severe NPDR', 'PDR', 'Macular edema', 'Not screened']],
                        'nephropathy' => ['type' => 'select', 'label' => 'Diabetic Nephropathy', 'options' => ['None', 'Microalbuminuria', 'Macroalbuminuria', 'CKD 3-4', 'ESRD', 'Not screened']],
                        'neuropathy' => ['type' => 'select', 'label' => 'Diabetic Neuropathy', 'options' => ['None', 'Peripheral sensory', 'Peripheral motor', 'Autonomic', 'Painful neuropathy', 'Not screened']],
                        'peripheral_vascular' => ['type' => 'select', 'label' => 'Peripheral Vascular Disease', 'options' => ['None', 'Claudication', 'Rest pain', 'Previous amputation', 'Not screened']],
                        'foot_exam' => ['type' => 'textarea', 'label' => 'Diabetic Foot Examination'],
                        'monofilament_test' => ['type' => 'select', 'label' => 'Monofilament Test', 'options' => ['Normal', 'Reduced sensation', 'Absent sensation', 'Not done']],
                    ],
                ],
                'bone_health' => [
                    'label' => 'Bone Health Assessment',
                    'fields' => [
                        'dexa_done' => ['type' => 'boolean', 'label' => 'DEXA Scan Done'],
                        'dexa_date' => ['type' => 'date', 'label' => 'DEXA Date'],
                        'dexa_t_score_spine' => ['type' => 'number', 'label' => 'DEXA T-Score (Lumbar Spine)'],
                        'dexa_t_score_hip' => ['type' => 'number', 'label' => 'DEXA T-Score (Hip)'],
                        'dexa_t_score_femur' => ['type' => 'number', 'label' => 'DEXA T-Score (Femoral Neck)'],
                        'dexa_z_score' => ['type' => 'number', 'label' => 'Z-Score (if applicable)'],
                        'dexa_interpretation' => ['type' => 'select', 'label' => 'DEXA Interpretation', 'options' => ['Normal (T-score ≥ -1.0)', 'Osteopenia (T-score -1.0 to -2.5)', 'Osteoporosis (T-score ≤ -2.5)', 'Severe Osteoporosis (T ≤ -2.5 + fracture)']],
                        'serum_calcium' => ['type' => 'number', 'label' => 'Serum Calcium (mg/dL)'],
                        'serum_phosphorus' => ['type' => 'number', 'label' => 'Serum Phosphorus (mg/dL)'],
                        'vitamin_d_25oh' => ['type' => 'number', 'label' => '25-OH Vitamin D (ng/mL)'],
                        'vitamin_d_status' => ['type' => 'select', 'label' => 'Vitamin D Status', 'options' => ['Sufficient (>30)', 'Insufficient (20-30)', 'Deficient (<20)', 'Severely deficient (<10)']],
                        'pth' => ['type' => 'number', 'label' => 'iPTH (pg/mL)'],
                        'alkaline_phosphatase' => ['type' => 'number', 'label' => 'Alkaline Phosphatase (U/L)'],
                        'fracture_history' => ['type' => 'textarea', 'label' => 'Fracture History'],
                        'frax_score' => ['type' => 'number', 'label' => 'FRAX 10-year Fracture Risk (%)'],
                    ],
                ],
                'diagnosis' => [
                    'label' => 'Diagnosis',
                    'fields' => [
                        'provisional_diagnosis' => ['type' => 'text', 'label' => 'Provisional Diagnosis', 'required' => true, 'autocomplete' => 'icd10_endocrinology'],
                        'differential_diagnosis' => ['type' => 'tags', 'label' => 'Differential Diagnosis'],
                        'icd10_code' => ['type' => 'text', 'label' => 'ICD-10 Code'],
                    ],
                ],
                'plan' => [
                    'label' => 'Plan & Follow-up',
                    'fields' => [
                        'treatment_plan' => ['type' => 'textarea', 'label' => 'Treatment Plan'],
                        'diet_advice' => ['type' => 'textarea', 'label' => 'Diet & Lifestyle Advice'],
                        'investigations' => ['type' => 'multiselect', 'label' => 'Investigations', 'options' => ['TSH', 'FT3/FT4', 'Thyroid Antibodies', 'Thyroid USG', 'FNAC', 'Thyroid Scan', 'HbA1c', 'Fasting/PP Glucose', 'C-Peptide', 'GAD Antibodies', 'Insulin Level', 'Cortisol (8am)', 'ACTH', '24hr Urine Cortisol', 'Dex Suppression Test', 'IGF-1', 'GH', 'Prolactin', 'LH/FSH', 'Testosterone', 'Estradiol', 'DHEAS', 'Calcium/Phosphorus', 'Vitamin D', 'PTH', 'DEXA Scan', 'MRI Pituitary', 'CT Adrenals', 'Lipid Profile', 'Urine ACR']],
                        'follow_up_date' => ['type' => 'date', 'label' => 'Follow-up Date'],
                        'follow_up_notes' => ['type' => 'textarea', 'label' => 'Follow-up Instructions'],
                        'referral' => ['type' => 'text', 'label' => 'Referral To'],
                    ],
                ],
            ],
            'scales' => ['TIRADS', 'Bethesda', 'NOSPECS', 'FRAX', 'FINDRISC'],
            'procedures' => [
                'fnac_thyroid' => ['label' => 'FNAC Thyroid', 'sac_code' => '999311', 'params' => ['nodule_location', 'nodule_size', 'usg_guided']],
                'insulin_initiation' => ['label' => 'Insulin Initiation/Titration', 'sac_code' => '999311', 'params' => ['regimen', 'dose', 'device']],
                'cgm_placement' => ['label' => 'CGM Sensor Placement', 'sac_code' => '999311', 'params' => ['device_type', 'site']],
                'insulin_pump' => ['label' => 'Insulin Pump Setup', 'sac_code' => '999311', 'params' => ['pump_model', 'basal_rates', 'carb_ratio']],
            ],
        ];
    }

    /**
     * Default data structure for a new endocrinology visit.
     */
    public static function defaultData(): array
    {
        return [
            'chief_complaint' => '',
            'history_present_illness' => '',
            'endocrine_history' => ['thyroid_symptoms' => [], 'adrenal_symptoms' => [], 'pituitary_symptoms' => [], 'calcium_symptoms' => []],
            'physical_exam' => ['thyroid_size' => 'Normal', 'thyroid_nodules' => 'None', 'ophthalmopathy' => [], 'acromegaly_features' => [], 'cushing_features' => []],
            'thyroid' => ['tsh' => null, 'ft3' => null, 'ft4' => null, 'antibodies' => [], 'usg' => '', 'tirads' => '', 'fnac' => ''],
            'diabetes' => ['type' => '', 'hba1c' => null, 'fasting' => null, 'pp' => null, 'insulin_regimen' => '', 'complications' => []],
            'bone_health' => ['dexa_t_score' => null, 'dexa_z_score' => null, 'calcium' => null, 'vitamin_d' => null, 'pth' => null],
            'diagnosis' => ['provisional' => '', 'differential' => [], 'icd10' => ''],
            'plan' => ['treatment' => '', 'follow_up_date' => '', 'follow_up_notes' => ''],
        ];
    }

    /**
     * Get endocrinology EMR template fields.
     */
    public static function getFields(): array
    {
        Log::info('Loading Endocrinology EMR template');
        Log::info('Endocrinology template: building sections array');

        return [
            'specialty' => 'endocrinology',
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
                            'placeholder' => 'e.g., 6 months, 2 years',
                        ],
                    ],
                ],

                [
                    'id' => 'endocrine_history',
                    'title' => 'Endocrine History',
                    'fields' => [
                        [
                            'name' => 'thyroid_symptoms',
                            'type' => 'multiselect',
                            'label' => 'Thyroid Symptoms',
                            'options' => [
                                'weight_gain', 'weight_loss', 'heat_intolerance',
                                'cold_intolerance', 'fatigue', 'tremor', 'palpitations',
                                'constipation', 'diarrhea', 'menstrual_irregularity',
                                'hair_loss', 'dry_skin', 'neck_swelling', 'eye_symptoms',
                            ],
                        ],
                        [
                            'name' => 'adrenal_symptoms',
                            'type' => 'multiselect',
                            'label' => 'Adrenal Symptoms',
                            'options' => [
                                'fatigue', 'central_weight_gain', 'skin_darkening',
                                'postural_dizziness', 'salt_craving', 'easy_bruising',
                                'striae', 'moon_facies', 'proximal_weakness',
                            ],
                        ],
                        [
                            'name' => 'pituitary_symptoms',
                            'type' => 'multiselect',
                            'label' => 'Pituitary Symptoms',
                            'options' => [
                                'headache', 'visual_field_defects', 'galactorrhea',
                                'amenorrhea', 'decreased_libido', 'erectile_dysfunction',
                                'acral_enlargement', 'polyuria', 'polydipsia',
                            ],
                        ],
                        [
                            'name' => 'calcium_metabolism',
                            'type' => 'multiselect',
                            'label' => 'Calcium/Bone Symptoms',
                            'options' => [
                                'bone_pain', 'fracture_history', 'kidney_stones',
                                'muscle_cramps', 'paraesthesia', 'tetany', 'fatigue',
                            ],
                        ],
                    ],
                ],

                [
                    'id' => 'physical_exam',
                    'title' => 'Physical Examination',
                    'fields' => [
                        [
                            'name' => 'thyroid_size',
                            'type' => 'select',
                            'label' => 'Thyroid Size',
                            'options' => ['normal', 'grade_0', 'grade_1', 'grade_2', 'grade_3'],
                        ],
                        [
                            'name' => 'thyroid_nodules',
                            'type' => 'select',
                            'label' => 'Thyroid Nodules',
                            'options' => ['none', 'single_right', 'single_left', 'single_isthmus', 'multinodular'],
                        ],
                        [
                            'name' => 'thyroid_consistency',
                            'type' => 'select',
                            'label' => 'Thyroid Consistency',
                            'options' => ['soft', 'firm', 'hard', 'nodular'],
                        ],
                        [
                            'name' => 'thyroid_bruit',
                            'type' => 'boolean',
                            'label' => 'Thyroid Bruit',
                        ],
                        [
                            'name' => 'ophthalmopathy',
                            'type' => 'multiselect',
                            'label' => 'Ophthalmopathy (Graves)',
                            'options' => [
                                'none', 'lid_retraction', 'lid_lag', 'proptosis',
                                'periorbital_edema', 'chemosis', 'diplopia',
                                'exposure_keratopathy', 'optic_neuropathy',
                            ],
                        ],
                        [
                            'name' => 'acromegaly_features',
                            'type' => 'multiselect',
                            'label' => 'Acromegaly Features',
                            'options' => [
                                'none', 'coarsened_facies', 'prognathism', 'frontal_bossing',
                                'macroglossia', 'enlarged_hands', 'enlarged_feet',
                                'skin_tags', 'acanthosis', 'hyperhidrosis',
                            ],
                        ],
                        [
                            'name' => 'cushing_features',
                            'type' => 'multiselect',
                            'label' => 'Cushing Features',
                            'options' => [
                                'none', 'moon_facies', 'buffalo_hump', 'central_obesity',
                                'thin_extremities', 'proximal_myopathy', 'purple_striae',
                                'easy_bruising', 'hirsutism', 'thin_skin',
                            ],
                        ],
                        [
                            'name' => 'bmi',
                            'type' => 'number',
                            'label' => 'BMI (kg/m²)',
                        ],
                        [
                            'name' => 'waist_circumference',
                            'type' => 'number',
                            'label' => 'Waist Circumference (cm)',
                        ],
                        [
                            'name' => 'acanthosis_nigricans',
                            'type' => 'select',
                            'label' => 'Acanthosis Nigricans',
                            'options' => ['none', 'mild_neck', 'moderate_neck_axillae', 'severe'],
                        ],
                    ],
                ],

                [
                    'id' => 'thyroid_assessment',
                    'title' => 'Thyroid Assessment',
                    'fields' => [
                        [
                            'name' => 'tsh',
                            'type' => 'number',
                            'label' => 'TSH (mIU/L)',
                        ],
                        [
                            'name' => 'ft3',
                            'type' => 'number',
                            'label' => 'Free T3 (pg/mL)',
                        ],
                        [
                            'name' => 'ft4',
                            'type' => 'number',
                            'label' => 'Free T4 (ng/dL)',
                        ],
                        [
                            'name' => 'anti_tpo',
                            'type' => 'number',
                            'label' => 'Anti-TPO Antibodies (IU/mL)',
                        ],
                        [
                            'name' => 'anti_tg',
                            'type' => 'number',
                            'label' => 'Anti-Thyroglobulin Antibodies (IU/mL)',
                        ],
                        [
                            'name' => 'trab',
                            'type' => 'number',
                            'label' => 'TSH Receptor Antibodies (TRAb)',
                        ],
                        [
                            'name' => 'thyroid_usg',
                            'type' => 'textarea',
                            'label' => 'Thyroid USG Findings',
                        ],
                        [
                            'name' => 'tirads',
                            'type' => 'select',
                            'label' => 'TIRADS Category',
                            'options' => [
                                ['value' => 'TR1', 'label' => 'TR1 - Benign'],
                                ['value' => 'TR2', 'label' => 'TR2 - Not suspicious'],
                                ['value' => 'TR3', 'label' => 'TR3 - Mildly suspicious'],
                                ['value' => 'TR4', 'label' => 'TR4 - Moderately suspicious'],
                                ['value' => 'TR5', 'label' => 'TR5 - Highly suspicious'],
                            ],
                        ],
                        [
                            'name' => 'fnac_bethesda',
                            'type' => 'select',
                            'label' => 'FNAC (Bethesda)',
                            'options' => [
                                ['value' => 'I', 'label' => 'I - Non-diagnostic'],
                                ['value' => 'II', 'label' => 'II - Benign'],
                                ['value' => 'III', 'label' => 'III - AUS/FLUS'],
                                ['value' => 'IV', 'label' => 'IV - Follicular neoplasm'],
                                ['value' => 'V', 'label' => 'V - Suspicious for malignancy'],
                                ['value' => 'VI', 'label' => 'VI - Malignant'],
                            ],
                        ],
                    ],
                ],

                [
                    'id' => 'diabetes_assessment',
                    'title' => 'Diabetes Assessment',
                    'fields' => [
                        [
                            'name' => 'dm_type',
                            'type' => 'select',
                            'label' => 'Diabetes Type',
                            'options' => ['type_1', 'type_2', 'gdm', 'lada', 'mody', 'secondary', 'prediabetes', 'not_diabetic'],
                        ],
                        [
                            'name' => 'dm_duration',
                            'type' => 'number',
                            'label' => 'DM Duration (years)',
                        ],
                        [
                            'name' => 'hba1c',
                            'type' => 'number',
                            'label' => 'HbA1c (%)',
                        ],
                        [
                            'name' => 'fasting_glucose',
                            'type' => 'number',
                            'label' => 'Fasting Glucose (mg/dL)',
                        ],
                        [
                            'name' => 'pp_glucose',
                            'type' => 'number',
                            'label' => 'Post-Prandial Glucose (mg/dL)',
                        ],
                        [
                            'name' => 'insulin_regimen',
                            'type' => 'textarea',
                            'label' => 'Current Insulin Regimen',
                            'placeholder' => 'Type, dose, timing...',
                        ],
                        [
                            'name' => 'oha_regimen',
                            'type' => 'textarea',
                            'label' => 'Current OHA Regimen',
                        ],
                        [
                            'name' => 'complications_screening',
                            'type' => 'multiselect',
                            'label' => 'Complications Screening',
                            'options' => [
                                'retinopathy_screened', 'nephropathy_screened',
                                'neuropathy_screened', 'foot_examined',
                                'cardiovascular_risk_assessed',
                            ],
                        ],
                        [
                            'name' => 'retinopathy_status',
                            'type' => 'select',
                            'label' => 'Retinopathy Status',
                            'options' => ['none', 'mild_npdr', 'moderate_npdr', 'severe_npdr', 'pdr', 'macular_edema', 'not_screened'],
                        ],
                        [
                            'name' => 'neuropathy_status',
                            'type' => 'select',
                            'label' => 'Neuropathy Status',
                            'options' => ['none', 'peripheral_sensory', 'autonomic', 'painful', 'not_screened'],
                        ],
                    ],
                ],

                [
                    'id' => 'bone_health',
                    'title' => 'Bone Health',
                    'fields' => [
                        [
                            'name' => 'dexa_t_score_spine',
                            'type' => 'number',
                            'label' => 'DEXA T-Score (Lumbar Spine)',
                        ],
                        [
                            'name' => 'dexa_t_score_hip',
                            'type' => 'number',
                            'label' => 'DEXA T-Score (Hip)',
                        ],
                        [
                            'name' => 'dexa_z_score',
                            'type' => 'number',
                            'label' => 'Z-Score',
                        ],
                        [
                            'name' => 'dexa_interpretation',
                            'type' => 'select',
                            'label' => 'Interpretation',
                            'options' => ['normal', 'osteopenia', 'osteoporosis', 'severe_osteoporosis'],
                        ],
                        [
                            'name' => 'serum_calcium',
                            'type' => 'number',
                            'label' => 'Serum Calcium (mg/dL)',
                        ],
                        [
                            'name' => 'vitamin_d',
                            'type' => 'number',
                            'label' => '25-OH Vitamin D (ng/mL)',
                        ],
                        [
                            'name' => 'pth',
                            'type' => 'number',
                            'label' => 'iPTH (pg/mL)',
                        ],
                        [
                            'name' => 'fracture_history',
                            'type' => 'textarea',
                            'label' => 'Fracture History',
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
                            'autocomplete' => 'icd10_endocrinology',
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
                            'placeholder' => 'e.g., E11.9',
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
                            'placeholder' => 'Medication changes, dose titration...',
                        ],
                        [
                            'name' => 'diet_lifestyle',
                            'type' => 'textarea',
                            'label' => 'Diet & Lifestyle Advice',
                        ],
                        [
                            'name' => 'investigations',
                            'type' => 'multiselect',
                            'label' => 'Investigations',
                            'options' => [
                                'tsh', 'ft3_ft4', 'thyroid_antibodies', 'thyroid_usg',
                                'fnac', 'thyroid_scan', 'hba1c', 'fasting_pp_glucose',
                                'c_peptide', 'gad_antibodies', 'cortisol_8am', 'acth',
                                '24hr_urine_cortisol', 'igf1', 'prolactin', 'lh_fsh',
                                'calcium_phosphorus', 'vitamin_d', 'pth', 'dexa_scan',
                                'mri_pituitary', 'ct_adrenals', 'lipid_profile', 'urine_acr',
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
                                ['value' => 180, 'label' => '6 Months'],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Get HbA1c interpretation.
     */
    public static function getHba1cInterpretation(float $hba1c): string
    {
        Log::info('Getting HbA1c interpretation', ['hba1c' => $hba1c]);

        return match (true) {
            $hba1c < 5.7 => 'Normal',
            $hba1c < 6.5 => 'Prediabetes',
            $hba1c < 7.0 => 'Good glycemic control (DM at target)',
            $hba1c < 8.0 => 'Suboptimal control',
            $hba1c < 9.0 => 'Poor control',
            default => 'Very poor control - urgent intervention needed',
        };
    }

    /**
     * Get thyroid function interpretation.
     */
    public static function getThyroidInterpretation(float $tsh, ?float $ft4 = null, ?float $ft3 = null): string
    {
        Log::info('Getting thyroid function interpretation', [
            'tsh' => $tsh,
            'ft4' => $ft4,
            'ft3' => $ft3,
        ]);

        if ($tsh < 0.4) {
            if ($ft4 !== null && $ft4 > 1.8) {
                return 'Overt hyperthyroidism (low TSH, high FT4)';
            }
            if ($ft3 !== null && $ft3 > 4.4) {
                return 'T3 thyrotoxicosis (low TSH, high FT3, normal FT4)';
            }
            return 'Subclinical hyperthyroidism (low TSH, normal FT4/FT3)';
        }

        if ($tsh > 4.5) {
            if ($ft4 !== null && $ft4 < 0.8) {
                return 'Overt hypothyroidism (high TSH, low FT4)';
            }
            return 'Subclinical hypothyroidism (high TSH, normal FT4)';
        }

        return 'Euthyroid (normal thyroid function)';
    }

    /**
     * Get DEXA interpretation based on T-score.
     */
    public static function getDexaInterpretation(float $tScore, bool $hasFracture = false): string
    {
        Log::info('Getting DEXA interpretation', [
            't_score' => $tScore,
            'has_fracture' => $hasFracture,
        ]);

        if ($tScore >= -1.0) {
            return 'Normal bone density';
        }

        if ($tScore > -2.5) {
            return 'Osteopenia (low bone mass)';
        }

        if ($hasFracture) {
            return 'Severe/Established Osteoporosis (T ≤ -2.5 with fragility fracture)';
        }

        return 'Osteoporosis';
    }

    /**
     * Get common endocrinology diagnoses for autocomplete.
     */
    public static function getCommonDiagnoses(): array
    {
        Log::info('Fetching common endocrinology diagnoses list');

        return [
            ['code' => 'E05', 'name' => 'Thyrotoxicosis (Hyperthyroidism)'],
            ['code' => 'E05.0', 'name' => 'Thyrotoxicosis with diffuse goitre (Graves disease)'],
            ['code' => 'E05.1', 'name' => 'Thyrotoxicosis with toxic single nodule'],
            ['code' => 'E05.2', 'name' => 'Thyrotoxicosis with toxic multinodular goitre'],
            ['code' => 'E03', 'name' => 'Other hypothyroidism'],
            ['code' => 'E03.9', 'name' => 'Hypothyroidism, unspecified'],
            ['code' => 'E06.3', 'name' => "Hashimoto's thyroiditis (Autoimmune thyroiditis)"],
            ['code' => 'E06.1', 'name' => 'Subacute thyroiditis (de Quervain)'],
            ['code' => 'E04.0', 'name' => 'Nontoxic diffuse goitre'],
            ['code' => 'E04.1', 'name' => 'Nontoxic single thyroid nodule'],
            ['code' => 'E04.2', 'name' => 'Nontoxic multinodular goitre'],
            ['code' => 'C73', 'name' => 'Thyroid carcinoma'],
            ['code' => 'E11', 'name' => 'Type 2 diabetes mellitus'],
            ['code' => 'E11.9', 'name' => 'Type 2 DM without complications'],
            ['code' => 'E11.65', 'name' => 'Type 2 DM with hyperglycemia'],
            ['code' => 'E11.21', 'name' => 'Type 2 DM with diabetic nephropathy'],
            ['code' => 'E11.31', 'name' => 'Type 2 DM with retinopathy'],
            ['code' => 'E11.40', 'name' => 'Type 2 DM with diabetic neuropathy'],
            ['code' => 'E10', 'name' => 'Type 1 diabetes mellitus'],
            ['code' => 'E10.9', 'name' => 'Type 1 DM without complications'],
            ['code' => 'E13', 'name' => 'Other specified diabetes mellitus (LADA/MODY)'],
            ['code' => 'R73.03', 'name' => 'Prediabetes (Impaired fasting glucose)'],
            ['code' => 'E22.0', 'name' => 'Acromegaly and pituitary gigantism'],
            ['code' => 'E23.0', 'name' => 'Hypopituitarism'],
            ['code' => 'E22.1', 'name' => 'Hyperprolactinemia'],
            ['code' => 'E24', 'name' => "Cushing's syndrome"],
            ['code' => 'E24.0', 'name' => "Pituitary-dependent Cushing's disease"],
            ['code' => 'E24.2', 'name' => 'Drug-induced Cushing syndrome'],
            ['code' => 'E27.1', 'name' => "Primary adrenocortical insufficiency (Addison's disease)"],
            ['code' => 'E27.4', 'name' => 'Other and unspecified adrenocortical insufficiency'],
            ['code' => 'E21', 'name' => 'Hyperparathyroidism'],
            ['code' => 'E21.0', 'name' => 'Primary hyperparathyroidism'],
            ['code' => 'E21.1', 'name' => 'Secondary hyperparathyroidism'],
            ['code' => 'E20', 'name' => 'Hypoparathyroidism'],
            ['code' => 'M81', 'name' => 'Osteoporosis without pathological fracture'],
            ['code' => 'M81.0', 'name' => 'Age-related osteoporosis'],
            ['code' => 'M80', 'name' => 'Osteoporosis with pathological fracture'],
            ['code' => 'E55.9', 'name' => 'Vitamin D deficiency, unspecified'],
            ['code' => 'E28.2', 'name' => 'Polycystic ovarian syndrome (PCOS)'],
            ['code' => 'E66', 'name' => 'Obesity'],
            ['code' => 'E66.01', 'name' => 'Morbid obesity due to excess calories'],
        ];
    }
}
