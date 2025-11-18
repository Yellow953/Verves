<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgressTracking extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'coach_id',
        'program_id',
        'booking_id',
        'tracking_date',
        'type',
        'weight_kg',
        'body_fat_percentage',
        'muscle_mass_kg',
        'chest_cm',
        'waist_cm',
        'hips_cm',
        'arms_cm',
        'thighs_cm',
        'photos',
        'notes',
        'exercise_data',
        'body_composition_data',
    ];

    protected function casts(): array
    {
        return [
            'tracking_date' => 'date',
            'photos' => 'array',
            'exercise_data' => 'array',
            'body_composition_data' => 'array',
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

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
