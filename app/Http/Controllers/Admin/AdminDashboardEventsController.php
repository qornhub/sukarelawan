<?php

namespace App\Http\Controllers\Admin;


use Carbon\Carbon;
use App\Models\Event;
use App\Models\Attendance;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\EventCategory;
use App\Models\EventRegistration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class AdminDashboardEventsController extends Controller
{
    /**
     * Event creation trend (monthly/weekly/daily) using Eloquent
     */
    public function creationTrend(Request $request)
    {
        $period = $request->get('period', 'monthly');

        $labels = [];
        $counts = [];

        if ($period === 'daily') {
            $days = 30;
            $now = Carbon::now()->startOfDay();
            for ($i = $days - 1; $i >= 0; $i--) {
                $d = (clone $now)->subDays($i);
                $labels[] = $d->format('d M');
                $counts[] = Event::whereDate('created_at', $d)->count();
            }
        } elseif ($period === 'weekly') {
            $weeks = 12;
            $startOfThisWeek = Carbon::now()->startOfWeek();
            for ($i = $weeks - 1; $i >= 0; $i--) {
                $start = (clone $startOfThisWeek)->subWeeks($i)->startOfWeek();
                $end = (clone $start)->endOfWeek();
                $labels[] = $start->format('d M');
                $counts[] = Event::whereBetween('created_at', [$start->toDateTimeString(), $end->toDateTimeString()])->count();
            }
        } else {
            $months = 12;
            $startMonth = Carbon::now()->startOfMonth();
            for ($i = $months - 1; $i >= 0; $i--) {
                $start = (clone $startMonth)->subMonths($i)->startOfMonth();
                $end = (clone $start)->endOfMonth();
                $labels[] = $start->format('M Y');
                $counts[] = Event::whereBetween('created_at', [$start->toDateTimeString(), $end->toDateTimeString()])->count();
            }
        }

        return response()->json(['success' => true, 'labels' => $labels, 'counts' => $counts]);
    }

    /**
     * Event category distribution using Eloquent relationships
     */
    public function categoryDistribution()
{
    // Try preferred Eloquent route: categories with events count
    try {
        $categories = EventCategory::withCount('events')
            ->orderByDesc('events_count')
            ->get(['eventCategory_id', 'eventCategoryName', 'events_count']);
    } catch (\Throwable $ex) {
        Log::warning('categoryDistribution: EventCategory::withCount failed: '.$ex->getMessage());
        $categories = collect();
    }

    $labels = $categories->pluck('eventCategoryName')->map(function ($v) { return $v ?? 'Uncategorized'; })->toArray();
    $counts = $categories->pluck('events_count')->map(function ($v) { return (int) $v; })->toArray();

    // If nothing useful found or total sum is zero, fallback to DB grouping (works without EventCategory model)
    if (empty($labels) || empty($counts) || array_sum($counts) === 0) {
        $rows = DB::table('events as e')
            ->leftJoin('event_categories as ec', 'e.category_id', '=', 'ec.eventCategory_id')
            ->select(DB::raw("COALESCE(ec.eventCategoryName, 'Uncategorized') as category"), DB::raw('COUNT(e.event_id) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        $labels = $rows->pluck('category')->toArray();
        $counts = $rows->pluck('total')->map(fn($v) => (int)$v)->toArray();

        if (empty($labels) || empty($counts)) {
            Log::debug('categoryDistribution: fallback returned empty (no events or categories present).');
            // return empty arrays so frontend can handle gracefully
            return response()->json(['success' => true, 'labels' => [], 'counts' => []]);
        }
    }

    return response()->json(['success' => true, 'labels' => $labels, 'counts' => $counts]);
}


    /**
     * Event registration status summary (status, count)
     */
    public function registrationStatusSummary()
    {
        // If EventRegistration model exists, use it
        if (class_exists(EventRegistration::class)) {
            $rows = EventRegistration::query()
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->get();
            $labels = $rows->pluck('status')->toArray();
            $counts = $rows->pluck('total')->toArray();
        } else {
            // fallback: query DB via Event model relationships
            $registrations = Event::with('registrations')->get()->pluck('registrations')->flatten();
            $grouped = $registrations->groupBy('status')->map->count();
            $labels = $grouped->keys()->toArray();
            $counts = $grouped->values()->toArray();
        }

        return response()->json(['success' => true, 'labels' => $labels, 'counts' => $counts]);
    }

    /**
     * Event attendance rate top events (Top N) using Eloquent
     */
    public function attendanceRateTopEvents(Request $request)
    {
        $limit = intval($request->get('limit', 10));
        // eager load counts to avoid N+1
        $events = Event::withCount([
            'registrations as registered_cnt' => function ($q) {
                $q->select(DB::raw('COUNT(DISTINCT user_id)'));
            },
            'attendances as attended_cnt' => function ($q) {
                $q->where('status', 'present')->select(DB::raw('COUNT(DISTINCT user_id)'));
            },
        ])->get();

        $rows = $events->map(function ($e) {
            $registered = intval($e->registered_cnt ?? 0);
            $attended = intval($e->attended_cnt ?? 0);
            $rate = $registered > 0 ? round(($attended / $registered) * 100, 1) : 0.0;
            return [
                'event_id' => $e->event_id,
                'title' => $e->eventTitle,
                'registered' => $registered,
                'attended' => $attended,
                'attendance_rate' => $rate,
            ];
        })->sortByDesc('attendance_rate')->values()->take($limit);

        $labels = $rows->map(fn($r) => Str::limit($r['title'], 40))->toArray();
        $rates = $rows->pluck('attendance_rate')->toArray();
        $registered = $rows->pluck('registered')->toArray();
        $attended = $rows->pluck('attended')->toArray();

        return response()->json([
            'success' => true,
            'labels' => $labels,
            'attendance_rate' => $rates,
            'registered_counts' => $registered,
            'attended_counts' => $attended,
        ]);
    }

    /**
     * Active vs Completed events (eventEnd compared to now)
     */
    public function activeVsCompleted()
    {
        $now = Carbon::now();

        // Use Eloquent queries
        $active = Event::where('eventEnd', '>=', $now)->count();
        $completed = Event::where('eventEnd', '<', $now)->count();

        return response()->json([
            'success' => true,
            'labels' => ['Active (ongoing/upcoming)', 'Completed'],
            'counts' => [$active, $completed],
        ]);
    }
}
