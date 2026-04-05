<?php

namespace App\Http\Controllers\WhatsApp;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Visit;
use App\Models\Invoice;
use App\Models\WhatsappMessage;
use App\Services\WhatsAppService;
use App\Jobs\SendBulkWhatsAppJob;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class WhatsAppController extends Controller
{
    public function __construct(
        private readonly WhatsAppService $whatsAppService,
    ) {}

    // -------------------------------------------------------------------------
    // Public endpoints (webhooks)
    // -------------------------------------------------------------------------

    /**
     * POST /whatsapp/webhook (inbound messages from Meta)
     */
    public function inbound(Request $request): mixed
    {
        Log::info('WhatsAppController.inbound: Webhook POST received');
        return $this->inboundWebhook($request);
    }

    /**
     * GET /whatsapp/webhook (Meta challenge verification)
     *
     * Meta sends hub.mode, hub.verify_token, hub.challenge. PHP often maps dots to underscores
     * in $_GET; some stacks keep dotted keys — read both. Token must match WHATSAPP_VERIFY_TOKEN
     * or system_settings whatsapp_verify_token (defaults in config/services.php).
     */
    public function verify(Request $request): mixed
    {
        $p = self::metaWebhookVerifyParams($request);
        $mode = $p['mode'];
        $token = $p['token'];
        $challenge = $p['challenge'];

        Log::info('WhatsAppController.verify: Meta challenge', [
            'query_keys' => array_keys($request->query->all()),
            'hub_mode' => $mode,
            'has_verify_token' => $token !== null && $token !== '',
            'has_challenge' => $challenge !== null && $challenge !== '',
        ]);

        $expected = WhatsAppService::resolveVerifyToken();
        if ($expected === '') {
            Log::warning('WhatsAppController.verify: WHATSAPP_VERIFY_TOKEN / whatsapp_verify_token is empty — set in .env or Super Admin');
        }

        if ($mode === 'subscribe'
            && $challenge !== null && $challenge !== ''
            && $token !== null
            && hash_equals((string) $expected, (string) $token)) {
            Log::info('WhatsAppController.verify: Challenge accepted');

            return response((string) $challenge, 200)->header('Content-Type', 'text/plain; charset=utf-8');
        }

        Log::warning('WhatsAppController.verify: Challenge rejected', [
            'mode_ok' => $mode === 'subscribe',
            'expected_token_configured' => $expected !== '',
            'hint' => 'Use the same string in Meta and WHATSAPP_VERIFY_TOKEN (or system_settings whatsapp_verify_token). Default env default is clinicos_webhook_verify unless overridden.',
        ]);

        return response('Forbidden', 403);
    }

    // -------------------------------------------------------------------------
    // Authenticated endpoints
    // -------------------------------------------------------------------------

    /**
     * GET /whatsapp/messages
     * Paginated message log for the clinic with optional search/filter.
     * Query params: patient_id, type, status, search, per_page
     */
    public function messages(Request $request): JsonResponse
    {
        Log::info('WhatsAppController.messages: Fetching message log');
        return $this->index($request);
    }

    /**
     * POST /whatsapp/template
     * Send a WhatsApp template message (appointment/rx/payment).
     */
    public function sendTemplate(Request $request): JsonResponse
    {
        Log::info('WhatsAppController.sendTemplate: Sending template message');
        return $this->send($request);
    }

    /**
     * GET /whatsapp/threads/{patientId}
     * Chat thread for a specific patient.
     */
    public function thread(int $patientId): JsonResponse
    {
        Log::info('WhatsAppController.thread: Fetching thread', ['patient_id' => $patientId]);
        $clinicId = auth()->user()->clinic_id;

        $messages = WhatsappMessage::where('clinic_id', $clinicId)
            ->where('patient_id', $patientId)
            ->orderBy('created_at', 'asc')
            ->limit(100)
            ->get();

        $patient = Patient::where('clinic_id', $clinicId)->find($patientId);

        Log::info('WhatsAppController.thread: Thread fetched', ['patient_id' => $patientId, 'count' => $messages->count()]);

        return response()->json([
            'data'    => $messages,
            'message' => 'Chat thread retrieved',
            'meta'    => [
                'patient' => $patient ? ['id' => $patient->id, 'name' => $patient->name, 'phone' => $patient->phone] : null,
                'count'   => $messages->count(),
            ],
        ]);
    }

    /**
     * GET /whatsapp/messages (original)
     */
    public function index(Request $request): JsonResponse
    {
        $clinicId = auth()->user()->clinic_id;

        $request->validate([
            'patient_id' => 'nullable|integer|exists:patients,id',
            'type'       => 'nullable|string|in:appointment_reminder,prescription,payment_link,receipt,recall,hep,custom,inbound',
            'status'     => 'nullable|string|in:queued,sent,delivered,read,failed',
            'search'     => 'nullable|string|max:100',
            'per_page'   => 'nullable|integer|min:5|max:100',
        ]);

        $query = WhatsappMessage::with(['patient:id,name,phone'])
            ->where('clinic_id', $clinicId)
            ->when($request->patient_id, fn($q, $pid) => $q->where('patient_id', $pid))
            ->when($request->type, fn($q, $t) => $q->where('message_type', $t))
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->search, function ($q, $search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('to_number', 'like', "%{$search}%")
                          ->orWhereHas('patient', fn($p) => $p->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest();

        $perPage   = (int) ($request->per_page ?? 20);
        $paginated = $query->paginate($perPage);

        return response()->json([
            'data'    => $paginated->items(),
            'message' => 'Message log retrieved successfully.',
            'meta'    => [
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
                'per_page'     => $paginated->perPage(),
                'total'        => $paginated->total(),
            ],
        ]);
    }

    /**
     * POST /whatsapp/send
     * Send a single WhatsApp message using a named template.
     */
    public function send(Request $request): JsonResponse
    {
        $clinicId = auth()->user()->clinic_id;

        $validated = $request->validate([
            'patient_id'   => 'required|integer|exists:patients,id',
            'template'     => 'required|string|max:100',
            'variables'    => 'nullable|array',
            'variables.*'  => 'nullable|string',
            'text'         => 'nullable|string|max:4096', // for free-text fallback
        ]);

        $patient = Patient::where('clinic_id', $clinicId)->findOrFail($validated['patient_id']);

        if (!$patient->phone) {
            return response()->json([
                'data'    => null,
                'message' => 'Patient does not have a phone number on record.',
                'meta'    => [],
            ], 422);
        }

        $to = $this->formatPhoneNumber($patient->phone);

        try {
            if ($validated['template'] === 'text' && isset($validated['text'])) {
                $apiResponse = $this->whatsAppService->sendText($to, $validated['text']);
            } else {
                $components  = $this->buildTemplatePayload($validated['template'], $validated['variables'] ?? []);
                $apiResponse = $this->whatsAppService->send($to, $validated['template'], $components);
            }

            $message = $this->logMessage([
                'clinic_id'    => $clinicId,
                'patient_id'   => $patient->id,
                'to_number'    => $to,
                'message_type' => 'custom',
                'template'     => $validated['template'],
                'status'       => 'sent',
                'wa_message_id'=> $apiResponse['messages'][0]['id'] ?? null,
                'payload'      => json_encode($apiResponse),
            ]);

            return response()->json([
                'data'    => $message,
                'message' => 'WhatsApp message sent successfully.',
                'meta'    => [],
            ], 201);
        } catch (\Throwable $e) {
            Log::error('WhatsAppController@send failed', ['error' => $e->getMessage()]);
            return response()->json([
                'data'    => null,
                'message' => 'Failed to send WhatsApp message: ' . $e->getMessage(),
                'meta'    => [],
            ], 500);
        }
    }

    /**
     * POST /whatsapp/send-bulk
     * Batch send to multiple patients (e.g. recall reminders). Jobs are queued.
     */
    public function sendBulk(Request $request): JsonResponse
    {
        $clinicId = auth()->user()->clinic_id;

        $validated = $request->validate([
            'patient_ids'  => 'required|array|min:1|max:500',
            'patient_ids.*'=> 'required|integer|exists:patients,id',
            'template'     => 'required|string|max:100',
            'variables'    => 'nullable|array',
            'variables.*'  => 'nullable|string',
        ]);

        // Verify all patients belong to clinic
        $patients = Patient::where('clinic_id', $clinicId)
            ->whereIn('id', $validated['patient_ids'])
            ->whereNotNull('phone')
            ->get();

        if ($patients->isEmpty()) {
            return response()->json([
                'data'    => null,
                'message' => 'No valid patients with phone numbers found.',
                'meta'    => [],
            ], 422);
        }

        // Dispatch queued jobs per patient
        foreach ($patients as $patient) {
            SendBulkWhatsAppJob::dispatch(
                $clinicId,
                $patient->id,
                $this->formatPhoneNumber($patient->phone),
                $validated['template'],
                $validated['variables'] ?? []
            );
        }

        return response()->json([
            'data'    => ['queued' => $patients->count(), 'skipped' => count($validated['patient_ids']) - $patients->count()],
            'message' => sprintf('%d messages queued for sending.', $patients->count()),
            'meta'    => [],
        ], 202);
    }

    /**
     * POST /whatsapp/appointment-reminder/{appointmentId}
     * Send a formatted appointment reminder to the patient.
     */
    public function sendAppointmentReminder(int $appointmentId): JsonResponse
    {
        $clinicId = auth()->user()->clinic_id;

        $appointment = Appointment::with(['patient', 'doctor'])
            ->where('clinic_id', $clinicId)
            ->findOrFail($appointmentId);

        $patient = $appointment->patient;
        if (!$patient || !$patient->phone) {
            return response()->json([
                'data'    => null,
                'message' => 'Patient phone number not available.',
                'meta'    => [],
            ], 422);
        }

        $to          = $this->formatPhoneNumber($patient->phone);
        $appointDate = Carbon::parse($appointment->scheduled_at)->format('d M Y, h:i A');
        $doctorName  = $appointment->doctor->name ?? 'the doctor';

        try {
            $apiResponse = $this->whatsAppService->send($to, 'appointment_reminder', [
                ['type' => 'body', 'parameters' => [
                    ['type' => 'text', 'text' => $patient->name],
                    ['type' => 'text', 'text' => $appointDate],
                    ['type' => 'text', 'text' => $doctorName],
                    ['type' => 'text', 'text' => $appointment->clinic->name ?? 'our clinic'],
                ]],
            ]);

            $message = $this->logMessage([
                'clinic_id'      => $clinicId,
                'patient_id'     => $patient->id,
                'to_number'      => $to,
                'message_type'   => 'appointment_reminder',
                'template'       => 'appointment_reminder',
                'reference_id'   => $appointmentId,
                'reference_type' => 'appointment',
                'status'         => 'sent',
                'wa_message_id'  => $apiResponse['messages'][0]['id'] ?? null,
                'payload'        => json_encode($apiResponse),
            ]);

            return response()->json([
                'data'    => $message,
                'message' => 'Appointment reminder sent successfully.',
                'meta'    => [],
            ]);
        } catch (\Throwable $e) {
            Log::error('WhatsAppController@sendAppointmentReminder failed', ['error' => $e->getMessage()]);
            return response()->json([
                'data'    => null,
                'message' => 'Failed to send reminder: ' . $e->getMessage(),
                'meta'    => [],
            ], 500);
        }
    }

    /**
     * POST /whatsapp/prescription/{visitId}
     * Send prescription summary and PDF download link via WhatsApp.
     */
    public function sendPrescription(int $visitId): JsonResponse
    {
        $clinicId = auth()->user()->clinic_id;

        $visit = Visit::with(['patient', 'prescriptions'])
            ->where('clinic_id', $clinicId)
            ->findOrFail($visitId);

        $patient = $visit->patient;
        if (!$patient || !$patient->phone) {
            return response()->json([
                'data'    => null,
                'message' => 'Patient phone number not available.',
                'meta'    => [],
            ], 422);
        }

        $to         = $this->formatPhoneNumber($patient->phone);
        $pdfUrl     = route('prescription.pdf', ['visit' => $visitId]);
        $visitDate  = Carbon::parse($visit->visit_date)->format('d M Y');

        try {
            // Send document (PDF link)
            $apiResponse = $this->whatsAppService->send($to, 'prescription_ready', [
                ['type' => 'header', 'parameters' => [
                    ['type' => 'document', 'document' => [
                        'link'     => $pdfUrl,
                        'filename' => 'prescription_' . $visitDate . '.pdf',
                    ]],
                ]],
                ['type' => 'body', 'parameters' => [
                    ['type' => 'text', 'text' => $patient->name],
                    ['type' => 'text', 'text' => $visitDate],
                ]],
            ]);

            $message = $this->logMessage([
                'clinic_id'      => $clinicId,
                'patient_id'     => $patient->id,
                'to_number'      => $to,
                'message_type'   => 'prescription',
                'template'       => 'prescription_ready',
                'reference_id'   => $visitId,
                'reference_type' => 'visit',
                'status'         => 'sent',
                'wa_message_id'  => $apiResponse['messages'][0]['id'] ?? null,
                'payload'        => json_encode($apiResponse),
            ]);

            return response()->json([
                'data'    => $message,
                'message' => 'Prescription sent via WhatsApp successfully.',
                'meta'    => [],
            ]);
        } catch (\Throwable $e) {
            Log::error('WhatsAppController@sendPrescription failed', ['error' => $e->getMessage()]);
            return response()->json([
                'data'    => null,
                'message' => 'Failed to send prescription: ' . $e->getMessage(),
                'meta'    => [],
            ], 500);
        }
    }

    /**
     * POST /whatsapp/payment-link/{invoiceId}
     * Send Razorpay payment link for a specific invoice.
     */
    public function sendPaymentLink(int $invoiceId): JsonResponse
    {
        $clinicId = auth()->user()->clinic_id;

        $invoice = Invoice::with('patient')
            ->where('clinic_id', $clinicId)
            ->findOrFail($invoiceId);

        $patient = $invoice->patient;
        if (!$patient || !$patient->phone) {
            return response()->json([
                'data'    => null,
                'message' => 'Patient phone number not available.',
                'meta'    => [],
            ], 422);
        }

        if (!in_array($invoice->payment_status, ['pending', 'partial'])) {
            return response()->json([
                'data'    => null,
                'message' => 'Payment link can only be sent for finalized or partially paid invoices.',
                'meta'    => [],
            ], 422);
        }

        $to          = $this->formatPhoneNumber($patient->phone);
        $outstanding = round($invoice->grand_total - $invoice->amount_paid, 2);
        $paymentLink = route('payment.link', ['invoice' => $invoiceId]);

        try {
            $apiResponse = $this->whatsAppService->send($to, 'payment_link', [
                ['type' => 'body', 'parameters' => [
                    ['type' => 'text', 'text' => $patient->name],
                    ['type' => 'text', 'text' => $invoice->invoice_number ?? ('INV-' . $invoiceId)],
                    ['type' => 'text', 'text' => number_format($outstanding, 2)],
                    ['type' => 'text', 'text' => $paymentLink],
                ]],
            ]);

            $message = $this->logMessage([
                'clinic_id'      => $clinicId,
                'patient_id'     => $patient->id,
                'to_number'      => $to,
                'message_type'   => 'payment_link',
                'template'       => 'payment_link',
                'reference_id'   => $invoiceId,
                'reference_type' => 'invoice',
                'status'         => 'sent',
                'wa_message_id'  => $apiResponse['messages'][0]['id'] ?? null,
                'payload'        => json_encode($apiResponse),
            ]);

            return response()->json([
                'data'    => $message,
                'message' => 'Payment link sent via WhatsApp.',
                'meta'    => [],
            ]);
        } catch (\Throwable $e) {
            Log::error('WhatsAppController@sendPaymentLink failed', ['error' => $e->getMessage()]);
            return response()->json([
                'data'    => null,
                'message' => 'Failed to send payment link: ' . $e->getMessage(),
                'meta'    => [],
            ], 500);
        }
    }

    /**
     * POST /whatsapp/hep/{planId}
     * Send Home Exercise Programme (physiotherapy) to the patient.
     */
    public function sendHep(int $planId): JsonResponse
    {
        $clinicId = auth()->user()->clinic_id;

        // Assumes a HepPlan model — adjust relation as needed
        $plan = \App\Models\HepPlan::with('patient')
            ->where('clinic_id', $clinicId)
            ->findOrFail($planId);

        $patient = $plan->patient;
        if (!$patient || !$patient->phone) {
            return response()->json([
                'data'    => null,
                'message' => 'Patient phone number not available.',
                'meta'    => [],
            ], 422);
        }

        $to      = $this->formatPhoneNumber($patient->phone);
        $pdfUrl  = route('hep.pdf', ['plan' => $planId]);

        try {
            $apiResponse = $this->whatsAppService->sendDocument(
                $to,
                $pdfUrl,
                'home_exercise_programme.pdf'
            );

            $message = $this->logMessage([
                'clinic_id'      => $clinicId,
                'patient_id'     => $patient->id,
                'to_number'      => $to,
                'message_type'   => 'hep',
                'template'       => 'document',
                'reference_id'   => $planId,
                'reference_type' => 'hep_plan',
                'status'         => 'sent',
                'wa_message_id'  => $apiResponse['messages'][0]['id'] ?? null,
                'payload'        => json_encode($apiResponse),
            ]);

            return response()->json([
                'data'    => $message,
                'message' => 'Home Exercise Programme sent via WhatsApp.',
                'meta'    => [],
            ]);
        } catch (\Throwable $e) {
            Log::error('WhatsAppController@sendHep failed', ['error' => $e->getMessage()]);
            return response()->json([
                'data'    => null,
                'message' => 'Failed to send HEP: ' . $e->getMessage(),
                'meta'    => [],
            ], 500);
        }
    }

    /**
     * GET|POST /whatsapp/webhook/inbound
     * Handle Meta Cloud API inbound messages.
     * GET:  verify hub challenge (Meta webhook verification).
     * POST: receive messages and status updates.
     */
    public function inboundWebhook(Request $request): mixed
    {
        // --- Webhook verification (GET) — same as verify(); kept for direct inboundWebhook GET if routed ---
        if ($request->isMethod('GET')) {
            $p = self::metaWebhookVerifyParams($request);
            $expected = WhatsAppService::resolveVerifyToken();
            if ($p['mode'] === 'subscribe'
                && $p['challenge'] !== null && $p['challenge'] !== ''
                && $p['token'] !== null
                && hash_equals((string) $expected, (string) $p['token'])) {
                return response((string) $p['challenge'], 200)->header('Content-Type', 'text/plain; charset=utf-8');
            }

            return response('Forbidden', 403);
        }

        // --- Incoming message (POST) ---
        if (!$this->verifyMetaSignature($request)) {
            Log::warning('WhatsApp inbound webhook: invalid signature');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $payload = $request->json()->all();

        try {
            foreach ($payload['entry'] ?? [] as $entry) {
                foreach ($entry['changes'] ?? [] as $change) {
                    $value = $change['value'] ?? [];

                    // Process inbound messages
                    foreach ($value['messages'] ?? [] as $msg) {
                        $from      = $msg['from'] ?? null;
                        $waId      = $msg['id'] ?? null;
                        $timestamp = $msg['timestamp'] ?? null;
                        $body      = $msg['text']['body'] ?? $msg['type'] ?? '';

                        // Find patient by phone number
                        $phone   = preg_replace('/^91/', '', ltrim($from, '+'));
                        $patient = Patient::where('phone', 'like', '%' . $phone)->first();

                        $this->logMessage([
                            'clinic_id'      => $patient->clinic_id ?? null,
                            'patient_id'     => $patient->id ?? null,
                            'to_number'      => '+' . $from,
                            'message_type'   => 'inbound',
                            'template'       => null,
                            'status'         => 'received',
                            'wa_message_id'  => $waId,
                            'payload'        => json_encode($msg),
                            'body'           => $body,
                            'direction'      => 'inbound',
                            'received_at'    => $timestamp ? Carbon::createFromTimestamp($timestamp)->toDateTimeString() : now()->toDateTimeString(),
                        ]);

                        // Auto-reply on keyword match
                        $this->handleAutoReply($from, strtolower(trim($body)), $patient);
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error('WhatsApp inbound webhook processing error', ['error' => $e->getMessage()]);
        }

        // Always return 200 to Meta
        return response()->json(['status' => 'ok']);
    }

    /**
     * POST /whatsapp/webhook/status
     * Handle delivery/read status updates from Meta Cloud API.
     */
    public function statusWebhook(Request $request): JsonResponse
    {
        if (!$this->verifyMetaSignature($request)) {
            Log::warning('WhatsApp status webhook: invalid signature');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $payload = $request->json()->all();

        try {
            foreach ($payload['entry'] ?? [] as $entry) {
                foreach ($entry['changes'] ?? [] as $change) {
                    foreach ($change['value']['statuses'] ?? [] as $statusUpdate) {
                        $waId      = $statusUpdate['id'] ?? null;
                        $newStatus = $statusUpdate['status'] ?? null; // sent|delivered|read|failed

                        if ($waId && $newStatus) {
                            WhatsappMessage::where('wa_message_id', $waId)
                                ->update([
                                    'status'     => $newStatus,
                                    'updated_at' => now(),
                                ]);
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error('WhatsApp status webhook processing error', ['error' => $e->getMessage()]);
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * GET /whatsapp/templates
     * List available approved WhatsApp templates from Meta.
     */
    public function templates(): JsonResponse
    {
        $phoneId      = config('services.whatsapp.phone_id');
        $businessId   = config('services.whatsapp.business_id');
        $token        = config('services.whatsapp.token');

        try {
            $response = \Illuminate\Support\Facades\Http::withToken($token)
                ->get("https://graph.facebook.com/v19.0/{$businessId}/message_templates", [
                    'fields' => 'name,status,language,components',
                    'limit'  => 100,
                ]);

            if ($response->failed()) {
                throw new \RuntimeException('Meta API error: ' . $response->body());
            }

            $templates = collect($response->json('data', []))
                ->where('status', 'APPROVED')
                ->values();

            return response()->json([
                'data'    => $templates,
                'message' => 'Templates retrieved successfully.',
                'meta'    => ['total' => $templates->count()],
            ]);
        } catch (\Throwable $e) {
            Log::error('WhatsAppController@templates failed', ['error' => $e->getMessage()]);
            return response()->json([
                'data'    => null,
                'message' => 'Failed to fetch templates: ' . $e->getMessage(),
                'meta'    => [],
            ], 500);
        }
    }

    /**
     * GET /whatsapp/stats
     * Message statistics: sent/delivered/read/failed counts by day for last 30 days.
     */
    public function stats(): JsonResponse
    {
        $clinicId = auth()->user()->clinic_id;

        $from = Carbon::now()->subDays(29)->startOfDay();

        $daily = WhatsappMessage::select([
                DB::raw('DATE(created_at) as date'),
                DB::raw("SUM(CASE WHEN status = 'sent'      THEN 1 ELSE 0 END) as sent"),
                DB::raw("SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered"),
                DB::raw("SUM(CASE WHEN status = 'read'      THEN 1 ELSE 0 END) as `read`"),
                DB::raw("SUM(CASE WHEN status = 'failed'    THEN 1 ELSE 0 END) as failed"),
                DB::raw('COUNT(*) as total'),
            ])
            ->where('clinic_id', $clinicId)
            ->where('created_at', '>=', $from)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        $totals = WhatsappMessage::select([
                DB::raw("SUM(CASE WHEN status = 'sent'      THEN 1 ELSE 0 END) as sent"),
                DB::raw("SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered"),
                DB::raw("SUM(CASE WHEN status = 'read'      THEN 1 ELSE 0 END) as `read`"),
                DB::raw("SUM(CASE WHEN status = 'failed'    THEN 1 ELSE 0 END) as failed"),
                DB::raw('COUNT(*) as total'),
            ])
            ->where('clinic_id', $clinicId)
            ->where('created_at', '>=', $from)
            ->first();

        return response()->json([
            'data'    => $daily,
            'message' => 'Message statistics retrieved successfully.',
            'meta'    => [
                'period' => ['from' => $from->toDateString(), 'to' => now()->toDateString()],
                'totals' => $totals,
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Meta sends hub.mode, hub.verify_token, hub.challenge — PHP/Symfony may expose
     * keys as hub_mode / hub_verify_token / hub_challenge or with dots.
     *
     * @return array{mode: ?string, token: ?string, challenge: ?string}
     */
    private static function metaWebhookVerifyParams(Request $request): array
    {
        $q = $request->query->all();

        return [
            'mode' => $q['hub_mode'] ?? $q['hub.mode'] ?? null,
            'token' => $q['hub_verify_token'] ?? $q['hub.verify_token'] ?? null,
            'challenge' => $q['hub_challenge'] ?? $q['hub.challenge'] ?? null,
        ];
    }

    /**
     * Verify Meta Cloud API webhook signature using HMAC SHA256 with APP_SECRET.
     */
    private function verifyMetaSignature(Request $request): bool
    {
        $signature = $request->header('X-Hub-Signature-256');
        if (!$signature) {
            return false;
        }

        $appSecret = WhatsAppService::resolveAppSecret();
        if ($appSecret === '') {
            Log::warning('WhatsApp webhook: app secret empty — set in Super Admin or WHATSAPP_APP_SECRET');

            return false;
        }
        $expected  = 'sha256=' . hash_hmac('sha256', $request->getContent(), $appSecret);

        return hash_equals($expected, $signature);
    }

    /**
     * Ensure E.164 +91 prefix for Indian mobile numbers.
     */
    private function formatPhoneNumber(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (strlen($digits) === 10) {
            return '+91' . $digits;
        }

        if (strlen($digits) === 12 && str_starts_with($digits, '91')) {
            return '+' . $digits;
        }

        return '+' . ltrim($digits, '+');
    }

    /**
     * Build Meta Cloud API template payload for the given template name and variables.
     *
     * @param  string  $template  Template name
     * @param  array   $vars      Ordered list of variable values
     * @return array              components array for Meta API
     */
    private function buildTemplatePayload(string $template, array $vars): array
    {
        $parameters = array_map(
            fn(string $v) => ['type' => 'text', 'text' => $v],
            array_values($vars)
        );

        return [
            [
                'type'       => 'body',
                'parameters' => $parameters,
            ],
        ];
    }

    /**
     * Persist a WhatsApp message record to whatsapp_messages table.
     *
     * @param  array  $data  Column data
     * @return WhatsappMessage
     */
    private function logMessage(array $data): WhatsappMessage
    {
        return WhatsappMessage::create(array_merge([
            'direction'  => 'outbound',
            'status'     => 'queued',
            'created_at' => now(),
            'updated_at' => now(),
        ], $data));
    }

    /**
     * Handle keyword-based auto-reply for inbound messages.
     */
    private function handleAutoReply(string $from, string $body, ?Patient $patient): void
    {
        $autoReplies = [
            'hi'          => 'Hello! Thank you for contacting us. Our team will get back to you shortly.',
            'hello'       => 'Hello! Thank you for contacting us. Our team will get back to you shortly.',
            'appointment' => 'To book an appointment, please call our front desk or visit our website.',
            'timing'      => 'Our clinic timings are Mon–Sat, 9 AM – 7 PM.',
            'hours'       => 'Our clinic timings are Mon–Sat, 9 AM – 7 PM.',
            'stop'        => 'You have been unsubscribed from WhatsApp notifications.',
        ];

        foreach ($autoReplies as $keyword => $reply) {
            if (str_contains($body, $keyword)) {
                try {
                    $this->whatsAppService->sendText('+' . $from, $reply);

                    if ($keyword === 'stop' && $patient) {
                        $patient->update(['whatsapp_opted_in' => false]);
                    }
                } catch (\Throwable $e) {
                    Log::warning('Auto-reply failed', ['from' => $from, 'error' => $e->getMessage()]);
                }
                break;
            }
        }
    }
}
