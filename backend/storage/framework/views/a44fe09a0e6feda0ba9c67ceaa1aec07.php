<?php $__env->startSection('title', 'Admit Patient'); ?>
<?php $__env->startSection('breadcrumb', 'Admit Patient'); ?>

<?php $__env->startSection('content'); ?>

<script type="application/json" id="ipd-admit-wards-json"><?php echo json_encode($admitWardsPayload->values()->all(), 15, 512) ?></script>
<div x-data="ipdAdmitForm()" class="p-4 sm:p-5 lg:p-7 max-w-3xl mx-auto space-y-5">

    
    <div class="flex items-center gap-4">
        <a href="<?php echo e(route('ipd.index')); ?>"
           class="p-2 rounded-lg border border-gray-200 text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900 font-display">Admit Patient</h1>
            <p class="text-sm text-gray-500 mt-0.5">Fill in the admission details below</p>
        </div>
    </div>

    <?php if($errors->any()): ?>
    <div class="px-4 py-3 rounded-xl text-sm" style="background:#fff1f2;color:#dc2626;border:1px solid #fecaca;">
        <p class="font-semibold mb-1">Please fix the following errors:</p>
        <ul class="list-disc list-inside space-y-0.5">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('ipd.store')); ?>" class="space-y-5">
        <?php echo csrf_field(); ?>

        
        <div class="bg-white border border-gray-200 rounded-xl p-5 space-y-4">
            <h2 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">1</span>
                Patient Information
            </h2>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Patient <span class="text-red-500">*</span></label>
                <select name="patient_id" required
                    class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="">Select patient…</option>
                    <?php $__currentLoopData = $patients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $patient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($patient->id); ?>" <?php echo e(old('patient_id') == $patient->id ? 'selected' : ''); ?>>
                        <?php echo e($patient->name); ?>

                        <?php if($patient->phone): ?> — <?php echo e($patient->phone); ?><?php endif; ?>
                        <?php if($patient->age_years): ?> (<?php echo e($patient->age_years); ?>y<?php echo e($patient->sex ? ', '.ucfirst($patient->sex) : ''); ?>)<?php endif; ?>
                    </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>

        
        <div class="bg-white border border-gray-200 rounded-xl p-5 space-y-4">
            <h2 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">2</span>
                Ward, Room &amp; Bed
            </h2>

            <?php if($admitWardsPayload->isEmpty()): ?>
            <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                No wards with free beds were found for this clinic. Add wards, rooms, and beds under <strong>Hospital Settings</strong>, ensure at least one bed status is <strong>available</strong>, then refresh this page.
            </div>
            <?php else: ?>
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ward <span class="text-red-500">*</span></label>
                    <select name="ward_id" x-model="selectedWard" @change="onWardChange()"
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">Select ward…</option>
                        <?php $__currentLoopData = $admitWardsPayload; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($w['id']); ?>"><?php echo e($w['name']); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Room <span class="text-red-500">*</span></label>
                    
                    <select id="ipd-admit-room-select" x-ref="roomSelect" x-model="selectedRoom" @change="onRoomChange()" :disabled="!selectedWard"
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white disabled:opacity-50">
                        <option value="">Select room…</option>
                    </select>
                    <p class="text-xs text-amber-700 mt-1" x-show="selectedWard && roomsForWard.length === 0">No rooms in this ward — add rooms in Hospital Settings.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bed <span class="text-red-500">*</span></label>
                    <select id="ipd-admit-bed-select" x-ref="bedSelect" name="bed_id" x-model="selectedBed" required :disabled="!selectedRoom"
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white disabled:opacity-50">
                        <option value="">Select bed…</option>
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Only available beds</p>
                    <p class="text-xs text-amber-700 mt-1" x-show="selectedRoom && bedsForRoom.length === 0">No free beds in this room.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>

        
        <div class="bg-white border border-gray-200 rounded-xl p-5 space-y-4">
            <h2 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">3</span>
                Admission Details
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Primary Doctor <span class="text-red-500">*</span></label>
                    <select name="primary_doctor_id" required
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">Select doctor…</option>
                        <?php $__currentLoopData = $doctors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doctor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($doctor->id); ?>" <?php echo e(old('primary_doctor_id') == $doctor->id ? 'selected' : ''); ?>>
                            <?php echo e($doctor->name); ?>

                        </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Admission Type <span class="text-red-500">*</span></label>
                    <select name="admission_type" required
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">Select type…</option>
                        <option value="emergency" <?php echo e(old('admission_type') === 'emergency' ? 'selected' : ''); ?>>Emergency</option>
                        <option value="planned"   <?php echo e(old('admission_type') === 'planned'   ? 'selected' : ''); ?>>Planned</option>
                        <option value="transfer"  <?php echo e(old('admission_type') === 'transfer'  ? 'selected' : ''); ?>>Transfer</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Admission Diagnosis <span class="text-red-500">*</span></label>
                <input type="text" name="diagnosis_at_admission" required
                    value="<?php echo e(old('diagnosis_at_admission')); ?>"
                    placeholder="e.g. Acute Appendicitis, Type 2 Diabetes Mellitus…"
                    class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Chief Complaint</label>
                <textarea name="chief_complaint" rows="3"
                    placeholder="Patient's main presenting complaint…"
                    class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"><?php echo e(old('chief_complaint')); ?></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Diet Type</label>
                    <select name="diet_type"
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">Select diet…</option>
                        <option value="normal"     <?php echo e(old('diet_type') === 'normal'     ? 'selected' : ''); ?>>Normal</option>
                        <option value="liquid"     <?php echo e(old('diet_type') === 'liquid'     ? 'selected' : ''); ?>>Liquid</option>
                        <option value="soft"       <?php echo e(old('diet_type') === 'soft'       ? 'selected' : ''); ?>>Soft</option>
                        <option value="diabetic"   <?php echo e(old('diet_type') === 'diabetic'   ? 'selected' : ''); ?>>Diabetic</option>
                        <option value="npo"        <?php echo e(old('diet_type') === 'npo'        ? 'selected' : ''); ?>>NPO (Nil per Oral)</option>
                        <option value="low_sodium" <?php echo e(old('diet_type') === 'low_sodium' ? 'selected' : ''); ?>>Low Sodium</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estimated Stay (days)</label>
                    <input type="number" name="estimated_days" min="1" max="365"
                        value="<?php echo e(old('estimated_days')); ?>"
                        placeholder="e.g. 5"
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Additional Notes</label>
                <textarea name="notes" rows="3"
                    placeholder="Any additional notes or instructions…"
                    class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"><?php echo e(old('notes')); ?></textarea>
            </div>
        </div>

        
        <div class="flex items-center justify-between pt-1">
            <a href="<?php echo e(route('ipd.index')); ?>" class="px-4 py-2.5 text-sm font-semibold text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                Cancel
            </a>
            <button type="submit"
                class="px-6 py-2.5 text-sm font-semibold text-white rounded-xl transition-all hover:shadow-lg hover:scale-[1.02]"
                style="background:linear-gradient(135deg,#1447E6,#0891B2);">
                Admit Patient
            </button>
        </div>

    </form>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function readWardsJson() {
    const el = document.getElementById('ipd-admit-wards-json');
    if (!el) {
        console.warn('[ipd/admit] ipd-admit-wards-json missing');
        return [];
    }
    try {
        return JSON.parse(el.textContent);
    } catch (e) {
        console.error('[ipd/admit] parse wards JSON failed', e);
        return [];
    }
}
function normalizeRooms(rooms) {
    if (rooms == null) return [];
    const list = Array.isArray(rooms) ? rooms : Object.values(rooms);
    return list.map((r) => ({
        id: r.id,
        name: r.name,
        beds: normalizeBeds(r.beds),
    }));
}
function normalizeBeds(beds) {
    if (beds == null) return [];
    return Array.isArray(beds) ? beds : Object.values(beds);
}
function normalizeWardsPayload(raw) {
    const wards = Array.isArray(raw) ? raw : [];
    return wards.map((w) => ({
        id: w.id,
        name: w.name,
        rooms: normalizeRooms(w.rooms),
    }));
}
function ipdAdmitForm() {
    const raw = readWardsJson();
    const wards = normalizeWardsPayload(raw);
    console.log('[ipd/admit] wards loaded', { wardCount: wards.length, firstWardRooms: wards[0] ? wards[0].rooms.length : 0 });
    return admitForm(wards);
}
function admitForm(wards) {
    const oldWard = <?php echo json_encode(old('ward_id', ''), 512) ?>;
    const oldBed = <?php echo json_encode(old('bed_id', ''), 512) ?>;

    return {
        wards,
        selectedWard: '',
        selectedRoom: '',
        selectedBed: '',
        init() {
            if (oldWard) {
                this.selectedWard = String(oldWard);
            }
            if (oldBed) {
                for (const w of this.wards) {
                    for (const r of (w.rooms || [])) {
                        const hit = (r.beds || []).find(b => String(b.id) === String(oldBed));
                        if (hit) {
                            this.selectedWard = String(w.id);
                            this.selectedRoom = String(r.id);
                            this.selectedBed = String(oldBed);
                            break;
                        }
                    }
                }
            }
            this.$nextTick(() => {
                this.syncRoomOptions();
                this.syncBedOptions();
                this.$nextTick(() => {
                    this.syncRoomOptions();
                    this.syncBedOptions();
                    setTimeout(() => {
                        this.syncRoomOptions();
                        this.syncBedOptions();
                    }, 0);
                });
                const rel = this.roomSelectEl();
                console.log('[ipd/admit] admitForm init', {
                    wardCount: this.wards.length,
                    oldWard,
                    oldBed: oldBed || null,
                    roomOptions: rel ? rel.options.length : 0,
                });
            });
        },
        roomSelectEl() {
            return this.$refs.roomSelect || document.getElementById('ipd-admit-room-select');
        },
        bedSelectEl() {
            return this.$refs.bedSelect || document.getElementById('ipd-admit-bed-select');
        },
        syncRoomOptions() {
            const el = this.roomSelectEl();
            if (!el) {
                console.warn('[ipd/admit] syncRoomOptions: room select element missing');
                return;
            }
            const prevRoom = this.selectedRoom;
            while (el.options.length > 1) {
                el.remove(1);
            }
            const w = this.wards.find((x) => String(x.id) === String(this.selectedWard));
            if (!w || !Array.isArray(w.rooms)) {
                console.log('[ipd/admit] syncRoomOptions: no rooms for ward', { selectedWard: this.selectedWard });
                return;
            }
            w.rooms.forEach((r) => {
                const opt = document.createElement('option');
                opt.value = String(r.id);
                opt.textContent = r.name != null && String(r.name).trim() !== '' ? String(r.name) : ('Room #' + r.id);
                el.appendChild(opt);
            });
            if (prevRoom && [...el.options].some((o) => o.value === String(prevRoom))) {
                this.selectedRoom = String(prevRoom);
            }
            console.log('[ipd/admit] syncRoomOptions done', { count: w.rooms.length, selectedWard: this.selectedWard });
        },
        syncBedOptions() {
            const el = this.bedSelectEl();
            if (!el) {
                console.warn('[ipd/admit] syncBedOptions: bed select element missing');
                return;
            }
            const prevBed = this.selectedBed;
            while (el.options.length > 1) {
                el.remove(1);
            }
            const w = this.wards.find((x) => String(x.id) === String(this.selectedWard));
            if (!w || !Array.isArray(w.rooms)) return;
            const r = w.rooms.find((x) => String(x.id) === String(this.selectedRoom));
            if (!r || !Array.isArray(r.beds)) {
                console.log('[ipd/admit] syncBedOptions: no beds for room', { selectedRoom: this.selectedRoom });
                return;
            }
            r.beds.forEach((b) => {
                const opt = document.createElement('option');
                opt.value = String(b.id);
                opt.textContent = 'Bed ' + (b.code || ('#' + b.id));
                el.appendChild(opt);
            });
            if (prevBed && [...el.options].some((o) => o.value === String(prevBed))) {
                this.selectedBed = String(prevBed);
            }
            console.log('[ipd/admit] syncBedOptions done', { count: r.beds.length, selectedRoom: this.selectedRoom });
        },
        get roomsForWard() {
            const w = this.wards.find(x => String(x.id) === String(this.selectedWard));
            return w && Array.isArray(w.rooms) ? w.rooms : [];
        },
        get bedsForRoom() {
            const r = this.roomsForWard.find(x => String(x.id) === String(this.selectedRoom));
            return r && Array.isArray(r.beds) ? r.beds : [];
        },
        onWardChange() {
            this.selectedRoom = '';
            this.selectedBed = '';
            const w = this.wards.find((x) => String(x.id) === String(this.selectedWard));
            console.log('[ipd/admit] ward changed', {
                selectedWard: this.selectedWard,
                roomCount: w && Array.isArray(w.rooms) ? w.rooms.length : 0,
            });
            this.$nextTick(() => {
                this.syncRoomOptions();
                this.syncBedOptions();
            });
        },
        onRoomChange() {
            this.selectedBed = '';
            const r = this.roomsForWard.find((x) => String(x.id) === String(this.selectedRoom));
            console.log('[ipd/admit] room changed', {
                selectedRoom: this.selectedRoom,
                bedCount: r && Array.isArray(r.beds) ? r.beds.length : 0,
            });
            this.$nextTick(() => {
                this.syncBedOptions();
            });
        },
    };
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/ipd/create.blade.php ENDPATH**/ ?>