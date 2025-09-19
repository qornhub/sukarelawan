<?php

namespace App\Http\Controllers\Events;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class EventRegistrationController extends Controller
{

    

    // helper example (implement based on your app)
    protected function currentUserCanManageEvent(Event $event): bool
    {
        // Example: if the event's organizer_id matches logged in user id
        return Auth::check() && Auth::id() === optional($event->organizer)->id;
        // adapt to your roles/permissions (e.g., role check for NGO)
    }

    /**
     * Show registration form for a given event
     */
    public function create(Event $event)
    {
        $user = Auth::user();
        $volunteerProfile = $user->volunteerProfile; // relationship

        return view('volunteer.event_registrations.registrations_create', compact('event', 'user', 'volunteerProfile'));
    }

    /**
     * Store a new event registration
     */
    public function store(Request $request, Event $event)
    {
        
        $request->validate([
            // user info (snapshot)
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'contactNumber' => 'required|string|max:20',
            'age' => 'nullable|integer|min:10|max:120',
            'gender' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',

            // registration details
            'company' => 'nullable|string|max:255',
            'volunteeringExperience' => 'nullable|string',
            'skill' => 'nullable|string|max:255',
            'emergencyContact' => 'required|string|max:255',
            'emergencyContactNumber' => 'required|string|max:20',
            'contactRelationship' => 'required|string|max:255',
        ]);

        // prevent duplicate registration
        $alreadyRegistered = EventRegistration::where('event_id', $event->event_id)
            ->where('user_id', Auth::id())
            ->exists();

        if ($alreadyRegistered) {
            return redirect()->route('volunteer.events.show', $event->event_id)
                ->with('warning', 'You have already registered for this event.');
        }

        // Check capacity (if eventMaximum is used)
        if ($event->eventMaximum && $event->registrations()->count() >= $event->eventMaximum) {
            return back()->with('error', 'This event is full.');
        }

        EventRegistration::create([
            'registration_id' => (string) Str::uuid(),
            
            'event_id'=> $event->event_id,
            'user_id' => Auth::id(),
            'registrationDate' => now(),
            'status' => 'pending',

            // new user snapshot fields
            'name' => $request->name,
            'email' => $request->email,
            'contactNumber' => $request->contactNumber,
            'age' => $request->age,
            'gender' => $request->gender,
            'address' => $request->address,

            // existing registration fields
            'company' => $request->company,
            'volunteeringExperience' => $request->volunteeringExperience,
            'skill' => $request->skill,
            'emergencyContact' => $request->emergencyContact,
            'emergencyContactNumber' => $request->emergencyContactNumber,
            'contactRelationship' => $request->contactRelationship,
        ]);

        // Redirect to event detail (consistent with other methods)
        return redirect()->route('volunteer.events.show', $event->event_id)
            ->with('success', 'You have successfully registered for this event!');
    }

    /**
     * Show edit form
     */
    public function edit(EventRegistration $registration)
    {
        // Ensure only the owner can edit
        if ($registration->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('volunteer.event_registrations.registrations_edit', compact('registration'));
    }

    /**
     * Update registration
     */
    public function update(Request $request, EventRegistration $registration)
    {
        if ($registration->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            // user info (snapshot)
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'contactNumber' => 'required|string|max:20',
            'age' => 'nullable|integer|min:10|max:120',
            'gender' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',

            // registration details
            'company' => 'nullable|string|max:255',
            'volunteeringExperience' => 'nullable|string',
            'skill' => 'nullable|string|max:255',
            'emergencyContact' => 'required|string|max:255',
            'emergencyContactNumber' => 'required|string|max:20',
            'contactRelationship' => 'required|string|max:255',
        ]);

        $registration->update([
            // user snapshot fields
            'name' => $request->name,
            'email' => $request->email,
            'contactNumber' => $request->contactNumber,
            'age' => $request->age,
            'gender' => $request->gender,
            'address' => $request->address,

            // registration fields
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
     * Delete (cancel) registration
     */
    public function destroy(EventRegistration $registration)
    {
        if ($registration->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $eventId = $registration->event_id;
        $registration->delete();

        return redirect()->route('volunteer.events.show', $eventId)
            ->with('success', 'Your registration has been cancelled.');
    }
}
