<?php $__env->startSection('title', 'EMR — ' . ($patient->name ?? 'Patient')); ?>

<?php $__env->startSection('breadcrumb', 'Patients / ' . ($patient->name ?? 'Patient') . ' / EMR'); ?>

<?php $__env->startPush('styles'); ?>
<style>
  :root {
    --blue:#1447e6; --blue-light:#eff3ff; --teal:#0891b2;
    --green:#059669; --green-light:#ecfdf5; --amber:#d97706;
    --red:#dc2626; --dark:#0d1117;
    --text:#1a1f2e; --text2:#4b5563; --text3:#9ca3af;
    --border:#e5e7eb; --bg:#f3f4f6;
  }
  /* PATIENT HEADER */
  .patient-header-bar{background:white;border-bottom:1px solid var(--border);padding:16px 28px;display:flex;align-items:center;gap:20px}
  .pat-avatar{width:52px;height:52px;border-radius:50%;background:linear-gradient(135deg,#f59e0b,#ef4444);display:flex;align-items:center;justify-content:center;color:white;font-weight:800;font-size:20px;flex-shrink:0}
  .pat-name{font-family:'Sora',sans-serif;font-size:18px;font-weight:700;color:var(--dark)}
  .pat-meta{display:flex;gap:12px;margin-top:4px;flex-wrap:wrap}
  .meta-chip{display:flex;align-items:center;gap:5px;font-size:12px;color:var(--text2)}
  .meta-chip span{font-weight:600;color:var(--text)}
  .abha-chip{background:linear-gradient(135deg,#f97316,#ef4444);color:white;padding:4px 12px;border-radius:100px;font-size:11px;font-weight:700;display:flex;align-items:center;gap:5px}
  .specialty-badge{background:var(--blue-light);color:var(--blue);padding:4px 12px;border-radius:100px;font-size:11px;font-weight:700}
  .status-pill-active{background:var(--green-light);color:var(--green);padding:4px 12px;border-radius:100px;font-size:11px;font-weight:700;display:flex;align-items:center;gap:5px}
  /* AI STRIP */
  .ai-dictation-strip{background:linear-gradient(135deg,rgba(20,71,230,.05),rgba(8,145,178,.05));border:1.5px solid rgba(20,71,230,.15);border-radius:10px;padding:14px 16px;display:flex;align-items:center;gap:12px;margin-bottom:16px}
  .ai-strip-icon{width:36px;height:36px;border-radius:9px;background:var(--blue);display:flex;align-items:center;justify-content:center;color:white;font-size:15px;flex-shrink:0}
  .ai-strip-text h4{font-size:13px;font-weight:700;color:var(--dark)}
  .ai-strip-text p{font-size:12px;color:var(--text3);margin-top:2px}
  .ai-strip-actions{margin-left:auto;display:flex;gap:8px}
  /* TABS */
  .emr-tabs{background:white;border-bottom:1px solid var(--border);padding:0 28px;display:flex;gap:2px;flex-shrink:0}
  .emr-tab{padding:14px 18px;font-size:13px;font-weight:600;color:var(--text3);cursor:pointer;border-bottom:2px solid transparent;transition:all .15s;white-space:nowrap}
  .emr-tab:hover{color:var(--text2)}
  .emr-tab.active{color:var(--blue);border-bottom-color:var(--blue)}
  /* EMR BODY */
  .emr-body{display:flex;flex:1;min-height:500px;overflow:visible}
  .emr-sidebar{width:280px;flex-shrink:0;background:white;border-right:1px solid var(--border);overflow-y:auto;padding:16px}
  .emr-main{flex:1;overflow-y:auto;padding:24px 28px;padding-bottom:100px;min-height:400px}
  /* TIMELINE */
  .timeline-header{font-size:12px;font-weight:700;color:var(--text3);letter-spacing:.06em;text-transform:uppercase;margin-bottom:12px}
  .visit-card{border:1.5px solid var(--border);border-radius:10px;padding:12px;margin-bottom:8px;cursor:pointer;transition:all .15s}
  .visit-card:hover{border-color:var(--blue);background:var(--blue-light)}
  .visit-card.active-visit{border-color:var(--blue);background:var(--blue-light)}
  .visit-date{font-size:11px;font-weight:700;color:var(--blue)}
  .visit-type{font-size:13px;font-weight:600;color:var(--dark);margin-top:3px}
  .visit-summary{font-size:11px;color:var(--text3);margin-top:4px;line-height:1.5}
  .visit-chips{display:flex;gap:4px;margin-top:6px;flex-wrap:wrap}
  .vchip{padding:2px 8px;border-radius:4px;font-size:10px;font-weight:600}
  /* FORM SECTIONS */
  .form-section{background:white;border:1px solid var(--border);border-radius:12px;margin-bottom:16px;overflow:hidden}
  .form-section-header{padding:14px 20px;background:var(--bg);border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px;cursor:pointer}
  .form-section-header h3{font-size:14px;font-weight:700;color:var(--dark);flex:1}
  .form-section-header .toggle{color:var(--text3);font-size:18px}
  .form-body{padding:20px}
  .form-row{display:grid;gap:12px;margin-bottom:12px}
  .form-row-2{grid-template-columns:1fr 1fr}
  .form-row-3{grid-template-columns:1fr 1fr 1fr}
  .field-group{display:flex;flex-direction:column;gap:4px}
  .field-label{font-size:11px;font-weight:600;color:var(--text3);letter-spacing:.04em;text-transform:uppercase}
  .field-input{padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;transition:border-color .15s;background:white;width:100%}
  .field-input:focus{border-color:var(--blue)}
  .field-select{padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;background:white;cursor:pointer;width:100%}
  .field-textarea{padding:10px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;resize:vertical;min-height:80px;width:100%;line-height:1.6;transition:border-color .15s}
  .field-textarea:focus{border-color:var(--blue)}
  /* BODY MAP */
  .body-map-container{display:flex;gap:20px;align-items:flex-start}
  .body-diagram{background:var(--bg);border:1.5px solid var(--border);border-radius:12px;padding:20px;text-align:center;cursor:crosshair;position:relative;min-width:160px}
  .body-diagram svg{width:100px}
  .lesion-annotations{flex:1}
  .lesion-row{display:flex;align-items:center;gap:8px;padding:8px 10px;background:var(--bg);border-radius:8px;margin-bottom:6px}
  .lesion-color{width:12px;height:12px;border-radius:50%;flex-shrink:0}
  .lesion-desc{font-size:12px;color:var(--text2);flex:1}
  .lesion-remove{color:var(--text3);cursor:pointer;font-size:14px}
  /* SCALES */
  .scale-group{display:flex;gap:6px;align-items:center;margin-bottom:8px}
  .scale-label{font-size:12px;color:var(--text2);width:120px;flex-shrink:0}
  .scale-input{width:80px;padding:6px 10px;border:1.5px solid var(--border);border-radius:7px;font-size:13px;font-weight:700;text-align:center;outline:none;font-family:'Inter',sans-serif}
  .scale-input:focus{border-color:var(--blue)}
  .scale-range{font-size:11px;color:var(--text3)}
  .scale-result{font-size:12px;font-weight:700;padding:3px 10px;border-radius:100px}
  .sr-mild{background:var(--green-light);color:var(--green)}
  .sr-mod{background:#fffbeb;color:var(--amber)}
  .sr-sev{background:#fff1f2;color:var(--red)}
  /* PROCEDURE */
  .proc-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:12px}
  .proc-chip{padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:12px;font-weight:600;color:var(--text2);cursor:pointer;text-align:center;transition:all .15s}
  .proc-chip:hover{border-color:var(--blue);color:var(--blue)}
  .proc-chip.selected{background:var(--blue);border-color:var(--blue);color:white}
  /* PHOTO */
  .photo-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:10px}
  .photo-thumb{aspect-ratio:1;border-radius:8px;overflow:hidden;position:relative;cursor:pointer;border:2px solid transparent;transition:border-color .15s;background:var(--bg)}
  .photo-thumb:hover{border-color:var(--blue)}
  .photo-placeholder{width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:24px;flex-direction:column;gap:4px}
  .photo-label{font-size:10px;color:var(--text3);font-weight:500}
  /* DRUG TABLE */
  .drug-table{width:100%;border-collapse:collapse}
  .drug-table th{font-size:11px;font-weight:600;color:var(--text3);text-align:left;padding:8px 10px;background:var(--bg);letter-spacing:.04em;text-transform:uppercase}
  .drug-table td{font-size:13px;color:var(--text2);padding:10px 10px;border-bottom:1px solid var(--border)}
  .drug-table tr:last-child td{border-bottom:none}
  .drug-table tr:hover td{background:var(--bg)}
  .drug-name{font-weight:600;color:var(--dark)}
  .drug-generic{font-size:11px;color:var(--text3);margin-top:1px}
  .drug-remove{color:var(--text3);cursor:pointer;font-size:16px}
  .add-drug-btn{display:flex;align-items:center;gap:8px;padding:10px 14px;border:1.5px dashed var(--border);border-radius:8px;font-size:13px;color:var(--text3);cursor:pointer;margin-top:8px;transition:all .15s;font-weight:500;background:none}
  .add-drug-btn:hover{border-color:var(--blue);color:var(--blue)}
  /* UPLOAD ZONE */
  .upload-zone{border:2px dashed var(--border);border-radius:10px;padding:32px;text-align:center;cursor:pointer;transition:all .2s}
  .upload-zone:hover{border-color:var(--blue);background:var(--blue-light)}
  /* BOTTOM BAR */
  .bottom-bar{position:sticky;bottom:0;background:white;border-top:1px solid var(--border);padding:14px 28px;display:flex;align-items:center;gap:12px;z-index:10}
  .save-info{font-size:12px;color:var(--text3)}
  .save-info strong{color:var(--green);font-weight:600}
  .status-dot{width:8px;height:8px;border-radius:50%;background:var(--green);animation:pulse2 2s infinite;display:inline-block}
  @keyframes pulse2{0%,100%{opacity:1}50%{opacity:.4}}
  /* DRUG SEARCH */
  .drug-search-container{position:relative;margin-bottom:16px}
  .drug-autocomplete{position:absolute;top:100%;left:0;right:0;background:white;border:1.5px solid var(--border);border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.1);z-index:20;max-height:200px;overflow-y:auto}
  .drug-option{padding:10px 14px;cursor:pointer;font-size:13px;border-bottom:1px solid var(--border)}
  .drug-option:last-child{border-bottom:none}
  .drug-option:hover{background:var(--blue-light);color:var(--blue)}
  .drug-option strong{color:var(--dark)}
  .drug-option span{color:var(--text3);font-size:11px}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
  $patientId = $patient->id;
  $visitId = $visit->id;
  $statusDisplay = match($visit->status) {
    'draft' => 'In Consultation',
    'finalised' => 'Completed',
    default => ucfirst($visit->status ?? 'Unknown'),
  };
  $commonComplaints = $commonComplaints ?? ['General Checkup', 'Follow-up', 'New Complaint'];
?>

<div id="emr-alpine-root" x-data="{ activeTab: 'visit', recording: false, dictationBusy: false, dictationLang: 'auto', aiSummarising: false, autoSaved: true, lastSaved: '' }" x-init="lastSaved = new Date().toLocaleTimeString('en-IN',{hour:'2-digit',minute:'2-digit'}); console.log('Alpine EMR initialized, activeTab:', activeTab)">
<form method="POST" action="<?php echo e(route('emr.update', [$patientId, $visitId])); ?>" id="emr-form">
  <?php echo csrf_field(); ?>
  <?php echo method_field('PATCH'); ?>

  
  <div style="background:white;border-bottom:1px solid var(--border);padding:0 28px;height:60px;display:flex;align-items:center;gap:12px;flex-shrink:0;position:sticky;top:0;z-index:20">
    <a href="<?php echo e(route('patients.show', $patient)); ?>" style="font-size:13px;color:var(--text3);text-decoration:none;display:flex;align-items:center;gap:4px">
      <svg style="width:16px;height:16px" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
      Back to Patient
    </a>
    <span style="font-size:13px;color:var(--text3)">/</span>
    <span style="font-size:14px;font-weight:700;color:var(--dark)"><?php echo e($patient->name); ?></span>
    <div style="display:flex;align-items:center;gap:6px;margin-left:4px">
      <?php if($visit->status === 'draft'): ?>
      <div class="status-dot"></div>
      <span style="font-size:12px;color:var(--green);font-weight:600"><?php echo e($statusDisplay); ?></span>
      <?php else: ?>
      <span style="font-size:12px;color:var(--text3);font-weight:600"><?php echo e($statusDisplay); ?></span>
      <?php endif; ?>
    </div>
    <div style="margin-left:auto;display:flex;gap:8px">
      <a href="<?php echo e(route('whatsapp.index')); ?>" style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;border:1px solid var(--border);background:transparent;color:var(--text2);text-decoration:none">💬 WhatsApp</a>
      <a href="<?php echo e(route('billing.create')); ?>?patient_id=<?php echo e($patient->id); ?>&visit_id=<?php echo e($visit->id); ?>" style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;border:1px solid var(--border);background:transparent;color:var(--text2);text-decoration:none">🧾 Create Invoice</a>
      <?php if($visit->status !== 'finalised'): ?>
      <button type="button" onclick="completeVisit()" style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;border:none;background:var(--green);color:white">✓ Complete Visit</button>
      <?php endif; ?>
    </div>
  </div>

  
  <div class="patient-header-bar">
    <div class="pat-avatar"><?php echo e(substr($patient->name, 0, 1)); ?></div>
    <div>
      <div class="pat-name"><?php echo e($patient->name); ?></div>
      <div class="pat-meta">
        <div class="meta-chip">Age: <span><?php echo e($patient->age_years ?? 'N/A'); ?><?php echo e($patient->sex ? strtoupper(substr($patient->sex, 0, 1)) : ''); ?></span></div>
        <?php if($patient->dob): ?>
        <div class="meta-chip">DOB: <span><?php echo e(\Carbon\Carbon::parse($patient->dob)->format('d M Y')); ?></span></div>
        <?php endif; ?>
        <div class="meta-chip">📞 <span><?php echo e($patient->phone); ?></span></div>
        <?php if($patient->blood_group): ?>
        <div class="meta-chip">Blood: <span><?php echo e($patient->blood_group); ?></span></div>
        <?php endif; ?>
        <div class="meta-chip">Visit #: <span><?php echo e($visit->visit_number ?? $patient->visit_count ?? 1); ?></span></div>
        <?php if($patient->abha_id): ?>
        <div class="abha-chip">🛡️ ABHA: <?php echo e($patient->abha_id); ?></div>
        <?php endif; ?>
        <?php if($visit->specialty): ?>
        <div class="specialty-badge"><?php echo e(ucfirst($visit->specialty)); ?></div>
        <?php endif; ?>
        <?php if($visit->status === 'draft'): ?>
        <div class="status-pill-active">
          <div class="status-dot"></div>
          In Consultation
        </div>
        <?php endif; ?>
      </div>
    </div>
    <div class="pat-actions" style="margin-left:auto;display:flex;gap:8px">
      <a href="<?php echo e(route('patients.edit', $patient)); ?>" style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;border:1px solid var(--border);background:transparent;color:var(--text2);text-decoration:none">Edit Profile</a>
      <a href="<?php echo e(route('patients.show', $patient)); ?>" style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;border:1px solid var(--border);background:transparent;color:var(--text2);text-decoration:none">Medical History</a>
    </div>
  </div>

  
  <?php $photoCount = ($patientPhotos ?? collect())->flatten()->count(); ?>
  <div class="emr-tabs">
    <div class="emr-tab" :class="{ 'active': activeTab === 'visit' }" @click="activeTab = 'visit'">📋 Visit Note</div>
    <div class="emr-tab" :class="{ 'active': activeTab === 'prescription' }" @click="activeTab = 'prescription'">💊 Prescription <?php if($prescription): ?>(<?php echo e($prescription->drugs->count()); ?>)<?php endif; ?></div>
    <div class="emr-tab" :class="{ 'active': activeTab === 'photos' }" @click="activeTab = 'photos'">📷 Photos <?php if($photoCount > 0): ?>(<?php echo e($photoCount); ?>)<?php endif; ?></div>
    <div class="emr-tab" :class="{ 'active': activeTab === 'progress' }" @click="activeTab = 'progress'">📈 Progress</div>
    <div class="emr-tab" :class="{ 'active': activeTab === 'investigations' }" @click="activeTab = 'investigations'">🔬 Investigations <?php if(($labOrders ?? collect())->count() > 0): ?>(<?php echo e($labOrders->count()); ?>)<?php endif; ?></div>
    <div class="emr-tab" :class="{ 'active': activeTab === 'billing' }" @click="activeTab = 'billing'">🧾 Billing <?php if($visit->invoice): ?>✓<?php endif; ?></div>
    <?php if(($customTemplates ?? collect())->count() > 0): ?>
    <div class="emr-tab" :class="{ 'active': activeTab === 'custom' }" @click="activeTab = 'custom'">Custom Fields</div>
    <?php endif; ?>
  </div>

  
  <div class="emr-body">

    
    <div class="emr-sidebar">
      <div class="timeline-header">Visit History</div>

      <?php $__currentLoopData = $visitHistory ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $historyVisit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <a href="<?php echo e(route('emr.show', [$patient, $historyVisit])); ?>" 
         class="visit-card <?php echo e($historyVisit->id === $visit->id ? 'active-visit' : ''); ?>" 
         style="text-decoration:none;display:block">
        <div class="visit-date">
          <?php if($historyVisit->created_at->isToday()): ?>
            Today · <?php echo e($historyVisit->created_at->format('d M Y')); ?>

          <?php else: ?>
            <?php echo e($historyVisit->created_at->format('d M Y')); ?>

          <?php endif; ?>
        </div>
        <div class="visit-type">
          <?php if($historyVisit->visit_number === 1): ?>
            Initial Consultation
          <?php else: ?>
            Follow-up #<?php echo e($historyVisit->visit_number); ?>

          <?php endif; ?>
          <?php if($historyVisit->chief_complaint): ?>
            · <?php echo e(Str::limit($historyVisit->chief_complaint, 20)); ?>

          <?php endif; ?>
        </div>
        <?php if($historyVisit->diagnosis_text): ?>
        <div class="visit-summary"><?php echo e(Str::limit($historyVisit->diagnosis_text, 60)); ?></div>
        <?php endif; ?>
        <div class="visit-chips">
          <?php if($historyVisit->id === $visit->id): ?>
            <span class="vchip" style="background:#ecfdf5;color:#059669">Current</span>
          <?php endif; ?>
          <?php if($historyVisit->status === 'finalised'): ?>
            <span class="vchip" style="background:#f1f5f9;color:#64748b">Completed</span>
          <?php elseif($historyVisit->status === 'draft'): ?>
            <span class="vchip" style="background:#eff3ff;color:#1447e6">In Progress</span>
          <?php endif; ?>
          <?php if($historyVisit->prescriptions->isNotEmpty()): ?>
            <span class="vchip" style="background:#f1f5f9;color:#64748b">Rx</span>
          <?php endif; ?>
        </div>
      </a>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

      <?php if(($visitHistory ?? collect())->isEmpty()): ?>
      <div style="padding:16px;text-align:center;color:var(--text3);font-size:12px">
        This is the first visit for this patient
      </div>
      <?php endif; ?>

      
      <?php
        $alertsAllergiesLine = $patient->getAllergiesString();
        $alertsConditionsLine = $patient->getConditionsString();
      ?>
      <div style="margin-top:16px;padding:12px;background:var(--bg);border-radius:10px">
        <div style="font-size:11px;font-weight:700;color:var(--text3);letter-spacing:.05em;text-transform:uppercase;margin-bottom:10px">Alerts</div>
        <div style="display:flex;flex-direction:column;gap:6px">
          <?php if($alertsAllergiesLine !== ''): ?>
          <div style="display:flex;gap:6px;align-items:flex-start;font-size:11px">
            <span>⚠️</span>
            <span style="color:var(--red);font-weight:500">Allergies: <?php echo e($alertsAllergiesLine); ?></span>
          </div>
          <?php endif; ?>
          
          <?php if($previousPrescriptions && $previousPrescriptions->isNotEmpty()): ?>
          <?php $lastRx = $previousPrescriptions->first(); ?>
          <div style="display:flex;gap:6px;align-items:flex-start;font-size:11px">
            <span>💊</span>
            <span style="color:var(--text2)">Last Rx: <?php echo e($lastRx->created_at->format('d M')); ?> — 
              <?php $__currentLoopData = $lastRx->drugs->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $drug): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo e($drug->drug_name); ?><?php echo e(!$loop->last ? ', ' : ''); ?>

              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              <?php if($lastRx->drugs->count() > 2): ?> +<?php echo e($lastRx->drugs->count() - 2); ?> more <?php endif; ?>
            </span>
          </div>
          <?php endif; ?>

          <?php $photoCount = ($patientPhotos ?? collect())->flatten()->count(); ?>
          <?php if($photoCount > 0): ?>
          <div style="display:flex;gap:6px;align-items:flex-start;font-size:11px">
            <span>📸</span>
            <span style="color:var(--blue);font-weight:500"><?php echo e($photoCount); ?> photos on file</span>
          </div>
          <?php endif; ?>

          <?php if($alertsConditionsLine !== ''): ?>
          <div style="display:flex;gap:6px;align-items:flex-start;font-size:11px">
            <span>🩺</span>
            <span style="color:var(--amber);font-weight:500"><?php echo e(Str::limit($alertsConditionsLine, 50)); ?></span>
          </div>
          <?php endif; ?>

          <?php if($alertsAllergiesLine === '' && $alertsConditionsLine === '' && (!$previousPrescriptions || $previousPrescriptions->isEmpty()) && $photoCount === 0): ?>
          <div style="font-size:11px;color:var(--text3)">No alerts for this patient</div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    
    <div class="emr-main">

      
      <div x-show="activeTab === 'visit'">

        
        <div class="ai-dictation-strip">
          <div class="ai-strip-icon">🎙️</div>
          <div class="ai-strip-text">
            <h4 x-text="dictationBusy ? '⏳ Transcribing & mapping…' : (recording ? '🔴 Recording… tap to stop' : 'AI Dictation Mode')"></h4>
            <p>Speak in any language (English, Hindi, Marathi, Telugu, etc.) — audio is sent to Whisper, then AI maps to your note fields. Choose a hint language or Auto-detect.</p>
          </div>
          <div class="ai-strip-actions" style="display:flex;flex-wrap:wrap;align-items:center;gap:8px">
            <label style="display:inline-flex;align-items:center;gap:6px;font-size:11px;color:var(--text2)">
              <span>Speech</span>
              <select class="field-select" style="min-width:140px;padding:6px 8px;font-size:12px" x-model="dictationLang" :disabled="recording || dictationBusy">
                <option value="auto">Auto-detect</option>
                <option value="en">English</option>
                <option value="hi">Hindi</option>
                <option value="mr">Marathi</option>
                <option value="te">Telugu</option>
                <option value="ta">Tamil</option>
                <option value="kn">Kannada</option>
                <option value="ml">Malayalam</option>
                <option value="bn">Bengali</option>
                <option value="gu">Gujarati</option>
                <option value="pa">Punjabi</option>
              </select>
            </label>
            <button type="button"
              @click="window.emrToggleDictation && window.emrToggleDictation()"
              :disabled="dictationBusy"
              :style="(recording ? 'background:#dc2626' : '') + (dictationBusy ? ';opacity:0.7;cursor:wait' : '')"
              style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;border:none;background:var(--blue);color:white;transition:all .2s">
              <span x-text="dictationBusy ? '⏳ Working…' : (recording ? '⏹ Stop' : '🎤 Start Dictation')"></span>
            </button>
            <button type="button"
              @click="window.emrAiSummarise && window.emrAiSummarise()"
              :disabled="aiSummarising"
              :style="aiSummarising ? 'opacity:0.6;cursor:wait' : ''"
              style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;border:1px solid var(--border);background:transparent;color:var(--text2)">
              <span x-text="aiSummarising ? '⏳ Summarising…' : '✨ AI Summarise'"></span>
            </button>
          </div>
        </div>

        <?php
          $priorVisitsForSummary = collect($visitHistory ?? [])->filter(fn ($v) => $v->id !== $visit->id)->take(5);
        ?>
        <?php if($priorVisitsForSummary->isNotEmpty()): ?>
        <div style="margin-bottom:16px;padding:14px 16px;background:#f0f9ff;border:1px solid #bae6fd;border-radius:10px;font-size:12px;color:var(--text2)">
          <div style="font-weight:700;margin-bottom:8px;color:#0369a1">Previous visits &amp; treatment</div>
          <?php $__currentLoopData = $priorVisitsForSummary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div style="margin-bottom:10px;padding-bottom:10px;border-bottom:1px solid #e0f2fe">
              <div style="font-weight:600"><?php echo e($pv->created_at->format('d M Y')); ?><?php if($pv->doctor): ?> · <?php echo e($pv->doctor->name); ?> <?php endif; ?></div>
              <?php if($pv->chief_complaint): ?><div style="margin-top:4px"><strong>Complaint:</strong> <?php echo e(\Illuminate\Support\Str::limit($pv->chief_complaint, 220)); ?></div><?php endif; ?>
              <?php if($pv->history): ?><div style="margin-top:4px"><strong>History:</strong> <?php echo e(\Illuminate\Support\Str::limit($pv->history, 300)); ?></div><?php endif; ?>
              <?php if($pv->diagnosis_text): ?><div style="margin-top:4px"><strong>Diagnosis:</strong> <?php echo e(\Illuminate\Support\Str::limit($pv->diagnosis_text, 220)); ?></div><?php endif; ?>
              <?php if($pv->plan): ?><div style="margin-top:4px"><strong>Plan:</strong> <?php echo e(\Illuminate\Support\Str::limit($pv->plan, 220)); ?></div><?php endif; ?>
            </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>

        
        <div class="form-section" x-data="{sectionOpen:true}">
          <div class="form-section-header" @click="sectionOpen=!sectionOpen">
            <h3>Chief Complaint &amp; History</h3>
            <span class="toggle" x-text="sectionOpen ? '−' : '+'"></span>
          </div>
          <div class="form-body" x-show="sectionOpen">
            <?php
              $emrSpecSel = strtolower((string) ($visit->specialty ?? auth()->user()->specialty ?? 'general'));
              if ($emrSpecSel === 'dentistry') {
                  $emrSpecSel = 'dental';
              }
              if (in_array($emrSpecSel, ['orthopaedic', 'orthopaedics', 'ortho'], true)) {
                  $emrSpecSel = 'orthopedics';
              }
              $emrSpecOptions = [
                  'general' => 'General',
                  'dermatology' => 'Dermatology',
                  'dental' => 'Dental',
                  'ophthalmology' => 'Ophthalmology',
                  'pediatrics' => 'Pediatrics',
                  'orthopedics' => 'Orthopaedics',
                  'physiotherapy' => 'Physiotherapy',
                  'ent' => 'ENT',
                  'gynaecology' => 'Gynaecology',
                  'gynecology' => 'Gynecology',
                  'cardiology' => 'Cardiology',
              ];
            ?>
            <div class="form-row form-row-2">
              <div class="field-group" style="grid-column:1/-1;max-width:min(100%,420px)">
                <div class="field-label">Visit specialty (documentation template)</div>
                <select name="specialty" id="emr-visit-specialty" class="field-select"
                  <?php if($visit->status === 'finalised'): ?> disabled title="Specialty is locked for completed visits" <?php endif; ?>
                  onchange="window.emrOnChangeVisitSpecialty && window.emrOnChangeVisitSpecialty(this)">
                  <?php $__currentLoopData = $emrSpecOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($val); ?>" <?php if($emrSpecSel === $val): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  <?php if($emrSpecSel !== '' && !array_key_exists($emrSpecSel, $emrSpecOptions)): ?>
                    <option value="<?php echo e($emrSpecSel); ?>" selected><?php echo e(ucfirst(str_replace('_', ' ', $emrSpecSel))); ?> (current)</option>
                  <?php endif; ?>
                </select>
                <div style="font-size:11px;color:var(--text3);margin-top:4px">Chooses clinical sections for this visit (e.g. dental chart). Saving reloads the page.</div>
              </div>
            </div>
            <div class="form-row form-row-2">
              <div class="field-group">
                <div class="field-label">Chief Complaint</div>
                <select name="chief_complaint" class="field-select" @change="window.triggerAutoSave()">
                  <option value="">Select complaint...</option>
                  <?php $__currentLoopData = $commonComplaints; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $complaint): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($complaint); ?>" <?php echo e(($visit->chief_complaint ?? '') === $complaint ? 'selected' : ''); ?>><?php echo e($complaint); ?></option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  <option value="other" <?php echo e(!in_array($visit->chief_complaint ?? '', $commonComplaints) && $visit->chief_complaint ? 'selected' : ''); ?>>Other</option>
                </select>
              </div>
              <div class="field-group">
                <div class="field-label">Duration</div>
                <input name="duration" class="field-input" value="<?php echo e($visit->getStructuredField('duration', '')); ?>" type="text" placeholder="e.g. 2 weeks, 3 months" @input="window.triggerAutoSave()"/>
              </div>
            </div>
            <div class="form-row form-row-3">
              <div class="field-group">
                <div class="field-label">Onset</div>
                <select name="onset" class="field-select" @change="window.triggerAutoSave()">
                  <option value="">Select...</option>
                  <option value="gradual" <?php echo e($visit->getStructuredField('onset') === 'gradual' ? 'selected' : ''); ?>>Gradual</option>
                  <option value="sudden" <?php echo e($visit->getStructuredField('onset') === 'sudden' ? 'selected' : ''); ?>>Sudden</option>
                  <option value="recurrent" <?php echo e($visit->getStructuredField('onset') === 'recurrent' ? 'selected' : ''); ?>>Recurrent</option>
                </select>
              </div>
              <div class="field-group">
                <div class="field-label">Progression</div>
                <select name="progression" class="field-select" @change="window.triggerAutoSave()">
                  <option value="">Select...</option>
                  <option value="worsening" <?php echo e($visit->getStructuredField('progression') === 'worsening' ? 'selected' : ''); ?>>Worsening</option>
                  <option value="improving" <?php echo e($visit->getStructuredField('progression') === 'improving' ? 'selected' : ''); ?>>Improving</option>
                  <option value="static" <?php echo e($visit->getStructuredField('progression') === 'static' ? 'selected' : ''); ?>>Static</option>
                  <option value="fluctuating" <?php echo e($visit->getStructuredField('progression') === 'fluctuating' ? 'selected' : ''); ?>>Fluctuating</option>
                </select>
              </div>
              <div class="field-group">
                <div class="field-label">Previous Treatment</div>
                <select name="previous_treatment" class="field-select" @change="window.triggerAutoSave()">
                  <option value="">Select...</option>
                  <option value="yes_ongoing" <?php echo e($visit->getStructuredField('previous_treatment') === 'yes_ongoing' ? 'selected' : ''); ?>>Yes — On treatment</option>
                  <option value="yes_stopped" <?php echo e($visit->getStructuredField('previous_treatment') === 'yes_stopped' ? 'selected' : ''); ?>>Yes — Stopped</option>
                  <option value="no" <?php echo e($visit->getStructuredField('previous_treatment') === 'no' ? 'selected' : ''); ?>>No</option>
                </select>
              </div>
            </div>
            <div class="field-group">
              <div class="field-label">History Notes</div>
              <textarea name="history" class="field-textarea" placeholder="Document relevant medical history, symptoms, triggers, and any other relevant information..." @input="window.triggerAutoSave()"><?php echo e($visit->history); ?></textarea>
            </div>
          </div>
        </div>

        
        <?php
            $specialty = strtolower($visit->specialty ?? auth()->user()->specialty ?? 'general');
            // Maps visit.specialty (dropdown/API values) → Blade under resources/views/emr/specialty/
            $specialtyTemplates = [
                'dermatology' => 'emr.specialty.dermatology',
                'dental' => 'emr.specialty.dental',
                'dentistry' => 'emr.specialty.dental',
                'physiotherapy' => 'emr.specialty.physiotherapy',
                'orthopaedic' => 'emr.specialty.orthopaedics',
                'orthopedics' => 'emr.specialty.orthopaedics',
                'orthopaedics' => 'emr.specialty.orthopaedics',
                'ortho' => 'emr.specialty.orthopaedics',
                'ophthalmology' => 'emr.specialty.ophthalmology',
                'eye' => 'emr.specialty.ophthalmology',
                'ent' => 'emr.specialty.ent',
                'gynaecology' => 'emr.specialty.gynaecology',
                'gynecology' => 'emr.specialty.gynaecology',
                'obstetrics' => 'emr.specialty.gynaecology',
                'general' => 'emr.specialty.general_physician',
                'general_physician' => 'emr.specialty.general_physician',
                'cardiology' => 'emr.specialty.cardiology',
                'pediatrics' => 'emr.specialty.paediatrics',
                'paediatrics' => 'emr.specialty.paediatrics',
                'gastroenterology' => 'emr.specialty.gastroenterology',
                'nephrology' => 'emr.specialty.nephrology',
                'endocrinology' => 'emr.specialty.endocrinology',
                'diabetology' => 'emr.specialty.diabetology',
                'pulmonology' => 'emr.specialty.pulmonology',
                'neurology' => 'emr.specialty.neurology',
                'oncology' => 'emr.specialty.oncology',
                'psychiatry' => 'emr.specialty.psychiatry',
                'rheumatology' => 'emr.specialty.rheumatology',
                'urology' => 'emr.specialty.urology',
                'general_surgery' => 'emr.specialty.general_surgery',
                'ayush' => 'emr.specialty.ayush',
                'homeopathy' => 'emr.specialty.homeopathy',
            ];
            $templateToUse = $specialtyTemplates[$specialty] ?? null;
            $emrTemplateBannerLabel = match ($specialty) {
                'general', 'general_physician' => 'General',
                'pediatrics', 'paediatrics' => 'Pediatrics',
                default => ucfirst(str_replace('_', ' ', $specialty)),
            };

            \Log::info('EMR specialty template selection', [
                'visit_specialty' => $visit->specialty,
                'user_specialty' => auth()->user()->specialty,
                'resolved_specialty' => $specialty,
                'template' => $templateToUse,
                'banner_label' => $emrTemplateBannerLabel,
            ]);
        ?>

        
        <?php if($templateToUse && view()->exists($templateToUse)): ?>
            <div style="margin-bottom:12px;padding:10px 14px;background:linear-gradient(135deg, rgba(20,71,230,0.08), rgba(8,145,178,0.08));border-radius:10px;border:1px solid rgba(20,71,230,0.15)">
                <div style="display:flex;align-items:center;gap:8px">
                    <span style="font-size:16px">
                        <?php switch($specialty):
                            case ('dermatology'): ?> 🩺 <?php break; ?>
                            <?php case ('dental'): ?> 🦷 <?php break; ?>
                            <?php case ('physiotherapy'): ?>
                            <?php case ('orthopaedic'): ?>
                            <?php case ('orthopedics'): ?>
                            <?php case ('orthopaedics'): ?>
                            <?php case ('ortho'): ?> 💪 <?php break; ?>
                            <?php case ('ophthalmology'): ?>
                            <?php case ('eye'): ?> 👁️ <?php break; ?>
                            <?php case ('ent'): ?> 👂 <?php break; ?>
                            <?php case ('gynaecology'): ?>
                            <?php case ('gynecology'): ?>
                            <?php case ('obstetrics'): ?> 🤰 <?php break; ?>
                            <?php case ('cardiology'): ?> 🫀 <?php break; ?>
                            <?php case ('general'): ?>
                            <?php case ('general_physician'): ?> 📋 <?php break; ?>
                            <?php case ('pediatrics'): ?>
                            <?php case ('paediatrics'): ?> 👶 <?php break; ?>
                            <?php default: ?> 📋
                        <?php endswitch; ?>
                    </span>
                    <span style="font-size:13px;font-weight:600;color:var(--dark)"><?php echo e($emrTemplateBannerLabel); ?> EMR Template</span>
                    <span style="margin-left:auto;font-size:11px;color:var(--text3)">Specialty-specific fields below</span>
                </div>
            </div>
            
            <?php echo $__env->make($templateToUse, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php else: ?>
            
            
            
            <div class="form-section">
              <div class="form-section-header">
                <h3>Lesion Map &amp; Skin Findings</h3>
                <span class="toggle">−</span>
              </div>
              <div class="form-body">
                <div class="body-map-container">
                  <div>
                    <div class="body-diagram" title="Tap to add lesion">
                      <div style="font-size:11px;font-weight:600;color:var(--text3);margin-bottom:10px">Tap to annotate</div>
                      <svg viewBox="0 0 80 160" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <ellipse cx="40" cy="16" rx="14" ry="15" fill="#d1d5db"/>
                        <rect x="22" y="32" width="36" height="48" rx="8" fill="#d1d5db"/>
                        <rect x="7" y="33" width="14" height="38" rx="7" fill="#d1d5db"/>
                        <rect x="59" y="33" width="14" height="38" rx="7" fill="#d1d5db"/>
                        <rect x="23" y="82" width="15" height="50" rx="7" fill="#d1d5db"/>
                        <rect x="42" y="82" width="15" height="50" rx="7" fill="#d1d5db"/>
                        
                        <circle cx="36" cy="18" r="5" fill="#ef4444" opacity=".8"/>
                        <circle cx="46" cy="14" r="4" fill="#ef4444" opacity=".6"/>
                        <circle cx="33" cy="28" r="3.5" fill="#f59e0b" opacity=".8"/>
                        <circle cx="50" cy="42" r="4" fill="#6366f1" opacity=".6"/>
                        <circle cx="12" cy="45" r="4.5" fill="#6366f1" opacity=".7"/>
                      </svg>
                      <div style="font-size:10px;color:var(--text3);margin-top:8px">Front View</div>
                    </div>
                  </div>
                  <div class="lesion-annotations">
                    <div style="font-size:11px;font-weight:700;color:var(--text3);letter-spacing:.06em;text-transform:uppercase;margin-bottom:10px">Recorded Lesions</div>
                    
                    <?php $__empty_1 = true; $__currentLoopData = $visit->lesions ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lesion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="lesion-row" data-lesion-id="<?php echo e($lesion->id); ?>">
                      <div class="lesion-color" style="background:<?php echo e($lesion->colour ?? '#ef4444'); ?>"></div>
                      <div class="lesion-desc">
                        <strong style="color:var(--dark)"><?php echo e($lesion->body_region); ?></strong> · <?php echo e($lesion->lesion_type); ?>

                        <?php if($lesion->size_cm): ?> · <?php echo e($lesion->size_cm); ?> cm <?php endif; ?>
                        <?php if($lesion->notes): ?>
                        <div style="font-size:11px;color:var(--text3)"><?php echo e($lesion->notes); ?></div>
                        <?php endif; ?>
                        <?php if($lesion->distribution || $lesion->surface): ?>
                        <div style="font-size:11px;color:var(--text3)">
                          <?php if($lesion->distribution): ?>Distribution: <?php echo e($lesion->distribution); ?><?php endif; ?>
                          <?php if($lesion->distribution && $lesion->surface): ?> · <?php endif; ?>
                          <?php if($lesion->surface): ?>Surface: <?php echo e($lesion->surface); ?><?php endif; ?>
                        </div>
                        <?php endif; ?>
                      </div>
                      <button type="button" class="lesion-remove" onclick="removeLesion(<?php echo e($lesion->id); ?>)" title="Remove">✕</button>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div style="padding:16px;text-align:center;color:var(--text3);font-size:12px;background:var(--bg);border-radius:8px">
                      No lesions recorded yet. Click the body diagram to add annotations.
                    </div>
                    <?php endif; ?>
                    
                    <button type="button" 
                            onclick="openAddLesionModal()"
                            style="display:flex;align-items:center;gap:6px;background:none;border:1.5px dashed var(--border);border-radius:8px;padding:8px 14px;font-size:12px;color:var(--text3);cursor:pointer;margin-top:8px;font-family:'Inter',sans-serif;width:100%;justify-content:center">
                      ＋ Add lesion annotation
                    </button>
                  </div>
                </div>
              </div>
            </div>

            
            <div class="form-section">
              <div class="form-section-header">
                <h3>Clinical Grading Scales</h3>
                <span class="toggle">−</span>
              </div>
              <div class="form-body">
                <?php
                  $scales = $visit->scales ?? collect();
                  $pasiScale = $scales->firstWhere('scale_name', 'PASI');
                  $igaScale = $scales->firstWhere('scale_name', 'IGA');
                  $dlqiScale = $scales->firstWhere('scale_name', 'DLQI');
                ?>
                
                <div class="scale-group">
                  <div class="scale-label">PASI Score</div>
                  <input name="scales[pasi]" class="scale-input" value="<?php echo e($pasiScale?->score ?? ''); ?>" type="number" step="0.1" min="0" max="72" placeholder="0-72" @input="window.triggerAutoSave()"/>
                  <div class="scale-range">(0–72)</div>
                  <?php if($pasiScale): ?>
                    <?php
                      $pasiSeverity = $pasiScale->score <= 5 ? 'mild' : ($pasiScale->score <= 12 ? 'mod' : 'sev');
                      $pasiLabel = $pasiScale->score <= 5 ? 'Mild' : ($pasiScale->score <= 12 ? 'Moderate' : 'Severe');
                    ?>
                    <div class="scale-result sr-<?php echo e($pasiSeverity); ?>"><?php echo e($pasiLabel); ?></div>
                  <?php endif; ?>
                  <?php if(isset($scaleChanges['PASI'])): ?>
                    <div style="font-size:11px;color:var(--text3);margin-left:8px">
                      <?php echo e($scaleChanges['PASI']['change'] > 0 ? '↑' : '↓'); ?><?php echo e(abs($scaleChanges['PASI']['change'])); ?> vs last
                    </div>
                  <?php endif; ?>
                </div>
                
                <div class="scale-group">
                  <div class="scale-label">IGA Grade</div>
                  <input name="scales[iga]" class="scale-input" value="<?php echo e($igaScale?->score ?? ''); ?>" type="number" step="1" min="0" max="4" placeholder="0-4" @input="window.triggerAutoSave()"/>
                  <div class="scale-range">(0–4)</div>
                  <?php if($igaScale): ?>
                    <?php
                      $igaSeverity = $igaScale->score <= 1 ? 'mild' : ($igaScale->score <= 2 ? 'mod' : 'sev');
                      $igaLabel = match((int)$igaScale->score) { 0 => 'Clear', 1 => 'Almost Clear', 2 => 'Mild', 3 => 'Moderate', 4 => 'Severe', default => 'Unknown' };
                    ?>
                    <div class="scale-result sr-<?php echo e($igaSeverity); ?>"><?php echo e($igaLabel); ?></div>
                  <?php endif; ?>
                  <?php if(isset($scaleChanges['IGA'])): ?>
                    <div style="font-size:11px;color:var(--text3);margin-left:8px">
                      <?php if($scaleChanges['IGA']['change'] === 0): ?> Unchanged <?php else: ?> <?php echo e($scaleChanges['IGA']['change'] > 0 ? '↑' : '↓'); ?><?php echo e(abs($scaleChanges['IGA']['change'])); ?> <?php endif; ?>
                    </div>
                  <?php endif; ?>
                </div>
                
                <div class="scale-group">
                  <div class="scale-label">DLQI Score</div>
                  <input name="scales[dlqi]" class="scale-input" value="<?php echo e($dlqiScale?->score ?? ''); ?>" type="number" step="1" min="0" max="30" placeholder="0-30" @input="window.triggerAutoSave()"/>
                  <div class="scale-range">(0–30)</div>
                  <?php if($dlqiScale): ?>
                    <?php
                      $dlqiSeverity = $dlqiScale->score <= 5 ? 'mild' : ($dlqiScale->score <= 10 ? 'mod' : 'sev');
                      $dlqiLabel = $dlqiScale->score <= 1 ? 'No effect' : ($dlqiScale->score <= 5 ? 'Small effect' : ($dlqiScale->score <= 10 ? 'Moderate effect' : 'Large effect on QoL'));
                    ?>
                    <div class="scale-result sr-<?php echo e($dlqiSeverity); ?>"><?php echo e($dlqiLabel); ?></div>
                  <?php endif; ?>
                  <?php if(isset($scaleChanges['DLQI'])): ?>
                    <div style="font-size:11px;color:var(--text3);margin-left:8px">
                      <?php echo e($scaleChanges['DLQI']['change'] > 0 ? '↑' : '↓'); ?><?php echo e(abs($scaleChanges['DLQI']['change'])); ?> vs last
                    </div>
                  <?php endif; ?>
                </div>

                <?php if($previousVisit && $previousVisit->scales->isNotEmpty()): ?>
                <div style="margin-top:12px;padding:12px;background:var(--blue-light);border-radius:8px;font-size:12px;color:var(--blue)">
                  <strong>vs Last Visit (<?php echo e($previousVisit->created_at->format('d M')); ?>):</strong>
                  <?php $__currentLoopData = $scaleChanges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $change): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo e($name); ?> <?php echo e($change['previous']); ?> → <?php echo e($change['current']); ?> 
                    (<?php echo e($change['change'] > 0 ? '↑' : ($change['change'] < 0 ? '↓' : '=')); ?><?php echo e(abs($change['change'])); ?>)<?php echo e(!$loop->last ? ' · ' : ''); ?>

                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endif; ?>
              </div>
            </div>
        <?php endif; ?>

        
        <?php
          $__emrProcSelected = $visit->procedures->pluck('procedure_name')->values()->all();
          $__emrProcList = $availableProcedures;
          \Illuminate\Support\Facades\Log::debug('EMR show: procedure Alpine payload', [
            'visit_id' => $visit->id,
            'selected_count' => count($__emrProcSelected),
            'options_count' => count($__emrProcList ?? []),
          ]);
        ?>
        <script>
          window.emrProcedureBlock = function (selectedProcs, procs) {
            if (typeof console !== 'undefined' && console.debug) {
              console.debug('[EMR procedures] emrProcedureBlock init', { nSelected: (selectedProcs || []).length, nOptions: (procs || []).length });
            }
            return {
              selectedProcs: Array.isArray(selectedProcs) ? selectedProcs : [],
              procs: Array.isArray(procs) ? procs : [],
              toggleProc(proc) {
                const idx = this.selectedProcs.indexOf(proc);
                if (idx >= 0) this.selectedProcs.splice(idx, 1);
                else this.selectedProcs.push(proc);
              },
            };
          };
        </script>
        <div class="form-section">
          <div class="form-section-header">
            <h3>Procedure Performed Today</h3>
            <span class="toggle">−</span>
          </div>
          <div class="form-body" x-data="emrProcedureBlock(<?php echo json_encode($__emrProcSelected, 15, 512) ?>, <?php echo json_encode($__emrProcList, 15, 512) ?>)">
            <div style="margin-bottom:12px">
              <div class="field-label" style="margin-bottom:8px">Select Procedure</div>
              <div class="proc-grid">
                <template x-for="proc in procs" :key="proc">
                  <div class="proc-chip"
                       :class="selectedProcs.includes(proc) && 'selected'"
                       @click="toggleProc(proc); window.triggerAutoSave()"
                       x-text="proc"></div>
                </template>
              </div>
              <template x-for="proc in selectedProcs" :key="proc">
                <input type="hidden" name="procedures[]" :value="proc"/>
              </template>
            </div>
            
            <?php 
              $firstProc = $visit->procedures->first(); 
              $procParams = $firstProc?->parameters ?? [];
            ?>
            <div class="form-row form-row-3">
              <div class="field-group">
                <div class="field-label">Agent / Product</div>
                <input name="procedure_agent" class="field-input" type="text" value="<?php echo e($procParams['agent'] ?? ''); ?>" placeholder="e.g. Salicylic Acid 30%" @input="window.triggerAutoSave()"/>
              </div>
              <div class="field-group">
                <div class="field-label">Areas Treated</div>
                <input name="areas_treated" class="field-input" value="<?php echo e($firstProc?->body_region ?? ''); ?>" type="text" placeholder="e.g. Full face, T-zone" @input="window.triggerAutoSave()"/>
              </div>
              <div class="field-group">
                <div class="field-label">Session No.</div>
                <input name="session_number" class="field-input" value="<?php echo e($procParams['session_number'] ?? ''); ?>" type="text" placeholder="e.g. 3 of 6" @input="window.triggerAutoSave()"/>
              </div>
            </div>
            <div class="field-group">
              <div class="field-label">Procedure Notes</div>
              <textarea name="procedure_notes" class="field-textarea" style="min-height:60px" placeholder="Document procedure details, patient tolerance, post-procedure care instructions..." @input="window.triggerAutoSave()"><?php echo e($firstProc?->notes ?? ''); ?></textarea>
            </div>
          </div>
        </div>

        
        <div class="form-section">
          <div class="form-section-header">
            <h3>Plan &amp; Follow-up</h3>
            <span class="toggle">−</span>
          </div>
          <div class="form-body">
            <div class="form-row form-row-2">
              <div class="field-group">
                <div class="field-label">Follow-up In (Days)</div>
                <select name="followup_in_days" class="field-select" @change="updateFollowupDate($event.target.value); triggerAutoSave()">
                  <option value="">Select...</option>
                  <option value="7" <?php echo e(($visit->followup_in_days ?? 0) == 7 ? 'selected' : ''); ?>>1 week</option>
                  <option value="14" <?php echo e(($visit->followup_in_days ?? 0) == 14 ? 'selected' : ''); ?>>2 weeks</option>
                  <option value="28" <?php echo e(($visit->followup_in_days ?? 0) == 28 ? 'selected' : ''); ?>>4 weeks</option>
                  <option value="42" <?php echo e(($visit->followup_in_days ?? 0) == 42 ? 'selected' : ''); ?>>6 weeks</option>
                  <option value="90" <?php echo e(($visit->followup_in_days ?? 0) == 90 ? 'selected' : ''); ?>>3 months</option>
                  <option value="0" <?php echo e(($visit->followup_in_days ?? 0) == 0 && $visit->followup_date === null ? 'selected' : ''); ?>>As needed</option>
                </select>
              </div>
              <div class="field-group">
                <div class="field-label">Follow-up Date</div>
                <input name="followup_date" id="followup_date" class="field-input" type="date" value="<?php echo e($visit->followup_date?->format('Y-m-d') ?? ''); ?>" @input="window.triggerAutoSave()"/>
              </div>
            </div>
            <div class="form-row form-row-2">
              <div class="field-group">
                <div class="field-label">Diagnosis</div>
                <input name="diagnosis_text" class="field-input" type="text" value="<?php echo e($visit->diagnosis_text ?? ''); ?>" placeholder="e.g. Acne vulgaris, Psoriasis" @input="window.triggerAutoSave()"/>
              </div>
              <div class="field-group">
                <div class="field-label">ICD-10 Code (Optional)</div>
                <input name="diagnosis_code" class="field-input" type="text" value="<?php echo e($visit->diagnosis_code ?? ''); ?>" placeholder="e.g. L70.0" @input="window.triggerAutoSave()"/>
              </div>
            </div>
            <div class="field-group">
              <div class="field-label">Plan Notes</div>
              <textarea name="plan" class="field-textarea" style="min-height:60px" placeholder="Document treatment plan, patient counselling, and any special instructions..." @input="window.triggerAutoSave()"><?php echo e($visit->plan); ?></textarea>
            </div>
          </div>
        </div>

      </div>

      
      <?php
        $__emrRxDrugsForAlpine = ($prescription?->drugs ?? collect())->map(function ($d) {
          return [
            'id' => $d->id,
            'name' => $d->drug_name,
            'generic' => $d->generic_name,
            'dose' => $d->dose,
            'frequency' => $d->frequency,
            'duration' => $d->duration,
            'instructions' => $d->instructions,
          ];
        })->values()->all();
        \Illuminate\Support\Facades\Log::debug('EMR show: prescription Alpine drugs', [
          'visit_id' => $visit->id,
          'rows' => count($__emrRxDrugsForAlpine),
        ]);
      ?>

      
      <script>
        window.emrPrescriptionAlpineFactory = function () {
          return {
            drugs: <?php echo json_encode($__emrRxDrugsForAlpine, 15, 512) ?>,
            drugSearchUrl: <?php echo json_encode(route('api.drugs.search'), 15, 512) ?>,
            drugSearch: '',
            showSuggestions: false,
            suggestions: [],
            patientAllergies: <?php echo json_encode($patientAllergiesDisplay ?? [], 15, 512) ?>,
            allergyWarnings: [],
            init() {
              console.log('Prescription tab initialized', {
                existingDrugCount: this.drugs.length,
                allergyCount: this.patientAllergies.length,
                drugSearchUrl: this.drugSearchUrl,
              });
              this.recalculateSafetyWarnings();
            },
            recalculateSafetyWarnings() {
              this.allergyWarnings = [];
              if (!Array.isArray(this.patientAllergies) || this.patientAllergies.length === 0) {
                console.log('No known allergies available for current patient');
                return;
              }

              const warnings = [];
              for (const drug of this.drugs) {
                const drugName = String(drug?.name || '').toLowerCase();
                const genericName = String(drug?.generic || '').toLowerCase();
                for (const allergyRaw of this.patientAllergies) {
                  const allergy = String(allergyRaw || '').trim();
                  if (!allergy) continue;
                  const allergyLower = allergy.toLowerCase();
                  if ((drugName && drugName.includes(allergyLower)) || (genericName && genericName.includes(allergyLower))) {
                    warnings.push({
                      allergy,
                      drugName: drug?.name || 'Unknown drug',
                      message: 'Potential allergy conflict: ' + (drug?.name || 'Drug') + ' may match patient allergy (' + allergy + ').'
                    });
                  }
                }
              }

              this.allergyWarnings = warnings;
              console.log('Prescription safety warnings recalculated', {
                warningCount: warnings.length,
                warnings
              });
            },
            async searchDrugs() {
              if (this.drugSearch.length < 2) {
                this.suggestions = [];
                this.showSuggestions = false;
                return;
              }
              try {
                const res = await fetch(this.drugSearchUrl + '?q=' + encodeURIComponent(this.drugSearch), {
                  credentials: 'same-origin',
                  headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!res.ok) {
                  console.warn('Drug search HTTP error', res.status);
                  this.suggestions = [];
                  this.showSuggestions = false;
                  return;
                }
                const data = await res.json();
                this.suggestions = Array.isArray(data) ? data : [];
                this.showSuggestions = this.suggestions.length > 0;
              } catch (e) {
                console.error('Drug search failed:', e);
                this.suggestions = [];
                this.showSuggestions = false;
              }
            },
            addDrug(drug) {
              this.drugs.push({
                id: null,
                name: drug.brand_name,
                generic: drug.generic_name,
                dose: '',
                frequency: '',
                duration: '',
                instructions: ''
              });
              console.log('Drug added', { brand_name: drug.brand_name, generic_name: drug.generic_name });
              this.drugSearch = '';
              this.showSuggestions = false;
              this.recalculateSafetyWarnings();
              this.triggerAutoSave();
            },
            removeDrug(index) {
              const removedDrug = this.drugs[index];
              this.drugs.splice(index, 1);
              console.log('Drug removed', { removedDrug, remainingCount: this.drugs.length });
              this.recalculateSafetyWarnings();
              this.triggerAutoSave();
            },
            triggerAutoSave() {
              window.dispatchEvent(new CustomEvent('emr-autosave'));
            }
          };
        };
      </script>

      
      <div x-show="activeTab === 'prescription'" x-cloak x-data="emrPrescriptionAlpineFactory()">

        <div class="form-section">
          <div class="form-section-header">
            <h3>Prescription</h3>
            <a href="#" onclick="window.print(); return false;" style="font-size:12px;color:var(--blue);font-weight:600;text-decoration:none;margin-left:auto">🖨 Print Preview</a>
          </div>
          <div class="form-body">

            
            <div class="drug-search-container">
              <input type="text"
                class="field-input"
                x-ref="drugSearchInput"
                placeholder="🔍 Search drugs — type brand or generic name..."
                x-model="drugSearch"
                @input.debounce.300ms="searchDrugs()"
                @focus="showSuggestions = suggestions.length > 0"
                @blur="setTimeout(() => showSuggestions = false, 200)"
                style="padding-left:14px"/>
              <div class="drug-autocomplete" x-show="showSuggestions" x-cloak>
                <template x-for="drug in suggestions" :key="drug.id || drug.brand_name">
                  <div class="drug-option" @mousedown="addDrug(drug)">
                    <strong x-text="drug.brand_name"></strong>
                    <span x-text="' · ' + (drug.generic_name || drug.composition || '')"></span>
                    <span x-text="drug.manufacturer ? ' · ' + drug.manufacturer : ''"></span>
                  </div>
                </template>
              </div>
            </div>

            
            <table class="drug-table">
              <thead>
                <tr>
                  <th>Drug Name</th>
                  <th>Dose</th>
                  <th>Frequency</th>
                  <th>Duration</th>
                  <th>Instructions</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <template x-for="(drug, index) in drugs" :key="index">
                  <tr>
                    <td>
                      <div class="drug-name" x-text="drug.name"></div>
                      <div class="drug-generic" x-text="drug.generic || ''"></div>
                      <input type="hidden" :name="'drugs['+index+'][name]'" :value="drug.name"/>
                      <input type="hidden" :name="'drugs['+index+'][generic]'" :value="drug.generic"/>
                    </td>
                    <td><input type="text" :name="'drugs['+index+'][dose]'" x-model="drug.dose" class="field-input" style="width:80px;padding:4px 8px" placeholder="e.g. 1 tab" @input="window.triggerAutoSave()"/></td>
                    <td><input type="text" :name="'drugs['+index+'][frequency]'" x-model="drug.frequency" class="field-input" style="width:120px;padding:4px 8px" placeholder="e.g. BD" @input="window.triggerAutoSave()"/></td>
                    <td><input type="text" :name="'drugs['+index+'][duration]'" x-model="drug.duration" class="field-input" style="width:90px;padding:4px 8px" placeholder="e.g. 2 weeks" @input="window.triggerAutoSave()"/></td>
                    <td><input type="text" :name="'drugs['+index+'][instructions]'" x-model="drug.instructions" class="field-input" style="min-width:180px;padding:4px 8px" placeholder="Special instructions" @input="window.triggerAutoSave()"/></td>
                    <td><span class="drug-remove" @click="removeDrug(index)" title="Remove">✕</span></td>
                  </tr>
                </template>
                <tr x-show="drugs.length === 0">
                  <td colspan="6" style="text-align:center;padding:24px;color:var(--text3)">
                    No drugs added yet. Search and add drugs above.
                  </td>
                </tr>
              </tbody>
            </table>

            <template x-if="allergyWarnings.length > 0">
              <div style="margin-top:12px;padding:10px 14px;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;font-size:12px;color:#b91c1c">
                <div style="font-weight:700;margin-bottom:6px">⚠️ Allergy Alerts</div>
                <template x-for="(warning, idx) in allergyWarnings" :key="idx">
                  <div style="margin-bottom:4px" x-text="warning.message"></div>
                </template>
              </div>
            </template>
            
            <button type="button" class="add-drug-btn" @click="$refs.drugSearchInput?.focus()">
              ＋ Add drug from database
            </button>
            
            <?php if(!empty($patientAllergiesDisplay)): ?>
            <div style="margin-top:12px;padding:10px 14px;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;font-size:12px;color:#dc2626">
              ⚠️ Patient Allergies: <?php echo e(implode(', ', $patientAllergiesDisplay)); ?>

            </div>
            <?php endif; ?>
          </div>
        </div>

      </div>

      
      <div x-show="activeTab === 'photos'" x-cloak>
        <div class="form-section">
          <div class="form-section-header">
            <h3>Photo Vault</h3>
            <?php $totalPhotos = ($patientPhotos ?? collect())->flatten()->count(); ?>
            <span style="font-size:12px;color:var(--text3)"><?php echo e($totalPhotos); ?> photos</span>
          </div>
          <div class="form-body">
            
            <form id="emr-photo-vault-form" action="<?php echo e(route('patients.upload-photo', $patient)); ?>" method="POST" enctype="multipart/form-data" class="upload-zone" style="margin-bottom:20px"
                  ondragover="event.preventDefault(); this.style.borderColor='var(--blue)'"
                  ondragleave="this.style.borderColor=''"
                  ondrop="event.preventDefault(); this.style.borderColor=''; (function(f){ var inp=document.getElementById('emr-photo-file-input'); if(!inp||!f||!f.type||f.type.indexOf('image')!==0)return; var dt=new DataTransfer(); dt.items.add(f); inp.files=dt.files; if(inp.files.length) inp.form.submit(); })(event.dataTransfer.files[0])">
              <?php echo csrf_field(); ?>
              <input type="hidden" name="visit_id" value="<?php echo e($visit->id); ?>"/>
              <input type="hidden" name="consent_obtained" value="1"/>
              <input type="hidden" name="photo_type" id="emr-photo-type-input" value="before"/>
              <input type="file" name="photo" id="emr-photo-file-input" accept="image/jpeg,image/png,image/gif,image/webp" style="display:none"
                     onchange="if (this.files && this.files.length) { this.form.submit(); }"/>
              <div style="font-size:32px;margin-bottom:8px">📷</div>
              <p style="font-size:14px;font-weight:600;color:var(--text2)">Drop photos here or <button type="button" style="color:var(--blue);background:none;border:none;padding:0;font:inherit;cursor:pointer;text-decoration:underline" onclick="document.getElementById('emr-photo-type-input').value='before'; document.getElementById('emr-photo-file-input').click();">click to upload</button></p>
              <p style="font-size:12px;color:var(--text3);margin-top:4px">JPEG, PNG · Max 10MB each</p>
              <div style="margin-top:16px;display:flex;gap:8px;justify-content:center;flex-wrap:wrap">
                <button type="button" style="cursor:pointer;border:none;font:inherit;display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;font-size:12px;font-weight:600;background:var(--blue);color:white"
                        onclick="document.getElementById('emr-photo-type-input').value='before'; document.getElementById('emr-photo-file-input').click();">📸 Before Photo</button>
                <button type="button" style="cursor:pointer;border:none;font:inherit;display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;font-size:12px;font-weight:600;background:var(--teal);color:white"
                        onclick="document.getElementById('emr-photo-type-input').value='after'; document.getElementById('emr-photo-file-input').click();">📸 After Photo</button>
                <button type="button" style="cursor:pointer;border:none;font:inherit;display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;font-size:12px;font-weight:600;border:1px solid var(--border);color:var(--text2);background:white"
                        onclick="document.getElementById('emr-photo-type-input').value='progress'; document.getElementById('emr-photo-file-input').click();">📸 Progress Photo</button>
              </div>
            </form>

            
            <div style="font-size:12px;font-weight:700;color:var(--text3);letter-spacing:.05em;text-transform:uppercase;margin-bottom:10px">Patient Photos</div>
            <div class="photo-grid">
              <?php $allPhotos = ($patientPhotos ?? collect())->flatten(); ?>
              <?php $__empty_1 = true; $__currentLoopData = $allPhotos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <div class="photo-thumb" style="position:relative;overflow:hidden">
                <img src="<?php echo e(route('patients.view-photo', [$patient, $photo])); ?>" 
                     alt="<?php echo e($photo->photo_type ?? 'Photo'); ?>" 
                     style="width:100%;height:100%;object-fit:cover"/>
                <div style="position:absolute;bottom:0;left:0;right:0;padding:6px;background:linear-gradient(transparent,rgba(0,0,0,.7));color:white">
                  <div style="font-size:10px;font-weight:600;text-transform:uppercase"><?php echo e($photo->photo_type ?? 'Photo'); ?></div>
                  <div style="font-size:9px;opacity:0.8"><?php echo e($photo->created_at->format('d M Y')); ?></div>
                </div>
              </div>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <div style="grid-column:span 4;text-align:center;padding:32px;color:var(--text3)">
                <div style="font-size:32px;margin-bottom:8px">📷</div>
                <p style="font-size:13px">No photos uploaded yet</p>
                <p style="font-size:11px;margin-top:4px">Use the upload buttons above to add clinical photos</p>
              </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

      
      <div x-show="activeTab === 'progress'" x-cloak>
        <div class="form-section">
          <div class="form-section-header"><h3>Progress Tracking</h3></div>
          <div class="form-body">
            <?php
              // Collect scale history from past visits
              $scaleHistory = [];
              foreach (($visitHistory ?? collect())->reverse() as $histVisit) {
                $visitDate = $histVisit->created_at->format('d M');
                foreach ($histVisit->scales ?? [] as $scale) {
                  if (!isset($scaleHistory[$scale->scale_name])) {
                    $scaleHistory[$scale->scale_name] = ['labels' => [], 'values' => []];
                  }
                  $scaleHistory[$scale->scale_name]['labels'][] = $visitDate;
                  $scaleHistory[$scale->scale_name]['values'][] = $scale->score;
                }
              }
            ?>

            <?php if(count($scaleHistory) > 0): ?>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px">
              <?php $__currentLoopData = $scaleHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scaleName => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <div style="background:var(--bg);border-radius:10px;padding:16px">
                <div style="font-size:12px;font-weight:700;color:var(--text3);text-transform:uppercase;margin-bottom:12px"><?php echo e($scaleName); ?> Trend</div>
                <canvas id="chart-<?php echo e(Str::slug($scaleName)); ?>" height="150"></canvas>
              </div>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            
            <script>
              document.addEventListener('DOMContentLoaded', function() {
                <?php $__currentLoopData = $scaleHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scaleName => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                new Chart(document.getElementById('chart-<?php echo e(Str::slug($scaleName)); ?>').getContext('2d'), {
                  type: 'line',
                  data: {
                    labels: <?php echo json_encode($data['labels']); ?>,
                    datasets: [{
                      label: '<?php echo e($scaleName); ?>',
                      data: <?php echo json_encode($data['values']); ?>,
                      borderColor: 'rgb(79, 70, 229)',
                      backgroundColor: 'rgba(79, 70, 229, 0.1)',
                      tension: 0.3,
                      fill: true,
                    }]
                  },
                  options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                  }
                });
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              });
            </script>
            <?php else: ?>
            <div style="padding:40px;text-align:center;color:var(--text3)">
              <div style="font-size:32px;margin-bottom:12px">📈</div>
              <div style="font-size:14px;font-weight:600;margin-bottom:4px">Progress Charts</div>
              <div style="font-size:13px">Add grading scales in previous visits to see progress trends</div>
            </div>
            <?php endif; ?>

            
            <?php if(($visitHistory ?? collect())->count() > 1): ?>
            <div style="margin-top:20px">
              <div style="font-size:12px;font-weight:700;color:var(--text3);text-transform:uppercase;margin-bottom:10px">Visit Summary</div>
              <table style="width:100%;border-collapse:collapse">
                <thead>
                  <tr style="background:var(--bg)">
                    <th style="padding:8px 10px;text-align:left;font-size:11px;color:var(--text3)">Date</th>
                    <th style="padding:8px 10px;text-align:left;font-size:11px;color:var(--text3)">Chief Complaint</th>
                    <th style="padding:8px 10px;text-align:left;font-size:11px;color:var(--text3)">Diagnosis</th>
                    <th style="padding:8px 10px;text-align:left;font-size:11px;color:var(--text3)">Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $__currentLoopData = $visitHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $histVisit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <tr style="border-bottom:1px solid var(--border)<?php echo e($histVisit->id === $visit->id ? ';background:var(--blue-light)' : ''); ?>">
                    <td style="padding:10px;font-size:12px;color:var(--dark)"><?php echo e($histVisit->created_at->format('d M Y')); ?></td>
                    <td style="padding:10px;font-size:12px;color:var(--text2)"><?php echo e($histVisit->chief_complaint ?? 'N/A'); ?></td>
                    <td style="padding:10px;font-size:12px;color:var(--text2)"><?php echo e(Str::limit($histVisit->diagnosis_text ?? 'N/A', 30)); ?></td>
                    <td style="padding:10px">
                      <span style="font-size:10px;font-weight:600;padding:3px 8px;border-radius:100px;
                        <?php if($histVisit->status === 'finalised'): ?> background:var(--green-light);color:var(--green)
                        <?php elseif($histVisit->status === 'draft'): ?> background:#fffbeb;color:var(--amber)
                        <?php else: ?> background:#f1f5f9;color:#64748b <?php endif; ?>">
                        <?php echo e(ucfirst(str_replace('_', ' ', $histVisit->status))); ?>

                      </span>
                    </td>
                  </tr>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
              </table>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      
      <div x-show="activeTab === 'investigations'" x-cloak>
        <div class="form-section">
          <div class="form-section-header">
            <h3>Lab Investigations</h3>
            <a href="<?php echo e(route('vendor.index')); ?>" style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;border:none;background:var(--blue);color:white;margin-left:auto;text-decoration:none">+ Order Lab Tests</a>
          </div>
          <div class="form-body">
            <?php
              $pendingLabs = ($labOrders ?? collect())->where('status', '!=', 'completed');
              $completedLabs = ($labOrders ?? collect())->where('status', 'completed');
            ?>
            
            <div style="background:var(--bg);border-radius:10px;padding:16px;margin-bottom:12px">
              <div style="font-size:11px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:.05em;margin-bottom:10px">Pending Tests</div>
              <div style="display:flex;flex-direction:column;gap:8px">
                <?php $__empty_1 = true; $__currentLoopData = $pendingLabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:10px 14px;background:white;border-radius:8px;border:1px solid var(--border);flex-wrap:wrap">
                  <div style="flex:1;min-width:0">
                    <div style="font-size:13px;font-weight:600;color:var(--dark)">
                      <?php echo e($lab->display_test_names); ?>

                    </div>
                    <div style="font-size:11px;color:var(--text3)">
                      Ordered: <?php echo e($lab->created_at?->format('d M Y') ?? '—'); ?>

                      <?php if($lab->vendor_id && $lab->relationLoaded('vendor') && $lab->vendor): ?> · <?php echo e($lab->vendor->name); ?> <?php elseif(!empty($lab->provider_name)): ?> · <?php echo e($lab->provider_name); ?> <?php endif; ?>
                    </div>
                  </div>
                  <div style="display:flex;align-items:center;gap:8px;flex-shrink:0">
                    <a href="<?php echo e(route('laboratory.orders.report', ['orderId' => $lab->id])); ?>" target="_blank" rel="noopener" style="font-size:11px;font-weight:600;color:var(--blue);text-decoration:none">Details</a>
                    <?php if(!empty($lab->result_pdf_url) || !empty($lab->result_url)): ?>
                    <a href="<?php echo e($lab->result_pdf_url ?: $lab->result_url); ?>" target="_blank" rel="noopener" style="font-size:11px;font-weight:600;color:var(--teal);text-decoration:none">PDF</a>
                    <?php endif; ?>
                  </div>
                  <span style="background:#fffbeb;color:var(--amber);padding:3px 10px;border-radius:100px;font-size:11px;font-weight:600">
                    <?php echo e(ucfirst($lab->status)); ?>

                  </span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div style="text-align:center;padding:16px;color:var(--text3);font-size:12px">
                  No pending lab tests
                </div>
                <?php endif; ?>
              </div>
            </div>
            
            <div style="background:var(--bg);border-radius:10px;padding:16px">
              <div style="font-size:11px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:.05em;margin-bottom:10px">Previous Results</div>
              <div style="display:flex;flex-direction:column;gap:8px">
                <?php $__empty_1 = true; $__currentLoopData = $completedLabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:10px 14px;background:white;border-radius:8px;border:1px solid var(--border);flex-wrap:wrap">
                  <div style="flex:1;min-width:0">
                    <div style="font-size:13px;font-weight:600;color:var(--dark)">
                      <?php echo e($lab->display_test_names); ?>

                    </div>
                    <div style="font-size:11px;color:var(--text3)">
                      Completed: <?php echo e($lab->updated_at?->format('d M Y') ?? '—'); ?>

                    </div>
                  </div>
                  <div style="display:flex;align-items:center;gap:8px;flex-shrink:0">
                    <a href="<?php echo e(route('laboratory.orders.report', ['orderId' => $lab->id])); ?>" target="_blank" rel="noopener" style="font-size:11px;font-weight:600;color:var(--blue);text-decoration:none">View report</a>
                    <?php if(!empty($lab->result_pdf_url) || !empty($lab->result_url)): ?>
                    <a href="<?php echo e($lab->result_pdf_url ?: $lab->result_url); ?>" target="_blank" rel="noopener" style="font-size:11px;font-weight:600;color:var(--teal);text-decoration:none">Open PDF</a>
                    <?php endif; ?>
                  </div>
                  <span style="background:var(--green-light);color:var(--green);padding:3px 10px;border-radius:100px;font-size:11px;font-weight:600">
                    Completed ✓
                  </span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div style="text-align:center;padding:16px;color:var(--text3);font-size:12px">
                  No previous lab results
                </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>

      
      <div x-show="activeTab === 'billing'" x-cloak>
        <div class="form-section">
          <div class="form-section-header">
            <h3>Visit Billing</h3>
            <?php if(!$visit->invoice): ?>
            <a href="<?php echo e(route('billing.create')); ?>?patient_id=<?php echo e($patient->id); ?>&visit_id=<?php echo e($visit->id); ?>" style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;border:none;background:var(--blue);color:white;text-decoration:none;margin-left:auto">+ Create Invoice</a>
            <?php endif; ?>
          </div>
          <div class="form-body">
            <?php if($visit->invoice): ?>
              <?php $invoice = $visit->invoice; ?>
              <div style="background:var(--green-light);border:1px solid rgba(5,150,105,.15);border-radius:10px;padding:16px;margin-bottom:16px">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
                  <div>
                    <div style="font-size:14px;font-weight:700;color:var(--dark)">Invoice #<?php echo e($invoice->invoice_number); ?></div>
                    <div style="font-size:12px;color:var(--text3)">Created: <?php echo e($invoice->created_at->format('d M Y')); ?></div>
                  </div>
                  <div style="display:flex;gap:8px;flex-wrap:wrap">
                    <a href="<?php echo e(route('billing.show', $invoice)); ?>" style="padding:6px 12px;background:var(--blue);color:white;border-radius:6px;font-size:12px;font-weight:600;text-decoration:none">View Invoice</a>
                    <a href="<?php echo e(route('billing.pdf', ['invoice' => $invoice, 'format' => 'gst'])); ?>" style="padding:6px 12px;background:white;color:var(--text2);border:1px solid var(--border);border-radius:6px;font-size:12px;font-weight:600;text-decoration:none">GST PDF</a>
                    <a href="<?php echo e(route('billing.pdf', ['invoice' => $invoice, 'format' => 'bill'])); ?>" style="padding:6px 12px;background:white;color:var(--text2);border:1px solid var(--border);border-radius:6px;font-size:12px;font-weight:600;text-decoration:none">Bill PDF</a>
                  </div>
                </div>
                <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px">
                  <div>
                    <div style="font-size:11px;color:var(--text3);text-transform:uppercase">Subtotal</div>
                    <div style="font-size:16px;font-weight:700;color:var(--dark)">₹<?php echo e(number_format($invoice->subtotal ?? 0, 2)); ?></div>
                  </div>
                  <div>
                    <div style="font-size:11px;color:var(--text3);text-transform:uppercase">GST</div>
                    <div style="font-size:16px;font-weight:700;color:var(--dark)">₹<?php echo e(number_format(($invoice->cgst ?? 0) + ($invoice->sgst ?? 0), 2)); ?></div>
                  </div>
                  <div>
                    <div style="font-size:11px;color:var(--text3);text-transform:uppercase">Total</div>
                    <div style="font-size:16px;font-weight:700;color:var(--blue)">₹<?php echo e(number_format($invoice->total ?? 0, 2)); ?></div>
                  </div>
                  <div>
                    <div style="font-size:11px;color:var(--text3);text-transform:uppercase">Status</div>
                    <div style="font-size:14px;font-weight:700;color:<?php echo e($invoice->payment_status === 'paid' ? 'var(--green)' : 'var(--amber)'); ?>">
                      <?php echo e(ucfirst($invoice->payment_status ?? 'pending')); ?>

                    </div>
                  </div>
                </div>
              </div>
              
              <?php if($invoice->items && $invoice->items->count() > 0): ?>
              <div style="font-size:11px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:.05em;margin-bottom:10px">Invoice Items</div>
              <table style="width:100%;border-collapse:collapse">
                <thead>
                  <tr style="background:var(--bg)">
                    <th style="padding:8px 10px;text-align:left;font-size:11px;color:var(--text3)">Description</th>
                    <th style="padding:8px 10px;text-align:center;font-size:11px;color:var(--text3)">Qty</th>
                    <th style="padding:8px 10px;text-align:right;font-size:11px;color:var(--text3)">Rate</th>
                    <th style="padding:8px 10px;text-align:right;font-size:11px;color:var(--text3)">Total</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $__currentLoopData = $invoice->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <tr style="border-bottom:1px solid var(--border)">
                    <td style="padding:10px;font-size:13px;color:var(--dark)"><?php echo e($item->description); ?></td>
                    <td style="padding:10px;font-size:13px;color:var(--text2);text-align:center"><?php echo e($item->quantity); ?></td>
                    <td style="padding:10px;font-size:13px;color:var(--text2);text-align:right">₹<?php echo e(number_format($item->unit_price, 2)); ?></td>
                    <td style="padding:10px;font-size:13px;font-weight:600;color:var(--dark);text-align:right">₹<?php echo e(number_format($item->total, 2)); ?></td>
                  </tr>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
              </table>
              <?php endif; ?>
            <?php else: ?>
            <div style="background:var(--blue-light);border:1px solid rgba(20,71,230,.15);border-radius:10px;padding:16px;text-align:center">
              <div style="font-size:32px;margin-bottom:8px">🧾</div>
              <p style="font-size:13px;font-weight:600;color:var(--dark);margin-bottom:4px">No invoice for this visit yet</p>
              <p style="font-size:12px;color:var(--text3)">Click "Create Invoice" to generate a GST-compliant invoice</p>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      
      <?php if(($customTemplates ?? collect())->count() > 0): ?>
      <div x-show="activeTab === 'custom'" x-cloak
           x-data="{
             customValues: {},
             init() {
               // Pre-populate from saved structured_data.custom
               const saved = <?php echo json_encode(($visit->structured_data['custom'] ?? []), 15, 512) ?>;
               if (saved && typeof saved === 'object') {
                 this.customValues = saved;
               }
             },
             getField(templateId, fieldName) {
               return (this.customValues[templateId] ?? {})[fieldName] ?? '';
             },
             setField(templateId, fieldName, value) {
               if (!this.customValues[templateId]) {
                 this.customValues[templateId] = {};
               }
               this.customValues[templateId][fieldName] = value;
               this.$nextTick(() => {
                 const hidden = document.getElementById('custom_field_data_input');
                 if (hidden) hidden.value = JSON.stringify(this.customValues);
               });
             },
             async saveCustomFields() {
               const url = '<?php echo e(route('emr.save-custom-fields', [$patient, $visit])); ?>';
               const payload = { custom_field_data: JSON.stringify(this.customValues) };
               try {
                 const res = await fetch(url, {
                   method: 'POST',
                   headers: {
                     'Content-Type': 'application/json',
                     'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                     'Accept': 'application/json'
                   },
                   body: JSON.stringify(payload)
                 });
                 const data = await res.json();
                 if (data.success) {
                   alert('Custom fields saved!');
                 } else {
                   alert('Save failed: ' + (data.error || 'Unknown error'));
                 }
               } catch (e) {
                 alert('Save failed: ' + e.message);
               }
             }
           }">

        <input type="hidden" id="custom_field_data_input" name="custom_field_data" :value="JSON.stringify(customValues)">

        <?php $__currentLoopData = $customTemplates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
          $fields = is_string($template->fields ?? null)
            ? json_decode($template->fields, true)
            : (array)($template->fields ?? []);
        ?>
        <div class="form-section" style="margin-bottom:16px">
          <div class="form-section-header" x-data="{open:true}" @click="open=!open">
            <h3><?php echo e($template->name); ?></h3>
            <?php if(!empty($template->description)): ?>
            <span style="font-size:12px;color:var(--text3);margin-left:8px"><?php echo e($template->description); ?></span>
            <?php endif; ?>
            <span class="toggle" x-text="open ? '−' : '+'"></span>
          </div>
          <div class="form-body" x-show="open">
            <?php if(!empty($fields)): ?>
            <div class="form-row" style="grid-template-columns: repeat(auto-fill, minmax(220px,1fr))">
              <?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <?php
                $fieldName = $field['name'] ?? $field['key'] ?? 'field_' . $loop->index;
                $fieldLabel = $field['label'] ?? ucwords(str_replace('_', ' ', $fieldName));
                $fieldType = $field['type'] ?? 'text';
                $templateId = (string)$template->id;
              ?>
              <div class="field-group">
                <div class="field-label"><?php echo e($fieldLabel); ?></div>
                <?php if($fieldType === 'textarea'): ?>
                  <textarea class="field-textarea" style="min-height:60px"
                    x-bind:value="getField('<?php echo e($templateId); ?>', '<?php echo e($fieldName); ?>')"
                    @input="setField('<?php echo e($templateId); ?>', '<?php echo e($fieldName); ?>', $event.target.value)"
                    placeholder="<?php echo e($field['placeholder'] ?? ''); ?>"></textarea>
                <?php elseif($fieldType === 'select'): ?>
                  <select class="field-select"
                    x-bind:value="getField('<?php echo e($templateId); ?>', '<?php echo e($fieldName); ?>')"
                    @change="setField('<?php echo e($templateId); ?>', '<?php echo e($fieldName); ?>', $event.target.value)">
                    <option value="">Select…</option>
                    <?php $__currentLoopData = $field['options'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e(is_array($opt) ? ($opt['value'] ?? $opt['label'] ?? $opt) : $opt); ?>">
                      <?php echo e(is_array($opt) ? ($opt['label'] ?? $opt['value'] ?? $opt) : $opt); ?>

                    </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </select>
                <?php elseif($fieldType === 'radio'): ?>
                  <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:4px">
                    <?php $__currentLoopData = $field['options'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $optVal = is_array($opt) ? ($opt['value'] ?? $opt['label'] ?? $opt) : $opt; ?>
                    <label style="display:flex;align-items:center;gap:4px;font-size:13px;cursor:pointer">
                      <input type="radio"
                        :checked="getField('<?php echo e($templateId); ?>', '<?php echo e($fieldName); ?>') === '<?php echo e($optVal); ?>'"
                        @change="setField('<?php echo e($templateId); ?>', '<?php echo e($fieldName); ?>', '<?php echo e($optVal); ?>')"
                        value="<?php echo e($optVal); ?>">
                      <?php echo e(is_array($opt) ? ($opt['label'] ?? $optVal) : $opt); ?>

                    </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </div>
                <?php elseif($fieldType === 'number'): ?>
                  <input type="number" class="field-input"
                    :value="getField('<?php echo e($templateId); ?>', '<?php echo e($fieldName); ?>')"
                    @input="setField('<?php echo e($templateId); ?>', '<?php echo e($fieldName); ?>', $event.target.value)"
                    placeholder="<?php echo e($field['placeholder'] ?? ''); ?>"
                    min="<?php echo e($field['min'] ?? ''); ?>" max="<?php echo e($field['max'] ?? ''); ?>">
                <?php elseif($fieldType === 'checkbox'): ?>
                  <label style="display:flex;align-items:center;gap:8px;font-size:13px;margin-top:4px;cursor:pointer">
                    <input type="checkbox"
                      :checked="getField('<?php echo e($templateId); ?>', '<?php echo e($fieldName); ?>') == '1' || getField('<?php echo e($templateId); ?>', '<?php echo e($fieldName); ?>') === true"
                      @change="setField('<?php echo e($templateId); ?>', '<?php echo e($fieldName); ?>', $event.target.checked ? '1' : '0')">
                    <?php echo e($fieldLabel); ?>

                  </label>
                <?php else: ?>
                  <input type="text" class="field-input"
                    :value="getField('<?php echo e($templateId); ?>', '<?php echo e($fieldName); ?>')"
                    @input="setField('<?php echo e($templateId); ?>', '<?php echo e($fieldName); ?>', $event.target.value)"
                    placeholder="<?php echo e($field['placeholder'] ?? ''); ?>">
                <?php endif; ?>
              </div>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php else: ?>
            <p style="color:var(--text3);font-size:13px">No fields configured for this template.</p>
            <?php endif; ?>
          </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <div style="display:flex;justify-content:flex-end;margin-top:8px">
          <button type="button" @click="saveCustomFields()"
            style="display:inline-flex;align-items:center;gap:6px;padding:10px 20px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;border:none;background:var(--blue);color:white">
            Save Custom Fields
          </button>
        </div>
      </div>
      <?php endif; ?>

    </div>
  </div>

  
  <div class="bottom-bar">
    <div class="save-info">
      <template x-if="autoSaved">
        <span><strong>Auto-saved</strong> · <span x-text="lastSaved"></span></span>
      </template>
      <template x-if="!autoSaved">
        <span style="color:var(--amber)">Saving…</span>
      </template>
    </div>
    <div style="margin-left:auto;display:flex;gap:10px">
      <button type="button" onclick="saveAsDraft()" style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;border:1px solid var(--border);background:transparent;color:var(--text2)">Save Draft</button>
      <button type="button" onclick="window.print()" style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;border:1px solid var(--border);background:transparent;color:var(--text2)">🖨 Print Note</button>
      <?php if($visit->status !== 'finalised'): ?>
      <button type="button" onclick="finaliseVisit()" style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;border:none;background:var(--blue);color:white">✓ Finalise &amp; Send Prescription via WhatsApp</button>
      <?php else: ?>
      <span style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;background:var(--green-light);color:var(--green)">✓ Visit Completed</span>
      <?php endif; ?>
    </div>
  </div>

</form>
  </div>

  <div id="prescription-safety-banner" hidden style="position:fixed;bottom:72px;left:50%;transform:translateX(-50%);max-width:560px;z-index:60;padding:12px 16px;background:#fff7ed;border:1px solid #fdba74;border-radius:10px;box-shadow:0 4px 20px rgba(0,0,0,.12);font-size:13px;color:#9a3412;">
    <div style="font-weight:700;margin-bottom:6px">Blocking prescription safety issue</div>
    <div style="margin-bottom:10px">Finalising this visit will be blocked until you acknowledge with a clinical reason (allergy match or major drug interaction).</div>
    <button type="button" onclick="openPrescriptionSafetyModal()" style="padding:6px 12px;border-radius:8px;border:none;background:#ea580c;color:#fff;font-weight:600;cursor:pointer">Acknowledge &amp; record reason</button>
  </div>

  <div id="prescription-safety-modal" style="display:none;position:fixed;inset:0;z-index:70;background:rgba(0,0,0,.45);align-items:center;justify-content:center;padding:16px;">
    <div style="background:#fff;border-radius:12px;max-width:480px;width:100%;padding:20px;box-shadow:0 8px 32px rgba(0,0,0,.2);">
      <h3 style="margin:0 0 8px;font-size:16px">Override reason (required)</h3>
      <p style="margin:0 0 12px;font-size:13px;color:#64748b">Document why you are proceeding despite blocking allergy or major interaction alerts. Minimum 10 characters. This is stored on the prescription and audit log.</p>
      <textarea id="prescription-safety-override-reason" rows="4" style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:10px;font-size:13px" placeholder="e.g. Discussed risks with patient; no suitable alternative; monitored in OPD…"></textarea>
      <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:14px">
        <button type="button" onclick="closePrescriptionSafetyModal()" style="padding:8px 14px;border-radius:8px;border:1px solid #cbd5e1;background:#fff;cursor:pointer">Cancel</button>
        <button type="button" onclick="submitPrescriptionSafetyAcknowledge()" style="padding:8px 14px;border-radius:8px;border:none;background:#2563eb;color:#fff;font-weight:600;cursor:pointer">Save with acknowledgement</button>
      </div>
    </div>
  </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
  window.emrOnChangeVisitSpecialty = function (sel) {
    const saveUrl = '<?php echo e(route('emr.update', [$patient, $visit])); ?>';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '<?php echo e(csrf_token()); ?>';
    console.log('[EMR] visit specialty change request', { value: sel.value, visitId: <?php echo e($visit->id); ?> });
    fetch(saveUrl, {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json'
      },
      body: JSON.stringify({ specialty: sel.value })
    })
      .then(function (r) { return r.json(); })
      .then(function (result) {
        console.log('[EMR] visit specialty save response', result);
        if (result.saved) {
          window.location.reload();
        }
      })
      .catch(function (err) {
        console.error('[EMR] visit specialty save failed', err);
      });
  };

  window.emrDictationSpecialty = <?php echo json_encode(strtolower((string) ($visit->specialty ?? auth()->user()->specialty ?? 'general')), 15, 512) ?>;
  const emrAiTranscribeUrl = '<?php echo e(route('ai.transcribe')); ?>';
  const emrAiMapFieldsUrl = '<?php echo e(route('ai.map-fields')); ?>';

  window.emrDictationTemplateSchema = function () {
    return [
      { key: 'chief_complaint', type: 'string', description: 'Main complaint — short clinical wording (English preferred)' },
      { key: 'duration', type: 'string', description: 'Duration e.g. 2 weeks, 3 days' },
      { key: 'onset', type: 'enum', description: 'One of: gradual, sudden, recurrent' },
      { key: 'progression', type: 'enum', description: 'One of: worsening, improving, static, fluctuating' },
      { key: 'previous_treatment', type: 'enum', description: 'One of: yes_ongoing, yes_stopped, no' },
      { key: 'history', type: 'text', description: 'History of present illness — narrative (English clinical prose)' },
      { key: 'diagnosis_text', type: 'string', description: 'Provisional or working diagnosis' },
      { key: 'plan', type: 'text', description: 'Plan, investigations, follow-up' },
    ];
  };

  window.emrApplyDictationFields = function (fields, transcriptRaw) {
    const form = document.getElementById('emr-form');
    if (!form) {
      console.warn('[EMR dictation] emrApplyDictationFields: no form');
      return;
    }
    const setVal = function (name, val) {
      if (val === undefined || val === null) return;
      const el = form.querySelector('[name="' + name + '"]');
      if (!el || !('value' in el)) return;
      const v = typeof val === 'string' ? val : String(val);
      if (name === 'chief_complaint' && el.tagName === 'SELECT') {
        const match = Array.from(el.options).find(function (o) {
          return o.value === v || o.textContent.trim() === v;
        });
        if (match) {
          el.value = match.value;
        } else if (v) {
          const hist = form.querySelector('[name="history"]');
          if (hist) {
            hist.value = (hist.value ? hist.value + '\n\n' : '') + 'Chief complaint (dictated): ' + v;
            hist.dispatchEvent(new Event('input', { bubbles: true }));
          }
          console.log('[EMR dictation] chief_complaint not in list, appended to history', { v: v });
          return;
        }
      } else {
        el.value = v;
      }
      el.dispatchEvent(new Event('input', { bubbles: true }));
      el.dispatchEvent(new Event('change', { bubbles: true }));
    };

    if (fields && typeof fields === 'object') {
      Object.keys(fields).forEach(function (key) {
        if (key === 'needs_confirmation' || key === 'transcript') return;
        const val = fields[key];
        if (val && typeof val === 'object' && !Array.isArray(val)) return;
        setVal(key, val);
      });
    }

    const hist = form.querySelector('textarea[name="history"]');
    if (hist && transcriptRaw) {
      const rawBlock = '\n\n--- Original transcript ---\n' + transcriptRaw;
      if (!hist.value || hist.value.indexOf(transcriptRaw) === -1) {
        hist.value = (hist.value || '') + rawBlock;
        hist.dispatchEvent(new Event('input', { bubbles: true }));
      }
    }
    if (window.triggerAutoSave) window.triggerAutoSave();
    console.log('[EMR dictation] emrApplyDictationFields done', { fieldKeys: fields ? Object.keys(fields) : [] });
  };

  (function () {
    var mediaRecorder = null;
    var chunks = [];
    var stream = null;

    window.emrToggleDictation = async function () {
      var root = document.getElementById('emr-alpine-root');
      var alpine = root && window.Alpine ? window.Alpine.$data(root) : null;
      if (!alpine) {
        console.warn('[EMR dictation] Alpine root not found');
        return;
      }
      if (alpine.dictationBusy) {
        console.log('[EMR dictation] skip (busy)');
        return;
      }
      if (!navigator.mediaDevices || !window.MediaRecorder) {
        alert('Voice dictation needs a modern browser with microphone support.');
        return;
      }

      if (!alpine.recording) {
        try {
          stream = await navigator.mediaDevices.getUserMedia({ audio: true });
          chunks = [];
          var mime = window.MediaRecorder.isTypeSupported('audio/webm;codecs=opus')
            ? 'audio/webm;codecs=opus'
            : (window.MediaRecorder.isTypeSupported('audio/webm') ? 'audio/webm' : '');
          mediaRecorder = mime ? new MediaRecorder(stream, { mimeType: mime }) : new MediaRecorder(stream);
          mediaRecorder.ondataavailable = function (e) {
            if (e.data && e.data.size) chunks.push(e.data);
          };
          mediaRecorder.onerror = function (e) {
            console.error('[EMR dictation] MediaRecorder error', e);
          };
          mediaRecorder.start();
          alpine.recording = true;
          console.log('[EMR dictation] recording started', { mime: mediaRecorder.mimeType });
        } catch (e) {
          console.error('[EMR dictation] getUserMedia failed', e);
          alert('Microphone error: ' + (e && e.message ? e.message : e));
        }
        return;
      }

      alpine.recording = false;
      alpine.dictationBusy = true;
      console.log('[EMR dictation] stopping recorder');

      var blob = null;
      try {
        blob = await new Promise(function (resolve, reject) {
          if (!mediaRecorder) {
            resolve(null);
            return;
          }
          mediaRecorder.addEventListener('stop', function onStop() {
            mediaRecorder.removeEventListener('stop', onStop);
            if (stream) {
              stream.getTracks().forEach(function (t) { t.stop(); });
              stream = null;
            }
            var out = new Blob(chunks, { type: mediaRecorder.mimeType || 'audio/webm' });
            mediaRecorder = null;
            chunks = [];
            resolve(out);
          });
          try {
            mediaRecorder.stop();
          } catch (err) {
            reject(err);
          }
        });
      } catch (e) {
        console.error('[EMR dictation] stop failed', e);
        alpine.dictationBusy = false;
        alert('Could not stop recording: ' + (e && e.message ? e.message : e));
        return;
      }

      if (!blob || blob.size < 20) {
        console.warn('[EMR dictation] empty or tiny blob', { size: blob ? blob.size : 0 });
        alpine.dictationBusy = false;
        alert('No audio captured. Try speaking longer or check the microphone.');
        return;
      }

      var csrf = document.querySelector('meta[name="csrf-token"]') && document.querySelector('meta[name="csrf-token"]').content
        ? document.querySelector('meta[name="csrf-token"]').content
        : '<?php echo e(csrf_token()); ?>';
      var fd = new FormData();
      fd.append('audio', blob, 'dictation.webm');
      fd.append('language', alpine.dictationLang || 'auto');

      try {
        console.log('[EMR dictation] POST transcribe', { url: emrAiTranscribeUrl, lang: alpine.dictationLang, bytes: blob.size });
        var tr = await fetch(emrAiTranscribeUrl, {
          method: 'POST',
          headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
          body: fd,
        });
        var trRaw = await tr.text();
        var trJson;
        try {
          trJson = trRaw && trRaw.trim() ? JSON.parse(trRaw) : {};
        } catch (parseErr) {
          console.error('[EMR dictation] transcribe non-JSON response', { status: tr.status, body: trRaw.slice(0, 1200) });
          throw new Error('HTTP ' + tr.status + ' — server returned non-JSON (often a PHP crash). Check backend/storage/logs/laravel.log. Snippet: ' + String(trRaw).trim().slice(0, 200));
        }
        console.log('[EMR dictation] transcribe JSON', { ok: tr.ok, status: tr.status, keys: trJson ? Object.keys(trJson) : [], diagnostic: trJson && trJson.diagnostic });
        if (!tr.ok || !trJson.success) {
          var firstErr = trJson.error;
          if (!firstErr && trJson.message) firstErr = trJson.message;
          if (!firstErr && trJson.errors) {
            try {
              var flat = [];
              Object.keys(trJson.errors).forEach(function (k) {
                var v = trJson.errors[k];
                if (Array.isArray(v)) {
                  v.forEach(function (x) { flat.push(String(x)); });
                } else {
                  flat.push(String(v));
                }
              });
              if (flat.length) firstErr = flat[0];
            } catch (e2) {
              console.warn('[EMR dictation] could not flatten validation errors', e2);
            }
          }
          var hint = '';
          if (trJson.diagnostic) {
            try { hint = '\n\n[Debug] ' + JSON.stringify(trJson.diagnostic); } catch (e3) {}
          }
          throw new Error((firstErr || ('HTTP ' + tr.status)) + hint);
        }
        var transcript = String(trJson.transcription || '');
        if (!transcript.trim()) {
          throw new Error('Empty transcript — check Settings → AI & APIs (OpenAI key) and try again.');
        }

        var mapBody = {
          transcript: transcript,
          specialty: window.emrDictationSpecialty || 'general',
          template: window.emrDictationTemplateSchema(),
        };
        console.log('[EMR dictation] POST map-fields', { url: emrAiMapFieldsUrl, specialty: mapBody.specialty });

        var mf = await fetch(emrAiMapFieldsUrl, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
          },
          body: JSON.stringify(mapBody),
        });
        var mfRaw = await mf.text();
        var mfJson;
        try {
          mfJson = mfRaw && mfRaw.trim() ? JSON.parse(mfRaw) : {};
        } catch (parseErr2) {
          console.error('[EMR dictation] map-fields non-JSON', { status: mf.status, body: mfRaw.slice(0, 1200) });
          throw new Error('map-fields HTTP ' + mf.status + ' — non-JSON. Snippet: ' + String(mfRaw).trim().slice(0, 200));
        }
        console.log('[EMR dictation] map-fields JSON', { ok: mf.ok, status: mf.status, keys: mfJson ? Object.keys(mfJson) : [] });
        if (!mf.ok) {
          throw new Error((mfJson && (mfJson.message || mfJson.error)) || ('HTTP ' + mf.status));
        }
        var mapped = mfJson.fields || {};
        if (mapped && typeof mapped === 'object' && Object.keys(mapped).length) {
          window.emrApplyDictationFields(mapped, transcript);
          alert('Dictation applied. Review the note fields.');
        } else {
          window.emrApplyDictationFields({}, transcript);
          alert('Transcription added to History. Field mapping returned empty — check Anthropic API key (Settings → AI & APIs).');
        }
      } catch (e) {
        console.error('[EMR dictation] pipeline failed', e);
        alert('Dictation failed: ' + (e && e.message ? e.message : e));
      } finally {
        alpine.dictationBusy = false;
      }
    };
  })();

  /** Web EMR: POST session-auth AI summary (Anthropic). See EmrWebController@aiSummary */
  window.emrAiSummarise = function () {
    const url = '<?php echo e(route('emr.ai-summary', [$patient, $visit])); ?>';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '<?php echo e(csrf_token()); ?>';
    const wrapper = document.querySelector('[x-data]');
    const alpine = wrapper && wrapper.__x ? wrapper.__x.$data : null;
    if (alpine) alpine.aiSummarising = true;
    const form = document.getElementById('emr-form');
    const draft = (form && typeof buildEmrPatchPayload === 'function') ? buildEmrPatchPayload(form) : {};
    console.log('[EMR] AI summarise request', {
      visitId: <?php echo e($visit->id); ?>,
      url: url,
      draftKeys: Object.keys(draft),
      historyLen: (draft.history && String(draft.history).length) || 0
    });

    fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
      body: JSON.stringify({ draft: draft }),
    })
      .then(function (r) {
        return r.json().then(function (data) {
          return { ok: r.ok, status: r.status, data: data };
        });
      })
      .then(function (result) {
        console.log('[EMR] AI summarise response', result);
        if (!result.ok) {
          const msg = result.data && (result.data.message || result.data.error)
            ? (result.data.message || result.data.error)
            : ('HTTP ' + result.status);
          throw new Error(msg);
        }
        const summary = (result.data && result.data.summary) ? String(result.data.summary) : '';
        const hist = document.querySelector('#emr-form textarea[name="history"]');
        if (hist && summary) {
          hist.value = (hist.value ? hist.value + '\n\n' : '') + '--- AI summary ---\n' + summary;
          hist.dispatchEvent(new Event('input', { bubbles: true }));
          if (window.triggerAutoSave) window.triggerAutoSave();
        }
        alert(summary ? 'AI summary added to History Notes.' : 'No summary text returned.');
      })
      .catch(function (err) {
        console.error('[EMR] AI summarise failed', err);
        alert('AI summary failed: ' + (err.message || 'Unknown error'));
      })
      .finally(function () {
        if (alpine) alpine.aiSummarising = false;
      });
  };

  // Global triggerAutoSave function for Alpine components
  window.triggerAutoSave = function() {
    // Dispatch event to trigger auto-save
    window.dispatchEvent(new CustomEvent('emr-autosave'));
    
    // Also update Alpine state if available
    const wrapper = document.querySelector('[x-data]');
    if (wrapper && wrapper.__x) {
      wrapper.__x.$data.autoSaved = false;
      setTimeout(() => {
        wrapper.__x.$data.autoSaved = true;
        wrapper.__x.$data.lastSaved = new Date().toLocaleTimeString('en-IN',{hour:'2-digit',minute:'2-digit'});
      }, 1200);
    }
  };

  function isEmrSpecialtyFieldName(name) {
    if (!name || name === '_token' || name === '_method') return false;
    if (name.endsWith('_data') || name.endsWith('_diagnoses')) return true;
    if (/^drugs\[/i.test(name) || /\[\]$/.test(name)) return false;
    if (/photos|xrays|files/i.test(name)) return false;
    const prefixes = [
      'physio_', 'dental_', 'ortho_', 'ent_', 'gynae_', 'obs_', 'ophthal_',
      'cardiology_', 'paediatrics_', 'general_physician_', 'gastroenterology_', 'nephrology_',
      'endocrinology_', 'diabetology_', 'pulmonology_', 'neurology_', 'oncology_', 'psychiatry_',
      'rheumatology_', 'urology_', 'general_surgery_', 'ayush_', 'homeopathy_',
    ];
    if (prefixes.some(function (p) { return name.indexOf(p) === 0; })) return true;
    return ['va_data', 'iop_data', 'refraction_data', 'slit_lamp_data', 'fundus_data', 'ophthal_diagnoses',
      'lesions_json', 'procedures_json', 'pasi_score', 'pasi_data', 'iga_score', 'dlqi_score', 'dlqi_data',
      'dental_teeth_data', 'physio_hep_data'].indexOf(name) !== -1;
  }

  /** Core fields + structured_data + specialty template fields for PATCH emr.update */
  function buildEmrPatchPayload(formEl) {
    const formData = new FormData(formEl);
    const data = {};
    ['chief_complaint', 'history', 'diagnosis_text', 'diagnosis_code', 'plan', 'followup_in_days', 'followup_date'].forEach(function (field) {
      const input = formEl.querySelector('[name="' + field + '"]');
      if (input) data[field] = input.value;
    });
    const structuredData = {};
    ['duration', 'onset', 'progression', 'previous_treatment'].forEach(function (field) {
      const input = formEl.querySelector('[name="' + field + '"]');
      if (input && input.value) structuredData[field] = input.value;
    });
    data.structured_data = structuredData;
    const seen = new Set();
    for (const pair of formData.entries()) {
      const name = pair[0];
      let value = pair[1];
      if (seen.has(name)) continue;
      seen.add(name);
      if (!isEmrSpecialtyFieldName(name)) continue;
      if (typeof value !== 'string') continue;
      const t = value.trim();
      if (t.length && (t[0] === '{' || t[0] === '[')) {
        try {
          value = JSON.parse(value);
        } catch (e) {
          console.warn('[EMR PATCH] specialty field JSON parse failed', { name: name, err: String(e) });
        }
      }
      data[name] = value;
    }
    return data;
  }

  // Auto-save via AJAX on field changes
  document.addEventListener('DOMContentLoaded', function () {
    let saveTimer;
    const form = document.getElementById('emr-form');
    const saveUrl = '<?php echo e(route('emr.update', [$patient, $visit])); ?>';
    const prescriptionSaveUrl = '<?php echo e(route('emr.save-prescription', [$patient, $visit])); ?>';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '<?php echo e(csrf_token()); ?>';

    function extractPrescriptionDrugs(formData) {
      const rows = {};
      let hasIncompleteRows = false;

      for (const [key, value] of formData.entries()) {
        const match = key.match(/^drugs\[(\d+)\]\[(name|generic|dose|frequency|duration|instructions)\]$/);
        if (!match) continue;

        const rowIndex = match[1];
        const field = match[2];
        if (!rows[rowIndex]) rows[rowIndex] = {};
        rows[rowIndex][field] = String(value || '').trim();
      }

      const drugs = Object.keys(rows)
        .sort((a, b) => Number(a) - Number(b))
        .map((rowIndex) => rows[rowIndex])
        .filter((row) => {
          const hasAnyValue = ['name', 'generic', 'dose', 'frequency', 'duration', 'instructions']
            .some((field) => !!row[field]);

          if (!hasAnyValue) return false;

          const isComplete = !!row.name && !!row.dose && !!row.frequency && !!row.duration;
          if (!isComplete) hasIncompleteRows = true;
          return isComplete;
        });

      console.log('Prescription payload extracted for autosave', {
        drugCount: drugs.length,
        hasIncompleteRows
      });

      return { drugs, hasIncompleteRows };
    }

    function performAutoSave() {
      const formData = new FormData(form);
      const data = buildEmrPatchPayload(form);
      const prescriptionPayload = extractPrescriptionDrugs(formData);

      console.log('[EMR] performAutoSave', { keys: Object.keys(data), structuredKeys: Object.keys(data.structured_data || {}) });

      return fetch(saveUrl, {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json'
        },
        body: JSON.stringify(data)
      })
      .then(function (res) {
        return res.json().then(function (result) {
          return { httpOk: res.ok, result: result };
        });
      })
      .then(function (out) {
        if (!out.result || !out.result.saved) {
          console.warn('[EMR] performAutoSave not saved', out);
          return out;
        }
        console.log('[EMR] Auto-saved at', out.result.at);

        if (prescriptionPayload.hasIncompleteRows) {
          console.log('Skipping prescription autosave due to incomplete drug rows');
          return out;
        }

        if (!prescriptionPayload.drugs.length) {
          console.log('No complete prescription drugs found for autosave');
          return out;
        }

        console.log('Triggering prescription autosave', { drugCount: prescriptionPayload.drugs.length });
        fetch(prescriptionSaveUrl, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
          },
          body: JSON.stringify({ drugs: prescriptionPayload.drugs, autosave: true })
        })
        .then(res => res.json())
        .then(rxResult => {
          if (rxResult?.success) {
            console.log('Prescription autosaved successfully', {
              prescriptionId: rxResult.prescription_id,
              warnings: rxResult.warnings || {},
              blocking: rxResult.blocking,
              safety_ack_required: rxResult.safety_ack_required
            });
            const banner = document.getElementById('prescription-safety-banner');
            if (banner) {
              banner.hidden = !rxResult.safety_ack_required;
            }
          } else {
            console.warn('Prescription autosave failed', rxResult);
          }
        })
        .catch(err => console.error('Prescription autosave request failed:', err));

        return out;
      })
      .catch(function (err) {
        console.error('[EMR] Auto-save failed:', err);
        throw err;
      });
    }

    /** Clears debounce and PATCHes immediately. Used before AI summarise so server has latest History Notes. */
    window.flushEmrAutosaveNow = function () {
      clearTimeout(saveTimer);
      console.log('[EMR] flushEmrAutosaveNow');
      return performAutoSave();
    };
    
    form?.addEventListener('input', function () {
      clearTimeout(saveTimer);
      saveTimer = setTimeout(performAutoSave, 2000);
    });
    
    // Listen for custom auto-save events
    window.addEventListener('emr-autosave', function() {
      clearTimeout(saveTimer);
      saveTimer = setTimeout(performAutoSave, 2000);
    });
  });
  
  // Update follow-up date based on selected interval
  function updateFollowupDate(days) {
    if (days && parseInt(days) > 0) {
      const date = new Date();
      date.setDate(date.getDate() + parseInt(days));
      const formatted = date.toISOString().split('T')[0];
      document.getElementById('followup_date').value = formatted;
    }
  }
  
  // Lesion management
  function openAddLesionModal() {
    // For now, show a simple prompt - can be enhanced with a modal
    const region = prompt('Enter body region (e.g., Left Cheek, Forehead):');
    if (!region) return;
    
    const type = prompt('Enter lesion type (e.g., Plaque, Papule, Macule):');
    if (!type) return;
    
    const description = prompt('Enter description (optional):') || '';
    
    addLesion(region, type, description);
  }
  
  function addLesion(region, type, description) {
    const url = '<?php echo e(route('emr.add-lesion', [$patient, $visit])); ?>';
    
    fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        body_region: region,
        lesion_type: type,
        description: description
      })
    })
    .then(res => res.json())
    .then(result => {
      if (result.success) {
        location.reload();
      } else {
        alert('Failed to add lesion: ' + (result.error || 'Unknown error'));
      }
    })
    .catch(err => {
      console.error('Add lesion error:', err);
      alert('Failed to add lesion');
    });
  }
  
  function removeLesion(lesionId) {
    if (!confirm('Remove this lesion annotation?')) return;
    
    fetch(`<?php echo e(url('emr/' . $patient->id . '/' . $visit->id . '/lesions')); ?>/${lesionId}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
        'Accept': 'application/json'
      }
    })
    .then(res => res.json())
    .then(result => {
      if (result.success) {
        document.querySelector(`[data-lesion-id="${lesionId}"]`)?.remove();
      }
    })
    .catch(err => console.error('Remove lesion error:', err));
  }

  // Save draft manually
  function saveAsDraft() {
    const form = document.getElementById('emr-form');
    const saveUrl = '<?php echo e(route('emr.update', [$patient, $visit])); ?>';
    const data = buildEmrPatchPayload(form);
    console.log('[EMR] save draft payload keys', Object.keys(data));

    fetch(saveUrl, {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
        'Accept': 'application/json'
      },
      body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(result => {
      if (result.saved) {
        alert('Draft saved successfully!');
      } else {
        alert('Failed to save draft');
      }
    })
    .catch(err => {
      console.error('Save error:', err);
      alert('Failed to save draft');
    });
  }

  // Complete visit (top bar button)
  function completeVisit() {
    if (!confirm('Complete this visit? The consultation will be marked as completed.')) {
      return;
    }
    submitFinaliseForm();
  }

  // Finalise visit (bottom bar button - with WhatsApp)
  function finaliseVisit() {
    if (!confirm('Are you sure you want to finalise this visit? This will mark the consultation as complete and send the prescription via WhatsApp.')) {
      return;
    }
    submitFinaliseForm();
  }
  
  // Common form submission for finalising
  function submitFinaliseForm() {
    // Create a form and submit it outside the main form
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?php echo e(route('emr.finalise', [$patient, $visit])); ?>';
    form.style.display = 'none';
    
    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = '<?php echo e(csrf_token()); ?>';
    form.appendChild(csrf);
    
    document.body.appendChild(form);
    form.submit();
  }

  window.openPrescriptionSafetyModal = function () {
    const m = document.getElementById('prescription-safety-modal');
    if (m) {
      m.style.display = 'flex';
      console.log('Prescription safety modal opened');
    }
  };

  window.closePrescriptionSafetyModal = function () {
    const m = document.getElementById('prescription-safety-modal');
    if (m) {
      m.style.display = 'none';
      console.log('Prescription safety modal closed');
    }
  };

  window.submitPrescriptionSafetyAcknowledge = function () {
    const reasonEl = document.getElementById('prescription-safety-override-reason');
    const reason = (reasonEl && reasonEl.value) ? String(reasonEl.value).trim() : '';
    if (reason.length < 10) {
      alert('Please enter a clinical reason (at least 10 characters).');
      return;
    }
    const form = document.getElementById('emr-form');
    const prescriptionSaveUrl = '<?php echo e(route('emr.save-prescription', [$patient, $visit])); ?>';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '<?php echo e(csrf_token()); ?>';

    function extractPrescriptionDrugs(formData) {
      const rows = {};
      for (const [key, value] of formData.entries()) {
        const match = key.match(/^drugs\[(\d+)\]\[(name|generic|dose|frequency|duration|instructions)\]$/);
        if (!match) continue;
        const rowIndex = match[1];
        const field = match[2];
        if (!rows[rowIndex]) rows[rowIndex] = {};
        rows[rowIndex][field] = String(value || '').trim();
      }
      const drugs = Object.keys(rows)
        .sort((a, b) => Number(a) - Number(b))
        .map((rowIndex) => rows[rowIndex])
        .filter((row) => !!row.name && !!row.dose && !!row.frequency && !!row.duration);
      return drugs;
    }

    const formData = new FormData(form);
    const drugs = extractPrescriptionDrugs(formData);
    if (!drugs.length) {
      alert('Add at least one complete prescription row before acknowledging.');
      return;
    }

    console.log('Submitting prescription safety acknowledgement', { drugCount: drugs.length });
    fetch(prescriptionSaveUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        drugs,
        acknowledge_safety_warnings: true,
        override_reason: reason
      })
    })
      .then((res) => res.json())
      .then((rxResult) => {
        if (rxResult?.success) {
          console.log('Prescription safety acknowledgement saved', rxResult);
          const banner = document.getElementById('prescription-safety-banner');
          if (banner) {
            banner.hidden = !rxResult.safety_ack_required;
          }
          window.closePrescriptionSafetyModal();
          if (reasonEl) reasonEl.value = '';
        } else {
          console.warn('Acknowledgement save failed', rxResult);
          alert(rxResult?.error || 'Could not save acknowledgement.');
        }
      })
      .catch((err) => {
        console.error('Acknowledgement request failed', err);
        alert('Request failed. Check console.');
      });
  };
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/emr/show.blade.php ENDPATH**/ ?>