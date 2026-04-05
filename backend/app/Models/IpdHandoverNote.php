<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IpdHandoverNote extends Model
{
    protected $table = 'ipd_handover_notes';

    protected $guarded = [];

    public function admission(): BelongsTo
    {
        return $this->belongsTo(IpdAdmission::class, 'admission_id');
    }

    public function handedOverBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handed_over_by');
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
