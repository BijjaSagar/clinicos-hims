<?php
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
?>
<div class="sheet">
    <div class="top-rule"></div>
    <table class="head-row" cellspacing="0" cellpadding="0">
        <tr>
            <td style="width:58%;">
                <?php if($logoDataUri): ?>
                    <img class="logo" src="<?php echo e($logoDataUri); ?>" alt="Logo">
                    <div style="height:8px;"></div>
                <?php endif; ?>
                <div class="facility-name"><?php echo e($clinic->name ?? 'Healthcare facility'); ?></div>
                <?php if($tagline): ?>
                    <div class="tagline"><?php echo e($tagline); ?></div>
                <?php endif; ?>
                <div class="letterhead">
                    <?php $__currentLoopData = $letterheadLines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php echo e($line); ?><br>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php if($showTaxStrip): ?>
                <div class="gst-strip">
                    <?php if($clinic->gstin): ?>
                        <strong>GSTIN:</strong> <?php echo e($clinic->gstin); ?>

                    <?php endif; ?>
                    <?php if($clinic->pan): ?>
                        <?php echo e($clinic->gstin ? ' · ' : ''); ?><strong>PAN:</strong> <?php echo e($clinic->pan); ?>

                    <?php endif; ?>
                    <?php if($clinic->registration_number): ?>
                        <?php echo e(($clinic->gstin || $clinic->pan) ? ' · ' : ''); ?><strong>Reg.:</strong> <?php echo e($clinic->registration_number); ?>

                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </td>
            <td style="width:42%;">
                <div class="meta-box">
                    <div class="doc-title"><?php echo e($pdfTitle); ?></div>
                    <?php if($pdfSubtitle): ?>
                        <div class="doc-sub"><?php echo e($pdfSubtitle); ?></div>
                    <?php endif; ?>
                    <div class="inv-num">
                        No. <strong><?php echo e($invoice->invoice_number); ?></strong><br>
                        <span style="font-weight:400;color:#64748b;">Date:
                            <?php echo e($invoice->invoice_date ? $invoice->invoice_date->format('d M Y') : $invoice->created_at->format('d M Y')); ?>

                        </span>
                    </div>
                    <div style="margin-top:10px;text-align:right;">
                        <?php if(($invoice->payment_status ?? '') === 'paid'): ?>
                            <span class="badge badge-paid">Paid</span>
                        <?php elseif(($invoice->payment_status ?? '') === 'partial'): ?>
                            <span class="badge badge-part">Partially paid</span>
                        <?php else: ?>
                            <span class="badge badge-pend">Pending</span>
                        <?php endif; ?>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <div class="section-h">Bill to</div>
    <div class="party">
        <div class="party-name"><?php echo e($invoice->patient->name ?? 'Patient'); ?></div>
        <div class="party-sm">
            <?php if($invoice->patient->phone ?? null): ?> Phone: <?php echo e($invoice->patient->phone); ?><br><?php endif; ?>
            <?php if($invoice->patient->email ?? null): ?> Email: <?php echo e($invoice->patient->email); ?><br><?php endif; ?>
            UHID / ID: <?php echo e($invoice->patient->patient_uid ?? $invoice->patient->id); ?>

        </div>
    </div>

    <?php if($format === 'gst'): ?>
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
            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td class="c"><?php echo e($i + 1); ?></td>
                <td><?php echo e($item->description); ?></td>
                <td class="c" style="font-size:8.5px;"><?php echo e($item->sac_code ?? $item->hsn_code ?? '—'); ?></td>
                <td class="c"><?php echo e(rtrim(rtrim(number_format((float)$item->quantity, 2), '0'), '.')); ?></td>
                <td class="r">₹<?php echo e(number_format($item->unit_price, 2)); ?></td>
                <td class="r">₹<?php echo e(number_format($item->taxable_amount, 2)); ?></td>
                <td class="r" style="font-size:8.5px;">₹<?php echo e(number_format($item->cgst_amount, 2)); ?></td>
                <td class="r" style="font-size:8.5px;">₹<?php echo e(number_format($item->sgst_amount, 2)); ?></td>
                <td class="r"><strong>₹<?php echo e(number_format($item->total, 2)); ?></strong></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    <?php else: ?>
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
            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td class="c"><?php echo e($i + 1); ?></td>
                <td><?php echo e($item->description); ?></td>
                <td class="c"><?php echo e(rtrim(rtrim(number_format((float)$item->quantity, 2), '0'), '.')); ?></td>
                <td class="r">₹<?php echo e(number_format($item->unit_price, 2)); ?></td>
                <td class="r"><strong>₹<?php echo e(number_format($item->total, 2)); ?></strong></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    <?php endif; ?>

    <table class="sum-wrap" cellspacing="0" cellpadding="0">
        <tr>
            <td style="width:55%; padding-right:12px;">
                <div class="words">
                    <strong>Amount in words (Indian rupees):</strong><br>
                    <?php echo e($amountWords); ?>

                </div>
            </td>
            <td style="width:45%;">
                <table class="sum-table">
                    <?php if($format === 'gst'): ?>
                    <tr>
                        <td class="lbl">Taxable value</td>
                        <td class="val">₹<?php echo e(number_format($invoice->subtotal ?? 0, 2)); ?></td>
                    </tr>
                    <?php if(($invoice->discount_amount ?? 0) > 0): ?>
                    <tr>
                        <td class="lbl">Discount</td>
                        <td class="val" style="color:#b91c1c;">−₹<?php echo e(number_format($invoice->discount_amount, 2)); ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td class="lbl">CGST @ <?php echo e(number_format($halfRate, 2)); ?>%</td>
                        <td class="val">₹<?php echo e(number_format($invoice->cgst_amount ?? 0, 2)); ?></td>
                    </tr>
                    <tr>
                        <td class="lbl">SGST @ <?php echo e(number_format($halfRate, 2)); ?>%</td>
                        <td class="val">₹<?php echo e(number_format($invoice->sgst_amount ?? 0, 2)); ?></td>
                    </tr>
                    <?php else: ?>
                    <tr>
                        <td class="lbl">Subtotal</td>
                        <td class="val">₹<?php echo e(number_format($invoice->subtotal ?? 0, 2)); ?></td>
                    </tr>
                    <?php if(($invoice->discount_amount ?? 0) > 0): ?>
                    <tr>
                        <td class="lbl">Discount</td>
                        <td class="val" style="color:#b91c1c;">−₹<?php echo e(number_format($invoice->discount_amount, 2)); ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td class="lbl" colspan="2" style="font-size:8px;color:#94a3b8;padding-top:6px;">
                            Tax components are not itemised on this bill layout. Use the GST tax invoice for statutory GST detail.
                        </td>
                    </tr>
                    <?php endif; ?>
                    <tr class="grand">
                        <td>Grand total</td>
                        <td>₹<?php echo e(number_format($invoice->total ?? 0, 2)); ?></td>
                    </tr>
                    <?php if(($invoice->paid ?? 0) > 0): ?>
                    <tr>
                        <td class="lbl">Paid</td>
                        <td class="val" style="color:#047857;">₹<?php echo e(number_format($invoice->paid, 2)); ?></td>
                    </tr>
                    <?php $bal = ($invoice->total ?? 0) - ($invoice->paid ?? 0); ?>
                    <?php if($bal > 0.009): ?>
                    <tr>
                        <td class="lbl">Balance due</td>
                        <td class="val" style="color:#b91c1c;">₹<?php echo e(number_format($bal, 2)); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php endif; ?>
                </table>
            </td>
        </tr>
    </table>

    <?php if($invoice->notes ?? null): ?>
    <div style="margin-top:12px;font-size:9px;color:#475569;">
        <strong>Notes:</strong> <?php echo e($invoice->notes); ?>

    </div>
    <?php endif; ?>

    <div class="foot">
        <p><strong><?php echo e($clinic->name ?? ''); ?></strong> — thank you for your trust.</p>
        <p style="margin-top:4px;">Computer-generated document. Signature not required unless mandated by law.</p>
        <?php if(data_get($clinic->settings, 'payment_terms')): ?>
        <p style="margin-top:6px;"><?php echo e(data_get($clinic->settings, 'payment_terms')); ?></p>
        <?php endif; ?>
        <?php if($footerNote): ?>
        <p style="margin-top:6px;color:#64748b;"><?php echo e($footerNote); ?></p>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/billing/partials/invoice-print-body.blade.php ENDPATH**/ ?>