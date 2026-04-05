<?php $__env->startSection('title', $clinic->name); ?>
<?php $__env->startSection('subtitle', 'Clinic Details'); ?>

<?php $__env->startSection('header_actions'); ?>
<div class="flex items-center gap-3">
    <form action="<?php echo e(route('admin.clinics.toggle-status', $clinic)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <button type="submit" class="px-4 py-2.5 <?php echo e($clinic->is_active ? 'bg-red-50 text-red-600 hover:bg-red-100' : 'bg-green-50 text-green-600 hover:bg-green-100'); ?> text-sm font-medium rounded-xl transition-colors">
            <?php echo e($clinic->is_active ? 'Deactivate' : 'Activate'); ?>

        </button>
    </form>
    <a href="<?php echo e(route('admin.clinics.edit', $clinic)); ?>" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition-colors">
        Edit Clinic
    </a>
    <form action="<?php echo e(route('admin.clinics.impersonate', $clinic)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <button type="submit" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-colors">
            Login as Owner
        </button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="grid grid-cols-3 gap-6">
        <div class="col-span-2 bg-white rounded-xl p-6 border border-gray-100">
            <div class="flex items-start justify-between mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-indigo-100 rounded-xl flex items-center justify-center">
                        <span class="text-indigo-600 font-bold text-2xl"><?php echo e(substr($clinic->name, 0, 1)); ?></span>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900"><?php echo e($clinic->name); ?></h3>
                        <p class="text-sm text-gray-500"><?php echo e($clinic->city ?? 'N/A'); ?>, <?php echo e($clinic->state ?? 'N/A'); ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <?php if($clinic->is_active): ?>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-full bg-green-100 text-green-700">
                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                        Active
                    </span>
                    <?php else: ?>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-full bg-red-100 text-red-700">
                        <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                        Inactive
                    </span>
                    <?php endif; ?>
                    <span class="inline-flex px-3 py-1.5 text-sm font-medium rounded-full
                        <?php if($clinic->plan === 'trial'): ?> bg-amber-100 text-amber-700
                        <?php elseif($clinic->plan === 'solo'): ?> bg-blue-100 text-blue-700
                        <?php elseif($clinic->plan === 'small'): ?> bg-green-100 text-green-700
                        <?php elseif($clinic->plan === 'group'): ?> bg-purple-100 text-purple-700
                        <?php else: ?> bg-indigo-100 text-indigo-700 <?php endif; ?>
                    "><?php echo e(ucfirst($clinic->plan)); ?> Plan</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Email</p>
                    <p class="text-sm text-gray-900"><?php echo e($clinic->email ?? 'Not set'); ?></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Phone</p>
                    <p class="text-sm text-gray-900"><?php echo e($clinic->phone ?? 'Not set'); ?></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">GSTIN</p>
                    <p class="text-sm text-gray-900"><?php echo e($clinic->gstin ?? 'Not set'); ?></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Specialties</p>
                    <p class="text-sm text-gray-900">
                        <?php if(is_array($clinic->specialties)): ?>
                            <?php echo e(implode(', ', $clinic->specialties)); ?>

                        <?php else: ?>
                            <?php echo e($clinic->specialties ?? 'Not set'); ?>

                        <?php endif; ?>
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Created</p>
                    <p class="text-sm text-gray-900"><?php echo e($clinic->created_at->format('d M Y, h:i A')); ?></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Trial Ends</p>
                    <?php if($clinic->trial_ends_at): ?>
                    <p class="text-sm <?php echo e($clinic->trial_ends_at->isPast() ? 'text-red-600' : 'text-gray-900'); ?>">
                        <?php echo e($clinic->trial_ends_at->format('d M Y')); ?>

                        (<?php echo e($clinic->trial_ends_at->diffForHumans()); ?>)
                    </p>
                    <?php else: ?>
                    <p class="text-sm text-gray-500">N/A (Paid plan)</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        
        <div class="bg-white rounded-xl p-6 border border-gray-100">
            <h4 class="font-semibold text-gray-900 mb-4">Owner</h4>
            <?php if($clinic->owner): ?>
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                    <span class="text-indigo-600 font-semibold"><?php echo e(substr($clinic->owner->name, 0, 1)); ?></span>
                </div>
                <div>
                    <p class="font-medium text-gray-900"><?php echo e($clinic->owner->name); ?></p>
                    <p class="text-sm text-gray-500"><?php echo e($clinic->owner->role); ?></p>
                </div>
            </div>
            <div class="space-y-2">
                <div class="flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                    </svg>
                    <span class="text-gray-600"><?php echo e($clinic->owner->email); ?></span>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/>
                    </svg>
                    <span class="text-gray-600"><?php echo e($clinic->owner->phone ?? 'Not set'); ?></span>
                </div>
            </div>
            <?php else: ?>
            <p class="text-gray-500 text-sm">No owner assigned</p>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="grid grid-cols-5 gap-4">
        <div class="bg-white rounded-xl p-4 border border-gray-100 text-center">
            <p class="text-2xl font-bold text-gray-900"><?php echo e(number_format($stats['total_patients'])); ?></p>
            <p class="text-xs text-gray-500">Patients</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100 text-center">
            <p class="text-2xl font-bold text-gray-900"><?php echo e(number_format($stats['total_appointments'])); ?></p>
            <p class="text-xs text-gray-500">Appointments</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100 text-center">
            <p class="text-2xl font-bold text-gray-900"><?php echo e(number_format($stats['total_invoices'])); ?></p>
            <p class="text-xs text-gray-500">Invoices</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100 text-center">
            <p class="text-2xl font-bold text-gray-900">₹<?php echo e(number_format($stats['total_revenue'])); ?></p>
            <p class="text-xs text-gray-500">Total Revenue</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100 text-center">
            <p class="text-2xl font-bold text-gray-900"><?php echo e($stats['staff_count']); ?></p>
            <p class="text-xs text-gray-500">Staff</p>
        </div>
    </div>

    
    <?php if($clinic->plan === 'trial'): ?>
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-medium text-amber-800">Trial Period</p>
                    <p class="text-sm text-amber-600">
                        <?php if($clinic->trial_ends_at->isPast()): ?>
                            Expired <?php echo e($clinic->trial_ends_at->diffForHumans()); ?>

                        <?php else: ?>
                            Ends <?php echo e($clinic->trial_ends_at->format('d M Y')); ?> (<?php echo e($clinic->trial_ends_at->diffForHumans()); ?>)
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            <form action="<?php echo e(route('admin.clinics.extend-trial', $clinic)); ?>" method="POST" class="flex items-center gap-2">
                <?php echo csrf_field(); ?>
                <input type="number" name="days" value="15" min="1" max="365" class="w-20 px-3 py-2 rounded-lg border border-amber-300 text-sm">
                <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-lg">
                    Extend Days
                </button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-2 gap-6">
        
        <div class="bg-white rounded-xl border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h4 class="font-semibold text-gray-900">Recent Patients</h4>
            </div>
            <div class="divide-y divide-gray-100">
                <?php $__empty_1 = true; $__currentLoopData = $recentPatients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $patient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="px-6 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-xs font-medium text-gray-600">
                            <?php echo e(substr($patient->name, 0, 1)); ?>

                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900"><?php echo e($patient->name); ?></p>
                            <p class="text-xs text-gray-500"><?php echo e($patient->phone); ?></p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-500"><?php echo e($patient->created_at->diffForHumans()); ?></span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="px-6 py-8 text-center text-gray-500 text-sm">
                    No patients yet
                </div>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="bg-white rounded-xl border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h4 class="font-semibold text-gray-900">Recent Invoices</h4>
            </div>
            <div class="divide-y divide-gray-100">
                <?php $__empty_1 = true; $__currentLoopData = $recentInvoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="px-6 py-3 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-900"><?php echo e($invoice->invoice_number ?? '#' . $invoice->id); ?></p>
                        <p class="text-xs text-gray-500"><?php echo e($invoice->patient?->name ?? 'N/A'); ?></p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-900">₹<?php echo e(number_format($invoice->total)); ?></p>
                        <span class="text-xs px-2 py-0.5 rounded-full
                            <?php if($invoice->payment_status === 'paid'): ?> bg-green-100 text-green-700
                            <?php elseif($invoice->payment_status === 'partial'): ?> bg-amber-100 text-amber-700
                            <?php else: ?> bg-red-100 text-red-700 <?php endif; ?>
                        "><?php echo e(ucfirst($invoice->payment_status)); ?></span>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="px-6 py-8 text-center text-gray-500 text-sm">
                    No invoices yet
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <div class="bg-white rounded-xl border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h4 class="font-semibold text-gray-900">Staff Members</h4>
            <span class="text-sm text-gray-500"><?php echo e($clinic->users->count()); ?> members</span>
        </div>
        <div class="divide-y divide-gray-100">
            <?php $__empty_1 = true; $__currentLoopData = $clinic->users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="px-6 py-3 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-indigo-100 rounded-full flex items-center justify-center text-sm font-medium text-indigo-600">
                        <?php echo e(substr($user->name, 0, 1)); ?>

                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900"><?php echo e($user->name); ?></p>
                        <p class="text-xs text-gray-500"><?php echo e($user->email); ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-600"><?php echo e(ucfirst($user->role)); ?></span>
                    <?php if($user->is_active): ?>
                    <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                    <?php else: ?>
                    <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="px-6 py-8 text-center text-gray-500 text-sm">
                No staff members
            </div>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="bg-red-50 border border-red-200 rounded-xl p-6">
        <h4 class="font-semibold text-red-800 mb-2">Danger Zone</h4>
        <p class="text-sm text-red-600 mb-4">Deleting this clinic will remove all associated data including patients, appointments, and invoices. This action cannot be undone.</p>
        <form action="<?php echo e(route('admin.clinics.destroy', $clinic)); ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this clinic? This action cannot be undone.')">
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>
            <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg">
                Delete Clinic
            </button>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/admin/clinics/show.blade.php ENDPATH**/ ?>