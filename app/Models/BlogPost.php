<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    protected $table = 'blog_posts';
     protected $primaryKey = 'blogPost_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
    'blogPost_id',
    'user_id',
    'category_id',
    'title',
    'content',
    'image',
    'publishedDate',
    'created_at',
    'updated_at',
];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
