<?php

namespace App\Support;

use App\Models\IpdAdmission;
use App\Models\IpdMedicationOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

/**
 * Resolves IPD table column names across Laravel migrations vs legacy SQL dumps
 * (e.g. ipd_admission_id vs admission_id, dose vs dosage, temp_c vs temperature).
 */
final class IpdSchema
{
    public static function admissionFkColumn(string $table): string
    {
        if (Schema::hasColumn($table, 'admission_id')) {
            return 'admission_id';
        }
        if (Schema::hasColumn($table, 'ipd_admission_id')) {
            return 'ipd_admission_id';
        }

        return 'admission_id';
    }

    public static function medicationOrderForeignKeyOnMar(): string
    {
        if (Schema::hasColumn('ipd_medication_administrations', 'ipd_medication_order_id')) {
            return 'ipd_medication_order_id';
        }

        return 'order_id';
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public static function filterToTable(string $table, array $payload): array
    {
        $allowed = array_flip(Schema::getColumnListing($table));

        return array_intersect_key($payload, $allowed);
    }

    /**
     * @param  array<string, mixed>  $validated  From storeMedicationOrder validation
     * @return array<string, mixed>
     */
    public static function mapMedicationOrderInsert(int $clinicId, int $admissionId, int $prescribedById, array $validated, bool $isSos): array
    {
        $table = 'ipd_medication_orders';
        $payload = [
            'clinic_id' => $clinicId,
            'admission_id' => $admissionId,
            'ipd_admission_id' => $admissionId,
            'prescribed_by' => $prescribedById,
            'drug_name' => $validated['drug_name'],
            'route' => $validated['route'],
            'dosage' => $validated['dosage'],
            'dose' => $validated['dosage'],
            'frequency' => $validated['frequency'],
            'start_date' => $validated['start_date'],
            'started_on' => $validated['start_date'],
            'end_date' => $validated['end_date'] ?? null,
            'ended_on' => $validated['end_date'] ?? null,
            'instructions' => $validated['instructions'] ?? null,
            'is_sos' => $isSos,
            'status' => 'active',
        ];

        $fk = self::admissionFkColumn($table);
        if ($fk === 'ipd_admission_id') {
            unset($payload['admission_id']);
        } else {
            unset($payload['ipd_admission_id']);
        }

        $cols = Schema::getColumnListing($table);
        if (in_array('dosage', $cols, true) && ! in_array('dose', $cols, true)) {
            unset($payload['dose']);
        }
        if (in_array('dose', $cols, true) && ! in_array('dosage', $cols, true)) {
            unset($payload['dosage']);
        }
        if (in_array('start_date', $cols, true) && ! in_array('started_on', $cols, true)) {
            unset($payload['started_on']);
        }
        if (in_array('started_on', $cols, true) && ! in_array('start_date', $cols, true)) {
            unset($payload['start_date']);
        }
        if (in_array('end_date', $cols, true) && ! in_array('ended_on', $cols, true)) {
            unset($payload['ended_on']);
        }
        if (in_array('ended_on', $cols, true) && ! in_array('end_date', $cols, true)) {
            unset($payload['end_date']);
        }
        if (! in_array('is_sos', $cols, true)) {
            unset($payload['is_sos']);
        }

        return self::filterToTable($table, $payload);
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    public static function mapVitalsInsert(int $clinicId, int $admissionId, int $recordedById, array $validated): array
    {
        $table = 'ipd_vitals';
        $payload = [
            'clinic_id' => $clinicId,
            'admission_id' => $admissionId,
            'recorded_by' => $recordedById,
            'recorded_at' => now(),
            'temperature' => $validated['temperature'] ?? null,
            'temp_c' => $validated['temperature'] ?? null,
            'pulse' => $validated['pulse'] ?? null,
            'bp_systolic' => $validated['bp_systolic'] ?? null,
            'bp_diastolic' => $validated['bp_diastolic'] ?? null,
            'respiratory_rate' => $validated['respiratory_rate'] ?? null,
            'rr' => $validated['respiratory_rate'] ?? null,
            'spo2' => $validated['spo2'] ?? null,
            'pain_score' => $validated['pain_score'] ?? null,
            'gcs' => $validated['gcs'] ?? null,
            'gcs_score' => $validated['gcs'] ?? null,
            'weight' => $validated['weight'] ?? null,
            'weight_kg' => $validated['weight'] ?? null,
            'height' => $validated['height'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ];

        $cols = Schema::getColumnListing($table);
        if (in_array('temp_c', $cols, true) && ! in_array('temperature', $cols, true)) {
            unset($payload['temperature']);
        }
        if (in_array('temperature', $cols, true) && ! in_array('temp_c', $cols, true)) {
            unset($payload['temp_c']);
        }
        if (in_array('rr', $cols, true) && ! in_array('respiratory_rate', $cols, true)) {
            unset($payload['respiratory_rate']);
        }
        if (in_array('respiratory_rate', $cols, true) && ! in_array('rr', $cols, true)) {
            unset($payload['rr']);
        }
        if (in_array('weight_kg', $cols, true) && ! in_array('weight', $cols, true)) {
            unset($payload['weight']);
        }
        if (in_array('weight', $cols, true) && ! in_array('weight_kg', $cols, true)) {
            unset($payload['weight_kg']);
        }
        if (in_array('gcs_score', $cols, true) && ! in_array('gcs', $cols, true)) {
            unset($payload['gcs']);
        }
        if (in_array('gcs', $cols, true) && ! in_array('gcs_score', $cols, true)) {
            unset($payload['gcs_score']);
        }
        if (! in_array('pain_score', $cols, true)) {
            unset($payload['pain_score']);
        }
        if (! in_array('height', $cols, true)) {
            unset($payload['height']);
        }

        return self::filterToTable($table, $payload);
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    public static function mapProgressNoteInsert(int $clinicId, int $admissionId, int $authorId, array $validated): array
    {
        $table = 'ipd_progress_notes';
        $noteAt = \Carbon\Carbon::parse($validated['note_date'].' '.$validated['note_time']);

        $soap = [
            'note_type' => $validated['note_type'],
            'subjective' => $validated['subjective'],
            'objective' => $validated['objective'],
            'assessment' => $validated['assessment'],
            'plan' => $validated['plan'],
            'notes' => $validated['notes'] ?? null,
        ];

        $payload = [
            'clinic_id' => $clinicId,
            'admission_id' => $admissionId,
            'author_id' => $authorId,
            'note_type' => $validated['note_type'],
            'note_date' => $validated['note_date'],
            'note_time' => $validated['note_time'],
            'subjective' => $validated['subjective'],
            'objective' => $validated['objective'],
            'assessment' => $validated['assessment'],
            'plan' => $validated['plan'],
            'notes' => $validated['notes'] ?? null,
            'body' => json_encode($soap, JSON_UNESCAPED_UNICODE),
            'note_at' => $noteAt,
        ];

        $cols = Schema::getColumnListing($table);
        if (in_array('subjective', $cols, true)) {
            unset($payload['body'], $payload['note_at']);
        } else {
            unset(
                $payload['note_type'],
                $payload['note_date'],
                $payload['note_time'],
                $payload['subjective'],
                $payload['objective'],
                $payload['assessment'],
                $payload['plan'],
                $payload['notes']
            );
        }

        return self::filterToTable($table, $payload);
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    public static function mapMarInsert(IpdMedicationOrder $order, IpdAdmission $admission, array $validated, bool $notAdministered): array
    {
        $table = 'ipd_medication_administrations';
        $fk = self::medicationOrderForeignKeyOnMar();
        $fkVal = $order->id;

        $notes = trim(($validated['dose_given'] ?? '').' '.($validated['notes'] ?? ''));
        if ($notAdministered && ! empty($validated['not_administered_reason'] ?? null)) {
            $notes .= ' | Not given: '.$validated['not_administered_reason'];
        }

        $payload = [
            'clinic_id' => $admission->clinic_id,
            'admission_id' => $admission->id,
            'order_id' => $order->id,
            'ipd_medication_order_id' => $order->id,
            'administered_by' => Auth::id(),
            'recorded_by' => Auth::id(),
            'administered_at' => $validated['administered_at'],
            'dose_given' => $validated['dose_given'],
            'route_used' => $validated['route_used'] ?? null,
            'notes' => $notes !== '' ? $notes : null,
            'not_administered' => $notAdministered,
            'not_administered_reason' => $validated['not_administered_reason'] ?? null,
            'status' => $notAdministered ? 'not_given' : 'given',
        ];

        if ($fk === 'ipd_medication_order_id') {
            unset($payload['order_id']);
        } else {
            unset($payload['ipd_medication_order_id']);
        }

        $cols = Schema::getColumnListing($table);
        if (! in_array('clinic_id', $cols, true)) {
            unset($payload['clinic_id']);
        }
        if (! in_array('admission_id', $cols, true)) {
            unset($payload['admission_id']);
        }
        if (! in_array('administered_by', $cols, true)) {
            unset($payload['administered_by']);
        }
        if (! in_array('dose_given', $cols, true)) {
            unset($payload['dose_given']);
        }
        if (! in_array('route_used', $cols, true)) {
            unset($payload['route_used']);
        }
        if (! in_array('not_administered', $cols, true)) {
            unset($payload['not_administered'], $payload['not_administered_reason']);
        }

        return self::filterToTable($table, $payload);
    }
}
