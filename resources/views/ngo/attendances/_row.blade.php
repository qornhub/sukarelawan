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
