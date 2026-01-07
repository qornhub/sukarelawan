<div class="landing-header-component">
    {{-- =========================
         LANDING PAGE HEADER PARTIAL
         - Place this partial inside your landing page (not a full HTML file)
         - Uses: route('landing.home'), route('landing.about'), route('login')
         ========================= --}}

    <header class="lhc-header" role="banner">
        <div class="lhc-inner">
            <!-- Logo -->
            <a href="{{ route('landing.home') }}" class="lhc-logo" aria-label="SukaRelawan home">
                <img src="{{ asset('assets/sukarelawan_logo.png') }}" alt="SukaRelawan logo" />
                <span class="lhc-brand">SukaRelawan</span>
            </a>

            <!-- Desktop Nav -->
            <nav class="lhc-nav" role="navigation" aria-label="Main navigation">
                <a href="{{ route('landing.home') }}"
                   class="lhc-nav-link {{ request()->routeIs('landing.home') ? 'active' : '' }}">
                    Home
                </a>

                <a href="{{ route('landing.about') }}"
                   class="lhc-nav-link {{ request()->routeIs('landing.about') ? 'active' : '' }}">
                    About Us
                </a>
            </nav>

            <!-- CTA - FIXED: Added closing tag and button text -->
            <div class="lhc-cta">
               <a href="{{ route('landing.home') }}#login-section"
   class="lhc-btn lhc-btn-primary lhc-btn-cta">
    <span>Get Started</span>
    <i class="fas fa-arrow-right"></i>
</a>

            </div>

            <!-- Mobile Menu Toggle -->
            <button id="lhcMenuBtn"
                    class="lhc-menu-btn"
                    aria-controls="lhcMobileMenu"
                    aria-expanded="false"
                    aria-label="Toggle navigation">
                <span class="lhc-icon" aria-hidden="true"><i class="fas fa-bars"></i></span>
            </button>
        </div>
    </header>

    <!-- Mobile Menu (hidden by default) -->
    <div id="lhcMobileMenu" class="lhc-mobile-menu" role="dialog" aria-modal="true" aria-hidden="true">
        <div class="lhc-mobile-inner" role="document">
            <button id="lhcMobileClose" class="lhc-mobile-close" aria-label="Close menu">
                <i class="fas fa-times"></i>
            </button>

            <nav class="lhc-mobile-nav" role="navigation" aria-label="Mobile navigation">
                <a href="{{ route('landing.home') }}" class="lhc-mobile-link {{ request()->routeIs('landing.home') ? 'active' : '' }}">Home</a>
                <a href="{{ route('landing.about') }}" class="lhc-mobile-link {{ request()->routeIs('landing.about') ? 'active' : '' }}">About Us</a>
               <a href="{{ route('landing.home') }}#login-section"
   class="lhc-mobile-link lhc-mobile-login">
    Get Started
</a>
            </nav>
        </div>
        <!-- background overlay (click to close) -->
        <div class="lhc-mobile-backdrop" id="lhcMobileBackdrop" tabindex="-1" aria-hidden="true"></div>
    </div>

    {{-- =========================
         SCOPED CSS
         - All styles namespaced under .landing-header-component to avoid leakage
         ========================= --}}
    <style>
    /* ===== root & defaults ===== */
    .landing-header-component {
        --lhc-primary: #004AAD;
        --lhc-accent: #3498db;
        --lhc-text: #333;
        --lhc-muted: #6c757d;
        --lhc-bg: #ffffff;
        --lhc-border: #e6e9ee;
        --lhc-radius: 10px;
        font-family: Inter, "Segoe UI", Roboto, system-ui, -apple-system, "Helvetica Neue", Arial;
    }

    /* ===== header layout ===== */
    .landing-header-component .lhc-header{
        position: sticky;
        top: 0;
        z-index: 1200;
        background: var(--lhc-bg);
        border-bottom: 1px solid var(--lhc-border);
        box-shadow: 0 6px 18px rgba(10, 12, 20, 0.04);
    }

    .landing-header-component .lhc-inner{
        max-width: 1180px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        gap: 1.25rem;
        padding: 0.75rem 1.25rem;
        justify-content: space-between;
    }

    /* logo */
    .landing-header-component .lhc-logo{
        display: inline-flex;
        align-items: center;
        gap: .6rem;
        text-decoration: none;
        color: var(--lhc-primary);
        font-weight: 700;
    }
    .landing-header-component .lhc-logo img{
        height: 44px;
        width: auto;
        display: block;
    }
    .landing-header-component .lhc-brand{
        font-size: 1.2rem;
        letter-spacing: -0.02em;
    }

    /* nav (desktop) */
    .landing-header-component .lhc-nav{
        display: flex;
        gap: 2rem;
        align-items: center;
        margin-left: auto; /* Changed from margin-left: 1rem to auto for better spacing */
        margin-right: 2rem; /* Added to create space between nav and CTA button */
    }
    .landing-header-component .lhc-nav-link{
        color: var(--lhc-muted);
        text-decoration: none;
        font-weight: 600;
        padding: .35rem 0;
        position: relative;
        transition: color .18s ease, transform .18s ease;
    }
    .landing-header-component .lhc-nav-link:hover{
        color: var(--lhc-primary);
        transform: translateY(-2px);
    }
    .landing-header-component .lhc-nav-link.active{
        color: var(--lhc-primary);
    }
    .landing-header-component .lhc-nav-link::after{
        content: '';
        display: block;
        height: 3px;
        width: 0;
        background: var(--lhc-accent);
        border-radius: 3px;
        position: absolute;
        left: 0;
        bottom: -8px;
        transition: width .18s ease;
    }
    .landing-header-component .lhc-nav-link:hover::after,
    .landing-header-component .lhc-nav-link.active::after{
        width: 100%;
    }

    /* CTA */
    .landing-header-component .lhc-cta {
        flex-shrink: 0; /* Prevent button from shrinking */
    }
    .landing-header-component .lhc-btn {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        padding: .5rem 1.05rem;
        border-radius: 8px;
        font-weight: 700;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: transform .12s ease, box-shadow .12s ease;
    }
    .landing-header-component .lhc-btn-primary{
        background: var(--lhc-primary);
        color: #fff;
        box-shadow: 0 6px 18px rgba(4, 42, 91, 0.08);
    }
    .landing-header-component .lhc-btn-primary:hover{
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(4, 42, 91, 0.12);
    }

    /* menu button (mobile) */
    .landing-header-component .lhc-menu-btn{
        display: none;
        background: transparent;
        border: none;
        font-size: 1.25rem;
        color: var(--lhc-primary);
        cursor: pointer;
        padding: 0.5rem;
    }

    /* ===== mobile menu (panel) ===== */
    .landing-header-component .lhc-mobile-menu{
        display: none; /* activated via .active by JS */
        position: fixed;
        inset: 0;
        z-index: 1400;
        pointer-events: none;
    }

    .landing-header-component .lhc-mobile-menu.active{
        display: block;
        pointer-events: auto;
    }

    .landing-header-component .lhc-mobile-inner{
        position: relative;
        width: min(420px, 94%);
        max-width: 420px;
        height: 100%;
        background: var(--lhc-bg);
        box-shadow: 0 20px 60px rgba(6,8,20,0.15);
        transform: translateX(-100%);
        animation: lhcSlideIn .32s ease both;
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
        z-index: 1401;
    }

    .landing-header-component .lhc-mobile-menu.active .lhc-mobile-inner {
        transform: translateX(0);
    }

    .landing-header-component .lhc-mobile-backdrop{
        position: fixed;
        inset: 0;
        background: rgba(12,16,25,0.45);
        transition: opacity .18s ease;
        opacity: 0;
        pointer-events: none;
    }

    .landing-header-component .lhc-mobile-menu.active .lhc-mobile-backdrop {
        opacity: 1;
        pointer-events: auto;
    }

    .landing-header-component .lhc-mobile-close{
        background: transparent;
        border: none;
        font-size: 1.25rem;
        color: var(--lhc-muted);
        padding: .3rem;
        margin-left: auto;
        cursor: pointer;
    }

    .landing-header-component .lhc-mobile-nav{
        display: flex;
        flex-direction: column;
        gap: .65rem;
        margin-top: .5rem;
    }
    .landing-header-component .lhc-mobile-link{
        text-decoration: none;
        color: var(--lhc-text);
        font-weight: 700;
        padding: .8rem 1rem;
        border-radius: 8px;
        transition: background-color .18s ease;
    }
    .landing-header-component .lhc-mobile-link:hover{
        background: #f6f8fb;
    }
    .landing-header-component .lhc-mobile-link.active {
        color: var(--lhc-primary);
        background: rgba(0, 74, 173, 0.05);
    }
    .landing-header-component .lhc-mobile-login{
        margin-top: .7rem;
        background: var(--lhc-primary);
        color: #fff;
        display: inline-block;
        text-align: center;
    }

    /* Animations */
    @keyframes lhcSlideIn {
        from { transform: translateX(-100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    /* ===== responsive rules ===== */
    @media (max-width: 900px) {
        .landing-header-component .lhc-inner{
            padding: .6rem 1rem;
        }
        .landing-header-component .lhc-nav{
            gap: 1.25rem;
            margin-right: 1rem;
        }
    }

    @media (max-width: 768px){
        .landing-header-component .lhc-nav,
        .landing-header-component .lhc-cta{
            display: none;
        }
        .landing-header-component .lhc-menu-btn{
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    }

    /* small visual polish */
    .landing-header-component .lhc-logo img[alt=""]{
        /* hide broken images gracefully */
        display: inline-block;
    }
    </style>

    {{-- =========================
         ROBUST JS (scoped)
         - Handles toggle, focus trap, outside click, escape key
         ========================= --}}
    <script>
    (function () {
        if (!document) return;

        const scope = document.querySelector('.landing-header-component');
        if (!scope) return;

        // Elements
        const menuBtn = scope.querySelector('#lhcMenuBtn');
        const mobileMenu = scope.querySelector('#lhcMobileMenu');
        const mobileInner = scope.querySelector('.lhc-mobile-inner');
        const backdrop = scope.querySelector('#lhcMobileBackdrop');
        const closeBtn = scope.querySelector('#lhcMobileClose');
        const mobileLinks = Array.from(scope.querySelectorAll('.lhc-mobile-link'));

        // Accessibility helpers
        function setMenuOpen(isOpen) {
            if (!menuBtn || !mobileMenu) return;
            menuBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            mobileMenu.classList.toggle('active', !!isOpen);
            mobileMenu.setAttribute('aria-hidden', isOpen ? 'false' : 'true');

            if (isOpen) {
                // save the previously focused element to restore later
                scope.__prevFocus = document.activeElement;
                // focus the close button first
                if (closeBtn) closeBtn.focus();
                document.body.style.overflow = 'hidden'; // lock body scroll
                document.addEventListener('keydown', onKeyDown, true);
            } else {
                // restore focus
                if (scope.__prevFocus && typeof scope.__prevFocus.focus === 'function') {
                    scope.__prevFocus.focus();
                }
                document.body.style.overflow = ''; // restore scrolling
                document.removeEventListener('keydown', onKeyDown, true);
            }
        }

        // find focusable elements for trap
        function findFocusable(root) {
            if (!root) return [];
            const selectors = [
                'a[href]:not([disabled])',
                'button:not([disabled])',
                'input:not([disabled])',
                'select:not([disabled])',
                'textarea:not([disabled])',
                '[tabindex]:not([tabindex="-1"])'
            ];
            return Array.from(root.querySelectorAll(selectors.join(','))).filter(el => el.offsetParent !== null);
        }

        // focus trap - loops focus within mobileInner
        function onKeyDown(e) {
            if (e.key === 'Escape') {
                // close on escape
                e.preventDefault();
                setMenuOpen(false);
                return;
            }

            if (e.key === 'Tab') {
                const focusables = findFocusable(mobileInner);
                if (!focusables.length) return;

                const first = focusables[0];
                const last = focusables[focusables.length - 1];
                const active = document.activeElement;

                if (e.shiftKey && active === first) {
                    e.preventDefault();
                    last.focus();
                } else if (!e.shiftKey && active === last) {
                    e.preventDefault();
                    first.focus();
                }
            }
        }

        // toggle handler
        function onToggleClick(e) {
            const isOpen = menuBtn.getAttribute('aria-expanded') === 'true';
            setMenuOpen(!isOpen);
        }

        // outside click (backdrop) closes menu
        function onBackdropClick(e) {
            setMenuOpen(false);
        }

        // handle link click -> close menu
        function onMobileLinkClick(e) {
            // Allow normal navigation, but close menu immediately for UX
            setMenuOpen(false);
        }

        // close button
        function onCloseClick(e) {
            setMenuOpen(false);
        }

        // Attach events
        try {
            if (menuBtn) menuBtn.addEventListener('click', onToggleClick);
            if (backdrop) backdrop.addEventListener('click', onBackdropClick);
            if (closeBtn) closeBtn.addEventListener('click', onCloseClick);
            mobileLinks.forEach(a => a.addEventListener('click', onMobileLinkClick));
        } catch (err) {
            console.warn('landing header script init failed', err);
        }

        // Ensure aria state is consistent on load
        (function initAria() {
            if (menuBtn) menuBtn.setAttribute('aria-expanded', 'false');
            if (mobileMenu) mobileMenu.setAttribute('aria-hidden', 'true');
        })();
    })();
    </script>
</div>