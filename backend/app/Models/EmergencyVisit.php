<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class EmergencyVisit extends Model
{
    protected $table = 'emergency_visits';

    protected $guarded = [];

    protected $casts = [
        'registered_at' => 'datetime',
        'discharged_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (EmergencyVisit $row) {
            Log::info('EmergencyVisit creating', ['clinic_id' => $row->clinic_id, 'patient_id' => $row->patient_id]);
        });
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    public function admission(): BelongsTo
    {
        return $this->belongsTo(IpdAdmission::class, 'ipd_admission_id');
    }
}
