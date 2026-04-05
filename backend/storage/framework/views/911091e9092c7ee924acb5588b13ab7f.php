
<style>
.uro-card { background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; margin-bottom: 16px; }
.uro-header { padding: 12px 16px; background: linear-gradient(135deg, #dbeafe, #eff6ff); border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; gap: 8px; cursor: pointer; }
.uro-header h3 { font-size: 14px; font-weight: 600; color: #1e3a5f; margin: 0; }
.uro-body { padding: 16px; }
.field-label { font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
.field-input { width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; }
.field-input:focus { outline: none; border-color: #1e3a5f; box-shadow: 0 0 0 3px rgba(30,58,95,0.12); }
.field-select { width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: white; }
.chip { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #f1f5f9; border-radius: 20px; font-size: 12px; margin: 4px; cursor: pointer; transition: all 0.15s; }
.chip:hover { background: #e2e8f0; }
.chip.selected { background: #1e3a5f; color: white; }
.uro-ipss { font-size: 20px; font-weight: 700; color: #1e3a5f; text-align: center; padding: 10px; background: #dbeafe; border-radius: 10px; margin-top: 10px; }
</style>

<div x-data="urologyEMR()" class="uro-section">
    <div class="uro-card">
        <div class="uro-header" @click="sections.luts = !sections.luts">
            <span style="font-size:18px;">💧</span><h3>LUTS Assessment</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.luts ? '▼' : '▶'"></span>
        </div>
        <div class="uro-body" x-show="sections.luts" x-collapse>
            <div class="field-label">Storage</div>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:8px;">
                <template x-for="s in lutsStorage" :key="s">
                    <label style="font-size:12px;display:flex;align-items:center;gap:6px;"><input type="checkbox" :checked="formData.luts.storage.includes(s)" @change="toggleLuts('storage',s)"><span x-text="s"></span></label>
                </template>
            </div>
            <div class="field-label" style="margin-top:10px;">Voiding</div>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:8px;">
                <template x-for="s in lutsVoiding" :key="s">
                    <label style="font-size:12px;display:flex;align-items:center;gap:6px;"><input type="checkbox" :checked="formData.luts.voiding.includes(s)" @change="toggleLuts('voiding',s)"><span x-text="s"></span></label>
                </template>
            </div>
        </div>
    </div>
    <div class="uro-card">
        <div class="uro-header" @click="sections.ipss = !sections.ipss">
            <span style="font-size:18px;">📋</span><h3>IPSS</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.ipss ? '▼' : '▶'"></span>
        </div>
        <div class="uro-body" x-show="sections.ipss" x-collapse>
            <template x-for="(q, i) in ipssQs" :key="i">
                <div style="margin-bottom:6px;display:flex;align-items:center;gap:8px;">
                    <span style="flex:1;font-size:11px;" x-text="q"></span>
                    <select class="field-select" style="max-width:70px;" x-model="formData.ipss[i]" @change="updateField()"><option>0</option><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option></select>
                </div>
            </template>
            <div style="margin-top:10px;"><div class="field-label">QoL if urinary state stayed same</div>
                <select class="field-select" x-model="formData.ipss_qol" @change="updateField()"><option>0</option><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option><option>6</option></select>
            </div>
            <div class="uro-ipss">IPSS Total: <span x-text="ipssTotal"></span> / 35</div>
        </div>
    </div>
    <div class="uro-card">
        <div class="uro-header" @click="sections.dre = !sections.dre">
            <span style="font-size:18px;">🩺</span><h3>DRE Findings</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.dre ? '▼' : '▶'"></span>
        </div>
        <div class="uro-body" x-show="sections.dre" x-collapse>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
                <div><div class="field-label">Prostate size</div><select class="field-select" x-model="formData.dre.size" @change="updateField()"><option value="">—</option><option>Normal</option><option>Enlarged I</option><option>Enlarged II</option><option>Enlarged III</option></select></div>
                <div><div class="field-label">Consistency</div><select class="field-select" x-model="formData.dre.consistency" @change="updateField()"><option value="">—</option><option>Soft</option><option>Firm</option><option>Hard</option></select></div>
                <div><div class="field-label">Nodules</div><select class="field-select" x-model="formData.dre.nodules" @change="updateField()"><option value="">—</option><option>None</option><option>Suspicious</option></select></div>
                <div><div class="field-label">Median sulcus</div><select class="field-select" x-model="formData.dre.sulcus" @change="updateField()"><option value="">—</option><option>Preserved</option><option>Effaced</option></select></div>
            </div>
        </div>
    </div>
    <div class="uro-card">
        <div class="uro-header" @click="sections.flow = !sections.flow">
            <span style="font-size:18px;">📈</span><h3>Uroflowmetry</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.flow ? '▼' : '▶'"></span>
        </div>
        <div class="uro-body" x-show="sections.flow" x-collapse>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">
                <div><div class="field-label">Qmax (mL/s)</div><input type="number" step="0.1" class="field-input" x-model="formData.flow.qmax" @change="updateField()"></div>
                <div><div class="field-label">Voided vol (mL)</div><input type="number" class="field-input" x-model="formData.flow.vv" @change="updateField()"></div>
                <div><div class="field-label">PVR (mL)</div><input type="number" class="field-input" x-model="formData.flow.pvr" @change="updateField()"></div>
            </div>
        </div>
    </div>
    <div class="uro-card">
        <div class="uro-header" @click="sections.dx = !sections.dx">
            <span style="font-size:18px;">🏥</span><h3>Diagnoses</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.dx ? '▼' : '▶'"></span>
        </div>
        <div class="uro-body" x-show="sections.dx" x-collapse>
            <div style="display:flex;flex-wrap:wrap;gap:4px;">
                <template x-for="dx in commonDiagnoses" :key="dx.code">
                    <button type="button" class="chip" :class="{'selected': selectedDiagnoses.includes(dx.code)}" @click="toggleDiagnosis(dx)">
                        <span x-text="dx.name"></span><span style="font-size:10px;opacity:0.7;" x-text="dx.code"></span>
                    </button>
                </template>
            </div>
            <input type="hidden" name="urology_diagnoses" :value="JSON.stringify(selectedDiagnoses)">
        </div>
    </div>
    <input type="hidden" name="urology_data" :value="JSON.stringify(formData)">
</div>

<?php
    $urologyFormDataDefault = [
        'luts' => ['storage' => [], 'voiding' => []],
        'ipss' => ['0', '0', '0', '0', '0', '0', '0'],
        'ipss_qol' => '0',
        'dre' => ['size' => '', 'consistency' => '', 'nodules' => '', 'sulcus' => ''],
        'flow' => ['qmax' => '', 'vv' => '', 'pvr' => ''],
    ];
?>

<script>
console.log('Urology EMR template loaded');
function urologyEMR() {
    return {
        sections: { luts: true, ipss: true, dre: true, flow: true, dx: true },
        lutsStorage: ['Frequency', 'Urgency', 'Nocturia', 'Incontinence'],
        lutsVoiding: ['Weak stream', 'Intermittency', 'Straining', 'Incomplete emptying'],
        ipssQs: ['Incomplete emptying', 'Frequency', 'Intermittency', 'Urgency', 'Weak stream', 'Straining', 'Nocturia'],
        formData: <?php echo json_encode(($visit ?? null)?->getStructuredField('urology_data') ?? $urologyFormDataDefault, 15, 512) ?>,
        commonDiagnoses: [
            { code: 'N40', name: 'BPH' }, { code: 'N20', name: 'Renal stone' }, { code: 'C61', name: 'Prostate cancer' },
            { code: 'N30', name: 'Cystitis' }, { code: 'N52', name: 'ED' }, { code: 'N13', name: 'Hydronephrosis' },
            { code: 'N41.1', name: 'Chronic prostatitis' }, { code: 'C64', name: 'Renal cancer' },
        ],
        selectedDiagnoses: <?php echo json_encode(($visit ?? null)?->getStructuredField('urology_diagnoses') ?? [], 15, 512) ?>,
        get ipssTotal() {
            let t = 0;
            (this.formData.ipss || []).forEach(x => { t += parseInt(x, 10) || 0; });
            console.log('[Uro] IPSS', t);
            return t;
        },
        init() { console.log('Urology EMR initialized', this.formData); if (!Array.isArray(this.formData.luts.storage)) this.formData.luts.storage = []; if (!Array.isArray(this.formData.luts.voiding)) this.formData.luts.voiding = []; },
        toggleLuts(which, s) {
            const arr = this.formData.luts[which];
            const i = arr.indexOf(s);
            if (i > -1) arr.splice(i, 1); else arr.push(s);
            console.log('[Uro] LUTS', which, s);
            this.updateField();
        },
        updateField() {
            console.log('Urology data updated', JSON.stringify(this.formData).substring(0, 220));
            if (window.triggerAutoSave) window.triggerAutoSave();
        },
        toggleDiagnosis(dx) {
            const idx = this.selectedDiagnoses.indexOf(dx.code);
            if (idx > -1) this.selectedDiagnoses.splice(idx, 1); else this.selectedDiagnoses.push(dx.code);
            console.log('Uro diagnosis toggled', dx.code);
            if (window.triggerAutoSave) window.triggerAutoSave();
        },
    };
}
</script>
<?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/emr/specialty/urology.blade.php ENDPATH**/ ?>