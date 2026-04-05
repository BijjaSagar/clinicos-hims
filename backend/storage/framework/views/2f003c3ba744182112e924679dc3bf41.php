
<style>
.diab-card { background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; margin-bottom: 16px; }
.diab-header { padding: 12px 16px; background: linear-gradient(135deg, #ccfbf1, #f0fdfa); border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; gap: 8px; cursor: pointer; }
.diab-header h3 { font-size: 14px; font-weight: 600; color: #115e59; margin: 0; }
.diab-body { padding: 16px; }
.field-label { font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
.field-input { width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; }
.field-input:focus { outline: none; border-color: #115e59; box-shadow: 0 0 0 3px rgba(17,94,89,0.12); }
.field-select { width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: white; }
.chip { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #f1f5f9; border-radius: 20px; font-size: 12px; margin: 4px; cursor: pointer; transition: all 0.15s; }
.chip:hover { background: #e2e8f0; }
.chip.selected { background: #115e59; color: white; }
.diab-a1c-big { font-size: 40px; font-weight: 800; text-align: center; padding: 20px; border-radius: 14px; }
.diab-a1c-ok { background: #d1fae5; color: #065f46; }
.diab-a1c-warn { background: #fef3c7; color: #92400e; }
.diab-a1c-bad { background: #fee2e2; color: #b91c1c; }
</style>

<div x-data="diabetologyEMR()" class="diab-section">
    <div class="diab-card">
        <div class="diab-header" @click="sections.overview = !sections.overview">
            <span style="font-size:18px;">📋</span><h3>Diabetes Overview</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.overview ? '▼' : '▶'"></span>
        </div>
        <div class="diab-body" x-show="sections.overview" x-collapse>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
                <div>
                    <div class="field-label">Type</div>
                    <select class="field-select" x-model="formData.overview.type" @change="updateField()">
                        <option value="">Select</option><option>Type 1</option><option>Type 2</option><option>GDM</option><option>Secondary</option>
                    </select>
                </div>
                <div><div class="field-label">Duration (years)</div><input type="number" class="field-input" x-model="formData.overview.duration_y" @change="updateField()"></div>
                <div style="grid-column:1/-1;"><div class="field-label">Current regimen</div><textarea class="field-input" style="min-height:60px;" x-model="formData.overview.regimen" @change="updateField()"></textarea></div>
            </div>
        </div>
    </div>
    <div class="diab-card">
        <div class="diab-header" @click="sections.a1c = !sections.a1c">
            <span style="font-size:18px;">🎯</span><h3>HbA1c Monitor</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.a1c ? '▼' : '▶'"></span>
        </div>
        <div class="diab-body" x-show="sections.a1c" x-collapse>
            <div class="diab-a1c-big" :class="a1cBigClass"><span x-text="formData.monitor.a1c || '--'"></span>%</div>
            <input type="range" min="4" max="15" step="0.1" x-model="formData.monitor.a1c" @input="updateField()" style="width:100%;margin-top:10px;">
        </div>
    </div>
    <div class="diab-card">
        <div class="diab-header" @click="sections.comp = !sections.comp">
            <span style="font-size:18px;">🔍</span><h3>Complications Screening</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.comp ? '▼' : '▶'"></span>
        </div>
        <div class="diab-body" x-show="sections.comp" x-collapse>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
                <div><label class="field-label">Retinopathy</label><select class="field-select" x-model="formData.comp.retino" @change="updateField()"><option value="">None</option><option>Mild NPDR</option><option>Moderate NPDR</option><option>Severe NPDR</option><option>PDR</option></select></div>
                <div><label class="field-label">Nephropathy</label><select class="field-select" x-model="formData.comp.nephro" @change="updateField()"><option value="">None</option><option>Microalbuminuria</option><option>Macroalbuminuria</option><option>ESRD</option></select></div>
                <div><label class="field-label">Neuropathy</label><select class="field-select" x-model="formData.comp.neuro" @change="updateField()"><option value="">None</option><option>Peripheral</option><option>Autonomic</option><option>Both</option></select></div>
                <div><label class="field-label">Foot risk</label><select class="field-select" x-model="formData.comp.foot_grade" @change="updateField()"><option value="">Low</option><option>Moderate</option><option>High</option><option>Ulcer active</option></select></div>
            </div>
        </div>
    </div>
    <div class="diab-card">
        <div class="diab-header" @click="sections.foot = !sections.foot">
            <span style="font-size:18px;">🦶</span><h3>Foot Exam</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.foot ? '▼' : '▶'"></span>
        </div>
        <div class="diab-body" x-show="sections.foot" x-collapse>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
                <div><div class="field-label">Pulses</div><select class="field-select" x-model="formData.foot.pulses" @change="updateField()"><option value="">Select</option><option>Normal</option><option>Diminished</option><option>Absent</option></select></div>
                <div><label style="display:flex;align-items:center;gap:8px;"><input type="checkbox" x-model="formData.foot.deformity" @change="updateField()"> Deformity</label></div>
                <div>
                    <div class="field-label">Ulcer (Wagner)</div>
                    <select class="field-select" x-model="formData.foot.wagner" @change="updateField()">
                        <option>0</option><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option>
                    </select>
                </div>
                <div><label style="display:flex;align-items:center;gap:8px;"><input type="checkbox" x-model="formData.foot.monofilament" @change="updateField()"> Monofilament intact</label></div>
            </div>
        </div>
    </div>
    <div class="diab-card">
        <div class="diab-header" @click="sections.dx = !sections.dx">
            <span style="font-size:18px;">🏥</span><h3>Diagnoses</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.dx ? '▼' : '▶'"></span>
        </div>
        <div class="diab-body" x-show="sections.dx" x-collapse>
            <div style="display:flex;flex-wrap:wrap;gap:4px;">
                <template x-for="dx in commonDiagnoses" :key="dx.code">
                    <button type="button" class="chip" :class="{'selected': selectedDiagnoses.includes(dx.code)}" @click="toggleDiagnosis(dx)">
                        <span x-text="dx.name"></span><span style="font-size:10px;opacity:0.7;" x-text="dx.code"></span>
                    </button>
                </template>
            </div>
            <input type="hidden" name="diabetology_diagnoses" :value="JSON.stringify(selectedDiagnoses)">
        </div>
    </div>
    <input type="hidden" name="diabetology_data" :value="JSON.stringify(formData)">
</div>

<?php
    $diabetologyFormDataDefault = [
        'overview' => ['type' => '', 'duration_y' => '', 'regimen' => ''],
        'monitor' => ['a1c' => 6.5],
        'comp' => ['retino' => '', 'nephro' => '', 'neuro' => '', 'foot_grade' => ''],
        'foot' => ['pulses' => '', 'deformity' => false, 'wagner' => '0', 'monofilament' => false],
    ];
?>

<script>
console.log('Diabetology EMR template loaded');
function diabetologyEMR() {
    return {
        sections: { overview: true, a1c: true, comp: true, foot: true, dx: true },
        formData: <?php echo json_encode(($visit ?? null)?->getStructuredField('diabetology_data') ?? $diabetologyFormDataDefault, 15, 512) ?>,
        commonDiagnoses: [
            { code: 'E11.65', name: 'DM2 hyperglycemia' }, { code: 'E11.22', name: 'DM2 nephropathy' }, { code: 'E11.31', name: 'DM2 retinopathy' },
            { code: 'E11.40', name: 'DM2 neuropathy' }, { code: 'E10', name: 'DM1' }, { code: 'E11.9', name: 'DM2 uncomplicated' },
            { code: 'E11.21', name: 'DM2 nephropathy NOS' }, { code: 'E13.9', name: 'Other DM' }, { code: 'Z79.4', name: 'Long-term insulin' },
        ],
        selectedDiagnoses: <?php echo json_encode(($visit ?? null)?->getStructuredField('diabetology_diagnoses') ?? [], 15, 512) ?>,
        get a1cBigClass() {
            const v = parseFloat(this.formData.monitor.a1c);
            if (isNaN(v)) return '';
            if (v < 7) return 'diab-a1c-ok';
            if (v < 9) return 'diab-a1c-warn';
            return 'diab-a1c-bad';
        },
        init() { console.log('Diabetology EMR initialized', this.formData); },
        updateField() {
            console.log('Diabetology data updated', JSON.stringify(this.formData).substring(0, 220));
            if (window.triggerAutoSave) window.triggerAutoSave();
        },
        toggleDiagnosis(dx) {
            const idx = this.selectedDiagnoses.indexOf(dx.code);
            if (idx > -1) this.selectedDiagnoses.splice(idx, 1); else this.selectedDiagnoses.push(dx.code);
            console.log('Diabetology diagnosis toggled', dx.code);
            if (window.triggerAutoSave) window.triggerAutoSave();
        },
    };
}
</script>
<?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/emr/specialty/diabetology.blade.php ENDPATH**/ ?>