/* Unified safe script: image preview, skills dropdown, SDG multi-select, tabs.
   Paste this just before </body> and replace your existing combined script.
*/
document.addEventListener("DOMContentLoaded", function () {
    /* ---------------- Image preview (select/remove) ---------------- */
    (function imagePreview() {
        const selectImageBtn = document.getElementById("selectImageBtn");
        const removeImageBtn = document.getElementById("removeImageBtn");
        const fileInput = document.getElementById("event_image");
        const imgPreview = document.getElementById("imgPreview");
        const imgPlaceholder = document.getElementById("imgPlaceholder");

        if (!fileInput) {
            // no image input on this page — skip safely
            // console.info('imagePreview: no #event_image found, skipping image preview init');
            return;
        }

        if (selectImageBtn) {
            selectImageBtn.addEventListener("click", function () {
                fileInput.click();
            });
        }

        fileInput.addEventListener("change", function () {
            if (fileInput.files && fileInput.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    if (imgPreview) {
                        imgPreview.src = e.target.result;
                        imgPreview.classList.remove("d-none");
                    }
                    if (imgPlaceholder) imgPlaceholder.classList.add("d-none");
                    if (removeImageBtn)
                        removeImageBtn.classList.remove("d-none");
                };
                reader.readAsDataURL(fileInput.files[0]);
            }
        });

        if (removeImageBtn) {
            removeImageBtn.addEventListener("click", function () {
                fileInput.value = "";
                if (imgPreview) {
                    imgPreview.src = "";
                    imgPreview.classList.add("d-none");
                }
                if (imgPlaceholder) imgPlaceholder.classList.remove("d-none");
                removeImageBtn.classList.add("d-none");
            });
        }
    })();

    /* ---------------- Skills multi-select dropdown ---------------- */
    (function skillsDropdown() {
        const wrapper = document.getElementById("skillsDropdown");
        if (!wrapper) return;

        const toggle = document.getElementById("skillsToggle");
        const panel = document.getElementById("skillsPanel");
        const doneBtn = document.getElementById("skillsDone");
        const clearBtn = document.getElementById("skillsClear");
        const label = document.getElementById("skillsLabel");
        const countEl = document.getElementById("skillsCount");
        const search = document.getElementById("skillsSearch");

        function checkboxes() {
            return Array.from(wrapper.querySelectorAll(".skill-checkbox"));
        }

        function updateLabel() {
            const selected = checkboxes()
                .filter((cb) => cb.checked)
                .map((cb) => {
                    const sp = cb.nextElementSibling;
                    return sp && sp.textContent ? sp.textContent.trim() : "";
                })
                .filter(Boolean);

            const count = selected.length;
            if (countEl)
                countEl.textContent =
                    count + (count === 1 ? " selected" : " selected");

            if (!label) return;
            if (count === 0) {
                label.textContent = "Select skills";
            } else if (count <= 2) {
                label.textContent = selected.join(", ");
            } else {
                label.textContent =
                    selected.slice(0, 2).join(", ") +
                    " +" +
                    (count - 2) +
                    " more";
            }
        }

        function setPanelSizeAndPosition() {
            if (!panel || !toggle || !wrapper) return;
            const wrapRect = wrapper.getBoundingClientRect();
            const docWidth =
                document.documentElement.clientWidth || window.innerWidth;
            let width = Math.round(wrapRect.width);
            const maxAllowed =
                docWidth - Math.max(16, Math.round(wrapRect.left));
            if (width > maxAllowed) width = Math.max(180, maxAllowed - 8);
            const left = toggle.offsetLeft;
            const top = toggle.offsetTop + toggle.offsetHeight + 8;
            panel.style.position = "absolute";
            panel.style.left = left + "px";
            panel.style.top = top + "px";
            panel.style.width = width + "px";
            panel.style.boxSizing = "border-box";
        }

        function openPanel() {
            setPanelSizeAndPosition();
            panel.classList.remove("d-none");
            const s = panel.querySelector("#skillsSearch");
            if (s) s.focus();
            document.addEventListener("click", outsideClick);
            window.addEventListener("resize", setPanelSizeAndPosition);
        }
        function closePanel() {
            panel.classList.add("d-none");
            document.removeEventListener("click", outsideClick);
            window.removeEventListener("resize", setPanelSizeAndPosition);
        }
        function outsideClick(e) {
            if (!wrapper.contains(e.target)) closePanel();
        }

        if (toggle) {
            toggle.addEventListener("click", function (e) {
                e.stopPropagation();
                if (!panel) return;
                if (panel.classList.contains("d-none")) openPanel();
                else closePanel();
            });

            toggle.addEventListener("keydown", function (e) {
                if (e.key === "Enter" || e.key === " ") {
                    e.preventDefault();
                    if (!panel) return;
                    if (panel.classList.contains("d-none")) openPanel();
                    else closePanel();
                }
            });
        }

        if (doneBtn)
            doneBtn.addEventListener("click", function () {
                closePanel();
            });
        if (clearBtn) {
            clearBtn.addEventListener("click", function (e) {
                e.preventDefault();
                checkboxes().forEach((cb) => (cb.checked = false));
                updateLabel();
            });
        }

        wrapper.addEventListener("change", function (e) {
            if (e.target && e.target.classList.contains("skill-checkbox"))
                updateLabel();
        });

        document.addEventListener("keydown", function (e) {
            if (e.key === "Escape") closePanel();
        });

        if (search) {
            search.addEventListener("input", function () {
                const q = (this.value || "").trim().toLowerCase();
                wrapper.querySelectorAll(".msd-list li").forEach((li) => {
                    const text = li.textContent.trim().toLowerCase();
                    li.style.display = !q || text.includes(q) ? "" : "none";
                });
            });
        }

        if (panel)
            panel.addEventListener("click", function (e) {
                e.stopPropagation();
            });

        updateLabel();
    })();

    /* ---------------- SDG multi-select (robust) ---------------- */
    (function sdgMultiSelect() {
        const sdgGrid = document.getElementById("sdgGrid");
        if (!sdgGrid) {
            // console.info('sdgMultiSelect: no #sdgGrid found — skipping');
            return;
        }

        // Ensure items are focusable
        sdgGrid.querySelectorAll(".sdg-item").forEach((item) => {
            if (!item.hasAttribute("tabindex"))
                item.setAttribute("tabindex", "0");
        });

        // Gather checkboxes
        const checkboxes = Array.from(sdgGrid.querySelectorAll(".sdg-input"));

        // Set initial visual state
        checkboxes.forEach((cb) => {
            const label = cb.closest(".sdg-item");
            if (!label) return;
            setSelectedState(label, !!cb.checked);
            cb.addEventListener("change", function () {
                setSelectedState(label, !!cb.checked);
            });
        });

        // Delegated click handling (prevents double-toggles across browsers)
        sdgGrid.addEventListener("click", function (e) {
            const item = e.target.closest(".sdg-item");
            if (!item || !sdgGrid.contains(item)) return;

            const cb = item.querySelector(".sdg-input");
            if (!cb) return;

            // If native input clicked, allow default (change event will run)
            if (e.target === cb) return;

            // Prevent native label toggling which can be inconsistent; toggle manually
            e.preventDefault();
            const newVal = !cb.checked;
            cb.checked = newVal;
            cb.dispatchEvent(new Event("change", { bubbles: true }));
        });

        // Keyboard support
        sdgGrid.addEventListener("keydown", function (e) {
            const item = e.target.closest(".sdg-item");
            if (!item || !sdgGrid.contains(item)) return;
            if (e.key === "Enter" || e.key === " ") {
                e.preventDefault();
                const cb = item.querySelector(".sdg-input");
                if (!cb) return;
                const newVal = !cb.checked;
                cb.checked = newVal;
                cb.dispatchEvent(new Event("change", { bubbles: true }));
            }
        });

        function setSelectedState(item, isSelected) {
            if (isSelected) {
                item.classList.add("selected");
                item.setAttribute("aria-pressed", "true");
            } else {
                item.classList.remove("selected");
                item.setAttribute("aria-pressed", "false");
            }
        }

        // debug helper
        window.getSelectedSdgs = function () {
            return checkboxes.filter((cb) => cb.checked).map((cb) => cb.value);
        };

        console.info(
            "SDG multi-select initialized. Current selected:",
            window.getSelectedSdgs()
        );
    })();

    /* ---------------- Tabs block ---------------- */
   (function tabsBlock() {
  const indicator = document.querySelector('.nav-indicator');
  const allTabs = Array.from(document.querySelectorAll('.nav-tab'));
  if (!allTabs.length || !indicator) return;

  // Only tabs that are JS-driven get a click handler
  const jsTabs = allTabs.filter(t => (t.getAttribute('href') || '#') === '#');

  // Initial active tab (prefer the one already marked active)
  const initialActive = document.querySelector('.nav-tab.active') || allTabs[0];
  if (initialActive) {
    setActiveTab(initialActive.getAttribute('data-tab'));
  }

  jsTabs.forEach(tab => {
    tab.addEventListener('click', function (e) {
      e.preventDefault(); // only for href="#"
      const tabName = this.getAttribute('data-tab');
      setActiveTab(tabName);
    });
  });

  function setActiveTab(tabName) {
    allTabs.forEach(t => t.classList.toggle('active', t.getAttribute('data-tab') === tabName));
    const activeTab = document.querySelector(`.nav-tab[data-tab="${tabName}"]`);
    if (activeTab) {
      positionIndicator(activeTab);
      updateContent(tabName);
    }
  }

  function positionIndicator(activeTab) {
    indicator.style.width = activeTab.offsetWidth + 'px';
    indicator.style.transform = `translateX(${activeTab.offsetLeft}px)`;
  }

  function updateContent(tabName) {
    const contentArea = document.querySelector('.content-placeholder');
    if (!contentArea) return;
    switch (tabName) {
      case 'event':
        contentArea.innerHTML = `
          <i class="fas fa-calendar-day"></i>
          <h3>Create New Event</h3>
          <p>Set up a new event with details, date, and location</p>`;
        break;
      case 'manage':
        contentArea.innerHTML = `
          <i class="fas fa-tasks"></i>
          <h3>Manage Events</h3>
          <p>View and manage all your events in one place</p>`;
        break;
      default:
        contentArea.innerHTML = '';
    }
  }

  // Keep indicator aligned on resize
  window.addEventListener('resize', () => {
    const current = document.querySelector('.nav-tab.active');
    if (current) positionIndicator(current);
  });
})();

}); /* end DOMContentLoaded */
