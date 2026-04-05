<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\IpdAdmission;
use App\Models\Payment;
use App\Models\Patient;
use App\Models\Visit;
use App\Services\WhatsAppService;
use App\Support\InvoicePdfPresenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Barryvdh\DomPDF\Facade\Pdf;

class BillingWebController extends Controller
{
    public function index(Request $request)
    {
        Log::info('BillingWebController@index', ['user' => auth()->id()]);

        try {
            $clinicId = auth()->user()->clinic_id;
            
            $query = Invoice::with(['patient', 'items', 'payments'])
                ->where('clinic_id', $clinicId)
                ->latest();

            if ($request->filled('status')) {
                $query->where('payment_status', $request->status);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('patient', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            if ($request->filled('patient_id')) {
                $query->where('patient_id', $request->integer('patient_id'));
                Log::info('BillingWebController@index filter patient_id', ['patient_id' => $request->integer('patient_id')]);
            }

            if ($request->filled('admission_id') && Schema::hasColumn('invoices', 'admission_id')) {
                $query->where('admission_id', $request->integer('admission_id'));
                Log::info('BillingWebController@index filter admission_id', ['admission_id' => $request->integer('admission_id')]);
            }

            $invoices = $query->paginate(20);

            // Use correct column names from Invoice model
            $stats = [
                'total_today' => Invoice::where('clinic_id', $clinicId)
                    ->whereDate('created_at', today())
                    ->sum('total') ?? 0,
                'pending' => Invoice::where('clinic_id', $clinicId)
                    ->where('payment_status', 'pending')
                    ->selectRaw('SUM(total - paid) as balance')
                    ->value('balance') ?? 0,
                'collected_month' => Payment::whereHas('invoice', function ($q) use ($clinicId) {
                    $q->where('clinic_id', $clinicId);
                })->whereMonth('payment_date', now()->month)->sum('amount') ?? 0,
            ];

            Log::info('BillingWebController@index success', ['invoices_count' => $invoices->count()]);

            return view('billing.index', compact('invoices', 'stats'));
        } catch (\Throwable $e) {
            Log::error('BillingWebController@index error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            throw $e;
        }
    }

    public function create(Request $request)
    {
        $clinicId = auth()->user()->clinic_id;
        Log::info('BillingWebController@create', [
            'patient_id' => $request->input('patient_id'),
            'visit_id' => $request->input('visit_id'),
            'admission_id' => $request->input('admission_id'),
        ]);

        $patients = Patient::where('clinic_id', $clinicId)
            ->orderBy('name')
            ->get();

        $invoiceFormDefaults = [
            'items' => [
                ['description' => '', 'sac_code' => '998314', 'amount' => 0],
            ],
        ];

        if ($request->filled('admission_id') && Schema::hasTable('ipd_admissions')) {
            $adm = IpdAdmission::where('clinic_id', $clinicId)->find($request->integer('admission_id'));
            if ($adm) {
                $invoiceFormDefaults['items'] = [[
                    'description' => 'IPD — Admission '.$adm->admission_number.' (ward/bed & services)',
                    'sac_code' => '998314',
                    'amount' => 0,
                ]];
            }
        } elseif ($request->filled('visit_id') && Schema::hasTable('visits')) {
            $visit = Visit::where('clinic_id', $clinicId)->find($request->integer('visit_id'));
            if ($visit) {
                $invoiceFormDefaults['items'] = [[
                    'description' => 'OPD consultation — Visit #'.$visit->id,
                    'sac_code' => '999311',
                    'amount' => 0,
                ]];
            }
        }

        Log::info('BillingWebController@create ready', [
            'patients_count' => $patients->count(),
            'default_item_count' => count($invoiceFormDefaults['items'] ?? []),
        ]);

        return view('billing.create', compact('patients', 'invoiceFormDefaults'));
    }

    public function store(Request $request, WhatsAppService $whatsApp)
    {
        Log::info('BillingWebController@store - Raw request', [
            'all' => $request->all(),
            'visit_id_raw' => $request->input('visit_id'),
            'visit_id_filled' => $request->filled('visit_id'),
        ]);

        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'visit_id' => 'nullable|integer|exists:visits,id',
            'admission_id' => 'nullable|integer',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'nullable|numeric|min:1',
            'items.*.amount' => 'required|numeric|min:0',
            'items.*.sac_code' => 'nullable|string',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $clinicId = auth()->user()->clinic_id;
            $clinic = auth()->user()->clinic;

            $visitId = ! empty($validated['visit_id']) ? (int) $validated['visit_id'] : null;
            if ($visitId !== null) {
                $visit = Visit::where('clinic_id', $clinicId)->where('id', $visitId)->first();
                if (! $visit || (int) $visit->patient_id !== (int) $validated['patient_id']) {
                    Log::warning('BillingWebController@store visit/patient mismatch', ['visit_id' => $visitId]);

                    return back()->withInput()->with('error', 'Selected visit does not match this patient.');
                }
            }

            $admissionId = null;
            if (! empty($validated['admission_id']) && Schema::hasColumn('invoices', 'admission_id')) {
                $admissionId = (int) $validated['admission_id'];
                $adm = IpdAdmission::where('clinic_id', $clinicId)->whereKey($admissionId)->first();
                if (! $adm) {
                    return back()->withInput()->with('error', 'Invalid admission for this clinic.');
                }
                if ((int) $adm->patient_id !== (int) $validated['patient_id']) {
                    Log::warning('BillingWebController@store admission/patient mismatch', ['admission_id' => $admissionId]);

                    return back()->withInput()->with('error', 'Selected IPD admission does not match this patient.');
                }
            }
            
            // Calculate totals
            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $qty = $item['quantity'] ?? 1;
                $subtotal += $item['amount'] * $qty;
            }
            
            $discountAmount = $validated['discount_amount'] ?? 0;
            $taxableAmount = $subtotal - $discountAmount;
            
            // Get GST rate from clinic settings (default 18%)
            $gstRate = $clinic->settings['default_gst_rate'] ?? 18;
            $cgstRate = $gstRate / 2;
            $sgstRate = $gstRate / 2;
            
            $cgstAmount = round($taxableAmount * ($cgstRate / 100), 2);
            $sgstAmount = round($taxableAmount * ($sgstRate / 100), 2);
            $total = $taxableAmount + $cgstAmount + $sgstAmount;

            Log::info('Creating invoice', [
                'visit_id' => $visitId,
                'admission_id' => $admissionId,
            ]);

            $invoicePayload = [
                'clinic_id' => $clinicId,
                'patient_id' => $validated['patient_id'],
                'visit_id' => $visitId,
                'invoice_date' => now(),
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'cgst_amount' => $cgstAmount,
                'sgst_amount' => $sgstAmount,
                'total' => $total,
                'paid' => 0,
                'payment_status' => 'pending',
                'notes' => $validated['notes'] ?? null,
            ];
            if ($admissionId !== null) {
                $invoicePayload['admission_id'] = $admissionId;
            }
            $invoicePayload = array_intersect_key($invoicePayload, array_flip(Schema::getColumnListing('invoices')));
            $invoice = Invoice::create($invoicePayload);

            // Create invoice items
            foreach ($validated['items'] as $index => $item) {
                $qty = $item['quantity'] ?? 1;
                $itemAmount = $item['amount'] * $qty;
                $itemCgst = round($itemAmount * ($cgstRate / 100), 2);
                $itemSgst = round($itemAmount * ($sgstRate / 100), 2);
                $itemTotal = $itemAmount + $itemCgst + $itemSgst;
                
                \App\Models\InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $item['description'],
                    'item_type' => 'service',
                    'sac_code' => $item['sac_code'] ?? '999312', // Default healthcare SAC
                    'gst_rate' => $gstRate,
                    'unit_price' => $item['amount'],
                    'quantity' => $qty,
                    'taxable_amount' => $itemAmount,
                    'cgst_amount' => $itemCgst,
                    'sgst_amount' => $itemSgst,
                    'total' => $itemTotal,
                    'sort_order' => $index,
                ]);
            }

            Log::info('Invoice created', ['invoice_id' => $invoice->id, 'total' => $total]);

            try {
                $invoice->load(['patient', 'clinic']);
                $whatsApp->notifyInvoiceCreated($invoice);
            } catch (\Throwable $e) {
                Log::warning('BillingWebController@store: invoice WhatsApp failed', [
                    'invoice_id' => $invoice->id,
                    'error' => $e->getMessage(),
                ]);
            }

            return redirect()->route('billing.show', $invoice)
                ->with('success', 'Invoice created successfully');
                
        } catch (\Throwable $e) {
            Log::error('Invoice creation failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->withInput()->with('error', 'Failed to create invoice: ' . $e->getMessage());
        }
    }

    public function show(Invoice $invoice)
    {
        Log::info('BillingWebController@show', ['invoice' => $invoice->id]);
        $this->ensureInvoiceBelongsToClinic($invoice);

        $invoice->load(['patient', 'items', 'payments', 'clinic', 'visit', 'admission']);

        return view('billing.show', compact('invoice'));
    }

    public function preview(Request $request, Invoice $invoice)
    {
        Log::info('BillingWebController@preview', [
            'invoice_id' => $invoice->id,
            'format'     => $request->query('format'),
            'user'       => auth()->id(),
        ]);
        $this->ensureInvoiceBelongsToClinic($invoice);

        $invoice->load(['patient', 'items', 'clinic']);
        $data = $this->buildInvoicePdfViewData($invoice, $request->query('format'));

        Log::info('BillingWebController@preview render', ['invoice_id' => $invoice->id, 'format' => $data['format'] ?? null]);

        return view('billing.invoice-preview', $data);
    }

    public function pdf(Request $request, Invoice $invoice)
    {
        Log::info('BillingWebController@pdf', [
            'invoice' => $invoice->id,
            'format'  => $request->query('format'),
        ]);
        $this->ensureInvoiceBelongsToClinic($invoice);

        try {
            $invoice->load(['patient', 'items', 'clinic']);
            $data = $this->buildInvoicePdfViewData($invoice, $request->query('format'));

            if (!class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
                return view('billing.invoice-pdf', $data);
            }

            $pdf = Pdf::loadView('billing.invoice-pdf', $data);
            $suffix = ($data['format'] ?? 'gst') === 'gst' ? 'GST' : 'BILL';

            return $pdf->download("invoice-{$invoice->invoice_number}-{$suffix}.pdf");
        } catch (\Throwable $e) {
            Log::error('PDF generation failed', ['invoice_id' => $invoice->id, 'error' => $e->getMessage()]);

            $invoice->load(['patient', 'items', 'clinic']);

            return view('billing.invoice-pdf', $this->buildInvoicePdfViewData($invoice, $request->query('format')));
        }
    }

    /**
     * Public signed PDF for patients / WhatsApp Cloud API document delivery (no auth).
     * URL must be HTTPS and reachable by Meta’s servers (see WhatsAppService::sendDocument).
     */
    public function pdfPublic(Request $request, Invoice $invoice)
    {
        Log::info('BillingWebController@pdfPublic', [
            'invoice_id' => $invoice->id,
            'format' => $request->query('format'),
            'host' => $request->getHost(),
        ]);

        try {
            $invoice->load(['patient', 'items', 'clinic']);
            $data = $this->buildInvoicePdfViewData($invoice, $request->query('format'));

            if (! class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
                Log::warning('BillingWebController@pdfPublic: DomPDF missing, returning HTML view');

                return view('billing.invoice-pdf', $data);
            }

            $pdf = Pdf::loadView('billing.invoice-pdf', $data);
            $suffix = ($data['format'] ?? 'gst') === 'gst' ? 'GST' : 'BILL';

            return $pdf->stream("invoice-{$invoice->invoice_number}-{$suffix}.pdf");
        } catch (\Throwable $e) {
            Log::error('BillingWebController@pdfPublic failed', ['invoice_id' => $invoice->id, 'error' => $e->getMessage()]);

            abort(500, 'Invoice PDF could not be generated');
        }
    }

    private function ensureInvoiceBelongsToClinic(Invoice $invoice): void
    {
        $authClinicId = auth()->user()->clinic_id;
        if ((int) $invoice->clinic_id !== (int) $authClinicId) {
            Log::warning('BillingWebController invoice clinic mismatch', [
                'invoice_id'     => $invoice->id,
                'invoice_clinic' => $invoice->clinic_id,
                'auth_clinic'    => $authClinicId,
                'user_id'        => auth()->id(),
            ]);
            abort(403);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function buildInvoicePdfViewData(Invoice $invoice, ?string $formatQuery): array
    {
        $clinic = $invoice->clinic;
        $format = $formatQuery;
        if (!in_array($format, ['gst', 'bill'], true)) {
            $format = data_get($clinic->settings, 'default_invoice_format', 'gst');
        }
        if (!in_array($format, ['gst', 'bill'], true)) {
            $format = 'gst';
        }

        [$pdfTitle, $pdfSubtitle] = InvoicePdfPresenter::titles($format);
        $letterheadLines = InvoicePdfPresenter::letterheadLines($clinic);
        $logoDataUri = InvoicePdfPresenter::logoDataUri($clinic);
        $tagline = data_get($clinic->settings, 'invoice_tagline');
        $footerNote = data_get($clinic->settings, 'invoice_footer');
        $amountWords = InvoicePdfPresenter::amountInWords((float) ($invoice->total ?? 0));

        Log::info('BillingWebController@buildInvoicePdfViewData', [
            'format' => $format,
            'lines'  => count($letterheadLines),
            'logo'   => $logoDataUri ? 'yes' : 'no',
        ]);

        return [
            'invoice' => $invoice,
            'format' => $format,
            'pdfTitle' => $pdfTitle,
            'pdfSubtitle' => $pdfSubtitle,
            'letterheadLines' => $letterheadLines,
            'logoDataUri' => $logoDataUri,
            'tagline' => $tagline,
            'footerNote' => $footerNote,
            'amountWords' => $amountWords,
        ];
    }

    public function sendWhatsApp(Invoice $invoice)
    {
        Log::info('BillingWebController@sendWhatsApp', ['invoice' => $invoice->id]);
        $this->ensureInvoiceBelongsToClinic($invoice);

        try {
            $invoice->load(['patient', 'clinic']);
            $patient = $invoice->patient;
            $clinic = $invoice->clinic;

            if (!$patient || !$patient->phone) {
                return back()->with('error', 'Patient phone number not available');
            }

            // Format phone number
            $phone = preg_replace('/[^0-9]/', '', $patient->phone);
            if (strlen($phone) === 10) {
                $phone = '91' . $phone;
            }

            // Create WhatsApp message
            $message = "Hello {$patient->name},\n\n";
            $message .= "Your invoice from {$clinic->name} is ready.\n\n";
            $message .= "Invoice #: {$invoice->invoice_number}\n";
            $message .= "Amount: ₹" . number_format($invoice->total, 2) . "\n";
            $message .= "Status: " . ucfirst($invoice->payment_status) . "\n\n";
            
            $balance = $invoice->total - ($invoice->paid ?? 0);
            if ($balance > 0) {
                $message .= "Balance Due: ₹" . number_format($balance, 2) . "\n\n";
            }
            
            $message .= "Thank you for choosing {$clinic->name}!";

            // Generate WhatsApp URL
            $whatsappUrl = "https://wa.me/{$phone}?text=" . urlencode($message);

            // Update invoice
            $invoice->update(['whatsapp_link_sent_at' => now()]);

            // Redirect to WhatsApp (opens in new tab via JavaScript)
            return back()->with('success', 'Opening WhatsApp...')
                         ->with('whatsapp_url', $whatsappUrl);
                         
        } catch (\Throwable $e) {
            Log::error('WhatsApp send failed', ['invoice_id' => $invoice->id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Failed to send WhatsApp: ' . $e->getMessage());
        }
    }

    public function markPaid(Request $request, Invoice $invoice)
    {
        Log::info('BillingWebController@markPaid', ['invoice' => $invoice->id]);
        $this->ensureInvoiceBelongsToClinic($invoice);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'method' => 'required|in:cash,upi,card,bank_transfer,netbanking',
            'reference' => 'nullable|string',
        ]);

        try {
            Payment::create([
                'clinic_id' => $invoice->clinic_id,
                'invoice_id' => $invoice->id,
                'patient_id' => $invoice->patient_id,
                'amount' => $validated['amount'],
                'payment_method' => $validated['method'],
                'transaction_ref' => $validated['reference'] ?? null,
                'payment_date' => now(),
                'recorded_by' => auth()->id(),
            ]);

            // Update invoice paid amount and status
            $invoice->paid = ($invoice->paid ?? 0) + $validated['amount'];
            if ($invoice->paid >= $invoice->total) {
                $invoice->payment_status = 'paid';
            } elseif ($invoice->paid > 0) {
                $invoice->payment_status = 'partial';
            }
            $invoice->save();

            Log::info('Payment recorded successfully', ['invoice_id' => $invoice->id, 'amount' => $validated['amount']]);

            return back()->with('success', 'Payment recorded successfully');
        } catch (\Throwable $e) {
            Log::error('Payment recording failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to record payment: ' . $e->getMessage());
        }
    }
}
