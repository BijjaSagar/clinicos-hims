<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Spectacle prescription</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
        h1 { font-size: 16px; margin: 0 0 8px; }
        .muted { color: #555; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: center; }
        th { background: #f3f4f6; }
        .hdr { margin-bottom: 16px; }
    </style>
</head>
<body>
    <div class="hdr">
        <h1>Spectacle prescription</h1>
        <div><strong><?php echo e($clinic->name ?? 'Clinic'); ?></strong></div>
        <div class="muted">Generated <?php echo e(now()->format('d M Y, h:i A')); ?></div>
    </div>
    <p><strong>Patient:</strong> <?php echo e($patient->name); ?> <?php if($patient->phone): ?> &nbsp;|&nbsp; <?php echo e($patient->phone); ?> <?php endif; ?></p>
    <p><strong>Prescriber:</strong> <?php echo e($doctor->name ?? '—'); ?></p>
    <p class="muted">Visit #<?php echo e($visit->visit_number); ?> &nbsp;|&nbsp; Source: <?php echo e($spectacle['source'] ?? '—'); ?></p>

    <table>
        <thead>
            <tr>
                <th>Eye</th>
                <th>Sphere</th>
                <th>Cylinder</th>
                <th>Axis</th>
                <th>Add</th>
                <th>VA</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $od = $spectacle['od'] ?? [];
                $os = $spectacle['os'] ?? [];
            ?>
            <tr>
                <td>OD</td>
                <td><?php echo e($od['sphere'] ?? '—'); ?></td>
                <td><?php echo e($od['cylinder'] ?? '—'); ?></td>
                <td><?php echo e($od['axis'] ?? '—'); ?></td>
                <td><?php echo e($od['add'] ?? '—'); ?></td>
                <td><?php echo e($od['va'] ?? '—'); ?></td>
            </tr>
            <tr>
                <td>OS</td>
                <td><?php echo e($os['sphere'] ?? '—'); ?></td>
                <td><?php echo e($os['cylinder'] ?? '—'); ?></td>
                <td><?php echo e($os['axis'] ?? '—'); ?></td>
                <td><?php echo e($os['add'] ?? '—'); ?></td>
                <td><?php echo e($os['va'] ?? '—'); ?></td>
            </tr>
        </tbody>
    </table>

    <p style="margin-top:12px;"><strong>PD (mm):</strong> Distance <?php echo e($spectacle['pdDistance'] ?? ($spectacle['pd_distance'] ?? '—')); ?>

        &nbsp;|&nbsp; Near <?php echo e($spectacle['pdNear'] ?? ($spectacle['pd_near'] ?? '—')); ?></p>
    <?php if(!empty($spectacle['instructions'])): ?>
        <p><strong>Instructions:</strong> <?php echo e($spectacle['instructions']); ?></p>
    <?php endif; ?>
    <?php if(!empty($spectacle['lensType'])): ?>
        <p><strong>Lens type:</strong> <?php echo e($spectacle['lensType']); ?></p>
    <?php endif; ?>

    <p class="muted" style="margin-top:24px;">This document is generated from EMR data for clinical use. Verify before dispensing.</p>
</body>
</html>
<?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/prescriptions/spectacle-pdf.blade.php ENDPATH**/ ?>