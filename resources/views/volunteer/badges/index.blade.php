<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Badges — Volunteer Dashboard</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-blue: #4361ee;
            --secondary-blue: #4895ef;
            --light-blue: #e6f0ff;
            --primary-purple: #7209b7;
            --secondary-purple: #b5179e;
            --light-purple: #f3e8fd;
            --dark-gray: #333;
            --medium-gray: #6c757d;
            --light-gray: #f9f9f9;
            --text-color: #333;
        }

        body {
            background-color: #f0f5ff;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            color: var(--text-color);
        }

        /* Hero Banner */
        .hero {
            position: relative;
            height: 360px;
            background-image: url('https://images.unsplash.com/photo-1552664730-d307ca884978?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1770&q=80');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            overflow: hidden;
        }

        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(83, 111, 235, 0.7), rgba(136, 22, 212, 0.8));
        }

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            padding: 0 20px;
        }

        .hero-title {
            font-size: clamp(2rem, 4vw, 3.5rem);
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .hero-sub {
            font-size: clamp(1rem, 2vw, 1.5rem);
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
        }

        /* Leaderboard Canvas */
        .leaderboard-canvas-container {
            position: relative;
            border-radius: 0 0 12px 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border: none;
            overflow: hidden;
            background-color: #f8f9fa;
        }

        .leaderboard-canvas {
            width: 100%;
            height: 450px;
            display: block;
        }

        .leaderboard-header {
            background-color: var(--primary-purple);
            color: white;
            font-weight: 600;
            padding: 15px;
            text-align: center;
            border-radius: 12px 12px 0 0;
        }

        /* Top-3 quick links (clickable names + avatars under canvas) */
        .top3-links {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 12px;
            margin-bottom: 20px;
        }

        .top3-item {
            text-align: center;
            width: 140px;
        }

        .top3-avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e9e9e9;
            display: block;
            margin: 0 auto 8px;
        }

        .top3-name {
            display: block;
            font-weight: 600;
            color: var(--dark-gray);
            text-decoration: none;
        }

        .top3-name:hover {
            text-decoration: underline;
        }

        /* List group for remaining leaderboard */
        .leaderboard-list {
            background-color: white;
            border-radius: 0 0 12px 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .list-group-item {
            border-left: none;
            border-right: none;
            padding: 12px 20px;
        }

        .list-group-item .small-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 12px;
        }

        .bg-purple {
            background-color: var(--primary-purple) !important;
            color: white !important;
        }

        /* Right column cards */
        .col-md-4 .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .col-md-4 .card-header {
            background-color: var(--primary-purple);
            color: white;
            font-weight: 600;
            border-radius: 12px 12px 0 0 !important;
        }

        .events-card .card-body {
            background-color: white;
            padding: 30px 20px;
            text-align: center;
        }

        .btn-primary {
            background-color: var(--primary-purple);
            border-color: var(--primary-purple);
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 600;
        }

        .btn-primary:hover {
            background-color: var(--secondary-purple);
            border-color: var(--secondary-purple);
        }

        .btn-more-reward {
            background-color: transparent;
            border: 1px solid var(--primary-purple);
            color: var(--primary-purple);
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-more-reward:hover {
            background-color: var(--primary-purple);
            color: white;
        }

        /* Category Tabs */
        .category-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        .category-tab {
            padding: 10px 20px;
            background-color: white;
            border-radius: 8px;
            text-decoration: none;
            color: var(--medium-gray);
            font-weight: 500;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
        }

        .category-tab:hover,
        .category-tab.active {
            background-color: var(--primary-purple);
            color: white;
        }

        .category-tab .badge {
            background-color: var(--light-purple) !important;
            color: var(--primary-purple);
        }

        .category-tab.active .badge,
        .category-tab:hover .badge {
            background-color: white !important;
            color: var(--primary-purple);
        }

        /* Search and Sort Container */
        .search-sort-container {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
        }

        .search-box {
            position: relative;
            flex: 1;
        }

        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--medium-gray);
            z-index: 10;
        }

        .search-box .form-control {
            padding-left: 45px;
            border-radius: 8px;
            border: 1px solid #ddd;
            height: 45px;
        }

        .sort-dropdown .btn {
            border-radius: 8px;
            height: 45px;
            border: 1px solid #ddd;
        }

        /* Badge Grid */
        .badge-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .badge-card {
            background: white;
            border-radius: 12px;
            overflow: visible;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            padding: 20px;
        }

        .badge-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        }

        .badge-image-container {
            text-align: center;
            padding: 5px 0;
            background-color: var(--light-purple);
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .badge-image {
            max-width: 200px;
            max-height: 200px;
            width: auto;
            height: auto;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }

        .badge-content {
            padding: 0;
        }

        .badge-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .badge-name {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--dark-gray);
            margin: 0;
            flex: 1;
        }

        .badge-category {
            background-color: var(--light-purple);
            color: var(--primary-purple);
            font-size: 0.75rem;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 600;
        }

        .badge-description {
            color: var(--medium-gray);
            margin-bottom: 15px;
            font-size: 0.9rem;
        }

        .badge-points {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--primary-purple);
            font-weight: 600;
            margin-bottom: 15px;
        }

        .badge-points i {
            color: gold;
        }

        .badge-footer {
            padding: 15px 0 0 0;
            background-color: transparent;
            border-top: none;
            font-size: 0.8rem;
            color: var(--medium-gray);
            text-align: center;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--medium-gray);
        }

        .empty-state i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            color: var(--dark-gray);
            margin-bottom: 10px;
        }

        /* Pagination */
        .pagination-container {
            display: flex;
            justify-content: center;
        }

        .pagination .page-link {
            color: var(--primary-purple);
            border-radius: 8px;
            margin: 0 5px;
            border: 1px solid #ddd;
        }

        .pagination .page-item.active .page-link {
            background-color: var(--primary-purple);
            color: #fff;
            border-color: var(--primary-purple);
        }

       .rank-highlight { background-color:#7209b7; color:#fff; border-radius:6px; }
    .podium-highlight { background: linear-gradient(180deg, rgba(114,9,183,0.12), transparent); border-radius:6px; padding:6px; }
    .earned-wrapper{ position:relative; }
    .earned-next{ position:absolute; right:6px; top:6px; border:none; background:transparent; cursor:pointer; }
    .earned-grid{ display:grid; grid-template-columns: repeat(2,1fr); gap:12px; align-items:start; }
    .earned-item img{ width:100px; height:100px; object-fit:cover; border-radius:8px; display:block; margin:0 auto 8px; }
    .badge-modal-img{ width:100%; height:100%; object-fit:contain; display:block; margin:0 auto; max-width:260px; max-height:260px; }
    .badge-detail-row{ display:flex; gap:24px; align-items:flex-start; }
    .badge-detail-left{ flex:0 0 320px; }
    .badge-detail-right{ flex:1 1 auto; }
    .more-badges-btn{ display:block; margin:14px auto 0; }
   

        /* small tweak to make list <ol> numbers align when highlighting */
        .list-group-numbered > li.rank-highlight::marker { color: #fff; }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .search-sort-container {
                flex-direction: column;
            }

            .category-tabs {
                overflow-x: auto;
                padding-bottom: 10px;
            }

            .badge-grid {
                grid-template-columns: 1fr;
            }

            .top3-item {
                width: 110px;
            }

             .badge-detail-row{ flex-direction:column; }
      .badge-detail-left{ width:100%; }
      .earned-grid{ grid-template-columns: repeat(2,1fr); }
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
        <div class="leaderboard-header mb-2"><h4 class="mb-0">Leaderboard</h4></div>

        <div class="leaderboard-canvas-container mb-2 p-3 bg-light rounded">
          {{-- Podium top 3 --}}
          <div class="d-flex justify-content-center align-items-end mb-3" style="gap: 2.5rem;">
            @if(isset($topVolunteers[1]))
              @php $u=$topVolunteers[1]; $isCurrent = auth()->check() && auth()->id()===$u->id; @endphp
              <div class="text-center" style="width:160px;">
                <div style="font-size:18px;color:#777;">2</div>
                <a href="{{ route('volunteer.profile.show', $u->id) }}">
                  <img src="{{ $u->avatar_url }}" alt="{{ $u->name }}" style="width:110px;height:110px;border-radius:50%;object-fit:cover;border:5px solid #c0c0c0;">
                </a>
                <div class="mt-2" @if($isCurrent) class="podium-highlight" @endif>
                  <a href="{{ route('volunteer.profile.show', $u->id) }}" style="@if($isCurrent)color:#fff;@endif;text-decoration:none;font-weight:600;">{{ $u->name }}</a>
                </div>
                <div class="small text-muted">{{ $u->total_points }} pts</div>
              </div>
            @endif

            @if(isset($topVolunteers[0]))
              @php $u=$topVolunteers[0]; $isCurrent = auth()->check() && auth()->id()===$u->id; @endphp
              <div class="text-center" style="width:180px;">
                <div style="font-size:18px;color:#FFD700;">1</div>
                <div style="position:relative;display:inline-block;">
                  <a href="{{ route('volunteer.profile.show', $u->id) }}">
                    <img src="{{ $u->avatar_url }}" alt="{{ $u->name }}" style="width:130px;height:130px;border-radius:50%;object-fit:cover;border:6px solid #FFD700;">
                  </a>
                  <div style="position:absolute;left:50%;transform:translateX(-50%);top:-30px;font-size:28px;">👑</div>
                </div>
                <div class="mt-2" @if($isCurrent) class="podium-highlight" @endif>
                  <a href="{{ route('volunteer.profile.show', $u->id) }}" style="@if($isCurrent)color:#fff;@endif;text-decoration:none;font-weight:700;">{{ $u->name }}</a>
                </div>
                <div class="small text-muted">{{ $u->total_points }} pts</div>
              </div>
            @endif

            @if(isset($topVolunteers[2]))
              @php $u=$topVolunteers[2]; $isCurrent = auth()->check() && auth()->id()===$u->id; @endphp
              <div class="text-center" style="width:160px;">
                <div style="font-size:18px;color:#cd7f32;">3</div>
                <a href="{{ route('volunteer.profile.show', $u->id) }}">
                  <img src="{{ $u->avatar_url }}" alt="{{ $u->name }}" style="width:110px;height:110px;border-radius:50%;object-fit:cover;border:5px solid #cd7f32;">
                </a>
                <div class="mt-2" @if($isCurrent) class="podium-highlight" @endif>
                  <a href="{{ route('volunteer.profile.show', $u->id) }}" style="@if($isCurrent)color:#fff;@endif;text-decoration:none;font-weight:600;">{{ $u->name }}</a>
                </div>
                <div class="small text-muted">{{ $u->total_points }} pts</div>
              </div>
            @endif
          </div>
        </div>

        {{-- Remaining — manual numbering so start at 4 --}}
        <div class="leaderboard-list">
          <ol class="list-group list-group-numbered">
            @foreach($topVolunteers->skip(3) as $loopIndex => $volunteer)
              @php $isCurrent = auth()->check() && auth()->id()===$volunteer->id; @endphp
              <li class="list-group-item d-flex justify-content-between align-items-center @if($isCurrent) rank-highlight @endif">
                <div class="d-flex align-items-center">
                  <a href="{{ route('volunteer.profile.show', $volunteer->id) }}">
                    <img src="{{ $volunteer->avatar_url }}" alt="{{ $volunteer->name }}" style="width:44px;height:44px;border-radius:50%;object-fit:cover;margin-right:12px;">
                  </a>
                  <a href="{{ route('volunteer.profile.show', $volunteer->id) }}" style="font-weight:600;text-decoration:none;@if($isCurrent)color:#fff;@endif;">
                    <span class="me-2 text-muted" style="font-weight:600;">{{ $loopIndex + 4 }}.</span> {{ $volunteer->name }}
                  </a>
                </div>
                <span class="badge bg-purple rounded-pill">{{ $volunteer->total_points }} pts</span>
              </li>
            @endforeach
          </ol>
        </div>
      </div>

      <!-- RIGHT: Events + Earned (server-side pages) -->
      <div class="col-md-4">
        <div class="card events-card mb-4">
          <div class="card-body">
            <h5 class="card-title">Join More Events to Earn Points</h5>
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
                @foreach($earnedBadges as $eb)
                  @php $claimedAt = optional($eb->pivot->created_at) ? $eb->pivot->created_at->format('d M Y') : '-'; @endphp
                  <div class="earned-item" data-badge-id="{{ $eb->badge_id }}">
                    <a href="javascript:void(0)" class="earned-open" data-badge-id="{{ $eb->badge_id }}">
                      <img src="{{ $eb->img_url ?? asset('images/badges/default-badge.jpg') }}" alt="{{ $eb->badgeName }}">
                    </a>
                    <div style="font-weight:600; font-size:0.95rem;">{{ $eb->badgeName }}</div>
                    <div class="small text-muted">Claimed {{ $claimedAt }}</div>
                  </div>
                @endforeach
              </div>

              {{-- bottom controls / page nav & "More Badges" scroll button --}}
              <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                  @if($earnedLast > 1)
                    <nav aria-label="Earned badges pages">
                      <ul class="pagination pagination-sm mb-0">
                        {{-- previous --}}
                        <li class="page-item {{ $earnedCurrent <= 1 ? 'disabled' : '' }}">
                          <a class="page-link" href="{{ $earnedCurrent > 1 ? $earnedBadges->url($earnedCurrent - 1) : '#' }}" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                          </a>
                        </li>

                        {{-- page numbers (show up to 5 pages, centered on current) --}}
                        @php
                          $start = max(1, $earnedCurrent - 2);
                          $end = min($earnedLast, $start + 4);
                          if ($end - $start < 4) { $start = max(1, $end - 4); }
                        @endphp

                        @for($p = $start; $p <= $end; $p++)
                          <li class="page-item {{ $p === $earnedCurrent ? 'active' : '' }}">
                            <a class="page-link" href="{{ $earnedBadges->url($p) }}">{{ $p }}</a>
                          </li>
                        @endfor

                        {{-- next --}}
                        <li class="page-item {{ $earnedCurrent >= $earnedLast ? 'disabled' : '' }}">
                          <a class="page-link" href="{{ $earnedCurrent < $earnedLast ? $earnedBadges->url($earnedCurrent + 1) : '#' }}" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                          </a>
                        </li>
                      </ul>
                    </nav>
                  @endif
                </div>

                <div>
                  {{-- More Badges scroll to main badges section --}}
                  <a id="earnedMoreBtn" href="#badges-section" class="btn btn-outline-primary btn-sm">More Badges</a>
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
        <a href="{{ route('volunteer.rewards.index') }}" class="category-tab {{ request('category') ? '' : 'active' }}">
          All <span class="badge bg-secondary ms-1">{{ \App\Models\Badge::count() }}</span>
        </a>
        @foreach ($categories as $cat)
          <a href="{{ route('volunteer.rewards.index', array_merge(request()->except('page'), ['category' => $cat->badgeCategory_id])) }}"
             class="category-tab {{ request('category') == $cat->badgeCategory_id ? 'active' : '' }}">
            {{ $cat->badgeCategoryName }} <span class="badge bg-secondary ms-1">{{ $cat->badges_count }}</span>
          </a>
        @endforeach
      </div>

      <div class="search-sort-container mt-3 mb-3 d-flex gap-3">
        <form method="GET" action="{{ route('volunteer.rewards.index') }}" class="search-box flex-grow-1">
          <input type="hidden" name="category" value="{{ request('category') }}">
          <i class="fas fa-search search-icon"></i>
          <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search badges...">
        </form>

        <div class="sort-dropdown">
          <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fas fa-sort me-2"></i>
              @if(request('sort') == 'points_asc') Points: Low to High
              @elseif(request('sort') == 'points_desc') Points: High to Low
              @elseif(request('sort') == 'oldest') Oldest First
              @else Newest First
              @endif
            </button>
            <ul class="dropdown-menu" aria-labelledby="sortDropdown">
              <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort'=>'newest','page'=>1]) }}">Newest First</a></li>
              <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort'=>'oldest','page'=>1]) }}">Oldest First</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort'=>'points_asc','page'=>1]) }}">Points: Low to High</a></li>
              <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort'=>'points_desc','page'=>1]) }}">Points: High to Low</a></li>
            </ul>
          </div>
        </div>
      </div>

      {{-- Badge grid --}}
      @if ($badges->count() > 0)
        <div class="badge-grid d-flex flex-wrap gap-3">
          @foreach ($badges as $badge)
            <div class="badge-card card p-3" style="width:300px;"
                 data-badge-id="{{ $badge->badge_id }}"
                 data-badge-name="{{ e($badge->badgeName) }}"
                 data-badge-desc="{{ e($badge->badgeDescription) }}"
                 data-badge-img="{{ $badge->img_url ?? asset('images/badges/default-badge.jpg') }}"
                 data-badge-created="{{ $badge->created_at->format('d M Y') }}"
                 data-badge-category="{{ $badge->category->badgeCategoryName ?? 'Uncategorized' }}"
                 data-badge-claimed="{{ $badge->claimed_count ?? 0 }}"
                 data-badge-points="{{ $badge->pointsRequired ?? 0 }}"
                 data-badge-has-earned="{{ $badge->has_earned ? '1' : '0' }}"
                 data-claim-route="{{ route('volunteer.badges.claim', $badge->badge_id) }}"
            >
              <div class="badge-image-container text-center mb-2">
                <img src="{{ $badge->img_url ?? asset('images/badges/default-badge.jpg') }}" alt="{{ $badge->badgeName }}" style="max-width:120px; max-height:120px; object-fit:contain;">
              </div>

              <div class="badge-content">
                <div class="badge-header d-flex justify-content-between align-items-start mb-2">
                  <h3 class="badge-name" style="font-size:1.1rem;">{{ $badge->badgeName }}</h3>
                  <span class="badge-category small">{{ $badge->category->badgeCategoryName ?? 'Uncategorized' }}</span>
                </div>
                <p class="badge-description text-muted" style="min-height:36px;">{{ \Illuminate\Support\Str::limit($badge->badgeDescription, 120) }}</p>

                <div class="badge-points mb-2">
                  <i class="fas fa-star"></i>
                  <span>{{ $badge->pointsRequired }} points required</span>
                </div>

                @if (!empty($badge->has_earned) && $badge->has_earned)
                  <span class="badge bg-secondary">Claimed</span>
                @elseif (!empty($badge->claimable_by_user) && $badge->claimable_by_user)
                  <span class="badge bg-success">Claimable</span>
                  <form action="{{ route('volunteer.badges.claim', $badge->badge_id) }}" method="POST" class="mt-2">
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
  (function(){
    const points = Number({{ json_encode($points ?? 0) }});
    const bsModal = new bootstrap.Modal(document.getElementById('badgeModal'));

    // open modal from badge cards
    document.querySelectorAll('.badge-card').forEach(card => {
      card.addEventListener('click', function(e){
        if (e.target.closest('form')) return;
        const ds = this.dataset;
        document.getElementById('badgeModalImg').src = ds.badgeImg || '';
        document.getElementById('badgeModalName').textContent = ds.badgeName || '';
        document.getElementById('badgeModalDesc').textContent = ds.badgeDesc || '';
        document.getElementById('badgeModalCreated').textContent = ds.badgeCreated || '';
        document.getElementById('badgeModalCategory').textContent = ds.badgeCategory || '';
        document.getElementById('badgeModalPoints').textContent = ds.badgePoints || '0';
        document.getElementById('badgeModalClaimed').textContent = ds.badgeClaimed || '0';

        const action = document.getElementById('badgeModalAction');
        action.innerHTML = '';

        const hasEarned = ds.badgeHasEarned === '1';
        const claimRoute = ds.claimRoute || '';

        if (hasEarned) {
          action.innerHTML = '<span class="badge bg-secondary">Already Claimed</span>';
        } else if (points >= parseInt(ds.badgePoints || '0', 10)) {
          if (claimRoute) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = claimRoute;
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content') || '';
            const inToken = document.createElement('input');
            inToken.type = 'hidden';
            inToken.name = '_token';
            inToken.value = token;
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

        bsModal.show();
      });
    });

    // earned thumbnail click: if card present open modal, else navigate to badges section with open_badge param
    document.querySelectorAll('.earned-open').forEach(el => {
      el.addEventListener('click', function(e){
        const bid = this.dataset.badgeId;
        if (!bid) return;
        const card = document.querySelector('.badge-card[data-badge-id="'+bid+'"]');
        if (card) {
          card.click();
        } else {
          // go to badges section and include "open_badge" param so page can handle it or user can see it
          const base = new URL(window.location.href);
          base.searchParams.set('open_badge', bid);
          // keep earned_page param if present (so you don't lose earned page)
          window.location.href = base.pathname + base.search + '#badges-section';
        }
      });
    });

    // if URL contains open_badge param on load, try to open modal
    document.addEventListener('DOMContentLoaded', function(){
      const params = new URLSearchParams(window.location.search);
      const openBadge = params.get('open_badge');
      if (openBadge) {
        const card = document.querySelector('.badge-card[data-badge-id="'+openBadge+'"]');
        if (card) card.click();
        // otherwise nothing: user is sent to #badges-section to look for it
      }
    });

    // small UX for earned "More Badges" anchor to scroll to bottom of badges list
    const moreBtn = document.getElementById('earnedMoreBtn');
    if(moreBtn){
      moreBtn.addEventListener('click', function(e){
        // default anchor behavior will navigate to #badges-section; ensure smooth scroll
        e.preventDefault();
        const target = document.getElementById('badges-section');
        if (target) target.scrollIntoView({ behavior: 'smooth', block: 'end' });
      });
    }
  })();
  </script>
</body>
</html>