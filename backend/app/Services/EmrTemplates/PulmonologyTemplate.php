<?php

namespace App\Services\EmrTemplates;

use Illuminate\Support\Facades\Log;

class PulmonologyTemplate
{
    /**
     * Get pulmonology EMR template fields.
     */
    public static function getFields(): array
    {
        Log::info('Loading Pulmonology EMR template');
        Log::info('PulmonologyTemplate::getFields() - building sections array');

        return [
            'specialty' => 'pulmonology',
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
                            'name' => 'cough_type',
                            'type' => 'select',
                            'label' => 'Cough Type',
                            'options' => ['dry', 'productive', 'paroxysmal', 'nocturnal', 'postural'],
                        ],
                        [
                            'name' => 'cough_duration',
                            'type' => 'text',
                            'label' => 'Cough Duration',
                            'placeholder' => 'e.g., 2 weeks, 3 months',
                        ],
                        [
                            'name' => 'sputum',
                            'type' => 'select',
                            'label' => 'Sputum',
                            'options' => ['none', 'mucoid', 'mucopurulent', 'purulent', 'frothy', 'rusty', 'blood_tinged'],
                        ],
                        [
                            'name' => 'sputum_quantity',
                            'type' => 'select',
                            'label' => 'Sputum Quantity',
                            'options' => ['scanty', 'moderate', 'copious'],
                        ],
                        [
                            'name' => 'hemoptysis',
                            'type' => 'select',
                            'label' => 'Hemoptysis',
                            'options' => ['none', 'streaky', 'frank', 'massive'],
                        ],
                        [
                            'name' => 'hemoptysis_volume',
                            'type' => 'text',
                            'label' => 'Hemoptysis Volume (approx ml)',
                            'placeholder' => 'e.g., 5 ml',
                        ],
                        [
                            'name' => 'dyspnea_grade',
                            'type' => 'select',
                            'label' => 'Dyspnea Grade (mMRC)',
                            'options' => [
                                ['value' => 0, 'label' => '0 - Breathless with strenuous exercise only'],
                                ['value' => 1, 'label' => '1 - Breathless when hurrying on level or walking up slight hill'],
                                ['value' => 2, 'label' => '2 - Walks slower than contemporaries on level due to breathlessness'],
                                ['value' => 3, 'label' => '3 - Stops after 100m or a few minutes on level'],
                                ['value' => 4, 'label' => '4 - Too breathless to leave house or breathless when dressing'],
                            ],
                        ],
                        [
                            'name' => 'wheeze',
                            'type' => 'select',
                            'label' => 'Wheeze',
                            'options' => ['none', 'expiratory', 'inspiratory', 'both', 'nocturnal', 'exercise_induced'],
                        ],
                        [
                            'name' => 'chest_pain',
                            'type' => 'select',
                            'label' => 'Chest Pain',
                            'options' => ['none', 'pleuritic', 'central', 'localised', 'diffuse'],
                        ],
                        [
                            'name' => 'fever',
                            'type' => 'boolean',
                            'label' => 'Fever',
                        ],
                        [
                            'name' => 'night_sweats',
                            'type' => 'boolean',
                            'label' => 'Night Sweats',
                        ],
                        [
                            'name' => 'weight_loss',
                            'type' => 'boolean',
                            'label' => 'Weight Loss',
                        ],
                    ],
                ],

                // Respiratory History
                [
                    'id' => 'respiratory_history',
                    'title' => 'Respiratory History',
                    'fields' => [
                        [
                            'name' => 'smoking_status',
                            'type' => 'select',
                            'label' => 'Smoking Status',
                            'options' => ['never', 'current_smoker', 'ex_smoker', 'passive_smoker'],
                        ],
                        [
                            'name' => 'smoking_pack_years',
                            'type' => 'number',
                            'label' => 'Smoking Pack-Years',
                            'placeholder' => 'Packs/day × years',
                        ],
                        [
                            'name' => 'smoking_quit_date',
                            'type' => 'date',
                            'label' => 'Smoking Quit Date',
                        ],
                        [
                            'name' => 'occupational_exposure',
                            'type' => 'multiselect',
                            'label' => 'Occupational Exposure',
                            'options' => [
                                'asbestos', 'silica', 'coal_dust', 'cotton_dust', 'chemicals',
                                'fumes', 'grain_dust', 'wood_dust', 'animal_dander', 'mould',
                            ],
                        ],
                        [
                            'name' => 'occupational_exposure_duration',
                            'type' => 'text',
                            'label' => 'Exposure Duration',
                            'placeholder' => 'e.g., 10 years in textile mill',
                        ],
                        [
                            'name' => 'tb_history',
                            'type' => 'select',
                            'label' => 'TB History',
                            'options' => ['none', 'treated_completed', 'treated_incomplete', 'mdr_tb', 'contact_history'],
                        ],
                        [
                            'name' => 'tb_treatment_details',
                            'type' => 'textarea',
                            'label' => 'TB Treatment Details',
                            'placeholder' => 'Regimen, duration, outcome...',
                        ],
                        [
                            'name' => 'allergy_history',
                            'type' => 'textarea',
                            'label' => 'Allergy History',
                            'placeholder' => 'Known allergens, atopic history...',
                        ],
                        [
                            'name' => 'atopic_history',
                            'type' => 'multiselect',
                            'label' => 'Atopic History',
                            'options' => ['asthma', 'allergic_rhinitis', 'eczema', 'food_allergy', 'drug_allergy'],
                        ],
                        [
                            'name' => 'past_respiratory_illness',
                            'type' => 'multiselect',
                            'label' => 'Past Respiratory Illness',
                            'options' => [
                                'childhood_asthma', 'pneumonia', 'pleurisy', 'empyema',
                                'lung_abscess', 'bronchiectasis', 'ild', 'pneumothorax',
                            ],
                        ],
                        [
                            'name' => 'family_history_respiratory',
                            'type' => 'textarea',
                            'label' => 'Family History (Respiratory)',
                        ],
                    ],
                ],

                // Examination
                [
                    'id' => 'examination',
                    'title' => 'Respiratory Examination',
                    'fields' => [
                        [
                            'name' => 'respiratory_rate',
                            'type' => 'number',
                            'label' => 'Respiratory Rate (breaths/min)',
                            'unit' => '/min',
                        ],
                        [
                            'name' => 'spo2_room_air',
                            'type' => 'number',
                            'label' => 'SpO2 on Room Air (%)',
                            'unit' => '%',
                        ],
                        [
                            'name' => 'spo2_on_oxygen',
                            'type' => 'number',
                            'label' => 'SpO2 on Oxygen (%)',
                            'unit' => '%',
                        ],
                        [
                            'name' => 'oxygen_flow_rate',
                            'type' => 'text',
                            'label' => 'Oxygen Flow Rate/Device',
                            'placeholder' => 'e.g., 2L nasal prongs',
                        ],
                        [
                            'name' => 'chest_shape',
                            'type' => 'select',
                            'label' => 'Chest Shape',
                            'options' => ['normal', 'barrel_chest', 'pigeon_chest', 'funnel_chest', 'kyphoscoliosis', 'flattened'],
                        ],
                        [
                            'name' => 'chest_expansion',
                            'type' => 'select',
                            'label' => 'Chest Expansion',
                            'options' => ['normal_bilateral', 'reduced_bilateral', 'reduced_right', 'reduced_left'],
                        ],
                        [
                            'name' => 'trachea',
                            'type' => 'select',
                            'label' => 'Trachea Position',
                            'options' => ['central', 'deviated_right', 'deviated_left'],
                        ],
                        [
                            'name' => 'apex_beat',
                            'type' => 'text',
                            'label' => 'Apex Beat',
                        ],
                        [
                            'name' => 'tactile_vocal_fremitus',
                            'type' => 'select',
                            'label' => 'Tactile Vocal Fremitus',
                            'options' => ['normal', 'increased_right', 'increased_left', 'decreased_right', 'decreased_left', 'decreased_bilateral'],
                        ],
                        [
                            'name' => 'percussion_right',
                            'type' => 'select',
                            'label' => 'Percussion (Right)',
                            'options' => ['resonant', 'hyper_resonant', 'dull', 'stony_dull'],
                        ],
                        [
                            'name' => 'percussion_left',
                            'type' => 'select',
                            'label' => 'Percussion (Left)',
                            'options' => ['resonant', 'hyper_resonant', 'dull', 'stony_dull'],
                        ],
                        [
                            'name' => 'percussion_note',
                            'type' => 'textarea',
                            'label' => 'Percussion Details',
                            'placeholder' => 'Zones and findings...',
                        ],
                        [
                            'name' => 'breath_sounds',
                            'type' => 'select',
                            'label' => 'Breath Sounds',
                            'options' => ['vesicular', 'bronchial', 'bronchovesicular', 'diminished', 'absent'],
                        ],
                        [
                            'name' => 'breath_sounds_side',
                            'type' => 'select',
                            'label' => 'Breath Sounds Side',
                            'options' => ['bilateral_normal', 'reduced_right', 'reduced_left', 'reduced_bilateral', 'absent_right', 'absent_left'],
                        ],
                        [
                            'name' => 'added_sounds',
                            'type' => 'multiselect',
                            'label' => 'Added Sounds',
                            'options' => [
                                'wheeze_expiratory', 'wheeze_inspiratory', 'wheeze_bilateral',
                                'crackles_fine', 'crackles_coarse', 'crackles_bibasal',
                                'rhonchi', 'stridor', 'pleural_rub', 'bronchial_breathing',
                            ],
                        ],
                        [
                            'name' => 'vocal_resonance',
                            'type' => 'select',
                            'label' => 'Vocal Resonance',
                            'options' => ['normal', 'increased', 'decreased', 'aegophony', 'whispering_pectoriloquy'],
                        ],
                        [
                            'name' => 'examination_notes',
                            'type' => 'textarea',
                            'label' => 'Additional Examination Notes',
                        ],
                    ],
                ],

                // Spirometry / PFT
                [
                    'id' => 'spirometry',
                    'title' => 'Spirometry / Pulmonary Function Tests',
                    'fields' => [
                        [
                            'name' => 'fev1_actual',
                            'type' => 'number',
                            'label' => 'FEV1 Actual (L)',
                            'unit' => 'L',
                            'step' => 0.01,
                        ],
                        [
                            'name' => 'fev1_predicted',
                            'type' => 'number',
                            'label' => 'FEV1 Predicted (L)',
                            'unit' => 'L',
                            'step' => 0.01,
                        ],
                        [
                            'name' => 'fev1_percent_predicted',
                            'type' => 'number',
                            'label' => 'FEV1 % Predicted',
                            'unit' => '%',
                        ],
                        [
                            'name' => 'fvc_actual',
                            'type' => 'number',
                            'label' => 'FVC Actual (L)',
                            'unit' => 'L',
                            'step' => 0.01,
                        ],
                        [
                            'name' => 'fvc_predicted',
                            'type' => 'number',
                            'label' => 'FVC Predicted (L)',
                            'unit' => 'L',
                            'step' => 0.01,
                        ],
                        [
                            'name' => 'fvc_percent_predicted',
                            'type' => 'number',
                            'label' => 'FVC % Predicted',
                            'unit' => '%',
                        ],
                        [
                            'name' => 'fev1_fvc_ratio',
                            'type' => 'number',
                            'label' => 'FEV1/FVC Ratio',
                            'unit' => '%',
                        ],
                        [
                            'name' => 'pef',
                            'type' => 'number',
                            'label' => 'Peak Expiratory Flow (L/min)',
                            'unit' => 'L/min',
                        ],
                        [
                            'name' => 'fef_25_75',
                            'type' => 'number',
                            'label' => 'FEF 25-75% (L/sec)',
                            'unit' => 'L/sec',
                            'step' => 0.01,
                        ],
                        [
                            'name' => 'dlco',
                            'type' => 'number',
                            'label' => 'DLCO (mL/min/mmHg)',
                            'unit' => 'mL/min/mmHg',
                            'step' => 0.1,
                        ],
                        [
                            'name' => 'reversibility_test',
                            'type' => 'select',
                            'label' => 'Reversibility Test',
                            'options' => ['not_done', 'positive', 'negative'],
                        ],
                        [
                            'name' => 'post_bronchodilator_fev1',
                            'type' => 'number',
                            'label' => 'Post-Bronchodilator FEV1 (L)',
                            'unit' => 'L',
                            'step' => 0.01,
                        ],
                        [
                            'name' => 'reversibility_percent',
                            'type' => 'number',
                            'label' => 'Reversibility %',
                            'unit' => '%',
                        ],
                        [
                            'name' => 'spirometry_interpretation',
                            'type' => 'select',
                            'label' => 'Spirometry Interpretation',
                            'options' => ['normal', 'obstructive', 'restrictive', 'mixed', 'non_specific'],
                        ],
                        [
                            'name' => 'obstruction_severity',
                            'type' => 'select',
                            'label' => 'Obstruction Severity (if obstructive)',
                            'options' => [
                                ['value' => 'mild', 'label' => 'Mild (FEV1 ≥80%)'],
                                ['value' => 'moderate', 'label' => 'Moderate (FEV1 50-79%)'],
                                ['value' => 'severe', 'label' => 'Severe (FEV1 30-49%)'],
                                ['value' => 'very_severe', 'label' => 'Very Severe (FEV1 <30%)'],
                            ],
                        ],
                    ],
                ],

                // ABG Interpretation
                [
                    'id' => 'abg',
                    'title' => 'ABG Interpretation',
                    'fields' => [
                        [
                            'name' => 'abg_ph',
                            'type' => 'number',
                            'label' => 'pH',
                            'step' => 0.01,
                            'placeholder' => '7.35-7.45',
                        ],
                        [
                            'name' => 'abg_pco2',
                            'type' => 'number',
                            'label' => 'pCO2 (mmHg)',
                            'unit' => 'mmHg',
                            'placeholder' => '35-45',
                        ],
                        [
                            'name' => 'abg_po2',
                            'type' => 'number',
                            'label' => 'pO2 (mmHg)',
                            'unit' => 'mmHg',
                            'placeholder' => '80-100',
                        ],
                        [
                            'name' => 'abg_hco3',
                            'type' => 'number',
                            'label' => 'HCO3 (mEq/L)',
                            'unit' => 'mEq/L',
                            'step' => 0.1,
                            'placeholder' => '22-26',
                        ],
                        [
                            'name' => 'abg_spo2',
                            'type' => 'number',
                            'label' => 'SpO2 (%)',
                            'unit' => '%',
                        ],
                        [
                            'name' => 'abg_base_excess',
                            'type' => 'number',
                            'label' => 'Base Excess (mEq/L)',
                            'unit' => 'mEq/L',
                            'step' => 0.1,
                        ],
                        [
                            'name' => 'abg_fio2',
                            'type' => 'number',
                            'label' => 'FiO2 (%)',
                            'unit' => '%',
                        ],
                        [
                            'name' => 'pf_ratio',
                            'type' => 'number',
                            'label' => 'P/F Ratio',
                            'placeholder' => 'PaO2 / FiO2',
                        ],
                        [
                            'name' => 'abg_interpretation',
                            'type' => 'select',
                            'label' => 'ABG Interpretation',
                            'options' => [
                                'normal',
                                'respiratory_acidosis_acute',
                                'respiratory_acidosis_chronic',
                                'respiratory_alkalosis_acute',
                                'respiratory_alkalosis_chronic',
                                'metabolic_acidosis',
                                'metabolic_alkalosis',
                                'mixed_disorder',
                                'compensated_respiratory_acidosis',
                                'compensated_metabolic_acidosis',
                            ],
                        ],
                        [
                            'name' => 'abg_notes',
                            'type' => 'textarea',
                            'label' => 'ABG Notes',
                        ],
                    ],
                ],

                // Scales
                [
                    'id' => 'scales',
                    'title' => 'Clinical Scales & Scoring',
                    'fields' => [
                        [
                            'name' => 'mmrc_dyspnea',
                            'type' => 'select',
                            'label' => 'mMRC Dyspnea Scale',
                            'options' => [
                                ['value' => 0, 'label' => '0 - Breathless with strenuous exercise only'],
                                ['value' => 1, 'label' => '1 - Short of breath when hurrying on level / walking up slight hill'],
                                ['value' => 2, 'label' => '2 - Walks slower than people of same age on level'],
                                ['value' => 3, 'label' => '3 - Stops for breath after walking ~100m on level'],
                                ['value' => 4, 'label' => '4 - Too breathless to leave house / breathless when dressing'],
                            ],
                        ],
                        [
                            'name' => 'cat_score',
                            'type' => 'number',
                            'label' => 'CAT Score (COPD Assessment Test)',
                            'min' => 0,
                            'max' => 40,
                            'placeholder' => '0-40',
                        ],
                        [
                            'name' => 'cat_interpretation',
                            'type' => 'select',
                            'label' => 'CAT Interpretation',
                            'options' => [
                                ['value' => 'low', 'label' => 'Low impact (<10)'],
                                ['value' => 'medium', 'label' => 'Medium impact (10-20)'],
                                ['value' => 'high', 'label' => 'High impact (21-30)'],
                                ['value' => 'very_high', 'label' => 'Very high impact (>30)'],
                            ],
                        ],
                        [
                            'name' => 'gold_stage',
                            'type' => 'select',
                            'label' => 'GOLD Stage (COPD)',
                            'options' => [
                                ['value' => '1', 'label' => 'GOLD 1 - Mild (FEV1 ≥80%)'],
                                ['value' => '2', 'label' => 'GOLD 2 - Moderate (50% ≤ FEV1 < 80%)'],
                                ['value' => '3', 'label' => 'GOLD 3 - Severe (30% ≤ FEV1 < 50%)'],
                                ['value' => '4', 'label' => 'GOLD 4 - Very Severe (FEV1 < 30%)'],
                            ],
                        ],
                        [
                            'name' => 'gold_group',
                            'type' => 'select',
                            'label' => 'GOLD Group (ABE)',
                            'options' => [
                                ['value' => 'A', 'label' => 'A - Low risk, less symptoms'],
                                ['value' => 'B', 'label' => 'B - Low risk, more symptoms'],
                                ['value' => 'E', 'label' => 'E - Exacerbation history ≥2 or ≥1 hospitalization'],
                            ],
                        ],
                        [
                            'name' => 'exacerbation_history',
                            'type' => 'number',
                            'label' => 'Exacerbations in Last 12 Months',
                        ],
                        [
                            'name' => 'act_score',
                            'type' => 'number',
                            'label' => 'ACT Score (Asthma Control Test)',
                            'min' => 5,
                            'max' => 25,
                            'placeholder' => '5-25',
                        ],
                        [
                            'name' => 'act_interpretation',
                            'type' => 'select',
                            'label' => 'ACT Interpretation',
                            'options' => [
                                ['value' => 'well_controlled', 'label' => 'Well controlled (≥20)'],
                                ['value' => 'not_well_controlled', 'label' => 'Not well controlled (16-19)'],
                                ['value' => 'very_poorly_controlled', 'label' => 'Very poorly controlled (≤15)'],
                            ],
                        ],
                        [
                            'name' => 'asthma_severity',
                            'type' => 'select',
                            'label' => 'Asthma Severity (GINA Step)',
                            'options' => [
                                ['value' => 'step1', 'label' => 'Step 1 - Mild intermittent'],
                                ['value' => 'step2', 'label' => 'Step 2 - Mild persistent'],
                                ['value' => 'step3', 'label' => 'Step 3 - Moderate persistent'],
                                ['value' => 'step4', 'label' => 'Step 4 - Severe persistent'],
                                ['value' => 'step5', 'label' => 'Step 5 - Severe uncontrolled'],
                            ],
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
                            'autocomplete' => 'icd10_pulmonology',
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
                            'placeholder' => 'e.g., J44.1',
                        ],
                        [
                            'name' => 'disease_severity',
                            'type' => 'select',
                            'label' => 'Disease Severity',
                            'options' => ['mild', 'moderate', 'severe', 'life_threatening'],
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
                            'name' => 'inhalers_prescribed',
                            'type' => 'multiselect',
                            'label' => 'Inhalers Prescribed',
                            'options' => [
                                'salbutamol_mdi', 'ipratropium_mdi', 'budesonide_mdi',
                                'formoterol_mdi', 'salmeterol_fluticasone_dpi',
                                'budesonide_formoterol_dpi', 'tiotropium_dpi',
                                'umeclidinium_vilanterol', 'glycopyrronium',
                            ],
                        ],
                        [
                            'name' => 'inhaler_technique_checked',
                            'type' => 'boolean',
                            'label' => 'Inhaler Technique Checked',
                        ],
                        [
                            'name' => 'oxygen_therapy',
                            'type' => 'textarea',
                            'label' => 'Oxygen Therapy Details',
                            'placeholder' => 'Flow rate, device, duration, target SpO2...',
                        ],
                        [
                            'name' => 'investigations_ordered',
                            'type' => 'multiselect',
                            'label' => 'Investigations',
                            'options' => [
                                'chest_xray', 'ct_thorax', 'hrct_chest', 'ct_pulmonary_angiography',
                                'pft', 'abg', 'sputum_afb', 'sputum_culture', 'sputum_cbnaat',
                                'cbc', 'crp', 'procalcitonin', 'd_dimer', 'bnp',
                                'pleural_fluid_analysis', 'bronchoscopy', 'biopsy',
                                'mantoux_test', 'igra_test', 'sleep_study',
                            ],
                        ],
                        [
                            'name' => 'smoking_cessation_counselling',
                            'type' => 'boolean',
                            'label' => 'Smoking Cessation Counselling Given',
                        ],
                        [
                            'name' => 'pulmonary_rehab',
                            'type' => 'boolean',
                            'label' => 'Pulmonary Rehabilitation Referred',
                        ],
                        [
                            'name' => 'vaccination_advice',
                            'type' => 'multiselect',
                            'label' => 'Vaccination Advice',
                            'options' => ['influenza', 'pneumococcal', 'covid19'],
                        ],
                        [
                            'name' => 'action_plan_given',
                            'type' => 'boolean',
                            'label' => 'Asthma/COPD Action Plan Given',
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
                        [
                            'name' => 'follow_up_notes',
                            'type' => 'textarea',
                            'label' => 'Follow-up Instructions',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Get common pulmonology diagnoses for autocomplete.
     */
    public static function getCommonDiagnoses(): array
    {
        Log::info('PulmonologyTemplate::getCommonDiagnoses() - returning ICD-10 codes');

        return [
            ['code' => 'J45.20', 'name' => 'Mild intermittent asthma, uncomplicated'],
            ['code' => 'J45.30', 'name' => 'Mild persistent asthma, uncomplicated'],
            ['code' => 'J45.40', 'name' => 'Moderate persistent asthma, uncomplicated'],
            ['code' => 'J45.50', 'name' => 'Severe persistent asthma, uncomplicated'],
            ['code' => 'J45.901', 'name' => 'Asthma, acute exacerbation'],
            ['code' => 'J44.0', 'name' => 'COPD with acute lower respiratory infection'],
            ['code' => 'J44.1', 'name' => 'COPD with acute exacerbation'],
            ['code' => 'J44.9', 'name' => 'COPD, unspecified'],
            ['code' => 'J18.9', 'name' => 'Pneumonia, unspecified organism'],
            ['code' => 'J18.1', 'name' => 'Lobar pneumonia, unspecified organism'],
            ['code' => 'J13', 'name' => 'Pneumonia due to Streptococcus pneumoniae'],
            ['code' => 'J15.1', 'name' => 'Pneumonia due to Pseudomonas'],
            ['code' => 'J90', 'name' => 'Pleural effusion, not elsewhere classified'],
            ['code' => 'J93.0', 'name' => 'Spontaneous tension pneumothorax'],
            ['code' => 'J93.1', 'name' => 'Other spontaneous pneumothorax'],
            ['code' => 'J84.1', 'name' => 'Idiopathic pulmonary fibrosis'],
            ['code' => 'J84.9', 'name' => 'Interstitial pulmonary disease, unspecified'],
            ['code' => 'A15.0', 'name' => 'Pulmonary tuberculosis, confirmed by sputum microscopy'],
            ['code' => 'A15.9', 'name' => 'Respiratory tuberculosis, unspecified'],
            ['code' => 'J47.0', 'name' => 'Bronchiectasis with acute lower respiratory infection'],
            ['code' => 'J47.9', 'name' => 'Bronchiectasis, uncomplicated'],
            ['code' => 'D86.0', 'name' => 'Sarcoidosis of lung'],
            ['code' => 'D86.9', 'name' => 'Sarcoidosis, unspecified'],
            ['code' => 'C34.90', 'name' => 'Malignant neoplasm of unspecified part of bronchus or lung'],
            ['code' => 'J96.00', 'name' => 'Acute respiratory failure, unspecified'],
            ['code' => 'J96.10', 'name' => 'Chronic respiratory failure, unspecified'],
            ['code' => 'J96.20', 'name' => 'Acute and chronic respiratory failure, unspecified'],
            ['code' => 'J80', 'name' => 'Acute respiratory distress syndrome (ARDS)'],
            ['code' => 'I26.99', 'name' => 'Pulmonary embolism without acute cor pulmonale'],
            ['code' => 'G47.33', 'name' => 'Obstructive sleep apnoea'],
        ];
    }
}
