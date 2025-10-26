<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>{{ $post->title }} - Blog</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/blogs/show.css') }}">
    <link rel="stylesheet" href="{{ asset('css/blogs/comment.css') }}">
    <style>
        .post-settings {
            top: -20px;
            position: relative;
            /* keeps it in normal flow (Option A) */
            margin-bottom: 38px;
            /* your existing gap */
            text-align: right;
            /* RIGHT-align the inline button inside this block */
        }

        /* keep the button visuals */
        .post-settings .btn-settings {
            display: inline-block;
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(0, 0, 0, 0.08);
            padding: 8px 10px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
            color: #333;
        }




        /* responsive tweaks — on small screens keep icon closer to the card */
        @media (max-width: 767.98px) {
            .post-settings {
                top: -20px;
                /* less negative on narrow screens */
                right: 12px;
            }

            .main-content-card {
                padding-top: 20px;
            }
        }
    </style>
</head>

<body>

    @include('layouts.volunteer_header')

    <!-- Blog Header with Background Image in HTML -->
    <section class="blog-header">
        <img src="{{ $post->image ? asset('images/Blog/' . $post->image) : asset('images/Blog/default-blog.jpg') }}"
            alt="{{ $post->title }}" class="header-image">
        <div class="container main-container">
            <div class="row">
                <div class="col-12">
                    <h1 class="title">{{ $post->title }}</h1>

                </div>
            </div>
        </div>
    </section>

    @include('layouts.messages')

    <!-- Main Content -->
    <main>
        <div class="container main-container">



           <div class="post-settings">
                <div class="btn-group">
                    <button type="button" class="btn btn-settings btn-sm dropdown-toggle dropdown-toggle-no-caret"
                        data-bs-toggle="dropdown" aria-expanded="false" title="Post settings">
                        <i class="fa fa-cog"></i>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end">
                        {{-- Edit --}}
                        <li>
                            <a class="dropdown-item" href="{{ route('volunteer.blogs.edit', $post->blogPost_id) }}">
                                <i class="fa fa-edit me-2"></i> Edit
                            </a>
                        </li>

                        {{-- Delete --}}
                        <li>
                            <form action="{{ route('volunteer.blogs.destroy', $post->blogPost_id) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to permanently delete this post? This action cannot be undone.');"
                                class="m-0 p-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fa fa-trash me-2"></i> Delete Post
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>


            <!-- Single Card Container -->
            <div class="main-content-card">
                <div class="row content-row">
                    <!-- Left Column - Blog Content (col-md-8) -->
                    <div class="col-md-8 content-left">
                        <h2 class="content-title">Blog Content</h2>
                        <div class="blog-content">
                            <!-- Main Content with Read More -->
                            <div id="read-more-section" class="read-more-section">
                                {!! $post->content !!}

                                <!-- Read More Overlay -->
                                <div class="read-more-overlay">
                                    <button class="btn-read-more-toggle" onclick="toggleReadMore()" id="read-more-btn">
                                        Read Full Blog ↓
                                    </button>
                                </div>
                            </div>

                            <!-- Show Less Button (appears when content is expanded) -->
                            <div class="show-less-container">
                                <button class="btn-read-more-toggle" onclick="toggleReadMore()" id="show-less-btn">
                                    Show Less ↑
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Blog Info & Comments (col-md-4) -->
                    <div class="col-md-4 content-right">
                        <!-- Blog Information -->
                        <div class="mb-5">
                            <h3 class="content-title">Blog Information</h3>

                            <!-- 2x2 Grid Layout -->
                            <div class="blog-info-grid">
                                <div class="info-item">
                                    <span class="info-label">Publication Date</span>
                                    <span class="info-value">
                                        @if ($post->published_at)
                                            {{ \Carbon\Carbon::parse($post->published_at)->format('F d, Y') }}
                                        @else
                                            Not published
                                        @endif
                                    </span>
                                </div>

                                <div class="info-item">
                                    <span class="info-label">Category</span>
                                    <span class="info-value">
                                        {{ optional($post->category)->categoryName ?? 'Uncategorized' }}
                                    </span>
                                </div>

                                <div class="info-item">
                                    <span class="info-label">Reading Time</span>
                                    <span class="info-value">
                                        @php
                                            $wordCount = str_word_count(strip_tags($post->content));
                                            $readingTime = ceil($wordCount / 200);
                                            echo $readingTime . ' Min';
                                        @endphp
                                    </span>
                                </div>

                                <div class="info-item">
                                    <span class="info-label">Author Name</span>
                                    <span class="info-value">
                                        {{ optional($post->user)->name ?? 'Unknown' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Comments Section -->
                        <div>
                            <!-- Hardcoded Comments for testing -->
                            @include('partials.blog.comments', [
                                'post' => $post,
                                'profileRelation' => 'volunteerProfile',
                                'profileRoute' => 'volunteer.profile.show',
                                'profileStoragePath' => 'images/profiles/',
                            ])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    @include('layouts.volunteer_footer')

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Enhanced Read More/Less Functionality -->
    <script>
        function toggleReadMore() {
            const readMoreSection = document.getElementById('read-more-section');
            const readMoreBtn = document.getElementById('read-more-btn');
            const showLessContainer = document.querySelector('.show-less-container');

            if (readMoreSection.classList.contains('expanded')) {
                // Collapse the content
                readMoreSection.classList.remove('expanded');
                showLessContainer.style.display = 'none';

                // Scroll back to the position where read more was clicked
                setTimeout(() => {
                    readMoreSection.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }, 100);
            } else {
                // Expand the content
                readMoreSection.classList.add('expanded');
                showLessContainer.style.display = 'block';
            }
        }

        // Auto-expand if content is shorter than the max height
        document.addEventListener('DOMContentLoaded', function() {
            const readMoreSection = document.getElementById('read-more-section');
            const showLessContainer = document.querySelector('.show-less-container');
            const contentHeight = readMoreSection.scrollHeight;
            const maxHeight = 600; // Should match CSS max-height

            if (contentHeight <= maxHeight) {
                readMoreSection.classList.add('expanded');
                document.querySelector('.read-more-overlay').style.display = 'none';
                showLessContainer.style.display = 'block';
            }
        });

        // Handle responsive behavior
        function handleResize() {
            const readMoreSection = document.getElementById('read-more-section');
            const showLessContainer = document.querySelector('.show-less-container');
            const contentHeight = readMoreSection.scrollHeight;
            const maxHeight = 600;

            // On mobile, always show full content
            if (window.innerWidth < 768) {
                readMoreSection.classList.add('expanded');
                document.querySelector('.read-more-overlay').style.display = 'none';
                showLessContainer.style.display = 'block';
            } else if (contentHeight <= maxHeight) {
                readMoreSection.classList.add('expanded');
                document.querySelector('.read-more-overlay').style.display = 'none';
                showLessContainer.style.display = 'block';
            }
        }

        // Initial check and resize listener
        document.addEventListener('DOMContentLoaded', handleResize);
        window.addEventListener('resize', handleResize);
    </script>

    @stack('scripts')
</body>

</html>
