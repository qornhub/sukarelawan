@php
  // Defensive default: parent should pass $disabled (true when event ended)
  $disabled = $disabled ?? false;
  $isAdminReadonly = $isAdminReadonly ?? false;
@endphp

@if (!empty($ajax) && $ajax)
  {{-- AJAX-only: table HTML --}}
  <div class="table-responsive" data-disabled="{{ $disabled ? '1' : '0' }}" aria-disabled="{{ $disabled ? 'true' : 'false' }}">
    <table class="table table-sm attendance-table align-middle mb-0">
      <thead>
        <tr>
           <th style="width:30%;">Volunteer</th>
    <th style="width:30%;">Email</th>
    <th style="width:15%;">Status</th>
    <th style="width:15%;">Attended At</th>
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

            {{-- ✅ Dynamic Status --}}
            <td>
              @php
                $isLate = $attendance->status === 'late';
              @endphp
              <div class="d-flex align-items-center attendance-status">
                <div class="status-dot {{ $isLate ? 'bg-warning' : 'bg-success' }} me-2"
                     style="width:10px; height:10px; border-radius:50%;"></div>
                <span>{{ $isLate ? 'Late' : 'Present' }}</span>
              </div>
            </td>
<td>
  @if($attendance->attendanceTime)
    <span title="{{ $attendance->attendanceTime }}">
      {{ \Carbon\Carbon::parse($attendance->attendanceTime)->format('d M Y, h:i A') }}
    </span>
  @else
    <span class="text-muted">—</span>
  @endif
</td>


            <td class="text-center">
              @if(!$disabled)
                <button type="button"
                        class="btn btn-outline-primary btn-sm btn-edit-attendance me-1"
                        data-attendance-id="{{ $attendance->attendance_id }}"
                        data-event-id="{{ $attendance->event_id }}"
                        data-current-status="{{ $attendance->status }}">
                  Edit
                </button>
              @endif

              <button type="button"
                      class="btn btn-outline-danger btn-sm btn-delete-attendance"
                      data-event-id="{{ $attendance->event_id }}"
                      data-attendance-id="{{ $attendance->attendance_id }}"
                      {{ $disabled ? 'disabled aria-disabled=true tabindex=-1' : '' }}>
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
  <div id="section-attendance"
       class="mt-4 card participant-section {{ $disabled ? 'is-disabled' : '' }}"
       style="display:none"
       data-disabled="{{ $disabled ? '1' : '0' }}"
       aria-disabled="{{ $disabled ? 'true' : 'false' }}">
    <div class="card-body p-3">
      <div class="d-flex align-items-center justify-content-between mb-1">
        <h3 class="attendance-title mb-0">Attendance</h3>
      </div>

      @if ($disabled && !$isAdminReadonly)
        <div class="alert alert-warning">
          Attendance management is disabled — this event has ended.
        </div>
      @endif

      <div id="attendance-list-flash" class="attendance-flash-area" aria-live="polite" aria-atomic="true"></div>
      @include('layouts/messages')

      <div id="attendance-table-container" data-disabled="{{ $disabled ? '1' : '0' }}">
        @include('ngo.attendances.list', ['ajax' => true])
      </div>
    </div>
  </div>

  {{-- ================= EDIT MODAL (ONE TIME) ================= --}}
  <div class="modal fade" id="attendanceEditModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Attendance Status</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" id="editAttendanceId">
          <input type="hidden" id="editEventId">

          <label class="form-label">Status</label>
          <select id="editAttendanceStatus" class="form-select">
            <option value="present">Present</option>
            <option value="late">Late</option>
          </select>

          <small class="text-muted d-block mt-2">
            Marking as <strong>Late</strong> may deduct 30 points.
          </small>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
            Cancel
          </button>
          <button type="button" class="btn btn-primary btn-sm" id="saveAttendanceEdit">
            Save
          </button>
        </div>
      </div>
    </div>
  </div>

  @push('scripts')
  <script>
  (function() {
    if (window.__attendanceScriptInitialized) return;
    window.__attendanceScriptInitialized = true;

    /* ================= DELETE (UNCHANGED) ================= */
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
        const token = document.querySelector('meta[name="csrf-token"]').content;
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

        const body = await resp.json();
        if (!resp.ok) {
          flash(body.message || 'Failed to delete attendance', 'error');
          return;
        }

        document.querySelector(`tr[data-attendance-id="${attendanceId}"]`)?.remove();
        flash(body.message || 'Attendance deleted', 'success');
      } finally {
        btn.disabled = false;
      }
    });

    /* ================= EDIT: OPEN MODAL ================= */
    document.addEventListener('click', function (e) {
      const btn = e.target.closest('.btn-edit-attendance');
      if (!btn) return;

      document.getElementById('editAttendanceId').value = btn.dataset.attendanceId;
      document.getElementById('editEventId').value = btn.dataset.eventId;
      document.getElementById('editAttendanceStatus').value = btn.dataset.currentStatus;

      new bootstrap.Modal(document.getElementById('attendanceEditModal')).show();
    });

    /* ================= EDIT: SAVE PATCH ================= */
    document.getElementById('saveAttendanceEdit').addEventListener('click', async function () {
      const attendanceId = document.getElementById('editAttendanceId').value;
      const eventId = document.getElementById('editEventId').value;
      const status = document.getElementById('editAttendanceStatus').value;

      const token = document.querySelector('meta[name="csrf-token"]').content;
      const urlTemplate = "{{ route('ngo.attendances.update', ['event' => 'EVENT_ID', 'attendance' => 'ATTENDANCE_ID']) }}";
      const url = urlTemplate
        .replace('EVENT_ID', encodeURIComponent(eventId))
        .replace('ATTENDANCE_ID', encodeURIComponent(attendanceId));

      try {
        const resp = await fetch(url, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ _method: 'PATCH', status })
        });

        const data = await resp.json();
        if (!resp.ok) {
          flash(data.message || 'Failed to update attendance', 'error');
          return;
        }

        const row = document.querySelector(`tr[data-attendance-id="${attendanceId}"]`);
        if (row) {
          const dot = row.querySelector('.status-dot');
          const text = row.querySelector('.attendance-status span');

          if (status === 'late') {
            dot.classList.remove('bg-success');
            dot.classList.add('bg-warning');
            text.textContent = 'Late';
          } else {
            dot.classList.remove('bg-warning');
            dot.classList.add('bg-success');
            text.textContent = 'Present';
          }

          row.querySelector('.btn-edit-attendance').dataset.currentStatus = status;
        }

        bootstrap.Modal.getInstance(document.getElementById('attendanceEditModal')).hide();
        flash(data.message || 'Attendance updated', 'success');
      } catch (err) {
        console.error(err);
        flash('An error occurred while updating', 'error');
      }
    });

  function flash(text, type = 'success', { duration = 2400 } = {}) {
  let container = document.querySelector('#attendance-list-flash');
  if (!container) return;

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
    setTimeout(() => el.remove(), 300);
  }, duration);
}


  })();
  </script>
  @endpush
@endif
