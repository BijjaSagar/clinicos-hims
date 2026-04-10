<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\LabTestCatalog;
use App\Models\Patient;
use App\Models\LabOrder;
use App\Services\EnsureLabCatalogDefaults;
use App\Services\LabSampleCollectionService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class LabController extends Controller
{
    public function dashboard(Request $request)
    {
        $clinicId = auth()->user()->clinic_id;
        Log::info('LabController@dashboard', ['clinic_id' => $clinicId]);

        $stats = [
            'total_tests' => 0, 'active_tests' => 0,
            'pending_results' => 0, 'orders_today' => 0, 'results_received' => 0,
        ];

        $recentOrders = collect();
        $patients = collect();

        try {
            $catalogReady = Schema::hasTable('lab_tests_catalog');
            $ordersReady = Schema::hasTable('lab_orders');

            if ($catalogReady) {
                $stats['total_tests'] = LabTestCatalog::where('clinic_id', $clinicId)->count();
                $stats['active_tests'] = LabTestCatalog::where('clinic_id', $clinicId)->where('is_active', true)->count();
            }

            if ($ordersReady) {
                $stats['pending_results'] = DB::table('lab_orders')->where('clinic_id', $clinicId)->whereIn('status', ['pending', 'sample_collected', 'processing'])->count();
                $stats['orders_today'] = DB::table('lab_orders')->where('clinic_id', $clinicId)->whereDate('created_at', today())->count();
                $stats['results_received'] = DB::table('lab_orders')->where('clinic_id', $clinicId)->where('status', 'completed')->whereMonth('created_at', now()->month)->count();

                $search = $request->query('search');
                
                // Fetch a pool of recent orders to allow for server-side filtering
                // of encrypted names while keeping performance high.
                $query = \App\Models\LabOrder::with(['patient', 'doctor', 'department'])
                    ->where('clinic_id', $clinicId)
                    ->orderByDesc('created_at');

                if ($search) {
                    // Fetch more records to filter in-memory because standard LIKE 
                    // doesn't work on encrypted patient names.
                    $allRecent = $query->limit(100)->get();
                    
                    $recentOrders = $allRecent->filter(function($order) use ($search) {
                        $term = strtolower($search);
                        
                        // 1. Check Order Number
                        if (str_contains(strtolower($order->order_number), $term)) return true;
                        
                        // 2. Check Accession Number
                        if ($order->accession_number && str_contains(strtolower($order->accession_number), $term)) return true;
                        
                        // 3. Check Patient Name (DECRYPTED)
                        if (str_contains(strtolower($order->patient_name ?? ''), $term)) return true;
                        
                        // 4. Check Patient UHID
                        if (str_contains(strtolower($order->patient?->uhid ?? ''), $term)) return true;

                        return false;
                    })->take(20);
                } else {
                    $recentOrders = $query->limit(20)->get();
                }
            }

            $patients = Patient::where('clinic_id', $clinicId)->orderBy('name')->get(['id', 'name', 'phone']);

            Log::info('LabController@dashboard loaded', ['patients_count' => $patients->count(), 'stats' => $stats, 'search' => $search]);
        } catch (\Throwable $e) {
            Log::error('LabController@dashboard error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        }

        return view('lab.index', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'patients' => $patients,
            'search' => $request->query('search')
        ]);
    }

    public function catalog(Request $request)
    {
        $clinicId = auth()->user()->clinic_id;
        Log::info('LabController@catalog', ['clinic_id' => $clinicId, 'query' => $request->all()]);

        $tests = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
        $categories = collect();
        $activeCount = 0;
        $categoryCount = 0;

        try {
            if (! Schema::hasTable('lab_tests_catalog')) {
                return view('lab.catalog', compact('tests', 'categories', 'activeCount', 'categoryCount'));
            }

            if (Schema::hasTable('lab_departments') && Schema::hasColumn('lab_tests_catalog', 'department_id')) {
                try {
                    app(EnsureLabCatalogDefaults::class)->syncForClinic($clinicId);
                } catch (\Throwable $e) {
                    Log::warning('LabController@catalog: default catalog sync skipped', ['error' => $e->getMessage()]);
                }

                $nameCol = Schema::hasColumn('lab_tests_catalog', 'test_name') ? 'lab_tests_catalog.test_name' : 'lab_tests_catalog.name';

                $q = DB::table('lab_tests_catalog')
                    ->join('lab_departments', 'lab_tests_catalog.department_id', '=', 'lab_departments.id')
                    ->where('lab_tests_catalog.clinic_id', $clinicId)
                    ->where('lab_departments.clinic_id', $clinicId)
                    ->select([
                        'lab_tests_catalog.id',
                        'lab_tests_catalog.test_name as name',
                        'lab_tests_catalog.test_code as code',
                        'lab_departments.name as category',
                        'lab_tests_catalog.sample_type',
                        'lab_tests_catalog.price',
                        'lab_tests_catalog.tat_hours as turnaround_hours',
                        'lab_tests_catalog.is_active',
                    ]);

                if ($request->filled('search')) {
                    $term = '%'.$request->string('search').'%';
                    $q->where(function ($w) use ($term) {
                        $w->where('lab_tests_catalog.test_name', 'like', $term)
                            ->orWhere('lab_tests_catalog.test_code', 'like', $term);
                    });
                }
                if ($request->filled('category')) {
                    $q->where('lab_departments.name', $request->string('category'));
                }

                $tests = $q->orderBy('lab_departments.name')->orderBy($nameCol)->paginate(20)->withQueryString();

                $categories = DB::table('lab_departments')
                    ->where('clinic_id', $clinicId)
                    ->orderBy('name')
                    ->pluck('name');
                $categoryCount = $categories->count();
                $activeCount = (int) DB::table('lab_tests_catalog')
                    ->where('clinic_id', $clinicId)
                    ->where('is_active', true)
                    ->count();
                Log::info('LabController@catalog HIMS catalog loaded', [
                    'total'       => $tests->total(),
                    'categories'  => $categoryCount,
                    'activeCount' => $activeCount,
                ]);
            } else {
                $q = LabTestCatalog::where('clinic_id', $clinicId);
                if ($request->filled('search')) {
                    $term = '%'.$request->string('search').'%';
                    $q->where(function ($w) use ($term) {
                        $w->where('name', 'like', $term)->orWhere('code', 'like', $term);
                    });
                }
                if ($request->filled('category')) {
                    $q->where('category', $request->string('category'));
                }
                $tests = $q->orderBy('name')->paginate(20)->withQueryString();
                $categories = LabTestCatalog::where('clinic_id', $clinicId)
                    ->distinct()
                    ->orderBy('category')
                    ->pluck('category')
                    ->filter()
                    ->values();
                $categoryCount = $categories->count();
                $activeCount = (int) LabTestCatalog::where('clinic_id', $clinicId)->where('is_active', true)->count();
            }
        } catch (\Throwable $e) {
            Log::error('LabController@catalog error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        }

        return view('lab.catalog', compact('tests', 'categories', 'activeCount', 'categoryCount'));
    }

    public function storeTest(Request $request)
    {
        Log::info('LabController@storeTest', ['user' => auth()->id()]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'turnaround_hours' => 'nullable|integer|min:1',
            'sample_type' => 'nullable|string|max:100',
            'reference_range' => 'nullable|string',
            'unit' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);
        $clinicId = auth()->user()->clinic_id;

        try {
            if (!Schema::hasTable('lab_tests_catalog')) {
                Log::warning('LabController@storeTest: lab_tests_catalog missing');

                return redirect()->route('laboratory.catalog')->with('error', 'Lab catalog is not installed.');
            }

            $columns = Schema::getColumnListing('lab_tests_catalog');
            $colset = array_flip($columns);

            if (isset($colset['test_name']) && Schema::hasTable('lab_departments')) {
                $deptId = DB::table('lab_departments')->where('clinic_id', $clinicId)->value('id');
                if (!$deptId) {
                    $deptId = DB::table('lab_departments')->insertGetId([
                        'clinic_id' => $clinicId,
                        'name' => 'General',
                        'code' => 'GEN',
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    Log::info('LabController@storeTest created default lab_departments row', ['id' => $deptId]);
                }

                $code = $validated['code'] ?: strtoupper(substr(preg_replace('/\s+/', '_', preg_replace('/[^a-zA-Z0-9\s]+/', '', $validated['name'])), 0, 40));
                if ($code === '') {
                    $code = 'TEST-' . substr(uniqid(), -8);
                }

                $sampleType = $validated['sample_type'] ?? 'blood';
                $allowedSamples = ['blood', 'urine', 'stool', 'swab', 'fluid', 'tissue', 'sputum', 'other'];
                if (!in_array($sampleType, $allowedSamples, true)) {
                    $sampleType = 'other';
                }

                $insert = [
                    'clinic_id' => $clinicId,
                    'department_id' => $deptId,
                    'test_code' => $code,
                    'test_name' => $validated['name'],
                    'test_type' => 'single',
                    'price' => $validated['price'],
                    'sample_type' => $sampleType,
                    'tat_hours' => $validated['turnaround_hours'] ?? 24,
                    'unit' => $validated['unit'] ?? null,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $row = array_intersect_key($insert, $colset);
                DB::table('lab_tests_catalog')->insert($row);
                Log::info('LabController@storeTest HIMS row inserted', ['keys' => array_keys($row), 'name' => $validated['name']]);
            } else {
                $validated['clinic_id'] = $clinicId;
                $validated['is_active'] = true;
                LabTestCatalog::create($validated);
                Log::info('LabController@storeTest simple catalog created', ['name' => $validated['name']]);
            }

            return redirect()->route('laboratory.catalog')->with('success', 'Test added successfully');
        } catch (\Throwable $e) {
            Log::error('LabController@storeTest error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return redirect()->route('laboratory.catalog')->with('error', 'Failed to add test: ' . $e->getMessage());
        }
    }

    public function orders(Request $request)
    {
        $user = auth()->user();
        $clinicId = $user->clinic_id;
        Log::info('LabController@orders', ['clinic_id' => $clinicId, 'query' => $request->all()]);

        if ($clinicId === null) {
            Log::warning('LabController@orders: user has no clinic_id', ['user_id' => $user->id]);

            return redirect()->route('app.home')->with('error', 'No clinic associated with your account.');
        }

        $orders = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
        $patients = collect();
        $tests = collect();
        $labOrderTestIds = [];
        $stats = [
            'pending' => 0,
            'in_progress' => 0,
            'completed_today' => 0,
            'total_month' => 0,
        ];

        try {
            if (Schema::hasTable('lab_orders') && Schema::hasColumn('lab_orders', 'clinic_id')) {
                try {
                    $ordersBase = DB::table('lab_orders')->where('lab_orders.clinic_id', $clinicId);
                    $stats['pending'] = (clone $ordersBase)->whereIn('status', ['pending', 'ordered', 'new', 'accepted'])->count();
                    $stats['in_progress'] = (clone $ordersBase)->whereIn('status', ['sample_collected', 'processing'])->count();

                    if (Schema::hasColumn('lab_orders', 'updated_at')) {
                        $stats['completed_today'] = (clone $ordersBase)->whereIn('status', ['completed', 'ready', 'sent'])->whereDate('updated_at', today())->count();
                    }
                    if (Schema::hasColumn('lab_orders', 'created_at')) {
                        $stats['total_month'] = (clone $ordersBase)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
                    } else {
                        $stats['total_month'] = (clone $ordersBase)->count();
                    }

                    $select = [
                        'lab_orders.*',
                        'patients.name as patient_name',
                        'lab_departments.name as department_name',
                    ];
                    if (Schema::hasColumn('patients', 'phone')) {
                        $select[] = 'patients.phone as patient_phone';
                    } else {
                        $select[] = DB::raw('NULL as patient_phone');
                    }

                    $ordersQuery = LabOrder::with(['patient', 'department', 'doctor'])
                        ->where('clinic_id', $clinicId);

                    if ($request->filled('patient_id')) {
                        $ordersQuery->where('patient_id', $request->integer('patient_id'));
                    }
                    if ($request->filled('status')) {
                        $ordersQuery->where('status', $request->string('status'));
                    }
                    if ($request->filled('date_from')) {
                        $ordersQuery->whereDate('created_at', '>=', $request->date('date_from'));
                    }
                    if ($request->filled('date_to')) {
                        $ordersQuery->whereDate('created_at', '<=', $request->date('date_to'));
                    }
                    if ($request->filled('search')) {
                        $term = $request->string('search');
                        $ordersQuery->where(function ($q) use ($term) {
                            $q->whereHas('patient', function ($pq) use ($term) {
                                $pq->searchByToken($term);
                            })
                            ->orWhere('order_number', 'like', "%{$term}%")
                            ->orWhere('accession_number', 'like', "%{$term}%");
                        });
                    }

                    $orders = $ordersQuery->orderByDesc('created_at')->paginate(20)->withQueryString();
                } catch (\Throwable $eList) {
                    Log::error('LabController@orders: order list query failed', [
                        'error' => $eList->getMessage(),
                        'trace' => $eList->getTraceAsString(),
                    ]);
                    $orders = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
                }
            }

            $patientCols = ['id', 'name'];
            if (Schema::hasColumn('patients', 'phone')) {
                $patientCols[] = 'phone';
            }
            $patients = Patient::where('clinic_id', $clinicId)->orderBy('name')->get($patientCols);

            if (Schema::hasTable('lab_tests_catalog')) {
                try {
                    if (Schema::hasTable('lab_departments') && Schema::hasColumn('lab_tests_catalog', 'department_id')) {
                        try {
                            app(EnsureLabCatalogDefaults::class)->syncForClinic($clinicId);
                        } catch (\Throwable $e) {
                            Log::warning('LabController@orders: default catalog sync skipped', ['error' => $e->getMessage()]);
                        }
                        $catQuery = DB::table('lab_tests_catalog')
                            ->join('lab_departments', 'lab_tests_catalog.department_id', '=', 'lab_departments.id')
                            ->where('lab_tests_catalog.clinic_id', $clinicId)
                            ->where('lab_departments.clinic_id', $clinicId)
                            ->where('lab_tests_catalog.is_active', true)
                            ->select(
                                'lab_tests_catalog.*',
                                'lab_departments.name as department_name'
                            )
                            ->orderBy('lab_departments.name');
                        if (Schema::hasColumn('lab_tests_catalog', 'test_name')) {
                            $catQuery->orderBy('lab_tests_catalog.test_name');
                        } elseif (Schema::hasColumn('lab_tests_catalog', 'name')) {
                            $catQuery->orderBy('lab_tests_catalog.name');
                        } else {
                            $catQuery->orderBy('lab_tests_catalog.id');
                        }
                        $tests = $catQuery->get();
                        Log::info('LabController@orders catalog joined lab_departments', ['tests' => $tests->count()]);
                    } else {
                        $catCols = array_flip(Schema::getColumnListing('lab_tests_catalog'));
                        $orderCol = isset($catCols['name']) ? 'name' : (isset($catCols['test_name']) ? 'test_name' : 'id');
                        $tests = LabTestCatalog::where('clinic_id', $clinicId);
                        if (isset($catCols['is_active'])) {
                            $tests->where('is_active', true);
                        }
                        $tests = $tests->orderBy($orderCol)->get();
                    }
                } catch (\Throwable $eCat) {
                    Log::error('LabController@orders: catalog load failed', [
                        'error' => $eCat->getMessage(),
                        'trace' => $eCat->getTraceAsString(),
                    ]);
                    $tests = collect();
                }
            }

            $labOrderTestIds = $tests->pluck('id')->filter()->values()->map(function ($id) {
                return is_numeric($id) ? (int) $id : (string) $id;
            })->all();

            Log::info('LabController@orders loaded', [
                'orders_count' => $orders instanceof \Countable ? $orders->count() : 0,
                'stats'        => $stats,
                'test_ids'     => count($labOrderTestIds),
            ]);
        } catch (\Throwable $e) {
            Log::error('LabController@orders error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            $labOrderTestIds = [];
        }

        $departments = collect();
        if (Schema::hasTable('lab_departments')) {
            $departments = DB::table('lab_departments')
                ->where('clinic_id', $clinicId)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }

        return view('lab.orders', compact('orders', 'patients', 'tests', 'stats', 'labOrderTestIds', 'departments'));
    }

    public function collectSampleWeb(Request $request, int $orderId)
    {
        $clinicId = auth()->user()->clinic_id;
        Log::info('LabController@collectSampleWeb', ['order_id' => $orderId, 'clinic_id' => $clinicId]);

        $validated = $request->validate([
            'sample_type' => 'required|string|max:100',
            'accession_number' => 'nullable|string|max:50',
            'collection_notes' => 'nullable|string',
        ]);

        try {
            app(LabSampleCollectionService::class)->collectForOrder($orderId, $clinicId, $validated);
            
            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Sample collected successfully.']);
            }
        } catch (\Throwable $e) {
            Log::error('LabController@collectSampleWeb error', ['error' => $e->getMessage(), 'order_id' => $orderId]);

            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 422);
            }
            return redirect()->route('laboratory.orders')->with('error', 'Could not record sample collection.');
        }

        return redirect()->route('laboratory.orders')->with('success', 'Sample collected. Accession recorded.');
    }

    public function orderDetailsApi(int $orderId)
    {
        $clinicId = auth()->user()->clinic_id;
        try {
            // Eager load everything that might contain data
            $order = \App\Models\LabOrder::with(['patient', 'doctor', 'labOrderItems', 'labOrderTests'])
                ->where('clinic_id', $clinicId)
                ->findOrFail($orderId);
            
            // Collect items from multiple possible sources
            $items = collect();
            
            // Source 1: labOrderItems (Modern HIMS)
            if ($order->labOrderItems && $order->labOrderItems->isNotEmpty()) {
                $items = $order->labOrderItems->map(fn($it) => [
                    'id' => $it->id,
                    'name' => $it->test_name ?? 'Unnamed Test',
                    'test_code' => $it->test_code ?? '-',
                    'price' => $it->price ?? 0,
                    'status' => $it->status ?? 'pending'
                ]);
            } 
            // Source 2: labOrderTests (Legacy/Integration relation)
            elseif ($order->labOrderTests && $order->labOrderTests->isNotEmpty()) {
                $items = $order->labOrderTests->map(fn($it) => [
                    'id' => $it->id,
                    'name' => $it->test_name ?? 'Unnamed Test',
                    'test_code' => $it->test_code ?? '-',
                    'price' => $it->price ?? 0,
                    'status' => $it->status ?? 'pending'
                ]);
            }
            // Source 3: JSON 'tests' column (External Integrations)
            else {
                $rawTests = $order->getAttribute('tests');
                if (is_string($rawTests)) {
                    $rawTests = json_decode($rawTests, true);
                }
                if (is_array($rawTests)) {
                    $items = collect($rawTests)->map(fn($t, $idx) => [
                        'id' => 'json-' . $idx,
                        'name' => is_array($t) ? ($t['name'] ?? $t['test_name'] ?? 'Test') : (is_string($t) ? $t : 'Test'),
                        'test_code' => is_array($t) ? ($t['code'] ?? $t['test_code'] ?? '-') : '-',
                        'price' => is_array($t) ? ($t['price'] ?? 0) : 0,
                        'status' => is_array($t) ? ($t['status'] ?? 'sent') : 'sent',
                    ]);
                }
            }

            // Safe metadata discovery
            $patientName = $order->patient_name ?: ($order->patient ? $order->patient->name : 'Walk-in Patient');
            $patientPhone = $order->patient_phone ?: ($order->patient ? $order->patient->phone : '—');
            
            return response()->json([
                'success' => true,
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'status' => $order->status,
                    'created_at' => $order->created_at ? $order->created_at->format('d M Y, h:i A') : null,
                    'total_amount' => $order->total_amount,
                    'patient' => [
                        'name' => $patientName,
                        'phone' => $patientPhone,
                    ],
                    'doctor' => $order->doctor ? ['name' => $order->doctor->name] : ['name' => 'Self / Walk-in'],
                    'department_name' => $order->department_name ?? $order->department?->name ?? 'Laboratory',
                    'items' => $items
                ]
            ]);
        } catch (\Throwable $e) {
            Log::error('LabController@orderDetailsApi error', [
                'id' => $orderId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false, 
                'error' => 'Technical error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function viewOrderReport(int $orderId)
    {
        $clinicId = auth()->user()->clinic_id;
        Log::info('LabController@viewOrderReport', ['order_id' => $orderId, 'clinic_id' => $clinicId]);

        try {
            $patientSelect = [
                'patients.name as patient_name',
                'patients.phone',
            ];
            if (Schema::hasColumn('patients', 'dob')) {
                $patientSelect[] = 'patients.dob as date_of_birth';
            } elseif (Schema::hasColumn('patients', 'date_of_birth')) {
                $patientSelect[] = 'patients.date_of_birth as date_of_birth';
            } else {
                $patientSelect[] = DB::raw('NULL as date_of_birth');
            }
            if (Schema::hasColumn('patients', 'sex')) {
                $patientSelect[] = 'patients.sex as gender';
            } elseif (Schema::hasColumn('patients', 'gender')) {
                $patientSelect[] = 'patients.gender as gender';
            } else {
                $patientSelect[] = DB::raw('NULL as gender');
            }

            $order = DB::table('lab_orders')
                ->join('patients', 'lab_orders.patient_id', '=', 'patients.id')
                ->where('lab_orders.id', $orderId)
                ->where('lab_orders.clinic_id', $clinicId)
                ->select(array_merge(['lab_orders.*'], $patientSelect));

            $this->joinLabOrderDoctorForOrders($order);

            $order = $order->firstOrFail();
            $items = $this->buildResultEntryLineItems($orderId);
            $clinicName = auth()->user()->clinic->name ?? config('app.name');

            Log::info('LabController@viewOrderReport rendering', ['order_id' => $orderId, 'items' => $items->count()]);

            return view('lab.technician.report', compact('order', 'items', 'clinicName'));
        } catch (\Throwable $e) {
            Log::error('LabController@viewOrderReport error', [
                'error' => $e->getMessage(),
                'order_id' => $orderId,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('laboratory.orders')->with('error', 'Could not load lab report.');
        }
    }

    public function approveReport(Request $request, int $orderId)
    {
        $clinicId = auth()->user()->clinic_id;

        try {
            $order = DB::table('lab_orders')
                ->where('id', $orderId)
                ->where('clinic_id', $clinicId)
                ->first();

            if (!$order) {
                return back()->with('error', 'Lab order not found.');
            }

            // Mark as sent
            $orderCols = array_flip(\Illuminate\Support\Facades\Schema::getColumnListing('lab_orders'));
            $update = ['updated_at' => now()];

            if (isset($orderCols['status'])) {
                $update['status'] = 'sent';
            }
            if (isset($orderCols['result_sent_at'])) {
                $update['result_sent_at'] = now();
            }
            if (isset($orderCols['result_sent_to_patient'])) {
                $update['result_sent_to_patient'] = true;
            }

            DB::table('lab_orders')->where('id', $orderId)->update($update);

            \App\Models\AuditLog::log(
                'lab_results_approved',
                "Doctor approved lab results for order #{$orderId}",
                'lab_orders',
                $orderId
            );

            // Notify over WhatsApp
            $patient = \App\Models\Patient::find($order->patient_id);
            $labOrder = \App\Models\LabOrder::find($orderId);

            Log::info('Checking notification requirements', [
                'order_id' => $orderId,
                'patient_found' => (bool)$patient,
                'lab_order_found' => (bool)$labOrder,
                'patient_phone' => $patient?->phone,
            ]);

            if ($patient && $labOrder && $patient->phone) {
                Log::info('Triggering WhatsAppService@sendLabResults', ['order_id' => $orderId]);
                app(\App\Services\WhatsAppService::class)->sendLabResults($patient, $labOrder);
                Log::info('WhatsApp lab results sent after doctor approval', ['order_id' => $orderId]);
            } else {
                Log::warning('WhatsApp lab results skipped', [
                    'order_id' => $orderId,
                    'patient_id' => $order->patient_id,
                    'reason' => (!$patient ? 'Patient not found' : (!$labOrder ? 'LabOrder not found' : 'Phone missing'))
                ]);
            }

            return back()->with('success', 'Lab report approved and patient notified via WhatsApp.');
        } catch (\Throwable $e) {
            Log::error('LabController@approveReport error', ['error' => $e->getMessage()]);
            return back()->with('error', 'Error approving report: ' . $e->getMessage());
        }
    }

    /**
     * Left join ordering doctor for lab orders list / report (doctor_name for report template).
     */
    private function joinLabOrderDoctorForOrders($query): void
    {
        if (Schema::hasColumn('lab_orders', 'doctor_id')) {
            $query->leftJoin('users as lab_order_doctors', 'lab_orders.doctor_id', '=', 'lab_order_doctors.id');
        } elseif (Schema::hasColumn('lab_orders', 'ordered_by')) {
            $query->leftJoin('users as lab_order_doctors', 'lab_orders.ordered_by', '=', 'lab_order_doctors.id');
        } elseif (Schema::hasColumn('lab_orders', 'created_by')) {
            $query->leftJoin('users as lab_order_doctors', 'lab_orders.created_by', '=', 'lab_order_doctors.id');
        } else {
            $query->addSelect(DB::raw('NULL as doctor_name'));
            $query->addSelect(DB::raw('NULL as ordered_by_name'));

            return;
        }
        $query->addSelect('lab_order_doctors.name as doctor_name');
        $query->addSelect('lab_order_doctors.name as ordered_by_name');
    }

    public function storeOrder(Request $request)
    {
        Log::info('LabController@storeOrder', ['user' => auth()->id()]);

        if (!Schema::hasTable('lab_orders') || !Schema::hasTable('lab_order_items')) {
            return redirect()->route('laboratory.index')->with('error', 'Lab module tables are not yet set up. Please run migrations.');
        }

        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'tests' => 'required|array|min:1',
            'tests.*' => 'exists:lab_tests_catalog,id',
            'notes' => 'nullable|string',
            'priority' => 'in:routine,urgent,stat',
            'sample_collection_type' => 'nullable|in:lab,home',
            'collection_date' => 'nullable|date',
            'collection_address' => 'nullable|string',
        ]);
        $clinicId = auth()->user()->clinic_id;

        try {
            DB::transaction(function () use ($validated, $clinicId) {
                $orderCols = array_flip(Schema::getColumnListing('lab_orders'));
                $itemCols = array_flip(Schema::getColumnListing('lab_order_items'));

                $orderRow = $this->buildInternalLabOrderRow($orderCols, $clinicId, $validated);
                Log::info('LabController@storeOrder lab_orders row', ['columns' => array_keys($orderRow)]);

                $orderId = DB::table('lab_orders')->insertGetId($orderRow);

                $orderFk = isset($itemCols['lab_order_id']) ? 'lab_order_id' : (isset($itemCols['order_id']) ? 'order_id' : null);
                $testFk = isset($itemCols['lab_test_catalog_id']) ? 'lab_test_catalog_id' : (isset($itemCols['test_id']) ? 'test_id' : null);

                if (!$orderFk || !$testFk) {
                    Log::error('LabController@storeOrder unsupported lab_order_items schema', ['itemCols' => array_keys($itemCols)]);

                    throw new \RuntimeException('lab_order_items table uses an unsupported column layout.');
                }

                foreach ($validated['tests'] as $testId) {
                    $testRow = DB::table('lab_tests_catalog')->where('id', $testId)->first();
                    $price = $testRow->price ?? 0;

                    $itemInsert = [
                        $orderFk => $orderId,
                        $testFk => $testId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    if (isset($itemCols['status'])) {
                        $itemInsert['status'] = 'pending';
                    }
                    if (isset($itemCols['price'])) {
                        $itemInsert['price'] = $price;
                    }
                    if (isset($itemCols['discount'])) {
                        $itemInsert['discount'] = 0;
                    }

                    $itemInsert = array_intersect_key($itemInsert, $itemCols);
                    DB::table('lab_order_items')->insert($itemInsert);
                    Log::info('LabController@storeOrder line item inserted', ['order_id' => $orderId, 'test_id' => $testId]);
                }
            });

            Log::info('Lab order created successfully');
            return redirect()->route('laboratory.orders')->with('success', 'Lab order created successfully');
        } catch (\Throwable $e) {
            Log::error('LabController@storeOrder error', ['error' => $e->getMessage()]);
            return redirect()->route('laboratory.orders')->with('error', 'Failed to create lab order: ' . $e->getMessage());
        }
    }

    public function resultEntry(Request $request, $orderId)
    {
        $clinicId = auth()->user()->clinic_id;
        Log::info('LabController@resultEntry', ['order_id' => $orderId, 'clinic_id' => $clinicId]);

        try {
            $order = \App\Models\LabOrder::with('patient')
                ->where('id', $orderId)
                ->where('clinic_id', $clinicId)
                ->firstOrFail();
            $items = $this->buildResultEntryLineItems($orderId);
            Log::info('LabController@resultEntry items', ['order_id' => $orderId, 'count' => $items->count()]);

            return view('lab.result-entry', compact('order', 'items'));
        } catch (\Throwable $e) {
            return redirect()->route('laboratory.orders')->with('error', 'Failed to load result entry: ' . $e->getMessage());
        }
    }

    public function downloadReport(Request $request, $orderId)
    {
        try {
            if ($request->hasValidSignature()) {
                 $order = \App\Models\LabOrder::with(['patient', 'doctor', 'clinic'])->findOrFail($orderId);
                 $clinic = $order->clinic;
            } else {
                 $clinicId = auth()->user()->clinic_id;
                 $order = \App\Models\LabOrder::with(['patient', 'doctor', 'clinic'])
                    ->where('id', $orderId)
                    ->where('clinic_id', $clinicId)
                    ->firstOrFail();
                 $clinic = auth()->user()->clinic;
            }
            
            $items = $this->buildResultEntryLineItems($orderId);

            $pdf = Pdf::loadView('lab.report-pdf', compact('order', 'items', 'clinic'));
            
            return $pdf->stream("Lab_Report_{$order->order_number}.pdf");
        } catch (\Throwable $e) {
            Log::error('LabController@downloadReport error', ['error' => $e->getMessage(), 'order_id' => $orderId]);
            return redirect()->back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }

    public function shareOnWhatsApp(Request $request, $orderId)
    {
        $clinicId = auth()->user()->clinic_id;
        try {
            $order = \App\Models\LabOrder::with(['patient'])->where('id', $orderId)->where('clinic_id', $clinicId)->firstOrFail();
            $patient = $order->patient;

            if (!$patient || !$patient->phone) {
                return response()->json(['success' => false, 'error' => 'Patient has no WhatsApp number']);
            }

            $success = app(\App\Services\WhatsAppService::class)->sendLabReportShare($patient, $order);

            if ($success) {
                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false, 'error' => 'Failed to send message via WhatsApp']);
            }
        } catch (\Throwable $e) {
            Log::error('LabController@shareOnWhatsApp error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function saveResult(Request $request, $orderId)
    {
        $action = $request->input('action', 'save_print'); // 'draft' or 'save_print'
        $notes = $request->input('notes');
        
        Log::info('LabController@saveResult', ['order_id' => $orderId, 'action' => $action]);

        $validated = $request->validate([
            'results' => 'required|array',
            'results.*.item_id' => 'required|integer',
            'results.*.value' => 'required|string',
            'results.*.is_abnormal' => 'nullable|integer', // Changed to integer to match form 0/1
            'results.*.remarks' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($validated, $orderId, $action, $notes) {
                $itemCols = Schema::hasTable('lab_order_items')
                    ? array_flip(Schema::getColumnListing('lab_order_items'))
                    : [];
                $orderFk = $this->labOrderItemsOrderFk();

                foreach ($validated['results'] as $result) {
                    $update = [];
                    if (isset($itemCols['result_value'])) {
                        $update['result_value'] = $result['value'];
                    }
                    if (isset($itemCols['value'])) {
                        $update['value'] = $result['value'];
                    }
                    if (isset($itemCols['is_abnormal'])) {
                        $update['is_abnormal'] = !empty($result['is_abnormal']);
                    }
                    if (isset($itemCols['remarks'])) {
                        $update['remarks'] = $result['remarks'] ?? null;
                    }
                    if (isset($itemCols['status']) && $action === 'save_print') {
                        $update['status'] = 'completed';
                    }
                    if (isset($itemCols['updated_at'])) {
                        $update['updated_at'] = now();
                    }
                    if ($update === []) {
                        continue;
                    }
                    $q = DB::table('lab_order_items')->where('id', $result['item_id']);
                    if ($orderFk) {
                        $q->where($orderFk, $orderId);
                    }
                    $q->update($update);
                }

                $orderUpdate = ['updated_at' => now()];
                $orderCols = Schema::hasTable('lab_orders')
                    ? array_flip(Schema::getColumnListing('lab_orders'))
                    : [];
                
                if (isset($orderCols['notes'])) {
                    $orderUpdate['notes'] = $notes;
                }

                if (isset($orderCols['status'])) {
                    $orderUpdate['status'] = ($action === 'save_print') ? 'completed' : 'processing';
                }
                
                if (isset($orderCols['completed_at']) && $action === 'save_print') {
                    $orderUpdate['completed_at'] = now();
                }

                DB::table('lab_orders')->where('id', $orderId)->update($orderUpdate);
            });

            $msg = $action === 'save_print' ? 'Results verified and completed successfully' : 'Draft results saved successfully';
            return redirect()->route('laboratory.index')->with('success', $msg);
        } catch (\Throwable $e) {
            Log::error('LabController@saveResult error', ['error' => $e->getMessage(), 'order_id' => $orderId]);
            return redirect()->back()->withInput()->with('error', 'Failed to save results: ' . $e->getMessage());
        }
    }

    /**
     * Build a lab_orders insert row using only columns that exist (core vs HIMS vs integration).
     *
     * @param  array<string, int>  $orderCols  flip(Schema::getColumnListing('lab_orders'))
     * @return array<string, mixed>
     */
    private function buildInternalLabOrderRow(array $orderCols, int $clinicId, array $validated): array
    {
        $notes = $validated['notes'] ?? null;
        $priority = $validated['priority'] ?? 'routine';
        $userId = auth()->id();

        $candidates = [
            'clinic_id' => $clinicId,
            'patient_id' => $validated['patient_id'],
            'doctor_id' => $userId,
            'ordered_by' => $userId,
            'created_by' => $userId,
            'visit_id' => null,
            'admission_id' => null,
            'priority' => $priority,
            'clinical_notes' => $notes,
            'notes' => $notes,
            'order_number' => 'LAB-' . strtoupper(uniqid()),
            'order_date' => now()->toDateString(),
            'status' => 'pending',
            'sample_collection_type' => $validated['sample_collection_type'] ?? 'lab',
            'collection_date' => $validated['collection_date'] ?? null,
            'collection_address' => $validated['collection_address'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $row = array_intersect_key($candidates, $orderCols);

        if (isset($orderCols['ordered_by'])) {
            $row['ordered_by'] = $userId;
            $row['status'] = 'ordered';
        }

        if (isset($orderCols['order_date']) && !isset($row['order_date'])) {
            $row['order_date'] = now()->toDateString();
        }

        if (isset($orderCols['status']) && !isset($row['status'])) {
            $row['status'] = 'pending';
        }

        Log::info('LabController@buildInternalLabOrderRow done', [
            'has_ordered_by' => isset($orderCols['ordered_by']),
            'status' => $row['status'] ?? null,
        ]);

        return $row;
    }

    private function labOrderItemsOrderFk(): ?string
    {
        if (!Schema::hasTable('lab_order_items')) {
            return null;
        }
        $cols = Schema::getColumnListing('lab_order_items');
        if (in_array('lab_order_id', $cols, true)) {
            return 'lab_order_id';
        }
        if (in_array('order_id', $cols, true)) {
            return 'order_id';
        }

        return null;
    }

    private function labOrderItemsTestFk(): ?string
    {
        if (!Schema::hasTable('lab_order_items')) {
            return null;
        }
        $cols = Schema::getColumnListing('lab_order_items');
        if (in_array('lab_test_catalog_id', $cols, true)) {
            return 'lab_test_catalog_id';
        }
        if (in_array('test_id', $cols, true)) {
            return 'test_id';
        }

        return null;
    }

    /**
     * Line items for /laboratory result entry — matches lab_order_items + lab_tests_catalog layout on the server.
     */
    private function buildResultEntryLineItems(int $orderId): Collection
    {
        if (!Schema::hasTable('lab_order_items') || !Schema::hasTable('lab_tests_catalog')) {
            Log::warning('LabController@buildResultEntryLineItems: missing tables');

            return collect();
        }

        $orderFk = $this->labOrderItemsOrderFk();
        $testFk = $this->labOrderItemsTestFk();
        if (!$orderFk || !$testFk) {
            Log::warning('LabController@buildResultEntryLineItems: unsupported FK columns');

            return collect();
        }

        $catCols = array_flip(Schema::getColumnListing('lab_tests_catalog'));
        $select = ['lab_order_items.*'];
        if (isset($catCols['name'])) {
            $select[] = 'lab_tests_catalog.name as test_name';
        } elseif (isset($catCols['test_name'])) {
            $select[] = 'lab_tests_catalog.test_name as test_name';
        } else {
            $select[] = DB::raw("'' as test_name");
        }
        if (isset($catCols['unit'])) {
            $select[] = 'lab_tests_catalog.unit';
        } else {
            $select[] = DB::raw('NULL as unit');
        }
        if (isset($catCols['reference_range'])) {
            $select[] = 'lab_tests_catalog.reference_range';
        } elseif (isset($catCols['normal_range_male'])) {
            $select[] = 'lab_tests_catalog.normal_range_male as reference_range';
        } else {
            $select[] = DB::raw('NULL as reference_range');
        }

        if (isset($catCols['category'])) {
            $select[] = 'lab_tests_catalog.category';
        } else {
            $select[] = DB::raw("'General' as category");
        }

        return DB::table('lab_order_items')
            ->join('lab_tests_catalog', "lab_order_items.{$testFk}", '=', 'lab_tests_catalog.id')
            ->where("lab_order_items.{$orderFk}", $orderId)
            ->select($select)
            ->get();
    }

    public function editTest(int $id): View
    {
        $clinicId = auth()->user()->clinic_id;
        $test = DB::table('lab_tests_catalog')->where('clinic_id', $clinicId)->where('id', $id)->first();
        if (!$test) abort(404);

        $categories = DB::table('lab_tests_catalog')
            ->where('clinic_id', $clinicId)
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->filter();

        return view('lab.edit-test', compact('test', 'categories'));
    }

    public function updateTest(Request $request, int $id): RedirectResponse
    {
        $clinicId = auth()->user()->clinic_id;
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'turnaround_hours' => 'nullable|integer|min:1',
            'sample_type' => 'nullable|string|max:100',
            'reference_range' => 'nullable|string',
            'unit' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $update = [
            'test_name' => $validated['name'],
            'test_code' => $validated['code'],
            'price' => $validated['price'],
            'tat_hours' => $validated['turnaround_hours'],
            'sample_type' => $validated['sample_type'],
            'unit' => $validated['unit'],
            'is_active' => $request->has('is_active'),
            'updated_at' => now(),
        ];

        // Handle both simple and HIMS schemas
        $cols = array_flip(Schema::getColumnListing('lab_tests_catalog'));
        if (isset($cols['name'])) $update['name'] = $validated['name'];
        if (isset($cols['code'])) $update['code'] = $validated['code'];
        if (isset($cols['category'])) $update['category'] = $validated['category'];

        DB::table('lab_tests_catalog')
            ->where('clinic_id', $clinicId)
            ->where('id', $id)
            ->update(array_intersect_key($update, $cols));

        return redirect()->route('laboratory.catalog')->with('success', 'Test updated successfully');
    }

    public function destroyTest(int $id): RedirectResponse
    {
        $clinicId = auth()->user()->clinic_id;
        DB::table('lab_tests_catalog')
            ->where('clinic_id', $clinicId)
            ->where('id', $id)
            ->delete();

        return redirect()->route('laboratory.catalog')->with('success', 'Test removed from catalog.');
    }
}
