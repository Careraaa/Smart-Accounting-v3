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
.emp-avatar { width:32px;height:32px;border-radius:50%;background:#f3f4f6;display:flex;align-items:center;justify-content:center;font-size:0.72rem;font-weight:700;color:#6b7280;flex-shrink:0;border:1.5px solid #e5e7eb;text-transform:uppercase; }
.emp-name   { font-weight:600;color:#111827;font-size:0.845rem; }
.emp-dept   { font-size:0.72rem;color:#9ca3af;margin-top:1px; }

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
.emp-pagination-strip { display:flex;justify-content:space-between;align-items:center;padding:12px 16px;border-top:1px solid #f3f4f6;background:#fafafa; }
.emp-pagination-info  { font-size:0.75rem;color:#9ca3af; }
.emp-pagination-info strong { color:#374151; }

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
            <option value="admin">Admin</option>
            <option value="operation">Operation</option>
            <option value="hr">HR Department</option>
            <option value="accounting">Accounting Department</option>
        </select>
    </div>

    {{-- Table --}}
    <div class="emp-table-card">
        <div class="emp-table-scroll">
            <table class="emp-table">
                <thead>
                    <tr>
                        @php
                            $headers = [
                                'first_name'  => 'First Name',
                                'last_name'   => 'Last Name',
                                'gender'      => 'Gender',
                                'position'    => 'Position',
                                'department'  => 'Department',
                            ];
                        @endphp
                        @foreach($headers as $column => $label)
                        <th>
                            <a href="{{ route('employees.index', ['sort_by' => $column, 'sort_order' => ($sortBy === $column && $sortOrder === 'asc') ? 'desc' : 'asc']) }}">
                                {{ $label }}
                                @if($sortBy === $column)
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
                        @endforeach
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
                                    <div class="emp-name">{{ $employee->first_name }}</div>
                                </div>
                            </td>
                            <td><span style="color:#111827;font-weight:600;font-size:0.845rem;">{{ $employee->last_name }}</span></td>
                            <td><span style="font-size:0.835rem;color:#6b7280;">{{ $employee->gender ? ucwords(str_replace('_', ' ', $employee->gender)) : '—' }}</span></td>
                            <td><span style="color:#374151;font-size:0.835rem;">{{ $employee->position ?? '—' }}</span></td>
                            <td><span style="font-size:0.835rem;color:#6b7280;">{{ ucfirst($employee->department ?? '—') }}</span></td>
                            <td class="text-center">
                                <span class="emp-status {{ $sc }}">{{ ucfirst($employee->status) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7">
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

        @if(method_exists($employees, 'hasPages') && $employees->hasPages())
        <div class="emp-pagination-strip">
            <div class="emp-pagination-info">
                Showing <strong>{{ $employees->firstItem() }}</strong>–<strong>{{ $employees->lastItem() }}</strong>
                of <strong>{{ $employees->total() }}</strong> employees
            </div>
            {{ $employees->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
(function () {
    const search = document.getElementById('empSearch');
    const statusF = document.getElementById('empStatusFilter');
    const deptF   = document.getElementById('empDeptFilter');
    const tbody   = document.getElementById('empTbody');
    const noRes   = document.getElementById('empNoResults');

    function run() {
        const q  = search.value.toLowerCase().trim();
        const st = statusF.value;
        const dt = deptF.value;
        const rows = Array.from(tbody.querySelectorAll('tr[data-name]'));
        const vis = rows.filter(r =>
            (!q  || r.dataset.name.includes(q)) &&
            (!st || r.dataset.status === st) &&
            (!dt || r.dataset.dept === dt)
        );
        rows.forEach(r => r.style.display = 'none');
        vis.forEach(r => r.style.display = '');
        noRes.style.display = vis.length === 0 && rows.length > 0 ? 'block' : 'none';
    }

    search.addEventListener('input', run);
    statusF.addEventListener('change', run);
    deptF.addEventListener('change', run);
})();
</script>
@endpush