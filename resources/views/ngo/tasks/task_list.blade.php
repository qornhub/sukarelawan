<div id="section-tasks" class="mt-4 card participant-section {{ $disabled ? 'is-disabled' : '' }}" style="display:none"
    data-disabled="{{ $disabled ? '1' : '0' }}" aria-disabled="{{ $disabled ? 'true' : 'false' }}">

    <div class="card-body p-3">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h3 class="task-title mb-0">Tasks</h3>

            {{-- Add Task --}}
            @if ($disabled)
                <button type="button" class="btn btn-primary btn-sm add-task-btn is-disabled" aria-disabled="true"
                    tabindex="-1" title="Task creation disabled — event has ended">
                    <i class="fa fa-plus me-1"></i> Add Task
                </button>
            @else
                <a href="#" class="btn btn-primary btn-sm add-task-btn">
                    <i class="fa fa-plus me-1"></i> Add Task
                </a>
            @endif
        </div>

        <div id="task-list-flash" class="task-flash-area" aria-live="polite" aria-atomic="true"></div>
        @include('layouts/messages')

        {{-- TASK CARD LIST --}}
        <div id="task-card-list">

            @forelse ($tasks as $task)
                <div class="task-card" data-task-id="{{ $task->task_id }}">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h5 class="mb-0">{{ $task->title }}</h5>
                        <span class="badge-event">
                            {{ optional($task->event)->eventTitle ?? 'N/A' }}
                        </span>
                    </div>

                    <p class="mb-2">
                        {{ \Illuminate\Support\Str::limit($task->description, 160, '...') }}
                    </p>

                    <div class="d-flex gap-2">
                        {{-- Edit --}}
                        @if ($disabled)
                            <button type="button" class="btn btn-outline-secondary btn-sm btn-edit-task is-disabled"
                                data-task-id="{{ $task->task_id }}" data-disabled="1" aria-disabled="true"
                                tabindex="-1">
                                Edit
                            </button>
                        @else
                            <button type="button" class="btn btn-outline-secondary btn-sm btn-edit-task"
                                data-task-id="{{ $task->task_id }}" data-title="{{ e($task->title) }}"
                                data-description="{{ e($task->description) }}"
                                data-event-id="{{ $task->event->event_id ?? ($task->event_id ?? $event->event_id) }}">
                                Edit
                            </button>
                        @endif

                        {{-- Delete --}}
                        @if ($disabled)
                            <button type="button" class="btn btn-outline-danger btn-sm btn-delete-task is-disabled"
                                data-task-id="{{ $task->task_id }}"
                                data-event-id="{{ $task->event->event_id ?? $task->event_id }}" data-disabled="1"
                                aria-disabled="true" tabindex="-1">
                                Delete
                            </button>
                        @else
                            <button type="button" class="btn btn-outline-danger btn-sm btn-delete-task"
                                data-task-id="{{ $task->task_id }}"
                                data-event-id="{{ $task->event->event_id ?? $task->event_id }}">
                                Delete
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="task-empty">No tasks found.</div>
            @endforelse

        </div>
    </div>
</div>

@push('scripts')
    <script>
        (function() {
            function ready(fn) {
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', fn);
                } else {
                    fn();
                }
            }
            /* ==========================================
             * SYNC HELPERS: Update Manage Section DOM
             * ========================================== */

            // 1. SYNC CREATE: Appends a new card to the Manage Tasks list
            function syncManageTaskCreate(task) {
                const list = document.querySelector('#section-manage-tasks .manage-task-card-list');
                if (!list) return;

                // Remove "No tasks found" message if it exists
                const emptyMsg = list.querySelector('.text-center.text-muted');
                if (emptyMsg) emptyMsg.remove();

                // Create the HTML for the new Manage Card
                // Note: We assume a new task has no assignments yet.
                const html = `
    <div class="manage-task-card"
         data-task-id="${task.task_id}"
         data-assigned="">

        <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
                <h5 class="task-title mb-1">${escapeHtml(task.title)}</h5>
                <p class="text-muted mb-0">
                    ${escapeHtml(task.description || '').substring(0, 100)}
                </p>
            </div>
            <button type="button" class="btn btn-sm btn-outline-success assign-btn"
                    data-task-id="${task.task_id}">
                Assign To
            </button>
        </div>

        <div class="assigned-users mt-2">
            <span class="text-muted">—</span>
        </div>
    </div>`;

                // Append to list
                list.insertAdjacentHTML('beforeend', html);
            }

            // 2. SYNC UPDATE: Updates title and description in Manage Tasks list
            function syncManageTaskUpdate(taskId, title, description) {
                const card = document.querySelector(
                `#section-manage-tasks .manage-task-card[data-task-id="${taskId}"]`);
                if (!card) return;

                // Update Title
                const titleEl = card.querySelector('.task-title');
                if (titleEl) titleEl.textContent = title;

                // Update Description (truncated)
                const descEl = card.querySelector('p.text-muted');
                if (descEl) {
                    let descText = description || '';
                    if (descText.length > 100) descText = descText.substring(0, 100) + '...';
                    descEl.textContent = descText;
                }
            }

            // 3. SYNC DELETE: Removes the card from Manage Tasks list
            function syncManageTaskDelete(taskId) {
                const card = document.querySelector(
                `#section-manage-tasks .manage-task-card[data-task-id="${taskId}"]`);
                if (card) {
                    card.remove();
                }

                // If list is empty, add the "No tasks" message back
                const list = document.querySelector('#section-manage-tasks .manage-task-card-list');
                if (list && list.children.length === 0) {
                    list.innerHTML = `
            <div class="text-center text-muted py-4">
                No tasks found.
            </div>
        `;
                }
            }

            // Helper for security (prevent XSS)
            function escapeHtml(text) {
                if (!text) return '';
                return text
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            }
            ready(function() {

                const deleteUrlTemplate =
                    "{{ route('ngo.tasks.destroy', ['event' => 'EVENT_ID', 'task' => 'TASK_ID']) }}";

                /* ==============================
                 * DISABLED CHECK
                 * ============================== */
                function isControlDisabled(el) {
                    if (!el) return false;
                    const section = el.closest('#section-tasks');
                    if (section?.dataset?.disabled === '1') return true;
                    if (el.dataset?.disabled === '1') return true;
                    if (el.classList.contains('is-disabled')) return true;
                    if (el.getAttribute('aria-disabled') === 'true') return true;
                    return false;
                }

                /* ==============================
                 * EMPTY STATE HANDLER
                 * ============================== */
                function normalizeEmptyState() {
                    const list = document.getElementById('task-card-list');
                    if (!list) return;

                    const cards = list.querySelectorAll('.task-card');
                    const empty = list.querySelector('.task-empty');

                    if (cards.length === 0 && !empty) {
                        const div = document.createElement('div');
                        div.className = 'task-empty';
                        div.textContent = 'No tasks found.';
                        list.appendChild(div);
                    }

                    if (cards.length > 0 && empty) {
                        empty.remove();
                    }
                }

                /* ==============================
                 * EDIT GUARD
                 * ============================== */
                document.addEventListener('click', function(e) {
                    const btn = e.target.closest('.btn-edit-task');
                    if (!btn) return;

                    if (isControlDisabled(btn)) {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        flash('Task editing is disabled — this event has ended.', 'error');
                    }
                });

                /* ==============================
                 * DELETE HANDLER
                 * ============================== */
                document.addEventListener('click', async function(e) {
                    const btn = e.target.closest('.btn-delete-task');
                    if (!btn) return;

                    if (isControlDisabled(btn)) {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        flash('Task deletion is disabled — this event has ended.', 'error');
                        return;
                    }

                    e.preventDefault();

                    const taskId = btn.dataset.taskId;
                    const eventId = btn.dataset.eventId;

                    if (!taskId || !eventId) {
                        flash('Missing task or event id', 'error');
                        return;
                    }

                    if (!confirm('Are you sure you want to delete this task?')) return;

                    btn.disabled = true;

                    try {
                        const token = document.querySelector('meta[name="csrf-token"]')?.content ||
                            '';

                        const url = deleteUrlTemplate
                            .replace('EVENT_ID', encodeURIComponent(eventId))
                            .replace('TASK_ID', encodeURIComponent(taskId));

                        const resp = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            },
                            body: new URLSearchParams({
                                _method: 'DELETE'
                            })
                        });

                        const body = await resp.json().catch(() => ({}));

                        if (!resp.ok) {
                            flash(body.message || 'Failed to delete task', 'error');
                            return;
                        }

                        document.querySelector(
                            `#task-card-list .task-card[data-task-id="${taskId}"]`
                        )?.remove();

                        //document.querySelector(
                          //  `#section-manage-tasks tr[data-task-id="${taskId}"]`
                       // )?.remove();
                      
                        syncManageTaskDelete(taskId); // <--- CALL THE HELPER HERE


                        normalizeEmptyState();
                        flash(body.message || 'Task deleted', 'success');

                    } catch (err) {
                        console.error(err);
                        flash('An error occurred while deleting', 'error');
                    } finally {
                        btn.disabled = false;
                    }
                });

                /* ==============================
                 * FLASH
                 * ============================== */
                function flash(text, type = 'success', duration = 2400) {
                    const container = document.querySelector('#task-list-flash') || document.body;
                    const el = document.createElement('div');
                    el.className = 'task-flash ' + type;
                    el.textContent = text;
                    container.prepend(el);
                    setTimeout(() => el.remove(), duration);
                }

            });
        })();
    </script>
@endpush
