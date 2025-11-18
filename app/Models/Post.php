<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_id',
        'user_id',
        'body',
        'is_first_post',
    ];

    protected function casts(): array
    {
        return [
            'is_first_post' => 'boolean',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($post) {
            // Update topic's replies count and last_reply_at
            $topic = $post->topic;
            if (!$post->is_first_post) {
                $topic->increment('replies_count');
            }
            $topic->update(['last_reply_at' => now()]);
        });

        static::deleted(function ($post) {
            // Update topic's replies count
            $topic = $post->topic;
            if ($topic && !$post->is_first_post) {
                $topic->decrement('replies_count');
                // Update last_reply_at to the latest post's created_at
                $latestPost = $topic->posts()->latest()->first();
                $topic->update(['last_reply_at' => $latestPost ? $latestPost->created_at : null]);
            }
        });
    }

    /**
     * Get the topic that owns the post.
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * Get the user that created the post.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
