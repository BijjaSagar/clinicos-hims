<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('lab_order_items')) {
            return;
        }

        Schema::table('lab_order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('lab_order_items', 'result_value')) {
                $table->string('result_value', 255)->nullable();
            }
            if (!Schema::hasColumn('lab_order_items', 'is_abnormal')) {
                $table->boolean('is_abnormal')->default(false);
            }
            if (!Schema::hasColumn('lab_order_items', 'is_critical')) {
                $table->boolean('is_critical')->default(false);
            }
            if (!Schema::hasColumn('lab_order_items', 'remarks')) {
                $table->text('remarks')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('lab_order_items')) {
            return;
        }

        Schema::table('lab_order_items', function (Blueprint $table) {
            foreach (['result_value', 'is_abnormal', 'is_critical', 'remarks'] as $col) {
                if (Schema::hasColumn('lab_order_items', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
