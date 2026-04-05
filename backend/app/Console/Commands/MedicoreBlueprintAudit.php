<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Maps MediCoreOS / HIMS blueprint expectations to actual DB tables (development compass).
 *
 * @see docs/MEDICORE_BLUEPRINT_DEVELOPMENT.md
 */
class MedicoreBlueprintAudit extends Command
{
    protected $signature = 'medicore:blueprint-audit {--json : Output machine-readable JSON}';

    protected $description = 'Audit DB foundations against MediCoreOS + HIMS expansion blueprint';

    public function handle(): int
    {
        Log::info('MedicoreBlueprintAudit: started', ['json' => (bool) $this->option('json')]);

        try {
            $checks = $this->runSchemaChecks();
        } catch (\Throwable $e) {
            Log::warning('MedicoreBlueprintAudit: database unavailable', ['error' => $e->getMessage()]);
            $this->error('Database unavailable — cannot audit schema. Fix DB connection in .env then re-run.');
            $this->line('  '.$e->getMessage());
            $this->newLine();
            $this->comment('Read: docs/MEDICORE_BLUEPRINT_DEVELOPMENT.md');

            return self::FAILURE;
        }

        $nextFocus = $this->suggestNextPhase($checks);

        if ($this->option('json')) {
            $this->line(json_encode([
                'checks' => $checks,
                'suggested_next_phase' => $nextFocus,
                'remediation' => $this->remediationHints($checks),
                'doc' => 'docs/MEDICORE_BLUEPRINT_DEVELOPMENT.md',
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            Log::info('MedicoreBlueprintAudit: json output', ['suggested_next_phase' => $nextFocus]);

            return self::SUCCESS;
        }

        $this->info('MediCoreOS / HIMS blueprint audit (database presence)');
        $this->newLine();

        foreach ($checks as $name => $ok) {
            $this->line(sprintf('  [%s] %s', $ok ? '✓' : '·', $name));
        }

        $this->newLine();
        $this->comment('Suggested engineering focus: '.$nextFocus);
        $this->line('  See: docs/MEDICORE_BLUEPRINT_DEVELOPMENT.md · docs/HIMS_EXPANSION_PLAN.md');

        $remediation = $this->remediationHints($checks);
        if ($remediation !== []) {
            $this->newLine();
            $this->warn('Remediation (copy to server / run as needed):');
            foreach ($remediation as $line) {
                $this->line('  '.$line);
            }
        }

        Log::info('MedicoreBlueprintAudit: completed', [
            'suggested_next_phase' => $nextFocus,
            'passed' => count(array_filter($checks)),
            'total' => count($checks),
            'remediation' => $remediation,
        ]);

        return self::SUCCESS;
    }

    /**
     * @param  array<string, bool>  $checks
     * @return string[]
     */
    private function remediationHints(array $checks): array
    {
        $hints = [];

        if (!$checks['invoices.admission_id']) {
            $hints[] = 'Deploy migrations from repo (at least): 2026_04_03_120000_add_admission_id_to_invoices.php';
            $hints[] = 'If migrate says "Nothing to migrate" but column is still missing, deploy: 2026_04_05_120000_ensure_invoices_admission_id_column.php (repair / idempotent).';
            $hints[] = 'Then: php artisan migrate --force --no-interaction';
            $hints[] = 'Diagnose: mysql -e "SHOW COLUMNS FROM invoices LIKE \'admission_id\';" AND SELECT migration FROM migrations WHERE migration LIKE \'%admission%\';';
        }

        if (!$checks['razorpay_webhook_events']) {
            $hints[] = 'Run full migrations or migrate production_hardening migration for razorpay_webhook_events.';
        }

        return $hints;
    }

    /**
     * Ward/bed master: repo uses `hospital_wards` + `hospital_beds` (HIMS UI); legacy migrations may use `wards` + `beds`.
     */
    private function hasWardBedMaster(): bool
    {
        $modern = Schema::hasTable('hospital_wards') && Schema::hasTable('hospital_beds');
        $legacy = Schema::hasTable('wards') && Schema::hasTable('beds');

        return $modern || $legacy;
    }

    /**
     * @return array<string, bool>
     */
    private function runSchemaChecks(): array
    {
        return [
            'clinics' => Schema::hasTable('clinics'),
            'clinics.hims_foundation' => Schema::hasTable('clinics')
                && Schema::hasColumn('clinics', 'facility_type')
                && Schema::hasColumn('clinics', 'hims_features'),
            'patients' => Schema::hasTable('patients'),
            'appointments' => Schema::hasTable('appointments'),
            'visits' => Schema::hasTable('visits'),
            'invoices' => Schema::hasTable('invoices'),
            'invoices.admission_id' => Schema::hasTable('invoices') && Schema::hasColumn('invoices', 'admission_id'),
            'payments' => Schema::hasTable('payments'),
            'razorpay_webhook_events' => Schema::hasTable('razorpay_webhook_events'),
            'ward_bed_master (hospital_* or wards/beds)' => $this->hasWardBedMaster(),
            'wards (legacy table name)' => Schema::hasTable('wards'),
            'beds (legacy table name)' => Schema::hasTable('beds'),
            'hospital_wards' => Schema::hasTable('hospital_wards'),
            'hospital_beds' => Schema::hasTable('hospital_beds'),
            'ipd_admissions' => Schema::hasTable('ipd_admissions'),
            'ipd_vitals' => Schema::hasTable('ipd_vitals'),
            'ipd_progress_notes' => Schema::hasTable('ipd_progress_notes'),
            'pharmacy_items' => Schema::hasTable('pharmacy_items'),
            'lab_orders' => Schema::hasTable('lab_orders'),
            'indian_drugs' => Schema::hasTable('indian_drugs'),
        ];
    }

    /**
     * @param  array<string, bool>  $checks
     */
    private function suggestNextPhase(array $checks): string
    {
        if (! ($checks['ward_bed_master (hospital_* or wards/beds)'] ?? false)) {
            return 'Phase A — ward/bed master (HIMS_EXPANSION_PLAN §8 Phase A)';
        }
        if (! $checks['ipd_admissions']) {
            return 'Phase B — IPD admissions ADT (HIMS §8 Phase B)';
        }
        if (! ($checks['ipd_vitals'] && $checks['ipd_progress_notes'])) {
            return 'Phase B — IPD vitals + progress notes CRUD';
        }
        if (! $checks['invoices.admission_id']) {
            return 'Phase G — link billing to IPD/OPD encounters (migrate invoices.admission_id)';
        }
        if (! $checks['razorpay_webhook_events']) {
            return 'Production hardening — run migrations for razorpay_webhook_events';
        }

        return 'Phase C–E — hospital OPD tokens, pharmacy PO/GRN depth, LIS accessioning (per HIMS_EXPANSION_PLAN)';
    }
}
