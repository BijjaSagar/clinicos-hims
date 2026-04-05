@extends('layouts.app')

@section('title', 'Stock Report')
@section('breadcrumb', 'Stock Report')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Stock Report</h1>
            <p class="text-sm text-gray-500 mt-0.5">Complete stock overview with batch details and expiry tracking</p>
        </div>
        <a href="{{ route('pharmacy.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border rounded-xl hover:bg-gray-50">
            Back
        </a>
    </div>

    @if($items->isEmpty())
        <div class="bg-white rounded-xl border p-12 text-center text-gray-400">
            <svg class="mx-auto h-12 w-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            <p class="font-medium">No items in inventory</p>
            <p class="text-sm mt-1">Add medicines through the inventory page first.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($items as $item)
                <div class="bg-white rounded-xl border shadow-sm">
                    <div class="px-5 py-4 border-b flex items-center justify-between">
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ $item->name }}</h3>
                            <p class="text-xs text-gray-500">{{ $item->generic_name ?? '' }} &middot; {{ $item->unit }}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-bold {{ $item->current_stock <= ($item->reorder_level ?? 0) ? 'text-red-600' : 'text-green-600' }}">
                                Stock: {{ $item->current_stock ?? 0 }}
                            </div>
                            <div class="text-xs text-gray-500">MRP: ₹{{ number_format($item->mrp ?? 0, 2) }}</div>
                        </div>
                    </div>

                    @if($item->stocks->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-100 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Batch</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Expiry</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Qty In</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Qty Out</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Available</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($item->stocks as $stock)
                                        @php
                                            $expiry = $stock->expiry_date;
                                            $isExpired = $expiry && $expiry < $today;
                                            $isExpiringSoon = $expiry && !$isExpired && $expiry <= $soon;
                                        @endphp
                                        <tr class="{{ $isExpired ? 'bg-red-50' : ($isExpiringSoon ? 'bg-amber-50' : '') }}">
                                            <td class="px-4 py-2 text-gray-900">{{ $stock->batch_number }}</td>
                                            <td class="px-4 py-2 {{ $isExpired ? 'text-red-600 font-medium' : ($isExpiringSoon ? 'text-amber-600' : 'text-gray-600') }}">
                                                {{ $expiry ? \Carbon\Carbon::parse($expiry)->format('d M Y') : '—' }}
                                                @if($isExpired) <span class="text-xs">(Expired)</span> @endif
                                                @if($isExpiringSoon) <span class="text-xs">(Expiring soon)</span> @endif
                                            </td>
                                            <td class="px-4 py-2 text-right text-gray-700">{{ $stock->quantity_in }}</td>
                                            <td class="px-4 py-2 text-right text-gray-700">{{ $stock->quantity_out }}</td>
                                            <td class="px-4 py-2 text-right font-semibold text-gray-900">{{ $stock->quantity_available }}</td>
                                            <td class="px-4 py-2">
                                                @if($stock->quantity_available <= 0)
                                                    <span class="px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-500">Empty</span>
                                                @elseif($isExpired)
                                                    <span class="px-2 py-0.5 text-xs rounded-full bg-red-100 text-red-700">Expired</span>
                                                @else
                                                    <span class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-700">Active</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="px-5 py-3 text-sm text-gray-400">No batches recorded for this item.</div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
