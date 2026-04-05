<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\LabOrderLineItemSync;
use App\Services\LabSampleCollectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class LabTechnicianController extends Controller
{
    /**
     * Lab technician dashboard — shows pending/in-progress orders assigned to this clinic.
     */
    public function dashboard(Request $request)
    {
        Log::info('LabTechnicianController@dashboard', ['clinic_id' => auth()->user()->clinic_id]);
        $clinicId = auth()->user()->clinic_id;

        $pendingOrders = collect();
        $stats = ['pending' => 0, 'sample_collected' => 0, 'processing' => 0, 'completed_today' => 0];
        $labOrderItemFk = Schema::hasTable('lab_order_items') ? $this->labOrderItemsOrderFk() : null;
        $labOrderItemsHasAbnormal = Schema::hasTable('lab_order_items')
            && Schema::hasColumn('lab_order_items', 'is_abnormal');
        Log::info('LabTechnicianController@dashboard lab_order_items meta', [
            'labOrderItemFk' => $labOrderItemFk,
            'labOrderItemsHasAbnormal' => $labOrderItemsHasAbnormal,
        ]);

        try {
            if (!Schema::hasTable('lab_orders')) {
                Log::warning('LabTechnicianController: lab_orders table missing');

                return view('lab.technician.dashboard', compact(
                    'pendingOrders',
                    'stats',
                    'labOrderItemFk',
                    'labOrderItemsHasAbnormal'
                ));
            }

            $query = DB::table('lab_orders')
                ->join('patients', 'lab_orders.patient_id', '=', 'patients.id')
                ->where('lab_orders.clinic_id', $clinicId)
                ->whereIn('lab_orders.status', $this->labOrderActiveStatuses())
                ->select(
                    'lab_orders.*',
                    'patients.name as patient_name',
                    'patients.phone as patient_phone',
                    'patients.dob as date_of_birth',
                    'patients.sex as gender'
                );

            $this->joinLabOrderDoctor($query);

            if (Schema::hasColumn('lab_orders', 'priority')) {
                $query->orderByRaw("FIELD(lab_orders.priority,'stat','urgent','routine')");
                Log::info('LabTechnicianController@dashboard: ordering by priority');
            }

            $pendingOrders = $query->orderBy('lab_orders.created_at')->get();

            $completedToday = DB::table('lab_orders')
                ->where('clinic_id', $clinicId)
                ->whereIn('status', ['completed', 'ready', 'sent'])
                ->whereDate('updated_at', today())
                ->count();

            $stats = [
                'pending' => $pendingOrders->whereIn('status', ['pending', 'ordered', 'new', 'accepted'])->count(),
                'sample_collected' => $pendingOrders->where('status', 'sample_collected')->count(),
                'processing' => $pendingOrders->where('status', 'processing')->count(),
                'completed_today' => $completedToday,
            ];

            Log::info('LabTechnicianController@dashboard loaded', ['rows' => $pendingOrders->count(), 'stats' => $stats]);
        } catch (\Throwable $e) {
            Log::error('LabTechnicianController@dashboard error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        }

        return view('lab.technician.dashboard', compact(
            'pendingOrders',
            'stats',
            'labOrderItemFk',
            'labOrderItemsHasAbnormal'
        ));
    }

    public function collectSample(Request $request, int $orderId)
    {
        $clinicId = auth()->user()->clinic_id;
        Log::info('LabTechnicianController@collectSample', ['order_id' => $orderId, 'clinic_id' => $clinicId]);

        $validated = $request->validate([
            'sample_type'      => 'required|string|max:100',
            'collection_notes' => 'nullable|string',
        ]);

        try {
            app(LabSampleCollectionService::class)->collectForOrder($orderId, $clinicId, $validated);
            Log::info('LabTechnicianController@collectSample delegated to LabSampleCollectionService', ['order_id' => $orderId]);
        } catch (\Throwable $e) {
            Log::error('LabTechnicianController@collectSample error', ['error' => $e->getMessage()]);

            return response()->json(['success' => false, 'message' => 'Could not record sample collection'], 500);
        }

        return response()->json(['success' => true, 'message' => 'Sample collected']);
    }

    public function resultForm(int $orderId)
    {
        $clinicId = auth()->user()->clinic_id;
        Log::info('LabTechnicianController@resultForm', ['order_id' => $orderId]);

        $order = DB::table('lab_orders')
            ->join('patients', 'lab_orders.patient_id', '=', 'patients.id')
            ->where('lab_orders.id', $orderId)
            ->where('lab_orders.clinic_id', $clinicId)
            ->select(
                'lab_orders.*',
                'patients.name as patient_name',
                'patients.dob as date_of_birth',
                'patients.sex as gender'
            );

        $this->joinLabOrderDoctor($order, true);

        $order = $order->firstOrFail();

        try {
            LabOrderLineItemSync::syncFromOrderJson($orderId, $clinicId);
        } catch (\Throwable $e) {
            Log::warning('LabTechnicianController@resultForm: line item sync failed', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);
        }

        $items = $this->buildLabOrderLineItemsForOrder($orderId);

        DB::table('lab_orders')->where('id', $orderId)
            ->where('status', 'sample_collected')
            ->update(['status' => 'processing', 'updated_at' => now()]);

        return view('lab.technician.result-form', compact('order', 'items'));
    }

    public function saveResults(Request $request, int $orderId)
    {
        $clinicId = auth()->user()->clinic_id;
        Log::info('LabTechnicianController@saveResults', ['order_id' => $orderId]);

        $validated = $request->validate([
            'results'               => 'required|array',
            'results.*.item_id'     => 'required|integer',
            'results.*.value'       => 'required|string|max:255',
            'results.*.is_abnormal' => 'nullable|boolean',
            'results.*.is_critical' => 'nullable|boolean',
            'results.*.remarks'     => 'nullable|string',
        ]);

        $itemCols = Schema::hasTable('lab_order_items') ? array_flip(Schema::getColumnListing('lab_order_items')) : [];
        $orderFk = $this->labOrderItemsOrderFk();

        try {
            DB::transaction(function () use ($validated, $orderId, $clinicId, $itemCols, $orderFk) {
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
                    if (isset($itemCols['is_critical'])) {
                        $update['is_critical'] = !empty($result['is_critical']);
                    }
                    if (isset($itemCols['remarks'])) {
                        $update['remarks'] = $result['remarks'] ?? null;
                    }
                    if (isset($itemCols['status'])) {
                        $update['status'] = 'completed';
                    }
                    if (isset($itemCols['updated_at'])) {
                        $update['updated_at'] = now();
                    }

                    if ($update === []) {
                        Log::warning('LabTechnicianController@saveResults: no updatable columns on lab_order_items');

                        continue;
                    }

                    $q = DB::table('lab_order_items')->where('id', $result['item_id']);
                    if ($orderFk) {
                        $q->where($orderFk, $orderId);
                    }
                    $q->update($update);
                }

                $orderUpdate = ['updated_at' => now()];
                $orderCols = array_flip(Schema::getColumnListing('lab_orders'));
                if (isset($orderCols['status'])) {
                    $orderUpdate['status'] = 'completed';
                }
                if (isset($orderCols['completed_at'])) {
                    $orderUpdate['completed_at'] = now();
                }

                DB::table('lab_orders')->where('id', $orderId)->where('clinic_id', $clinicId)->update($orderUpdate);
            });
        } catch (\Throwable $e) {
            Log::error('LabTechnicianController@saveResults error', ['error' => $e->getMessage()]);

            return back()->with('error', 'Failed to save results: '.$e->getMessage());
        }

        \App\Models\AuditLog::log(
            'lab_results_saved',
            "Lab results saved for order #{$orderId}",
            'lab_orders',
            $orderId
        );

        try {
            $order = DB::table('lab_orders')
                ->join('patients', 'lab_orders.patient_id', '=', 'patients.id')
                ->where('lab_orders.id', $orderId)
                ->select('patients.id as patient_id', 'patients.phone', 'patients.name as patient_name', 'lab_orders.order_number', 'lab_orders.clinic_id')
                ->first();

            if ($order && $order->phone) {
                $patientName = $order->patient_name ?? 'Patient';
                $orderRef = $order->order_number ?? ('LAB-'.$orderId);
                $clinicName = auth()->user()->clinic->name ?? 'ClinicOS';

                $patient = \App\Models\Patient::find($order->patient_id);
                $labOrder = \App\Models\LabOrder::find($orderId);

                if ($patient && $labOrder) {
                    app(\App\Services\WhatsAppService::class)->sendLabResults($patient, $labOrder);
                } else {
                    $message = "Dear {$patientName}, your lab results for order #{$orderRef} are ready. Please visit the hospital to collect your report or contact your doctor for details. — {$clinicName}";
                    app(\App\Services\WhatsAppService::class)->sendText($order->phone, $message);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to send WhatsApp lab notification', ['error' => $e->getMessage(), 'order_id' => $orderId]);
        }

        return redirect()->route('lab.technician.dashboard')->with('success', 'Results saved. Doctor has been notified.');
    }

    public function doctorResults(Request $request)
    {
        $clinicId = auth()->user()->clinic_id;
        $doctorId = auth()->id();
        Log::info('LabTechnicianController@doctorResults', ['doctor_id' => $doctorId]);

        $q = DB::table('lab_orders')
            ->join('patients', 'lab_orders.patient_id', '=', 'patients.id')
            ->where('lab_orders.clinic_id', $clinicId)
            ->whereIn('lab_orders.status', ['completed', 'ready', 'sent'])
            ->select('lab_orders.*', 'patients.name as patient_name', 'patients.phone');

        if (Schema::hasColumn('lab_orders', 'doctor_id')) {
            $q->where('lab_orders.doctor_id', $doctorId);
        } elseif (Schema::hasColumn('lab_orders', 'ordered_by')) {
            $q->where('lab_orders.ordered_by', $doctorId);
        }

        $orderBy = Schema::hasColumn('lab_orders', 'completed_at') ? 'lab_orders.completed_at' : 'lab_orders.updated_at';
        $completedOrders = $q->orderByDesc($orderBy)->paginate(20);

        $labOrderItemFk = Schema::hasTable('lab_order_items') ? $this->labOrderItemsOrderFk() : null;
        $labOrderItemsHasAbnormal = Schema::hasTable('lab_order_items')
            && Schema::hasColumn('lab_order_items', 'is_abnormal');
        Log::info('LabTechnicianController@doctorResults meta', [
            'labOrderItemFk' => $labOrderItemFk,
            'labOrderItemsHasAbnormal' => $labOrderItemsHasAbnormal,
            'rows' => $completedOrders->total(),
        ]);

        return view('lab.technician.doctor-results', compact(
            'completedOrders',
            'labOrderItemFk',
            'labOrderItemsHasAbnormal'
        ));
    }

    public function viewReport(int $orderId)
    {
        $clinicId = auth()->user()->clinic_id;
        Log::info('LabTechnicianController@viewReport', ['order_id' => $orderId]);

        $order = DB::table('lab_orders')
            ->join('patients', 'lab_orders.patient_id', '=', 'patients.id')
            ->where('lab_orders.id', $orderId)
            ->where('lab_orders.clinic_id', $clinicId)
            ->select(
                'lab_orders.*',
                'patients.name as patient_name',
                'patients.dob as date_of_birth',
                'patients.sex as gender',
                'patients.phone'
            );

        $this->joinLabOrderDoctor($order);

        // joinLabOrderDoctor already selects doctors.name as doctor_name — do not add twice (MySQL duplicate alias errors).
        $order = $order->firstOrFail();

        $items = $this->buildLabOrderLineItemsForOrder($orderId);

        $clinicName = auth()->user()->clinic->name ?? config('app.name');

        return view('lab.technician.report', compact('order', 'items', 'clinicName'));
    }

    private function labOrderActiveStatuses(): array
    {
        return [
            'pending', 'ordered', 'new', 'accepted',
            'sample_collected', 'processing',
        ];
    }

    private function joinLabOrderDoctor($query, bool $withDoctorId = false): void
    {
        if (Schema::hasColumn('lab_orders', 'doctor_id')) {
            $query->leftJoin('users as doctors', 'lab_orders.doctor_id', '=', 'doctors.id');
        } elseif (Schema::hasColumn('lab_orders', 'ordered_by')) {
            $query->leftJoin('users as doctors', 'lab_orders.ordered_by', '=', 'doctors.id');
        } elseif (Schema::hasColumn('lab_orders', 'created_by')) {
            $query->leftJoin('users as doctors', 'lab_orders.created_by', '=', 'doctors.id');
        } else {
            $query->addSelect(DB::raw('NULL as doctor_name'));
            if ($withDoctorId) {
                $query->addSelect(DB::raw('NULL as doctor_id'));
            }

            return;
        }
        $query->addSelect('doctors.name as doctor_name');
        if ($withDoctorId) {
            $query->addSelect('doctors.id as doctor_id');
        }
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
        $cols = Schema::getColumnListing('lab_order_items');

        if (in_array('lab_test_catalog_id', $cols, true)) {
            return 'lab_test_catalog_id';
        }
        if (in_array('test_id', $cols, true)) {
            return 'test_id';
        }

        return null;
    }

    private function buildLabOrderLineItemsForOrder(int $orderId): Collection
    {
        if (!Schema::hasTable('lab_order_items') || !Schema::hasTable('lab_tests_catalog')) {
            Log::warning('LabTechnicianController: lab_order_items or lab_tests_catalog missing');

            return collect();
        }

        $orderFk = $this->labOrderItemsOrderFk();
        $testFk = $this->labOrderItemsTestFk();
        if (!$orderFk || !$testFk) {
            Log::warning('LabTechnicianController: could not resolve lab_order_items FK columns');

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
            $select[] = DB::raw('NULL as category');
        }

        return DB::table('lab_order_items')
            ->join('lab_tests_catalog', "lab_order_items.{$testFk}", '=', 'lab_tests_catalog.id')
            ->where("lab_order_items.{$orderFk}", $orderId)
            ->select($select)
            ->get();
    }
}
