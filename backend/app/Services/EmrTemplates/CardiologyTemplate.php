<?php

namespace App\Services\EmrTemplates;

use Illuminate\Support\Facades\Log;

class CardiologyTemplate
{
    /**
     * Get the complete field schema for the Cardiology specialty EMR template.
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
                        'chest_pain_character' => ['type' => 'select', 'label' => 'Chest Pain Character', 'options' => ['Crushing', 'Squeezing', 'Burning', 'Stabbing', 'Pressure', 'Heaviness', 'None']],
                        'dyspnea_grade' => ['type' => 'select', 'label' => 'Dyspnea', 'options' => ['At rest', 'On minimal exertion', 'On moderate exertion', 'On severe exertion', 'None']],
                        'palpitations' => ['type' => 'boolean', 'label' => 'Palpitations'],
                        'syncope' => ['type' => 'boolean', 'label' => 'Syncope/Pre-syncope'],
                        'orthopnea' => ['type' => 'boolean', 'label' => 'Orthopnea'],
                        'pnd' => ['type' => 'boolean', 'label' => 'Paroxysmal Nocturnal Dyspnea'],
                        'pedal_edema_history' => ['type' => 'boolean', 'label' => 'Pedal Edema'],
                    ],
                ],
                'cardiovascular_history' => [
                    'label' => 'Cardiovascular History & Risk Factors',
                    'fields' => [
                        'smoking' => ['type' => 'select', 'label' => 'Smoking Status', 'options' => ['Current', 'Former', 'Never']],
                        'smoking_pack_years' => ['type' => 'number', 'label' => 'Pack-Years'],
                        'diabetes' => ['type' => 'boolean', 'label' => 'Diabetes Mellitus'],
                        'hypertension' => ['type' => 'boolean', 'label' => 'Hypertension'],
                        'dyslipidemia' => ['type' => 'boolean', 'label' => 'Dyslipidemia'],
                        'family_history_cad' => ['type' => 'boolean', 'label' => 'Family History of Premature CAD'],
                        'obesity' => ['type' => 'boolean', 'label' => 'Obesity (BMI > 30)'],
                        'prior_mi' => ['type' => 'boolean', 'label' => 'Prior MI'],
                        'prior_pci_cabg' => ['type' => 'text', 'label' => 'Prior PCI/CABG Details'],
                        'prior_stroke' => ['type' => 'boolean', 'label' => 'Prior Stroke/TIA'],
                        'rheumatic_fever' => ['type' => 'boolean', 'label' => 'History of Rheumatic Fever'],
                    ],
                ],
                'physical_examination' => [
                    'label' => 'Physical Examination',
                    'fields' => [
                        'jvp' => ['type' => 'select', 'label' => 'JVP', 'options' => ['Normal', 'Raised', 'Not assessed']],
                        'jvp_cm' => ['type' => 'number', 'label' => 'JVP (cm above sternal angle)'],
                        'heart_sounds' => ['type' => 'multiselect', 'label' => 'Heart Sounds', 'options' => ['S1 Normal', 'S1 Loud', 'S1 Soft', 'S2 Normal', 'S2 Loud (P2)', 'S2 Paradoxical Split', 'S2 Wide Fixed Split', 'S3 Present', 'S4 Present', 'Opening Snap']],
                        'murmurs' => ['type' => 'textarea', 'label' => 'Murmurs (type, grade, location, radiation)'],
                        'murmur_grade' => ['type' => 'select', 'label' => 'Murmur Grade', 'options' => ['I/VI', 'II/VI', 'III/VI', 'IV/VI', 'V/VI', 'VI/VI', 'None']],
                        'peripheral_pulses' => ['type' => 'select', 'label' => 'Peripheral Pulses', 'options' => ['All present and equal', 'Weak', 'Absent peripherally', 'Radio-femoral delay', 'Irregularly irregular', 'Pulsus paradoxus']],
                        'pedal_edema' => ['type' => 'select', 'label' => 'Pedal Edema', 'options' => ['None', 'Mild (ankle)', 'Moderate (below knee)', 'Severe (above knee)', 'Anasarca']],
                        'lung_auscultation' => ['type' => 'select', 'label' => 'Lung Auscultation', 'options' => ['Clear', 'Bibasal crepitations', 'Bilateral crepitations', 'Pleural effusion', 'Wheeze']],
                        'hepatomegaly' => ['type' => 'boolean', 'label' => 'Hepatomegaly'],
                        'bp_right_arm' => ['type' => 'text', 'label' => 'BP - Right Arm (mmHg)'],
                        'bp_left_arm' => ['type' => 'text', 'label' => 'BP - Left Arm (mmHg)'],
                        'heart_rate' => ['type' => 'number', 'label' => 'Heart Rate (bpm)'],
                        'spo2' => ['type' => 'number', 'label' => 'SpO2 (%)'],
                    ],
                ],
                'ecg_findings' => [
                    'label' => 'ECG Findings',
                    'fields' => [
                        'ecg_rate' => ['type' => 'number', 'label' => 'Rate (bpm)'],
                        'ecg_rhythm' => ['type' => 'select', 'label' => 'Rhythm', 'options' => ['Normal Sinus Rhythm', 'Sinus Bradycardia', 'Sinus Tachycardia', 'Atrial Fibrillation', 'Atrial Flutter', 'SVT', 'VT', 'Junctional', 'Paced Rhythm', 'AV Block (1st degree)', 'AV Block (2nd degree - Mobitz I)', 'AV Block (2nd degree - Mobitz II)', 'Complete Heart Block']],
                        'ecg_axis' => ['type' => 'select', 'label' => 'Axis', 'options' => ['Normal', 'Left Axis Deviation', 'Right Axis Deviation', 'Extreme Axis']],
                        'st_changes' => ['type' => 'multiselect', 'label' => 'ST Changes', 'options' => ['No ST changes', 'ST Elevation (anterior)', 'ST Elevation (inferior)', 'ST Elevation (lateral)', 'ST Depression (anterior)', 'ST Depression (inferior)', 'ST Depression (lateral)', 'ST Depression (diffuse)']],
                        't_wave' => ['type' => 'multiselect', 'label' => 'T Wave Changes', 'options' => ['Normal', 'T Inversion (anterior)', 'T Inversion (inferior)', 'T Inversion (lateral)', 'Tall T waves', 'Flat T waves', 'Biphasic T waves']],
                        'pr_interval' => ['type' => 'text', 'label' => 'PR Interval (ms)'],
                        'qrs_duration' => ['type' => 'text', 'label' => 'QRS Duration (ms)'],
                        'qtc_interval' => ['type' => 'text', 'label' => 'QTc Interval (ms)'],
                        'bundle_branch_block' => ['type' => 'select', 'label' => 'Bundle Branch Block', 'options' => ['None', 'RBBB', 'LBBB', 'LAFB', 'LPFB', 'Bifascicular Block']],
                        'lvh_criteria' => ['type' => 'boolean', 'label' => 'LVH Voltage Criteria Met'],
                        'q_waves' => ['type' => 'text', 'label' => 'Pathological Q Waves (leads)'],
                        'ecg_notes' => ['type' => 'textarea', 'label' => 'Additional ECG Notes'],
                    ],
                ],
                'echo_2d' => [
                    'label' => '2D Echocardiography',
                    'fields' => [
                        'lvef' => ['type' => 'number', 'label' => 'LVEF (%)', 'min' => 5, 'max' => 80],
                        'lvef_method' => ['type' => 'select', 'label' => 'LVEF Method', 'options' => ['Biplane Simpson', 'M-mode', 'Visual estimate', 'Teichholz']],
                        'lvidd' => ['type' => 'number', 'label' => 'LVIDd (mm)'],
                        'lvids' => ['type' => 'number', 'label' => 'LVIDs (mm)'],
                        'ivs_thickness' => ['type' => 'number', 'label' => 'IVS Thickness (mm)'],
                        'pw_thickness' => ['type' => 'number', 'label' => 'PW Thickness (mm)'],
                        'rwma' => ['type' => 'select', 'label' => 'Regional Wall Motion Abnormality', 'options' => ['None', 'Hypokinesia', 'Akinesia', 'Dyskinesia', 'Aneurysmal']],
                        'rwma_segments' => ['type' => 'text', 'label' => 'RWMA Segments'],
                        'la_size' => ['type' => 'number', 'label' => 'LA Size (mm)'],
                        'aortic_root' => ['type' => 'number', 'label' => 'Aortic Root (mm)'],
                        'rv_function' => ['type' => 'select', 'label' => 'RV Function', 'options' => ['Normal', 'Mildly reduced', 'Moderately reduced', 'Severely reduced']],
                        'tapse' => ['type' => 'number', 'label' => 'TAPSE (mm)'],
                        'mitral_valve' => ['type' => 'select', 'label' => 'Mitral Valve', 'options' => ['Normal', 'Mild MR', 'Moderate MR', 'Severe MR', 'MS (mild)', 'MS (moderate)', 'MS (severe)', 'MVP', 'Rheumatic changes']],
                        'aortic_valve' => ['type' => 'select', 'label' => 'Aortic Valve', 'options' => ['Normal', 'Mild AR', 'Moderate AR', 'Severe AR', 'AS (mild)', 'AS (moderate)', 'AS (severe)', 'Bicuspid', 'Calcified', 'Prosthetic']],
                        'tricuspid_valve' => ['type' => 'select', 'label' => 'Tricuspid Valve', 'options' => ['Normal', 'Mild TR', 'Moderate TR', 'Severe TR']],
                        'pulmonary_valve' => ['type' => 'select', 'label' => 'Pulmonary Valve', 'options' => ['Normal', 'Mild PR', 'Moderate PR', 'Severe PR']],
                        'pa_systolic_pressure' => ['type' => 'number', 'label' => 'PA Systolic Pressure (mmHg)'],
                        'diastolic_function' => ['type' => 'select', 'label' => 'Diastolic Function', 'options' => ['Normal', 'Grade I (Impaired relaxation)', 'Grade II (Pseudonormal)', 'Grade III (Restrictive)', 'Indeterminate']],
                        'e_a_ratio' => ['type' => 'text', 'label' => 'E/A Ratio'],
                        'e_prime' => ['type' => 'text', 'label' => "E/e' Ratio"],
                        'pericardial_effusion' => ['type' => 'select', 'label' => 'Pericardial Effusion', 'options' => ['None', 'Trivial', 'Mild', 'Moderate', 'Large', 'Tamponade']],
                        'echo_notes' => ['type' => 'textarea', 'label' => 'Additional Echo Notes'],
                    ],
                ],
                'stress_test' => [
                    'label' => 'Stress Test / TMT',
                    'fields' => [
                        'stress_test_type' => ['type' => 'select', 'label' => 'Test Type', 'options' => ['TMT (Bruce Protocol)', 'TMT (Modified Bruce)', 'Dobutamine Stress Echo', 'Pharmacological MPI', 'Exercise MPI', 'Not done']],
                        'stress_test_result' => ['type' => 'select', 'label' => 'Result', 'options' => ['Negative', 'Positive', 'Equivocal', 'Non-diagnostic', 'Terminated early']],
                        'exercise_duration' => ['type' => 'text', 'label' => 'Exercise Duration (min)'],
                        'mets_achieved' => ['type' => 'number', 'label' => 'METs Achieved'],
                        'max_hr_achieved' => ['type' => 'number', 'label' => 'Max HR Achieved (bpm)'],
                        'target_hr_percent' => ['type' => 'number', 'label' => '% Target HR Achieved'],
                        'bp_response' => ['type' => 'select', 'label' => 'BP Response', 'options' => ['Normal rise', 'Exaggerated rise', 'Hypotensive response', 'Flat response']],
                        'st_depression_tmt' => ['type' => 'text', 'label' => 'ST Depression (mm, leads)'],
                        'symptoms_during_test' => ['type' => 'multiselect', 'label' => 'Symptoms During Test', 'options' => ['None', 'Chest pain', 'Dyspnea', 'Fatigue', 'Dizziness', 'Arrhythmia']],
                        'duke_treadmill_score' => ['type' => 'number', 'label' => 'Duke Treadmill Score'],
                        'stress_test_notes' => ['type' => 'textarea', 'label' => 'Additional Notes'],
                    ],
                ],
                'scales' => [
                    'label' => 'Cardiology Scales & Scores',
                    'fields' => [
                        'nyha_class' => ['type' => 'select', 'label' => 'NYHA Functional Class', 'options' => [
                            ['value' => 1, 'label' => 'Class I - No limitation of physical activity'],
                            ['value' => 2, 'label' => 'Class II - Slight limitation, comfortable at rest'],
                            ['value' => 3, 'label' => 'Class III - Marked limitation, comfortable only at rest'],
                            ['value' => 4, 'label' => 'Class IV - Unable to carry out any activity without discomfort'],
                        ]],
                        'ccs_angina_class' => ['type' => 'select', 'label' => 'CCS Angina Grading', 'options' => [
                            ['value' => 1, 'label' => 'Class I - Angina with strenuous/prolonged exertion only'],
                            ['value' => 2, 'label' => 'Class II - Slight limitation of ordinary activity'],
                            ['value' => 3, 'label' => 'Class III - Marked limitation of ordinary activity'],
                            ['value' => 4, 'label' => 'Class IV - Angina at rest'],
                        ]],
                        'cha2ds2_vasc_chf' => ['type' => 'boolean', 'label' => 'CHA2DS2-VASc: CHF (+1)'],
                        'cha2ds2_vasc_htn' => ['type' => 'boolean', 'label' => 'CHA2DS2-VASc: Hypertension (+1)'],
                        'cha2ds2_vasc_age75' => ['type' => 'boolean', 'label' => 'CHA2DS2-VASc: Age ≥75 (+2)'],
                        'cha2ds2_vasc_dm' => ['type' => 'boolean', 'label' => 'CHA2DS2-VASc: Diabetes (+1)'],
                        'cha2ds2_vasc_stroke' => ['type' => 'boolean', 'label' => 'CHA2DS2-VASc: Stroke/TIA/TE (+2)'],
                        'cha2ds2_vasc_vascular' => ['type' => 'boolean', 'label' => 'CHA2DS2-VASc: Vascular disease (+1)'],
                        'cha2ds2_vasc_age65' => ['type' => 'boolean', 'label' => 'CHA2DS2-VASc: Age 65-74 (+1)'],
                        'cha2ds2_vasc_sex' => ['type' => 'boolean', 'label' => 'CHA2DS2-VASc: Female sex (+1)'],
                        'cha2ds2_vasc_total' => ['type' => 'number', 'label' => 'CHA2DS2-VASc Total Score', 'computed' => true],
                        'hasbled_score' => ['type' => 'number', 'label' => 'HAS-BLED Score'],
                    ],
                ],
                'diagnosis' => [
                    'label' => 'Diagnosis',
                    'fields' => [
                        'provisional_diagnosis' => ['type' => 'text', 'label' => 'Provisional Diagnosis', 'required' => true, 'autocomplete' => 'icd10_cardiology'],
                        'differential_diagnosis' => ['type' => 'tags', 'label' => 'Differential Diagnosis'],
                        'icd_code' => ['type' => 'text', 'label' => 'ICD-10 Code', 'placeholder' => 'e.g., I25.1'],
                    ],
                ],
                'fitness_certificate' => [
                    'label' => 'Fitness Certificate',
                    'fields' => [
                        'fitness_status' => ['type' => 'select', 'label' => 'Fitness Status', 'options' => ['Fit for duty', 'Fit with restrictions', 'Temporarily unfit', 'Permanently unfit', 'Requires further evaluation']],
                        'risk_level' => ['type' => 'select', 'label' => 'Cardiovascular Risk Level', 'options' => ['Low risk', 'Intermediate risk', 'High risk', 'Very high risk']],
                        'restrictions' => ['type' => 'textarea', 'label' => 'Activity Restrictions'],
                        'max_exercise_level' => ['type' => 'select', 'label' => 'Max Exercise Level Allowed', 'options' => ['Sedentary only', 'Light activity', 'Moderate activity', 'Heavy activity', 'No restrictions']],
                        'driving_clearance' => ['type' => 'select', 'label' => 'Driving Clearance', 'options' => ['Cleared', 'Restricted', 'Not cleared', 'Not applicable']],
                        'valid_until' => ['type' => 'date', 'label' => 'Certificate Valid Until'],
                        'review_date' => ['type' => 'date', 'label' => 'Next Review Date'],
                        'fitness_notes' => ['type' => 'textarea', 'label' => 'Additional Notes'],
                    ],
                ],
                'plan' => [
                    'label' => 'Plan & Follow-up',
                    'fields' => [
                        'treatment_plan' => ['type' => 'textarea', 'label' => 'Treatment Plan'],
                        'investigations' => ['type' => 'multiselect', 'label' => 'Investigations', 'options' => ['ECG', '2D Echo', 'TMT', 'Coronary Angiography', 'CT Coronary Angiography', 'Holter Monitor', 'Ambulatory BP', 'Cardiac MRI', 'NT-proBNP', 'Troponin', 'Lipid Profile', 'HbA1c', 'CBC', 'KFT', 'LFT', 'Thyroid Function', 'Coagulation Profile', 'D-Dimer']],
                        'referral' => ['type' => 'text', 'label' => 'Referral To'],
                        'followup_in_days' => ['type' => 'select', 'label' => 'Follow-up', 'options' => [
                            ['value' => 3, 'label' => '3 Days'],
                            ['value' => 7, 'label' => '1 Week'],
                            ['value' => 14, 'label' => '2 Weeks'],
                            ['value' => 30, 'label' => '1 Month'],
                            ['value' => 90, 'label' => '3 Months'],
                            ['value' => 180, 'label' => '6 Months'],
                        ]],
                        'lifestyle_advice' => ['type' => 'textarea', 'label' => 'Lifestyle Advice'],
                        'cardiac_rehab' => ['type' => 'boolean', 'label' => 'Cardiac Rehabilitation Referral'],
                    ],
                ],
            ],
            'scales' => ['NYHA', 'CCS', 'CHA2DS2-VASc', 'HAS-BLED', 'GRACE', 'TIMI'],
            'procedures' => [
                'ecg' => ['label' => 'ECG', 'sac_code' => '999311', 'params' => ['ecg_type']],
                'echo' => ['label' => '2D Echocardiography', 'sac_code' => '999311', 'params' => ['echo_type', 'contrast']],
                'tmt' => ['label' => 'TMT/Stress Test', 'sac_code' => '999311', 'params' => ['protocol', 'duration']],
                'holter' => ['label' => 'Holter Monitor', 'sac_code' => '999311', 'params' => ['duration_hours']],
                'abpm' => ['label' => 'Ambulatory BP Monitoring', 'sac_code' => '999311', 'params' => ['duration_hours']],
            ],
        ];
    }

    /**
     * Default data structure for a new cardiology visit.
     */
    public static function defaultData(): array
    {
        return [
            'chief_complaint' => '',
            'history_present_illness' => '',
            'risk_factors' => ['smoking' => 'Never', 'diabetes' => false, 'hypertension' => false, 'dyslipidemia' => false, 'family_history_cad' => false],
            'ecg' => ['rate' => null, 'rhythm' => '', 'axis' => '', 'st_changes' => [], 't_wave' => []],
            'echo' => ['lvef' => null, 'lvidd' => null, 'lvids' => null, 'rwma' => 'None', 'valves' => []],
            'scales' => ['nyha_class' => null, 'ccs_class' => null, 'cha2ds2_vasc' => null],
            'diagnosis' => ['provisional' => '', 'differential' => [], 'icd10' => ''],
            'fitness_certificate' => ['fitness_status' => '', 'risk_level' => '', 'restrictions' => '', 'valid_until' => ''],
            'plan' => ['treatment' => '', 'follow_up_date' => '', 'follow_up_notes' => ''],
        ];
    }

    /**
     * Get cardiology EMR template fields.
     */
    public static function getFields(): array
    {
        Log::info('Loading Cardiology EMR template');
        Log::info('Cardiology template: building sections array');

        return [
            'specialty' => 'cardiology',
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
                            'placeholder' => 'e.g., 2 days, 3 months',
                        ],
                        [
                            'name' => 'chest_pain_character',
                            'type' => 'select',
                            'label' => 'Chest Pain Character',
                            'options' => ['crushing', 'squeezing', 'burning', 'stabbing', 'pressure', 'heaviness', 'none'],
                        ],
                        [
                            'name' => 'associated_symptoms',
                            'type' => 'multiselect',
                            'label' => 'Associated Symptoms',
                            'options' => [
                                'dyspnea', 'palpitations', 'syncope', 'presyncope',
                                'orthopnea', 'pnd', 'pedal_edema', 'diaphoresis',
                                'nausea', 'jaw_pain', 'left_arm_pain', 'fatigue',
                            ],
                        ],
                    ],
                ],

                [
                    'id' => 'cardiovascular_history',
                    'title' => 'Cardiovascular History',
                    'fields' => [
                        [
                            'name' => 'smoking_status',
                            'type' => 'select',
                            'label' => 'Smoking Status',
                            'options' => ['current', 'former', 'never'],
                        ],
                        [
                            'name' => 'smoking_pack_years',
                            'type' => 'number',
                            'label' => 'Pack-Years',
                        ],
                        [
                            'name' => 'diabetes_mellitus',
                            'type' => 'boolean',
                            'label' => 'Diabetes Mellitus',
                        ],
                        [
                            'name' => 'hypertension',
                            'type' => 'boolean',
                            'label' => 'Hypertension',
                        ],
                        [
                            'name' => 'dyslipidemia',
                            'type' => 'boolean',
                            'label' => 'Dyslipidemia',
                        ],
                        [
                            'name' => 'family_history_cad',
                            'type' => 'boolean',
                            'label' => 'Family History of Premature CAD',
                        ],
                        [
                            'name' => 'prior_cardiac_events',
                            'type' => 'textarea',
                            'label' => 'Prior Cardiac Events (MI, PCI, CABG)',
                        ],
                        [
                            'name' => 'current_medications',
                            'type' => 'textarea',
                            'label' => 'Current Cardiac Medications',
                        ],
                    ],
                ],

                [
                    'id' => 'physical_examination',
                    'title' => 'Physical Examination',
                    'fields' => [
                        [
                            'name' => 'jvp',
                            'type' => 'select',
                            'label' => 'JVP',
                            'options' => ['normal', 'raised', 'not_assessed'],
                        ],
                        [
                            'name' => 'heart_sounds',
                            'type' => 'multiselect',
                            'label' => 'Heart Sounds',
                            'options' => [
                                's1_normal', 's1_loud', 's1_soft',
                                's2_normal', 's2_loud_p2', 's3_present', 's4_present',
                                'opening_snap',
                            ],
                        ],
                        [
                            'name' => 'murmurs',
                            'type' => 'textarea',
                            'label' => 'Murmurs',
                            'placeholder' => 'Type, grade, location, radiation...',
                        ],
                        [
                            'name' => 'peripheral_pulses',
                            'type' => 'select',
                            'label' => 'Peripheral Pulses',
                            'options' => ['all_present_equal', 'weak', 'absent_peripherally', 'radio_femoral_delay', 'irregularly_irregular'],
                        ],
                        [
                            'name' => 'pedal_edema',
                            'type' => 'select',
                            'label' => 'Pedal Edema',
                            'options' => ['none', 'mild_ankle', 'moderate_below_knee', 'severe_above_knee', 'anasarca'],
                        ],
                        [
                            'name' => 'lung_crepitations',
                            'type' => 'select',
                            'label' => 'Lung Auscultation',
                            'options' => ['clear', 'bibasal_crepitations', 'bilateral_crepitations', 'pleural_effusion'],
                        ],
                        [
                            'name' => 'bp_systolic',
                            'type' => 'number',
                            'label' => 'BP Systolic (mmHg)',
                        ],
                        [
                            'name' => 'bp_diastolic',
                            'type' => 'number',
                            'label' => 'BP Diastolic (mmHg)',
                        ],
                    ],
                ],

                [
                    'id' => 'ecg_findings',
                    'title' => 'ECG Findings',
                    'fields' => [
                        [
                            'name' => 'ecg_rate',
                            'type' => 'number',
                            'label' => 'Rate (bpm)',
                        ],
                        [
                            'name' => 'ecg_rhythm',
                            'type' => 'select',
                            'label' => 'Rhythm',
                            'options' => [
                                'normal_sinus', 'sinus_bradycardia', 'sinus_tachycardia',
                                'atrial_fibrillation', 'atrial_flutter', 'svt', 'vt',
                                'complete_heart_block', 'paced_rhythm',
                            ],
                        ],
                        [
                            'name' => 'ecg_axis',
                            'type' => 'select',
                            'label' => 'Axis',
                            'options' => ['normal', 'left_axis_deviation', 'right_axis_deviation', 'extreme_axis'],
                        ],
                        [
                            'name' => 'st_changes',
                            'type' => 'multiselect',
                            'label' => 'ST Changes',
                            'options' => [
                                'no_st_changes', 'st_elevation_anterior', 'st_elevation_inferior',
                                'st_elevation_lateral', 'st_depression_anterior', 'st_depression_inferior',
                                'st_depression_lateral', 'st_depression_diffuse',
                            ],
                        ],
                        [
                            'name' => 't_wave_changes',
                            'type' => 'multiselect',
                            'label' => 'T Wave Changes',
                            'options' => [
                                'normal', 't_inversion_anterior', 't_inversion_inferior',
                                't_inversion_lateral', 'tall_t_waves', 'flat_t_waves',
                            ],
                        ],
                        [
                            'name' => 'pr_interval',
                            'type' => 'text',
                            'label' => 'PR Interval (ms)',
                        ],
                        [
                            'name' => 'qrs_duration',
                            'type' => 'text',
                            'label' => 'QRS Duration (ms)',
                        ],
                        [
                            'name' => 'qtc_interval',
                            'type' => 'text',
                            'label' => 'QTc Interval (ms)',
                        ],
                    ],
                ],

                [
                    'id' => 'echo_2d',
                    'title' => '2D Echocardiography',
                    'fields' => [
                        [
                            'name' => 'lvef',
                            'type' => 'number',
                            'label' => 'LVEF (%)',
                            'min' => 5,
                            'max' => 80,
                        ],
                        [
                            'name' => 'lvidd',
                            'type' => 'number',
                            'label' => 'LVIDd (mm)',
                        ],
                        [
                            'name' => 'lvids',
                            'type' => 'number',
                            'label' => 'LVIDs (mm)',
                        ],
                        [
                            'name' => 'rwma',
                            'type' => 'select',
                            'label' => 'Regional Wall Motion Abnormality',
                            'options' => ['none', 'hypokinesia', 'akinesia', 'dyskinesia', 'aneurysmal'],
                        ],
                        [
                            'name' => 'valve_assessment',
                            'type' => 'textarea',
                            'label' => 'Valve Assessment',
                            'placeholder' => 'MV, AV, TV, PV status...',
                        ],
                        [
                            'name' => 'diastolic_function',
                            'type' => 'select',
                            'label' => 'Diastolic Function',
                            'options' => ['normal', 'grade_1_impaired_relaxation', 'grade_2_pseudonormal', 'grade_3_restrictive'],
                        ],
                        [
                            'name' => 'pa_pressure',
                            'type' => 'number',
                            'label' => 'PA Systolic Pressure (mmHg)',
                        ],
                        [
                            'name' => 'pericardial_effusion',
                            'type' => 'select',
                            'label' => 'Pericardial Effusion',
                            'options' => ['none', 'trivial', 'mild', 'moderate', 'large'],
                        ],
                    ],
                ],

                [
                    'id' => 'stress_test',
                    'title' => 'Stress Test / TMT',
                    'fields' => [
                        [
                            'name' => 'tmt_protocol',
                            'type' => 'select',
                            'label' => 'Protocol',
                            'options' => ['bruce', 'modified_bruce', 'dobutamine_stress_echo', 'pharmacological_mpi', 'not_done'],
                        ],
                        [
                            'name' => 'tmt_result',
                            'type' => 'select',
                            'label' => 'Result',
                            'options' => ['negative', 'positive', 'equivocal', 'non_diagnostic'],
                        ],
                        [
                            'name' => 'mets_achieved',
                            'type' => 'number',
                            'label' => 'METs Achieved',
                        ],
                        [
                            'name' => 'tmt_notes',
                            'type' => 'textarea',
                            'label' => 'TMT Notes',
                        ],
                    ],
                ],

                [
                    'id' => 'scales',
                    'title' => 'Cardiology Scales',
                    'fields' => [
                        [
                            'name' => 'nyha_class',
                            'type' => 'select',
                            'label' => 'NYHA Functional Class',
                            'options' => [
                                ['value' => 1, 'label' => 'Class I - No limitation'],
                                ['value' => 2, 'label' => 'Class II - Slight limitation'],
                                ['value' => 3, 'label' => 'Class III - Marked limitation'],
                                ['value' => 4, 'label' => 'Class IV - Symptoms at rest'],
                            ],
                        ],
                        [
                            'name' => 'ccs_angina_class',
                            'type' => 'select',
                            'label' => 'CCS Angina Grading',
                            'options' => [
                                ['value' => 1, 'label' => 'Class I - Angina with strenuous exertion'],
                                ['value' => 2, 'label' => 'Class II - Slight limitation of ordinary activity'],
                                ['value' => 3, 'label' => 'Class III - Marked limitation of ordinary activity'],
                                ['value' => 4, 'label' => 'Class IV - Angina at rest'],
                            ],
                        ],
                        [
                            'name' => 'cha2ds2_vasc_score',
                            'type' => 'cha2ds2_vasc_calculator',
                            'label' => 'CHA2DS2-VASc Score',
                            'components' => [
                                'chf' => ['label' => 'CHF/LV dysfunction', 'points' => 1],
                                'hypertension' => ['label' => 'Hypertension', 'points' => 1],
                                'age_75_plus' => ['label' => 'Age ≥ 75', 'points' => 2],
                                'diabetes' => ['label' => 'Diabetes', 'points' => 1],
                                'stroke_tia' => ['label' => 'Stroke/TIA/TE', 'points' => 2],
                                'vascular_disease' => ['label' => 'Vascular disease', 'points' => 1],
                                'age_65_74' => ['label' => 'Age 65-74', 'points' => 1],
                                'female_sex' => ['label' => 'Female sex', 'points' => 1],
                            ],
                            'range' => [0, 9],
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
                            'autocomplete' => 'icd10_cardiology',
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
                            'placeholder' => 'e.g., I25.1',
                        ],
                    ],
                ],

                [
                    'id' => 'fitness_certificate',
                    'title' => 'Fitness Certificate',
                    'fields' => [
                        [
                            'name' => 'fitness_status',
                            'type' => 'select',
                            'label' => 'Fitness Status',
                            'options' => ['fit_for_duty', 'fit_with_restrictions', 'temporarily_unfit', 'permanently_unfit', 'requires_further_evaluation'],
                        ],
                        [
                            'name' => 'risk_level',
                            'type' => 'select',
                            'label' => 'Cardiovascular Risk Level',
                            'options' => ['low', 'intermediate', 'high', 'very_high'],
                        ],
                        [
                            'name' => 'restrictions',
                            'type' => 'textarea',
                            'label' => 'Activity Restrictions',
                            'placeholder' => 'Describe activity limitations...',
                        ],
                        [
                            'name' => 'valid_until',
                            'type' => 'date',
                            'label' => 'Certificate Valid Until',
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
                            'placeholder' => 'Medical management, interventional plan...',
                        ],
                        [
                            'name' => 'investigations',
                            'type' => 'multiselect',
                            'label' => 'Investigations',
                            'options' => [
                                'ecg', '2d_echo', 'tmt', 'coronary_angiography',
                                'ct_coronary_angio', 'holter_monitor', 'abpm',
                                'cardiac_mri', 'nt_probnp', 'troponin',
                                'lipid_profile', 'hba1c', 'cbc', 'kft', 'thyroid',
                            ],
                        ],
                        [
                            'name' => 'lifestyle_advice',
                            'type' => 'textarea',
                            'label' => 'Lifestyle Advice',
                            'placeholder' => 'Diet, exercise, smoking cessation...',
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
                                ['value' => 3, 'label' => '3 Days'],
                                ['value' => 7, 'label' => '1 Week'],
                                ['value' => 14, 'label' => '2 Weeks'],
                                ['value' => 30, 'label' => '1 Month'],
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
     * Calculate CHA2DS2-VASc score from component data.
     */
    public static function calculateCha2ds2Vasc(array $components): array
    {
        Log::info('Calculating CHA2DS2-VASc score', ['components' => $components]);

        $score = 0;
        $breakdown = [];

        $factors = [
            'chf' => ['points' => 1, 'label' => 'CHF/LV dysfunction'],
            'hypertension' => ['points' => 1, 'label' => 'Hypertension'],
            'age_75_plus' => ['points' => 2, 'label' => 'Age ≥ 75'],
            'diabetes' => ['points' => 1, 'label' => 'Diabetes'],
            'stroke_tia' => ['points' => 2, 'label' => 'Stroke/TIA/TE'],
            'vascular_disease' => ['points' => 1, 'label' => 'Vascular disease'],
            'age_65_74' => ['points' => 1, 'label' => 'Age 65-74'],
            'female_sex' => ['points' => 1, 'label' => 'Female sex'],
        ];

        foreach ($factors as $key => $factor) {
            $present = $components[$key] ?? false;
            if ($present) {
                $score += $factor['points'];
                $breakdown[] = "{$factor['label']} (+{$factor['points']})";
            }
        }

        $recommendation = match (true) {
            $score === 0 => 'No anticoagulation needed',
            $score === 1 => 'Consider anticoagulation (OAC preferred over aspirin)',
            default => 'Oral anticoagulation recommended',
        };

        Log::info('CHA2DS2-VASc calculated', [
            'score' => $score,
            'recommendation' => $recommendation,
        ]);

        return [
            'total' => $score,
            'breakdown' => $breakdown,
            'recommendation' => $recommendation,
        ];
    }

    /**
     * Get NYHA class interpretation.
     */
    public static function getNyhaInterpretation(int $class): string
    {
        Log::info('Getting NYHA interpretation', ['class' => $class]);

        return match ($class) {
            1 => 'No limitation of physical activity. Ordinary physical activity does not cause undue fatigue, palpitation, or dyspnea.',
            2 => 'Slight limitation of physical activity. Comfortable at rest. Ordinary physical activity results in fatigue, palpitation, or dyspnea.',
            3 => 'Marked limitation of physical activity. Comfortable at rest. Less than ordinary activity causes fatigue, palpitation, or dyspnea.',
            4 => 'Unable to carry out any physical activity without discomfort. Symptoms at rest. Any physical activity causes increased discomfort.',
            default => 'Unknown NYHA class',
        };
    }

    /**
     * Get common cardiology diagnoses for autocomplete.
     */
    public static function getCommonDiagnoses(): array
    {
        Log::info('Fetching common cardiology diagnoses list');

        return [
            ['code' => 'I25.1', 'name' => 'Atherosclerotic heart disease (CAD)'],
            ['code' => 'I25.10', 'name' => 'Atherosclerotic heart disease without angina'],
            ['code' => 'I25.11', 'name' => 'Atherosclerotic heart disease with angina pectoris'],
            ['code' => 'I11.0', 'name' => 'Hypertensive heart disease with heart failure'],
            ['code' => 'I11.9', 'name' => 'Hypertensive heart disease without heart failure'],
            ['code' => 'I50.9', 'name' => 'Heart failure, unspecified'],
            ['code' => 'I50.2', 'name' => 'Systolic (congestive) heart failure'],
            ['code' => 'I50.3', 'name' => 'Diastolic (congestive) heart failure'],
            ['code' => 'I48', 'name' => 'Atrial fibrillation and flutter'],
            ['code' => 'I48.0', 'name' => 'Paroxysmal atrial fibrillation'],
            ['code' => 'I48.1', 'name' => 'Persistent atrial fibrillation'],
            ['code' => 'I48.2', 'name' => 'Chronic atrial fibrillation'],
            ['code' => 'I42.0', 'name' => 'Dilated cardiomyopathy (DCM)'],
            ['code' => 'I42.1', 'name' => 'Obstructive hypertrophic cardiomyopathy (HOCM)'],
            ['code' => 'I35.0', 'name' => 'Aortic valve stenosis'],
            ['code' => 'I35.1', 'name' => 'Aortic valve insufficiency'],
            ['code' => 'I34.0', 'name' => 'Mitral valve insufficiency'],
            ['code' => 'I34.1', 'name' => 'Mitral valve prolapse'],
            ['code' => 'I05.0', 'name' => 'Rheumatic mitral stenosis'],
            ['code' => 'I21.9', 'name' => 'Acute myocardial infarction, unspecified'],
            ['code' => 'I21.0', 'name' => 'Acute ST elevation MI of anterior wall'],
            ['code' => 'I21.1', 'name' => 'Acute ST elevation MI of inferior wall'],
            ['code' => 'I20.0', 'name' => 'Unstable angina'],
            ['code' => 'I20.9', 'name' => 'Angina pectoris, unspecified'],
            ['code' => 'I10', 'name' => 'Essential (primary) hypertension'],
            ['code' => 'I44.1', 'name' => 'Atrioventricular block, second degree'],
            ['code' => 'I44.2', 'name' => 'Atrioventricular block, complete'],
            ['code' => 'I47.2', 'name' => 'Ventricular tachycardia'],
            ['code' => 'I26.9', 'name' => 'Pulmonary embolism'],
            ['code' => 'I27.0', 'name' => 'Primary pulmonary hypertension'],
        ];
    }
}
