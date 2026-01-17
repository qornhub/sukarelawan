<?php

namespace App\Http\Controllers\Events;

use Carbon\Carbon;
use App\Models\Task;
use App\Models\Event;
use App\Models\EventComment;
use Illuminate\Http\Request;
use App\Models\EventCategory;
use App\Models\TaskAssignment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class EventDiscoveryController extends Controller
{
public function index(Request $request)
{
    $search     = $request->input('search');
    $categoryId = $request->input('category');
    $location   = $request->input('location');
    $dateRange  = $request->input('date_range');

    // Categories from DB
    $categories = EventCategory::orderBy('eventCategoryName')->get();

    // Custom categories from events table (no longer used in dropdown, but kept here in case you still need it elsewhere)
    $customCategories = Event::whereNotNull('custom_category')
        ->where('custom_category', '!=', '')
        ->distinct()
        ->pluck('custom_category');

    // Fixed Malaysia locations
    $locations = [
        'Perlis','Kedah','Penang','Perak','Kelantan','Terengganu',
        'Pahang','Selangor','Negeri Sembilan','Melaka','Johor',
        'Sabah','Sarawak','Kuala Lumpur','Putrajaya','Labuan'
    ];

    // Build query
    $query = Event::with(['category', 'organizer', 'registrations']);

    // Only future events
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
            $query->whereBetween('eventStart', [
                $start->toDateTimeString(),
                $end->toDateTimeString()
            ]);
        }
    }

    // Order upcoming first
    $query->orderBy('eventStart', 'asc');

    $events = $query->paginate(6)->appends($request->except('page'));

    if ($request->ajax()) {
        $html = view('partials.events.event_cards_volunteer', ['events' => $events])->render();

        return response()->json([
            'html'      => $html,
            'next_page' => $events->hasMorePages() ? $events->nextPageUrl() : null,
        ]);
    }

    return view('volunteer.index', [
        'events'           => $events,
        'categories'       => $categories,
        'customCategories' => $customCategories, // safe to keep; unused in new dropdown
        'locations'        => $locations,
        'search'           => $search,
        'categoryId'       => $categoryId,
        'location'         => $location,
        'date_range'       => $dateRange,
    ]);
}




    
   public function show($event_id)
{
   $event = Event::with([
    'category',
    'organizer',
    'organizer.ngoProfile',
    'organizer.volunteerProfile',
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



    return view('volunteer.events.show', compact('event', 'registrations', 'registeredCount', 'comments'));
}






public function show2($event_id)
{
    $event = Event::with([
        'category',
        'organizer',
        'organizer.ngoProfile',
        'organizer.volunteerProfile',
        'sdgs',
        'skills',
    ])->findOrFail($event_id);

    $userId = Auth::id();

    $registration = $event->registrations()
        ->where('user_id', $userId)
        ->first();

    if (!$registration) {
        return redirect()->back()->with('error', 'You have not registered for this event.');
    }

    // ✅ only show PENDING assignments in the modal
    $assignments = TaskAssignment::with('task')
        ->where('user_id', $userId)
        ->whereHas('task', function ($q) use ($event_id) {
            $q->where('event_id', $event_id);
        })
         ->whereIn('status', ['pending', 'accepted']) // ✅ show both
        ->orderBy('assignedDate', 'desc')
        ->get();

    $autoOpenAssignments = $assignments->count() > 0;

    $comments = EventComment::where('event_id', $event->event_id)
        ->with('user')
        ->orderBy('created_at', 'asc')
        ->paginate(5, ['*'], 'event_comments_page')
        ->withQueryString();

    return view('volunteer.profile.registrationEditDelete', compact(
        'event',
        'registration',
        'assignments',
        'autoOpenAssignments',
        'comments'
    ));
}



}
