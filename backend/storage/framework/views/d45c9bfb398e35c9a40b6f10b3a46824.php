<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Pre-visit questionnaire — <?php echo e($clinic->name); ?></title>
    <style>
        :root { font-family: system-ui, sans-serif; }
        body { margin: 0; background: #f8fafc; color: #0f172a; }
        .wrap { max-width: 560px; margin: 0 auto; padding: 24px 16px 48px; }
        .card {
            background: #fff;
            border-radius: 16px;
            padding: 24px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
        }
        h1 { font-size: 1.25rem; margin: 0 0 8px; }
        .muted { color: #64748b; font-size: 0.875rem; margin-bottom: 20px; }
        label { display: block; font-weight: 600; font-size: 0.8rem; margin-bottom: 6px; color: #334155; }
        textarea {
            width: 100%; min-height: 72px; padding: 10px 12px; border-radius: 10px;
            border: 1px solid #cbd5e1; font-size: 0.95rem; resize: vertical; box-sizing: border-box;
        }
        .field { margin-bottom: 16px; }
        button {
            margin-top: 8px; width: 100%; padding: 12px 16px; border: none; border-radius: 10px;
            background: linear-gradient(135deg, #1447e6, #0891b2); color: #fff; font-weight: 600;
            cursor: pointer; font-size: 1rem;
        }
        button:hover { opacity: .95; }
        .ok {
            background: #ecfdf5; color: #065f46; border: 1px solid #6ee7b7;
            padding: 12px 14px; border-radius: 10px; margin-bottom: 16px; font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="card">
            <h1><?php echo e($clinic->name); ?></h1>
            <p class="muted">
                Pre-visit questionnaire for <?php echo e($appointment->patient->name ?? 'patient'); ?>

                · <?php echo e(\Carbon\Carbon::parse($appointment->scheduled_at)->format('D, d M Y · h:i A')); ?>

            </p>

            <?php if(session('success')): ?>
                <div class="ok"><?php echo e(session('success')); ?></div>
            <?php endif; ?>

            <form method="post" action="<?php echo e(route('public.booking.pre-visit.submit', ['clinicSlug' => $clinic->slug, 'token' => $appointment->pre_visit_token])); ?>">
                <?php echo csrf_field(); ?>
                <?php $__currentLoopData = $questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="field">
                        <label for="answer_<?php echo e($q['id']); ?>"><?php echo e($q['label']); ?></label>
                        <textarea
                            id="answer_<?php echo e($q['id']); ?>"
                            name="answer_<?php echo e($q['id']); ?>"
                            placeholder="Your answer"
                        ><?php echo e(old('answer_' . $q['id'], data_get($appointment->pre_visit_answers, $q['id']))); ?></textarea>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <button type="submit">Submit responses</button>
            </form>
        </div>
    </div>
    <script>
        console.log('[pre-visit] form loaded', { clinic: <?php echo json_encode($clinic->slug, 15, 512) ?>, appt: <?php echo e($appointment->id); ?> });
    </script>
</body>
</html>
<?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/public/pre-visit.blade.php ENDPATH**/ ?>