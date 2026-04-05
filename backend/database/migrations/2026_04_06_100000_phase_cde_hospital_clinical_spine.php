<?php

/**
 * Phase C–E (HIMS_EXPANSION_PLAN): OPD department + ER triage, nursing handover/care plans,
 * foundation tables for MAR (administrations already exist on ipd_medication_administrations).
 */
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('appointments') && ! Schema::hasColumn('appointments', 'opd_department')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->string('opd_department', 120)->nullable()->after('specialty')
                    ->comment('OPD department / session label (Phase C)');
            });
            Log::info('phase_cde_spine: appointments.opd_department added');
        }

        if (! Schema::hasTable('emergency_visits')) {
            Schema::create('emergency_visits', function (Blueprint $table) {
                $table->id();
                $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
                $table->foreignId('patient_id')->nullable()->constrained()->nullOnDelete();
                $table->string('patient_name', 200)->nullable()->comment('For unknown / trauma');
                $table->string('phone', 30)->nullable();
                $table->unsignedTinyInteger('triage_level')->nullable()->comment('1–5 ESI-style');
                $table->string('chief_complaint', 500)->nullable();
                $table->string('bay_number', 40)->nullable();
                $table->enum('status', ['registered', 'triaged', 'in_treatment', 'discharged', 'admitted', 'left_ama'])
                    ->default('registered');
                $table->foreignId('ipd_admission_id')->nullable()->constrained('ipd_admissions')->nullOnDelete();
                $table->foreignId('registered_by')->constrained('users');
                $table->timestamp('registered_at')->useCurrent();
                $table->timestamp('discharged_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->index(['clinic_id', 'status']);
                $table->index(['clinic_id', 'registered_at']);
            });
            Log::info('phase_cde_spine: emergency_visits created');
        }

        if (! Schema::hasTable('ipd_handover_notes')) {
            Schema::create('ipd_handover_notes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
                $table->foreignId('admission_id')->constrained('ipd_admissions')->cascadeOnDelete();
                $table->string('shift', 20)->nullable()->comment('morning|evening|night');
                $table->text('summary');
                $table->text('concerns')->nullable();
                $table->foreignId('handed_over_by')->constrained('users');
                $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->index(['admission_id', 'created_at']);
            });
            Log::info('phase_cde_spine: ipd_handover_notes created');
        }

        if (! Schema::hasTable('ipd_care_plans')) {
            Schema::create('ipd_care_plans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
                $table->foreignId('admission_id')->constrained('ipd_admissions')->cascadeOnDelete();
                $table->string('goal', 500);
                $table->text('interventions')->nullable();
                $table->text('outcome_review')->nullable();
                $table->foreignId('updated_by')->constrained('users');
                $table->timestamps();
                $table->index('admission_id');
            });
            Log::info('phase_cde_spine: ipd_care_plans created');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ipd_care_plans');
        Schema::dropIfExists('ipd_handover_notes');
        Schema::dropIfExists('emergency_visits');

        if (Schema::hasTable('appointments') && Schema::hasColumn('appointments', 'opd_department')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->dropColumn('opd_department');
            });
        }
    }
};
