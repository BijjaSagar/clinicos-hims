


<style>
.cardio-card { background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; margin-bottom: 16px; }
.cardio-header { padding: 12px 16px; background: linear-gradient(135deg, #fff1f2, #ffe4e6); border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; gap: 8px; cursor: pointer; }
.cardio-header h3 { font-size: 14px; font-weight: 600; color: #9f1239; margin: 0; }
.cardio-body { padding: 16px; }
.field-label { font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
.field-input { width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; }
.field-input:focus { outline: none; border-color: #e11d48; box-shadow: 0 0 0 3px rgba(225,29,72,0.1); }
.field-select { width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: white; }
.chip { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #f1f5f9; border-radius: 20px; font-size: 12px; margin: 4px; cursor: pointer; transition: all 0.15s; }
.chip:hover { background: #e2e8f0; }
.chip.selected { background: #e11d48; color: white; }
.lvef-display { font-size: 36px; font-weight: 700; text-align: center; padding: 16px; border-radius: 12px; }
.lvef-normal { background: #dcfce7; color: #166534; }
.lvef-mild { background: #fef3c7; color: #92400e; }
.lvef-moderate { background: #fed7aa; color: #9a3412; }
.lvef-severe { background: #fee2e2; color: #dc2626; }
.nyha-option { padding: 10px 16px; border: 2px solid #e5e7eb; border-radius: 10px; cursor: pointer; margin-bottom: 8px; transition: all 0.15s; }
.nyha-option.selected { border-color: #e11d48; background: #fff1f2; }
.cds-alert { padding: 10px; border-radius: 8px; margin-bottom: 8px; font-size: 13px; }
.cds-critical { background: #fee2e2; border-left: 4px solid #dc2626; }
.cds-high { background: #fef3c7; border-left: 4px solid #f59e0b; }
.cds-moderate { background: #e0f2fe; border-left: 4px solid #0ea5e9; }
</style>

<div x-data="cardiologyEMR()" class="cardio-section">
    
    <div class="cardio-card">
        <div class="cardio-header" @click="sections.risk = !sections.risk">
            <span style="font-size:18px;">🫀</span>
            <h3>Cardiovascular Risk Factors</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.risk ? '▼' : '▶'"></span>
        </div>
        <div class="cardio-body" x-show="sections.risk" x-collapse>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">
                <template x-for="rf in ['Smoking','Diabetes','Hypertension','Dyslipidemia','Family History of CAD','Obesity','Sedentary Lifestyle','Alcohol']" :key="rf">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;padding:8px;border:1px solid #e5e7eb;border-radius:8px;">
                        <input type="checkbox" :checked="formData.risk_factors.includes(rf)" @change="toggleRisk(rf)">
                        <span x-text="rf"></span>
                    </label>
                </template>
            </div>
        </div>
    </div>

    
    <div class="cardio-card">
        <div class="cardio-header" @click="sections.ecg = !sections.ecg">
            <span style="font-size:18px;">📊</span>
            <h3>ECG Findings</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.ecg ? '▼' : '▶'"></span>
        </div>
        <div class="cardio-body" x-show="sections.ecg" x-collapse>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">
                <div><div class="field-label">Heart Rate</div><input type="number" class="field-input" x-model="formData.ecg.rate" @change="updateField()" placeholder="bpm"></div>
                <div>
                    <div class="field-label">Rhythm</div>
                    <select class="field-select" x-model="formData.ecg.rhythm" @change="updateField()">
                        <option value="">Select</option>
                        <option>Normal Sinus</option><option>Sinus Tachycardia</option><option>Sinus Bradycardia</option>
                        <option>Atrial Fibrillation</option><option>Atrial Flutter</option><option>SVT</option>
                        <option>VT</option><option>Heart Block 1st</option><option>Heart Block 2nd</option><option>Heart Block 3rd (Complete)</option>
                    </select>
                </div>
                <div>
                    <div class="field-label">Axis</div>
                    <select class="field-select" x-model="formData.ecg.axis" @change="updateField()">
                        <option value="">Select</option><option>Normal</option><option>LAD</option><option>RAD</option><option>Extreme</option>
                    </select>
                </div>
                <div>
                    <div class="field-label">ST Changes</div>
                    <select class="field-select" x-model="formData.ecg.st_changes" @change="updateField()">
                        <option value="">None</option><option>ST Elevation</option><option>ST Depression</option><option>T Inversion</option><option>Tall T Waves</option>
                    </select>
                </div>
                <div><div class="field-label">PR Interval (ms)</div><input type="number" class="field-input" x-model="formData.ecg.pr_interval" @change="updateField()" placeholder="120-200"></div>
                <div><div class="field-label">QTc (ms)</div><input type="number" class="field-input" x-model="formData.ecg.qtc" @change="updateField()" placeholder="<440"></div>
            </div>
            <div style="margin-top:12px;"><div class="field-label">ECG Notes</div><textarea class="field-input" style="min-height:60px;" x-model="formData.ecg.notes" @change="updateField()"></textarea></div>
        </div>
    </div>

    
    <div class="cardio-card">
        <div class="cardio-header" @click="sections.echo = !sections.echo">
            <span style="font-size:18px;">🔊</span>
            <h3>2D Echocardiography</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.echo ? '▼' : '▶'"></span>
        </div>
        <div class="cardio-body" x-show="sections.echo" x-collapse>
            <div style="text-align:center;margin-bottom:16px;">
                <div class="field-label">Left Ventricular Ejection Fraction (LVEF)</div>
                <div class="lvef-display" :class="getLvefClass(formData.echo.lvef)">
                    <span x-text="formData.echo.lvef || '--'"></span>%
                </div>
                <input type="range" min="10" max="80" step="1" x-model="formData.echo.lvef" @input="updateField()" style="width:100%;margin-top:8px;">
                <div style="display:flex;justify-content:space-between;font-size:10px;color:#9ca3af;">
                    <span>10% (Severe)</span><span style="color:#22c55e;">Normal: 55-70%</span><span>80%</span>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">
                <div><div class="field-label">LV Dimensions</div><input type="text" class="field-input" x-model="formData.echo.lv_dimensions" @change="updateField()" placeholder="LVIDd / LVIDs"></div>
                <div>
                    <div class="field-label">RWMA</div>
                    <select class="field-select" x-model="formData.echo.rwma" @change="updateField()">
                        <option value="">Select</option><option>No RWMA</option><option>Hypokinesia</option><option>Akinesia</option><option>Dyskinesia</option>
                    </select>
                </div>
                <div>
                    <div class="field-label">Diastolic Function</div>
                    <select class="field-select" x-model="formData.echo.diastolic" @change="updateField()">
                        <option value="">Select</option><option>Normal</option><option>Grade I (Impaired relaxation)</option><option>Grade II (Pseudonormal)</option><option>Grade III (Restrictive)</option>
                    </select>
                </div>
                <div>
                    <div class="field-label">Mitral Valve</div>
                    <select class="field-select" x-model="formData.echo.mv" @change="updateField()">
                        <option>Normal</option><option>Mild MR</option><option>Moderate MR</option><option>Severe MR</option><option>MS</option><option>MVP</option>
                    </select>
                </div>
                <div>
                    <div class="field-label">Aortic Valve</div>
                    <select class="field-select" x-model="formData.echo.av" @change="updateField()">
                        <option>Normal</option><option>Mild AR</option><option>Moderate AR</option><option>Severe AR</option><option>Mild AS</option><option>Moderate AS</option><option>Severe AS</option>
                    </select>
                </div>
                <div><div class="field-label">Pericardial Effusion</div>
                    <select class="field-select" x-model="formData.echo.pericardial" @change="updateField()">
                        <option>None</option><option>Trivial</option><option>Mild</option><option>Moderate</option><option>Large</option><option>Tamponade</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    
    <div class="cardio-card">
        <div class="cardio-header" @click="sections.nyha = !sections.nyha">
            <span style="font-size:18px;">📋</span>
            <h3>NYHA Functional Classification</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.nyha ? '▼' : '▶'"></span>
        </div>
        <div class="cardio-body" x-show="sections.nyha" x-collapse>
            <template x-for="cls in nyhaClasses" :key="cls.grade">
                <div class="nyha-option" :class="{'selected': formData.nyha === cls.grade}" @click="formData.nyha = cls.grade; updateField();">
                    <strong x-text="'Class ' + cls.grade"></strong>
                    <span style="margin-left:8px;color:#64748b;font-size:13px;" x-text="cls.description"></span>
                </div>
            </template>
        </div>
    </div>

    
    <div class="cardio-card">
        <div class="cardio-header" @click="sections.fitness = !sections.fitness">
            <span style="font-size:18px;">📄</span>
            <h3>Fitness Certificate</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.fitness ? '▼' : '▶'"></span>
        </div>
        <div class="cardio-body" x-show="sections.fitness" x-collapse>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
                <div>
                    <div class="field-label">Fitness Status</div>
                    <select class="field-select" x-model="formData.fitness.status" @change="updateField()">
                        <option value="">Select</option><option>Fit</option><option>Fit with Restrictions</option><option>Temporarily Unfit</option><option>Permanently Unfit</option>
                    </select>
                </div>
                <div>
                    <div class="field-label">Risk Level</div>
                    <select class="field-select" x-model="formData.fitness.risk_level" @change="updateField()">
                        <option value="">Select</option><option>Low Risk</option><option>Moderate Risk</option><option>High Risk</option>
                    </select>
                </div>
                <div><div class="field-label">Restrictions</div><textarea class="field-input" x-model="formData.fitness.restrictions" @change="updateField()" placeholder="e.g., No heavy lifting, avoid strenuous exercise..."></textarea></div>
                <div><div class="field-label">Valid Until</div><input type="date" class="field-input" x-model="formData.fitness.valid_until" @change="updateField()"></div>
            </div>
        </div>
    </div>

    
    <div class="cardio-card">
        <div class="cardio-header" @click="sections.dx = !sections.dx">
            <span style="font-size:18px;">🏥</span>
            <h3>Quick Diagnoses</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.dx ? '▼' : '▶'"></span>
        </div>
        <div class="cardio-body" x-show="sections.dx" x-collapse>
            <div style="display:flex;flex-wrap:wrap;gap:4px;">
                <template x-for="dx in commonDiagnoses" :key="dx.code">
                    <button type="button" class="chip" :class="{'selected': selectedDiagnoses.includes(dx.code)}" @click="toggleDiagnosis(dx)">
                        <span x-text="dx.name"></span>
                        <span style="font-size:10px;opacity:0.7;" x-text="dx.code"></span>
                    </button>
                </template>
            </div>
            <input type="hidden" name="cardiology_diagnoses" :value="JSON.stringify(selectedDiagnoses)">
        </div>
    </div>

    <input type="hidden" name="cardiology_data" :value="JSON.stringify(formData)">
</div>

<?php
    $cardiologyFormDataDefault = [
        'risk_factors' => [],
        'ecg' => ['rate' => '', 'rhythm' => '', 'axis' => '', 'st_changes' => '', 'pr_interval' => '', 'qtc' => '', 'notes' => ''],
        'echo' => ['lvef' => 55, 'lv_dimensions' => '', 'rwma' => '', 'diastolic' => '', 'mv' => 'Normal', 'av' => 'Normal', 'pericardial' => 'None'],
        'nyha' => '',
        'fitness' => ['status' => '', 'risk_level' => '', 'restrictions' => '', 'valid_until' => ''],
    ];
?>

<script>
console.log('Cardiology EMR template loaded');

function cardiologyEMR() {
    return {
        sections: { risk: true, ecg: true, echo: true, nyha: false, fitness: false, dx: true },
        formData: <?php echo json_encode(($visit ?? null)?->getStructuredField('cardiology_data') ?? $cardiologyFormDataDefault, 15, 512) ?>,
        nyhaClasses: [
            { grade: 'I', description: 'No limitation of physical activity. Ordinary activity does not cause symptoms.' },
            { grade: 'II', description: 'Slight limitation. Comfortable at rest. Ordinary activity causes symptoms.' },
            { grade: 'III', description: 'Marked limitation. Comfortable at rest. Less than ordinary activity causes symptoms.' },
            { grade: 'IV', description: 'Unable to carry on any physical activity without discomfort. Symptoms at rest.' },
        ],
        commonDiagnoses: [
            { code: 'I25.1', name: 'Coronary Artery Disease' }, { code: 'I50.9', name: 'Heart Failure' },
            { code: 'I10', name: 'Essential Hypertension' }, { code: 'I48', name: 'Atrial Fibrillation' },
            { code: 'I21.9', name: 'Acute MI' }, { code: 'I42.0', name: 'Dilated Cardiomyopathy' },
            { code: 'I35.0', name: 'Aortic Stenosis' }, { code: 'I34.0', name: 'Mitral Regurgitation' },
            { code: 'I11.0', name: 'Hypertensive Heart Disease' }, { code: 'I20.9', name: 'Angina Pectoris' },
            { code: 'I47.2', name: 'Ventricular Tachycardia' }, { code: 'I44.2', name: 'Complete Heart Block' },
        ],
        selectedDiagnoses: <?php echo json_encode(($visit ?? null)?->getStructuredField('cardiology_diagnoses') ?? [], 15, 512) ?>,

        init() { console.log('Cardiology EMR initialized'); },

        toggleRisk(rf) {
            const idx = this.formData.risk_factors.indexOf(rf);
            if (idx > -1) this.formData.risk_factors.splice(idx, 1);
            else this.formData.risk_factors.push(rf);
            this.updateField();
        },

        getLvefClass(val) {
            const v = parseInt(val);
            if (isNaN(v)) return '';
            if (v >= 55) return 'lvef-normal';
            if (v >= 40) return 'lvef-mild';
            if (v >= 30) return 'lvef-moderate';
            return 'lvef-severe';
        },

        updateField() {
            console.log('Cardiology data updated', JSON.stringify(this.formData).substring(0, 200));
            if (window.triggerAutoSave) window.triggerAutoSave();
        },

        toggleDiagnosis(dx) {
            const idx = this.selectedDiagnoses.indexOf(dx.code);
            if (idx > -1) this.selectedDiagnoses.splice(idx, 1);
            else this.selectedDiagnoses.push(dx.code);
            console.log('Diagnosis toggled', dx.code);
            if (window.triggerAutoSave) window.triggerAutoSave();
        }
    };
}
</script>
<?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/emr/specialty/cardiology.blade.php ENDPATH**/ ?>