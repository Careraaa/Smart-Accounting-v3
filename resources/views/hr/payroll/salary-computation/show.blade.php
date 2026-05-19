@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
.prl-page { font-family: 'Sora', sans-serif; }
.prl-wrap { max-width: 760px; margin: 0 auto; padding-bottom: 56px; }

.prl-back-link { display:inline-flex;align-items:center;gap:8px;font-size:0.84rem;font-weight:700;color:#374151;text-decoration:none;margin-bottom:16px;padding:9px 16px;background:#fff;border:1px solid #e5e7eb;border-radius:10px;cursor:pointer;transition:all 0.13s; }
.prl-back-link:hover { border-color:#c8292a;color:#c8292a;background:#fff5f5; }

/* ── Hero ────────────────────────────────────────────────────── */
.prl-hero {
    background:#111827;border-radius:16px;padding:22px 26px;
    display:flex;align-items:center;justify-content:space-between;
    gap:20px;margin-bottom:20px;flex-wrap:wrap;
    position:relative;overflow:hidden;
}
.prl-hero::before { content:'';position:absolute;top:-50px;right:-50px;width:180px;height:180px;border-radius:50%;background:rgba(200,41,42,0.12);pointer-events:none; }
.prl-hero-left { position:relative;z-index:1; }
.prl-hero-name { font-size:1.1rem;font-weight:800;color:#fff;margin:0 0 3px;letter-spacing:-0.02em; }
.prl-hero-role { font-size:0.78rem;color:#6b7280;margin:0 0 12px; }
.prl-hero-period { font-family:'DM Mono',monospace;font-size:0.8rem;color:#9ca3af; }
.prl-hero-right { position:relative;z-index:1;display:flex;flex-direction:column;align-items:flex-end;gap:8px; }

.prl-status { display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:20px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;white-space:nowrap; }
.prl-status::before { content:'';width:5px;height:5px;border-radius:50%; }
.prl-status.s-pending   { background:rgba(217,119,6,0.15);color:#fbbf24;border:1px solid rgba(251,191,36,0.2); }
.prl-status.s-pending::before { background:#fbbf24; }
.prl-status.s-finalized { background:rgba(59,130,246,0.15);color:#60a5fa;border:1px solid rgba(96,165,250,0.2); }
.prl-status.s-finalized::before { background:#60a5fa; }
.prl-status.s-submitted { background:rgba(139,92,246,0.15);color:#a78bfa;border:1px solid rgba(167,139,250,0.2); }
.prl-status.s-submitted::before { background:#a78bfa; }
.prl-status.s-approved  { background:rgba(34,197,94,0.15);color:#4ade80;border:1px solid rgba(74,222,128,0.2); }
.prl-status.s-approved::before { background:#4ade80; }
.prl-status.s-released  { background:rgba(34,197,94,0.2);color:#4ade80;border:1px solid rgba(74,222,128,0.25); }
.prl-status.s-released::before { background:#22c55e; }
.prl-status.s-rejected  { background:rgba(239,68,68,0.15);color:#f87171;border:1px solid rgba(248,113,113,0.2); }
.prl-status.s-rejected::before { background:#ef4444; }

.prl-hero-chips { display:flex;gap:10px;margin-top:14px;flex-wrap:wrap; }
.prl-hero-chip { background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);border-radius:8px;padding:8px 14px;text-align:center; }
.prl-hero-chip-lbl { font-size:0.62rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#6b7280;display:block;margin-bottom:3px; }
.prl-hero-chip-val { font-family:'DM Mono',monospace;font-size:0.95rem;font-weight:700;color:#fff;font-variant-numeric:tabular-nums; }

/* ── Cards ───────────────────────────────────────────────────── */
.prl-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;margin-bottom:16px; }
.prl-card-head { padding:14px 20px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;gap:10px; }
.prl-card-head-icon { width:30px;height:30px;border-radius:7px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.prl-card-head-icon.green { background:#f0fdf4;color:#16a34a; }
.prl-card-head-icon.red   { background:#fff0f0;color:#c8292a; }
.prl-card-head-icon.amber { background:#fffbeb;color:#d97706; }
.prl-card-head-icon.blue  { background:#eff6ff;color:#2563eb; }
.prl-card-head-title { font-size:0.845rem;font-weight:700;color:#111827;margin:0; }
.prl-card-head-sub   { font-size:0.72rem;color:#9ca3af;margin:0; }
.prl-card-body { padding:18px 20px; }

/* ── Breakdown rows ──────────────────────────────────────────── */
.prl-breakdown { display:flex;flex-direction:column; }
.prl-brow { display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f3f4f6;gap:10px; }
.prl-brow:last-child { border-bottom:none;padding-bottom:0; }
.prl-brow-lbl { font-size:0.845rem;color:#6b7280;display:flex;align-items:center;gap:7px;flex:1; }
.prl-brow-lbl.c-green { color:#16a34a; }
.prl-brow-lbl.c-red   { color:#dc2626; }
.prl-brow-lbl.c-bold  { color:#111827;font-weight:700;font-size:.9rem; }
.prl-badge { font-size:0.67rem;font-weight:600;background:#f3f4f6;color:#6b7280;border-radius:4px;padding:2px 6px;white-space:nowrap; }
.prl-brow-val { font-size:.9rem;font-weight:700;font-variant-numeric:tabular-nums;font-family:'DM Mono',monospace;color:#111827;white-space:nowrap; }
.prl-brow-val.c-green { color:#16a34a; }
.prl-brow-val.c-red   { color:#dc2626; }

/* ── Stat chips ──────────────────────────────────────────────── */
.prl-chips { display:flex;gap:10px;flex-wrap:wrap; }
.prl-chip { flex:1;min-width:100px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px;padding:12px 14px;text-align:center; }
.prl-chip-lbl { display:block;font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#9ca3af;margin-bottom:4px; }
.prl-chip-val { font-size:1.1rem;font-weight:800;color:#111827;font-variant-numeric:tabular-nums;font-family:'DM Mono',monospace; }

/* ── OT/UT table ─────────────────────────────────────────────── */
.prl-mini-table { width:100%;border-collapse:collapse;font-size:0.82rem; }
.prl-mini-table thead tr { background:#f8f9fb;border-bottom:1px solid #e5e7eb; }
.prl-mini-table thead th { padding:9px 14px;font-size:0.67rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#6b7280;white-space:nowrap; }
.prl-mini-table tbody tr { border-bottom:1px solid #f3f4f6; }
.prl-mini-table tbody tr:last-child { border-bottom:none; }
.prl-mini-table tbody td { padding:10px 14px;color:#374151;vertical-align:middle; }

/* ── Net box ─────────────────────────────────────────────────── */
.prl-net { background:#111827;border-radius:12px;padding:18px 22px;display:flex;align-items:center;justify-content:space-between;margin-bottom:20px; }
.prl-net-lbl { font-size:.72rem;font-weight:700;letter-spacing:.09em;text-transform:uppercase;color:#6b7280; }
.prl-net-val { font-size:1.55rem;font-weight:800;color:#fff;font-variant-numeric:tabular-nums;letter-spacing:-.02em;font-family:'DM Mono',monospace; }

/* ── Footer ──────────────────────────────────────────────────── */
.prl-footer { display:flex;gap:10px;padding-top:20px;border-top:1px solid #f3f4f6;flex-wrap:wrap; }
.prl-btn-payslip {
    display:inline-flex;align-items:center;gap:8px;padding:10px 20px;
    background:#c8292a;color:#fff;border:none;border-radius:10px;
    font-family:'Sora',sans-serif;font-size:0.845rem;font-weight:700;
    text-decoration:none;transition:background 0.15s;
}
.prl-btn-payslip:hover { background:#a81f20;color:#fff; }
.prl-btn-delete {
    display:inline-flex;align-items:center;gap:7px;padding:10px 18px;
    background:#fff;color:#dc2626;border:1px solid #fecaca;border-radius:10px;
    font-family:'Sora',sans-serif;font-size:0.845rem;font-weight:600;cursor:pointer;
    transition:background 0.13s,border-color 0.13s;
}
.prl-btn-delete:hover { background:#fff0f0;border-color:#fca5a5; }
</style>
@endpush

@section('content')
<div class="prl-page">
<div class="prl-wrap">

    <button type="button" onclick="history.back()" class="prl-back-link">
        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Back
    </button>

    @php
        $sc = match($payroll->status) {
            'pending'   => 's-pending',
            'finalized' => 's-finalized',
            'submitted' => 's-submitted',
            'approved'  => 's-approved',
            'released', 'paid' => 's-released',
            'rejected'  => 's-rejected',
            default     => 's-pending',
        };
        $overtimeAllowances = $payroll->allowances->filter(fn($a) => str_starts_with($a->allowance_type, 'Overtime Pay'));
        $regularAllowances  = $payroll->allowances->reject(fn($a) => str_starts_with($a->allowance_type, 'Overtime Pay'));
        $undertimeDeductions= $payroll->deductions->filter(fn($d) => str_starts_with($d->deduction_type, 'Undertime Deduction'));
        $regularDeductions  = $payroll->deductions->reject(fn($d) => str_starts_with($d->deduction_type, 'Undertime Deduction'));
        $otAllowanceTotal = $overtimeAllowances->sum('amount');
        $regularAllowanceTotal = $regularAllowances->sum('amount');
        $holidayPay = max(0, (float)($payroll->gross_pay ?? 0) - (float)($payroll->basic_salary ?? 0) - $otAllowanceTotal - $regularAllowanceTotal);
    @endphp

    {{-- Hero ──────────────────────────────────────────────────────── --}}
    <div class="prl-hero">
        <div class="prl-hero-left">
            <h1 class="prl-hero-name">{{ $payroll->user->first_name }} {{ $payroll->user->last_name }}</h1>
            <p class="prl-hero-role">{{ $payroll->user->position ?? ($payroll->user->department ?? 'Employee') }}</p>
            <div class="prl-hero-period">
                {{ $payroll->payroll_period_start->format('F d, Y') }} — {{ $payroll->payroll_period_end->format('F d, Y') }}
            </div>
            <div class="prl-hero-chips">
                <div class="prl-hero-chip">
                    <span class="prl-hero-chip-lbl">Days Worked</span>
                    <span class="prl-hero-chip-val">{{ $payroll->days_worked }}</span>
                </div>
                @php
                    $daysAbsent = \App\Models\Attendance::where('user_id', $payroll->user_id)
                        ->whereBetween('date', [$payroll->payroll_period_start, $payroll->payroll_period_end])
                        ->where('status', 'absent')
                        ->count();
                @endphp
                @if($daysAbsent > 0)
                <div class="prl-hero-chip" style="border-color:#fecaca;">
                    <span class="prl-hero-chip-lbl" style="color:#f87171;">Days Absent</span>
                    <span class="prl-hero-chip-val" style="color:#f87171;">{{ $daysAbsent }}</span>
                </div>
                @endif
                <div class="prl-hero-chip">
                    <span class="prl-hero-chip-lbl">Daily Rate</span>
                    <span class="prl-hero-chip-val">₱{{ number_format($payroll->per_day_rate, 2) }}</span>
                </div>
                <div class="prl-hero-chip">
                    <span class="prl-hero-chip-lbl">Hourly Rate</span>
                    <span class="prl-hero-chip-val">₱{{ number_format($payroll->hourly_rate, 2) }}</span>
                </div>
            </div>
        </div>
        <div class="prl-hero-right">
            <span class="prl-status {{ $sc }}">{{ in_array($payroll->status, ['released', 'paid'], true) ? 'Released' : ucfirst($payroll->status) }}</span>
            @if($payroll->approvedBy)
                <span style="font-size:0.72rem;color:#6b7280;font-family:'DM Mono',monospace;">
                    Approved by {{ $payroll->approvedBy->first_name }} {{ $payroll->approvedBy->last_name }}
                </span>
            @endif
        </div>
    </div>

    {{-- Earnings ─────────────────────────────────────────────────────── --}}
    <div class="prl-card">
        <div class="prl-card-head">
            <div class="prl-card-head-icon green">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="prl-card-head-title">Earnings</p>
                <p class="prl-card-head-sub">Basic pay, overtime, and allowances</p>
            </div>
        </div>
        <div class="prl-card-body">
            <div class="prl-breakdown">
                <div class="prl-brow">
                    <span class="prl-brow-lbl">Basic Pay <span class="prl-badge">daily rate × {{ $payroll->days_worked }} days</span></span>
                    <span class="prl-brow-val">₱{{ number_format($payroll->basic_salary, 2) }}</span>
                </div>

                @if($holidayPay > 0)
                <div class="prl-brow">
                    <span class="prl-brow-lbl c-green">+ Holiday Pay <span class="prl-badge">premium</span></span>
                    <span class="prl-brow-val c-green">+₱{{ number_format($holidayPay, 2) }}</span>
                </div>
                @endif

                @foreach($overtimeAllowances as $ot)
                <div class="prl-brow">
                    <span class="prl-brow-lbl c-green">+ {{ $ot->allowance_type }}</span>
                    <span class="prl-brow-val c-green">+₱{{ number_format($ot->amount, 2) }}</span>
                </div>
                @endforeach

                @foreach($regularAllowances as $allow)
                <div class="prl-brow">
                    <span class="prl-brow-lbl c-green">+ {{ $allow->allowance_type }}</span>
                    <span class="prl-brow-val c-green">+₱{{ number_format($allow->amount, 2) }}</span>
                </div>
                @endforeach

                <div class="prl-brow">
                    <span class="prl-brow-lbl c-bold">Gross Pay</span>
                    <span class="prl-brow-val" style="font-size:1rem;">₱{{ number_format($payroll->gross_pay, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Deductions ───────────────────────────────────────────────────── --}}
    <div class="prl-card">
        <div class="prl-card-head">
            <div class="prl-card-head-icon red">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
            </div>
            <div>
                <p class="prl-card-head-title">Deductions</p>
                <p class="prl-card-head-sub">Statutory contributions and other deductions</p>
            </div>
        </div>
        <div class="prl-card-body">
            <div class="prl-breakdown">
                @forelse($regularDeductions as $d)
                <div class="prl-brow">
                    <span class="prl-brow-lbl c-red">
                        − {{ $d->deduction_type }}
                        @if($d->description)<span class="prl-badge">{{ $d->description }}</span>@endif
                    </span>
                    <span class="prl-brow-val c-red">₱{{ number_format($d->amount, 2) }}</span>
                </div>
                @empty
                @endforelse

                @foreach($undertimeDeductions as $ut)
                <div class="prl-brow">
                    <span class="prl-brow-lbl c-red">− {{ $ut->deduction_type }} <span class="prl-badge">attendance-based</span></span>
                    <span class="prl-brow-val c-red">₱{{ number_format($ut->amount, 2) }}</span>
                </div>
                @endforeach

                @if($payroll->deductions->isEmpty())
                <div class="prl-brow">
                    <span style="font-size:0.82rem;color:#d1d5db;font-style:italic;">No deductions</span>
                    <span></span>
                </div>
                @endif

                <div class="prl-brow">
                    <span class="prl-brow-lbl c-bold">Total Deductions</span>
                    <span class="prl-brow-val c-red" style="font-size:1rem;">₱{{ number_format($payroll->total_deductions, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Bonuses ─────────────────────────────────────────────────────── --}}
    @if($payroll->bonuses->count())
    <div class="prl-card">
        <div class="prl-card-head">
            <div class="prl-card-head-icon" style="background:#f3e8ff;color:#9333ea;">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>
            </div>
            <div>
                <p class="prl-card-head-title">Bonuses</p>
                <p class="prl-card-head-sub">Performance, holiday, and special bonuses</p>
            </div>
        </div>
        <div class="prl-card-body">
            <div class="prl-breakdown">
                @foreach($payroll->bonuses as $bonus)
                <div class="prl-brow">
                    <span class="prl-brow-lbl" style="color:#9333ea;">
                        + {{ $bonus->bonus_type }}
                        @if($bonus->description)<span class="prl-badge">{{ $bonus->description }}</span>@endif
                    </span>
                    <span class="prl-brow-val" style="color:#9333ea;">+₱{{ number_format($bonus->amount, 2) }}</span>
                </div>
                @endforeach
                <div class="prl-brow">
                    <span class="prl-brow-lbl c-bold">Total Bonuses</span>
                    <span class="prl-brow-val" style="font-size:1rem;color:#9333ea;">+₱{{ number_format($payroll->total_bonuses, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- OT / UT Breakdown ────────────────────────────────────────────── --}}
    @if($overtimeUndertimeBreakdown->count())
    <div class="prl-card">
        <div class="prl-card-head">
            <div class="prl-card-head-icon amber">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 6v6l4 2"/></svg>
            </div>
            <div>
                <p class="prl-card-head-title">Overtime & Undertime Records</p>
                <p class="prl-card-head-sub">Approved records within this payroll period</p>
            </div>
        </div>
        <div class="prl-card-body" style="padding:0;">
            <table class="prl-mini-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Hours</th>
                        <th>Reason</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($overtimeUndertimeBreakdown as $record)
                    @php
                        $isOT   = $record->type === 'overtime';
                        $amount = round($payroll->hourly_rate * $record->hours, 2);
                    @endphp
                    <tr>
                        <td style="font-family:'DM Mono',monospace;font-size:0.78rem;">{{ $record->date->format('M d, Y') }}</td>
                        <td>
                            <span style="font-size:0.68rem;font-weight:700;padding:2px 8px;border-radius:20px;
                                background:{{ $isOT ? '#f0fdf4' : '#fff0f0' }};
                                color:{{ $isOT ? '#16a34a' : '#dc2626' }};">
                                {{ ucfirst($record->type) }}
                            </span>
                        </td>
                        <td style="font-family:'DM Mono',monospace;">{{ number_format($record->hours, 2) }} hrs</td>
                        <td style="color:#9ca3af;font-size:0.78rem;">{{ $record->reason ?? '—' }}</td>
                        <td class="text-end" style="font-family:'DM Mono',monospace;font-weight:600;color:{{ $isOT ? '#16a34a' : '#dc2626' }};">
                            {{ $isOT ? '+' : '−' }}₱{{ number_format($amount, 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Net Pay ──────────────────────────────────────────────────────── --}}
    <div class="prl-net">
        <span class="prl-net-lbl">Net Pay</span>
        <span class="prl-net-val">₱{{ number_format($payroll->net_pay, 2) }}</span>
    </div>

    {{-- Actions ──────────────────────────────────────────────────────── --}}
    <div class="prl-footer">
        <a href="{{ route('payroll.generatePayslip', $payroll) }}" class="prl-btn-payslip">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Generate Payslip
        </a>
        <form action="{{ route('payroll.salary-computation.destroy', $payroll) }}" method="POST" data-sa-confirm="Delete this payroll record?">
            @csrf @method('DELETE')
            <button type="submit" class="prl-btn-delete">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Delete
            </button>
        </form>
    </div>

</div>
</div>
@endsection