

<div id="section-create" class="card section-create-card participant-section" style="display:none" >
    <div class="card-header" role="heading" aria-level="2">
        <i class="fas fa-tasks" aria-hidden="true"></i>
        Add New Task
    </div>

    <div class="card-body">
        <div id="create-form-errors" class="form-errors" role="alert" aria-live="assertive" style="display:none"></div>

        <form id="create-task-form" action="{{ route('ngo.tasks.store', $event->event_id) }}" method="POST" autocomplete="off" novalidate>
            @csrf

            {{-- Event --}}
            <div class="mb-3">
                <label class="form-label">Event</label>
                <div class="event-info" aria-hidden="true">
                    <strong>{{ $event->eventTitle }}</strong>
                    <small class="text-muted">{{ $event->venueName ?? ($event->eventLocation ?? '—') }}</small>
                </div>
            </div>

            {{-- Task Title --}}
            <div class="mb-3">
                <label for="title" class="form-label">Task Title</label>
                <input id="title" name="title" type="text" class="form-control"
                       placeholder="e.g. Set up registration desk"
                       required value="{{ old('title') }}" aria-required="true" />
            </div>

            {{-- Description --}}
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-control"
                          rows="5" placeholder="Describe the task in detail..." required aria-required="true">{{ old('description') }}</textarea>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-outline-secondary btn-cancel-create" aria-label="Cancel">
                    <i class="fas fa-times" aria-hidden="true"></i> Cancel
                </button>

                <button type="submit" class="btn btn-success" id="create-task-submit" aria-label="Create Task">
                    <i class="fas fa-plus-circle" aria-hidden="true"></i> Create Task
                </button>
            </div>
        </form>
    </div>
</div>


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    /* =====================================================
     * HTML ESCAPE HELPER (🔥 REQUIRED)
     * ===================================================== */
    function escapeHtml(str) {
        return String(str ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    // selectors
    const sectionTasks = document.getElementById('section-tasks');
    const sectionCreate = document.getElementById('section-create');
    const addButtons = document.querySelectorAll('.add-task-btn, .side-btn[data-show="create"]');
    const cancelButtons = document.querySelectorAll('.btn-cancel-create');

    // toggle to show create form
    addButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('.participant-section').forEach(s => s.style.display = 'none');
            if (sectionCreate) sectionCreate.style.display = 'block';
            const t = document.getElementById('title');
            if (t) t.focus();
        });
    });

    // cancel -> back to tasks
    cancelButtons.forEach(btn => btn.addEventListener('click', function() {
        if (sectionCreate) sectionCreate.style.display = 'none';
        if (sectionTasks) sectionTasks.style.display = 'block';

        const errBox = document.getElementById('create-form-errors');
        if (errBox) {
            errBox.style.display = 'none';
            errBox.innerHTML = '';
        }

        const frm = document.getElementById('create-task-form');
        if (frm) frm.reset();
    }));

    // AJAX submit
    const form = document.getElementById('create-task-form');
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const submitBtn = document.getElementById('create-task-submit');
            const errBox = document.getElementById('create-form-errors');
            errBox.style.display = 'none';
            errBox.innerHTML = '';

            submitBtn.disabled = true;
            submitBtn.textContent = 'Creating...';

            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const resp = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': token
                    },
                    body: new FormData(form),
                });

                if (resp.status === 422) {
                    const json = await resp.json();
                    let html = '<ul style="margin:0;padding-left:18px">';
                    for (const key in json.errors) {
                        json.errors[key].forEach(msg => html += `<li>${msg}</li>`);
                    }
                    html += '</ul>';
                    errBox.innerHTML = html;
                    errBox.style.display = 'block';
                    return;
                }

                if (!resp.ok) throw new Error('Server error');

                const data = await resp.json();
                const task = data.task || data;

                insertTaskCard(task);
                //insertManageTaskRow(task);
                syncManageTaskCreate(task);


                form.reset();
                sectionCreate.style.display = 'none';
                sectionTasks.style.display = 'block';

                flashMessage('Task created', 'success');

            } catch (err) {
                console.error(err);
                errBox.innerHTML = 'Unexpected error. Please try again.';
                errBox.style.display = 'block';
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Create Task';
            }
        });
    }

    /* =====================================================
     * FLASH MESSAGE
     * ===================================================== */
    function flashMessage(text, type = 'success', opts = { duration: 2200 }) {
        let container = document.querySelector('#task-list-flash') || document.body;
        const el = document.createElement('div');
        el.className = 'task-flash ' + type;
        el.textContent = text;
        container.prepend(el);
        setTimeout(() => el.remove(), opts.duration);
    }

    /* =====================================================
     * TABLE HELPERS
     * ===================================================== */
    function removePlaceholderRowsFrom(tbody) {
        if (!tbody) return;
        tbody.querySelectorAll('.empty-row').forEach(r => r.remove());
    }

    function insertTaskCard(task) {
    const list = document.getElementById('task-card-list');
    if (!list) return;

    // remove empty placeholder if exists
    const empty = list.querySelector('.task-empty');
    if (empty) empty.remove();

    const taskId = task.task_id || task.id;

    const eventId =
        (task.event && task.event.event_id) ||
        task.event_id ||
        '';

    const eventTitle =
        (task.event && task.event.eventTitle) ||
        task.eventTitle ||
        'N/A';

    const card = document.createElement('div');
    card.className = 'task-card';
    card.setAttribute('data-task-id', taskId);

    card.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-1">
            <h5 class="mb-0">${escapeHtml(task.title)}</h5>
            <span class="badge-event">${escapeHtml(eventTitle)}</span>
        </div>

        <p class="mb-2">
            ${escapeHtml(task.description)}
        </p>

        <div class="d-flex gap-2">
            <button type="button"
                class="btn btn-outline-secondary btn-sm btn-edit-task"
                data-task-id="${escapeHtml(taskId)}"
                data-title="${escapeHtml(task.title)}"
                data-description="${escapeHtml(task.description)}"
                data-event-id="${escapeHtml(eventId)}">
                Edit
            </button>

            <button type="button"
                class="btn btn-outline-danger btn-sm btn-delete-task"
                data-task-id="${escapeHtml(taskId)}"
                data-event-id="${escapeHtml(eventId)}">
                Delete
            </button>
        </div>
    `;

    // prepend so newest task appears on top (optional)
    list.prepend(card);
}


    function insertManageTaskRow(task) {
        const tbody = document.querySelector('#section-manage-tasks table tbody');
        if (!tbody) return;

        removePlaceholderRowsFrom(tbody);

        const taskId = task.task_id || task.id;

        const row = document.createElement('tr');
        row.dataset.taskId = taskId;

        row.innerHTML = `
            <td class="task-title">${escapeHtml(task.title)}</td>
            <td>${escapeHtml(task.description)}</td>
            <td class="assigned-users"><span class="text-muted">—</span></td>
            <td>
                <button class="btn btn-sm btn-outline-success assign-btn"
                    data-task-id="${escapeHtml(taskId)}">
                    Assign To
                </button>
            </td>
        `;

        tbody.appendChild(row);
    }

});
</script>


@endpush