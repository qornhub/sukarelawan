<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPoint extends Model
{
    use HasFactory;

    protected $primaryKey = 'userPoint_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'points',
        'activityType',
        'event_id',
        'attendance_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
