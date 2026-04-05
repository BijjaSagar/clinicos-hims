<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Registration used plan=trial + missing specialties — MySQL enum rejected 'trial'
 * and specialties JSON was NOT NULL with no default. Widen plan + allow null specialties.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('clinics')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            try {
                DB::statement("ALTER TABLE clinics MODIFY COLUMN plan VARCHAR(32) NOT NULL DEFAULT 'trial'");
            } catch (\Throwable $e) {
                Log::warning('Migration alter clinics.plan skipped or failed', ['error' => $e->getMessage()]);
            }
            try {
                DB::statement('ALTER TABLE clinics MODIFY COLUMN specialties JSON NULL');
            } catch (\Throwable $e) {
                Log::warning('Migration alter clinics.specialties nullable skipped', ['error' => $e->getMessage()]);
            }
        }
    }

    public function down(): void
    {
        // Intentionally empty — reverting enum is environment-specific
    }
};
