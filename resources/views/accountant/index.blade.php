@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <!-- Payroll Statistics Row -->
    <div class="row mb-4">
        <div class="col-xxl-2 col-lg-4 col-md-6">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="fs-12 fw-medium text-muted mb-3">Total Employees</div>
                    <div class="hstack justify-content-between lh-base">
                        <h3>{{ $totalEmployees }}</h3>
                        <div class="hstack gap-2 fs-11 text-primary">
                            <i class="feather-users fs-12"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-2 col-lg-4 col-md-6">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="fs-12 fw-medium text-muted mb-3">Total Payroll</div>
                    <div class="hstack justify-content-between lh-base">
                        <h3>₱{{ number_format($totalPayroll, 0) }}</h3>
                        <div class="hstack gap-2 fs-11 text-success">
                            <i class="feather-arrow-up-circle fs-12"></i>
                            <span>+5.2%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-2 col-lg-4 col-md-6">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="fs-12 fw-medium text-muted mb-3">Total Allowances</div>
                    <div class="hstack justify-content-between lh-base">
                        <h3>₱{{ number_format($totalAllowances, 0) }}</h3>
                        <div class="hstack gap-2 fs-11 text-success">
                            <i class="feather-plus-circle fs-12"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-2 col-lg-4 col-md-6">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="fs-12 fw-medium text-muted mb-3">Total Deductions</div>
                    <div class="hstack justify-content-between lh-base">
                        <h3>₱{{ number_format($totalDeductions, 0) }}</h3>
                        <div class="hstack gap-2 fs-11 text-danger">
                            <i class="feather-minus-circle fs-12"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-2 col-lg-4 col-md-6">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="fs-12 fw-medium text-muted mb-3">Avg. Basic Salary</div>
                    <div class="hstack justify-content-between lh-base">
                        <h3>₱{{ number_format($averageBasicSalary, 0) }}</h3>
                        <div class="hstack gap-2 fs-11 text-info">
                            <i class="feather-trending-up fs-12"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-2 col-lg-4 col-md-6">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="fs-12 fw-medium text-muted mb-3">Outstanding Loans</div>
                    <div class="hstack justify-content-between lh-base">
                        <h3>₱{{ number_format($totalOutstandingLoans, 0) }}</h3>
                        <div class="hstack gap-2 fs-11 text-warning">
                            <i class="feather-alert-circle fs-12"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payroll Status & Loan Cards Row -->
<div class="col-md-12">
    <div class="row mb-4">
        <div class="col-xxl-3 col-md-6">
            <div class="card card-body">
                <div class="fs-12 text-muted mb-3">Processing Payroll</div>
                <div class="hstack justify-content-between mt-4">
                    <div class="text-dark fw-bold fs-5">{{ $processingPayroll }}</div>
                    <span class="badge bg-soft-warning text-warning">
                        <i class="feather-clock fs-10"></i>
                    </span>
                </div>
                <div class="progress mt-3 ht-3">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $totalEmployees > 0 ? ($processingPayroll/$totalEmployees)*100 : 0 }}%"></div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-md-6">
            <div class="card card-body">
                <div class="fs-12 text-muted mb-3">Approved Payroll</div>
                <div class="hstack justify-content-between mt-4">
                    <div class="text-dark fw-bold fs-5">{{ $approvedPayroll }}</div>
                    <span class="badge bg-soft-info text-info">
                        <i class="feather-check-circle fs-10"></i>
                    </span>
                </div>
                <div class="progress mt-3 ht-3">
                    <div class="progress-bar bg-info" role="progressbar" style="width: {{ $totalEmployees > 0 ? ($approvedPayroll/$totalEmployees)*100 : 0 }}%"></div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-md-6">
            <div class="card card-body">
                <div class="fs-12 text-muted mb-3">Paid Payroll</div>
                <div class="hstack justify-content-between mt-4">
                    <div class="text-dark fw-bold fs-5">{{ $paidPayroll }}</div>
                    <span class="badge bg-soft-success text-success">
                        <i class="feather-check-circle fs-10"></i>
                    </span>
                </div>
                <div class="progress mt-3 ht-3">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $totalEmployees > 0 ? ($paidPayroll/$totalEmployees)*100 : 0 }}%"></div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-md-6">
            <div class="card card-body">
                <div class="fs-12 text-muted mb-3">Active Salary Loans</div>
                <div class="hstack justify-content-between mt-4">
                    <div class="text-dark fw-bold fs-5">{{ $activeSalaryLoans }}</div>
                    <span class="badge bg-soft-danger text-danger">
                        <i class="feather-alert-triangle fs-10"></i>
                    </span>
                </div>
                <div class="mt-3">
                    <a href="#" class="fs-11 text-primary fw-semibold">View Details →</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Allowance vs Deduction Chart -->
<div class="col-md-12">
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Allowances vs Deductions</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fs-12 fw-medium">Allowances</span>
                            <span class="fs-12 fw-bold text-success">{{ number_format($allowancePercentage, 1) }}%</span>
                        </div>
                        <div class="progress ht-4">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $allowancePercentage }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fs-12 fw-medium">Deductions</span>
                            <span class="fs-12 fw-bold text-danger">{{ number_format($deductionPercentage, 1) }}%</span>
                        </div>
                        <div class="progress ht-4">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $deductionPercentage }}%"></div>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-top">
                        <div class="row text-center">
                            <div class="col">
                                <div class="fs-12 text-muted mb-1">Total Allowances</div>
                                <div class="fw-bold text-success">₱{{ number_format($totalAllowances, 0) }}</div>
                            </div>
                            <div class="col">
                                <div class="fs-12 text-muted mb-1">Total Deductions</div>
                                <div class="fw-bold text-danger">₱{{ number_format($totalDeductions, 0) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Monthly Payroll Trend</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <tbody>
                                @foreach($monthlyPayrollTrend as $trend)
                                    <tr>
                                        <td>{{ $trend['month'] }}</td>
                                        <td>
                                            <div class="progress ht-6">
                                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $totalPayroll > 0 ? ($trend['total']/$totalPayroll)*100 : 0 }}%"></div>
                                            </div>
                                        </td>
                                        <td class="text-end fw-bold">₱{{ number_format($trend['total'], 0) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Payroll Table -->
<div class="col-md-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Recent Payroll Records</h5>
            <a href="#" class="btn btn-primary btn-sm">View All</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Employee</th>
                            <th>Basic Salary</th>
                            <th>Allowances</th>
                            <th>Deductions</th>
                            <th>Gross Pay</th>
                            <th>Net Pay</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPayroll as $payroll)
                            <tr>
                                <td><strong>{{ $payroll->employee->name ?? 'N/A' }}</strong></td>
                                <td>₱{{ number_format($payroll->basic_salary, 2) }}</td>
                                <td class="text-success">₱{{ number_format($payroll->total_allowances, 2) }}</td>
                                <td class="text-danger">₱{{ number_format($payroll->total_deductions, 2) }}</td>
                                <td>₱{{ number_format($payroll->gross_pay, 2) }}</td>
                                <td><strong>₱{{ number_format($payroll->net_pay, 2) }}</strong></td>
                                <td>
                                    <span class="badge bg-{{ $payroll->status === 'paid' ? 'success' : ($payroll->status === 'approved' ? 'info' : 'warning') }}">
                                        {{ ucfirst($payroll->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="#" class="btn btn-info btn-sm" title="View">
                                        <i class="feather-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No payroll records found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
