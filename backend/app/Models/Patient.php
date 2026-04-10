<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\HasApiTokens;

class Patient extends Model
{
    use HasFactory, SoftDeletes, HasApiTokens;

    protected $table = 'patients';

    protected $fillable = [
        'clinic_id',
        'uhid',
        'name',
        'dob',
        'age_years',
        'sex',
        'blood_group',
        'phone',
        'phone_alt',
        'email',
        'address',
        'aadhaar',
        'abha_id',
        'abha_address',
        'abha_verified',
        'abdm_consent_active',
        'salutation',
        'first_name',
        'middle_name',
        'last_name',
        'mlc_id',
        'mlc_type',
        'known_allergies',
        'chronic_conditions',
        'current_medications',
        'family_history',
        'referred_by',
        'source',
        'visit_count',
        'last_visit_date',
        'next_followup_date',
        'photo_consent_given',
        'photo_consent_at',
        'photo_consent_signature_path',
        'name_bindex',
        'phone_bindex',
        'email_bindex',
        'abha_bindex',
    ];

    protected $casts = [
        'dob' => 'date',
        'name' => 'encrypted',
        'first_name' => 'encrypted',
        'middle_name' => 'encrypted',
        'last_name' => 'encrypted',
        'phone' => 'encrypted',
        'phone_alt' => 'encrypted',
        'email' => 'encrypted',
        'aadhaar' => 'encrypted',
        'address' => 'encrypted',
        'city' => 'string',
        'state' => 'string',
        'abha_id' => 'encrypted',
        'abha_address' => 'encrypted',
        'family_history' => 'encrypted',
        'known_allergies' => 'array',
        'chronic_conditions' => 'array',
        'current_medications' => 'array',
        'abha_verified' => 'boolean',
        'abdm_consent_active' => 'boolean',
        'photo_consent_given' => 'boolean',
        'photo_consent_at' => 'datetime',
        'last_visit_date' => 'date',
        'next_followup_date' => 'date',
        'visit_count' => 'integer',
        'age_years' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (Patient $patient) {
            // Auto-generate UHID if not provided
            if (!$patient->uhid) {
                $patient->uhid = self::generateUhid($patient->clinic_id);
            }
        });

        static::saving(function (Patient $patient) {
            // Reconstruct full name if any component changes to keep legacy field in sync
            if ($patient->isDirty(['salutation', 'first_name', 'middle_name', 'last_name'])) {
                $patient->name = trim(($patient->salutation ?? '') . ' ' . 
                                      $patient->first_name . ' ' . 
                                      ($patient->middle_name ?? '') . ' ' . 
                                      ($patient->last_name ?? ''));
            }

            $salt = config('app.key');
            
            // Normalize and hash PII for search (Blind Indexing)
            if ($patient->isDirty('name')) {
                $patient->name_bindex = self::generateBlindIndex($patient->name, $salt, 'name');
            }

            if ($patient->isDirty('phone')) {
                $patient->phone_bindex = self::generateBlindIndex($patient->phone, $salt, 'phone');
            }

            if ($patient->isDirty('email')) {
                $patient->email_bindex = self::generateBlindIndex($patient->email, $salt, 'email');
            }

            if ($patient->isDirty('abha_id')) {
                $patient->abha_bindex = self::generateBlindIndex($patient->abha_id, $salt, 'abha');
            }
        });
    }

    /**
     * Generate a unique UHID for the patient based on clinic ID and sequence.
     * Format: UHID-{CLINIC_ID}-{YY}-{SEQUENCE} (e.g., UHID-1-26-00001)
     */
    public static function generateUhid(int $clinicId): string
    {
        $yearCode = now()->format('y'); // 26 for 2026
        $prefix = "UHID-{$clinicId}-{$yearCode}";
        
        // Find the last patient with this prefix
        $lastPatient = self::where('clinic_id', $clinicId)
            ->where('uhid', 'like', "{$prefix}-%")
            ->orderBy('id', 'desc')
            ->first();

        $sequence = 1;
        if ($lastPatient && preg_match('/-(\d+)$/', $lastPatient->uhid, $matches)) {
            $sequence = (int)$matches[1] + 1;
        }

        return sprintf('%s-%05d', $prefix, $sequence);
    }

    /**
     * Standardized normalization and hashing for blind indexing.
     */
    public static function generateBlindIndex(?string $value, string $salt, string $type): ?string
    {
        if (empty($value)) {
            return null;
        }

        $normalized = match ($type) {
            'phone' => preg_replace('/[^0-9]/', '', (string)$value),
            'email', 'name' => strtolower(trim((string)$value)),
            'abha' => strtoupper(trim((string)$value)),
            default => trim((string)$value)
        };

        // If it's a phone number, we also strip the country code if it's 12 digits (assuming India +91)
        if ($type === 'phone' && strlen($normalized) === 12 && str_starts_with($normalized, '91')) {
            $normalized = substr($normalized, 2);
        }

        return hash_hmac('sha256', $normalized, $salt);
    }

    /**
     * Scope to search by name, phone, email or ABHA ID using blind indexes,
     * and by UHID using plain-text matching.
     */
    public function scopeSearchByToken($query, ?string $term = null)
    {
        if (empty($term)) {
            return $query;
        }

        $salt = config('app.key');
        
        return $query->where(function ($q) use ($term, $salt) {
            $q->where('name_bindex', self::generateBlindIndex($term, $salt, 'name'))
              ->orWhere('email_bindex', self::generateBlindIndex($term, $salt, 'email'))
              ->orWhere('abha_bindex', self::generateBlindIndex($term, $salt, 'abha'))
              ->orWhere('uhid', 'like', "%{$term}%"); // Plain-text UHID search
            
            $phoneToken = self::generateBlindIndex($term, $salt, 'phone');
            if ($phoneToken) {
                $q->orWhere('phone_bindex', $phoneToken);
            }
        });
    }

    // ─── Relationships ───────────────────────────────────────────────────────

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function familyMembers(): HasMany
    {
        return $this->hasMany(PatientFamilyMember::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(PatientPhoto::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function dentalTeeth(): HasMany
    {
        return $this->hasMany(DentalTooth::class);
    }

    public function physioTreatmentPlans(): HasMany
    {
        return $this->hasMany(PhysioTreatmentPlan::class);
    }

    public function labOrders(): HasMany
    {
        return $this->hasMany(LabOrder::class);
    }

    public function abdmConsents(): HasMany
    {
        return $this->hasMany(AbdmConsent::class);
    }

    public function whatsappMessages(): HasMany
    {
        return $this->hasMany(WhatsappMessage::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeForClinic($query, int $clinicId)
    {
        return $query->where('clinic_id', $clinicId);
    }

    public function scopeWithAbha($query)
    {
        return $query->whereNotNull('abha_id');
    }

    public function scopeSearchByName($query, string $name)
    {
        return $query->where('name', 'like', "%{$name}%");
    }

    public function scopeSearchByPhone($query, string $phone)
    {
        return $query->where('phone', 'like', "%{$phone}%");
    }

    public function scopeNeedingFollowup($query)
    {
        return $query->whereNotNull('next_followup_date')
                     ->where('next_followup_date', '<=', now()->addDays(7));
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    // ─── Compatibility Accessors ─────────────────────────────────────────────
    // These map old column name conventions to actual DB columns (dob, sex, name)

    public function getFullNameAttribute(): string
    {
        return $this->name ?? '';
    }

    public function getDateOfBirthAttribute(): mixed
    {
        return $this->dob;
    }

    public function getGenderAttribute(): ?string
    {
        return $this->sex;
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function getAge(): ?int
    {
        if ($this->dob) {
            return $this->dob->age;
        }
        return $this->age_years;
    }

    /**
     * Override getAttribute to gracefully handle plain-text data during encryption transition.
     * This prevents DecryptException from crashing the app when existing plain text is read.
     */
    public function getAttribute($key)
    {
        try {
            return parent::getAttribute($key);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            // Decryption fallback for access via $model->attribute
            Log::debug('Patient decryption fallback used (getAttribute)', ['id' => $this->id, 'field' => $key]);
            return $this->attributes[$key] ?? null;
        }
    }

    protected function castAttribute($key, $value)
    {
        try {
            return parent::castAttribute($key, $value);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            // Decryption fallback for toArray() / json_encode()
            Log::debug('Patient decryption fallback used (castAttribute)', ['id' => $this->id, 'field' => $key]);
            return $value;
        }
    }

    public function hasAbha(): bool
    {
        return !empty($this->abha_id);
    }

    public function hasPhotoConsent(): bool
    {
        return $this->photo_consent_given === true;
    }

    public function getAllergiesString(): string
    {
        return implode(', ', $this->allergiesDisplayListForUi());
    }

    /**
     * Normalised allergy tokens (lowercase, deduped) for CDS + prescription safety matching.
     * Merges canonical known_allergies with optional legacy allergy_notes text if present.
     *
     * @return array<int, string>
     */
    public function allergiesListForClinicalChecks(): array
    {
        $seen = [];
        foreach ($this->known_allergies ?? [] as $item) {
            $s = strtolower(trim((string) $item));
            if ($s !== '') {
                $seen[$s] = true;
            }
        }
        foreach ($this->tokensFromAllergyNotesAttribute() as $t) {
            $s = strtolower(trim($t));
            if ($s !== '') {
                $seen[$s] = true;
            }
        }

        $keys = array_keys($seen);
        Log::info('Patient::allergiesListForClinicalChecks', [
            'patient_id' => $this->id,
            'token_count' => count($keys),
        ]);

        return $keys;
    }

    /**
     * Human-readable allergy list for EMR UI (deduped by normalised token).
     *
     * @return array<int, string>
     */
    public function allergiesDisplayListForUi(): array
    {
        $seen = [];
        $out = [];
        foreach ($this->known_allergies ?? [] as $item) {
            $s = strtolower(trim((string) $item));
            if ($s === '' || isset($seen[$s])) {
                continue;
            }
            $seen[$s] = true;
            $out[] = trim((string) $item);
        }
        foreach ($this->tokensFromAllergyNotesAttribute() as $t) {
            $s = strtolower(trim($t));
            if ($s === '' || isset($seen[$s])) {
                continue;
            }
            $seen[$s] = true;
            $out[] = $t;
        }

        return $out;
    }

    /**
     * @return array<int, string>
     */
    private function tokensFromAllergyNotesAttribute(): array
    {
        if (! Schema::hasColumn($this->getTable(), 'allergy_notes')) {
            return [];
        }

        $raw = $this->attributes['allergy_notes'] ?? null;
        if ($raw === null || $raw === '') {
            return [];
        }

        $text = is_string($raw) ? $raw : (string) $raw;
        $parts = preg_split('/[,;\n\r]+/', $text) ?: [];

        return array_values(array_filter(array_map(static fn ($p) => trim((string) $p), $parts)));
    }

    public function getConditionsString(): string
    {
        return implode(', ', $this->chronic_conditions ?? []);
    }

    /**
     * Human-readable current medications for profile / print (avoids array in Blade {{ }}).
     */
    public function getCurrentMedicationsString(): string
    {
        $m = $this->current_medications;
        if (is_array($m)) {
            $line = implode(', ', array_values(array_filter(array_map(static fn ($x) => trim((string) $x), $m), static fn ($x) => $x !== '')));
            Log::debug('Patient::getCurrentMedicationsString', ['patient_id' => $this->id, 'len' => strlen($line)]);

            return $line;
        }

        return is_string($m) ? trim($m) : '';
    }

    public function incrementVisitCount(): void
    {
        $this->increment('visit_count');
        $this->update(['last_visit_date' => now()->toDateString()]);
        Log::info('Patient visit count incremented', ['patient_id' => $this->id, 'new_count' => $this->visit_count]);
    }
}
