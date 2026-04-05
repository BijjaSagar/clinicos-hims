<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Services\AI\ClinicAiCredentialResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SettingsWebController extends Controller
{
    public function index()
    {
        Log::info('SettingsWebController@index', ['user' => auth()->id()]);

        try {
            $clinic = auth()->user()->clinic;
            $user = auth()->user();

            Log::info('SettingsWebController loaded', ['clinic_id' => $clinic?->id]);

            $resolver = app(ClinicAiCredentialResolver::class);
            $aiOpenaiConfigured = $clinic ? $resolver->hasOpenaiConfigured($clinic) : false;
            $aiAnthropicConfigured = $clinic ? $resolver->hasAnthropicConfigured($clinic) : false;

            return view('settings.index', compact('clinic', 'user', 'aiOpenaiConfigured', 'aiAnthropicConfigured'));
        } catch (\Throwable $e) {
            Log::error('SettingsWebController@index error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function updateClinic(Request $request)
    {
        Log::info('SettingsWebController@updateClinic', $request->all());

        $gstinTrim = trim((string) $request->input('gstin', ''));
        $panTrim = trim((string) $request->input('pan', ''));
        $regTrim = trim((string) $request->input('registration_number', ''));
        $request->merge([
            'gstin' => $gstinTrim === '' ? null : $gstinTrim,
            'pan' => $panTrim === '' ? null : $panTrim,
            'registration_number' => $regTrim === '' ? null : $regTrim,
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'specialty' => 'nullable|string|max:100',
            'address_line1' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'gstin' => ['nullable', 'string', 'max:15', 'regex:/^$|^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/i'],
            'pan' => ['nullable', 'string', 'max:10', 'regex:/^$|^[A-Z]{5}[0-9]{4}[A-Z]{1}$/i'],
            'registration_number' => 'nullable|string|max:120',
        ]);

        try {
            $clinic = auth()->user()->clinic;
            
            // Handle specialties as array
            $updateData = $validated;
            if (isset($validated['specialty'])) {
                $updateData['specialties'] = [$validated['specialty']];
                unset($updateData['specialty']);
            }

            if (!empty($updateData['gstin'])) {
                $updateData['gstin'] = strtoupper((string) $updateData['gstin']);
            }
            if (!empty($updateData['pan'])) {
                $updateData['pan'] = strtoupper((string) $updateData['pan']);
            }
            
            $clinic->update($updateData);

            Log::info('Clinic settings updated', [
                'clinic_id' => $clinic->id,
                'gstin_set' => !empty($clinic->gstin),
                'pan_set' => !empty($clinic->pan),
                'registration_set' => !empty($clinic->registration_number),
            ]);

            return back()->with('success', 'Clinic settings updated successfully');
        } catch (\Throwable $e) {
            Log::error('updateClinic error', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to update settings: ' . $e->getMessage());
        }
    }

    public function updateBilling(Request $request)
    {
        Log::info('SettingsWebController@updateBilling', $request->except(['invoice_logo']));

        $validated = $request->validate([
            'invoice_prefix' => 'required|string|max:10',
            'default_gst_rate' => 'required|numeric|min:0|max:100',
            'payment_terms' => 'nullable|string|max:500',
            'invoice_letterhead' => 'nullable|string|max:4000',
            'invoice_footer' => 'nullable|string|max:2000',
            'invoice_tagline' => 'nullable|string|max:255',
            'default_invoice_format' => 'required|in:gst,bill',
            'invoice_logo' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:3072',
            'remove_invoice_logo' => 'nullable|boolean',
        ]);

        try {
            $clinic = auth()->user()->clinic;
            $settings = array_merge($clinic->settings ?? [], [
                'invoice_prefix' => $validated['invoice_prefix'],
                'default_gst_rate' => $validated['default_gst_rate'],
                'payment_terms' => $validated['payment_terms'] ?? null,
                'invoice_letterhead' => $validated['invoice_letterhead'] ?? null,
                'invoice_footer' => $validated['invoice_footer'] ?? null,
                'invoice_tagline' => $validated['invoice_tagline'] ?? null,
                'default_invoice_format' => $validated['default_invoice_format'],
            ]);

            if ($request->boolean('remove_invoice_logo')) {
                $old = $settings['invoice_logo_path'] ?? null;
                if ($old && Storage::disk('public')->exists($old)) {
                    Storage::disk('public')->delete($old);
                    Log::info('SettingsWebController@updateBilling removed invoice logo', ['path' => $old]);
                }
                unset($settings['invoice_logo_path']);
            }

            if ($request->hasFile('invoice_logo')) {
                $file = $request->file('invoice_logo');
                $dir = 'clinics/'.$clinic->id.'/branding';
                Storage::disk('public')->makeDirectory($dir);
                $old = $settings['invoice_logo_path'] ?? null;
                if ($old && Storage::disk('public')->exists($old)) {
                    Storage::disk('public')->delete($old);
                }
                $path = $file->store($dir, 'public');
                $settings['invoice_logo_path'] = $path;
                Log::info('SettingsWebController@updateBilling saved invoice logo', ['path' => $path]);
            }

            $clinic->update(['settings' => $settings]);

            Log::info('Billing settings updated', ['clinic_id' => $clinic->id]);

            return back()->with('success', 'Billing & invoice print settings updated successfully');
        } catch (\Throwable $e) {
            Log::error('updateBilling error', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to update billing settings: ' . $e->getMessage());
        }
    }

    public function updateAiCredentials(Request $request)
    {
        Log::info('SettingsWebController@updateAiCredentials', [
            'user' => auth()->id(),
            'has_openai_input' => $request->filled('openai_api_key'),
            'has_anthropic_input' => $request->filled('anthropic_api_key'),
        ]);

        $validated = $request->validate([
            'openai_api_key' => 'nullable|string|max:512',
            'anthropic_api_key' => 'nullable|string|max:512',
            'clear_openai_key' => 'nullable|boolean',
            'clear_anthropic_key' => 'nullable|boolean',
            'ai_openai_chat_model' => 'nullable|string|max:80',
            'ai_openai_fast_model' => 'nullable|string|max:80',
            'ai_anthropic_model' => 'nullable|string|max:80',
        ]);

        try {
            $clinic = auth()->user()->clinic;
            $settings = array_merge($clinic->settings ?? [], []);

            if ($request->boolean('clear_openai_key')) {
                unset($settings['ai_openai_key_enc']);
                Log::info('SettingsWebController@updateAiCredentials cleared OpenAI key', ['clinic_id' => $clinic->id]);
            } elseif (!empty(trim((string) ($validated['openai_api_key'] ?? '')))) {
                $plain = trim((string) $validated['openai_api_key']);
                $settings['ai_openai_key_enc'] = Crypt::encryptString($plain);
                Log::info('SettingsWebController@updateAiCredentials stored OpenAI key', ['clinic_id' => $clinic->id]);
            }

            if ($request->boolean('clear_anthropic_key')) {
                unset($settings['ai_anthropic_key_enc']);
                Log::info('SettingsWebController@updateAiCredentials cleared Anthropic key', ['clinic_id' => $clinic->id]);
            } elseif (!empty(trim((string) ($validated['anthropic_api_key'] ?? '')))) {
                $plain = trim((string) $validated['anthropic_api_key']);
                $settings['ai_anthropic_key_enc'] = Crypt::encryptString($plain);
                Log::info('SettingsWebController@updateAiCredentials stored Anthropic key', ['clinic_id' => $clinic->id]);
            }

            foreach (['ai_openai_chat_model', 'ai_openai_fast_model', 'ai_anthropic_model'] as $k) {
                $v = isset($validated[$k]) ? trim((string) $validated[$k]) : '';
                if ($v !== '') {
                    $settings[$k] = $v;
                } else {
                    unset($settings[$k]);
                }
            }

            $clinic->update(['settings' => $settings]);

            Log::info('SettingsWebController@updateAiCredentials saved', ['clinic_id' => $clinic->id]);

            return back()->with('success', 'AI API settings saved. Keys are stored encrypted.');
        } catch (\Throwable $e) {
            Log::error('SettingsWebController@updateAiCredentials error', ['error' => $e->getMessage()]);

            return back()->with('error', 'Failed to save AI settings: '.$e->getMessage());
        }
    }
}
