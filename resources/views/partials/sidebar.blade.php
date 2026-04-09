<nav class="nxl-navigation">
    <div class="navbar-wrapper">

        {{-- ── Logo Header ── --}}
        <div class="m-header">
            <a href="{{ route('dashboard') }}" class="b-brand kt-brand-text">
                <span class="kt-logo-icon"><i class="feather-truck"></i></span>
                <span class="kt-logo-full">Knights Transport</span>
                <span class="kt-logo-mini">KT</span>
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

                {{-- ── SUPERADMIN ── --}}
                @if (auth()->user()->role === 'superadmin')
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
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('attendance.index') }}">Attendance Records</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('overtime.index') }}">Overtime / Undertime</a></li>
                        </ul>
                    </li>

                    <li class="nxl-item nxl-hasmenu{{ request()->routeIs('leave.*', 'leave-type.*') ? 'active' : '' }}">
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
                            <li class="nxl-item {{ request()->routeIs('payroll.history.*') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('payroll.history.index') }}">Payroll History</a>
                            </li>
                        </ul>
                    </li>

                    <li class="nxl-item nxl-caption"><label>Operational Records</label></li>

                    <li class="nxl-item nxl-hasmenu {{ request()->routeIs('drivers.*', 'paos.*', 'routes.*', 'vehicles.*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-users"></i></span>
                            <span class="nxl-mtext">Transport Management</span>
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
                                <a class="nxl-link" href="{{ route('routes.index') }}">Routes</a>
                            </li>
                            <li class="nxl-item {{ request()->routeIs('vehicles.*') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('vehicles.index') }}">Vehicles</a>
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
                                <a class="nxl-link" href="{{ route('short-remittances.index') }}">Short Remittances</a>
                            </li>
                        </ul>
                    </li>

                    <li class="nxl-item nxl-caption"><label>Approvals</label></li>

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

                    <li class="nxl-item nxl-caption"><label>Reports</label></li>

                    <li class="nxl-item nxl-hasmenu {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-file-text"></i></span>
                            <span class="nxl-mtext">Reports</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('reports.remittance-details') }}">Remittance Details</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('reports.remittance-summary') }}">Remittance Summary</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('reports.payslips') }}">Payslips</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('reports.payroll-summary') }}">Payroll Summary</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('reports.deduction-summary') }}">Deduction Summary</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('reports.government-contribution') }}">Government Contribution</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('reports.payroll') }}">Payroll Reports</a></li>
                        </ul>
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

                    <li class="nxl-item nxl-hasmenu{{ request()->routeIs('leave.*', 'leave-type.*') ? 'active' : '' }}">
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
                                <a class="nxl-link" href="{{ route('payroll.receivables.index') }}">Payroll Receivables</a>
                            </li>
                            <li class="nxl-item {{ request()->routeIs('payroll.history.*') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('payroll.history.index') }}">Payroll History</a>
                            </li>
                        </ul>
                    </li>

                    <li class="nxl-item nxl-hasmenu {{ request()->routeIs('reports.payslips', 'reports.payroll*', 'reports.deduction*', 'reports.government*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-bar-chart-2"></i></span>
                            <span class="nxl-mtext">Reports</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item {{ request()->routeIs('payroll.generate-payslip.*') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('payroll.generate-payslip.index') }}">Payslips</a>
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

                    <li class="nxl-item nxl-caption"><label>My Profile</label></li>

                    <li class="nxl-item {{ request()->routeIs('employee.profile.*') ? 'active' : '' }}">
                        <a href="{{ route('employee.profile.show') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-user"></i></span>
                            <span class="nxl-mtext">Personal Records</span>
                        </a>
                    </li>

                    <li class="nxl-item {{ request()->routeIs('employee.attachments.*') ? 'active' : '' }}">
                        <a href="{{ route('employee.attachments.index') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-folder"></i></span>
                            <span class="nxl-mtext">My Documents</span>
                        </a>
                    </li>
                @endif

                {{-- ── Accountant ── --}}
                @if (auth()->user()->role === 'accountant')
                    <li class="nxl-item nxl-caption"><label>ACCOUNTING</label></li>

                    {{-- 1. GENERAL LEDGER (CORE MODULE) --}}
                    <li class="nxl-item nxl-hasmenu {{ request()->routeIs('accountant.gl.*', 'accountant.journal-entries.*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-layers"></i></span>
                            <span class="nxl-mtext">General Ledger</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('accountant.gl.index') }}">Chart of Accounts</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('accountant.journal-entries.index') }}">Journal Entries</a></li>
                        </ul>
                    </li>

                    {{-- 2. CASH MANAGEMENT --}}
                    <li class="nxl-item nxl-hasmenu {{ request()->routeIs('accountant.cash.*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-arrow-down-circle"></i></span>
                            <span class="nxl-mtext">Cash Management</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('accountant.cash.receipts') }}">Cash Receipts</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('accountant.cash.receipt.create') }}">Record Receipt</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('accountant.cash.dashboard') }}">Daily Cash Summary</a></li>
                        </ul>
                    </li>

                    {{-- 3. ACCOUNTS RECEIVABLE (AR) --}}
                    <li class="nxl-item nxl-hasmenu">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-arrow-up-right"></i></span>
                            <span class="nxl-mtext">Accounts Receivable</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('remittance-approval.index') }}">Driver Outstanding Remittance</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('reports.remittance') }}">Collection Monitoring</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('accountant.reports.dashboard') }}">Aging Report</a></li>
                        </ul>
                    </li>

                    {{-- 4. ACCOUNTS PAYABLE (AP) --}}
                    <li class="nxl-item nxl-hasmenu {{ request()->routeIs('accountant.suppliers.*', 'accountant.bills.*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-arrow-up-left"></i></span>
                            <span class="nxl-mtext">Accounts Payable</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('accountant.suppliers.index') }}">Supplier Management</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('accountant.bills.index') }}">Bills / Invoices</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('accountant.bills.create') }}">Record Bill</a></li>
                        </ul>
                    </li>

                    {{-- 5. EXPENSE MANAGEMENT --}}
                    <li class="nxl-item nxl-hasmenu {{ request()->routeIs('accountant.expenses.*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-trending-down"></i></span>
                            <span class="nxl-mtext">Expense Management</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('accountant.expenses.dashboard') }}">Expense Dashboard</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('accountant.expenses.categories.index') }}">Expense Categories</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('accountant.expenses.allocations.index') }}">Allocations</a></li>
                        </ul>
                    </li>

                    {{-- 6. FINANCIAL REPORTS (VERY IMPORTANT) --}}
                    <li class="nxl-item nxl-hasmenu {{ request()->routeIs('accountant.reports.*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-bar-chart-2"></i></span>
                            <span class="nxl-mtext">Financial Reports</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('accountant.reports.income-statement') }}">Income Statement</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('accountant.reports.balance-sheet') }}">Balance Sheet</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('accountant.reports.cash-flow-statement') }}">Cash Flow Statement</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('accountant.reports.trial-balance') }}">Trial Balance</a></li>
                        </ul>
                    </li>

                    <li class="nxl-item nxl-caption"><label>APPROVALS & REPORTS</label></li>

                    {{-- PAYROLL RELEASE APPROVAL --}}
                    <li class="nxl-item {{ request()->routeIs('payroll-approval.*') ? 'active' : '' }}">
                        <a href="{{ route('payroll-approval.index') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-check-circle"></i></span>
                            <span class="nxl-mtext">Payroll Release Approval</span>
                        </a>
                    </li>

                    {{-- REMITTANCE APPROVAL --}}
                    <li class="nxl-item {{ request()->routeIs('remittance-approval.*') ? 'active' : '' }}">
                        <a href="{{ route('remittance-approval.index') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-inbox"></i></span>
                            <span class="nxl-mtext">Remittance Reports</span>
                        </a>
                    </li>

                    {{-- PAYROLL REPORTS --}}
                    <li class="nxl-item {{ request()->routeIs('reports.payroll') ? 'active' : '' }}">
                        <a href="{{ route('reports.payroll') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-dollar-sign"></i></span>
                            <span class="nxl-mtext">Payroll Reports</span>
                        </a>
                    </li>

                    {{-- RECEIVABLES & LOANS --}}
                    <li class="nxl-item {{ request()->routeIs('payroll.receivables.*') ? 'active' : '' }}">
                        <a href="{{ route('payroll.receivables.index') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-trending-up"></i></span>
                            <span class="nxl-mtext">Receivables & Loans</span>
                        </a>
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