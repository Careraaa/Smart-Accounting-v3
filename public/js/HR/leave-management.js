// Leave Management Page JavaScript

document.addEventListener('DOMContentLoaded', function () {
    // Initialize date range if filters exist
    const filters = {
        search: document.querySelector('input[name="search"]'),
        status: document.querySelector('select[name="status"]'),
        leaveType: document.querySelector('select[name="leave_type"]'),
        filterBtn: document.querySelector('button[type="submit"]')
    };

    // Auto-submit form on filter change
    if (filters.status && filters.leaveType) {
        filters.status.addEventListener('change', function () {
            if (filters.filterBtn) {
                filters.filterBtn.click();
            }
        });

        filters.leaveType.addEventListener('change', function () {
            if (filters.filterBtn) {
                filters.filterBtn.click();
            }
        });
    }

    // Highlight current active filters
    if (filters.status && filters.status.value !== 'all') {
        filters.status.classList.add('active-filter');
    }
    if (filters.leaveType && filters.leaveType.value !== 'all') {
        filters.leaveType.classList.add('active-filter');
    }
});

// Calculate leave duration in create/edit form
function initializeLeaveDurationCalculator() {
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');

    if (startDateInput && endDateInput) {
        // Function to update duration
        function updateDuration() {
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);

            if (startDate && endDate && endDate >= startDate) {
                const daysCount = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1;
                const durationDisplay = document.getElementById('durationDays');
                if (durationDisplay) {
                    durationDisplay.textContent = daysCount;
                }
                
                // Add visual feedback
                endDateInput.classList.remove('is-invalid');
            } else if (endDate < startDate) {
                endDateInput.classList.add('is-invalid');
            }
        }

        startDateInput.addEventListener('change', updateDuration);
        endDateInput.addEventListener('change', updateDuration);

        // Set minimum date for end_date to be start_date
        startDateInput.addEventListener('change', function () {
            endDateInput.min = this.value;
        });

        // Initial calculation
        updateDuration();
    }
}

// Initialize on page load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeLeaveDurationCalculator);
} else {
    initializeLeaveDurationCalculator();
}

// Handle approval modals
function handleLeaveApproval(leaveId) {
    const approveButton = document.querySelector(`button[data-leave-id="${leaveId}"][data-action="approve"]`);
    if (approveButton) {
        approveButton.addEventListener('click', function () {
            if (confirm('Are you sure you want to approve this leave request?')) {
                // Submit approval form
                document.querySelector(`form[data-leave-id="${leaveId}"][data-action="approve"]`).submit();
            }
        });
    }
}
