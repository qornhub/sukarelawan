<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BadgeCategory extends Model
{
    use HasFactory;

    protected $primaryKey = 'badgeCategory_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->badgeCategory_id)) {
                $model->badgeCategory_id = (string) Str::uuid();
            }
        });
    }

    protected $fillable = [
        'badgeCategoryName',
    ];

    public function badges()
    {
        return $this->hasMany(Badge::class, 'badgeCategory_id');
    }
}
