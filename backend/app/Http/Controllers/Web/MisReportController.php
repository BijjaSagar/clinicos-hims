<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class MisReportController extends Controller
{
    public function index()
    {
        Log::info('MisReportController@index', ['user' => auth()->id()]);
        return view('reports.index');
    }

    public function dailySummary(Request $request)
    {
        $clinicId = auth()->user()->clinic_id;
        $date = $request->get('date', today()->toDateString());
        Log::info('MisReportController@dailySummary', ['clinic_id' => $clinicId, 'date' => $date]);

        $summary = [
            'date' => $date,
            'patients_seen' => 0,
            'new_registrations' => 0,
            'appointments_total' => 0,
            'appointments_completed' => 0,
            'appointments_cancelled' => 0,
            'revenue' => 0,
            'prescriptions' => 0,
            'lab_orders' => 0,
        ];

        try {
            if (Schema::hasTable('appointments')) {
                $summary['appointments_total'] = DB::table('appointments')->where('clinic_id', $clinicId)->whereDate('appointment_date', $date)->count();
                $summary['appointments_completed'] = DB::table('appointments')->where('clinic_id', $clinicId)->whereDate('appointment_date', $date)->where('status', 'completed')->count();
                $summary['appointments_cancelled'] = DB::table('appointments')->where('clinic_id', $clinicId)->whereDate('appointment_date', $date)->where('status', 'cancelled')->count();
                $summary['patients_seen'] = DB::table('appointments')->where('clinic_id', $clinicId)->whereDate('appointment_date', $date)->whereIn('status', ['completed', 'in_progress'])->distinct('patient_id')->count('patient_id');
            }

            if (Schema::hasTable('patients')) {
                $summary['new_registrations'] = DB::table('patients')->where('clinic_id', $clinicId)->whereDate('created_at', $date)->count();
            }

            if (Schema::hasTable('invoices')) {
                $summary['revenue'] = DB::table('invoices')->where('clinic_id', $clinicId)->whereDate('created_at', $date)->sum('total_amount') ?? 0;
            }

            if (Schema::hasTable('prescriptions')) {
                $summary['prescriptions'] = DB::table('prescriptions')->where('clinic_id', $clinicId)->whereDate('created_at', $date)->count();
            }

            if (Schema::hasTable('lab_orders')) {
                $summary['lab_orders'] = DB::table('lab_orders')->where('clinic_id', $clinicId)->whereDate('created_at', $date)->count();
            }

            Log::info('Daily summary loaded', $summary);
        } catch (\Throwable $e) {
            Log::error('MisReportController@dailySummary error', ['error' => $e->getMessage()]);
        }

        return view('reports.daily-summary', compact('summary'));
    }

    public function monthlyMIS(Request $request)
    {
        $clinicId = auth()->user()->clinic_id;
        $month = $request->get('month', now()->format('Y-m'));
        Log::info('MisReportController@monthlyMIS', ['clinic_id' => $clinicId, 'month' => $month]);

        [$year, $mon] = explode('-', $month);

        $mis = [
            'month' => $month,
            'total_patients' => 0,
            'new_patients' => 0,
            'total_appointments' => 0,
            'completed_appointments' => 0,
            'no_show_rate' => 0,
            'total_revenue' => 0,
            'total_prescriptions' => 0,
            'total_lab_orders' => 0,
            'daily_breakdown' => [],
            'doctor_summary' => [],
        ];

        try {
            if (Schema::hasTable('appointments')) {
                $mis['total_appointments'] = DB::table('appointments')->where('clinic_id', $clinicId)->whereYear('appointment_date', $year)->whereMonth('appointment_date', $mon)->count();
                $mis['completed_appointments'] = DB::table('appointments')->where('clinic_id', $clinicId)->whereYear('appointment_date', $year)->whereMonth('appointment_date', $mon)->where('status', 'completed')->count();
                $noShows = DB::table('appointments')->where('clinic_id', $clinicId)->whereYear('appointment_date', $year)->whereMonth('appointment_date', $mon)->where('status', 'no_show')->count();
                $mis['no_show_rate'] = $mis['total_appointments'] > 0 ? round(($noShows / $mis['total_appointments']) * 100, 1) : 0;

                $mis['total_patients'] = DB::table('appointments')->where('clinic_id', $clinicId)->whereYear('appointment_date', $year)->whereMonth('appointment_date', $mon)->distinct('patient_id')->count('patient_id');
            }

            if (Schema::hasTable('patients')) {
                $mis['new_patients'] = DB::table('patients')->where('clinic_id', $clinicId)->whereYear('created_at', $year)->whereMonth('created_at', $mon)->count();
            }

            if (Schema::hasTable('invoices')) {
                $mis['total_revenue'] = DB::table('invoices')->where('clinic_id', $clinicId)->whereYear('created_at', $year)->whereMonth('created_at', $mon)->sum('total_amount') ?? 0;

                $mis['daily_breakdown'] = DB::table('invoices')->where('clinic_id', $clinicId)->whereYear('created_at', $year)->whereMonth('created_at', $mon)->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as revenue'), DB::raw('count(*) as invoices'))->groupBy('date')->orderBy('date')->get()->toArray();
            }

            if (Schema::hasTable('prescriptions')) {
                $mis['total_prescriptions'] = DB::table('prescriptions')->where('clinic_id', $clinicId)->whereYear('created_at', $year)->whereMonth('created_at', $mon)->count();
            }

            if (Schema::hasTable('lab_orders')) {
                $mis['total_lab_orders'] = DB::table('lab_orders')->where('clinic_id', $clinicId)->whereYear('created_at', $year)->whereMonth('created_at', $mon)->count();
            }

            Log::info('Monthly MIS loaded', ['revenue' => $mis['total_revenue'], 'patients' => $mis['total_patients']]);
        } catch (\Throwable $e) {
            Log::error('MisReportController@monthlyMIS error', ['error' => $e->getMessage()]);
        }

        return view('reports.monthly-mis', compact('mis'));
    }

    public function exportCsv(Request $request)
    {
        $clinicId = auth()->user()->clinic_id;
        $type = $request->get('type', 'daily_summary');
        $date = $request->get('date', today()->toDateString());
        Log::info('MisReportController@exportCsv', ['type' => $type, 'date' => $date, 'clinic_id' => $clinicId]);

        try {
            $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename=\"{$type}_{$date}.csv\""];
            $callback = function () use ($type, $clinicId, $date) {
                $file = fopen('php://output', 'w');

                if ($type === 'patient_register') {
                    fputcsv($file, ['ID', 'Name', 'Phone', 'Gender', 'Age', 'Registered On']);
                    if (Schema::hasTable('patients')) {
                        DB::table('patients')->where('clinic_id', $clinicId)->orderBy('name')->chunk(200, function ($patients) use ($file) {
                            foreach ($patients as $p) {
                                fputcsv($file, [$p->id, $p->name, $p->phone ?? '', $p->gender ?? '', $p->age_years ?? '', $p->created_at]);
                            }
                        });
                    }
                } elseif ($type === 'appointment_register') {
                    fputcsv($file, ['Date', 'Patient', 'Doctor', 'Status', 'Type']);
                    if (Schema::hasTable('appointments')) {
                        DB::table('appointments')->where('clinic_id', $clinicId)->whereDate('appointment_date', $date)->orderBy('appointment_date')->chunk(200, function ($appts) use ($file) {
                            foreach ($appts as $a) {
                                fputcsv($file, [$a->appointment_date, $a->patient_id, $a->doctor_id ?? '', $a->status ?? '', $a->type ?? '']);
                            }
                        });
                    }
                } else {
                    fputcsv($file, ['Date', 'Metric', 'Value']);
                    fputcsv($file, [$date, 'Report Type', $type]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Throwable $e) {
            Log::error('MisReportController@exportCsv error', ['error' => $e->getMessage()]);
            return back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }

    public function generateReport(Request $request)
    {
        $type = $request->input('type', 'daily_summary');
        Log::info('MisReportController@generateReport', ['type' => $type]);

        return match ($type) {
            'daily_summary' => $this->dailySummary($request),
            'monthly_mis' => $this->monthlyMIS($request),
            default => redirect()->route('reports.index')->with('error', 'Unknown report type'),
        };
    }
}
