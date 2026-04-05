<?php

namespace App\Services;

use App\Support\LabCatalogSeedData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Seeds standard lab departments + tests per clinic (idempotent by test_code).
 */
class EnsureLabCatalogDefaults
{
    public function syncForClinic(int $clinicId): void
    {
        if (! Schema::hasTable('lab_departments') || ! Schema::hasTable('lab_tests_catalog')) {
            Log::info('EnsureLabCatalogDefaults: skip — tables missing', ['clinic_id' => $clinicId]);

            return;
        }

        $catCols = array_flip(Schema::getColumnListing('lab_tests_catalog'));
        if (! isset($catCols['department_id'], $catCols['test_code'], $catCols['test_name'])) {
            Log::info('EnsureLabCatalogDefaults: skip — HIMS lab_tests_catalog columns not present', [
                'clinic_id' => $clinicId,
                'columns'   => array_keys($catCols),
            ]);

            return;
        }

        $deptIds = [];
        $insertedDepts = 0;
        $insertedTests = 0;

        try {
            DB::transaction(function () use ($clinicId, &$deptIds, &$insertedDepts, &$insertedTests, $catCols) {
                foreach (LabCatalogSeedData::departments() as $d) {
                    $existing = DB::table('lab_departments')
                        ->where('clinic_id', $clinicId)
                        ->where('code', $d['code'])
                        ->value('id');
                    if ($existing) {
                        $deptIds[$d['code']] = (int) $existing;
                        Log::debug('EnsureLabCatalogDefaults: department exists', ['code' => $d['code'], 'id' => $existing]);

                        continue;
                    }
                    $id = DB::table('lab_departments')->insertGetId([
                        'clinic_id'  => $clinicId,
                        'name'       => $d['name'],
                        'code'       => $d['code'],
                        'is_active'  => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $deptIds[$d['code']] = (int) $id;
                    $insertedDepts++;
                    Log::info('EnsureLabCatalogDefaults: department inserted', ['code' => $d['code'], 'id' => $id]);
                }

                foreach (LabCatalogSeedData::tests() as $t) {
                    $deptCode = $t['dept'];
                    if (! isset($deptIds[$deptCode])) {
                        Log::warning('EnsureLabCatalogDefaults: unknown department on test row', ['dept' => $deptCode, 'test' => $t['code']]);

                        continue;
                    }
                    $exists = DB::table('lab_tests_catalog')
                        ->where('clinic_id', $clinicId)
                        ->where('test_code', $t['code'])
                        ->exists();
                    if ($exists) {
                        continue;
                    }

                    $sample = $this->normalizeSampleType($t['sample']);
                    $row = [
                        'clinic_id'      => $clinicId,
                        'department_id'  => $deptIds[$deptCode],
                        'test_code'      => $t['code'],
                        'test_name'      => $t['name'],
                        'test_type'      => 'single',
                        'price'          => $t['price'],
                        'sample_type'    => $sample,
                        'tat_hours'      => $t['tat'],
                        'is_active'      => true,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ];
                    $row = array_intersect_key($row, $catCols);
                    DB::table('lab_tests_catalog')->insert($row);
                    $insertedTests++;
                    Log::debug('EnsureLabCatalogDefaults: test inserted', ['code' => $t['code'], 'dept' => $deptCode]);
                }
            });
        } catch (\Throwable $e) {
            Log::error('EnsureLabCatalogDefaults: sync failed', [
                'clinic_id' => $clinicId,
                'error'     => $e->getMessage(),
                'trace'     => $e->getTraceAsString(),
            ]);
            throw $e;
        }

        Log::info('EnsureLabCatalogDefaults: sync complete', [
            'clinic_id'      => $clinicId,
            'departments_new'=> $insertedDepts,
            'tests_new'      => $insertedTests,
        ]);
    }

    private function normalizeSampleType(string $sample): string
    {
        $s = strtolower(trim($sample));
        $allowed = ['blood', 'urine', 'stool', 'swab', 'fluid', 'tissue', 'sputum', 'other'];

        return in_array($s, $allowed, true) ? $s : 'other';
    }
}
