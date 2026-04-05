<?php $__env->startSection('title', 'Add Medicine'); ?>
<?php $__env->startSection('breadcrumb', 'Add Medicine'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6">
    <div class="max-w-3xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Add New Medicine</h1>
                <p class="text-sm text-gray-500 mt-0.5">Enter medicine details to add to inventory</p>
            </div>
            <a href="<?php echo e(route('pharmacy.inventory')); ?>" class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border rounded-xl hover:bg-gray-50">
                Back
            </a>
        </div>

        <form action="<?php echo e(route('pharmacy.items.store')); ?>" method="POST" class="space-y-6">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="medicine_catalog_id" value="<?php echo e(old('medicine_catalog_id')); ?>">

            <?php if(($catalogCount ?? 0) > 0): ?>
            <p class="text-sm text-gray-600 rounded-xl border border-teal-100 bg-teal-50/50 px-4 py-3">
                National catalog loaded (<strong><?php echo e(number_format($catalogCount)); ?></strong> products).
                Fastest: open <a href="<?php echo e(route('pharmacy.inventory')); ?>" class="text-teal-700 font-medium underline">Pharmacy → Inventory</a>, click <strong>Add Medicine</strong>, type the brand name, pick from the list — it auto-fills and links this SKU to the catalog.
            </p>
            <?php endif; ?>

            <div class="bg-white rounded-xl border border-gray-200">
                <div class="px-5 py-4 border-b"><h2 class="text-base font-semibold text-gray-900">Drug Information</h2></div>
                <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Drug Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="<?php echo e(old('name')); ?>" required class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Generic Name</label>
                        <input type="text" name="generic_name" value="<?php echo e(old('generic_name')); ?>" class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Manufacturer</label>
                        <input type="text" name="manufacturer" value="<?php echo e(old('manufacturer')); ?>" class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Unit <span class="text-red-500">*</span></label>
                        <select name="unit" required class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="tablet" <?php echo e(old('unit') === 'tablet' ? 'selected' : ''); ?>>Tablet</option>
                            <option value="capsule" <?php echo e(old('unit') === 'capsule' ? 'selected' : ''); ?>>Capsule</option>
                            <option value="syrup" <?php echo e(old('unit') === 'syrup' ? 'selected' : ''); ?>>Syrup (ml)</option>
                            <option value="injection" <?php echo e(old('unit') === 'injection' ? 'selected' : ''); ?>>Injection</option>
                            <option value="cream" <?php echo e(old('unit') === 'cream' ? 'selected' : ''); ?>>Cream/Ointment</option>
                            <option value="drops" <?php echo e(old('unit') === 'drops' ? 'selected' : ''); ?>>Drops</option>
                            <option value="sachet" <?php echo e(old('unit') === 'sachet' ? 'selected' : ''); ?>>Sachet</option>
                            <option value="other" <?php echo e(old('unit') === 'other' ? 'selected' : ''); ?>>Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pack Size</label>
                        <input type="text" name="pack_size" value="<?php echo e(old('pack_size')); ?>" placeholder="e.g. 10 tablets" class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Schedule</label>
                        <select name="schedule" class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">None</option>
                            <option value="H" <?php echo e(old('schedule') === 'H' ? 'selected' : ''); ?>>Schedule H</option>
                            <option value="H1" <?php echo e(old('schedule') === 'H1' ? 'selected' : ''); ?>>Schedule H1</option>
                            <option value="X" <?php echo e(old('schedule') === 'X' ? 'selected' : ''); ?>>Schedule X</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">HSN Code</label>
                        <input type="text" name="hsn_code" value="<?php echo e(old('hsn_code')); ?>" class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200">
                <div class="px-5 py-4 border-b"><h2 class="text-base font-semibold text-gray-900">Pricing & Stock</h2></div>
                <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">MRP <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" name="mrp" value="<?php echo e(old('mrp')); ?>" required class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Selling Price <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" name="selling_price" value="<?php echo e(old('selling_price')); ?>" required class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">GST Rate (%) <span class="text-red-500">*</span></label>
                        <select name="gst_rate" required class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="0" <?php echo e(old('gst_rate') == '0' ? 'selected' : ''); ?>>0%</option>
                            <option value="5" <?php echo e(old('gst_rate') == '5' ? 'selected' : ''); ?>>5%</option>
                            <option value="12" <?php echo e(old('gst_rate') == '12' ? 'selected' : ''); ?>>12%</option>
                            <option value="18" <?php echo e(old('gst_rate') == '18' ? 'selected' : ''); ?>>18%</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Reorder Level</label>
                        <input type="number" name="reorder_level" value="<?php echo e(old('reorder_level', 10)); ?>" class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Reorder Qty</label>
                        <input type="number" name="reorder_qty" value="<?php echo e(old('reorder_qty', 50)); ?>" class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Storage Conditions</label>
                        <input type="text" name="storage_conditions" value="<?php echo e(old('storage_conditions')); ?>" placeholder="e.g. Store below 25°C" class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>
                    <div class="flex items-center gap-2 mt-4">
                        <input type="checkbox" name="is_controlled" id="is_controlled" value="1" <?php echo e(old('is_controlled') ? 'checked' : ''); ?> class="rounded text-blue-600 focus:ring-blue-500">
                        <label for="is_controlled" class="text-sm text-gray-700">Controlled Substance</label>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="<?php echo e(route('pharmacy.inventory')); ?>" class="px-6 py-2.5 text-sm font-medium text-gray-600 bg-white border rounded-xl hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-6 py-2.5 text-sm font-semibold text-white bg-teal-600 rounded-xl hover:bg-teal-700 shadow-sm">Add Medicine</button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/pharmacy/add-item.blade.php ENDPATH**/ ?>