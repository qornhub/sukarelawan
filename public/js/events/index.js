/**
 * Initialize all attendance bars on page load or when new events are appended.
 * Call initAttendanceBars() after DOM ready or after AJAX loads new HTML.
 */
function initAttendanceBars(root = document) {
  const nodes = root.querySelectorAll('.event-attendance');
  nodes.forEach(el => {
    const approved = parseInt(el.dataset.approved || '0', 10);
    const max = parseInt(el.dataset.max || '0', 10);
    const textEl = el.querySelector('.attendance-text');
    const barEl = el.querySelector('.attendance-bar');

    const percent = (max > 0) ? Math.min(100, Math.round((approved / max) * 100)) : 0;

    // Update DOM
    if (textEl) textEl.textContent = `${approved}/${max ? max : '∞'}`;
    if (barEl) {
      barEl.style.width = percent + '%';
      barEl.setAttribute('aria-valuenow', percent);
      barEl.setAttribute('title', percent + '%');
    }
  });
}

/**
 * Update a single event attendance bar programmatically.
 * Useful after an AJAX registration/approval or real-time push.
 * @param {string|number} eventId
 * @param {number} approved
 * @param {number|null} max
 */
function updateAttendance(eventId, approved, max = null) {
  if (typeof approved === 'string') approved = parseInt(approved, 10) || 0;
  if (typeof max === 'string') max = parseInt(max, 10) || 0;

  // Find element by data-event-id
  const el = document.querySelector(`.event-attendance[data-event-id="${eventId}"]`);
  if (!el) return false;

  // Update dataset values
  el.dataset.approved = approved;
  if (max !== null) el.dataset.max = max;

  // Re-run the per-element update (so logic stays in one place)
  const textEl = el.querySelector('.attendance-text');
  const barEl = el.querySelector('.attendance-bar');
  const curMax = parseInt(el.dataset.max || '0', 10);

  const percent = (curMax > 0) ? Math.min(100, Math.round((approved / curMax) * 100)) : 0;

  if (textEl) textEl.textContent = `${approved}/${curMax ? curMax : '∞'}`;
  if (barEl) {
    barEl.style.width = percent + '%';
    barEl.setAttribute('aria-valuenow', percent);
    barEl.setAttribute('title', percent + '%');
  }

  return true;
}

/* Auto-init on DOM ready */
document.addEventListener('DOMContentLoaded', function () {
  // Initialize attendance bars for visible events
  initAttendanceBars();

  // === AJAX pagination (View More Events) ===
  const btn = document.querySelector('.view-more-btn');
  const eventList = document.querySelector('#event-list');

  if (!btn || !eventList) return;

  btn.addEventListener('click', function (ev) {
  ev.preventDefault();

  const nextUrl = btn.dataset.nextPage;
  if (!nextUrl) return;

  // disable to prevent double clicks; do NOT change the button text
  btn.disabled = true;
  btn.setAttribute('aria-busy', 'true');

  fetch(nextUrl, {
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'application/json'
    }
  })
  .then(response => {
    if (!response.ok) throw new Error('Network response was not ok');
    return response.json();
  })
  .then(data => {
    if (data.html) {
      // append the new items and initialize only newly-added nodes
      const beforeCount = eventList.querySelectorAll('.event-card').length;
      eventList.insertAdjacentHTML('beforeend', data.html);
      // init attendance bars inside eventList (function safely re-inits existing ones)
      initAttendanceBars(eventList);

      if (data.next_page) {
        // more pages remain — update next page URL and re-enable button
        btn.dataset.nextPage = data.next_page;
        btn.disabled = false;
        btn.removeAttribute('aria-busy');
      } else {
        // no more pages — hide or disable the button
        btn.disabled = true;
        btn.removeAttribute('aria-busy');
        btn.textContent = 'No More Events';
      }
    } else {
      // nothing returned
      btn.disabled = true;
      btn.removeAttribute('aria-busy');
      btn.textContent = 'No More Events';
    }
  })
  .catch(err => {
    // quietly show error in console and re-enable the button so user can retry
    console.error('Failed loading events:', err);
    btn.disabled = false;
    btn.removeAttribute('aria-busy');
  });
});

});
