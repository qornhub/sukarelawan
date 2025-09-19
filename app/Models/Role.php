<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $primaryKey = 'role_id';
    protected $keyType = 'string';

    protected $fillable = ['role_id', 'roleName'];

    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }
}

