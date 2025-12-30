{{-- resources/views/partials/blog/comments.blade.php --}}
<div id="comments" class="mt-4">

    {{-- Header --}}
    <div class="title111 d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">
            Comments ({{ $comments->total() }})
        </h4>
    </div>

    {{-- Comment Form --}}
    @auth
        <div class="mb-4">
            <form action="{{ route('blogs.comments.store', $post->blogPost_id) }}"
                  method="POST"
                  class="d-flex align-items-start">
                @csrf
                <input name="content"
                       class="comment-input form-control"
                       placeholder="Type your comment here"
                       aria-label="Type your comment here">
                <button type="submit" class="comment-publish-btn ms-2">
                    Publish
                </button>
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

    {{-- Comments List --}}
    <div class="comments-list">
        @forelse ($comments as $comment)
            @php
                // permission
                $isOwner = auth()->check() && (string) auth()->id() === (string) $comment->user_id;
                $isAdmin = auth()->check() && (
                    (isset(auth()->user()->role->roleName) && strtolower(auth()->user()->role->roleName) === 'admin') ||
                    (property_exists(auth()->user(), 'is_admin') && auth()->user()->is_admin)
                );
                $canManage = $isOwner || $isAdmin;

                // user + profile
                $user = optional($comment->user);
                $profile = $user->volunteerProfile ?? $user->ngoProfile;
                $filename = $profile->profilePhoto ?? $profile->avatar ?? $profile->photo ?? null;
                $avatarUrl = $filename
                    ? asset('images/profiles/' . $filename)
                    : asset('images/default-profile.png');

                if ($user->volunteerProfile) {
                    $profileRoute = Route::has('volunteer.profile.show')
                        ? route('volunteer.profile.show', $user->id)
                        : '#';
                } elseif ($user->ngoProfile) {
                    $profileRoute = Route::has('ngo.profile.show')
                        ? route('ngo.profile.show', $user->id)
                        : '#';
                } else {
                    $profileRoute = '#';
                }

                $displayName = $user->name ?? $profile->name ?? 'User';

                // sentiment
                $sent = $comment->sentiment;
                $sentLabel = $sent ? ucfirst($sent) : null;
                $sentClass = $sent ? strtolower($sent) : '';
            @endphp

            <div id="comment-{{ $comment->blogComment_id }}"
                 class="comment-card mb-3 {{ $sent ? 'sentiment-' . e($sentClass) : '' }}">
                <div class="d-flex">

                    {{-- Avatar --}}
                    <div class="me-3">
                        <a href="{{ $profileRoute }}" title="{{ $displayName }}">
                            <img src="{{ $avatarUrl }}"
                                 class="avatar-circle"
                                 alt="{{ $displayName }}"
                                 onerror="this.onerror=null;this.src='{{ asset('images/default-profile.png') }}'">
                        </a>
                    </div>

                    <div class="flex-grow-1">

                        {{-- Meta + menu --}}
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="comment-meta">
                                <h6 class="mb-0">{{ $displayName }}</h6>
                                <small class="text-muted">
                                    <i class="far fa-clock me-1"></i>
                                    {{ $comment->created_at->diffForHumans() }}
                                </small>
                            </div>

                            @if ($canManage)
                                <div class="position-relative">
                                    <button type="button"
                                            class="comment-menu-btn"
                                            onclick="toggleCommentMenu('{{ $comment->blogComment_id }}', event)">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>

                                    <div id="commentMenu{{ $comment->blogComment_id }}"
                                         class="comment-menu-dropdown"
                                         style="display:none; position:absolute; right:0; top:100%; min-width:140px;">
                                        @if ($isOwner)
                                            <button type="button"
                                                    class="dropdown-item"
                                                    onclick="toggleEdit('{{ $comment->blogComment_id }}')">
                                                <i class="fas fa-edit me-2 text-primary"></i> Edit
                                            </button>
                                        @endif

                                        <form action="{{ route('blogs.comments.destroy', [$post->blogPost_id, $comment->blogComment_id]) }}"
                                              method="POST"
                                              onsubmit="return confirmDeleteComment(this)">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fas fa-trash me-2"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Content --}}
                        <div id="comment-content-{{ $comment->blogComment_id }}"
                             class="comment-body-bubble">
                            {!! nl2br(e($comment->content)) !!}
                        </div>

                        {{-- Edit form --}}
                        @if ($isOwner)
                            <form id="comment-edit-{{ $comment->blogComment_id }}"
                                  action="{{ route('blogs.comments.update', [$post->blogPost_id, $comment->blogComment_id]) }}"
                                  method="POST"
                                  class="edit-comment-form"
                                  style="display:none; margin-top:.5rem;">
                                @csrf
                                @method('PUT')

                                <textarea name="content"
                                          rows="3"
                                          class="form-control">{{ old('content', $comment->content) }}</textarea>

                                <div class="mt-2 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                    <button type="button"
                                            class="btn btn-outline-secondary btn-sm"
                                            onclick="toggleEdit('{{ $comment->blogComment_id }}')">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        @endif

                        {{-- Sentiment --}}
                        @if ($sentLabel)
                            <div class="sentiment-row mt-2">
                                <span class="sentiment-badge {{ e($sentClass) }}">
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

    {{-- Pagination --}}
    @if ($comments->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $comments->withQueryString()->fragment('comments')->links('pagination::bootstrap-5') }}
        </div>
    @endif

</div>



@push('scripts')
<script>
/* ===============================
   MENU & EDIT TOGGLES (GLOBAL)
================================ */

document.addEventListener('click', function (e) {
    if (!e.target.closest('.position-relative')) closeAllMenus();
});

function toggleCommentMenu(commentId, e) {
    if (e) e.stopPropagation();
    closeAllMenus();
    const menu = document.getElementById('commentMenu' + commentId);
    if (menu) menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
}

function closeAllMenus() {
    document.querySelectorAll('.comment-menu-dropdown')
        .forEach(m => m.style.display = 'none');
}

function toggleEdit(commentId) {
    const editForm   = document.getElementById('comment-edit-' + commentId);
    const contentDiv = document.getElementById('comment-content-' + commentId);
    if (!editForm || !contentDiv) return;

    // Close others
    document.querySelectorAll('.edit-comment-form').forEach(f => f.style.display = 'none');
    document.querySelectorAll('[id^="comment-content-"]').forEach(d => d.style.display = 'block');

    const open = editForm.style.display === 'block';
    editForm.style.display   = open ? 'none' : 'block';
    contentDiv.style.display = open ? 'block' : 'none';

    if (!open) {
        const ta = editForm.querySelector('textarea');
        if (ta) {
            ta.focus();
            ta.selectionStart = ta.selectionEnd = ta.value.length;
        }
    }

    closeAllMenus();
}

function confirmDeleteComment() {
    return confirm('Are you sure you want to delete this comment?');
}

/* ===============================
   AJAX COMMENTS (CREATE / EDIT / DELETE / PAGINATION)
================================ */

(function () {
    const containerSel = '#comments';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    function extractComments(html) {
        const doc = new DOMParser().parseFromString(html, 'text/html');
        return doc.querySelector(containerSel)?.innerHTML ?? null;
    }

    async function ajaxRequest(url, options = {}) {
        options.headers = options.headers || {};
        options.headers['X-Requested-With'] = 'XMLHttpRequest';
        if (csrfToken) options.headers['X-CSRF-TOKEN'] = csrfToken;

        const res = await fetch(url, options);
        const text = await res.text();

        if (!res.ok) throw text;
        return text;
    }

    async function handleForm(form) {
        const methodInput = form.querySelector('input[name="_method"]');
        const method = (methodInput?.value || form.method || 'POST').toUpperCase();

        if (method === 'DELETE' && !confirmDeleteComment()) return;

        const btn = form.querySelector('[type="submit"]');
        const btnHtml = btn?.innerHTML;
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        }

        try {
            const html = await ajaxRequest(form.action, {
                method: 'POST',
                body: new FormData(form)
            });

            const frag = extractComments(html);
            if (frag) {
                document.querySelector(containerSel).innerHTML = frag;
            }
        } catch (err) {
            console.error(err);
            alert('An error occurred. Please try again.');
        } finally {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = btnHtml;
            }
        }
    }

    /* FORM SUBMIT (POST / PUT / DELETE) */
    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (!form.closest(containerSel)) return;
        e.preventDefault();
        handleForm(form);
    });

    /* PAGINATION LINKS */
    document.addEventListener('click', function (e) {
        const link = e.target.closest('.pagination a');
        if (!link || !link.closest(containerSel)) return;

        e.preventDefault();

        ajaxRequest(link.href)
            .then(html => {
                const frag = extractComments(html);
                if (frag) {
                    document.querySelector(containerSel).innerHTML = frag;
                    document.getElementById('comments')
                        ?.scrollIntoView({ behavior: 'smooth' });
                }
            })
            .catch(err => {
                console.error(err);
                alert('Failed to load comments.');
            });
    });
})();
</script>
@endpush

