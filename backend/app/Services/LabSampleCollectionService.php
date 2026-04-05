<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Shared accession / sample collection for in-house LIS (doctor + lab tech routes).
 */
class LabSampleCollectionService
{
    /**
     * Mark order as sample collected; optionally insert lab_samples row when schema matches.
     *
     * @param  array{sample_type: string, collection_notes?: string|null}  $validated
     */
    public function collectForOrder(int $orderId, int $clinicId, array $validated): void
    {
        Log::info('LabSampleCollectionService@collectForOrder', [
            'order_id' => $orderId,
            'clinic_id' => $clinicId,
            'sample_type' => $validated['sample_type'] ?? null,
        ]);

        $accession = 'ACC-'.$orderId.'-'.strtoupper(substr(sha1((string) microtime(true)), 0, 8));

        DB::transaction(function () use ($orderId, $clinicId, $validated, $accession) {
            if (Schema::hasTable('lab_samples')) {
                $row = $this->buildSampleInsertRow($orderId, $clinicId, $validated, $accession);
                if ($row !== null) {
                    DB::table('lab_samples')->insert($row);
                    Log::info('LabSampleCollectionService inserted lab_samples', ['keys' => array_keys($row)]);
                } else {
                    Log::warning('LabSampleCollectionService skipped lab_samples insert (schema mismatch)');
                }
            }

            $update = ['updated_at' => now()];
            if (Schema::hasColumn('lab_orders', 'status')) {
                $update['status'] = 'sample_collected';
            }
            if (Schema::hasColumn('lab_orders', 'sample_collected_at')) {
                $update['sample_collected_at'] = now();
            }
            if (Schema::hasColumn('lab_orders', 'collected_by')) {
                $update['collected_by'] = auth()->id();
            }
            if (Schema::hasColumn('lab_orders', 'accession_number')) {
                $update['accession_number'] = $accession;
            }

            DB::table('lab_orders')->where('id', $orderId)
                ->where('clinic_id', $clinicId)
                ->update($update);

            Log::info('LabSampleCollectionService lab_orders updated', ['order_id' => $orderId, 'keys' => array_keys($update)]);
        });
    }

    /**
     * @param  array{sample_type: string, collection_notes?: string|null}  $validated
     * @return array<string, mixed>|null
     */
    private function buildSampleInsertRow(int $orderId, int $clinicId, array $validated, string $accession): ?array
    {
        $cols = array_flip(Schema::getColumnListing('lab_samples'));
        $row = [];

        if (isset($cols['order_id'])) {
            $row['order_id'] = $orderId;
        }
        if (isset($cols['clinic_id'])) {
            $row['clinic_id'] = $clinicId;
        }
        if (isset($cols['sample_type'])) {
            $row['sample_type'] = $validated['sample_type'];
        }
        if (isset($cols['barcode'])) {
            $row['barcode'] = $accession;
        }
        if (isset($cols['sample_id'])) {
            $row['sample_id'] = 'SMP-'.strtoupper(uniqid());
        }
        if (isset($cols['collected_by'])) {
            $row['collected_by'] = auth()->id();
        }
        if (isset($cols['collected_at'])) {
            $row['collected_at'] = now();
        }
        if (isset($cols['status'])) {
            $row['status'] = 'collected';
        }
        if (isset($cols['notes'])) {
            $row['notes'] = $validated['collection_notes'] ?? null;
        }
        if (isset($cols['created_at'])) {
            $row['created_at'] = now();
        }
        if (isset($cols['updated_at'])) {
            $row['updated_at'] = now();
        }

        if (isset($cols['item_id'])) {
            $fk = $this->labOrderItemsOrderFk();
            if ($fk) {
                $first = DB::table('lab_order_items')->where($fk, $orderId)->orderBy('id')->first();
                $row['item_id'] = $first->id ?? null;
            }
            if (empty($row['item_id'])) {
                Log::warning('LabSampleCollectionService: lab_samples needs item_id but no line item found');

                return null;
            }
        }

        foreach (['order_id', 'clinic_id', 'sample_type'] as $c) {
            if (isset($cols[$c]) && empty($row[$c])) {
                return null;
            }
        }
        if (isset($cols['barcode']) && empty($row['barcode']) && ! isset($cols['sample_id'])) {
            return null;
        }
        if (isset($cols['sample_id']) && empty($row['sample_id']) && ! isset($cols['barcode'])) {
            return null;
        }

        return array_intersect_key($row, $cols);
    }

    private function labOrderItemsOrderFk(): ?string
    {
        if (! Schema::hasTable('lab_order_items')) {
            return null;
        }
        $cols = Schema::getColumnListing('lab_order_items');
        if (in_array('lab_order_id', $cols, true)) {
            return 'lab_order_id';
        }
        if (in_array('order_id', $cols, true)) {
            return 'order_id';
        }

        return null;
    }
}
