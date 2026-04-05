<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('invoices')) {
            Log::info('add_admission_id_to_invoices: invoices missing, skip');

            return;
        }

        if (Schema::hasColumn('invoices', 'admission_id')) {
            Log::info('add_admission_id_to_invoices: column exists, skip');

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
            Log::info('add_admission_id_to_invoices: FK to ipd_admissions applied');
        } else {
            Log::warning('add_admission_id_to_invoices: ipd_admissions missing, no FK');
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('invoices') || !Schema::hasColumn('invoices', 'admission_id')) {
            return;
        }

        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasTable('ipd_admissions')) {
                try {
                    $table->dropForeign(['admission_id']);
                } catch (\Throwable $e) {
                    Log::warning('add_admission_id_to_invoices rollback dropForeign', ['error' => $e->getMessage()]);
                }
            }
            $table->dropColumn('admission_id');
        });
    }
};
