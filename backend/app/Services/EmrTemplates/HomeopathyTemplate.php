<?php

namespace App\Services\EmrTemplates;

use Illuminate\Support\Facades\Log;

class HomeopathyTemplate
{
    /**
     * Get Homeopathy EMR template fields.
     */
    public static function getFields(): array
    {
        Log::info('Loading Homeopathy EMR template');
        Log::info('HomeopathyTemplate::getFields() - building case-taking and repertorization sections');

        return [
            'specialty' => 'homeopathy',
            'sections' => [
                // Chief Complaint (with modalities)
                [
                    'id' => 'chief_complaint',
                    'title' => 'Chief Complaint',
                    'fields' => [
                        [
                            'name' => 'chief_complaint',
                            'type' => 'textarea',
                            'label' => 'Chief Complaint',
                            'required' => true,
                            'placeholder' => 'Main presenting complaint in patient\'s own words...',
                        ],
                        [
                            'name' => 'duration',
                            'type' => 'text',
                            'label' => 'Duration',
                            'placeholder' => 'e.g., 3 months, 2 years',
                        ],
                        [
                            'name' => 'modalities_better',
                            'type' => 'tags',
                            'label' => 'Modalities - Better (Amelioration)',
                            'suggestions' => [
                                'rest', 'warmth', 'cold_application', 'pressure', 'motion',
                                'open_air', 'eating', 'lying_down', 'bending_double',
                                'warm_drinks', 'cold_drinks', 'company', 'solitude',
                            ],
                        ],
                        [
                            'name' => 'modalities_worse',
                            'type' => 'tags',
                            'label' => 'Modalities - Worse (Aggravation)',
                            'suggestions' => [
                                'morning', 'evening', 'night', 'cold', 'heat', 'damp',
                                'motion', 'rest', 'touch', 'pressure', 'after_eating',
                                'before_menses', 'during_menses', 'exertion', 'fasting',
                            ],
                        ],
                        [
                            'name' => 'onset',
                            'type' => 'select',
                            'label' => 'Onset',
                            'options' => ['sudden', 'gradual', 'insidious', 'recurrent', 'after_suppression'],
                        ],
                        [
                            'name' => 'causation',
                            'type' => 'text',
                            'label' => 'Causation (Exciting Cause)',
                            'placeholder' => 'e.g., grief, fright, injury, suppression...',
                        ],
                    ],
                ],

                // Case Taking (CLAMS)
                [
                    'id' => 'case_taking',
                    'title' => 'Case Taking (CLAMS)',
                    'fields' => [
                        [
                            'name' => 'character',
                            'type' => 'textarea',
                            'label' => 'Character (Nature of complaint)',
                            'placeholder' => 'Type of pain, sensation, appearance...',
                        ],
                        [
                            'name' => 'location',
                            'type' => 'textarea',
                            'label' => 'Location',
                            'placeholder' => 'Exact location, radiation, extension...',
                        ],
                        [
                            'name' => 'accompaniments',
                            'type' => 'textarea',
                            'label' => 'Accompaniments (Concomitants)',
                            'placeholder' => 'Symptoms that appear along with the chief complaint...',
                        ],
                        [
                            'name' => 'modalities_detailed',
                            'type' => 'textarea',
                            'label' => 'Modalities (Detailed)',
                            'placeholder' => 'What makes it better/worse — time, weather, position, food, emotion...',
                        ],
                        [
                            'name' => 'sensation',
                            'type' => 'multiselect',
                            'label' => 'Sensation',
                            'options' => [
                                'burning', 'stitching', 'throbbing', 'pressing', 'cutting',
                                'tearing', 'drawing', 'boring', 'cramping', 'soreness',
                                'rawness', 'numbness', 'tingling', 'heaviness', 'emptiness',
                                'constricting', 'bursting', 'pulsating', 'shooting',
                            ],
                        ],
                        [
                            'name' => 'laterality',
                            'type' => 'select',
                            'label' => 'Laterality',
                            'options' => ['left', 'right', 'bilateral', 'alternating', 'left_to_right', 'right_to_left'],
                        ],
                        [
                            'name' => 'periodicity',
                            'type' => 'text',
                            'label' => 'Periodicity',
                            'placeholder' => 'e.g., every 7 days, at 4 PM daily, with full moon...',
                        ],
                    ],
                ],

                // Constitution Assessment
                [
                    'id' => 'constitution',
                    'title' => 'Constitution Assessment',
                    'fields' => [
                        [
                            'name' => 'thermal_state',
                            'type' => 'select',
                            'label' => 'Thermal State',
                            'required' => true,
                            'options' => ['hot_patient', 'chilly_patient', 'ambithermal'],
                        ],
                        [
                            'name' => 'thirst',
                            'type' => 'select',
                            'label' => 'Thirst',
                            'options' => ['thirsty_large_quantity', 'thirsty_small_sips', 'thirstless', 'normal', 'unquenchable'],
                        ],
                        [
                            'name' => 'appetite',
                            'type' => 'select',
                            'label' => 'Appetite',
                            'options' => ['increased', 'decreased', 'normal', 'variable', 'ravenous', 'loss_of_appetite'],
                        ],
                        [
                            'name' => 'food_desires',
                            'type' => 'tags',
                            'label' => 'Food Desires',
                            'suggestions' => [
                                'sweets', 'salt', 'sour', 'spicy', 'fatty', 'cold_food',
                                'warm_food', 'eggs', 'milk', 'meat', 'fruits', 'ice',
                                'chocolate', 'raw_food', 'bread', 'pickles',
                            ],
                        ],
                        [
                            'name' => 'food_aversions',
                            'type' => 'tags',
                            'label' => 'Food Aversions',
                            'suggestions' => [
                                'sweets', 'salt', 'fat', 'milk', 'meat', 'bread',
                                'eggs', 'fish', 'warm_food', 'cold_food', 'fruits',
                            ],
                        ],
                        [
                            'name' => 'food_aggravates',
                            'type' => 'tags',
                            'label' => 'Food That Aggravates',
                        ],
                        [
                            'name' => 'perspiration',
                            'type' => 'select',
                            'label' => 'Perspiration',
                            'options' => ['profuse', 'scanty', 'absent', 'normal', 'offensive', 'staining', 'cold_sweat', 'one_sided'],
                        ],
                        [
                            'name' => 'perspiration_location',
                            'type' => 'multiselect',
                            'label' => 'Perspiration Location',
                            'options' => ['head', 'palms', 'soles', 'axillae', 'back', 'chest', 'generalized', 'uncovered_parts'],
                        ],
                        [
                            'name' => 'sleep_position',
                            'type' => 'select',
                            'label' => 'Sleep Position',
                            'options' => ['on_back', 'on_abdomen', 'on_left', 'on_right', 'knee_chest', 'sitting', 'variable'],
                        ],
                        [
                            'name' => 'sleep_quality',
                            'type' => 'select',
                            'label' => 'Sleep Quality',
                            'options' => ['sound', 'light', 'restless', 'unrefreshing', 'disturbed', 'sleepless'],
                        ],
                        [
                            'name' => 'dreams',
                            'type' => 'tags',
                            'label' => 'Dreams',
                            'suggestions' => [
                                'falling', 'flying', 'dead_persons', 'snakes', 'fire',
                                'water', 'animals', 'pleasant', 'frightful', 'of_daily_work',
                                'amorous', 'death', 'teeth_falling', 'being_chased',
                            ],
                        ],
                    ],
                ],

                // Mental / Emotional
                [
                    'id' => 'mental_emotional',
                    'title' => 'Mental / Emotional',
                    'fields' => [
                        [
                            'name' => 'temperament',
                            'type' => 'select',
                            'label' => 'Temperament',
                            'options' => [
                                'sanguine', 'choleric', 'melancholic', 'phlegmatic',
                                'nervous', 'bilious', 'lymphatic',
                            ],
                        ],
                        [
                            'name' => 'fears',
                            'type' => 'multiselect',
                            'label' => 'Fears',
                            'options' => [
                                'death', 'disease', 'darkness', 'thunderstorm', 'animals',
                                'being_alone', 'crowd', 'heights', 'narrow_places', 'future',
                                'poverty', 'ghosts', 'insanity', 'failure', 'robbers',
                            ],
                        ],
                        [
                            'name' => 'anxieties',
                            'type' => 'multiselect',
                            'label' => 'Anxieties',
                            'options' => [
                                'health', 'future', 'family', 'business', 'salvation',
                                'trifles', 'anticipatory', 'performance', 'generalized',
                            ],
                        ],
                        [
                            'name' => 'irritability',
                            'type' => 'select',
                            'label' => 'Irritability',
                            'options' => ['none', 'mild', 'moderate', 'marked', 'violent'],
                        ],
                        [
                            'name' => 'irritability_triggers',
                            'type' => 'tags',
                            'label' => 'Irritability Triggers',
                        ],
                        [
                            'name' => 'sadness',
                            'type' => 'select',
                            'label' => 'Sadness',
                            'options' => ['none', 'mild', 'moderate', 'marked', 'with_weeping', 'silent_grief'],
                        ],
                        [
                            'name' => 'sensitivity',
                            'type' => 'multiselect',
                            'label' => 'Sensitivity',
                            'options' => [
                                'noise', 'light', 'music', 'odors', 'touch', 'pain',
                                'criticism', 'contradiction', 'injustice', 'reprimand',
                            ],
                        ],
                        [
                            'name' => 'social_behavior',
                            'type' => 'select',
                            'label' => 'Social Behavior',
                            'options' => [
                                'social', 'reserved', 'wants_company', 'wants_solitude',
                                'indifferent', 'sympathetic', 'jealous', 'suspicious',
                            ],
                        ],
                        [
                            'name' => 'concentration',
                            'type' => 'select',
                            'label' => 'Concentration',
                            'options' => ['good', 'poor', 'variable', 'absent_minded', 'difficulty_focusing'],
                        ],
                        [
                            'name' => 'weeping',
                            'type' => 'select',
                            'label' => 'Weeping Tendency',
                            'options' => ['none', 'easy', 'from_consolation', 'better_from_consolation', 'worse_from_consolation', 'in_solitude'],
                        ],
                        [
                            'name' => 'mental_generals_notes',
                            'type' => 'textarea',
                            'label' => 'Mental Generals Notes',
                        ],
                    ],
                ],

                // Past History & Family History
                [
                    'id' => 'past_family_history',
                    'title' => 'Past History & Family History',
                    'fields' => [
                        [
                            'name' => 'past_illnesses',
                            'type' => 'textarea',
                            'label' => 'Past Illnesses',
                            'placeholder' => 'Childhood diseases, major illnesses, hospitalizations...',
                        ],
                        [
                            'name' => 'past_treatments',
                            'type' => 'textarea',
                            'label' => 'Past Treatments',
                            'placeholder' => 'Allopathic, Homeopathic, Ayurvedic treatments taken...',
                        ],
                        [
                            'name' => 'suppression_history',
                            'type' => 'textarea',
                            'label' => 'History of Suppressions',
                            'placeholder' => 'Skin eruptions suppressed, discharges suppressed...',
                        ],
                        [
                            'name' => 'vaccination_history',
                            'type' => 'textarea',
                            'label' => 'Vaccination History',
                        ],
                        [
                            'name' => 'family_history',
                            'type' => 'multiselect',
                            'label' => 'Family History (Diseases)',
                            'options' => [
                                'tuberculosis', 'diabetes', 'hypertension', 'cancer',
                                'asthma', 'skin_diseases', 'mental_illness', 'epilepsy',
                                'rheumatic', 'thyroid', 'kidney_disease', 'cardiac',
                            ],
                        ],
                        [
                            'name' => 'family_history_details',
                            'type' => 'textarea',
                            'label' => 'Family History Details',
                        ],
                    ],
                ],

                // Miasmatic Assessment
                [
                    'id' => 'miasmatic_assessment',
                    'title' => 'Miasmatic Assessment',
                    'fields' => [
                        [
                            'name' => 'psora_features',
                            'type' => 'multiselect',
                            'label' => 'Psora Features',
                            'options' => [
                                'itching', 'burning', 'functional_disorders', 'anxiety',
                                'restlessness', 'skin_eruptions', 'alternating_complaints',
                                'periodicity', 'deficiency', 'hypersensitivity',
                            ],
                        ],
                        [
                            'name' => 'sycosis_features',
                            'type' => 'multiselect',
                            'label' => 'Sycosis Features',
                            'options' => [
                                'warts', 'growths', 'tumors', 'overgrowth', 'excess',
                                'infiltration', 'thickening', 'catarrh', 'secretive',
                                'suspicious', 'jealousy', 'fixed_ideas',
                            ],
                        ],
                        [
                            'name' => 'syphilis_features',
                            'type' => 'multiselect',
                            'label' => 'Syphilis (Destruction) Features',
                            'options' => [
                                'ulceration', 'destruction', 'deformity', 'necrosis',
                                'deep_fissures', 'hemorrhage', 'suicidal_tendency',
                                'hopelessness', 'nihilism', 'malignancy',
                            ],
                        ],
                        [
                            'name' => 'predominant_miasm',
                            'type' => 'select',
                            'label' => 'Predominant Miasm',
                            'options' => [
                                'psora', 'sycosis', 'syphilis',
                                'psora_sycosis', 'psora_syphilis', 'sycosis_syphilis',
                                'tri_miasmatic',
                            ],
                        ],
                        [
                            'name' => 'miasmatic_notes',
                            'type' => 'textarea',
                            'label' => 'Miasmatic Analysis Notes',
                        ],
                    ],
                ],

                // Repertorization
                [
                    'id' => 'repertorization',
                    'title' => 'Repertorization',
                    'fields' => [
                        [
                            'name' => 'rubrics_selected',
                            'type' => 'textarea',
                            'label' => 'Rubrics Selected',
                            'placeholder' => 'List selected rubrics with chapter references...',
                        ],
                        [
                            'name' => 'repertory_used',
                            'type' => 'select',
                            'label' => 'Repertory Used',
                            'options' => [
                                'kent_repertory', 'boger_boenninghausen',
                                'synthesis', 'complete_repertory',
                                'murphy_repertory', 'phatak_repertory',
                                'boericke_repertory',
                            ],
                        ],
                        [
                            'name' => 'analysis_method',
                            'type' => 'select',
                            'label' => 'Analysis Method',
                            'options' => [
                                'totality_of_symptoms', 'keynote_prescribing',
                                'constitutional_prescribing', 'miasmatic_prescribing',
                                'organ_affinity', 'clinical_prescribing',
                                'boger_method', 'boenninghausen_method',
                            ],
                        ],
                        [
                            'name' => 'top_remedies',
                            'type' => 'tags',
                            'label' => 'Top Remedies from Repertorization',
                        ],
                        [
                            'name' => 'repertorization_chart',
                            'type' => 'textarea',
                            'label' => 'Repertorization Result / Chart',
                            'placeholder' => 'Remedy scores and analysis...',
                        ],
                    ],
                ],

                // Remedy Selection
                [
                    'id' => 'remedy_selection',
                    'title' => 'Remedy Selection',
                    'fields' => [
                        [
                            'name' => 'remedy_name',
                            'type' => 'text',
                            'label' => 'Remedy Name',
                            'required' => true,
                            'placeholder' => 'e.g., Lycopodium, Sulphur, Natrum Mur',
                        ],
                        [
                            'name' => 'potency',
                            'type' => 'select',
                            'label' => 'Potency',
                            'options' => [
                                'mother_tincture', '3X', '6X', '12X', '30X', '200X',
                                '3C', '6C', '12C', '30C', '200C', '1M', '10M', '50M', 'CM',
                            ],
                        ],
                        [
                            'name' => 'lm_potency',
                            'type' => 'select',
                            'label' => 'LM Potency (if applicable)',
                            'options' => [
                                'not_applicable', 'LM1', 'LM2', 'LM3', 'LM4', 'LM5',
                                'LM6', 'LM7', 'LM8', 'LM9', 'LM10', 'LM12', 'LM18', 'LM30',
                            ],
                        ],
                        [
                            'name' => 'dose',
                            'type' => 'select',
                            'label' => 'Dose',
                            'options' => [
                                'single_dose', 'daily_once', 'twice_daily',
                                'thrice_daily', 'four_times_daily', 'hourly',
                                'sos', 'weekly', 'fortnightly',
                            ],
                        ],
                        [
                            'name' => 'repetition',
                            'type' => 'select',
                            'label' => 'Repetition',
                            'options' => [
                                'single_dose_no_repeat', 'repeat_as_needed',
                                'daily_for_7_days', 'daily_for_14_days',
                                'daily_for_30_days', 'weekly', 'fortnightly',
                                'wait_and_watch',
                            ],
                        ],
                        [
                            'name' => 'administration',
                            'type' => 'select',
                            'label' => 'Administration',
                            'options' => [
                                'dry_on_tongue', 'in_water', 'medicated_globules',
                                'liquid_dilution', 'trituration', 'olfaction',
                            ],
                        ],
                        [
                            'name' => 'intercurrent_remedy',
                            'type' => 'text',
                            'label' => 'Intercurrent Remedy (if any)',
                        ],
                        [
                            'name' => 'biochemic_tissue_salt',
                            'type' => 'text',
                            'label' => 'Biochemic Tissue Salt (if any)',
                        ],
                        [
                            'name' => 'prescription_rationale',
                            'type' => 'textarea',
                            'label' => 'Prescription Rationale',
                            'placeholder' => 'Why this remedy was chosen...',
                        ],
                    ],
                ],

                // Follow-up Assessment
                [
                    'id' => 'followup_assessment',
                    'title' => 'Follow-up Assessment',
                    'fields' => [
                        [
                            'name' => 'remedy_response',
                            'type' => 'select',
                            'label' => 'Remedy Response',
                            'options' => [
                                'marked_amelioration', 'moderate_amelioration',
                                'slight_amelioration', 'no_change', 'slight_aggravation',
                                'marked_aggravation', 'new_symptoms_appeared',
                                'old_symptoms_returned', 'proving_symptoms',
                            ],
                        ],
                        [
                            'name' => 'direction_of_cure',
                            'type' => 'multiselect',
                            'label' => 'Direction of Cure (Hering\'s Law)',
                            'options' => [
                                'above_downward', 'inside_outward', 'important_to_less_important',
                                'reverse_order_of_appearance', 'not_following_herings_law',
                            ],
                        ],
                        [
                            'name' => 'new_symptoms',
                            'type' => 'textarea',
                            'label' => 'New Symptoms (if any)',
                        ],
                        [
                            'name' => 'old_symptoms_returned',
                            'type' => 'textarea',
                            'label' => 'Old Symptoms Returned',
                        ],
                        [
                            'name' => 'general_wellbeing',
                            'type' => 'select',
                            'label' => 'General Wellbeing',
                            'options' => ['much_better', 'better', 'same', 'worse', 'much_worse'],
                        ],
                        [
                            'name' => 'follow_up_action',
                            'type' => 'select',
                            'label' => 'Action Required',
                            'options' => [
                                'wait_and_watch', 'repeat_same_potency', 'higher_potency',
                                'change_remedy', 'intercurrent_remedy', 'complementary_remedy',
                                'antidote', 'placebo',
                            ],
                        ],
                        [
                            'name' => 'follow_up_notes',
                            'type' => 'textarea',
                            'label' => 'Follow-up Notes',
                        ],
                    ],
                ],

                // Diagnosis
                [
                    'id' => 'diagnosis',
                    'title' => 'Diagnosis',
                    'fields' => [
                        [
                            'name' => 'clinical_diagnosis',
                            'type' => 'text',
                            'label' => 'Clinical Diagnosis',
                            'required' => true,
                        ],
                        [
                            'name' => 'miasmatic_diagnosis',
                            'type' => 'text',
                            'label' => 'Miasmatic Diagnosis',
                        ],
                        [
                            'name' => 'icd_code',
                            'type' => 'text',
                            'label' => 'ICD-10 Code',
                            'placeholder' => 'e.g., J45.9',
                        ],
                        [
                            'name' => 'differential_diagnosis',
                            'type' => 'tags',
                            'label' => 'Differential Diagnosis',
                        ],
                    ],
                ],

                // Plan
                [
                    'id' => 'plan',
                    'title' => 'Plan',
                    'fields' => [
                        [
                            'name' => 'dietary_advice',
                            'type' => 'textarea',
                            'label' => 'Dietary Advice',
                            'placeholder' => 'Foods to avoid during homeopathic treatment...',
                        ],
                        [
                            'name' => 'lifestyle_advice',
                            'type' => 'textarea',
                            'label' => 'Lifestyle Advice',
                        ],
                        [
                            'name' => 'follow_up_date',
                            'type' => 'date',
                            'label' => 'Follow-up Date',
                        ],
                        [
                            'name' => 'follow_up_interval',
                            'type' => 'select',
                            'label' => 'Follow-up Interval',
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
                            'name' => 'referral',
                            'type' => 'text',
                            'label' => 'Referral To',
                        ],
                        [
                            'name' => 'investigations',
                            'type' => 'multiselect',
                            'label' => 'Investigations (if required)',
                            'options' => [
                                'cbc', 'esr', 'blood_sugar', 'lipid_profile', 'lft',
                                'rft', 'thyroid', 'urine_routine', 'xray', 'usg', 'ecg',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Get common homeopathy diagnoses mapped to ICD-10 codes.
     */
    public static function getCommonDiagnoses(): array
    {
        Log::info('HomeopathyTemplate::getCommonDiagnoses() - returning ICD-10 codes for common homeopathic conditions');

        return [
            ['code' => 'L20.9', 'name' => 'Atopic dermatitis / Eczema'],
            ['code' => 'L40.0', 'name' => 'Psoriasis vulgaris'],
            ['code' => 'L80', 'name' => 'Vitiligo'],
            ['code' => 'B07', 'name' => 'Viral warts'],
            ['code' => 'J45.9', 'name' => 'Bronchial asthma'],
            ['code' => 'J30.4', 'name' => 'Allergic rhinitis, unspecified'],
            ['code' => 'J06.9', 'name' => 'Upper respiratory tract infection'],
            ['code' => 'G43.9', 'name' => 'Migraine, unspecified'],
            ['code' => 'K58.9', 'name' => 'Irritable bowel syndrome'],
            ['code' => 'M17.9', 'name' => 'Osteoarthritis, knee'],
            ['code' => 'M06.9', 'name' => 'Rheumatoid arthritis, unspecified'],
            ['code' => 'I84.9', 'name' => 'Hemorrhoids, unspecified'],
            ['code' => 'N39.0', 'name' => 'Urinary tract infection'],
            ['code' => 'L50.0', 'name' => 'Allergic urticaria'],
            ['code' => 'L63.9', 'name' => 'Alopecia areata'],
            ['code' => 'K29.7', 'name' => 'Gastritis, unspecified'],
            ['code' => 'E05.9', 'name' => 'Thyrotoxicosis / Hyperthyroidism'],
            ['code' => 'E03.9', 'name' => 'Hypothyroidism, unspecified'],
            ['code' => 'F41.1', 'name' => 'Generalized anxiety disorder'],
            ['code' => 'F32.9', 'name' => 'Depressive episode, unspecified'],
            ['code' => 'G47.0', 'name' => 'Insomnia'],
            ['code' => 'N94.6', 'name' => 'Dysmenorrhea, unspecified'],
            ['code' => 'L70.0', 'name' => 'Acne vulgaris'],
            ['code' => 'D50.9', 'name' => 'Iron deficiency anemia'],
        ];
    }
}
