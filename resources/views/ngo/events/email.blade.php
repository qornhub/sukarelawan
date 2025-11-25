{{-- resources/views/ngo/events/email.blade.php --}}

@php
    $ngoName  = optional(auth()->user())->name ?? ($event->organizer->name ?? 'NGO');
    $ngoEmail = optional(auth()->user())->email ?? ($event->organizer->email ?? '');

    $registeredList = $registered ?? collect();
    $confirmedList  = $confirmed ?? collect();
    $rejectedList   = $rejected ?? collect();
    $attendedList   = $attendedList ?? collect();
@endphp

<div id="section-email" class="participant-section" style="display:none;">

    <div class="card mt-4">
        <div class="card-body">

            <h4 class="mb-3">Email Participants</h4>
            @include('layouts.messages')

            <form id="emailForm"
                  method="POST"
                  action="{{ route('ngo.events.email.send', $event->event_id) }}">
                @csrf

                {{-- SENDER INFO --}}
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input class="form-control" type="text" name="from_name"
                           value="{{ $ngoName }}" readonly>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">From</label>
                        <input class="form-control" type="email" name="from_email"
                               value="{{ $ngoEmail }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">To (direct email addresses)</label>
                        <input id="toManual" class="form-control"
                               placeholder="comma separated emails (optional)">
                        <div class="form-text">Or select recipients below.</div>
                    </div>
                </div>

                {{-- GROUP SELECT BUTTONS --}}
                <div class="mb-3">
                    <label class="form-label">Or Select</label>
                    <div class="d-flex flex-wrap gap-2 mb-2">

                        <button type="button" class="btn btn-outline-primary btn-sm select-group"
                            data-group="registered">All Registered Participants</button>

                        <button type="button" class="btn btn-outline-primary btn-sm select-group"
                            data-group="confirmed">Confirmed Participants</button>

                        <button type="button" class="btn btn-outline-primary btn-sm select-group"
                            data-group="rejected">Rejected Participants</button>

                        <button type="button" class="btn btn-outline-primary btn-sm select-group"
                            data-group="attended">Attended Participants</button>

                        <button type="button" class="btn btn-outline-secondary btn-sm"
                                id="clearSelection">Clear Selection</button>
                    </div>

                    <div class="form-text">Use the buttons to quickly select common groups.</div>
                </div>

                {{-- SELECTED CHIPS --}}
                <div class="mb-3">
                    <label class="form-label">Selected Recipients</label>
                    <div id="selectedChips"
                         class="d-flex flex-wrap gap-2 mb-2"></div>
                    <div class="text-muted small">Selected: <span id="selectedCount">0</span></div>
                </div>

                {{-- PARTICIPANT LIST --}}
                <div class="mb-3">
                    <label class="form-label">Pick Individuals</label>

                    <div class="participant-list"
                         style="max-height:220px; overflow:auto; border:1px solid #eee; border-radius:6px; padding:8px;">

                        {{-- REGISTERED --}}
                        <div id="email-registered-wrapper">
                            @if ($registeredList->count())
                                <div class="fw-semibold mb-1">Registered</div>
                                <div id="email-registered-list">
                                    @foreach ($registeredList as $p)
                                        @php
                                            $uid = $p->registration_id ?? $p->user_id;
                                            $email = $p->email;
                                            $name  = $p->name;
                                        @endphp

                                        <label class="d-flex align-items-center gap-2 mb-1 participant-row"
                                               data-id="{{ $uid }}">
                                            <input type="checkbox"
                                                   class="form-check-input participant-checkbox"
                                                   data-email="{{ $email }}"
                                                   data-id="{{ $uid }}"
                                                   data-group="registered">
                                            <div class="flex-grow-1">
                                                <div style="font-weight:600">{{ $name }}</div>
                                                <div class="text-muted small">{{ $email }}</div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <div id="email-registered-list"></div>
                            @endif
                        </div>

                        {{-- CONFIRMED --}}
                        <div id="email-confirmed-wrapper">
                            @if ($confirmedList->count())
                                <hr>
                                <div class="fw-semibold mb-1">Confirmed</div>
                                <div id="email-confirmed-list">
                                    @foreach ($confirmedList as $p)
                                        @php
                                            $uid   = $p->registration_id ?? $p->user_id;
                                            $email = $p->email;
                                            $name  = $p->name;
                                        @endphp

                                        <label class="d-flex align-items-center gap-2 mb-1 participant-row"
                                               data-id="{{ $uid }}">
                                            <input type="checkbox"
                                                   class="form-check-input participant-checkbox"
                                                   data-email="{{ $email }}"
                                                   data-id="{{ $uid }}"
                                                   data-group="confirmed">
                                            <div class="flex-grow-1">
                                                <div style="font-weight:600">{{ $name }}</div>
                                                <div class="text-muted small">{{ $email }}</div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <div id="email-confirmed-list"></div>
                            @endif
                        </div>

                        {{-- REJECTED --}}
                        <div id="email-rejected-wrapper">
                            @if ($rejectedList->count())
                                <hr>
                                <div class="fw-semibold mb-1">Rejected</div>
                                <div id="email-rejected-list">
                                    @foreach ($rejectedList as $p)
                                        @php
                                            $uid   = $p->registration_id ?? $p->user_id;
                                            $email = $p->email;
                                            $name  = $p->name;
                                        @endphp

                                        <label class="d-flex align-items-center gap-2 mb-1 participant-row"
                                               data-id="{{ $uid }}">
                                            <input type="checkbox"
                                                   class="form-check-input participant-checkbox"
                                                   data-email="{{ $email }}"
                                                   data-id="{{ $uid }}"
                                                   data-group="rejected">
                                            <div class="flex-grow-1">
                                                <div style="font-weight:600">{{ $name }}</div>
                                                <div class="text-muted small">{{ $email }}</div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <div id="email-rejected-list"></div>
                            @endif
                        </div>

                        {{-- ATTENDED --}}
                        <div id="email-attended-wrapper">
                            @if ($attendedList->count())
                                <hr>
                                <div class="fw-semibold mb-1">Attended</div>
                                <div id="email-attended-list">
                                    @foreach ($attendedList as $a)
                                        @php
                                            $user  = $a->user;
                                            $uid   = $user->id ?? null;
                                            $email = $user->email ?? null;
                                            $name  = $user->name ?? 'Participant';
                                        @endphp

                                        <label class="d-flex align-items-center gap-2 mb-1 participant-row"
                                               data-id="{{ $uid }}">
                                            <input type="checkbox"
                                                   class="form-check-input participant-checkbox"
                                                   data-email="{{ $email }}"
                                                   data-id="{{ $uid }}"
                                                   data-group="attended">
                                            <div class="flex-grow-1">
                                                <div style="font-weight:600">{{ $name }}</div>
                                                <div class="text-muted small">{{ $email }}</div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <div id="email-attended-list"></div>
                            @endif
                        </div>

                        {{-- EMPTY STATE FOR ALL --}}
                        @if (
                            !$registeredList->count() &&
                            !$confirmedList->count() &&
                            !$rejectedList->count() &&
                            !$attendedList->count()
                        )
                            <div class="text-muted small p-2">
                                No participants available for this event.
                            </div>
                        @endif

                    </div>
                </div>

                {{-- Hidden inputs for final values --}}
                <input type="hidden" name="recipient_emails" id="recipient_emails">
                <input type="hidden" name="recipient_user_ids" id="recipient_user_ids">

                <div class="mb-3">
                    <label class="form-label">Subject</label>
                    <input class="form-control" name="subject" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Message</label>
                    <textarea id="messageEditor" name="message" class="form-control" required></textarea>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Tip: You can manually add comma-separated emails in the "To" field as well.
                    </div>

                    <button type="submit" class="btn btn-success" id="sendBtn">
                        Send
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // =====================================================================
    //  TINYMCE INITIALIZATION
    // =====================================================================
    if (typeof tinymce !== 'undefined') {
        try {
            tinymce.init({
                selector: '#messageEditor',
                height: 300,
                menubar: false,
                plugins: 'link lists table code',
                toolbar:
                    'undo redo | bold italic underline | ' +
                    'bullist numlist | alignleft aligncenter alignright | link | code',
                setup: function (editor) {
                    editor.on('change', function () {
                        tinymce.triggerSave();
                    });
                }
            });
        } catch (e) {
            console.warn('[email] tinymce init failed', e);
        }
    }

    // =====================================================================
    //   SMALL HELPER FUNCTIONS
    // =====================================================================
    const toS     = v => v == null ? '' : String(v).trim();
    const norm    = s => toS(s).toLowerCase();
    const isEmail = s => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(s).trim());
    const unique  = arr => Array.from(new Set(arr.map(toS))).filter(Boolean);

    function parseManualEmails(str) {
        if (!str) return [];
        return String(str)
            .split(',')
            .map(s => s.trim())
            .filter(Boolean)
            .filter(isEmail);
    }

    // =====================================================================
    //   DOM SHORTCUTS
    // =====================================================================
    const selectedChips     = document.getElementById('selectedChips');
    const selectedCount     = document.getElementById('selectedCount');
    const recipientEmails   = document.getElementById('recipient_emails');
    const recipientUserIds  = document.getElementById('recipient_user_ids');
    const toManual          = document.getElementById('toManual');
    const clearBtn          = document.getElementById('clearSelection');
    const sendBtn           = document.getElementById('sendBtn');
    const emailForm         = document.getElementById('emailForm');
    const emailSection      = document.getElementById('section-email');

    function getCheckboxes() {
        return Array.from(document.querySelectorAll('.participant-checkbox'));
    }

    // =====================================================================
    //   FLASH / ALERT HELPERS  (AUTO-HIDE AFTER 5s)
    // =====================================================================
    function autoHideAlertsIn(container) {
        if (!container) return;
        const alerts = container.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                // simple fade-out then remove
                alert.style.transition = 'opacity 0.4s ease';
                alert.style.opacity = '0';
                setTimeout(() => {
                    if (alert && alert.parentNode) {
                        alert.parentNode.removeChild(alert);
                    }
                }, 450);
            }, 5000);
        });
    }

    // run once for any server-side flash messages from layouts.messages
    autoHideAlertsIn(emailSection);

    function showInlineAlert(type, text) {
        if (!text) return;
        const container = emailSection
            ? emailSection.querySelector('.card-body') || emailSection
            : document.body;

        const alert = document.createElement('div');
        alert.className = 'alert alert-' + (type === 'success' ? 'success' : 'danger');
        alert.textContent = text;

        container.insertBefore(alert, container.firstChild);
        autoHideAlertsIn(container);
    }

    // =====================================================================
    //   REFRESH SELECTED RECIPIENTS (CHIPS + HIDDEN INPUTS)
    // =====================================================================
    function refreshRecipients() {
        const boxes = getCheckboxes();
        const emails = [];
        const userIds = [];

        boxes.forEach(cb => {
            if (cb.checked) {
                const em = toS(cb.dataset.email);
                const id = toS(cb.dataset.id);

                if (em && isEmail(em)) emails.push(em);
                if (id) userIds.push(id);
            }
        });

        // Manual "To" field
        parseManualEmails(toManual.value).forEach(email => emails.push(email));

        const uniqueEmails = unique(emails);
        const uniqueIds    = unique(userIds);

        // Render chips
        selectedChips.innerHTML = '';

        if (!uniqueEmails.length) {
            const ph = document.createElement('div');
            ph.className = 'selected-placeholder text-muted';
            ph.textContent = 'No recipients selected';
            selectedChips.appendChild(ph);
        } else {
            uniqueEmails.forEach(email => {
                const chip = document.createElement('div');
                chip.className = 'chip';
                chip.setAttribute('data-email', email);

                chip.innerHTML = `
                    <span class="chip-label">${email}</span>
                    <button type="button" class="chip-remove" aria-label="Remove ${email}">&times;</button>
                `;

                chip.querySelector('.chip-remove').addEventListener('click', e => {
                    e.stopPropagation();

                    // Uncheck checkbox if exists
                    let found = false;
                    getCheckboxes().forEach(cb => {
                        if (norm(cb.dataset.email) === norm(email)) {
                            cb.checked = false;
                            found = true;
                        }
                    });

                    // Remove from manual field if needed
                    if (!found) {
                        const remaining = parseManualEmails(toManual.value)
                            .filter(x => norm(x) !== norm(email));
                        toManual.value = remaining.join(', ');
                    }

                    refreshRecipients();
                });

                selectedChips.appendChild(chip);
            });
        }

        selectedCount.textContent = uniqueEmails.length;
        recipientEmails.value      = uniqueEmails.join(',');
        recipientUserIds.value     = uniqueIds.join(',');
    }

    // =====================================================================
    //  DYNAMIC UPDATE HELPERS (CALLED FROM APPROVE/REJECT JS)
    // =====================================================================
    // NOTE: we use registration_id as primary id if possible, fallback to user_id.
    window.addEmailRecipient = function(vol, group) {
        if (!vol || !group) return;

        const list = document.getElementById(`email-${group}-list`);
        if (!list) return;

        const id    = toS(vol.registration_id || vol.user_id);
        const email = toS(vol.email);
        const name  = toS(vol.name || 'Participant');

        if (!id || !email) return;

        // Avoid duplicate in this group
        if (list.querySelector(`[data-id="${id}"]`)) {
            return;
        }

        const label = document.createElement('label');
        label.className = 'd-flex align-items-center gap-2 mb-1 participant-row';
        label.setAttribute('data-id', id);

        label.innerHTML = `
            <input type="checkbox" class="form-check-input participant-checkbox"
                data-email="${email}" data-id="${id}" data-group="${group}">
            <div class="flex-grow-1">
                <div style="font-weight:600">${name}</div>
                <div class="text-muted small">${email}</div>
            </div>
        `;

        list.appendChild(label);

        // Recalculate chips if any of these were already selected
        refreshRecipients();
    };

    window.removeEmailRecipient = function(vol, group) {
        if (!vol || !group) return;

        const list = document.getElementById(`email-${group}-list`);
        if (!list) return;

        const id = toS(vol.registration_id || vol.user_id);
        if (!id) return;

        const row = list.querySelector(`[data-id="${id}"]`);
        if (row) row.remove();

        refreshRecipients();
    };

    // =====================================================================
    //  GROUP SELECTION BUTTONS
    // =====================================================================
    document.querySelectorAll('.select-group').forEach(btn => {
        btn.addEventListener('click', e => {
            e.preventDefault();
            const group = toS(btn.dataset.group);
            const boxes = getCheckboxes().filter(cb => cb.dataset.group === group);

            if (!boxes.length) {
                alert(`No participants found for "${group}".`);
                btn.classList.remove('active');
                btn.setAttribute('aria-pressed', 'false');
                return;
            }

            // Toggle logic
            const anyUnchecked = boxes.some(cb => !cb.checked);
            boxes.forEach(cb => cb.checked = anyUnchecked);

            btn.classList.toggle('active', anyUnchecked);
            btn.setAttribute('aria-pressed', anyUnchecked ? 'true' : 'false');

            btn.blur();
            refreshRecipients();
        });
    });

    // =====================================================================
    //  CLEAR SELECTION
    // =====================================================================
    if (clearBtn) {
        clearBtn.addEventListener('click', function(e) {
            e.preventDefault();
            getCheckboxes().forEach(cb => cb.checked = false);
            toManual.value = '';
            refreshRecipients();

            document.querySelectorAll('.select-group').forEach(b => {
                b.classList.remove('active');
                b.setAttribute('aria-pressed', 'false');
            });
        });
    }

    // =====================================================================
    //  CHECKBOX CHANGES
    // =====================================================================
    document.addEventListener('change', function(e) {
        if (e.target.matches('.participant-checkbox')) {
            const group = e.target.dataset.group;

            // Sync group button state
            const btn = document.querySelector(`.select-group[data-group="${group}"]`);
            if (btn) {
                const boxes = getCheckboxes().filter(cb => cb.dataset.group === group);
                const anyChecked = boxes.some(cb => cb.checked);
                btn.classList.toggle('active', anyChecked);
                btn.setAttribute('aria-pressed', anyChecked ? 'true' : 'false');
            }

            refreshRecipients();
        }
    });

    // =====================================================================
    //  FORM SUBMIT  (AJAX, NO PAGE RELOAD)
    // =====================================================================
    if (emailForm) {
        emailForm.addEventListener('submit', async function(e) {
            // always prevent default so page doesn't reload
            e.preventDefault();

            if (typeof tinymce !== 'undefined') {
                try { tinymce.triggerSave(); } catch (err) {}
            }

            refreshRecipients();

            if (!recipientEmails.value) {
                alert('Please select at least one valid recipient (checkboxes or manual "To" field).');
                return false;
            }

            if (sendBtn) {
                sendBtn.disabled = true;
                sendBtn.innerHTML =
                    '<i class="fas fa-spinner fa-spin me-2"></i> Sending…';
            }

            // ---- AJAX POST ----
            const url      = emailForm.action;
            const formData = new FormData(emailForm);
            const token    = document.querySelector('meta[name="csrf-token"]')
                                ?.getAttribute('content') || '';

            try {
                const resp = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': token
                        // NOTE: do NOT set Content-Type; browser will set for FormData
                    },
                    body: formData
                });

                let ok = resp.ok;
                let message = '';

                // Try JSON first
                try {
                    const data = await resp.clone().json();
                    message = data.message || data.status || '';
                } catch (jsonErr) {
                    // Fallback to plain text
                    const text = await resp.text();
                    // use full text only if short; otherwise generic
                    message = text && text.length < 300
                        ? text
                        : '';
                }

                if (!ok) {
                    showInlineAlert('error', message || 'Failed to send email(s).');
                } else {
                    showInlineAlert('success', message || 'Emails queued for sending.');

                    // reset form + UI state
                    emailForm.reset();
                    getCheckboxes().forEach(cb => cb.checked = false);
                    toManual.value = '';
                    refreshRecipients();

                    if (typeof tinymce !== 'undefined' && tinymce.get('messageEditor')) {
                        tinymce.get('messageEditor').setContent('');
                    }
                }
            } catch (err) {
                console.error('[email] send error', err);
                showInlineAlert('error', 'Network or server error while sending email.');
            } finally {
                if (sendBtn) {
                    sendBtn.disabled = false;
                    sendBtn.innerHTML = 'Send';
                }
            }
        });
    }

    // Initial load
    refreshRecipients();

});
</script>
@endpush

