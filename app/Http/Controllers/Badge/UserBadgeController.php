<?php

namespace App\Http\Controllers\Badge;

use App\Models\User;
use App\Models\Badge;
use App\Models\UserBadge;
use App\Models\UserPoint;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserBadgeController extends Controller
{
    
    public function index()
    {
        $user = Auth::user();

        // Use the belongsToMany relation on User to get Badge models + pivot
        $earnedBadges = $user->badges()->orderByDesc('pivot_earnedDate')->get();

        // If you also want the claimable badges here, compute them:
        $totalPoints = (int) UserPoint::where('user_id', $user->id)->sum('points');

        $claimedIds = $earnedBadges->pluck('badge_id')->toArray();

        $claimableBadges = \App\Models\Badge::where('pointsRequired', '<=', $totalPoints)
            ->whereNotIn('badge_id', $claimedIds)
            ->orderByDesc('created_at')
            ->get();

        return view('volunteer.badges.index', compact('earnedBadges', 'claimableBadges', 'totalPoints'));
    }

    /**
     * Volunteer: Claim an unlocked badge.
     * Creates a record in user_badges table (UserBadge model).
     */
    public function claim(Request $request, $badge_id)
    {
        $user = Auth::user();

        $badge = Badge::findOrFail($badge_id);

        // Prevent duplicate claims
        if (UserBadge::where('user_id', $user->id)->where('badge_id', $badge->badge_id)->exists()) {
            return redirect()->back()->with('info', 'You have already claimed this badge.');
        }

        // Check points
        $points = UserPoint::where('user_id', $user->id)->sum('points');
        if ($points < $badge->pointsRequired) {
            return redirect()->back()->with('error', 'Not enough points to claim this badge.');
        }

        // Create user_badges record
        UserBadge::create([
            'userBadge_id' => (string) Str::uuid(),
            'user_id'      => $user->id,
            'badge_id'     => $badge->badge_id,
            'earnedDate'   => now(),
        ]);

        return redirect()->back()->with('success', 'Badge claimed successfully!');
    }
}
