<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Badge Category</title>
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

        .form-section {
            background: white;
            border-radius: 12px;
            padding: 1.25rem;
            box-shadow: 0 6px 18px rgba(0,0,0,0.04);
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            font-weight: 600;
            color: #222;
            margin-bottom: 0.5rem;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid var(--admin-gray-dark);
        }

        .form-control:focus {
            border-color: var(--admin-blue);
            box-shadow: 0 0 0 0.2rem rgba(0,74,173,0.25);
        }

        .error-text {
            color: var(--admin-danger);
            font-size: 0.875rem;
        }

        .btn-success {
            background: var(--admin-success);
            border: none;
            color: #fff;
            font-weight: 600;
            padding: 0.55rem 1rem;
            border-radius: 8px;
        }
        .btn-success:hover {
            background: #23913d;
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(40,167,69,0.12);
        }

        .btn-secondary {
            background: #6c757d;
            border: none;
            color: #fff;
            font-weight: 600;
            padding: 0.55rem 1rem;
            border-radius: 8px;
        }
        .btn-secondary:hover {
            background: darken(#6c757d, 10%);
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(108,117,125,0.12);
        }

        /* responsive tweaks */
        @media (max-width: 768px) {
            .row {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    @include('layouts/admin_nav')

    <div class="admin-container">
        <div class="page-header">
            <div class="header-top">
                <h1 class="page-title">Edit Badge Category</h1>
                <a href="{{ route('admin.badge_categories.badgeCategory-list') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
            </div>
        </div>

        @include('layouts/messages')

        <div class="form-section">
            <form action="{{ route('admin.badge_categories.update', $category->badgeCategory_id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label">Category Name</label>
                    <input type="text" 
                           name="badgeCategoryName" 
                           class="form-control" 
                           value="{{ old('badgeCategoryName', $category->badgeCategoryName) }}" 
                           required>
                </div>

                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-2"></i>Save Changes
                </button>
                <a href="{{ route('admin.badge_categories.badgeCategory-list') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>