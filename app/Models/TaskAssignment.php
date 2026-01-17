<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskAssignment extends Model
{
    protected $table = 'task_assignments';

    // composite primary keys aren't directly supported by Eloquent.
    // We'll treat this model as a normal model but mark incrementing = false.
    protected $primaryKey = null;
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
    'task_id',
    'user_id',
    'assignedDate',
    'status',
    'reject_reason',
    'responded_at',
];


    protected $dates = [
        'assignedDate',
        'created_at',
        'updated_at',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id', 'task_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
