<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Badges — Volunteer Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/badges/badges.css') }}">

    <style>

        /* Ensure text is readable on highlighted rows */
.rank-highlight,
.list-group-item.rank-highlight {
    background-color: var(--primary-purple);
    color: #fff;
}

/* Make anchors (names) white and remove default link color in highlighted rows */
.rank-highlight a,
.rank-highlight a:visited,
.rank-highlight a:hover,
.rank-highlight a:focus {
    color: #fff !important;
    text-decoration: none;
}

/* Make small / muted text readable on purple */
.rank-highlight .small,
.rank-highlight .text-muted,
.rank-highlight .me-2,
.rank-highlight .list-group-numbered>li::marker {
    color: rgba(255,255,255,0.92) !important;
}

/* Style badges inside highlighted rows so they remain visible (lighter translucent pill) */
.rank-highlight .badge {
    background-color: rgba(255,255,255,0.14) !important;
    color: #fff !important;
    border: 1px solid rgba(255,255,255,0.18);
}

/* Slight visual lift for avatar on highlighted row */
.rank-highlight img {
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    border: 2px solid rgba(255,255,255,0.06);
}

/* Podium-specific: ensure podium highlighted names are white (for top-3) */
.podium-highlight a,
.podium-highlight a:visited,
.podium-highlight a:focus,
.podium-highlight a:hover {
    color: #fff !important;
}

/* If you used the .podium-current-link class above, style it too */
.podium-current-link {
    color: #fff !important;
}

/* Optional: adjust list marker when using ordered list and highlight */
.list-group-numbered>li.rank-highlight::marker {
    color: #fff;
}

/* Fix username visibility for top-1 user */
.podium-current-1 a {
    color: #1a1a1a !important; /* dark color, readable on white background */
}

/* Fix for top-2 (silver background / grey border) */
.podium-current-2 a {
    color: #1a1a1a !important;
}

/* Fix for top-3 (bronze) */
.podium-current-3 a {
    color: #1a1a1a !important;
}

    </style>
</head>

<body>
    @include('layouts.volunteer_header')

    <!-- HERO -->
    <header class="hero mb-3">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1 class="hero-title">Rewards</h1>
            <h2 class="hero-sub">Impact experiences can be more fun, rewarding, and organised.</h2>
        </div>
    </header>

    @include('layouts.messages')

    <div class="container mt-4">
        <div class="row">
            <!-- LEFT: Leaderboard -->
            <div class="col-md-8">
                <div class="leaderboard-header mb-2">
                    <h4 class="mb-0">Leaderboard</h4>
                </div>

                <div class="leaderboard-canvas-container mb-2 p-3 bg-light rounded">
                    {{-- Podium top 3 --}}
                    <div class="d-flex justify-content-center align-items-end mb-3" style="gap: 2.5rem;">
                        @if (isset($topVolunteers[1]))
                            @php
                                $u = $topVolunteers[1];
                                $isCurrent = auth()->check() && auth()->id() === $u->id;
                            @endphp
                            <div class="text-center" style="width:160px;">
                                <div style="font-size:18px;color:#777;">2</div>
                                <a href="{{ route('volunteer.profile.show', $u->id) }}">
                                    <img src="{{ $u->avatar_url }}" alt="{{ $u->name }}"
                                        style="width:110px;height:110px;border-radius:50%;object-fit:cover;border:5px solid #c0c0c0;">
                                </a>
                                <div class="mt-2 podium-slot @if ($isCurrent) podium-current-2 @endif">

                                    <a href="{{ route('volunteer.profile.show', $u->id) }}"
                                        style="@if ($isCurrent) color:#fff; @endif;text-decoration:none;font-weight:600;">{{ $u->name }}</a>
                                </div>
                                <div class="badge bg-purple rounded-pill">{{ $u->total_points }} pts</div>
                            </div>
                        @endif

                        @if (isset($topVolunteers[0]))
                            @php
                                $u = $topVolunteers[0];
                                $isCurrent = auth()->check() && auth()->id() === $u->id;
                            @endphp
                            <div class="text-center" style="width:180px;">
                                <div style="font-size:18px;color:#FFD700;">1</div>
                                <div style="position:relative;display:inline-block;">
                                    <a href="{{ route('volunteer.profile.show', $u->id) }}">
                                        <img src="{{ $u->avatar_url }}" alt="{{ $u->name }}"
                                            style="width:130px;height:130px;border-radius:50%;object-fit:cover;border:6px solid #FFD700;">
                                    </a>
                                    <div
                                        style="position:absolute;left:50%;transform:translateX(-50%);top:-30px;font-size:28px;">
                                        👑</div>
                                </div>
                                
                                    <div class="mt-2 podium-slot @if ($isCurrent) podium-current-1 @endif">

                                    <a href="{{ route('volunteer.profile.show', $u->id) }}"
                                        style="@if ($isCurrent) color:#fff; @endif;text-decoration:none;font-weight:700;">{{ $u->name }}</a>
                                </div>
                                <div class="badge bg-purple rounded-pill">{{ $u->total_points }} pts</div>
                            </div>
                        @endif

                        @if (isset($topVolunteers[2]))
                            @php
                                $u = $topVolunteers[2];
                                $isCurrent = auth()->check() && auth()->id() === $u->id;
                            @endphp
                            <div class="text-center" style="width:160px;">
                                <div style="font-size:18px;color:#cd7f32;">3</div>
                                <a href="{{ route('volunteer.profile.show', $u->id) }}">
                                    <img src="{{ $u->avatar_url }}" alt="{{ $u->name }}"
                                        style="width:110px;height:110px;border-radius:50%;object-fit:cover;border:5px solid #cd7f32;">
                                </a>
                                <div class="mt-2 podium-slot @if ($isCurrent) podium-current-3 @endif">

                                    <a href="{{ route('volunteer.profile.show', $u->id) }}"
                                        style="@if ($isCurrent) color:#fff; @endif;text-decoration:none;font-weight:600;">{{ $u->name }}</a>
                                </div>
                                <div class="badge bg-purple rounded-pill">{{ $u->total_points }} pts</div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Remaining — manual numbering so start at 4 --}}
                <div class="leaderboard-list">
                    
                        @foreach ($topVolunteers->skip(3) as $volunteer)
                            <li
                                class="list-group-item d-flex justify-content-between align-items-center @if (auth()->check() && auth()->id() === $volunteer->id) rank-highlight @endif">
                                <div class="d-flex align-items-center">
                                   <span class="me-2 text-muted"
                                            style="font-weight:600;">{{ $loop->iteration + 3 }}.</span>
                                    <a href="{{ route('volunteer.profile.show', $volunteer->id) }}">
                                     
                                        <img src="{{ $volunteer->avatar_url }}" alt="{{ $volunteer->name }}"
                                            style="width:44px;height:44px;border-radius:50%;object-fit:cover;margin-right:12px;">
                                    </a>
                                    <a href="{{ route('volunteer.profile.show', $volunteer->id) }}"
                                        style="font-weight:600;text-decoration:none;">
                                        
                                        {{ $volunteer->name }}
                                    </a>
                                </div>
                                <span class="badge bg-purple rounded-pill">{{ $volunteer->total_points }} pts</span>
                            </li>
                        @endforeach

                    
                </div>
            </div>

            <!-- RIGHT: Events + Earned (server-side pages) -->
            <div class="col-md-4">
                <div class="card events-card mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Join More Events to Earn Points</h5>
                        <a href="{{ route('volunteer.index.public') }}" class="btn btn-primary">Join Now</a>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Rewards Earned</h5>
                    </div>

                    <div class="card-body">
                        {{-- earned badges paginator is $earnedBadges --}}
                        @php
                            $earnedCurrent = $earnedBadges->currentPage();
                            $earnedLast = $earnedBadges->lastPage();
                        @endphp

                        <div class="earned-wrapper">


                            {{-- grid shows only current page (server-side paginated) --}}
                            <div id="earnedGrid" class="earned-grid">
                                @foreach ($earnedBadges as $eb)
                                    @php $claimedAt = optional($eb->pivot->created_at) ? $eb->pivot->created_at->format('d M Y') : '-'; @endphp
                                    <div class="earned-item" data-badge-id="{{ $eb->badge_id }}">
                                        <a href="javascript:void(0)" class="earned-open"
                                            data-badge-id="{{ $eb->badge_id }}">
                                            <img src="{{ $eb->img_url ?? asset('images/badges/default-badge.jpg') }}"
                                                alt="{{ $eb->badgeName }}">
                                        </a>
                                        <div style="font-weight:600; font-size:0.95rem;">{{ $eb->badgeName }}</div>
                                        <div class="small text-muted">Claimed {{ $claimedAt }}</div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- bottom controls / page nav & "More Badges" scroll button --}}
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div>
                                    @if ($earnedLast > 1)
                                        <nav aria-label="Earned badges pages">
                                            <ul class="pagination pagination-sm mb-0">
                                                {{-- previous --}}
                                                <li class="page-item {{ $earnedCurrent <= 1 ? 'disabled' : '' }}">
                                                    <a class="page-link"
                                                        href="{{ $earnedCurrent > 1 ? $earnedBadges->url($earnedCurrent - 1) : '#' }}"
                                                        aria-label="Previous">
                                                        <span aria-hidden="true">&laquo;</span>
                                                    </a>
                                                </li>

                                                {{-- page numbers (show up to 5 pages, centered on current) --}}
                                                @php
                                                    $start = max(1, $earnedCurrent - 2);
                                                    $end = min($earnedLast, $start + 4);
                                                    if ($end - $start < 4) {
                                                        $start = max(1, $end - 4);
                                                    }
                                                @endphp

                                                @for ($p = $start; $p <= $end; $p++)
                                                    <li class="page-item {{ $p === $earnedCurrent ? 'active' : '' }}">
                                                        <a class="page-link"
                                                            href="{{ $earnedBadges->url($p) }}">{{ $p }}</a>
                                                    </li>
                                                @endfor

                                                {{-- next --}}
                                                <li
                                                    class="page-item {{ $earnedCurrent >= $earnedLast ? 'disabled' : '' }}">
                                                    <a class="page-link"
                                                        href="{{ $earnedCurrent < $earnedLast ? $earnedBadges->url($earnedCurrent + 1) : '#' }}"
                                                        aria-label="Next">
                                                        <span aria-hidden="true">&raquo;</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </nav>
                                    @endif
                                </div>

                                <div>
                                    {{-- More Badges scroll to main badges section --}}
                                    <a id="earnedMoreBtn" href="#badges-section"
                                        class="btn btn-outline-primary btn-sm">More Badges</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> {{-- end card --}}
            </div>
        </div>

        <!-- BADGES SECTION -->
        <section id="badges-section" class="mt-5">
            <div class="category-tabs mb-3">
                <a href="{{ route('volunteer.rewards.index') }}"
                    class="category-tab {{ request('category') ? '' : 'active' }}">
                    All <span class="badge bg-secondary ms-1">{{ \App\Models\Badge::count() }}</span>
                </a>
                @foreach ($categories as $cat)
                    <a href="{{ route('volunteer.rewards.index', array_merge(request()->except('page'), ['category' => $cat->badgeCategory_id])) }}"
                        class="category-tab {{ request('category') == $cat->badgeCategory_id ? 'active' : '' }}">
                        {{ $cat->badgeCategoryName }} <span
                            class="badge bg-secondary ms-1">{{ $cat->badges_count }}</span>
                    </a>
                @endforeach
            </div>

            <div class="search-sort-container mt-3 mb-3 d-flex gap-3">
                <form method="GET" action="{{ route('volunteer.rewards.index') }}" class="search-box flex-grow-1">
                    <input type="hidden" name="category" value="{{ request('category') }}">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                        placeholder="Search badges...">
                </form>

                <div class="sort-dropdown">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-sort me-2"></i>
                            @if (request('sort') == 'points_asc')
                                Points: Low to High
                            @elseif(request('sort') == 'points_desc')
                                Points: High to Low
                            @elseif(request('sort') == 'oldest')
                                Oldest First
                            @else
                                Newest First
                            @endif
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                            <li><a class="dropdown-item"
                                    href="{{ request()->fullUrlWithQuery(['sort' => 'newest', 'page' => 1]) }}">Newest
                                    First</a></li>
                            <li><a class="dropdown-item"
                                    href="{{ request()->fullUrlWithQuery(['sort' => 'oldest', 'page' => 1]) }}">Oldest
                                    First</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item"
                                    href="{{ request()->fullUrlWithQuery(['sort' => 'points_asc', 'page' => 1]) }}">Points:
                                    Low to High</a></li>
                            <li><a class="dropdown-item"
                                    href="{{ request()->fullUrlWithQuery(['sort' => 'points_desc', 'page' => 1]) }}">Points:
                                    High to Low</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Badge grid --}}
            @if ($badges->count() > 0)
                <div class="badge-grid d-flex flex-wrap gap-3">
                    @foreach ($badges as $badge)
                        <div class="badge-card card p-3" style="width:300px;" data-badge-id="{{ $badge->badge_id }}"
                            data-badge-name="{{ e($badge->badgeName) }}"
                            data-badge-desc="{{ e($badge->badgeDescription) }}"
                            data-badge-img="{{ $badge->img_url ?? asset('images/badges/default-badge.jpg') }}"
                            data-badge-created="{{ $badge->created_at->format('d M Y') }}"
                            data-badge-category="{{ $badge->category->badgeCategoryName ?? 'Uncategorized' }}"
                            data-badge-claimed="{{ $badge->claimed_count ?? 0 }}"
                            data-badge-points="{{ $badge->pointsRequired ?? 0 }}"
                            data-badge-has-earned="{{ $badge->has_earned ? '1' : '0' }}"
                            data-claim-route="{{ route('volunteer.badges.claim', $badge->badge_id) }}">
                            <div class="badge-image-container text-center mb-2">
                                <img src="{{ $badge->img_url ?? asset('images/badges/default-badge.jpg') }}"
                                    alt="{{ $badge->badgeName }}"
                                    style="max-width:120px; max-height:120px; object-fit:contain;">
                            </div>

                            <div class="badge-content">
                                <div class="badge-header d-flex justify-content-between align-items-start mb-2">
                                    <h3 class="badge-name" style="font-size:1.1rem;">{{ $badge->badgeName }}</h3>
                                    <span
                                        class="badge-category small">{{ $badge->category->badgeCategoryName ?? 'Uncategorized' }}</span>
                                </div>
                                <p class="badge-description text-muted" style="min-height:36px;">
                                    {{ \Illuminate\Support\Str::limit($badge->badgeDescription, 120) }}</p>

                                <div class="badge-points mb-2">
                                    <i class="fas fa-star"></i>
                                    <span>{{ $badge->pointsRequired }} points required</span>
                                </div>

                                @if (!empty($badge->has_earned) && $badge->has_earned)
                                    <span class="badge bg-secondary">Claimed</span>
                                @elseif (!empty($badge->claimable_by_user) && $badge->claimable_by_user)
                                    <span class="badge bg-success">Claimable</span>
                                    <form action="{{ route('volunteer.badges.claim', $badge->badge_id) }}"
                                        method="POST" class="mt-2">
                                        @csrf
                                        <button type="submit" class="btn btn-primary w-100">Claim Badge</button>
                                    </form>
                                @else
                                    <span class="badge bg-secondary">Not Enough Points</span>
                                @endif
                            </div>

                            <div class="badge-footer text-center mt-3">
                                <small>Created {{ $badge->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- pagination for main badges (already server-side) --}}
                <div class="pagination-container d-flex justify-content-center mt-4">
                    {{ $badges->appends(request()->query())->links() }}
                </div>
            @else
                <div class="empty-state text-center">
                    <i class="fas fa-award"></i>
                    <h3>No badges found</h3>
                    <p class="text-muted">Try adjusting your search or filter criteria</p>
                </div>
            @endif
        </section>
    </div>

    {{-- Badge detail modal (image left, details right) --}}
    <div class="modal fade" id="badgeModal" tabindex="-1" aria-labelledby="badgeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="badgeModalLabel">Badge detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="badge-detail-row">
                        <div class="badge-detail-left text-center">
                            <img id="badgeModalImg" class="badge-modal-img" src="" alt="">
                        </div>

                        <div class="badge-detail-right">
                            <h4 id="badgeModalName"></h4>
                            <p id="badgeModalDesc" class="text-muted"></p>

                            <div class="small text-muted mb-2">
                                <span id="badgeModalCategory"></span> · Created <span id="badgeModalCreated"></span>
                            </div>

                            <div class="mb-2"><strong><span id="badgeModalPoints"></span></strong> required</div>
                            <div class="mb-2">Claimed by <strong id="badgeModalClaimed"></strong> volunteers</div>

                            <div id="badgeModalAction" class="mt-3"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
(function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content') || '';

    // Utility: fetch full page HTML and return parsed document
    async function fetchDoc(url) {
        const res = await fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const text = await res.text();
        const parser = new DOMParser();
        return parser.parseFromString(text, 'text/html');
    }

    // Replace earned-wrapper (earned badges card area)
    function replaceEarned(doc) {
        const newEarned = doc.querySelector('.earned-wrapper');
        const curEarned = document.querySelector('.earned-wrapper');
        if (newEarned && curEarned) {
            curEarned.innerHTML = newEarned.innerHTML;
        }
    }

    // Replace badges grid + pagination
    function replaceBadges(doc) {
        const newGrid = doc.querySelector('.badge-grid');
        const newPager = doc.querySelector('.pagination-container');

        const curGrid = document.querySelector('.badge-grid');
        const curPager = document.querySelector('.pagination-container');

        if (newGrid && curGrid) curGrid.innerHTML = newGrid.innerHTML;
        if (newPager && curPager) curPager.innerHTML = newPager.innerHTML;
    }

    // Replace category tabs active state and counts
    function replaceCategoryTabs(doc) {
        const newTabs = doc.querySelector('.category-tabs');
        const curTabs = document.querySelector('.category-tabs');
        if (newTabs && curTabs) curTabs.innerHTML = newTabs.innerHTML;
    }

    // Re-initialize any UI behaviour that depends on DOM elements (modals, click handlers)
    // We use delegation for most things so this may be light; keep here for future needs.
    function initDynamicUI() {
        // nothing heavy here because most logic uses delegation below
    }

    // Update browser URL and history
    function pushUrl(url) {
        try {
            history.pushState(null, '', url);
        } catch (e) { /* ignore */ }
    }

    // Decide whether an href is for earned paginator (contains earned_page param)
    function isEarnedPageUrl(url) {
        try {
            const u = new URL(url, window.location.origin);
            return u.searchParams.has('earned_page');
        } catch (e) {
            return false;
        }
    }

    // Handle clicks on pagination / category / sort links
    document.addEventListener('click', async function (e) {
        const a = e.target.closest('a');
        if (!a) return;

        // Handle same-page anchors (fragments) — smooth scroll instead of AJAX fetch
const href = a.getAttribute('href') || '';
if (href.startsWith('#')) {
    // If it's an anchor to an element id on the page, scroll to it smoothly.
    const targetId = href.slice(1);
    const targetEl = document.getElementById(targetId);
    if (targetEl) {
        e.preventDefault();
        targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
        // update URL fragment so back/forward works
        try { history.pushState(null, '', href); } catch (err) { /* ignore */ }
        return;
    }
    // if no element found, allow normal behavior (or let other handlers run)
}


        // ===== Earned pagination (links inside earned-wrapper) =====
        if (a.closest('.earned-wrapper') || a.closest('nav[aria-label="Earned badges pages"]')) {
            const href = a.getAttribute('href') || '#';
            if (href === '#' || href === '') { e.preventDefault(); return; }

            e.preventDefault();
            const doc = await fetchDoc(href);
            replaceEarned(doc);
            // If badges section also present in returned doc we should also update .badge-grid (rare)
            // push state to keep URL in sync
            pushUrl(href);
            initDynamicUI();
            return;
        }

        // ===== Main badges pagination / any pagination inside .pagination-container =====
        if (a.closest('.pagination-container')) {
            const href = a.getAttribute('href') || '#';
            if (href === '#' || href === '') { e.preventDefault(); return; }

            e.preventDefault();
            const doc = await fetchDoc(href);
            replaceBadges(doc);
            replaceCategoryTabs(doc); // tabs might change if count updates
            pushUrl(href);
            initDynamicUI();
            return;
        }

        // ===== Category tabs (class .category-tab) =====
        if (a.classList.contains('category-tab')) {
            const href = a.getAttribute('href') || '#';
            if (href === '#' || href === '') { e.preventDefault(); return; }

            e.preventDefault();
            const doc = await fetchDoc(href);
            replaceBadges(doc);
            replaceCategoryTabs(doc);
            replaceEarned(doc); // server might return earned wrapper (keeps everything consistent)
            pushUrl(href);
            initDynamicUI();
            return;
        }

        // ===== Sort dropdown items: they use fullUrlWithQuery links in your blade =====
        if (a.closest('.dropdown-menu')) {
            const href = a.getAttribute('href') || '#';
            if (href === '#' || href === '') { e.preventDefault(); return; }

            // treat like a badges load
            e.preventDefault();
            const doc = await fetchDoc(href);
            replaceBadges(doc);
            replaceCategoryTabs(doc);
            pushUrl(href);
            initDynamicUI();
            return;
        }

        // other anchors: leave as normal (profile links, external, etc.)
    });

    // Intercept search form submit to load badges via AJAX
    const searchForm = document.querySelector('form.search-box');
    if (searchForm) {
        searchForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            // serialize form
            const formData = new FormData(searchForm);
            const params = new URLSearchParams();
            for (const [k, v] of formData.entries()) params.append(k, v);
            const url = searchForm.getAttribute('action') + '?' + params.toString();
            const doc = await fetchDoc(url);
            replaceBadges(doc);
            replaceCategoryTabs(doc);
            pushUrl(url);
            initDynamicUI();
        });
    }

    // Delegated click for badge cards to open modal (was per-element previously)
    document.addEventListener('click', function (e) {
        const card = e.target.closest('.badge-card');
        if (!card) return;

        // if clicked inside a form (claim) let it submit normally
        if (e.target.closest('form')) return;
        e.preventDefault();

        const ds = card.dataset;
        // fill modal elements (same IDs as before)
        const img = document.getElementById('badgeModalImg');
        const name = document.getElementById('badgeModalName');
        const desc = document.getElementById('badgeModalDesc');
        const created = document.getElementById('badgeModalCreated');
        const category = document.getElementById('badgeModalCategory');
        const pointsEl = document.getElementById('badgeModalPoints');
        const claimedEl = document.getElementById('badgeModalClaimed');
        const action = document.getElementById('badgeModalAction');

        if (img) img.src = ds.badgeImg || '';
        if (name) name.textContent = ds.badgeName || '';
        if (desc) desc.textContent = ds.badgeDesc || '';
        if (created) created.textContent = ds.badgeCreated || '';
        if (category) category.textContent = ds.badgeCategory || '';
        if (pointsEl) pointsEl.textContent = ds.badgePoints || '0';
        if (claimedEl) claimedEl.textContent = ds.badgeClaimed || '0';
        if (action) action.innerHTML = '';

        const points = Number({{ json_encode($points ?? 0) }});
        const hasEarned = ds.badgeHasEarned === '1';
        const claimRoute = ds.claimRoute || '';

        if (hasEarned) {
            action.innerHTML = '<span class="badge bg-secondary">Already Claimed</span>';
        } else if (points >= parseInt(ds.badgePoints || '0', 10)) {
            if (claimRoute) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = claimRoute;
                const inToken = document.createElement('input');
                inToken.type = 'hidden';
                inToken.name = '_token';
                inToken.value = csrfToken;
                form.appendChild(inToken);
                const btn = document.createElement('button');
                btn.type = 'submit';
                btn.className = 'btn btn-primary';
                btn.textContent = 'Claim Badge';
                form.appendChild(btn);
                action.appendChild(form);
            } else {
                action.innerHTML = '<span class="badge bg-success">Claimable</span>';
            }
        } else {
            action.innerHTML = '<span class="badge bg-secondary">Not Enough Points</span>';
        }

        // show bootstrap modal
        const modalEl = document.getElementById('badgeModal');
        if (modalEl) {
            const bsModal = new bootstrap.Modal(modalEl);
            bsModal.show();
        }
    });

    // Handle back/forward navigation — re-fetch current URL and update parts
    window.addEventListener('popstate', async function () {
        const url = window.location.href;
        const doc = await fetchDoc(url);
        replaceBadges(doc);
        replaceEarned(doc);
        replaceCategoryTabs(doc);
        initDynamicUI();
    });

    // initial init
    initDynamicUI();
})();
</script>

</body>

</html>
