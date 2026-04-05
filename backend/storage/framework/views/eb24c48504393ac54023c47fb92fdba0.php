<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Prescription - <?php echo e($patient->name); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #1f2937;
            padding: 20px;
        }
        .header {
            border-bottom: 2px solid #1447e6;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        .clinic-name {
            font-size: 18px;
            font-weight: bold;
            color: #1447e6;
        }
        .clinic-address {
            font-size: 9px;
            color: #6b7280;
            margin-top: 3px;
        }
        .doctor-info {
            margin-top: 8px;
            padding: 8px;
            background: #f0f4ff;
            border-radius: 4px;
        }
        .doctor-name {
            font-weight: bold;
            color: #1f2937;
        }
        .doctor-reg {
            font-size: 9px;
            color: #6b7280;
        }
        .patient-section {
            display: table;
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
            background: #fafafa;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
        }
        .patient-row {
            display: table-row;
        }
        .patient-cell {
            display: table-cell;
            padding: 3px 10px;
        }
        .patient-label {
            font-weight: bold;
            color: #6b7280;
            font-size: 9px;
            text-transform: uppercase;
        }
        .patient-value {
            color: #1f2937;
        }
        .rx-symbol {
            font-size: 24px;
            font-weight: bold;
            color: #1447e6;
            margin: 15px 0 10px 0;
        }
        .prescription-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .prescription-table th {
            background: #1447e6;
            color: white;
            padding: 8px 10px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
        }
        .prescription-table td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }
        .prescription-table tr:nth-child(even) {
            background: #f9fafb;
        }
        .drug-name {
            font-weight: bold;
            color: #1f2937;
        }
        .drug-form {
            font-size: 9px;
            color: #6b7280;
        }
        .dosage {
            color: #1447e6;
            font-weight: 600;
        }
        .frequency {
            font-weight: 500;
        }
        .instructions {
            font-size: 9px;
            color: #6b7280;
            font-style: italic;
            margin-top: 3px;
        }
        .controlled-badge {
            display: inline-block;
            background: #fef2f2;
            color: #dc2626;
            font-size: 8px;
            padding: 2px 5px;
            border-radius: 3px;
            margin-left: 5px;
        }
        .substitutable {
            font-size: 8px;
            color: #6b7280;
        }
        .footer {
            margin-top: 30px;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
        }
        .signature-area {
            text-align: right;
            margin-top: 30px;
        }
        .signature-line {
            width: 180px;
            border-top: 1px solid #1f2937;
            margin-left: auto;
            padding-top: 5px;
            text-align: center;
        }
        .advice-section {
            margin-top: 15px;
            padding: 10px;
            background: #fef3cd;
            border-radius: 4px;
        }
        .advice-title {
            font-weight: bold;
            color: #856404;
            font-size: 10px;
            margin-bottom: 5px;
        }
        .advice-text {
            color: #856404;
            font-size: 9px;
        }
        .diagnosis-section {
            margin: 10px 0;
            padding: 8px;
            background: #e8f5e9;
            border-left: 3px solid #4caf50;
        }
        .diagnosis-label {
            font-size: 9px;
            color: #2e7d32;
            font-weight: bold;
        }
        .diagnosis-text {
            color: #1f2937;
            font-weight: 500;
        }
        .date-section {
            text-align: right;
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 10px;
        }
        .watermark {
            position: fixed;
            bottom: 40px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 8px;
            color: #d1d5db;
        }
    </style>
</head>
<body>
    
    <div class="header">
        <div class="clinic-name"><?php echo e($clinic->name); ?></div>
        <div class="clinic-address">
            <?php echo e($clinic->address ?? ''); ?><br>
            Phone: <?php echo e($clinic->phone ?? 'N/A'); ?> | Email: <?php echo e($clinic->email ?? 'N/A'); ?>

            <?php if($clinic->gstin): ?>
            | GSTIN: <?php echo e($clinic->gstin); ?>

            <?php endif; ?>
        </div>
        <div class="doctor-info">
            <div class="doctor-name"><?php echo e($doctor->name); ?></div>
            <div class="doctor-reg">
                <?php echo e($doctor->specialty ?? 'General Practitioner'); ?>

                <?php if($doctor->registration_number): ?>
                | Reg. No: <?php echo e($doctor->registration_number); ?>

                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <div class="date-section">
        Date: <?php echo e(now()->format('d M Y')); ?> | Time: <?php echo e(now()->format('h:i A')); ?>

    </div>

    
    <div class="patient-section">
        <div class="patient-row">
            <div class="patient-cell">
                <div class="patient-label">Patient Name</div>
                <div class="patient-value"><?php echo e($patient->name); ?></div>
            </div>
            <div class="patient-cell">
                <div class="patient-label">Age / Gender</div>
                <div class="patient-value"><?php echo e($patient->age ?? 'N/A'); ?> yrs / <?php echo e(ucfirst($patient->gender ?? 'N/A')); ?></div>
            </div>
            <div class="patient-cell">
                <div class="patient-label">Patient ID</div>
                <div class="patient-value"><?php echo e($patient->patient_id); ?></div>
            </div>
        </div>
        <div class="patient-row">
            <div class="patient-cell">
                <div class="patient-label">Phone</div>
                <div class="patient-value"><?php echo e($patient->phone ?? 'N/A'); ?></div>
            </div>
            <?php if($patient->abha_id): ?>
            <div class="patient-cell">
                <div class="patient-label">ABHA ID</div>
                <div class="patient-value"><?php echo e($patient->abha_id); ?></div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    
    <?php if($visit->diagnosis_text): ?>
    <div class="diagnosis-section">
        <span class="diagnosis-label">DIAGNOSIS: </span>
        <span class="diagnosis-text"><?php echo e($visit->diagnosis_text); ?></span>
        <?php if($visit->diagnosis_code): ?>
        <span style="font-size:9px; color:#666;"> (<?php echo e($visit->diagnosis_code); ?>)</span>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    
    <div class="rx-symbol">℞</div>

    
    <?php if($items->count() > 0): ?>
    <table class="prescription-table">
        <thead>
            <tr>
                <th style="width:5%">#</th>
                <th style="width:35%">Medicine</th>
                <th style="width:15%">Dosage</th>
                <th style="width:20%">Frequency</th>
                <th style="width:15%">Duration</th>
                <th style="width:10%">Qty</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($index + 1); ?></td>
                <td>
                    <div class="drug-name">
                        <?php echo e($item->drug_name); ?>

                        <?php if($item->drug && $item->drug->is_controlled): ?>
                        <span class="controlled-badge">Controlled</span>
                        <?php endif; ?>
                    </div>
                    <?php if($item->drug): ?>
                    <div class="drug-form"><?php echo e($item->drug->form); ?> - <?php echo e($item->drug->strength); ?></div>
                    <?php endif; ?>
                    <?php if($item->instructions): ?>
                    <div class="instructions"><?php echo e($item->instructions); ?></div>
                    <?php endif; ?>
                    <?php if($item->is_substitutable): ?>
                    <div class="substitutable">Generic substitution allowed</div>
                    <?php endif; ?>
                </td>
                <td class="dosage"><?php echo e($item->dosage); ?></td>
                <td class="frequency"><?php echo e($item->frequency_label); ?></td>
                <td><?php echo e($item->duration); ?></td>
                <td><?php echo e($item->quantity ?? '-'); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    <?php else: ?>
    <p style="color:#6b7280; text-align:center; padding:20px;">No medications prescribed</p>
    <?php endif; ?>

    
    <?php if($visit->plan || $visit->followup_date): ?>
    <div class="advice-section">
        <div class="advice-title">ADVICE / FOLLOW-UP</div>
        <div class="advice-text">
            <?php if($visit->plan): ?>
            <?php echo e($visit->plan); ?>

            <?php endif; ?>
            <?php if($visit->followup_date): ?>
            <br>Follow-up on: <?php echo e($visit->followup_date->format('d M Y')); ?>

            <?php elseif($visit->followup_in_days): ?>
            <br>Follow-up after: <?php echo e($visit->followup_in_days); ?> days
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    
    <div class="signature-area">
        <div class="signature-line">
            Dr. <?php echo e($doctor->name); ?><br>
            <span style="font-size:8px;"><?php echo e($doctor->specialty ?? ''); ?></span>
        </div>
    </div>

    
    <div class="footer">
        <div style="font-size:8px; color:#9ca3af; text-align:center;">
            This is a computer-generated prescription. Valid for 30 days from date of issue.
            <br>For queries, contact: <?php echo e($clinic->phone ?? ''); ?>

        </div>
    </div>

    <div class="watermark">
        Generated by ClinicOS | <?php echo e(now()->format('d/m/Y H:i')); ?>

    </div>
</body>
</html>
<?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/prescriptions/pdf.blade.php ENDPATH**/ ?>