@extends('layouts.app')
@section('title', 'Prescription Analytics')
@section('breadcrumb', 'Analytics')

@section('content')
@php
    \Illuminate\Support\Facades\Log::info('analytics.prescriptions.view', [
        'clinic_id' => auth()->user()->clinic_id ?? null,
        'top_drugs' => count($data['top_drugs'] ?? []),
    ]);
    $maxCount = 1;
    if (!empty($data['top_drugs'])) {
        $first = $data['top_drugs'][0];
        $maxCount = max(1, (int) data_get($first, 'count', 1));
    }
    $trend = $data['trend'] ?? [];
    $maxT = 1;
    foreach ($trend as $t) {
        $c = (int) data_get($t, 'count', 0);
        if ($c > $maxT) {
            $maxT = $c;
        }
    }
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
    <div class="mb-2">
        <h1 class="text-2xl font-bold text-gray-900 font-display tracking-tight">Prescription analytics</h1>
        <p class="text-sm text-gray-500 mt-1">Drug usage and prescription volume for the current month.</p>
    </div>

    @include('analytics.partials.subnav', ['active' => 'prescriptions'])

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-sm font-medium text-gray-500">Prescriptions this month</p>
            <p class="mt-2 text-2xl font-extrabold text-brand-blue font-display tabular-nums">{{ $data['total_rx'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-sm font-medium text-gray-500">Avg drugs per Rx</p>
            <p class="mt-2 text-2xl font-extrabold text-emerald-600 font-display tabular-nums">{{ $data['avg_per_rx'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-sm font-medium text-gray-500">Unique drugs prescribed</p>
            <p class="mt-2 text-2xl font-extrabold text-amber-600 font-display tabular-nums">{{ count($data['top_drugs']) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-8 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-900">Most prescribed drugs</h2>
            </div>
            <div class="p-5">
                @if(count($data['top_drugs']) > 0)
                    @foreach($data['top_drugs'] as $drug)
                        @php
                            $cnt = (int) data_get($drug, 'count', 0);
                            $w = $maxCount > 0 ? ($cnt / $maxCount) * 100 : 0;
                        @endphp
                        <div class="flex items-center gap-3 mb-3 last:mb-0">
                            <span class="text-sm text-gray-800 truncate flex-1 min-w-0 max-w-[220px] sm:max-w-xs" title="{{ data_get($drug, 'drug_name') }}">{{ data_get($drug, 'drug_name') }}</span>
                            <div class="flex-1 min-w-0 h-5 rounded-md bg-gray-100 overflow-hidden">
                                <div class="h-full rounded-md bg-gradient-to-r from-indigo-500 to-violet-600 transition-all" style="width: {{ $w }}%"></div>
                            </div>
                            <span class="text-sm font-bold text-gray-900 tabular-nums w-10 text-right shrink-0">{{ $cnt }}</span>
                        </div>
                    @endforeach
                @else
                    <div class="flex flex-col items-center justify-center py-14 text-center">
                        <p class="text-sm font-medium text-gray-600">No prescription data</p>
                        <p class="text-xs text-gray-400 mt-1">Prescriptions issued this month will appear here.</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="lg:col-span-4 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-900">Prescription trend</h2>
                <p class="text-xs text-gray-500 mt-0.5">Last 30 days</p>
            </div>
            <div class="p-5">
                @if(count($trend) > 0)
                    <div class="flex gap-0.5 h-40">
                        @foreach($trend as $day)
                            @php
                                $c = (int) data_get($day, 'count', 0);
                                $h = $maxT > 0 ? ($c / $maxT) * 100 : 0;
                            @endphp
                            <div class="flex-1 min-w-0 flex flex-col justify-end group">
                                <div
                                    class="w-full rounded-t bg-gradient-to-t from-violet-600 to-indigo-400 opacity-90 group-hover:opacity-100 min-h-[2px]"
                                    style="height: {{ max(2, $h) }}%"
                                    title="{{ data_get($day, 'date') }}: {{ $c }}"
                                ></div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 text-center py-8">No trend data</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
