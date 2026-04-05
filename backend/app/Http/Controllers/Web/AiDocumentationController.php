<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Visit;
use App\Services\AI\ClinicAiCredentialResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

/**
 * AI Documentation Assistant Controller
 * Handles voice-to-EMR transcription and AI-powered clinical note generation
 */
class AiDocumentationController extends Controller
{
    public function __construct(
        private readonly ClinicAiCredentialResolver $aiCredentials
    ) {}
    /**
     * Show the AI assistant interface
     */
    public function index(): View
    {
        Log::info('AiDocumentationController: Loading AI assistant page');
        return view('ai-assistant.index');
    }

    /**
     * Transcribe audio to text using Whisper API
     */
    public function transcribe(Request $request): JsonResponse
    {
        Log::info('AiDocumentationController: Transcribing audio');

        // Browsers often label WebM as video/webm or application/octet-stream — validate extension + size only.
        $request->validate([
            'audio' => [
                'required',
                Rule::file()
                    ->extensions(['webm', 'mp3', 'wav', 'm4a', 'mp4', 'mpeg', 'mpga', 'oga', 'ogg'])
                    ->max(25600),
            ],
            'language' => 'nullable|string|in:en,hi,mr,ta,te,kn,ml,bn,gu,pa,auto',
        ]);

        $clinic = auth()->user()->clinic;
        $resolution = $this->aiCredentials->openaiKeyResolution($clinic);
        $apiKey = $this->normalizeOpenAiSecretKey($resolution['key']);
        $sourceUsed = $resolution['source'];

        $diagnostic = [
            'openai_key_source_used' => $sourceUsed,
            'env_openai_configured' => strlen(trim((string) config('services.openai.api_key', ''))) > 0,
            'clinic_openai_key_stored' => $clinic && filled(data_get($clinic->settings, 'ai_openai_key_enc')),
            'clinic_key_overrides_env' => (bool) config('services.openai.clinic_key_overrides_env', false),
        ];

        if (! $apiKey) {
            Log::warning('AiDocumentationController: OpenAI API key not configured', $diagnostic);

            return response()->json([
                'success' => false,
                'error' => 'AI service not configured. Set OPENAI_API_KEY in backend/.env or add a key under Settings → AI & APIs.',
                'diagnostic' => $diagnostic,
            ], 503);
        }

        if (! $this->looksLikeOpenAiSecretKey($apiKey)) {
            Log::warning('AiDocumentationController: OPENAI_API_KEY is not a valid OpenAI secret format', [
                'key_length' => strlen($apiKey),
                'starts_with_sk' => str_starts_with(trim($apiKey), 'sk-'),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'OPENAI_API_KEY must be an OpenAI secret key from https://platform.openai.com/api-keys — it must start with sk- or sk-proj- (often 40+ characters). '
                    .'You may have pasted an Anthropic key, a JWT, or another provider token. Update backend/.env and run: php artisan config:clear',
                'diagnostic' => array_merge($diagnostic, [
                    'openai_key_format_ok' => false,
                ]),
            ], 422);
        }

        Log::info('AiDocumentationController: OpenAI key source for Whisper', $diagnostic);

        try {
            $audio = $request->file('audio');
            $language = $request->input('language', 'auto');

            $payload = [
                'model' => 'whisper-1',
                'response_format' => 'json',
                'prompt' => 'Clinical consultation audio from Indian clinic. Accurately transcribe Hindi-English mixed speech and preserve medical terms, drug names, and vitals.',
            ];
            if ($language !== 'auto') {
                $payload['language'] = $language;
            }

            Log::info('AiDocumentationController: Whisper request payload prepared', [
                'language' => $language,
                'audio_name' => $audio->getClientOriginalName(),
                'audio_size' => $audio->getSize(),
                'client_mime' => $audio->getClientMimeType(),
                'detected_mime' => $audio->getMimeType(),
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
            ])
                ->timeout(120)
                ->connectTimeout(30)
                ->attach(
                    'file',
                    file_get_contents($audio->getRealPath()),
                    $audio->getClientOriginalName() ?: 'dictation.webm',
                )
                ->post('https://api.openai.com/v1/audio/transcriptions', $payload);

            if ($response->successful()) {
                $transcription = $response->json('text');
                Log::info('AiDocumentationController: Transcription successful', ['length' => strlen((string) $transcription)]);

                return response()->json([
                    'success' => true,
                    'transcription' => $transcription,
                ]);
            }

            $body = $response->json();
            $openaiMsg = is_array($body) && isset($body['error']['message'])
                ? (string) $body['error']['message']
                : $response->body();
            $safeMsg = is_string($openaiMsg) ? $this->sanitizeOpenAiErrorMessage($openaiMsg) : '';
            $httpStatus = $response->status();
            $missingModelScope = $httpStatus === 401 && $this->openAiErrorMissingModelRequestScope((string) $openaiMsg);
            Log::error('AiDocumentationController: Whisper API error', [
                'status' => $httpStatus,
                'body' => $safeMsg !== '' ? $safeMsg : '[non-json body]',
                'whisper_denied' => $httpStatus === 403 && $this->openAiErrorDeniesWhisperModelAccess((string) $openaiMsg),
                'restricted_key_missing_model_scope' => $missingModelScope,
            ]);

            $userMsg = match (true) {
                $missingModelScope => 'OpenAI blocked Whisper: this API key cannot call models (missing scope model.request). You are using a restricted API key without Model access. At https://platform.openai.com/api-keys create a new standard secret key (full access), or edit the restricted key and enable Model / model.request for audio. Put OPENAI_API_KEY=sk-... in backend/.env and run: php artisan config:clear.',
                $httpStatus === 401 => 'OpenAI rejected the API key (401). Create a new secret key at https://platform.openai.com/api-keys (must start with sk-) and set OPENAI_API_KEY in backend/.env, then run: php artisan config:clear.',
                $httpStatus === 429 => 'OpenAI rate limit reached (429). Try again in a minute.',
                $httpStatus === 403 && $this->openAiErrorDeniesWhisperModelAccess((string) $openaiMsg) => 'OpenAI blocked Whisper for this API key’s project (403). The key is valid but project limits do not allow model whisper-1. In https://platform.openai.com → API keys, open the project tied to this key (or create a new key under a project with Audio / Speech-to-text enabled). Ensure billing and organization model access include Whisper. Then retry dictation.',
                $httpStatus === 403 => 'OpenAI denied access (403). '.($safeMsg !== '' ? $safeMsg : 'Check project permissions, model allowlist, and billing.'),
                default => 'OpenAI Whisper error: '.($safeMsg !== '' ? $safeMsg : 'Unknown error'),
            };

            return response()->json([
                'success' => false,
                'error' => $userMsg,
                'diagnostic' => array_merge($diagnostic, [
                    'openai_http_status' => $httpStatus,
                    'openai_error_message' => $safeMsg !== '' ? $safeMsg : null,
                    'openai_restricted_key_missing_model_scope' => $missingModelScope,
                ]),
            ], $httpStatus >= 400 && $httpStatus < 600 ? $httpStatus : 500);
        } catch (\Throwable $e) {
            Log::error('AiDocumentationController: Transcription error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => 'Transcription error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate structured EMR notes from transcription
     */
    public function generateNotes(Request $request): JsonResponse
    {
        Log::info('AiDocumentationController: Generating clinical notes');

        $validated = $request->validate([
            'transcription' => 'required|string|max:10000',
            'specialty' => 'nullable|string',
            'note_type' => 'nullable|string|in:soap,progress,initial,discharge',
            'visit_id' => 'nullable|exists:visits,id',
            'language_context' => 'nullable|string|in:en,hi,mixed',
        ]);

        $clinic = auth()->user()->clinic;
        $apiKey = $this->aiCredentials->openaiApiKey($clinic);

        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'error' => 'AI service not configured. Owner: Settings → AI & APIs, or OPENAI_API_KEY.',
            ], 503);
        }

        $chatModel = $this->aiCredentials->openaiChatModel($clinic);

        $specialty = $validated['specialty'] ?? 'general';
        $noteType = $validated['note_type'] ?? 'soap';
        $languageContext = $validated['language_context'] ?? 'mixed';

        $systemPrompt = $this->buildSystemPrompt($specialty, $noteType, $languageContext);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => $chatModel,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => "Convert this clinical conversation to structured EMR notes:\n\n" . $validated['transcription']],
                ],
                'temperature' => 0.3,
                'max_tokens' => 2000,
            ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content');
                $structuredNotes = $this->parseNotes($content, $noteType);
                $structuredNotes['consultation_summary'] = $this->buildConsultationSummary($structuredNotes);
                
                Log::info('AiDocumentationController: Notes generated', [
                    'note_type' => $noteType,
                    'language_context' => $languageContext,
                    'summary_length' => strlen($structuredNotes['consultation_summary'] ?? ''),
                ]);

                return response()->json([
                    'success' => true,
                    'notes' => $structuredNotes,
                    'raw_content' => $content,
                ]);
            } else {
                Log::error('AiDocumentationController: GPT API error', ['response' => $response->body()]);
                return response()->json([
                    'success' => false,
                    'error' => 'Note generation failed',
                ], 500);
            }
        } catch (\Throwable $e) {
            Log::error('AiDocumentationController: Note generation error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Extract ICD-10 codes from clinical notes
     */
    public function extractCodes(Request $request): JsonResponse
    {
        Log::info('AiDocumentationController: Extracting ICD codes');

        $validated = $request->validate([
            'notes' => 'required|string|max:5000',
            'specialty' => 'nullable|string',
        ]);

        $clinic = auth()->user()->clinic;
        $apiKey = $this->aiCredentials->openaiApiKey($clinic);

        if (!$apiKey) {
            return response()->json(['success' => false, 'error' => 'AI service not configured'], 503);
        }

        $fastModel = $this->aiCredentials->openaiFastModel($clinic);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => $fastModel,
                'messages' => [
                    ['role' => 'system', 'content' => "You are a medical coding specialist. Extract relevant ICD-10-CM codes from clinical notes. Return ONLY a JSON array with objects containing 'code' and 'description' fields. Focus on primary diagnoses and relevant secondary conditions."],
                    ['role' => 'user', 'content' => $validated['notes']],
                ],
                'temperature' => 0.1,
                'max_tokens' => 500,
            ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content');
                $codes = json_decode($content, true) ?? [];
                
                Log::info('AiDocumentationController: ICD codes extracted', ['count' => count($codes)]);

                return response()->json([
                    'success' => true,
                    'codes' => $codes,
                ]);
            }

            return response()->json(['success' => false, 'error' => 'Code extraction failed'], 500);
        } catch (\Throwable $e) {
            Log::error('AiDocumentationController: Code extraction error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Save AI-generated notes to a visit
     */
    public function saveToVisit(Request $request, Visit $visit): JsonResponse
    {
        Log::info('AiDocumentationController: Saving notes to visit', ['visit_id' => $visit->id]);

        abort_unless(auth()->user()->clinic_id === $visit->clinic_id, 403);

        $validated = $request->validate([
            'chief_complaint' => 'nullable|string|max:500',
            'history' => 'nullable|string|max:5000',
            'examination' => 'nullable|string|max:5000',
            'assessment' => 'nullable|string|max:2000',
            'plan' => 'nullable|string|max:2000',
            'icd_codes' => 'nullable|array',
        ]);

        try {
            $updateData = [];
            
            if (!empty($validated['chief_complaint'])) {
                $updateData['chief_complaint'] = $validated['chief_complaint'];
            }
            if (!empty($validated['history'])) {
                $updateData['history_of_present_illness'] = $validated['history'];
            }
            if (!empty($validated['examination'])) {
                $updateData['physical_examination'] = $validated['examination'];
            }
            if (!empty($validated['assessment'])) {
                $updateData['assessment'] = $validated['assessment'];
            }
            if (!empty($validated['plan'])) {
                $updateData['plan'] = $validated['plan'];
            }
            if (!empty($validated['icd_codes'])) {
                $updateData['icd_codes'] = json_encode($validated['icd_codes']);
            }

            $visit->update($updateData);

            Log::info('AiDocumentationController: Notes saved to visit', ['visit_id' => $visit->id]);

            return response()->json([
                'success' => true,
                'message' => 'Notes saved to patient record',
            ]);
        } catch (\Throwable $e) {
            Log::error('AiDocumentationController: Save error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Generate clinical letter (referral, discharge summary)
     */
    public function generateLetter(Request $request): JsonResponse
    {
        Log::info('AiDocumentationController: Generating clinical letter');

        $validated = $request->validate([
            'letter_type' => 'required|string|in:referral,discharge,follow_up,medical_certificate',
            'patient_info' => 'required|array',
            'clinical_info' => 'required|string|max:5000',
            'recipient' => 'nullable|string',
        ]);

        $clinic = auth()->user()->clinic;
        $apiKey = $this->aiCredentials->openaiApiKey($clinic);

        if (!$apiKey) {
            return response()->json(['success' => false, 'error' => 'AI service not configured'], 503);
        }

        $fastModel = $this->aiCredentials->openaiFastModel($clinic);

        $letterPrompts = [
            'referral' => "Generate a professional medical referral letter.",
            'discharge' => "Generate a comprehensive discharge summary.",
            'follow_up' => "Generate a follow-up appointment letter.",
            'medical_certificate' => "Generate a medical certificate/fitness certificate.",
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => $fastModel,
                'messages' => [
                    ['role' => 'system', 'content' => "You are a medical documentation assistant. " . $letterPrompts[$validated['letter_type']] . " Use proper medical terminology and formal language. Format as a proper letter."],
                    ['role' => 'user', 'content' => "Patient: " . json_encode($validated['patient_info']) . "\n\nClinical Information:\n" . $validated['clinical_info'] . ($validated['recipient'] ? "\n\nRecipient: " . $validated['recipient'] : '')],
                ],
                'temperature' => 0.3,
                'max_tokens' => 1500,
            ]);

            if ($response->successful()) {
                $letter = $response->json('choices.0.message.content');
                
                return response()->json([
                    'success' => true,
                    'letter' => $letter,
                ]);
            }

            return response()->json(['success' => false, 'error' => 'Letter generation failed'], 500);
        } catch (\Throwable $e) {
            Log::error('AiDocumentationController: Letter generation error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Build system prompt based on specialty and note type
     */
    private function buildSystemPrompt(string $specialty, string $noteType, string $languageContext = 'mixed'): string
    {
        $specialtyContext = [
            'dermatology' => 'Focus on skin conditions, lesion descriptions (size, shape, color, distribution), dermatoscopy findings, and topical/systemic treatments.',
            'ophthalmology' => 'Include visual acuity, IOP, slit lamp findings, fundus examination, and refraction details.',
            'orthopaedics' => 'Include range of motion, muscle power grading, special tests, and imaging findings.',
            'ent' => 'Include ear/nose/throat examination findings, hearing tests, and endoscopy findings.',
            'gynaecology' => 'Include menstrual history, obstetric history, per vaginal examination, and USG findings.',
            'dental' => 'Include tooth charting, periodontal status, and treatment procedures.',
            'physiotherapy' => 'Include ROM measurements, muscle strength, functional assessments, and exercise prescriptions.',
            'general' => 'Include relevant systems review and general physical examination.',
        ];

        $noteStructures = [
            'soap' => 'Use SOAP format: S (Subjective - patient complaints, history), O (Objective - examination findings, vitals, investigations), A (Assessment - diagnoses), P (Plan - treatment, follow-up).',
            'progress' => 'Use Progress Note format: Current status, changes since last visit, response to treatment, ongoing issues, updated plan.',
            'initial' => 'Use Initial Evaluation format: Chief complaint, detailed HPI, PMH, medications, allergies, family/social history, ROS, examination, assessment, plan.',
            'discharge' => 'Use Discharge Summary format: Admission diagnosis, hospital course, procedures, discharge diagnosis, discharge medications, follow-up instructions.',
        ];

        $specialtyText = $specialtyContext[$specialty] ?? $specialtyContext['general'];
        $noteStructureText = $noteStructures[$noteType] ?? $noteStructures['soap'];
        
        $languageGuidance = match ($languageContext ?? 'mixed') {
            'hi' => 'Conversation may include Hindi clinical terms. Normalize to professional English EMR while retaining original clinical meaning.',
            'en' => 'Conversation is primarily English clinical dictation.',
            default => 'Conversation may be code-mixed Hindi-English. Preserve intent accurately and normalize output to professional English EMR.',
        };

        $prompt = "You are a medical documentation assistant for {$specialty}. {$specialtyText}\n\n";
        $prompt .= "{$noteStructureText}\n\n";
        $prompt .= "Guidelines:\n";
        $prompt .= "- {$languageGuidance}\n";
        $prompt .= "- Use proper medical terminology\n";
        $prompt .= "- Be concise but comprehensive\n";
        $prompt .= "- Include relevant negative findings\n";
        $prompt .= "- Extract all mentioned medications with dosages\n";
        $prompt .= "- Note any red flags or urgent findings\n";
        $prompt .= "- Output in a structured, parseable format\n";
        $prompt .= "- Use Indian medical conventions (units, medications)";

        return $prompt;
    }

    private function buildConsultationSummary(array $notes): string
    {
        $parts = [];

        if (!empty($notes['chief_complaint'])) {
            $parts[] = 'Chief complaint: ' . $notes['chief_complaint'];
        }
        if (!empty($notes['assessment'])) {
            $parts[] = 'Assessment: ' . $notes['assessment'];
        }
        if (!empty($notes['plan'])) {
            $parts[] = 'Plan: ' . $notes['plan'];
        }

        $summary = implode(' ', $parts);
        if (!$summary && !empty($notes['history'])) {
            $summary = mb_substr((string) $notes['history'], 0, 280);
        }

        return trim($summary);
    }

    /**
     * Parse AI-generated notes into structured format
     */
    private function parseNotes(string $content, string $noteType): array
    {
        $notes = [
            'chief_complaint' => '',
            'history' => '',
            'examination' => '',
            'assessment' => '',
            'plan' => '',
            'vitals' => [],
            'medications' => [],
            'investigations' => [],
        ];

        if ($noteType === 'soap') {
            if (preg_match('/S(?:ubjective)?[:\s]*(.+?)(?=O(?:bjective)?[:\s]|$)/is', $content, $matches)) {
                $subjective = trim($matches[1]);
                if (preg_match('/Chief\s*Complaint[:\s]*(.+?)(?=History|HPI|$)/is', $subjective, $cc)) {
                    $notes['chief_complaint'] = trim($cc[1]);
                }
                $notes['history'] = $subjective;
            }
            
            if (preg_match('/O(?:bjective)?[:\s]*(.+?)(?=A(?:ssessment)?[:\s]|$)/is', $content, $matches)) {
                $notes['examination'] = trim($matches[1]);
            }
            
            if (preg_match('/A(?:ssessment)?[:\s]*(.+?)(?=P(?:lan)?[:\s]|$)/is', $content, $matches)) {
                $notes['assessment'] = trim($matches[1]);
            }
            
            if (preg_match('/P(?:lan)?[:\s]*(.+?)$/is', $content, $matches)) {
                $notes['plan'] = trim($matches[1]);
            }
        } else {
            $notes['history'] = $content;
        }

        if (preg_match_all('/(?:BP|Blood Pressure)[:\s]*(\d{2,3}\/\d{2,3})/i', $content, $matches)) {
            $notes['vitals']['bp'] = $matches[1][0] ?? null;
        }
        if (preg_match_all('/(?:HR|Pulse|Heart Rate)[:\s]*(\d{2,3})/i', $content, $matches)) {
            $notes['vitals']['pulse'] = $matches[1][0] ?? null;
        }
        if (preg_match_all('/(?:Temp|Temperature)[:\s]*([\d.]+)/i', $content, $matches)) {
            $notes['vitals']['temperature'] = $matches[1][0] ?? null;
        }
        if (preg_match_all('/(?:SpO2|Oxygen|O2)[:\s]*(\d{2,3})/i', $content, $matches)) {
            $notes['vitals']['spo2'] = $matches[1][0] ?? null;
        }

        return $notes;
    }

    /**
     * Trim .env noise; strip a leading "-" before sk- (common paste error; see logs echoing "-sk-proj…").
     */
    private function normalizeOpenAiSecretKey(mixed $key): ?string
    {
        if (! is_string($key)) {
            return null;
        }
        $t = trim($key);
        $t = trim($t, "\"' ");
        if ($t !== '' && str_starts_with($t, '-') && str_contains($t, 'sk-')) {
            Log::info('AiDocumentationController: normalized OPENAI_API_KEY (removed leading hyphen before sk-)');
            $t = ltrim($t, '-');
            $t = trim($t);
        }

        return $t === '' ? null : $t;
    }

    /**
     * OpenAI API secret keys start with sk- (e.g. sk-proj-…). Other strings will get 401 from OpenAI.
     */
    private function looksLikeOpenAiSecretKey(string $key): bool
    {
        $t = trim($key);

        return $t !== '' && str_starts_with($t, 'sk-') && strlen($t) >= 20;
    }

    /**
     * 403 body: Project `proj_…` does not have access to model `whisper-1`.
     */
    private function openAiErrorDeniesWhisperModelAccess(string $msg): bool
    {
        $m = strtolower($msg);

        return str_contains($m, 'whisper') && str_contains($m, 'does not have access');
    }

    /**
     * Restricted API keys can omit model.request; Whisper then returns 401 with "Missing scopes: model.request".
     */
    private function openAiErrorMissingModelRequestScope(string $msg): bool
    {
        $m = strtolower($msg);

        return str_contains($m, 'model.request')
            && (str_contains($m, 'missing scopes') || str_contains($m, 'insufficient permissions') || str_contains($m, 'restricted api key'));
    }

    /**
     * Strip echoed key material from OpenAI error strings (401 responses repeat part of the sent key).
     * Redact project ids in 403 messages for logs/diagnostics.
     */
    private function sanitizeOpenAiErrorMessage(string $msg): string
    {
        $msg = trim($msg);
        if ($msg === '') {
            return '';
        }
        $msg = preg_replace('/Incorrect API key provided:\s*.+$/i', 'Incorrect API key provided.', $msg) ?? $msg;
        $msg = preg_replace('/`proj_[a-zA-Z0-9]+`/', '`proj_[redacted]`', $msg) ?? $msg;
        $msg = preg_replace('/\bproj_[a-zA-Z0-9]+\b/', 'proj_[redacted]', $msg) ?? $msg;

        return mb_substr($msg, 0, 500);
    }
}
