{{-- Gastroenterology EMR Template --}}
<style>
.gi-card { background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; margin-bottom: 16px; }
.gi-header { padding: 12px 16px; background: linear-gradient(135deg, #fef3c7, #fffbeb); border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; gap: 8px; cursor: pointer; }
.gi-header h3 { font-size: 14px; font-weight: 600; color: #92400e; margin: 0; }
.gi-body { padding: 16px; }
.field-label { font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
.field-input { width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; }
.field-input:focus { outline: none; border-color: #92400e; box-shadow: 0 0 0 3px rgba(146,64,14,0.12); }
.field-select { width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: white; }
.chip { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #f1f5f9; border-radius: 20px; font-size: 12px; margin: 4px; cursor: pointer; transition: all 0.15s; }
.chip:hover { background: #e2e8f0; }
.chip.selected { background: #92400e; color: white; }
.gi-cp-score { font-size: 28px; font-weight: 700; text-align: center; padding: 12px; border-radius: 10px; background: #fef3c7; color: #92400e; }
</style>

<div x-data="gastroenterologyEMR()" class="gi-section">
    <div class="gi-card">
        <div class="gi-header" @click="sections.symptoms = !sections.symptoms">
            <span style="font-size:18px;">🫄</span><h3>GI Symptoms</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.symptoms ? '▼' : '▶'"></span>
        </div>
        <div class="gi-body" x-show="sections.symptoms" x-collapse>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
                <div>
                    <div class="field-label">Pain location (9 quadrants)</div>
                    <select class="field-select" x-model="formData.symptoms.pain_quadrant" @change="updateField()">
                        <option value="">Select</option>
                        <option>RUQ</option><option>Epigastric</option><option>LUQ</option>
                        <option>RLQ</option><option>Periumbilical</option><option>LLQ</option>
                        <option>Suprapubic</option><option>Diffuse</option><option>Flank (L/R)</option>
                    </select>
                </div>
                <div>
                    <div class="field-label">Bowel habits</div>
                    <select class="field-select" x-model="formData.symptoms.bowel" @change="updateField()">
                        <option value="">Select</option><option>Normal</option><option>Constipation</option><option>Diarrhea</option><option>Alternating</option><option>Blood in stool</option>
                    </select>
                </div>
                <div><label style="display:flex;align-items:center;gap:8px;"><input type="checkbox" x-model="formData.symptoms.nausea" @change="updateField()"> Nausea</label></div>
                <div><label style="display:flex;align-items:center;gap:8px;"><input type="checkbox" x-model="formData.symptoms.vomiting" @change="updateField()"> Vomiting</label></div>
            </div>
        </div>
    </div>
    <div class="gi-card">
        <div class="gi-header" @click="sections.endo = !sections.endo">
            <span style="font-size:18px;">🔬</span><h3>Endoscopy Findings</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.endo ? '▼' : '▶'"></span>
        </div>
        <div class="gi-body" x-show="sections.endo" x-collapse>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
                <div>
                    <div class="field-label">Type</div>
                    <select class="field-select" x-model="formData.endo.type" @change="updateField()">
                        <option value="">Select</option><option>OGD</option><option>Colonoscopy</option><option>Sigmoidoscopy</option><option>ERCP</option><option>EUS</option>
                    </select>
                </div>
                <div><div class="field-label">Site</div><input type="text" class="field-input" x-model="formData.endo.site" @change="updateField()"></div>
                <div style="grid-column:1/-1;"><div class="field-label">Findings</div><textarea class="field-input" style="min-height:70px;" x-model="formData.endo.findings" @change="updateField()"></textarea></div>
                <div>
                    <div class="field-label">H. pylori status</div>
                    <select class="field-select" x-model="formData.endo.h_pylori" @change="updateField()">
                        <option value="">Unknown</option><option>Positive</option><option>Negative</option><option>Not tested</option>
                    </select>
                </div>
                <div><label style="display:flex;align-items:center;gap:8px;"><input type="checkbox" x-model="formData.endo.biopsy" @change="updateField()"> Biopsy taken</label></div>
            </div>
        </div>
    </div>
    <div class="gi-card">
        <div class="gi-header" @click="sections.liver = !sections.liver">
            <span style="font-size:18px;">🧪</span><h3>Liver Assessment (Child-Pugh)</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.liver ? '▼' : '▶'"></span>
        </div>
        <div class="gi-body" x-show="sections.liver" x-collapse>
            <div class="gi-cp-score">Score: <span x-text="childPughScoreDisplay"></span> <span x-show="childPughClass" x-text="'(' + childPughClass + ')'"></span></div>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-top:12px;">
                <div><div class="field-label">Bilirubin (mg/dL)</div><input type="number" step="0.1" class="field-input" x-model="formData.liver.bili" @change="updateField()"></div>
                <div><div class="field-label">Albumin (g/dL)</div><input type="number" step="0.1" class="field-input" x-model="formData.liver.alb" @change="updateField()"></div>
                <div><div class="field-label">INR</div><input type="number" step="0.01" class="field-input" x-model="formData.liver.inr" @change="updateField()"></div>
                <div>
                    <div class="field-label">Ascites</div>
                    <select class="field-select" x-model="formData.liver.ascites" @change="updateField()">
                        <option value="none">None (1)</option><option value="mild">Mild / diuretic (2)</option><option value="mod">Moderate (3)</option>
                    </select>
                </div>
                <div style="grid-column:1/-1;">
                    <div class="field-label">Encephalopathy</div>
                    <select class="field-select" x-model="formData.liver.enceph" @change="updateField()">
                        <option value="none">None (1)</option><option value="grade12">Grade 1-2 (2)</option><option value="grade34">Grade 3-4 (3)</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="gi-card">
        <div class="gi-header" @click="sections.dx = !sections.dx">
            <span style="font-size:18px;">🏥</span><h3>Diagnoses</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.dx ? '▼' : '▶'"></span>
        </div>
        <div class="gi-body" x-show="sections.dx" x-collapse>
            <div style="display:flex;flex-wrap:wrap;gap:4px;">
                <template x-for="dx in commonDiagnoses" :key="dx.code">
                    <button type="button" class="chip" :class="{'selected': selectedDiagnoses.includes(dx.code)}" @click="toggleDiagnosis(dx)">
                        <span x-text="dx.name"></span><span style="font-size:10px;opacity:0.7;" x-text="dx.code"></span>
                    </button>
                </template>
            </div>
            <input type="hidden" name="gastroenterology_diagnoses" :value="JSON.stringify(selectedDiagnoses)">
        </div>
    </div>
    <input type="hidden" name="gastroenterology_data" :value="JSON.stringify(formData)">
</div>

@php
    $gastroenterologyFormDataDefault = [
        'symptoms' => ['pain_quadrant' => '', 'bowel' => '', 'nausea' => false, 'vomiting' => false],
        'endo' => ['type' => '', 'site' => '', 'findings' => '', 'h_pylori' => '', 'biopsy' => false],
        'liver' => ['bili' => '', 'alb' => '', 'inr' => '', 'ascites' => 'none', 'enceph' => 'none'],
    ];
@endphp

<script>
console.log('Gastroenterology EMR template loaded');
function gastroenterologyEMR() {
    return {
        sections: { symptoms: true, endo: true, liver: true, dx: true },
        formData: @json(($visit ?? null)?->getStructuredField('gastroenterology_data') ?? $gastroenterologyFormDataDefault),
        commonDiagnoses: [
            { code: 'K21.0', name: 'GERD' }, { code: 'K25', name: 'Gastric ulcer' }, { code: 'K29', name: 'Gastritis' },
            { code: 'K50', name: "Crohn's" }, { code: 'K51', name: 'UC' }, { code: 'K74', name: 'Cirrhosis' },
            { code: 'K80', name: 'Gallstones' }, { code: 'K85', name: 'Pancreatitis' }, { code: 'K58', name: 'IBS' }, { code: 'K57', name: 'Diverticular' },
        ],
        selectedDiagnoses: @json(($visit ?? null)?->getStructuredField('gastroenterology_diagnoses') ?? []),
        get childPughScoreDisplay() {
            const b = parseFloat(this.formData.liver.bili), a = parseFloat(this.formData.liver.alb), i = parseFloat(this.formData.liver.inr);
            if (isNaN(b) || isNaN(a) || isNaN(i)) return '—';
            let s = 0;
            s += b < 2 ? 1 : (b <= 3 ? 2 : 3);
            s += a > 3.5 ? 1 : (a >= 2.8 ? 2 : 3);
            s += i < 1.7 ? 1 : (i <= 2.3 ? 2 : 3);
            s += { none: 1, mild: 2, mod: 3 }[this.formData.liver.ascites] || 1;
            s += { none: 1, grade12: 2, grade34: 3 }[this.formData.liver.enceph] || 1;
            console.log('[GI] Child-Pugh score', s);
            return String(s);
        },
        get childPughClass() {
            const b = parseFloat(this.formData.liver.bili), a = parseFloat(this.formData.liver.alb), i = parseFloat(this.formData.liver.inr);
            if (isNaN(b) || isNaN(a) || isNaN(i)) return '';
            const sc = parseInt(this.childPughScoreDisplay, 10);
            if (isNaN(sc)) return '';
            if (sc <= 6) return 'A';
            if (sc <= 9) return 'B';
            return 'C';
        },
        init() { console.log('Gastroenterology EMR initialized', this.formData); },
        updateField() {
            console.log('Gastroenterology data updated', JSON.stringify(this.formData).substring(0, 220));
            if (window.triggerAutoSave) window.triggerAutoSave();
        },
        toggleDiagnosis(dx) {
            const idx = this.selectedDiagnoses.indexOf(dx.code);
            if (idx > -1) this.selectedDiagnoses.splice(idx, 1); else this.selectedDiagnoses.push(dx.code);
            console.log('GI diagnosis toggled', dx.code);
            if (window.triggerAutoSave) window.triggerAutoSave();
        },
    };
}
</script>
