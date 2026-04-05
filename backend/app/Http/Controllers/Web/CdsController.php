<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\ClinicalDecisionSupportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CdsController extends Controller
{
    public function checkPrescription(Request $request): JsonResponse
    {
        Log::info('CdsController@checkPrescription', ['user' => auth()->id()]);

        $validated = $request->validate([
            'patient_id' => 'required|integer|exists:patients,id',
            'drugs' => 'required|array|min:1',
            'drugs.*.name' => 'required|string',
        ]);

        $clinicId = auth()->user()->clinic_id;
        $result = ClinicalDecisionSupportService::checkPrescription(
            $validated['patient_id'],
            $validated['drugs'],
            $clinicId
        );

        Log::info('CDS check result', ['patient_id' => $validated['patient_id'], 'summary' => $result['summary']]);

        return response()->json($result);
    }

    public function interactionLookup(Request $request): JsonResponse
    {
        Log::info('CdsController@interactionLookup', ['drug' => $request->get('drug')]);

        $drugName = strtolower(trim($request->get('drug', '')));
        if (empty($drugName)) {
            return response()->json(['interactions' => [], 'message' => 'Please provide a drug name']);
        }

        $result = ClinicalDecisionSupportService::checkPrescription(
            0,
            [['name' => $drugName]],
            auth()->user()->clinic_id ?? 0
        );

        return response()->json([
            'drug' => $drugName,
            'interactions' => $result['alerts'],
        ]);
    }

    public function allergyCheck(Request $request): JsonResponse
    {
        Log::info('CdsController@allergyCheck', ['patient_id' => $request->get('patient_id'), 'drug' => $request->get('drug')]);

        $patientId = (int) $request->get('patient_id', 0);
        $drug = $request->get('drug', '');

        if (!$patientId || !$drug) {
            return response()->json(['alerts' => [], 'message' => 'Provide patient_id and drug']);
        }

        $result = ClinicalDecisionSupportService::checkPrescription(
            $patientId,
            [['name' => $drug]],
            auth()->user()->clinic_id ?? 0
        );

        $allergyAlerts = array_filter($result['alerts'], fn($a) => $a['type'] === 'allergy');

        return response()->json([
            'drug' => $drug,
            'patient_id' => $patientId,
            'allergy_alerts' => array_values($allergyAlerts),
            'safe' => count($allergyAlerts) === 0,
        ]);
    }
}
