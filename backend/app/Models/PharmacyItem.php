<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class PharmacyItem extends Model
{
    protected $table = 'pharmacy_items';

    protected $fillable = [
        'clinic_id',
        'name',
        'generic_name',
        'drug_id',
        'medicine_catalog_id',
        'category_id',
        'category',
        'hsn_code',
        'unit',
        'pack_size',
        'manufacturer',
        'schedule',
        'is_controlled',
        'gst_rate',
        'mrp',
        'selling_price',
        'reorder_level',
        'reorder_qty',
        'storage_conditions',
        'is_active',
    ];

    protected $casts = [
        'is_controlled'  => 'boolean',
        'is_active'      => 'boolean',
        'gst_rate'       => 'decimal:2',
        'mrp'            => 'decimal:2',
        'selling_price'  => 'decimal:2',
    ];

    // ── Relationships ────────────────────────────────────────────────────────

    public function medicineCatalog(): BelongsTo
    {
        return $this->belongsTo(MedicineCatalog::class, 'medicine_catalog_id');
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(PharmacyStock::class, 'item_id');
    }

    /**
     * Correlated subquery: total on-hand quantity for each pharmacy_items row.
     * Caches the SQL fragment for one request — production DBs may lack quantity_available.
     */
    public static function sqlTotalStockSubqueryForPharmacyItems(): string
    {
        static $cached = null;
        if ($cached !== null) {
            return $cached;
        }

        $t = 'pharmacy_stock';
        if (! Schema::hasTable($t)) {
            Log::info('PharmacyItem::sqlTotalStockSubqueryForPharmacyItems: pharmacy_stock missing, using 0');

            return $cached = '0';
        }

        if (Schema::hasColumn($t, 'quantity_available')) {
            $sum = 'COALESCE(SUM(quantity_available), 0)';
        } elseif (Schema::hasColumn($t, 'quantity_in')) {
            $outExpr = Schema::hasColumn($t, 'quantity_out')
                ? 'COALESCE(quantity_out, 0)'
                : '0';
            $sum = "COALESCE(SUM(quantity_in - {$outExpr}), 0)";
            Log::info('PharmacyItem::sqlTotalStockSubqueryForPharmacyItems: using quantity_in/quantity_out (no quantity_available)');
        } elseif (Schema::hasColumn($t, 'quantity')) {
            $sum = 'COALESCE(SUM(quantity), 0)';
            Log::info('PharmacyItem::sqlTotalStockSubqueryForPharmacyItems: using legacy quantity column');
        } else {
            Log::warning('PharmacyItem::sqlTotalStockSubqueryForPharmacyItems: no known qty columns on pharmacy_stock');

            return $cached = '0';
        }

        return $cached = "(SELECT {$sum} FROM {$t} WHERE {$t}.item_id = pharmacy_items.id)";
    }

    /**
     * Restrict a pharmacy_stock relation query to batches with sellable quantity (schema-aware).
     */
    public static function constrainStockQueryToPositiveQty($stockQuery): void
    {
        $t = 'pharmacy_stock';
        if (! Schema::hasTable($t)) {
            return;
        }
        if (Schema::hasColumn($t, 'quantity_available')) {
            $stockQuery->where($t.'.quantity_available', '>', 0);
        } elseif (Schema::hasColumn($t, 'quantity_in')) {
            $out = Schema::hasColumn($t, 'quantity_out') ? 'COALESCE('.$t.'.quantity_out, 0)' : '0';
            $stockQuery->whereRaw("({$t}.quantity_in - {$out}) > 0");
        } elseif (Schema::hasColumn($t, 'quantity')) {
            $stockQuery->where($t.'.quantity', '>', 0);
        }
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        if (Schema::hasColumn('pharmacy_items', 'is_active')) {
            return $query->where('is_active', true);
        }

        Log::info('PharmacyItem@scopeActive: is_active column missing, skipping filter');

        return $query;
    }

    /**
     * Items whose current stock is at or below their reorder level.
     */
    public function scopeLowStock($query)
    {
        $sub = self::sqlTotalStockSubqueryForPharmacyItems();
        $level = Schema::hasColumn('pharmacy_items', 'reorder_level')
            ? 'COALESCE(pharmacy_items.reorder_level, 0)'
            : '0';

        return $query->whereRaw("{$sub} <= {$level}");
    }

    // ── Accessors ────────────────────────────────────────────────────────────

    /**
     * Real-time stock quantity derived from pharmacy_stock.
     */
    public function getStockQuantityAttribute(): int
    {
        $t = 'pharmacy_stock';
        if (! Schema::hasTable($t)) {
            return 0;
        }

        $q = DB::table($t)->where('item_id', $this->id);

        if (Schema::hasColumn($t, 'quantity_available')) {
            return (int) $q->sum('quantity_available');
        }

        if (Schema::hasColumn($t, 'quantity_in')) {
            if (Schema::hasColumn($t, 'quantity_out')) {
                return (int) $q->selectRaw('COALESCE(SUM(quantity_in - COALESCE(quantity_out, 0)), 0) as s')->value('s');
            }

            return (int) $q->sum('quantity_in');
        }

        if (Schema::hasColumn($t, 'quantity')) {
            return (int) $q->sum('quantity');
        }

        Log::warning('PharmacyItem@getStockQuantityAttribute: no qty column on pharmacy_stock', ['item_id' => $this->id]);

        return 0;
    }

    // Compatibility: views use current_stock
    public function getCurrentStockAttribute(): int
    {
        return $this->stock_quantity;
    }

    /** Blade / reports use price_per_unit — map from selling_price or MRP */
    public function getPricePerUnitAttribute(): float
    {
        return (float) ($this->selling_price ?? $this->mrp ?? 0);
    }
}
