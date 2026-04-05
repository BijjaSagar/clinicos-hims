<?php $__env->startSection('title', 'Workspace v2'); ?>
<?php $__env->startSection('breadcrumb', 'Workspace v2'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-5xl mx-auto space-y-6 px-2 sm:px-0">

    <div class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">ClinicOS v2 · Laravel Blade</p>
        <h1 class="mt-1 text-xl font-bold text-gray-900"><?php echo e($clinic->name); ?></h1>
        <p class="mt-1 text-sm text-gray-500">
            API <code class="rounded bg-gray-100 px-1.5 py-0.5 text-xs"><?php echo e($bootstrap['api']['version'] ?? '2'); ?></code>
            · Same tenant and modules as <code class="rounded bg-gray-100 px-1.5 py-0.5 text-xs">/api/v2/bootstrap</code>
        </p>
        <p class="mt-3 text-sm text-gray-600">
            Build new screens here with Blade components; use <strong>route('app.home')</strong> and versioned API routes under <code class="text-xs">/api/v2</code> (module middleware already applied).
        </p>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 shadow-sm">
        <h2 class="text-base font-semibold text-gray-900">Product modules</h2>
        <p class="mt-1 text-sm text-gray-500">Super Admin toggles · <code class="text-xs">clinics.settings.enabled_product_modules</code></p>
        <ul class="mt-4 grid gap-2 sm:grid-cols-2">
            <?php $__currentLoopData = config('clinic_modules.modules', []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $meta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $on = isset($enabledSet[$key]); ?>
                <li class="flex items-start justify-between rounded-xl border px-3 py-2.5 text-sm <?php echo e($on ? 'border-emerald-200 bg-emerald-50/70' : 'border-gray-100 bg-gray-50/90 text-gray-500'); ?>">
                    <span>
                        <span class="font-medium text-gray-900"><?php echo e($meta['label'] ?? $key); ?></span>
                        <?php if(!empty($meta['description'])): ?>
                            <span class="mt-0.5 block text-xs text-gray-500"><?php echo e($meta['description']); ?></span>
                        <?php endif; ?>
                    </span>
                    <span class="shrink-0 text-xs font-semibold <?php echo e($on ? 'text-emerald-700' : 'text-gray-400'); ?>"><?php echo e($on ? 'On' : 'Off'); ?></span>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 shadow-sm" x-data="{ open: false }">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-base font-semibold text-gray-900">Bootstrap JSON</h2>
            <button type="button" @click="open = !open" class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50">
                <span x-text="open ? 'Hide' : 'Show'"></span>
            </button>
        </div>
        <p class="mt-1 text-sm text-gray-500">Matches <code class="text-xs">GET /api/v2/bootstrap</code> (for mobile / external clients).</p>
        <pre x-show="open" x-cloak class="mt-4 max-h-80 overflow-auto rounded-xl bg-gray-900 p-4 text-xs text-gray-100"><?php echo e($bootstrapJson); ?></pre>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/app-v2/index.blade.php ENDPATH**/ ?>