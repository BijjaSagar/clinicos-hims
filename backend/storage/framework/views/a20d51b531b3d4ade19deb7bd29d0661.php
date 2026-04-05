<?php $__env->startSection('title', 'Dispensing History'); ?>
<?php $__env->startSection('breadcrumb', 'Dispensing History'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-4 sm:p-6 lg:p-8 space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Dispensing History</h1>
            <p class="text-sm text-gray-500 mt-0.5">Track all pharmacy dispensing records</p>
        </div>
        <a href="<?php echo e(route('pharmacy.index')); ?>" class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border rounded-xl hover:bg-gray-50">
            Back
        </a>
    </div>

    
    <div class="bg-white rounded-xl border p-4">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Rx number or patient..."
                       class="w-full text-sm rounded-lg border-gray-300 focus:ring-teal-500 focus:border-teal-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">From</label>
                <input type="date" name="from" value="<?php echo e(request('from')); ?>" class="w-full text-sm rounded-lg border-gray-300 focus:ring-teal-500 focus:border-teal-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">To</label>
                <input type="date" name="to" value="<?php echo e(request('to')); ?>" class="w-full text-sm rounded-lg border-gray-300 focus:ring-teal-500 focus:border-teal-500">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 text-sm font-semibold text-white bg-teal-600 rounded-lg hover:bg-teal-700">
                    Filter
                </button>
            </div>
        </form>
    </div>

    
    <div class="bg-white rounded-xl border shadow-sm">
        <?php if($dispensings->isEmpty()): ?>
            <div class="p-12 text-center text-gray-400">
                <svg class="mx-auto h-12 w-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <p class="font-medium">No dispensing records found</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rx #</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Patient</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Items</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">By</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php $__currentLoopData = $dispensings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900"><?php echo e($d->dispensing_number ?? '—'); ?></td>
                            <td class="px-4 py-3 text-sm text-gray-700"><?php echo e($d->patient?->name ?? '—'); ?></td>
                            <td class="px-4 py-3 text-sm text-gray-500"><?php echo e($d->items->count()); ?> item(s)</td>
                            <td class="px-4 py-3 text-sm font-semibold text-gray-900">₹<?php echo e(number_format($d->total, 2)); ?></td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-700"><?php echo e(ucfirst($d->payment_mode ?? '—')); ?></span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500"><?php echo e($d->dispensedBy?->name ?? '—'); ?></td>
                            <td class="px-4 py-3 text-sm text-gray-500"><?php echo e($d->created_at?->format('d M Y H:i')); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t"><?php echo e($dispensings->links()); ?></div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/pharmacy/dispensing-history.blade.php ENDPATH**/ ?>