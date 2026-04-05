<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo e($pdfTitle); ?> <?php echo e($invoice->invoice_number); ?></title>
    <?php echo $__env->make('billing.partials.invoice-print-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <style>
        body { margin: 0; padding: 0; }
    </style>
</head>
<body>
<div class="invoice-print-root">
<?php echo $__env->make('billing.partials.invoice-print-body', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
</body>
</html>
<?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/billing/invoice-pdf.blade.php ENDPATH**/ ?>