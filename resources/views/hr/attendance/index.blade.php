@extends('layouts.layout')

@section('content')
<div class="container-fluid">
    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex gap-2">
                <a href="{{ route('attendance.create') }}" class="btn btn-primary btn-sm">
                    <i class="feather-plus me-2"></i>Record Attendance
                </a>
                <a href="{{ route('hr.attendance.monitor') }}" class="btn btn-success btn-sm" target="_blank">
                    <i class="feather-monitor me-2"></i>QR Monitor Display
                </a>
            </div>
        </div>
    </div>

    <!-- Recent QR Scans -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="feather-log-in me-2"></i>Recent QR Scans (Last 24 Hours)
                    </h5>
                </div>
                <div class="card-body">
                    <div id="recent-scans-container">
                        <p class="text-muted text-center py-3">Loading recent scans...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Records Table -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Attendance Records</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
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
                            @foreach($attendances as $attendance)
                                <tr>
                                    <td>{{ $attendance->employee->first_name }} {{ $attendance->employee->last_name }}</td>
                                    <td>{{ $attendance->date }}</td>
                                    <td>{{ $attendance->time_in ? $attendance->time_in->setTimezone(config('app.timezone'))->format('g:i A') : 'N/A' }}</td>
                                    <td>{{ $attendance->time_out ? $attendance->time_out->setTimezone(config('app.timezone'))->format('g:i A') : 'N/A' }}</td>
                                    <td><span class="badge bg-info">{{ $attendance->status }}</span></td>
                                    <td>
                                        <a href="{{ route('attendance.edit', $attendance) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('attendance.destroy', $attendance) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Load recent QR scans
    async function loadRecentScans() {
        try {
            const response = await fetch("{{ route('api.attendance.recent') }}");
            const logs = await response.json();
            const container = document.getElementById('recent-scans-container');

            if (!logs || logs.length === 0) {
                container.innerHTML = '<p class="text-muted text-center py-3">No recent QR scans</p>';
                return;
            }

            let html = '<div class="table-responsive"><table class="table table-sm table-hover mb-0"><thead class="table-light"><tr><th>Employee</th><th>Type</th><th>Time</th><th>Date</th></tr></thead><tbody>';
            
            logs.forEach(log => {
                const typeLabel = log.type === 'time_in' ? 'TIME IN' : 'TIME OUT';
                const badgeClass = log.type === 'time_in' ? 'bg-success' : 'bg-warning';
                html += `
                    <tr>
                        <td><strong>${log.employee_name}</strong></td>
                        <td><span class="badge ${badgeClass}">${typeLabel}</span></td>
                        <td>${log.time}</td>
                        <td><small class="text-muted">${log.date}</small></td>
                    </tr>
                `;
            });

            html += '</tbody></table></div>';
            container.innerHTML = html;
        } catch (error) {
            console.error('Failed to load recent scans:', error);
            document.getElementById('recent-scans-container').innerHTML = 
                '<p class="text-danger text-center py-3"><i class="feather-alert-circle me-2"></i>Failed to load recent scans</p>';
        }
    }

    // Load on page load and refresh every 30 seconds
    window.addEventListener('load', () => {
        loadRecentScans();
        setInterval(loadRecentScans, 30000);
    });
</script>

@endsection
