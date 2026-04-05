<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Bed;
use App\Models\HospitalRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class HospitalSettingsController extends Controller
{
    /**
     * True when hospital_settings stores key/value pairs per clinic (EAV).
     */
    private function isHospitalSettingsEav(): bool
    {
        if (!Schema::hasTable('hospital_settings')) {
            return false;
        }

        return Schema::hasColumn('hospital_settings', 'key')
            && Schema::hasColumn('hospital_settings', 'value');
    }

    private function getSettings(int $clinicId): array
    {
        $defaults = [
            'hospital_name'            => '',
            'hospital_type'            => 'clinic',
            'total_beds'               => '0',
            'icu_beds'                 => '0',
            'emergency_beds'           => '0',
            'registration_prefix'      => 'IPD',
            'discharge_summary_footer' => '',
            'enable_ipd'               => '0',
            'enable_pharmacy'          => '0',
            'enable_lab'               => '0',
            'enable_opd_queue'         => '0',
        ];

        try {
            if (!Schema::hasTable('hospital_settings')) {
                Log::warning('HospitalSettingsController: hospital_settings table missing');

                return $this->normalizeSettings($defaults, $clinicId);
            }

            if ($this->isHospitalSettingsEav()) {
                Log::info('HospitalSettingsController: loading EAV hospital_settings', ['clinic_id' => $clinicId]);
                $rows = DB::table('hospital_settings')
                    ->where('clinic_id', $clinicId)
                    ->pluck('value', 'key')
                    ->toArray();

                return $this->normalizeSettings(array_merge($defaults, $rows), $clinicId);
            }

            Log::info('HospitalSettingsController: loading wide-row hospital_settings', ['clinic_id' => $clinicId]);

            return $this->normalizeSettings($this->getSettingsFromWideHospitalRow($clinicId, $defaults), $clinicId);
        } catch (\Throwable $e) {
            Log::error('HospitalSettingsController: Failed to load settings', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return $this->normalizeSettings($defaults, $clinicId);
        }
    }

    /**
     * Single-row schema (e.g. migration 2026_04_01) — map columns to form keys.
     */
    private function getSettingsFromWideHospitalRow(int $clinicId, array $defaults): array
    {
        $row = DB::table('hospital_settings')->where('clinic_id', $clinicId)->first();
        $clinicName = '';
        if (Schema::hasTable('clinics')) {
            $clinicName = (string) (DB::table('clinics')->where('id', $clinicId)->value('name') ?? '');
        }

        if (!$row) {
            return array_merge($defaults, ['hospital_name' => $clinicName]);
        }

        $ht = (string) ($row->hospital_type ?? 'clinic');
        // Form uses "polyclinic"; migration enum may use multi_specialty / multi_speciality
        if (in_array($ht, ['multi_specialty', 'multi_speciality'], true)) {
            $ht = 'polyclinic';
        }

        $mapped = [
            'hospital_name'            => $clinicName,
            'hospital_type'            => $ht,
            'total_beds'               => (string) ($row->total_beds ?? 0),
            'icu_beds'                 => '0',
            'emergency_beds'           => '0',
            'registration_prefix'      => (string) ($row->registration_number ?? $defaults['registration_prefix']),
            'discharge_summary_footer' => '',
            'enable_ipd'               => !empty($row->ipd_active) ? '1' : '0',
            'enable_pharmacy'          => !empty($row->pharmacy_active) ? '1' : '0',
            'enable_lab'               => !empty($row->lab_active) ? '1' : '0',
            'enable_opd_queue'         => !empty($row->opd_active) ? '1' : '0',
        ];

        if (Schema::hasColumn('hospital_settings', 'icu_active')) {
            $mapped['icu_beds'] = !empty($row->icu_active) ? '1' : '0';
        }
        if (Schema::hasColumn('hospital_settings', 'emergency_active')) {
            $mapped['emergency_beds'] = !empty($row->emergency_active) ? '1' : '0';
        }

        Log::info('HospitalSettingsController@getSettingsFromWideHospitalRow', ['mapped_keys' => array_keys($mapped)]);

        return array_merge($defaults, $mapped);
    }

    /**
     * Ensure checkbox and numeric fields are strings the Blade can compare safely.
     */
    private function normalizeSettings(array $settings, int $clinicId): array
    {
        foreach (['enable_ipd', 'enable_pharmacy', 'enable_lab', 'enable_opd_queue'] as $k) {
            $v = $settings[$k] ?? '0';
            $on = $v === true || $v === 1 || in_array((string) $v, ['1', 'true', 'on', 'yes'], true);
            $settings[$k] = $on ? '1' : '0';
        }
        foreach (['total_beds', 'icu_beds', 'emergency_beds'] as $k) {
            $settings[$k] = (string) ($settings[$k] ?? 0);
        }
        if ($settings['hospital_name'] === '' && Schema::hasTable('clinics')) {
            $settings['hospital_name'] = (string) (DB::table('clinics')->where('id', $clinicId)->value('name') ?? '');
        }

        return $settings;
    }

    /**
     * Count physical beds per ward (hospital_rooms → hospital_beds).
     */
    private function wardBedCounts(int $clinicId, Collection $wards): array
    {
        $counts = [];
        if (!Schema::hasTable('hospital_beds') || !Schema::hasTable('hospital_rooms')) {
            return $counts;
        }
        foreach ($wards as $w) {
            $wid = $w->id ?? null;
            if (!$wid) {
                continue;
            }
            try {
                $counts[$wid] = (int) DB::table('hospital_beds')
                    ->join('hospital_rooms', 'hospital_beds.room_id', '=', 'hospital_rooms.id')
                    ->where('hospital_rooms.clinic_id', $clinicId)
                    ->where('hospital_rooms.ward_id', $wid)
                    ->count();
            } catch (\Throwable $e) {
                Log::warning('wardBedCounts query failed', ['ward_id' => $wid, 'error' => $e->getMessage()]);
                $counts[$wid] = 0;
            }
        }

        return $counts;
    }

    private function loadWards(int $clinicId): Collection
    {
        try {
            if (Schema::hasTable('hospital_wards')) {
                $wards = DB::table('hospital_wards')->where('clinic_id', $clinicId)->orderBy('name')->get();
                Log::info('HospitalSettingsController@loadWards hospital_wards', ['count' => $wards->count()]);

                return $wards;
            }
            if (Schema::hasTable('wards')) {
                $wards = DB::table('wards')->where('clinic_id', $clinicId)->orderBy('name')->get();
                foreach ($wards as $w) {
                    $w->type = $w->ward_type ?? null;
                    if (!isset($w->total_beds)) {
                        $w->total_beds = 0;
                    }
                }
                Log::info('HospitalSettingsController@loadWards wards', ['count' => $wards->count()]);

                return $wards;
            }
        } catch (\Throwable $e) {
            Log::error('HospitalSettingsController: Failed to load wards', ['error' => $e->getMessage()]);
        }

        return collect();
    }

    public function index()
    {
        $clinicId = auth()->user()->clinic_id;
        Log::info('HospitalSettingsController@index', ['user_id' => auth()->id(), 'clinic_id' => $clinicId]);
        if (!$clinicId) {
            Log::warning('HospitalSettingsController@index: missing clinic_id');

            return redirect()->route('dashboard')->with('error', 'No clinic is linked to this account.');
        }
        $settings = $this->getSettings((int) $clinicId);
        $wards = $this->loadWards((int) $clinicId);
        $wardBedCounts = $this->wardBedCounts((int) $clinicId, $wards);

        $roomsByWard = collect();
        $roomBedCounts = [];
        if (Schema::hasTable('hospital_rooms')) {
            $roomsByWard = DB::table('hospital_rooms')
                ->where('clinic_id', $clinicId)
                ->orderBy('name')
                ->get()
                ->groupBy('ward_id');
            Log::info('HospitalSettingsController@index rooms grouped', ['ward_keys' => $roomsByWard->keys()->count()]);

            if (Schema::hasTable('hospital_beds')) {
                foreach ($roomsByWard->flatten() as $roomRow) {
                    $rid = (int) $roomRow->id;
                    try {
                        $roomBedCounts[$rid] = (int) DB::table('hospital_beds')->where('room_id', $rid)->count();
                    } catch (\Throwable $e) {
                        Log::warning('HospitalSettingsController@index roomBedCounts failed', ['room_id' => $rid, 'error' => $e->getMessage()]);
                        $roomBedCounts[$rid] = 0;
                    }
                }
            }
        }

        Log::info('HospitalSettingsController@index room bed counts', ['rooms_counted' => count($roomBedCounts)]);

        return view('hospital-settings.index', compact('settings', 'wards', 'wardBedCounts', 'roomsByWard', 'roomBedCounts'));
    }

    public function update(Request $request)
    {
        $clinicId = auth()->user()->clinic_id;
        Log::info('HospitalSettingsController@update', ['clinic_id' => $clinicId]);
        if (!$clinicId) {
            return redirect()->route('dashboard')->with('error', 'No clinic is linked to this account.');
        }

        if (!Schema::hasTable('hospital_settings')) {
            return back()->with('error', 'Hospital settings table is missing. Please run migrations.');
        }

        $validated = $request->validate([
            'hospital_name'            => 'required|string|max:255',
            'hospital_type'            => 'required|in:clinic,hospital,nursing_home,polyclinic',
            'total_beds'               => 'nullable|integer|min:0',
            'icu_beds'                 => 'nullable|integer|min:0',
            'emergency_beds'           => 'nullable|integer|min:0',
            'registration_prefix'      => 'nullable|string|max:20',
            'discharge_summary_footer' => 'nullable|string',
            'enable_ipd'               => 'nullable',
            'enable_pharmacy'          => 'nullable',
            'enable_lab'               => 'nullable',
            'enable_opd_queue'         => 'nullable',
        ]);

        try {
            if (Schema::hasTable('clinics')) {
                DB::table('clinics')->where('id', $clinicId)->update([
                    'name'       => $validated['hospital_name'],
                    'updated_at' => now(),
                ]);
                Log::info('HospitalSettingsController@update clinic name synced', ['clinic_id' => $clinicId]);
            }

            if ($this->isHospitalSettingsEav()) {
                foreach ($validated as $key => $value) {
                    if (in_array($key, ['enable_ipd', 'enable_pharmacy', 'enable_lab', 'enable_opd_queue'], true)) {
                        $value = $request->has($key) ? '1' : '0';
                    }
                    $update = ['value' => $value ?? '', 'updated_at' => now()];
                    if (Schema::hasColumn('hospital_settings', 'created_at')) {
                        $exists = DB::table('hospital_settings')->where('clinic_id', $clinicId)->where('key', $key)->exists();
                        if (!$exists) {
                            $update['created_at'] = now();
                        }
                    }
                    DB::table('hospital_settings')->updateOrInsert(
                        ['clinic_id' => $clinicId, 'key' => $key],
                        $update
                    );
                }
                Log::info('HospitalSettingsController@update EAV saved');

                return back()->with('success', 'Hospital settings saved');
            }

            $this->saveWideHospitalRow($clinicId, $validated, $request);
            Log::info('HospitalSettingsController@update wide row saved');

            return back()->with('success', 'Hospital settings saved');
        } catch (\Throwable $e) {
            Log::error('HospitalSettingsController@update error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return back()->with('error', 'Failed to save settings: '.$e->getMessage());
        }
    }

    /**
     * Persist to single-row hospital_settings + optional clinic columns.
     */
    private function saveWideHospitalRow(int $clinicId, array $validated, Request $request): void
    {
        $cols = array_flip(Schema::getColumnListing('hospital_settings'));
        $row = [];

        if (isset($cols['hospital_type'])) {
            $ht = $validated['hospital_type'];
            if ($ht === 'polyclinic') {
                $ht = 'multi_specialty';
            }
            $row['hospital_type'] = $ht;
        }
        if (isset($cols['total_beds'])) {
            $row['total_beds'] = (int) ($validated['total_beds'] ?? 0);
        }
        if (isset($cols['registration_number'])) {
            $row['registration_number'] = $validated['registration_prefix'] ?? '';
        }
        if (isset($cols['ipd_active'])) {
            $row['ipd_active'] = $request->has('enable_ipd');
        }
        if (isset($cols['pharmacy_active'])) {
            $row['pharmacy_active'] = $request->has('enable_pharmacy');
        }
        if (isset($cols['lab_active'])) {
            $row['lab_active'] = $request->has('enable_lab');
        }
        if (isset($cols['opd_active'])) {
            $row['opd_active'] = $request->has('enable_opd_queue');
        }
        if (isset($cols['icu_active'])) {
            $row['icu_active'] = ((int) ($validated['icu_beds'] ?? 0) > 0);
        }
        if (isset($cols['emergency_active'])) {
            $row['emergency_active'] = ((int) ($validated['emergency_beds'] ?? 0) > 0);
        }
        if (isset($cols['updated_at'])) {
            $row['updated_at'] = now();
        }
        if (isset($cols['created_at'])) {
            $exists = DB::table('hospital_settings')->where('clinic_id', $clinicId)->exists();
            if (!$exists) {
                $row['created_at'] = now();
            }
        }

        $row = array_intersect_key($row, $cols);
        if ($row === []) {
            Log::warning('HospitalSettingsController@saveWideHospitalRow: no writable columns matched schema');

            return;
        }

        DB::table('hospital_settings')->updateOrInsert(['clinic_id' => $clinicId], $row);
    }

    public function storeWard(Request $request)
    {
        Log::info('HospitalSettingsController@storeWard');

        $request->validate([
            'name'       => 'required|string|max:120',
            'type'       => 'nullable|string|max:60',
            'floor'      => 'nullable|string|max:30',
            'wing'       => 'nullable|string|max:60',
            'is_icu'     => 'nullable',
            'total_beds' => 'nullable|integer|min:1',
            'notes'      => 'nullable|string',
        ]);

        $clinicId = auth()->user()->clinic_id;

        try {
            if (Schema::hasTable('hospital_wards')) {
                $isIcu = $request->boolean('is_icu') || $request->input('type') === 'icu';
                $row = [
                    'clinic_id'  => $clinicId,
                    'name'       => $request->name,
                    'floor'      => $request->floor,
                    'wing'       => $request->wing,
                    'is_icu'     => $isIcu,
                    'is_active'  => 1,
                    'sort_order' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                if (Schema::hasColumn('hospital_wards', 'code')) {
                    $row['code'] = strtoupper(substr(preg_replace('/\W+/', '', $request->name), 0, 8)) ?: 'WARD';
                }
                if (Schema::hasColumn('hospital_wards', 'type')) {
                    $row['type'] = $request->input('type', 'general');
                }
                $allowed = array_flip(Schema::getColumnListing('hospital_wards'));
                $row = array_intersect_key($row, $allowed);
                DB::table('hospital_wards')->insert($row);
                Log::info('HospitalSettingsController@storeWard inserted hospital_wards', ['keys' => array_keys($row)]);

                return back()->with('success', 'Ward added');
            }

            if (Schema::hasTable('wards')) {
                $wardType = (string) $request->input('type', 'general');
                $wardTypeMap = ['paediatric' => 'pediatric'];
                $wardType = $wardTypeMap[$wardType] ?? $wardType;
                $row = [
                    'clinic_id'   => $clinicId,
                    'name'        => $request->name,
                    'ward_type'   => $wardType,
                    'floor'       => $request->floor,
                    'total_beds'  => (int) $request->input('total_beds', 1),
                    'is_active'   => true,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
                $allowed = array_flip(Schema::getColumnListing('wards'));
                $row = array_intersect_key($row, $allowed);
                DB::table('wards')->insert($row);
                Log::info('HospitalSettingsController@storeWard inserted wards', ['keys' => array_keys($row)]);

                return back()->with('success', 'Ward added');
            }
        } catch (\Throwable $e) {
            Log::error('HospitalSettingsController@storeWard error', ['error' => $e->getMessage()]);
        }

        return back()->with('error', 'No ward table (hospital_wards / wards) found or insert failed.');
    }

    /**
     * Update an existing ward (hospital_wards or legacy wards).
     */
    public function updateWard(Request $request, int $ward)
    {
        $clinicId = auth()->user()->clinic_id;
        Log::info('HospitalSettingsController@updateWard', ['ward_id' => $ward, 'clinic_id' => $clinicId]);

        if (! $clinicId) {
            return redirect()->route('dashboard')->with('error', 'No clinic is linked to this account.');
        }

        $request->validate([
            'name'       => 'required|string|max:120',
            'type'       => 'nullable|string|max:60',
            'floor'      => 'nullable|string|max:30',
            'wing'       => 'nullable|string|max:60',
            'is_icu'     => 'nullable',
            'is_active'  => 'nullable',
            'total_beds' => 'nullable|integer|min:0',
            'notes'      => 'nullable|string',
        ]);

        try {
            if (Schema::hasTable('hospital_wards')) {
                $existing = DB::table('hospital_wards')->where('clinic_id', $clinicId)->where('id', $ward)->first();
                if (! $existing) {
                    Log::warning('HospitalSettingsController@updateWard ward not found', ['ward_id' => $ward]);

                    return back()->with('error', 'Ward not found.');
                }

                $isIcu = $request->boolean('is_icu') || $request->input('type') === 'icu';
                $row = [
                    'name'       => $request->name,
                    'floor'      => $request->floor,
                    'wing'       => $request->wing,
                    'is_icu'     => $isIcu,
                    'updated_at' => now(),
                ];
                if (Schema::hasColumn('hospital_wards', 'is_active')) {
                    $row['is_active'] = $request->boolean('is_active');
                }
                if (Schema::hasColumn('hospital_wards', 'type')) {
                    $row['type'] = $request->input('type', 'general');
                }
                if (Schema::hasColumn('hospital_wards', 'code')) {
                    $row['code'] = strtoupper(substr(preg_replace('/\W+/', '', $request->name), 0, 8)) ?: 'WARD';
                }

                $allowed = array_flip(Schema::getColumnListing('hospital_wards'));
                $row = array_intersect_key($row, $allowed);
                DB::table('hospital_wards')->where('id', $ward)->where('clinic_id', $clinicId)->update($row);
                Log::info('HospitalSettingsController@updateWard hospital_wards updated', ['ward_id' => $ward, 'keys' => array_keys($row)]);

                return back()->with('success', 'Ward updated.');
            }

            if (Schema::hasTable('wards')) {
                $existing = DB::table('wards')->where('clinic_id', $clinicId)->where('id', $ward)->first();
                if (! $existing) {
                    return back()->with('error', 'Ward not found.');
                }

                $wardType = (string) $request->input('type', 'general');
                $wardTypeMap = ['paediatric' => 'pediatric'];
                $wardType = $wardTypeMap[$wardType] ?? $wardType;

                $row = [
                    'name'       => $request->name,
                    'ward_type'  => $wardType,
                    'floor'      => $request->floor,
                    'total_beds' => (int) $request->input('total_beds', $existing->total_beds ?? 0),
                    'is_active'  => $request->boolean('is_active'),
                    'updated_at' => now(),
                ];
                $allowed = array_flip(Schema::getColumnListing('wards'));
                $row = array_intersect_key($row, $allowed);
                DB::table('wards')->where('id', $ward)->where('clinic_id', $clinicId)->update($row);
                Log::info('HospitalSettingsController@updateWard wards updated', ['ward_id' => $ward]);

                return back()->with('success', 'Ward updated.');
            }
        } catch (\Throwable $e) {
            Log::error('HospitalSettingsController@updateWard error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return back()->with('error', 'Could not update ward: '.$e->getMessage());
        }

        return back()->with('error', 'No ward table found.');
    }

    /**
     * Delete a ward and its rooms/beds when safe (no active admissions on those beds).
     */
    public function destroyWard(int $ward)
    {
        $clinicId = auth()->user()->clinic_id;
        Log::info('HospitalSettingsController@destroyWard', ['ward_id' => $ward, 'clinic_id' => $clinicId]);

        if (! $clinicId) {
            return redirect()->route('dashboard')->with('error', 'No clinic is linked to this account.');
        }

        try {
            if (Schema::hasTable('hospital_wards')) {
                $existing = DB::table('hospital_wards')->where('clinic_id', $clinicId)->where('id', $ward)->first();
                if (! $existing) {
                    return back()->with('error', 'Ward not found.');
                }

                $roomIds = [];
                if (Schema::hasTable('hospital_rooms')) {
                    $roomIds = DB::table('hospital_rooms')->where('clinic_id', $clinicId)->where('ward_id', $ward)->pluck('id')->all();
                }

                $bedIds = [];
                if ($roomIds !== [] && Schema::hasTable('hospital_beds')) {
                    $bedIds = DB::table('hospital_beds')->whereIn('room_id', $roomIds)->pluck('id')->all();
                }

                if ($bedIds !== [] && Schema::hasTable('ipd_admissions')) {
                    $active = DB::table('ipd_admissions')
                        ->whereIn('bed_id', $bedIds)
                        ->where('status', 'admitted')
                        ->exists();
                    if ($active) {
                        Log::warning('HospitalSettingsController@destroyWard blocked: active admission', ['ward_id' => $ward]);

                        return back()->with('error', 'Cannot delete this ward: one or more beds have active admissions. Discharge patients first.');
                    }
                }

                DB::transaction(function () use ($clinicId, $ward, $roomIds, $bedIds) {
                    if ($bedIds !== [] && Schema::hasTable('hospital_beds')) {
                        DB::table('hospital_beds')->whereIn('id', $bedIds)->delete();
                        Log::info('HospitalSettingsController@destroyWard deleted beds', ['count' => count($bedIds)]);
                    }
                    if ($roomIds !== [] && Schema::hasTable('hospital_rooms')) {
                        DB::table('hospital_rooms')->where('ward_id', $ward)->where('clinic_id', $clinicId)->delete();
                        Log::info('HospitalSettingsController@destroyWard deleted rooms', ['count' => count($roomIds)]);
                    }
                    DB::table('hospital_wards')->where('id', $ward)->where('clinic_id', $clinicId)->delete();
                });

                Log::info('HospitalSettingsController@destroyWard hospital_wards deleted', ['ward_id' => $ward]);

                return back()->with('success', 'Ward deleted.');
            }

            if (Schema::hasTable('wards')) {
                $existing = DB::table('wards')->where('clinic_id', $clinicId)->where('id', $ward)->first();
                if (! $existing) {
                    return back()->with('error', 'Ward not found.');
                }

                if (Schema::hasTable('ipd_admissions') && Schema::hasColumn('ipd_admissions', 'ward_id')) {
                    $q = DB::table('ipd_admissions')->where('ward_id', $ward)->where('status', 'admitted');
                    if (Schema::hasColumn('ipd_admissions', 'clinic_id')) {
                        $q->where('clinic_id', $clinicId);
                    }
                    if ($q->exists()) {
                        Log::warning('HospitalSettingsController@destroyWard blocked: active admission on legacy ward', ['ward_id' => $ward]);

                        return back()->with('error', 'Cannot delete this ward: there are active admissions. Discharge patients first.');
                    }
                }

                DB::transaction(function () use ($clinicId, $ward) {
                    if (Schema::hasTable('beds')) {
                        DB::table('beds')->where('ward_id', $ward)->where('clinic_id', $clinicId)->delete();
                    }
                    if (Schema::hasTable('rooms')) {
                        DB::table('rooms')->where('ward_id', $ward)->where('clinic_id', $clinicId)->delete();
                    }
                    DB::table('wards')->where('id', $ward)->where('clinic_id', $clinicId)->delete();
                });

                Log::info('HospitalSettingsController@destroyWard legacy wards deleted', ['ward_id' => $ward]);

                return back()->with('success', 'Ward deleted.');
            }
        } catch (\Throwable $e) {
            Log::error('HospitalSettingsController@destroyWard error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return back()->with('error', 'Could not delete ward: '.$e->getMessage());
        }

        return back()->with('error', 'No ward table found.');
    }

    /**
     * Add a named room under a ward (room numbers / labels for bed allocation).
     */
    public function storeRoom(Request $request)
    {
        $request->validate([
            'ward_id'   => 'required|integer|min:1',
            'room_name' => 'required|string|max:120',
            'room_type' => 'nullable|string|max:60',
        ]);

        $clinicId = auth()->user()->clinic_id;
        Log::info('HospitalSettingsController@storeRoom', ['ward_id' => $request->ward_id, 'clinic_id' => $clinicId]);

        if (!Schema::hasTable('hospital_rooms')) {
            return back()->with('error', 'Room table is not available. Run migrations.');
        }

        try {
            $ward = DB::table('hospital_wards')->where('clinic_id', $clinicId)->where('id', $request->ward_id)->first();
            if (!$ward) {
                return back()->with('error', 'Ward not found.');
            }

            $row = [
                'clinic_id' => $clinicId,
                'ward_id'   => (int) $request->ward_id,
                'name'      => $request->room_name,
                'room_type' => $request->input('room_type', 'general'),
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $allowed = array_flip(Schema::getColumnListing('hospital_rooms'));
            $row = array_intersect_key($row, $allowed);
            DB::table('hospital_rooms')->insert($row);

            Log::info('HospitalSettingsController@storeRoom inserted', ['ward_id' => $request->ward_id]);

            return back()->with('success', 'Room "'.$request->room_name.'" added under '.$ward->name.'. Use "Apply" next to that room to set how many beds it has.');
        } catch (\Throwable $e) {
            Log::error('HospitalSettingsController@storeRoom error', ['error' => $e->getMessage()]);

            return back()->with('error', 'Could not add room: '.$e->getMessage());
        }
    }

    /**
     * Ensure a ward has the requested number of beds (creates a default room + bed rows).
     */
    public function syncWardBeds(Request $request)
    {
        $request->validate([
            'ward_id'   => 'required|integer|min:1',
            'bed_count' => 'required|integer|min:1|max:500',
        ]);

        $clinicId = auth()->user()->clinic_id;
        $wardId = (int) $request->input('ward_id');
        $target = (int) $request->input('bed_count');

        Log::info('HospitalSettingsController@syncWardBeds', [
            'clinic_id' => $clinicId,
            'ward_id'   => $wardId,
            'target'    => $target,
        ]);

        if (!Schema::hasTable('hospital_wards') || !Schema::hasTable('hospital_rooms') || !Schema::hasTable('hospital_beds')) {
            return back()->with('error', 'Bed tables are not available. Run migrations.');
        }

        try {
            $ward = DB::table('hospital_wards')->where('clinic_id', $clinicId)->where('id', $wardId)->first();
            if (!$ward) {
                return back()->with('error', 'Ward not found.');
            }

            $room = HospitalRoom::where('clinic_id', $clinicId)->where('ward_id', $wardId)->first();
            if (!$room) {
                $room = HospitalRoom::create([
                    'clinic_id' => $clinicId,
                    'ward_id'   => $wardId,
                    'name'      => 'Main — '.$ward->name,
                    'room_type' => 'general',
                    'is_active' => true,
                ]);
                Log::info('HospitalSettingsController@syncWardBeds created default room', ['room_id' => $room->id]);
            }

            $existing = Bed::where('clinic_id', $clinicId)->where('room_id', $room->id)->count();
            $toAdd = max(0, $target - $existing);
            for ($i = 0; $i < $toAdd; $i++) {
                $n = $existing + $i + 1;
                Bed::create([
                    'clinic_id' => $clinicId,
                    'room_id'   => $room->id,
                    'bed_code'  => 'R'.$room->id.'-B-'.$n,
                    'status'    => 'available',
                ]);
            }

            Log::info('HospitalSettingsController@syncWardBeds done', [
                'existing' => $existing,
                'added'    => $toAdd,
                'total'    => $existing + $toAdd,
            ]);

            return back()->with('success', "Beds updated for {$ward->name}: {$target} total (added {$toAdd}).");
        } catch (\Throwable $e) {
            Log::error('HospitalSettingsController@syncWardBeds error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return back()->with('error', 'Could not update beds: '.$e->getMessage());
        }
    }

    /**
     * Set the number of physical beds for one room (creates bed rows under that room only).
     */
    public function syncRoomBeds(Request $request)
    {
        $request->validate([
            'room_id'   => 'required|integer|min:1',
            'bed_count' => 'required|integer|min:1|max:500',
        ]);

        $clinicId = auth()->user()->clinic_id;
        $roomId = (int) $request->input('room_id');
        $target = (int) $request->input('bed_count');

        Log::info('HospitalSettingsController@syncRoomBeds', [
            'clinic_id' => $clinicId,
            'room_id'   => $roomId,
            'target'    => $target,
        ]);

        if (!Schema::hasTable('hospital_wards') || !Schema::hasTable('hospital_rooms') || !Schema::hasTable('hospital_beds')) {
            return back()->with('error', 'Bed tables are not available. Run migrations.');
        }

        try {
            $room = HospitalRoom::where('clinic_id', $clinicId)->where('id', $roomId)->first();
            if (!$room) {
                Log::warning('HospitalSettingsController@syncRoomBeds room not found', ['room_id' => $roomId]);

                return back()->with('error', 'Room not found.');
            }

            $ward = DB::table('hospital_wards')->where('clinic_id', $clinicId)->where('id', $room->ward_id)->first();
            if (!$ward) {
                return back()->with('error', 'Ward not found for this room.');
            }

            $existing = Bed::where('clinic_id', $clinicId)->where('room_id', $room->id)->count();
            $toAdd = max(0, $target - $existing);
            for ($i = 0; $i < $toAdd; $i++) {
                $n = $existing + $i + 1;
                Bed::create([
                    'clinic_id' => $clinicId,
                    'room_id'   => $room->id,
                    'bed_code'  => 'R'.$room->id.'-B-'.$n,
                    'status'    => 'available',
                ]);
            }

            Log::info('HospitalSettingsController@syncRoomBeds done', [
                'room_id'  => $room->id,
                'existing' => $existing,
                'added'    => $toAdd,
                'total'    => $existing + $toAdd,
            ]);

            return back()->with('success', "Beds updated for room \"{$room->name}\" ({$ward->name}): {$target} total (added {$toAdd}).");
        } catch (\Throwable $e) {
            Log::error('HospitalSettingsController@syncRoomBeds error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return back()->with('error', 'Could not update beds: '.$e->getMessage());
        }
    }
}
