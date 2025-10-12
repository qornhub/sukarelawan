<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>{{ $post->title }} - Blog</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/blogs/show.css') }}">

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



            <div class="register-row text-end mb-3">
                <div class="d-flex justify-content-end mt-1">
                    <!-- Equal-width buttons container -->
                    <div class="d-flex gap-2 flex-fill" style="max-width: 320px;">

                        <!-- Edit Blog (link styled as button) -->
                        <a href="{{ route('volunteer.blogs.edit', $post->blogPost_id) }}"
                            class="btn btn-warning flex-fill d-flex justify-content-center align-items-center"
                            style="height: 42px;">
                            <i class="fas fa-edit me-1"></i> Edit Blog
                        </a>

                        <!-- Delete Blog (form button) -->
                        <form action="{{ route('volunteer.blogs.destroy', $post->blogPost_id) }}" method="POST"
                            onsubmit="return confirm('Are you sure you want to delete this blog? This action cannot be undone.');"
                            class="flex-fill">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="btn btn-danger flex-fill d-flex justify-content-center align-items-center"
                                style="height: 42px;">
                                <i class="fas fa-trash-alt me-1"></i> Delete Blog
                            </button>
                        </form>
                    </div>
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
