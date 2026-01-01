{{-- resources/views/admin/users/ngo_profile.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>NGO Profile | Admin View | SukaRelawan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Bootstrap & Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- Reuse volunteer profile css --}}
    <link rel="stylesheet" href="{{ asset('css/volunteer_profile.css') }}">

    <style>
        .content-wrapper {
            margin-left: 70px;
            padding-bottom: 40px;
        }

        .content-wrapper .card {
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        /* Same tab style as admin.users.volunteer_profile */
        .volunteer-profile-tabs {
            display: flex;
            flex-wrap: nowrap !important;
            overflow-x: auto;
            white-space: nowrap;
            border-bottom: 1px solid #dee2e6 !important;
        }


        .volunteer-profile-tabs .nav-item {
            white-space: nowrap;
        }

        .volunteer-profile-tabs .nav-link {
            white-space: nowrap;
        }

        .event-card {
            border-radius: 12px;
            overflow: hidden;
        }
    </style>
</head>

<body>

    {{-- ADMIN NAV --}}
    @include('layouts.admin_nav')

    @include('layouts.messages')

    <div class="content-wrapper">
        {{-- Cover Section --}}
        <div class="cover-container position-relative" style="min-height: 300px;">
            <div class="cover-photo"
                style="background: url('{{ $profile->coverPhoto ? asset('images/covers/' . $profile->coverPhoto) : asset('images/default-cover.jpg') }}') center/cover;">
            </div>

            <img src="{{ $profile->profilePhoto
                ? asset('images/profiles/' . $profile->profilePhoto)
                : asset('assets/default-profile.png') }}"
                class="profile-avatar rounded-circle img-fluid" alt="Profile Photo">

            <div class="profile-header">
                <h1 class="profile-name">{{ $profile->organizationName ?? 'Unnamed NGO' }}</h1>
                <div>
                    <span class="profile-badge">NGO</span>
                </div>
            </div>

            {{-- No edit button for admin --}}
        </div>

        {{-- Main Content --}}
        <div style="margin-left: 70px; margin-right:40px;">
            <div class="container pb-5" style="max-width: 1400px;">
                <div class="row g-4">
                    {{-- LEFT COLUMN: Info --}}
                    <div class="col-lg-3">
                        <div class="card">
                            <div class="card-header bg-light border-bottom"
                                style="padding: 1rem 1.25rem; font-weight: 600;">
                                <i class="fas fa-info-circle me-2"></i>NGO Info
                            </div>
                            <div class="card-body" style="padding: 1.25rem;">
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
                                                <a href="{{ $profile->website }}" target="_blank" rel="noopener">
                                                    {{ $profile->website }}
                                                </a>
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
                            <ul class="nav nav-tabs volunteer-profile-tabs" id="profileTabs" role="tablist">
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
                                            $start = $event->eventStart
                                                ? \Carbon\Carbon::parse($event->eventStart)
                                                : null;
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
                                                            <i class="fas fa-coins me-1"></i>
                                                            {{ $event->eventPoints }}
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
                                <div class="tab-pane fade {{ request('past_page') ? 'show active' : '' }}"
                                    id="past" role="tabpanel" aria-labelledby="past-tab">

                                    @forelse($pastEvents as $event)
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
                                            <img src="{{ $eventImage }}" class="card-img-top"
                                                alt="{{ $event->eventTitle }}"
                                                style="height: 180px; object-fit: cover; border-top-left-radius: 8px; border-top-right-radius: 8px;">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <h5 class="card-title mb-1">{{ $event->eventTitle }}</h5>
                                                    @if (!empty($event->eventPoints))
                                                        <span class="badge bg-light text-primary p-2 mb-1">
                                                            <i class="fas fa-coins me-1"></i>
                                                            {{ $event->eventPoints }}
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
                                                : \Illuminate\Support\Str::limit(
                                                    strip_tags($post->content),
                                                    120,
                                                    '...',
                                                );

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
                                            $ownerEditRoute = $isOwner
                                                ? route('ngo.blogs.edit', $post->blogPost_id)
                                                : null;
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
                                                                <span
                                                                    class="badge bg-secondary p-2 mb-1">Private</span>
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

                    {{-- RIGHT COLUMN: simple stats --}}
                    <div class="col-lg-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="mb-2 text-muted small">Total Events Created</div>
                                <div class="mb-3">
                                    <button class="btn btn-primary btn-lg w-100">{{ $totalEvents ?? 0 }}
                                        Events</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> {{-- row --}}
            </div> {{-- container --}}
        </div>
    </div> {{-- content-wrapper --}}

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
