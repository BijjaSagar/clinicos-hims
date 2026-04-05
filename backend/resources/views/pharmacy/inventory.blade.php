@extends('layouts.app')

@section('title', 'Pharmacy Inventory')
@section('breadcrumb', 'Pharmacy Inventory')

@section('content')
<div id="pharmacy-inventory-root" x-data="inventoryPage()" class="p-4 sm:p-5 lg:p-7 space-y-5">

    {{-- Header --}}
    @php
        $viewMode = $viewMode ?? 'clinic';
        $isNational = $viewMode === 'national';
    @endphp

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900 font-display">Pharmacy Inventory</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage medicines, stock levels & pricing</p>
        </div>
        <div class="flex gap-2">
            {{-- url() avoids RouteNotFoundException when route:cache is stale or names mismatch in production --}}
            <a href="{{ url('/pharmacy') }}" class="px-3 py-2 text-sm font-semibold text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                Back
            </a>
            <a href="{{ url('/pharmacy/items/create') }}" class="px-3 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50">
                Full form
            </a>
            <button type="button" @click="openAddMedicineBlank()"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:shadow-lg"
                style="background:linear-gradient(135deg,#1447E6,#0891B2);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Add Medicine
            </button>
        </div>
    </div>

    {{-- Clinic stock vs national imported catalog (large list, paginated) --}}
    <div class="flex flex-wrap gap-2">
        <a href="{{ url('/pharmacy/inventory?view=clinic') }}"
            class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold border transition-colors {{ $isNational ? 'border-gray-200 text-gray-600 bg-white hover:bg-gray-50' : 'border-blue-200 text-white shadow-sm' }}"
            style="{{ $isNational ? '' : 'background:linear-gradient(135deg,#1447E6,#0891B2);' }}">
            My clinic inventory
        </a>
        <a href="{{ url('/pharmacy/inventory?view=national') }}"
            class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold border transition-colors {{ $isNational ? 'border-blue-200 text-white shadow-sm' : 'border-gray-200 text-gray-600 bg-white hover:bg-gray-50' }}"
            style="{{ $isNational ? 'background:linear-gradient(135deg,#1447E6,#0891B2);' : '' }}">
            National database
        </a>
        <span class="text-xs text-gray-500 self-center hidden sm:inline">~{{ number_format((float) ($catalogCount ?? 0), 0) }} products in imported catalog</span>
    </div>

    @if(session('success'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium" style="background:#ecfdf5;color:#059669;border:1px solid #a7f3d0;">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="px-4 py-3 rounded-xl text-sm font-medium" style="background:#fff1f2;color:#dc2626;border:1px solid #fecaca;">
        {{ session('error') }}
    </div>
    @endif

    @if(isset($errors) && $errors->any())
    <div class="px-4 py-3 rounded-xl text-sm" style="background:#fff1f2;color:#dc2626;border:1px solid #fecaca;">
        <p class="font-semibold mb-1">Could not save — please fix the following:</p>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @include('pharmacy.partials.catalog-stock-hint', ['context' => 'inventory'])

    {{-- Filter Bar --}}
    <div class="bg-white border border-gray-200 rounded-xl p-4">
        <form method="GET" action="{{ url('/pharmacy/inventory') }}" class="flex flex-col sm:flex-row gap-3">
            @if($isNational)
            <input type="hidden" name="view" value="national">
            @endif
            {{-- Search --}}
            <div class="flex-1 relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ $isNational ? 'Search product name, manufacturer, composition…' : 'Search medicine name, generic name…' }}"
                    class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            @if(!$isNational)
            {{-- Category Filter --}}
            <select name="category" class="px-3 py-2 border border-gray-200 rounded-lg text-sm text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Categories</option>
                @foreach($categories ?? ['Antibiotics', 'Analgesics', 'Antacids', 'Vitamins', 'Cardiac', 'Diabetic', 'Others'] as $cat)
                <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>

            {{-- Stock Status --}}
            <select name="stock_status" class="px-3 py-2 border border-gray-200 rounded-lg text-sm text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Stock</option>
                <option value="in-stock"    {{ request('stock_status') === 'in-stock'    ? 'selected' : '' }}>In Stock</option>
                <option value="low-stock"   {{ request('stock_status') === 'low-stock'   ? 'selected' : '' }}>Low Stock</option>
                <option value="out-of-stock" {{ request('stock_status') === 'out-of-stock' ? 'selected' : '' }}>Out of Stock</option>
            </select>
            @endif

            <button type="submit" class="px-4 py-2 bg-gray-900 text-white text-sm font-semibold rounded-lg hover:bg-gray-700 transition-colors">
                {{ $isNational ? 'Search' : 'Filter' }}
            </button>
        </form>
    </div>

    {{-- Inventory Table --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h3 class="text-sm font-bold text-gray-900">{{ $isNational ? 'National medicine database' : 'My clinic stock' }}</h3>
                @if($isNational)
                <p class="text-xs text-gray-500 mt-0.5">Imported catalog (paginated). Use <strong>+ Add to clinic</strong> to create a shelf SKU from a product.</p>
                @endif
            </div>
            <span class="text-xs text-gray-400">{{ (int) ($items->total() ?? 0) }} items</span>
        </div>
        <div class="overflow-x-auto">
            @if($isNational)
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Product</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Manufacturer</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Composition</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">MRP</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Rx</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($items ?? [] as $row)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <p class="text-sm font-semibold text-gray-900">{{ $row->name ?? '—' }}</p>
                            <p class="text-xs text-gray-400">ID {{ $row->id }}</p>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 max-w-[14rem] break-words">{{ $row->manufacturer ?: '—' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 max-w-md">
                            {{ \Illuminate\Support\Str::limit((string) ($row->composition ?? ''), 120) }}
                        </td>
                        <td class="px-4 py-3 text-sm font-semibold text-gray-900">
                            @if($row->mrp !== null)
                                ₹{{ number_format((float) $row->mrp, 2) }}
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            @if($row->rx_required === true || ($row->prescription_label ?? '') !== '')
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">Rx</span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        @php
                            $pharmacyCatalogPayload = [
                                'id' => (int) $row->id,
                                'name' => (string) ($row->name ?? ''),
                                'manufacturer' => (string) ($row->manufacturer ?? ''),
                                'composition' => \Illuminate\Support\Str::limit((string) ($row->composition ?? ''), 4000, '…'),
                                'mrp' => $row->mrp,
                            ];
                            $pharmacyCatalogJson = json_encode($pharmacyCatalogPayload, JSON_UNESCAPED_UNICODE);
                            if ($pharmacyCatalogJson === false) {
                                $pharmacyCatalogJson = json_encode([
                                    'id' => (int) $row->id,
                                    'name' => 'Product',
                                    'manufacturer' => '',
                                    'composition' => '',
                                    'mrp' => null,
                                ], JSON_UNESCAPED_UNICODE);
                            }
                        @endphp
                        <td class="px-4 py-3 relative z-[60] align-middle">
                            {{-- Native onclick + Alpine.$data: x-on:click inside tables / nested x-data can fail to bind reliably --}}
                            <button type="button"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white relative z-[70] cursor-pointer"
                                style="background:#1447E6;"
                                data-pharmacy-catalog="{{ e($pharmacyCatalogJson) }}"
                                onclick="return window.pharmacyInventoryAddFromCatalogClick(this, event)">
                                <svg class="w-3.5 h-3.5 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                <span class="pointer-events-none">Add to clinic</span>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-16 text-center">
                            <p class="text-sm font-semibold text-gray-500">No products in this view</p>
                            <p class="text-xs text-gray-400 mt-2">Try another search, or confirm the national catalog import ran on the server.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            @else
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Medicine</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Category</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Current Stock</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Reorder Level</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Nearest Expiry</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Price / Unit</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($items ?? [] as $item)
                    @php
                        $stockQty    = $item->current_stock ?? 0;
                        $reorderLvl  = $item->reorder_level ?? 0;
                        if ($stockQty <= 0) {
                            $stockStatus = ['label' => 'Out of Stock', 'bg' => '#fff1f2', 'color' => '#dc2626'];
                        } elseif ($stockQty <= $reorderLvl) {
                            $stockStatus = ['label' => 'Low Stock',    'bg' => '#fffbeb', 'color' => '#d97706'];
                        } else {
                            $stockStatus = ['label' => 'In Stock',     'bg' => '#ecfdf5', 'color' => '#059669'];
                        }
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <p class="text-sm font-semibold text-gray-900">{{ $item->name ?? '—' }}</p>
                            @if($item->generic_name ?? null)
                            <p class="text-xs text-gray-400">{{ $item->generic_name }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $item->category ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="text-sm font-semibold text-gray-900">{{ $stockQty }}</span>
                            <span class="text-xs text-gray-400 ml-1">{{ $item->unit ?? '' }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $reorderLvl }} {{ $item->unit ?? '' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            @if($item->nearest_expiry ?? null)
                                @php
                                    $expiry = \Carbon\Carbon::parse($item->nearest_expiry);
                                    $expiring = $expiry->diffInDays(now()) <= 90 && $expiry->isFuture();
                                @endphp
                                <span class="{{ $expiring ? 'text-amber-600 font-semibold' : '' }}">
                                    {{ $expiry->format('M Y') }}
                                    @if($expiring) (soon)@endif
                                </span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm font-semibold text-gray-900">₹{{ number_format((float) ($item->price_per_unit ?? 0), 2) }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold"
                                  style="background:{{ $stockStatus['bg'] }};color:{{ $stockStatus['color'] }};">
                                {{ $stockStatus['label'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1">
                                {{-- data-item-name + e(): avoids @json on invalid UTF-8 / odd types from DB --}}
                                <button type="button" class="p-1.5 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors" title="Add Stock"
                                    data-item-name="{{ e($item->name ?? '') }}"
                                    @click="openAddStock({{ $item->id }}, $event.currentTarget.getAttribute('data-item-name') || '')">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </button>
                                <button class="p-1.5 rounded-lg text-gray-400 hover:text-green-600 hover:bg-green-50 transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                                    <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-semibold text-gray-500">No medicines found</p>
                                <button type="button" @click="openAddMedicineBlank()" class="text-sm font-semibold" style="color:#1447E6;">Add first medicine →</button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            @endif
        </div>

        @if(isset($items) && $items->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">
            {{-- Default pagination view from framework; explicit pagination::tailwind can 500 if view missing --}}
            {{ $items->withQueryString()->links() }}
        </div>
        @endif
    </div>

    {{-- Add Medicine Modal --}}
    <div x-show="showAddModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="closeAddModal()"></div>
            <div class="relative bg-white rounded-2xl shadow-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-base font-bold text-gray-900">
                        <span x-show="!addFromCatalog">Add New Medicine</span>
                        <span x-show="addFromCatalog" x-cloak>Add to clinic</span>
                    </h3>
                    <button type="button" @click="closeAddModal()" class="p-1.5 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form id="pharmacy-add-item-form" method="POST" action="{{ url('/pharmacy/items') }}" class="p-6 space-y-4">
                    @csrf
                    <input type="hidden" name="medicine_catalog_id" value="">
                    <p x-show="!addFromCatalog" class="text-xs text-gray-500 sm:col-span-2">Type in <strong>Medicine name</strong> to search the national catalog (~{{ number_format((float) ($catalogCount ?? 0), 0) }} products). Click a row to auto-fill.</p>
                    <div x-show="addFromCatalog" x-cloak class="rounded-xl border border-blue-200 bg-blue-50/90 p-4 text-sm text-blue-950 space-y-2">
                        <p class="font-semibold text-blue-900">Product details (from national catalog)</p>
                        <div x-show="catalogPickSummary" class="space-y-1">
                            <p><span class="text-blue-800/80">Name:</span> <span class="font-medium text-gray-900" x-text="catalogPickSummary ? catalogPickSummary.name : ''"></span></p>
                            <p x-show="catalogPickSummary && catalogPickSummary.manufacturer"><span class="text-blue-800/80">Manufacturer:</span> <span x-text="catalogPickSummary ? catalogPickSummary.manufacturer : ''"></span></p>
                            <p x-show="catalogPickSummary && catalogPickSummary.composition" class="text-xs text-gray-700 leading-snug"><span class="text-blue-800/80">Composition:</span> <span x-text="catalogPickSummary ? catalogPickSummary.composition : ''"></span></p>
                            <p class="text-xs text-blue-900/90">Catalog ID <span x-text="catalogPickSummary ? catalogPickSummary.catalogId : ''"></span> · MRP ₹<span x-text="catalogPickSummary ? catalogPickSummary.mrpLabel : '—'"></span></p>
                        </div>
                        <p class="text-xs font-medium text-blue-900 pt-1 border-t border-blue-100">Enter <strong>opening stock</strong> and <strong>batch expiry</strong> below (required to create stock). Category &amp; unit are auto-filled — adjust if needed.</p>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2 relative">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Medicine Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required placeholder="e.g. Dolo 650 — type 2+ letters to search catalog"
                                @input="debouncedCatalogSearch($event.target.value)"
                                class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <div x-show="catalogLoading" class="text-xs text-gray-400 mt-1">Searching catalog…</div>
                            <div x-show="catalogResults.length" x-cloak class="absolute z-10 mt-1 w-full max-h-48 overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-lg text-sm">
                                <template x-for="row in catalogResults" :key="row.id">
                                    <button type="button" @click="applyCatalogPick(row)"
                                        class="w-full text-left px-3 py-2 hover:bg-blue-50 border-b border-gray-100 last:border-0">
                                        <span class="font-medium text-gray-900" x-text="row.name"></span>
                                        <span class="block text-xs text-gray-500" x-text="(row.manufacturer || '') + (row.mrp ? ' · ₹' + row.mrp : '')"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Manufacturer</label>
                            <input type="text" name="manufacturer" placeholder="From catalog or enter manually"
                                class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Generic Name</label>
                            <input type="text" name="generic_name" placeholder="e.g. Amoxicillin"
                                class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
                            <select name="category" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Select…</option>
                                @foreach(['Antibiotics', 'Analgesics', 'Antacids', 'Vitamins', 'Cardiac', 'Diabetic', 'Dermatology', 'Others'] as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Unit <span class="text-red-500">*</span></label>
                            <select name="unit" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Select…</option>
                                <option value="Tablet">Tablet</option>
                                <option value="Capsule">Capsule</option>
                                <option value="Syrup (ml)">Syrup (ml)</option>
                                <option value="Injection">Injection</option>
                                <option value="Cream (gm)">Cream (gm)</option>
                                <option value="Drops">Drops</option>
                                <option value="Sachet">Sachet</option>
                                <option value="Strip">Strip</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Reorder Level</label>
                            <input type="number" name="reorder_level" min="0" placeholder="10"
                                class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div id="pharmacy-add-stock-expiry-section" class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4 transition-shadow"
                            x-bind:class="addFromCatalog ? 'rounded-xl border-2 border-blue-400 bg-blue-50/50 p-3 sm:p-4' : ''">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Opening stock
                                <span x-show="addFromCatalog" class="text-red-500">*</span>
                            </label>
                            <input type="number" name="initial_stock" step="1" placeholder="Qty to receive"
                                x-bind:min="addFromCatalog ? 1 : 0"
                                x-bind:required="addFromCatalog"
                                class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-xs text-gray-400 mt-1" x-show="!addFromCatalog">Creates one batch. Use &quot;Add Stock&quot; on the row for more batches.</p>
                            <p class="text-xs text-blue-800 mt-1 font-medium" x-show="addFromCatalog" x-cloak>How many units are you adding now?</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Batch expiry
                                <span x-show="addFromCatalog" class="text-red-500">*</span>
                            </label>
                            <input type="date" name="initial_expiry_date"
                                x-bind:required="addFromCatalog"
                                class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-xs text-blue-800 mt-1 font-medium" x-show="addFromCatalog" x-cloak>Expiry date for this opening batch.</p>
                        </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Price per Unit (₹) <span class="text-red-500">*</span></label>
                            <input type="number" name="price_per_unit" required min="0" step="0.01" placeholder="5.00"
                                class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">HSN Code</label>
                            <input type="text" name="hsn_code" placeholder="30049099"
                                class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">GST Rate (%)</label>
                            <select name="gst_rate" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="0">0%</option>
                                <option value="5" selected>5%</option>
                                <option value="12">12%</option>
                                <option value="18">18%</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-1">
                        <button type="button" @click="closeAddModal()"
                            class="px-4 py-2 text-sm font-semibold text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50">Cancel</button>
                        <button type="submit"
                            class="px-5 py-2 text-sm font-semibold text-white rounded-xl" style="background:#1447E6;">
                            <span x-show="!addFromCatalog">Add Medicine</span>
                            <span x-show="addFromCatalog" x-cloak>Save to clinic</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Add Stock Modal --}}
    <div x-show="showStockModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showStockModal = false"></div>
            <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-base font-bold text-gray-900">Add Stock — <span x-text="stockItem.name"></span></h3>
                    <button @click="showStockModal = false" class="p-1.5 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form method="POST" action="{{ url('/pharmacy/stock-in') }}" class="p-6 space-y-4">
                    @csrf
                    <input type="hidden" name="item_id" x-bind:value="stockItem.id">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Quantity <span class="text-red-500">*</span></label>
                            <input type="number" name="quantity_in" required min="1"
                                class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Batch No. <span class="text-red-500">*</span></label>
                            <input type="text" name="batch_number" required placeholder="e.g. BTH001"
                                class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date <span class="text-red-500">*</span></label>
                            <input type="date" name="expiry_date" required
                                class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Purchase rate / MRP (₹) <span class="text-red-500">*</span></label>
                            <input type="number" name="purchase_rate" required step="0.01" min="0"
                                class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-xs text-gray-400 mt-1">MRP defaults to this rate if left unset server-side.</p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                        <input type="text" name="supplier_name" placeholder="Supplier name"
                            class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="showStockModal = false"
                            class="px-4 py-2 text-sm font-semibold text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50">Cancel</button>
                        <button type="submit"
                            class="px-5 py-2 text-sm font-semibold text-white rounded-xl" style="background:#059669;">
                            Add Stock
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

@push('scripts')
@php
    $inventoryMedicineCatalogSearchUrl = url('/pharmacy/medicine-catalog/search');
@endphp
<script>
/**
 * National "Add to clinic" — native onclick (reliable in tables); then calls Alpine component on #pharmacy-inventory-root.
 */
function pharmacyInventoryGetComponentApi(rootEl) {
    if (!rootEl) {
        return null;
    }
    if (typeof Alpine !== 'undefined' && typeof Alpine.$data === 'function') {
        try {
            const d = Alpine.$data(rootEl);
            if (d && typeof d.openAddFromCatalog === 'function') {
                return d;
            }
        } catch (e) {
            console.warn('[pharmacy] Alpine.$data failed', e);
        }
    }
    const stack = rootEl._x_dataStack;
    if (stack && stack.length) {
        const d = stack[stack.length - 1];
        if (d && typeof d.openAddFromCatalog === 'function') {
            console.log('[pharmacy] pharmacyInventoryGetComponentApi: using _x_dataStack fallback');
            return d;
        }
    }
    return null;
}

function pharmacyInventoryAddFromCatalogClick(button, ev) {
    console.log('[pharmacy] pharmacyInventoryAddFromCatalogClick', button);
    if (ev) {
        try {
            ev.preventDefault();
            ev.stopPropagation();
        } catch (e) {
            console.warn('[pharmacy] pharmacyInventoryAddFromCatalogClick: preventDefault failed', e);
        }
    }
    const raw = button && button.getAttribute ? button.getAttribute('data-pharmacy-catalog') : null;
    if (!raw) {
        console.error('[pharmacy] pharmacyInventoryAddFromCatalogClick: missing data-pharmacy-catalog');
        return false;
    }
    let row;
    try {
        row = JSON.parse(raw);
    } catch (e) {
        console.error('[pharmacy] pharmacyInventoryAddFromCatalogClick: JSON.parse failed', e, raw);
        return false;
    }
    const root = document.getElementById('pharmacy-inventory-root');
    if (!root) {
        console.error('[pharmacy] pharmacyInventoryAddFromCatalogClick: #pharmacy-inventory-root not found');
        return false;
    }
    let tries = 0;
    const run = () => {
        tries++;
        const api = pharmacyInventoryGetComponentApi(root);
        if (api) {
            console.log('[pharmacy] pharmacyInventoryAddFromCatalogClick: calling openAddFromCatalog', row);
            api.openAddFromCatalog(row);
            return;
        }
        if (tries < 30) {
            setTimeout(run, 50);
            return;
        }
        console.error('[pharmacy] pharmacyInventoryAddFromCatalogClick: Alpine component API not ready after retries');
    };
    setTimeout(run, 0);
    return false;
}

function inventoryPage() {
    return {
        showAddModal: false,
        showStockModal: false,
        stockItem: { id: null, name: '' },
        addFromCatalog: false,
        catalogPickSummary: null,
        catalogSearchUrl: @json($inventoryMedicineCatalogSearchUrl),
        catalogResults: [],
        catalogLoading: false,
        catalogSearchTimer: null,
        viewMode: @json($viewMode ?? 'clinic'),
        init() {
            console.log('[pharmacy] inventory init', { viewMode: this.viewMode, catalogSearchUrl: this.catalogSearchUrl });
            try {
                const params = new URLSearchParams(window.location.search || '');
                const v = params.get('add_medicine');
                if (v === '1' || v === 'true' || v === 'yes') {
                    this.openAddMedicineBlank();
                    console.log('[pharmacy] inventory: opened Add New Medicine modal from ?add_medicine=', v);
                    const url = new URL(window.location.href);
                    url.searchParams.delete('add_medicine');
                    const next = url.pathname + (url.search ? url.search : '') + (url.hash || '');
                    window.history.replaceState({}, '', next || url.pathname);
                }
            } catch (e) {
                console.error('[pharmacy] inventory init add_medicine failed', e);
            }
        },
        openAddMedicineBlank() {
            console.log('[pharmacy] openAddMedicineBlank');
            this.addFromCatalog = false;
            this.catalogPickSummary = null;
            this.showAddModal = true;
        },
        closeAddModal() {
            console.log('[pharmacy] closeAddModal');
            this.showAddModal = false;
            this.addFromCatalog = false;
            this.catalogPickSummary = null;
        },
        /**
         * National table button: payload lives in data-pharmacy-catalog (avoids broken multiline @click / Blade @ issues).
         */
        openAddFromCatalogFromEl(ev) {
            console.log('[pharmacy] openAddFromCatalogFromEl', ev);
            const el = ev && ev.currentTarget ? ev.currentTarget : null;
            if (!el) {
                console.warn('[pharmacy] openAddFromCatalogFromEl: no currentTarget');
                return;
            }
            const raw = el.getAttribute('data-pharmacy-catalog');
            if (!raw) {
                console.warn('[pharmacy] openAddFromCatalogFromEl: missing data-pharmacy-catalog');
                return;
            }
            let row;
            try {
                row = JSON.parse(raw);
            } catch (e) {
                console.error('[pharmacy] openAddFromCatalogFromEl: JSON.parse failed', e, raw);
                return;
            }
            this.openAddFromCatalog(row);
        },
        openAddFromCatalog(row) {
            console.log('[pharmacy] openAddFromCatalog', row);
            if (!row || typeof row !== 'object') {
                console.warn('[pharmacy] openAddFromCatalog: invalid row');
                return;
            }
            this.addFromCatalog = true;
            this.showAddModal = true;
            const run = () => {
                this.applyCatalogPick(row);
                this.afterCatalogPickFocus();
            };
            if (typeof this.$nextTick === 'function') {
                this.$nextTick(run);
            } else {
                setTimeout(run, 0);
            }
        },
        afterCatalogPickFocus() {
            const wrap = document.getElementById('pharmacy-add-stock-expiry-section');
            if (wrap) {
                console.log('[pharmacy] afterCatalogPickFocus: scroll into view');
                try {
                    wrap.scrollIntoView({ behavior: 'smooth', block: 'center' });
                } catch (e) {
                    console.warn('[pharmacy] afterCatalogPickFocus scroll failed', e);
                }
            }
            const stockInput = document.querySelector('#pharmacy-add-item-form [name="initial_stock"]');
            if (stockInput) {
                setTimeout(() => {
                    try {
                        stockInput.focus();
                        stockInput.select();
                    } catch (e) {
                        console.warn('[pharmacy] afterCatalogPickFocus focus failed', e);
                    }
                }, 400);
            }
        },
        inferUnitFromProductName(name) {
            const s = String(name || '').toLowerCase();
            if (s.includes('tablet')) return 'Tablet';
            if (s.includes('capsule')) return 'Capsule';
            if (s.includes('syrup')) return 'Syrup (ml)';
            if (s.includes('injection') || s.includes('inject')) return 'Injection';
            if (s.includes('cream') || s.includes('ointment') || s.includes('gel')) return 'Cream (gm)';
            if (s.includes('drops')) return 'Drops';
            if (s.includes('sachet')) return 'Sachet';
            if (s.includes('strip')) return 'Strip';
            return 'Tablet';
        },
        openAddStock(id, name) {
            this.stockItem = { id, name };
            this.showStockModal = true;
        },
        debouncedCatalogSearch(q) {
            clearTimeout(this.catalogSearchTimer);
            if (!q || String(q).trim().length < 2) {
                this.catalogResults = [];
                return;
            }
            const query = String(q).trim();
            this.catalogSearchTimer = setTimeout(() => this.fetchCatalog(query), 400);
        },
        async fetchCatalog(q) {
            this.catalogLoading = true;
            try {
                const r = await fetch(this.catalogSearchUrl + '?q=' + encodeURIComponent(q), { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                const j = await r.json();
                this.catalogResults = (j && j.results) ? j.results : [];
            } catch (e) {
                console.error('[pharmacy] catalog search failed', e);
                this.catalogResults = [];
            }
            this.catalogLoading = false;
        },
        applyCatalogPick(row) {
            console.log('[pharmacy] applyCatalogPick', row, 'addFromCatalog=', this.addFromCatalog);
            const f = document.getElementById('pharmacy-add-item-form');
            if (!f || !row) return;
            const set = (name, val) => {
                const el = f.querySelector('[name="' + name + '"]');
                if (el) el.value = val != null ? String(val) : '';
            };
            set('name', row.name);
            set('manufacturer', row.manufacturer || '');
            const comp = row.composition != null ? String(row.composition) : '';
            let gen = '';
            if (comp) {
                const first = comp.split('+')[0].trim();
                const m = first.match(/^([\w\s\-.]+?)\s*\(/);
                gen = m ? m[1].trim() : first.substring(0, 120);
            }
            set('generic_name', gen);
            if (row.mrp != null && row.mrp !== '') {
                set('price_per_unit', parseFloat(row.mrp).toFixed(2));
            }
            set('medicine_catalog_id', row.id);

            const cat = f.querySelector('[name="category"]');
            if (cat) {
                if (this.addFromCatalog) {
                    cat.value = 'Others';
                } else if (!cat.value) {
                    cat.value = 'Others';
                }
            }
            const unitEl = f.querySelector('[name="unit"]');
            if (unitEl) {
                unitEl.value = this.inferUnitFromProductName(row.name);
            }

            set('initial_stock', '');
            set('initial_expiry_date', '');

            if (this.addFromCatalog) {
                const mrpNum = row.mrp != null && row.mrp !== '' ? parseFloat(row.mrp) : null;
                this.catalogPickSummary = {
                    name: row.name || '',
                    manufacturer: row.manufacturer || '',
                    composition: comp,
                    catalogId: row.id,
                    mrpLabel: mrpNum != null && !isNaN(mrpNum) ? mrpNum.toFixed(2) : '—',
                };
                console.log('[pharmacy] applyCatalogPick catalogPickSummary', this.catalogPickSummary);
            }

            this.catalogResults = [];
        }
    };
}
</script>
@endpush
@endsection
