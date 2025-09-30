<?php
//im testing
//testing agian
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Admin\SdgController;
use App\Http\Controllers\Task\TaskController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\SkillController;
use App\Http\Controllers\Badge\BadgeController;
use App\Http\Controllers\Events\EventController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\NGOProfileController;
use App\Http\Controllers\Badge\UserBadgeController;
use App\Http\Controllers\Badge\UserPointController;
use App\Http\Controllers\Auth\NGORegisterController;
use App\Http\Controllers\Auth\AdminProfileController;
use App\Http\Controllers\Task\AssignedTaskController;
use App\Http\Controllers\Admin\BlogCategoryController;

use App\Http\Controllers\Auth\AdminRegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\NGO\TaskAssignmentController;
use App\Http\Controllers\Admin\EventCategoryController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Badge\BadgeCategoryController;
use App\Http\Controllers\Auth\VolunteerProfileController;

use App\Http\Controllers\Events\EventDiscoveryController;
use App\Http\Controllers\Attendances\AttendanceController;
use App\Http\Controllers\Auth\VolunteerRegisterController;
use App\Http\Controllers\Events\EventRegistrationController;

use App\Http\Controllers\Events\NgoEventDiscoveryController;
use App\Http\Controllers\Events\NGOEventManagementController;
/*
|--------------------------------------------------------------------------
| Common / Shared Routes (All Users)
|--------------------------------------------------------------------------
*/



// Password Reset
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

// Login & Logout (Generic)
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::post('/logout/ngo', [LoginController::class, 'logoutNgo'])->name('logout.ngo');
Route::post('/logout/volunteer', [LoginController::class, 'logoutVolunteer'])->name('logout.volunteer');




// Public volunteer page
Route::get('/volunteer', [EventDiscoveryController::class, 'index'])->name('volunteer.index.public');

// Preview layout (dev only)
Route::get('/preview/header-footer', fn () => view('layouts.preview-header-footer'));

// Default redirect to Volunteer Registration
Route::get('/', fn () => redirect('/register/volunteer'));

// Publicly accessible NGO profile (anyone can see)
//Route::get('/ngo/profile/{id}', [NGOProfileController::class, 'show'])->name('ngo.profile.show');
Route::get('/volunteers/{id}', [VolunteerProfileController::class, 'show'])->name('volunteer.profile.show');
Route::get('volunteer/{id}/badges', [UserBadgeController::class, 'showByUser'])->name('volunteer.badges.show');






/*
|--------------------------------------------------------------------------
| Volunteer Routes
|--------------------------------------------------------------------------
*/

// Registration
Route::get('/register/volunteer', [VolunteerRegisterController::class, 'showRegisterForm'])->name('register.volunteer');
Route::post('/register/volunteer', [VolunteerRegisterController::class, 'register']);

// Login
Route::get('/login/volunteer', fn () => view('auth.login', ['role' => 'volunteer']))->name('login.volunteer');
Route::post('/login/volunteer', [LoginController::class, 'login'])->name('login.volunteer.submit');


// Authenticated Volunteer Routes
Route::middleware(['auth', 'isVolunteer'])
    ->prefix('volunteer')
    ->name('volunteer.')
    ->group(function () {
        Route::get('/dashboard', fn () => view('volunteer.dashboard'))->name('dashboard');
        Route::get('/profile', [VolunteerProfileController::class, 'show'])->name('profile.profile');
        Route::get('/profile/edit', [VolunteerProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile/update', [VolunteerProfileController::class, 'update'])->name('profile.update');
        // Event actions
        Route::get('/events/{event_id}', [EventDiscoveryController::class, 'show'])->name('events.show');
        Route::get('/events/{event_id}/manage', [EventDiscoveryController::class, 'show2'])->name('profile.registrationEditDelete');
        //Registration actions
        Route::get('/events/{event}/register', [EventRegistrationController::class, 'create'])->name('event.register');
        Route::post('/events/{event}/register', [EventRegistrationController::class, 'store'])->name('event.register.store');
        Route::get('/registrations/{registration}/edit', [EventRegistrationController::class, 'edit'])->name('event.register.edit');
        Route::put('/registrations/{registration}', [EventRegistrationController::class, 'update'])->name('event.register.update');
        Route::delete('/registrations/{registration}', [EventRegistrationController::class, 'destroy'])->name('event.register.destroy');
        //Assigned Task
        Route::get('my-tasks', [AssignedTaskController::class, 'index'])->name('tasks.assigned.index');
        Route::get('my-tasks/{task}', [AssignedTaskController::class, 'show'])->name('tasks.assigned.show');
         
        Route::get('rewards', [UserPointController::class, 'index'])
            ->name('rewards.index');
        

        Route::get('user-badges', [UserBadgeController::class, 'index'])
            ->name('user_badges.index');

        Route::post('badges/{badge}/claim', [UserBadgeController::class, 'claim'])
            ->name('badges.claim');
    });


/*
|--------------------------------------------------------------------------
| NGO Routes
|--------------------------------------------------------------------------
*/

// Registration
Route::get('/register/ngo', [NGORegisterController::class, 'showRegisterForm'])->name('register.ngo');
Route::post('/register/ngo', [NGORegisterController::class, 'register']);

// Login
Route::get('/login/ngo', fn () => view('auth.ngo_login', ['role' => 'ngo']))->name('login.ngo');

Route::middleware(['auth', 'isNGO'])
    ->prefix('ngo')
    ->name('ngo.')
    ->group(function () {

          Route::get('events/{event}/qr', function ($event) {
        $event = \App\Models\Event::findOrFail($event);
        return view('ngo.attendances.qr', compact('event'));
    })->name('attendance.qr');
    // <-- Fix: call the plural method that actually exists in controller
    Route::get('/attendance-list/{eventId}', [AttendanceController::class, 'attendancesList'])->name('attendances.list');
    Route::delete('/events/{event}/attendances/{attendance}', [AttendanceController::class, 'destroy'])->name('attendances.destroy');


        Route::get('/dashboard', fn () => view('ngo.dashboard'))->name('dashboard');
        
        // NGO Profile
        //Route::get('/profile', [NGOProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile', [NGOProfileController::class, 'show'])->name('profile.self');
        Route::get('/profile/edit', [NGOProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile/update', [NGOProfileController::class, 'update'])->name('profile.update');

        // Event Management
        Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
        Route::post('/events', [EventController::class, 'store'])->name('events.store');
        Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.event_edit');
        Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
        Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');

        // Event discovery for NGO
        Route::get('/events', [NgoEventDiscoveryController::class, 'index'])->name('events.index');
        Route::get('/my-events/{event_id}', [NgoEventDiscoveryController::class, 'show'])->name('events.show');
        Route::get('/my-events/{event_id}/manage', [NgoEventDiscoveryController::class, 'show2'])->name('profile.eventEditDelete');

  
        Route::get('/events/{event_id}/manage', [NGOEventManagementController::class, 'manage'])->name('events.manage');

        // Approve / Reject registration (AJAX friendly)
        Route::post('/events/{event}/registrations/{registration}/approve', [NGOEventManagementController::class, 'approve'])->name('events.registrations.approve');
        Route::post('/events/{event}/registrations/{registration}/reject', [NGOEventManagementController::class, 'reject'])->name('events.registrations.reject');
        //Route::get('/events/{event}/participants', [NGOEventManagementController::class, 'participants'])->name('events.participants');

       Route::get('events/{event}/tasks',          [TaskController::class, 'index'])->name('tasks.index');
        Route::get('events/{event}/tasks/create',   [TaskController::class, 'create'])->name('tasks.create');
        Route::post('events/{event}/tasks',         [TaskController::class, 'store'])->name('tasks.store');
        Route::get('/events/{event}/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
        Route::put('events/{event}/tasks/{task}',       [TaskController::class, 'update'])->name('tasks.update');
        Route::delete('events/{event}/tasks/{task}',    [TaskController::class, 'destroy'])->name('tasks.destroy');

        // Assignments
          Route::post('/tasks/{task}/assign', [AssignedTaskController::class, 'assign'])->name('tasks.assign');
    Route::delete('/tasks/{task}/unassign/{userId}', [AssignedTaskController::class, 'unassign'])->name('tasks.unassign');
    //Route::get('/tasks/{task}/assigned', [TaskController::class, 'assignedList']);
    Route::get('/tasks/{task}/assigned', [TaskController::class, 'assignedList'])->name('tasks.assigned');

    Route::post('events/{event}/email', [NGOEventManagementController::class, 'sendEmail'])->name('events.email.send');


    


    });

    Route::get('/ngo/profile/{id}', [NGOProfileController::class, 'show'])->name('ngo.profile.show');



/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

// Login
Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminLoginController::class, 'login']);
Route::post('/admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

// Also allow admin login via generic login form
Route::get('/login/admin', fn () => view('auth.login', ['role' => 'admin']))->name('login.admin');

// Authenticated Admin Routes
Route::middleware(['auth', 'isAdmin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', fn () => view('admin.dashboard'))->name('dashboard');
        Route::get('/profile', [AdminProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [AdminProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile/update', [AdminProfileController::class, 'update'])->name('profile.update');

        // Admin event deletion
        Route::delete('/events/{event}', [EventController::class, 'adminDestroy'])->name('events.destroy');
        Route::get('/sdg', [SdgController::class, 'index'])->name('sdg.sdg-list');
        Route::get('/sdg/create', [SdgController::class, 'create'])->name('sdg.create');
        Route::post('/sdg', [SdgController::class, 'store'])->name('sdg.store'); // Save new SDG
        Route::get('/sdg/{id}/edit', [SdgController::class, 'edit'])->name('sdg.edit'); // Show edit form
        Route::put('/sdg/{id}', [SdgController::class, 'update'])->name('sdg.update'); // Update SDG
        Route::delete('/sdg/{id}', [SdgController::class, 'destroy'])->name('sdg.destroy'); // Delete SDG

         // Skill routes
        Route::get('/skill', [SkillController::class, 'index'])->name('skill.skill-list');
        Route::get('/skill/create', [SkillController::class, 'create'])->name('skill.create');
        Route::post('/skill', [SkillController::class, 'store'])->name('skill.store');
        Route::get('/skill/{id}/edit', [SkillController::class, 'edit'])->name('skill.edit');
        Route::put('/skill/{id}', [SkillController::class, 'update'])->name('skill.update');
        Route::delete('/skill/{id}', [SkillController::class, 'destroy'])->name('skill.destroy');

        // Event Categories CRUD
        Route::get('/event-categories', [EventCategoryController::class, 'index'])->name('eventCategory.eventCategory-list');
        Route::get('/event-categories/create', [EventCategoryController::class, 'create'])->name('eventCategory.create');
        Route::post('/event-categories', [EventCategoryController::class, 'store'])->name('eventCategory.store');
        Route::get('/event-categories/{id}/edit', [EventCategoryController::class, 'edit'])->name('eventCategory.edit');
        Route::put('/event-categories/{id}', [EventCategoryController::class, 'update'])->name('eventCategory.update');
        Route::delete('/event-categories/{id}', [EventCategoryController::class, 'destroy'])->name('eventCategory.destroy');

        // Blog categories CRUD
        Route::get('/blog-categories', [BlogCategoryController::class, 'index'])->name('blogcategory.category-list');
        Route::get('/blog-categories/create', [BlogCategoryController::class, 'create'])->name('blogcategory.create');
        Route::post('/blog-categories', [BlogCategoryController::class, 'store'])->name('blogcategory.store');
        Route::get('/blog-categories/{id}/edit', [BlogCategoryController::class, 'edit'])->name('blogcategory.edit');
        Route::put('/blog-categories/{id}', [BlogCategoryController::class, 'update'])->name('blogcategory.update');
        Route::delete('/blog-categories/{id}', [BlogCategoryController::class, 'destroy'])->name('blogcategory.destroy');

        Route::get('badges', [BadgeController::class, 'index'])->name('badges.index');
        Route::get('badges/create', [BadgeController::class, 'create'])->name('badges.create');
        Route::post('badges', [BadgeController::class, 'store'])->name('badges.store');
        Route::get('badges/{id}/edit', [BadgeController::class, 'edit'])->name('badges.edit');
        Route::put('badges/{id}', [BadgeController::class, 'update'])->name('badges.update');
        Route::delete('badges/{id}', [BadgeController::class, 'destroy'])->name('badges.destroy');

        // Badge Categories
        Route::get('badge-categories', [BadgeCategoryController::class, 'index'])->name('badge_categories.badgeCategory-list');
        Route::get('badge-categories/create', [BadgeCategoryController::class, 'create'])->name('badge_categories.create');
        Route::post('badge-categories', [BadgeCategoryController::class, 'store'])->name('badge_categories.store');
        Route::get('badge-categories/{id}/edit', [BadgeCategoryController::class, 'edit'])->name('badge_categories.edit');
        Route::put('badge-categories/{id}', [BadgeCategoryController::class, 'update'])->name('badge_categories.update');
        Route::delete('badge-categories/{id}', [BadgeCategoryController::class, 'destroy'])->name('badge_categories.destroy');

        // User Badges (view earned badges system-wide)
        Route::get('user-badges', [UserBadgeController::class, 'index'])->name('user_badges.index');
        Route::post('/badges/{badge}/claim', [UserBadgeController::class, 'claim'])->name('volunteer.badges.claim');
    


        // User Points (view all user points)
        Route::get('user-points', [UserPointController::class, 'manage'])->name('user_points.manage');

    });
