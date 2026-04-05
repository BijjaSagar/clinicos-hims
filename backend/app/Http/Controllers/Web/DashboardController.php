<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\WhatsappMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        Log::info('DashboardController@index called');

        $user = auth()->user();
        $clinicId = $user->clinic_id ?? null;
        $today = today();

        $empty = [
            'appointments' => collect(),
            'stats' => $this->emptyStats(),
            'whatsapp' => collect(),
            'queue' => collect(),
            'invoices' => collect(),
            'visitsByType' => collect(),
            'weekChartLabels' => [],
            'weekChartData' => [],
            'weekRevenue' => $this->emptyWeekRevenue(),
            'suggestions' => [],
        ];

        if (! $clinicId) {
            Log::warning('User has no clinic_id', ['user_id' => $user->id]);

            return view('dashboard.index', $empty);
        }

        try {
            $todayAppointments = Appointment::with(['patient'])
                ->where('clinic_id', $clinicId)
                ->whereDate('scheduled_at', $today)
                ->orderBy('scheduled_at')
                ->get();

            Log::info('DashboardController: today appointments loaded', [
                'count' => $todayAppointments->count(),
                'ids' => $todayAppointments->pluck('id')->all(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Error loading appointments', ['error' => $e->getMessage()]);
            $todayAppointments = collect();
        }

        $seenStatuses = ['completed', 'in_consultation', 'checked_in'];
        $yesterday = $today->copy()->subDay();

        try {
            $todayPatients = $todayAppointments->whereIn('status', $seenStatuses)->count();
            $yesterdayPatients = Appointment::where('clinic_id', $clinicId)
                ->whereDate('scheduled_at', $yesterday)
                ->whereIn('status', $seenStatuses)
                ->count();

            $todayRevenue = (float) (Invoice::where('clinic_id', $clinicId)
                ->whereDate('created_at', $today)
                ->where('payment_status', Invoice::STATUS_PAID)
                ->sum('total') ?? 0);

            $yesterdayRevenue = (float) (Invoice::where('clinic_id', $clinicId)
                ->whereDate('created_at', $yesterday)
                ->where('payment_status', Invoice::STATUS_PAID)
                ->sum('total') ?? 0);

            $pendingDues = (float) (Invoice::where('clinic_id', $clinicId)
                ->whereIn('payment_status', [Invoice::STATUS_PENDING, Invoice::STATUS_PARTIAL])
                ->selectRaw('COALESCE(SUM(total - paid), 0) as s')
                ->value('s') ?? 0);

            $pendingInvoiceCount = Invoice::where('clinic_id', $clinicId)
                ->whereIn('payment_status', [Invoice::STATUS_PENDING, Invoice::STATUS_PARTIAL])
                ->whereRaw('total > paid')
                ->count();

            $queueCount = $todayAppointments->whereIn('status', [
                Appointment::STATUS_CHECKED_IN,
                Appointment::STATUS_CONFIRMED,
                Appointment::STATUS_BOOKED,
            ])->count();

            $waitingCount = $todayAppointments->where('status', Appointment::STATUS_CHECKED_IN)->count();

            $abdmRecords = 0;
            if (Schema::hasTable('abdm_care_contexts')) {
                $abdmRecords = (int) DB::table('abdm_care_contexts')
                    ->where('clinic_id', $clinicId)
                    ->whereMonth('pushed_at', now()->month)
                    ->whereYear('pushed_at', now()->year)
                    ->whereNotNull('pushed_at')
                    ->count();
            }

            $active = $todayAppointments->firstWhere('status', Appointment::STATUS_IN_CONSULTATION)
                ?? $todayAppointments->where('status', Appointment::STATUS_CHECKED_IN)->sortBy('token_number')->first();

            $stats = [
                'today_patients' => $todayPatients,
                'yesterday_patients' => $yesterdayPatients,
                'patients_delta' => $todayPatients - $yesterdayPatients,
                'revenue' => $todayRevenue,
                'yesterday_revenue' => $yesterdayRevenue,
                'revenue_delta_pct' => $yesterdayRevenue > 0
                    ? round((($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100)
                    : null,
                'pending_dues' => $pendingDues,
                'pending_invoice_count' => $pendingInvoiceCount,
                'queue_count' => $queueCount,
                'waiting_count' => $waitingCount,
                'month_revenue' => (float) (Invoice::where('clinic_id', $clinicId)
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->where('payment_status', Invoice::STATUS_PAID)
                    ->sum('total') ?? 0),
                'current_token' => $active?->token_number,
                'current_patient' => $active?->patient?->name,
                'abdm_records' => $abdmRecords,
            ];

            Log::info('Dashboard stats calculated', ['clinic_id' => $clinicId, 'stats' => $stats]);
        } catch (\Throwable $e) {
            Log::error('Error calculating stats', ['error' => $e->getMessage()]);
            $stats = $this->emptyStats();
        }

        $appointments = $todayAppointments->map(fn (Appointment $apt) => $this->mapAppointmentForDashboard($apt))->values();

        try {
            $whatsappMessages = WhatsappMessage::where('clinic_id', $clinicId)
                ->with('patient')
                ->orderByDesc('sent_at')
                ->orderByDesc('created_at')
                ->limit(5)
                ->get();
            $whatsapp = $whatsappMessages->map(fn (WhatsappMessage $m) => $this->mapWhatsappForDashboard($m));
        } catch (\Throwable $e) {
            Log::error('Error loading WhatsApp messages', ['error' => $e->getMessage()]);
            $whatsapp = collect();
        }

        try {
            $queue = $this->buildQueueFromAppointments($todayAppointments);
        } catch (\Throwable $e) {
            Log::error('Error building queue', ['error' => $e->getMessage()]);
            $queue = collect();
        }

        try {
            $invoices = Invoice::with(['patient', 'items', 'payments'])
                ->where('clinic_id', $clinicId)
                ->orderByDesc('created_at')
                ->limit(5)
                ->get()
                ->map(fn (Invoice $inv) => $this->mapInvoiceForDashboard($inv));
        } catch (\Throwable $e) {
            Log::error('Error loading recent invoices', ['error' => $e->getMessage()]);
            $invoices = collect();
        }

        try {
            $visitsByType = $this->buildVisitsByType($clinicId);
        } catch (\Throwable $e) {
            Log::error('Error visits by type', ['error' => $e->getMessage()]);
            $visitsByType = collect();
        }

        try {
            $week = $this->buildWeekRevenueChart($clinicId);
            $weekChartLabels = $week['labels'];
            $weekChartData = $week['amounts'];
            $weekRevenue = $week['summary'];
        } catch (\Throwable $e) {
            Log::error('Error week revenue', ['error' => $e->getMessage()]);
            $weekChartLabels = [];
            $weekChartData = [];
            $weekRevenue = $this->emptyWeekRevenue();
        }

        $suggestions = [];

        Log::info('DashboardController@index ready', [
            'clinic_id' => $clinicId,
            'queue_rows' => $queue->count(),
            'invoices' => $invoices->count(),
        ]);

        return view('dashboard.index', compact(
            'appointments',
            'stats',
            'whatsapp',
            'queue',
            'invoices',
            'visitsByType',
            'weekChartLabels',
            'weekChartData',
            'weekRevenue',
            'suggestions'
        ));
    }

    /**
     * @return array<string, mixed>
     */
    private function emptyStats(): array
    {
        return [
            'today_patients' => 0,
            'yesterday_patients' => 0,
            'patients_delta' => 0,
            'revenue' => 0,
            'yesterday_revenue' => 0,
            'revenue_delta_pct' => null,
            'pending_dues' => 0,
            'pending_invoice_count' => 0,
            'queue_count' => 0,
            'waiting_count' => 0,
            'month_revenue' => 0,
            'current_token' => null,
            'current_patient' => null,
            'abdm_records' => 0,
        ];
    }

    /**
     * @return array{total: float, collected: float, pending: float, gst: float}
     */
    private function emptyWeekRevenue(): array
    {
        return [
            'total' => 0.0,
            'collected' => 0.0,
            'pending' => 0.0,
            'gst' => 0.0,
        ];
    }

    /**
     * @return \Illuminate\Support\Collection<int, array{num: int|string, name: string, type: string, wait: string, dim?: bool}>
     */
    private function buildQueueFromAppointments(\Illuminate\Support\Collection $todayAppointments)
    {
        $ordered = $todayAppointments->filter(fn (Appointment $a) => in_array($a->status, [
            Appointment::STATUS_IN_CONSULTATION,
            Appointment::STATUS_CHECKED_IN,
            Appointment::STATUS_CONFIRMED,
            Appointment::STATUS_BOOKED,
        ], true))->sortBy(fn (Appointment $a) => $a->token_number ?? 9999)->values();

        return $ordered->map(function (Appointment $a) {
            $name = $a->patient?->name ?? 'Patient';
            $type = ucfirst(str_replace('_', ' ', $a->appointment_type ?? 'Appointment'));
            $wait = match ($a->status) {
                Appointment::STATUS_CONFIRMED, Appointment::STATUS_BOOKED => $a->scheduled_at?->format('H:i') ?? '—',
                default => 'In queue',
            };

            return [
                'num' => $a->token_number ?? '—',
                'name' => $name,
                'type' => $type,
                'wait' => $wait,
                'dim' => $a->status === Appointment::STATUS_CONFIRMED,
            ];
        });
    }

    /**
     * @return array{labels: array<int, string>, amounts: array<int, float>, summary: array{total: float, collected: float, pending: float, gst: float}}
     */
    private function buildWeekRevenueChart(int $clinicId): array
    {
        $start = now()->startOfWeek();
        $labels = [];
        $amounts = [];

        for ($i = 0; $i < 7; $i++) {
            $day = $start->copy()->addDays($i);
            $labels[] = $day->format('D');
            $amounts[] = (float) (Invoice::where('clinic_id', $clinicId)
                ->whereDate('created_at', $day->toDateString())
                ->sum('total') ?? 0);
        }

        $startDt = $start->copy()->startOfDay();
        $endDt = $start->copy()->addDays(6)->endOfDay();

        $totals = DB::table('invoices')
            ->where('clinic_id', $clinicId)
            ->whereBetween('created_at', [$startDt, $endDt])
            ->selectRaw('
                COALESCE(SUM(total), 0) as total_sum,
                COALESCE(SUM(paid), 0) as paid_sum,
                COALESCE(SUM(total - paid), 0) as pending_sum,
                COALESCE(SUM(cgst_amount + sgst_amount + igst_amount), 0) as gst_sum
            ')
            ->first();

        $summary = [
            'total' => (float) ($totals->total_sum ?? 0),
            'collected' => (float) ($totals->paid_sum ?? 0),
            'pending' => (float) ($totals->pending_sum ?? 0),
            'gst' => (float) ($totals->gst_sum ?? 0),
        ];

        return [
            'labels' => $labels,
            'amounts' => $amounts,
            'summary' => $summary,
        ];
    }

    /**
     * @return \Illuminate\Support\Collection<int, array{label: string, pct: int, color: string}>
     */
    private function buildVisitsByType(int $clinicId)
    {
        if (! Schema::hasTable('appointments')) {
            return collect();
        }

        $rows = Appointment::query()
            ->where('clinic_id', $clinicId)
            ->whereMonth('scheduled_at', now()->month)
            ->whereYear('scheduled_at', now()->year)
            ->selectRaw('COALESCE(NULLIF(appointment_type, ""), ?) as apt_type, COUNT(*) as c', ['consultation'])
            ->groupByRaw('COALESCE(NULLIF(appointment_type, ""), ?)', ['consultation'])
            ->get();

        $total = (int) $rows->sum('c');
        if ($total === 0) {
            return collect();
        }

        $palette = ['#1447E6', '#0891B2', '#8b5cf6', '#d97706', '#059669', '#ec4899'];

        return $rows->values()->map(function ($row, $idx) use ($total, $palette) {
            $c = (int) $row->c;
            $pct = (int) round(100 * $c / $total);
            $label = Str::title(str_replace('_', ' ', (string) $row->apt_type));

            return [
                'label' => $label,
                'pct' => $pct,
                'color' => $palette[$idx % count($palette)],
            ];
        });
    }

    /**
     * @return array{name: string, initials: string, gradient: string, desc: string, amount: string, status: string, method: string, url: string|null}
     */
    private function mapInvoiceForDashboard(Invoice $inv): array
    {
        $patient = $inv->patient;
        $name = $patient?->name ?? 'Patient';
        $parts = preg_split('/\s+/', trim($name), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $initials = count($parts) >= 2
            ? strtoupper(substr((string) $parts[0], 0, 1).substr((string) end($parts), 0, 1))
            : strtoupper(substr($name, 0, 2));

        $desc = $inv->items->first()?->description ?? 'Invoice';
        $desc = Str::limit($desc, 42);

        $balance = $inv->getBalanceDue();
        $lastPayment = $inv->payments->sortByDesc('payment_date')->first();
        $methodLabel = $lastPayment?->payment_method
            ? strtoupper(str_replace('_', ' ', (string) $lastPayment->payment_method))
            : null;

        if ($inv->payment_status === Invoice::STATUS_PAID) {
            $status = 'paid';
            $method = $methodLabel ?? 'Paid';
        } elseif ($inv->paid > 0 && $balance > 0) {
            $status = 'advance';
            $method = 'Advance: ₹'.number_format((float) $inv->paid, 0);
        } elseif ($inv->payment_link) {
            $status = 'due';
            $method = 'Due · Link sent';
        } else {
            $status = 'due';
            $method = 'Due · ₹'.number_format($balance, 0);
        }

        $user = auth()->user();
        $canOpenInvoice = $user && in_array($user->role, ['doctor', 'receptionist'], true)
            && \Illuminate\Support\Facades\Route::has('billing.show');
        $url = $canOpenInvoice ? route('billing.show', $inv) : null;

        return [
            'name' => $name,
            'initials' => $initials ?: 'P',
            'gradient' => $this->gradientForString($name),
            'desc' => $desc,
            'amount' => number_format((float) $inv->total, 0),
            'status' => $status,
            'method' => $method,
            'url' => $url,
        ];
    }

    /**
     * @return array{name: string, msg: string, time: string, status: string}
     */
    private function mapWhatsappForDashboard(WhatsappMessage $msg): array
    {
        $name = $msg->patient?->name ?? 'Contact';
        $body = $msg->body ?? '';
        $time = $msg->sent_at ?? $msg->created_at;
        $timeStr = $time ? $time->format('H:i') : '—';

        $needsReply = $msg->direction === WhatsappMessage::DIRECTION_INBOUND
            && $msg->read_at === null;

        return [
            'name' => $name,
            'msg' => Str::limit($body, 140),
            'time' => $timeStr,
            'status' => $needsReply ? 'unread' : 'delivered',
        ];
    }

    private function gradientForString(string $s): string
    {
        $opts = [
            '#6366f1,#8b5cf6',
            '#f59e0b,#ef4444',
            '#0891b2,#6366f1',
            '#8b5cf6,#ec4899',
            '#059669,#0891b2',
            '#1447e6,#0891b2',
        ];
        $idx = abs(crc32($s)) % count($opts);

        return $opts[$idx];
    }

    /**
     * @return array{time: string, name: string, initials: string, gradient: string, type: string, status: string, token: mixed, id: int, url: string}
     */
    private function mapAppointmentForDashboard(Appointment $apt): array
    {
        $name = $apt->patient?->name ?? 'Patient';
        $parts = preg_split('/\s+/', trim($name), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $initials = count($parts) >= 2
            ? strtoupper(substr((string) $parts[0], 0, 1).substr((string) end($parts), 0, 1))
            : strtoupper(substr($name, 0, 2));

        $statusUi = match ($apt->status) {
            'completed' => 'done',
            'checked_in' => 'waiting',
            'in_consultation' => 'in-consultation',
            'no_show' => 'no-show',
            'cancelled' => 'no-show',
            default => str_replace('_', '-', $apt->status),
        };

        $type = ucfirst(str_replace('_', ' ', $apt->appointment_type ?? 'consultation'));
        if ($apt->booking_source === 'online_booking') {
            $type .= ' · Web booking';
        }

        Log::debug('DashboardController: mapAppointmentForDashboard', [
            'appointment_id' => $apt->id,
            'status_ui' => $statusUi,
        ]);

        return [
            'time' => $apt->scheduled_at->format('H:i'),
            'name' => $name,
            'initials' => $initials ?: 'P',
            'gradient' => $this->gradientForString($name),
            'type' => $type,
            'status' => $statusUi,
            'token' => $apt->token_number,
            'id' => $apt->id,
            'url' => $this->dashboardAppointmentUrl($apt),
        ];
    }

    private function dashboardAppointmentUrl(Appointment $apt): string
    {
        $user = auth()->user();
        if ($user && in_array($user->role, ['doctor', 'receptionist', 'nurse'], true)) {
            return route('appointments.show', $apt);
        }

        return route('schedule');
    }
}
