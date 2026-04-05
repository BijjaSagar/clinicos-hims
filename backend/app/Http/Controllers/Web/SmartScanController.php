<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SmartScanController extends Controller
{
    public function index()
    {
        Log::info('SmartScanController@index', ['user' => auth()->id()]);
        return view('smart-scan.index');
    }

    public function upload(Request $request)
    {
        Log::info('SmartScanController@upload', ['user' => auth()->id()]);

        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'patient_id' => 'nullable|integer',
        ]);

        try {
            $file = $request->file('file');
            $path = $file->store('smart-scan/' . auth()->user()->clinic_id, 'public');
            Log::info('SmartScan: File stored', ['path' => $path, 'size' => $file->getSize()]);

            $parsedValues = self::attemptOcrParse($file);

            return response()->json([
                'success' => true,
                'file_path' => $path,
                'parsed_values' => $parsedValues,
                'message' => count($parsedValues) > 0
                    ? count($parsedValues) . ' lab values detected.'
                    : 'File uploaded. Manual OCR integration pending.',
            ]);
        } catch (\Throwable $e) {
            Log::error('SmartScan upload error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Upload failed: ' . $e->getMessage()], 500);
        }
    }

    public function parseResult(Request $request)
    {
        Log::info('SmartScanController@parseResult', ['user' => auth()->id()]);

        $text = $request->input('text', '');
        if (empty($text)) {
            return response()->json(['parsed_values' => [], 'message' => 'No text provided']);
        }

        $parsed = self::extractLabValues($text);
        Log::info('SmartScan parsed', ['values_found' => count($parsed)]);

        return response()->json([
            'parsed_values' => $parsed,
            'raw_text' => $text,
            'count' => count($parsed),
        ]);
    }

    private static function attemptOcrParse($file): array
    {
        Log::info('SmartScan: OCR parse not yet connected to AI service — using filename-based stub');
        return [];
    }

    private static function extractLabValues(string $text): array
    {
        $patterns = [
            'hemoglobin' => '/he?moglobin\s*[:=\-]?\s*([\d.]+)\s*(g\/?dl)?/i',
            'wbc' => '/(?:wbc|white\s*blood\s*cells?|total\s*leucocyte)\s*[:=\-]?\s*([\d.,]+)\s*(cells|\/cumm|\/ul)?/i',
            'rbc' => '/(?:rbc|red\s*blood\s*cells?)\s*[:=\-]?\s*([\d.]+)/i',
            'platelets' => '/platelets?\s*[:=\-]?\s*([\d.,]+)\s*(lakh|lac|\/cumm|\/ul)?/i',
            'hba1c' => '/(?:hba1c|glycated\s*hemoglobin)\s*[:=\-]?\s*([\d.]+)\s*%?/i',
            'fasting_glucose' => '/(?:fasting|fbs|fpg)\s*(?:blood\s*)?(?:sugar|glucose)\s*[:=\-]?\s*([\d.]+)/i',
            'pp_glucose' => '/(?:pp|postprandial|ppbs|2hr)\s*(?:blood\s*)?(?:sugar|glucose)\s*[:=\-]?\s*([\d.]+)/i',
            'creatinine' => '/creatinine\s*[:=\-]?\s*([\d.]+)\s*(mg\/?dl)?/i',
            'bun' => '/(?:bun|blood\s*urea\s*nitrogen)\s*[:=\-]?\s*([\d.]+)/i',
            'urea' => '/urea\s*[:=\-]?\s*([\d.]+)\s*(mg\/?dl)?/i',
            'sgpt' => '/(?:sgpt|alt)\s*[:=\-]?\s*([\d.]+)\s*(u\/?l|iu\/?l)?/i',
            'sgot' => '/(?:sgot|ast)\s*[:=\-]?\s*([\d.]+)\s*(u\/?l|iu\/?l)?/i',
            'total_cholesterol' => '/(?:total\s*)?cholesterol\s*[:=\-]?\s*([\d.]+)/i',
            'hdl' => '/hdl\s*[:=\-]?\s*([\d.]+)/i',
            'ldl' => '/ldl\s*[:=\-]?\s*([\d.]+)/i',
            'triglycerides' => '/triglycerides?\s*[:=\-]?\s*([\d.]+)/i',
            'tsh' => '/tsh\s*[:=\-]?\s*([\d.]+)\s*(miu\/?l|uiu\/?ml)?/i',
            't3' => '/(?:free\s*)?t3\s*[:=\-]?\s*([\d.]+)/i',
            't4' => '/(?:free\s*)?t4\s*[:=\-]?\s*([\d.]+)/i',
            'vitamin_d' => '/vitamin\s*d\s*[:=\-]?\s*([\d.]+)/i',
            'vitamin_b12' => '/vitamin\s*b12\s*[:=\-]?\s*([\d.]+)/i',
            'calcium' => '/calcium\s*[:=\-]?\s*([\d.]+)/i',
            'sodium' => '/sodium\s*[:=\-]?\s*([\d.]+)/i',
            'potassium' => '/potassium\s*[:=\-]?\s*([\d.]+)/i',
            'uric_acid' => '/uric\s*acid\s*[:=\-]?\s*([\d.]+)/i',
            'esr' => '/esr\s*[:=\-]?\s*([\d.]+)/i',
            'crp' => '/(?:c[\-\s]?reactive\s*protein|crp)\s*[:=\-]?\s*([\d.]+)/i',
        ];

        $normalRanges = [
            'hemoglobin' => ['unit' => 'g/dL', 'male' => [13.0, 17.0], 'female' => [12.0, 15.5]],
            'wbc' => ['unit' => '/cumm', 'range' => [4000, 11000]],
            'platelets' => ['unit' => '/cumm', 'range' => [150000, 400000]],
            'hba1c' => ['unit' => '%', 'range' => [4.0, 5.6]],
            'fasting_glucose' => ['unit' => 'mg/dL', 'range' => [70, 100]],
            'creatinine' => ['unit' => 'mg/dL', 'range' => [0.6, 1.2]],
            'sgpt' => ['unit' => 'U/L', 'range' => [7, 56]],
            'sgot' => ['unit' => 'U/L', 'range' => [10, 40]],
            'tsh' => ['unit' => 'mIU/L', 'range' => [0.4, 4.0]],
            'total_cholesterol' => ['unit' => 'mg/dL', 'range' => [0, 200]],
        ];

        $results = [];
        foreach ($patterns as $test => $pattern) {
            if (preg_match($pattern, $text, $match)) {
                $value = (float) str_replace(',', '', $match[1]);
                $unit = $match[2] ?? ($normalRanges[$test]['unit'] ?? '');
                $flag = 'normal';

                if (isset($normalRanges[$test]['range'])) {
                    [$low, $high] = $normalRanges[$test]['range'];
                    if ($value < $low) $flag = 'low';
                    elseif ($value > $high) $flag = 'high';
                }

                $results[] = [
                    'test' => $test,
                    'label' => ucwords(str_replace('_', ' ', $test)),
                    'value' => $value,
                    'unit' => trim($unit),
                    'flag' => $flag,
                ];
            }
        }

        Log::info('SmartScan: Extracted lab values', ['count' => count($results)]);
        return $results;
    }
}
