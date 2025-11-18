<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramExercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'exercise_name',
        'description',
        'muscle_group',
        'equipment',
        'day_number',
        'order',
        'sets',
        'reps',
        'weight',
        'duration_seconds',
        'rest_seconds',
        'instructions',
        'video_urls',
        'images',
    ];

    protected function casts(): array
    {
        return [
            'video_urls' => 'array',
            'images' => 'array',
        ];
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }
}
