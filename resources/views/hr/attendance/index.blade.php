@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
.att-page { font-family: 'Sora', sans-serif; }

/* ── Topbar ─────────────────────────────────────────────────── */
.att-topbar { display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px;flex-wrap:wrap; }
.att-topbar-title { font-size:1.35rem;font-weight:800;color:#111827;letter-spacing:-0.02em;margin:0 0 2px; }
.att-topbar-sub   { font-size:0.78rem;color:#9ca3af;margin:0; }
.att-topbar-actions { display:flex;gap:8px;flex-wrap:wrap;align-items:center; }

.att-btn-primary {
    display:inline-flex;align-items:center;gap:7px;padding:9px 18px;
    background:#111827;color:#fff;border:none;border-radius:10px;
    font-family:'Sora',sans-serif;font-size:0.82rem;font-weight:600;
    text-decoration:none;cursor:pointer;transition:background 0.15s;white-space:nowrap;
}
.att-btn-primary:hover { background:#000;color:#fff; }

/* ── Flash messages ─────────────────────────────────────────── */
.att-flash { display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:10px;font-size:0.82rem;font-weight:500;margin-bottom:20px;animation:flashIn 0.3s ease; }
.att-flash.success { background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d; }
.att-flash.error   { background:#fff0f0;border:1px solid #fecaca;color:#c8292a; }
.att-flash.info    { background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8; }
@keyframes flashIn { from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)} }

/* ── Filter bar ─────────────────────────────────────────────── */
.att-filter-bar { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:12px 16px;display:flex;align-items:center;gap:10px;margin-bottom:14px;flex-wrap:wrap; }
.att-search-wrap { position:relative;flex:1;min-width:180px; }
.att-search-wrap svg { position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#9ca3af;pointer-events:none; }
.att-search-input { width:100%;border:1px solid #e5e7eb;border-radius:8px;padding:8px 12px 8px 34px;font-size:0.82rem;font-family:'Sora',sans-serif;color:#111827;background:#f9fafb;outline:none;transition:border-color 0.15s,background 0.15s; }
.att-search-input:focus { border-color:#c8292a;background:#fff;box-shadow:0 0 0 3px rgba(200,41,42,0.08); }
.att-filter-select { border:1px solid #e5e7eb;border-radius:8px;padding:8px 12px;font-size:0.82rem;font-family:'Sora',sans-serif;color:#374151;background:#f9fafb;outline:none;cursor:pointer;transition:border-color 0.15s; }
.att-filter-select:focus { border-color:#c8292a; }

/* ── Table card ─────────────────────────────────────────────── */
.att-section-head  { display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;flex-wrap:wrap;gap:10px; }
.att-section-title { font-size:0.88rem;font-weight:700;color:#111827;margin:0;display:flex;align-items:center;gap:8px; }
.att-dot { width:8px;height:8px;border-radius:50%;background:#c8292a;display:inline-block; }

.att-table-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden; }
.att-table-scroll { overflow-x:auto; }
.att-table { width:100%;border-collapse:collapse;font-size:0.835rem; }
.att-table thead tr { background:#f8f9fb;border-bottom:1px solid #e5e7eb; }
.att-table thead th { padding:11px 16px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.09em;color:#6b7280;white-space:nowrap;font-family:'Sora',sans-serif; }
.att-table tbody tr { border-bottom:1px solid #f3f4f6;transition:background 0.1s; }
.att-table tbody tr:last-child { border-bottom:none; }
.att-table tbody tr:hover { background:#fafafa; }
.att-table tbody td { padding:12px 16px;color:#374151;vertical-align:middle; }

/* Employee cell */
.att-emp-cell   { display:flex;align-items:center;gap:10px; }
.att-emp-avatar { width:32px;height:32px;border-radius:50%;background:#f3f4f6;display:flex;align-items:center;justify-content:center;font-size:0.72rem;font-weight:700;color:#6b7280;flex-shrink:0;border:1.5px solid #e5e7eb;text-transform:uppercase; }
.att-emp-name   { font-weight:600;color:#111827;font-size:0.845rem; }

/* Mono values */
.att-mono { font-family:'DM Mono',monospace;font-size:0.82rem;font-variant-numeric:tabular-nums; }
.att-date-tag { display:inline-block;font-family:'DM Mono',monospace;font-size:0.72rem;color:#6b7280;background:#f3f4f6;padding:2px 8px;border-radius:4px; }

/* Status badges */
.att-status { display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;white-space:nowrap; }
.att-status::before { content:'';width:5px;height:5px;border-radius:50%; }
.att-status.s-present     { background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0; }
.att-status.s-present::before { background:#16a34a; }
.att-status.s-late        { background:#fffbeb;color:#d97706;border:1px solid #fde68a; }
.att-status.s-late::before { background:#d97706; }
.att-status.s-absent      { background:#fff0f0;color:#c8292a;border:1px solid #fecaca; }
.att-status.s-absent::before { background:#ef4444; }
.att-status.s-early-leave { background:#f5f3ff;color:#7c3aed;border:1px solid #ddd6fe; }
.att-status.s-early-leave::before { background:#8b5cf6; }
.att-status.s-default     { background:#f3f4f6;color:#6b7280; }
.att-status.s-default::before { background:#9ca3af; }

/* Entry type badges */
.att-entry { display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:700;letter-spacing:0.06em;white-space:nowrap; }
.att-entry.e-manual { background:#f0f9ff;color:#0284c7;border:1px solid #bae6fd; }
.att-entry.e-qr     { background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0; }

/* Empty state */
.att-empty { display:flex;flex-direction:column;align-items:center;justify-content:center;padding:56px 24px;text-align:center; }
.att-empty-icon  { width:56px;height:56px;background:#f3f4f6;border-radius:16px;display:flex;align-items:center;justify-content:center;margin-bottom:14px;color:#d1d5db; }
.att-empty-title { font-size:0.9rem;font-weight:700;color:#374151;margin:0 0 6px; }
.att-empty-sub   { font-size:0.78rem;color:#9ca3af;margin:0; }

/* Pagination strip */
.att-pagination-strip { display:flex;justify-content:space-between;align-items:center;padding:12px 16px;border-top:1px solid #f3f4f6;background:#fafafa; }
.att-pagination-info  { font-size:0.75rem;color:#9ca3af; }
.att-pagination-info strong { color:#374151; }

/* No-results overlay */
#attNoResults { display:none; }
</style>
@endpush

@section('content')
<div class="att-page">

    {{-- Flash messages --}}
    @foreach(['success','error','info'] as $t)
        @if(session($t))
        <div class="att-flash {{ $t }}">
            @if($t==='success')
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            @else
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
            @endif
            {{ session($t) }}
        </div>
        @endif
    @endforeach

    {{-- Topbar --}}
    <div class="att-topbar">
        <div>
            <h1 class="att-topbar-title">Attendance Records</h1>
            <p class="att-topbar-sub">View and manage employee attendance logs</p>
        </div>
        <div class="att-topbar-actions">
            <a href="{{ route('attendance.create') }}" class="att-btn-primary">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Manual Log
            </a>
        </div>
    </div>

    {{-- Section header + filter --}}
    <div class="att-section-head">
        <h2 class="att-section-title"><span class="att-dot"></span> All Logs</h2>
    </div>

    <div class="att-filter-bar">
        <div class="att-search-wrap">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
            <input type="text" class="att-search-input" id="attSearch" placeholder="Search employee…">
        </div>
        <select class="att-filter-select" id="attStatusFilter">
            <option value="">All Statuses</option>
            <option value="present">Present</option>
            <option value="late">Late</option>
            <option value="absent">Absent</option>
            <option value="early_leave">Early Leave</option>
        </select>
        <select class="att-filter-select" id="attEntryFilter">
            <option value="">All Entry Types</option>
            <option value="manual">Manual</option>
            <option value="qr">QR Scanned</option>
        </select>
    </div>

    {{-- Table --}}
    <div class="att-table-card">
        <div class="att-table-scroll">
            <table class="att-table">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Date</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Entry Type</th>
                    </tr>
                </thead>
                <tbody id="attTbody">
                    @forelse($attendances as $attendance)
                        @php
                            $initials = strtoupper(
                                substr($attendance->employee->first_name ?? 'U', 0, 1) .
                                substr($attendance->employee->last_name ?? '', 0, 1)
                            );
                            $statusClass = match($attendance->status) {
                                'present'     => 's-present',
                                'late'        => 's-late',
                                'absent'      => 's-absent',
                                'early_leave' => 's-early-leave',
                                default       => 's-default',
                            };
                            $statusLabel = ucfirst(str_replace('_', ' ', $attendance->status));
                        @endphp
                        <tr data-name="{{ strtolower(($attendance->employee->first_name ?? '') . ' ' . ($attendance->employee->last_name ?? '')) }}"
                            data-status="{{ $attendance->status }}"
                            data-entry="{{ $attendance->is_manual ? 'manual' : 'qr' }}">
                            <td>
                                <div class="att-emp-cell">
                                    <div class="att-emp-avatar">{{ $initials }}</div>
                                    <div class="att-emp-name">
                                        {{ $attendance->employee->first_name }}
                                        {{ $attendance->employee->last_name }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="att-date-tag">{{ $attendance->date->format('M d, Y') }}</span>
                            </td>
                            <td>
                                <span class="att-mono">
                                    {{ $attendance->time_in
                                        ? \Carbon\Carbon::createFromFormat('H:i:s', $attendance->time_in)->format('g:i A')
                                        : '—' }}
                                </span>
                            </td>
                            <td>
                                <span class="att-mono">
                                    {{ $attendance->time_out
                                        ? \Carbon\Carbon::createFromFormat('H:i:s', $attendance->time_out)->format('g:i A')
                                        : '—' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="att-status {{ $statusClass }}">{{ $statusLabel }}</span>
                            </td>
                            <td class="text-center">
                                @if($attendance->is_manual)
                                    <span class="att-entry e-manual">
                                        <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Manual
                                    </span>
                                @else
                                    <span class="att-entry e-qr">
                                        <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        QR Scanned
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">
                            <div class="att-empty">
                                <div class="att-empty-icon">
                                    <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 6v6l4 2"/></svg>
                                </div>
                                <p class="att-empty-title">No attendance records found</p>
                                <p class="att-empty-sub">Records will appear here once employees clock in.</p>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div id="attNoResults">
            <div class="att-empty" style="padding:32px;">
                <p class="att-empty-title">No results found</p>
                <p class="att-empty-sub">Try adjusting your filters.</p>
            </div>
        </div>

        @if($attendances->hasPages())
        <div class="att-pagination-strip">
            <div class="att-pagination-info">
                Showing <strong>{{ $attendances->firstItem() }}</strong>–<strong>{{ $attendances->lastItem() }}</strong>
                of <strong>{{ $attendances->total() }}</strong> entries
            </div>
            {{ $attendances->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
window.attendanceRowsUrl    = "{{ route('api.attendance.table-rows') }}";
window.notificationsCountUrl = "{{ route('notifications.count') }}";

// ── Client-side filter ───────────────────────────────────────────
(function () {
    const search = document.getElementById('attSearch');
    const statusF = document.getElementById('attStatusFilter');
    const entryF  = document.getElementById('attEntryFilter');
    const tbody   = document.getElementById('attTbody');
    const noRes   = document.getElementById('attNoResults');

    function run() {
        const q  = search.value.toLowerCase().trim();
        const st = statusF.value;
        const et = entryF.value;
        const rows = Array.from(tbody.querySelectorAll('tr[data-name]'));
        const vis = rows.filter(r =>
            (!q  || r.dataset.name.includes(q)) &&
            (!st || r.dataset.status === st) &&
            (!et || r.dataset.entry === et)
        );
        rows.forEach(r => r.style.display = 'none');
        vis.forEach(r => r.style.display = '');
        noRes.style.display = vis.length === 0 && rows.length > 0 ? 'block' : 'none';
    }

    search.addEventListener('input', run);
    statusF.addEventListener('change', run);
    entryF.addEventListener('change', run);
})();
</script>

@if(auth()->user()->role === 'hr')
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
function refreshAttendanceTable() {
    fetch(window.attendanceRowsUrl)
        .then(r => { if (!r.ok) throw new Error(`HTTP ${r.status}`); return r.json(); })
        .then(data => { if (data.rows && data.rows.length) updateTableRows(data.rows); })
        .catch(err => console.error('Failed to refresh attendance table:', err.message));
}

function updateTableRows(rows) {
    const tbody = document.getElementById('attTbody');
    if (!tbody) return;

    const statusMap = {
        present:     { cls: 's-present',     label: 'Present' },
        late:        { cls: 's-late',         label: 'Late' },
        absent:      { cls: 's-absent',       label: 'Absent' },
        early_leave: { cls: 's-early-leave',  label: 'Early Leave' },
    };

    let html = '';
    rows.forEach(att => {
        const sm   = statusMap[att.status] || { cls: 's-default', label: att.status };
        const init = (att.employee || 'U').split(' ').map(n => n[0]).join('').substring(0,2).toUpperCase();
        const entry = att.is_manual
            ? `<span class="att-entry e-manual"><svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>Manual</span>`
            : `<span class="att-entry e-qr"><svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>QR Scanned</span>`;

        html += `
        <tr data-name="${att.employee.toLowerCase()}" data-status="${att.status}" data-entry="${att.is_manual ? 'manual' : 'qr'}">
            <td><div class="att-emp-cell"><div class="att-emp-avatar">${init}</div><div class="att-emp-name">${att.employee}</div></div></td>
            <td><span class="att-date-tag">${att.date}</span></td>
            <td><span class="att-mono">${att.time_in}</span></td>
            <td><span class="att-mono">${att.time_out}</span></td>
            <td class="text-center"><span class="att-status ${sm.cls}">${sm.label}</span></td>
            <td class="text-center">${entry}</td>
        </tr>`;
    });

    tbody.innerHTML = html;
}

function pollNotifications() {
    fetch(window.notificationsCountUrl)
        .then(r => { if (!r.ok) throw new Error(`HTTP ${r.status}`); return r.json(); })
        .then(data => {
            const unread = data.unread_count;
            const dot    = document.getElementById('notif-count');
            const badge  = document.getElementById('notif-badge');
            if (dot)   { dot.textContent = unread > 99 ? '99+' : unread; dot.style.display = unread > 0 ? 'inline-block' : 'none'; }
            if (badge) { badge.textContent = unread > 0 ? `${unread} New` : '0 New'; }
        })
        .catch(err => console.error('Notification poll error:', err.message));
}

setInterval(pollNotifications, 30000);
pollNotifications();
</script>
@endif
@endpush