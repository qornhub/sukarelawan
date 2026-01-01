<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>NGO Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/NGODashboard/layouts.css') }}">
    <link rel="stylesheet" href="{{ asset('css/NGODashboard/eventTrend.css') }}">
    <link rel="stylesheet" href="{{ asset('css/NGODashboard/blogTrend.css') }}">

    <style>
        .pie-chart-container {
            height: 220px;
            min-height: 220px;
        }

        .category-list-container {
            max-height: 220px;
            overflow-y: auto;
            padding-right: 6px;
        }

        /* Optional: cleaner scrollbar */
        .category-list-container::-webkit-scrollbar {
            width: 6px;
        }

        .category-list-container::-webkit-scrollbar-thumb {
            background-color: rgba(0, 0, 0, 0.2);
            border-radius: 4px;
        }
    </style>
</head>

<body>
    @include('layouts.ngo_header')

    <!-- Blur Background Header -->
    <div class="dashboard-header">
        <div class="container">
            <div class="header-content">
                <h1 class="greeting-title">Hello, {{ $user->name ?? ($user->ngo_name ?? 'NGO') }}</h1>
                <p class="greeting-subtitle">Here's an overview of your organisation activity</p>
            </div>
        </div>
    </div>

    <div class="container main-content">
        {{-- Statistics cards --}}
        {{-- Statistics cards --}}
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="stat-card h-100">
                    <div class="stat-icon">
                        <i class="bi bi-calendar-event"></i>
                    </div>
                    <div class="stat-content">
                        <div class="small text-muted">Total event</div>
                        <div class="value">{{ str_pad($totalEvents, 2, '0', STR_PAD_LEFT) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card h-100">
                    <div class="stat-icon">
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="stat-content">
                        <div class="small text-muted">Total Volunteer Joined</div>
                        <div class="value">{{ $totalRegistrations }}</div>
                        <div class="additional-info">Unique volunteers: {{ $uniqueVolunteers }}</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card h-100">
                    <div class="stat-icon">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <div class="small text-muted">Ongoing / Completed</div>
                        <div class="value">{{ $completedEvents }} completed</div>
                        <div class="additional-info">{{ $totalEvents - $completedEvents }} upcoming/ongoing</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="create-card h-100 text-center d-flex flex-column">
                    <div class="create-icon">
                        <i class="bi bi-plus-circle"></i>
                    </div>
                    <h6 class="fw-bold">Create an Impact</h6>
                    <p class="small text-muted">Inspire change through meaningful actions that leave a lasting
                        difference in the lives of others.</p>
                    <a href="{{ route('ngo.events.create') }}" class="btn btn-primary btn-create mt-auto">Create
                        Event</a>
                </div>
            </div>
        </div>

        {{-- Attendance / progress --}}
        <div class="row">
            <div class="col-lg-12">
                <div class="card attendance-card mb-4">
                    <div class="card-header bg-transparent border-0">
                        <h5 class="card-title mb-0">My Top Attendance</h5>
                    </div>
                    <div class="card-body">
                        @forelse($topAttendanceEvents as $event)
                            <div class="attendance-item d-flex align-items-center mb-4 p-3 rounded">
                                <div class="event-image me-3">
                                    <img src="{{ $event['image'] ?: asset('assets/default_event.jpg') }}"
                                        alt="Event Image" class="rounded"
                                        onerror="this.onerror=null;this.src='{{ asset('assets/default_event.jpg') }}';" />
                                </div>
                                <div class="event-details flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="event-title mb-0">{{ $event['title'] }}</h6>
                                        <span
                                            class="attendance-rate badge bg-primary">{{ $event['attendance_rate'] }}%</span>
                                    </div>
                                    <div class="progress mb-2">
                                        <div class="progress-bar" role="progressbar"
                                            style="width: {{ $event['attendance_rate'] }}%"
                                            aria-valuenow="{{ $event['attendance_rate'] }}" aria-valuemin="0"
                                            aria-valuemax="100">
                                        </div>
                                    </div>
                                    <small class="text-muted">
                                        {{ $event['attended'] }} / {{ $event['registered'] }} volunteers
                                    </small>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="bi bi-calendar-x text-muted display-4"></i>
                                <p class="text-muted mt-3 mb-0">No attendance data available yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>


        </div>

        {{-- Event trend + category pie (partial) --}}
        @include('partials.events.event_trend', [
            'eventTrendDailyLabels' => $eventTrendDailyLabels,
            'eventTrendDailyCounts' => $eventTrendDailyCounts,
            'eventTrendMonthsLabels' => $eventTrendMonthsLabels,
            'eventTrendMonthsCounts' => $eventTrendMonthsCounts,
            'eventTrendYearsLabels' => $eventTrendYearsLabels,
            'eventTrendYearsCounts' => $eventTrendYearsCounts,
            'categoryData' => $categoryData,
        ])

        {{-- Blog summary + trend (partial) --}}
        @include('partials.blog.blog_trend', [
            'totalBlogs' => $totalBlogs,
            'blogDailyLabels' => $blogDailyLabels,
            'blogDailyCounts' => $blogDailyCounts,
            'blogMonthlyLabels' => $blogMonthlyLabels,
            'blogMonthlyCounts' => $blogMonthlyCounts,
            'blogYearlyLabels' => $blogYearlyLabels,
            'blogYearlyCounts' => $blogYearlyCounts,
        ])

    </div>

    @include('layouts.messages')
    @include('layouts.ngo_footer')

    <!-- scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@2.0.1/dist/chartjs-plugin-zoom.min.js"></script>

    @stack('scripts')
</body>

</html>
