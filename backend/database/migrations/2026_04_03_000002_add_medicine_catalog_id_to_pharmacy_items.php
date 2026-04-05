<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pharmacy_items')) {
            Log::warning('add_medicine_catalog_id_to_pharmacy_items: pharmacy_items missing, skip');

            return;
        }

        Schema::table('pharmacy_items', function (Blueprint $table) {
            if (! Schema::hasColumn('pharmacy_items', 'medicine_catalog_id')) {
                $table->foreignId('medicine_catalog_id')
                    ->nullable()
                    ->constrained('medicine_catalog')
                    ->nullOnDelete();
                Log::info('add_medicine_catalog_id_to_pharmacy_items: column added');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('pharmacy_items')) {
            return;
        }
        Schema::table('pharmacy_items', function (Blueprint $table) {
            if (Schema::hasColumn('pharmacy_items', 'medicine_catalog_id')) {
                $table->dropForeign(['medicine_catalog_id']);
                $table->dropColumn('medicine_catalog_id');
            }
        });
    }
};
