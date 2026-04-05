<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IpdCarePlan extends Model
{
    protected $table = 'ipd_care_plans';

    protected $guarded = [];

    public function admission(): BelongsTo
    {
        return $this->belongsTo(IpdAdmission::class, 'admission_id');
    }
}
