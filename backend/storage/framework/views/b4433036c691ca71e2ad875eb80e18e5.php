<?php $__env->startSection('title', 'Daily Summary'); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="fas fa-calendar-day me-2"></i>Daily Summary — <?php echo e($summary['date']); ?></h4>
        <div>
            <form method="GET" class="d-inline">
                <input type="date" name="date" class="form-control form-control-sm d-inline-block" style="width: auto;" value="<?php echo e($summary['date']); ?>" onchange="this.form.submit()">
            </form>
            <a href="<?php echo e(route('reports.index')); ?>" class="btn btn-outline-secondary btn-sm ms-2">All Reports</a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm text-center py-3 border-start border-4 border-primary">
                <h3 class="text-primary mb-0"><?php echo e($summary['patients_seen']); ?></h3>
                <small class="text-muted">Patients Seen</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm text-center py-3 border-start border-4 border-success">
                <h3 class="text-success mb-0">₹<?php echo e(number_format($summary['revenue'])); ?></h3>
                <small class="text-muted">Revenue</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm text-center py-3 border-start border-4 border-info">
                <h3 class="text-info mb-0"><?php echo e($summary['appointments_total']); ?></h3>
                <small class="text-muted">Appointments</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm text-center py-3 border-start border-4 border-warning">
                <h3 class="text-warning mb-0"><?php echo e($summary['new_registrations']); ?></h3>
                <small class="text-muted">New Registrations</small>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white"><h6 class="mb-0">Appointment Breakdown</h6></div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2"><span>Total</span><strong><?php echo e($summary['appointments_total']); ?></strong></div>
                    <div class="d-flex justify-content-between mb-2"><span>Completed</span><span class="badge bg-success"><?php echo e($summary['appointments_completed']); ?></span></div>
                    <div class="d-flex justify-content-between mb-2"><span>Cancelled</span><span class="badge bg-danger"><?php echo e($summary['appointments_cancelled']); ?></span></div>
                    <div class="d-flex justify-content-between"><span>Pending</span><span class="badge bg-warning"><?php echo e($summary['appointments_total'] - $summary['appointments_completed'] - $summary['appointments_cancelled']); ?></span></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white"><h6 class="mb-0">Other Metrics</h6></div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2"><span>Prescriptions Written</span><strong><?php echo e($summary['prescriptions']); ?></strong></div>
                    <div class="d-flex justify-content-between"><span>Lab Orders</span><strong><?php echo e($summary['lab_orders']); ?></strong></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/reports/daily-summary.blade.php ENDPATH**/ ?>