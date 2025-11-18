<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'coach_id',
        'client_id',
        'program_id',
        'session_date',
        'duration_minutes',
        'session_type',
        'location',
        'meeting_link',
        'status',
        'notes',
        'client_notes',
        'coach_notes',
        'cancelled_at',
        'cancellation_reason',
        'price',
        'payment_status',
    ];

    protected function casts(): array
    {
        return [
            'session_date' => 'datetime',
            'cancelled_at' => 'datetime',
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

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function progressTracking(): HasMany
    {
        return $this->hasMany(ProgressTracking::class);
    }

    public function isUpcoming(): bool
    {
        return $this->session_date > now() && in_array($this->status, ['pending', 'confirmed']);
    }

    public function isPast(): bool
    {
        return $this->session_date < now();
    }

    public function canBeCancelled(): bool
    {
        return $this->isUpcoming() && !in_array($this->status, ['cancelled', 'completed']);
    }
}
