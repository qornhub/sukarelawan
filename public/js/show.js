// show.js — single, cleaned-up script (replace your existing file)
(function () {
  // Initialize Bootstrap tooltips
  try {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el); });
  } catch (e) { /* ignore if bootstrap not available yet */ }

  // Use Bootstrap Tab API for switching tabs
  document.querySelectorAll('.nav-tabs .nav-link').forEach(link => {
    link.addEventListener('click', function (e) {
      e.preventDefault();
      new bootstrap.Tab(this).show();
    });
  });

  // Card hover animations
  document.querySelectorAll('.card').forEach(card => {
    card.addEventListener('mouseenter', function () {
      this.style.transform = 'translateY(-5px)';
      this.style.boxShadow = '0 0.5rem 2rem 0 rgba(58, 59, 69, 0.2)';
    });
    card.addEventListener('mouseleave', function () {
      this.style.transform = '';
      this.style.boxShadow = '';
    });
  });

  // Activate correct tab on load when paginating (keeps correct tab open after clicking pagination)
  document.addEventListener('DOMContentLoaded', function () {
    try {
      var url = new URL(window.location.href);
      if (url.searchParams.get('past_page')) {
        var pastTabTrigger = document.querySelector('[data-bs-target="#past"]');
        if (pastTabTrigger) new bootstrap.Tab(pastTabTrigger).show();
      } else if (url.searchParams.get('upcoming_page')) {
        var upTabTrigger = document.querySelector('[data-bs-target="#upcoming"]');
        if (upTabTrigger) new bootstrap.Tab(upTabTrigger).show();
      }
    } catch (err) {
      // ignore URL parsing errors on older browsers
      console.warn('Tab restore skipped:', err);
    }
  });
})();
