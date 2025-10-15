<?php

namespace App\Http\Controllers\NGO;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Attendance;
use App\Models\BlogPost;
use App\Models\Role;
use App\Models\EventCategory;
use App\Models\User;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $ngoId = $user->id;

        // 1️⃣ Events owned by this NGO
        $events = Event::where('user_id', $ngoId)->latest()->get();
        $eventIds = $events->pluck('event_id')->filter()->values()->all();
        $totalEvents = $events->count();

        // 2️⃣ Registrations & Unique Volunteers
        $totalRegistrations = 0;
        $uniqueVolunteers = 0;

        if (!empty($eventIds)) {
            $totalRegistrations = EventRegistration::whereIn('event_id', $eventIds)->count();
            $uniqueVolunteers = EventRegistration::whereIn('event_id', $eventIds)
                ->distinct()
                ->count('user_id');
        }

        // 3️⃣ Completed Events
        $today = Carbon::today();
        $completedEvents = $events->filter(function ($ev) use ($today) {
            $end = $ev->eventEnd ?? $ev->event_end ?? $ev->end_date ?? null;
            if (!$end) return false;
            return Carbon::parse($end)->startOfDay()->lte($today);
        })->count();

        // 4️⃣ Attendance Rate Calculation (based on 'present' status only)
        $totalAttended = 0;
        if (!empty($eventIds)) {
            $totalAttended = Attendance::whereIn('event_id', $eventIds)
                ->where('status', 'present')
                ->count();
        }

        $attendanceRate = $totalRegistrations > 0
            ? round(($totalAttended / $totalRegistrations) * 100, 2)
            : 0;

        // 5️⃣ Category Distribution
        $categoryData = [];
        if (!empty($eventIds)) {
            $catRows = Event::select('category_id', DB::raw('count(*) as cnt'))
                ->whereIn('event_id', $eventIds)
                ->groupBy('category_id')
                ->get();

            foreach ($catRows as $row) {
                $cat = EventCategory::where('eventCategory_id', $row->category_id)->first();
                $label = $cat->name ?? $cat->eventCategoryName ?? 'Uncategorized';
                $categoryData[] = [
                    'label' => $label,
                    'count' => (int)$row->cnt,
                ];
            }
        }

        // 6️⃣ Event Participation Trends (daily, monthly, yearly)
        [$eventTrendDailyLabels, $eventTrendDailyCounts] = $this->registrationsByDays($eventIds, 30);
        [$eventTrendMonthsLabels, $eventTrendMonthsCounts] = $this->registrationsByMonths($eventIds, 12);
        [$eventTrendYearsLabels, $eventTrendYearsCounts]   = $this->registrationsByYears($eventIds, 5);

        // 7️⃣ Blog Stats
        $totalBlogs = BlogPost::where('user_id', $ngoId)->count();
        [$blogDailyLabels, $blogDailyCounts] = $this->blogsByDays($ngoId, 30);
[$blogMonthlyLabels, $blogMonthlyCounts] = $this->blogsByMonths($ngoId, 12);
[$blogYearlyLabels, $blogYearlyCounts] = $this->blogsByYears($ngoId, 5);


        // 8️⃣ Role Check
        $isNgo = false;
        $ngoRole = Role::where('roleName', 'ngo')->first();
        if ($ngoRole) {
            $isNgo = User::where('id', $ngoId)
                ->where('role_id', $ngoRole->role_id)
                ->exists();
        }

        // 9️⃣ Recent Events
        $recentEvents = Event::where('user_id', $ngoId)
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get()
            ->map(function ($ev) {
                $img = $ev->eventImage ? asset('images/events/' . $ev->eventImage) : asset('images/events/default-event.jpg');
                return [
                    'id' => $ev->event_id,
                    'title' => $ev->eventTitle ?? 'Untitled Event',
                    'image' => $img,
                    'date' => Carbon::parse($ev->eventStart ?? $ev->event_date ?? now())->format('d M Y'),
                ];
            });

        // 🔟 Top Attendance Events
        $topAttendanceEvents = collect();

        if (!empty($eventIds)) {
            $topAttendanceEvents = Event::whereIn('event_id', $eventIds)
                ->get()
                ->map(function ($event) {
                    $totalRegistered = EventRegistration::where('event_id', $event->event_id)->count();
                    $totalAttended = Attendance::where('event_id', $event->event_id)
                        ->where('status', 'present')
                        ->count();
                    $percent = $totalRegistered > 0
                        ? round(($totalAttended / $totalRegistered) * 100, 2)
                        : 0;

                    $eventImage = $event->eventImage
                        ? asset('images/events/' . $event->eventImage)
                        : asset('images/events/default-event.jpg');

                    return [
                        'id' => $event->event_id,
                        'title' => $event->eventTitle ?? 'Untitled Event',
                        'image' => $eventImage,
                        'attendance_rate' => $percent,
                        'attended' => $totalAttended,
                        'registered' => $totalRegistered,
                    ];
                })
                ->sortByDesc('attendance_rate')
                ->take(3);
        }

        return view('ngo.dashboard_layout', [
            'user' => $user,
            'events' => $events,
            'totalEvents' => $totalEvents,
            'totalRegistrations' => $totalRegistrations,
            'uniqueVolunteers' => $uniqueVolunteers,
            'completedEvents' => $completedEvents,
            'totalAttended' => $totalAttended,
            'attendanceRate' => $attendanceRate,
            'categoryData' => $categoryData,
            'eventTrendDailyLabels' => $eventTrendDailyLabels,
            'eventTrendDailyCounts' => $eventTrendDailyCounts,
            'eventTrendMonthsLabels' => $eventTrendMonthsLabels,
            'eventTrendMonthsCounts' => $eventTrendMonthsCounts,
            'eventTrendYearsLabels' => $eventTrendYearsLabels,
            'eventTrendYearsCounts' => $eventTrendYearsCounts,
            'totalBlogs' => $totalBlogs,
            'blogDailyLabels' => $blogDailyLabels,
'blogDailyCounts' => $blogDailyCounts,
'blogMonthlyLabels' => $blogMonthlyLabels,
'blogMonthlyCounts' => $blogMonthlyCounts,
'blogYearlyLabels' => $blogYearlyLabels,
'blogYearlyCounts' => $blogYearlyCounts,

            
            'recentEvents' => $recentEvents,
            'topAttendanceEvents' => $topAttendanceEvents,
            'isNgo' => $isNgo,
        ]);
    }

    // 🔹 Daily registrations
    protected function registrationsByDays(array $eventIds, int $days = 30)
    {
        $labels = [];
        $counts = [];
        $now = Carbon::now()->startOfDay();

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = (clone $now)->subDays($i);
            $labels[] = $date->format('d M');
            $cnt = empty($eventIds) ? 0 :
                EventRegistration::whereIn('event_id', $eventIds)
                    ->whereDate('created_at', $date)
                    ->count();
            $counts[] = $cnt;
        }
        return [$labels, $counts];
    }

    // 🔹 Monthly registrations
    protected function registrationsByMonths(array $eventIds, int $months = 12)
    {
        $labels = [];
        $counts = [];
        $now = Carbon::now()->startOfMonth();

        for ($i = $months - 1; $i >= 0; $i--) {
            $start = (clone $now)->subMonths($i)->startOfMonth();
            $end = (clone $now)->subMonths($i)->endOfMonth();
            $labels[] = $start->format('M Y');
            $cnt = empty($eventIds) ? 0 :
                EventRegistration::whereIn('event_id', $eventIds)
                    ->whereBetween('created_at', [$start, $end])
                    ->count();
            $counts[] = $cnt;
        }
        return [$labels, $counts];
    }

    // 🔹 Yearly registrations
    protected function registrationsByYears(array $eventIds, int $years = 5)
    {
        $labels = [];
        $counts = [];
        $currentYear = Carbon::now()->year;

        for ($y = $currentYear - ($years - 1); $y <= $currentYear; $y++) {
            $labels[] = (string)$y;
            $start = Carbon::create($y, 1, 1)->startOfDay();
            $end = Carbon::create($y, 12, 31)->endOfDay();
            $cnt = empty($eventIds) ? 0 :
                EventRegistration::whereIn('event_id', $eventIds)
                    ->whereBetween('created_at', [$start, $end])
                    ->count();
            $counts[] = $cnt;
        }
        return [$labels, $counts];
    }

    // 🔹 Blog Trend Helpers
protected function blogsByDays($ngoId, $days = 30)
{
    $labels = [];
    $counts = [];
    $now = Carbon::now()->startOfDay();

    for ($i = $days - 1; $i >= 0; $i--) {
        $date = (clone $now)->subDays($i);
        $labels[] = $date->format('d M');
        $cnt = BlogPost::where('user_id', $ngoId)
            ->whereDate('created_at', $date)
            ->count();
        $counts[] = $cnt;
    }
    return [$labels, $counts];
}

protected function blogsByMonths($ngoId, $months = 12)
{
    $labels = [];
    $counts = [];
    $now = Carbon::now()->startOfMonth();

    for ($i = $months - 1; $i >= 0; $i--) {
        $start = (clone $now)->subMonths($i)->startOfMonth();
        $end = (clone $now)->subMonths($i)->endOfMonth();
        $labels[] = $start->format('M Y');
        $cnt = BlogPost::where('user_id', $ngoId)
            ->whereBetween('created_at', [$start, $end])
            ->count();
        $counts[] = $cnt;
    }
    return [$labels, $counts];
}

protected function blogsByYears($ngoId, $years = 5)
{
    $labels = [];
    $counts = [];
    $currentYear = Carbon::now()->year;

    for ($y = $currentYear - ($years - 1); $y <= $currentYear; $y++) {
        $labels[] = (string)$y;
        $start = Carbon::create($y, 1, 1)->startOfDay();
        $end = Carbon::create($y, 12, 31)->endOfDay();
        $cnt = BlogPost::where('user_id', $ngoId)
            ->whereBetween('created_at', [$start, $end])
            ->count();
        $counts[] = $cnt;
    }
    return [$labels, $counts];
}

}
