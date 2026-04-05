@extends('layouts.app')

@section('title', 'IPD — ' . ($admission->patient->name ?? 'Patient'))
@section('breadcrumb', 'IPD · ' . ($admission->admission_number ?? 'Admission'))

@section('content')
@php
    $ipdAllowedTabs = ['notes', 'vitals', 'medications', 'handover', 'care', 'discharge'];
    $ipdInitialTab = request('tab', session('open_ipd_tab', 'notes'));
    $ipdInitialTab = in_array($ipdInitialTab, $ipdAllowedTabs, true) ? $ipdInitialTab : 'notes';
@endphp
<div x-data="ipdShow({{ \Illuminate\Support\Js::from($ipdInitialTab) }})" class="p-4 sm:p-5 lg:p-7 space-y-5">

    @if(session('success'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium" style="background:#ecfdf5;color:#059669;border:1px solid #a7f3d0;">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Back + Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('ipd.index') }}"
           class="p-2 rounded-lg border border-gray-200 text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div class="flex-1">
            <h1 class="text-xl font-bold text-gray-900 font-display">{{ $admission->patient->name ?? 'Patient' }}</h1>
            <p class="text-sm text-gray-500">Admission #{{ $admission->admission_number }}</p>
        </div>
        <a href="{{ route('ipd.print-card', $admission) }}" target="_blank"
           class="inline-flex items-center gap-2 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Print Card
        </a>
        <a href="{{ route('ipd.print-prescription', $admission) }}" target="_blank"
           class="inline-flex items-center gap-2 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Print Rx
        </a>
        @if($admission->status === 'admitted')
        <button @click="showDischargeModal = true"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:shadow-lg"
            style="background:linear-gradient(135deg,#dc2626,#b91c1c);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            Discharge Patient
        </button>
        @endif
    </div>

    {{-- Quick links: same patient across Billing / Lab / Pharmacy (Phase A spine) --}}
    @php
        $ipdPid = $admission->patient_id;
        $ipdAdmId = $admission->id;
    @endphp
    <div class="bg-gradient-to-r from-slate-50 to-blue-50/80 border border-gray-200 rounded-xl p-4 flex flex-col sm:flex-row sm:flex-wrap sm:items-center gap-3">
        <span class="text-xs font-bold uppercase tracking-wide text-gray-500">Continue care</span>
        <div class="flex flex-wrap gap-2">
            @if(\Illuminate\Support\Facades\Route::has('billing.create'))
                <a href="{{ route('billing.create', ['patient_id' => $ipdPid, 'admission_id' => $ipdAdmId]) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-indigo-200 text-indigo-900 hover:bg-indigo-50 transition-colors">
                    <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Create invoice (this admission)
                </a>
            @endif
            @if(\Illuminate\Support\Facades\Route::has('billing.index'))
                <a href="{{ route('billing.index', ['patient_id' => $ipdPid, 'admission_id' => $ipdAdmId]) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-gray-200 text-gray-800 hover:border-blue-300 hover:bg-blue-50/80 transition-colors">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                    Invoices (this patient)
                </a>
            @endif
            @if(\Illuminate\Support\Facades\Route::has('laboratory.orders'))
                <a href="{{ route('laboratory.orders', ['patient_id' => $ipdPid]) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-gray-200 text-gray-800 hover:border-teal-300 hover:bg-teal-50/80 transition-colors">
                    <svg class="w-3.5 h-3.5 text-teal-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    In-house lab orders
                </a>
            @endif
            @if(\Illuminate\Support\Facades\Route::has('lab.index'))
                <a href="{{ route('lab.index') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-gray-200 text-gray-800 hover:border-cyan-300 hover:bg-cyan-50/80 transition-colors">
                    External lab integration
                </a>
            @endif
            @if(\Illuminate\Support\Facades\Route::has('pharmacy.dispense.form'))
                <a href="{{ route('pharmacy.dispense.form', ['patient_id' => $ipdPid, 'admission_id' => $ipdAdmId]) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-gray-200 text-gray-800 hover:border-emerald-300 hover:bg-emerald-50/80 transition-colors">
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    Pharmacy dispense
                </a>
            @endif
        </div>
    </div>

    {{-- Patient Info Card --}}
    <div class="bg-white border border-gray-200 rounded-xl p-5">
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-5">
            <div>
                <p class="text-xs font-medium text-gray-400 mb-1">Patient</p>
                <p class="text-sm font-semibold text-gray-900">{{ $admission->patient->name ?? '—' }}</p>
                <p class="text-xs text-gray-500">{{ $admission->patient->phone ?? '' }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-400 mb-1">Age / Gender</p>
                <p class="text-sm font-semibold text-gray-900">
                    {{ $admission->patient->age_years ?? '—' }} yr
                    @if($admission->patient->sex ?? null) · {{ ucfirst($admission->patient->sex) }} @endif
                </p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-400 mb-1">Ward / Bed</p>
                <p class="text-sm font-semibold text-gray-900">{{ $admission->ward->name ?? '—' }}</p>
                <p class="text-xs text-gray-500">Bed {{ $admission->bed->bed_number ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-400 mb-1">Admission Date</p>
                <p class="text-sm font-semibold text-gray-900">
                    {{ $admission->admission_date ? \Carbon\Carbon::parse($admission->admission_date)->format('d M Y') : '—' }}
                </p>
                <p class="text-xs text-gray-500">
                    {{ $admission->admission_date ? \Carbon\Carbon::parse($admission->admission_date)->diffForHumans() : '' }}
                </p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-400 mb-1">Primary Doctor</p>
                <p class="text-sm font-semibold text-gray-900">{{ $admission->primaryDoctor->name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-400 mb-1">Diagnosis</p>
                <p class="text-sm font-semibold text-gray-900 truncate" title="{{ $admission->diagnosis_at_admission }}">
                    {{ $admission->diagnosis_at_admission ?? '—' }}
                </p>
                @php
                    $statusMap = [
                        'admitted'    => ['label' => 'Admitted',    'bg' => '#ecfdf5', 'color' => '#059669'],
                        'discharged'  => ['label' => 'Discharged',  'bg' => '#f1f5f9', 'color' => '#64748b'],
                        'transferred' => ['label' => 'Transferred', 'bg' => '#fff7ed', 'color' => '#d97706'],
                        'critical'    => ['label' => 'Critical',    'bg' => '#fff1f2', 'color' => '#dc2626'],
                    ];
                    $s = $statusMap[$admission->status] ?? $statusMap['admitted'];
                @endphp
                <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded-full text-xs font-semibold"
                      style="background:{{ $s['bg'] }};color:{{ $s['color'] }};">
                    {{ $s['label'] }}
                </span>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="border-b border-gray-100 flex overflow-x-auto">
            @foreach(['notes' => 'Progress Notes', 'vitals' => 'Vitals Chart', 'medications' => 'Medications & MAR', 'handover' => 'Handover', 'care' => 'Care Plans', 'discharge' => 'Discharge Summary'] as $tabKey => $tabLabel)
            <button @click="activeTab = '{{ $tabKey }}'"
                :class="activeTab === '{{ $tabKey }}'
                    ? 'border-b-2 text-blue-600 font-semibold'
                    : 'text-gray-500 hover:text-gray-700'"
                class="px-5 py-4 text-sm whitespace-nowrap transition-colors"
                style="border-color: activeTab === '{{ $tabKey }}' ? '#1447E6' : 'transparent';">
                {{ $tabLabel }}
            </button>
            @endforeach
        </div>

        {{-- Progress Notes Tab --}}
        <div x-show="activeTab === 'notes'" class="p-5 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-900">Progress Notes</h3>
                <button @click="showNoteModal = true"
                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold text-white"
                    style="background:#1447E6;">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Note
                </button>
            </div>

            @forelse($progressNotes as $note)
            @php $soap = $note->soapPayload(); @endphp
            <div class="border border-gray-100 rounded-xl p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">{{ ucfirst($soap['note_type'] ?? $note->note_type ?? 'note') }}</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $note->author->name ?? 'Staff' }}</span>
                    </div>
                    <span class="text-xs text-gray-400">
                        @if($note->note_at ?? null)
                            {{ \Carbon\Carbon::parse($note->note_at)->format('d M Y H:i') }}
                        @elseif($note->note_date)
                            {{ \Carbon\Carbon::parse($note->note_date)->format('d M Y') }}
                            {{ $note->note_time ?? '' }}
                        @endif
                    </span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                    @if(!empty($soap['subjective'] ?? $note->subjective))
                    <div><p class="text-xs font-semibold text-gray-400 mb-0.5">Subjective</p><p class="text-gray-700">{{ $soap['subjective'] ?? $note->subjective }}</p></div>
                    @endif
                    @if(!empty($soap['objective'] ?? $note->objective))
                    <div><p class="text-xs font-semibold text-gray-400 mb-0.5">Objective</p><p class="text-gray-700">{{ $soap['objective'] ?? $note->objective }}</p></div>
                    @endif
                    @if(!empty($soap['assessment'] ?? $note->assessment))
                    <div><p class="text-xs font-semibold text-gray-400 mb-0.5">Assessment</p><p class="text-gray-700">{{ $soap['assessment'] ?? $note->assessment }}</p></div>
                    @endif
                    @if(!empty($soap['plan'] ?? $note->plan))
                    <div><p class="text-xs font-semibold text-gray-400 mb-0.5">Plan</p><p class="text-gray-700">{{ $soap['plan'] ?? $note->plan }}</p></div>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-10 text-gray-400 text-sm">No progress notes yet. Add the first note.</div>
            @endforelse
        </div>

        {{-- Vitals Tab --}}
        <div x-show="activeTab === 'vitals'" class="p-5 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-900">Vitals Chart</h3>
                <button @click="showVitalsModal = true"
                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold text-white"
                    style="background:#1447E6;">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Record Vitals
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Time</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Temp</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Pulse</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase">BP</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase">SpO2</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase">RR</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Pain</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase">By</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($vitals as $vital)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 text-xs text-gray-500">{{ \Carbon\Carbon::parse($vital->recorded_at)->format('d M H:i') }}</td>
                            <td class="px-3 py-2">{{ $vital->temperature_display !== null ? number_format((float) $vital->temperature_display, 1).'°C' : '—' }}</td>
                            <td class="px-3 py-2">{{ $vital->pulse ? $vital->pulse.' bpm' : '—' }}</td>
                            <td class="px-3 py-2">
                                @if($vital->bp_systolic && $vital->bp_diastolic)
                                    {{ $vital->bp_systolic }}/{{ $vital->bp_diastolic }}
                                @else —
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                @if($vital->spo2)
                                    <span class="{{ $vital->spo2 < 95 ? 'text-red-600 font-semibold' : '' }}">{{ $vital->spo2 }}%</span>
                                @else —
                                @endif
                            </td>
                            <td class="px-3 py-2">{{ $vital->respiratory_rate_display !== null ? $vital->respiratory_rate_display.'/min' : '—' }}</td>
                            <td class="px-3 py-2">{{ $vital->pain_score !== null ? $vital->pain_score.'/10' : '—' }}</td>
                            <td class="px-3 py-2 text-xs text-gray-500">{{ $vital->recordedBy->name ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-3 py-10 text-center text-gray-400 text-sm">No vitals recorded yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Medications & MAR Tab --}}
        <div x-show="activeTab === 'medications'" class="p-5 space-y-6">
            @if($admission->status === 'admitted')
            <form method="POST" action="{{ route('ipd.medication-orders.store', $admission) }}" class="border border-gray-100 rounded-xl p-4 space-y-3 bg-slate-50/50">
                @csrf
                <h3 class="text-sm font-bold text-gray-900">New medication order</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Drug name</label>
                        <input type="text" name="drug_name" required value="{{ old('drug_name') }}"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white" placeholder="e.g. Paracetamol 500mg">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Route</label>
                        <select name="route" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white">
                            @foreach(['oral' => 'Oral', 'iv' => 'IV', 'im' => 'IM', 'sc' => 'SC', 'topical' => 'Topical', 'sublingual' => 'Sublingual', 'inhalation' => 'Inhalation', 'rectal' => 'Rectal'] as $rv => $rl)
                                <option value="{{ $rv }}" @selected(old('route') === $rv)>{{ $rl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Dosage</label>
                        <input type="text" name="dosage" required value="{{ old('dosage') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="500 mg">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Frequency</label>
                        <input type="text" name="frequency" required value="{{ old('frequency') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="TDS / OD">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Start</label>
                        <input type="date" name="start_date" required value="{{ old('start_date', date('Y-m-d')) }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">End (optional)</label>
                        <input type="date" name="end_date" value="{{ old('end_date') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Instructions</label>
                        <input type="text" name="instructions" value="{{ old('instructions') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="After food, etc.">
                    </div>
                    <div class="flex items-center gap-2 pt-6">
                        <input type="checkbox" name="is_sos" id="is_sos" value="1" class="rounded border-gray-300" @checked(old('is_sos'))>
                        <label for="is_sos" class="text-sm text-gray-700">SOS / stat</label>
                    </div>
                </div>
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white" style="background:#1447E6;">Save order</button>
            </form>
            @endif

            @forelse($medicationOrders as $med)
            <div class="border border-gray-100 rounded-xl overflow-hidden">
                <div class="flex flex-col sm:flex-row sm:items-start gap-3 p-4 bg-white">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900">{{ $med->drug_name ?? '—' }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $med->dosage ?? '' }} · {{ $med->route ?? '' }} · {{ $med->frequency ?? '' }}</p>
                        <p class="text-xs text-gray-400 mt-1">By {{ $med->prescribedBy->name ?? '—' }} · {{ ucfirst($med->status ?? 'active') }}</p>
                    </div>
                </div>
                @if($admission->status === 'admitted' && ($med->status ?? '') === 'active')
                <div class="px-4 pb-4 border-t border-gray-50 bg-gray-50/80">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide pt-3 mb-2">Record administration (MAR)</p>
                    <form method="POST" action="{{ route('ipd.mar.store', $admission) }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 items-end">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $med->id }}">
                        <div>
                            <label class="block text-xs text-gray-600 mb-0.5">Dose given</label>
                            <input type="text" name="dose_given" required class="w-full px-2 py-1.5 border rounded text-sm" placeholder="1 tab">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 mb-0.5">Time</label>
                            <input type="datetime-local" name="administered_at" required value="{{ now()->format('Y-m-d\TH:i') }}" class="w-full px-2 py-1.5 border rounded text-sm">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 mb-0.5">Notes</label>
                            <input type="text" name="notes" class="w-full px-2 py-1.5 border rounded text-sm">
                        </div>
                        <div class="flex items-center gap-2 pb-0.5">
                            <input type="checkbox" name="not_administered" value="1" id="na_{{ $med->id }}" class="rounded">
                            <label for="na_{{ $med->id }}" class="text-xs text-gray-600">Not given</label>
                        </div>
                        <div class="sm:col-span-2 lg:col-span-4">
                            <input type="text" name="not_administered_reason" class="w-full px-2 py-1.5 border rounded text-sm" placeholder="Reason if not given">
                        </div>
                        <div>
                            <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700">Log MAR</button>
                        </div>
                    </form>
                </div>
                @endif
                @if($med->administrations && $med->administrations->count())
                <div class="px-4 py-3 bg-white border-t border-gray-100">
                    <p class="text-xs font-semibold text-gray-500 mb-2">Recent MAR</p>
                    <ul class="space-y-1 text-xs text-gray-700">
                        @foreach($med->administrations->sortByDesc('administered_at')->take(8) as $adm)
                        <li class="flex flex-wrap gap-x-3 gap-y-0.5">
                            <span>{{ $adm->administered_at ? \Carbon\Carbon::parse($adm->administered_at)->format('d M H:i') : '' }}</span>
                            <span>{{ $adm->dose_given }}</span>
                            @if($adm->not_administered)
                                <span class="text-amber-700">Not given{{ $adm->not_administered_reason ? ': '.$adm->not_administered_reason : '' }}</span>
                            @endif
                            <span class="text-gray-400">{{ $adm->administeredBy->name ?? '' }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
            @empty
            <div class="text-center py-10 text-gray-400 text-sm">No medication orders yet.</div>
            @endforelse
        </div>

        {{-- Handover Tab --}}
        <div x-show="activeTab === 'handover'" class="p-5 space-y-4">
            @if($admission->status === 'admitted')
            <form method="POST" action="{{ route('ipd.handover.store', $admission) }}" class="border border-gray-100 rounded-xl p-4 space-y-3 bg-amber-50/30">
                @csrf
                <h3 class="text-sm font-bold text-gray-900">Shift handover</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Shift</label>
                        <select name="shift" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white">
                            <option value="">—</option>
                            <option value="morning">Morning</option>
                            <option value="evening">Evening</option>
                            <option value="night">Night</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Summary <span class="text-red-500">*</span></label>
                    <textarea name="summary" required rows="4" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="Patient status, lines, pending tasks…">{{ old('summary') }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Concerns / risks</label>
                    <textarea name="concerns" rows="2" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">{{ old('concerns') }}</textarea>
                </div>
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white bg-amber-700 hover:bg-amber-800">Save handover</button>
            </form>
            @endif
            <div class="space-y-3">
                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide">History</h4>
                @forelse($handoverNotes as $h)
                <div class="border border-gray-100 rounded-xl p-4 space-y-2">
                    <div class="flex flex-wrap justify-between gap-2 text-xs text-gray-500">
                        <span>{{ $h->created_at?->format('d M Y H:i') }}</span>
                        <span>{{ $h->shift ? ucfirst($h->shift) : 'Shift' }} · {{ $h->handedOverBy->name ?? 'Staff' }}</span>
                    </div>
                    <p class="text-sm text-gray-800 whitespace-pre-wrap">{{ $h->summary }}</p>
                    @if($h->concerns)
                    <p class="text-xs text-amber-900 bg-amber-50 rounded-lg p-2"><span class="font-semibold">Concerns:</span> {{ $h->concerns }}</p>
                    @endif
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-6">No handover notes yet.</p>
                @endforelse
            </div>
        </div>

        {{-- Care plans Tab --}}
        <div x-show="activeTab === 'care'" class="p-5 space-y-4">
            @if($admission->status === 'admitted')
            <form method="POST" action="{{ route('ipd.care-plan.store', $admission) }}" class="border border-gray-100 rounded-xl p-4 space-y-3 bg-indigo-50/30">
                @csrf
                <h3 class="text-sm font-bold text-gray-900">Add / update care plan line</h3>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Goal <span class="text-red-500">*</span></label>
                        <input type="text" name="goal" required value="{{ old('goal') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="e.g. Maintain SpO2 above 94%">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Interventions</label>
                    <textarea name="interventions" rows="3" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">{{ old('interventions') }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Outcome review</label>
                    <textarea name="outcome_review" rows="2" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">{{ old('outcome_review') }}</textarea>
                </div>
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white" style="background:#4338ca;">Save care plan</button>
            </form>
            @endif
            <div class="space-y-3">
                @forelse($carePlans as $cp)
                <div class="border border-gray-100 rounded-xl p-4">
                    <p class="text-sm font-semibold text-gray-900">{{ $cp->goal }}</p>
                    @if($cp->interventions)
                    <p class="text-sm text-gray-700 mt-2 whitespace-pre-wrap">{{ $cp->interventions }}</p>
                    @endif
                    @if($cp->outcome_review)
                    <p class="text-xs text-gray-500 mt-2"><span class="font-semibold">Review:</span> {{ $cp->outcome_review }}</p>
                    @endif
                    <p class="text-xs text-gray-400 mt-2">{{ $cp->updated_at?->format('d M Y H:i') }}</p>
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-6">No care plan entries yet.</p>
                @endforelse
            </div>
        </div>

        {{-- Discharge Summary Tab --}}
        <div x-show="activeTab === 'discharge'" class="p-5" id="discharge">
            @if($admission->status === 'discharged')
            <div class="space-y-4">
                <h3 class="text-sm font-bold text-gray-900">Discharge Summary</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-xs font-medium text-gray-400 mb-1">Discharge Date</p>
                        <p class="font-semibold text-gray-900">{{ $admission->discharge_date ? \Carbon\Carbon::parse($admission->discharge_date)->format('d M Y, H:i') : '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-400 mb-1">Discharge Type</p>
                        <p class="font-semibold text-gray-900">{{ ucfirst($admission->discharge_type ?? '—') }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-xs font-medium text-gray-400 mb-1">Final Diagnosis</p>
                        <p class="text-gray-700">{{ $admission->final_diagnosis ?? '—' }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-xs font-medium text-gray-400 mb-1">Discharge Notes</p>
                        <p class="text-gray-700">{{ $admission->discharge_notes ?? '—' }}</p>
                    </div>
                </div>
            </div>
            @else
            <form method="POST" action="{{ route('ipd.discharge', $admission) }}" class="space-y-4 max-w-xl">
                @csrf
                <h3 class="text-sm font-bold text-gray-900">Process Discharge</h3>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Discharge Type <span class="text-red-500">*</span></label>
                    <select name="discharge_type" required
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">Select type…</option>
                        <option value="recovered">Recovered</option>
                        <option value="lama">LAMA (Left Against Medical Advice)</option>
                        <option value="transfer">Transfer to Another Facility</option>
                        <option value="expired">Expired</option>
                        <option value="improved">Improved / Partial Recovery</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Final Diagnosis <span class="text-red-500">*</span></label>
                    <input type="text" name="final_diagnosis" required
                        value="{{ old('final_diagnosis', $admission->diagnosis_at_admission) }}"
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Discharge Notes</label>
                    <textarea name="discharge_notes" rows="4"
                        placeholder="Instructions for patient, follow-up recommendations…"
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('discharge_notes') }}</textarea>
                </div>
                <div class="flex gap-3">
                    <button type="submit"
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:shadow-lg"
                        style="background:linear-gradient(135deg,#dc2626,#b91c1c);"
                        onclick="return confirm('Are you sure you want to discharge this patient?')">
                        Confirm Discharge
                    </button>
                </div>
            </form>
            @endif
        </div>
    </div>

    {{-- Add Progress Note Modal --}}
    <div x-show="showNoteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showNoteModal = false"></div>
            <div class="relative bg-white rounded-2xl shadow-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-base font-bold text-gray-900">Add Progress Note</h3>
                    <button @click="showNoteModal = false" class="p-1.5 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form method="POST" action="{{ route('ipd.progress-notes.store', $admission) }}" class="p-6 space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Note Type</label>
                            <select name="note_type" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="doctor">Doctor's Note</option>
                                <option value="nursing">Nursing Note</option>
                                <option value="consultant">Consultant</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Date</label>
                            <input type="date" name="note_date" required value="{{ date('Y-m-d') }}"
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Time</label>
                        <input type="time" name="note_time" required value="{{ date('H:i') }}"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    @foreach(['subjective' => 'Subjective (S)', 'objective' => 'Objective (O)', 'assessment' => 'Assessment (A)', 'plan' => 'Plan (P)'] as $field => $label)
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">{{ $label }}</label>
                        <textarea name="{{ $field }}" required rows="2"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                    </div>
                    @endforeach
                    <div class="flex justify-end gap-3 pt-1">
                        <button type="button" @click="showNoteModal = false"
                            class="px-4 py-2 text-sm font-semibold text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-semibold text-white rounded-xl" style="background:#1447E6;">Save Note</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Record Vitals Modal --}}
    <div x-show="showVitalsModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showVitalsModal = false"></div>
            <div class="relative bg-white rounded-2xl shadow-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-base font-bold text-gray-900">Record Vitals</h3>
                    <button @click="showVitalsModal = false" class="p-1.5 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form id="vitalsForm" class="p-6 space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Temperature (°C)</label>
                            <input type="number" name="temperature" step="0.1" min="30" max="45" placeholder="37.0"
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Pulse (bpm)</label>
                            <input type="number" name="pulse" min="20" max="300" placeholder="72"
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">BP Systolic</label>
                            <input type="number" name="bp_systolic" min="50" max="250" placeholder="120"
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">BP Diastolic</label>
                            <input type="number" name="bp_diastolic" min="30" max="150" placeholder="80"
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">SpO2 (%)</label>
                            <input type="number" name="spo2" step="0.1" min="50" max="100" placeholder="98"
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Respiratory Rate</label>
                            <input type="number" name="respiratory_rate" min="4" max="60" placeholder="16"
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Pain Score (0–10)</label>
                            <input type="number" name="pain_score" min="0" max="10" placeholder="0"
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">GCS (3–15)</label>
                            <input type="number" name="gcs" min="3" max="15" placeholder="15"
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Notes</label>
                        <textarea name="notes" rows="2" placeholder="Any relevant observations…"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                    </div>
                    <div class="flex justify-end gap-3 pt-1">
                        <button type="button" @click="showVitalsModal = false"
                            class="px-4 py-2 text-sm font-semibold text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50">Cancel</button>
                        <button type="button" @click="saveVitals()"
                            class="px-4 py-2 text-sm font-semibold text-white rounded-xl" style="background:#1447E6;">Save Vitals</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Discharge Modal --}}
    <div x-show="showDischargeModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showDischargeModal = false"></div>
            <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-900">Discharge Patient</h3>
                </div>
                <form method="POST" action="{{ route('ipd.discharge', $admission) }}" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Discharge Type <span class="text-red-500">*</span></label>
                        <select name="discharge_type" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select type…</option>
                            <option value="recovered">Recovered</option>
                            <option value="lama">LAMA</option>
                            <option value="transfer">Transfer</option>
                            <option value="expired">Expired</option>
                            <option value="improved">Improved</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Final Diagnosis <span class="text-red-500">*</span></label>
                        <input type="text" name="final_diagnosis" required
                            value="{{ $admission->diagnosis_at_admission }}"
                            class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Discharge Notes</label>
                        <textarea name="discharge_notes" rows="3"
                            class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="showDischargeModal = false"
                            class="px-4 py-2 text-sm font-semibold text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-semibold text-white rounded-xl"
                            style="background:#dc2626;"
                            onclick="return confirm('Confirm discharge?')">
                            Confirm Discharge
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
function ipdShow(initialTab) {
    console.log('[ipd.show] ipdShow()', { initialTab });
    return {
        activeTab: initialTab || 'notes',
        showNoteModal: false,
        showVitalsModal: false,
        showDischargeModal: false,

        async saveVitals() {
            const form = document.getElementById('vitalsForm');
            const data = Object.fromEntries(new FormData(form));
            console.log('[ipd.show] saveVitals payload', data);
            try {
                const res = await fetch('/ipd/{{ $admission->id }}/vitals', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(data),
                });
                const text = await res.text();
                let json = {};
                try { json = JSON.parse(text); } catch (e) { console.warn('[ipd.show] saveVitals non-JSON', text); }
                console.log('[ipd.show] saveVitals response', res.status, json);
                if (res.ok && json.success) {
                    this.showVitalsModal = false;
                    location.reload();
                } else {
                    alert('Error saving vitals: ' + (json.message || text || res.status));
                }
            } catch (e) {
                console.error('[ipd.show] saveVitals', e);
                alert('Failed to save vitals');
            }
        }
    };
}
</script>
@endpush
@endsection
