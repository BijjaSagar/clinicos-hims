<?php $__env->startSection('title', 'Edit User'); ?>
<?php $__env->startSection('subtitle', $user->name); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-2xl">
    <form action="<?php echo e(route('admin.users.update', $user)); ?>" method="POST" class="space-y-6">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div class="bg-white rounded-xl p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">User Information</h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Full Name *</label>
                    <input type="text" name="name" value="<?php echo e(old('name', $user->name)); ?>" required
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
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
                        <input type="email" name="email" value="<?php echo e(old('email', $user->email)); ?>" required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
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
                        <input type="text" name="phone" value="<?php echo e(old('phone', $user->phone)); ?>"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">New Password</label>
                    <input type="password" name="password"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Leave blank to keep current password">
                    <p class="text-xs text-gray-500 mt-1">Minimum 8 characters. Leave blank to keep current password.</p>
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
                            <option value="super_admin" <?php echo e(old('role', $user->role) === 'super_admin' ? 'selected' : ''); ?>>Super Admin</option>
                            <option value="owner" <?php echo e(old('role', $user->role) === 'owner' ? 'selected' : ''); ?>>Clinic Owner</option>
                            <option value="doctor" <?php echo e(old('role', $user->role) === 'doctor' ? 'selected' : ''); ?>>Doctor</option>
                            <option value="receptionist" <?php echo e(old('role', $user->role) === 'receptionist' ? 'selected' : ''); ?>>Receptionist</option>
                            <option value="nurse" <?php echo e(old('role', $user->role) === 'nurse' ? 'selected' : ''); ?>>Nurse</option>
                            <option value="staff" <?php echo e(old('role', $user->role) === 'staff' ? 'selected' : ''); ?>>Staff</option>
                        </select>
                    </div>

                    <div id="clinic-field">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Clinic</label>
                        <select name="clinic_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">No Clinic</option>
                            <?php $__currentLoopData = $clinics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $clinic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($clinic->id); ?>" <?php echo e(old('clinic_id', $user->clinic_id) == $clinic->id ? 'selected' : ''); ?>><?php echo e($clinic->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>

                <div id="specialty-field">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Specialty</label>
                    <select name="specialty" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Specialty</option>
                        <?php $__currentLoopData = ['general', 'dermatology', 'dental', 'ophthalmology', 'pediatrics', 'orthopedics', 'cardiology', 'gynecology', 'physiotherapy']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $spec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($spec); ?>" <?php echo e(old('specialty', $user->specialty) === $spec ? 'selected' : ''); ?>><?php echo e(ucfirst($spec)); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', $user->is_active) ? 'checked' : ''); ?>

                            class="w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-gray-700">User is active</span>
                    </label>
                </div>
            </div>
        </div>

        
        <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
            <div class="grid grid-cols-3 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">Created</p>
                    <p class="font-medium text-gray-900"><?php echo e($user->created_at->format('d M Y, h:i A')); ?></p>
                </div>
                <div>
                    <p class="text-gray-500">Last Updated</p>
                    <p class="font-medium text-gray-900"><?php echo e($user->updated_at->format('d M Y, h:i A')); ?></p>
                </div>
                <div>
                    <p class="text-gray-500">Last Login</p>
                    <p class="font-medium text-gray-900"><?php echo e($user->last_login_at ? $user->last_login_at->format('d M Y, h:i A') : 'Never'); ?></p>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <a href="<?php echo e(route('admin.users.index')); ?>" class="px-6 py-2.5 text-gray-700 hover:text-gray-900">
                Cancel
            </a>
            <div class="flex items-center gap-3">
                <?php if($user->id !== auth()->id()): ?>
                <button type="button" onclick="document.getElementById('delete-form').submit()" 
                    class="px-4 py-2.5 text-red-600 hover:text-red-700 font-medium">
                    Delete User
                </button>
                <?php endif; ?>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition-colors">
                    Save Changes
                </button>
            </div>
        </div>
    </form>

    <?php if($user->id !== auth()->id()): ?>
    <form id="delete-form" action="<?php echo e(route('admin.users.destroy', $user)); ?>" method="POST" class="hidden" onsubmit="return confirm('Are you sure you want to delete this user?')">
        <?php echo csrf_field(); ?>
        <?php echo method_field('DELETE'); ?>
    </form>
    <?php endif; ?>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    const roleSelect = document.getElementById('role-select');
    const clinicField = document.getElementById('clinic-field');
    const specialtyField = document.getElementById('specialty-field');
    
    function updateFields() {
        const role = roleSelect.value;
        clinicField.style.display = role === 'super_admin' ? 'none' : 'block';
        specialtyField.style.display = role === 'doctor' ? 'block' : 'none';
    }
    
    roleSelect.addEventListener('change', updateFields);
    updateFields();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/admin/users/edit.blade.php ENDPATH**/ ?>