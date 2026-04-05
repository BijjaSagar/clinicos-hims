{{-- National medicine DB vs clinic SKUs; stock can be updated later. Pass $context: inventory|dispense --}}
@php
    $context = $context ?? 'inventory';
@endphp
<div class="rounded-xl border px-4 py-3 sm:px-5 sm:py-4 text-sm leading-relaxed shadow-sm" style="background:#eff6ff;border-color:#93c5fd;color:#1e3a8a;">
    <p class="font-semibold flex items-start gap-2">
        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>
            @if($context === 'dispense')
                Dispense Medicine: clinic stock only
            @else
                Pharmacy Inventory: national database and stock updates
            @endif
        </span>
    </p>
    <div class="mt-2 space-y-2 text-sm opacity-[0.98] pl-0 sm:pl-7">
        @if($context === 'dispense')
            <p>
                The medicine dropdown lists <strong>medicines already added</strong> to your pharmacy (your shelf inventory). It does not search the full national database.
            </p>
            <p>
                To search the <strong>national medicine database</strong> and add a new product, open
                <a href="{{ url('/pharmacy/inventory?add_medicine=1') }}" class="font-semibold underline decoration-blue-400 hover:text-blue-950">Pharmacy, then Inventory, then Add Medicine</a>.
                <strong>Opening stock can be zero</strong> — you can add batches and <strong>update stock later</strong> from Inventory (row <strong>+</strong> Add Stock or Record GRN).
            </p>
        @else
            <p>
                Open the <strong>National database</strong> tab to browse the full imported product list (paginated). <strong>My clinic inventory</strong> shows only medicines you have added as shelf SKUs with stock.
            </p>
            <p>
                <strong>Add Medicine</strong> searches the same national catalog so you can link a product by name. Use <strong>Add to clinic</strong> on a national row or <strong>+ Add Medicine</strong> to create a clinic item.
            </p>
            <p>
                <strong>Opening stock is optional.</strong> Add or adjust batches anytime with <strong>+</strong> on a row (Add Stock) or
                <a href="{{ url('/pharmacy/purchases/create') }}" class="font-semibold underline decoration-blue-400 hover:text-blue-950">Record GRN</a>.
                Staff can <strong>update quantities later</strong>; you do not need perfect stock on day one.
            </p>
        @endif
    </div>
</div>
