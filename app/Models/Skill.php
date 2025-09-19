<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;

    protected $table = 'skills';
    protected $primaryKey = 'skill_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'skill_id',
        'skillName',
    ];

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_skill', 'skill_id', 'event_id');
    }
}
