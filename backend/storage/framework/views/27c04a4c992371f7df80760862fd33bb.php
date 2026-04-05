<?php $__env->startSection('title', 'Edit User'); ?>
<?php $__env->startSection('breadcrumb', 'Users & Staff / Edit User'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6 max-w-2xl mx-auto">
    
    <div class="mb-6">
        <a href="<?php echo e(route('clinic.users.index')); ?>" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-3">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
            </svg>
            Back to Users
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Edit User</h1>
        <p class="text-sm text-gray-500 mt-1">Update user information and access settings.</p>
    </div>

    
    <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold text-white flex-shrink-0
                <?php if($user->role === 'owner'): ?> bg-gradient-to-br from-amber-500 to-orange-600
                <?php elseif($user->role === 'doctor'): ?> bg-gradient-to-br from-blue-500 to-indigo-600
                <?php elseif($user->role === 'receptionist'): ?> bg-gradient-to-br from-purple-500 to-pink-600
                <?php elseif($user->role === 'nurse'): ?> bg-gradient-to-br from-pink-500 to-rose-600
                <?php else: ?> bg-gradient-to-br from-gray-500 to-gray-600 <?php endif; ?>
            ">
                <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

            </div>
            <div class="flex-1">
                <h2 class="text-lg font-semibold text-gray-900"><?php echo e($user->name); ?></h2>
                <p class="text-sm text-gray-500"><?php echo e($user->email); ?></p>
                <div class="flex items-center gap-2 mt-2">
                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full
                        <?php if($user->role === 'owner'): ?> bg-amber-100 text-amber-800
                        <?php elseif($user->role === 'doctor'): ?> bg-blue-100 text-blue-800
                        <?php elseif($user->role === 'receptionist'): ?> bg-purple-100 text-purple-800
                        <?php elseif($user->role === 'nurse'): ?> bg-pink-100 text-pink-800
                        <?php else: ?> bg-gray-100 text-gray-800 <?php endif; ?>
                    "><?php echo e(ucfirst($user->role)); ?></span>
                    <?php if($user->is_active): ?>
                    <span class="px-2 py-0.5 text-xs font-medium bg-emerald-100 text-emerald-700 rounded-full">Active</span>
                    <?php else: ?>
                    <span class="px-2 py-0.5 text-xs font-medium bg-red-100 text-red-700 rounded-full">Inactive</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <form action="<?php echo e(route('clinic.users.update', $user)); ?>" method="POST" class="p-6 space-y-6">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="<?php echo e(old('name', $user->name)); ?>" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                    placeholder="Dr. John Doe">
                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-sm text-red-500"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address <span class="text-red-500">*</span></label>
                <input type="email" name="email" id="email" value="<?php echo e(old('email', $user->email)); ?>" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                    placeholder="john@clinic.com">
                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-sm text-red-500"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                <input type="tel" name="phone" id="phone" value="<?php echo e(old('phone', $user->phone)); ?>"
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                    placeholder="+91 98765 43210">
                <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-sm text-red-500"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Role <span class="text-red-500">*</span></label>
                <select name="role" id="role" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                    <?php if($user->role === 'owner'): ?> disabled <?php endif; ?>>
                    <option value="owner" <?php echo e(old('role', $user->role) === 'owner' ? 'selected' : ''); ?>>Owner</option>
                    <option value="doctor" <?php echo e(old('role', $user->role) === 'doctor' ? 'selected' : ''); ?>>Doctor</option>
                    <option value="receptionist" <?php echo e(old('role', $user->role) === 'receptionist' ? 'selected' : ''); ?>>Receptionist</option>
                    <option value="nurse" <?php echo e(old('role', $user->role) === 'nurse' ? 'selected' : ''); ?>>Nurse</option>
                    <option value="staff" <?php echo e(old('role', $user->role) === 'staff' ? 'selected' : ''); ?>>Other Staff</option>
                </select>
                <?php if($user->role === 'owner'): ?>
                    <input type="hidden" name="role" value="owner">
                    <p class="mt-1 text-xs text-amber-600">Owner role cannot be changed</p>
                <?php endif; ?>
                <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-sm text-red-500"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div>
                <label for="specialty" class="block text-sm font-medium text-gray-700 mb-2">Specialty / Department</label>
                <input type="text" name="specialty" id="specialty" value="<?php echo e(old('specialty', $user->specialty)); ?>"
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['specialty'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                    placeholder="Dermatology, General Medicine, etc.">
                <?php $__errorArgs = ['specialty'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-sm text-red-500"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <hr class="border-gray-200">

            <div class="p-4 bg-amber-50 border border-amber-100 rounded-xl">
                <h4 class="text-sm font-medium text-amber-900 mb-2">Change Password (Optional)</h4>
                <p class="text-xs text-amber-700 mb-4">Leave blank to keep the current password.</p>

                
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                    <input type="password" name="password" id="password"
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        placeholder="Minimum 6 characters">
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-500"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Re-enter new password">
                </div>
            </div>

            
            <div class="flex items-center justify-end gap-3 pt-4">
                <a href="<?php echo e(route('clinic.users.index')); ?>" class="px-6 py-3 border border-gray-300 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
                    Update User
                </button>
            </div>
        </form>
    </div>

    
    <?php if($user->id !== auth()->id() && $user->role !== 'owner'): ?>
    <div class="mt-6 bg-white rounded-2xl border border-red-200 overflow-hidden">
        <div class="px-6 py-4 bg-red-50 border-b border-red-200">
            <h3 class="font-semibold text-red-900">Danger Zone</h3>
        </div>
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-medium text-gray-900">Delete this user</h4>
                    <p class="text-xs text-gray-500 mt-1">Once deleted, this user will no longer be able to access the system.</p>
                </div>
                <form action="<?php echo e(route('clinic.users.destroy', $user)); ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this user? This cannot be undone.')">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-xl transition-colors">
                        Delete User
                    </button>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/clinic-users/edit.blade.php ENDPATH**/ ?>