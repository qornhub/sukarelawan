<div id="section-tasks"
     class="mt-4 card participant-section {{ $disabled ? 'is-disabled' : '' }}"
     style="display:none"
     data-disabled="{{ $disabled ? '1' : '0' }}"
     aria-disabled="{{ $disabled ? 'true' : 'false' }}">

    <div class="card-body p-3">
        <div class="d-flex align-items-center justify-content-between mb-1">
            <h3 class="task-title mb-0">Tasks</h3>

            <!-- Add Task button -->
            <div class="d-flex justify-content-between align-items-center mb-1">
                @if($disabled)
                    <button type="button"
                            class="btn btn-primary btn-sm add-task-btn side-btn is-disabled"
                            aria-disabled="true"
                            tabindex="-1"
                            title="Task creation disabled — event has ended">
                        <i class="fa fa-plus me-1"></i> Add Task
                    </button>
                @else
                    <a href="#" class="btn btn-primary btn-sm add-task-btn side-btn" role="button">
                        <i class="fa fa-plus me-1"></i> Add Task
                    </a>
                @endif
            </div>
        </div>

        <div id="task-list-flash" class="task-flash-area" aria-live="polite" aria-atomic="true"></div>
        @include('layouts/messages')

        <div class="table-responsive">
            <table class="table table-sm task-table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:30%;">Task Title</th>
                        <th style="width:45%;">Description</th>
                        <th style="width:15%;">Event</th>
                        <th style="width:10%;" class="text-center">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($tasks as $task)
                        <tr data-task-id="{{ $task->task_id }}">
                            <td class="task-title-cell">{{ $task->title }}</td>

                            <td class="task-desc">
                                {{ \Illuminate\Support\Str::limit($task->description, 160, '...') }}
                            </td>

                            <td class="text-nowrap">
                                <span class="badge-event">{{ optional($task->event)->eventTitle ?? 'N/A' }}</span>
                            </td>

                            <td class="text-center">
                                <div class="task-actions">
                                    {{-- Edit button (disabled when $disabled) --}}
                                    @if($disabled)
                                        <button type="button"
                                                class="btn btn-outline-secondary btn-sm btn-edit-task is-disabled"
                                                data-task-id="{{ $task->task_id }}"
                                                data-disabled="1"
                                                aria-disabled="true"
                                                tabindex="-1"
                                                title="Editing disabled — event has ended">
                                            Edit
                                        </button>
                                    @else
                                        <button type="button"
                                                class="btn btn-outline-secondary btn-sm btn-edit-task"
                                                data-task-id="{{ $task->task_id }}"
                                                data-title="{{ e($task->title) }}"
                                                data-description="{{ e($task->description) }}"
                                                data-event-id="{{ $task->event->event_id ?? ($task->event_id ?? $event->event_id) }}">
                                            Edit
                                        </button>
                                    @endif

                                    {{-- Delete button (disabled when $disabled) --}}
                                    @if($disabled)
                                        <button type="button"
                                                class="btn btn-outline-danger btn-sm btn-delete-task is-disabled"
                                                data-event-id="{{ $task->event->event_id ?? $task->event_id }}"
                                                data-task-id="{{ $task->task_id }}"
                                                data-disabled="1"
                                                aria-disabled="true"
                                                tabindex="-1"
                                                title="Deleting disabled — event has ended">
                                            Delete
                                        </button>
                                    @else
                                        <button type="button"
                                                class="btn btn-outline-danger btn-sm btn-delete-task"
                                                data-event-id="{{ $task->event->event_id ?? $task->event_id }}"
                                                data-task-id="{{ $task->task_id }}">
                                            Delete
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="empty-row">
                            <td colspan="4" class="text-center">No tasks found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function() {
        function ready(fn) {
            if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', fn);
            else fn();
        }

        ready(function() {
            console.log('[tasks] handlers attached');

            // route template (blade will render the string with placeholders)
            const deleteUrlTemplate =
                "{{ route('ngo.tasks.destroy', ['event' => 'EVENT_ID', 'task' => 'TASK_ID']) }}";

            // helper: find container and disabled state for a button
            function isControlDisabled(el) {
                if (!el) return false;
                const section = el.closest('#section-tasks');
                if (section && section.dataset && section.dataset.disabled === '1') return true;
                if (el.dataset && el.dataset.disabled === '1') return true;
                return false;
            }

            // guard edit clicks (prevents edit modals/links when disabled)
            document.addEventListener('click', function(e) {
                const editBtn = e.target.closest('.btn-edit-task');
                if (!editBtn) return;

                if (isControlDisabled(editBtn)) {
                    e.preventDefault();
                    e.stopPropagation();
                    flash('Task editing is disabled — this event has ended.', 'error');
                    return;
                }

                // otherwise let other handlers proceed (you may have modal opening logic elsewhere)
            });

            // delete handler (ajax-backed deletion)
            document.addEventListener('click', async function(e) {
                const btn = e.target.closest('.btn-delete-task');
                if (!btn) return;

                // if disabled, show message and don't proceed
                if (isControlDisabled(btn)) {
                    e.preventDefault();
                    e.stopPropagation();
                    flash('Task deletion is disabled — this event has ended.', 'error');
                    return;
                }

                e.preventDefault();

                const taskId = btn.dataset.taskId;
                const eventId = btn.dataset.eventId;

                if (!taskId || !eventId) {
                    console.warn('[tasks] missing data-task-id or data-event-id on button', btn);
                    flash('Missing task or event id', 'error');
                    return;
                }

                if (!confirm('Are you sure you want to delete this task?')) return;

                // disable while request in progress
                btn.disabled = true;

                try {
                    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
                    const token = tokenMeta ? tokenMeta.getAttribute('content') : '';

                    // build url from template
                    const url = deleteUrlTemplate
                        .replace('EVENT_ID', encodeURIComponent(eventId))
                        .replace('TASK_ID', encodeURIComponent(taskId));

                    console.log('[tasks] delete url', url);

                    const resp = await fetch(url, {
                        method: 'POST', // use POST + _method=DELETE so it matches your routes
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        body: new URLSearchParams({
                            _method: 'DELETE'
                        })
                    });

                    console.log('[tasks] response status', resp.status, resp.statusText);

                    // try parse JSON, but handle non-JSON too
                    let body = null;
                    try {
                        body = await resp.json();
                        console.log('[tasks] response json', body);
                    } catch (jsonErr) {
                        const text = await resp.text();
                        console.warn('[tasks] response text (not json)', text.slice(0, 512));
                        body = { ok: resp.ok, text: text };
                    }

                    if (!resp.ok) {
                        console.error('[tasks] delete failed', resp.status, body);
                        flash(body?.message || 'Failed to delete task', 'error');
                        return;
                    }

                    // success: remove row from task list
                    const row = document.querySelector(`#section-tasks tr[data-task-id="${taskId}"]`);
                    if (row) row.remove();
                    else if (btn.closest('tr')) btn.closest('tr').remove();

                    // also remove from Manage Tasks if exists
                    const manageRow = document.querySelector(`#section-manage-tasks tr[data-task-id="${taskId}"]`);
                    if (manageRow) manageRow.remove();

                    // if table now empty, insert empty row
                    const tbody = document.querySelector('#section-tasks table.task-table tbody') ||
                                  document.querySelector('#section-tasks table tbody');
                    if (tbody && tbody.children.length === 0) {
                        const tr = document.createElement('tr');
                        tr.className = 'empty-row';
                        tr.innerHTML = '<td colspan="4" class="text-center">No tasks found.</td>';
                        tbody.appendChild(tr);
                    }

                    flash(body?.message || 'Task deleted', 'success');
                } catch (err) {
                    console.error('[tasks] delete error', err);
                    flash('An error occurred while deleting', 'error');
                } finally {
                    btn.disabled = false;
                }
            });

            // flash helper (same as you had)
            function flash(text, type = 'success', { duration = 2400 } = {}) {
                let container = document.querySelector('#task-list-flash');
                if (!container) container = document.querySelector('#section-tasks .card-body') || document.body;

                const el = document.createElement('div');
                el.className = 'task-flash enter ' + (type === 'success' ? 'success' : 'error');
                el.setAttribute('role', 'status');
                el.textContent = text;

                container.prepend(el);

                requestAnimationFrame(() => {
                    el.classList.remove('enter');
                    el.classList.add('enter-active');
                });

                setTimeout(() => {
                    el.classList.remove('enter-active');
                    el.classList.add('exit');
                    setTimeout(() => { el.remove(); }, 320);
                }, duration);
            }
        });
    })();
</script>
@endpush
