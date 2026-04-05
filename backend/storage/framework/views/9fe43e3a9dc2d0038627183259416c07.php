
<style>
.psych-card { background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; margin-bottom: 16px; }
.psych-header { padding: 12px 16px; background: linear-gradient(135deg, #fce7f3, #fdf2f8); border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; gap: 8px; cursor: pointer; }
.psych-header h3 { font-size: 14px; font-weight: 600; color: #9d174d; margin: 0; }
.psych-body { padding: 16px; }
.field-label { font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
.field-input { width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; }
.field-input:focus { outline: none; border-color: #9d174d; box-shadow: 0 0 0 3px rgba(157,23,77,0.12); }
.field-select { width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: white; }
.chip { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #f1f5f9; border-radius: 20px; font-size: 12px; margin: 4px; cursor: pointer; transition: all 0.15s; }
.chip:hover { background: #e2e8f0; }
.chip.selected { background: #9d174d; color: white; }
.psych-total { font-size: 22px; font-weight: 700; color: #9d174d; text-align: center; padding: 10px; background: #fce7f3; border-radius: 10px; margin-top: 8px; }
</style>

<div x-data="psychiatryEMR()" class="psych-section">
    <div class="psych-card">
        <div class="psych-header" @click="sections.pc = !sections.pc">
            <span style="font-size:18px;">💬</span><h3>Presenting Complaints</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.pc ? '▼' : '▶'"></span>
        </div>
        <div class="psych-body" x-show="sections.pc" x-collapse>
            <textarea class="field-input" style="min-height:80px;" x-model="formData.pc.text" @change="updateField()" placeholder="Chief complaints..."></textarea>
        </div>
    </div>
    <div class="psych-card">
        <div class="psych-header" @click="sections.sub = !sections.sub">
            <span style="font-size:18px;">⚠️</span><h3>Substance Use</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.sub ? '▼' : '▶'"></span>
        </div>
        <div class="psych-body" x-show="sections.sub" x-collapse>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
                <div><div class="field-label">Type</div><select class="field-select" x-model="formData.sub.type" @change="updateField()"><option value="">None</option><option>Alcohol</option><option>Tobacco</option><option>Cannabis</option><option>Opioids</option><option>Other</option></select></div>
                <div><div class="field-label">Quantity</div><input type="text" class="field-input" x-model="formData.sub.qty" @change="updateField()"></div>
                <div><div class="field-label">Years</div><input type="number" class="field-input" x-model="formData.sub.years" @change="updateField()"></div>
            </div>
            <div class="field-label" style="margin-top:12px;">CAGE (Yes=1 each)</div>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:8px;">
                <template x-for="(q,i) in cageQs" :key="i">
                    <label style="font-size:12px;display:flex;align-items:center;gap:8px;">
                        <input type="checkbox" :checked="formData.sub.cage[i]==='1'" @change="formData.sub.cage[i]=$event.target.checked?'1':'0';updateField()"><span x-text="q"></span>
                    </label>
                </template>
            </div>
            <div class="psych-total">CAGE score: <span x-text="cageScore"></span> / 4</div>
        </div>
    </div>
    <div class="psych-card">
        <div class="psych-header" @click="sections.mse = !sections.mse">
            <span style="font-size:18px;">🧩</span><h3>MSE</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.mse ? '▼' : '▶'"></span>
        </div>
        <div class="psych-body" x-show="sections.mse" x-collapse>
            <template x-for="f in mseFields" :key="f.key">
                <div style="margin-bottom:10px;"><div class="field-label" x-text="f.label"></div><textarea class="field-input" style="min-height:44px;" x-model="formData.mse[f.key]" @change="updateField()"></textarea></div>
            </template>
            <div class="field-label">Insight (1–6)</div>
            <input type="range" min="1" max="6" x-model="formData.mse.insight" @input="updateField()" style="width:100%;"><span x-text="formData.mse.insight"></span>
        </div>
    </div>
    <div class="psych-card">
        <div class="psych-header" @click="sections.risk = !sections.risk">
            <span style="font-size:18px;">🛡️</span><h3>Risk Assessment</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.risk ? '▼' : '▶'"></span>
        </div>
        <div class="psych-body" x-show="sections.risk" x-collapse>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">
                <div><div class="field-label">Suicide</div><select class="field-select" x-model="formData.risk.suicide" @change="updateField()"><option value="">—</option><option>low</option><option>medium</option><option>high</option></select></div>
                <div><div class="field-label">Self-harm</div><select class="field-select" x-model="formData.risk.selfharm" @change="updateField()"><option value="">—</option><option>low</option><option>medium</option><option>high</option></select></div>
                <div><div class="field-label">Violence</div><select class="field-select" x-model="formData.risk.violence" @change="updateField()"><option value="">—</option><option>low</option><option>medium</option><option>high</option></select></div>
            </div>
        </div>
    </div>
    <div class="psych-card">
        <div class="psych-header" @click="sections.phq = !sections.phq">
            <span style="font-size:18px;">📋</span><h3>PHQ-9</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.phq ? '▼' : '▶'"></span>
        </div>
        <div class="psych-body" x-show="sections.phq" x-collapse>
            <template x-for="(label, idx) in phqLabels" :key="idx">
                <div style="margin-bottom:8px;display:flex;align-items:center;gap:10px;">
                    <span style="flex:1;font-size:12px;" x-text="(idx+1)+'. '+label"></span>
                    <select class="field-select" style="max-width:80px;" x-model="formData.phq[idx]" @change="updateField()"><option>0</option><option>1</option><option>2</option><option>3</option></select>
                </div>
            </template>
            <div class="psych-total">PHQ-9 Total: <span x-text="phqTotal"></span> / 27</div>
        </div>
    </div>
    <div class="psych-card">
        <div class="psych-header" @click="sections.dx = !sections.dx">
            <span style="font-size:18px;">🏥</span><h3>Diagnoses</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.dx ? '▼' : '▶'"></span>
        </div>
        <div class="psych-body" x-show="sections.dx" x-collapse>
            <div style="display:flex;flex-wrap:wrap;gap:4px;">
                <template x-for="dx in commonDiagnoses" :key="dx.code">
                    <button type="button" class="chip" :class="{'selected': selectedDiagnoses.includes(dx.code)}" @click="toggleDiagnosis(dx)">
                        <span x-text="dx.name"></span><span style="font-size:10px;opacity:0.7;" x-text="dx.code"></span>
                    </button>
                </template>
            </div>
            <input type="hidden" name="psychiatry_diagnoses" :value="JSON.stringify(selectedDiagnoses)">
        </div>
    </div>
    <input type="hidden" name="psychiatry_data" :value="JSON.stringify(formData)">
</div>

<?php
    $psychiatryFormDataDefault = [
        'pc' => ['text' => ''],
        'sub' => ['type' => '', 'qty' => '', 'years' => '', 'cage' => ['0', '0', '0', '0']],
        'mse' => ['appearance' => '', 'behavior' => '', 'speech' => '', 'mood' => '', 'affect' => '', 'thought' => '', 'perception' => '', 'cognition' => '', 'insight' => '3'],
        'risk' => ['suicide' => '', 'selfharm' => '', 'violence' => ''],
        'phq' => ['0', '0', '0', '0', '0', '0', '0', '0', '0'],
    ];
?>

<script>
console.log('Psychiatry EMR template loaded');
function psychiatryEMR() {
    return {
        sections: { pc: true, sub: true, mse: true, risk: true, phq: true, dx: true },
        cageQs: ['Cut down?', 'Annoyed?', 'Guilty?', 'Eye-opener?'],
        mseFields: [
            { key: 'appearance', label: 'Appearance' }, { key: 'behavior', label: 'Behavior' }, { key: 'speech', label: 'Speech' },
            { key: 'mood', label: 'Mood' }, { key: 'affect', label: 'Affect' }, { key: 'thought', label: 'Thought' },
            { key: 'perception', label: 'Perception' }, { key: 'cognition', label: 'Cognition' },
        ],
        phqLabels: ['Little interest', 'Down/depressed', 'Sleep', 'Tired', 'Appetite', 'Failure feeling', 'Concentration', 'Psychomotor', 'Suicidal thoughts'],
        formData: <?php echo json_encode(($visit ?? null)?->getStructuredField('psychiatry_data') ?? $psychiatryFormDataDefault, 15, 512) ?>,
        commonDiagnoses: [
            { code: 'F32', name: 'Depression' }, { code: 'F41.1', name: 'GAD' }, { code: 'F20', name: 'Schizophrenia' },
            { code: 'F31', name: 'Bipolar' }, { code: 'F42', name: 'OCD' }, { code: 'F43.1', name: 'PTSD' },
            { code: 'F06.3', name: 'Organic mood' }, { code: 'F43.0', name: 'Acute stress' }, { code: 'F90.0', name: 'ADHD' },
        ],
        selectedDiagnoses: <?php echo json_encode(($visit ?? null)?->getStructuredField('psychiatry_diagnoses') ?? [], 15, 512) ?>,
        get cageScore() {
            const c = this.formData.sub.cage;
            if (!c) return 0;
            const arr = Array.isArray(c) ? c : Object.values(c);
            return arr.filter(x => x === '1' || x === 1 || x === true).length;
        },
        get phqTotal() {
            let t = 0;
            (this.formData.phq || []).forEach(x => { t += parseInt(x, 10) || 0; });
            console.log('[Psych] PHQ-9 total', t);
            return t;
        },
        init() { console.log('Psychiatry EMR initialized', this.formData); },
        updateField() {
            console.log('Psychiatry data updated', JSON.stringify(this.formData).substring(0, 200));
            if (window.triggerAutoSave) window.triggerAutoSave();
        },
        toggleDiagnosis(dx) {
            const idx = this.selectedDiagnoses.indexOf(dx.code);
            if (idx > -1) this.selectedDiagnoses.splice(idx, 1); else this.selectedDiagnoses.push(dx.code);
            console.log('Psych diagnosis toggled', dx.code);
            if (window.triggerAutoSave) window.triggerAutoSave();
        },
    };
}
</script>
<?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/emr/specialty/psychiatry.blade.php ENDPATH**/ ?>