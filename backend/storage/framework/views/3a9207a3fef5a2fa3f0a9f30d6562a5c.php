<?php $__env->startSection('title', 'Dispense Medicine'); ?>
<?php $__env->startSection('breadcrumb', 'Dispense Medicine'); ?>

<?php $__env->startSection('content'); ?>
<div x-data="dispensingPage()" class="p-4 sm:p-5 lg:p-7 space-y-5">

    
    <div class="flex items-center gap-4">
        <a href="<?php echo e(route('pharmacy.index')); ?>"
           class="p-2 rounded-lg border border-gray-200 text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900 font-display">Dispense Medicine</h1>
            <p class="text-sm text-gray-500 mt-0.5">Create a new medicine dispensing record</p>
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

    <?php if($errors->any()): ?>
    <div class="px-4 py-3 rounded-xl text-sm" style="background:#fff1f2;color:#dc2626;border:1px solid #fecaca;">
        <p class="font-semibold mb-1">Please fix the following errors:</p>
        <ul class="list-disc list-inside space-y-0.5">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
    <?php endif; ?>

    <?php echo $__env->make('pharmacy.partials.catalog-stock-hint', ['context' => 'dispense'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <form method="POST" action="<?php echo e(route('pharmacy.dispense')); ?>" class="space-y-5">
        <?php echo csrf_field(); ?>

        
        <div class="bg-white border border-gray-200 rounded-xl p-5 space-y-4">
            <h2 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">1</span>
                Patient Details
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Patient <span class="text-red-500">*</span></label>
                    <select name="patient_id" required
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">Select patient…</option>
                        <?php $__currentLoopData = $patients ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $patient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($patient->id); ?>" <?php echo e((string) old('patient_id', $preselectPatientId ?? null) === (string) $patient->id ? 'selected' : ''); ?>>
                            <?php echo e($patient->full_name); ?><?php if($patient->phone): ?> — <?php echo e($patient->phone); ?><?php endif; ?>
                        </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Doctor / Prescription Ref.</label>
                    <input type="text" name="doctor_reference" value="<?php echo e(old('doctor_reference')); ?>"
                        placeholder="e.g. Dr. Sharma / RX-20240401"
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Mode</label>
                    <select name="payment_mode"
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="cash"   <?php echo e(old('payment_mode') === 'cash'   ? 'selected' : ''); ?>>Cash</option>
                        <option value="upi"    <?php echo e(old('payment_mode') === 'upi'    ? 'selected' : ''); ?>>UPI</option>
                        <option value="card"   <?php echo e(old('payment_mode') === 'card'   ? 'selected' : ''); ?>>Card</option>
                        <option value="credit" <?php echo e(old('payment_mode') === 'credit' ? 'selected' : ''); ?>>Credit (Bill Later)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <input type="text" name="notes" value="<?php echo e(old('notes', !empty($prefillAdmissionId) ? 'IPD admission #'.$prefillAdmissionId : '')); ?>" placeholder="Optional notes"
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        
        <div class="bg-white border border-gray-200 rounded-xl p-5 space-y-4">
            <h2 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">2</span>
                Medicine Items
            </h2>

            
            <div class="overflow-x-auto border border-gray-100 rounded-xl">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase w-[40%]">Medicine</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Qty</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Price/Unit</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Total</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Notes</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(row, idx) in rows" :key="idx">
                            <tr class="border-b border-gray-50">
                                <td class="px-4 py-3">
                                    <select :name="'items[' + idx + '][item_id]'" required
                                        x-model="row.itemId"
                                        @change="updatePrice(idx)"
                                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                        <option value="">Select medicine…</option>
                                        <?php $__currentLoopData = $medicines ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $med): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($med->id); ?>" data-price="<?php echo e($med->price_per_unit ?? 0); ?>">
                                            <?php echo e($med->name); ?><?php if($med->current_stock ?? null): ?> (<?php echo e($med->current_stock); ?> left)<?php endif; ?>
                                        </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" :name="'items[' + idx + '][quantity]'" required min="1"
                                        x-model.number="row.qty"
                                        @input="calcRow(idx)"
                                        class="w-20 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" :name="'items[' + idx + '][unit_price]'" step="0.01" min="0"
                                        x-model.number="row.price"
                                        @input="calcRow(idx)"
                                        class="w-24 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-sm font-semibold text-gray-900" x-text="'₹' + row.total.toFixed(2)"></span>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text" :name="'items[' + idx + '][notes]'"
                                        x-model="row.notes" placeholder="Optional"
                                        class="w-32 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </td>
                                <td class="px-4 py-3">
                                    <button type="button" @click="removeRow(idx)"
                                        x-show="rows.length > 1"
                                        class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <button type="button" @click="addRow()"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-xl transition-colors"
                style="color:#1447E6;border:1px dashed #1447E6;background:rgba(20,71,230,0.04);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Add Another Medicine
            </button>
        </div>

        
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <h2 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">3</span>
                Order Summary
            </h2>
            <div class="max-w-xs ml-auto space-y-2">
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Subtotal</span>
                    <span x-text="'₹' + subtotal.toFixed(2)"></span>
                </div>
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Discount</span>
                    <div class="flex items-center gap-2">
                        <span>₹</span>
                        <input type="number" name="discount" x-model.number="discount" min="0" step="0.01"
                            @input="calcTotal()"
                            class="w-20 px-2 py-1 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-right">
                    </div>
                </div>
                <div class="border-t border-gray-100 pt-2 flex justify-between font-bold text-gray-900">
                    <span>Total Amount</span>
                    <span x-text="'₹' + grandTotal.toFixed(2)" class="text-lg"></span>
                </div>
                <input type="hidden" name="total_amount" x-bind:value="grandTotal.toFixed(2)">
            </div>
        </div>

        
        <div class="flex items-center justify-between">
            <a href="<?php echo e(route('pharmacy.index')); ?>" class="px-4 py-2.5 text-sm font-semibold text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                Cancel
            </a>
            <button type="submit"
                class="px-6 py-2.5 text-sm font-semibold text-white rounded-xl transition-all hover:shadow-lg"
                style="background:linear-gradient(135deg,#1447E6,#0891B2);">
                Save & Dispense
            </button>
        </div>
    </form>

    
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Recent Dispensing History</h3>
            <span class="text-xs text-gray-400">Today</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Bill No.</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Patient</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Items</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Payment</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Time</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">By</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php $__empty_1 = true; $__currentLoopData = $recentDispensing ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <span class="font-mono text-xs font-semibold text-blue-600"><?php echo e($record->dispensing_number ?? '—'); ?></span>
                        </td>
                        <td class="px-4 py-3 text-sm font-semibold text-gray-900"><?php echo e($record->patient_name ?? '—'); ?></td>
                        <td class="px-4 py-3 text-sm text-gray-600"><?php echo e($record->items_count ?? 0); ?> items</td>
                        <td class="px-4 py-3 text-sm font-semibold text-gray-900">₹<?php echo e(number_format($record->total_amount ?? 0)); ?></td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                <?php echo e(ucfirst($record->payment_mode ?? 'cash')); ?>

                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-400">
                            <?php echo e($record->created_at ? \Carbon\Carbon::parse($record->created_at)->format('H:i') : '—'); ?>

                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500"><?php echo e($record->dispensed_by_name ?? '—'); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-sm text-gray-400">No dispensing records today.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php $__env->startPush('scripts'); ?>
<script>
function dispensingPage() {
    const medicinePrices = <?php echo json_encode(
        collect($medicines ?? [])->mapWithKeys(fn($m) => [$m->id => $m->price_per_unit ?? 0])
    , 15, 512) ?>;

    return {
        rows: [{ itemId: '', qty: 1, price: 0, total: 0, notes: '' }],
        discount: 0,

        get subtotal() {
            return this.rows.reduce((s, r) => s + (r.total || 0), 0);
        },

        get grandTotal() {
            return Math.max(0, this.subtotal - this.discount);
        },

        addRow() {
            this.rows.push({ itemId: '', qty: 1, price: 0, total: 0, notes: '' });
        },

        removeRow(idx) {
            if (this.rows.length > 1) this.rows.splice(idx, 1);
        },

        updatePrice(idx) {
            const id = this.rows[idx].itemId;
            this.rows[idx].price = medicinePrices[id] || 0;
            this.calcRow(idx);
        },

        calcRow(idx) {
            const r = this.rows[idx];
            r.total = (r.qty || 0) * (r.price || 0);
        },

        calcTotal() {
            // reactive via grandTotal getter
        }
    };
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/pharmacy/dispensing.blade.php ENDPATH**/ ?>