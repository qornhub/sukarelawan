<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Profile | SukaRelawan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/volunteer_profile.css') }}">
    <style>
        /* =====================================================
   BLOG POSTS TAB ONLY
   Scope: #blog
===================================================== */

        /* Card wrapper */
        #blog .event-card {
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        #blog .event-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.12);
        }

        /* -----------------------------------------------------
   Blog Image
----------------------------------------------------- */
        #blog .event-card img {
            transition: transform 0.35s ease;
        }

        #blog .event-card:hover img {
            transform: scale(1.03);
        }

        /* -----------------------------------------------------
   Category badge on image
----------------------------------------------------- */
        #blog .event-card .position-absolute {

            letter-spacing: 0.02em;
            text-transform: capitalize;
        }

        /* -----------------------------------------------------
   Card body
----------------------------------------------------- */
        #blog .card-body {
            padding: 1rem 1.1rem 0.9rem;
        }

        /* -----------------------------------------------------
   Title row
----------------------------------------------------- */
        #blog .card-title {
            font-size: 1.05rem;
            font-weight: 700;
            line-height: 1.25;
            color: #1f2937;
        }



        /* -----------------------------------------------------
   Excerpt
----------------------------------------------------- */
        #blog .card-body p {

            line-height: 1.55;
            color: #4b5563;
        }

        /* -----------------------------------------------------
   Date footer (bottom-left) with primary icon
----------------------------------------------------- */
        #blog .card-body .mt-auto {
            margin-top: 0.75rem;
        }

        #blog .card-body .fa-calendar-alt {
            font-size: 0.8rem;
            color: #004aad;
            /* Primary color for the icon */
            margin-right: 4px;
        }

        #blog .card-body span {
            font-size: 0.75rem;
            color: #454545;
            /* subtle text for the date */
        }


        /* -----------------------------------------------------
   Pagination alignment
----------------------------------------------------- */
        #blog .events-pagination {
            margin-top: 1.5rem;
        }

        /* -----------------------------------------------------
   Mobile polish
----------------------------------------------------- */
        @media (max-width: 576px) {
            #blog .card-title {
                font-size: 1rem;
            }

            #blog .event-card img {
                height: 160px !important;
            }
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
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

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <!-- Cover Photo Section -->
        <div class="cover-container position-relative" style="min-height: 300px;">
            <div class="cover-photo"
                style="background: url('{{ $profile->coverPhoto ? asset('images/covers/' . $profile->coverPhoto) : asset('images/default-cover.jpg') }}') center/cover;">
            </div>
            <img src="{{ $profile->profilePhoto
                ? asset('images/profiles/' . $profile->profilePhoto)
                : asset('assets/default-profile.png') }}"
                class="profile-avatar rounded-circle img-fluid" alt="Profile Photo">

            <div class="profile-header">
                <h1 class="profile-name">{{ $profile->name }}</h1>
                <div>
                    <span class="profile-badge">
                        {{ ucfirst(optional($profile->user->role)->roleName ?? 'Volunteer') }}
                    </span>
                </div>
            </div>

            <!-- Edit button (floating on cover) -->
            @if (auth()->id() === $profile->user_id)
                <a href="{{ route('volunteer.profile.edit') }}" class="edit-profile-btn">
                    <i class="fas fa-pen me-1"></i> Edit
                </a>
            @endif
        </div>

        <!-- Main Content Container -->
        <div class="container pb-5" style="max-width: 1400px;">
            <div class="row g-4"> <!-- Added g-4 for consistent gutter -->
                <!-- Left Column - About & Stats -->
                <div class="col-lg-3">
                    <!-- About Card -->
                    <div class="card" style="border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                        <div class="card-header bg-light border-bottom"
                            style="padding: 1rem 1.25rem; font-weight: 600;">
                            <i class="fas fa-user-circle me-2"></i>About
                        </div>
                        <div class="card-body" style="padding: 1.25rem;">
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-{{ $profile->gender === 'male' ? 'mars' : 'venus' }}"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Gender</div>
                                    <div>{{ ucfirst($profile->gender) }}</div>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-birthday-cake"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Date of Birth</div>
                                    <div>{{ \Carbon\Carbon::parse($profile->dateOfBirth)->format('F j, Y') }}</div>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Address</div>
                                    <div>{{ $profile->address }}</div>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Email</div>
                                    <div>{{ auth()->user()->email }}</div>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Contact</div>
                                    <div>{{ $profile->contactNumber }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Volunteer Stats Card -->
                    <div class="card mt-4" style="border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                        <div class="card-header bg-light border-bottom"
                            style="padding: 1rem 1.25rem; font-weight: 600;">
                            <i class="fas fa-chart-line me-2"></i>Volunteer Stats
                        </div>
                        <div class="card-body" style="padding: 1.25rem;">
                            <div class="d-flex flex-column">
                                <div class="stat-card text-center py-3 border-bottom">
                                    <div class="stat-number">{{ count($upcomingEvents) + count($pastEvents) }}</div>
                                    <div class="stat-label">Events</div>
                                </div>

                                <div class="stat-card text-center py-3 border-bottom">
                                    <div class="stat-number">{{ count($blogPosts) }}</div>
                                    <div class="stat-label">Blogs</div>
                                </div>

                                <div class="stat-card text-center py-3">
                                    <div class="stat-number">{{ $userBadges->count() }}</div>
                                    <div class="stat-label">Badges</div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Middle Column - Tabs Content -->
                <div class="col-lg-6">
                    <div class="card" style="border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                        <!-- Tabs Navigation -->
                        <ul class="nav nav-tabs" id="profileTabs" role="tablist">
                            <li class="nav-item me-2" role="presentation">
                                <button
                                    class="nav-link {{ !request('past_page') && !request()->has('tab') ? 'active' : '' }}"
                                    id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming" type="button"
                                    role="tab" aria-controls="upcoming" aria-selected="true">
                                    <i class="fas fa-calendar-check me-2"></i>Upcoming Events
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
                            <!-- Upcoming Events Tab -->
                            <div class="tab-pane fade {{ !request('past_page') && !request()->has('tab') ? 'show active' : '' }}"
                                id="upcoming" role="tabpanel" aria-labelledby="upcoming-tab">
                                @forelse($upcomingEvents as $event)
                                    @php
                                        $eventImage = $event->eventImage
                                            ? asset('images/events/' . $event->eventImage)
                                            : asset('assets/default_event.jpg');
                                        $start = $event->eventStart ? \Carbon\Carbon::parse($event->eventStart) : null;
                                        $end = $event->eventEnd ? \Carbon\Carbon::parse($event->eventEnd) : null;
                                    @endphp

                                    <a href="{{ route('volunteer.profile.registrationEditDelete', $event->event_id) }}"
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
                                            <p class="text-muted mb-0">No upcoming events</p>
                                        </div>
                                    </div>
                                @endforelse

                                {{-- Pagination for Upcoming Events --}}
                                @if ($upcomingEvents->hasPages())
                                    <div class="d-flex justify-content-center mt-3 events-pagination">
                                        {{ $upcomingEvents->links('pagination::bootstrap-5') }}
                                    </div>
                                @endif
                            </div>

                            <!-- Past Events Tab -->
                            <div class="tab-pane fade {{ request('past_page') ? 'show active' : '' }}" id="past"
                                role="tabpanel" aria-labelledby="past-tab">
                                @forelse($pastEvents as $event)
                                    @php
                                        $eventImage = $event->eventImage
                                            ? asset('images/events/' . $event->eventImage)
                                            : asset('assets/default_event.jpg');
                                        $start = $event->eventStart ? \Carbon\Carbon::parse($event->eventStart) : null;
                                        $end = $event->eventEnd ? \Carbon\Carbon::parse($event->eventEnd) : null;
                                        $attendance = optional($event->attendances)->first();
                                        $attendedAt =
                                            $attendance && $attendance->attendanceTime
                                                ? \Carbon\Carbon::parse($attendance->attendanceTime)
                                                : null;
                                        $pointsEarned =
                                            $attendance && isset($attendance->pointEarned)
                                                ? $attendance->pointEarned
                                                : $event->eventPoints ?? null;
                                    @endphp

                                    <a href="{{ route('volunteer.profile.registrationEditDelete', $event->event_id) }}"
                                        class="card event-card mb-3 event-card-link"
                                        style="text-decoration: none; color: inherit;">
                                        <img src="{{ $eventImage }}" class="card-img-top"
                                            alt="{{ $event->eventTitle }}"
                                            style="height: 180px; object-fit: cover; border-top-left-radius: 8px; border-top-right-radius: 8px;">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <h5 class="card-title mb-1">{{ $event->eventTitle }}</h5>
                                                @if (!empty($pointsEarned))
                                                    <span class="badge bg-light text-success p-2 mb-1">
                                                        <i class="fas fa-coins me-1"></i> {{ $pointsEarned }} pts
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
                                                @if ($attendedAt)
                                                    <div>
                                                        <i class="fas fa-check-circle text-success me-1"></i>
                                                        Attended on {{ $attendedAt->format('j M Y g:i A') }}
                                                    </div>
                                                @endif
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

                           <div class="tab-pane fade {{ request()->has('tab') && request('tab') == 'blog' ? 'show active' : '' }}"
    id="blog" role="tabpanel" aria-labelledby="blog-tab">

    {{-- Container for the list (No 'row' class needed for vertical stacking) --}}
    <div class="d-flex flex-column gap-3"> 
        @forelse($blogPosts as $post)
            @php
                // 1. Check Ownership
                $isOwner = auth()->check() && auth()->id() === $post->user_id;

                // 2. Clean Excerpt Logic
                $rawContent = !empty($post->blogSummary) ? $post->blogSummary : $post->content;
                $excerpt = \Illuminate\Support\Str::limit(
                    trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($rawContent)))),
                    120,
                    '...'
                );

                // 3. Image URL
                $imageUrl = $post->image
                    ? asset('images/Blog/' . $post->image)
                    : asset('assets/default_blog.jpg');

                // 4. Date Logic
                $displayDate = $post->published_at
                    ? \Carbon\Carbon::parse($post->published_at)->format('j M Y')
                    : ($post->created_at
                        ? \Carbon\Carbon::parse($post->created_at)->format('j M Y')
                        : '-');

                // 5. ROUTING LOGIC
                if ($isOwner) {
                    if ($post->status === 'draft') {
                        // Draft -> Direct Edit
                        $cardLink = route('volunteer.blogs.edit', $post->blogPost_id);
                    } else {
                        // Published -> Manage View
                        $cardLink = route('volunteer.blogs.manage', $post->blogPost_id);
                    }
                } else {
                    // Visitor -> Public Show
                    $cardLink = route('blogs.show', $post->blogPost_id);
                }
            @endphp

            {{-- Full Width Card --}}
            <div class="card event-card border-0 shadow-sm w-100 position-relative">
                
                {{-- Image Header --}}
                <div class="position-relative">
                    {{-- 
                       NOTE: I kept the height at 180px. 
                       If you want the image larger for a full-width card, you can increase this.
                    --}}
                    <img src="{{ $imageUrl }}" alt="{{ $post->title }}" class="w-100"
                        style="height: 180px; object-fit: cover; border-radius: 10px 10px 0 0;">

                    {{-- Category Badge --}}
                    <span class="position-absolute top-0 start-0 m-2 px-3 py-1 small fw-semibold text-white"
                        style="background: rgba(0,0,0,0.65); border-radius: 18px; box-shadow: 0 4px 10px rgba(0,0,0,0.3);">
                        {{ $post->custom_category ?: optional($post->category)->categoryName ?? 'Uncategorized' }}
                    </span>
                </div>

                {{-- Card Body --}}
                <div class="card-body d-flex flex-column">
                    
                    {{-- Title & Draft Badge --}}
                    <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                        <h5 class="card-title mb-0">
                            {{ $post->title }}
                        </h5>
                        @if ($post->status !== 'published' && $isOwner)
                            <span class="badge bg-warning text-dark fw-semibold flex-shrink-0">
                                Draft
                            </span>
                        @endif
                    </div>

                    {{-- Excerpt --}}
                    <p class="text-muted small mb-3">
                        {{ $excerpt }}
                    </p>

                    {{-- Footer: Date (Left) & 3-Dots (Right) --}}
                    <div class="mt-auto d-flex align-items-center justify-content-between">
                        
                        {{-- Date --}}
                        <div class="d-flex align-items-center gap-1 small text-secondary" style="opacity: 0.85;">
                            <i class="fas fa-calendar-alt"></i>
                            <span>{{ $displayDate }}</span>
                        </div>

                        {{-- 3-Dot Menu (Owner Only) --}}
                        @if($isOwner)
                            <div class="dropdown" style="z-index: 2; position: relative;">
                                <button class="btn btn-sm btn-light rounded-circle" type="button" 
                                        data-bs-toggle="dropdown" aria-expanded="false" 
                                        style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-ellipsis-v text-muted"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                    {{-- Edit Option --}}
                                    <li>
                                        <a class="dropdown-item" href="{{ route('volunteer.blogs.edit', $post->blogPost_id) }}">
                                            <i class="fas fa-edit me-2 text-primary"></i> Edit
                                        </a>
                                    </li>
                                    {{-- Delete Option --}}
                                    <li>
                                        <form action="{{ route('volunteer.blogs.destroy', $post->blogPost_id) }}" method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this blog post?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fas fa-trash-alt me-2"></i> Delete
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @endif

                    </div>
                </div>

                {{-- Stretched Link to make the whole card clickable --}}
                <a href="{{ $cardLink }}" class="stretched-link"></a>
            </div>

        @empty
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-file-alt text-muted fa-3x mb-3"></i>
                    <p class="text-muted mb-0">No blog posts yet</p>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if ($blogPosts->hasPages())
        <div class="d-flex justify-content-center mt-4 events-pagination">
            {{ $blogPosts->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>




                        </div>
                    </div>
                </div>

                <!-- Right Column - Points and Rewards -->
                <div class="col-lg-3">
                    <!-- Points Display Card -->
                    <div class="card mb-4" style="border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                        <div class="card-body text-center" style="padding: 2rem 1.25rem;">
                            <div class="points-display"
                                style="font-size: 3rem; font-weight: 700; color: #1976d2; line-height: 1;">
                                {{ $totalPoints }}</div>
                            <p class="text-muted mt-2">Total Points</p>
                        </div>
                    </div>

                    <!-- Rewards Earned Card -->
                    <div class="card" style="border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                        <div class="card-header bg-light border-bottom d-flex align-items-center"
                            style="padding: 1rem 1.25rem; font-weight: 600;">
                            <i class="fas fa-award me-2 text-warning"></i>
                            <span>Rewards Earned</span>
                        </div>
                        <div class="card-body" style="padding: 1.25rem;">
                            @php
                                $earnedCurrent = $userBadges->currentPage();
                                $earnedLast = $userBadges->lastPage();
                            @endphp

                            <div class="earned-wrapper">
                                {{-- earned badges list (1 column, 5 per page) --}}
                                <div id="earnedList" class="earned-list">
                                    @forelse($userBadges as $ub)
                                        @php
                                            $claimedAt = optional($ub->created_at)
                                                ? $ub->created_at->format('d M Y')
                                                : '-';
                                        @endphp
                                        <div class="earned-item d-flex align-items-center mb-3"
                                            data-badge-id="{{ $ub->badge->id }}">
                                            <a href="javascript:void(0)" class="earned-open me-3"
                                                data-badge-id="{{ $ub->badge->id }}">
                                                <img src="{{ $ub->badge->img_url ?? asset('images/badges/default-badge.jpg') }}"
                                                    alt="{{ $ub->badge->badgeName }}" class="earned-badge-img">
                                            </a>
                                            <div>
                                                <div style="font-weight:600; font-size:0.95rem;">
                                                    {{ $ub->badge->badgeName }}</div>
                                                <div class="small text-muted">Claimed {{ $claimedAt }}</div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-4">
                                            <i class="fas fa-award text-muted fa-2x mb-2"></i>
                                            <p class="text-muted mb-0">No rewards earned yet</p>
                                        </div>
                                    @endforelse
                                </div>

                                {{-- bottom pagination --}}
                                @if ($earnedLast > 1)
                                    <nav aria-label="Earned badges pages" class="mt-3 rewards-pagination">
                                        <ul class="pagination pagination-sm mb-0">
                                            {{-- previous --}}
                                            <li class="page-item {{ $earnedCurrent <= 1 ? 'disabled' : '' }}">
                                                <a class="page-link"
                                                    href="{{ $earnedCurrent > 1 ? $userBadges->url($earnedCurrent - 1) : '#' }}"
                                                    aria-label="Previous">
                                                    <span aria-hidden="true">&laquo;</span>
                                                </a>
                                            </li>

                                            {{-- page numbers (max 5, centered) --}}
                                            @php
                                                $start = max(1, $earnedCurrent - 2);
                                                $end = min($earnedLast, $start + 4);
                                                if ($end - $start < 4) {
                                                    $start = max(1, $end - 4);
                                                }
                                            @endphp

                                            @for ($p = $start; $p <= $end; $p++)
                                                <li class="page-item {{ $p === $earnedCurrent ? 'active' : '' }}">
                                                    <a class="page-link"
                                                        href="{{ $userBadges->url($p) }}">{{ $p }}</a>
                                                </li>
                                            @endfor

                                            {{-- next --}}
                                            <li
                                                class="page-item {{ $earnedCurrent >= $earnedLast ? 'disabled' : '' }}">
                                                <a class="page-link"
                                                    href="{{ $earnedCurrent < $earnedLast ? $userBadges->url($earnedCurrent + 1) : '#' }}"
                                                    aria-label="Next">
                                                    <span aria-hidden="true">&raquo;</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </nav>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FOOTER --}}
    @include('layouts.volunteer_footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/show.js') }}"></script>
</body>

</html>
