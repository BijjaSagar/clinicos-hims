<?php $__env->startSection('title', 'ABDM HIU'); ?>
<?php $__env->startSection('breadcrumb', 'ABDM / HIU'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6 max-w-3xl mx-auto space-y-6">
    <h1 class="text-xl font-bold text-gray-900">Health Information User (HIU) — M3 scaffold</h1>
    <p class="text-sm text-gray-500">Record intended links to external HIPs / care contexts. Production requires ABDM gateway credentials and certificate setup.</p>

    <?php if(isset($hiuSchemaReady) && !$hiuSchemaReady): ?>
    <div class="rounded-lg bg-amber-50 text-amber-900 px-4 py-3 text-sm border border-amber-100">
        HIU table not found. Run <code class="bg-amber-100 px-1 rounded">php artisan migrate</code> to save links here.
    </div>
    <?php endif; ?>

    <?php if(session('success')): ?>
        <div class="rounded-lg bg-green-50 text-green-800 px-4 py-3 text-sm"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="font-semibold mb-4">Register interest / link</h2>
        <form method="POST" action="<?php echo e(route('abdm.hiu.store')); ?>" class="space-y-4">
            <?php echo csrf_field(); ?>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Patient</label>
                <select name="patient_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <?php $__currentLoopData = $patients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($p->id); ?>"><?php echo e($p->name); ?> <?php if($p->abha_id): ?> (<?php echo e($p->abha_id); ?>) <?php endif; ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">HIP ID (remote)</label>
                <input type="text" name="hip_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="From gateway / facility registry">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Care context reference</label>
                <input type="text" name="care_context_reference" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium">Save</button>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200 font-semibold">Saved rows</div>
        <ul class="divide-y divide-gray-100">
            <?php $__empty_1 = true; $__currentLoopData = $links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <li class="px-4 py-3 text-sm">
                    <div class="font-medium"><?php echo e($row->patient->name ?? 'Patient'); ?></div>
                    <div class="text-xs text-gray-500">Status: <?php echo e($row->status); ?> · HIP: <?php echo e($row->hip_id ?? '—'); ?></div>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <li class="px-4 py-6 text-center text-gray-500 text-sm">No HIU rows yet.</li>
            <?php endif; ?>
        </ul>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/abdm/hiu.blade.php ENDPATH**/ ?>