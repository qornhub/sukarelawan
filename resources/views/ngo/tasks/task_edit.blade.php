<div id="section-edit" class="card section-edit-card participant-section" style="display:none" aria-hidden="true">
    <div class="card-header" role="heading" aria-level="2">
        <i class="fas fa-edit" aria-hidden="true"></i>
        Edit Task
    </div>

    <div class="card-body">
        <div id="edit-form-errors" class="form-errors" role="alert" aria-live="assertive" style="display:none"></div>

        @php
            // Build a template action from the route that uses {event} and {task} placeholders.
            $actionTemplate = str_replace(
                [$event->event_id, $task->task_id ?? 0],
                ['{event}', '{task}'],
                route('ngo.tasks.update', [$event->event_id, $task->task_id ?? 0]),
            );
        @endphp
        <form id="edit-task-form" action="{{ route('ngo.tasks.update', [$event->event_id, $task->task_id ?? 0]) }}"
            data-action-template="{{ $actionTemplate }}" method="POST" autocomplete="off" novalidate>

            @csrf
            @method('PUT')

            {{-- Event --}}
            <div class="mb-3">
                <label class="form-label">Event</label>

                @if (isset($events) && $events->count())
                    {{-- If multiple events available let user choose --}}
                    <select name="event_id" id="event_id" class="form-select" required>
                        @foreach ($events as $ev)
                            <option value="{{ $ev->event_id }}"
                                {{ old('event_id', $task->event_id ?? ($event->event_id ?? '')) == $ev->event_id ? 'selected' : '' }}>
                                {{ $ev->eventTitle }} —
                                {{ \Illuminate\Support\Str::limit($ev->venueName ?? ($ev->eventLocation ?? ''), 40) }}
                            </option>
                        @endforeach
                    </select>
                @else
                    {{-- Single event context — display the current event (prefer $event then $task->event) --}}
                    <div class="event-info">
                        <strong>
                            {{ $event->eventTitle ?? (optional($task->event)->eventTitle ?? 'Event (N/A)') }}
                        </strong>

                        <small class="text-muted">
                            {{ $event->venueName ??
                                ($event->eventLocation ?? (optional($task->event)->venueName ?? (optional($task->event)->eventLocation ?? '—'))) }}
                        </small>

                        {{-- Keep a hidden input so the event_id is submitted --}}
                        <input type="hidden" name="event_id"
                            value="{{ old('event_id', $task->event_id ?? ($event->event_id ?? '')) }}">
                    </div>
                @endif
            </div>


            {{-- Task Title --}}
            <div class="mb-3">
                <label for="edit-title" class="form-label">Task Title</label>
                <input id="edit-title" name="title" type="text" class="form-control" required
                    value="{{ old('title', $task->title ?? '') }}">
            </div>

            {{-- Description --}}
            <div class="mb-3">
                <label for="edit-description" class="form-label">Description</label>
                <textarea id="edit-description" name="description" class="form-control" rows="6" required>{{ old('description', $task->description ?? '') }}</textarea>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-outline-secondary btn-cancel-edit" aria-label="Cancel">
                    <i class="fas fa-times" aria-hidden="true"></i> Cancel
                </button>

                <button type="submit" class="btn btn-success" id="edit-task-submit" aria-label="Update Task">
                    <i class="fas fa-save" aria-hidden="true"></i> Update Task
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sectionTasks = document.getElementById('section-tasks');
            const sectionEdit = document.getElementById('section-edit');
            const editForm = document.getElementById('edit-task-form');
            const errBox = document.getElementById('edit-form-errors');

            // Read the action template (if provided)
            const actionTemplate = editForm ? editForm.dataset.actionTemplate : null;

            // ----------------------------------------------------
            // Delegated handler for Edit buttons (works for dynamic rows)
            // ----------------------------------------------------
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.btn-edit-task');
                if (!btn) return;
                e.preventDefault();

                const taskId = btn.dataset.taskId || '';
                const title = btn.dataset.title || '';
                const description = btn.dataset.description || '';
                const eventId = btn.dataset.eventId || '';

                // fill inputs
                const titleInput = document.getElementById('edit-title');
                const descInput = document.getElementById('edit-description');
                const eventInput = document.getElementById('event_id');

                if (titleInput) titleInput.value = title;
                if (descInput) descInput.value = description;
                if (eventInput && eventId) eventInput.value = eventId;

                // Compute new form action from template if present, else fallback to simple pattern
                if (actionTemplate) {
                    editForm.action = actionTemplate
                        .replace('{event}', encodeURIComponent(eventId))
                        .replace('{task}', encodeURIComponent(taskId));
                } else {
                    // fallback: guess route pattern; adjust if your route is different
                    editForm.action =
                        `/ngo/${encodeURIComponent(eventId)}/tasks/${encodeURIComponent(taskId)}`;
                }

                // show edit, hide other participant sections
                document.querySelectorAll('.participant-section').forEach(s => s.style.display = 'none');
                sectionEdit.style.display = 'block';
                sectionEdit.setAttribute('aria-hidden', 'false');
                sectionEdit.scrollIntoView({
                    behavior: 'smooth'
                });
            });

            // ----------------------------------------------------
            // Delegated cancel handler
            // ----------------------------------------------------
            document.addEventListener('click', function(e) {
                const cancel = e.target.closest('.btn-cancel-edit');
                if (!cancel) return;
                e.preventDefault();

                sectionEdit.style.display = 'none';
                sectionEdit.setAttribute('aria-hidden', 'true');
                if (sectionTasks) sectionTasks.style.display = 'block';
                if (errBox) {
                    errBox.style.display = 'none';
                    errBox.innerHTML = '';
                }
                if (editForm) editForm.reset();
            });

            // ----------------------------------------------------
            // AJAX submit + update rows
            // ----------------------------------------------------
            if (editForm) {
                editForm.addEventListener('submit', async function(e) {
                    e.preventDefault();

                    const submitBtn = document.getElementById('edit-task-submit');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = 'Updating...';
                    }

                    if (errBox) {
                        errBox.style.display = 'none';
                        errBox.innerHTML = '';
                    }

                    try {
                        const tokenMeta = document.querySelector('meta[name="csrf-token"]');
                        const token = tokenMeta ? tokenMeta.getAttribute('content') : '';
                        const formData = new FormData(editForm);

                        const resp = await fetch(editForm.action, {
                            method: 'POST', // Laravel expects POST + _method=PUT
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': token
                            },
                            body: formData
                        });

                        if (resp.status === 422) {
                            const json = await resp.json();
                            const errors = json.errors || json;
                            let html = '<ul style="margin:0;padding-left:18px">';
                            for (const key in errors) {
                                (errors[key] || []).forEach(msg => html += `<li>${msg}</li>`);
                            }
                            html += '</ul>';
                            if (errBox) {
                                errBox.innerHTML = html;
                                errBox.style.display = 'block';
                            }
                            return;
                        }

                        if (!resp.ok) throw new Error('Server error: ' + resp.status);

                        const data = await resp.json();

                        if (data && data.task) {
                            updateTaskCard(data.task); // ✅ CARD UI
                            updateManageTaskRow(data.task); // ✅ MANAGE TABLE
                        }

                        // back to list
                        editForm.reset();
                        sectionEdit.style.display = 'none';
                        if (sectionTasks) sectionTasks.style.display = 'block';

                        flashMessage('Task updated', 'success');
                    } catch (err) {
                        console.error(err);
                        if (errBox) {
                            errBox.innerHTML = 'Unexpected error. Please try again.';
                            errBox.style.display = 'block';
                        }
                    } finally {
                        const submitBtn = document.getElementById('edit-task-submit');
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML =
                                '<i class="fas fa-save" aria-hidden="true"></i> Update Task';
                        }
                    }
                });
            }

            // ----------------------------------------------------
            // Update row in Task List table
            // ----------------------------------------------------
            function updateTaskCard(task) {
                const card = document.querySelector(
                    `#task-card-list .task-card[data-task-id="${task.task_id}"]`
                );
                if (!card) return;

                const titleEl = card.querySelector('h5');
                const descEl = card.querySelector('p');
                const badgeEl = card.querySelector('.badge-event');

                if (titleEl) {
                    titleEl.textContent = task.title || '';
                }

                if (descEl) {
                    const full = task.description || '';
                    descEl.textContent =
                        full.length > 160 ? full.slice(0, 160) + '...' : full;
                }

                if (badgeEl && task.event && task.event.eventTitle) {
                    badgeEl.textContent = task.event.eventTitle;
                }

                /* 🔥 VERY IMPORTANT
                   Update edit button dataset so next edit opens correct data */
                const editBtn = card.querySelector('.btn-edit-task');
                if (editBtn) {
                    editBtn.dataset.title = task.title || '';
                    editBtn.dataset.description = task.description || '';
                    editBtn.dataset.eventId =
                        (task.event && task.event.event_id) || editBtn.dataset.eventId;
                }
            }

            // ----------------------------------------------------
            // NEW: Update row in Manage Tasks table
            // ----------------------------------------------------
            function updateManageTaskRow(task) {
    const card = document.querySelector(
        `#section-manage-tasks .manage-task-card[data-task-id="${task.task_id}"]`
    );
    if (!card) return;

    const titleEl = card.querySelector('.task-title');
    const descEl = card.querySelector('p.text-muted');

    if (titleEl) titleEl.textContent = task.title || '';

    if (descEl) {
        const full = task.description || '';
        descEl.textContent = full.length > 100
            ? full.slice(0, 100) + '...'
            : full;
    }
}


            // ----------------------------------------------------
            // Flash helper
            // ----------------------------------------------------
            function flashMessage(text, type = 'success', opts = {
                duration: 2200
            }) {
                let container = document.querySelector('#task-list-flash');

                const useFloating = !container;
                if (useFloating) container = document.body;

                const el = document.createElement('div');
                el.className = 'task-flash ' + (type === 'success' ? 'success' : (type === 'error' ? 'error' :
                    'info'));
                el.setAttribute('role', 'status');
                el.textContent = text;

                if (useFloating) {
                    el.style.position = 'fixed';
                    el.style.right = '20px';
                    el.style.top = '20px';
                    el.style.zIndex = 9999;
                }

                if (useFloating) container.appendChild(el);
                else container.prepend(el);

                const duration = opts.duration ?? 2200;

                requestAnimationFrame(() => {
                    /* could add enter animation here */
                });

                setTimeout(() => {
                    el.style.transition = 'opacity 260ms ease, transform 260ms ease';
                    el.style.opacity = '0';
                    el.style.transform = 'translateY(-6px) scale(.995)';
                    setTimeout(() => el.remove(), 300);
                }, duration);
            }
        });
    </script>
@endpush
