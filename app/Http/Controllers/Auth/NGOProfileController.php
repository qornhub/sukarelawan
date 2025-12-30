<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\NGOProfile;
use App\Models\Event;
use App\Models\BlogPost;

class NGOProfileController extends Controller
{
    
/**
 * Show an NGO profile. $id is optional:
 *  - If $id is provided, show that profile (public view).
 *  - If $id is null, show the currently authenticated NGO's profile (requires auth).
 *
 * This supports both /ngo/profile and /ngo/profile/{id}.
 */
public function show($id = null)
{
    // If no id provided, require an authenticated user and use their ID
    if (is_null($id)) {
        if (! Auth::check()) {
            abort(403, 'Unauthorized');
        }
        $id = Auth::id();
    }

    // Lookup the profile by either ngo_id or user_id (be permissive)
    $profile = NGOProfile::where('ngo_id', $id)
        ->orWhere('user_id', $id)
        ->first();

    if (! $profile) {
        abort(404, 'Profile not found');
    }

    $isOwner = Auth::check() && Auth::user()->id === $profile->user_id;

    // Determine which column links events to this NGO/profile
    $eventsTable = 'events';

    $ngoReferenceCols  = ['ngo_profile_id', 'ngo_id', 'organization_id'];
    $userReferenceCols = ['user_id', 'created_by', 'creator_id', 'owner_id'];

    $ownerColumn = null;
    $ownerValue  = null;

    // check ngo-ref columns first
    foreach ($ngoReferenceCols as $col) {
        if (Schema::hasColumn($eventsTable, $col)) {
            $ownerColumn = $col;
            // prefer explicit ngo_id, otherwise try model id or user_id
            $ownerValue  = $profile->ngo_id ?? $profile->id ?? $profile->user_id ?? null;
            break;
        }
    }

    // if none found, check user-ref columns
    if (! $ownerColumn) {
        foreach ($userReferenceCols as $col) {
            if (Schema::hasColumn($eventsTable, $col)) {
                $ownerColumn = $col;
                $ownerValue  = $profile->user_id ?? $profile->id ?? null;
                break;
            }
        }
    }

    // If we couldn't detect a linking column or owner value, return safe empty datasets
    if (! $ownerColumn || is_null($ownerValue)) {
        return view('ngo.profile.show', [
            'profile'       => $profile,
            'ongoingEvents' => collect(), // blade is defensive so this is OK
            'pastEvents'    => collect(),
            'blogPosts'     => collect(),
            'totalEvents'   => 0,
            'isOwner'       => $isOwner,
        ]);
    }

    // Build event queries
    $baseQuery = Event::where($ownerColumn, $ownerValue);
    $now = now();

    // -----------------------
    // Paginate ongoing events
    // -----------------------
    // Use a custom page parameter 'ongoing_page' so pagination for ongoing/past don't collide.
    // Change 6 to whatever items-per-page you prefer.
    $ongoingQuery = (clone $baseQuery)
        ->where(function ($q) use ($now) {
            $q->where(function ($q2) use ($now) {
                $q2->whereNotNull('eventEnd')->where('eventEnd', '>=', $now);
            })
            ->orWhere(function ($q2) use ($now) {
                $q2->whereNull('eventEnd')->where('eventStart', '>=', $now);
            });
        })
        ->orderBy('eventStart', 'asc');

    $ongoingEvents = $ongoingQuery->paginate(3, ['*'], 'ongoing_page');

    // ----------------------
    // Paginate past events
    // ----------------------
    $pastQuery = (clone $baseQuery)
        ->where(function ($q) use ($now) {
            $q->where(function ($q2) use ($now) {
                $q2->whereNotNull('eventEnd')->where('eventEnd', '<', $now);
            })
            ->orWhere(function ($q2) use ($now) {
                $q2->whereNull('eventEnd')->where('eventStart', '<', $now);
            });
        })
        ->orderBy('eventEnd', 'desc');

    $pastEvents = $pastQuery->paginate(3, ['*'], 'past_page');

    $totalEvents = (clone $baseQuery)->count();

    // Blogs: prefer user_id, fallback to ngo_profile_id (ngo_id)
    $blogQuery = null;
    if (Schema::hasColumn('blog_posts', 'user_id')) {
        $blogQuery = BlogPost::where('user_id', $profile->user_id);
    } elseif (Schema::hasColumn('blog_posts', 'ngo_profile_id')) {
        $blogQuery = BlogPost::where('ngo_profile_id', $profile->ngo_id ?? $profile->id);
    }

    // If we couldn't find any blog linking column, set an empty paginator-like collection
    if (! $blogQuery) {
        $blogPosts = collect();
    } else {
        // only owners can see drafts; others see published only
        if (! $isOwner) {
            $blogQuery = $blogQuery->where('status', 'published');
        }

        // order & paginate (keeps previous behaviour)
        $blogPosts = $blogQuery->orderBy('published_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(3);
    }

    return view('ngo.profile.show', compact(
        'profile',
        'ongoingEvents',
        'pastEvents',
        'blogPosts',
        'totalEvents',
        'isOwner'
    ));
}





    /**
     * Show edit form for the authenticated NGO user.
     */
    public function edit()
    {
        $profile = NGOProfile::where('user_id', Auth::id())->first();
    
    if (!$profile) {
        abort(404, 'Profile not found');
    }
        return view('ngo.profile.edit', compact('profile'));

        
    }

    /**
     * Update profile (keeps your existing move()/unlink() behaviour).
     */
    public function update(Request $request)
    {
        $request->validate([
            'organizationName'   => 'required|string|max:255',
            'registrationNumber' => 'required|string|max:255',
            'country'            => 'required|string|max:255',
            'contactNumber'      => 'required|string|max:20',
            'about'              => 'nullable|string',
            'website'            => 'nullable|url',
            'coverPhoto'         => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'profilePhoto'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $profile = $user->ngoProfile;

        // Helper: do not delete placeholder defaults
        $defaultCover   = 'default-cover.jpg';
        $defaultProfile = 'default-profile.png';

        // ---------- Cover photo ----------
        if ($request->hasFile('coverPhoto')) {
            if ($profile->coverPhoto) {
                $oldCoverBasename = basename($profile->coverPhoto);
                $oldCoverPath = public_path('images/covers/' . $oldCoverBasename);

                if ($oldCoverBasename !== $defaultCover && file_exists($oldCoverPath)) {
                    @unlink($oldCoverPath);
                }
            }

            $coverFileName = time() . '_cover_' . preg_replace('/\s+/', '_', $request->coverPhoto->getClientOriginalName());
            $request->coverPhoto->move(public_path('images/covers'), $coverFileName);

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

        // ---------- Update profile ----------
        $profile->fill([
            'organizationName'   => $request->organizationName,
            'registrationNumber' => $request->registrationNumber,
            'country'            => $request->country,
            'contactNumber'      => $request->contactNumber,
            'about'              => $request->about,
            'website'            => $request->website,
        ])->save();

        // Keep users.name in sync with NGO name
        $user->name = $request->organizationName;
        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
}
