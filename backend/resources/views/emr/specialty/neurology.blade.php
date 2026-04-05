{{-- Neurology EMR Template --}}
<style>
.neuro-card { background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; margin-bottom: 16px; }
.neuro-header { padding: 12px 16px; background: linear-gradient(135deg, #e0e7ff, #eef2ff); border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; gap: 8px; cursor: pointer; }
.neuro-header h3 { font-size: 14px; font-weight: 600; color: #312e81; margin: 0; }
.neuro-body { padding: 16px; }
.field-label { font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
.field-input { width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; }
.field-input:focus { outline: none; border-color: #312e81; box-shadow: 0 0 0 3px rgba(49,46,129,0.12); }
.field-select { width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: white; }
.chip { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #f1f5f9; border-radius: 20px; font-size: 12px; margin: 4px; cursor: pointer; transition: all 0.15s; }
.chip:hover { background: #e2e8f0; }
.chip.selected { background: #312e81; color: white; }
.neuro-gcs { font-size: 28px; font-weight: 800; text-align: center; padding: 12px; background: #e0e7ff; color: #312e81; border-radius: 10px; }
.neuro-cn { display: grid; grid-template-columns: repeat(4, 1fr); gap: 6px; font-size: 11px; }
</style>

<div x-data="neurologyEMR()" class="neuro-section">
    <div class="neuro-card">
        <div class="neuro-header" @click="sections.hx = !sections.hx">
            <span style="font-size:18px;">📝</span><h3>Neuro History</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.hx ? '▼' : '▶'"></span>
        </div>
        <div class="neuro-body" x-show="sections.hx" x-collapse>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:10px;">
                <template x-for="s in ['headache','seizure','weakness','sensory']" :key="s">
                    <label style="display:flex;align-items:center;gap:8px;text-transform:capitalize;">
                        <input type="checkbox" :checked="formData.hx[s]" @change="toggleHx(s)"><span x-text="s"></span>
                    </label>
                </template>
            </div>
        </div>
    </div>
    <div class="neuro-card">
        <div class="neuro-header" @click="sections.cn = !sections.cn">
            <span style="font-size:18px;">👁️</span><h3>Cranial Nerves I–XII</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.cn ? '▼' : '▶'"></span>
        </div>
        <div class="neuro-body" x-show="sections.cn" x-collapse>
            <div class="neuro-cn">
                <template x-for="n in cranialNerves" :key="n">
                    <div style="border:1px solid #e5e7eb;border-radius:6px;padding:6px;">
                        <div class="field-label" x-text="'CN ' + n"></div>
                        <select class="field-select" style="font-size:11px;padding:4px;" x-model="formData.cn[n]" @change="updateField()">
                            <option value="normal">Normal</option><option value="abnormal">Abnormal</option>
                        </select>
                    </div>
                </template>
            </div>
        </div>
    </div>
    <div class="neuro-card">
        <div class="neuro-header" @click="sections.motor = !sections.motor">
            <span style="font-size:18px;">💪</span><h3>Motor (MRC 0–5)</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.motor ? '▼' : '▶'"></span>
        </div>
        <div class="neuro-body" x-show="sections.motor" x-collapse>
            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px;font-size:12px;">
                <template x-for="seg in motorSegments" :key="seg.key">
                    <div>
                        <div class="field-label" x-text="seg.label"></div>
                        <select class="field-select" x-model="formData.motor[seg.key]" @change="updateField()">
                            <template x-for="g in [0,1,2,3,4,5]" :key="g"><option :value="String(g)" x-text="g"></option></template>
                        </select>
                    </div>
                </template>
            </div>
        </div>
    </div>
    <div class="neuro-card">
        <div class="neuro-header" @click="sections.gcs = !sections.gcs">
            <span style="font-size:18px;">🧠</span><h3>GCS Calculator</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.gcs ? '▼' : '▶'"></span>
        </div>
        <div class="neuro-body" x-show="sections.gcs" x-collapse>
            <div class="neuro-gcs">GCS <span x-text="gcsTotal"></span> / 15</div>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-top:12px;">
                <div>
                    <div class="field-label">Eye (1–4)</div>
                    <select class="field-select" x-model="formData.gcs.eye" @change="updateField()"><option>1</option><option>2</option><option>3</option><option>4</option></select>
                </div>
                <div>
                    <div class="field-label">Verbal (1–5)</div>
                    <select class="field-select" x-model="formData.gcs.verbal" @change="updateField()"><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option></select>
                </div>
                <div>
                    <div class="field-label">Motor (1–6)</div>
                    <select class="field-select" x-model="formData.gcs.motor" @change="updateField()"><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option><option>6</option></select>
                </div>
            </div>
        </div>
    </div>
    <div class="neuro-card">
        <div class="neuro-header" @click="sections.dx = !sections.dx">
            <span style="font-size:18px;">🏥</span><h3>Diagnoses</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.dx ? '▼' : '▶'"></span>
        </div>
        <div class="neuro-body" x-show="sections.dx" x-collapse>
            <div style="display:flex;flex-wrap:wrap;gap:4px;">
                <template x-for="dx in commonDiagnoses" :key="dx.code">
                    <button type="button" class="chip" :class="{'selected': selectedDiagnoses.includes(dx.code)}" @click="toggleDiagnosis(dx)">
                        <span x-text="dx.name"></span><span style="font-size:10px;opacity:0.7;" x-text="dx.code"></span>
                    </button>
                </template>
            </div>
            <input type="hidden" name="neurology_diagnoses" :value="JSON.stringify(selectedDiagnoses)">
        </div>
    </div>
    <input type="hidden" name="neurology_data" :value="JSON.stringify(formData)">
</div>

@php
    $neurologyFormDataDefault = [
        'hx' => ['headache' => false, 'seizure' => false, 'weakness' => false, 'sensory' => false],
        'cn' => ['I' => 'normal', 'II' => 'normal', 'III' => 'normal', 'IV' => 'normal', 'V' => 'normal', 'VI' => 'normal', 'VII' => 'normal', 'VIII' => 'normal', 'IX' => 'normal', 'X' => 'normal', 'XI' => 'normal', 'XII' => 'normal'],
        'motor' => ['ue_r' => '5', 'ue_l' => '5', 'le_r' => '5', 'le_l' => '5'],
        'gcs' => ['eye' => '4', 'verbal' => '5', 'motor' => '6'],
    ];
@endphp

<script>
console.log('Neurology EMR template loaded');
function neurologyEMR() {
    const defaultCn = () => Object.fromEntries(['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'].map(n => [n, 'normal']));
    return {
        sections: { hx: true, cn: true, motor: true, gcs: true, dx: true },
        cranialNerves: ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'],
        motorSegments: [
            { key: 'ue_r', label: 'UE R' }, { key: 'ue_l', label: 'UE L' }, { key: 'le_r', label: 'LE R' }, { key: 'le_l', label: 'LE L' },
        ],
        formData: @json(($visit ?? null)?->getStructuredField('neurology_data') ?? $neurologyFormDataDefault),
        commonDiagnoses: [
            { code: 'G40', name: 'Epilepsy' }, { code: 'G43', name: 'Migraine' }, { code: 'G20', name: "Parkinson's" },
            { code: 'I63', name: 'Stroke' }, { code: 'G35', name: 'MS' }, { code: 'G61', name: 'GBS' }, { code: 'G30', name: "Alzheimer's" },
            { code: 'G93.1', name: 'Anoxic brain' }, { code: 'R51', name: 'Headache' },
        ],
        selectedDiagnoses: @json(($visit ?? null)?->getStructuredField('neurology_diagnoses') ?? []),
        init() {
            console.log('Neurology EMR initialized', this.formData);
            if (!this.formData.cn || typeof this.formData.cn !== 'object') this.formData.cn = defaultCn();
            const need = ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
            need.forEach(n => { if (this.formData.cn[n] === undefined) this.formData.cn[n] = 'normal'; });
            ['ue_r','ue_l','le_r','le_l'].forEach(k => { if (!this.formData.motor) this.formData.motor = {}; if (this.formData.motor[k] === undefined) this.formData.motor[k] = '5'; });
            if (!this.formData.gcs) this.formData.gcs = { eye: '4', verbal: '5', motor: '6' };
        },
        get gcsTotal() {
            const e = parseInt(this.formData.gcs?.eye, 10) || 0, v = parseInt(this.formData.gcs?.verbal, 10) || 0, m = parseInt(this.formData.gcs?.motor, 10) || 0;
            const t = e + v + m;
            console.log('[Neuro] GCS', t);
            return t;
        },
        toggleHx(s) { this.formData.hx[s] = !this.formData.hx[s]; this.updateField(); },
        updateField() {
            console.log('Neurology data updated', JSON.stringify(this.formData).substring(0, 200));
            if (window.triggerAutoSave) window.triggerAutoSave();
        },
        toggleDiagnosis(dx) {
            const idx = this.selectedDiagnoses.indexOf(dx.code);
            if (idx > -1) this.selectedDiagnoses.splice(idx, 1); else this.selectedDiagnoses.push(dx.code);
            console.log('Neuro diagnosis toggled', dx.code);
            if (window.triggerAutoSave) window.triggerAutoSave();
        },
    };
}
</script>
