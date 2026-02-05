@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <!-- Employee & Attendance Statistics Row -->
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
                    <div class="fs-12 fw-medium text-muted mb-3">Active Employees</div>
                    <div class="hstack justify-content-between lh-base">
                        <h3>{{ $activeEmployees }}</h3>
                        <div class="hstack gap-2 fs-11 text-success">
                            <i class="feather-check-circle fs-12"></i>
                            <span>{{ $totalEmployees > 0 ? number_format(($activeEmployees/$totalEmployees)*100, 0) : 0 }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-2 col-lg-4 col-md-6">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="fs-12 fw-medium text-muted mb-3">Present Today</div>
                    <div class="hstack justify-content-between lh-base">
                        <h3>{{ $presentToday }}</h3>
                        <div class="hstack gap-2 fs-11 text-success">
                            <i class="feather-arrow-up-circle fs-12"></i>
                            <span>{{ number_format($attendanceRate, 1) }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-2 col-lg-4 col-md-6">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="fs-12 fw-medium text-muted mb-3">Absent Today</div>
                    <div class="hstack justify-content-between lh-base">
                        <h3>{{ $absentToday }}</h3>
                        <div class="hstack gap-2 fs-11 text-danger">
                            <i class="feather-x-circle fs-12"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-2 col-lg-4 col-md-6">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="fs-12 fw-medium text-muted mb-3">Late Today</div>
                    <div class="hstack justify-content-between lh-base">
                        <h3>{{ $lateToday }}</h3>
                        <div class="hstack gap-2 fs-11 text-warning">
                            <i class="feather-alert-circle fs-12"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-2 col-lg-4 col-md-6">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="fs-12 fw-medium text-muted mb-3">On Leave</div>
                    <div class="hstack justify-content-between lh-base">
                        <h3>{{ $onLeaveEmployees }}</h3>
                        <div class="hstack gap-2 fs-11 text-info">
                            <i class="feather-calendar fs-12"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Leave & Overtime Status Cards -->
<div class="col-md-12">
    <div class="row mb-4">
        <div class="col-xxl-3 col-md-6">
            <div class="card card-body">
                <div class="fs-12 text-muted mb-3">Pending Leave Requests</div>
                <div class="hstack justify-content-between mt-4">
                    <div class="text-dark fw-bold fs-5">{{ $pendingLeaves }}</div>
                    <span class="badge bg-soft-warning text-warning">
                        <i class="feather-clock fs-10"></i>
                    </span>
                </div>
                <div class="progress mt-3 ht-3">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $totalLeaves > 0 ? ($pendingLeaves/$totalLeaves)*100 : 0 }}%"></div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-md-6">
            <div class="card card-body">
                <div class="fs-12 text-muted mb-3">Approved Leaves</div>
                <div class="hstack justify-content-between mt-4">
                    <div class="text-dark fw-bold fs-5">{{ $approvedLeaves }}</div>
                    <span class="badge bg-soft-success text-success">
                        <i class="feather-check-circle fs-10"></i>
                    </span>
                </div>
                <div class="progress mt-3 ht-3">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $totalLeaves > 0 ? ($approvedLeaves/$totalLeaves)*100 : 0 }}%"></div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-md-6">
            <div class="card card-body">
                <div class="fs-12 text-muted mb-3">Total Overtime Hours</div>
                <div class="hstack justify-content-between mt-4">
                    <div class="text-dark fw-bold fs-5">{{ number_format($totalOvertimeHours, 1) }}</div>
                    <span class="badge bg-soft-info text-info">
                        <i class="feather-trending-up fs-10"></i>
                    </span>
                </div>
                <div class="mt-3">
                    <span class="fs-11 text-muted">{{ $totalOvertimeRecords }} records</span>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-md-6">
            <div class="card card-body">
                <div class="fs-12 text-muted mb-3">Total Undertime Hours</div>
                <div class="hstack justify-content-between mt-4">
                    <div class="text-dark fw-bold fs-5">{{ number_format($totalUndertimeHours, 1) }}</div>
                    <span class="badge bg-soft-danger text-danger">
                        <i class="feather-trending-down fs-10"></i>
                    </span>
                </div>
                <div class="mt-3">
                    <a href="#" class="fs-11 text-primary fw-semibold">View Details →</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Attendance Trend & Employee Status -->
<div class="col-md-12">
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">7-Day Attendance Trend</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <tbody>
                                @foreach($attendanceTrend as $trend)
                                    <tr>
                                        <td class="fw-medium fs-12">{{ $trend['date'] }}</td>
                                        <td>
                                            <span class="badge bg-success">P: {{ $trend['present'] }}</span>
                                            <span class="badge bg-danger">A: {{ $trend['absent'] }}</span>
                                            <span class="badge bg-warning">L: {{ $trend['late'] }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Employee Status Summary</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fs-12 fw-medium">Active Employees</span>
                            <span class="fs-12 fw-bold text-success">{{ $activeEmployees }}</span>
                        </div>
                        <div class="progress ht-4">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $totalEmployees > 0 ? ($activeEmployees/$totalEmployees)*100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fs-12 fw-medium">Inactive Employees</span>
                            <span class="fs-12 fw-bold text-danger">{{ $inactiveEmployees }}</span>
                        </div>
                        <div class="progress ht-4">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $totalEmployees > 0 ? ($inactiveEmployees/$totalEmployees)*100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fs-12 fw-medium">On Leave</span>
                            <span class="fs-12 fw-bold text-info">{{ $onLeaveEmployees }}</span>
                        </div>
                        <div class="progress ht-4">
                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $totalEmployees > 0 ? ($onLeaveEmployees/$totalEmployees)*100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-top text-center">
                        <div class="fs-12 text-muted mb-1">Overall Attendance Rate</div>
                        <div class="fw-bold fs-5 text-primary">{{ number_format($attendanceRate, 1) }}%</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Leave Requests Table -->
<div class="col-md-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Recent Leave Requests</h5>
            <a href="#" class="btn btn-primary btn-sm">View All</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Employee</th>
                            <th>Leave Type</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Days</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentLeaves as $leave)
                            <tr>
                                <td><strong>{{ $leave->employee->name ?? 'N/A' }}</strong></td>
                                <td>{{ ucfirst($leave->leave_type ?? 'N/A') }}</td>
                                <td>{{ $leave->start_date?->format('M d, Y') ?? 'N/A' }}</td>
                                <td>{{ $leave->end_date?->format('M d, Y') ?? 'N/A' }}</td>
                                <td>{{ $leave->duration_days ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $leave->status === 'approved' ? 'success' : ($leave->status === 'pending' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($leave->status) }}
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
                                <td colspan="7" class="text-center text-muted py-4">No leave records found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Recent Attendance Records Table -->
<div class="col-md-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Recent Attendance Records</h5>
            <a href="#" class="btn btn-primary btn-sm">View All</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Employee</th>
                            <th>Date</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentAttendance as $attendance)
                            <tr>
                                <td><strong>{{ $attendance->employee->name ?? 'N/A' }}</strong></td>
                                <td>{{ $attendance->date?->format('M d, Y') ?? 'N/A' }}</td>
                                <td>{{ $attendance->time_in?->format('H:i A') ?? 'N/A' }}</td>
                                <td>{{ $attendance->time_out?->format('H:i A') ?? '--:-- --' }}</td>
                                <td>
                                    <span class="badge bg-{{ $attendance->status === 'present' ? 'success' : ($attendance->status === 'late' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($attendance->status) }}
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
                                <td colspan="6" class="text-center text-muted py-4">No attendance records found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
