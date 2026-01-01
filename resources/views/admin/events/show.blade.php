{{-- resources/views/admin/events/show.blade.php --}}
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
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="{{ asset('css/events/event_show.css') }}">
    <link rel="stylesheet" href="{{ asset('css/blogs/comment.css') }}">

    <!-- Small admin-specific CSS for settings button (keeps everything else in show.css) -->
    <style>
        /* place settings button in document flow so margin-bottom works and allow right alignment */
        .post-settings {
            position: relative;
            margin-bottom: 18px;
            /* space below the settings button */
            text-align: right;
            /* align to right edge of container */
        }

        .post-settings .btn-settings {
            display: inline-block;
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(0, 0, 0, 0.08);
            padding: 8px 10px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
            color: #333;
        }

        .post-settings .dropdown-menu {
            min-width: 180px;
        }

        @media (max-width: 767.98px) {
            .post-settings {
                margin-bottom: 12px;
                text-align: center;
            }
        }

        /* Sentiment badge only — for event comments (scoped to #event-comments) */

        /* wrapper (if you place the badge below the content) */
        #event-comments .sentiment-row {
            display: block;
            margin-top: 0.5rem;
            /* space between content/edit form and badge */
            text-align: right;
            /* change to right if you prefer right-aligned */
        }

        /* badge (pill) */
        #event-comments .sentiment-badge {
            display: inline-block;
            font-size: 0.75rem;
            padding: 0.18rem 0.5rem;
            border-radius: 999px;
            font-weight: 600;
            line-height: 1;
            vertical-align: middle;
            color: #ffffff;
            /* white text on colored pills */
            box-shadow: none;
        }

        /* colors */
        #event-comments .sentiment-badge.positive {
            background: #198754;
        }

        /* green */
        #event-comments .sentiment-badge.negative {
            background: #fd7e14;
        }

        /* orange */
        #event-comments .sentiment-badge.toxic {
            background: #dc3545;
        }

        /* red */

        /* small mobile tweak */
        @media (max-width: 576px) {
            #event-comments .sentiment-badge {
                font-size: 0.70rem;
                padding: 0.14rem 0.4rem;
            }
        }

        /* specificity booster (in case other CSS overrides) */
        #event-comments .comments-list .sentiment-badge {
            /* noop: keeps selector strong; uncomment !important only if absolutely needed */
            /* color: #fff !important; */
        }
    </style>
</head>

<body>

    @include('layouts.admin_nav')

    @php
        // image helpers
        $eventImage = $event->eventImage ?? null;
        $eventHeroUrl = $eventImage ? asset('images/events/' . $eventImage) : asset('assets/default_event.jpg');

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

    {{-- WRAPPER: push everything right by 70px so admin_nav can expand --}}
    <div style="margin-left: 70px;">

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

        {{-- SETTINGS ICON (replaces register button). right-aligned under hero --}}
        <div class="container">
            <div class="post-settings" style="margin-top:18px;">
                <div class="btn-group">
                    <button type="button" class="btn btn-settings btn-sm dropdown-toggle" data-bs-toggle="dropdown"
                        aria-expanded="false" title="Event settings">
                        <i class="fa fa-cog"></i>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end">

                        {{-- MANAGE EVENT (View Only) --}}
                        <li>
                            <a href="{{ route('admin.events.view', $event->event_id) }}" class="dropdown-item">
                                <i class="fas fa-tasks me-2"></i> Manage Event
                            </a>
                        </li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        {{-- DELETE EVENT --}}
                        <li>
                            <form action="{{ route('admin.events.destroy', $event->event_id) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to permanently delete this event? This action cannot be undone.');"
                                class="m-0 p-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fa fa-trash me-2"></i> Delete event
                                </button>
                            </form>
                        </li>

                    </ul>
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

                    {{-- Requirements (moved under Mission Description; same structure) --}}
                    <section class="content-card mt-3">
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
                    <section class="content-card mt-3">
                        <h4 class="section-heading">
                            <i class="fas fa-heart icon"></i>
                            Mission Impact
                        </h4>
                        <div class="text-content">
                            {!! nl2br(e($event->eventImpact ?? 'No impact information provided.')) !!}
                        </div>
                    </section>

                    {{-- SDG Addressed --}}
                    <section class="content-card mt-3">
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
                    <section class="content-card mt-3">
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
                                                    : asset('assets/default-profile.png');
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
                                    <div>No participants yet.</div>
                                </div>
                            @endif
                        </div>
                    </section>

                    {{-- comments --}}
                    <section class="content-card mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="section-heading mb-0">
                                <i class="fas fa-comments icon"></i>
                                Comments ({{ $comments->total() ?? count($comments) }})
                            </h4>
                        </div>

                        {{-- include comments partial and pass admin flag so partial can show delete controls --}}
                        @include('partials.events.comments', [
                            'event' => $event,
                            'comments' => $comments,
                            'profileRelation' => 'volunteerProfile',
                            'profileRoute' => 'volunteer.profile.show',
                            'profileStoragePath' => 'images/profiles/',
                            'isAdmin' => true, // allow partial to show admin delete on comments
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
                    <div class="sidebar-card mt-3">
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

                        <div class="event-details mt-3">
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
                                        <div class="d-flex flex-column align-items-end gap-2">
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
                    <div class="sidebar-card organizer-card mt-3">
                        <div class="organizer-header">
                            @php
                                use Illuminate\Support\Facades\Storage;

                                $default = asset('assets/default-profile.png');
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
                                <div class="organizer-name">{{ optional($event->organizer)->name ?? 'Organizer' }}
                                </div>
                            </div>
                        </div>

                        <div class="organizer-actions">
                            @php
                                $phone = optional($event->organizer)->phone ?? null;
                                $waLink = $phone
                                    ? "https://wa.me/{$phone}"
                                    : 'https://wa.me/?text=' .
                                        urlencode(
                                            'Hello, I am interested in your event: ' . ($event->eventTitle ?? ''),
                                        );
                            @endphp

                            <a href="{{ $waLink }}" target="_blank" class="btn-organizer primary">
                                <i class="fab fa-whatsapp me-2"></i>WhatsApp Organizer
                            </a>
                            @php
                                $organizer = optional($event->organizer);
                                $profileId = $organizer->ngo_id ?? ($organizer->id ?? '#');
                            @endphp

                            <a href="{{ route('ngo.profile.show', ['id' => $profileId]) }}"
                                class="btn-organizer secondary">
                                <i class="fas fa-user me-2"></i>View Profile
                            </a>

                        </div>
                    </div>
                </aside>
            </div>
        </main>

    </div> {{-- end wrapper margin-left:70px --}}

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>
