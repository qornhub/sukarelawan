<div class="row mb-4">
    <div class="col-md-8">
        <div class="card trend-card h-100">
            <div class="card-body d-flex flex-column">
                <div class="trend-header">
                    <h6 class="trend-title mb-0">Event Participation Trend</h6>
                    <div class="trend-controls">
                        <div class="btn-group btn-group-sm" role="group">
                            <button id="btn-daily" class="btn btn-period active">Daily</button>
                            <button id="btn-monthly" class="btn btn-period">Monthly</button>
                            <button id="btn-yearly" class="btn btn-period">Yearly</button>
                        </div>
                        <button id="resetZoom" class="btn btn-reset btn-sm">
                            <i class="bi bi-arrow-clockwise me-1"></i>Reset Zoom
                        </button>
                    </div>
                </div>
                <div class="chart-container flex-grow-1">
                    <canvas id="eventTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card category-card h-100">
            <div class="card-body d-flex flex-column">
                <h6 class="category-title mb-3">Event Categories</h6>
                <div class="pie-chart-container">
                    <canvas id="eventCategoryPie"></canvas>
                </div>
                <div class="category-list-container mt-3 flex-grow-1">
                    @if (empty($categoryData))
                        <div class="empty-state text-center py-4">
                            <i class="bi bi-inbox text-muted fs-1"></i>
                            <p class="text-muted mb-0 mt-2">No categories available</p>
                        </div>
                    @else
                        <div class="category-items">
                            @foreach ($categoryData as $c)
                                <div class="category-item d-flex justify-content-between align-items-center py-2">
                                    <span class="category-label text-truncate">{{ $c['label'] }}</span>
                                    <span class="category-count">{{ $c['count'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    if (window.__suka_eventTrend_init) return;
    window.__suka_eventTrend_init = true;

    const dailyLabels = @json($eventTrendDailyLabels ?? []);
    const dailyCounts = @json($eventTrendDailyCounts ?? []);
    const monthsLabels = @json($eventTrendMonthsLabels ?? []);
    const monthsCounts = @json($eventTrendMonthsCounts ?? []);
    const yearsLabels = @json($eventTrendYearsLabels ?? []);
    const yearsCounts = @json($eventTrendYearsCounts ?? []);
    const categoryData = @json($categoryData ?? []);

    const MAX_VISIBLE_POINTS = 36;

    function sanitize(labels, data) {
        const sLabels = labels.map(l => String(l || ''));
        const sData = data.map(d => Number(d) || 0);
        const len = Math.min(sLabels.length, sData.length);
        return {
            labels: sLabels.slice(-Math.min(len, MAX_VISIBLE_POINTS)),
            data: sData.slice(-Math.min(len, MAX_VISIBLE_POINTS))
        };
    }

    let eventTrendChart = null;
    const trendCtx = document.getElementById('eventTrendChart').getContext('2d');

    function createTrendChart(labels, data) {
        if (eventTrendChart) eventTrendChart.destroy();

        eventTrendChart = new Chart(trendCtx, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Registrations',
                    data,
                    fill: true,
                    borderColor: '#004AAD',
                    backgroundColor: 'rgba(0,74,173,0.1)',
                    tension: 0.25,
                    borderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    x: {
                        type: 'category',
                        title: { display: true, text: 'Period' },
                        ticks: { autoSkip: true, maxTicksLimit: 12 },
                        offset: true,
                    },
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Registrations' },
                        ticks: { precision: 0 },
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: { mode: 'index', intersect: false },
                    zoom: {
                        pan: {
                            enabled: true,
                            mode: 'x',
                            modifierKey: null,
                            onPanStart: () => document.body.style.cursor = 'grabbing',
                            onPanComplete: () => document.body.style.cursor = 'default',
                        },
                        zoom: {
                            wheel: { enabled: true, speed: 0.1, modifierKey: null },
                            drag: {
                                enabled: true,
                                backgroundColor: 'rgba(0,74,173,0.15)',
                                borderColor: '#004AAD'
                            },
                            pinch: { enabled: true },
                            mode: 'x'
                        },
                        limits: {
                            x: { min: null, max: null } // unrestricted pan range
                        }
                    }
                }
            }
        });

        // Allow touchpad and touchscreen gestures (requires chartjs-plugin-zoom)
        trendCtx.canvas.style.touchAction = 'none';
    }

    const btnDaily = document.getElementById('btn-daily');
    const btnMonthly = document.getElementById('btn-monthly');
    const btnYearly = document.getElementById('btn-yearly');

    function setActive(btn) {
        [btnDaily, btnMonthly, btnYearly].forEach(b => b?.classList.remove('active'));
        btn?.classList.add('active');
    }

    btnDaily.addEventListener('click', () => {
        setActive(btnDaily);
        const s = sanitize(dailyLabels, dailyCounts);
        createTrendChart(s.labels, s.data);
    });
    btnMonthly.addEventListener('click', () => {
        setActive(btnMonthly);
        const s = sanitize(monthsLabels, monthsCounts);
        createTrendChart(s.labels, s.data);
    });
    btnYearly.addEventListener('click', () => {
        setActive(btnYearly);
        const s = sanitize(yearsLabels, yearsCounts);
        createTrendChart(s.labels, s.data);
    });

    // Default chart (daily)
    const s = sanitize(dailyLabels, dailyCounts);
    createTrendChart(s.labels, s.data);

    // Reset zoom button
    document.getElementById('resetZoom').addEventListener('click', () => {
        if (eventTrendChart) eventTrendChart.resetZoom();
    });

    // Pie chart
    const pieCtx = document.getElementById('eventCategoryPie').getContext('2d');
    if (categoryData?.length) {
        new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: categoryData.map(c => c.label || 'Unknown'),
                datasets: [{
                    data: categoryData.map(c => Number(c.count) || 0)
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }
})();
</script>
@endpush

