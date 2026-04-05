<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Prescription — <?php echo e($admission->patient->full_name ?? $admission->patient->name); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; padding: 20px; }
        }
    </style>
</head>
<body class="bg-gray-100 p-6">
    <!-- Print Controls -->
    <div class="no-print max-w-3xl mx-auto mb-4 flex justify-between items-center">
        <a href="<?php echo e(route('ipd.show', $admission)); ?>" class="text-sm text-blue-600 hover:underline">&larr; Back to Admission</a>
        <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-2 rounded-lg text-sm font-semibold">Print Prescription</button>
    </div>

    <!-- Prescription -->
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow">
        <!-- Header -->
        <div class="border-b-2 border-blue-600 pb-4 mb-4">
            <h1 class="text-xl font-bold text-blue-800"><?php echo e(auth()->user()->clinic->name ?? 'ClinicOS'); ?></h1>
            <p class="text-sm text-gray-500"><?php echo e(auth()->user()->clinic->address_line1 ?? ''); ?><?php echo e(auth()->user()->clinic->city ? ', ' . auth()->user()->clinic->city : ''); ?></p>
        </div>

        <!-- Patient & Doctor Info -->
        <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
            <div>
                <p><span class="font-semibold">Patient:</span> <?php echo e($admission->patient->full_name ?? $admission->patient->name); ?></p>
                <p><span class="font-semibold">Age/Gender:</span> <?php echo e($admission->patient->date_of_birth ? \Carbon\Carbon::parse($admission->patient->date_of_birth)->age . ' yrs' : ($admission->patient->age_years ? $admission->patient->age_years . ' yrs' : 'N/A')); ?> / <?php echo e(ucfirst($admission->patient->gender ?? $admission->patient->sex ?? 'N/A')); ?></p>
                <p><span class="font-semibold">IPD No:</span> <?php echo e($admission->admission_number ?? 'IPD-'.str_pad($admission->id, 5, '0', STR_PAD_LEFT)); ?></p>
            </div>
            <div class="text-right">
                <p><span class="font-semibold">Doctor:</span> Dr. <?php echo e($admission->primaryDoctor->name ?? 'N/A'); ?></p>
                <p><span class="font-semibold">Ward/Bed:</span> <?php echo e($admission->ward->name ?? 'N/A'); ?> / <?php echo e($admission->bed->bed_number ?? 'N/A'); ?></p>
                <p><span class="font-semibold">Date:</span> <?php echo e(now()->format('d M Y')); ?></p>
            </div>
        </div>

        <!-- Rx Symbol -->
        <div class="text-2xl font-bold text-blue-800 mb-3">&#8478;</div>

        <!-- Medications Table -->
        <table class="w-full text-sm mb-8">
            <thead>
                <tr class="border-b-2 border-gray-300">
                    <th class="text-left py-2 font-semibold">#</th>
                    <th class="text-left py-2 font-semibold">Medication</th>
                    <th class="text-left py-2 font-semibold">Dose</th>
                    <th class="text-left py-2 font-semibold">Route</th>
                    <th class="text-left py-2 font-semibold">Frequency</th>
                    <th class="text-left py-2 font-semibold">Duration</th>
                    <th class="text-left py-2 font-semibold">Instructions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $medicationOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $med): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="border-b border-gray-100">
                    <td class="py-2"><?php echo e($i + 1); ?></td>
                    <td class="py-2 font-medium"><?php echo e($med->drug_name); ?></td>
                    <td class="py-2"><?php echo e($med->dose ?? '-'); ?></td>
                    <td class="py-2"><?php echo e($med->route ?? 'Oral'); ?></td>
                    <td class="py-2"><?php echo e($med->frequency ?? '-'); ?></td>
                    <td class="py-2"><?php echo e($med->duration ?? '-'); ?></td>
                    <td class="py-2 text-gray-600"><?php echo e($med->instructions ?? '-'); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="7" class="py-4 text-center text-gray-400">No medications ordered</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Diagnosis -->
        <?php if($admission->diagnosis_at_admission): ?>
        <div class="mb-6 text-sm">
            <span class="font-semibold">Diagnosis:</span> <?php echo e($admission->diagnosis_at_admission); ?>

        </div>
        <?php endif; ?>

        <!-- Signature -->
        <div class="mt-12 text-right">
            <div class="border-t border-gray-400 inline-block pt-2 px-8">
                <p class="font-semibold">Dr. <?php echo e($admission->primaryDoctor->name ?? ''); ?></p>
                <p class="text-xs text-gray-500"><?php echo e($admission->primaryDoctor->qualification ?? ''); ?></p>
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/ipd/print-prescription.blade.php ENDPATH**/ ?>