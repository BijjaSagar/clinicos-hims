
<style>
.peds-card { background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; margin-bottom: 16px; }
.peds-header { padding: 12px 16px; background: linear-gradient(135deg, #dcfce7, #f0fdf4); border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; gap: 8px; cursor: pointer; }
.peds-header h3 { font-size: 14px; font-weight: 600; color: #166534; margin: 0; }
.peds-body { padding: 16px; }
.field-label { font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
.field-input { width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; }
.field-input:focus { outline: none; border-color: #166534; box-shadow: 0 0 0 3px rgba(22,101,52,0.12); }
.field-select { width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: white; }
.chip { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #f1f5f9; border-radius: 20px; font-size: 12px; margin: 4px; cursor: pointer; transition: all 0.15s; }
.chip:hover { background: #e2e8f0; }
.chip.selected { background: #166534; color: white; }
.peds-vax { width: 100%; font-size: 11px; border-collapse: collapse; }
.peds-vax th, .peds-vax td { border: 1px solid #e5e7eb; padding: 6px; }
.peds-growth { font-size: 13px; color: #166534; margin-top: 8px; padding: 8px; background: #dcfce7; border-radius: 8px; }
</style>

<div x-data="paediatricsEMR()" class="peds-section">
    <div class="peds-card">
        <div class="peds-header" @click="sections.birth = !sections.birth">
            <span style="font-size:18px;">👶</span><h3>Birth History</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.birth ? '▼' : '▶'"></span>
        </div>
        <div class="peds-body" x-show="sections.birth" x-collapse>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">
                <div><div class="field-label">GA (weeks)</div><input type="number" class="field-input" x-model="formData.birth.ga" @change="updateField()"></div>
                <div><div class="field-label">Birth weight (kg)</div><input type="number" step="0.01" class="field-input" x-model="formData.birth.bw_kg" @change="updateField()"></div>
                <div>
                    <div class="field-label">Delivery</div>
                    <select class="field-select" x-model="formData.birth.delivery" @change="updateField()"><option value="">—</option><option>NSVD</option><option>LSCS</option><option>Instrumental</option></select>
                </div>
                <div><div class="field-label">Apgar 1 min</div><input type="number" min="0" max="10" class="field-input" x-model="formData.birth.apgar1" @change="updateField()"></div>
                <div><div class="field-label">Apgar 5 min</div><input type="number" min="0" max="10" class="field-input" x-model="formData.birth.apgar5" @change="updateField()"></div>
            </div>
        </div>
    </div>
    <div class="peds-card">
        <div class="peds-header" @click="sections.growth = !sections.growth">
            <span style="font-size:18px;">📏</span><h3>Growth</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.growth ? '▼' : '▶'"></span>
        </div>
        <div class="peds-body" x-show="sections.growth" x-collapse>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">
                <div><div class="field-label">Weight (kg)</div><input type="number" step="0.01" class="field-input" x-model="formData.growth.wt" @input="updateField()"></div>
                <div><div class="field-label">Height (cm)</div><input type="number" step="0.1" class="field-input" x-model="formData.growth.ht" @input="updateField()"></div>
                <div><div class="field-label">HC (cm)</div><input type="number" step="0.1" class="field-input" x-model="formData.growth.hc" @input="updateField()"></div>
            </div>
            <div class="peds-growth" x-text="growthNote"></div>
        </div>
    </div>
    <div class="peds-card">
        <div class="peds-header" @click="sections.dev = !sections.dev">
            <span style="font-size:18px;">🧸</span><h3>Developmental Milestones</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.dev ? '▼' : '▶'"></span>
        </div>
        <div class="peds-body" x-show="sections.dev" x-collapse>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
                <template x-for="d in devDomains" :key="d.key">
                    <div>
                        <label style="display:flex;align-items:center;gap:8px;font-size:12px;"><input type="checkbox" x-model="formData.dev[d.key].ok" @change="updateField()"><span x-text="d.label"></span></label>
                        <input type="text" class="field-input" style="margin-top:4px;" x-model="formData.dev[d.key].age_note" @change="updateField()" placeholder="Age appropriate / notes">
                    </div>
                </template>
            </div>
        </div>
    </div>
    <div class="peds-card">
        <div class="peds-header" @click="sections.vax = !sections.vax">
            <span style="font-size:18px;">💉</span><h3>Vaccination Tracker</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.vax ? '▼' : '▶'"></span>
        </div>
        <div class="peds-body" x-show="sections.vax" x-collapse style="overflow-x:auto;">
            <table class="peds-vax">
                <thead><tr><th>Vaccine</th><th>Date</th><th>Status</th></tr></thead>
                <tbody>
                    <template x-for="v in vaxList" :key="v">
                        <tr>
                            <td x-text="v"></td>
                            <td><input type="date" class="field-input" style="padding:4px;font-size:11px;" x-model="formData.vax[v].date" @change="updateField()"></td>
                            <td><select class="field-select" style="padding:4px;font-size:11px;" x-model="formData.vax[v].status" @change="updateField()"><option value="">—</option><option>Given</option><option>Due</option><option>Missed</option></select></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
    <div class="peds-card">
        <div class="peds-header" @click="sections.dx = !sections.dx">
            <span style="font-size:18px;">🏥</span><h3>Diagnoses</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.dx ? '▼' : '▶'"></span>
        </div>
        <div class="peds-body" x-show="sections.dx" x-collapse>
            <div style="display:flex;flex-wrap:wrap;gap:4px;">
                <template x-for="dx in commonDiagnoses" :key="dx.code">
                    <button type="button" class="chip" :class="{'selected': selectedDiagnoses.includes(dx.code)}" @click="toggleDiagnosis(dx)">
                        <span x-text="dx.name"></span><span style="font-size:10px;opacity:0.7;" x-text="dx.code"></span>
                    </button>
                </template>
            </div>
            <input type="hidden" name="paediatrics_diagnoses" :value="JSON.stringify(selectedDiagnoses)">
        </div>
    </div>
    <input type="hidden" name="paediatrics_data" :value="JSON.stringify(formData)">
</div>

<?php
    $paediatricsFormDataDefault = [
        'birth' => ['ga' => '', 'bw_kg' => '', 'delivery' => '', 'apgar1' => '', 'apgar5' => ''],
        'growth' => ['wt' => '', 'ht' => '', 'hc' => ''],
        'dev' => ['gross' => ['ok' => false, 'age_note' => ''], 'fine' => ['ok' => false, 'age_note' => ''], 'lang' => ['ok' => false, 'age_note' => ''], 'social' => ['ok' => false, 'age_note' => '']],
        'vax' => [
            'BCG' => ['date' => '', 'status' => ''], 'OPV' => ['date' => '', 'status' => ''], 'IPV' => ['date' => '', 'status' => ''],
            'Pentavalent' => ['date' => '', 'status' => ''], 'Rotavirus' => ['date' => '', 'status' => ''], 'PCV' => ['date' => '', 'status' => ''], 'MMR' => ['date' => '', 'status' => ''],
        ],
    ];
?>

<script>
console.log('Paediatrics EMR template loaded');
function paediatricsEMR() {
    const vaxKeys = ['BCG','OPV','IPV','Pentavalent','Rotavirus','PCV','MMR'];
    const defaultVax = () => Object.fromEntries(vaxKeys.map(v => [v, { date: '', status: '' }]));
    return {
        sections: { birth: true, growth: true, dev: true, vax: true, dx: true },
        vaxList: vaxKeys,
        devDomains: [
            { key: 'gross', label: 'Gross motor' }, { key: 'fine', label: 'Fine motor' },
            { key: 'lang', label: 'Language' }, { key: 'social', label: 'Social' },
        ],
        formData: <?php echo json_encode(($visit ?? null)?->getStructuredField('paediatrics_data') ?? $paediatricsFormDataDefault, 15, 512) ?>,
        commonDiagnoses: [
            { code: 'J06.9', name: 'URTI' }, { code: 'J18', name: 'Pneumonia' }, { code: 'A09', name: 'AGE' },
            { code: 'J45', name: 'Asthma' }, { code: 'D50', name: 'Anemia' }, { code: 'H66', name: 'Otitis media' },
            { code: 'B09', name: 'Viral exanthem' }, { code: 'K52.9', name: 'Non-inf gastroenteritis' }, { code: 'R56.0', name: 'Febrile convulsion' },
        ],
        selectedDiagnoses: <?php echo json_encode(($visit ?? null)?->getStructuredField('paediatrics_diagnoses') ?? [], 15, 512) ?>,
        get growthNote() {
            const w = parseFloat(this.formData.growth.wt), h = parseFloat(this.formData.growth.ht);
            if (isNaN(w) || isNaN(h) || h <= 0) return 'Enter weight and height — plot on growth chart clinically.';
            const bmi = w / ((h / 100) * (h / 100));
            console.log('[Peds] BMI approx', bmi);
            return 'BMI (approx): ' + (Math.round(bmi * 10) / 10) + ' — correlate with age-specific percentiles.';
        },
        init() {
            console.log('Paediatrics EMR initialized', this.formData);
            if (!this.formData.vax || typeof this.formData.vax !== 'object') this.formData.vax = defaultVax();
            vaxKeys.forEach(v => { if (!this.formData.vax[v]) this.formData.vax[v] = { date: '', status: '' }; });
        },
        updateField() {
            console.log('Paediatrics data updated', JSON.stringify(this.formData).substring(0, 220));
            if (window.triggerAutoSave) window.triggerAutoSave();
        },
        toggleDiagnosis(dx) {
            const idx = this.selectedDiagnoses.indexOf(dx.code);
            if (idx > -1) this.selectedDiagnoses.splice(idx, 1); else this.selectedDiagnoses.push(dx.code);
            console.log('Peds diagnosis toggled', dx.code);
            if (window.triggerAutoSave) window.triggerAutoSave();
        },
    };
}
</script>
<?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/emr/specialty/paediatrics.blade.php ENDPATH**/ ?>