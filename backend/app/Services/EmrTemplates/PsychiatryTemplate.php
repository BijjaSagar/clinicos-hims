<?php

namespace App\Services\EmrTemplates;

use Illuminate\Support\Facades\Log;

class PsychiatryTemplate
{
    /**
     * Get psychiatry EMR template fields.
     */
    public static function getFields(): array
    {
        Log::info('Loading Psychiatry EMR template');
        Log::info('PsychiatryTemplate::getFields() - building sections array');

        return [
            'specialty' => 'psychiatry',
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
                            'placeholder' => 'Main presenting complaint in patient\'s own words...',
                        ],
                        [
                            'name' => 'informant',
                            'type' => 'text',
                            'label' => 'Informant / Reliable Attendant',
                            'placeholder' => 'Relationship to patient',
                        ],
                        [
                            'name' => 'referral_source',
                            'type' => 'select',
                            'label' => 'Referral Source',
                            'options' => ['self', 'family', 'gp', 'physician', 'emergency', 'court', 'employer', 'school'],
                        ],
                    ],
                ],

                // Psychiatric History
                [
                    'id' => 'psychiatric_history',
                    'title' => 'Psychiatric History',
                    'fields' => [
                        [
                            'name' => 'presenting_complaints_timeline',
                            'type' => 'textarea',
                            'label' => 'Presenting Complaints (with Timeline)',
                            'placeholder' => 'Chronological account of symptoms with onset and duration...',
                        ],
                        [
                            'name' => 'presenting_symptoms',
                            'type' => 'multiselect',
                            'label' => 'Presenting Symptoms',
                            'options' => [
                                'low_mood', 'anhedonia', 'anxiety', 'panic_attacks',
                                'irritability', 'anger_outbursts', 'sleep_disturbance',
                                'appetite_change', 'fatigue', 'poor_concentration',
                                'suicidal_ideation', 'self_harm', 'hallucinations',
                                'delusions', 'suspiciousness', 'social_withdrawal',
                                'restlessness', 'elevated_mood', 'grandiosity',
                                'decreased_need_for_sleep', 'pressured_speech',
                                'obsessions', 'compulsions', 'flashbacks',
                                'avoidance', 'hypervigilance',
                            ],
                        ],
                        [
                            'name' => 'past_psychiatric_history',
                            'type' => 'textarea',
                            'label' => 'Past Psychiatric History',
                            'placeholder' => 'Previous episodes, diagnoses, treatments, hospitalizations...',
                        ],
                        [
                            'name' => 'past_psychiatric_diagnoses',
                            'type' => 'tags',
                            'label' => 'Past Psychiatric Diagnoses',
                        ],
                        [
                            'name' => 'previous_medications',
                            'type' => 'textarea',
                            'label' => 'Previous Psychiatric Medications',
                            'placeholder' => 'Medications tried, doses, response, side effects...',
                        ],
                        [
                            'name' => 'previous_hospitalisations',
                            'type' => 'number',
                            'label' => 'Number of Previous Hospitalisations',
                        ],
                        [
                            'name' => 'ect_history',
                            'type' => 'select',
                            'label' => 'ECT History',
                            'options' => ['none', 'previous_with_response', 'previous_no_response', 'currently_receiving'],
                        ],
                        [
                            'name' => 'substance_use_alcohol',
                            'type' => 'select',
                            'label' => 'Alcohol Use',
                            'options' => ['none', 'social', 'regular', 'heavy', 'dependent', 'previously_dependent'],
                        ],
                        [
                            'name' => 'alcohol_quantity',
                            'type' => 'text',
                            'label' => 'Alcohol Quantity/Pattern',
                            'placeholder' => 'e.g., 180ml whisky daily x 5 years',
                        ],
                        [
                            'name' => 'cage_score',
                            'type' => 'number',
                            'label' => 'CAGE Score (0-4)',
                            'min' => 0,
                            'max' => 4,
                        ],
                        [
                            'name' => 'substance_use_tobacco',
                            'type' => 'select',
                            'label' => 'Tobacco Use',
                            'options' => ['none', 'smoking', 'chewing', 'both', 'quit'],
                        ],
                        [
                            'name' => 'substance_use_cannabis',
                            'type' => 'select',
                            'label' => 'Cannabis Use',
                            'options' => ['none', 'occasional', 'regular', 'daily', 'dependent', 'quit'],
                        ],
                        [
                            'name' => 'substance_use_opioids',
                            'type' => 'select',
                            'label' => 'Opioid Use',
                            'options' => ['none', 'prescription', 'misuse', 'dependent', 'on_substitution', 'quit'],
                        ],
                        [
                            'name' => 'substance_use_other',
                            'type' => 'textarea',
                            'label' => 'Other Substance Use',
                            'placeholder' => 'Benzodiazepines, stimulants, inhalants, IV drugs...',
                        ],
                        [
                            'name' => 'substance_use_duration',
                            'type' => 'text',
                            'label' => 'Substance Use Duration',
                        ],
                        [
                            'name' => 'personal_history',
                            'type' => 'textarea',
                            'label' => 'Personal History',
                            'placeholder' => 'Birth, milestones, education, occupation, relationships, sexual history...',
                        ],
                        [
                            'name' => 'premorbid_personality',
                            'type' => 'textarea',
                            'label' => 'Premorbid Personality',
                            'placeholder' => 'Social relationships, temperament, habits, coping style, character traits...',
                        ],
                        [
                            'name' => 'forensic_history',
                            'type' => 'textarea',
                            'label' => 'Forensic History',
                            'placeholder' => 'Legal issues, arrests, convictions...',
                        ],
                    ],
                ],

                // Family History
                [
                    'id' => 'family_history',
                    'title' => 'Family History',
                    'fields' => [
                        [
                            'name' => 'family_psychiatric_illness',
                            'type' => 'textarea',
                            'label' => 'Family History of Psychiatric Illness',
                            'placeholder' => 'Genogram details - family members with psychiatric diagnoses...',
                        ],
                        [
                            'name' => 'family_psychiatric_conditions',
                            'type' => 'multiselect',
                            'label' => 'Family Psychiatric Conditions',
                            'options' => [
                                'depression', 'bipolar', 'schizophrenia', 'anxiety',
                                'ocd', 'substance_use', 'suicide', 'dementia',
                                'intellectual_disability', 'autism', 'adhd', 'epilepsy',
                            ],
                        ],
                        [
                            'name' => 'family_suicide_history',
                            'type' => 'boolean',
                            'label' => 'Family History of Suicide',
                        ],
                        [
                            'name' => 'family_history_notes',
                            'type' => 'textarea',
                            'label' => 'Family History Notes',
                        ],
                    ],
                ],

                // Mental Status Examination
                [
                    'id' => 'mse',
                    'title' => 'Mental Status Examination',
                    'fields' => [
                        [
                            'name' => 'appearance',
                            'type' => 'multiselect',
                            'label' => 'Appearance',
                            'options' => [
                                'well_groomed', 'dishevelled', 'unkempt', 'bizarre_dressing',
                                'age_appropriate', 'older_than_stated_age', 'thin', 'obese',
                                'tattoos', 'self_harm_scars', 'poor_hygiene',
                            ],
                        ],
                        [
                            'name' => 'behaviour',
                            'type' => 'multiselect',
                            'label' => 'Behaviour',
                            'options' => [
                                'cooperative', 'guarded', 'hostile', 'agitated',
                                'retarded', 'restless', 'mannerisms', 'stereotypies',
                                'posturing', 'catatonic', 'poor_eye_contact',
                                'good_eye_contact', 'disinhibited', 'withdrawn',
                            ],
                        ],
                        [
                            'name' => 'rapport',
                            'type' => 'select',
                            'label' => 'Rapport',
                            'options' => ['easily_established', 'difficult_to_establish', 'not_established', 'fluctuating'],
                        ],
                        [
                            'name' => 'speech_rate',
                            'type' => 'select',
                            'label' => 'Speech - Rate',
                            'options' => ['normal', 'slow', 'pressured', 'mute', 'poverty_of_speech'],
                        ],
                        [
                            'name' => 'speech_volume',
                            'type' => 'select',
                            'label' => 'Speech - Volume',
                            'options' => ['normal', 'loud', 'soft', 'whispered'],
                        ],
                        [
                            'name' => 'speech_tone',
                            'type' => 'select',
                            'label' => 'Speech - Tone',
                            'options' => ['normal', 'monotonous', 'tremulous', 'high_pitched'],
                        ],
                        [
                            'name' => 'speech_coherence',
                            'type' => 'select',
                            'label' => 'Speech - Coherence',
                            'options' => ['coherent', 'incoherent', 'tangential', 'circumstantial'],
                        ],
                        [
                            'name' => 'mood_subjective',
                            'type' => 'text',
                            'label' => 'Mood (Subjective - Patient\'s Words)',
                            'placeholder' => 'In patient\'s own words...',
                        ],
                        [
                            'name' => 'mood_objective',
                            'type' => 'select',
                            'label' => 'Mood (Objective)',
                            'options' => ['euthymic', 'depressed', 'anxious', 'elated', 'irritable', 'angry', 'fearful', 'apathetic'],
                        ],
                        [
                            'name' => 'affect_type',
                            'type' => 'select',
                            'label' => 'Affect - Type',
                            'options' => ['appropriate', 'inappropriate', 'labile', 'blunted', 'flat', 'restricted', 'reactive'],
                        ],
                        [
                            'name' => 'affect_congruence',
                            'type' => 'select',
                            'label' => 'Affect - Congruence with Mood',
                            'options' => ['congruent', 'incongruent'],
                        ],
                        [
                            'name' => 'thought_form',
                            'type' => 'multiselect',
                            'label' => 'Thought Form',
                            'options' => [
                                'normal', 'flight_of_ideas', 'loosening_of_associations',
                                'tangentiality', 'circumstantiality', 'thought_blocking',
                                'perseveration', 'neologisms', 'word_salad',
                                'derailment', 'poverty_of_thought',
                            ],
                        ],
                        [
                            'name' => 'delusions',
                            'type' => 'multiselect',
                            'label' => 'Delusions',
                            'options' => [
                                'none', 'persecutory', 'grandiose', 'reference',
                                'erotomanic', 'jealous', 'somatic', 'nihilistic',
                                'guilt', 'thought_insertion', 'thought_withdrawal',
                                'thought_broadcasting', 'passivity', 'control',
                            ],
                        ],
                        [
                            'name' => 'obsessions',
                            'type' => 'multiselect',
                            'label' => 'Obsessions',
                            'options' => [
                                'none', 'contamination', 'symmetry', 'aggressive',
                                'sexual', 'religious', 'hoarding', 'doubt',
                                'somatic', 'other',
                            ],
                        ],
                        [
                            'name' => 'compulsions',
                            'type' => 'multiselect',
                            'label' => 'Compulsions',
                            'options' => [
                                'none', 'washing', 'checking', 'counting',
                                'ordering', 'hoarding', 'repeating', 'mental_rituals',
                            ],
                        ],
                        [
                            'name' => 'suicidal_ideation',
                            'type' => 'select',
                            'label' => 'Suicidal Ideation',
                            'options' => [
                                'none', 'passive_death_wish', 'active_ideation_no_plan',
                                'active_ideation_with_plan', 'intent_present', 'recent_attempt',
                            ],
                        ],
                        [
                            'name' => 'suicidal_ideation_details',
                            'type' => 'textarea',
                            'label' => 'Suicidal Ideation Details',
                        ],
                        [
                            'name' => 'homicidal_ideation',
                            'type' => 'select',
                            'label' => 'Homicidal Ideation',
                            'options' => ['none', 'passive_thoughts', 'active_ideation', 'plan_present'],
                        ],
                        [
                            'name' => 'hallucinations',
                            'type' => 'multiselect',
                            'label' => 'Hallucinations',
                            'options' => [
                                'none', 'auditory_second_person', 'auditory_third_person',
                                'auditory_command', 'auditory_running_commentary',
                                'visual', 'tactile', 'olfactory', 'gustatory',
                                'somatic', 'hypnagogic', 'hypnopompic',
                            ],
                        ],
                        [
                            'name' => 'illusions',
                            'type' => 'select',
                            'label' => 'Illusions',
                            'options' => ['none', 'visual', 'auditory', 'tactile'],
                        ],
                        [
                            'name' => 'depersonalisation',
                            'type' => 'boolean',
                            'label' => 'Depersonalisation',
                        ],
                        [
                            'name' => 'derealisation',
                            'type' => 'boolean',
                            'label' => 'Derealisation',
                        ],
                        [
                            'name' => 'cognition_orientation',
                            'type' => 'select',
                            'label' => 'Cognition - Orientation',
                            'options' => ['oriented_tpp', 'disoriented_time', 'disoriented_place', 'disoriented_person', 'grossly_disoriented'],
                        ],
                        [
                            'name' => 'cognition_attention',
                            'type' => 'select',
                            'label' => 'Cognition - Attention & Concentration',
                            'options' => ['normal', 'mildly_impaired', 'moderately_impaired', 'severely_impaired'],
                        ],
                        [
                            'name' => 'cognition_memory',
                            'type' => 'select',
                            'label' => 'Cognition - Memory',
                            'options' => ['intact', 'immediate_impaired', 'recent_impaired', 'remote_impaired', 'global_impaired'],
                        ],
                        [
                            'name' => 'abstract_thinking',
                            'type' => 'select',
                            'label' => 'Abstract Thinking',
                            'options' => ['intact', 'concrete', 'impaired'],
                        ],
                        [
                            'name' => 'judgment',
                            'type' => 'select',
                            'label' => 'Judgment',
                            'options' => ['intact', 'impaired_personal', 'impaired_social', 'impaired_test', 'grossly_impaired'],
                        ],
                        [
                            'name' => 'insight_grade',
                            'type' => 'select',
                            'label' => 'Insight (Grading 1-6)',
                            'options' => [
                                ['value' => 1, 'label' => '1 - Complete denial of illness'],
                                ['value' => 2, 'label' => '2 - Slight awareness of being sick, but denying it'],
                                ['value' => 3, 'label' => '3 - Aware of illness but blames external factors'],
                                ['value' => 4, 'label' => '4 - Aware something is wrong but doesn\'t know what'],
                                ['value' => 5, 'label' => '5 - Intellectual insight (aware but no change)'],
                                ['value' => 6, 'label' => '6 - True emotional insight'],
                            ],
                        ],
                    ],
                ],

                // Risk Assessment
                [
                    'id' => 'risk_assessment',
                    'title' => 'Risk Assessment',
                    'fields' => [
                        [
                            'name' => 'suicide_risk',
                            'type' => 'select',
                            'label' => 'Suicide Risk Level',
                            'options' => [
                                ['value' => 'low', 'label' => 'Low - No current ideation, no plan'],
                                ['value' => 'moderate', 'label' => 'Moderate - Ideation present but no intent/plan'],
                                ['value' => 'high', 'label' => 'High - Ideation with plan or intent'],
                                ['value' => 'imminent', 'label' => 'Imminent - Active plan with access to means'],
                            ],
                        ],
                        [
                            'name' => 'suicide_risk_factors',
                            'type' => 'multiselect',
                            'label' => 'Suicide Risk Factors',
                            'options' => [
                                'previous_attempt', 'family_history_suicide', 'male_gender',
                                'elderly', 'living_alone', 'chronic_illness', 'substance_use',
                                'hopelessness', 'recent_loss', 'access_to_means',
                                'social_isolation', 'discharge_from_hospital',
                            ],
                        ],
                        [
                            'name' => 'suicide_protective_factors',
                            'type' => 'multiselect',
                            'label' => 'Protective Factors',
                            'options' => [
                                'family_support', 'social_network', 'employment',
                                'religious_beliefs', 'children', 'future_plans',
                                'treatment_engagement', 'no_access_to_means',
                            ],
                        ],
                        [
                            'name' => 'self_harm_risk',
                            'type' => 'select',
                            'label' => 'Self-Harm Risk',
                            'options' => ['none', 'low', 'moderate', 'high'],
                        ],
                        [
                            'name' => 'self_harm_history',
                            'type' => 'textarea',
                            'label' => 'Self-Harm History',
                            'placeholder' => 'Methods, frequency, last episode...',
                        ],
                        [
                            'name' => 'violence_risk',
                            'type' => 'select',
                            'label' => 'Violence Risk',
                            'options' => ['none', 'low', 'moderate', 'high'],
                        ],
                        [
                            'name' => 'violence_risk_details',
                            'type' => 'textarea',
                            'label' => 'Violence Risk Details',
                        ],
                        [
                            'name' => 'safeguarding_concerns',
                            'type' => 'multiselect',
                            'label' => 'Safeguarding Concerns',
                            'options' => [
                                'none', 'child_at_risk', 'vulnerable_adult',
                                'domestic_violence', 'elder_abuse', 'neglect',
                                'financial_exploitation', 'sexual_abuse',
                            ],
                        ],
                        [
                            'name' => 'safeguarding_action',
                            'type' => 'textarea',
                            'label' => 'Safeguarding Action Taken',
                        ],
                        [
                            'name' => 'safety_plan',
                            'type' => 'textarea',
                            'label' => 'Safety Plan',
                            'placeholder' => 'Warning signs, coping strategies, contacts, emergency plan...',
                        ],
                    ],
                ],

                // Scales
                [
                    'id' => 'scales',
                    'title' => 'Psychiatric Scales & Scoring',
                    'fields' => [
                        [
                            'name' => 'phq9_score',
                            'type' => 'number',
                            'label' => 'PHQ-9 Score (Depression)',
                            'min' => 0,
                            'max' => 27,
                        ],
                        [
                            'name' => 'phq9_interpretation',
                            'type' => 'select',
                            'label' => 'PHQ-9 Interpretation',
                            'options' => [
                                ['value' => 'minimal', 'label' => 'Minimal (0-4)'],
                                ['value' => 'mild', 'label' => 'Mild (5-9)'],
                                ['value' => 'moderate', 'label' => 'Moderate (10-14)'],
                                ['value' => 'moderately_severe', 'label' => 'Moderately severe (15-19)'],
                                ['value' => 'severe', 'label' => 'Severe (20-27)'],
                            ],
                        ],
                        [
                            'name' => 'gad7_score',
                            'type' => 'number',
                            'label' => 'GAD-7 Score (Anxiety)',
                            'min' => 0,
                            'max' => 21,
                        ],
                        [
                            'name' => 'gad7_interpretation',
                            'type' => 'select',
                            'label' => 'GAD-7 Interpretation',
                            'options' => [
                                ['value' => 'minimal', 'label' => 'Minimal (0-4)'],
                                ['value' => 'mild', 'label' => 'Mild (5-9)'],
                                ['value' => 'moderate', 'label' => 'Moderate (10-14)'],
                                ['value' => 'severe', 'label' => 'Severe (15-21)'],
                            ],
                        ],
                        [
                            'name' => 'audit_score',
                            'type' => 'number',
                            'label' => 'AUDIT Score (Alcohol)',
                            'min' => 0,
                            'max' => 40,
                        ],
                        [
                            'name' => 'audit_interpretation',
                            'type' => 'select',
                            'label' => 'AUDIT Interpretation',
                            'options' => [
                                ['value' => 'low_risk', 'label' => 'Low risk (0-7)'],
                                ['value' => 'hazardous', 'label' => 'Hazardous (8-15)'],
                                ['value' => 'harmful', 'label' => 'Harmful (16-19)'],
                                ['value' => 'dependence_likely', 'label' => 'Dependence likely (20-40)'],
                            ],
                        ],
                        [
                            'name' => 'ham_d_score',
                            'type' => 'number',
                            'label' => 'HAM-D Score',
                            'min' => 0,
                            'max' => 52,
                        ],
                        [
                            'name' => 'ham_a_score',
                            'type' => 'number',
                            'label' => 'HAM-A Score',
                            'min' => 0,
                            'max' => 56,
                        ],
                        [
                            'name' => 'bprs_score',
                            'type' => 'number',
                            'label' => 'BPRS Score (Brief Psychiatric Rating Scale)',
                            'min' => 18,
                            'max' => 126,
                        ],
                        [
                            'name' => 'ymrs_score',
                            'type' => 'number',
                            'label' => 'YMRS Score (Young Mania Rating Scale)',
                            'min' => 0,
                            'max' => 60,
                        ],
                        [
                            'name' => 'ymrs_interpretation',
                            'type' => 'select',
                            'label' => 'YMRS Interpretation',
                            'options' => [
                                ['value' => 'remission', 'label' => 'Remission (<12)'],
                                ['value' => 'mild', 'label' => 'Mild mania (12-19)'],
                                ['value' => 'moderate', 'label' => 'Moderate mania (20-25)'],
                                ['value' => 'severe', 'label' => 'Severe mania (>25)'],
                            ],
                        ],
                        [
                            'name' => 'cgis_score',
                            'type' => 'select',
                            'label' => 'CGI-S (Severity)',
                            'options' => [
                                ['value' => 1, 'label' => '1 - Normal, not ill'],
                                ['value' => 2, 'label' => '2 - Borderline ill'],
                                ['value' => 3, 'label' => '3 - Mildly ill'],
                                ['value' => 4, 'label' => '4 - Moderately ill'],
                                ['value' => 5, 'label' => '5 - Markedly ill'],
                                ['value' => 6, 'label' => '6 - Severely ill'],
                                ['value' => 7, 'label' => '7 - Extremely ill'],
                            ],
                        ],
                    ],
                ],

                // Diagnosis
                [
                    'id' => 'diagnosis',
                    'title' => 'Diagnosis (ICD-10 with Specifiers)',
                    'fields' => [
                        [
                            'name' => 'provisional_diagnosis',
                            'type' => 'text',
                            'label' => 'Provisional Diagnosis',
                            'required' => true,
                            'autocomplete' => 'icd10_psychiatry',
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
                            'placeholder' => 'e.g., F32.1',
                        ],
                        [
                            'name' => 'severity_specifier',
                            'type' => 'select',
                            'label' => 'Severity Specifier',
                            'options' => ['mild', 'moderate', 'severe', 'severe_with_psychotic_features', 'in_partial_remission', 'in_full_remission'],
                        ],
                        [
                            'name' => 'course_specifier',
                            'type' => 'select',
                            'label' => 'Course Specifier',
                            'options' => ['first_episode', 'recurrent', 'chronic', 'episodic', 'continuous'],
                        ],
                        [
                            'name' => 'comorbid_diagnoses',
                            'type' => 'tags',
                            'label' => 'Comorbid Diagnoses',
                        ],
                    ],
                ],

                // Treatment Plan
                [
                    'id' => 'treatment_plan',
                    'title' => 'Treatment Plan',
                    'fields' => [
                        [
                            'name' => 'pharmacotherapy',
                            'type' => 'textarea',
                            'label' => 'Pharmacotherapy Plan',
                            'placeholder' => 'Medications, doses, titration schedule...',
                        ],
                        [
                            'name' => 'medication_class',
                            'type' => 'multiselect',
                            'label' => 'Medication Classes Prescribed',
                            'options' => [
                                'ssri', 'snri', 'tca', 'maoi', 'mirtazapine',
                                'bupropion', 'typical_antipsychotic', 'atypical_antipsychotic',
                                'mood_stabiliser_lithium', 'mood_stabiliser_valproate',
                                'mood_stabiliser_carbamazepine', 'mood_stabiliser_lamotrigine',
                                'benzodiazepine', 'z_drug', 'melatonin',
                                'clonidine', 'propranolol', 'disulfiram', 'naltrexone',
                                'acamprosate', 'buprenorphine', 'methadone',
                            ],
                        ],
                        [
                            'name' => 'psychotherapy_type',
                            'type' => 'multiselect',
                            'label' => 'Psychotherapy Type',
                            'options' => [
                                'cbt', 'dbt', 'psychodynamic', 'interpersonal',
                                'emdr', 'family_therapy', 'couple_therapy',
                                'group_therapy', 'art_therapy', 'supportive',
                                'motivational_interviewing', 'relapse_prevention',
                                'mindfulness_based', 'act', 'schema_therapy',
                            ],
                        ],
                        [
                            'name' => 'psychotherapy_frequency',
                            'type' => 'select',
                            'label' => 'Psychotherapy Frequency',
                            'options' => [
                                ['value' => 'weekly', 'label' => 'Weekly'],
                                ['value' => 'biweekly', 'label' => 'Biweekly'],
                                ['value' => 'monthly', 'label' => 'Monthly'],
                                ['value' => 'intensive', 'label' => 'Intensive (2-3x/week)'],
                            ],
                        ],
                        [
                            'name' => 'social_interventions',
                            'type' => 'multiselect',
                            'label' => 'Social Interventions',
                            'options' => [
                                'social_worker_referral', 'housing_support',
                                'employment_support', 'financial_advice',
                                'carer_support', 'community_mental_health_team',
                                'crisis_team_referral', 'day_care', 'supported_living',
                                'self_help_groups', 'aa_na_referral',
                            ],
                        ],
                        [
                            'name' => 'admission_recommended',
                            'type' => 'select',
                            'label' => 'Admission Recommended',
                            'options' => ['no', 'voluntary', 'involuntary_assessment', 'day_hospital'],
                        ],
                        [
                            'name' => 'mha_section',
                            'type' => 'text',
                            'label' => 'Mental Health Act Section (if applicable)',
                        ],
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
                            'label' => 'Advice/Instructions',
                        ],
                        [
                            'name' => 'lifestyle_advice',
                            'type' => 'multiselect',
                            'label' => 'Lifestyle Advice',
                            'options' => [
                                'sleep_hygiene', 'regular_exercise', 'structured_routine',
                                'substance_abstinence', 'stress_management',
                                'mindfulness_practice', 'social_engagement',
                                'dietary_advice',
                            ],
                        ],
                        [
                            'name' => 'monitoring_required',
                            'type' => 'multiselect',
                            'label' => 'Monitoring Required',
                            'options' => [
                                'blood_levels_lithium', 'blood_levels_valproate',
                                'blood_levels_clozapine', 'metabolic_monitoring',
                                'ecg', 'bmi_weight', 'blood_pressure', 'lft', 'kft',
                                'thyroid', 'prolactin', 'hba1c', 'lipids',
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
                            'name' => 'emergency_contact',
                            'type' => 'text',
                            'label' => 'Emergency Contact / Crisis Number Given',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Get common psychiatry diagnoses for autocomplete.
     */
    public static function getCommonDiagnoses(): array
    {
        Log::info('PsychiatryTemplate::getCommonDiagnoses() - returning ICD-10 codes');

        return [
            ['code' => 'F32.0', 'name' => 'Depressive episode, mild'],
            ['code' => 'F32.1', 'name' => 'Depressive episode, moderate'],
            ['code' => 'F32.2', 'name' => 'Depressive episode, severe without psychotic features'],
            ['code' => 'F32.3', 'name' => 'Depressive episode, severe with psychotic features'],
            ['code' => 'F33.0', 'name' => 'Recurrent depressive disorder, current episode mild'],
            ['code' => 'F33.1', 'name' => 'Recurrent depressive disorder, current episode moderate'],
            ['code' => 'F33.2', 'name' => 'Recurrent depressive disorder, current episode severe'],
            ['code' => 'F41.1', 'name' => 'Generalised anxiety disorder'],
            ['code' => 'F41.0', 'name' => 'Panic disorder'],
            ['code' => 'F40.10', 'name' => 'Social phobia, unspecified'],
            ['code' => 'F40.00', 'name' => 'Agoraphobia, unspecified'],
            ['code' => 'F20.0', 'name' => 'Paranoid schizophrenia'],
            ['code' => 'F20.9', 'name' => 'Schizophrenia, unspecified'],
            ['code' => 'F25.0', 'name' => 'Schizoaffective disorder, manic type'],
            ['code' => 'F31.0', 'name' => 'Bipolar disorder, current episode hypomanic'],
            ['code' => 'F31.1', 'name' => 'Bipolar disorder, current episode manic without psychotic features'],
            ['code' => 'F31.3', 'name' => 'Bipolar disorder, current episode depressed, mild/moderate'],
            ['code' => 'F31.9', 'name' => 'Bipolar disorder, unspecified'],
            ['code' => 'F10.20', 'name' => 'Alcohol dependence, uncomplicated'],
            ['code' => 'F10.10', 'name' => 'Alcohol abuse, uncomplicated'],
            ['code' => 'F10.239', 'name' => 'Alcohol dependence with withdrawal'],
            ['code' => 'F43.10', 'name' => 'Post-traumatic stress disorder, unspecified'],
            ['code' => 'F43.12', 'name' => 'Post-traumatic stress disorder, chronic'],
            ['code' => 'F42.2', 'name' => 'Obsessive-compulsive disorder, mixed'],
            ['code' => 'F42.0', 'name' => 'OCD, predominantly obsessional thoughts'],
            ['code' => 'F50.00', 'name' => 'Anorexia nervosa, unspecified'],
            ['code' => 'F50.2', 'name' => 'Bulimia nervosa'],
            ['code' => 'F60.3', 'name' => 'Borderline personality disorder'],
            ['code' => 'F60.2', 'name' => 'Antisocial personality disorder'],
            ['code' => 'F60.9', 'name' => 'Personality disorder, unspecified'],
            ['code' => 'F90.0', 'name' => 'ADHD, predominantly inattentive'],
            ['code' => 'F90.2', 'name' => 'ADHD, combined type'],
        ];
    }
}
