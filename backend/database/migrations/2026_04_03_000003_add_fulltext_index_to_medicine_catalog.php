<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Large medicine_catalog imports (~250k rows) make COUNT(*) and LIKE '%q%' very slow.
 * FULLTEXT (MySQL) speeds up catalog autocomplete; other drivers skip this migration.
 */
return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver !== 'mysql') {
            Log::info('add_fulltext_index_to_medicine_catalog: skipped (driver='.$driver.')');

            return;
        }

        if (! Schema::hasTable('medicine_catalog')) {
            return;
        }

        try {
            Schema::table('medicine_catalog', function (Blueprint $table) {
                $table->fullText(['name', 'manufacturer', 'composition']);
            });
            Log::info('add_fulltext_index_to_medicine_catalog: fulltext index created');
        } catch (\Throwable $e) {
            Log::warning('add_fulltext_index_to_medicine_catalog: could not add fulltext (may already exist)', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        if (! Schema::hasTable('medicine_catalog')) {
            return;
        }

        try {
            Schema::table('medicine_catalog', function (Blueprint $table) {
                $table->dropFullText(['name', 'manufacturer', 'composition']);
            });
        } catch (\Throwable $e) {
            Log::warning('add_fulltext_index_to_medicine_catalog down: drop failed', ['error' => $e->getMessage()]);
        }
    }
};
