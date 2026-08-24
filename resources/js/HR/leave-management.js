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
        function updateDuration() {
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);

            if (startDate && endDate && endDate >= startDate) {
                const daysCount = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1;
                const durationDisplay = document.getElementById('durationDays');
                if (durationDisplay) durationDisplay.textContent = daysCount;
                endDateInput.classList.remove('is-invalid');
            } else if (endDate < startDate) {
                endDateInput.classList.add('is-invalid');
            }
        }

        startDateInput.addEventListener('change', updateDuration);
        endDateInput.addEventListener('change', updateDuration);

        startDateInput.addEventListener('change', function () {
            endDateInput.min = this.value;
        });

        updateDuration();
    }
}

// Initialize on page load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeLeaveDurationCalculator);
} else {
    initializeLeaveDurationCalculator();
}

// Initialize leave approval and rejection buttons
function initializeLeaveApprovalButtons() {
    // Handle approve buttons
    document.querySelectorAll('.approve-btn').forEach(button => {
        button.addEventListener('click', async function (e) {
            e.preventDefault();
            
            const leaveId = this.dataset.leaveId;
            const employeeName = this.dataset.employeeName;
            
            if (await window.saConfirm({ message: `Are you sure you want to approve the leave request from ${employeeName}?`, confirmText: 'Approve', variant: 'primary' })) {
                document.getElementById(`approve-form-${leaveId}`).submit();
            }
        });
    });
    
    // Handle reject buttons
    document.querySelectorAll('.reject-btn').forEach(button => {
        button.addEventListener('click', async function (e) {
            e.preventDefault();
            
            const leaveId = this.dataset.leaveId;
            const employeeName = this.dataset.employeeName;
            
            if (await window.saConfirm({ message: `Are you sure you want to reject the leave request from ${employeeName}?`, confirmText: 'Reject', variant: 'danger' })) {
                document.getElementById(`reject-form-${leaveId}`).submit();
            }
        });
    });
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeLeaveApprovalButtons);
} else {
    initializeLeaveApprovalButtons();
}
