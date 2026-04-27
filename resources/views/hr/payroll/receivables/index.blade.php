@extends('layouts.layout')

@section('title', 'Payroll Receivables')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');

.prl-page { font-family: 'Sora', sans-serif; }

/* ── Topbar ── */
.prl-topbar { display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px;flex-wrap:wrap; }
.prl-topbar-title { font-size:1.35rem;font-weight:800;color:#111827;letter-spacing:-0.02em;margin:0 0 2px; }
.prl-topbar-sub   { font-size:0.78rem;color:#9ca3af;margin:0; }

/* ── Flash ── */
.prl-flash { display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:10px;font-size:0.82rem;font-weight:500;margin-bottom:20px;animation:flashIn 0.3s ease; }
.prl-flash.success { background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d; }
.prl-flash.error   { background:#fff0f0;border:1px solid #fecaca;color:#c8292a; }
@keyframes flashIn { from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)} }

/* ── Stats ── */
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

/* ── Tab bar ── */
.prl-tabs { display:flex;gap:4px;background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:5px;margin-bottom:20px;width:fit-content; }
.prl-tab { display:inline-flex;align-items:center;gap:7px;padding:8px 18px;border-radius:9px;font-family:'Sora',sans-serif;font-size:0.82rem;font-weight:600;color:#6b7280;text-decoration:none;transition:all 0.15s;white-space:nowrap;border:none;background:transparent;cursor:pointer; }
.prl-tab:hover { color:#111827;background:#f3f4f6; }
.prl-tab.active { background:#111827;color:#fff;box-shadow:0 2px 8px rgba(0,0,0,0.15); }
.prl-tab svg { opacity:0.7; }
.prl-tab.active svg { opacity:1; }

/* ── Section head ── */
.prl-section-head { display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;flex-wrap:wrap;gap:10px; }
.prl-section-title { font-size:0.88rem;font-weight:700;color:#111827;margin:0;display:flex;align-items:center;gap:8px; }
.prl-dot { width:8px;height:8px;border-radius:50%;background:#c8292a;display:inline-block; }

/* ── Filter bar ── */
.prl-filter-bar { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:12px 16px;display:flex;align-items:center;gap:10px;margin-bottom:14px;flex-wrap:wrap; }
.prl-search-wrap { position:relative;flex:1;min-width:180px; }
.prl-search-wrap svg { position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#9ca3af;pointer-events:none; }
.prl-search-input { width:100%;border:1px solid #e5e7eb;border-radius:8px;padding:8px 12px 8px 34px;font-size:0.82rem;font-family:'Sora',sans-serif;color:#111827;background:#f9fafb;outline:none;transition:border-color 0.15s,background 0.15s; }
.prl-search-input:focus { border-color:#c8292a;background:#fff;box-shadow:0 0 0 3px rgba(200,41,42,0.08); }
.prl-filter-select { border:1px solid #e5e7eb;border-radius:8px;padding:8px 12px;font-size:0.82rem;font-family:'Sora',sans-serif;color:#374151;background:#f9fafb;outline:none;cursor:pointer;transition:border-color 0.15s; }
.prl-filter-select:focus { border-color:#c8292a; }

/* ── Table ── */
.prl-table-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden; }
.prl-table { width:100%;border-collapse:collapse;font-size:0.835rem; }
.prl-table thead tr { background:#f8f9fb;border-bottom:1px solid #e5e7eb; }
.prl-table thead th { padding:11px 16px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.09em;color:#6b7280;white-space:nowrap;font-family:'Sora',sans-serif; }
.prl-table tbody tr { border-bottom:1px solid #f3f4f6;transition:background 0.1s; }
.prl-table tbody tr:last-child { border-bottom:none; }
.prl-table tbody tr:hover { background:#fafafa; }
.prl-table tbody td { padding:12px 16px;color:#374151;vertical-align:middle; }
.prl-table-scroll { overflow-x:auto; }

/* ── Employee cell ── */
.prl-emp-cell { display:flex;align-items:center;gap:10px; }
.prl-emp-avatar { width:32px;height:32px;border-radius:50%;background:#f3f4f6;display:flex;align-items:center;justify-content:center;font-size:0.72rem;font-weight:700;color:#6b7280;flex-shrink:0;border:1.5px solid #e5e7eb;text-transform:uppercase; }
.prl-emp-name { font-weight:600;color:#111827;font-size:0.845rem; }
.prl-emp-dept { font-size:0.72rem;color:#9ca3af;margin-top:1px; }
.prl-mono { font-family:'DM Mono',monospace;font-size:0.82rem;font-variant-numeric:tabular-nums; }
.prl-mono.c-bold { color:#111827;font-weight:600; }
.prl-mono.c-red  { color:#c8292a;font-weight:500; }

/* ── Status badges ── */
.prl-status { display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;white-space:nowrap; }
.prl-status::before { content:'';width:5px;height:5px;border-radius:50%; }
.prl-status.s-pending  { background:#fffbeb;color:#d97706;border:1px solid #fde68a; }
.prl-status.s-pending::before  { background:#d97706; }
.prl-status.s-approved { background:#f0f9ff;color:#0284c7;border:1px solid #bae6fd; }
.prl-status.s-approved::before { background:#0284c7; }
.prl-status.s-active   { background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0; }
.prl-status.s-active::before   { background:#16a34a; }
.prl-status.s-settled  { background:#f5f3ff;color:#7c3aed;border:1px solid #ddd6fe; }
.prl-status.s-settled::before  { background:#8b5cf6; }
.prl-status.s-deducted { background:#f0fdf4;color:#15803d;border:1px solid #86efac; }
.prl-status.s-deducted::before { background:#22c55e; }
.prl-status.s-rejected { background:#fff0f0;color:#c8292a;border:1px solid #fecaca; }
.prl-status.s-rejected::before { background:#ef4444; }

/* ── Actions ── */
.prl-actions { display:flex;align-items:center;gap:5px;justify-content:flex-end; }
.prl-action-btn { width:30px;height:30px;border-radius:7px;border:none;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;text-decoration:none;font-size:13px;transition:background 0.13s,color 0.13s;background:#f4f5f7;color:#6b7280;padding:0; }
.prl-action-btn:hover        { background:#eff6ff;color:#3b82f6; }
.prl-action-btn.success:hover { background:#f0fdf4;color:#16a34a; }
.prl-action-btn.danger:hover  { background:#fff1f2;color:#e11d48; }

/* ── Rejection reason row ── */
.prl-reason-row td { background:#fffbeb !important;border-left:3px solid #f59e0b; }
.prl-reason-text { font-size:0.775rem;color:#92400e;display:flex;align-items:center;gap:6px; }

/* ── Progress bar ── */
.prl-progress-wrap { display:flex;align-items:center;gap:8px; }
.prl-progress { flex:1;height:6px;border-radius:4px;background:#f3f4f6;overflow:hidden;min-width:80px; }
.prl-progress-bar { height:100%;border-radius:4px;background:linear-gradient(90deg,#16a34a,#4ade80);transition:width 0.3s; }
.prl-progress-label { font-size:0.72rem;color:#9ca3af;white-space:nowrap;font-family:'DM Mono',monospace; }

/* ── Empty state ── */
.prl-empty { display:flex;flex-direction:column;align-items:center;justify-content:center;padding:56px 24px;text-align:center; }
.prl-empty-icon { width:56px;height:56px;background:#f3f4f6;border-radius:16px;display:flex;align-items:center;justify-content:center;margin-bottom:14px;color:#d1d5db; }
.prl-empty-title { font-size:0.9rem;font-weight:700;color:#374151;margin:0 0 6px; }
.prl-empty-sub   { font-size:0.78rem;color:#9ca3af;margin:0; }

/* ── Table footer / pagination ── */
.prl-table-footer { padding:12px 16px;border-top:1px solid #f3f4f6;display:flex;justify-content:flex-end; }

/* ── Modal ── */
.prl-modal .modal-content { border-radius:16px;border:none;box-shadow:0 20px 60px rgba(0,0,0,0.15);font-family:'Sora',sans-serif; }
.prl-modal .modal-header { border-bottom:1px solid #f3f4f6;padding:18px 22px; }
.prl-modal .modal-title { font-size:0.92rem;font-weight:800;color:#111827; }
.prl-modal .modal-body { padding:20px 22px; }
.prl-modal .modal-footer { border-top:1px solid #f3f4f6;padding:14px 22px;gap:8px; }
.prl-modal .form-label { font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#9ca3af;margin-bottom:6px; }
.prl-modal .form-control { border:1px solid #e5e7eb;border-radius:8px;font-size:0.845rem;font-family:'Sora',sans-serif;color:#111827;padding:9px 12px; }
.prl-modal .form-control:focus { border-color:#c8292a;box-shadow:0 0 0 3px rgba(200,41,42,0.08);outline:none; }
.prl-modal-btn { display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:9px;font-family:'Sora',sans-serif;font-size:0.82rem;font-weight:700;border:none;cursor:pointer;transition:all 0.15s; }
.prl-modal-btn.cancel { background:#f3f4f6;color:#374151; }
.prl-modal-btn.cancel:hover { background:#e5e7eb; }
.prl-modal-btn.confirm { background:#111827;color:#fff;box-shadow:0 2px 8px rgba(0,0,0,0.15); }
.prl-modal-btn.confirm:hover { background:#000; }
.prl-modal-btn.danger-btn { background:#c8292a;color:#fff;box-shadow:0 2px 8px rgba(200,41,42,0.3); }
.prl-modal-btn.danger-btn:hover { background:#a81f20; }
</style>
@endpush

@section('content')
<div class="prl-page">

    {{-- ── Flash messages ── --}}
    @if(session('success'))
    <div class="prl-flash success">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if($errors->any())
    <div class="prl-flash error">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
        {{ $errors->first() }}
    </div>
    @endif

    {{-- ── Topbar ── --}}
    <div class="prl-topbar">
        <div>
            <h1 class="prl-topbar-title">Payroll Receivables</h1>
            <p class="prl-topbar-sub">Manage cash advances and salary loan applications</p>
        </div>
    </div>

    {{-- ── Stats ── --}}
    @php
        $caItems      = method_exists($cashAdvances, 'getCollection') ? $cashAdvances->getCollection() : collect($cashAdvances->all());
        $loanItems    = method_exists($salaryLoans,  'getCollection') ? $salaryLoans->getCollection()  : collect($salaryLoans->all());
        $totalCA      = $caItems->sum('amount');
        $pendingCA    = $caItems->where('status','pending')->count();
        $caCount      = method_exists($cashAdvances, 'total') ? $cashAdvances->total() : $caItems->count();
        $totalLoans   = $loanItems->sum('loan_amount');
        $activeLoans  = $loanItems->whereIn('status',['active','approved'])->count();
    @endphp
    <div class="prl-stats">
        <div class="prl-stat s-amber">
            <div class="prl-stat-icon">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path stroke-linecap="round" d="M2 10h20"/></svg>
            </div>
            <div>
                <div class="prl-stat-label">Cash Advances</div>
                <div class="prl-stat-value" style="font-size:1rem;font-family:'DM Mono',monospace;">₱{{ number_format($totalCA,2) }}</div>
                <div class="prl-stat-sub">{{ $caCount }} total requests</div>
            </div>
        </div>
        <div class="prl-stat s-red">
            <div class="prl-stat-icon">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 6v6l4 2"/></svg>
            </div>
            <div>
                <div class="prl-stat-label">Pending Advances</div>
                <div class="prl-stat-value">{{ $pendingCA }}</div>
                <div class="prl-stat-sub">awaiting approval</div>
            </div>
        </div>
        <div class="prl-stat s-blue">
            <div class="prl-stat-icon">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <div class="prl-stat-label">Salary Loans</div>
                <div class="prl-stat-value" style="font-size:1rem;font-family:'DM Mono',monospace;">₱{{ number_format($totalLoans,2) }}</div>
                <div class="prl-stat-sub">total loan portfolio</div>
            </div>
        </div>
        <div class="prl-stat s-green">
            <div class="prl-stat-icon">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="prl-stat-label">Active Loans</div>
                <div class="prl-stat-value">{{ $activeLoans }}</div>
                <div class="prl-stat-sub">currently repaying</div>
            </div>
        </div>
    </div>

    {{-- ── Tab bar ── --}}
    <div class="prl-tabs">
        <a href="{{ route('payroll.receivables.index') }}?tab=cash_advances"
           class="prl-tab {{ $tab === 'cash_advances' ? 'active' : '' }}">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><rect x="2" y="5" width="20" height="14" rx="2"/><path stroke-linecap="round" d="M2 10h20"/></svg>
            Cash Advances
        </a>
        <a href="{{ route('payroll.receivables.index') }}?tab=salary_loans"
           class="prl-tab {{ $tab === 'salary_loans' ? 'active' : '' }}">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            Salary Loans
        </a>
    </div>

    {{-- ══════════════════════════════════════════
         CASH ADVANCES TAB
    ══════════════════════════════════════════ --}}
    @if($tab === 'cash_advances')
    <div class="prl-section-head">
        <h2 class="prl-section-title"><span class="prl-dot"></span> Cash Advance Requests</h2>
    </div>

    <div class="prl-filter-bar">
        <div class="prl-search-wrap">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
            <input type="text" class="prl-search-input" id="caSearch" placeholder="Search employee…">
        </div>
        <select class="prl-filter-select" id="caStatusFilter">
            <option value="">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="deducted">Deducted</option>
            <option value="rejected">Rejected</option>
        </select>
    </div>

    <div class="prl-table-card">
        <div class="prl-table-scroll">
            <table class="prl-table">
                <thead><tr>
                    <th>Employee</th>
                    <th class="text-end">Amount</th>
                    <th>Requested</th>
                    <th class="text-center">Status</th>
                    <th>Approved By</th>
                    <th>Deducted On</th>
                    @if(auth()->user()->role === 'accountant')
                    <th class="text-end">Actions</th>
                    @endif
                </tr></thead>
                <tbody id="caTbody">
                @forelse($cashAdvances as $advance)
                    @php
                        $initials = strtoupper(substr($advance->user->first_name ?? ($advance->user->name ?? 'U'), 0, 1) . substr($advance->user->last_name ?? '', 0, 1));
                        $statusCls = match($advance->status) {
                            'pending'  => 's-pending',
                            'approved' => 's-approved',
                            'deducted' => 's-deducted',
                            'rejected' => 's-rejected',
                            default    => 's-pending',
                        };
                    @endphp
                    <tr data-name="{{ strtolower($advance->user->name ?? '') }}" data-status="{{ $advance->status }}">
                        <td>
                            <div class="prl-emp-cell">
                                <div class="prl-emp-avatar">{{ $initials }}</div>
                                <div>
                                    <div class="prl-emp-name">{{ $advance->user->name ?? '—' }}</div>
                                    <div class="prl-emp-dept">{{ $advance->user->position ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="text-end"><span class="prl-mono c-bold">₱{{ number_format($advance->amount, 2) }}</span></td>
                        <td><span style="font-size:.82rem;color:#374151;">{{ $advance->request_date ? \Carbon\Carbon::parse($advance->request_date)->format('M d, Y') : '—' }}</span></td>
                        <td class="text-center"><span class="prl-status {{ $statusCls }}">{{ ucfirst($advance->status) }}</span></td>
                        <td>
                            <span style="font-size:.82rem;color:#374151;">{{ optional($advance->approver)->name ?? '—' }}</span>
                            @if($advance->approved_at)
                                <div style="font-size:.72rem;color:#9ca3af;">{{ \Carbon\Carbon::parse($advance->approved_at)->format('M d, Y') }}</div>
                            @endif
                        </td>
                        <td>
                            <span style="font-size:.82rem;color:#374151;">
                                @if($advance->deductedPayroll)
                                    Period ending {{ \Carbon\Carbon::parse($advance->deductedPayroll->payroll_period_end)->format('M d, Y') }}
                                @else
                                    —
                                @endif
                            </span>
                        </td>
                        @if(auth()->user()->role === 'accountant')
                        <td>
                            <div class="prl-actions">
                                @if($advance->status === 'pending')
                                <form method="POST" action="{{ route('cash-advances.approve', $advance) }}" style="display:inline;">
                                    @csrf
                                    <button class="prl-action-btn success" title="Approve">
                                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                </form>
                                <button class="prl-action-btn danger" title="Reject" type="button"
                                        onclick="openRejectModal('ca', {{ $advance->id }})">
                                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                                @else
                                <span style="font-size:.75rem;color:#d1d5db;">—</span>
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
                    @if($advance->rejection_reason)
                    <tr class="prl-reason-row">
                        <td colspan="{{ auth()->user()->role === 'accountant' ? 7 : 6 }}">
                            <div class="prl-reason-text">
                                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                <strong>Rejection reason:</strong>&nbsp;{{ $advance->rejection_reason }}
                            </div>
                        </td>
                    </tr>
                    @endif
                @empty
                    <tr><td colspan="{{ auth()->user()->role === 'accountant' ? 7 : 6 }}">
                        <div class="prl-empty">
                            <div class="prl-empty-icon"><svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><rect x="2" y="5" width="20" height="14" rx="2"/><path stroke-linecap="round" d="M2 10h20"/></svg></div>
                            <p class="prl-empty-title">No cash advance requests</p>
                            <p class="prl-empty-sub">There are no records to display.</p>
                        </div>
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div id="caNoResults" style="display:none;">
            <div class="prl-empty" style="padding:32px;">
                <p class="prl-empty-title">No results found</p>
                <p class="prl-empty-sub">Try a different search or filter.</p>
            </div>
        </div>
        @if($cashAdvances->hasPages())
        <div class="prl-table-footer">{{ $cashAdvances->links() }}</div>
        @endif
    </div>
    @endif

    {{-- ══════════════════════════════════════════
         SALARY LOANS TAB
    ══════════════════════════════════════════ --}}
    @if($tab === 'salary_loans')
    <div class="prl-section-head">
        <h2 class="prl-section-title"><span class="prl-dot"></span> Salary Loan Applications</h2>
    </div>

    <div class="prl-filter-bar">
        <div class="prl-search-wrap">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
            <input type="text" class="prl-search-input" id="loanSearch" placeholder="Search employee…">
        </div>
        <select class="prl-filter-select" id="loanStatusFilter">
            <option value="">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="active">Active</option>
            <option value="settled">Settled</option>
            <option value="rejected">Rejected</option>
        </select>
    </div>

    <div class="prl-table-card">
        <div class="prl-table-scroll">
            <table class="prl-table">
                <thead><tr>
                    <th>Employee</th>
                    <th class="text-end">Loan Amount</th>
                    <th class="text-end">Monthly</th>
                    <th class="text-end">Remaining</th>
                    <th>Progress</th>
                    <th class="text-center">Status</th>
                    <th>Start Date</th>
                    @if(auth()->user()->role === 'accountant')
                    <th class="text-end">Actions</th>
                    @endif
                </tr></thead>
                <tbody id="loanTbody">
                @forelse($salaryLoans as $loan)
                    @php
                        $totalMonths = $loan->monthly_deduction > 0
                            ? ceil($loan->loan_amount / $loan->monthly_deduction) : 0;
                        $progress = $totalMonths > 0
                            ? min(100, round(($loan->months_paid / $totalMonths) * 100)) : 0;
                        $initials = strtoupper(substr($loan->user->first_name ?? ($loan->user->name ?? 'U'), 0, 1) . substr($loan->user->last_name ?? '', 0, 1));
                        $loanCls = match($loan->status) {
                            'pending'  => 's-pending',
                            'active'   => 's-approved',
                            'settled'  => 's-settled',
                            'rejected' => 's-rejected',
                            default    => 's-pending',
                        };
                    @endphp
                    <tr data-name="{{ strtolower($loan->user->name ?? '') }}" data-status="{{ $loan->status }}">
                        <td>
                            <div class="prl-emp-cell">
                                <div class="prl-emp-avatar">{{ $initials }}</div>
                                <div>
                                    <div class="prl-emp-name">{{ $loan->user->name ?? '—' }}</div>
                                    <div class="prl-emp-dept">{{ $loan->user->position ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="text-end"><span class="prl-mono c-bold">₱{{ number_format($loan->loan_amount, 2) }}</span></td>
                        <td class="text-end"><span class="prl-mono" style="color:#6b7280;">₱{{ number_format($loan->monthly_deduction, 2) }}</span></td>
                        <td class="text-end"><span class="prl-mono c-red">₱{{ number_format($loan->remaining_balance, 2) }}</span></td>
                        <td style="min-width:140px;">
                            <div class="prl-progress-wrap">
                                <div class="prl-progress">
                                    <div class="prl-progress-bar" style="width:{{ $progress }}%"></div>
                                </div>
                                <span class="prl-progress-label">{{ $loan->months_paid }}/{{ $totalMonths }}mo</span>
                            </div>
                        </td>
                        <td class="text-center"><span class="prl-status {{ $loanCls }}">{{ ucfirst($loan->status) }}</span></td>
                        <td><span style="font-size:.82rem;color:#374151;">{{ $loan->start_date ? \Carbon\Carbon::parse($loan->start_date)->format('M d, Y') : '—' }}</span></td>
                        @if(auth()->user()->role === 'accountant')
                        <td>
                            <div class="prl-actions">
                                @if($loan->status === 'pending')
                                <form method="POST" action="{{ route('salary-loans.approve', $loan) }}" style="display:inline;">
                                    @csrf
                                    <button class="prl-action-btn success" title="Approve">
                                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                </form>
                                <button class="prl-action-btn danger" title="Reject" type="button"
                                        onclick="openRejectModal('loan', {{ $loan->id }})">
                                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                                @else
                                <span style="font-size:.75rem;color:#d1d5db;">—</span>
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
                    @if($loan->rejection_reason)
                    <tr class="prl-reason-row">
                        <td colspan="{{ auth()->user()->role === 'accountant' ? 8 : 7 }}">
                            <div class="prl-reason-text">
                                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                <strong>Rejection reason:</strong>&nbsp;{{ $loan->rejection_reason }}
                            </div>
                        </td>
                    </tr>
                    @endif
                @empty
                    <tr><td colspan="{{ auth()->user()->role === 'accountant' ? 8 : 7 }}">
                        <div class="prl-empty">
                            <div class="prl-empty-icon"><svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></div>
                            <p class="prl-empty-title">No salary loan applications</p>
                            <p class="prl-empty-sub">There are no records to display.</p>
                        </div>
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div id="loanNoResults" style="display:none;">
            <div class="prl-empty" style="padding:32px;">
                <p class="prl-empty-title">No results found</p>
                <p class="prl-empty-sub">Try a different search or filter.</p>
            </div>
        </div>
        @if($salaryLoans->hasPages())
        <div class="prl-table-footer">{{ $salaryLoans->links() }}</div>
        @endif
    </div>
    @endif

</div>

{{-- ── Reject Modal ── --}}
@if(auth()->user()->role === 'accountant')
<div class="modal fade prl-modal" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title">Reject Request</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Reason for Rejection</label>
                    <textarea name="rejection_reason" class="form-control" rows="3"
                              placeholder="Enter reason…" required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="prl-modal-btn cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="prl-modal-btn danger-btn">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        Reject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- ── Mark Paid Modal (HR / superadmin) ── --}}
@if(in_array(auth()->user()->role, ['hr', 'superadmin']))
<div class="modal fade prl-modal" id="payModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <form id="payForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title">Mark as Paid</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Payment Date</label>
                    <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="prl-modal-btn cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="prl-modal-btn confirm">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Confirm
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
(function () {
    // Cash Advances filter
    const caSearch  = document.getElementById('caSearch');
    const caFilter  = document.getElementById('caStatusFilter');
    const caTbody   = document.getElementById('caTbody');
    const caNR      = document.getElementById('caNoResults');

    function runCA() {
        if (!caTbody) return;
        const q  = caSearch.value.toLowerCase().trim();
        const st = caFilter.value;
        const rows = Array.from(caTbody.querySelectorAll('tr[data-name]'));
        const vis  = rows.filter(r => (!q || r.dataset.name.includes(q)) && (!st || r.dataset.status === st));
        rows.forEach(r => r.style.display = 'none');
        vis.forEach(r => r.style.display = '');
        if (caNR) caNR.style.display = vis.length === 0 && rows.length > 0 ? 'block' : 'none';
    }
    if (caSearch) { caSearch.addEventListener('input', runCA); caFilter.addEventListener('change', runCA); }

    // Salary Loans filter
    const loanSearch = document.getElementById('loanSearch');
    const loanFilter = document.getElementById('loanStatusFilter');
    const loanTbody  = document.getElementById('loanTbody');
    const loanNR     = document.getElementById('loanNoResults');

    function runLoan() {
        if (!loanTbody) return;
        const q  = loanSearch.value.toLowerCase().trim();
        const st = loanFilter.value;
        const rows = Array.from(loanTbody.querySelectorAll('tr[data-name]'));
        const vis  = rows.filter(r => (!q || r.dataset.name.includes(q)) && (!st || r.dataset.status === st));
        rows.forEach(r => r.style.display = 'none');
        vis.forEach(r => r.style.display = '');
        if (loanNR) loanNR.style.display = vis.length === 0 && rows.length > 0 ? 'block' : 'none';
    }
    if (loanSearch) { loanSearch.addEventListener('input', runLoan); loanFilter.addEventListener('change', runLoan); }
})();

@if(auth()->user()->role === 'accountant')
function openRejectModal(type, id) {
    const routes = {
        ca:   `/cash-advances/${id}/reject`,
        loan: `/salary-loans/${id}/reject`,
    };
    document.getElementById('rejectForm').action = routes[type];
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}
@endif

@if(in_array(auth()->user()->role, ['hr', 'superadmin']))
function openPayModal(payrollId) {
    document.getElementById('payForm').action = `/payroll/receivables/${payrollId}/mark-paid`;
    new bootstrap.Modal(document.getElementById('payModal')).show();
}
@endif
</script>
@endpush
@endsection