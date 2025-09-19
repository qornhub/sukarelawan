<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Registration</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS (via CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
      <link rel="stylesheet" href="{{ asset('css/events/event_show.css') }}">

    
    <style>
        /* HERO */
        

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
        // Expecting $registration passed from controller
        $event = $registration->event;
        $user = Auth::user();
        $volunteerProfile = $user ? $user->volunteerProfile : null;

        $eventImage = $event->eventImage ?? null;
        $eventHeroUrl = $eventImage ? asset('images/events/' . $eventImage) : asset('images/events/default-event.jpg');

        $start = $event->eventStart ? \Carbon\Carbon::parse($event->eventStart) : null;
        $end = $event->eventEnd ? \Carbon\Carbon::parse($event->eventEnd) : null;
    @endphp

    <!-- HERO -->
    <header class="hero" style="background-image: url('{{ $eventHeroUrl }}');">
        <div class="hero-overlay"></div>

        <div class="hero-content container">
            <div class="hero-text">
                <h1 class="hero-title">{{ $event->eventTitle ?? 'Untitled Event' }}</h1>
                <div class="hero-sub ">By {{ optional($event->organizer)->name ?? 'Organizer' }}</div>
            </div>
        </div>
    </header>
    
      

    <div class="container mb-5">
        <div class="row g-4">

            <!-- LEFT: edit form -->
            <div class="col-lg-8">
                {{-- Update route: adjust route name if yours is different --}}
                <form action="{{ route('volunteer.event.register.update', $registration->registration_id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Keep IDs as hidden for safety --}}
                    <input type="hidden" name="registration_id" value="{{ $registration->registration_id }}">
                    <input type="hidden" name="event_id" value="{{ $registration->event_id }}">

                    <div class="d-flex align-items-center mb-2">
                        <span class="section-number">1</span>
                        <div class="section-title mt-3">Your Details</div>
                    </div>

                    <div class="form-section">
                        {{-- show validation errors summary --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <strong>There are some problems with your input.</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $err)
                                        <li>{{ $err }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full Name</label>
                                <input id="name" type="text" name="name" class="form-control"
                                       value="{{ old('name', $registration->name ?? $user->name ?? '') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input id="email" type="email" name="email" class="form-control"
                                       value="{{ old('email', $registration->email ?? $user->email ?? '') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label for="contactNumber" class="form-label">Contact Number</label>
                                <input id="contactNumber" type="tel" name="contactNumber" class="form-control"
                                       value="{{ old('contactNumber', $registration->contactNumber ?? $volunteerProfile->contactNumber ?? '') }}" required>
                            </div>

                            <div class="col-md-3">
                                <label for="age" class="form-label">Age</label>
                                <input id="age" type="number" name="age" class="form-control" min="10" max="120"
                                       value="{{ old('age', $registration->age ?? $volunteerProfile->age ?? '') }}">
                            </div>

                            <div class="col-md-3">
                                <label for="gender" class="form-label">Gender</label>
                                @php $sg = old('gender', $registration->gender ?? $volunteerProfile->gender ?? ''); @endphp
                                <select id="gender" name="gender" class="form-control">
                                    <option value="">Choose Your Gender</option>
                                    <option value="male" {{ $sg == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ $sg == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ $sg == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label for="company" class="form-label">Company (Optional)</label>
                                <input id="company" type="text" name="company" class="form-control"
                                       value="{{ old('company', $registration->company ?? '') }}" placeholder="Enter your Company">
                            </div>

                            <div class="col-12">
                                <label for="address" class="form-label">Home Address</label>
                                <textarea id="address" name="address" class="form-control" rows="2">{{ old('address', $registration->address ?? $volunteerProfile->address ?? '') }}</textarea>
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
                                <label for="emergencyContact" class="form-label">Name</label>
                                <input id="emergencyContact" type="text" name="emergencyContact" class="form-control"
                                       value="{{ old('emergencyContact', $registration->emergencyContact ?? '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="emergencyContactNumber" class="form-label">Phone Number</label>
                                <input id="emergencyContactNumber" type="tel" name="emergencyContactNumber" class="form-control"
                                       value="{{ old('emergencyContactNumber', $registration->emergencyContactNumber ?? '') }}" required>
                            </div>
                            <div class="col-12">
                                <label for="contactRelationship" class="form-label">Relationship</label>
                                <input id="contactRelationship" type="text" name="contactRelationship" class="form-control"
                                       value="{{ old('contactRelationship', $registration->contactRelationship ?? '') }}" required>
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
                                <label for="volExp" class="form-label">Have you Volunteered Before</label>
                                <select id="volExp" name="volunteeringExperience" class="form-control">
                                    @php $ve = old('volunteeringExperience', $registration->volunteeringExperience ?? ''); @endphp
                                    <option value="">Choose</option>
                                    <option value="yes" {{ $ve == 'yes' ? 'selected' : '' }}>Yes</option>
                                    <option value="no" {{ $ve == 'no' ? 'selected' : '' }}>No</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="skill" class="form-label">Skills</label>
                                <input id="skill" type="text" name="skill" class="form-control"
                                       value="{{ old('skill', $registration->skill ?? $volunteerProfile->skill ?? '') }}" placeholder="e.g. First Aid, Crowd Management">
                            </div>

                            <div class="col-12 text-start mt-3 d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                <a href="{{ route('volunteer.profile.registrationEditDelete', $event) }}" class="btn btn-outline-secondary">Cancel</a>

                                
                                
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
                    <div class="muted small">Date:
                        <span>
                            {{ $start ? $start->format('j M Y') : 'TBA' }}
                            @if($start && $end)
                                — {{ $end->format('j M Y') }}
                            @endif
                        </span>
                    </div>

                    <div class="muted small">Time:
                        <span>
                            {{ $start ? $start->format('g:i A') : '-' }}
                            @if($end)
                                — {{ $end->format('g:i A') }}
                            @endif
                        </span>
                    </div>

                    <div class="muted small">Location: <span>{{ $event->venueName ?? $event->eventLocation ?? 'TBA' }}</span></div>

                    <div class="mt-3"><strong>Capacity</strong></div>
                    <div class="muted small">
                        {{ $event->eventMaximum !== null ? $event->eventMaximum . ' volunteers' : 'Unlimited' }}
                    </div>

                    @if(!empty($event->points))
                        <div class="mt-2"><strong>Points</strong></div>
                        <div class="muted small">{{ $event->points }} pts</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- bootstrap js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
