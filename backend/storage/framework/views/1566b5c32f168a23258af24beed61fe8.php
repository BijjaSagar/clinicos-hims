<?php $__env->startSection('title', 'Pharmacy'); ?>
<?php $__env->startSection('breadcrumb', 'Pharmacy'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-4 sm:p-5 lg:p-7 space-y-5">

    
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900 font-display">Pharmacy</h1>
            <p class="text-sm text-gray-500 mt-0.5">Medicine dispensing, inventory & stock management</p>
        </div>
        <div class="flex flex-wrap gap-2 text-xs font-semibold">
            <?php if(\Illuminate\Support\Facades\Route::has('opd.queue')): ?>
                <a href="<?php echo e(route('opd.queue')); ?>" class="px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-800 border border-indigo-100 hover:bg-indigo-100">OPD Queue</a>
            <?php endif; ?>
            <?php if(\Illuminate\Support\Facades\Route::has('ipd.index')): ?>
                <a href="<?php echo e(route('ipd.index')); ?>" class="px-3 py-1.5 rounded-lg bg-slate-50 text-slate-800 border border-slate-200 hover:bg-slate-100">IPD</a>
            <?php endif; ?>
            <?php if(\Illuminate\Support\Facades\Route::has('laboratory.index')): ?>
                <a href="<?php echo e(route('laboratory.index')); ?>" class="px-3 py-1.5 rounded-lg bg-teal-50 text-teal-900 border border-teal-100 hover:bg-teal-100">Laboratory</a>
            <?php endif; ?>
            <?php if(\Illuminate\Support\Facades\Route::has('billing.index')): ?>
                <a href="<?php echo e(route('billing.index')); ?>" class="px-3 py-1.5 rounded-lg bg-blue-50 text-blue-900 border border-blue-100 hover:bg-blue-100">Billing</a>
            <?php endif; ?>
        </div>
    </div>

    <?php if(session('success')): ?>
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium" style="background:#ecfdf5;color:#059669;border:1px solid #a7f3d0;">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <?php echo e(session('success')); ?>

    </div>
    <?php endif; ?>

    
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-5">
            <p class="text-xs font-medium text-gray-400 mb-1.5">Total Medicines</p>
            <p class="font-display font-extrabold text-2xl sm:text-3xl text-gray-900 leading-none"><?php echo e($stats['total_medicines'] ?? 0); ?></p>
            <p class="text-xs text-gray-400 mt-2">In catalog</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-5">
            <p class="text-xs font-medium text-gray-400 mb-1.5">Low Stock Alerts</p>
            <p class="font-display font-extrabold text-2xl sm:text-3xl leading-none" style="color:#d97706;"><?php echo e($stats['low_stock_count'] ?? 0); ?></p>
            <p class="text-xs text-gray-400 mt-2">Below reorder level</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-5">
            <p class="text-xs font-medium text-gray-400 mb-1.5">Today's Dispensing</p>
            <p class="font-display font-extrabold text-2xl sm:text-3xl leading-none" style="color:#1447E6;"><?php echo e($stats['dispensed_today'] ?? 0); ?></p>
            <p class="text-xs text-gray-400 mt-2">Patients served</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-5">
            <p class="text-xs font-medium text-gray-400 mb-1.5">Monthly Revenue</p>
            <p class="font-display font-extrabold text-xl sm:text-3xl leading-none" style="color:#059669;">₹<?php echo e($stats['monthly_revenue'] ?? '0'); ?></p>
            <p class="text-xs text-gray-400 mt-2">This month</p>
        </div>
    </div>

    
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <?php
        $quickActions = [
            ['label' => 'Dispense Medicine', 'href' => route('pharmacy.dispense.form'),  'icon' => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z', 'gradient' => '#1447E6,#0891B2'],
            // Add Stock → same inventory page but opens "Add New Medicine" modal (catalog search), not GRN/supplier flow
            ['label' => 'Add Stock',         'href' => url('/pharmacy/inventory').'?add_medicine=1', 'icon' => 'M12 4v16m8-8H4', 'gradient' => '#059669,#0d9488'],
            ['label' => 'View Inventory',    'href' => route('pharmacy.inventory'),   'icon' => 'M4 6h16M4 10h16M4 14h16M4 18h16', 'gradient' => '#7c3aed,#a855f7'],
            ['label' => 'Reports',           'href' => route('pharmacy.reports'),    'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'gradient' => '#d97706,#ef4444'],
            ['label' => 'Expiry alerts',     'href' => route('pharmacy.expiry-alerts'), 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'gradient' => '#ea580c,#f97316'],
            ['label' => 'Returns / adjust',  'href' => route('pharmacy.returns.form'), 'icon' => 'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6', 'gradient' => '#64748b,#475569'],
        ];
        ?>
        <?php $__currentLoopData = $quickActions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e($action['href']); ?>"
           class="flex flex-col items-center gap-3 p-4 bg-white border border-gray-200 rounded-xl hover:shadow-md transition-all hover:border-gray-300 text-center group">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center text-white"
                 style="background:linear-gradient(135deg,<?php echo e($action['gradient']); ?>);">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="<?php echo e($action['icon']); ?>"/>
                </svg>
            </div>
            <span class="text-xs font-semibold text-gray-700 group-hover:text-gray-900 leading-tight"><?php echo e($action['label']); ?></span>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-900">Recent Dispensing</h3>
                <a href="<?php echo e(route('pharmacy.history')); ?>" class="text-xs font-semibold" style="color:#1447E6;">View all →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Patient</th>
                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Items</th>
                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Amount</th>
                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php $__empty_1 = true; $__currentLoopData = $recentDispensing ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dispensing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <p class="text-sm font-semibold text-gray-900"><?php echo e($dispensing->patient_name ?? '—'); ?></p>
                                <p class="text-xs text-gray-400"><?php echo e($dispensing->dispensing_number ?? ''); ?></p>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700"><?php echo e($dispensing->items_count ?? 0); ?> items</td>
                            <td class="px-4 py-3 text-sm font-semibold text-gray-900">₹<?php echo e(number_format($dispensing->total_amount ?? 0)); ?></td>
                            <td class="px-4 py-3 text-xs text-gray-400">
                                <?php echo e($dispensing->created_at ? \Carbon\Carbon::parse($dispensing->created_at)->format('H:i') : '—'); ?>

                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-sm text-gray-400">
                                No dispensing records today.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-900">Low Stock Alerts</h3>
                <a href="<?php echo e(route('pharmacy.inventory')); ?>?stock_status=low-stock" class="text-xs font-semibold" style="color:#1447E6;">View all →</a>
            </div>
            <div class="p-4 space-y-2">
                <?php $__empty_1 = true; $__currentLoopData = $lowStockItems ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="flex items-center gap-3 p-3 rounded-xl" style="background:#fffbeb;border:1px solid #fde68a;">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" style="background:#fef3c7;">
                        <svg class="w-4 h-4" style="color:#d97706;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate"><?php echo e($item->name ?? '—'); ?></p>
                        <p class="text-xs text-gray-500">Current: <?php echo e($item->current_stock ?? 0); ?> <?php echo e($item->unit ?? ''); ?> | Reorder: <?php echo e($item->reorder_level ?? 0); ?></p>
                    </div>
                    <a href="<?php echo e(route('pharmacy.inventory')); ?>" class="flex-shrink-0 text-xs font-semibold px-2.5 py-1 rounded-lg" style="background:#fde68a;color:#92400e;">
                        Restock
                    </a>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="flex flex-col items-center gap-2 py-8 text-center">
                    <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-600">All stock levels are adequate</p>
                    <p class="text-xs text-gray-400">No items below reorder level</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/pharmacy/index.blade.php ENDPATH**/ ?>