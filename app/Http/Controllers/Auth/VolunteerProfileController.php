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

   // Upcoming events = approved + not ended
$upcomingEvents = Event::whereHas('registrations', function ($q) use ($user) {
        $q->where('user_id', $user->id)
          ->where('status', 'approved'); // ✅ only approved registrations
    })
    ->whereDate('eventEnd', '>=', $today)
    ->get();

    $pastEvents = Event::whereHas('attendances', fn($q) => 
        $q->where('user_id', $user->id)
    )->get();

    $blogPosts   = BlogPost::where('user_id', $user->id)->get();
    $totalPoints = Attendance::where('user_id', $user->id)->sum('pointEarned');
    //$badges      = UserBadge::where('user_id', $user->id)->with('badge')->get()->pluck('badge');
     // Load the badges for this user
    $userBadges = UserBadge::with('badge')
        ->where('user_id',  $user->id)
        ->get();

    return view('volunteer.profile.profile', compact(
        'profile', 'upcomingEvents', 'pastEvents', 'blogPosts', 'totalPoints', 'userBadges'
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
