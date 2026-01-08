<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>SukaRelawan | Volunteer Platform</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    {{-- Font Awesome --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    {{-- Animate.css --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
     <link rel="stylesheet" href="{{ asset('css/landing_badges.css') }}">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #004aad 0%, #0066cc 100%);
            --secondary-gradient: linear-gradient(135deg, #ff6b6b 0%, #ff9a3c 100%);
        }

        /* ================= RESET & BASE ================= */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            background: #f8fafc;
            scroll-behavior: smooth;
            overflow-x: hidden;
        }

        img {
            max-width: 100%;
            height: auto;
            display: block;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        button {
            cursor: pointer;
            border: none;
            font-family: inherit;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* ================= HERO SECTION ================= */
        .hero {
            min-height: 100vh;
            background: linear-gradient(135deg, #004aad 0%, #0066cc 100%);
            color: white;
            display: flex;
            align-items: center;
            padding: 80px 0 60px;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 80%, rgba(108, 138, 236, 0.2) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 107, 107, 0.15) 0%, transparent 50%);
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 800px;
            margin: 0 auto;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(255, 255, 255, 0.15);
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 600;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }

        .hero-title {
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 20px;
        }

        .hero-title span {
            color: #ffd166;
            display: inline-block;
        }

        .hero-subtitle {
            font-size: 1.1rem;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.9);
            max-width: 600px;
            margin: 0 auto 40px;
        }

        .cta-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 60px;
        }

        /* ENHANCED GET STARTED BUTTON */
        .hero-cta {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 18px 40px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1.2rem;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            background: var(--secondary-gradient);
            color: white;
            box-shadow: 0 8px 30px rgba(255, 107, 107, 0.3);
            position: relative;
            overflow: hidden;
        }

        .hero-cta::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #ff9a3c 0%, #ff6b6b 100%);
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .hero-cta:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 15px 40px rgba(255, 107, 107, 0.4);
        }

        .hero-cta:hover::before {
            opacity: 1;
        }

        .hero-cta span {
            position: relative;
            z-index: 1;
        }

        .hero-cta i {
            position: relative;
            z-index: 1;
            transition: transform 0.4s ease;
        }

        .hero-cta:hover i {
            transform: translateX(8px);
        }

        .hero-stats {
            display: flex;
            justify-content: center;
            gap: 40px;
            flex-wrap: wrap;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            display: block;
            font-size: 2.5rem;
            font-weight: 700;
            color: #ffd166;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.8);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* ================= SHOWCASE SECTION ================= */
        .showcase {
            padding: 80px 0;
            background: #f8fafc;
        }

        .section-title {
            text-align: center;
            color: #004aad;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .section-subtitle {
            text-align: center;
            color: #666;
            max-width: 600px;
            margin: 0 auto 50px;
            font-size: 1.1rem;
            line-height: 1.6;
        }

        .showcase-slider {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
            position: relative;
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
            background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
            color: white;
            padding: 30px;
        }

        .slider-controls {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            margin-top: 30px;
        }

        .slider-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: white;
            border: 2px solid #004aad;
            color: #004aad;
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }

        .slider-btn:hover {
            background: #004aad;
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
            background: #ddd;
            border: none;
            transition: all 0.3s ease;
        }

        .slider-dot.active {
            background: #004aad;
            transform: scale(1.2);
        }

        /* ================= ROLES SECTION ================= */
        .roles {
            padding: 80px 0;
            background: white;
            position: relative;
            z-index: 1;
        }

        .roles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            max-width: 900px;
            margin: 0 auto;
        }

        .role-card {
            background: #fff;
            border-radius: 15px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 2px solid transparent;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .role-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            border-color: #004aad;
        }

        .role-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #004aad, #0066cc);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 25px;
            color: white;
            font-size: 2rem;
        }

        .role-card:hover .role-icon {
            transform: rotate(10deg) scale(1.1);
        }

        .role-card h3 {
            color: #004aad;
            font-size: 1.8rem;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .role-card p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 25px;
            flex-grow: 1;
        }

        .role-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 30px;
            background: #004aad;
            color: white;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .role-btn:hover {
            background: #003580;
            transform: translateX(5px);
            box-shadow: 0 10px 20px rgba(0, 74, 173, 0.3);
        }

        /* ================= FLOATING BUTTON - FIXED ================= */
        .floating-scroll-top {
            position: fixed;
  right: 20px;
  bottom: 24px;
  z-index: 9999;
  opacity: 0;
  visibility: hidden;
  transform: translateY(12px);
  transition: opacity .25s ease, transform .25s ease, visibility .25s;
  pointer-events: none; /* disable clicks while hidden */
        }

        .floating-scroll-top.show {
            opacity: 1;
  visibility: visible;
  transform: translateY(0);
  pointer-events: auto;
        }

        .scroll-top-btn {
             width: 48px;
  height: 48px;
  border-radius: 50%;
  border: none;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: #0d6efd;
  color: #fff;
  cursor: pointer;
  box-shadow: 0 6px 18px rgba(0,0,0,0.15);
  font-size: 16px;
        }

        .scroll-top-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.1);
            transform: scaleX(0);
            transform-origin: right;
            transition: transform 0.5s ease;
        }

        .scroll-top-btn:hover::before {
            transform: scaleX(1);
            transform-origin: left;
        }

        .scroll-top-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 74, 173, 0.6);
            animation: bounce 0.5s ease infinite alternate;
        }

        @keyframes bounce {
            from { transform: translateY(-5px); }
            to { transform: translateY(-10px); }
        }

        /* ================= UTILITIES ================= */
        .hidden {
            opacity: 0;
            transform: translateY(30px);
        }

        .visible {
            opacity: 1;
            transform: translateY(0);
            transition: all 0.8s ease;
        }

        /* ================= RESPONSIVE ================= */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1rem;
                padding: 0 20px;
            }
            
            .cta-group {
                flex-direction: column;
                align-items: center;
                gap: 15px;
            }
            
            .hero-cta {
                width: 280px;
                justify-content: center;
                padding: 16px 30px;
                font-size: 1.1rem;
            }
            
            .hero-stats {
                gap: 30px;
            }
            
            .stat-number {
                font-size: 2rem;
            }
            
            .slider-track {
                height: 300px;
            }
            
            .section-title {
                font-size: 2rem;
                padding: 0 20px;
            }
            
            .section-subtitle {
                padding: 0 20px;
            }
            
            .roles-grid {
                grid-template-columns: 1fr;
                max-width: 400px;
            }
            
            .role-card {
                padding: 30px 20px;
            }
            
            .floating-scroll-top {
                bottom: 20px;
                right: 20px;
            }
            
            .scroll-top-btn {
                width: 50px;
                height: 50px;
                font-size: 1.1rem;
            }
        }

        @media (max-width: 480px) {
            .hero-title {
                font-size: 2rem;
            }
            
            .slider-track {
                height: 250px;
            }
            
            .slider-btn {
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }
            
            .hero-stats {
                gap: 20px;
            }
            
            .stat-number {
                font-size: 1.8rem;
            }
            
            .stat-label {
                font-size: 0.8rem;
            }
            
            .hero-cta {
                width: 250px;
                padding: 14px 25px;
                font-size: 1rem;
            }
        }
    </style>
</head>

<body>

    @include('layouts.landing_header')

    <main>
        {{-- HERO SECTION --}}
        <section class="hero" id="hero">
            <div class="container">
                <div class="hero-content">
                    <div class="hero-badge animate__animated animate__fadeInDown">
                        <i class="fas fa-users"></i>
                        <span>Community-Powered Volunteering Platform</span>
                    </div>
                    
                    <h1 class="hero-title animate__animated animate__fadeInUp">
                        Ready to Make a Difference with <span>SukaRelawan</span>?
                    </h1>
                    
                    <p class="hero-subtitle animate__animated animate__fadeInUp animate__delay-1s">
                        Connect with meaningful causes, join impactful events, and build stronger communities 
                        through our all-in-one volunteer management platform.
                    </p>
                    
                    <div class="cta-group">
                        <button id="scrollToLogin" class="hero-cta animate__animated animate__fadeInUp animate__delay-2s">
                            <span>Get Started Now</span>
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        {{-- SHOWCASE SECTION --}}
        <section class="showcase" id="showcase">
            <div class="container">
                <h2 class="section-title animate__animated animate__fadeInUp">Community Impact in Action</h2>
                <p class="section-subtitle animate__animated animate__fadeInUp animate__delay-1s">
                    Discover recent events where volunteers made real differences in their communities.
                </p>
                
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
        <section class="roles" id="login-section">
            <div class="container">
                <h2 class="section-title animate__animated animate__fadeInUp">Get Started Based on Your Role</h2>
                <p class="section-subtitle animate__animated animate__fadeInUp animate__delay-1s">
                    Select your role to access tailored features 
                </p>
                
                <div class="roles-grid">
                    <!-- Volunteer Card -->
                    <div class="role-card animate__animated animate__fadeInUp">
                        <div class="role-icon">
                            <i class="fas fa-hands-helping"></i>
                        </div>
                        <h3>Volunteer</h3>
                        <p>Discover events, earn rewards, and make a real impact in your community. Track your volunteering journey and connect with like-minded individuals.</p>
                        <a href="{{ route('login.volunteer') }}" class="role-btn">
                            <span>Login as Volunteer</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    
                    <!-- NGO Card -->
                    <div class="role-card animate__animated animate__fadeInUp animate__delay-1s">
                        <div class="role-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <h3>NGO / Organization</h3>
                        <p>Manage events, coordinate volunteers, and measure community impact with our powerful management tools. Streamline your operations and grow your reach.</p>
                        <a href="{{ route('login.ngo') }}" class="role-btn">
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
    <script src="https://unpkg.com/gsap@3/dist/gsap.min.js"></script>


    <script>
        // Simple image fallback function
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
            console.log('Page loaded - debugging scroll-to-top button');
            
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
                
                // Move the slider track
                sliderTrack.style.transform = `translateX(-${currentSlide * 100}%)`;
                
                // Update dots
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
            
            (function() {
  const scrollTopBtn = document.getElementById('scrollTopBtn');
  if (!scrollTopBtn) return;

  // If your app uses a custom scrollable container instead of the window,
  // set this selector to that container (e.g. '.main', '#app', etc.).
  // Leave as null to use the window/document scroll.
  const customScrollContainerSelector = null; // or '.main' if you have one

  const getScrollContainer = () => {
    if (customScrollContainerSelector) {
      return document.querySelector(customScrollContainerSelector);
    }
    // document.scrollingElement is the element that scrolls (html or body)
    return window;
  };

  const scroller = getScrollContainer();

  const getScrollY = () => {
    if (scroller === window) {
      return window.pageYOffset || document.documentElement.scrollTop || 0;
    } else {
      return scroller.scrollTop;
    }
  };

  function handleScroll() {
    const y = getScrollY();
    // lower threshold for easier testing
    const SHOW_AT = 200;
    // debug:
    // console.log('Scroll position:', y);
    if (y > SHOW_AT) {
      scrollTopBtn.classList.add('show');
    } else {
      scrollTopBtn.classList.remove('show');
    }
  }

  // Attach listener to the correct element
  if (scroller === window) {
    window.addEventListener('scroll', handleScroll, { passive: true });
  } else {
    scroller.addEventListener('scroll', handleScroll, { passive: true });
  }

  // initial check
  handleScroll();

  // click handler (use GSAP if you have ScrollToPlugin, otherwise fallback)
  const scrollTopButton = scrollTopBtn.querySelector('.scroll-top-btn');
  if (scrollTopButton) {
    scrollTopButton.addEventListener('click', function() {
      if (scroller === window) {
        window.scrollTo({ top: 0, behavior: 'smooth' });
      } else {
        scroller.scrollTo({ top: 0, behavior: 'smooth' });
      }
    });

    // keyboard support for accessibility
    scrollTopButton.addEventListener('keydown', function(e) {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        scrollTopButton.click();
      }
    });
  }
})();

            
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
                
                gsap.from('.hero-title', {
                    duration: 1.2,
                    y: 40,
                    opacity: 0,
                    delay: 0.2,
                    ease: "power3.out"
                });
                
                gsap.from('.hero-subtitle', {
                    duration: 1,
                    y: 30,
                    opacity: 0,
                    delay: 0.4,
                    ease: "power3.out"
                });
                
                gsap.from('.hero-cta', {
                    duration: 0.8,
                    y: 20,
                    opacity: 0,
                    delay: 0.6,
                    ease: "back.out(1.2)"
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
                
                // Parallax effect for hero
                gsap.to('.hero', {
                    scrollTrigger: {
                        trigger: '.hero',
                        start: "top top",
                        end: "bottom top",
                        scrub: true
                    },
                    y: 100,
                    ease: "none"
                });
            }
        });
    </script>

    <script>
document.addEventListener('DOMContentLoaded', function () {
    if (!location.hash) return;

    const target = document.querySelector(location.hash);
    if (target) {
        setTimeout(() => {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }, 100);
    }
});
</script>
@stack('scripts')

</body>
</html>