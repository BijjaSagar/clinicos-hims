<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Support\IpdSchema;

class IpdAdmission extends Model
{
    protected $table = 'ipd_admissions';

    protected $guarded = [];

    protected $casts = [
        'consultant_doctor_ids' => 'array',
        'icd_codes'             => 'array',
        'admitted_at'           => 'datetime',
        'discharge_date'        => 'datetime',
    ];

    /**
     * Some databases use admitted_at instead of admission_date — unify for Blade.
     */
    public function getAdmissionDateAttribute($value): ?\Carbon\Carbon
    {
        if ($value !== null) {
            return $this->asDateTime($value);
        }
        if (isset($this->attributes['admitted_at'])) {
            return $this->asDateTime($this->attributes['admitted_at']);
        }

        return null;
    }

    protected static function booted(): void
    {
        static::creating(function (IpdAdmission $admission) {
            Log::info('Creating IPD admission', [
                'clinic_id'  => $admission->clinic_id,
                'patient_id' => $admission->patient_id,
                'bed_id'     => $admission->bed_id,
            ]);
        });

        static::created(function (IpdAdmission $admission) {
            Log::info('IPD admission created', [
                'id'               => $admission->id,
                'admission_number' => $admission->admission_number,
            ]);
        });

        static::updating(function (IpdAdmission $admission) {
            if ($admission->isDirty('status')) {
                Log::info('IPD admission status changed', [
                    'id'         => $admission->id,
                    'old_status' => $admission->getOriginal('status'),
                    'new_status' => $admission->status,
                ]);
            }
        });
    }

    // ─── Relationships ───────────────────────────────────────────────────────

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function bed(): BelongsTo
    {
        return $this->belongsTo(Bed::class);
    }

    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class);
    }

    public function admittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admitted_by');
    }

    public function primaryDoctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'primary_doctor_id');
    }

    public function progressNotes(): HasMany
    {
        $fk = IpdSchema::admissionFkColumn('ipd_progress_notes');
        $q = $this->hasMany(IpdProgressNote::class, $fk);
        if (Schema::hasColumn('ipd_progress_notes', 'note_at')) {
            return $q->orderByDesc('note_at');
        }

        return $q->orderByDesc('note_date');
    }

    public function vitals(): HasMany
    {
        $fk = IpdSchema::admissionFkColumn('ipd_vitals');

        return $this->hasMany(IpdVital::class, $fk)->orderByDesc('recorded_at');
    }

    public function medicationOrders(): HasMany
    {
        $fk = IpdSchema::admissionFkColumn('ipd_medication_orders');

        return $this->hasMany(IpdMedicationOrder::class, $fk)->orderByDesc('created_at');
    }

    public function handoverNotes(): HasMany
    {
        $fk = IpdSchema::admissionFkColumn('ipd_handover_notes');

        return $this->hasMany(IpdHandoverNote::class, $fk)->orderByDesc('created_at');
    }

    public function carePlans(): HasMany
    {
        $fk = IpdSchema::admissionFkColumn('ipd_care_plans');

        return $this->hasMany(IpdCarePlan::class, $fk)->orderByDesc('updated_at');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'admission_id');
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'admitted');
    }

    public function scopeForClinic($query, int $clinicId)
    {
        return $query->where('clinic_id', $clinicId);
    }

    public function scopeDischargedToday($query)
    {
        return $query->where('status', 'discharged')
                     ->whereDate('discharge_date', now()->toDateString());
    }

    // ─── Accessors ───────────────────────────────────────────────────────────

    public function getDaysAdmittedAttribute(): int
    {
        $start = $this->admission_date ?? now();
        $end   = $this->discharge_date ?? now();

        return (int) $start->diffInDays($end);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status === 'admitted';
    }

    public function isDischarged(): bool
    {
        return $this->status === 'discharged';
    }
}
