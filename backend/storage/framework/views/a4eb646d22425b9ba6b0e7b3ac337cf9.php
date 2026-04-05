
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['subtitle' => '']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['subtitle' => '']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>
<div class="w-full text-center mb-8 sm:mb-10 md:mb-12">
    <a href="<?php echo e(url('/')); ?>"
       class="inline-flex max-w-full items-center justify-center bg-transparent border-0 shadow-none ring-0 p-0 outline-none focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/35 focus-visible:ring-offset-2 focus-visible:ring-offset-white rounded-sm">
        <img
            src="<?php echo e(asset('images/clinicos-logo.png')); ?>"
            alt="ClinicOS"
            width="480"
            height="144"
            loading="eager"
            decoding="async"
            class="mx-auto block w-auto max-w-[min(100%,32rem)] h-24 sm:h-28 md:h-32 lg:h-36 object-contain object-center"
        />
    </a>
    <?php if($subtitle !== ''): ?>
    <p class="mt-4 sm:mt-5 text-sm sm:text-base text-gray-500 max-w-lg mx-auto leading-relaxed"><?php echo e($subtitle); ?></p>
    <?php endif; ?>
</div>
<?php $__env->startPush('scripts'); ?>
<script>
(function () {
    console.log('[ClinicOS][auth:brand-mark]', { path: '<?php echo e(request()->path()); ?>', hasSubtitle: <?php echo e($subtitle !== '' ? 'true' : 'false'); ?> });
})();
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/auth/partials/brand-mark.blade.php ENDPATH**/ ?>