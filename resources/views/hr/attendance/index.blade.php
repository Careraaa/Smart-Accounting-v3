@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
*, *::before, *::after { box-sizing: border-box; }
.att-page { font-family: 'Sora', sans-serif; }

/* ── Layout ─────────────────────────────────────────────────── */
.att-layout {
    display: block;
}

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
@keyframes flashIn { from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)} }

/* ── Filter bar ─────────────────────────────────────────────── */
.att-filter-bar { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:10px 14px;display:flex;align-items:center;gap:10px;margin-bottom:14px;flex-wrap:wrap; }
.att-search-wrap { position:relative;flex:1;min-width:160px; }
.att-search-wrap svg { position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#9ca3af;pointer-events:none; }
.att-search-input { width:100%;border:1px solid #e5e7eb;border-radius:8px;padding:7px 12px 7px 32px;font-size:0.82rem;font-family:'Sora',sans-serif;color:#111827;background:#f9fafb;outline:none;transition:border-color 0.15s; }
.att-search-input:focus { border-color:#c8292a;background:#fff;box-shadow:0 0 0 3px rgba(200,41,42,0.08); }
.att-filter-select { border:1px solid #e5e7eb;border-radius:8px;padding:7px 12px;font-size:0.82rem;font-family:'Sora',sans-serif;color:#374151;background:#f9fafb;outline:none;cursor:pointer; }
.att-filter-select:focus { border-color:#c8292a; }

/* ── Employee table card ─────────────────────────────────────── */
.att-table-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden; }
.att-table-scroll { overflow-x:auto; }
.att-table { width:100%;border-collapse:collapse;font-size:0.835rem; }
.att-table thead tr { background:#f8f9fb;border-bottom:1px solid #e5e7eb; }
.att-table thead th { padding:11px 16px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.09em;color:#6b7280;white-space:nowrap; }
.att-table tbody tr { border-bottom:1px solid #f3f4f6;transition:background 0.12s;cursor:pointer; }
.att-table tbody tr:last-child { border-bottom:none; }
.att-table tbody tr:hover { background:#fdf4f4; }
.att-table tbody td { padding:12px 16px;color:#374151;vertical-align:middle; }

/* Employee cell */
.att-emp-cell   { display:flex;align-items:center;gap:10px; }
.att-emp-avatar { width:34px;height:34px;border-radius:50%;background:#f3f4f6;display:flex;align-items:center;justify-content:center;font-size:0.72rem;font-weight:700;color:#6b7280;flex-shrink:0;border:1.5px solid #e5e7eb;text-transform:uppercase; }
.att-emp-name   { font-weight:600;color:#111827;font-size:0.845rem;line-height:1.2; }
.att-emp-pos    { font-size:0.72rem;color:#9ca3af;margin-top:1px; }

/* Department badge */
.att-dept { display:inline-block;padding:2px 9px;border-radius:20px;font-size:0.68rem;font-weight:700;background:#f3f4f6;color:#374151;white-space:nowrap; }

/* Status badges */
.att-status { display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;white-space:nowrap; }
.att-status::before { content:'';width:5px;height:5px;border-radius:50%; }
.att-status.s-in      { background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0; }
.att-status.s-in::before { background:#16a34a; }
.att-status.s-out     { background:#fffbeb;color:#d97706;border:1px solid #fde68a; }
.att-status.s-out::before { background:#d97706; }
.att-status.s-absent  { background:#fff0f0;color:#c8292a;border:1px solid #fecaca; }
.att-status.s-absent::before { background:#ef4444; }
.att-status.s-late    { background:#fdf4ff;color:#9333ea;border:1px solid #e9d5ff; }
.att-status.s-late::before { background:#a855f7; }

/* View link */
.att-view-link { display:inline-flex;align-items:center;gap:5px;font-size:0.78rem;font-weight:600;color:#c8292a;text-decoration:none;padding:5px 10px;border-radius:7px;border:1px solid #fecaca;background:#fff0f0;transition:all 0.12s; }
.att-view-link:hover { background:#c8292a;color:#fff;border-color:#c8292a; }

/* Empty state */
.att-empty { display:flex;flex-direction:column;align-items:center;justify-content:center;padding:48px 24px;text-align:center; }
.att-empty-icon  { width:52px;height:52px;background:#f3f4f6;border-radius:14px;display:flex;align-items:center;justify-content:center;margin-bottom:12px;color:#d1d5db; }
.att-empty-title { font-size:0.88rem;font-weight:700;color:#374151;margin:0 0 4px; }
.att-empty-sub   { font-size:0.75rem;color:#9ca3af;margin:0; }

/* ── Side panel ─────────────────────────────────────────────── */
.att-side-card {
    background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;
    position:sticky;top:20px;
}
.att-side-header {
    padding:14px 16px;border-bottom:1px solid #f3f4f6;
    display:flex;align-items:center;justify-content:space-between;
}
.att-side-title { font-size:0.82rem;font-weight:800;color:#111827;margin:0;display:flex;align-items:center;gap:7px; }
.att-side-dot   { width:7px;height:7px;border-radius:50%;background:#22c55e;box-shadow:0 0 0 2px rgba(34,197,94,0.25);animation:pulse 2s infinite; }
@keyframes pulse { 0%,100%{box-shadow:0 0 0 2px rgba(34,197,94,0.25)} 50%{box-shadow:0 0 0 5px rgba(34,197,94,0.1)} }
.att-side-sub   { font-size:0.68rem;color:#9ca3af;margin:0; }

.att-log-list { max-height:480px;overflow-y:auto; }
.att-log-list::-webkit-scrollbar { width:4px; }
.att-log-list::-webkit-scrollbar-track { background:transparent; }
.att-log-list::-webkit-scrollbar-thumb { background:#e5e7eb;border-radius:4px; }

.att-log-item {
    display:flex;align-items:center;gap:10px;padding:10px 16px;
    border-bottom:1px solid #f9fafb;transition:background 0.1s;
}
.att-log-item:last-child { border-bottom:none; }
.att-log-item:hover { background:#fafafa; }

.att-log-icon {
    width:28px;height:28px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;
}
.att-log-icon.in  { background:#f0fdf4;color:#16a34a; }
.att-log-icon.out { background:#fffbeb;color:#d97706; }

.att-log-name { font-size:0.78rem;font-weight:600;color:#111827;line-height:1.2; }
.att-log-meta { font-size:0.68rem;color:#9ca3af;margin-top:1px; }
.att-log-time { font-family:'DM Mono',monospace;font-size:0.72rem;color:#6b7280;margin-left:auto;white-space:nowrap; }

.att-side-empty { padding:32px 16px;text-align:center; }
.att-side-empty p { font-size:0.78rem;color:#9ca3af;margin:0; }

/* Today date badge */
.att-today-badge {
    display:inline-flex;align-items:center;gap:5px;padding:3px 10px;
    background:#f0fdf4;border:1px solid #bbf7d0;border-radius:20px;
    font-size:0.68rem;font-weight:700;color:#16a34a;
}

/* Pagination strip */
.att-pagination-strip {
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:12px 16px;
    border-top:1px solid #f3f4f6;
    background:#fafafa;
    flex-wrap:wrap;
    gap:16px;
}

.att-pagination-info {
    font-size:0.75rem;
    color:#9ca3af;
}

.att-pagination-info strong {
    color:#374151;
}

.att-pagination-strip nav {
    margin-left:auto;
}

.att-pagination-strip .pagination {
    display:flex;
    align-items:center;
    gap:4px;
    margin:0;
    padding:0;
    list-style:none;
}

.att-pagination-strip .page-item {
    display:flex;
}

.att-pagination-strip .page-item .page-link {
    width:28px;
    height:28px;

    border-radius:7px !important;
    border:1px solid #e5e7eb;

    background:#fff;
    color:#6b7280;

    font-size:0.74rem;
    font-weight:600;

    display:flex;
    align-items:center;
    justify-content:center;

    padding:0;
    text-decoration:none;

    transition:all .15s ease;
}

.att-pagination-strip .page-item .page-link:hover {
    background:#f3f4f6;
    border-color:#d1d5db;
    color:#111827;
}

.att-pagination-strip .page-item.active .page-link {
    background:#c8292a;
    border-color:#c8292a;
    color:#fff;
}

.att-pagination-strip .page-item.disabled .page-link {
    background:#f9fafb;
    color:#d1d5db;
    pointer-events:none;
    cursor:not-allowed;
}
</style>
@endpush

@section('content')
<div class="att-page">

    {{-- Flash messages --}}
    @foreach(['success','error'] as $t)
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
            <h1 class="att-topbar-title">Attendance</h1>
            <p class="att-topbar-sub">
                Employee time-in / time-out status for today —
                <span class="att-today-badge">
                    <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                    {{ now()->format('l, F j, Y') }}
                </span>
            </p>
        </div>
        <div class="att-topbar-actions">
            <a href="{{ route('attendance.create') }}" class="att-btn-primary">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Manual Log
            </a>
        </div>
    </div>

    <div class="att-layout">

        {{-- ── Main: Employee List ─────────────────────────────── --}}
        <div class="att-main">

            {{-- Filter bar --}}
            <div class="att-filter-bar">
                <div class="att-search-wrap">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
                    <input type="text" class="att-search-input" id="empSearch" placeholder="Search employee…">
                </div>
                <select class="att-filter-select" id="deptFilter">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ strtolower($dept) }}">{{ $dept }}</option>
                    @endforeach
                </select>
                <select class="att-filter-select" id="statusFilter">
                    <option value="">All Statuses</option>
                    <option value="in">Timed In</option>
                    <option value="out">Timed Out</option>
                    <option value="absent">Not Yet In</option>
                </select>
            </div>

            {{-- Table --}}
            <div class="att-table-card">
                <div class="att-table-scroll">
                    <table class="att-table">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Department</th>
                                <th class="text-center">Today's Status</th>
                                <th>Time In</th>
                                <th>Time Out</th>
                            </tr>
                        </thead>
                        <tbody id="empTbody">
                            @forelse($employees as $emp)
                                @php
                                    $att = $todayAttendance[$emp->id] ?? null;
                                    $initials = strtoupper(substr($emp->first_name ?? 'U', 0, 1) . substr($emp->last_name ?? '', 0, 1));

                                    if (!$att) {
                                        $todayStatus = 'absent';
                                        $statusClass = 's-absent';
                                        $statusLabel = 'Not Yet In';
                                    } elseif ($att->time_out) {
                                        $todayStatus = 'out';
                                        $statusClass = 's-out';
                                        $statusLabel = 'Timed Out';
                                    } elseif ($att->status === 'late') {
                                        $todayStatus = 'in';
                                        $statusClass = 's-late';
                                        $statusLabel = 'Late';
                                    } else {
                                        $todayStatus = 'in';
                                        $statusClass = 's-in';
                                        $statusLabel = 'Timed In';
                                    }
                                @endphp
                                <tr
                                    data-emp-id="{{ $emp->id }}"
                                    data-name="{{ strtolower($emp->first_name . ' ' . $emp->last_name) }}"
                                    data-dept="{{ strtolower($emp->department ?? '') }}"
                                    data-status="{{ $todayStatus }}"
                                    onclick="window.location='{{ route('attendance.employee.calendar', $emp->id) }}'"
                                >
                                    <td>
                                        <div class="att-emp-cell">
                                            <div class="att-emp-avatar">{{ $initials }}</div>
                                            <div>
                                                <div class="att-emp-name">{{ $emp->first_name }} {{ $emp->last_name }}</div>
                                                <div class="att-emp-pos">{{ $emp->position ?? '—' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="att-dept">{{ $emp->department ?? '—' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="att-status {{ $statusClass }}">{{ $statusLabel }}</span>
                                    </td>
                                    <td style="font-family:'DM Mono',monospace;font-size:1rem;color:#374151;">
                                        {{ $att && $att->time_in ? \Carbon\Carbon::createFromFormat('H:i:s', $att->time_in)->format('g:i A') : '—' }}
                                    </td>
                                    <td style="font-family:'DM Mono',monospace;font-size:1rem;color:#374151;">
                                        {{ $att && $att->time_out ? \Carbon\Carbon::createFromFormat('H:i:s', $att->time_out)->format('g:i A') : '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6">
                                    <div class="att-empty">
                                        <div class="att-empty-icon">
                                            <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                                        </div>
                                        <p class="att-empty-title">No employees found</p>
                                        <p class="att-empty-sub">Add employees to start tracking attendance.</p>
                                    </div>
                                </td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div id="empNoResults" style="display:none;">
                    <div class="att-empty" style="padding:28px;">
                        <p class="att-empty-title">No results</p>
                        <p class="att-empty-sub">Try adjusting your filters.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Pagination --}}
    <div class="att-pagination-strip">
        <div class="att-pagination-info" id="attPaginationInfo">
            Showing <strong>1</strong>–<strong>10</strong> of <strong>0</strong> employees
        </div>

        <nav id="attPaginationNav"></nav>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.notificationsCountUrl = "{{ route('notifications.count') }}";
window.attendanceCalendarRoute = "{{ route('attendance.employee.calendar', ['employee' => ':id']) }}";

// ── Build complete employee data with attendance ──────────────────
window.allEmployeesData = {!! json_encode($allEmployees->map(fn($e) => [
    'id' => $e->id,
    'firstName' => $e->first_name,
    'lastName' => $e->last_name,
    'name' => strtolower($e->first_name . ' ' . $e->last_name),
    'dept' => strtolower($e->department ?? ''),
    'department' => $e->department,
    'position' => $e->position,
])) !!};

window.todayAttendance = {!! json_encode($todayAttendance->map(fn($a) => [
    'user_id' => $a->user_id,
    'time_in' => $a->time_in,
    'time_out' => $a->time_out,
    'status' => $a->status,
])->keyBy('user_id')) !!};

// ── Client-side filter and pagination ─────────────────────────────
(function () {
    const search  = document.getElementById('empSearch');
    const deptF   = document.getElementById('deptFilter');
    const statusF = document.getElementById('statusFilter');
    const tbody   = document.getElementById('empTbody');
    const noRes   = document.getElementById('empNoResults');
    const paginationStrip = document.querySelector('.att-pagination-strip');
    
    let currentPage = 1;
    const itemsPerPage = 10;
    let filteredData = [];

    function getEmployeeStatus(emp) {
        const att = window.todayAttendance[emp.id];
        if (!att) return 'absent';
        if (att.time_out) return 'out';
        if (att.status === 'late') return 'in';
        return 'in';
    }

    function applyFilters() {
        const q = search.value.toLowerCase().trim();
        const d = deptF.value;
        const s = statusF.value;

        filteredData = window.allEmployeesData.filter(emp => {
            const matchesSearch = !q || emp.name.includes(q);
            const matchesDept = !d || emp.dept === d;
            const matchesStatus = !s || getEmployeeStatus(emp) === s;
            return matchesSearch && matchesDept && matchesStatus;
        });

        currentPage = 1;
        renderTable();
    }

    function renderTable() {
        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const pageData = filteredData.slice(start, end);
        
        // Clear tbody
        tbody.innerHTML = '';

        if (pageData.length === 0) {
            if (filteredData.length === 0 && window.allEmployeesData.length > 0) {
                noRes.style.display = 'block';
            } else if (window.allEmployeesData.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6"><div class="att-empty"><div class="att-empty-icon"><svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg></div><p class="att-empty-title">No employees found</p><p class="att-empty-sub">Add employees to start tracking attendance.</p></div></td></tr>';
            }
        } else {
            noRes.style.display = 'none';
            pageData.forEach(emp => {
                const att = window.todayAttendance[emp.id];
                const status = getEmployeeStatus(emp);
                let statusClass = 's-absent';
                let statusLabel = 'Not Yet In';
                
                if (att) {
                    if (att.time_out) {
                        statusClass = 's-out';
                        statusLabel = 'Timed Out';
                    } else if (att.status === 'late') {
                        statusClass = 's-late';
                        statusLabel = 'Late';
                    } else {
                        statusClass = 's-in';
                        statusLabel = 'Timed In';
                    }
                }

                const initials = (emp.firstName.charAt(0) + emp.lastName.charAt(0)).toUpperCase();
                const timeIn = att && att.time_in ? new Date('2000-01-01 ' + att.time_in).toLocaleTimeString('en-US', {hour: 'numeric', minute: '2-digit', hour12: true}) : '—';
                const timeOut = att && att.time_out ? new Date('2000-01-01 ' + att.time_out).toLocaleTimeString('en-US', {hour: 'numeric', minute: '2-digit', hour12: true}) : '—';

                const calendarUrl = window.attendanceCalendarRoute.replace(':id', emp.id);
                const row = document.createElement('tr');
                row.dataset.empId = emp.id;
                row.dataset.name = emp.name;
                row.dataset.dept = emp.dept;
                row.dataset.status = status;
                row.style.cursor = 'pointer';
                row.onclick = () => window.location = calendarUrl;
                row.innerHTML = `
                    <td>
                        <div class="att-emp-cell">
                            <div class="att-emp-avatar">${initials}</div>
                            <div>
                                <div class="att-emp-name">${emp.firstName} ${emp.lastName}</div>
                                <div class="att-emp-pos">${emp.position || '—'}</div>
                            </div>
                        </div>
                    </td>
                    <td><span class="att-dept">${emp.department || '—'}</span></td>
                    <td class="text-center"><span class="att-status ${statusClass}">${statusLabel}</span></td>
                    <td style="font-family:'DM Mono',monospace;font-size:1rem;color:#374151;">${timeIn}</td>
                    <td style="font-family:'DM Mono',monospace;font-size:1rem;color:#374151;">${timeOut}</td>
                `;
                tbody.appendChild(row);
            });
        }

        updatePagination();
    }

    function updatePagination() {
        const totalPages = Math.ceil(filteredData.length / itemsPerPage);

        const info = document.getElementById('attPaginationInfo');
        const nav = document.getElementById('attPaginationNav');

        if (!info || !nav) return;

        // No results
        if (filteredData.length === 0) {
            info.innerHTML = 'No employees to display';
            nav.innerHTML = '';
            return;
        }

        const start = (currentPage - 1) * itemsPerPage + 1;
        const end = Math.min(currentPage * itemsPerPage, filteredData.length);

        info.innerHTML = `
            Showing <strong>${start}</strong>–<strong>${end}</strong>
            of <strong>${filteredData.length}</strong>
            employee${filteredData.length > 1 ? 's' : ''}
        `;

        // Hide pagination if only 1 page
        if (totalPages <= 1) {
            nav.innerHTML = '';
            return;
        }

        let html = `<ul class="pagination mb-0">`;

        // Previous button
        html += `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage - 1}">
                    ‹
                </a>
            </li>
        `;

        // Page numbers
        for (let i = 1; i <= totalPages; i++) {
            html += `
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">
                        ${i}
                    </a>
                </li>
            `;
        }

        // Next button
        html += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage + 1}">
                    ›
                </a>
            </li>
        `;

        html += `</ul>`;

        nav.innerHTML = html;

        // Click handlers
        nav.querySelectorAll('a[data-page]').forEach(link => {
            link.addEventListener('click', e => {
                e.preventDefault();

                const page = parseInt(link.dataset.page);

                if (page < 1 || page > totalPages) return;

                currentPage = page;
                renderTable();
            });
        });
    }

    search.addEventListener('input', applyFilters);
    deptF.addEventListener('change', applyFilters);
    statusF.addEventListener('change', applyFilters);

    // Initial render
    applyFilters();
})();

// ── Notification polling ─────────────────────────────────────────
function pollNotifications() {
    fetch(window.notificationsCountUrl)
        .then(r => r.ok ? r.json() : Promise.reject())
        .then(data => {
            const unread = data.unread_count;
            const dot   = document.getElementById('notif-count');
            const badge = document.getElementById('notif-badge');
            if (dot)   { dot.textContent = unread > 99 ? '99+' : unread; dot.style.display = unread > 0 ? 'inline-block' : 'none'; }
            if (badge) { badge.textContent = unread > 0 ? `${unread} New` : '0 New'; }
        })
        .catch(() => {});
}
setInterval(pollNotifications, 30000);
pollNotifications();
</script>
@endpush
