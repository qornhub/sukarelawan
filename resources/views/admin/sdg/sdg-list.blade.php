{{-- resources/views/admin/sdg/sdg-list.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>SDGs Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --admin-blue: #004AAD;
            --admin-blue-dark: #003780;
            --admin-gray: #f8f9fa;
            --admin-gray-dark: #e9ecef;
            --admin-danger: #dc3545;
            --side-nav-width: 40px;
        }

        body {
            background-color: var(--admin-gray);
            min-height: 100vh;
            padding: 20px;
            color: #222;
        }

        .admin-container {
            max-width: 1400px;
            margin-left: calc(var(--side-nav-width) + 20px);
            padding: 1rem;
        }

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
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.04);
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

        .sdg-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 1.25rem;
        }

        .sdg-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.04);
            transition: transform .18s ease, box-shadow .18s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .sdg-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.06);
        }

        .sdg-image {
            width: 100%;
            height: 200px;
            object-fit: contain;
            border-bottom: 1px solid var(--admin-gray-dark);
            background: #fff;
        }

        .sdg-content {
            padding: 1rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .sdg-name {
            font-weight: 700;
            font-size: 1.05rem;
            margin-bottom: 0.6rem;
            color: var(--admin-blue);
        }

        .sdg-actions {
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
            border-color: rgba(255, 193, 7, 0.18);
            background: rgba(255, 193, 7, 0.06);
        }

        .btn-edit:hover {
            background: rgba(255, 193, 7, 0.14);
            color: #b58b00;
        }

        .btn-delete {
            color: var(--admin-danger);
            border-color: rgba(220, 53, 69, 0.12);
            background: rgba(220, 53, 69, 0.06);
        }

        .btn-delete:hover {
            background: rgba(220, 53, 69, 0.12);
            color: var(--admin-danger);
        }

        .sdg-footer {
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
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.04);
        }

        .empty-state i {
            font-size: 3rem;
            color: #dee2e6;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    @include('layouts/admin_nav')

    <div class="admin-container">
        <div class="page-header">
            <div class="header-top">
                <h1 class="page-title">SDGs Management</h1>
                <a href="{{ route('admin.sdg.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add New SDG
                </a>
            </div>

            <!-- Search -->
            <div class="search-sort-container">
                <form method="GET" action="{{ route('admin.sdg.sdg-list') }}" class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                        placeholder="Search SDGs by name...">
                </form>
            </div>
        </div>

        @include('layouts/messages')

        <!-- SDG Grid -->
        @if ($sdgs->count() > 0)
            <div class="sdg-grid">
                @foreach ($sdgs as $sdg)
                    <div class="sdg-card">
                        <div class="text-center">
                            <img src="{{ asset('images/sdgs/' . $sdg->sdgImage) }}" class="sdg-image"
                                alt="{{ $sdg->sdgName }}"
                                onerror="this.src='{{ asset('images/sdgs/default-sdg.jpg') }}'">
                        </div>

                        <div class="sdg-content">
                            <h3 class="sdg-name">{{ $sdg->sdgName }}</h3>
                            <div class="sdg-actions">
                                <a href="{{ route('admin.sdg.edit', $sdg->sdg_id) }}" class="btn-action btn-edit">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </a>
                                <form action="{{ route('admin.sdg.destroy', $sdg->sdg_id) }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to delete this SDG?');"
                                    style="flex:1;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action btn-delete">
                                        <i class="fas fa-trash me-1"></i>Delete
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="sdg-footer">
                            <small>Created {{ $sdg->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-leaf"></i>
                <h3>No SDGs found</h3>
                <p class="text-muted">Get started by creating your first SDG</p>
                <a href="{{ route('admin.sdg.create') }}" class="btn btn-primary mt-2">
                    <i class="fas fa-plus me-2"></i>Create New SDG
                </a>
            </div>
        @endif

        <!-- Pagination -->
        @if ($sdgs->count() > 0)
            <div class="pagination-container mt-4 d-flex justify-content-center">
                {{ $sdgs->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
