<?php

namespace App\Http\Controllers\Events;

use App\Http\Controllers\Controller;
use App\Models\Sdg;
use App\Models\Event;
use App\Models\Skill;
use App\Models\EventCategory;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

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

  

    // Show create form
    public function create()
    {
        $categories = EventCategory::orderBy('eventCategoryName')->get();
       $sdgs = Sdg::orderBy('sdg_number')->orderBy('sdgName')->get();
        $skills = Skill::orderBy('skillName')->get();

        return view('ngo.events.create', compact('categories', 'sdgs', 'skills'));
    }

    // Store event
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
            'requirements'     => $request->input('requirements') ?? $request->input('requirements_text') ?? null,
        ];

        // Validation rules
        $rules = [
            'eventTitle'       => 'required|string|max:255',
            'eventPoints'      => 'required|integer|min:0',
            'eventStart'       => 'required|date',
            'eventEnd'         => 'required|date|after_or_equal:eventStart',
            'eventSummary'     => 'nullable|string|max:500',
            'eventDescription' => 'required|string',
            'venueName'        => 'required|string|max:255',
            'zipCode'          => 'nullable|string|max:20',
            'city'             => 'required|string|max:100',
            'state'            => 'required|string|max:100',
            'country'          => 'required|string|max:100',
            'eventMaximum'     => 'nullable|integer|min:0',
            'category_id'      => 'required|exists:event_categories,eventCategory_id',
            'sdgs'             => 'nullable|array',
            'sdgs.*'           => 'exists:sdgs,sdg_id',
            'skills'           => 'nullable|array',
            'skills.*'         => 'exists:skills,skill_id',
            'requirements'     => 'nullable|string|max:2000',
        ];

        $validator = Validator::make($input + $request->only('sdgs', 'skills'), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->all());
        }

        // ---------- Image handling ----------
        $defaultEventImage = 'default-event.jpg';
        $imageFileName = null;

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

        // Prepare payload
        $payload = [
            'event_id'        => $eventId,
            'user_id'         => Auth::id(),
            'category_id'     => $input['category_id'],
            'eventTitle'      => $input['eventTitle'],
            'eventPoints'     => (int) $input['eventPoints'],
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

        return redirect()->route('ngo.events.index')->with('success', 'Event created successfully.');
    }

    // Show edit form
    public function edit(Event $event)
    {
        $this->authorizeNGO($event);

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

    // Update event
    public function update(Request $request, Event $event)
    {
        $this->authorizeNGO($event);

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
            'requirements'     => $request->input('requirements') ?? $request->input('requirements_text') ?? null,
        ];

        // Validation rules (category_id optional)
        $rules = [
            'eventTitle'       => 'required|string|max:255',
            'eventPoints'      => 'required|integer|min:0',
            'eventStart'       => 'required|date',
            'eventEnd'         => 'required|date|after_or_equal:eventStart',
            'eventSummary'     => 'nullable|string|max:500',
            'eventDescription' => 'required|string',
            'venueName'        => 'required|string|max:255',
            'zipCode'          => 'nullable|string|max:20',
            'city'             => 'required|string|max:100',
            'state'            => 'required|string|max:100',
            'country'          => 'required|string|max:100',
            'eventMaximum'     => 'nullable|integer|min:0',
            'category_id'      => 'nullable|exists:event_categories,eventCategory_id',
            'sdgs'             => 'nullable|array',
            'sdgs.*'           => 'exists:sdgs,sdg_id',
            'skills'           => 'nullable|array',
            'skills.*'         => 'exists:skills,skill_id',
            'requirements'     => 'nullable|string|max:2000',
        ];

        $validator = Validator::make(array_merge($input, $request->only('sdgs', 'skills')), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->all());
        }

        // Image handling (replace if new uploaded)
        $defaultEventImage = 'default-event.jpg';

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

        // Update model fields
        $event->category_id      = $input['category_id'];
        $event->eventTitle       = $input['eventTitle'];
        $event->eventPoints      = (int) $input['eventPoints'];
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

        return redirect()->route('ngo.events.index')->with('success', 'Event updated successfully.');
    }

    // Delete own event
    public function destroy(Event $event)
    {
        $this->authorizeNGO($event);

        $defaultEventImage = 'default-event.jpg';
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
    public function adminDestroy(Event $event)
    {
        $defaultEventImage = 'default-event.jpg';
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
        return back()->with('success', 'Event removed by Admin.');
    }

    // Private NGO check
    private function authorizeNGO(Event $event)
    {
        if ($event->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    }
}
