<?php $__env->startSection('title', 'OPD Register'); ?>
<?php $__env->startSection('breadcrumb', 'OPD · Register'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-4 sm:p-5 lg:p-7 max-w-6xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900 font-display">OPD daily register</h1>
            <p class="text-sm text-gray-500 mt-0.5">Printable list with token order — same day filter as queue (Phase C).</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="<?php echo e(route('appointments.create', ['date' => $date])); ?>"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm">
                + New appointment
            </a>
            <form method="get" class="flex items-center gap-2">
                <input type="date" name="date" value="<?php echo e($date); ?>" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                <button type="submit" class="px-4 py-2 rounded-lg text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700">Go</button>
            </form>
            <a href="<?php echo e(route('opd.register.export', ['date' => $date])); ?>"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold border border-gray-200 bg-white hover:bg-gray-50">
                Export CSV
            </a>
            <a href="<?php echo e(route('opd.queue', ['date' => $date])); ?>"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white bg-slate-700 hover:bg-slate-800">
                Back to queue
            </a>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
        <div class="px-5 py-3 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-sm font-bold text-gray-900"><?php echo e(\Carbon\Carbon::parse($date)->format('l, d M Y')); ?></h2>
            <span class="text-xs text-gray-500"><?php echo e($appointments->count()); ?> rows</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        <th class="px-4 py-3">Token</th>
                        <th class="px-4 py-3">Time</th>
                        <th class="px-4 py-3">Patient</th>
                        <th class="px-4 py-3">Doctor</th>
                        <th class="px-4 py-3">Department</th>
                        <th class="px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__currentLoopData = $appointments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="px-4 py-3 font-mono font-bold text-indigo-700"><?php echo e($a->token_number ?? '—'); ?></td>
                        <td class="px-4 py-3 whitespace-nowrap"><?php echo e($a->scheduled_at ? $a->scheduled_at->format('h:i A') : '—'); ?></td>
                        <td class="px-4 py-3"><?php echo e($a->patient->name ?? '—'); ?> <span class="text-gray-400 text-xs"><?php echo e($a->patient->phone ?? ''); ?></span></td>
                        <td class="px-4 py-3"><?php echo e($a->doctor->name ?? '—'); ?></td>
                        <td class="px-4 py-3 max-w-[200px]">
                            <?php if(\Illuminate\Support\Facades\Schema::hasColumn('appointments', 'opd_department')): ?>
                            <form method="POST" action="<?php echo e(route('opd.department', $a)); ?>" class="flex gap-1">
                                <?php echo csrf_field(); ?>
                                <input type="text" name="opd_department" value="<?php echo e($a->opd_department); ?>" class="flex-1 min-w-0 text-xs border rounded px-2 py-1" placeholder="Dept">
                                <button type="submit" class="text-xs font-semibold text-indigo-600 px-2">OK</button>
                            </form>
                            <?php else: ?>
                            <span class="text-gray-400">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-xs"><?php echo e($a->status); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/opd/register.blade.php ENDPATH**/ ?>