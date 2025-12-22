<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Attendance;
use App\Models\BlogPost;
use App\Models\UserPoint;
use App\Models\UserBadge;
use App\Models\Badge;
use App\Models\User;


use Illuminate\Support\Facades\Auth;

class VolunteerProfileController extends Controller
{
public function show($id = null)
{
    // If no id is passed → show logged-in user's profile
    $user = $id ? User::findOrFail($id) : Auth::user();
    $profile = $user->volunteerProfile;
    $today = now()->toDateString();

    // Upcoming events = approved + not ended + NOT already attended
    // newest (by eventStart) first, paginated 3 per page
    $upcomingEvents = Event::whereHas('registrations', function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->where('status', 'approved');
        })
        ->whereDate('eventEnd', '>=', $today)
        ->whereDoesntHave('attendances', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->orderByDesc('eventStart')
        ->paginate(3, ['*'], 'upcoming_page');

    // Past events — events where we have an attendance record for the user
    // newest (most recent eventStart) first, paginated 3 per page
    $pastEvents = Event::whereHas('attendances', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->with(['attendances' => function ($q) use ($user) {
            $q->where('user_id', $user->id);
        }])
        ->orderByDesc('eventStart')
        ->paginate(3, ['*'], 'past_page');

    //$totalPoints = Attendance::where('user_id', $user->id)->sum('pointEarned'); calculate from attendace table
$totalPoints = (int) UserPoint::where('user_id', $user->id)->sum('points');

    // paginate user badges, 5 per page. named page param 'earned_page'
    $userBadges = UserBadge::with('badge')
        ->where('user_id',  $user->id)
        ->orderByDesc('created_at')
        ->paginate(5, ['*'], 'earned_page');

    // ---------- Blog posts for profile ----------
    // Owner sees all posts (draft + published); others see only published
    $isOwner = Auth::check() && Auth::id() === $user->id;

    $blogQuery = BlogPost::where('user_id', $user->id)
        ->with(['category', 'user'])
        ->orderByDesc('created_at');

    if (! $isOwner) {
        $blogQuery->where('status', 'published');
    }

    // Paginate blog posts separately (6 per page). Use page param 'blog_page'
    $blogPosts = $blogQuery->paginate(3, ['*'], 'blog_page');

    
    return view('volunteer.profile.profile', compact(
        'profile',
        'upcomingEvents',
        'pastEvents',
        'blogPosts',
        'totalPoints',
        'userBadges',
        'isOwner'
    ));
}





public function edit()
{
    $profile = auth::user()->volunteerProfile;
    return view('volunteer.profile.edit', compact('profile'));
}

public function update(Request $request)
{
    $request->validate([
        'name'         => 'required|string|max:255',
        'contactNumber'=> 'nullable|string|max:20',
        'country'      => 'nullable|string|max:100',
        'dateOfBirth'  => 'nullable|date',
        'gender'       => 'nullable|in:male,female,other',
        'address'      => 'nullable|string|max:255',
        'coverPhoto'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'profilePhoto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    /** @var \App\Models\User $user */
    $user = Auth::user();
    $profile = $user->volunteerProfile;

    // Helper: do not delete placeholder defaults
    $defaultCover = 'default-cover.jpg';
    $defaultProfile = 'default-profile.png';

    // ---------- Cover photo ----------
    if ($request->hasFile('coverPhoto')) {
        // build old file path robustly
        if ($profile->coverPhoto) {
            $oldCoverBasename = basename($profile->coverPhoto); // works if DB stores "covers/xxx" or "xxx"
            $oldCoverPath = public_path('images/covers/' . $oldCoverBasename);

            if ($oldCoverBasename !== $defaultCover && file_exists($oldCoverPath)) {
                @unlink($oldCoverPath); // @ to suppress warning; you may prefer to log on failure
            }
        }

        $coverFileName = time() . '_cover_' . preg_replace('/\s+/', '_', $request->coverPhoto->getClientOriginalName());
        $request->coverPhoto->move(public_path('images/covers'), $coverFileName);

        // store only the filename (clear and simple)
        $profile->coverPhoto = $coverFileName;
    }

    // ---------- Profile photo ----------
    if ($request->hasFile('profilePhoto')) {
        if ($profile->profilePhoto) {
            $oldProfileBasename = basename($profile->profilePhoto);
            $oldProfilePath = public_path('images/profiles/' . $oldProfileBasename);

            if ($oldProfileBasename !== $defaultProfile && file_exists($oldProfilePath)) {
                @unlink($oldProfilePath);
            }
        }

        $profileFileName = time() . '_profile_' . preg_replace('/\s+/', '_', $request->profilePhoto->getClientOriginalName());
        $request->profilePhoto->move(public_path('images/profiles'), $profileFileName);

        $profile->profilePhoto = $profileFileName;
    }

    // ---------- Update profile and user ----------
    $profile->fill([
        'name'          => $request->name,
        'contactNumber' => $request->contactNumber,
        'country'       => $request->country,
        'dateOfBirth'   => $request->dateOfBirth,
        'gender'        => $request->gender,
        'address'       => $request->address,
    ])->save();

    // Keep users.name in sync
    $user->name = $request->name;
    $user->save();

    return redirect()->back()->with('success', 'Profile updated successfully.');
}




}
