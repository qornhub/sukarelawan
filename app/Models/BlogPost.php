<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    use HasFactory;

    protected $table = 'blog_posts';
    protected $primaryKey = 'blogPost_id';
    public $incrementing = false;
    protected $keyType = 'string';

    // Allow mass assignment for relevant fields (no created_at/updated_at)
    protected $fillable = [
        'blogPost_id',
        'user_id',
        'category_id',
        'title',
        'blogSummary',
        'content',
        'image',
        'status',        // 'draft' | 'published'
        'published_at',  // nullable timestamp
    ];

    // Casts
    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * Booted model events:
     * - set UUID for primary key if not provided
     * - ensure published_at set/cleared based on status
     */
    protected static function booted()
    {
        static::creating(function ($post) {
            if (empty($post->{$post->getKeyName()})) {
                $post->{$post->getKeyName()} = (string) Str::uuid();
            }

            // If a post is being created as published but published_at not provided, set now()
            if (($post->status ?? null) === 'published' && empty($post->published_at)) {
                $post->published_at = now();
            }
        });

        static::saving(function ($post) {
            // If status is changed to published and published_at is empty, set published_at
            if ($post->isDirty('status') && $post->status === 'published' && empty($post->published_at)) {
                $post->published_at = now();
            }

            // If status is draft, clear published_at
            if ($post->status === 'draft') {
                $post->published_at = null;
            }
        });
    }

    /* Relations */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'category_id', 'blogCategory_id');
    }

    /* Scopes */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function comments()
{
    return $this->hasMany(BlogComment::class, 'blogPost_id');
}
}
