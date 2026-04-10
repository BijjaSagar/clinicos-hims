@extends('layouts.app')

@section('title', 'Lab Integration')
@section('breadcrumb', 'Lab Orders')

@section('content')

@push('user-guide')
@php
$guideSteps = [
    ['title' => 'View Pending Tests', 'detail' => 'The dashboard shows all lab orders waiting for sample collection or result entry.'],
    ['title' => 'Collect a Sample', 'detail' => 'Mark a test as \"Sample Collected\" once the patient\'s sample is received.'],
    ['title' => 'Enter Results', 'detail' => 'Click \"Enter Results\" on any pending test to fill in the measured values.'],
    ['title' => 'Dispatch Report', 'detail' => 'Once results are entered and verified, click \"Dispatch\" to notify the doctor.'],
];
$guideExamples = [
    ['scenario' => 'Processing a blood test', 'action' => 'Patient referred for CBC → Mark \"Sample Collected\" → Enter WBC, RBC, Hb values → Save → Doctor gets notified.'],
];
$guideTips = [
    'Critical values (outside reference range) are automatically flagged in red.',
    'Reports can be printed or sent to patients via WhatsApp directly from the result screen.',
];
@endphp

<x-user-guide
    title="Laboratory"
    icon="science"
    description="Manage lab test orders, pending samples, result entry, and final report dispatch."
    :steps="$guideSteps"
    :examples="$guideExamples"
    :tips="$guideTips"
/>

@endpush
@php
    $labDashboardInit = [
        'inHouseOrdersUrl' => \Illuminate\Support\Facades\Route::has('laboratory.orders') ? route('laboratory.orders') : '',
    ];
@endphp
{{-- Single-quoted x-data so @json() does not break the attribute --}}
<div x-data='labDashboard(@json($labDashboardInit))' class="px-6 py-6 space-y-6 max-w-[1600px] mx-auto">

    {{-- PAGE HEADER --}}

    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div class="space-y-1">
            <p class="text-[0.65rem] font-bold uppercase tracking-widest text-on-surface-variant">Clinical Command / Laboratory</p>
            <h1 class="text-2xl font-extrabold text-on-surface tracking-tight uppercase">Lab Integration</h1>
            <p class="text-sm text-on-surface-variant font-bold opacity-60">In-house LIS or external reference labs</p>
        </div>


        <div class="flex items-center gap-2">
            @if(\Illuminate\Support\Facades\Route::has('laboratory.orders'))
            <a href="{{ route('laboratory.orders') }}" 
               class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl text-sm font-bold text-on-surface-variant border border-outline-variant/20 bg-surface-container-lowest hover:border-primary/20 transition-all shadow-clinical">
                <span class="material-symbols-outlined text-base">list_alt</span>View All Orders
            </a>
            @endif
            @if(\Illuminate\Support\Facades\Route::has('laboratory.catalog'))
            <a href="{{ route('laboratory.catalog') }}" 
               class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl text-sm font-bold text-on-surface-variant border border-outline-variant/20 bg-surface-container-lowest hover:border-primary/20 transition-all shadow-clinical">
                <span class="material-symbols-outlined text-base">settings</span>Test Catalog
            </a>
            <button type="button" @click.prevent="showNewTestModal = true"
               class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl text-sm font-bold text-on-surface-variant border border-outline-variant/20 bg-surface-container-lowest hover:border-primary/20 transition-all shadow-clinical">
                <span class="material-symbols-outlined text-base">add_box</span>New Test
            </button>
            @endif
            <button type="button" @click.prevent="showOrderModal = true"
                    class="inline-flex items-center gap-2 px-7 py-3.5 rounded-xl text-sm font-bold text-on-primary hover:opacity-90 transition-all shadow-clinical"
                    style="background:linear-gradient(135deg,#0043c8,#0057ff);">
                <span class="material-symbols-outlined text-base">add_circle</span>New Lab Order
            </button>
        </div>
    </div>

    {{-- Schema warning --}}
    @if(isset($labSchemaReady) && !$labSchemaReady)
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-tertiary-fixed border border-tertiary/15 text-sm font-semibold text-on-surface">
        <span class="material-symbols-outlined text-tertiary flex-shrink-0">warning</span>
        Lab orders table missing. Run <code class="bg-surface-container px-1.5 py-0.5 rounded text-xs font-mono">php artisan migrate</code> to enable this module.
    </div>
    @endif

    {{-- WORKFLOW SHORTCUTS --}}
    <div class="flex flex-wrap items-center gap-2 bg-surface-container-lowest rounded-xl border border-outline-variant/15 px-6 py-4 shadow-clinical">
        <span class="text-[0.62rem] font-bold uppercase tracking-widest text-on-surface-variant mr-3">Workflow Grid</span>
        @if(\Illuminate\Support\Facades\Route::has('opd.queue'))
        <a href="{{ route('opd.queue') }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-surface-container text-on-surface-variant hover:bg-surface-container-high transition-all">OPD Queue</a>
        @endif
        @if(\Illuminate\Support\Facades\Route::has('ipd.index'))
        <a href="{{ route('ipd.index') }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-surface-container text-on-surface-variant hover:bg-surface-container-high transition-all">IPD</a>
        @endif
        @if(\Illuminate\Support\Facades\Route::has('laboratory.orders'))
        <a href="{{ route('laboratory.orders') }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-primary-fixed text-primary hover:opacity-80 transition-all">In-house Lab Orders</a>
        @endif
        @if(\Illuminate\Support\Facades\Route::has('pharmacy.dispense.form'))
        <a href="{{ route('pharmacy.dispense.form') }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-surface-container text-on-surface-variant hover:bg-surface-container-high transition-all">Pharmacy</a>
        @endif
        @if(\Illuminate\Support\Facades\Route::has('billing.index'))
        <a href="{{ route('billing.index') }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-surface-container text-on-surface-variant hover:bg-surface-container-high transition-all">Billing</a>
        @endif
    </div>

    {{-- KPI STATS --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        @foreach([
            ['label'=>'Orders Today',       'val'=>$stats['orders_today'],    'icon'=>'science',        'bg'=>'bg-primary-fixed',  'color'=>'text-primary'],
            ['label'=>'Awaiting Results',   'val'=>$stats['pending_results'], 'icon'=>'hourglass_top',  'bgStyle'=>'background:#fef3c7;', 'colorStyle'=>'color:#d97706;'],
            ['label'=>'Results This Month', 'val'=>$stats['results_received'],'icon'=>'check_circle',   'bgStyle'=>'background:#d1fae5;', 'colorStyle'=>'color:#059669;'],
        ] as $s)
        <div class="bg-surface-container-lowest rounded-xl p-4 border border-outline-variant/10 shadow-clinical">
            <div class="flex items-start justify-between mb-4">
                <p class="text-[0.68rem] font-bold uppercase tracking-widest text-on-surface-variant">{{ $s['label'] }}</p>
                <span class="w-9 h-9 rounded-lg flex items-center justify-center {{ $s['bg'] ?? '' }}"
                      @if(!empty($s['bgStyle'])) style="{{ $s['bgStyle'] }}" @endif>
                    <span class="material-symbols-outlined text-xl {{ $s['color'] ?? '' }}"
                          @if(!empty($s['colorStyle'])) style="{{ $s['colorStyle'] }};font-variation-settings:'FILL' 1;" @else style="font-variation-settings:'FILL' 1;" @endif>{{ $s['icon'] }}</span>
                </span>
            </div>
            <p class="font-extrabold text-3xl text-on-surface tabular-nums">{{ $s['val'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- LAB PROVIDERS --}}
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/15 shadow-clinical overflow-hidden">
        <div class="px-6 py-5 border-b border-outline-variant/10 bg-surface-container/40">
            <p class="text-[0.65rem] font-bold uppercase tracking-widest text-on-surface-variant">Lab Providers</p>
            <p class="text-xs text-on-surface-variant font-bold opacity-60 mt-0.5">Choose in-house LIS or a connected reference lab</p>
        </div>
        <div class="p-8 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            @if(\Illuminate\Support\Facades\Route::has('laboratory.orders'))
            <a href="{{ route('laboratory.orders') }}"
               class="flex flex-col items-center gap-3 p-6 rounded-xl border-2 border-primary/30 bg-primary-fixed/40 hover:bg-primary-fixed transition-all text-center group">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-extrabold text-xs"
                     style="background:linear-gradient(135deg,#0043c8,#0057ff);">IN</div>
                <p class="text-xs font-bold text-primary">In-house Lab</p>
                <p class="text-[0.6rem] text-on-surface-variant font-bold">Hospital LIS</p>
            </a>
            @endif
            @php
            $labs = [
                ['name'=>'Dr. Lal PathLabs','code'=>'lal_pathlabs','gradient'=>'#dc2626,#b91c1c'],
                ['name'=>'SRL Diagnostics', 'code'=>'srl',         'gradient'=>'#2563eb,#1d4ed8'],
                ['name'=>'Thyrocare',       'code'=>'thyrocare',   'gradient'=>'#7c3aed,#6d28d9'],
                ['name'=>'Metropolis',      'code'=>'metropolis',  'gradient'=>'#059669,#047857'],
                ['name'=>'Pathkind Labs',   'code'=>'pathkind',    'gradient'=>'#ea580c,#c2410c'],
            ];
            @endphp
            @foreach($labs as $lab)
            <div class="flex flex-col items-center gap-3 p-6 rounded-xl border border-outline-variant/15 bg-surface-container-lowest hover:border-primary/20 hover:shadow-clinical transition-all text-center cursor-pointer"
                 @click="selectProvider('{{ $lab['code'] }}')">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-extrabold text-xs"
                     style="background:linear-gradient(135deg,{{ $lab['gradient'] }});">
                    {{ substr($lab['name'], 0, 2) }}
                </div>
                <p class="text-xs font-bold text-on-surface leading-tight">{{ $lab['name'] }}</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ORDER MANAGEMENT --}}
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/15 shadow-clinical overflow-hidden">
        <div class="px-6 py-5 border-b border-outline-variant/10 bg-surface-container/40 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <p class="text-[0.65rem] font-bold uppercase tracking-widest text-on-surface-variant">Order Management</p>
                <p class="text-[0.6rem] text-on-surface-variant font-bold opacity-60">Search or manage recent laboratory orders</p>
            </div>
            <div class="flex items-center gap-3">
                <form action="{{ route('laboratory.index') }}" method="GET" class="relative group">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-sm text-on-surface-variant group-focus-within:text-primary transition-colors">search</span>
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search by Order # or Patient..." 
                           class="pl-9 pr-4 py-2 bg-surface-container border border-outline-variant/20 rounded-xl text-xs text-on-surface focus:ring-2 focus:ring-primary/20 outline-none w-full sm:w-64 transition-all">
                    @if($search)
                    <a href="{{ route('laboratory.index') }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-error transition-colors">
                        <span class="material-symbols-outlined text-xs">close</span>
                    </a>
                    @endif
                </form>
                <a href="{{ route('laboratory.orders') }}" class="text-xs font-bold text-primary hover:underline flex items-center gap-1">
                    View All <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>
        </div>

        {{-- Column headers --}}
        <div class="grid grid-cols-12 gap-2 px-5 py-2.5 bg-surface-container border-b border-outline-variant/10">
            <div class="col-span-2"><p class="text-[0.62rem] font-bold uppercase tracking-widest text-on-surface-variant">Order #</p></div>
            <div class="col-span-2"><p class="text-[0.62rem] font-bold uppercase tracking-widest text-on-surface-variant">Patient</p></div>
            <div class="col-span-2"><p class="text-[0.62rem] font-bold uppercase tracking-widest text-on-surface-variant">Lab</p></div>
            <div class="col-span-1 text-center"><p class="text-[0.62rem] font-bold uppercase tracking-widest text-on-surface-variant">Tests</p></div>
            <div class="col-span-1"><p class="text-[0.62rem] font-bold uppercase tracking-widest text-on-surface-variant">Amount</p></div>
            <div class="col-span-2"><p class="text-[0.62rem] font-bold uppercase tracking-widest text-on-surface-variant">Status</p></div>
            <div class="col-span-1"><p class="text-[0.62rem] font-bold uppercase tracking-widest text-on-surface-variant">Date</p></div>
            <div class="col-span-1 text-right"><p class="text-[0.62rem] font-bold uppercase tracking-widest text-on-surface-variant">Actions</p></div>
        </div>

        @php
        $labStatusBadge = [
            'pending'          => ['#f1f5f9','#475569'],
            'sample_collected' => ['#dce1ff','#0043c8'],
            'processing'       => ['#fef3c7','#92400e'],
            'ready'            => ['#d1fae5','#065f46'],
            'sent'             => ['#e0f2fe','#0369a1'],
            'completed'        => ['#d1fae5','#065f46'],
            'cancelled'        => ['#ffdad6','#ba1a1a'],
        ];
        @endphp

        <div class="divide-y divide-outline-variant/10">
            @forelse($recentOrders as $order)
            @php
                $lb = $labStatusBadge[$order->status] ?? ['#f1f5f9','#475569'];
                $tests = json_decode($order->tests, true) ?? [];
                $pKey = strtolower((string) \Illuminate\Support\Arr::get((array) $order, 'provider', ''));
                $pName = trim((string) \Illuminate\Support\Arr::get((array) $order, 'provider_name', ''));
                $externalCodes = ['lal_pathlabs','srl','thyrocare','metropolis','pathkind'];
                $isExternalLab = in_array($pKey, $externalCodes, true) || ($pName !== '' && !in_array($pKey, ['inhouse','internal','hospital','lis'], true));
                $labViewUrl = null;
                $labViewTitle = 'View order';
                if ($isExternalLab) {
                    if (\Illuminate\Support\Facades\Route::has('lab.orders.status')) { $labViewUrl = route('lab.orders.status', $order->id); $labViewTitle = 'View order details'; }
                    elseif (!empty(\Illuminate\Support\Arr::get((array) $order, 'result_url')) && \Illuminate\Support\Facades\Route::has('lab.orders.download')) { $labViewUrl = route('lab.orders.download', $order->id); $labViewTitle = 'View / download result'; }
                } else {
                    $st = (string) \Illuminate\Support\Arr::get((array) $order, 'status', '');
                    if (\Illuminate\Support\Facades\Route::has('laboratory.orders.report') && in_array($st, ['completed','ready','sent'], true)) { $labViewUrl = route('laboratory.orders.report', $order->id); $labViewTitle = 'View report'; }
                    elseif (\Illuminate\Support\Facades\Route::has('laboratory.result-entry')) { $labViewUrl = route('laboratory.result-entry', $order->id); $labViewTitle = 'Open order & results'; }
                    elseif (\Illuminate\Support\Facades\Route::has('laboratory.orders')) { $labViewUrl = route('laboratory.orders'); $labViewTitle = 'In-house lab orders'; }
                }
            @endphp
            <div class="grid grid-cols-12 gap-2 px-5 py-3.5 items-center hover:bg-surface-container-low transition-colors">
                <div class="col-span-2">
                    @if($labViewUrl)
                    <a href="{{ $labViewUrl }}" class="text-xs font-mono font-bold text-primary hover:underline transition-all" title="{{ $labViewTitle }}">
                        {{ $order->order_number }}
                    </a>
                    @else
                    <span class="text-xs font-mono font-bold text-primary">{{ $order->order_number }}</span>
                    @endif
                </div>
                <div class="col-span-2">
                    <p class="text-xs font-bold text-on-surface truncate">{{ $order->patient_name ?? '—' }}</p>
                    <p class="text-[0.6rem] text-on-surface-variant italic">{{ $order->patient_phone ?? '' }}</p>
                </div>
                <div class="col-span-2">
                    <p class="text-xs text-on-surface-variant truncate">
                        {{ $order->department_name ?? ($order->department?->name ?? ($order->provider_name ?? 'In-house Lab')) }}
                    </p>
                </div>
                <div class="col-span-1 text-center">
                    <span class="text-xs font-bold text-on-surface-variant">{{ $order->tests_count }}</span>
                </div>
                <div class="col-span-1">
                    <span class="text-sm font-bold text-on-surface">₹{{ number_format($order->total_amount) }}</span>
                </div>
                <div class="col-span-2">
                    <span class="text-[0.62rem] font-bold px-2 py-0.5 rounded-full"
                          style="background:{{ $lb[0] }};color:{{ $lb[1] }};">
                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                    </span>
                </div>
                <div class="col-span-1">
                    <p class="text-[0.62rem] text-on-surface-variant">{{ \Carbon\Carbon::parse($order->created_at)->format('d M') }}</p>
                </div>
                <div class="col-span-1 flex items-center justify-end gap-1.5">
                    @if($order->status === 'pending')
                    <button type="button" @click.prevent="quickCollectOrder = { id: {{ $order->id }}, number: '{{ $order->order_number }}', patient: '{{ addslashes($order->patient_name) }}' }; showQuickCollect = true"
                            class="w-7 h-7 flex items-center justify-center rounded-lg bg-primary-fixed text-primary hover:opacity-80 transition-all" title="Quick Sample Collection">
                        <span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1;">colorize</span>
                    </button>
                    @endif

                    @if(in_array($order->status, ['completed', 'ready', 'sent']))
                    <a href="{{ route('laboratory.orders.download', $order->id) }}"
                       class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-surface-container transition-all" title="Download Report (PDF)">
                        <span class="material-symbols-outlined text-sm" style="color:#059669;">download</span>
                    </a>

                    <button type="button" @click.prevent="shareOnWhatsApp({{ $order->id }})"
                       class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-[#25D366]/10 transition-all" title="Share on WhatsApp">
                        <span class="material-symbols-outlined text-sm text-[#25D366]">share</span>
                    </button>
                    @endif

                    <button type="button" @click.prevent="previewOrderDetails({{ $order->id }})"
                       class="w-7 h-7 flex items-center justify-center rounded-lg text-on-surface-variant hover:bg-primary-fixed hover:text-primary transition-all" title="Quick Preview">
                        <span class="material-symbols-outlined text-sm">visibility</span>
                    </button>
                </div>
            </div>
            @empty
            <div class="py-16 text-center">
                @if($search)
                <div class="w-16 h-16 bg-surface-container rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-3xl text-on-surface-variant/30">search_off</span>
                </div>
                <p class="text-sm font-bold text-on-surface">No results matches "{{ $search }}"</p>
                <p class="text-[0.65rem] text-on-surface-variant opacity-60 mb-6">Try searching for the full UHID or Order Number.</p>
                <a href="{{ route('laboratory.index') }}" class="px-6 py-2 rounded-xl text-[0.65rem] font-bold uppercase tracking-widest bg-primary text-on-primary shadow-clinical transition-all hover:opacity-90">
                    Clear Search
                </a>
                @else
                <span class="material-symbols-outlined text-4xl text-on-surface-variant/30 mb-3">science</span>
                <p class="text-sm font-bold text-on-surface-variant">No lab orders yet</p>
                <p class="text-[0.65rem] text-on-surface-variant opacity-60">Create your first order using the button above</p>
                @endif
            </div>
            @endforelse
        </div>
    </div>

    {{-- NEW ORDER MODAL --}}
    <div x-show="showOrderModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         @click.self="showOrderModal = false">
        <div class="bg-surface-container-lowest rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto border border-outline-variant/15" @click.stop>
            <div class="px-6 py-4 border-b border-outline-variant/10 flex items-center justify-between sticky top-0 bg-surface-container-lowest">
                <p class="font-bold text-on-surface">Create Lab Order</p>
                <button @click="showOrderModal = false"
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-on-surface-variant hover:bg-surface-container transition-all">
                    <span class="material-symbols-outlined text-sm">close</span>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[0.65rem] font-bold uppercase tracking-widest text-on-surface-variant mb-1.5">Patient</label>
                        <select x-model="orderForm.patient_id" 
                                @change="const opt = $event.target.options[$event.target.selectedIndex]; orderForm.collection_address = opt.getAttribute('data-address');"
                                class="w-full px-3 py-2.5 bg-surface-container border border-outline-variant/20 rounded-lg text-sm text-on-surface focus:ring-2 focus:ring-primary/20 outline-none">
                            <option value="">Select patient</option>
                            @foreach($patients ?? [] as $patient)
                            <option value="{{ $patient?->id }}" data-address="{{ $patient?->address }}">{{ $patient?->name }}@if($patient?->phone) — {{ $patient?->phone }}@endif</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[0.65rem] font-bold uppercase tracking-widest text-on-surface-variant mb-1.5">Lab Provider</label>
                        <select x-model="orderForm.provider" @change="onProviderChange()" class="w-full px-3 py-2.5 bg-surface-container border border-outline-variant/20 rounded-lg text-sm text-on-surface focus:ring-2 focus:ring-primary/20 outline-none">
                            <option value="">Select lab</option>
                            @if(\Illuminate\Support\Facades\Route::has('laboratory.orders'))
                            <option value="inhouse">In-house laboratory (LIS)</option>
                            @endif
                            <option value="lal_pathlabs">Dr. Lal PathLabs</option>
                            <option value="srl">SRL Diagnostics</option>
                            <option value="thyrocare">Thyrocare</option>
                            <option value="metropolis">Metropolis</option>
                            <option value="pathkind">Pathkind Labs</option>
                        </select>
                    </div>
                </div>

                <div x-show="orderForm.provider === 'inhouse'" x-cloak
                     class="rounded-xl bg-primary-fixed border border-primary/15 px-4 py-3 text-sm">
                    <p class="font-bold text-primary">In-house lab orders</p>
                    <p class="mt-1 text-on-surface-variant text-xs">External catalog search does not apply. Open the hospital lab workbench to pick tests from your catalog.</p>
                    <button type="button" @click="goToInHouseLab()"
                            class="mt-3 inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-bold text-on-primary hover:opacity-90"
                            style="background:linear-gradient(135deg,#0043c8,#0057ff);">
                        <span class="material-symbols-outlined text-sm">open_in_new</span>Go to in-house lab
                    </button>
                </div>

                <div x-show="orderForm.provider && orderForm.provider !== 'inhouse'" class="space-y-2">
                    <label class="block text-[0.65rem] font-bold uppercase tracking-widest text-on-surface-variant">Search Tests or Category</label>
                    <input type="text" x-model="testSearch" @input.debounce.300ms="searchTests()"
                           placeholder="e.g. CBC, Lipid, Hematology…"
                           class="w-full px-3 py-2.5 bg-surface-container border border-outline-variant/20 rounded-lg text-sm text-on-surface focus:ring-2 focus:ring-primary/20 outline-none">

                    <div x-show="testsLoading" class="rounded-lg border border-outline-variant/15 px-4 py-6 text-center text-sm text-on-surface-variant">
                        Loading test catalog…
                    </div>
                    <div x-show="!testsLoading && testsFetchError" class="rounded-lg bg-error-container border border-error/15 px-4 py-3 text-sm text-error" x-text="testsFetchError"></div>
                    <div x-show="!testsLoading && !testsFetchError && !availableTests.length" class="rounded-lg bg-tertiary-fixed border border-tertiary/10 px-4 py-3 text-sm text-on-surface">
                        No tests match your search. Clear the box above to see all categories.
                    </div>
                    <div x-show="!testsLoading && !testsFetchError && availableTests.length"
                         class="border border-outline-variant/15 rounded-xl max-h-64 overflow-y-auto bg-surface-container-lowest shadow-clinical">
                        <template x-for="[cat, items] in groupedTestEntries" :key="cat">
                            <div class="border-b border-outline-variant/10 last:border-b-0">
                                <div class="px-4 py-2 bg-surface-container text-[0.6rem] font-bold text-on-surface-variant uppercase tracking-widest" x-text="cat"></div>
                                <template x-for="test in items" :key="test.code + '-' + cat">
                                    <div class="flex items-center justify-between px-4 py-2.5 hover:bg-primary-fixed/30 cursor-pointer border-t border-outline-variant/5 first:border-t-0"
                                         @click="addTest(test)">
                                        <div>
                                            <div class="text-sm font-semibold text-on-surface" x-text="test.name"></div>
                                            <div class="text-xs text-on-surface-variant" x-text="test.code"></div>
                                        </div>
                                        <div class="text-sm font-bold text-primary" x-text="'₹' + (test.price ?? 0)"></div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>

                <div x-show="orderForm.provider && orderForm.provider !== 'inhouse' && orderForm.tests.length">
                    <label class="block text-[0.65rem] font-bold uppercase tracking-widest text-on-surface-variant mb-2">Selected Tests</label>
                    <div class="space-y-2">
                        <template x-for="(test, idx) in orderForm.tests" :key="idx">
                            <div class="flex items-center justify-between px-3 py-2 bg-primary-fixed rounded-lg">
                                <span class="text-sm font-semibold text-on-surface" x-text="test.name"></span>
                                <div class="flex items-center gap-3">
                                    <span class="text-sm font-bold text-primary" x-text="'₹' + test.price"></span>
                                    <button @click="removeTest(idx)" class="text-error hover:opacity-70 font-bold text-lg leading-none">×</button>
                                </div>
                            </div>
                        </template>
                        <div class="text-right font-extrabold text-on-surface text-sm" x-text="'Total: ₹' + orderTotal"></div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4" x-show="orderForm.provider && orderForm.provider !== 'inhouse'">
                    <div>
                        <label class="block text-[0.65rem] font-bold uppercase tracking-widest text-on-surface-variant mb-1.5">Sample Collection</label>
                        <select x-model="orderForm.sample_collection_type" class="w-full px-3 py-2.5 bg-surface-container border border-outline-variant/20 rounded-lg text-sm text-on-surface focus:ring-2 focus:ring-primary/20 outline-none">
                            <option value="lab">At Lab</option>
                            <option value="home">Home Collection</option>
                        </select>
                    </div>
                    <div x-show="orderForm.sample_collection_type === 'home'">
                        <label class="block text-[0.65rem] font-bold uppercase tracking-widest text-on-surface-variant mb-1.5">Collection Date</label>
                        <input type="date" x-model="orderForm.collection_date" class="w-full px-3 py-2.5 bg-surface-container border border-outline-variant/20 rounded-lg text-sm text-on-surface focus:ring-2 focus:ring-primary/20 outline-none">
                    </div>
                    <div class="col-span-2" x-show="orderForm.sample_collection_type === 'home'" x-transition>
                        <label class="block text-[0.65rem] font-bold uppercase tracking-widest text-on-surface-variant mb-1.5">Home Collection Address</label>
                        <textarea x-model="orderForm.collection_address" rows="2" placeholder="Enter full address for home collection…"
                                  class="w-full px-4 py-3 bg-surface-container border border-outline-variant/20 rounded-xl text-sm text-on-surface focus:ring-2 focus:ring-primary/20 outline-none resize-none shadow-sm"></textarea>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-outline-variant/10 flex justify-end gap-3 bg-surface-container/40 sticky bottom-0">
                <button type="button" @click="showOrderModal = false"
                        class="px-5 py-2.5 rounded-lg text-sm font-bold text-on-surface-variant border border-outline-variant/20 hover:bg-surface-container transition-all">
                    Cancel
                </button>
                <button type="button"
                        x-show="orderForm.provider && orderForm.provider !== 'inhouse'"
                        @click="submitOrder()" :disabled="!canSubmit"
                        class="px-6 py-2.5 rounded-lg text-sm font-bold text-on-primary hover:opacity-90 disabled:opacity-50 transition-all"
                        style="background:linear-gradient(135deg,#0043c8,#0057ff);">
                    Create Order
                </button>
            </div>
        </div>
    </div>

    {{-- NEW LAB TEST MODAL --}}
    <div x-show="showNewTestModal" x-cloak
         class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         @click.self="showNewTestModal = false">
        <div class="bg-surface-container-lowest rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto border border-outline-variant/15" @click.stop>
            <div class="px-6 py-4 border-b border-outline-variant/10 flex items-center justify-between sticky top-0 bg-surface-container-lowest">
                <p class="font-bold text-on-surface">Add New Lab Test</p>
                <button @click="showNewTestModal = false" class="w-8 h-8 flex items-center justify-center rounded-lg text-on-surface-variant hover:bg-surface-container transition-all">
                    <span class="material-symbols-outlined text-sm">close</span>
                </button>
            </div>
            <form method="POST" action="{{ route('laboratory.catalog.store') }}" class="p-6 space-y-5">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-[0.65rem] font-bold uppercase tracking-widest text-on-surface-variant mb-1.5">Test Name <span class="text-error">*</span></label>
                        <input type="text" name="name" required placeholder="e.g. Complete Blood Count"
                            class="w-full px-3 py-2.5 bg-surface-container border border-outline-variant/20 rounded-lg text-sm text-on-surface focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[0.65rem] font-bold uppercase tracking-widest text-on-surface-variant mb-1.5">Test Code</label>
                        <input type="text" name="code" placeholder="e.g. CBC"
                            class="w-full px-3 py-2.5 bg-surface-container border border-outline-variant/20 rounded-lg text-sm text-on-surface focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[0.65rem] font-bold uppercase tracking-widest text-on-surface-variant mb-1.5">Category</label>
                        <input type="text" name="category" placeholder="e.g. Haematology"
                            class="w-full px-3 py-2.5 bg-surface-container border border-outline-variant/20 rounded-lg text-sm text-on-surface focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[0.65rem] font-bold uppercase tracking-widest text-on-surface-variant mb-1.5">Sample Type</label>
                        <select name="sample_type" class="w-full px-3 py-2.5 bg-surface-container border border-outline-variant/20 rounded-lg text-sm text-on-surface focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                            <option value="blood">Blood</option>
                            <option value="urine">Urine</option>
                            <option value="stool">Stool</option>
                            <option value="swab">Swab</option>
                            <option value="fluid">Fluid</option>
                            <option value="tissue">Tissue</option>
                            <option value="sputum">Sputum</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[0.65rem] font-bold uppercase tracking-widest text-on-surface-variant mb-1.5">Unit</label>
                        <input type="text" name="unit" placeholder="e.g. mg/dL, g/L"
                            class="w-full px-3 py-2.5 bg-surface-container border border-outline-variant/20 rounded-lg text-sm text-on-surface focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[0.65rem] font-bold uppercase tracking-widest text-on-surface-variant mb-1.5">Price (₹)</label>
                        <input type="number" name="price" min="0" step="0.01" value="0.00"
                            class="w-full px-3 py-2.5 bg-surface-container border border-outline-variant/20 rounded-lg text-sm text-on-surface focus:ring-2 focus:ring-primary/20 outline-none transition-all tabular-nums font-mono">
                    </div>
                    <div>
                        <label class="block text-[0.65rem] font-bold uppercase tracking-widest text-on-surface-variant mb-1.5">TAT (hours)</label>
                        <input type="number" name="turnaround_hours" min="1" value="24"
                            class="w-full px-3 py-2.5 bg-surface-container border border-outline-variant/20 rounded-lg text-sm text-on-surface focus:ring-2 focus:ring-primary/20 outline-none transition-all tabular-nums font-mono">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-[0.65rem] font-bold uppercase tracking-widest text-on-surface-variant mb-1.5">Reference Range</label>
                        <textarea name="reference_range" rows="2" placeholder="e.g. Male: 13.5–17.5 g/dL, Female: 12.0–15.5 g/dL"
                            class="w-full px-3 py-2.5 bg-surface-container border border-outline-variant/20 rounded-lg text-sm text-on-surface focus:ring-2 focus:ring-primary/20 outline-none transition-all resize-none"></textarea>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-outline-variant/10">
                    <button type="button" @click="showNewTestModal = false" class="px-5 py-2.5 text-sm font-bold text-on-surface-variant border border-outline-variant/20 rounded-xl hover:bg-surface-container transition-all">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2.5 text-sm font-bold text-on-primary rounded-xl hover:opacity-90 transition-all shadow-clinical shadow-primary/20"
                            style="background:linear-gradient(135deg,#0043c8,#0057ff);">
                        Add Test
                    </button>
                </div>
      </form>
        </div>
    </div>
</div>

    {{-- QUICK PREVIEW PANEL BACKDROP --}}
    <div x-show="showPreviewPanel" 
         x-transition:enter="transition opacity-100 ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition opacity-0 ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="showPreviewPanel = false"
         class="fixed inset-0 bg-black/40 backdrop-blur-[2px] z-[140]" x-cloak>
    </div>

    {{-- QUICK PREVIEW PANEL (Slide-over) --}}
    <div x-show="showPreviewPanel" x-cloak
         class="fixed inset-y-0 right-0 z-[150] w-full max-w-lg bg-surface-container-lowest shadow-2xl border-l border-outline-variant/15 flex flex-col"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-300 transform"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full">
        
        <div class="px-6 py-5 border-b border-outline-variant/10 flex items-center justify-between bg-surface-container/20">
            <div class="space-y-0.5">
                <p class="text-[0.65rem] font-bold uppercase tracking-widest text-on-surface-variant">Order Preview</p>
                <h3 class="text-sm font-bold text-on-surface" x-text="previewData.order_number || 'Loading...'"></h3>
            </div>
            <button @click="showPreviewPanel = false" class="w-8 h-8 flex items-center justify-center rounded-lg text-on-surface-variant hover:bg-surface-container transition-all">
                <span class="material-symbols-outlined text-sm">close</span>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-6 space-y-6">
            <div x-show="previewLoading" class="flex flex-col items-center justify-center py-20 space-y-3 opacity-50">
                <div class="w-8 h-8 border-4 border-primary/20 border-t-primary rounded-full animate-spin"></div>
                <p class="text-xs font-bold text-on-surface-variant tracking-wider uppercase">Fetching Details...</p>
            </div>

            <div x-show="!previewLoading && previewData.patient" class="space-y-6" x-cloak>
                {{-- Patient & Doctor --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-3 rounded-xl bg-surface-container/40 border border-outline-variant/10">
                        <p class="text-[0.6rem] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Patient</p>
                        <p class="text-sm font-bold text-on-surface" x-text="previewData.patient.name"></p>
                        <p class="text-[0.65rem] text-on-surface-variant" x-text="previewData.patient.phone || 'No phone'"></p>
                    </div>
                    <div class="p-3 rounded-xl bg-surface-container/40 border border-outline-variant/10">
                        <p class="text-[0.6rem] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Doctor</p>
                        <p class="text-sm font-bold text-on-surface" x-text="previewData.doctor ? previewData.doctor.name : 'Not assigned'"></p>
                        <p class="text-[0.65rem] text-on-surface-variant font-medium uppercase tracking-tighter opacity-70" x-text="previewData.department_name || 'General'"></p>
                    </div>
                </div>

                {{-- Status & Dates --}}
                <div class="space-y-3">
                    <p class="text-[0.65rem] font-bold uppercase tracking-widest text-on-surface-variant">Timeline & Status</p>
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 rounded-full text-[0.62rem] font-bold tracking-wider uppercase" 
                              :style="{ background: getStatusBadge(previewData.status).bg, color: getStatusBadge(previewData.status).text }"
                              x-text="previewData.status.replace('_', ' ')"></span>
                        <span class="text-[0.65rem] text-on-surface-variant font-medium tabular-nums" x-text="'Ordered: ' + formatDate(previewData.created_at)"></span>
                    </div>
                </div>

                {{-- Tests List --}}
                <div class="space-y-3">
                    <p class="text-[0.65rem] font-bold uppercase tracking-widest text-on-surface-variant">Order Items</p>
                    <div class="space-y-2">
                        <template x-for="item in previewData.items" :key="item.id">
                            <div class="p-4 rounded-xl border border-outline-variant/15 flex items-center justify-between bg-surface-container-lowest">
                                <div class="space-y-1">
                                    <p class="text-sm font-bold text-on-surface" x-text="item.name || item.test_name"></p>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[0.6rem] px-1.5 py-0.5 rounded bg-surface-container border border-outline-variant/10 text-on-surface-variant font-mono" x-text="item.test_code || item.code"></span>
                                        <span class="text-[0.6rem] font-bold text-primary uppercase tracking-tighter" x-text="item.status || 'Pending'"></span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold text-on-surface" x-text="'₹' + item.price"></p>
                                </div>
                            </div>
                        </template>
                        <div class="pt-3 flex items-center justify-between border-t border-outline-variant/10">
                            <p class="text-xs font-bold text-on-surface-variant">Total Amount</p>
                            <p class="text-lg font-extrabold text-on-surface" x-text="'₹' + previewData.total_amount"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 bg-surface-container/20 border-t border-outline-variant/10 flex items-center gap-3">
            <template x-if="['completed', 'ready', 'sent'].includes(previewData.status)">
                <a :href="'/laboratory/orders/' + previewData.id + '/download'" target="_blank"
                   class="flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-sm font-bold text-on-primary shadow-clinical transition-all hover:opacity-90"
                   style="background:linear-gradient(135deg,#0043c8,#0057ff);">
                    <span class="material-symbols-outlined text-base">picture_as_pdf</span>
                    Download Report
                </a>
            </template>
            <template x-if="previewData.status === 'pending'">
                <button type="button" @click="quickCollectOrder = { id: previewData.id, number: previewData.order_number, patient: previewData.patient.name }; showQuickCollect = true; showPreviewPanel = false;"
                        class="flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-sm font-bold text-on-primary shadow-clinical transition-all hover:opacity-90"
                        style="background:linear-gradient(135deg,#059669,#047857);">
                    <span class="material-symbols-outlined text-base">colorize</span>
                    Collect Sample
                </button>
            </template>
            <button @click="showPreviewPanel = false" class="px-6 py-3 rounded-xl text-sm font-bold text-on-surface-variant border border-outline-variant/20 hover:bg-surface-container transition-all">
                Close
            </button>
        </div>
    </div>

    {{-- QUICK COLLECT MODAL --}}
    <div x-show="showQuickCollect" x-cloak
         class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         @click.self="showQuickCollect = false">
        <div class="bg-surface-container-lowest rounded-xl shadow-2xl w-full max-w-md overflow-hidden border border-outline-variant/15" @click.stop>
            <div class="px-6 py-4 border-b border-outline-variant/10 flex items-center justify-between bg-surface-container/20">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl flex items-center justify-center bg-primary-fixed text-primary">
                        <span class="material-symbols-outlined text-xl" style="font-variation-settings:'FILL' 1;">colorize</span>
                    </span>
                    <div>
                        <p class="font-bold text-on-surface">Sample Collection</p>
                        <p class="text-[0.65rem] text-on-surface-variant font-bold opacity-60 uppercase tracking-widest" x-text="quickCollectOrder.number"></p>
                    </div>
                </div>
                <button @click="showQuickCollect = false" class="w-8 h-8 flex items-center justify-center rounded-lg text-on-surface-variant hover:bg-surface-container transition-all">
                    <span class="material-symbols-outlined text-sm">close</span>
                </button>
            </div>
            <form @submit.prevent="submitQuickCollection()" class="p-6 space-y-5">
                <div class="p-4 rounded-xl bg-primary-fixed/30 border border-primary/10">
                    <p class="text-xs font-bold text-primary mb-1">Patient Details</p>
                    <p class="text-sm font-bold text-on-surface" x-text="quickCollectOrder.patient"></p>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-[0.65rem] font-bold uppercase tracking-widest text-on-surface-variant mb-1.5">Sample Type <span class="text-error">*</span></label>
                        <select x-model="quickCollectData.sample_type" required 
                                class="w-full px-3 py-2.5 bg-surface-container border border-outline-variant/20 rounded-lg text-sm text-on-surface focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                            <option value="blood">Blood</option>
                            <option value="urine">Urine</option>
                            <option value="stool">Stool</option>
                            <option value="swab">Swab</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-outline-variant/10">
                    <button type="button" @click="showQuickCollect = false" class="px-5 py-2.5 text-sm font-bold text-on-surface-variant border border-outline-variant/20 rounded-xl hover:bg-surface-container transition-all">
                        Cancel
                    </button>
                    <button type="submit" :disabled="submittingCollection"
                            class="px-6 py-2.5 text-sm font-bold text-on-primary rounded-xl hover:opacity-90 transition-all shadow-clinical shadow-primary/20 flex items-center gap-2"
                            style="background:linear-gradient(135deg,#059669,#047857);">
                        <span x-show="submittingCollection" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                        <span x-text="submittingCollection ? 'Updating...' : 'Mark as Collected'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

<script>
console.log('Lab Integration dashboard loaded');

window.labDashboard = function labDashboard(cfg) {
    const initData = cfg || {};
    return {
        ...initData,
        showOrderModal: false,
        showNewTestModal: false,
        showPreviewPanel: false,
        previewLoading: false,
        previewData: {},
        showQuickCollect: false,
        submittingCollection: false,
        quickCollectOrder: { id: null, number: '', patient: '' },
        quickCollectData: {
            sample_type: 'blood',
            accession_number: '',
            collection_notes: ''
        },
        testSearch: '',
        availableTests: [],
        testsLoading: false,
        testsFetchError: '',
        orderForm: {
            patient_id: '',
            visit_id: null,
            provider: '',
            tests: [],
            sample_collection_type: 'lab',
            collection_date: '',
            collection_address: '',
        },

        get orderTotal() {
            return this.orderForm.tests.reduce((sum, t) => sum + t.price, 0);
        },

        get canSubmit() {
            return this.orderForm.patient_id && this.orderForm.provider && this.orderForm.provider !== 'inhouse' && this.orderForm.tests.length > 0;
        },

        get groupedTestEntries() {
            const groups = {};
            const list = Array.isArray(this.availableTests) ? this.availableTests : [];
            list.forEach((t) => {
                if (!t || !t.code) return;
                const c = (t.category && String(t.category).trim()) ? String(t.category).trim() : 'General';
                if (!groups[c]) groups[c] = [];
                groups[c].push(t);
            });
            return Object.entries(groups).sort((a, b) => a[0].localeCompare(b[0]));
        },

        selectProvider(provider) {
            this.orderForm.provider = provider;
            this.showOrderModal = true;
            this.onProviderChange();
        },

        onProviderChange() {
            this.testSearch = '';
            this.orderForm.tests = [];
            this.testsFetchError = '';
            if (!this.orderForm.provider) { this.availableTests = []; return; }
            if (this.orderForm.provider === 'inhouse') { this.availableTests = []; return; }
            this.loadTests();
        },

        goToInHouseLab() {
            const base = this.inHouseOrdersUrl;
            if (!base) return;
            const pid = this.orderForm.patient_id;
            const url = pid ? base + (base.indexOf('?') >= 0 ? '&' : '?') + 'patient_id=' + encodeURIComponent(pid) : base;
            window.location.href = url;
        },

        async loadTests() {
            if (!this.orderForm.provider || this.orderForm.provider === 'inhouse') { this.availableTests = []; return; }
            this.testsLoading = true;
            this.testsFetchError = '';
            try {
                const url = '/lab/tests/' + encodeURIComponent(this.orderForm.provider);
                const response = await fetch(url, { credentials: 'same-origin', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                const data = await this.parseCatalogJson(response);
                if (!response.ok) { this.testsFetchError = data.error || ('HTTP ' + response.status); this.availableTests = []; return; }
                if (data.success && Array.isArray(data.tests)) { this.availableTests = data.tests; }
                else { this.availableTests = []; this.testsFetchError = (data && data.error) ? String(data.error) : 'Catalog response was empty or invalid.'; }
            } catch (e) { this.availableTests = []; this.testsFetchError = 'Could not load tests. Check network or refresh the page.'; }
            finally { this.testsLoading = false; }
        },

        async searchTests() {
            if (!this.orderForm.provider || this.orderForm.provider === 'inhouse') return;
            this.testsLoading = true;
            this.testsFetchError = '';
            try {
                const url = '/lab/tests/' + encodeURIComponent(this.orderForm.provider) + '?search=' + encodeURIComponent(this.testSearch || '');
                const response = await fetch(url, { credentials: 'same-origin', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                const data = await this.parseCatalogJson(response);
                if (!response.ok) { this.testsFetchError = data.error || ('HTTP ' + response.status); this.availableTests = []; return; }
                if (data.success && Array.isArray(data.tests)) { this.availableTests = data.tests; }
                else { this.availableTests = []; }
            } catch (e) { this.availableTests = []; }
            finally { this.testsLoading = false; }
        },

        async parseCatalogJson(response) {
            const text = await response.text();
            try { return text ? JSON.parse(text) : {}; }
            catch (e) { return { success: false, error: 'Server returned non-JSON (login page or error HTML).' }; }
        },

        addTest(test) {
            if (!this.orderForm.tests.find(t => t.code === test.code)) { this.orderForm.tests.push(test); }
        },

        removeTest(idx) { this.orderForm.tests.splice(idx, 1); },

        async previewOrderDetails(id) {
            this.showPreviewPanel = true;
            this.previewLoading = true;
            this.previewData = { patient: {}, items: [] };
            try {
                const response = await fetch('/laboratory/orders/' + id + '/details-json', { headers: { 'Accept': 'application/json' } });
                const data = await response.json();
                if (data.success) { this.previewData = data.order; }
                else { window.clinicToast('Could not load order details', 'error'); this.showPreviewPanel = false; }
            } catch (e) { window.clinicToast('Network error loading details', 'error'); this.showPreviewPanel = false; }
            finally { this.previewLoading = false; }
        },

        async shareOnWhatsApp(id) {
            try {
                window.clinicToast('Sending report to patient via WhatsApp...', 'info');
                const response = await fetch(`/laboratory/orders/${id}/share-whatsapp`, {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json', 
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 
                        'Accept': 'application/json' 
                    }
                });
                const data = await response.json();
                if (data.success) {
                    window.clinicToast('Report shared successfully!', 'success');
                } else {
                    window.clinicToast(data.error || 'Failed to share report', 'error');
                }
            } catch (e) {
                window.clinicToast('Network error while sharing', 'error');
            }
        },

        async submitQuickCollection() {
            if (!this.quickCollectOrder.id) return;
            this.submittingCollection = true;
            try {
                const response = await fetch('/laboratory/orders/' + this.quickCollectOrder.id + '/collect-sample', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                    body: JSON.stringify(this.quickCollectData),
                });
                const data = await response.json();
                if (data.success) {
                    window.clinicToast('Sample collected successfully!', 'success');
                    this.showQuickCollect = false;
                    setTimeout(() => location.reload(), 1000);
                } else {
                    window.clinicToast('Error: ' + (data.error || 'Check input'), 'error');
                }
            } catch (e) { window.clinicToast('Failed to record collection', 'error'); }
            finally { this.submittingCollection = false; }
        },

        getStatusBadge(status) {
            const badges = {
                'pending': { bg: '#f1f5f9', text: '#475569' },
                'sample_collected': { bg: '#dce1ff', text: '#0043c8' },
                'processing': { bg: '#fef3c7', text: '#92400e' },
                'ready': { bg: '#dcfce7', text: '#166534' },
                'completed': { bg: '#d1fae5', text: '#065f46' },
                'approved': { bg: '#d1fae5', text: '#065f46' },
                'default': { bg: '#f1f5f9', text: '#475569' }
            };
            return badges[status] || badges['default'];
        },

        formatDate(dateStr) {
            if (!dateStr) return '';
            const d = new Date(dateStr);
            return d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' });
        },

        async submitOrder() {
            try {
                const response = await fetch('/lab/orders', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify(this.orderForm),
                });
                const data = await response.json();
                if (data.success) { window.clinicToast('Order created: ' + data.order_number, 'success'); this.showOrderModal = false; location.reload(); }
                else { window.clinicToast('Error: ' + data.error, 'error'); }
            } catch (error) { window.clinicToast('Failed to create order', 'error'); }
        },
    };
};
</script>
@endsection
