<?php $__env->startSection('title', 'Trial Expired — Upgrade to Continue'); ?>
<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 flex flex-col items-center justify-center p-6">

    <div class="text-center mb-10">
        <div class="inline-flex items-center gap-3 mb-6">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center font-bold text-white text-xl" style="background:linear-gradient(135deg,#1447E6,#0891B2);">C</div>
            <h1 class="text-2xl font-bold text-white">ClinicOS</h1>
        </div>
        <div class="w-16 h-16 rounded-full bg-amber-500/20 border-2 border-amber-500 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h2 class="text-3xl font-bold text-white mb-2">Your free trial has ended</h2>
        <p class="text-gray-400 max-w-md mx-auto">Subscribe to continue using ClinicOS. Your data is safe and will be restored immediately after subscribing.</p>
    </div>

    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl w-full">
        <?php
        $plans = [
            ['key'=>'starter','name'=>'Starter','price'=>'₹2,999','period'=>'/month','desc'=>'Perfect for solo practitioners','color'=>'#1447E6','features'=>['Up to 500 patients','Appointments & EMR','Billing & invoicing','WhatsApp reminders','1 doctor + 2 staff']],
            ['key'=>'professional','name'=>'Professional','price'=>'₹5,999','period'=>'/month','desc'=>'For growing clinics','color'=>'#0891B2','badge'=>'Most Popular','features'=>['Unlimited patients','All Starter features','Multi-doctor support','Analytics & reports','ABDM integration','5 staff users']],
            ['key'=>'hospital','name'=>'Hospital HIMS','price'=>'₹14,999','period'=>'/month','desc'=>'Full hospital management','color'=>'#7c3aed','features'=>['All Professional features','IPD & bed management','Pharmacy module','Lab LIS module','OPD queue system','Unlimited staff']],
        ];
        ?>
        <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="bg-white rounded-2xl p-6 relative <?php echo e(isset($plan['badge']) ? 'ring-2 ring-cyan-500 shadow-cyan-500/20 shadow-xl' : ''); ?>">
            <?php if(isset($plan['badge'])): ?>
            <div class="absolute -top-3 left-1/2 -translate-x-1/2 px-4 py-1 text-xs font-bold text-white rounded-full" style="background:linear-gradient(135deg,#1447E6,#0891B2);"><?php echo e($plan['badge']); ?></div>
            <?php endif; ?>
            <h3 class="text-lg font-bold text-gray-900 mb-1"><?php echo e($plan['name']); ?></h3>
            <p class="text-xs text-gray-500 mb-4"><?php echo e($plan['desc']); ?></p>
            <div class="flex items-baseline gap-1 mb-5">
                <span class="text-3xl font-extrabold text-gray-900"><?php echo e($plan['price']); ?></span>
                <span class="text-sm text-gray-400"><?php echo e($plan['period']); ?></span>
            </div>
            <ul class="space-y-2 mb-6">
                <?php $__currentLoopData = $plan['features']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="flex items-center gap-2 text-sm text-gray-700">
                    <svg class="w-4 h-4 flex-shrink-0 text-green-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <?php echo e($f); ?>

                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
            <a href="mailto:sales@clinicos.in?subject=Subscription+<?php echo e($plan['name']); ?>"
               class="block w-full py-3 rounded-xl text-center text-sm font-bold text-white transition-all hover:opacity-90"
               style="background:linear-gradient(135deg,<?php echo e($plan['color']); ?>,<?php echo e($plan['color']); ?>cc);">
                Subscribe — <?php echo e($plan['price']); ?>/mo
            </a>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <div class="mt-8 text-center">
        <p class="text-gray-500 text-sm">Need help? Contact us at <a href="mailto:support@clinicos.in" class="text-cyan-400">support@clinicos.in</a></p>
        <form method="POST" action="<?php echo e(route('logout')); ?>" class="mt-4 inline">
            <?php echo csrf_field(); ?>
            <button type="submit" class="text-gray-500 hover:text-gray-300 text-sm underline">Log out</button>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/subscription/expired.blade.php ENDPATH**/ ?>