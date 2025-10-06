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
    <link rel="stylesheet" href="{{ asset('css/blogs/index.css') }}">

</head>

<body>

    {{-- header / nav include --}}
    @include('layouts.volunteer_header')

    {{-- HERO --}}
    <section class="blog-hero">
        <div class="container">
            <div class="row gy-4 align-items-center">
                <div class="col-md-8">
                    <h1 class="title">Real experiences. Real impact.</h1>
                    <p class="subtitle mt-3">Explore personal stories, tips, and reflections from volunteers and NGOs —
                        be inspired to take your own step toward change.</p>
                </div>

                <div class="col-md-4 ">
                    {{-- Search & New button --}}
                    <div
                        class="d-flex justify-content-md-end justify-content-center align-items-center gap-3 flex-wrap">


                        <form id="blog-search-form" action="{{ route('blogs.index') }}" method="GET"
                            class="d-flex align-items-center" role="search" aria-label="Search blogs">
                            <label for="blog-search" class="sr-only">Search Blog</label>
                            <div class="input-group search-box">
                                <input id="blog-search" type="search" name="q" value="{{ request('q') }}"
                                    class="form-control" placeholder="Search Blog" aria-label="Search Blog"
                                    autocomplete="off">
                                <button class="btn btn-outline-secondary" type="submit" title="Search">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </form>

                        {{-- Loading indicator --}}
                        <div id="search-loading" class="search-loading" style="display:none;">
                            <div class="spinner-border spinner-border-sm text-primary" role="status"
                                aria-hidden="true"></div>
                            <span>Searching...</span>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('layouts.messages')

    {{-- MAIN: blog posts list --}}
    <main>
        <div class="container main-content">

            {{-- New blog button --}}
            @php
                $createRoute = null;
                if (auth()->check()) {
                    $user = auth()->user();
                    if (isset($user->role) && in_array(strtolower($user->role), ['volunteer', 'ngo', 'admin'])) {
                        $createRoute = route(strtolower($user->role) . '.blogs.create');
                    } elseif (property_exists($user, 'is_admin') && $user->is_admin) {
                        $createRoute = route('admin.blogs.create');
                    } else {
                        $createRoute = route('volunteer.blogs.create');
                    }
                } else {
                    $createRoute = route('login');
                }
            @endphp

            <div>
                <div class="d-flex justify-content-end mb-4">
                    <a href="{{ $createRoute }}" class="btn new-blog-btn d-inline-flex align-items-center">
                        <i class="bi bi-plus-lg me-2" style="font-size:1.05rem"></i>
                        <span>New Blog</span>
                    </a>
                </div>

            </div>

            {{-- posts list container --}}
            <div id="posts-area">
                @if ($posts->count() === 0)
                    <div class="no-results">
                        <i class="bi bi-file-text"></i>
                        <h4>No blog posts found</h4>
                        <p>Try adjusting your search or create a new blog post.</p>
                    </div>
                @else
                    <div class="posts-list">
                        @foreach ($posts as $post)
                            <div class="card blog-card p-3">
                                <div class="row g-3 align-items-center">
                                    {{-- IMAGE LEFT --}}
                                    <div class="col-md-5">
                                        @php
                                            $img = $post->image
                                                ? asset('images/Blog/' . $post->image)
                                                : asset('images/Blog/default-blog.jpg');
                                        @endphp
                                        <div class="ratio ratio-16x9 rounded" style="overflow:hidden;">
                                            <img src="{{ $img }}" alt="{{ $post->title }}" class="img-left">
                                        </div>
                                    </div>

                                    {{-- RIGHT CONTENT --}}
                                    <div class="col-md-7">
                                        <div class="card-body card-body-vertical p-0 ps-md-3">
                                            {{-- Title --}}
                                            <h2 class="post-title">
                                                <a href="{{ route('blogs.show', $post->blogPost_id) }}">
                                                    {{ $post->title }}
                                                </a>
                                            </h2>

                                            {{-- Excerpt --}}
                                            <p class="excerpt">
                                                {!! \Illuminate\Support\Str::limit(strip_tags($post->content), 300, '...') !!}
                                            </p>

                                            {{-- Meta table - matches the image exactly --}}
                                            <table class="meta-table">
                                                <thead>
                                                    <tr>
                                                        <th>Category</th>
                                                        <th>Publication Date</th>
                                                        <th>Author</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="category">
                                                            {{ optional($post->category)->categoryName ?? 'Uncategorized' }}
                                                        </td>

                                                        <td class="publication-date">
                                                            @if ($post->published_at)
                                                                {{ \Carbon\Carbon::parse($post->published_at)->format('F d, Y') }}
                                                            @else
                                                                <span class="text-muted">Not published</span>
                                                            @endif
                                                        </td>

                                                        <td class="author">
                                                            @php
                                                                $author = optional($post->user);
                                                            @endphp

                                                            @if ($author && $author->id)
                                                                <a href="{{ route('volunteer.profile.show', $author->id) }}"
                                                                    class="text-primary text-decoration-none"
                                                                    title="View volunteer profile">
                                                                    {{ $author->name ?? 'Unknown' }}
                                                                </a>
                                                            @else
                                                                {{ $author->name ?? 'Unknown' }}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                </tbody>

                                            </table>

                                            {{-- Read more button --}}
                                            <div class="read-more-wrap">
                                                <a href="{{ route('blogs.show', $post->blogPost_id) }}"
                                                    class="btn btn-read-more d-inline-flex align-items-center">
                                                    Read More
                                                    <i class="bi bi-arrow-right-short ms-2"
                                                        style="font-size:1.25rem;line-height:1"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Pagination --}}
            @if ($posts->count() > 0)
                <div class="mt-5 d-flex justify-content-center">
                    {{ $posts->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </main>

    @include('layouts.volunteer_footer')

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Improved Search JS --}}
    <script>
        (function() {
            // Elements
            const searchInput = document.getElementById('blog-search');
            const form = document.getElementById('blog-search-form');
            const postsArea = document.getElementById('posts-area');
            const loadingIndicator = document.getElementById('search-loading');

            // Track current search to prevent duplicates
            let currentSearchQuery = '';
            let isSearching = false;
            let abortController = null;

            // Debounce util
            function debounce(fn, wait) {
                let timeout;
                return function(...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => fn.apply(this, args), wait);
                };
            }

            // Show/hide loading indicator
            function setLoading(isLoading) {
                isSearching = isLoading;
                if (loadingIndicator) {
                    loadingIndicator.style.display = isLoading ? 'inline-flex' : 'none';
                }
            }

            // Abort previous request if needed
            function abortPreviousRequest() {
                if (abortController) {
                    abortController.abort();
                    abortController = null;
                }
            }

            // Fetch and replace posts area
            async function fetchSearchResults(query) {
                // Don't search if already searching for the same query
                if (isSearching && query === currentSearchQuery) return;

                abortPreviousRequest();
                currentSearchQuery = query;

                try {
                    setLoading(true);
                    abortController = new AbortController();

                    // Build URL with query
                    const url = new URL(window.location.href);
                    if (query) {
                        url.searchParams.set('q', query);
                    } else {
                        url.searchParams.delete('q');
                    }

                    const resp = await fetch(url.toString(), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html'
                        },
                        method: 'GET',
                        credentials: 'same-origin',
                        signal: abortController.signal
                    });

                    if (!resp.ok) {
                        throw new Error(`HTTP error! status: ${resp.status}`);
                    }

                    const text = await resp.text();

                    // Use DOMParser to extract the posts area
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(text, 'text/html');

                    // Extract the main content we need
                    const newPostsArea = doc.getElementById('posts-area');
                    const newPagination = doc.querySelector('.pagination, .mt-5.d-flex.justify-content-center');

                    if (newPostsArea) {
                        // Replace content
                        let replacementHTML = newPostsArea.innerHTML;

                        // Add pagination if exists
                        if (newPagination) {
                            replacementHTML += '<div class="mt-5 d-flex justify-content-center">' + newPagination
                                .innerHTML + '</div>';
                        }

                        postsArea.innerHTML = replacementHTML;
                    } else {
                        // Fallback: try to extract the main content
                        const mainContent = doc.querySelector('main .container') || doc.querySelector('main');
                        if (mainContent) {
                            postsArea.innerHTML = mainContent.innerHTML;
                        } else {
                            postsArea.innerHTML =
                                '<div class="no-results"><i class="bi bi-search"></i><h4>No results found</h4><p>Try different keywords</p></div>';
                        }
                    }

                    // Update URL without page reload
                    window.history.replaceState({}, '', url.toString());

                } catch (err) {
                    if (err.name === 'AbortError') {
                        console.log('Search request was aborted');
                        return;
                    }
                    console.error('Search error:', err);

                    // Fallback to normal form submission on error
                    if (!query) {
                        window.location.href = "{{ route('blogs.index') }}";
                    } else {
                        form.submit();
                    }
                } finally {
                    setLoading(false);
                    abortController = null;
                }
            }

            // Debounced search handler
            const debouncedSearch = debounce(function(e) {
                const query = e.target.value.trim();
                fetchSearchResults(query);
            }, 500);

            // Event listeners
            if (searchInput) {
                // Input event for live search
                searchInput.addEventListener('input', function(e) {
                    debouncedSearch(e);
                });

                // Form submit handler
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const query = searchInput.value.trim();
                    fetchSearchResults(query);
                });

                // Clear search on Escape key
                searchInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        searchInput.value = '';
                        fetchSearchResults('');
                        e.preventDefault();
                    }
                });
            }

            // Handle initial page load with search query
            document.addEventListener('DOMContentLoaded', function() {
                const urlParams = new URLSearchParams(window.location.search);
                const initialQuery = urlParams.get('q') || '';

                if (initialQuery && searchInput) {
                    searchInput.value = initialQuery;
                }
            });
        })();
    </script>

    @stack('scripts')
</body>

</html>
