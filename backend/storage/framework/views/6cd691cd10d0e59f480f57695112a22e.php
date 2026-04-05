<?php $__env->startSection('title', 'New GRN'); ?>
<?php $__env->startSection('breadcrumb', 'Pharmacy · New GRN'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-4 sm:p-5 lg:p-7 max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-3">
        <a href="<?php echo e(route('pharmacy.purchases.index')); ?>" class="text-sm text-gray-500 hover:text-gray-800">← Back</a>
    </div>
    <div>
        <h1 class="text-xl font-bold text-gray-900 font-display">Record goods receipt (GRN)</h1>
        <p class="text-sm text-gray-500 mt-0.5">Creates purchase lines and stock batches (FIFO) linked to this GRN.</p>
    </div>

    <?php if(session('error')): ?>
    <div class="px-4 py-3 rounded-xl text-sm font-medium bg-red-50 text-red-800 border border-red-200"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
    <div class="px-4 py-3 rounded-xl text-sm bg-amber-50 text-amber-900 border border-amber-200">
        <ul class="list-disc list-inside space-y-0.5">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($e); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('pharmacy.purchases.store')); ?>" class="bg-white border border-gray-200 rounded-xl p-5 space-y-6 shadow-sm">
        <?php echo csrf_field(); ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-gray-600 mb-1">Supplier</label>
                <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                    <select name="supplier_id" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white flex-1">
                        <option value="">—</option>
                        <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($s->id); ?>" <?php if(old('supplier_id') == $s->id): echo 'selected'; endif; ?>><?php echo e($s->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php if(\Illuminate\Support\Facades\Route::has('pharmacy.suppliers.index')): ?>
                        <a href="<?php echo e(route('pharmacy.suppliers.index')); ?>" class="inline-flex items-center justify-center shrink-0 px-3 py-2 text-xs font-semibold rounded-lg border border-gray-200 bg-white text-gray-800 hover:bg-gray-50">
                            Add supplier
                        </a>
                    <?php endif; ?>
                </div>
                <?php if($suppliers->isEmpty()): ?>
                <p class="text-xs text-amber-700 mt-1">No suppliers yet — add one via <strong>Suppliers</strong> or below link. You can still save GRN without supplier.</p>
                <?php endif; ?>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Supplier invoice #</label>
                <input type="text" name="invoice_number" value="<?php echo e(old('invoice_number')); ?>" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Invoice date</label>
                <input type="date" name="invoice_date" value="<?php echo e(old('invoice_date')); ?>" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Received date <span class="text-red-500">*</span></label>
                <input type="date" name="received_date" required value="<?php echo e(old('received_date', date('Y-m-d'))); ?>" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-gray-600 mb-1">Notes</label>
                <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm resize-none"><?php echo e(old('notes')); ?></textarea>
            </div>
        </div>

        <div class="border-t border-gray-100 pt-4 space-y-4">
            <h2 class="text-sm font-bold text-gray-900">Line items</h2>
            <?php $oldItems = old('items', [['item_id' => '', 'batch_number' => '', 'expiry_date' => '', 'quantity' => 1, 'free_quantity' => 0, 'purchase_rate' => '', 'mrp' => '', 'discount_percent' => 0, 'gst_rate' => 12]]); ?>
            <?php $__currentLoopData = $oldItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="border border-gray-100 rounded-lg p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 bg-slate-50/50">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Item <span class="text-red-500">*</span></label>
                    <select name="items[<?php echo e($idx); ?>][item_id]" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white">
                        <option value="">Select…</option>
                        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $it): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($it->id); ?>" <?php if(($line['item_id'] ?? '') == $it->id): echo 'selected'; endif; ?>><?php echo e($it->name); ?> (<?php echo e($it->unit ?? 'unit'); ?>)</option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Batch <span class="text-red-500">*</span></label>
                    <input type="text" name="items[<?php echo e($idx); ?>][batch_number]" required value="<?php echo e($line['batch_number'] ?? ''); ?>" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Expiry <span class="text-red-500">*</span></label>
                    <input type="date" name="items[<?php echo e($idx); ?>][expiry_date]" required value="<?php echo e($line['expiry_date'] ?? ''); ?>" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Qty <span class="text-red-500">*</span></label>
                    <input type="number" min="1" name="items[<?php echo e($idx); ?>][quantity]" required value="<?php echo e($line['quantity'] ?? 1); ?>" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Free qty</label>
                    <input type="number" min="0" name="items[<?php echo e($idx); ?>][free_quantity]" value="<?php echo e($line['free_quantity'] ?? 0); ?>" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Purchase rate <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" min="0" name="items[<?php echo e($idx); ?>][purchase_rate]" required value="<?php echo e($line['purchase_rate'] ?? ''); ?>" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">MRP <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" min="0" name="items[<?php echo e($idx); ?>][mrp]" required value="<?php echo e($line['mrp'] ?? ''); ?>" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Disc %</label>
                    <input type="number" step="0.01" min="0" max="100" name="items[<?php echo e($idx); ?>][discount_percent]" value="<?php echo e($line['discount_percent'] ?? 0); ?>" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">GST %</label>
                    <input type="number" step="0.01" min="0" max="100" name="items[<?php echo e($idx); ?>][gst_rate]" value="<?php echo e($line['gst_rate'] ?? 12); ?>" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="flex justify-end gap-3">
            <a href="<?php echo e(route('pharmacy.purchases.index')); ?>" class="px-4 py-2.5 rounded-xl text-sm font-semibold border border-gray-200">Cancel</a>
            <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700">Save GRN</button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/pharmacy/purchase-create.blade.php ENDPATH**/ ?>