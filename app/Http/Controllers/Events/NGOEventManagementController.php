<?php

namespace App\Http\Controllers\Events;

use App\Models\Task;
use App\Models\User;
use App\Models\Event;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\TaskAssignment;
use App\Models\EventRegistration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Jobs\SendParticipantEmailJob;
use Illuminate\Support\Facades\Validator;


class NGOEventManagementController extends Controller
{
    protected function eventOwnerId(Event $event)
    {
        // Adjust this if your events table has another owner column (e.g., ngo_id)
        return $event->user_id;
    }

    protected function authorizeOwner(Event $event)
    {
        $ownerId = $this->eventOwnerId($event);
        if ($ownerId && Auth::id() === $ownerId) return true;

        // allow admin role as well
        if (Auth::check() && optional(Auth::user()->role)->roleName === 'admin') return true;

        return false;
    }





public function manage($event_id, Request $request)
{
    $event = Event::where('event_id', $event_id)->firstOrFail();

    if (! $this->authorizeOwner($event)) {
        abort(403, 'Unauthorized');
    }

    $search = $request->input('search');

    $query = EventRegistration::where('event_id', $event->event_id);

    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('email', 'LIKE', "%{$search}%")
              ->orWhere('contactNumber', 'LIKE', "%{$search}%")
              ->orWhere('age', 'LIKE', "%{$search}%")
              ->orWhere('gender', 'LIKE', "%{$search}%")
              ->orWhere('skill', 'LIKE', "%{$search}%");
        });
    }

    $registered = $query->orderBy('created_at', 'asc')->get();

    $confirmed = EventRegistration::where('event_id', $event->event_id)
        ->where('status', EventRegistration::STATUS_APPROVED)
        ->orderBy('updated_at', 'desc')
        ->get();

    $rejected = EventRegistration::where('event_id', $event->event_id)
        ->where('status', EventRegistration::STATUS_REJECTED)
        ->orderBy('updated_at', 'desc')
        ->get();

    // Build confirmedParticipants as User models (only those with user_id)
   
    $userIds = $confirmed->pluck('user_id')->filter()->unique()->values()->all();
    $confirmedParticipants = User::whereIn('id', $userIds)->orderBy('name')->get();

    // --- Build assignedMap: user_id => 'task1,task2' (string CSV)
    // Defensive: if no userIds, return empty array immediately to avoid unnecessary query
    $assignedMap = [];
    if (!empty($userIds)) {
        $assignedMap = TaskAssignment::whereIn('user_id', $userIds)
            ->get()
            ->groupBy('user_id')
            ->map(function ($rows) {
                // $rows is a collection of TaskAssignment rows for that user
                return $rows->pluck('task_id')->implode(',');
            })
            ->toArray();
    }

    // Load tasks including assignments->user to avoid N+1 queries
    $tasks = Task::with(['assignments.user'])
        ->where('event_id', $event->event_id)
        ->get();

      $attendances = Attendance::with(['user.volunteerProfile'])
        ->where('event_id', $event->event_id)
        ->get();

    // Pass assignedMap to the view so Blade can render data-assigned-tasks
    return view('ngo.events.manage', compact(
        'event', 'registered', 'confirmed', 'rejected', 'search', 'tasks', 'confirmedParticipants', 'assignedMap', 'attendances'
    ));
}


    public function approve($event_id, $registration_id, Request $request)
    {
        $event = Event::where('event_id', $event_id)->firstOrFail();
        $registration = EventRegistration::where('registration_id', $registration_id)->firstOrFail();

        if ($registration->event_id !== $event->event_id) {
            return response()->json(['success' => false, 'error' => 'Registration does not belong to this event'], 400);
        }

        if (! $this->authorizeOwner($event)) {
            return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
        }

        $registration->status = EventRegistration::STATUS_APPROVED;
        
        $registration->save();

        return response()->json([
            'success' => true,
            'registration' => $registration,
            

            'volunteer' => [
  'registration_id' => $registration->registration_id,
  'user_id' => $registration->user_id,
  'name' => $registration->name,
  'email' => $registration->email,
  'contact' => $registration->contactNumber,
  'age' => $registration->age,
  'gender' => $registration->gender,
  'skill' => $registration->skill,
  'registrationDate' => $registration->registrationDate ?? $registration->created_at?->toDateString(),
],

        ]);
    }

    public function reject($event_id, $registration_id, Request $request)
    {
        $event = Event::where('event_id', $event_id)->firstOrFail();
        $registration = EventRegistration::where('registration_id', $registration_id)->firstOrFail();

        if ($registration->event_id !== $event->event_id) {
            return response()->json(['success' => false, 'error' => 'Registration does not belong to this event'], 400);
        }

        if (! $this->authorizeOwner($event)) {
            return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
        }

        $registration->status = EventRegistration::STATUS_REJECTED;
        
        $registration->save();

      return response()->json([
        'success' => true,
        'registration' => $registration,
        
        'volunteer' => [
  'registration_id' => $registration->registration_id,
  'user_id' => $registration->user_id,
  'name' => $registration->name,
  'email' => $registration->email,
  'contact' => $registration->contactNumber,
  'age' => $registration->age,
  'gender' => $registration->gender,
  'skill' => $registration->skill,
  'registrationDate' => $registration->registrationDate ?? $registration->created_at?->toDateString(),
],

    ]);
    }

public function sendEmail($event_id, Request $request)
{
    $event = Event::where('event_id', $event_id)->firstOrFail();

    if (! $this->authorizeOwner($event)) {
        abort(403, 'Unauthorized');
    }

    // Validation
    $validator = Validator::make($request->all(), [
        'from_email' => 'required|email',
        'from_name'  => 'nullable|string|max:191',
        'subject'    => 'required|string|max:255',
        'message'    => 'required|string',
        'recipient_emails'   => 'nullable|string', // comma separated
        'recipient_user_ids' => 'nullable|string', // comma separated (registration_id or user ids)
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    $fromEmail = $request->input('from_email');
    $fromName  = $request->input('from_name') ?: optional(Auth::user())->name;
    $subject   = $request->input('subject');
    $message   = $request->input('message');

    // Parse manual emails
    $manualCsv = (string) $request->input('recipient_emails', '');
    $manualEmails = collect(explode(',', $manualCsv))
        ->map(fn($e) => trim($e))
        ->filter()
        ->unique()
        ->values()
        ->all();

    // Parse recipient_user_ids (could be registration ids or user ids)
    $idsCsv = (string) $request->input('recipient_user_ids', '');
    $ids = collect(explode(',', $idsCsv))
        ->map(fn($s) => trim($s))
        ->filter()
        ->unique()
        ->values()
        ->all();

    // Resolve emails from registration ids / user ids ONLY for this event
    $resolvedEmails = collect();

    if (!empty($ids)) {
        // Try interpreting IDs as registration_id first
        $regs = EventRegistration::where('event_id', $event->event_id)
            ->whereIn('registration_id', $ids)
            ->get(['email', 'registration_id', 'user_id', 'name']);

        if ($regs->count()) {
            $regs->pluck('email')->filter()->each(fn($e) => $resolvedEmails->push($e));
        }

        // Also resolve as user IDs (if not found above)
        $userIdsToCheck = collect($ids)
            ->filter(fn($id) => ! $regs->pluck('registration_id')->contains($id))
            ->values()
            ->all();

        if (!empty($userIdsToCheck)) {
            $users = User::whereIn('id', $userIdsToCheck)->get(['email','id','name']);
            $users->pluck('email')->filter()->each(fn($e) => $resolvedEmails->push($e));
        }
    }

    // Combine all candidate recipients
    $allCandidates = collect($manualEmails)->merge($resolvedEmails)->map(fn($e) => trim($e))->filter()->unique()->values();

    // Validate email addresses and filter out invalid ones
    $validEmails = $allCandidates->filter(function($email){
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    })->values();

    if ($validEmails->isEmpty()) {
        return redirect()->back()->with('error', 'No valid recipients selected. Please choose participants or enter valid emails.');
    }

    // Optional: cap maximum recipients per request to avoid accidental mass sends
    $maxRecipients = 500;
    if ($validEmails->count() > $maxRecipients) {
        return redirect()->back()->with('error', "Too many recipients (max {$maxRecipients}). Please reduce recipients or send in batches.");
    }

    // Prepare payload for job
    $payload = [
        'event_id'   => $event->event_id,
        'event_title'=> $event->eventTitle ?? '',
        'from_email' => $fromEmail,
        'from_name'  => $fromName,
        'subject'    => $subject,
        'message'    => $message,
    ];

    // Queue a job per recipient (you could batch jobs or chunk them)
    $queuedCount = 0;
    foreach ($validEmails as $email) {
        // Dispatch a job that will actually send and log.
        SendParticipantEmailJob::dispatch($email, $payload);
        $queuedCount++;
    }

    // Log summary
    Log::info("NGO email queued", [
        'ngo_user_id' => optional(Auth::user())->id,
        'ngo_email' => $fromEmail,
        'event_id' => $event->event_id,
        'event_title' => $event->eventTitle ?? null,
        'recipients_count' => $queuedCount,
        'recipients' => $validEmails->all(),
        'subject' => $subject,
    ]);

    return redirect()->back()->with('success', "Queued {$queuedCount} email(s) for sending.");
}


}
