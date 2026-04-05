<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppSettingsController extends Controller
{
    public function index()
    {
        $clinic = auth()->user()->clinic;

        $globalApiConfigured = false;
        try {
            $pid = DB::table('system_settings')->where('key', 'whatsapp_phone_number_id')->value('value');
            $tok = DB::table('system_settings')->where('key', 'whatsapp_access_token')->value('value');
            $globalApiConfigured = filled($pid) && filled($tok);
        } catch (\Throwable $e) {
            Log::warning('WhatsAppSettingsController@index system_settings check failed', ['error' => $e->getMessage()]);
        }

        $webhookUrl = url('/api/v1/whatsapp/webhook');
        $verifyToken = WhatsAppService::resolveVerifyToken();

        Log::info('WhatsAppSettingsController@index', [
            'clinic_id' => $clinic->id,
            'globalApiConfigured' => $globalApiConfigured,
        ]);

        // Get existing templates
        $templates = DB::table('whatsapp_templates')
            ->where('clinic_id', $clinic->id)
            ->orderBy('type')
            ->get();

        // Get reminder configs
        $reminders = DB::table('whatsapp_reminders')
            ->where('clinic_id', $clinic->id)
            ->get()
            ->keyBy('type');

        return view('whatsapp.settings', compact('clinic', 'globalApiConfigured', 'webhookUrl', 'verifyToken', 'templates', 'reminders'));
    }

    public function saveCredentials(Request $request)
    {
        Log::info('WhatsAppSettingsController@saveCredentials blocked — use Super Admin', ['user_id' => auth()->id()]);

        return back()->with(
            'error',
            'WhatsApp Business API credentials are configured once for the whole platform in the Super Admin portal: System → WhatsApp (Global).'
        );
    }

    public function testConnection(Request $request)
    {
        $phoneNumberId = DB::table('system_settings')->where('key', 'whatsapp_phone_number_id')->value('value');
        $accessToken = DB::table('system_settings')->where('key', 'whatsapp_access_token')->value('value');

        if (empty($phoneNumberId) || empty($accessToken)) {
            return response()->json([
                'success' => false,
                'message' => 'Platform WhatsApp is not configured yet. Ask your administrator to set credentials in Super Admin → WhatsApp.',
            ]);
        }

        try {
            $response = Http::withToken($accessToken)
                ->get("https://graph.facebook.com/v19.0/{$phoneNumberId}");

            if ($response->successful()) {
                $data = $response->json();
                Log::info('WhatsAppSettingsController@testConnection ok', ['phone' => $data['display_phone_number'] ?? null]);

                return response()->json([
                    'success' => true,
                    'message' => 'Platform OK — Phone: '.($data['display_phone_number'] ?? 'N/A').', Quality: '.($data['quality_rating'] ?? 'N/A'),
                    'data'    => $data,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Connection failed: '.($response->json('error.message') ?? 'Unknown error'),
            ]);
        } catch (\Throwable $e) {
            Log::error('WhatsAppSettingsController@testConnection', ['error' => $e->getMessage()]);

            return response()->json(['success' => false, 'message' => 'Error: '.$e->getMessage()]);
        }
    }

    public function seedTemplates()
    {
        $clinicId = auth()->user()->clinic_id;

        $templates = [
            [
                'name' => 'appointment_confirmation',
                'type' => 'appointment_reminder',
                'content' => "Hi {{patient_name}}, your appointment is confirmed!\n\n📅 Date: {{date}}\n⏰ Time: {{time}}\n👨‍⚕️ Doctor: Dr. {{doctor_name}}\n🏥 {{clinic_name}}\n\nPlease arrive 10 minutes early. Reply CANCEL to cancel.",
                'variables' => json_encode(['patient_name', 'date', 'time', 'doctor_name', 'clinic_name']),
            ],
            [
                'name' => 'appointment_reminder_24h',
                'type' => 'appointment_reminder',
                'content' => "Reminder: You have an appointment tomorrow!\n\n📅 {{date}} at {{time}}\n👨‍⚕️ Dr. {{doctor_name}}\n🏥 {{clinic_name}}\n\nSee you there!",
                'variables' => json_encode(['patient_name', 'date', 'time', 'doctor_name', 'clinic_name']),
            ],
            [
                'name' => 'appointment_reminder_2h',
                'type' => 'appointment_reminder',
                'content' => "Hi {{patient_name}}, your appointment with Dr. {{doctor_name}} is in 2 hours ({{time}}). See you soon! — {{clinic_name}}",
                'variables' => json_encode(['patient_name', 'doctor_name', 'time', 'clinic_name']),
            ],
            [
                'name' => 'prescription_ready',
                'type' => 'prescription',
                'content' => "Hi {{patient_name}}, your prescription from Dr. {{doctor_name}} is ready. You can view it in your patient portal or collect it from {{clinic_name}}.\n\nGet well soon! 🙏",
                'variables' => json_encode(['patient_name', 'doctor_name', 'clinic_name']),
            ],
            [
                'name' => 'lab_results_ready',
                'type' => 'custom',
                'content' => "Dear {{patient_name}}, your lab results for order #{{order_number}} are ready. Please visit {{clinic_name}} to collect your report or contact your doctor for details.",
                'variables' => json_encode(['patient_name', 'order_number', 'clinic_name']),
            ],
            [
                'name' => 'follow_up_reminder',
                'type' => 'follow_up',
                'content' => "Hi {{patient_name}}, this is a reminder for your follow-up visit with Dr. {{doctor_name}} at {{clinic_name}}.\n\n📅 Scheduled: {{date}}\n\nPlease book your appointment if you haven't already.",
                'variables' => json_encode(['patient_name', 'doctor_name', 'clinic_name', 'date']),
            ],
            [
                'name' => 'payment_reminder',
                'type' => 'custom',
                'content' => "Hi {{patient_name}}, you have a pending payment of ₹{{amount}} for Invoice #{{invoice_number}} at {{clinic_name}}.\n\nPay online: {{payment_link}}\n\nPlease ignore if already paid.",
                'variables' => json_encode(['patient_name', 'amount', 'invoice_number', 'clinic_name', 'payment_link']),
            ],
            [
                'name' => 'birthday_greeting',
                'type' => 'birthday',
                'content' => "🎂 Happy Birthday, {{patient_name}}! Wishing you good health and happiness from all of us at {{clinic_name}}.\n\nTake care of yourself! 🙏",
                'variables' => json_encode(['patient_name', 'clinic_name']),
            ],
            [
                'name' => 'ipd_admission_notify',
                'type' => 'custom',
                'content' => "Dear {{relative_name}}, {{patient_name}} has been admitted to {{clinic_name}}.\n\n🏥 Ward: {{ward}}\n🛏 Bed: {{bed}}\n👨‍⚕️ Doctor: Dr. {{doctor_name}}\n📅 Date: {{date}}\n\nVisiting hours: 10 AM - 12 PM, 4 PM - 6 PM",
                'variables' => json_encode(['relative_name', 'patient_name', 'clinic_name', 'ward', 'bed', 'doctor_name', 'date']),
            ],
            [
                'name' => 'discharge_summary',
                'type' => 'custom',
                'content' => "Dear {{patient_name}}, you have been discharged from {{clinic_name}}.\n\n📋 Discharge Summary has been shared with you.\n💊 Please follow the prescribed medications.\n📅 Follow-up: {{followup_date}}\n\nWishing you a speedy recovery!",
                'variables' => json_encode(['patient_name', 'clinic_name', 'followup_date']),
            ],
            [
                'name' => 'pharmacy_dispensing',
                'type' => 'custom',
                'content' => "Hi {{patient_name}}, your medicines have been dispensed from {{clinic_name}} Pharmacy.\n\n💊 Items: {{item_count}} medicines\n💰 Total: ₹{{total}}\n\nPlease follow the dosage instructions carefully.",
                'variables' => json_encode(['patient_name', 'clinic_name', 'item_count', 'total']),
            ],
        ];

        $seeded = 0;
        foreach ($templates as $t) {
            $exists = DB::table('whatsapp_templates')
                ->where('clinic_id', $clinicId)
                ->where('name', $t['name'])
                ->exists();

            if (!$exists) {
                DB::table('whatsapp_templates')->insert(array_merge($t, [
                    'clinic_id' => $clinicId,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
                $seeded++;
            }
        }

        return back()->with('success', "{$seeded} templates seeded successfully!");
    }

    public function toggleReminder(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:appointment_before_1d,appointment_before_1h,follow_up,birthday,payment_reminder,lab_results,discharge_summary',
            'is_active' => 'required|boolean',
        ]);

        $clinicId = auth()->user()->clinic_id;

        DB::table('whatsapp_reminders')->updateOrInsert(
            ['clinic_id' => $clinicId, 'type' => $validated['type']],
            ['is_active' => $validated['is_active'], 'updated_at' => now()]
        );

        return response()->json(['success' => true]);
    }
}
