{{-- =========================
     Landing Badges Section
========================= --}}
<section class="landing-badges" id="rewards">
    <div class="container">

        {{-- Section Header --}}
        <div class="badges-header">
            <h2 class="badges-title">Unique Rewards Await You 🏅</h2>
            <p class="badges-subtitle">
                Earn badges as you volunteer and grow your impact.
            </p>
        </div>

        {{-- Badge Rail Wrapper --}}
        @if($badges->count() > 0)
            <div class="landing-badge-rail">
                <div class="landing-badge-grid">
                    @foreach($badges as $badge)
                        <div class="landing-badge-card">
                            <div class="badge-image-wrapper">
                                @if(!empty($badge->badgeImage) && file_exists(public_path($badge->badgeImage)))
                                    <img
                                        src="{{ asset($badge->badgeImage) }}"
                                        alt="{{ $badge->badgeName }}"
                                        class="landing-badge-image"
                                    >
                                @else
                                    <img
                                        src="{{ asset('images/badges/default-badge.jpg') }}"
                                        alt="Default badge"
                                        class="landing-badge-image"
                                    >
                                @endif
                            </div>

                            <h4 class="landing-badge-name">
                                {{ $badge->badgeName }}
                            </h4>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <p class="text-muted text-center">No badges available yet.</p>
        @endif

    </div>
</section>

{{-- =========================
     GSAP Script (PUSH)
========================= --}}
@push('scripts')
    {{-- GSAP CDN --}}
    <script src="https://unpkg.com/gsap@3/dist/gsap.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const rail = document.querySelector(".landing-badge-grid");

            if (!rail) return;

            /**
             * Duplicate badges for seamless loop
             */
            rail.innerHTML += rail.innerHTML;

            /**
             * Calculate width of one full set
             */
            const totalWidth = rail.scrollWidth / 2;

            /**
             * GSAP infinite horizontal loop
             */
            const tween = gsap.to(rail, {
                x: -totalWidth,
                duration: 25,     // ⬅️ speed (smaller = faster)
                ease: "none",
                repeat: -1
            });

            /**
             * Optional: Pause on hover (UX friendly)
             */
            rail.addEventListener("mouseenter", () => tween.pause());
            rail.addEventListener("mouseleave", () => tween.play());
        });
    </script>
@endpush
