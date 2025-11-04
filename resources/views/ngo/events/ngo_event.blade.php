{{-- resources/views/ngo/Ngo_Event.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NGO Events Discovery</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/events/index.css') }}">

    <style>
        .events-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 0 0 18px;
            gap: 12px;
        }

        .add-mission-btn {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            border-radius: 999px;
            padding: 10px 18px;
            font-weight: 600;
            box-shadow: var(--shadow, 0 4px 20px rgba(0, 0, 0, .08));
            white-space: nowrap;
        }

        @media (max-width: 576px) {
            .add-mission-btn {
                width: 100%;
                justify-content: center;
            }
        }

        .content-wrapper { max-width: 100%; }

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
            .attendance-progress { width: 120px; max-width: 45vw; }
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
    @include('layouts.ngo_header')
    @include('layouts.messages')

    <div class="content-wrapper">
        <div class="events-container">

            {{-- Header --}}
            <div class="events-header">
                <h2 class="section-title">
                    <i class="fa-solid fa-seedling me-2"></i>Discover Events
                </h2>

                <a href="{{ route('ngo.events.create') }}" class="btn btn-primary add-mission-btn">
                    <i class="fa-solid fa-plus"></i> Add Mission
                </a>
            </div>

            {{-- Search & Filters --}}
            <form method="GET" action="{{ route('ngo.events.index') }}" class="events-container">
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
                                        {{ request('category') == $category->eventCategory_id ? 'selected' : '' }}>
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
                                    <option value="{{ $loc }}" {{ request('location') == $loc ? 'selected' : '' }}>
                                        {{ $loc }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="filter-group">
                            <label class="filter-label">Date Range</label>
                            <select name="date_range" class="filter-select" onchange="this.form.submit()">
                                <option value="">Any Date</option>
                                <option value="this_week" {{ request('date_range') == 'this_week' ? 'selected' : '' }}>This Week</option>
                                <option value="next_week" {{ request('date_range') == 'next_week' ? 'selected' : '' }}>Next Week</option>
                                <option value="this_month" {{ request('date_range') == 'this_month' ? 'selected' : '' }}>This Month</option>
                            </select>
                        </div>

                        <div class="filter-group" style="align-self:flex-end;">
                            <button type="submit" class="btn btn-primary">Apply</button>
                            <a href="{{ route('ngo.events.index') }}" class="btn btn-link">Reset</a>
                        </div>
                    </div>
                </div>
            </form>

            <div id="event-list" class="events-grid">
    @include('partials.events.event_cards', ['events' => $events])
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

    @include('layouts.ngo_footer')

    
     <script src="{{ asset('js/events/index.js') }}"></script>
</body>
</html>
