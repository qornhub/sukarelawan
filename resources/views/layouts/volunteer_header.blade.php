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
                    <a href="#" class="volunteer-dropdown-item">
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
    <img src="{{ auth()->user()->volunteerProfile && auth()->user()->volunteerProfile->profilePhoto 
                    ? asset('images/profiles/' . auth()->user()->volunteerProfile->profilePhoto) 
                    : asset('images/default-profile.png') }}"
        alt="Profile Photo" class="volunteer-profile-img">

    <div class="volunteer-profile-info">
        <p class="volunteer-profile-name">{{ auth()->user()->name }}</p>
        <p class="volunteer-profile-role">{{ ucfirst(auth()->user()->role->roleName) }}</p>
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


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('volunteerMobileMenuBtn');
        const mobileMenuContainer = document.getElementById('volunteerMobileMenuContainer');

        mobileMenuBtn.addEventListener('click', function() {
            mobileMenuContainer.classList.toggle('active');

            // Change icon
            const icon = this.querySelector('i');
            if (mobileMenuContainer.classList.contains('active')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            const isClickInside = mobileMenuContainer.contains(e.target) || mobileMenuBtn.contains(e
                .target);

            if (!isClickInside && mobileMenuContainer.classList.contains('active')) {
                mobileMenuContainer.classList.remove('active');
                const icon = mobileMenuBtn.querySelector('i');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });

        // Set active nav link
        const navLinks = document.querySelectorAll('.volunteer-nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                navLinks.forEach(l => l.classList.remove('active'));
                this.classList.add('active');

                // Close mobile menu after selection
                if (mobileMenuContainer.classList.contains('active')) {
                    mobileMenuContainer.classList.remove('active');
                    const icon = mobileMenuBtn.querySelector('i');
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            });
        });
    });
</script>
