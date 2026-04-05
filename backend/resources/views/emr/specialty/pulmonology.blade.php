{{-- Pulmonology EMR Template --}}
<style>
.pulm-card { background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; margin-bottom: 16px; }
.pulm-header { padding: 12px 16px; background: linear-gradient(135deg, #e0f2fe, #f0f9ff); border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; gap: 8px; cursor: pointer; }
.pulm-header h3 { font-size: 14px; font-weight: 600; color: #075985; margin: 0; }
.pulm-body { padding: 16px; }
.field-label { font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
.field-input { width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; }
.field-input:focus { outline: none; border-color: #075985; box-shadow: 0 0 0 3px rgba(7,89,133,0.12); }
.field-select { width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: white; }
.chip { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #f1f5f9; border-radius: 20px; font-size: 12px; margin: 4px; cursor: pointer; transition: all 0.15s; }
.chip:hover { background: #e2e8f0; }
.chip.selected { background: #075985; color: white; }
.pulm-hint { font-size: 12px; color: #0369a1; margin-top: 8px; padding: 8px; background: #e0f2fe; border-radius: 8px; }
</style>

<div x-data="pulmonologyEMR()" class="pulm-section">
    <div class="pulm-card">
        <div class="pulm-header" @click="sections.resp = !sections.resp">
            <span style="font-size:18px;">🫁</span><h3>Respiratory Symptoms</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.resp ? '▼' : '▶'"></span>
        </div>
        <div class="pulm-body" x-show="sections.resp" x-collapse>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
                <div>
                    <div class="field-label">Cough type</div>
                    <select class="field-select" x-model="formData.resp.cough" @change="updateField()"><option value="">None</option><option>Dry</option><option>Productive</option><option>Barking</option></select>
                </div>
                <div><div class="field-label">Sputum</div><input type="text" class="field-input" x-model="formData.resp.sputum" @change="updateField()"></div>
                <div><label style="display:flex;align-items:center;gap:8px;"><input type="checkbox" x-model="formData.resp.hemoptysis" @change="updateField()"> Hemoptysis</label></div>
                <div>
                    <div class="field-label">Dyspnea mMRC</div>
                    <select class="field-select" x-model="formData.resp.mmrc" @change="updateField()">
                        <option value="">Select</option><option>0</option><option>1</option><option>2</option><option>3</option><option>4</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="pulm-card">
        <div class="pulm-header" @click="sections.smoke = !sections.smoke">
            <span style="font-size:18px;">🚭</span><h3>Smoking History</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.smoke ? '▼' : '▶'"></span>
        </div>
        <div class="pulm-body" x-show="sections.smoke" x-collapse>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">
                <div>
                    <div class="field-label">Status</div>
                    <select class="field-select" x-model="formData.smoke.status" @change="updateField()"><option value="">Select</option><option>Never</option><option>Former</option><option>Current</option></select>
                </div>
                <div><div class="field-label">Packs/day</div><input type="number" step="0.1" class="field-input" x-model="formData.smoke.ppd" @input="updateField()"></div>
                <div><div class="field-label">Years</div><input type="number" class="field-input" x-model="formData.smoke.years" @input="updateField()"></div>
            </div>
            <div class="pulm-hint">Pack-years: <strong x-text="packYears"></strong></div>
        </div>
    </div>
    <div class="pulm-card">
        <div class="pulm-header" @click="sections.spiro = !sections.spiro">
            <span style="font-size:18px;">📉</span><h3>Spirometry</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.spiro ? '▼' : '▶'"></span>
        </div>
        <div class="pulm-body" x-show="sections.spiro" x-collapse>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">
                <div><div class="field-label">FEV1 % pred</div><input type="number" class="field-input" x-model="formData.spiro.fev1" @change="updateField()"></div>
                <div><div class="field-label">FVC % pred</div><input type="number" class="field-input" x-model="formData.spiro.fvc" @change="updateField()"></div>
                <div><div class="field-label">FEV1/FVC ratio</div><input type="number" step="0.01" class="field-input" x-model="formData.spiro.ratio" @input="updateField()"></div>
            </div>
            <div class="pulm-hint" x-text="spiroInterp"></div>
        </div>
    </div>
    <div class="pulm-card">
        <div class="pulm-header" @click="sections.abg = !sections.abg">
            <span style="font-size:18px;">🧪</span><h3>ABG</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.abg ? '▼' : '▶'"></span>
        </div>
        <div class="pulm-body" x-show="sections.abg" x-collapse>
            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;">
                <div><div class="field-label">pH</div><input type="number" step="0.01" class="field-input" x-model="formData.abg.ph" @input="updateField()"></div>
                <div><div class="field-label">pCO2</div><input type="number" step="0.1" class="field-input" x-model="formData.abg.pco2" @input="updateField()"></div>
                <div><div class="field-label">pO2</div><input type="number" class="field-input" x-model="formData.abg.po2" @input="updateField()"></div>
                <div><div class="field-label">HCO3</div><input type="number" step="0.1" class="field-input" x-model="formData.abg.hco3" @input="updateField()"></div>
            </div>
            <div class="pulm-hint" x-text="abgInterp"></div>
        </div>
    </div>
    <div class="pulm-card">
        <div class="pulm-header" @click="sections.dx = !sections.dx">
            <span style="font-size:18px;">🏥</span><h3>Diagnoses</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.dx ? '▼' : '▶'"></span>
        </div>
        <div class="pulm-body" x-show="sections.dx" x-collapse>
            <div style="display:flex;flex-wrap:wrap;gap:4px;">
                <template x-for="dx in commonDiagnoses" :key="dx.code">
                    <button type="button" class="chip" :class="{'selected': selectedDiagnoses.includes(dx.code)}" @click="toggleDiagnosis(dx)">
                        <span x-text="dx.name"></span><span style="font-size:10px;opacity:0.7;" x-text="dx.code"></span>
                    </button>
                </template>
            </div>
            <input type="hidden" name="pulmonology_diagnoses" :value="JSON.stringify(selectedDiagnoses)">
        </div>
    </div>
    <input type="hidden" name="pulmonology_data" :value="JSON.stringify(formData)">
</div>

@php
    $pulmonologyFormDataDefault = [
        'resp' => ['cough' => '', 'sputum' => '', 'hemoptysis' => false, 'mmrc' => ''],
        'smoke' => ['status' => '', 'ppd' => '', 'years' => ''],
        'spiro' => ['fev1' => '', 'fvc' => '', 'ratio' => ''],
        'abg' => ['ph' => '', 'pco2' => '', 'po2' => '', 'hco3' => ''],
    ];
@endphp

<script>
console.log('Pulmonology EMR template loaded');
function pulmonologyEMR() {
    return {
        sections: { resp: true, smoke: true, spiro: true, abg: true, dx: true },
        formData: @json(($visit ?? null)?->getStructuredField('pulmonology_data') ?? $pulmonologyFormDataDefault),
        commonDiagnoses: [
            { code: 'J45', name: 'Asthma' }, { code: 'J44', name: 'COPD' }, { code: 'J18', name: 'Pneumonia' },
            { code: 'J90', name: 'Pleural effusion' }, { code: 'A15', name: 'TB' }, { code: 'C34', name: 'Lung cancer' },
            { code: 'J43', name: 'Emphysema' }, { code: 'J84.9', name: 'ILD' }, { code: 'J96.00', name: 'Resp failure' },
        ],
        selectedDiagnoses: @json(($visit ?? null)?->getStructuredField('pulmonology_diagnoses') ?? []),
        get packYears() {
            const p = parseFloat(this.formData.smoke.ppd), y = parseFloat(this.formData.smoke.years);
            if (isNaN(p) || isNaN(y)) return '--';
            const py = Math.round(p * y * 10) / 10;
            console.log('[Pulm] pack-years', py);
            return String(py);
        },
        get spiroInterp() {
            const r = parseFloat(this.formData.spiro.ratio);
            if (isNaN(r)) return 'Enter FEV1/FVC ratio for interpretation.';
            if (r < 0.70) return 'Ratio <0.70 suggests obstructive pattern (clinical correlation).';
            return 'Ratio ≥0.70 — correlate with volumes and clinical picture.';
        },
        get abgInterp() {
            const ph = parseFloat(this.formData.abg.ph), pc = parseFloat(this.formData.abg.pco2), hc = parseFloat(this.formData.abg.hco3);
            if (isNaN(ph)) return 'Enter pH for acid-base interpretation.';
            let t = [];
            if (ph < 7.35) t.push('Acidemia');
            else if (ph > 7.45) t.push('Alkalemia');
            else t.push('pH roughly normal');
            if (!isNaN(pc) && pc > 45) t.push('respiratory acidosis component');
            if (!isNaN(pc) && pc < 35) t.push('respiratory alkalosis component');
            if (!isNaN(hc) && hc < 22) t.push('metabolic acidosis component');
            if (!isNaN(hc) && hc > 26) t.push('metabolic alkalosis component');
            console.log('[Pulm] ABG interp', t.join('; '));
            return t.join('; ') + ' — confirm with full clinical context.';
        },
        init() { console.log('Pulmonology EMR initialized', this.formData); },
        updateField() {
            console.log('Pulmonology data updated', JSON.stringify(this.formData).substring(0, 220));
            if (window.triggerAutoSave) window.triggerAutoSave();
        },
        toggleDiagnosis(dx) {
            const idx = this.selectedDiagnoses.indexOf(dx.code);
            if (idx > -1) this.selectedDiagnoses.splice(idx, 1); else this.selectedDiagnoses.push(dx.code);
            console.log('Pulm diagnosis toggled', dx.code);
            if (window.triggerAutoSave) window.triggerAutoSave();
        },
    };
}
</script>
