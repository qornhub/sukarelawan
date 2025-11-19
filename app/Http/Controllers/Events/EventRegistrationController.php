<?php

namespace App\Http\Controllers\Events;

use Carbon\Carbon;
use App\Models\Event;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\EventRegistration;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class EventRegistrationController extends Controller
{
    /**
     * Check if current user is the organizer of the event
     */
    protected function currentUserCanManageEvent(Event $event): bool
    {
        return Auth::check() && Auth::id() === optional($event->organizer)->id;
    }

    /**
     * Determine if the event has already started
     */
    protected function eventHasStartedForRegistration($registrationOrEvent): bool
    {
        $eventStartRaw = null;

        if ($registrationOrEvent instanceof EventRegistration) {
            if (isset($registrationOrEvent->event) && !empty($registrationOrEvent->event->eventStart)) {
                $eventStartRaw = $registrationOrEvent->event->eventStart;
            } elseif (!empty($registrationOrEvent->eventStart)) {
                $eventStartRaw = $registrationOrEvent->eventStart;
            }
        } elseif ($registrationOrEvent instanceof Event) {
            $eventStartRaw = $registrationOrEvent->eventStart ?? null;
        }

        if (!$eventStartRaw) return false;

        try {
            $eventStart = Carbon::parse($eventStartRaw);
            return Carbon::now()->greaterThanOrEqualTo($eventStart);
        } catch (\Exception $ex) {
            return false;
        }
    }

    /**
     * Show create registration form
     */
    public function create(Event $event)
    {
        if ($this->eventHasStartedForRegistration($event)) {
            return redirect()->route('volunteer.events.show', $event->event_id)
                ->with('error', 'This event has already started. Registration is closed.');
        }

        $user = Auth::user();
        $volunteerProfile = $user->volunteerProfile ?? null;

        return view('volunteer.event_registrations.registrations_create', compact('event', 'user', 'volunteerProfile'));
    }

    /**
     * Store new event registration
     */
    public function store(Request $request, Event $event)
    {
        if ($this->eventHasStartedForRegistration($event)) {
            return redirect()->route('volunteer.events.show', $event->event_id)
                ->with('error', 'This event has already started. Registration is closed.');
        }

        // Validation — age MUST be 16 or above
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'contactNumber' => 'required|string|max:20',
            'age' => 'required|integer|min:16|max:120',   // UPDATED HERE
            'gender' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',

            'company' => 'nullable|string|max:255',
            'volunteeringExperience' => 'nullable|string',
            'skill' => 'nullable|string|max:255',

            'emergencyContact' => 'required|string|max:255',
            'emergencyContactNumber' => 'required|string|max:20',
            'contactRelationship' => 'required|string|max:255',
        ]);

        // Extra strict check (prevents HTML tampering)
        if ($request->age < 16) {
            return back()->with('error', 'You must be at least 16 years old to register for this event.');
        }

        // Prevent duplicate registration
        $alreadyRegistered = EventRegistration::where('event_id', $event->event_id)
            ->where('user_id', Auth::id())
            ->exists();

        if ($alreadyRegistered) {
            return redirect()->route('volunteer.events.show', $event->event_id)
                ->with('warning', 'You have already registered for this event.');
        }

        // Check capacity
        if ($event->eventMaximum && $event->registrations()->count() >= $event->eventMaximum) {
            return back()->with('error', 'This event is full.');
        }

        // Create registration
        $registration = EventRegistration::create([
            'registration_id' => (string) Str::uuid(),
            'event_id' => $event->event_id,
            'user_id' => Auth::id(),
            'registrationDate' => now(),
            'status' => 'pending',

            'name' => $request->name,
            'email' => $request->email,
            'contactNumber' => $request->contactNumber,
            'age' => $request->age,
            'gender' => $request->gender,
            'address' => $request->address,

            'company' => $request->company,
            'volunteeringExperience' => $request->volunteeringExperience,
            'skill' => $request->skill,

            'emergencyContact' => $request->emergencyContact,
            'emergencyContactNumber' => $request->emergencyContactNumber,
            'contactRelationship' => $request->contactRelationship,
        ]);

        // Notify organizer
        $organizer = $event->organizer;

        if (!$organizer) {
            $ngoProfile = \App\Models\NGOProfile::where('user_id', $event->user_id)->first();
            if ($ngoProfile) $organizer = $ngoProfile->user;
        }

        if ($organizer && method_exists($organizer, 'notify')) {
            $organizer->notify(
                new \App\Notifications\VolunteerRegisteredNotification($event, $registration)
            );
        } else {
            Log::warning('No valid NGO organizer found for event', [
                'event_id' => $event->event_id,
            ]);
        }

        return redirect()->route('volunteer.events.show', $event->event_id)
            ->with('success', 'You have successfully registered for this event!');
    }

    /**
     * Show edit form
     */
    public function edit(EventRegistration $registration)
    {
        if ($registration->user_id !== Auth::id()) abort(403);

        if ($this->eventHasStartedForRegistration($registration)) {
            return redirect()->route('volunteer.events.show', $registration->event_id)
                ->with('error', 'Event has already started — registration cannot be edited.');
        }

        return view('volunteer.event_registrations.registrations_edit', compact('registration'));
    }

    /**
     * Update registration
     */
    public function update(Request $request, EventRegistration $registration)
    {
        if ($registration->user_id !== Auth::id()) abort(403);

        if ($this->eventHasStartedForRegistration($registration)) {
            return redirect()->route('volunteer.events.show', $registration->event_id)
                ->with('error', 'Event has already started — registration cannot be updated.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'contactNumber' => 'required|string|max:20',
            'age' => 'required|integer|min:16|max:120',   // UPDATED HERE TOO
            'gender' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',

            'company' => 'nullable|string|max:255',
            'volunteeringExperience' => 'nullable|string',
            'skill' => 'nullable|string|max:255',

            'emergencyContact' => 'required|string|max:255',
            'emergencyContactNumber' => 'required|string|max:20',
            'contactRelationship' => 'required|string|max:255',
        ]);

        if ($request->age < 16) {
            return back()->with('error', 'You must be at least 16 years old.');
        }

        $registration->update([
            'name' => $request->name,
            'email' => $request->email,
            'contactNumber' => $request->contactNumber,
            'age' => $request->age,
            'gender' => $request->gender,
            'address' => $request->address,

            'company' => $request->company,
            'volunteeringExperience' => $request->volunteeringExperience,
            'skill' => $request->skill,

            'emergencyContact' => $request->emergencyContact,
            'emergencyContactNumber' => $request->emergencyContactNumber,
            'contactRelationship' => $request->contactRelationship,
        ]);

        return redirect()->route('volunteer.profile.registrationEditDelete', $registration->event_id)
            ->with('success', 'Your registration has been updated!');
    }

    /**
     * Destroy registration
     */
    public function destroy(EventRegistration $registration)
    {
        if ($registration->user_id !== Auth::id()) abort(403);

        if ($this->eventHasStartedForRegistration($registration)) {
            return redirect()->route('volunteer.events.show', $registration->event_id)
                ->with('error', 'Event has already started — registration cannot be deleted.');
        }

        $eventId = $registration->event_id;
        $registration->delete();

        return redirect()->route('volunteer.events.show', $eventId)
            ->with('success', 'Your registration has been cancelled.');
    }
}
