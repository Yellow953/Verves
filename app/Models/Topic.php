<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Topic extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'user_id',
        'title',
        'slug',
        'body',
        'is_pinned',
        'is_locked',
        'views_count',
        'replies_count',
        'last_reply_at',
    ];

    protected function casts(): array
    {
        return [
            'is_pinned' => 'boolean',
            'is_locked' => 'boolean',
            'views_count' => 'integer',
            'replies_count' => 'integer',
            'last_reply_at' => 'datetime',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($topic) {
            if (empty($topic->slug)) {
                $topic->slug = Str::slug($topic->title);
            }
        });

        static::updating(function ($topic) {
            if ($topic->isDirty('title') && empty($topic->slug)) {
                $topic->slug = Str::slug($topic->title);
            }
        });
    }

    /**
     * Get the category that owns the topic.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the user that created the topic.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the posts for the topic.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get the first post (original post) for the topic.
     */
    public function firstPost(): HasMany
    {
        return $this->hasMany(Post::class)->where('is_first_post', true);
    }

    /**
     * Get the replies (non-first posts) for the topic.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Post::class)->where('is_first_post', false);
    }

    /**
     * Get the views for the topic.
     */
    public function views(): HasMany
    {
        return $this->hasMany(TopicView::class);
    }

    /**
     * Increment the views count.
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }
}
