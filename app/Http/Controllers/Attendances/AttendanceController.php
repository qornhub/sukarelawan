<?php

namespace App\Http\Controllers\Attendances;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


class AttendanceController extends Controller
{
    /**
     * API endpoint when volunteer scans QR code
     */
    public function scan(Request $request, $event_id)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Check if already attended
        $existing = Attendance::where('event_id', $event_id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Already marked present'], 200);
        }

        $attendance = Attendance::create([
            'attendance_id' => (string) Str::uuid(),
            'event_id'      => $event_id,
            'user_id'       => $user->id,
            'attendanceTime'=> Carbon::now(),
            'pointEarned'   => 10, // adjust as needed
            'status'        => 'present',
        ]);

        return response()->json([
            'message' => 'Attendance recorded',
            'attendance' => $attendance,
        ], 201);
    }
}
