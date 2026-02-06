@extends('layouts.layout')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Payroll Review</h5>
                    <a href="{{ route('payroll-approval.index') }}" class="btn btn-secondary btn-sm">Back</a>
                </div>
                <div class="card-body">

                    {{-- Employee Info --}}
                    <h6 class="mb-3">Employee Information</h6>
                    @if ($payroll->employee)
                        <p><strong>Name:</strong> {{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}</p>
                        <p><strong>Department:</strong> {{ $payroll->employee->department ?? 'N/A' }}</p>
                        <p><strong>Position:</strong> {{ $payroll->employee->position ?? 'N/A' }}</p>
                    @else
                        <p class="text-danger">Employee record not found.</p>
                    @endif

                    <hr>

                    {{-- Payroll Period --}}
                    <h6 class="mb-3">Payroll Period</h6>
                    <p>
                        {{ $payroll->payroll_period_start?->format('M d, Y') ?? 'N/A' }}
                        -
                        {{ $payroll->payroll_period_end?->format('M d, Y') ?? 'N/A' }}
                    </p>

                    <hr>

                    {{-- Payroll Breakdown --}}
                    <h6 class="mb-3">Payroll Breakdown</h6>
                    <table class="table table-bordered">
                        <tbody>
                            @php
                                $basicSalary = $payroll->employee?->salary_rate * 15 ?? 0;
                            @endphp
                            <tr>
                                <th>Basic Salary</th>
                                <td>₱{{ number_format($basicSalary, 2) }}</td>
                            </tr>

                            {{-- Allowances --}}
                            <tr>
                                <th>Total Allowances</th>
                                <td>
                                    ₱{{ number_format($payroll->total_allowances ?? $payroll->allowances->sum('amount') ?? 0, 2) }}
                                    @if ($payroll->allowances && $payroll->allowances->isNotEmpty())
                                        <ul class="mb-0">
                                            @foreach ($payroll->allowances as $allowance)
                                                <li>{{ $allowance->name }}: ₱{{ number_format($allowance->amount, 2) }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </td>
                            </tr>

                            {{-- Deductions --}}
                            <tr>
                                <th>Total Deductions</th>
                                <td>
                                    ₱{{ number_format($payroll->total_deductions ?? $payroll->deductions->sum('amount') ?? 0, 2) }}
                                    @if ($payroll->deductions && $payroll->deductions->isNotEmpty())
                                        <ul class="mb-0">
                                            @foreach ($payroll->deductions as $deduction)
                                                <li>{{ $deduction->name }}: ₱{{ number_format($deduction->amount, 2) }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </td>
                            </tr>

                            <tr>
                                <th>Gross Pay</th>
                                <td>₱{{ number_format($payroll->gross_pay ?? ($basicSalary + ($payroll->allowances->sum('amount') ?? 0)), 2) }}</td>
                            </tr>
                            <tr>
                                <th>Net Pay</th>
                                <td>₱{{ number_format($payroll->net_pay ?? ($basicSalary + ($payroll->allowances->sum('amount') ?? 0) - ($payroll->deductions->sum('amount') ?? 0)), 2) }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
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
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    {{-- Approve / Reject --}}
                    @if ($payroll->status === 'pending')
                        <div class="d-flex gap-2">
                            <form action="{{ route('payroll-approval.approve', $payroll) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success" onclick="return confirm('Approve this payroll?')">Approve</button>
                            </form>

                            <form action="{{ route('payroll-approval.reject', $payroll) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Reject this payroll?')">Reject</button>
                            </form>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
