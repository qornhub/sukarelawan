<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class NGOProfile extends Model
{
    use HasFactory;

    protected $table = 'ngo_profiles'; // explicitly define table name

    public $incrementing = false;
    protected $primaryKey = 'ngo_id';
    protected $keyType = 'string';

    protected $fillable = [
        'ngo_id',
        'user_id',
        'organizationName',
        'registrationNumber',
        'country',
        'contactNumber',
        'about',
        'website',
        'coverPhoto',
        'profilePhoto',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
