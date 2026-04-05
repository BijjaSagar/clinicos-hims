{{-- AYUSH EMR Template --}}
<style>
.ayush-card { background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; margin-bottom: 16px; }
.ayush-header { padding: 12px 16px; background: linear-gradient(135deg, #ecfccb, #f7fee7); border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; gap: 8px; cursor: pointer; }
.ayush-header h3 { font-size: 14px; font-weight: 600; color: #365314; margin: 0; }
.ayush-body { padding: 16px; }
.field-label { font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
.field-input { width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; }
.field-input:focus { outline: none; border-color: #365314; box-shadow: 0 0 0 3px rgba(54,83,20,0.12); }
.field-select { width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: white; }
.chip { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #f1f5f9; border-radius: 20px; font-size: 12px; margin: 4px; cursor: pointer; transition: all 0.15s; }
.chip:hover { background: #e2e8f0; }
.chip.selected { background: #365314; color: white; }
.ayush-dosha { text-align: center; font-size: 14px; font-weight: 600; color: #365314; margin-top: 10px; padding: 10px; background: #ecfccb; border-radius: 10px; }
</style>

<div x-data="ayushEMR()" class="ayush-section">
    <div class="ayush-card">
        <div class="ayush-header" @click="sections.prakriti = !sections.prakriti">
            <span style="font-size:18px;">☯️</span><h3>Prakriti Assessment</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.prakriti ? '▼' : '▶'"></span>
        </div>
        <div class="ayush-body" x-show="sections.prakriti" x-collapse>
            <div style="margin-bottom:10px;"><div class="field-label">Vata (0–10)</div><input type="range" min="0" max="10" x-model="formData.prakriti.vata" @input="updateField()" style="width:100%;"><span x-text="formData.prakriti.vata"></span></div>
            <div style="margin-bottom:10px;"><div class="field-label">Pitta (0–10)</div><input type="range" min="0" max="10" x-model="formData.prakriti.pitta" @input="updateField()" style="width:100%;"><span x-text="formData.prakriti.pitta"></span></div>
            <div style="margin-bottom:10px;"><div class="field-label">Kapha (0–10)</div><input type="range" min="0" max="10" x-model="formData.prakriti.kapha" @input="updateField()" style="width:100%;"><span x-text="formData.prakriti.kapha"></span></div>
            <div class="ayush-dosha" x-text="dominantDosha"></div>
        </div>
    </div>
    <div class="ayush-card">
        <div class="ayush-header" @click="sections.ashta = !sections.ashta">
            <span style="font-size:18px;">🔮</span><h3>Ashtavidha Pariksha</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.ashta ? '▼' : '▶'"></span>
        </div>
        <div class="ayush-body" x-show="sections.ashta" x-collapse>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
                <template x-for="a in ashtaKeys" :key="a.key">
                    <div>
                        <div class="field-label" x-text="a.label"></div>
                        <select class="field-select" x-model="formData.ashta[a.key]" @change="updateField()">
                            <option value="">—</option><option>Normal</option><option>Altered</option><option>Marked</option>
                        </select>
                    </div>
                </template>
            </div>
        </div>
    </div>
    <div class="ayush-card">
        <div class="ayush-header" @click="sections.vikrti = !sections.vikrti">
            <span style="font-size:18px;">⚖️</span><h3>Dosha Vikrti</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.vikrti ? '▼' : '▶'"></span>
        </div>
        <div class="ayush-body" x-show="sections.vikrti" x-collapse>
            <div style="display:flex;flex-wrap:wrap;gap:10px;">
                <template x-for="v in vikrtiOpts" :key="v">
                    <label style="font-size:12px;display:flex;align-items:center;gap:6px;"><input type="checkbox" :checked="formData.vikrti.includes(v)" @change="toggleVikrti(v)"><span x-text="v"></span></label>
                </template>
            </div>
        </div>
    </div>
    <div class="ayush-card">
        <div class="ayush-header" @click="sections.pancha = !sections.pancha">
            <span style="font-size:18px;">🌿</span><h3>Panchakarma</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.pancha ? '▼' : '▶'"></span>
        </div>
        <div class="ayush-body" x-show="sections.pancha" x-collapse>
            <div>
                <div class="field-label">Procedure</div>
                <select class="field-select" x-model="formData.pancha.procedure" @change="updateField()">
                    <option value="">—</option><option>Vamana</option><option>Virechana</option><option>Basti</option><option>Nasya</option><option>Raktamokshana</option>
                </select>
            </div>
            <div style="margin-top:12px;"><div class="field-label">Details</div><textarea class="field-input" style="min-height:70px;" x-model="formData.pancha.details" @change="updateField()"></textarea></div>
        </div>
    </div>
    <div class="ayush-card">
        <div class="ayush-header" @click="sections.dx = !sections.dx">
            <span style="font-size:18px;">🏥</span><h3>Diagnoses</h3>
            <span style="margin-left:auto;color:#64748b;" x-text="sections.dx ? '▼' : '▶'"></span>
        </div>
        <div class="ayush-body" x-show="sections.dx" x-collapse>
            <div style="display:flex;flex-wrap:wrap;gap:4px;">
                <template x-for="dx in commonDiagnoses" :key="dx.code">
                    <button type="button" class="chip" :class="{'selected': selectedDiagnoses.includes(dx.code)}" @click="toggleDiagnosis(dx)">
                        <span x-text="dx.name"></span><span style="font-size:10px;opacity:0.7;" x-text="dx.code"></span>
                    </button>
                </template>
            </div>
            <input type="hidden" name="ayush_diagnoses" :value="JSON.stringify(selectedDiagnoses)">
        </div>
    </div>
    <input type="hidden" name="ayush_data" :value="JSON.stringify(formData)">
</div>

@php
    $ayushFormDataDefault = [
        'prakriti' => ['vata' => '5', 'pitta' => '5', 'kapha' => '5'],
        'ashta' => ['nadi' => '', 'mutra' => '', 'mala' => '', 'jihva' => '', 'shabda' => '', 'sparsha' => '', 'drik' => '', 'akriti' => ''],
        'vikrti' => [],
        'pancha' => ['procedure' => '', 'details' => ''],
    ];
@endphp

<script>
console.log('AYUSH EMR template loaded');
function ayushEMR() {
    return {
        sections: { prakriti: true, ashta: true, vikrti: true, pancha: true, dx: true },
        ashtaKeys: [
            { key: 'nadi', label: 'Nadi' }, { key: 'mutra', label: 'Mutra' }, { key: 'mala', label: 'Mala' }, { key: 'jihva', label: 'Jihva' },
            { key: 'shabda', label: 'Shabda' }, { key: 'sparsha', label: 'Sparsha' }, { key: 'drik', label: 'Drik' }, { key: 'akriti', label: 'Akriti' },
        ],
        vikrtiOpts: ['Vata vriddhi', 'Pitta vriddhi', 'Kapha vriddhi', 'Ama', 'Agni mandya'],
        formData: @json(($visit ?? null)?->getStructuredField('ayush_data') ?? $ayushFormDataDefault),
        commonDiagnoses: [
            { code: 'M06.9', name: 'Amavata' }, { code: 'E11', name: 'Prameha' }, { code: 'J45', name: 'Tamaka Shwasa' },
            { code: 'M17', name: 'Sandhivata' }, { code: 'D50', name: 'Pandu' }, { code: 'J02.9', name: 'Tundikeri/Pharyngitis' },
            { code: 'R10.4', name: 'Shula/Other pain ABD' }, { code: 'M54.5', name: 'Grudrata/Low back pain' },
        ],
        selectedDiagnoses: @json(($visit ?? null)?->getStructuredField('ayush_diagnoses') ?? []),
        get dominantDosha() {
            const v = +this.formData.prakriti.vata, p = +this.formData.prakriti.pitta, k = +this.formData.prakriti.kapha;
            const m = Math.max(v, p, k);
            let d = 'Balanced';
            if (m === v && v >= p && v >= k) d = 'Vata dominant';
            else if (m === p) d = 'Pitta dominant';
            else if (m === k) d = 'Kapha dominant';
            console.log('[Ayush] dosha display', d);
            return d;
        },
        init() { console.log('AYUSH EMR initialized', this.formData); if (!Array.isArray(this.formData.vikrti)) this.formData.vikrti = []; },
        toggleVikrti(v) {
            const a = this.formData.vikrti, i = a.indexOf(v);
            if (i > -1) a.splice(i, 1); else a.push(v);
            this.updateField();
        },
        updateField() {
            console.log('AYUSH data updated', JSON.stringify(this.formData).substring(0, 200));
            if (window.triggerAutoSave) window.triggerAutoSave();
        },
        toggleDiagnosis(dx) {
            const idx = this.selectedDiagnoses.indexOf(dx.code);
            if (idx > -1) this.selectedDiagnoses.splice(idx, 1); else this.selectedDiagnoses.push(dx.code);
            console.log('Ayush diagnosis toggled', dx.code);
            if (window.triggerAutoSave) window.triggerAutoSave();
        },
    };
}
</script>
