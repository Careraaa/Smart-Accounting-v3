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
                                <span>{{ $totalEmployees > 0 ? number_format(($activeEmployees / $totalEmployees) * 100, 0) : 0 }}%</span>
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
                        <div class="progress-bar bg-warning" role="progressbar"
                            style="width: {{ $totalLeaves > 0 ? ($pendingLeaves / $totalLeaves) * 100 : 0 }}%"></div>
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
                        <div class="progress-bar bg-success" role="progressbar"
                            style="width: {{ $totalLeaves > 0 ? ($approvedLeaves / $totalLeaves) * 100 : 0 }}%"></div>
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
                                    @foreach ($attendanceTrend as $trend)
                                        <tr>
                                            <td class="fw-medium fs-12">{{ $trend['date'] }}</td>
                                            <td>
                                                <span class="emp-badge" style="background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0;">P: {{ $trend['present'] }}</span>
                                                <span class="emp-badge" style="background:#fff1f2; color:#e11d48; border:1px solid #fcd0d0;">A: {{ $trend['absent'] }}</span>
                                                <span class="emp-badge" style="background:#fffbeb; color:#d97706; border:1px solid #fde68a;">L: {{ $trend['late'] }}</span>
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
                                <div class="progress-bar bg-success" role="progressbar"
                                    style="width: {{ $totalEmployees > 0 ? ($activeEmployees / $totalEmployees) * 100 : 0 }}%">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="fs-12 fw-medium">Inactive Employees</span>
                                <span class="fs-12 fw-bold text-danger">{{ $inactiveEmployees }}</span>
                            </div>
                            <div class="progress ht-4">
                                <div class="progress-bar bg-danger" role="progressbar"
                                    style="width: {{ $totalEmployees > 0 ? ($inactiveEmployees / $totalEmployees) * 100 : 0 }}%">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="fs-12 fw-medium">On Leave</span>
                                <span class="fs-12 fw-bold text-info">{{ $onLeaveEmployees }}</span>
                            </div>
                            <div class="progress ht-4">
                                <div class="progress-bar bg-info" role="progressbar"
                                    style="width: {{ $totalEmployees > 0 ? ($onLeaveEmployees / $totalEmployees) * 100 : 0 }}%">
                                </div>
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
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="card-title mb-0">Recent Leave Requests</span>
                <a href="{{ route('leave.index') }}" class="btn btn-primary btn-sm">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover w-100 mb-0">
                        <thead>
                            <tr>
                                <th><div class="sort-link">Employee</div></th>
                                <th><div class="sort-link">Leave Type</div></th>
                                <th><div class="sort-link">Start Date</div></th>
                                <th><div class="sort-link">End Date</div></th>
                                <th><div class="sort-link">Days</div></th>
                                <th class="text-center"><div class="sort-link justify-content-center">Status</div></th>
                                <th class="text-center"><div class="sort-link justify-content-center">Actions</div></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentLeaves as $leave)
                                <tr>
                                    <td><strong>{{ $leave->employee->name ?? 'N/A' }}</strong></td>
                                    <td>{{ ucfirst($leave->leave_type ?? 'N/A') }}</td>
                                    <td><small class="text-muted">{{ $leave->start_date?->format('M d, Y') ?? 'N/A' }}</small></td>
                                    <td><small class="text-muted">{{ $leave->end_date?->format('M d, Y') ?? 'N/A' }}</small></td>
                                    <td>{{ $leave->duration_days ?? 'N/A' }}</td>
                                    <td class="text-center">
                                        @php
                                            $leaveStatusMap = [
                                                'pending'  => ['bg' => '#fffbeb', 'color' => '#d97706', 'border' => '#fde68a'],
                                                'approved' => ['bg' => '#f0fdf4', 'color' => '#16a34a', 'border' => '#bbf7d0'],
                                                'rejected' => ['bg' => '#fff1f2', 'color' => '#e11d48', 'border' => '#fcd0d0'],
                                            ];
                                            $ls = $leaveStatusMap[$leave->status] ?? ['bg' => '#f4f5f7', 'color' => '#9898a8', 'border' => '#e8e8ef'];
                                        @endphp
                                        <span class="emp-badge"
                                            style="background:{{ $ls['bg'] }}; color:{{ $ls['color'] }}; border:1px solid {{ $ls['border'] }};">
                                            {{ ucfirst($leave->status) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('leave.show', $leave) }}" class="emp-action-btn emp-action-view" title="View">
                                            <i class="feather-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">
                                        <i class="feather-file-text d-block mb-2" style="font-size:28px; opacity:.3;"></i>
                                        No leave records found
                                    </td>
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
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="card-title mb-0">Recent Attendance Records</span>
                <a href="{{ route('attendance.index') }}" class="btn btn-primary btn-sm">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover w-100 mb-0">
                        <thead>
                            <tr>
                                <th><div class="sort-link">Employee</div></th>
                                <th><div class="sort-link">Date</div></th>
                                <th><div class="sort-link">Time In</div></th>
                                <th><div class="sort-link">Time Out</div></th>
                                <th class="text-center"><div class="sort-link justify-content-center">Status</div></th>
                                <th class="text-center"><div class="sort-link justify-content-center">Actions</div></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentAttendance as $attendance)
                                <tr>
                                    <td><strong>{{ $attendance->employee->name ?? 'N/A' }}</strong></td>
                                    <td><small class="text-muted">{{ $attendance->date?->format('M d, Y') ?? 'N/A' }}</small></td>
                                    <td>{{ $attendance->time_in ? \Carbon\Carbon::createFromFormat('H:i:s', $attendance->time_in)->format('g:i A') : '—' }}</td>
                                    <td>{{ $attendance->time_out ? \Carbon\Carbon::createFromFormat('H:i:s', $attendance->time_out)->format('g:i A') : '—' }}</td>
                                    <td class="text-center">
                                        @php
                                            $attStatusMap = [
                                                'present' => ['bg' => '#f0fdf4', 'color' => '#16a34a', 'border' => '#bbf7d0'],
                                                'late'    => ['bg' => '#fffbeb', 'color' => '#d97706', 'border' => '#fde68a'],
                                                'absent'  => ['bg' => '#fff1f2', 'color' => '#e11d48', 'border' => '#fcd0d0'],
                                            ];
                                            $as = $attStatusMap[$attendance->status] ?? ['bg' => '#f4f5f7', 'color' => '#9898a8', 'border' => '#e8e8ef'];
                                        @endphp
                                        <span class="emp-badge"
                                            style="background:{{ $as['bg'] }}; color:{{ $as['color'] }}; border:1px solid {{ $as['border'] }};">
                                            {{ ucfirst($attendance->status) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('attendance.show', $attendance) }}" class="emp-action-btn emp-action-view" title="View">
                                            <i class="feather-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <i class="feather-clock d-block mb-2" style="font-size:28px; opacity:.3;"></i>
                                        No attendance records found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection