<?php $__env->startSection('title', 'NABH checklist'); ?>
<?php $__env->startSection('breadcrumb', 'Compliance'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6 max-w-3xl mx-auto space-y-6">
    <h1 class="text-xl font-bold text-gray-900">NABH-oriented checklist</h1>
    <p class="text-sm text-gray-500">Internal documentation aid for small clinics. This is not a certification.</p>

    <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $title => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h2 class="font-semibold text-gray-900 mb-3"><?php echo e($title); ?></h2>
            <ul class="list-disc list-inside space-y-2 text-sm text-gray-700">
                <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($line); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/compliance/nabh.blade.php ENDPATH**/ ?>