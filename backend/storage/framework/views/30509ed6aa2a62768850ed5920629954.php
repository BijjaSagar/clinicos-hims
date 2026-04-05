<?php $__env->startSection('title', 'MIS Reports'); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <h4 class="mb-4"><i class="fas fa-file-alt me-2"></i>MIS Reports</h4>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-day fa-3x text-primary mb-3"></i>
                    <h6>Daily Summary</h6>
                    <p class="text-muted small">Patients seen, revenue, appointments for any day</p>
                    <a href="<?php echo e(route('reports.daily-summary')); ?>" class="btn btn-primary btn-sm w-100">View Report</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-chart-pie fa-3x text-success mb-3"></i>
                    <h6>Monthly MIS</h6>
                    <p class="text-muted small">Complete monthly metrics and KPIs</p>
                    <a href="<?php echo e(route('reports.monthly-mis')); ?>" class="btn btn-success btn-sm w-100">View Report</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-download fa-3x text-info mb-3"></i>
                    <h6>Export CSV</h6>
                    <p class="text-muted small">Download patient register, appointment data</p>
                    <div class="btn-group w-100">
                        <a href="<?php echo e(route('reports.export-csv', ['type' => 'patient_register'])); ?>" class="btn btn-outline-info btn-sm">Patients</a>
                        <a href="<?php echo e(route('reports.export-csv', ['type' => 'appointment_register'])); ?>" class="btn btn-outline-info btn-sm">Appointments</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-2">
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-rupee-sign fa-3x text-warning mb-3"></i>
                    <h6>Revenue Analytics</h6>
                    <p class="text-muted small">Detailed revenue breakdown by doctor, payment</p>
                    <a href="<?php echo e(route('analytics.revenue')); ?>" class="btn btn-warning btn-sm w-100">View</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-prescription fa-3x text-danger mb-3"></i>
                    <h6>Prescription Analytics</h6>
                    <p class="text-muted small">Top drugs, antibiotic rates, trends</p>
                    <a href="<?php echo e(route('analytics.prescriptions')); ?>" class="btn btn-danger btn-sm w-100">View</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-3x text-secondary mb-3"></i>
                    <h6>Patient Analytics</h6>
                    <p class="text-muted small">Demographics, visit frequency, top visitors</p>
                    <a href="<?php echo e(route('analytics.patients')); ?>" class="btn btn-secondary btn-sm w-100">View</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/reports/index.blade.php ENDPATH**/ ?>