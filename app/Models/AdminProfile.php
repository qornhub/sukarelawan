<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdminProfile extends Model
{
    use HasFactory;

    protected $table = 'admin_profiles';

    public $incrementing = false;
    protected $primaryKey = 'admin_id';
    protected $keyType = 'string';

    protected $fillable = [
        'admin_id',
        'user_id',
        'name',
        'profilePhoto',
    ];
}
