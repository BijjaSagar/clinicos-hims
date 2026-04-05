@extends('layouts.app')

@section('title', 'Pharmacy suppliers')
@section('breadcrumb', 'Pharmacy · Suppliers')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900 font-display">Suppliers</h1>
            <p class="text-sm text-gray-500 mt-0.5">Vendors for purchase orders and GRN. Linked to stock receipts.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            @if(\Illuminate\Support\Facades\Route::has('pharmacy.purchases.create'))
                <a href="{{ route('pharmacy.purchases.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white bg-gray-900 hover:bg-gray-800">
                    New GRN
                </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="px-4 py-3 rounded-xl text-sm font-medium bg-emerald-50 text-emerald-800 border border-emerald-200">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="px-4 py-3 rounded-xl text-sm font-medium bg-red-50 text-red-800 border border-red-200">{{ session('error') }}</div>
    @endif

    <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
        <h2 class="text-sm font-bold text-gray-900 mb-4">Add supplier</h2>
        <form method="POST" action="{{ route('pharmacy.suppliers.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required maxlength="255"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="e.g. ABC Pharma Distributors">
                    @error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Contact person</label>
                    <input type="text" name="contact_person" value="{{ old('contact_person') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Address</label>
                    <textarea name="address" rows="2" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm resize-none">{{ old('address') }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">GST number</label>
                    <input type="text" name="gst_number" value="{{ old('gst_number') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Drug licence</label>
                    <input type="text" name="drug_license" value="{{ old('drug_license') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Payment terms</label>
                    <input type="text" name="payment_terms" value="{{ old('payment_terms') }}" placeholder="e.g. Net 30"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                </div>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="px-5 py-2 text-sm font-semibold text-white bg-gray-900 rounded-xl hover:bg-gray-800">Save supplier</button>
            </div>
        </form>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
        <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-gray-900">Saved suppliers</h2>
            <span class="text-xs text-gray-400">{{ $suppliers->count() }} total</span>
        </div>
        @if($suppliers->isEmpty())
            <p class="px-5 py-10 text-center text-sm text-gray-500">No suppliers yet. Use the form above.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Name</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Phone</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">GST</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($suppliers as $s)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $s->name }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $s->phone ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $s->gst_number ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
console.log('[pharmacy.suppliers] page loaded', { count: {{ $suppliers->count() }} });
</script>
@endpush
