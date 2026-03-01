@extends('layouts.layout')

@section('content')
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-body">
                <h4 class="card-title mb-3">Welcome to Superadmin Dashboard</h4>
                <p class="text-muted">You have full access to all modules in the Smart Accounting System.</p>
            </div>
        </div>

        <!-- HR Management Section -->
        <div class="row mb-4">
            <div class="col-md-12">
                <h5 class="mb-3">HR Management</h5>
            </div>
            <!-- Employee Management -->
            <div class="col-xxl-3 col-lg-4 col-md-6">
                <div class="card stretch stretch-full">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="mb-0">Employee Management</h6>
                            <i class="feather-user-plus fs-20 text-primary"></i>
                        </div>
                        <p class="text-muted fs-12 mb-3">Manage employee records and information</p>
                        <div class="btn-group btn-group-sm w-100" role="group">
                            <a href="{{ route('employees.index') }}" class="btn btn-primary">View</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Attendance -->
            <div class="col-xxl-3 col-lg-4 col-md-6">
                <div class="card stretch stretch-full">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="mb-0">Attendance & Leave</h6>
                            <i class="feather-clock fs-20 text-info"></i>
                        </div>
                        <p class="text-muted fs-12 mb-3">Track attendance, leaves, and overtime</p>
                        <div class="btn-group btn-group-sm w-100" role="group">
                            <a href="{{ route('attendance.index') }}" class="btn btn-info">View</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payroll Section -->
        <div class="row mb-4">
            <div class="col-md-12">
                <h5 class="mb-3">Payroll Processing</h5>
            </div>
            <!-- Salary Computation -->
            <div class="col-xxl-3 col-lg-4 col-md-6">
                <div class="card stretch stretch-full">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="mb-0">Salary Computation</h6>
                            <i class="feather-dollar-sign fs-20 text-success"></i>
                        </div>
                        <p class="text-muted fs-12 mb-3">Compute employee salaries and payroll</p>
                        <div class="btn-group btn-group-sm w-100" role="group">
                            <a href="{{ route('payroll.salary-computation.index') }}" class="btn btn-success">View</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Statutory Deductions -->
            <div class="col-xxl-3 col-lg-4 col-md-6">
                <div class="card stretch stretch-full">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="mb-0">Statutory Deductions</h6>
                            <i class="feather-minus-circle fs-20 text-warning"></i>
                        </div>
                        <p class="text-muted fs-12 mb-3">Manage tax and statutory deductions</p>
                        <div class="btn-group btn-group-sm w-100" role="group">
                            <a href="{{ route('payroll.statutory-deductions.index') }}" class="btn btn-warning">View</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Payroll Receivables -->
            <div class="col-xxl-3 col-lg-4 col-md-6">
                <div class="card stretch stretch-full">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="mb-0">Payroll Receivables</h6>
                            <i class="feather-credit-card fs-20 text-danger"></i>
                        </div>
                        <p class="text-muted fs-12 mb-3">Track salary loans and advances</p>
                        <div class="btn-group btn-group-sm w-100" role="group">
                            <a href="{{ route('payroll.receivables.index') }}" class="btn btn-danger">View</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Generate Payslip -->
            <div class="col-xxl-3 col-lg-4 col-md-6">
                <div class="card stretch stretch-full">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="mb-0">Generate Payslips</h6>
                            <i class="feather-file-text fs-20 text-secondary"></i>
                        </div>
                        <p class="text-muted fs-12 mb-3">Create and print employee payslips</p>
                        <div class="btn-group btn-group-sm w-100" role="group">
                            <a href="{{ route('payroll.generate-payslip.index') }}" class="btn btn-secondary">View</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Operational Records Section -->
        <div class="row mb-4">
            <div class="col-md-12">
                <h5 class="mb-3">Operational Records</h5>
            </div>
            <!-- Drivers -->
            <div class="col-xxl-3 col-lg-4 col-md-6">
                <div class="card stretch stretch-full">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="mb-0">Drivers</h6>
                            <i class="feather-users fs-20 text-primary"></i>
                        </div>
                        <p class="text-muted fs-12 mb-3">Manage driver records and information</p>
                        <div class="btn-group btn-group-sm w-100" role="group">
                            <a href="{{ route('drivers.index') }}" class="btn btn-primary">View</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- PAO / Conductors -->
            <div class="col-xxl-3 col-lg-4 col-md-6">
                <div class="card stretch stretch-full">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="mb-0">PAO / Conductors</h6>
                            <i class="feather-user-check fs-20 text-info"></i>
                        </div>
                        <p class="text-muted fs-12 mb-3">Manage PAO and conductor records</p>
                        <div class="btn-group btn-group-sm w-100" role="group">
                            <a href="{{ route('paos.index') }}" class="btn btn-info">View</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Routes & Vehicles -->
            <div class="col-xxl-3 col-lg-4 col-md-6">
                <div class="card stretch stretch-full">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="mb-0">Routes & Vehicles</h6>
                            <i class="feather-truck fs-20 text-success"></i>
                        </div>
                        <p class="text-muted fs-12 mb-3">Manage routes and vehicle information</p>
                        <div class="btn-group btn-group-sm w-100" role="group">
                            <a href="{{ route('routes.index') }}" class="btn btn-success">View</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Daily Remittances -->
            <div class="col-xxl-3 col-lg-4 col-md-6">
                <div class="card stretch stretch-full">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="mb-0">Daily Remittances</h6>
                            <i class="feather-activity fs-20 text-warning"></i>
                        </div>
                        <p class="text-muted fs-12 mb-3">Record and track daily remittances</p>
                        <div class="btn-group btn-group-sm w-100" role="group">
                            <a href="{{ route('remittances.index') }}" class="btn btn-warning">View</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approvals Section -->
        <div class="row mb-4">
            <div class="col-md-12">
                <h5 class="mb-3">Approvals</h5>
            </div>
            <!-- Payroll Release Approval -->
            <div class="col-xxl-3 col-lg-4 col-md-6">
                <div class="card stretch stretch-full">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="mb-0">Payroll Release</h6>
                            <i class="feather-check-circle fs-20 text-success"></i>
                        </div>
                        <p class="text-muted fs-12 mb-3">Approve payroll release requests</p>
                        <div class="btn-group btn-group-sm w-100" role="group">
                            <a href="{{ route('payroll-approval.index') }}" class="btn btn-success">View</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Remittance Approval -->
            <div class="col-xxl-3 col-lg-4 col-md-6">
                <div class="card stretch stretch-full">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="mb-0">Remittance Approval</h6>
                            <i class="feather-check-square fs-20 text-info"></i>
                        </div>
                        <p class="text-muted fs-12 mb-3">Approve remittance transactions</p>
                        <div class="btn-group btn-group-sm w-100" role="group">
                            <a href="{{ route('remittance-approval.index') }}" class="btn btn-info">View</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reports Section -->
        <div class="row">
            <div class="col-md-12">
                <h5 class="mb-3">Reports</h5>
            </div>
            <!-- Remittance Reports -->
            <div class="col-xxl-3 col-lg-4 col-md-6">
                <div class="card stretch stretch-full">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="mb-0">Remittance Reports</h6>
                            <i class="feather-file-text fs-20 text-primary"></i>
                        </div>
                        <p class="text-muted fs-12 mb-3">View remittance details and summaries</p>
                        <div class="btn-group btn-group-sm w-100" role="group">
                            <a href="{{ route('reports.remittance-details') }}" class="btn btn-primary">View</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Payroll Reports -->
            <div class="col-xxl-3 col-lg-4 col-md-6">
                <div class="card stretch stretch-full">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="mb-0">Payroll Reports</h6>
                            <i class="feather-bar-chart-2 fs-20 text-success"></i>
                        </div>
                        <p class="text-muted fs-12 mb-3">Access payroll and payslip reports</p>
                        <div class="btn-group btn-group-sm w-100" role="group">
                            <a href="{{ route('reports.payslips') }}" class="btn btn-success">View</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Deduction & Tax Reports -->
            <div class="col-xxl-3 col-lg-4 col-md-6">
                <div class="card stretch stretch-full">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="mb-0">Deduction Reports</h6>
                            <i class="feather-minus-square fs-20 text-warning"></i>
                        </div>
                        <p class="text-muted fs-12 mb-3">Review deductions and government contributions</p>
                        <div class="btn-group btn-group-sm w-100" role="group">
                            <a href="{{ route('reports.deduction-summary') }}" class="btn btn-warning">View</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
