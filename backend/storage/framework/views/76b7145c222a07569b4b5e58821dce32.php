<?php $__env->startSection('title', 'New Invoice'); ?>
<?php $__env->startSection('breadcrumb', 'Create Invoice'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $invoiceFormAlpineDefaults = $invoiceFormDefaults ?? [
        'items' => [
            ['description' => '', 'sac_code' => '998314', 'amount' => 0],
        ],
    ];
?>
<div class="p-6">
    <div class="max-w-4xl mx-auto">
        
        <div class="mb-6">
            <h1 class="text-xl font-bold text-gray-900">Create Invoice</h1>
            <p class="text-sm text-gray-500 mt-0.5">Generate a new invoice for a patient</p>
        </div>

        <?php if(request('admission_id')): ?>
        <div class="mb-4 p-3 bg-indigo-50 border border-indigo-200 rounded-lg text-sm text-indigo-900">
            <strong>IPD billing:</strong> This invoice will link to admission #<?php echo e(request('admission_id')); ?>. Edit line amounts as needed (ward, procedures, pharmacy, lab).
        </div>
        <?php elseif(request('visit_id')): ?>
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-900">
            <strong>OPD billing:</strong> Linked to <strong>Visit #<?php echo e(request('visit_id')); ?></strong> — totals will show under this encounter in EMR.
        </div>
        <?php else: ?>
        <div class="mb-4 p-3 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700">
            Standalone invoice (no visit / admission link). You can still pick the patient below.
        </div>
        <?php endif; ?>

        <form action="<?php echo e(route('billing.store')); ?>" method="POST" x-data="invoiceForm(<?php echo e(\Illuminate\Support\Js::from($invoiceFormAlpineDefaults)); ?>)" x-init="calculateTotals()" class="space-y-6">
            <?php echo csrf_field(); ?>
            
            <input type="hidden" name="visit_id" value="<?php echo e(request('visit_id')); ?>">
            <input type="hidden" name="admission_id" value="<?php echo e(request('admission_id')); ?>">

            
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h3 class="font-bold text-gray-900">Patient</h3>
                </div>
                <div class="p-5">
                    <select name="patient_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Select patient</option>
                        <?php $__currentLoopData = $patients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $patient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($patient->id); ?>" <?php echo e((string) request('patient_id') === (string) $patient->id ? 'selected' : ''); ?>>
                            <?php echo e($patient->name); ?> — <?php echo e($patient->phone); ?>

                        </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>

            
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="font-bold text-gray-900">Invoice Items</h3>
                </div>
                <div class="p-5">
                    
                    <div class="hidden sm:grid sm:grid-cols-12 gap-2 text-xs text-gray-500 uppercase pb-2 border-b border-gray-100">
                        <div class="sm:col-span-5">Description</div>
                        <div class="sm:col-span-3">SAC Code</div>
                        <div class="sm:col-span-3 text-right">Amount (₹)</div>
                        <div class="sm:col-span-1"></div>
                    </div>
                    <div class="divide-y divide-gray-100">
                        <template x-for="(item, index) in items" :key="'inv-line-'+index">
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 py-3 sm:items-center">
                                <div class="sm:col-span-5">
                                    <label class="sm:hidden text-xs font-medium text-gray-500">Description</label>
                                    <input type="text" :name="`items[${index}][description]`" x-model="item.description"
                                        placeholder="Service description"
                                        class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                        required>
                                </div>
                                <div class="sm:col-span-3">
                                    <label class="sm:hidden text-xs font-medium text-gray-500">SAC Code</label>
                                    <input type="text" :name="`items[${index}][sac_code]`" x-model="item.sac_code"
                                        placeholder="998314"
                                        class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                </div>
                                <div class="sm:col-span-3">
                                    <label class="sm:hidden text-xs font-medium text-gray-500">Amount (₹)</label>
                                    <input type="number" :name="`items[${index}][amount]`" x-model.number="item.amount"
                                        @input="calculateTotals()"
                                        placeholder="0" min="0" step="0.01"
                                        class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-right"
                                        required>
                                </div>
                                <div class="sm:col-span-1 flex justify-end">
                                    <button type="button" @click="removeItem(index)" x-show="items.length > 1"
                                        class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <button 
                        type="button" 
                        @click="addItem()"
                        class="mt-4 flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Line Item
                    </button>
                </div>
            </div>

            
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h3 class="font-bold text-gray-900">Summary</h3>
                </div>
                <div class="p-5">
                    <div class="max-w-sm ml-auto space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Subtotal</span>
                            <span class="font-semibold text-gray-900">₹<span x-text="subtotal.toFixed(2)">0.00</span></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">CGST (9%)</span>
                            <span class="font-semibold text-gray-900">₹<span x-text="cgst.toFixed(2)">0.00</span></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">SGST (9%)</span>
                            <span class="font-semibold text-gray-900">₹<span x-text="sgst.toFixed(2)">0.00</span></span>
                        </div>
                        <div class="border-t border-gray-200 pt-3 flex justify-between">
                            <span class="font-bold text-gray-900">Total</span>
                            <span class="font-bold text-xl text-blue-600">₹<span x-text="total.toFixed(2)">0.00</span></span>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="flex items-center justify-end gap-3">
                <a href="<?php echo e(route('billing.index')); ?>" class="px-6 py-2.5 text-gray-600 font-semibold rounded-xl hover:bg-gray-100 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Create Invoice
                </button>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<?php
    $billingCreatePageContext = [
        'visit_id' => request('visit_id'),
        'admission_id' => request('admission_id'),
        'patient_id' => request('patient_id'),
        'defaults' => $invoiceFormDefaults ?? null,
    ];
?>
<script>
(function () {
    var ctx = <?php echo json_encode($billingCreatePageContext, 15, 512) ?>;
    console.log('[billing.create] page context', ctx);
})();
function invoiceForm(defaults) {
    defaults = defaults || {};
    console.log('[billing.create] invoiceForm()', defaults);
    const initial = (defaults.items && defaults.items.length)
        ? defaults.items.map((row) => ({
            description: row.description ?? '',
            sac_code: row.sac_code ?? '998314',
            amount: parseFloat(row.amount) || 0,
        }))
        : [{ description: '', sac_code: '998314', amount: 0 }];
    return {
        items: initial,
        subtotal: 0,
        cgst: 0,
        sgst: 0,
        total: 0,

        addItem() {
            console.log('[billing.create] addItem before', this.items.length);
            this.items.push({ description: '', sac_code: '998314', amount: 0 });
            console.log('[billing.create] addItem after', this.items.length);
        },

        removeItem(index) {
            this.items.splice(index, 1);
            this.calculateTotals();
        },

        calculateTotals() {
            this.subtotal = this.items.reduce((sum, item) => sum + (parseFloat(item.amount) || 0), 0);
            this.cgst = this.subtotal * 0.09;
            this.sgst = this.subtotal * 0.09;
            this.total = this.subtotal + this.cgst + this.sgst;
        }
    };
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/billing/create.blade.php ENDPATH**/ ?>