@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
.emp-page { font-family: 'Sora', sans-serif; }

/* ── Topbar ─────────────────────────────────────────────────── */
.emp-topbar { display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px;flex-wrap:wrap; }
.emp-topbar-title { font-size:1.35rem;font-weight:800;color:#111827;letter-spacing:-0.02em;margin:0 0 2px; }
.emp-topbar-sub   { font-size:0.78rem;color:#9ca3af;margin:0; }
.emp-topbar-actions { display:flex;gap:8px;flex-wrap:wrap;align-items:center; }

.emp-btn-primary {
    display:inline-flex;align-items:center;gap:7px;padding:9px 18px;
    background:#111827;color:#fff;border:none;border-radius:10px;
    font-family:'Sora',sans-serif;font-size:0.82rem;font-weight:600;
    text-decoration:none;cursor:pointer;transition:background 0.15s;white-space:nowrap;
}
.emp-btn-primary:hover { background:#000;color:#fff; }

.emp-btn-sec {
    display:inline-flex;align-items:center;gap:7px;padding:9px 16px;background:#fff;color:#374151;
    border:1px solid #e5e7eb;border-radius:10px;font-family:'Sora',sans-serif;font-size:0.82rem;
    font-weight:600;text-decoration:none;cursor:pointer;transition:all 0.15s;white-space:nowrap;
}
.emp-btn-sec:hover { border-color:#c8292a;color:#c8292a;background:#fff5f5; }

/* ── Flash messages ─────────────────────────────────────────── */
.emp-flash { display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:10px;font-size:0.82rem;font-weight:500;margin-bottom:20px;animation:empFlashIn 0.3s ease; }
.emp-flash.success { background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d; }
.emp-flash.error   { background:#fff0f0;border:1px solid #fecaca;color:#c8292a; }
.emp-flash.info    { background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8; }
@keyframes empFlashIn { from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)} }

/* ── Filter bar ─────────────────────────────────────────────── */
.emp-filter-bar { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:12px 16px;display:flex;align-items:center;gap:10px;margin-bottom:14px;flex-wrap:wrap; }
.emp-search-wrap { position:relative;flex:1;min-width:180px; }
.emp-search-wrap svg { position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#9ca3af;pointer-events:none; }
.emp-search-input { width:100%;border:1px solid #e5e7eb;border-radius:8px;padding:8px 12px 8px 34px;font-size:0.82rem;font-family:'Sora',sans-serif;color:#111827;background:#f9fafb;outline:none;transition:border-color 0.15s,background 0.15s; }
.emp-search-input:focus { border-color:#c8292a;background:#fff;box-shadow:0 0 0 3px rgba(200,41,42,0.08); }
.emp-filter-select { border:1px solid #e5e7eb;border-radius:8px;padding:8px 12px;font-size:0.82rem;font-family:'Sora',sans-serif;color:#374151;background:#f9fafb;outline:none;cursor:pointer;transition:border-color 0.15s; }
.emp-filter-select:focus { border-color:#c8292a; }

/* ── Section header ─────────────────────────────────────────── */
.emp-section-head  { display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;flex-wrap:wrap;gap:10px; }
.emp-section-title { font-size:0.88rem;font-weight:700;color:#111827;margin:0;display:flex;align-items:center;gap:8px; }
.emp-dot { width:8px;height:8px;border-radius:50%;background:#c8292a;display:inline-block; }

/* ── Table ──────────────────────────────────────────────────── */
.emp-table-card   { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden; }
.emp-table-scroll { overflow-x:auto; }
.emp-table { width:100%;border-collapse:collapse;font-size:0.835rem; }
.emp-table thead tr { background:#f8f9fb;border-bottom:1px solid #e5e7eb; }
.emp-table thead th { padding:11px 16px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.09em;color:#6b7280;white-space:nowrap;font-family:'Sora',sans-serif; }
.emp-table thead th a { color:#6b7280;text-decoration:none;display:inline-flex;align-items:center;gap:4px;transition:color 0.13s; }
.emp-table thead th a:hover { color:#c8292a; }
.emp-table tbody tr { border-bottom:1px solid #f3f4f6;transition:background 0.1s;cursor:pointer; }
.emp-table tbody tr:last-child { border-bottom:none; }
.emp-table tbody tr:hover { background:#fff5f5; }
.emp-table tbody td { padding:12px 16px;color:#374151;vertical-align:middle; }

/* Employee cell */
.emp-cell   { display:flex;align-items:center;gap:10px; }
.emp-avatar { width:34px;height:34px;border-radius:50%;background:#f3f4f6;display:flex;align-items:center;justify-content:center;font-size:0.72rem;font-weight:700;color:#6b7280;flex-shrink:0;border:1.5px solid #e5e7eb;text-transform:uppercase; }
.emp-name   { font-weight:600;color:#111827;font-size:0.845rem;line-height:1.2; }
.emp-pos    { font-size:0.72rem;color:#9ca3af;margin-top:1px; }

/* Department badge */
.emp-dept { display:inline-block;padding:2px 9px;border-radius:20px;font-size:0.68rem;font-weight:700;background:#f3f4f6;color:#374151;white-space:nowrap; }

/* Status badges */
.emp-status { display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;white-space:nowrap; }
.emp-status::before { content:'';width:5px;height:5px;border-radius:50%; }
.emp-status.s-active   { background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0; }
.emp-status.s-active::before { background:#16a34a; }
.emp-status.s-inactive { background:#fff0f0;color:#c8292a;border:1px solid #fecaca; }
.emp-status.s-inactive::before { background:#ef4444; }

/* Action buttons */
.emp-actions { display:flex;align-items:center;gap:5px;justify-content:flex-end; }
.emp-action-btn { width:30px;height:30px;border-radius:7px;border:none;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;text-decoration:none;font-size:13px;transition:background 0.13s,color 0.13s;background:#f4f5f7;color:#6b7280;padding:0; }
.emp-action-btn:hover         { background:#eff6ff;color:#3b82f6; }
.emp-action-btn.edit:hover    { background:#fffbeb;color:#d97706; }
.emp-action-btn.danger:hover  { background:#fff1f2;color:#e11d48; }

/* Empty state */
.emp-empty { display:flex;flex-direction:column;align-items:center;justify-content:center;padding:56px 24px;text-align:center; }
.emp-empty-icon  { width:56px;height:56px;background:#f3f4f6;border-radius:16px;display:flex;align-items:center;justify-content:center;margin-bottom:14px;color:#d1d5db; }
.emp-empty-title { font-size:0.9rem;font-weight:700;color:#374151;margin:0 0 6px; }
.emp-empty-sub   { font-size:0.78rem;color:#9ca3af;margin:0; }

/* Pagination strip */
.emp-pagination-strip {
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:12px 16px;
    border-top:1px solid #f3f4f6;
    background:#fafafa;
    flex-wrap:wrap;
    gap:16px;
}

.emp-pagination-info {
    font-size:0.75rem;
    color:#9ca3af;
}

.emp-pagination-info strong {
    color:#374151;
}

.emp-pagination-strip nav {
    margin-left:auto;
}

.emp-pagination-strip .pagination {
    display:flex;
    align-items:center;
    gap:4px;
    margin:0;
    padding:0;
    list-style:none;
}

.emp-pagination-strip .page-item {
    display:flex;
}

.emp-pagination-strip .page-item .page-link {
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

.emp-pagination-strip .page-item .page-link:hover {
    background:#f3f4f6;
    border-color:#d1d5db;
    color:#111827;
}

.emp-pagination-strip .page-item.active .page-link {
    background:#c8292a;
    border-color:#c8292a;
    color:#fff;
}

.emp-pagination-strip .page-item.disabled .page-link {
    background:#f9fafb;
    color:#d1d5db;
    pointer-events:none;
    cursor:not-allowed;
}

/* No results */
#empNoResults { display:none; }
</style>
@endpush

@section('content')
<div class="emp-page">

    {{-- Flash messages --}}
    @foreach(['success','error','info'] as $t)
        @if(session($t))
        <div class="emp-flash {{ $t }}">
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
    <div class="emp-topbar">
        <div>
            <h1 class="emp-topbar-title">Employees</h1>
            <p class="emp-topbar-sub">Manage your workforce and employee records</p>
        </div>
        <div class="emp-topbar-actions">
            <a href="{{ route('hr.reports.print.employee-report') }}" class="emp-btn-sec" target="_blank">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2m-10 0h8v4H8v-4z"/></svg>
                Print
            </a>
            <a href="{{ route('employees.create') }}" class="emp-btn-primary">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Add Employee
            </a>
        </div>
    </div>

    {{-- Section head + filter --}}
    <div class="emp-section-head">
        <h2 class="emp-section-title"><span class="emp-dot"></span> All Employees</h2>
    </div>

    <div class="emp-filter-bar">
        <div class="emp-search-wrap">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
            <input type="text" class="emp-search-input" id="empSearch" placeholder="Search by name">
        </div>
        <select class="emp-filter-select" id="empStatusFilter">
            <option value="">All Statuses</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
        <select class="emp-filter-select" id="empDeptFilter">
            <option value="">All Departments</option>
            @foreach($departments as $dept)
                <option value="{{ strtolower($dept) }}">{{ $dept }}</option>
            @endforeach
        </select>
    </div>

    {{-- Table --}}
    <div class="emp-table-card">
        <div class="emp-table-scroll">
            <table class="emp-table">
                <thead>
                    <tr>
                        <th>
                            <a href="{{ route('employees.index', ['sort_by' => 'first_name', 'sort_order' => ($sortBy === 'first_name' && $sortOrder === 'asc') ? 'desc' : 'asc']) }}">
                                Employee
                                @if($sortBy === 'first_name')
                                    @if($sortOrder === 'asc')
                                        <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
                                    @else
                                        <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                    @endif
                                @else
                                    <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="opacity:0.3"><path stroke-linecap="round" stroke-linejoin="round" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/></svg>
                                @endif
                            </a>
                        </th>
                        <th>Gender</th>
                        <th>Department</th>
                        <th>Contact Number</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody id="empTbody">
                    @forelse($employees as $employee)
                        @php
                            $initials = strtoupper(substr($employee->first_name ?? 'U', 0, 1) . substr($employee->last_name ?? '', 0, 1));
                            $sc = $employee->status === 'active' ? 's-active' : 's-inactive';
                        @endphp
                        
                        <tr data-name="{{ strtolower(($employee->first_name ?? '') . ' ' . ($employee->last_name ?? '')) }}"
                            data-status="{{ $employee->status }}"
                            data-dept="{{ strtolower($employee->department ?? '') }}"
                            data-href="{{ route('employees.show', $employee) }}"
                            onclick="if(!event.target.closest('a,button,form'))window.location=this.dataset.href">
                            <td>
                                <div class="emp-cell">
                                    <div class="emp-avatar">{{ $initials }}</div>
                                    <div>
                                        <div class="emp-name">{{ $employee->first_name }} {{ $employee->last_name }}</div>
                                        <div class="emp-pos">{{ $employee->position ?? '—' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span style="font-size:0.835rem;color:#6b7280;">{{ $employee->gender ? ucwords(str_replace('_', ' ', $employee->gender)) : '—' }}</span></td>
                            <td><span class="emp-dept">{{ $employee->department ?? '—' }}</span></td>
                            <td><span style="font-family:'DM Mono',monospace;font-size:0.835rem;color:#374151;">{{ $employee->phone ?? '—' }}</span></td>
                            <td class="text-center">
                                <span class="emp-status {{ $sc }}">{{ ucfirst($employee->status) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5">
                            <div class="emp-empty">
                                <div class="emp-empty-icon">
                                    <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <p class="emp-empty-title">No employees found</p>
                                <p class="emp-empty-sub">Add your first employee to get started.</p>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div id="empNoResults">
            <div class="emp-empty" style="padding:32px;">
                <p class="emp-empty-title">No results found</p>
                <p class="emp-empty-sub">Try a different search or filter.</p>
            </div>
        </div>
    </div>
    {{-- Pagination --}}
    <div class="emp-pagination-strip">
        <div class="emp-pagination-info" id="empPaginationInfo">
            Showing <strong>1</strong>–<strong>10</strong> of <strong>0</strong> employees
        </div>

        <nav id="empPaginationNav"></nav>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ── Build complete employee data ──────────────────────────────────
window.allEmployeesData = {!! json_encode($allEmployees->map(fn($e) => [
    'id' => $e->id,
    'firstName' => $e->first_name,
    'lastName' => $e->last_name,
    'name' => strtolower(($e->first_name ?? '') . ' ' . ($e->last_name ?? '')),
    'gender' => $e->gender,
    'position' => $e->position,
    'department' => strtolower($e->department ?? ''),
    'departmentDisplay' => $e->department,
    'phone' => $e->phone,
    'status' => $e->status,
])) !!};

// ── Client-side filter and pagination ─────────────────────────────
(function () {
    const search = document.getElementById('empSearch');
    const statusF = document.getElementById('empStatusFilter');
    const deptF   = document.getElementById('empDeptFilter');
    const tbody   = document.getElementById('empTbody');
    const noRes   = document.getElementById('empNoResults');
    const paginationStrip = document.querySelector('.emp-pagination-strip');
    
    let currentPage = 1;
    const itemsPerPage = 10;
    let filteredData = [];

    function applyFilters() {
        const q = search.value.toLowerCase().trim();
        const st = statusF.value;
        const dt = deptF.value;

        filteredData = window.allEmployeesData.filter(emp => {
            const matchesSearch = !q || emp.name.includes(q);
            const matchesStatus = !st || emp.status === st;
            const matchesDept = !dt || emp.department === dt;
            return matchesSearch && matchesStatus && matchesDept;
        });

        currentPage = 1;
        renderTable();
    }

    function renderTable() {
        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const pageData = filteredData.slice(start, end);
        
        tbody.innerHTML = '';

        if (pageData.length === 0) {
            if (filteredData.length === 0 && window.allEmployeesData.length > 0) {
                noRes.style.display = 'block';
            } else if (window.allEmployeesData.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5"><div class="emp-empty"><div class="emp-empty-icon"><svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div><p class="emp-empty-title">No employees found</p><p class="emp-empty-sub">Add your first employee to get started.</p></div></td></tr>';
            }
        } else {
            noRes.style.display = 'none';
            pageData.forEach(emp => {
                const initials = (emp.firstName.charAt(0) + emp.lastName.charAt(0)).toUpperCase();
                const sc = emp.status === 'active' ? 's-active' : 's-inactive';
                const empRoute = '/employees/' + emp.id;

                const row = document.createElement('tr');
                row.dataset.name = emp.name;
                row.dataset.status = emp.status;
                row.dataset.dept = emp.department;
                row.dataset.href = empRoute;
                row.style.cursor = 'pointer';
                row.onclick = (e) => {
                    if (!e.target.closest('a,button,form')) {
                        window.location = empRoute;
                    }
                };

                row.innerHTML = `
                    <td>
                        <div class="emp-cell">
                            <div class="emp-avatar">${initials}</div>
                            <div>
                                <div class="emp-name">${emp.firstName} ${emp.lastName}</div>
                                <div class="emp-pos">${emp.position ?? '—'}</div>
                            </div>
                        </div>
                    </td>
                    <td><span style="font-size:0.835rem;color:#6b7280;">${emp.gender ? emp.gender.charAt(0).toUpperCase() + emp.gender.slice(1).replace('_', ' ') : '—'}</span></td>
                    <td><span class="emp-dept">${emp.departmentDisplay ? emp.departmentDisplay.charAt(0).toUpperCase() + emp.departmentDisplay.slice(1) : '—'}</span></td>
                    <td><span style="font-family:'DM Mono',monospace;font-size:0.835rem;color:#374151;">${emp.phone ?? '—'}</span></td>
                    <td class="text-center">
                        <span class="emp-status ${sc}">${emp.status.charAt(0).toUpperCase() + emp.status.slice(1)}</span>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        updatePagination();
    }

    function updatePagination() {
        const totalPages = Math.ceil(filteredData.length / itemsPerPage);

        const info = document.getElementById('empPaginationInfo');
        const nav = document.getElementById('empPaginationNav');

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
    statusF.addEventListener('change', applyFilters);
    deptF.addEventListener('change', applyFilters);

    // Initial render
    applyFilters();
})();
</script>
@endpush