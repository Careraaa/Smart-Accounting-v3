@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
.prl-page { font-family:'Sora',sans-serif; }
.bd-hero{background:#111827;border-radius:16px;padding:22px 26px;display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;position:relative;overflow:hidden;margin-bottom:18px;}
.bd-hero::before{content:'';position:absolute;top:-50px;right:-50px;width:180px;height:180px;border-radius:50%;background:rgba(200,41,42,.12);pointer-events:none;}
.bd-title{color:#fff;font-weight:900;letter-spacing:-.02em;margin:0 0 6px;font-size:1.05rem;}
.bd-sub{color:#6b7280;font-family:'DM Mono',monospace;font-size:.82rem;}
.bd-badge{display:inline-flex;align-items:center;gap:6px;padding:5px 10px;border-radius:999px;font-size:.7rem;font-weight:900;text-transform:uppercase;letter-spacing:.06em;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.06);color:#fff;}
.bd-actions{display:flex;gap:8px;flex-wrap:wrap;align-items:center;z-index:1;}
.bd-btn{display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border-radius:12px;border:1px solid transparent;font-weight:800;font-size:.82rem;text-decoration:none;cursor:pointer;}
.bd-btn.primary{background:#c8292a;color:#fff;border-color:#c8292a;box-shadow:0 10px 26px rgba(200,41,42,.22);}
.bd-btn.ghost{background:rgba(255,255,255,.06);color:#fff;border-color:rgba(255,255,255,.12);}
.bd-btn.warn{background:#fff1f2;color:#c8292a;border-color:#fecaca;}
.bd-card{background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;}
.bd-head{padding:14px 16px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;}
.bd-head h2{margin:0;font-size:.88rem;font-weight:900;color:#111827;display:flex;align-items:center;gap:8px;}
.bd-dot{width:8px;height:8px;border-radius:50%;background:#c8292a;display:inline-block;}
.bd-meta{color:#6b7280;font-size:.78rem;}
.bd-table{width:100%;border-collapse:collapse;font-size:.85rem;}
.bd-table thead th{background:#f8f9fb;border-bottom:1px solid #e5e7eb;padding:11px 16px;font-size:.68rem;font-weight:900;text-transform:uppercase;letter-spacing:.09em;color:#6b7280;white-space:nowrap;}
.bd-table tbody tr { cursor:pointer; transition:background .12s; }
.bd-table tbody tr:hover { background:#fdf4f4; }
.bd-table tbody td { padding:13px 16px; border-bottom:1px solid #f3f4f6; vertical-align:middle; }
.bd-table tbody tr:last-child td { border-bottom:none; }
.bd-table tfoot td { padding:12px 16px; border-top:2px solid #e5e7eb; }
.bd-mono{font-family:'DM Mono',monospace;font-variant-numeric:tabular-nums;}
.bd-row-actions{display:flex;gap:6px;justify-content:flex-end;}
.bd-icon-btn{width:30px;height:30px;border-radius:8px;border:none;background:#f4f5f7;color:#6b7280;display:inline-flex;align-items:center;justify-content:center;text-decoration:none;}
.bd-icon-btn:hover{background:#eff6ff;color:#3b82f6;}
.bd-btn.disabled{background:rgba(255,255,255,.04);color:rgba(255,255,255,.25);border-color:rgba(255,255,255,.08);cursor:not-allowed;pointer-events:none;}
.bd-rejection-banner{background:#2d0a0a;border:1px solid rgba(200,41,42,.35);border-radius:14px;padding:18px 22px;margin-bottom:18px;display:flex;gap:14px;align-items:flex-start;}
.bd-rejection-icon{width:36px;height:36px;border-radius:10px;background:rgba(200,41,42,.18);color:#f87171;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;}
.bd-rejection-title{font-size:.82rem;font-weight:900;color:#f87171;margin:0 0 6px;text-transform:uppercase;letter-spacing:.06em;}
.bd-rejection-meta{font-size:.78rem;color:#9ca3af;margin:0 0 10px;}
.bd-rejection-note{background:rgba(200,41,42,.1);border:1px solid rgba(200,41,42,.25);border-radius:8px;padding:10px 14px;font-size:.82rem;color:#fca5a5;line-height:1.6;}
</style>
@endpush

@section('content')
<div class="prl-page">
    <div class="bd-hero">
        <div style="position:relative;z-index:1;">
            <h1 class="bd-title">{{ $batch->display_name }}</h1>
            <div class="bd-sub">
                Period {{ $batch->period_start->format('M d, Y') }} – {{ $batch->period_end->format('M d, Y') }}
            </div>
            <div style="margin-top:10px;">
                <span class="bd-badge">Status: {{ ucfirst($batch->status) }}</span>
            </div>
        </div>
        <div class="bd-actions">
            <a href="{{ route('payroll.salary-computation.index') }}" class="bd-btn ghost">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Back
            </a>
            @if($batch->status === 'rejected')
                <span class="bd-btn disabled">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Payslips
                </span>
            @else
                <a href="{{ route('payroll.batch.payslips', $batch) }}" class="bd-btn ghost">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Payslips
                </a>
            @endif

            @if(auth()->user()?->role === 'accountant' && $batch->status === 'submitted')
                <form action="{{ route('payroll-approval.approve-batch') }}" method="POST" style="display:inline;">
                    @csrf
                    <input type="hidden" name="batch_id" value="{{ $batch->id }}">
                    <button class="bd-btn primary" type="submit" data-sa-confirm="Approve this entire batch?">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Approve Batch
                    </button>
                </form>
                <form action="{{ route('payroll-approval.reject-batch') }}" method="POST" style="display:inline;">
                    @csrf
                    <input type="hidden" name="batch_id" value="{{ $batch->id }}">
                    <input type="text" name="rejection_note" required minlength="3"
                           placeholder="Rejection note (required)"
                           style="padding:10px 12px;border-radius:12px;border:1px solid #e5e7eb;min-width:240px;font-size:.82rem;">
                    <button class="bd-btn warn" type="submit" data-sa-confirm="Reject this entire batch?">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        Reject Batch
                    </button>
                </form>
            @endif

            @if(in_array(auth()->user()?->role, ['hr','superadmin','qr_admin','accountant'], true) && $batch->isEditable())
                <a href="{{ route('payroll.batch.confirm', $batch) }}" class="bd-btn ghost">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5"/><path stroke-linecap="round" stroke-linejoin="round" d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Edit Batch
                </a>
            @endif
        </div>
    </div>

    @if($batch->status === 'rejected')
    <div class="bd-rejection-banner">
        <div class="bd-rejection-icon">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </div>
        <div style="flex:1;min-width:0;">
            <p class="bd-rejection-title">Batch Rejected</p>
            <p class="bd-rejection-meta">
                Rejected by
                <strong style="color:#e5e7eb;">{{ optional($batch->rejectedBy)->first_name }} {{ optional($batch->rejectedBy)->last_name }}</strong>
                @if($batch->rejected_at)
                    &nbsp;·&nbsp; {{ $batch->rejected_at->format('M d, Y \a\t h:i A') }}
                @endif
            </p>
            @if($batch->rejection_note)
                <div class="bd-rejection-note">
                    <strong style="display:block;margin-bottom:4px;font-size:.72rem;text-transform:uppercase;letter-spacing:.08em;color:#f87171;">Reason</strong>
                    {{ $batch->rejection_note }}
                </div>
            @endif
        </div>
    </div>
    @endif

    <div class="bd-card">
        <div class="bd-head">
            <h2><span class="bd-dot"></span> Employees in this batch</h2>
            <div class="bd-meta">
                {{ $batch->payrolls->count() }} employees · Total net <span class="bd-mono">₱{{ number_format($batch->total_net_pay,2) }}</span>
            </div>
        </div>
        <div class="table-responsive">
            <table class="bd-table">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Position</th>
                        <th class="text-center">Days</th>
                        <th class="text-end">Basic pay</th>
                        <th class="text-end">Gross pay</th>
                        <th class="text-end">Deductions</th>
                        <th class="text-end">Net pay</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($batch->payrolls as $p)
                        @php
                            $user     = $p->user;
                            $initials = strtoupper(substr($user->first_name ?? '', 0, 1) . substr($user->last_name ?? '', 0, 1));
                        @endphp
                        <tr onclick="window.location='{{ route('payroll.salary-computation.show', $p) }}'">
                            <td class="fw-bold" style="font-size:0.88rem;color:#111827;">
                                {{ $user->first_name }} {{ $user->last_name }}
                            </td>
                            <td style="font-size:0.82rem;color:#6b7280;">
                                {{ $user->department ?? 'N/A' }}
                            </td>
                            <td style="font-size:0.82rem;color:#6b7280;">
                                {{ $user->position ?? 'N/A' }}
                            </td>
                            <td class="text-center bd-mono" style="font-size:0.85rem;">
                                {{ $p->days_worked ?? '—' }}
                            </td>
                            <td class="text-end bd-mono" style="font-size:0.85rem;">
                                ₱{{ number_format($p->basic_salary, 2) }}
                            </td>
                            <td class="text-end bd-mono" style="font-size:0.85rem;">
                                ₱{{ number_format($p->gross_pay, 2) }}
                            </td>
                            <td class="text-end bd-mono text-muted" style="font-size:0.85rem;">
                                ₱{{ number_format($p->total_deductions, 2) }}
                            </td>
                            <td class="text-end bd-mono fw-bold" style="font-size:0.88rem;color:#15803d;">
                                ₱{{ number_format($p->net_pay, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" style="text-center text-muted py-5">
                            <i class="feather-inbox d-block mb-2" style="font-size:28px;opacity:.3;"></i>
                            No employees in this batch.
                        </td></tr>
                    @endforelse
                </tbody>
                @if($batch->payrolls->isNotEmpty())
                <tfoot>
                    <tr style="background:#f9fafb;font-size:0.85rem;">
                        <td colspan="5" class="fw-bold" style="color:#111827;">Totals</td>
                        <td class="text-end bd-mono fw-bold">
                            ₱{{ number_format($batch->payrolls->sum('gross_pay'), 2) }}
                        </td>
                        <td class="text-end bd-mono text-muted">
                            ₱{{ number_format($batch->payrolls->sum('total_deductions'), 2) }}
                        </td>
                        <td class="text-end bd-mono fw-bold" style="color:#15803d;">
                            ₱{{ number_format($batch->total_net_pay, 2) }}
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection

