<?php $__env->startSection('title', 'Payments'); ?>
<?php $__env->startSection('breadcrumb', 'Payments'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6 space-y-6">
    
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Payments</h1>
            <p class="text-sm text-gray-500 mt-0.5">Track and manage all payment transactions</p>
        </div>
        <a href="<?php echo e(route('billing.create')); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            New Invoice
        </a>
    </div>

    
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 font-medium">Today's Collection</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">₹<?php echo e(number_format($stats['today'] ?? 0)); ?></p>
            <p class="text-xs text-gray-400 mt-1"><?php echo e($stats['today_count'] ?? 0); ?> payments</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 font-medium">This Week</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">₹<?php echo e(number_format($stats['week'] ?? 0)); ?></p>
            <p class="text-xs text-gray-400 mt-1"><?php echo e($stats['week_count'] ?? 0); ?> payments</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 font-medium">This Month</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">₹<?php echo e(number_format($stats['month'] ?? 0)); ?></p>
            <p class="text-xs text-gray-400 mt-1"><?php echo e($stats['month_count'] ?? 0); ?> payments</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 font-medium">Pending Collection</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">₹<?php echo e(number_format($stats['pending'] ?? 0)); ?></p>
            <p class="text-xs text-gray-400 mt-1"><?php echo e($stats['pending_count'] ?? 0); ?> invoices</p>
        </div>
    </div>

    
    <?php if(isset($methodBreakdown) && $methodBreakdown->count() > 0): ?>
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Payment Methods (This Month)</h3>
        <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
            <?php
                $methodIcons = [
                    'upi' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-600', 'label' => 'UPI'],
                    'card' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-600', 'label' => 'Card'],
                    'cash' => ['bg' => 'bg-green-100', 'text' => 'text-green-600', 'label' => 'Cash'],
                    'netbanking' => ['bg' => 'bg-teal-100', 'text' => 'text-teal-600', 'label' => 'Net Banking'],
                    'wallet' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-600', 'label' => 'Wallet'],
                    'insurance' => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-600', 'label' => 'Insurance'],
                ];
            ?>
            <?php $__currentLoopData = $methodBreakdown; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $methodInfo = $methodIcons[$method] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'label' => ucfirst($method)]; ?>
            <div class="flex items-center gap-3 p-3 <?php echo e($methodInfo['bg']); ?> rounded-lg">
                <div>
                    <p class="text-xs <?php echo e($methodInfo['text']); ?> font-medium"><?php echo e($methodInfo['label']); ?></p>
                    <p class="text-lg font-bold text-gray-900">₹<?php echo e(number_format($data->total)); ?></p>
                    <p class="text-xs text-gray-500"><?php echo e($data->count); ?> txns</p>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>

    
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET" class="flex flex-wrap items-center gap-4">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Payment Method</label>
                <select name="method" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Methods</option>
                    <option value="upi" <?php echo e(request('method') === 'upi' ? 'selected' : ''); ?>>UPI</option>
                    <option value="card" <?php echo e(request('method') === 'card' ? 'selected' : ''); ?>>Card</option>
                    <option value="cash" <?php echo e(request('method') === 'cash' ? 'selected' : ''); ?>>Cash</option>
                    <option value="netbanking" <?php echo e(request('method') === 'netbanking' ? 'selected' : ''); ?>>Net Banking</option>
                    <option value="wallet" <?php echo e(request('method') === 'wallet' ? 'selected' : ''); ?>>Wallet</option>
                    <option value="insurance" <?php echo e(request('method') === 'insurance' ? 'selected' : ''); ?>>Insurance</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">From Date</label>
                <input type="date" name="from" value="<?php echo e(request('from')); ?>" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">To Date</label>
                <input type="date" name="to" value="<?php echo e(request('to')); ?>" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    Filter
                </button>
                <a href="<?php echo e(route('payments.index')); ?>" class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors">
                    Clear
                </a>
            </div>
        </form>
    </div>

    
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-900">Recent Payments</h3>
        </div>
        
        <?php if(isset($payments) && (is_object($payments) ? $payments->count() : count($payments)) > 0): ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date & Time</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Patient</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Invoice</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Method</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Reference</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-4">
                            <p class="text-sm font-medium text-gray-900"><?php echo e($payment->payment_date?->format('d M Y') ?? 'N/A'); ?></p>
                            <p class="text-xs text-gray-500"><?php echo e($payment->payment_date?->format('h:i A') ?? ''); ?></p>
                        </td>
                        <td class="px-5 py-4">
                            <?php if($payment->patient): ?>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-xs font-bold">
                                    <?php echo e(strtoupper(substr($payment->patient->name ?? 'P', 0, 1))); ?>

                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900"><?php echo e($payment->patient->name); ?></p>
                                    <p class="text-xs text-gray-500"><?php echo e($payment->patient->phone ?? ''); ?></p>
                                </div>
                            </div>
                            <?php else: ?>
                            <span class="text-gray-400 text-sm">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-4">
                            <?php if($payment->invoice): ?>
                            <a href="<?php echo e(route('billing.show', $payment->invoice)); ?>" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                <?php echo e($payment->invoice->invoice_number ?? '#' . $payment->invoice_id); ?>

                            </a>
                            <?php else: ?>
                            <span class="text-gray-400 text-sm">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-4">
                            <?php
                                $methodColors = [
                                    'upi' => 'bg-purple-100 text-purple-700',
                                    'card' => 'bg-blue-100 text-blue-700',
                                    'cash' => 'bg-green-100 text-green-700',
                                    'netbanking' => 'bg-teal-100 text-teal-700',
                                    'wallet' => 'bg-orange-100 text-orange-700',
                                    'insurance' => 'bg-indigo-100 text-indigo-700',
                                ];
                                $color = $methodColors[$payment->payment_method] ?? 'bg-gray-100 text-gray-700';
                            ?>
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold <?php echo e($color); ?>">
                                <?php echo e(strtoupper($payment->payment_method ?? 'N/A')); ?>

                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-sm text-gray-600 font-mono">
                                <?php echo e($payment->transaction_ref ?? $payment->razorpay_payment_id ?? '-'); ?>

                            </p>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <p class="text-sm font-bold text-green-600">₹<?php echo e(number_format($payment->amount ?? 0)); ?></p>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        
        
        <?php if(method_exists($payments, 'links')): ?>
        <div class="px-5 py-4 border-t border-gray-200">
            <?php echo e($payments->withQueryString()->links()); ?>

        </div>
        <?php endif; ?>
        <?php else: ?>
        <div class="p-12 text-center">
            <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">No payments found</h3>
            <p class="text-gray-500 mt-1">Payments will appear here once invoices are paid.</p>
            <a href="<?php echo e(route('billing.create')); ?>" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Create Invoice
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/payments/index.blade.php ENDPATH**/ ?>