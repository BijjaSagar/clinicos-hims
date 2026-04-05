<?php

namespace App\Services\EmrTemplates;

use Illuminate\Support\Facades\Log;

class GeneralSurgeryTemplate
{
    /**
     * Get general surgery EMR template fields.
     */
    public static function getFields(): array
    {
        Log::info('Loading General Surgery EMR template');
        Log::info('GeneralSurgeryTemplate::getFields() - building sections array');

        return [
            'specialty' => 'general_surgery',
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
                            'placeholder' => 'e.g., 3 days, 2 months',
                        ],
                        [
                            'name' => 'onset',
                            'type' => 'select',
                            'label' => 'Onset',
                            'options' => ['sudden', 'gradual', 'insidious'],
                        ],
                        [
                            'name' => 'progression',
                            'type' => 'select',
                            'label' => 'Progression',
                            'options' => ['increasing', 'decreasing', 'static', 'fluctuating'],
                        ],
                        [
                            'name' => 'history_present_illness',
                            'type' => 'textarea',
                            'label' => 'History of Present Illness',
                        ],
                    ],
                ],

                // Surgical History
                [
                    'id' => 'surgical_history',
                    'title' => 'Surgical History',
                    'fields' => [
                        [
                            'name' => 'previous_surgeries',
                            'type' => 'textarea',
                            'label' => 'Previous Surgeries',
                            'placeholder' => 'List previous surgeries with year...',
                        ],
                        [
                            'name' => 'anesthesia_history',
                            'type' => 'select',
                            'label' => 'Anesthesia History',
                            'options' => ['no_prior_anesthesia', 'general_uneventful', 'general_complications', 'spinal_uneventful', 'spinal_complications', 'local_uneventful'],
                        ],
                        [
                            'name' => 'anesthesia_complications_details',
                            'type' => 'textarea',
                            'label' => 'Anesthesia Complications (if any)',
                        ],
                        [
                            'name' => 'dvt_pe_history',
                            'type' => 'select',
                            'label' => 'DVT/PE History',
                            'options' => ['none', 'dvt_previous', 'pe_previous', 'dvt_and_pe', 'family_history_dvt_pe'],
                        ],
                        [
                            'name' => 'dvt_pe_details',
                            'type' => 'textarea',
                            'label' => 'DVT/PE Details',
                        ],
                        [
                            'name' => 'blood_transfusion_history',
                            'type' => 'select',
                            'label' => 'Blood Transfusion History',
                            'options' => ['none', 'previous_uneventful', 'previous_reaction'],
                        ],
                    ],
                ],

                // Pre-operative Assessment
                [
                    'id' => 'preoperative_assessment',
                    'title' => 'Pre-operative Assessment',
                    'fields' => [
                        [
                            'name' => 'asa_classification',
                            'type' => 'select',
                            'label' => 'ASA Classification',
                            'required' => true,
                            'options' => [
                                ['value' => 1, 'label' => 'ASA I - Normal healthy patient'],
                                ['value' => 2, 'label' => 'ASA II - Mild systemic disease'],
                                ['value' => 3, 'label' => 'ASA III - Severe systemic disease'],
                                ['value' => 4, 'label' => 'ASA IV - Severe systemic disease, constant threat to life'],
                                ['value' => 5, 'label' => 'ASA V - Moribund, not expected to survive without surgery'],
                                ['value' => 6, 'label' => 'ASA VI - Brain-dead organ donor'],
                            ],
                        ],
                        [
                            'name' => 'airway_assessment',
                            'type' => 'select',
                            'label' => 'Airway Assessment (Mallampati)',
                            'options' => [
                                ['value' => 1, 'label' => 'Class I - Soft palate, fauces, uvula, pillars visible'],
                                ['value' => 2, 'label' => 'Class II - Soft palate, fauces, uvula visible'],
                                ['value' => 3, 'label' => 'Class III - Soft palate, base of uvula visible'],
                                ['value' => 4, 'label' => 'Class IV - Hard palate only visible'],
                            ],
                        ],
                        [
                            'name' => 'comorbidities',
                            'type' => 'multiselect',
                            'label' => 'Comorbidities',
                            'options' => [
                                'diabetes', 'hypertension', 'ihd', 'copd', 'asthma',
                                'ckd', 'liver_disease', 'thyroid', 'obesity', 'anemia',
                                'coagulopathy', 'epilepsy', 'stroke', 'cardiac_failure',
                            ],
                        ],
                        [
                            'name' => 'medications_anticoagulants',
                            'type' => 'multiselect',
                            'label' => 'Anticoagulants / Antiplatelets',
                            'options' => [
                                'aspirin', 'clopidogrel', 'warfarin', 'enoxaparin',
                                'rivaroxaban', 'apixaban', 'dabigatran', 'heparin', 'none',
                            ],
                        ],
                        [
                            'name' => 'anticoagulant_stopped',
                            'type' => 'boolean',
                            'label' => 'Anticoagulant Stopped Pre-operatively',
                        ],
                        [
                            'name' => 'fasting_status',
                            'type' => 'select',
                            'label' => 'Fasting Status',
                            'options' => [
                                'nil_by_mouth_6hrs', 'nil_by_mouth_8hrs',
                                'clear_fluids_2hrs', 'not_fasting', 'emergency_not_fasted',
                            ],
                        ],
                        [
                            'name' => 'consent_obtained',
                            'type' => 'boolean',
                            'label' => 'Informed Consent Obtained',
                            'required' => true,
                        ],
                        [
                            'name' => 'consent_details',
                            'type' => 'textarea',
                            'label' => 'Consent Details',
                            'placeholder' => 'Procedure explained, risks discussed, patient questions addressed...',
                        ],
                        [
                            'name' => 'high_risk_consent',
                            'type' => 'boolean',
                            'label' => 'High-Risk Consent Obtained',
                        ],
                    ],
                ],

                // Physical Examination - Local Examination
                [
                    'id' => 'physical_examination',
                    'title' => 'Physical Examination',
                    'fields' => [
                        [
                            'name' => 'general_condition',
                            'type' => 'select',
                            'label' => 'General Condition',
                            'options' => ['stable', 'guarded', 'critical', 'moribund'],
                        ],
                        [
                            'name' => 'vitals_bp',
                            'type' => 'text',
                            'label' => 'Blood Pressure (mmHg)',
                            'placeholder' => 'e.g., 120/80',
                        ],
                        [
                            'name' => 'vitals_pulse',
                            'type' => 'number',
                            'label' => 'Pulse (bpm)',
                        ],
                        [
                            'name' => 'vitals_temp',
                            'type' => 'number',
                            'label' => 'Temperature (°F)',
                        ],
                        [
                            'name' => 'vitals_spo2',
                            'type' => 'number',
                            'label' => 'SpO2 (%)',
                        ],
                        [
                            'name' => 'local_exam_site',
                            'type' => 'text',
                            'label' => 'Site',
                            'placeholder' => 'Anatomical location...',
                        ],
                        [
                            'name' => 'local_exam_size',
                            'type' => 'text',
                            'label' => 'Size',
                            'placeholder' => 'e.g., 3x4 cm',
                        ],
                        [
                            'name' => 'local_exam_shape',
                            'type' => 'select',
                            'label' => 'Shape',
                            'options' => ['round', 'oval', 'irregular', 'lobulated', 'pedunculated', 'sessile'],
                        ],
                        [
                            'name' => 'local_exam_surface',
                            'type' => 'select',
                            'label' => 'Surface',
                            'options' => ['smooth', 'rough', 'irregular', 'nodular', 'ulcerated', 'fungating'],
                        ],
                        [
                            'name' => 'local_exam_edges',
                            'type' => 'select',
                            'label' => 'Edges',
                            'options' => ['well_defined', 'ill_defined', 'raised', 'everted', 'undermined', 'rolled', 'sloping'],
                        ],
                        [
                            'name' => 'local_exam_consistency',
                            'type' => 'select',
                            'label' => 'Consistency',
                            'options' => ['soft', 'firm', 'hard', 'cystic', 'fluctuant', 'bony_hard', 'rubbery', 'doughy'],
                        ],
                        [
                            'name' => 'local_exam_tenderness',
                            'type' => 'select',
                            'label' => 'Tenderness',
                            'options' => ['none', 'mild', 'moderate', 'severe', 'rebound_tenderness'],
                        ],
                        [
                            'name' => 'local_exam_fixity',
                            'type' => 'multiselect',
                            'label' => 'Fixity',
                            'options' => ['mobile', 'fixed_to_skin', 'fixed_to_underlying_structures', 'fixed_to_muscle', 'tethered'],
                        ],
                        [
                            'name' => 'local_exam_transillumination',
                            'type' => 'select',
                            'label' => 'Transillumination',
                            'options' => ['positive', 'negative', 'not_applicable'],
                        ],
                        [
                            'name' => 'local_exam_additional',
                            'type' => 'textarea',
                            'label' => 'Additional Examination Findings',
                        ],
                    ],
                ],

                // Wound Assessment
                [
                    'id' => 'wound_assessment',
                    'title' => 'Wound Assessment',
                    'fields' => [
                        [
                            'name' => 'wound_type',
                            'type' => 'select',
                            'label' => 'Wound Type',
                            'options' => [
                                ['value' => 'clean', 'label' => 'Clean - No infection, no hollow viscus entered'],
                                ['value' => 'clean_contaminated', 'label' => 'Clean-Contaminated - Hollow viscus entered, controlled'],
                                ['value' => 'contaminated', 'label' => 'Contaminated - Open wound, spillage from GI tract'],
                                ['value' => 'dirty', 'label' => 'Dirty/Infected - Pus, perforated viscus, traumatic wound'],
                            ],
                        ],
                        [
                            'name' => 'wound_classification',
                            'type' => 'select',
                            'label' => 'Wound Classification',
                            'options' => [
                                'acute_surgical', 'acute_traumatic', 'chronic_ulcer',
                                'chronic_pressure_sore', 'diabetic_foot', 'burn',
                                'abscess', 'gangrene', 'bite_wound',
                            ],
                        ],
                        [
                            'name' => 'wound_size',
                            'type' => 'text',
                            'label' => 'Wound Size (LxWxD)',
                            'placeholder' => 'e.g., 5x3x2 cm',
                        ],
                        [
                            'name' => 'wound_bed',
                            'type' => 'multiselect',
                            'label' => 'Wound Bed',
                            'options' => ['granulation', 'slough', 'necrotic', 'epithelializing', 'eschar', 'exposed_bone', 'exposed_tendon'],
                        ],
                        [
                            'name' => 'wound_exudate',
                            'type' => 'select',
                            'label' => 'Exudate',
                            'options' => ['none', 'serous', 'serosanguinous', 'sanguinous', 'purulent', 'feculent'],
                        ],
                        [
                            'name' => 'wound_edges',
                            'type' => 'select',
                            'label' => 'Wound Edges',
                            'options' => ['approximated', 'gaping', 'undermined', 'rolled', 'macerated'],
                        ],
                    ],
                ],

                // Operative Note
                [
                    'id' => 'operative_note',
                    'title' => 'Operative Note',
                    'fields' => [
                        [
                            'name' => 'procedure_name',
                            'type' => 'text',
                            'label' => 'Procedure Name',
                            'required' => true,
                            'placeholder' => 'e.g., Open Appendicectomy, Lap Cholecystectomy',
                        ],
                        [
                            'name' => 'surgeon',
                            'type' => 'text',
                            'label' => 'Surgeon',
                            'required' => true,
                        ],
                        [
                            'name' => 'assistant',
                            'type' => 'text',
                            'label' => 'Assistant',
                        ],
                        [
                            'name' => 'anesthetist',
                            'type' => 'text',
                            'label' => 'Anesthetist',
                        ],
                        [
                            'name' => 'anesthesia_type',
                            'type' => 'select',
                            'label' => 'Anesthesia Type',
                            'options' => [
                                'general', 'spinal', 'epidural', 'combined_spinal_epidural',
                                'local', 'regional_block', 'local_with_sedation', 'mac',
                            ],
                        ],
                        [
                            'name' => 'position',
                            'type' => 'select',
                            'label' => 'Patient Position',
                            'options' => ['supine', 'prone', 'lateral_left', 'lateral_right', 'lithotomy', 'trendelenburg', 'reverse_trendelenburg', 'jack_knife'],
                        ],
                        [
                            'name' => 'incision',
                            'type' => 'text',
                            'label' => 'Incision',
                            'placeholder' => 'e.g., Midline, Kocher, Lanz, Pfannenstiel',
                        ],
                        [
                            'name' => 'findings',
                            'type' => 'textarea',
                            'label' => 'Intra-operative Findings',
                            'required' => true,
                        ],
                        [
                            'name' => 'procedure_details',
                            'type' => 'textarea',
                            'label' => 'Procedure Details',
                            'required' => true,
                            'placeholder' => 'Step-by-step operative details...',
                        ],
                        [
                            'name' => 'drain',
                            'type' => 'select',
                            'label' => 'Drain',
                            'options' => ['none', 'romovac', 'corrugated', 'tube_drain', 'pigtail', 'penrose', 'sump'],
                        ],
                        [
                            'name' => 'drain_site',
                            'type' => 'text',
                            'label' => 'Drain Site',
                        ],
                        [
                            'name' => 'specimen_sent',
                            'type' => 'select',
                            'label' => 'Specimen Sent',
                            'options' => ['none', 'histopathology', 'culture_sensitivity', 'frozen_section', 'cytology'],
                        ],
                        [
                            'name' => 'specimen_details',
                            'type' => 'text',
                            'label' => 'Specimen Details',
                        ],
                        [
                            'name' => 'ebl',
                            'type' => 'number',
                            'label' => 'Estimated Blood Loss (ml)',
                            'unit' => 'ml',
                        ],
                        [
                            'name' => 'complications',
                            'type' => 'select',
                            'label' => 'Intra-operative Complications',
                            'options' => ['none', 'bleeding', 'bowel_injury', 'bladder_injury', 'vascular_injury', 'nerve_injury', 'anesthetic_complication', 'other'],
                        ],
                        [
                            'name' => 'complication_details',
                            'type' => 'textarea',
                            'label' => 'Complication Details',
                        ],
                        [
                            'name' => 'closure',
                            'type' => 'text',
                            'label' => 'Closure',
                            'placeholder' => 'e.g., Vicryl 1-0 interrupted, Ethilon 3-0 subcuticular',
                        ],
                        [
                            'name' => 'duration_minutes',
                            'type' => 'number',
                            'label' => 'Duration (minutes)',
                        ],
                    ],
                ],

                // Post-operative Orders
                [
                    'id' => 'postoperative_orders',
                    'title' => 'Post-operative Orders',
                    'fields' => [
                        [
                            'name' => 'vitals_monitoring',
                            'type' => 'select',
                            'label' => 'Vitals Monitoring',
                            'options' => [
                                'every_15min_for_2hrs', 'every_30min_for_4hrs',
                                'hourly_for_6hrs', 'every_2hrs', 'every_4hrs', 'every_6hrs',
                            ],
                        ],
                        [
                            'name' => 'iv_fluids',
                            'type' => 'textarea',
                            'label' => 'IV Fluids',
                            'placeholder' => 'e.g., DNS 1L @ 100ml/hr, RL 1L @ 125ml/hr',
                        ],
                        [
                            'name' => 'antibiotics',
                            'type' => 'textarea',
                            'label' => 'Antibiotics',
                            'placeholder' => 'e.g., Inj Ceftriaxone 1g IV BD',
                        ],
                        [
                            'name' => 'analgesics',
                            'type' => 'textarea',
                            'label' => 'Analgesics',
                            'placeholder' => 'e.g., Inj Tramadol 50mg IV TDS PRN',
                        ],
                        [
                            'name' => 'dvt_prophylaxis',
                            'type' => 'select',
                            'label' => 'DVT Prophylaxis',
                            'options' => [
                                'not_required', 'compression_stockings', 'pneumatic_compression',
                                'enoxaparin_40mg', 'enoxaparin_60mg', 'heparin_5000u',
                                'early_mobilization_only',
                            ],
                        ],
                        [
                            'name' => 'drain_care',
                            'type' => 'textarea',
                            'label' => 'Drain Care Instructions',
                            'placeholder' => 'Monitor output, character, volume...',
                        ],
                        [
                            'name' => 'diet_progression',
                            'type' => 'select',
                            'label' => 'Diet Progression',
                            'options' => [
                                'nil_by_mouth', 'sips_of_water', 'clear_fluids',
                                'full_fluids', 'soft_diet', 'normal_diet',
                                'high_protein_diet',
                            ],
                        ],
                        [
                            'name' => 'mobilization',
                            'type' => 'select',
                            'label' => 'Mobilization',
                            'options' => [
                                'strict_bed_rest', 'bed_rest_with_head_elevation',
                                'sit_up_in_bed', 'dangle_legs', 'walk_with_support',
                                'ambulate_freely',
                            ],
                        ],
                        [
                            'name' => 'io_chart',
                            'type' => 'boolean',
                            'label' => 'Intake/Output Chart',
                        ],
                        [
                            'name' => 'catheter_care',
                            'type' => 'textarea',
                            'label' => 'Catheter/Tube Care',
                        ],
                        [
                            'name' => 'special_instructions',
                            'type' => 'textarea',
                            'label' => 'Special Instructions',
                        ],
                    ],
                ],

                // Diagnosis
                [
                    'id' => 'diagnosis',
                    'title' => 'Diagnosis',
                    'fields' => [
                        [
                            'name' => 'preoperative_diagnosis',
                            'type' => 'text',
                            'label' => 'Pre-operative Diagnosis',
                            'required' => true,
                            'autocomplete' => 'icd10_surgery',
                        ],
                        [
                            'name' => 'postoperative_diagnosis',
                            'type' => 'text',
                            'label' => 'Post-operative Diagnosis',
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
                            'placeholder' => 'e.g., K40.9',
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
                            'name' => 'investigations',
                            'type' => 'multiselect',
                            'label' => 'Investigations',
                            'options' => [
                                'cbc', 'rft', 'lft', 'coagulation_profile', 'blood_group',
                                'ecg', 'chest_xray', 'usg_abdomen', 'ct_abdomen', 'mri',
                                'biopsy', 'culture_sensitivity', 'tumor_markers',
                            ],
                        ],
                        [
                            'name' => 'referral',
                            'type' => 'text',
                            'label' => 'Referral To',
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
                            'placeholder' => 'Wound care, suture removal, diet, activity restrictions...',
                        ],
                        [
                            'name' => 'discharge_summary',
                            'type' => 'textarea',
                            'label' => 'Discharge Summary',
                        ],
                    ],
                ],

                // Clinical Photos
                [
                    'id' => 'photos',
                    'title' => 'Clinical Photos',
                    'type' => 'photo_gallery',
                    'fields' => [
                        [
                            'name' => 'photo_consent',
                            'type' => 'boolean',
                            'label' => 'Photo consent obtained',
                            'required' => true,
                        ],
                        [
                            'name' => 'photos',
                            'type' => 'photo_upload',
                            'label' => 'Photos',
                            'tags' => ['preoperative', 'intraoperative', 'postoperative', 'wound', 'specimen'],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Get common general surgery diagnoses for autocomplete.
     */
    public static function getCommonDiagnoses(): array
    {
        Log::info('GeneralSurgeryTemplate::getCommonDiagnoses() - returning ICD-10 codes');

        return [
            ['code' => 'K40.9', 'name' => 'Inguinal hernia, unspecified'],
            ['code' => 'K40.2', 'name' => 'Bilateral inguinal hernia, without obstruction or gangrene'],
            ['code' => 'K80.2', 'name' => 'Cholelithiasis (gallstone)'],
            ['code' => 'K80.0', 'name' => 'Cholelithiasis with acute cholecystitis'],
            ['code' => 'K35.8', 'name' => 'Acute appendicitis, other and unspecified'],
            ['code' => 'K35.3', 'name' => 'Acute appendicitis with localized peritonitis'],
            ['code' => 'K60.0', 'name' => 'Acute anal fissure'],
            ['code' => 'K60.1', 'name' => 'Chronic anal fissure'],
            ['code' => 'K61.0', 'name' => 'Anal abscess'],
            ['code' => 'K61.1', 'name' => 'Rectal abscess'],
            ['code' => 'I84.0', 'name' => 'Internal thrombosed hemorrhoids'],
            ['code' => 'I84.1', 'name' => 'Internal hemorrhoids with complications'],
            ['code' => 'I84.5', 'name' => 'External hemorrhoids'],
            ['code' => 'K42.9', 'name' => 'Umbilical hernia without obstruction or gangrene'],
            ['code' => 'K43.9', 'name' => 'Incisional hernia without obstruction or gangrene'],
            ['code' => 'L02.9', 'name' => 'Cutaneous abscess, unspecified'],
            ['code' => 'L02.2', 'name' => 'Cutaneous abscess of trunk'],
            ['code' => 'L05.0', 'name' => 'Pilonidal cyst with abscess'],
            ['code' => 'L05.9', 'name' => 'Pilonidal cyst without abscess'],
            ['code' => 'C18.9', 'name' => 'Malignant neoplasm of colon, unspecified'],
            ['code' => 'C18.0', 'name' => 'Malignant neoplasm of caecum'],
            ['code' => 'K56.6', 'name' => 'Intestinal obstruction, unspecified'],
            ['code' => 'K56.0', 'name' => 'Paralytic ileus'],
            ['code' => 'K56.5', 'name' => 'Intestinal adhesions with obstruction'],
            ['code' => 'K41.9', 'name' => 'Femoral hernia, unspecified'],
            ['code' => 'K81.0', 'name' => 'Acute cholecystitis'],
            ['code' => 'K85.9', 'name' => 'Acute pancreatitis, unspecified'],
            ['code' => 'K25.0', 'name' => 'Gastric ulcer, acute with hemorrhage'],
            ['code' => 'K26.5', 'name' => 'Duodenal ulcer, chronic with perforation'],
        ];
    }
}
