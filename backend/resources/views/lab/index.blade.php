@extends('layouts.app')

@section('title', 'Lab Integration')

@section('breadcrumb', 'Lab Orders')

@section('content')
@php
    $labDashboardInit = [
        'inHouseOrdersUrl' => \Illuminate\Support\Facades\Route::has('laboratory.orders') ? route('laboratory.orders') : '',
    ];
@endphp
{{-- Single-quoted x-data so @json() does not break the attribute (double quotes inside x-data="..." truncate HTML) --}}
<div x-data='labDashboard(@json($labDashboardInit))' class="p-6 space-y-6">
    @if(isset($labSchemaReady) && !$labSchemaReady)
    <div class="rounded-xl border border-amber-200 bg-amber-50 text-amber-900 px-4 py-3 text-sm">
        Lab orders table is missing. Run <code class="bg-amber-100 px-1 rounded">php artisan migrate</code> to enable this module.
    </div>
    @endif

    {{-- Hospital workflow shortcuts (LIS + external lab) --}}
    <div class="flex flex-wrap items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm">
        <span class="text-xs font-bold uppercase tracking-wide text-gray-400 mr-1">Workflow</span>
        @if(\Illuminate\Support\Facades\Route::has('opd.queue'))
            <a href="{{ route('opd.queue') }}" class="px-3 py-1.5 rounded-lg font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-100">OPD Queue</a>
        @endif
        @if(\Illuminate\Support\Facades\Route::has('ipd.index'))
            <a href="{{ route('ipd.index') }}" class="px-3 py-1.5 rounded-lg font-medium text-slate-700 bg-slate-50 hover:bg-slate-100 border border-slate-200">IPD</a>
        @endif
        @if(\Illuminate\Support\Facades\Route::has('laboratory.orders'))
            <a href="{{ route('laboratory.orders') }}" class="px-3 py-1.5 rounded-lg font-medium text-teal-800 bg-teal-50 hover:bg-teal-100 border border-teal-100">In-house lab orders</a>
        @endif
        @if(\Illuminate\Support\Facades\Route::has('pharmacy.dispense.form'))
            <a href="{{ route('pharmacy.dispense.form') }}" class="px-3 py-1.5 rounded-lg font-medium text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-100">Pharmacy</a>
        @endif
        @if(\Illuminate\Support\Facades\Route::has('billing.index'))
            <a href="{{ route('billing.index') }}" class="px-3 py-1.5 rounded-lg font-medium text-blue-800 bg-blue-50 hover:bg-blue-100 border border-blue-100">Billing</a>
        @endif
    </div>

    {{-- Header --}}
    <div class="bg-gradient-to-r from-teal-500 to-cyan-600 rounded-2xl p-6 text-white">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold">Lab Integration</h1>
                <p class="text-white/80 mt-1">In-house LIS or external reference labs</p>
            </div>
            <button type="button" @click.prevent="showOrderModal = true; console.log('[lab.index] New Lab Order open')" class="ml-auto px-6 py-3 bg-white text-teal-600 font-semibold rounded-xl hover:bg-teal-50 transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                New Lab Order
            </button>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-2xl">📝</div>
                <div>
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['orders_today'] }}</div>
                    <div class="text-sm text-gray-500">Orders Today</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center text-2xl">⏳</div>
                <div>
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['pending_results'] }}</div>
                    <div class="text-sm text-gray-500">Awaiting Results</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center text-2xl">✅</div>
                <div>
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['results_received'] }}</div>
                    <div class="text-sm text-gray-500">Results This Month</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Lab Providers --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="p-4 border-b border-gray-100 bg-gray-50">
            <h2 class="font-semibold text-gray-900">Lab providers</h2>
            <p class="text-xs text-gray-500 mt-0.5">Choose in-house LIS or a connected reference lab</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                @if(\Illuminate\Support\Facades\Route::has('laboratory.orders'))
                <a href="{{ route('laboratory.orders') }}" class="border-2 border-teal-400 bg-teal-50/50 rounded-lg p-4 text-center hover:bg-teal-50 transition-colors ring-1 ring-teal-200">
                    <div class="w-12 h-12 mx-auto rounded-lg bg-teal-600 flex items-center justify-center text-white font-bold text-xs mb-2">IN</div>
                    <div class="text-sm font-semibold text-teal-900">In-house lab</div>
                    <div class="text-[11px] text-teal-700 mt-1">Hospital LIS</div>
                </a>
                @endif
                @php
                    $labs = [
                        ['name' => 'Dr. Lal PathLabs', 'code' => 'lal_pathlabs', 'color' => 'bg-red-500'],
                        ['name' => 'SRL Diagnostics', 'code' => 'srl', 'color' => 'bg-blue-600'],
                        ['name' => 'Thyrocare', 'code' => 'thyrocare', 'color' => 'bg-purple-600'],
                        ['name' => 'Metropolis', 'code' => 'metropolis', 'color' => 'bg-green-600'],
                        ['name' => 'Pathkind Labs', 'code' => 'pathkind', 'color' => 'bg-orange-500'],
                    ];
                @endphp
                @foreach($labs as $lab)
                <div class="border border-gray-200 rounded-lg p-4 text-center hover:border-teal-300 transition-colors cursor-pointer" @click="selectProvider('{{ $lab['code'] }}')">
                    <div class="w-12 h-12 mx-auto rounded-lg {{ $lab['color'] }} flex items-center justify-center text-white font-bold text-sm mb-2">
                        {{ substr($lab['name'], 0, 2) }}
                    </div>
                    <div class="text-sm font-medium text-gray-900">{{ $lab['name'] }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Recent Orders --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h2 class="font-semibold text-gray-900">Recent Lab Orders</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Order #</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Patient</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Lab</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tests</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentOrders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <span class="font-mono text-sm font-medium text-teal-600">{{ $order->order_number }}</span>
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $order->patient_name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $order->provider_name }}</td>
                        <td class="px-4 py-3">
                            @php $tests = json_decode($order->tests, true) ?? []; @endphp
                            <span class="text-sm text-gray-600">{{ count($tests) }} tests</span>
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-900">₹{{ number_format($order->total_amount) }}</td>
                        <td class="px-4 py-3">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-gray-100 text-gray-700',
                                    'sample_collected' => 'bg-blue-100 text-blue-700',
                                    'processing' => 'bg-yellow-100 text-yellow-700',
                                    'completed' => 'bg-green-100 text-green-700',
                                    'cancelled' => 'bg-red-100 text-red-700',
                                ];
                            @endphp
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusColors[$order->status] ?? 'bg-gray-100' }}">
                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($order->created_at)->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $pKey = strtolower((string) \Illuminate\Support\Arr::get((array) $order, 'provider', ''));
                                $pName = trim((string) \Illuminate\Support\Arr::get((array) $order, 'provider_name', ''));
                                $externalCodes = ['lal_pathlabs','srl','thyrocare','metropolis','pathkind'];
                                $isExternalLab = in_array($pKey, $externalCodes, true)
                                    || ($pName !== '' && ! in_array($pKey, ['inhouse','internal','hospital','lis'], true));

                                $labViewUrl = null;
                                $labViewTitle = 'View order';
                                if ($isExternalLab) {
                                    // External: order status JSON; PDF uses the green download icon.
                                    if (\Illuminate\Support\Facades\Route::has('lab.orders.status')) {
                                        $labViewUrl = route('lab.orders.status', $order->id);
                                        $labViewTitle = 'View order details';
                                    } elseif (! empty(\Illuminate\Support\Arr::get((array) $order, 'result_url')) && \Illuminate\Support\Facades\Route::has('lab.orders.download')) {
                                        $labViewUrl = route('lab.orders.download', $order->id);
                                        $labViewTitle = 'View / download result';
                                    }
                                } else {
                                    $st = (string) \Illuminate\Support\Arr::get((array) $order, 'status', '');
                                    if (\Illuminate\Support\Facades\Route::has('laboratory.orders.report') && in_array($st, ['completed','ready','sent'], true)) {
                                        $labViewUrl = route('laboratory.orders.report', $order->id);
                                        $labViewTitle = 'View report';
                                    } elseif (\Illuminate\Support\Facades\Route::has('laboratory.result-entry')) {
                                        $labViewUrl = route('laboratory.result-entry', $order->id);
                                        $labViewTitle = 'Open order & results';
                                    } elseif (\Illuminate\Support\Facades\Route::has('laboratory.orders')) {
                                        $labViewUrl = route('laboratory.orders');
                                        $labViewTitle = 'In-house lab orders';
                                    }
                                }
                            @endphp
                            <div class="flex gap-2">
                                @if(\Illuminate\Support\Arr::get((array) $order, 'result_url'))
                                <a href="{{ \Illuminate\Support\Facades\Route::has('lab.orders.download') ? route('lab.orders.download', $order->id) : '/lab/orders/'.$order->id.'/download' }}" class="p-2 text-green-600 hover:bg-green-50 rounded-lg" title="Download Result">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </a>
                                @endif
                                @if($labViewUrl)
                                <a href="{{ $labViewUrl }}" class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg" title="{{ $labViewTitle }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                @else
                                <span class="p-2 text-gray-300 cursor-not-allowed rounded-lg" title="No view link for this order">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-gray-500">
                            No lab orders yet. Create your first order.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- New Order Modal --}}
    <div x-show="showOrderModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50" @click="showOrderModal = false"></div>
            <div class="relative bg-white rounded-2xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Create Lab Order</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Patient</label>
                            <select x-model="orderForm.patient_id" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                                <option value="">Select patient</option>
                                @foreach($patients ?? [] as $patient)
                                <option value="{{ $patient->id }}">{{ $patient->name }}@if($patient->phone) — {{ $patient->phone }}@endif</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lab Provider</label>
                            <select x-model="orderForm.provider" @change="onProviderChange()" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
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

                    <div x-show="orderForm.provider === 'inhouse'" x-cloak class="rounded-xl border border-teal-200 bg-teal-50/80 px-4 py-3 text-sm text-teal-900">
                        <p class="font-semibold text-teal-950">In-house lab orders</p>
                        <p class="mt-1 text-teal-800/90">External catalog search does not apply. Open the hospital lab workbench to pick tests from your catalog and create the order.</p>
                        <button type="button" @click="goToInHouseLab()" class="mt-3 inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-teal-600 text-white text-sm font-semibold hover:bg-teal-700">
                            Go to in-house lab orders
                        </button>
                    </div>

                    <div x-show="orderForm.provider && orderForm.provider !== 'inhouse'" class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Search tests or category</label>
                        <input type="text" x-model="testSearch" @input.debounce.300ms="searchTests()" placeholder="e.g. CBC, Lipid, Hematology…" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                        <p class="text-xs text-gray-400">Tests are grouped by category. Pick from the list below.</p>

                        <div x-show="testsLoading" class="rounded-lg border border-dashed border-gray-200 px-4 py-6 text-center text-sm text-gray-500">
                            Loading test catalog…
                        </div>
                        <div x-show="!testsLoading && testsFetchError" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" x-text="testsFetchError"></div>
                        <div x-show="!testsLoading && !testsFetchError && !availableTests.length" class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                            No tests match your search. Clear the box above to see all categories.
                        </div>
                        <div x-show="!testsLoading && !testsFetchError && availableTests.length" class="border border-gray-200 rounded-lg max-h-64 overflow-y-auto bg-white shadow-sm">
                            <template x-for="[cat, items] in groupedTestEntries" :key="cat">
                                <div class="border-b border-gray-100 last:border-b-0">
                                    <div class="px-4 py-2 bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wide" x-text="cat"></div>
                                    <template x-for="test in items" :key="test.code + '-' + cat">
                                        <div class="flex items-center justify-between px-4 py-2.5 hover:bg-teal-50/60 cursor-pointer border-t border-gray-50 first:border-t-0" @click="addTest(test)">
                                            <div>
                                                <div class="font-medium text-sm text-gray-900" x-text="test.name"></div>
                                                <div class="text-xs text-gray-500" x-text="test.code"></div>
                                            </div>
                                            <div class="text-sm font-semibold text-teal-600" x-text="'₹' + (test.price ?? 0)"></div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div x-show="orderForm.provider && orderForm.provider !== 'inhouse' && orderForm.tests.length">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Selected Tests</label>
                        <div class="space-y-2">
                            <template x-for="(test, idx) in orderForm.tests" :key="idx">
                                <div class="flex items-center justify-between px-3 py-2 bg-teal-50 rounded-lg">
                                    <span class="text-sm font-medium" x-text="test.name"></span>
                                    <div class="flex items-center gap-3">
                                        <span class="text-sm text-teal-600" x-text="'₹' + test.price"></span>
                                        <button @click="removeTest(idx)" class="text-red-500 hover:text-red-600">×</button>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <div class="mt-2 text-right font-semibold text-gray-900" x-text="'Total: ₹' + orderTotal"></div>
                    </div>

                    <div class="grid grid-cols-2 gap-4" x-show="orderForm.provider && orderForm.provider !== 'inhouse'">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sample Collection</label>
                            <select x-model="orderForm.sample_collection_type" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                                <option value="lab">At Lab</option>
                                <option value="home">Home Collection</option>
                            </select>
                        </div>
                        <div x-show="orderForm.sample_collection_type === 'home'">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Collection Date</label>
                            <input type="date" x-model="orderForm.collection_date" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                        </div>
                    </div>
                </div>
                <div class="p-6 border-t border-gray-100 flex justify-end gap-3">
                    <button type="button" @click="showOrderModal = false" class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50">Cancel</button>
                    <button type="button" x-show="orderForm.provider && orderForm.provider !== 'inhouse'" @click="submitOrder()" :disabled="!canSubmit" class="px-4 py-2 bg-teal-600 text-white font-medium rounded-lg hover:bg-teal-700 disabled:opacity-50">
                        Create Order
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
console.log('Lab Integration dashboard loaded');

window.labDashboard = function labDashboard(cfg) {
    cfg = cfg || {};
    return {
        inHouseOrdersUrl: cfg.inHouseOrdersUrl || '',
        showOrderModal: false,
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
            if (!this.orderForm.provider) {
                this.availableTests = [];
                return;
            }
            if (this.orderForm.provider === 'inhouse') {
                this.availableTests = [];
                console.log('[lab.index] onProviderChange in-house');
                return;
            }
            this.loadTests();
        },

        goToInHouseLab() {
            const base = this.inHouseOrdersUrl;
            if (!base) {
                console.warn('[lab.index] goToInHouseLab: missing URL');
                return;
            }
            const pid = this.orderForm.patient_id;
            const url = pid ? base + (base.indexOf('?') >= 0 ? '&' : '?') + 'patient_id=' + encodeURIComponent(pid) : base;
            console.log('[lab.index] goToInHouseLab', { url });
            window.location.href = url;
        },

        async loadTests() {
            if (!this.orderForm.provider || this.orderForm.provider === 'inhouse') {
                this.availableTests = [];
                return;
            }
            this.testsLoading = true;
            this.testsFetchError = '';
            try {
                const url = '/lab/tests/' + encodeURIComponent(this.orderForm.provider);
                const response = await fetch(url, {
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
                const data = await this.parseCatalogJson(response);
                if (!response.ok) {
                    this.testsFetchError = data.error || ('HTTP ' + response.status + ' — could not load catalog');
                    this.availableTests = [];
                    console.warn('[lab.index] loadTests HTTP error', { url, status: response.status, data });
                    return;
                }
                if (data.success && Array.isArray(data.tests)) {
                    this.availableTests = data.tests;
                } else {
                    this.availableTests = [];
                    this.testsFetchError = (data && data.error) ? String(data.error) : 'Catalog response was empty or invalid.';
                }
                console.log('[lab.index] loadTests ok', { provider: this.orderForm.provider, count: this.availableTests.length });
            } catch (e) {
                this.availableTests = [];
                this.testsFetchError = 'Could not load tests. Check network or refresh the page.';
                console.error('[lab.index] loadTests exception', e);
            } finally {
                this.testsLoading = false;
            }
        },

        async searchTests() {
            if (!this.orderForm.provider || this.orderForm.provider === 'inhouse') return;
            this.testsLoading = true;
            this.testsFetchError = '';
            try {
                const url = '/lab/tests/' + encodeURIComponent(this.orderForm.provider) + '?search=' + encodeURIComponent(this.testSearch || '');
                const response = await fetch(url, {
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
                const data = await this.parseCatalogJson(response);
                if (!response.ok) {
                    this.testsFetchError = data.error || ('HTTP ' + response.status);
                    this.availableTests = [];
                    return;
                }
                if (data.success && Array.isArray(data.tests)) {
                    this.availableTests = data.tests;
                } else {
                    this.availableTests = [];
                }
                console.log('[lab.index] searchTests', { q: this.testSearch, count: this.availableTests.length });
            } catch (e) {
                console.error('[lab.index] searchTests', e);
                this.availableTests = [];
            } finally {
                this.testsLoading = false;
            }
        },

        async parseCatalogJson(response) {
            const text = await response.text();
            try {
                return text ? JSON.parse(text) : {};
            } catch (e) {
                console.error('[lab.index] Non-JSON catalog response', text.slice(0, 400));
                return { success: false, error: 'Server returned non-JSON (login page or error HTML).' };
            }
        },

        addTest(test) {
            if (!this.orderForm.tests.find(t => t.code === test.code)) {
                this.orderForm.tests.push(test);
            }
        },

        removeTest(idx) {
            this.orderForm.tests.splice(idx, 1);
        },

        async submitOrder() {
            try {
                console.log('LabIntegration submitOrder payload', JSON.parse(JSON.stringify(this.orderForm)));
                const response = await fetch('/lab/orders', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(this.orderForm),
                });

                const data = await response.json();

                if (data.success) {
                    alert('Order created: ' + data.order_number);
                    this.showOrderModal = false;
                    location.reload();
                } else {
                    alert('Error: ' + data.error);
                }
            } catch (error) {
                console.error('Order error:', error);
                alert('Failed to create order');
            }
        },
    };
};
</script>
@endsection
