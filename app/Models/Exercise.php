<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'muscle_group',
        'equipment',
        'difficulty',
        'instructions',
        'video_urls',
        'images',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'video_urls' => 'array',
            'images' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Scope to get only active exercises
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by muscle group
     */
    public function scopeByMuscleGroup($query, $muscleGroup)
    {
        return $query->where('muscle_group', $muscleGroup);
    }

    /**
     * Scope to filter by equipment
     */
    public function scopeByEquipment($query, $equipment)
    {
        return $query->where('equipment', $equipment);
    }
}

