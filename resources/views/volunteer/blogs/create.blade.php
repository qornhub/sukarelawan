<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create blog</title> 
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS (via CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Optionally include Bootstrap Icons if you use them -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { border-radius: .6rem; }
    </style>
</head>
<body class="bg-light">

    @include('layouts.volunteer_header')
    @include('layouts.messages')

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">Create Blog Post</h5>
                        <a href="{{ route('blogs.index') }}" class="btn btn-sm btn-light">← Back to Blog</a>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('volunteer.blogs.store') }}" method="POST" enctype="multipart/form-data">
                            @include('volunteer.blogs._form')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@include('layouts.volunteer_footer')
    <!-- Bootstrap JS (via CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Stacked scripts (TinyMCE from the form partial will be pushed here) -->
    @stack('scripts')
</body>
</html>
