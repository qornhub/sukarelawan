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
        .event-header {
            width: 100%;
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid #eee;
            background: transparent;
            box-shadow: none;
            border-radius: 0;
        }

        .header-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.5rem;
        }

        .nav-container {
            display: flex;
            justify-content: center;
            width: 100%;
        }

        .nav-tabs {
            display: flex;
            background: white;
            border-radius: 50px;
            padding: 0.25rem;
            position: relative;
        }

        .nav-tab {
            padding: 0.75rem 1.5rem;
            text-decoration: none;
            color: var(--text-color);
            font-weight: 500;
            border-radius: 50px;
            transition: all 0.3s ease;
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-tab i {
            font-size: 0.9rem;
        }

        .nav-tab.active {
            color: white;
        }

        .nav-indicator {
            position: absolute;
            height: calc(100% - 0.5rem);
            border-radius: 50px;
            background: var(--primary-color);
            transition: all 0.3s ease;
            top: 0.25rem;
            left: 0.25rem;
            z-index: 0;
            pointer-events: none; /* <- important so it never blocks links */
        }

        .back-button-container {
            width: 100%;
            display: flex;
            justify-content: flex-start;
        }
    </style>
</head>

<body>
    @include('layouts.ngo_header')
    @include('layouts.messages')

    @php
        // image helpers
        $eventImage = $event->eventImage ?? null;
        $eventHeroUrl = $eventImage ? asset('images/events/' . $eventImage) : asset('images/events/default-event.jpg');

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


   <div class="event-header mt-3">
    <div class="header-content">
        <div class="nav-container">
            <nav class="nav-tabs">
                <div class="nav-indicator" style="width: 88px; transform: translateX(0);"></div>

                {{-- Event Tab --}}
                <a href="{{ route('ngo.profile.eventEditDelete', $event->event_id) }}"
                   class="nav-tab {{ request()->routeIs('ngo.profile.eventEditDelete') ? 'active' : '' }}"
                   data-tab="event">
                    <i class="fas fa-calendar-day"></i>
                    <span>Event</span>
                </a>

                {{-- Edit Tab --}}
                <a href="{{ route('ngo.events.event_edit', $event->event_id) }}"
                   class="nav-tab {{ request()->routeIs('ngo.events.event_edit') ? 'active' : '' }}"
                   data-tab="edit"
                   onclick="event.stopPropagation();">
                    <i class="fas fa-edit"></i>
                    <span>Edit</span>
                </a>

                {{-- Manage Tab --}}
                <a href="{{ route('ngo.events.manage', $event->event_id) }}"
                   class="nav-tab {{ request()->routeIs('ngo.events.manage') ? 'active' : '' }}"
                   data-tab="manage">
                    <i class="fas fa-tasks"></i>
                    <span>Manage</span>
                </a>

                
            </nav>
        </div>
    </div>
</div>


    <div class="container">
        <div class="register-row text-end" style="margin-top: 18px; margin-bottom: 18px;">
            <div class="d-flex justify-content-end gap-2 mt-3">

                <!-- Wrap buttons in equal-width container -->
                <div class="d-flex gap-2" style="width: 300px;"> <!-- adjust width as needed -->
                    <!-- Edit -->


                    <!-- Delete -->
                    <form action="{{ route('ngo.events.destroy', $event->event_id) }}" method="POST"
                        onsubmit="return confirm('Are you sure you want to delete this event? This action cannot be undone.');"
                        class="flex-fill">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-trash-alt me-1"></i> Delete
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>




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
                    </h4>


                    <div class="participants">
                        @if ($registrations->count())
                            <div class="participants-avatars">

                                @foreach ($registrations->take(6) as $reg)
                                    @php
                                        $user = $reg->user ?? null;
                                        $filename = optional($user->volunteerProfile)->profilePhoto ?? null;
                                        $avatarUrl = $filename
                                            ? asset('images/profiles/' . $filename)
                                            : asset('images/default-profile.png');
                                        $title = optional($user)->name ?? ($reg->name ?? 'Volunteer');
                                    @endphp

                                    @if ($user)
                                        <a href="{{ route('volunteer.profile.show', $user->id) }}">
                                            <img src="{{ $avatarUrl }}" alt="participant" class="avatar"
                                                title="{{ $title }}">
                                        </a>
                                    @else
                                        <img src="{{ $avatarUrl }}" alt="participant" class="avatar"
                                            title="{{ $title }}">
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

                                <section class="content-card">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="section-heading mb-0">
                            <i class="fas fa-comments icon"></i>
                            Comments ({{ $comments->total() ?? count($comments) }})
                        </h4>
                    </div>


                    @include('partials.events.comments', [
                        'event' => $event,
                        'comments' => $comments,
                        // optional:
                        'profileRelation' => 'ngoProfile',
                        'profileRoute' => 'ngo.profile.show',
                        'profileStoragePath' => 'images/profiles/',
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

                        {{-- Skills --}}
                        <div class="detail-item">
                            <div class="detail-label">
                                <i class="fas fa-tools me-1"></i>Skills
                            </div>
                            <div class="detail-value">
                                @if ($skills->count())
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach ($skills as $skill)
                                            {{-- skill table column is skillName --}}
                                            <span class="badge bg-light text-dark border">
                                                <i class="fas fa-check-circle text-success me-1"></i>
                                                {{ $skill->skillName ?? ($skill->name ?? 'Skill') }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted">No specific skills required</span>
                                @endif
                            </div>
                        </div>

                        {{-- Requirements --}}
                        <div class="detail-item">
                            <div class="detail-label">
                                <i class="fas fa-list-check me-1"></i>Requirements
                            </div>
                            <div class="detail-value">
                                @if ($event->requirements && trim($event->requirements) !== '')
                                    {!! nl2br(e($event->requirements)) !!}
                                @else
                                    <span class="text-muted">No requirements specified</span>
                                @endif
                            </div>
                        </div>

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
                        <div class="org-avatar">
                            <i class="fas fa-user"></i>
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

    @include('layouts.ngo_footer')
    <script src="{{ asset('js/events/createEvents.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>
