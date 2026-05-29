@php
$calMonth = now()->month;
$calYear  = now()->year;
$today    = now()->format('Y-m-d');
$apiUrl   = url('api/holidays');
@endphp

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden w-full max-w-[280px]" id="cal-card">
    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
        <span class="text-[11px] font-semibold text-gray-600 uppercase tracking-widest">Calendar</span>
        <div class="flex items-center gap-1">
            <button class="p-1.5 rounded-lg hover:bg-gray-100 transition-all text-gray-500 hover:text-gray-700 active:scale-90" id="cal-prev" type="button">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <span class="text-sm font-bold text-gray-900 min-w-[120px] text-center tracking-tight leading-none" id="cal-label">{{ \Carbon\Carbon::createFromDate($calYear, $calMonth, 1)->format('F Y') }}</span>
            <button class="p-1.5 rounded-lg hover:bg-gray-100 transition-all text-gray-500 hover:text-gray-700 active:scale-90" id="cal-next" type="button">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
    </div>
    <div class="px-3 pt-1 pb-3">
        <div class="grid grid-cols-7 gap-px" id="cal-grid"></div>
        <div class="flex items-center gap-3 mt-3 pt-2.5 border-t border-gray-50">
            <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span><span class="text-[11px] text-gray-600">Today</span></span>
            <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span><span class="text-[11px] text-gray-600">Selected</span></span>
            <span class="flex items-center gap-1" id="cal-holiday-legend"><span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span><span class="text-[11px] text-gray-600">Holiday</span></span>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const calState = { month: {{ $calMonth }}, year: {{ $calYear }}, selected: '{{ $today }}' };
    const todayStr = '{{ $today }}';
    const calGrid = document.getElementById('cal-grid');
    const calLabel = document.getElementById('cal-label');
    const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    const holidays = {};

    // ── Tooltip ──
    var calTip = document.createElement('div');
    calTip.className = 'fixed z-[9999] pointer-events-none transition-all duration-150';
    calTip.style.opacity = '0';
    calTip.style.transform = 'translateY(4px)';
    calTip.innerHTML =
        '<div class="bg-white rounded-xl border border-gray-200 shadow-lg shadow-gray-200/50 px-3 py-2.5 max-w-[220px]">' +
            '<div class="flex items-center gap-2">' +
                '<div class="w-7 h-7 rounded-full bg-amber-100 flex items-center justify-center shrink-0">' +
                    '<svg class="w-3.5 h-3.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">' +
                        '<path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>' +
                    '</svg>' +
                '</div>' +
                '<div class="min-w-0">' +
                    '<div class="text-[11px] font-semibold text-amber-600 uppercase tracking-wider leading-tight">Holiday</div>' +
                    '<div class="text-sm font-semibold text-gray-800 leading-tight mt-0.5 truncate" id="cal-tip-name"></div>' +
                '</div>' +
            '</div>' +
            '<div class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-2.5 h-2.5 bg-white border-r border-b border-gray-200 rotate-45 rounded-br-[2px]"></div>' +
        '</div>';
    document.body.appendChild(calTip);
    var calTipName = document.getElementById('cal-tip-name');
    var pinnedCell = null;

    function showTip(cell, name) {
        calTipName.textContent = name;
        void calTip.offsetHeight;
        var r = cell.getBoundingClientRect();
        var cx = r.left + r.width / 2;
        var tx = Math.max(8, Math.min(cx - 100, window.innerWidth - 208));
        var ty = r.top - calTip.offsetHeight - 6;
        calTip.style.left = tx + 'px';
        calTip.style.top = ty + 'px';
        calTip.style.opacity = '1';
        calTip.style.transform = 'translateY(0)';
    }

    function hideTip() {
        if (pinnedCell) return;
        calTip.style.opacity = '0';
        calTip.style.transform = 'translateY(4px)';
    }

    function pinTip(cell) {
        pinnedCell = cell;
        showTip(cell, cell.dataset.tip);
    }

    function unpinTip() {
        pinnedCell = null;
        hideTip();
    }

    // ── Calendar ──
    function fetchHolidays(year) {
        if (Object.keys(holidays).length && year === calState.year) return;
        fetch('{{ $apiUrl }}?year=' + year)
            .then(function (r) { return r.json(); })
            .then(function (data) {
                data.forEach(function (h) {
                    if (!holidays[h.date]) holidays[h.date] = [];
                    holidays[h.date].push(h.name);
                });
                renderCalendar();
            })
            .catch(function () {});
    }

    function renderCalendar() {
        const firstDow = new Date(calState.year, calState.month - 1, 1).getDay();
        const daysIn = new Date(calState.year, calState.month, 0).getDate();
        calLabel.textContent = monthNames[calState.month - 1] + ' ' + calState.year;
        let html = '';

        ['S','M','T','W','T','F','S'].forEach(function (d) {
            html += '<span class="text-[10px] font-semibold uppercase tracking-widest text-gray-600 text-center pb-1 pt-2">' + d + '</span>';
        });

        for (let i = 0; i < firstDow; i++) {
            html += '<span></span>';
        }

        for (let d = 1; d <= daysIn; d++) {
            const ds = calState.year + '-' + String(calState.month).padStart(2,'0') + '-' + String(d).padStart(2,'0');
            const dayHolidays = holidays[ds];
            const isHoliday = !!dayHolidays;
            const tipText = dayHolidays ? dayHolidays.join(', ') : '';
            let cls = 'relative text-center py-2 text-xs font-semibold rounded-lg transition-all duration-150 cursor-pointer select-none ';
            if (ds === todayStr) {
                cls += 'bg-rose-500 text-white shadow-sm shadow-rose-200 ';
            } else if (ds === calState.selected) {
                cls += 'bg-rose-50 text-rose-600 ring-1 ring-rose-200 ';
            } else if (isHoliday) {
                cls += 'text-amber-700 bg-amber-50 cal-cell-holiday hover:bg-amber-100 ';
            } else {
                cls += 'text-gray-700 hover:bg-gray-50 ';
            }
            html += '<span class="' + cls + '" data-date="' + ds + '"' + (isHoliday ? ' data-tip="' + escHtml(tipText) + '"' : '') + '>' + d + '</span>';
        }

        calGrid.innerHTML = html;

        calGrid.querySelectorAll('[data-date]').forEach(function (el) {
            if (el.matches('.cal-cell-holiday')) {
                el.addEventListener('click', function (e) {
                    e.stopPropagation();
                    if (pinnedCell === this) { unpinTip(); return; }
                    calState.selected = this.dataset.date;
                    renderCalendar();
                    pinTip(this);
                });
                el.addEventListener('mouseenter', function () { if (this !== pinnedCell) showTip(this, this.dataset.tip); });
                el.addEventListener('mouseleave', function () { if (this !== pinnedCell) hideTip(); });
            } else {
                el.addEventListener('click', function () {
                    unpinTip();
                    calState.selected = this.dataset.date;
                    renderCalendar();
                });
            }
        });

        document.addEventListener('click', function (e) {
            if (pinnedCell && !pinnedCell.contains(e.target)) unpinTip();
        });
    }

    function escHtml(s) {
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    fetchHolidays(calState.year);
    renderCalendar();

    document.getElementById('cal-prev').addEventListener('click', function () {
        if (--calState.month < 1) { calState.month = 12; calState.year--; }
        fetchHolidays(calState.year);
        renderCalendar();
    });
    document.getElementById('cal-next').addEventListener('click', function () {
        if (++calState.month > 12) { calState.month = 1; calState.year++; }
        fetchHolidays(calState.year);
        renderCalendar();
    });
});
</script>
@endpush
