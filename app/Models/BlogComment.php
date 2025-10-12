<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogComment extends Model
{
    protected $table = 'blog_comments';
    protected $primaryKey = 'blogComment_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'blogComment_id',
        'blogPost_id',
        'user_id',
        'content',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }

    public function post()
    {
        return $this->belongsTo(\App\Models\BlogPost::class, 'blogPost_id', 'blogPost_id');
    }
}
