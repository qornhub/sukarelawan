{{-- resources/views/partials/events/comments.blade.php --}}
@php
    // Expected:
    // - $event (Event model)
    // - $comments (paginator or collection) — comments for this event
    // Optional include overrides:
    // - 'profileRelation' (string) e.g. 'volunteerProfile' or 'ngoProfile' — used to find profile on user
    // - 'profileRoute' (string) route name for profile show (if supplied)
    // - 'profileStoragePath' (string) path prefix for profile images e.g. 'images/profiles/'
    //
    // Determine batch size (per page). Use paginator value if available, otherwise default to 3.
    $perPage = method_exists($comments, 'perPage') ? $comments->perPage() : 3;
    $profileRelation = $profileRelation ?? null;
    $profileRouteOverride = $profileRoute ?? null;
    $profileStoragePath = $profileStoragePath ?? 'images/profiles/';
@endphp

<div id="event-comments" class="mt-4">



    <!-- Comment Form -->
    @auth
        <div class="mb-4">
            <form action="{{ route('events.comments.store', $event->event_id) }}" method="POST"
                class="d-flex align-items-start">
                @csrf
                <input name="content" class="comment-input form-control" placeholder="Type your comment here"
                    aria-label="Type your comment here" />
                <button type="submit" class="comment-publish-btn ms-2 btn btn-primary">Publish</button>
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
                // ownership / admin detection
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

                // per-comment user & profile resolution
                $user = optional($comment->user);
                $profile = null;
                if ($profileRelation && isset($user->{$profileRelation})) {
                    $profile = $user->{$profileRelation};
                } else {
                    // try common relations
                    $profile = $user->volunteerProfile ?? ($user->ngoProfile ?? null);
                }

                $filename = $profile->profilePhoto ?? ($profile->avatar ?? ($profile->photo ?? null));
                $avatarUrl = $filename ? asset($profileStoragePath . $filename) : asset('images/default-profile.png');

                // route to profile (allow override)
                if ($profileRouteOverride && \Illuminate\Support\Facades\Route::has($profileRouteOverride)) {
                    $profileUrl = route($profileRouteOverride, $user->id ?? '#');
                } else {
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
                }

                $displayName = $user->name ?? ($profile->name ?? 'User');

                // Decide whether to hide this comment initially: show first $perPage comments (index 0..perPage-1)
                $hideInitially = $loop->index >= $perPage ? true : false;
            @endphp

            <div id="comment-{{ $comment->eventComment_id }}"
                class="comment-card mb-3 {{ $hideInitially ? 'hidden-comment' : '' }}"
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
                                <h6 class="mb-0">{{ $displayName }}</h6>
                                <small class="text-muted"><i
                                        class="far fa-clock me-1"></i>{{ $comment->created_at->diffForHumans() }}</small>
                            </div>

                            @if ($canManage)
                                <div class="position-relative">
                                    <button class="comment-menu-btn btn btn-link p-0 text-secondary"
                                        style="transform: translateY(-4px); padding:6px;" type="button"
                                        onclick="toggleCommentMenu('{{ $comment->eventComment_id }}', event)">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>


                                    <div id="commentMenu{{ $comment->eventComment_id }}"
                                        class="comment-menu-dropdown bg-white border rounded shadow-sm"
                                        style="display:none; position:absolute; right:0; top:100%; min-width:160px; z-index:2500;">
                                        @if ($isOwner)
                                            <button class="dropdown-item" type="button"
                                                onclick="toggleEdit('{{ $comment->eventComment_id }}'); closeAllMenus();">
                                                <i class="fas fa-edit me-2 text-primary"></i> Edit
                                            </button>
                                        @endif
                                        @if ($isAdmin || $isOwner)
                                            <form
                                                action="{{ route('events.comments.destroy', [$event->event_id, $comment->eventComment_id]) }}"
                                                method="POST" onsubmit="return confirmDeleteEventComment(this);"
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
                        <div id="comment-content-{{ $comment->eventComment_id }}" class="comment-body-bubble">
                            {!! nl2br(e($comment->content)) !!}
                        </div>

                        {{-- edit form (owner only, hidden by default) --}}
                        @if ($isOwner)
                            <form id="comment-edit-{{ $comment->eventComment_id }}" class="edit-comment-form mt-2"
                                data-comment-id="{{ $comment->eventComment_id }}"
                                action="{{ route('events.comments.update', [$event->event_id, $comment->eventComment_id]) }}"
                                method="POST" style="display:none; margin-top:.5rem;">
                                @csrf
                                @method('PUT')
                                <textarea name="content" rows="3" class="form-control edit-content">{{ old('content', $comment->content) }}</textarea>
                                <div class="mt-2 d-flex gap-2">
                                    <button class="btn btn-primary btn-sm" type="submit">Save</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm"
                                        onclick="toggleEdit('{{ $comment->eventComment_id }}')">Cancel</button>
                                </div>
                            </form>
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

    <!-- Load more button holder (client-side reveal). If paginator has more pages, we keep next-page URL as fallback -->
    @php
        $nextPageUrl =
            isset($comments) && method_exists($comments, 'hasMorePages') && $comments->hasMorePages()
                ? request()->fullUrlWithQuery(['event_comments_page' => ($comments->currentPage() ?? 1) + 1])
                : '';
    @endphp

    @if ((isset($comments) && $comments->count() > $perPage) || !empty($nextPageUrl))
        <div id="comments-load-more" data-batch="{{ $perPage }}" data-next-url="{{ $nextPageUrl }}"
            style="text-align: center; margin-top: 8px;">
            <button id="loadMoreBtn" type="button" class="btn btn-link">Load more comments</button>
        </div>
    @endif
</div>

@push('scripts')
    <script>
        /* menu helpers */
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

        /* Toggle edit: show/hide edit form and content (no AJAX) */
        function toggleEdit(commentId) {
            const editForm = document.getElementById('comment-edit-' + commentId);
            const contentDiv = document.getElementById('comment-content-' + commentId);
            if (!editForm) return;

            // Close other open edit forms
            document.querySelectorAll('.edit-comment-form').forEach(f => {
                if (f.id !== 'comment-edit-' + commentId) {
                    f.style.display = 'none';
                }
            });
            document.querySelectorAll('[id^="comment-content-"]').forEach(d => {
                if (d.id !== 'comment-content-' + commentId) {
                    d.style.display = 'block';
                }
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

        /* Confirm delete (regular form submit) */
        function confirmDeleteEventComment(form) {
            return confirm('Are you sure you want to delete this comment?');
        }

        /* Client-side "Load more" reveal logic (no AJAX)
           - Reveals next batch of hidden comments already present in DOM.
           - If none are left but a next-page URL exists (server has more), it navigates to that URL.
        */
        (function() {
            const holder = document.getElementById('comments-load-more');
            if (!holder) return;
            const batch = parseInt(holder.dataset.batch || 3, 10);
            const nextUrl = holder.dataset.nextUrl || '';
            const loadBtn = document.getElementById('loadMoreBtn');

            function revealNextBatch() {
                const container = document.querySelector('#event-comments .comments-list');
                if (!container) return;

                // select only hidden comments inside the comments-list
                const hidden = Array.from(container.querySelectorAll('.hidden-comment'));
                if (hidden.length === 0) {
                    // nothing hidden on this page, fallback to server next page if available
                    if (nextUrl) {
                        window.location.href = nextUrl;
                    } else {
                        // nothing left to show
                        if (loadBtn) loadBtn.style.display = 'none';
                    }
                    return;
                }

                // reveal up to `batch` items
                const toReveal = hidden.slice(0, batch);
                toReveal.forEach(node => {
                    node.classList.remove('hidden-comment');
                    node.style.display = ''; // let CSS determine display
                    node.classList.add('comment-new'); // optional animation class if you have it
                });

                // if no more hidden items and no nextUrl, hide button
                const stillHidden = container.querySelectorAll('.hidden-comment').length;
                if (stillHidden === 0) {
                    if (!nextUrl) {
                        if (loadBtn) loadBtn.style.display = 'none';
                    }
                }
            }

            if (loadBtn) {
                loadBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    revealNextBatch();
                });
            }
        })();
    </script>
@endpush
