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
    /**
     * Admin listing: show all events with filtering & sorting (same filters as volunteer).
     */
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $categoryId = $request->input('category');
        $location   = $request->input('location');
        $dateRange  = $request->input('date_range');
        $when       = $request->input('when'); // upcoming | past | all

        // Categories from DB
        $categories = EventCategory::orderBy('eventCategoryName')->get();

        // Fixed list of Malaysia states & federal territories for location filter
        $locations = [
            'Perlis','Kedah','Penang','Perak','Kelantan','Terengganu',
            'Pahang','Selangor','Negeri Sembilan','Melaka','Johor',
            'Sabah','Sarawak','Kuala Lumpur','Putrajaya','Labuan'
        ];

        // Base query. Eager-load relations used by the list view.
        $query = Event::with([
            'category',
            'organizer',
            'sdgs',
            'skills',
            'registrations.user',
            'registrations.user.volunteerProfile',
        ]);

        // Search (title / summary / description)
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('eventTitle', 'LIKE', "%{$search}%")
                  ->orWhere('eventSummary', 'LIKE', "%{$search}%")
                  ->orWhere('eventDescription', 'LIKE', "%{$search}%");
            });
        }

        // Category filter (expects event.category_id storing eventCategory_id)
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        // Location filter (assumes events table has 'state' column)
        if ($location) {
            $query->where('state', $location);
        }

        // Date range quick filters
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
                $end = null;
            }

            if ($start && $end) {
                $query->whereBetween('eventStart', [$start->toDateTimeString(), $end->toDateTimeString()]);
            }
        }

        // 'when' filter: upcoming / past
        if ($when) {
            $today = Carbon::today()->toDateString();
            if ($when === 'upcoming') {
                $query->whereDate('eventStart', '>=', $today);
            } elseif ($when === 'past') {
                $query->whereDate('eventEnd', '<', $today);
            }
        }

        // Sorting
        $sort = $request->input('sort', 'date'); // date | popular
        if ($sort === 'popular') {
            $query->withCount('registrations')
                  ->orderByDesc('registrations_count')
                  ->orderBy('eventStart', 'asc');
        } else {
            $query->orderBy('eventStart', 'asc');
        }

        // Pagination
        $events = $query->paginate(10)->withQueryString();

        // Return admin view
        return view('admin.events.index', [
            'events'     => $events,
            'categories' => $categories,
            'locations'  => $locations,
            'search'     => $search,
            'categoryId' => $categoryId,
            'location'   => $location,
            'date_range' => $dateRange,
            'when'       => $when,
            'sort'       => $sort,
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
