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
                            <!-- Recent QR Scans Section -->
                            <tbody id="recent-scans-tbody">
                                <tr>
                                    <td colspan="6" class="text-muted text-center py-3">Loading recent scans...</td>
                                </tr>
                            </tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Load recent QR scans into the same table
    async function loadRecentScans() {
        try {
            const response = await fetch("{{ route('api.attendance.recent') }}", {
                credentials: 'include'
            });
            const logs = await response.json();
            const tbody = document.getElementById('recent-scans-tbody');

            console.log('API Response:', logs);

            if (!logs || logs.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-muted text-center py-3">No recent QR scans</td></tr>';
                return;
            }

            // Group scans by employee and date to consolidate into one row per day per employee
            const consolidated = {};
            logs.forEach(log => {
                const key = `${log.employee_name}|${log.date}`;
                if (!consolidated[key]) {
                    consolidated[key] = {
                        employee_id: log.employee_id,
                        employee_name: log.employee_name,
                        date: log.date,
                        time_in: 'N/A',
                        time_in_id: null,
                        time_out: 'N/A',
                        time_out_id: null
                    };
                }
                if (log.type === 'time_in') {
                    consolidated[key].time_in = log.time;
                    consolidated[key].time_in_id = log.id;
                } else if (log.type === 'time_out') {
                    consolidated[key].time_out = log.time;
                    consolidated[key].time_out_id = log.id;
                }
            });

            let html = '';
            Object.values(consolidated).forEach(record => {
                // Use time_in_id if available, otherwise time_out_id - at least one must exist
                const logId = record.time_in_id || record.time_out_id;
                console.log('Record:', record, 'LogId:', logId);
                
                if (!logId) {
                    console.warn('No log ID found for record:', record);
                    return;
                }
                
                html += `
                    <tr>
                        <td><strong>${record.employee_name}</strong></td>
                        <td><small class="text-muted">${record.date}</small></td>
                        <td>${record.time_in}</td>
                        <td>${record.time_out}</td>
                        <td><span class="badge bg-success">QR Scanned</span></td>
                    </tr>
                `;
            });

            tbody.innerHTML = html;
        } catch (error) {
            console.error('Failed to load recent scans:', error);
            document.getElementById('recent-scans-tbody').innerHTML = 
                '<tr><td colspan="6" class="text-danger text-center py-3"><i class="feather-alert-circle me-2"></i>Failed to load recent scans</td></tr>';
        }
    }

    // Load on page load and refresh every 30 seconds
    window.addEventListener('load', () => {
        loadRecentScans();
        setInterval(loadRecentScans, 30000);
    });
</script>

@endsection
