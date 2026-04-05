<?php

namespace App\Services\EmrTemplates;

use Illuminate\Support\Facades\Log;

class NeurologyTemplate
{
    /**
     * Get neurology EMR template fields.
     */
    public static function getFields(): array
    {
        Log::info('Loading Neurology EMR template');
        Log::info('NeurologyTemplate::getFields() - building sections array');

        return [
            'specialty' => 'neurology',
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
                            'name' => 'onset',
                            'type' => 'select',
                            'label' => 'Onset',
                            'options' => ['sudden', 'acute', 'subacute', 'gradual', 'progressive'],
                        ],
                        [
                            'name' => 'laterality',
                            'type' => 'select',
                            'label' => 'Laterality',
                            'options' => ['right', 'left', 'bilateral', 'alternating'],
                        ],
                    ],
                ],

                // Neurological History
                [
                    'id' => 'neurological_history',
                    'title' => 'Neurological History',
                    'fields' => [
                        [
                            'name' => 'headache_character',
                            'type' => 'select',
                            'label' => 'Headache Character',
                            'options' => ['throbbing', 'pressing', 'stabbing', 'dull', 'band_like', 'thunderclap', 'none'],
                        ],
                        [
                            'name' => 'headache_location',
                            'type' => 'multiselect',
                            'label' => 'Headache Location',
                            'options' => [
                                'frontal', 'temporal_right', 'temporal_left', 'occipital',
                                'vertex', 'periorbital', 'hemicranial_right', 'hemicranial_left',
                                'diffuse', 'nuchal',
                            ],
                        ],
                        [
                            'name' => 'headache_severity',
                            'type' => 'slider',
                            'label' => 'Headache Severity (0-10)',
                            'min' => 0,
                            'max' => 10,
                            'step' => 1,
                        ],
                        [
                            'name' => 'headache_aura',
                            'type' => 'multiselect',
                            'label' => 'Aura Symptoms',
                            'options' => ['visual_scotoma', 'visual_fortification', 'paresthesia', 'dysphasia', 'motor', 'none'],
                        ],
                        [
                            'name' => 'headache_triggers',
                            'type' => 'multiselect',
                            'label' => 'Headache Triggers',
                            'options' => [
                                'stress', 'sleep_disturbance', 'food', 'weather', 'menstruation',
                                'exertion', 'light', 'noise', 'fasting', 'alcohol',
                            ],
                        ],
                        [
                            'name' => 'seizure_type',
                            'type' => 'select',
                            'label' => 'Seizure Type',
                            'options' => [
                                'none', 'focal_aware', 'focal_impaired_awareness',
                                'focal_to_bilateral_tonic_clonic', 'generalised_tonic_clonic',
                                'absence', 'myoclonic', 'atonic', 'unknown',
                            ],
                        ],
                        [
                            'name' => 'seizure_frequency',
                            'type' => 'text',
                            'label' => 'Seizure Frequency',
                            'placeholder' => 'e.g., 2 per month, weekly',
                        ],
                        [
                            'name' => 'seizure_duration',
                            'type' => 'text',
                            'label' => 'Seizure Duration',
                            'placeholder' => 'e.g., 1-2 minutes',
                        ],
                        [
                            'name' => 'seizure_aura',
                            'type' => 'textarea',
                            'label' => 'Seizure Aura / Warning',
                        ],
                        [
                            'name' => 'post_ictal_state',
                            'type' => 'textarea',
                            'label' => 'Post-ictal State',
                        ],
                        [
                            'name' => 'last_seizure_date',
                            'type' => 'date',
                            'label' => 'Last Seizure Date',
                        ],
                        [
                            'name' => 'weakness_pattern',
                            'type' => 'select',
                            'label' => 'Weakness Pattern',
                            'options' => [
                                'none', 'monoparesis', 'hemiparesis_right', 'hemiparesis_left',
                                'paraparesis', 'quadriparesis', 'proximal', 'distal',
                                'facial', 'bulbar',
                            ],
                        ],
                        [
                            'name' => 'weakness_onset',
                            'type' => 'select',
                            'label' => 'Weakness Onset',
                            'options' => ['sudden', 'progressive', 'fluctuating', 'ascending', 'descending'],
                        ],
                        [
                            'name' => 'sensory_changes',
                            'type' => 'multiselect',
                            'label' => 'Sensory Changes',
                            'options' => [
                                'numbness', 'tingling', 'burning', 'pins_needles',
                                'loss_of_sensation', 'hypersensitivity', 'band_like_sensation',
                                'glove_stocking_distribution',
                            ],
                        ],
                        [
                            'name' => 'gait_issues',
                            'type' => 'multiselect',
                            'label' => 'Gait Issues',
                            'options' => [
                                'unsteadiness', 'dragging_foot', 'wide_based', 'shuffling',
                                'festinant', 'ataxic', 'spastic', 'waddling', 'trendelenburg',
                                'falls', 'freezing',
                            ],
                        ],
                        [
                            'name' => 'sphincter_disturbance',
                            'type' => 'multiselect',
                            'label' => 'Sphincter Disturbance',
                            'options' => ['urinary_incontinence', 'urinary_retention', 'bowel_incontinence', 'constipation', 'none'],
                        ],
                        [
                            'name' => 'visual_symptoms',
                            'type' => 'multiselect',
                            'label' => 'Visual Symptoms',
                            'options' => [
                                'diplopia', 'blurred_vision', 'field_loss', 'oscillopsia',
                                'ptosis', 'nystagmus', 'visual_loss_acute', 'visual_loss_progressive',
                            ],
                        ],
                        [
                            'name' => 'speech_disturbance',
                            'type' => 'select',
                            'label' => 'Speech Disturbance',
                            'options' => ['none', 'dysarthria', 'dysphasia_expressive', 'dysphasia_receptive', 'dysphasia_global', 'dysphonia'],
                        ],
                        [
                            'name' => 'swallowing_difficulty',
                            'type' => 'select',
                            'label' => 'Swallowing Difficulty',
                            'options' => ['none', 'solids', 'liquids', 'both', 'nasal_regurgitation'],
                        ],
                        [
                            'name' => 'memory_complaints',
                            'type' => 'select',
                            'label' => 'Memory/Cognitive Complaints',
                            'options' => ['none', 'forgetfulness', 'word_finding', 'disorientation', 'personality_change', 'behavioral_change'],
                        ],
                        [
                            'name' => 'sleep_disturbance',
                            'type' => 'multiselect',
                            'label' => 'Sleep Disturbance',
                            'options' => ['insomnia', 'excessive_daytime_sleepiness', 'rem_sleep_behaviour', 'restless_legs', 'sleep_apnoea'],
                        ],
                    ],
                ],

                // Neurological Examination - Higher Mental Functions
                [
                    'id' => 'higher_mental_functions',
                    'title' => 'Higher Mental Functions',
                    'fields' => [
                        [
                            'name' => 'consciousness',
                            'type' => 'select',
                            'label' => 'Level of Consciousness',
                            'options' => ['alert', 'drowsy', 'confused', 'stuporous', 'comatose'],
                        ],
                        [
                            'name' => 'orientation',
                            'type' => 'multiselect',
                            'label' => 'Orientation',
                            'options' => ['time', 'place', 'person'],
                        ],
                        [
                            'name' => 'attention',
                            'type' => 'select',
                            'label' => 'Attention',
                            'options' => ['normal', 'impaired'],
                        ],
                        [
                            'name' => 'speech_examination',
                            'type' => 'select',
                            'label' => 'Speech',
                            'options' => ['normal', 'dysarthric', 'expressive_dysphasia', 'receptive_dysphasia', 'global_dysphasia', 'scanning'],
                        ],
                        [
                            'name' => 'memory_examination',
                            'type' => 'select',
                            'label' => 'Memory',
                            'options' => ['normal', 'immediate_impaired', 'recent_impaired', 'remote_impaired', 'global_impairment'],
                        ],
                        [
                            'name' => 'neglect',
                            'type' => 'select',
                            'label' => 'Neglect',
                            'options' => ['none', 'left_visual', 'right_visual', 'left_sensory', 'right_sensory'],
                        ],
                        [
                            'name' => 'apraxia',
                            'type' => 'select',
                            'label' => 'Apraxia',
                            'options' => ['none', 'ideomotor', 'ideational', 'constructional', 'dressing'],
                        ],
                    ],
                ],

                // Cranial Nerves
                [
                    'id' => 'cranial_nerves',
                    'title' => 'Cranial Nerve Examination',
                    'fields' => [
                        [
                            'name' => 'cn_i',
                            'type' => 'select',
                            'label' => 'CN I - Olfactory',
                            'options' => ['normal', 'anosmia_right', 'anosmia_left', 'anosmia_bilateral', 'hyposmia', 'not_tested'],
                        ],
                        [
                            'name' => 'cn_ii_acuity',
                            'type' => 'text',
                            'label' => 'CN II - Visual Acuity (R/L)',
                            'placeholder' => 'e.g., 6/6, 6/12',
                        ],
                        [
                            'name' => 'cn_ii_fields',
                            'type' => 'select',
                            'label' => 'CN II - Visual Fields',
                            'options' => [
                                'normal', 'homonymous_hemianopia_right', 'homonymous_hemianopia_left',
                                'bitemporal_hemianopia', 'quadrantanopia', 'central_scotoma', 'tunnel_vision',
                            ],
                        ],
                        [
                            'name' => 'cn_ii_fundoscopy',
                            'type' => 'select',
                            'label' => 'CN II - Fundoscopy',
                            'options' => ['normal', 'papilloedema', 'optic_atrophy', 'retinal_changes', 'not_done'],
                        ],
                        [
                            'name' => 'cn_ii_rapd',
                            'type' => 'select',
                            'label' => 'CN II - RAPD',
                            'options' => ['absent', 'present_right', 'present_left'],
                        ],
                        [
                            'name' => 'cn_iii_iv_vi',
                            'type' => 'select',
                            'label' => 'CN III, IV, VI - Eye Movements',
                            'options' => ['full_range', 'restricted', 'iii_palsy_right', 'iii_palsy_left', 'iv_palsy_right', 'iv_palsy_left', 'vi_palsy_right', 'vi_palsy_left', 'internuclear_ophthalmoplegia'],
                        ],
                        [
                            'name' => 'pupils',
                            'type' => 'text',
                            'label' => 'Pupils (Size, Shape, Reactivity)',
                            'placeholder' => 'e.g., 3mm bilat, equal, reactive',
                        ],
                        [
                            'name' => 'nystagmus',
                            'type' => 'select',
                            'label' => 'Nystagmus',
                            'options' => ['absent', 'horizontal_right', 'horizontal_left', 'vertical_up', 'vertical_down', 'rotatory', 'multidirectional'],
                        ],
                        [
                            'name' => 'cn_v_sensory',
                            'type' => 'select',
                            'label' => 'CN V - Sensory (V1/V2/V3)',
                            'options' => ['normal', 'v1_impaired', 'v2_impaired', 'v3_impaired', 'all_impaired', 'right_impaired', 'left_impaired'],
                        ],
                        [
                            'name' => 'cn_v_motor',
                            'type' => 'select',
                            'label' => 'CN V - Motor (Jaw)',
                            'options' => ['normal', 'weakness_right', 'weakness_left', 'bilateral_weakness', 'jaw_jerk_brisk'],
                        ],
                        [
                            'name' => 'cn_vii',
                            'type' => 'select',
                            'label' => 'CN VII - Facial Nerve',
                            'options' => [
                                'normal', 'umn_right', 'umn_left', 'lmn_right', 'lmn_left',
                                'bilateral_weakness',
                            ],
                        ],
                        [
                            'name' => 'cn_viii',
                            'type' => 'select',
                            'label' => 'CN VIII - Hearing',
                            'options' => ['normal', 'reduced_right', 'reduced_left', 'reduced_bilateral', 'rinne_abnormal', 'weber_lateralised'],
                        ],
                        [
                            'name' => 'cn_ix_x',
                            'type' => 'select',
                            'label' => 'CN IX, X - Palate/Gag',
                            'options' => ['normal', 'palate_deviation_right', 'palate_deviation_left', 'absent_gag', 'nasal_speech', 'bulbar_palsy', 'pseudobulbar_palsy'],
                        ],
                        [
                            'name' => 'cn_xi',
                            'type' => 'select',
                            'label' => 'CN XI - Sternocleidomastoid/Trapezius',
                            'options' => ['normal', 'weakness_right', 'weakness_left', 'bilateral_weakness'],
                        ],
                        [
                            'name' => 'cn_xii',
                            'type' => 'select',
                            'label' => 'CN XII - Tongue',
                            'options' => ['normal', 'deviation_right', 'deviation_left', 'fasciculations', 'wasting', 'spastic'],
                        ],
                    ],
                ],

                // Motor Examination
                [
                    'id' => 'motor_exam',
                    'title' => 'Motor Examination',
                    'type' => 'motor_assessment',
                    'fields' => [
                        [
                            'name' => 'bulk',
                            'type' => 'select',
                            'label' => 'Bulk',
                            'options' => ['normal', 'wasting_proximal', 'wasting_distal', 'wasting_generalised', 'pseudohypertrophy'],
                        ],
                        [
                            'name' => 'fasciculations',
                            'type' => 'select',
                            'label' => 'Fasciculations',
                            'options' => ['absent', 'present_upper_limbs', 'present_lower_limbs', 'present_tongue', 'widespread'],
                        ],
                        [
                            'name' => 'tone_upper_right',
                            'type' => 'select',
                            'label' => 'Tone - Upper Limb Right',
                            'options' => ['normal', 'hypotonia', 'spasticity_mild', 'spasticity_moderate', 'spasticity_severe', 'rigidity', 'cogwheel'],
                        ],
                        [
                            'name' => 'tone_upper_left',
                            'type' => 'select',
                            'label' => 'Tone - Upper Limb Left',
                            'options' => ['normal', 'hypotonia', 'spasticity_mild', 'spasticity_moderate', 'spasticity_severe', 'rigidity', 'cogwheel'],
                        ],
                        [
                            'name' => 'tone_lower_right',
                            'type' => 'select',
                            'label' => 'Tone - Lower Limb Right',
                            'options' => ['normal', 'hypotonia', 'spasticity_mild', 'spasticity_moderate', 'spasticity_severe', 'rigidity', 'cogwheel'],
                        ],
                        [
                            'name' => 'tone_lower_left',
                            'type' => 'select',
                            'label' => 'Tone - Lower Limb Left',
                            'options' => ['normal', 'hypotonia', 'spasticity_mild', 'spasticity_moderate', 'spasticity_severe', 'rigidity', 'cogwheel'],
                        ],
                        [
                            'name' => 'power_mrc_grading_info',
                            'type' => 'info',
                            'label' => 'MRC Power Grading',
                            'content' => '0=No contraction, 1=Flicker/trace, 2=Active movement gravity eliminated, 3=Active movement against gravity, 4-=Movement against slight resistance, 4=Movement against moderate resistance, 4+=Movement against strong resistance, 5=Normal',
                        ],
                        [
                            'name' => 'power_upper_limb',
                            'type' => 'power_table',
                            'label' => 'Power - Upper Limbs (MRC)',
                            'muscles' => [
                                ['name' => 'Shoulder Abduction (C5)', 'columns' => ['right', 'left']],
                                ['name' => 'Elbow Flexion (C5,C6)', 'columns' => ['right', 'left']],
                                ['name' => 'Elbow Extension (C7)', 'columns' => ['right', 'left']],
                                ['name' => 'Wrist Extension (C6,C7)', 'columns' => ['right', 'left']],
                                ['name' => 'Finger Extension (C7)', 'columns' => ['right', 'left']],
                                ['name' => 'Finger Flexion (C8)', 'columns' => ['right', 'left']],
                                ['name' => 'Hand Intrinsics (T1)', 'columns' => ['right', 'left']],
                            ],
                            'grading' => [0, 1, 2, 3, '4-', 4, '4+', 5],
                        ],
                        [
                            'name' => 'power_lower_limb',
                            'type' => 'power_table',
                            'label' => 'Power - Lower Limbs (MRC)',
                            'muscles' => [
                                ['name' => 'Hip Flexion (L1,L2)', 'columns' => ['right', 'left']],
                                ['name' => 'Hip Extension (L5,S1)', 'columns' => ['right', 'left']],
                                ['name' => 'Knee Extension (L3,L4)', 'columns' => ['right', 'left']],
                                ['name' => 'Knee Flexion (L5,S1)', 'columns' => ['right', 'left']],
                                ['name' => 'Ankle Dorsiflexion (L4,L5)', 'columns' => ['right', 'left']],
                                ['name' => 'Ankle Plantarflexion (S1,S2)', 'columns' => ['right', 'left']],
                                ['name' => 'Toe Extension (L5)', 'columns' => ['right', 'left']],
                            ],
                            'grading' => [0, 1, 2, 3, '4-', 4, '4+', 5],
                        ],
                        [
                            'name' => 'reflexes_biceps_r',
                            'type' => 'select',
                            'label' => 'Biceps Reflex (C5,C6) - Right',
                            'options' => ['absent', 'diminished', 'normal', 'brisk', 'clonus'],
                        ],
                        [
                            'name' => 'reflexes_biceps_l',
                            'type' => 'select',
                            'label' => 'Biceps Reflex (C5,C6) - Left',
                            'options' => ['absent', 'diminished', 'normal', 'brisk', 'clonus'],
                        ],
                        [
                            'name' => 'reflexes_triceps_r',
                            'type' => 'select',
                            'label' => 'Triceps Reflex (C7) - Right',
                            'options' => ['absent', 'diminished', 'normal', 'brisk', 'clonus'],
                        ],
                        [
                            'name' => 'reflexes_triceps_l',
                            'type' => 'select',
                            'label' => 'Triceps Reflex (C7) - Left',
                            'options' => ['absent', 'diminished', 'normal', 'brisk', 'clonus'],
                        ],
                        [
                            'name' => 'reflexes_supinator_r',
                            'type' => 'select',
                            'label' => 'Supinator Reflex (C6) - Right',
                            'options' => ['absent', 'diminished', 'normal', 'brisk', 'inverted'],
                        ],
                        [
                            'name' => 'reflexes_supinator_l',
                            'type' => 'select',
                            'label' => 'Supinator Reflex (C6) - Left',
                            'options' => ['absent', 'diminished', 'normal', 'brisk', 'inverted'],
                        ],
                        [
                            'name' => 'reflexes_knee_r',
                            'type' => 'select',
                            'label' => 'Knee Reflex (L3,L4) - Right',
                            'options' => ['absent', 'diminished', 'normal', 'brisk', 'clonus'],
                        ],
                        [
                            'name' => 'reflexes_knee_l',
                            'type' => 'select',
                            'label' => 'Knee Reflex (L3,L4) - Left',
                            'options' => ['absent', 'diminished', 'normal', 'brisk', 'clonus'],
                        ],
                        [
                            'name' => 'reflexes_ankle_r',
                            'type' => 'select',
                            'label' => 'Ankle Reflex (S1) - Right',
                            'options' => ['absent', 'diminished', 'normal', 'brisk', 'clonus'],
                        ],
                        [
                            'name' => 'reflexes_ankle_l',
                            'type' => 'select',
                            'label' => 'Ankle Reflex (S1) - Left',
                            'options' => ['absent', 'diminished', 'normal', 'brisk', 'clonus'],
                        ],
                        [
                            'name' => 'plantar_right',
                            'type' => 'select',
                            'label' => 'Plantar Response - Right',
                            'options' => ['flexor', 'extensor', 'equivocal', 'mute'],
                        ],
                        [
                            'name' => 'plantar_left',
                            'type' => 'select',
                            'label' => 'Plantar Response - Left',
                            'options' => ['flexor', 'extensor', 'equivocal', 'mute'],
                        ],
                    ],
                ],

                // Sensory Examination
                [
                    'id' => 'sensory_exam',
                    'title' => 'Sensory Examination',
                    'fields' => [
                        [
                            'name' => 'light_touch',
                            'type' => 'select',
                            'label' => 'Light Touch',
                            'options' => ['normal', 'impaired_right', 'impaired_left', 'impaired_bilateral', 'glove_stocking', 'dermatomal', 'sensory_level'],
                        ],
                        [
                            'name' => 'pinprick',
                            'type' => 'select',
                            'label' => 'Pinprick',
                            'options' => ['normal', 'impaired_right', 'impaired_left', 'impaired_bilateral', 'glove_stocking', 'dermatomal', 'sensory_level'],
                        ],
                        [
                            'name' => 'vibration',
                            'type' => 'select',
                            'label' => 'Vibration (128Hz tuning fork)',
                            'options' => ['normal', 'impaired_distally', 'impaired_right', 'impaired_left', 'absent_distally'],
                        ],
                        [
                            'name' => 'proprioception',
                            'type' => 'select',
                            'label' => 'Proprioception (Joint Position Sense)',
                            'options' => ['normal', 'impaired_toes', 'impaired_fingers', 'impaired_both', 'absent'],
                        ],
                        [
                            'name' => 'cortical_sensation',
                            'type' => 'multiselect',
                            'label' => 'Cortical Sensation',
                            'options' => [
                                'two_point_discrimination_normal', 'two_point_discrimination_impaired',
                                'stereognosis_normal', 'stereognosis_impaired',
                                'graphaesthesia_normal', 'graphaesthesia_impaired',
                                'sensory_inattention_present',
                            ],
                        ],
                        [
                            'name' => 'sensory_level',
                            'type' => 'text',
                            'label' => 'Sensory Level (if present)',
                            'placeholder' => 'e.g., T10, L1',
                        ],
                        [
                            'name' => 'sensory_notes',
                            'type' => 'textarea',
                            'label' => 'Sensory Examination Notes',
                        ],
                    ],
                ],

                // Cerebellar Examination
                [
                    'id' => 'cerebellar',
                    'title' => 'Cerebellar Examination',
                    'fields' => [
                        [
                            'name' => 'finger_nose_test',
                            'type' => 'select',
                            'label' => 'Finger-Nose Test',
                            'options' => ['normal', 'dysmetria_right', 'dysmetria_left', 'dysmetria_bilateral', 'intention_tremor_right', 'intention_tremor_left'],
                        ],
                        [
                            'name' => 'heel_shin_test',
                            'type' => 'select',
                            'label' => 'Heel-Shin Test',
                            'options' => ['normal', 'impaired_right', 'impaired_left', 'impaired_bilateral'],
                        ],
                        [
                            'name' => 'rapid_alternating_movements',
                            'type' => 'select',
                            'label' => 'Rapid Alternating Movements (Dysdiadochokinesia)',
                            'options' => ['normal', 'impaired_right', 'impaired_left', 'impaired_bilateral'],
                        ],
                        [
                            'name' => 'romberg_test',
                            'type' => 'select',
                            'label' => 'Romberg Test',
                            'options' => ['negative', 'positive', 'not_testable'],
                        ],
                        [
                            'name' => 'tandem_gait',
                            'type' => 'select',
                            'label' => 'Tandem Gait',
                            'options' => ['normal', 'impaired', 'unable'],
                        ],
                        [
                            'name' => 'nystagmus_cerebellar',
                            'type' => 'textarea',
                            'label' => 'Cerebellar Nystagmus Notes',
                        ],
                    ],
                ],

                // Gait Assessment
                [
                    'id' => 'gait',
                    'title' => 'Gait Assessment',
                    'fields' => [
                        [
                            'name' => 'gait_pattern',
                            'type' => 'select',
                            'label' => 'Gait Pattern',
                            'options' => [
                                'normal', 'hemiplegic', 'diplegic', 'ataxic_cerebellar',
                                'ataxic_sensory', 'parkinsonian_shuffling', 'steppage',
                                'waddling', 'scissoring', 'antalgic', 'festinant',
                                'marche_a_petits_pas', 'apraxic',
                            ],
                        ],
                        [
                            'name' => 'gait_aids',
                            'type' => 'select',
                            'label' => 'Gait Aids',
                            'options' => ['none', 'walking_stick', 'frame', 'wheelchair', 'bed_bound'],
                        ],
                        [
                            'name' => 'gait_notes',
                            'type' => 'textarea',
                            'label' => 'Gait Notes',
                        ],
                    ],
                ],

                // Scales
                [
                    'id' => 'scales',
                    'title' => 'Neurological Scales & Scoring',
                    'fields' => [
                        [
                            'name' => 'gcs_eye',
                            'type' => 'select',
                            'label' => 'GCS - Eye Opening',
                            'options' => [
                                ['value' => 4, 'label' => '4 - Spontaneous'],
                                ['value' => 3, 'label' => '3 - To voice'],
                                ['value' => 2, 'label' => '2 - To pressure'],
                                ['value' => 1, 'label' => '1 - None'],
                            ],
                        ],
                        [
                            'name' => 'gcs_verbal',
                            'type' => 'select',
                            'label' => 'GCS - Verbal Response',
                            'options' => [
                                ['value' => 5, 'label' => '5 - Oriented'],
                                ['value' => 4, 'label' => '4 - Confused'],
                                ['value' => 3, 'label' => '3 - Inappropriate words'],
                                ['value' => 2, 'label' => '2 - Incomprehensible sounds'],
                                ['value' => 1, 'label' => '1 - None'],
                            ],
                        ],
                        [
                            'name' => 'gcs_motor',
                            'type' => 'select',
                            'label' => 'GCS - Motor Response',
                            'options' => [
                                ['value' => 6, 'label' => '6 - Obeys commands'],
                                ['value' => 5, 'label' => '5 - Localises pain'],
                                ['value' => 4, 'label' => '4 - Normal flexion'],
                                ['value' => 3, 'label' => '3 - Abnormal flexion'],
                                ['value' => 2, 'label' => '2 - Extension'],
                                ['value' => 1, 'label' => '1 - None'],
                            ],
                        ],
                        [
                            'name' => 'gcs_total',
                            'type' => 'number',
                            'label' => 'GCS Total (/15)',
                            'min' => 3,
                            'max' => 15,
                            'computed' => true,
                        ],
                        [
                            'name' => 'nihss_score',
                            'type' => 'nihss_calculator',
                            'label' => 'NIHSS Score (0-42)',
                            'min' => 0,
                            'max' => 42,
                        ],
                        [
                            'name' => 'nihss_interpretation',
                            'type' => 'select',
                            'label' => 'NIHSS Interpretation',
                            'options' => [
                                ['value' => 'no_stroke', 'label' => '0 - No stroke symptoms'],
                                ['value' => 'minor', 'label' => '1-4 - Minor stroke'],
                                ['value' => 'moderate', 'label' => '5-15 - Moderate stroke'],
                                ['value' => 'moderate_severe', 'label' => '16-20 - Moderate to severe stroke'],
                                ['value' => 'severe', 'label' => '21-42 - Severe stroke'],
                            ],
                        ],
                        [
                            'name' => 'modified_rankin',
                            'type' => 'select',
                            'label' => 'Modified Rankin Scale',
                            'options' => [
                                ['value' => 0, 'label' => '0 - No symptoms'],
                                ['value' => 1, 'label' => '1 - No significant disability'],
                                ['value' => 2, 'label' => '2 - Slight disability'],
                                ['value' => 3, 'label' => '3 - Moderate disability (requires some help)'],
                                ['value' => 4, 'label' => '4 - Moderately severe disability (unable to walk unassisted)'],
                                ['value' => 5, 'label' => '5 - Severe disability (requires constant care)'],
                                ['value' => 6, 'label' => '6 - Dead'],
                            ],
                        ],
                        [
                            'name' => 'mmse_score',
                            'type' => 'number',
                            'label' => 'MMSE Score (/30)',
                            'min' => 0,
                            'max' => 30,
                        ],
                        [
                            'name' => 'moca_score',
                            'type' => 'number',
                            'label' => 'MoCA Score (/30)',
                            'min' => 0,
                            'max' => 30,
                        ],
                        [
                            'name' => 'moca_interpretation',
                            'type' => 'select',
                            'label' => 'MoCA Interpretation',
                            'options' => [
                                ['value' => 'normal', 'label' => 'Normal (≥26)'],
                                ['value' => 'mci', 'label' => 'Mild Cognitive Impairment (18-25)'],
                                ['value' => 'moderate', 'label' => 'Moderate Impairment (10-17)'],
                                ['value' => 'severe', 'label' => 'Severe Impairment (<10)'],
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
                            'autocomplete' => 'icd10_neurology',
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
                            'placeholder' => 'e.g., G40.9',
                        ],
                        [
                            'name' => 'neurological_localisation',
                            'type' => 'select',
                            'label' => 'Neurological Localisation',
                            'options' => [
                                'cortical', 'subcortical', 'brainstem', 'cerebellar',
                                'spinal_cord', 'nerve_root', 'plexus', 'peripheral_nerve',
                                'neuromuscular_junction', 'muscle', 'multifocal',
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
                            'name' => 'investigations_ordered',
                            'type' => 'multiselect',
                            'label' => 'Investigations',
                            'options' => [
                                'mri_brain', 'mri_spine', 'ct_brain', 'ct_angiography',
                                'eeg', 'emg_ncs', 'visual_evoked_potential', 'ssep', 'baep',
                                'csf_analysis', 'carotid_doppler', 'tcd',
                                'cbc', 'esr', 'crp', 'blood_sugar', 'hba1c', 'lipid_profile',
                                'thyroid', 'b12', 'folate', 'copper', 'ceruloplasmin',
                                'ana', 'anca', 'anti_nmda', 'anti_ganglioside',
                            ],
                        ],
                        [
                            'name' => 'neurorehabilitation',
                            'type' => 'multiselect',
                            'label' => 'Neurorehabilitation',
                            'options' => [
                                'physiotherapy', 'occupational_therapy', 'speech_therapy',
                                'cognitive_rehabilitation', 'vocational_rehabilitation',
                            ],
                        ],
                        [
                            'name' => 'driving_advice',
                            'type' => 'select',
                            'label' => 'Driving Advice',
                            'options' => ['no_restriction', 'cannot_drive_temporarily', 'cannot_drive_permanently', 'discussed'],
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
     * Get common neurology diagnoses for autocomplete.
     */
    public static function getCommonDiagnoses(): array
    {
        Log::info('NeurologyTemplate::getCommonDiagnoses() - returning ICD-10 codes');

        return [
            ['code' => 'G40.009', 'name' => 'Epilepsy, unspecified, not intractable'],
            ['code' => 'G40.109', 'name' => 'Epilepsy with localisation-related seizures'],
            ['code' => 'G40.309', 'name' => 'Generalised idiopathic epilepsy'],
            ['code' => 'G40.909', 'name' => 'Epilepsy, unspecified'],
            ['code' => 'G43.009', 'name' => 'Migraine without aura, not intractable'],
            ['code' => 'G43.109', 'name' => 'Migraine with aura, not intractable'],
            ['code' => 'G43.909', 'name' => 'Migraine, unspecified'],
            ['code' => 'G44.1', 'name' => 'Vascular headache (cluster headache)'],
            ['code' => 'G20', 'name' => 'Parkinson\'s disease'],
            ['code' => 'G35', 'name' => 'Multiple sclerosis'],
            ['code' => 'I63.9', 'name' => 'Cerebral infarction, unspecified'],
            ['code' => 'I63.5', 'name' => 'Cerebral infarction due to unspecified occlusion of cerebral artery'],
            ['code' => 'I61.9', 'name' => 'Non-traumatic intracerebral haemorrhage, unspecified'],
            ['code' => 'I61.0', 'name' => 'Intracerebral haemorrhage in hemisphere, subcortical'],
            ['code' => 'G61.0', 'name' => 'Guillain-Barré syndrome'],
            ['code' => 'G70.00', 'name' => 'Myasthenia gravis without exacerbation'],
            ['code' => 'G70.01', 'name' => 'Myasthenia gravis with exacerbation'],
            ['code' => 'G30.9', 'name' => 'Alzheimer\'s disease, unspecified'],
            ['code' => 'G30.0', 'name' => 'Alzheimer\'s disease with early onset'],
            ['code' => 'G62.9', 'name' => 'Polyneuropathy, unspecified'],
            ['code' => 'G62.1', 'name' => 'Alcoholic polyneuropathy'],
            ['code' => 'G62.0', 'name' => 'Drug-induced polyneuropathy'],
            ['code' => 'G56.0', 'name' => 'Carpal tunnel syndrome'],
            ['code' => 'G51.0', 'name' => 'Bell\'s palsy'],
            ['code' => 'G12.21', 'name' => 'Amyotrophic lateral sclerosis'],
            ['code' => 'G91.9', 'name' => 'Hydrocephalus, unspecified'],
            ['code' => 'G47.33', 'name' => 'Obstructive sleep apnoea'],
            ['code' => 'R56.9', 'name' => 'Unspecified convulsions'],
        ];
    }
}
