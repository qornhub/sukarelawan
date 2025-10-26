{{-- resources/views/admin/adminDashboard/index.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Sukarelawan</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome (icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Dashboard CSS (ensure this file contains the trend-icon styles below) -->
    <link rel="stylesheet" href="{{ asset('css/adminDashboard/index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/adminDashboard/volunteer-chart.css') }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>

    {{-- admin nav (replaces volunteer_header) --}}
    @include('layouts.admin_nav')
    @include('layouts.messages')

    {{-- WRAPPER: push everything right by 70px so admin_nav can expand --}}
    <div style="margin-left: 100px; margin-right:20px;">

        <div class="container-fluid py-3">

            <!-- Search bar -->
            <div class="mb-3">
                <div class="input-group" style="max-width:720px">
                    <span class="input-group-text bg-white"><i class="fa fa-search"></i></span>
                    <input id="admin-search" type="search" class="form-control" placeholder="Search here..."
                        aria-label="Search" />
                </div>
            </div>

            <!-- Title -->
            <h3 class="mb-3">Dashboard</h3>

            <!-- Cards -->
            <div class="row g-3 mb-4">
                <!-- Total Volunteers (clickable) -->
                <div class="col-md-3">
                    <div class="card clickable-card shadow-sm" data-metric="registrations"
                        data-target="registrations_chart">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted">Total Volunteers</small>
                                    <h4 id="total-volunteers">{{ number_format($totalVolunteers ?? 0) }}</h4>
                                </div>
                                <div class="text-end">
                                    <i class="fa-solid fa-users" style="font-size:28px;"></i>
                                </div>
                            </div>

                            @php $v = $volChange; @endphp
                            <div
                                class="mt-2 small @if ($v['direction'] === 'up') text-success @elseif($v['direction'] === 'down') text-danger @else text-muted @endif">
                                @if ($v['percentage'] === null)
                                    <span class="trend-icon text-success" aria-hidden="true">▲</span>
                                    <span class="sr-only">New</span>
                                    New this month
                                @elseif($v['direction'] === 'up')
                                    <span class="trend-icon text-success" aria-hidden="true">▲</span>
                                    {{ $v['percentage'] }}% Up from past month
                                @elseif($v['direction'] === 'down')
                                    <span class="trend-icon text-danger" aria-hidden="true">▼</span>
                                    {{ $v['percentage'] }}% Down from past month
                                @else
                                    0% change from past month
                                @endif
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Total NGOs -->
                <div class="col-md-3">
                    <div class="card clickable-card shadow-sm" data-metric="registrations"
                        data-target="registrations_chart" data-show="ngos">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted">Total NGOs</small>
                                    <h4 id="total-ngos">{{ number_format($totalNgos ?? 0) }}</h4>
                                </div>
                                <div class="text-end">
                                    <i class="fa-solid fa-city" style="font-size:28px;"></i>
                                </div>
                            </div>

                            @php $n = $ngoChange; @endphp
                            <div
                                class="mt-2 small @if ($n['direction'] === 'up') text-success @elseif($n['direction'] === 'down') text-danger @else text-muted @endif">
                                @if ($n['percentage'] === null)
                                    <span class="trend-icon text-success" aria-hidden="true">▲</span>
                                    <span class="sr-only">New</span>
                                    New this month
                                @elseif($n['direction'] === 'up')
                                    <span class="trend-icon text-success" aria-hidden="true">▲</span>
                                    {{ $n['percentage'] }}% Up from past month
                                @elseif($n['direction'] === 'down')
                                    <span class="trend-icon text-danger" aria-hidden="true">▼</span>
                                    {{ $n['percentage'] }}% Down from past month
                                @else
                                    0% change from past month
                                @endif
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Total Events -->
                <div class="col-md-3">
                    <div class="card clickable-card shadow-sm" data-metric="events" data-target="events_chart" data-show="events">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted">Total Events</small>
                                    <h4 id="total-events">{{ number_format($totalEvents ?? 0) }}</h4>
                                </div>
                                <div class="text-end">
                                    <i class="fa-solid fa-calendar-days" style="font-size:28px;"></i>
                                </div>
                            </div>

                            @php $e = $eventsChange; @endphp
                            <div
                                class="mt-2 small @if ($e['direction'] === 'up') text-success @elseif($e['direction'] === 'down') text-danger @else text-muted @endif">
                                @if ($e['percentage'] === null)
                                    <span class="trend-icon text-success" aria-hidden="true">▲</span>
                                    <span class="sr-only">New</span>
                                    New this month
                                @elseif($e['direction'] === 'up')
                                    <span class="trend-icon text-success" aria-hidden="true">▲</span>
                                    {{ $e['percentage'] }}% Up from past month
                                @elseif($e['direction'] === 'down')
                                    <span class="trend-icon text-danger" aria-hidden="true">▼</span>
                                    {{ $e['percentage'] }}% Down from past month
                                @else
                                    0% change from past month
                                @endif
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Total Blog Posts -->
                <div class="col-md-3">
                    <div class="card clickable-card shadow-sm" data-metric="blogs" data-target="blogs_chart" data-show="blogs">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted">Total BlogPosts</small>
                                    <h4 id="total-blogs">{{ number_format($totalBlogs ?? 0) }}</h4>
                                </div>
                                <div class="text-end">
                                    <i class="fa-solid fa-newspaper" style="font-size:28px;"></i>
                                </div>
                            </div>

                            @php $b = $blogsChange; @endphp
                            <div
                                class="mt-2 small @if ($b['direction'] === 'up') text-success @elseif($b['direction'] === 'down') text-danger @else text-muted @endif">
                                @if ($b['percentage'] === null)
                                    <span class="trend-icon text-success" aria-hidden="true">▲</span>
                                    <span class="sr-only">New</span>
                                    New this month
                                @elseif($b['direction'] === 'up')
                                    <span class="trend-icon text-success" aria-hidden="true">▲</span>
                                    {{ $b['percentage'] }}% Up from past month
                                @elseif($b['direction'] === 'down')
                                    <span class="trend-icon text-danger" aria-hidden="true">▼</span>
                                    {{ $b['percentage'] }}% Down from past month
                                @else
                                    0% change from past month
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div id="dashboard-chart-area" class="card shadow-sm p-3" style="min-height:360px;">
                {{-- Volunteer chart (visible by default) --}}
                <div id="vol-chart-wrapper">
                    @include('admin.adminDashboard.partials.volunteer-chart')
                </div>

                {{-- NGO chart (hidden initially) --}}
                <div id="ngo-chart-wrapper" style="display:none;">
                    @include('admin.adminDashboard.partials.ngo-chart')
                </div>

                {{-- NGO chart (hidden initially) --}}
                <div id="events-chart-wrapper" style="display:none;">
                    @include('admin.adminDashboard.partials.event-chart')
                </div>

                {{-- NGO chart (hidden initially) --}}
                <div id="blogs-chart-wrapper" style="display:none;">
                    @include('admin.adminDashboard.partials.blog-chart')
                </div>
            </div>


        </div> {{-- container-fluid --}}
    </div> {{-- end admin wrapper margin-left:70px --}}

   <!-- Chart.js CDN (for charts) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  // wrappers
  const volWrapper = document.getElementById('vol-chart-wrapper');
  const ngoWrapper = document.getElementById('ngo-chart-wrapper');
  // placeholders for future blades (create these wrappers when you add their partials)
  let eventsWrapper = document.getElementById('events-chart-wrapper');
  let blogsWrapper = document.getElementById('blogs-chart-wrapper');

  // card nodes
  const cards = Array.from(document.querySelectorAll('.clickable-card'));

  // period state (shared across views)
  let regPeriod = 'monthly';
  let trendPeriod = 'monthly';

  // helper to hide all wrappers
  function hideAll() {
    if (volWrapper) volWrapper.style.display = 'none';
    if (ngoWrapper) ngoWrapper.style.display = 'none';
    if (eventsWrapper) eventsWrapper.style.display = 'none';
    if (blogsWrapper) blogsWrapper.style.display = 'none';
  }

  // helper to set active card visual
  function setActiveCard(activeCard) {
    cards.forEach(c => c.classList.toggle('active-card', c === activeCard));
  }

  // call this to switch view to volunteers
  async function showVolunteers() {
    hideAll();
    if (volWrapper) volWrapper.style.display = '';
    // destroy NGO charts to free memory (optional)
    if (window.NGO_CHARTS && typeof window.NGO_CHARTS.destroy === 'function') window.NGO_CHARTS.destroy();
    // init / refresh volunteer charts
    if (window.VOL_CHARTS && typeof window.VOL_CHARTS.refresh === 'function') {
      await window.VOL_CHARTS.refresh(regPeriod, trendPeriod);
    } else if (window.VOL_CHARTS && typeof window.VOL_CHARTS.init === 'function') {
      window.VOL_CHARTS.init();
    }
  }

  // call this to switch view to NGOs
  async function showNgos() {
    hideAll();
    if (ngoWrapper) ngoWrapper.style.display = '';
    // destroy VOL charts
    if (window.VOL_CHARTS && typeof window.VOL_CHARTS.destroy === 'function') window.VOL_CHARTS.destroy();
    // ensure NGO module exists
    if (!window.NGO_CHARTS) {
      console.warn('NGO_CHARTS not found — make sure your ngo-chart partial defines window.NGO_CHARTS similar to VOL_CHARTS.');
      return;
    }
    // refresh NGO charts
    if (typeof window.NGO_CHARTS.refresh === 'function') {
      await window.NGO_CHARTS.refresh(regPeriod, trendPeriod);
    } else if (typeof window.NGO_CHARTS.init === 'function') {
      window.NGO_CHARTS.init();
    }
  }

  // placeholders for events/blogs (when you add partials implement modules similar to VOL_CHARTS)
  async function showEvents() {
    hideAll();
    if (!eventsWrapper) {
      console.warn('events wrapper missing — add #events-chart-wrapper and an EVENTS_CHARTS module later.');
      return;
    }
    eventsWrapper.style.display = '';
    if (window.EVENTS_CHARTS && typeof window.EVENTS_CHARTS.refresh === 'function') {
      await window.EVENTS_CHARTS.refresh(regPeriod, trendPeriod);
    }
  }

  async function showBlogs() {
    hideAll();
    if (!blogsWrapper) {
      console.warn('blogs wrapper missing — add #blogs-chart-wrapper and a BLOGS_CHARTS module later.');
      return;
    }
    blogsWrapper.style.display = '';
    if (window.BLOGS_CHARTS && typeof window.BLOGS_CHARTS.refresh === 'function') {
      await window.BLOGS_CHARTS.refresh(regPeriod, trendPeriod);
    }
  }

  // wire card clicks (decide view by data-show attribute)
  cards.forEach(card => {
    // prevent duplicate binding
    if (card._adminCardBound) return;
    card._adminCardBound = true;

    card.addEventListener('click', function () {
      setActiveCard(this);

      const show = (this.dataset.show || '').toLowerCase();
      // update regPeriod/trendPeriod if you store on card later (not required now)
      // switch by show attribute
      if (show === 'ngos') {
        showNgos();
      } else if (show === 'events') {
        showEvents();
      } else if (show === 'blogs') {
        showBlogs();
      } else {
        // default is volunteers
        showVolunteers();
      }
    });
  });

  // Hook shared period toggles (top-level buttons inside whichever wrapper is visible).
  // When a period button is clicked we update state and tell currently visible module to refresh.
  // Use event delegation to catch clicks from either wrapper's buttons.
  document.addEventListener('click', async function (e) {
    const btn = e.target.closest('.chart-period-btn');
    if (!btn) return;
    const p = btn.dataset.period;
    if (!p) return;
    regPeriod = p;

    // refresh visible module
    // decide which wrapper is visible
    if (volWrapper && volWrapper.style.display !== 'none') {
      if (window.VOL_CHARTS && typeof window.VOL_CHARTS.refresh === 'function') await window.VOL_CHARTS.refresh(regPeriod, trendPeriod);
    } else if (ngoWrapper && ngoWrapper.style.display !== 'none') {
      if (window.NGO_CHARTS && typeof window.NGO_CHARTS.refresh === 'function') await window.NGO_CHARTS.refresh(regPeriod, trendPeriod);
    } else if (eventsWrapper && eventsWrapper.style.display !== 'none') {
      if (window.EVENTS_CHARTS && typeof window.EVENTS_CHARTS.refresh === 'function') await window.EVENTS_CHARTS.refresh(regPeriod, trendPeriod);
    } else if (blogsWrapper && blogsWrapper.style.display !== 'none') {
      if (window.BLOGS_CHARTS && typeof window.BLOGS_CHARTS.refresh === 'function') await window.BLOGS_CHARTS.refresh(regPeriod, trendPeriod);
    }
  });

  // trend period toggles (delegated)
  document.addEventListener('click', async function (e) {
    const btn = e.target.closest('.trend-period');
    if (!btn) return;
    const p = btn.dataset.period;
    if (!p) return;
    trendPeriod = p;

    if (volWrapper && volWrapper.style.display !== 'none') {
      if (window.VOL_CHARTS && typeof window.VOL_CHARTS.refresh === 'function') await window.VOL_CHARTS.refresh(regPeriod, trendPeriod);
    } else if (ngoWrapper && ngoWrapper.style.display !== 'none') {
      if (window.NGO_CHARTS && typeof window.NGO_CHARTS.refresh === 'function') await window.NGO_CHARTS.refresh(regPeriod, trendPeriod);
    } else if (eventsWrapper && eventsWrapper.style.display !== 'none') {
      if (window.EVENTS_CHARTS && typeof window.EVENTS_CHARTS.refresh === 'function') await window.EVENTS_CHARTS.refresh(regPeriod, trendPeriod);
    } else if (blogsWrapper && blogsWrapper.style.display !== 'none') {
      if (window.BLOGS_CHARTS && typeof window.BLOGS_CHARTS.refresh === 'function') await window.BLOGS_CHARTS.refresh(regPeriod, trendPeriod);
    }
  });

  // initial state: show volunteers on first load
  (async function () {
    // ensure wrappers exist references (in case events/blogs wrappers are created later)
    eventsWrapper = document.getElementById('events-chart-wrapper');
    blogsWrapper = document.getElementById('blogs-chart-wrapper');

    // initial visible state
    hideAll();
    if (volWrapper) volWrapper.style.display = '';

    // ask VOL_CHARTS to render (if present)
    if (window.VOL_CHARTS && typeof window.VOL_CHARTS.refresh === 'function') {
      await window.VOL_CHARTS.refresh(regPeriod, trendPeriod);
    } else if (window.VOL_CHARTS && typeof window.VOL_CHARTS.init === 'function') {
      window.VOL_CHARTS.init();
    }
  })();

});
</script>

    @stack('scripts')

</body>

</html>
