<?php $__env->startSection('title', 'Lab Orders'); ?>
<?php $__env->startSection('breadcrumb', 'Lab Orders'); ?>

<?php $__env->startSection('content'); ?>
<div x-data="labOrders()" class="p-4 sm:p-5 lg:p-7 space-y-5">

    
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900 font-display">Lab Orders</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage in-house lab test orders and results</p>
        </div>
        <button @click="showNewOrderModal = true"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:shadow-lg hover:scale-[1.02]"
            style="background:linear-gradient(135deg,#1447E6,#0891B2);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            New Lab Order
        </button>
    </div>

    <?php if(request()->filled('patient_id')): ?>
    <div class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-900 flex flex-wrap items-center justify-between gap-2">
        <span>Showing lab orders for <strong>patient #<?php echo e((int) request('patient_id')); ?></strong>.</span>
        <a href="<?php echo e(route('laboratory.orders')); ?>" class="font-semibold underline hover:no-underline">Show all patients</a>
    </div>
    <?php endif; ?>

    <?php if(session('success')): ?>
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium" style="background:#ecfdf5;color:#059669;border:1px solid #a7f3d0;">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <?php echo e(session('success')); ?>

    </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium" style="background:#fef2f2;color:#b91c1c;border:1px solid #fecaca;">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <?php echo e(session('error')); ?>

    </div>
    <?php endif; ?>

    
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-5">
            <p class="text-xs font-medium text-gray-400 mb-1.5">Pending</p>
            <p class="font-display font-extrabold text-2xl sm:text-3xl leading-none" style="color:#d97706;"><?php echo e($stats['pending'] ?? 0); ?></p>
            <p class="text-xs text-gray-400 mt-2">Awaiting sample</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-5">
            <p class="text-xs font-medium text-gray-400 mb-1.5">In Progress</p>
            <p class="font-display font-extrabold text-2xl sm:text-3xl leading-none" style="color:#1447E6;"><?php echo e($stats['in_progress'] ?? 0); ?></p>
            <p class="text-xs text-gray-400 mt-2">Being processed</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-5">
            <p class="text-xs font-medium text-gray-400 mb-1.5">Completed Today</p>
            <p class="font-display font-extrabold text-2xl sm:text-3xl leading-none" style="color:#059669;"><?php echo e($stats['completed_today'] ?? 0); ?></p>
            <p class="text-xs text-gray-400 mt-2">Results ready</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-5">
            <p class="text-xs font-medium text-gray-400 mb-1.5">This Month</p>
            <p class="font-display font-extrabold text-2xl sm:text-3xl text-gray-900 leading-none"><?php echo e($stats['total_month'] ?? 0); ?></p>
            <p class="text-xs text-gray-400 mt-2">Total orders</p>
        </div>
    </div>

    
    <div class="bg-white border border-gray-200 rounded-xl p-4">
        <form method="GET" action="<?php echo e(route('laboratory.orders')); ?>" class="flex flex-col sm:flex-row gap-3">
            
            <select name="status" class="px-3 py-2 border border-gray-200 rounded-lg text-sm text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Status</option>
                <option value="pending"    <?php echo e(request('status') === 'pending'    ? 'selected' : ''); ?>>Pending</option>
                <option value="sample_collected" <?php echo e(request('status') === 'sample_collected' ? 'selected' : ''); ?>>Sample Collected</option>
                <option value="processing" <?php echo e(request('status') === 'processing' ? 'selected' : ''); ?>>Processing</option>
                <option value="completed"  <?php echo e(request('status') === 'completed'  ? 'selected' : ''); ?>>Completed</option>
                <option value="cancelled"  <?php echo e(request('status') === 'cancelled'  ? 'selected' : ''); ?>>Cancelled</option>
            </select>

            
            <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>"
                class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <input type="date" name="date_to" value="<?php echo e(request('date_to')); ?>"
                class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">

            
            <div class="flex-1 relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Search patient name, order no…"
                    class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <button type="submit" class="px-4 py-2 bg-gray-900 text-white text-sm font-semibold rounded-lg hover:bg-gray-700 transition-colors">
                Filter
            </button>
        </form>
    </div>

    
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Orders</h3>
            <span class="text-xs text-gray-400"><?php echo e($orders->total() ?? 0); ?> total</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Order #</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Accession</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Patient</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Tests</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Priority</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Ordered By</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $statusMap = [
                            'pending'          => ['label' => 'Pending',          'bg' => '#f1f5f9', 'color' => '#64748b'],
                            'sample_collected' => ['label' => 'Sample Collected', 'bg' => '#eff6ff', 'color' => '#1447e6'],
                            'processing'       => ['label' => 'Processing',       'bg' => '#fffbeb', 'color' => '#d97706'],
                            'completed'        => ['label' => 'Completed',        'bg' => '#ecfdf5', 'color' => '#059669'],
                            'cancelled'        => ['label' => 'Cancelled',        'bg' => '#fff1f2', 'color' => '#dc2626'],
                        ];
                        $s = $statusMap[$order->status] ?? $statusMap['pending'];

                        $priorityMap = [
                            'routine' => ['label' => 'Routine', 'bg' => '#f1f5f9', 'color' => '#64748b'],
                            'urgent'  => ['label' => 'Urgent',  'bg' => '#fffbeb', 'color' => '#d97706'],
                            'stat'    => ['label' => 'STAT',    'bg' => '#fff1f2', 'color' => '#dc2626'],
                        ];
                        $p = $priorityMap[$order->priority ?? 'routine'] ?? $priorityMap['routine'];
                    ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <span class="font-mono text-xs font-semibold text-teal-600"><?php echo e($order->order_number); ?></span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-mono text-xs text-gray-600"><?php echo e($order->accession_number ?? '—'); ?></span>
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-sm font-semibold text-gray-900"><?php echo e($order->patient_name); ?></p>
                            <p class="text-xs text-gray-400"><?php echo e($order->patient_phone ?? ''); ?></p>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            <?php echo e($order->tests_count ?? 0); ?> test(s)
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold"
                                  style="background:<?php echo e($s['bg']); ?>;color:<?php echo e($s['color']); ?>;"><?php echo e($s['label']); ?></span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold"
                                  style="background:<?php echo e($p['bg']); ?>;color:<?php echo e($p['color']); ?>;"><?php echo e($p['label']); ?></span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600"><?php echo e($order->ordered_by_name ?? '—'); ?></td>
                        <td class="px-4 py-3 text-sm text-gray-500">
                            <?php echo e($order->created_at ? \Carbon\Carbon::parse($order->created_at)->format('d M Y') : '—'); ?>

                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1 flex-wrap">
                                <?php if(in_array($order->status, ['pending', 'ordered', 'new', 'accepted'], true)): ?>
                                <form method="POST" action="<?php echo e(route('laboratory.orders.collect-sample', $order->id)); ?>" class="inline-flex items-center"
                                      onsubmit="return confirm('Record sample collection for this order?');">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="sample_type" value="blood">
                                    <button type="submit" class="p-1.5 rounded-lg text-gray-400 hover:text-amber-600 hover:bg-amber-50 transition-colors" title="Collect sample">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                        </svg>
                                    </button>
                                </form>
                                <?php endif; ?>
                                <?php if(in_array($order->status, ['pending', 'sample_collected', 'processing', 'ordered', 'new', 'accepted'], true)): ?>
                                <a href="<?php echo e(route('laboratory.result-entry', $order->id)); ?>"
                                   class="p-1.5 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors" title="Enter Results">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <?php endif; ?>
                                <?php if(in_array($order->status, ['completed', 'ready', 'sent'], true)): ?>
                                <a href="<?php echo e(route('laboratory.orders.report', $order->id)); ?>" class="p-1.5 rounded-lg text-gray-400 hover:text-green-600 hover:bg-green-50 transition-colors" title="View Report">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="9" class="px-4 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                                    <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-semibold text-gray-500">No lab orders found</p>
                                <button @click="showNewOrderModal = true" class="text-sm font-semibold" style="color:#1447E6;">Create first order →</button>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($orders->hasPages()): ?>
        <div class="px-5 py-4 border-t border-gray-100">
            <?php echo e($orders->withQueryString()->links()); ?>

        </div>
        <?php endif; ?>
    </div>

    
    <div x-show="showNewOrderModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showNewOrderModal = false"></div>
            <div class="relative bg-white rounded-2xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-base font-bold text-gray-900">New Lab Order</h3>
                    <button @click="showNewOrderModal = false" class="p-1.5 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form method="POST" action="<?php echo e(route('laboratory.orders.store')); ?>" class="p-6 space-y-5">
                    <?php echo csrf_field(); ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Patient <span class="text-red-500">*</span></label>
                            <select name="patient_id" required
                                class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Select patient…</option>
                                <?php $__currentLoopData = $patients ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $patient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($patient->id); ?>"><?php echo e($patient->name); ?><?php if($patient->phone): ?> — <?php echo e($patient->phone); ?><?php endif; ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                            <select name="priority"
                                class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="routine">Routine</option>
                                <option value="urgent">Urgent</option>
                                <option value="stat">STAT</option>
                            </select>
                        </div>
                    </div>

                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Tests <span class="text-red-500">*</span></label>

                        
                        <div class="relative mb-3">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" x-model="testSearch" placeholder="Search tests…"
                                class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="border border-gray-200 rounded-xl max-h-64 overflow-y-auto divide-y divide-gray-50">
                            <?php
                                $testsByCategory = ($tests ?? collect())->groupBy(fn ($t) => $t->department_name ?? $t->category ?? 'General');
                            ?>
                            <?php $__currentLoopData = $testsByCategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $categoryTests): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div>
                                <div class="px-4 py-2 bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php echo e($category); ?></div>
                                <?php $__currentLoopData = $categoryTests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $test): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $testLabel = $test->name ?? $test->test_name ?? 'Test';
                                    $tat = $test->turnaround_hours ?? $test->tat_hours ?? null;
                                    $sampleLabel = $test->sample_type ?? null;
                                ?>
                                <label class="flex items-center justify-between px-4 py-2.5 hover:bg-blue-50 cursor-pointer transition-colors"
                                    x-show="testSearch === '' || <?php echo e(json_encode(strtolower($testLabel))); ?>.includes(testSearch.toLowerCase())">
                                    <div class="flex items-center gap-3">
                                        <input type="checkbox" name="tests[]" value="<?php echo e($test->id); ?>"
                                            class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900"><?php echo e($testLabel); ?></p>
                                            <?php if($sampleLabel || $tat): ?>
                                            <p class="text-xs text-gray-400"><?php echo e($sampleLabel ?: '—'); ?><?php echo e($tat ? ' · '.$tat.'h TAT' : ''); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <span class="text-sm font-semibold text-teal-600">₹<?php echo e(number_format($test->price ?? 0)); ?></span>
                                </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            <?php if(($tests ?? collect())->isEmpty()): ?>
                            <div class="px-4 py-8 text-center text-sm text-gray-400">
                                No tests in catalog.
                                <a href="<?php echo e(route('laboratory.catalog')); ?>" class="font-semibold" style="color:#1447E6;">Add tests →</a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea name="notes" rows="2"
                            placeholder="Any special instructions or clinical notes…"
                            class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" @click="showNewOrderModal = false"
                            class="px-4 py-2 text-sm font-semibold text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50">Cancel</button>
                        <button type="submit"
                            class="px-5 py-2 text-sm font-semibold text-white rounded-xl" style="background:#1447E6;">
                            Create Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<?php $__env->startPush('scripts'); ?>
<script>
function labOrders() {
    const allTestIds = <?php echo json_encode($labOrderTestIds ?? [], 15, 512) ?>;
    console.log('[lab.orders] catalog test ids', allTestIds.length, allTestIds);
    return {
        showNewOrderModal: false,
        testSearch: '',
        allTestIds,
        filteredTests() {
            return this.allTestIds;
        }
    };
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/lab/orders.blade.php ENDPATH**/ ?>