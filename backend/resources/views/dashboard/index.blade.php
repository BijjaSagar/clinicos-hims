@extends('layouts.app')

@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')
@php
    \Illuminate\Support\Facades\Log::info('dashboard.index.view', [
        'clinic_id' => auth()->user()->clinic_id ?? null,
        'appointments' => isset($appointments) ? count($appointments) : 0,
    ]);
    $formatShortRupee = static function (float $n): string {
        if ($n >= 100000) {
            return '₹'.number_format($n / 100000, 2).'L';
        }
        if ($n >= 1000) {
            return '₹'.number_format($n / 1000, 1).'k';
        }

        return '₹'.number_format($n, 0);
    };
    $weekTotal = $weekRevenue['total'] ?? 0;
    $deltaPatients = $stats['patients_delta'] ?? 0;
    $revPct = $stats['revenue_delta_pct'];
@endphp

<div class="p-4 sm:p-5 lg:p-7 space-y-4 sm:space-y-5">

    {{-- ── ABDM Compliance Banner ── --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-4 rounded-xl px-4 py-3.5 sm:px-5 sm:py-4"
         style="background:linear-gradient(135deg,#0d1117 0%,#0d1f3c 100%);">
        <div class="flex items-start gap-3 sm:items-center min-w-0 flex-1">
        <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl flex items-center justify-center flex-shrink-0"
             style="background:rgba(20,71,230,.2);border:1px solid rgba(20,71,230,.3);">
            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
        </div>
        <div class="min-w-0">
            <h4 class="text-white font-semibold text-sm">ABDM Compliance Active</h4>
            <p class="text-xs mt-0.5 leading-relaxed" style="color:#64748b;">
                ABHA creation live · HFR registered · FHIR R4 records syncing ·
                {{ (int) ($stats['abdm_records'] ?? 0) }} records shared this month
            </p>
        </div>
        </div>
        <div class="flex flex-wrap items-center gap-2 sm:ml-auto sm:flex-shrink-0 sm:justify-end">
            <span class="px-2.5 sm:px-3 py-1 rounded-full text-[10px] sm:text-xs font-semibold" style="background:rgba(5,150,105,.15);color:#6ee7b7;">M1 ✓ Live</span>
            <span class="px-2.5 sm:px-3 py-1 rounded-full text-[10px] sm:text-xs font-semibold" style="background:rgba(5,150,105,.15);color:#6ee7b7;">HFR ✓</span>
            <span class="px-2.5 sm:px-3 py-1 rounded-full text-[10px] sm:text-xs font-semibold" style="background:#1e2535;color:#94a3b8;">M2 In Progress</span>
        </div>
    </div>

    {{-- ── KPI STAT CARDS ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white border border-gray-200 rounded-xl p-3 sm:p-5 min-w-0">
            <p class="text-[10px] sm:text-xs font-medium text-gray-400 mb-1.5 sm:mb-2 truncate">Today's Patients</p>
            <p class="font-display font-extrabold text-2xl sm:text-3xl text-gray-900 leading-none tabular-nums">{{ (int) ($stats['today_patients'] ?? 0) }}</p>
            <div class="flex items-center gap-2 mt-2">
                @if($deltaPatients !== 0)
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $deltaPatients > 0 ? '' : 'bg-gray-100 text-gray-600' }}" style="{{ $deltaPatients > 0 ? 'background:#ecfdf5;color:#059669;' : '' }}">{{ $deltaPatients > 0 ? '+' : '' }}{{ $deltaPatients }}</span>
                @else
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-gray-100 text-gray-500">0</span>
                @endif
                <span class="text-xs text-gray-400">vs yesterday</span>
            </div>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-3 sm:p-5 min-w-0">
            <p class="text-[10px] sm:text-xs font-medium text-gray-400 mb-1.5 sm:mb-2 truncate">Today's Revenue</p>
            <p class="font-display font-extrabold text-xl sm:text-3xl text-gray-900 leading-none tabular-nums break-all">₹{{ number_format((float) ($stats['revenue'] ?? 0), 0) }}</p>
            <div class="flex items-center gap-2 mt-2">
                @if($revPct !== null)
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $revPct >= 0 ? '' : 'bg-red-50 text-red-600' }}" style="{{ $revPct >= 0 ? 'background:#ecfdf5;color:#059669;' : '' }}">{{ $revPct >= 0 ? '+' : '' }}{{ $revPct }}%</span>
                @else
                    <span class="text-xs text-gray-400">—</span>
                @endif
                <span class="text-xs text-gray-400">vs yesterday</span>
            </div>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-3 sm:p-5 min-w-0">
            <p class="text-[10px] sm:text-xs font-medium text-gray-400 mb-1.5 sm:mb-2 truncate">Pending Collections</p>
            <p class="font-display font-extrabold text-xl sm:text-3xl text-gray-900 leading-none tabular-nums break-all">₹{{ number_format((float) ($stats['pending_dues'] ?? 0), 0) }}</p>
            <div class="flex items-center gap-2 mt-2">
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">{{ (int) ($stats['pending_invoice_count'] ?? 0) }} invoices</span>
                <span class="text-xs text-gray-400">outstanding</span>
            </div>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-3 sm:p-5 min-w-0">
            <p class="text-[10px] sm:text-xs font-medium text-gray-400 mb-1.5 sm:mb-2 truncate">Queue Now</p>
            <p class="font-display font-extrabold text-2xl sm:text-3xl text-gray-900 leading-none tabular-nums">{{ (int) ($stats['queue_count'] ?? 0) }}</p>
            <div class="flex items-center gap-2 mt-2">
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full" style="background:#fff7ed;color:#d97706;">{{ (int) ($stats['waiting_count'] ?? 0) }} waiting</span>
                <span class="text-xs text-gray-400">in clinic</span>
            </div>
        </div>
    </div>

    {{-- ── SCHEDULE + QUEUE/WHATSAPP ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Today's Schedule --}}
        <div class="lg:col-span-2 bg-white border border-gray-200 rounded-xl overflow-hidden" x-data="{ filter: 'all' }">
            <div class="px-3 sm:px-5 py-3 sm:py-4 border-b border-gray-100 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h3 class="text-sm font-bold text-gray-900 shrink-0">Today's Schedule</h3>
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3 w-full sm:w-auto sm:ml-auto min-w-0">
                    <div class="flex gap-1 bg-gray-100 rounded-lg p-1 overflow-x-auto">
                        <button @click="filter='all'"
                                :class="filter==='all' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500'"
                                class="px-2.5 py-1 rounded-md text-xs font-semibold transition-all">All</button>
                        <button @click="filter='waiting'"
                                :class="filter==='waiting' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500'"
                                class="px-2.5 py-1 rounded-md text-xs font-semibold transition-all">Waiting</button>
                        <button @click="filter='done'"
                                :class="filter==='done' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500'"
                                class="px-2.5 py-1 rounded-md text-xs font-semibold transition-all">Done</button>
                    </div>
                    <a href="{{ route('schedule') }}" class="text-xs font-semibold" style="color:#1447E6;">Full calendar →</a>
                </div>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($appointments ?? [] as $apt)
                @php
                    $statusMap = [
                        'in-consultation' => ['label'=>'In Consultation','bg'=>'#ecfdf5','color'=>'#059669'],
                        'waiting'         => ['label'=>'Waiting · Token '.($apt['token']??''),'bg'=>'#fffbeb','color'=>'#d97706'],
                        'confirmed'       => ['label'=>'Confirmed','bg'=>'#eff3ff','color'=>'#1447e6'],
                        'done'            => ['label'=>'Done','bg'=>'#f1f5f9','color'=>'#9ca3af'],
                        'no-show'         => ['label'=>'No-show','bg'=>'#fff1f2','color'=>'#dc2626'],
                        'booked'          => ['label'=>'Booked','bg'=>'#f0f9ff','color'=>'#0891b2'],
                    ];
                    $s = $statusMap[$apt['status']] ?? $statusMap['booked'];
                    $opacity = in_array($apt['status'], ['done','no-show']) ? 'opacity-60' : '';
                @endphp
                <a href="{{ $apt['url'] ?? route('schedule') }}" class="flex flex-wrap sm:flex-nowrap items-center gap-2 sm:gap-3 px-3 sm:px-5 py-3 hover:bg-gray-50 transition-colors cursor-pointer min-w-0 {{ $opacity }}
                            {{ $apt['status']==='in-consultation' ? 'bg-green-50/50' : '' }}">
                    <span class="text-xs font-semibold text-gray-400 w-10 sm:w-12 flex-shrink-0 text-right">{{ $apt['time'] }}</span>
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-xs flex-shrink-0"
                         style="background:linear-gradient(135deg,{{ $apt['gradient'] ?? '#1447e6,#0891b2' }})">
                        {{ $apt['initials'] }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $apt['name'] }}</p>
                        <p class="text-xs text-gray-400 truncate mt-0.5">{{ $apt['type'] }}</p>
                    </div>
                    <span class="flex-shrink-0 text-[10px] sm:text-xs font-semibold px-2 sm:px-2.5 py-1 rounded-full max-w-full truncate sm:max-w-[14rem] sm:whitespace-normal"
                          style="background:{{ $s['bg'] }};color:{{ $s['color'] }};">{{ $s['label'] }}</span>
                </a>
                @empty
                <div class="px-5 py-12 text-center text-sm text-gray-500">No appointments scheduled for today.</div>
                @endforelse
            </div>
        </div>

        {{-- Queue + WhatsApp --}}
        <div class="flex flex-col gap-4">

            {{-- Live Queue --}}
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-900">Live Queue</h3>
                    @if(\Illuminate\Support\Facades\Route::has('opd.queue'))
                    <a href="{{ route('opd.queue') }}" class="text-xs font-semibold" style="color:#1447E6;">Manage →</a>
                    @endif
                </div>
                <div class="p-4">
                    <div class="rounded-xl p-4 text-center mb-3"
                         style="background:linear-gradient(135deg,#1447E6 0%,#0891B2 100%);">
                        <p class="text-xs font-semibold uppercase tracking-widest mb-1" style="color:rgba(255,255,255,.7);">Now Serving</p>
                        <p class="font-display font-extrabold text-5xl text-white leading-none">{{ $stats['current_token'] ?? '—' }}</p>
                        <p class="text-xs mt-1.5" style="color:rgba(255,255,255,.75);">{{ $stats['current_patient'] ?? '—' }}</p>
                    </div>
                    <div class="space-y-1.5">
                        @forelse($queue ?? [] as $q)
                        <div class="flex items-center gap-2.5 px-3 py-2 rounded-lg bg-gray-50 {{ ($q['dim']??false) ? 'opacity-50' : '' }}">
                            <div class="w-7 h-7 rounded-md bg-white border border-gray-200 flex items-center justify-center text-xs font-bold text-gray-600 flex-shrink-0">
                                {{ $q['num'] }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-gray-900 truncate">{{ $q['name'] }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ $q['type'] }}</p>
                            </div>
                            <span class="text-xs text-gray-400 flex-shrink-0">{{ $q['wait'] }}</span>
                        </div>
                        @empty
                        <p class="text-xs text-gray-500 text-center py-4">No patients in queue right now.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- WhatsApp Activity --}}
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-900">WhatsApp Activity</h3>
                    @if(\Illuminate\Support\Facades\Route::has('whatsapp.index'))
                    <a href="{{ route('whatsapp.index') }}" class="text-xs font-semibold" style="color:#1447E6;">View all →</a>
                    @endif
                </div>
                <div class="px-4 py-3 space-y-2">
                    @forelse($whatsapp ?? [] as $wa)
                    <div class="flex items-start gap-2.5 px-3 py-2.5 rounded-lg {{ $wa['status']==='unread' ? 'bg-orange-50' : 'bg-gray-50' }}">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-white text-xs flex-shrink-0"
                             style="background:#25D366;">💬</div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-gray-900 truncate">
                                {{ $wa['status']==='unread' ? 'Reply — ' : 'Sent — ' }}{{ $wa['name'] }}
                            </p>
                            <p class="text-xs text-gray-400 truncate mt-0.5">{{ $wa['msg'] }}</p>
                            @if($wa['status']==='unread')
                                <p class="text-xs font-semibold mt-0.5" style="color:#d97706;">● Needs reply</p>
                            @else
                                <p class="text-xs font-semibold mt-0.5" style="color:#25D366;">✓✓ Delivered</p>
                            @endif
                        </div>
                        <span class="text-xs text-gray-400 flex-shrink-0">{{ $wa['time'] }}</span>
                    </div>
                    @empty
                    <p class="text-xs text-gray-500 text-center py-6">No recent WhatsApp messages.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    {{-- ── REVENUE CHART + RECENT INVOICES + SIDEBAR ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Revenue Bar Chart --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between gap-2">
                <h3 class="text-sm font-bold text-gray-900">Revenue — This Week</h3>
                <span class="font-display font-extrabold text-lg text-gray-900 tabular-nums">{{ $formatShortRupee((float) $weekTotal) }}</span>
            </div>
            <div class="p-5">
                <div style="height:140px;position:relative;">
                    <canvas id="revenueChart"></canvas>
                </div>
                <div class="flex flex-wrap gap-4 mt-4 pt-4 border-t border-gray-100">
                    <div>
                        <p class="text-xs text-gray-400">Collected</p>
                        <p class="text-sm font-bold tabular-nums" style="color:#059669;">₹{{ number_format((float) ($weekRevenue['collected'] ?? 0), 0) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Pending</p>
                        <p class="text-sm font-bold tabular-nums" style="color:#d97706;">₹{{ number_format((float) ($weekRevenue['pending'] ?? 0), 0) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">GST</p>
                        <p class="text-sm font-bold text-gray-600 tabular-nums">₹{{ number_format((float) ($weekRevenue['gst'] ?? 0), 0) }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Invoices --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-900">Recent Invoices</h3>
                @if(\Illuminate\Support\Facades\Route::has('billing.index'))
                <a href="{{ route('billing.index') }}" class="text-xs font-semibold" style="color:#1447E6;">All invoices →</a>
                @endif
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($invoices ?? [] as $inv)
                @php
                    $isPaid = $inv['status']==='paid';
                    $isDue  = $inv['status']==='due';
                @endphp
                @if(!empty($inv['url']))
                <a href="{{ $inv['url'] }}" class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50 transition-colors cursor-pointer">
                @else
                <div class="flex items-center gap-3 px-5 py-3">
                @endif
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white font-bold text-xs flex-shrink-0"
                         style="background:linear-gradient(135deg,{{ $inv['gradient'] }})">
                        {{ $inv['initials'] }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-gray-900 truncate">{{ $inv['name'] }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ $inv['desc'] }}</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-sm font-bold text-gray-900 tabular-nums">₹{{ $inv['amount'] }}</p>
                        <p class="text-xs font-semibold mt-0.5 {{ $isPaid ? 'text-green-600' : ($isDue ? 'text-amber-600' : 'text-blue-600') }}">
                            {{ $isPaid ? 'Paid · '.$inv['method'] : ($isDue ? 'Due · '.$inv['method'] : $inv['method']) }}
                        </p>
                    </div>
                @if(!empty($inv['url']))
                </a>
                @else
                </div>
                @endif
                @empty
                <div class="px-5 py-10 text-center text-sm text-gray-500">No invoices yet.</div>
                @endforelse
            </div>
        </div>

        {{-- AI Suggestions + Visits by Type --}}
        <div class="flex flex-col gap-4">

            <div class="rounded-xl p-4" style="background:linear-gradient(135deg,rgba(20,71,230,.04),rgba(8,145,178,.04));border:1px solid rgba(20,71,230,.12);">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center text-white text-xs font-bold" style="background:#1447E6;">✦</div>
                    <h4 class="text-sm font-bold text-gray-900">AI Suggestions</h4>
                </div>
                @if(!empty($suggestions) && count($suggestions) > 0)
                    @foreach($suggestions as $sug)
                    <div class="bg-white rounded-lg border border-gray-200 p-3 mb-2 cursor-pointer hover:border-blue-300 transition-colors">
                        <p class="text-xs font-semibold text-gray-900 mb-1">{{ $sug['title'] }}</p>
                        <p class="text-xs text-gray-400 leading-relaxed">{{ $sug['body'] }}</p>
                    </div>
                    @endforeach
                @else
                    <p class="text-xs text-gray-500 leading-relaxed">No AI suggestions right now. When automated insights are available for your clinic, they will show here.</p>
                @endif
            </div>

            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-bold text-gray-900">Visits by Type</h3>
                    <p class="text-xs text-gray-400 mt-0.5">This month — appointment types</p>
                </div>
                <div class="p-5 space-y-3">
                    @forelse($visitsByType ?? [] as $vt)
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-gray-600 w-24 flex-shrink-0 truncate" title="{{ $vt['label'] }}">{{ $vt['label'] }}</span>
                        <div class="flex-1 h-1.5 rounded-full bg-gray-100 overflow-hidden">
                            <div class="h-full rounded-full" style="width:{{ $vt['pct'] }}%;background:{{ $vt['color'] }};"></div>
                        </div>
                        <span class="text-xs font-semibold text-gray-700 w-8 text-right tabular-nums">{{ $vt['pct'] }}%</span>
                    </div>
                    @empty
                    <p class="text-xs text-gray-500 text-center py-4">No appointment type data for this month.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</div>{{-- /page --}}

<a href="{{ route('patients.create') }}"
   class="fixed bottom-8 right-8 flex items-center gap-2 text-white font-semibold text-sm px-5 py-3 rounded-full shadow-xl hover:shadow-2xl transition-all hover:scale-105 z-50"
   style="background:linear-gradient(135deg,#1447E6,#0891B2);">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
    </svg>
    New Patient
</a>

@endsection

@push('scripts')
<script>
(function() {
    const ctx = document.getElementById('revenueChart');
    if (!ctx) return;
    const labels = @json($weekChartLabels ?? []);
    const data = @json($weekChartData ?? []);
    const maxVal = Math.max(1, ...data);
    const colors = data.map((v, i) => v === maxVal && v > 0 ? '#1447E6' : '#93c5fd');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels.length ? labels : ['—'],
            datasets: [{
                data: data.length ? data : [0],
                backgroundColor: colors.length ? colors : ['#e5e7eb'],
                borderRadius: 4,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: {
                callbacks: { label: c => '₹' + Number(c.raw).toLocaleString('en-IN') }
            }},
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 10 }, color: '#9ca3af' } },
                y: { display: false, grid: { display: false } }
            }
        }
    });
})();
</script>
@endpush
