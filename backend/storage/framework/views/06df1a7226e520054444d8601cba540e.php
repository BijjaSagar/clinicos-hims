
<style>
.gsurg-card { background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; margin-bottom: 16px; }
.gsurg-header { padding: 12px 16px; background: linear-gradient(135deg, #f3f4f6, #fafafa); border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; gap: 8px; cursor: pointer; }
.gsurg-header h3 { font-size: 14px; font-weight: 600; color: #374151; margin: 0; }
.gsurg-body { padding: 16px; }
.field-label { font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
.field-input { width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; }
.field-input:focus { outline: none; border-color: #dc2626; box-shadow: 0 0 0 3px rgba(220,38,38,0.08); }
.field-select { width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: white; }
.chip { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #f1f5f9; border-radius: 20px; font-size: 12px; margin: 4px; cursor: pointer; transition: all 0.15s; }
.chip:hover { background: #e2e8f0; }
.chip.selected { background: #374151; color: white; }
</style>

<div x-data="generalSurgeryEMR()" class="gsurg-section">
    <div class="gsurg-card">
        <div class="gsurg-header" @click="sections.preop = !sections.preop">
            <span style="font-size:18px;">✅</span><h3>Pre-op Assessment</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.preop ? '▼' : '▶'"></span>
        </div>
        <div class="gsurg-body" x-show="sections.preop" x-collapse>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
                <div><div class="field-label">ASA</div><select class="field-select" x-model="formData.preop.asa" @change="updateField()"><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option><option>6</option></select></div>
                <div><div class="field-label">Mallampati</div><select class="field-select" x-model="formData.preop.mallampati" @change="updateField()"><option>1</option><option>2</option><option>3</option><option>4</option></select></div>
                <div style="grid-column:1/-1;">
                    <div class="field-label">Comorbidities</div>
                    <div style="display:flex;flex-wrap:wrap;gap:8px;">
                        <template x-for="c in comorbOpts" :key="c">
                            <label style="font-size:12px;display:flex;align-items:center;gap:6px;"><input type="checkbox" :checked="formData.preop.comorb.includes(c)" @change="toggleComorb(c)"><span x-text="c"></span></label>
                        </template>
                    </div>
                </div>
                <div><div class="field-label">Fasting</div><select class="field-select" x-model="formData.preop.fasting" @change="updateField()"><option value="">—</option><option>Clear 6h</option><option>Solid 8h</option><option>Emergency</option></select></div>
                <div><label style="display:flex;align-items:center;gap:8px;margin-top:20px;"><input type="checkbox" x-model="formData.preop.consent" @change="updateField()"> Consent obtained</label></div>
            </div>
        </div>
    </div>
    <div class="gsurg-card">
        <div class="gsurg-header" @click="sections.local = !sections.local">
            <span style="font-size:18px;">🔍</span><h3>Local Examination</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.local ? '▼' : '▶'"></span>
        </div>
        <div class="gsurg-body" x-show="sections.local" x-collapse>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
                <template x-for="f in localFields" :key="f">
                    <div><div class="field-label" x-text="f"></div><textarea class="field-input" style="min-height:50px;" x-model="formData.local[f.toLowerCase()]" @change="updateField()"></textarea></div>
                </template>
            </div>
        </div>
    </div>
    <div class="gsurg-card">
        <div class="gsurg-header" @click="sections.opnote = !sections.opnote">
            <span style="font-size:18px;">📝</span><h3>Operative Note</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.opnote ? '▼' : '▶'"></span>
        </div>
        <div class="gsurg-body" x-show="sections.opnote" x-collapse>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
                <div style="grid-column:1/-1;"><div class="field-label">Procedure</div><input type="text" class="field-input" x-model="formData.opnote.procedure" @change="updateField()"></div>
                <div><div class="field-label">Surgeon</div><input type="text" class="field-input" x-model="formData.opnote.surgeon" @change="updateField()"></div>
                <div><div class="field-label">Anesthesia</div><input type="text" class="field-input" x-model="formData.opnote.anesthesia" @change="updateField()"></div>
                <div style="grid-column:1/-1;"><div class="field-label">Incision</div><textarea class="field-input" style="min-height:40px;" x-model="formData.opnote.incision" @change="updateField()"></textarea></div>
                <div style="grid-column:1/-1;"><div class="field-label">Findings</div><textarea class="field-input" style="min-height:50px;" x-model="formData.opnote.findings" @change="updateField()"></textarea></div>
                <div style="grid-column:1/-1;"><div class="field-label">Procedure details</div><textarea class="field-input" style="min-height:50px;" x-model="formData.opnote.details" @change="updateField()"></textarea></div>
                <div><div class="field-label">Drain</div><input type="text" class="field-input" x-model="formData.opnote.drain" @change="updateField()"></div>
                <div><div class="field-label">EBL (mL)</div><input type="number" class="field-input" x-model="formData.opnote.ebl" @change="updateField()"></div>
                <div><div class="field-label">Specimen</div><input type="text" class="field-input" x-model="formData.opnote.specimen" @change="updateField()"></div>
                <div style="grid-column:1/-1;"><div class="field-label">Complications</div><textarea class="field-input" style="min-height:40px;" x-model="formData.opnote.complications" @change="updateField()"></textarea></div>
            </div>
        </div>
    </div>
    <div class="gsurg-card">
        <div class="gsurg-header" @click="sections.postop = !sections.postop">
            <span style="font-size:18px;">🏥</span><h3>Post-op Orders</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.postop ? '▼' : '▶'"></span>
        </div>
        <div class="gsurg-body" x-show="sections.postop" x-collapse>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
                <div><div class="field-label">Vitals frequency</div><input type="text" class="field-input" x-model="formData.postop.vitals" @change="updateField()"></div>
                <div><div class="field-label">IV fluids</div><input type="text" class="field-input" x-model="formData.postop.iv" @change="updateField()"></div>
                <div><div class="field-label">Antibiotics</div><input type="text" class="field-input" x-model="formData.postop.abx" @change="updateField()"></div>
                <div><div class="field-label">Analgesics</div><input type="text" class="field-input" x-model="formData.postop.analgesia" @change="updateField()"></div>
                <div style="grid-column:1/-1;">
                    <label style="font-size:12px;display:flex;align-items:center;gap:8px;"><input type="checkbox" x-model="formData.postop.dvt_mechanical" @change="updateField()"> DVT mechanical prophylaxis</label>
                    <label style="font-size:12px;display:flex;align-items:center;gap:8px;"><input type="checkbox" x-model="formData.postop.dvt_chem" @change="updateField()"> DVT chemoprophylaxis</label>
                </div>
            </div>
        </div>
    </div>
    <div class="gsurg-card">
        <div class="gsurg-header" @click="sections.dx = !sections.dx">
            <span style="font-size:18px;">🏷️</span><h3>Diagnoses</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.dx ? '▼' : '▶'"></span>
        </div>
        <div class="gsurg-body" x-show="sections.dx" x-collapse>
            <div style="display:flex;flex-wrap:wrap;gap:4px;">
                <template x-for="dx in commonDiagnoses" :key="dx.code">
                    <button type="button" class="chip" :class="{'selected': selectedDiagnoses.includes(dx.code)}" @click="toggleDiagnosis(dx)">
                        <span x-text="dx.name"></span><span style="font-size:10px;opacity:0.7;" x-text="dx.code"></span>
                    </button>
                </template>
            </div>
            <input type="hidden" name="general_surgery_diagnoses" :value="JSON.stringify(selectedDiagnoses)">
        </div>
    </div>
    <input type="hidden" name="general_surgery_data" :value="JSON.stringify(formData)">
</div>

<?php
    $generalSurgeryFormDefaults = [
        'preop' => ['asa' => '2', 'mallampati' => '1', 'comorb' => [], 'fasting' => '', 'consent' => false],
        'local' => ['site' => '', 'size' => '', 'shape' => '', 'surface' => '', 'consistency' => ''],
        'opnote' => ['procedure' => '', 'surgeon' => '', 'anesthesia' => '', 'incision' => '', 'findings' => '', 'details' => '', 'drain' => '', 'ebl' => '', 'specimen' => '', 'complications' => ''],
        'postop' => ['vitals' => '', 'iv' => '', 'abx' => '', 'analgesia' => '', 'dvt_mechanical' => false, 'dvt_chem' => false],
    ];
    $generalSurgeryFormData = ($visit ?? null)?->getStructuredField('general_surgery_data') ?? $generalSurgeryFormDefaults;
?>
<script>
console.log('General Surgery EMR template loaded');
function generalSurgeryEMR() {
    return {
        sections: { preop: true, local: true, opnote: true, postop: true, dx: true },
        comorbOpts: ['DM', 'HTN', 'CAD', 'CKD', 'COPD', 'Bleeding disorder'],
        localFields: ['Site', 'Size', 'Shape', 'Surface', 'Consistency'],
        formData: <?php echo json_encode($generalSurgeryFormData, 15, 512) ?>,
        commonDiagnoses: [
            { code: 'K40', name: 'Hernia' }, { code: 'K80', name: 'Gallstones' }, { code: 'K35', name: 'Appendicitis' },
            { code: 'I84', name: 'Hemorrhoids' }, { code: 'K60', name: 'Fissure' }, { code: 'L03', name: 'Cellulitis' },
            { code: 'T81.4', name: 'Wound infection' }, { code: 'K81', name: 'Cholecystitis' },
        ],
        selectedDiagnoses: <?php echo json_encode(($visit ?? null)?->getStructuredField('general_surgery_diagnoses') ?? [], 15, 512) ?>,
        init() {
            console.log('General Surgery EMR initialized', this.formData);
            if (!Array.isArray(this.formData.preop.comorb)) this.formData.preop.comorb = [];
        },
        toggleComorb(c) {
            const a = this.formData.preop.comorb, i = a.indexOf(c);
            if (i > -1) a.splice(i, 1); else a.push(c);
            console.log('[GSurg] comorb', c);
            this.updateField();
        },
        updateField() {
            console.log('General Surgery data updated', JSON.stringify(this.formData).substring(0, 220));
            if (window.triggerAutoSave) window.triggerAutoSave();
        },
        toggleDiagnosis(dx) {
            const idx = this.selectedDiagnoses.indexOf(dx.code);
            if (idx > -1) this.selectedDiagnoses.splice(idx, 1); else this.selectedDiagnoses.push(dx.code);
            console.log('GSurg diagnosis toggled', dx.code);
            if (window.triggerAutoSave) window.triggerAutoSave();
        },
    };
}
</script>
<?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/emr/specialty/general_surgery.blade.php ENDPATH**/ ?>