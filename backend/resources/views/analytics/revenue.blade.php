@extends('layouts.app')
@section('title', 'Revenue Report')
@section('breadcrumb', 'Analytics')

@section('content')
@php
    \Illuminate\Support\Facades\Log::info('analytics.revenue.view', [
        'clinic_id' => auth()->user()->clinic_id ?? null,
        'from' => $data['from'] ?? null,
        'to' => $data['to'] ?? null,
        'total' => $data['total'] ?? 0,
    ]);
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
    <div class="mb-2">
        <h1 class="text-2xl font-bold text-gray-900 font-display tracking-tight">Revenue report</h1>
        <p class="text-sm text-gray-500 mt-1">Filter by date range and review daily totals and payment mix.</p>
    </div>

    @include('analytics.partials.subnav', ['active' => 'revenue'])

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 mb-6">
        <form method="GET" class="flex flex-col sm:flex-row flex-wrap gap-4 items-end">
            <div class="w-full sm:w-auto sm:min-w-[160px]">
                <label for="rev-from" class="block text-sm font-medium text-gray-700 mb-1">From</label>
                <input type="date" id="rev-from" name="from" value="{{ $data['from'] }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue bg-white">
            </div>
            <div class="w-full sm:w-auto sm:min-w-[160px]">
                <label for="rev-to" class="block text-sm font-medium text-gray-700 mb-1">To</label>
                <input type="date" id="rev-to" name="to" value="{{ $data['to'] }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue bg-white">
            </div>
            <button type="submit" class="w-full sm:w-auto px-5 py-2 rounded-lg text-sm font-semibold text-white bg-brand-blue hover:bg-brand-blue-dark transition-colors shadow-sm">
                Apply filter
            </button>
        </form>
    </div>

    <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl shadow-sm text-white p-6 mb-6 ring-1 ring-white/10">
        <p class="text-sm font-medium text-white/85">Total revenue (selected range)</p>
        <p class="mt-2 text-3xl font-extrabold font-display tabular-nums">₹{{ number_format($data['total'], 2) }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-8 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-900">Daily revenue</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide bg-gray-50/80">
                            <th class="px-5 py-3">Date</th>
                            <th class="px-5 py-3 text-right">Revenue</th>
                            <th class="px-5 py-3 text-right">Invoices</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($data['daily'] as $day)
                            <tr class="hover:bg-gray-50/60">
                                <td class="px-5 py-3 text-gray-800">{{ data_get($day, 'date') }}</td>
                                <td class="px-5 py-3 text-right font-medium text-gray-900 tabular-nums">₹{{ number_format((float) data_get($day, 'total', 0), 2) }}</td>
                                <td class="px-5 py-3 text-right text-gray-600 tabular-nums">{{ data_get($day, 'count') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-10 text-center text-sm text-gray-500">No data for this range</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="lg:col-span-4 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-900">By payment method</h2>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($data['by_payment'] as $pm)
                    <div class="flex justify-between items-center gap-3 px-5 py-3 hover:bg-gray-50/80">
                        <span class="text-sm text-gray-800">{{ ucfirst(data_get($pm, 'payment_method') ?? 'N/A') }}</span>
                        <span class="text-sm font-semibold text-gray-900 tabular-nums text-right">
                            ₹{{ number_format((float) data_get($pm, 'total', 0), 2) }}
                            <span class="text-gray-500 font-normal">({{ data_get($pm, 'count') }})</span>
                        </span>
                    </div>
                @empty
                    <div class="px-5 py-10 text-center text-sm text-gray-500">No data</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
