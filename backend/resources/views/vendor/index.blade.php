@extends('layouts.app')

@section('title', 'Vendor Lab Portal')

@section('breadcrumb', 'Vendor Lab')

@section('content')
<div class="p-6 space-y-6">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-2xl p-6 text-white">
        <h1 class="text-2xl font-bold">Vendor Lab Portal</h1>
        <p class="text-purple-100 mt-1">Manage external lab orders, track processing, and upload results</p>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">New Today</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['new_today'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Processing</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['processing'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Ready</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['ready'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">This Month</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['total_month'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Orders Table --}}
    <div class="bg-white rounded-xl border shadow-sm">
        <div class="px-6 py-4 border-b flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Lab Orders</h2>
            <form method="GET" class="flex gap-2">
                <select name="status" class="text-sm border rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-purple-500" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
                    <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                    <option value="sample_collected" {{ request('status') == 'sample_collected' ? 'selected' : '' }}>Sample Collected</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>Ready</option>
                    <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                </select>
            </form>
        </div>

        @if($orders->isEmpty())
            <div class="p-12 text-center text-gray-400">
                <svg class="mx-auto h-12 w-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <p class="font-medium">No lab orders found</p>
                <p class="text-sm mt-1">Orders from partner clinics will appear here.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order #</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Patient</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Clinic</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tests</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($orders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $order->order_number }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $order->patient?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $order->clinic?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                @if(!empty($vendorCanLoadTests))
                                    @forelse($order->labOrderTests ?? [] as $test)
                                        <span class="inline-block bg-gray-100 text-gray-700 text-xs px-2 py-0.5 rounded-full mr-1 mb-1">{{ $test->test_name }}</span>
                                    @empty
                                        <span class="text-gray-400 text-xs" title="Integration order">{{ \Illuminate\Support\Str::limit($order->display_test_names, 80) }}</span>
                                    @endforelse
                                @else
                                    <span class="text-gray-400 text-xs" title="Line items table not available">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $statusColors = [
                                        'new' => 'bg-blue-100 text-blue-700',
                                        'accepted' => 'bg-cyan-100 text-cyan-700',
                                        'sample_collected' => 'bg-teal-100 text-teal-700',
                                        'processing' => 'bg-amber-100 text-amber-700',
                                        'ready' => 'bg-green-100 text-green-700',
                                        'sent' => 'bg-gray-100 text-gray-700',
                                        'cancelled' => 'bg-red-100 text-red-700',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $order->created_at ? $order->created_at->format('d M Y H:i') : '—' }}</td>
                            <td class="px-4 py-3 text-sm">
                                <div class="flex gap-2">
                                    @if(in_array($order->status, ['new']))
                                        <form method="POST" action="{{ route('vendor.accept', $order) }}">
                                            @csrf
                                            <button type="submit" class="text-xs bg-green-600 text-white px-3 py-1 rounded-lg hover:bg-green-700 transition">Accept</button>
                                        </form>
                                    @endif
                                    @if(in_array($order->status, ['accepted', 'sample_collected', 'processing']))
                                        <button onclick="document.getElementById('upload-modal-{{ $order->id }}').showModal()" class="text-xs bg-purple-600 text-white px-3 py-1 rounded-lg hover:bg-purple-700 transition">Upload Result</button>
                                    @endif
                                </div>

                                @if(in_array($order->status, ['accepted', 'sample_collected', 'processing']))
                                <dialog id="upload-modal-{{ $order->id }}" class="rounded-xl p-0 w-full max-w-md backdrop:bg-black/50">
                                    <div class="p-6">
                                        <h3 class="text-lg font-semibold mb-4">Upload Result — {{ $order->order_number }}</h3>
                                        <form method="POST" action="{{ route('vendor.upload', $order) }}" enctype="multipart/form-data">
                                            @csrf
                                            <div class="space-y-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Result PDF</label>
                                                    <input type="file" name="result_file" accept=".pdf" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                                    <textarea name="notes" rows="2" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500" placeholder="Optional notes"></textarea>
                                                </div>
                                                <div class="flex justify-end gap-2">
                                                    <button type="button" onclick="this.closest('dialog').close()" class="px-4 py-2 text-sm border rounded-lg hover:bg-gray-50">Cancel</button>
                                                    <button type="submit" class="px-4 py-2 text-sm bg-purple-600 text-white rounded-lg hover:bg-purple-700">Upload</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </dialog>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    {{-- Partner labs (VendorLab has many clinics via pivot — do not use ->clinic, it does not exist) --}}
    @if($partnerClinics->isNotEmpty())
    <div class="bg-white rounded-xl border shadow-sm">
        <div class="px-6 py-4 border-b">
            <h2 class="text-lg font-semibold text-gray-900">Partner labs</h2>
        </div>
        <div class="divide-y divide-gray-200">
            @foreach($partnerClinics as $vendorLab)
            <div class="px-6 py-3 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ $vendorLab->name ?? '—' }}</p>
                    <p class="text-xs text-gray-500">{{ $vendorLab->city ?? '' }}</p>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ ($vendorLab->is_active ?? false) ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ ($vendorLab->is_active ?? false) ? 'Active' : 'Inactive' }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
