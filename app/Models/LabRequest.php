<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabRequest extends Model
{
    protected $fillable = [
        'patient_id',
        'consultation_id',
        'requested_by',
        'lab_test_name',
        'lab_test_description',
        'status',
        'requested_date',
        'completed_date',
        'results',
        'notes',
    ];

    protected $casts = [
        'requested_date' => 'date',
        'completed_date' => 'date',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(HealthWorker::class, 'requested_by');
    }
}
