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

                        <!-- Employee Info -->
                        <div class="mb-3">
                            <strong>Employee:</strong>
                            <span>{{ $payroll->employee ? $payroll->employee->first_name . ' ' . $payroll->employee->last_name : 'N/A' }}</span>
                        </div>

                        <div class="mb-3">
                            <strong>Payroll Period:</strong>
                            <span>{{ $payroll->payroll_period_start->format('M d, Y') }} -
                                {{ $payroll->payroll_period_end->format('M d, Y') }}</span>
                        </div>

                        @php
                            $basicSalary = $payroll->employee ? $payroll->employee->salary_rate * 15 : 0;
                        @endphp

                        <div class="mb-3">
                            <strong>Basic Salary (15 days):</strong>
                            <span>₱{{ number_format($basicSalary, 2) }}</span>
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
                            <span>₱{{ number_format($basicSalary + $payroll->total_allowances, 2) }}</span>
                        </div>

                        <div class="mb-3">
                            <strong>Net Pay:</strong>
                            <span>₱{{ number_format($basicSalary + $payroll->total_allowances - $payroll->total_deductions, 2) }}</span>
                        </div>

                        <!-- Status -->
                        <div class="mb-3">
                            <strong>Status:</strong>
                            @php
                                $statusStyles = [
                                    'pending' => 'bg-soft-warning text-warning',
                                    'approved' => 'bg-soft-info text-info',
                                    'rejected' => 'bg-soft-danger text-danger',
                                ];
                            @endphp
                            <span class="badge px-3 {{ $statusStyles[$payroll->status] ?? 'bg-secondary text-white' }}">
                                {{ ucfirst($payroll->status) }}
                            </span>
                        </div>

                        <div class="mb-3">
                            <strong>Approved By:</strong>
                            <span>{{ $payroll->approvedBy ? $payroll->approvedBy->first_name . ' ' . $payroll->approvedBy->last_name : 'N/A' }}</span>
                        </div>

                        <!-- Allowances List -->
                        @if ($payroll->allowances->count() > 0)
                            <div class="mb-3">
                                <strong>Allowances:</strong>
                                <ul class="list-group list-group-flush">
                                    @foreach ($payroll->allowances as $allowance)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            {{ $allowance->allowance_type }}
                                            <span>₱{{ number_format($allowance->amount, 2) }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <hr class="my-2 border-top border-secondary">
                        @endif

                        <!-- Deductions List -->
                        @if ($payroll->deductions->count() > 0)
                            <div class="mb-3">
                                <strong>Deductions:</strong>
                                <ul class="list-group list-group-flush">
                                    @foreach ($payroll->deductions as $deduction)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            {{ $deduction->deduction_type }}
                                            <span>₱{{ number_format($deduction->amount, 2) }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Actions -->
                        <div class="d-flex gap-2 mt-3 mb-2">
                            <a href="{{ route('payroll.edit', $payroll) }}"
                                class="btn btn-outline-warning btn-sm border-1 rounded">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <a href="{{ route('payroll.index') }}"
                                class="btn btn-outline-secondary btn-sm border-1 rounded">
                                <i class="bi bi-arrow-left"></i> Back
                            </a>
                            <form action="{{ route('payroll.destroy', $payroll) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm border-1 rounded"
                                    onclick="return confirm('Are you sure?')">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </form>
                            <a href="{{ route('payroll.generatePayslip', $payroll) }}"
                                class="btn btn-outline-primary btn-sm border-1 rounded">
                                <i class="bi bi-file-earmark-text"></i> Generate Payslip
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
