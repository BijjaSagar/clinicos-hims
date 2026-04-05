<?php $__env->startSection('title', 'Analytics Dashboard'); ?>
<?php $__env->startSection('breadcrumb', 'Analytics'); ?>

<?php $__env->startSection('content'); ?>
<?php
    \Illuminate\Support\Facades\Log::info('analytics.dashboard.view', [
        'clinic_id' => auth()->user()->clinic_id ?? null,
        'revenue_days' => count($data['revenue']['daily'] ?? []),
        'diagnoses' => count($data['top_diagnoses'] ?? []),
    ]);
    $dailyRev = $data['revenue']['daily'] ?? [];
    $maxRev = 1;
    foreach ($dailyRev as $row) {
        $t = (float) data_get($row, 'total', 0);
        if ($t > $maxRev) {
            $maxRev = $t;
        }
    }
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
    <div class="mb-2">
        <h1 class="text-2xl font-bold text-gray-900 font-display tracking-tight">Analytics Dashboard</h1>
        <p class="text-sm text-gray-500 mt-1">Key metrics, revenue trend, and team performance for your clinic.</p>
    </div>

    <?php echo $__env->make('analytics.partials.subnav', ['active' => 'index'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <div class="relative overflow-hidden rounded-2xl shadow-sm text-white p-5 bg-gradient-to-br from-violet-600 via-purple-600 to-indigo-800 ring-1 ring-white/10">
            <div class="flex justify-between items-start gap-3">
                <span class="text-sm font-medium text-white/85">Total Patients</span>
                <svg class="w-9 h-9 text-white/35 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
            </div>
            <p class="mt-3 text-3xl font-extrabold font-display tabular-nums"><?php echo e(number_format($data['patients']['total'])); ?></p>
            <p class="mt-1 text-xs text-white/75">+<?php echo e($data['patients']['new_this_month']); ?> this month</p>
        </div>

        <div class="relative overflow-hidden rounded-2xl shadow-sm text-white p-5 bg-gradient-to-br from-fuchsia-500 via-pink-500 to-rose-600 ring-1 ring-white/10">
            <div class="flex justify-between items-start gap-3">
                <span class="text-sm font-medium text-white/85">Today&apos;s Appointments</span>
                <svg class="w-9 h-9 text-white/35 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5a2.25 2.25 0 002.25-2.25m-18 0v-7.5A2.25 2.25 0 017.5 9h9a2.25 2.25 0 012.25 2.25v7.5"/></svg>
            </div>
            <p class="mt-3 text-3xl font-extrabold font-display tabular-nums"><?php echo e($data['appointments']['today']); ?></p>
            <p class="mt-1 text-xs text-white/75"><?php echo e($data['appointments']['this_month']); ?> this month</p>
        </div>

        <div class="relative overflow-hidden rounded-2xl shadow-sm text-white p-5 bg-gradient-to-br from-sky-500 via-cyan-500 to-teal-600 ring-1 ring-white/10">
            <div class="flex justify-between items-start gap-3">
                <span class="text-sm font-medium text-white/85">Today&apos;s Revenue</span>
                <svg class="w-9 h-9 text-white/35 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="mt-3 text-3xl font-extrabold font-display tabular-nums">₹<?php echo e(number_format($data['revenue']['total_today'])); ?></p>
            <p class="mt-1 text-xs text-white/75">₹<?php echo e(number_format($data['revenue']['total_month'])); ?> this month</p>
        </div>

        <div class="relative overflow-hidden rounded-2xl shadow-sm text-white p-5 bg-gradient-to-br from-emerald-500 via-teal-500 to-cyan-600 ring-1 ring-white/10">
            <div class="flex justify-between items-start gap-3">
                <span class="text-sm font-medium text-white/85">Top Diagnoses</span>
                <svg class="w-9 h-9 text-white/35 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
            </div>
            <p class="mt-3 text-3xl font-extrabold font-display tabular-nums"><?php echo e(count($data['top_diagnoses'])); ?></p>
            <p class="mt-1 text-xs text-white/75">Unique this month</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <div class="lg:col-span-8 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between gap-3">
                <h2 class="text-base font-semibold text-gray-900">Revenue trend (last 30 days)</h2>
                <span class="text-xs text-gray-400 font-medium">Invoices</span>
            </div>
            <div class="p-5">
                <?php if(count($dailyRev) > 0): ?>
                    <div class="flex gap-0.5 sm:gap-1 h-52 px-1" role="img" aria-label="Daily revenue bars">
                        <?php $__currentLoopData = $dailyRev; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $dayTotal = (float) data_get($day, 'total', 0);
                                $h = $maxRev > 0 ? ($dayTotal / $maxRev) * 100 : 0;
                                $d = data_get($day, 'date');
                            ?>
                            <div class="flex-1 min-w-0 flex flex-col justify-end group">
                                <div
                                    class="w-full rounded-t-md bg-gradient-to-t from-indigo-600 to-violet-500 opacity-90 group-hover:opacity-100 transition-opacity min-h-[4px]"
                                    style="height: <?php echo e(max(4, $h)); ?>%"
                                    title="<?php echo e($d); ?>: ₹<?php echo e(number_format($dayTotal)); ?>"
                                ></div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <p class="text-center text-xs text-gray-400 mt-3">Hover bars for date and amount</p>
                <?php else: ?>
                    <div class="flex flex-col items-center justify-center py-14 text-center">
                        <svg class="w-12 h-12 text-gray-200 mb-3" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
                        <p class="text-sm font-medium text-gray-600">No revenue data yet</p>
                        <p class="text-xs text-gray-400 mt-1 max-w-sm">Recorded invoice totals will appear here for the last 30 days.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="lg:col-span-4 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden flex flex-col min-h-[280px]">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-900">Top diagnoses</h2>
                <p class="text-xs text-gray-500 mt-0.5">This month, by visit count</p>
            </div>
            <div class="divide-y divide-gray-100 flex-1 overflow-y-auto max-h-[320px]">
                <?php $__empty_1 = true; $__currentLoopData = $data['top_diagnoses']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="flex items-center justify-between gap-3 px-5 py-3 hover:bg-gray-50/80 transition-colors">
                        <span class="text-sm text-gray-800 truncate" title="<?php echo e(data_get($dx, 'diagnosis_text')); ?>"><?php echo e(data_get($dx, 'diagnosis_text')); ?></span>
                        <span class="shrink-0 inline-flex items-center justify-center min-w-[2rem] px-2 py-0.5 rounded-full text-xs font-bold bg-brand-blue/10 text-brand-blue tabular-nums"><?php echo e(data_get($dx, 'count')); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="flex flex-col items-center justify-center py-12 px-5 text-center">
                        <p class="text-sm text-gray-500">No diagnosis data</p>
                        <p class="text-xs text-gray-400 mt-1">Diagnoses from visits this month will list here.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-900">Doctor performance</h2>
                <p class="text-xs text-gray-500 mt-0.5">Appointments completed this month</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide bg-gray-50/80">
                            <th class="px-5 py-3">Doctor</th>
                            <th class="px-5 py-3 text-right">Patients seen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $__empty_1 = true; $__currentLoopData = $data['doctor_performance']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-gray-50/60">
                                <td class="px-5 py-3 text-gray-800"><?php echo e(data_get($doc, 'doctor')); ?></td>
                                <td class="px-5 py-3 text-right font-semibold text-gray-900 tabular-nums"><?php echo e(data_get($doc, 'patients_seen')); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="2" class="px-5 py-10 text-center text-sm text-gray-500">No data for this period</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-900">Patient gender distribution</h2>
                <p class="text-xs text-gray-500 mt-0.5">All registered patients</p>
            </div>
            <div class="p-5 space-y-4">
                <?php if(count($data['patients']['gender_dist']) > 0): ?>
                    <?php $__currentLoopData = $data['patients']['gender_dist']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gender => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $pct = $data['patients']['total'] > 0 ? ($count / $data['patients']['total']) * 100 : 0;
                            $barColor = $gender === 'male' ? 'bg-indigo-500' : ($gender === 'female' ? 'bg-rose-500' : 'bg-emerald-500');
                        ?>
                        <div>
                            <div class="flex justify-between text-sm mb-1.5">
                                <span class="font-medium text-gray-700"><?php echo e(ucfirst($gender ?? 'N/A')); ?></span>
                                <span class="text-gray-600 tabular-nums"><?php echo e($count); ?></span>
                            </div>
                            <div class="h-2.5 rounded-full bg-gray-100 overflow-hidden">
                                <div class="h-full rounded-full <?php echo e($barColor); ?> transition-all" style="width: <?php echo e($pct); ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <div class="flex flex-col items-center justify-center py-10 text-center">
                        <p class="text-sm text-gray-500">No demographic breakdown yet</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/analytics/dashboard.blade.php ENDPATH**/ ?>