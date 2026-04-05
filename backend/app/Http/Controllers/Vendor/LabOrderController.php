<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\LabOrder;
use App\Models\LabOrderTest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class LabOrderController extends Controller
{
    /**
     * GET /lab-orders
     */
    public function index(Request $request): JsonResponse
    {
        Log::info('LabOrderController.index: Listing lab orders');
        $clinicId = auth()->user()->clinic_id;

        $query = LabOrder::with(['patient:id,name,phone', 'doctor:id,name', 'labOrderTests'])
            ->where('clinic_id', $clinicId);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->integer('patient_id'));
        }

        $orders = $query->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 20));

        Log::info('LabOrderController.index: Orders fetched', ['count' => $orders->total()]);

        return response()->json([
            'data'    => $orders->items(),
            'message' => 'Lab orders retrieved',
            'meta'    => [
                'total'        => $orders->total(),
                'per_page'     => $orders->perPage(),
                'current_page' => $orders->currentPage(),
                'last_page'    => $orders->lastPage(),
            ],
        ]);
    }

    /**
     * POST /lab-orders
     */
    public function store(Request $request): JsonResponse
    {
        Log::info('LabOrderController.store: Creating lab order');
        $clinicId = auth()->user()->clinic_id;

        $validated = $request->validate([
            'patient_id'    => "required|integer|exists:patients,id",
            'doctor_id'     => 'required|integer|exists:users,id',
            'visit_id'      => 'nullable|integer|exists:visits,id',
            'vendor_lab_id' => 'nullable|integer|exists:vendor_labs,id',
            'priority'      => 'nullable|in:routine,urgent,stat',
            'notes'         => 'nullable|string|max:1000',
            'tests'         => 'required|array|min:1',
            'tests.*.test_name'   => 'required|string|max:200',
            'tests.*.test_code'   => 'nullable|string|max:50',
            'tests.*.sample_type' => 'nullable|string|max:50',
        ]);

        $order = DB::transaction(function () use ($validated, $clinicId) {
            $priority = $validated['priority'] ?? 'routine';
            $isUrgent = in_array($priority, ['urgent', 'stat'], true);

            $order = LabOrder::create([
                'clinic_id'      => $clinicId,
                'patient_id'     => $validated['patient_id'],
                'doctor_id'      => $validated['doctor_id'],
                'visit_id'       => $validated['visit_id'] ?? null,
                'vendor_id'      => $validated['vendor_lab_id'] ?? null,
                'is_urgent'      => $isUrgent,
                'clinical_notes' => $validated['notes'] ?? null,
                'status'         => LabOrder::STATUS_NEW,
            ]);

            foreach ($validated['tests'] as $test) {
                $order->labOrderTests()->create([
                    'test_name' => $test['test_name'],
                    'test_code' => $test['test_code'] ?? null,
                    'is_urgent' => $isUrgent,
                ]);
            }

            Log::info('LabOrderController.store: line items created', [
                'order_id' => $order->id,
                'line_count' => count($validated['tests']),
            ]);

            return $order;
        });

        Log::info('LabOrderController.store: Lab order created', ['order_id' => $order->id]);

        return response()->json([
            'data'    => $order->load('labOrderTests'),
            'message' => 'Lab order created',
            'meta'    => [],
        ], 201);
    }

    /**
     * GET /lab-orders/{id}
     */
    public function show(int $id): JsonResponse
    {
        Log::info('LabOrderController.show: Fetching lab order', ['id' => $id]);
        $clinicId = auth()->user()->clinic_id;

        $order = LabOrder::with(['patient', 'doctor', 'labOrderTests', 'vendor'])
            ->where('clinic_id', $clinicId)
            ->findOrFail($id);

        Log::info('LabOrderController.show: Order fetched', ['id' => $id, 'status' => $order->status]);

        return response()->json([
            'data'    => $order,
            'message' => 'Lab order retrieved',
            'meta'    => [],
        ]);
    }

    /**
     * PUT /lab-orders/{id}/status
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        Log::info('LabOrderController.updateStatus', ['id' => $id]);
        $clinicId = auth()->user()->clinic_id;

        $validated = $request->validate([
            'status' => 'required|in:new,accepted,sample_collected,processing,ready,sent,cancelled,completed',
        ]);

        $order = LabOrder::where('clinic_id', $clinicId)->findOrFail($id);
        $status = $validated['status'] === 'completed' ? LabOrder::STATUS_SENT : $validated['status'];
        $order->update(['status' => $status]);

        Log::info('LabOrderController.updateStatus: Status updated', ['id' => $id, 'requested' => $validated['status'], 'stored' => $status]);

        return response()->json([
            'data'    => $order->fresh(),
            'message' => 'Lab order status updated',
            'meta'    => [],
        ]);
    }

    /**
     * POST /lab-orders/{id}/result
     */
    public function uploadResult(Request $request, int $id): JsonResponse
    {
        Log::info('LabOrderController.uploadResult', ['id' => $id]);
        $clinicId = auth()->user()->clinic_id;

        $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $order = LabOrder::where('clinic_id', $clinicId)->findOrFail($id);

        $path = $request->file('file')->store(
            "clinics/{$clinicId}/lab-results/{$id}",
            's3'
        );

        $order->update([
            'result_pdf_s3_key' => $path,
            'result_pdf_url' => null,
            'status' => LabOrder::STATUS_READY,
        ]);

        Log::info('LabOrderController.uploadResult: Result uploaded', ['id' => $id, 'path' => $path]);

        return response()->json([
            'data'    => $order->fresh(),
            'message' => 'Lab result uploaded',
            'meta'    => ['file_path' => $path],
        ]);
    }

    /**
     * POST /lab-orders/{id}/send
     */
    public function sendResult(int $id): JsonResponse
    {
        Log::info('LabOrderController.sendResult', ['id' => $id]);
        $clinicId = auth()->user()->clinic_id;

        $order = LabOrder::with('patient')
            ->where('clinic_id', $clinicId)
            ->findOrFail($id);

        if (!$order->result_pdf_s3_key && !$order->result_pdf_url) {
            return response()->json([
                'data'    => null,
                'message' => 'No result file uploaded yet',
                'meta'    => [],
            ], 422);
        }

        $order->update(['result_sent_at' => now()]);

        Log::info('LabOrderController.sendResult: Result sent notification', ['id' => $id]);

        return response()->json([
            'data'    => $order->fresh(),
            'message' => 'Lab result sent to clinic and patient',
            'meta'    => [],
        ]);
    }
}
