@extends('layouts.app')
@section('title', 'Monthly MIS Report')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Monthly MIS — {{ $mis['month'] }}</h4>
        <div>
            <form method="GET" class="d-inline">
                <input type="month" name="month" class="form-control form-control-sm d-inline-block" style="width: auto;" value="{{ $mis['month'] }}" onchange="this.form.submit()">
            </form>
            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm ms-2">All Reports</a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="card shadow-sm text-center py-2">
                <h4 class="text-primary mb-0">{{ number_format($mis['total_patients']) }}</h4>
                <small class="text-muted">Patients</small>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm text-center py-2">
                <h4 class="text-success mb-0">{{ $mis['new_patients'] }}</h4>
                <small class="text-muted">New Patients</small>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm text-center py-2">
                <h4 class="text-info mb-0">{{ number_format($mis['total_appointments']) }}</h4>
                <small class="text-muted">Appointments</small>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm text-center py-2">
                <h4 class="text-warning mb-0">₹{{ number_format($mis['total_revenue']) }}</h4>
                <small class="text-muted">Revenue</small>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm text-center py-2">
                <h4 class="text-danger mb-0">{{ $mis['no_show_rate'] }}%</h4>
                <small class="text-muted">No-Show Rate</small>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm text-center py-2">
                <h4 class="text-secondary mb-0">{{ $mis['total_prescriptions'] }}</h4>
                <small class="text-muted">Prescriptions</small>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between">
            <h6 class="mb-0">Daily Breakdown</h6>
            <a href="{{ route('reports.export-csv', ['type' => 'appointment_register', 'date' => $mis['month'] . '-01']) }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-download me-1"></i>Export CSV</a>
        </div>
        <div class="card-body p-0">
            <table class="table table-sm table-striped mb-0">
                <thead><tr><th>Date</th><th class="text-end">Revenue</th><th class="text-end">Invoices</th></tr></thead>
                <tbody>
                @forelse($mis['daily_breakdown'] as $day)
                <tr><td>{{ $day->date }}</td><td class="text-end">₹{{ number_format($day->revenue, 2) }}</td><td class="text-end">{{ $day->invoices }}</td></tr>
                @empty
                <tr><td colspan="3" class="text-center text-muted py-3">No data for this month</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
