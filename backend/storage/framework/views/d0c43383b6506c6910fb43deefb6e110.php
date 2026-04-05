<?php $__env->startSection('title', 'Enter Lab Results'); ?>
<?php $__env->startSection('breadcrumb', 'Enter Lab Results'); ?>

<?php $__env->startSection('content'); ?>
<div x-data="resultEntry()" class="p-4 sm:p-5 lg:p-7 space-y-5">

    
    <div class="flex items-center gap-4">
        <a href="<?php echo e(route('laboratory.orders')); ?>"
           class="p-2 rounded-lg border border-gray-200 text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div class="flex-1">
            <h1 class="text-xl font-bold text-gray-900 font-display">Enter Lab Results</h1>
            <p class="text-sm text-gray-500 mt-0.5">Order: <span class="font-semibold text-teal-600"><?php echo e($order->order_number); ?></span></p>
        </div>
        <div class="flex items-center gap-2">
            <?php
                $priorityMap = [
                    'routine' => ['label' => 'Routine', 'bg' => '#f1f5f9', 'color' => '#64748b'],
                    'urgent'  => ['label' => 'Urgent',  'bg' => '#fffbeb', 'color' => '#d97706'],
                    'stat'    => ['label' => 'STAT',    'bg' => '#fff1f2', 'color' => '#dc2626'],
                ];
                $p = $priorityMap[$order->priority ?? 'routine'] ?? $priorityMap['routine'];
            ?>
            <span class="px-2.5 py-1 rounded-full text-xs font-semibold"
                  style="background:<?php echo e($p['bg']); ?>;color:<?php echo e($p['color']); ?>;"><?php echo e($p['label']); ?></span>
        </div>
    </div>

    
    <div class="bg-white border border-gray-200 rounded-xl p-5">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-5">
            <div>
                <p class="text-xs font-medium text-gray-400 mb-1">Patient</p>
                <p class="text-sm font-bold text-gray-900"><?php echo e($order->patient_name); ?></p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-400 mb-1">Order Date</p>
                <p class="text-sm font-semibold text-gray-900">
                    <?php echo e($order->created_at ? \Carbon\Carbon::parse($order->created_at)->format('d M Y, H:i') : '—'); ?>

                </p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-400 mb-1">Status</p>
                <?php
                    $statusMap = [
                        'pending'          => ['label' => 'Pending',          'bg' => '#f1f5f9', 'color' => '#64748b'],
                        'sample_collected' => ['label' => 'Sample Collected', 'bg' => '#eff6ff', 'color' => '#1447e6'],
                        'processing'       => ['label' => 'Processing',       'bg' => '#fffbeb', 'color' => '#d97706'],
                        'completed'        => ['label' => 'Completed',        'bg' => '#ecfdf5', 'color' => '#059669'],
                    ];
                    $s = $statusMap[$order->status] ?? $statusMap['pending'];
                ?>
                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold"
                      style="background:<?php echo e($s['bg']); ?>;color:<?php echo e($s['color']); ?>;"><?php echo e($s['label']); ?></span>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-400 mb-1">Notes</p>
                <p class="text-sm text-gray-600"><?php echo e($order->notes ?? '—'); ?></p>
            </div>
        </div>
    </div>

    
    <form method="POST" action="<?php echo e(route('laboratory.save-result', $order->id)); ?>" id="resultsForm">
        <?php echo csrf_field(); ?>

        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-900">Test Results</h3>
                <span class="text-xs text-gray-400"><?php echo e($items->count()); ?> test(s)</span>
            </div>

            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide w-[22%]">Test Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide w-[18%]">Reference Range</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide w-[20%]">Result Value <span class="text-red-400">*</span></th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide w-[8%]">Unit</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide w-[10%]">Abnormal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50 transition-colors" x-data="{ abnormal: <?php echo e($item->is_abnormal ? 'true' : 'false'); ?> }">
                            <td class="px-4 py-4">
                                <input type="hidden" name="results[<?php echo e($index); ?>][item_id]" value="<?php echo e($item->id); ?>">
                                <p class="text-sm font-semibold text-gray-900"><?php echo e($item->test_name); ?></p>
                                <?php if($item->status === 'completed'): ?>
                                <span class="text-xs font-semibold text-green-600">Completed</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-4">
                                <p class="text-sm text-gray-600"><?php echo e($item->reference_range ?? '—'); ?></p>
                            </td>
                            <td class="px-4 py-4">
                                <input type="text" name="results[<?php echo e($index); ?>][value]"
                                    value="<?php echo e(old("results.{$index}.value", $item->result_value ?? '')); ?>"
                                    required
                                    placeholder="Enter result…"
                                    :class="abnormal ? 'border-red-300 bg-red-50 focus:ring-red-500' : 'border-gray-200 focus:ring-blue-500'"
                                    class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 transition-colors">
                            </td>
                            <td class="px-4 py-4">
                                <span class="text-sm text-gray-600"><?php echo e($item->unit ?? '—'); ?></span>
                            </td>
                            <td class="px-4 py-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="hidden" name="results[<?php echo e($index); ?>][is_abnormal]" value="0">
                                    <input type="checkbox" name="results[<?php echo e($index); ?>][is_abnormal]" value="1"
                                        x-model="abnormal"
                                        <?php echo e(old("results.{$index}.is_abnormal", $item->is_abnormal ?? false) ? 'checked' : ''); ?>

                                        class="w-4 h-4 rounded border-gray-300 text-red-600 focus:ring-red-500">
                                    <span class="text-xs font-medium" :class="abnormal ? 'text-red-600' : 'text-gray-500'"
                                        x-text="abnormal ? 'Yes' : 'No'"></span>
                                </label>
                            </td>
                            <td class="px-4 py-4">
                                <input type="text" name="results[<?php echo e($index); ?>][remarks]"
                                    value="<?php echo e(old("results.{$index}.remarks", $item->remarks ?? '')); ?>"
                                    placeholder="Optional remarks…"
                                    class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-sm text-gray-400">
                                No test items found for this order.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        
        <div class="bg-amber-50 border border-amber-200 rounded-xl px-5 py-3 flex items-start gap-3">
            <svg class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <p class="text-xs text-amber-800">
                Mark any test result as <strong>Abnormal</strong> if the value falls outside the reference range. Abnormal results will be highlighted on the patient's report.
            </p>
        </div>

        
        <div class="flex items-center justify-between">
            <a href="<?php echo e(route('laboratory.orders')); ?>" class="px-4 py-2.5 text-sm font-semibold text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                Cancel
            </a>
            <div class="flex gap-3">
                <button type="submit" name="action" value="draft"
                    class="px-5 py-2.5 text-sm font-semibold text-gray-700 border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">
                    Save Draft
                </button>
                <button type="submit" name="action" value="save_print"
                    @click="printAfterSave = true"
                    class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-xl transition-all hover:shadow-lg"
                    style="background:linear-gradient(135deg,#1447E6,#0891B2);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Save &amp; Print
                </button>
            </div>
        </div>
    </form>

</div>

<?php $__env->startPush('scripts'); ?>
<script>
function resultEntry() {
    return {
        printAfterSave: false,
    };
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/lab/result-entry.blade.php ENDPATH**/ ?>