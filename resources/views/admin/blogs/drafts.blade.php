{{-- resources/views/admin/blogs/drafts.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>My Blog Posts</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --admin-blue: #004AAD;
            --admin-blue-dark: #003780;
            --admin-gray: #f3f7ff;
            /* soft blue like your screenshot */
            --side-nav-width: 40px;
        }

        body {
            background: var(--admin-gray);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #222;
            min-height: 100vh;
        }

        .admin-container {
            max-width: 1400px;
            margin-left: calc(var(--side-nav-width) + 30px);
            padding: 1.5rem;
            margin-right: calc(var(--side-nav-width) + 30px);
        }

        @media (max-width: 992px) {
            .admin-container {
                margin-left: 0;
            }
        }

        .blog-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            transition: .25s ease;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            height: 100%;
        }

        .blog-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12);
        }

        .blog-image {
            height: 200px;
            width: 100%;
            object-fit: cover;
        }

        .badge-draft {
            background: #ffc107;
            color: #222;
        }

        .badge-published {
            background: #28a745;
        }

        .card-title {
            font-size: 1.05rem;
            font-weight: 600;
        }

        .card-excerpt {
            font-size: 0.95rem;
        }

        /* --- 3-dot actions menu --- */
        .actions-menu {
            position: relative;
            z-index: 2;
        }

        .actions-toggle {
            border: none;
            background: transparent;
            padding: 0.25rem 0.5rem;
            border-radius: 999px;
            cursor: pointer;
            transition: background-color .15s ease, transform .1s ease;
        }

        .actions-toggle i {
            font-size: 1.1rem;
            color: #555;
            transition: color .15s ease;
        }

        .actions-toggle:hover {
            background-color: rgba(0, 0, 0, 0.06);
            transform: translateY(-1px);
        }

        .actions-toggle:hover i {
            color: var(--admin-blue-dark);
        }

        .actions-dropdown {
            position: absolute;
            top: 120%;
            right: 0;
            min-width: 160px;
            background: #fff;
            border-radius: 0.5rem;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
            padding: 0.25rem 0;
            display: none;
        }

        .actions-dropdown.show {
            display: block;
        }

        .actions-dropdown .dropdown-item {
            font-size: 0.9rem;
            padding: 0.45rem 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .actions-dropdown .dropdown-item i {
            font-size: 1rem;
        }

        .actions-dropdown .dropdown-item:hover {
            background-color: #f1f5ff;
        }
    </style>
</head>

<body>

    @include('layouts.admin_nav')

    <div style="margin-left: 70px;">
        <div class="admin-container">

            <h1 class="fw-bold mb-4" style="color: var(--admin-blue);">
                My Blog Posts (Draft & Published)
            </h1>

            @include('layouts.messages')

            @if ($drafts->count() === 0)
                <div class="alert alert-info">You have no blog posts yet.</div>
            @endif

            <div class="row g-4">
                @foreach ($drafts as $post)
                    @php
                        $imageUrl = $post->image
                            ? asset('images/Blog/' . $post->image)
                            : asset('images/Blog/default_blog.jpg');

                        // ===== CLEAN EXCERPT LOGIC (summary > content) =====
                        if (!empty($post->blogSummary)) {
                            $excerpt = \Illuminate\Support\Str::limit(
                                trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($post->blogSummary)))),
                                120,
                                '...',
                            );
                        } else {
                            $excerpt = \Illuminate\Support\Str::limit(
                                trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($post->content)))),
                                120,
                                '...',
                            );
                        }

                        $categoryName =
                            $post->custom_category ?: optional($post->category)->categoryName ?: 'Uncategorized';

                        $badgeClass = $post->status === 'draft' ? 'badge-draft' : 'badge-published';

                        // click: draft -> edit, published -> show
                        $cardLink =
                            $post->status === 'draft'
                                ? route('admin.blogs.edit', $post->blogPost_id)
                                : route('admin.blogs.show', $post->blogPost_id);
                    @endphp

                    <div class="col-md-6 col-12">
                        <div class="card blog-card position-relative">

                            {{-- Image --}}
                            <img src="{{ $imageUrl }}" class="blog-image" alt="{{ $post->title }}">

                            {{-- Body as flex column so bottom meta aligns --}}
                            <div class="card-body d-flex flex-column">

                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <h5 class="card-title mb-0">{{ $post->title }}</h5>

                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge {{ $badgeClass }}">
                                            @if ($post->status === 'draft')
                                                <i class="bi bi-pencil-square me-1"></i> Draft
                                            @else
                                                <i class="bi bi-check-circle me-1"></i> Published
                                            @endif
                                        </span>

                                        {{-- 3-dot menu --}}
                                        <div class="actions-menu">
                                            <button type="button" class="actions-toggle" aria-label="More actions"
                                                data-menu-id="menu-{{ $post->blogPost_id }}">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>

                                            <div class="actions-dropdown" id="menu-{{ $post->blogPost_id }}">
                                                {{-- Edit --}}
                                                <a href="{{ route('admin.blogs.edit', $post->blogPost_id) }}"
                                                    class="dropdown-item">
                                                    <i class="bi bi-pencil-square"></i>
                                                    <span>Edit</span>
                                                </a>

                                                {{-- Delete (own post → go back to drafts in controller) --}}
                                                <form
                                                    action="{{ route('admin.blogs.adminDestroy', $post->blogPost_id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Are you sure you want to delete this blog post?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="bi bi-trash"></i>
                                                        <span>Delete</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Excerpt --}}
                                <p class="text-muted mt-2 mb-2 card-excerpt">
                                    {{ $excerpt }}
                                </p>

                                {{-- Meta row pinned to bottom --}}
                                <div class="d-flex flex-wrap gap-4 small text-muted mt-auto pt-2">
                                    <div>
                                        <i class="bi bi-calendar me-1"></i>
                                        {{ $post->updated_at ? $post->updated_at->format('j M Y') : '-' }}
                                    </div>
                                    <div>
                                        <i class="bi bi-folder me-1"></i>
                                        {{ $categoryName }}
                                    </div>
                                    <div>
                                        <i class="bi bi-person me-1"></i>
                                        {{ optional($post->user)->name ?? 'Admin' }}
                                    </div>
                                </div>
                            </div>

                            {{-- Whole-card clickable (behind actions menu) --}}
                            <a href="{{ $cardLink }}" class="stretched-link"></a>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if ($drafts->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $drafts->links('pagination::bootstrap-5') }}
                </div>
            @endif

        </div>
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Simple JS for 3-dot dropdown --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let openMenu = null;

            document.querySelectorAll('.actions-toggle').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();

                    const menuId = this.getAttribute('data-menu-id');
                    const menu = document.getElementById(menuId);
                    if (!menu) return;

                    if (openMenu && openMenu !== menu) {
                        openMenu.classList.remove('show');
                    }

                    menu.classList.toggle('show');
                    openMenu = menu.classList.contains('show') ? menu : null;
                });
            });

            document.addEventListener('click', function() {
                if (openMenu) {
                    openMenu.classList.remove('show');
                    openMenu = null;
                }
            });
        });
    </script>

</body>

</html>
