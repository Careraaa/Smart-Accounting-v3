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

/* ── Period selector ────────────────────────────────────────── */
.prl-period-selector { margin-bottom: 16px; }
.prl-period-label { display: block; font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.09em; color: #6b7280; margin-bottom: 8px; }
.prl-period-select { width: 100%; border: 1px solid #e5e7eb; border-radius: 8px; padding: 10px 12px; font-size: 0.82rem; font-family: 'Sora', sans-serif; color: #111827; background: #f9fafb; outline: none; cursor: pointer; transition: border-color 0.15s, background 0.15s; }
.prl-period-select:focus { border-color: #c8292a; background: #fff; box-shadow: 0 0 0 3px rgba(200,41,42,0.08); }

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

/* ── Batch cards ────────────────────────────────────────────── */
.prl-pending-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden; }
.prl-pending-header { padding:14px 18px;border-bottom:1px solid #e5e7eb;background:#fff;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px; }
.prl-pending-title { font-size:0.85rem;font-weight:700;color:#111827;display:flex;align-items:center;gap:8px;margin:0; }
.prl-pending-sub { font-size:0.75rem;color:#9ca3af;margin:0; }
.prl-pending-body { padding:0; }
.prl-batch-grid { display:flex;flex-direction:column;gap:0; }
@media (max-width:900px) { .prl-batch-grid { flex-direction:column; } }
.prl-batch-col { width:100%;border-bottom:1px solid #f3f4f6; }
.prl-batch-col:last-child { border-bottom:none; }
.prl-batch-col-full { width:100%; }
.prl-batch-card-link { text-decoration:none;color:inherit;display:block; }
.prl-batch-card { background:#fff;border:none;border-radius:0;padding:0;transition:background 0.15s;cursor:pointer; }
.prl-batch-card:hover { background:#fafafa; }
.prl-batch-card-inner { display:flex;align-items:center;gap:18px;padding:18px 20px;flex-wrap:nowrap;justify-content:flex-start; }
.prl-batch-card-icon { width:52px;height:52px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;color:#fff;font-size:1.35rem; }
.prl-batch-card-icon.a { background:linear-gradient(135deg, #c8292a, #9f1e1f); }
.prl-batch-card-icon.b { background:linear-gradient(135deg, #0284c7, #0369a1); }
.prl-batch-card-content { flex:0 1 auto;min-width:200px; }
.prl-batch-card-title { font-size:0.9rem;font-weight:700;color:#111827;margin:0 0 4px;line-height:1.2; }
.prl-batch-card-date { font-size:0.75rem;color:#9ca3af;font-family:'DM Mono',monospace;margin:0; }
.prl-batch-card-stats { display:flex;align-items:center;gap:18px;margin-left:auto;flex-shrink:0;flex-wrap:nowrap;justify-content:flex-end; }
.prl-batch-status { display:inline-flex;align-items:center;justify-content:center;font-size:0.65rem;font-weight:700;padding:4px 10px;border-radius:20px;text-transform:uppercase;letter-spacing:0.06em;white-space:nowrap;flex-shrink:0;height:24px;min-width:100px; }
.prl-batch-stat { display:flex;flex-direction:column;align-items:center;text-align:center;flex-shrink:0;min-width:80px;justify-content:center; }
.prl-batch-stat small { font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#9ca3af;margin-bottom:4px;display:block; }
.prl-batch-stat strong { font-family:'DM Mono',monospace;font-size:0.92rem;color:#111827;line-height:1.2;white-space:nowrap; }
.prl-batch-card-arrow { width:20px;height:20px;display:flex;align-items:center;justify-content:center;color:#d1d5db;flex-shrink:0;font-size:1.1rem;margin-left:12px; }

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

    {{-- Generate hero --}}
    <div class="prl-generate-card">
        <div class="prl-generate-left">
            <div class="prl-generate-eyebrow">
                @if($currentInProgressBatch) Batch in progress @else Ready to generate @endif
            </div>
            <h2 class="prl-generate-title">
                @if($currentInProgressBatch) Continue Batch @else Generate New Payroll Batch @endif
            </h2>
            <div class="prl-generate-period">
                @if($currentInProgressBatch)
                    Period: {{ \Carbon\Carbon::parse($currentInProgressBatch->period_start)->format('M d, Y') }} &mdash; {{ \Carbon\Carbon::parse($currentInProgressBatch->period_end)->format('M d, Y') }}
                @else
                    Select any period from the current or past 24 months to create a new batch
                @endif
            </div>
        </div>
        <div class="prl-generate-right">
            @if($currentInProgressBatch)
                <a href="{{ route('payroll.batch.confirm', $currentInProgressBatch) }}" class="prl-btn-generate">
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

    {{-- Pending batches ────────────────────────────────────────── --}}
    <div class="prl-pending-card">
        <div class="prl-pending-header">
            <span class="prl-pending-title">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="display:inline;margin-right:8px;vertical-align:-2px;color:#c8292a;"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>Payroll Batches
            </span>
        </div>
        <div class="prl-pending-body">
            <div class="prl-batch-grid">
                @forelse($batches as $batch)
                    @php
                        $startDate = $batch->period_start;
                        $endDate   = $batch->period_end;
                        $isFirst   = $startDate->format('d') <= 15;
                        $variant   = $isFirst ? 'a' : 'b';
                        $statusColors = [
                            'pending' => ['bg' => '#fef3c7', 'color' => '#92400e', 'label' => 'Pending'],
                            'submitted' => ['bg' => '#f5f3ff', 'color' => '#7c3aed', 'label' => 'Submitted'],
                            'approved' => ['bg' => '#f0fdf4', 'color' => '#16a34a', 'label' => 'Approved'],
                            'rejected' => ['bg' => '#fff0f0', 'color' => '#c8292a', 'label' => 'Rejected'],
                        ];
                        $statusInfo = $statusColors[$batch->status] ?? $statusColors['pending'];
                    @endphp
                    <div class="prl-batch-col">
                        <a href="{{ route('payroll.batch.details', $batch) }}" class="prl-batch-card-link">
                            <div class="prl-batch-card">
                                <div class="prl-batch-card-inner">
                                    <div class="prl-batch-card-icon {{ $variant }}">
                                        @if($isFirst)
                                            <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        @else
                                            <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @endif
                                    </div>
                                    <div class="prl-batch-card-content">
                                        <h6 class="prl-batch-card-title">
                                            {{ $startDate->format('F Y') }} — {{ $isFirst ? '1st' : '2nd' }} half
                                        </h6>
                                        <div class="prl-batch-card-date">
                                            {{ $startDate->format('M d') }} – {{ $endDate->format('M d, Y') }}
                                        </div>
                                    </div>
                                    <div class="prl-batch-card-stats">
                                        <span class="prl-batch-status" style="background:{{ $statusInfo['bg'] }};color:{{ $statusInfo['color'] }};">
                                            {{ $statusInfo['label'] }}
                                        </span>
                                        <div class="prl-batch-stat">
                                            <small>Employees</small>
                                            <strong>{{ $batch->employee_count }}</strong>
                                        </div>
                                        <div class="prl-batch-stat">
                                            <small>Gross</small>
                                            <strong>₱{{ number_format($batch->total_gross_pay ?? 0, 2) }}</strong>
                                        </div>
                                        <div class="prl-batch-stat">
                                            <small>Net</small>
                                            <strong style="color:#16a34a;">₱{{ number_format($batch->total_net_pay, 2) }}</strong>
                                        </div>
                                        <div class="prl-batch-card-arrow">
                                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="prl-batch-col-full">
                        <div class="prl-empty" style="padding:40px 20px;">
                            <div class="prl-empty-icon">
                                <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <p class="prl-empty-title">No batches pending approval</p>
                        </div>
                    </div>
                @endforelse
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
        <h3 class="prl-modal-title">Generate Payroll Batch</h3>
        <p class="prl-modal-body">
            Select a payroll period and create a new batch.<br>
            You can add employees, review, and finalize before submitting to accounting.
        </p>
        <form action="{{ route('payroll.batch.generate') }}" method="POST">
            @csrf
            <div class="prl-period-selector">
                <label class="prl-period-label">Payroll Period</label>
                <select name="period" class="prl-period-select" id="periodSelect" required>
                    <option value="">-- Select a period --</option>
                    @foreach($availablePeriods as $period)
                    <option value="{{ $period['start'] }}|{{ $period['end'] }}" 
                            @if($period['start'] === $currentPeriod['start'] && $period['end'] === $currentPeriod['end']) selected @endif>
                        {{ $period['display'] }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="prl-modal-actions">
                <button type="button" class="prl-modal-cancel" onclick="closeModal('generateModal')">Cancel</button>
                <button type="submit" class="prl-modal-confirm generate" style="width:100%;">
                    Yes, Generate Batch
                </button>
            </div>
        </form>
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
// ── Modal helpers ────────────────────────────────────────────────
function openGenerateModal() {
    document.getElementById('generateModal').classList.add('open');
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
