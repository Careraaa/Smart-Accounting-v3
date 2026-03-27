@extends('layouts.layout')

@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <span class="card-title mb-0">Payroll Management</span>
                    <p class="text-muted small mt-1 mb-0">Manage payroll cutoff schedules and employee payments</p>
                </div>
                <a href="{{ route('payroll.salary-computation.batch-generate') }}" class="btn btn-sm btn-primary">
                    <i class="feather-layers me-1"></i>Release Payroll Batch
                </a>
            </div>
            <div class="card-body">

                {{-- Statistics Cards --}}
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card card-statistic">
                            <div class="card-body">
                                <div class="stat-label">Next Cutoff Date</div>
                                <h3 class="mb-1" style="color: #8B3A62;">{{ $nextCutoffDate ? $nextCutoffDate->format('M d, Y') : 'N/A' }}</h3>
                                <small class="text-muted">{{ $nextCutoffDate ? $nextCutoffDate->format('l') : 'Not set' }}</small>
                            </div>
                            <div class="card-icon" style="color: #8B3A62; opacity: 0.2;">
                                <i class="feather-calendar" style="font-size: 2.5rem;"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-statistic">
                            <div class="card-body">
                                <div class="stat-label">Total Amount</div>
                                <h3 class="mb-1" style="color: #B8860B;">₱{{ number_format($totalPayroll, 2) }}</h3>
                                <small class="text-muted">{{ $payrollCount }} pending employee(s)</small>
                            </div>
                            <div class="card-icon" style="color: #B8860B; opacity: 0.2;">
                                <i class="feather-dollar-sign" style="font-size: 2.5rem;"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-statistic">
                            <div class="card-body">
                                <div class="stat-label">Active Employees</div>
                                <h3 class="mb-1" style="color: #8B3A5D;">{{ $activeEmployees }}</h3>
                                <small class="text-muted">Total payroll-eligible</small>
                            </div>
                            <div class="card-icon" style="color: #8B3A5D; opacity: 0.2;">
                                <i class="feather-users" style="font-size: 2.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Payroll Cutoff Schedule --}}
                <h5 class="mb-3">Payroll Cutoff Schedule</h5>
                <div class="row mb-4">
                    @forelse ($cutoffSchedules as $schedule)
                        @php
                            $monthName = \Carbon\Carbon::now()->format('F');
                            $displayLabel = $monthName . ' - ' . $schedule->label;
                            
                            // Determine icon and color based on batch
                            if ($schedule->cutoff_day == 5) {
                                $iconClass = 'feather-calendar';
                                $iconColor = '#8B3A62';
                                $borderColor = '#d4a5c4';
                                $bgColor = '#f5e6f0';
                                $subtitleText = 'First cutoff period: 1st - 15th';
                            } else {
                                $iconClass = 'feather-check-circle';
                                $iconColor = '#B8860B';
                                $borderColor = '#dab894';
                                $bgColor = '#f9f3eb';
                                $lastDay = \Carbon\Carbon::now()->endOfMonth()->day;
                                $subtitleText = 'Second cutoff period: 16th - ' . $lastDay;
                            }
                        @endphp
                        <div class="col-md-6 mb-3">
                            <div class="card" style="border: 2px solid {{ $borderColor }}; background-color: {{ $bgColor }}; position: relative; overflow: hidden;">
                                <div class="card-body" style="padding: 20px; display: flex; justify-content: space-between; align-items: flex-start;">
                                    <div style="flex: 1;">
                                        <h6 class="mb-1" style="color: {{ $iconColor }}; font-weight: 700; font-size: 1.1rem;">
                                            {{ $displayLabel }}
                                        </h6>
                                        <small style="color: {{ $iconColor }}; opacity: 0.8;">
                                            {{ $subtitleText }}
                                        </small>
                                    </div>
                                    <div style="color: {{ $iconColor }}; opacity: 0.3; font-size: 2.5rem; margin-left: 15px;">
                                        <i class="feather {{ $iconClass }}" style="font-size: 2.5rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info" role="alert">
                                No cutoff schedules configured.
                            </div>
                        </div>
                    @endforelse
                </div>

                {{-- Employee Payroll Section --}}
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Employee Payroll</h5>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="fw-semibold text-uppercase fs-6">Employee Name</th>
                                        <th class="fw-semibold text-uppercase fs-6">Department</th>
                                        <th class="fw-semibold text-uppercase fs-6">Position</th>
                                        <th class="fw-semibold text-uppercase fs-6 text-end">Gross Pay</th>
                                        <th class="fw-semibold text-uppercase fs-6 text-end">Deductions</th>
                                        <th class="fw-semibold text-uppercase fs-6 text-end">Net Pay</th>
                                        <th class="fw-semibold text-uppercase fs-6 text-center">Status</th>
                                        <th class="fw-semibold text-uppercase fs-6 text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($payrolls->where('status', '!=', 'approved') as $payroll)
                                        <tr>
                                            <td>{{ $payroll->user->first_name }} {{ $payroll->user->last_name }}</td>
                                            <td>
                                                <span class="badge bg-light text-dark">{{ $payroll->user->department ?? 'N/A' }}</span>
                                            </td>
                                            <td>{{ $payroll->user->position ?? 'N/A' }}</td>
                                            <td class="text-end">₱{{ number_format($payroll->gross_pay, 2) }}</td>
                                            <td class="text-end">₱{{ number_format($payroll->total_deductions, 2) }}</td>
                                            <td class="text-end fw-semibold">₱{{ number_format($payroll->net_pay, 2) }}</td>
                                            <td class="text-center">
                                                @php
                                                    $statusMap = [
                                                        'draft' => 'badge bg-secondary',
                                                        'pending' => 'badge bg-warning',
                                                        'submitted' => 'badge bg-warning',
                                                        'approved' => 'badge bg-info',
                                                        'paid' => 'badge bg-success',
                                                        'rejected' => 'badge bg-danger',
                                                    ];
                                                    $statusClass = $statusMap[$payroll->status] ?? $statusMap['draft'];
                                                @endphp
                                                <span class="{{ $statusClass }}">
                                                    {{ ucfirst($payroll->status) }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-1">
                                                    <a href="{{ route('payroll.salary-computation.show', $payroll) }}" class="emp-action-btn emp-action-view" title="View">
                                                        <i class="feather-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-5">
                                                <i class="feather-file-text" style="font-size: 2rem; opacity: 0.3; display: block; margin-bottom: 10px;"></i>
                                                No payroll records found
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
        </div>
    </div>
@endsection
