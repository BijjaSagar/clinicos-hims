<?php $__env->startSection('title', 'Expiry alerts'); ?>
<?php $__env->startSection('breadcrumb', 'Pharmacy · Expiry alerts'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-4 sm:p-5 lg:p-7 space-y-5">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900 font-display">Expiry alerts</h1>
            <p class="text-sm text-gray-500 mt-0.5">Batches with stock expiring within the selected window</p>
        </div>
        <a href="<?php echo e(route('pharmacy.index')); ?>" class="px-3 py-2 text-sm font-semibold text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
            Back to Pharmacy
        </a>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl p-4">
        <form method="GET" action="<?php echo e(route('pharmacy.expiry-alerts')); ?>" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Days ahead</label>
                <input type="number" name="days" min="1" max="365" value="<?php echo e((int) $days); ?>"
                    class="w-28 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-900 text-white text-sm font-semibold rounded-lg hover:bg-gray-700 transition-colors">
                Apply
            </button>
        </form>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Batches</h3>
            <span class="text-xs text-gray-400"><?php echo e($rows->total() ?? 0); ?> total</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Medicine</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Batch</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Expiry</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Qty</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $name = $row->item->name ?? '—';
                        $exp = $row->expiry_date ? \Carbon\Carbon::parse($row->expiry_date)->startOfDay() : null;
                        $daysLeft = $exp ? now()->startOfDay()->diffInDays($exp, false) : null;
                    ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-semibold text-gray-900"><?php echo e($name); ?></td>
                        <td class="px-4 py-3 text-sm text-gray-600 font-mono"><?php echo e($row->batch_number ?? '—'); ?></td>
                        <td class="px-4 py-3 text-sm">
                            <span class="text-gray-800"><?php echo e($exp ? $exp->format('d M Y') : '—'); ?></span>
                            <?php if($daysLeft !== null): ?>
                            <span class="ml-2 text-xs font-semibold <?php echo e($daysLeft < 0 ? 'text-red-700' : ($daysLeft <= 7 ? 'text-red-600' : ($daysLeft <= 30 ? 'text-amber-600' : 'text-gray-500'))); ?>">
                                <?php if($daysLeft < 0): ?> (expired) <?php else: ?> <?php endif; ?>
                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700"><?php echo e($row->quantity_available ?? 0); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="4" class="px-4 py-12 text-center text-sm text-gray-500">No batches in this window with available stock.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($rows->hasPages()): ?>
        <div class="px-5 py-4 border-t border-gray-100">
            <?php echo e($rows->withQueryString()->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/pharmacy/expiry-alerts.blade.php ENDPATH**/ ?>