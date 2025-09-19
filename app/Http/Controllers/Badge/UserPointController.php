<?php

namespace App\Http\Controllers\Badge;

use App\Models\User;
use App\Models\Badge;
use App\Models\Event;
use App\Models\UserBadge;
use App\Models\UserPoint;
use App\Models\Attendance;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\BadgeCategory;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserPointController extends Controller
{
    /**
     * Volunteer: View own points & badges
     */
    



public function index()
{
    $user = Auth::user();

    // total points for current user
    $points = (int) UserPoint::where('user_id', $user->id)->sum('points');

    // latest activities
    $activities = UserPoint::where('user_id', $user->id)->latest()->get();

    // earned badges: server-side paginate 9 per page, page parameter name = 'earned_page'
$earnedBadges = $user->badges()
    ->withPivot('earnedDate', 'created_at')
    ->orderByDesc('user_badges.created_at') // most-recent first
    ->paginate(6, ['*'], 'earned_page');

// decorate earned badges with image url (optional)
$earnedBadges->getCollection()->transform(function ($b) {
    $b->img_url = (!empty($b->badgeImage) && file_exists(public_path($b->badgeImage)))
        ? asset($b->badgeImage)
        : asset('images/badges/default-badge.jpg');
    return $b;
});

    // quick lookup array of earned badge ids
    $earnedBadgeIds = $earnedBadges->pluck('badge_id')->toArray();

    //
    // Leaderboard: top 10 volunteers (with sum of points + avatar url)
    //
    $topVolunteers = User::select('users.*')
        ->withSum('userPoints', 'points')
        ->whereHas('role', function ($q) {
            $q->where('roleName', 'volunteer');
        })
        ->orderByDesc('user_points_sum_points')
        ->take(10)
        ->get()
        ->map(function ($u) {
            $u->total_points = (int) ($u->user_points_sum_points ?? 0);

            // Build avatar url from volunteerProfile->profilePhoto (fallback)
            $filename = optional($u->volunteerProfile)->profilePhoto ?? optional($u->volunteerProfile)->avatar ?? null;
            $u->avatar_url = $filename && file_exists(public_path('images/profiles/' . $filename))
                ? asset('images/profiles/' . $filename)
                : asset('images/default-profile.png');

            return $u;
        });

    //
    // Compute current user's global rank among volunteers (robust)
    // We load ordered volunteer ids and search for current user's id.
    // If user not found in volunteers list, rank becomes (count + 1).
    //
    $volunteerIdsOrdered = User::withSum('userPoints', 'points')
        ->whereHas('role', function ($q) {
            $q->where('roleName', 'volunteer');
        })
        ->orderByDesc('user_points_sum_points')
        ->pluck('id')
        ->toArray();

    $pos = array_search($user->id, $volunteerIdsOrdered);
    if ($pos === false) {
        // not found (shouldn't usually happen) -> rank after all volunteers
        $userRank = count($volunteerIdsOrdered) + 1;
    } else {
        $userRank = $pos + 1; // array_search returns 0-based index
    }

    //
    // Badges collection: search / category / sort (same as before),
    // but eager-load category and include claimed_count and img_url, has_earned, claimable_by_user
    //
    $query = Badge::query()->with('category');

    if ($search = request('q')) {
        $query->where(function ($q) use ($search) {
            $q->where('badgeName', 'like', "%{$search}%")
              ->orWhere('badgeDescription', 'like', "%{$search}%");
        });
    }

    if ($category = request('category')) {
        $query->where('badgeCategory_id', $category);
    }

    if ($sort = request('sort')) {
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'points_asc':
                $query->orderBy('pointsRequired', 'asc');
                break;
            case 'points_desc':
                $query->orderBy('pointsRequired', 'desc');
                break;
            default:
                $query->latest();
        }
    } else {
        $query->latest();
    }

    // The badges list at bottom
    $badges = $query->withCount(['users as claimed_count'])->paginate(8)->withQueryString();

    // decorate badges (avoid heavy Blade logic)
    $badges->getCollection()->transform(function ($badge) use ($earnedBadgeIds, $user, $points) {
        // image url fallback
        if (!empty($badge->badgeImage) && file_exists(public_path($badge->badgeImage))) {
            $badge->img_url = asset($badge->badgeImage);
        } else {
            $badge->img_url = asset('images/badges/default-badge.jpg');
        }

        // claimed_count provided by withCount (if not present, fallback to DB query)
        if (!isset($badge->claimed_count)) {
            $badge->claimed_count = DB::table('user_badges')->where('badge_id', $badge->badge_id)->count();
        }

        // whether current user has earned this badge
        $badge->has_earned = in_array($badge->badge_id, $earnedBadgeIds, true);

        // whether user can claim (has enough points and hasn't claimed yet)
        $badge->claimable_by_user = (!$badge->has_earned) && ($points >= ($badge->pointsRequired ?? 0));

        return $badge;
    });

    // categories for filter
    $categories = BadgeCategory::withCount('badges')->get();

    return view('volunteer.badges.index', compact(
        'user',
        'points',
        'activities',
        'earnedBadges',
        'topVolunteers',
        'userRank',
        'badges',
        'categories'
    ));
}

    /**
     * Admin: View all user points (existing)
     */
    public function manage()
    {
        $allUserPoints = UserPoint::with('user')->latest()->get();
        return view('admin.badges.manage', compact('allUserPoints'));
    }

    /**
     * Auto-award points when attendance is confirmed
     */
    public function awardPointsFromAttendance($attendance_id)
    {
        $attendance = Attendance::with('event')->findOrFail($attendance_id);

        $event = $attendance->event;

        if (!$event || !$event->eventPoints) {
            return; // No points configured for this event
        }

        // Check if user already got points for this attendance
        $alreadyAwarded = UserPoint::where('attendance_id', $attendance->attendance_id)->exists();

        if (!$alreadyAwarded) {
            UserPoint::create([
                'userPoint_id'  => (string) Str::uuid(),
                'user_id'       => $attendance->user_id,
                'points'        => $event->eventPoints,
                'activityType'  => 'Event Attendance',
                'event_id'      => $event->event_id,
                'attendance_id' => $attendance->attendance_id,
            ]);
        }
    }
}
