@php
    // Defensive defaults so view won't crash if include forgot something
    $tasks = $tasks ?? collect();
    $assignedMap = $assignedMap ?? [];
    $confirmedParticipants = $confirmedParticipants ?? collect();
    // $disabled expected to be passed from parent (true when event ended)
    $disabled = $disabled ?? false;
    $isAdminReadonly = $isAdminReadonly ?? false;

@endphp

<div class="card mt-4 participant-section {{ $disabled ? 'is-disabled' : '' }}"
     id="section-manage-tasks"
     style="display:none"
     data-disabled="{{ $disabled ? '1' : '0' }}"
     aria-disabled="{{ $disabled ? 'true' : 'false' }}">

    <div class="card-body">
        <h3 class="mb-3">Manage Tasks</h3>
         @include('layouts/messages')

       @if ($disabled && !$isAdminReadonly)
    <div class="alert alert-warning">
        Task management is disabled — this event has ended.
    </div>
@endif


        <div class="manage-task-card-list">

    @forelse ($tasks as $task)
        <div class="manage-task-card"
             data-task-id="{{ (string) $task->task_id }}"
             data-assigned="{{ $task->assignments->pluck('user_id')->map(fn($v) => (string) $v)->implode(',') }}">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <h5 class="task-title mb-1">{{ $task->title }}</h5>
                    <p class="text-muted mb-0">
                        {{ \Illuminate\Support\Str::limit($task->description, 100) }}
                    </p>
                </div>

                {{-- ACTION --}}
                @if($disabled)
                    <button type="button"
                            class="btn btn-sm btn-outline-success assign-btn is-disabled"
                            data-task-id="{{ (string) $task->task_id }}"
                            aria-disabled="true"
                            tabindex="-1"
                            title="Assigning disabled — event has ended">
                        Assign To
                    </button>
                @else
                    <button type="button"
                            class="btn btn-sm btn-outline-success assign-btn"
                            data-task-id="{{ (string) $task->task_id }}">
                        Assign To
                    </button>
                @endif
            </div>

            {{-- ASSIGNED USERS --}}
            <div class="assigned-users mt-2">

                @forelse($task->assignments as $a)
                    <span class="badge bg-primary me-1 assigned-badge"
                          data-user-id="{{ (string) $a->user_id }}">

                        {{ optional($a->user)->name ?? 'User ' . $a->user_id }}

                        @if($disabled)
                            <button type="button"
                                class="btn-close btn-close-white btn-sm ms-1 unassign-btn is-disabled"
                                aria-label="Remove"
                                data-task-id="{{ (string) $task->task_id }}"
                                data-user-id="{{ (string) $a->user_id }}"
                                aria-disabled="true"
                                tabindex="-1"
                                title="Unassign disabled — event ended">
                            </button>
                        @else
                            <button type="button"
                                class="btn-close btn-close-white btn-sm ms-1 unassign-btn"
                                aria-label="Remove"
                                data-task-id="{{ (string) $task->task_id }}"
                                data-user-id="{{ (string) $a->user_id }}">
                            </button>
                        @endif
                    </span>
                @empty
                    <span class="text-muted">—</span>
                @endforelse

            </div>
        </div>

    @empty
        <div class="text-center text-muted py-4">
            No tasks found.
        </div>
    @endforelse

</div>

    </div>
</div>

{{-- Assign Modal (keep this OUTSIDE any .card so Bootstrap styles apply) --}}
<div class="modal fade" id="assignModal" tabindex="-1" aria-hidden="{{ $disabled ? 'true' : 'false' }}">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Assign Participants <small id="modal-task-title" class="text-muted"></small>
                </h5>
                {{-- Keep close available even when disabled so user can dismiss modal --}}
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                @if($disabled)
                    <div class="alert alert-warning">
                        Assigning participants is disabled because the event has ended.
                    </div>
                @endif

                <div class="mb-3">
                    <input type="text" id="assign-search" class="form-control"
                           placeholder="Search participants…" {{ $disabled ? 'disabled aria-disabled="true"' : '' }}>
                </div>

                <form id="assignForm">
                    <input type="hidden" name="task_id" id="assign-task-id">
                    <div class="list-group" id="participants-list" style="max-height:360px; overflow:auto;">

                        @forelse ($confirmedParticipants as $p)
                            @php
                                // Ensure participantId is resolved
                                $participantId = (string) ($p->id ?? ($p->user_id ?? ($p->registration_id ?? null)));
                                $assignedTasksCsv = isset($assignedMap[$participantId])
                                    ? $assignedMap[$participantId]
                                    : '';
                            @endphp

                            <label class="list-group-item d-flex align-items-start gap-2"
                                data-user-id="{{ $participantId }}" data-assigned-tasks="{{ $assignedTasksCsv }}">

                                {{-- Checkbox: disabled when $disabled --}}
                                <input type="checkbox"
                                    class="form-check-input mt-1 participant-checkbox"
                                    value="{{ $participantId }}"
                                    data-user-id="{{ $participantId }}"
                                    data-assigned-tasks="{{ $assignedTasksCsv }}"
                                    name="user_ids[]"
                                    {{ $disabled ? 'disabled aria-disabled="true" tabindex="-1"' : '' }}>

                                <div>
                                    <div class="fw-semibold">{{ $p->name ?? 'User ' . $participantId }}</div>
                                    <div class="text-muted small">{{ $p->email ?? '' }}</div>
                                </div>
                            </label>
                        @empty
                            <div class="text-muted small px-3">No confirmed participants yet.</div>
                        @endforelse
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>

                {{-- Assign submit: disabled when $disabled --}}
                @if($disabled)
                    <button type="button" class="btn btn-primary" id="assign-submit" disabled aria-disabled="true" tabindex="-1">
                        Assign
                    </button>
                @else
                    <button type="submit" form="assignForm" class="btn btn-primary" id="assign-submit" disabled>
                        Assign
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    'use strict';

    const baseTasks       = "{{ url('ngo/tasks') }}";
    const manageSection   = document.getElementById('section-manage-tasks'); // ⭐ scope everything here
    const assignModalEl   = document.getElementById('assignModal');

    if (!manageSection) {
        console.warn('[assign] #section-manage-tasks not found');
        return;
    }
    if (!assignModalEl) {
        console.warn('[assign] assignModal not found');
        return;
    }

    const assignModal   = new bootstrap.Modal(assignModalEl, { backdrop: 'static' });
    const assignForm    = document.getElementById('assignForm');
    const participants  = document.getElementById('participants-list');
    const assignBtnEl   = document.getElementById('assign-submit');
    const searchInput   = document.getElementById('assign-search');
    const modalTitle    = document.getElementById('modal-task-title');
    let currentTaskId   = null;

    // ----------------------------------------------------
    // Helpers
    // ----------------------------------------------------
    const toS = v => v == null ? '' : String(v).trim();

    function escapeHtml(s) {
        if (s == null) return '';
        return String(s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    // ⭐ Only look inside #section-manage-tasks so we don't touch the Task List table
   function getAllRowsByTaskId(taskId) {
    const t = toS(taskId);
    if (!t || !manageSection) return [];

    return Array.from(
        manageSection.querySelectorAll('[data-task-id]')
    ).filter(el => toS(el.getAttribute('data-task-id')) === t);
}


    function getRowByTaskId(taskId) {
        const rows = getAllRowsByTaskId(taskId);
        if (!rows.length) return null;
        const withAssigned = rows.find(r => r.hasAttribute('data-assigned'));
        return withAssigned || rows[0];
    }

    // Inject small CSS for hidden items (if not already)
    (function injectCSS() {
        if (document.getElementById('task-helper-style')) return;
        const css = `
            .list-group-item.already-assigned { opacity: 0.7; }
            .hidden-assigned { display: none !important; }
        `;
        const s = document.createElement('style');
        s.id = 'task-helper-style';
        s.appendChild(document.createTextNode(css));
        document.head.appendChild(s);
    })();

    function readAssignedFromDOM(taskId) {
        const rows = getAllRowsByTaskId(taskId);
        const ids = new Set();
        rows.forEach(r => {
            const csv = (r.getAttribute('data-assigned') || '').trim();
            if (!csv) return;
            csv.split(',')
               .map(toS)
               .filter(Boolean)
               .forEach(id => ids.add(id));
        });
        return Array.from(ids);
    }

    async function fetchAssignedFromServer(taskId) {
        try {
            if (!taskId) return [];
            const url = `${baseTasks}/${encodeURIComponent(taskId)}/assigned`;
            const resp = await fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            if (!resp.ok) {
                console.warn('[assign] fetchAssignedFromServer non-ok', resp.status);
                return readAssignedFromDOM(taskId);
            }
            const json = await resp.json();
            if (Array.isArray(json.assigned)) {
                return json.assigned.map(toS).filter(Boolean);
            }
            return readAssignedFromDOM(taskId);
        } catch (err) {
            console.warn('[assign] fetchAssignedFromServer failed', err);
            return readAssignedFromDOM(taskId);
        }
    }

    async function readAssignedIdsForTask(taskId) {
        return await fetchAssignedFromServer(taskId);
    }

    // ----------------------------------------------------
    // Open "Assign" modal
    // ----------------------------------------------------
    document.addEventListener('click', async function(e) {
        const btn = e.target.closest && e.target.closest('.assign-btn');
        if (!btn || !manageSection.contains(btn)) return; // ⭐ only respond within manage section

        e.preventDefault();

        currentTaskId = btn.dataset.taskId;
        document.getElementById('assign-task-id').value = currentTaskId || '';

        const row = getRowByTaskId(currentTaskId);
        modalTitle.textContent = row
            ? ' — ' + (row.querySelector('.task-title')?.textContent.trim() || '')
            : '';

        // Reset search and checkboxes
        if (searchInput) searchInput.value = '';
        if (participants) {
            Array.from(participants.querySelectorAll('.participant-checkbox')).forEach(cb => {
                cb.checked = false;
                cb.disabled = false;
                const label = cb.closest('.list-group-item');
                if (label) label.classList.remove('already-assigned', 'hidden-assigned');
            });
        }

        let assignedArr = [];
        try {
            assignedArr = await readAssignedIdsForTask(currentTaskId);
        } catch (err) {
            console.warn('[assign] readAssignedIdsForTask failed', err);
            assignedArr = (row ? (row.getAttribute('data-assigned') || '') : '')
                .split(',')
                .map(toS)
                .filter(Boolean);
        }
        const assignedSet = new Set((assignedArr || []).map(toS));

        // Hide participants already assigned to this task
        if (participants) {
            Array.from(participants.querySelectorAll('.list-group-item')).forEach(label => {
                const cb  = label.querySelector('.participant-checkbox');
                const uid = toS(cb?.value || label.dataset.userId || '');
                if (assignedSet.has(uid)) {
                    label.classList.add('hidden-assigned');
                    label.dataset.assignedTasks = (label.dataset.assignedTasks || '')
                        .split(',')
                        .filter(Boolean)
                        .concat([toS(currentTaskId)])
                        .filter(Boolean)
                        .join(',');
                } else {
                    label.classList.remove('hidden-assigned');
                }
            });
        }

        updateAssignButtonState();
        assignModal.show();
    });

    assignModalEl.addEventListener('shown.bs.modal', function() {
        if (currentTaskId) {
            hideAssignedParticipantsInModal(currentTaskId);
        }
    });

    async function hideAssignedParticipantsInModal(taskId) {
        try {
            const assignedArr = await readAssignedIdsForTask(taskId);
            const assignedSet = new Set((assignedArr || []).map(toS));
            if (!participants) return;

            Array.from(participants.querySelectorAll('.list-group-item')).forEach(label => {
                const cb  = label.querySelector('.participant-checkbox');
                const uid = toS(cb?.value || label.dataset.userId || '');
                if (assignedSet.has(uid)) {
                    label.classList.add('hidden-assigned');
                    label.dataset.assignedTasks = (label.dataset.assignedTasks || '')
                        .split(',')
                        .filter(Boolean)
                        .concat([toS(taskId)])
                        .filter(Boolean)
                        .join(',');
                } else {
                    label.classList.remove('hidden-assigned');
                }
            });
        } catch (err) {
            console.error('[assign] hideAssignedParticipantsInModal error', err);
        }
    }

    // ----------------------------------------------------
    // Search inside modal
    // ----------------------------------------------------
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const q = this.value.trim().toLowerCase();
            if (!participants) return;

            Array.from(participants.querySelectorAll('.list-group-item')).forEach(label => {
                if (label.classList.contains('hidden-assigned')) {
                    // stay hidden
                    return;
                }
                const txt = label.textContent.toLowerCase();
                label.style.display = !q || txt.includes(q) ? '' : 'none';
            });
        });
    }

    // ----------------------------------------------------
    // Enable / Disable Assign button
    // ----------------------------------------------------
    if (participants) {
        participants.addEventListener('change', updateAssignButtonState);
    }

    function updateAssignButtonState() {
        try {
            if (!participants) return;
            const anyNewChecked =
                participants.querySelectorAll('.participant-checkbox:checked:not([disabled])').length > 0;
            if (assignBtnEl) assignBtnEl.disabled = !anyNewChecked;
        } catch (err) {
            console.error('[assign] updateAssignButtonState error', err);
        }
    }

    function getCheckedNewValues() {
        try {
            if (!participants) return [];
            return Array.from(
                participants.querySelectorAll('.participant-checkbox:checked:not([disabled])')
            ).map(cb =>
                toS(cb.value || cb.dataset.userId || cb.closest('label')?.dataset?.userId)
            ).filter(Boolean);
        } catch (err) {
            console.error('[assign] getCheckedNewValues error', err);
            return [];
        }
    }

    // ----------------------------------------------------
    // Assign submit
    // ----------------------------------------------------
    if (assignForm) {
        assignForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            if (!currentTaskId) {
                alert('Internal error: missing task id');
                return;
            }

            const newIds = getCheckedNewValues();
            if (!newIds.length) {
                alert('Please select at least one participant to assign.');
                return;
            }

            const url   = `${baseTasks}/${encodeURIComponent(currentTaskId)}/assign`;
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            try {
                if (assignBtnEl) {
                    assignBtnEl.disabled = true;
                    assignBtnEl.textContent = 'Assigning…';
                }

                const resp = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ user_ids: newIds })
                });

                const bodyText = await resp.text();
                let json = null;

                try {
                    json = bodyText ? JSON.parse(bodyText) : {};
                } catch (err) {
                    console.warn('[assign] response not JSON', err, bodyText);
                    json = null;
                }

                if (!resp.ok) {
                    const message = (json && json.message) ? json.message : `Assign failed (${resp.status})`;
                    alert(message);
                    return;
                }

                const assignedFromServer = Array.isArray(json?.assigned)
                    ? json.assigned.map(toS)
                    : null;

                const assignedIds = assignedFromServer || (function() {
                    const row  = getRowByTaskId(currentTaskId);
                    const prev = (row?.getAttribute('data-assigned') || '')
                        .split(',').map(toS).filter(Boolean);
                    return Array.from(new Set(prev.concat(newIds)));
                })();

                // Update task row badges and modal list
                updateAssignedBadges(currentTaskId, assignedIds);
                syncModalAfterAssign(assignedIds, currentTaskId);

                assignModal.hide();
                showFlash('Participants assigned', 'success');
            } catch (err) {
                console.error('[assign] fetch error', err);
                alert('Network or server error when assigning participants.');
            } finally {
                if (assignBtnEl) {
                    assignBtnEl.disabled = false;
                    assignBtnEl.textContent = 'Assign';
                }
            }
        });
    } else {
        console.warn('[assign] assignForm not found');
    }

    // ----------------------------------------------------
    // Unassign handler
    // ----------------------------------------------------
    document.addEventListener('click', async function(e) {
        const btn = e.target.closest && e.target.closest('.unassign-btn');
        if (!btn || !manageSection.contains(btn)) return; // ⭐ ignore unassign outside manage section

        e.preventDefault();

        if (!confirm('Remove this participant from the task?')) return;

        try {
            const taskId = btn.dataset.taskId;
            const userId = btn.dataset.userId;
            if (!taskId || !userId) {
                alert('Missing task/user id');
                return;
            }

            const url   = `${baseTasks}/${encodeURIComponent(taskId)}/unassign/${encodeURIComponent(userId)}`;
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            const resp = await fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const bodyText = await resp.text();
            let json = null;

            try {
                json = bodyText ? JSON.parse(bodyText) : {};
            } catch {
                json = null;
            }

            if (!resp.ok) {
                alert((json && json.message) ? json.message : `Unassign failed (${resp.status})`);
                return;
            }

            const badge = btn.closest('.assigned-badge');
            if (badge) badge.remove();

            decrementAssignedCount(taskId, userId);
            showParticipantInModal(userId);
            showFlash('Participant unassigned', 'success');
        } catch (err) {
            console.error('[unassign] error', err);
            alert('Failed to unassign participant (network or server error).');
        }
    });

    // ----------------------------------------------------
    // Update badges in manage tasks table rows
    // ----------------------------------------------------
   function updateAssignedBadges(taskId, assignedIds) {
    const cards = getAllRowsByTaskId(taskId);
    if (!cards.length) return;

    const nameById = {};

    // Build name map from modal
    if (participants) {
        participants.querySelectorAll('.list-group-item').forEach(label => {
            const cb = label.querySelector('.participant-checkbox');
            if (!cb) return;

            const id = toS(cb.value || label.dataset.userId);
            const name = label.querySelector('.fw-semibold')?.textContent?.trim();
            if (id && name) nameById[id] = name;
        });
    }

    cards.forEach(card => {
        const container = card.querySelector('.assigned-users');
        if (!container) return;

        container.innerHTML = '';

        if (!assignedIds || !assignedIds.length) {
            container.innerHTML = '<span class="text-muted">—</span>';
            card.setAttribute('data-assigned', '');
            return;
        }

        assignedIds.forEach(uid => {
            const span = document.createElement('span');
            span.className = 'badge bg-primary me-1 assigned-badge';
            span.dataset.userId = uid;

            span.innerHTML = `
                ${escapeHtml(nameById[uid] || 'User ' + uid)}
                <button type="button"
                    class="btn-close btn-close-white btn-sm ms-1 unassign-btn"
                    aria-label="Remove"
                    data-task-id="${escapeHtml(taskId)}"
                    data-user-id="${escapeHtml(uid)}">
                </button>
            `;

            container.appendChild(span);
        });

        card.setAttribute('data-assigned', assignedIds.join(','));
    });
}


    function decrementAssignedCount(taskId, userId) {
        const rows = getAllRowsByTaskId(taskId);
        if (!rows.length) {
            console.warn('[assign] decrementAssignedCount: rows not found for', taskId);
            return;
        }

        rows.forEach(row => {
            const current = (row.getAttribute('data-assigned') || '')
                .split(',')
                .map(toS)
                .filter(Boolean);

            const remaining = current.filter(id => id !== toS(userId));

            try {
                row.setAttribute('data-assigned', remaining.join(','));
            } catch (err) {
                console.warn('[assign] decrementAssignedCount: failed to update attribute', err);
            }

            let cell = row.querySelector('.assigned-users') ||
                       row.querySelector('td:nth-child(3)') ||
                       (row.cells && row.cells[2]);

            if (cell && !remaining.length) {
                cell.innerHTML = '<span class="text-muted">—</span>';
            } else if (cell) {
                const badges = Array.from(cell.querySelectorAll('.assigned-badge'));
                badges.forEach(b => {
                    if (toS(b.getAttribute('data-user-id')) === toS(userId)) {
                        b.remove();
                    }
                });
            }
        });
    }

    function syncModalAfterAssign(assignedIds, taskId) {
        const taskIdS = toS(taskId || currentTaskId);

        // update data-assigned attribute on all manage rows
        try {
            const rows = getAllRowsByTaskId(taskIdS);
            rows.forEach(row => {
                if (row && Array.isArray(assignedIds)) {
                    const assignedString = assignedIds.map(id => toS(id)).join(',');
                    row.setAttribute('data-assigned', assignedString);
                }
            });
        } catch (err) {
            console.warn('[assign] syncModalAfterAssign: failed to update data-assigned attribute', err);
        }

        // hide assigned participants in modal
        try {
            const setA = new Set((assignedIds || []).map(String));
            if (!participants) return;

            Array.from(participants.querySelectorAll('.list-group-item')).forEach(lbl => {
                const cb  = lbl.querySelector('.participant-checkbox');
                const uid = toS(cb?.value || lbl.dataset.userId || '');
                if (setA.has(uid)) {
                    lbl.classList.add('hidden-assigned');
                    lbl.style.display = 'none';
                }
            });
        } catch (err) {
            console.warn('[assign] syncModalAfterAssign error', err);
        }
    }

    function showParticipantInModal(userId) {
        const uid = toS(userId);
        if (!participants) return;

        Array.from(participants.querySelectorAll('.list-group-item')).forEach(lbl => {
            const cb  = lbl.querySelector('.participant-checkbox');
            const val = toS(cb?.value || lbl.dataset.userId || '');
            if (val === uid) {
                lbl.classList.remove('hidden-assigned');
                lbl.style.display = '';

                const csv = toS(cb.dataset.assignedTasks || lbl.dataset.assignedTasks || '');
                const arr = csv ? csv.split(',').map(s => s.trim()).filter(Boolean) : [];
                const newArr = arr.filter(id => id !== toS(currentTaskId));
                const newCsv = newArr.join(',');

                cb.dataset.assignedTasks  = newCsv;
                lbl.dataset.assignedTasks = newCsv;
            }
        });
    }

    function showFlash(text, type = 'success') {
        if (window.flashMessage) {
            window.flashMessage(text, type);
            return;
        }
        const el = document.createElement('div');
        el.className = 'alert ' + (type === 'success' ? 'alert-success' : 'alert-danger');
        el.style.position = 'fixed';
        el.style.right = '20px';
        el.style.top = '20px';
        el.style.zIndex = 9999;
        el.textContent = text;
        document.body.appendChild(el);
        setTimeout(() => el.remove(), 2200);
    }

    // ----------------------------------------------------
    // Hooks for approve/reject → Manage Tasks modal
    // ----------------------------------------------------
    window.addConfirmedParticipantForTasks = function(vol) {
        const userId = toS(vol.user_id);
        if (!userId) return;
        if (!participants) return;

        // Remove "No confirmed participants yet." placeholder if present
        const placeholder = participants.querySelector('.no-confirmed-placeholder, .text-muted.small.px-3');
        if (placeholder) placeholder.remove();

        // If already exists, just un-hide
        let label = participants.querySelector(`.list-group-item[data-user-id="${userId}"]`);
        if (label) {
            label.classList.remove('hidden-assigned');
            label.style.display = '';
            const cb = label.querySelector('.participant-checkbox');
            if (cb) cb.disabled = false;
            return;
        }

        const name  = vol.name  || ('User ' + userId);
        const email = vol.email || '';

        label = document.createElement('label');
        label.className = 'list-group-item d-flex align-items-start gap-2';
        label.setAttribute('data-user-id', userId);
        label.setAttribute('data-assigned-tasks', '');

        const cb = document.createElement('input');
        cb.type  = 'checkbox';
        cb.className = 'form-check-input mt-1 participant-checkbox';
        cb.value     = userId;
        cb.setAttribute('data-user-id', userId);
        cb.setAttribute('data-assigned-tasks', '');

        const info = document.createElement('div');
        info.innerHTML =
            '<div class="fw-semibold">' + escapeHtml(name) + '</div>' +
            '<div class="text-muted small">' + escapeHtml(email) + '</div>';

        label.appendChild(cb);
        label.appendChild(info);

        participants.appendChild(label);
    };

    window.removeConfirmedParticipantForTasks = function(vol) {
        const userId = toS(vol.user_id);
        if (!userId) return;
        if (!participants) return;

        const label = participants.querySelector(`.list-group-item[data-user-id="${userId}"]`);
        if (label) label.remove();

        // If no confirmed participants left, show placeholder text again
        const stillAny = participants.querySelector('.list-group-item');
        if (!stillAny) {
            const placeholder = document.createElement('div');
            placeholder.className = 'text-muted small px-3 no-confirmed-placeholder';
            placeholder.textContent = 'No confirmed participants yet.';
            participants.appendChild(placeholder);
        }
    };

    console.log('[assign] task_manage script loaded (scoped to #section-manage-tasks)');
});
</script>
@endpush


