{{-- resources/views/ngo/events/create.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Create Event — NGO</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/events/create_events.css') }}">

    
</head>


<body>
    @include('layouts.ngo_header')
 
   
            <main class="container-fluid page-wrapper">
    <div class="create-event-card">

        <header class="event-header">
            <div class="header-content">
                <h1 class="page-title">
                    <i class="fas fa-calendar-plus"></i>
                    <span>Start An Event</span>
                </h1>
                
                <nav class="nav-tabs">
                    <div class="nav-indicator" style="width: 88px; transform: translateX(0);"></div>
                    <a href="#" class="nav-tab active" data-tab="event">
                        <i class="fas fa-calendar-day"></i>
                        <span>Event</span>
                    </a>
                    <a href="#" class="nav-tab" data-tab="edit">
                        <i class="fas fa-edit"></i>
                        <span>Edit</span>
                    </a>
                    <a href="#" class="nav-tab" data-tab="manage">
                        <i class="fas fa-tasks"></i>
                        <span>Manage</span>
                    </a>
                </nav>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <a href="{{ route('ngo.events.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to my events
                    </a>
                </div>
            </div>
        </header>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <strong>There were some problems with your submission:</strong>
                    <ul class="mb-0">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('ngo.events.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf

                {{-- equal-height row with two equal columns --}}
                <div class="row align-items-stretch">
                    {{-- LEFT: main form fields (equal width) --}}
                    <div class="col-lg-6 col-12 col-equal">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="event_title" class="form-label">Event Title <span class="text-danger">*</span></label>
                                <input id="event_title" name="event_title" value="{{ old('event_title') }}" type="text"
                                    class="form-control @error('eventTitle') is-invalid @enderror @error('event_title') is-invalid @enderror" required>
                                @error('eventTitle') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                @error('event_title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                    <option value="">Select category</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->eventCategory_id ?? $cat->id }}" {{ (old('category_id') == ($cat->eventCategory_id ?? $cat->id)) ? 'selected' : '' }}>
                                            {{ $cat->eventCategoryName ?? ($cat->name ?? 'Category') }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="reward_points" class="form-label">Reward Points <span class="text-danger">*</span></label>
                                <input id="reward_points" name="reward_points" value="{{ old('reward_points') }}" type="number" min="0"
                                    class="form-control @error('eventPoints') is-invalid @enderror @error('reward_points') is-invalid @enderror" required>
                                @error('eventPoints') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                @error('reward_points') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Start Date & Time <span class="text-danger">*</span></label>
                                <input id="start_date" name="start_date" value="{{ old('start_date') }}" type="datetime-local"
                                    class="form-control @error('eventStart') is-invalid @enderror @error('start_date') is-invalid @enderror" required>
                                @error('eventStart') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="end_date" class="form-label">End Date & Time <span class="text-danger">*</span></label>
                                <input id="end_date" name="end_date" value="{{ old('end_date') }}" type="datetime-local"
                                    class="form-control @error('eventEnd') is-invalid @enderror @error('end_date') is-invalid @enderror" required>
                                @error('eventEnd') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label for="event_summary" class="form-label">Short Summary</label>
                                <textarea id="event_summary" name="event_summary" value="{{ old('event_summary') }}" type="text"
                                    class="form-control @error('eventSummary') is-invalid @enderror @error('event_summary') is-invalid @enderror" placeholder="Short summary (max 200 chars)"></textarea>
                                @error('eventSummary') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                @error('event_summary') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label for="event_description" class="form-label">Detailed Description <span class="text-danger">*</span></label>
                                <textarea id="event_description" name="event_description" rows="6"
                                    class="form-control @error('eventDescription') is-invalid @enderror @error('event_description') is-invalid @enderror" required>{{ old('event_description') }}</textarea>
                                @error('eventDescription') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                @error('event_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Requirements --}}
                            <div class="col-12">
                                <label for="requirements" class="form-label">Requirements <small class="text-muted">(what volunteers must bring / know)</small></label>
                                <textarea id="requirements" name="requirements" rows="4" class="form-control @error('requirements') is-invalid @enderror" placeholder="E.g. Must be 18+, bring gloves, comfortable walking shoes">{{ old('requirements') }}</textarea>
                                @error('requirements') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="form-note mt-1">This will be shown on the event page under Requirements.</div>
                            </div>

                            {{-- Expected Impact (larger textarea) --}}
                            <div class="col-12">
                                <label for="event_impact" class="form-label">Expected Impact / KPI</label>
                                <textarea id="event_impact" name="event_impact" rows="4" class="form-control @error('eventImpact') is-invalid @enderror" placeholder="Describe expected outcomes, KPIs, measurable goals (e.g. 'Clean 10 beaches, 500kg rubbish removed, 300 participants reached')">{{ old('event_impact') }}</textarea>
                                @error('eventImpact') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="form-note mt-1">This field supports multiple lines and will appear in the event details.</div>
                            </div>


                             <div class="col-md-6">
                                <label for="event_maximum" class="form-label">Maximum Participants</label>
                                <input id="event_maximum" name="event_maximum" value="{{ old('event_maximum') }}" type="number" min="0" class="form-control @error('eventMaximum') is-invalid @enderror">
                                <div class="form-note">Leave blank for unlimited.</div>
                                @error('eventMaximum') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Skills (left column) --}}
                            @php $oldSkills = old('skills', []); @endphp
                            <div class="col-md-6">
                                <label for="skillsDropdown" class="form-label">Event Skills (optional)</label>

                                <div id="skillsDropdown" class="multi-select-dropdown">
                                    <button type="button" id="skillsToggle" class="form-control" aria-haspopup="listbox" aria-expanded="false">
                                        <span id="skillsLabel">Select skills</span>
                                        <span><i class="fas fa-caret-down"></i></span>
                                    </button>

                                    <div id="skillsPanel" class="msd-panel d-none" role="listbox" aria-multiselectable="true">
                                        <div class="msd-search">
                                            <input type="search" id="skillsSearch" placeholder="Search skills..." class="form-control form-control-sm">
                                        </div>

                                        <ul class="list-unstyled msd-list p-2 mb-0" style="max-height:220px; overflow:auto;">
                                            @if(isset($skills) && $skills->count())
                                                @foreach($skills as $skill)
                                                    @php $sid = $skill->skill_id ?? $skill->id; @endphp
                                                    <li class="py-1">
                                                        <label class="form-check d-flex align-items-center gap-2 mb-0">
                                                            <input class="form-check-input skill-checkbox" type="checkbox" name="skills[]" value="{{ $sid }}" id="skill_{{ $sid }}" {{ in_array((string)$sid, (array)$oldSkills) ? 'checked' : '' }}>
                                                            <span class="form-check-label" for="skill_{{ $sid }}">{{ $skill->skillName ?? $skill->name }}</span>
                                                        </label>
                                                    </li>
                                                @endforeach
                                            @else
                                                <li class="text-muted">No skills available. Admin can add skills from dashboard.</li>
                                            @endif
                                        </ul>

                                        <div class="d-flex justify-content-between align-items-center p-2 border-top">
                                            <small class="text-muted" id="skillsCount">0 selected</small>
                                            <div>
                                                <button type="button" id="skillsClear" class="btn btn-sm btn-link">Clear</button>
                                                <button type="button" id="skillsDone" class="btn btn-sm btn-primary">Done</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @error('skills') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                <div class="form-note mt-1">Click to open — selected skills will be attached to the event.</div>
                            </div>

                            {{-- Location header --}}
                            <div class="col-12 pt-2">
                                <h5 class="mb-0">Location</h5>
                            </div>

                            <div class="col-md-6">
                                <label for="venue_name" class="form-label">Venue / Venue Name <span class="text-danger">*</span></label>
                                <input id="venue_name" name="venue_name" value="{{ old('venue_name') }}" type="text" class="form-control @error('venueName') is-invalid @enderror" required>
                                @error('venueName') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-2">
                                <label for="zip_code" class="form-label">ZIP</label>
                                <input id="zip_code" name="zip_code" value="{{ old('zip_code') }}" type="text" class="form-control @error('zipCode') is-invalid @enderror">
                                @error('zipCode') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                                <input id="city" name="city" value="{{ old('city') }}" type="text" class="form-control @error('city') is-invalid @enderror" required>
                                @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- state + country fill the area: each half width --}}
                            <div class="col-md-6">
                                <label for="state" class="form-label">State / Territory <span class="text-danger">*</span></label>
                                <select id="state" name="state" class="form-select @error('state') is-invalid @enderror" required>
                                    <option value="">Choose state</option>
                                    @php
                                        $states = ['Perlis','Kedah','Penang','Perak','Kelantan','Terengganu','Pahang','Selangor','Negeri Sembilan','Melaka','Johor','Sabah','Sarawak','Kuala Lumpur','Putrajaya','Labuan'];
                                    @endphp
                                    @foreach($states as $s)
                                        <option value="{{ $s }}" {{ old('state') == $s ? 'selected' : '' }}>{{ $s }}</option>
                                    @endforeach
                                </select>
                                @error('state') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="country" class="form-label">Country <span class="text-danger">*</span></label>
                                <input id="country" name="country" value="{{ old('country', 'Malaysia') }}" type="text" class="form-control @error('country') is-invalid @enderror" required>
                                @error('country') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Submit buttons (left column bottom) --}}
                            <div class="col-12 mt-5">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-plus-circle"></i> Create Event
                                    </button>
                                    <a href="{{ route('ngo.events.index') }}" class="btn btn-outline-secondary">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT: image preview + upload + SDG images (equal width) --}}
                    <div class="col-lg-6 col-12 col-equal">
                        <div>
                            <label class="form-label">Event Image</label>
                            <div class="image-box mb-2" id="imageBox">
                                <img id="imgPreview" src="" class="d-none" alt="Preview">
                                <div id="imgPlaceholder" class="text-muted">
                                    <i class="far fa-image fa-2x"></i>
                                    <div>Image preview</div>
                                </div>
                            </div>

                            <div class="d-flex gap-2 mb-3">
                                <input id="event_image" name="event_image" type="file" accept="image/*" class="d-none @error('eventImage') is-invalid @enderror">
                                <button type="button" class="btn btn-primary btn-select-image" id="selectImageBtn">
                                    <i class="fas fa-upload"></i> Select New Image
                                </button>
                                <button type="button" id="removeImageBtn" class="btn btn-outline-danger d-none">Remove</button>
                            </div>


                            @error('eventImage') <div class="invalid-feedback d-block mt-1">{{ $message }}</div> @enderror
                            <div class="form-note mb-3">Optional: JPG/PNG. Will be used in event listings.</div>
                        </div>

                        <div>
                            <label class="form-label d-block">Select SDGs (optional)</label>
                            <div class="sdg-grid mb-2" id="sdgGrid">
                                @if (isset($sdgs) && $sdgs->count())
                                    @foreach ($sdgs as $sdg)
                                        @php
                                            $sdgId = $sdg->sdg_id ?? $sdg->id;
                                            $img = $sdg->sdgImage ? asset('images/sdgs/' . $sdg->sdgImage) : asset('images/sdgs/default-sdg.png');
                                            $checked = is_array(old('sdgs')) && in_array($sdgId, old('sdgs'));
                                        @endphp

                                        <label class="sdg-item {{ $checked ? 'selected' : '' }}" data-sdg-id="{{ $sdgId }}" tabindex="0" role="button" aria-pressed="{{ $checked ? 'true' : 'false' }}">
                                            <div class="check-badge"><i class="fas fa-check"></i></div>
                                            <img src="{{ $img }}" alt="{{ $sdg->sdgName }}">
                                            <div class="sdg-name">{{ $sdg->sdgName }}</div>
                                            <input type="checkbox" name="sdgs[]" value="{{ $sdgId }}" class="sdg-input" {{ $checked ? 'checked' : '' }}>
                                        </label>
                                    @endforeach
                                @else
                                    <div class="text-muted">No SDGs available. Admin can add SDGs from the dashboard.</div>
                                @endif
                            </div>
                            @error('sdgs') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            <div class="form-note">Click an SDG to select. Selected SDGs will be shown on the event page.</div>
                        </div>

                        {{-- keep right column bottom spacing flexible --}}
                        <div class="mt-auto"></div>
                    </div>
                </div>
            </form>
        </div>
    </main>

    @include('layouts.volunteer_footer')


 <script src="{{ asset('js/events/createEvents.js') }}"></script>
</body>
</html>
