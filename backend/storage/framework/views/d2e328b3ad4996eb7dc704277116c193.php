<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Contact lens prescription</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
        h1 { font-size: 16px; margin: 0 0 8px; }
        .muted { color: #555; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: center; }
        th { background: #f3f4f6; }
    </style>
</head>
<body>
    <div style="margin-bottom: 16px;">
        <h1>Contact lens prescription</h1>
        <div><strong><?php echo e($clinic->name ?? 'Clinic'); ?></strong></div>
        <div class="muted">Generated <?php echo e(now()->format('d M Y, h:i A')); ?></div>
    </div>
    <p><strong>Patient:</strong> <?php echo e($patient->name); ?> <?php if($patient->phone): ?> &nbsp;|&nbsp; <?php echo e($patient->phone); ?> <?php endif; ?></p>
    <p><strong>Prescriber:</strong> <?php echo e($doctor->name ?? '—'); ?></p>
    <p class="muted">Visit #<?php echo e($visit->visit_number); ?> &nbsp;|&nbsp; Source: <?php echo e($contactLens['source'] ?? '—'); ?></p>

    <?php
        $od = $contactLens['od'] ?? [];
        $os = $contactLens['os'] ?? [];
    ?>

    <table>
        <thead>
            <tr>
                <th>Eye</th>
                <th>BC / K</th>
                <th>Power</th>
                <th>Dia</th>
                <th>Cyl</th>
                <th>Axis</th>
                <th>Brand / Type</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>OD</td>
                <td><?php echo e($od['bc'] ?? $od['base_curve'] ?? '—'); ?></td>
                <td><?php echo e($od['power'] ?? '—'); ?></td>
                <td><?php echo e($od['dia'] ?? $od['diameter'] ?? '—'); ?></td>
                <td><?php echo e($od['cyl'] ?? '—'); ?></td>
                <td><?php echo e($od['axis'] ?? '—'); ?></td>
                <td><?php echo e($od['brand'] ?? '—'); ?></td>
            </tr>
            <tr>
                <td>OS</td>
                <td><?php echo e($os['bc'] ?? $os['base_curve'] ?? '—'); ?></td>
                <td><?php echo e($os['power'] ?? '—'); ?></td>
                <td><?php echo e($os['dia'] ?? $os['diameter'] ?? '—'); ?></td>
                <td><?php echo e($os['cyl'] ?? '—'); ?></td>
                <td><?php echo e($os['axis'] ?? '—'); ?></td>
                <td><?php echo e($os['brand'] ?? '—'); ?></td>
            </tr>
        </tbody>
    </table>

    <p style="margin-top:12px;"><strong>Modality:</strong> <?php echo e($contactLens['modality'] ?? '—'); ?>

        &nbsp;|&nbsp; <strong>Wear:</strong> <?php echo e($contactLens['wearSchedule'] ?? ($contactLens['wear'] ?? '—')); ?></p>
    <?php if(!empty($contactLens['notes'])): ?>
        <p><strong>Notes:</strong> <?php echo e($contactLens['notes']); ?></p>
    <?php endif; ?>

    <p class="muted" style="margin-top:24px;">Verify fit and follow-up per clinic protocol. Generated from EMR.</p>
</body>
</html>
<?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/prescriptions/contact-lens-pdf.blade.php ENDPATH**/ ?>