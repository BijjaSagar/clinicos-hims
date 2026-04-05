{{-- Nephrology EMR Template --}}
<style>
.neph-card { background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; margin-bottom: 16px; }
.neph-header { padding: 12px 16px; background: linear-gradient(135deg, #e0f2fe, #f0f9ff); border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; gap: 8px; cursor: pointer; }
.neph-header h3 { font-size: 14px; font-weight: 600; color: #155e75; margin: 0; }
.neph-body { padding: 16px; }
.field-label { font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
.field-input { width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; }
.field-input:focus { outline: none; border-color: #155e75; box-shadow: 0 0 0 3px rgba(21,94,117,0.12); }
.field-select { width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: white; }
.chip { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #f1f5f9; border-radius: 20px; font-size: 12px; margin: 4px; cursor: pointer; transition: all 0.15s; }
.chip:hover { background: #e2e8f0; }
.chip.selected { background: #155e75; color: white; }
.neph-egfr { font-size: 24px; font-weight: 700; color: #155e75; text-align: center; padding: 10px; background: #e0f2fe; border-radius: 10px; }
</style>

@php
    $__nephDefaultFormData = [
        'fluid' => ['edema_grade' => '', 'jvp' => '', 'weight' => ''],
        'lab' => ['creatinine' => '', 'bun' => '', 'sex' => 'f', 'age' => '', 'na' => '', 'k' => '', 'ca' => '', 'po4' => '', 'urine_protein' => ''],
        'ckd' => ['g_stage' => '', 'a_stage' => ''],
        'dialysis' => ['type' => '', 'freq' => '', 'dry_wt' => '', 'access' => ''],
    ];
    $__nephFormDataForAlpine = ($visit ?? null)?->getStructuredField('nephrology_data') ?? $__nephDefaultFormData;
    $__nephDxForAlpine = ($visit ?? null)?->getStructuredField('nephrology_diagnoses') ?? [];
@endphp

<div x-data="nephrologyEMR()" class="neph-section">
    <div class="neph-card">
        <div class="neph-header" @click="sections.fluid = !sections.fluid">
            <span style="font-size:18px;">💧</span><h3>Fluid Status</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.fluid ? '▼' : '▶'"></span>
        </div>
        <div class="neph-body" x-show="sections.fluid" x-collapse>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">
                <div>
                    <div class="field-label">Edema grade</div>
                    <select class="field-select" x-model="formData.fluid.edema_grade" @change="updateField()">
                        <option value="">Select</option><option>1</option><option>2</option><option>3</option><option>4</option>
                    </select>
                </div>
                <div><div class="field-label">JVP (cm)</div><input type="text" class="field-input" x-model="formData.fluid.jvp" @change="updateField()"></div>
                <div><div class="field-label">Weight (kg)</div><input type="number" step="0.1" class="field-input" x-model="formData.fluid.weight" @change="updateField()"></div>
            </div>
        </div>
    </div>
    <div class="neph-card">
        <div class="neph-header" @click="sections.lab = !sections.lab">
            <span style="font-size:18px;">🧪</span><h3>Lab Review</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.lab ? '▼' : '▶'"></span>
        </div>
        <div class="neph-body" x-show="sections.lab" x-collapse>
            <div class="neph-egfr">eGFR (CKD-EPI): <span x-text="egfrDisplay"></span> mL/min/1.73m²</div>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-top:12px;">
                <div><div class="field-label">Creatinine (mg/dL)</div><input type="number" step="0.01" class="field-input" x-model="formData.lab.creatinine" @input="updateField()"></div>
                <div><div class="field-label">BUN (mg/dL)</div><input type="number" class="field-input" x-model="formData.lab.bun" @change="updateField()"></div>
                <div>
                    <div class="field-label">Sex (for eGFR)</div>
                    <select class="field-select" x-model="formData.lab.sex" @change="updateField()"><option value="f">Female</option><option value="m">Male</option></select>
                </div>
                <div><div class="field-label">Age (years)</div><input type="number" class="field-input" x-model="formData.lab.age" @input="updateField()"></div>
                <div><div class="field-label">Na</div><input type="number" step="0.1" class="field-input" x-model="formData.lab.na" @change="updateField()"></div>
                <div><div class="field-label">K</div><input type="number" step="0.1" class="field-input" x-model="formData.lab.k" @change="updateField()"></div>
                <div><div class="field-label">Ca</div><input type="number" step="0.1" class="field-input" x-model="formData.lab.ca" @change="updateField()"></div>
                <div><div class="field-label">PO4</div><input type="number" step="0.1" class="field-input" x-model="formData.lab.po4" @change="updateField()"></div>
                <div style="grid-column:1/-1;"><div class="field-label">Urine protein</div><input type="text" class="field-input" x-model="formData.lab.urine_protein" @change="updateField()" placeholder="dipstick / ACR"></div>
            </div>
        </div>
    </div>
    <div class="neph-card">
        <div class="neph-header" @click="sections.ckd = !sections.ckd">
            <span style="font-size:18px;">📊</span><h3>CKD Staging (KDIGO)</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.ckd ? '▼' : '▶'"></span>
        </div>
        <div class="neph-body" x-show="sections.ckd" x-collapse>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
                <div>
                    <div class="field-label">G stage</div>
                    <select class="field-select" x-model="formData.ckd.g_stage" @change="updateField()">
                        <option value="">Select</option><option>G1</option><option>G2</option><option>G3a</option><option>G3b</option><option>G4</option><option>G5</option>
                    </select>
                </div>
                <div>
                    <div class="field-label">Albuminuria (A)</div>
                    <select class="field-select" x-model="formData.ckd.a_stage" @change="updateField()">
                        <option value="">Select</option><option>A1</option><option>A2</option><option>A3</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="neph-card">
        <div class="neph-header" @click="sections.dialysis = !sections.dialysis">
            <span style="font-size:18px;">🩺</span><h3>Dialysis Prescription</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.dialysis ? '▼' : '▶'"></span>
        </div>
        <div class="neph-body" x-show="sections.dialysis" x-collapse>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
                <div>
                    <div class="field-label">Type</div>
                    <select class="field-select" x-model="formData.dialysis.type" @change="updateField()"><option value="">N/A</option><option>HD</option><option>PD</option></select>
                </div>
                <div><div class="field-label">Frequency</div><input type="text" class="field-input" x-model="formData.dialysis.freq" @change="updateField()"></div>
                <div><div class="field-label">Dry weight (kg)</div><input type="number" step="0.1" class="field-input" x-model="formData.dialysis.dry_wt" @change="updateField()"></div>
                <div><div class="field-label">Access</div><input type="text" class="field-input" x-model="formData.dialysis.access" @change="updateField()"></div>
            </div>
        </div>
    </div>
    <div class="neph-card">
        <div class="neph-header" @click="sections.dx = !sections.dx">
            <span style="font-size:18px;">🏥</span><h3>Diagnoses</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.dx ? '▼' : '▶'"></span>
        </div>
        <div class="neph-body" x-show="sections.dx" x-collapse>
            <div style="display:flex;flex-wrap:wrap;gap:4px;">
                <template x-for="dx in commonDiagnoses" :key="dx.code">
                    <button type="button" class="chip" :class="{'selected': selectedDiagnoses.includes(dx.code)}" @click="toggleDiagnosis(dx)">
                        <span x-text="dx.name"></span><span style="font-size:10px;opacity:0.7;" x-text="dx.code"></span>
                    </button>
                </template>
            </div>
            <input type="hidden" name="nephrology_diagnoses" :value="JSON.stringify(selectedDiagnoses)">
        </div>
    </div>
    <input type="hidden" name="nephrology_data" :value="JSON.stringify(formData)">
</div>

<script>
console.log('Nephrology EMR template loaded');
function nephrologyEMR() {
    return {
        sections: { fluid: true, lab: true, ckd: true, dialysis: false, dx: true },
        formData: @json($__nephFormDataForAlpine),
        commonDiagnoses: [
            { code: 'N18.1', name: 'CKD stage 1' }, { code: 'N18.2', name: 'CKD stage 2' }, { code: 'N18.3', name: 'CKD stage 3' },
            { code: 'N18.4', name: 'CKD stage 4' }, { code: 'N18.5', name: 'CKD stage 5' }, { code: 'N04', name: 'Nephrotic' },
            { code: 'N17', name: 'AKI' }, { code: 'N20', name: 'Stones' }, { code: 'E11.22', name: 'DN' },
        ],
        selectedDiagnoses: @json($__nephDxForAlpine),
        get egfrDisplay() {
            const scr = parseFloat(this.formData.lab.creatinine);
            const age = parseFloat(this.formData.lab.age);
            if (isNaN(scr) || isNaN(age) || scr <= 0) return '--';
            const isF = this.formData.lab.sex === 'f';
            const k = isF ? 0.7 : 0.9;
            const a = isF ? -0.241 : -0.302;
            const min = Math.min(scr / k, 1);
            const max = Math.max(scr / k, 1);
            let egfr = 142 * Math.pow(min, a) * Math.pow(max, -1.200) * Math.pow(0.9938, age);
            if (isF) egfr *= 1.012;
            const v = Math.round(egfr * 10) / 10;
            console.log('[Neph] eGFR CKD-EPI', v);
            return String(v);
        },
        init() { console.log('Nephrology EMR initialized', this.formData); },
        updateField() {
            console.log('Nephrology data updated', JSON.stringify(this.formData).substring(0, 220));
            if (window.triggerAutoSave) window.triggerAutoSave();
        },
        toggleDiagnosis(dx) {
            const idx = this.selectedDiagnoses.indexOf(dx.code);
            if (idx > -1) this.selectedDiagnoses.splice(idx, 1); else this.selectedDiagnoses.push(dx.code);
            console.log('Neph diagnosis toggled', dx.code);
            if (window.triggerAutoSave) window.triggerAutoSave();
        },
    };
}
</script>
