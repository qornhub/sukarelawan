{{-- resources/views/admin/adminDashboard/partials/ngo-chart.blade.php --}}

<div id="ngo-chart-wrapper">
  <!-- HEADER + MAIN: NGOs bar chart (only NGOs series) - CONSISTENT STRUCTURE -->
  <div class="card p-3 mb-5 chart-card">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <div>
        <h6 class="mb-0">NGO Registrations chart</h6>
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

    <div><small class="text-muted">NGO registrations (NGOs only)</small></div>
    <div class="chart-canvas-wrapper">
      <canvas id="regChartNgo"></canvas>
    </div>
  </div>

  <!-- SECOND: NGO registrations trend (line) -->
  <div class="card p-3 mb-5 chart-card">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <div>
        <h6 class="mb-0">NGO Registrations Over Time</h6>
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

    <div><small class="text-muted">NGO registrations (trend)</small></div>
    <div class="chart-canvas-wrapper">
      <canvas id="ngoTrendChart"></canvas>
    </div>
  </div>

  <!-- THIRD: Active vs Inactive NGOs -->
  <div class="card p-3 mb-5 chart-card">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <div>
        <h6 class="mb-0">NGO Activity (30 days)</h6>
        <small class="text-muted">NGOs who created at least one event in the last 30 days.</small>
      </div>
    </div>

    <div><small class="text-muted">Active vs Inactive NGOs</small></div>
    <div class="chart-canvas-wrapper">
      <canvas id="ngoActiveChart"></canvas>
    </div>
  </div>
</div>


@push('scripts')
<script>
/* NGO chart module (idempotent) */
if (!window.NGO_CHARTS) {
  window.NGO_CHARTS = (function () {
    // chart instances
    let regChart = null;
    let trendChart = null;
    let activeChart = null;

    // helper to fetch json
    async function fetchJson(url, params = {}) {
      const qs = new URLSearchParams(params).toString();
      const full = qs ? `${url}?${qs}` : url;
      const res = await fetch(full, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      return res.json();
    }

    function destroyIf(chart) { if (chart) { chart.destroy(); } }

    // render helpers (operate on canvases within this partial)
    function renderReg(labels = [], data = []) {
      const canvas = document.getElementById('regChartNgo');
      if (!canvas) return;
      const ctx = canvas.getContext('2d');
      destroyIf(regChart);
      regChart = new Chart(ctx, {
        type: 'bar',
        data: { labels, datasets: [{ label: 'NGOs', data, borderWidth: 1 }] },
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
      });
    }

    function renderTrend(labels = [], data = []) {
      const canvas = document.getElementById('ngoTrendChart');
      if (!canvas) return;
      const ctx = canvas.getContext('2d');
      destroyIf(trendChart);
      trendChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels,
          datasets: [{
            label: 'New NGOs',
            data,
            borderColor: 'rgba(54,162,235,1)',
            backgroundColor: 'rgba(54,162,235,0.12)',
            fill: true, tension: 0.25, pointRadius: 3
          }]
        },
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
      });
    }

    function renderActive(labels = [], counts = []) {
      const canvas = document.getElementById('ngoActiveChart');
      if (!canvas) return;
      const ctx = canvas.getContext('2d');
      destroyIf(activeChart);
      activeChart = new Chart(ctx, {
        type: 'doughnut',
        data: { labels, datasets: [{ data: counts, backgroundColor: ['#28a745', '#e9ecef'], borderColor: ['#28a745', '#e9ecef'], borderWidth: 1 }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } }
      });
    }

    // idempotent init: here we don't auto-fetch — main script will call refresh()
    async function init() {
      if (typeof Chart === 'undefined') {
        console.warn('Chart.js not loaded. Make sure Chart.js is included once globally.');
        return;
      }
      // nothing to do immediately; this function exists if you want to pre-warm things later
    }

    // refresh: fetch and render current NGO datasets.
    // regPeriod: 'daily'|'weekly'|'monthly' ; trendPeriod same
    async function refresh(regPeriod = 'monthly', trendPeriod = 'monthly') {
      try {
        // main registrations data (chartData returns both volunteers & ngos)
        const chartData = await fetchJson("{{ route('admin.dashboard.chartData') }}");
        if (chartData && chartData.success) {
          const labels = chartData.labels[regPeriod] || [];
          const data = (chartData.datasets && chartData.datasets.ngos && chartData.datasets.ngos[regPeriod]) ? chartData.datasets.ngos[regPeriod] : [];
          renderReg(labels, data);
        } else {
          renderReg([], []);
        }

        // trend: call explicit NGO trend endpoint
        const t = await fetchJson("{{ route('admin.dashboard.ngoTrend') }}", { period: trendPeriod });
        if (t && t.success) {
          renderTrend(t.labels || [], t.counts || []);
        }

        // active: call ngo active endpoint
        const a = await fetchJson("{{ route('admin.dashboard.ngoActive') }}");
        if (a && a.success) {
          renderActive(a.labels || [], a.counts || []);
        }
      } catch (err) {
        console.error('NGO_CHARTS refresh error', err);
      }
    }

    function destroy() {
      destroyIf(regChart);
      destroyIf(trendChart);
      destroyIf(activeChart);
      regChart = trendChart = activeChart = null;
    }

    return { init, refresh, destroy };
  })();
}
</script>
@endpush
