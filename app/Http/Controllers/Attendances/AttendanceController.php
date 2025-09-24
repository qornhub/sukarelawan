<?php

namespace App\Http\Controllers\Attendances;


use Carbon\Carbon;
use App\Models\Event;
use App\Models\Attendance;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * API endpoint when volunteer scans QR code
     * POST /api/ngo/events/{event}/scan
     */
    public function scan(Request $request, $event_id)
    {
        $user = $request->user(); // same as Auth::user()

        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // 1) Load event
        $event = Event::find($event_id);
        if (! $event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        // 2) Optionally: verify user is registered/confirmed for event
        // Uncomment and adapt if you have an event registration system:
        // $registered = \App\Models\EventRegistration::where('event_id', $event_id)
        //     ->where('user_id', $user->id)
        //     ->exists();
        // if (! $registered) {
        //     return response()->json(['message' => 'User not registered for this event'], 403);
        // }

        // 3) Prevent duplicate attendance (fast check)
        $existing = Attendance::where('event_id', $event_id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            // You can return 200 with a message, or 409 for conflict — choose what your app expects
            return response()->json(['message' => 'Already marked present', 'attendance' => $existing], 200);
        }

        // 4) Create attendance inside a DB transaction (safer for concurrency)
        try {
            $attendance = DB::transaction(function () use ($event, $user) {
                $points = 0;
                // Use the eventPoints column (fallback to 0)
                if (isset($event->eventPoints)) {
                    // cast to int to be safe
                    $points = (int) $event->eventPoints;
                } elseif (isset($event->points)) {
                    // alternative column name fallback
                    $points = (int) $event->points;
                }

                $a = Attendance::create([
                    'attendance_id'  => (string) Str::uuid(),
                    'event_id'       => $event->event_id ?? $event->id ?? null,
                    'user_id'        => $user->id,
                    'attendanceTime' => Carbon::now()->toDateTimeString(),
                    'pointEarned'    => $points,
                    'status'         => 'present',
                ]);

                return $a;
            });
        } catch (\Exception $e) {
            // log error and return friendly message
            Log::error('Attendance create failed: '.$e->getMessage());
            return response()->json(['message' => 'Could not record attendance'], 500);
        }

        return response()->json([
            'message' => 'Attendance recorded',
            'attendance' => $attendance,
        ], 201);
    }
}
