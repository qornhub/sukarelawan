{{-- resources/views/ngo/events/show.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $event->eventTitle ?? 'Event Details' }}</title>

    <!-- Bootstrap & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/events/event_show.css') }}">
    <link rel="stylesheet" href="{{ asset('css/blogs/comment.css') }}">
<style>
    
/* Assignment banner refined */
.assignment-banner {
    background:  var(--primary-light);
    color: white;
    border-radius: 0 0 var(--border-radius) var(--border-radius); /* bottom only */
    box-shadow: var(--shadow);
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 6rem; /* more left-right spacing */
    margin-bottom: 1.5rem;
    animation: slideIn 0.6s ease;
}

.assignment-banner strong {
    font-size: 1rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.assignment-banner strong::before {
    content: "🎉";
    font-size: 1.2rem;
}

/* Button styling */
.assignment-banner .btn {
    background: white;
    color: var(--primary-color);
    font-weight: 600;
    border-radius: 10px; /* pill style */
    padding: 0.4rem 1rem;
    transition: all 0.2s ease;
    box-shadow: var(--card-shadow);
}

.assignment-banner .btn:hover {
    background: var(--primary-hover);
    color: #fff;
}

 /* Modal customization */
        .assignment-modal .modal-content {
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            border: none;
        }

        .assignment-modal .modal-header {
            background: linear-gradient(135deg, var(--bg-gradient-start), var(--bg-gradient-end));
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 1rem 1.5rem;
        }

        .assignment-modal .modal-title {
            color: var(--primary-color);
            font-weight: 600;
        }

        .assignment-modal .modal-body {
            padding: 1.5rem;
            max-height: 60vh;
            overflow-y: auto;
        }

        .assignment-modal .modal-footer {
            border-top: 1px solid rgba(0,0,0,0.05);
            padding: 1rem 1.5rem;
        }

        /* Task grid styles */
        .task-grid {
            display: grid;
            gap: 1rem;
        }

        .task-card {
            background: white;
            border-radius: var(--rounded-sm);
            padding: 1.25rem;
            box-shadow: var(--card-shadow);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border-left: 4px solid var(--primary-color);
        }

        .task-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1.5rem rgba(58, 59, 69, 0.15);
        }

        .task-header {
            display: flex;
            align-items: center;
            margin-bottom: 0.75rem;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .task-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            background-color: var(--primary-color);
            color: white;
            border-radius: 50%;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .task-title {
            font-weight: 600;
            margin: 0;
            color: var(--text-color);
            flex: 1;
            min-width: 200px;
        }

        .task-date {
            font-size: 0.85rem;
            color: #6c757d;
            background-color: var(--light-gray);
            padding: 0.25rem 0.75rem;
            border-radius: 30px;
            white-space: nowrap;
        }

        .task-desc {
            color: var(--text-color);
            margin: 0;
            line-height: 1.5;
            font-size: 0.95rem;
        }

        /* Empty state */
        .empty-state {
            padding: 3rem 1rem;
        }

        .empty-state i {
            opacity: 0.5;
        }

        /* Button styles */
        .btn-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }

        .btn-success:hover {
            background-color: #17a673;
            border-color: #17a673;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        /* Responsive adjustments */
        @media (max-width: 576px) {
            .task-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .task-date {
                align-self: flex-start;
            }
            
            .modal-footer {
                flex-direction: column;
                gap: 1rem;
            }
            
            .modal-footer .organizer-contact {
                width: 100%;
            }
            
            .modal-footer .btn {
                width: 100%;
            }
        }

/* Slide-in animation */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}


        .post-settings {
            top: 20px;
            position: relative;
            /* keeps it in normal flow (Option A) */
            margin-bottom: 38px;
            /* your existing gap */
            text-align: right;
            /* RIGHT-align the inline button inside this block */
        }

        /* keep the button visuals */
        .post-settings .btn-settings {
            display: inline-block;
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(0, 0, 0, 0.08);
            padding: 8px 10px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
            color: #333;
        }




        /* responsive tweaks — on small screens keep icon closer to the card */
        @media (max-width: 767.98px) {
            .post-settings {
                top: -20px;
                /* less negative on narrow screens */
                right: 12px;
            }

            .main-content-card {
                padding-top: 20px;
            }
        }
 
</style>
</head>

<body>
    @include('layouts.volunteer_header')


    @php
        // image helpers
        $eventImage = $event->eventImage ?? null;
        $eventHeroUrl = $eventImage ? asset('images/events/' . $eventImage) : asset('images/events/default_event.jpg');

        // dates
        $start = $event->eventStart ? \Carbon\Carbon::parse($event->eventStart) : null;
        $end = $event->eventEnd ? \Carbon\Carbon::parse($event->eventEnd) : null;

        // registrations & sdgs & skills
        $registrations = $event->registrations ?? collect();
        $sdgs = $event->sdgs ?? collect();
        $skills = $event->skills ?? collect();
        $registeredCount = $registrations->count();
        $max = $event->eventMaximum ?? 0;
    @endphp

    {{-- HERO --}}
    <header class="hero" style="background-image: url('{{ $eventHeroUrl }}');">
        <div class="hero-overlay"></div>

        <div class="hero-content container">
            <div class="hero-text">
                <h1 class="hero-title">{{ $event->eventTitle ?? 'Untitled Event' }}</h1>
                <div class="hero-sub">By {{ optional($event->organizer)->name ?? 'Organizer' }}</div>
            </div>
        </div>
    </header>
    @include('layouts.messages')


    @if(isset($assignments) && $assignments->count() > 0)
    <div class="assignment-banner">
        <div>
            <strong>You’ve been assigned {{ $assignments->count() }} task(s) for this event!</strong>
        </div>
        <button type="button" class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#assignmentsModal">
            View Assignments
        </button>
    </div>
@endif



   <div class="modal fade" id="assignmentsModal" tabindex="-1" aria-labelledby="assignmentsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content assignment-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="assignmentsModalLabel">
                    <i class="fas fa-tasks me-2"></i> Your Assigned Tasks
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                @if(isset($assignments) && $assignments->count())
                    <div class="task-grid">
                        <div class="task-list">
                            @foreach($assignments as $index => $assignment)
                                @php 
                                    $task = $assignment->task; 
                                    $ad = $assignment->assignedDate ?? null;
                                    $assignedText = 'N/A';
                                    if ($ad) {
                                        try {
                                            $assignedText = \Carbon\Carbon::parse($ad)->format('M j, Y g:i A');
                                        } catch (\Exception $ex) {
                                            $assignedText = (string) $ad;
                                        }
                                    }
                                @endphp

                                <div class="task-card">
                                    <div class="task-header">
                                        <span class="task-number">{{ $index + 1 }}</span>
                                        <h6 class="task-title">{{ $task->title }}</h6>
                                        <span class="task-date">Assigned: {{ $assignedText }}</span>
                                    </div>
                                    <p class="task-desc">{{ $task->description }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="empty-state text-center p-4">
                        <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">No tasks have been assigned to you yet.</p>
                    </div>
                @endif
            </div>

            <div class="modal-footer d-flex justify-content-between align-items-center">
                {{-- Contact Organizer --}}
                <div class="organizer-contact">
                    @php
                        $phone = optional($event->organizer)->phone ?? null;
                        $waLink = $phone
                            ? "https://wa.me/{$phone}"
                            : 'https://wa.me/?text=' .
                                urlencode('Hello, I am interested in your event: ' . ($event->eventTitle ?? ''));
                    @endphp
                    <a href="{{ $waLink }}" target="_blank" class="btn btn-success">
                        <i class="fab fa-whatsapp me-2"></i>Contact Organizer
                    </a>
                </div>

                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

  @php
    use Carbon\Carbon;

    // Try to get the event start. This handles both: $registration->event relation,
    // or a raw datetime field like $registration->eventStart if you store it on registration.
    $eventStartRaw = null;
    if (isset($registration->event) && !empty($registration->event->eventStart)) {
        $eventStartRaw = $registration->event->eventStart;
    } elseif (!empty($registration->eventStart)) {
        $eventStartRaw = $registration->eventStart;
    }

    // Parse safely — default to null on failure
    $eventHasStarted = false;
    if ($eventStartRaw) {
        try {
            $eventStart = Carbon::parse($eventStartRaw);
            // Use app timezone or explicit timezone if you need
            // $now = Carbon::now('Asia/Kuala_Lumpur');
            $now = Carbon::now(); // assumes your app timezone is configured correctly
            $eventHasStarted = $now->greaterThanOrEqualTo($eventStart);
        } catch (\Exception $ex) {
            // parsing error — keep $eventHasStarted = false (don't hide)
        }
    }
@endphp

@if (!$eventHasStarted)

<div class="container mb-5">
    <div class="post-settings">
        <div class="btn-group">
            <button type="button" class="btn btn-settings btn-sm dropdown-toggle dropdown-toggle-no-caret"
                data-bs-toggle="dropdown" aria-expanded="false" title="Post settings">
                <i class="fa fa-cog"></i>
            </button>

            <ul class="dropdown-menu dropdown-menu-end">
                {{-- Edit --}}
                <li>
                    <a class="dropdown-item"
                       href="{{ route('volunteer.event.register.edit', $registration->registration_id) }}">
                        <i class="fa fa-edit me-2"></i> Edit Registration
                    </a>
                </li>

                {{-- Delete --}}
                <li>
                    <form action="{{ route('volunteer.event.register.destroy', $registration->registration_id) }}"
                          method="POST"
                          onsubmit="return confirm('Are you sure you want to permanently delete this registration? This action cannot be undone.');"
                          class="m-0 p-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="fa fa-trash me-2"></i> Delete Registration
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>
@endif


    <main class="container page-body">
        <div class="row gx-4 gy-4">
            {{-- LEFT MAIN --}}
            <div class="col-lg-8 left-col">
                {{-- Mission Description --}}
                <section class="content-card">
                    <h4 class="section-heading">
                        <i class="fas fa-bullseye icon"></i>
                        Mission Description
                    </h4>
                    <div class="text-content">
                        {!! nl2br(e($event->eventDescription ?? 'No description provided.')) !!}
                    </div>
                </section>

                {{-- Requirements (moved here; same structure as Mission Description) --}}
                <section class="content-card">
                    <h4 class="section-heading">
                        <i class="fas fa-list-check icon"></i>
                        Requirements
                    </h4>
                    <div class="text-content">
                        @if ($event->requirements && trim($event->requirements) !== '')
                            {!! nl2br(e($event->requirements)) !!}
                        @else
                            <span class="text-muted">No requirements specified</span>
                        @endif
                    </div>
                </section>

                {{-- Mission Impact --}}
                <section class="content-card">
                    <h4 class="section-heading">
                        <i class="fas fa-heart icon"></i>
                        Mission Impact
                    </h4>
                    <div class="text-content">
                        {!! nl2br(e($event->eventImpact ?? 'No impact information provided.')) !!}
                    </div>
                </section>

                {{-- SDG Addressed --}}
                <section class="content-card">
                    <h4 class="section-heading">
                        <i class="fas fa-globe icon"></i>
                        SDG Addressed
                    </h4>
                    <div class="sdg-badges d-flex flex-wrap gap-3">
                        @if ($sdgs->count())
                            @foreach ($sdgs as $sdg)
                                @php
                                    $img = $sdg->sdgImage
                                        ? asset('images/sdgs/' . $sdg->sdgImage)
                                        : asset('images/sdgs/default-sdg.png');
                                @endphp
                                <div class="sdg-item text-center" style="width:120px;">
                                    <img src="{{ $img }}" alt="{{ $sdg->sdgName }}" class="img-fluid"
                                        style="max-width:80px; height:auto; margin:0 auto;">
                                    <div class="sdg-name mt-2" style="font-size:0.9rem;">{{ $sdg->sdgName }}</div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state">
                                <i class="fas fa-globe-americas"></i>
                                <div>No SDGs linked to this event.</div>
                            </div>
                        @endif
                    </div>
                </section>

                {{-- Participants --}}
                <section class="content-card">
                    <h4 class="section-heading">
                        <i class="fas fa-users icon"></i>
                        Participants
                        <div class="participant-count">
                            {{ $registrations->where('status', 'approved')->count() }}
                            Participant{{ $registrations->where('status', 'approved')->count() !== 1 ? 's' : '' }}
                        </div>
                    </h4>

                    <div class="participants">
                        @if ($registrations->where('status', 'approved')->count())
                            <div class="participants-avatars">
                                @foreach ($registrations->take(6) as $reg)
                                    @if ($reg->status === 'approved')
                                        @php
                                            $user = $reg->user;
                                            $filename = optional($user->volunteerProfile)->profilePhoto;
                                            $avatarUrl = $filename
                                                ? asset('images/profiles/' . $filename)
                                                : asset('images/default-profile.png');
                                            $title = $user->name ?? 'Volunteer';
                                        @endphp

                                        <a href="{{ route('volunteer.profile.show', $user->id) }}">
                                            <img src="{{ $avatarUrl }}" alt="participant" class="avatar"
                                                title="{{ $title }}">
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="fas fa-user-plus"></i>
                                <div>No participants yet. Be the first to join!</div>
                            </div>
                        @endif
                    </div>
                </section>

                 {{-- commetn--}}
                <section class="content-card">
@include('partials.events.comments', [
    'event' => $event,
    'comments' => $comments,
    // optional:
    'profileRelation' => 'volunteerProfile',
    'profileRoute' => 'volunteer.profile.show',
    'profileStoragePath' => 'images/profiles/'
])

                     </section>


            </div>

            {{-- RIGHT SIDEBAR --}}
            <aside class="col-lg-4">
                {{-- Share --}}
                <div class="sidebar-card">
                    <h6 class="sidebar-heading">
                        <i class="fas fa-share-alt"></i>
                        Share With Your Friends
                    </h6>
                    <div class="social-share">
                        <a class="social-btn facebook"
                            href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}"
                            target="_blank">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a class="social-btn twitter"
                            href="https://x.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}"
                            target="_blank">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a class="social-btn instagram" href="#">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a class="social-btn whatsapp"
                            href="https://wa.me/?text={{ urlencode(request()->fullUrl()) }}" target="_blank">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>

                {{-- Location & Details --}}
                <div class="sidebar-card">
                    <h6 class="sidebar-heading">
                        <i class="fas fa-map-marker-alt"></i>
                        Event Details
                    </h6>

                    <div class="map-container">
                        @php
                            $addressParts = array_filter([
                                $event->venueName,
                                $event->city,
                                $event->state,
                                $event->zipCode,
                                $event->country,
                            ]);
                            $queryAddress = urlencode(implode(', ', $addressParts));
                            $mapsUrl = "https://www.google.com/maps?q={$queryAddress}&output=embed";
                        @endphp

                        @if ($queryAddress)
                            <iframe src="{{ $mapsUrl }}" width="100%" height="100%" style="border:0;"
                                allowfullscreen="" loading="lazy"></iframe>
                        @else
                            <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                                <div class="text-center">
                                    <i class="fas fa-map-marker-alt fa-2x mb-2 opacity-50"></i>
                                    <div>No location specified</div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="event-details">
                        <div class="detail-item">
                            <div class="detail-label">
                                <i class="fas fa-building me-1"></i>Venue
                            </div>
                            <div class="detail-value">{{ $event->venueName ?? 'TBD' }}</div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">
                                <i class="fas fa-map-pin me-1"></i>Location
                            </div>
                            <div class="detail-value">
                                {{ $event->city ?? '' }}{{ $event->city && $event->zipCode ? ', ' : '' }}{{ $event->zipCode ?? '' }}<br>
                                <small class="text-muted">{{ $event->state ?? '' }}</small>
                            </div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">
                                <i class="fas fa-calendar-check me-1"></i>Starts
                            </div>
                            <div class="detail-value">{{ $start ? $start->format('M j, Y g:i A') : '-' }}</div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">
                                <i class="fas fa-calendar-check me-1"></i>Ends
                            </div>
                            <div class="detail-value">{{ $end ? $end->format('M j, Y g:i A') : '-' }}</div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">
                                <i class="fas fa-clock me-1"></i>Posted
                            </div>
                            <div class="detail-value">
                                {{ $event->created_at ? \Carbon\Carbon::parse($event->created_at)->format('M j, Y') : '-' }}
                            </div>
                        </div>

                        {{-- Skills (stacked vertically, right-aligned) --}}
                        <div class="detail-item">
                            <div class="detail-label">
                                <i class="fas fa-tools me-1"></i>Skills
                            </div>
                            <div class="detail-value">
                                @if ($skills->count())
                                    <div class="d-flex flex-column gap-2">
                                        @foreach ($skills as $skill)
                                            <div class="w-100 text-end">
                                                <span class="badge bg-light text-dark border d-inline-block">
                                                    <i class="fas fa-check-circle text-success me-1"></i>
                                                    {{ $skill->skillName ?? ($skill->name ?? 'Skill') }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted">No specific skills required</span>
                                @endif
                            </div>
                        </div>

                        {{-- Reward --}}
                        <div class="detail-item">
                            <div class="detail-label">
                                <i class="fas fa-star me-1"></i>Reward
                            </div>
                            <div class="detail-value">
                                <span class="points-badge">
                                    <i class="fas fa-coins"></i>
                                    {{ $event->eventPoints ?? 0 }} Points
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Organizer card --}}
                <div class="sidebar-card organizer-card">
                    <div class="organizer-header">
                        @php
                            use Illuminate\Support\Facades\Storage;

                            $default = asset('images/default-profile.png');
                            $organizer = optional($event->organizer);

                            // Try to get profile photo from organizer->ngoProfile or volunteerProfile
                            $file =
                                optional($organizer->ngoProfile)->profilePhoto ??
                                (optional($organizer->volunteerProfile)->profilePhoto ?? null);

                            $profileImageUrl = $default;

                            if ($file) {
                                $basename = trim(basename($file));

                                // Case 1: public/images/profiles/<basename>
                                if (file_exists(public_path("images/profiles/{$basename}"))) {
                                    $profileImageUrl = asset("images/profiles/{$basename}");
                                }
                                // Case 2: public/images/<basename>
                                elseif (file_exists(public_path("images/{$basename}"))) {
                                    $profileImageUrl = asset("images/{$basename}");
                                }
                                // Case 3: stored in storage/app/public
                                elseif (Storage::disk('public')->exists($file)) {
                                    $profileImageUrl = Storage::disk('public')->url($file);
                                } elseif (Storage::disk('public')->exists("profiles/{$basename}")) {
                                    $profileImageUrl = Storage::disk('public')->url("profiles/{$basename}");
                                }
                            }
                        @endphp

                        <div class="org-avatar">
                            <img src="{{ $profileImageUrl }}" alt="Organizer Image" class="rounded-circle"
                                style="width:60px;height:60px;object-fit:cover;">
                        </div>
                        <div>
                            <div class="organizer-label">Organized By</div>
                            <div class="organizer-name">{{ optional($event->organizer)->name ?? 'Organizer' }}</div>
                        </div>
                    </div>


                    <div class="organizer-actions">
                        @php
                            $phone = optional($event->organizer)->phone ?? null;
                            $waLink = $phone
                                ? "https://wa.me/{$phone}"
                                : 'https://wa.me/?text=' .
                                    urlencode('Hello, I am interested in your event: ' . ($event->eventTitle ?? ''));
                        @endphp

                        <a href="{{ $waLink }}" target="_blank" class="btn-organizer primary">
                            <i class="fab fa-whatsapp me-2"></i>WhatsApp Organizer
                        </a>
                        <a href="{{ route('ngo.profile.show', optional($event->organizer)->id ?? '#') }}"
                            class="btn-organizer secondary">
                            <i class="fas fa-user me-2"></i>View Profile
                        </a>
                    </div>
                </div>
            </aside>
        </div>
    </main>

    @include('layouts.volunteer_footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @if(!empty($autoOpenAssignments))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var el = document.getElementById('assignmentsModal');
            if (el && typeof bootstrap !== 'undefined') {
                var modal = new bootstrap.Modal(el);
                modal.show();
            }
        });
    </script>
    @stack('scripts')
@endif


</html>
