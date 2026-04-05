@php
    $clinic = $invoice->clinic;
    $format = $format ?? 'gst';
    $pdfTitle = $pdfTitle ?? ($format === 'gst' ? 'TAX INVOICE' : 'INVOICE');
    $pdfSubtitle = $pdfSubtitle ?? '';
    $letterheadLines = $letterheadLines ?? [];
    $logoDataUri = $logoDataUri ?? null;
    $tagline = $tagline ?? null;
    $footerNote = $footerNote ?? null;
    $amountWords = $amountWords ?? '';
    $items = $invoice->items ?? collect();
    $gstRateFirst = (float) ($items->first()->gst_rate ?? ($clinic->settings['default_gst_rate'] ?? 18));
    $halfRate = $gstRateFirst / 2;
    $showTaxStrip = $clinic->gstin || $clinic->pan || $clinic->registration_number;
@endphp
<div class="sheet">
    <div class="top-rule"></div>
    <table class="head-row" cellspacing="0" cellpadding="0">
        <tr>
            <td style="width:58%;">
                @if($logoDataUri)
                    <img class="logo" src="{{ $logoDataUri }}" alt="Logo">
                    <div style="height:8px;"></div>
                @endif
                <div class="facility-name">{{ $clinic->name ?? 'Healthcare facility' }}</div>
                @if($tagline)
                    <div class="tagline">{{ $tagline }}</div>
                @endif
                <div class="letterhead">
                    @foreach($letterheadLines as $line)
                        {{ $line }}<br>
                    @endforeach
                </div>
                @if($showTaxStrip)
                <div class="gst-strip">
                    @if($clinic->gstin)
                        <strong>GSTIN:</strong> {{ $clinic->gstin }}
                    @endif
                    @if($clinic->pan)
                        {{ $clinic->gstin ? ' · ' : '' }}<strong>PAN:</strong> {{ $clinic->pan }}
                    @endif
                    @if($clinic->registration_number)
                        {{ ($clinic->gstin || $clinic->pan) ? ' · ' : '' }}<strong>Reg.:</strong> {{ $clinic->registration_number }}
                    @endif
                </div>
                @endif
            </td>
            <td style="width:42%;">
                <div class="meta-box">
                    <div class="doc-title">{{ $pdfTitle }}</div>
                    @if($pdfSubtitle)
                        <div class="doc-sub">{{ $pdfSubtitle }}</div>
                    @endif
                    <div class="inv-num">
                        No. <strong>{{ $invoice->invoice_number }}</strong><br>
                        <span style="font-weight:400;color:#64748b;">Date:
                            {{ $invoice->invoice_date ? $invoice->invoice_date->format('d M Y') : $invoice->created_at->format('d M Y') }}
                        </span>
                    </div>
                    <div style="margin-top:10px;text-align:right;">
                        @if(($invoice->payment_status ?? '') === 'paid')
                            <span class="badge badge-paid">Paid</span>
                        @elseif(($invoice->payment_status ?? '') === 'partial')
                            <span class="badge badge-part">Partially paid</span>
                        @else
                            <span class="badge badge-pend">Pending</span>
                        @endif
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <div class="section-h">Bill to</div>
    <div class="party">
        <div class="party-name">{{ $invoice->patient->name ?? 'Patient' }}</div>
        <div class="party-sm">
            @if($invoice->patient->phone ?? null) Phone: {{ $invoice->patient->phone }}<br>@endif
            @if($invoice->patient->email ?? null) Email: {{ $invoice->patient->email }}<br>@endif
            UHID / ID: {{ $invoice->patient->patient_uid ?? $invoice->patient->id }}
        </div>
    </div>

    @if($format === 'gst')
    <table class="items" cellspacing="0">
        <thead>
            <tr>
                <th style="width:4%;">#</th>
                <th style="width:32%;">Description</th>
                <th class="c" style="width:9%;">SAC / HSN</th>
                <th class="c" style="width:6%;">Qty</th>
                <th class="r" style="width:10%;">Rate</th>
                <th class="r" style="width:11%;">Taxable</th>
                <th class="r" style="width:8%;">CGST</th>
                <th class="r" style="width:8%;">SGST</th>
                <th class="r" style="width:12%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $i => $item)
            <tr>
                <td class="c">{{ $i + 1 }}</td>
                <td>{{ $item->description }}</td>
                <td class="c" style="font-size:8.5px;">{{ $item->sac_code ?? $item->hsn_code ?? '—' }}</td>
                <td class="c">{{ rtrim(rtrim(number_format((float)$item->quantity, 2), '0'), '.') }}</td>
                <td class="r">₹{{ number_format($item->unit_price, 2) }}</td>
                <td class="r">₹{{ number_format($item->taxable_amount, 2) }}</td>
                <td class="r" style="font-size:8.5px;">₹{{ number_format($item->cgst_amount, 2) }}</td>
                <td class="r" style="font-size:8.5px;">₹{{ number_format($item->sgst_amount, 2) }}</td>
                <td class="r"><strong>₹{{ number_format($item->total, 2) }}</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <table class="items" cellspacing="0">
        <thead>
            <tr>
                <th style="width:5%;">#</th>
                <th style="width:47%;">Description</th>
                <th class="c" style="width:8%;">Qty</th>
                <th class="r" style="width:14%;">Rate</th>
                <th class="r" style="width:16%;">Amount (₹)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $i => $item)
            <tr>
                <td class="c">{{ $i + 1 }}</td>
                <td>{{ $item->description }}</td>
                <td class="c">{{ rtrim(rtrim(number_format((float)$item->quantity, 2), '0'), '.') }}</td>
                <td class="r">₹{{ number_format($item->unit_price, 2) }}</td>
                <td class="r"><strong>₹{{ number_format($item->total, 2) }}</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <table class="sum-wrap" cellspacing="0" cellpadding="0">
        <tr>
            <td style="width:55%; padding-right:12px;">
                <div class="words">
                    <strong>Amount in words (Indian rupees):</strong><br>
                    {{ $amountWords }}
                </div>
            </td>
            <td style="width:45%;">
                <table class="sum-table">
                    @if($format === 'gst')
                    <tr>
                        <td class="lbl">Taxable value</td>
                        <td class="val">₹{{ number_format($invoice->subtotal ?? 0, 2) }}</td>
                    </tr>
                    @if(($invoice->discount_amount ?? 0) > 0)
                    <tr>
                        <td class="lbl">Discount</td>
                        <td class="val" style="color:#b91c1c;">−₹{{ number_format($invoice->discount_amount, 2) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="lbl">CGST @ {{ number_format($halfRate, 2) }}%</td>
                        <td class="val">₹{{ number_format($invoice->cgst_amount ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">SGST @ {{ number_format($halfRate, 2) }}%</td>
                        <td class="val">₹{{ number_format($invoice->sgst_amount ?? 0, 2) }}</td>
                    </tr>
                    @else
                    <tr>
                        <td class="lbl">Subtotal</td>
                        <td class="val">₹{{ number_format($invoice->subtotal ?? 0, 2) }}</td>
                    </tr>
                    @if(($invoice->discount_amount ?? 0) > 0)
                    <tr>
                        <td class="lbl">Discount</td>
                        <td class="val" style="color:#b91c1c;">−₹{{ number_format($invoice->discount_amount, 2) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="lbl" colspan="2" style="font-size:8px;color:#94a3b8;padding-top:6px;">
                            Tax components are not itemised on this bill layout. Use the GST tax invoice for statutory GST detail.
                        </td>
                    </tr>
                    @endif
                    <tr class="grand">
                        <td>Grand total</td>
                        <td>₹{{ number_format($invoice->total ?? 0, 2) }}</td>
                    </tr>
                    @if(($invoice->paid ?? 0) > 0)
                    <tr>
                        <td class="lbl">Paid</td>
                        <td class="val" style="color:#047857;">₹{{ number_format($invoice->paid, 2) }}</td>
                    </tr>
                    @php $bal = ($invoice->total ?? 0) - ($invoice->paid ?? 0); @endphp
                    @if($bal > 0.009)
                    <tr>
                        <td class="lbl">Balance due</td>
                        <td class="val" style="color:#b91c1c;">₹{{ number_format($bal, 2) }}</td>
                    </tr>
                    @endif
                    @endif
                </table>
            </td>
        </tr>
    </table>

    @if($invoice->notes ?? null)
    <div style="margin-top:12px;font-size:9px;color:#475569;">
        <strong>Notes:</strong> {{ $invoice->notes }}
    </div>
    @endif

    <div class="foot">
        <p><strong>{{ $clinic->name ?? '' }}</strong> — thank you for your trust.</p>
        <p style="margin-top:4px;">Computer-generated document. Signature not required unless mandated by law.</p>
        @if(data_get($clinic->settings, 'payment_terms'))
        <p style="margin-top:6px;">{{ data_get($clinic->settings, 'payment_terms') }}</p>
        @endif
        @if($footerNote)
        <p style="margin-top:6px;color:#64748b;">{{ $footerNote }}</p>
        @endif
    </div>
</div>
