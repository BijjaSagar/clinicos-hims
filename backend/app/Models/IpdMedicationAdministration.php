<?php

namespace App\Models;

use App\Support\IpdSchema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class IpdMedicationAdministration extends Model
{
    protected $table = 'ipd_medication_administrations';

    protected $guarded = [];

    protected $casts = [
        'administered_at' => 'datetime',
        'not_administered' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (IpdMedicationAdministration $row) {
            $fk = IpdSchema::medicationOrderForeignKeyOnMar();
            Log::info('MAR record', [
                'order_fk' => $fk,
                'order_id' => $row->getAttribute($fk),
            ]);
        });
    }

    public function order(): BelongsTo
    {
        $fk = IpdSchema::medicationOrderForeignKeyOnMar();

        return $this->belongsTo(IpdMedicationOrder::class, $fk);
    }

    public function administeredBy(): BelongsTo
    {
        $fk = Schema::hasColumn('ipd_medication_administrations', 'recorded_by')
            ? 'recorded_by'
            : 'administered_by';

        return $this->belongsTo(User::class, $fk);
    }
}
