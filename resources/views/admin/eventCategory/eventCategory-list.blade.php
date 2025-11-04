<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Categories</title>
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

        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.25rem;
            margin-top: 1.25rem;
        }

        .category-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 18px rgba(0,0,0,0.04);
            transition: transform .18s ease, box-shadow .18s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .category-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 28px rgba(0,0,0,0.06);
        }

        .category-content {
            padding: 1rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .category-name {
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--admin-blue);
            margin-bottom: 0.5rem;
        }

        .category-date {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .category-actions {
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

        /* responsive tweaks */
        @media (max-width: 768px) {
            .category-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }
        }
    </style>
</head>
<body>
    @include('layouts/admin_nav')

    <div class="admin-container">
        <div class="page-header">
            <div class="header-top">
                <h1 class="page-title">Event Categories</h1>
                <a href="{{ route('admin.eventCategory.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add Event Category
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if($categories->isEmpty())
            <div class="empty-state">
                <i class="fas fa-folder-open"></i>
                <h3>No Categories Found</h3>
                <p class="text-muted">Get started by adding your first event category.</p>
                <a href="{{ route('admin.eventCategory.create') }}" class="btn btn-primary mt-2">
                    <i class="fas fa-plus me-2"></i>Create New Category
                </a>
            </div>
        @else
            <div class="category-grid">
                @foreach($categories as $index => $category)
                    <div class="category-card">
                        <div class="category-content">
                            <h3 class="category-name">{{ $category->eventCategoryName }}</h3>
                            <p class="category-points"><strong>Base Points:</strong> {{ $category->basePoints }}</p>
                            <p class="category-date">Created: {{ $category->created_at ? $category->created_at->format('Y-m-d') : '-' }}</p>
                            <div class="category-actions">
                                <a href="{{ route('admin.eventCategory.edit', $category->eventCategory_id) }}" class="btn-action btn-edit">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </a>
                                <form action="{{ route('admin.eventCategory.destroy', $category->eventCategory_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this category?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn-action btn-delete" type="submit">
                                        <i class="fas fa-trash me-1"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>