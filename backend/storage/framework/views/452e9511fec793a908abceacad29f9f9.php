<?php $__env->startSection('title', 'Create User'); ?>
<?php $__env->startSection('subtitle', 'Add a new user to the platform'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-2xl">
    <form action="<?php echo e(route('admin.users.store')); ?>" method="POST" class="space-y-6">
        <?php echo csrf_field(); ?>

        <div class="bg-white rounded-xl p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">User Information</h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Full Name *</label>
                    <input type="text" name="name" value="<?php echo e(old('name')); ?>" required
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Dr. John Smith">
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email *</label>
                        <input type="email" name="email" value="<?php echo e(old('email')); ?>" required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="user@clinic.com">
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Phone</label>
                        <input type="text" name="phone" value="<?php echo e(old('phone')); ?>"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="+91 98765 43210">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Password *</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="••••••••">
                    <p class="text-xs text-gray-500 mt-1">Minimum 8 characters</p>
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Role *</label>
                        <select name="role" id="role-select" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Role</option>
                            <option value="super_admin" <?php echo e(old('role') === 'super_admin' ? 'selected' : ''); ?>>Super Admin</option>
                            <option value="owner" <?php echo e(old('role') === 'owner' ? 'selected' : ''); ?>>Clinic Owner</option>
                            <option value="doctor" <?php echo e(old('role') === 'doctor' ? 'selected' : ''); ?>>Doctor</option>
                            <option value="receptionist" <?php echo e(old('role') === 'receptionist' ? 'selected' : ''); ?>>Receptionist</option>
                            <option value="nurse" <?php echo e(old('role') === 'nurse' ? 'selected' : ''); ?>>Nurse</option>
                            <option value="staff" <?php echo e(old('role') === 'staff' ? 'selected' : ''); ?>>Staff</option>
                        </select>
                        <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div id="clinic-field">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Clinic *</label>
                        <select name="clinic_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Clinic</option>
                            <?php $__currentLoopData = $clinics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $clinic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($clinic->id); ?>" <?php echo e(old('clinic_id') == $clinic->id ? 'selected' : ''); ?>><?php echo e($clinic->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['clinic_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <div id="specialty-field">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Specialty</label>
                    <select name="specialty" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Specialty</option>
                        <option value="general">General Practice</option>
                        <option value="dermatology">Dermatology</option>
                        <option value="dental">Dental</option>
                        <option value="ophthalmology">Ophthalmology</option>
                        <option value="pediatrics">Pediatrics</option>
                        <option value="orthopedics">Orthopedics</option>
                        <option value="cardiology">Cardiology</option>
                        <option value="gynecology">Gynecology</option>
                        <option value="physiotherapy">Physiotherapy</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <a href="<?php echo e(route('admin.users.index')); ?>" class="px-6 py-2.5 text-gray-700 hover:text-gray-900">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition-colors">
                Create User
            </button>
        </div>
    </form>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    const roleSelect = document.getElementById('role-select');
    const clinicField = document.getElementById('clinic-field');
    const specialtyField = document.getElementById('specialty-field');
    
    function updateFields() {
        const role = roleSelect.value;
        
        // Show/hide clinic field based on role
        clinicField.style.display = role === 'super_admin' ? 'none' : 'block';
        
        // Show specialty field only for doctors
        specialtyField.style.display = role === 'doctor' ? 'block' : 'none';
    }
    
    roleSelect.addEventListener('change', updateFields);
    updateFields();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/admin/users/create.blade.php ENDPATH**/ ?>