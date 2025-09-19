<?php

namespace App\Models;
use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\Model;

class EventRegistration extends Model
{
    protected $table = 'event_registrations';
     protected $primaryKey = 'registration_id';
    public $incrementing = false;
    protected $keyType = 'string';

    public const STATUS_PENDING  = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_CANCELLED = 'cancelled';

   protected $fillable = [
    'registration_id',
    'event_id',
    'user_id',
    'name',
    'email',
    'contactNumber',
    'age',
    'gender',
    'address',
    'company',
    'volunteeringExperience',
    'emergencyContact',
    'emergencyContactNumber',
    'contactRelationship',
    'skill',
    'registrationDate',
];

  public static function statuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_APPROVED,
            self::STATUS_REJECTED,
            self::STATUS_CANCELLED,
        ];
    }

protected static function boot()
{
    parent::boot();

    static::creating(function ($model) {
        if (empty($model->registration_id)) {
            $model->registration_id = (string) \Illuminate\Support\Str::uuid();
        }
    });
}


    public function event()
{
   return $this->belongsTo(Event::class, 'event_id', 'event_id');

}

public function user()
{
    return $this->belongsTo(User::class, 'user_id', 'id');
}
}
