<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

/**
 * In-house LIS: optional accession + sample collection audit on lab_orders.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('lab_orders')) {
            Log::info('lab_orders_accession_tracking: lab_orders missing, skip');

            return;
        }

        Schema::table('lab_orders', function (Blueprint $table) {
            if (! Schema::hasColumn('lab_orders', 'accession_number')) {
                $table->string('accession_number', 40)->nullable()->after('order_number');
                Log::info('lab_orders_accession_tracking: accession_number added');
            }
            if (! Schema::hasColumn('lab_orders', 'sample_collected_at')) {
                $table->timestamp('sample_collected_at')->nullable();
            }
            if (! Schema::hasColumn('lab_orders', 'collected_by')) {
                $table->foreignId('collected_by')->nullable()->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('lab_orders')) {
            return;
        }

        Schema::table('lab_orders', function (Blueprint $table) {
            if (Schema::hasColumn('lab_orders', 'collected_by')) {
                $table->dropForeign(['collected_by']);
            }
        });
        Schema::table('lab_orders', function (Blueprint $table) {
            foreach (['accession_number', 'sample_collected_at', 'collected_by'] as $col) {
                if (Schema::hasColumn('lab_orders', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
