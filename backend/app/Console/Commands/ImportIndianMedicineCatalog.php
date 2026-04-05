<?php

namespace App\Console\Commands;

use App\Models\MedicineCatalog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Imports medicines.json into medicine_catalog (schema aligned with INDIAN_MEDICINE_MCP_SERVER).
 *
 * @see https://github.com/nowitsidb/INDIAN_MEDICINE_MCP_SERVER
 */
class ImportIndianMedicineCatalog extends Command
{
    protected $signature = 'indian-medicines:import
                            {path? : Absolute path to medicines.json (array of {Name, Manufacturer, Composition, MRP, Prescription})}
                            {--chunk=500 : Batch size for upsert}
                            {--truncate : Delete all rows in medicine_catalog before import (use only if no pharmacy links or after backup)}';

    protected $description = 'Import Indian medicine product catalog from JSON (MCP-compatible medicines.json)';

    public function handle(): int
    {
        if (! Schema::hasTable('medicine_catalog')) {
            $this->error('Table medicine_catalog does not exist. Run: php artisan migrate');

            return self::FAILURE;
        }

        $path = $this->argument('path') ?: (string) config('indian_medicines.json_path', '');
        if ($path === '') {
            $this->error('Provide a path: php artisan indian-medicines:import /path/to/medicines.json');
            $this->line('Or set INDIAN_MEDICINES_JSON_PATH in .env');

            return self::FAILURE;
        }

        if (! is_readable($path)) {
            $this->error('Cannot read this path: '.$path);
            Log::warning('ImportIndianMedicineCatalog: unreadable path', ['path' => $path]);
            $this->newLine();
            $this->warn('/full/path/to/medicines.json was only an example in the docs � it is not a real file.');
            $this->line('Do this instead:');
            $this->line('  1. Get the actual <fg=cyan>medicines.json</> file (same format as INDIAN_MEDICINE_MCP_SERVER � often supplied separately from the GitHub repo).');
            $this->line('  2. Upload it to your server, e.g. <fg=cyan>'.storage_path('app/medicines.json').'</>');
            $this->line('  3. Run: <fg=cyan>php artisan indian-medicines:import '.storage_path('app/medicines.json').'</>');
            $this->line('  Or set <fg=cyan>INDIAN_MEDICINES_JSON_PATH</> in <fg=cyan>.env</> to the absolute path, then: <fg=cyan>php artisan indian-medicines:import</>');

            return self::FAILURE;
        }

        Log::info('ImportIndianMedicineCatalog: start', ['path' => $path, 'truncate' => $this->option('truncate')]);

        if ($this->option('truncate')) {
            if (! $this->confirm('This deletes all medicine_catalog rows. Continue?', false)) {
                return self::FAILURE;
            }
            $deleted = MedicineCatalog::query()->delete();
            $this->info('Deleted '.$deleted.' existing catalog rows.');
            Log::info('ImportIndianMedicineCatalog: truncated', ['deleted' => $deleted]);
        }

        $raw = file_get_contents($path);
        if ($raw === false) {
            $this->error('Could not read file.');

            return self::FAILURE;
        }

        $data = json_decode($raw, true);
        if (! is_array($data)) {
            $this->error('JSON must be an array of medicine objects.');

            return self::FAILURE;
        }

        $chunkSize = max(50, min(2000, (int) $this->option('chunk')));
        $total = count($data);
        $this->info('Parsed '.$total.' records. Importing in chunks of '.$chunkSize.'�');

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $imported = 0;
        $skipped = 0;

        foreach (array_chunk($data, $chunkSize) as $chunk) {
            $rows = [];
            foreach ($chunk as $row) {
                if (! is_array($row)) {
                    $skipped++;
                    continue;
                }
                $mapped = $this->mapRow($row);
                if ($mapped === null) {
                    $skipped++;
                    continue;
                }
                $rows[] = $mapped;
            }

            if ($rows === []) {
                continue;
            }

            try {
                DB::table('medicine_catalog')->upsert(
                    $rows,
                    ['name', 'manufacturer'],
                    [
                        'composition',
                        'mrp',
                        'prescription_label',
                        'rx_required',
                        'source',
                        'updated_at',
                    ]
                );
                $imported += count($rows);
            } catch (\Throwable $e) {
                Log::error('ImportIndianMedicineCatalog: upsert failed', ['error' => $e->getMessage()]);
                $this->newLine();
                $this->error($e->getMessage());

                return self::FAILURE;
            }

            $bar->advance(count($chunk));
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('Upserted ~'.$imported.' rows. Skipped invalid: '.$skipped.'.');
        Log::info('ImportIndianMedicineCatalog: done', ['imported' => $imported, 'skipped' => $skipped]);

        return self::SUCCESS;
    }

    /**
     * Supports:
     * - MCP / medicines.json: Name, Manufacturer, Composition, MRP, Prescription
     * - Indian-Medicine-Dataset medicine.json: name, manufacturer_name, price(?), short_composition1/2
     *
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>|null
     */
    private function mapRow(array $row): ?array
    {
        $name = $this->firstNonEmptyString($row, ['Name', 'name', 'NAME', 'product_name']);
        if ($name === '') {
            return null;
        }

        $manufacturer = $this->firstNonEmptyString($row, ['Manufacturer', 'manufacturer_name', 'manufacturer', 'Manufacturer_name']);
        $composition = $this->buildComposition($row);

        $mrp = $this->parseMrpFromRow($row);

        $prescriptionLabel = $this->firstNonEmptyString($row, ['Prescription', 'prescription', 'Prescription_required']);
        if ($prescriptionLabel === '') {
            $prescriptionLabel = null;
        }

        $rx = null;
        if ($prescriptionLabel !== null) {
            $lower = strtolower($prescriptionLabel);
            if (str_contains($lower, 'yes')) {
                $rx = true;
            } elseif (str_contains($lower, 'no')) {
                $rx = false;
            }
        }

        $source = isset($row['Name']) ? 'indian_medicines_json' : 'indian_medicine_dataset';

        $now = now();

        return [
            'name' => mb_substr($name, 0, 255),
            'manufacturer' => mb_substr($manufacturer, 0, 191),
            'composition' => $composition,
            'mrp' => $mrp,
            'prescription_label' => $prescriptionLabel ? mb_substr($prescriptionLabel, 0, 16) : null,
            'rx_required' => $rx,
            'source' => $source,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function firstNonEmptyString(array $row, array $keys): string
    {
        foreach ($keys as $k) {
            if (! array_key_exists($k, $row)) {
                continue;
            }
            $v = $row[$k];
            if ($v === null || $v === '') {
                continue;
            }
            $s = trim((string) $v);
            if ($s !== '') {
                return $s;
            }
        }

        return '';
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function buildComposition(?array $row): ?string
    {
        if ($row === null) {
            return null;
        }
        if (! empty($row['Composition'])) {
            return (string) $row['Composition'];
        }

        $p1 = isset($row['short_composition1']) ? trim((string) $row['short_composition1']) : '';
        $p2 = isset($row['short_composition2']) ? trim((string) $row['short_composition2']) : '';
        if ($p1 === '' && $p2 === '') {
            return null;
        }
        if ($p2 === '') {
            return $p1;
        }
        if ($p1 === '') {
            return $p2;
        }

        return $p1.' + '.$p2;
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function parseMrpFromRow(array $row): ?float
    {
        if (isset($row['MRP']) && $row['MRP'] !== '' && $row['MRP'] !== null && is_numeric($row['MRP'])) {
            return round((float) $row['MRP'], 2);
        }
        if (isset($row['mrp']) && $row['mrp'] !== '' && is_numeric($row['mrp'])) {
            return round((float) $row['mrp'], 2);
        }

        foreach ($row as $key => $value) {
            if (! is_string($key)) {
                continue;
            }
            if (str_starts_with($key, 'price') && $value !== '' && $value !== null && is_numeric($value)) {
                return round((float) $value, 2);
            }
        }

        return null;
    }
}
