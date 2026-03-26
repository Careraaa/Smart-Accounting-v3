@extends('layouts.layout')

@section('content')
    <div class="payroll-show-wrapper">

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="card-title mb-0">Payroll Details</span>
                <a href="{{ route('payroll.salary-computation.index') }}" class="emp-action-btn emp-action-back">
                    <i class="feather-arrow-left"></i> Back
                </a>
            </div>

            <div class="card-body">

                {{-- Employee & Period --}}
                <div class="row mb-4">
                    <div class="col-md-6">
                        <span class="prl-field-label">Employee</span>
                        <div class="prl-field-value fw-bold">
                            {{ optional($payroll->user)->first_name }}
                            {{ optional($payroll->user)->last_name ?? 'N/A' }}
                        </div>
                        <div class="prl-field-sub">{{ optional($payroll->user)->position ?? '' }}</div>

                    </div>
                    <div class="col-md-6">
                        <span class="prl-field-label">Payroll Period</span>
                        <div class="prl-field-value">
                            {{ $payroll->payroll_period_start->format('M d, Y') }} –
                            {{ $payroll->payroll_period_end->format('M d, Y') }}
                        </div>
                        <div class="mt-1">
                            @php
                                $statusMap = [
                                    'draft' => ['bg' => '#f4f5f7', 'color' => '#9898a8'],
                                    'pending' => ['bg' => '#fffbeb', 'color' => '#d97706'],
                                    'submitted' => ['bg' => '#fffbeb', 'color' => '#d97706'],
                                    'approved' => ['bg' => '#f0f9ff', 'color' => '#0284c7'],
                                    'paid' => ['bg' => '#f0fdf4', 'color' => '#16a34a'],
                                    'rejected' => ['bg' => '#fff1f2', 'color' => '#e11d48'],
                                ];
                                $st = $statusMap[$payroll->status] ?? $statusMap['draft'];
                            @endphp
                            <span
                                style="display:inline-block; background:{{ $st['bg'] }}; color:{{ $st['color'] }}; font-size:0.7rem; font-weight:700; padding:3px 10px; border-radius:20px; letter-spacing:0.4px; text-transform:uppercase;">
                                {{ ucfirst($payroll->status) }}
                            </span>
                        </div>
                        @if ($payroll->approvedBy)
                            <div class="prl-field-sub mt-1">Approved by {{ $payroll->approvedBy->first_name }}
                                {{ $payroll->approvedBy->last_name }}</div>
                        @endif
                    </div>
                </div>

                <hr>

                {{-- Earnings & Deductions --}}
                <div class="row">
                    {{-- EARNINGS --}}
                    <div class="col-md-6 mb-4">
                        <div class="prl-section-label mb-3">Earnings</div>
                        <div class="prl-line">
                            <span class="prl-line-key text-muted">Per Day Rate</span>
                            <span class="prl-line-val text-muted">₱{{ number_format($payroll->per_day_rate, 2) }}</span>
                        </div>
                        <div class="prl-line">
                            <span class="prl-line-key text-muted">Hourly Rate</span>
                            <span class="prl-line-val text-muted">₱{{ number_format($payroll->hourly_rate, 2) }}</span>
                        </div>
                        <div class="prl-line">
                            <span class="prl-line-key">Basic Pay</span>
                            <span class="prl-line-val">₱{{ number_format($payroll->basic_salary, 2) }}</span>
                        </div>

                        {{-- Overtime line items --}}
                        @php
                            $overtimeAllowances = $payroll->allowances->filter(
                                fn($a) => str_starts_with($a->allowance_type, 'Overtime Pay'),
                            );
                            $regularAllowances = $payroll->allowances->reject(
                                fn($a) => str_starts_with($a->allowance_type, 'Overtime Pay'),
                            );
                        @endphp

                        @if ($overtimeAllowances->count())
                            <div class="prl-sub-label mt-2 mb-1" style="color:#16a34a;">Overtime</div>
                            @foreach ($overtimeAllowances as $ot)
                                <div class="prl-line">
                                    <span class="prl-line-key" style="color:#16a34a;">{{ $ot->allowance_type }}</span>
                                    <span class="prl-line-val"
                                        style="color:#16a34a;">+₱{{ number_format($ot->amount, 2) }}</span>
                                </div>
                            @endforeach
                        @endif

                        @if ($regularAllowances->count())
                            <div class="prl-sub-label mt-2 mb-1">Allowances</div>
                            @foreach ($regularAllowances as $allowance)
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

                    {{-- DEDUCTIONS --}}
                    <div class="col-md-6 mb-4">
                        <div class="prl-section-label mb-3">Deductions</div>
                        @php
                            $undertimeDeductions = $payroll->deductions->filter(
                                fn($d) => str_starts_with($d->deduction_type, 'Undertime Deduction'),
                            );
                            $regularDeductions = $payroll->deductions->reject(
                                fn($d) => str_starts_with($d->deduction_type, 'Undertime Deduction'),
                            );
                        @endphp

                        @if ($regularDeductions->count())
                            @foreach ($regularDeductions as $deduction)
                                <div class="prl-line">
                                    <span class="prl-line-key">{{ $deduction->deduction_type }}</span>
                                    <span class="prl-line-val">₱{{ number_format($deduction->amount, 2) }}</span>
                                </div>
                            @endforeach
                        @endif

                        @if ($undertimeDeductions->count())
                            <div class="prl-sub-label mt-2 mb-1" style="color:#e11d48;">Undertime</div>
                            @foreach ($undertimeDeductions as $ut)
                                <div class="prl-line">
                                    <span class="prl-line-key" style="color:#e11d48;">{{ $ut->deduction_type }}</span>
                                    <span class="prl-line-val"
                                        style="color:#e11d48;">−₱{{ number_format($ut->amount, 2) }}</span>
                                </div>
                            @endforeach
                            <div class="prl-hint mt-1" style="color:#e11d48;">
                                <i class="feather-alert-circle" style="font-size:0.7rem;"></i>
                                Affected attendance records have been flagged.
                            </div>
                        @endif

                        @if ($payroll->deductions->isEmpty())
                            <div class="text-muted" style="font-size:.845rem;">No deductions</div>
                        @endif

                        <div class="prl-line mt-2 pt-2 border-top">
                            <span class="prl-line-key fw-bold" style="color:#e11d48;">Total Deductions</span>
                            <span class="prl-line-val fw-bold"
                                style="color:#e11d48;">₱{{ number_format($payroll->total_deductions, 2) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Overtime / Undertime Breakdown Table --}}
                @if ($overtimeUndertimeBreakdown->count())
                    <div class="mb-4">
                        <div class="prl-section-label mb-2">Overtime & Undertime Records</div>
                        <table class="table table-sm table-bordered" style="font-size:0.82rem;">
                            <thead style="background:#f4f5f7;">
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Hours</th>
                                    <th>Reason</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody <tbody>
                                @foreach ($overtimeUndertimeBreakdown as $record)
                                    @php
                                        $hourlyRate = $payroll->hourly_rate;
                                        $amount = round($hourlyRate * $record->hours, 2);
                                        $isOT = $record->type === 'overtime';
                                    @endphp
                                    <tr>
                                        <td>{{ $record->date->format('M d, Y') }}</td>
                                        <td>
                                            <span
                                                style="font-size:0.7rem; font-weight:700; padding:2px 8px; border-radius:20px;
                                            background:{{ $isOT ? '#f0fdf4' : '#fff1f2' }};
                                            color:{{ $isOT ? '#16a34a' : '#e11d48' }};">
                                                {{ ucfirst($record->type) }}
                                            </span>
                                        </td>
                                        <td>{{ number_format($record->hours, 2) }} hrs</td>
                                        <td class="text-muted">{{ $record->reason ?? '—' }}</td>
                                        <td style="color:{{ $isOT ? '#16a34a' : '#e11d48' }}; font-weight:600;">
                                            {{ $isOT ? '+' : '−' }}₱{{ number_format($amount, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                {{-- Net Pay --}}
                <div class="prl-net-box mb-4">
                    <span class="prl-net-label">Net Pay</span>
                    <span class="prl-net-value">₱{{ number_format($payroll->net_pay, 2) }}</span>
                </div>

                {{-- Actions --}}
                <div class="d-flex gap-2 pt-3 border-top">
                    <a href="{{ route('payroll.salary-computation.edit', $payroll) }}"
                        class="emp-action-btn emp-action-edit">
                        <i class="feather-edit-2 me-1"></i> Edit
                    </a>
                    <form action="{{ route('payroll.salary-computation.destroy', $payroll) }}" method="POST"
                        class="d-inline">
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
