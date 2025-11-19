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

        /* Ensure text is readable on highlighted rows */
.rank-highlight,
.list-group-item.rank-highlight {
    background-color: var(--primary-purple);
    color: #fff;
}

/* Make anchors (names) white and remove default link color in highlighted rows */
.rank-highlight a,
.rank-highlight a:visited,
.rank-highlight a:hover,
.rank-highlight a:focus {
    color: #fff !important;
    text-decoration: none;
}

/* Make small / muted text readable on purple */
.rank-highlight .small,
.rank-highlight .text-muted,
.rank-highlight .me-2,
.rank-highlight .list-group-numbered>li::marker {
    color: rgba(255,255,255,0.92) !important;
}

/* Style badges inside highlighted rows so they remain visible (lighter translucent pill) */
.rank-highlight .badge {
    background-color: rgba(255,255,255,0.14) !important;
    color: #fff !important;
    border: 1px solid rgba(255,255,255,0.18);
}

/* Slight visual lift for avatar on highlighted row */
.rank-highlight img {
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    border: 2px solid rgba(255,255,255,0.06);
}

/* Podium-specific: ensure podium highlighted names are white (for top-3) */
.podium-highlight a,
.podium-highlight a:visited,
.podium-highlight a:focus,
.podium-highlight a:hover {
    color: #fff !important;
}

/* If you used the .podium-current-link class above, style it too */
.podium-current-link {
    color: #fff !important;
}

/* Optional: adjust list marker when using ordered list and highlight */
.list-group-numbered>li.rank-highlight::marker {
    color: #fff;
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
                <span class="profile-badge">
                    {{ $profile instanceof \App\Models\NGOProfile ? 'NGO' : 'Profile' }}
                </span>


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

                <div class="col-lg-6">
                    <div class="card" style="border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                        <!-- Tabs Navigation -->
                        <ul class="nav nav-tabs" id="profileTabs" role="tablist">
                            <li class="nav-item me-2" role="presentation">
                                <button
                                    class="nav-link {{ !request('past_page') && !request()->has('tab') ? 'active' : '' }}"
                                    id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming" type="button"
                                    role="tab" aria-controls="upcoming" aria-selected="true">
                                    <i class="fas fa-calendar-check me-2"></i>Ongoing Events
                                </button>
                            </li>

                            <li class="nav-item me-2" role="presentation">
                                <button class="nav-link {{ request('past_page') ? 'active' : '' }}" id="past-tab"
                                    data-bs-toggle="tab" data-bs-target="#past" type="button" role="tab"
                                    aria-controls="past" aria-selected="false">
                                    <i class="fas fa-history me-2"></i>Past Events
                                </button>
                            </li>

                            
                                    <li class="nav-item me-5" role="presentation">
                                        <button
                                            class="nav-link {{ request()->has('tab') && request('tab') == 'blog' ? 'active' : '' }}"
                                            id="blog-tab" data-bs-toggle="tab" data-bs-target="#blog" type="button"
                                            role="tab" aria-controls="blog" aria-selected="false">
                                            <i class="fas fa-blog me-2"></i>My Blog
                                        </button>
                                    </li>
                               
                        </ul>

                        <!-- Tabs Content -->
                        <div class="tab-content mt-0" id="profileTabsContent">
                            <!-- Ongoing Events Tab (uses $ongoingEvents) -->
                            <div class="tab-pane fade {{ !request('past_page') && !request()->has('tab') ? 'show active' : '' }}"
                                id="upcoming" role="tabpanel" aria-labelledby="upcoming-tab">
                                @forelse($ongoingEvents as $event)
                                    @php
                                        $eventImage = $event->eventImage
                                            ? asset('images/events/' . $event->eventImage)
                                            : asset('images/events/default-event.jpg');
                                        $start = $event->eventStart ? \Carbon\Carbon::parse($event->eventStart) : null;
                                        $end = $event->eventEnd ? \Carbon\Carbon::parse($event->eventEnd) : null;
                                    @endphp

                                    <a href="{{ route('ngo.profile.eventEditDelete', $event->event_id) }}"
                                        class="card event-card mb-3 event-card-link"
                                        style="text-decoration: none; color: inherit;">
                                        <img src="{{ $eventImage }}" class="card-img-top"
                                            alt="{{ $event->eventTitle }}"
                                            style="height: 180px; object-fit: cover; border-top-left-radius: 8px; border-top-right-radius: 8px;">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <h5 class="card-title mb-1">{{ $event->eventTitle }}</h5>
                                                @if (!empty($event->eventPoints))
                                                    <span class="badge bg-light text-primary p-2 mb-1">
                                                        <i class="fas fa-coins me-1"></i> {{ $event->eventPoints }}
                                                        points
                                                    </span>
                                                @endif
                                            </div>

                                            <p class="text-muted small mb-2 mt-2">
                                                {{ $event->eventSummary ?? 'No description available' }}
                                            </p>

                                            <div class="d-flex flex-wrap gap-3 mt-3">
                                                <div>
                                                    <i class="fas fa-clock text-primary me-1"></i>
                                                    {{ $start ? $start->format('j M Y g:i A') : '-' }} –
                                                    {{ $end ? $end->format('j M Y g:i A') : '—' }}
                                                </div>
                                                <div>
                                                    <i class="fas fa-map-marker-alt text-primary me-1"></i>
                                                    {{ $event->venueName ?? '-' }}
                                                </div>
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

                                {{-- Pagination for Ongoing Event --}}
                                 
                                @if ($ongoingEvents->hasPages())
                                    <div class="d-flex justify-content-center mt-3 events-pagination">
                                        {{ $ongoingEvents->links('pagination::bootstrap-5') }}
                                    </div>
                                @endif
                            </div>

                            <!-- Past Events Tab (uses $pastEvents) -->
                            <!-- Past Events Tab (matches Ongoing Events design) -->
                            <div class="tab-pane fade {{ request('past_page') ? 'show active' : '' }}" id="past"
                                role="tabpanel" aria-labelledby="past-tab">

                                @forelse($pastEvents as $event)
                                    @php
                                        $eventImage = $event->eventImage
                                            ? asset('images/events/' . $event->eventImage)
                                            : asset('images/events/default-event.jpg');
                                        $start = $event->eventStart ? \Carbon\Carbon::parse($event->eventStart) : null;
                                        $end = $event->eventEnd ? \Carbon\Carbon::parse($event->eventEnd) : null;
                                    @endphp

                                    <a href="{{ route('ngo.profile.eventEditDelete', $event->event_id) }}"
                                        class="card event-card mb-3 event-card-link"
                                        style="text-decoration: none; color: inherit;">
                                        <img src="{{ $eventImage }}" class="card-img-top"
                                            alt="{{ $event->eventTitle }}"
                                            style="height: 180px; object-fit: cover; border-top-left-radius: 8px; border-top-right-radius: 8px;">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <h5 class="card-title mb-1">{{ $event->eventTitle }}</h5>
                                                @if (!empty($event->eventPoints))
                                                    <span class="badge bg-light text-primary p-2 mb-1">
                                                        <i class="fas fa-coins me-1"></i> {{ $event->eventPoints }}
                                                        points
                                                    </span>
                                                @endif
                                            </div>

                                            <p class="text-muted small mb-2 mt-2">
                                                {{ $event->eventSummary ?? 'No description available' }}
                                            </p>

                                            <div class="d-flex flex-wrap gap-3 mt-3">
                                                <div>
                                                    <i class="fas fa-clock text-primary me-1"></i>
                                                    {{ $start ? $start->format('j M Y g:i A') : '-' }} –
                                                    {{ $end ? $end->format('j M Y g:i A') : '—' }}
                                                </div>
                                                <div>
                                                    <i class="fas fa-map-marker-alt text-primary me-1"></i>
                                                    {{ $event->venueName ?? '-' }}
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @empty
                                    <div class="card">
                                        <div class="card-body text-center py-4">
                                            <i class="fas fa-history text-muted fa-2x mb-3"></i>
                                            <p class="text-muted mb-0">No past events</p>
                                        </div>
                                    </div>
                                @endforelse

                                
                                 {{-- Pagination for Past Events --}}
                                @if ($pastEvents->hasPages())
                                    <div class="d-flex justify-content-center mt-3 events-pagination">
                                        {{ $pastEvents->links('pagination::bootstrap-5') }}
                                    </div>
                                @endif
                            </div>


                            <!-- Blog Posts Tab (uses $blogPosts) -->
                            <div class="tab-pane fade {{ request()->has('tab') && request('tab') == 'blog' ? 'show active' : '' }}"
                                id="blog" role="tabpanel" aria-labelledby="blog-tab">

                                @forelse($blogPosts as $post)
                                    @php
                                        $isOwner = auth()->check() && auth()->id() === $profile->user_id;
                                        $canView = $post->status === 'published' || $isOwner;

                                        // excerpt: prefer blogSummary, fallback to stripped content
                                        $excerpt = $post->blogSummary
                                            ? $post->blogSummary
                                            : \Illuminate\Support\Str::limit(strip_tags($post->content), 120, '...');

                                        $imageUrl = $post->image
                                            ? asset('images/Blog/' . $post->image)
                                            : asset('images/Blog/default-blog.jpg');

                                        $displayDate = $post->published_at
                                            ? \Carbon\Carbon::parse($post->published_at)->format('j M Y')
                                            : ($post->created_at
                                                ? \Carbon\Carbon::parse($post->created_at)->format('j M Y')
                                                : '-');

                                        // public & owner routes for blog (NGO controller handles manage/edit/destroy)
                                        $publicRoute = route('blogs.show', $post->blogPost_id);
                                        $ownerManageRoute = $isOwner
                                            ? route('ngo.blogs.manage', $post->blogPost_id)
                                            : null;
                                        $ownerEditRoute = $isOwner ? route('ngo.blogs.edit', $post->blogPost_id) : null;
                                        $ownerDeleteRoute = $isOwner
                                            ? route('ngo.blogs.destroy', $post->blogPost_id)
                                            : null;

                                        $cardLink = $ownerManageRoute ?? $publicRoute;
                                        $authorName =
                                            optional($post->user)->name ??
                                            ($profile->organizationName ?? ($profile->name ?? 'Unknown'));
                                    @endphp

                                    <a href="{{ $cardLink }}" class="text-decoration-none text-reset">
                                        <div class="card event-card mb-3">
                                            @if ($imageUrl)
                                                <img src="{{ $imageUrl }}" class="card-img-top"
                                                    alt="{{ $post->title }}"
                                                    style="height: 180px; object-fit: cover; border-top-left-radius: 8px; border-top-right-radius: 8px;">
                                            @endif

                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <h5 class="card-title mb-1">{{ $post->title }}</h5>

                                                    @if ($post->status !== 'published')
                                                        @if ($isOwner)
                                                            <span class="badge bg-warning text-dark p-2 mb-1">
                                                                <i class="fas fa-edit me-1"></i> Draft
                                                            </span>
                                                        @else
                                                            <span class="badge bg-secondary p-2 mb-1">Private</span>
                                                        @endif
                                                    @endif
                                                </div>

                                                <p class="text-muted small mb-2 mt-2">{{ $excerpt }}</p>

                                                <div class="d-flex flex-wrap gap-3 mt-3 small text-muted">
                                                    <div>
                                                        <i class="fas fa-calendar text-primary me-1"></i>
                                                        {{ $displayDate }}
                                                    </div>
                                                    <div>
                                                        <i class="fas fa-folder text-primary me-1"></i>
                                                        {{ optional($post->category)->categoryName ?? 'Uncategorized' }}
                                                    </div>
                                                    <div>
                                                        <i class="fas fa-user text-primary me-1"></i>
                                                        {{ $authorName }}
                                                    </div>
                                                </div>

                                                {{-- Owner inline actions (not part of the card link) --}}
                                                @if ($isOwner)
                                                    <div class="d-flex justify-content-start gap-2 mt-3">
                                                        <a href="{{ $ownerEditRoute }}"
                                                            class="btn btn-sm btn-outline-success"
                                                            onclick="event.stopPropagation();">
                                                            <i class="fas fa-pen me-1"></i> Edit
                                                        </a>

                                                        <form action="{{ $ownerDeleteRoute }}" method="POST"
                                                            class="d-inline-block"
                                                            onsubmit="event.stopPropagation(); return confirm('Delete this post?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="btn btn-sm btn-outline-danger">
                                                                <i class="fas fa-trash me-1"></i> Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                @empty
                                    <div class="card">
                                        <div class="card-body text-center py-4">
                                            <i class="fas fa-file-alt text-muted fa-2x mb-3"></i>
                                            <p class="text-muted mb-0">No blog posts yet</p>
                                        </div>
                                    </div>
                                @endforelse

                                @if ($blogPosts->hasPages())
                                    <div class="d-flex justify-content-center mt-3 events-pagination">
                                        {{ $blogPosts->withQueryString()->links('pagination::bootstrap-5') }}
                                    </div>
                                @endif
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
    <script src="js/show.js"></script>
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
