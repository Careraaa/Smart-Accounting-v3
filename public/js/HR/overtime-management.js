// Overtime/Undertime Management Page JavaScript

document.addEventListener('DOMContentLoaded', function () {
    // Initialize filters
    const filters = {
        search: document.querySelector('input[name="search"]'),
        type: document.querySelector('select[name="type"]'),
        status: document.querySelector('select[name="status"]'),
        filterBtn: document.querySelector('button[type="submit"]')
    };

    // Auto-submit form on filter change
    if (filters.type && filters.status) {
        filters.type.addEventListener('change', function () {
            if (filters.filterBtn) {
                filters.filterBtn.click();
            }
        });

        filters.status.addEventListener('change', function () {
            if (filters.filterBtn) {
                filters.filterBtn.click();
            }
        });
    }

    // Highlight current active filters
    if (filters.type && filters.type.value !== 'all') {
        filters.type.classList.add('active-filter');
    }
    if (filters.status && filters.status.value !== 'all') {
        filters.status.classList.add('active-filter');
    }
});

// Handle overtime/undertime type selection
function initializeOvertimeTypeHandler() {
    const typeSelect = document.getElementById('type');

    if (typeSelect) {
        typeSelect.addEventListener('change', function () {
            const selectedType = this.value;
            
            // Update reason placeholder based on type
            const reasonTextarea = document.getElementById('reason');
            if (reasonTextarea) {
                if (selectedType === 'overtime') {
                    reasonTextarea.placeholder = 'Reason for overtime (e.g., project deadline, additional work, etc.)';
                } else if (selectedType === 'undertime') {
                    reasonTextarea.placeholder = 'Reason for undertime (e.g., leave, appointment, etc.)';
                } else {
                    reasonTextarea.placeholder = 'Reason for overtime/undertime';
                }
            }

            // Highlight type badge with appropriate color
            const typeBadge = document.querySelector('.type-badge');
            if (typeBadge) {
                typeBadge.classList.remove('badge-overtime', 'badge-undertime');
                if (selectedType === 'overtime') {
                    typeBadge.classList.add('badge-overtime');
                    typeBadge.textContent = 'Overtime';
                } else if (selectedType === 'undertime') {
                    typeBadge.classList.add('badge-undertime');
                    typeBadge.textContent = 'Undertime';
                }
            }
        });

        // Trigger initial change
        typeSelect.dispatchEvent(new Event('change'));
    }
}

// Validate hours input
function validateHoursInput(input) {
    const value = parseFloat(input.value);

    if (isNaN(value) || value < 0.5) {
        input.classList.add('is-invalid');
        return false;
    } else if (value > 24) {
        input.classList.add('is-invalid');
        return false;
    } else {
        input.classList.remove('is-invalid');
        return true;
    }
}

// Initialize hours input validation
function initializeHoursValidation() {
    const hoursInput = document.getElementById('hours');

    if (hoursInput) {
        hoursInput.addEventListener('blur', function () {
            validateHoursInput(this);
        });

        hoursInput.addEventListener('change', function () {
            validateHoursInput(this);
        });
    }
}

// Initialize handlers on page load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () {
        initializeOvertimeTypeHandler();
        initializeHoursValidation();
    });
} else {
    initializeOvertimeTypeHandler();
    initializeHoursValidation();
}

// Handle approval/rejection
function handleOvertimeApproval(overtimeId) {
    const approveButton = document.querySelector(`button[data-overtime-id="${overtimeId}"][data-action="approve"]`);
    if (approveButton) {
        approveButton.addEventListener('click', function () {
            if (confirm('Are you sure you want to approve this record?')) {
                document.querySelector(`form[data-overtime-id="${overtimeId}"][data-action="approve"]`).submit();
            }
        });
    }
}

// Format hours display
function formatHours(hours) {
    return parseFloat(hours).toFixed(2) + ' hrs';
}
