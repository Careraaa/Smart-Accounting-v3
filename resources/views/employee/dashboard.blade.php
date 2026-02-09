@extends('layouts.layout')

@section('content')
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="mb-2">Welcome, {{ auth()->user()->name }}!</h1>
            <p class="text-muted">
                <i class="feather-calendar me-1"></i>
                {{ now()->format('l, F d, Y') }}
            </p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="feather-clock me-2"></i>Attendance Management
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <a href="{{ route('attendance.scan') }}" class="btn btn-lg btn-outline-primary w-100 py-3">
                                <i class="feather-camera d-block mb-2" style="font-size: 32px;"></i>
                                <strong>Scan QR Code</strong>
                                <small class="d-block text-muted mt-1">Log in/out via QR</small>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-info h-100 d-flex align-items-center mb-0">
                                <div>
                                    <h6 class="mb-2">Current Status</h6>
                                    <p class="mb-1">
                                        <span id="current-status" class="badge bg-warning">Loading...</span>
                                    </p>
                                    <small class="text-muted">
                                        Last: <span id="last-log-time">--:--</span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Today's Attendance Card -->
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="feather-list me-2"></i>Today's Attendance
                    </h5>
                </div>
                <div class="card-body">
                    <div id="attendance-logs" class="list-group">
                        <p class="text-muted text-center py-3">Loading attendance logs...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- My Profile Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="feather-user me-2"></i>My Profile
                    </h5>
                </div>
                <div class="card-body">
                    @if (auth()->user()->employee)
                        <p class="mb-2">
                            <strong>Position:</strong>
                            <span class="badge bg-secondary">{{ auth()->user()->employee->position }}</span>
                        </p>
                        <p class="mb-2">
                            <strong>Department:</strong>
                            {{ auth()->user()->employee->department }}
                        </p>
                        <p class="mb-2">
                            <strong>Status:</strong>
                            <span class="badge bg-{{ auth()->user()->employee->status === 'active' ? 'success' : 'danger' }}">
                                {{ ucfirst(auth()->user()->employee->status) }}
                            </span>
                        </p>
                        <p class="mb-0">
                            <strong>Hire Date:</strong>
                            {{ auth()->user()->employee->date_of_hire->format('M d, Y') }}
                        </p>
                    @else
                        <p class="text-muted">No employee profile linked</p>
                    @endif
                </div>
            </div>

            <!-- Quick Tips Card -->
            <div class="card shadow-sm bg-light">
                <div class="card-header bg-white border-bottom-0">
                    <h5 class="mb-0">
                        <i class="feather-info me-2"></i>Quick Tips
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled small">
                        <li class="mb-2">
                            <i class="feather-check-circle text-success me-2"></i>
                            Scan QR code upon arrival
                        </li>
                        <li class="mb-2">
                            <i class="feather-check-circle text-success me-2"></i>
                            Scan again before leaving
                        </li>
                        <li class="mb-2">
                            <i class="feather-check-circle text-success me-2"></i>
                            Keep your device camera accessible
                        </li>
                        <li>
                            <i class="feather-check-circle text-success me-2"></i>
                            Report issues to HR immediately
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Load today's attendance logs
    window.addEventListener('load', async () => {
        try {
            // For now, we'll show a sample message
            // This can be expanded to fetch actual logs via an API
            const lastLog = localStorage.getItem('lastAttendanceLog');
            
            if (lastLog) {
                const logData = JSON.parse(lastLog);
                document.getElementById('current-status').textContent = logData.type.replace('_', ' ').toUpperCase();
                document.getElementById('current-status').className = `badge bg-${logData.type === 'time_in' ? 'success' : 'warning'}`;
                document.getElementById('last-log-time').textContent = logData.time;
            } else {
                document.getElementById('current-status').textContent = 'NOT LOGGED';
                document.getElementById('current-status').className = 'badge bg-danger';
            }

            // Fetch attendance logs for today
            // This would need an endpoint to be created to fetch real data
            const logsHtml = `
                <div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">First Log</h6>
                            <small class="text-muted">Waiting for first scan...</small>
                        </div>
                    </div>
                </div>
            `;
            document.getElementById('attendance-logs').innerHTML = logsHtml;
        } catch (e) {
            console.log('No logs available');
        }
    });
</script>
@endsection
