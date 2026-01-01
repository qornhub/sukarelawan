<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>{{ $post->title }} - Blog</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Font Awesome v6 CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/blogs/show.css') }}">
    <link rel="stylesheet" href="{{ asset('css/blogs/comment.css') }}">

</head>

<body>

    @include('layouts.volunteer_header')

    <!-- Blog Header with Background Image in HTML -->
    <section class="blog-header">
        <img src="{{ $post->image ? asset('images/Blog/' . $post->image) : asset('assets/default_blog.jpg') }}"
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
                                        {{ $post->custom_category ? $post->custom_category : optional($post->category)->categoryName ?? 'Uncategorized' }}
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
    @stack('scripts')
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

                // Scroll back to top of content
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

        // Check content height and adjust buttons visibility
        function adjustReadMoreUI() {
            const readMoreSection = document.getElementById('read-more-section');
            const readMoreOverlay = document.querySelector('.read-more-overlay');
            const showLessContainer = document.querySelector('.show-less-container');

            if (!readMoreSection || !readMoreOverlay || !showLessContainer) return;

            const contentHeight = readMoreSection.scrollHeight;
            const maxHeight = 600; // Match your CSS limit

            if (window.innerWidth < 768) {
                // Mobile: always show full content, hide buttons
                readMoreSection.classList.add('expanded');
                readMoreOverlay.style.display = 'none';
                showLessContainer.style.display = 'none';
                return;
            }

            if (contentHeight <= maxHeight) {
                // Content is short → fully visible, hide both buttons
                readMoreSection.classList.add('expanded');
                readMoreOverlay.style.display = 'none';
                showLessContainer.style.display = 'none'; // ✅ fixed here
            } else {
                // Content is long → show "Read More", hide "Show Less"
                readMoreSection.classList.remove('expanded');
                readMoreOverlay.style.display = 'block';
                showLessContainer.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', adjustReadMoreUI);
        window.addEventListener('resize', adjustReadMoreUI);
    </script>



</body>

</html>
