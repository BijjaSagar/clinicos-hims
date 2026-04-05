<?php

namespace App\Services;

use App\Models\Patient;
use Illuminate\Support\Facades\Log;

/**
 * Unified allergy + drug-interaction checks for EMR and pharmacy prescription flows.
 */
class PrescriptionSafetyService
{
    /**
     * @param  array<int, array{name?:string,generic?:string|null,dose?:string,...}>  $drugs
     * @return array{
     *   allergy_warnings: array<int, array{allergy:string,drug_name:string,generic_name:?string,message:string}>,
     *   interaction_warnings: array<int, array{drug_a:string,drug_b:string,severity:string,description:string}>,
     *   blocking: bool,
     *   blocking_reasons: string[]
     * }
     */
    public static function analyze(Patient $patient, array $drugs): array
    {
        Log::info('PrescriptionSafetyService::analyze started', [
            'patient_id' => $patient->id,
            'drug_rows' => count($drugs),
        ]);

        $allergyWarnings = self::allergyWarnings($patient, $drugs);

        $drugNamesForInteractionCheck = collect($drugs)
            ->map(function (array $drug): string {
                return trim((string) (($drug['generic'] ?? '') !== '' ? $drug['generic'] : ($drug['name'] ?? '')));
            })
            ->filter()
            ->values()
            ->all();

        $interactionWarnings = DrugInteractionService::check($drugNamesForInteractionCheck);

        $blockingReasons = [];
        if (! empty($allergyWarnings)) {
            $blockingReasons[] = 'allergy';
        }
        foreach ($interactionWarnings as $iw) {
            if (($iw['severity'] ?? '') === 'major') {
                $blockingReasons[] = 'major_drug_interaction:' . ($iw['drug_a'] ?? '') . '+' . ($iw['drug_b'] ?? '');
            }
        }

        $blocking = ! empty($blockingReasons);

        Log::info('PrescriptionSafetyService::analyze complete', [
            'patient_id' => $patient->id,
            'allergy_warning_count' => count($allergyWarnings),
            'interaction_warning_count' => count($interactionWarnings),
            'blocking' => $blocking,
        ]);

        return [
            'allergy_warnings' => $allergyWarnings,
            'interaction_warnings' => $interactionWarnings,
            'blocking' => $blocking,
            'blocking_reasons' => array_values(array_unique($blockingReasons)),
        ];
    }

    /**
     * @param  array<int, array{name?:string,generic?:string|null,...}>  $drugs
     * @return array<int, array{allergy:string,drug_name:string,generic_name:?string,message:string}>
     */
    public static function allergyWarnings(Patient $patient, array $drugs): array
    {
        $knownAllergies = $patient->allergiesListForClinicalChecks();

        Log::info('PrescriptionSafetyService::allergyWarnings', [
            'patient_id' => $patient->id,
            'merged_allergy_count' => count($knownAllergies),
            'drug_count' => count($drugs),
        ]);

        if (empty($knownAllergies) || empty($drugs)) {
            return [];
        }

        $warnings = [];

        foreach ($drugs as $drug) {
            $drugName = strtolower(trim((string) ($drug['name'] ?? '')));
            $genericName = strtolower(trim((string) ($drug['generic'] ?? '')));

            foreach ($knownAllergies as $allergy) {
                $matchesDrugName = $drugName !== '' && str_contains($drugName, $allergy);
                $matchesGenericName = $genericName !== '' && str_contains($genericName, $allergy);

                if ($matchesDrugName || $matchesGenericName) {
                    $warnings[] = [
                        'allergy' => $allergy,
                        'drug_name' => (string) ($drug['name'] ?? ''),
                        'generic_name' => ! empty($drug['generic']) ? (string) $drug['generic'] : null,
                        'message' => 'Potential allergy conflict: patient allergy "' . $allergy . '" matched with prescribed drug "' . ($drug['name'] ?? '') . '".',
                    ];
                }
            }
        }

        return $warnings;
    }
}
