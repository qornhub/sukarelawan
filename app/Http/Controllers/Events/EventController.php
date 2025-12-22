<?php

namespace App\Http\Controllers\Events;

use App\Models\Sdg;
use App\Models\Event;
use App\Models\Skill;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\EventCategory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    // Show NGO's own events
    public function index()
    {
        $now = Carbon::now();

        $events = Event::where('user_id', Auth::id())
            ->where('eventEnd', '>=', $now) // datetime comparison
            ->orderBy('eventStart', 'desc')
            ->get();

        return view('ngo.events.index', compact('events'));
    }

    // Helper: determine if an event has ended (server-side guard)
    protected function eventHasEnded(Event $event): bool
    {
        if (empty($event->eventEnd)) {
            return false;
        }

        try {
            // Compare using date granularity (startOfDay) so events that end today are considered ended.
            $end = Carbon::parse($event->eventEnd)->startOfDay();
            return Carbon::now()->startOfDay()->greaterThanOrEqualTo($end);
        } catch (\Exception $ex) {
            // If parsing fails, be permissive (do not block). Change to `true` to be strict.
            return false;
        }
    }

    /**
     * AJAX endpoint: calculate event points based on category, start/end and maximum.
     * Uses the controller's private calculateEventPoints() method (single source of truth).
     */
    public function calcPoints(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id'  => 'required', // validate existence more flexibly below
            'eventStart'   => 'required|date',
            // Allow equal start/end to match store/update validation and client-side check.
            'eventEnd'     => 'required|date|after_or_equal:eventStart',
            'eventMaximum' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'calculated' => false,
                'points'     => 0,
                'errors'     => $validator->errors()->messages(),
            ], 422);
        }

        try {
            $catId = $request->input('category_id');

            // Robust category lookup:
            // - first try primary key id()
            // - then try eventCategory_id (if your table uses that column)
            $category = EventCategory::find($catId);
            if (! $category) {
                $category = EventCategory::where('eventCategory_id', $catId)->first();
            }

            // Use admin-configured basePoints (DB default is already 10 if not set)
            $categoryBase = $category ? (int) $category->basePoints : 10;

            // Reuse your private calculation method — exact same logic
            $points = $this->calculateEventPoints(
                $categoryBase,
                $request->input('eventStart'),
                $request->input('eventEnd'),
                $request->input('eventMaximum')
            );

            return response()->json([
                'calculated' => true,
                'points'     => (int) $points,
            ]);
        } catch (\Throwable $e) {
            // Log full context to ease debugging
            Log::error('calcPoints error: ' . $e->getMessage(), [
                'payload' => $request->all(),
                'trace'   => $e->getTraceAsString(),
            ]);
            return response()->json([
                'calculated' => false,
                'points'     => 0,
                'message'    => 'Unable to calculate points.',
            ], 500);
        }
    }

    // Show create form
    public function create()
    {
        $categories = EventCategory::orderBy('eventCategoryName')->get();
        $sdgs = Sdg::orderBy('sdg_number')->orderBy('sdgName')->get();
        $skills = Skill::orderBy('skillName')->get();

        return view('ngo.events.create', compact('categories', 'sdgs', 'skills'));
    }

    // --- store() (replace the existing store method) ---
   // --- store() (replace the existing store method) ---
public function store(Request $request)
{
    // Normalize request inputs
    $input = [
        'eventTitle'       => $request->input('eventTitle') ?? $request->input('event_title'),
        'eventPoints'      => $request->input('eventPoints') ?? $request->input('reward_points') ?? $request->input('event_points'),
        'eventStart'       => $request->input('eventStart') ?? $request->input('start_date') ?? $request->input('event_start'),
        'eventEnd'         => $request->input('eventEnd') ?? $request->input('end_date') ?? $request->input('event_end'),
        'eventSummary'     => $request->input('eventSummary') ?? $request->input('event_summary'),
        'eventDescription' => $request->input('eventDescription') ?? $request->input('event_description'),
        'eventImpact'      => $request->input('eventImpact') ?? $request->input('event_impact'),
        'venueName'        => $request->input('venueName') ?? $request->input('event_location') ?? $request->input('venue_name'),
        'zipCode'          => $request->input('zipCode') ?? $request->input('zip_code'),
        'city'             => $request->input('city'),
        'state'            => $request->input('state'),
        'country'          => $request->input('country'),
        'eventMaximum'     => $request->input('eventMaximum') ?? $request->input('event_maximum') ?? $request->input('max_attendees'),
        'category_id'      => $request->input('category_id') ?? $request->input('category') ?? $request->input('event_category_id'),
        'custom_category'  => $request->input('custom_category'),
        'requirements'     => $request->input('requirements') ?? $request->input('requirements_text') ?? null,
    ];

    // Validation rules - eventPoints is now optional (auto-generated if not provided)
    $rules = [
        'eventTitle'       => 'required|string|max:255',
        'eventPoints'      => 'nullable|integer|min:0',
        'eventStart'       => 'required|date|after_or_equal:now',
        'eventEnd'         => 'required|date|after_or_equal:eventStart',

        'eventSummary'     => 'nullable|string|max:500',
        'eventDescription' => 'required|string',
        'venueName'        => 'required|string|max:255',
        'zipCode'          => 'nullable|string|max:20',
        'city'             => 'required|string|max:100',
        'state'            => 'required|string|max:100',
        'country'          => 'required|string|max:100',
        'eventMaximum'     => 'nullable|integer|min:1',

        // allow existing category or "other" with custom
        'category_id'      => 'required|string',
        'custom_category'  => 'nullable|string|max:255|required_if:category_id,other',

        'sdgs'             => 'nullable|array',
        'sdgs.*'           => 'exists:sdgs,sdg_id',
        'skills'           => 'nullable|array',
        'skills.*'         => 'exists:skills,skill_id',
        'requirements'     => 'nullable|string|max:2000',

        // image is OPTIONAL
        'eventImage'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
    ];

    $validator = Validator::make(
        $input + $request->only('sdgs', 'skills', 'eventImage'),
        $rules
    );

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput($request->all());
    }

    // ---------- Image handling ----------
    $defaultEventImage = 'default_event.jpg';
    $imageFileName = $defaultEventImage; // default if user uploads nothing

    if ($request->hasFile('eventImage') || $request->hasFile('event_image')) {
        $file = $request->hasFile('eventImage') ? $request->file('eventImage') : $request->file('event_image');

        $safeOriginal = preg_replace('/\s+/', '_', $file->getClientOriginalName());
        $imageFileName = time() . '_event_' . $safeOriginal;

        $destFolder = public_path('images/events');
        if (! is_dir($destFolder)) {
            mkdir($destFolder, 0755, true);
        }

        $file->move($destFolder, $imageFileName);
    }

    // Generate event_id
    $eventId = (string) Str::uuid();

    // --- AUTO-GENERATE POINTS ---
    // robust lookup (covers eventCategory_id or id)
    $category = null;
    if ($input['category_id'] !== 'other') {
        $category = EventCategory::find($input['category_id']);
        if (! $category) {
            $category = EventCategory::where('eventCategory_id', $input['category_id'])->first();
        }
    }
    $categoryBase = $category ? (int) $category->basePoints : 10;

    $calculatedPoints = $this->calculateEventPoints(
        $categoryBase,
        $input['eventStart'],
        $input['eventEnd'],
        $input['eventMaximum']
    );

    // Decide what to store in category/custom_category
    $categoryIdToStore = $input['category_id'];
    $customCategoryToStore = null;

    if ($input['category_id'] === 'other') {
        $categoryIdToStore = null;
        $customCategoryToStore = $input['custom_category'];
    }

    // Prepare payload
    $payload = [
        'event_id'        => $eventId,
        'user_id'         => Auth::id(),
        'category_id'     => $categoryIdToStore,
        'custom_category' => $customCategoryToStore,
        'eventTitle'      => $input['eventTitle'],
        'eventPoints'     => (int) $calculatedPoints,
        'eventStart'      => $input['eventStart'],
        'eventEnd'        => $input['eventEnd'],
        'eventSummary'    => $input['eventSummary'],
        'eventDescription'=> $input['eventDescription'],
        'eventImage'      => $imageFileName,
        'eventImpact'     => $input['eventImpact'],
        'venueName'       => $input['venueName'],
        'zipCode'         => $input['zipCode'],
        'city'            => $input['city'],
        'state'           => $input['state'],
        'country'         => $input['country'],
        'eventMaximum'    => $input['eventMaximum'],
        'requirements'    => $input['requirements'],
    ];

    // Create event
    $event = Event::create($payload);

    // Attach selected SDGs and skills
    if ($request->has('sdgs')) {
        $event->sdgs()->attach($request->input('sdgs', []));
    }

    if ($request->has('skills')) {
        $event->skills()->attach($request->input('skills', []));
    }

    return redirect()->route('ngo.events.index')->with('success', 'Event created successfully. Points: ' . $calculatedPoints);
}


    // Show edit form
    public function edit(Event $event)
    {
        $this->authorizeNGO($event);

        // Block editing if event ended
        if ($this->eventHasEnded($event)) {
            return redirect()->route('ngo.profile.eventEditDelete', $event->event_id)
                ->with('error', 'Event has ended — editing is disabled.');
        }

        // categories for dropdown
        $categories = EventCategory::orderBy('eventCategoryName')->get();

        // all SDGs for checkbox list
        $sdgs = Sdg::orderBy('sdg_number')->orderBy('sdgName')->get();

        // all skills for checkbox/list
        $skills = Skill::orderBy('skillName')->get();

        $selectedSdgs = $event->sdgs()->pluck('sdgs.sdg_id')->toArray();
        $selectedSkills = $event->skills()->pluck('skills.skill_id')->toArray();

        return view('ngo.events.event_edit', compact('event', 'categories', 'sdgs', 'skills', 'selectedSdgs', 'selectedSkills'));
    }

    // --- update() (replace the existing update method) ---
   public function update(Request $request, Event $event)
{
    $this->authorizeNGO($event);

    // Block updating if event ended
    if ($this->eventHasEnded($event)) {
        return redirect()->route('ngo.profile.eventEditDelete', $event->event_id)
            ->with('error', 'Event has ended — updates are disabled.');
    }

    // Normalize inputs (same as store)
    $input = [
        'eventTitle'       => $request->input('eventTitle') ?? $request->input('event_title'),
        'eventPoints'      => $request->input('eventPoints') ?? $request->input('reward_points') ?? $request->input('event_points'),
        'eventStart'       => $request->input('eventStart') ?? $request->input('start_date') ?? $request->input('event_start'),
        'eventEnd'         => $request->input('eventEnd') ?? $request->input('end_date') ?? $request->input('event_end'),
        'eventSummary'     => $request->input('eventSummary') ?? $request->input('event_summary'),
        'eventDescription' => $request->input('eventDescription') ?? $request->input('event_description'),
        'eventImpact'      => $request->input('eventImpact') ?? $request->input('event_impact'),
        'venueName'        => $request->input('venueName') ?? $request->input('event_location') ?? $request->input('venue_name'),
        'zipCode'          => $request->input('zipCode') ?? $request->input('zip_code'),
        'city'             => $request->input('city'),
        'state'            => $request->input('state'),
        'country'          => $request->input('country'),
        'eventMaximum'     => $request->input('eventMaximum') ?? $request->input('event_maximum') ?? $request->input('max_attendees'),
        'category_id'      => $request->input('category_id') ?? $request->input('category') ?? $request->input('event_category_id'),
        'custom_category'  => $request->input('custom_category'),
        'requirements'     => $request->input('requirements') ?? $request->input('requirements_text') ?? null,
    ];

    // Validation rules (eventPoints optional, we'll recalculate below)
    $rules = [
        'eventTitle'       => 'required|string|max:255',
        'eventPoints'      => 'nullable|integer|min:0',
        'eventStart'       => 'required|date|after_or_equal:now',
        'eventEnd'         => 'required|date|after_or_equal:eventStart',

        'eventSummary'     => 'nullable|string|max:500',
        'eventDescription' => 'required|string',
        'venueName'        => 'required|string|max:255',
        'zipCode'          => 'nullable|string|max:20',
        'city'             => 'required|string|max:100',
        'state'            => 'required|string|max:100',
        'country'          => 'required|string|max:100',
        // eventMaximum now REQUIRED on edit
        'eventMaximum'     => 'required|integer|min:1',

        // allow existing category or "other" with custom
        'category_id'      => 'nullable|string',
        'custom_category'  => 'nullable|string|max:255|required_if:category_id,other',

        'sdgs'             => 'nullable|array',
        'sdgs.*'           => 'exists:sdgs,sdg_id',
        'skills'           => 'nullable|array',
        'skills.*'         => 'exists:skills,skill_id',
        'requirements'     => 'nullable|string|max:2000',

        // image ALWAYS optional on edit
        'eventImage'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
    ];

    // Create validator
    $validator = Validator::make(
        array_merge($input, $request->only('sdgs', 'skills', 'eventImage')),
        $rules
    );

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput($request->all());
    }

    // Image handling (replace if new uploaded)
    $defaultEventImage = 'default_event.jpg';

    if ($request->hasFile('eventImage') || $request->hasFile('event_image')) {
        if ($event->eventImage) {
            $oldBasename = basename($event->eventImage);
            $oldPath = public_path('images/events/' . $oldBasename);

            if ($oldBasename !== $defaultEventImage && file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }

        $file = $request->hasFile('eventImage') ? $request->file('eventImage') : $request->file('event_image');
        $safeOriginal = preg_replace('/\s+/', '_', $file->getClientOriginalName());
        $imageFileName = time() . '_event_' . $safeOriginal;

        $destFolder = public_path('images/events');
        if (! is_dir($destFolder)) {
            mkdir($destFolder, 0755, true);
        }

        $file->move($destFolder, $imageFileName);
        $event->eventImage = $imageFileName;
    }

    // If still no image, ensure default
    if (! $event->eventImage) {
        $event->eventImage = $defaultEventImage;
    }

    // --- AUTO-CALCULATE points for update as well ---
    $categoryIdToUse = $input['category_id'] ?? $event->category_id;
    $customCategoryToStore = null;

    if ($input['category_id'] === 'other') {
        $categoryIdToUse = null;
        $customCategoryToStore = $input['custom_category'];
    }

    if ($categoryIdToUse !== null) {
        $category = EventCategory::find($categoryIdToUse);
        if (! $category) {
            $category = EventCategory::where('eventCategory_id', $categoryIdToUse)->first();
        }
    } else {
        $category = null;
    }

    $categoryBase = $category ? (int) $category->basePoints : 10;

    $calculatedPoints = $this->calculateEventPoints(
        $categoryBase,
        $input['eventStart'],
        $input['eventEnd'],
        $input['eventMaximum']
    );

    // Update model fields
    $event->category_id      = $categoryIdToUse;
    $event->custom_category  = $customCategoryToStore;
    $event->eventTitle       = $input['eventTitle'];
    $event->eventPoints      = (int) $calculatedPoints;
    $event->eventStart       = $input['eventStart'];
    $event->eventEnd         = $input['eventEnd'];
    $event->eventSummary     = $input['eventSummary'];
    $event->eventDescription = $input['eventDescription'];
    $event->eventImpact      = $input['eventImpact'];
    $event->venueName        = $input['venueName'];
    $event->zipCode          = $input['zipCode'];
    $event->city             = $input['city'];
    $event->state            = $input['state'];
    $event->country          = $input['country'];
    $event->eventMaximum     = $input['eventMaximum'];
    $event->requirements     = $input['requirements'];

    $event->save();

    // Sync SDGs and Skills (attach / detach to match selection)
    $event->sdgs()->sync($request->input('sdgs', []));
    $event->skills()->sync($request->input('skills', []));

    return redirect()->route('ngo.events.index')->with('success', 'Event updated successfully. Points: ' . $calculatedPoints);
}


    private function calculateEventPoints($categoryBasePoints, $eventStart, $eventEnd, $eventMaximum)
    {
        $base = (int) ($categoryBasePoints ?? 10);
        $eventMaximum = (int) ($eventMaximum ?? 0);

        try {
            // App timezone
            $tz = config('app.timezone') ?? 'UTC';

            // Normalize common input formats:
            // - "2025-11-01 18:06:00"
            // - "2025-11-01T18:06" (from datetime-local)
            // - "2025-11-01T18:06:00"
            $normalize = function ($s) {
                if ($s === null) return null;
                $s = trim($s);
                // If contains 'T' and no seconds, convert to space and add :00
                if (strpos($s, 'T') !== false) {
                    // examples: 2025-11-01T18:06  or 2025-11-01T18:06:00
                    $s = str_replace('T', ' ', $s);
                    // ensure seconds exist
                    if (! preg_match('/:\d{2}:\d{2}$/', $s)) {
                        $s = preg_replace('/(:\d{2})$/', '$1:00', $s);
                    }
                }
                // If input has only date and time without seconds but with space (e.g. "Y-m-d H:i"), add seconds
                if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/', $s)) {
                    $s .= ':00';
                }
                return $s;
            };

            $sRaw = $normalize($eventStart);
            $eRaw = $normalize($eventEnd);

            $start = null; $end = null;

            // Try createFromFormat first (most deterministic)
            if ($sRaw) {
                try {
                    $start = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $sRaw, $tz);
                } catch (\Throwable $ex) {
                    // fallback to parse
                    try { $start = \Carbon\Carbon::parse($sRaw, $tz); } catch (\Throwable $ie) { $start = null; }
                }
            }

            if ($eRaw) {
                try {
                    $end = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $eRaw, $tz);
                } catch (\Throwable $ex) {
                    try { $end = \Carbon\Carbon::parse($eRaw, $tz); } catch (\Throwable $ie) { $end = null; }
                }
            }

            // If either failed, try generic parse (best-effort)
            if (! $start) {
                $start = Carbon::parse($eventStart, $tz);
            }
            if (! $end) {
                $end = Carbon::parse($eventEnd, $tz);
            }

            // If still missing, return base and log
            if (! $start || ! $end) {
                Log::warning('calculateEventPoints: could not parse start/end', [
                    'raw_start' => $eventStart, 'raw_end' => $eventEnd, 'normalized_start' => $sRaw, 'normalized_end' => $eRaw
                ]);
                return $base;
            }

            // Ensure start <= end (swap if necessary)
            if ($end->lt($start)) {
                [$start, $end] = [$end, $start];
            }

            // Compute duration in minutes and convert to hours (ceil partial hours)
            $minutes = $start->diffInMinutes($end); // start -> end
            $durationHours = max(1, (int) ceil($minutes / 60));

            // Scaling factors (tweakable)
            $durationFactor = min($durationHours / 1, 80);   // every 2 hours ~ 1 unit, capped
            $attendeeFactor = min($eventMaximum / 5, 80);   // every 10 attendees ~ 1 unit, capped

            // Coefficients
            $durationCoef = 1.5;
            $attendeeCoef = 1.2;

            $points = $base
                    + (int) round($durationFactor * $durationCoef)
                    + (int) round($attendeeFactor * $attendeeCoef);

            // Debug log (optional — remove in production if noisy)
            Log::debug('calculateEventPoints debug', [
                'base' => $base,
                'start' => $start->toDateTimeString(),
                'end' => $end->toDateTimeString(),
                'minutes' => $minutes,
                'durationHours' => $durationHours,
                'durationFactor' => $durationFactor,
                'attendeeFactor' => $attendeeFactor,
                'points' => $points,
                'eventMaximum' => $eventMaximum,
            ]);

            return max($points, $base);
        } catch (\Exception $e) {
            Log::error('calculateEventPoints error: '.$e->getMessage(), [
                'start' => $eventStart, 'end' => $eventEnd, 'max' => $eventMaximum
            ]);
            return $base;
        }
    }

    // Delete own event
   public function destroy(Event $event)
{
    $this->authorizeNGO($event);

    // Block deletion if event ended
    if ($this->eventHasEnded($event)) {
        return redirect()->route('ngo.profile.eventEditDelete', $event->event_id)
            ->with('error', 'Event has ended — deletion is disabled.');
    }

    $defaultEventImage = 'default_event.jpg';
    if ($event->eventImage) {
        $basename = basename($event->eventImage);
        $path = public_path('images/events/' . $basename);
        if ($basename !== $defaultEventImage && file_exists($path)) {
            @unlink($path);
        }
    }

    // detach SDGs and skills to keep pivots clean
    $event->sdgs()->detach();
    $event->skills()->detach();

    $event->delete();

    return redirect()->route('ngo.profile.show', Auth::id())
        ->with('success', 'Event deleted successfully.');
}


    // Admin delete (no authorization check)
   // Admin delete (no authorization check)
public function adminDestroy(Event $event)
{
    $defaultEventImage = 'default_event.jpg';
    if ($event->eventImage) {
        $basename = basename($event->eventImage);
        $path = public_path('images/events/' . $basename);
        if ($basename !== $defaultEventImage && file_exists($path)) {
            @unlink($path);
        }
    }

    // detach SDGs and skills
    $event->sdgs()->detach();
    $event->skills()->detach();
    $event->registrations()->delete();

    $event->delete();
    return redirect()->route('admin.events.index')->with('success', 'Event removed by Admin.');
}


    // Private NGO check
    private function authorizeNGO(Event $event)
    {
        if ($event->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    }
}
