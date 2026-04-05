<?php $__env->startSection('title', 'New Patient'); ?>
<?php $__env->startSection('breadcrumb', 'Add Patient'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6">
    <div class="max-w-3xl mx-auto">
        
        <div class="mb-6">
            <h1 class="text-xl font-bold text-gray-900">New Patient Registration</h1>
            <p class="text-sm text-gray-500 mt-0.5">Enter patient details to create their profile</p>
        </div>

        <form action="<?php echo e(route('patients.store')); ?>" method="POST" class="space-y-6">
            <?php echo csrf_field(); ?>
            <?php if(request('return_to')): ?>
            <input type="hidden" name="return_to" value="<?php echo e(request('return_to')); ?>">
            <?php endif; ?>

            
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h3 class="font-bold text-gray-900">Personal Information</h3>
                </div>
                <div class="p-5 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Full Name *</label>
                            <input type="text" name="name" value="<?php echo e(old('name')); ?>" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Phone Number *</label>
                            <input type="tel" name="phone" value="<?php echo e(old('phone')); ?>" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="+91 98765 43210" required>
                            <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Date of Birth</label>
                            <input type="date" name="dob" value="<?php echo e(old('dob')); ?>" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <?php $__errorArgs = ['dob'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Gender</label>
                            <select name="sex" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select</option>
                                <option value="M" <?php echo e(old('sex') == 'M' ? 'selected' : ''); ?>>Male</option>
                                <option value="F" <?php echo e(old('sex') == 'F' ? 'selected' : ''); ?>>Female</option>
                                <option value="O" <?php echo e(old('sex') == 'O' ? 'selected' : ''); ?>>Other</option>
                            </select>
                            <?php $__errorArgs = ['sex'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Blood Group</label>
                            <select name="blood_group" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select</option>
                                <?php $__currentLoopData = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($bg); ?>" <?php echo e(old('blood_group') == $bg ? 'selected' : ''); ?>><?php echo e($bg); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                        <input type="email" name="email" value="<?php echo e(old('email')); ?>" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Address</label>
                        <textarea name="address" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"><?php echo e(old('address')); ?></textarea>
                    </div>
                </div>
            </div>

            
            <div class="bg-gradient-to-r from-blue-600 to-teal-500 rounded-xl p-5 text-white">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold">ABHA (Ayushman Bharat Health Account)</h4>
                        <p class="text-sm text-white/80 mt-0.5">Link patient's ABHA ID for digital health records</p>
                    </div>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-white/90 mb-1.5">ABHA Number (14 digit)</label>
                        <input type="text" name="abha_id" value="<?php echo e(old('abha_id')); ?>" class="w-full px-4 py-2.5 rounded-xl bg-white/20 border border-white/30 text-white placeholder-white/50 focus:ring-2 focus:ring-white/50" placeholder="12-3456-7890-1234">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-white/90 mb-1.5">ABHA Address</label>
                        <input type="text" name="abha_address" value="<?php echo e(old('abha_address')); ?>" class="w-full px-4 py-2.5 rounded-xl bg-white/20 border border-white/30 text-white placeholder-white/50 focus:ring-2 focus:ring-white/50" placeholder="username@abdm">
                    </div>
                </div>
            </div>

            
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h3 class="font-bold text-gray-900">Medical History</h3>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Known Allergies</label>
                        <textarea name="known_allergies" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., Penicillin, Sulfa drugs"><?php echo e(old('known_allergies')); ?></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Chronic Conditions</label>
                        <textarea name="chronic_conditions" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., Diabetes, Hypertension"><?php echo e(old('chronic_conditions')); ?></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Current Medications</label>
                        <textarea name="current_medications" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="List current medications"><?php echo e(old('current_medications')); ?></textarea>
                    </div>
                </div>
            </div>

            
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h3 class="font-bold text-gray-900">Emergency Contact</h3>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Name</label>
                            <input type="text" name="emergency_contact_name" value="<?php echo e(old('emergency_contact_name')); ?>" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Relationship</label>
                            <input type="text" name="emergency_contact_relation" value="<?php echo e(old('emergency_contact_relation')); ?>" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., Spouse, Parent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Phone</label>
                            <input type="tel" name="emergency_contact_phone" value="<?php echo e(old('emergency_contact_phone')); ?>" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="flex items-center justify-end gap-3">
                <a href="<?php echo e(route('patients.index')); ?>" class="px-6 py-2.5 text-gray-600 font-semibold rounded-xl hover:bg-gray-100 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Register Patient
                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/patients/create.blade.php ENDPATH**/ ?>