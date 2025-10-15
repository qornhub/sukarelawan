<div class="row mb-4">
    <div class="col-md-4">
        <div class="card blog-stat-card h-100">
            <div class="card-body text-center d-flex flex-column justify-content-center">
                <div class="blog-stat-icon mb-3">
                    <i class="bi bi-file-text"></i>
                </div>
                <h6 class="blog-stat-label">Total Blog</h6>
                <div class="blog-stat-value">{{ $totalBlogs }}</div>
                <a href="{{ route('ngo.blogs.create') }}" class="btn btn-create-blog mt-3">Create Blog</a>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card trend-card h-100">
            <div class="card-body d-flex flex-column">
                <div class="trend-header">
                    <h6 class="trend-title mb-0">Blog Trends</h6>
                    <div class="trend-controls">
                        <div class="btn-group btn-group-sm" role="group">
                            <button id="blog-daily" class="btn btn-period active">Daily</button>
                            <button id="blog-monthly" class="btn btn-period">Monthly</button>
                            <button id="blog-yearly" class="btn btn-period">Yearly</button>
                        </div>
                        <button id="blog-resetZoom" class="btn btn-reset btn-sm">
                            <i class="bi bi-arrow-clockwise me-1"></i>Reset Zoom
                        </button>
                    </div>
                </div>
                <div class="chart-container flex-grow-1">
                    <canvas id="blogTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- Chart.js + Zoom Plugin -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@2.0.1"></script>

<script>
(function () {
    if (window.__suka_blogTrend_init) return;
    window.__suka_blogTrend_init = true;

    // --- Load data from backend ---
    const dailyLabels   = @json($blogDailyLabels ?? []);
    const dailyCounts   = @json($blogDailyCounts ?? []);
    const monthlyLabels = @json($blogMonthlyLabels ?? []);
    const monthlyCounts = @json($blogMonthlyCounts ?? []);
    const yearlyLabels  = @json($blogYearlyLabels ?? []);
    const yearlyCounts  = @json($blogYearlyCounts ?? []);

    let blogChart = null;
    const ctx = document.getElementById('blogTrendChart').getContext('2d');

    // --- Create Chart Function ---
    function createBlogChart(labels, data) {
        if (blogChart) blogChart.destroy();

        blogChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Blogs',
                    data,
                    backgroundColor: '#0b63d5',
                    borderRadius: 4,
                    barThickness: 'flex',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        title: { display: true, text: 'Period' },
                        ticks: { maxRotation: 0, minRotation: 0 }
                    },
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Number of Blogs' },
                        grace: '10%'
                    }
                },
                plugins: {
                    legend: { display: false },
                    zoom: {
                        zoom: {
                            wheel: { enabled: true },
                            pinch: { enabled: true },
                            mode: 'x',
                        },
                        pan: {
                            enabled: true,
                            mode: 'x',
                        },
                        limits: {
                            x: { min: 'original', max: 'original' },
                            y: { min: 0 }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => ` ${ctx.formattedValue} blog${ctx.parsed.y !== 1 ? 's' : ''}`
                        }
                    }
                }
            }
        });
    }

    // --- Button Controls ---
    const btnDaily = document.getElementById('blog-daily');
    const btnMonthly = document.getElementById('blog-monthly');
    const btnYearly = document.getElementById('blog-yearly');
    const btnResetZoom = document.getElementById('blog-resetZoom');

    function setActive(btn) {
        [btnDaily, btnMonthly, btnYearly].forEach(b => b && b.classList.remove('active'));
        if (btn) btn.classList.add('active');
    }

    function updateDaily() {
        setActive(btnDaily);
        createBlogChart(dailyLabels, dailyCounts);
    }

    function updateMonthly() {
        setActive(btnMonthly);
        createBlogChart(monthlyLabels, monthlyCounts);
    }

    function updateYearly() {
        setActive(btnYearly);
        createBlogChart(yearlyLabels, yearlyCounts);
    }

    // --- Event Listeners ---
    btnDaily.addEventListener('click', updateDaily);
    btnMonthly.addEventListener('click', updateMonthly);
    btnYearly.addEventListener('click', updateYearly);
    btnResetZoom.addEventListener('click', function () {
        if (blogChart) blogChart.resetZoom();
    });

    // Default: show daily
    updateDaily();
})();
</script>
@endpush
