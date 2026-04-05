{{-- Homeopathy EMR Template --}}
<style>
.hom-card { background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; margin-bottom: 16px; }
.hom-header { padding: 12px 16px; background: linear-gradient(135deg, #ede9fe, #f5f3ff); border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; gap: 8px; cursor: pointer; }
.hom-header h3 { font-size: 14px; font-weight: 600; color: #4c1d95; margin: 0; }
.hom-body { padding: 16px; }
.field-label { font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
.field-input { width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; }
.field-input:focus { outline: none; border-color: #4c1d95; box-shadow: 0 0 0 3px rgba(76,29,149,0.12); }
.field-select { width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: white; }
.chip { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #f1f5f9; border-radius: 20px; font-size: 12px; margin: 4px; cursor: pointer; transition: all 0.15s; }
.chip:hover { background: #e2e8f0; }
.chip.selected { background: #4c1d95; color: white; }
.hom-radio { display: flex; gap: 12px; flex-wrap: wrap; font-size: 12px; }
</style>

<div x-data="homeopathyEMR()" class="hom-section">
    <div class="hom-card">
        <div class="hom-header" @click="sections.case = !sections.case">
            <span style="font-size:18px;">📜</span><h3>Case Taking</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.case ? '▼' : '▶'"></span>
        </div>
        <div class="hom-body" x-show="sections.case" x-collapse>
            <div style="margin-bottom:10px;"><div class="field-label">Chief complaint</div><textarea class="field-input" style="min-height:50px;" x-model="formData.case.cc" @change="updateField()"></textarea></div>
            <div style="margin-bottom:10px;"><div class="field-label">Modalities — Better</div><textarea class="field-input" style="min-height:44px;" x-model="formData.case.better" @change="updateField()"></textarea></div>
            <div style="margin-bottom:10px;"><div class="field-label">Modalities — Worse</div><textarea class="field-input" style="min-height:44px;" x-model="formData.case.worse" @change="updateField()"></textarea></div>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:10px;">
                <div><div class="field-label">Concomitant</div><input type="text" class="field-input" x-model="formData.case.concomitant" @change="updateField()"></div>
                <div><div class="field-label">Location</div><input type="text" class="field-input" x-model="formData.case.location" @change="updateField()"></div>
                <div><div class="field-label">Amelioration</div><input type="text" class="field-input" x-model="formData.case.amel" @change="updateField()"></div>
                <div><div class="field-label">Modalities (CLAMS)</div><input type="text" class="field-input" x-model="formData.case.modalities" @change="updateField()"></div>
                <div><div class="field-label">Sensation</div><input type="text" class="field-input" x-model="formData.case.sensation" @change="updateField()"></div>
            </div>
        </div>
    </div>
    <div class="hom-card">
        <div class="hom-header" @click="sections.constit = !sections.constit">
            <span style="font-size:18px;">🌡️</span><h3>Constitution</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.constit ? '▼' : '▶'"></span>
        </div>
        <div class="hom-body" x-show="sections.constit" x-collapse>
            <div class="field-label">Thermal</div>
            <div class="hom-radio">
                <label><input type="radio" value="hot" x-model="formData.constit.thermal" @change="updateField()"> Hot</label>
                <label><input type="radio" value="chilly" x-model="formData.constit.thermal" @change="updateField()"> Chilly</label>
                <label><input type="radio" value="ambithermal" x-model="formData.constit.thermal" @change="updateField()"> Ambithermal</label>
            </div>
            <div style="margin-top:12px;display:grid;grid-template-columns:repeat(2,1fr);gap:10px;">
                <div><div class="field-label">Thirst</div><input type="text" class="field-input" x-model="formData.constit.thirst" @change="updateField()"></div>
                <div><div class="field-label">Appetite</div><input type="text" class="field-input" x-model="formData.constit.appetite" @change="updateField()"></div>
                <div><div class="field-label">Desires</div><input type="text" class="field-input" x-model="formData.constit.desires" @change="updateField()"></div>
                <div><div class="field-label">Aversions</div><input type="text" class="field-input" x-model="formData.constit.aversions" @change="updateField()"></div>
            </div>
        </div>
    </div>
    <div class="hom-card">
        <div class="hom-header" @click="sections.miasm = !sections.miasm">
            <span style="font-size:18px;">🔯</span><h3>Miasmatic Assessment</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.miasm ? '▼' : '▶'"></span>
        </div>
        <div class="hom-body" x-show="sections.miasm" x-collapse>
            <div class="hom-radio">
                <label><input type="radio" value="psora" x-model="formData.miasm.primary" @change="updateField()"> Psora</label>
                <label><input type="radio" value="sycosis" x-model="formData.miasm.primary" @change="updateField()"> Sycosis</label>
                <label><input type="radio" value="syphilis" x-model="formData.miasm.primary" @change="updateField()"> Syphilis</label>
            </div>
        </div>
    </div>
    <div class="hom-card">
        <div class="hom-header" @click="sections.remedy = !sections.remedy">
            <span style="font-size:18px;">💊</span><h3>Remedy Selection</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.remedy ? '▼' : '▶'"></span>
        </div>
        <div class="hom-body" x-show="sections.remedy" x-collapse>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
                <div style="grid-column:1/-1;"><div class="field-label">Remedy</div><input type="text" class="field-input" x-model="formData.remedy.name" @change="updateField()"></div>
                <div>
                    <div class="field-label">Potency</div>
                    <select class="field-select" x-model="formData.remedy.potency" @change="updateField()">
                        <option>6C</option><option>30C</option><option>200C</option><option>1M</option><option>10M</option><option>LM</option>
                    </select>
                </div>
                <div><div class="field-label">Dose</div><input type="text" class="field-input" x-model="formData.remedy.dose" @change="updateField()"></div>
                <div style="grid-column:1/-1;">
                    <div class="field-label">Repetition</div>
                    <select class="field-select" x-model="formData.remedy.repeat" @change="updateField()"><option>Single dose</option><option>Weekly</option><option>PRN</option><option>Chronic schedule</option></select>
                </div>
            </div>
        </div>
    </div>
    <div class="hom-card">
        <div class="hom-header" @click="sections.fu = !sections.fu">
            <span style="font-size:18px;">📅</span><h3>Follow-up</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.fu ? '▼' : '▶'"></span>
        </div>
        <div class="hom-body" x-show="sections.fu" x-collapse>
            <div>
                <div class="field-label">Response</div>
                <select class="field-select" x-model="formData.fu.response" @change="updateField()">
                    <option value="">—</option><option>Amelioration</option><option>Aggravation</option><option>New symptoms</option>
                </select>
            </div>
            <div style="margin-top:10px;"><div class="field-label">Direction of cure</div><textarea class="field-input" style="min-height:50px;" x-model="formData.fu.direction" @change="updateField()"></textarea></div>
        </div>
    </div>
    <div class="hom-card">
        <div class="hom-header" @click="sections.dx = !sections.dx">
            <span style="font-size:18px;">🏥</span><h3>Diagnoses</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.dx ? '▼' : '▶'"></span>
        </div>
        <div class="hom-body" x-show="sections.dx" x-collapse>
            <div style="display:flex;flex-wrap:wrap;gap:4px;">
                <template x-for="dx in commonDiagnoses" :key="dx.code">
                    <button type="button" class="chip" :class="{'selected': selectedDiagnoses.includes(dx.code)}" @click="toggleDiagnosis(dx)">
                        <span x-text="dx.name"></span><span style="font-size:10px;opacity:0.7;" x-text="dx.code"></span>
                    </button>
                </template>
            </div>
            <input type="hidden" name="homeopathy_diagnoses" :value="JSON.stringify(selectedDiagnoses)">
        </div>
    </div>
    <input type="hidden" name="homeopathy_data" :value="JSON.stringify(formData)">
</div>

@php
    $homeopathyFormDataDefault = [
        'case' => ['cc' => '', 'better' => '', 'worse' => '', 'concomitant' => '', 'location' => '', 'amel' => '', 'modalities' => '', 'sensation' => ''],
        'constit' => ['thermal' => '', 'thirst' => '', 'appetite' => '', 'desires' => '', 'aversions' => ''],
        'miasm' => ['primary' => ''],
        'remedy' => ['name' => '', 'potency' => '30C', 'dose' => '', 'repeat' => 'Single dose'],
        'fu' => ['response' => '', 'direction' => ''],
    ];
@endphp

<script>
console.log('Homeopathy EMR template loaded');
function homeopathyEMR() {
    return {
        sections: { case: true, constit: true, miasm: true, remedy: true, fu: true, dx: true },
        formData: @json(($visit ?? null)?->getStructuredField('homeopathy_data') ?? $homeopathyFormDataDefault),
        commonDiagnoses: [
            { code: 'L20', name: 'Eczema' }, { code: 'J45', name: 'Asthma' }, { code: 'J30', name: 'Allergic rhinitis' },
            { code: 'K58', name: 'IBS' }, { code: 'F41.1', name: 'Anxiety' }, { code: 'L40', name: 'Psoriasis' },
            { code: 'M79.2', name: 'Neuralgia' }, { code: 'N39.0', name: 'UTI' },
        ],
        selectedDiagnoses: @json(($visit ?? null)?->getStructuredField('homeopathy_diagnoses') ?? []),
        init() { console.log('Homeopathy EMR initialized', this.formData); },
        updateField() {
            console.log('Homeopathy data updated', JSON.stringify(this.formData).substring(0, 200));
            if (window.triggerAutoSave) window.triggerAutoSave();
        },
        toggleDiagnosis(dx) {
            const idx = this.selectedDiagnoses.indexOf(dx.code);
            if (idx > -1) this.selectedDiagnoses.splice(idx, 1); else this.selectedDiagnoses.push(dx.code);
            console.log('Homeopathy diagnosis toggled', dx.code);
            if (window.triggerAutoSave) window.triggerAutoSave();
        },
    };
}
</script>
