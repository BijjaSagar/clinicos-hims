<?php $__env->startSection('title', 'Referrals'); ?>
<?php $__env->startSection('breadcrumb', 'Referrals'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6 max-w-5xl mx-auto space-y-6">
    <h1 class="text-xl font-bold text-gray-900">Referral letters</h1>
    <p class="text-sm text-gray-500">Track referrals to other specialists or hospitals.</p>

    <?php if(session('success')): ?>
        <div class="rounded-lg bg-green-50 text-green-800 px-4 py-3 text-sm"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <?php if(isset($patients) && $patients->count() > 0): ?>
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="font-semibold text-gray-900 mb-4">New referral</h2>
        <form method="POST" action="<?php echo e(route('referrals.store')); ?>" class="space-y-4">
            <?php echo csrf_field(); ?>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Patient</label>
                <select name="patient_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <?php $__currentLoopData = $patients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($p->id); ?>"><?php echo e($p->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">To specialty</label>
                    <input type="text" name="to_specialty" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="e.g. Cardiology">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Urgency</label>
                    <select name="urgency" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <option value="routine">Routine</option>
                        <option value="urgent">Urgent</option>
                        <option value="emergency">Emergency</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Facility / doctor</label>
                <input type="text" name="to_facility_name" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm mb-2" placeholder="Hospital or clinic name">
                <input type="text" name="to_doctor_name" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Consultant name (optional)">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Reason</label>
                <textarea name="reason" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"></textarea>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Clinical summary</label>
                <textarea name="clinical_summary" rows="4" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"></textarea>
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">Save draft</button>
        </form>
    </div>
    <?php else: ?>
        <div class="rounded-lg bg-amber-50 text-amber-900 px-4 py-3 text-sm border border-amber-100">Add patients before creating referrals.</div>
    <?php endif; ?>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200 font-semibold text-gray-900">Recent</div>
        <div class="divide-y divide-gray-100">
            <?php $__empty_1 = true; $__currentLoopData = $referrals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ref): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="px-4 py-3 flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <div class="font-medium text-gray-900"><?php echo e($ref->patient->name ?? 'Patient'); ?></div>
                        <div class="text-xs text-gray-500"><?php echo e($ref->to_specialty ?? '—'); ?> · <?php echo e($ref->status); ?></div>
                    </div>
                    <form method="POST" action="<?php echo e(route('referrals.status', $ref)); ?>" class="flex items-center gap-2">
                        <?php echo csrf_field(); ?>
                        <select name="status" class="text-sm rounded border border-gray-300 px-2 py-1">
                            <option value="draft" <?php if($ref->status==='draft'): echo 'selected'; endif; ?>>Draft</option>
                            <option value="sent" <?php if($ref->status==='sent'): echo 'selected'; endif; ?>>Sent</option>
                            <option value="acknowledged" <?php if($ref->status==='acknowledged'): echo 'selected'; endif; ?>>Acknowledged</option>
                            <option value="completed" <?php if($ref->status==='completed'): echo 'selected'; endif; ?>>Completed</option>
                            <option value="cancelled" <?php if($ref->status==='cancelled'): echo 'selected'; endif; ?>>Cancelled</option>
                        </select>
                        <button type="submit" class="text-sm text-blue-600 font-medium">Update</button>
                    </form>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="px-4 py-8 text-center text-gray-500 text-sm">No referrals yet.</div>
            <?php endif; ?>
        </div>
        <?php if($referrals->hasPages()): ?>
            <div class="px-4 py-3 border-t border-gray-200"><?php echo e($referrals->links()); ?></div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/referrals/index.blade.php ENDPATH**/ ?>