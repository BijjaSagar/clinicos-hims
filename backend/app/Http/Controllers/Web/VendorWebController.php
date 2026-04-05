<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\LabOrder;
use App\Models\VendorLab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class VendorWebController extends Controller
{
    public function index(Request $request)
    {
        Log::info('VendorWebController@index', ['user' => auth()->id()]);

        $orders = collect();
        $stats = ['new_today' => 0, 'processing' => 0, 'ready' => 0, 'total_month' => 0];
        $partnerClinics = collect();

        try {
            if (!Schema::hasTable('lab_orders')) {
                Log::warning('VendorWebController: lab_orders table missing');
                $vendorCanLoadTests = false;

                return view('vendor.index', compact('orders', 'stats', 'partnerClinics', 'vendorCanLoadTests'));
            }

            $with = ['patient', 'clinic'];
            if (Schema::hasTable('lab_order_tests')) {
                $with[] = 'labOrderTests';
                Log::info('VendorWebController@index: eager-loading labOrderTests relation');
            } else {
                Log::warning('VendorWebController@index: lab_order_tests table missing, skipping labOrderTests eager load');
            }

            $query = LabOrder::with($with)->latest();

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $orders = $query->paginate(20);

            $stats = [
                'new_today' => LabOrder::whereDate('created_at', today())
                    ->where('status', 'new')->count(),
                'processing' => LabOrder::whereIn('status', ['accepted', 'sample_collected', 'processing'])->count(),
                'ready' => LabOrder::where('status', 'ready')->count(),
                'total_month' => LabOrder::whereMonth('created_at', now()->month)->count(),
            ];

            try {
                if (Schema::hasTable('vendor_labs') && Schema::hasTable('clinic_vendor_links')) {
                    $partnerClinics = VendorLab::with('clinics')
                        ->limit(5)
                        ->get();
                    Log::info('VendorWebController@index partner clinics loaded', ['count' => $partnerClinics->count()]);
                } else {
                    Log::info('VendorWebController@index skipping partner clinics (missing vendor_labs or clinic_vendor_links)');
                }
            } catch (\Throwable $e) {
                Log::warning('Could not load partner clinics', ['error' => $e->getMessage()]);
            }

            Log::info('VendorWebController@index success', ['orders_count' => $orders->count()]);
        } catch (\Throwable $e) {
            Log::error('VendorWebController@index error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        }

        $vendorCanLoadTests = Schema::hasTable('lab_order_tests');

        return view('vendor.index', compact('orders', 'stats', 'partnerClinics', 'vendorCanLoadTests'));
    }

    public function acceptOrder(LabOrder $order)
    {
        Log::info('VendorWebController@acceptOrder', ['order' => $order->id]);

        $order->update([
            'status' => 'processing',
            'accepted_at' => now(),
        ]);

        return back()->with('success', 'Order accepted successfully');
    }

    public function uploadResult(Request $request, LabOrder $order)
    {
        Log::info('VendorWebController@uploadResult', ['order' => $order->id]);

        $validated = $request->validate([
            'result_file' => 'required|file|mimes:pdf|max:10240',
            'notes' => 'nullable|string|max:500',
        ]);

        // Store file and update order
        if ($request->hasFile('result_file')) {
            $path = $request->file('result_file')->store('lab-results', 'public');
            $order->update([
                'result_file' => $path,
                'result_notes' => $validated['notes'] ?? null,
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        }

        return back()->with('success', 'Result uploaded successfully');
    }
}
