<?php

namespace App\Models;

use App\Support\IpdSchema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IpdMedicationOrder extends Model
{
    protected $table = 'ipd_medication_orders';

    protected $guarded = [];

    protected $casts = [
        'start_date'    => 'date',
        'end_date'      => 'date',
        'is_prn'        => 'boolean',
        'is_stat'       => 'boolean',
        'is_active'     => 'boolean',
    ];

    // ─── Relationships ───────────────────────────────────────────────────────

    public function admission(): BelongsTo
    {
        $fk = IpdSchema::admissionFkColumn('ipd_medication_orders');

        return $this->belongsTo(IpdAdmission::class, $fk);
    }

    public function prescribedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prescribed_by');
    }

    /** @deprecated use prescribedBy — column is prescribed_by */
    public function orderedBy(): BelongsTo
    {
        return $this->prescribedBy();
    }

    public function administrations(): HasMany
    {
        $fk = IpdSchema::medicationOrderForeignKeyOnMar();

        return $this->hasMany(IpdMedicationAdministration::class, $fk);
    }

    /**
     * Legacy DBs use `dose`; migrations use `dosage`.
     */
    public function getDosageAttribute($value): ?string
    {
        if ($value !== null && $value !== '') {
            return $value;
        }

        return $this->attributes['dose'] ?? null;
    }
}
