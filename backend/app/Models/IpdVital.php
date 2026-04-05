<?php

namespace App\Models;

use App\Support\IpdSchema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IpdVital extends Model
{
    protected $table = 'ipd_vitals';

    protected $guarded = [];

    protected $casts = [
        'recorded_at' => 'datetime',
        'temperature' => 'float',
        'temp_c' => 'float',
        'pulse' => 'integer',
        'bp_systolic' => 'integer',
        'bp_diastolic' => 'integer',
        'respiratory_rate' => 'integer',
        'spo2' => 'float',
        'pain_score' => 'integer',
        'gcs' => 'integer',
        'gcs_score' => 'integer',
        'weight' => 'float',
        'weight_kg' => 'float',
        'height' => 'float',
    ];

    // ─── Relationships ───────────────────────────────────────────────────────

    public function admission(): BelongsTo
    {
        $fk = IpdSchema::admissionFkColumn('ipd_vitals');

        return $this->belongsTo(IpdAdmission::class, $fk);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function getTemperatureDisplayAttribute(): ?float
    {
        if (array_key_exists('temperature', $this->attributes) && $this->attributes['temperature'] !== null) {
            return (float) $this->attributes['temperature'];
        }
        if (array_key_exists('temp_c', $this->attributes) && $this->attributes['temp_c'] !== null) {
            return (float) $this->attributes['temp_c'];
        }

        return null;
    }

    public function getRespiratoryRateDisplayAttribute(): ?int
    {
        if (array_key_exists('respiratory_rate', $this->attributes) && $this->attributes['respiratory_rate'] !== null) {
            return (int) $this->attributes['respiratory_rate'];
        }
        if (array_key_exists('rr', $this->attributes) && $this->attributes['rr'] !== null) {
            return (int) $this->attributes['rr'];
        }

        return null;
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function isTempAbnormal(): bool
    {
        $t = $this->temperature_display;

        return $t !== null && ($t < 36.0 || $t > 37.5);
    }

    public function isPulseAbnormal(): bool
    {
        return $this->pulse !== null && ($this->pulse < 60 || $this->pulse > 100);
    }

    public function isBpAbnormal(): bool
    {
        return ($this->bp_systolic !== null && ($this->bp_systolic < 90 || $this->bp_systolic > 140))
            || ($this->bp_diastolic !== null && ($this->bp_diastolic < 60 || $this->bp_diastolic > 90));
    }

    public function isSpo2Abnormal(): bool
    {
        return $this->spo2 !== null && $this->spo2 < 95;
    }
}
