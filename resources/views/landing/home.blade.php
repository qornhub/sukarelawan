<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>SukaRelawan | Volunteer Platform</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SukaRelawan — Connect with meaningful causes, join impactful events, and build stronger communities through our volunteer platform.">
    
    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    {{-- Font Awesome --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    {{-- Animate.css --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="{{ asset('css/landing_badges.css') }}">
    
    <style>
    /* ===== CSS Variables ===== */
    :root {
        --primary: #004aad;
        --primary-light: #0066cc;
        --secondary: #00b4d8;
        --accent: #10b981;
        --light: #f8fafc;
        --dark: #002855;
        --text: #334155;
        --text-light: #64748b;
        --border: #e2e8f0;
        --card-bg: #ffffff;
        --radius: 16px;
        --shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        --shadow-lg: 0 10px 40px rgba(0, 0, 0, 0.12);
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* ===== Reset & Base Styles ===== */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    html {
        scroll-behavior: smooth;
    }

    body {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        color: var(--text);
        line-height: 1.6;
        background: var(--light);
        overflow-x: hidden;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 24px;
    }

    /* ===== Typography ===== */
    h1, h2, h3, h4, h5, h6 {
        font-weight: 700;
        line-height: 1.2;
        color: var(--dark);
    }

    h1 {
        font-size: 3.5rem;
        margin-bottom: 1.5rem;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    h2 {
        font-size: 2.5rem;
        margin-bottom: 1.5rem;
        position: relative;
        display: inline-block;
    }

    h2:after {
        content: '';
        position: absolute;
        bottom: -8px;
        left: 0;
        width: 60px;
        height: 4px;
        background: linear-gradient(90deg, var(--secondary), var(--primary));
        border-radius: 2px;
    }

    h3 {
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }

    p {
        margin-bottom: 1.5rem;
        color: var(--text);
        line-height: 1.7;
    }

    .text-lead {
        font-size: 1.125rem;
        color: var(--text-light);
        font-weight: 500;
        line-height: 1.8;
    }

    .text-center {
        text-align: center;
    }

    .text-center h2:after {
        left: 50%;
        transform: translateX(-50%);
    }

    /* ===== Hero Section ===== */
    .home-hero {
        padding: 8rem 0 6rem;
        background: linear-gradient(135deg, 
            rgba(0, 74, 173, 0.05) 0%, 
            rgba(0, 180, 216, 0.03) 100%);
        position: relative;
        overflow: hidden;
        min-height: 100vh;
        display: flex;
        align-items: center;
    }

    .hero-content {
        max-width: 800px;
        text-align: center;
        margin: 0 auto;
        position: relative;
        z-index: 2;
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: rgba(0, 74, 173, 0.1);
        padding: 12px 24px;
        border-radius: 50px;
        font-weight: 600;
        margin-bottom: 2rem;
        border: 1px solid rgba(0, 74, 173, 0.2);
        backdrop-filter: blur(10px);
        color: var(--primary);
    }

    .hero-stats {
        display: flex;
        justify-content: center;
        gap: 2rem;
        margin-top: 4rem;
        flex-wrap: wrap;
    }

    .stat-card {
        background: var(--card-bg);
        padding: 2rem 2.5rem;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        text-align: center;
        min-width: 180px;
        transition: var(--transition);
        border: 1px solid var(--border);
        position: relative;
        overflow: hidden;
    }

    .stat-card:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(to bottom, var(--secondary), var(--primary));
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-lg);
        border-color: var(--secondary);
    }

    .stat-card:hover:before {
        opacity: 1;
    }

    .stat-number {
        font-size: 2.75rem;
        font-weight: 800;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        display: block;
        line-height: 1;
        margin-bottom: 0.5rem;
    }

    .stat-label {
        font-size: 0.875rem;
        color: var(--text-light);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }

    /* ===== Buttons ===== */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 2.5rem;
        font-weight: 600;
        text-decoration: none;
        border-radius: 12px;
        transition: var(--transition);
        border: 2px solid transparent;
        cursor: pointer;
        font-size: 1.125rem;
        position: relative;
        z-index: 1;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        color: white;
        width: 100%;
        max-width: 255px;
        box-shadow: 0 4px 15px rgba(0, 74, 173, 0.2);
    }

    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 74, 173, 0.3);
        background: linear-gradient(135deg, var(--primary-light), var(--primary));
    }

    .btn-secondary {
        background: transparent;
        color: var(--primary);
        border-color: var(--border);
    }

    .btn-secondary:hover {
        background: rgba(0, 74, 173, 0.05);
        border-color: var(--primary);
        transform: translateY(-3px);
    }

    .btn-group {
        display: flex;
        gap: 1.5rem;
        justify-content: center;
        flex-wrap: wrap;
        margin-top: 3rem;
    }

    /* ===== Showcase Section ===== */
    .showcase-section {
        padding: 6rem 0;
        background: var(--card-bg);
    }

    .showcase-slider {
        background: var(--card-bg);
        border-radius: var(--radius);
        overflow: hidden;
        box-shadow: var(--shadow);
        border: 1px solid var(--border);
        transition: var(--transition);
        max-width: 900px;
        margin: 3rem auto 0;
    }

    .showcase-slider:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
    }

    .slider-track {
        display: flex;
        transition: transform 0.5s ease;
        height: 500px;
    }

    .slider-slide {
        min-width: 100%;
        height: 100%;
        flex-shrink: 0;
        position: relative;
    }

    .slider-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .slide-caption {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(0, 42, 85, 0.9), transparent);
        color: white;
        padding: 2rem;
    }

    /* Add these rules to ensure text is white */
.slide-caption h3,
.slide-caption p {
    color: white !important; /* Using !important to override any other styles */
}

.slide-caption h3 {
    margin-bottom: 0.5rem;
    font-size: 1.5rem;
    font-weight: 700;
}

.slide-caption p {
    margin-bottom: 0;
    font-size: 1rem;
    opacity: 0.9; /* Slightly transparent for better contrast */
}

    .slider-controls {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 20px;
        margin-top: 2rem;
    }

    .slider-btn {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: var(--card-bg);
        border: 2px solid var(--primary);
        color: var(--primary);
        font-size: 1.2rem;
        transition: var(--transition);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .slider-btn:hover {
        background: var(--primary);
        color: white;
        transform: scale(1.1);
    }

    .slider-dots {
        display: flex;
        gap: 10px;
    }

    .slider-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: var(--border);
        border: none;
        transition: var(--transition);
        cursor: pointer;
    }

    .slider-dot.active {
        background: var(--primary);
        transform: scale(1.2);
    }

    /* ===== Roles Section ===== */
    .roles-section {
        padding: 6rem 0;
        background: linear-gradient(135deg, 
            rgba(0, 180, 216, 0.05) 0%, 
            rgba(0, 74, 173, 0.03) 100%);
    }

    .roles-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 2.5rem;
        margin-top: 3rem;
        max-width: 900px;
        margin-left: auto;
        margin-right: auto;
    }

    .role-card {
        background: var(--card-bg);
        padding: 3rem;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        transition: var(--transition);
        border: 1px solid var(--border);
        position: relative;
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .role-card:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(to bottom, var(--secondary), var(--primary));
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .role-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-lg);
        border-color: var(--secondary);
    }

    .role-card:hover:before {
        opacity: 1;
    }

    .role-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 2rem;
        color: white;
        font-size: 2rem;
        transition: var(--transition);
    }

    .role-card:hover .role-icon {
        transform: scale(1.1) rotate(5deg);
    }

    .role-card h3 {
        font-size: 1.5rem;
        margin-bottom: 1rem;
        color: var(--dark);
    }

    .role-card p {
        color: var(--text-light);
        flex-grow: 1;
        margin-bottom: 2rem;
    }

    /* ===== Floating Scroll Button ===== */
    .floating-scroll-top {
        position: fixed;
        right: 24px;
        bottom: 24px;
        z-index: 9999;
        opacity: 0;
        visibility: hidden;
        transform: translateY(12px);
        transition: opacity 0.25s ease, transform 0.25s ease, visibility 0.25s;
        pointer-events: none;
    }

    .floating-scroll-top.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
        pointer-events: auto;
    }

    .scroll-top-btn {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        color: white;
        cursor: pointer;
        box-shadow: var(--shadow-lg);
        font-size: 1.25rem;
        transition: var(--transition);
    }

    .scroll-top-btn:hover {
        transform: translateY(-5px) scale(1.1);
        box-shadow: 0 15px 30px rgba(0, 74, 173, 0.3);
    }

    /* ===== Decorative Elements ===== */
    .floating-element {
        position: absolute;
        border-radius: 50%;
        background: linear-gradient(135deg, 
            rgba(0, 180, 216, 0.1) 0%, 
            rgba(0, 74, 173, 0.05) 100%);
        pointer-events: none;
        z-index: 1;
    }
    .hero-cta-btn {
    /* This ensures the button is only as wide as its content */
    width: auto;
    display: inline-flex; /* Already set by .btn class */
}


    /* ===== Responsive Design ===== */
    @media (max-width: 992px) {
        h1 {
            font-size: 2.75rem;
        }
        
        h2 {
            font-size: 2.25rem;
        }
        
        .roles-grid {
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        }
        
        .slider-track {
            height: 400px;
        }
    }

    @media (max-width: 768px) {
        .container {
            padding: 0 20px;
        }
        
        .home-hero {
            padding: 6rem 0 4rem;
            min-height: auto;
        }
         .hero-cta-btn {
        width: auto !important;
        max-width: none !important;
    }
    
    .home-hero .btn-group {
        /* If you want the button group to not stretch full width */
        width: auto;
        display: inline-flex;
    }
        
        .showcase-section,
        .roles-section {
            padding: 4rem 0;
        }
        
        h1 {
            font-size: 2.5rem;
        }
        
        h2 {
            font-size: 2rem;
        }
        
        .hero-stats {
            gap: 1.5rem;
        }
        
        .stat-card {
            min-width: 160px;
            padding: 1.75rem 2rem;
        }
        
        .stat-number {
            font-size: 2.25rem;
        }
        
        .role-card {
            padding: 2rem;
        }
        
        .btn-group {
            flex-direction: column;
            align-items: center;
        }
        
        .btn {
            width: 100%;
            max-width: 300px;
            justify-content: center;
        }
        
        .slider-track {
            height: 350px;
        }
    }

    @media (max-width: 480px) {
        h1 {
            font-size: 2.25rem;
        }
        
        h2 {
            font-size: 1.75rem;
        }
        
        .hero-stats {
            flex-direction: column;
            align-items: center;
        }
        
        .stat-card {
            width: 100%;
            max-width: 280px;
        }
        
        .roles-grid {
            grid-template-columns: 1fr;
        }
        
        .role-card {
            padding: 1.75rem;
        }
        
        .btn {
            padding: 0.875rem 2rem;
        }
        
        .slider-track {
            height: 250px;
        }
        
        .floating-scroll-top {
            right: 20px;
            bottom: 20px;
        }
        
        .scroll-top-btn {
            width: 48px;
            height: 48px;
        }
    }

    /* ✅ FIX role buttons not showing */
.role-card a.btn {
    margin-top: auto;
    display: inline-flex !important;
    opacity: 1 !important;
    visibility: visible !important;
    position: relative;
    z-index: 10;
}

.role-card {
    overflow: visible !important;
}

.role-card a.btn.btn-primary,
.role-card a.btn.btn-secondary{
    width: 100%;
    max-width: 280px;
    justify-content: center;
}

    </style>
</head>

<body>

    @include('layouts.landing_header')

    <main>
        {{-- HERO SECTION --}}
        <section class="home-hero" id="hero">
            <div class="container">
                <div class="hero-content">
                    <div class="hero-badge animate__animated animate__fadeInDown">
                        <i class="fas fa-users"></i>
                        <span>Community-Powered Volunteering Platform</span>
                    </div>
                    
                    <h1 class="animate__animated animate__fadeInUp">
                        Ready to Make a Difference with SukaRelawan?
                    </h1>
                    
                    <p class="text-lead animate__animated animate__fadeInUp animate__delay-1s">
                        Connect with meaningful causes, join impactful events, and build stronger communities 
                        through our all-in-one volunteer management platform.
                    </p>
                    
                    <div class="btn-group">
                       <button id="scrollToLogin" class="btn btn-primary hero-cta-btn animate__animated animate__fadeInUp animate__delay-2s">
    <span>Get Started Now</span>
    <i class="fas fa-arrow-right"></i>
</button>
                    </div>
                    
                   
                </div>
            </div>
            
            <!-- Floating Elements -->
            <div class="floating-element" style="width: 300px; height: 300px; top: 10%; left: 5%;"></div>
            <div class="floating-element" style="width: 200px; height: 200px; bottom: 20%; right: 10%;"></div>
        </section>

        {{-- SHOWCASE SECTION --}}
        <section class="showcase-section" id="showcase">
            <div class="container">
                <div class="text-center">
                    <h2>Community Impact in Action</h2>
                    <p class="text-lead" style="max-width: 800px; margin: 0 auto;">
                        Discover recent events where volunteers made real differences in their communities.
                    </p>
                </div>
                
                <div class="showcase-slider">
                    <div class="slider-track" id="sliderTrack">
                        <!-- Slide 1 -->
                        <div class="slider-slide">
                            <img src="{{ asset('assets/event1.jpg') }}" 
                                 alt="Community Event" 
                                 class="slider-image"
                                 onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1545235617-9465d2a55698?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80';">
                            <div class="slide-caption">
                                <h3>Community Cleanup Event</h3>
                                <p>Join us in keeping our environment clean and green</p>
                            </div>
                        </div>
                        
                        <!-- Slide 2 -->
                        <div class="slider-slide">
                            <img src="{{ asset('assets/event2.jpg') }}" 
                                 alt="Volunteer Activity" 
                                 class="slider-image"
                                 onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1529156069898-49953e39b3ac?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80';">
                            <div class="slide-caption">
                                <h3>Educational Workshop</h3>
                                <p>Empowering youth through knowledge and skills</p>
                            </div>
                        </div>
                        
                        <!-- Slide 3 -->
                        <div class="slider-slide">
                            <img src="{{ asset('assets/event3.jpg') }}" 
                                 alt="Team Building" 
                                 class="slider-image"
                                 onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1532635241-17e820acc59f?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80';">
                            <div class="slide-caption">
                                <h3>Food Distribution Program</h3>
                                <p>Helping communities in need with essential supplies</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="slider-controls">
                        <button class="slider-btn" id="prevSlide">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        
                        <div class="slider-dots" id="sliderDots">
                            <button class="slider-dot active" data-slide="0"></button>
                            <button class="slider-dot" data-slide="1"></button>
                            <button class="slider-dot" data-slide="2"></button>
                        </div>
                        
                        <button class="slider-btn" id="nextSlide">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        @include('landing.badges')
        @include('landing.blog')

        {{-- ROLES SECTION --}}
        <section class="roles-section" id="login-section">
            <div class="container">
                <div class="text-center">
                    <h2>Get Started Based on Your Role</h2>
                    <p class="text-lead" style="max-width: 800px; margin: 0 auto;">
                        Select your role to access tailored features and personalized dashboard.
                    </p>
                </div>
                
                <div class="roles-grid">
                    <!-- Volunteer Card -->
                    <div class="role-card">
                        <div class="role-icon">
                            <i class="fas fa-hands-helping"></i>
                        </div>
                        <h3>Volunteer</h3>
                        <p>Discover events, earn rewards, and make a real impact in your community. Track your volunteering journey and connect with like-minded individuals.</p>
                        <a href="{{ route('login.volunteer') }}" class="btn btn-primary">
                            <span>Login as Volunteer</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    
                    <!-- NGO Card -->
                    <div class="role-card">
                        <div class="role-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <h3>NGO / Organization</h3>
                        <p>Manage events, coordinate volunteers, and measure community impact with our powerful management tools. Streamline your operations and grow your reach.</p>
                        <a href="{{ route('login.ngo') }}" class="btn btn-primary">
                            <span>Login as NGO</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    {{-- FLOATING SCROLL TO TOP BUTTON --}}
    <div class="floating-scroll-top" id="scrollTopBtn">
        <button class="scroll-top-btn" aria-label="Scroll to top">
            <i class="fas fa-chevron-up"></i>
        </button>
    </div>

    @include('layouts.landing_footer')

    {{-- GSAP --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>

    <script>
        // Image fallback function
        function setImageFallback(element) {
            const fallbackImages = [
                'https://images.unsplash.com/photo-1545235617-9465d2a55698?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1532635241-17e820acc59f?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80'
            ];
            
            element.onerror = function() {
                const index = Math.floor(Math.random() * fallbackImages.length);
                this.src = fallbackImages[index];
            };
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Set up image fallbacks
            document.querySelectorAll('.slider-image').forEach(img => {
                setImageFallback(img);
            });

            // ===== SIMPLE SLIDER =====
            const sliderTrack = document.getElementById('sliderTrack');
            const slides = document.querySelectorAll('.slider-slide');
            const prevBtn = document.getElementById('prevSlide');
            const nextBtn = document.getElementById('nextSlide');
            const dots = document.querySelectorAll('.slider-dot');
            
            let currentSlide = 0;
            const totalSlides = slides.length;
            
            function updateSlider() {
                if (!sliderTrack) return;
                
                sliderTrack.style.transform = `translateX(-${currentSlide * 100}%)`;
                
                dots.forEach((dot, index) => {
                    dot.classList.toggle('active', index === currentSlide);
                });
            }
            
            function nextSlide() {
                currentSlide = (currentSlide + 1) % totalSlides;
                updateSlider();
            }
            
            function prevSlide() {
                currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
                updateSlider();
            }
            
            // Event listeners
            if (prevBtn) prevBtn.addEventListener('click', prevSlide);
            if (nextBtn) nextBtn.addEventListener('click', nextSlide);
            
            // Dot navigation
            dots.forEach(dot => {
                dot.addEventListener('click', function() {
                    currentSlide = parseInt(this.dataset.slide);
                    updateSlider();
                });
            });
            
            // Auto-slide every 5 seconds
            let slideInterval = setInterval(nextSlide, 5000);
            
            // Pause on hover
            if (sliderTrack) {
                sliderTrack.addEventListener('mouseenter', () => clearInterval(slideInterval));
                sliderTrack.addEventListener('mouseleave', () => {
                    clearInterval(slideInterval);
                    slideInterval = setInterval(nextSlide, 5000);
                });
            }
            
            // Initialize slider
            updateSlider();
            
            // ===== SMOOTH SCROLL =====
            const scrollToLoginBtn = document.getElementById('scrollToLogin');
            if (scrollToLoginBtn) {
                scrollToLoginBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const loginSection = document.getElementById('login-section');
                    if (loginSection) {
                        loginSection.scrollIntoView({ 
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            }
            
            // ===== SCROLL TO TOP BUTTON =====
            const scrollTopBtn = document.getElementById('scrollTopBtn');
            
            function handleScroll() {
                const y = window.pageYOffset || document.documentElement.scrollTop;
                
                if (y > 300) {
                    scrollTopBtn.classList.add('show');
                } else {
                    scrollTopBtn.classList.remove('show');
                }
            }
            
            window.addEventListener('scroll', handleScroll, { passive: true });
            
            // Click handler for scroll to top
            const scrollTopButton = scrollTopBtn.querySelector('.scroll-top-btn');
            if (scrollTopButton) {
                scrollTopButton.addEventListener('click', function() {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
            }
            
            // Initial check
            handleScroll();
            
            // ===== CARD HOVER ANIMATIONS =====
            document.querySelectorAll('.stat-card, .role-card, .showcase-slider').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-8px)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
            
            // ===== GSAP ANIMATIONS =====
            if (typeof gsap !== 'undefined') {
                gsap.registerPlugin(ScrollTrigger);
                
                // Hero animations
                gsap.from('.hero-badge', {
                    duration: 1,
                    y: -30,
                    opacity: 0,
                    ease: "power3.out"
                });
                
                gsap.from('h1', {
                    duration: 1.2,
                    y: 40,
                    opacity: 0,
                    delay: 0.2,
                    ease: "power3.out"
                });
                
                gsap.from('.text-lead', {
                    duration: 1,
                    y: 30,
                    opacity: 0,
                    delay: 0.4,
                    ease: "power3.out"
                });
                
                gsap.from('.btn', {
                    duration: 0.8,
                    y: 20,
                    opacity: 0,
                    delay: 0.6,
                    ease: "back.out(1.2)"
                });
                
                // Stat cards animation
                gsap.utils.toArray('.stat-card').forEach((card, i) => {
                    gsap.from(card, {
                        duration: 0.8,
                        y: 30,
                        opacity: 0,
                        delay: 0.8 + (i * 0.1),
                        ease: "power3.out"
                    });
                });
                
                // Role cards animation on scroll
                gsap.utils.toArray('.role-card').forEach((card, i) => {
                    gsap.from(card, {
                        scrollTrigger: {
                            trigger: card,
                            start: "top 80%",
                            end: "bottom 20%",
                            toggleActions: "play none none none"
                        },
                        y: 40,
                        opacity: 0,
                        duration: 0.8,
                        delay: i * 0.2,
                        ease: "power3.out"
                    });
                });
            }
            
            // Handle hash URLs for direct section access
            if (location.hash) {
                const target = document.querySelector(location.hash);
                if (target) {
                    setTimeout(() => {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }, 100);
                }
            }
        });
    </script>

    @stack('scripts')

</body>
</html>