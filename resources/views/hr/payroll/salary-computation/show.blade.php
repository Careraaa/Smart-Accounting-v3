@extends('layouts.layout')

@section('content')
<div class="col-md-8 offset-md-2">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="card-title mb-0">Payroll Details</span>
            <a href="{{ route('payroll.index') }}" class="emp-action-btn emp-action-back">
                <i class="feather-arrow-left me-1"></i> Back
            </a>
        </div>
        <div class="card-body">

            {{-- Employee & Period --}}
            <div class="row mb-4">
                <div class="col-md-6">
                    <span class="prl-field-label">Employee</span>
                    <div class="prl-field-value fw-bold">
                        {{ optional($payroll->employee)->first_name }} {{ optional($payroll->employee)->last_name ?? 'N/A' }}
                    </div>
                    <div class="prl-field-sub">{{ optional($payroll->employee)->position ?? '' }}</div>
                </div>
                <div class="col-md-6">
                    <span class="prl-field-label">Payroll Period</span>
                    <div class="prl-field-value">
                        {{ $payroll->payroll_period_start->format('M d, Y') }} – {{ $payroll->payroll_period_end->format('M d, Y') }}
                    </div>
                    <div class="mt-1">
                        @php
                            $statusMap = [
                                'draft'     => ['bg' => '#f4f5f7', 'color' => '#9898a8'],
                                'pending'   => ['bg' => '#fffbeb', 'color' => '#d97706'],
                                'submitted' => ['bg' => '#fffbeb', 'color' => '#d97706'],
                                'approved'  => ['bg' => '#f0f9ff', 'color' => '#0284c7'],
                                'paid'      => ['bg' => '#f0fdf4', 'color' => '#16a34a'],
                                'rejected'  => ['bg' => '#fff1f2', 'color' => '#e11d48'],
                            ];
                            $st = $statusMap[$payroll->status] ?? $statusMap['draft'];
                        @endphp
                        <span style="display:inline-block; background:{{ $st['bg'] }}; color:{{ $st['color'] }}; font-size:0.7rem; font-weight:700; padding:3px 10px; border-radius:20px; letter-spacing:0.4px; text-transform:uppercase;">
                            {{ ucfirst($payroll->status) }}
                        </span>
                    </div>
                    @if($payroll->approvedBy)
                        <div class="prl-field-sub mt-1">Approved by {{ $payroll->approvedBy->first_name }} {{ $payroll->approvedBy->last_name }}</div>
                    @endif
                </div>
            </div>

            <hr>

            {{-- Earnings & Deductions --}}
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="prl-section-label mb-3">Earnings</div>
                    <div class="prl-line">
                        <span class="prl-line-key text-muted">Per Day Rate</span>
                        <span class="prl-line-val text-muted">₱{{ number_format($payroll->per_day_rate, 2) }}</span>
                    </div>
                    <div class="prl-line">
                        <span class="prl-line-key">Basic Pay (15 days)</span>
                        <span class="prl-line-val">₱{{ number_format($payroll->basic_salary, 2) }}</span>
                    </div>
                    @if ($payroll->allowances->count())
                        <div class="prl-sub-label mt-2 mb-1">Allowances</div>
                        @foreach ($payroll->allowances as $allowance)
                            <div class="prl-line">
                                <span class="prl-line-key">{{ $allowance->allowance_type }}</span>
                                <span class="prl-line-val">₱{{ number_format($allowance->amount, 2) }}</span>
                            </div>
                        @endforeach
                    @endif
                    <div class="prl-line prl-line-total mt-2 pt-2 border-top">
                        <span class="prl-line-key fw-bold">Gross Pay</span>
                        <span class="prl-line-val fw-bold">₱{{ number_format($payroll->gross_pay, 2) }}</span>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="prl-section-label mb-3">Deductions</div>
                    @if ($payroll->deductions->count())
                        @foreach ($payroll->deductions as $deduction)
                            <div class="prl-line">
                                <span class="prl-line-key">{{ $deduction->deduction_type }}</span>
                                <span class="prl-line-val">₱{{ number_format($deduction->amount, 2) }}</span>
                            </div>
                        @endforeach
                    @else
                        <div class="text-muted" style="font-size:.845rem;">No deductions</div>
                    @endif
                    <div class="prl-line mt-2 pt-2 border-top">
                        <span class="prl-line-key fw-bold" style="color:#e11d48;">Total Deductions</span>
                        <span class="prl-line-val fw-bold" style="color:#e11d48;">₱{{ number_format($payroll->total_deductions, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Net Pay --}}
            <div class="prl-net-box mb-4">
                <span class="prl-net-label">Net Pay</span>
                <span class="prl-net-value">₱{{ number_format($payroll->net_pay, 2) }}</span>
            </div>

            {{-- Actions --}}
            <div class="d-flex gap-2 pt-3 border-top">
                <a href="{{ route('payroll.edit', $payroll) }}" class="emp-action-btn emp-action-edit">
                    <i class="feather-edit-2 me-1"></i> Edit
                </a>
                <form action="{{ route('payroll.destroy', $payroll) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="emp-action-btn emp-action-danger"
                        onclick="return confirm('Delete this payroll?')">
                        <i class="feather-trash-2 me-1"></i> Delete
                    </button>
                </form>
                <a href="{{ route('payroll.generatePayslip', $payroll) }}" class="emp-action-btn emp-action-view">
                    <i class="feather-file-text me-1"></i> Payslip
                </a>
            </div>

        </div>
    </div>
</div>

<style>
.prl-field-label  { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #9898a8; display: block; margin-bottom: 3px; }
.prl-field-value  { font-size: 0.9rem; color: #1c1c1e; }
.prl-field-sub    { font-size: 0.78rem; color: #9898a8; }
.prl-section-label { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1.2px; color: #9898a8; }
.prl-sub-label    { font-size: 0.72rem; font-weight: 600; color: #9898a8; text-transform: uppercase; letter-spacing: 0.8px; }
.prl-line         { display: flex; justify-content: space-between; align-items: center; padding: 4px 0; font-size: 0.845rem; color: #4a4a58; }
.prl-line-key     {}
.prl-line-val     { font-variant-numeric: tabular-nums; }
.prl-net-box {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #1c1c1e;
    border-radius: 10px;
    padding: 16px 20px;
}
.prl-net-label { font-size: 0.8rem; font-weight: 600; color: rgba(255,255,255,0.5); text-transform: uppercase; letter-spacing: 1px; }
.prl-net-value { font-size: 1.4rem; font-weight: 800; color: #fff; font-variant-numeric: tabular-nums; }

/* Action buttons */
.emp-action-btn {
    display: inline-flex;
    align-items: center;
    height: 30px;
    padding: 0 12px;
    border-radius: 6px;
    background: #f4f5f7;
    border: none;
    color: #9898a8;
    font-size: 0.815rem;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    transition: background 0.13s, color 0.13s;
    white-space: nowrap;
}
.emp-action-btn.emp-action-edit:hover   { background: #fffbeb; color: #d97706; }
.emp-action-btn.emp-action-danger:hover { background: #fff1f2; color: #e11d48; }
.emp-action-btn.emp-action-back:hover   { background: #f0f9ff; color: #3b82f6; }
.emp-action-btn.emp-action-view:hover   { background: #eff6ff; color: #3b82f6; }
</style>
@endsection