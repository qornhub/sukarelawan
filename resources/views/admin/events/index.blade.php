{{-- resources/views/admin/events/index.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Events Discovery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/events/index.css') }}">
    <style>
        /* --- Event footer: attendance progress --- */
        .event-footer {
            display: flex;
            align-items: center;
            gap: 12px;
            justify-content: flex-start;
            width: 100%;
        }

        .event-footer .event-organizer {
            flex: 0 0 auto;
            white-space: nowrap;
        }

        .event-attendance {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-left: auto;
            flex: 0 0 auto;
        }

        .event-attendance .attendance-text {
            white-space: nowrap;
            font-size: 0.95rem;
        }

        .attendance-progress {
            flex: 0 0 auto;
            width: 100px;
            max-width: 40vw;
            min-width: 80px;
            height: 8px;
            margin-left: 8px;
            background: #e9e9e9;
            border-radius: 999px;
            overflow: hidden;
        }

        .attendance-bar {
            height: 100%;
            transition: width 350ms ease;
            background: var(--primary-color, #004aad);
            border-radius: 999px;
        }

        @media (max-width: 600px) {
            .attendance-progress {
                width: 120px;
                max-width: 45vw;
            }
        }

        /* View More button */
        .view-more-btn {
            background: var(--primary-color, #004aad);
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: 0.2s ease;
        }

        .view-more-btn:hover:not(:disabled) {
            background: var(--primary-hover, #003780);
        }

        .view-more-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
    </style>
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
                <div id="event-list" class="events-grid">
                    @include('partials.events.event_cards_admin', ['events' => $events])
                </div>

                {{-- Hide default pagination (kept for SEO or fallback) --}}
                <div class="d-none">{{ $events->links() }}</div>

                {{-- View more button keeps the nextPageUrl from paginator --}}
                <div class="view-more text-center my-4">
                    @if ($events->hasMorePages())
                        <button class="view-more-btn" data-next-page="{{ $events->nextPageUrl() }}">
                            View More Events
                        </button>
                    @else
                        <button class="view-more-btn" disabled>No More Events</button>
                    @endif
                </div>
            </div>
        </div>

    </div> {{-- end admin wrapper margin-left:70px --}}


    <script src="{{ asset('js/events/index.js') }}"></script>
</body>

</html>
