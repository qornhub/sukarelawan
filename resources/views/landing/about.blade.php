<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>About — SukaRelawan | Empowering Volunteers</title>
  <meta name="description" content="SukaRelawan — a modern platform connecting volunteers and NGOs. Learn our mission, vision, and development journey." />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  
  {{-- Font Awesome --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  
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

    h4 {
        font-size: 1.25rem;
        margin-bottom: 0.75rem;
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

    .text-muted {
        color: var(--text-light);
    }

    .text-center {
        text-align: center;
    }

    .text-center h2:after {
        left: 50%;
        transform: translateX(-50%);
    }

    /* ===== Hero Section ===== */
    .about-hero {
        padding: 8rem 0 6rem;
        background: linear-gradient(135deg, 
            rgba(0, 74, 173, 0.05) 0%, 
            rgba(0, 180, 216, 0.03) 100%);
        position: relative;
        overflow: hidden;
    }

    .hero-content {
        max-width: 800px;
        text-align: center;
        margin: 0 auto;
        position: relative;
        z-index: 2;
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

    /* ===== Mission & Vision ===== */
    .mission-vision {
        padding: 6rem 0;
        background: var(--card-bg);
    }

    .mv-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 2.5rem;
        margin-top: 3rem;
    }

    .mv-card {
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
    }

    .mv-card:before {
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

    .mv-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-lg);
        border-color: var(--secondary);
    }

    .mv-card:hover:before {
        opacity: 1;
    }

    .mv-icon {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 2rem;
        color: white;
        font-size: 1.75rem;
        transition: var(--transition);
    }

    .mv-card:hover .mv-icon {
        transform: scale(1.1);
    }

    .mv-card h3 {
        font-size: 1.5rem;
        margin-bottom: 1rem;
        color: var(--dark);
    }

    .mv-card p {
        color: var(--text-light);
        flex-grow: 1;
        margin-bottom: 0;
    }

    /* ===== Vision Statement ===== */
    .vision-section {
        padding: 6rem 0;
        background: linear-gradient(135deg, var(--primary), var(--dark));
        position: relative;
        overflow: hidden;
    }

    .vision-section:before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        transform: translate(30%, -30%);
    }

    .vision-section:after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
        transform: translate(-30%, 30%);
    }

    .vision-container {
        max-width: 800px;
        margin: 0 auto;
        text-align: center;
        position: relative;
        z-index: 2;
    }

    .vision-container h2 {
        color: white;
        margin-bottom: 2rem;
    }

    .vision-container h2:after {
        background: var(--secondary);
        left: 50%;
        transform: translateX(-50%);
    }

    .vision-container p {
        color: rgba(255, 255, 255, 0.9);
        font-size: 1.25rem;
        line-height: 1.8;
        margin-bottom: 0;
    }

    /* ===== Values Section ===== */
    .values-section {
        padding: 6rem 0;
        background: var(--light);
    }

    .values-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 2rem;
        margin-top: 3rem;
    }

    .value-card {
        background: var(--card-bg);
        padding: 2.5rem;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        transition: var(--transition);
        border: 1px solid var(--border);
        text-align: center;
    }

    .value-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
        border-color: var(--secondary);
    }

    .value-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        color: white;
        font-size: 1.5rem;
        transition: var(--transition);
    }

    .value-card:hover .value-icon {
        transform: rotate(10deg) scale(1.1);
    }

    .value-card h4 {
        margin-bottom: 1rem;
    }

    .value-card p {
        color: var(--text-light);
        font-size: 0.95rem;
        margin-bottom: 0;
    }

    /* ===== CTA Section ===== */
    .cta-section {
        padding: 6rem 0;
        background: var(--card-bg);
        position: relative;
        overflow: hidden;
    }

    .cta-container {
        max-width: 800px;
        margin: 0 auto;
        text-align: center;
        position: relative;
        z-index: 2;
    }

    .cta-content {
        background: linear-gradient(135deg, var(--primary), var(--dark));
        padding: 4rem;
        border-radius: var(--radius);
        color: white;
        position: relative;
        overflow: hidden;
    }

    .cta-content:before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 400px;
        height: 400px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }

    .cta-content h3 {
        color: white;
        margin-bottom: 1.5rem;
        font-size: 2rem;
        position: relative;
        z-index: 1;
    }

    .cta-content p {
        color: rgba(255, 255, 255, 0.9);
        margin-bottom: 2.5rem;
        font-size: 1.125rem;
        position: relative;
        z-index: 1;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
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
        background: white;
        color: var(--primary);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .btn-primary:hover {
        background: rgb(230, 230, 230);
         color: var(--primary);
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .btn-secondary {
        background: transparent;
        color: white;
        border-color: rgba(255, 255, 255, 0.3);
    }

    .btn-secondary:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: white;
        transform: translateY(-3px);
    }

    .btn-group {
        display: flex;
        gap: 1.5rem;
        justify-content: center;
        flex-wrap: wrap;
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

    /* ===== Responsive Design ===== */
    @media (max-width: 992px) {
        h1 {
            font-size: 2.75rem;
        }
        
        h2 {
            font-size: 2.25rem;
        }
        
        .mv-grid {
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        }
        
        .values-grid {
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        }
        
        .cta-content {
            padding: 3rem 2rem;
        }
    }

    @media (max-width: 768px) {
        .container {
            padding: 0 20px;
        }
        
        .about-hero {
            padding: 6rem 0 4rem;
        }
        
        .mission-vision,
        .vision-section,
        .values-section,
        .cta-section {
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
        
        .mv-card,
        .value-card {
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
        
        .mv-grid,
        .values-grid {
            grid-template-columns: 1fr;
        }
        
        .mv-card,
        .value-card {
            padding: 1.75rem;
        }
        
        .cta-content {
            padding: 2.5rem 1.5rem;
        }
        
        .btn {
            padding: 0.875rem 2rem;
        }
    }
    .cta-content .btn {
  width: 280px;            /* fixed width you asked for */
  max-width: 100%;        /* don't overflow the container on tiny screens */
  margin: 0 auto;         /* center inside the cta-content */
  display: inline-flex !important;
  justify-content: center;
  padding: 0.9rem 1.25rem; /* slightly smaller padding so the button doesn't look too tall */
  font-size: 1rem;        /* slightly smaller text to match the narrower width */
  box-sizing: border-box;
}
/* ===== Floating Scroll Button (same as home) ===== */
.floating-scroll-top {
    position: fixed;
    right: 24px;
    bottom: 24px;
    z-index: 9999;                 /* high so footer / overlays don't cover it */
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
    box-shadow: 0 10px 40px rgba(0,0,0,0.12);
    font-size: 1.25rem;
    transition: all 0.25s ease;
}

.scroll-top-btn:hover {
    transform: translateY(-5px) scale(1.05);
    box-shadow: 0 15px 30px rgba(0,74,173,0.18);
}

/* smaller on very small screens */
@media (max-width: 480px) {
    .floating-scroll-top { right: 20px; bottom: 20px; }
    .scroll-top-btn { width: 48px; height: 48px; font-size: 1rem; }
}

  </style>
</head>
<body>
  @include('layouts.landing_header')

  <!-- Hero Section -->
  <section class="about-hero" id="hero">
    <div class="container">
      <div class="hero-content">
        <h1>We Are SukaRelawan</h1>
        <p class="text-lead">
          A modern web platform designed to make volunteering simple, meaningful, and measurable. 
          We connect passionate volunteers with NGOs, help organizers manage events efficiently, 
          and track real impact — all in one place.
        </p>
      </div>
    </div>
    
    <!-- Floating Elements -->
    <div class="floating-element" style="width: 300px; height: 300px; top: 10%; left: 5%;"></div>
    <div class="floating-element" style="width: 200px; height: 200px; bottom: 20%; right: 10%;"></div>
  </section>

  <!-- Mission & Vision -->
  <section class="mission-vision" id="mission">
    <div class="container">
      <div class="text-center">
        <h2>Our Mission</h2>
        <p class="text-lead" style="max-width: 800px; margin: 0 auto 2rem;">
          We're committed to transforming volunteerism through innovative technology, 
          making it accessible, rewarding, and impactful for everyone involved.
        </p>
      </div>
      
      <div class="mv-grid">
        <div class="mv-card">
          <div class="mv-icon">
            <i class="fas fa-handshake"></i>
          </div>
          <h3>Democratize Volunteering</h3>
          <p>
            Create a barrier-free platform where anyone can find volunteering opportunities 
            that match their time, skills, and passions. We believe everyone should have 
            the chance to contribute to their community.
          </p>
        </div>
        
        <div class="mv-card">
          <div class="mv-icon">
            <i class="fas fa-users-cog"></i>
          </div>
          <h3>Empower NGOs</h3>
          <p>
            Provide organizations with intuitive tools to post events, manage volunteers, 
            and measure outcomes — allowing them to focus on creating impact rather than 
            handling administration.
          </p>
        </div>
        
        <div class="mv-card">
          <div class="mv-icon">
            <i class="fas fa-award"></i>
          </div>
          <h3>Promote Trust & Recognition</h3>
          <p>
            Build trust through transparent tracking and acknowledge contributions with 
            digital badges, certificates, and detailed impact reports. Every hour of 
            service matters and should be recognized.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- Vision Section -->
  <section class="vision-section" id="vision">
    <div class="container">
      <div class="vision-container">
        <h2>Our Vision</h2>
        <p>
          We envision a future where volunteering is a natural part of community life — 
          accessible, rewarding, and aligned with personal growth. By connecting volunteers, 
          NGOs, and supporters through innovative technology, we aim to build stronger, 
          more resilient communities across Malaysia and beyond.
        </p>
      </div>
    </div>
  </section>

  <!-- Core Features Section -->
<section class="values-section" id="features">
  <div class="container">
    <div class="text-center">
      <h2>Our Core Features</h2>
      <p class="text-lead" style="max-width: 800px; margin: 0 auto 2rem;">
        SukaRelawan is built with purpose-driven features that simplify volunteer coordination, 
        empower NGOs, and create meaningful engagement for every participant.
      </p>
    </div>
    
    <div class="values-grid">
      
      <!-- Event Discovery -->
      <div class="value-card">
        <div class="value-icon">
          <i class="fas fa-search-location"></i>
        </div>
        <h4>Smart Event Discovery</h4>
        <p>
          Volunteers can easily explore and join events based on location, interests, 
          skills, and availability — ensuring every opportunity feels relevant and impactful.
        </p>
      </div>
      
      <!-- Event Management -->
      <div class="value-card">
        <div class="value-icon">
          <i class="fas fa-calendar-check"></i>
        </div>
        <h4>Event & Volunteer Management</h4>
        <p>
          NGOs can create events, manage registrations, track participation, and monitor 
          volunteer progress through an intuitive dashboard — all in one place.
        </p>
      </div>
      
      <!-- Task Assignment -->
      <div class="value-card">
        <div class="value-icon">
          <i class="fas fa-tasks"></i>
        </div>
        <h4>Task Assignment & Coordination</h4>
        <p>
          Organizers can assign tasks, define roles, and communicate expectations clearly, 
          helping volunteers understand their responsibilities before and during events.
        </p>
      </div>
      
      <!-- QR Attendance -->
      <div class="value-card">
        <div class="value-icon">
          <i class="fas fa-qrcode"></i>
        </div>
        <h4>QR Code Attendance Tracking</h4>
        <p>
          Attendance is verified using QR code scanning, providing accurate participation 
          records while reducing manual work and ensuring transparency.
        </p>
      </div>
      
      <!-- Community -->
      <div class="value-card">
        <div class="value-icon">
          <i class="fas fa-comments"></i>
        </div>
        <h4>Community Engagement</h4>
        <p>
          Built-in community features such as blogs, comments, and shared stories allow 
          volunteers to reflect, connect, and inspire others through real experiences.
        </p>
      </div>
      
      <!-- Rewards -->
      <div class="value-card">
        <div class="value-icon">
          <i class="fas fa-award"></i>
        </div>
        <h4>Rewards & Recognition</h4>
        <p>
          Volunteers earn digital badges, points, and certificates based on participation, 
          encouraging long-term engagement and recognizing meaningful contributions.
        </p>
      </div>
      
    </div>
  </div>
</section>


  <!-- CTA Section -->
  <section class="cta-section" id="join">
    <div class="container">
      <div class="cta-container">
        <div class="cta-content">
          <h3>Ready to Make a Difference?</h3>
          <p>
            Join thousands of volunteers and organizations creating real impact 
            in communities across the region. Whether you want to volunteer or 
            need volunteers for your cause, we're here to help.
          </p>
          
          <div class="btn-group">
            <a href="{{ url('/') }}#login-section" class="btn btn-primary">
    <span>Get Started</span>
    <i class="fas fa-arrow-right"></i>
  </a>
          </div>
        </div>
      </div>
    </div>
  </section>

  @include('layouts.landing_footer')
<!-- FLOATING SCROLL TO TOP BUTTON -->
<div class="floating-scroll-top" id="scrollTopBtn" aria-hidden="true">
  <button class="scroll-top-btn" aria-label="Scroll to top">
    <i class="fas fa-chevron-up" aria-hidden="true"></i>
  </button>
</div>

  <script>
    // Simple hover animations for cards
    document.querySelectorAll('.mv-card, .stat-card, .value-card').forEach(card => {
      card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-8px)';
      });
      
      card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
      });
    });

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const targetId = this.getAttribute('href');
        if(targetId === '#') return;
        
        const targetElement = document.querySelector(targetId);
        if(targetElement) {
          window.scrollTo({
            top: targetElement.offsetTop - 80,
            behavior: 'smooth'
          });
        }
      });
    });

    // Add current year to footer if needed
    document.addEventListener('DOMContentLoaded', function() {
      const yearElements = document.querySelectorAll('[data-year]');
      yearElements.forEach(el => {
        el.textContent = new Date().getFullYear();
      });
    });
  </script>

  <script>
  (function() {
    document.addEventListener('DOMContentLoaded', function () {
      const scrollTopBtn = document.getElementById('scrollTopBtn');
      if (!scrollTopBtn) return;

      function handleScroll() {
        const y = window.pageYOffset || document.documentElement.scrollTop;
        if (y > 300) {
          scrollTopBtn.classList.add('show');
        } else {
          scrollTopBtn.classList.remove('show');
        }
      }

      // Use passive listener for performance
      window.addEventListener('scroll', handleScroll, { passive: true });

      // Click to scroll to top
      const button = scrollTopBtn.querySelector('.scroll-top-btn');
      if (button) {
        button.addEventListener('click', function () {
          window.scrollTo({ top: 0, behavior: 'smooth' });
        });
      }

      // initial state
      handleScroll();

      // Optional: if you have hash navigation on load, keep it
      if (location.hash) {
        const target = document.querySelector(location.hash);
        if (target) {
          setTimeout(() => {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }, 100);
        }
      }
    });
  })();
</script>

</body>
</html>