<div class="landing-footer-component">


    <footer class="lfc-footer py-4" id="mainFooter">
        <div class="container">
            <div class="row align-items-center">
                <!-- Logo and Brand -->
                <div class="col-md-6 mb-4 mb-md-0">
                    <div class="lfc-brand-section animate__animated" data-animate="fadeInLeft">
                        <a href="{{ route('landing.home') }}" class="d-inline-flex align-items-center text-decoration-none">
                            <img src="{{ asset('images/sukarelawan_logo.png') }}" alt="SukaRelawan Logo" height="40" class="me-3">
                            <span class="h4 fw-bold text-primary mb-0">SukaRelawan</span>
                        </a>
                        <p class="text-muted mt-2 small">
                            Connecting volunteers with meaningful opportunities.
                        </p>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="col-md-6">
                    <div class="lfc-links-section animate__animated" data-animate="fadeInRight">
                        <div class="d-flex justify-content-md-end flex-wrap gap-3">
                            <a href="{{ route('landing.home') }}" class="lfc-link-item text-decoration-none">
                                Home
                            </a>
                            <a href="{{ route('landing.about') }}" class="lfc-link-item text-decoration-none">
                                About Us
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <div class="row mt-4">
                <div class="col-12">
                    <hr class="my-3">
                </div>
            </div>

            <!-- Copyright -->
            <div class="row">
                <div class="col-12">
                    <div class="text-center">
                        <p class="text-muted mb-0 small">
                            &copy; <span id="currentYear"></span> SukaRelawan. All rights reserved.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back to Top Button -->
        <button class="lfc-back-to-top btn btn-primary p-3" id="backToTop" aria-label="Back to top">
            <i class="bi bi-arrow-up"></i>
        </button>
    </footer>

    <style>
    /* ===== Footer Base Styles ===== */
    .landing-footer-component {
        --lfc-primary: #004AAD;
        --lfc-primary-light: rgba(0, 74, 173, 0.1);
        --lfc-text: #333;
        --lfc-text-light: #6c757d;
        --lfc-bg: #f8f9fa;
        --lfc-border: #dee2e6;
        font-family: Inter, system-ui, -apple-system, sans-serif;
    }

    .lfc-footer {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        position: relative;
        overflow: hidden;
        box-shadow: 0 -2px 15px rgba(0, 0, 0, 0.03);
    }

    /* Decorative Top Border */
    .lfc-footer::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--lfc-primary), #3498db);
        opacity: 0.8;
    }

    /* Logo Animation Container */
    .lfc-brand-section {
        position: relative;
    }

    .lfc-brand-section a {
        transition: transform 0.3s ease;
    }

    .lfc-brand-section a:hover {
        transform: translateX(5px);
    }

    /* Link Items */
    .lfc-link-item {
        color: var(--lfc-text-light);
        font-weight: 500;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        opacity: 0;
        transform: translateY(10px);
    }

    .lfc-link-item:hover {
        color: var(--lfc-primary);
        background: var(--lfc-primary-light);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 74, 173, 0.1);
    }

    .lfc-link-item::after {
        content: '';
        position: absolute;
        left: 50%;
        bottom: 0;
        width: 0;
        height: 2px;
        background: var(--lfc-primary);
        transition: all 0.3s ease;
        transform: translateX(-50%);
    }

    .lfc-link-item:hover::after {
        width: 80%;
    }

    /* Back to Top Button */
    .lfc-back-to-top {
        position: fixed;
        bottom: 30px;
        right: 30px;
        border-radius: 50%;
        width: 56px;
        height: 56px;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        visibility: hidden;
        transform: translateY(20px) scale(0.8);
        transition: all 0.3s ease;
        z-index: 1000;
        box-shadow: 0 6px 20px rgba(0, 74, 173, 0.3);
    }

    .lfc-back-to-top.visible {
        opacity: 1;
        visibility: visible;
        transform: translateY(0) scale(1);
    }

    .lfc-back-to-top:hover {
        transform: translateY(-5px) scale(1.05);
        box-shadow: 0 10px 25px rgba(0, 74, 173, 0.4);
    }

    .lfc-back-to-top i {
        font-size: 1.2rem;
    }

    /* Divider */
    .lfc-footer hr {
        border: none;
        height: 1px;
        background: linear-gradient(90deg, transparent, var(--lfc-border), transparent);
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .lfc-footer {
            text-align: center;
        }
        
        .lfc-brand-section a {
            justify-content: center;
        }
        
        .lfc-links-section .d-flex {
            justify-content: center;
        }
        
        .lfc-back-to-top {
            bottom: 20px;
            right: 20px;
            width: 48px;
            height: 48px;
        }
    }

    @media (max-width: 576px) {
        .lfc-footer {
            padding: 2rem 0 !important;
        }
        
        .lfc-link-item {
            padding: 0.4rem 0.8rem;
        }
    }
    </style>

    <script>
    (function() {
        'use strict';
        
        // Set current year in copyright
        document.getElementById('currentYear').textContent = new Date().getFullYear();
        
        // Back to Top functionality
        const backToTopBtn = document.getElementById('backToTop');
        
        function toggleBackToTop() {
            if (window.pageYOffset > 300) {
                backToTopBtn.classList.add('visible');
            } else {
                backToTopBtn.classList.remove('visible');
            }
        }
        
        window.addEventListener('scroll', toggleBackToTop);
        
        backToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        
        // Initialize Animations
        document.addEventListener('DOMContentLoaded', function() {
            // GSAP Animations
            if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
                
                // Animate footer entrance
                gsap.from(".lfc-footer", {
                    scrollTrigger: {
                        trigger: ".lfc-footer",
                        start: "top bottom-=100",
                        toggleActions: "play none none reverse"
                    },
                    y: 30,
                    opacity: 0,
                    duration: 0.8,
                    ease: "power2.out"
                });
                
                // Animate logo section
                gsap.from(".lfc-brand-section", {
                    scrollTrigger: {
                        trigger: ".lfc-brand-section",
                        start: "top bottom-=50",
                        toggleActions: "play none none none"
                    },
                    x: -30,
                    opacity: 0,
                    duration: 0.6,
                    ease: "back.out(1.4)"
                });
                
                // Animate link items with stagger
                gsap.to(".lfc-link-item", {
                    scrollTrigger: {
                        trigger: ".lfc-links-section",
                        start: "top bottom-=50",
                        toggleActions: "play none none none"
                    },
                    opacity: 1,
                    y: 0,
                    stagger: 0.2,
                    duration: 0.5,
                    ease: "power2.out"
                });
                
                // Animate back to top button
                gsap.to(backToTopBtn, {
                    scrollTrigger: {
                        trigger: "body",
                        start: "300px top",
                        end: "bottom bottom",
                        toggleActions: "play reverse play reverse"
                    },
                    scale: 1,
                    opacity: 1,
                    y: 0,
                    duration: 0.4,
                    ease: "power2.out"
                });
                
                // Add hover animation to links
                document.querySelectorAll('.lfc-link-item').forEach(link => {
                    link.addEventListener('mouseenter', function() {
                        gsap.to(this, {
                            scale: 1.05,
                            duration: 0.2,
                            ease: "power2.out"
                        });
                    });
                    
                    link.addEventListener('mouseleave', function() {
                        gsap.to(this, {
                            scale: 1,
                            duration: 0.2,
                            ease: "power2.out"
                        });
                    });
                });
                
                // Add bounce animation to back to top button on hover
                backToTopBtn.addEventListener('mouseenter', function() {
                    gsap.to(this, {
                        scale: 1.1,
                        rotation: 5,
                        duration: 0.2,
                        ease: "back.out(1.7)"
                    });
                });
                
                backToTopBtn.addEventListener('mouseleave', function() {
                    gsap.to(this, {
                        scale: 1,
                        rotation: 0,
                        duration: 0.2,
                        ease: "power2.out"
                    });
                });
                
            } else {
                // Fallback: Use Animate.css if GSAP is not available
                const animatedElements = document.querySelectorAll('[data-animate]');
                
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const element = entry.target;
                            const animation = element.getAttribute('data-animate');
                            
                            element.classList.add('animate__animated', `animate__${animation}`);
                            observer.unobserve(element);
                        }
                    });
                }, {
                    threshold: 0.1
                });
                
                animatedElements.forEach(el => observer.observe(el));
                
                // Show link items with delay
                const linkItems = document.querySelectorAll('.lfc-link-item');
                linkItems.forEach((link, index) => {
                    setTimeout(() => {
                        link.style.opacity = '1';
                        link.style.transform = 'translateY(0)';
                    }, 300 + (index * 100));
                });
            }
        });
        
        // Smooth scroll for anchor links (Home and About Us)
        document.querySelectorAll('.lfc-link-item').forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href.startsWith('#')) {
                    e.preventDefault();
                    const targetElement = document.querySelector(href);
                    if (targetElement) {
                        // Close mobile menu if open (if exists)
                        const mobileMenu = document.querySelector('.lhc-mobile-menu.active');
                        if (mobileMenu) {
                            mobileMenu.classList.remove('active');
                        }
                        
                        // Smooth scroll to target
                        targetElement.scrollIntoView({
                            behavior: 'smooth'
                        });
                    }
                }
            });
        });
    })();
    </script>
</div>