{{-- resources/views/volunteer/notifications/index.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Notifications — Volunteer</title>

  {{-- CSRF for AJAX --}}
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- Bootstrap + FontAwesome (CDN) --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  <style>
    body { background: #f7f9fc; color: #222; }
    .topbar { background: #fff; border-bottom: 1px solid #e9ecef; box-shadow: 0 1px 8px rgba(0,0,0,0.03); }
    .badge-count {
      background: #dc3545;
      color: #fff;
      border-radius: 999px;
      padding: 0.25rem 0.5rem;
      font-size: 0.75rem;
      display: inline-block;
      min-width: 22px;
      text-align: center;
    }
    .notification-unread { background: #f1f7ff; }
    .notification-item { transition: background .15s ease; }
    .no-notifs { color: #6c757d; }
  </style>
</head>
<body>

  @include('layouts.volunteer_header')

  {{-- Page content --}}
  <main class="py-4">
    <div class="container">

      <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Notifications</h3>

        <div>
          <button id="btn-mark-all" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-check-double me-1"></i> Mark all as read
          </button>
        </div>
      </div>

      <div id="notif-flash-area"></div>

      <div class="card">
        <div class="card-body p-0">
          <div id="notifications-list" class="list-group list-group-flush">
            @forelse($notifications as $note)
              @php
                $data = (array) $note->data;
                $isUnread = $note->read_at ? false : true;
              @endphp

              <div class="list-group-item notification-item {{ $isUnread ? 'notification-unread' : '' }}" data-id="{{ $note->id }}">
                <div class="d-flex align-items-start justify-content-between">
                  <div>
                    <div class="fw-semibold mb-1">
                      {{-- status if present --}}
                      @if(!empty($data['status']))
                        <span class="me-2 text-capitalize small text-muted">[{{ $data['status'] }}]</span>
                      @endif
                      {!! e($data['message'] ?? 'Notification') !!}
                    </div>
                    <div class="small text-muted">
                      {{ $note->created_at->diffForHumans() }}
                    </div>
                  </div>

                  <div class="text-end">
                    @if($isUnread)
                      <button class="btn btn-sm btn-outline-success btn-mark-read" data-id="{{ $note->id }}">
                        Mark read
                      </button>
                    @else
                      <span class="small text-muted">Read</span>
                    @endif
                  </div>
                </div>
              </div>

            @empty
              <div class="p-4 text-center no-notifs">
                <i class="far fa-bell fa-2x mb-2"></i>
                <div>No notifications yet.</div>
              </div>
            @endforelse
          </div>
        </div>

        <div class="card-footer">
          {{-- pagination (preserves GET params) --}}
          <div class="d-flex justify-content-center">
            {{ $notifications->links() }}
          </div>
        </div>

      </div>
    </div>
  </main>

  {{-- Scripts --}}
  <script src="https://js.pusher.com/8.2/pusher.min.js"></script>
  <script src="{{ asset('js/echo.js') }}"></script>

  <script>
    (function() {
      // Helpers
      const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

      function flash(text, type = 'success', timeout = 2400) {
        const area = document.getElementById('notif-flash-area');
        const el = document.createElement('div');
        el.className = 'alert ' + (type === 'success' ? 'alert-success' : 'alert-danger') + '';
        el.textContent = text;
        area.prepend(el);
        setTimeout(() => el.remove(), timeout);
      }

      // Initialize badge from server
      async function initUnreadCount() {
        try {
          const resp = await fetch("{{ route('volunteer.notifications.unreadCount') }}", {
            credentials: 'same-origin',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' }
          });
          if (!resp.ok) return;
          const json = await resp.json();
          const count = parseInt(json.unread || 0);
          const badge = document.getElementById('notification-count');
          if (count > 0) {
            badge.style.display = 'inline-block';
            badge.textContent = count;
          } else {
            badge.style.display = 'none';
          }
        } catch (err) {
          console.warn('Could not fetch unread count', err);
        }
      }

      // Mark a single notification as read
      async function markAsRead(id, elButton) {
        try {
          const url = "{{ url('/volunteer/notifications') }}/" + encodeURIComponent(id) + "/mark-as-read";
          const resp = await fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrfToken,
              'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({})
          });
          if (!resp.ok) {
            const text = await resp.text();
            throw new Error('Server error: ' + resp.status + ' ' + text);
          }
          // update DOM
          const row = document.querySelector(`.notification-item[data-id="${id}"]`);
          if (row) {
            row.classList.remove('notification-unread');
            // change button to 'Read' label
            const btn = row.querySelector('.btn-mark-read');
            if (btn) btn.replaceWith(Object.assign(document.createElement('span'), { className: 'small text-muted', textContent: 'Read' }));
          }
          // decrement badge
          const badge = document.getElementById('notification-count');
          if (badge) {
            let cur = parseInt(badge.textContent || '0');
            cur = Math.max(0, cur - 1);
            if (cur <= 0) { badge.style.display = 'none'; badge.textContent = '0'; }
            else badge.textContent = cur;
          }
          flash('Marked as read', 'success');
        } catch (err) {
          console.error('markAsRead error', err);
          flash('Failed to mark read', 'error');
        }
      }

      // Mark all read
      async function markAllRead() {
        try {
          const url = "{{ route('volunteer.notifications.markAllRead') }}";
          const resp = await fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrfToken,
              'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({})
          });
          if (!resp.ok) throw new Error('Server returned ' + resp.status);
          // Update DOM: convert all unread rows
          document.querySelectorAll('.notification-item.notification-unread').forEach(row => {
            row.classList.remove('notification-unread');
            const btn = row.querySelector('.btn-mark-read');
            if (btn) btn.replaceWith(Object.assign(document.createElement('span'), { className: 'small text-muted', textContent: 'Read' }));
          });
          const badge = document.getElementById('notification-count');
          if (badge) { badge.style.display = 'none'; badge.textContent = '0'; }
          flash('All notifications marked as read', 'success');
        } catch (err) {
          console.error('markAllRead error', err);
          flash('Failed to mark all read', 'error');
        }
      }

      // Wire up UI events
      document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-mark-read');
        if (btn) {
          const id = btn.dataset.id;
          markAsRead(id, btn);
        }
        const allBtn = e.target.closest('#btn-mark-all');
        if (allBtn) {
          markAllRead();
        }
      });

      // Listen for real-time notifications via Echo
      @if(auth()->check())
        window.Echo && window.Echo.private(`App.Models.User.{{ auth()->id() }}`)
          .notification(function(notification) {
            try {
              // notification structure: { id, type, data: { ... } }
              const payload = notification.data || notification;
              const msg = payload.message || 'New notification';
              const status = payload.status || '';
              const createdAt = new Date().toLocaleString();

              // prepend new item to list
              const container = document.getElementById('notifications-list');
              if (container) {
                const wrapper = document.createElement('div');
                wrapper.className = 'list-group-item notification-item notification-unread';
                // Using a safe text insertion for message
                const statusHtml = status ? `<span class="me-2 text-capitalize small text-muted">[${status}]</span>` : '';
                wrapper.setAttribute('data-id', notification.id || payload.id || ('n-' + Math.floor(Math.random()*100000)));
                wrapper.innerHTML = `
                  <div class="d-flex align-items-start justify-content-between">
                    <div>
                      <div class="fw-semibold mb-1">${statusHtml}${escapeHtml(msg)}</div>
                      <div class="small text-muted">${escapeHtml(createdAt)}</div>
                    </div>
                    <div class="text-end">
                      <button class="btn btn-sm btn-outline-success btn-mark-read" data-id="${notification.id || payload.id || ''}">Mark read</button>
                    </div>
                  </div>
                `;
                container.prepend(wrapper);
              }

              // bump badge
              const badge = document.getElementById('notification-count');
              if (badge) {
                const cur = parseInt(badge.textContent || '0') || 0;
                badge.style.display = 'inline-block';
                badge.textContent = cur + 1;
              }
              // optional visual popup
              // you can replace alert with a nicer toast
              // alert(msg);
            } catch (err) {
              console.error('Realtime notification handling failed', err);
            }
          });
      @endif

      // simple escape helper
      function escapeHtml(s) {
        if (!s) return '';
        return String(s)
          .replace(/&/g, '&amp;')
          .replace(/</g, '&lt;')
          .replace(/>/g, '&gt;')
          .replace(/"/g, '&quot;')
          .replace(/'/g, '&#39;');
      }

      // init
      initUnreadCount();
    })();
  </script>

  {{-- Bootstrap JS (optional, used by pagination styling etc) --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
