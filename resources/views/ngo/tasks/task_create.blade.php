

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

            const url = form.action;
            const formData = new FormData(form);

            submitBtn.disabled = true;
            submitBtn.textContent = 'Creating...';

            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const resp = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': token
                    },
                    body: formData,
                });

                if (resp.status === 422) {
                    const json = await resp.json();
                    const errors = json.errors || json;
                    let html = '<ul style="margin:0;padding-left:18px">';
                    for (const key in errors) {
                        (errors[key] || []).forEach(msg => html += `<li>${msg}</li>`);
                    }
                    html += '</ul>';
                    errBox.innerHTML = html;
                    errBox.style.display = 'block';
                    sectionCreate.style.display = 'block';
                    sectionTasks.style.display = 'none';
                    return;
                }

                if (!resp.ok) throw new Error('Server error: ' + resp.status);

                const data = await resp.json(); // expected new task object

                if (data && data.task) {
                    const task = data.task;
                    insertTaskRow(task);         // existing list insert
                    insertManageTaskRow(task);   // 🔥 NEW CODE
                } else if (data && data.task_id) {
                    insertTaskRow(data);
                    insertManageTaskRow(data);   // 🔥 NEW CODE
                }

                form.reset();
                sectionCreate.style.display = 'none';
                if (sectionTasks) sectionTasks.style.display = 'block';

                flashMessage('Task created', 'success');

            } catch (err) {
                console.error(err);
                errBox.innerHTML = 'Unexpected error. Please try again.';
                errBox.style.display = 'block';
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Create Task';
            }
        });
    }

    // flash message util
    function flashMessage(text, type = 'success', opts = { duration: 2200 }) {
        let container = document.querySelector('#task-list-flash');
        const useFloating = !container;
        if (useFloating) container = document.body;
        const el = document.createElement('div');
        el.className = 'task-flash ' + (type === 'success' ? 'success' : (type === 'error' ? 'error' : 'info'));
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
        setTimeout(() => {
            el.classList.add('hidden');
            setTimeout(() => el.remove(), 360);
        }, duration);
    }

    // helper to remove placeholder rows from a tbody
function removePlaceholderRowsFrom(tbody) {
    if (!tbody) return;
    const rows = Array.from(tbody.querySelectorAll('tr'));
    rows.forEach(tr => {
        // remove explicit .empty-row class
        if (tr.classList.contains('empty-row')) {
            tr.remove();
            return;
        }
        // or remove rows that show "No tasks found." placeholder text
        const txt = (tr.textContent || '').trim();
        if (/No tasks found\./i.test(txt)) {
            tr.remove();
            return;
        }
    });
}

// helper to insert new row into main task list (prepending)
function insertTaskRow(task) {
    const tbody = document.querySelector('#section-tasks table.task-table tbody')
               || document.querySelector('#section-tasks table tbody');
    if (!tbody) return;

    // remove "No tasks found" placeholders before inserting
    removePlaceholderRowsFrom(tbody);

    const row = document.createElement('tr');
    row.setAttribute('data-task-id', task.task_id);

    const titleTd = document.createElement('td');
    titleTd.className = 'task-title-cell';
    titleTd.textContent = task.title || '';

    const descTd = document.createElement('td');
    descTd.className = 'task-desc';
    descTd.textContent = task.description ? (task.description.length > 200 ? task.description.slice(0,200) + '...' : task.description) : '';

    const eventTd = document.createElement('td');
    eventTd.className = 'text-nowrap';
    eventTd.innerHTML = '<span class="badge-event">' + (task.event?.eventTitle || '') + '</span>';

    const actionsTd = document.createElement('td');
    actionsTd.className = 'text-center';
    actionsTd.innerHTML = `
        <div class="task-actions">
          <a href="${buildEditUrl(task)}" class="btn btn-outline-secondary btn-sm">Edit</a>
          <form action="${buildDeleteUrl(task)}" method="POST" class="d-inline" onsubmit="return confirm('Delete this task?');">
            <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]').content}">
            <input type="hidden" name="_method" value="DELETE">
            <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
          </form>
        </div>
    `;

    row.appendChild(titleTd);
    row.appendChild(descTd);
    row.appendChild(eventTd);
    row.appendChild(actionsTd);

    // prepend new row
    if (tbody.firstChild) tbody.insertBefore(row, tbody.firstChild);
    else tbody.appendChild(row);
}

// helper to insert new row into Manage Tasks table (appending)
function insertManageTaskRow(task) {
    const tbody = document.querySelector('#section-manage-tasks table tbody');
    if (!tbody) return;

    // remove "No tasks found" placeholders before inserting
    removePlaceholderRowsFrom(tbody);

    const row = document.createElement('tr');
    row.setAttribute('data-task-id', task.task_id);
    row.setAttribute('data-assigned', '');

    row.innerHTML = `
        <td class="task-title">${task.title || ''}</td>
        <td>${task.description || ''}</td>
        <td class="assigned-users"><span class="text-muted">—</span></td>
        <td>
            <button type="button" class="btn btn-sm btn-outline-success assign-btn"
                data-task-id="${task.task_id}">
                Assign To
            </button>
        </td>
    `;

    // append to manage table
    tbody.appendChild(row);
}


    // helpers to build URLs
    function buildEditUrl(task) {
        const table = document.querySelector('#section-tasks table.task-table');
        const template = table?.getAttribute('data-edit-url');
        if (template) return template.replace('{event}', encodeURIComponent(task.event?.event_id || '{{ $event->event_id }}'))
                                     .replace('{task}', encodeURIComponent(task.task_id || task.id));
        return '#';
    }

    function buildDeleteUrl(task) {
        const table = document.querySelector('#section-tasks table.task-table');
        const template = table?.getAttribute('data-destroy-url');
        if (template) return template.replace('{event}', encodeURIComponent(task.event?.event_id || '{{ $event->event_id }}'))
                                     .replace('{task}', encodeURIComponent(task.task_id || task.id));
        return '#';
    }
});
</script>

@endpush