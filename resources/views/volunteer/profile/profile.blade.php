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
                    <span class="profile-badge">{{ ucfirst(auth()->user()->role->roleName) }}</span>
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

                    
                </div>

                <!-- Middle Column - Tabs -->
                <div class="card col-lg-6 ">

                    <ul class="nav nav-tabs">
                        <li class="nav-item me-2">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#upcoming">
                                <i class="fas fa-calendar-check me-2"></i>Upcoming Events
                            </button>
                        </li>
                        <li class="nav-item me-2">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#past">
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
                        {{-- Upcoming Events --}}
                        <div class="tab-pane fade show active" id="upcoming">
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

                                    {{-- Event Image --}}
                                    <img src="{{ $eventImage }}" class="card-img-top" alt="{{ $event->eventTitle }}"
                                        style="height:200px; object-fit:cover;">

                                    {{-- Event Info --}}
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <h5 class="card-title mb-1">{{ $event->eventTitle }}</h5>

                                            {{-- Points (if applicable) --}}
                                            @if (!empty($event->eventPoints))
                                                <span class="badge bg-light text-primary p-2 mb-1">
                                                    <i class="fas fa-coins me-1"></i> {{ $event->eventPoints }} points
                                                </span>
                                            @endif


                                        </div>

                                        <p class="text-muted small mb-2 mt-2">
                                            {{ $event->eventSummary ?? 'No description available' }}</p>

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
                        </div>

                        <!-- Past Events -->
                        <div class="tab-pane fade" id="past">
                            @forelse($pastEvents as $event)
                                <div class="card event-card">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $event->title }}</h5>
                                        <p class="text-muted">Attended on
                                            {{ \Carbon\Carbon::parse($event->attendance_date)->format('j M Y') }}
                                        </p>
                                        <div class="d-flex flex-wrap gap-3 mt-3">
                                            <div>
                                                <i class="fas fa-clock text-muted me-1"></i>
                                                <span>{{ $event->start_time }} – {{ $event->end_time }}</span>
                                            </div>
                                            <div>
                                                <i class="fas fa-map-marker-alt text-muted me-1"></i>
                                                <span>{{ $event->location }}</span>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <span class="badge bg-light text-success p-2">
                                                <i class="fas fa-coins me-1"></i> {{ $event->points }} points
                                                earned
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="card">
                                    <div class="card-body text-center py-4">
                                        <i class="fas fa-history text-muted fa-2x mb-3"></i>
                                        <p class="text-muted mb-0">No past events</p>
                                    </div>
                                </div>
                            @endforelse
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
                            <div class="progress mb-3">
                                <div class="progress-bar bg-success" role="progressbar"
                                    style="width: {{ min(($totalPoints / 50) * 100, 100) }}%"></div>
                            </div>
                            <p>{{ max(50 - $totalPoints, 0) }} points to next level</p>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <i class="fas fa-award me-2 text-warning"></i>
                            <span class="fw-bold">Rewards Earned</span>
                        </div>

                        <div class="card-body">
                            @forelse($userBadges as $userBadge)
                                <div class="reward-item mb-3 d-flex align-items-center">
                                    <div>
                                        <h6 class="mb-0">{{ $userBadge->badge->badgeName }}</h6>
                                        @if ($userBadge->badge->badgeDescription)
                                            <p class="text-muted mb-0 small">{{ $userBadge->badge->badgeDescription }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4">
                                    <i class="fas fa-award text-muted fa-2x mb-2"></i>
                                    <p class="text-muted mb-0">No rewards earned yet</p>
                                </div>
                            @endforelse

                            @if ($userBadges->count() > 3)
                                <div class="text-center mt-3">
                                    <a href="{{ route('volunteer.rewards.index') }}"
                                        class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-ellipsis-h me-1"></i> View All Rewards
                                    </a>
                                </div>
                            @endif
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
            </div>
        </div>

    </div>

    {{-- FOOTER --}}
    @include('layouts.volunteer_footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/show.js') }}"></script>
</body>

</html>
