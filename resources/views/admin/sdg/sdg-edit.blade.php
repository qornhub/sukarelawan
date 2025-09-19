{{-- resources/views/admin/sdg/sdg-edit.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit SDG</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ============ Theme variables ============ */
        :root {
            --admin-blue: #004AAD;
            --admin-blue-dark: #003780;
            --admin-gray: #f8f9fa;
            --admin-gray-dark: #e9ecef;
            --admin-danger: #dc3545;
            --admin-success: #28a745;
            --side-nav-width: 40px;
        }

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

        .current-image {
            max-width: 200px;
            border-radius: 8px;
            border: 1px solid var(--admin-gray-dark);
            margin-bottom: 0.5rem;
        }

        .preview-image {
            max-width: 200px;
            border-radius: 8px;
            border: 1px solid var(--admin-gray-dark);
            display: none;
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
            background: #5a6268;
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(108,117,125,0.12);
        }
    </style>
</head>
<body>
    @include('layouts/admin_nav')

    <div class="admin-container">
        <div class="page-header">
            <div class="header-top">
                <h1 class="page-title">Edit Sustainable Development Goal</h1>
                <a href="{{ route('admin.sdg.sdg-list') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
            </div>
        </div>

        <div class="form-section">
            <form action="{{ route('admin.sdg.update', $sdg->sdg_id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- SDG Name -->
                <div class="form-group">
                    <label class="form-label">SDG Name</label>
                    <input type="text" name="sdgName" class="form-control"
                           value="{{ old('sdgName', $sdg->sdgName) }}" required>
                    @error('sdgName')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Current Image -->
                <div class="form-group mb-3">
                    <label class="form-label">Current Image</label><br>
                    @if(!empty($sdg->sdgImage))
                        <img src="{{ asset('images/sdgs/' . $sdg->sdgImage) }}" 
                             alt="SDG Image" class="current-image">
                    @else
                        <div class="text-muted">No image uploaded.</div>
                    @endif
                </div>

                <!-- Upload New Image -->
                <div class="form-group mb-3">
                    <label class="form-label">Change Image (Optional)</label>
                    <input type="file" name="sdgImage" class="form-control" accept="image/*" onchange="previewImage(event)">
                    @error('sdgImage')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Preview Section -->
                <div class="form-group mb-3">
                    <label class="form-label">New Image Preview</label><br>
                    <img id="preview" src="#" alt="Image Preview" class="preview-image">
                </div>

                <!-- Buttons -->
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-2"></i>Update SDG
                </button>
                <a href="{{ route('admin.sdg.sdg-list') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function previewImage(event) {
            const input = event.target;
            const preview = document.getElementById('preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.style.display = 'none';
            }
        }
    </script>
</body>
</html>
