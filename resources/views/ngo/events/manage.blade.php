<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Manage Event - {{ $event->eventTitle ?? 'Event' }}</title>

    <!-- CSRF for AJAX -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

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

</head>

<body>
    @include('layouts.ngo_header')
    <!-- HERO -->
    <header class="hero mb-3">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1 class="hero-title">Event Management</h1>

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
                        data-tab="edit" onclick="event.stopPropagation();">
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


                </nav>
            </div>
        </div>
    </div>

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


                    <a class="side-btn" href="{{ route('ngo.events.manage', $event->event_id) }}" {{-- fallback if JS is off --}}
                        data-show="manage-tasks" {{-- JS hook --}} role="button"
                        aria-controls="section-manage-tasks">
                        Manage Task
                    </a>

                </div>

                <div class="side-card mb-3">
                    <div style="font-weight:700;margin-bottom:8px">Communications</div>
                    <a class="side-btn" data-show="email"
                        href="{{ route('ngo.events.manage', $event->event_id) }}#email" role="button">
                        Email Participants
                    </a>
                </div>


                <div class="side-card">
                    <div style="font-weight:700;margin-bottom:8px">Attendance</div>
                    
                     <a class="side-btn" data-show="qr" role="button">
                        Attendance Qr
                    </a>
                    <a class="side-btn" href="#">Attendance List</a>
                </div>
            </aside>

            <!-- Content -->
            <section class="content">
                <div class="section-title">
                    <div style="display:flex;align-items:center;gap:12px">
                        <h3 style="margin:0">Participants Lists</h3>

                    </div>

                    <div>
                        <form method="GET"
                            action="{{ route('ngo.events.manage', ['event_id' => $event->event_id]) }}">
                            <div class="search-container">
                                <input type="text" name="search" class="search"
                                    placeholder=" Search name, email, age..." value="{{ request('search') }}">
                                <button type="submit" class="search-btn">Search</button>
                                <a href="{{ route('ngo.events.manage', $event->event_id) }}"
                                    class="reset-btn">Reset</a>
                            </div>
                        </form>


                    </div>
                </div>

                <!-- Registered -->
                <div id="section-registered" class="card participant-section" aria-live="polite">
                    <h4 style="margin-top:0">
                        Registered Participants:
                        <span class="badge badge-registered">{{ $registered->count() }}</span>
                    </h4>

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
                                                <button class="action-btn btn-approve"
                                                    data-id="{{ $r->registration_id }}">Approve</button>
                                                <button class="action-btn btn-reject"
                                                    data-id="{{ $r->registration_id }}">Reject</button>
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
                    <h4 style="margin-top:0">
                        Confirmed Participants:
                        <span class="badge badge-confirmed">{{ $confirmed->count() }}</span>
                    </h4>

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

                                        <td>Approved</td>
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
                    <h4 style="margin-top:0">
                        Rejected Participants:
                        <span class="badge badge-rejected">{{ $rejected->count() }}</span>
                    </h4>

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

                                        <td>Rejected</td>
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

                <!-- Tasks Section -->


                <!-- Tasks Section -->

                @include('ngo.tasks.task_list', ['event' => $event])

                <!-- Create Task Section -->
                @include('ngo.tasks.task_create', ['event' => $event])
                <!--edit task-->
                @include('ngo.tasks.task_edit', ['event' => $event])
                <!-- Manage Tasks section (hidden by default) -->

                {{-- in manage.blade (parent) --}}
                @include('ngo.tasks.task_manage', [
                    'event' => $event,
                    'tasks' => $tasks,
                    'confirmedParticipants' => $confirmedParticipants,
                    'assignedMap' => $assignedMap,
                ])

                @include('ngo.events.email', [
                    'event' => $event,
                    'registered' => $registered,
                    'confirmed' => $confirmed,
                    'rejected' => $rejected,
                    'confirmedParticipants' => $confirmedParticipants, // optional, used by your email blade
                ])


                <!-- Attendance QR Section -->
                @include('ngo.attendances.qr', ['event' => $event])



            </section>


        </div>
    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.tiny.cloud/1/pfjth33chx6jf9i6f3dluc05zg5hatcny7fdyaiza5bmpwme/tinymce/6/tinymce.min.js"
        referrerpolicy="origin"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar toggles
            document.querySelectorAll('.sidebar .side-btn[data-show]').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    document.querySelectorAll('.sidebar .side-btn').forEach(b => b.classList.remove(
                        'active'));
                    this.classList.add('active');

                    const show = this.getAttribute('data-show');
                    document.querySelectorAll('.participant-section').forEach(sec => sec.style
                        .display = 'none');
                    const target = document.getElementById('section-' + show);
                    if (target) target.style.display = 'block';
                });
            });

            // Simple client-side search on visible section
            const searchInput = document.getElementById('search-input');
            searchInput && searchInput.addEventListener('input', function() {
                const q = this.value.trim().toLowerCase();
                const visible = Array.from(document.querySelectorAll('.participant-section')).find(s => s
                    .style.display !== 'none');
                if (!visible) return;
                visible.querySelectorAll('tbody tr').forEach(tr => {
                    // ignore "no rows" placeholders
                    if (!tr.id) return;
                    const text = tr.textContent.toLowerCase();
                    tr.style.display = text.includes(q) ? '' : 'none';
                });
            });

            // CSRF token
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // URL templates (Blade renders with '__REG__' placeholder)
            const approveUrlTemplate = @json(route('ngo.events.registrations.approve', ['event' => $event->event_id, 'registration' => '__REG__']));
            const rejectUrlTemplate = @json(route('ngo.events.registrations.reject', ['event' => $event->event_id, 'registration' => '__REG__']));

            // Delegate approve/reject clicks


            // ------- Delegated clicks (robust) -------
            document.addEventListener('click', function(e) {
                const approveBtn = e.target.closest && e.target.closest('.btn-approve');
                const rejectBtn = e.target.closest && e.target.closest('.btn-reject');

                if (approveBtn) {
                    const id = approveBtn.getAttribute('data-id');
                    handleAction('approve', id, approveBtn);
                    return;
                }
                if (rejectBtn) {
                    const id = rejectBtn.getAttribute('data-id');
                    handleAction('reject', id, rejectBtn);
                    return;
                }
            });

            // ------- Helper: extract data from a Registered row (defensive) -------
            function extractVolFromRow(regRow, registrationId) {
                if (!regRow) return {
                    registration_id: registrationId
                };
                const tds = Array.from(regRow.querySelectorAll('td'));
                const nameLink = tds[0]?.querySelector('a');
                const userHref = nameLink ? nameLink.getAttribute('href') : null;
                const userId = userHref ? userHref.split('/').pop() : (nameLink ? nameLink.textContent.trim() :
                    null);

                return {
                    registration_id: registrationId,
                    user_id: userId,
                    name: nameLink ? nameLink.textContent.trim() : (tds[0]?.textContent.trim() || ''),
                    email: tds[1]?.textContent.trim() || '',
                    contact: tds[2]?.textContent.trim() || '',
                    age: tds[3]?.textContent.trim() || '',
                    gender: tds[4]?.textContent.trim() || '',
                    skill: tds[5]?.textContent.trim() || '',
                    registrationDate: tds[8]?.textContent.trim() || ''
                };
            }

            // Replace your existing handleAction with this version (adds a confirm prompt)
            function handleAction(action, registrationId, btn) {
                if (!registrationId) return;

                // friendly verb & message
                const verb = action === 'approve' ? 'approve' : 'reject';
                const pretty = action === 'approve' ? 'Approve' : 'Reject';
                const confirmMsg =
                    `${pretty} this participant?\n\nThis action cannot be undone. Do you want to continue?`;

                // show native confirmation dialog
                if (!window.confirm(confirmMsg)) {
                    // user cancelled — do nothing
                    return;
                }

                // user confirmed -> disable the button and proceed
                if (btn) btn.disabled = true;

                let url = (action === 'approve') ? approveUrlTemplate : rejectUrlTemplate;
                url = url.replace('__REG__', registrationId);

                fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({})
                    })
                    .then(response => response.text().then(text => {
                        if (!response.ok) {
                            const err = new Error(text || `Request failed (${response.status})`);
                            err.bodyText = text;
                            err.status = response.status;
                            throw err;
                        }
                        try {
                            return JSON.parse(text || '{}');
                        } catch (parseErr) {
                            console.warn('Non-JSON response, falling back to DOM row data. text=', text);
                            return null;
                        }
                    }))
                    .then(data => {
                        if (btn) btn.disabled = false;

                        const regRow = document.getElementById('reg-row-' + registrationId);
                        let vol = (data && data.volunteer) ? data.volunteer : null;

                        if (!vol && regRow) vol = extractVolFromRow(regRow, registrationId);
                        vol = vol || {
                            registration_id: registrationId
                        };

                        // Update Registered row in-place (keeps it visible) — same as before
                        if (regRow) {
                            const cols = Array.from(regRow.querySelectorAll('td'));

                            if (cols[0]) {
                                cols[0].innerHTML = '';
                                if (vol.user_id) {
                                    const uid = String(vol.user_id).split('/').pop();
                                    const a = document.createElement('a');
                                    a.href = `/volunteers/${uid}`;
                                    a.className = 'text-primary text-decoration-none';
                                    a.title = 'View volunteer profile';
                                    a.textContent = vol.name || '';
                                    cols[0].appendChild(a);
                                } else {
                                    cols[0].textContent = vol.name || '';
                                }
                            }

                            if (cols[1]) cols[1].textContent = vol.email || '';
                            if (cols[2]) cols[2].textContent = vol.contact || '';
                            if (cols[3]) cols[3].textContent = vol.age ?? '';
                            if (cols[4]) cols[4].textContent = vol.gender || '';
                            if (cols[5]) cols[5].textContent = vol.skill || 'No skills';

                            if (cols[6]) {
                                cols[6].innerHTML = (action === 'approve') ?
                                    '<span class="badge badge-success">Approved</span>' :
                                    '<span class="badge badge-danger">Rejected</span>';
                            }

                            if (cols[7]) {
                                cols[7].innerHTML = '';
                                const viewBtn = document.createElement('button');
                                viewBtn.type = 'button';
                                viewBtn.className = 'btn-view-details';
                                try {
                                    viewBtn.setAttribute('data-json', JSON.stringify(vol));
                                } catch (e) {
                                    viewBtn.setAttribute('data-json', '{}');
                                }
                                viewBtn.textContent = 'View';
                                cols[7].appendChild(viewBtn);
                            }
                        }

                        // Also insert into other lists & update badges
                        if (action === 'approve') {
                            insertConfirmedRow(vol);
                            incrementBadge('confirmed', 1);
                        } else {
                            insertRejectedRow(vol);
                            incrementBadge('rejected', 1);
                        }

                        // keep registered row visible (so we DO NOT decrement registered badge here).
                        // If you want Registered to represent "pending only", remove the Registered row here and call:
                        //   if (regRow) regRow.remove();
                        //   incrementBadge('registered', -1);
                        updateTotalCount();
                    })
                    .catch(err => {
                        console.error('handleAction error:', err);
                        if (btn) btn.disabled = false;

                        // fallback UI update so user still sees change (best-effort)
                        const regRow = document.getElementById('reg-row-' + registrationId);
                        if (regRow) {
                            try {
                                const cols = Array.from(regRow.querySelectorAll('td'));
                                if (cols[6]) cols[6].innerHTML = (action === 'approve') ?
                                    '<span class="badge badge-success">Approved</span>' :
                                    '<span class="badge badge-danger">Rejected</span>';

                                if (cols[7]) {
                                    cols[7].innerHTML = '';
                                    const viewBtn = document.createElement('button');
                                    viewBtn.type = 'button';
                                    viewBtn.className = 'btn-view-details';
                                    const fallbackVol = extractVolFromRow(regRow, registrationId);
                                    try {
                                        viewBtn.setAttribute('data-json', JSON.stringify(fallbackVol));
                                    } catch (e) {
                                        viewBtn.setAttribute('data-json', '{}');
                                    }
                                    viewBtn.textContent = 'View';
                                    cols[7].appendChild(viewBtn);
                                }

                                const fallbackVol = extractVolFromRow(regRow, registrationId);
                                if (action === 'approve') insertConfirmedRow(fallbackVol), incrementBadge(
                                    'confirmed', 1);
                                else insertRejectedRow(fallbackVol), incrementBadge('rejected', 1);

                                updateTotalCount();
                            } catch (e) {
                                console.error('fallback DOM update failed', e);
                            }
                        }

                        let msg = 'Request failed';
                        if (err && err.bodyText) msg += ': ' + err.bodyText;
                        else if (err && err.message) msg = err.message;
                        alert(msg);
                    });
            }
            // ------- Insert full confirmed row (defensive, avoids duplicates) -------
            function insertConfirmedRow(vol) {
                const tbody = document.querySelector('#table-confirmed tbody');
                if (!tbody) return;

                const placeholder = tbody.querySelector('tr.empty-row');
                if (placeholder) placeholder.remove();

                if (document.getElementById('conf-row-' + vol.registration_id)) return;

                const tr = document.createElement('tr');
                tr.id = 'conf-row-' + vol.registration_id;

                // name
                const tdName = document.createElement('td');
                if (vol.user_id) {
                    const uid = String(vol.user_id).split('/').pop();
                    const a = document.createElement('a');
                    a.href = `/volunteers/${uid}`;
                    a.className = 'text-primary text-decoration-none';
                    a.textContent = vol.name || '';
                    tdName.appendChild(a);
                } else {
                    tdName.textContent = vol.name || '';
                }
                tr.appendChild(tdName);

                // email, contact, age, gender, skill
                ['email', 'contact', 'age', 'gender', 'skill'].forEach(k => {
                    const td = document.createElement('td');
                    td.textContent = vol[k] ?? (k === 'skill' ? 'No skills' : '');
                    tr.appendChild(td);
                });

                // status
                const tdStatus = document.createElement('td');
                tdStatus.textContent = 'Approved';
                tr.appendChild(tdStatus);

                // action view
                const tdAction = document.createElement('td');
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'btn-view-details';
                try {
                    btn.setAttribute('data-json', JSON.stringify(vol));
                } catch (e) {
                    btn.setAttribute('data-json', '{}');
                }
                btn.textContent = 'View';
                tdAction.appendChild(btn);
                tr.appendChild(tdAction);

                tbody.prepend(tr);
            }
            // ------- Insert full rejected row (defensive) -------
            function insertRejectedRow(vol) {
                const tbody = document.querySelector('#table-rejected tbody');
                if (!tbody) return;

                const placeholder = tbody.querySelector('tr.empty-row');
                if (placeholder) placeholder.remove();

                if (document.getElementById('rej-row-' + vol.registration_id)) return;

                const tr = document.createElement('tr');
                tr.id = 'rej-row-' + vol.registration_id;

                const tdName = document.createElement('td');
                if (vol.user_id) {
                    const uid = String(vol.user_id).split('/').pop();
                    const a = document.createElement('a');
                    a.href = `/volunteers/${uid}`;
                    a.className = 'text-primary text-decoration-none';
                    a.textContent = vol.name || '';
                    tdName.appendChild(a);
                } else {
                    tdName.textContent = vol.name || '';
                }
                tr.appendChild(tdName);

                ['email', 'contact', 'age', 'gender', 'skill'].forEach(k => {
                    const td = document.createElement('td');
                    td.textContent = vol[k] ?? (k === 'skill' ? 'No skills' : '');
                    tr.appendChild(td);
                });

                const tdStatus = document.createElement('td');
                tdStatus.textContent = 'Rejected';
                tr.appendChild(tdStatus);

                const tdAction = document.createElement('td');
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'btn-view-details';
                try {
                    btn.setAttribute('data-json', JSON.stringify(vol));
                } catch (e) {
                    btn.setAttribute('data-json', '{}');
                }
                btn.textContent = 'View';
                tdAction.appendChild(btn);
                tr.appendChild(tdAction);

                tbody.prepend(tr);
            }
            // Update all DOM nodes that represent a badge for "which".
            // Preferred: a single element with id="badge-<which>" (canonical).
            // Also supports copies with class .badge-<which> or data-badge="<which>".
            function incrementBadge(which, delta) {
                if (!which) return;

                const idSelector = '#badge-' + which;
                const classSelector = '.badge-' + which;
                const dataSelector = '[data-badge="' + which + '"]';

                // Update canonical id element if present
                const idEl = document.querySelector(idSelector);
                if (idEl) {
                    let n = parseInt(idEl.textContent || '0', 10);
                    n = Math.max(0, n + (delta || 0));
                    idEl.textContent = String(n);
                }

                // Update class copies (synchronize with id if id existed)
                const classEls = document.querySelectorAll(classSelector);
                if (classEls.length) {
                    const base = idEl ? parseInt(idEl.textContent || '0', 10) : null;
                    classEls.forEach(el => {
                        if (base !== null) {
                            el.textContent = String(base);
                        } else {
                            let cur = parseInt(el.textContent || '0', 10);
                            cur = Math.max(0, cur + (delta || 0));
                            el.textContent = String(cur);
                        }
                    });
                }

                // Update data-badge copies if any (same logic)
                const dataEls = document.querySelectorAll(dataSelector);
                if (dataEls.length) {
                    const base = idEl ? parseInt(idEl.textContent || '0', 10) : null;
                    dataEls.forEach(el => {
                        if (base !== null) {
                            el.textContent = String(base);
                        } else {
                            let cur = parseInt(el.textContent || '0', 10);
                            cur = Math.max(0, cur + (delta || 0));
                            el.textContent = String(cur);
                        }
                    });
                }
            }
            // Recalculate total across registered/confirmed/rejected badges and update all copies
            function updateTotalCount() {
                const r = parseInt(document.querySelector('#badge-registered')?.textContent || '0', 10);
                const c = parseInt(document.querySelector('#badge-confirmed')?.textContent || '0', 10);
                const j = parseInt(document.querySelector('#badge-rejected')?.textContent || '0', 10);
                const total = (isNaN(r) ? 0 : r) + (isNaN(c) ? 0 : c) + (isNaN(j) ? 0 : j);

                // update canonical total element and any copies with class .total-count-copy
                const totalEl = document.getElementById('total-count');
                if (totalEl) totalEl.textContent = String(total);
                document.querySelectorAll('.total-count-copy').forEach(el => el.textContent = String(total));

                // If you have other total copies using data-total, update them too
                document.querySelectorAll('[data-total]').forEach(el => el.textContent = String(total));
            }

            function escapeHtml(s) {
                if (!s) return '';
                return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g,
                    '&quot;').replace(/'/g, '&#39;');
            }
            // ===== Details modal handler (uses escapeHtml already defined above) =====
            (function registrationDetails() {
                const modal = document.getElementById('sr-details-modal');
                if (!modal) return;

                function openModalWith(reg) {
                    // reg is a plain object with fields from event_registrations
                    document.getElementById('sr-modal-name').textContent = escapeHtml(reg.name);
                    document.getElementById('sr-modal-email').textContent = escapeHtml(reg.email);
                    document.getElementById('sr-modal-contact').textContent = escapeHtml(reg.contactNumber);
                    document.getElementById('sr-modal-age-gender').textContent = (escapeHtml(reg.age) || '') +
                        ' / ' + (escapeHtml(reg.gender) || '');
                    document.getElementById('sr-modal-skill').textContent = escapeHtml(reg.skill ||
                        'No skills');
                    document.getElementById('sr-modal-company').textContent = escapeHtml(reg.company || '—');
                    document.getElementById('sr-modal-experience').textContent = escapeHtml(reg
                        .volunteeringExperience || '—');
                    document.getElementById('sr-modal-emer-name').textContent = escapeHtml(reg
                        .emergencyContact || '—');
                    document.getElementById('sr-modal-emer-number').textContent = escapeHtml(reg
                        .emergencyContactNumber || '—');
                    document.getElementById('sr-modal-emer-rel').textContent = escapeHtml(reg
                        .contactRelationship || '—');
                    document.getElementById('sr-modal-address').textContent = escapeHtml(reg.address || '—');
                    document.getElementById('sr-modal-created-at').textContent = escapeHtml(reg
                        .registrationDate || reg.created_at || '');

                    modal.style.display = 'flex';
                    modal.setAttribute('aria-hidden', 'false');

                    // optionally trap focus, etc. (keep simple)
                }

                function closeModal() {
                    modal.style.display = 'none';
                    modal.setAttribute('aria-hidden', 'true');
                }

                // Delegated click listener (safe — won't conflict)
                document.addEventListener('click', function(e) {
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

                    // close actions (close button or clicking backdrop)
                    if (e.target.closest('[data-close="true"]')) {
                        // close when user clicks element with data-close (backdrop or close btn)
                        closeModal();
                    }
                });

                // close on ESC
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && modal.style.display === 'flex') closeModal();
                });
            })();
            (function tabsBlock() {
                const indicator = document.querySelector('.nav-indicator');
                const allTabs = Array.from(document.querySelectorAll('.nav-tab'));
                if (!allTabs.length || !indicator) return;

                // Only tabs that are JS-driven get a click handler
                const jsTabs = allTabs.filter(t => (t.getAttribute('href') || '#') === '#');

                // Initial active tab (prefer the one already marked active)
                const initialActive = document.querySelector('.nav-tab.active') || allTabs[0];
                if (initialActive) {
                    setActiveTab(initialActive.getAttribute('data-tab'));
                }

                jsTabs.forEach(tab => {
                    tab.addEventListener('click', function(e) {
                        e.preventDefault(); // only for href="#"
                        const tabName = this.getAttribute('data-tab');
                        setActiveTab(tabName);
                    });
                });

                function setActiveTab(tabName) {
                    allTabs.forEach(t => t.classList.toggle('active', t.getAttribute('data-tab') === tabName));
                    const activeTab = document.querySelector(`.nav-tab[data-tab="${tabName}"]`);
                    if (activeTab) {
                        positionIndicator(activeTab);
                        updateContent(tabName);
                    }
                }

                function positionIndicator(activeTab) {
                    indicator.style.width = activeTab.offsetWidth + 'px';
                    indicator.style.transform = `translateX(${activeTab.offsetLeft}px)`;
                }

                function updateContent(tabName) {
                    const contentArea = document.querySelector('.content-placeholder');
                    if (!contentArea) return;
                    switch (tabName) {
                        case 'event':
                            contentArea.innerHTML = `
            <i class="fas fa-calendar-day"></i>
            <h3>Create New Event</h3>
            <p>Set up a new event with details, date, and location</p>`;
                            break;
                        case 'manage':
                            contentArea.innerHTML = `
            <i class="fas fa-tasks"></i>
            <h3>Manage Events</h3>
            <p>View and manage all your events in one place</p>`;
                            break;
                        default:
                            contentArea.innerHTML = '';
                    }
                }

                // Keep indicator aligned on resize
                window.addEventListener('resize', () => {
                    const current = document.querySelector('.nav-tab.active');
                    if (current) positionIndicator(current);
                });
            })();
        });
    </script>
    {{-- stack for scripts pushed from partials like task_create.blade --}}

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
    @include('layouts.ngo_footer');
</body>

</html>
