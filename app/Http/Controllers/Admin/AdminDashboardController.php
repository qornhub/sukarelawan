<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Role;
use App\Models\Event;
use App\Models\BlogPost;
use App\Models\Attendance;


class AdminDashboardController extends Controller
{
 

public function index(Request $request)
{
    // Resolve role rows (adjust names if different)
    $volRole = Role::where('roleName', 'volunteer')->first();
    $ngoRole = Role::where('roleName', 'ngo')->first();

    $volRoleId = $volRole ? $volRole->role_id : null;
    $ngoRoleId = $ngoRole ? $ngoRole->role_id : null;

    // Totals (all time)
    $totalVolunteers = $volRoleId ? User::where('role_id', $volRoleId)->count() : 0;
    $totalNgos = $ngoRoleId ? User::where('role_id', $ngoRoleId)->count() : 0;
    $totalEvents = Event::count();
    $totalBlogs = BlogPost::count();

    // Helper: compute current month & previous month counts and percent change
    $now = Carbon::now();
    $startThisMonth = $now->copy()->startOfMonth();
    $endThisMonth = $now->copy()->endOfMonth();
    $startPrevMonth = $now->copy()->subMonth()->startOfMonth();
    $endPrevMonth = $now->copy()->subMonth()->endOfMonth();

    $makeChange = function ($currentCount, $previousCount) {
        // returns array: ['percentage' => float|null, 'direction' => 'up'|'down'|'same'|'new', 'label' => string]
        if ($previousCount == 0) {
            if ($currentCount == 0) {
                return [
                    'percentage' => 0.0,
                    'direction' => 'same',
                    'label' => '0% from past month',
                ];
            }
            // previous zero, current > 0 => new growth
            return [
                'percentage' => null,
                'direction' => 'up',
                'label' => 'New',
            ];
        }

        $diff = $currentCount - $previousCount;
        $pct = ($diff / max(1, $previousCount)) * 100.0;
        $pctRounded = round($pct, 1);

        if ($pctRounded === 0.0) {
            return [
                'percentage' => 0.0,
                'direction' => 'same',
                'label' => '0% from past month',
            ];
        }

        return [
            'percentage' => abs($pctRounded),
            'direction' => $pctRounded > 0 ? 'up' : 'down',
            'label' => ($pctRounded > 0 ? 'Up' : 'Down') . ' from past month',
        ];
    };

    // Volunteers this month / prev month
    $volThisMonth = $volRoleId
        ? User::where('role_id', $volRoleId)->whereBetween('created_at', [$startThisMonth, $endThisMonth])->count()
        : 0;
    $volPrevMonth = $volRoleId
        ? User::where('role_id', $volRoleId)->whereBetween('created_at', [$startPrevMonth, $endPrevMonth])->count()
        : 0;
    $volChange = $makeChange($volThisMonth, $volPrevMonth);

    // NGOs this month / prev month
    $ngoThisMonth = $ngoRoleId
        ? User::where('role_id', $ngoRoleId)->whereBetween('created_at', [$startThisMonth, $endThisMonth])->count()
        : 0;
    $ngoPrevMonth = $ngoRoleId
        ? User::where('role_id', $ngoRoleId)->whereBetween('created_at', [$startPrevMonth, $endPrevMonth])->count()
        : 0;
    $ngoChange = $makeChange($ngoThisMonth, $ngoPrevMonth);

    // Events this month / prev month
    $eventsThisMonth = Event::whereBetween('created_at', [$startThisMonth, $endThisMonth])->count();
    $eventsPrevMonth = Event::whereBetween('created_at', [$startPrevMonth, $endPrevMonth])->count();
    $eventsChange = $makeChange($eventsThisMonth, $eventsPrevMonth);

    // Blogs this month / prev month
    $blogsThisMonth = BlogPost::whereBetween('created_at', [$startThisMonth, $endThisMonth])->count();
    $blogsPrevMonth = BlogPost::whereBetween('created_at', [$startPrevMonth, $endPrevMonth])->count();
    $blogsChange = $makeChange($blogsThisMonth, $blogsPrevMonth);

    return view('admin.adminDashboard.index', [
        'totalVolunteers' => $totalVolunteers,
        'totalNgos' => $totalNgos,
        'totalEvents' => $totalEvents,
        'totalBlogs' => $totalBlogs,

        // month comparison helpers (use in blade)
        'volThisMonth' => $volThisMonth,
        'volPrevMonth' => $volPrevMonth,
        'volChange' => $volChange,

        'ngoThisMonth' => $ngoThisMonth,
        'ngoPrevMonth' => $ngoPrevMonth,
        'ngoChange' => $ngoChange,

        'eventsThisMonth' => $eventsThisMonth,
        'eventsPrevMonth' => $eventsPrevMonth,
        'eventsChange' => $eventsChange,

        'blogsThisMonth' => $blogsThisMonth,
        'blogsPrevMonth' => $blogsPrevMonth,
        'blogsChange' => $blogsChange,
    ]);
}


    /**
     * Return JSON data for charting registrations (volunteers vs ngos)
     * Query params: metric (optional) -> 'registrations' (default)
     * We'll return daily (last 7 days), weekly (last 12 weeks), monthly (last 12 months).
     */
    public function chartData(Request $request)
    {
        // Find role ids
        $volRole = Role::where('roleName', 'volunteer')->first();
        $ngoRole = Role::where('roleName', 'ngo')->first();

        $volRoleId = $volRole ? $volRole->role_id : null;
        $ngoRoleId = $ngoRole ? $ngoRole->role_id : null;

        // Daily (last 7 days)
        $now = Carbon::now()->startOfDay();
        $dailyLabels = [];
        $volDaily = [];
        $ngoDaily = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = (clone $now)->subDays($i);
            $dailyLabels[] = $d->format('d M');

            $volCount = $volRoleId
                ? User::where('role_id', $volRoleId)->whereDate('created_at', $d)->count()
                : 0;
            $ngoCount = $ngoRoleId
                ? User::where('role_id', $ngoRoleId)->whereDate('created_at', $d)->count()
                : 0;

            $volDaily[] = $volCount;
            $ngoDaily[] = $ngoCount;
        }

        // Weekly (last 12 weeks) - week labels are start date of week
        $weeklyLabels = [];
        $volWeekly = [];
        $ngoWeekly = [];
        $startOfThisWeek = Carbon::now()->startOfWeek(); // Mon as start
        for ($i = 11; $i >= 0; $i--) {
            $start = (clone $startOfThisWeek)->subWeeks($i)->startOfWeek();
            $end = (clone $start)->endOfWeek();
            $weeklyLabels[] = $start->format('d M');

            $volCount = $volRoleId
                ? User::where('role_id', $volRoleId)->whereBetween('created_at', [$start, $end])->count()
                : 0;
            $ngoCount = $ngoRoleId
                ? User::where('role_id', $ngoRoleId)->whereBetween('created_at', [$start, $end])->count()
                : 0;

            $volWeekly[] = $volCount;
            $ngoWeekly[] = $ngoCount;
        }

        // Monthly (last 12 months)
        $monthlyLabels = [];
        $volMonthly = [];
        $ngoMonthly = [];
        $startMonth = Carbon::now()->startOfMonth();
        for ($i = 11; $i >= 0; $i--) {
            $start = (clone $startMonth)->subMonths($i)->startOfMonth();
            $end = (clone $start)->endOfMonth();
            $monthlyLabels[] = $start->format('M Y');

            $volCount = $volRoleId
                ? User::where('role_id', $volRoleId)->whereBetween('created_at', [$start, $end])->count()
                : 0;
            $ngoCount = $ngoRoleId
                ? User::where('role_id', $ngoRoleId)->whereBetween('created_at', [$start, $end])->count()
                : 0;

            $volMonthly[] = $volCount;
            $ngoMonthly[] = $ngoCount;
        }

        return response()->json([
            'success' => true,
            'labels' => [
                'daily' => $dailyLabels,
                'weekly' => $weeklyLabels,
                'monthly' => $monthlyLabels,
            ],
            'datasets' => [
                'volunteers' => [
                    'daily' => $volDaily,
                    'weekly' => $volWeekly,
                    'monthly' => $volMonthly,
                ],
                'ngos' => [
                    'daily' => $ngoDaily,
                    'weekly' => $ngoWeekly,
                    'monthly' => $ngoMonthly,
                ],
            ],
        ]);
    }

    /**
 * Volunteer registrations over time for charting.
 * Query param: period = daily|weekly|monthly (default monthly)
 * Returns JSON: { success: true, labels: [...], counts: [...] }
 */
public function volunteerTrendData(Request $request)
{
    // Resolve volunteer role id
    $volRole = Role::where('roleName', 'volunteer')->first();
    $volRoleId = $volRole ? $volRole->role_id : null;

    $period = $request->get('period', 'monthly'); // daily, weekly, monthly

    if ($period === 'daily') {
        $days = 30; // last 30 days
        $labels = [];
        $counts = [];
        $now = Carbon::now()->startOfDay();
        for ($i = $days - 1; $i >= 0; $i--) {
            $d = (clone $now)->subDays($i);
            $labels[] = $d->format('d M');
            $cnt = $volRoleId
                ? User::where('role_id', $volRoleId)->whereDate('created_at', $d)->count()
                : 0;
            $counts[] = $cnt;
        }
    } elseif ($period === 'weekly') {
        // last 12 weeks
        $labels = [];
        $counts = [];
        $startOfThisWeek = Carbon::now()->startOfWeek();
        for ($i = 11; $i >= 0; $i--) {
            $start = (clone $startOfThisWeek)->subWeeks($i)->startOfWeek();
            $end = (clone $start)->endOfWeek();
            $labels[] = $start->format('d M');
            $cnt = $volRoleId
                ? User::where('role_id', $volRoleId)->whereBetween('created_at', [$start, $end])->count()
                : 0;
            $counts[] = $cnt;
        }
    } else {
        // monthly - last 12 months
        $labels = [];
        $counts = [];
        $startMonth = Carbon::now()->startOfMonth();
        for ($i = 11; $i >= 0; $i--) {
            $start = (clone $startMonth)->subMonths($i)->startOfMonth();
            $end = (clone $start)->endOfMonth();
            $labels[] = $start->format('M Y');
            $cnt = $volRoleId
                ? User::where('role_id', $volRoleId)->whereBetween('created_at', [$start, $end])->count()
                : 0;
            $counts[] = $cnt;
        }
    }

    return response()->json([
        'success' => true,
        'labels' => $labels,
        'counts' => $counts,
    ]);
}

/**
 * Volunteer activity stats (Active vs Inactive).
 * Active = distinct volunteers with a 'present' attendance in last 30 days.
 * Returns JSON: { success: true, labels: ['Active (30d)','Inactive'], counts: [active, inactive] }
 */
public function volunteerActiveStats()
{
    // Resolve volunteer role id
    $volRole = Role::where('roleName', 'volunteer')->first();
    $volRoleId = $volRole ? $volRole->role_id : null;

    $totalVols = $volRoleId ? User::where('role_id', $volRoleId)->count() : 0;

    // active volunteers in last 30 days based on Attendance::status == 'present'
    $thirtyDaysAgo = Carbon::now()->subDays(30);
    $active30 = Attendance::where('status', 'present')
        ->where('created_at', '>=', $thirtyDaysAgo)
        ->distinct()
        ->count('user_id');

    // safety: ensure active not greater than total
    $active30 = min($active30, $totalVols);

    $inactive = max(0, $totalVols - $active30);

    return response()->json([
        'success' => true,
        'labels' => ['Active (30d)', 'Inactive'],
        'counts' => [$active30, $inactive],
    ]);
}

/**
 * NGO registrations over time for charting.
 * Query param: period = daily|weekly|monthly (default monthly)
 * Returns JSON: { success: true, labels: [...], counts: [...] }
 */
public function ngoTrendData(Request $request)
{
    // Resolve NGO role id
    $ngoRole = Role::where('roleName', 'ngo')->first();
    $ngoRoleId = $ngoRole ? $ngoRole->role_id : null;

    $period = $request->get('period', 'monthly'); // daily, weekly, monthly

    if ($period === 'daily') {
        $days = 30; // last 30 days
        $labels = [];
        $counts = [];
        $now = Carbon::now()->startOfDay();
        for ($i = $days - 1; $i >= 0; $i--) {
            $d = (clone $now)->subDays($i);
            $labels[] = $d->format('d M');
            $cnt = $ngoRoleId
                ? User::where('role_id', $ngoRoleId)->whereDate('created_at', $d)->count()
                : 0;
            $counts[] = $cnt;
        }
    } elseif ($period === 'weekly') {
        // last 12 weeks
        $labels = [];
        $counts = [];
        $startOfThisWeek = Carbon::now()->startOfWeek();
        for ($i = 11; $i >= 0; $i--) {
            $start = (clone $startOfThisWeek)->subWeeks($i)->startOfWeek();
            $end = (clone $start)->endOfWeek();
            $labels[] = $start->format('d M');
            $cnt = $ngoRoleId
                ? User::where('role_id', $ngoRoleId)->whereBetween('created_at', [$start, $end])->count()
                : 0;
            $counts[] = $cnt;
        }
    } else {
        // monthly - last 12 months
        $labels = [];
        $counts = [];
        $startMonth = Carbon::now()->startOfMonth();
        for ($i = 11; $i >= 0; $i--) {
            $start = (clone $startMonth)->subMonths($i)->startOfMonth();
            $end = (clone $start)->endOfMonth();
            $labels[] = $start->format('M Y');
            $cnt = $ngoRoleId
                ? User::where('role_id', $ngoRoleId)->whereBetween('created_at', [$start, $end])->count()
                : 0;
            $counts[] = $cnt;
        }
    }

    return response()->json([
        'success' => true,
        'labels' => $labels,
        'counts' => $counts,
    ]);
}

/**
 * NGO activity stats (Active vs Inactive).
 * Active: NGOs that created an event in last 30 days (adjust if you use different column).
 */
public function ngoActiveStats()
{
    $ngoRole = Role::where('roleName', 'ngo')->first();
    $ngoRoleId = $ngoRole ? $ngoRole->role_id : null;

    $totalNgos = $ngoRoleId ? User::where('role_id', $ngoRoleId)->count() : 0;

    $thirtyDaysAgo = Carbon::now()->subDays(30);

    // use events.user_id (your events table) instead of created_by
    $activeNgos = Event::where('created_at', '>=', $thirtyDaysAgo)
        ->distinct()
        ->count('user_id');

    $activeNgos = min($activeNgos, $totalNgos);
    $inactive = max(0, $totalNgos - $activeNgos);

    return response()->json([
        'success' => true,
        'labels' => ['Active (30d)', 'Inactive'],
        'counts' => [$activeNgos, $inactive],
    ]);
}


}
