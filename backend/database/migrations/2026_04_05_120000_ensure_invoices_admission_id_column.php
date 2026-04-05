<?php

/**
 * Repair / ensure migration: run when an older migration was missing on deploy or
 * "Nothing to migrate" but invoices.admission_id is still absent.
 *
 * @see database/migrations/2026_04_03_120000_add_admission_id_to_invoices.php
 */
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('invoices')) {
            Log::info('ensure_invoices_admission_id: invoices missing, skip');

            return;
        }

        if (Schema::hasColumn('invoices', 'admission_id')) {
            Log::info('ensure_invoices_admission_id: column already present, skip');

            return;
        }

        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('admission_id')->nullable()->after('visit_id');
            $table->index('admission_id');
        });

        if (Schema::hasTable('ipd_admissions')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->foreign('admission_id')
                    ->references('id')
                    ->on('ipd_admissions')
                    ->nullOnDelete();
            });
            Log::info('ensure_invoices_admission_id: column + FK applied');
        } else {
            Log::warning('ensure_invoices_admission_id: ipd_admissions missing, no FK');
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('invoices') || ! Schema::hasColumn('invoices', 'admission_id')) {
            return;
        }

        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasTable('ipd_admissions')) {
                try {
                    $table->dropForeign(['admission_id']);
                } catch (\Throwable $e) {
                    Log::warning('ensure_invoices_admission_id rollback dropForeign', ['error' => $e->getMessage()]);
                }
            }
            $table->dropColumn('admission_id');
        });
    }
};
