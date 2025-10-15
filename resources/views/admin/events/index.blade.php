{{-- resources/views/admin/events/index.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Volunteer Events Discovery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/events/index.css') }}">
</head>

<body>

    {{-- admin nav (replaces volunteer_header) --}}
    @include('layouts.admin_nav')
    @include('layouts.messages')

    {{-- WRAPPER: push everything right by 70px so admin_nav can expand --}}
    <div style="margin-left: 70px;">

        <div class="content-wrapper">
            <div class="events-container">

                <!-- optional top-right New Event button (keeps classes/layout unchanged) -->
                <div class="events-header mt-5">
                    <h2 class="section-title">
                        <i class="fa-solid fa-seedling me-2"></i>Admin Discover Events
                    </h2>
                </div>

                <!-- Search and Filters Section -->
                <!-- Search & Filters form -->
                <form method="GET" action="{{ route('admin.events.index') }}" class="events-container">
                    <div class="search-section">
                        <div class="search-bar">
                            <i class="fas fa-search"></i>
                            <input type="text" name="search" class="search-input"
                                placeholder="Search Events (eg. Sabah, Kuala Lumpur)"
                                value="{{ old('search', $search ?? request('search')) }}">
                        </div>

                        <div class="filters-container">
                            <div class="filter-group">
                                <label class="filter-label">Categories</label>
                                <select name="category" class="filter-select" onchange="this.form.submit()">
                                    <option value="">All Categories</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->eventCategory_id }}"
                                            {{ isset($categoryId) && $categoryId == $category->eventCategory_id ? 'selected' : (request('category') == $category->eventCategory_id ? 'selected' : '') }}>
                                            {{ $category->eventCategoryName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="filter-group">
                                <label class="filter-label">Location</label>
                                <select name="location" class="filter-select" onchange="this.form.submit()">
                                    <option value="">All Locations</option>
                                    @foreach ($locations as $loc)
                                        <option value="{{ $loc }}"
                                            {{ isset($location) && $location === $loc ? 'selected' : (request('location') == $loc ? 'selected' : '') }}>
                                            {{ $loc }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="filter-group">
                                <label class="filter-label">Date Range</label>
                                <select name="date_range" class="filter-select" onchange="this.form.submit()">
                                    <option value="">Any Date</option>
                                    <option value="this_week"
                                        {{ request('date_range') == 'this_week' ? 'selected' : '' }}>This Week</option>
                                    <option value="next_week"
                                        {{ request('date_range') == 'next_week' ? 'selected' : '' }}>Next Week</option>
                                    <option value="this_month"
                                        {{ request('date_range') == 'this_month' ? 'selected' : '' }}>This Month
                                    </option>
                                </select>
                            </div>

                            <div class="filter-group" style="align-self:flex-end;">
                                <button type="submit" class="btn btn-primary">Apply</button>
                                <a href="{{ route('admin.events.index') }}" class="btn btn-link">Reset</a>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Events Grid (outside form to avoid nested forms; but still will read query string) -->
                <div class="events-grid">
                    @foreach ($events as $event)
                        @if (\Carbon\Carbon::parse($event->eventStart)->gte(\Carbon\Carbon::today()))
                            <div class="event-card">
                                <div class="image-container">
                                    <img src="{{ asset('images/events/' . ($event->eventImage ?? 'default-event.jpg')) }}"
                                        alt="{{ $event->eventTitle }}" class="event-image">
                                    <span
                                        class="category-tag">{{ $event->category->eventCategoryName ?? 'Uncategorized' }}</span>
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

                                    {{-- DETAILS button → goes to admin event show --}}
                                    <a href="{{ route('admin.events.show', $event->event_id) }}" class="join-btn">
                                        <i class="fas fa-info-circle"></i> Details
                                    </a>

                                    <div class="event-footer">
                                        <div class="event-organizer">
                                            <i class="fas fa-user-circle"></i> By
                                            {{ $event->organizer->name ?? 'Organizer' }}
                                        </div>
                                        <div class="event-attendance">
                                            @php
                                                // ✅ Only approved registrations
                                                $approved = $event->registrations->where('status', 'approved')->count();
                                                $max = $event->eventMaximum ?? 0;
                                                $percent = $max > 0 ? round(($approved / $max) * 100) : 0;
                                            @endphp

                                            <i class="fas fa-users"></i>
                                            <span>{{ $approved }}/{{ $max ?: '∞' }}</span>

                                            <div class="attendance-progress">
                                                <div class="attendance-bar" style="width: {{ $percent }}%"></div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $events->withQueryString()->links() }}
                </div>

                <!-- View More Button -->
                <div class="view-more">
                    <button class="view-more-btn">View More Events</button>
                </div>
            </div>
        </div>

    </div> {{-- end admin wrapper margin-left:70px --}}


    <script src="{{ asset('js/events/index.js') }}"></script>
</body>

</html>
