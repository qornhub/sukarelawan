{{-- resources/views/admin/adminDashboard/partials/volunteer-chart.blade.php --}}
<!-- MAIN: Volunteers bar chart (only volunteers series) - CONSISTENT STRUCTURE -->
<div class="card p-3 mb-5 chart-card">
  <div class="d-flex justify-content-between align-items-center mb-2">
    <div>
      <h6 class="mb-0">Volunteer Registrations chart</h6>
      <small class="text-muted">Select period to view trend.</small>
    </div>

    <div>
      <div class="btn-group" role="group" aria-label="period toggles">
        <button type="button" class="btn btn-sm btn-outline-primary chart-period-btn" data-period="daily">Daily</button>
        <button type="button" class="btn btn-sm btn-outline-primary chart-period-btn" data-period="weekly">Weekly</button>
        <button type="button" class="btn btn-sm btn-outline-primary chart-period-btn active" data-period="monthly">Monthly</button>
      </div>
    </div>
  </div>

  <div><small class="text-muted">Volunteer registrations (Volunteers only)</small></div>
  <div class="chart-canvas-wrapper">
    <canvas id="regChart"></canvas>
  </div>
</div>


<!-- SECOND: Volunteer registrations trend -->
<div class="card p-3 mb-5 chart-card">
  <div class="d-flex justify-content-between align-items-center mb-2">
    <div>
      <h6 class="mb-0">Volunteer Registrations Over Time</h6>
      <small class="text-muted">Select period to view trend.</small>
    </div>
    <div>
      <!-- change in second chart's button group -->
<div class="btn-group" role="group" aria-label="trend toggles">
  <button type="button" class="btn btn-sm btn-outline-primary chart-period-btn trend-period" data-period="daily">Daily</button>
  <button type="button" class="btn btn-sm btn-outline-primary chart-period-btn trend-period" data-period="weekly">Weekly</button>
  <button type="button" class="btn btn-sm btn-outline-primary chart-period-btn trend-period active" data-period="monthly">Monthly</button>
</div>

    </div>
  </div>

  <div><small class="text-muted">Volunteer registrations (trend)</small></div>
  <div class="chart-canvas-wrapper">
    <canvas id="volTrendChart"></canvas>
  </div>
</div>

<div class="card p-3 mb-5 chart-card">
  <div class="d-flex justify-content-between align-items-center mb-2">
    <div>
      <h6 class="mb-0">Volunteer Activity (30 days)</h6>
      <small class="text-muted">Volunteers who attended at least one event in the last 30 days.</small>
    </div>
  </div>

  <div><small class="text-muted">Active vs Inactive volunteers</small></div>
  <div class="chart-canvas-wrapper">
    <canvas id="volActiveChart"></canvas>
  </div>
</div>



@push('scripts')
<script>
/**
 * VOL_CHARTS module — only responsible for rendering volunteer charts.
 * Exposes: init(), refresh(regPeriod='monthly', trendPeriod='monthly'), destroy()
 * NOTE: This does not bind card clicks or period toggles globally.
 */
if (!window.VOL_CHARTS) {
  window.VOL_CHARTS = (function () {
    // Scoped DOM nodes inside the volunteer wrapper
    const root = document.getElementById('vol-chart-wrapper');
    if (!root) return { init: () => {}, refresh: () => {}, destroy: () => {} };

    const regCanvas = root.querySelector('#regChart');
    const trendCanvas = root.querySelector('#volTrendChart');
    const activeCanvas = root.querySelector('#volActiveChart');

    // Chart instances
    let regChart = null, trendChart = null, activeChart = null;

    function destroyIf(chart) { if (chart) { chart.destroy(); } }

    // Renderers (these assume Chart.js is loaded globally)
    function renderReg(labels = [], data = []) {
      if (!regCanvas) return;
      destroyIf(regChart);
      const ctx = regCanvas.getContext('2d');
      regChart = new Chart(ctx, {
        type: 'bar',
        data: { labels, datasets: [{ label: 'Volunteers', data, borderWidth: 1 }] },
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
      });
    }

    function renderTrend(labels = [], data = []) {
      if (!trendCanvas) return;
      destroyIf(trendChart);
      const ctx = trendCanvas.getContext('2d');
      trendChart = new Chart(ctx, {
        type: 'line',
        data: { labels, datasets: [{ label: 'New volunteers', data, borderColor: 'rgba(54,162,235,1)', backgroundColor: 'rgba(54,162,235,0.12)', fill: true, tension: 0.25, pointRadius: 3 }] },
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
      });
    }

    function renderActive(labels = [], data = []) {
      if (!activeCanvas) return;
      destroyIf(activeChart);
      const ctx = activeCanvas.getContext('2d');
      activeChart = new Chart(ctx, {
        type: 'doughnut',
        data: { labels, datasets: [{ data, backgroundColor: ['#28a745', '#e9ecef'], borderColor: ['#28a745', '#e9ecef'], borderWidth: 1 }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } }
      });
    }

    // Fetch helpers (the module itself fetches data when refresh() is called)
    async function fetchJson(url, params = {}) {
      const qs = new URLSearchParams(params).toString();
      const full = qs ? `${url}?${qs}` : url;
      const res = await fetch(full, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' });
      return res.json();
    }

    // Public functions
    async function init() {
      if (typeof Chart === 'undefined') console.warn('Chart.js not found — include it once globally (index.blade).');
      // nothing else to auto-run here
    }

    /**
     * refresh: load dataset and render charts.
     * regPeriod: 'daily'|'weekly'|'monthly'  (used for main reg chart)
     * trendPeriod: 'daily'|'weekly'|'monthly'
     */
    async function refresh(regPeriod = 'monthly', trendPeriod = 'monthly') {
      try {
        // chartData provides volunteers & ngos datasets
        const chartData = await fetchJson("{{ route('admin.dashboard.chartData') }}");
        if (chartData && chartData.success) {
          const labels = chartData.labels[regPeriod] || [];
          const data = (chartData.datasets && chartData.datasets.volunteers && chartData.datasets.volunteers[regPeriod]) ? chartData.datasets.volunteers[regPeriod] : [];
          renderReg(labels, data);
        } else {
          renderReg([], []);
        }

        // trend: volunteerTrend endpoint
        const t = await fetchJson("{{ route('admin.dashboard.volunteerTrend') }}", { period: trendPeriod });
        if (t && t.success) renderTrend(t.labels || [], t.counts || []);
        else renderTrend([], []);

        // active
        const a = await fetchJson("{{ route('admin.dashboard.volActive') }}");
        if (a && a.success) renderActive(a.labels || [], a.counts || []);
        else renderActive([], []);
      } catch (err) {
        console.error('VOL_CHARTS.refresh error', err);
      }
    }

    function destroy() {
      destroyIf(regChart); destroyIf(trendChart); destroyIf(activeChart);
      regChart = trendChart = activeChart = null;
    }

    return { init, refresh, destroy };
  })();
}
</script>
@endpush

