
<?php
    $modules = config('clinic_modules.modules', []);
    $specialtyPresets = config('clinic_modules.specialty_suggestions', []);
    $enabledSet = array_flip($enabledProductModuleKeys ?? []);
?>
<div class="bg-white rounded-xl p-6 border border-gray-100" id="product-modules-panel">
    <h3 class="text-lg font-semibold text-gray-900 mb-1">Product modules</h3>
    <p class="text-sm text-gray-500 mb-4">
        Only checked modules appear in the clinic sidebar (role rules still apply). Leave specialty-driven suggestions or tune manually for the region you sell into.
    </p>

    <div class="flex flex-wrap gap-2 mb-4">
        <button type="button" id="pm-select-all" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-gray-100 text-gray-800 hover:bg-gray-200">Select all</button>
        <button type="button" id="pm-clear-all" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-gray-100 text-gray-800 hover:bg-gray-200">Clear all</button>
        <button type="button" id="pm-apply-specialty" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100">Apply specialty preset</button>
    </div>

    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-2">Global</p>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-6">
        <?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $meta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(($meta['region'] ?? 'global') === 'IN'): ?>
                <?php continue; ?>
            <?php endif; ?>
            <label class="flex items-start gap-3 p-3 rounded-xl border border-gray-100 hover:border-indigo-200 cursor-pointer">
                <input type="checkbox" name="product_modules[]" value="<?php echo e($key); ?>"
                    class="mt-0.5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 pm-mod-cb"
                    <?php echo e(isset($enabledSet[$key]) ? 'checked' : ''); ?>>
                <span>
                    <span class="text-sm font-medium text-gray-900"><?php echo e($meta['label'] ?? $key); ?></span>
                    <?php if(!empty($meta['description'])): ?>
                        <span class="block text-xs text-gray-500 mt-0.5"><?php echo e($meta['description']); ?></span>
                    <?php endif; ?>
                </span>
            </label>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <p class="text-xs font-semibold uppercase tracking-wide text-amber-700 mb-2">India-specific</p>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-2">
        <?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $meta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(($meta['region'] ?? '') !== 'IN'): ?>
                <?php continue; ?>
            <?php endif; ?>
            <label class="flex items-start gap-3 p-3 rounded-xl border border-amber-100 bg-amber-50/40 hover:border-amber-200 cursor-pointer">
                <input type="checkbox" name="product_modules[]" value="<?php echo e($key); ?>"
                    class="mt-0.5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 pm-mod-cb"
                    <?php echo e(isset($enabledSet[$key]) ? 'checked' : ''); ?>>
                <span>
                    <span class="text-sm font-medium text-gray-900"><?php echo e($meta['label'] ?? $key); ?></span>
                    <?php if(!empty($meta['description'])): ?>
                        <span class="block text-xs text-gray-500 mt-0.5"><?php echo e($meta['description']); ?></span>
                    <?php endif; ?>
                </span>
            </label>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php $__errorArgs = ['product_modules'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <p class="text-red-500 text-xs mt-2"><?php echo e($message); ?></p>
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    <?php $__errorArgs = ['product_modules.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <p class="text-red-500 text-xs mt-2"><?php echo e($message); ?></p>
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
(function () {
    const specialtySelect = document.querySelector('select[name="specialty"]');
    const presets = <?php echo json_encode($specialtyPresets, 15, 512) ?>;
    const boxes = () => Array.from(document.querySelectorAll('.pm-mod-cb'));

    document.getElementById('pm-select-all')?.addEventListener('click', function () {
        boxes().forEach(function (b) { b.checked = true; });
        console.log('[product-modules] select all');
    });
    document.getElementById('pm-clear-all')?.addEventListener('click', function () {
        boxes().forEach(function (b) { b.checked = false; });
        console.log('[product-modules] clear all');
    });
    document.getElementById('pm-apply-specialty')?.addEventListener('click', function () {
        const spec = specialtySelect?.value || '';
        const suggested = presets[spec];
        console.log('[product-modules] apply preset', spec, suggested);
        if (!suggested || !Array.isArray(suggested)) {
            boxes().forEach(function (b) { b.checked = true; });
            return;
        }
        const set = new Set(suggested);
        boxes().forEach(function (b) { b.checked = set.has(b.value); });
    });
})();
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/admin/clinics/partials/product-modules.blade.php ENDPATH**/ ?>