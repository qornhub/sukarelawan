<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Blog</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Optional: Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* Header styling inspired by your screenshot */
        .blog-hero {
            background-color: #f1f3f5;
            padding: 48px 0 32px;
        }

        .blog-hero .title {
            font-weight: 800;
            font-size: 2.4rem;
            letter-spacing: -0.5px;
        }

        .blog-hero .subtitle {
            color: #5b6268;
            font-size: 1rem;
            max-width: 520px;
        }

        .search-box {
            max-width: 420px;
            width: 100%;
        }

        .new-blog-btn {
            border-radius: 6px;
        }

        .blog-card {
            border: none;
            background: #fff;
        }

        .blog-card .img-left {
            border-radius: 8px;
            object-fit: cover;
            width: 100%;
            height: 100%;
            max-height: 240px;
        }

        .meta-row .meta-item {
            font-size: 0.9rem;
            color: #586169;
            margin-right: 18px;
        }

        .excerpt {
            color: #4f5458;
        }

        @media (max-width: 767px) {
            .blog-hero .title { font-size: 1.6rem; }
            .blog-hero { padding: 28px 0 20px; }
            .blog-card .img-left { max-height: 180px; }
        }
    </style>
</head>
<body class="bg-light">

    {{-- header / nav (your header include could go here if you have one) --}}
    @include('layouts.volunteer_header') {{-- safe even for guests; adjust if causes issues --}}
    @include('layouts.messages')

    {{-- Hero --}}
    <section class="blog-hero">
        <div class="container">
            <div class="row align-items-center gy-3">
                <div class="col-md-6">
                    <h1 class="title">Real experiences. Real impact.</h1>
                    <p class="subtitle mt-3">Explore personal stories, tips, and reflections from volunteers and NGOs — be inspired to take your own step toward change.</p>
                </div>

                <div class="col-md-6 text-md-end">
                    {{-- Search box centered visually --}}
                    <form action="{{ route('blogs.index') }}" method="GET" class="d-flex justify-content-center justify-content-md-end mb-3">
                        <div class="input-group search-box">
                            <input
                                type="search"
                                name="q"
                                value="{{ request('q') }}"
                                class="form-control"
                                placeholder="Search Blog"
                                aria-label="Search Blog"
                            >
                            <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                        </div>
                    </form>

                    {{-- New Blog button: route depends on logged-in user's role --}}
                    @php
                        $createRoute = null;
                        if (auth()->check()) {
                            $user = auth()->user();
                            if (isset($user->role) && in_array(strtolower($user->role), ['volunteer','ngo','admin'])) {
                                $createRoute = route(strtolower($user->role) . '.blogs.create');
                            } elseif (property_exists($user, 'is_admin') && $user->is_admin) {
                                $createRoute = route('admin.blogs.create');
                            } else {
                                // fallback to volunteer create if role unknown
                                $createRoute = route('volunteer.blogs.create');
                            }
                        } else {
                            $createRoute = route('login');
                        }
                    @endphp

                    <div class="mt-2 d-flex justify-content-center justify-content-md-end">
                        <a href="{{ $createRoute }}" class="btn btn-primary new-blog-btn">
                            <i class="bi bi-plus-lg me-1"></i> New Blog
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Blog list --}}
    <main class="py-5">
        <div class="container">
            @if($posts->count() === 0)
                <div class="alert alert-info">No blog posts found.</div>
            @endif

            <div class="row g-4">
                @foreach($posts as $post)
                    <div class="col-12">
                        <div class="card blog-card shadow-sm p-3">
                            <div class="row g-0 align-items-center">
                                {{-- Left image --}}
                                <div class="col-md-5 pe-md-4 mb-3 mb-md-0">
                                    @php
                                        $img = $post->image ? asset('images/Blog/' . $post->image) : asset('images/Blog/default-blog.jpg');
                                    @endphp
                                    <img src="{{ $img }}" alt="{{ $post->title }}" class="img-left w-100 rounded">
                                </div>

                                {{-- Right content --}}
                                <div class="col-md-7">
                                    <div class="card-body p-0 ps-md-3">
                                        <h3 class="h5">
                                            <a href="{{ route('blogs.show', $post->blogPost_id) }}" class="text-decoration-none text-primary">
                                                {{ $post->title }}
                                            </a>
                                        </h3>

                                        <p class="excerpt mt-2 mb-3">
                                            {!! \Illuminate\Support\Str::limit(strip_tags($post->content), 300, '...') !!}
                                        </p>

                                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center meta-row">
                                            <div class="d-flex align-items-center flex-wrap">
                                                <div class="meta-item me-3">
                                                    <strong>Category</strong><br>
                                                    <a href="#" class="text-decoration-none">{{ optional($post->category)->categoryName ?? 'Uncategorized' }}</a>
                                                </div>

                                                <div class="meta-item me-3">
                                                    <strong>Publication Date</strong><br>
                                                    @if($post->published_at)
                                                        {{ \Carbon\Carbon::parse($post->published_at)->format('F d, Y') }}
                                                    @else
                                                        <span class="text-muted">Not published</span>
                                                    @endif
                                                </div>

                                                <div class="meta-item">
                                                    <strong>Author</strong><br>
                                                    {{ optional($post->user)->name ?? 'Unknown' }}
                                                </div>
                                            </div>

                                            <div class="mt-3 mt-sm-0">
                                                <a href="{{ route('blogs.show', $post->blogPost_id) }}" class="btn btn-outline-primary">
                                                    Read More <i class="bi bi-arrow-right-short"></i>
                                                </a>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div> {{-- row --}}
                        </div> {{-- card --}}
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-5 d-flex justify-content-center">
                {{ $posts->withQueryString()->links() }}
            </div>
        </div>
    </main>

    {{-- Footer (optional include) --}}
    @include('layouts.volunteer_footer')

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts') {{-- in case TinyMCE or other scripts are pushed --}}
</body>
</html>
