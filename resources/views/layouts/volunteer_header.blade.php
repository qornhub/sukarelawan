<div class="volunteer-header-component">
    @php
        use Illuminate\Support\Facades\Auth;

        $user = Auth::user();
        $volunteer = $user->volunteerProfile ?? null;
        $roleName = $user->role->roleName ?? 'Volunteer';
        $name = $volunteer->name ?? 'Anonymous';
        $volProfile = optional(auth()->user())->volunteerProfile;
        $filename = $volProfile && $volProfile->profilePhoto ? $volProfile->profilePhoto : null;
    @endphp


    <!-- Volunteer Header -->
    <header class="volunteer-header">
        <a href="#" class="volunteer-logo-section">
            <img src="{{ asset('images/sukarelawan_logo.png') }}" alt="Logo">
            <h4 class="volunteer-logo-title">SukaRelawan</h4>
        </a>

        <!-- Desktop Navigation and Profile -->
        <nav class="volunteer-nav-section">
            <a href="{{ route('volunteer.index.public') }}"
                class="volunteer-nav-link {{ request()->routeIs('volunteer.index.public') ? 'active' : '' }}">
                <i class="fas fa-home"></i> Home
            </a>

            <a href="{{ route('blogs.index') }}"
                class="volunteer-nav-link {{ request()->routeIs('blogs.index') ? 'active' : '' }}">
                <i class="fas fa-blog"></i> Blog
            </a>

            <a href="{{ route('volunteer.rewards.index') }}"
                class="volunteer-nav-link {{ request()->routeIs('volunteer.rewards.index') ? 'active' : '' }}">
                <i class="fas fa-award"></i> Reward
            </a>

        </nav>

        <div class="desktop-profile">
            <div class="volunteer-profile-section">
                <img src="{{ $filename ? asset('images/profiles/' . $filename) : asset('images/default-profile.png') }}"
                    alt="Profile Photo" class="volunteer-profile-img">
                <div class="volunteer-profile-info">
                    <p class="volunteer-profile-name">{{ $name }}</p>
                    <p class="volunteer-profile-role">{{ ucfirst($roleName) }}</p>
                </div>
                <div class="volunteer-profile-dropdown">
                    @auth
                        @if (auth()->user()->role->roleName === 'volunteer' && Route::has('volunteer.profile.profile'))
                            <a href="{{ route('volunteer.profile.profile') }}" class="volunteer-dropdown-item">
                                <i class="fas fa-user-circle"></i> My Profile
                            </a>
                        @endif
                    @endauth
                    <a href="#" class="volunteer-dropdown-item">
                        <i class="fas fa-cog"></i> Account Settings
                    </a>
                    <a href="{{ route('volunteer.notifications.index') }}"
                        class="volunteer-dropdown-item position-relative volunteer-notifications-link">
                        <i class="fas fa-bell"></i> Notifications
                    </a>

                    <div class="volunteer-dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout.volunteer') }}">
                        @csrf
                        <button type="submit" class="volunteer-dropdown-item" aria-label="Logout">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>

                </div>
            </div>
        </div>

        <!-- Mobile Menu Button -->
        <button class="volunteer-mobile-menu-btn" id="volunteerMobileMenuBtn">
            <i class="fas fa-bars"></i>
        </button>
    </header>

    <!-- Mobile Menu Container -->
    <div class="volunteer-mobile-menu-container" id="volunteerMobileMenuContainer">
        <nav class="volunteer-nav-section">
            <a href="#" class="volunteer-nav-link active"><i class="fas fa-home"></i> Home</a>
            <a href="#" class="volunteer-nav-link"><i class="fas fa-calendar-alt"></i> Event</a>
            <a href="#" class="volunteer-nav-link"><i class="fas fa-blog"></i> Blog</a>
            <a href="#" class="volunteer-nav-link"><i class="fas fa-award"></i> Reward</a>
        </nav>



        <div class="volunteer-profile-section">
            <div style="position:relative; display:inline-block;">
                <img src="{{ $filename ? asset('images/profiles/' . $filename) : asset('images/default-profile.png') }}"
                    alt="Profile Photo" class="volunteer-profile-img">


            </div>

            <div class="volunteer-profile-info">
                <p class="volunteer-profile-name">{{ $name }}</p>
                <p class="volunteer-profile-role">{{ ucfirst($roleName) }}</p>
            </div>
        </div>

        <div class="volunteer-mobile-menu-actions">
            @auth
                @if (auth()->user()->role->roleName === 'volunteer' && Route::has('volunteer.profile.profile'))
                    <a href="{{ route('volunteer.profile.profile', auth()->id()) }}" class="volunteer-dropdown-item">
                        <i class="fas fa-user-circle"></i> My Profile
                    </a>
                @endif
            @endauth



            <a href="#" class="volunteer-dropdown-item"><i class="fas fa-cog"></i> Account Settings</a>
            <a href="#" class="volunteer-dropdown-item"><i class="fas fa-bell"></i> Notifications</a>
            <form method="POST" action="{{ route('logout.volunteer') }}">
                @csrf
                <button type="submit" class="volunteer-dropdown-item">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    .volunteer-header-component {
        --vol-header-primary-color: #004AAD;
        --vol-header-primary-light: #3a506b;
        --vol-header-accent-color: #3498db;
        --vol-header-text-light: #f8f9fa;
        --vol-header-text-gray: #6c757d;
        --vol-header-border-color: #e0e0e0;
        --vol-header-hover-bg: rgba(255, 255, 255, 0.1);
        --vol-header-transition: all 0.3s ease;
        --vol-header-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    /* Header Styles */
    .volunteer-header-component .volunteer-header {
        background: white;
        padding: 0.75rem 2rem;
        box-shadow: var(--vol-header-shadow);
        position: sticky;
        top: 0;
        z-index: 1000;
        border-bottom: 1px solid var(--vol-header-border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        margin: 0;
    }

    .volunteer-header-component .volunteer-logo-section {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        text-decoration: none;
        transition: var(--vol-header-transition);
    }

    .volunteer-header-component .volunteer-logo-section:hover {
        transform: translateY(-2px);
    }

    .volunteer-header-component .volunteer-logo-section img {
        height: 40px;
        transition: var(--vol-header-transition);
    }

    .volunteer-header-component .volunteer-logo-title {
        font-weight: 700;
        color: var(--vol-header-primary-color);
        margin: 0;
        font-size: 1.35rem;
    }

    .volunteer-header-component .volunteer-nav-section {
        display: flex;
        gap: 4rem;
    }

    .volunteer-header-component .volunteer-nav-link {
        position: relative;
        color: var(--vol-header-text-gray);
        text-decoration: none;
        font-weight: 500;
        padding: 0.5rem 0;
        transition: var(--vol-header-transition);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .volunteer-header-component .volunteer-nav-link i {
        font-size: 0.9rem;
        opacity: 0.8;
    }

    .volunteer-header-component .volunteer-nav-link:hover,
    .volunteer-header-component .volunteer-nav-link.active {
        color: var(--vol-header-primary-color);
    }

    .volunteer-header-component .volunteer-nav-link:hover::after,
    .volunteer-header-component .volunteer-nav-link.active::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: var(--vol-header-accent-color);
        border-radius: 2px;
    }

    .volunteer-header-component .volunteer-profile-section {
        display: flex;
        align-items: center;
        gap: 1rem;
        position: relative;
        cursor: pointer;
    }

    .volunteer-header-component .volunteer-profile-img {
        height: 40px;
        width: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e0e0e0;
        transition: var(--vol-header-transition);
    }

    .volunteer-header-component .volunteer-profile-section:hover .volunteer-profile-img {
        border-color: var(--vol-header-accent-color);
        transform: scale(1.05);
    }

    .volunteer-header-component .volunteer-profile-info {
        text-align: right;
    }

    .volunteer-header-component .volunteer-profile-name {
        font-weight: 600;
        color: var(--vol-header-primary-color);
        margin: 0;
        font-size: 0.95rem;
    }

    .volunteer-header-component .volunteer-profile-role {
        font-size: 0.8rem;
        color: var(--vol-header-text-gray);
        margin: 0;
    }

    .volunteer-header-component .volunteer-profile-dropdown {
        position: absolute;
        top: 100%;
        right: -1rem;
        width: 200px;
        background: white;
        border-radius: 8px;
        box-shadow: var(--vol-header-shadow);
        padding: 0.5rem 0;
        margin-top: 1rem;
        opacity: 0;
        visibility: hidden;
        transform: translateY(10px);
        transition: var(--vol-header-transition);
        z-index: 1000;
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .volunteer-header-component .volunteer-profile-section:hover .volunteer-profile-dropdown {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    /* dropdown item normalization for anchors & buttons */
    .volunteer-header-component .volunteer-dropdown-item,
    .volunteer-header-component button.volunteer-dropdown-item,
    .volunteer-header-component .volunteer-mobile-menu-actions .volunteer-dropdown-item,
    .volunteer-header-component .volunteer-mobile-menu-actions button.volunteer-dropdown-item {
        /* remove native button chrome and match anchor layout */
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        background: none;
        border: none;
        padding: 0.75rem 1.5rem;
        margin: 0;
        width: 100%;
        text-align: left;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: var(--vol-header-text-gray);
        text-decoration: none;
        transition: var(--vol-header-transition);
        font: inherit;
        /* match surrounding font */
    }

    .volunteer-header-component .volunteer-dropdown-item:hover,
    .volunteer-header-component button.volunteer-dropdown-item:hover,
    .volunteer-header-component .volunteer-mobile-menu-actions .volunteer-dropdown-item:hover {
        background: #f8f9fa;
        color: var(--vol-header-primary-color);
    }

    .volunteer-header-component .volunteer-dropdown-divider {
        height: 1px;
        background: var(--vol-header-border-color);
        margin: 0.5rem 0;
    }

    /* Mobile menu */
    .volunteer-header-component .volunteer-mobile-menu-btn {
        display: none;
        background: none;
        border: none;
        font-size: 1.5rem;
        color: var(--vol-header-primary-color);
        cursor: pointer;
        position: absolute;
        right: 1.5rem;
        top: 1rem;
    }

    /* Mobile menu container */
    .volunteer-header-component .volunteer-mobile-menu-container {
        display: none;
        position: fixed;
        top: 70px;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.95);
        z-index: 999;
        padding: 1.5rem;
        overflow-y: auto;
        transform: translateX(100%);
        transition: transform 0.3s ease;
    }

    .volunteer-header-component .volunteer-mobile-menu-container.active {
        transform: translateX(0);
    }

    .volunteer-header-component .volunteer-mobile-menu-container .volunteer-nav-section {
        flex-direction: column;
        gap: 0;
        margin-bottom: 1.5rem;
    }

    .volunteer-header-component .volunteer-mobile-menu-container .volunteer-nav-link {
        padding: 1.1rem 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .volunteer-header-component .volunteer-mobile-menu-container .volunteer-profile-section {
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 8px;
        flex-direction: row;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .volunteer-header-component .volunteer-mobile-menu-container .volunteer-profile-info {
        text-align: left;
        flex-grow: 1;
    }

    .volunteer-header-component .volunteer-mobile-menu-actions .volunteer-dropdown-item {
        padding: 1.1rem 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: var(--vol-header-text-gray);
        text-decoration: none;
        transition: var(--vol-header-transition);
        border-bottom: 1px solid #f0f0f0;
    }

    .volunteer-header-component .volunteer-mobile-menu-actions .volunteer-dropdown-item:last-child {
        border-bottom: none;
    }

    /* notification badge on profile image */
    .profile-notif-badge {
        position: absolute;
        top: -6px;
        right: -6px;
        min-width: 20px;
        height: 20px;
        line-height: 18px;
        padding: 0 6px;
        border-radius: 999px;
        background: #dc3545;
        color: #fff;
        font-size: 0.7rem;
        font-weight: 600;
        display: inline-block;
        /* JS will toggle visibility */
        text-align: center;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
        pointer-events: none;
        /* so it doesn't block hover */
    }

    /* slightly smaller / reposition for small screens */
    @media (max-width: 768px) {
        .profile-notif-badge {
            top: -4px;
            right: -4px;
            min-width: 18px;
            height: 18px;
            font-size: 0.65rem;
        }
    }


    /* Responsive styles */
    @media (max-width: 992px) {
        .volunteer-header-component .volunteer-header {
            padding: 0.75rem 1.5rem;
        }

        .volunteer-header-component .volunteer-nav-section {
            gap: 1rem;
        }
    }

    @media (max-width: 768px) {
        .volunteer-header-component .volunteer-header {
            padding: 0.75rem 1rem;
            position: relative;
        }

        .volunteer-header-component .volunteer-mobile-menu-btn {
            display: block;
        }

        .volunteer-header-component .volunteer-nav-section,
        .volunteer-header-component .desktop-profile {
            display: none;
        }

        .volunteer-header-component .volunteer-mobile-menu-container {
            display: block;
        }
    }
</style>

<!-- Pusher + Echo (already in your files, keep these) -->
<script src="https://js.pusher.com/8.2/pusher.min.js"></script>
<script src="{{ asset('js/echo.js') }}"></script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // IDs & classes used by the script
        const PROFILE_BADGE_ID = 'notification-count-profile'; // created on profile image parent
        const TAB_BADGE_ID = 'notification-count-tab'; // created inside notifications link
        const GENERIC_CLASS = 'notif-badge';

        // helper: find all badges the script should update
        function getBadgeElements() {
            const elems = [];
            const profile = document.getElementById(PROFILE_BADGE_ID);
            const tab = document.getElementById(TAB_BADGE_ID);
            if (profile) elems.push(profile);
            if (tab) elems.push(tab);
            document.querySelectorAll('.' + GENERIC_CLASS).forEach(e => elems.push(e));
            document.querySelectorAll('[data-notif-badge]').forEach(e => elems.push(e));
            return elems;
        }

        function setBadges(n) {
            getBadgeElements().forEach(el => {
                const val = Math.max(0, parseInt(n) || 0);
                el.textContent = val;
                el.style.display = val > 0 ? 'inline-block' : 'none';
            });
        }

        function bumpBadges(delta = 1) {
            getBadgeElements().forEach(el => {
                const cur = Math.max(0, parseInt(el.textContent || '0') || 0);
                const next = Math.max(0, cur + delta);
                el.textContent = next;
                el.style.display = next > 0 ? 'inline-block' : 'none';
            });
        }

        function decrementBadges(delta = 1) {
            getBadgeElements().forEach(el => {
                const cur = Math.max(0, parseInt(el.textContent || '0') || 0);
                const next = Math.max(0, cur - delta);
                el.textContent = next;
                el.style.display = next > 0 ? 'inline-block' : 'none';
            });
        }

        // Create badges if missing
        (function ensureBadgesExist() {
            // Profile badge: attach next to profile image
            if (!document.getElementById(PROFILE_BADGE_ID)) {
                const profileImg = document.querySelector('.volunteer-profile-img');
                if (profileImg) {
                    const parent = profileImg.parentElement || profileImg;
                    // ensure parent is positioned for absolute child
                    if (getComputedStyle(parent).position === 'static') parent.style.position = 'relative';
                    const span = document.createElement('span');
                    span.id = PROFILE_BADGE_ID;
                    span.className = 'profile-notif-badge ' + GENERIC_CLASS;
                    // small style default (you already have .profile-notif-badge in CSS; keep minimal inline so it's visible)
                    span.style.cssText =
                        'display:none;position:absolute;top:-6px;right:-6px;min-width:20px;height:20px;line-height:18px;padding:0 6px;border-radius:999px;background:#dc3545;color:#fff;font-size:0.7rem;font-weight:600;text-align:center;pointer-events:none;box-shadow:0 1px 3px rgba(0,0,0,0.15);';
                    span.textContent = '0';
                    parent.appendChild(span);
                }
            }

            // Tab badge: attach to notifications link (use stable class)
            if (!document.getElementById(TAB_BADGE_ID)) {
                const notifLink = document.querySelector('.volunteer-notifications-link');
                if (notifLink && !notifLink.querySelector('#' + TAB_BADGE_ID) && !notifLink.querySelector(
                        '.' + GENERIC_CLASS)) {
                    const spanTab = document.createElement('span');
                    spanTab.id = TAB_BADGE_ID;
                    spanTab.className = GENERIC_CLASS;
                    spanTab.style.cssText =
                        'background:#dc3545;color:#fff;border-radius:999px;padding:2px 6px;font-size:0.7rem;margin-left:8px;display:none;vertical-align:middle;';
                    spanTab.textContent = '0';
                    notifLink.appendChild(spanTab);
                }
            }
        })();

        // CSRF + fetch unread (keep your existing route strings)
        const csrfToken = (document.querySelector('meta[name="csrf-token"]') || {}).getAttribute?.('content') ||
            '';

        async function initUnreadCount() {
            try {
                const resp = await fetch("{{ route('volunteer.notifications.unreadCount') }}", {
                    credentials: 'same-origin',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                if (!resp.ok) {
                    console.warn('initUnreadCount: server returned', resp.status);
                    return;
                }
                const json = await resp.json();
                const count = parseInt(json.unread || 0);
                setBadges(count);
            } catch (err) {
                console.warn('Could not fetch unread count', err);
            }
        }
        initUnreadCount();

        // ---------- Notifications page actions: mark as read / mark all ----------
        async function markAsRead(id, elButton) {
            if (!id) return;
            try {
                if (elButton) elButton.disabled = true;
                const url = "{{ url('/volunteer/notifications') }}/" + encodeURIComponent(id) +
                    "/mark-as-read";
                const resp = await fetch(url, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({})
                });
                if (!resp.ok) {
                    const text = await resp.text();
                    throw new Error('Server error: ' + resp.status + ' ' + text);
                }

                // update row DOM
                const row = document.querySelector(`.notification-item[data-id="${id}"]`);
                if (row) {
                    row.classList.remove('notification-unread');
                    const btn = row.querySelector('.btn-mark-read');
                    if (btn) btn.replaceWith(Object.assign(document.createElement('span'), {
                        className: 'small text-muted',
                        textContent: 'Read'
                    }));
                }

                // decrement badges everywhere
                decrementBadges(1);

                // flash (if page has notif-flash-area)
                const area = document.getElementById('notif-flash-area');
                if (area) {
                    const el = document.createElement('div');
                    el.className = 'alert alert-success';
                    el.textContent = 'Marked as read';
                    area.prepend(el);
                    setTimeout(() => el.remove(), 2400);
                }
            } catch (err) {
                console.error('markAsRead error', err);
                if (elButton) elButton.disabled = false;
                const area = document.getElementById('notif-flash-area');
                if (area) {
                    const el = document.createElement('div');
                    el.className = 'alert alert-danger';
                    el.textContent = 'Failed to mark read';
                    area.prepend(el);
                    setTimeout(() => el.remove(), 2400);
                }
            }
        }

        async function markAllRead() {
            try {
                const url = "{{ route('volunteer.notifications.markAllRead') }}";
                const resp = await fetch(url, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({})
                });
                if (!resp.ok) throw new Error('Server returned ' + resp.status);

                // update list rows
                document.querySelectorAll('.notification-item.notification-unread').forEach(row => {
                    row.classList.remove('notification-unread');
                    const btn = row.querySelector('.btn-mark-read');
                    if (btn) btn.replaceWith(Object.assign(document.createElement('span'), {
                        className: 'small text-muted',
                        textContent: 'Read'
                    }));
                });

                // clear badges
                setBadges(0);

                const area = document.getElementById('notif-flash-area');
                if (area) {
                    const el = document.createElement('div');
                    el.className = 'alert alert-success';
                    el.textContent = 'All notifications marked as read';
                    area.prepend(el);
                    setTimeout(() => el.remove(), 2400);
                }
            } catch (err) {
                console.error('markAllRead error', err);
                const area = document.getElementById('notif-flash-area');
                if (area) {
                    const el = document.createElement('div');
                    el.className = 'alert alert-danger';
                    el.textContent = 'Failed to mark all read';
                    area.prepend(el);
                    setTimeout(() => el.remove(), 2400);
                }
            }
        }

        // ---------- Click delegation (buttons in notifications page) ----------
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-mark-read');
            if (btn) {
                const id = btn.dataset.id;
                markAsRead(id, btn);
                return;
            }
            const allBtn = e.target.closest('#btn-mark-all');
            if (allBtn) {
                markAllRead();
                return;
            }
        });

        // ---------- Echo: realtime notifications ----------
        @if (auth()->check())
            if (window.Echo && window.Echo.private) {
                window.Echo.private(`App.Models.User.{{ auth()->id() }}`)
                    .notification(function(notification) {
                        try {
                            const payload = notification.data || notification;
                            const msg = payload.message || payload.body || 'New notification';
                            const status = payload.status || '';
                            const createdAt = payload.created_at || new Date().toLocaleString();

                            // If notifications page present, prepend the item
                            const container = document.getElementById('notifications-list');
                            if (container) {
                                const wrapper = document.createElement('div');
                                wrapper.className = 'list-group-item notification-item notification-unread';
                                const statusHtml = status ?
                                    `<span class="me-2 text-capitalize small text-muted">[${escapeHtml(status)}]</span>` :
                                    '';
                                const nid = notification.id || payload.id || ('n-' + Math.floor(Math
                                .random() * 100000));
                                wrapper.setAttribute('data-id', nid);
                                wrapper.innerHTML = `
                <div class="d-flex align-items-start justify-content-between">
                  <div>
                    <div class="fw-semibold mb-1">${statusHtml}${escapeHtml(msg)}</div>
                    <div class="small text-muted">${escapeHtml(createdAt)}</div>
                  </div>
                  <div class="text-end">
                    <button class="btn btn-sm btn-outline-success btn-mark-read" data-id="${nid}">Mark read</button>
                  </div>
                </div>
              `;
                                container.prepend(wrapper);
                            }

                            // Update badges everywhere
                            bumpBadges(1);
                            console.log('Realtime notification handled:', notification);
                        } catch (err) {
                            console.error('Realtime notification handling failed', err);
                        }
                    });
            } else {
                console.warn('Echo not initialized or window.Echo.private not available.');
            }
        @endif

        // ---------- Simple escape helper ----------
        function escapeHtml(s) {
            if (!s) return '';
            return String(s)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        // ---------- Keep mobile menu & nav active behavior (safe to re-run) ----------
        try {
            const mobileMenuBtn = document.getElementById('volunteerMobileMenuBtn');
            const mobileMenuContainer = document.getElementById('volunteerMobileMenuContainer');

            if (mobileMenuBtn && mobileMenuContainer) {
                mobileMenuBtn.addEventListener('click', function() {
                    mobileMenuContainer.classList.toggle('active');
                    const icon = this.querySelector('i');
                    if (icon) {
                        icon.classList.toggle('fa-times');
                        icon.classList.toggle('fa-bars');
                    }
                });

                document.addEventListener('click', function(e) {
                    const isClickInside = mobileMenuContainer.contains(e.target) || (mobileMenuBtn &&
                        mobileMenuBtn.contains(e.target));
                    if (!isClickInside && mobileMenuContainer.classList.contains('active')) {
                        mobileMenuContainer.classList.remove('active');
                        const icon = mobileMenuBtn.querySelector('i');
                        if (icon) {
                            icon.classList.remove('fa-times');
                            icon.classList.add('fa-bars');
                        }
                    }
                });
            }

            document.querySelectorAll('.volunteer-nav-link').forEach(link => {
                link.addEventListener('click', function() {
                    document.querySelectorAll('.volunteer-nav-link').forEach(l => l.classList
                        .remove('active'));
                    this.classList.add('active');
                    if (mobileMenuContainer && mobileMenuContainer.classList.contains(
                        'active')) {
                        mobileMenuContainer.classList.remove('active');
                        const icon = mobileMenuBtn.querySelector('i');
                        if (icon) {
                            icon.classList.remove('fa-times');
                            icon.classList.add('fa-bars');
                        }
                    }
                });
            });
        } catch (err) {
            console.warn('Menu init error', err);
        }

        // ---------- Expose APIs for other scripts ----------
        window.__volunteerNotifications = {
            initUnreadCount,
            bumpBadges,
            setBadges,
            decrementBadges,
            markAsRead,
            markAllRead
        };

    }); // DOMContentLoaded end
</script>
