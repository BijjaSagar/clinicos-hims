<?php

namespace App\Services\AI;

use App\Models\Clinic;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

/**
 * Resolves OpenAI / Anthropic API keys.
 *
 * OpenAI default: .env OPENAI_API_KEY first (when non-empty), then encrypted clinic Settings key.
 * Set OPENAI_CLINIC_KEY_OVERRIDES_ENV=true to use clinic key first (enterprise multi-tenant).
 *
 * Renamed from AiCredentialResolver to avoid rare production collisions (duplicate class load / merged uploads).
 */
class ClinicAiCredentialResolver
{
    /**
     * @return array{key: ?string, source: 'env'|'clinic'|'none'}
     */
    public function openaiKeyResolution(?Clinic $clinic = null): array
    {
        $clinic = $clinic ?? Auth::user()?->clinic;
        $envKey = $this->trimSecret((string) config('services.openai.api_key', ''));
        $clinicKey = $clinic ? $this->decryptClinicOpenaiKey($clinic) : null;

        $clinicFirst = (bool) config('services.openai.clinic_key_overrides_env', false);
        // Legacy: OPENAI_PREFER_ENV_KEY=true ? prefer .env over clinic (same as clinic_first=false)
        if (config('services.openai.prefer_env_key', false)) {
            $clinicFirst = false;
        }

        if ($clinicFirst) {
            if ($clinicKey !== null) {
                Log::info('ClinicAiCredentialResolver: OpenAI key source = clinic (OPENAI_CLINIC_KEY_OVERRIDES_ENV=true)', [
                    'clinic_id' => $clinic?->id,
                ]);

                return ['key' => $clinicKey, 'source' => 'clinic'];
            }
            if ($envKey !== null) {
                Log::info('ClinicAiCredentialResolver: OpenAI key source = env (fallback, clinic empty)', ['clinic_id' => $clinic?->id]);

                return ['key' => $envKey, 'source' => 'env'];
            }

            return ['key' => null, 'source' => 'none'];
        }

        if ($envKey !== null) {
            Log::info('ClinicAiCredentialResolver: OpenAI key source = env (default precedence)', ['clinic_id' => $clinic?->id]);

            return ['key' => $envKey, 'source' => 'env'];
        }
        if ($clinicKey !== null) {
            Log::info('ClinicAiCredentialResolver: OpenAI key source = clinic (no OPENAI_API_KEY in .env)', [
                'clinic_id' => $clinic?->id,
            ]);

            return ['key' => $clinicKey, 'source' => 'clinic'];
        }

        return ['key' => null, 'source' => 'none'];
    }

    public function openaiApiKey(?Clinic $clinic = null): ?string
    {
        return $this->openaiKeyResolution($clinic)['key'];
    }

    private function decryptClinicOpenaiKey(Clinic $clinic): ?string
    {
        $enc = data_get($clinic->settings, 'ai_openai_key_enc');
        if (! is_string($enc) || $enc === '') {
            return null;
        }
        try {
            $plain = $this->trimSecret(Crypt::decryptString($enc));

            return $plain;
        } catch (\Throwable $e) {
            Log::warning('ClinicAiCredentialResolver: OpenAI decrypt failed', [
                'clinic_id' => $clinic->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Trim whitespace / newlines — pasted keys in Settings or .env often pick up trailing spaces ? OpenAI 401.
     */
    private function trimSecret(string $value): ?string
    {
        $t = trim($value, " \t\n\r\0\x0B\"'");

        return $t === '' ? null : $t;
    }

    public function anthropicApiKey(?Clinic $clinic = null): ?string
    {
        $clinic = $clinic ?? Auth::user()?->clinic;
        if ($clinic) {
            $enc = data_get($clinic->settings, 'ai_anthropic_key_enc');
            if (is_string($enc) && $enc !== '') {
                try {
                    $plain = $this->trimSecret(Crypt::decryptString($enc));
                    if ($plain !== null) {
                        Log::info('ClinicAiCredentialResolver: using clinic Anthropic key', ['clinic_id' => $clinic->id]);

                        return $plain;
                    }
                } catch (\Throwable $e) {
                    Log::warning('ClinicAiCredentialResolver: Anthropic decrypt failed', [
                        'clinic_id' => $clinic->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        $env = $this->trimSecret((string) config('services.anthropic.api_key', ''));
        if ($env !== null) {
            Log::debug('ClinicAiCredentialResolver: using env ANTHROPIC_API_KEY fallback');

            return $env;
        }

        return null;
    }

    public function openaiChatModel(?Clinic $clinic = null): string
    {
        $clinic = $clinic ?? Auth::user()?->clinic;
        $s = data_get($clinic?->settings, 'ai_openai_chat_model');

        return is_string($s) && trim($s) !== '' ? trim($s) : 'gpt-4o';
    }

    public function openaiFastModel(?Clinic $clinic = null): string
    {
        $clinic = $clinic ?? Auth::user()?->clinic;
        $s = data_get($clinic?->settings, 'ai_openai_fast_model');

        return is_string($s) && trim($s) !== '' ? trim($s) : 'gpt-4o-mini';
    }

    public function anthropicModel(?Clinic $clinic = null): string
    {
        $clinic = $clinic ?? Auth::user()?->clinic;
        $s = data_get($clinic?->settings, 'ai_anthropic_model');
        $raw = is_string($s) && trim($s) !== '' ? trim($s) : 'claude-haiku-4-5';

        // Anthropic retires snapshot ids; map common old Settings values to current API ids (see docs.anthropic.com models).
        $legacy = [
            'claude-3-5-haiku-20241022' => 'claude-haiku-4-5',
            'claude-3-5-sonnet-20241022' => 'claude-sonnet-4-6',
            'claude-3-5-haiku-20240620' => 'claude-haiku-4-5',
            'claude-3-haiku-20240307' => 'claude-haiku-4-5',
        ];
        if (isset($legacy[$raw])) {
            Log::notice('ClinicAiCredentialResolver: mapping deprecated Anthropic model id', ['from' => $raw, 'to' => $legacy[$raw]]);

            return $legacy[$raw];
        }

        return $raw;
    }

    public function hasOpenaiConfigured(?Clinic $clinic = null): bool
    {
        return $this->openaiApiKey($clinic) !== null;
    }

    public function hasAnthropicConfigured(?Clinic $clinic = null): bool
    {
        return $this->anthropicApiKey($clinic) !== null;
    }
}
