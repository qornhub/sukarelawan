<?php

namespace App\Http\Controllers\Attendances;

use Carbon\Carbon;
use App\Models\Event;
use App\Models\UserPoint;
use App\Models\Attendance;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\EventRegistration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Events\AttendanceCreated;
use Illuminate\Support\Facades\View;
use App\Notifications\EventStatusNotification;

use Illuminate\Support\Facades\Cache;
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

        // --- SERVER GUARD: disallow scanning after event end (inclusive) ---
        try {
            if (!empty($event->eventEnd)) {
                $eventHasEnded = Carbon::parse($event->eventEnd)
                    ->startOfDay()
                    ->lessThanOrEqualTo(Carbon::now()->startOfDay());
                if ($eventHasEnded) {
                    return response()->json(['message' => 'Event has ended. Attendance closed.'], 403);
                }
            }
        } catch (\Exception $ex) {
            // if date parse fails, be conservative and allow (or you could choose to block)
            Log::warning('Attendance scan: failed to parse eventEnd for event_id='.$event_id.' - '.$ex->getMessage());
        }
        // --- end guard ---

        // 2) Check if user is registered AND approved for this event
        $registration = EventRegistration::where('event_id', $event_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$registration) {
            return response()->json([
                'message' => 'You are not registered for this event'
            ], 403);
        }

        // Check registration status
        if ($registration->status === EventRegistration::STATUS_REJECTED) {
            return response()->json([
                'message' => 'Your registration has been rejected for this event'
            ], 403);
        }

        if ($registration->status !== EventRegistration::STATUS_APPROVED) {
            return response()->json([
                'message' => 'Your registration is pending approval for this event'
            ], 403);
        }

        // 3) Prevent duplicate attendance (fast check)
        $existing = Attendance::where('event_id', $event_id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Attendance already recorded for this event',
                'attendance' => $existing
            ], 200);
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

            // render row HTML using the partial
            $html = View::make('ngo.attendances._row', ['attendance' => $attendance])->render();

            // DEBUG: log that we're about to broadcast
            Log::info('attendance: broadcasting for attendance_id='.$attendance->attendance_id.' event_id='.$attendance->event_id);

            // broadcast
            event(new AttendanceCreated($attendance, $html));

            // DEBUG: log broadcast complete
            Log::info('attendance: broadcasted for attendance_id='.$attendance->attendance_id);

        } catch (\Exception $e) {
            Log::error('Attendance create failed: '.$e->getMessage());
            return response()->json(['message' => 'Could not record attendance'], 500);
        }
        // ✅ Send notification to user about attendance
$user = $attendance->user; // this assumes Attendance model has a 'user()' relationship
if ($user) {
    $user->notify(new EventStatusNotification($event, 'attended'));
}


        return response()->json([
            'message' => 'Attendance recorded successfully',
            'attendance' => $attendance,
        ], 201);
    }

    /**
     * Show list of volunteers who attended a specific event.
     */
    public function attendancesList($eventId, Request $request)
    {
        $event = Event::where('event_id', $eventId)->firstOrFail();

        $attendances = Attendance::with('user.volunteerProfile')
            ->where('event_id', $event->event_id)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($request->ajax()) {
            // return only table HTML (blade will see $ajax=true branch)
            return view('ngo.attendances.list', [
                'event' => $event,
                'attendances' => $attendances,
                'ajax' => true,
            ])->render();
        }

        return view('ngo.attendances.list', compact('event','attendances'));
    }

    public function destroy($eventId, $attendanceId)
    {
        $attendance = Attendance::where('event_id', $eventId)
            ->where('attendance_id', $attendanceId)
            ->firstOrFail();

        UserPoint::where('user_id', $attendance->user_id)
            ->where('event_id', $attendance->event_id)
            ->delete();

        // Now delete the attendance itself
        $attendance->delete();

        return response()->json(['message' => 'Attendance and related points deleted successfully']);
    }
}
