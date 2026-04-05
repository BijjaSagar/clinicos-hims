<?php

namespace App\Services;

use App\Models\Patient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ClinicalDecisionSupportService
{
    private static array $crossReactivity = [
        'penicillin' => ['amoxicillin', 'ampicillin', 'piperacillin', 'amoxyclav', 'augmentin'],
        'sulfonamides' => ['sulfasalazine', 'sulfamethoxazole', 'cotrimoxazole', 'dapsone'],
        'nsaids' => ['aspirin', 'ibuprofen', 'diclofenac', 'naproxen', 'piroxicam', 'ketorolac', 'aceclofenac', 'indomethacin'],
        'cephalosporins' => ['cefixime', 'ceftriaxone', 'cephalexin', 'cefuroxime', 'cefpodoxime'],
    ];

    private static array $contraindications = [
        'pregnancy' => ['methotrexate' => 'ABSOLUTE: Teratogenic', 'warfarin' => 'ABSOLUTE: Teratogenic', 'isotretinoin' => 'ABSOLUTE: Teratogenic', 'atorvastatin' => 'ABSOLUTE: Category X', 'enalapril' => 'ABSOLUTE: Fetal toxicity', 'doxycycline' => 'ABSOLUTE: Tooth discoloration', 'misoprostol' => 'ABSOLUTE: Abortifacient'],
        'renal_failure' => ['metformin' => 'Contraindicated if eGFR<30', 'ibuprofen' => 'RELATIVE: Worsens renal function', 'lithium' => 'DOSE_ADJUST: Narrow therapeutic index', 'gentamicin' => 'DOSE_ADJUST: Nephrotoxic'],
        'liver_failure' => ['methotrexate' => 'ABSOLUTE: Hepatotoxic', 'paracetamol' => 'DOSE_ADJUST: Max 2g/day', 'atorvastatin' => 'RELATIVE: Check LFT'],
        'asthma' => ['propranolol' => 'RELATIVE: Bronchospasm', 'atenolol' => 'RELATIVE: Bronchospasm', 'aspirin' => 'RELATIVE: Aspirin-sensitive asthma'],
        'peptic_ulcer' => ['ibuprofen' => 'RELATIVE: GI bleeding', 'aspirin' => 'RELATIVE: GI bleeding', 'prednisolone' => 'RELATIVE: Ulcer exacerbation'],
        'heart_failure' => ['ibuprofen' => 'RELATIVE: Fluid retention', 'verapamil' => 'RELATIVE: Negative inotropy', 'pioglitazone' => 'ABSOLUTE: Fluid retention'],
    ];

    public static function checkPrescription(int $patientId, array $newDrugs, int $clinicId): array
    {
        Log::info('CDS: Running prescription check', ['patient_id' => $patientId, 'drugs_count' => count($newDrugs), 'clinic_id' => $clinicId]);

        $alerts = [];

        try {
            $patient = Patient::query()->find($patientId);
            if (!$patient) {
                Log::warning('CDS: Patient not found', ['patient_id' => $patientId]);
                return ['alerts' => [], 'summary' => ['total' => 0], 'can_proceed' => true];
            }

            $allergies = $patient->allergiesListForClinicalChecks();
            Log::info('CDS: merged allergies for checks', ['patient_id' => $patientId, 'count' => count($allergies)]);

            $existingDrugs = self::getExistingMedications($patientId, $clinicId);
            $conditions = self::getPatientConditions($patientId);

            $age = null;
            if ($patient->dob) {
                $age = $patient->dob->age;
            } elseif ($patient->age_years) {
                $age = (int) $patient->age_years;
            }

            $sex = $patient->sex;

            foreach ($newDrugs as $drug) {
                $drugName = self::normalizeDrugName($drug['name'] ?? $drug ?? '');

                $alerts = array_merge($alerts, self::checkDrugAllergy($drugName, $allergies));
                $alerts = array_merge($alerts, self::checkContraindications($drugName, $conditions, $sex));

                if ($age !== null) {
                    $alerts = array_merge($alerts, self::checkAgeAlerts($drugName, $age));
                }
            }

            $allDrugNames = array_merge(
                $existingDrugs,
                array_map(fn($d) => self::normalizeDrugName($d['name'] ?? $d ?? ''), $newDrugs)
            );
            $alerts = array_merge($alerts, self::checkDrugInteractions($allDrugNames));
            $alerts = array_merge($alerts, self::checkDuplicateTherapy($newDrugs, $existingDrugs));

            Log::info('CDS: Check complete', ['patient_id' => $patientId, 'alerts_count' => count($alerts)]);
        } catch (\Throwable $e) {
            Log::error('CDS: Error during check', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        }

        $critical = count(array_filter($alerts, fn($a) => ($a['severity'] ?? '') === 'critical'));
        $high = count(array_filter($alerts, fn($a) => ($a['severity'] ?? '') === 'high'));

        return [
            'alerts' => array_values($alerts),
            'summary' => ['total' => count($alerts), 'critical' => $critical, 'high' => $high, 'moderate' => count($alerts) - $critical - $high],
            'can_proceed' => $critical === 0,
        ];
    }

    private static function checkDrugAllergy(string $drugName, array $allergies): array
    {
        $alerts = [];
        $allergiesLower = array_map('strtolower', array_map('trim', $allergies));

        if (in_array($drugName, $allergiesLower)) {
            Log::warning('CDS: Direct allergy match', ['drug' => $drugName]);
            $alerts[] = [
                'type' => 'allergy', 'severity' => 'critical',
                'title' => 'Known Drug Allergy', 'drug' => $drugName,
                'message' => "Patient has documented allergy to {$drugName}.",
                'recommendation' => 'Do NOT prescribe. Choose alternative.',
            ];
        }

        foreach (self::$crossReactivity as $allergen => $relatedDrugs) {
            if (in_array($allergen, $allergiesLower) && in_array($drugName, $relatedDrugs)) {
                Log::warning('CDS: Cross-reactivity alert', ['drug' => $drugName, 'allergen' => $allergen]);
                $alerts[] = [
                    'type' => 'allergy', 'severity' => 'high',
                    'title' => 'Cross-Reactivity Risk', 'drug' => $drugName,
                    'message' => "Patient allergic to {$allergen}. {$drugName} may cross-react.",
                    'recommendation' => 'Use with extreme caution or choose alternative.',
                ];
            }
        }

        return $alerts;
    }

    private static function checkDrugInteractions(array $drugs): array
    {
        $normalized = array_unique(array_map([self::class, 'normalizeDrugName'], $drugs));
        $normalized = array_values(array_filter($normalized));

        Log::info('CDS: checkDrugInteractions delegating to DrugInteractionService', [
            'drug_count' => count($normalized),
        ]);

        return DrugInteractionService::toCdsAlerts($normalized);
    }

    private static function checkContraindications(string $drugName, array $conditions, ?string $sex): array
    {
        $alerts = [];

        foreach ($conditions as $condition) {
            $condKey = strtolower(str_replace(' ', '_', $condition));
            if (!isset(self::$contraindications[$condKey])) continue;

            foreach (self::$contraindications[$condKey] as $drug => $detail) {
                if ($drug === $drugName || str_contains($drugName, $drug)) {
                    $severity = str_starts_with($detail, 'ABSOLUTE') ? 'critical' : 'moderate';
                    Log::warning('CDS: Contraindication found', ['drug' => $drugName, 'condition' => $condition]);
                    $alerts[] = [
                        'type' => 'contraindication', 'severity' => $severity,
                        'title' => 'Contraindication', 'drug' => $drugName,
                        'message' => "Contraindicated in {$condition}: {$detail}",
                        'recommendation' => $severity === 'critical' ? 'Do NOT prescribe.' : 'Use with caution; consider alternative.',
                    ];
                }
            }
        }

        return $alerts;
    }

    private static function checkAgeAlerts(string $drugName, int $age): array
    {
        $alerts = [];

        if ($age >= 65) {
            $elderlyRisks = [
                'diazepam' => 'BEERS criteria: Fall risk in elderly',
                'alprazolam' => 'BEERS criteria: Fall risk in elderly',
                'lorazepam' => 'BEERS criteria: Fall risk in elderly',
                'chlorpheniramine' => 'BEERS criteria: Anticholinergic effects',
                'hydroxyzine' => 'BEERS criteria: Anticholinergic sedation',
                'glibenclamide' => 'BEERS criteria: Prolonged hypoglycemia',
                'glimepiride' => 'BEERS criteria: Hypoglycemia risk in elderly',
            ];
            if (isset($elderlyRisks[$drugName])) {
                Log::info('CDS: Elderly alert', ['drug' => $drugName, 'age' => $age]);
                $alerts[] = [
                    'type' => 'age', 'severity' => 'moderate',
                    'title' => 'Elderly Precaution', 'drug' => $drugName,
                    'message' => $elderlyRisks[$drugName],
                    'recommendation' => 'Consider lower dose or safer alternative.',
                ];
            }
        }

        if ($age < 12) {
            $pediRisks = [
                'aspirin' => 'WARNING: Reye syndrome risk in children',
                'ciprofloxacin' => 'WARNING: Cartilage damage in children',
                'levofloxacin' => 'WARNING: Cartilage damage in children',
                'doxycycline' => 'WARNING: Dental staining under age 8',
                'tetracycline' => 'WARNING: Dental staining under age 8',
            ];
            if (isset($pediRisks[$drugName])) {
                Log::info('CDS: Pediatric alert', ['drug' => $drugName, 'age' => $age]);
                $alerts[] = [
                    'type' => 'age', 'severity' => 'high',
                    'title' => 'Pediatric Warning', 'drug' => $drugName,
                    'message' => $pediRisks[$drugName],
                    'recommendation' => 'Avoid in pediatric patients unless benefits outweigh risks.',
                ];
            }
        }

        return $alerts;
    }

    private static function checkDuplicateTherapy(array $newDrugs, array $existingDrugs): array
    {
        $alerts = [];
        $newNames = array_map(fn($d) => self::normalizeDrugName($d['name'] ?? $d ?? ''), $newDrugs);

        foreach ($newNames as $drug) {
            if (in_array($drug, $existingDrugs)) {
                Log::info('CDS: Duplicate therapy', ['drug' => $drug]);
                $alerts[] = [
                    'type' => 'duplicate', 'severity' => 'moderate',
                    'title' => 'Duplicate Therapy', 'drug' => $drug,
                    'message' => "Patient is already taking {$drug}.",
                    'recommendation' => 'Review if duplication is intentional.',
                ];
            }
        }

        return $alerts;
    }

    private static function getExistingMedications(int $patientId, int $clinicId): array
    {
        try {
            if (!Schema::hasTable('prescription_drugs') || !Schema::hasTable('prescriptions')) {
                return [];
            }

            $drugs = DB::table('prescription_drugs')
                ->join('prescriptions', 'prescription_drugs.prescription_id', '=', 'prescriptions.id')
                ->where('prescriptions.patient_id', $patientId)
                ->where('prescriptions.clinic_id', $clinicId)
                ->where('prescriptions.created_at', '>=', now()->subDays(90))
                ->pluck('prescription_drugs.drug_name')
                ->map(fn($n) => self::normalizeDrugName($n))
                ->unique()
                ->values()
                ->toArray();

            Log::info('CDS: Existing medications loaded', ['patient_id' => $patientId, 'count' => count($drugs)]);
            return $drugs;
        } catch (\Throwable $e) {
            Log::error('CDS: Failed to load existing medications', ['error' => $e->getMessage()]);
            return [];
        }
    }

    private static function getPatientConditions(int $patientId): array
    {
        try {
            $patient = Patient::query()->find($patientId, ['id', 'chronic_conditions']);
            if (!$patient) {
                return [];
            }
            $raw = $patient->chronic_conditions ?? [];
            if (!is_array($raw)) {
                return [];
            }

            return array_values(array_filter(array_map(static fn ($c) => trim((string) $c), $raw)));
        } catch (\Throwable $e) {
            Log::error('CDS: Failed to load conditions', ['error' => $e->getMessage()]);
            return [];
        }
    }

    private static function normalizeDrugName(string $name): string
    {
        $name = strtolower(trim($name));
        $name = preg_replace('/\s*\d+\s*(mg|ml|mcg|g|iu|%)\s*$/i', '', $name);
        $name = preg_replace('/\s*(tablet|capsule|syrup|injection|cream|gel|ointment|drops|inhaler|patch)\s*$/i', '', $name);
        return trim($name);
    }
}
