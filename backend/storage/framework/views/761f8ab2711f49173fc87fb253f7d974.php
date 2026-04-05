<?php $__env->startSection('title', 'Emergency'); ?>
<?php $__env->startSection('breadcrumb', 'Emergency'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-4 sm:p-5 lg:p-7 max-w-6xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900 font-display">Emergency / Casualty</h1>
            <p class="text-sm text-gray-500 mt-0.5">Register visits, triage, and track bay assignment (Phase C).</p>
        </div>
    </div>

    <?php if(session('success')): ?>
    <div class="px-4 py-3 rounded-xl text-sm font-medium" style="background:#ecfdf5;color:#059669;border:1px solid #a7f3d0;"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
    <div class="px-4 py-3 rounded-xl text-sm font-medium bg-red-50 text-red-800 border border-red-200"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
        <h2 class="text-sm font-bold text-gray-900 mb-4">Register new visit</h2>
        <form method="POST" action="<?php echo e(route('emergency.store')); ?>" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php echo csrf_field(); ?>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-600 mb-1">Existing patient (optional)</label>
                <select name="patient_id" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white">
                    <option value="">— Walk-in / unknown —</option>
                    <?php $__currentLoopData = $patients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($p->id); ?>"><?php echo e($p->name); ?> · <?php echo e($p->phone); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Patient name <span class="text-red-500">*</span> (if no patient selected)</label>
                <input type="text" name="patient_name" value="<?php echo e(old('patient_name')); ?>" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="Name">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Phone</label>
                <input type="text" name="phone" value="<?php echo e(old('phone')); ?>" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-600 mb-1">Chief complaint</label>
                <input type="text" name="chief_complaint" value="<?php echo e(old('chief_complaint')); ?>" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Triage (1–5)</label>
                <select name="triage_level" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white">
                    <option value="">—</option>
                    <?php for($i = 1; $i <= 5; $i++): ?>
                        <option value="<?php echo e($i); ?>" <?php if((string)old('triage_level') === (string)$i): echo 'selected'; endif; ?>><?php echo e($i); ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Bay / bed</label>
                <input type="text" name="bay_number" value="<?php echo e(old('bay_number')); ?>" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="ER-1">
            </div>
            <div class="md:col-span-2">
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-red-600 hover:bg-red-700">Register visit</button>
            </div>
        </form>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
        <div class="px-5 py-3 border-b border-gray-100">
            <h2 class="text-sm font-bold text-gray-900">Recent visits</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        <th class="px-4 py-3">Time</th>
                        <th class="px-4 py-3">Patient</th>
                        <th class="px-4 py-3">Triage</th>
                        <th class="px-4 py-3">Bay</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Triage update</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $visits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50/80">
                        <td class="px-4 py-3 whitespace-nowrap text-gray-600"><?php echo e($v->registered_at?->format('d M H:i')); ?></td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900"><?php echo e($v->patient_name ?? $v->patient?->name ?? '—'); ?></div>
                            <div class="text-xs text-gray-400"><?php echo e($v->phone); ?></div>
                        </td>
                        <td class="px-4 py-3"><?php echo e($v->triage_level ?? '—'); ?></td>
                        <td class="px-4 py-3"><?php echo e($v->bay_number ?? '—'); ?></td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700"><?php echo e($v->status); ?></span>
                        </td>
                        <td class="px-4 py-3">
                            <form method="POST" action="<?php echo e(route('emergency.triage', $v)); ?>" class="flex flex-wrap items-center gap-2">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PATCH'); ?>
                                <select name="triage_level" class="text-xs border rounded px-2 py-1 bg-white">
                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                        <option value="<?php echo e($i); ?>" <?php if($v->triage_level == $i): echo 'selected'; endif; ?>><?php echo e($i); ?></option>
                                    <?php endfor; ?>
                                </select>
                                <input type="text" name="bay_number" value="<?php echo e($v->bay_number); ?>" placeholder="Bay" class="text-xs border rounded px-2 py-1 w-24">
                                <select name="status" class="text-xs border rounded px-2 py-1 bg-white max-w-[140px]">
                                    <?php $__currentLoopData = ['registered','triaged','in_treatment','discharged','admitted','left_ama']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($st); ?>" <?php if($v->status === $st): echo 'selected'; endif; ?>><?php echo e($st); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <button type="submit" class="text-xs font-semibold text-white bg-gray-800 px-2 py-1 rounded">Save</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="6" class="px-4 py-10 text-center text-gray-400">No emergency visits yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/emergency/index.blade.php ENDPATH**/ ?>