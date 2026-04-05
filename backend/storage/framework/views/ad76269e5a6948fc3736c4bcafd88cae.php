<?php $__env->startSection('title', 'Stock Report'); ?>
<?php $__env->startSection('breadcrumb', 'Stock Report'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-4 sm:p-6 lg:p-8 space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Stock Report</h1>
            <p class="text-sm text-gray-500 mt-0.5">Complete stock overview with batch details and expiry tracking</p>
        </div>
        <a href="<?php echo e(route('pharmacy.index')); ?>" class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border rounded-xl hover:bg-gray-50">
            Back
        </a>
    </div>

    <?php if($items->isEmpty()): ?>
        <div class="bg-white rounded-xl border p-12 text-center text-gray-400">
            <svg class="mx-auto h-12 w-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            <p class="font-medium">No items in inventory</p>
            <p class="text-sm mt-1">Add medicines through the inventory page first.</p>
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-white rounded-xl border shadow-sm">
                    <div class="px-5 py-4 border-b flex items-center justify-between">
                        <div>
                            <h3 class="font-semibold text-gray-900"><?php echo e($item->name); ?></h3>
                            <p class="text-xs text-gray-500"><?php echo e($item->generic_name ?? ''); ?> &middot; <?php echo e($item->unit); ?></p>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-bold <?php echo e($item->current_stock <= ($item->reorder_level ?? 0) ? 'text-red-600' : 'text-green-600'); ?>">
                                Stock: <?php echo e($item->current_stock ?? 0); ?>

                            </div>
                            <div class="text-xs text-gray-500">MRP: ₹<?php echo e(number_format($item->mrp ?? 0, 2)); ?></div>
                        </div>
                    </div>

                    <?php if($item->stocks->count() > 0): ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-100 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Batch</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Expiry</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Qty In</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Qty Out</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Available</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <?php $__currentLoopData = $item->stocks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stock): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $expiry = $stock->expiry_date;
                                            $isExpired = $expiry && $expiry < $today;
                                            $isExpiringSoon = $expiry && !$isExpired && $expiry <= $soon;
                                        ?>
                                        <tr class="<?php echo e($isExpired ? 'bg-red-50' : ($isExpiringSoon ? 'bg-amber-50' : '')); ?>">
                                            <td class="px-4 py-2 text-gray-900"><?php echo e($stock->batch_number); ?></td>
                                            <td class="px-4 py-2 <?php echo e($isExpired ? 'text-red-600 font-medium' : ($isExpiringSoon ? 'text-amber-600' : 'text-gray-600')); ?>">
                                                <?php echo e($expiry ? \Carbon\Carbon::parse($expiry)->format('d M Y') : '—'); ?>

                                                <?php if($isExpired): ?> <span class="text-xs">(Expired)</span> <?php endif; ?>
                                                <?php if($isExpiringSoon): ?> <span class="text-xs">(Expiring soon)</span> <?php endif; ?>
                                            </td>
                                            <td class="px-4 py-2 text-right text-gray-700"><?php echo e($stock->quantity_in); ?></td>
                                            <td class="px-4 py-2 text-right text-gray-700"><?php echo e($stock->quantity_out); ?></td>
                                            <td class="px-4 py-2 text-right font-semibold text-gray-900"><?php echo e($stock->quantity_available); ?></td>
                                            <td class="px-4 py-2">
                                                <?php if($stock->quantity_available <= 0): ?>
                                                    <span class="px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-500">Empty</span>
                                                <?php elseif($isExpired): ?>
                                                    <span class="px-2 py-0.5 text-xs rounded-full bg-red-100 text-red-700">Expired</span>
                                                <?php else: ?>
                                                    <span class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-700">Active</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="px-5 py-3 text-sm text-gray-400">No batches recorded for this item.</div>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/pharmacy/stock-report.blade.php ENDPATH**/ ?>