<?php

namespace App\Services\AI;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * OpenAI Whisper transcription (used by API AI assistant).
 */
class WhisperService
{
    public function __construct(
        private readonly ClinicAiCredentialResolver $credentials
    ) {}

    public function transcribe(UploadedFile $file, string $language = 'auto'): string
    {
        $apiKey = $this->credentials->openaiApiKey();
        if (!$apiKey) {
            Log::warning('WhisperService: OpenAI API key not configured (Settings or OPENAI_API_KEY)');
            return '';
        }

        Log::info('WhisperService: transcribe', [
            'language' => $language,
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
        ]);

        try {
            $payload = [
                'model' => 'whisper-1',
                'response_format' => 'json',
                'prompt' => 'Clinical consultation audio from an Indian clinic. Accurately transcribe mixed Indian languages (Hindi, Marathi, Telugu, Tamil, English, etc.) and preserve medical terms, drug names, vitals, and units.',
            ];
            if ($language !== 'auto') {
                $payload['language'] = $language;
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
            ])->attach(
                'file',
                file_get_contents($file->path()),
                $file->getClientOriginalName()
            )->post('https://api.openai.com/v1/audio/transcriptions', $payload);

            if ($response->successful()) {
                $text = (string) $response->json('text', '');
                Log::info('WhisperService: transcribe ok', ['length' => strlen($text)]);

                return $text;
            }

            $raw = $response->body();
            $missingScope = str_contains(strtolower($raw), 'model.request')
                && (str_contains(strtolower($raw), 'missing scopes') || str_contains(strtolower($raw), 'insufficient permissions'));
            Log::error('WhisperService: API error', [
                'status' => $response->status(),
                'restricted_key_missing_model_scope' => $missingScope,
                'body_preview' => mb_substr($raw, 0, 500),
            ]);

            return '';
        } catch (\Throwable $e) {
            Log::error('WhisperService: exception', ['error' => $e->getMessage()]);

            return '';
        }
    }
}
