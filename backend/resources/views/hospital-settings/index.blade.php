@extends('layouts.app')

@section('title', 'Hospital Settings')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Hospital Settings</h1>
        <p class="text-sm text-gray-500 mt-0.5">Configure your facility details, modules, and ward management</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- LEFT: Hospital Settings Form --}}
        <div class="space-y-6">
            <form method="POST" action="{{ route('hospital-settings.update') }}" class="space-y-6">
                @csrf

                {{-- Basic Info --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h2 class="text-base font-semibold text-gray-900 mb-4">Facility Information</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hospital Name <span class="text-red-500">*</span></label>
                            <input type="text" name="hospital_name" value="{{ old('hospital_name', $settings['hospital_name']) }}" required
                                placeholder="e.g. City General Hospital"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hospital Type <span class="text-red-500">*</span></label>
                            <select name="hospital_type"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue transition-colors bg-white">
                                <option value="clinic"        {{ $settings['hospital_type'] === 'clinic'        ? 'selected' : '' }}>Clinic</option>
                                <option value="hospital"      {{ $settings['hospital_type'] === 'hospital'      ? 'selected' : '' }}>Hospital</option>
                                <option value="nursing_home"  {{ $settings['hospital_type'] === 'nursing_home'  ? 'selected' : '' }}>Nursing Home</option>
                                <option value="polyclinic"    {{ $settings['hospital_type'] === 'polyclinic'    ? 'selected' : '' }}>Polyclinic</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Registration Prefix</label>
                            <input type="text" name="registration_prefix" value="{{ old('registration_prefix', $settings['registration_prefix']) }}"
                                placeholder="e.g. IPD, OPD"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue transition-colors">
                            <p class="text-xs text-gray-400 mt-1">Used as prefix for admission registration numbers.</p>
                        </div>
                    </div>
                </div>

                {{-- Bed Configuration --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h2 class="text-base font-semibold text-gray-900 mb-4">Bed Configuration</h2>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Total Beds</label>
                            <input type="number" name="total_beds" value="{{ old('total_beds', $settings['total_beds']) }}" min="0" placeholder="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ICU Beds</label>
                            <input type="number" name="icu_beds" value="{{ old('icu_beds', $settings['icu_beds']) }}" min="0" placeholder="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Emergency Beds</label>
                            <input type="number" name="emergency_beds" value="{{ old('emergency_beds', $settings['emergency_beds']) }}" min="0" placeholder="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue transition-colors">
                        </div>
                    </div>
                </div>

                {{-- Module Toggles --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h2 class="text-base font-semibold text-gray-900 mb-1">Module Toggles</h2>
                    <p class="text-sm text-gray-500 mb-4">Enable or disable modules for your facility.</p>
                    <div class="space-y-3">
                        @foreach([
                            ['key' => 'enable_ipd',       'label' => 'Enable IPD (Inpatient Department)',    'desc' => 'Manage inpatient admissions and bed allocation'],
                            ['key' => 'enable_pharmacy',  'label' => 'Enable Pharmacy',                      'desc' => 'Manage drug inventory and dispensing'],
                            ['key' => 'enable_lab',       'label' => 'Enable Lab (Laboratory)',              'desc' => 'Manage lab orders, samples and results'],
                            ['key' => 'enable_opd_queue', 'label' => 'Enable OPD Queue',                     'desc' => 'Real-time outpatient queue management'],
                        ] as $module)
                        <label class="flex items-start gap-3 cursor-pointer p-3 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">
                            <input type="checkbox" name="{{ $module['key'] }}" value="1"
                                {{ $settings[$module['key']] === '1' ? 'checked' : '' }}
                                class="mt-0.5 w-4 h-4 text-brand-blue border-gray-300 rounded focus:ring-brand-blue/30">
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $module['label'] }}</p>
                                <p class="text-xs text-gray-500">{{ $module['desc'] }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Discharge Summary Footer --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h2 class="text-base font-semibold text-gray-900 mb-1">Discharge Summary Footer</h2>
                    <p class="text-sm text-gray-500 mb-3">This text will appear at the bottom of all discharge summaries.</p>
                    <textarea name="discharge_summary_footer" rows="4"
                        placeholder="e.g. Follow up after 1 week. In case of emergency contact: 1800-XXX-XXXX"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue transition-colors resize-none">{{ old('discharge_summary_footer', $settings['discharge_summary_footer']) }}</textarea>
                </div>

                <button type="submit"
                    class="w-full py-3 bg-brand-blue text-white text-sm font-semibold rounded-xl hover:bg-brand-blue-dark transition-colors shadow-sm">
                    Save Hospital Settings
                </button>
            </form>
        </div>

        {{-- RIGHT: Ward Management --}}
        @php
            $roomsByWard = $roomsByWard ?? collect();
            $roomBedCounts = $roomBedCounts ?? [];
        @endphp
        <div x-data="{
                wardOpen: false,
                editOpen: false,
                edit: { id: null, name: '', type: 'general', floor: '', wing: '', is_active: true, is_icu: false, total_beds: 0 },
                openEdit(w) {
                    this.edit = Object.assign({ id: null, name: '', type: 'general', floor: '', wing: '', is_active: true, is_icu: false, total_beds: 0 }, w);
                    this.editOpen = true;
                    console.log('[hospital-settings] openEdit ward', this.edit);
                },
                closeEdit() { this.editOpen = false; }
            }">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">Ward Management</h2>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $wards->count() }} ward(s) configured</p>
                    </div>
                    <button @click="wardOpen = true"
                        class="inline-flex items-center gap-1.5 px-3 py-2 bg-brand-blue text-white text-sm font-medium rounded-lg hover:bg-brand-blue-dark transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Ward
                    </button>
                </div>

                {{-- Wards List --}}
                <div class="divide-y divide-gray-100">
                    @forelse($wards as $ward)
                    @php
                        $wardEditPayload = [
                            'id' => (int) $ward->id,
                            'name' => $ward->name,
                            'type' => data_get($ward, 'type', data_get($ward, 'ward_type', data_get($ward, 'is_icu') ? 'icu' : 'general')),
                            'floor' => $ward->floor ?? '',
                            'wing' => data_get($ward, 'wing', ''),
                            'is_active' => (bool) data_get($ward, 'is_active', true),
                            'is_icu' => (bool) data_get($ward, 'is_icu', false),
                            'total_beds' => (int) data_get($ward, 'total_beds', 0),
                        ];
                    @endphp
                    <div class="flex items-start justify-between gap-4 px-6 py-4 hover:bg-gray-50 transition-colors">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <p class="font-medium text-gray-900 truncate">{{ $ward->name }}</p>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ data_get($ward, 'is_active', true) ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                    {{ data_get($ward, 'is_active', true) ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <div class="flex items-center gap-3 mt-1 text-xs text-gray-500">
                                <span class="capitalize">{{ str_replace('_', ' ', data_get($ward, 'type', data_get($ward, 'ward_type', data_get($ward, 'is_icu') ? 'icu' : 'general'))) }}</span>
                                <span>·</span>
                                <span>{{ (int) ($wardBedCounts[$ward->id] ?? data_get($ward, 'total_beds', 0)) }} beds</span>
                                @if($ward->floor)
                                    <span>·</span>
                                    <span>Floor: {{ $ward->floor }}</span>
                                @endif
                            </div>
                            @php
                                $wardRooms = $roomsByWard[$ward->id] ?? collect();
                            @endphp
                            @if($wardRooms->isNotEmpty())
                            <div class="mt-2 text-xs text-gray-600">
                                <span class="font-medium text-gray-700">Rooms:</span>
                                @foreach($wardRooms as $r)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-gray-100 text-gray-800 mr-1 mb-1">{{ $r->name }}</span>
                                @endforeach
                            </div>
                            @endif
                            <form method="POST" action="{{ route('hospital-settings.ward.rooms.store') }}" class="mt-3 flex flex-wrap items-end gap-2 border-t border-gray-50 pt-3">
                                @csrf
                                <input type="hidden" name="ward_id" value="{{ $ward->id }}">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-0.5">Add room (label / number)</label>
                                    <input type="text" name="room_name" maxlength="120" class="w-40 px-2 py-1.5 border border-gray-200 rounded-lg text-sm" placeholder="e.g. 101, ICU-2" required>
                                </div>
                                <button type="submit" class="px-3 py-1.5 text-xs font-semibold text-white bg-slate-700 rounded-lg hover:bg-slate-800">
                                    Add room
                                </button>
                            </form>

                            @if($wardRooms->isNotEmpty())
                                <div class="mt-3 space-y-2">
                                    <p class="text-xs font-medium text-gray-700">Beds per room</p>
                                    @foreach($wardRooms as $room)
                                        <div class="flex flex-wrap items-center gap-x-3 gap-y-2 rounded-lg bg-gray-50/80 px-3 py-2 border border-gray-100">
                                            <div class="min-w-0 flex-1">
                                                <span class="text-sm font-medium text-gray-900">{{ $room->name }}</span>
                                                <span class="text-xs text-gray-500 ml-2">{{ (int) ($roomBedCounts[$room->id] ?? 0) }} bed(s)</span>
                                            </div>
                                            <form method="POST" action="{{ route('hospital-settings.ward.room.beds') }}" class="flex flex-wrap items-end gap-2">
                                                @csrf
                                                <input type="hidden" name="room_id" value="{{ $room->id }}">
                                                <label class="sr-only">Bed count for {{ $room->name }}</label>
                                                <input type="number" name="bed_count" min="1" max="500" title="Target number of beds in this room"
                                                    class="w-24 px-2 py-1.5 border border-gray-200 rounded-lg text-sm" placeholder="#" required>
                                                <button type="submit" class="px-3 py-1.5 text-xs font-semibold text-white bg-brand-blue rounded-lg hover:bg-brand-blue-dark whitespace-nowrap">
                                                    Apply
                                                </button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                                <p class="text-[11px] text-gray-400 mt-2">Each room gets its own bed numbers (e.g. R15-B-1). IPD admission picks ward → room → bed.</p>
                            @else
                                <form method="POST" action="{{ route('hospital-settings.ward.beds') }}" class="mt-3 flex flex-wrap items-end gap-2">
                                    @csrf
                                    <input type="hidden" name="ward_id" value="{{ $ward->id }}">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-0.5">Beds (creates default room)</label>
                                        <input type="number" name="bed_count" min="1" max="500" class="w-28 px-2 py-1.5 border border-gray-200 rounded-lg text-sm" placeholder="e.g. 10" required>
                                    </div>
                                    <button type="submit" class="px-3 py-1.5 text-xs font-semibold text-white bg-brand-blue rounded-lg hover:bg-brand-blue-dark">
                                        Apply beds
                                    </button>
                                </form>
                                <p class="text-[11px] text-gray-400 mt-1">No rooms yet — beds go into one auto-created room. Add named rooms above for ward → room → bed structure.</p>
                            @endif
                        </div>
                        <div class="flex flex-col gap-2 flex-shrink-0 pt-0.5">
                            <button type="button"
                                @click='openEdit(@json($wardEditPayload))'
                                class="inline-flex items-center justify-center gap-1 px-3 py-1.5 text-xs font-semibold text-brand-blue border border-brand-blue/30 rounded-lg hover:bg-brand-blue/5 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Edit
                            </button>
                            <form method="POST" action="{{ route('hospital-settings.ward.destroy', $ward->id) }}"
                                onsubmit="return confirm('Delete ward &quot;{{ $ward->name }}&quot;? Rooms and beds under it will be removed. This cannot be undone if there are no active admissions.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-1 px-3 py-1.5 text-xs font-semibold text-red-700 border border-red-200 rounded-lg hover:bg-red-50 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="px-6 py-12 text-center">
                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 5h2a2 2 0 002-2v-4a2 2 0 00-2-2h-2a2 2 0 00-2 2v4a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-sm text-gray-500 font-medium">No wards configured yet</p>
                        <p class="text-xs text-gray-400 mt-1">Click "Add Ward" to create your first ward.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Add Ward Modal --}}
            <div x-show="wardOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="wardOpen = false">
                <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="wardOpen = false"></div>
                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto" @click.stop>
                    <div class="flex items-center justify-between p-6 border-b border-gray-100">
                        <h2 class="text-lg font-semibold text-gray-900">Add New Ward</h2>
                        <button @click="wardOpen = false" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('hospital-settings.ward.store') }}" class="p-6 space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ward Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required placeholder="e.g. General Ward A"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ward Type <span class="text-red-500">*</span></label>
                            <select name="type" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue transition-colors bg-white">
                                <option value="">Select type...</option>
                                <option value="general">General</option>
                                <option value="icu">ICU</option>
                                <option value="emergency">Emergency</option>
                                <option value="maternity">Maternity</option>
                                <option value="paediatric">Paediatric</option>
                                <option value="surgical">Surgical</option>
                                <option value="medical">Medical</option>
                                <option value="private">Private</option>
                                <option value="semi_private">Semi-Private</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Total Beds <span class="text-red-500">*</span></label>
                                <input type="number" name="total_beds" required min="1" placeholder="10"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue transition-colors">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Floor</label>
                                <input type="text" name="floor" placeholder="e.g. Ground, 1st, 2nd"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue transition-colors">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                            <textarea name="notes" rows="2" placeholder="Any additional notes..."
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue transition-colors resize-none"></textarea>
                        </div>
                        <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                            <button type="button" @click="wardOpen = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                Cancel
                            </button>
                            <button type="submit" class="px-5 py-2 text-sm font-medium text-white bg-brand-blue rounded-lg hover:bg-brand-blue-dark transition-colors shadow-sm">
                                Add Ward
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Edit Ward Modal --}}
            <div x-show="editOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="closeEdit()">
                <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="closeEdit()"></div>
                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto" @click.stop>
                    <div class="flex items-center justify-between p-6 border-b border-gray-100">
                        <h2 class="text-lg font-semibold text-gray-900">Edit Ward</h2>
                        <button type="button" @click="closeEdit()" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <form method="POST" x-bind:action="'{{ url('/hospital-settings/wards') }}/' + (edit.id || '')" class="p-6 space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ward Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" x-model="edit.name" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ward Type <span class="text-red-500">*</span></label>
                            <select name="type" x-model="edit.type" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue transition-colors bg-white">
                                <option value="general">General</option>
                                <option value="icu">ICU</option>
                                <option value="emergency">Emergency</option>
                                <option value="maternity">Maternity</option>
                                <option value="paediatric">Paediatric</option>
                                <option value="surgical">Surgical</option>
                                <option value="medical">Medical</option>
                                <option value="private">Private</option>
                                <option value="semi_private">Semi-Private</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Total beds (legacy)</label>
                                <input type="number" name="total_beds" x-model="edit.total_beds" min="0" max="2000"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue transition-colors">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Floor</label>
                                <input type="text" name="floor" x-model="edit.floor"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue transition-colors">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Wing</label>
                            <input type="text" name="wing" x-model="edit.wing"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue transition-colors">
                        </div>
                        <div class="flex flex-wrap gap-4">
                            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                <input type="checkbox" name="is_icu" value="1" x-model="edit.is_icu" class="rounded border-gray-300 text-brand-blue focus:ring-brand-blue">
                                ICU
                            </label>
                            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                <input type="checkbox" name="is_active" value="1" x-model="edit.is_active" class="rounded border-gray-300 text-brand-blue focus:ring-brand-blue">
                                Active
                            </label>
                        </div>
                        <p class="text-[11px] text-gray-400">Rooms and beds are managed below each ward on this page.</p>
                        <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                            <button type="button" @click="closeEdit()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                Cancel
                            </button>
                            <button type="submit" class="px-5 py-2 text-sm font-medium text-white bg-brand-blue rounded-lg hover:bg-brand-blue-dark transition-colors shadow-sm">
                                Save changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
