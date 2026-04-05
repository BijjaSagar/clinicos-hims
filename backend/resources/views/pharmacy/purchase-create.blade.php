@extends('layouts.app')

@section('title', 'New GRN')
@section('breadcrumb', 'Pharmacy · New GRN')

@section('content')
<div class="p-4 sm:p-5 lg:p-7 max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('pharmacy.purchases.index') }}" class="text-sm text-gray-500 hover:text-gray-800">← Back</a>
    </div>
    <div>
        <h1 class="text-xl font-bold text-gray-900 font-display">Record goods receipt (GRN)</h1>
        <p class="text-sm text-gray-500 mt-0.5">Creates purchase lines and stock batches (FIFO) linked to this GRN.</p>
    </div>

    @if(session('error'))
    <div class="px-4 py-3 rounded-xl text-sm font-medium bg-red-50 text-red-800 border border-red-200">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
    <div class="px-4 py-3 rounded-xl text-sm bg-amber-50 text-amber-900 border border-amber-200">
        <ul class="list-disc list-inside space-y-0.5">
            @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('pharmacy.purchases.store') }}" class="bg-white border border-gray-200 rounded-xl p-5 space-y-6 shadow-sm">
        @csrf
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-gray-600 mb-1">Supplier</label>
                <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                    <select name="supplier_id" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white flex-1">
                        <option value="">—</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}" @selected(old('supplier_id') == $s->id)>{{ $s->name }}</option>
                        @endforeach
                    </select>
                    @if(\Illuminate\Support\Facades\Route::has('pharmacy.suppliers.index'))
                        <a href="{{ route('pharmacy.suppliers.index') }}" class="inline-flex items-center justify-center shrink-0 px-3 py-2 text-xs font-semibold rounded-lg border border-gray-200 bg-white text-gray-800 hover:bg-gray-50">
                            Add supplier
                        </a>
                    @endif
                </div>
                @if($suppliers->isEmpty())
                <p class="text-xs text-amber-700 mt-1">No suppliers yet — add one via <strong>Suppliers</strong> or below link. You can still save GRN without supplier.</p>
                @endif
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Supplier invoice #</label>
                <input type="text" name="invoice_number" value="{{ old('invoice_number') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Invoice date</label>
                <input type="date" name="invoice_date" value="{{ old('invoice_date') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Received date <span class="text-red-500">*</span></label>
                <input type="date" name="received_date" required value="{{ old('received_date', date('Y-m-d')) }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-gray-600 mb-1">Notes</label>
                <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm resize-none">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="border-t border-gray-100 pt-4 space-y-4">
            <h2 class="text-sm font-bold text-gray-900">Line items</h2>
            @php $oldItems = old('items', [['item_id' => '', 'batch_number' => '', 'expiry_date' => '', 'quantity' => 1, 'free_quantity' => 0, 'purchase_rate' => '', 'mrp' => '', 'discount_percent' => 0, 'gst_rate' => 12]]); @endphp
            @foreach($oldItems as $idx => $line)
            <div class="border border-gray-100 rounded-lg p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 bg-slate-50/50">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Item <span class="text-red-500">*</span></label>
                    <select name="items[{{ $idx }}][item_id]" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white">
                        <option value="">Select…</option>
                        @foreach($items as $it)
                            <option value="{{ $it->id }}" @selected(($line['item_id'] ?? '') == $it->id)>{{ $it->name }} ({{ $it->unit ?? 'unit' }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Batch <span class="text-red-500">*</span></label>
                    <input type="text" name="items[{{ $idx }}][batch_number]" required value="{{ $line['batch_number'] ?? '' }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Expiry <span class="text-red-500">*</span></label>
                    <input type="date" name="items[{{ $idx }}][expiry_date]" required value="{{ $line['expiry_date'] ?? '' }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Qty <span class="text-red-500">*</span></label>
                    <input type="number" min="1" name="items[{{ $idx }}][quantity]" required value="{{ $line['quantity'] ?? 1 }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Free qty</label>
                    <input type="number" min="0" name="items[{{ $idx }}][free_quantity]" value="{{ $line['free_quantity'] ?? 0 }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Purchase rate <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" min="0" name="items[{{ $idx }}][purchase_rate]" required value="{{ $line['purchase_rate'] ?? '' }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">MRP <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" min="0" name="items[{{ $idx }}][mrp]" required value="{{ $line['mrp'] ?? '' }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Disc %</label>
                    <input type="number" step="0.01" min="0" max="100" name="items[{{ $idx }}][discount_percent]" value="{{ $line['discount_percent'] ?? 0 }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">GST %</label>
                    <input type="number" step="0.01" min="0" max="100" name="items[{{ $idx }}][gst_rate]" value="{{ $line['gst_rate'] ?? 12 }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                </div>
            </div>
            @endforeach
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('pharmacy.purchases.index') }}" class="px-4 py-2.5 rounded-xl text-sm font-semibold border border-gray-200">Cancel</a>
            <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700">Save GRN</button>
        </div>
    </form>
</div>
@endsection
