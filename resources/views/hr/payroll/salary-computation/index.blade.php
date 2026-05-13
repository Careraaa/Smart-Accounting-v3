@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
*, *::before, *::after { box-sizing: border-box; }
.prl-page { font-family: 'Sora', sans-serif; }

/* ── Topbar ─────────────────────────────────────────────────── */
.prl-topbar { display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px;flex-wrap:wrap; }
.prl-topbar-title { font-size:1.35rem;font-weight:800;color:#111827;letter-spacing:-0.02em;margin:0 0 2px; }
.prl-topbar-sub   { font-size:0.78rem;color:#9ca3af;margin:0; }

/* ── Flash ──────────────────────────────────────────────────── */
.prl-flash { display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:10px;font-size:0.82rem;font-weight:500;margin-bottom:20px;animation:flashIn 0.3s ease; }
.prl-flash.success { background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d; }
.prl-flash.error   { background:#fff0f0;border:1px solid #fecaca;color:#c8292a; }
.prl-flash.info    { background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8; }
@keyframes flashIn { from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)} }

/* ── Stats ──────────────────────────────────────────────────── */
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

/* ── Generate hero ──────────────────────────────────────────── */
.prl-generate-card {
    background:#111827;border-radius:16px;padding:28px 32px;
    display:flex;align-items:center;justify-content:space-between;gap:24px;
    margin-bottom:28px;flex-wrap:wrap;position:relative;overflow:hidden;
}
.prl-generate-card::before { content:'';position:absolute;top:-60px;right:-60px;width:200px;height:200px;border-radius:50%;background:rgba(200,41,42,0.15);pointer-events:none; }
.prl-generate-left { position:relative;z-index:1; }
.prl-generate-eyebrow { font-size:0.67rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#c8292a;margin-bottom:6px; }
.prl-generate-title  { font-size:1.15rem;font-weight:800;color:#fff;margin:0 0 6px;letter-spacing:-0.02em; }
.prl-generate-period { font-size:0.82rem;color:#6b7280;font-family:'DM Mono',monospace; }
.prl-generate-right  { position:relative;z-index:1; }

.prl-btn-generate {
    display:inline-flex;align-items:center;gap:10px;padding:13px 28px;background:#c8292a;color:#fff;
    border:none;border-radius:12px;font-family:'Sora',sans-serif;font-size:0.9rem;font-weight:700;
    cursor:pointer;transition:background 0.15s,box-shadow 0.15s;
    box-shadow:0 4px 20px rgba(200,41,42,0.5);white-space:nowrap;text-decoration:none;
}
.prl-btn-generate:hover { background:#a81f20;color:#fff;box-shadow:0 8px 28px rgba(200,41,42,0.6); }

/* ── Filter bar ─────────────────────────────────────────────── */
.prl-filter-bar { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:10px 14px;display:flex;align-items:center;gap:10px;margin-bottom:14px;flex-wrap:wrap; }
.prl-search-wrap { position:relative;flex:1;min-width:180px; }
.prl-search-wrap svg { position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#9ca3af;pointer-events:none; }
.prl-search-input { width:100%;border:1px solid #e5e7eb;border-radius:8px;padding:7px 12px 7px 32px;font-size:0.82rem;font-family:'Sora',sans-serif;color:#111827;background:#f9fafb;outline:none;transition:border-color 0.15s; }
.prl-search-input:focus { border-color:#c8292a;background:#fff;box-shadow:0 0 0 3px rgba(200,41,42,0.08); }
.prl-filter-select { border:1px solid #e5e7eb;border-radius:8px;padding:7px 12px;font-size:0.82rem;font-family:'Sora',sans-serif;color:#374151;background:#f9fafb;outline:none;cursor:pointer; }
.prl-filter-select:focus { border-color:#c8292a; }

/* ── Batch table ─────────────────────────────────────────────── */
.prl-table-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden; }
.prl-table-scroll { overflow-x:auto; }
.prl-table { width:100%;border-collapse:collapse;font-size:0.835rem; }
.prl-table thead tr { background:#f8f9fb;border-bottom:1px solid #e5e7eb; }
.prl-table thead th { padding:11px 16px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.09em;color:#6b7280;white-space:nowrap; }
.prl-table tbody tr { border-bottom:1px solid #f3f4f6;transition:background 0.12s;cursor:pointer; }
.prl-table tbody tr:last-child { border-bottom:none; }
.prl-table tbody tr:hover { background:#fdf4f4; }
.prl-table tbody td { padding:13px 16px;color:#374151;vertical-align:middle; }

.prl-batch-name { font-weight:700;color:#111827;font-size:0.845rem;line-height:1.2; }
.prl-batch-sub  { font-size:0.72rem;color:#9ca3af;margin-top:2px;font-family:'DM Mono',monospace; }
.prl-mono { font-family:'DM Mono',monospace;font-size:0.82rem;font-variant-numeric:tabular-nums; }
.prl-mono.bold { color:#111827;font-weight:700; }
.prl-period-tag { display:inline-block;font-family:'DM Mono',monospace;font-size:0.72rem;color:#6b7280;background:#f3f4f6;padding:2px 8px;border-radius:4px; }

/* Status badges */
.prl-status { display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;white-space:nowrap; }
.prl-status::before { content:'';width:5px;height:5px;border-radius:50%; }
.prl-status.s-submitted { background:#f5f3ff;color:#7c3aed;border:1px solid #ddd6fe; }
.prl-status.s-submitted::before { background:#8b5cf6; }
.prl-status.s-approved  { background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0; }
.prl-status.s-approved::before { background:#16a34a; }
.prl-status.s-rejected  { background:#fff0f0;color:#c8292a;border:1px solid #fecaca; }
.prl-status.s-rejected::before { background:#ef4444; }

/* Action buttons */
.prl-actions { display:flex;align-items:center;gap:6px;justify-content:flex-end; }
.prl-action-btn {
    display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:8px;
    font-family:'Sora',sans-serif;font-size:0.72rem;font-weight:700;
    text-decoration:none;border:none;cursor:pointer;transition:all 0.12s;white-space:nowrap;
}
.prl-action-btn.edit    { background:#fffbeb;color:#d97706;border:1px solid #fde68a; }
.prl-action-btn.edit:hover { background:#d97706;color:#fff;border-color:#d97706; }
.prl-action-btn.reopen  { background:#fff0f0;color:#c8292a;border:1px solid #fecaca; }
.prl-action-btn.reopen:hover { background:#c8292a;color:#fff;border-color:#c8292a; }
.prl-action-btn.view    { background:#f3f4f6;color:#374151;border:1px solid #e5e7eb; }
.prl-action-btn.view:hover { background:#111827;color:#fff;border-color:#111827; }

/* Empty state */
.prl-empty { display:flex;flex-direction:column;align-items:center;justify-content:center;padding:56px 24px;text-align:center; }
.prl-empty-icon  { width:56px;height:56px;background:#f3f4f6;border-radius:16px;display:flex;align-items:center;justify-content:center;margin-bottom:14px;color:#d1d5db; }
.prl-empty-title { font-size:0.9rem;font-weight:700;color:#374151;margin:0 0 6px; }
.prl-empty-sub   { font-size:0.78rem;color:#9ca3af;margin:0; }

/* ── Confirmation modal ─────────────────────────────────────── */
.prl-modal-overlay {
    position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:9999;
    display:flex;align-items:center;justify-content:center;padding:20px;
    opacity:0;pointer-events:none;transition:opacity 0.2s;
}
.prl-modal-overlay.open { opacity:1;pointer-events:all; }
.prl-modal {
    background:#fff;border-radius:18px;padding:32px;max-width:440px;width:100%;
    box-shadow:0 20px 60px rgba(0,0,0,0.2);transform:translateY(12px);transition:transform 0.2s;
}
.prl-modal-overlay.open .prl-modal { transform:translateY(0); }
.prl-modal-icon { width:52px;height:52px;border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px; }
.prl-modal-icon.generate { background:#fff0f0;color:#c8292a; }
.prl-modal-icon.resubmit { background:#fffbeb;color:#d97706; }
.prl-modal-title { font-size:1.05rem;font-weight:800;color:#111827;text-align:center;margin:0 0 8px;letter-spacing:-0.01em; }
.prl-modal-body  { font-size:0.82rem;color:#6b7280;text-align:center;margin:0 0 24px;line-height:1.6; }
.prl-modal-period { display:inline-block;font-family:'DM Mono',monospace;font-size:0.78rem;background:#f3f4f6;padding:4px 12px;border-radius:6px;color:#374151;margin-bottom:16px; }
.prl-modal-actions { display:flex;gap:10px; }
.prl-modal-cancel {
    flex:1;padding:11px;background:#fff;color:#374151;border:1px solid #e5e7eb;border-radius:10px;
    font-family:'Sora',sans-serif;font-size:0.845rem;font-weight:600;cursor:pointer;transition:all 0.12s;
}
.prl-modal-cancel:hover { border-color:#c8292a;color:#c8292a; }
.prl-modal-confirm {
    flex:1;padding:11px;border:none;border-radius:10px;
    font-family:'Sora',sans-serif;font-size:0.845rem;font-weight:700;cursor:pointer;transition:all 0.12s;
}
.prl-modal-confirm.generate { background:#c8292a;color:#fff; }
.prl-modal-confirm.generate:hover { background:#a81f20; }
.prl-modal-confirm.resubmit { background:#d97706;color:#fff; }
.prl-modal-confirm.resubmit:hover { background:#b45309; }
</style>
@endpush

@section('content')
<div class="prl-page">

    {{-- Flash messages --}}
    @foreach(['success','error','info'] as $t)
        @if(session($t))
        <div class="prl-flash {{ $t }}">
            @if($t==='success')<svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            @else<svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>@endif
            {{ session($t) }}
        </div>
        @endif
    @endforeach

    {{-- Topbar --}}
    <div class="prl-topbar">
        <div>
            <h1 class="prl-topbar-title">Payroll Management</h1>
            <p class="prl-topbar-sub">Generate, review, and submit payroll batches for accounting approval</p>
        </div>
    </div>

    {{-- Stats --}}
    <div class="prl-stats">
        <div class="prl-stat s-blue">
            <div class="prl-stat-icon"><svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
            <div>
                <div class="prl-stat-label">Total Batches</div>
                <div class="prl-stat-value">{{ $batches->count() }}</div>
                <div class="prl-stat-sub">all time</div>
            </div>
        </div>
        <div class="prl-stat s-amber">
            <div class="prl-stat-icon"><svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 6v6l4 2"/></svg></div>
            <div>
                <div class="prl-stat-label">Pending Approval</div>
                <div class="prl-stat-value">{{ $submittedCount }}</div>
                <div class="prl-stat-sub">awaiting accountant</div>
            </div>
        </div>
        <div class="prl-stat s-green">
            <div class="prl-stat-icon"><svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></div>
            <div>
                <div class="prl-stat-label">Approved</div>
                <div class="prl-stat-value">{{ $approvedCount }}</div>
                <div class="prl-stat-sub">batches approved</div>
            </div>
        </div>
        <div class="prl-stat s-red">
            <div class="prl-stat-icon"><svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></div>
            <div>
                <div class="prl-stat-label">Rejected</div>
                <div class="prl-stat-value">{{ $rejectedCount }}</div>
                <div class="prl-stat-sub">need resubmission</div>
            </div>
        </div>
    </div>

    {{-- Generate hero --}}
    <div class="prl-generate-card">
        <div class="prl-generate-left">
            <div class="prl-generate-eyebrow">
                @if($currentPendingBatch) Batch in progress @else Ready to generate @endif
            </div>
            <h2 class="prl-generate-title">
                @if($currentPendingBatch) Continue Pending Batch @else Generate New Payroll Batch @endif
            </h2>
            <div class="prl-generate-period">
                Period: {{ \Carbon\Carbon::parse($currentPeriod['start'])->format('M d, Y') }} &mdash; {{ \Carbon\Carbon::parse($currentPeriod['end'])->format('M d, Y') }}
            </div>
        </div>
        <div class="prl-generate-right">
            @if($currentPendingBatch)
                <a href="{{ route('payroll.batch.confirm', $currentPendingBatch) }}" class="prl-btn-generate">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Continue Batch
                </a>
            @else
                <button type="button" class="prl-btn-generate" onclick="openGenerateModal()">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Generate Payroll Batch
                </button>
            @endif
        </div>
    </div>

    {{-- Filter bar --}}
    <div class="prl-filter-bar">
        <div class="prl-search-wrap">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
            <input type="text" class="prl-search-input" id="batchSearch" placeholder="Search batch…">
        </div>
        <select class="prl-filter-select" id="statusFilter">
            <option value="">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="submitted">Submitted</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
        </select>
    </div>

    {{-- Batch table --}}
    <div class="prl-table-card">
        <div class="prl-table-scroll">
            <table class="prl-table">
                <thead>
                    <tr>
                        <th>Batch</th>
                        <th>Period</th>
                        <th class="text-center">Employees</th>
                        <th class="text-end">Total Net Pay</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="batchTbody">
                @forelse($batches as $batch)
                    @php
                        $sc = match($batch->status) {
                            'approved'  => 's-approved',
                            'rejected'  => 's-rejected',
                            'submitted' => 's-submitted',
                            default     => 's-pending',
                        };
                        $detailsUrl = route('payroll.batch.details', $batch);
                        $confirmUrl = route('payroll.batch.confirm', $batch);
                    @endphp
                    <tr
                        data-name="{{ strtolower($batch->display_name) }}"
                        data-status="{{ $batch->status }}"
                        onclick="window.location='{{ $detailsUrl }}'"
                    >
                        <td>
                            <div class="prl-batch-name">{{ $batch->display_name }}</div>
                            <div class="prl-batch-sub">
                                Created {{ $batch->created_at->format('M d, Y') }}
                                @if($batch->finalized_at) &middot; Submitted {{ $batch->finalized_at->format('M d') }} @endif
                            </div>
                        </td>
                        <td>
                            <span class="prl-period-tag">
                                {{ $batch->period_start->format('M d') }} &ndash; {{ $batch->period_end->format('M d, Y') }}
                            </span>
                        </td>
                        <td class="text-center" style="font-family:'DM Mono',monospace;font-weight:700;color:#374151;">
                            {{ $batch->employee_count }}
                        </td>
                        <td class="text-end">
                            <span class="prl-mono bold">&#8369;{{ number_format($batch->total_net_pay, 2) }}</span>
                        </td>
                        <td class="text-center">
                            <span class="prl-status {{ $sc }}">{{ ucfirst($batch->status) }}</span>
                        </td>
                        <td onclick="event.stopPropagation()">
                            <div class="prl-actions">
                                @if($batch->status === 'pending')
                                    {{-- Pending: edit (go to confirm/build page) --}}
                                    <a href="{{ $confirmUrl }}" class="prl-action-btn edit">
                                        <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Edit &amp; Submit
                                    </a>

                                @elseif($batch->status === 'submitted')
                                    {{-- Submitted: can edit (reopen to pending) then resubmit --}}
                                    <form action="{{ route('payroll.batch.reopen', $batch) }}" method="POST" style="display:inline;" id="reopen-form-{{ $batch->id }}">
                                        @csrf
                                    </form>
                                    <button type="button" class="prl-action-btn edit"
                                        onclick="openResubmitModal({{ $batch->id }}, '{{ addslashes($batch->display_name) }}', '{{ $batch->period_start->format('M d') }} – {{ $batch->period_end->format('M d, Y') }}')">
                                        <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Edit
                                    </button>
                                    <a href="{{ $detailsUrl }}" class="prl-action-btn view">
                                        <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        View
                                    </a>

                                @elseif($batch->status === 'approved')
                                    {{-- Approved: view only, no edit --}}
                                    <a href="{{ $detailsUrl }}" class="prl-action-btn view">
                                        <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        View
                                    </a>

                                @elseif($batch->status === 'rejected')
                                    {{-- Rejected: reopen to edit and resubmit --}}
                                    <form action="{{ route('payroll.batch.reopen', $batch) }}" method="POST" style="display:inline;" id="reopen-form-{{ $batch->id }}">
                                        @csrf
                                    </form>
                                    <button type="button" class="prl-action-btn reopen"
                                        onclick="openResubmitModal({{ $batch->id }}, '{{ addslashes($batch->display_name) }}', '{{ $batch->period_start->format('M d') }} – {{ $batch->period_end->format('M d, Y') }}')">
                                        <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        Edit &amp; Resubmit
                                    </button>
                                    <a href="{{ $detailsUrl }}" class="prl-action-btn view">
                                        <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        View
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">
                        <div class="prl-empty">
                            <div class="prl-empty-icon">
                                <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <p class="prl-empty-title">No payroll batches yet</p>
                            <p class="prl-empty-sub">Click "Generate Payroll Batch" above to get started.</p>
                        </div>
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div id="batchNoResults" style="display:none;">
            <div class="prl-empty" style="padding:28px;">
                <p class="prl-empty-title">No results</p>
                <p class="prl-empty-sub">Try adjusting your filters.</p>
            </div>
        </div>
    </div>

</div>

{{-- ── Generate Batch Modal ──────────────────────────────────── --}}
<div class="prl-modal-overlay" id="generateModal">
    <div class="prl-modal">
        <div class="prl-modal-icon generate">
            <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </div>
        <h3 class="prl-modal-title">Generate Payroll Batch?</h3>
        <p class="prl-modal-body">
            This will create a new payroll batch for the current period.<br>
            You can add employees, review, and finalize before submitting to accounting.
        </p>
        <div style="text-align:center;">
            <span class="prl-modal-period">
                {{ \Carbon\Carbon::parse($currentPeriod['start'])->format('M d, Y') }} &mdash; {{ \Carbon\Carbon::parse($currentPeriod['end'])->format('M d, Y') }}
            </span>
        </div>
        <div class="prl-modal-actions">
            <button type="button" class="prl-modal-cancel" onclick="closeModal('generateModal')">Cancel</button>
            <form action="{{ route('payroll.batch.generate') }}" method="POST" style="flex:1;">
                @csrf
                <button type="submit" class="prl-modal-confirm generate" style="width:100%;">
                    Yes, Generate Batch
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ── Edit / Resubmit Modal ────────────────────────────────── --}}
<div class="prl-modal-overlay" id="resubmitModal">
    <div class="prl-modal">
        <div class="prl-modal-icon resubmit">
            <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
        </div>
        <h3 class="prl-modal-title" id="resubmitModalTitle">Reopen Batch for Editing?</h3>
        <p class="prl-modal-body" id="resubmitModalBody">
            This will reopen the batch so you can make changes. After editing, you will need to finalize and resubmit it to accounting.
        </p>
        <div style="text-align:center;">
            <span class="prl-modal-period" id="resubmitModalPeriod"></span>
        </div>
        <div class="prl-modal-actions">
            <button type="button" class="prl-modal-cancel" onclick="closeModal('resubmitModal')">Cancel</button>
            <button type="button" class="prl-modal-confirm resubmit" id="resubmitConfirmBtn" onclick="submitReopenForm()">
                Yes, Reopen &amp; Edit
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ── Client-side filter ───────────────────────────────────────────
(function () {
    const search  = document.getElementById('batchSearch');
    const statusF = document.getElementById('statusFilter');
    const tbody   = document.getElementById('batchTbody');
    const noRes   = document.getElementById('batchNoResults');

    function run() {
        const q = search.value.toLowerCase().trim();
        const s = statusF.value;
        const rows = Array.from(tbody.querySelectorAll('tr[data-name]'));
        const vis = rows.filter(r =>
            (!q || r.dataset.name.includes(q)) &&
            (!s || r.dataset.status === s)
        );
        rows.forEach(r => r.style.display = 'none');
        vis.forEach(r => r.style.display = '');
        noRes.style.display = vis.length === 0 && rows.length > 0 ? 'block' : 'none';
    }

    search.addEventListener('input', run);
    statusF.addEventListener('change', run);
})();

// ── Modal helpers ────────────────────────────────────────────────
function openGenerateModal() {
    document.getElementById('generateModal').classList.add('open');
}

let _reopenBatchId = null;

function openResubmitModal(batchId, batchName, period) {
    _reopenBatchId = batchId;
    document.getElementById('resubmitModalTitle').textContent = 'Reopen "' + batchName + '"?';
    document.getElementById('resubmitModalPeriod').textContent = period;
    document.getElementById('resubmitModal').classList.add('open');
}

function submitReopenForm() {
    if (_reopenBatchId) {
        document.getElementById('reopen-form-' + _reopenBatchId).submit();
    }
}

function closeModal(id) {
    document.getElementById(id).classList.remove('open');
}

// Close on overlay click
document.querySelectorAll('.prl-modal-overlay').forEach(function(overlay) {
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) overlay.classList.remove('open');
    });
});

// Close on Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.prl-modal-overlay.open').forEach(function(m) {
            m.classList.remove('open');
        });
    }
});
</script>
@endpush
