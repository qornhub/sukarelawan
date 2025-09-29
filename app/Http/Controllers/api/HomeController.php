<?php

namespace App\Http\Controllers\api;


use App\Models\Event;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\VolunteerProfile;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{

    public function profile(Request $request)
{
    try {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $profile = VolunteerProfile::where('user_id', $user->id)->first();
        if (!$profile) {
            Log::info("VolunteerProfile not found for user_id={$user->id}");
            return response()->json(['error' => 'Profile not found'], 404);
        }

        // age
        $age = null;
        if ($profile->dateOfBirth) {
            try {
                $age = Carbon::parse($profile->dateOfBirth)->age;
            } catch (\Exception $e) {
                Log::warning("Invalid dateOfBirth for volunteer {$profile->volunteer_id}: " . $e->getMessage());
            }
        }

        // profile photo: build full URL if stored as filename
        $photo = $profile->profilePhoto;
        if ($photo && !preg_match('/^https?:\\/\\//', $photo)) {
            $photo = url('images/profiles/' . ltrim($photo, '/'));
        } elseif (!$photo) {
            $photo = null;
        }

        // ====== New stats ======
        // total attended events (count rows in attendances for this user)
        $totalAttended = \App\Models\Attendance::where('user_id', $user->id)->count();

        // total points (sum the "points" column in user_points table for this user)
        // NOTE: adjust table/column name if yours differs. This uses the DB facade.
        $totalPoints = \Illuminate\Support\Facades\DB::table('user_points')
            ->where('user_id', $user->id)
            ->sum('points');

        // prepare payload
        $payload = [
            'user_id'      => $user->id,
            'volunteer_id' => $profile->volunteer_id,
            'name'         => $profile->name,
            'dateOfBirth'  => $profile->dateOfBirth,
            'age'          => $age,
            'profilePhoto' => $photo,
            'total_attended' => (int) $totalAttended,
            'total_points'   => (int) $totalPoints,
        ];

        Log::info('Volunteer profile payload', ['user_id' => $user->id, 'payload' => $payload]);

        return response()->json($payload, 200);
    } catch (\Throwable $ex) {
        Log::error('Profile fetch failed: ' . $ex->getMessage(), ['trace' => $ex->getTraceAsString()]);
        return response()->json(['error' => 'Server error'], 500);
    }
}



    
    // Get upcoming events the volunteer has registered for (approved only)
    public function upcoming(Request $request)
    {
        $user = Auth::user();
        $today = now()->toDateString();

        $upcomingEvents = Event::whereHas('registrations', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->where('status', 'approved'); // ✅ only approved registrations
            })
            ->whereDate('eventEnd', '>=', $today) // not ended
            ->orderBy('eventStart', 'asc')
            ->get(['event_id', 'eventTitle', 'eventStart', 'eventPoints']);

        return response()->json($upcomingEvents);
    }

    // Get attended events for the logged-in volunteer
   // Get attended events for the logged-in volunteer
public function attended(Request $request)
{
    $user = Auth::user();

    $attended = Attendance::with('event')
        ->where('user_id', $user->id)   // <-- fixed column name
        ->get()
        ->map(function ($attendance) {
            $event = $attendance->event;
            return [
                'attendance_id' => $attendance->attendance_id,
                'event_id'      => $event ? $event->event_id : null,
                'eventTitle'    => $event ? $event->eventTitle : null,
                'eventStart'    => $event ? $event->eventStart : null,
                'eventPoints'   => $event ? $event->eventPoints : null,
            ];
        });

    return response()->json($attended);
}

}
