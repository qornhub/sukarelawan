<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VolunteerProfile extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $primaryKey = 'volunteer_id';
    protected $keyType = 'string';

    protected $fillable = [
        'volunteer_id', 
        'user_id', 
        'name',
        'contactNumber',
        'country',
        'dateOfBirth',
        'gender',
        'address',
        'coverPhoto',
        'profilePhoto'
    ];
      public function user()
    {
        return $this->belongsTo(User::class);
    }
}

