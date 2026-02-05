@extends('layouts.layout')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Payroll Details</h5>
                        <a href="{{ route('payroll.index') }}" class="btn btn-outline-secondary btn-sm">Back to Payrolls</a>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Employee:</strong>
                            <span>{{ $payroll->employee ? $payroll->employee->first_name . ' ' . $payroll->employee->last_name : 'N/A' }}</span>
                        </div>

                        <div class="mb-3">
                            <strong>Payroll Period:</strong>
                            <span>{{ $payroll->payroll_period_start->format('M d, Y') }} -
                                {{ $payroll->payroll_period_end->format('M d, Y') }}</span>
                        </div>

                        <div class="mb-3">
                            <strong>Basic Salary:</strong>
                            <span>₱{{ number_format($payroll->basic_salary, 2) }}</span>
                        </div>

                        <div class="mb-3">
                            <strong>Total Allowances:</strong>
                            <span>₱{{ number_format($payroll->total_allowances, 2) }}</span>
                        </div>

                        <div class="mb-3">
                            <strong>Total Deductions:</strong>
                            <span>₱{{ number_format($payroll->total_deductions, 2) }}</span>
                        </div>

                        <div class="mb-3">
                            <strong>Gross Pay:</strong>
                            <span>₱{{ number_format($payroll->gross_pay, 2) }}</span>
                        </div>

                        <div class="mb-3">
                            <strong>Net Pay:</strong>
                            <span>₱{{ number_format($payroll->net_pay, 2) }}</span>
                        </div>

                        <div class="mb-3">
                            <strong>Status:</strong>
                            @php
                                $statusColors = [
                                    'draft' => 'secondary',
                                    'submitted' => 'info',
                                    'approved' => 'primary',
                                    'paid' => 'success',
                                ];
                            @endphp
                            <span class="badge bg-{{ $statusColors[$payroll->status] ?? 'secondary' }}">
                                {{ ucfirst($payroll->status) }}
                            </span>
                        </div>

                        <div class="mb-3">
                            <strong>Approved By:</strong>
                            <span>{{ $payroll->approvedBy ? $payroll->approvedBy->first_name . ' ' . $payroll->approvedBy->last_name : 'N/A' }}</span>
                        </div>

                        @if ($payroll->deductions->count() > 0)
                            <div class="mb-3">
                                <strong>Deductions:</strong>
                                <ul>
                                    @foreach ($payroll->deductions as $deduction)
                                        <li>{{ $deduction->description }}: ₱{{ number_format($deduction->amount, 2) }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="d-flex gap-2">
                            <a href="{{ route('payroll.edit', $payroll) }}" class="btn btn-warning">Edit</a>
                            <a href="{{ route('payroll.index') }}" class="btn btn-outline-secondary">Back</a>
                            <form action="{{ route('payroll.destroy', $payroll) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger"
                                    onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                            <a href="{{ route('payroll.generatePayslip', $payroll) }}" class="btn btn-primary">Generate
                                Payslip</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
