<?php $__env->startSection('title', 'My Lab Results'); ?>

<?php $__env->startSection('content'); ?>

    
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">My Lab Results</h1>
            <p class="text-sm text-gray-500 mt-1">Completed lab orders for your patients</p>
        </div>
    </div>

    
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <?php if($completedOrders->isEmpty()): ?>
            <div class="text-center py-16 text-gray-400">
                <svg class="mx-auto h-12 w-12 mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="font-medium text-gray-500">No completed lab results yet</p>
                <p class="text-sm mt-1">Results will appear here once your lab orders are processed.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tests</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completed At</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        <?php $__currentLoopData = $completedOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $fk = $labOrderItemFk ?? null;
                                $testCount = 0;
                                if ($fk) {
                                    $testCount = \Illuminate\Support\Facades\DB::table('lab_order_items')
                                        ->where($fk, $order->id)->count();
                                }
                                if ($testCount === 0 && !empty($order->tests)) {
                                    $decoded = is_string($order->tests) ? json_decode($order->tests, true) : $order->tests;
                                    $testCount = is_array($decoded) ? count($decoded) : 0;
                                }
                                $abnormalCount = 0;
                                if (!empty($labOrderItemsHasAbnormal) && $fk) {
                                    $abnormalCount = \Illuminate\Support\Facades\DB::table('lab_order_items')
                                        ->where($fk, $order->id)
                                        ->where('is_abnormal', true)->count();
                                }
                            ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-sm font-mono font-medium text-gray-900">
                                    <?php echo e($order->order_number); ?>

                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-sm font-medium text-gray-900"><?php echo e($order->patient_name); ?></p>
                                    <?php if($order->phone): ?>
                                        <p class="text-xs text-gray-500"><?php echo e($order->phone); ?></p>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 font-semibold text-xs">
                                            <?php echo e($testCount); ?>

                                        </span>
                                        <?php if($abnormalCount > 0): ?>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700">
                                                <?php echo e($abnormalCount); ?> abnormal
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    <?php if($order->completed_at): ?>
                                        <?php echo e(\Carbon\Carbon::parse($order->completed_at)->format('d M Y, H:i')); ?>

                                    <?php else: ?>
                                        —
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <a
                                        href="<?php echo e(route('lab.technician.report', $order->id)); ?>"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition-colors"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        View Report
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            
            <?php if($completedOrders->hasPages()): ?>
                <div class="px-4 py-4 border-t border-gray-100">
                    <?php echo e($completedOrders->links()); ?>

                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/lab/technician/doctor-results.blade.php ENDPATH**/ ?>