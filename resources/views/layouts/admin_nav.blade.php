<!-- resources/views/layouts/_sidebar.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Admin Sidebar - SukaRelawan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        /* ---------- Variables & Reset ---------- */
        :root {
            /* Light theme */
            --sidebar-bg: #ffffff;
            /* main sidebar background (light) */
            --sidebar-surface: #f8fafb;
            /* slightly different surface for hovered area */
            --sidebar-border: #e6eef6;
            /* subtle border */
            --text: #17202a;
            /* primary text color */
            --muted: #6b7280;
            /* muted text */
            --hover-bg: #eef6ff;
            /* item hover */
            --active-bg: #0b69ff;
            /* active/selected blue */
            --active-text-on-blue: #ffffff;
            --transition: all 0.22s ease;
            --sidebar-width-collapsed: 70px;
            --sidebar-width-expanded: 260px;
            --shadow: 0 4px 12px rgba(11, 105, 255, 0.06);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: #f3f6f9;
            color: var(--text);
            overflow-x: hidden;
        }

        /* ---------- Sidebar ---------- */
        .admin-sidebar {
            width: var(--sidebar-width-collapsed);
            height: 100vh;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
            transition: var(--transition);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: var(--shadow);
        }

        /* expand on hover (desktop) */
        .admin-sidebar:hover {
            width: var(--sidebar-width-expanded);
        }

        .logo-section {
            padding: 16px 14px;
            border-bottom: 1px solid var(--sidebar-border);
            display: flex;
            align-items: center;
            gap: 12px;
            background: linear-gradient(0deg, rgba(255, 255, 255, 0.6), rgba(255, 255, 255, 0));
        }

        .logo-img {
            height: 34px;
            width: auto;
            display: block;
            transition: var(--transition);
            border-radius: 6px;
        }

        .logo-text {
            color: var(--text);
            font-weight: 700;
            font-size: 1.05rem;
            white-space: nowrap;
            opacity: 0;
            transition: var(--transition);
        }

        .admin-sidebar:hover .logo-text {
            opacity: 1;
        }

        .nav-links {
            padding: 12px 0;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        /* nav item used for both anchors and submenu container */
        .nav-item {
            display: flex;
            align-items: center;
            padding: 10px 14px;
            margin: 0 10px;
            color: var(--text);
            text-decoration: none;
            border-radius: 8px;
            transition: var(--transition);
            white-space: nowrap;
            gap: 14px;
            cursor: pointer;
        }

        .nav-item:hover {
            background: var(--hover-bg);
        }

        .nav-item.active {
            background: var(--active-bg);
            color: var(--active-text-on-blue);
            box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.02);
        }

        .nav-item i:first-child {
            font-size: 1.12rem;
            min-width: 26px;
            text-align: center;
            color: inherit;
        }

        .nav-text {
            opacity: 0;
            transition: var(--transition);
            font-weight: 600;
        }

        .admin-sidebar:hover .nav-text {
            opacity: 1;
        }

        /* ---------- Submenu (compact) ---------- */
        .has-submenu {
            flex-direction: column;
            position: relative;
        }

        /* hide stray chevrons; show only the designated one */
        .nav-item .fa-chevron-down {
            display: none;
        }

        .dropdown-icon {
            display: inline-block;
            margin-left: auto;
            transition: transform 0.22s ease;
            color: var(--muted);
        }

        /* increase gap on main items like Users/Event to match others visually */
        .has-submenu .dropdown-toggle {
            display: flex;
            align-items: center;
            width: 100%;
            gap: 18px;
        }

        .has-submenu .submenu {
            display: none;
            /* hidden by default */
            flex-direction: column;
            padding-left: 32px;
            /* compact indent */
            margin-top: 6px;
            background: transparent;
        }

        .has-submenu.open .submenu {
            display: flex;
        }

        /* Submenu items smaller, no icons */
        .submenu a {
            display: block;
            padding: 6px 8px;
            margin: 3px 8px;
            font-size: 0.88rem;
            /* smaller text */
            color: var(--text);
            text-decoration: none;
            border-radius: 6px;
            white-space: nowrap;
            transition: var(--transition);
        }

        .submenu a:hover {
            background: var(--hover-bg);
        }

        /* blue active background for clicked submenu items */
        .submenu a.active {
            background: var(--active-bg);
            color: var(--active-text-on-blue);
        }

        .has-submenu.open .dropdown-icon {
            transform: rotate(180deg);
            color: var(--text);
        }

        /* ---------- User / Logout ---------- */
        .user-section {
            padding: 12px 14px;
            border-top: 1px solid var(--sidebar-border);
            display: flex;
            flex-direction: column;
            gap: 12px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.4), rgba(255, 255, 255, 0));
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Avatar round by default (collapsed) and remains round on hover */
        .user-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #cfe6ff;
            color: var(--active-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.95rem;
            overflow: hidden;
            flex-shrink: 0;
            transition: var(--transition);
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .user-details {
            opacity: 0;
            transition: var(--transition);
        }

        .admin-sidebar:hover .user-details {
            opacity: 1;
        }

        .user-name {
            font-weight: 600;
            color: var(--text);
            font-size: 0.95rem;
        }

        .user-email {
            color: var(--muted);
            font-size: 0.78rem;
        }

        .logout-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            background: transparent;
            border: none;
            margin: 0 10px;
            color: var(--text);
            cursor: pointer;
            border-radius: 8px;
        }

        .logout-btn span {
            opacity: 0;
            transition: var(--transition);
        }

        .admin-sidebar:hover .logout-btn span {
            opacity: 1;
        }

        .logout-btn:hover {
            background: var(--hover-bg);
        }

        /* ---------- Mobile (responsive) ---------- */
        @media (max-width: 768px) {
            .admin-sidebar {
                width: 0;
            }

            .admin-sidebar.active {
                width: var(--sidebar-width-expanded);
            }

            .mobile-toggle {
                display: flex !important;
            }

            /* when active on mobile show text and user details */
            .admin-sidebar.active .nav-text,
            .admin-sidebar.active .logo-text,
            .admin-sidebar.active .user-details,
            .admin-sidebar.active .logout-btn span {
                opacity: 1;
            }

            /* slightly larger submenu padding on mobile */
            .submenu {
                padding-left: 20px;
            }

            .user-avatar {
                width: 48px;
                height: 48px;
            }
        }

        .page-content {
            margin-left: var(--sidebar-width-collapsed);
            padding: 20px;
            transition: var(--transition);
        }

        .admin-sidebar:hover~.page-content {
            margin-left: var(--sidebar-width-expanded);
        }

        .admin-sidebar.active~.page-content {
            margin-left: var(--sidebar-width-expanded);
        }

        /* small helpers */
        .nav-item-link {
            text-decoration: none;
            color: inherit;
            display: flex;
            width: 100%;
            align-items: center;
        }
    </style>
</head>

<body>
    <aside class="admin-sidebar" id="adminSidebar" aria-label="Admin Sidebar">
        <div class="logo-section">
            <!-- Replace with user's logo -->
            <img src="{{ asset('images/sukarelawan_logo.png') }}" alt="SukaRelawan logo" class="logo-img">
            <div class="logo-text">SukaRelawan</div>
        </div>

        <div class="nav-links" role="navigation" aria-label="Main Navigation">
            <!-- Home -->
            <a href="#" class="nav-item active nav-link" data-key="home">
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
                    <a href="#" role="menuitem" data-key="event-discovery">Event Discovery</a>
                    <a href="{{ route('admin.eventCategory.eventCategory-list') }}" role="menuitem"
                        data-key="event-category">Event Category</a>
                    <a href="{{ route('admin.sdg.sdg-list') }}" role="menuitem" data-key="event-sdg">Event SDG</a>
                    <a href="{{ route('admin.skill.skill-list') }}" role="menuitem" data-key="event-skill">Event
                        Skill</a>
                </div>
            </div>


            <!-- Event dropdown -->
            <div class="nav-item has-submenu" data-key="blog">
                <div class="dropdown-toggle nav-link" role="button" aria-expanded="false"
                    aria-controls="blog-submenu">
                    <i class="fas fa-blog"></i>
                    <span class="nav-text">Blog</span>
                    <i class="fas fa-chevron-down dropdown-icon" aria-hidden="true"></i>
                </div>

                <div class="submenu" id="blog-submenu" role="menu" aria-label="blog submenu">
                    <a href="#" role="menuitem" data-key="event-discovery">Blog Post</a>
                    <a href="{{ route('admin.blogcategory.category-list') }}" role="menuitem" data-key="event-category">Blog Category</a>
                </div>
            </div>

          

            <div class="nav-item has-submenu" data-key="reward">
                <div class="dropdown-toggle nav-link" role="button" aria-expanded="false"
                    aria-controls="reward-submenu">
                    <i class="fas fa-gift"></i>
                    <span class="nav-text">Reward</span>
                    <i class="fas fa-chevron-down dropdown-icon" aria-hidden="true"></i>
                </div>

                <div class="submenu" id="reward-submenu" role="menu" aria-label="reward submenu">
                    <a href="{{ route('admin.badges.index') }}" role="menuitem" data-key="reward-manage">manage reward</a>
                    <a href="{{ route('admin.badge_categories.badgeCategory-list') }}" role="menuitem" data-key="reward-category">reward Category</a>
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
                    <a href="#" role="menuitem" data-key="all-users">All Users</a>
                    <a href="#" role="menuitem" data-key="add-user">Add New User</a>
                    <a href="#" role="menuitem" data-key="permissions">Permissions</a>
                    <a href="#" role="menuitem" data-key="activity-log">Activity Log</a>
                </div>
            </div>
        </div>

        <!-- logout -->
        <button class="logout-btn" aria-label="Logout">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </button>

        <!-- User section: dynamic using Auth -->
        <div class="user-section">
            <div class="user-info">
                <div class="user-avatar" aria-hidden="true">
                    @php
                        $user = Auth::user();
                        $profile = $user ? $user->adminProfile ?? null : null;
                        // compute initials fallback
                        $initials = '';
                        if ($profile && !empty($profile->name)) {
                            $parts = preg_split('/\s+/', trim($profile->name));
                            $initials = strtoupper(
                                substr($parts[0], 0, 1) .
                                    (count($parts) > 1 ? substr($parts[count($parts) - 1], 0, 1) : ''),
                            );
                        } elseif ($user && !empty($user->email)) {
                            $initials = strtoupper(substr($user->email, 0, 2));
                        }
                    @endphp

                    @if ($profile && !empty($profile->profilePhoto) && file_exists(public_path('storage/' . $profile->profilePhoto)))
                        <img src="{{ asset('storage/' . $profile->profilePhoto) }}" alt="{{ $profile->name }}">
                    @else
                        {{ $initials ?: 'A' }}
                    @endif
                </div>

                <div class="user-details" aria-hidden="false">
                    <div class="user-name">
                        {{ $profile->name ?? ($user->name ?? ($user->email ?? 'Admin')) }}
                    </div>
                    <div class="user-email">
                        {{ $user->email ?? '' }}
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <!-- Mobile Toggle Button -->
    <button class="mobile-toggle" id="mobileToggle" style="display:none;" aria-label="Toggle sidebar">
        <i class="fas fa-bars" aria-hidden="true"></i>
    </button>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('adminSidebar');
            const mobileToggle = document.getElementById('mobileToggle');

            // Top-level nav item active logic (ignore has-submenu containers)
            const topNavItems = Array.from(document.querySelectorAll('.nav-links > .nav-item'))
                .filter(el => !el.classList.contains('has-submenu') || el.classList.contains('nav-link'));

            topNavItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    // remove active from other nav items and submenu items
                    document.querySelectorAll('.nav-item').forEach(i => i.classList.remove(
                        'active'));
                    document.querySelectorAll('.submenu a.active').forEach(s => s.classList.remove(
                        'active'));
                    this.classList.add('active');
                });
            });

            // Dropdown toggles - allow multiple open at once
            document.querySelectorAll('.has-submenu .dropdown-toggle').forEach(toggle => {
                toggle.addEventListener('click', function(e) {

                    e.stopPropagation();
                    const parent = this.parentElement;
                    parent.classList.toggle('open');

                    // set aria-expanded
                    const expanded = parent.classList.contains('open');
                    this.setAttribute('aria-expanded', expanded ? 'true' : 'false');

                    // visual: mark parent as active (main)
                    document.querySelectorAll('.nav-item').forEach(i => i.classList.remove(
                        'active'));
                    parent.classList.add('active');
                });
            });

            // Submenu link click: make submenu item blue and set parent active
            document.querySelectorAll('.submenu a').forEach(link => {
                link.addEventListener('click', function(e) {

                    e.stopPropagation();

                    // remove active from other submenu items
                    document.querySelectorAll('.submenu a.active').forEach(s => s.classList.remove(
                        'active'));
                    this.classList.add('active');

                    // mark parent .has-submenu as active and keep it open
                    const parent = this.closest('.has-submenu');
                    if (parent) {
                        parent.classList.add('open');
                        // remove active from other top-level anchors
                        document.querySelectorAll('.nav-item').forEach(i => {
                            if (!parent.isSameNode(i)) i.classList.remove('active');
                        });
                        parent.classList.add('active');
                    }
                });
            });

            // Auto-collapse open submenus when sidebar is hovered out (desktop only)
            sidebar.addEventListener('mouseleave', () => {
                if (window.innerWidth > 768) {
                    document.querySelectorAll('.has-submenu.open').forEach(el => {
                        el.classList.remove('open');
                        // update aria-expanded on its toggle
                        const toggle = el.querySelector('.dropdown-toggle');
                        if (toggle) toggle.setAttribute('aria-expanded', 'false');
                    });
                }
            });

            // Close submenus when clicking outside on desktop
            window.addEventListener('click', (e) => {
                if (window.innerWidth > 768 && !sidebar.contains(e.target)) {
                    document.querySelectorAll('.has-submenu.open').forEach(el => {
                        el.classList.remove('open');
                        const toggle = el.querySelector('.dropdown-toggle');
                        if (toggle) toggle.setAttribute('aria-expanded', 'false');
                    });
                }
            });

            // Mobile toggle functionality
            if (mobileToggle) {
                mobileToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                    const icon = this.querySelector('i');
                    icon.classList.toggle('fa-bars');
                    icon.classList.toggle('fa-times');
                });

                // style & position mobile toggle via JS (keeps CSS minimal)
                Object.assign(mobileToggle.style, {
                    position: 'fixed',
                    top: '14px',
                    left: '14px',
                    zIndex: '1100',
                    background: 'var(--active-bg)',
                    color: 'white',
                    border: 'none',
                    borderRadius: '6px',
                    width: '42px',
                    height: '42px',
                    fontSize: '1.1rem',
                    display: 'none',
                    justifyContent: 'center',
                    alignItems: 'center',
                });
            }

            // Handle responsiveness & behavior differences
            function handleResize() {
                if (window.innerWidth <= 768) {
                    if (mobileToggle) mobileToggle.style.display = 'flex';
                } else {
                    if (mobileToggle) mobileToggle.style.display = 'none';
                    sidebar.classList.remove('active');
                }
            }
            handleResize();
            window.addEventListener('resize', handleResize);
        });
    </script>
</body>

</html>
