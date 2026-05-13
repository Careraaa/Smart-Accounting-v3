@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
.prl-page { font-family: 'Sora', sans-serif; }

.prl-topbar { display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px;flex-wrap:wrap; }
.prl-topbar-title { font-size:1.35rem;font-weight:800;color:#111827;letter-spacing:-0.02em;margin:0 0 2px; }
.prl-topbar-sub   { font-size:0.78rem;color:#9ca3af;margin:0; }
.prl-topbar-actions { display:flex;gap:8px;flex-wrap:wrap;align-items:center; }

.prl-btn-sec {
    display:inline-flex;align-items:center;gap:7px;padding:9px 16px;background:#fff;color:#374151;
    border:1px solid #e5e7eb;border-radius:10px;font-family:'Sora',sans-serif;font-size:0.82rem;
    font-weight:600;text-decoration:none;cursor:pointer;transition:all 0.15s;white-space:nowrap;
}
.prl-btn-sec:hover { border-color:#c8292a;color:#c8292a;background:#fff5f5; }

/* Stats */
.prl-stats { display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:24px; }
@media (max-width:1100px) { .prl-stats { grid-template-columns:repeat(2,1fr); } }
@media (max-width:600px)  { .prl-stats { grid-template-columns:1fr; } }
.prl-stat { background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:18px 20px;display:flex;align-items:flex-start;gap:14px;position:relative;overflow:hidden;transition:box-shadow 0.15s; }
.prl-stat:hover { box-shadow:0 4px 20px rgba(0,0,0,0.07); }
.prl-stat::after { content:'';position:absolute;bottom:0;left:0;right:0;height:3px;border-radius:0 0 14px 14px; }
.prl-stat.s-red::after   { background:#c8292a; }
.prl-stat.s-green::after { background:#16a34a; }
.prl-stat.s-amber::after { background:#d97706; }
.prl-stat.s-blue::after  { background:#0284c7; }
.prl-stat-icon { width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.prl-stat.s-red   .prl-stat-icon { background:#fff0f0;color:#c8292a; }
.prl-stat.s-green .prl-stat-icon { background:#f0fdf4;color:#16a34a; }
.prl-stat.s-amber .prl-stat-icon { background:#fffbeb;color:#d97706; }
.prl-stat.s-blue  .prl-stat-icon { background:#f0f9ff;color:#0284c7; }
.prl-stat-label { font-size:0.67rem;font-weight:700;text-transform:uppercase;letter-spacing:0.09em;color:#9ca3af;margin-bottom:4px; }
.prl-stat-value { font-size:1.6rem;font-weight:800;color:#111827;line-height:1;font-variant-numeric:tabular-nums;font-family:'DM Mono',monospace; }
.prl-stat-sub   { font-size:0.73rem;color:#9ca3af;margin-top:4px; }

/* Summary banner */
.prl-summary-banner {
    background:#111827;border-radius:16px;padding:20px 28px;
    display:flex;align-items:center;justify-content:space-between;gap:20px;
    margin-bottom:24px;flex-wrap:wrap;position:relative;overflow:hidden;
}
.prl-summary-banner::before { content:'';position:absolute;top:-50px;right:-50px;width:180px;height:180px;border-radius:50%;background:rgba(200,41,42,0.12);pointer-events:none; }
.prl-summary-left { position:relative;z-index:1; }
.prl-summary-eyebrow { font-size:0.67rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#c8292a;margin-bottom:4px; }
.prl-summary-title { font-size:1rem;font-weight:800;color:#fff;margin:0 0 4px;letter-spacing:-0.01em; }
.prl-summary-period { font-size:0.82rem;color:#6b7280;font-family:'DM Mono',monospace; }
.prl-summary-right { position:relative;z-index:1;display:flex;gap:28px;flex-wrap:wrap; }
.prl-summary-stat { text-align:right; }
.prl-summary-stat-label { font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#6b7280;margin-bottom:2px; }
.prl-summary-stat-value { font-size:0.92rem;font-weight:800;color:#fff;font-family:'DM Mono',monospace; }

/* Section */
.prl-section-head { display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;flex-wrap:wrap;gap:10px; }
.prl-section-title { font-size:0.88rem;font-weight:700;color:#111827;margin:0;display:flex;align-items:center;gap:8px; }
.prl-dot { width:8px;height:8px;border-radius:50%;background:#c8292a;display:inline-block; }

/* Filter bar */
.prl-filter-bar { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:12px 16px;display:flex;align-items:center;gap:10px;margin-bottom:14px;flex-wrap:wrap; }
.prl-search-wrap { position:relative;flex:1;min-width:180px; }
.prl-search-wrap svg { position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#9ca3af;pointer-events:none; }
.prl-search-input { width:100%;border:1px solid #e5e7eb;border-radius:8px;padding:8px 12px 8px 34px;font-size:0.82rem;font-family:'Sora',sans-serif;color:#111827;background:#f9fafb;outline:none;transition:border-color 0.15s; }
.prl-search-input:focus { border-color:#c8292a;background:#fff;box-shadow:0 0 0 3px rgba(200,41,42,0.08); }
.prl-filter-select { border:1px solid #e5e7eb;border-radius:8px;padding:8px 12px;font-size:0.82rem;font-family:'Sora',sans-serif;color:#374151;background:#f9fafb;outline:none;cursor:pointer; }

/* Table */
.prl-table-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden; }
.prl-table { width:100%;border-collapse:collapse;font-size:0.835rem; }
.prl-table thead tr { background:#f8f9fb;border-bottom:1px solid #e5e7eb; }
.prl-table thead th { padding:11px 16px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.09em;color:#6b7280;white-space:nowrap;font-family:'Sora',sans-serif; }
.prl-table tbody tr { border-bottom:1px solid #f3f4f6;transition:background 0.1s; }
.prl-table tbody tr:last-child { border-bottom:none; }
.prl-table tbody tr:hover { background:#fafafa; }
.prl-table tbody td { padding:12px 16px;color:#374151;vertical-align:middle; }
.prl-table-scroll { overflow-x:auto; }
.prl-table-footer { padding:14px 18px;border-top:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px; }
.prl-table-footer-note { font-size:0.78rem;color:#9ca3af; }
.prl-table-footer-note strong { color:#374151; }

/* Employee cell */
.prl-emp-cell { display:flex;align-items:center;gap:10px; }
.prl-emp-avatar { width:32px;height:32px;border-radius:50%;background:#f3f4f6;display:flex;align-items:center;justify-content:center;font-size:0.72rem;font-weight:700;color:#6b7280;flex-shrink:0;border:1.5px solid #e5e7eb;text-transform:uppercase; }
.prl-emp-name { font-weight:600;color:#111827;font-size:0.845rem; }
.prl-emp-dept { font-size:0.72rem;color:#9ca3af;margin-top:1px; }

.prl-dept-tag { display:inline-block;font-size:0.7rem;font-weight:600;color:#374151;background:#f3f4f6;padding:2px 8px;border-radius:5px; }
.prl-mono { font-family:'DM Mono',monospace;font-size:0.82rem;font-variant-numeric:tabular-nums; }
.prl-mono.c-bold { color:#111827;font-weight:700; }
.prl-mono.c-red  { color:#c8292a;font-weight:500; }
.prl-mono.c-muted { color:#9ca3af; }

/* Status */
.prl-status { display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;white-space:nowrap; }
.prl-status::before { content:'';width:5px;height:5px;border-radius:50%; }
.prl-status.s-pending   { background:#fffbeb;color:#d97706;border:1px solid #fde68a; }
.prl-status.s-pending::before   { background:#d97706; }
.prl-status.s-finalized { background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe; }
.prl-status.s-finalized::before { background:#3b82f6; }
.prl-status.s-submitted { background:#f5f3ff;color:#7c3aed;border:1px solid #ddd6fe; }
.prl-status.s-submitted::before { background:#8b5cf6; }
.prl-status.s-approved  { background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0; }
.prl-status.s-approved::before  { background:#16a34a; }
.prl-status.s-released  { background:#f0fdf4;color:#15803d;border:1px solid #86efac; }
.prl-status.s-released::before  { background:#22c55e; }
.prl-status.s-rejected  { background:#fff0f0;color:#c8292a;border:1px solid #fecaca; }
.prl-status.s-rejected::before  { background:#ef4444; }

/* Actions */
.prl-actions { display:flex;align-items:center;gap:5px;justify-content:flex-end; }
.prl-action-btn { width:30px;height:30px;border-radius:7px;border:none;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;text-decoration:none;font-size:13px;transition:background 0.13s,color 0.13s;background:#f4f5f7;color:#6b7280;padding:0; }
.prl-action-btn:hover { background:#eff6ff;color:#3b82f6; }

/* Empty */
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
            <h1 class="prl-topbar-title">Batch Details</h1>
            <p class="prl-topbar-sub">Period: {{ $startDate->format('M d, Y') }} – {{ $endDate->format('M d, Y') }}</p>
        </div>
        <div class="prl-topbar-actions">
            <a href="{{ route('payroll.history.index') }}" class="prl-btn-sec">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Back to History
            </a>
            @if(isset($batch) && $batch && $batch->status === 'rejected')
                <a href="{{ route('payroll.batch.confirm', $batch) }}" class="prl-btn-sec" style="border-color:#fecaca;color:#c8292a;">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v6h6M20 20v-6h-6"/><path stroke-linecap="round" stroke-linejoin="round" d="M20 8a8 8 0 00-14.828-3M4 16a8 8 0 0014.828 3"/></svg>
                    Resubmit
                </a>
            @endif
        </div>
    </div>

    {{-- Stats --}}
    <div class="prl-stats">
        <div class="prl-stat s-blue">
            <div class="prl-stat-icon">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <div class="prl-stat-label">Total Employees</div>
                <div class="prl-stat-value">{{ $payrolls->count() }}</div>
                <div class="prl-stat-sub">in this batch</div>
            </div>
        </div>
        <div class="prl-stat s-amber">
            <div class="prl-stat-icon">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="prl-stat-label">Gross Pay</div>
                <div class="prl-stat-value" style="font-size:1rem;">₱{{ number_format($totalGross,2) }}</div>
                <div class="prl-stat-sub">total gross salary</div>
            </div>
        </div>
        <div class="prl-stat s-red">
            <div class="prl-stat-icon">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="prl-stat-label">Deductions</div>
                <div class="prl-stat-value" style="font-size:1rem;">₱{{ number_format($totalDeductions,2) }}</div>
                <div class="prl-stat-sub">total deductions</div>
            </div>
        </div>
        <div class="prl-stat s-green">
            <div class="prl-stat-icon">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="prl-stat-label">Net Pay</div>
                <div class="prl-stat-value" style="font-size:1rem;">₱{{ number_format($totalNetPay,2) }}</div>
                <div class="prl-stat-sub">amount to release</div>
            </div>
        </div>
    </div>

    {{-- Summary banner --}}
    <div class="prl-summary-banner">
        <div class="prl-summary-left">
            <div class="prl-summary-eyebrow">Batch Summary</div>
            <h2 class="prl-summary-title">{{ $startDate->format('M d') }} – {{ $endDate->format('M d, Y') }}</h2>
            <div class="prl-summary-period">{{ $payrolls->count() }} records · {{ $releasedCount }} released · {{ $pendingCount }} pending</div>
        </div>
        <div class="prl-summary-right">
            <div class="prl-summary-stat">
                <div class="prl-summary-stat-label">Released</div>
                <div class="prl-summary-stat-value">{{ $releasedCount }}</div>
            </div>
            <div class="prl-summary-stat">
                <div class="prl-summary-stat-label">Pending</div>
                <div class="prl-summary-stat-value">{{ $pendingCount }}</div>
            </div>
            <div class="prl-summary-stat">
                <div class="prl-summary-stat-label">Release Amount</div>
                <div class="prl-summary-stat-value">₱{{ number_format($totalNetPay,2) }}</div>
            </div>
        </div>
    </div>

    {{-- Records table --}}
    <div class="prl-section-head">
        <h2 class="prl-section-title"><span class="prl-dot"></span> Payroll Records</h2>
    </div>

    <div class="prl-filter-bar">
        <div class="prl-search-wrap">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
            <input type="text" class="prl-search-input" id="batchSearch" placeholder="Search employee…">
        </div>
        <select class="prl-filter-select" id="batchStatusFilter">
            <option value="">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="released">Released</option>
            <option value="rejected">Rejected</option>
        </select>
    </div>

    <div class="prl-table-card">
        <div class="prl-table-scroll">
            <table class="prl-table">
                <thead><tr>
                    <th>Employee</th>
                    <th>Department</th>
                    <th>Position</th>
                    <th class="text-end">Gross Pay</th>
                    <th class="text-end">Deductions</th>
                    <th class="text-end">Net Pay</th>
                    <th class="text-center">Status</th>
                    <th class="text-end">Actions</th>
                </tr></thead>
                <tbody id="batchTbody">
                @forelse($payrolls as $payroll)
                    @php
                        $initials = strtoupper(substr($payroll->user->first_name ?? 'U', 0, 1) . substr($payroll->user->last_name ?? '', 0, 1));
                        $sc = match($payroll->status) {
                            'pending'   => 's-pending',
                            'finalized' => 's-finalized',
                            'submitted' => 's-submitted',
                            'approved'  => 's-approved',
                            'released', 'paid' => 's-released',
                            'rejected'  => 's-rejected',
                            default     => 's-pending',
                        };
                    @endphp
                    <tr data-name="{{ strtolower(($payroll->user->first_name ?? '') . ' ' . ($payroll->user->last_name ?? '')) }}"
                        data-status="{{ $payroll->status }}">
                        <td>
                            <div class="prl-emp-cell">
                                <div class="prl-emp-avatar">{{ $initials }}</div>
                                <div>
                                    <div class="prl-emp-name">{{ $payroll->user->first_name }} {{ $payroll->user->last_name }}</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="prl-dept-tag">{{ $payroll->user->department ?? 'N/A' }}</span></td>
                        <td><span style="font-size:.82rem;color:#374151;">{{ $payroll->user->position ?? 'N/A' }}</span></td>
                        <td class="text-end"><span class="prl-mono c-muted">₱{{ number_format($payroll->gross_pay, 2) }}</span></td>
                        <td class="text-end"><span class="prl-mono c-red">₱{{ number_format($payroll->total_deductions, 2) }}</span></td>
                        <td class="text-end"><span class="prl-mono c-bold">₱{{ number_format($payroll->net_pay, 2) }}</span></td>
                        <td class="text-center"><span class="prl-status {{ $sc }}">{{ in_array($payroll->status, ['released', 'paid'], true) ? 'Released' : ucfirst($payroll->status) }}</span></td>
                        <td>
                            <div class="prl-actions">
                                <a href="{{ route('payroll.salary-computation.show', $payroll) }}" class="prl-action-btn" title="View">
                                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8">
                        <div class="prl-empty">
                            <div class="prl-empty-icon"><svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
                            <p class="prl-empty-title">No payroll records</p>
                            <p class="prl-empty-sub">No records found in this batch.</p>
                        </div>
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div id="batchNoResults" style="display:none;">
            <div class="prl-empty" style="padding:32px;">
                <p class="prl-empty-title">No results found</p>
                <p class="prl-empty-sub">Try a different search or filter.</p>
            </div>
        </div>
        <div class="prl-table-footer">
            <div class="prl-table-footer-note">
                <strong>{{ $releasedCount }}</strong> released &nbsp;·&nbsp; <strong>{{ $pendingCount }}</strong> pending &nbsp;·&nbsp; <strong>{{ $payrolls->count() }}</strong> total
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
(function () {
    const s  = document.getElementById('batchSearch');
    const f  = document.getElementById('batchStatusFilter');
    const tb = document.getElementById('batchTbody');
    const nr = document.getElementById('batchNoResults');
    function run() {
        const q  = s.value.toLowerCase().trim();
        const st = f.value;
        const rows = Array.from(tb.querySelectorAll('tr[data-name]'));
        const vis  = rows.filter(r => (!q || r.dataset.name.includes(q)) && (!st || r.dataset.status === st));
        rows.forEach(r => r.style.display = 'none');
        vis.forEach(r => r.style.display = '');
        nr.style.display = vis.length === 0 && rows.length > 0 ? 'block' : 'none';
    }
    s.addEventListener('input', run);
    f.addEventListener('change', run);
})();
</script>
@endpush