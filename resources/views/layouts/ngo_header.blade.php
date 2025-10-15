{{-- resources/views/layouts/ngo_header.blade.php --}}
<div class="ngo-header-component">
    @php
        use Illuminate\Support\Str;
        use Illuminate\Support\Facades\Auth;

        $user = Auth::user();
        $ngoProfile = optional($user)->ngoProfile; // NGOProfile or null

        // Name: prefer organizationName from NGO profile, then user's name, then Anonymous
$name = $ngoProfile->organizationName ?? ($user->name ?? 'Anonymous');

// Role: normalized to lowercase (fallback to 'ngo')
$roleNameLower = strtolower(optional($user->role)->roleName ?? 'ngo');

// Profile photo filename (as stored in DB) or null
$filename = $ngoProfile->profilePhoto ?? null;

// Default avatar
$profileImageUrl = asset('images/default-profile.png');

if ($filename) {
    $basename = basename($filename);

    // candidate filesystem paths (public and storage)
    $paths = [
        public_path("images/profiles/{$filename}"),
        public_path("images/profiles/{$basename}"),
        public_path("images/{$filename}"),
        public_path("storage/{$filename}"),
        public_path("storage/profiles/{$basename}"),
    ];

    // map paths to asset URL templates to use when found
    $assetTemplates = [
        asset("images/profiles/{$filename}"),
        asset("images/profiles/{$basename}"),
        asset("images/{$filename}"),
        asset("storage/{$filename}"),
        asset("storage/profiles/{$basename}"),
    ];

    foreach ($paths as $i => $p) {
        if (file_exists($p)) {
            $profileImageUrl = $assetTemplates[$i];
            break;
        }
    }

    // If none existed but the stored value looks like a storage path, prefer storage url
    if ($profileImageUrl === asset('images/default-profile.png')) {
        if (
            Str::startsWith($filename, 'profiles/') ||
            Str::startsWith($filename, 'covers/') ||
            Str::contains($filename, 'storage')
        ) {
            $profileImageUrl = asset('storage/' . ltrim($filename, '/'));
        } else {
            // fallback: try images/profiles with basename
            $profileImageUrl = asset('images/profiles/' . $basename);
                }
            }
        }
    @endphp


    <!-- NGO Header -->
    <header class="ngo-header">
        <a href="{{ route('ngo.events.index') }}" class="ngo-logo-section">
            <img src="{{ asset('images/sukarelawan_logo.png') }}" alt="Logo">
            <h4 class="ngo-logo-title">SukaRelawan</h4>
        </a>

        <!-- Desktop Navigation and Profile -->
        <nav class="ngo-nav-section">
            <a href="{{ route('ngo.dashboard') }}"
                class="ngo-nav-link {{ request()->routeIs('ngo.dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i> Home
            </a>

            <a href="{{ route('ngo.events.index') }}"
                class="ngo-nav-link {{ request()->routeIs('ngo.events.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt"></i> Event
            </a>

            <a href="{{ route('blogs.index') }}"
                class="ngo-nav-link {{ request()->routeIs('blogs.index') ? 'active' : '' }}">
                <i class="fas fa-blog"></i> Blog
            </a>

        </nav>

        <div class="desktop-profile">
            <div class="ngo-profile-section">
                <img src="{{ $profileImageUrl }}" alt="Profile Photo" class="ngo-profile-img">

                <div class="ngo-profile-info">
                    <p class="ngo-profile-name">{{ $name }}</p>
                    <p class="ngo-profile-role">{{ ucfirst($roleNameLower ?? 'ngo') }}</p>
                </div>

                <div class="ngo-profile-dropdown">
                    @auth
                        @if ($roleNameLower === 'ngo' && Route::has('ngo.profile.self'))
                            <a href="{{ route('ngo.profile.self') }}" class="ngo-dropdown-item">
                                <i class="fas fa-user-circle"></i> My Profile
                            </a>
                        @endif
                    @endauth

                    <a href="#" class="ngo-dropdown-item">
                        <i class="fas fa-cog"></i> Account Settings
                    </a>
                    <a href="#" class="ngo-dropdown-item">
                        <i class="fas fa-bell"></i> Notifications
                    </a>
                    <div class="ngo-dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout.ngo') }}">
                        @csrf
                        <button type="submit" class="ngo-dropdown-item">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>

                </div>
            </div>
        </div>

        <!-- Mobile Menu Button -->
        <button class="ngo-mobile-menu-btn" id="ngoMobileMenuBtn">
            <i class="fas fa-bars"></i>
        </button>
    </header>

    <!-- Mobile Menu Container -->
    <div class="ngo-mobile-menu-container" id="ngoMobileMenuContainer">
        <nav class="ngo-nav-section">
            <a href="{{ route('ngo.events.index') }}" class="ngo-nav-link active"><i class="fas fa-home"></i> Home</a>
            <a href="{{ route('ngo.events.index') }}" class="ngo-nav-link"><i class="fas fa-calendar-alt"></i>
                Event</a>
            <a href="{{ route('blogs.index') }}" class="ngo-nav-link"><i class="fas fa-blog"></i> Blog</a>
        </nav>

        <div class="ngo-profile-section">
            <img src="{{ $profileImageUrl }}" alt="Profile Photo" class="ngo-profile-img">

            <div class="ngo-profile-info">
                <p class="ngo-profile-name">{{ $name }}</p>
                <p class="ngo-profile-role">{{ ucfirst($roleNameLower ?? 'ngo') }}</p>
            </div>
        </div>

        <div class="ngo-mobile-menu-actions">
            @auth
                @if ($roleNameLower === 'ngo' && Route::has('ngo.profile.self'))
                    <a href="{{ route('ngo.profile.self') }}" class="ngo-dropdown-item">
                        <i class="fas fa-user-circle"></i> My Profile
                    </a>
                @endif
            @endauth
            <a href="#" class="ngo-dropdown-item"><i class="fas fa-cog"></i> Account Settings</a>
            <a href="#" class="ngo-dropdown-item"><i class="fas fa-bell"></i> Notifications</a>
            <form method="POST" action="{{ route('logout.ngo') }}">
                @csrf
                <button type="submit" class="ngo-dropdown-item">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>


        </div>
    </div>
</div>

<style>
    /* exactly same visual tokens & rules as volunteer header but namespaced to ngo- */
    .ngo-header-component {
        --primary-color: #004AAD;
        --primary-light: #3a506b;
        --accent-color: #3498db;
        --text-light: #f8f9fa;
        --text-gray: #6c757d;
        --border-color: #e0e0e0;
        --hover-bg: rgba(255, 255, 255, 0.1);
        --transition: all 0.3s ease;
        --shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .ngo-header-component * {
        box-sizing: border-box;
    }

    .ngo-header-component .ngo-header {
        background: white;
        padding: 0.75rem 2rem;
        box-shadow: var(--shadow);
        position: sticky;
        top: 0;
        z-index: 1000;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        margin: 0;
    }

    .ngo-header-component .ngo-logo-section {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        text-decoration: none;
        transition: var(--transition);
    }

    .ngo-header-component .ngo-logo-section:hover {
        transform: translateY(-2px);
    }

    .ngo-header-component .ngo-logo-section img {
        height: 40px;
        transition: var(--transition);
    }

    .ngo-header-component .ngo-logo-title {
        font-weight: 700;
        color: var(--primary-color);
        margin: 0;
        font-size: 1.35rem;
    }

    .ngo-header-component .ngo-nav-section {
        display: flex;
        gap: 4rem;
    }

    .ngo-header-component .ngo-nav-link {
        position: relative;
        color: var(--text-gray);
        text-decoration: none;
        font-weight: 500;
        padding: 0.5rem 0;
        transition: var(--transition);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .ngo-header-component .ngo-nav-link i {
        font-size: 0.9rem;
        opacity: 0.8;
    }

    .ngo-header-component .ngo-nav-link:hover,
    .ngo-header-component .ngo-nav-link.active {
        color: var(--primary-color);
    }

    .ngo-header-component .ngo-nav-link:hover::after,
    .ngo-header-component .ngo-nav-link.active::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: var(--accent-color);
        border-radius: 2px;
    }

    .ngo-header-component .ngo-profile-section {
        display: flex;
        align-items: center;
        gap: 1rem;
        position: relative;
        cursor: pointer;
    }

    .ngo-header-component .ngo-profile-img {
        height: 40px;
        width: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e0e0e0;
        transition: var(--transition);
    }

    .ngo-header-component .ngo-profile-section:hover .ngo-profile-img {
        border-color: var(--accent-color);
        transform: scale(1.05);
    }

    .ngo-header-component .ngo-profile-info {
        text-align: right;
    }

    .ngo-header-component .ngo-profile-name {
        font-weight: 600;
        color: var(--primary-color);
        margin: 0;
        font-size: 0.95rem;
    }

    .ngo-header-component .ngo-profile-role {
        font-size: 0.8rem;
        color: var(--text-gray);
        margin: 0;
    }

    .ngo-header-component .ngo-profile-dropdown {
        position: absolute;
        top: 100%;
        right: -1rem;
        width: 200px;
        background: white;
        border-radius: 8px;
        box-shadow: var(--shadow);
        padding: 0.5rem 0;
        margin-top: 1rem;
        opacity: 0;
        visibility: hidden;
        transform: translateY(10px);
        transition: var(--transition);
        z-index: 1000;

        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .ngo-header-component .ngo-profile-section:hover .ngo-profile-dropdown {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .ngo-header-component .ngo-dropdown-item {
        padding: 0.75rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: var(--text-gray);
        text-decoration: none;
        transition: var(--transition);
        background: none;
        border: none;
        width: 100%;
        text-align: left;
    }

    .ngo-header-component .ngo-dropdown-item:hover {
        background: #f8f9fa;
        color: var(--primary-color);
    }

    .ngo-header-component .ngo-dropdown-divider {
        height: 1px;
        background: var(--border-color);
        margin: 0.5rem 0;
    }

    /* Mobile menu */
    .ngo-header-component .ngo-mobile-menu-btn {
        display: none;
        background: none;
        border: none;
        font-size: 1.5rem;
        color: var(--primary-color);
        cursor: pointer;
        position: absolute;
        right: 1.5rem;
        top: 1rem;
    }

    .ngo-header-component .ngo-mobile-menu-container {
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

    .ngo-header-component .ngo-mobile-menu-container.active {
        transform: translateX(0);
    }

    .ngo-header-component .ngo-mobile-menu-container .ngo-nav-section {
        flex-direction: column;
        gap: 0;
        margin-bottom: 1.5rem;
    }

    .ngo-header-component .ngo-mobile-menu-container .ngo-nav-link {
        padding: 1.1rem 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .ngo-header-component .ngo-mobile-menu-container .ngo-profile-section {
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 8px;
        flex-direction: row;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .ngo-header-component .ngo-mobile-menu-container .ngo-profile-info {
        text-align: left;
        flex-grow: 1;
    }

    .ngo-header-component .ngo-mobile-menu-actions .ngo-dropdown-item {
        padding: 1.1rem 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: var(--text-gray);
        text-decoration: none;
        transition: var(--transition);
        border-bottom: 1px solid #f0f0f0;
    }

    .ngo-header-component .ngo-mobile-menu-actions .ngo-dropdown-item:last-child {
        border-bottom: none;
    }

    /* Responsive */
    @media (max-width: 992px) {
        .ngo-header-component .ngo-header {
            padding: 0.75rem 1.5rem;
        }

        .ngo-header-component .ngo-nav-section {
            gap: 1rem;
        }
    }

    @media (max-width: 768px) {
        .ngo-header-component .ngo-header {
            padding: 0.75rem 1rem;
            position: relative;
        }

        .ngo-header-component .ngo-mobile-menu-btn {
            display: block;
        }

        .ngo-header-component .ngo-nav-section,
        .ngo-header-component .desktop-profile {
            display: none;
        }

        .ngo-header-component .ngo-mobile-menu-container {
            display: block;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuBtn = document.getElementById('ngoMobileMenuBtn');
        const mobileMenuContainer = document.getElementById('ngoMobileMenuContainer');

        if (mobileMenuBtn && mobileMenuContainer) {
            mobileMenuBtn.addEventListener('click', function() {
                mobileMenuContainer.classList.toggle('active');

                const icon = this.querySelector('i');
                if (mobileMenuContainer.classList.contains('active')) {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                } else {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            });

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

            // Set active nav link behavior
            const navLinks = document.querySelectorAll('.ngo-nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    navLinks.forEach(l => l.classList.remove('active'));
                    this.classList.add('active');

                    if (mobileMenuContainer.classList.contains('active')) {
                        mobileMenuContainer.classList.remove('active');
                        const icon = mobileMenuBtn.querySelector('i');
                        icon.classList.remove('fa-times');
                        icon.classList.add('fa-bars');
                    }
                });
            });
        }
    });
</script>
