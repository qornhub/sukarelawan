<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Registration</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS (via CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="{{ asset('css/events/event_show.css') }}">

    <style>
        /* ---------------------------
           Section number + title
        --------------------------- */
        .section-number {
            background-color: var(--primary-color) !important;
            color: #fff !important;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1rem;
            margin-right: 12px;
            box-shadow: 0 6px 18px rgba(0, 74, 173, 0.12);
        }

        .section-title {
            color: var(--primary-color) !important;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .form-section {
            padding: 18px 22px;
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 6px 24px rgba(3, 0, 0, 0.04);
            margin-bottom: 18px;
        }

        /* Organizer card */
        .organizer-card {
            border: 1px solid #e6e9ef;
            padding: 18px;
            border-radius: 8px;
            background: #fff;
        }

        .organizer-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #eef2ff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #0b5ed7;
        }

        .organizer-actions .btn {
            width: 100%;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.95rem;
        }

        /* Primary button consistency */
        .btn.btn-primary {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: #fff !important;
            font-weight: 600;
            padding: 10px 18px;
            border-radius: 8px;
            transition: background-color 0.15s ease, transform 0.1s ease;
        }

        .btn.btn-primary:hover {
            background-color: var(--primary-hover) !important;
            border-color: var(--primary-hover) !important;
            transform: translateY(-1px);
        }

        .btn.btn-primary:active,
        .btn.btn-primary:focus,
        .btn.btn-primary:focus-visible {
            background-color: var(--primary-hover) !important;
            border-color: var(--primary-hover) !important;
            box-shadow: none !important;
            outline: none !important;
        }

        /* Outline button */
        .btn-outline-secondary {
            border-radius: 8px;
            padding: 10px 18px;
        }

        /* Right column spacing match create.blade */
        @media (min-width: 992px) {
            .col-lg-4 {
                display: flex;
                flex-direction: column;
            }

            .col-lg-4 .organizer-card {
                flex: 0 0 auto;
            }

            .col-lg-4 .form-section:last-child {
                flex: 1;
                margin-top: 1rem;
            }
        }

        @media (max-width: 991px) {
            .col-lg-4 .organizer-card {
                margin-top: 0;
            }
        }
    </style>

</head>

<body class="bg-light">

    @include('layouts.volunteer_header')

    @php
        $event = $registration->event;
        $user = Auth::user();
        $volunteerProfile = $user ? $user->volunteerProfile : null;

        $eventImage = $event->eventImage ?? null;
        $eventHeroUrl = $eventImage ? asset('images/events/' . $eventImage) : asset('assets/default_event.jpg');
    @endphp

    <!-- HERO -->
    <header class="hero" style="background-image: url('{{ $eventHeroUrl }}');">
        <div class="hero-overlay"></div>

        <div class="hero-content container">
            <div class="hero-text">
                <h1 class="hero-title">{{ $event->eventTitle ?? 'Untitled Event' }}</h1>
                <div class="hero-sub">By {{ optional($event->organizer)->name ?? 'Organizer' }}</div>
            </div>
        </div>
    </header>

    @include('layouts.messages')

    <div class="container mt-4">
        <div class="row g-4 align-items-start">

            <!-- LEFT FORM -->
            <div class="col-lg-8">
                <form action="{{ route('volunteer.event.register.update', $registration->registration_id) }}"
                    method="POST" novalidate>
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="registration_id" value="{{ $registration->registration_id }}">
                    <input type="hidden" name="event_id" value="{{ $registration->event_id }}">

                    <!-- SECTION 1 -->
                    <div class="d-flex align-items-center mb-2">
                        <span class="section-number">1</span>
                        <div class="section-title">Your Details</div>
                    </div>

                    <div class="form-section">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" class="form-control"
                                    value="{{ old('name', $registration->name ?? ($user->name ?? '')) }}" required>
                                @error('name')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control"
                                    value="{{ old('email', $registration->email ?? ($user->email ?? '')) }}" required>
                                @error('email')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Contact Number</label>
                                <input type="text" name="contactNumber" class="form-control"
                                    value="{{ old('contactNumber', $registration->contactNumber ?? ($volunteerProfile->contactNumber ?? '')) }}"
                                    required>
                                @error('contactNumber')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Age</label>
                                <input type="number" name="age" class="form-control" min="16" max="120"
                                    value="{{ old('age', $registration->age ?? ($volunteerProfile->age ?? '')) }}"
                                    required>
                                @error('age')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Gender</label>
                                @php $sg = old('gender', $registration->gender ?? $volunteerProfile->gender ?? ''); @endphp
                                <select name="gender" class="form-control">
                                    <option value="">Choose Your Gender</option>
                                    <option value="male" {{ $sg == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ $sg == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ $sg == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Company (Optional)</label>
                                <input type="text" name="company" class="form-control"
                                    value="{{ old('company', $registration->company ?? '') }}">
                                @error('company')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Home Address</label>
                                <textarea name="address" class="form-control" rows="2">{{ old('address', $registration->address ?? ($volunteerProfile->address ?? '')) }}</textarea>
                                @error('address')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>

                    <!-- SECTION 2 -->
                    <div class="d-flex align-items-center mt-3 mb-2">
                        <span class="section-number">2</span>
                        <div class="section-title">Emergency Contact</div>
                    </div>

                    <div class="form-section">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input type="text" name="emergencyContact" class="form-control"
                                    value="{{ old('emergencyContact', $registration->emergencyContact ?? '') }}"
                                    required>
                                @error('emergencyContact')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="emergencyContactNumber" class="form-control"
                                    value="{{ old('emergencyContactNumber', $registration->emergencyContactNumber ?? '') }}"
                                    required>
                                @error('emergencyContactNumber')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Relationship</label>
                                <input type="text" name="contactRelationship" class="form-control"
                                    value="{{ old('contactRelationship', $registration->contactRelationship ?? '') }}"
                                    required>
                                @error('contactRelationship')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>

                    <!-- SECTION 3 -->
                    <div class="d-flex align-items-center mt-3 mb-2">
                        <span class="section-number">3</span>
                        <div class="section-title">Skills & Experience</div>
                    </div>

                    <div class="form-section">
                        <div class="row g-3 align-items-center">

                            <div class="col-md-6">
                                <label class="form-label">Have you Volunteered Before</label>
                                @php $ve = old('volunteeringExperience', $registration->volunteeringExperience ?? ''); @endphp
                                <select name="volunteeringExperience" class="form-control">
                                    <option value="">Choose</option>
                                    <option value="yes" {{ $ve == 'yes' ? 'selected' : '' }}>Yes</option>
                                    <option value="no" {{ $ve == 'no' ? 'selected' : '' }}>No</option>
                                </select>
                                @error('volunteeringExperience')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Skills</label>
                                <input type="text" name="skill" class="form-control"
                                    value="{{ old('skill', $registration->skill ?? ($volunteerProfile->skill ?? '')) }}">
                                @error('skill')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 text-start mt-4 d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                <a href="{{ route('volunteer.profile.registrationEditDelete', $event) }}"
                                    class="btn btn-outline-secondary">Cancel</a>
                            </div>

                        </div>
                    </div>

                </form>
            </div>

            <!-- RIGHT SIDEBAR -->
            <div class="col-lg-4">

                @php
                    use Illuminate\Support\Facades\Storage;

                    $default = asset('assets/default-profile.png');
                    $organizer = optional($event->organizer);

                    $file =
                        optional($organizer->ngoProfile)->profilePhoto ??
                        (optional($organizer->volunteerProfile)->profilePhoto ?? null);

                    $profileImageUrl = $default;

                    if ($file) {
                        $basename = basename($file);

                        if (file_exists(public_path("images/profiles/{$basename}"))) {
                            $profileImageUrl = asset("images/profiles/{$basename}");
                        } elseif (file_exists(public_path("images/{$basename}"))) {
                            $profileImageUrl = asset("images/{$basename}");
                        } elseif (Storage::disk('public')->exists($file)) {
                            $profileImageUrl = Storage::disk('public')->url($file);
                        } elseif (Storage::disk('public')->exists("profiles/{$basename}")) {
                            $profileImageUrl = Storage::disk('public')->url("profiles/{$basename}");
                        }
                    }
                @endphp

                <div class="organizer-card mb-3 mt-5">
                    <div class="d-flex align-items-center gap-3">

                        <div class="organizer-avatar">
                            <img src="{{ $profileImageUrl }}" alt="Organizer Image" class="rounded-circle"
                                style="width:60px;height:60px;object-fit:cover;">
                        </div>

                        <div>
                            <div style="font-weight:700">
                                {{ $organizer->name ?? 'Organizer' }}
                            </div>
                            <div class="muted small">
                                {{ optional($organizer->organization)->name ?? '' }}
                            </div>
                        </div>

                    </div>

                    <div class="organizer-actions mt-4">
                        @php
                            $phone = optional($event->organizer)->phone ?? null;
                            $waLink = $phone
                                ? "https://wa.me/{$phone}"
                                : 'https://wa.me/?text=' .
                                    urlencode('Hello, I am interested in your event: ' . $event->eventTitle);
                        @endphp

                        <a href="{{ $waLink }}" target="_blank" class="btn-organizer primary">
                            <i class="fab fa-whatsapp me-2"></i>WhatsApp Organizer
                        </a>

                        @php
                            $profileId = $organizer->ngo_id ?? ($organizer->id ?? '#');
                        @endphp

                        <a href="{{ route('ngo.profile.show', ['id' => $profileId]) }}"
                            class="btn-organizer secondary">
                            <i class="fas fa-user me-2"></i>View Profile
                        </a>
                    </div>
                </div>

                <div class="form-section">
                    <div class="mb-2"><strong>Event Details</strong></div>
                    <div class="muted small">Date: <span>{{ $event->eventStart ?? 'TBA' }}</span></div>
                    <div class="muted small">Location: <span>{{ $event->venueName ?? 'TBA' }}</span></div>

                    <div class="mt-3"><strong>Capacity</strong></div>
                    <div class="muted small">{{ $event->eventMaximum ?? 'Unlimited' }} volunteers</div>
                </div>

            </div>

        </div>
    </div>

    <!-- bootstrap js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
