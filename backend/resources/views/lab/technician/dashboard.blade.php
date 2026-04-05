@extends('layouts.app')

@section('title', 'Lab — Work Queue')

@section('content')
<div
    x-data="{ collectModal: false, activeOrderId: null, sampleType: '', collectionNotes: '' }"
    class="p-4 sm:p-6 lg:p-8 space-y-6"
>

    {{-- Hero --}}
    <div class="relative overflow-hidden rounded-2xl border border-cyan-900/20 bg-gradient-to-br from-slate-900 via-cyan-950 to-teal-900 text-white shadow-lg">
        <div class="absolute inset-0 opacity-[0.07]" style="background-image:url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        <div class="relative px-5 py-6 sm:px-8 sm:py-8 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 rounded-2xl bg-white/15 backdrop-blur-sm flex items-center justify-center shadow-inner ring-1 ring-white/20 flex-shrink-0">
                    <svg class="w-7 h-7 text-cyan-200" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-cyan-300/90 mb-1">Laboratory · LIS</p>
                    <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">Work Queue</h1>
                    <p class="mt-1.5 text-sm text-cyan-100/85 max-w-xl leading-relaxed">
                        Pending collection and in-progress orders for your clinic. Results entry opens after sample collection.
                    </p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 lg:flex-shrink-0">
                @if(\Illuminate\Support\Facades\Route::has('laboratory.orders'))
                    <a
                        href="{{ route('laboratory.orders') }}"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-white/10 text-white hover:bg-white/20 ring-1 ring-white/25 transition-colors"
                    >
                        <svg class="w-4 h-4 opacity-90" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        All lab orders
                    </a>
                @endif
                <a
                    href="{{ url('/laboratory') }}"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold bg-white text-teal-900 shadow-md hover:bg-cyan-50 transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    New lab order
                </a>
                <span class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-xl text-xs font-medium text-cyan-200/90 bg-black/20 ring-1 ring-white/10" title="Page reloads automatically">
                    <svg class="w-3.5 h-3.5 opacity-90" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Auto-refresh 60s
                </span>
            </div>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium bg-emerald-50 text-emerald-800 border border-emerald-200">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- KPI cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 relative overflow-hidden">
            <div class="absolute inset-y-0 left-0 w-1 rounded-l-2xl bg-red-500"></div>
            <div class="flex items-start justify-between pl-3">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Pending</p>
                    <p class="text-3xl font-extrabold text-red-600 leading-none tabular-nums">{{ $stats['pending'] }}</p>
                    <p class="text-xs text-gray-400 mt-2">Awaiting collection</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 relative overflow-hidden">
            <div class="absolute inset-y-0 left-0 w-1 rounded-l-2xl bg-amber-400"></div>
            <div class="flex items-start justify-between pl-3">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Sample collected</p>
                    <p class="text-3xl font-extrabold text-amber-600 leading-none tabular-nums">{{ $stats['sample_collected'] }}</p>
                    <p class="text-xs text-gray-400 mt-2">Ready for processing</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 relative overflow-hidden">
            <div class="absolute inset-y-0 left-0 w-1 rounded-l-2xl bg-sky-500"></div>
            <div class="flex items-start justify-between pl-3">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Processing</p>
                    <p class="text-3xl font-extrabold text-sky-600 leading-none tabular-nums">{{ $stats['processing'] }}</p>
                    <p class="text-xs text-gray-400 mt-2">Tests in progress</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-sky-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 relative overflow-hidden">
            <div class="absolute inset-y-0 left-0 w-1 rounded-l-2xl bg-emerald-500"></div>
            <div class="flex items-start justify-between pl-3">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Completed today</p>
                    <p class="text-3xl font-extrabold text-emerald-600 leading-none tabular-nums">{{ $stats['completed_today'] }}</p>
                    <p class="text-xs text-gray-400 mt-2">Reports finalized</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Priority queue --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 sm:px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50/80 to-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3">
                <h2 class="text-base font-bold text-gray-900 tracking-tight">Priority work queue</h2>
                @if(!$pendingOrders->isEmpty())
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-cyan-100 text-cyan-900 ring-1 ring-cyan-200/80">
                        {{ $pendingOrders->count() }} active
                    </span>
                @endif
            </div>
            <p class="text-xs text-gray-500">STAT and urgent orders should be handled first.</p>
        </div>

        @if($pendingOrders->isEmpty())
            <div class="relative text-center py-16 sm:py-20 px-6">
                <div class="absolute inset-0 bg-gradient-to-b from-slate-50/50 to-white pointer-events-none"></div>
                <div class="relative max-w-md mx-auto">
                    <div class="mx-auto w-16 h-16 rounded-2xl bg-gradient-to-br from-slate-100 to-cyan-50 flex items-center justify-center ring-1 ring-gray-200/80 shadow-inner mb-5">
                        <svg class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.25">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <p class="text-lg font-semibold text-gray-800">No pending orders in this queue</p>
                    <p class="text-sm text-gray-500 mt-2 leading-relaxed">
                        Create orders from <span class="font-medium text-gray-700">Laboratory → New lab order</span> or your EMR workflow. Pending and in-progress work appears here.
                    </p>
                    <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                        <a href="{{ url('/laboratory') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-teal-600 to-cyan-600 hover:from-teal-700 hover:to-cyan-700 shadow-md">
                            Open laboratory
                        </a>
                        @if(\Illuminate\Support\Facades\Route::has('laboratory.catalog'))
                            <a href="{{ route('laboratory.catalog') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200">
                                Test catalog
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead>
                        <tr class="bg-slate-50/90">
                            <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Order #</th>
                            <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Priority</th>
                            <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Patient</th>
                            <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ordering doctor</th>
                            <th scope="col" class="px-4 py-3.5 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Tests</th>
                            <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ordered</th>
                            <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-50">
                        @foreach($pendingOrders as $order)
                            @php
                                $fk = $labOrderItemFk ?? null;
                                $testCount = 0;
                                if ($fk) {
                                    $testCount = \Illuminate\Support\Facades\DB::table('lab_order_items')
                                        ->where($fk, $order->id)->count();
                                }
                                if ($testCount === 0 && !empty($order->tests)) {
                                    $decoded = is_string($order->tests) ? json_decode($order->tests, true) : $order->tests;
                                    $testCount = is_array($decoded) ? count($decoded) : 0;
                                }
                                $age = $order->date_of_birth
                                    ? \Carbon\Carbon::parse($order->date_of_birth)->age . 'y'
                                    : '—';
                            @endphp
                            <tr class="hover:bg-cyan-50/40 transition-colors">
                                <td class="px-4 py-3.5 text-sm font-mono font-semibold text-gray-900">
                                    {{ $order->order_number }}
                                </td>
                                <td class="px-4 py-3.5">
                                    @if($order->priority === 'stat')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-red-100 text-red-800 uppercase tracking-wide ring-1 ring-red-200/60">STAT</span>
                                    @elseif($order->priority === 'urgent')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-orange-100 text-orange-800 uppercase tracking-wide ring-1 ring-orange-200/60">Urgent</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 text-slate-600 uppercase tracking-wide">Routine</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5">
                                    <p class="text-sm font-semibold text-gray-900">{{ $order->patient_name }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $age }} · {{ ucfirst($order->gender ?? '—') }}</p>
                                </td>
                                <td class="px-4 py-3.5 text-sm text-gray-700">
                                    Dr. {{ $order->doctor_name }}
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[1.75rem] h-7 px-1.5 rounded-full bg-indigo-100 text-indigo-800 font-bold text-xs tabular-nums ring-1 ring-indigo-200/50">
                                        {{ $testCount }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-sm text-gray-600 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($order->created_at)->format('d M, H:i') }}
                                </td>
                                <td class="px-4 py-3.5">
                                    @if($order->status === 'pending')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700 ring-1 ring-red-100">Pending</span>
                                    @elseif($order->status === 'sample_collected')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-800 ring-1 ring-amber-100">Sample collected</span>
                                    @elseif($order->status === 'processing')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-sky-50 text-sky-800 ring-1 ring-sky-100">Processing</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-800 ring-1 ring-emerald-100">Completed</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        @if($order->status === 'pending')
                                            <button
                                                type="button"
                                                @click="collectModal = true; activeOrderId = {{ $order->id }}"
                                                class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg bg-amber-500 text-white hover:bg-amber-600 shadow-sm transition-colors"
                                            >
                                                Collect sample
                                            </button>
                                        @elseif(in_array($order->status, ['sample_collected', 'processing']))
                                            <a
                                                href="{{ route('lab.technician.result-form', $order->id) }}"
                                                class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg bg-sky-600 text-white hover:bg-sky-700 shadow-sm transition-colors"
                                            >
                                                Enter results
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Sample collection modal --}}
    <div
        x-show="collectModal"
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4"
        style="display: none;"
        role="dialog"
        aria-modal="true"
        aria-labelledby="collect-sample-title"
    >
        <div
            x-show="collectModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            @click.outside="collectModal = false"
            class="bg-white rounded-2xl shadow-2xl border border-gray-100 w-full max-w-md p-6"
        >
            <div class="flex items-center justify-between mb-5">
                <h3 id="collect-sample-title" class="text-lg font-bold text-gray-900">Collect sample</h3>
                <button type="button" @click="collectModal = false" class="p-1 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100" aria-label="Close">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Sample type <span class="text-red-500">*</span></label>
                    <select x-model="sampleType" class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                        <option value="">— Select sample type —</option>
                        <option value="blood_venous">Blood (venous)</option>
                        <option value="blood_capillary">Blood (capillary)</option>
                        <option value="urine">Urine</option>
                        <option value="stool">Stool</option>
                        <option value="sputum">Sputum</option>
                        <option value="swab">Swab</option>
                        <option value="csf">CSF</option>
                        <option value="tissue">Tissue / biopsy</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Collection notes</label>
                    <textarea
                        x-model="collectionNotes"
                        rows="3"
                        placeholder="Optional notes (tube colour, fasting, etc.)"
                        class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500"
                    ></textarea>
                </div>
            </div>

            <div class="mt-6 flex gap-3 justify-end">
                <button
                    type="button"
                    @click="collectModal = false"
                    class="px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    @click="submitCollect(activeOrderId, sampleType, collectionNotes)"
                    class="px-4 py-2 text-sm font-semibold text-white bg-amber-500 rounded-xl hover:bg-amber-600 shadow-sm"
                >
                    Confirm collection
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    console.log('[LabWorkQueue] technician dashboard script loaded');
    setTimeout(function () {
        console.log('[LabWorkQueue] auto-refresh in 60s');
        location.reload();
    }, 60000);

    function submitCollect(orderId, sampleType, notes) {
        console.log('[LabWorkQueue] submitCollect', { orderId: orderId, sampleType: sampleType, hasNotes: !!notes });
        if (!sampleType) {
            alert('Please select a sample type.');
            return;
        }

        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        fetch('/lab-portal/' + orderId + '/collect', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                sample_type: sampleType,
                collection_notes: notes,
            }),
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            console.log('[LabWorkQueue] collect response', data);
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'An error occurred.');
            }
        })
        .catch(function (err) {
            console.error('[LabWorkQueue] collect failed', err);
            alert('Failed to submit. Please try again.');
        });
    }
</script>
@endpush
