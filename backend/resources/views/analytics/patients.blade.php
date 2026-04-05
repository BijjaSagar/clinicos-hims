@extends('layouts.app')
@section('title', 'Patient Report')
@section('breadcrumb', 'Analytics')

@section('content')
@php
    \Illuminate\Support\Facades\Log::info('analytics.patients.view', [
        'clinic_id' => auth()->user()->clinic_id ?? null,
        'total' => $data['total'] ?? 0,
    ]);
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
    <div class="mb-2">
        <h1 class="text-2xl font-bold text-gray-900 font-display tracking-tight">Patient report</h1>
        <p class="text-sm text-gray-500 mt-1">Registrations and patients with the most visits.</p>
    </div>

    @include('analytics.partials.subnav', ['active' => 'patients'])

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-sm font-medium text-gray-500">Total patients</p>
            <p class="mt-2 text-2xl font-extrabold text-brand-blue font-display tabular-nums">{{ number_format($data['total']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-sm font-medium text-gray-500">Registered today</p>
            <p class="mt-2 text-2xl font-extrabold text-emerald-600 font-display tabular-nums">{{ $data['new_today'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-sm font-medium text-gray-500">New this month</p>
            <p class="mt-2 text-2xl font-extrabold text-cyan-600 font-display tabular-nums">{{ $data['new_this_month'] }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">Top visitors</h2>
            <p class="text-xs text-gray-500 mt-0.5">Patients by appointment count</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide bg-gray-50/80">
                        <th class="px-5 py-3 w-12">#</th>
                        <th class="px-5 py-3">Patient</th>
                        <th class="px-5 py-3">Phone</th>
                        <th class="px-5 py-3 text-right">Visits</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($data['top_visitors'] as $i => $v)
                        <tr class="hover:bg-gray-50/60">
                            <td class="px-5 py-3 text-gray-500 tabular-nums">{{ $i + 1 }}</td>
                            <td class="px-5 py-3 text-gray-900 font-medium">{{ data_get($v, 'name') }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ data_get($v, 'phone') }}</td>
                            <td class="px-5 py-3 text-right">
                                <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-0.5 rounded-full text-xs font-bold bg-brand-blue/10 text-brand-blue tabular-nums">{{ data_get($v, 'visits') }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-10 text-center text-sm text-gray-500">No data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
