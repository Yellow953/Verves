<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'type',
        'bio',
        'specialization',
        'availability',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'availability' => 'array',
        ];
    }

    /**
     * Check if user is a coach.
     */
    public function isCoach(): bool
    {
        return $this->type === 'coach';
    }

    /**
     * Check if user is a client.
     */
    public function isClient(): bool
    {
        return $this->type === 'client';
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->type === 'admin' || $this->role === 'admin';
    }

    // Coach relationships
    public function clients(): HasMany
    {
        return $this->hasMany(CoachClientRelationship::class, 'coach_id');
    }

    public function activeClients(): HasMany
    {
        return $this->hasMany(CoachClientRelationship::class, 'coach_id')
            ->where('status', 'active');
    }

    public function programs(): HasMany
    {
        return $this->hasMany(Program::class, 'coach_id');
    }

    public function bookingsAsCoach(): HasMany
    {
        return $this->hasMany(Booking::class, 'coach_id');
    }

    // Client relationships
    public function coaches(): HasMany
    {
        return $this->hasMany(CoachClientRelationship::class, 'client_id');
    }

    public function activeCoaches(): HasMany
    {
        return $this->hasMany(CoachClientRelationship::class, 'client_id')
            ->where('status', 'active');
    }

    public function clientPrograms(): HasMany
    {
        return $this->hasMany(Program::class, 'client_id');
    }

    public function bookingsAsClient(): HasMany
    {
        return $this->hasMany(Booking::class, 'client_id');
    }

    public function progressTracking(): HasMany
    {
        return $this->hasMany(ProgressTracking::class, 'client_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'client_id');
    }

    public function activeSubscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'client_id')
            ->where('status', 'active');
    }

    public function healthData(): HasMany
    {
        return $this->hasMany(ClientHealthData::class, 'client_id');
    }
}
