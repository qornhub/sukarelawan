<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register for Event</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS (via CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
 <link rel="stylesheet" href="{{ asset('css/events/event_show.css') }}">
    <style>
        
        
        /* Layout */
        .section-number {
            width:40px; height:40px; border-radius:50%; background:#0d6efd; color:#fff; display:inline-flex; align-items:center; justify-content:center; font-weight:700;
            margin-right:12px; box-shadow:0 4px 14px rgba(13,110,253,0.15);
        }
        .section-title { font-size:1.25rem; font-weight:700; margin-bottom:16px; }
        .form-section { padding:18px 22px; border-radius:8px; background:#fff; box-shadow:0 6px 24px rgba(3,0,0,0.04); margin-bottom:18px; }

        /* Organizer card */
        .organizer-card { border:1px solid #e6e9ef; padding:18px; border-radius:8px; background:#fff; }
        .organizer-avatar { width:48px; height:48px; border-radius:50%; background:#eef2ff; display:inline-flex; align-items:center; justify-content:center; font-weight:700; color:#0b5ed7; }
        .organizer-actions .btn { width:100%; }

        /* Small helpers */
        .muted { color:#6c757d; }
        .form-label { font-weight:600; font-size:0.95rem; }
        
        .col-lg-4 .organizer-card {
  margin-top: 69px; /* desktop: adjust the px value to tune alignment */
}
        /* Responsive tweaks */
        @media (max-width: 768px) {
            .hero { height:180px; }
        }
    </style>
</head>
<body class="bg-light">

    @include('layouts.volunteer_header')
    @include('layouts.messages')

    @php
        $eventImage = $event->eventImage ?? null;
        $eventHeroUrl = $eventImage ? asset('images/events/' . $eventImage) : asset('images/events/default-event.jpg');
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

    <div class="container mb-5">
        <div class="row g-4">
            <!-- LEFT: form (wide) -->
            <div class="col-lg-8">
                <form action="{{ route('volunteer.event.register.store', $event) }}" method="POST">
                    @csrf

                    <!-- optional fallback -->
    <input type="hidden" name="event_id" value="{{ $event->event_id }}">
                    <!-- Section 1: Your Details -->
                    <div class="d-flex align-items-center mb-2">
                        <span class="section-number">1</span>
                        <div class="section-title mt-3">Your Details</div>
                    </div>

                    <div class="form-section">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name ?? '') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email ?? '') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Contact Number</label>
                                <input type="text" name="contactNumber" class="form-control" value="{{ old('contactNumber', $volunteerProfile->contactNumber ?? '') }}" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Age</label>
                                <input type="number" name="age" class="form-control" min="10" max="120" value="{{ old('age', $volunteerProfile->age ?? '') }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Gender</label>
                                <select name="gender" class="form-control">
                                    @php $sg = old('gender', $volunteerProfile->gender ?? ''); @endphp
                                    <option value="">Choose Your Gender</option>
                                    <option value="male" {{ $sg=='male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ $sg=='female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ $sg=='other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Company (Optional)</label>
                                <input type="text" name="company" class="form-control" value="{{ old('company') }}" placeholder="Enter your Company">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Home Address</label>
                                <textarea name="address" class="form-control" rows="2">{{ old('address', $volunteerProfile->address ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Emergency Contact -->
                    <div class="d-flex align-items-center mt-3 mb-2">
                        <span class="section-number">2</span>
                        <div class="section-title mt-3">Emergency Contact</div>
                    </div>

                    <div class="form-section">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input type="text" name="emergencyContact" class="form-control" value="{{ old('emergencyContact') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="emergencyContactNumber" class="form-control" value="{{ old('emergencyContactNumber') }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Relationship</label>
                                <input type="text" name="contactRelationship" class="form-control" value="{{ old('contactRelationship') }}" required>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Skills & Experience -->
                    <div class="d-flex align-items-center mt-3 mb-2">
                        <span class="section-number">3</span>
                        <div class="section-title mt-3">Skills & Experience</div>
                    </div>

                    <div class="form-section">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-6">
                                <label class="form-label">Have you Volunteered Before</label>
                                <select name="volunteeringExperience" class="form-control">
                                    <option value="">Choose</option>
                                    <option value="yes" {{ old('volunteeringExperience')=='yes' ? 'selected':'' }}>Yes</option>
                                    <option value="no" {{ old('volunteeringExperience')=='no' ? 'selected':'' }}>No</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Skills</label>
                                <input type="text" name="skill" class="form-control" value="{{ old('skill', $volunteerProfile->skill ?? '') }}" placeholder="e.g. First Aid, Crowd Management">
                            </div>

                            <div class="col-12 text-start mt-3">
                                <button type="submit" class="btn btn-primary">Register Now</button>
                            </div>
                        </div>
                    </div>

                </form>
            </div>

            <!-- RIGHT: organizer card + quick info -->
            <div class="col-lg-4">
                <div class="organizer-card mb-3 mt-6">
                    <div class="d-flex align-items-center gap-3">
                        <div class="organizer-avatar">{{ strtoupper(substr(optional($event->organizer)->name ?? 'O',0,1)) }}</div>
                        <div>
                            <div style="font-weight:700">{{ optional($event->organizer)->name ?? 'Organizer' }}</div>
                            <div class="muted small">{{ optional($event->organizer)->organization ?? '' }}</div>
                        </div>
                    </div>

                    <div class="mt-3 organizer-actions">
                        <a href="#" class="btn btn-primary mb-2">WhatsApp Organizer</a>
                        <a href="#" class="btn btn-outline-primary">View Profile</a>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
