<nav class="nxl-navigation">
    <div class="navbar-wrapper">

        {{-- ── Logo Header ── --}}
        <div class="m-header">
            <a href="{{ route('dashboard') }}" class="b-brand kt-brand-text">
                <span class="kt-logo-icon">
                    <img src="{{ asset('images/bus.png') }}" alt="Bus Logo" width="24" height="24" class="kt-sidebar-logo-img" style="filter: brightness(0) invert(1); object-fit: contain; display: block;">
                </span>
                <span class="kt-logo-full">Knights Transport</span>
            </a>
        </div>

        {{-- ── Scrollable Nav Body ── --}}
        <div class="navbar-content">
            <ul class="nxl-navbar">
                {{-- ── SUPERADMIN ── --}}
                @if (auth()->user()->role === 'superadmin')
                    <li class="nxl-item nxl-caption"><label>Superadmin Modules</label></li>

                    <li class="nxl-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <a href="{{ route('dashboard') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-airplay"></i></span>
                            <span class="nxl-mtext">Dashboard</span>
                        </a>
                    </li>

                    <li class="nxl-item {{ request()->routeIs('superadmin.accounts.*') ? 'active' : '' }}">
                        <a href="{{ route('superadmin.accounts.index') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-briefcase"></i></span>
                            <span class="nxl-mtext">Accounts</span>
                        </a>
                    </li>

                    <li class="nxl-item {{ request()->routeIs('configuration.*') ? 'active' : '' }}">
                        <a href="{{ route('configuration.index') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-settings"></i></span>
                            <span class="nxl-mtext">Configuration</span>
                        </a>
                    </li>

                    <li class="nxl-item nxl-caption"><label>Menu</label></li>

                    {{-- HR Modules --}}
                    <li class="nxl-item {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                        <a href="{{ route('employees.index') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-user-plus"></i></span>
                            <span class="nxl-mtext">Employee Management</span>
                        </a>
                    </li>
                    <li class="nxl-item nxl-hasmenu {{ request()->routeIs('leave.*', 'leave-type.*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-calendar"></i></span>
                            <span class="nxl-mtext">Leave Management</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('leave-type.index') }}">Leave Types</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('leave.pending') }}">Pending Leaves</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('leave.approved') }}">Approved Leaves</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('leave.rejected') }}">Rejected Leaves</a></li>
                        </ul>
                    </li>
                    <li class="nxl-item nxl-hasmenu {{ request()->routeIs('holiday.*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-gift"></i></span>
                            <span class="nxl-mtext">Holiday Management</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('holiday.index') }}">All Holidays</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('holiday.calendar') }}">Holiday Calendar</a></li>
                        </ul>
                    </li>
                    <li class="nxl-item nxl-hasmenu {{ request()->routeIs('attendance.*', 'overtime.*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-clock"></i></span>
                            <span class="nxl-mtext">Attendance</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('attendance.index') }}">QR Time IN / OUT Records</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('overtime.index') }}">Overtime / Undertime</a></li>
                        </ul>
                    </li>
                    <li class="nxl-item nxl-hasmenu {{ request()->routeIs('payroll.*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-dollar-sign"></i></span>
                            <span class="nxl-mtext">Payroll Processing</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item {{ request()->routeIs('payroll.salary-computation.*') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('payroll.salary-computation.index') }}">Payroll Management</a>
                            </li>
                            <li class="nxl-item {{ request()->routeIs('payroll.statutory-deductions.*') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('payroll.statutory-deductions.index') }}">Statutory Deductions</a>
                            </li>
                            <li class="nxl-item {{ request()->routeIs('payroll.receivables.*') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('payroll.receivables.index') }}">Payroll Receivables</a>
                            </li>
                            <li class="nxl-item {{ request()->routeIs('payroll.generate-payslip.*') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('payroll.generate-payslip.index') }}">Pay Slips</a>
                            </li>
                            <li class="nxl-item {{ request()->routeIs('payroll.history.*') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('payroll.history.index') }}">Payroll Summary</a>
                            </li>
                        </ul>
                    </li>

                    <li class="nxl-item {{ request()->routeIs('payroll.thirteenth-month-pay.*') ? 'active' : '' }}">
                        <a href="{{ route('bonuses.index') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-gift"></i></span>
                            <span class="nxl-mtext">Bonuses</span>
                        </a>
                    </li>

                    {{-- Remittance Modules --}}
                    <li class="nxl-item nxl-hasmenu {{ request()->routeIs('drivers.*', 'paos.*', 'routes.*', 'vehicles.*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-users"></i></span>
                            <span class="nxl-mtext">Operational Records</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item {{ request()->routeIs('drivers.*') ? 'active' : '' }}"><a class="nxl-link" href="{{ route('drivers.index') }}">List of Drivers</a></li>
                            <li class="nxl-item {{ request()->routeIs('paos.*') ? 'active' : '' }}"><a class="nxl-link" href="{{ route('paos.index') }}">List of PAO / Conductors</a></li>
                            <li class="nxl-item {{ request()->routeIs('routes.*') ? 'active' : '' }}"><a class="nxl-link" href="{{ route('routes.index') }}">Manage Routes</a></li>
                            <li class="nxl-item {{ request()->routeIs('vehicles.*') ? 'active' : '' }}"><a class="nxl-link" href="{{ route('vehicles.index') }}">Manage Vehicles</a></li>
                        </ul>
                    </li>
                    <li class="nxl-item nxl-hasmenu {{ request()->routeIs('remittances.*', 'short-remittances.*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-activity"></i></span>
                            <span class="nxl-mtext">Daily Remittance</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('remittances.index') }}">Record Remittance</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('short-remittances.index') }}">Short Remittance</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('reports.index') }}">Remittance Report</a></li>
                        </ul>
                    </li>

                    {{-- Accountant Modules --}}
                    <li class="nxl-item nxl-hasmenu {{ request()->routeIs('payroll-approval.*', 'remittance-approval.*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-check-circle"></i></span>
                            <span class="nxl-mtext">Approvals</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('payroll-approval.index') }}">Payroll Release Approval</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('remittance-approval.index') }}">Remittance Approval</a></li>
                        </ul>
                    </li>
                    <li class="nxl-item nxl-hasmenu {{ request()->routeIs('reports.remittance', 'reports.payroll') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-file-text"></i></span>
                            <span class="nxl-mtext">Accountant Reports</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('reports.remittance') }}">Remittance Reports</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('reports.payroll') }}">Payroll Reports</a></li>
                        </ul>
                    </li>

                    {{-- QR Admin Module --}}
                    <li class="nxl-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('admin.dashboard') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-camera"></i></span>
                            <span class="nxl-mtext">QR Monitor</span>
                        </a>
                    </li>
                @endif

                @if (auth()->user()->role !== 'superadmin')
                    <li class="nxl-item nxl-caption"><label>Menu</label></li>

                    <li class="nxl-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <a href="{{ route('dashboard') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-airplay"></i></span>
                            <span class="nxl-mtext">Dashboard</span>
                        </a>
                    </li>

                    <li class="nxl-item {{ request()->routeIs('attendance.scan') ? 'active' : '' }}">
                        <a href="{{ route('attendance.scan') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-camera"></i></span>
                            <span class="nxl-mtext">Scan QR Attendance</span>
                        </a>
                    </li>
                @endif

                {{-- ── Remittance Clerk ── --}}
                @if (auth()->user()->role === 'remittance_clerk')
                    <li class="nxl-item nxl-caption"><label>Operations</label></li>

                    <li class="nxl-item nxl-hasmenu {{ request()->routeIs('drivers.*', 'paos.*', 'routes.*', 'vehicles.*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-users"></i></span>
                            <span class="nxl-mtext">Operational Records</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item {{ request()->routeIs('drivers.*') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('drivers.index') }}">List of Drivers</a>
                            </li>
                            <li class="nxl-item {{ request()->routeIs('paos.*') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('paos.index') }}">List of PAO / Conductors</a>
                            </li>
                            <li class="nxl-item {{ request()->routeIs('routes.*') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('routes.index') }}">Manage Routes</a>
                            </li>
                            <li class="nxl-item {{ request()->routeIs('vehicles.*') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('vehicles.index') }}">Manage Vehicles</a>
                            </li>
                        </ul>
                    </li>

                    <li class="nxl-item nxl-hasmenu {{ request()->routeIs('remittances.*', 'short-remittances.*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-activity"></i></span>
                            <span class="nxl-mtext">Daily Remittance</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item">
                                <a class="nxl-link" href="{{ route('remittances.index') }}">Record Remittance</a>
                            </li>
                            <li class="nxl-item">
                                <a class="nxl-link" href="{{ route('short-remittances.index') }}">Short Remittance</a>
                            </li>
                        </ul>
                    </li>

                    <li class="nxl-item nxl-hasmenu {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <a href="{{ route('reports.index') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-file-text"></i></span>
                            <span class="nxl-mtext">Remittance Report</span>
                        </a>
                    </li>
                @endif

                {{-- ── HR ── --}}
                @if (auth()->user()->role === 'hr')
                    <li class="nxl-item nxl-caption"><label>HR Management</label></li>

                    <li class="nxl-item {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                        <a href="{{ route('employees.index') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-user-plus"></i></span>
                            <span class="nxl-mtext">Employee Management</span>
                        </a>
                    </li>

                    <li class="nxl-item nxl-hasmenu {{ request()->routeIs('leave.*', 'leave-type.*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-calendar"></i></span>
                            <span class="nxl-mtext">Leave Management</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('leave-type.index') }}">Leave Types</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('leave.pending') }}">Pending Leaves</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('leave.approved') }}">Approved Leaves</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('leave.rejected') }}">Rejected Leaves</a></li>
                        </ul>
                    </li>

                    <li class="nxl-item nxl-hasmenu {{ request()->routeIs('holiday.*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-gift"></i></span>
                            <span class="nxl-mtext">Holiday Management</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('holiday.index') }}">All Holidays</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('holiday.calendar') }}">Holiday Calendar</a></li>
                        </ul>
                    </li>

                    <li class="nxl-item nxl-hasmenu {{ request()->routeIs('attendance.*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-clock"></i></span>
                            <span class="nxl-mtext">Attendance</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('attendance.index') }}">QR Time IN / OUT Records</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('overtime.index') }}">Overtime / Undertime</a></li>
                        </ul>
                    </li>

                    <li class="nxl-item nxl-caption"><label>Payroll</label></li>

                    <li class="nxl-item nxl-hasmenu {{ request()->routeIs('payroll.*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-dollar-sign"></i></span>
                            <span class="nxl-mtext">Payroll Processing</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item {{ request()->routeIs('payroll.salary-computation.*') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('payroll.salary-computation.index') }}">Payroll Management</a>
                            </li>
                            <li class="nxl-item {{ request()->routeIs('payroll.statutory-deductions.*') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('payroll.statutory-deductions.index') }}">Statutory Deductions</a>
                            </li>
                            <li class="nxl-item {{ request()->routeIs('payroll.receivables.*') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('payroll.receivables.index') }}">Employee Receivables</a>
                            </li>
                        </ul>
                    </li>

                    <li class="nxl-item {{ request()->routeIs('payroll.thirteenth-month-pay.*') ? 'active' : '' }}">
                        <a href="{{ route('bonuses.index') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-gift"></i></span>
                            <span class="nxl-mtext">Bonuses</span>
                        </a>
                    </li>

                    <li class="nxl-item nxl-hasmenu {{ request()->routeIs('reports.payslips', 'reports.payroll*', 'reports.deduction*', 'reports.government*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-bar-chart-2"></i></span>
                            <span class="nxl-mtext">Reports</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item {{ request()->routeIs('payroll.generate-payslip.*') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('payroll.generate-payslip.index') }}">Pay Slips</a>
                            </li>
                            <li class="nxl-item {{ request()->routeIs('payroll.history.*') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('payroll.history.index') }}">Payroll Summary</a>
                            </li>
                            <li class="nxl-item {{ request()->routeIs('reports.government-contribution') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('reports.government-contribution') }}">Government Contribution Summary</a>
                            </li>
                        </ul>
                    </li>
                @endif

                {{-- ── Employee ── --}}
                @if (auth()->user()->role === 'employee')
                    <li class="nxl-item nxl-caption"><label>My Finances</label></li>

                    <li class="nxl-item {{ request()->routeIs('employee.cash-advances.*') ? 'active' : '' }}">
                        <a href="{{ route('employee.cash-advances.index') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-credit-card"></i></span>
                            <span class="nxl-mtext">Cash Advances</span>
                        </a>
                    </li>

                    <li class="nxl-item {{ request()->routeIs('employee.salary-loans.*') ? 'active' : '' }}">
                        <a href="{{ route('employee.salary-loans.index') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-briefcase"></i></span>
                            <span class="nxl-mtext">Salary Loans</span>
                        </a>
                    </li>

                    <li class="nxl-item nxl-caption"><label>Time Off</label></li>

                    <li class="nxl-item {{ request()->routeIs('employee.leaves.*') ? 'active' : '' }}">
                        <a href="{{ route('employee.leaves.index') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-calendar"></i></span>
                            <span class="nxl-mtext">My Leaves</span>
                        </a>
                    </li>

                    <li class="nxl-item {{ request()->routeIs('employee.overtime-undertime.*') ? 'active' : '' }}">
                        <a href="{{ route('employee.overtime-undertime.index') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-clock"></i></span>
                            <span class="nxl-mtext">OT / UT Requests</span>
                        </a>
                    </li>
                @endif

                {{-- ── Accountant ── --}}
                @if (auth()->user()->role === 'accountant')
                    <li class="nxl-item nxl-caption"><label>Approvals &amp; Reports</label></li>

                    <li class="nxl-item nxl-hasmenu {{ request()->routeIs('payroll-approval.*', 'remittance-approval.*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-check-circle"></i></span>
                            <span class="nxl-mtext">Approvals</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('payroll-approval.index') }}">Payroll Release Approval</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('remittance-approval.index') }}">Remittance Approval</a></li>
                        </ul>
                    </li>

                    <li class="nxl-item {{ request()->routeIs('payroll.receivables.*') ? 'active' : '' }}">
                        <a href="{{ route('payroll.receivables.index') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-inbox"></i></span>
                            <span class="nxl-mtext">Receivables &amp; Loans</span>
                        </a>
                    </li>

                    <li class="nxl-item nxl-hasmenu {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-file-text"></i></span>
                            <span class="nxl-mtext">Reports</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('reports.remittance') }}">Remittance Reports</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('reports.payroll') }}">Payroll Reports</a></li>
                        </ul>
                    </li>
                @endif

            </ul>
        </div>

        {{-- ── User Footer (hidden via CSS) ── --}}
        <div class="kt-sidebar-user">
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white kt-sidebar-user-avatar" style="width: 45px; height: 45px; font-size: 20px; font-weight: normal; min-width: 45px;">
                {{ auth()->user()->getFirstLetter() }}
            </div>
            <div class="kt-sidebar-user-info">
                <div class="kt-sidebar-user-name">{{ auth()->user()->name }}</div>
                <div class="kt-sidebar-user-role">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</div>
            </div>
            <a href="javascript:void(0);" class="kt-sidebar-user-logout"
                onclick="document.getElementById('logout-form-sidebar').submit();" title="Logout">
                <i class="feather-log-out"></i>
            </a>
            <form method="POST" action="{{ route('logout') }}" id="logout-form-sidebar" style="display:none;">@csrf</form>
        </div>

    </div>
</nav>