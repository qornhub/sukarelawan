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
                    {{ (old('event_id', $task->event_id ?? $event->event_id ?? '') == $ev->event_id) ? 'selected' : '' }}>
                    {{ $ev->eventTitle }} — {{ \Illuminate\Support\Str::limit($ev->venueName ?? $ev->eventLocation ?? '', 40) }}
                </option>
            @endforeach
        </select>
    @else
        {{-- Single event context — display the current event (prefer $event then $task->event) --}}
        <div class="event-info">
            <strong>
                {{ $event->eventTitle 
                    ?? optional($task->event)->eventTitle 
                    ?? 'Event (N/A)' }}
            </strong>

            <small class="text-muted">
                {{ $event->venueName 
                    ?? $event->eventLocation 
                    ?? optional($task->event)->venueName 
                    ?? optional($task->event)->eventLocation 
                    ?? '—' }}
            </small>

            {{-- Keep a hidden input so the event_id is submitted --}}
            <input type="hidden" name="event_id" value="{{ old('event_id', $task->event_id ?? $event->event_id ?? '') }}">
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
document.addEventListener('DOMContentLoaded', function () {
    const sectionTasks = document.getElementById('section-tasks');
    const sectionEdit = document.getElementById('section-edit');
    const editForm = document.getElementById('edit-task-form');
    const errBox = document.getElementById('edit-form-errors');

    // Read the action template (if provided)
    const actionTemplate = editForm ? editForm.dataset.actionTemplate : null;

    // Delegated handler for Edit buttons (works for dynamic rows)
    document.addEventListener('click', function (e) {
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
            editForm.action = `/ngo/${encodeURIComponent(eventId)}/tasks/${encodeURIComponent(taskId)}`;
        }

        // show edit, hide other participant sections
        document.querySelectorAll('.participant-section').forEach(s => s.style.display = 'none');
        sectionEdit.style.display = 'block';
        sectionEdit.setAttribute('aria-hidden', 'false');
        sectionEdit.scrollIntoView({ behavior: 'smooth' });
    });

    // Delegated cancel handler
    document.addEventListener('click', function (e) {
        const cancel = e.target.closest('.btn-cancel-edit');
        if (!cancel) return;
        e.preventDefault();

        sectionEdit.style.display = 'none';
        sectionEdit.setAttribute('aria-hidden', 'true');
        if (sectionTasks) sectionTasks.style.display = 'block';
        if (errBox) { errBox.style.display = 'none'; errBox.innerHTML = ''; }
        if (editForm) editForm.reset();
    });

    // AJAX submit + update row
    if (editForm) {
        editForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const submitBtn = document.getElementById('edit-task-submit');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = 'Updating...';
            }

            if (errBox) { errBox.style.display = 'none'; errBox.innerHTML = ''; }

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
                    if (errBox) { errBox.innerHTML = html; errBox.style.display = 'block'; }
                    return;
                }

                if (!resp.ok) throw new Error('Server error: ' + resp.status);

                const data = await resp.json();

                if (data && data.task) updateTaskRow(data.task);

                // back to list
                editForm.reset();
                sectionEdit.style.display = 'none';
                if (sectionTasks) sectionTasks.style.display = 'block';

                flashMessage('Task updated', 'success');
            } catch (err) {
                console.error(err);
                if (errBox) { errBox.innerHTML = 'Unexpected error. Please try again.'; errBox.style.display = 'block'; }
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save"></i> Update Task';
                }
            }
        });
    }

    // update task row in table
    function updateTaskRow(task) {
        const row = document.querySelector(`#section-tasks tr[data-task-id="${task.task_id}"]`);
        if (!row) return;

        const titleCell = row.querySelector('.task-title-cell');
        const descCell = row.querySelector('.task-desc');
        const badge = row.querySelector('.badge-event');

        if (titleCell) titleCell.textContent = task.title;
        if (descCell) {
            descCell.textContent = task.description.length > 200 ? task.description.slice(0,200) + '...' : task.description;
        }
        if (badge) badge.textContent = task.event?.eventTitle || '';
    }

    // Replace your old flashMessage(...) with this function in task_edit.blade
function flashMessage(text, type = 'success', opts = { duration: 2200 }) {
  // Try to render inside the Task list flash container first
  let container = document.querySelector('#task-list-flash');

  // Fallback to body-floating alert if inline container isn't present
  const useFloating = !container;
  if (useFloating) container = document.body;

  // Build message element
  const el = document.createElement('div');
  el.className = 'task-flash ' + (type === 'success' ? 'success' : (type === 'error' ? 'error' : 'info'));
  el.setAttribute('role', 'status');
  el.textContent = text;

  // If floating fallback, apply fixed-position styles (keeps previous behaviour)
  if (useFloating) {
    el.style.position = 'fixed';
    el.style.right = '20px';
    el.style.top = '20px';
    el.style.zIndex = 9999;
    // give it a neutral "info" look if not success/error
    if (!el.classList.contains('success') && !el.classList.contains('error')) {
      el.classList.add('info');
    }
  }

  // Insert message into container
  if (useFloating) container.appendChild(el);
  else container.prepend(el);

  // Auto-hide after duration with a subtle hide animation
  const duration = opts.duration ?? 2200;
  // allow browser to paint
  requestAnimationFrame(() => {
    // optional: we could toggle classes to animate in
  });

  setTimeout(() => {
    // apply simple hide (you can animate with CSS class if you added transitions)
    el.style.transition = 'opacity 260ms ease, transform 260ms ease';
    el.style.opacity = '0';
    el.style.transform = 'translateY(-6px) scale(.995)';
    setTimeout(() => el.remove(), 300);
  }, duration);
}

});
</script>
@endpush

