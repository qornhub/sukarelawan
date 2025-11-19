<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Blog</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Page CSS (contains hero, grid and preview styles) -->
    <link rel="stylesheet" href="{{ asset('css/blogs/create.css') }}">

</head>
<body class="bg-light">

    @include('layouts.ngo_header')
    

    <header class="hero mb-3">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1 class="hero-title">Blog Management</h1>

        </div>
    </header>
    @include('layouts.messages')

   
    

   <!-- MAIN: form area -->
<main class="my-4">
    <div class="row justify-content-center mx-0">
        <div class="col-12 px-5">
            <!-- top label area (left aligned heading like screenshot) -->
            <div class="d-flex align-items-center mb-3">
                
                <div class="section-title">Blog Details</div>
            </div>

            <!-- form wrapper (single form - partial contains fields/grid) -->
            <form action="{{ route('ngo.blogs.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf
                <div class="form-section p-0">
                    @include('volunteer.blogs._form')
                </div>
            </form>
        </div>
    </div>
</main>

    @include('layouts.ngo_footer')

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    {{-- render scripts pushed by the partial (TinyMCE + preview) --}}
    @stack('scripts')
</body>
</html>
