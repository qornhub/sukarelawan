{{-- resources/views/ngo/profile/show.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $profile->organizationName ?? 'NGO Profile' }} | SukaRelawan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Bootstrap & Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- Reuse your profile css (adjust path if different) --}}
    <link rel="stylesheet" href="{{ asset('css/volunteer_profile.css') }}">
    <style>
        .event-card-link {
            display: block;
            transition: transform .12s ease, box-shadow .12s ease;
            border-radius: .5rem;
            overflow: hidden;
        }

        .event-card-link:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 22px rgba(29, 39, 54, 0.06);
            text-decoration: none;
        }

        .event-card-link:focus {
            outline: 3px solid rgba(11, 105, 255, 0.12);
            outline-offset: 2px;
        }

        .event-card-link .card-img-top {
            display: block;
            width: 100%;
        }
    </style>
</head>

<body>
    {{-- header --}}
    @auth
        @if (auth()->user()->role->roleName === 'volunteer')
            @include('layouts.volunteer_header')
        @elseif(auth()->user()->role->roleName === 'ngo')
            @include('layouts.ngo_header')
        @else
            @include('layouts.ngo_header') {{-- Default to NGO header for other roles --}}
        @endif
    @else
        @include('layouts.ngo_header') {{-- Show NGO header for non-authenticated users --}}
    @endauth

    @include('layouts.messages')

    <div class="content-wrapper">

        {{-- COVER: white card with banner inside --}}
        <div class="cover-container position-relative">
            <div class="cover-photo"
                style="background-image: url('{{ $profile && $profile->coverPhoto ? asset('images/covers/' . $profile->coverPhoto) : asset('images/default-cover.jpg') }}');">
            </div>

            {{-- Avatar --}}
            <img src="{{ $profile && $profile->profilePhoto ? asset('images/profiles/' . $profile->profilePhoto) : asset('images/default-profile.png') }}"
                class="profile-avatar rounded-circle" alt="{{ $profile->organizationName ?? 'NGO' }}">



            {{-- Name + role --}}
            <div class="profile-header">
                <h1 class="profile-name">{{ $profile->organizationName ?? 'Unnamed NGO' }}</h1>
                <div>
                    <span class="profile-badge">{{ ucfirst(auth()->user()->role->roleName) }}</span>
                </div>
            </div>




            {{-- Edit button - Only show if user owns this profile or is admin --}}
            @auth
                @if (auth()->user()->id === $profile->user_id || auth()->user()->role->roleName === 'admin')
                    {{-- Debug: Check what route is being generated --}}
                    {{-- <div style="position: absolute; top: 0; left: 0; background: white; padding: 10px; z-index: 1000;">
                        Route: {{ route('ngo.profile.edit') }}
                        User ID: {{ auth()->user()->id }}
                        Profile User ID: {{ $profile->user_id }}
                    </div> --}}

                    <a href="{{ route('ngo.profile.edit') }}" class="edit-profile-btn">
                        <i class="fas fa-pen me-1"></i> Edit Profile
                    </a>
                @endif
            @endauth
        </div>

        <div class="container main-container pb-5">

            {{-- Main grid: left(3) / middle(6) / right(3) --}}
            <div class="row">
                {{-- LEFT: Info --}}
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-info-circle me-2"></i>Info
                        </div>
                        <div class="card-body">
                            <div class="info-item">
                                <div class="info-icon"><i class="fas fa-align-left"></i></div>
                                <div>
                                    <div class="text-muted small">About</div>
                                    <div>{{ $profile->about ?? '-' }}</div>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-icon"><i class="fas fa-globe"></i></div>
                                <div>
                                    <div class="text-muted small">Website</div>
                                    <div>
                                        @if (!empty($profile->website))
                                            <a href="{{ $profile->website }}" target="_blank"
                                                rel="noopener">{{ $profile->website }}</a>
                                        @else
                                            -
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-icon"><i class="fas fa-phone"></i></div>
                                <div>
                                    <div class="text-muted small">Contact Number</div>
                                    <div>{{ $profile->contactNumber ?? '-' }}</div>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-icon"><i class="fas fa-envelope"></i></div>
                                <div>
                                    <div class="text-muted small">Email</div>
                                    <div>{{ optional($profile->user)->email ?? '-' }}</div>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-icon"><i class="fas fa-flag"></i></div>
                                <div>
                                    <div class="text-muted small">Country</div>
                                    <div>{{ $profile->country ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- MIDDLE: Tabs --}}
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <ul class="nav nav-tabs">
                                <li class="nav-item">
                                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#ongoing">
                                        <i class="fas fa-play-circle me-2"></i>Ongoing Event
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#past">
                                        <i class="fas fa-history me-2"></i>Past Event
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#blog">
                                        <i class="fas fa-blog me-2"></i>My Blog
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content mt-3">
                                {{-- ongoing events --}}
                                <div class="tab-pane fade show active" id="ongoing">
                                    @forelse($ongoingEvents as $event)
                                        @php
                                            $eventImage = $event->eventImage
                                                ? asset('images/events/' . $event->eventImage)
                                                : asset('images/events/default-event.jpg');
                                            $start = $event->eventStart
                                                ? \Carbon\Carbon::parse($event->eventStart)
                                                : null;
                                            $end = $event->eventEnd ? \Carbon\Carbon::parse($event->eventEnd) : null;
                                        @endphp

                                        <a href="{{ route('ngo.profile.eventEditDelete', $event->event_id) }}"
                                            class="card event-card mb-3 event-card-link"
                                            style="text-decoration: none; color: inherit;">
                                            @if (!empty($event->eventImage))
                                                <img src="{{ $eventImage }}" class="card-img-top"
                                                    alt="{{ $event->eventTitle }}"
                                                    style="height:200px; object-fit:cover;">
                                            @endif

                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <h5 class="card-title mb-1">{{ $event->eventTitle }}</h5>
                                                    <span
                                                        class="event-date">{{ $start ? $start->format('j M Y') : '-' }}</span>
                                                </div>

                                                <p class="text-muted small mb-2 mt-2">{{ $event->eventSummary }}</p>

                                                <div class="d-flex flex-wrap gap-3">
                                                    <div><i class="fas fa-clock text-primary me-1"></i>
                                                        {{ $start ? $start->format('j M Y g:i A') : '-' }} –
                                                        {{ $end ? $end->format('j M Y g:i A') : '—' }}
                                                    </div>
                                                    <div><i class="fas fa-map-marker-alt text-primary me-1"></i>
                                                        {{ $event->venueName ?? '-' }}</div>
                                                </div>
                                            </div>
                                        </a>
                                    @empty
                                        <div class="card">
                                            <div class="card-body text-center py-5">
                                                <i class="fas fa-calendar-times text-muted fa-2x mb-3"></i>
                                                <p class="text-muted mb-0">No ongoing events</p>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>

                                {{-- Past --}}
                                <div class="tab-pane fade" id="past">
                                    @forelse($pastEvents as $event)
                                        <div class="card event-card mb-3">


                                            @if (!empty($event->eventImage))
                                                <img src="{{ asset('images/events/' . $event->eventImage) }}"
                                                    class="card-img-top" alt="{{ $event->eventTitle }}"
                                                    style="height:200px; object-fit:cover;">
                                            @endif

                                            <div class="card-body">
                                                <h5 class="card-title mb-1">{{ $event->eventTitle }}</h5>
                                                <p class="text-muted small mb-2 mt-2"> {{ $event->eventSummary }}</p>

                                                <div class="d-flex flex-wrap gap-3">
                                                    <div><i class="fas fa-calendar-alt me-1"></i>
                                                        {{ optional($event->eventEnd) ? \Carbon\Carbon::parse($event->eventEnd)->format('j M Y') : '-' }}
                                                    </div>
                                                    <div><i class="fas fa-clock me-1"></i>
                                                        {{ $event->eventStart ?? '-' }} –
                                                        {{ $event->eventEnd ?? '-' }}</div>
                                                    <div><i class="fas fa-map-marker-alt me-1"></i>
                                                        {{ $event->venueName ?? '-' }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="card">
                                            <div class="card-body text-center py-5">
                                                <i class="fas fa-history text-muted fa-2x mb-3"></i>
                                                <p class="text-muted mb-0">No past events</p>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>

                                {{-- Blog --}}
                                <div class="tab-pane fade" id="blog">
                                    @forelse($blogs as $blog)
                                        <div class="card mb-3">
                                            <div class="card-body">
                                                <h5 class="card-title">{{ $blog->title }}</h5>
                                                <p class="text-muted small mb-2">Published:
                                                    {{ \Carbon\Carbon::parse($blog->created_at)->format('j M Y') }}</p>
                                                <p class="mb-2">
                                                    {{ Str::limit($blog->excerpt ?? $blog->content, 140) }}</p>
                                                <a href="{{ route('blog.show', $blog->id) }}"
                                                    class="btn btn-sm btn-outline-primary">Read more</a>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="card">
                                            <div class="card-body text-center py-5">
                                                <i class="fas fa-file-alt text-muted fa-2x mb-3"></i>
                                                <p class="text-muted mb-0">No blog posts yet</p>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT: Total Events --}}
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="mb-2 text-muted small">Total Events Created</div>
                            <div class="mb-3">
                                <button class="btn btn-primary btn-lg w-100">{{ $totalEvents ?? 0 }} Events</button>
                            </div>
                            <a href="{{ route('ngo.events.index') ?? '#' }}"
                                class="btn btn-outline-secondary btn-sm w-100">Manage Events</a>
                        </div>
                    </div>
                </div>
            </div> {{-- row --}}
        </div> {{-- container --}}
    </div> {{-- content-wrapper --}}

    {{-- footer --}}
    @include('layouts.ngo_footer')

    {{-- scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('click', function(e) {
            // if the click was on an inner interactive element inside a clickable card, do nothing
            const innerInteractive = e.target.closest('button, a, input, select, textarea, label');
            if (!innerInteractive) return;

            // find if that interactive is inside a clickable event card link
            const cardLink = innerInteractive.closest('.event-card-link');
            if (!cardLink) return;

            // if the interactive element itself is an anchor which is the card link, allow default
            if (innerInteractive === cardLink) return;

            // otherwise stop the event from bubbling to the outer link
            e.stopPropagation();
        });
    </script>

</body>

</html>
