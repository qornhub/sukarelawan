class AdminUsersManager {
    constructor() {
        this.deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        this.currentDeleteForm = null;
        this.init();
    }

    init() {
        this.bindEvents();
        this.highlightActiveFilters();
        this.setupLoadingStates();
    }

    /*** EVENT BINDINGS ***/
    bindEvents() {
        // Delete confirmation
        document.querySelectorAll('.delete-form').forEach(form =>
            form.addEventListener('submit', e => {
                e.preventDefault();
                this.showDeleteConfirmation(form);
            })
        );

        // Debounced search
        const searchInput = document.querySelector('.search-input');
        if (searchInput) {
            searchInput.addEventListener(
                'input',
                this.debounce(() => this.submitFilterForm(), 500)
            );
        }

        // Auto-submit on filter change (role, dates, per_page) – keep sorting separate
        document
            .querySelectorAll('.filter-control:not(.sort-controls *)')
            .forEach(control =>
                control.addEventListener('change', () => this.submitFilterForm())
            );

        // ⭐ NEW: activity filter logic based on sort_by text
        const sortBySelect   = document.querySelector('select[name="sort_by"]');
        const sortDirSelect  = document.querySelector('select[name="sort_dir"]');
        const activityInput  = document.querySelector('input[name="activity"]');

        if (sortBySelect) {
            sortBySelect.addEventListener('change', () => {
                const selectedOption = sortBySelect.options[sortBySelect.selectedIndex];
                const text = selectedOption.text.toLowerCase().trim();

                if (!activityInput) {
                    this.submitFilterForm();
                    return;
                }

                if (text === 'active users') {
                    activityInput.value = 'active';
                    sortBySelect.value  = 'activity';
                    this.submitFilterForm();

                } else if (text === 'inactive users') {
                    activityInput.value = 'inactive';
                    sortBySelect.value  = 'activity';
                    this.submitFilterForm();

                } else {
                    // When switching back to Joined Date / Name / Role
                    activityInput.value = '';
                    this.submitFilterForm();
                }
            });
        }

        // Confirm delete
        document.getElementById('confirmDelete')?.addEventListener('click', () => this.confirmDelete());
    }

    /*** DELETE HANDLERS ***/
    showDeleteConfirmation(form) {
        this.currentDeleteForm = form;
        this.deleteModal.show();
    }

    confirmDelete() {
        if (this.currentDeleteForm) this.currentDeleteForm.submit();
        this.deleteModal.hide();
    }

    /*** FILTER FORM HANDLING ***/
    submitFilterForm() {
        const filterForm = document.getElementById('filterForm');
        const searchInput = document.querySelector('.search-input');
        if (!filterForm) return;

        // Ensure search query is included
        if (searchInput) {
            let hiddenSearch = filterForm.querySelector('input[name="q"]');
            if (!hiddenSearch) {
                hiddenSearch = document.createElement('input');
                hiddenSearch.type = 'hidden';
                hiddenSearch.name = 'q';
                filterForm.appendChild(hiddenSearch);
            }
            hiddenSearch.value = searchInput.value;
        }

        filterForm.submit();
    }

    /*** UI ENHANCEMENTS ***/
    highlightActiveFilters() {
        document.querySelectorAll('.filter-control').forEach(filter => {
            if (filter.value) filter.classList.add('border-primary');
        });
    }

    setupLoadingStates() {
        document.querySelectorAll('form').forEach(form =>
            form.addEventListener('submit', () => {
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.classList.add('loading');
                    submitBtn.disabled = true;
                }
            })
        );
    }

    /*** UTILITIES ***/
    debounce(fn, delay) {
        let timer;
        return (...args) => {
            clearTimeout(timer);
            timer = setTimeout(() => fn(...args), delay);
        };
    }

    showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = `
            top: 20px;
            right: 20px;
            z-index: 1060;
            min-width: 300px;
        `;
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(notification);

        setTimeout(() => notification.remove(), 5000);
    }
}

/*** INITIALIZE ON DOM READY ***/
document.addEventListener('DOMContentLoaded', () => new AdminUsersManager());

/*** EXPORT (OPTIONAL, FOR TESTING/MODULE USAGE) ***/
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AdminUsersManager;
}
