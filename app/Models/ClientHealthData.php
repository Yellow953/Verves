<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientHealthData extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'coach_id',
        'consent_given',
        'consent_date',
        'consent_withdrawn_at',
        'consent_notes',
        'date_of_birth',
        'gender',
        'height_cm',
        'medical_conditions',
        'medications',
        'injuries',
        'allergies',
        'fitness_goals',
        'previous_experience',
        'activity_level',
        'dietary_restrictions',
        'lifestyle_notes',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'data_retention_until',
        'data_deletion_requested',
        'data_deletion_requested_at',
    ];

    protected function casts(): array
    {
        return [
            'consent_given' => 'boolean',
            'consent_date' => 'datetime',
            'consent_withdrawn_at' => 'datetime',
            'date_of_birth' => 'date',
            'medical_conditions' => 'array',
            'medications' => 'array',
            'injuries' => 'array',
            'allergies' => 'array',
            'data_retention_until' => 'datetime',
            'data_deletion_requested' => 'boolean',
            'data_deletion_requested_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function coach(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    public function hasConsent(): bool
    {
        return $this->consent_given && $this->consent_withdrawn_at === null;
    }
}
