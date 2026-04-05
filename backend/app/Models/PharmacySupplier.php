<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PharmacySupplier extends Model
{
    protected $table = 'pharmacy_suppliers';

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function purchases(): HasMany
    {
        return $this->hasMany(PharmacyPurchase::class, 'supplier_id');
    }
}
