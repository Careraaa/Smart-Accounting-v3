@extends('layouts.layout')

@section('content')
<div class="col-md-12">

    {{-- Top stat cards --}}
    <div class="row g-3 mb-4">

        <div class="col-xxl-2 col-lg-4 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="dash-label">Total Employees</span>
                        <span class="dash-icon"><i class="feather-users"></i></span>
                    </div>
                    <div class="dash-value">{{ $totalEmployees }}</div>
                </div>
            </div>
        </div>

        <div class="col-xxl-2 col-lg-4 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="dash-label">Total Payroll</span>
                        <span class="dash-icon di-green"><i class="feather-arrow-up-circle"></i></span>
                    </div>
                    <div class="dash-value">₱{{ number_format($totalPayroll, 0) }}</div>
                    <div class="dash-sub">+5.2% this period</div>
                </div>
            </div>
        </div>

        <div class="col-xxl-2 col-lg-4 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="dash-label">Total Allowances</span>
                        <span class="dash-icon di-green"><i class="feather-plus-circle"></i></span>
                    </div>
                    <div class="dash-value">₱{{ number_format($totalAllowances, 0) }}</div>
                </div>
            </div>
        </div>

        <div class="col-xxl-2 col-lg-4 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="dash-label">Total Deductions</span>
                        <span class="dash-icon di-red"><i class="feather-minus-circle"></i></span>
                    </div>
                    <div class="dash-value">₱{{ number_format($totalDeductions, 0) }}</div>
                </div>
            </div>
        </div>

        <div class="col-xxl-2 col-lg-4 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="dash-label">Avg. Basic Salary</span>
                        <span class="dash-icon di-blue"><i class="feather-trending-up"></i></span>
                    </div>
                    <div class="dash-value">₱{{ number_format($averageBasicSalary, 0) }}</div>
                </div>
            </div>
        </div>

        <div class="col-xxl-2 col-lg-4 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="dash-label">Outstanding Loans</span>
                        <span class="dash-icon di-amber"><i class="feather-alert-circle"></i></span>
                    </div>
                    <div class="dash-value">₱{{ number_format($totalOutstandingLoans, 0) }}</div>
                </div>
            </div>
        </div>

    </div>

    {{-- Status cards --}}
    <div class="row g-3 mb-4">

        <div class="col-xxl-3 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <span class="dash-label">Processing Payroll</span>
                    <div class="dash-value mt-3">{{ $processingPayroll }}</div>
                    <div class="progress dash-progress mt-3">
                        <div class="progress-bar bg-warning" style="width: {{ $totalEmployees > 0 ? ($processingPayroll/$totalEmployees)*100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <span class="dash-label">Approved Payroll</span>
                    <div class="dash-value mt-3">{{ $approvedPayroll }}</div>
                    <div class="progress dash-progress mt-3">
                        <div class="progress-bar bg-info" style="width: {{ $totalEmployees > 0 ? ($approvedPayroll/$totalEmployees)*100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <span class="dash-label">Paid Payroll</span>
                    <div class="dash-value mt-3">{{ $paidPayroll }}</div>
                    <div class="progress dash-progress mt-3">
                        <div class="progress-bar bg-success" style="width: {{ $totalEmployees > 0 ? ($paidPayroll/$totalEmployees)*100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <span class="dash-label">Active Salary Loans</span>
                    <div class="dash-value mt-3">{{ $activeSalaryLoans }}</div>
                    <div class="dash-sub mt-1">
                        <a href="#" style="font-size:.8rem; color:#c8292a; font-weight:600; text-decoration:none;">View Details →</a>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Allowances vs Deductions + Monthly Trend --}}
    <div class="row g-3 mb-4">

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <span class="card-title mb-0">Allowances vs Deductions</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="dash-label" style="text-transform:none; letter-spacing:0;">Allowances</span>
                            <span style="font-size:.82rem; font-weight:700; color:#16a34a;">{{ number_format($allowancePercentage, 1) }}%</span>
                        </div>
                        <div class="progress dash-progress">
                            <div class="progress-bar bg-success" style="width: {{ $allowancePercentage }}%"></div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="dash-label" style="text-transform:none; letter-spacing:0;">Deductions</span>
                            <span style="font-size:.82rem; font-weight:700; color:#e11d48;">{{ number_format($deductionPercentage, 1) }}%</span>
                        </div>
                        <div class="progress dash-progress">
                            <div class="progress-bar bg-danger" style="width: {{ $deductionPercentage }}%"></div>
                        </div>
                    </div>
                    <div class="pt-3 border-top">
                        <div class="row text-center">
                            <div class="col">
                                <div class="dash-label">Total Allowances</div>
                                <div style="font-size:.95rem; font-weight:700; color:#16a34a; margin-top:4px;">₱{{ number_format($totalAllowances, 0) }}</div>
                            </div>
                            <div class="col">
                                <div class="dash-label">Total Deductions</div>
                                <div style="font-size:.95rem; font-weight:700; color:#e11d48; margin-top:4px;">₱{{ number_format($totalDeductions, 0) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <span class="card-title mb-0">Monthly Payroll Trend</span>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <tbody>
                            @foreach($monthlyPayrollTrend as $trend)
                                <tr>
                                    <td class="ps-4" style="font-size:.82rem; font-weight:600; color:#4a4a58; width:100px;">
                                        {{ $trend['month'] }}
                                    </td>
                                    <td>
                                        <div class="progress dash-progress">
                                            <div class="progress-bar" style="background:#c8292a; width: {{ $totalPayroll > 0 ? ($trend['total']/$totalPayroll)*100 : 0 }}%"></div>
                                        </div>
                                    </td>
                                    <td class="text-end pe-4" style="font-size:.82rem; font-weight:700; color:#1c1c1e; width:110px;">
                                        ₱{{ number_format($trend['total'], 0) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    {{-- Recent Payroll --}}
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="card-title mb-0">Recent Payroll Records</span>
            <a href="#" class="btn btn-primary btn-sm">View All</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Basic Salary</th>
                            <th>Allowances</th>
                            <th>Deductions</th>
                            <th>Gross Pay</th>
                            <th>Net Pay</th>
                            <th class="text-center">Status</th>
                            <th class="text-center"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPayroll as $payroll)
                            <tr>
                                <td><strong>{{ $payroll->employee->name ?? 'N/A' }}</strong></td>
                                <td>₱{{ number_format($payroll->basic_salary, 2) }}</td>
                                <td style="color:#16a34a;">₱{{ number_format($payroll->total_allowances, 2) }}</td>
                                <td style="color:#e11d48;">₱{{ number_format($payroll->total_deductions, 2) }}</td>
                                <td>₱{{ number_format($payroll->gross_pay, 2) }}</td>
                                <td><strong>₱{{ number_format($payroll->net_pay, 2) }}</strong></td>
                                <td class="text-center">
                                    @php
                                        $statusMap = [
                                            'paid'     => ['bg' => '#f0fdf4', 'color' => '#16a34a'],
                                            'approved' => ['bg' => '#f0f9ff', 'color' => '#0284c7'],
                                            'pending'  => ['bg' => '#fffbeb', 'color' => '#d97706'],
                                        ];
                                        $st = $statusMap[$payroll->status] ?? ['bg' => '#f4f5f7', 'color' => '#9898a8'];
                                    @endphp
                                    <span style="display:inline-block; background:{{ $st['bg'] }}; color:{{ $st['color'] }}; font-size:0.7rem; font-weight:700; padding:3px 10px; border-radius:20px; letter-spacing:0.4px; text-transform:uppercase;">
                                        {{ ucfirst($payroll->status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="#" class="emp-action-btn emp-action-view" title="View">
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

<style>
.dash-label   { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#9898a8; }
.dash-value   { font-size:1.75rem; font-weight:800; color:#1c1c1e; line-height:1; }
.dash-sub     { font-size:.75rem; color:#9898a8; margin-top:4px; }
.dash-icon    { width:34px; height:34px; border-radius:8px; background:#f4f5f7; display:flex; align-items:center; justify-content:center; color:#9898a8; font-size:15px; flex-shrink:0; }
.dash-icon.di-green { background:#f0fdf4; color:#16a34a; }
.dash-icon.di-red   { background:#fff5f5; color:#c8292a; }
.dash-icon.di-amber { background:#fffbeb; color:#d97706; }
.dash-icon.di-blue  { background:#f0f9ff; color:#0284c7; }
.dash-progress { height:4px; border-radius:4px; background:#f4f5f7; }
.emp-action-btn {
    display:inline-flex; align-items:center; justify-content:center;
    width:30px; height:30px; border-radius:6px;
    background:#f4f5f7; border:none; color:#9898a8;
    font-size:13px; cursor:pointer; text-decoration:none;
    transition:background 0.13s, color 0.13s; padding:0;
}
.emp-action-btn.emp-action-view:hover { background:#eff6ff; color:#3b82f6; }
</style>
@endsection