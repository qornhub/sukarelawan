<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventComment extends Model
{
    protected $table = 'event_comments';
    protected $primaryKey = 'eventComment_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'eventComment_id',
        'event_id',
        'user_id',
        'content',
        'sentiment',               
        'sentiment_confidence',    
    ];

    protected $casts = [
        'sentiment_confidence' => 'float',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }

    public function event()
    {
        return $this->belongsTo(\App\Models\Event::class, 'event_id', 'event_id');
    }
}
