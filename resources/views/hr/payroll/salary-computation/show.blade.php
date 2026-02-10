@extends('layouts.layout')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Payroll Details</h5>
                        <a href="{{ route('payroll.index') }}" class="btn btn-outline-secondary btn-sm border-1 rounded">Back
                            to Payrolls</a>
                    </div>
                    <div class="card-body">

                        {{-- EMPLOYEE & PAYROLL INFO --}}
                        <div class="mb-4">
                            <h6 class="fw-bold">Employee Information</h6>
                            <div>{{ $payroll->employee->first_name ?? '' }} {{ $payroll->employee->last_name ?? '' }}</div>
                            <div class="text-muted">
                                {{ $payroll->employee->position ?? 'N/A' }}
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold">Payroll Period</h6>
                            <div>
                                {{ $payroll->payroll_period_start->format('M d, Y') }} –
                                {{ $payroll->payroll_period_end->format('M d, Y') }}
                            </div>
                            <div class="mt-1">
                                Status:
                                <span
                                    class="badge
                @if ($payroll->status === 'pending') bg-warning
                @elseif($payroll->status === 'approved') bg-info
                @elseif($payroll->status === 'rejected') bg-danger
                @else bg-secondary @endif">
                                    {{ ucfirst($payroll->status) }}
                                </span>
                            </div>

                            <div class="text-muted mt-1">
                                Approved By:
                                {{ $payroll->approvedBy ? $payroll->approvedBy->first_name . ' ' . $payroll->approvedBy->last_name : 'N/A' }}
                            </div>
                        </div>

                        {{-- BREAKDOWN --}}
                        <div class="row">
                            {{-- EARNINGS --}}
                            <div class="col-md-6">
                                <h6 class="fw-bold mb-2">Earnings</h6>

                                <div class="d-flex justify-content-between text-muted">
                                    <span>Per Day Rate</span>
                                    <span>₱{{ number_format($payroll->per_day_rate, 2) }}</span>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <span>Basic Pay</span>
                                    <span>₱{{ number_format($payroll->basic_salary, 2) }}</span>
                                </div>

                                @if ($payroll->allowances->count())
                                    <div class="mt-2">
                                        <small class="text-muted">Allowances</small>
                                        @foreach ($payroll->allowances as $allowance)
                                            <div class="d-flex justify-content-between">
                                                <span>{{ $allowance->allowance_type }}</span>
                                                <span>₱{{ number_format($allowance->amount, 2) }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <hr>

                                <div class="d-flex justify-content-between fw-bold">
                                    <span>Gross Pay</span>
                                    <span>₱{{ number_format($payroll->gross_pay, 2) }}</span>
                                </div>
                            </div>

                            {{-- DEDUCTIONS --}}
                            <div class="col-md-6">
                                <h6 class="fw-bold mb-2">Deductions</h6>

                                @if ($payroll->deductions->count())
                                    @foreach ($payroll->deductions as $deduction)
                                        <div class="d-flex justify-content-between">
                                            <span>{{ $deduction->deduction_type }}</span>
                                            <span>₱{{ number_format($deduction->amount, 2) }}</span>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-muted">No deductions</div>
                                @endif

                                <hr>

                                <div class="d-flex justify-content-between fw-bold text-danger">
                                    <span>Total Deductions</span>
                                    <span>₱{{ number_format($payroll->total_deductions, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- FINAL --}}
                        <hr>

                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Net Pay</strong>
                                <div class="fs-5 fw-bold">
                                    ₱{{ number_format($payroll->net_pay, 2) }}
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <a href="{{ route('payroll.edit', $payroll) }}" class="btn btn-outline-warning btn-sm">
                                    Edit
                                </a>

                                <form action="{{ route('payroll.destroy', $payroll) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm"
                                        onclick="return confirm('Delete this payroll?')">
                                        Delete
                                    </button>
                                </form>

                                <a href="{{ route('payroll.generatePayslip', $payroll) }}"
                                    class="btn btn-outline-primary btn-sm">
                                    Payslip
                                </a>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        @endsection
