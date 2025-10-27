@php
    // Defensive defaults so view won't crash if include forgot something
    $tasks = $tasks ?? collect();
    $assignedMap = $assignedMap ?? [];
    $confirmedParticipants = $confirmedParticipants ?? collect();
    // $disabled expected to be passed from parent (true when event ended)
    $disabled = $disabled ?? false;
@endphp

<div class="card mt-4 participant-section {{ $disabled ? 'is-disabled' : '' }}"
     id="section-manage-tasks"
     style="display:none"
     data-disabled="{{ $disabled ? '1' : '0' }}"
     aria-disabled="{{ $disabled ? 'true' : 'false' }}">

    <div class="card-body">
        <h3 class="mb-3">Manage Tasks</h3>

        @if($disabled)
            <div class="alert alert-warning mb-3">
                Task management is disabled — this event has ended.
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>Task</th>
                        <th>Description</th>
                        <th>Assigned To <small class="text-muted assigned-count"></small></th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tasks as $task)
                        <tr data-task-id="{{ (string) $task->task_id }}"
                            data-assigned="{{ $task->assignments->pluck('user_id')->map(fn($v) => (string) $v)->implode(',') }}">
                            <td class="task-title">{{ $task->title }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($task->description, 100) }}</td>

                            <td class="assigned-users">
                                @forelse($task->assignments as $a)
                                    <span class="badge bg-primary me-1 assigned-badge"
                                        data-user-id="{{ (string) $a->user_id }}">
                                        {{ optional($a->user)->name ?? 'User ' . $a->user_id }}

                                        {{-- Unassign button: disabled when $disabled --}}
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
                            </td>

                            <td>
                                {{-- Assign button: disabled when $disabled --}}
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
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No tasks found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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

            // Base URL rendered by Blade
            const baseTasks = "{{ url('ngo/tasks') }}";

            const assignModalEl = document.getElementById('assignModal');
            if (!assignModalEl) {
                console.warn('[assign] assignModal not found');
                return;
            }

            const assignModal = new bootstrap.Modal(assignModalEl, {
                backdrop: 'static'
            });
            const assignForm = document.getElementById('assignForm');
            const participants = document.getElementById('participants-list');
            const assignBtnEl = document.getElementById('assign-submit');
            const searchInput = document.getElementById('assign-search');
            const modalTitle = document.getElementById('modal-task-title');
            let currentTaskId = null;

            // ---------- small helpers ----------
            function toS(v) {
                return v == null ? '' : String(v).trim();
            }

            function escapeHtml(s) {
                if (s == null) return '';
                return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g,
                    '&quot;').replace(/'/g, '&#39;');
            }

            // Robust row finders that handle duplicate rows
            function getAllRowsByTaskId(taskId) {
                const t = toS(taskId);
                if (!t) return [];
                return Array.from(document.querySelectorAll('tr[data-task-id]')).filter(r => toS(r.getAttribute(
                    'data-task-id')) === t);
            }

            function getRowByTaskId(taskId) {
                const rows = getAllRowsByTaskId(taskId);
                if (!rows.length) return null;
                const withAssigned = rows.find(r => r.hasAttribute('data-assigned'));
                return withAssigned || rows[0];
            }

            // Inject CSS once
            (function injectCSS() {
                if (document.getElementById('task-helper-style')) return;
                const css =
                    '\n                    .list-group-item.already-assigned { opacity: 0.7; }\n                    .hidden-assigned { display: none !important; }\n                ';
                const s = document.createElement('style');
                s.id = 'task-helper-style';
                s.appendChild(document.createTextNode(css));
                document.head.appendChild(s);
            })();

            // Read assigned IDs from the DOM rows for the task (fallback when server endpoint missing)
            function readAssignedFromDOM(taskId) {
                const rows = getAllRowsByTaskId(taskId);
                const ids = new Set();
                rows.forEach(r => {
                    const csv = (r.getAttribute('data-assigned') || '').trim();
                    if (!csv) return;
                    csv.split(',').map(s => toS(s)).filter(Boolean).forEach(id => ids.add(id));
                });
                return Array.from(ids);
            }

            // ---------- fetch authoritative assigned list from server (with DOM fallback) ----------
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
                    if (Array.isArray(json.assigned)) return json.assigned.map(toS).filter(Boolean);
                    return readAssignedFromDOM(taskId);
                } catch (err) {
                    console.warn('[assign] fetchAssignedFromServer failed', err);
                    return readAssignedFromDOM(taskId);
                }
            }

            // ---------- read assigned ids for task (prefers server, falls back to DOM) ----------
            async function readAssignedIdsForTask(taskId) {
                return await fetchAssignedFromServer(taskId);
            }

            // ---------- Modal open handler (delegated) ----------
            document.addEventListener('click', async function(e) {
                const btn = e.target.closest && e.target.closest('.assign-btn');
                if (!btn) return;
                e.preventDefault();
                currentTaskId = btn.dataset.taskId;
                document.getElementById('assign-task-id').value = currentTaskId || '';
                const row = getRowByTaskId(currentTaskId);
                modalTitle.textContent = row ? (' — ' + (row.querySelector('.task-title')?.textContent
                    .trim() || '')) : '';
                if (searchInput) searchInput.value = '';
                if (participants) {
                    Array.from(participants.querySelectorAll('.participant-checkbox')).forEach(cb => {
                        cb.checked = false;
                        cb.disabled = false;
                        const label = cb.closest('.list-group-item');
                        if (label) label.classList.remove('already-assigned',
                        'hidden-assigned');
                    });
                }
                let assignedArr = [];
                try {
                    assignedArr = await readAssignedIdsForTask(currentTaskId);
                } catch (err) {
                    console.warn('[assign] readAssignedIdsForTask failed', err);
                    assignedArr = (row ? (row.getAttribute('data-assigned') || '') : '').split(',').map(
                        toS).filter(Boolean);
                }
                const assignedSet = new Set((assignedArr || []).map(toS));
                if (participants) {
                    Array.from(participants.querySelectorAll('.list-group-item')).forEach(label => {
                        const cb = label.querySelector('.participant-checkbox');
                        const uid = toS(cb?.value || label.dataset.userId || '');
                        if (assignedSet.has(uid)) {
                            label.classList.add('hidden-assigned');
                            label.dataset.assignedTasks = (label.dataset.assignedTasks || '')
                                .split(',').filter(Boolean).concat([toS(currentTaskId)]).filter(
                                    Boolean).join(',');
                        } else {
                            label.classList.remove('hidden-assigned');
                        }
                    });
                }
                updateAssignButtonState();
                assignModal.show();
            });

            assignModalEl.addEventListener && assignModalEl.addEventListener('shown.bs.modal', function() {
                if (currentTaskId) hideAssignedParticipantsInModal(currentTaskId);
            });

            // ---------- hideAssignedParticipantsInModal (kept for compatibility) ----------
            async function hideAssignedParticipantsInModal(taskId) {
                try {
                    const assignedArr = await readAssignedIdsForTask(taskId);
                    const assignedSet = new Set((assignedArr || []).map(toS));
                    if (!participants) return;
                    Array.from(participants.querySelectorAll('.list-group-item')).forEach(label => {
                        const cb = label.querySelector('.participant-checkbox');
                        const uid = toS(cb?.value || label.dataset.userId || '');
                        if (assignedSet.has(uid)) {
                            label.classList.add('hidden-assigned');
                            label.dataset.assignedTasks = (label.dataset.assignedTasks || '').split(',')
                                .filter(Boolean).concat([toS(taskId)]).filter(Boolean).join(',');
                        } else {
                            label.classList.remove('hidden-assigned');
                        }
                    });
                } catch (err) {
                    console.error('[assign] hideAssignedParticipantsInModal error', err);
                }
            }

            // ---------- search and button state ----------
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const q = this.value.trim().toLowerCase();
                    if (!participants) return;
                    Array.from(participants.querySelectorAll('.list-group-item')).forEach(label => {
                        if (label.classList.contains('hidden-assigned'))
                    return; // keep hidden ones out
                        const txt = label.textContent.toLowerCase();
                        label.style.display = q === '' || txt.includes(q) ? '' : 'none';
                    });
                });
            }

            if (participants) participants.addEventListener('change', updateAssignButtonState);

            function updateAssignButtonState() {
                try {
                    if (!participants) return;
                    const anyNewChecked = participants.querySelectorAll(
                        '.participant-checkbox:checked:not([disabled])').length > 0;
                    if (assignBtnEl) assignBtnEl.disabled = !anyNewChecked;
                } catch (err) {
                    console.error('[assign] updateAssignButtonState error', err);
                }
            }

            function getCheckedNewValues() {
                try {
                    if (!participants) return [];
                    return Array.from(participants.querySelectorAll(
                        '.participant-checkbox:checked:not([disabled])')).map(cb => toS(cb.value || cb.dataset
                        .userId || cb.closest('label')?.dataset?.userId)).filter(Boolean);
                } catch (err) {
                    console.error('[assign] getCheckedNewValues error', err);
                    return [];
                }
            }

            // ---------- Assign submit ----------
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
                    const url = `${baseTasks}/${encodeURIComponent(currentTaskId)}/assign`;
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                        'content') || '';
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
                            body: JSON.stringify({
                                user_ids: newIds
                            })
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
                            const message = (json && json.message) ? json.message :
                                `Assign failed (${resp.status})`;
                            alert(message);
                            return;
                        }
                        const assignedFromServer = Array.isArray(json?.assigned) ? json.assigned.map(
                            toS) : null;
                        const assignedIds = assignedFromServer || (function() {
                            const row = getRowByTaskId(currentTaskId);
                            const prev = (row?.getAttribute('data-assigned') || '').split(',')
                                .map(toS).filter(Boolean);
                            return Array.from(new Set(prev.concat(newIds)));
                        })();
                        try {
                            updateAssignedBadges(currentTaskId, assignedIds);
                            syncModalAfterAssign(assignedIds, currentTaskId);
                        } catch (err) {
                            console.error('[assign] UI update error', err);
                        }
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

            // ---------- Unassign handler ----------
            document.addEventListener('click', async function(e) {
                const btn = e.target.closest && e.target.closest('.unassign-btn');
                if (!btn) return;
                e.preventDefault();
                if (!confirm('Remove this participant from the task?')) return;
                try {
                    const taskId = btn.dataset.taskId;
                    const userId = btn.dataset.userId;
                    if (!taskId || !userId) {
                        alert('Missing task/user id');
                        return;
                    }
                    const url =
                        `${baseTasks}/${encodeURIComponent(taskId)}/unassign/${encodeURIComponent(userId)}`;
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                        'content') || '';
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
                    } catch (err) {
                        json = null;
                    }
                    if (!resp.ok) {
                        alert((json && json.message) ? json.message :
                            `Unassign failed (${resp.status})`);
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

            // ---------- helpers to sync UI (updated to affect all duplicate rows) ----------
            function updateAssignedBadges(taskId, assignedIds) {
                try {
                    const rows = getAllRowsByTaskId(taskId);
                    if (!rows.length) {
                        console.warn('[assign] updateAssignedBadges: no rows for', taskId);
                        return;
                    }
                    const nameById = {};
                    if (participants) Array.from(participants.querySelectorAll('.list-group-item')).forEach(
                        labelEl => {
                            const cb = labelEl.querySelector('.participant-checkbox');
                            if (!cb) return;
                            const id = toS(cb.value || labelEl.dataset.userId || '');
                            const name = (labelEl.querySelector('.fw-semibold')?.textContent || labelEl
                                .textContent || '').trim();
                            if (id) nameById[id] = name;
                        });
                    rows.forEach(row => {
                        let cell = row.querySelector('.assigned-users') || row.querySelector(
                            '[data-assigned-cell]');
                        if (!cell) {
                            const tds = row.querySelectorAll('td');
                            if (tds && tds[2]) {
                                const newTd = document.createElement('td');
                                newTd.className = 'assigned-users';
                                row.insertBefore(newTd, tds[2]);
                                cell = newTd;
                            } else {
                                const newTd = document.createElement('td');
                                newTd.className = 'assigned-users';
                                row.appendChild(newTd);
                                cell = newTd;
                            }
                        }
                        cell.innerHTML = '';
                        if (!assignedIds || !assignedIds.length) {
                            cell.innerHTML = '<span class="text-muted">—</span>';
                        } else {
                            assignedIds.forEach(uid => {
                                const span = document.createElement('span');
                                span.className = 'badge bg-primary me-1 assigned-badge';
                                span.setAttribute('data-user-id', String(uid));
                                const labelHtml = escapeHtml(nameById[String(uid)] || ('User ' +
                                    uid));
                                span.innerHTML = `${labelHtml}` +
                                    `<button type="button" class="btn-close btn-close-white btn-sm ms-1 unassign-btn" aria-label="Remove" data-task-id="${escapeHtml(String(taskId))}" data-user-id="${escapeHtml(String(uid))}"></button>`;
                                cell.appendChild(span);
                            });
                        }
                        try {
                            row.setAttribute('data-assigned', (assignedIds || []).map(String).join(','));
                        } catch (err) {
                            console.warn('[assign] updateAssignedBadges: failed to set data-assigned', err);
                        }
                    });
                } catch (err) {
                    console.error('[assign] updateAssignedBadges: unexpected error', err);
                }
            }

            function decrementAssignedCount(taskId, userId) {
                const rows = getAllRowsByTaskId(taskId);
                if (!rows.length) {
                    console.warn('[assign] decrementAssignedCount: rows not found for', taskId);
                    return;
                }
                rows.forEach(row => {
                    const current = (row.getAttribute('data-assigned') || '').split(',').map(toS).filter(
                        Boolean);
                    const remaining = current.filter(id => id !== toS(userId));
                    try {
                        row.setAttribute('data-assigned', remaining.join(','));
                    } catch (err) {
                        console.warn('[assign] decrementAssignedCount: failed to update attribute', err);
                    }
                    let cell = row.querySelector('.assigned-users') || row.querySelector(
                        'td:nth-child(3)') || (row.cells && row.cells[2]);
                    if (cell && !remaining.length) cell.innerHTML = '<span class="text-muted">—</span>';
                    else if (cell) {
                        const badges = Array.from(cell.querySelectorAll('.assigned-badge'));
                        badges.forEach(b => {
                            if (toS(b.getAttribute('data-user-id')) === toS(userId)) b.remove();
                        });
                    }
                });
            }

            function syncModalAfterAssign(assignedIds, taskId) {
                const taskIdS = toS(taskId || currentTaskId);
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
                try {
                    const setA = new Set((assignedIds || []).map(String));
                    if (!participants) return;
                    Array.from(participants.querySelectorAll('.list-group-item')).forEach(lbl => {
                        const cb = lbl.querySelector('.participant-checkbox');
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
                    const cb = lbl.querySelector('.participant-checkbox');
                    const val = toS(cb?.value || lbl.dataset.userId || '');
                    if (val === uid) {
                        lbl.classList.remove('hidden-assigned');
                        lbl.style.display = '';
                        const csv = toS(cb.dataset.assignedTasks || lbl.dataset.assignedTasks || '');
                        const arr = csv ? csv.split(',').map(s => s.trim()).filter(Boolean) : [];
                        const newArr = arr.filter(id => id !== toS(currentTaskId));
                        const newCsv = newArr.join(',');
                        if (cb) cb.dataset.assignedTasks = newCsv;
                        if (lbl) lbl.dataset.assignedTasks = newCsv;
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

            console.log('[assign] script loaded (robust, duplicates-aware)');
        });
    </script>
@endpush
