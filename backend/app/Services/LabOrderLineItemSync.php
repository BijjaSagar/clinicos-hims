<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Ensures lab_order_items rows exist for integration / JSON-based lab_orders
 * so LIS result entry and technician portal can show one row per test.
 */
class LabOrderLineItemSync
{
    public static function syncFromOrderJson(int $orderId, int $clinicId): void
    {
        if (!Schema::hasTable('lab_order_items') || !Schema::hasTable('lab_tests_catalog')) {
            Log::info('LabOrderLineItemSync: skip (missing lab_order_items or lab_tests_catalog)');

            return;
        }

        $orderFk = self::orderForeignKey();
        if (!$orderFk) {
            Log::warning('LabOrderLineItemSync: could not resolve order FK on lab_order_items');

            return;
        }

        if (DB::table('lab_order_items')->where($orderFk, $orderId)->exists()) {
            Log::info('LabOrderLineItemSync: items already exist', ['order_id' => $orderId]);

            return;
        }

        $order = DB::table('lab_orders')->where('id', $orderId)->where('clinic_id', $clinicId)->first();
        if (!$order || empty($order->tests)) {
            Log::warning('LabOrderLineItemSync: order or tests JSON missing', ['order_id' => $orderId]);

            return;
        }

        $tests = is_string($order->tests) ? json_decode($order->tests, true) : $order->tests;
        if (!is_array($tests) || $tests === []) {
            return;
        }

        $deptId = self::ensureDefaultDepartment($clinicId);
        if (!$deptId) {
            Log::error('LabOrderLineItemSync: no lab department');

            return;
        }

        $itemCols = array_flip(Schema::getColumnListing('lab_order_items'));
        $catCols = array_flip(Schema::getColumnListing('lab_tests_catalog'));

        $fkTest = isset($itemCols['test_id'])
            ? 'test_id'
            : (isset($itemCols['lab_test_catalog_id']) ? 'lab_test_catalog_id' : null);
        if (!$fkTest) {
            Log::warning('LabOrderLineItemSync: lab_order_items has no test_id / lab_test_catalog_id');

            return;
        }

        foreach ($tests as $t) {
            $code = (string) ($t['code'] ?? $t['test_code'] ?? '');
            $name = (string) ($t['name'] ?? $t['test_name'] ?? 'Laboratory test');
            $price = (float) ($t['price'] ?? 0);
            if ($code === '') {
                $code = 'AUTO-' . substr(md5($name . $orderId), 0, 10);
            }

            $testId = DB::table('lab_tests_catalog')
                ->where('clinic_id', $clinicId)
                ->where('test_code', $code)
                ->value('id');

            if (!$testId) {
                $catRow = [
                    'clinic_id' => $clinicId,
                    'department_id' => $deptId,
                    'test_code' => $code,
                    'test_name' => $name,
                    'test_type' => 'single',
                    'price' => $price,
                    'sample_type' => 'blood',
                    'is_active' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                if (isset($catCols['tat_hours'])) {
                    $catRow['tat_hours'] = 24;
                }
                $catRow = array_intersect_key($catRow, $catCols);
                $testId = (int) DB::table('lab_tests_catalog')->insertGetId($catRow);
                Log::info('LabOrderLineItemSync: created catalog test', ['test_id' => $testId, 'code' => $code]);
            }

            $row = [
                $orderFk => $orderId,
                $fkTest => $testId,
                'price' => $price,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if (isset($itemCols['discount'])) {
                $row['discount'] = 0;
            }
            $row = array_intersect_key($row, $itemCols);
            DB::table('lab_order_items')->insert($row);
        }

        Log::info('LabOrderLineItemSync: synced line items', ['order_id' => $orderId, 'count' => count($tests)]);
    }

    private static function orderForeignKey(): ?string
    {
        if (!Schema::hasTable('lab_order_items')) {
            return null;
        }
        $cols = Schema::getColumnListing('lab_order_items');
        if (in_array('lab_order_id', $cols, true)) {
            return 'lab_order_id';
        }
        if (in_array('order_id', $cols, true)) {
            return 'order_id';
        }

        return null;
    }

    private static function ensureDefaultDepartment(int $clinicId): ?int
    {
        if (!Schema::hasTable('lab_departments')) {
            return null;
        }
        $id = DB::table('lab_departments')->where('clinic_id', $clinicId)->orderBy('id')->value('id');
        if ($id) {
            return (int) $id;
        }

        return (int) DB::table('lab_departments')->insertGetId([
            'clinic_id' => $clinicId,
            'name' => 'General Laboratory',
            'code' => 'GEN',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
