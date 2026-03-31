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

/* Generate hero card */
.prl-generate-card {
    background:#111827;border-radius:16px;padding:28px 32px;
    display:flex;align-items:center;justify-content:space-between;gap:24px;
    margin-bottom:24px;flex-wrap:wrap;position:relative;overflow:hidden;
}
.prl-generate-card::before { content:'';position:absolute;top:-60px;right:-60px;width:200px;height:200px;border-radius:50%;background:rgba(200,41,42,0.15);pointer-events:none; }
.prl-generate-left { position:relative;z-index:1; }
.prl-generate-eyebrow { font-size:0.67rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#c8292a;margin-bottom:6px; }
.prl-generate-title { font-size:1.15rem;font-weight:800;color:#fff;margin:0 0 6px;letter-spacing:-0.02em; }
.prl-generate-period { font-size:0.82rem;color:#6b7280;font-family:'DM Mono',monospace; }
.prl-generate-right { position:relative;z-index:1;display:flex;flex-direction:column;align-items:flex-end;gap:8px; }

.prl-btn-generate {
    display:inline-flex;align-items:center;gap:10px;padding:13px 28px;background:#c8292a;color:#fff;
    border:none;border-radius:12px;font-family:'Sora',sans-serif;font-size:0.9rem;font-weight:700;
    cursor:pointer;transition:background 0.15s,box-shadow 0.15s;
    box-shadow:0 4px 20px rgba(200,41,42,0.5);white-space:nowrap;text-decoration:none;
}
.prl-btn-generate:hover { background:#a81f20;color:#fff;box-shadow:0 8px 28px rgba(200,41,42,0.6); }
.prl-btn-generate.disabled { background:#374151;box-shadow:none;cursor:not-allowed;pointer-events:none; }

.prl-already-badge { font-size:0.72rem;color:#6b7280;display:flex;align-items:center;gap:5px; }
.prl-already-badge a { color:#c8292a;text-decoration:none;font-weight:700; }
.prl-already-badge a:hover { text-decoration:underline; }

.prl-two-col { display:grid;grid-template-columns:1fr 360px;gap:16px;align-items:start; }
@media (max-width:900px) { .prl-two-col { grid-template-columns:1fr; } }

.prl-section-head { display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;flex-wrap:wrap;gap:10px; }
.prl-section-title { font-size:0.88rem;font-weight:700;color:#111827;margin:0;display:flex;align-items:center;gap:8px; }
.prl-dot { width:8px;height:8px;border-radius:50%;background:#c8292a;display:inline-block; }

.prl-filter-bar { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:12px 16px;display:flex;align-items:center;gap:10px;margin-bottom:14px;flex-wrap:wrap; }
.prl-search-wrap { position:relative;flex:1;min-width:180px; }
.prl-search-wrap svg { position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#9ca3af;pointer-events:none; }
.prl-search-input { width:100%;border:1px solid #e5e7eb;border-radius:8px;padding:8px 12px 8px 34px;font-size:0.82rem;font-family:'Sora',sans-serif;color:#111827;background:#f9fafb;outline:none;transition:border-color 0.15s,background 0.15s; }
.prl-search-input:focus { border-color:#c8292a;background:#fff;box-shadow:0 0 0 3px rgba(200,41,42,0.08); }
.prl-filter-select { border:1px solid #e5e7eb;border-radius:8px;padding:8px 12px;font-size:0.82rem;font-family:'Sora',sans-serif;color:#374151;background:#f9fafb;outline:none;cursor:pointer;transition:border-color 0.15s; }
.prl-filter-select:focus { border-color:#c8292a; }

.prl-table-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden; }
.prl-table { width:100%;border-collapse:collapse;font-size:0.835rem; }
.prl-table thead tr { background:#f8f9fb;border-bottom:1px solid #e5e7eb; }
.prl-table thead th { padding:11px 16px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.09em;color:#6b7280;white-space:nowrap;font-family:'Sora',sans-serif; }
.prl-table tbody tr { border-bottom:1px solid #f3f4f6;transition:background 0.1s; }
.prl-table tbody tr:last-child { border-bottom:none; }
.prl-table tbody tr:hover { background:#fafafa; }
.prl-table tbody td { padding:12px 16px;color:#374151;vertical-align:middle; }

.prl-emp-cell { display:flex;align-items:center;gap:10px; }
.prl-emp-avatar { width:32px;height:32px;border-radius:50%;background:#f3f4f6;display:flex;align-items:center;justify-content:center;font-size:0.72rem;font-weight:700;color:#6b7280;flex-shrink:0;border:1.5px solid #e5e7eb;text-transform:uppercase; }
.prl-emp-name { font-weight:600;color:#111827;font-size:0.845rem; }
.prl-emp-dept { font-size:0.72rem;color:#9ca3af;margin-top:1px; }
.prl-mono { font-family:'DM Mono',monospace;font-size:0.82rem;font-variant-numeric:tabular-nums; }
.prl-mono.c-red  { color:#c8292a;font-weight:500; }
.prl-mono.c-bold { color:#111827;font-weight:600; }
.prl-period-tag { display:inline-block;font-family:'DM Mono',monospace;font-size:0.72rem;color:#6b7280;background:#f3f4f6;padding:2px 8px;border-radius:4px; }

.prl-status { display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;white-space:nowrap; }
.prl-status::before { content:'';width:5px;height:5px;border-radius:50%; }
.prl-status.s-draft     { background:#f3f4f6;color:#6b7280; }
.prl-status.s-draft::before { background:#9ca3af; }
.prl-status.s-pending   { background:#fffbeb;color:#d97706;border:1px solid #fde68a; }
.prl-status.s-pending::before { background:#d97706; }
.prl-status.s-finalized { background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe; }
.prl-status.s-finalized::before { background:#3b82f6; }
.prl-status.s-submitted { background:#f5f3ff;color:#7c3aed;border:1px solid #ddd6fe; }
.prl-status.s-submitted::before { background:#8b5cf6; }
.prl-status.s-approved  { background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0; }
.prl-status.s-approved::before { background:#16a34a; }
.prl-status.s-paid      { background:#f0fdf4;color:#15803d;border:1px solid #86efac; }
.prl-status.s-paid::before { background:#22c55e; }
.prl-status.s-rejected  { background:#fff0f0;color:#c8292a;border:1px solid #fecaca; }
.prl-status.s-rejected::before { background:#ef4444; }

.prl-actions { display:flex;align-items:center;gap:5px;justify-content:flex-end; }
.prl-action-btn { width:30px;height:30px;border-radius:7px;border:none;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;text-decoration:none;font-size:13px;transition:background 0.13s,color 0.13s;background:#f4f5f7;color:#6b7280;padding:0; }
.prl-action-btn:hover         { background:#eff6ff;color:#3b82f6; }
.prl-action-btn.danger:hover  { background:#fff1f2;color:#e11d48; }
.prl-action-btn.success:hover { background:#f0fdf4;color:#16a34a; }

.prl-side-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden; }
.prl-side-head { padding:14px 18px;border-bottom:1px solid #f3f4f6;font-size:0.82rem;font-weight:700;color:#111827; }
.prl-batch-item { display:flex;align-items:center;justify-content:space-between;gap:10px;padding:12px 18px;border-bottom:1px solid #f3f4f6;transition:background 0.1s;text-decoration:none; }
.prl-batch-item:last-child { border-bottom:none; }
.prl-batch-item:hover { background:#fafafa; }
.prl-batch-period { font-family:'DM Mono',monospace;font-size:0.78rem;color:#111827;font-weight:500; }
.prl-batch-meta   { font-size:0.72rem;color:#9ca3af;margin-top:2px; }
.prl-batch-empty  { padding:24px 18px;text-align:center;color:#9ca3af;font-size:0.78rem; }

.prl-empty { display:flex;flex-direction:column;align-items:center;justify-content:center;padding:56px 24px;text-align:center; }
.prl-empty-icon { width:56px;height:56px;background:#f3f4f6;border-radius:16px;display:flex;align-items:center;justify-content:center;margin-bottom:14px;color:#d1d5db; }
.prl-empty-title { font-size:0.9rem;font-weight:700;color:#374151;margin:0 0 6px; }
.prl-empty-sub   { font-size:0.78rem;color:#9ca3af;margin:0; }

.prl-flash { display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:10px;font-size:0.82rem;font-weight:500;margin-bottom:20px;animation:flashIn 0.3s ease; }
.prl-flash.success { background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d; }
.prl-flash.error   { background:#fff0f0;border:1px solid #fecaca;color:#c8292a; }
.prl-flash.info    { background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8; }
@keyframes flashIn { from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)} }

.prl-table-scroll { overflow-x:auto; }
</style>
@endpush

@section('content')
<div class="prl-page">

    @foreach(['success','error','info'] as $t)
        @if(session($t))
        <div class="prl-flash {{ $t }}">
            @if($t==='success')<svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            @else<svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>@endif
            {{ session($t) }}
        </div>
        @endif
    @endforeach

    <div class="prl-topbar">
        <div>
            <h1 class="prl-topbar-title">Payroll Management</h1>
            <p class="prl-topbar-sub">Auto-generate, review, and finalize employee payrolls</p>
        </div>
        <div class="prl-topbar-actions">
            <a href="{{ route('payroll.salary-computation.create') }}" class="prl-btn-sec">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Individual Payroll
            </a>
        </div>
    </div>

    <div class="prl-stats">
        <div class="prl-stat s-red">
            <div class="prl-stat-icon"><svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" d="M16 2v4M8 2v4M3 10h18"/></svg></div>
            <div><div class="prl-stat-label">Next Cutoff</div><div class="prl-stat-value" style="font-size:1rem;font-family:'Sora',sans-serif;">{{ $nextCutoffDate ? $nextCutoffDate->format('M d, Y') : 'N/A' }}</div><div class="prl-stat-sub">{{ $nextCutoffDate ? $nextCutoffDate->format('l') : '—' }}</div></div>
        </div>
        <div class="prl-stat s-amber">
            <div class="prl-stat-icon"><svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 6v6l4 2"/></svg></div>
            <div><div class="prl-stat-label">Pending Records</div><div class="prl-stat-value">{{ $payrollCount }}</div><div class="prl-stat-sub">awaiting processing</div></div>
        </div>
        <div class="prl-stat s-green">
            <div class="prl-stat-icon"><svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
            <div><div class="prl-stat-label">Pending Amount</div><div class="prl-stat-value" style="font-size:1rem;font-family:'DM Mono',monospace;">₱{{ number_format($totalPayroll,0) }}</div><div class="prl-stat-sub">{{ $payrollCount }} records</div></div>
        </div>
        <div class="prl-stat s-blue">
            <div class="prl-stat-icon"><svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
            <div><div class="prl-stat-label">Active Employees</div><div class="prl-stat-value">{{ $activeEmployees }}</div><div class="prl-stat-sub">payroll-eligible</div></div>
        </div>
    </div>

    {{-- Generate hero --}}
    <div class="prl-generate-card">
        <div class="prl-generate-left">
            <div class="prl-generate-eyebrow">{{ $batchAlreadyExists ? 'Batch already generated' : 'Ready to generate' }}</div>
            <h2 class="prl-generate-title">{{ $batchAlreadyExists ? 'Payroll Draft Exists' : 'Generate Payroll Batch' }}</h2>
            <div class="prl-generate-period">
                Period: {{ \Carbon\Carbon::parse($currentPeriod['start'])->format('M d, Y') }} &mdash; {{ \Carbon\Carbon::parse($currentPeriod['end'])->format('M d, Y') }}
            </div>
        </div>
        <div class="prl-generate-right">
            @if($batchAlreadyExists)
                @php $existingBatch = \App\Models\PayrollBatch::where('period_start',$currentPeriod['start'])->where('period_end',$currentPeriod['end'])->first(); @endphp
                <span class="prl-btn-generate disabled">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Already Generated
                </span>
                @if($existingBatch)
                <span class="prl-already-badge">
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <a href="{{ route('payroll.batch.confirm', $existingBatch) }}">View / Edit Draft →</a>
                </span>
                @endif
            @else
                <form action="{{ route('payroll.batch.generate') }}" method="POST">
                    @csrf
                    <button type="submit" class="prl-btn-generate">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        Generate Payroll Batch
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="prl-two-col">
        {{-- Records table --}}
        <div>
            <div class="prl-section-head">
                <h2 class="prl-section-title"><span class="prl-dot"></span> Payroll Records</h2>
            </div>
            <div class="prl-filter-bar">
                <div class="prl-search-wrap">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
                    <input type="text" class="prl-search-input" id="prlSearch" placeholder="Search employee…">
                </div>
                <select class="prl-filter-select" id="prlStatusFilter">
                    <option value="">All Statuses</option>
                    <option value="draft">Draft</option>
                    <option value="pending">Pending</option>
                    <option value="finalized">Finalized</option>
                    <option value="submitted">Submitted</option>
                    <option value="approved">Approved</option>
                    <option value="paid">Paid</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            <div class="prl-table-card">
                <div class="prl-table-scroll">
                    <table class="prl-table">
                        <thead><tr>
                            <th>Employee</th><th>Period</th>
                            <th class="text-end">Gross</th><th class="text-end">Net</th>
                            <th class="text-center">Status</th><th class="text-end">Actions</th>
                        </tr></thead>
                        <tbody id="prlTbody">
                        @forelse($payrolls as $payroll)
                            @php
                                $initials = strtoupper(substr($payroll->user->first_name??'U',0,1).substr($payroll->user->last_name??'',0,1));
                                $sc = match($payroll->status){
                                    'pending'=>'s-pending','finalized'=>'s-finalized','submitted'=>'s-submitted',
                                    'approved'=>'s-approved','paid'=>'s-paid','rejected'=>'s-rejected',default=>'s-draft'
                                };
                            @endphp
                            <tr data-name="{{ strtolower(($payroll->user->first_name??'').' '.($payroll->user->last_name??'')) }}"
                                data-status="{{ $payroll->status }}">
                                <td>
                                    <div class="prl-emp-cell">
                                        <div class="prl-emp-avatar">{{ $initials }}</div>
                                        <div>
                                            <div class="prl-emp-name">{{ $payroll->user->first_name }} {{ $payroll->user->last_name }}</div>
                                            <div class="prl-emp-dept">{{ $payroll->user->department ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="prl-period-tag">{{ $payroll->payroll_period_start->format('M d') }} – {{ $payroll->payroll_period_end->format('M d, Y') }}</span></td>
                                <td class="text-end"><span class="prl-mono">₱{{ number_format($payroll->gross_pay,2) }}</span></td>
                                <td class="text-end"><span class="prl-mono c-bold">₱{{ number_format($payroll->net_pay,2) }}</span></td>
                                <td class="text-center"><span class="prl-status {{ $sc }}">{{ ucfirst($payroll->status) }}</span></td>
                                <td>
                                    <div class="prl-actions">
                                        <a href="{{ route('payroll.salary-computation.show',$payroll) }}" class="prl-action-btn" title="View">
                                            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                        <a href="{{ route('payroll.generatePayslip',$payroll) }}" class="prl-action-btn success" title="Payslip">
                                            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </a>
                                        <form action="{{ route('payroll.salary-computation.destroy',$payroll) }}" method="POST" onsubmit="return confirm('Delete?')" style="display:inline;">
                                            @csrf @method('DELETE')
                                            <button class="prl-action-btn danger" title="Delete">
                                                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6">
                                <div class="prl-empty">
                                    <div class="prl-empty-icon"><svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
                                    <p class="prl-empty-title">No payroll records yet</p>
                                    <p class="prl-empty-sub">Generate a batch above to get started.</p>
                                </div>
                            </td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <div id="prlNoResults" style="display:none;">
                    <div class="prl-empty" style="padding:32px;">
                        <p class="prl-empty-title">No results found</p>
                        <p class="prl-empty-sub">Try a different filter.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent batches --}}
        <div>
            <div class="prl-section-head">
                <h2 class="prl-section-title"><span class="prl-dot"></span> Recent Batches</h2>
            </div>
            <div class="prl-side-card">
                <div class="prl-side-head">Last 5 generated batches</div>
                @forelse($recentBatches as $b)
                @php $bsc = match($b->status){'finalized'=>'s-finalized','submitted'=>'s-submitted','paid'=>'s-paid',default=>'s-draft'}; @endphp
                <a href="{{ route('payroll.batch.confirm',$b) }}" class="prl-batch-item">
                    <div>
                        <div class="prl-batch-period">{{ $b->period_start->format('M d') }} – {{ $b->period_end->format('M d, Y') }}</div>
                        <div class="prl-batch-meta">{{ $b->employee_count }} employees · ₱{{ number_format($b->total_net_pay,0) }}</div>
                    </div>
                    <span class="prl-status {{ $bsc }}">{{ ucfirst($b->status) }}</span>
                </a>
                @empty
                    <div class="prl-batch-empty">No batches yet.</div>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
(function(){
    const s=document.getElementById('prlSearch'), f=document.getElementById('prlStatusFilter');
    const tbody=document.getElementById('prlTbody'), nr=document.getElementById('prlNoResults');
    function run(){
        const q=s.value.toLowerCase().trim(), st=f.value;
        const rows=Array.from(tbody.querySelectorAll('tr[data-name]'));
        const vis=rows.filter(r=>(!q||r.dataset.name.includes(q))&&(!st||r.dataset.status===st));
        rows.forEach(r=>r.style.display='none');
        vis.forEach(r=>r.style.display='');
        nr.style.display=vis.length===0&&rows.length>0?'block':'none';
    }
    s.addEventListener('input',run); f.addEventListener('change',run);
})();
</script>
@endpush