<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class PharmacyPurchase extends Model
{
    protected $table = 'pharmacy_purchases';

    protected $guarded = [];

    protected $casts = [
        'received_date' => 'date',
        'invoice_date'  => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (PharmacyPurchase $p) {
            Log::info('PharmacyPurchase creating', [
                'clinic_id' => $p->clinic_id,
                'purchase_number' => $p->purchase_number,
            ]);
        });
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(PharmacySupplier::class, 'supplier_id');
    }

    public function receivedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
