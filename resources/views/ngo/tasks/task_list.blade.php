<div id="section-tasks" class="mt-5 card participant-section" style="display:none">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h3 class="task-title mb-0">Tasks</h3>

            <!-- Add Task button -->


            <div class="d-flex justify-content-between align-items-center mb-5">

                <a href="#" class="btn btn-primary btn-sm add-task-btn side-btn" role="button">
                    <i class="fa fa-plus me-1"></i> Add Task
                </a>
            </div>

        </div>
        <div id="task-list-flash" class="task-flash-area" aria-live="polite" aria-atomic="true"></div>
        @include('layouts/messages')

        <div class="table-responsive">
            <table class="table task-table align-middle mb-0 mt-5">
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
                                <span class="badge-event">{{ $task->event->eventTitle ?? 'N/A' }}</span>
                            </td>

                            <td class="text-center">
                                <div class="task-actions">
                                    <button type="button" class="btn btn-outline-secondary btn-sm btn-edit-task"
                                        data-task-id="{{ $task->task_id }}" data-title="{{ e($task->title) }}"
                                        data-description="{{ e($task->description) }}"
                                        data-event-id="{{ $task->event->event_id ?? ($task->event_id ?? $event->event_id) }}">
                                        Edit
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm btn-delete-task"
                                        data-event-id="{{ $event->event_id }}" data-task-id="{{ $task->task_id }}">
                                        Delete
                                    </button>


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
                console.log('[tasks] delete handler attached');

                // build a route template using blade so it matches your named route:
                // route('ngo.tasks.destroy', ['event' => 'EVENT_ID', 'task' => 'TASK_ID'])
                const deleteUrlTemplate =
                    "{{ route('ngo.tasks.destroy', ['event' => 'EVENT_ID', 'task' => 'TASK_ID']) }}";

                document.addEventListener('click', async function(e) {
                    const btn = e.target.closest('.btn-delete-task');
                    if (!btn) return;

                    e.preventDefault();

                    const taskId = btn.dataset.taskId;
                    const eventId = btn.dataset.eventId;

                    if (!taskId || !eventId) {
                        console.warn('[tasks] missing data-task-id or data-event-id on button',
                        btn);
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
                            // not JSON (likely an HTML redirect page) — capture text for debugging
                            const text = await resp.text();
                            console.warn('[tasks] response text (not json)', text.slice(0, 512));
                            body = {
                                ok: resp.ok,
                                text: text
                            };
                        }

                        if (!resp.ok) {
                            // show helpful error to console and flash
                            console.error('[tasks] delete failed', resp.status, body);
                            flash(body?.message || 'Failed to delete task', 'error');
                            return;
                        }

                        // success: remove row
                        const row = document.querySelector(
                            `#section-tasks tr[data-task-id="${taskId}"]`);
                        if (row) row.remove();
                        else if (btn.closest('tr')) btn.closest('tr').remove();

                        // if table now empty, insert empty row
                        const tbody = document.querySelector(
                            '#section-tasks table.task-table tbody') || document.querySelector(
                            '#section-tasks table tbody');
                        if (tbody && tbody.children.length === 0) {
                            const tr = document.createElement('tr');
                            tr.className = 'empty-row';
                            tr.innerHTML =
                                '<td colspan="4" class="text-center">No tasks found.</td>';
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

                function flash(text, type = 'success', {
                    duration = 2400
                } = {}) {
                    // Find inline container inside the task card (prefer #section-tasks container)
                    let container = document.querySelector('#task-list-flash');

                    // fallback: if not present, use #section-tasks card top area
                    if (!container) {
                        container = document.querySelector('#section-tasks .card-body') || document.body;
                    }

                    const el = document.createElement('div');
                    el.className = 'task-flash enter ' + (type === 'success' ? 'success' : 'error');
                    el.setAttribute('role', 'status');
                    el.textContent = text;

                    container.prepend(el);

                    // trigger enter animation
                    requestAnimationFrame(() => {
                        el.classList.remove('enter');
                        el.classList.add('enter-active');
                    });

                    // auto-remove after duration
                    setTimeout(() => {
                        // exit animation
                        el.classList.remove('enter-active');
                        el.classList.add('exit');

                        // remove after transition
                        setTimeout(() => {
                            el.remove();
                        }, 320);
                    }, duration);
                }

            });
        })();
    </script>
@endpush
