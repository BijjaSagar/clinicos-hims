<?php
/**
 * ClinicOS DB Diagnostic — DELETE AFTER USE
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "<h2>ClinicOS DB Diagnostics</h2><pre>\n";

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Laravel: ✅ Booted (" . app()->version() . ")\n";
echo "PHP: " . PHP_VERSION . "\n\n";

echo "=== DATABASE TABLES ===\n";
$tables = [
    'hospital_settings', 'hospital_wards', 'hospital_rooms', 'hospital_beds', 'beds',
    'lab_orders', 'lab_order_items', 'lab_order_tests', 'lab_tests_catalog', 'lab_samples', 'lab_results',
    'pharmacy_dispensing', 'pharmacy_dispensing_items', 'pharmacy_items', 'pharmacy_stock',
    'pharmacy_categories', 'pharmacy_suppliers', 'pharmacy_purchases',
    'ipd_admissions', 'ipd_vitals', 'ipd_progress_notes', 'ipd_medication_orders',
    'vendor_labs', 'vendor_lab_tests',
    'visits', 'patients', 'users', 'clinics', 'appointments', 'invoices',
    'audit_logs', 'dental_teeth', 'visit_lesions', 'visit_procedures',
];

foreach ($tables as $table) {
    try {
        $exists = \Illuminate\Support\Facades\Schema::hasTable($table);
        if ($exists) {
            $count = \Illuminate\Support\Facades\DB::table($table)->count();
            echo "  ✅ {$table} ({$count} rows)\n";
        } else {
            echo "  ❌ {$table} — MISSING\n";
        }
    } catch (\Throwable $e) {
        echo "  ⚠️  {$table} — " . $e->getMessage() . "\n";
    }
}

echo "\n=== KEY COLUMN CHECKS ===\n";
$checks = [
    'hospital_beds' => ['id','clinic_id','room_id','bed_code','status'],
    'pharmacy_dispensing' => ['total','subtotal','discount','total_amount','discount_amount','gst_amount','payment_mode'],
    'pharmacy_dispensing_items' => ['selling_price','unit_price','total','gst_amount'],
    'ipd_vitals' => ['ipd_admission_id','admission_id'],
    'ipd_progress_notes' => ['ipd_admission_id','admission_id','author_id','recorded_by'],
    'ipd_medication_orders' => ['ipd_admission_id','admission_id','ordered_by','prescribed_by','is_prn','is_sos'],
    'lab_results' => ['lab_order_item_id','order_item_id'],
    'lab_samples' => ['lab_order_id','order_id'],
    'lab_order_items' => ['lab_order_id','order_id','lab_test_catalog_id','price','status','result_value'],
    'lab_order_tests' => ['lab_order_id','test_catalog_id','test_name','unit_price'],
    'audit_logs' => ['model_type','model_id','entity_type','entity_id'],
];
foreach ($checks as $t => $cols) {
    if (!\Illuminate\Support\Facades\Schema::hasTable($t)) { echo "  {$t}: table missing\n"; continue; }
    echo "  {$t}:\n";
    foreach ($cols as $c) {
        $has = \Illuminate\Support\Facades\Schema::hasColumn($t, $c);
        echo "    {$c}: " . ($has ? '✅' : '❌') . "\n";
    }
}

echo "\n=== USER ROLES ===\n";
$roles = \Illuminate\Support\Facades\DB::table('users')
    ->select('role', \Illuminate\Support\Facades\DB::raw('COUNT(*) as cnt'))
    ->groupBy('role')->get();
foreach ($roles as $r) echo "  {$r->role}: {$r->cnt}\n";

echo "\n=== CLINIC HIMS FEATURES ===\n";
$clinics = \Illuminate\Support\Facades\DB::table('clinics')->select('id','name','facility_type','hims_features')->get();
foreach ($clinics as $c) {
    echo "  [{$c->id}] {$c->name} (type={$c->facility_type})\n";
    echo "    hims_features: " . ($c->hims_features ?: 'NULL') . "\n";
}

echo "\n</pre><p style='color:red;font-weight:bold'>⚠ DELETE THIS FILE!</p>";
