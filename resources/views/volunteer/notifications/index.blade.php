{{-- resources/views/volunteer/notifications/index.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Notifications — Volunteer</title>

  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  <style>
    :root {
      --primary-color: #4361ee;
      --primary-light: #4895ef;
      --secondary-color: #3f37c9;
      --success-color: #4cc9f0;
      --danger-color: #f72585;
      --warning-color: #f8961e;
      --light-bg: #f8f9fa;
      --card-bg: #ffffff;
      --border-color: #e9ecef;
      --text-primary: #2b2d42;
      --text-secondary: #6c757d;
      --text-muted: #adb5bd;
      --unread-bg: #f0f7ff;
      --shadow-sm: 0 2px 8px rgba(0,0,0,0.04);
      --shadow-md: 0 4px 20px rgba(0,0,0,0.08);
      --radius-lg: 12px;
      --radius-md: 8px;
      --transition: all 0.2s ease;
    }

    body {
      background: linear-gradient(135deg, var(--light-bg) 0%, #f1f4f9 100%);
      color: var(--text-primary);
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
      min-height: 100vh;
    }

    .notification-app {
      max-width: 800px;
      margin: 0 auto;
    }

    .header-section {
      background: var(--card-bg);
      backdrop-filter: blur(10px);
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow-sm);
      padding: 1.5rem;
      margin-bottom: 1.5rem;
      border: 1px solid var(--border-color);
    }

    .notification-card {
      background: var(--card-bg);
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow-sm);
      border: 1px solid var(--border-color);
      overflow: hidden;
    }

    .notification-item {
      padding: 1.25rem 1.5rem;
      border-bottom: 1px solid var(--border-color);
      transition: var(--transition);
      position: relative;
    }

    .notification-item:last-child {
      border-bottom: none;
    }

    .notification-item:hover {
      background: #fafbfe;
      transform: translateY(-1px);
    }

    .notification-unread {
      background: var(--unread-bg);
      border-left: 4px solid var(--primary-color);
    }

    .notification-unread:hover {
      background: #e8f2ff;
    }

    .notification-icon {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 1rem;
      flex-shrink: 0;
    }

    .icon-approved {
      background: linear-gradient(135deg, #4cc9f0, #4361ee);
      color: white;
    }

    .icon-rejected {
      background: linear-gradient(135deg, #f72585, #b5179e);
      color: white;
    }

    .icon-attended {
      background: linear-gradient(135deg, #f8961e, #f3722c);
      color: white;
    }

    .icon-default {
      background: linear-gradient(135deg, #7209b7, #3a0ca3);
      color: white;
    }

    .status-badge {
      font-size: 0.75rem;
      font-weight: 600;
      padding: 0.25rem 0.75rem;
      border-radius: 20px;
      text-transform: capitalize;
    }

    .badge-approved {
      background: rgba(76, 201, 240, 0.1);
      color: #4cc9f0;
      border: 1px solid rgba(76, 201, 240, 0.2);
    }

    .badge-rejected {
      background: rgba(247, 37, 133, 0.1);
      color: #f72585;
      border: 1px solid rgba(247, 37, 133, 0.2);
    }

    .badge-attended {
      background: rgba(248, 150, 30, 0.1);
      color: #f8961e;
      border: 1px solid rgba(248, 150, 30, 0.2);
    }

    .badge-default {
      background: rgba(67, 97, 238, 0.1);
      color: #4361ee;
      border: 1px solid rgba(67, 97, 238, 0.2);
    }

    .btn-mark-read {
      font-size: 0.8rem;
      font-weight: 500;
      padding: 0.4rem 0.8rem;
      border-radius: 6px;
      transition: var(--transition);
    }

    .btn-mark-all {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      border: none;
      color: white;
      font-weight: 500;
      padding: 0.5rem 1.25rem;
      border-radius: var(--radius-md);
      transition: var(--transition);
    }

    .btn-mark-all:hover {
      transform: translateY(-1px);
      box-shadow: var(--shadow-md);
    }

    .empty-state {
      padding: 3rem 2rem;
      text-align: center;
      color: var(--text-secondary);
    }

    .empty-state-icon {
      font-size: 4rem;
      margin-bottom: 1rem;
      opacity: 0.5;
    }

    .notification-message {
      line-height: 1.5;
      color: var(--text-primary);
    }

    .notification-time {
      font-size: 0.85rem;
      color: var(--text-muted);
    }

    .unread-indicator {
      width: 8px;
      height: 8px;
      background: var(--primary-color);
      border-radius: 50%;
      display: inline-block;
      margin-right: 0.5rem;
    }

    .flash-message {
      border-radius: var(--radius-md);
      border: none;
      box-shadow: var(--shadow-sm);
      padding: 0.75rem 1rem;
      margin-bottom: 1.5rem;
    }

    .pagination-container {
      background: var(--light-bg);
      padding: 1rem;
    }

    .page-link {
      border: none;
      color: var(--text-secondary);
      padding: 0.5rem 0.75rem;
      margin: 0 0.25rem;
      border-radius: var(--radius-md);
      transition: var(--transition);
    }

    .page-link:hover {
      background: var(--primary-color);
      color: white;
    }

    .page-item.active .page-link {
      background: var(--primary-color);
      border: none;
    }

    @media (max-width: 768px) {
      .notification-item {
        padding: 1rem;
      }
      
      .header-section {
        padding: 1rem;
      }
      
      .notification-message {
        font-size: 0.95rem;
      }
    }
  </style>
</head>

<body>
  @include('layouts.volunteer_header')

  <main class="py-4">
    <div class="container">
      <div class="notification-app">
        
        <!-- Header Section -->
        <div class="header-section">
          <div class="row align-items-center">
            <div class="col">
              <h1 class="h3 mb-1 fw-bold">Notifications</h1>
              <p class="text-muted mb-0">Stay updated with your volunteer activities</p>
            </div>
            <div class="col-auto">
              <button id="btn-mark-all" class="btn btn-mark-all">
                <i class="fas fa-check-double me-2"></i>Mark all as read
              </button>
            </div>
          </div>
        </div>

        <!-- Flash Messages Area -->
        <div id="notif-flash-area"></div>

        <!-- Notifications Card -->
        <div class="notification-card">
          <div id="notifications-list">
            @forelse($notifications as $note)
              @php
                $data = (array) $note->data;
                $isUnread = $note->read_at ? false : true;
                $status = $data['status'] ?? null;
                $eventName = $data['event_name'] ?? $data['eventTitle'] ?? null;

                // Determine icon and badge class based on status
                $iconClass = 'icon-default';
                $badgeClass = 'badge-default';
                
                if ($status === 'approved') {
                  $iconClass = 'icon-approved';
                  $badgeClass = 'badge-approved';
                } elseif ($status === 'rejected') {
                  $iconClass = 'icon-rejected';
                  $badgeClass = 'badge-rejected';
                } elseif ($status === 'attended') {
                  $iconClass = 'icon-attended';
                  $badgeClass = 'badge-attended';
                }

                // Determine icon
                $icon = 'fas fa-bell';
                if ($status === 'approved') $icon = 'fas fa-check-circle';
                if ($status === 'rejected') $icon = 'fas fa-times-circle';
                if ($status === 'attended') $icon = 'fas fa-user-check';

                // Message logic
                if (!empty($data['message'])) {
                  $messageToShow = $data['message'];
                } else {
                  if ($status === 'approved') {
                    $messageToShow = "You have been approved to join '" . ($eventName ?? 'Unknown Event') . "'";
                  } elseif ($status === 'rejected') {
                    $messageToShow = "Your registration for '" . ($eventName ?? 'Unknown Event') . "' was rejected";
                  } elseif ($status === 'attended') {
                    $messageToShow = "Your attendance for '" . ($eventName ?? 'Unknown Event') . "' has been recorded";
                  } else {
                    $messageToShow = $data['message'] ?? "Update for '" . ($eventName ?? 'Unknown Event') . "'";
                  }
                }
              @endphp

              <div class="notification-item {{ $isUnread ? 'notification-unread' : '' }}" data-id="{{ $note->id }}">
                <div class="d-flex align-items-start">
                  <div class="notification-icon {{ $iconClass }}">
                    <i class="{{ $icon }}"></i>
                  </div>
                  
                  <div class="flex-grow-1 me-3">
                    <div class="d-flex align-items-center mb-2">
                      @if($isUnread)
                        <span class="unread-indicator"></span>
                      @endif
                      
                      @if(!empty($status))
                        <span class="status-badge {{ $badgeClass }} me-2">
                          {{ $status }}
                        </span>
                      @endif
                      
                      <div class="notification-message flex-grow-1">
                        {!! e($messageToShow) !!}
                      </div>
                    </div>
                    
                    <div class="notification-time">
                      <i class="far fa-clock me-1"></i>
                      {{ $note->created_at->diffForHumans() }}
                    </div>
                  </div>

                  <div class="flex-shrink-0">
                    @if($isUnread)
                      <button class="btn btn-mark-read btn-outline-primary" data-id="{{ $note->id }}">
                        <i class="fas fa-check me-1"></i>Mark read
                      </button>
                    @else
                      <span class="text-muted small">
                        <i class="fas fa-check-circle text-success me-1"></i>Read
                      </span>
                    @endif
                  </div>
                </div>
              </div>

            @empty
              <div class="empty-state">
                <div class="empty-state-icon">
                  <i class="far fa-bell"></i>
                </div>
                <h4 class="h5 text-muted mb-2">No notifications yet</h4>
                <p class="text-muted mb-0">When you get notifications, they'll appear here</p>
              </div>
            @endforelse
          </div>

          @if($notifications->hasPages())
            <div class="pagination-container">
              <div class="d-flex justify-content-center">
                {{ $notifications->links() }}
              </div>
            </div>
          @endif
        </div>
      </div>
    </div>
  </main>

  <script src="https://js.pusher.com/8.2/pusher.min.js"></script>
  <script src="{{ asset('js/echo.js') }}"></script>

  <script>
  (function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Flash message helper
    function flash(text, type = 'success', timeout = 3000) {
      const area = document.getElementById('notif-flash-area');
      if (!area) return;
      
      const alertClass = type === 'success' ? 'alert alert-success flash-message' : 'alert alert-danger flash-message';
      const normalized = (text || '').trim();

      // Dedupe
      const existing = Array.from(area.querySelectorAll('.alert'))
        .find(a => a.textContent.trim() === normalized);
      if (existing) return;

      const el = document.createElement('div');
      el.className = alertClass;
      el.innerHTML = `
        <div class="d-flex align-items-center">
          <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'} me-2"></i>
          <span>${normalized}</span>
          <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
      `;
      area.prepend(el);

      setTimeout(() => {
        if (el.parentNode) {
          el.style.opacity = '0';
          setTimeout(() => el.remove(), 300);
        }
      }, timeout);
    }

    // Badge management functions
    function getBadgeEls() {
      const arr = [];
      const p = document.getElementById('notification-count');
      const t = document.getElementById('notification-count-tab');
      if (p) arr.push(p);
      if (t) arr.push(t);
      document.querySelectorAll('.notif-badge').forEach(e => arr.push(e));
      return arr;
    }

    function setBadges(n) {
      const val = Math.max(0, parseInt(n || 0) || 0);
      getBadgeEls().forEach(el => {
        el.textContent = val;
        el.style.display = val > 0 ? 'inline-block' : 'none';
      });
    }

    function decrementBadges(delta = 1) {
      getBadgeEls().forEach(el => {
        const cur = Math.max(0, parseInt(el.textContent || '0') || 0);
        const next = Math.max(0, cur - delta);
        el.textContent = next;
        el.style.display = next > 0 ? 'inline-block' : 'none';
      });
    }

    // Ensure badges exist
    (function ensureBadgesExist() {
      if (!document.getElementById('notification-count')) {
        const profileImg = document.querySelector('.volunteer-profile-img');
        if (profileImg) {
          const parent = profileImg.parentElement || profileImg;
          if (getComputedStyle(parent).position === 'static') parent.style.position = 'relative';
          const span = document.createElement('span');
          span.id = 'notification-count';
          span.className = 'notif-badge';
          span.style.cssText = 'position:absolute;top:-6px;right:-6px;pointer-events:none;background:var(--danger-color);color:white;border-radius:50%;width:18px;height:18px;font-size:0.7rem;display:flex;align-items:center;justify-content:center;';
          span.textContent = '0';
          parent.appendChild(span);
        }
      }
    })();

    // Initialize unread count
    async function initUnreadCount() {
      try {
        const resp = await fetch("{{ route('volunteer.notifications.unreadCount') }}", {
          credentials: 'same-origin',
          headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (!resp.ok) return;
        const json = await resp.json();
        const count = parseInt(json.unread || 0);
        setBadges(count);
      } catch (err) {
        console.warn('Could not fetch unread count', err);
      }
    }

    // Mark single as read
    async function markAsRead(id, elButton) {
      if (!id) return;
      try {
        if (elButton) {
          elButton.disabled = true;
          elButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Marking...';
        }

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

        if (!resp.ok) throw new Error('Server error: ' + resp.status);

        const row = document.querySelector(`.notification-item[data-id="${id}"]`);
        if (row) {
          row.classList.remove('notification-unread');
          const unreadIndicator = row.querySelector('.unread-indicator');
          if (unreadIndicator) unreadIndicator.remove();
          
          const btnContainer = row.querySelector('.flex-shrink-0');
          if (btnContainer) {
            btnContainer.innerHTML = '<span class="text-muted small"><i class="fas fa-check-circle text-success me-1"></i>Read</span>';
          }
        }

        decrementBadges(1);
        flash('Notification marked as read', 'success');
      } catch (err) {
        console.error('markAsRead error', err);
        flash('Failed to mark notification as read', 'error');
      }
    }

    // Mark all read
    async function markAllRead() {
      try {
        const btn = document.getElementById('btn-mark-all');
        if (btn) {
          btn.disabled = true;
          btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Marking all...';
        }

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

        document.querySelectorAll('.notification-item.notification-unread').forEach(row => {
          row.classList.remove('notification-unread');
          const unreadIndicator = row.querySelector('.unread-indicator');
          if (unreadIndicator) unreadIndicator.remove();
          
          const btnContainer = row.querySelector('.flex-shrink-0');
          if (btnContainer) {
            btnContainer.innerHTML = '<span class="text-muted small"><i class="fas fa-check-circle text-success me-1"></i>Read</span>';
          }
        });

        setBadges(0);
        flash('All notifications marked as read', 'success');
      } catch (err) {
        console.error('markAllRead error', err);
        flash('Failed to mark all notifications as read', 'error');
      } finally {
        const btn = document.getElementById('btn-mark-all');
        if (btn) {
          btn.disabled = false;
          btn.innerHTML = '<i class="fas fa-check-double me-2"></i>Mark all as read';
        }
      }
    }

    // Event delegation for click handlers
    document.addEventListener('click', function(e) {
      const btn = e.target.closest('.btn-mark-read');
      if (btn) {
        const id = btn.dataset.id;
        markAsRead(id, btn);
        return;
      }
      
      const allBtn = e.target.closest('#btn-mark-all');
      if (allBtn) {
        markAllRead();
        return;
      }
    });

    // Echo realtime notifications
    @if(auth()->check())
      if (window.Echo && window.Echo.private) {
        window.Echo.private(`App.Models.User.{{ auth()->id() }}`)
          .notification(function(notification) {
            try {
              // Handle realtime notification display
              bumpBadges(1);
            } catch (err) {
              console.error('Realtime notification handling failed', err);
            }
          });
      }
    @endif

    // Initialize
    initUnreadCount();
  })();
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>