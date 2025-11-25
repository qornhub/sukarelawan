<?php

namespace App\Http\Controllers\Events;

use Carbon\Carbon;
use App\Models\Event;
use App\Models\EventComment;
use App\Models\EventCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminEventDiscoveryController extends Controller
{
public function index(Request $request)
{
    $search     = $request->input('search');
    $categoryId = $request->input('category');
    $location   = $request->input('location');
    $dateRange  = $request->input('date_range');

    // Categories from DB
    $categories = EventCategory::orderBy('eventCategoryName')->get();

    // Fixed list of Malaysia states & federal territories for location filter
    $locations = [
        'Perlis','Kedah','Penang','Perak','Kelantan','Terengganu',
        'Pahang','Selangor','Negeri Sembilan','Melaka','Johor',
        'Sabah','Sarawak','Kuala Lumpur','Putrajaya','Labuan'
    ];

    // Build query
    $query = Event::with(['category', 'organizer', 'registrations']);

    // --- Only future events (move this into query so pagination counts only valid events) ---
    $query->whereDate('eventStart', '>=', Carbon::today()->toDateString());

    // Search filter
    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('eventTitle', 'LIKE', "%{$search}%")
              ->orWhere('eventSummary', 'LIKE', "%{$search}%")
              ->orWhere('eventDescription', 'LIKE', "%{$search}%");
        });
    }

    // -------------- CATEGORY FILTER (UPDATED FOR "other") --------------
    if ($categoryId) {
        if ($categoryId === 'other') {
            // Show all events that use a custom category (not admin-defined)
            $query->whereNotNull('custom_category')
                  ->where('custom_category', '!=', '');
        } else {
            // Normal predefined category (admin-defined)
            $query->where('category_id', $categoryId);
        }
    }
    // -------------------------------------------------------------------

    // Location filter
    if ($location) {
        $query->where('state', $location);
    }

    // Date range filter
    if ($dateRange) {
        $now = Carbon::now();

        if ($dateRange === 'this_week') {
            $start = (clone $now)->startOfWeek();
            $end   = (clone $now)->endOfWeek();
        } elseif ($dateRange === 'next_week') {
            $start = (clone $now)->addWeek()->startOfWeek();
            $end   = (clone $now)->addWeek()->endOfWeek();
        } elseif ($dateRange === 'this_month') {
            $start = (clone $now)->startOfMonth();
            $end   = (clone $now)->endOfMonth();
        } else {
            $start = null;
            $end   = null;
        }

        if ($start && $end) {
            $query->whereBetween('eventStart', [$start->toDateTimeString(), $end->toDateTimeString()]);
        }
    }

    // Order by nearest first (optional)
    $query->orderBy('eventStart', 'asc');

    // paginate and keep the current query string so nextPageUrl preserves filters
    $events = $query->paginate(6)->appends($request->except('page'));

    // If AJAX request, return JSON with rendered HTML of just the event cards
    if ($request->ajax()) {
        $html = view('partials.events.event_cards_admin', ['events' => $events])->render();

        return response()->json([
            'html'      => $html,
            'next_page' => $events->hasMorePages() ? $events->nextPageUrl() : null,
        ]);
    }

    // Normal full-page render
    return view('admin.events.index', [
        'events'     => $events,
        'categories' => $categories,
        'locations'  => $locations,
        'search'     => $search,
        'categoryId' => $categoryId,
        'location'   => $location,
        'date_range' => $dateRange,
    ]);
}



    /**
     * Admin show: view single event + registrations + comments.
     */
    public function show($event_id)
    {
        $event = Event::with([
            'category',
            'organizer',
            'sdgs',
            'skills',
            'registrations.user',
            'registrations.user.volunteerProfile',
        ])->findOrFail($event_id);

        $registrations   = $event->registrations->sortByDesc('created_at')->values();
        $registeredCount = $registrations->count();

        $comments = EventComment::where('event_id', $event->event_id)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->paginate(5, ['*'], 'event_comments_page')
            ->withQueryString();

        return view('admin.events.show', compact('event', 'registrations', 'registeredCount', 'comments'));
    }
}
