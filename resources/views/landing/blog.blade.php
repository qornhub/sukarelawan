<section class="landing-blog" id="blog">
    <div class="container">

        {{-- Section Header --}}
        <div class="landing-blog-header">
            <h2 class="landing-blog-title">Stories That Inspire Change</h2>
            <p class="landing-blog-subtitle">
                Real experiences shared by volunteers from our community.
            </p>
        </div>

        {{-- Blog Cards --}}
        @if($blogs->count() > 0)
            <div class="landing-blog-grid">
                @foreach ($blogs as $post)
                    @php
                        $img = $post->image
                            ? asset('images/Blog/' . $post->image)
                            : asset('images/Blog/default_blog.jpg');
                    @endphp

                    <article class="landing-blog-card">
                        <img src="{{ $img }}" alt="{{ $post->title }}">

                        <div class="landing-blog-content">
                            <p class="blog-meta">
                                by {{ optional($post->user)->name ?? 'Unknown' }}
                                ·
                                {{ \Carbon\Carbon::parse($post->published_at)->format('M d, Y') }}
                            </p>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <p class="text-muted text-center">No stories yet.</p>
        @endif

        {{-- CTA --}}
        <div class="landing-blog-cta">
            <a href="#login-section" class="landing-blog-btn">
                Share Your Story
            </a>
        </div>

    </div>
</section>


{{-- =========================
     Scoped CSS
========================= --}}
<style>
/* ===== Blog Section ===== */
.landing-blog {
    padding: 5rem 1.25rem;
    background: #ffffff;
}

.landing-blog .container {
    max-width: 1180px;
    margin: 0 auto;
}

/* ===== Header ===== */
.landing-blog-header {
    max-width: 700px;
    margin-bottom: 3rem;
}

.landing-blog-title {
    font-size: 2.1rem;
    font-weight: 800;
    margin-bottom: .75rem;
    color: #1a1a1a;
}

.landing-blog-subtitle {
    font-size: 1.05rem;
    color: #6c757d;
}

/* ===== Blog Grid ===== */
.landing-blog-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
}

/* ===== Blog Card ===== */
.landing-blog-card {
    background: #ffffff;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    transition: transform .3s ease, box-shadow .3s ease;
}

.landing-blog-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.12);
}

.landing-blog-card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

/* ===== Content ===== */
.landing-blog-content {
    padding: 1.25rem 1.5rem;
}

.landing-blog-content h3 {
    font-size: 1.1rem;
    font-weight: 700;
    margin-bottom: .4rem;
    color: #1a1a1a;
}

.blog-meta {
    font-size: .85rem;
    color: #999;
}

/* ===== CTA ===== */
.landing-blog-cta {
    text-align: center;
    margin-top: 3.5rem;
}

.landing-blog-btn {
    display: inline-block;
    padding: .8rem 1.6rem;
    font-weight: 700;
    border-radius: 10px;
    background: #0004ad;
    color: #ffffff;
    text-decoration: none;
    transition: transform .2s ease, box-shadow .2s ease;
}

.landing-blog-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 28px rgba(114, 9, 183, 0.35);
}

/* ===== Responsive ===== */
@media (max-width: 768px) {
    .landing-blog-title {
        font-size: 1.8rem;
    }

    .landing-blog-card img {
        height: 180px;
    }
}
</style>
