<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

class LabOrder extends Model
{
    protected $table = 'lab_orders';

    protected $fillable = [
        'clinic_id',
        'patient_id',
        'doctor_id',
        'visit_id',
        'vendor_id',
        'order_number',
        'is_urgent',
        'status',
        'result_pdf_url',
        'result_pdf_s3_key',
        'result_sent_at',
        'result_sent_to_patient',
        'fhir_resource_id',
        'total_amount',
        'clinical_notes',
        'department_id',
        'pathologist_id',
        'accession_number',
        'sample_collection_type',
        'collection_date',
        'collection_address',
    ];

    protected $casts = [
        'is_urgent' => 'boolean',
        'result_sent_at' => 'datetime',
        'result_sent_to_patient' => 'boolean',
        'total_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Convenience attributes for UI (mapped to Patient/Doctor relationships).
     * These ensure automatic decryption of PII via Eloquent casts.
     */
    public function getPatientNameAttribute()
    {
        return $this->patient?->name;
    }

    public function getDoctorNameAttribute()
    {
        return $this->doctor?->name;
    }

    public function getGenderAttribute()
    {
        return $this->patient?->sex;
    }

    public function getDateOfBirthAttribute()
    {
        return $this->patient?->dob;
    }

    public function getPatientPhoneAttribute()
    {
        return $this->patient?->phone;
    }

    public function getPhoneAttribute()
    {
        return $this->patient?->phone;
    }

    const STATUS_NEW = 'new';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_SAMPLE_COLLECTED = 'sample_collected';
    const STATUS_PROCESSING = 'processing';
    const STATUS_READY = 'ready';
    const STATUS_SENT = 'sent';
    const STATUS_CANCELLED = 'cancelled';

    protected static function booted(): void
    {
        static::creating(function (LabOrder $order) {
            Log::info('Creating lab order', [
                'clinic_id' => $order->clinic_id,
                'patient_id' => $order->patient_id,
                'vendor_id' => $order->vendor_id
            ]);

            // Auto-generate order number
            if (empty($order->order_number)) {
                $order->order_number = self::generateOrderNumber();
            }
        });

        static::created(function (LabOrder $order) {
            Log::info('Lab order created', ['id' => $order->id, 'order_number' => $order->order_number]);
        });

        static::updating(function (LabOrder $order) {
            if ($order->isDirty('status')) {
                Log::info('Lab order status changed', [
                    'id' => $order->id,
                    'old_status' => $order->getOriginal('status'),
                    'new_status' => $order->status
                ]);
            }
        });
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        // Try doctor_id first, then fallback to ordered_by or created_by
        $cols = \Illuminate\Support\Facades\Schema::getColumnListing('lab_orders');
        $fk = 'doctor_id';
        if (!in_array('doctor_id', $cols)) {
            if (in_array('ordered_by', $cols)) {
                $fk = 'ordered_by';
            } elseif (in_array('created_by', $cols)) {
                $fk = 'created_by';
            }
        }
        return $this->belongsTo(User::class, $fk);
    }

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(VendorLab::class, 'vendor_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(LabDepartment::class, 'department_id');
    }

    public function orderedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function pathologist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pathologist_id');
    }

    /**
     * Line items in lab_order_tests table.
     * Named labOrderTests — NOT "tests" — because lab_orders.tests is also a JSON column (integration orders).
     */
    public function labOrderTests(): HasMany
    {
        return $this->hasMany(LabOrderTest::class, 'lab_order_id');
    }

    /**
     * Line items in lab_order_items table (HIMS Lab management).
     */
    public function labOrderItems(): HasMany
    {
        // Try to resolve the foreign key (lab_order_id vs order_id)
        $fk = \Illuminate\Support\Facades\Schema::hasColumn('lab_order_items', 'lab_order_id') ? 'lab_order_id' : 'order_id';
        return $this->hasMany(\App\Models\LabOrderItem::class, $fk);
    }

    /**
     * Human-readable test list for EMR/UI (JSON integration payload or lab_order_tests rows).
     */
    public function getDisplayTestNamesAttribute(): string
    {
        try {
            // Priority 1: lab_order_items (HIMS Lab)
            $items = \Illuminate\Support\Facades\DB::table('lab_order_items')
                ->join('lab_tests_catalog', function($join) {
                    $itemCols = \Illuminate\Support\Facades\Schema::getColumnListing('lab_order_items');
                    $testFk = in_array('test_id', $itemCols) ? 'test_id' : 'lab_test_catalog_id';
                    $join->on("lab_order_items.{$testFk}", '=', 'lab_tests_catalog.id');
                })
                ->where(function($q) {
                    $itemCols = \Illuminate\Support\Facades\Schema::getColumnListing('lab_order_items');
                    $orderFk = in_array('lab_order_id', $itemCols) ? 'lab_order_id' : 'order_id';
                    $q->where("lab_order_items.{$orderFk}", $this->id);
                })
                ->pluck('lab_tests_catalog.test_name')
                ->filter()
                ->unique();

            if ($items->isNotEmpty()) {
                return $items->join(', ');
            }

            // Priority 2: lab_order_tests (Legacy EMR)
            if ($this->relationLoaded('labOrderTests') && $this->labOrderTests->isNotEmpty()) {
                $s = $this->labOrderTests->pluck('test_name')->filter()->join(', ');
                return $s !== '' ? $s : 'Lab Tests';
            }

            // Priority 3: JSON data
            $raw = $this->attributes['tests'] ?? null;
            if ($raw !== null && $raw !== '') {
                $d = is_string($raw) ? json_decode($raw, true) : $raw;
                if (is_array($d)) {
                    $line = collect($d)->map(function ($row) {
                        if (!is_array($row)) {
                            return '';
                        }

                        return $row['name'] ?? $row['test_name'] ?? $row['code'] ?? '';
                    })->filter()->unique()->join(', ');

                    return $line !== '' ? $line : 'Lab Tests';
                }
            }
        } catch (\Throwable $e) {
            Log::warning('LabOrder::display_test_names failed', ['id' => $this->id, 'error' => $e->getMessage()]);
        }

        return 'Lab Tests';
    }


    public function getTestsCountAttribute(): int
    {
        try {
            // Count from lab_order_items (HIMS)
            $itemsCount = $this->labOrderItems()->count();
            if ($itemsCount > 0) {
                return $itemsCount;
            }

            // Fallback: Check JSON data
            $raw = $this->attributes['tests'] ?? null;
            if ($raw !== null && $raw !== '') {
                $d = is_string($raw) ? json_decode($raw, true) : $raw;
                if (is_array($d)) {
                    return count($d);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('LabOrder::tests_count failed', ['id' => $this->id, 'error' => $e->getMessage()]);
        }

        return 0;
    }

    public function scopeForClinic($query, int $clinicId)
    {
        return $query->where('clinic_id', $clinicId);
    }

    public function scopeForPatient($query, int $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    public function scopePending($query)
    {
        return $query->whereNotIn('status', [self::STATUS_SENT, self::STATUS_CANCELLED]);
    }

    public function scopeReady($query)
    {
        return $query->where('status', self::STATUS_READY);
    }

    public function isReady(): bool
    {
        return $this->status === self::STATUS_READY;
    }

    public function hasResults(): bool
    {
        return !empty($this->result_pdf_url);
    }

    public function wasSentToPatient(): bool
    {
        return $this->result_sent_to_patient === true;
    }

    public static function generateOrderNumber(): string
    {
        $year = now()->year;
        $lastOrder = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = 1;
        if ($lastOrder && preg_match('/(\d+)$/', $lastOrder->order_number, $matches)) {
            $sequence = (int)$matches[1] + 1;
        }

        return sprintf('LO-%d-%06d', $year, $sequence);
    }
}
