<?php

namespace App\Services\EmrTemplates;

use Illuminate\Support\Facades\Log;

class AyushTemplate
{
    /**
     * Get AYUSH (Ayurveda) EMR template fields.
     */
    public static function getFields(): array
    {
        Log::info('Loading AYUSH (Ayurveda) EMR template');
        Log::info('AyushTemplate::getFields() - building Ayurvedic assessment sections');

        return [
            'specialty' => 'ayush',
            'sections' => [
                // Chief Complaint (Pradhana Vedana)
                [
                    'id' => 'chief_complaint',
                    'title' => 'Chief Complaint (Pradhana Vedana)',
                    'fields' => [
                        [
                            'name' => 'pradhana_vedana',
                            'type' => 'textarea',
                            'label' => 'Pradhana Vedana (Chief Complaint)',
                            'required' => true,
                            'placeholder' => 'Main presenting complaint in patient\'s words...',
                        ],
                        [
                            'name' => 'duration',
                            'type' => 'text',
                            'label' => 'Duration (Kala)',
                            'placeholder' => 'e.g., 2 months, 1 year',
                        ],
                        [
                            'name' => 'onset',
                            'type' => 'select',
                            'label' => 'Onset',
                            'options' => ['sudden_aakashmik', 'gradual_kramik', 'recurrent_punarbhava'],
                        ],
                        [
                            'name' => 'associated_complaints',
                            'type' => 'textarea',
                            'label' => 'Associated Complaints (Anubandhi Vedana)',
                        ],
                    ],
                ],

                // Prakriti Assessment
                [
                    'id' => 'prakriti_assessment',
                    'title' => 'Prakriti Assessment',
                    'fields' => [
                        [
                            'name' => 'vata_prakriti_score',
                            'type' => 'slider',
                            'label' => 'Vata Prakriti Score',
                            'min' => 0,
                            'max' => 30,
                        ],
                        [
                            'name' => 'vata_features',
                            'type' => 'multiselect',
                            'label' => 'Vata Features',
                            'options' => [
                                'thin_frame', 'dry_skin', 'cold_hands_feet', 'light_sleep',
                                'variable_appetite', 'constipation_tendency', 'creative_mind',
                                'quick_to_learn_quick_to_forget', 'irregular_routine',
                                'anxiety_prone', 'cracking_joints', 'gas_bloating',
                            ],
                        ],
                        [
                            'name' => 'pitta_prakriti_score',
                            'type' => 'slider',
                            'label' => 'Pitta Prakriti Score',
                            'min' => 0,
                            'max' => 30,
                        ],
                        [
                            'name' => 'pitta_features',
                            'type' => 'multiselect',
                            'label' => 'Pitta Features',
                            'options' => [
                                'medium_frame', 'warm_skin', 'strong_appetite', 'sharp_intellect',
                                'irritable', 'acidity_tendency', 'soft_loose_stools',
                                'excessive_sweating', 'early_greying', 'intolerant_to_heat',
                                'good_leadership', 'skin_rashes_tendency',
                            ],
                        ],
                        [
                            'name' => 'kapha_prakriti_score',
                            'type' => 'slider',
                            'label' => 'Kapha Prakriti Score',
                            'min' => 0,
                            'max' => 30,
                        ],
                        [
                            'name' => 'kapha_features',
                            'type' => 'multiselect',
                            'label' => 'Kapha Features',
                            'options' => [
                                'heavy_frame', 'oily_skin', 'deep_sleep', 'slow_digestion',
                                'calm_patient', 'good_memory', 'weight_gain_tendency',
                                'mucus_tendency', 'cold_intolerance', 'steady_energy',
                                'thick_hair', 'smooth_joints',
                            ],
                        ],
                        [
                            'name' => 'prakriti_result',
                            'type' => 'select',
                            'label' => 'Prakriti Type',
                            'options' => [
                                'vata', 'pitta', 'kapha',
                                'vata_pitta', 'vata_kapha', 'pitta_kapha',
                                'tridoshaja',
                            ],
                        ],
                    ],
                ],

                // Rogi Pariksha (Ashtavidha Pariksha)
                [
                    'id' => 'rogi_pariksha',
                    'title' => 'Rogi Pariksha (Ashtavidha Pariksha - Eightfold Examination)',
                    'fields' => [
                        [
                            'name' => 'nadi',
                            'type' => 'select',
                            'label' => 'Nadi (Pulse)',
                            'options' => [
                                'vata_nadi_manduka', 'pitta_nadi_manduka', 'kapha_nadi_hamsa',
                                'vata_pitta', 'vata_kapha', 'pitta_kapha', 'sama_nadi',
                            ],
                        ],
                        [
                            'name' => 'nadi_gati',
                            'type' => 'text',
                            'label' => 'Nadi Gati (Pulse Rate/Character)',
                            'placeholder' => 'e.g., 72/min, regular, manduka gati',
                        ],
                        [
                            'name' => 'mutra',
                            'type' => 'select',
                            'label' => 'Mutra (Urine)',
                            'options' => ['prakrit_normal', 'alpa_scanty', 'prabhuta_excessive', 'sarukta_painful', 'peeta_yellow', 'rakta_reddish', 'avila_turbid'],
                        ],
                        [
                            'name' => 'mutra_details',
                            'type' => 'text',
                            'label' => 'Mutra Details',
                            'placeholder' => 'Frequency, colour, odour...',
                        ],
                        [
                            'name' => 'mala',
                            'type' => 'select',
                            'label' => 'Mala (Stool)',
                            'options' => ['prakrit_normal', 'baddha_constipated', 'drava_loose', 'sama_with_ama', 'nirama_without_ama', 'rakta_blood_mixed'],
                        ],
                        [
                            'name' => 'mala_details',
                            'type' => 'text',
                            'label' => 'Mala Details',
                            'placeholder' => 'Frequency, consistency, colour...',
                        ],
                        [
                            'name' => 'jihva',
                            'type' => 'select',
                            'label' => 'Jihva (Tongue)',
                            'options' => ['prakrit_normal', 'sama_coated', 'nirama_clean', 'rakta_red', 'shukla_white', 'peeta_yellowish', 'krishna_blackish'],
                        ],
                        [
                            'name' => 'shabda',
                            'type' => 'select',
                            'label' => 'Shabda (Voice/Speech)',
                            'options' => ['prakrit_normal', 'ksheena_weak', 'galita_hoarse', 'uccha_loud', 'gadgada_stammering'],
                        ],
                        [
                            'name' => 'sparsha',
                            'type' => 'select',
                            'label' => 'Sparsha (Touch/Skin)',
                            'options' => ['prakrit_normal', 'ushna_hot', 'sheeta_cold', 'ruksha_dry', 'snigdha_oily', 'sama_swollen'],
                        ],
                        [
                            'name' => 'drik',
                            'type' => 'select',
                            'label' => 'Drik (Eyes)',
                            'options' => ['prakrit_normal', 'rakta_red', 'peeta_yellowish', 'pandura_pale', 'ashrumukhi_watery', 'krishna_dark_circles'],
                        ],
                        [
                            'name' => 'akriti',
                            'type' => 'select',
                            'label' => 'Akriti (General Appearance)',
                            'options' => ['swastha_healthy', 'krusha_emaciated', 'sthula_obese', 'madhyama_moderate', 'durbala_weak'],
                        ],
                    ],
                ],

                // Dashavidha Pariksha
                [
                    'id' => 'dashavidha_pariksha',
                    'title' => 'Dashavidha Pariksha (Tenfold Examination)',
                    'fields' => [
                        [
                            'name' => 'prakriti_dasha',
                            'type' => 'select',
                            'label' => 'Prakriti (Constitution)',
                            'options' => ['vata', 'pitta', 'kapha', 'vata_pitta', 'vata_kapha', 'pitta_kapha', 'tridoshaja'],
                        ],
                        [
                            'name' => 'vikriti',
                            'type' => 'select',
                            'label' => 'Vikriti (Morbidity/Current Imbalance)',
                            'options' => ['vata_vikriti', 'pitta_vikriti', 'kapha_vikriti', 'vata_pitta_vikriti', 'vata_kapha_vikriti', 'pitta_kapha_vikriti', 'tridosha_vikriti'],
                        ],
                        [
                            'name' => 'sara',
                            'type' => 'select',
                            'label' => 'Sara (Tissue Essence/Excellence)',
                            'options' => ['rasa_sara', 'rakta_sara', 'mamsa_sara', 'meda_sara', 'asthi_sara', 'majja_sara', 'shukra_sara', 'satva_sara'],
                        ],
                        [
                            'name' => 'samhanana',
                            'type' => 'select',
                            'label' => 'Samhanana (Body Compactness)',
                            'options' => ['pravara_excellent', 'madhyama_moderate', 'avara_poor'],
                        ],
                        [
                            'name' => 'pramana',
                            'type' => 'select',
                            'label' => 'Pramana (Body Proportions/Measurements)',
                            'options' => ['sama_proportionate', 'hrasva_short', 'dirgha_tall', 'sthula_broad', 'krusha_thin'],
                        ],
                        [
                            'name' => 'satmya',
                            'type' => 'select',
                            'label' => 'Satmya (Adaptability/Homologation)',
                            'options' => ['pravara_excellent', 'madhyama_moderate', 'avara_poor'],
                        ],
                        [
                            'name' => 'satva',
                            'type' => 'select',
                            'label' => 'Satva (Psychic/Mental Strength)',
                            'options' => ['pravara_strong', 'madhyama_moderate', 'avara_weak'],
                        ],
                        [
                            'name' => 'ahara_shakti',
                            'type' => 'select',
                            'label' => 'Ahara Shakti (Digestive Power)',
                            'options' => ['pravara_strong', 'madhyama_moderate', 'avara_weak'],
                        ],
                        [
                            'name' => 'vyayama_shakti',
                            'type' => 'select',
                            'label' => 'Vyayama Shakti (Exercise Tolerance)',
                            'options' => ['pravara_strong', 'madhyama_moderate', 'avara_weak'],
                        ],
                        [
                            'name' => 'vaya',
                            'type' => 'select',
                            'label' => 'Vaya (Age Category)',
                            'options' => ['balya_childhood', 'yuva_young', 'madhyama_middle', 'vriddha_elderly'],
                        ],
                    ],
                ],

                // Dosha Assessment
                [
                    'id' => 'dosha_assessment',
                    'title' => 'Dosha Assessment',
                    'fields' => [
                        [
                            'name' => 'vata_vikrti',
                            'type' => 'multiselect',
                            'label' => 'Vata Vikrti (Vata Vitiation Signs)',
                            'options' => [
                                'sandhigata_vata', 'vataja_shula', 'gridhrasi', 'apatantraka',
                                'pakshavadha', 'kampavata', 'anidra', 'adhmana', 'vibandha',
                                'toda', 'stambha', 'supti',
                            ],
                        ],
                        [
                            'name' => 'pitta_vikrti',
                            'type' => 'multiselect',
                            'label' => 'Pitta Vikrti (Pitta Vitiation Signs)',
                            'options' => [
                                'amlapitta', 'raktapitta', 'daha', 'jwara', 'vidaha',
                                'pandu', 'kamala', 'visarpa', 'kushtha', 'netra_roga',
                                'trishna', 'atisara',
                            ],
                        ],
                        [
                            'name' => 'kapha_vikrti',
                            'type' => 'multiselect',
                            'label' => 'Kapha Vikrti (Kapha Vitiation Signs)',
                            'options' => [
                                'kasa', 'shwasa', 'pratishyaya', 'sthaulya', 'prameha',
                                'mandagni', 'tandra', 'gaurava', 'arochaka', 'chardi',
                                'shleshma_praseka', 'alasya',
                            ],
                        ],
                        [
                            'name' => 'dhatu_involvement',
                            'type' => 'multiselect',
                            'label' => 'Dhatu Involvement (Tissue)',
                            'options' => ['rasa', 'rakta', 'mamsa', 'meda', 'asthi', 'majja', 'shukra'],
                        ],
                        [
                            'name' => 'srotas_involved',
                            'type' => 'multiselect',
                            'label' => 'Srotas Involved (Channels)',
                            'options' => [
                                'pranavaha', 'annavaha', 'udakavaha', 'rasavaha',
                                'raktavaha', 'mamsavaha', 'medovaha', 'asthivaha',
                                'majjavaha', 'shukravaha', 'mutravaha', 'purishavaha',
                                'swedavaha', 'artavavaha', 'stanyavaha', 'manovaha',
                            ],
                        ],
                        [
                            'name' => 'agni_type',
                            'type' => 'select',
                            'label' => 'Agni Type (Digestive Fire)',
                            'options' => [
                                'sama_agni_balanced', 'vishama_agni_irregular',
                                'tikshna_agni_sharp', 'manda_agni_weak',
                            ],
                        ],
                    ],
                ],

                // Samprapti (Pathogenesis)
                [
                    'id' => 'samprapti',
                    'title' => 'Samprapti (Pathogenesis)',
                    'fields' => [
                        [
                            'name' => 'nidana',
                            'type' => 'textarea',
                            'label' => 'Nidana (Causative Factors)',
                            'placeholder' => 'Aharaja, Viharaja, Manasika nidana...',
                        ],
                        [
                            'name' => 'dosha_samprapti',
                            'type' => 'textarea',
                            'label' => 'Dosha (Vitiated Dosha)',
                            'placeholder' => 'Which dosha is predominantly vitiated...',
                        ],
                        [
                            'name' => 'dushya',
                            'type' => 'textarea',
                            'label' => 'Dushya (Vitiated Tissue/Dhatu)',
                        ],
                        [
                            'name' => 'srotas_samprapti',
                            'type' => 'textarea',
                            'label' => 'Srotas (Affected Channels)',
                        ],
                        [
                            'name' => 'agni_samprapti',
                            'type' => 'textarea',
                            'label' => 'Agni (State of Digestive Fire)',
                        ],
                        [
                            'name' => 'ama',
                            'type' => 'select',
                            'label' => 'Ama (Metabolic Toxins)',
                            'options' => ['sama_with_ama', 'nirama_without_ama', 'ama_pachana_in_process'],
                        ],
                        [
                            'name' => 'sthana',
                            'type' => 'textarea',
                            'label' => 'Sthana (Site of Disease Manifestation)',
                        ],
                        [
                            'name' => 'samprapti_ghatakas',
                            'type' => 'textarea',
                            'label' => 'Samprapti Ghatakas (Summary)',
                            'placeholder' => 'Dosha → Dushya → Srotas → Agni → Ama → Sthana...',
                        ],
                    ],
                ],

                // Chikitsa (Treatment)
                [
                    'id' => 'chikitsa',
                    'title' => 'Chikitsa (Treatment)',
                    'fields' => [
                        [
                            'name' => 'shamana_chikitsa',
                            'type' => 'textarea',
                            'label' => 'Shamana Chikitsa (Palliative - Oral Medicines)',
                            'placeholder' => 'Ayurvedic oral medicines with dose and anupana...',
                        ],
                        [
                            'name' => 'shamana_medicines',
                            'type' => 'tags',
                            'label' => 'Shamana Medicines List',
                            'suggestions' => [
                                'triphala_churna', 'ashwagandha_churna', 'guduchi_satwa',
                                'dashamoola_kwatha', 'kaishore_guggulu', 'chandraprabha_vati',
                                'yogaraj_guggulu', 'arogyavardhini_vati', 'kutajarishta',
                                'draksharishta', 'sarivadyasava', 'punarnavasava',
                            ],
                        ],
                        [
                            'name' => 'shodhana_chikitsa',
                            'type' => 'multiselect',
                            'label' => 'Shodhana Chikitsa (Panchakarma Procedures)',
                            'options' => [
                                'vamana_emesis', 'virechana_purgation', 'basti_enema',
                                'nasya_nasal', 'raktamokshana_bloodletting',
                                'abhyanga_massage', 'swedana_sudation', 'shirodhara',
                                'shirobasti', 'kati_basti', 'janu_basti', 'greeva_basti',
                                'netra_tarpana', 'pizhichil', 'udvartana', 'lepana',
                            ],
                        ],
                        [
                            'name' => 'panchakarma_details',
                            'type' => 'textarea',
                            'label' => 'Panchakarma Details',
                            'placeholder' => 'Duration, schedule, specific oils/decoctions...',
                        ],
                        [
                            'name' => 'pathya',
                            'type' => 'textarea',
                            'label' => 'Pathya (Wholesome Diet & Lifestyle)',
                            'placeholder' => 'Recommended foods, activities, habits...',
                        ],
                        [
                            'name' => 'apathya',
                            'type' => 'textarea',
                            'label' => 'Apathya (Unwholesome - To Avoid)',
                            'placeholder' => 'Foods, activities, habits to avoid...',
                        ],
                        [
                            'name' => 'yoga_pranayama',
                            'type' => 'textarea',
                            'label' => 'Yoga & Pranayama Advice',
                        ],
                        [
                            'name' => 'dincharya',
                            'type' => 'textarea',
                            'label' => 'Dincharya (Daily Routine Recommendations)',
                        ],
                        [
                            'name' => 'ritucharya',
                            'type' => 'textarea',
                            'label' => 'Ritucharya (Seasonal Recommendations)',
                        ],
                    ],
                ],

                // Diagnosis
                [
                    'id' => 'diagnosis',
                    'title' => 'Diagnosis',
                    'fields' => [
                        [
                            'name' => 'ayurvedic_diagnosis',
                            'type' => 'text',
                            'label' => 'Ayurvedic Diagnosis (Vyadhi)',
                            'required' => true,
                            'placeholder' => 'e.g., Amavata, Sandhivata, Kushtha',
                        ],
                        [
                            'name' => 'modern_correlation',
                            'type' => 'text',
                            'label' => 'Modern Correlation',
                            'placeholder' => 'e.g., Rheumatoid Arthritis, Osteoarthritis',
                        ],
                        [
                            'name' => 'icd_code',
                            'type' => 'text',
                            'label' => 'ICD-10 Code',
                            'placeholder' => 'e.g., M06.9',
                        ],
                        [
                            'name' => 'differential_diagnosis',
                            'type' => 'tags',
                            'label' => 'Differential Diagnosis (Ayurvedic)',
                        ],
                    ],
                ],

                // Plan
                [
                    'id' => 'plan',
                    'title' => 'Plan',
                    'fields' => [
                        [
                            'name' => 'treatment_duration',
                            'type' => 'text',
                            'label' => 'Treatment Duration',
                            'placeholder' => 'e.g., 21 days, 1 month',
                        ],
                        [
                            'name' => 'follow_up_date',
                            'type' => 'date',
                            'label' => 'Follow-up Date',
                        ],
                        [
                            'name' => 'follow_up_instructions',
                            'type' => 'textarea',
                            'label' => 'Follow-up Instructions',
                        ],
                        [
                            'name' => 'referral',
                            'type' => 'text',
                            'label' => 'Referral To',
                        ],
                        [
                            'name' => 'investigations',
                            'type' => 'multiselect',
                            'label' => 'Investigations (Modern)',
                            'options' => [
                                'cbc', 'esr', 'crp', 'ra_factor', 'uric_acid',
                                'blood_sugar', 'lipid_profile', 'lft', 'rft', 'thyroid',
                                'urine_routine', 'stool_routine', 'xray', 'usg',
                            ],
                        ],
                        [
                            'name' => 'prognosis',
                            'type' => 'select',
                            'label' => 'Prognosis (Sadhya-Asadhyata)',
                            'options' => [
                                'sadhya_curable', 'krichra_sadhya_difficult_to_cure',
                                'yapya_manageable', 'asadhya_incurable',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Get common AYUSH diagnoses mapped to ICD-10 codes.
     */
    public static function getCommonDiagnoses(): array
    {
        Log::info('AyushTemplate::getCommonDiagnoses() - returning Ayurvedic to ICD-10 mapped codes');

        return [
            ['code' => 'M06.9', 'name' => 'Amavata (Rheumatoid Arthritis)'],
            ['code' => 'M10.9', 'name' => 'Vatarakta (Gout)'],
            ['code' => 'D50.9', 'name' => 'Pandu (Iron Deficiency Anemia)'],
            ['code' => 'E11.9', 'name' => 'Prameha / Madhumeha (Type 2 Diabetes Mellitus)'],
            ['code' => 'J45.9', 'name' => 'Tamaka Shwasa (Bronchial Asthma)'],
            ['code' => 'K58.9', 'name' => 'Grahani (Irritable Bowel Syndrome)'],
            ['code' => 'R18.8', 'name' => 'Udara Roga (Ascites)'],
            ['code' => 'L40.9', 'name' => 'Kushtha - Ekakushtha (Psoriasis)'],
            ['code' => 'I84.9', 'name' => 'Arsha (Hemorrhoids)'],
            ['code' => 'M17.9', 'name' => 'Sandhivata (Osteoarthritis)'],
            ['code' => 'M54.5', 'name' => 'Katishoola / Gridhrasi (Low Back Pain / Sciatica)'],
            ['code' => 'I10', 'name' => 'Raktachapa Vridhi / Uccharaktachapa (Essential Hypertension)'],
            ['code' => 'K29.7', 'name' => 'Amlapitta (Gastritis)'],
            ['code' => 'J06.9', 'name' => 'Pratishyaya (Upper Respiratory Tract Infection)'],
            ['code' => 'L20.9', 'name' => 'Vicharchika (Eczema / Atopic Dermatitis)'],
            ['code' => 'E66.9', 'name' => 'Sthaulya / Medoroga (Obesity)'],
            ['code' => 'G47.0', 'name' => 'Anidra (Insomnia)'],
            ['code' => 'N94.6', 'name' => 'Kashtartava (Dysmenorrhea)'],
            ['code' => 'E05.9', 'name' => 'Galaganda (Thyroid Disorder)'],
            ['code' => 'K59.0', 'name' => 'Vibandha (Constipation)'],
            ['code' => 'R51', 'name' => 'Shirahshoola (Headache)'],
            ['code' => 'N39.0', 'name' => 'Mutrakrichra (Urinary Tract Infection)'],
        ];
    }
}
