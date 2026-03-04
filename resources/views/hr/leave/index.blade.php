@extends('layouts.layout')

@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="card-title mb-0">Leave Management</span>
                <a href="{{ route('leave.create') }}" class="btn btn-primary btn-sm">
                    <i class="feather-plus me-1"></i> New Leave Request
                </a>
            </div>
            <div class="card-body">

                {{-- Statistics Cards --}}
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card card-statistic">
                            <div class="card-body">
                                <div class="stat-label">Total Requests</div>
                                <h3 class="mb-0">{{ $totalLeaves }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-statistic">
                            <div class="card-body">
                                <div class="stat-label" style="color:#d97706;">Pending</div>
                                <h3 class="mb-0" style="color:#d97706;">{{ $pendingLeaves }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-statistic">
                            <div class="card-body">
                                <div class="stat-label" style="color:#16a34a;">Approved</div>
                                <h3 class="mb-0" style="color:#16a34a;">{{ $approvedLeaves }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-statistic">
                            <div class="card-body">
                                <div class="stat-label" style="color:#e11d48;">Rejected</div>
                                <h3 class="mb-0" style="color:#e11d48;">{{ $rejectedLeaves }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Filters --}}
                <form method="GET" action="{{ route('leave.index') }}" class="d-flex gap-2 flex-wrap mb-3">
                    <input type="text" name="search" class="form-control form-control-sm"
                        placeholder="Search by employee..." value="{{ request('search') }}"
                        style="flex:1; min-width:200px;">
                    <select name="status" class="form-control form-control-sm" style="max-width:130px;">
                        <option value="all">All Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                    <select name="leave_type" class="form-control form-control-sm" style="max-width:150px;">
                        <option value="all">All Types</option>
                        @foreach ($leaveTypeStats as $stat)
                            <option value="{{ $stat->leave_type }}"
                                {{ request('leave_type') === $stat->leave_type ? 'selected' : '' }}>
                                {{ $stat->leave_type }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-sm btn-outline-secondary">Filter</button>
                </form>

            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover w-100 mb-0">
                        <thead>
                            <tr>
                                @php
                                    $cols = [
                                        'user_id' => 'Employee',
                                        'leave_type' => 'Type',
                                        'start_date' => 'Duration',
                                        'status' => 'Status',
                                    ];
                                @endphp
                                @foreach ($cols as $col => $label)
                                    <th class="sortable-header">
                                        <a href="{{ route('leave.index', array_merge(request()->all(), ['sort_by' => $col, 'sort_order' => $sortBy === $col && $sortOrder === 'asc' ? 'desc' : 'asc'])) }}"
                                            class="sort-link">
                                            {{ $label }}
                                            @if ($sortBy === $col)
                                                <i class="feather-arrow-{{ $sortOrder === 'asc' ? 'up' : 'down' }} ms-1"
                                                    style="font-size:0.875rem;"></i>
                                            @else
                                                <i class="feather-arrow-up-down ms-1"
                                                    style="font-size:0.875rem; opacity:.3;"></i>
                                            @endif
                                        </a>
                                    </th>
                                @endforeach
                                <th class="sortable-header text-center">
                                    <div class="sort-link justify-content-center">Actions</div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leaves as $leave)
                                <tr>
                                    <td>
                                        <strong>
                                            {{ $leave->employee->first_name ?? 'N/A' }}
                                            {{ $leave->employee->last_name ?? '' }}
                                        </strong>
                                    </td>
                                    <td>{{ $leave->leave_type }}</td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $leave->start_date->format('M d, Y') }} –
                                            {{ $leave->end_date->format('M d, Y') }}
                                        </small><br>
                                        @php $days = $leave->start_date->diffInDays($leave->end_date) + 1; @endphp
                                        <small class="text-muted">{{ $days }}
                                            day{{ $days != 1 ? 's' : '' }}</small>
                                    </td>
                                    <td>
                                        @php
                                            $statusMap = [
                                                'pending' => [
                                                    'bg' => '#fffbeb',
                                                    'color' => '#d97706',
                                                    'border' => '#fde68a',
                                                ],
                                                'approved' => [
                                                    'bg' => '#f0fdf4',
                                                    'color' => '#16a34a',
                                                    'border' => '#bbf7d0',
                                                ],
                                                'rejected' => [
                                                    'bg' => '#fff1f2',
                                                    'color' => '#e11d48',
                                                    'border' => '#fcd0d0',
                                                ],
                                            ];
                                            $st = $statusMap[$leave->status] ?? [
                                                'bg' => '#f4f5f7',
                                                'color' => '#9898a8',
                                                'border' => '#e8e8ef',
                                            ];
                                        @endphp
                                        <span class="emp-badge"
                                            style="background:{{ $st['bg'] }}; color:{{ $st['color'] }}; border:1px solid {{ $st['border'] }};">
                                            {{ ucfirst($leave->status) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="{{ route('leave.show', $leave) }}"
                                                class="emp-action-btn emp-action-view" title="View">
                                                <i class="feather-eye"></i>
                                            </a>
                                            <a href="{{ route('leave.edit', $leave) }}"
                                                class="emp-action-btn emp-action-edit" title="Edit">
                                                <i class="feather-edit-2"></i>
                                            </a>
                                            <form action="{{ route('leave.destroy', $leave) }}" method="POST"
                                                class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="emp-action-btn emp-action-danger"
                                                    title="Delete" onclick="return confirm('Delete this leave request?')">
                                                    <i class="feather-trash-2"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        <i class="feather-file-text d-block mb-2" style="font-size:28px; opacity:.3;"></i>
                                        No leave requests found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($leaves->hasPages())
                    <div class="d-flex justify-content-between align-items-center px-3 py-2 flex-wrap gap-2">

                        {{-- LEFT: Showing text --}}
                        <div class="small text-muted">
                            Showing
                            <strong>{{ $leaves->firstItem() }}</strong>
                            to
                            <strong>{{ $leaves->lastItem() }}</strong>
                            of
                            <strong>{{ $leaves->total() }}</strong>
                            entries
                        </div>

                        {{-- RIGHT: Pagination --}}
                        <div>
                            {{ $leaves->links('pagination::bootstrap-5') }}
                        </div>

                    </div>
                @endif
            </div>
        </div>
    </div>
    @section('scripts')
        <script src="{{ asset('js/HR/leave-management.js') }}"></script>
    @endsection
@endsection
