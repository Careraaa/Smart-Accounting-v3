@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
.prl-page { font-family: 'Sora', sans-serif; }

.prl-topbar { display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px;flex-wrap:wrap; }
.prl-topbar-title { font-size:1.35rem;font-weight:800;color:#111827;letter-spacing:-0.02em;margin:0 0 2px; }
.prl-topbar-sub   { font-size:0.78rem;color:#9ca3af;margin:0; }

.prl-filter-bar { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:12px 16px;display:flex;align-items:center;gap:10px;margin-bottom:14px;flex-wrap:wrap; }
.prl-search-wrap { position:relative;flex:1;min-width:200px; }
.prl-search-wrap svg { position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#9ca3af;pointer-events:none; }
.prl-search-input { width:100%;border:1px solid #e5e7eb;border-radius:8px;padding:8px 12px 8px 34px;font-size:0.82rem;font-family:'Sora',sans-serif;color:#111827;background:#f9fafb;outline:none;transition:border-color 0.15s,background 0.15s; }
.prl-search-input:focus { border-color:#c8292a;background:#fff;box-shadow:0 0 0 3px rgba(200,41,42,0.08); }
.prl-filter-select { border:1px solid #e5e7eb;border-radius:8px;padding:8px 12px;font-size:0.82rem;font-family:'Sora',sans-serif;color:#374151;background:#f9fafb;outline:none;cursor:pointer; }
.prl-btn-filter { display:inline-flex;align-items:center;gap:6px;padding:8px 14px;background:#111827;color:#fff;border:none;border-radius:8px;font-family:'Sora',sans-serif;font-size:0.82rem;font-weight:600;cursor:pointer;transition:background 0.15s;text-decoration:none; }
.prl-btn-filter:hover { background:#000;color:#fff; }
.prl-btn-clear { display:inline-flex;align-items:center;gap:6px;padding:8px 14px;background:#fff;color:#6b7280;border:1px solid #e5e7eb;border-radius:8px;font-family:'Sora',sans-serif;font-size:0.82rem;font-weight:600;cursor:pointer;transition:all 0.15s;text-decoration:none; }
.prl-btn-clear:hover { border-color:#c8292a;color:#c8292a;background:#fff5f5; }

.prl-section-head { display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;flex-wrap:wrap;gap:10px; }
.prl-section-title { font-size:0.88rem;font-weight:700;color:#111827;margin:0;display:flex;align-items:center;gap:8px; }
.prl-dot { width:8px;height:8px;border-radius:50%;background:#c8292a;display:inline-block; }

.prl-table-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden; }
.prl-table { width:100%;border-collapse:collapse;font-size:0.835rem; }
.prl-table thead tr { background:#f8f9fb;border-bottom:1px solid #e5e7eb; }
.prl-table thead th { padding:11px 16px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.09em;color:#6b7280;white-space:nowrap;font-family:'Sora',sans-serif; }
.prl-table tbody tr { border-bottom:1px solid #f3f4f6;transition:background 0.1s; }
.prl-table tbody tr:last-child { border-bottom:none; }
.prl-table tbody tr:hover { background:#fafafa; }
.prl-table tbody td { padding:12px 16px;color:#374151;vertical-align:middle; }
.prl-table-scroll { overflow-x:auto; }
.prl-table-footer { padding:12px 16px;border-top:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px; }
.prl-table-footer-note { font-size:0.78rem;color:#9ca3af; }
.prl-table-footer-note strong { color:#374151; }

.prl-emp-cell { display:flex;align-items:center;gap:10px; }
.prl-emp-avatar { width:32px;height:32px;border-radius:50%;background:#f3f4f6;display:flex;align-items:center;justify-content:center;font-size:0.72rem;font-weight:700;color:#6b7280;flex-shrink:0;border:1.5px solid #e5e7eb;text-transform:uppercase; }
.prl-emp-name { font-weight:600;color:#111827;font-size:0.845rem; }
.prl-emp-dept { font-size:0.72rem;color:#9ca3af;margin-top:1px; }
.prl-mono { font-family:'DM Mono',monospace;font-size:0.82rem;font-variant-numeric:tabular-nums; }
.prl-mono.c-bold  { color:#111827;font-weight:700; }
.prl-mono.c-green { color:#16a34a;font-weight:600; }
.prl-mono.c-muted { color:#9ca3af; }
.prl-period-tag { display:inline-block;font-family:'DM Mono',monospace;font-size:0.72rem;color:#374151;background:#f3f4f6;padding:2px 8px;border-radius:5px; }
.prl-work-meta { font-size:0.72rem;color:#9ca3af;margin-top:2px; }

.prl-status { display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;white-space:nowrap; }
.prl-status::before { content:'';width:5px;height:5px;border-radius:50%; }
.prl-status.s-approved { background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0; }
.prl-status.s-approved::before { background:#16a34a; }
.prl-status.s-released { background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe; }
.prl-status.s-released::before { background:#3b82f6; }
.prl-status.s-pending  { background:#fffbeb;color:#d97706;border:1px solid #fde68a; }
.prl-status.s-pending::before { background:#d97706; }
.prl-status.s-default  { background:#f3f4f6;color:#6b7280; }
.prl-status.s-default::before { background:#9ca3af; }

.prl-actions { display:flex;align-items:center;gap:5px;justify-content:flex-end; }
.prl-action-btn { width:30px;height:30px;border-radius:7px;border:none;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;text-decoration:none;font-size:13px;transition:background 0.13s,color 0.13s;background:#f4f5f7;color:#6b7280;padding:0; }
.prl-action-btn:hover { background:#eff6ff;color:#3b82f6; }

.prl-empty { display:flex;flex-direction:column;align-items:center;justify-content:center;padding:56px 24px;text-align:center; }
.prl-empty-icon { width:56px;height:56px;background:#f3f4f6;border-radius:16px;display:flex;align-items:center;justify-content:center;margin-bottom:14px;color:#d1d5db; }
.prl-empty-title { font-size:0.9rem;font-weight:700;color:#374151;margin:0 0 6px; }
.prl-empty-sub   { font-size:0.78rem;color:#9ca3af;margin:0; }
</style>
@endpush

@section('content')
<div class="prl-page">

    <div class="prl-topbar">
        <div>
            <h1 class="prl-topbar-title">Generate Payslips</h1>
            <p class="prl-topbar-sub">Search and download individual employee payslips</p>
        </div>
    </div>

    <div class="prl-section-head">
        <h2 class="prl-section-title"><span class="prl-dot"></span> Payroll Records</h2>
    </div>

    {{-- Filter bar --}}
    <form method="GET" action="{{ route('payroll.generate-payslip.index') }}" id="filterForm">
        <div class="prl-filter-bar">
            <div class="prl-search-wrap">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
                <input type="text" id="employeeSearch" class="prl-search-input"
                    placeholder="Search employee…" autocomplete="off" list="employeeList"
                    value="{{ request('user_id') ? $employees->firstWhere('id', request('user_id'))?->name : '' }}">
                <input type="hidden" name="user_id" id="employeeId" value="{{ request('user_id') }}">
                <datalist id="employeeList">
                    @foreach($employees as $emp)
                        <option data-id="{{ $emp->id }}"
                            value="{{ $emp->name }}{{ $emp->position ? ' — ' . $emp->position : '' }}">
                    @endforeach
                </datalist>
            </div>
            <button type="submit" class="prl-btn-filter">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                Filter
            </button>
            <a href="{{ route('payroll.generate-payslip.index') }}" class="prl-btn-clear">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                Clear
            </a>
        </div>
    </form>

    <div class="prl-table-card">
        <div class="prl-table-scroll">
            <table class="prl-table">
                <thead><tr>
                    <th>Employee</th>
                    <th>Pay Period</th>
                    <th class="text-end">Basic Salary</th>
                    <th class="text-end">Gross Pay</th>
                    <th class="text-end">Net Pay</th>
                    <th class="text-center">Status</th>
                    <th class="text-end">Actions</th>
                </tr></thead>
                <tbody>
                @forelse($payrolls as $payroll)
                    @php
                        $initials = strtoupper(substr($payroll->user->first_name ?? ($payroll->user->name ?? 'U'), 0, 1) . substr($payroll->user->last_name ?? '', 0, 1));
                        $sc = match($payroll->status) {
                            'approved' => 's-approved',
                            'released', 'paid' => 's-released',
                            'pending'  => 's-pending',
                            default    => 's-default',
                        };
                    @endphp
                    <tr>
                        <td>
                            <div class="prl-emp-cell">
                                <div class="prl-emp-avatar">{{ $initials }}</div>
                                <div>
                                    <div class="prl-emp-name">{{ $payroll->user->first_name ?? $payroll->user->name ?? 'N/A' }} {{ $payroll->user->last_name ?? '' }}</div>
                                    <div class="prl-emp-dept">{{ $payroll->user->position ?? '—' }} · {{ $payroll->user->department ?? '—' }} · ID #{{ str_pad($payroll->user_id, 4, '0', STR_PAD_LEFT) }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="prl-period-tag">{{ $payroll->payroll_period_start->format('M d') }} – {{ $payroll->payroll_period_end->format('M d, Y') }}</span>
                            <div class="prl-work-meta">{{ $payroll->days_worked ?? 0 }} days · {{ number_format($payroll->hours_worked ?? 0, 1) }} hrs</div>
                        </td>
                        <td class="text-end"><span class="prl-mono c-muted">₱{{ number_format($payroll->basic_salary, 2) }}</span></td>
                        <td class="text-end"><span class="prl-mono c-green">₱{{ number_format($payroll->gross_pay, 2) }}</span></td>
                        <td class="text-end"><span class="prl-mono c-bold">₱{{ number_format($payroll->net_pay, 2) }}</span></td>
                        <td class="text-center"><span class="prl-status {{ $sc }}">{{ in_array($payroll->status, ['released', 'paid'], true) ? 'Released' : ucfirst($payroll->status) }}</span></td>
                        <td>
                            <div class="prl-actions">
                                <a href="{{ route('payroll.generatePayslip', $payroll) }}" class="prl-action-btn" title="View Payslip" target="_blank">
                                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7">
                        <div class="prl-empty">
                            <div class="prl-empty-icon"><svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
                            <p class="prl-empty-title">No payroll records found</p>
                            <p class="prl-empty-sub">Try adjusting your search filter.</p>
                        </div>
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($payrolls->hasPages())
        <div class="prl-table-footer">
            <div class="prl-table-footer-note">
                Showing <strong>{{ $payrolls->firstItem() }}</strong> – <strong>{{ $payrolls->lastItem() }}</strong> of <strong>{{ $payrolls->total() }}</strong>
            </div>
            {{ $payrolls->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('employeeSearch');
    const hiddenId    = document.getElementById('employeeId');
    const datalist    = document.getElementById('employeeList');
    const form        = document.getElementById('filterForm');
    const optionMap   = {};
    Array.from(datalist.options).forEach(opt => { optionMap[opt.value] = opt.getAttribute('data-id'); });
    searchInput.addEventListener('change', function () { hiddenId.value = optionMap[this.value] ?? ''; });
    searchInput.addEventListener('input',  function () { if (!this.value) hiddenId.value = ''; });
    form.addEventListener('submit', function () { if (!optionMap[searchInput.value]) hiddenId.value = ''; });
});
</script>
@endpush