<?php $__env->startSection('title', 'Pharmacy PO / GRN'); ?>
<?php $__env->startSection('breadcrumb', 'Pharmacy · Purchases'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-4 sm:p-5 lg:p-7 space-y-5 max-w-6xl mx-auto">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900 font-display">Purchase orders / GRN</h1>
            <p class="text-sm text-gray-500 mt-0.5">Goods receipt against supplier invoices — stock batches created automatically (Phase E).</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="<?php echo e(route('pharmacy.purchases.create')); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700">New GRN</a>
            <a href="<?php echo e(route('pharmacy.index')); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold border border-gray-200 bg-white hover:bg-gray-50">Pharmacy home</a>
        </div>
    </div>

    <?php if(session('success')): ?>
    <div class="px-4 py-3 rounded-xl text-sm font-medium" style="background:#ecfdf5;color:#059669;border:1px solid #a7f3d0;"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
    <div class="px-4 py-3 rounded-xl text-sm font-medium bg-red-50 text-red-800 border border-red-200"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <form method="get" class="flex flex-wrap gap-2 items-center">
        <input type="search" name="q" value="<?php echo e(request('q')); ?>" placeholder="PO # or invoice #" class="px-3 py-2 border border-gray-200 rounded-lg text-sm min-w-[200px]">
        <button type="submit" class="px-4 py-2 rounded-lg text-sm font-semibold bg-slate-800 text-white">Search</button>
    </form>

    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        <th class="px-4 py-3">PO #</th>
                        <th class="px-4 py-3">Received</th>
                        <th class="px-4 py-3">Supplier</th>
                        <th class="px-4 py-3">Invoice</th>
                        <th class="px-4 py-3 text-right">Net</th>
                        <th class="px-4 py-3">Payment</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $purchases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50/80">
                        <td class="px-4 py-3 font-mono text-xs font-semibold text-gray-900"><?php echo e($p->purchase_number); ?></td>
                        <td class="px-4 py-3 whitespace-nowrap"><?php echo e($p->received_date ? \Carbon\Carbon::parse($p->received_date)->format('d M Y') : '—'); ?></td>
                        <td class="px-4 py-3"><?php echo e($p->supplier->name ?? '—'); ?></td>
                        <td class="px-4 py-3 text-xs"><?php echo e($p->invoice_number ?? '—'); ?></td>
                        <td class="px-4 py-3 text-right font-semibold">₹<?php echo e(number_format((float) $p->net_amount, 2)); ?></td>
                        <td class="px-4 py-3"><span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-amber-50 text-amber-800"><?php echo e($p->payment_status); ?></span></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="6" class="px-4 py-12 text-center text-gray-400">No purchases recorded yet. Create a GRN to receive stock.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($purchases instanceof \Illuminate\Pagination\AbstractPaginator): ?>
        <div class="px-4 py-3 border-t border-gray-100"><?php echo e($purchases->links()); ?></div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/pharmacy/purchases-index.blade.php ENDPATH**/ ?>