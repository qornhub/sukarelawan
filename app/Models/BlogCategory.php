<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    protected $table = 'blog_categories';
    protected $primaryKey = 'blogCategory_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'blogCategory_id',
        'categoryName',
    ];
}
