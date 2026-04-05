<?php

namespace App\Models;

use App\Support\IpdSchema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class IpdProgressNote extends Model
{
    protected $table = 'ipd_progress_notes';

    protected $guarded = [];

    protected $casts = [
        'note_date' => 'date',
        'note_at' => 'datetime',
    ];

    // ─── Relationships ───────────────────────────────────────────────────────

    public function admission(): BelongsTo
    {
        $fk = IpdSchema::admissionFkColumn('ipd_progress_notes');

        return $this->belongsTo(IpdAdmission::class, $fk);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * SOAP fields from migration columns, or JSON stored in `body` (legacy schema).
     *
     * @return array{note_type?: string, subjective?: string, objective?: string, assessment?: string, plan?: string, notes?: string|null}
     */
    public function soapPayload(): array
    {
        if (Schema::hasColumn('ipd_progress_notes', 'subjective')) {
            return [
                'note_type' => $this->note_type ?? 'note',
                'subjective' => $this->subjective ?? '',
                'objective' => $this->objective ?? '',
                'assessment' => $this->assessment ?? '',
                'plan' => $this->plan ?? '',
                'notes' => $this->notes ?? null,
            ];
        }

        $raw = $this->attributes['body'] ?? '';
        if ($raw === '' || $raw === null) {
            return [];
        }
        $j = json_decode((string) $raw, true);

        return is_array($j) ? $j : [];
    }
}
