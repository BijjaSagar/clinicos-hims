<?php $__env->startSection('title', 'Pharmacy suppliers'); ?>
<?php $__env->startSection('breadcrumb', 'Pharmacy · Suppliers'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-4 sm:p-6 lg:p-8 max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900 font-display">Suppliers</h1>
            <p class="text-sm text-gray-500 mt-0.5">Vendors for purchase orders and GRN. Linked to stock receipts.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <?php if(\Illuminate\Support\Facades\Route::has('pharmacy.purchases.create')): ?>
                <a href="<?php echo e(route('pharmacy.purchases.create')); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white bg-gray-900 hover:bg-gray-800">
                    New GRN
                </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="px-4 py-3 rounded-xl text-sm font-medium bg-emerald-50 text-emerald-800 border border-emerald-200"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="px-4 py-3 rounded-xl text-sm font-medium bg-red-50 text-red-800 border border-red-200"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
        <h2 class="text-sm font-bold text-gray-900 mb-4">Add supplier</h2>
        <form method="POST" action="<?php echo e(route('pharmacy.suppliers.store')); ?>" class="space-y-4">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="<?php echo e(old('name')); ?>" required maxlength="255"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="e.g. ABC Pharma Distributors">
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-red-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Contact person</label>
                    <input type="text" name="contact_person" value="<?php echo e(old('contact_person')); ?>" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Phone</label>
                    <input type="text" name="phone" value="<?php echo e(old('phone')); ?>" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Email</label>
                    <input type="email" name="email" value="<?php echo e(old('email')); ?>" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Address</label>
                    <textarea name="address" rows="2" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm resize-none"><?php echo e(old('address')); ?></textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">GST number</label>
                    <input type="text" name="gst_number" value="<?php echo e(old('gst_number')); ?>" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Drug licence</label>
                    <input type="text" name="drug_license" value="<?php echo e(old('drug_license')); ?>" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Payment terms</label>
                    <input type="text" name="payment_terms" value="<?php echo e(old('payment_terms')); ?>" placeholder="e.g. Net 30"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                </div>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="px-5 py-2 text-sm font-semibold text-white bg-gray-900 rounded-xl hover:bg-gray-800">Save supplier</button>
            </div>
        </form>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
        <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-gray-900">Saved suppliers</h2>
            <span class="text-xs text-gray-400"><?php echo e($suppliers->count()); ?> total</span>
        </div>
        <?php if($suppliers->isEmpty()): ?>
            <p class="px-5 py-10 text-center text-sm text-gray-500">No suppliers yet. Use the form above.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Name</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Phone</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">GST</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900"><?php echo e($s->name); ?></td>
                            <td class="px-4 py-3 text-gray-600"><?php echo e($s->phone ?? '—'); ?></td>
                            <td class="px-4 py-3 text-gray-600"><?php echo e($s->gst_number ?? '—'); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
console.log('[pharmacy.suppliers] page loaded', { count: <?php echo e($suppliers->count()); ?> });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/pharmacy/suppliers.blade.php ENDPATH**/ ?>