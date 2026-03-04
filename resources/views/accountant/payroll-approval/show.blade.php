@extends('layouts.layout')

@section('content')
<div class="col-md-8 offset-md-2">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="card-title mb-0">Payroll Review</span>
            <a href="{{ route('payroll-approval.index') }}" class="emp-action-btn emp-action-back">
                <i class="feather-arrow-left me-1"></i> Back
            </a>
        </div>
        <div class="card-body">

            {{-- Employee & Period --}}
            <div class="row mb-4">
                <div class="col-md-6">
                    <span class="prl-field-label">Employee</span>
                    @if ($payroll->employee)
                        <div class="prl-field-value fw-bold">
                            {{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}
                        </div>
                        <div class="prl-field-sub">{{ $payroll->employee->position ?? '' }}</div>
                        <div class="prl-field-sub">{{ $payroll->employee->department ?? '' }}</div>
                    @else
                        <div class="prl-field-value text-danger">Employee record not found.</div>
                    @endif
                </div>
                <div class="col-md-6">
                    <span class="prl-field-label">Payroll Period</span>
                    <div class="prl-field-value">
                        {{ $payroll->payroll_period_start?->format('M d, Y') ?? 'N/A' }}
                        –
                        {{ $payroll->payroll_period_end?->format('M d, Y') ?? 'N/A' }}
                    </div>
                    <div class="mt-1">
                        @php
                            $statusMap = [
                                'pending'  => ['bg' => '#fffbeb', 'color' => '#d97706'],
                                'approved' => ['bg' => '#f0f9ff', 'color' => '#0284c7'],
                                'rejected' => ['bg' => '#fff1f2', 'color' => '#e11d48'],
                            ];
                            $st = $statusMap[$payroll->status] ?? ['bg' => '#f4f5f7', 'color' => '#9898a8'];
                        @endphp
                        <span style="display:inline-block; background:{{ $st['bg'] }}; color:{{ $st['color'] }}; font-size:0.7rem; font-weight:700; padding:3px 10px; border-radius:20px; letter-spacing:0.4px; text-transform:uppercase;">
                            {{ ucfirst($payroll->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <hr>

            {{-- Earnings & Deductions --}}
            @php
                $basicSalary = $payroll->employee?->salary_rate * 15 ?? 0;
                $totalAllowances = $payroll->total_allowances ?? ($payroll->allowances?->sum('amount') ?? 0);
                $totalDeductions = $payroll->total_deductions ?? ($payroll->deductions?->sum('amount') ?? 0);
                $grossPay = $payroll->gross_pay ?? ($basicSalary + $totalAllowances);
                $netPay = $payroll->net_pay ?? ($grossPay - $totalDeductions);
            @endphp

            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="prl-section-label mb-3">Earnings</div>
                    <div class="prl-line">
                        <span class="prl-line-key">Basic Salary (15 days)</span>
                        <span class="prl-line-val">₱{{ number_format($basicSalary, 2) }}</span>
                    </div>
                    @if ($payroll->allowances && $payroll->allowances->isNotEmpty())
                        <div class="prl-sub-label mt-2 mb-1">Allowances</div>
                        @foreach ($payroll->allowances as $allowance)
                            <div class="prl-line">
                                <span class="prl-line-key">{{ $allowance->name }}</span>
                                <span class="prl-line-val">₱{{ number_format($allowance->amount, 2) }}</span>
                            </div>
                        @endforeach
                    @endif
                    <div class="prl-line prl-line-total mt-2 pt-2 border-top">
                        <span class="prl-line-key fw-bold">Gross Pay</span>
                        <span class="prl-line-val fw-bold">₱{{ number_format($grossPay, 2) }}</span>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="prl-section-label mb-3">Deductions</div>
                    @if ($payroll->deductions && $payroll->deductions->isNotEmpty())
                        @foreach ($payroll->deductions as $deduction)
                            <div class="prl-line">
                                <span class="prl-line-key">{{ $deduction->name }}</span>
                                <span class="prl-line-val">₱{{ number_format($deduction->amount, 2) }}</span>
                            </div>
                        @endforeach
                    @else
                        <div class="text-muted" style="font-size:.845rem;">No deductions</div>
                    @endif
                    <div class="prl-line mt-2 pt-2 border-top">
                        <span class="prl-line-key fw-bold" style="color:#e11d48;">Total Deductions</span>
                        <span class="prl-line-val fw-bold" style="color:#e11d48;">₱{{ number_format($totalDeductions, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Net Pay --}}
            <div class="prl-net-box mb-4">
                <span class="prl-net-label">Net Pay</span>
                <span class="prl-net-value">₱{{ number_format($netPay, 2) }}</span>
            </div>

            {{-- Approve / Reject Actions --}}
            @if ($payroll->status === 'pending')
                <div class="d-flex gap-2 pt-3 border-top">
                    <form action="{{ route('payroll-approval.approve', $payroll) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="emp-action-btn emp-action-approve"
                            onclick="return confirm('Approve this payroll?')">
                            <i class="feather-check me-1"></i> Approve
                        </button>
                    </form>
                    <form action="{{ route('payroll-approval.reject', $payroll) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="emp-action-btn emp-action-danger"
                            onclick="return confirm('Reject this payroll?')">
                            <i class="feather-x me-1"></i> Reject
                        </button>
                    </form>
                </div>
            @endif

        </div>
    </div>
</div>

<style>
.emp-action-btn {
    height: 30px;
    padding: 0 12px;
    font-size: 0.815rem;
    font-weight: 500;
    white-space: nowrap;
    width: auto;
}
</style>
@endsection