{{-- resources/views/admin/adminDashboard/partials/blog-chart.blade.php --}}

<div class="d-flex justify-content-between align-items-center mb-2">
    <div>
        <h6 class="mb-0">Blog Posts Overview</h6>
        <small class="text-muted">Use the period toggle to change the main trend chart.</small>
    </div>

    <div>
        <div class="btn-group" role="group" aria-label="period toggles">
            <button type="button" class="btn btn-sm btn-outline-primary chart-period-btn" data-period="daily">Daily</button>
            <button type="button" class="btn btn-sm btn-outline-primary chart-period-btn" data-period="weekly">Weekly</button>
            <button type="button" class="btn btn-sm btn-outline-primary chart-period-btn active" data-period="monthly">Monthly</button>
        </div>
    </div>
</div>

{{-- 1) Posts trend (line / bar) --}}
<div class="card p-3 mb-5 chart-card">
    <div><small class="text-muted">Total blog posts (trend)</small></div>
    <div class="chart-canvas-wrapper" style="position:relative;">
        <canvas id="blogsTrendChart"></canvas>
    </div>
</div>

{{-- 2) Top authors --}}
<div class="card p-3 mb-5 chart-card">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h6 class="mb-0">Top Authors</h6>
            <small class="text-muted">Most blog posts by author</small>
        </div>
    </div>
    <div><small class="text-muted">Top contributors</small></div>
    <div class="chart-canvas-wrapper" style="position:relative;">
        <canvas id="blogsTopAuthorsChart"></canvas>
    </div>
</div>

{{-- 3) Category distribution --}}
<div class="card p-3 mb-5 chart-card">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h6 class="mb-0">Blog Categories</h6>
            <small class="text-muted">Distribution by category</small>
        </div>
    </div>
    <div><small class="text-muted">Categories</small></div>
    <div class="chart-canvas-wrapper" style="position:relative;">
        <canvas id="blogsCategoryChart"></canvas>
    </div>
</div>

{{-- 4) Comments per post (top N) --}}
<div class="card p-3 mb-5 chart-card">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h6 class="mb-0">Most Commented Posts</h6>
            <small class="text-muted">Top posts by comment count</small>
        </div>
    </div>
    <div><small class="text-muted">Comments per post</small></div>
    <div class="chart-canvas-wrapper" style="position:relative;">
        <canvas id="blogsCommentsChart"></canvas>
    </div>
</div>

{{-- 5) Status summary --}}
<div class="card p-3 mb-5 chart-card">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h6 class="mb-0">Post Status</h6>
            <small class="text-muted">Draft / Published / Archived</small>
        </div>
    </div>
    <div><small class="text-muted">Status breakdown</small></div>
    <div class="chart-canvas-wrapper" style="position:relative;">
        <canvas id="blogsStatusChart"></canvas>
    </div>
</div>

{{-- 6) Average comments KPI (small box) --}}
<div class="card p-3 mb-5 chart-card">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h6 class="mb-0">Engagement</h6>
            <small class="text-muted">Average comments per post</small>
        </div>
    </div>
    <div class="d-flex align-items-center" style="min-height:90px;">
        <div id="blogsAvgBox" style="font-size:28px; font-weight:700; padding-left:12px;">—</div>
        <div style="padding-left:18px;color:var(--muted)">(<small id="blogsTotals">0 comments / 0 posts</small>)</div>
    </div>
</div>

@push('scripts')
<script>
/**
 * BLOGS_CHARTS module — loads blog-related endpoints and renders charts.
 * Public: init(), refresh(period), destroy()
 */
if (!window.BLOGS_CHARTS) {
  window.BLOGS_CHARTS = (function () {
    // Canvas DOM nodes
    const trendCanvas = document.getElementById('blogsTrendChart');
    const authorsCanvas = document.getElementById('blogsTopAuthorsChart');
    const categoryCanvas = document.getElementById('blogsCategoryChart');
    const commentsCanvas = document.getElementById('blogsCommentsChart');
    const statusCanvas = document.getElementById('blogsStatusChart');
    const avgBox = document.getElementById('blogsAvgBox');
    const totalsLabel = document.getElementById('blogsTotals');

    // Chart instances
    let trendChart = null, authorsChart = null, categoryChart = null, commentsChart = null, statusChart = null;

    // helper to fetch JSON (robust)
    async function fetchJson(url, params = {}) {
      const qs = new URLSearchParams(params).toString();
      const full = qs ? `${url}?${qs}` : url;
      const res = await fetch(full, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' });
      if (!res.ok) {
        console.warn('fetchJson non-OK', res.status, full);
        return { success: false, status: res.status };
      }
      try {
        return await res.json();
      } catch (err) {
        console.error('fetchJson parse error for', full, err);
        return { success: false };
      }
    }

    function destroyIf(chart) { if (chart) chart.destroy(); }

    // no-data overlay helpers
    function showNoData(canvas, msg = 'No data') {
      if (!canvas) return;
      const parent = canvas.parentElement;
      parent.style.position = 'relative';
      let o = parent.querySelector('.no-data-overlay');
      if (!o) {
        o = document.createElement('div');
        o.className = 'no-data-overlay';
        o.style.cssText = 'position:absolute;inset:0;display:flex;align-items:center;justify-content:center;color:#6c757d;font-size:14px;pointer-events:none;';
        parent.appendChild(o);
      }
      o.textContent = msg;
      o.style.display = 'flex';
    }
    function hideNoData(canvas) {
      if (!canvas) return;
      const parent = canvas.parentElement;
      const o = parent ? parent.querySelector('.no-data-overlay') : null;
      if (o) o.style.display = 'none';
    }

    // simple color palette
    function palette(n) {
      const base = [
        'rgba(54,162,235,0.85)','rgba(255,99,132,0.85)','rgba(255,205,86,0.85)',
        'rgba(75,192,192,0.85)','rgba(153,102,255,0.85)','rgba(201,203,207,0.85)'
      ];
      return Array.from({length:n}, (_,i)=>base[i % base.length]);
    }

    // RENDERS
    function renderTrend(labels=[], counts=[]) {
      if (!trendCanvas) return;
      destroyIf(trendChart);
      const ctx = trendCanvas.getContext('2d');
      if (!labels.length || !counts.length) {
        showNoData(trendCanvas, 'No posts yet');
        return;
      }
      hideNoData(trendCanvas);
      trendChart = new Chart(ctx, {
        type: 'line',
        data: { labels, datasets:[{ label: 'Posts', data: counts, borderColor:'rgba(0,123,255,1)', backgroundColor:'rgba(0,123,255,0.08)', fill:true, tension:0.2 }] },
        options: { responsive:true, maintainAspectRatio:false, scales:{ y:{ beginAtZero:true } }, plugins:{ legend:{ display:false } } }
      });
    }

    /**
     * renderAuthors now accepts userIds and roles arrays:
     * labels[] - author names (or "Name (role)" if controller returns that)
     * counts[] - numeric counts
     * userIds[] - parallel array of user ids
     * roles[] - parallel array of role names
     */
    function renderAuthors(labels=[], counts=[], userIds = [], roles = []) {
      if (!authorsCanvas) return;
      destroyIf(authorsChart);
      const ctx = authorsCanvas.getContext('2d');

      if (!labels.length) { showNoData(authorsCanvas,'No authors'); return; }
      hideNoData(authorsCanvas);

      try {
        authorsChart = new Chart(ctx, {
          type: 'bar',
          data: {
            labels: labels,
            datasets: [{
              label: 'Posts',
              data: counts.map(v => Number(v) || 0),
              backgroundColor: palette(counts.length),
              borderWidth: 1
            }]
          },
          options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            scales: { x: { beginAtZero: true } },
            plugins: {
              legend: { display: false },
              tooltip: {
                callbacks: {
                  // shows count and role in tooltip body
                  label: function(context) {
                    const idx = context.dataIndex;
                    const val = context.dataset.data[idx];
                    const role = (roles && roles[idx]) ? roles[idx] : 'Unknown';
                    return `${context.dataset.label}: ${val} — ${role}`;
                  },
                  // show author's name as tooltip title
                  title: function(context) {
                    return context && context.length ? context[0].label : '';
                  }
                }
              }
            },
            onClick: function(evt, elements) {
              if (!elements || !elements.length) return;
              const el = elements[0];
              const idx = el.index;
              const userId = (userIds && userIds[idx]) ? userIds[idx] : null;
              // Replace with navigation/modal if needed — for now log for debug
              console.log('Author clicked:', { index: idx, userId, name: labels[idx], role: roles[idx] });
              // Example navigation (uncomment to use):
              // if (userId) window.location.href = `/admin/users/${userId}/blog-posts`;
            }
          }
        });
      } catch (err) {
        console.error('renderAuthors error', err);
        showNoData(authorsCanvas, 'Could not render authors');
      }
    }

    function renderCategories(labels=[], counts=[]) {
      if (!categoryCanvas) return;
      destroyIf(categoryChart);
      const ctx = categoryCanvas.getContext('2d');
      if (!labels.length) { showNoData(categoryCanvas,'No categories'); return; }
      hideNoData(categoryCanvas);
      categoryChart = new Chart(ctx, {
        type: 'doughnut',
        data: { labels, datasets:[{ data:counts, backgroundColor: palette(counts.length) }] },
        options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ position:'right' } } }
      });
    }

    function renderComments(labels=[], counts=[]) {
      if (!commentsCanvas) return;
      destroyIf(commentsChart);
      const ctx = commentsCanvas.getContext('2d');
      if (!labels.length) { showNoData(commentsCanvas,'No comments'); return; }
      hideNoData(commentsCanvas);
      commentsChart = new Chart(ctx, {
        type: 'bar',
        data: { labels, datasets:[{ label:'Comments', data:counts.map(v=>Number(v)||0), backgroundColor: palette(counts.length) }] },
        options: { responsive:true, maintainAspectRatio:false, scales:{ y:{ beginAtZero:true } }, plugins:{ legend:{ display:false } } }
      });
    }

    function renderStatus(labels=[], counts=[]) {
      if (!statusCanvas) return;
      destroyIf(statusChart);
      const ctx = statusCanvas.getContext('2d');
      if (!labels.length) { showNoData(statusCanvas,'No status data'); return; }
      hideNoData(statusCanvas);
      statusChart = new Chart(ctx, {
        type: 'doughnut',
        data: { labels, datasets:[{ data:counts, backgroundColor: palette(counts.length) }] },
        options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ position:'right' } } }
      });
    }

    // PUBLIC

    function init() {
      if (typeof Chart === 'undefined') console.warn('Chart.js missing - include it once in index.blade.php');
    }

    /**
     * refresh(period)
     * period controls postsTrend endpoint (daily|weekly|monthly)
     */
    async function refresh(period = 'monthly') {
      try {
        // 1) posts trend
        const trendUrl = "{{ route('admin.dashboard.blogs.postsTrend') }}";
        const t = await fetchJson(trendUrl, { period });
        renderTrend(t && t.success ? (t.labels||[]) : [], t && t.success ? (t.counts||[]) : []);

        // 2) top authors (now expects user_ids and roles in response)
        const authorsUrl = "{{ route('admin.dashboard.blogs.topAuthors') }}";
        const a = await fetchJson(authorsUrl, { limit: 10 });
        const authorLabels = (a && a.success) ? (a.labels || []) : [];
        const authorCounts = (a && a.success) ? (a.counts || []) : [];
        const authorUserIds = (a && a.success) ? (a.user_ids || []) : [];
        const authorRoles = (a && a.success) ? (a.roles || []) : [];
        renderAuthors(authorLabels, authorCounts, authorUserIds, authorRoles);

        // 3) categories
        const catUrl = "{{ route('admin.dashboard.blogs.categoryDistribution') }}";
        const c = await fetchJson(catUrl);
        renderCategories(c && c.success ? (c.labels||[]) : [], c && c.success ? (c.counts||[]) : []);

        // 4) comments per post
        const commUrl = "{{ route('admin.dashboard.blogs.commentsPerPost') }}";
        const cm = await fetchJson(commUrl, { limit: 10 });
        renderComments(cm && cm.success ? (cm.labels||[]) : [], cm && cm.success ? (cm.counts||[]) : []);

        // 5) status summary
        const stUrl = "{{ route('admin.dashboard.blogs.statusSummary') }}";
        const s = await fetchJson(stUrl);
        renderStatus(s && s.success ? (s.labels||[]) : [], s && s.success ? (s.counts||[]) : []);

        // 6) avg comments KPI
        const avgUrl = "{{ route('admin.dashboard.blogs.avgComments') }}";
        const avg = await fetchJson(avgUrl);
        if (avg && avg.success) {
          avgBox.textContent = avg.average_comments_per_post;
          totalsLabel.textContent = `${avg.total_comments} comments / ${avg.total_posts} posts`;
        } else {
          avgBox.textContent = '—';
          totalsLabel.textContent = '';
        }

      } catch (err) {
        console.error('BLOGS_CHARTS.refresh error', err);
      }
    }

    function destroy() {
      destroyIf(trendChart); destroyIf(authorsChart); destroyIf(categoryChart); destroyIf(commentsChart); destroyIf(statusChart);
      trendChart = authorsChart = categoryChart = commentsChart = statusChart = null;
      if (avgBox) avgBox.textContent = '—';
      if (totalsLabel) totalsLabel.textContent = '';
    }

    return { init, refresh, destroy };
  })();
}
</script>
@endpush
