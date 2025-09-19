<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Badge extends Model
{
    use HasFactory;

    protected $primaryKey = 'badge_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'badgeCategory_id',
        'badgeName',
        'badgeDescription',
        'pointsRequired',
        'badgeImage',
    ];

    protected static function boot() { 
        parent::boot(); static::creating(function ($model) { 
            if (empty($model->badge_id)) { 
            $model->badge_id = (string) Str::uuid(); 
            } 
        }); 
    }

     public function users(): BelongsToMany
    {
        // pivot table: user_badges
        // localKey (badge_id) and relatedKey (user_id) are set explicitly
        return $this->belongsToMany(\App\Models\User::class, 'user_badges', 'badge_id', 'user_id')
                    ->withPivot('created_at') // if you store earned/claimed dates
                    ->withTimestamps();       // optional, if pivot has timestamps
    }

    public function category()
    {
        return $this->belongsTo(BadgeCategory::class, 'badgeCategory_id');
    }

    public function userBadges()
    {
        return $this->hasMany(UserBadge::class, 'badge_id');
    }

     public function getImgUrlAttribute()
    {
        if (!empty($this->badgeImage) && file_exists(public_path($this->badgeImage))) {
            return asset($this->badgeImage);
        }
        return asset('images/badges/default-badge.jpg');
    }
}

