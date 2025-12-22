@foreach ($events as $event)
    {{-- We already filtered to future events in the controller --}}
    <div class="event-card">
         <div class="image-container">
            <img src="{{ asset('images/events/' . ($event->eventImage ?? 'default_event.jpg')) }}"
                alt="{{ $event->eventTitle }}" class="event-image">

            <span class="category-tag">
                @if (!empty($event->custom_category))
                    {{ $event->custom_category }} 
                @elseif ($event->category)
                    {{ $event->category->eventCategoryName }}
                @else
                    Uncategorized
                @endif
            </span>
        </div>

        <div class="event-details">
            <div class="event-meta">
                <div class="event-date">
                    <i class="far fa-calendar-alt"></i>
                    <span>{{ \Carbon\Carbon::parse($event->eventStart)->format('l, j F Y g:i A') }}</span>
                </div>
                <div class="event-points">{{ $event->eventPoints ?? 0 }} Points</div>
            </div>

            <h3 class="event-title">{{ $event->eventTitle }}</h3>

            <div class="event-location">
                <i class="fas fa-map-marker-alt"></i>
                <span>{{ $event->venueName ?? ($event->city ?? ($event->eventLocation ?? 'N/A')) }}</span>
            </div>

            <p class="event-description">{{ $event->eventSummary }}</p>

            <a href="{{ route('ngo.events.show', $event->event_id) }}" class="join-btn">
                <i class="fas fa-info-circle"></i> Details
            </a>

            <div class="event-footer">

                @php
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
                        elseif (\Illuminate\Support\Facades\Storage::disk('public')->exists($file)) {
                            $profileImageUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($file);
                        } elseif (\Illuminate\Support\Facades\Storage::disk('public')->exists("profiles/{$basename}")) {
                            $profileImageUrl = \Illuminate\Support\Facades\Storage::disk('public')->url(
                                "profiles/{$basename}",
                            );
                        }
                    }
                @endphp

                <div class="event-organizer">
                    <div class="org-avatar" aria-hidden="true">
                        <img src="{{ $profileImageUrl }}" alt="{{ $organizer->name ?? 'Organizer' }}">
                    </div>
                    <div class="org-meta">
                        <div class="org-by">By</div>
                        <div class="org-name">{{ $organizer->name ?? 'Organizer' }}</div>
                    </div>
                </div>



                @php
                    $approved = $event->registrations->where('status', 'approved')->count();
                    $max = $event->eventMaximum ?? 0;
                    $percent = $max > 0 ? round(($approved / $max) * 100) : 0;
                @endphp

                {{-- Add data-* attributes so the JS can initialize and update bars for appended HTML --}}
                <div class="event-attendance" data-event-id="{{ $event->event_id }}"
                    data-approved="{{ $approved }}" data-max="{{ $max }}">
                    <i class="fas fa-users"></i>
                    <span class="attendance-text">{{ $approved }}/{{ $max ?: '∞' }}</span>
                    <div class="attendance-progress">
                        <div class="attendance-bar" style="width: {{ $percent }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach
