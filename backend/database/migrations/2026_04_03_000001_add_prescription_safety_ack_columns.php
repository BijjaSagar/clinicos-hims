<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('prescriptions')) {
            Log::warning('add_prescription_safety_ack_columns: prescriptions table missing');

            return;
        }

        Schema::table('prescriptions', function (Blueprint $table) {
            if (! Schema::hasColumn('prescriptions', 'safety_acknowledged_at')) {
                $table->dateTime('safety_acknowledged_at')->nullable();
            }
            if (! Schema::hasColumn('prescriptions', 'safety_override_reason')) {
                $table->text('safety_override_reason')->nullable();
            }
            if (! Schema::hasColumn('prescriptions', 'safety_acknowledged_by')) {
                $table->foreignId('safety_acknowledged_by')->nullable()->constrained('users')->nullOnDelete();
            }
        });

        Log::info('prescriptions: safety acknowledgement columns ensured');
    }

    public function down(): void
    {
        if (! Schema::hasTable('prescriptions')) {
            return;
        }

        Schema::table('prescriptions', function (Blueprint $table) {
            if (Schema::hasColumn('prescriptions', 'safety_acknowledged_by')) {
                $table->dropConstrainedForeignId('safety_acknowledged_by');
            }
            if (Schema::hasColumn('prescriptions', 'safety_override_reason')) {
                $table->dropColumn('safety_override_reason');
            }
            if (Schema::hasColumn('prescriptions', 'safety_acknowledged_at')) {
                $table->dropColumn('safety_acknowledged_at');
            }
        });
    }
};
