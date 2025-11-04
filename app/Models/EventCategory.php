<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventCategory extends Model
{
    protected $table = 'event_categories';
    protected $primaryKey = 'eventCategory_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'eventCategory_id',
        'eventCategoryName',
        'basePoints',
    ];

    
  /*  public function events()
{
    return $this->hasMany(Event::class, 'eventCategoryId', 'id');
}*/

 public function events()
    {
        return $this->hasMany(Event::class, 'category_id', 'eventCategory_id');
    }
}
