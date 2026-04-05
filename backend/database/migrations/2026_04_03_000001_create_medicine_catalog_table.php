<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

/**
 * National Indian medicine product catalog (same schema as INDIAN_MEDICINE_MCP_SERVER / medicines.json).
 * Populated via: php artisan indian-medicines:import /path/to/medicines.json
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('medicine_catalog')) {
            return;
        }

        Log::info('create_medicine_catalog_table: up');

        Schema::create('medicine_catalog', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('manufacturer', 191)->default('');
            $table->text('composition')->nullable();
            $table->decimal('mrp', 12, 2)->nullable();
            $table->string('prescription_label', 16)->nullable();
            $table->boolean('rx_required')->nullable();
            $table->string('source', 32)->default('indian_medicines_json');
            $table->timestamps();

            $table->unique(['name', 'manufacturer']);
            $table->index('manufacturer');
            $table->index('rx_required');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicine_catalog');
    }
};
