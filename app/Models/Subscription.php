<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'coach_id',
        'plan_name',
        'plan_description',
        'price',
        'currency',
        'billing_cycle',
        'sessions_included',
        'start_date',
        'end_date',
        'next_billing_date',
        'status',
        'cancelled_at',
        'cancellation_reason',
        'features',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'next_billing_date' => 'date',
            'cancelled_at' => 'datetime',
            'features' => 'array',
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

    public function isActive(): bool
    {
        return $this->status === 'active' && 
               ($this->end_date === null || $this->end_date >= now()->toDateString());
    }

    public function isExpired(): bool
    {
        return $this->end_date !== null && $this->end_date < now()->toDateString();
    }
}
