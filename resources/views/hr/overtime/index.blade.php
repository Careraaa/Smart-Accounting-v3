@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
*, *::before, *::after { box-sizing: border-box; }
.ot-page { font-family: 'Sora', sans-serif; }

/* ── Topbar ─────────────────────────────────────────────────── */
.ot-topbar { display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px;flex-wrap:wrap; }
.ot-topbar-title { font-size:1.35rem;font-weight:800;color:#111827;letter-spacing:-0.02em;margin:0 0 2px; }
.ot-topbar-sub   { font-size:0.78rem;color:#9ca3af;margin:0; }
.ot-topbar-actions { display:flex;gap:8px;flex-wrap:wrap;align-items:center; }

.ot-btn-primary {
    display:inline-flex;align-items:center;gap:7px;padding:9px 18px;
    background:#111827;color:#fff;border:none;border-radius:10px;
    font-family:'Sora',sans-serif;font-size:0.82rem;font-weight:600;
    text-decoration:none;cursor:pointer;transition:background 0.15s;white-space:nowrap;
}
.ot-btn-primary:hover { background:#000;color:#fff; }

/* ── Flash ──────────────────────────────────────────────────── */
.ot-flash { display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:10px;font-size:0.82rem;font-weight:500;margin-bottom:20px;animation:flashIn 0.3s ease; }
.ot-flash.success { background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d; }
.ot-flash.error   { background:#fff0f0;border:1px solid #fecaca;color:#c8292a; }
@keyframes flashIn { from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)} }

/* ── Filter bar ─────────────────────────────────────────────── */
.ot-filter-bar { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:10px 14px;display:flex;align-items:center;gap:10px;margin-bottom:14px;flex-wrap:wrap; }
.ot-search-wrap { position:relative;flex:1;min-width:160px; }
.ot-search-wrap svg { position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#9ca3af;pointer-events:none; }
.ot-search-input { width:100%;border:1px solid #e5e7eb;border-radius:8px;padding:7px 12px 7px 32px;font-size:0.82rem;font-family:'Sora',sans-serif;color:#111827;background:#f9fafb;outline:none;transition:border-color 0.15s; }
.ot-search-input:focus { border-color:#c8292a;background:#fff;box-shadow:0 0 0 3px rgba(200,41,42,0.08); }
.ot-filter-select { border:1px solid #e5e7eb;border-radius:8px;padding:7px 12px;font-size:0.82rem;font-family:'Sora',sans-serif;color:#374151;background:#f9fafb;outline:none;cursor:pointer; }
.ot-filter-select:focus { border-color:#c8292a; }

/* ── Table card ─────────────────────────────────────────────── */
.ot-table-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden; }
.ot-table-scroll { overflow-x:auto; }
.ot-table { width:100%;border-collapse:collapse;font-size:0.835rem; }
.ot-table thead tr { background:#f8f9fb;border-bottom:1px solid #e5e7eb; }
.ot-table thead th { padding:11px 16px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.09em;color:#6b7280;white-space:nowrap; }
.ot-table tbody tr { border-bottom:1px solid #f3f4f6;transition:background 0.12s;cursor:pointer; }
.ot-table tbody tr:last-child { border-bottom:none; }
.ot-table tbody tr:hover { background:#fdf4f4; }
.ot-table tbody td { padding:12px 16px;color:#374151;vertical-align:middle; }

/* Employee cell */
.ot-emp-cell   { display:flex;align-items:center;gap:10px; }
.ot-emp-avatar { width:34px;height:34px;border-radius:50%;background:#f3f4f6;display:flex;align-items:center;justify-content:center;font-size:0.72rem;font-weight:700;color:#6b7280;flex-shrink:0;border:1.5px solid #e5e7eb;text-transform:uppercase; }
.ot-emp-name   { font-weight:600;color:#111827;font-size:0.845rem;line-height:1.2; }
.ot-emp-pos    { font-size:0.72rem;color:#9ca3af;margin-top:1px; }

/* Department badge */
.ot-dept { display:inline-block;padding:2px 9px;border-radius:20px;font-size:0.68rem;font-weight:700;background:#f3f4f6;color:#374151;white-space:nowrap; }

/* Hours pills */
.ot-pill { display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:20px;font-size:0.72rem;font-weight:700;font-family:'DM Mono',monospace; }
.ot-pill.ot { background:#f0f9ff;color:#0284c7;border:1px solid #bae6fd; }
.ot-pill.ut { background:#fffbeb;color:#d97706;border:1px solid #fde68a; }
.ot-pill.none { background:#f3f4f6;color:#9ca3af;border:1px solid #e5e7eb; }

/* View link */
.ot-view-link { display:inline-flex;align-items:center;gap:5px;font-size:0.78rem;font-weight:600;color:#c8292a;text-decoration:none;padding:5px 10px;border-radius:7px;border:1px solid #fecaca;background:#fff0f0;transition:all 0.12s; }
.ot-view-link:hover { background:#c8292a;color:#fff;border-color:#c8292a; }

/* Empty state */
.ot-empty { display:flex;flex-direction:column;align-items:center;justify-content:center;padding:48px 24px;text-align:center; }
.ot-empty-icon  { width:52px;height:52px;background:#f3f4f6;border-radius:14px;display:flex;align-items:center;justify-content:center;margin-bottom:12px;color:#d1d5db; }
.ot-empty-title { font-size:0.88rem;font-weight:700;color:#374151;margin:0 0 4px; }
.ot-empty-sub   { font-size:0.75rem;color:#9ca3af;margin:0; }

/* Month badge */
.ot-month-badge { display:inline-flex;align-items:center;gap:5px;padding:3px 10px;background:#f0f9ff;border:1px solid #bae6fd;border-radius:20px;font-size:0.68rem;font-weight:700;color:#0284c7; }

/* Pagination strip */
.ot-pagination-strip {
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:12px 16px;
    border-top:1px solid #f3f4f6;
    background:#fafafa;
    flex-wrap:wrap;
    gap:16px;
}

.ot-pagination-info {
    font-size:0.75rem;
    color:#9ca3af;
}

.ot-pagination-info strong {
    color:#374151;
}

.ot-pagination-strip nav {
    margin-left:auto;
}

.ot-pagination-strip .pagination {
    display:flex;
    align-items:center;
    gap:4px;
    margin:0;
    padding:0;
    list-style:none;
}

.ot-pagination-strip .page-item {
    display:flex;
}

.ot-pagination-strip .page-item .page-link {
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

.ot-pagination-strip .page-item .page-link:hover {
    background:#f3f4f6;
    border-color:#d1d5db;
    color:#111827;
}

.ot-pagination-strip .page-item.active .page-link {
    background:#c8292a;
    border-color:#c8292a;
    color:#fff;
}

.ot-pagination-strip .page-item.disabled .page-link {
    background:#f9fafb;
    color:#d1d5db;
    pointer-events:none;
    cursor:not-allowed;
}
</style>
@endpush

@section('content')
<div class="ot-page">

    {{-- Flash --}}
    @foreach(['success','error'] as $t)
        @if(session($t))
        <div class="ot-flash {{ $t }}">
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
    <div class="ot-topbar">
        <div>
            <h1 class="ot-topbar-title">Overtime / Undertime</h1>
            <p class="ot-topbar-sub">
                Employee OT &amp; UT records —
                <span class="ot-month-badge">
                    <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                    {{ now()->format('F Y') }}
                </span>
            </p>
        </div>
        <div class="ot-topbar-actions">
            <a href="{{ route('overtime.pending') }}" class="ot-btn-primary">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 7v5l3 3"/></svg>
                Pending Requests
                @php $pendingOtCount = \App\Models\OvertimeUndertime::where('status','pending')->count(); @endphp
                @if($pendingOtCount > 0)
                    <span style="background:rgba(255,255,255,0.25);border-radius:999px;padding:1px 7px;font-size:0.72rem;font-weight:800;">{{ $pendingOtCount }}</span>
                @endif
            </a>
        </div>
    </div>

    {{-- Filter bar --}}
    <div class="ot-filter-bar">
        <div class="ot-search-wrap">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
            <input type="text" class="ot-search-input" id="empSearch" placeholder="Search employee…">
        </div>
        <select class="ot-filter-select" id="deptFilter">
            <option value="">All Departments</option>
            @foreach($departments as $dept)
                <option value="{{ strtolower($dept) }}">{{ $dept }}</option>
            @endforeach
        </select>
    </div>

    {{-- Employee table --}}
    <div class="ot-table-card">
        <div class="ot-table-scroll">
            <table class="ot-table">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Department</th>
                        <th class="text-center">OT This Month</th>
                        <th class="text-center">UT This Month</th>
                        <th class="text-center">Records</th>
                    </tr>
                </thead>
                <tbody id="empTbody">
                    @forelse($allEmployees as $emp)
                        @php
                            $summary  = $monthlySummary[$emp->id] ?? null;
                            $otHrs    = $summary ? (float) $summary->ot_hours : 0;
                            $utHrs    = $summary ? (float) $summary->ut_hours : 0;
                            $recCount = $summary ? (int)   $summary->total_records : 0;
                            $initials = strtoupper(substr($emp->first_name ?? 'U', 0, 1) . substr($emp->last_name ?? '', 0, 1));
                        @endphp
                        <tr
                            data-emp-id="{{ $emp->id }}"
                            data-name="{{ strtolower($emp->first_name . ' ' . $emp->last_name) }}"
                            data-dept="{{ strtolower($emp->department ?? '') }}"
                            onclick="window.location='{{ route('overtime.employee.calendar', $emp->id) }}'"
                        >
                            <td>
                                <div class="ot-emp-cell">
                                    <div class="ot-emp-avatar">{{ $initials }}</div>
                                    <div>
                                        <div class="ot-emp-name">{{ $emp->first_name }} {{ $emp->last_name }}</div>
                                        <div class="ot-emp-pos">{{ $emp->position ?? '—' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="ot-dept">{{ $emp->department ?? '—' }}</span></td>
                            <td class="text-center">
                                @if($otHrs > 0)
                                    <span class="ot-pill ot">
                                        <svg width="9" height="9" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                        {{ number_format($otHrs, 1) }} hrs
                                    </span>
                                @else
                                    <span class="ot-pill none">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($utHrs > 0)
                                    <span class="ot-pill ut">
                                        <svg width="9" height="9" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
                                        {{ number_format($utHrs, 1) }} hrs
                                    </span>
                                @else
                                    <span class="ot-pill none">—</span>
                                @endif
                            </td>
                            <td class="text-center" style="font-family:'DM Mono',monospace;font-size:0.82rem;color:#374151;font-weight:700;">
                                {{ $recCount > 0 ? $recCount : '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">
                            <div class="ot-empty">
                                <div class="ot-empty-icon">
                                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                                </div>
                                <p class="ot-empty-title">No employees found</p>
                                <p class="ot-empty-sub">Add employees to start tracking overtime and undertime.</p>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div id="empNoResults" style="display:none;">
            <div class="ot-empty" style="padding:28px;">
                <p class="ot-empty-title">No results</p>
                <p class="ot-empty-sub">Try adjusting your filters.</p>
            </div>
        </div>
    </div>
    {{-- Pagination --}}
    <div class="ot-pagination-strip">
        <div class="ot-pagination-info" id="otPaginationInfo">
            Showing <strong>1</strong>–<strong>10</strong> of <strong>0</strong> employees
        </div>

        <nav id="otPaginationNav"></nav>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ── Build complete employee data with OT/UT summary ──────────────────
window.allEmployeesData = {!! json_encode($allEmployees->map(fn($e) => [
    'id' => $e->id,
    'firstName' => $e->first_name,
    'lastName' => $e->last_name,
    'name' => strtolower($e->first_name . ' ' . $e->last_name),
    'dept' => strtolower($e->department ?? ''),
    'department' => $e->department,
    'position' => $e->position,
])) !!};

window.monthlySummary = {!! json_encode($monthlySummary->map(fn($s) => [
    'user_id' => $s->user_id,
    'ot_hours' => (float) $s->ot_hours,
    'ut_hours' => (float) $s->ut_hours,
    'total_records' => (int) $s->total_records,
])->keyBy('user_id')) !!};

// ── Client-side filter and pagination ─────────────────────────────
(function () {
    const search  = document.getElementById('empSearch');
    const deptF   = document.getElementById('deptFilter');
    const tbody   = document.getElementById('empTbody');
    const noRes   = document.getElementById('empNoResults');
    const paginationStrip = document.querySelector('.ot-pagination-strip');
    
    let currentPage = 1;
    const itemsPerPage = 10;
    let filteredData = [];

    function applyFilters() {
        const q = search.value.toLowerCase().trim();
        const d = deptF.value;

        filteredData = window.allEmployeesData.filter(emp => {
            const matchesSearch = !q || emp.name.includes(q);
            const matchesDept = !d || emp.dept === d;
            return matchesSearch && matchesDept;
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
                tbody.innerHTML = '<tr><td colspan="6"><div class="ot-empty"><div class="ot-empty-icon"><svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg></div><p class="ot-empty-title">No employees found</p><p class="ot-empty-sub">Add employees to start tracking overtime and undertime.</p></div></td></tr>';
            }
        } else {
            noRes.style.display = 'none';
            pageData.forEach(emp => {
                const summary = window.monthlySummary[emp.id];
                const otHrs = summary ? summary.ot_hours : 0;
                const utHrs = summary ? summary.ut_hours : 0;
                const recCount = summary ? summary.total_records : 0;

                const initials = (emp.firstName.charAt(0) + emp.lastName.charAt(0)).toUpperCase();
                const calendarUrl = '{{ route("overtime.employee.calendar", ["employee" => ":id"]) }}'.replace(':id', emp.id);
                const row = document.createElement('tr');
                row.dataset.empId = emp.id;
                row.dataset.name = emp.name;
                row.dataset.dept = emp.dept;
                row.style.cursor = 'pointer';
                row.onclick = () => window.location = calendarUrl;
                
                let otPill = '<span class="ot-pill none">—</span>';
                if (otHrs > 0) {
                    otPill = `<span class="ot-pill ot"><svg width="9" height="9" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>${otHrs.toFixed(1)} hrs</span>`;
                }

                let utPill = '<span class="ot-pill none">—</span>';
                if (utHrs > 0) {
                    utPill = `<span class="ot-pill ut"><svg width="9" height="9" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>${utHrs.toFixed(1)} hrs</span>`;
                }

                row.innerHTML = `
                    <td>
                        <div class="ot-emp-cell">
                            <div class="ot-emp-avatar">${initials}</div>
                            <div>
                                <div class="ot-emp-name">${emp.firstName} ${emp.lastName}</div>
                                <div class="ot-emp-pos">${emp.position || '—'}</div>
                            </div>
                        </div>
                    </td>
                    <td><span class="ot-dept">${emp.department || '—'}</span></td>
                    <td class="text-center">${otPill}</td>
                    <td class="text-center">${utPill}</td>
                    <td class="text-center" style="font-family:'DM Mono',monospace;font-size:0.82rem;color:#374151;font-weight:700;">${recCount > 0 ? recCount : '—'}</td>
                `;
                tbody.appendChild(row);
            });
        }

        updatePagination();
    }

    function updatePagination() {
        const totalPages = Math.ceil(filteredData.length / itemsPerPage);

        const info = document.getElementById('otPaginationInfo');
        const nav = document.getElementById('otPaginationNav');

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

    // Initial render
    applyFilters();
})();
</script>
@endpush
