<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'coach_id',
        'client_id',
        'relationship_id',
        'name',
        'description',
        'type',
        'duration_weeks',
        'start_date',
        'end_date',
        'status',
        'goals',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'goals' => 'array',
            'notes' => 'array',
        ];
    }

    public function coach(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function relationship(): BelongsTo
    {
        return $this->belongsTo(CoachClientRelationship::class, 'relationship_id');
    }

    public function exercises(): HasMany
    {
        return $this->hasMany(ProgramExercise::class)->orderBy('day_number')->orderBy('order');
    }

    public function progressTracking(): HasMany
    {
        return $this->hasMany(ProgressTracking::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
