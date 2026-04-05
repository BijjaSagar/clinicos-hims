@extends('layouts.app')

@section('title', 'Returns & adjustments')
@section('breadcrumb', 'Pharmacy · Returns')

@section('content')
<div class="p-4 sm:p-5 lg:p-7 space-y-5 max-w-3xl">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900 font-display">Returns & stock adjustment</h1>
            <p class="text-sm text-gray-500 mt-0.5">Reduce quantity on a batch (returns, damage, expiry write-off, corrections)</p>
        </div>
        <a href="{{ route('pharmacy.index') }}" class="px-3 py-2 text-sm font-semibold text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
            Back
        </a>
    </div>

    @if(session('success'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium" style="background:#ecfdf5;color:#059669;border:1px solid #a7f3d0;">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="px-4 py-3 rounded-xl text-sm font-medium" style="background:#fff1f2;color:#dc2626;border:1px solid #fecaca;">
        {{ session('error') }}
    </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-xl p-6">
        <form method="POST" action="{{ route('pharmacy.returns.store') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Batch <span class="text-red-500">*</span></label>
                <select name="stock_id" required
                    class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Select batch…</option>
                    @foreach($batches as $b)
                    @php
                        $label = ($b->item->name ?? 'Item').' · '.($b->batch_number ?? '#'.$b->id).' · exp '.($b->expiry_date ? \Carbon\Carbon::parse($b->expiry_date)->format('d M Y') : '—').' · qty '.$b->quantity_available;
                    @endphp
                    <option value="{{ $b->id }}" @selected((string) old('stock_id') === (string) $b->id)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity to remove <span class="text-red-500">*</span></label>
                <input type="number" name="quantity" min="1" required value="{{ old('quantity') }}"
                    class="w-full max-w-xs px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Reason <span class="text-red-500">*</span></label>
                <select name="reason" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="return">Return to supplier</option>
                    <option value="damage">Damage / breakage</option>
                    <option value="expired">Expired — write off</option>
                    <option value="adjustment">Stock correction</option>
                    <option value="theft">Theft / loss</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea name="notes" rows="2" placeholder="Optional details…"
                    class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('notes') }}</textarea>
            </div>
            <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:shadow-lg"
                style="background:linear-gradient(135deg,#64748b,#475569);">
                Record adjustment
            </button>
        </form>
    </div>
</div>
@endsection
