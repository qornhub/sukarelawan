{{-- resources/views/ngo/attendances/list.blade.php --}}
@if (!empty($ajax) && $ajax)
  {{-- AJAX-only: table HTML --}}
  <div class="table-responsive">
    <table class="table table-sm attendance-table align-middle mb-0">
      <thead>
        <tr>
          <th style="width:35%;">Volunteer</th>
          <th style="width:35%;">Email</th>
          <th style="width:20%;">Status</th>
          <th style="width:10%;" class="text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($attendances as $attendance)
          <tr data-attendance-id="{{ $attendance->attendance_id }}">
            <td>
              {{ optional($attendance->user)->name ?? 'N/A' }}
              @if($attendance->user && $attendance->user->volunteerProfile)
                <small class="text-muted d-block">
                  {{ $attendance->user->volunteerProfile->skill ?? '' }}
                </small>
              @endif
            </td>
            <td>{{ optional($attendance->user)->email ?? 'N/A' }}</td>
            <td>
              <div class="d-flex align-items-center">
                <div class="status-dot bg-success me-2" style="width:10px; height:10px; border-radius:50%;"></div>
                <span>Attended</span>
              </div>
            </td>
            <td class="text-center">
              <button type="button" class="btn btn-outline-danger btn-sm btn-delete-attendance"
                      data-event-id="{{ $attendance->event_id }}"
                      data-attendance-id="{{ $attendance->attendance_id }}">
                Delete
              </button>
            </td>
          </tr>
        @empty
          <tr class="empty-row">
            <td colspan="4" class="text-center">No attendances found.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

@else
  {{-- Full card + guarded JS (initial page render) --}}
  <div id="section-attendance" class="mt-4 card participant-section" style="display:none">
    <div class="card-body p-3">
      <div class="d-flex align-items-center justify-content-between mb-1">
        <h3 class="attendance-title mb-0">Attendance</h3>
      </div>

      <div id="attendance-list-flash" class="attendance-flash-area" aria-live="polite" aria-atomic="true"></div>
      @include('layouts/messages')

      <div id="attendance-table-container">
        {{-- initial table render (same markup as AJAX) --}}
        <div class="table-responsive">
          <table class="table table-sm attendance-table align-middle mb-0">
            <thead>
              <tr>
                <th style="width:35%;">Volunteer</th>
                <th style="width:35%;">Email</th>
                <th style="width:20%;">Status</th>
                <th style="width:10%;" class="text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($attendances as $attendance)
                <tr data-attendance-id="{{ $attendance->attendance_id }}">
                  <td>
                    {{ optional($attendance->user)->name ?? 'N/A' }}
                    @if($attendance->user && $attendance->user->volunteerProfile)
                      <small class="text-muted d-block">
                        {{ $attendance->user->volunteerProfile->skill ?? '' }}
                      </small>
                    @endif
                  </td>
                  <td>{{ optional($attendance->user)->email ?? 'N/A' }}</td>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="status-dot bg-success me-2" style="width:10px; height:10px; border-radius:50%;"></div>
                      <span>Attended</span>
                    </div>
                  </td>
                  <td class="text-center">
                    <button type="button" class="btn btn-outline-danger btn-sm btn-delete-attendance"
                            data-event-id="{{ $attendance->event_id }}"
                            data-attendance-id="{{ $attendance->attendance_id }}">
                      Delete
                    </button>
                  </td>
                </tr>
              @empty
                <tr class="empty-row">
                  <td colspan="4" class="text-center">No attendances found.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  @push('scripts')
  <script>
  (function() {
    if (window.__attendanceScriptInitialized) return;
    window.__attendanceScriptInitialized = true;

    // Delegated delete handler
    document.addEventListener('click', async function(e) {
      const btn = e.target.closest('.btn-delete-attendance');
      if (!btn) return;
      e.preventDefault();

      const attendanceId = btn.dataset.attendanceId;
      const eventId = btn.dataset.eventId;
      if (!attendanceId || !eventId) return;
      if (!confirm('Are you sure you want to delete this attendance?')) return;

      btn.disabled = true;
      try {
        const tokenMeta = document.querySelector('meta[name="csrf-token"]');
        const token = tokenMeta ? tokenMeta.getAttribute('content') : '';

        // plural destroy route
        const deleteUrlTemplate = "{{ route('ngo.attendances.destroy', ['event' => 'EVENT_ID', 'attendance' => 'ATTENDANCE_ID']) }}";
        const url = deleteUrlTemplate
          .replace('EVENT_ID', encodeURIComponent(eventId))
          .replace('ATTENDANCE_ID', encodeURIComponent(attendanceId));

        const resp = await fetch(url, {
          method: 'POST',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
          },
          body: new URLSearchParams({ _method: 'DELETE' })
        });

        let body = null;
        try { body = await resp.json(); } catch { body = { ok: resp.ok }; }

        if (!resp.ok) {
          flash(body?.message || 'Failed to delete attendance', 'error');
          return;
        }

        const row = document.querySelector(`#section-attendance tr[data-attendance-id="${attendanceId}"]`);
        if (row) row.remove();

        const tbody = document.querySelector('#section-attendance table.attendance-table tbody');
        if (tbody && tbody.children.length === 0) {
          const tr = document.createElement('tr');
          tr.className = 'empty-row';
          tr.innerHTML = '<td colspan="4" class="text-center">No attendances found.</td>';
          tbody.appendChild(tr);
        }

        flash(body?.message || 'Attendance deleted', 'success');
      } catch (err) {
        console.error('[attendance] delete error', err);
        flash('An error occurred while deleting', 'error');
      } finally {
        btn.disabled = false;
      }
    });

    // Reload (AJAX) function uses plural list route
    async function reloadAttendanceList() {
      const url = "{{ route('ngo.attendances.list', ['eventId' => $event->event_id]) }}";
      try {
        const resp = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }});
        if (!resp.ok) {
          console.warn('[attendance] reload returned non-ok', resp.status);
          return;
        }
        const html = await resp.text();
        const container = document.getElementById('attendance-table-container');
        if (container) container.innerHTML = html;
        console.log('[attendance] list reloaded');
      } catch (err) {
        console.error('[attendance] reload failed', err);
      }
    }

    if (!window.__attendanceReloadIntervalId) {
      reloadAttendanceList(); // immediate refresh
      window.__attendanceReloadIntervalId = setInterval(reloadAttendanceList, 10000);
    }

    function flash(text, type = 'success', { duration = 2400 } = {}) {
      let container = document.querySelector('#attendance-list-flash') || document.body;
      const el = document.createElement('div');
      el.className = 'task-flash enter ' + (type === 'success' ? 'success' : 'error');
      el.setAttribute('role', 'status');
      el.textContent = text;
      container.prepend(el);
      requestAnimationFrame(() => { el.classList.remove('enter'); el.classList.add('enter-active'); });
      setTimeout(() => { el.classList.remove('enter-active'); el.classList.add('exit'); setTimeout(() => el.remove(), 320); }, duration);
    }

  })();
  </script>
  @endpush
@endif
