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
.bd-table td{padding:12px 16px;border-bottom:1px solid #f3f4f6;vertical-align:middle;color:#374151;}
.bd-mono{font-family:'DM Mono',monospace;font-variant-numeric:tabular-nums;}
.bd-row-actions{display:flex;gap:6px;justify-content:flex-end;}
.bd-icon-btn{width:30px;height:30px;border-radius:8px;border:none;background:#f4f5f7;color:#6b7280;display:inline-flex;align-items:center;justify-content:center;text-decoration:none;}
.bd-icon-btn:hover{background:#eff6ff;color:#3b82f6;}
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
            <a href="{{ route('payroll.batch.payslips', $batch) }}" class="bd-btn ghost">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Payslips
            </a>

            @if(auth()->user()?->role === 'accountant' && $batch->status === 'submitted')
                <form action="{{ route('payroll-approval.approve-batch') }}" method="POST" style="display:inline;">
                    @csrf
                    <input type="hidden" name="start" value="{{ $batch->period_start->format('Y-m-d') }}">
                    <input type="hidden" name="end" value="{{ $batch->period_end->format('Y-m-d') }}">
                    <button class="bd-btn primary" type="submit" data-sa-confirm="Approve this entire batch?">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Approve Batch
                    </button>
                </form>
                <form action="{{ route('payroll-approval.reject-batch') }}" method="POST" style="display:inline;">
                    @csrf
                    <input type="hidden" name="start" value="{{ $batch->period_start->format('Y-m-d') }}">
                    <input type="hidden" name="end" value="{{ $batch->period_end->format('Y-m-d') }}">
                    <input type="text" name="rejection_note" required minlength="3"
                           placeholder="Rejection note (required)"
                           style="padding:10px 12px;border-radius:12px;border:1px solid #e5e7eb;min-width:240px;font-size:.82rem;">
                    <button class="bd-btn warn" type="submit" data-sa-confirm="Reject this entire batch?">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        Reject Batch
                    </button>
                </form>
            @endif

            @if(in_array(auth()->user()?->role, ['hr','superadmin','qr_admin','accountant'], true) && $batch->status === 'draft')
                <a href="{{ route('payroll.batch.confirm', $batch) }}" class="bd-btn ghost">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5"/><path stroke-linecap="round" stroke-linejoin="round" d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Open Draft
                </a>
            @endif
        </div>
    </div>

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
                        <th>Position</th>
                        <th class="text-end">Net</th>
                        <th class="text-end">View</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($batch->payrolls as $p)
                        <tr>
                            <td><strong>{{ $p->user->first_name }} {{ $p->user->last_name }}</strong></td>
                            <td style="color:#6b7280;">{{ $p->user->position ?? 'N/A' }}</td>
                            <td class="text-end bd-mono">₱{{ number_format($p->net_pay,2) }}</td>
                            <td class="text-end">
                                <div class="bd-row-actions">
                                    <a class="bd-icon-btn" href="{{ route('payroll.salary-computation.show', $p) }}" title="View employee payroll">
                                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="text-align:center;padding:32px;color:#9ca3af;">No employees in this batch.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

