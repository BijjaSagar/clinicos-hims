<?php $__env->startSection('title', 'Register'); ?>

<?php $__env->startSection('content'); ?>
<form method="POST" action="<?php echo e(route('register.post')); ?>" class="w-full max-w-5xl mx-auto" x-data="{ plan: '<?php echo e(old('plan', 'professional')); ?>' }">
    <?php echo csrf_field(); ?>

    <?php echo $__env->make('auth.partials.brand-mark', ['subtitle' => 'Create your clinic · 14-day free trial'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">
        
        <div class="lg:col-span-4 space-y-3">
            <p class="text-sm font-semibold text-gray-800">Choose plan</p>
            <p class="text-xs text-gray-500 -mt-1">All plans include a free trial — cancel anytime.</p>
            <div class="space-y-2">
                <?php
                $plans = [
                    ['key'=>'starter',     'name'=>'Starter',     'price'=>'₹2,999', 'features'=>'EMR, Billing, WhatsApp'],
                    ['key'=>'professional','name'=>'Pro',         'price'=>'₹5,999', 'features'=>'+ Analytics, ABDM'],
                    ['key'=>'hospital',    'name'=>'Hospital',    'price'=>'₹14,999','features'=>'+ IPD, Pharmacy, Lab'],
                ];
                ?>
                <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <label class="block cursor-pointer">
                    <input type="radio" name="plan" value="<?php echo e($p['key']); ?>" class="sr-only" x-model="plan"
                        <?php echo e(old('plan', 'professional') === $p['key'] ? 'checked' : ''); ?>>
                    <div
                        class="rounded-xl border-2 px-3 py-2.5 transition-all"
                        :class="plan === '<?php echo e($p['key']); ?>'
                            ? 'border-blue-500 bg-blue-50/80 ring-2 ring-blue-500/20'
                            : 'border-gray-200 bg-white hover:border-gray-300'"
                    >
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-sm font-bold text-gray-900"><?php echo e($p['name']); ?></span>
                            <span class="text-sm font-extrabold text-blue-600"><?php echo e($p['price']); ?><span class="text-xs font-normal text-gray-500">/mo</span></span>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5"><?php echo e($p['features']); ?></p>
                    </div>
                </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <p class="text-xs text-gray-400">No credit card required to start.</p>
        </div>

        
        <div class="lg:col-span-8 bg-white rounded-2xl border border-gray-100 shadow-lg shadow-gray-200/40 p-5 sm:p-6 lg:p-7">
            <h2 class="text-lg font-bold text-gray-900 font-display">Account details</h2>
            <p class="text-sm text-gray-500 mt-0.5 mb-5">We&apos;ll set up your clinic workspace after you register.</p>

            <?php if(session('error')): ?>
            <div class="bg-red-50 border border-red-100 rounded-xl p-3 mb-4 text-red-800 text-sm" role="alert"><?php echo e(session('error')); ?></div>
            <?php endif; ?>
            <?php if(session('success')): ?>
            <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-3 mb-4 text-emerald-800 text-sm"><?php echo e(session('success')); ?></div>
            <?php endif; ?>
            <?php if($errors->any()): ?>
            <div class="bg-red-50 border border-red-100 rounded-xl p-3 mb-4">
                <ul class="text-red-700 text-sm space-y-1">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <?php echo e($error); ?>

                    </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-1">
                    <label for="name" class="block text-sm font-semibold text-gray-800 mb-1">Full name</label>
                    <input type="text" name="name" id="name" value="<?php echo e(old('name')); ?>"
                        class="w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-gray-50/80 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 focus:bg-white"
                        placeholder="Dr. Sharma" required autocomplete="name">
                </div>
                <div class="sm:col-span-1">
                    <label for="phone" class="block text-sm font-semibold text-gray-800 mb-1">Phone</label>
                    <input type="tel" name="phone" id="phone" value="<?php echo e(old('phone')); ?>"
                        class="w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-gray-50/80 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 focus:bg-white"
                        placeholder="+91 98765 43210" required autocomplete="tel">
                </div>
                <div class="sm:col-span-2">
                    <label for="email" class="block text-sm font-semibold text-gray-800 mb-1">Email</label>
                    <input type="email" name="email" id="email" value="<?php echo e(old('email')); ?>"
                        class="w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-gray-50/80 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 focus:bg-white"
                        placeholder="doctor@clinic.com" required autocomplete="email">
                </div>
                <div class="sm:col-span-1">
                    <label for="clinic_name" class="block text-sm font-semibold text-gray-800 mb-1">Clinic name</label>
                    <input type="text" name="clinic_name" id="clinic_name" value="<?php echo e(old('clinic_name')); ?>"
                        class="w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-gray-50/80 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 focus:bg-white"
                        placeholder="Sharma Skin Clinic" required autocomplete="organization">
                </div>
                <div class="sm:col-span-1">
                    <label for="specialty" class="block text-sm font-semibold text-gray-800 mb-1">Specialty</label>
                    <select name="specialty" id="specialty"
                        class="w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-gray-50/80 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 focus:bg-white"
                        required>
                        <option value="">Select specialty</option>
                        <option value="dermatology" <?php if(old('specialty') === 'dermatology'): echo 'selected'; endif; ?>>Dermatology</option>
                        <option value="physiotherapy" <?php if(old('specialty') === 'physiotherapy'): echo 'selected'; endif; ?>>Physiotherapy</option>
                        <option value="dental" <?php if(old('specialty') === 'dental'): echo 'selected'; endif; ?>>Dental</option>
                        <option value="ophthalmology" <?php if(old('specialty') === 'ophthalmology'): echo 'selected'; endif; ?>>Ophthalmology</option>
                        <option value="orthopedics" <?php if(old('specialty') === 'orthopedics'): echo 'selected'; endif; ?>>Orthopedics</option>
                        <option value="ent" <?php if(old('specialty') === 'ent'): echo 'selected'; endif; ?>>ENT</option>
                        <option value="gynecology" <?php if(old('specialty') === 'gynecology'): echo 'selected'; endif; ?>>Gynecology</option>
                        <option value="general" <?php if(old('specialty') === 'general'): echo 'selected'; endif; ?>>General Practice</option>
                    </select>
                </div>
                <div class="sm:col-span-1">
                    <label for="password" class="block text-sm font-semibold text-gray-800 mb-1">Password</label>
                    <input type="password" name="password" id="password"
                        class="w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-gray-50/80 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 focus:bg-white"
                        placeholder="Min. 8 characters" required autocomplete="new-password">
                </div>
                <div class="sm:col-span-1">
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-800 mb-1">Confirm</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        class="w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-gray-50/80 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 focus:bg-white"
                        placeholder="Repeat password" required autocomplete="new-password">
                </div>
            </div>

            <button type="submit"
                class="mt-6 w-full py-3 px-4 rounded-xl text-white font-semibold text-sm shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2"
                style="background: linear-gradient(135deg, #1447E6 0%, #0891B2 100%);">
                Create account
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </button>

            <p class="mt-5 text-center text-sm text-gray-500">
                Already have an account?
                <a href="<?php echo e(route('login')); ?>" class="font-semibold text-blue-600 hover:text-blue-700">Sign in</a>
            </p>
        </div>
    </div>
</form>
<?php $__env->startPush('scripts'); ?>
<script>
(function () {
    console.log('[ClinicOS][auth:register]', { route: 'register', path: window.location.pathname });
})();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/auth/register.blade.php ENDPATH**/ ?>