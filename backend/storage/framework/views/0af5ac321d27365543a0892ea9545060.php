<?php $__env->startSection('title', 'Edit Patient'); ?>
<?php $__env->startSection('breadcrumb', 'Edit Patient'); ?>

<?php
    $fmtList = function ($v): string {
        if ($v === null || $v === '') {
            return '';
        }
        if (is_array($v)) {
            return implode(', ', array_map(static fn ($x) => (string) $x, $v));
        }

        return (string) $v;
    };
    $allergiesText = old('known_allergies') !== null ? old('known_allergies') : $fmtList($patient->known_allergies);
    $chronicText = old('chronic_conditions') !== null ? old('chronic_conditions') : $fmtList($patient->chronic_conditions);
    $medsText = old('current_medications') !== null ? old('current_medications') : $fmtList($patient->current_medications);
?>

<?php $__env->startSection('content'); ?>
<div class="p-4 sm:p-6 lg:p-8">
    <div class="max-w-3xl mx-auto space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 font-display tracking-tight">Edit Patient</h1>
                <p class="text-sm text-gray-500 mt-1">Update details for <?php echo e($patient->name ?? 'patient'); ?></p>
            </div>
            <a href="<?php echo e(route('patients.show', $patient)); ?>"
               class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-xl shadow-sm hover:bg-gray-50 hover:border-gray-300 transition-colors">
                Cancel
            </a>
        </div>

        <?php if(session('success')): ?>
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium border" style="background:#ecfdf5;color:#059669;border-color:#a7f3d0;">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <?php echo e(session('success')); ?>

        </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium border" style="background:#fff1f2;color:#b91c1c;border-color:#fecaca;">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <?php echo e(session('error')); ?>

        </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
            <p class="font-semibold mb-1">Please fix the following:</p>
            <ul class="list-disc list-inside space-y-0.5">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($err); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <?php endif; ?>

        <form action="<?php echo e(route('patients.update', $patient)); ?>" method="POST" class="space-y-6">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
                <div class="px-5 sm:px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-slate-50 to-white">
                    <h2 class="text-base font-bold text-gray-900">Personal information</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Name, contact, and demographics</p>
                </div>
                <div class="p-5 sm:p-6 space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-1.5">Full name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="<?php echo e(old('name', $patient->name)); ?>" required autocomplete="name"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50/50 text-gray-900 placeholder-gray-400 text-sm shadow-inner focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 focus:bg-white transition-colors">
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1.5"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-1.5">Phone <span class="text-red-500">*</span></label>
                            <input type="tel" name="phone" value="<?php echo e(old('phone', $patient->phone)); ?>" required inputmode="tel" autocomplete="tel"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50/50 text-gray-900 placeholder-gray-400 text-sm shadow-inner focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 focus:bg-white transition-colors">
                            <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1.5"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-1.5">Email</label>
                            <input type="email" name="email" value="<?php echo e(old('email', $patient->email)); ?>" autocomplete="email"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50/50 text-gray-900 placeholder-gray-400 text-sm shadow-inner focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 focus:bg-white transition-colors">
                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1.5"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-1.5">Date of birth</label>
                            <input type="date" name="dob" value="<?php echo e(old('dob', $patient->dob?->format('Y-m-d'))); ?>"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50/50 text-gray-900 text-sm shadow-inner focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 focus:bg-white transition-colors">
                            <?php $__errorArgs = ['dob'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1.5"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-1.5">Sex</label>
                            <select name="sex"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50/50 text-gray-900 text-sm shadow-inner focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 focus:bg-white transition-colors appearance-none bg-[length:1.25rem] bg-[right_0.75rem_center] bg-no-repeat"
                                    style="background-image:url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 fill=%22none%22 viewBox=%220 0 24 24%22 stroke=%236b7280%22%3E%3Cpath stroke-linecap=%22round%22 stroke-linejoin=%22round%22 stroke-width=%222%22 d=%22M19 9l-7 7-7-7%22/%3E%3C/svg%3E');">
                                <option value="">Select</option>
                                <option value="M" <?php echo e(old('sex', $patient->sex) === 'M' ? 'selected' : ''); ?>>Male</option>
                                <option value="F" <?php echo e(old('sex', $patient->sex) === 'F' ? 'selected' : ''); ?>>Female</option>
                                <option value="O" <?php echo e(old('sex', $patient->sex) === 'O' ? 'selected' : ''); ?>>Other</option>
                            </select>
                            <?php $__errorArgs = ['sex'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1.5"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-1.5">Blood group</label>
                            <select name="blood_group"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50/50 text-gray-900 text-sm shadow-inner focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 focus:bg-white transition-colors appearance-none bg-[length:1.25rem] bg-[right_0.75rem_center] bg-no-repeat"
                                    style="background-image:url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 fill=%22none%22 viewBox=%220 0 24 24%22 stroke=%236b7280%22%3E%3Cpath stroke-linecap=%22round%22 stroke-linejoin=%22round%22 stroke-width=%222%22 d=%22M19 9l-7 7-7-7%22/%3E%3C/svg%3E');">
                                <option value="">Select</option>
                                <?php $__currentLoopData = ['A+','A-','B+','B-','AB+','AB-','O+','O-']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($bg); ?>" <?php echo e(old('blood_group', $patient->blood_group) === $bg ? 'selected' : ''); ?>><?php echo e($bg); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['blood_group'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1.5"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
                <div class="px-5 sm:px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-slate-50 to-white">
                    <h2 class="text-base font-bold text-gray-900">Address</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Street, area, city — include PIN in this field if needed</p>
                </div>
                <div class="p-5 sm:p-6">
                    <label class="block text-sm font-semibold text-gray-800 mb-1.5">Full address</label>
                    <textarea name="address" rows="3" placeholder="Building, street, city, PIN…"
                              class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50/50 text-gray-900 placeholder-gray-400 text-sm shadow-inner focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 focus:bg-white transition-colors resize-y min-h-[5rem]"><?php echo e(old('address', $patient->address)); ?></textarea>
                    <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1.5"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
                <div class="px-5 sm:px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-slate-50 to-white">
                    <h2 class="text-base font-bold text-gray-900">Medical info</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Separate items with commas or new lines</p>
                </div>
                <div class="p-5 sm:p-6 space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-1.5">Known allergies</label>
                        <textarea name="known_allergies" rows="2" placeholder="e.g. Penicillin, Sulfa"
                                  class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50/50 text-gray-900 placeholder-gray-400 text-sm shadow-inner focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 focus:bg-white transition-colors resize-y"><?php echo e($allergiesText); ?></textarea>
                        <?php $__errorArgs = ['known_allergies'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1.5"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-1.5">Chronic conditions</label>
                        <textarea name="chronic_conditions" rows="2" placeholder="e.g. Diabetes, Hypertension"
                                  class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50/50 text-gray-900 placeholder-gray-400 text-sm shadow-inner focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 focus:bg-white transition-colors resize-y"><?php echo e($chronicText); ?></textarea>
                        <?php $__errorArgs = ['chronic_conditions'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1.5"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-1.5">Current medications</label>
                        <textarea name="current_medications" rows="2" placeholder="List current medications"
                                  class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50/50 text-gray-900 placeholder-gray-400 text-sm shadow-inner focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 focus:bg-white transition-colors resize-y"><?php echo e($medsText); ?></textarea>
                        <?php $__errorArgs = ['current_medications'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1.5"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
            </div>

            <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3 pt-2">
                <a href="<?php echo e(route('patients.show', $patient)); ?>"
                   class="inline-flex justify-center px-6 py-3 text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="inline-flex justify-center items-center gap-2 px-6 py-3 text-sm font-semibold text-white rounded-xl shadow-md hover:shadow-lg transition-all"
                        style="background:linear-gradient(135deg,#1447E6,#0891B2);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Save changes
                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/patients/edit.blade.php ENDPATH**/ ?>