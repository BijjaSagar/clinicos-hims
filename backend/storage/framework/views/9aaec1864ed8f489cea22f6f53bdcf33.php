<?php $__env->startSection('title', 'Invoice #' . ($invoice->invoice_number ?? $invoice->id)); ?>
<?php $__env->startSection('breadcrumb', 'Invoice Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6">
    <div class="max-w-4xl mx-auto space-y-6">
        
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Invoice #<?php echo e($invoice->invoice_number ?? $invoice->id); ?></h1>
                <p class="text-sm text-gray-500">Created <?php echo e($invoice->created_at->format('d M Y, h:i A')); ?></p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="<?php echo e(route('billing.preview', ['invoice' => $invoice, 'format' => 'gst'])); ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2 border border-indigo-200 bg-indigo-50 text-indigo-900 font-semibold rounded-xl hover:bg-indigo-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    Print preview
                </a>
                <a href="<?php echo e(route('billing.pdf', ['invoice' => $invoice, 'format' => 'gst'])); ?>" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    GST invoice PDF
                </a>
                <a href="<?php echo e(route('billing.pdf', ['invoice' => $invoice, 'format' => 'bill'])); ?>" class="inline-flex items-center gap-2 px-4 py-2 border border-teal-200 text-teal-800 font-semibold rounded-xl hover:bg-teal-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Simple bill PDF
                </a>
                <form action="<?php echo e(route('billing.send-whatsapp', $invoice)); ?>" method="POST" class="inline">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white font-semibold rounded-xl hover:bg-green-700 transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        Send WhatsApp
                    </button>
                </form>
            </div>
        </div>

        
        <?php 
            $status = $invoice->payment_status ?? 'pending';
            $balanceDue = ($invoice->total ?? 0) - ($invoice->paid ?? 0);
        ?>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Status</p>
                    <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold mt-1 <?php echo e($status === 'paid' ? 'bg-green-100 text-green-700' : 
                        ($status === 'partial' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-700')); ?>">
                        <?php echo e(ucfirst($status)); ?>

                    </span>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Balance Due</p>
                    <p class="text-2xl font-bold <?php echo e($balanceDue > 0 ? 'text-red-600' : 'text-green-600'); ?>">₹<?php echo e(number_format($balanceDue)); ?></p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="lg:col-span-2 space-y-6">
                
                <div class="bg-white rounded-xl border border-gray-200">
                    <div class="px-5 py-4 border-b border-gray-200">
                        <h3 class="font-bold text-gray-900">Patient Details</h3>
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Name</p>
                                <p class="font-semibold text-gray-900"><?php echo e($invoice->patient->name ?? 'N/A'); ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Phone</p>
                                <p class="font-semibold text-gray-900"><?php echo e($invoice->patient->phone ?? 'N/A'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                    $canOpenEmr = in_array(auth()->user()->role ?? '', ['doctor', 'nurse', 'owner'], true);
                ?>
                <?php if(($invoice->visit_id && $invoice->visit) || ($invoice->admission_id && $invoice->admission)): ?>
                <div class="bg-white rounded-xl border border-gray-200">
                    <div class="px-5 py-4 border-b border-gray-200">
                        <h3 class="font-bold text-gray-900">Encounter linkage</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Where this bill was generated from (OPD visit or IPD admission).</p>
                    </div>
                    <div class="p-5 flex flex-wrap gap-2">
                        <?php if($canOpenEmr && $invoice->visit_id && $invoice->visit && \Illuminate\Support\Facades\Route::has('emr.show')): ?>
                        <a href="<?php echo e(route('emr.show', [$invoice->patient, $invoice->visit])); ?>"
                           class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-semibold bg-white border border-green-200 text-green-900 hover:bg-green-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            OPD visit #<?php echo e($invoice->visit_id); ?>

                        </a>
                        <?php elseif($invoice->visit_id): ?>
                        <span class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium bg-gray-50 border border-gray-200 text-gray-600" title="Open EMR from a doctor/nurse account">OPD visit #<?php echo e($invoice->visit_id); ?></span>
                        <?php endif; ?>
                        <?php if($invoice->admission_id && $invoice->admission && \Illuminate\Support\Facades\Route::has('ipd.show')): ?>
                        <a href="<?php echo e(route('ipd.show', $invoice->admission)); ?>"
                           class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-semibold bg-white border border-indigo-200 text-indigo-900 hover:bg-indigo-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            IPD <?php echo e($invoice->admission->admission_number ?? ('#'.$invoice->admission_id)); ?>

                        </a>
                        <?php elseif($invoice->admission_id): ?>
                        <span class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium bg-gray-50 border border-gray-200 text-gray-600">IPD admission #<?php echo e($invoice->admission_id); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                
                <div class="bg-white rounded-xl border border-gray-200">
                    <div class="px-5 py-4 border-b border-gray-200">
                        <h3 class="font-bold text-gray-900">Items</h3>
                    </div>
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Description</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Qty</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Rate</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php $__empty_1 = true; $__currentLoopData = $invoice->items ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="px-5 py-4 text-gray-900"><?php echo e($item->description); ?></td>
                                <td class="px-5 py-4 text-right text-gray-600"><?php echo e($item->quantity); ?></td>
                                <td class="px-5 py-4 text-right text-gray-600">₹<?php echo e(number_format($item->unit_price ?? 0)); ?></td>
                                <td class="px-5 py-4 text-right font-semibold text-gray-900">₹<?php echo e(number_format($item->amount ?? 0)); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="px-5 py-4 text-center text-gray-400">No items</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-5 py-3 text-right font-semibold text-gray-700">Subtotal</td>
                                <td class="px-5 py-3 text-right font-semibold text-gray-900">₹<?php echo e(number_format($invoice->subtotal ?? 0)); ?></td>
                            </tr>
                            <?php if(($invoice->cgst_amount ?? 0) > 0): ?>
                            <tr>
                                <td colspan="3" class="px-5 py-2 text-right text-sm text-gray-500">CGST (9%)</td>
                                <td class="px-5 py-2 text-right text-gray-600">₹<?php echo e(number_format($invoice->cgst_amount ?? 0)); ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if(($invoice->sgst_amount ?? 0) > 0): ?>
                            <tr>
                                <td colspan="3" class="px-5 py-2 text-right text-sm text-gray-500">SGST (9%)</td>
                                <td class="px-5 py-2 text-right text-gray-600">₹<?php echo e(number_format($invoice->sgst_amount ?? 0)); ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr class="border-t border-gray-200">
                                <td colspan="3" class="px-5 py-4 text-right text-lg font-bold text-gray-900">Total</td>
                                <td class="px-5 py-4 text-right text-lg font-bold text-blue-600">₹<?php echo e(number_format($invoice->total ?? 0)); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            
            <div class="space-y-6">
                
                <div class="bg-white rounded-xl border border-gray-200">
                    <div class="px-5 py-4 border-b border-gray-200">
                        <h3 class="font-bold text-gray-900">Payments</h3>
                    </div>
                    <div class="p-5 space-y-3">
                        <?php $__empty_1 = true; $__currentLoopData = $invoice->payments ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-semibold text-gray-900">₹<?php echo e(number_format($payment->amount)); ?></p>
                                <p class="text-xs text-gray-500"><?php echo e(ucfirst($payment->method)); ?> • <?php echo e($payment->created_at->format('d M Y')); ?></p>
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">Paid</span>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-gray-400 text-sm">No payments recorded</p>
                        <?php endif; ?>
                    </div>
                </div>

                
                <?php if($balanceDue > 0): ?>
                <div class="bg-white rounded-xl border border-gray-200">
                    <div class="px-5 py-4 border-b border-gray-200">
                        <h3 class="font-bold text-gray-900">Record Payment</h3>
                    </div>
                    <form action="<?php echo e(route('billing.mark-paid', $invoice)); ?>" method="POST" class="p-5 space-y-4">
                        <?php echo csrf_field(); ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Amount</label>
                            <input type="number" name="amount" value="<?php echo e($balanceDue); ?>" step="0.01" min="1" max="<?php echo e($balanceDue); ?>" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Method</label>
                            <select name="method" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="cash">Cash</option>
                                <option value="upi">UPI</option>
                                <option value="card">Card</option>
                                <option value="bank_transfer">Bank Transfer</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Reference (Optional)</label>
                            <input type="text" name="reference" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Transaction ID">
                        </div>
                        <button type="submit" class="w-full px-4 py-2.5 bg-green-600 text-white font-semibold rounded-xl hover:bg-green-700 transition-colors">
                            Record Payment
                        </button>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="pt-4">
            <a href="<?php echo e(route('billing.index')); ?>" class="text-blue-600 hover:text-blue-700 font-medium flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Invoices
            </a>
        </div>
    </div>
</div>
<?php $__env->startPush('scripts'); ?>
<script>
(function () {
    var payload = {
        invoiceId: <?php echo e((int) $invoice->id); ?>,
        patientId: <?php echo e((int) ($invoice->patient_id ?? 0)); ?>,
        visitId: <?php echo json_encode($invoice->visit_id, 15, 512) ?>,
        admissionId: <?php echo json_encode($invoice->admission_id, 15, 512) ?>,
        hasVisitRelation: <?php echo e($invoice->relationLoaded('visit') && $invoice->visit ? 'true' : 'false'); ?>,
        hasAdmissionRelation: <?php echo e($invoice->relationLoaded('admission') && $invoice->admission ? 'true' : 'false'); ?>,
    };
    console.log('[billing.show] invoice linkage', payload);
})();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/billing/show.blade.php ENDPATH**/ ?>