@php
    $isAdminReadonly = true;
@endphp

@php
    use Carbon\Carbon;
    $eventHasEnded = false;
    if (!empty($event->eventEnd)) {
        try {
            $eventHasEnded = Carbon::parse($event->eventEnd)
                ->startOfDay()
                ->lessThanOrEqualTo(Carbon::now()->startOfDay());
        } catch (\Exception $ex) {
            $eventHasEnded = false;
        }
    }
@endphp
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Manage Event - {{ $event->eventTitle ?? 'Event' }}</title>

    <!-- CSRF for AJAX -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        // ensures Echo will POST to the correct absolute auth endpoint
        window.__BROADCAST_AUTH_ENDPOINT = "{{ url('/broadcasting/auth') }}";
        // pass current event id (or empty string if none)
        window.__EVENT_ID = "{{ $event->event_id ?? '' }}";
    </script>

    @vite(['resources/js/app.js', 'resources/css/app.css'])

    <!-- Fonts & icons -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    {{-- HEAD (Bootstrap CSS) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/events/manage.css') }}">
    <link rel="stylesheet" href="{{ asset('css/task/task_list.css') }}">
    <link rel="stylesheet" href="{{ asset('css/task/task_create.css') }}">
    <link rel="stylesheet" href="{{ asset('css/task/task_edit.css') }}">
    <link rel="stylesheet" href="{{ asset('css/task/task_manage.css') }}">
    <link rel="stylesheet" href="{{ asset('css/events/email.css') }}">
    <style>
        /* make the form behave like the anchor nav tabs */
        .nav-tabs .delete-tab {
            margin: 0;
            padding: 0;
            display: inline-flex;
            /* match anchor layout */
            align-items: center;
            height: 100%;
            text-decoration: none;
            border: none;
            background: transparent;

            cursor: pointer;
        }

        /* remove default button styles and inherit the nav-tab look */
        .delete-tab-button {
            all: unset;
            /* remove default button styles */
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .6rem 1rem;
            /* tune to match .nav-tab */
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

        /* ----------------------------
       Disabled state for nav tabs
       ---------------------------- */

        /* Generic disabled look & block interactions (applies to edit/delete/etc) */
        .nav-tab.disabled,
        .nav-tab[aria-disabled="true"] {
            pointer-events: none;
            /* block clicks on the parent */
            cursor: default;
            opacity: 0.55;
            /* visually dim */
            color: inherit;
            /* keep text color consistent */
        }

        /* Also block clicks on any children and prevent focus */
        .nav-tab.disabled *,
        .nav-tab[aria-disabled="true"] * {
            pointer-events: none;
        }

        /* Slightly mute the icon when disabled for parity */
        .nav-tab.disabled i,
        .nav-tab[aria-disabled="true"] i {
            opacity: 0.65;
        }

        /* If you want keyboard focus to be prevented for inner elements */
        .nav-tab.disabled [tabindex],
        .nav-tab[aria-disabled="true"] [tabindex] {
            outline: none;
        }

        /* generic disabled state */
        .is-disabled,
        .disabled,
        [aria-disabled="true"] {
            pointer-events: none !important;
            cursor: default !important;
            opacity: 0.55 !important;
        }

        /* ensure inner controls don't accidentally remain interactive */
        .is-disabled *,
        .disabled *,
        [aria-disabled="true"] * {
            pointer-events: none !important;
        }

        /* optionally mute icons a little more */
        .is-disabled i,
        .disabled i,
        [aria-disabled="true"] i {
            opacity: 0.65;
        }
    </style>
</head>

<body>
    @include('layouts.admin_nav')
    <!-- HERO -->
    <header class="hero mb-3">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1 class="hero-title">Event Management</h1>
        </div>
    </header>
<div style="margin-left: 120px; margin-right:40px;">
    <div class="page" role="main">
        <div class="grid">
            <!-- Sidebar -->
            <aside class="sidebar">
                <div class="side-card mb-3">
                    <div style="font-weight:700;margin-bottom:8px">Participants</div>

                    <a class="side-btn active" data-show="registered" role="button">
                        <span>Registered Participants </span>
                        <span class="badge" id="badge-registered">{{ $registered->count() }}</span>
                    </a>

                    <a class="side-btn" data-show="confirmed" role="button">
                        <span>Confirmed Participants </span>
                        <span class="badge" id="badge-confirmed">{{ $confirmed->count() }}</span>
                    </a>

                    <a class="side-btn" data-show="rejected" role="button">
                        <span>Rejected Participants </span>
                        <span class="badge" id="badge-rejected">{{ $rejected->count() }}</span>
                    </a>
                </div>

                <div class="side-card mb-3">
                    <div style="font-weight:700;margin-bottom:8px">Task</div>
                    <a class="side-btn" data-show="tasks" role="button">
                        Task Creation
                    </a>

                    <a class="side-btn"
                       href="{{ route('ngo.events.manage', $event->event_id) }}"
                       {{-- fallback if JS is off --}}
                       data-show="manage-tasks" {{-- JS hook --}}
                       role="button"
                       aria-controls="section-manage-tasks">
                        Manage Task
                    </a>
                </div>

                <div class="side-card">
                    <div style="font-weight:700;margin-bottom:8px">Attendance</div>

                    <a class="side-btn" data-show="attendance" role="button">
                        Attendance List
                    </a>
                </div>
            </aside>

            <!-- Content -->
            <section class="content">

                <!-- Registered -->
                <div id="section-registered" class="card participant-section" aria-live="polite">
                    <div
                        style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; margin-bottom: 20px;">
                        <h4 style="margin:0;">
                            Registered Participants:
                            <span class="badge badge-registered">{{ $registered->count() }}</span>
                        </h4>

                        <form method="GET"
                            action="{{ route('ngo.events.manage', ['event_id' => $event->event_id]) }}"
                            style="display: flex; align-items: center; gap: 8px;">
                            <input type="hidden" name="status" value="registered">
                            <input type="text" name="search" class="search"
                                placeholder="Search name, email, age..."
                                value="{{ request('status') === 'registered' ? request('search') : '' }}"
                                style="padding: 6px 10px; border: 1px solid #ccc; border-radius: 6px; width: 220px;">
                            <button type="submit" class="search-btn" style="padding: 6px 12px;">Search</button>
                            <a href="{{ route('ngo.events.manage', ['event_id' => $event->event_id, 'status' => 'registered']) }}"
                                class="reset-btn" style="padding: 6px 12px; text-decoration:none;">Reset</a>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table" id="table-registered" role="table"
                            aria-label="Registered participants">
                            <thead>
                                <tr>
                                    <th>Participant</th>
                                    <th>Email</th>
                                    <th>Contact</th>
                                    <th>Age</th>
                                    <th>Gender</th>
                                    <th>Skill</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($registered as $r)
                                    <tr id="reg-row-{{ $r->registration_id }}">
                                        <td>
                                            @if ($r->user_id)
                                                <a href="{{ route('volunteer.profile.show', $r->user_id) }}"
                                                    class="text-primary text-decoration-none"
                                                    title="View volunteer profile">
                                                    {{ $r->name }}
                                                </a>
                                            @else
                                                {{ $r->name }}
                                            @endif
                                        </td>
                                        <td>{{ $r->email }}</td>
                                        <td>{{ $r->contactNumber }}</td>
                                        <td>{{ $r->age }}</td>
                                        <td>{{ $r->gender }}</td>
                                        <td>{{ \Illuminate\Support\Str::limit($r->skill ?? 'No skills', 30) }}</td>
                                        <td>
                                            @if ($r->status === 'pending')
                                                <span class="badge badge-secondary">Pending</span>
                                            @elseif($r->status === 'approved')
                                                <span class="badge badge-success">Approved</span>
                                            @elseif($r->status === 'rejected')
                                                <span class="badge badge-danger">Rejected</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn-view-details"
                                                data-json='@json($r)'>View</button>
                                        </td>
                                        {{-- View details uses a JSON payload to avoid many data-* attributes --}}
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No registered participants</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Confirmed -->
                <div id="section-confirmed" class="card participant-section" style="display:none">
                    <div
                        style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; margin-bottom: 20px;">
                        <h4 style="margin:0;">
                            Confirmed Participants:
                            <span class="badge badge-confirmed">{{ $confirmed->count() }}</span>
                        </h4>

                        <form method="GET"
                            action="{{ route('ngo.events.manage', ['event_id' => $event->event_id]) }}"
                            style="display: flex; align-items: center; gap: 8px;">
                            <input type="hidden" name="status" value="confirmed">
                            <input type="text" name="search" class="search"
                                placeholder="Search name, email, age..."
                                value="{{ request('status') === 'confirmed' ? request('search') : '' }}"
                                style="padding: 6px 10px; border: 1px solid #ccc; border-radius: 6px; width: 220px;">
                            <button type="submit" class="search-btn" style="padding: 6px 12px;">Search</button>
                            <a href="{{ route('ngo.events.manage', ['event_id' => $event->event_id, 'status' => 'confirmed']) }}"
                                class="reset-btn" style="padding: 6px 12px; text-decoration:none;">Reset</a>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table" id="table-confirmed" role="table"
                            aria-label="Confirmed participants">
                            <thead>
                                <tr>
                                    <th>Participant</th>
                                    <th>Email</th>
                                    <th>Contact</th>
                                    <th>Age</th>
                                    <th>Gender</th>
                                    <th>Skill</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($confirmed as $r)
                                    <tr id="conf-row-{{ $r->registration_id }}">
                                        <td>
                                            @if ($r->user_id)
                                                <a href="{{ route('volunteer.profile.show', $r->user_id) }}"
                                                    class="text-primary text-decoration-none"
                                                    title="View volunteer profile">
                                                    {{ $r->name }}
                                                </a>
                                            @else
                                                {{ $r->name }}
                                            @endif
                                        </td>
                                        <td>{{ $r->email }}</td>
                                        <td>{{ $r->contactNumber }}</td>
                                        <td>{{ $r->age }}</td>
                                        <td>{{ $r->gender }}</td>
                                        <td>{{ $r->skill ?? 'No skills' }}</td>
                                        <td>
                                            <span class="badge badge-success">Approved</span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn-view-details"
                                                data-json='@json($r)'>View</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="empty-row">
                                        <td colspan="9" class="text-center">No confirmed participants</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Rejected -->
                <div id="section-rejected" class="card participant-section" style="display:none">
                    <div
                        style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; margin-bottom: 20px;">
                        <h4 style="margin:0;">
                            Rejected Participants:
                            <span class="badge badge-rejected">{{ $rejected->count() }}</span>
                        </h4>

                        <form method="GET"
                            action="{{ route('ngo.events.manage', ['event_id' => $event->event_id]) }}"
                            style="display: flex; align-items: center; gap: 8px;">
                            <input type="hidden" name="status" value="rejected">
                            <input type="text" name="search" class="search"
                                placeholder="Search name, email, age..."
                                value="{{ request('status') === 'rejected' ? request('search') : '' }}"
                                style="padding: 6px 10px; border: 1px solid #ccc; border-radius: 6px; width: 220px;">
                            <button type="submit" class="search-btn" style="padding: 6px 12px;">Search</button>
                            <a href="{{ route('ngo.events.manage', ['event_id' => $event->event_id, 'status' => 'rejected']) }}"
                                class="reset-btn" style="padding: 6px 12px; text-decoration:none;">Reset</a>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table" id="table-rejected" role="table" aria-label="Rejected participants">
                            <thead>
                                <tr>
                                    <th>Participant</th>
                                    <th>Email</th>
                                    <th>Contact</th>
                                    <th>Age</th>
                                    <th>Gender</th>
                                    <th>Skill</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rejected as $r)
                                    <tr id="rej-row-{{ $r->registration_id }}">
                                        <td>
                                            @if ($r->user_id)
                                                <a href="{{ route('volunteer.profile.show', $r->user_id) }}"
                                                    class="text-primary text-decoration-none"
                                                    title="View volunteer profile">
                                                    {{ $r->name }}
                                                </a>
                                            @else
                                                {{ $r->name }}
                                            @endif
                                        </td>
                                        <td>{{ $r->email }}</td>
                                        <td>{{ $r->contactNumber }}</td>
                                        <td>{{ $r->age }}</td>
                                        <td>{{ $r->gender }}</td>
                                        <td>{{ $r->skill ?? 'No skills' }}</td>
                                        <td>
                                            <span class="badge badge-danger">Rejected</span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn-view-details"
                                                data-json='@json($r)'>View</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="empty-row">
                                        <td colspan="9" class="text-center">No rejected participants</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tasks Section (view-only for Admin) -->
                @include('ngo.tasks.task_list', [
                    'event' => $event,
                    'disabled' => $eventHasEnded,
                ])

                <!-- Manage Tasks section (view-only for Admin) -->
                @include('ngo.tasks.task_manage', [
                    'event' => $event,
                    'tasks' => $tasks,
                    'confirmedParticipants' => $confirmedParticipants,
                    'assignedMap' => $assignedMap,
                     'isAdminReadonly'       => true,   
                ])

                <!-- Attendance List (view-only for Admin) -->
                @include('ngo.attendances.list', [
                    'event' => $event,
                    'attendances' => $attendances ?? collect(),
                    'isAdminReadonly'       => true,   
                ])

            </section>
        </div>
    </div>
</div>

       <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // ---------------------------
            // Sidebar toggles
            // ---------------------------
            document.querySelectorAll('.sidebar .side-btn[data-show]').forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();

                    // set active state on sidebar buttons
                    document.querySelectorAll('.sidebar .side-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    const show = this.getAttribute('data-show');

                    // hide all toggleable sections
                    document.querySelectorAll('.participant-section').forEach(sec => {
                        sec.style.display = 'none';
                    });

                    // show the target section if it exists
                    const target = document.getElementById('section-' + show);
                    if (target) {
                        target.style.display = 'block';
                    }
                });
            });

            // ---------------------------
            // Helper: escape HTML
            // ---------------------------
            function escapeHtml(s) {
                if (!s) return '';
                return String(s)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#39;');
            }

            // ---------------------------
            // Registration details modal
            // ---------------------------
            (function registrationDetails() {
                const modal = document.getElementById('sr-details-modal');
                if (!modal) return;

                function openModalWith(reg) {
                    // reg is a plain object with fields from event_registrations
                    document.getElementById('sr-modal-name').textContent = escapeHtml(reg.name);
                    document.getElementById('sr-modal-email').textContent = escapeHtml(reg.email);
                    document.getElementById('sr-modal-contact').textContent = escapeHtml(reg.contactNumber);
                    document.getElementById('sr-modal-age-gender').textContent =
                        (escapeHtml(reg.age) || '') + ' / ' + (escapeHtml(reg.gender) || '');
                    document.getElementById('sr-modal-skill').textContent =
                        escapeHtml(reg.skill || 'No skills');
                    document.getElementById('sr-modal-company').textContent =
                        escapeHtml(reg.company || '—');
                    document.getElementById('sr-modal-experience').textContent =
                        escapeHtml(reg.volunteeringExperience || '—');
                    document.getElementById('sr-modal-emer-name').textContent =
                        escapeHtml(reg.emergencyContact || '—');
                    document.getElementById('sr-modal-emer-number').textContent =
                        escapeHtml(reg.emergencyContactNumber || '—');
                    document.getElementById('sr-modal-emer-rel').textContent =
                        escapeHtml(reg.contactRelationship || '—');
                    document.getElementById('sr-modal-address').textContent =
                        escapeHtml(reg.address || '—');
                    document.getElementById('sr-modal-created-at').textContent =
                        escapeHtml(reg.registrationDate || reg.created_at || '');

                    modal.style.display = 'flex';
                    modal.setAttribute('aria-hidden', 'false');
                }

                function closeModal() {
                    modal.style.display = 'none';
                    modal.setAttribute('aria-hidden', 'true');
                }

                // Delegated click for "View" buttons and close controls
                document.addEventListener('click', function (e) {
                    // open
                    const openBtn = e.target.closest('.btn-view-details');
                    if (openBtn) {
                        const json = openBtn.getAttribute('data-json') || '{}';
                        let reg = {};
                        try {
                            reg = JSON.parse(json);
                        } catch (err) {
                            console.error('Invalid JSON in btn-view-details', err);
                        }
                        openModalWith(reg);
                        return;
                    }

                    // close (button or backdrop with data-close="true")
                    if (e.target.closest('[data-close="true"]')) {
                        closeModal();
                    }
                });

                // close on ESC
                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape' && modal.style.display === 'flex') {
                        closeModal();
                    }
                });
            })();
        });
    </script>

    @stack('scripts')

    <!-- Details Modal -->
    <div id="sr-details-modal" class="sr-modal" aria-hidden="true" style="display:none;">
        <div class="sr-modal-backdrop" data-close="true"></div>
        <div class="sr-modal-panel" role="dialog" aria-modal="true">
            <button class="sr-modal-close" aria-label="Close" data-close="true">×</button>
            <div class="sr-modal-body">
                <h3 id="sr-modal-name"></h3>
                <p><strong>Email:</strong> <span id="sr-modal-email"></span></p>
                <p><strong>Contact:</strong> <span id="sr-modal-contact"></span></p>
                <p><strong>Age / Gender:</strong> <span id="sr-modal-age-gender"></span></p>
                <p><strong>Skill:</strong> <span id="sr-modal-skill"></span></p>
                <p><strong>Company:</strong> <span id="sr-modal-company"></span></p>
                <p><strong>Volunteering Experience:</strong> <span id="sr-modal-experience"
                        style="white-space:pre-wrap;"></span></p>
                <p><strong>Emergency Contact:</strong> <span id="sr-modal-emer-name"></span> (<span
                        id="sr-modal-emer-number"></span>)</p>
                <p><strong>Contact Relationship:</strong> <span id="sr-modal-emer-rel"></span></p>
                <p><strong>Address:</strong> <span id="sr-modal-address"></span></p>
                <p style="font-size:12px;color:#666">Registered at: <span id="sr-modal-created-at"></span></p>
            </div>
        </div>
    </div>

    <style>
        /* Minimal scoped styles for modal (tweak to match your theme) */
        .sr-modal {
            position: fixed;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .sr-modal-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
        }

        .sr-modal-panel {
            position: relative;
            background: #fff;
            padding: 18px;
            border-radius: 8px;
            width: min(820px, 95%);
            max-height: 80vh;
            overflow: auto;
            z-index: 2;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.25);
        }

        .sr-modal-close {
            position: absolute;
            right: 10px;
            top: 6px;
            border: none;
            background: transparent;
            font-size: 20px;
            cursor: pointer;
        }

        .sr-modal-body h3 {
            margin-top: 0;
            margin-bottom: 6px;
        }
    </style>

   
</body>

</html>

