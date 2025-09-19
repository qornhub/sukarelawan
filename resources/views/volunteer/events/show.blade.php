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
</head>

<body>
    @include('layouts.volunteer_header')


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
    @include('layouts.messages')

    {{-- Register button (below hero, above main content) --}}
    <div class="container">
        <div class="register-row text-end" style="margin-top: 18px;">
            <a href="{{ route('volunteer.event.register', $event) }}" class="register-btn">
                <i class="fas fa-user-plus me-2"></i> REGISTER NOW
            </a>

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
                        <a class="social-btn whatsapp" href="https://wa.me/?text={{ urlencode(request()->fullUrl()) }}"
                            target="_blank">
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
                        @php
                            use Illuminate\Support\Str;

                            $ngo = optional($event->organizer);
                            $filename = $ngo->profilePhoto ?? null;
                            $profileImageUrl = asset('images/default-profile.png');

                            if ($filename) {
                                $basename = basename($filename);

                                $paths = [
                                    public_path("images/profiles/{$filename}"),
                                    public_path("images/profiles/{$basename}"),
                                    public_path("images/{$filename}"),
                                    public_path("storage/{$filename}"),
                                    public_path("storage/profiles/{$basename}"),
                                ];

                                $assetTemplates = [
                                    asset("images/profiles/{$filename}"),
                                    asset("images/profiles/{$basename}"),
                                    asset("images/{$filename}"),
                                    asset("storage/{$filename}"),
                                    asset("storage/profiles/{$basename}"),
                                ];

                                foreach ($paths as $i => $p) {
                                    if (file_exists($p)) {
                                        $profileImageUrl = $assetTemplates[$i];
                                        break;
                                    }
                                }

                                if ($profileImageUrl === asset('images/default-profile.png')) {
                                    if (
                                        Str::startsWith($filename, 'profiles/') ||
                                        Str::startsWith($filename, 'covers/') ||
                                        Str::contains($filename, 'storage')
                                    ) {
                                        $profileImageUrl = asset('storage/' . ltrim($filename, '/'));
                                    } else {
                                        $profileImageUrl = asset('images/profiles/' . $basename);
                                    }
                                }
                            }
                        @endphp

                        <div class="org-avatar">
                            <img src="{{ $profileImageUrl }}" alt="Organizer Image" class="rounded-circle"
                                style="width: 60px; height: 60px; object-fit: cover;">
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

    @include('layouts.volunteer_footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
