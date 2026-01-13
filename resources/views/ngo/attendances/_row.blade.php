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
    @php $isLate = $attendance->status === 'late'; @endphp
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
    <button type="button"
            class="btn btn-outline-primary btn-sm btn-edit-attendance me-1"
            data-attendance-id="{{ $attendance->attendance_id }}"
            data-event-id="{{ $attendance->event_id }}"
            data-current-status="{{ $attendance->status ?? 'present' }}">
      Edit
    </button>

    <button type="button"
            class="btn btn-outline-danger btn-sm btn-delete-attendance"
            data-event-id="{{ $attendance->event_id }}"
            data-attendance-id="{{ $attendance->attendance_id }}">
      Delete
    </button>
  </td>
</tr>
