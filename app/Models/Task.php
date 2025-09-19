<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Task extends Model
{
    protected $table = 'tasks';
    protected $primaryKey = 'task_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'task_id',
        'event_id',
        'title',
        'description',
    ];

    public function getRouteKeyName()
{
    return 'task_id';
}
    // Auto-generate UUID on create
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id', 'event_id');
    }

    public function assignments()
    {
        return $this->hasMany(TaskAssignment::class, 'task_id', 'task_id');
    }

    public function assignedUsers()
{
    return $this->belongsToMany(User::class, 'task_assignments', 'task_id', 'user_id')
                ->withPivot('assignedDate')
                ->withTimestamps();
}
}
