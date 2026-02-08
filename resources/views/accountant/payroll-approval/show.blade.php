@extends('layouts.layout')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Payroll Review</h5>
                        <a href="{{ route('payroll-approval.index') }}"
                            class="btn btn-outline-secondary btn-sm border-1 rounded">
                            Back to Payrolls
                        </a>
                    </div>
                    <div class="card-body">

                        <!-- Employee Info -->
                        <div class="mb-3">
                            <strong>Employee:</strong>
                            @if ($payroll->employee)
                                <span>{{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}</span><br>
                                <span><strong>Department:</strong> {{ $payroll->employee->department ?? 'N/A' }}</span><br>
                                <span><strong>Position:</strong> {{ $payroll->employee->position ?? 'N/A' }}</span>
                            @else
                                <span class="text-danger">Employee record not found.</span>
                            @endif
                        </div>

                        <!-- Payroll Period -->
                        <div class="mb-3">
                            <strong>Payroll Period:</strong>
                            <span>
                                {{ $payroll->payroll_period_start?->format('M d, Y') ?? 'N/A' }}
                                -
                                {{ $payroll->payroll_period_end?->format('M d, Y') ?? 'N/A' }}
                            </span>
                        </div>

                        @php
                            $basicSalary = $payroll->employee?->salary_rate * 15 ?? 0;
                        @endphp

                        <!-- Payroll Breakdown -->
                        <div class="mb-3">
                            <strong>Basic Salary (15 days):</strong>
                            <span>₱{{ number_format($basicSalary, 2) }}</span>
                        </div>

                        <div class="mb-3">
                            <strong>Total Allowances:</strong>
                            <span>₱{{ number_format($payroll->total_allowances ?? ($payroll->allowances->sum('amount') ?? 0), 2) }}</span>
                            @if ($payroll->allowances && $payroll->allowances->isNotEmpty())
                                <ul class="list-group list-group-flush mt-1">
                                    @foreach ($payroll->allowances as $allowance)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            {{ $allowance->name }}
                                            <span>₱{{ number_format($allowance->amount, 2) }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                        <div class="mb-3">
                            <strong>Total Deductions:</strong>
                            <span>₱{{ number_format($payroll->total_deductions ?? ($payroll->deductions->sum('amount') ?? 0), 2) }}</span>
                            @if ($payroll->deductions && $payroll->deductions->isNotEmpty())
                                <ul class="list-group list-group-flush mt-1">
                                    @foreach ($payroll->deductions as $deduction)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            {{ $deduction->name }}
                                            <span>₱{{ number_format($deduction->amount, 2) }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                        <div class="mb-3">
                            <strong>Gross Pay:</strong>
                            <span>₱{{ number_format($payroll->gross_pay ?? $basicSalary + ($payroll->allowances->sum('amount') ?? 0), 2) }}</span>
                        </div>

                        <div class="mb-3">
                            <strong>Net Pay:</strong>
                            <span>₱{{ number_format($payroll->net_pay ?? $basicSalary + ($payroll->allowances->sum('amount') ?? 0) - ($payroll->deductions->sum('amount') ?? 0), 2) }}</span>
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

                        <!-- Approve / Reject -->
                        @if ($payroll->status === 'pending')
                            <div class="d-flex gap-2 mt-3 mb-2">
                                <form action="{{ route('payroll-approval.approve', $payroll) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-success btn-sm border-1 rounded"
                                        onclick="return confirm('Approve this payroll?')">
                                        <i class="bi bi-check2"></i> Approve
                                    </button>
                                </form>

                                <form action="{{ route('payroll-approval.reject', $payroll) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger btn-sm border-1 rounded"
                                        onclick="return confirm('Reject this payroll?')">
                                        <i class="bi bi-x"></i> Reject
                                    </button>
                                </form>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
