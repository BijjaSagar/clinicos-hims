@extends('layouts.app')

@section('title', 'Bed Map')
@section('breadcrumb', 'IPD Bed Map')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 space-y-6">
    @if(session('success'))
    <div class="px-4 py-3 rounded-xl text-sm font-medium" style="background:#ecfdf5;color:#059669;border:1px solid #a7f3d0;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="px-4 py-3 rounded-xl text-sm font-medium bg-red-50 text-red-800 border border-red-200">{{ session('error') }}</div>
    @endif

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Bed Map</h1>
            <p class="text-sm text-gray-500 mt-1">Visual overview of bed occupancy. After discharge, beds go to <strong>cleaning</strong> — mark <strong>available</strong> when housekeeping is done (Phase A spine).</p>
        </div>
        <a href="{{ route('ipd.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border rounded-xl hover:bg-gray-50">
            Back to IPD
        </a>
    </div>

    <div class="flex flex-wrap gap-3 text-sm">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-green-100 text-green-700 border border-green-200">
            <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span> Available
        </span>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-100 text-red-700 border border-red-200">
            <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span> Occupied
        </span>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 border border-yellow-200">
            <span class="w-2.5 h-2.5 rounded-full bg-yellow-500"></span> Cleaning
        </span>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gray-100 text-gray-600 border border-gray-200">
            <span class="w-2.5 h-2.5 rounded-full bg-gray-400"></span> Maintenance
        </span>
    </div>

    @forelse($wards as $ward)
        <div class="bg-white rounded-xl border shadow-sm">
            <div class="px-6 py-4 border-b bg-gray-50 rounded-t-xl flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">{{ $ward->name }}</h2>
                    <p class="text-xs text-gray-500">{{ $ward->is_icu ? 'ICU' : 'General' }} Ward &middot; {{ $ward->rooms->count() }} room(s)</p>
                </div>
                @if($ward->is_icu)
                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-700">ICU</span>
                @endif
            </div>

            <div class="p-6">
                @foreach($ward->rooms as $room)
                    <div class="mb-4 last:mb-0">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">{{ $room->name ?? 'Room ' . $room->room_number }}</h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                            @foreach($room->beds as $bed)
                                @php
                                    $colors = match($bed->status) {
                                        'available' => 'bg-green-50 border-green-200 text-green-700',
                                        'occupied' => 'bg-red-50 border-red-200 text-red-700',
                                        'cleaning' => 'bg-yellow-50 border-yellow-200 text-yellow-700',
                                        'maintenance' => 'bg-gray-50 border-gray-200 text-gray-500',
                                        default => 'bg-gray-50 border-gray-200 text-gray-500',
                                    };
                                @endphp
                                <div class="rounded-xl border-2 p-3 text-center {{ $colors }}">
                                    <div class="text-sm font-bold">{{ $bed->bed_code }}</div>
                                    <div class="text-xs mt-1 capitalize">{{ str_replace('_', ' ', $bed->status) }}</div>
                                    @if($bed->currentAdmission?->patient)
                                        <div class="text-xs mt-1 font-medium truncate">{{ $bed->currentAdmission->patient->name }}</div>
                                    @endif
                                    @if(in_array($bed->status, ['cleaning', 'maintenance'], true))
                                    <form method="POST" action="{{ route('ipd.beds.mark-available', $bed) }}" class="mt-2">
                                        @csrf
                                        <button type="submit" class="text-xs font-semibold px-2 py-1 rounded-lg bg-white/90 border border-amber-300 text-amber-900 hover:bg-white">
                                            Mark available
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div class="bg-white rounded-xl border p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <p class="text-gray-500 mt-3 font-medium">No wards configured</p>
            <p class="text-gray-400 text-sm mt-1">Set up wards and beds in Hospital Settings first.</p>
        </div>
    @endforelse
</div>
@endsection
