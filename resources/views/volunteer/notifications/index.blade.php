{{-- resources/views/volunteer/notifications/index.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Notifications — Volunteer</title>

  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="user-id" content="{{ auth()->id() ?? '' }}">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/notification.css') }}">

  
</head>

<body>
  @include('layouts.volunteer_header')

  <main class="py-4">
    <div class="container">
      <div class="notification-app">

        <!-- Header Section -->
        <div class="header-section mb-3">
          <div class="row align-items-center">
            <div class="col">
              <h1 class="h3 mb-1 fw-bold">Notifications</h1>
              <p class="text-muted mb-0">Stay updated with your volunteer activities</p>
            </div>
            <div class="col-auto">
              <button id="btn-mark-all" class="btn btn-mark-all btn-outline-primary">
                <i class="fas fa-check-double me-2"></i>Mark all as read
              </button>
            </div>
          </div>
        </div>

        <!-- Flash Messages Area -->
        <div id="notif-flash-area"></div>

        <!-- Notifications Card -->
        <div class="notification-card card">
          <div class="card-body" id="notifications-list">
            @forelse($notifications as $note)
              @php
                // Normalize the data array
                $data = (array) $note->data;
                $isUnread = $note->read_at ? false : true;

                // Common fields
                $action = $data['action'] ?? null; // 'assigned'|'unassigned'
                $taskTitle = $data['task_title'] ?? $data['taskName'] ?? $data['title'] ?? null;
                $taskId = $data['task_id'] ?? null;
                // event info (try several common keys)
                $eventId = $data['event_id'] ?? $data['eventId'] ?? $data['event'] ?? null;
                $eventName = $data['event_name'] ?? $data['eventTitle'] ?? $data['eventName'] ?? null;
                $by = $data['by'] ?? $data['actor'] ?? null;
                $givenMessage = $data['message'] ?? null;
                $status = $data['status'] ?? null;

                // Default icon/badge/message
                $icon = 'fas fa-bell';
                $iconClass = 'icon-default';
                $badgeClass = 'badge-default';
                $messageToShow = $givenMessage ?? '';

                // Task assignment/unassignment priority
                if ($action === 'assigned') {
                    $icon = 'fas fa-user-plus';
                    $iconClass = 'icon-assigned';
                    $badgeClass = 'badge-assigned';
                    if (!$messageToShow) {
                        // prefer showing event name (clickable) if available, otherwise task title
                        $targetName = $eventName ?: $taskTitle ?: 'a task';
                        $messageToShow = "You were assigned to '{$targetName}'";
                        if ($by) $messageToShow .= " (by {$by})";
                    }
                } elseif ($action === 'unassigned') {
                    $icon = 'fas fa-user-minus';
                    $iconClass = 'icon-unassigned';
                    $badgeClass = 'badge-unassigned';
                    if (!$messageToShow) {
                        $targetName = $eventName ?: $taskTitle ?: 'a task';
                        $messageToShow = "You were unassigned from '{$targetName}'";
                        if ($by) $messageToShow .= " (by {$by})";
                    }
                } else {
                    // fallback to existing event status logic
                    if ($givenMessage) {
                      $messageToShow = $givenMessage;
                    } else {
                      if ($status === 'approved') {
                          $icon = 'fas fa-check-circle';
                          $iconClass = 'icon-approved';
                          $badgeClass = 'badge-approved';
                          $messageToShow = "You have been approved to join '" . ($eventName ?? 'Unknown Event') . "'";
                      } elseif ($status === 'rejected') {
                          $icon = 'fas fa-times-circle';
                          $iconClass = 'icon-rejected';
                          $badgeClass = 'badge-rejected';
                          $messageToShow = "Your registration for '" . ($eventName ?? 'Unknown Event') . "' was rejected";
                      } elseif ($status === 'attended') {
                          $icon = 'fas fa-user-check';
                          $iconClass = 'icon-attended';
                          $badgeClass = 'badge-attended';
                          $messageToShow = "Your attendance for '" . ($eventName ?? 'Unknown Event') . "' has been recorded";
                      } else {
                          $messageToShow = $givenMessage ?? "Update for '" . ($eventName ?? 'Unknown Event') . "'";
                      }
                    }
                }

                // Build safe HTML for message and make event name clickable (if eventId present)
                $messageHtml = e($messageToShow);

                if ($eventId && $eventName) {
                    // create link to volunteer event manage route (server-side)
                    $link = '<a href="' . e(route('volunteer.profile.registrationEditDelete', ['event_id' => $eventId])) . '" class="text-decoration-underline">' . e($eventName) . '</a>';
                    // replace plain event name in message with link (first occurrence)
                    $pos = mb_stripos($messageHtml, e($eventName));
                    if ($pos !== false) {
                        $before = mb_substr($messageHtml, 0, $pos);
                        $after = mb_substr($messageHtml, $pos + mb_strlen(e($eventName)));
                        $messageHtml = $before . $link . $after;
                    } else {
                        // if the event name wasn't present verbatim, append the link at the end
                        $messageHtml .= ' — ' . $link;
                    }
                } elseif ($eventId && !$eventName) {
                    // If event id exists but not name, append link labeled "View event"
                    $link = '<a href="' . e(route('volunteer.profile.registrationEditDelete', ['event_id' => $eventId])) . '" class="text-decoration-underline">View event</a>';
                    $messageHtml .= ' — ' . $link;
                }
              @endphp

              <div class="notification-item {{ $isUnread ? 'notification-unread' : '' }} mb-2 p-2" data-id="{{ $note->id }}">
                <div class="d-flex align-items-start">
                  <div class="notification-icon me-3 {{ $iconClass }}" style="min-width:36px;">
                    <i class="{{ $icon }}"></i>
                  </div>

                  <div class="flex-grow-1 me-3">
                    <div class="d-flex align-items-center mb-2">
                      @if($isUnread)
                        <span class="unread-indicator me-2" title="Unread"></span>
                      @endif

                      @if(!empty($status) || !empty($action))
                        <span class="status-badge {{ $badgeClass }} me-2">
                          {{ $action ?? $status }}
                        </span>
                      @endif

                      <div class="notification-message flex-grow-1">
                        {{-- allow limited html for the event link (we already escaped other text) --}}
                        {!! $messageHtml !!}
                      </div>
                    </div>

                    <div class="notification-time text-muted small">
                      <i class="far fa-clock me-1"></i>
                      {{ $note->created_at->diffForHumans() }}
                    </div>
                  </div>

                  <div class="flex-shrink-0 text-end">
                    @if($isUnread)
                      <button class="btn btn-mark-read btn-sm btn-outline-primary" data-id="{{ $note->id }}">
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
              <div class="empty-state text-center py-5">
                <div class="empty-state-icon mb-3">
                  <i class="far fa-bell fa-2x text-muted"></i>
                </div>
                <h4 class="h5 text-muted mb-2">No notifications yet</h4>
                <p class="text-muted mb-0">When you get notifications, they'll appear here</p>
              </div>
            @endforelse
          </div>

          @if($notifications->hasPages())
            <div class="pagination-container card-footer">
              <div class="d-flex justify-content-center">
                {{ $notifications->links() }}
              </div>
            </div>
          @endif
        </div>
      </div>
    </div>
  </main>

 {{-- Remove these old tags --}}
{{-- <script src="https://js.pusher.com/8.2/pusher.min.js"></script> --}}
{{-- <script src="{{ asset('js/echo.js') }}"></script> --}}

{{-- Replace them with this --}}
@vite(['resources/js/app.js'])

  <script>
  (function() {
    'use strict';
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const currentUserId = document.querySelector('meta[name="user-id"]').getAttribute('content') || null;

    // ----- Flash helper with dedupe set (avoid duplicate messages) -----
    const recentFlashSet = new Set();
    function flash(text, type = 'success', timeout = 5000) { // Changed to 5 seconds
      const normalized = (text || '').trim();
      if (!normalized) return;
      // dedupe identical messages for a short window (5s)
      if (recentFlashSet.has(normalized)) return;
      recentFlashSet.add(normalized);
      setTimeout(() => recentFlashSet.delete(normalized), 5000);

      const area = document.getElementById('notif-flash-area');
      if (!area) return;

      const alertClass = type === 'success' ? 'alert alert-success flash-message' : 'alert alert-danger flash-message';
      const el = document.createElement('div');
      el.className = alertClass + ' d-flex align-items-center';
      el.innerHTML = `
        <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'} me-2"></i>
        <div class="flex-grow-1">${escapeHtml(normalized)}</div>
        <button type="button" class="btn-close ms-3" aria-label="Close"></button>
      `;
      area.prepend(el);

      el.querySelector('.btn-close').addEventListener('click', () => el.remove());

      setTimeout(() => {
        if (el.parentNode) {
          el.style.opacity = '0';
          setTimeout(() => el.remove(), 300);
        }
      }, timeout);
    }

    function escapeHtml(s) {
      return String(s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
    }

    // ----- Badge helpers -----
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

    // ensure a small badge on header/profile exists
    (function ensureBadgesExist() {
      if (!document.getElementById('notification-count')) {
        const profileImg = document.querySelector('.volunteer-profile-img');
        if (profileImg) {
          const parent = profileImg.parentElement || profileImg;
          if (getComputedStyle(parent).position === 'static') parent.style.position = 'relative';
          const span = document.createElement('span');
          span.id = 'notification-count';
          span.className = 'notif-badge';
          span.style.cssText = 'position:absolute;top:-6px;right:-6px;pointer-events:none;background:#dc3545;color:white;border-radius:50%;width:18px;height:18px;font-size:0.7rem;display:flex;align-items:center;justify-content:center;';
          span.textContent = '0';
          parent.appendChild(span);
        }
      }
    })();

    // ----- Initialize unread count -----
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

    // ----- Mark single as read -----
    let markingSingle = new Set(); // prevent double-click / duplicate requests
    async function markAsRead(id, elButton) {
      if (!id) return;
      if (markingSingle.has(id)) return;
      markingSingle.add(id);
      
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

        const data = await resp.json();
        
        // Only update UI if the operation was successful
        if (data.success) {
            const row = document.querySelector(`.notification-item[data-id="${id}"]`);
            if (row && row.classList.contains('notification-unread')) {
                row.classList.remove('notification-unread');
                const unreadIndicator = row.querySelector('.unread-indicator');
                if (unreadIndicator) unreadIndicator.remove();

                const btnContainer = row.querySelector('.flex-shrink-0');
                if (btnContainer) {
                    btnContainer.innerHTML = '<span class="text-muted small"><i class="fas fa-check-circle text-success me-1"></i>Read</span>';
                }
                decrementBadges(1);
            }
            
            // REMOVED: No success message from frontend
            // Only show error messages if operation failed
        } else {
            flash(data.message || 'Failed to mark notification as read', 'error');
        }
      } catch (err) {
        console.error('markAsRead error', err);
        flash('Failed to mark notification as read', 'error');
      } finally {
        markingSingle.delete(id);
        if (elButton) {
          elButton.disabled = false;
          elButton.innerHTML = '<i class="fas fa-check me-1"></i>Mark read';
        }
      }
    }

    // ----- Mark all as read -----
    let markingAll = false;
    async function markAllRead() {
      if (markingAll) return;
      markingAll = true;
      
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

        const data = await resp.json();
        
        if (data.success) {
            // Update DOM (only unread ones)
            const unreadRows = document.querySelectorAll('.notification-item.notification-unread');
            unreadRows.forEach(row => {
                row.classList.remove('notification-unread');
                const unreadIndicator = row.querySelector('.unread-indicator');
                if (unreadIndicator) unreadIndicator.remove();

                const btnContainer = row.querySelector('.flex-shrink-0');
                if (btnContainer) {
                    btnContainer.innerHTML = '<span class="text-muted small"><i class="fas fa-check-circle text-success me-1"></i>Read</span>';
                }
            });

            setBadges(0);
            // REMOVED: No success message from frontend
            // Only show error messages if operation failed
        } else {
            flash(data.message || 'Failed to mark all notifications as read', 'error');
        }
      } catch (err) {
        console.error('markAllRead error', err);
        flash('Failed to mark all notifications as read', 'error');
      } finally {
        markingAll = false;
        const btn = document.getElementById('btn-mark-all');
        if (btn) {
          btn.disabled = false;
          btn.innerHTML = '<i class="fas fa-check-double me-2"></i>Mark all as read';
        }
      }
    }

    // ----- Event delegation -----
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

    // ----- Create DOM node for incoming notification (broadcast) -----
    function createNotificationNode(id, payload, unread = true) {
      // don't add if already exists (avoid duplicates)
      if (document.querySelector(`.notification-item[data-id="${id}"]`)) return null;

      const container = document.createElement('div');
      container.className = 'notification-item ' + (unread ? 'notification-unread' : '') + ' mb-2 p-2';
      container.dataset.id = id || ('notif-' + Date.now());

      const iconClass = (payload.action === 'assigned') ? 'fas fa-user-plus' :
                        (payload.action === 'unassigned') ? 'fas fa-user-minus' :
                        (payload.status === 'approved' ? 'fas fa-check-circle' :
                          (payload.status === 'rejected' ? 'fas fa-times-circle' :
                            (payload.status === 'attended' ? 'fas fa-user-check' : 'fas fa-bell')));

      const badgeText = payload.action ? payload.action : (payload.status || '');
      const eventId = payload.event_id ?? payload.eventId ?? null;
      const eventName = payload.event_name ?? payload.eventTitle ?? payload.eventName ?? null;

      let msg = payload.message || '';
      if (!msg) {
        if (payload.action === 'assigned') {
          const target = eventName || payload.task_title || 'a task';
          msg = `You were assigned to '${target}'` + (payload.by ? ` (by ${payload.by})` : '');
        } else if (payload.action === 'unassigned') {
          const target = eventName || payload.task_title || 'a task';
          msg = `You were unassigned from '${target}'` + (payload.by ? ` (by ${payload.by})` : '');
        } else if (payload.status) {
          msg = payload.message || `Update: ${payload.status}`;
        } else {
          msg = 'New notification';
        }
      }

      // Build message HTML and include clickable event link if available
      let messageHtml = escapeHtml(msg);

      if (eventId && eventName) {
        // Build link URL directly to the volunteer manage route pattern
        const linkUrl = '/volunteer/events/' + encodeURIComponent(eventId) + '/manage';
        const escapedEventName = escapeHtml(eventName);
        const link = `<a href="${escapeHtml(linkUrl)}" class="text-decoration-underline">${escapedEventName}</a>`;

        // attempt to replace first occurrence of escaped eventName in the escaped message
        const idx = messageHtml.indexOf(escapedEventName);
        if (idx !== -1) {
          messageHtml = messageHtml.slice(0, idx) + link + messageHtml.slice(idx + escapedEventName.length);
        } else {
          messageHtml += ' — ' + link;
        }
      } else if (eventId && !eventName) {
        const linkUrl = '/volunteer/events/' + encodeURIComponent(eventId) + '/manage';
        const link = `<a href="${escapeHtml(linkUrl)}" class="text-decoration-underline">View event</a>`;
        messageHtml += ' — ' + link;
      }

      container.innerHTML = `
        <div class="d-flex align-items-start">
          <div class="notification-icon me-3"><i class="${iconClass}"></i></div>
          <div class="flex-grow-1 me-3">
            <div class="d-flex align-items-center mb-2">
              ${unread ? '<span class="unread-indicator me-2"></span>' : ''}
              ${badgeText ? `<span class="status-badge me-2">${escapeHtml(badgeText)}</span>` : ''}
              <div class="notification-message flex-grow-1">${messageHtml}</div>
            </div>
            <div class="notification-time text-muted small"><i class="far fa-clock me-1"></i>just now</div>
          </div>
          <div class="flex-shrink-0 text-end">
            ${unread ? `<button class="btn btn-mark-read btn-sm btn-outline-primary" data-id="${escapeHtml(id || '')}">
                <i class="fas fa-check me-1"></i>Mark read
              </button>` :
              `<span class="text-muted small"><i class="fas fa-check-circle text-success me-1"></i>Read</span>`}
          </div>
        </div>
      `;
      return container;
    }

    // ----- Echo: realtime notifications (avoid duplicate flashes) -----
    @if(auth()->check())
      if (window.Echo && window.Echo.private && currentUserId) {
        window.Echo.private(`App.Models.User.{{ auth()->id() }}`)
          .notification(function(notification) {
            try {
              const id = notification.id || ('notif-' + Date.now());
              // if item already exists, skip adding and skip flash
              if (document.querySelector(`.notification-item[data-id="${id}"]`)) {
                return;
              }
              const list = document.getElementById('notifications-list');
              if (list) {
                const node = createNotificationNode(id, notification, true);
                if (node) list.prepend(node);
              }

              // increment badges
              const currentBadge = document.getElementById('notification-count');
              if (currentBadge) {
                const cur = parseInt(currentBadge.textContent || '0', 10) || 0;
                currentBadge.textContent = cur + 1;
                currentBadge.style.display = 'inline-block';
              }

              // REMOVED: No success message for realtime notifications from frontend
            } catch (err) {
              console.error('Realtime notification handling failed', err);
            }
          });
      }
    @endif

    // Initialize once
    initUnreadCount();
  })();
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>