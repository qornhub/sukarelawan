<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendances';
    protected $primaryKey = 'attendance_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'attendance_id',
        'user_id',
        'event_id',
        'status',
        'attendanceTime',
        'pointEarned',
    ];

    protected static function booted()
{
    static::created(function ($attendance) {
        app(\App\Http\Controllers\Badge\UserPointController::class)
            ->awardPointsFromAttendance($attendance->attendance_id);
    });
}

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
