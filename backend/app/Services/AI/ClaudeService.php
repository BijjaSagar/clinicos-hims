<?php

namespace App\Services\AI;

use App\Models\Clinic;
use App\Models\Visit;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Anthropic Claude — structured EMR extraction, summaries, Rx hints (API AI assistant).
 */
class ClaudeService
{
    public function __construct(
        private readonly ClinicAiCredentialResolver $credentials
    ) {}

    /**
     * @return array{ok: bool, text: string, error: ?string, status: ?int}
     */
    private function callAnthropicMessages(string $system, string $user, int $maxTokens, ?Clinic $clinic = null): array
    {
        $key = $this->credentials->anthropicApiKey($clinic);
        if (!$key) {
            Log::warning('ClaudeService: Anthropic API key not configured (Settings or ANTHROPIC_API_KEY)');

            return ['ok' => false, 'text' => '', 'error' => 'No Anthropic API key. Add it under Settings → AI & APIs or set ANTHROPIC_API_KEY in .env.', 'status' => null];
        }

        $model = $this->credentials->anthropicModel($clinic);

        Log::info('ClaudeService: messages request', [
            'system_len' => strlen($system),
            'user_len' => strlen($user),
            'max_tokens' => $maxTokens,
            'model' => $model,
        ]);

        try {
            $response = Http::timeout(120)
                ->connectTimeout(15)
                ->withHeaders([
                    'x-api-key' => $key,
                    'anthropic-version' => '2023-06-01',
                    'content-type' => 'application/json',
                ])->post('https://api.anthropic.com/v1/messages', [
                    'model' => $model,
                    'max_tokens' => $maxTokens,
                    'system' => $system,
                    'messages' => [
                        ['role' => 'user', 'content' => $user],
                    ],
                ]);

            if ($response->successful()) {
                $text = '';
                foreach ($response->json('content', []) as $block) {
                    if (($block['type'] ?? '') === 'text') {
                        $text .= $block['text'] ?? '';
                    }
                }
                Log::info('ClaudeService: messages ok', ['out_len' => strlen($text)]);

                return ['ok' => true, 'text' => trim($text), 'error' => null, 'status' => $response->status()];
            }

            $json = $response->json();
            $msg = is_array($json) && isset($json['error']['message'])
                ? (string) $json['error']['message']
                : Str::limit((string) $response->body(), 800);
            Log::error('ClaudeService: API error', [
                'status' => $response->status(),
                'model' => $model,
                'body' => Str::limit((string) $response->body(), 2000),
            ]);

            return [
                'ok' => false,
                'text' => '',
                'error' => 'Anthropic returned '.$response->status().': '.$msg,
                'status' => $response->status(),
            ];
        } catch (\Throwable $e) {
            Log::error('ClaudeService: exception', ['error' => $e->getMessage()]);

            return ['ok' => false, 'text' => '', 'error' => 'Request failed: '.$e->getMessage(), 'status' => null];
        }
    }

    public function extractStructuredFields(string $systemPrompt, string $userText, ?Clinic $clinic = null): array
    {
        $user = "Transcript:\n{$userText}\n\nReply with ONLY valid JSON object, no markdown.";
        $res = $this->callAnthropicMessages($systemPrompt, $user, 4096, $clinic);
        $raw = $res['ok'] ? $res['text'] : '';
        if ($raw === '') {
            return [];
        }

        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        if (preg_match('/\{[\s\S]*\}/', $raw, $m)) {
            $decoded = json_decode($m[0], true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        Log::warning('ClaudeService: extractStructuredFields could not parse JSON', ['raw_len' => strlen($raw)]);

        return [];
    }

    /**
     * Dermatology (and similar) often persist default PASI/DLQI/IGA = 0 before the clinician finishes entry.
     * Those zeros in visit_scales can contradict History Notes or structured_data — do not send them to the model in that case.
     *
     * @return array{0: bool, 1: string|null} [suppress, reason for logs]
     */
    private function shouldSuppressDbScalesForSummary(Visit $visit, int $historyTrimLen, bool $hasScalesTable): array
    {
        if (! $hasScalesTable || $visit->scales->isEmpty()) {
            return [false, ''];
        }
        $allZeroOrNull = true;
        foreach ($visit->scales as $s) {
            $raw = $s->score;
            if ($raw === null || $raw === '') {
                continue;
            }
            if ((float) $raw > 0.00001) {
                $allZeroOrNull = false;
                break;
            }
        }
        if (! $allZeroOrNull) {
            return [false, ''];
        }
        $sd = $visit->structured_data ?? [];
        $formPasi = isset($sd['pasi_score']) && $sd['pasi_score'] !== '' && $sd['pasi_score'] !== null
            ? (float) $sd['pasi_score'] : null;
        $formDlqi = isset($sd['dlqi_score']) && $sd['dlqi_score'] !== '' && $sd['dlqi_score'] !== null
            ? (float) $sd['dlqi_score'] : null;
        $structuredClaimsNonZero = ($formPasi !== null && $formPasi > 0.00001)
            || ($formDlqi !== null && $formDlqi > 0.00001);

        if ($structuredClaimsNonZero) {
            return [true, 'suppressed: DB scales all zero but structured_data has non-zero pasi/dlqi score fields'];
        }
        if ($historyTrimLen >= 20) {
            return [true, 'suppressed: DB scales all zero while history_notes has text (likely chart defaults)'];
        }

        return [false, ''];
    }

    /**
     * PASI/DLQI/IGA stored in structured_data often stay at 0 until the form is fully used; the model treats them as fact.
     * When history has real text and scale fields look like all-zero defaults, omit those keys from the excerpt sent to the model.
     *
     * @param  array<string, mixed>  $sd
     * @return array{0: array<string, mixed>, 1: bool} [data, stripped_any]
     */
    private function stripDefaultDermatologyScaleFieldsFromStructuredData(array $sd, int $historyTrimLen): array
    {
        if ($historyTrimLen < 20) {
            return [$sd, false];
        }
        if (! $this->dermatologyScaleFieldsLookLikeAllZeros($sd)) {
            return [$sd, false];
        }
        $keysToRemove = ['pasi_score', 'pasi_data', 'dlqi_score', 'dlqi_data', 'iga_score'];
        $removed = [];
        foreach ($keysToRemove as $k) {
            if (array_key_exists($k, $sd)) {
                $removed[] = $k;
                unset($sd[$k]);
            }
        }
        if ($removed !== []) {
            Log::info('ClaudeService: stripped default-zero dermatology scale keys from structured_data for summary', [
                'removed' => $removed,
            ]);
        }

        return [$sd, $removed !== []];
    }

    /**
     * @param  array<string, mixed>  $sd
     */
    private function dermatologyScaleFieldsLookLikeAllZeros(array $sd): bool
    {
        $pasi = isset($sd['pasi_score']) ? (float) $sd['pasi_score'] : null;
        if ($pasi !== null && $pasi > 0.00001) {
            return false;
        }
        $dlqi = isset($sd['dlqi_score']) ? (float) $sd['dlqi_score'] : null;
        if ($dlqi !== null && $dlqi > 0.00001) {
            return false;
        }
        $iga = isset($sd['iga_score']) ? (float) $sd['iga_score'] : null;
        if ($iga !== null && $iga > 0.00001) {
            return false;
        }
        if (isset($sd['pasi_data']) && is_array($sd['pasi_data'])) {
            foreach ($sd['pasi_data'] as $region) {
                if (! is_array($region)) {
                    continue;
                }
                foreach ($region as $v) {
                    if (is_numeric($v) && (float) $v > 0) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * EMR appends new summaries after "--- AI summary ---". That block often repeats wrong PASI/DLQI text.
     * For summarisation, use only the clinician narrative before the first marker (unless that would be empty).
     */
    private function historyNotesBeforeAiSummaryMarker(string $history): string
    {
        $h = str_replace("\r\n", "\n", $history);
        $pos = stripos($h, "\n--- AI summary ---");
        if ($pos !== false) {
            return trim(substr($h, 0, $pos));
        }
        if (stripos(ltrim($h), '--- AI summary ---') === 0) {
            return '';
        }

        return trim($h);
    }

    public function generateConsultationSummary(Visit $visit, string $language = 'en'): string
    {
        $hasScalesTable = Schema::hasTable('visit_scales');
        $hasLesionsTable = Schema::hasTable('visit_lesions');
        if ($hasScalesTable) {
            $visit->loadMissing('scales');
        }
        if ($hasLesionsTable) {
            $visit->loadMissing('lesions');
        }

        $rawHistory = (string) ($visit->history ?? '');
        $historyPrimary = $this->historyNotesBeforeAiSummaryMarker($rawHistory);
        if (strlen(trim($historyPrimary)) < 5 && stripos($rawHistory, '--- AI summary ---') === false) {
            $historyPrimary = trim($rawHistory);
        }
        $history = $historyPrimary;
        if (strlen($history) > 12000) {
            $history = substr($history, 0, 12000).'…';
        }
        $historyTrimLen = strlen(trim((string) $history));
        if (strlen($rawHistory) > strlen($historyPrimary)) {
            Log::info('ClaudeService: history_notes truncated before --- AI summary --- for fresh summary', [
                'visit_id' => $visit->id,
                'raw_len' => strlen($rawHistory),
                'primary_len' => strlen($historyPrimary),
            ]);
        }

        $rawStructured = $visit->structured_data ?? [];
        $rawStructured = is_array($rawStructured) ? $rawStructured : [];
        [$structuredArr, $strippedStructuredScaleDefaults] = $this->stripDefaultDermatologyScaleFieldsFromStructuredData($rawStructured, $historyTrimLen);
        $structured = null;
        if (is_array($structuredArr) && $structuredArr !== []) {
            $structured = json_encode($structuredArr, JSON_UNESCAPED_UNICODE);
            if (is_string($structured) && strlen($structured) > 8000) {
                $structured = substr($structured, 0, 8000).'…';
            }
        }

        $scalesLines = [];
        if ($hasScalesTable && $visit->scales->isNotEmpty()) {
            foreach ($visit->scales as $s) {
                $line = ($s->scale_name ?? 'Scale').': score '.($s->score ?? '—');
                if (! empty($s->interpretation)) {
                    $line .= ' ('.$s->interpretation.')';
                }
                $scalesLines[] = $line;
            }
        }
        $scalesExcerpt = $scalesLines !== [] ? implode("\n", $scalesLines) : null;

        [$suppressDbScales, $suppressReason] = $this->shouldSuppressDbScalesForSummary($visit, $historyTrimLen, $hasScalesTable);
        if ($suppressDbScales) {
            $scalesExcerpt = null;
            Log::info('ClaudeService: clinical_scales omitted for summary', [
                'visit_id' => $visit->id,
                'reason' => $suppressReason,
            ]);
        }

        $lesionLines = [];
        if ($hasLesionsTable && $visit->lesions->isNotEmpty()) {
            foreach ($visit->lesions->take(24) as $les) {
                $parts = array_filter([
                    $les->body_region,
                    $les->lesion_type,
                    $les->size_cm !== null ? $les->size_cm.' cm' : null,
                    $les->colour,
                    $les->notes,
                ]);
                if ($parts !== []) {
                    $lesionLines[] = implode(' · ', $parts);
                }
            }
        }
        $lesionsExcerpt = $lesionLines !== [] ? implode("\n", $lesionLines) : null;

        $ctx = [
            'patient' => $visit->patient?->name,
            'specialty' => $visit->specialty,
            'chief_complaint' => $visit->chief_complaint,
            'history_notes' => $history,
            'structured_data_excerpt' => $structured,
            'clinical_scales' => $scalesExcerpt,
            'lesion_notes' => $lesionsExcerpt,
            'diagnosis' => $visit->diagnosis_text,
            'plan' => $visit->plan,
        ];
        $notes = [];
        if ($suppressDbScales) {
            $notes[] = 'visit_scales rows were all zero and were omitted; they often reflect UI defaults.';
        }
        if ($strippedStructuredScaleDefaults) {
            $notes[] = 'Default-zero PASI/DLQI/IGA fields were removed from structured_data_excerpt so they are not mistaken for a real exam — rely on history_notes for the clinical story.';
        }
        if ($notes !== []) {
            $ctx['summary_note'] = implode(' ', $notes);
        }

        $histLen = is_string($history) ? strlen($history) : 0;
        Log::info('ClaudeService: generateConsultationSummary context', [
            'visit_id' => $visit->id,
            'history_notes_len' => $histLen,
            'has_structured' => $structured !== null,
            'scales_count' => $hasScalesTable ? $visit->scales->count() : 0,
            'lesions_count' => $hasLesionsTable ? $visit->lesions->count() : 0,
            'suppressed_db_scales' => $suppressDbScales,
            'stripped_structured_scale_defaults' => $strippedStructuredScaleDefaults,
        ]);

        $system = 'You are a clinical assistant. Write a short, patient-friendly summary of the visit in plain language (2–6 sentences). '
            .'Output plain text only: no markdown, no headings (#), no bold (**), no bullet lists unless the source clearly uses them. '
            .'Use only information present in the context. '
            .'Priority order: (1) history_notes — the clinician’s free text is the primary story (any text after a line "--- AI summary ---" was removed from context as a prior draft — do not repeat it); (2) structured_data_excerpt — remaining scores and form fields; (3) diagnosis and plan; (4) lesion_notes. '
            .'If PASI/DLQI/IGA are not present in the context, do not mention them or invent scores. '
            .'Do not invent or default to “PASI 0 / DLQI 0 / clear skin / minimal disease” unless that is explicitly written in history_notes. '
            .'If history_notes, structured_data_excerpt, clinical_scales, lesion_notes, diagnosis, or plan contain substantive clinical detail, summarize that clearly — do not ask for more documentation. '
            .'Only say documentation is insufficient when there is no meaningful clinical content. No JSON.';
        $user = 'Language: '.$language."\nContext:\n".json_encode($ctx, JSON_UNESCAPED_UNICODE);

        $res = $this->callAnthropicMessages($system, $user, 1024, $visit->clinic);

        if ($res['ok'] && $res['text'] !== '') {
            return $res['text'];
        }

        if (! empty($res['error'])) {
            return "AI summary could not be generated.\n\n".$res['error'].
                "\n\nTip: In Settings → AI, set Anthropic model to a current API id (e.g. claude-haiku-4-5). Older ids such as claude-3-5-haiku-20241022 may be rejected by the API.";
        }

        return 'Summary could not be generated. Configure Anthropic in Settings or ANTHROPIC_API_KEY.';
    }

    public function suggestPrescription(
        string $diagnosis,
        string $specialty,
        ?int $patientAge,
        ?string $patientSex,
        array $allergies,
        array $currentMeds,
        ?Clinic $clinic = null,
    ): array {
        $system = 'You are a clinical decision support assistant for Indian outpatient practice. '
            .'Suggest a conservative prescription draft for the doctor to review. '
            .'Reply with ONLY valid JSON: { "drugs": [ { "name": "", "dose": "", "duration": "", "notes": "" } ], "warnings": [] }';
        $user = json_encode([
            'diagnosis' => $diagnosis,
            'specialty' => $specialty,
            'patient_age' => $patientAge,
            'patient_sex' => $patientSex,
            'allergies' => $allergies,
            'current_meds' => $currentMeds,
        ], JSON_UNESCAPED_UNICODE);

        $res = $this->callAnthropicMessages($system, $user, 4096, $clinic);
        $raw = $res['ok'] ? $res['text'] : '';
        if ($raw === '') {
            return ['drugs' => [], 'warnings' => ['AI not configured or API error: '.($res['error'] ?? 'unknown')]];
        }

        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        return ['drugs' => [], 'warnings' => ['Could not parse AI response'], 'raw' => $raw];
    }
}
