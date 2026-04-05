<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\PharmacyDispensing;
use App\Models\PharmacyDispensingItem;
use App\Models\PharmacyItem;
use App\Models\PharmacyPurchase;
use App\Models\PharmacyStock;
use App\Models\PharmacySupplier;
use App\Models\MedicineCatalog;
use App\Models\AuditLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class PharmacyController extends Controller
{
    /**
     * Active pharmacy SKUs for the dispense dropdown (clinic-scoped).
     */
    private function medicinesForDispensing(int $clinicId)
    {
        if (!Schema::hasTable('pharmacy_items')) {
            Log::warning('PharmacyController@medicinesForDispensing: pharmacy_items missing');

            return collect();
        }
        $q = PharmacyItem::where('clinic_id', $clinicId)->orderBy('name');
        if (Schema::hasColumn('pharmacy_items', 'is_active')) {
            $q->where('is_active', true);
        }

        return $q->get();
    }

    // ── Dashboard ────────────────────────────────────────────────────────────

    public function index()
    {
        Log::info('PharmacyController@index', ['user' => auth()->id()]);

        $clinicId = auth()->user()->clinic_id;
        $stats = [
            'total_medicines' => 0, 'low_stock_count' => 0, 'dispensed_today' => 0,
            'monthly_revenue' => '0', 'itemsInStock' => 0, 'lowStockCount' => 0,
            'expiringSoon' => 0, 'todaysSalesAmount' => 0,
        ];
        $recentDispensing = collect();
        $lowStockItems = collect();

        try {
            $pharmacyReady = Schema::hasTable('pharmacy_items');
            $dispensingReady = Schema::hasTable('pharmacy_dispensing');
            $stockReady = Schema::hasTable('pharmacy_stock');

            $totalMedicines = $pharmacyReady ? PharmacyItem::where('clinic_id', $clinicId)->active()->count() : 0;
            $lowStockCount  = $pharmacyReady ? PharmacyItem::where('clinic_id', $clinicId)->active()->lowStock()->count() : 0;

            $dispensedToday = $dispensingReady
                ? PharmacyDispensing::where('clinic_id', $clinicId)->whereDate('created_at', today())->count()
                : 0;

            $monthlyRevenue = $dispensingReady
                ? PharmacyDispensing::where('clinic_id', $clinicId)
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->sum('total')
                : 0;

            $expiringSoon = $stockReady
                ? PharmacyStock::where('clinic_id', $clinicId)->where('quantity_available', '>', 0)->where('expiry_date', '>=', now()->toDateString())->where('expiry_date', '<=', now()->addDays(30)->toDateString())->count()
                : 0;

            $stats = [
                'total_medicines'   => $totalMedicines,
                'low_stock_count'   => $lowStockCount,
                'dispensed_today'   => $dispensedToday,
                'monthly_revenue'   => number_format($monthlyRevenue, 0),
                'itemsInStock'      => $totalMedicines,
                'lowStockCount'     => $lowStockCount,
                'expiringSoon'      => $expiringSoon,
                'todaysSalesAmount' => $dispensingReady ? PharmacyDispensing::where('clinic_id', $clinicId)->whereDate('created_at', today())->sum('total') : 0,
            ];

            if ($dispensingReady) {
                $recentDispensing = DB::table('pharmacy_dispensing')
                    ->leftJoin('patients', 'pharmacy_dispensing.patient_id', '=', 'patients.id')
                    ->where('pharmacy_dispensing.clinic_id', $clinicId)
                    ->select(
                        'pharmacy_dispensing.*',
                        'patients.name as patient_name',
                        DB::raw('(SELECT COUNT(*) FROM pharmacy_dispensing_items WHERE dispensing_id = pharmacy_dispensing.id) as items_count'),
                        'pharmacy_dispensing.total as total_amount'
                    )
                    ->orderByDesc('pharmacy_dispensing.created_at')
                    ->limit(20)
                    ->get();
            }

            if ($pharmacyReady) {
                $lowStockItems = PharmacyItem::where('clinic_id', $clinicId)
                    ->active()
                    ->lowStock()
                    ->limit(10)
                    ->get();
            }

            Log::info('PharmacyController@index loaded', ['stats' => $stats]);
        } catch (\Throwable $e) {
            Log::error('PharmacyController@index error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        }

        return view('pharmacy.index', compact('stats', 'recentDispensing', 'lowStockItems'));
    }

    // ── Pharmacist Portal ──────────────────────────────────────────────────

    public function pharmacistPortal()
    {
        Log::info('PharmacyController@pharmacistPortal', ['user' => auth()->id()]);
        $clinicId = auth()->user()->clinic_id;

        $pendingCount = 0;
        $dispensedToday = 0;
        $lowStockItems = collect();
        $nearExpiryItems = collect();
        $recentDispensing = collect();

        try {
            if (Schema::hasTable('pharmacy_dispensing')) {
                $pendingCount = PharmacyDispensing::where('clinic_id', $clinicId)
                    ->where('status', 'pending')
                    ->count();

                $dispensedToday = PharmacyDispensing::where('clinic_id', $clinicId)
                    ->whereDate('created_at', today())
                    ->where('status', 'dispensed')
                    ->count();

                $recentDispensing = PharmacyDispensing::where('clinic_id', $clinicId)
                    ->with(['patient', 'dispensedBy'])
                    ->orderByDesc('created_at')
                    ->take(10)
                    ->get();
            }

            if (Schema::hasTable('pharmacy_items')) {
                $lowStockItems = PharmacyItem::where('clinic_id', $clinicId)
                    ->active()
                    ->whereColumn('current_stock', '<=', 'reorder_level')
                    ->orderBy('current_stock')
                    ->take(10)
                    ->get();
            }

            if (Schema::hasTable('pharmacy_stock')) {
                $nearExpiryItems = PharmacyStock::where('clinic_id', $clinicId)
                    ->where('expiry_date', '<=', now()->addDays(30))
                    ->where('expiry_date', '>', now())
                    ->where('quantity', '>', 0)
                    ->with('pharmacyItem')
                    ->take(10)
                    ->get();
            }

            Log::info('PharmacyController@pharmacistPortal loaded', [
                'pending' => $pendingCount,
                'dispensed_today' => $dispensedToday,
            ]);
        } catch (\Throwable $e) {
            Log::error('PharmacyController@pharmacistPortal error', ['error' => $e->getMessage()]);
        }

        return view('pharmacy.pharmacist-portal', compact(
            'pendingCount', 'dispensedToday', 'lowStockItems', 'nearExpiryItems', 'recentDispensing'
        ));
    }

    // ── Inventory ────────────────────────────────────────────────────────────

    public function inventory(Request $request)
    {
        Log::info('PharmacyController@inventory', ['user' => auth()->id()]);

        $user = auth()->user();
        if (! $user) {
            return redirect()->route('login');
        }

        $clinicId = $user->clinic_id;

        if ($clinicId === null && ($user->role ?? '') !== 'super_admin') {
            Log::warning('PharmacyController@inventory: no clinic_id', ['user_id' => $user->id]);

            return redirect()->route('app.home')->with('error', 'No clinic is assigned to your account.');
        }

        $categories = ['Antibiotics', 'Analgesics', 'Antacids', 'Vitamins', 'Cardiac', 'Diabetic', 'Dermatology', 'Others'];

        $viewMode = $request->input('view', 'clinic');
        if (! in_array($viewMode, ['clinic', 'national'], true)) {
            $viewMode = 'clinic';
        }
        Log::info('PharmacyController@inventory viewMode', ['viewMode' => $viewMode, 'user_id' => $user->id]);

        // Must be defined for catch() compact() if an exception happens early.
        $catalogCount = 0;

        try {
            // max(id) proxy — COUNT(*) on huge medicine_catalog can time out on shared hosting.
            if (Schema::hasTable('medicine_catalog')) {
                try {
                    $catalogCount = (int) Cache::remember('inventory:medicine_catalog_count_v2', 86400, function () {
                        $max = DB::table('medicine_catalog')->max('id');

                        return $max !== null ? (int) $max : 0;
                    });
                } catch (\Throwable $e) {
                    Log::warning('PharmacyController@inventory catalog max(id) cache failed', ['error' => $e->getMessage()]);
                    try {
                        $max = DB::table('medicine_catalog')->max('id');
                        $catalogCount = $max !== null ? (int) $max : 0;
                    } catch (\Throwable $e2) {
                        Log::warning('PharmacyController@inventory medicine_catalog max(id) failed', ['error' => $e2->getMessage()]);
                    }
                }
            }

            // National imported catalog (browse / search) — not clinic stock rows.
            if ($viewMode === 'national') {
                if (! Schema::hasTable('medicine_catalog')) {
                    Log::error('PharmacyController@inventory: national view but medicine_catalog missing');
                    $items = new LengthAwarePaginator([], 0, 50, 1, ['path' => $request->url(), 'query' => $request->query()]);

                    return response(
                        view('pharmacy.inventory', compact('items', 'categories', 'catalogCount', 'viewMode'))
                            ->with('error', 'National medicine catalog is not installed. Import medicines or run migrations.')
                            ->render()
                    );
                }

                $nationalSearch = trim((string) $request->input('search', ''));
                $items = MedicineCatalog::queryForBrowse($nationalSearch)->paginate(50)->withQueryString();
                Log::info('PharmacyController@inventory national browse loaded', [
                    'total' => $items->total(),
                    'search' => $nationalSearch,
                    'current_page' => $items->currentPage(),
                ]);

                return response(
                    view('pharmacy.inventory', compact('items', 'categories', 'catalogCount', 'viewMode'))->render()
                );
            }

            if (! Schema::hasTable('pharmacy_items')) {
                Log::error('PharmacyController@inventory: pharmacy_items table missing');

                $items = new LengthAwarePaginator([], 0, 30, 1, ['path' => $request->url(), 'query' => $request->query()]);

                return response(
                    view('pharmacy.inventory', compact('items', 'categories', 'catalogCount', 'viewMode'))
                        ->with('error', 'Pharmacy tables are not installed on this server.')
                        ->render()
                );
            }

            // Do not eager-load stocks: grid uses current_stock (aggregate per row only).
            $query = PharmacyItem::query();
            if ($clinicId !== null) {
                $query->where('clinic_id', $clinicId);
            }
            $query->active();

            if ($request->filled('category')) {
                $cat = $request->category;
                if (Schema::hasColumn('pharmacy_items', 'category')) {
                    $query->where('category', $cat);
                } elseif (Schema::hasColumn('pharmacy_items', 'category_id') && is_numeric($cat)) {
                    $query->where('category_id', (int) $cat);
                }
                Log::info('PharmacyController@inventory category filter', ['category' => $cat]);
            }

            if (Schema::hasTable('pharmacy_stock')) {
                $stockSub = PharmacyItem::sqlTotalStockSubqueryForPharmacyItems();
                if ($request->input('stock_status') === 'low-stock') {
                    $query->lowStock();
                } elseif ($request->input('stock_status') === 'out-of-stock') {
                    $query->whereRaw("{$stockSub} <= 0");
                } elseif ($request->input('stock_status') === 'in-stock') {
                    $query->whereRaw("{$stockSub} > 0");
                }
            }

            if ($request->boolean('low_stock')) {
                $query->lowStock();
            }

            if ($request->boolean('expiring_soon')) {
                $query->whereHas('stocks', function ($q) {
                    PharmacyItem::constrainStockQueryToPositiveQty($q);
                    if (Schema::hasColumn('pharmacy_stock', 'expiry_date')) {
                        $q->where('expiry_date', '>=', now()->toDateString())
                          ->where('expiry_date', '<=', now()->addDays(90)->toDateString());
                    }
                });
            }
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                    if (Schema::hasColumn('pharmacy_items', 'generic_name')) {
                        $q->orWhere('generic_name', 'like', "%{$search}%");
                    }
                });
            }

            $orderCol = Schema::hasColumn('pharmacy_items', 'name') ? 'name' : 'id';
            $items = $query->orderBy($orderCol)->paginate(30)->withQueryString();

            Log::info('PharmacyController@inventory loaded', [
                'clinic_id' => $clinicId,
                'total' => $items->total(),
                'pharmacy_stock_has_quantity_available' => Schema::hasTable('pharmacy_stock')
                    && Schema::hasColumn('pharmacy_stock', 'quantity_available'),
            ]);

            // Render here: return view() defers Blade until after the controller returns, so try/catch
            // never saw layout/view exceptions — they surfaced as uncaught 500.
            return response(
                view('pharmacy.inventory', compact('items', 'categories', 'catalogCount', 'viewMode'))->render()
            );
        } catch (\Throwable $e) {
            Log::error('PharmacyController@inventory failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Do not re-render inventory here: if layout/blade failed, the same render would fail again.
            // url() avoids depending on named routes when route:cache is broken.
            return redirect()->to(url('/pharmacy'))->with(
                'error',
                'Could not load Pharmacy Inventory. Please try again. If this persists, contact support.'
            );
        }
    }

    // ── Add Item ─────────────────────────────────────────────────────────────

    /**
     * JSON autocomplete for national medicine_catalog (imported via indian-medicines:import).
     */
    public function searchMedicineCatalog(Request $request): JsonResponse
    {
        Log::info('PharmacyController@searchMedicineCatalog', [
            'user_id' => auth()->id(),
            'q' => $request->input('q'),
        ]);

        if (! Schema::hasTable('medicine_catalog')) {
            return response()->json(['results' => [], 'ok' => false, 'message' => 'medicine_catalog table missing']);
        }

        $q = trim((string) $request->input('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json(['results' => [], 'ok' => true]);
        }

        $limit = min(50, max(5, (int) $request->input('limit', 25)));
        $results = MedicineCatalog::searchProducts($q, $limit);

        return response()->json([
            'ok' => true,
            'results' => $results->map(fn ($r) => [
                'id' => $r->id,
                'name' => $r->name,
                'manufacturer' => $r->manufacturer,
                'composition' => $r->composition,
                'mrp' => $r->mrp,
            ]),
        ]);
    }

    public function addItem(Request $request)
    {
        if ($request->isMethod('GET')) {
            $catalogCount = 0;
            if (Schema::hasTable('medicine_catalog')) {
                try {
                    $catalogCount = (int) DB::table('medicine_catalog')->count();
                } catch (\Throwable $e) {
                    Log::warning('PharmacyController@addItem catalog count failed', ['error' => $e->getMessage()]);
                }
            }

            return view('pharmacy.add-item', compact('catalogCount'));
        }

        Log::info('PharmacyController@addItem incoming', [
            'keys' => array_keys($request->except(['_token'])),
            'has_price_per_unit' => $request->filled('price_per_unit'),
        ]);

        if ($request->filled('price_per_unit')) {
            $pu = $request->input('price_per_unit');
            if (!$request->filled('mrp')) {
                $request->merge(['mrp' => $pu]);
            }
            if (!$request->filled('selling_price')) {
                $request->merge(['selling_price' => $pu]);
            }
        }

        $rules = [
            'name'               => 'required|string|max:255',
            'generic_name'       => 'nullable|string|max:255',
            'category'           => 'nullable|string|max:100',
            'category_id'        => 'nullable|integer',
            'hsn_code'           => 'nullable|string|max:50',
            'unit'               => 'required|string|max:50',
            'pack_size'          => 'nullable|string|max:50',
            'manufacturer'       => 'nullable|string|max:255',
            'schedule'           => 'nullable|string|max:10',
            'is_controlled'      => 'boolean',
            'gst_rate'           => 'required|numeric|min:0|max:100',
            'mrp'                => 'required|numeric|min:0',
            'selling_price'      => 'required|numeric|min:0',
            'reorder_level'      => 'nullable|integer|min:0',
            'reorder_qty'        => 'nullable|integer|min:0',
            'storage_conditions' => 'nullable|string|max:255',
            'initial_stock'           => 'nullable|integer|min:0|max:999999',
            'initial_expiry_date'     => 'nullable|date|after_or_equal:today',
            'initial_batch_number'    => 'nullable|string|max:100',
            'initial_purchase_rate'   => 'nullable|numeric|min:0',
            'initial_mrp'             => 'nullable|numeric|min:0',
        ];
        if (Schema::hasColumn('pharmacy_items', 'medicine_catalog_id')) {
            $rules['medicine_catalog_id'] = 'nullable|integer|exists:medicine_catalog,id';
        }

        $validated = $request->validate($rules);

        $validated['clinic_id']    = auth()->user()->clinic_id;
        $validated['is_active']    = true;
        $validated['is_controlled'] = $request->boolean('is_controlled');

        if (Schema::hasColumn('pharmacy_items', 'category') && !empty($validated['category'])) {
            Log::info('PharmacyController@addItem persisting category string column', ['category' => $validated['category']]);
        } else {
            unset($validated['category']);
        }

        // Production DBs may have a narrow pharmacy_items row (see SQL dump): only name, unit, hmrp, gst_rate, etc.
        // Inserting unknown columns causes SQLSTATE 42S22 → 500. Only pass columns that exist.
        $allowedCols = array_flip(Schema::getColumnListing('pharmacy_items'));
        $row = array_intersect_key($validated, $allowedCols);
        if (!isset($allowedCols['selling_price']) && isset($allowedCols['mrp']) && isset($validated['selling_price'])) {
            $row['mrp'] = $validated['selling_price'];
            Log::info('PharmacyController@addItem: mapped selling_price → mrp (no selling_price column)');
        }
        if (isset($allowedCols['pack_size']) && empty($row['pack_size'])) {
            $row['pack_size'] = 1;
        }

        try {
            $item = PharmacyItem::create($row);
        } catch (\Throwable $e) {
            Log::error('PharmacyController@addItem create failed', [
                'error' => $e->getMessage(),
                'row_keys' => array_keys($row),
            ]);
            throw $e;
        }

        // Optional opening stock (same request) — avoids separate stock-in step for new SKUs
        $initialQty = (int) $request->input('initial_stock', 0);
        if ($initialQty > 0 && Schema::hasTable('pharmacy_stock')) {
            $expiryInput = $request->input('initial_expiry_date');
            $expiry = $expiryInput
                ? \Carbon\Carbon::parse($expiryInput)->toDateString()
                : now()->addYear()->toDateString();
            $batch = $request->input('initial_batch_number', 'OPENING-'.now()->format('Ymd'));
            $purchase = (float) ($request->input('initial_purchase_rate') ?? $row['mrp'] ?? $row['selling_price'] ?? 0);
            $mrpStock = (float) ($request->input('initial_mrp') ?? $purchase);
            try {
                $stockRow = [
                    'clinic_id'           => $validated['clinic_id'],
                    'item_id'             => $item->id,
                    'batch_number'        => $batch,
                    'expiry_date'         => $expiry,
                    'quantity_in'         => $initialQty,
                    'quantity_out'        => 0,
                    'quantity_available'  => $initialQty,
                    'purchase_rate'       => $purchase,
                    'mrp'                 => $mrpStock,
                    'supplier_id'         => null,
                    'grn_id'              => null,
                ];
                if (Schema::hasColumn('pharmacy_stock', 'purchase_price')) {
                    $stockRow['purchase_price'] = $purchase;
                }
                if (Schema::hasColumn('pharmacy_stock', 'selling_price')) {
                    $stockRow['selling_price'] = $mrpStock;
                }
                $stockRow = array_intersect_key($stockRow, array_flip(Schema::getColumnListing('pharmacy_stock')));
                PharmacyStock::create($stockRow);
                Log::info('PharmacyController@addItem initial stock batch created', [
                    'item_id' => $item->id,
                    'qty'     => $initialQty,
                ]);
            } catch (\Throwable $e) {
                Log::error('PharmacyController@addItem initial stock failed', ['error' => $e->getMessage()]);
            }
        }

        Log::info('PharmacyController@addItem created', ['item_id' => $item->id]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'item' => $item], 201);
        }

        return redirect()->route('pharmacy.inventory')
            ->with('success', "Drug \"{$item->name}\" added to inventory.");
    }

    // ── Stock In ─────────────────────────────────────────────────────────────

    public function stockIn(Request $request)
    {
        Log::info('PharmacyController@stockIn incoming', ['keys' => array_keys($request->except(['_token']))]);

        if ($request->filled('pharmacy_item_id') && ! $request->filled('item_id')) {
            $request->merge(['item_id' => $request->input('pharmacy_item_id')]);
        }
        if ($request->filled('quantity') && ! $request->filled('quantity_in')) {
            $request->merge(['quantity_in' => (int) $request->input('quantity')]);
        }
        if ($request->filled('purchase_price') && ! $request->filled('purchase_rate')) {
            $request->merge(['purchase_rate' => $request->input('purchase_price')]);
        }
        // MRP defaults to purchase rate (filled() is false for "0", so use has() + merge)
        if (! $request->has('mrp') || $request->input('mrp') === null || $request->input('mrp') === '') {
            if ($request->has('purchase_rate') && $request->input('purchase_rate') !== null && $request->input('purchase_rate') !== '') {
                $request->merge(['mrp' => $request->input('purchase_rate')]);
            }
        }

        $clinicId = auth()->user()->clinic_id;

        $validated = $request->validate([
            'item_id'       => [
                'required',
                'integer',
                Rule::exists('pharmacy_items', 'id')->where(fn ($q) => $q->where('clinic_id', $clinicId)),
            ],
            'batch_number'  => 'required|string|max:100',
            'expiry_date'   => 'required|date|after_or_equal:today',
            'quantity_in'   => 'required|integer|min:1',
            'purchase_rate' => 'required|numeric|min:0',
            'mrp'           => 'required|numeric|min:0',
            'supplier_id'   => 'nullable|integer',
            'grn_id'        => 'nullable|integer',
        ]);

        $validated['clinic_id'] = $clinicId;
        $validated['quantity_out'] = 0;
        $validated['quantity_available'] = $validated['quantity_in'];

        if (Schema::hasColumn('pharmacy_stock', 'purchase_price')) {
            $validated['purchase_price'] = $validated['purchase_rate'];
        }
        if (Schema::hasColumn('pharmacy_stock', 'selling_price')) {
            $validated['selling_price'] = $validated['mrp'];
        }

        $stockCols = Schema::getColumnListing('pharmacy_stock');
        $stockColSet = array_flip($stockCols);

        // Legacy / narrow dumps: single "quantity" column instead of quantity_in / quantity_out
        if (! isset($stockColSet['quantity_in']) && isset($stockColSet['quantity'])) {
            $validated['quantity'] = $validated['quantity_in'];
            unset($validated['quantity_in'], $validated['quantity_out']);
            Log::info('PharmacyController@stockIn mapping quantity_in → quantity (legacy schema)');
        }

        // Drop fields not present on this database
        $validated = array_intersect_key($validated, array_flip($stockCols));

        Log::info('PharmacyController@stockIn insert keys', ['keys' => array_keys($validated)]);

        try {
            $stock = PharmacyStock::create($validated);
        } catch (\Throwable $e) {
            Log::error('PharmacyController@stockIn create failed', [
                'error' => $e->getMessage(),
                'keys' => array_keys($validated),
            ]);

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Could not save stock: '.$e->getMessage()], 500);
            }

            return redirect()->route('pharmacy.inventory')
                ->with('error', 'Could not add stock. If this persists, ask support to check pharmacy_stock columns match the app (quantity_in, purchase_rate, mrp). '.$e->getMessage());
        }

        Log::info('PharmacyController@stockIn created', ['stock_id' => $stock->id]);

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Stock received successfully.',
                'stock'   => $stock->fresh()->only(['id', 'item_id', 'batch_number', 'expiry_date', 'quantity_available', 'purchase_rate', 'mrp']),
            ]);
        }

        return redirect()->route('pharmacy.inventory')
            ->with('success', 'Stock added for batch '.$stock->batch_number.'.');
    }

    // ── Dispensing Form (GET alias) ───────────────────────────────────────────

    public function dispensingForm(Request $request)
    {
        $clinicId = auth()->user()->clinic_id;
        $preselectPatientId = $request->integer('patient_id') ?: null;
        $prefillAdmissionId = $request->integer('admission_id') ?: null;
        Log::info('PharmacyController@dispensingForm', [
            'clinic_id' => $clinicId,
            'patient_id' => $preselectPatientId,
            'admission_id' => $prefillAdmissionId,
        ]);
        $patients = Patient::where('clinic_id', $clinicId)
            ->orderBy('name')
            ->get(['id', 'name', 'phone', 'age_years', 'sex']);
        $medicines = $this->medicinesForDispensing($clinicId);
        Log::info('PharmacyController@dispensingForm medicines', ['count' => $medicines->count()]);

        return view('pharmacy.dispensing', compact('patients', 'medicines', 'preselectPatientId', 'prefillAdmissionId'));
    }

    // ── Dispense ─────────────────────────────────────────────────────────────

    public function dispense(Request $request)
    {
        if ($request->isMethod('GET')) {
            $clinicId = auth()->user()->clinic_id;
            $preselectPatientId = $request->integer('patient_id') ?: null;
            $prefillAdmissionId = $request->integer('admission_id') ?: null;
            $patients = Patient::where('clinic_id', $clinicId)
                ->orderBy('name')
                ->get(['id', 'name', 'phone', 'age_years', 'sex']);
            $medicines = $this->medicinesForDispensing($clinicId);

            return view('pharmacy.dispensing', compact('patients', 'medicines', 'preselectPatientId', 'prefillAdmissionId'));
        }

        // POST — process dispensing
        $validated = $request->validate([
            'patient_id'       => 'nullable|integer|exists:patients,id',
            'payment_mode'     => 'required|in:cash,card,upi,credit',
            'discount'         => 'nullable|numeric|min:0',
            'notes'            => 'nullable|string|max:500',
            'items'            => 'required|array|min:1',
            'items.*.item_id'  => 'required|integer|exists:pharmacy_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.instructions' => 'nullable|string|max:255',
        ]);

        $clinicId = auth()->user()->clinic_id;
        $discount = (float) ($validated['discount'] ?? 0);

        DB::beginTransaction();
        try {
            $subtotal  = 0;
            $gstTotal  = 0;
            $lineItems = [];

            foreach ($validated['items'] as $line) {
                $item     = PharmacyItem::findOrFail($line['item_id']);
                $needed   = (int) $line['quantity'];

                // FIFO: oldest expiry first, non-expired batches only
                $batches = PharmacyStock::where('clinic_id', $clinicId)
                    ->where('item_id', $item->id)
                    ->where('quantity_available', '>', 0)
                    ->nonExpired()
                    ->orderBy('expiry_date')
                    ->get();

                $remaining = $needed;
                $usedBatches = [];

                foreach ($batches as $batch) {
                    if ($remaining <= 0) {
                        break;
                    }
                    $take = min($batch->quantity_available, $remaining);
                    $usedBatches[] = ['batch' => $batch, 'qty' => $take];
                    $remaining -= $take;
                }

                if ($remaining > 0) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Insufficient stock for \"{$item->name}\". Only " . ($needed - $remaining) . ' units available.',
                    ], 422);
                }

                // Deduct stock and build line items
                $firstBatch = $usedBatches[0]['batch'];
                foreach ($usedBatches as $entry) {
                    /** @var PharmacyStock $batch */
                    $batch = $entry['batch'];
                    $take  = $entry['qty'];

                    $batch->quantity_out       += $take;
                    $batch->quantity_available -= $take;
                    $batch->save();
                }

                $unitPrice  = (float) $item->selling_price;
                $gstRate    = (float) $item->gst_rate;
                $lineTotal  = round($unitPrice * $needed, 2);
                $lineGst    = round($lineTotal * $gstRate / 100, 2);

                $subtotal += $lineTotal;
                $gstTotal += $lineGst;

                $lineTotalInclGst = round($lineTotal + $lineGst, 2);
                $lineItems[] = [
                    'item_id'        => $item->id,
                    'stock_id'       => $firstBatch->id,
                    'batch_number'   => $firstBatch->batch_number,
                    'expiry_date'    => $firstBatch->expiry_date,
                    'quantity'       => $needed,
                    'unit_price'     => $unitPrice,
                    'selling_price'  => $unitPrice,
                    'gst_rate'       => $gstRate,
                    'gst_amount'     => $lineGst,
                    'total'          => $lineTotalInclGst,
                    'total_price'    => $lineTotalInclGst,
                    'instructions'   => $line['instructions'] ?? null,
                ];
            }

            $gross = round($subtotal + $gstTotal, 2);
            $total = round($gross - $discount, 2);

            $dispensingNumber = 'RX-' . strtoupper(uniqid());

            $dispensingHeader = $this->buildPharmacyDispensingRow(
                $clinicId,
                $validated,
                $dispensingNumber,
                $gross,
                $gstTotal,
                $discount,
                $total
            );
            Log::info('PharmacyController@dispense header keys', ['keys' => array_keys($dispensingHeader)]);

            $dispensing = PharmacyDispensing::create($dispensingHeader);

            $itemCols = array_flip(Schema::getColumnListing('pharmacy_dispensing_items'));
            foreach ($lineItems as $li) {
                $li['dispensing_id'] = $dispensing->id;
                $lineRow = array_intersect_key($li, $itemCols);
                Log::info('PharmacyController@dispense line', ['keys' => array_keys($lineRow)]);
                PharmacyDispensingItem::create($lineRow);
            }

            DB::commit();

            Log::info('PharmacyController@dispense created', [
                'dispensing_id'     => $dispensing->id,
                'dispensing_number' => $dispensingNumber,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success'           => true,
                    'dispensing_number' => $dispensingNumber,
                    'total'             => $total,
                ]);
            }

            return redirect()
                ->route('pharmacy.dispense.form')
                ->with('success', "Dispensed successfully — {$dispensingNumber} (₹{$total})");
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('PharmacyController@dispense error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $msg = config('app.debug') ? $e->getMessage() : 'Dispensing failed. Please try again.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 500);
            }

            return back()->withInput()->with('error', $msg);
        }
    }

    /**
     * Map controller fields to actual pharmacy_dispensing columns (schemas differ by migration).
     */
    private function buildPharmacyDispensingRow(
        int $clinicId,
        array $validated,
        string $dispensingNumber,
        float $grossBeforeDiscount,
        float $gstTotal,
        float $discount,
        float $netTotal
    ): array {
        $cols = array_flip(Schema::getColumnListing('pharmacy_dispensing'));
        $candidates = [
            'clinic_id'           => $clinicId,
            'patient_id'          => $validated['patient_id'] ?? null,
            'dispensing_number'   => $dispensingNumber,
            'dispensed_by'        => auth()->id(),
            'dispensed_at'        => now(),
            'payment_mode'        => $validated['payment_mode'],
            'notes'               => $validated['notes'] ?? null,
            'total_amount'        => $grossBeforeDiscount,
            'discount_amount'     => $discount,
            'paid_amount'         => $netTotal,
            'gst_amount'          => $gstTotal,
            'total'               => $netTotal,
        ];
        if (isset($cols['created_at'])) {
            $candidates['created_at'] = now();
        }
        if (isset($cols['updated_at'])) {
            $candidates['updated_at'] = now();
        }

        return array_intersect_key($candidates, $cols);
    }

    // ── Dispensing History ────────────────────────────────────────────────────

    public function dispensingHistory(Request $request)
    {
        Log::info('PharmacyController@dispensingHistory', ['user' => auth()->id()]);

        $clinicId = auth()->user()->clinic_id;

        $query = PharmacyDispensing::with(['patient', 'items.item', 'dispensedBy'])
            ->where('clinic_id', $clinicId)
            ->latest();

        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('dispensing_number', 'like', "%{$search}%")
                  ->orWhereHas('patient', function ($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $dispensings = $query->paginate(30)->withQueryString();

        return view('pharmacy.dispensing-history', compact('dispensings'));
    }

    // ── Stock Report ──────────────────────────────────────────────────────────

    public function stockReport()
    {
        Log::info('PharmacyController@stockReport', ['user' => auth()->id()]);

        $clinicId = auth()->user()->clinic_id;

        $items = PharmacyItem::with(['stocks' => function ($q) {
            $q->orderBy('expiry_date');
        }])
        ->where('clinic_id', $clinicId)
        ->active()
        ->orderBy('name')
        ->get();

        $today       = now()->toDateString();
        $soon        = now()->addDays(90)->toDateString();

        return view('pharmacy.stock-report', compact('items', 'today', 'soon'));
    }

    // ── Expiry Alert (JSON for dashboard widget) ──────────────────────────────

    public function expiryAlert()
    {
        $clinicId = auth()->user()->clinic_id;

        $batches = PharmacyStock::with('item:id,name,generic_name')
            ->where('clinic_id', $clinicId)
            ->where('quantity_available', '>', 0)
            ->where('expiry_date', '>=', now()->toDateString())
            ->where('expiry_date', '<=', now()->addDays(60)->toDateString())
            ->orderBy('expiry_date')
            ->get(['id', 'item_id', 'batch_number', 'expiry_date', 'quantity_available']);

        return response()->json([
            'count'   => $batches->count(),
            'batches' => $batches,
        ]);
    }

    // ── Drug Search (JSON for dispensing autocomplete) ────────────────────────

    public function searchDrugs(Request $request)
    {
        $clinicId = auth()->user()->clinic_id;
        $search   = $request->get('q', '');

        $items = PharmacyItem::where('clinic_id', $clinicId)
            ->active()
            ->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('generic_name', 'like', "%{$search}%");
            })
            ->limit(15)
            ->get(['id', 'name', 'generic_name', 'selling_price', 'gst_rate', 'unit', 'schedule']);

        // Attach live stock qty
        $items->each(function ($item) {
            $item->stock_quantity = $item->stock_quantity;
        });

        return response()->json($items);
    }

    // ── Patient Search (JSON for dispensing autocomplete) ─────────────────────

    public function searchPatients(Request $request)
    {
        $clinicId = auth()->user()->clinic_id;
        $search   = $request->get('q', '');

        $patients = Patient::where('clinic_id', $clinicId)
            ->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'phone', 'age_years', 'sex']);

        return response()->json($patients);
    }

    // ── Purchase orders / GRN (Phase E) ───────────────────────────────────────

    public function purchasesIndex(Request $request): View
    {
        Log::info('PharmacyController@purchasesIndex', ['user' => auth()->id(), 'q' => $request->get('q')]);

        $clinicId = auth()->user()->clinic_id;
        $purchases = collect();

        if (Schema::hasTable('pharmacy_purchases')) {
            $q = PharmacyPurchase::query()
                ->where('clinic_id', $clinicId)
                ->with(['supplier', 'receivedByUser'])
                ->orderByDesc('created_at');

            if ($request->filled('q')) {
                $term = $request->q;
                $q->where(function ($w) use ($term) {
                    $w->where('purchase_number', 'like', "%{$term}%")
                        ->orWhere('invoice_number', 'like', "%{$term}%");
                });
                Log::info('PharmacyController@purchasesIndex search', ['term' => $term]);
            }

            $purchases = $q->paginate(25)->withQueryString();
        } else {
            Log::warning('PharmacyController@purchasesIndex: pharmacy_purchases missing');
        }

        return view('pharmacy.purchases-index', compact('purchases'));
    }

    public function purchaseCreate(): View
    {
        Log::info('PharmacyController@purchaseCreate', ['user' => auth()->id()]);

        $clinicId = auth()->user()->clinic_id;
        $suppliers = collect();
        $items = collect();

        if (Schema::hasTable('pharmacy_suppliers')) {
            $suppliers = PharmacySupplier::where('clinic_id', $clinicId)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }
        if (Schema::hasTable('pharmacy_items')) {
            $items = PharmacyItem::where('clinic_id', $clinicId)->active()->orderBy('name')->get();
        }

        Log::info('PharmacyController@purchaseCreate counts', [
            'suppliers' => $suppliers->count(),
            'items'     => $items->count(),
        ]);

        return view('pharmacy.purchase-create', compact('suppliers', 'items'));
    }

    public function purchaseStore(Request $request): RedirectResponse
    {
        Log::info('PharmacyController@purchaseStore keys', ['keys' => array_keys($request->except(['_token']))]);

        if (! Schema::hasTable('pharmacy_purchases') || ! Schema::hasTable('pharmacy_purchase_items')) {
            Log::error('PharmacyController@purchaseStore: tables missing');

            return redirect()->route('pharmacy.index')->with('error', 'Purchase tables are not installed.');
        }

        $validated = $request->validate([
            'supplier_id'    => ['nullable', 'integer', 'exists:pharmacy_suppliers,id'],
            'invoice_number' => ['nullable', 'string', 'max:120'],
            'invoice_date'   => ['nullable', 'date'],
            'received_date'  => ['required', 'date'],
            'notes'          => ['nullable', 'string', 'max:2000'],
            'items'          => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'integer', 'exists:pharmacy_items,id'],
            'items.*.batch_number' => ['required', 'string', 'max:100'],
            'items.*.expiry_date'  => ['required', 'date'],
            'items.*.quantity'     => ['required', 'integer', 'min:1'],
            'items.*.free_quantity' => ['nullable', 'integer', 'min:0'],
            'items.*.purchase_rate' => ['required', 'numeric', 'min:0'],
            'items.*.mrp'           => ['required', 'numeric', 'min:0'],
            'items.*.discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'items.*.gst_rate'      => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $clinicId = auth()->user()->clinic_id;

        if (! empty($validated['supplier_id'])) {
            PharmacySupplier::where('clinic_id', $clinicId)->where('id', $validated['supplier_id'])->firstOrFail();
            Log::info('PharmacyController@purchaseStore supplier ok', ['supplier_id' => $validated['supplier_id']]);
        }

        DB::beginTransaction();
        try {
            $sumTaxable = 0.0;
            $sumGst = 0.0;
            $sumDiscount = 0.0;
            $linePayloads = [];

            foreach ($validated['items'] as $idx => $line) {
                $item = PharmacyItem::where('clinic_id', $clinicId)->where('id', $line['item_id'])->firstOrFail();
                Log::info('PharmacyController@purchaseStore line', ['idx' => $idx, 'item_id' => $item->id, 'name' => $item->name]);

                $qty = (int) $line['quantity'];
                $rate = (float) $line['purchase_rate'];
                $disc = (float) ($line['discount_percent'] ?? 0);
                $gstRate = (float) ($line['gst_rate'] ?? $item->gst_rate ?? 12);

                $gross = $qty * $rate;
                $discVal = $gross * ($disc / 100);
                $taxable = $gross - $discVal;
                $gstAmt = $taxable * ($gstRate / 100);
                $lineNet = round($taxable + $gstAmt, 2);

                $sumTaxable += $taxable;
                $sumGst += $gstAmt;
                $sumDiscount += $discVal;

                $linePayloads[] = [
                    'item' => $item,
                    'line' => $line,
                    'line_net' => $lineNet,
                    'gst_amt' => $gstAmt,
                    'taxable' => $taxable,
                ];
            }

            $purchaseNumber = 'PO-'.$clinicId.'-'.now()->format('YmdHis').'-'.substr(sha1((string) microtime(true)), 0, 6);
            Log::info('PharmacyController@purchaseStore header totals', [
                'sumTaxable' => $sumTaxable,
                'sumGst' => $sumGst,
                'sumDiscount' => $sumDiscount,
                'purchase_number' => $purchaseNumber,
            ]);

            $header = [
                'clinic_id'        => $clinicId,
                'supplier_id'      => $validated['supplier_id'] ?? null,
                'purchase_number'  => $purchaseNumber,
                'invoice_number'   => $validated['invoice_number'] ?? null,
                'invoice_date'     => $validated['invoice_date'] ?? null,
                'received_by'      => auth()->id(),
                'received_date'    => $validated['received_date'],
                'total_amount'     => round($sumTaxable, 2),
                'discount_amount'  => round($sumDiscount, 2),
                'gst_amount'       => round($sumGst, 2),
                'net_amount'       => round($sumTaxable + $sumGst, 2),
                'payment_status'   => 'pending',
                'notes'            => $validated['notes'] ?? null,
            ];
            $header = array_intersect_key($header, array_flip(Schema::getColumnListing('pharmacy_purchases')));
            $purchase = PharmacyPurchase::create($header);
            Log::info('PharmacyController@purchaseStore purchase row', ['id' => $purchase->id]);

            $piCols = Schema::getColumnListing('pharmacy_purchase_items');

            foreach ($linePayloads as $pack) {
                $line = $pack['line'];
                $item = $pack['item'];
                $qty = (int) $line['quantity'];
                $freeQty = (int) ($line['free_quantity'] ?? 0);
                $rate = (float) $line['purchase_rate'];
                $mrp = (float) $line['mrp'];
                $disc = (float) ($line['discount_percent'] ?? 0);
                $gstRate = (float) ($line['gst_rate'] ?? $item->gst_rate ?? 12);

                $row = [
                    'purchase_id'       => $purchase->id,
                    'item_id'           => $item->id,
                    'batch_number'      => $line['batch_number'],
                    'expiry_date'       => $line['expiry_date'],
                    'quantity'          => $qty,
                    'free_quantity'     => $freeQty,
                    'purchase_rate'     => $rate,
                    'mrp'               => $mrp,
                    'discount_percent'  => $disc,
                    'gst_rate'          => $gstRate,
                    'net_amount'        => $pack['line_net'],
                ];
                $row = array_intersect_key($row, array_flip($piCols));
                DB::table('pharmacy_purchase_items')->insert(array_merge($row, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
                Log::info('PharmacyController@purchaseStore purchase_item', ['item_id' => $item->id, 'batch' => $line['batch_number']]);

                if (Schema::hasTable('pharmacy_stock')) {
                    $stockRow = [
                        'clinic_id'          => $clinicId,
                        'item_id'            => $item->id,
                        'batch_number'       => $line['batch_number'],
                        'expiry_date'        => $line['expiry_date'],
                        'quantity_in'        => $qty + $freeQty,
                        'quantity_out'       => 0,
                        'quantity_available' => $qty + $freeQty,
                        'purchase_rate'      => $rate,
                        'mrp'                => $mrp,
                        'supplier_id'        => $validated['supplier_id'] ?? null,
                        'grn_id'             => $purchase->id,
                    ];
                    if (Schema::hasColumn('pharmacy_stock', 'purchase_price')) {
                        $stockRow['purchase_price'] = $rate;
                    }
                    if (Schema::hasColumn('pharmacy_stock', 'selling_price')) {
                        $stockRow['selling_price'] = $mrp;
                    }
                    $stockRow = array_intersect_key($stockRow, array_flip(Schema::getColumnListing('pharmacy_stock')));
                    PharmacyStock::create($stockRow);
                    Log::info('PharmacyController@purchaseStore stock batch', ['item_id' => $item->id, 'grn_id' => $purchase->id]);
                }
            }

            DB::commit();
            Log::info('PharmacyController@purchaseStore committed', ['purchase_id' => $purchase->id]);

            return redirect()
                ->route('pharmacy.purchases.index')
                ->with('success', 'Purchase / GRN recorded — '.$purchase->purchase_number.'.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('PharmacyController@purchaseStore rollback', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return back()->withInput()->with('error', 'Could not save purchase: '.$e->getMessage());
        }
    }

    // ── Expiry alerts (batches nearing expiry) ───────────────────────────────

    public function expiryAlerts(Request $request): View
    {
        $clinicId = auth()->user()->clinic_id;
        $days = min(365, max(1, (int) $request->input('days', 90)));
        Log::info('PharmacyController@expiryAlerts', ['clinic_id' => $clinicId, 'days' => $days, 'query' => $request->all()]);

        $rows = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 40);

        try {
            if (Schema::hasTable('pharmacy_stock')) {
                $rows = PharmacyStock::query()
                    ->where('clinic_id', $clinicId)
                    ->where('quantity_available', '>', 0)
                    ->whereNotNull('expiry_date')
                    ->where('expiry_date', '<=', now()->addDays($days)->toDateString())
                    ->with('item')
                    ->orderBy('expiry_date')
                    ->paginate(40)
                    ->withQueryString();
                Log::info('PharmacyController@expiryAlerts loaded', ['total' => $rows->total()]);
            }
        } catch (\Throwable $e) {
            Log::error('PharmacyController@expiryAlerts error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        }

        return view('pharmacy.expiry-alerts', compact('rows', 'days'));
    }

    // ── Returns / stock adjustment (reduce batch qty) ────────────────────────

    public function returnsForm(): View
    {
        $clinicId = auth()->user()->clinic_id;
        Log::info('PharmacyController@returnsForm', ['clinic_id' => $clinicId]);

        $batches = collect();
        try {
            if (Schema::hasTable('pharmacy_stock')) {
                $batches = PharmacyStock::query()
                    ->where('clinic_id', $clinicId)
                    ->where('quantity_available', '>', 0)
                    ->with('item')
                    ->orderBy('expiry_date')
                    ->get();
                Log::info('PharmacyController@returnsForm batches', ['count' => $batches->count()]);
            }
        } catch (\Throwable $e) {
            Log::error('PharmacyController@returnsForm error', ['error' => $e->getMessage()]);
        }

        return view('pharmacy.returns', compact('batches'));
    }

    public function storeReturn(Request $request): RedirectResponse
    {
        $clinicId = auth()->user()->clinic_id;
        Log::info('PharmacyController@storeReturn incoming', ['keys' => array_keys($request->except(['_token']))]);

        $validated = $request->validate([
            'stock_id' => 'required|integer|exists:pharmacy_stock,id',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|in:return,damage,expired,adjustment,theft,other',
            'notes' => 'nullable|string|max:500',
        ]);

        if (!Schema::hasTable('pharmacy_stock')) {
            return redirect()->route('pharmacy.returns.form')->with('error', 'Stock module is not available.');
        }

        $stock = PharmacyStock::where('id', $validated['stock_id'])->where('clinic_id', $clinicId)->first();
        if (!$stock) {
            Log::warning('PharmacyController@storeReturn wrong clinic or missing', ['stock_id' => $validated['stock_id']]);

            return back()->withInput()->with('error', 'Batch not found for this clinic.');
        }

        $qty = (int) $validated['quantity'];
        if ($stock->quantity_available < $qty) {
            Log::warning('PharmacyController@storeReturn insufficient qty', [
                'stock_id' => $stock->id,
                'requested' => $qty,
                'available' => $stock->quantity_available,
            ]);

            return back()->withInput()->with('error', 'Insufficient quantity on this batch (available: '.$stock->quantity_available.').');
        }

        $beforeAvail = (int) $stock->quantity_available;

        try {
            DB::transaction(function () use ($stock, $qty, $validated, $beforeAvail) {
                $stock->quantity_out = (int) $stock->quantity_out + $qty;
                $stock->quantity_available = $beforeAvail - $qty;
                $stock->save();
                Log::info('PharmacyController@storeReturn stock updated', [
                    'stock_id' => $stock->id,
                    'quantity_available' => $stock->quantity_available,
                    'reason' => $validated['reason'],
                ]);

                AuditLog::log(
                    'pharmacy_stock_return',
                    'Removed '.$qty.' units from batch '.($stock->batch_number ?? '#'.$stock->id).' ('.$validated['reason'].')',
                    'pharmacy_stock',
                    (int) $stock->id,
                    ['quantity_available' => $beforeAvail],
                    [
                        'quantity_available' => $stock->quantity_available,
                        'quantity_out' => $stock->quantity_out,
                        'reason' => $validated['reason'],
                        'notes' => $validated['notes'] ?? null,
                    ]
                );
            });
        } catch (\Throwable $e) {
            Log::error('PharmacyController@storeReturn failed', ['error' => $e->getMessage()]);

            return back()->withInput()->with('error', 'Could not record adjustment: '.$e->getMessage());
        }

        return redirect()->route('pharmacy.returns.form')->with('success', 'Stock adjustment recorded.');
    }

    /**
     * List pharmacy suppliers and quick-add form (GRN / purchase workflow).
     */
    public function suppliersIndex(): View
    {
        Log::info('PharmacyController@suppliersIndex', ['user' => auth()->id()]);

        $clinicId = auth()->user()->clinic_id;
        $suppliers = collect();
        if (Schema::hasTable('pharmacy_suppliers')) {
            $suppliers = PharmacySupplier::where('clinic_id', $clinicId)
                ->orderBy('name')
                ->get();
        } else {
            Log::warning('PharmacyController@suppliersIndex: pharmacy_suppliers missing');
        }

        Log::info('PharmacyController@suppliersIndex loaded', ['count' => $suppliers->count()]);

        return view('pharmacy.suppliers', compact('suppliers'));
    }

    public function storeSupplier(Request $request): RedirectResponse
    {
        Log::info('PharmacyController@storeSupplier', ['keys' => array_keys($request->except(['_token']))]);

        if (! Schema::hasTable('pharmacy_suppliers')) {
            Log::error('PharmacyController@storeSupplier: table missing');

            return redirect()->route('pharmacy.index')->with('error', 'Suppliers are not available (tables missing).');
        }

        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:200'],
            'phone'          => ['nullable', 'string', 'max:50'],
            'email'          => ['nullable', 'email', 'max:150'],
            'address'        => ['nullable', 'string', 'max:2000'],
            'gst_number'     => ['nullable', 'string', 'max:50'],
            'drug_license'   => ['nullable', 'string', 'max:100'],
            'payment_terms'  => ['nullable', 'string', 'max:500'],
        ]);

        $clinicId = auth()->user()->clinic_id;

        PharmacySupplier::create(array_merge($validated, [
            'clinic_id' => $clinicId,
            'is_active' => true,
        ]));

        Log::info('PharmacyController@storeSupplier created', ['name' => $validated['name'], 'clinic_id' => $clinicId]);

        return redirect()->route('pharmacy.suppliers.index')->with('success', 'Supplier added.');
    }
}
