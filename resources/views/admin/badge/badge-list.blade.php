{{-- resources/views/admin/badge/badge-list.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Badges Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ============ Theme variables (user requested) ============ */
        :root {
            --admin-blue: #004AAD;
            --admin-blue-dark: #003780;
            --admin-gray: #f8f9fa;
            --admin-gray-dark: #e9ecef;
            --admin-danger: #dc3545;
            --admin-success: #28a745;

            /* helper: change this if your sidebar changes width */
            --side-nav-width: 40px;
        }

        /* Reset + base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--admin-gray);
            min-height: 100vh;
            padding: 20px;
            color: #222;
        }

        /* Main container offset to account for side nav */
        .admin-container {
            max-width: 1400px;
            margin-left: calc(var(--side-nav-width) + 20px); /* left space for your sidebar */
            padding: 1rem;
        }

        /* Small screens: remove left offset so content isn't hidden */
        @media (max-width: 992px) {
            .admin-container {
                margin-left: 0;
                padding: 1rem;
            }
        }

        .page-header {
            background: white;
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 1.25rem;
            box-shadow: 0 6px 18px rgba(0,0,0,0.04);
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
        }

        .page-title {
            font-weight: 700;
            color: var(--admin-blue);
            margin: 0;
            font-size: 1.25rem;
        }

        /* Primary button: solid (no gradient) */
        .btn-primary {
            background: var(--admin-blue);
            border: none;
            color: #fff;
            font-weight: 600;
            padding: 0.55rem 1rem;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: .5rem;
        }
        .btn-primary:hover {
            background: var(--admin-blue-dark);
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(0, 74, 173, 0.12);
        }

        .category-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .category-tab {
            padding: 0.5rem 0.9rem;
            border-radius: 40px;
            background: var(--admin-gray);
            color: #6c757d;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.18s ease;
            border: 1px solid transparent;
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            font-size: .95rem;
        }

        .category-tab:hover {
            background: var(--admin-gray-dark);
            color: var(--admin-blue);
            border-color: rgba(0,0,0,0.04);
        }

        .category-tab.active {
            background: var(--admin-blue);
            color: #fff;
        }

        .search-sort-container {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .search-box {
            position: relative;
            flex-grow: 1;
            min-width: 220px;
        }

        .search-box input {
            padding-left: 2.5rem;
            border-radius: 8px;
        }

        .search-icon {
            position: absolute;
            left: 0.9rem;
            top: 0.6rem;
            color: #6c757d;
            pointer-events: none;
        }

        .sort-dropdown .dropdown-toggle {
            border-radius: 8px;
            min-width: 180px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .5rem;
        }

        .badge-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 1.25rem;
        }

        .badge-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 18px rgba(0,0,0,0.04);
            transition: transform .18s ease, box-shadow .18s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .badge-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 28px rgba(0,0,0,0.06);
        }

        .badge-image {
            width: 100%;
            height: 200px;
            object-fit: contain;
            border-bottom: 1px solid var(--admin-gray-dark);
            background: #fff;
        }

        .badge-content {
            padding: 1rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .badge-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.6rem;
        }

        .badge-name {
            font-weight: 700;
            font-size: 1.05rem;
            margin-bottom: 0;
            color: var(--admin-blue);
        }

        .badge-category {
            display: inline-block;
            background: rgba(0,74,173,0.06);
            color: var(--admin-blue);
            padding: 0.18rem 0.5rem;
            border-radius: 999px;
            font-size: 0.78rem;
            white-space: nowrap;
        }

        .badge-description {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 0.8rem;
            flex-grow: 1;
            overflow: hidden;
        }

        .badge-points {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            color: var(--admin-blue);
            margin-bottom: 0.8rem;
        }

        .badge-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: auto;
        }

        .btn-action {
            flex: 1;
            padding: 0.45rem;
            border-radius: 6px;
            font-size: 0.9rem;
            text-align: center;
            border: 1px solid transparent;
            cursor: pointer;
            background: transparent;
        }

        .btn-edit {
            color: #b58b00;
            border-color: rgba(255,193,7,0.18);
            background: rgba(255,193,7,0.06);
        }

        .btn-edit:hover {
            background: rgba(255,193,7,0.14);
            color: #b58b00;
        }

        .btn-delete {
            color: var(--admin-danger);
            border-color: rgba(220,53,69,0.12);
            background: rgba(220,53,69,0.06);
        }

        .btn-delete:hover {
            background: rgba(220,53,69,0.12);
            color: var(--admin-danger);
        }

        .badge-footer {
            padding: 0.75rem 1rem;
            background: var(--admin-gray);
            border-top: 1px solid var(--admin-gray-dark);
            font-size: 0.82rem;
            color: #6c757d;
        }

        .empty-state {
            text-align: center;
            padding: 2.5rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.04);
        }

        .empty-state i {
            font-size: 3rem;
            color: #dee2e6;
            margin-bottom: 1rem;
        }

        .pagination-container {
            margin-top: 1.5rem;
            display: flex;
            justify-content: center;
        }

        .pagination .page-link {
            border-radius: 8px;
            margin: 0 0.2rem;
            border: none;
            color: var(--admin-blue);
        }

        .pagination .page-item.active .page-link {
            background: var(--admin-blue);
            color: white;
        }

        /* responsive tweaks */
        @media (max-width: 768px) {
            .badge-grid {
                grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            }

            .search-sort-container {
                flex-direction: column;
                align-items: stretch;
            }

            .sort-dropdown .dropdown-toggle {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    @include('layouts/admin_nav')

    <div class="admin-container">
        <div class="page-header">
            <div class="header-top">
                <h1 class="page-title">Badges Management</h1>
                <a href="{{ route('admin.badges.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add New Badge
                </a>
            </div>

            <!-- Category Tabs -->
            <div class="category-tabs">
                <a href="{{ route('admin.badges.index') }}" 
                   class="category-tab {{ request('category') ? '' : 'active' }}">
                   All <span class="badge bg-secondary ms-1">{{ \App\Models\Badge::count() }}</span>
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('admin.badges.index', array_merge(request()->except('page'), ['category' => $cat->badgeCategory_id])) }}"
                       class="category-tab {{ request('category') == $cat->badgeCategory_id ? 'active' : '' }}">
                       {{ $cat->badgeCategoryName }} <span class="badge bg-secondary ms-1">{{ $cat->badges_count }}</span>
                    </a>
                @endforeach
            </div>

            <!-- Search and Sort -->
            <div class="search-sort-container">
                <form method="GET" action="{{ route('admin.badges.index') }}" class="search-box">
                    <input type="hidden" name="category" value="{{ request('category') }}">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" name="q" value="{{ request('q') }}" 
                           class="form-control" placeholder="Search badges by name or description...">
                </form>

                <div class="sort-dropdown">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" 
                                id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-sort me-2"></i>
                            @if(request('sort') == 'points_asc')
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
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'newest', 'page' => 1]) }}">Newest First</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'oldest', 'page' => 1]) }}">Oldest First</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'points_asc', 'page' => 1]) }}">Points: Low to High</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'points_desc', 'page' => 1]) }}">Points: High to Low</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        @include('layouts/messages')

        <!-- Badge Grid -->
        @if($badges->count() > 0)
            <div class="badge-grid">
                @foreach($badges as $badge)
                    <div class="badge-card">
                        <div class="text-center">
                            @if(!empty($badge->badgeImage) && file_exists(public_path($badge->badgeImage)))
                                <img src="{{ asset($badge->badgeImage) }}" class="badge-image" alt="{{ $badge->badgeName }}">
                            @else
                                <img src="{{ asset('images/badges/default-badge.jpg') }}" class="badge-image" alt="Default badge">
                            @endif
                        </div>

                        <div class="badge-content">
                            <div class="badge-header">
                                <h3 class="badge-name">{{ $badge->badgeName }}</h3>
                                <span class="badge-category">{{ $badge->category->badgeCategoryName ?? 'Uncategorized' }}</span>
                            </div>
                            <p class="badge-description">{{ $badge->badgeDescription }}</p>
                            
                            <div class="badge-points">
                                <i class="fas fa-star"></i>
                                <span>{{ $badge->pointsRequired }} points required</span>
                            </div>
                            
                            <div class="badge-actions">
                                <a href="{{ route('admin.badges.edit', $badge->badge_id) }}" class="btn-action btn-edit" aria-label="Edit badge">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </a>
                                
                                <form action="{{ route('admin.badges.destroy', $badge->badge_id) }}" method="POST" 
                                      onsubmit="return confirm('Are you sure you want to delete this badge?');" style="flex:1;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action btn-delete" aria-label="Delete badge">
                                        <i class="fas fa-trash me-1"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <div class="badge-footer">
                            <small>Created {{ $badge->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-award"></i>
                <h3>No badges found</h3>
                <p class="text-muted">
                    @if(request('q') || request('category'))
                        Try adjusting your search or filter criteria
                    @else
                        Get started by creating your first badge
                    @endif
                </p>
                <a href="{{ route('admin.badges.create') }}" class="btn btn-primary mt-2">
                    <i class="fas fa-plus me-2"></i>Create New Badge
                </a>
            </div>
        @endif

        <!-- Pagination -->
        @if($badges->count() > 0)
            <div class="pagination-container">
                {{ $badges->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Attach sort param to the search form if one exists
            const searchForm = document.querySelector('.search-box');
            const sortValue = new URLSearchParams(window.location.search).get('sort');

            if (searchForm && sortValue) {
                // ensure hidden input not duplicated
                if (!searchForm.querySelector('input[name="sort"]')) {
                    const sortInput = document.createElement('input');
                    sortInput.type = 'hidden';
                    sortInput.name = 'sort';
                    sortInput.value = sortValue;
                    searchForm.appendChild(sortInput);
                }
            }
        });
    </script>
</body>
</html>