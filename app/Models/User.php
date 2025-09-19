<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function sendPasswordResetNotification($token)
    {
        // Get role from request or default to volunteer
        $role = request()->input('role', 'volunteer');

        // Build URL with role
        $url = url(route('password.reset', [
            'token' => $token,
            'role'  => $role
        ], false));

        // Send notification
        $this->notify(new \App\Notifications\ResetPasswordNotification($token, $role));
    }

    // Role relationship
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    // Profile relationships
    public function volunteerProfile()
    {
        return $this->hasOne(VolunteerProfile::class, 'user_id');
    }

    public function ngoProfile()
    {
        return $this->hasOne(NGOProfile::class, 'user_id');
    }

    public function adminProfile()
    {
        return $this->hasOne(AdminProfile::class, 'user_id');
    }

    // Event created by user (e.g., NGO)
    public function events()
    {
        return $this->hasMany(Event::class, 'user_id');
    }

    // Events user registered for
    public function eventRegistrations()
    {
        return $this->hasMany(EventRegistration::class, 'user_id');
    }

    // Attendances for events
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'user_id');
    }

    // Blog posts
    public function blogPosts()
    {
        return $this->hasMany(BlogPost::class, 'user_id');
    }

    // User points
    public function userPoints()
    {
        return $this->hasMany(UserPoint::class, 'user_id');
    }

    // Total points (e.g., accessor)
    public function getTotalPointsAttribute()
    {
       
        return $this->userPoints->sum('points');
    }

    // Badges earned
    public function userBadges()
    {
        return $this->hasMany(UserBadge::class, 'user_id');
    }

    // Shortcut to just get badges
    public function badges(): BelongsToMany
{
    return $this->belongsToMany(
        Badge::class,      // related model
        'user_badges',     // pivot table name (not model!)
        'user_id',         // foreign key on pivot for User
        'badge_id'         // foreign key on pivot for Badge
    )->withPivot('earnedDate')
     ->withTimestamps();
}
    
    public function taskAssignments()
{
    return $this->hasMany(TaskAssignment::class, 'user_id', 'id');
}





}
