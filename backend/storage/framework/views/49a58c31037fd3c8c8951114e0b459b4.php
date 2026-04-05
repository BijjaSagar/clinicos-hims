<?php $__env->startSection('title', 'Clinics'); ?>
<?php $__env->startSection('subtitle', 'Manage all clinics on the platform'); ?>

<?php $__env->startSection('header_actions'); ?>
<a href="<?php echo e(route('admin.clinics.create')); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-colors">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
    </svg>
    Add Clinic
</a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="grid grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-4 border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900"><?php echo e($stats['total']); ?></p>
                    <p class="text-xs text-gray-500">Total Clinics</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900"><?php echo e($stats['active']); ?></p>
                    <p class="text-xs text-gray-500">Active</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900"><?php echo e($stats['trial']); ?></p>
                    <p class="text-xs text-gray-500">On Trial</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900"><?php echo e($stats['paid']); ?></p>
                    <p class="text-xs text-gray-500">Paid Plans</p>
                </div>
            </div>
        </div>
    </div>

    
    <div class="bg-white rounded-xl p-4 border border-gray-100">
        <form action="<?php echo e(route('admin.clinics.index')); ?>" method="GET" class="flex items-center gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" 
                    placeholder="Search clinics by name, email, phone, city..."
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <select name="plan" class="px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500">
                <option value="">All Plans</option>
                <option value="trial" <?php echo e(request('plan') === 'trial' ? 'selected' : ''); ?>>Trial</option>
                <option value="solo" <?php echo e(request('plan') === 'solo' ? 'selected' : ''); ?>>Solo</option>
                <option value="small" <?php echo e(request('plan') === 'small' ? 'selected' : ''); ?>>Small</option>
                <option value="group" <?php echo e(request('plan') === 'group' ? 'selected' : ''); ?>>Group</option>
                <option value="enterprise" <?php echo e(request('plan') === 'enterprise' ? 'selected' : ''); ?>>Enterprise</option>
            </select>
            <select name="status" class="px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500">
                <option value="">All Status</option>
                <option value="active" <?php echo e(request('status') === 'active' ? 'selected' : ''); ?>>Active</option>
                <option value="inactive" <?php echo e(request('status') === 'inactive' ? 'selected' : ''); ?>>Inactive</option>
                <option value="trial" <?php echo e(request('status') === 'trial' ? 'selected' : ''); ?>>On Trial</option>
                <option value="expired" <?php echo e(request('status') === 'expired' ? 'selected' : ''); ?>>Trial Expired</option>
            </select>
            <button type="submit" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors">
                Filter
            </button>
            <?php if(request()->hasAny(['search', 'plan', 'status'])): ?>
            <a href="<?php echo e(route('admin.clinics.index')); ?>" class="px-4 py-2.5 text-gray-500 hover:text-gray-700">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Clinic</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Owner</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Plan</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Stats</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php $__empty_1 = true; $__currentLoopData = $clinics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $clinic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                                <span class="text-indigo-600 font-semibold"><?php echo e(substr($clinic->name, 0, 1)); ?></span>
                            </div>
                            <div>
                                <a href="<?php echo e(route('admin.clinics.show', $clinic)); ?>" class="font-medium text-gray-900 hover:text-indigo-600">
                                    <?php echo e($clinic->name); ?>

                                </a>
                                <p class="text-xs text-gray-500"><?php echo e($clinic->city ?? 'N/A'); ?>, <?php echo e($clinic->state ?? ''); ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <?php if($clinic->owner): ?>
                        <p class="text-sm text-gray-900"><?php echo e($clinic->owner->name); ?></p>
                        <p class="text-xs text-gray-500"><?php echo e($clinic->owner->email); ?></p>
                        <?php else: ?>
                        <span class="text-sm text-gray-400">No owner</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                            <?php if($clinic->plan === 'trial'): ?> bg-amber-100 text-amber-700
                            <?php elseif($clinic->plan === 'solo'): ?> bg-blue-100 text-blue-700
                            <?php elseif($clinic->plan === 'small'): ?> bg-green-100 text-green-700
                            <?php elseif($clinic->plan === 'group'): ?> bg-purple-100 text-purple-700
                            <?php else: ?> bg-indigo-100 text-indigo-700 <?php endif; ?>
                        "><?php echo e(ucfirst($clinic->plan)); ?></span>
                        <?php if($clinic->trial_ends_at && $clinic->plan === 'trial'): ?>
                        <p class="text-xs text-gray-500 mt-1">
                            <?php if($clinic->trial_ends_at->isPast()): ?>
                                <span class="text-red-500">Expired</span>
                            <?php else: ?>
                                Ends <?php echo e($clinic->trial_ends_at->diffForHumans()); ?>

                            <?php endif; ?>
                        </p>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4 text-xs text-gray-500">
                            <span title="Patients">👥 <?php echo e($clinic->patients_count ?? 0); ?></span>
                            <span title="Appointments">📅 <?php echo e($clinic->appointments_count ?? 0); ?></span>
                            <span title="Invoices">🧾 <?php echo e($clinic->invoices_count ?? 0); ?></span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <?php if($clinic->is_active): ?>
                        <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                            Active
                        </span>
                        <?php else: ?>
                        <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">
                            <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                            Inactive
                        </span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="<?php echo e(route('admin.clinics.show', $clinic)); ?>" class="p-2 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg" title="View">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </a>
                            <a href="<?php echo e(route('admin.clinics.edit', $clinic)); ?>" class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                </svg>
                            </a>
                            <form action="<?php echo e(route('admin.clinics.impersonate', $clinic)); ?>" method="POST" class="inline">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="p-2 text-gray-500 hover:text-purple-600 hover:bg-purple-50 rounded-lg" title="Login as Owner">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/>
                        </svg>
                        <p class="text-gray-500">No clinics found</p>
                        <a href="<?php echo e(route('admin.clinics.create')); ?>" class="inline-flex items-center gap-2 mt-4 text-indigo-600 hover:text-indigo-700 font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                            </svg>
                            Create your first clinic
                        </a>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if($clinics->hasPages()): ?>
        <div class="px-6 py-4 border-t border-gray-100">
            <?php echo e($clinics->withQueryString()->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/admin/clinics/index.blade.php ENDPATH**/ ?>