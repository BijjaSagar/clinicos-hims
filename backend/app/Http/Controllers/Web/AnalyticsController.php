<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class AnalyticsController extends Controller
{
    public function dashboard()
    {
        $clinicId = auth()->user()->clinic_id;
        Log::info('AnalyticsController@dashboard', ['clinic_id' => $clinicId]);

        $data = [
            'revenue' => ['daily' => [], 'total_month' => 0, 'total_today' => 0],
            'patients' => ['total' => 0, 'new_this_month' => 0, 'gender_dist' => []],
            'appointments' => ['today' => 0, 'this_month' => 0, 'trend' => []],
            'top_diagnoses' => [],
            'doctor_performance' => [],
        ];

        try {
            if (Schema::hasTable('patients')) {
                $data['patients']['total'] = DB::table('patients')->where('clinic_id', $clinicId)->count();
                $data['patients']['new_this_month'] = DB::table('patients')->where('clinic_id', $clinicId)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
                $data['patients']['gender_dist'] = DB::table('patients')->where('clinic_id', $clinicId)->select('gender', DB::raw('count(*) as count'))->groupBy('gender')->pluck('count', 'gender')->toArray();
            }

            if (Schema::hasTable('appointments')) {
                $data['appointments']['today'] = DB::table('appointments')->where('clinic_id', $clinicId)->whereDate('appointment_date', today())->count();
                $data['appointments']['this_month'] = DB::table('appointments')->where('clinic_id', $clinicId)->whereMonth('appointment_date', now()->month)->count();

                $data['appointments']['trend'] = DB::table('appointments')->where('clinic_id', $clinicId)->where('appointment_date', '>=', now()->subDays(30))->select(DB::raw('DATE(appointment_date) as date'), DB::raw('count(*) as count'))->groupBy('date')->orderBy('date')->get()->toArray();
            }

            if (Schema::hasTable('invoices')) {
                $data['revenue']['total_month'] = DB::table('invoices')->where('clinic_id', $clinicId)->whereMonth('created_at', now()->month)->sum('total_amount') ?? 0;
                $data['revenue']['total_today'] = DB::table('invoices')->where('clinic_id', $clinicId)->whereDate('created_at', today())->sum('total_amount') ?? 0;

                $data['revenue']['daily'] = DB::table('invoices')->where('clinic_id', $clinicId)->where('created_at', '>=', now()->subDays(30))->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as total'))->groupBy('date')->orderBy('date')->get()->toArray();
            }

            if (Schema::hasTable('visits')) {
                $data['top_diagnoses'] = DB::table('visits')->where('clinic_id', $clinicId)->whereNotNull('diagnosis_text')->whereMonth('created_at', now()->month)->select('diagnosis_text', DB::raw('count(*) as count'))->groupBy('diagnosis_text')->orderByDesc('count')->limit(10)->get()->toArray();
            }

            if (Schema::hasTable('appointments') && Schema::hasTable('users')) {
                $data['doctor_performance'] = DB::table('appointments')->join('users', 'appointments.doctor_id', '=', 'users.id')->where('appointments.clinic_id', $clinicId)->whereMonth('appointments.appointment_date', now()->month)->select('users.name as doctor', DB::raw('count(*) as patients_seen'))->groupBy('users.name')->orderByDesc('patients_seen')->limit(10)->get()->toArray();
            }

            Log::info('Analytics dashboard loaded', ['patients' => $data['patients']['total']]);
        } catch (\Throwable $e) {
            Log::error('AnalyticsController@dashboard error', ['error' => $e->getMessage()]);
        }

        return view('analytics.dashboard', compact('data'));
    }

    public function prescriptionAnalytics()
    {
        $clinicId = auth()->user()->clinic_id;
        Log::info('AnalyticsController@prescriptionAnalytics', ['clinic_id' => $clinicId]);

        $data = ['top_drugs' => [], 'avg_per_rx' => 0, 'total_rx' => 0, 'antibiotic_rate' => 0, 'trend' => []];

        try {
            if (Schema::hasTable('prescription_drugs') && Schema::hasTable('prescriptions')) {
                $data['top_drugs'] = DB::table('prescription_drugs')->join('prescriptions', 'prescription_drugs.prescription_id', '=', 'prescriptions.id')->where('prescriptions.clinic_id', $clinicId)->select('prescription_drugs.drug_name', DB::raw('count(*) as count'))->groupBy('prescription_drugs.drug_name')->orderByDesc('count')->limit(20)->get()->toArray();

                $data['total_rx'] = DB::table('prescriptions')->where('clinic_id', $clinicId)->whereMonth('created_at', now()->month)->count();

                $totalDrugs = DB::table('prescription_drugs')->join('prescriptions', 'prescription_drugs.prescription_id', '=', 'prescriptions.id')->where('prescriptions.clinic_id', $clinicId)->whereMonth('prescriptions.created_at', now()->month)->count();

                $data['avg_per_rx'] = $data['total_rx'] > 0 ? round($totalDrugs / $data['total_rx'], 1) : 0;

                $data['trend'] = DB::table('prescriptions')->where('clinic_id', $clinicId)->where('created_at', '>=', now()->subDays(30))->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))->groupBy('date')->orderBy('date')->get()->toArray();
            }

            Log::info('Prescription analytics loaded', ['top_drugs_count' => count($data['top_drugs'])]);
        } catch (\Throwable $e) {
            Log::error('AnalyticsController@prescriptionAnalytics error', ['error' => $e->getMessage()]);
        }

        return view('analytics.prescriptions', compact('data'));
    }

    public function revenueReport(Request $request)
    {
        $clinicId = auth()->user()->clinic_id;
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to = $request->get('to', now()->toDateString());
        Log::info('AnalyticsController@revenueReport', ['clinic_id' => $clinicId, 'from' => $from, 'to' => $to]);

        $data = ['daily' => [], 'by_payment' => [], 'by_doctor' => [], 'total' => 0, 'from' => $from, 'to' => $to];

        try {
            if (Schema::hasTable('invoices')) {
                $data['total'] = DB::table('invoices')->where('clinic_id', $clinicId)->whereBetween('created_at', [$from, $to . ' 23:59:59'])->sum('total_amount') ?? 0;

                $data['daily'] = DB::table('invoices')->where('clinic_id', $clinicId)->whereBetween('created_at', [$from, $to . ' 23:59:59'])->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as total'), DB::raw('count(*) as count'))->groupBy('date')->orderBy('date')->get()->toArray();

                $data['by_payment'] = DB::table('invoices')->where('clinic_id', $clinicId)->whereBetween('created_at', [$from, $to . ' 23:59:59'])->select('payment_method', DB::raw('SUM(total_amount) as total'), DB::raw('count(*) as count'))->groupBy('payment_method')->get()->toArray();
            }

            Log::info('Revenue report loaded', ['total' => $data['total']]);
        } catch (\Throwable $e) {
            Log::error('AnalyticsController@revenueReport error', ['error' => $e->getMessage()]);
        }

        return view('analytics.revenue', compact('data'));
    }

    public function patientReport(Request $request)
    {
        $clinicId = auth()->user()->clinic_id;
        Log::info('AnalyticsController@patientReport', ['clinic_id' => $clinicId]);

        $data = ['total' => 0, 'new_today' => 0, 'new_this_month' => 0, 'age_dist' => [], 'top_visitors' => []];

        try {
            if (Schema::hasTable('patients')) {
                $data['total'] = DB::table('patients')->where('clinic_id', $clinicId)->count();
                $data['new_today'] = DB::table('patients')->where('clinic_id', $clinicId)->whereDate('created_at', today())->count();
                $data['new_this_month'] = DB::table('patients')->where('clinic_id', $clinicId)->whereMonth('created_at', now()->month)->count();
            }

            if (Schema::hasTable('appointments') && Schema::hasTable('patients')) {
                $data['top_visitors'] = DB::table('appointments')->join('patients', 'appointments.patient_id', '=', 'patients.id')->where('appointments.clinic_id', $clinicId)->select('patients.name', 'patients.phone', DB::raw('count(*) as visits'))->groupBy('patients.name', 'patients.phone')->orderByDesc('visits')->limit(15)->get()->toArray();
            }

            Log::info('Patient report loaded', ['total' => $data['total']]);
        } catch (\Throwable $e) {
            Log::error('AnalyticsController@patientReport error', ['error' => $e->getMessage()]);
        }

        return view('analytics.patients', compact('data'));
    }
}
