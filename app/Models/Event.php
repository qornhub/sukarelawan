<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'events';
    protected $primaryKey = 'event_id';
    public $incrementing = false;
    protected $keyType = 'string';

   
    protected $fillable = [
    'event_id',
    'user_id',
    'category_id',
    'eventTitle',
    'eventPoints',
    'eventStart',
    'eventEnd',
    'requirements',
    'eventSummary',
    'eventDescription',
    'eventImage',
    'eventImpact',
    'venueName',
    'zipCode',
    'city',
    'state',
    'country',
    'eventMaximum',
];

public function organizer()
{
    return $this->belongsTo(User::class, 'user_id', 'id'); 
}
    
public function registrations()
{
    
    return $this->hasMany(EventRegistration::class, 'event_id', 'event_id');

}

public function category()
{
    return $this->belongsTo(EventCategory::class, 'category_id', 'eventCategory_id');
}


public function attendances()
{
    return $this->hasMany(Attendance::class, 'event_id');
}
public function sdgs()
{
    return $this->belongsToMany(Sdg::class, 'event_sdg', 'event_id', 'sdg_id');
}

public function skills()
{
    return $this->belongsToMany(Skill::class, 'event_skill', 'event_id', 'skill_id', 'event_id', 'skill_id');
}

  public function tasks()
    {
        return $this->hasMany(Task::class, 'event_id', 'event_id');
    }

    public function comments()
{
    return $this->hasMany(EventComment::class, 'event_id', 'event_id');
}



}
