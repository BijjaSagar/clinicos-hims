{{-- Rheumatology EMR Template --}}
<style>
.rheum-card { background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; margin-bottom: 16px; }
.rheum-header { padding: 12px 16px; background: linear-gradient(135deg, #fef3c7, #fffbeb); border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; gap: 8px; cursor: pointer; }
.rheum-header h3 { font-size: 14px; font-weight: 600; color: #92400e; margin: 0; }
.rheum-body { padding: 16px; }
.field-label { font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
.field-input { width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; }
.field-input:focus { outline: none; border-color: #92400e; box-shadow: 0 0 0 3px rgba(146,64,14,0.12); }
.field-select { width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: white; }
.chip { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #f1f5f9; border-radius: 20px; font-size: 12px; margin: 4px; cursor: pointer; transition: all 0.15s; }
.chip:hover { background: #e2e8f0; }
.chip.selected { background: #92400e; color: white; }
.rheum-das { font-size: 26px; font-weight: 800; text-align: center; padding: 12px; background: #fef3c7; color: #92400e; border-radius: 10px; }
</style>

<div x-data="rheumatologyEMR()" class="rheum-section">
    <div class="rheum-card">
        <div class="rheum-header" @click="sections.joint_sym = !sections.joint_sym">
            <span style="font-size:18px;">🦴</span><h3>Joint Symptoms</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.joint_sym ? '▼' : '▶'"></span>
        </div>
        <div class="rheum-body" x-show="sections.joint_sym" x-collapse>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
                <div><div class="field-label">Morning stiffness (min)</div><input type="number" class="field-input" x-model="formData.joint_sym.stiff_min" @change="updateField()"></div>
                <div>
                    <div class="field-label">Pattern</div>
                    <select class="field-select" x-model="formData.joint_sym.pattern" @change="updateField()"><option value="">Select</option><option>Symmetric</option><option>Asymmetric</option></select>
                </div>
            </div>
        </div>
    </div>
    <div class="rheum-card">
        <div class="rheum-header" @click="sections.count = !sections.count">
            <span style="font-size:18px;">🔢</span><h3>Joint Count (DAS28)</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.count ? '▼' : '▶'"></span>
        </div>
        <div class="rheum-body" x-show="sections.count" x-collapse>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
                <div><div class="field-label">Tender (0–28)</div><input type="number" min="0" max="28" class="field-input" x-model="formData.count.tender" @input="updateField()"></div>
                <div><div class="field-label">Swollen (0–28)</div><input type="number" min="0" max="28" class="field-input" x-model="formData.count.swollen" @input="updateField()"></div>
                <div><div class="field-label">Patient global VAS (0–100)</div><input type="number" min="0" max="100" class="field-input" x-model="formData.count.vas" @input="updateField()"></div>
            </div>
        </div>
    </div>
    <div class="rheum-card">
        <div class="rheum-header" @click="sections.lab = !sections.lab">
            <span style="font-size:18px;">🧪</span><h3>Lab Review</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.lab ? '▼' : '▶'"></span>
        </div>
        <div class="rheum-body" x-show="sections.lab" x-collapse>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">
                <div><div class="field-label">ESR</div><input type="number" class="field-input" x-model="formData.lab.esr" @input="updateField()"></div>
                <div><div class="field-label">CRP (mg/L)</div><input type="number" step="0.1" class="field-input" x-model="formData.lab.crp" @change="updateField()"></div>
                <div><div class="field-label">RF</div><input type="text" class="field-input" x-model="formData.lab.rf" @change="updateField()"></div>
                <div><div class="field-label">Anti-CCP</div><input type="text" class="field-input" x-model="formData.lab.ccp" @change="updateField()"></div>
                <div style="grid-column:1/-1;"><div class="field-label">ANA</div><input type="text" class="field-input" x-model="formData.lab.ana" @change="updateField()"></div>
            </div>
        </div>
    </div>
    <div class="rheum-card">
        <div class="rheum-header" @click="sections.das = !sections.das">
            <span style="font-size:18px;">📐</span><h3>DAS28 (ESR)</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.das ? '▼' : '▶'"></span>
        </div>
        <div class="rheum-body" x-show="sections.das" x-collapse>
            <div class="rheum-das">DAS28-ESR: <span x-text="das28Display"></span></div>
            <p style="font-size:11px;color:#64748b;margin-top:8px;">Uses tender, swollen, ESR, VAS from sections above.</p>
        </div>
    </div>
    <div class="rheum-card">
        <div class="rheum-header" @click="sections.dx = !sections.dx">
            <span style="font-size:18px;">🏥</span><h3>Diagnoses</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.dx ? '▼' : '▶'"></span>
        </div>
        <div class="rheum-body" x-show="sections.dx" x-collapse>
            <div style="display:flex;flex-wrap:wrap;gap:4px;">
                <template x-for="dx in commonDiagnoses" :key="dx.code">
                    <button type="button" class="chip" :class="{'selected': selectedDiagnoses.includes(dx.code)}" @click="toggleDiagnosis(dx)">
                        <span x-text="dx.name"></span><span style="font-size:10px;opacity:0.7;" x-text="dx.code"></span>
                    </button>
                </template>
            </div>
            <input type="hidden" name="rheumatology_diagnoses" :value="JSON.stringify(selectedDiagnoses)">
        </div>
    </div>
    <input type="hidden" name="rheumatology_data" :value="JSON.stringify(formData)">
</div>

@php
    $rheumatologyFormDataDefault = [
        'joint_sym' => ['stiff_min' => '', 'pattern' => ''],
        'count' => ['tender' => '', 'swollen' => '', 'vas' => ''],
        'lab' => ['esr' => '', 'crp' => '', 'rf' => '', 'ccp' => '', 'ana' => ''],
    ];
@endphp

<script>
console.log('Rheumatology EMR template loaded');
function rheumatologyEMR() {
    return {
        sections: { joint_sym: true, count: true, lab: true, das: true, dx: true },
        formData: @json(($visit ?? null)?->getStructuredField('rheumatology_data') ?? $rheumatologyFormDataDefault),
        commonDiagnoses: [
            { code: 'M05', name: 'RA' }, { code: 'M32', name: 'SLE' }, { code: 'M45', name: 'AS' },
            { code: 'M10', name: 'Gout' }, { code: 'M35.0', name: "Sjogren's" }, { code: 'L40.5', name: 'PsA' },
            { code: 'M06.9', name: 'Inflammatory polyarth' }, { code: 'M15', name: 'Polyosteoarthritis' },
        ],
        selectedDiagnoses: @json(($visit ?? null)?->getStructuredField('rheumatology_diagnoses') ?? []),
        get das28Display() {
            const t28 = parseFloat(this.formData.count.tender), s28 = parseFloat(this.formData.count.swollen);
            const esr = parseFloat(this.formData.lab.esr), vas = parseFloat(this.formData.count.vas);
            if (isNaN(t28) || isNaN(s28) || isNaN(esr) || isNaN(vas)) return '--';
            const esrAdj = Math.max(esr, 1);
            const das = 0.56 * Math.sqrt(t28) + 0.28 * Math.sqrt(s28) + 0.7 * Math.log(esrAdj) + 0.014 * vas;
            const v = Math.round(das * 100) / 100;
            console.log('[Rheum] DAS28-ESR', v);
            return String(v);
        },
        init() { console.log('Rheumatology EMR initialized', this.formData); },
        updateField() {
            console.log('Rheumatology data updated', JSON.stringify(this.formData).substring(0, 220));
            if (window.triggerAutoSave) window.triggerAutoSave();
        },
        toggleDiagnosis(dx) {
            const idx = this.selectedDiagnoses.indexOf(dx.code);
            if (idx > -1) this.selectedDiagnoses.splice(idx, 1); else this.selectedDiagnoses.push(dx.code);
            console.log('Rheum diagnosis toggled', dx.code);
            if (window.triggerAutoSave) window.triggerAutoSave();
        },
    };
}
</script>
