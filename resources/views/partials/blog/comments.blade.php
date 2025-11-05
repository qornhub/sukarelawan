{{-- resources/views/partials/blog/comments.blade.php --}}
@php
    // $post is required (BlogPost). $comments expected to be passed by controller (collection or paginator).
    // Determine batch size (per page). Use paginator value if available, otherwise default to 3.
    $perPage = method_exists($comments, 'perPage') ? $comments->perPage() : 3;
@endphp

<div id="comments" class="mt-4">
    <!-- Header -->
    <div class="title111 d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">
            Comments ({{ $comments->total() ?? count($comments) }})
        </h4>
    </div>

    <!-- Comment Form -->
    @auth
        <div class="mb-4">
            <form action="{{ route('blogs.comments.store', $post->blogPost_id) }}" method="POST"
                class="d-flex align-items-start">
                @csrf
                <input name="content" class="comment-input form-control" placeholder="Type your comment here"
                    aria-label="Type your comment here" />
                <button type="submit" class="comment-publish-btn ms-2">Publish</button>
            </form>
            @error('content')
                <div class="text-danger small mt-2">{{ $message }}</div>
            @enderror
        </div>
    @else
        <div class="alert alert-light border text-center py-3 mb-4">
            <i class="fas fa-lock me-2 text-muted"></i>
            Please <a href="{{ route('login') }}" class="fw-bold">login</a> to leave a comment.
        </div>
    @endauth

    <!-- Comments List -->
    <div class="comments-list">
        @forelse($comments as $comment)
            @php
                // owner/admin
                $isOwner = auth()->check() && (string) auth()->id() === (string) $comment->user_id;
                $isAdmin = false;
                if (auth()->check()) {
                    $u = auth()->user();
                    if (isset($u->role) && is_object($u->role) && isset($u->role->roleName)) {
                        $isAdmin = strtolower($u->role->roleName) === 'admin';
                    } elseif (!empty($u->role) && is_string($u->role)) {
                        $isAdmin = strtolower($u->role) === 'admin';
                    } elseif (property_exists($u, 'is_admin') && $u->is_admin) {
                        $isAdmin = true;
                    }
                }
                $canManage = $isOwner || $isAdmin;

                // per-comment user
                $user = optional($comment->user);
                $profile = $user->volunteerProfile ?? ($user->ngoProfile ?? null);
                $filename = $profile->profilePhoto ?? ($profile->avatar ?? ($profile->photo ?? null));
                $avatarUrl = $filename ? asset('images/profiles/' . $filename) : asset('images/default-profile.png');

                if (!is_null($user->volunteerProfile)) {
                    $profileRouteName = 'volunteer.profile.show';
                } elseif (!is_null($user->ngoProfile)) {
                    $profileRouteName = 'ngo.profile.show';
                } else {
                    $profileRouteName = null;
                }
                $profileUrl =
                    $profileRouteName && \Illuminate\Support\Facades\Route::has($profileRouteName)
                        ? route($profileRouteName, $user->id)
                        : '#';
                $displayName = $user->name ?? ($profile->name ?? 'User');

                // Decide whether to hide this comment initially: show first $perPage comments (index 0..perPage-1)
                $hideInitially = $loop->index >= $perPage ? true : false;

                // sentiment values expected: Positive, Negative, Toxic
                $sent = $comment->sentiment ?? null;
                $sentLabel = $sent ? ucfirst($sent) : null;
                $sentClass = $sent ? strtolower($sent) : '';
            @endphp

            <div id="comment-{{ $comment->blogComment_id }}"
                class="comment-card mb-3 {{ $hideInitially ? 'hidden-comment' : '' }} {{ $sent ? 'sentiment-' . e($sentClass) : '' }}"
                @if ($hideInitially) style="display: none;" @endif>
                <div class="d-flex">
                    <div class="me-3">
                        <a href="{{ $profileUrl }}" title="{{ $displayName }}">
                            <img src="{{ $avatarUrl }}" alt="{{ $displayName }}" class="avatar-circle"
                                onerror="this.onerror=null;this.src='{{ asset('images/default-profile.png') }}'">
                        </a>
                    </div>

                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="comment-meta">
                                <h6 class="mb-0 d-flex align-items-center">
                                    {{ $displayName }}
                                </h6>
                                <small class="text-muted">
                                    <i class="far fa-clock me-1"></i>{{ $comment->created_at->diffForHumans() }}
                                </small>
                            </div>

                            @if ($canManage)
                                <div class="position-relative">
                                    <button class="comment-menu-btn" type="button"
                                        onclick="toggleCommentMenu('{{ $comment->blogComment_id }}', event)">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>

                                    <div id="commentMenu{{ $comment->blogComment_id }}" class="comment-menu-dropdown"
                                        style="display:none; position:absolute; right:0; top:100%; min-width:140px;">
                                        @if ($isOwner)
                                            <button class="dropdown-item" type="button"
                                                onclick="toggleEdit('{{ $comment->blogComment_id }}'); closeAllMenus();">
                                                <i class="fas fa-edit me-2 text-primary"></i> Edit
                                            </button>
                                        @endif
                                        @if ($isAdmin || $isOwner)
                                            <form
                                                action="{{ route('blogs.comments.destroy', [$post->blogPost_id, $comment->blogComment_id]) }}"
                                                method="POST" onsubmit="return confirmDeleteComment(this);"
                                                style="margin:0;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger"><i
                                                        class="fas fa-trash me-2"></i> Delete</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- comment content -->
                        <div id="comment-content-{{ $comment->blogComment_id }}" class="comment-body-bubble">
                            {!! nl2br(e($comment->content)) !!}
                        </div>

                        {{-- edit form (owner only, hidden by default) --}}
                        @if ($isOwner)
                            <form id="comment-edit-{{ $comment->blogComment_id }}" class="edit-comment-form"
                                data-comment-id="{{ $comment->blogComment_id }}"
                                action="{{ route('blogs.comments.update', [$post->blogPost_id, $comment->blogComment_id]) }}"
                                method="POST" style="display:none; margin-top:.5rem;">
                                @csrf
                                @method('PUT')
                                <textarea name="content" rows="3" class="form-control edit-content">{{ old('content', $comment->content) }}</textarea>
                                <div class="mt-2 d-flex gap-2">
                                    <button class="btn btn-primary btn-sm" type="submit">Save</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm"
                                        onclick="toggleEdit('{{ $comment->blogComment_id }}')">Cancel</button>
                                </div>
                            </form>
                        @endif
                        @if ($sentLabel)
                            <div class="sentiment-row mt-2">
                                <span class="sentiment-badge {{ e($sentClass) }}" role="status"
                                    aria-label="Sentiment: {{ $sentLabel }}">
                                    {{ $sentLabel }}
                                </span>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5">
                <i class="fas fa-comments text-muted fa-3x mb-2"></i>
                <h5 class="text-muted">No comments yet</h5>
                <p class="text-muted mb-0">Be the first to share your thoughts!</p>
            </div>
        @endforelse
    </div>

    @php
        $nextPageUrl =
            $comments->hasMorePages() ?? false
                ? request()->fullUrlWithQuery(['comments_page' => ($comments->currentPage() ?? 1) + 1])
                : '';
    @endphp

    @if ($comments->count() > $perPage || !empty($nextPageUrl))
        <div id="comments-load-more" data-batch="{{ $perPage }}" data-next-url="{{ $nextPageUrl }}"
            style="text-align: center; margin-top: 8px;">
            <button id="loadMoreBtn" type="button" class="btn btn-link">Load more comments</button>
        </div>
    @endif
</div>



@push('scripts')
    <script>
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.position-relative')) closeAllMenus();
        });

        function toggleCommentMenu(commentId, e) {
            if (e) e.stopPropagation();
            closeAllMenus();
            const menu = document.getElementById('commentMenu' + commentId);
            if (menu) menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
        }

        function closeAllMenus() {
            document.querySelectorAll('.comment-menu-dropdown').forEach(m => m.style.display = 'none');
        }

        function toggleEdit(commentId) {
            const editForm = document.getElementById('comment-edit-' + commentId);
            const contentDiv = document.getElementById('comment-content-' + commentId);
            if (!editForm) return;
            document.querySelectorAll('.edit-comment-form').forEach(f => {
                if (f.id !== 'comment-edit-' + commentId) f.style.display = 'none';
            });
            document.querySelectorAll('[id^="comment-content-"]').forEach(d => {
                if (d.id !== 'comment-content-' + commentId) d.style.display = 'block';
            });
            const isOpen = editForm.style.display === 'block';
            if (isOpen) {
                editForm.style.display = 'none';
                if (contentDiv) contentDiv.style.display = 'block';
            } else {
                editForm.style.display = 'block';
                if (contentDiv) contentDiv.style.display = 'none';
                const ta = editForm.querySelector('textarea');
                if (ta) {
                    ta.focus();
                    ta.selectionStart = ta.selectionEnd = ta.value.length;
                }
            }
            closeAllMenus();
        }

        function confirmDeleteComment(form) {
            return confirm('Are you sure you want to delete this comment?');
        }
        (function() {
            const holder = document.getElementById('comments-load-more');
            if (!holder) return;
            const batch = parseInt(holder.dataset.batch || 3, 10);
            const nextUrl = holder.dataset.nextUrl || '';
            const loadBtn = document.getElementById('loadMoreBtn');

            function revealNextBatch() {
                const container = document.querySelector('#comments .comments-list');
                if (!container) return;
                const hidden = Array.from(container.querySelectorAll('.hidden-comment'));
                if (hidden.length === 0) {
                    if (nextUrl) {
                        window.location.href = nextUrl;
                    } else if (loadBtn) {
                        loadBtn.style.display = 'none';
                    }
                    return;
                }
                const toReveal = hidden.slice(0, batch);
                toReveal.forEach(node => {
                    node.classList.remove('hidden-comment');
                    node.style.display = '';
                    node.classList.add('comment-new');
                });
                const stillHidden = container.querySelectorAll('.hidden-comment').length;
                if (stillHidden === 0 && !nextUrl) {
                    if (loadBtn) loadBtn.style.display = 'none';
                }
            }
            if (loadBtn) loadBtn.addEventListener('click', function(e) {
                e.preventDefault();
                revealNextBatch();
            });
        })();

        /*
  AJAX for blog comments (#comments)
  - Delegated submit handler intercepts forms inside #comments
  - Replaces #comments HTML with server-returned fragment (so server flashes appear)
  - Avoids duplicate delete confirmation when inline onsubmit exists
  - Non-intrusive: doesn't remove existing functions
*/

(function() {
  const containerSel = '#comments';
  const metaToken = document.querySelector('meta[name="csrf-token"]')?.content;

  function extractFragment(htmlText) {
    const parser = new DOMParser();
    const doc = parser.parseFromString(htmlText, 'text/html');
    const node = doc.querySelector(containerSel);
    return node ? node.innerHTML : null;
  }

  async function handleFormSubmit(form) {
    // If some other handler already prevented the submit, do nothing
    // (this avoids double-confirm when inline handler returned false)
    // Note: e.defaultPrevented check must be done in caller; we still include guard here.
    // Collect method (support _method override)
    const methodInput = form.querySelector('input[name="_method"]');
    const method = (methodInput ? methodInput.value.toUpperCase() : (form.method || 'POST')).toUpperCase();

    // Delete confirmation: avoid double dialog
    if (method === 'DELETE') {
      // If inline onsubmit contains confirmDeleteComment, assume it already ran.
      const onsubmitAttr = (form.getAttribute && form.getAttribute('onsubmit')) || '';
      const hasInlineConfirm = onsubmitAttr && onsubmitAttr.indexOf('confirmDeleteComment') !== -1;

      if (!hasInlineConfirm) {
        // No inline confirm — call the existing confirm function if available
        if (typeof confirmDeleteComment === 'function') {
          if (!confirmDeleteComment(form)) return;
        } else {
          if (!confirm('Are you sure you want to delete this comment?')) return;
        }
      } else {
        // Inline confirm exists — assume it already ran and allowed submit,
        // but just to be safe if the inline returns false it would have prevented the event.
      }
    }

    // Build FormData
    const formData = new FormData(form);
    if (!formData.get('_token') && metaToken) formData.append('_token', metaToken);

    const headers = { 'X-Requested-With': 'XMLHttpRequest' };
    if (metaToken) headers['X-CSRF-TOKEN'] = metaToken;

    // disable submit button UX
    const submitBtn = form.querySelector('[type="submit"]');
    let originalBtnHtml = null;
    if (submitBtn) {
      originalBtnHtml = submitBtn.innerHTML;
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
    }

    try {
      const response = await fetch(form.action, {
        method: method === 'GET' ? 'GET' : 'POST',
        headers,
        body: formData
      });

      if (response.ok) {
        // Replace #comments fragment with server-rendered HTML (keeps server flashes)
        const text = await response.text();
        const frag = extractFragment(text);
        const container = document.querySelector(containerSel);
        if (frag !== null && container) {
          container.innerHTML = frag;
        } else if (container) {
          // fallback: insert full response
          container.innerHTML = text;
        }
      } else if (response.status === 422) {
        // Validation errors — try to show them inside comments area if possible
        let payload;
        try { payload = await response.json(); } catch (e) { payload = null; }
        const container = document.querySelector(containerSel);
        if (container) {
          if (payload && payload.errors) {
            const msgs = Object.values(payload.errors).flat().map(m => `<li>${m}</li>`).join('');
            container.insertAdjacentHTML('afterbegin', `<div class="alert alert-danger alert-sm mb-2"><ul class="mb-0">${msgs}</ul></div>`);
          } else {
            const text = await response.text();
            container.insertAdjacentHTML('afterbegin', `<div class="alert alert-danger alert-sm mb-2">${text}</div>`);
          }
        }
      } else {
        // other error — try to extract fragment and insert, otherwise show generic message
        const text = await response.text();
        const frag = extractFragment(text);
        const container = document.querySelector(containerSel);
        if (frag && container) {
          container.innerHTML = frag;
        } else if (container) {
          container.insertAdjacentHTML('afterbegin', `<div class="alert alert-danger alert-sm mb-2">An error occurred. Please refresh the page.</div>`);
        }
      }
    } catch (err) {
      console.error('Blog comments AJAX error', err);
      const container = document.querySelector(containerSel);
      if (container) container.insertAdjacentHTML('afterbegin', `<div class="alert alert-danger alert-sm mb-2">Network error. Please try again.</div>`);
    } finally {
      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnHtml;
      }
    }
  }

  // Delegated submit listener for forms inside #comments
  document.addEventListener('submit', function(e) {
    const form = e.target;
    if (!form || !form.closest(containerSel)) return;

    // If another handler already prevented default (e.g., inline onsubmit returned false),
    // do nothing — prevents duplicate confirmations/handlers.
    if (e.defaultPrevented) return;

    // Prevent native submit and handle via AJAX
    e.preventDefault();
    handleFormSubmit(form);
  });

})();
    </script>
@endpush
