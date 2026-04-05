{{-- Oncology EMR Template --}}
<style>
.onco-card { background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; margin-bottom: 16px; }
.onco-header { padding: 12px 16px; background: linear-gradient(135deg, #ffedd5, #fff7ed); border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; gap: 8px; cursor: pointer; }
.onco-header h3 { font-size: 14px; font-weight: 600; color: #9a3412; margin: 0; }
.onco-body { padding: 16px; }
.field-label { font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
.field-input { width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; }
.field-input:focus { outline: none; border-color: #9a3412; box-shadow: 0 0 0 3px rgba(154,52,18,0.12); }
.field-select { width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: white; }
.chip { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #f1f5f9; border-radius: 20px; font-size: 12px; margin: 4px; cursor: pointer; transition: all 0.15s; }
.chip:hover { background: #e2e8f0; }
.chip.selected { background: #9a3412; color: white; }
.onco-ecog { padding: 10px; border: 2px solid #fed7aa; border-radius: 10px; margin-bottom: 8px; cursor: pointer; font-size: 13px; }
.onco-ecog.selected { border-color: #9a3412; background: #ffedd5; }
</style>

<div x-data="oncologyEMR()" class="onco-section">
    <div class="onco-card">
        <div class="onco-header" @click="sections.cancer = !sections.cancer">
            <span style="font-size:18px;">🎗️</span><h3>Cancer Details</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.cancer ? '▼' : '▶'"></span>
        </div>
        <div class="onco-body" x-show="sections.cancer" x-collapse>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
                <div><div class="field-label">Primary site</div><input type="text" class="field-input" x-model="formData.cancer.site" @change="updateField()"></div>
                <div><div class="field-label">Histology</div><input type="text" class="field-input" x-model="formData.cancer.histology" @change="updateField()"></div>
                <div><div class="field-label">T</div><select class="field-select" x-model="formData.cancer.t" @change="updateField()"><option value="">—</option><option>T1</option><option>T2</option><option>T3</option><option>T4</option></select></div>
                <div><div class="field-label">N</div><select class="field-select" x-model="formData.cancer.n" @change="updateField()"><option value="">—</option><option>N0</option><option>N1</option><option>N2</option><option>N3</option></select></div>
                <div><div class="field-label">M</div><select class="field-select" x-model="formData.cancer.m" @change="updateField()"><option value="">—</option><option>M0</option><option>M1</option></select></div>
                <div><div class="field-label">Grade</div><select class="field-select" x-model="formData.cancer.grade" @change="updateField()"><option value="">—</option><option>G1</option><option>G2</option><option>G3</option><option>G4</option></select></div>
            </div>
        </div>
    </div>
    <div class="onco-card">
        <div class="onco-header" @click="sections.ecog = !sections.ecog">
            <span style="font-size:18px;">📊</span><h3>Performance Status (ECOG)</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.ecog ? '▼' : '▶'"></span>
        </div>
        <div class="onco-body" x-show="sections.ecog" x-collapse>
            <template x-for="e in ecogOpts" :key="e.grade">
                <div class="onco-ecog" :class="{'selected': formData.ecog === e.grade}" @click="formData.ecog = e.grade; updateField();">
                    <strong x-text="'ECOG ' + e.grade"></strong> — <span x-text="e.txt"></span>
                </div>
            </template>
        </div>
    </div>
    <div class="onco-card">
        <div class="onco-header" @click="sections.chemo = !sections.chemo">
            <span style="font-size:18px;">💊</span><h3>Chemo Protocol</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.chemo ? '▼' : '▶'"></span>
        </div>
        <div class="onco-body" x-show="sections.chemo" x-collapse>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
                <div style="grid-column:1/-1;"><div class="field-label">Regimen</div><input type="text" class="field-input" x-model="formData.chemo.regimen" @change="updateField()"></div>
                <div><div class="field-label">Cycle #</div><input type="number" class="field-input" x-model="formData.chemo.cycle" @change="updateField()"></div>
                <div><div class="field-label">Day</div><input type="number" class="field-input" x-model="formData.chemo.day" @change="updateField()"></div>
                <div style="grid-column:1/-1;">
                    <div class="field-label">Dose modification</div>
                    <select class="field-select" x-model="formData.chemo.dose_mod" @change="updateField()"><option value="">None</option><option>25% reduction</option><option>50% reduction</option><option>Held</option><option>Discontinued</option></select>
                </div>
            </div>
        </div>
    </div>
    <div class="onco-card">
        <div class="onco-header" @click="sections.tox = !sections.tox">
            <span style="font-size:18px;">⚡</span><h3>Toxicity (CTCAE)</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.tox ? '▼' : '▶'"></span>
        </div>
        <div class="onco-body" x-show="sections.tox" x-collapse>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
                <template x-for="t in toxFields" :key="t.key">
                    <div>
                        <div class="field-label" x-text="t.label"></div>
                        <select class="field-select" x-model="formData.tox[t.key]" @change="updateField()">
                            <option>0</option><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option>
                        </select>
                    </div>
                </template>
            </div>
        </div>
    </div>
    <div class="onco-card">
        <div class="onco-header" @click="sections.dx = !sections.dx">
            <span style="font-size:18px;">🏥</span><h3>Diagnoses</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.dx ? '▼' : '▶'"></span>
        </div>
        <div class="onco-body" x-show="sections.dx" x-collapse>
            <div style="display:flex;flex-wrap:wrap;gap:4px;">
                <template x-for="dx in commonDiagnoses" :key="dx.code">
                    <button type="button" class="chip" :class="{'selected': selectedDiagnoses.includes(dx.code)}" @click="toggleDiagnosis(dx)">
                        <span x-text="dx.name"></span><span style="font-size:10px;opacity:0.7;" x-text="dx.code"></span>
                    </button>
                </template>
            </div>
            <input type="hidden" name="oncology_diagnoses" :value="JSON.stringify(selectedDiagnoses)">
        </div>
    </div>
    <input type="hidden" name="oncology_data" :value="JSON.stringify(formData)">
</div>

@php
    $oncologyFormDataDefault = [
        'cancer' => ['site' => '', 'histology' => '', 't' => '', 'n' => '', 'm' => '', 'grade' => ''],
        'ecog' => '',
        'chemo' => ['regimen' => '', 'cycle' => '', 'day' => '', 'dose_mod' => ''],
        'tox' => ['nausea' => '0', 'neuro' => '0', 'neutro' => '0', 'muco' => '0'],
    ];
@endphp

<script>
console.log('Oncology EMR template loaded');
function oncologyEMR() {
    return {
        sections: { cancer: true, ecog: true, chemo: true, tox: true, dx: true },
        ecogOpts: [
            { grade: '0', txt: 'Fully active' }, { grade: '1', txt: 'Restricted strenuous activity' },
            { grade: '2', txt: 'Ambulatory, capable of self-care' }, { grade: '3', txt: 'Limited self-care' },
            { grade: '4', txt: 'Completely disabled' },
        ],
        toxFields: [
            { key: 'nausea', label: 'Nausea' }, { key: 'neuro', label: 'Neuropathy' },
            { key: 'neutro', label: 'Neutropenia' }, { key: 'muco', label: 'Mucositis' },
        ],
        formData: @json(($visit ?? null)?->getStructuredField('oncology_data') ?? $oncologyFormDataDefault),
        commonDiagnoses: [
            { code: 'C50', name: 'Breast' }, { code: 'C34', name: 'Lung' }, { code: 'C18', name: 'Colon' },
            { code: 'C61', name: 'Prostate' }, { code: 'C56', name: 'Ovarian' }, { code: 'C73', name: 'Thyroid' },
            { code: 'C16', name: 'Stomach' }, { code: 'C25', name: 'Pancreas' }, { code: 'C54', name: 'Endometrial' },
        ],
        selectedDiagnoses: @json(($visit ?? null)?->getStructuredField('oncology_diagnoses') ?? []),
        init() { console.log('Oncology EMR initialized', this.formData); },
        updateField() {
            console.log('Oncology data updated', JSON.stringify(this.formData).substring(0, 200));
            if (window.triggerAutoSave) window.triggerAutoSave();
        },
        toggleDiagnosis(dx) {
            const idx = this.selectedDiagnoses.indexOf(dx.code);
            if (idx > -1) this.selectedDiagnoses.splice(idx, 1); else this.selectedDiagnoses.push(dx.code);
            console.log('Onco diagnosis toggled', dx.code);
            if (window.triggerAutoSave) window.triggerAutoSave();
        },
    };
}
</script>
