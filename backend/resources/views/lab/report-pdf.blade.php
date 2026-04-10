<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Lab Report - {{ $order->order_number }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; line-height: 1.5; margin: 0; padding: 0; }
        .container { padding: 30px; }
        .header { border-bottom: 2px solid #0057ff; padding-bottom: 10px; margin-bottom: 20px; }
        .clinic-name { color: #0057ff; font-size: 24px; font-weight: bold; margin: 0; text-transform: uppercase; }
        .clinic-details { font-size: 10px; color: #666; margin: 2px 0; }
        
        .report-title { text-align: center; font-size: 18px; font-weight: bold; margin-top: 10px; text-decoration: underline; color: #1a1a1a; }
        
        .patient-info { width: 100%; border-collapse: collapse; margin-bottom: 25px; border: 1px solid #eee; background-color: #f9f9f9; }
        .patient-info td { padding: 8px 12px; vertical-align: top; width: 25%; }
        .label { font-size: 9px; font-weight: bold; color: #777; text-transform: uppercase; display: block; margin-bottom: 2px; }
        .value { font-size: 11px; font-weight: bold; color: #333; }
        
        .results-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .results-table th { background-color: #f2f2f2; padding: 10px; text-align: left; font-size: 10px; text-transform: uppercase; border-bottom: 1px solid #ddd; }
        .results-table td { padding: 12px 10px; border-bottom: 1px solid #eee; font-size: 11px; }
        .test-name { font-weight: bold; color: #000; }
        .result-value { font-weight: bold; }
        .abnormal { color: #dc2626; }
        .unit { color: #666; font-size: 9px; margin-left: 4px; }
        .ref-range { color: #555; font-style: italic; }
        
        .category-row { background-color: #fafafa; }
        .category-name { font-weight: bold; font-size: 10px; color: #0057ff; padding: 5px 10px; text-transform: uppercase; letter-spacing: 1px; }
        
        .notes-section { margin-top: 30px; padding: 15px; border: 1px solid #eee; background-color: #fffaf0; border-radius: 5px; }
        .notes-title { font-size: 9px; font-weight: bold; color: #b45309; text-transform: uppercase; margin-bottom: 5px; }
        .notes-content { font-size: 10px; font-style: italic; color: #444; }
        
        .footer { position: fixed; bottom: 50px; left: 30px; right: 30px; }
        .signature-area { width: 100%; border-top: 1px solid #eee; padding-top: 20px; }
        .signatory { text-align: right; }
        .sign-line { width: 150px; border-bottom: 1px solid #333; margin-left: auto; margin-bottom: 5px; height: 30px; text-align: center; font-style: italic; color: #0057ff; }
        .sign-label { font-size: 9px; font-weight: bold; text-transform: uppercase; color: #777; }
        
        .system-meta { font-size: 8px; color: #aaa; margin-top: 50px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Clinic Header -->
        <div class="header">
            <table width="100%">
                <tr>
                    <td>
                        <h1 class="clinic-name">{{ $clinic->name ?? 'CLINICOS LABS' }}</h1>
                        <p class="clinic-details">{{ $clinic->address ?? 'Healthcare Management System' }}</p>
                        <p class="clinic-details">Phone: {{ $clinic->phone ?? '—' }} | Email: {{ $clinic->email ?? '—' }}</p>
                    </td>
                    <td align="right" valign="top">
                        <p style="font-size: 14px; font-weight: bold; margin: 0;">LABORATORY REPORT</p>
                        <p style="font-size: 10px; color: #666; margin: 0;">Order No: {{ $order->order_number }}</p>
                        <p style="font-size: 10px; color: #666; margin: 0;">Date: {{ \Carbon\Carbon::parse($order->created_at)->format('d-M-Y') }}</p>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Patient Info -->
        <table class="patient-info">
            <tr>
                <td>
                    <span class="label">Patient Name</span>
                    <span class="value">{{ $order->patient_name }}</span>
                </td>
                <td>
                    <span class="label">Patient ID / UHID</span>
                    <span class="value">{{ $order->patient?->uhid ?? '#' . $order->patient_id }}</span>
                </td>
                <td>
                    <span class="label">Age / Gender</span>
                    <span class="value">{{ $order->patient->age ?? '—' }}Y / {{ ucfirst($order->patient->sex ?? '—') }}</span>
                </td>
                <td>
                    <span class="label">Referred By</span>
                    <span class="value">{{ $order->doctor ? $order->doctor->name : 'Self' }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="label">Sample Received</span>
                    <span class="value">{{ \Carbon\Carbon::parse($order->sample_collected_at)->format('d-M-Y H:i') }}</span>
                </td>
                <td>
                    <span class="label">Report Date</span>
                    <span class="value">{{ now()->format('d-M-Y H:i') }}</span>
                </td>
                <td colspan="2">
                    <span class="label">Status</span>
                    <span class="value" style="color: green;">VERIFIED & COMPLETED</span>
                </td>
            </tr>
        </table>

        <!-- Results Table -->
        <table class="results-table">
            <thead>
                <tr>
                    <th width="45%">Test Description / Parameter</th>
                    <th width="30%" align="right">Result (Unit)</th>
                    <th width="25%" align="right">Reference Range</th>
                </tr>
            </thead>
            <tbody>
                @php $currentCategory = null; @endphp
                @foreach($items as $item)
                    @if($currentCategory !== ($item->category ?? 'General'))
                        @php $currentCategory = $item->category ?? 'General'; @endphp
                        <tr class="category-row">
                            <td colspan="3" class="category-name">{{ $currentCategory }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="test-name">{{ $item->test_name }}</td>
                        <td align="right">
                            <span class="result-value {{ $item->is_abnormal ? 'abnormal' : '' }}">
                                {{ $item->result_value ?? '—' }}
                            </span>
                            @if($item->unit)
                                <span class="unit">{{ $item->unit }}</span>
                            @endif
                        </td>
                        <td align="right" class="ref-range">{{ $item->reference_range ?? '—' }}</td>
                    </tr>
                    @if($item->remarks)
                    <tr>
                        <td colspan="3" style="font-size: 9px; color: #666; padding-top: 2px;">
                            <strong>Note:</strong> {{ $item->remarks }}
                        </td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>

        <!-- Clinical Notes -->
        @if($order->notes)
        <div class="notes-section">
            <div class="notes-title">Clinical Interpretations & Notes</div>
            <div class="notes-content">{{ $order->notes }}</div>
        </div>
        @endif

        <!-- Footer / Signature -->
        <div class="footer">
            <table class="signature-area">
                <tr>
                    <td width="50%">
                        <p style="font-size: 9px; color: #777;">
                            * This is a computer-generated report and does not require a physical signature.<br>
                            * Please correlate clinically with symptoms.
                        </p>
                    </td>
                    <td class="signatory">
                        <div class="sign-line">{{ auth()->user()->name }}</div>
                        <div class="sign-label">Authorized Signatory / Pathologist</div>
                    </td>
                </tr>
            </table>
            
            <div class="system-meta">
                Generated via CLINICOS Healthcare Management System | www.clinicos.com
            </div>
        </div>
    </div>
</body>
</html>
