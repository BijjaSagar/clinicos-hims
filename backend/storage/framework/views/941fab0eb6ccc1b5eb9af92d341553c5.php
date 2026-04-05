<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Report — <?php echo e($order->order_number); ?></title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { font-size: 13px; }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">

<div class="max-w-3xl mx-auto py-8 px-4">

    
    <div class="no-print flex items-center justify-between mb-4">
        <a href="javascript:history.back()" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 transition-colors">
            <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back
        </a>
        <button
            onclick="window.print()"
            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors"
        >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Print Report
        </button>
    </div>

    
    <div class="bg-white rounded-2xl shadow-md overflow-hidden">

        
        <div class="bg-indigo-700 px-8 py-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight"><?php echo e($clinicName); ?></h1>
                    <p class="text-indigo-200 text-sm mt-1">Laboratory Report</p>
                </div>
                <div class="text-right">
                    <p class="text-indigo-200 text-xs uppercase tracking-wide">Order Number</p>
                    <p class="text-xl font-mono font-bold mt-0.5"><?php echo e($order->order_number); ?></p>
                    <div class="mt-2">
                        <?php if($order->priority === 'stat'): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold bg-red-400/30 text-white uppercase border border-red-300/50">STAT</span>
                        <?php elseif($order->priority === 'urgent'): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold bg-orange-400/30 text-white uppercase border border-orange-300/50">URGENT</span>
                        <?php else: ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-white/20 text-white uppercase">ROUTINE</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-8 py-6">

            
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Patient Information</p>
                    <dl class="space-y-1.5 text-sm">
                        <div class="flex gap-2">
                            <dt class="text-gray-500 w-20 flex-shrink-0">Name</dt>
                            <dd class="font-semibold text-gray-900"><?php echo e($order->patient_name); ?></dd>
                        </div>
                        <?php if(!empty($order->date_of_birth)): ?>
                        <div class="flex gap-2">
                            <dt class="text-gray-500 w-20 flex-shrink-0">Age</dt>
                            <dd class="text-gray-700"><?php echo e(\Carbon\Carbon::parse($order->date_of_birth)->age); ?> years</dd>
                        </div>
                        <?php endif; ?>
                        <?php if($order->gender): ?>
                        <div class="flex gap-2">
                            <dt class="text-gray-500 w-20 flex-shrink-0">Gender</dt>
                            <dd class="text-gray-700"><?php echo e(ucfirst($order->gender)); ?></dd>
                        </div>
                        <?php endif; ?>
                        <?php if($order->phone): ?>
                        <div class="flex gap-2">
                            <dt class="text-gray-500 w-20 flex-shrink-0">Phone</dt>
                            <dd class="text-gray-700"><?php echo e($order->phone); ?></dd>
                        </div>
                        <?php endif; ?>
                    </dl>
                </div>

                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Order Details</p>
                    <dl class="space-y-1.5 text-sm">
                        <div class="flex gap-2">
                            <dt class="text-gray-500 w-24 flex-shrink-0">Ordered By</dt>
                            <dd class="font-medium text-gray-800"><?php if($order->doctor_name): ?>Dr. <?php echo e($order->doctor_name); ?><?php else: ?>—<?php endif; ?></dd>
                        </div>
                        <div class="flex gap-2">
                            <dt class="text-gray-500 w-24 flex-shrink-0">Order Date</dt>
                            <dd class="text-gray-700"><?php echo e($order->created_at ? \Carbon\Carbon::parse($order->created_at)->format('d M Y, H:i') : '—'); ?></dd>
                        </div>
                        <?php if(!empty($order->completed_at)): ?>
                        <div class="flex gap-2">
                            <dt class="text-gray-500 w-24 flex-shrink-0">Reported At</dt>
                            <dd class="text-gray-700"><?php echo e(\Carbon\Carbon::parse($order->completed_at)->format('d M Y, H:i')); ?></dd>
                        </div>
                        <?php endif; ?>
                        <div class="flex gap-2">
                            <dt class="text-gray-500 w-24 flex-shrink-0">Status</dt>
                            <dd>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                    Completed
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            
            <div class="mb-6">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Test Results</p>
                <div class="overflow-x-auto rounded-xl border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Test Name</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Result</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Reference Range</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Unit</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Remarks</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $abn = isset($item->is_abnormal) ? (bool) $item->is_abnormal : false; ?>
                                <tr class="<?php echo e($abn ? 'bg-red-50/50' : ''); ?>">
                                    <td class="px-4 py-3">
                                        <p class="text-sm font-medium text-gray-900"><?php echo e($item->test_name ?? '—'); ?></p>
                                        <?php if(!empty($item->category)): ?>
                                            <p class="text-xs text-gray-400"><?php echo e($item->category); ?></p>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3">
                                        <?php if($item->result_value ?? $item->value ?? null): ?>
                                            <span class="text-sm font-bold <?php echo e($abn ? 'text-red-600' : 'text-gray-900'); ?>">
                                                <?php echo e($item->result_value ?? $item->value); ?>

                                            </span>
                                        <?php else: ?>
                                            <span class="text-sm text-gray-400">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 font-mono">
                                        <?php echo e($item->reference_range ?? '—'); ?>

                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500">
                                        <?php echo e($item->unit ?? '—'); ?>

                                    </td>
                                    <td class="px-4 py-3">
                                        <?php if($abn): ?>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-red-100 text-red-700 uppercase">
                                                Abnormal
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">
                                                Normal
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500">
                                        <?php echo e($item->remarks ?? '—'); ?>

                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>

            
            <?php if($items->filter(fn ($i) => !empty($i->is_abnormal))->count() > 0): ?>
                <div class="mb-6 flex items-center gap-3 text-xs text-gray-500">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-red-100 text-red-700 uppercase">Abnormal</span>
                    <span>Values outside the normal reference range</span>
                </div>
            <?php endif; ?>

            
            <div class="border-t border-gray-200 pt-5 flex items-end justify-between">
                <div>
                    <p class="text-xs text-gray-400">This report was generated by ClinicOS HIMS</p>
                    <p class="text-xs text-gray-400 mt-0.5">Report Date: <?php echo e(now()->format('d M Y, H:i')); ?></p>
                </div>
                <div class="text-right">
                    <div class="h-10 w-40 border-b border-gray-400 mb-1"></div>
                    <p class="text-xs font-medium text-gray-700">Reported by: Lab Technician</p>
                    <p class="text-xs text-gray-400"><?php echo e(now()->format('d M Y')); ?></p>
                </div>
            </div>

        </div>
    </div>

</div>

</body>
</html>
<?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/lab/technician/report.blade.php ENDPATH**/ ?>