
<style>
.endo-card { background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; margin-bottom: 16px; }
.endo-header { padding: 12px 16px; background: linear-gradient(135deg, #f3e8ff, #faf5ff); border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; gap: 8px; cursor: pointer; }
.endo-header h3 { font-size: 14px; font-weight: 600; color: #581c87; margin: 0; }
.endo-body { padding: 16px; }
.field-label { font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
.field-input { width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; }
.field-input:focus { outline: none; border-color: #581c87; box-shadow: 0 0 0 3px rgba(88,28,135,0.12); }
.field-select { width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: white; }
.chip { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #f1f5f9; border-radius: 20px; font-size: 12px; margin: 4px; cursor: pointer; transition: all 0.15s; }
.chip:hover { background: #e2e8f0; }
.chip.selected { background: #581c87; color: white; }
.endo-a1c { font-size: 32px; font-weight: 700; text-align: center; padding: 14px; border-radius: 12px; }
.endo-thy { font-size: 13px; padding: 8px; border-radius: 8px; background: #ede9fe; color: #581c87; margin-top: 8px; }
.endo-a1c-warn { background: #fef3c7; color: #92400e; }
.endo-a1c-high { background: #fee2e2; color: #b91c1c; }
</style>

<div x-data="endocrinologyEMR()" class="endo-section">
    <div class="endo-card">
        <div class="endo-header" @click="sections.thy_exam = !sections.thy_exam">
            <span style="font-size:18px;">🦋</span><h3>Thyroid Exam</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.thy_exam ? '▼' : '▶'"></span>
        </div>
        <div class="endo-body" x-show="sections.thy_exam" x-collapse>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
                <div><div class="field-label">Size</div><select class="field-select" x-model="formData.thy_exam.size" @change="updateField()"><option value="">Select</option><option>Normal</option><option>Diffuse goiter</option><option>Nodular</option></select></div>
                <div><div class="field-label">Nodules</div><select class="field-select" x-model="formData.thy_exam.nodules" @change="updateField()"><option value="">Select</option><option>None</option><option>Single</option><option>Multinodular</option></select></div>
                <div><div class="field-label">Consistency</div><select class="field-select" x-model="formData.thy_exam.consistency" @change="updateField()"><option value="">Select</option><option>Soft</option><option>Firm</option><option>Hard</option></select></div>
                <div><label style="display:flex;align-items:center;gap:8px;"><input type="checkbox" x-model="formData.thy_exam.bruit" @change="updateField()"> Thyroid bruit</label></div>
            </div>
        </div>
    </div>
    <div class="endo-card">
        <div class="endo-header" @click="sections.thy_lab = !sections.thy_lab">
            <span style="font-size:18px;">📈</span><h3>Thyroid Function</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.thy_lab ? '▼' : '▶'"></span>
        </div>
        <div class="endo-body" x-show="sections.thy_lab" x-collapse>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">
                <div><div class="field-label">TSH</div><input type="number" step="0.01" class="field-input" x-model="formData.thy_lab.tsh" @input="updateField()"></div>
                <div><div class="field-label">FT3</div><input type="number" step="0.01" class="field-input" x-model="formData.thy_lab.ft3" @input="updateField()"></div>
                <div><div class="field-label">FT4</div><input type="number" step="0.01" class="field-input" x-model="formData.thy_lab.ft4" @input="updateField()"></div>
            </div>
            <div class="endo-thy" x-text="thyroidInterpretation"></div>
        </div>
    </div>
    <div class="endo-card">
        <div class="endo-header" @click="sections.dm = !sections.dm">
            <span style="font-size:18px;">🩸</span><h3>HbA1c & Glucose</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.dm ? '▼' : '▶'"></span>
        </div>
        <div class="endo-body" x-show="sections.dm" x-collapse>
            <div class="endo-a1c" :class="a1cClass" x-text="formData.dm.a1c ? (formData.dm.a1c + '%') : '--'"></div>
            <input type="range" min="4" max="15" step="0.1" x-model="formData.dm.a1c" @input="updateField()" style="width:100%;margin-top:8px;">
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-top:12px;">
                <div><div class="field-label">FPG (mg/dL)</div><input type="number" class="field-input" x-model="formData.dm.fpg" @change="updateField()"></div>
                <div><div class="field-label">PPG (mg/dL)</div><input type="number" class="field-input" x-model="formData.dm.ppg" @change="updateField()"></div>
            </div>
        </div>
    </div>
    <div class="endo-card">
        <div class="endo-header" @click="sections.bone = !sections.bone">
            <span style="font-size:18px;">🦴</span><h3>Bone Health</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.bone ? '▼' : '▶'"></span>
        </div>
        <div class="endo-body" x-show="sections.bone" x-collapse>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">
                <div><div class="field-label">DEXA T-score</div><input type="number" step="0.1" class="field-input" x-model="formData.bone.tscore" @change="updateField()"></div>
                <div><div class="field-label">Vit D (ng/mL)</div><input type="number" step="0.1" class="field-input" x-model="formData.bone.vitd" @change="updateField()"></div>
                <div><div class="field-label">Calcium (mg/dL)</div><input type="number" step="0.1" class="field-input" x-model="formData.bone.ca" @change="updateField()"></div>
            </div>
        </div>
    </div>
    <div class="endo-card">
        <div class="endo-header" @click="sections.dx = !sections.dx">
            <span style="font-size:18px;">🏥</span><h3>Diagnoses</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.dx ? '▼' : '▶'"></span>
        </div>
        <div class="endo-body" x-show="sections.dx" x-collapse>
            <div style="display:flex;flex-wrap:wrap;gap:4px;">
                <template x-for="dx in commonDiagnoses" :key="dx.code">
                    <button type="button" class="chip" :class="{'selected': selectedDiagnoses.includes(dx.code)}" @click="toggleDiagnosis(dx)">
                        <span x-text="dx.name"></span><span style="font-size:10px;opacity:0.7;" x-text="dx.code"></span>
                    </button>
                </template>
            </div>
            <input type="hidden" name="endocrinology_diagnoses" :value="JSON.stringify(selectedDiagnoses)">
        </div>
    </div>
    <input type="hidden" name="endocrinology_data" :value="JSON.stringify(formData)">
</div>

<?php
    $endocrinologyFormDataDefault = [
        'thy_exam' => ['size' => '', 'nodules' => '', 'consistency' => '', 'bruit' => false],
        'thy_lab' => ['tsh' => '', 'ft3' => '', 'ft4' => ''],
        'dm' => ['a1c' => 5.7, 'fpg' => '', 'ppg' => ''],
        'bone' => ['tscore' => '', 'vitd' => '', 'ca' => ''],
    ];
?>

<script>
console.log('Endocrinology EMR template loaded');
function endocrinologyEMR() {
    return {
        sections: { thy_exam: true, thy_lab: true, dm: true, bone: true, dx: true },
        formData: <?php echo json_encode(($visit ?? null)?->getStructuredField('endocrinology_data') ?? $endocrinologyFormDataDefault, 15, 512) ?>,
        commonDiagnoses: [
            { code: 'E05', name: 'Hyperthyroid' }, { code: 'E03', name: 'Hypothyroid' }, { code: 'E11', name: 'DM2' },
            { code: 'E10', name: 'DM1' }, { code: 'E06.3', name: 'Hashimoto' }, { code: 'M81', name: 'Osteoporosis' }, { code: 'E22.0', name: 'Acromegaly' },
            { code: 'E23.0', name: 'Hypopituitarism' }, { code: 'E28.2', name: 'PCOS' },
        ],
        selectedDiagnoses: <?php echo json_encode(($visit ?? null)?->getStructuredField('endocrinology_diagnoses') ?? [], 15, 512) ?>,
        get thyroidInterpretation() {
            const t = parseFloat(this.formData.thy_lab.tsh);
            if (isNaN(t)) return 'Enter TSH (and optional FT3/FT4) for interpretation.';
            if (t < 0.1) return 'Pattern suggests hyperthyroidism (low TSH) — correlate clinically & with FT4.';
            if (t > 4.5) return 'Pattern suggests hypothyroidism (elevated TSH) — correlate with FT4.';
            return 'TSH in typical euthyroid range — correlate with symptoms and FT4.';
        },
        get a1cClass() {
            const v = parseFloat(this.formData.dm.a1c);
            if (isNaN(v)) return '';
            if (v < 5.7) return '';
            if (v < 6.5) return 'endo-a1c-warn';
            return 'endo-a1c-high';
        },
        init() { console.log('Endocrinology EMR initialized', this.formData); },
        updateField() {
            console.log('Endocrinology data updated', JSON.stringify(this.formData).substring(0, 200));
            if (window.triggerAutoSave) window.triggerAutoSave();
        },
        toggleDiagnosis(dx) {
            const idx = this.selectedDiagnoses.indexOf(dx.code);
            if (idx > -1) this.selectedDiagnoses.splice(idx, 1); else this.selectedDiagnoses.push(dx.code);
            console.log('Endo diagnosis toggled', dx.code);
            if (window.triggerAutoSave) window.triggerAutoSave();
        },
    };
}
</script>
<?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/emr/specialty/endocrinology.blade.php ENDPATH**/ ?>