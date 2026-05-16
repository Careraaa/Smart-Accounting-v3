{{-- Calendar Sidebar Component --}}
<div class="prl-calendar-container">
    <h2 class="prl-calendar-container-title">Calendar</h2>
    <div class="prl-sidebar">
        <div class="prl-calendar">
            <div class="prl-calendar-header">
                <h3 class="prl-calendar-title" id="calendarMonth">{{ now()->format('F Y') }}</h3>
                <div class="prl-calendar-nav">
                    <button class="prl-calendar-btn" id="calPrevBtn" type="button">&larr;</button>
                    <button class="prl-calendar-btn" id="calNextBtn" type="button">&rarr;</button>
                </div>
            </div>
            
            <div class="prl-calendar-weekdays">
                <div class="prl-calendar-weekday">Sun</div>
                <div class="prl-calendar-weekday">Mon</div>
                <div class="prl-calendar-weekday">Tue</div>
                <div class="prl-calendar-weekday">Wed</div>
                <div class="prl-calendar-weekday">Thu</div>
                <div class="prl-calendar-weekday">Fri</div>
                <div class="prl-calendar-weekday">Sat</div>
            </div>
            
            <div class="prl-calendar-dates" id="calendarDates"></div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Reset - Prevent Bootstrap from interfering with calendar grid */
.prl-calendar-dates > * {
    float: none !important;
    flex-basis: auto !important;
    flex-grow: 0 !important;
}

/* ── Calendar Container ── */
.prl-calendar-container {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
}

.prl-calendar-container-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #111827;
    font-family: 'Sora', sans-serif;
    margin: 0 0 16px 0;
}

/* ── Calendar Sidebar ── */
.prl-sidebar {
    position: relative;
    width: 100%;
    margin: 0;
}

/* ── Calendar Card ── */
.prl-calendar {
    width: 100%;
    background: #ffffff;
    border: 1px solid #ececec;
    border-radius: 16px;
    padding: 18px;
    font-family: 'Sora', sans-serif;
    box-shadow: 0 2px 10px rgba(0,0,0,0.04);
}

/* Header */
.prl-calendar-header {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    margin-bottom: 18px !important;
}

.prl-calendar-title {
    margin: 0 !important;
    font-size: 0.92rem !important;
    font-weight: 700 !important;
    color: #111827 !important;
}

/* Navigation */
.prl-calendar-nav {
    display: flex !important;
    gap: 6px !important;
}

.prl-calendar-btn {
    width: 22px !important;
    height: 22px !important;
    border: none !important;
    background: transparent !important;
    border-radius: 4px !important;
    cursor: pointer !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    color: #9ca3af !important;
    transition: all 0.15s ease !important;
    font-size: 0.75rem !important;
    font-weight: 600 !important;
    padding: 0 !important;
}

.prl-calendar-btn:hover {
    background: #f3f4f6 !important;
    color: #111827 !important;
}

/* Weekdays */
.prl-calendar-weekdays {
    display: grid !important;
    grid-template-columns: repeat(7, 1fr) !important;
    gap: 2px !important;
    margin-bottom: 6px !important;
}

.prl-calendar-weekday {
    text-align: center !important;
    font-size: 0.58rem !important;
    font-weight: 600 !important;
    color: #9ca3af !important;
    padding: 4px 0 !important;
    margin: 0 !important;
}

/* Dates - Force grid layout */
.prl-calendar-dates {
    display: grid !important;
    grid-template-columns: repeat(7, 1fr) !important;
    gap: 2px !important;
    width: 100% !important;
    grid-auto-rows: auto !important;
}

.prl-calendar-date {
    width: 28px !important;
    height: 28px !important;
    margin: auto !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    border-radius: 50% !important;
    font-size: 0.70rem !important;
    font-weight: 500 !important;
    color: #374151 !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
    background: transparent !important;
    border: none !important;
    padding: 0 !important;
}

.prl-calendar-date:hover {
    background: #f3f4f6 !important;
}

.prl-calendar-date.other-month {
    color: #d1d5db !important;
}

.prl-calendar-date.today {
    background: #7f1d1d !important;
    color: #fff !important;
    font-weight: 700 !important;
}

.prl-calendar-date.holiday {
    background: #fcd34d !important;
    color: #111827 !important;
    font-weight: 700 !important;
}

.prl-calendar-date.selected {
    background: #991b1b !important;
    color: #fff !important;
    font-weight: 700 !important;
}

/* ── Upcoming Holidays ── */
.prl-upcoming-holidays {
    margin-top: 16px !important;
}

.prl-upcoming-title {
    margin: 0 0 10px 0 !important;
    font-size: 0.85rem !important;
    font-weight: 700 !important;
    color: #111827 !important;
    font-family: 'Sora', sans-serif !important;
}

.prl-holidays-list {
    font-size: 0.75rem !important;
    color: #6b7280 !important;
    font-family: 'Sora', sans-serif !important;
}

.prl-holiday-item {
    padding: 8px 0 !important;
    border-bottom: 1px solid #f3f4f6 !important;
    margin: 0 !important;
}

.prl-holiday-item:last-child {
    border-bottom: none !important;
}

.prl-holiday-date {
    font-weight: 600 !important;
    color: #111827 !important;
}

.prl-holiday-name {
    color: #6b7280 !important;
    display: block !important;
}
</style>
@endpush

@push('scripts')
<script>
// Calendar functionality
function initCalendar() {
    // Initialize variables
    let currentDate = new Date();
    const calendarMonthEl = document.getElementById('calendarMonth');
    const calendarDatesEl = document.getElementById('calendarDates');
    const calPrevBtn = document.getElementById('calPrevBtn');
    const calNextBtn = document.getElementById('calNextBtn');
    
    // Ensure all elements exist
    if (!calendarMonthEl || !calendarDatesEl || !calPrevBtn || !calNextBtn) {
        console.error('Calendar elements not found');
        return;
    }
    
    let holidays = {};

    async function loadHolidaysForCalendar(year) {
        try {
            const res = await fetch(`/api/holidays?year=${year}`);
            if (!res.ok) return;
            const data = await res.json();
            holidays = {};
            data.forEach(h => {
                holidays[h.date] = h.name;
            });
        } catch (error) {
            console.warn('Holiday data unavailable:', error);
            holidays = {};
        }
    }
    
    async function renderUpcomingHolidays() {
        const today = new Date();
        const upcomingList = document.getElementById('upcomingHolidaysList');
        if (!upcomingList) return;
        
        upcomingList.innerHTML = '';
        
        // Load holidays for current year and next year
        await loadHolidaysForCalendar(today.getFullYear());
        const nextYearHolidays = {};
        try {
            const res = await fetch(`/api/holidays?year=${today.getFullYear() + 1}`);
            if (res.ok) {
                const data = await res.json();
                data.forEach(h => {
                    nextYearHolidays[h.date] = h.name;
                });
            }
        } catch (err) {
            console.warn('Next year holidays unavailable');
        }
        
        // Combine holidays
        const allHolidays = { ...holidays, ...nextYearHolidays };
        
        // Find upcoming holidays (from today onwards, next 6 months)
        const upcomingHolidays = [];
        const maxDate = new Date(today);
        maxDate.setMonth(maxDate.getMonth() + 6);
        
        Object.entries(allHolidays).forEach(([dateStr, name]) => {
            const holidayDate = new Date(dateStr);
            if (holidayDate >= today && holidayDate <= maxDate) {
                upcomingHolidays.push({ date: holidayDate, dateStr, name });
            }
        });
        
        // Sort by date
        upcomingHolidays.sort((a, b) => a.date - b.date);
        
        // Display upcoming holidays
        if (upcomingHolidays.length === 0) {
            upcomingList.innerHTML = '<div style="color: #9ca3af; font-size: 0.75rem; padding: 8px 0;">No upcoming holidays</div>';
        } else {
            upcomingHolidays.slice(0, 5).forEach(holiday => {
                const item = document.createElement('div');
                item.className = 'prl-holiday-item';
                const dateFormatted = holiday.date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                item.innerHTML = `<span class="prl-holiday-date">${dateFormatted}</span><span class="prl-holiday-name">${holiday.name}</span>`;
                upcomingList.appendChild(item);
            });
        }
    }
    
    async function renderCalendar() {
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();

        // Load holidays but don't block rendering
        await loadHolidaysForCalendar(year);
        
        calendarMonthEl.textContent = currentDate.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
        
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const daysInPrevMonth = new Date(year, month, 0).getDate();
        
        calendarDatesEl.innerHTML = '';
        
        // Previous month dates
        for (let i = firstDay - 1; i >= 0; i--) {
            const day = daysInPrevMonth - i;
            const div = document.createElement('div');
            div.className = 'prl-calendar-date other-month';
            div.textContent = day;
            calendarDatesEl.appendChild(div);
        }
        
        // Current month dates
        const today = new Date();
        for (let day = 1; day <= daysInMonth; day++) {
            const div = document.createElement('div');
            div.className = 'prl-calendar-date';
            div.textContent = day;
            
            const dateString = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            
            if (year === today.getFullYear() && month === today.getMonth() && day === today.getDate()) {
                div.classList.add('today');
            } else if (holidays[dateString]) {
                div.classList.add('holiday');
                div.title = holidays[dateString];
            }
            
            calendarDatesEl.appendChild(div);
        }
        
        // Next month dates
        const totalCells = calendarDatesEl.children.length;
        const remainingCells = 42 - totalCells;
        for (let day = 1; day <= remainingCells; day++) {
            const div = document.createElement('div');
            div.className = 'prl-calendar-date other-month';
            div.textContent = day;
            calendarDatesEl.appendChild(div);
        }
        
        // Render upcoming holidays
        renderUpcomingHolidays();
    }
    
    calPrevBtn.addEventListener('click', function() {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar();
    });
    
    calNextBtn.addEventListener('click', function() {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar();
    });
    
    // Initial render
    renderCalendar();
}

// Wait for DOM to be ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCalendar);
} else {
    initCalendar();
}
</script>
@endpush
