<nav class="nxl-navigation">
    <div class="navbar-wrapper">

        {{-- ── Logo Header ── --}}
        <div class="m-header">
            <a href="{{ route('dashboard') }}" class="b-brand kt-brand-text">
                <span class="kt-logo-icon"><i class="feather-truck"></i></span>
                <span class="kt-logo-full">Knights Transport</span>
                <span class="kt-logo-mini">KT</span>
            </a>
            </a>
        </div>

        {{-- ── Scrollable Nav Body ── --}}
        <div class="navbar-content">
            <ul class="nxl-navbar">

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
                                <a class="nxl-link" href="{{ route('routes.index') }}">Manage Routes &amp; Vehicles</a>
                            </li>
                        </ul>
                    </li>

                    <li class="nxl-item nxl-hasmenu {{ request()->routeIs('remittances.*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-activity"></i></span>
                            <span class="nxl-mtext">Daily Remittance</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item">
                                <a class="nxl-link" href="{{ route('remittances.index') }}">Record Remittance</a>
                            </li>
                        </ul>
                    </li>

                    <li class="nxl-item nxl-hasmenu {{ request()->routeIs('reports.remittance*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-file-text"></i></span>
                            <span class="nxl-mtext">Remittance Report</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item">
                                <a class="nxl-link" href="{{ route('reports.remittance-details') }}">Remittance Details</a>
                            </li>
                            <li class="nxl-item">
                                <a class="nxl-link" href="{{ route('reports.remittance-summary') }}">Remittance Summary</a>
                            </li>
                        </ul>
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

                    <li class="nxl-item nxl-hasmenu {{ request()->routeIs('attendance.*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-clock"></i></span>
                            <span class="nxl-mtext">Attendance</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('attendance.index') }}">QR Time IN / OUT Records</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('attendance.index') }}">Leave Management</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('attendance.index') }}">Overtime / Undertime</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('attendance.index') }}">Attendance Adjustment</a></li>
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
                                <a class="nxl-link" href="{{ route('payroll.salary-computation.index') }}">Salary Computation</a>
                            </li>
                            <li class="nxl-item {{ request()->routeIs('payroll.statutory-deductions.*') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('payroll.statutory-deductions.index') }}">Statutory Deductions</a>
                            </li>
                            <li class="nxl-item {{ request()->routeIs('payroll.receivables.*') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('payroll.receivables.index') }}">Payroll Receivables</a>
                            </li>
                            <li class="nxl-item {{ request()->routeIs('payroll.generate-payslip.*') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('payroll.generate-payslip.index') }}">Generate Payslip</a>
                            </li>
                        </ul>
                    </li>

                    <li class="nxl-item nxl-hasmenu {{ request()->routeIs('reports.payslips', 'reports.payroll*', 'reports.deduction*', 'reports.government*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-bar-chart-2"></i></span>
                            <span class="nxl-mtext">Payroll Reports</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('reports.payslips') }}">Payslips</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('reports.payroll-summary') }}">Payroll Summary</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('reports.deduction-summary') }}">Deduction Summary</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('reports.government-contribution') }}">Government Contribution</a></li>
                        </ul>
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

                    <li class="nxl-item nxl-hasmenu {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-file-text"></i></span>
                            <span class="nxl-mtext">Reports</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('reports.remittance') }}">Remittance Reports</a></li>
                        </ul>
                    </li>
                @endif

            </ul>
        </div>

        {{-- ── User Footer (hidden via CSS) ── --}}
        <div class="kt-sidebar-user">
            <img src="{{ auth()->user()->profile_picture ? asset('storage/' . auth()->user()->profile_picture) : asset('images/avatar/avatar.png') }}"
                alt="avatar" class="kt-sidebar-user-avatar">
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