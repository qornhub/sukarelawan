{{-- resources/views/ngo/events/email.blade.php --}}
@php
    $ngoName = optional(auth()->user())->name ?? ($event->organizer->name ?? 'NGO');
    $ngoEmail = optional(auth()->user())->email ?? ($event->organizer->email ?? '');
    // Build simple arrays of participants: email + name + id (if exists)
    $registeredList = $registered ?? collect();
    $confirmedList = $confirmed ?? collect();
    $rejectedList = $rejected ?? collect();
    // attended placeholder (if you track attendances elsewhere, replace this)
    $attendedList = $event->attendances ?? collect(); // fallback: likely empty
@endphp

<div id="section-email" class="participant-section" style="display:none;">

    
    <div class="card mt-4">
        <div class="card-body">
            <h4 class="mb-3">Email Participants</h4>
            @include('layouts.messages')
            <form id="emailForm" method="POST" action="{{ route('ngo.events.email.send', $event->event_id) }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input class="form-control" type="text" name="from_name" value="{{ $ngoName }}" readonly>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">From</label>
                        <input class="form-control" type="email" name="from_email" value="{{ $ngoEmail }}"
                            required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">To (direct email addresses)</label>
                        <input id="toManual" class="form-control" placeholder="comma separated emails (optional)">
                        <div class="form-text">Or select recipients below.</div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Or Select</label>
                    <div class="d-flex flex-wrap gap-2 mb-2">
                        <button type="button" class="btn btn-outline-primary btn-sm select-group"
                            data-group="registered">All Registered Participants</button>
                        <button type="button" class="btn btn-outline-primary btn-sm select-group"
                            data-group="rejected">Rejected Participants</button>
                        <button type="button" class="btn btn-outline-primary btn-sm select-group"
                            data-group="confirmed">Confirmed Participants</button>
                        <button type="button" class="btn btn-outline-primary btn-sm select-group"
                            data-group="attended">Attended Participants</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="clearSelection">Clear
                            Selection</button>
                    </div>
                    <div class="form-text">Use the buttons to quickly select common groups.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Selected Recipients</label>
                    <div id="selectedChips" class="d-flex flex-wrap gap-2 mb-2"></div>
                    <div class="text-muted small">Selected: <span id="selectedCount">0</span></div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Pick Individuals</label>
                    <div class="participant-list"
                        style="max-height:220px; overflow:auto; border:1px solid #eee; border-radius:6px; padding:8px;">
                        {{-- Registered --}}
                        @if ($registeredList->count())
                            <div class="fw-semibold mb-1">Registered</div>
                            @foreach ($registeredList as $p)
                                @php
                                    $uid = $p->registration_id ?? ($p->user_id ?? null);
                                    $email = $p->email ?? ($p->user->email ?? null);
                                    $name = $p->name ?? ($p->user->name ?? 'Participant');
                                @endphp
                                <label class="d-flex align-items-center gap-2 mb-1 participant-row">
                                    <input type="checkbox" class="form-check-input participant-checkbox"
                                        data-email="{{ $email }}" data-id="{{ $uid }}"
                                        data-group="registered" />
                                    <div class="flex-grow-1">
                                        <div style="font-weight:600">{{ $name }}</div>
                                        <div class="text-muted small">{{ $email }}</div>
                                    </div>
                                </label>
                            @endforeach
                        @endif

                        {{-- Confirmed --}}
                        @if ($confirmedList->count())
                            <hr />
                            <div class="fw-semibold mb-1">Confirmed</div>
                            @foreach ($confirmedList as $p)
                                @php
                                    $uid = $p->id ?? ($p->user_id ?? null);
                                    $email = $p->email ?? null;
                                    $name = $p->name ?? 'Participant';
                                @endphp
                                <label class="d-flex align-items-center gap-2 mb-1 participant-row">
                                    <input type="checkbox" class="form-check-input participant-checkbox"
                                        data-email="{{ $email }}" data-id="{{ $uid }}"
                                        data-group="confirmed" />
                                    <div class="flex-grow-1">
                                        <div style="font-weight:600">{{ $name }}</div>
                                        <div class="text-muted small">{{ $email }}</div>
                                    </div>
                                </label>
                            @endforeach
                        @endif

                        {{-- Rejected --}}
                        @if ($rejectedList->count())
                            <hr />
                            <div class="fw-semibold mb-1">Rejected</div>
                            @foreach ($rejectedList as $p)
                                @php
                                    $uid = $p->registration_id ?? ($p->user_id ?? null);
                                    $email = $p->email ?? null;
                                    $name = $p->name ?? ($p->user->name ?? 'Participant');
                                @endphp
                                <label class="d-flex align-items-center gap-2 mb-1 participant-row">
                                    <input type="checkbox" class="form-check-input participant-checkbox"
                                        data-email="{{ $email }}" data-id="{{ $uid }}"
                                        data-group="rejected" />
                                    <div class="flex-grow-1">
                                        <div style="font-weight:600">{{ $name }}</div>
                                        <div class="text-muted small">{{ $email }}</div>
                                    </div>
                                </label>
                            @endforeach
                        @endif

                        {{-- Attended - fallback empty if not provided --}}
                        @if (isset($attendedList) && $attendedList->count())
                            <hr />
                            <div class="fw-semibold mb-1">Attended</div>
                            @foreach ($attendedList as $p)
                                @php
                                    $uid = $p->id ?? ($p->user_id ?? null);
                                    $email = $p->email ?? null;
                                    $name = $p->name ?? ($p->user->name ?? 'Participant');
                                @endphp
                                <label class="d-flex align-items-center gap-2 mb-1 participant-row">
                                    <input type="checkbox" class="form-check-input participant-checkbox"
                                        data-email="{{ $email }}" data-id="{{ $uid }}"
                                        data-group="attended" />
                                    <div class="flex-grow-1">
                                        <div style="font-weight:600">{{ $name }}</div>
                                        <div class="text-muted small">{{ $email }}</div>
                                    </div>
                                </label>
                            @endforeach
                        @endif

                        @if (!$registeredList->count() && !$confirmedList->count() && !$rejectedList->count())
                            <div class="text-muted small p-2">No participants available for this event.</div>
                        @endif
                    </div>
                </div>

                <input type="hidden" name="recipient_emails" id="recipient_emails" value="">
                <input type="hidden" name="recipient_user_ids" id="recipient_user_ids" value="">

                <div class="mb-3">
                    <label class="form-label">Subject</label>
                    <input class="form-control" name="subject" placeholder="Subject" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Message</label>
                
                    <textarea id="messageEditor" name="message" class="form-control" required></textarea>

                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">Tip: You can manually add comma-separated emails in the "To" field as
                        well.</div>
                    <div>
                        <button type="submit" class="btn btn-success" id="sendBtn">Send</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- TinyMCE CDN for rich editor (optional) --}}




@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ---------------- TinyMCE safety (unchanged) ----------------
    if (typeof tinymce !== 'undefined') {
        try {
            tinymce.init({
                selector: '#messageEditor',
                height: 300,
                menubar: false,
                plugins: 'link lists table code',
                toolbar: 'undo redo | bold italic underline | bullist numlist | alignleft aligncenter alignright | link | code',
                setup: function (editor) {
                    editor.on('change', function () { tinymce.triggerSave(); });
                }
            });
        } catch (e) {
            console.warn('[email] tinymce init failed', e);
        }
    }

    // ---------------- helpers ----------------
    const toS = v => v == null ? '' : String(v).trim();
    const norm = s => toS(s).toLowerCase();
    const unique = arr => Array.from(new Set(arr.map(toS))).filter(Boolean);
    const isEmail = s => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(s).trim());

    function parseManualEmails(str) {
        if (!str) return [];
        return String(str).split(',')
            .map(s => s.trim())
            .filter(Boolean)
            .map(s => s.replace(/\s+/g, ''))
            .filter(isEmail);
    }

    // ---------------- elements ----------------
    const emailForm = document.getElementById('emailForm');
    const selectedChips = document.getElementById('selectedChips');
    const selectedCount = document.getElementById('selectedCount');
    const recipientEmails = document.getElementById('recipient_emails');
    const recipientUserIds = document.getElementById('recipient_user_ids');
    const toManual = document.getElementById('toManual');
    const clearBtn = document.getElementById('clearSelection');
    const sendBtn = document.getElementById('sendBtn');

    if (!emailForm || !selectedChips || !selectedCount || !recipientEmails || !recipientUserIds) {
        // required DOM not present — bail
        return;
    }

    function getCheckboxes() {
        return Array.from(document.querySelectorAll('.participant-checkbox') || []);
    }

    // ---------------- core refresh function ----------------
    function refreshRecipients() {
        const checkboxes = getCheckboxes();
        const emails = [];
        const userIds = [];

        checkboxes.forEach(cb => {
            try {
                if (cb.checked) {
                    const em = toS(cb.dataset.email);
                    const id = toS(cb.dataset.id);
                    if (em && isEmail(em)) emails.push(em);
                    if (id) userIds.push(id);
                }
            } catch (e) { /* ignore malformed */ }
        });

        // manual emails (validated)
        const manual = toS(toManual?.value || '');
        parseManualEmails(manual).forEach(e => emails.push(e));

        const uniqueEmails = Array.from(new Set(emails.map(e => toS(e)))).filter(Boolean);
        const uniqueUserIds = unique(userIds);

        // render chips
        selectedChips.innerHTML = '';

        if (!uniqueEmails.length) {
            const ph = document.createElement('div');
            ph.className = 'selected-placeholder text-muted';
            ph.textContent = 'No recipients selected';
            selectedChips.appendChild(ph);
        } else {
            uniqueEmails.forEach(e => {
                const emailNormalized = toS(e);
                const chip = document.createElement('div');
                chip.className = 'chip';
                chip.setAttribute('data-email', emailNormalized);

                const label = document.createElement('span');
                label.className = 'chip-label';
                label.textContent = emailNormalized;
                label.title = emailNormalized;

                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'chip-remove';
                btn.setAttribute('aria-label', 'Remove ' + emailNormalized);
                btn.innerHTML = '&times;';

                btn.addEventListener('click', function (evt) {
                    evt.stopPropagation();
                    // uncheck any checkbox with matching email (case-insensitive)
                    const boxes = getCheckboxes();
                    let removedFromCheckbox = false;
                    boxes.forEach(cb => {
                        try {
                            if (norm(cb.dataset.email) === norm(emailNormalized)) {
                                cb.checked = false;
                                removedFromCheckbox = true;
                            }
                        } catch (e) {}
                    });

                    if (!removedFromCheckbox && toManual) {
                        // remove from manual input if it exists there
                        const remaining = parseManualEmails(toManual.value).filter(x => norm(x) !== norm(emailNormalized));
                        toManual.value = remaining.join(', ');
                    }

                    // re-render
                    refreshRecipients();
                });

                chip.appendChild(label);
                chip.appendChild(btn);
                selectedChips.appendChild(chip);
            });
        }

        selectedCount.textContent = uniqueEmails.length;
        recipientEmails.value = uniqueEmails.join(',');
        recipientUserIds.value = uniqueUserIds.join(',');
    }

    // ---------------- group button behaviour ----------------
    function showAlert(message) {
        // simple alert fallback; you can replace with prettier toast
        try {
            // use Bootstrap toast if available later; for now simple
            alert(message);
        } catch (e) {
            console.warn('[email] alert failed', e);
        }
    }

    document.querySelectorAll('.select-group').forEach(btn => {
        btn.addEventListener('click', function (ev) {
            ev.preventDefault();
            const group = this.dataset.group;
            const boxes = getCheckboxes().filter(cb => toS(cb.dataset.group) === toS(group));

            if (!boxes.length) {
                // nothing to select — show message and do not toggle or focus
                showAlert(`No participants found for "${group}".`);
                // ensure visual pressed state removed
                this.classList.remove('active');
                this.setAttribute('aria-pressed', 'false');
                this.blur();
                return;
            }

            // toggle: if any in group currently unchecked -> check ALL; otherwise uncheck ALL
            const anyUnchecked = boxes.some(cb => !cb.checked);
            boxes.forEach(cb => cb.checked = anyUnchecked);

            // update button visual + accessibility
            if (anyUnchecked) {
                this.classList.add('active');
                this.setAttribute('aria-pressed', 'true');
            } else {
                this.classList.remove('active');
                this.setAttribute('aria-pressed', 'false');
            }

            // remove focus (prevents persistent blue focus outline)
            this.blur();

            refreshRecipients();
        });
    });

    // ---------------- clear selection ----------------
    if (clearBtn) {
        clearBtn.addEventListener('click', function (ev) {
            ev.preventDefault();
            getCheckboxes().forEach(cb => cb.checked = false);
            if (toManual) toManual.value = '';
            refreshRecipients();
            document.querySelectorAll('.select-group').forEach(b => {
                b.classList.remove('active');
                b.setAttribute('aria-pressed', 'false');
            });
            if (clearBtn.blur) clearBtn.blur();
        });
    }

    // ---------------- listen for checkbox changes (delegated) ----------------
    document.addEventListener('change', function (e) {
        if (e.target && e.target.matches && e.target.matches('.participant-checkbox')) {
            // keep the group button visual state in sync:
            const group = toS(e.target.dataset.group || '');
            if (group) {
                const btn = Array.from(document.querySelectorAll('.select-group')).find(x => toS(x.dataset.group) === group);
                if (btn) {
                    const boxes = getCheckboxes().filter(cb => toS(cb.dataset.group) === group);
                    const anyChecked = boxes.some(cb => cb.checked);
                    btn.classList.toggle('active', anyChecked);
                    btn.setAttribute('aria-pressed', anyChecked ? 'true' : 'false');
                }
            }
            refreshRecipients();
        }
    });

    // ---------------- form submit ----------------
    emailForm.addEventListener('submit', function (e) {
        if (typeof tinymce !== 'undefined') {
            try { tinymce.triggerSave(); } catch (er) { }
        }

        refreshRecipients();

        if (!recipientEmails.value) {
            e.preventDefault();
            showAlert('Please select at least one valid recipient (checkboxes or manual "To" field).');
            return false;
        }

        if (sendBtn) {
            sendBtn.disabled = true;
            sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Sending…';
        }
    });

    // initial render
    refreshRecipients();
});
</script>
@endpush



