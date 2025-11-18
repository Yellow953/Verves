<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TopicView extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_id',
        'user_id',
        'ip_address',
        'viewed_at',
    ];

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'viewed_at' => 'datetime',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($view) {
            if (empty($view->viewed_at)) {
                $view->viewed_at = now();
            }
        });
    }

    /**
     * Get the topic that was viewed.
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * Get the user that viewed the topic.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
