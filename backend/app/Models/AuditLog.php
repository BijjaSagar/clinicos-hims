<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'clinic_id', 'user_id', 'action',
        'entity_type', 'entity_id',
        'old_values', 'new_values',
        'ip_address', 'user_agent',
        'user_name', 'user_role', 'description',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Static helper to log an audit event.
     */
    public static function log(
        string $action,
        string $description,
        ?string $entityType = null,
        ?int $entityId = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): self {
        $user = auth()->user();

        return self::create([
            'clinic_id'   => $user?->clinic_id ?? 0,
            'user_id'     => $user?->id,
            'action'      => $action,
            'entity_type' => $entityType,
            'entity_id'   => $entityId,
            'old_values'  => $oldValues,
            'new_values'  => $newValues,
            'ip_address'  => request()?->ip(),
            'user_agent'  => request()?->userAgent(),
        ]);
    }
}
