<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Profile | SukaRelawan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/volunteer_profile.css') }}">

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
    <!-- Cover Photo -->
    <div class="content-wrapper">
        <div class="cover-container position-relative">
            <div class="cover-photo"
                style="background: url('{{ $profile->coverPhoto ? asset('images/covers/' . $profile->coverPhoto) : asset('images/default-cover.jpg') }}') center/cover;">
            </div>

            <img src="{{ $profile->profilePhoto
                ? asset('images/profiles/' . $profile->profilePhoto)
                : asset('images/default-profile.png') }}"
                class="profile-avatar rounded-circle" alt="Profile Photo">


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

        <div class="container pb-5 px-0">



            <div class="row">
                <!-- Left Column - About -->
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-user-circle me-2"></i>About
                        </div>
                        <div class="card-body">
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

                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-chart-line me-2"></i>Volunteer Stats
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <div class="stat-card">
                                        <div class="stat-number">{{ count($upcomingEvents) + count($pastEvents) }}
                                        </div>
                                        <div class="stat-label">Events</div>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="stat-card">
                                        <div class="stat-number">{{ $totalPoints * 2 }}</div>
                                        <div class="stat-label">Hours</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-card">
                                        <div class="stat-number">{{ count($blogPosts) }}</div>
                                        <div class="stat-label">Blogs</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-card">
                                        <div class="stat-number">{{ $userBadges->count() }}</div>

                                        <div class="stat-label">Badges</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>

                <!-- Middle Column - Tabs -->
                <div class="card col-lg-6 ">

                    <ul class="nav nav-tabs">
                        <li class="nav-item me-2">
                            <button class="nav-link {{ request('past_page') ? '' : 'active' }}" data-bs-toggle="tab"
                                data-bs-target="#upcoming">
                                <i class="fas fa-calendar-check me-2"></i>Upcoming Events
                            </button>
                        </li>
                        <li class="nav-item me-2">
                            <button class="nav-link {{ request('past_page') ? 'active' : '' }}" data-bs-toggle="tab"
                                data-bs-target="#past">
                                <i class="fas fa-history me-2"></i>Past Events
                            </button>
                        </li>
                        <li class="nav-item me-5">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#blog">
                                <i class="fas fa-blog me-2"></i>My Blog
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content mt-0">
                        <!-- Upcoming Events -->
                        <div class="tab-pane fade {{ request('past_page') ? '' : 'show active' }}" id="upcoming">
                            @forelse($upcomingEvents as $event)
                                @php
                                    $eventImage = $event->eventImage
                                        ? asset('images/events/' . $event->eventImage)
                                        : asset('images/events/default-event.jpg');
                                    $start = $event->eventStart ? \Carbon\Carbon::parse($event->eventStart) : null;
                                    $end = $event->eventEnd ? \Carbon\Carbon::parse($event->eventEnd) : null;
                                @endphp

                                <a href="{{ route('volunteer.profile.registrationEditDelete', $event->event_id) }}"
                                    class="card event-card mb-3 event-card-link"
                                    style="text-decoration: none; color: inherit;">
                                    <img src="{{ $eventImage }}" class="card-img-top" alt="{{ $event->eventTitle }}"
                                        style="height:200px; object-fit:cover;">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <h5 class="card-title mb-1">{{ $event->eventTitle }}</h5>
                                            @if (!empty($event->eventPoints))
                                                <span class="badge bg-light text-primary p-2 mb-1"><i
                                                        class="fas fa-coins me-1"></i> {{ $event->eventPoints }}
                                                    points</span>
                                            @endif
                                        </div>

                                        <p class="text-muted small mb-2 mt-2">
                                            {{ $event->eventSummary ?? 'No description available' }}</p>

                                        <div class="d-flex flex-wrap gap-3 mt-3">
                                            <div><i class="fas fa-clock text-primary me-1"></i>
                                                {{ $start ? $start->format('j M Y g:i A') : '-' }} –
                                                {{ $end ? $end->format('j M Y g:i A') : '—' }}</div>
                                            <div><i class="fas fa-map-marker-alt text-primary me-1"></i>
                                                {{ $event->venueName ?? '-' }}</div>
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


                            {{-- Pagination for Upcoming --}}
                            @if ($upcomingEvents->hasPages())
                                <div class="d-flex justify-content-center mt-3 events-pagination">
                                    {{ $upcomingEvents->links('pagination::bootstrap-5') }}
                                </div>
                            @endif
                        </div>

                        <!-- Past Events -->
                        <div class="tab-pane fade {{ request('past_page') ? 'show active' : '' }}" id="past">
                            @forelse($pastEvents as $event)
                                @php
                                    $eventImage = $event->eventImage
                                        ? asset('images/events/' . $event->eventImage)
                                        : asset('images/events/default-event.jpg');
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
                                        alt="{{ $event->eventTitle }}" style="height:200px; object-fit:cover;">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <h5 class="card-title mb-1">{{ $event->eventTitle }}</h5>
                                            @if (!empty($pointsEarned))
                                                <span class="badge bg-light text-success p-2 mb-1"><i
                                                        class="fas fa-coins me-1"></i> {{ $pointsEarned }} pts</span>
                                            @endif
                                        </div>

                                        <p class="text-muted small mb-2 mt-2">
                                            {{ $event->eventSummary ?? 'No description available' }}</p>

                                        <div class="d-flex flex-wrap gap-3 mt-3">
                                            <div><i class="fas fa-clock text-primary me-1"></i>
                                                {{ $start ? $start->format('j M Y g:i A') : '-' }} –
                                                {{ $end ? $end->format('j M Y g:i A') : '—' }}</div>
                                            <div><i class="fas fa-map-marker-alt text-primary me-1"></i>
                                                {{ $event->venueName ?? '-' }}</div>

                                            @if ($attendedAt)
                                                <div><i class="fas fa-check-circle text-success me-1"></i> Attended on
                                                    {{ $attendedAt->format('j M Y g:i A') }}</div>
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
                            @if ($pastEvents->hasPages())
                                <div class="d-flex justify-content-center mt-3 events-pagination">
                                    {{ $pastEvents->links('pagination::bootstrap-5') }}
                                </div>
                            @endif


                        </div>



                        <!-- Blog Posts -->
                        <div class="tab-pane fade" id="blog">
                            @forelse($blogPosts as $post)
                                <div class="card event-card">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $post->title }}</h5>
                                        <p class="text-muted">Published:
                                            {{ \Carbon\Carbon::parse($post->created_at)->format('j M Y') }}</p>
                                        <p class="card-text mt-3">{{ Str::limit($post->excerpt, 120) }}</p>
                                        <a href="{{ route('blog.show', $post) }}"
                                            class="btn btn-sm btn-outline-primary mt-2">
                                            Read more <i class="fas fa-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <div class="card">
                                    <div class="card-body text-center py-4">
                                        <i class="fas fa-file-alt text-muted fa-2x mb-3"></i>
                                        <p class="text-muted mb-0">No blog posts yet</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>

                <!-- Right Column - Points and Rewards -->
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="points-display">{{ $totalPoints }}</div>
                            <p class="text-muted">Total Points</p>


                        </div>
                    </div>



                    <div class="card mb-3">
                        <div class="card-header d-flex align-items-center">
                            <i class="fas fa-award me-2 text-warning"></i>
                            <span class="fw-bold">Rewards Earned</span>
                        </div>

                        <div class="card-body">
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
