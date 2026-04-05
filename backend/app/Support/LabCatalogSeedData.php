<?php

namespace App\Support;

/**
 * Default in-house LIS catalog: common pathology disciplines and tests
 * (aligned with typical WHO / CLSI-style lab groupings — not exhaustive).
 */
final class LabCatalogSeedData
{
    /**
     * @return list<array{code: string, name: string}>
     */
    public static function departments(): array
    {
        return [
            ['code' => 'HEM', 'name' => 'Haematology'],
            ['code' => 'BCH', 'name' => 'Clinical chemistry'],
            ['code' => 'SER', 'name' => 'Serology & immunology'],
            ['code' => 'MIC', 'name' => 'Microbiology'],
            ['code' => 'URI', 'name' => 'Urinalysis'],
            ['code' => 'END', 'name' => 'Endocrinology & hormones'],
            ['code' => 'COA', 'name' => 'Coagulation'],
            ['code' => 'MOB', 'name' => 'Molecular diagnostics'],
            ['code' => 'HIST', 'name' => 'Histopathology & cytology'],
            ['code' => 'TOX', 'name' => 'Toxicology & therapeutic drug monitoring'],
            ['code' => 'SPE', 'name' => 'Special chemistry'],
            ['code' => 'GEN', 'name' => 'General panels'],
        ];
    }

    /**
     * @return list<array{dept: string, code: string, name: string, sample: string, price: float|int, tat: int}>
     */
    public static function tests(): array
    {
        return [
            // Haematology
            ['dept' => 'HEM', 'code' => 'CBC', 'name' => 'Complete blood count (CBC)', 'sample' => 'blood', 'price' => 350, 'tat' => 24],
            ['dept' => 'HEM', 'code' => 'ESR', 'name' => 'Erythrocyte sedimentation rate (ESR)', 'sample' => 'blood', 'price' => 100, 'tat' => 24],
            ['dept' => 'HEM', 'code' => 'RETIC', 'name' => 'Reticulocyte count', 'sample' => 'blood', 'price' => 250, 'tat' => 24],
            ['dept' => 'HEM', 'code' => 'PS', 'name' => 'Peripheral blood smear', 'sample' => 'blood', 'price' => 200, 'tat' => 24],
            ['dept' => 'HEM', 'code' => 'BFMP', 'name' => 'Blood film for malaria parasite', 'sample' => 'blood', 'price' => 150, 'tat' => 12],
            ['dept' => 'HEM', 'code' => 'HB_ELECTRO', 'name' => 'Haemoglobin electrophoresis', 'sample' => 'blood', 'price' => 900, 'tat' => 72],
            ['dept' => 'HEM', 'code' => 'G6PD', 'name' => 'G6PD assay', 'sample' => 'blood', 'price' => 550, 'tat' => 48],
            ['dept' => 'HEM', 'code' => 'IRON_STUDY', 'name' => 'Iron studies (Fe, TIBC, transferrin sat.)', 'sample' => 'blood', 'price' => 800, 'tat' => 24],
            ['dept' => 'HEM', 'code' => 'B12_FOL', 'name' => 'Vitamin B12 & folate', 'sample' => 'blood', 'price' => 1200, 'tat' => 48],
            // Clinical chemistry
            ['dept' => 'BCH', 'code' => 'LFT', 'name' => 'Liver function test (LFT) panel', 'sample' => 'blood', 'price' => 650, 'tat' => 24],
            ['dept' => 'BCH', 'code' => 'KFT', 'name' => 'Kidney function test (KFT) panel', 'sample' => 'blood', 'price' => 550, 'tat' => 24],
            ['dept' => 'BCH', 'code' => 'LIPID', 'name' => 'Lipid profile', 'sample' => 'blood', 'price' => 500, 'tat' => 24],
            ['dept' => 'BCH', 'code' => 'ELECTRO', 'name' => 'Electrolyte panel (Na, K, Cl)', 'sample' => 'blood', 'price' => 400, 'tat' => 12],
            ['dept' => 'BCH', 'code' => 'RBS', 'name' => 'Random blood glucose', 'sample' => 'blood', 'price' => 80, 'tat' => 4],
            ['dept' => 'BCH', 'code' => 'FBS', 'name' => 'Fasting blood glucose', 'sample' => 'blood', 'price' => 100, 'tat' => 4],
            ['dept' => 'BCH', 'code' => 'PPBS', 'name' => 'Post-prandial blood glucose', 'sample' => 'blood', 'price' => 100, 'tat' => 4],
            ['dept' => 'BCH', 'code' => 'HBA1C', 'name' => 'HbA1c (glycated haemoglobin)', 'sample' => 'blood', 'price' => 450, 'tat' => 24],
            ['dept' => 'BCH', 'code' => 'CALMAG', 'name' => 'Calcium & magnesium', 'sample' => 'blood', 'price' => 350, 'tat' => 24],
            ['dept' => 'BCH', 'code' => 'PHOS', 'name' => 'Phosphate (inorganic)', 'sample' => 'blood', 'price' => 200, 'tat' => 24],
            ['dept' => 'BCH', 'code' => 'URIC', 'name' => 'Uric acid', 'sample' => 'blood', 'price' => 180, 'tat' => 24],
            ['dept' => 'BCH', 'code' => 'BILTOT', 'name' => 'Total bilirubin', 'sample' => 'blood', 'price' => 150, 'tat' => 24],
            ['dept' => 'BCH', 'code' => 'ALP', 'name' => 'Alkaline phosphatase (ALP)', 'sample' => 'blood', 'price' => 200, 'tat' => 24],
            ['dept' => 'BCH', 'code' => 'AMYL', 'name' => 'Serum amylase', 'sample' => 'blood', 'price' => 350, 'tat' => 24],
            ['dept' => 'BCH', 'code' => 'LIPASE', 'name' => 'Serum lipase', 'sample' => 'blood', 'price' => 450, 'tat' => 24],
            ['dept' => 'BCH', 'code' => 'CK_MB', 'name' => 'CK-MB', 'sample' => 'blood', 'price' => 600, 'tat' => 24],
            ['dept' => 'BCH', 'code' => 'TROP', 'name' => 'High-sensitivity troponin', 'sample' => 'blood', 'price' => 800, 'tat' => 6],
            ['dept' => 'BCH', 'code' => 'BNP', 'name' => 'BNP / NT-proBNP', 'sample' => 'blood', 'price' => 1200, 'tat' => 24],
            ['dept' => 'BCH', 'code' => 'LACTATE', 'name' => 'Lactate', 'sample' => 'blood', 'price' => 400, 'tat' => 4],
            ['dept' => 'BCH', 'code' => 'ABG', 'name' => 'Arterial blood gas (ABG)', 'sample' => 'blood', 'price' => 900, 'tat' => 2],
            ['dept' => 'BCH', 'code' => 'OSM', 'name' => 'Serum osmolality', 'sample' => 'blood', 'price' => 500, 'tat' => 48],
            // Serology
            ['dept' => 'SER', 'code' => 'HIV', 'name' => 'HIV screening (antibody/antigen)', 'sample' => 'blood', 'price' => 400, 'tat' => 24],
            ['dept' => 'SER', 'code' => 'HBSAG', 'name' => 'Hepatitis B surface antigen (HBsAg)', 'sample' => 'blood', 'price' => 350, 'tat' => 24],
            ['dept' => 'SER', 'code' => 'ANTIHCV', 'name' => 'Anti-HCV (Hepatitis C)', 'sample' => 'blood', 'price' => 600, 'tat' => 24],
            ['dept' => 'SER', 'code' => 'VDRL', 'name' => 'VDRL (syphilis screening)', 'sample' => 'blood', 'price' => 200, 'tat' => 24],
            ['dept' => 'SER', 'code' => 'DENGUE_NS1', 'name' => 'Dengue NS1 antigen', 'sample' => 'blood', 'price' => 600, 'tat' => 12],
            ['dept' => 'SER', 'code' => 'DENGUE_IGG_IGM', 'name' => 'Dengue IgG / IgM', 'sample' => 'blood', 'price' => 800, 'tat' => 24],
            ['dept' => 'SER', 'code' => 'TYPHI', 'name' => 'Typhoid (Widal / rapid)', 'sample' => 'blood', 'price' => 350, 'tat' => 24],
            ['dept' => 'SER', 'code' => 'MALARIA_RAPID', 'name' => 'Malaria rapid antigen', 'sample' => 'blood', 'price' => 300, 'tat' => 4],
            ['dept' => 'SER', 'code' => 'CRP', 'name' => 'C-reactive protein (CRP)', 'sample' => 'blood', 'price' => 350, 'tat' => 12],
            ['dept' => 'SER', 'code' => 'PCT', 'name' => 'Procalcitonin', 'sample' => 'blood', 'price' => 1500, 'tat' => 12],
            ['dept' => 'SER', 'code' => 'RF', 'name' => 'Rheumatoid factor (RF)', 'sample' => 'blood', 'price' => 400, 'tat' => 24],
            ['dept' => 'SER', 'code' => 'ANA', 'name' => 'Antinuclear antibody (ANA)', 'sample' => 'blood', 'price' => 700, 'tat' => 48],
            ['dept' => 'SER', 'code' => 'ASO', 'name' => 'ASO titre', 'sample' => 'blood', 'price' => 300, 'tat' => 24],
            ['dept' => 'SER', 'code' => 'COVID_AG', 'name' => 'COVID-19 rapid antigen', 'sample' => 'swab', 'price' => 250, 'tat' => 4],
            ['dept' => 'SER', 'code' => 'COVID_PCR', 'name' => 'COVID-19 RT-PCR', 'sample' => 'swab', 'price' => 800, 'tat' => 24],
            ['dept' => 'SER', 'code' => 'H_PYLORI', 'name' => 'H. pylori IgG / stool antigen', 'sample' => 'blood', 'price' => 900, 'tat' => 24],
            // Microbiology
            ['dept' => 'MIC', 'code' => 'UCULT', 'name' => 'Urine culture & sensitivity', 'sample' => 'urine', 'price' => 450, 'tat' => 72],
            ['dept' => 'MIC', 'code' => 'BCULT', 'name' => 'Blood culture (aerobic)', 'sample' => 'blood', 'price' => 600, 'tat' => 120],
            ['dept' => 'MIC', 'code' => 'STOOL_C_S', 'name' => 'Stool culture & sensitivity', 'sample' => 'stool', 'price' => 500, 'tat' => 72],
            ['dept' => 'MIC', 'code' => 'STOOL_O_P', 'name' => 'Stool ova & parasites', 'sample' => 'stool', 'price' => 300, 'tat' => 48],
            ['dept' => 'MIC', 'code' => 'GRAM', 'name' => 'Gram stain', 'sample' => 'swab', 'price' => 200, 'tat' => 12],
            ['dept' => 'MIC', 'code' => 'AFB', 'name' => 'AFB smear (ZN stain)', 'sample' => 'sputum', 'price' => 150, 'tat' => 24],
            ['dept' => 'MIC', 'code' => 'FUNGAL_CULT', 'name' => 'Fungal culture', 'sample' => 'other', 'price' => 800, 'tat' => 168],
            ['dept' => 'MIC', 'code' => 'THROAT_CULT', 'name' => 'Throat swab culture', 'sample' => 'swab', 'price' => 400, 'tat' => 48],
            // Urinalysis
            ['dept' => 'URI', 'code' => 'URINE_R', 'name' => 'Urine routine (dipstick + microscopy)', 'sample' => 'urine', 'price' => 150, 'tat' => 12],
            ['dept' => 'URI', 'code' => 'U_MICRO', 'name' => 'Urine microalbumin', 'sample' => 'urine', 'price' => 400, 'tat' => 24],
            ['dept' => 'URI', 'code' => 'U_ACR', 'name' => 'Urine albumin/creatinine ratio', 'sample' => 'urine', 'price' => 500, 'tat' => 24],
            ['dept' => 'URI', 'code' => '24H_URINE', 'name' => '24-hour urine protein / creatinine', 'sample' => 'urine', 'price' => 350, 'tat' => 24],
            // Endocrinology
            ['dept' => 'END', 'code' => 'TFT', 'name' => 'Thyroid function panel (TSH, FT3, FT4)', 'sample' => 'blood', 'price' => 750, 'tat' => 24],
            ['dept' => 'END', 'code' => 'TSH', 'name' => 'TSH alone', 'sample' => 'blood', 'price' => 300, 'tat' => 24],
            ['dept' => 'END', 'code' => 'CORT_AM', 'name' => 'Serum cortisol (AM)', 'sample' => 'blood', 'price' => 600, 'tat' => 24],
            ['dept' => 'END', 'code' => 'INSULIN', 'name' => 'Fasting insulin', 'sample' => 'blood', 'price' => 500, 'tat' => 24],
            ['dept' => 'END', 'code' => 'TESTO', 'name' => 'Total testosterone', 'sample' => 'blood', 'price' => 650, 'tat' => 48],
            ['dept' => 'END', 'code' => 'PROL', 'name' => 'Prolactin', 'sample' => 'blood', 'price' => 500, 'tat' => 24],
            ['dept' => 'END', 'code' => 'FSH_LH', 'name' => 'FSH / LH', 'sample' => 'blood', 'price' => 900, 'tat' => 48],
            ['dept' => 'END', 'code' => 'BHCG', 'name' => 'Beta-hCG (quantitative)', 'sample' => 'blood', 'price' => 450, 'tat' => 12],
            ['dept' => 'END', 'code' => 'VIT_D', 'name' => '25-OH Vitamin D', 'sample' => 'blood', 'price' => 1200, 'tat' => 48],
            ['dept' => 'END', 'code' => 'PTH', 'name' => 'Parathyroid hormone (PTH)', 'sample' => 'blood', 'price' => 1100, 'tat' => 48],
            // Coagulation
            ['dept' => 'COA', 'code' => 'PT_INR', 'name' => 'Prothrombin time / INR', 'sample' => 'blood', 'price' => 300, 'tat' => 12],
            ['dept' => 'COA', 'code' => 'APTT', 'name' => 'APTT', 'sample' => 'blood', 'price' => 350, 'tat' => 12],
            ['dept' => 'COA', 'code' => 'DDIMER', 'name' => 'D-dimer', 'sample' => 'blood', 'price' => 800, 'tat' => 12],
            ['dept' => 'COA', 'code' => 'FIB', 'name' => 'Fibrinogen', 'sample' => 'blood', 'price' => 400, 'tat' => 24],
            ['dept' => 'COA', 'code' => 'BT_CT', 'name' => 'Bleeding time / clotting time', 'sample' => 'blood', 'price' => 150, 'tat' => 4],
            // Molecular
            ['dept' => 'MOB', 'code' => 'HIV_PCR', 'name' => 'HIV-1 viral load (PCR)', 'sample' => 'blood', 'price' => 4500, 'tat' => 168],
            ['dept' => 'MOB', 'code' => 'HBV_DNA', 'name' => 'HBV DNA quantitative', 'sample' => 'blood', 'price' => 3500, 'tat' => 168],
            ['dept' => 'MOB', 'code' => 'HCV_RNA', 'name' => 'HCV RNA quantitative', 'sample' => 'blood', 'price' => 4000, 'tat' => 168],
            ['dept' => 'MOB', 'code' => 'TB_GENX', 'name' => 'MTB/RIF (GeneXpert)', 'sample' => 'sputum', 'price' => 2000, 'tat' => 48],
            ['dept' => 'MOB', 'code' => 'CT_NG_NAAT', 'name' => 'Chlamydia / Gonorrhoea NAAT', 'sample' => 'swab', 'price' => 1200, 'tat' => 48],
            // Histopathology
            ['dept' => 'HIST', 'code' => 'BX_SMALL', 'name' => 'Histopathology — small biopsy', 'sample' => 'tissue', 'price' => 1500, 'tat' => 120],
            ['dept' => 'HIST', 'code' => 'PAP', 'name' => 'Pap smear (cervical cytology)', 'sample' => 'swab', 'price' => 500, 'tat' => 72],
            ['dept' => 'HIST', 'code' => 'FNAC', 'name' => 'FNAC', 'sample' => 'fluid', 'price' => 900, 'tat' => 72],
            // Toxicology / TDM
            ['dept' => 'TOX', 'code' => 'DIGOXIN', 'name' => 'Serum digoxin', 'sample' => 'blood', 'price' => 700, 'tat' => 48],
            ['dept' => 'TOX', 'code' => 'PHENYTOIN', 'name' => 'Phenytoin level', 'sample' => 'blood', 'price' => 650, 'tat' => 48],
            ['dept' => 'TOX', 'code' => 'VALPROATE', 'name' => 'Valproic acid level', 'sample' => 'blood', 'price' => 650, 'tat' => 48],
            ['dept' => 'TOX', 'code' => 'LITHIUM', 'name' => 'Serum lithium', 'sample' => 'blood', 'price' => 500, 'tat' => 24],
            // Special chemistry
            ['dept' => 'SPE', 'code' => 'PROTEIN_EP', 'name' => 'Serum protein electrophoresis', 'sample' => 'blood', 'price' => 900, 'tat' => 72],
            ['dept' => 'SPE', 'code' => 'URINE_EP', 'name' => 'Urine protein electrophoresis (Bence Jones)', 'sample' => 'urine', 'price' => 1100, 'tat' => 72],
            ['dept' => 'SPE', 'code' => 'AMMONIA', 'name' => 'Blood ammonia', 'sample' => 'blood', 'price' => 600, 'tat' => 4],
            // General panels (often ordered together)
            ['dept' => 'GEN', 'code' => 'HEALTH_BASIC', 'name' => 'Executive health — basic metabolic panel', 'sample' => 'blood', 'price' => 1800, 'tat' => 24],
            ['dept' => 'GEN', 'code' => 'HEALTH_FULL', 'name' => 'Executive health — full body screening', 'sample' => 'blood', 'price' => 4500, 'tat' => 48],
        ];
    }
}
