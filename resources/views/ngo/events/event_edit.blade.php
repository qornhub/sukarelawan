{{-- resources/views/ngo/events/event_edit.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Event — NGO</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/events/create_events.css') }}">

    <style>

        .hero {
        position: relative;
        height:360px; /* takes 70% of screen height */
        background-image: url('https://images.unsplash.com/photo-1552664730-d307ca884978?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1770&q=80');
        background-size: cover;
        background-position: center;
        display: flex;
        align-items: center;
        justify-content: center;
        
        text-align: center;
        color: white;
        overflow: hidden;
    }

    .hero-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to bottom, rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.7));
    }

    .hero-content {
        position: relative;
        z-index: 2;
        max-width: 800px;
        padding: 0 20px;
    }

    .hero-title {
        font-size: clamp(2rem, 4vw, 3.5rem); /* responsive */
        font-weight: 700;
        margin-bottom: 0.5rem;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    }

    .hero-sub {
        font-size: clamp(1rem, 2vw, 1.5rem);
        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
    }
        .event-header {
            width: 100%;
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid #eee;
            background: transparent;
            box-shadow: none;
            border-radius: 0;
        }

        .header-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.5rem;
        }

        .nav-container {
            display: flex;
            justify-content: center;
            width: 100%;
        }

        .nav-tabs {
            display: flex;
            background: white;
            border-radius: 50px;
            padding: 0.25rem;
            position: relative;
        }

        .nav-tab {
            padding: 0.75rem 1.5rem;
            text-decoration: none;
            color: var(--text-color);
            font-weight: 500;
            border-radius: 50px;
            transition: all 0.3s ease;
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-tab i {
            font-size: 0.9rem;
        }

        .nav-tab.active {
            color: white;
        }

        .nav-indicator {
            position: absolute;
            height: calc(100% - 0.5rem);
            border-radius: 50px;
            background: var(--primary-color);
            transition: all 0.3s ease;
            top: 0.25rem;
            left: 0.25rem;
            z-index: 0;
            pointer-events: none; /* <- important so it never blocks links */
        }

        .back-button-container {
            width: 100%;
            display: flex;
            justify-content: flex-start;
        }

                /* make the form behave like the anchor nav tabs */
.nav-tabs .delete-tab {
  margin: 0;
  padding: 0;
  display: inline-flex;      /* match anchor layout */
  align-items: center;
  height: 100%;
  text-decoration: none;
  border: none;
  background: transparent;
  
  cursor: pointer;
}

/* remove default button styles and inherit the nav-tab look */
.delete-tab-button {
  all: unset;                /* remove default button styles */
  display: inline-flex;
  align-items: center;
  gap: .5rem;
  padding: .6rem 1rem;       /* tune to match .nav-tab */
  font: inherit;
  color: inherit;
  cursor: pointer;
  width: 100%;
  height: 100%;
}

/* optional: hover/focus visual parity with .nav-tab */
.delete-tab-button:hover,
.delete-tab-button:focus {
  /* either reuse your .nav-tab hover styles, or replicate here */
 
  text-decoration: none;
  outline: none;
}
    </style>
</head>

<body>
    @include('layouts.ngo_header')

    <!-- HERO -->
<header class="hero">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1 class="hero-title">Untitled Event</h1>
        <div class="hero-sub">By Organizer</div>
    </div>
</header>

              <div class="event-header mt-3">
    <div class="header-content">
        <div class="nav-container">
           <nav class="nav-tabs">
    <div class="nav-indicator" style="width: 88px; transform: translateX(0);"></div>

    {{-- Event Tab --}}
    <a href="{{ route('ngo.profile.eventEditDelete', $event->event_id) }}"
       class="nav-tab {{ request()->routeIs('ngo.profile.eventEditDelete') ? 'active' : '' }}"
       data-tab="event">
        <i class="fas fa-calendar-day"></i>
        <span>Event</span>
    </a>

    {{-- Edit Tab --}}
    <a href="{{ route('ngo.events.event_edit', $event->event_id) }}"
       class="nav-tab {{ request()->routeIs('ngo.events.event_edit') ? 'active' : '' }}"
       data-tab="edit"
       onclick="event.stopPropagation();">
        <i class="fas fa-edit"></i>
        <span>Edit</span>
    </a>

    {{-- Manage Tab --}}
    <a href="{{ route('ngo.events.manage', $event->event_id) }}"
       class="nav-tab {{ request()->routeIs('ngo.events.manage') ? 'active' : '' }}"
       data-tab="manage">
        <i class="fas fa-tasks"></i>
        <span>Manage</span>
    </a>

    {{-- Delete "tab" implemented as a form (no navigation) --}}
    <form action="{{ route('ngo.events.destroy', $event->event_id) }}"
          method="POST"
          class="nav-tab delete-tab"
          onsubmit="return confirm('Are you sure you want to delete this event? This action cannot be undone.');">
        @csrf
        @method('DELETE')

        <button type="submit"
                class="delete-tab-button"
                onclick="event.stopPropagation();"
                aria-label="Delete event">
            <i class="fas fa-trash-alt"></i>
            <span>Delete</span>
        </button>
    </form>
</nav>

        </div>
    </div>
</div>


    <main class="container-fluid page-wrapper">
        <div class="create-event-card">



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

            @php
                $oldSkills = old('skills', $selectedSkills ?? []);
                $oldSdgs = old('sdgs', $selectedSdgs ?? []);
            @endphp

            <form action="{{ route('ngo.events.update', $event) }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf
                @method('PUT')

                <div class="row align-items-stretch">
                    <div class="col-lg-6 col-12 col-equal">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="event_title" class="form-label">Event Title <span class="text-danger">*</span></label>
                                <input id="event_title" name="event_title" value="{{ old('event_title', old('eventTitle', $event->eventTitle)) }}" type="text"
                                    class="form-control @error('eventTitle') is-invalid @enderror @error('event_title') is-invalid @enderror" required>
                                @error('eventTitle') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                @error('event_title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                    <option value="">Select category</option>
                                    @foreach($categories as $cat)
                                        @php $catId = $cat->eventCategory_id ?? $cat->id; @endphp
                                        <option value="{{ $catId }}" {{ (string) old('category_id', (string)$event->category_id) === (string) $catId ? 'selected' : '' }}>
                                            {{ $cat->eventCategoryName ?? ($cat->name ?? 'Category') }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="reward_points" class="form-label">Reward Points <span class="text-danger">*</span></label>
                                <input id="reward_points" name="reward_points" value="{{ old('reward_points', old('eventPoints', $event->eventPoints)) }}" type="number" min="0"
                                    class="form-control @error('eventPoints') is-invalid @enderror @error('reward_points') is-invalid @enderror" required>
                                @error('eventPoints') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                @error('reward_points') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Start Date &amp; Time <span class="text-danger">*</span></label>
                                <input id="start_date" name="start_date" value="{{ old('start_date', old('eventStart', $event->eventStart)) }}" type="datetime-local"
                                    class="form-control @error('eventStart') is-invalid @enderror @error('start_date') is-invalid @enderror" required>
                                @error('eventStart') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="end_date" class="form-label">End Date &amp; Time <span class="text-danger">*</span></label>
                                <input id="end_date" name="end_date" value="{{ old('end_date', old('eventEnd', $event->eventEnd)) }}" type="datetime-local"
                                    class="form-control @error('eventEnd') is-invalid @enderror @error('end_date') is-invalid @enderror" required>
                                @error('eventEnd') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label for="event_summary" class="form-label">Short Summary</label>
                                <textarea id="event_summary" name="event_summary" rows="2"
                                    class="form-control @error('eventSummary') is-invalid @enderror @error('event_summary') is-invalid @enderror" placeholder="Short summary (max 200 chars)">{{ old('event_summary', old('eventSummary', $event->eventSummary)) }}</textarea>
                                @error('eventSummary') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                @error('event_summary') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label for="event_description" class="form-label">Detailed Description <span class="text-danger">*</span></label>
                                <textarea id="event_description" name="event_description" rows="6"
                                    class="form-control @error('eventDescription') is-invalid @enderror @error('event_description') is-invalid @enderror" required>{{ old('event_description', old('eventDescription', $event->eventDescription)) }}</textarea>
                                @error('eventDescription') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                @error('event_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label for="requirements" class="form-label">Requirements <small class="text-muted">(what volunteers must bring / know)</small></label>
                                <textarea id="requirements" name="requirements" rows="4" class="form-control @error('requirements') is-invalid @enderror" placeholder="E.g. Must be 18+, bring gloves, comfortable walking shoes">{{ old('requirements', $event->requirements) }}</textarea>
                                @error('requirements') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="form-note mt-1">This will be shown on the event page under Requirements.</div>
                            </div>

                            <div class="col-12">
                                <label for="event_impact" class="form-label">Expected Impact / KPI</label>
                                <textarea id="event_impact" name="event_impact" rows="4" class="form-control @error('eventImpact') is-invalid @enderror" placeholder="Describe expected outcomes, KPIs, measurable goals">{{ old('event_impact', old('eventImpact', $event->eventImpact)) }}</textarea>
                                @error('eventImpact') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="form-note mt-1">This field supports multiple lines and will appear in the event details.</div>
                            </div>

                            <div class="col-md-6">
                                <label for="event_maximum" class="form-label">Maximum Participants</label>
                                <input id="event_maximum" name="event_maximum" value="{{ old('event_maximum', old('eventMaximum', $event->eventMaximum)) }}" type="number" min="0" class="form-control @error('eventMaximum') is-invalid @enderror">
                                <div class="form-note">Leave blank for unlimited.</div>
                                @error('eventMaximum') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

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
                                            <small class="text-muted" id="skillsCount">{{ count((array)$oldSkills) }} selected</small>
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

                            <div class="col-12 pt-2">
                                <h5 class="mb-0">Location</h5>
                            </div>

                            <div class="col-md-6">
                                <label for="venue_name" class="form-label">Venue / Venue Name <span class="text-danger">*</span></label>
                                <input id="venue_name" name="venue_name" value="{{ old('venue_name', old('venueName', $event->venueName)) }}" type="text" class="form-control @error('venueName') is-invalid @enderror" required>
                                @error('venueName') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-2">
                                <label for="zip_code" class="form-label">ZIP</label>
                                <input id="zip_code" name="zip_code" value="{{ old('zip_code', old('zipCode', $event->zipCode)) }}" type="text" class="form-control @error('zipCode') is-invalid @enderror">
                                @error('zipCode') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                                <input id="city" name="city" value="{{ old('city', $event->city) }}" type="text" class="form-control @error('city') is-invalid @enderror" required>
                                @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="state" class="form-label">State / Territory <span class="text-danger">*</span></label>
                                <select id="state" name="state" class="form-select @error('state') is-invalid @enderror" required>
                                    <option value="">Choose state</option>
                                    @php
                                        $states = ['Perlis','Kedah','Penang','Perak','Kelantan','Terengganu','Pahang','Selangor','Negeri Sembilan','Melaka','Johor','Sabah','Sarawak','Kuala Lumpur','Putrajaya','Labuan'];
                                    @endphp
                                    @foreach($states as $s)
                                        <option value="{{ $s }}" {{ old('state', $event->state) == $s ? 'selected' : '' }}>{{ $s }}</option>
                                    @endforeach
                                </select>
                                @error('state') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="country" class="form-label">Country <span class="text-danger">*</span></label>
                                <input id="country" name="country" value="{{ old('country', $event->country ?? 'Malaysia') }}" type="text" class="form-control @error('country') is-invalid @enderror" required>
                                @error('country') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12 mt-5">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save changes
                                    </button>
                                    <a href="{{ route('ngo.events.index') }}" class="btn btn-outline-secondary">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-12 col-equal">
                        <div>
                            <label class="form-label">Event Image</label>
                            <div class="image-box mb-2" id="imageBox">
                                @if($event->eventImage)
                                    <img id="imgPreview" src="{{ asset('images/events/' . $event->eventImage) }}" alt="Preview">
                                @else
                                    <img id="imgPreview" src="" class="d-none" alt="Preview">
                                    <div id="imgPlaceholder" class="text-muted">
                                        <i class="far fa-image fa-2x"></i>
                                        <div>Image preview</div>
                                    </div>
                                @endif
                            </div>

                            <div class="d-flex gap-2 mb-3">
                                <input id="event_image" name="event_image" type="file" accept="image/*" class="d-none @error('eventImage') is-invalid @enderror">
                                <button type="button" class="btn btn-primary btn-select-image" id="selectImageBtn">
                                    <i class="fas fa-upload"></i> Select New Image
                                </button>
                                <button type="button" id="removeImageBtn" class="btn btn-outline-danger {{ $event->eventImage ? '' : 'd-none' }}">Remove</button>
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
                                            $checked = in_array((string)$sdgId, array_map('strval', (array)$oldSdgs));
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

                        <div class="mt-auto"></div>
                    </div>
                </div>
            </form>
        </div>
    </main>

    @include('layouts.volunteer_footer')

    <script src="{{ asset('js/events/createEvents.js') }}"></script>
    <script>
        // wire up select image button and preview
        //document.getElementById('selectImageBtn')?.addEventListener('click', function(){
            //document.getElementById('event_image').click();
        //});

        document.getElementById('event_image')?.addEventListener('change', function(e){
            const file = this.files[0];
            if (!file) return;
            const img = document.getElementById('imgPreview');
            const placeholder = document.getElementById('imgPlaceholder');
            const reader = new FileReader();
            reader.onload = function(ev){
                img.src = ev.target.result;
                img.classList.remove('d-none');
                if (placeholder) placeholder.classList.add('d-none');
                document.getElementById('removeImageBtn').classList.remove('d-none');
            }
            reader.readAsDataURL(file);
        });

        document.getElementById('removeImageBtn')?.addEventListener('click', function(){
            // clear preview and file input
            const img = document.getElementById('imgPreview');
            const placeholder = document.getElementById('imgPlaceholder');
            document.getElementById('event_image').value = '';
            if(img) { img.src = ''; img.classList.add('d-none'); }
            if(placeholder) { placeholder.classList.remove('d-none'); }
            this.classList.add('d-none');
        });

        // make SDG items toggle their associated checkbox when clicked (same behaviour as create)
        document.querySelectorAll('.sdg-item').forEach(function(lbl){
            lbl.addEventListener('click', function(e){
                if(e.target.tagName === 'INPUT') return; // let checkbox handle itself
                const cb = this.querySelector('.sdg-input');
                cb.checked = !cb.checked;
                this.classList.toggle('selected', cb.checked);
            });
        });


        
/*
  SDG toggle handler
  - Each .sdg-item contains a checkbox input.sdg-input
  - Clicking the label toggles the checkbox and .selected class
  - Direct changes to the checkbox (keyboard/tab, clicking the input) also update the .selected class
*/
document.querySelectorAll('.sdg-item').forEach(function(item){
  const cb = item.querySelector('.sdg-input');

  if (!cb) return;

  // When clicking the whole item (but not the input), toggle the checkbox
  item.addEventListener('click', function(e){
    // if user actually clicked the checkbox input itself, let the input handle it
    if (e.target === cb || e.target.tagName === 'INPUT') return;

    // toggle checked state
    cb.checked = !cb.checked;
    item.classList.toggle('selected', cb.checked);
  });

  // If the checkbox changes by other means (keyboard, direct click), update class
  cb.addEventListener('change', function(){
    item.classList.toggle('selected', cb.checked);
  });
});


    </script>
</body>
</html>
