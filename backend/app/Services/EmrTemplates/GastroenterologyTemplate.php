<?php

namespace App\Services\EmrTemplates;

use Illuminate\Support\Facades\Log;

class GastroenterologyTemplate
{
    /**
     * Get the complete field schema for the Gastroenterology specialty EMR template.
     */
    public static function schema(): array
    {
        return [
            'sections' => [
                'chief_complaint' => [
                    'label' => 'Chief Complaint & GI History',
                    'fields' => [
                        'chief_complaint' => ['type' => 'textarea', 'label' => 'Chief Complaint', 'required' => true],
                        'history_present_illness' => ['type' => 'textarea', 'label' => 'History of Present Illness'],
                        'duration' => ['type' => 'text', 'label' => 'Duration'],
                        'abdominal_pain_location' => ['type' => 'select', 'label' => 'Abdominal Pain Location', 'options' => ['Epigastric', 'Right Hypochondrium', 'Left Hypochondrium', 'Umbilical', 'Right Iliac Fossa', 'Left Iliac Fossa', 'Suprapubic', 'Right Lumbar', 'Left Lumbar', 'Diffuse', 'None']],
                        'pain_character' => ['type' => 'select', 'label' => 'Pain Character', 'options' => ['Colicky', 'Burning', 'Dull aching', 'Sharp', 'Cramping', 'Gnawing', 'Boring (radiating to back)', 'None']],
                        'pain_relation_food' => ['type' => 'select', 'label' => 'Pain Relation to Food', 'options' => ['Aggravated by food', 'Relieved by food', 'No relation', 'Occurs on empty stomach']],
                        'bowel_habit_frequency' => ['type' => 'text', 'label' => 'Bowel Frequency (per day)'],
                        'bowel_habit_consistency' => ['type' => 'select', 'label' => 'Stool Consistency (Bristol)', 'options' => ['Type 1 - Hard lumps', 'Type 2 - Lumpy sausage', 'Type 3 - Sausage with cracks', 'Type 4 - Smooth snake', 'Type 5 - Soft blobs', 'Type 6 - Mushy', 'Type 7 - Watery']],
                        'blood_in_stool' => ['type' => 'select', 'label' => 'Blood in Stool', 'options' => ['None', 'Fresh blood', 'Dark/altered blood', 'Melena', 'Occult blood positive']],
                        'mucus_in_stool' => ['type' => 'boolean', 'label' => 'Mucus in Stool'],
                        'nausea_vomiting' => ['type' => 'select', 'label' => 'Nausea/Vomiting', 'options' => ['None', 'Nausea only', 'Occasional vomiting', 'Frequent vomiting', 'Projectile vomiting', 'Hematemesis', 'Coffee ground vomiting']],
                        'appetite' => ['type' => 'select', 'label' => 'Appetite', 'options' => ['Normal', 'Decreased', 'Increased', 'Anorexia']],
                        'weight_change' => ['type' => 'select', 'label' => 'Weight Change', 'options' => ['Stable', 'Weight loss', 'Weight gain']],
                        'dysphagia' => ['type' => 'select', 'label' => 'Dysphagia', 'options' => ['None', 'Solids only', 'Solids and liquids', 'Progressive', 'Intermittent']],
                        'jaundice' => ['type' => 'boolean', 'label' => 'Jaundice'],
                        'diet_history' => ['type' => 'textarea', 'label' => 'Diet History'],
                        'alcohol_history' => ['type' => 'select', 'label' => 'Alcohol History', 'options' => ['None', 'Social', 'Moderate', 'Heavy', 'Abstinent (ex-drinker)']],
                        'alcohol_quantity' => ['type' => 'text', 'label' => 'Alcohol Quantity (units/week)'],
                    ],
                ],
                'gi_examination' => [
                    'label' => 'GI Examination',
                    'fields' => [
                        'abdominal_inspection' => ['type' => 'multiselect', 'label' => 'Inspection', 'options' => ['Normal', 'Distended', 'Scaphoid', 'Visible peristalsis', 'Caput medusae', 'Surgical scars', 'Striae', 'Spider naevi']],
                        'tenderness' => ['type' => 'multiselect', 'label' => 'Tenderness Location', 'options' => ['None', 'Epigastric', 'RHC', 'LHC', 'Umbilical', 'RIF', 'LIF', 'Suprapubic', 'Right Lumbar', 'Left Lumbar', 'Diffuse']],
                        'guarding' => ['type' => 'select', 'label' => 'Guarding', 'options' => ['None', 'Voluntary', 'Involuntary/Rigidity']],
                        'rigidity' => ['type' => 'boolean', 'label' => 'Board-like Rigidity'],
                        'rebound_tenderness' => ['type' => 'boolean', 'label' => 'Rebound Tenderness'],
                        'hepatomegaly' => ['type' => 'select', 'label' => 'Hepatomegaly', 'options' => ['None', 'Just palpable', '2 cm BCM', '4 cm BCM', 'Massive', 'Tender']],
                        'splenomegaly' => ['type' => 'select', 'label' => 'Splenomegaly', 'options' => ['None', 'Just palpable', 'Moderate', 'Massive']],
                        'ascites' => ['type' => 'select', 'label' => 'Ascites', 'options' => ['None', 'Mild (shifting dullness)', 'Moderate', 'Tense', 'Fluid thrill positive']],
                        'bowel_sounds' => ['type' => 'select', 'label' => 'Bowel Sounds', 'options' => ['Normal', 'Hyperactive', 'Sluggish', 'Absent', 'Tinkling (high-pitched)']],
                        'dre_findings' => ['type' => 'textarea', 'label' => 'DRE Findings'],
                        'murphy_sign' => ['type' => 'select', 'label' => "Murphy's Sign", 'options' => ['Negative', 'Positive', 'Not tested']],
                        'mcburney_tenderness' => ['type' => 'select', 'label' => "McBurney's Point Tenderness", 'options' => ['Negative', 'Positive', 'Not tested']],
                        'liver_span' => ['type' => 'text', 'label' => 'Liver Span (cm)'],
                        'stigmata_cld' => ['type' => 'multiselect', 'label' => 'Stigmata of CLD', 'options' => ['None', 'Jaundice', 'Palmar erythema', 'Spider naevi', 'Gynecomastia', 'Testicular atrophy', 'Dupuytren contracture', 'Leukonychia', 'Asterixis/Flapping tremor']],
                    ],
                ],
                'endoscopy_findings' => [
                    'label' => 'Endoscopy Findings',
                    'fields' => [
                        'endoscopy_type' => ['type' => 'select', 'label' => 'Endoscopy Type', 'options' => ['Upper GI Endoscopy (EGD)', 'Colonoscopy', 'Sigmoidoscopy', 'ERCP', 'EUS', 'Capsule Endoscopy', 'Not done']],
                        'endoscopy_date' => ['type' => 'date', 'label' => 'Date of Endoscopy'],
                        'esophagus_findings' => ['type' => 'multiselect', 'label' => 'Esophagus Findings', 'options' => ['Normal', 'Esophagitis (LA Grade A)', 'Esophagitis (LA Grade B)', 'Esophagitis (LA Grade C)', 'Esophagitis (LA Grade D)', 'Barrett esophagus', 'Varices (Grade I)', 'Varices (Grade II)', 'Varices (Grade III)', 'Varices (Grade IV)', 'Stricture', 'Malignant growth', 'Hiatus hernia', 'Candidiasis']],
                        'stomach_findings' => ['type' => 'multiselect', 'label' => 'Stomach Findings', 'options' => ['Normal', 'Gastritis (antral)', 'Gastritis (fundal)', 'Gastritis (pangastritis)', 'Erosions', 'Gastric ulcer', 'GAVE (Watermelon stomach)', 'Polyp', 'Malignant growth', 'Portal hypertensive gastropathy']],
                        'duodenum_findings' => ['type' => 'multiselect', 'label' => 'Duodenum Findings', 'options' => ['Normal', 'Duodenitis', 'Duodenal ulcer (D1)', 'Duodenal ulcer (D2)', 'Erosions', 'Deformed bulb', 'Polyp']],
                        'colonoscopy_findings' => ['type' => 'multiselect', 'label' => 'Colonoscopy Findings', 'options' => ['Normal', 'Polyp (sessile)', 'Polyp (pedunculated)', 'Diverticula', 'Colitis (mild)', 'Colitis (moderate)', 'Colitis (severe)', 'Ulcerations', 'Stricture', 'Malignant growth', 'Hemorrhoids (internal)', 'Melanosis coli', 'Vascular malformation']],
                        'biopsy_taken' => ['type' => 'boolean', 'label' => 'Biopsy Taken'],
                        'biopsy_site' => ['type' => 'text', 'label' => 'Biopsy Site'],
                        'biopsy_indication' => ['type' => 'text', 'label' => 'Biopsy Indication'],
                        'h_pylori_rut' => ['type' => 'select', 'label' => 'H. pylori (RUT)', 'options' => ['Not done', 'Positive', 'Negative']],
                        'h_pylori_other' => ['type' => 'select', 'label' => 'H. pylori (Other test)', 'options' => ['Not done', 'UBT Positive', 'UBT Negative', 'Stool Ag Positive', 'Stool Ag Negative', 'Histology Positive', 'Histology Negative']],
                        'therapeutic_intervention' => ['type' => 'textarea', 'label' => 'Therapeutic Intervention Done'],
                        'endoscopy_notes' => ['type' => 'textarea', 'label' => 'Additional Endoscopy Notes'],
                    ],
                ],
                'liver_assessment' => [
                    'label' => 'Liver Assessment',
                    'fields' => [
                        'child_pugh_bilirubin' => ['type' => 'select', 'label' => 'Child-Pugh: Bilirubin', 'options' => [
                            ['value' => 1, 'label' => '<2 mg/dL (1 point)'],
                            ['value' => 2, 'label' => '2-3 mg/dL (2 points)'],
                            ['value' => 3, 'label' => '>3 mg/dL (3 points)'],
                        ]],
                        'child_pugh_albumin' => ['type' => 'select', 'label' => 'Child-Pugh: Albumin', 'options' => [
                            ['value' => 1, 'label' => '>3.5 g/dL (1 point)'],
                            ['value' => 2, 'label' => '2.8-3.5 g/dL (2 points)'],
                            ['value' => 3, 'label' => '<2.8 g/dL (3 points)'],
                        ]],
                        'child_pugh_inr' => ['type' => 'select', 'label' => 'Child-Pugh: INR', 'options' => [
                            ['value' => 1, 'label' => '<1.7 (1 point)'],
                            ['value' => 2, 'label' => '1.7-2.3 (2 points)'],
                            ['value' => 3, 'label' => '>2.3 (3 points)'],
                        ]],
                        'child_pugh_ascites' => ['type' => 'select', 'label' => 'Child-Pugh: Ascites', 'options' => [
                            ['value' => 1, 'label' => 'None (1 point)'],
                            ['value' => 2, 'label' => 'Mild/Controlled (2 points)'],
                            ['value' => 3, 'label' => 'Moderate-Severe/Refractory (3 points)'],
                        ]],
                        'child_pugh_encephalopathy' => ['type' => 'select', 'label' => 'Child-Pugh: Encephalopathy', 'options' => [
                            ['value' => 1, 'label' => 'None (1 point)'],
                            ['value' => 2, 'label' => 'Grade I-II (2 points)'],
                            ['value' => 3, 'label' => 'Grade III-IV (3 points)'],
                        ]],
                        'child_pugh_total' => ['type' => 'number', 'label' => 'Child-Pugh Total Score', 'computed' => true],
                        'child_pugh_class' => ['type' => 'text', 'label' => 'Child-Pugh Class', 'computed' => true],
                        'meld_bilirubin' => ['type' => 'number', 'label' => 'MELD: Bilirubin (mg/dL)'],
                        'meld_creatinine' => ['type' => 'number', 'label' => 'MELD: Creatinine (mg/dL)'],
                        'meld_inr' => ['type' => 'number', 'label' => 'MELD: INR'],
                        'meld_sodium' => ['type' => 'number', 'label' => 'MELD-Na: Sodium (mEq/L)'],
                        'meld_score' => ['type' => 'number', 'label' => 'MELD Score', 'computed' => true],
                        'meld_na_score' => ['type' => 'number', 'label' => 'MELD-Na Score', 'computed' => true],
                        'liver_assessment_notes' => ['type' => 'textarea', 'label' => 'Liver Assessment Notes'],
                    ],
                ],
                'diagnosis' => [
                    'label' => 'Diagnosis',
                    'fields' => [
                        'provisional_diagnosis' => ['type' => 'text', 'label' => 'Provisional Diagnosis', 'required' => true, 'autocomplete' => 'icd10_gastroenterology'],
                        'differential_diagnosis' => ['type' => 'tags', 'label' => 'Differential Diagnosis'],
                        'icd10_code' => ['type' => 'text', 'label' => 'ICD-10 Code'],
                    ],
                ],
                'plan' => [
                    'label' => 'Plan & Follow-up',
                    'fields' => [
                        'treatment_plan' => ['type' => 'textarea', 'label' => 'Treatment Plan'],
                        'diet_advice' => ['type' => 'textarea', 'label' => 'Dietary Advice'],
                        'investigations' => ['type' => 'multiselect', 'label' => 'Investigations', 'options' => ['CBC', 'LFT', 'KFT', 'Lipid Profile', 'Serum Amylase', 'Serum Lipase', 'Stool Routine', 'Stool Culture', 'Stool Occult Blood', 'H. pylori (UBT/Stool Ag)', 'USG Abdomen', 'CT Abdomen', 'MRCP', 'Upper GI Endoscopy', 'Colonoscopy', 'Fibroscan', 'Liver Biopsy', 'Anti-tTG (Celiac)', 'AFP']],
                        'follow_up_date' => ['type' => 'date', 'label' => 'Follow-up Date'],
                        'follow_up_notes' => ['type' => 'textarea', 'label' => 'Follow-up Instructions'],
                        'referral' => ['type' => 'text', 'label' => 'Referral To'],
                    ],
                ],
            ],
            'scales' => ['Child-Pugh', 'MELD', 'MELD-Na', 'Rockall', 'Glasgow-Blatchford'],
            'procedures' => [
                'egd' => ['label' => 'Upper GI Endoscopy', 'sac_code' => '999311', 'params' => ['indication', 'sedation', 'findings']],
                'colonoscopy' => ['label' => 'Colonoscopy', 'sac_code' => '999311', 'params' => ['indication', 'preparation', 'sedation', 'findings']],
                'ercp' => ['label' => 'ERCP', 'sac_code' => '999311', 'params' => ['indication', 'findings', 'intervention']],
                'eus' => ['label' => 'Endoscopic Ultrasound', 'sac_code' => '999311', 'params' => ['indication', 'findings']],
                'paracentesis' => ['label' => 'Paracentesis', 'sac_code' => '999311', 'params' => ['volume_drained', 'fluid_appearance', 'albumin_replacement']],
                'liver_biopsy' => ['label' => 'Liver Biopsy', 'sac_code' => '999311', 'params' => ['approach', 'cores_obtained']],
            ],
        ];
    }

    /**
     * Default data structure for a new gastroenterology visit.
     */
    public static function defaultData(): array
    {
        return [
            'chief_complaint' => '',
            'history_present_illness' => '',
            'abdominal_pain' => ['location' => '', 'character' => '', 'relation_to_food' => ''],
            'bowel_habits' => ['frequency' => '', 'consistency' => '', 'blood' => 'None', 'mucus' => false],
            'examination' => ['tenderness' => [], 'guarding' => 'None', 'organomegaly' => '', 'ascites' => 'None', 'bowel_sounds' => 'Normal'],
            'endoscopy' => ['type' => '', 'findings' => '', 'biopsy' => false, 'h_pylori' => ''],
            'liver_scores' => ['child_pugh' => null, 'meld' => null],
            'diagnosis' => ['provisional' => '', 'differential' => [], 'icd10' => ''],
            'plan' => ['treatment' => '', 'diet' => '', 'follow_up_date' => '', 'follow_up_notes' => ''],
        ];
    }

    /**
     * Get gastroenterology EMR template fields.
     */
    public static function getFields(): array
    {
        Log::info('Loading Gastroenterology EMR template');
        Log::info('Gastroenterology template: building sections array');

        return [
            'specialty' => 'gastroenterology',
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
                            'placeholder' => 'e.g., 3 days, 2 weeks',
                        ],
                        [
                            'name' => 'abdominal_pain_location',
                            'type' => 'select',
                            'label' => 'Abdominal Pain Location',
                            'options' => [
                                'epigastric', 'right_hypochondrium', 'left_hypochondrium',
                                'umbilical', 'right_iliac_fossa', 'left_iliac_fossa',
                                'suprapubic', 'right_lumbar', 'left_lumbar', 'diffuse', 'none',
                            ],
                        ],
                        [
                            'name' => 'pain_character',
                            'type' => 'select',
                            'label' => 'Pain Character',
                            'options' => ['colicky', 'burning', 'dull_aching', 'sharp', 'cramping', 'gnawing', 'boring'],
                        ],
                        [
                            'name' => 'bowel_frequency',
                            'type' => 'text',
                            'label' => 'Bowel Frequency (per day)',
                        ],
                        [
                            'name' => 'stool_consistency',
                            'type' => 'select',
                            'label' => 'Stool Consistency (Bristol Scale)',
                            'options' => [
                                'type_1_hard_lumps', 'type_2_lumpy_sausage', 'type_3_cracked_sausage',
                                'type_4_smooth_snake', 'type_5_soft_blobs', 'type_6_mushy', 'type_7_watery',
                            ],
                        ],
                        [
                            'name' => 'blood_in_stool',
                            'type' => 'select',
                            'label' => 'Blood in Stool',
                            'options' => ['none', 'fresh_blood', 'dark_blood', 'melena', 'occult_positive'],
                        ],
                        [
                            'name' => 'diet_history',
                            'type' => 'textarea',
                            'label' => 'Diet History',
                        ],
                        [
                            'name' => 'alcohol_history',
                            'type' => 'select',
                            'label' => 'Alcohol History',
                            'options' => ['none', 'social', 'moderate', 'heavy', 'abstinent_ex_drinker'],
                        ],
                    ],
                ],

                [
                    'id' => 'gi_examination',
                    'title' => 'GI Examination',
                    'fields' => [
                        [
                            'name' => 'abdominal_inspection',
                            'type' => 'multiselect',
                            'label' => 'Abdomen Inspection',
                            'options' => [
                                'normal', 'distended', 'scaphoid', 'visible_peristalsis',
                                'caput_medusae', 'surgical_scars', 'spider_naevi',
                            ],
                        ],
                        [
                            'name' => 'tenderness',
                            'type' => 'multiselect',
                            'label' => 'Tenderness',
                            'options' => [
                                'none', 'epigastric', 'rhc', 'lhc', 'umbilical',
                                'rif', 'lif', 'suprapubic', 'diffuse',
                            ],
                        ],
                        [
                            'name' => 'guarding',
                            'type' => 'select',
                            'label' => 'Guarding',
                            'options' => ['none', 'voluntary', 'involuntary_rigidity'],
                        ],
                        [
                            'name' => 'rigidity',
                            'type' => 'boolean',
                            'label' => 'Board-like Rigidity',
                        ],
                        [
                            'name' => 'hepatomegaly',
                            'type' => 'select',
                            'label' => 'Hepatomegaly',
                            'options' => ['none', 'just_palpable', '2cm_bcm', '4cm_bcm', 'massive', 'tender'],
                        ],
                        [
                            'name' => 'splenomegaly',
                            'type' => 'select',
                            'label' => 'Splenomegaly',
                            'options' => ['none', 'just_palpable', 'moderate', 'massive'],
                        ],
                        [
                            'name' => 'ascites',
                            'type' => 'select',
                            'label' => 'Ascites',
                            'options' => ['none', 'mild_shifting_dullness', 'moderate', 'tense', 'fluid_thrill_positive'],
                        ],
                        [
                            'name' => 'bowel_sounds',
                            'type' => 'select',
                            'label' => 'Bowel Sounds',
                            'options' => ['normal', 'hyperactive', 'sluggish', 'absent', 'tinkling'],
                        ],
                        [
                            'name' => 'dre_findings',
                            'type' => 'textarea',
                            'label' => 'DRE Findings',
                            'placeholder' => 'Tone, mass, stool, blood on glove...',
                        ],
                        [
                            'name' => 'stigmata_cld',
                            'type' => 'multiselect',
                            'label' => 'Stigmata of Chronic Liver Disease',
                            'options' => [
                                'none', 'jaundice', 'palmar_erythema', 'spider_naevi',
                                'gynecomastia', 'dupuytren', 'leukonychia', 'asterixis',
                            ],
                        ],
                    ],
                ],

                [
                    'id' => 'endoscopy_findings',
                    'title' => 'Endoscopy Findings',
                    'fields' => [
                        [
                            'name' => 'endoscopy_type',
                            'type' => 'select',
                            'label' => 'Endoscopy Type',
                            'options' => ['upper_gi_egd', 'colonoscopy', 'sigmoidoscopy', 'ercp', 'eus', 'capsule', 'not_done'],
                        ],
                        [
                            'name' => 'endoscopy_date',
                            'type' => 'date',
                            'label' => 'Date of Endoscopy',
                        ],
                        [
                            'name' => 'endoscopy_site',
                            'type' => 'multiselect',
                            'label' => 'Site of Pathology',
                            'options' => [
                                'esophagus', 'gej', 'fundus', 'body', 'antrum',
                                'pylorus', 'd1', 'd2', 'cecum', 'ascending_colon',
                                'transverse_colon', 'descending_colon', 'sigmoid', 'rectum',
                            ],
                        ],
                        [
                            'name' => 'endoscopy_findings',
                            'type' => 'textarea',
                            'label' => 'Findings',
                            'placeholder' => 'Describe endoscopic findings...',
                        ],
                        [
                            'name' => 'biopsy_taken',
                            'type' => 'boolean',
                            'label' => 'Biopsy Taken',
                        ],
                        [
                            'name' => 'biopsy_site',
                            'type' => 'text',
                            'label' => 'Biopsy Site',
                        ],
                        [
                            'name' => 'h_pylori_test',
                            'type' => 'select',
                            'label' => 'H. pylori Test',
                            'options' => ['not_done', 'rut_positive', 'rut_negative', 'ubt_positive', 'ubt_negative', 'stool_ag_positive', 'stool_ag_negative'],
                        ],
                    ],
                ],

                [
                    'id' => 'liver_assessment',
                    'title' => 'Liver Assessment',
                    'fields' => [
                        [
                            'name' => 'child_pugh_score',
                            'type' => 'child_pugh_calculator',
                            'label' => 'Child-Pugh Score',
                            'components' => [
                                'bilirubin' => ['label' => 'Total Bilirubin', 'thresholds' => ['<2', '2-3', '>3']],
                                'albumin' => ['label' => 'Albumin', 'thresholds' => ['>3.5', '2.8-3.5', '<2.8']],
                                'inr' => ['label' => 'INR', 'thresholds' => ['<1.7', '1.7-2.3', '>2.3']],
                                'ascites' => ['label' => 'Ascites', 'thresholds' => ['None', 'Mild', 'Moderate-Severe']],
                                'encephalopathy' => ['label' => 'Encephalopathy', 'thresholds' => ['None', 'Grade I-II', 'Grade III-IV']],
                            ],
                            'range' => [5, 15],
                        ],
                        [
                            'name' => 'meld_score',
                            'type' => 'meld_calculator',
                            'label' => 'MELD Score',
                            'inputs' => ['bilirubin_mg_dl', 'creatinine_mg_dl', 'inr', 'sodium_meq_l'],
                        ],
                        [
                            'name' => 'liver_notes',
                            'type' => 'textarea',
                            'label' => 'Liver Assessment Notes',
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
                            'autocomplete' => 'icd10_gastroenterology',
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
                            'placeholder' => 'e.g., K21.0',
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
                            'placeholder' => 'Medical management, dietary plan...',
                        ],
                        [
                            'name' => 'diet_advice',
                            'type' => 'textarea',
                            'label' => 'Dietary Advice',
                        ],
                        [
                            'name' => 'investigations',
                            'type' => 'multiselect',
                            'label' => 'Investigations',
                            'options' => [
                                'cbc', 'lft', 'kft', 'amylase', 'lipase',
                                'stool_routine', 'stool_culture', 'stool_occult_blood',
                                'h_pylori_test', 'usg_abdomen', 'ct_abdomen', 'mrcp',
                                'upper_gi_endoscopy', 'colonoscopy', 'fibroscan', 'afp',
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
     * Calculate Child-Pugh score from component data.
     */
    public static function calculateChildPugh(array $components): array
    {
        Log::info('Calculating Child-Pugh score', ['components' => $components]);

        $score = 0;
        $score += $components['bilirubin'] ?? 1;
        $score += $components['albumin'] ?? 1;
        $score += $components['inr'] ?? 1;
        $score += $components['ascites'] ?? 1;
        $score += $components['encephalopathy'] ?? 1;

        $class = match (true) {
            $score <= 6 => 'A',
            $score <= 9 => 'B',
            default => 'C',
        };

        $survival = match ($class) {
            'A' => '100% 1-year survival',
            'B' => '81% 1-year survival',
            'C' => '45% 1-year survival',
            default => 'Unknown',
        };

        Log::info('Child-Pugh calculated', [
            'score' => $score,
            'class' => $class,
            'survival' => $survival,
        ]);

        return [
            'total' => $score,
            'class' => $class,
            'survival' => $survival,
        ];
    }

    /**
     * Calculate MELD score from lab values.
     */
    public static function calculateMeld(float $bilirubin, float $creatinine, float $inr): array
    {
        Log::info('Calculating MELD score', [
            'bilirubin' => $bilirubin,
            'creatinine' => $creatinine,
            'inr' => $inr,
        ]);

        $bilirubin = max($bilirubin, 1.0);
        $creatinine = max(min($creatinine, 4.0), 1.0);
        $inr = max($inr, 1.0);

        $meld = round(
            3.78 * log($bilirubin) +
            11.2 * log($inr) +
            9.57 * log($creatinine) +
            6.43,
            0
        );

        $meld = max(min($meld, 40), 6);

        $mortality_3month = match (true) {
            $meld < 10 => '1.9%',
            $meld < 20 => '6.0%',
            $meld < 30 => '19.6%',
            $meld < 40 => '52.6%',
            default => '71.3%',
        };

        Log::info('MELD calculated', [
            'score' => $meld,
            'mortality_3month' => $mortality_3month,
        ]);

        return [
            'score' => (int) $meld,
            'mortality_3month' => $mortality_3month,
        ];
    }

    /**
     * Get common gastroenterology diagnoses for autocomplete.
     */
    public static function getCommonDiagnoses(): array
    {
        Log::info('Fetching common gastroenterology diagnoses list');

        return [
            ['code' => 'K21.0', 'name' => 'Gastro-esophageal reflux disease with esophagitis (GERD)'],
            ['code' => 'K21.9', 'name' => 'Gastro-esophageal reflux disease without esophagitis'],
            ['code' => 'K25', 'name' => 'Gastric ulcer'],
            ['code' => 'K25.9', 'name' => 'Gastric ulcer, unspecified, without hemorrhage or perforation'],
            ['code' => 'K26', 'name' => 'Duodenal ulcer'],
            ['code' => 'K26.9', 'name' => 'Duodenal ulcer, unspecified, without hemorrhage or perforation'],
            ['code' => 'K29', 'name' => 'Gastritis and duodenitis'],
            ['code' => 'K29.7', 'name' => 'Gastritis, unspecified'],
            ['code' => 'K50', 'name' => "Crohn's disease"],
            ['code' => 'K50.9', 'name' => "Crohn's disease, unspecified"],
            ['code' => 'K51', 'name' => 'Ulcerative colitis'],
            ['code' => 'K51.9', 'name' => 'Ulcerative colitis, unspecified'],
            ['code' => 'K70', 'name' => 'Alcoholic liver disease'],
            ['code' => 'K70.3', 'name' => 'Alcoholic cirrhosis of liver'],
            ['code' => 'K74', 'name' => 'Fibrosis and cirrhosis of liver'],
            ['code' => 'K74.6', 'name' => 'Other and unspecified cirrhosis of liver'],
            ['code' => 'K76.0', 'name' => 'Fatty (change of) liver, NEC (NAFLD)'],
            ['code' => 'K80', 'name' => 'Cholelithiasis'],
            ['code' => 'K80.2', 'name' => 'Calculus of gallbladder without cholecystitis'],
            ['code' => 'K81', 'name' => 'Cholecystitis'],
            ['code' => 'K85', 'name' => 'Acute pancreatitis'],
            ['code' => 'K85.9', 'name' => 'Acute pancreatitis, unspecified'],
            ['code' => 'K86.1', 'name' => 'Other chronic pancreatitis'],
            ['code' => 'K57', 'name' => 'Diverticular disease of intestine'],
            ['code' => 'K57.3', 'name' => 'Diverticular disease of large intestine without perforation or abscess'],
            ['code' => 'K58', 'name' => 'Irritable bowel syndrome (IBS)'],
            ['code' => 'K58.0', 'name' => 'IBS with diarrhea'],
            ['code' => 'K58.9', 'name' => 'IBS without diarrhea'],
            ['code' => 'K22.1', 'name' => 'Esophageal ulcer'],
            ['code' => 'K22.7', 'name' => "Barrett's esophagus"],
            ['code' => 'B18.1', 'name' => 'Chronic viral hepatitis B'],
            ['code' => 'B18.2', 'name' => 'Chronic viral hepatitis C'],
            ['code' => 'K90.0', 'name' => 'Celiac disease'],
        ];
    }
}
