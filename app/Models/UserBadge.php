<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBadge extends Model

{
    protected $table = 'user_badges';
    protected $primaryKey = 'userBadge_id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
    'userBadge_id',
    'user_id',
    'badge_id',
    'earnedDate',
];


    public function badge()
    {
        return $this->belongsTo(Badge::class, 'badge_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
