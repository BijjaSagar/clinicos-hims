<?php

namespace App\Services\EmrTemplates;

use Illuminate\Support\Facades\Log;

class PaediatricsTemplate
{
    /**
     * Get the complete field schema for the Paediatrics specialty EMR template.
     */
    public static function getFields(): array
    {
        Log::info('Loading Paediatrics EMR template');
        Log::info('PaediatricsTemplate::getFields() - Building sections for paediatrics specialty');

        return [
            'specialty' => 'paediatrics',
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
                            'placeholder' => 'e.g., 3 days, 1 week',
                        ],
                        [
                            'name' => 'age',
                            'type' => 'text',
                            'label' => 'Age',
                            'placeholder' => 'e.g., 2 years 4 months, 6 months',
                        ],
                        [
                            'name' => 'informant',
                            'type' => 'select',
                            'label' => 'History Given By',
                            'options' => ['Mother', 'Father', 'Guardian', 'Self (adolescent)', 'Other'],
                        ],
                    ],
                ],

                // Birth History
                [
                    'id' => 'birth_history',
                    'title' => 'Birth History',
                    'fields' => [
                        [
                            'name' => 'gestational_age',
                            'type' => 'text',
                            'label' => 'Gestational Age at Birth',
                            'placeholder' => 'e.g., 38 weeks, 34+2 weeks',
                        ],
                        [
                            'name' => 'term_status',
                            'type' => 'select',
                            'label' => 'Term Status',
                            'options' => ['Full term (37-42 weeks)', 'Late preterm (34-36 weeks)', 'Preterm (<34 weeks)', 'Very preterm (<28 weeks)', 'Post-term (>42 weeks)'],
                        ],
                        [
                            'name' => 'birth_weight',
                            'type' => 'number',
                            'label' => 'Birth Weight (kg)',
                            'step' => 0.01,
                        ],
                        [
                            'name' => 'birth_weight_category',
                            'type' => 'select',
                            'label' => 'Birth Weight Category',
                            'options' => ['Normal (≥2.5 kg)', 'LBW (1.5-2.49 kg)', 'VLBW (1.0-1.49 kg)', 'ELBW (<1.0 kg)', 'Macrosomia (>4 kg)'],
                        ],
                        [
                            'name' => 'delivery_type',
                            'type' => 'select',
                            'label' => 'Type of Delivery',
                            'options' => ['Normal vaginal delivery', 'Assisted vaginal (vacuum)', 'Assisted vaginal (forceps)', 'Elective LSCS', 'Emergency LSCS'],
                        ],
                        [
                            'name' => 'birth_cry',
                            'type' => 'select',
                            'label' => 'Birth Cry',
                            'options' => ['Immediate', 'Delayed', 'Absent / Resuscitated'],
                        ],
                        [
                            'name' => 'apgar_1min',
                            'type' => 'number',
                            'label' => 'APGAR Score - 1 min',
                            'min' => 0,
                            'max' => 10,
                        ],
                        [
                            'name' => 'apgar_5min',
                            'type' => 'number',
                            'label' => 'APGAR Score - 5 min',
                            'min' => 0,
                            'max' => 10,
                        ],
                        [
                            'name' => 'nicu_stay',
                            'type' => 'select',
                            'label' => 'NICU Stay',
                            'options' => ['No', 'Yes - <48 hours', 'Yes - 2-7 days', 'Yes - >7 days', 'Yes - >28 days'],
                        ],
                        [
                            'name' => 'nicu_reason',
                            'type' => 'textarea',
                            'label' => 'Reason for NICU',
                            'placeholder' => 'Prematurity, RDS, jaundice, sepsis, HIE...',
                        ],
                        [
                            'name' => 'perinatal_complications',
                            'type' => 'textarea',
                            'label' => 'Perinatal Complications',
                            'placeholder' => 'Neonatal jaundice, meconium aspiration, birth asphyxia...',
                        ],
                    ],
                ],

                // Feeding History
                [
                    'id' => 'feeding_history',
                    'title' => 'Feeding History',
                    'fields' => [
                        [
                            'name' => 'feeding_type',
                            'type' => 'select',
                            'label' => 'Current Feeding',
                            'options' => ['Exclusive breastfeeding', 'Mixed feeding', 'Formula feeding', 'Complementary foods started', 'Family diet'],
                        ],
                        [
                            'name' => 'breastfeeding_duration',
                            'type' => 'text',
                            'label' => 'Breastfeeding Duration',
                            'placeholder' => 'e.g., Ongoing, 6 months, Never',
                        ],
                        [
                            'name' => 'formula_details',
                            'type' => 'text',
                            'label' => 'Formula Details',
                            'placeholder' => 'Brand, dilution, frequency',
                        ],
                        [
                            'name' => 'complementary_foods',
                            'type' => 'textarea',
                            'label' => 'Complementary Foods',
                            'placeholder' => 'Age started, types of foods, frequency, texture...',
                        ],
                        [
                            'name' => 'appetite',
                            'type' => 'select',
                            'label' => 'Appetite',
                            'options' => ['Good', 'Decreased', 'Poor', 'Picky eater', 'Increased'],
                        ],
                        [
                            'name' => 'dietary_concerns',
                            'type' => 'textarea',
                            'label' => 'Dietary Concerns',
                            'placeholder' => 'Food allergies, intolerances, aversions...',
                        ],
                    ],
                ],

                // Developmental History
                [
                    'id' => 'developmental_history',
                    'title' => 'Developmental History',
                    'fields' => [
                        [
                            'name' => 'gross_motor',
                            'type' => 'textarea',
                            'label' => 'Gross Motor Milestones',
                            'placeholder' => 'Head holding (3m), Sitting (6m), Standing (9m), Walking (12m), Running (18m)...',
                        ],
                        [
                            'name' => 'fine_motor',
                            'type' => 'textarea',
                            'label' => 'Fine Motor Milestones',
                            'placeholder' => 'Palmar grasp (4m), Transfer (6m), Pincer (9m), Scribbles (15m), Tower of 2 (15m)...',
                        ],
                        [
                            'name' => 'language',
                            'type' => 'textarea',
                            'label' => 'Language Milestones',
                            'placeholder' => 'Cooing (2m), Babbling (6m), First words (12m), 2-word sentences (2y)...',
                        ],
                        [
                            'name' => 'social',
                            'type' => 'textarea',
                            'label' => 'Social Milestones',
                            'placeholder' => 'Social smile (2m), Stranger anxiety (8m), Parallel play (2y), Cooperative play (3y)...',
                        ],
                        [
                            'name' => 'developmental_concern',
                            'type' => 'select',
                            'label' => 'Developmental Concern',
                            'options' => ['Age appropriate', 'Mild delay', 'Moderate delay', 'Severe delay', 'Regression noted'],
                        ],
                        [
                            'name' => 'developmental_screening',
                            'type' => 'text',
                            'label' => 'Screening Tool Used',
                            'placeholder' => 'e.g., Denver II, ASQ-3, M-CHAT-R',
                        ],
                    ],
                ],

                // Growth Parameters
                [
                    'id' => 'growth_parameters',
                    'title' => 'Growth Parameters',
                    'fields' => [
                        [
                            'name' => 'weight_kg',
                            'type' => 'number',
                            'label' => 'Weight (kg)',
                            'step' => 0.01,
                            'required' => true,
                        ],
                        [
                            'name' => 'height_cm',
                            'type' => 'number',
                            'label' => 'Height / Length (cm)',
                            'step' => 0.1,
                        ],
                        [
                            'name' => 'head_circumference_cm',
                            'type' => 'number',
                            'label' => 'Head Circumference (cm)',
                            'step' => 0.1,
                            'help' => 'Routinely measured until 2 years',
                        ],
                        [
                            'name' => 'mid_arm_circumference',
                            'type' => 'number',
                            'label' => 'Mid-Upper Arm Circumference - MUAC (cm)',
                            'step' => 0.1,
                            'help' => 'Red <11.5, Yellow 11.5-12.5, Green >12.5',
                        ],
                        [
                            'name' => 'weight_for_age_percentile',
                            'type' => 'text',
                            'label' => 'Weight-for-Age Percentile',
                            'placeholder' => 'e.g., 50th, <3rd, >97th',
                        ],
                        [
                            'name' => 'height_for_age_percentile',
                            'type' => 'text',
                            'label' => 'Height-for-Age Percentile',
                            'placeholder' => 'e.g., 25th, <3rd (stunted)',
                        ],
                        [
                            'name' => 'bmi_for_age_percentile',
                            'type' => 'text',
                            'label' => 'BMI-for-Age Percentile',
                            'placeholder' => 'e.g., 85th (overweight), 95th (obese)',
                        ],
                        [
                            'name' => 'weight_for_height',
                            'type' => 'text',
                            'label' => 'Weight-for-Height (Z-score)',
                            'placeholder' => 'e.g., -2 SD (wasted), +2 SD (overweight)',
                        ],
                        [
                            'name' => 'growth_chart',
                            'type' => 'select',
                            'label' => 'Growth Chart Reference',
                            'options' => ['WHO (0-5 years)', 'WHO (5-19 years)', 'IAP', 'CDC'],
                        ],
                        [
                            'name' => 'growth_trend',
                            'type' => 'select',
                            'label' => 'Growth Trend',
                            'options' => ['Following curve', 'Crossing percentiles upward', 'Crossing percentiles downward', 'Catch-up growth', 'Growth faltering'],
                        ],
                    ],
                ],

                // Immunization Status
                [
                    'id' => 'immunization',
                    'title' => 'Immunization Status',
                    'type' => 'vaccine_checklist',
                    'fields' => [
                        [
                            'name' => 'bcg',
                            'type' => 'vaccine_entry',
                            'label' => 'BCG',
                            'schedule' => 'At birth',
                            'fields' => [
                                ['name' => 'bcg_given', 'type' => 'boolean', 'label' => 'Given'],
                                ['name' => 'bcg_date', 'type' => 'date', 'label' => 'Date'],
                                ['name' => 'bcg_scar', 'type' => 'boolean', 'label' => 'Scar formed'],
                            ],
                        ],
                        [
                            'name' => 'opv',
                            'type' => 'vaccine_entry',
                            'label' => 'OPV (Oral Polio Vaccine)',
                            'schedule' => 'Birth, 6w, 10w, 14w, booster',
                            'doses' => ['OPV-0', 'OPV-1', 'OPV-2', 'OPV-3', 'OPV Booster'],
                        ],
                        [
                            'name' => 'ipv',
                            'type' => 'vaccine_entry',
                            'label' => 'IPV (Inactivated Polio Vaccine)',
                            'schedule' => '6w, 14w, booster',
                            'doses' => ['IPV-1', 'IPV-2', 'IPV Booster'],
                        ],
                        [
                            'name' => 'pentavalent',
                            'type' => 'vaccine_entry',
                            'label' => 'Pentavalent (DPT+HepB+Hib)',
                            'schedule' => '6w, 10w, 14w',
                            'doses' => ['Penta-1', 'Penta-2', 'Penta-3'],
                        ],
                        [
                            'name' => 'rotavirus',
                            'type' => 'vaccine_entry',
                            'label' => 'Rotavirus',
                            'schedule' => '6w, 10w, 14w',
                            'doses' => ['Rota-1', 'Rota-2', 'Rota-3'],
                        ],
                        [
                            'name' => 'pcv',
                            'type' => 'vaccine_entry',
                            'label' => 'PCV (Pneumococcal Conjugate)',
                            'schedule' => '6w, 14w, 9m booster',
                            'doses' => ['PCV-1', 'PCV-2', 'PCV Booster'],
                        ],
                        [
                            'name' => 'mmr',
                            'type' => 'vaccine_entry',
                            'label' => 'MMR',
                            'schedule' => '9-12m, 4-6y booster',
                            'doses' => ['MMR-1', 'MMR-2'],
                        ],
                        [
                            'name' => 'varicella',
                            'type' => 'vaccine_entry',
                            'label' => 'Varicella',
                            'schedule' => '15m, 4-6y booster',
                            'doses' => ['Varicella-1', 'Varicella-2'],
                        ],
                        [
                            'name' => 'hepatitis_a',
                            'type' => 'vaccine_entry',
                            'label' => 'Hepatitis A',
                            'schedule' => '12m, booster 6m later',
                            'doses' => ['HepA-1', 'HepA-2'],
                        ],
                        [
                            'name' => 'hepatitis_b',
                            'type' => 'vaccine_entry',
                            'label' => 'Hepatitis B',
                            'schedule' => 'Birth, 6w, 14w',
                            'doses' => ['HepB-Birth', 'HepB-1', 'HepB-2'],
                        ],
                        [
                            'name' => 'typhoid',
                            'type' => 'vaccine_entry',
                            'label' => 'Typhoid',
                            'schedule' => '9-12m, revaccination every 3y',
                            'doses' => ['Typhoid-1', 'Typhoid Revaccination'],
                        ],
                        [
                            'name' => 'hpv',
                            'type' => 'vaccine_entry',
                            'label' => 'HPV',
                            'schedule' => '9-14y (2 doses), >15y (3 doses)',
                            'doses' => ['HPV-1', 'HPV-2', 'HPV-3'],
                        ],
                        [
                            'name' => 'influenza',
                            'type' => 'vaccine_entry',
                            'label' => 'Influenza',
                            'schedule' => 'Annual from 6 months',
                            'doses' => ['Flu dose 1', 'Flu dose 2', 'Annual'],
                        ],
                        [
                            'name' => 'immunization_notes',
                            'type' => 'textarea',
                            'label' => 'Immunization Notes',
                            'placeholder' => 'Catch-up schedule, contraindications, adverse events...',
                        ],
                    ],
                ],

                // Examination
                [
                    'id' => 'examination',
                    'title' => 'Examination',
                    'fields' => [
                        [
                            'name' => 'general',
                            'type' => 'textarea',
                            'label' => 'General Appearance',
                            'placeholder' => 'Active, alert, playful, irritable, lethargic, pallor, jaundice, cyanosis, oedema...',
                        ],
                        [
                            'name' => 'vitals_temp',
                            'type' => 'number',
                            'label' => 'Temperature (°F)',
                            'step' => 0.1,
                        ],
                        [
                            'name' => 'vitals_hr',
                            'type' => 'number',
                            'label' => 'Heart Rate (bpm)',
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
                            'name' => 'head',
                            'type' => 'textarea',
                            'label' => 'Head',
                            'placeholder' => 'Anterior fontanelle (open/closed/bulging/sunken), sutures, shape...',
                        ],
                        [
                            'name' => 'ent',
                            'type' => 'textarea',
                            'label' => 'ENT',
                            'placeholder' => 'Ears, throat, tonsils, nose, oral cavity, teeth...',
                        ],
                        [
                            'name' => 'chest',
                            'type' => 'textarea',
                            'label' => 'Chest / Respiratory',
                            'placeholder' => 'Air entry, adventitious sounds, retractions, grunting...',
                        ],
                        [
                            'name' => 'cvs',
                            'type' => 'textarea',
                            'label' => 'Cardiovascular System',
                            'placeholder' => 'Heart sounds, murmurs, pulses, CRT...',
                        ],
                        [
                            'name' => 'abdomen',
                            'type' => 'textarea',
                            'label' => 'Abdomen',
                            'placeholder' => 'Soft, distended, hepatomegaly, splenomegaly, hernias...',
                        ],
                        [
                            'name' => 'cns',
                            'type' => 'textarea',
                            'label' => 'Central Nervous System',
                            'placeholder' => 'Tone, reflexes, cranial nerves, seizures, GCS...',
                        ],
                        [
                            'name' => 'skin',
                            'type' => 'textarea',
                            'label' => 'Skin',
                            'placeholder' => 'Rash, birthmarks, turgor, capillary refill...',
                        ],
                        [
                            'name' => 'lymph_nodes',
                            'type' => 'textarea',
                            'label' => 'Lymph Nodes',
                            'placeholder' => 'Cervical, axillary, inguinal...',
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
                            'autocomplete' => 'icd10_paediatrics',
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
                            'placeholder' => 'e.g., J06.9',
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
                            'placeholder' => 'Medications, fluids, supportive care...',
                        ],
                        [
                            'name' => 'investigations',
                            'type' => 'multiselect',
                            'label' => 'Investigations',
                            'options' => [
                                'cbc', 'crp', 'blood_culture', 'urine_routine',
                                'urine_culture', 'stool_routine', 'electrolytes',
                                'blood_sugar', 'lft', 'kft', 'thyroid',
                                'chest_xray', 'usg_abdomen', 'echocardiography',
                                'eeg', 'mri_brain', 'lumbar_puncture',
                                'iron_studies', 'vitamin_d', 'allergy_panel',
                            ],
                        ],
                        [
                            'name' => 'diet_advice',
                            'type' => 'textarea',
                            'label' => 'Diet / Feeding Advice',
                            'placeholder' => 'Breastfeeding advice, ORS, weaning, calorie dense foods...',
                        ],
                        [
                            'name' => 'danger_signs',
                            'type' => 'textarea',
                            'label' => 'Danger Signs Counselled',
                            'placeholder' => 'When to bring child immediately: not feeding, convulsions, lethargy, fast breathing...',
                        ],
                        [
                            'name' => 'referral',
                            'type' => 'text',
                            'label' => 'Referral To',
                            'placeholder' => 'Paediatric surgeon, cardiologist, neurologist...',
                        ],
                        [
                            'name' => 'followup_in_days',
                            'type' => 'select',
                            'label' => 'Follow-up',
                            'options' => [
                                ['value' => 2, 'label' => '2 Days'],
                                ['value' => 3, 'label' => '3 Days'],
                                ['value' => 5, 'label' => '5 Days'],
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
                        [
                            'name' => 'parent_education',
                            'type' => 'textarea',
                            'label' => 'Parent Education Notes',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Get common paediatric diagnoses for autocomplete (ICD-10).
     */
    public static function getCommonDiagnoses(): array
    {
        Log::info('PaediatricsTemplate::getCommonDiagnoses() - Returning common paediatric ICD-10 codes');

        return [
            ['code' => 'J06.9', 'name' => 'Acute upper respiratory tract infection (URTI)'],
            ['code' => 'J18', 'name' => 'Pneumonia, organism unspecified'],
            ['code' => 'J18.9', 'name' => 'Pneumonia, unspecified'],
            ['code' => 'A09', 'name' => 'Gastroenteritis and colitis (infectious)'],
            ['code' => 'A09.0', 'name' => 'Infectious gastroenteritis'],
            ['code' => 'J45', 'name' => 'Childhood asthma'],
            ['code' => 'J45.9', 'name' => 'Asthma, unspecified'],
            ['code' => 'L20', 'name' => 'Atopic dermatitis (eczema)'],
            ['code' => 'L20.9', 'name' => 'Atopic dermatitis, unspecified'],
            ['code' => 'J03', 'name' => 'Acute tonsillitis'],
            ['code' => 'J03.9', 'name' => 'Acute tonsillitis, unspecified'],
            ['code' => 'H66', 'name' => 'Suppurative and unspecified otitis media'],
            ['code' => 'H66.9', 'name' => 'Otitis media, unspecified'],
            ['code' => 'E46', 'name' => 'Protein-energy malnutrition, unspecified'],
            ['code' => 'D50', 'name' => 'Iron deficiency anaemia'],
            ['code' => 'D50.9', 'name' => 'Iron deficiency anaemia, unspecified'],
            ['code' => 'E40', 'name' => 'Kwashiorkor'],
            ['code' => 'R62', 'name' => 'Lack of expected normal physiological development (growth faltering)'],
            ['code' => 'R62.0', 'name' => 'Delayed milestone'],
            ['code' => 'J21', 'name' => 'Acute bronchiolitis'],
            ['code' => 'J20', 'name' => 'Acute bronchitis'],
            ['code' => 'B05', 'name' => 'Measles'],
            ['code' => 'A38', 'name' => 'Scarlet fever'],
            ['code' => 'R56', 'name' => 'Febrile convulsions'],
            ['code' => 'E55', 'name' => 'Vitamin D deficiency'],
            ['code' => 'K21', 'name' => 'Gastro-oesophageal reflux disease'],
        ];
    }
}
