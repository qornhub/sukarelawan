{{-- resources/views/admin/adminDashboard/partials/event-chart.blade.php --}}

<div class="d-flex justify-content-between align-items-center mb-2">
    <div>
        <h6 class="mb-0">Events Overview</h6>
        <small class="text-muted">Choose period for the main chart. Other charts update automatically.</small>
    </div>

    <div>
        <div class="btn-group" role="group" aria-label="period toggles">
            <button type="button" class="btn btn-sm btn-outline-primary chart-period-btn" data-period="daily">Daily</button>
            <button type="button" class="btn btn-sm btn-outline-primary chart-period-btn" data-period="weekly">Weekly</button>
            <button type="button" class="btn btn-sm btn-outline-primary chart-period-btn active" data-period="monthly">Monthly</button>
        </div>
    </div>
</div>

{{-- 1) Event Creation Trend (main chart) --}}
<div class="card p-3 mb-5 chart-card">
    <div><small class="text-muted">Events created (trend)</small></div>
    <div class="chart-canvas-wrapper">
        <canvas id="eventsCreationChart"></canvas>
    </div>
</div>

{{-- 2) Category Distribution --}}
<div class="card p-3 mb-5 chart-card">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h6 class="mb-0">Event Category Distribution</h6>
            <small class="text-muted">Which event types are most common.</small>
        </div>
    </div>
    <div><small class="text-muted">Categories</small></div>
    <div class="chart-canvas-wrapper">
        <canvas id="eventsCategoryChart"></canvas>
    </div>
</div>

{{-- 3) Registration Status Summary --}}
<div class="card p-3 mb-5 chart-card">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h6 class="mb-0">Registration Status</h6>
            <small class="text-muted">Approved / Pending / Rejected</small>
        </div>
    </div>
    <div><small class="text-muted">Registrations</small></div>
    <div class="chart-canvas-wrapper">
        <canvas id="eventsRegStatusChart"></canvas>
    </div>
</div>

{{-- 4) Attendance Rate (Top 10 horizontal bar) --}}
<div class="card p-3 mb-5 chart-card">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h6 class="mb-0">Top Events by Attendance Rate</h6>
            <small class="text-muted">% of registered volunteers who attended (top 10)</small>
        </div>
    </div>
    <div><small class="text-muted">Attendance rate (top events)</small></div>
    <div class="chart-canvas-wrapper">
        <canvas id="eventsAttendanceChart"></canvas>
    </div>
</div>

{{-- 5) Active vs Completed --}}
<div class="card p-3 mb-5 chart-card">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h6 class="mb-0">Active vs Completed Events</h6>
            <small class="text-muted">Ongoing/upcoming vs completed</small>
        </div>
    </div>
    <div><small class="text-muted">Active vs Completed</small></div>
    <div class="chart-canvas-wrapper">
        <canvas id="eventsActiveChart"></canvas>
    </div>
</div>

@push('scripts')
<script>
/**
 * EVENTS_CHARTS module — responsible for rendering event-related charts.
 * Exposes init(), refresh(regPeriod='monthly', trendPeriod='monthly'), destroy()
 *
 * This module is idempotent and safe to call multiple times.
 * It expects Chart.js to be loaded globally once (index.blade).
 */
if (!window.EVENTS_CHARTS) {
  window.EVENTS_CHARTS = (function () {
    // Scoped DOM
    const root = document.getElementById('vol-chart-wrapper') // note: module only included in event wrapper; root used for safety
      ? document.getElementById('vol-chart-wrapper') /* not used, but keep consistent */ 
      : document.body;

    // Canvas elements (global search OK because partial exists inside wrapper)
    const creationCanvas = document.getElementById('eventsCreationChart');
    const categoryCanvas = document.getElementById('eventsCategoryChart');
    const regStatusCanvas = document.getElementById('eventsRegStatusChart');
    const attendanceCanvas = document.getElementById('eventsAttendanceChart');
    const activeCanvas = document.getElementById('eventsActiveChart');

    // Chart instances
    let creationChart = null;
    let categoryChart = null;
    let regStatusChart = null;
    let attendanceChart = null;
    let activeChart = null;

    // Helper fetch
    async function fetchJson(url, params = {}) {
      const qs = new URLSearchParams(params).toString();
      const full = qs ? `${url}?${qs}` : url;
      const res = await fetch(full, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' });
      return res.json();
    }

    function destroyIf(chart) { if (chart) chart.destroy(); }

    // RENDERERS

    // 1) Creation Trend (line)
    function renderCreation(labels = [], counts = []) {
      if (!creationCanvas) return;
      destroyIf(creationChart);
      const ctx = creationCanvas.getContext('2d');
      creationChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels,
          datasets: [{
            label: 'Events created',
            data: counts,
            borderColor: 'rgba(0,74,173,1)',
            backgroundColor: 'rgba(0,74,173,0.08)',
            fill: true,
            tension: 0.25,
            pointRadius: 3
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: { y: { beginAtZero: true } },
          plugins: { legend: { display: false } }
        }
      });
    }

    // 2) Category distribution (doughnut)
    function renderCategory(labels = [], counts = []) {
      if (!categoryCanvas) return;
      destroyIf(categoryChart);
      const ctx = categoryCanvas.getContext('2d');
      categoryChart = new Chart(ctx, {
        type: 'doughnut',
        data: { labels, datasets: [{ data: counts, backgroundColor: generateColorPalette(counts.length), borderWidth: 1 }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } }
      });
    }

    // 3) Registration status (doughnut/pie)
    function renderRegStatus(labels = [], counts = []) {
      if (!regStatusCanvas) return;
      destroyIf(regStatusChart);
      const ctx = regStatusCanvas.getContext('2d');
      regStatusChart = new Chart(ctx, {
        type: 'doughnut',
        data: { labels, datasets: [{ data: counts, backgroundColor: generateColorPalette(counts.length), borderWidth: 1 }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } }
      });
    }

    // 4) Attendance rate (horizontal bar)
    function renderAttendance(labels = [], rates = []) {
      if (!attendanceCanvas) return;
      destroyIf(attendanceChart);
      const ctx = attendanceCanvas.getContext('2d');
      attendanceChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
            label: 'Attendance rate (%)',
            data: rates,
            borderWidth: 1,
            // Chart.js horizontal bar pattern: use indexAxis
          }]
        },
        options: {
          indexAxis: 'y',
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            x: { beginAtZero: true, max: 100 }
          },
          plugins: { legend: { display: false } }
        }
      });
    }

    // 5) Active vs Completed (doughnut)
    function renderActive(labels = [], counts = []) {
      if (!activeCanvas) return;
      destroyIf(activeChart);
      const ctx = activeCanvas.getContext('2d');
      activeChart = new Chart(ctx, {
        type: 'doughnut',
        data: { labels, datasets: [{ data: counts, backgroundColor: ['#28a745', '#e9ecef'], borderWidth: 1 }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } }
      });
    }

    // small color palette helper (simple predictable palette)
    function generateColorPalette(n) {
      const base = [
        'rgba(54,162,235,0.85)',
        'rgba(255,99,132,0.85)',
        'rgba(255,205,86,0.85)',
        'rgba(75,192,192,0.85)',
        'rgba(153,102,255,0.85)',
        'rgba(201,203,207,0.85)',
        'rgba(0,74,173,0.85)',
        'rgba(0,123,255,0.85)',
        'rgba(40,167,69,0.85)',
        'rgba(220,53,69,0.85)'
      ];
      const out = [];
      for (let i = 0; i < n; i++) out.push(base[i % base.length]);
      return out;
    }

    // PUBLIC: init() - no auto-fetch; just sanity check
    function init() {
      if (typeof Chart === 'undefined') {
        console.warn('Chart.js not loaded. Ensure a single Chart.js include exists in index.blade.php');
      }
    }

    /**
     * refresh: fetch data from endpoints and render charts
     * regPeriod: controls the creationTrend endpoint ('daily','weekly','monthly')
     * trendPeriod argument is accepted for API parity though not used by events currently.
     */
    async function refresh(regPeriod = 'monthly', trendPeriod = 'monthly') {
      try {
        // 1) Creation trend
        const creationUrl = "{{ route('admin.dashboard.events.creationTrend') }}";
        const creationResp = await fetchJson(creationUrl, { period: regPeriod });
        if (creationResp && creationResp.success) {
          renderCreation(creationResp.labels || [], creationResp.counts || []);
        } else {
          renderCreation([], []);
        }

        // 2) Category distribution
        const catUrl = "{{ route('admin.dashboard.events.categoryDistribution') }}";
        const catResp = await fetchJson(catUrl);
        if (catResp && catResp.success) {
          renderCategory(catResp.labels || [], catResp.counts || []);
        } else {
          renderCategory([], []);
        }

        // 3) registration status summary
        const regUrl = "{{ route('admin.dashboard.events.registrationStatus') }}";
        const regResp = await fetchJson(regUrl);
        if (regResp && regResp.success) {
          renderRegStatus(regResp.labels || [], regResp.counts || []);
        } else {
          renderRegStatus([], []);
        }

        // 4) attendance rate top events
        const attUrl = "{{ route('admin.dashboard.events.attendanceRate') }}";
        const attResp = await fetchJson(attUrl, { limit: 10 });
        if (attResp && attResp.success) {
          renderAttendance(attResp.labels || [], attResp.attendance_rate || []);
        } else {
          renderAttendance([], []);
        }

        // 5) active vs completed
        const activeUrl = "{{ route('admin.dashboard.events.activeSummary') }}";
        const activeResp = await fetchJson(activeUrl);
        if (activeResp && activeResp.success) {
          renderActive(activeResp.labels || [], activeResp.counts || []);
        } else {
          renderActive([], []);
        }

      } catch (err) {
        console.error('EVENTS_CHARTS.refresh error', err);
      }
    }

    function destroy() {
      destroyIf(creationChart);
      destroyIf(categoryChart);
      destroyIf(regStatusChart);
      destroyIf(attendanceChart);
      destroyIf(activeChart);
      creationChart = categoryChart = regStatusChart = attendanceChart = activeChart = null;
    }

    return { init, refresh, destroy };
  })();
}
</script>
@endpush
