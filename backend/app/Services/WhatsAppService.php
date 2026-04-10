<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Invoice;
use App\Models\LabOrder;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\Visit;
use App\Models\WhatsappMessage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

/**
 * WhatsApp Cloud API service for ClinicOS.
 *
 * Supports: template messages, free-form text, documents, images,
 * plus purpose-built convenience methods for every automated trigger
 * (appointment confirmation, reminders, prescriptions, lab results,
 * follow-ups, payments, birthdays).
 *
 * Requires the following config keys in config/services.php → 'whatsapp':
 *   phone_number_id, token, api_version (default v19.0), verify_token, app_secret
 */
class WhatsAppService
{
    private string $phoneNumberId;
    private string $accessToken;
    private string $apiVersion;
    private string $baseUrl;

    public function __construct()
    {
        // Load from system_settings (super admin configured) first, then fall back to .env
        try {
            $this->phoneNumberId = \Illuminate\Support\Facades\DB::table('system_settings')
                ->where('key', 'whatsapp_phone_number_id')->value('value')
                ?: config('services.whatsapp.phone_number_id', '');

            $this->accessToken = \Illuminate\Support\Facades\DB::table('system_settings')
                ->where('key', 'whatsapp_access_token')->value('value')
                ?: config('services.whatsapp.token', '');
        } catch (\Throwable $e) {
            // DB might not be available (e.g. during migrations)
            $this->phoneNumberId = config('services.whatsapp.phone_number_id', '');
            $this->accessToken   = config('services.whatsapp.token', '');
        }

        $this->apiVersion = config('services.whatsapp.api_version', 'v19.0');
        $this->baseUrl    = "https://graph.facebook.com/{$this->apiVersion}";
    }

    /**
     * Webhook verify token: Super Admin → system_settings, then .env.
     */
    public static function resolveVerifyToken(): string
    {
        try {
            $v = \Illuminate\Support\Facades\DB::table('system_settings')
                ->where('key', 'whatsapp_verify_token')
                ->value('value');
            if ($v !== null && $v !== '') {
                return (string) $v;
            }
        } catch (\Throwable $e) {
            Log::debug('WhatsAppService.resolveVerifyToken: '.$e->getMessage());
        }

        return (string) config('services.whatsapp.verify_token', 'clinicos_verify');
    }

    /**
     * App secret for webhook HMAC: Super Admin → system_settings, then .env.
     */
    public static function resolveAppSecret(): string
    {
        try {
            $v = \Illuminate\Support\Facades\DB::table('system_settings')
                ->where('key', 'whatsapp_app_secret')
                ->value('value');
            if ($v !== null && $v !== '') {
                return (string) $v;
            }
        } catch (\Throwable $e) {
            Log::debug('WhatsAppService.resolveAppSecret: '.$e->getMessage());
        }

        return (string) config('services.whatsapp.app_secret', '');
    }

    /**
     * Meta-approved utility template name with one body variable {{1}} (full message text).
     * system_settings `whatsapp_utility_text_template` overrides .env.
     */
    public function resolveUtilityTextTemplateName(): ?string
    {
        try {
            $v = DB::table('system_settings')
                ->where('key', 'whatsapp_utility_text_template')
                ->value('value');
            if (is_string($v) && trim($v) !== '') {
                Log::info('WhatsAppService.resolveUtilityTextTemplateName: from system_settings', ['template' => $v]);

                return trim($v);
            }
        } catch (\Throwable $e) {
            Log::debug('WhatsAppService.resolveUtilityTextTemplateName: '.$e->getMessage());
        }

        $env = config('services.whatsapp.utility_text_template');

        return is_string($env) && trim($env) !== '' ? trim($env) : null;
    }

    private function resolveUtilityTextTemplateLanguage(): string
    {
        try {
            $v = DB::table('system_settings')
                ->where('key', 'whatsapp_utility_text_template_language')
                ->value('value');
            if (is_string($v) && trim($v) !== '') {
                return trim($v);
            }
        } catch (\Throwable $e) {
            Log::debug('WhatsAppService.resolveUtilityTextTemplateLanguage: '.$e->getMessage());
        }

        return (string) config('services.whatsapp.utility_text_template_language', 'en');
    }

    /**
     * Plain text first; if Meta rejects (outside 24h session), retry with utility template when configured.
     */
    public function sendSessionTextWithFallback(string $phone, string $message, ?string $templateFallback = null, ?array $templateComponents = null): array
    {
        Log::info('WhatsAppService::sendSessionTextWithFallback', [
            'template' => $templateFallback,
            'len' => strlen($message),
        ]);

        $response = $this->sendText($phone, $message);

        if (! empty($response['success'])) {
            return array_merge($response, [
                'transport' => 'text',
            ]);
        }

        // Check if we should retry with a template
        if (! $this->shouldRetryTextWithTemplate($response)) {
            Log::info('WhatsAppService::sendSessionTextWithFallback: not using template retry', [
                'code' => $response['code'] ?? null,
            ]);

            return array_merge($response, ['transport' => 'none']);
        }

        // Use specific fallback template if provided, else use the generic utility template
        $tpl = $templateFallback ?: $this->resolveUtilityTextTemplateName();
        if ($tpl === null) {
            Log::warning('WhatsAppService::sendSessionTextWithFallback: text failed, no utility template configured');
            return array_merge($response, ['transport' => 'none']);
        }

        // Resolve components: use provided ones OR the message itself if using generic template
        $components = $templateComponents;
        if ($components === null) {
            $components = [mb_substr($message, 0, 1020)];
        }

        $lang = $this->resolveUtilityTextTemplateLanguage();
        $retry = $this->sendTemplate($phone, $tpl, $components, $lang);

        Log::info('WhatsAppService::sendSessionTextWithFallback retry', [
            'success' => ! empty($retry['success']),
            'template' => $tpl,
            'retry_code' => $retry['code'] ?? null,
            'retry_error' => $retry['error'] ?? null,
        ]);

        return array_merge($retry, [
            'transport' => 'template',
            'utility_template' => $tpl,
            'success' => ! empty($retry['success']),
        ]);
    }

    private function shouldRetryTextWithTemplate(array $response): bool
    {
        $code = isset($response['code']) ? (int) $response['code'] : null;
        $retryCodes = [131047, 131026, 131031, 131048];
        if ($code !== null && in_array($code, $retryCodes, true)) {
            Log::info('WhatsAppService.shouldRetryTextWithTemplate: matched error code', ['code' => $code]);

            return true;
        }

        $err = strtolower((string) ($response['error'] ?? ''));
        if ($err !== '' && (
            str_contains($err, 're-engagement')
            || str_contains($err, '24 hour')
            || str_contains($err, '24-hour')
            || str_contains($err, 'outside')
            || str_contains($err, 'session')
        )) {
            Log::info('WhatsAppService.shouldRetryTextWithTemplate: matched error text');

            return true;
        }

        return false;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Low-level send methods
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Send a pre-approved template message.
     *
     * @param  string  $phone         E.164 number (e.g. +919876543210)
     * @param  string  $templateName  Meta-approved template name
     * @param  array   $params        Ordered body parameter values
     * @param  string  $language      Template language code
     * @return array   Meta API response
     */
    public function sendTemplate(string $phone, string $templateName, array $params = [], string $language = 'en'): array
    {
        $phone = $this->formatPhone($phone);

        $payload = [
            'messaging_product' => 'whatsapp',
            'to'   => $phone,
            'type' => 'template',
            'template' => [
                'name'     => $templateName,
                'language' => ['code' => $language],
            ],
        ];

        if (!empty($params)) {
            $payload['template']['components'] = [
                [
                    'type'       => 'body',
                    'parameters' => array_map(
                        fn(string $v) => ['type' => 'text', 'text' => $v],
                        array_values($params)
                    ),
                ],
            ];
        }

        return $this->callApi("/{$this->phoneNumberId}/messages", $payload);
    }

    /**
     * Send a free-form text message (requires 24-hour customer window).
     */
    public function sendText(string $phone, string $message): array
    {
        $phone = $this->formatPhone($phone);

        return $this->callApi("/{$this->phoneNumberId}/messages", [
            'messaging_product' => 'whatsapp',
            'to'   => $phone,
            'type' => 'text',
            'text' => ['body' => $message],
        ]);
    }

    /**
     * Send a document (e.g. PDF prescription / invoice).
     */
    public function sendDocument(string $phone, string $documentUrl, string $filename, string $caption = ''): array
    {
        $phone = $this->formatPhone($phone);

        return $this->callApi("/{$this->phoneNumberId}/messages", [
            'messaging_product' => 'whatsapp',
            'to'   => $phone,
            'type' => 'document',
            'document' => [
                'link'     => $documentUrl,
                'filename' => $filename,
                'caption'  => $caption,
            ],
        ]);
    }

    /**
     * Send an image with optional caption.
     */
    public function sendImage(string $phone, string $imageUrl, string $caption = ''): array
    {
        $phone = $this->formatPhone($phone);

        return $this->callApi("/{$this->phoneNumberId}/messages", [
            'messaging_product' => 'whatsapp',
            'to'   => $phone,
            'type' => 'image',
            'image' => [
                'link'    => $imageUrl,
                'caption' => $caption,
            ],
        ]);
    }

    /**
     * Send interactive buttons (up to 3 buttons allowed by Meta).
     *
     * @param string $phone E.164 number
     * @param string $bodyText The main message body text
     * @param array $buttons Array of button definitions: [['id' => 'btn1', 'title' => 'Say Hi']]
     */
    public function sendInteractiveButtons(string $phone, string $bodyText, array $buttons): array
    {
        $phone = $this->formatPhone($phone);

        $formattedButtons = [];
        foreach (array_slice($buttons, 0, 3) as $btn) {
            $formattedButtons[] = [
                'type' => 'reply',
                'reply' => [
                    'id' => substr($btn['id'], 0, 256),
                    'title' => substr($btn['title'], 0, 20), // Meta restricts title to 20 chars
                ]
            ];
        }

        return $this->callApi("/{$this->phoneNumberId}/messages", [
            'messaging_product' => 'whatsapp',
            'to'   => $phone,
            'type' => 'interactive',
            'interactive' => [
                'type' => 'button',
                'body' => [
                    'text' => $bodyText
                ],
                'action' => [
                    'buttons' => $formattedButtons
                ]
            ]
        ]);
    }

    /**
     * Send interactive list (up to 10 rows per section).
     */
    public function sendInteractiveList(string $phone, string $bodyText, string $buttonLabel, array $rows): array
    {
        $phone = $this->formatPhone($phone);
        $sectionRows = [];
        foreach (array_slice($rows, 0, 10) as $row) {
            $sectionRows[] = [
                'id'          => substr($row['id'], 0, 200),
                'title'       => substr($row['title'], 0, 24),
                'description' => isset($row['description']) ? substr($row['description'], 0, 72) : null,
            ];
        }

        return $this->callApi("/{$this->phoneNumberId}/messages", [
            'messaging_product' => 'whatsapp',
            'to'   => $phone,
            'type' => 'interactive',
            'interactive' => [
                'type' => 'list',
                'body' => ['text' => $bodyText],
                'action' => [
                    'button' => substr($buttonLabel, 0, 20),
                    'sections' => [
                        [
                            'title' => 'Options',
                            'rows'  => $sectionRows,
                        ],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Send a Razorpay / UPI payment link.
     */
    public function sendPaymentLink(
        string $phone,
        string $patientName,
        float  $amount,
        string $invoiceNumber,
        string $paymentUrl
    ): array {
        return $this->sendTemplate($phone, 'payment_link', [
            $patientName,
            $invoiceNumber,
            number_format($amount, 2),
            $paymentUrl,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Convenience / trigger methods
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Appointment confirmation — sent immediately on booking.
     */
    public function sendAppointmentConfirmation(Patient $patient, Appointment $appointment): array
    {
        $phone = $this->formatPhone($patient->phone);
        $date  = \Carbon\Carbon::parse($appointment->scheduled_at)->format('d M Y, h:i A');
        $doctor = $appointment->doctor->name ?? 'your doctor';
        $clinicName = $appointment->clinic->name ?? 'ClinicOS';

        // Message to send
        $msg = "✅ *Appointment Confirmed*\n\nHi {$patient->name},\n\nYour appointment at *{$clinicName}* is confirmed for *{$date}* with Dr. {$doctor}.\n\nThank you!";

        // Add teleconsult link if applicable
        if ($appointment->appointment_type === 'teleconsultation' && $appointment->teleconsult_meeting_url) {
            $msg .= "\n\n🎥 *Video Consultation Link:*\n{$appointment->teleconsult_meeting_url}\n\nPlease join the meeting at the scheduled time.";
        }

        // Send using session text (which works perfectly since the user likely just interacted via chatbot)
        // It will automatically fall back if needed xbased on our unified helper.
        $response = $this->sendSessionTextWithFallback($patient->phone, $msg, 'appointment_confirmation');

        $tpl = $response['utility_template'] ?? 'appointment_confirmation';
        $mType = (($response['transport'] ?? '') === 'template') ? 'template' : 'text';

        // Persist internally
        $this->persistPlainText(
            $appointment->clinic_id,
            $patient->id,
            'appointment_confirmation',
            $msg,
            $phone,
            $response,
            $appointment->id,
            $tpl,
            $mType
        );

        return $response;
    }

    /**
     * Send pre-visit questionnaire link (free-form text; may require an open WhatsApp session).
     */
    public function sendPreVisitQuestionnaireLink(Patient $patient, Appointment $appointment, string $url): array
    {
        Log::info('WhatsAppService: sendPreVisitQuestionnaireLink', [
            'appointment_id' => $appointment->id,
            'patient_id' => $patient->id,
            'url_length' => strlen($url),
        ]);

        $phone = $this->formatPhone($patient->phone);
        $message = "Hi {$patient->name}, please complete your pre-visit questionnaire before your appointment: {$url}";

        $response = $this->sendText($phone, $message);

        $this->persistPlainText(
            $appointment->clinic_id,
            $patient->id,
            'manual',
            $message,
            $phone,
            $response,
            $appointment->id,
        );

        Log::info('WhatsAppService: sendPreVisitQuestionnaireLink result', [
            'appointment_id' => $appointment->id,
            'response_keys' => array_keys($response ?? []),
        ]);

        return $response;
    }

    /**
     * 24-hour appointment reminder.
     */
    public function sendAppointmentReminder24h(Patient $patient, Appointment $appointment): array
    {
        $phone = $this->formatPhone($patient->phone);
        $date  = Carbon::parse($appointment->scheduled_at)->format('d M Y, h:i A');
        $doctor = $appointment->doctor->name ?? 'your doctor';

        $response = $this->sendTemplate($phone, 'appointment_reminder_24h', [
            $patient->name,
            $date,
            $doctor,
        ]);

        $this->logOutbound(
            $appointment->clinic_id,
            $patient->id,
            'reminder_24h',
            'template',
            "24h reminder — {$date}",
            'appointment_reminder_24h',
            $response,
            $phone,
            $appointment->id,
        );

        return $response;
    }

    /**
     * 2-hour appointment reminder.
     */
    public function sendAppointmentReminder2h(Patient $patient, Appointment $appointment): array
    {
        $phone = $this->formatPhone($patient->phone);
        $time  = Carbon::parse($appointment->scheduled_at)->format('h:i A');
        $doctor = $appointment->doctor->name ?? 'your doctor';

        $response = $this->sendTemplate($phone, 'appointment_reminder_2h', [
            $patient->name,
            $time,
            $doctor,
        ]);

        $this->logOutbound(
            $appointment->clinic_id,
            $patient->id,
            'reminder_2h',
            'template',
            "2h reminder — {$time}",
            'appointment_reminder_2h',
            $response,
            $phone,
            $appointment->id,
        );

        return $response;
    }

    /**
     * Send prescription PDF via WhatsApp.
     */
    public function sendPrescription(Patient $patient, Prescription $prescription, string $pdfUrl): array
    {
        $phone = $this->formatPhone($patient->phone);
        $dateStr = $prescription->created_at ? $prescription->created_at->format('Y-m-d') : now()->format('Y-m-d');
        $filename = "prescription_{$dateStr}.pdf";

        $response = $this->sendDocument(
            $phone,
            $pdfUrl,
            $filename,
            "Your prescription " . ($prescription->created_at ? "dated " . $prescription->created_at->format('d M Y') : "") . ". Please take medicines as prescribed."
        );

        $this->logOutbound(
            $prescription->clinic_id,
            $patient->id,
            'prescription',
            'document',
            "Prescription PDF — {$dateStr}",
            'prescription_document',
            $response,
            $phone,
            $prescription->id,
        );

        return $response;
    }

    /**
     * Send lab results notification.
     */
    public function sendLabResults(Patient $patient, LabOrder $labOrder): array
    {
        $phone = $this->formatPhone($patient->phone);
        $clinicName = $labOrder->clinic->name ?? 'ClinicOS';
        $testNames = $labOrder->display_test_names;

        Log::info('WhatsAppService::sendLabResults internal start', [
            'order_id' => $labOrder->id,
            'patient_id' => $patient->id,
            'phone' => $phone,
            'test_names' => $testNames,
        ]);

        // Session Text Message
        $msg = "🔬 *Lab Results Ready*\n\nHi {$patient->name},\n\nYour results for *{$testNames}* at *{$clinicName}* are now available.\n\nYou can view them in the portal or collect the physical copy at the reception.\n\nThank you for choosing us!";

        // Send with fallback logic
        $response = $this->sendSessionTextWithFallback($patient->phone, $msg, 'lab_results_ready', [
            $patient->name,
            $testNames,
            now()->format('d M Y'),
        ]);

        Log::info('WhatsAppService::sendLabResults outcomes', [
            'success' => !empty($response['success']),
        ]);

        return $response;
    }

    /**
     * Notify patient upon registration. (Welcome message)
     */
    public function notifyPatientRegistered(Patient $patient): array
    {
        $phone = $this->formatPhone($patient->phone);
        $clinicName = $patient->clinic->name ?? 'ClinicOS';
        
        $msg = "Welcome to *{$clinicName}*!\n\nHi {$patient->name},\n\nYou have been successfully registered with us.\n\nUHID: *{$patient->uhid}*\nPhone: *{$patient->phone}*\n\nWe look forward to providing you with the best care!\n\nThank you.";

        $response = $this->sendSessionTextWithFallback($patient->phone, $msg, 'patient_welcome', [
            $patient->name,
            $clinicName,
            $patient->uhid ?? 'New',
        ]);

        $this->logOutbound(
            $patient->clinic_id,
            $patient->id,
            'welcome',
            'text',
            $msg,
            null,
            $response,
            $phone
        );

        return $response;
    }

    /**
     * Share lab report PDF via WhatsApp.
     */
    public function sendLabReportShare(Patient $patient, \App\Models\LabOrder $labOrder, string $pdfUrl): array
    {
        $phone = $this->formatPhone($patient->phone);
        $testNames = $labOrder->display_test_names;
        $dateStr = now('Asia/Kolkata')->format('d M Y');
        $filename = "lab_report_{$labOrder->id}.pdf";

        $caption = "Hi {$patient->name},\n\nYour lab results for *{$testNames}* are ready. Please find the attached report.\n\nDate: {$dateStr}";

        $response = $this->sendDocument($phone, $pdfUrl, $filename, $caption);

        $this->logOutbound(
            $labOrder->clinic_id,
            $patient->id,
            'lab_report',
            'document',
            "Lab Report Share — {$testNames}",
            null,
            $response,
            $phone,
            $labOrder->id
        );

        return $response;
    }
            'transport' => $response['transport'] ?? 'none',
            'success' => $response['success'] ?? false,
            'error' => $response['error'] ?? null,
        ]);

        $tpl = $response['utility_template'] ?? 'lab_results_ready';
        $mType = (($response['transport'] ?? '') === 'template') ? 'template' : 'text';

        $this->persistPlainText(
            $labOrder->clinic_id,
            $patient->id,
            'result',
            $msg,
            $phone,
            $response,
            $labOrder->id,
            $tpl,
            $mType
        );

        return $response;
    }

    /**
     * Follow-up reminder — sent on the scheduled follow-up date.
     */
    public function sendFollowUpReminder(Patient $patient, Visit $visit): array
    {
        $phone = $this->formatPhone($patient->phone);
        $visitDate = $visit->created_at?->format('d M Y') ?? '';

        $response = $this->sendTemplate($phone, 'followup_reminder', [
            $patient->name,
            $visitDate,
        ]);

        $this->logOutbound(
            $visit->clinic_id,
            $patient->id,
            'recall',
            'template',
            "Follow-up reminder — visit {$visitDate}",
            'followup_reminder',
            $response,
            $phone,
            $visit->id,
        );

        return $response;
    }

    /**
     * Payment reminder for overdue invoices.
     */
    public function sendPaymentReminder(Patient $patient, Invoice $invoice, string $paymentUrl): array
    {
        $phone       = $this->formatPhone($patient->phone);
        $outstanding = round((float) ($invoice->total ?? 0) - (float) ($invoice->paid ?? 0), 2);

        $response = $this->sendTemplate($phone, 'payment_reminder', [
            $patient->name,
            $invoice->invoice_number ?? ('INV-' . $invoice->id),
            number_format($outstanding, 2),
            $paymentUrl,
        ]);

        $this->logOutbound(
            $invoice->clinic_id,
            $patient->id,
            'payment_link',
            'template',
            'Payment reminder — '.($invoice->invoice_number ?? ''),
            'payment_reminder',
            $response,
            $phone,
            $invoice->id,
        );

        return $response;
    }

    /**
     * Birthday greeting (sent automatically via cron).
     */
    public function sendBirthdayGreeting(Patient $patient): array
    {
        $phone = $this->formatPhone($patient->phone);

        $response = $this->sendTemplate($phone, 'birthday_greeting', [
            $patient->name,
        ]);

        $this->logOutbound(
            $patient->clinic_id,
            $patient->id,
            'birthday',
            'template',
            'Birthday greeting',
            'birthday_greeting',
            $response,
            $phone,
            null,
        );

        return $response;
    }

    /**
     * New patient registration — plain text (shows in Dashboard / WhatsApp inbox).
     */
    public function notifyPatientRegistered(Patient $patient, ?Clinic $clinic = null): void
    {
        if ($patient->phone === null || trim((string) $patient->phone) === '') {
            Log::info('WhatsAppService::notifyPatientRegistered skipped (no phone)', ['patient_id' => $patient->id]);

            return;
        }

        $clinicName = $clinic?->name ?? 'our clinic';
        $msg = "Hello {$patient->name},\n\nYour profile has been registered at *{$clinicName}*. We will share visit updates and invoices on WhatsApp when applicable.\n\nThank you.";

        $phone = $this->formatPhone($patient->phone);
        $response = $this->sendSessionTextWithFallback($patient->phone, $msg, 'patient_registered');
        $tpl = $response['utility_template'] ?? null;
        $mType = (($response['transport'] ?? '') === 'template') ? 'template' : 'text';
        $this->persistPlainText(
            $patient->clinic_id,
            $patient->id,
            'manual',
            $msg,
            $phone,
            $response,
            $patient->id,
            $tpl,
            $mType,
        );

        Log::info('WhatsAppService::notifyPatientRegistered done', [
            'patient_id' => $patient->id,
            'success' => $response['success'] ?? null,
        ]);
    }

    /**
     * OPD walk-in / queue — token + time + doctor.
     */
    public function notifyOpdQueue(Patient $patient, Appointment $appointment, ?Clinic $clinic = null): void
    {
        if ($patient->phone === null || trim((string) $patient->phone) === '') {
            Log::info('WhatsAppService::notifyOpdQueue skipped (no phone)', ['patient_id' => $patient->id]);

            return;
        }

        $clinicName = $clinic?->name ?? 'Clinic';
        $token = $appointment->token_number !== null ? (string) $appointment->token_number : '—';
        $when = Carbon::parse($appointment->scheduled_at)->format('d M Y, h:i A');
        $doctor = $appointment->doctor->name ?? 'Doctor';

        $msg = "Hi {$patient->name},\n\nYou are on the OPD queue at *{$clinicName}*.\n\nToken: *{$token}*\nTime: {$when}\nDoctor: Dr. {$doctor}\n\nPlease be available near the consultation room.\n\nThank you.";

        $phone = $this->formatPhone($patient->phone);
        $response = $this->sendSessionTextWithFallback($patient->phone, $msg, 'opd_queue');
        $tpl = $response['utility_template'] ?? null;
        $mType = (($response['transport'] ?? '') === 'template') ? 'template' : 'text';
        $this->persistPlainText(
            $appointment->clinic_id,
            $patient->id,
            'manual',
            $msg,
            $phone,
            $response,
            $appointment->id,
            $tpl,
            $mType,
        );

        Log::info('WhatsAppService::notifyOpdQueue done', [
            'appointment_id' => $appointment->id,
            'success' => $response['success'] ?? null,
        ]);
    }

    /**
     * Invoice created — send PDF via WhatsApp Cloud API (document) + optional text fallback.
     * Meta requires a public HTTPS URL; we use a signed URL to billing.pdf.public.
     */
    public function notifyInvoiceCreated(Invoice $invoice): void
    {
        $invoice->loadMissing(['patient', 'clinic']);
        $patient = $invoice->patient;
        if (! $patient || $patient->phone === null || trim((string) $patient->phone) === '') {
            Log::info('WhatsAppService::notifyInvoiceCreated skipped (no patient phone)', ['invoice_id' => $invoice->id]);

            return;
        }

        $clinic = $invoice->clinic;
        $num = $invoice->invoice_number ?? ('INV-'.$invoice->id);
        $total = number_format((float) ($invoice->total ?? 0), 2);
        $phone = $this->formatPhone($patient->phone);

        $signedPdfUrl = URL::temporarySignedRoute(
            'billing.pdf.public',
            now()->addDays(7),
            ['invoice' => $invoice->id]
        );

        Log::info('WhatsAppService::notifyInvoiceCreated signed PDF URL', [
            'invoice_id' => $invoice->id,
            'url_length' => strlen($signedPdfUrl),
            'app_url' => config('app.url'),
        ]);

        $safeFile = 'invoice-'.preg_replace('/[^A-Za-z0-9_.-]/', '_', (string) $num).'.pdf';
        $caption = "Invoice {$num} — ₹{$total} — ".($clinic->name ?? 'Clinic');
        if (!empty($invoice->payment_link)) {
            $caption .= "\n\nPay Online: {$invoice->payment_link}";
        }

        $docResponse = $this->sendDocument($patient->phone, $signedPdfUrl, $safeFile, $caption);

        Log::info('WhatsAppService::notifyInvoiceCreated sendDocument', [
            'invoice_id' => $invoice->id,
            'success' => ! empty($docResponse['success']),
            'error' => $docResponse['error'] ?? null,
            'code' => $docResponse['code'] ?? null,
        ]);

        $this->logOutbound(
            $invoice->clinic_id,
            $patient->id,
            'manual',
            'document',
            'Invoice PDF: '.$caption,
            null,
            $docResponse,
            $phone,
            $invoice->id,
        );

        if (! empty($docResponse['success'])) {
            Log::info('WhatsAppService::notifyInvoiceCreated done (document sent)', ['invoice_id' => $invoice->id]);

            return;
        }

        Log::warning('WhatsAppService::notifyInvoiceCreated document failed, sending text with signed PDF link', [
            'invoice_id' => $invoice->id,
            'error' => $docResponse['error'] ?? null,
        ]);

        $msg = "Hi {$patient->name},\n\nInvoice *{$num}* for *₹{$total}* has been generated.\n\nDownload PDF: {$signedPdfUrl}\n\n— ".($clinic->name ?? 'Clinic');

        $response = $this->sendSessionTextWithFallback($patient->phone, $msg, 'invoice_created');
        $tpl = $response['utility_template'] ?? null;
        $mType = (($response['transport'] ?? '') === 'template') ? 'template' : 'text';
        $this->persistPlainText(
            $invoice->clinic_id,
            $patient->id,
            'manual',
            $msg,
            $phone,
            $response,
            $invoice->id,
            $tpl,
            $mType,
        );

        Log::info('WhatsAppService::notifyInvoiceCreated done (text fallback)', [
            'invoice_id' => $invoice->id,
            'success' => $response['success'] ?? null,
        ]);
    }

    /**
     * Invoice paid — send updated PAID PDF receipt.
     */
    public function notifyInvoicePaid(Invoice $invoice): void
    {
        $invoice->loadMissing(['patient', 'clinic']);
        $patient = $invoice->patient;
        if (! $patient || $patient->phone === null || trim((string) $patient->phone) === '') {
            return;
        }

        $clinic = $invoice->clinic;
        $num = $invoice->invoice_number ?? ('INV-'.$invoice->id);
        $total = number_format((float) ($invoice->total ?? 0), 2);
        $phone = $this->formatPhone($patient->phone);

        $signedPdfUrl = URL::temporarySignedRoute(
            'billing.pdf.public',
            now()->addDays(7),
            ['invoice' => $invoice->id]
        );

        $safeFile = 'receipt-'.preg_replace('/[^A-Za-z0-9_.-]/', '_', (string) $num).'.pdf';
        $caption = "Payment Received ✅\nInvoice {$num} — ₹{$total}\nThank you! — ".($clinic->name ?? 'Clinic');

        $docResponse = $this->sendDocument($patient->phone, $signedPdfUrl, $safeFile, $caption);

        $this->logOutbound(
            $invoice->clinic_id,
            $patient->id,
            'manual',
            'document',
            'Paid Receipt PDF: '.$caption,
            null,
            $docResponse,
            $phone,
            $invoice->id,
        );

        if (! empty($docResponse['success'])) {
            return;
        }

        $msg = "Hi {$patient->name},\n\nWe have received your payment of *₹{$total}* for Invoice *{$num}*.\n\nDownload Receipt: {$signedPdfUrl}\n\nThank you! — ".($clinic->name ?? 'Clinic');

        $response = $this->sendSessionTextWithFallback($patient->phone, $msg, 'invoice_paid');
        $this->persistPlainText(
            $invoice->clinic_id,
            $patient->id,
            'manual',
            $msg,
            $phone,
            $response,
            $invoice->id,
            $response['utility_template'] ?? null,
            (($response['transport'] ?? '') === 'template') ? 'template' : 'text',
        );
    }

    /**
     * Alias used by web UI — free-form text within the 24h customer window.
     */
    public function sendMessage(string $phone, string $message): array
    {
        Log::info('WhatsAppService::sendMessage', [
            'phone_prefix' => substr($this->formatPhone($phone), 0, 5),
            'len' => strlen($message),
        ]);

        return $this->sendSessionTextWithFallback($phone, $message, 'manual_send');
    }

    /**
     * Send teleconsult / video visit link (plain text; Meta template optional in production).
     */
    public function sendTeleconsultInvite(Patient $patient, Appointment $appointment, string $meetingUrl): array
    {
        $phone = $this->formatPhone($patient->phone);
        $when = Carbon::parse($appointment->scheduled_at)->format('d M Y, h:i A');
        $clinicName = $appointment->clinic->name ?? 'Clinic';
        $msg = "Video consultation\n\nHello {$patient->name},\n\nYour teleconsult is scheduled for {$when}.\n\nJoin: {$meetingUrl}\n\n— {$clinicName}";

        Log::info('WhatsAppService::sendTeleconsultInvite', [
            'appointment_id' => $appointment->id,
            'patient_id' => $patient->id,
        ]);

        return $this->sendText($phone, $msg);
    }

    /**
     * Generic send — used by WhatsAppController for custom template sends.
     * $components is the Meta Cloud API `components` array.
     */
    public function send(string $phone, string $templateName, array $components = []): array
    {
        $phone = $this->formatPhone($phone);

        $payload = [
            'messaging_product' => 'whatsapp',
            'to'   => $phone,
            'type' => 'template',
            'template' => [
                'name'       => $templateName,
                'language'   => ['code' => 'en'],
                'components' => $components,
            ],
        ];

        return $this->callApi("/{$this->phoneNumberId}/messages", $payload);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Internal helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Make a POST request to the Meta Cloud API.
     */
    private function callApi(string $endpoint, array $payload): array
    {
        if (empty($this->accessToken) || empty($this->phoneNumberId)) {
            Log::warning('WhatsApp credentials not configured — skipping API call', [
                'endpoint' => $endpoint,
            ]);

            return [
                'success' => false,
                'error'   => 'WhatsApp API credentials not configured',
            ];
        }

        try {
            $url = $this->baseUrl . $endpoint;

            Log::debug('WhatsApp API call', ['url' => $url, 'to' => $payload['to'] ?? null]);

            $response = Http::withToken($this->accessToken)
                ->timeout(15)
                ->post($url, $payload);

            $body = $response->json();

            if ($response->successful()) {
                Log::info('WhatsApp message sent', [
                    'to'         => $payload['to'] ?? null,
                    'message_id' => $body['messages'][0]['id'] ?? null,
                ]);

                return array_merge($body, ['success' => true]);
            }

            Log::error('WhatsApp API error', [
                'status'   => $response->status(),
                'response' => $body,
            ]);

            return [
                'success' => false,
                'error'   => $body['error']['message'] ?? 'Unknown API error',
                'code'    => $body['error']['code'] ?? null,
            ];
        } catch (\Throwable $e) {
            Log::error('WhatsApp API exception', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        }
    }

    /**
     * Persist template/document/template API responses — matches whatsapp_messages schema.
     */
    private function logOutbound(
        int $clinicId,
        int $patientId,
        string $dbTriggerType,
        string $messageType,
        ?string $bodyPreview,
        ?string $templateName,
        array $response,
        string $waPhoneTo,
        ?int $relatedId = null,
    ): void {
        $allowedTriggers = [
            'appointment_confirmation', 'reminder_24h', 'reminder_2h', 'prescription', 'payment_link',
            'recall', 'hep', 'result', 'birthday', 'manual', 'inbound_reply',
        ];
        if (! in_array($dbTriggerType, $allowedTriggers, true)) {
            $dbTriggerType = 'manual';
        }

        $ok = ! empty($response['success']);
        $status = $ok ? WhatsappMessage::STATUS_SENT : WhatsappMessage::STATUS_FAILED;
        $waId = $response['messages'][0]['id'] ?? null;
        $err = $ok ? null : (string) ($response['error'] ?? 'API error');
        if ($err !== null && strlen($err) > 300) {
            $err = substr($err, 0, 297).'...';
        }

        try {
            WhatsappMessage::create([
                'clinic_id' => $clinicId,
                'patient_id' => $patientId,
                'direction' => WhatsappMessage::DIRECTION_OUTBOUND,
                'wa_message_id' => $waId,
                'wa_phone_to' => $waPhoneTo,
                'template_name' => $templateName,
                'message_type' => in_array($messageType, ['text', 'template', 'image', 'document', 'audio'], true) ? $messageType : 'template',
                'body' => $bodyPreview ?? json_encode($response, JSON_UNESCAPED_UNICODE),
                'trigger_type' => $dbTriggerType,
                'related_id' => $relatedId,
                'status' => $status,
                'error_message' => $err,
                'sent_at' => $ok ? now() : null,
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('WhatsAppService::logOutbound failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Persist outbound plain-text sends (patient welcome, OPD, invoice notice).
     */
    private function persistPlainText(
        int $clinicId,
        int $patientId,
        string $dbTriggerType,
        string $textBody,
        string $waPhoneTo,
        array $response,
        ?int $relatedId = null,
        ?string $templateName = null,
        string $messageType = 'text',
    ): void {
        $allowedTriggers = [
            'appointment_confirmation', 'reminder_24h', 'reminder_2h', 'prescription', 'payment_link',
            'recall', 'hep', 'result', 'birthday', 'manual', 'inbound_reply',
        ];
        if (! in_array($dbTriggerType, $allowedTriggers, true)) {
            $dbTriggerType = 'manual';
        }

        $ok = ! empty($response['success']);
        $status = $ok ? WhatsappMessage::STATUS_SENT : WhatsappMessage::STATUS_FAILED;
        $waId = $response['messages'][0]['id'] ?? null;
        $err = $ok ? null : (string) ($response['error'] ?? 'API error');
        if ($err !== null && strlen($err) > 300) {
            $err = substr($err, 0, 297).'...';
        }
        $msgType = in_array($messageType, ['text', 'template', 'image', 'document', 'audio'], true) ? $messageType : 'text';

        try {
            WhatsappMessage::create([
                'clinic_id' => $clinicId,
                'patient_id' => $patientId,
                'direction' => WhatsappMessage::DIRECTION_OUTBOUND,
                'wa_message_id' => $waId,
                'wa_phone_to' => $waPhoneTo,
                'template_name' => $templateName,
                'message_type' => $msgType,
                'body' => $textBody,
                'trigger_type' => $dbTriggerType,
                'related_id' => $relatedId,
                'status' => $status,
                'error_message' => $err,
                'sent_at' => $ok ? now() : null,
                'created_at' => now(),
            ]);
            Log::info('WhatsAppService::persistPlainText stored', [
                'clinic_id' => $clinicId,
                'patient_id' => $patientId,
                'message_type' => $msgType,
                'template_name' => $templateName,
                'status' => $status,
            ]);
        } catch (\Throwable $e) {
            Log::error('WhatsAppService::persistPlainText failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Normalise phone to E.164 without the '+' for WhatsApp Cloud API.
     * Special handling for Indian numbers (10 digits, or 11 starting with 0).
     */
    public function formatPhone(string $phone): string
    {
        if (empty($phone)) return '';

        // 1. Remove all non-digits
        $digits = preg_replace('/\D/', '', $phone);

        // 2. Handle Indian numbers starting with 0 (e.g. 09876543210 -> 9876543210)
        if (strlen($digits) === 11 && str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        // 3. Handle '910...' cases where prefix 91 is followed by a redundant 0
        if (strlen($digits) === 13 && str_starts_with($digits, '910')) {
            $digits = '91' . substr($digits, 3);
        }

        // 4. If it is 10 digits, it is likely an Indian number without country code
        if (strlen($digits) === 10) {
            return '91' . $digits;
        }

        // 5. Return as is if it already has country code
        // Meta API prefers no leading '+'
        return $digits;
    }

    /**
     * Send location (GPS pin).
     */
    public function sendLocation(string $phone, float $lat, float $lng, string $name, string $address): array
    {
        $phone = $this->formatPhone($phone);

        return $this->callApi("/{$this->phoneNumberId}/messages", [
            'messaging_product' => 'whatsapp',
            'to'   => $phone,
            'type' => 'location',
            'location' => [
                'longitude' => $lng,
                'latitude'  => $lat,
                'name'      => $name,
                'address'   => $address,
            ],
        ]);
    }
}
