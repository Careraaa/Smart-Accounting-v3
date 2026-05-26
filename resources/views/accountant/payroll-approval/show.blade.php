@extends('layouts.layout')

@push('styles')
    @include('accountant._ui-styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
        .prl-page { font-family: 'Sora', sans-serif; }
        .prl-wrap { max-width: 820px; margin: 0 auto; padding-bottom: 56px; }

        .prl-back-link {
            display: inline-flex; align-items: center; gap: 6px; font-size: 0.78rem; font-weight: 600;
            color: #9ca3af; text-decoration: none; margin-bottom: 16px; transition: color 0.13s;
        }
        .prl-back-link:hover { color: #c8292a; }

        /* ── Hero ── */
        .prl-hero {
            background: #111827; border-radius: 16px; padding: 22px 26px;
            display: flex; align-items: flex-start; justify-content: space-between;
            gap: 20px; margin-bottom: 20px; flex-wrap: wrap; position: relative; overflow: hidden;
        }
        .prl-hero::before {
            content: ''; position: absolute; top: -50px; right: -50px; width: 180px; height: 180px;
            border-radius: 50%; background: rgba(200,41,42,.12); pointer-events: none;
        }
        .prl-hero-left  { position: relative; z-index: 1; }
        .prl-hero-right { position: relative; z-index: 1; display: flex; flex-direction: column; align-items: flex-end; gap: 8px; }
        .prl-hero-name   { font-size: 1.1rem; font-weight: 800; color: #fff; margin: 0 0 3px; letter-spacing: -0.02em; }
        .prl-hero-role   { font-size: 0.78rem; color: #6b7280; margin: 0 0 12px; }
        .prl-hero-period { font-family: 'DM Mono', monospace; font-size: 0.8rem; color: #9ca3af; }
        .prl-hero-chips  { display: flex; gap: 10px; margin-top: 14px; flex-wrap: wrap; }
        .prl-hero-chip   { background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.1); border-radius: 8px; padding: 8px 14px; text-align: center; }
        .prl-hero-chip-lbl { font-size: 0.62rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #6b7280; display: block; margin-bottom: 3px; }
        .prl-hero-chip-val { font-family: 'DM Mono', monospace; font-size: 0.95rem; font-weight: 700; color: #fff; font-variant-numeric: tabular-nums; }

        /* ── Status badge ── */
        .prl-status { display: inline-flex; align-items: center; gap: 5px; padding: 4px 12px; border-radius: 20px; font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; white-space: nowrap; }
        .prl-status::before { content: ''; width: 5px; height: 5px; border-radius: 50%; }
        .prl-status.s-pending   { background: rgba(217,119,6,.15);  color: #fbbf24; border: 1px solid rgba(251,191,36,.2); }
        .prl-status.s-pending::before   { background: #fbbf24; }
        .prl-status.s-submitted { background: rgba(139,92,246,.15); color: #a78bfa; border: 1px solid rgba(167,139,250,.2); }
        .prl-status.s-submitted::before { background: #a78bfa; }
        .prl-status.s-approved  { background: rgba(34,197,94,.15);  color: #4ade80; border: 1px solid rgba(74,222,128,.2); }
        .prl-status.s-approved::before  { background: #4ade80; }
        .prl-status.s-released  { background: rgba(34,197,94,.2);   color: #4ade80; border: 1px solid rgba(74,222,128,.25); }
        .prl-status.s-released::before  { background: #22c55e; }
        .prl-status.s-rejected  { background: rgba(239,68,68,.15);  color: #f87171; border: 1px solid rgba(248,113,113,.2); }
        .prl-status.s-rejected::before  { background: #ef4444; }

        /* ── Cards ── */
        .prl-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; overflow: hidden; margin-bottom: 16px; }
        .prl-card-head { padding: 14px 20px; border-bottom: 1px solid #f3f4f6; display: flex; align-items: center; gap: 10px; }
        .prl-card-head-icon { width: 30px; height: 30px; border-radius: 7px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .prl-card-head-icon.green { background: #f0fdf4; color: #16a34a; }
        .prl-card-head-icon.red   { background: #fff0f0; color: #c8292a; }
        .prl-card-head-icon.amber { background: #fffbeb; color: #d97706; }
        .prl-card-head-icon.blue  { background: #eff6ff; color: #2563eb; }
        .prl-card-head-title { font-size: 0.845rem; font-weight: 700; color: #111827; margin: 0; }
        .prl-card-head-sub   { font-size: 0.72rem; color: #9ca3af; margin: 0; }
        .prl-card-body { padding: 18px 20px; }

        /* ── Breakdown rows ── */
        .prl-breakdown { display: flex; flex-direction: column; }
        .prl-brow { display: flex; align-items: center; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f3f4f6; gap: 10px; }
        .prl-brow:last-child { border-bottom: none; padding-bottom: 0; }
        .prl-brow-lbl { font-size: 0.845rem; color: #6b7280; display: flex; align-items: center; gap: 7px; flex: 1; }
        .prl-brow-lbl.c-green { color: #16a34a; }
        .prl-brow-lbl.c-red   { color: #dc2626; }
        .prl-brow-lbl.c-bold  { color: #111827; font-weight: 700; font-size: .9rem; }
        .prl-badge { font-size: 0.67rem; font-weight: 600; background: #f3f4f6; color: #6b7280; border-radius: 4px; padding: 2px 6px; white-space: nowrap; }
        .prl-brow-val { font-size: .9rem; font-weight: 700; font-variant-numeric: tabular-nums; font-family: 'DM Mono', monospace; color: #111827; white-space: nowrap; }
        .prl-brow-val.c-green { color: #16a34a; }
        .prl-brow-val.c-red   { color: #dc2626; }

        /* ── OT/UT table ── */
        .prl-mini-table { width: 100%; border-collapse: collapse; font-size: 0.82rem; }
        .prl-mini-table thead tr { background: #f8f9fb; border-bottom: 1px solid #e5e7eb; }
        .prl-mini-table thead th { padding: 9px 14px; font-size: 0.67rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #6b7280; white-space: nowrap; }
        .prl-mini-table tbody tr { border-bottom: 1px solid #f3f4f6; }
        .prl-mini-table tbody tr:last-child { border-bottom: none; }
        .prl-mini-table tbody td { padding: 10px 14px; color: #374151; vertical-align: middle; }

        /* ── Net box ── */
        .prl-net { background: #111827; border-radius: 12px; padding: 18px 22px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
        .prl-net-lbl { font-size: .72rem; font-weight: 700; letter-spacing: .09em; text-transform: uppercase; color: #6b7280; }
        .prl-net-val { font-size: 1.55rem; font-weight: 800; color: #fff; font-variant-numeric: tabular-nums; letter-spacing: -.02em; font-family: 'DM Mono', monospace; }
    </style>
@endpush

@section('content')
@php
    $user = $payroll->user;
    $sc   = match($payroll->status) {
        'pending'           => 's-pending',
        'submitted'         => 's-submitted',
        'approved'          => 's-approved',
        'released', 'paid'  => 's-released',
        'rejected'          => 's-rejected',
        default             => 's-pending',
    };

    $overtimeAllowances  = $payroll->allowances->filter(fn($a) => str_starts_with($a->allowance_type, 'Overtime Pay'));
    $regularAllowances   = $payroll->allowances->reject(fn($a) => str_starts_with($a->allowance_type, 'Overtime Pay'));
    $undertimeDeductions = $payroll->deductions->filter(fn($d) => str_starts_with($d->deduction_type, 'Undertime Deduction'));
    $regularDeductions   = $payroll->deductions->reject(fn($d) => str_starts_with($d->deduction_type, 'Undertime Deduction'));
@endphp

<div class="col-12">
    <div class="remui-page prl-page">
        <div class="remui-backdrop"><div class="remui-grid"></div></div>

        <div class="prl-wrap">

            {{-- Back link ─────────────────────────────────────────── --}}
            @if($payroll->batch_id)
                <a href="{{ route('payroll-approval.batch', $payroll->batch_id) }}" class="prl-back-link">
                    <i class="feather-arrow-left"></i> Back to batch
                </a>
            @else
                <a href="{{ route('payroll-approval.index') }}" class="prl-back-link">
                    <i class="feather-arrow-left"></i> Back to approval queue
                </a>
            @endif

            {{-- Hero ──────────────────────────────────────────────── --}}
            <div class="prl-hero">
                <div class="prl-hero-left">
                    <h1 class="prl-hero-name">
                        {{ $user->first_name ?? 'Unknown' }} {{ $user->last_name ?? '' }}
                    </h1>
                    <p class="prl-hero-role">
                        {{ $user->position ?? $user->department ?? 'Employee' }}
                        @if($user->department && $user->position)
                            &nbsp;·&nbsp; {{ $user->department }}
                        @endif
                    </p>
                    <div class="prl-hero-period">
                        {{ $payroll->payroll_period_start?->format('F d, Y') ?? '—' }}
                        —
                        {{ $payroll->payroll_period_end?->format('F d, Y') ?? '—' }}
                    </div>
                    <div class="prl-hero-chips">
                        <div class="prl-hero-chip">
                            <span class="prl-hero-chip-lbl">Days worked</span>
                            <span class="prl-hero-chip-val">{{ $payroll->days_worked ?? '—' }}</span>
                        </div>
                        <div class="prl-hero-chip">
                            <span class="prl-hero-chip-lbl">Daily rate</span>
                            <span class="prl-hero-chip-val">₱{{ number_format($payroll->per_day_rate, 2) }}</span>
                        </div>
                        <div class="prl-hero-chip">
                            <span class="prl-hero-chip-lbl">Hourly rate</span>
                            <span class="prl-hero-chip-val">₱{{ number_format($payroll->hourly_rate, 2) }}</span>
                        </div>
                        @if($payroll->batch_id)
                        <div class="prl-hero-chip">
                            <span class="prl-hero-chip-lbl">Batch</span>
                            <span class="prl-hero-chip-val">#{{ str_pad($payroll->batch_id, 3, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="prl-hero-right">
                    <span class="prl-status {{ $sc }}">
                        {{ in_array($payroll->status, ['released', 'paid']) ? 'Released' : ucfirst($payroll->status) }}
                    </span>
                    @if($payroll->approvedBy)
                        <span style="font-size:0.72rem;color:#6b7280;font-family:'DM Mono',monospace;">
                            Approved by {{ $payroll->approvedBy->first_name }} {{ $payroll->approvedBy->last_name }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Earnings ───────────────────────────────────────────── --}}
            <div class="prl-card">
                <div class="prl-card-head">
                    <div class="prl-card-head-icon green">
                        <i class="feather-dollar-sign" style="font-size:13px;"></i>
                    </div>
                    <div>
                        <p class="prl-card-head-title">Earnings</p>
                        <p class="prl-card-head-sub">Basic pay, overtime, and allowances</p>
                    </div>
                </div>
                <div class="prl-card-body">
                    <div class="prl-breakdown">
                        <div class="prl-brow">
                            <span class="prl-brow-lbl">
                                Basic pay
                                <span class="prl-badge">daily rate × {{ $payroll->days_worked }} days</span>
                            </span>
                            <span class="prl-brow-val">₱{{ number_format($payroll->basic_salary, 2) }}</span>
                        </div>

                        @foreach($overtimeAllowances as $ot)
                        <div class="prl-brow">
                            <span class="prl-brow-lbl c-green">
                                <i class="feather-plus-circle" style="font-size:12px;"></i>
                                {{ $ot->allowance_type }}
                            </span>
                            <span class="prl-brow-val c-green">+₱{{ number_format($ot->amount, 2) }}</span>
                        </div>
                        @endforeach

                        @foreach($regularAllowances as $allow)
                        <div class="prl-brow">
                            <span class="prl-brow-lbl c-green">
                                <i class="feather-plus-circle" style="font-size:12px;"></i>
                                {{ $allow->allowance_type }}
                            </span>
                            <span class="prl-brow-val c-green">+₱{{ number_format($allow->amount, 2) }}</span>
                        </div>
                        @endforeach

                        @foreach($payroll->bonuses as $bonus)
                        <div class="prl-brow">
                            <span class="prl-brow-lbl" style="color:#9333ea;">
                                <i class="feather-plus-circle" style="font-size:12px;"></i>
                                {{ $bonus->bonus_type }}
                                @if($bonus->description)<span class="prl-badge">{{ $bonus->description }}</span>@endif
                            </span>
                            <span class="prl-brow-val" style="color:#9333ea;">+₱{{ number_format($bonus->amount, 2) }}</span>
                        </div>
                        @endforeach

                        <div class="prl-brow">
                            <span class="prl-brow-lbl c-bold">Gross pay</span>
                            <span class="prl-brow-val" style="font-size:1rem;">₱{{ number_format($payroll->gross_pay, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Deductions ──────────────────────────────────────────── --}}
            <div class="prl-card">
                <div class="prl-card-head">
                    <div class="prl-card-head-icon red">
                        <i class="feather-minus-circle" style="font-size:13px;"></i>
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
                                <i class="feather-minus-circle" style="font-size:12px;"></i>
                                {{ $d->deduction_type }}
                                @if($d->description)
                                    <span class="prl-badge">{{ $d->description }}</span>
                                @endif
                            </span>
                            <span class="prl-brow-val c-red">₱{{ number_format($d->amount, 2) }}</span>
                        </div>
                        @empty
                        @endforelse

                        @foreach($undertimeDeductions as $ut)
                        <div class="prl-brow">
                            <span class="prl-brow-lbl c-red">
                                <i class="feather-minus-circle" style="font-size:12px;"></i>
                                {{ $ut->deduction_type }}
                                <span class="prl-badge">attendance-based</span>
                            </span>
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
                            <span class="prl-brow-lbl c-bold">Total deductions</span>
                            <span class="prl-brow-val c-red" style="font-size:1rem;">₱{{ number_format($payroll->total_deductions, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- OT / UT breakdown ───────────────────────────────────── --}}
            @if($overtimeUndertimeBreakdown->count())
            <div class="prl-card">
                <div class="prl-card-head">
                    <div class="prl-card-head-icon amber">
                        <i class="feather-clock" style="font-size:13px;"></i>
                    </div>
                    <div>
                        <p class="prl-card-head-title">Overtime & undertime records</p>
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
                                <td style="font-family:'DM Mono',monospace;font-size:0.78rem;">
                                    {{ $record->date->format('M d, Y') }}
                                </td>
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

            {{-- Net pay ────────────────────────────────────────────── --}}
            <div class="prl-net">
                <span class="prl-net-lbl">Net pay</span>
                <span class="prl-net-val">₱{{ number_format($payroll->net_pay, 2) }}</span>
            </div>

            {{-- Employee info ───────────────────────────────────────── --}}
            <div class="prl-card">
                <div class="prl-card-head">
                    <div class="prl-card-head-icon blue">
                        <i class="feather-user" style="font-size:13px;"></i>
                    </div>
                    <div>
                        <p class="prl-card-head-title">Employee information</p>
                        <p class="prl-card-head-sub">For reference only</p>
                    </div>
                </div>
                <div class="prl-card-body">
                    <div class="row g-3" style="font-size:0.84rem;">
                        <div class="col-sm-6">
                            <div class="text-muted" style="font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;margin-bottom:3px;">Full name</div>
                            <div class="fw-bold">{{ $user->first_name }} {{ $user->middle_name ? $user->middle_name . ' ' : '' }}{{ $user->last_name }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted" style="font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;margin-bottom:3px;">Position / Department</div>
                            <div>{{ $user->position ?? '—' }} {{ $user->department ? '· ' . $user->department : '' }}</div>
                        </div>
                        @if($user->sss_number)
                        <div class="col-sm-6">
                            <div class="text-muted" style="font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;margin-bottom:3px;">SSS number</div>
                            <div class="font-monospace">{{ $user->sss_number }}</div>
                        </div>
                        @endif
                        @if($user->pagibig_number)
                        <div class="col-sm-6">
                            <div class="text-muted" style="font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;margin-bottom:3px;">Pag-IBIG number</div>
                            <div class="font-monospace">{{ $user->pagibig_number }}</div>
                        </div>
                        @endif
                        @if($user->philhealth_number)
                        <div class="col-sm-6">
                            <div class="text-muted" style="font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;margin-bottom:3px;">PhilHealth number</div>
                            <div class="font-monospace">{{ $user->philhealth_number }}</div>
                        </div>
                        @endif
                        @if($user->tin_number)
                        <div class="col-sm-6">
                            <div class="text-muted" style="font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;margin-bottom:3px;">TIN</div>
                            <div class="font-monospace">{{ $user->tin_number }}</div>
                        </div>
                        @endif
                        @if($user->date_of_hire)
                        <div class="col-sm-6">
                            <div class="text-muted" style="font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;margin-bottom:3px;">Date of hire</div>
                            <div>{{ $user->date_of_hire->format('F d, Y') }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
