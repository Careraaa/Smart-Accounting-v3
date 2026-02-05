<nav class="nxl-navigation">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ route('dashboard') }}" class="b-brand">
                <img src="{{ asset('images/knights_logo.png') }}" class="logo-full" alt="Logo">
                <img src="{{ asset('images/knights_logo_icon.png') }}" class="logo-mini" alt="Logo">
            </a>
        </div>
        <div class="navbar-content">
            <ul class="nxl-navbar">
                <!-- Remittance Clerk Section -->
                @if (auth()->user()->role === 'remittance_clerk')
                    <li class="nxl-item nxl-caption">
                        <label>Remittance Clerk</label>
                    </li>
                    <li class="nxl-item nxl-hasmenu">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-users"></i></span>
                            <span class="nxl-mtext">Operational Records</span><span class="nxl-arrow"><i
                                    class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('drivers.index') }}">List of
                                    Drivers</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('paos.index') }}">List of PAO /
                                    Conductors</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('routes.index') }}">Manage Routes
                                    &amp; Vehicles</a></li>
                        </ul>
                    </li>
                    <li class="nxl-item nxl-hasmenu">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-activity"></i></span>
                            <span class="nxl-mtext">Daily Remittance</span><span class="nxl-arrow"><i
                                    class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('remittances.index') }}">Record
                                    Remittance</a>
                            </li>
                        </ul>
                    </li>
                    <li class="nxl-item nxl-hasmenu">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-file-text"></i></span>
                            <span class="nxl-mtext">Remittance Report</span><span class="nxl-arrow"><i
                                    class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link"
                                    href="{{ route('reports.remittance-details') }}">Remittance
                                    Details</a></li>
                            <li class="nxl-item"><a class="nxl-link"
                                    href="{{ route('reports.remittance-summary') }}">Remittance
                                    Summary</a></li>
                        </ul>
                    </li>
                @endif

                <!-- HR Section -->
                @if (auth()->user()->role === 'hr')
                    <li class="nxl-item nxl-caption">
                        <label>HR Management</label>
                    </li>
                    <li class="nxl-item nxl-hasmenu">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-user-plus"></i></span>
                            <span class="nxl-mtext">Employee Management</span><span class="nxl-arrow"><i
                                    class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('employees.index') }}">Employee
                                    Information</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="#">Salary & Rate Setup</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="#">Allowances and Benefits Setup</a>
                            </li>
                            <li class="nxl-item"><a class="nxl-link" href="#">Deduction / Contribution Setup</a>
                            </li>
                        </ul>
                    </li>
                    <li class="nxl-item nxl-hasmenu">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-clock"></i></span>
                            <span class="nxl-mtext">Attendance (QR Based)</span><span class="nxl-arrow"><i
                                    class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('attendance.index') }}">QR Time
                                    IN / OUT Records</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('attendance.index') }}">Leave
                                    Management</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('attendance.index') }}">Overtime
                                    / Undertime Logging</a></li>
                            <li class="nxl-item"><a class="nxl-link"
                                    href="{{ route('attendance.index') }}">Attendance Adjustment</a></li>
                        </ul>
                    </li>
                    <li class="nxl-item nxl-hasmenu">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-dollar-sign"></i></span>
                            <span class="nxl-mtext">Payroll Processing</span><span class="nxl-arrow"><i
                                    class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item">
                                <a class="nxl-link {{ request()->routeIs('payroll.salary-computation.*') ? 'active' : '' }}"
                                    href="{{ route('payroll.salary-computation.index') }}">Salary Computation</a>
                            </li>
                            <li class="nxl-item">
                                <a class="nxl-link {{ request()->routeIs('payroll.statutory-deductions.*') ? 'active' : '' }}"
                                    href="{{ route('payroll.statutory-deductions.index') }}">Statutory Deductions</a>
                            </li>
                            <li class="nxl-item">
                                <a class="nxl-link {{ request()->routeIs('payroll.receivables.index') ? 'active' : '' }}"
                                    href="{{ route('payroll.receivables.index') }}">
                                    Payroll Receivables
                                </a>
                            </li>
                            <li class="nxl-item">
                                <a class="nxl-link {{ request()->routeIs('payroll.generate-payslip.index') ? 'active' : '' }}"
                                    href="{{ route('payroll.generate-payslip.index') }}">
                                    Generate Payslip
                                </a>
                            </li>
                        </ul>

                    </li>
                    <li class="nxl-item nxl-hasmenu">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-bar-chart-2"></i></span>
                            <span class="nxl-mtext">Payroll Report</span><span class="nxl-arrow"><i
                                    class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link"
                                    href="{{ route('reports.payslips') }}">Payslips</a></li>
                            <li class="nxl-item"><a class="nxl-link"
                                    href="{{ route('reports.payroll-summary') }}">Payroll Summary Report</a></li>
                            <li class="nxl-item"><a class="nxl-link"
                                    href="{{ route('reports.deduction-summary') }}">Deduction Summary</a></li>
                            <li class="nxl-item"><a class="nxl-link"
                                    href="{{ route('reports.government-contribution') }}">Government Contribution
                                    Summary</a>
                            </li>
                        </ul>
                    </li>
                @endif

                <!-- Accountant Section -->
                @if (auth()->user()->role === 'accountant')
                    <li class="nxl-item nxl-caption">
                        <label>Accounting</label>
                    </li>
                    <li class="nxl-item nxl-hasmenu">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-check-circle"></i></span>
                            <span class="nxl-mtext">Payroll Approvals</span><span class="nxl-arrow"><i
                                    class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link"
                                    href="{{ route('payroll-approval.index') }}">Payroll Release Approval</a></li>
                        </ul>
                    </li>
                    <li class="nxl-item nxl-hasmenu">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-file-text"></i></span>
                            <span class="nxl-mtext">Reports</span><span class="nxl-arrow"><i
                                    class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link"
                                    href="{{ route('reports.remittance') }}">Remittance Reports</a></li>
                        </ul>
                    </li>
                @endif
            </ul>
            <div class="card text-center">
                <div class="card-body">
                    <i class="feather-settings fs-4 text-dark"></i>
                    <h6 class="mt-4 text-dark fw-bolder">System Settings</h6>
                    <p class="fs-11 my-3 text-dark">Smart Accounting System - Manage your payroll, HR, and remittance
                        operations efficiently.</p>
                    <a href="#" class="btn btn-primary text-dark w-100">Settings</a>
                </div>
            </div>
        </div>
    </div>
</nav>
