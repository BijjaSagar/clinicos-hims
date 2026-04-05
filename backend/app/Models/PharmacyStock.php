<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class PharmacyStock extends Model
{
    protected $table = 'pharmacy_stock';

    protected $fillable = [
        'clinic_id',
        'item_id',
        'batch_number',
        'expiry_date',
        'quantity_in',
        'quantity_out',
        'quantity_available',
        'purchase_rate',
        'mrp',
        'purchase_price',
        'selling_price',
        'supplier_id',
        'grn_id',
    ];

    protected $casts = [
        'quantity_in'        => 'integer',
        'quantity_out'       => 'integer',
        'quantity_available' => 'integer',
        'purchase_rate'      => 'decimal:2',
        'mrp'                => 'decimal:2',
    ];

    /**
     * expiry_date: avoid native 'date' cast — invalid / zero MySQL dates (0000-00-00) crash Carbon in production.
     */
    protected function expiryDate(): Attribute
    {
        return Attribute::make(
            get: function (?string $value) {
                if ($value === null || $value === '' || $value === '0000-00-00' || str_starts_with((string) $value, '0000-')) {
                    return null;
                }
                try {
                    return Carbon::parse($value)->startOfDay();
                } catch (\Throwable $e) {
                    Log::warning('PharmacyStock: invalid expiry_date from DB', [
                        'stock_id' => $this->id ?? null,
                        'value' => $value,
                        'error' => $e->getMessage(),
                    ]);

                    return null;
                }
            },
            set: function ($value) {
                if ($value === null || $value === '') {
                    return ['expiry_date' => null];
                }
                if ($value instanceof \DateTimeInterface) {
                    return ['expiry_date' => $value->format('Y-m-d')];
                }
                try {
                    return ['expiry_date' => Carbon::parse((string) $value)->format('Y-m-d')];
                } catch (\Throwable $e) {
                    Log::warning('PharmacyStock: invalid expiry_date set', ['value' => $value, 'error' => $e->getMessage()]);

                    return ['expiry_date' => null];
                }
            },
        );
    }

    // ── Relationships ────────────────────────────────────────────────────────

    public function item(): BelongsTo
    {
        return $this->belongsTo(PharmacyItem::class, 'item_id');
    }

    /** Alias for views/controllers that expect `pharmacyItem`. */
    public function pharmacyItem(): BelongsTo
    {
        return $this->belongsTo(PharmacyItem::class, 'item_id');
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    /**
     * Batches that have not yet expired.
     */
    public function scopeNonExpired($query)
    {
        return $query->where(function ($q) {
            $q->where('expiry_date', '>=', now()->toDateString())
                ->orWhereNull('expiry_date');
        });
    }

    /**
     * Batches expiring within the next 90 days.
     */
    public function scopeExpiringSoon($query)
    {
        return $query->where('expiry_date', '>=', now()->toDateString())
                     ->where('expiry_date', '<=', now()->addDays(90)->toDateString());
    }
}
