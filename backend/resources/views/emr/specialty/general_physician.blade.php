{{-- General Physician EMR Template --}}
<style>
.gp-card { background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; margin-bottom: 16px; }
.gp-header { padding: 12px 16px; background: linear-gradient(135deg, #f1f5f9, #f8fafc); border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; gap: 8px; cursor: pointer; }
.gp-header h3 { font-size: 14px; font-weight: 600; color: #1e293b; margin: 0; }
.gp-body { padding: 16px; }
.field-label { font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
.field-input { width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; }
.field-input:focus { outline: none; border-color: #1e293b; box-shadow: 0 0 0 3px rgba(30,41,59,0.1); }
.field-select { width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: white; }
.chip { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #f1f5f9; border-radius: 20px; font-size: 12px; margin: 4px; cursor: pointer; transition: all 0.15s; }
.chip:hover { background: #e2e8f0; }
.chip.selected { background: #1e293b; color: white; }
</style>

<div x-data="generalPhysicianEMR()" class="gp-section">
    <div class="gp-card">
        <div class="gp-header" @click="sections.hx = !sections.hx">
            <span style="font-size:18px;">📋</span><h3>History</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.hx ? '▼' : '▶'"></span>
        </div>
        <div class="gp-body" x-show="sections.hx" x-collapse>
            <div class="field-label">HPI</div>
            <textarea class="field-input" style="min-height:90px;" x-model="formData.hx.hpi" @change="updateField()"></textarea>
            <div class="field-label" style="margin-top:12px;">Past medical</div>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;">
                <template x-for="p in pmhOpts" :key="p">
                    <label style="font-size:12px;display:flex;align-items:center;gap:6px;"><input type="checkbox" :checked="formData.hx.pmh.includes(p)" @change="togglePmh(p)"><span x-text="p"></span></label>
                </template>
            </div>
        </div>
    </div>
    <div class="gp-card">
        <div class="gp-header" @click="sections.gen_ex = !sections.gen_ex">
            <span style="font-size:18px;">🩺</span><h3>General Examination</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.gen_ex ? '▼' : '▶'"></span>
        </div>
        <div class="gp-body" x-show="sections.gen_ex" x-collapse>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;">
                <template x-for="g in genSigns" :key="g">
                    <label style="font-size:12px;display:flex;align-items:center;gap:6px;"><input type="checkbox" x-model="formData.gen_ex[g]" @change="updateField()"><span x-text="g"></span></label>
                </template>
            </div>
            <div style="margin-top:12px;"><div class="field-label">JVP</div><input type="text" class="field-input" x-model="formData.gen_ex.jvp" @change="updateField()"></div>
        </div>
    </div>
    <div class="gp-card">
        <div class="gp-header" @click="sections.systems = !sections.systems">
            <span style="font-size:18px;">🫀</span><h3>Systemic Exam</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.systems ? '▼' : '▶'"></span>
        </div>
        <div class="gp-body" x-show="sections.systems" x-collapse>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
                <div><div class="field-label">CVS</div><textarea class="field-input" style="min-height:44px;" x-model="formData.systems.cvs" @change="updateField()"></textarea>
                    <select class="field-select" style="margin-top:6px;" x-model="formData.systems.cvs_key" @change="updateField()"><option value="">Key finding</option><option>Normal</option><option>Murmur</option><option>S3/S4</option></select></div>
                <div><div class="field-label">RS</div><textarea class="field-input" style="min-height:44px;" x-model="formData.systems.rs" @change="updateField()"></textarea>
                    <select class="field-select" style="margin-top:6px;" x-model="formData.systems.rs_key" @change="updateField()"><option value="">Key finding</option><option>Clear</option><option>Creps</option><option>Wheeze</option></select></div>
                <div><div class="field-label">PA</div><textarea class="field-input" style="min-height:44px;" x-model="formData.systems.pa" @change="updateField()"></textarea>
                    <select class="field-select" style="margin-top:6px;" x-model="formData.systems.pa_key" @change="updateField()"><option value="">Key finding</option><option>Soft</option><option>Tender</option><option>Hepatosplenomegaly</option></select></div>
                <div><div class="field-label">CNS</div><textarea class="field-input" style="min-height:44px;" x-model="formData.systems.cns" @change="updateField()"></textarea>
                    <select class="field-select" style="margin-top:6px;" x-model="formData.systems.cns_key" @change="updateField()"><option value="">Key finding</option><option>Normal</option><option>Focal deficit</option><option>Meningeal</option></select></div>
            </div>
        </div>
    </div>
    <div class="gp-card">
        <div class="gp-header" @click="sections.inv = !sections.inv">
            <span style="font-size:18px;">🧪</span><h3>Investigations</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.inv ? '▼' : '▶'"></span>
        </div>
        <div class="gp-body" x-show="sections.inv" x-collapse>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;">
                <template x-for="t in invTests" :key="t">
                    <label style="font-size:12px;display:flex;align-items:center;gap:6px;"><input type="checkbox" :checked="formData.inv.includes(t)" @change="toggleInv(t)"><span x-text="t"></span></label>
                </template>
            </div>
        </div>
    </div>
    <div class="gp-card">
        <div class="gp-header" @click="sections.dx = !sections.dx">
            <span style="font-size:18px;">🏥</span><h3>Diagnoses</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.dx ? '▼' : '▶'"></span>
        </div>
        <div class="gp-body" x-show="sections.dx" x-collapse>
            <div style="display:flex;flex-wrap:wrap;gap:4px;">
                <template x-for="dx in commonDiagnoses" :key="dx.code">
                    <button type="button" class="chip" :class="{'selected': selectedDiagnoses.includes(dx.code)}" @click="toggleDiagnosis(dx)">
                        <span x-text="dx.name"></span><span style="font-size:10px;opacity:0.7;" x-text="dx.code"></span>
                    </button>
                </template>
            </div>
            <input type="hidden" name="general_physician_diagnoses" :value="JSON.stringify(selectedDiagnoses)">
        </div>
    </div>
    <input type="hidden" name="general_physician_data" :value="JSON.stringify(formData)">
</div>

@php
    $generalPhysicianFormDataDefault = [
        'hx' => ['hpi' => '', 'pmh' => []],
        'gen_ex' => ['pallor' => false, 'icterus' => false, 'cyanosis' => false, 'clubbing' => false, 'edema' => false, 'lymphadenopathy' => false, 'jvp' => ''],
        'systems' => ['cvs' => '', 'cvs_key' => '', 'rs' => '', 'rs_key' => '', 'pa' => '', 'pa_key' => '', 'cns' => '', 'cns_key' => ''],
        'inv' => [],
    ];
@endphp

<script>
console.log('General Physician EMR template loaded');
function generalPhysicianEMR() {
    return {
        sections: { hx: true, gen_ex: true, systems: true, inv: true, dx: true },
        pmhOpts: ['DM', 'HTN', 'Asthma', 'TB', 'Cardiac', 'Thyroid'],
        genSigns: ['pallor', 'icterus', 'cyanosis', 'clubbing', 'edema', 'lymphadenopathy'],
        invTests: ['CBC', 'LFT', 'KFT', 'Lipid', 'Thyroid', 'Urine', 'CXR', 'ECG', 'USG'],
        formData: @json(($visit ?? null)?->getStructuredField('general_physician_data') ?? $generalPhysicianFormDataDefault),
        commonDiagnoses: [
            { code: 'J06.9', name: 'URTI' }, { code: 'J18', name: 'Pneumonia' }, { code: 'A09', name: 'AGE' },
            { code: 'E11', name: 'DM' }, { code: 'I10', name: 'HTN' }, { code: 'D50', name: 'Anemia' },
            { code: 'N39.0', name: 'UTI' }, { code: 'A01', name: 'Typhoid' }, { code: 'B50', name: 'Malaria' }, { code: 'R50', name: 'Fever' },
        ],
        selectedDiagnoses: @json(($visit ?? null)?->getStructuredField('general_physician_diagnoses') ?? []),
        init() {
            console.log('General Physician EMR initialized', this.formData);
            if (!Array.isArray(this.formData.hx.pmh)) this.formData.hx.pmh = [];
            if (!Array.isArray(this.formData.inv)) this.formData.inv = [];
        },
        togglePmh(p) {
            const a = this.formData.hx.pmh, i = a.indexOf(p);
            if (i > -1) a.splice(i, 1); else a.push(p);
            console.log('[GP] PMH', p);
            this.updateField();
        },
        toggleInv(t) {
            const a = this.formData.inv, i = a.indexOf(t);
            if (i > -1) a.splice(i, 1); else a.push(t);
            console.log('[GP] Inv', t);
            this.updateField();
        },
        updateField() {
            console.log('General Physician data updated', JSON.stringify(this.formData).substring(0, 220));
            if (window.triggerAutoSave) window.triggerAutoSave();
        },
        toggleDiagnosis(dx) {
            const idx = this.selectedDiagnoses.indexOf(dx.code);
            if (idx > -1) this.selectedDiagnoses.splice(idx, 1); else this.selectedDiagnoses.push(dx.code);
            console.log('GP diagnosis toggled', dx.code);
            if (window.triggerAutoSave) window.triggerAutoSave();
        },
    };
}
</script>
