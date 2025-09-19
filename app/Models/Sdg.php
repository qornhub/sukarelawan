<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sdg extends Model
{
    protected $table = 'sdgs';

    // Your PK is sdg_id (UUID string)
    protected $primaryKey = 'sdg_id';
    public $incrementing = false;        // not auto-incrementing
    protected $keyType = 'string';       // UUID stored as string

    // Fillable fields (include sdg_id if you create via create([...]))
    protected $fillable = [
        'sdg_id',
        'sdgName',
        'sdgImage',
        'sdg_number',
        // 'description' // add if you have this column
    ];



    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_sdg', 'sdg_id', 'event_id');
    }
}
