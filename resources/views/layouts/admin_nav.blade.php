<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Admin Sidebar - SukaRelawan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        /* ===== Merged SukaRelawan admin sidebar =====
        - Merged advantages: Perfect icon alignment (no position jump on expand/collapse) from first
        - Better dropdown behavior (popout in collapsed, inline in expanded) from second
        - Colors, :root variables, and overall palette from second (preferred)
        - No double dropdown icons: Ensured single chevron with display rules
        - No horizontal scrollbar: overflow-x hidden, vertical only
        - Smooth transitions, no jumps, centered-ish icons without centering shifts
        */

        /* ---------- palette (from second, preferred) ---------- */
        :root {
            /* Primary palette */
            --primary-color: #004aad;
            --primary-hover: #003780;
            --primary-light: #6c8aec;

            /* Text & backgrounds */
            --text-color: #333;
            --light-gray: #f9f9f9;
            --bg-gradient-start: #f5f7fa;
            --bg-gradient-end: #e4edf5;
            --muted: #6c757d;

            /* Layout */
            --sidebar-width-expanded: 260px;
            /* slight increase for comfort */
            --sidebar-width-collapsed: 70px;
            /* from first for better centering */
            --transition-time: 0.26s;
            --shadow: 0 6px 18px rgba(2, 6, 23, 0.06);

            /* icon / spacing */
            --icon-size: 18px;
            /* base icon size */
            --icon-container-width: 26px;
            /* from first, tighter */
            --row-height: 48px;
            /* fixed row height for vertical center */
            --submenu-width: 240px;
            --radius: 10px;
            --z-submenu: 1100;
        }

        /* ---------- reset / body basics ---------- */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-color);
            background: linear-gradient(180deg, var(--bg-gradient-start), var(--bg-gradient-end));
        }

        /* ---------- sidebar base ---------- */
        .admin-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width-collapsed);
            background: linear-gradient(180deg, var(--light-gray), #ffffff);
            color: var(--text-color);
            overflow: visible;
            /* allow floating popouts */
            transition: width var(--transition-time) ease;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: var(--shadow);
            border-right: 1px solid rgba(0, 0, 0, 0.04);
        }

        /* expand on hover (desktop) OR via .active for mobile toggle */
        @media (hover: hover) and (pointer: fine) {
            .admin-sidebar:hover {
                width: var(--sidebar-width-expanded);
            }
        }

        .admin-sidebar.active {
            width: var(--sidebar-width-expanded);
        }

        /* ---------- logo section ---------- */
        .logo-section {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.04);
            white-space: nowrap;
            overflow: hidden;
        }

        /* logo image */
        .logo-img {
            width: 40px;
            height: 40px;
            object-fit: contain;
            border-radius: 6px;
        }

        .logo-text {
            font-weight: 700;
            font-size: 1rem;
            color: var(--text-color);
            opacity: 0;
            transform: translateX(-6px);
            transition: opacity var(--transition-time) ease, transform var(--transition-time) ease;
        }

        /* show text on expand */
        .admin-sidebar:hover .logo-text,
        .admin-sidebar.active .logo-text {
            opacity: 1;
            transform: translateX(0);
        }

        /* ---------- nav links ---------- */
        .nav-links {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            /* no horizontal scroll */
            padding: 8px 4px;
        }

        /* unify top-level row layout for perfect alignment */
        .nav-item {
            display: block;
            width: 100%;
            margin: 6px 6px;
            border-radius: 8px;
            position: relative;
        }

        /* primary clickable areas: either <a> or .dropdown-toggle */
        /* primary clickable areas: either <a> or .dropdown-toggle */
        .nav-link,
        .dropdown-toggle {
            display: flex;
            align-items: center;
            gap: 12px;
            height: var(--row-height);
            padding: 0 13px;
            border-radius: 8px;
            color: var(--text-color);
            text-decoration: none;
            white-space: nowrap;
            cursor: pointer;
            transition: background var(--transition-time) ease, color var(--transition-time) ease, padding var(--transition-time) ease;
            justify-content: flex-start;
            width: 100%;
            /* Ensure it takes full width */
        }

        /* Ensure nav text takes available space, pushing dropdown icon to the right */
        .dropdown-toggle .nav-text {
            flex: 1;
        }

        /* icon container: guaranteed square => perfect centering */
        .nav-link i,
        .dropdown-toggle i:first-child {
            font-size: 1.12rem;
            min-width: 26px;
            text-align: center;
            color: inherit;
        }

        /* smaller home icon to match visual weight */
        .nav-links>a.nav-link>i.fas.fa-home {
            font-size: 16px;
        }

        /* text label */
        .nav-text {
            display: inline-block;
            opacity: 0;
            transform: translateX(-6px);
            transition: opacity var(--transition-time) ease, transform var(--transition-time) ease;
            font-size: 15px;
            color: var(--text-color);
        }

        /* show nav text when expanded */
        .admin-sidebar:hover .nav-text,
        .admin-sidebar.active .nav-text {
            opacity: 1;
            transform: translateX(0);
        }

        /* dropdown chevron */
        .nav-item .fa-chevron-down {
            display: none;
        }

        /* make chevron hidden by default and only show when sidebar is expanded */
        .dropdown-icon {
            display: none;
            /* hidden by default (collapsed sidebar) */
            margin-left: auto;
            margin-right: 8px;
            transition: transform 0.22s ease;
            color: var(--muted);
            /* don't set opacity here; we control visibility with display */
        }

        /* show chevron when sidebar is expanded (hover on desktop OR .active for mobile toggle) */
        .admin-sidebar:hover .dropdown-icon,
        .admin-sidebar.active .dropdown-icon {
            display: inline-block;
        }


        /* hover/active styles */
        .nav-link:hover,
        .dropdown-toggle:hover {
            background: rgba(0, 0, 0, 0.03);
        }

        .nav-item.active>.nav-link,
        .nav-item.active>.dropdown-toggle {
            background: linear-gradient(90deg, rgba(0, 74, 173, 0.08), rgba(0, 74, 173, 0.03));
            color: var(--primary-color);
        }

        .nav-item.active>.nav-link i,
        .nav-item.active>.dropdown-toggle i {
            color: var(--primary-color);
        }

        /* focus visible */
        .nav-link:focus,
        .dropdown-toggle:focus {
            outline: 3px solid rgba(0, 74, 173, 0.12);
            outline-offset: -2px;
        }

        /* ---------- submenu rules (JS-controlled only) ---------- */
        /* Default: hide submenu */
        .submenu {
            display: none;
            opacity: 0;
            transform: translateY(-4px);
            transition: opacity var(--transition-time), transform var(--transition-time);
        }

        /* When parent has .open class, show submenu */
        .has-submenu.open>.submenu {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }

        /* Submenu styles for expanded sidebar */
        .admin-sidebar:hover .has-submenu.open>.submenu,
        .admin-sidebar.active .has-submenu.open>.submenu {
            position: relative;
            left: 0;
            min-width: 0;
            background: transparent;
            color: inherit;
            padding: 6px 0 0 0;
            box-shadow: none;
            border-radius: 6px;
            opacity: 1;
            transform: none;
        }

        /* Submenu item styles */
        .submenu a {
            display: block;
            padding: 10px 16px;
            padding-left: 56px;
            /* indent under icon + spacing */
            text-decoration: none;
            color: var(--muted);
            font-size: 13px;
            border-radius: 6px;
            transition: background var(--transition-time), color var(--transition-time), padding var(--transition-time);
        }

        .submenu a:hover {
            background: rgba(0, 74, 173, 0.06);
            color: var(--primary-color);
            padding-left: 62px;
        }

        .submenu a.active {
            background: rgba(0, 74, 173, 0.08);
            color: var(--primary-color);
            font-weight: 600;
        }

        /* ---------- floating popout submenu (only when collapsed) ---------- */
        /* Use popout only when sidebar is collapsed (desktop only). */
        @media (hover: hover) and (pointer: fine) {
            .admin-sidebar:not(:hover) .has-submenu.open>.submenu {
                display: block;
                position: absolute;
                left: calc(var(--sidebar-width-collapsed) + 12px);
                min-width: var(--submenu-width);
                background: #ffffff;
                color: var(--text-color);
                border-radius: var(--radius);
                padding: 8px;
                box-shadow: 0 8px 26px rgba(8, 24, 48, 0.12);
                border: 1px solid rgba(0, 0, 0, 0.06);
                opacity: 1;
                transform: translateY(0);
                z-index: var(--z-submenu);
            }

            /* In collapsed mode make popout submenu links use the popout colors */
            .admin-sidebar:not(:hover) .has-submenu.open>.submenu a {
                color: var(--text-color);
                background: transparent;
                padding-left: 12px;
            }

            .admin-sidebar:not(:hover) .has-submenu.open>.submenu a:hover {
                background: rgba(0, 74, 173, 0.06);
                color: var(--primary-color);
            }
        }

        /* ---------- logout (just above profile) ---------- */
        .logout-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            width: calc(100% - 18px);
            margin: 6px 9px;
            padding: 10px 12px;
            border-radius: 8px;
            background: transparent;
            border: 1px solid rgba(0, 0, 0, 0.04);
            color: var(--text-color);
            cursor: pointer;
            justify-content: flex-start;
            transition: background var(--transition-time), color var(--transition-time);
        }

        .admin-sidebar:not(:hover) .logout-btn span {
            display: none;
        }

        /* hover */
        .logout-btn:hover {
            background: rgba(0, 74, 173, 0.06);
            color: var(--primary-color);
        }

        /* ---------- user section ---------- */
        .user-section {
            padding: 12px;
            border-top: 1px solid rgba(0, 0, 0, 0.04);
            background: transparent;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .user-details {
            opacity: 0;
            transform: translateX(-6px);
            transition: opacity var(--transition-time), transform var(--transition-time);
        }

        .admin-sidebar:hover .user-details,
        .admin-sidebar.active .user-details {
            opacity: 1;
            transform: none;
        }

        .admin-sidebar:not(:hover) .user-details {
            display: none;
        }

        /* ---------- accessibility & small niceties ---------- */
        .nav-links::-webkit-scrollbar {
            width: 6px;
        }

        .nav-links::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.06);
            border-radius: 4px;
        }

        .nav-link:focus,
        .dropdown-toggle:focus,
        .submenu a:focus,
        .logout-btn:focus {
            outline: 3px solid rgba(0, 74, 173, 0.12);
            outline-offset: -2px;
        }

        /* mobile: no hover expansion; use .active to show */
        @media (max-width: 768px) {
            .admin-sidebar {
                width: 0;
                transition: width var(--transition-time);
                overflow: hidden;
            }

            .admin-sidebar.active {
                width: var(--sidebar-width-expanded);
            }

            .admin-sidebar .nav-text,
            .admin-sidebar .logo-text,
            .admin-sidebar .dropdown-icon,
            .admin-sidebar .user-details {
                opacity: 1;
                display: inline-block;
            }

            .admin-sidebar .nav-link,
            .admin-sidebar .dropdown-toggle {
                justify-content: flex-start;
                padding-left: 14px;
            }
        }



        /* ensure collapsed items hide text (chevrons handled above) */
        .admin-sidebar:not(:hover) .nav-text,
        .admin-sidebar:not(:hover) .logo-text,
        .admin-sidebar:not(:hover) .logout-btn span,
        .admin-sidebar:not(:hover) .user-details {
            display: none;
        }


        /* Mobile toggle button styling */
        .mobile-toggle {
            position: fixed;
            top: 14px;
            left: 14px;
            z-index: 1100;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 6px;
            width: 42px;
            height: 42px;
            font-size: 1.1rem;
            display: none;
            justify-content: center;
            align-items: center;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .mobile-toggle {
                display: flex;
            }
        }

        /* Remove any framework-added caret from dropdown-toggle used in the sidebar */
        .admin-sidebar .dropdown-toggle::after {
            content: none !important;
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }

        /* Smooth rotate and origin for the dropdown chevron */
        .dropdown-icon {
            transition: transform 0.22s ease, opacity 0.18s ease;
            transform-origin: center;
        }

        /* Flip the chevron when the parent .has-submenu is open */
        .has-submenu.open>.dropdown-toggle .dropdown-icon {
            transform: rotate(-180deg);
            /* rotate -180 so chevron points up when open */
        }

        /* If you want a small visual 'nudge' when chevron appears, keep opacity rule (optional) */
        .admin-sidebar:hover .dropdown-icon,
        .admin-sidebar.active .dropdown-icon {
            opacity: 1;
        }
    </style>
</head>

<body>
    <!-- Mobile Toggle Button -->
    <button class="mobile-toggle" id="mobileToggle" aria-label="Toggle sidebar">
        <i class="fas fa-bars" aria-hidden="true"></i>
    </button>

    <aside class="admin-sidebar" id="adminSidebar" aria-label="Admin Sidebar">
        <div class="logo-section">
            <!-- Replace with user's logo -->
            <img src="{{ asset('assets/sukarelawan_logo.png') }}" alt="SukaRelawan logo" class="logo-img">
            <div class="logo-text">SukaRelawan</div>
        </div>

        <div class="nav-links" role="navigation" aria-label="Main Navigation">
            <!-- Home -->
            <a href="{{ route('admin.dashboard.index') }}" class="nav-item active nav-link" data-key="home">
                <i class="fas fa-home"></i>
                <span class="nav-text">Home</span>
            </a>

            <!-- Event dropdown -->
            <div class="nav-item has-submenu" data-key="event">
                <div class="dropdown-toggle nav-link" role="button" aria-expanded="false"
                    aria-controls="event-submenu">
                    <i class="fas fa-calendar-alt"></i>
                    <span class="nav-text">Event</span>
                    <i class="fas fa-chevron-down dropdown-icon" aria-hidden="true"></i>
                </div>

                <div class="submenu" id="event-submenu" role="menu" aria-label="Event submenu">
                    <a href="{{ route('admin.events.index') }}" role="menuitem" data-key="event-discovery">Event
                        Discovery</a>
                    <a href="{{ route('admin.eventCategory.eventCategory-list') }}" role="menuitem"
                        data-key="event-category">Event Category</a>
                    <a href="{{ route('admin.sdg.sdg-list') }}" role="menuitem" data-key="event-sdg">Event SDG</a>
                    <a href="{{ route('admin.skill.skill-list') }}" role="menuitem" data-key="event-skill">Event
                        Skill</a>
                </div>
            </div>

            <!-- Blog dropdown -->
            <div class="nav-item has-submenu" data-key="blog">
                <div class="dropdown-toggle nav-link" role="button" aria-expanded="false" aria-controls="blog-submenu">
                    <i class="fas fa-blog"></i>
                    <span class="nav-text">Blog</span>
                    <i class="fas fa-chevron-down dropdown-icon" aria-hidden="true"></i>
                </div>

                <div class="submenu" id="blog-submenu" role="menu" aria-label="blog submenu">

                    <a href="{{ route('admin.blogs.index') }}" role="menuitem" data-key="blog-post">
                        Blog Post
                    </a>

                    <a href="{{ route('admin.blogcategory.category-list') }}" role="menuitem" data-key="blog-category">
                        Blog Category
                    </a>

                    <a href="{{ route('admin.blogs.drafts') }}" role="menuitem" data-key="blog-drafts">
                        Draft Blogs
                    </a>

                </div>

            </div>

            <!-- Reward dropdown -->
            <div class="nav-item has-submenu" data-key="reward">
                <div class="dropdown-toggle nav-link" role="button" aria-expanded="false"
                    aria-controls="reward-submenu">
                    <i class="fas fa-gift"></i>
                    <span class="nav-text">Reward</span>
                    <i class="fas fa-chevron-down dropdown-icon" aria-hidden="true"></i>
                </div>

                <div class="submenu" id="reward-submenu" role="menu" aria-label="reward submenu">
                    <a href="{{ route('admin.badges.index') }}" role="menuitem" data-key="reward-manage">
                        Reward</a>
                    <a href="{{ route('admin.badge_categories.badgeCategory-list') }}" role="menuitem"
                        data-key="reward-category">Reward Category</a>
                </div>
            </div>

            <!-- Users dropdown -->
            <div class="nav-item has-submenu" data-key="users">
                <div class="dropdown-toggle nav-link" role="button" aria-expanded="false"
                    aria-controls="users-submenu">
                    <i class="fas fa-users"></i>
                    <span class="nav-text">Users</span>
                    <i class="fas fa-chevron-down dropdown-icon" aria-hidden="true"></i>
                </div>

                <div class="submenu" id="users-submenu" role="menu" aria-label="Users submenu">
                    <a href="{{ route('admin.users.index') }}" role="menuitem" data-key="all-users">All Users</a>
                </div>
            </div>
        </div>

        <!-- logout -->
        <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="logout-btn" aria-label="Logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </button>
        </form>

        @php
            $user = Auth::user();
            // prefer adminProfile, then volunteerProfile, then ngoProfile for display in admin area
            $profile = $user ? $user->adminProfile ?? ($user->volunteerProfile ?? ($user->ngoProfile ?? null)) : null;

            // profile photo (could be null)
            $photoFile = optional($profile)->profilePhoto ?? null;

            // default image
            $profileImageUrl = asset('assets/default_admin.jpg');

            if ($photoFile) {
                $basename = trim(basename($photoFile));

                // check common public image locations first
                if ($basename && file_exists(public_path("images/profiles/{$basename}"))) {
                    $profileImageUrl = asset("images/profiles/{$basename}");
                } elseif ($basename && file_exists(public_path("images/{$basename}"))) {
                    $profileImageUrl = asset("images/{$basename}");
                }
                // then check storage disk 'public'
                elseif (\Illuminate\Support\Facades\Storage::disk('public')->exists($photoFile)) {
                    $profileImageUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($photoFile);
                } elseif (
                    $basename &&
                    \Illuminate\Support\Facades\Storage::disk('public')->exists("profiles/{$basename}")
                ) {
                    $profileImageUrl = \Illuminate\Support\Facades\Storage::disk('public')->url("profiles/{$basename}");
                }
            }
        @endphp

        <div class="user-section">
            <div class="user-info">
                <div class="user-avatar" aria-hidden="true">
                    <img src="{{ $profileImageUrl }}" alt="{{ $profile->name ?? ($user->name ?? 'Admin') }}">
                </div>

                <div class="user-details" aria-hidden="false">
                    <div class="user-name">
                        {{ $profile->name ?? ($user->name ?? 'Admin') }}
                    </div>
                    <div class="user-email">
                        {{ $user->email ?? '' }}
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('adminSidebar');
            const mobileToggle = document.getElementById('mobileToggle');

            function isSidebarExpanded() {
                const isActive = sidebar.classList.contains('active');
                const isHovered = window.innerWidth > 768 && sidebar.matches(':hover');
                return isActive || isHovered;
            }

            function updateChevronsVisibility() {
                const expanded = isSidebarExpanded();
                document.querySelectorAll('.dropdown-icon').forEach(d => {
                    d.setAttribute('aria-hidden', (!expanded).toString());
                });
            }

            // Utility: close all open submenus except `exceptEl` (if provided)
            function closeAllSubmenus(exceptEl = null) {
                document.querySelectorAll('.has-submenu.open').forEach(el => {
                    if (exceptEl && el.isSameNode(exceptEl)) return;
                    el.classList.remove('open');
                    const toggle = el.querySelector('.dropdown-toggle');
                    if (toggle) toggle.setAttribute('aria-expanded', 'false');
                });
            }

            // Top-level nav item active logic (ignore has-submenu containers)
            const topNavItems = Array.from(document.querySelectorAll('.nav-links > .nav-item'))
                .filter(el => !el.classList.contains('has-submenu') || el.classList.contains('nav-link'));

            topNavItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    document.querySelectorAll('.nav-item').forEach(i => i.classList.remove(
                        'active'));
                    document.querySelectorAll('.submenu a.active').forEach(s => s.classList.remove(
                        'active'));
                    this.classList.add('active');
                    closeAllSubmenus();
                    updateChevronsVisibility();
                });
            });

            // Dropdown toggles - only one open at a time, toggle chevron rotation via CSS
            document.querySelectorAll('.has-submenu .dropdown-toggle').forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const parent = this.parentElement;

                    // Close other open submenus first (so only one remains open)
                    closeAllSubmenus(parent);

                    // Toggle this one
                    const willOpen = !parent.classList.contains('open');
                    if (willOpen) {
                        parent.classList.add('open');
                    } else {
                        parent.classList.remove('open');
                    }

                    // aria-expanded for screen readers
                    this.setAttribute('aria-expanded', willOpen ? 'true' : 'false');

                    // Visual active state: make this top-level active
                    document.querySelectorAll('.nav-item').forEach(i => i.classList.remove(
                        'active'));
                    parent.classList.add('active');

                    // Keep chevrons in sync
                    updateChevronsVisibility();
                });
            });

            // Submenu link click: open its parent and close others
            document.querySelectorAll('.submenu a').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.stopPropagation();

                    document.querySelectorAll('.submenu a.active').forEach(s => s.classList.remove(
                        'active'));
                    this.classList.add('active');

                    const parent = this.closest('.has-submenu');
                    if (parent) {
                        // close all others then open this one
                        closeAllSubmenus(parent);
                        parent.classList.add('open');

                        // set aria-expanded on its toggle
                        const toggle = parent.querySelector('.dropdown-toggle');
                        if (toggle) toggle.setAttribute('aria-expanded', 'true');

                        // mark top-level active
                        document.querySelectorAll('.nav-item').forEach(i => {
                            if (!parent.isSameNode(i)) i.classList.remove('active');
                        });
                        parent.classList.add('active');
                    }

                    updateChevronsVisibility();
                });
            });

            // Auto-collapse open submenus when sidebar is hovered out (desktop only)
            sidebar.addEventListener('mouseleave', () => {
                if (window.innerWidth > 768) {
                    closeAllSubmenus();
                    updateChevronsVisibility();
                }
            });

            sidebar.addEventListener('mouseenter', () => {
                updateChevronsVisibility();
            });

            // Close submenus when clicking outside on desktop
            window.addEventListener('click', (e) => {
                if (window.innerWidth > 768 && !sidebar.contains(e.target)) {
                    closeAllSubmenus();
                    updateChevronsVisibility();
                }
            });

            // Mobile toggle functionality
            if (mobileToggle) {
                mobileToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                    const icon = this.querySelector('i');
                    icon.classList.toggle('fa-bars');
                    icon.classList.toggle('fa-times');

                    // When toggling mobile, close other open submenus to keep UI tidy
                    if (!sidebar.classList.contains('active')) {
                        closeAllSubmenus();
                    }
                    updateChevronsVisibility();
                });
            }

            // Handle responsiveness & behavior differences
            function handleResize() {
                if (window.innerWidth <= 768) {
                    if (mobileToggle) mobileToggle.style.display = 'flex';
                } else {
                    if (mobileToggle) mobileToggle.style.display = 'none';
                    // remove mobile active state when switching back to desktop
                    sidebar.classList.remove('active');
                    closeAllSubmenus();
                }
                updateChevronsVisibility();
            }
            handleResize();
            window.addEventListener('resize', handleResize);

            // Initialize chevrons state on load
            updateChevronsVisibility();
        });
    </script>

</body>

</html>
