@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
.prl-page { font-family: 'Sora', sans-serif; }

.prl-back-link { display:inline-flex;align-items:center;gap:6px;font-size:0.78rem;font-weight:600;color:#9ca3af;text-decoration:none;margin-bottom:16px;transition:color 0.13s; }
.prl-back-link:hover { color:#c8292a; }

/* ── Batch header card ─────────────────────────────────────── */
.prl-batch-hero {
    background:#111827;border-radius:16px;padding:24px 28px;
    display:flex;align-items:center;justify-content:space-between;
    gap:20px;margin-bottom:20px;flex-wrap:wrap;
    position:relative;overflow:hidden;
}
.prl-batch-hero::before { content:'';position:absolute;top:-50px;right:-50px;width:180px;height:180px;border-radius:50%;background:rgba(200,41,42,0.12);pointer-events:none; }

.prl-hero-left { position:relative;z-index:1; }
.prl-hero-eyebrow { font-size:0.67rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;margin-bottom:6px; }
.prl-hero-eyebrow.draft     { color:#6b7280; }
.prl-hero-eyebrow.finalized { color:#3b82f6; }
.prl-hero-eyebrow.submitted { color:#8b5cf6; }
.prl-hero-eyebrow.paid      { color:#22c55e; }

.prl-hero-title  { font-size:1.1rem;font-weight:800;color:#fff;margin:0 0 6px;letter-spacing:-0.02em; }
.prl-hero-period { font-family:'DM Mono',monospace;font-size:0.82rem;color:#6b7280; }

.prl-hero-chips { display:flex;gap:10px;margin-top:14px;flex-wrap:wrap; }
.prl-hero-chip { background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);border-radius:8px;padding:8px 14px;text-align:center; }
.prl-hero-chip-lbl { font-size:0.62rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#6b7280;display:block;margin-bottom:3px; }
.prl-hero-chip-val { font-family:'DM Mono',monospace;font-size:1rem;font-weight:700;color:#fff;font-variant-numeric:tabular-nums; }

.prl-hero-right { position:relative;z-index:1;display:flex;flex-direction:column;align-items:flex-end;gap:8px; }

/* ── Buttons ───────────────────────────────────────────────── */
.prl-btn-finalize {
    display:inline-flex;align-items:center;gap:8px;padding:11px 22px;
    background:#c8292a;color:#fff;border:none;border-radius:10px;
    font-family:'Sora',sans-serif;font-size:0.845rem;font-weight:700;cursor:pointer;
    transition:background 0.15s,box-shadow 0.15s;box-shadow:0 4px 14px rgba(200,41,42,0.3);
    text-decoration:none;white-space:nowrap;
}
.prl-btn-finalize:hover { background:#a81f20;color:#fff;box-shadow:0 6px 20px rgba(200,41,42,0.4); }

.prl-btn-submit {
    display:inline-flex;align-items:center;gap:8px;padding:11px 22px;
    background:#7c3aed;color:#fff;border:none;border-radius:10px;
    font-family:'Sora',sans-serif;font-size:0.845rem;font-weight:700;cursor:pointer;
    transition:background 0.15s,box-shadow 0.15s;box-shadow:0 4px 14px rgba(124,58,237,0.3);
    text-decoration:none;white-space:nowrap;
}
.prl-btn-submit:hover { background:#6d28d9;color:#fff;box-shadow:0 6px 20px rgba(124,58,237,0.4); }

.prl-btn-sec {
    display:inline-flex;align-items:center;gap:7px;padding:9px 16px;background:rgba(255,255,255,0.07);
    color:#9ca3af;border:1px solid rgba(255,255,255,0.1);border-radius:10px;
    font-family:'Sora',sans-serif;font-size:0.82rem;font-weight:600;
    text-decoration:none;cursor:pointer;transition:all 0.15s;white-space:nowrap;
}
.prl-btn-sec:hover { background:rgba(255,255,255,0.12);color:#fff;border-color:rgba(255,255,255,0.2); }

.prl-btn-locked {
    display:inline-flex;align-items:center;gap:7px;padding:9px 16px;
    background:rgba(255,255,255,0.04);color:#4b5563;border:1px solid rgba(255,255,255,0.07);
    border-radius:10px;font-family:'Sora',sans-serif;font-size:0.82rem;font-weight:600;cursor:not-allowed;
}

/* ── Summary totals bar ────────────────────────────────────── */
.prl-totals-bar {
    background:#fff;border:1px solid #e5e7eb;border-radius:12px;
    padding:14px 20px;display:flex;gap:24px;align-items:center;
    margin-bottom:16px;flex-wrap:wrap;
}
.prl-totals-item { }
.prl-totals-lbl { font-size:0.67rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#9ca3af; }
.prl-totals-val { font-family:'DM Mono',monospace;font-size:1rem;font-weight:700;color:#111827;font-variant-numeric:tabular-nums; }
.prl-totals-val.green { color:#16a34a; }
.prl-totals-divider { width:1px;height:32px;background:#e5e7eb;flex-shrink:0; }

/* ── Table ─────────────────────────────────────────────────── */
.prl-table-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden; }
.prl-table { width:100%;border-collapse:collapse;font-size:0.835rem; }
.prl-table thead tr { background:#f8f9fb;border-bottom:1px solid #e5e7eb; }
.prl-table thead th { padding:11px 16px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.09em;color:#6b7280;white-space:nowrap; }
.prl-table tbody tr { border-bottom:1px solid #f3f4f6;transition:background 0.1s; }
.prl-table tbody tr:last-child { border-bottom:none; }
.prl-table tbody tr:hover { background:#fafafa; }
.prl-table tbody td { padding:12px 16px;color:#374151;vertical-align:middle; }

.prl-emp-cell { display:flex;align-items:center;gap:10px; }
.prl-emp-avatar { width:34px;height:34px;border-radius:50%;background:#f3f4f6;display:flex;align-items:center;justify-content:center;font-size:0.74rem;font-weight:700;color:#6b7280;flex-shrink:0;border:1.5px solid #e5e7eb;text-transform:uppercase; }
.prl-emp-name { font-weight:600;color:#111827;font-size:0.845rem; }
.prl-emp-meta { font-size:0.72rem;color:#9ca3af;margin-top:1px; }
.prl-mono { font-family:'DM Mono',monospace;font-size:0.82rem;font-variant-numeric:tabular-nums; }
.prl-mono.c-red  { color:#c8292a;font-weight:500; }
.prl-mono.c-green{ color:#16a34a;font-weight:500; }
.prl-mono.c-bold { color:#111827;font-weight:700; }

.prl-actions { display:flex;align-items:center;gap:5px;justify-content:flex-end; }
.prl-action-btn { width:30px;height:30px;border-radius:7px;border:none;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;text-decoration:none;font-size:13px;transition:background 0.13s,color 0.13s;background:#f4f5f7;color:#6b7280;padding:0; }
.prl-action-btn:hover        { background:#eff6ff;color:#3b82f6; }
.prl-action-btn.edit:hover   { background:#fffbeb;color:#d97706; }
.prl-action-btn.prepare:hover { background:#f0fdf4;color:#16a34a; }
.prl-action-btn.remove:hover { background:#fff1f2;color:#e11d48; }
.prl-action-btn.locked { cursor:not-allowed;opacity:0.4; }

/* ── Status badges ─────────────────────────────────────────── */
.prl-status { display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;white-space:nowrap; }
.prl-status::before { content:'';width:5px;height:5px;border-radius:50%; }
.prl-status.s-draft     { background:#f3f4f6;color:#6b7280; }
.prl-status.s-draft::before { background:#9ca3af; }
.prl-status.s-finalized { background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe; }
.prl-status.s-finalized::before { background:#3b82f6; }
.prl-status.s-submitted { background:#f5f3ff;color:#7c3aed;border:1px solid #ddd6fe; }
.prl-status.s-submitted::before { background:#8b5cf6; }
.prl-status.s-paid      { background:#f0fdf4;color:#15803d;border:1px solid #86efac; }
.prl-status.s-paid::before { background:#22c55e; }

/* ── Lock notice ────────────────────────────────────────────── */
.prl-locked-notice {
    display:flex;align-items:center;gap:10px;background:#eff6ff;border:1px solid #bfdbfe;
    color:#1d4ed8;font-size:0.8rem;border-radius:10px;padding:12px 16px;margin-bottom:16px;
}

/* ── Flash ──────────────────────────────────────────────────── */
.prl-flash { display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:10px;font-size:0.82rem;font-weight:500;margin-bottom:16px;animation:flashIn 0.3s ease; }
.prl-flash.success { background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d; }
.prl-flash.error   { background:#fff0f0;border:1px solid #fecaca;color:#c8292a; }
.prl-flash.info    { background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8; }
@keyframes flashIn { from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)} }

.prl-table-scroll { overflow-x:auto; }
.prl-add-emp-card { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:14px 16px;margin-bottom:16px;display:flex;gap:10px;align-items:center;flex-wrap:wrap; }
.prl-add-emp-card select { min-width:280px;max-width:100%;padding:8px 10px;border:1px solid #e5e7eb;border-radius:8px;font-size:.82rem;color:#374151;background:#f9fafb; }
.prl-add-emp-btn { display:inline-flex;align-items:center;gap:6px;padding:8px 14px;background:#c8292a;color:#fff;border:0;border-radius:8px;font-size:.8rem;font-weight:700; }
.prl-add-emp-btn:hover { background:#a81f20; }
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

    <a href="{{ route('payroll.salary-computation.index') }}" class="prl-back-link">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Back to Payroll
    </a>

    {{-- Batch hero --}}
    <div class="prl-batch-hero">
        <div class="prl-hero-left">
            <div class="prl-hero-eyebrow {{ $batch->status }}">
                @if($batch->status === 'draft') ● Draft — Review &amp; edit before finalizing
                @elseif($batch->status === 'submitted') ● Submitted — Awaiting approval
                @elseif($batch->status === 'rejected') ● Rejected — Review note and reopen to edit
                @else ● {{ ucfirst($batch->status) }}
                @endif
            </div>
            <h1 class="prl-hero-title">Payroll Batch Review</h1>
            <div class="prl-hero-period">
                {{ $batch->period_start->format('F d, Y') }} — {{ $batch->period_end->format('F d, Y') }}
            </div>
            <div class="prl-hero-chips">
                <div class="prl-hero-chip">
                    <span class="prl-hero-chip-lbl">Employees</span>
                    <span class="prl-hero-chip-val">{{ $batch->employee_count }}</span>
                </div>
                <div class="prl-hero-chip">
                    <span class="prl-hero-chip-lbl">Total Net</span>
                    <span class="prl-hero-chip-val">₱{{ number_format($batch->total_net_pay, 2) }}</span>
                </div>
                <div class="prl-hero-chip">
                    <span class="prl-hero-chip-lbl">Generated</span>
                    <span class="prl-hero-chip-val" style="font-size:0.78rem;">{{ $batch->created_at->format('M d, h:i A') }}</span>
                </div>
            </div>
        </div>

        <div class="prl-hero-right">
            @if($batch->status === 'draft')
                {{-- Finalize --}}
                <form action="{{ route('payroll.batch.finalize', $batch) }}" method="POST"
                      data-sa-confirm="Finalize and submit this batch to accounting? Editing will be locked after this.">
                    @csrf
                    <button type="submit" class="prl-btn-finalize">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Finalize &amp; Submit
                    </button>
                </form>
                <a href="{{ route('payroll.salary-computation.index') }}" class="prl-btn-sec">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    Cancel
                </a>
            @elseif($batch->status === 'rejected')
                <form action="{{ route('payroll.batch.reopen', $batch) }}" method="POST"
                      data-sa-confirm="Reopen this rejected batch for editing?">
                    @csrf
                    <button type="submit" class="prl-btn-finalize">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v6h6M20 20v-6h-6"/><path stroke-linecap="round" stroke-linejoin="round" d="M20 8a8 8 0 00-14.828-3M4 16a8 8 0 0014.828 3"/></svg>
                        Reopen Batch
                    </button>
                </form>
            @else
                <span class="prl-btn-locked">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M7 11V7a5 5 0 0110 0v4"/></svg>
                    {{ ucfirst($batch->status) }}
                </span>
            @endif
        </div>
    </div>

    @if($batch->status === 'rejected' && $batch->rejection_note)
        <div class="prl-flash error" style="margin-bottom:16px;">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
            <strong>Rejection note:</strong> {{ $batch->rejection_note }}
        </div>
    @endif

    {{-- Lock notice when not draft --}}
    @if(!$batch->isEditable())
    <div class="prl-locked-notice">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M7 11V7a5 5 0 0110 0v4"/></svg>
        This batch is <strong>{{ $batch->status }}</strong> — individual payrolls can no longer be edited.
        @if($batch->finalizedBy) Finalized by {{ $batch->finalizedBy->name ?? 'system' }} on {{ $batch->finalized_at->format('M d, Y h:i A') }}. @endif
    </div>
    @endif

    @if($batch->isEditable())
    <div class="prl-add-emp-card">
        <div style="font-size:.78rem;color:#6b7280;font-weight:700;">Add employee to this batch</div>
        <form action="{{ route('payroll.batch.add-employee', $batch) }}" method="POST" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
            @csrf
            <select name="user_id" required>
                <option value="">Select employee...</option>
                @foreach($availableEmployees as $emp)
                    <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }}{{ $emp->position ? ' — '.$emp->position : '' }}</option>
                @endforeach
            </select>
            <button type="submit" class="prl-add-emp-btn">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Add Employee
            </button>
        </form>
        @if($availableEmployees->isEmpty())
            <span style="font-size:.75rem;color:#9ca3af;">All eligible employees already have payroll for this period.</span>
        @endif
    </div>
    @endif

    {{-- Totals bar --}}
    @php
        $totalGross = $batch->payrolls->sum('gross_pay');
        $totalDeductions = $batch->payrolls->sum('total_deductions');
        $totalNet = $batch->payrolls->sum('net_pay');
    @endphp
    <div class="prl-totals-bar">
        <div class="prl-totals-item">
            <div class="prl-totals-lbl">Total Gross</div>
            <div class="prl-totals-val">₱{{ number_format($totalGross, 2) }}</div>
        </div>
        <div class="prl-totals-divider"></div>
        <div class="prl-totals-item">
            <div class="prl-totals-lbl">Total Deductions</div>
            <div class="prl-totals-val">₱{{ number_format($totalDeductions, 2) }}</div>
        </div>
        <div class="prl-totals-divider"></div>
        <div class="prl-totals-item">
            <div class="prl-totals-lbl">Total Net Pay</div>
            <div class="prl-totals-val green">₱{{ number_format($totalNet, 2) }}</div>
        </div>
        <div class="prl-totals-divider"></div>
        <div class="prl-totals-item">
            <div class="prl-totals-lbl">Employees</div>
            <div class="prl-totals-val">{{ $batch->employee_count }}</div>
        </div>
    </div>

    {{-- Employee table --}}
    <div class="prl-table-card">
        <div class="prl-table-scroll">
            <table class="prl-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Employee</th>
                        <th class="text-end">Days</th>
                        <th class="text-end">Basic</th>
                        <th class="text-end">OT / Allow</th>
                        <th class="text-end">Deductions</th>
                        <th class="text-end">Net Pay</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($batch->payrolls as $i => $payroll)
                    @php
                        $initials = strtoupper(substr($payroll->user->first_name??'U',0,1).substr($payroll->user->last_name??'',0,1));
                        $sc = match($payroll->status){
                            'prepared'  => 's-finalized',
                            'submitted' => 's-submitted',
                            'approved'  => 's-paid',
                            'paid'      => 's-paid',
                            'rejected'  => 's-draft',
                            default     => 's-draft'
                        };
                        $otAllowances = $payroll->total_allowances;
                    @endphp
                    <tr>
                        <td style="color:#9ca3af;font-size:0.78rem;font-family:'DM Mono',monospace;">{{ $i + 1 }}</td>
                        <td>
                            <div class="prl-emp-cell">
                                <div class="prl-emp-avatar">{{ $initials }}</div>
                                <div>
                                    <div class="prl-emp-name">{{ $payroll->user->first_name }} {{ $payroll->user->last_name }}</div>
                                    <div class="prl-emp-meta">{{ $payroll->user->position ?? ($payroll->user->department ?? 'N/A') }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="text-end"><span class="prl-mono">{{ $payroll->days_worked }}</span></td>
                        <td class="text-end"><span class="prl-mono">₱{{ number_format($payroll->basic_salary, 2) }}</span></td>
                        <td class="text-end">
                            @if($otAllowances > 0)
                                <span class="prl-mono c-green">+₱{{ number_format($otAllowances, 2) }}</span>
                            @else
                                <span style="color:#d1d5db;font-size:0.75rem;">—</span>
                            @endif
                        </td>
                        <td class="text-end">
                            @if($payroll->total_deductions > 0)
                                <span class="prl-mono c-red">₱{{ number_format($payroll->total_deductions, 2) }}</span>
                            @else
                                <span style="color:#d1d5db;font-size:0.75rem;">—</span>
                            @endif
                        </td>
                        <td class="text-end"><span class="prl-mono c-bold">₱{{ number_format($payroll->net_pay, 2) }}</span></td>
                        <td class="text-center">
                            <span class="prl-status {{ $sc }}">
                                {{ $payroll->status === 'prepared' ? 'Prepared' : ucfirst($payroll->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="prl-actions">
                                <a href="{{ route('payroll.salary-computation.show', $payroll) }}"
                                   class="prl-action-btn" title="View">
                                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                @if($batch->isEditable())
                                    @if($payroll->status !== 'prepared')
                                        <form action="{{ route('payroll.batch.prepare-employee', [$batch, $payroll]) }}"
                                              method="POST"
                                              style="display:inline;">
                                            @csrf
                                            <button type="submit" class="prl-action-btn prepare" title="Mark as prepared">
                                                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('payroll.batch.edit-employee', [$batch, $payroll]) }}"
                                       class="prl-action-btn edit" title="Edit allowances &amp; deductions">
                                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('payroll.batch.remove-employee', [$batch, $payroll]) }}"
                                          method="POST"
                                          data-sa-confirm="Remove this employee from the batch?"
                                          style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="prl-action-btn remove" title="Remove employee from batch">
                                            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </form>
                                @else
                                    <span class="prl-action-btn locked" title="Batch is locked">
                                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M7 11V7a5 5 0 0110 0v4"/></svg>
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" style="text-align:center;padding:40px;color:#9ca3af;font-size:0.82rem;">No payroll records in this batch.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection