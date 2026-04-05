<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Indian national medicine product rows (from medicines.json / MCP-compatible dataset).
 */
class MedicineCatalog extends Model
{
    protected $table = 'medicine_catalog';

    protected $fillable = [
        'name',
        'manufacturer',
        'composition',
        'mrp',
        'prescription_label',
        'rx_required',
        'source',
    ];

    protected $casts = [
        'mrp' => 'decimal:2',
        'rx_required' => 'boolean',
    ];

    public function pharmacyItems(): HasMany
    {
        return $this->hasMany(PharmacyItem::class, 'medicine_catalog_id');
    }

    /**
     * Search by brand/product name or composition (for autocomplete).
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, static>
     */
    public static function searchProducts(string $query, int $limit = 30)
    {
        $q = trim($query);
        Log::info('MedicineCatalog::searchProducts', ['q' => $q, 'limit' => $limit]);

        if ($q === '') {
            return collect();
        }

        $driver = Schema::getConnection()->getDriverName();

        // Short queries: prefix LIKE only (can use the left part of the name index); avoids '%q%' scans.
        if (mb_strlen($q) < 4) {
            $prefix = $q.'%';

            return static::query()
                ->where(function ($w) use ($prefix) {
                    $w->where('name', 'like', $prefix)
                        ->orWhere('manufacturer', 'like', $prefix);
                })
                ->orderBy('name')
                ->limit($limit)
                ->get();
        }

        // MySQL: FULLTEXT when index exists (see migration add_fulltext_index_to_medicine_catalog).
        if ($driver === 'mysql') {
            try {
                return static::query()
                    ->whereFullText(['name', 'manufacturer', 'composition'], $q)
                    ->orderBy('name')
                    ->limit($limit)
                    ->get();
            } catch (\Throwable $e) {
                Log::warning('MedicineCatalog::searchProducts fulltext failed, using LIKE fallback', [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $like = '%'.$q.'%';

        return static::query()
            ->where(function ($w) use ($like) {
                $w->where('name', 'like', $like)
                    ->orWhere('composition', 'like', $like)
                    ->orWhere('manufacturer', 'like', $like);
            })
            ->orderBy('name')
            ->limit($limit)
            ->get();
    }

    /**
     * Paginated browse for Pharmacy Inventory "National database" tab (same matching rules as autocomplete).
     */
    public static function queryForBrowse(?string $search): Builder
    {
        $q = trim((string) $search);
        Log::info('MedicineCatalog::queryForBrowse', ['q' => $q, 'len' => mb_strlen($q)]);

        $driver = Schema::getConnection()->getDriverName();
        $base = static::query();

        if ($q === '') {
            return $base->orderBy('id');
        }

        if (mb_strlen($q) < 4) {
            $prefix = $q.'%';

            return $base->where(function ($w) use ($prefix) {
                $w->where('name', 'like', $prefix)
                    ->orWhere('manufacturer', 'like', $prefix);
            })->orderBy('name');
        }

        if ($driver === 'mysql') {
            try {
                return $base->whereFullText(['name', 'manufacturer', 'composition'], $q)->orderBy('name');
            } catch (\Throwable $e) {
                Log::warning('MedicineCatalog::queryForBrowse fulltext failed, using LIKE fallback', [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $like = '%'.$q.'%';

        return $base->where(function ($w) use ($like) {
            $w->where('name', 'like', $like)
                ->orWhere('composition', 'like', $like)
                ->orWhere('manufacturer', 'like', $like);
        })->orderBy('name');
    }
}
