<?php $__env->startSection('title', 'Invoice preview'); ?>
<?php $__env->startSection('breadcrumb', 'Invoice preview'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-4 lg:p-6">
    <style>
        @media print {
            .invoice-preview-toolbar { display: none !important; }
        }
    </style>
    <div class="invoice-preview-toolbar no-print mb-4 flex flex-wrap items-center gap-2 rounded-xl border border-gray-200 bg-white p-3 shadow-sm">
        <a href="<?php echo e(route('billing.show', $invoice)); ?>" class="inline-flex items-center gap-1 rounded-lg border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
            ← Back to invoice
        </a>
        <button type="button" onclick="window.print()" class="inline-flex items-center gap-1 rounded-lg bg-slate-800 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-900">
            Print
        </button>
        <a href="<?php echo e(route('billing.pdf', ['invoice' => $invoice, 'format' => 'gst'])); ?>" class="inline-flex items-center gap-1 rounded-lg border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
            Download GST PDF
        </a>
        <a href="<?php echo e(route('billing.pdf', ['invoice' => $invoice, 'format' => 'bill'])); ?>" class="inline-flex items-center gap-1 rounded-lg border border-teal-200 px-3 py-2 text-sm font-semibold text-teal-800 hover:bg-teal-50">
            Download simple bill PDF
        </a>
        <span class="hidden sm:inline text-sm text-gray-400">|</span>
        <span class="text-xs font-semibold uppercase tracking-wide text-gray-500">Preview layout</span>
        <a href="<?php echo e(route('billing.preview', ['invoice' => $invoice, 'format' => 'gst'])); ?>" class="rounded-lg px-2 py-1 text-sm font-semibold <?php echo e(($format ?? 'gst') === 'gst' ? 'bg-slate-100 text-slate-900' : 'text-gray-600 hover:bg-gray-50'); ?>">
            GST
        </a>
        <a href="<?php echo e(route('billing.preview', ['invoice' => $invoice, 'format' => 'bill'])); ?>" class="rounded-lg px-2 py-1 text-sm font-semibold <?php echo e(($format ?? '') === 'bill' ? 'bg-teal-50 text-teal-900' : 'text-gray-600 hover:bg-gray-50'); ?>">
            Simple bill
        </a>
    </div>

    <div class="mx-auto max-w-5xl overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
        <?php echo $__env->make('billing.partials.invoice-print-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <div class="invoice-print-root min-w-[720px]">
            <?php echo $__env->make('billing.partials.invoice-print-body', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/billing/invoice-preview.blade.php ENDPATH**/ ?>