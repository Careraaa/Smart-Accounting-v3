@extends('layouts.layout')

@push('styles')
    @include('accountant._ui-styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
        .prl-page { font-family: 'Sora', sans-serif; }
        .prl-wrap { max-width: 820px; margin: 0 auto; padding-bottom: 48px; }
        .prl-back-link {
            display: inline-flex; align-items: center; gap: 6px; font-size: 0.78rem; font-weight: 600; color: #9ca3af;
            text-decoration: none; margin-bottom: 16px; transition: color 0.13s;
        }
        .prl-back-link:hover { color: #c8292a; }
        .prl-hero {
            background: #111827; border-radius: 16px; padding: 22px 26px; display: flex; align-items: flex-start;
            justify-content: space-between; gap: 20px; margin-bottom: 20px; flex-wrap: wrap; position: relative; overflow: hidden;
        }
        .prl-hero::before {
            content: ''; position: absolute; top: -50px; right: -50px; width: 180px; height: 180px; border-radius: 50%;
            background: rgba(200, 41, 42, 0.12); pointer-events: none;
        }
        .prl-hero-left { position: relative; z-index: 1; }
        .prl-hero-name { font-size: 1.1rem; font-weight: 800; color: #fff; margin: 0 0 3px; letter-spacing: -0.02em; }
        .prl-hero-role { font-size: 0.78rem; color: #6b7280; margin: 0 0 12px; }
        .prl-hero-period { font-family: 'DM Mono', monospace; font-size: 0.8rem; color: #9ca3af; }
        .prl-hero-right { position: relative; z-index: 1; }
        .prl-status {
            display: inline-flex; align-items: center; gap: 5px; padding: 4px 12px; border-radius: 20px;
            font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; white-space: nowrap;
        }
        .prl-status::before { content: ''; width: 5px; height: 5px; border-radius: 50%; }
        .prl-status.s-pending { background: rgba(217, 119, 6, 0.15); color: #fbbf24; border: 1px solid rgba(251, 191, 36, 0.2); }
        .prl-status.s-pending::before { background: #fbbf24; }
        .prl-status.s-submitted { background: rgba(139, 92, 246, 0.15); color: #a78bfa; border: 1px solid rgba(167, 139, 250, 0.2); }
        .prl-status.s-submitted::before { background: #a78bfa; }
        .prl-status.s-approved { background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(74, 222, 128, 0.2); }
        .prl-status.s-approved::before { background: #4ade80; }
        .prl-status.s-released { background: rgba(34, 197, 94, 0.2); color: #4ade80; border: 1px solid rgba(74, 222, 128, 0.25); }
        .prl-status.s-released::before { background: #22c55e; }
        .prl-status.s-rejected { background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(248, 113, 113, 0.2); }
        .prl-status.s-rejected::before { background: #ef4444; }
        .prl-card {
            background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; overflow: hidden; margin-bottom: 16px;
        }
        .prl-card-head {
            padding: 14px 20px; border-bottom: 1px solid #f3f4f6; font-size: 0.845rem; font-weight: 700; color: #111827;
        }
        .prl-card-body { padding: 18px 20px; }
        .prl-net {
            background: #111827; border-radius: 12px; padding: 18px 22px; display: flex; align-items: center;
            justify-content: space-between; margin-bottom: 16px;
        }
        .prl-net-lbl { font-size: 0.72rem; font-weight: 700; letter-spacing: 0.09em; text-transform: uppercase; color: #6b7280; }
        .prl-net-val {
            font-size: 1.45rem; font-weight: 800; color: #fff; font-variant-numeric: tabular-nums;
            font-family: 'DM Mono', monospace;
        }
        .prl-actions .emp-action-btn { height: auto; min-height: 36px; padding: 8px 16px; font-weight: 600; }
    </style>
@endpush

@section('content')
@php
    $sc = match ($payroll->status) {
        'pending' => 's-pending',
        'submitted' => 's-submitted',
        'approved' => 's-approved',
        'released', 'paid' => 's-released',
        'rejected' => 's-rejected',
        default => 's-pending',
    };
    $basicSalary = ($payroll->employee?->salary_rate ?? 0) * 15;
    $totalAllowances = $payroll->total_allowances ?? ($payroll->allowances?->sum('amount') ?? 0);
    $totalDeductions = $payroll->total_deductions ?? ($payroll->deductions?->sum('amount') ?? 0);
    $grossPay = $payroll->gross_pay ?? ($basicSalary + $totalAllowances);
    $netPay = $payroll->net_pay ?? ($grossPay - $totalDeductions);
@endphp

<div class="col-12">
    <div class="remui-page prl-page">
        <div class="remui-backdrop">
            <div class="remui-grid"></div>
        </div>

        <div class="prl-wrap">
            <a href="{{ route('payroll-approval.index') }}" class="prl-back-link">
                <i class="feather-arrow-left"></i> Back to approval queue
            </a>

            <div class="prl-hero">
                <div class="prl-hero-left">
                    <h1 class="prl-hero-name">
                        @if ($payroll->employee)
                            {{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}
                        @else
                            Unknown employee
                        @endif
                    </h1>
                    <p class="prl-hero-role">{{ $payroll->employee->position ?? ($payroll->employee->department ?? 'Employee') }}</p>
                    <div class="prl-hero-period">
                        {{ $payroll->payroll_period_start?->format('F d, Y') ?? '—' }}
                        —
                        {{ $payroll->payroll_period_end?->format('F d, Y') ?? '—' }}
                    </div>
                </div>
                <div class="prl-hero-right">
                    <span class="prl-status {{ $sc }}">{{ in_array($payroll->status, ['released', 'paid'], true) ? 'Released' : ucfirst($payroll->status) }}</span>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="prl-card h-100">
                        <div class="prl-card-head">Earnings</div>
                        <div class="prl-card-body">
                            <div class="prl-line">
                                <span class="prl-line-key">Basic salary (15 days)</span>
                                <span class="prl-line-val">₱{{ number_format($basicSalary, 2) }}</span>
                            </div>
                            @if ($payroll->allowances && $payroll->allowances->isNotEmpty())
                                <div class="prl-sub-label mt-3 mb-1">Allowances</div>
                                @foreach ($payroll->allowances as $allowance)
                                    <div class="prl-line">
                                        <span class="prl-line-key">{{ $allowance->allowance_type }}</span>
                                        <span class="prl-line-val">₱{{ number_format($allowance->amount, 2) }}</span>
                                    </div>
                                @endforeach
                            @endif
                            <div class="prl-line prl-line-total mt-3 pt-3 border-top">
                                <span class="prl-line-key fw-bold">Gross pay</span>
                                <span class="prl-line-val fw-bold">₱{{ number_format($grossPay, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="prl-card h-100">
                        <div class="prl-card-head">Deductions</div>
                        <div class="prl-card-body">
                            @if ($payroll->deductions && $payroll->deductions->isNotEmpty())
                                @foreach ($payroll->deductions as $deduction)
                                    <div class="prl-line">
                                        <span class="prl-line-key">{{ $deduction->deduction_type }}</span>
                                        <span class="prl-line-val">₱{{ number_format($deduction->amount, 2) }}</span>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-muted" style="font-size: 0.845rem;">No deductions</div>
                            @endif
                            <div class="prl-line mt-3 pt-3 border-top">
                                <span class="prl-line-key fw-bold" style="color: #e11d48;">Total deductions</span>
                                <span class="prl-line-val fw-bold" style="color: #e11d48;">₱{{ number_format($totalDeductions, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="prl-net">
                <span class="prl-net-lbl">Net pay</span>
                <span class="prl-net-val">₱{{ number_format($netPay, 2) }}</span>
            </div>

            <div class="text-muted small mt-3">
                Individual payroll approval is disabled. Please use the batch approval screen.
            </div>
        </div>
    </div>
</div>
@endsection
