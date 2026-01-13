import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// 1. Force TLS to always be true for Railway
const forceTLS = true; 

// 2. Define the Auth Endpoint
const authEndpoint = (typeof window !== 'undefined' && window.__BROADCAST_AUTH_ENDPOINT)
  ? window.__BROADCAST_AUTH_ENDPOINT
  : '/broadcasting/auth';

if (!window.Echo) {
  window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    authEndpoint: authEndpoint,
    forceTLS: forceTLS,
    encrypted: true,        // Add this line
    disableStats: true,     // Optional: Cleaner logs
    auth: {
      headers: {
        'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') || {}).getAttribute?.('content') || ''
      }
    }
  });

  // When Pusher connects, stop the previous attendance polling (if present),
  // because real-time updates will replace the need to poll repeatedly.
  try {
    if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
      window.Echo.connector.pusher.connection.bind('connected', function() {
        if (window.__attendanceReloadIntervalId) {
          clearInterval(window.__attendanceReloadIntervalId);
          window.__attendanceReloadIntervalId = null;
          console.log('[attendance] stopped polling because realtime is connected');
        }
      });
    }
  } catch (err) {
    // ignore binding errors in environments where connector not available
    console.warn('[echo-listener] could not bind connected handler', err);
  }
}

/**
 * initAttendanceRealtime - call this with the current event id (string)
 * It attaches a listener to private channel `ngo-event.{eventId}` and
 * inserts server-rendered HTML row (if provided) or builds a row from JSON.
 */
export function initAttendanceRealtime(eventId) {
  if (!eventId || !window.Echo) return;

  const channelName = `ngo-event.${eventId}`;

  // Subscribe to the private channel and listen for the broadcasted event
  window.Echo.private(channelName)
    .listen('.AttendanceCreated', (e) => {
      // stop polling if not yet stopped (extra safety)
      if (window.__attendanceReloadIntervalId) {
        clearInterval(window.__attendanceReloadIntervalId);
        window.__attendanceReloadIntervalId = null;
        console.log('[attendance] stopped polling (on event receipt)');
      }

      const tbody = document.querySelector('#attendance-table-container table.attendance-table tbody');
      if (!tbody) return;
      const empty = tbody.querySelector('.empty-row');
      if (empty) empty.remove();

      if (e.html) {
        const tmp = document.createElement('tbody');
        tmp.innerHTML = e.html.trim();
        const newRow = tmp.firstElementChild;
        if (newRow) tbody.insertBefore(newRow, tbody.firstChild);
        return;
      }

      // ... inside initAttendanceRealtime function ...

      const data = e.attendance;
      if (!data) return;

      // 1. Create a simple date formatter to match your PHP format
      // Tries to use data.attendanceTime, falls back to current time
      const timeVal = data.attendanceTime || data.attendance_time || new Date().toISOString();
      const dateObj = new Date(timeVal);
      const formattedDate = dateObj.toLocaleDateString('en-GB', { 
          day: 'numeric', month: 'short', year: 'numeric' 
      }) + ', ' + dateObj.toLocaleTimeString('en-US', { 
          hour: 'numeric', minute: 'numeric', hour12: true 
      });

      const tr = document.createElement('tr');
      tr.setAttribute('data-attendance-id', data.attendance_id);
      
      // 2. Update the innerHTML to include the missing parts
      tr.innerHTML = `
        <td>
            ${escapeHtml(data.user?.name || 'N/A')}
            ${data.user?.volunteerProfile?.skill ? `<small class="text-muted d-block">${escapeHtml(data.user.volunteerProfile.skill)}</small>` : ''}
        </td>

        <td>${escapeHtml(data.user?.email || 'N/A')}</td>

        <td>
            <div class="d-flex align-items-center attendance-status">
                <div class="status-dot bg-success me-2" style="width:10px;height:10px;border-radius:50%;"></div>
                <span>Present</span>
            </div>
        </td>

        <td>
            <span title="${escapeHtml(timeVal)}">${formattedDate}</span>
        </td>

        <td class="text-center">
            <button type="button"
                class="btn btn-outline-primary btn-sm btn-edit-attendance me-1"
                data-attendance-id="${escapeHtml(data.attendance_id)}"
                data-event-id="${escapeHtml(data.event_id)}"
                data-current-status="${escapeHtml(data.status || 'present')}">
                Edit
            </button>

            <button type="button" 
                class="btn btn-outline-danger btn-sm btn-delete-attendance" 
                data-event-id="${escapeHtml(data.event_id)}" 
                data-attendance-id="${escapeHtml(data.attendance_id)}">
                Delete
            </button>
        </td>
      `;
      
      tbody.insertBefore(tr, tbody.firstChild);

// ... rest of the file ...
    });

  function escapeHtml(s) {
    if (!s) return '';
    return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
  }
}
