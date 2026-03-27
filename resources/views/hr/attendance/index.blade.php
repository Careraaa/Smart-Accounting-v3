@extends('layouts.layout')

@section('content')
    <div class="col-md-12">

        {{-- Quick Actions --}}
        <div class="d-flex gap-2 mb-4 justify-content-end">
            {{-- Manual Log  --}}
            @if (auth()->user()->role === 'hr')
                <a href="{{ route('attendance.create') }}" class="btn btn-primary btn-sm">
                    <i class="feather-plus me-1"></i> Manual Log
                </a>
            @endif
        </div>

        {{-- Attendance Records Table --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="card-title mb-0">Attendance Records</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover w-100 mb-0">
                        <thead>
                            <tr>
                                <th>
                                    <div class="sort-link">Employee</div>
                                </th>
                                <th>
                                    <div class="sort-link">Date</div>
                                </th>
                                <th>
                                    <div class="sort-link">Time In</div>
                                </th>
                                <th>
                                    <div class="sort-link">Time Out</div>
                                </th>
                                <th class="text-center">
                                    <div class="sort-link justify-content-center">Status</div>
                                </th>
                                <th class="text-center">
                                    <div class="sort-link justify-content-center">Entry Type</div>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="attendance-tbody">
                            @forelse($attendances as $attendance)
                                <tr>
                                    <td>
                                        <strong>
                                            {{ $attendance->employee->first_name }}
                                            {{ $attendance->employee->last_name }}
                                        </strong>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $attendance->date->format('M d, Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        {{ $attendance->time_in
                                            ? \Carbon\Carbon::createFromFormat('H:i:s', $attendance->time_in)->format('g:i A')
                                            : '—' }}
                                    </td>
                                    <td>
                                        {{ $attendance->time_out
                                            ? \Carbon\Carbon::createFromFormat('H:i:s', $attendance->time_out)->format('g:i A')
                                            : '—' }}
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $statusStyles = [
                                                'present' => [
                                                    'bg' => '#f0fdf4',
                                                    'color' => '#16a34a',
                                                    'border' => '#bbf7d0',
                                                ],
                                                'late' => [
                                                    'bg' => '#fffbeb',
                                                    'color' => '#d97706',
                                                    'border' => '#fde68a',
                                                ],
                                                'absent' => [
                                                    'bg' => '#fff1f2',
                                                    'color' => '#e11d48',
                                                    'border' => '#fcd0d0',
                                                ],
                                                'early_leave' => [
                                                    'bg' => '#f5f3ff',
                                                    'color' => '#7c3aed',
                                                    'border' => '#ddd6fe',
                                                ],
                                            ];
                                            $s = $statusStyles[$attendance->status] ?? [
                                                'bg' => '#f4f5f7',
                                                'color' => '#9898a8',
                                                'border' => '#e8e8ef',
                                            ];
                                        @endphp
                                        <span class="emp-badge"
                                            style="background:{{ $s['bg'] }}; color:{{ $s['color'] }}; border:1px solid {{ $s['border'] }};">
                                            {{ ucfirst(str_replace('_', ' ', $attendance->status)) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if ($attendance->is_manual)
                                            <span class="emp-badge"
                                                style="background:#f0f9ff; color:#0284c7; border:1px solid #bae6fd;">
                                                <i class="feather-edit-2 me-1" style="font-size:0.7rem;"></i>Manual
                                            </span>
                                        @else
                                            <span class="emp-badge"
                                                style="background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0;">
                                                <i class="feather-check-circle me-1" style="font-size:0.7rem;"></i>QR
                                                Scanned
                                            </span>
                                        @endif
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

                @if ($attendances->hasPages())
                    <div class="d-flex justify-content-between align-items-center px-3 py-2">
                        <div class="small text-muted">
                            Showing
                            <strong>{{ $attendances->firstItem() }}</strong>
                            to
                            <strong>{{ $attendances->lastItem() }}</strong>
                            of
                            <strong>{{ $attendances->total() }}</strong>
                            entries
                        </div>
                        <div>
                            {{ $attendances->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>

    <script>
        window.attendanceRowsUrl = "{{ route('api.attendance.table-rows') }}";
        window.notificationsCountUrl = "{{ route('notifications.count') }}";
    </script>


    @if (auth()->user()->role === 'hr')
        <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
        <script>
            // ── Refresh attendance table dynamically without full page reload ────
            function refreshAttendanceTable() {
                fetch(window.attendanceRowsUrl)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.rows && data.rows.length > 0) {
                            updateTableRows(data.rows);
                        }
                    })
                    .catch(err => {
                        console.error('Failed to refresh attendance table:', err.message);
                        const tbody = document.getElementById('attendance-tbody');
                        if (tbody) {
                            tbody.innerHTML =
                                `<tr><td colspan="6" class="text-center text-muted">Error loading attendance data</td></tr>`;
                        }
                    });
            }


            // ── Update table rows with fresh data ────
            function updateTableRows(rows) {
                const tbody = document.getElementById('attendance-tbody');
                if (!tbody) return;

                const statusStyles = {
                    'present': {
                        bg: '#f0fdf4',
                        color: '#16a34a',
                        border: '#bbf7d0'
                    },
                    'late': {
                        bg: '#fffbeb',
                        color: '#d97706',
                        border: '#fde68a'
                    },
                    'absent': {
                        bg: '#fff1f2',
                        color: '#e11d48',
                        border: '#fcd0d0'
                    },
                    'early_leave': {
                        bg: '#f5f3ff',
                        color: '#7c3aed',
                        border: '#ddd6fe'
                    }
                };

                let html = '';
                rows.forEach(att => {
                    const statusStyle = statusStyles[att.status] || {
                        bg: '#f4f5f7',
                        color: '#9898a8',
                        border: '#e8e8ef'
                    };
                    const entryType = att.is_manual ?
                        '<span class="emp-badge" style="background:#f0f9ff; color:#0284c7; border:1px solid #bae6fd;"><i class="feather-edit-2 me-1" style="font-size:0.7rem;"></i>Manual</span>' :
                        '<span class="emp-badge" style="background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0;"><i class="feather-check-circle me-1" style="font-size:0.7rem;"></i>QR Scanned</span>';

                    html += `
                    <tr>
                        <td><strong>${att.employee}</strong></td>
                        <td><small class="text-muted">${att.date}</small></td>
                        <td>${att.time_in}</td>
                        <td>${att.time_out}</td>
                        <td class="text-center">
                            <span class="emp-badge" style="background:${statusStyle.bg}; color:${statusStyle.color}; border:1px solid ${statusStyle.border};">
                                ${att.status.replace(/_/g, ' ').charAt(0).toUpperCase() + att.status.replace(/_/g, ' ').slice(1)}
                            </span>
                        </td>
                        <td class="text-center">${entryType}</td>
                    </tr>
                `;
                });

                tbody.innerHTML = html;
            }

            function pollNotifications() {
                fetch(window.notificationsCountUrl)
                    .then(r => {
                        if (!r.ok) throw new Error(`HTTP ${r.status}`);
                        return r.json();
                    })
                    .then(data => {
                        const unread = data.unread_count;

                        // Update the dot badge next to the bell
                        const notifCount = document.getElementById('notif-count');
                        if (notifCount) {
                            notifCount.textContent = unread > 99 ? '99+' : unread;
                            notifCount.style.display = unread > 0 ? 'inline-block' : 'none';
                        }

                        // Update the dropdown badge
                        const notifBadge = document.getElementById('notif-badge');
                        if (notifBadge) {
                            notifBadge.textContent = unread > 0 ? `${unread} New` : '0 New';
                        }
                    })
                    .catch(err => {
                        console.error("Error in notification polling:", err.message);
                    });
            }

            setInterval(pollNotifications, 30000);
            pollNotifications();
        </script>
    @endif
@endsection
