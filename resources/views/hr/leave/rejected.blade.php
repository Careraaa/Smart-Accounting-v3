@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div>
                <span class="card-title mb-0">Rejected Leaves</span>
                <p class="text-muted small mt-1 mb-0">View rejected leave requests</p>
            </div>
        </div>
        <div class="card-body">

            {{-- Statistics Cards --}}
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card card-statistic">
                        <div class="card-body">
                            <div class="stat-label">Total Rejected</div>
                            <h3 class="mb-1" style="color: #dc2626;">{{ $rejectedLeaves }}</h3>
                            <small class="text-muted">Rejected leaves</small>
                        </div>
                        <div class="card-icon" style="color: #ef4444; opacity: 0.2;">
                            <i class="feather-x-circle" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-statistic">
                        <div class="card-body">
                            <div class="stat-label">This Week</div>
                            <h3 class="mb-1" style="color: #b91c1c;">{{ $thisWeekLeaves }}</h3>
                            <small class="text-muted">Rejected requests</small>
                        </div>
                        <div class="card-icon" style="color: #f87171; opacity: 0.2;">
                            <i class="feather-calendar" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-statistic">
                        <div class="card-body">
                            <div class="stat-label">This Month</div>
                            <h3 class="mb-1" style="color: #991b1b;">{{ $thisMonthLeaves }}</h3>
                            <small class="text-muted">Total rejected</small>
                        </div>
                        <div class="card-icon" style="color: #fca5a5; opacity: 0.2;">
                            <i class="feather-calendar" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filters --}}
            <form method="GET" action="{{ route('leave.pending') }}" class="d-flex gap-2 flex-wrap mb-3">
                <input type="hidden" name="status" value="rejected">
                
                <select name="department" class="form-control form-control-sm" style="flex: 1; min-width: 150px;">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept }}" {{ request('department') === $dept ? 'selected' : '' }}>
                            {{ $dept }}
                        </option>
                    @endforeach
                </select>

                <select name="leave_type" class="form-control form-control-sm" style="flex: 1; min-width: 150px;">
                    <option value="">All Leave Types</option>
                    @foreach ($leaveTypeStats as $stat)
                        <option value="{{ $stat->leave_type }}"
                            {{ request('leave_type') === $stat->leave_type ? 'selected' : '' }}>
                            {{ $stat->leave_type }}
                        </option>
                    @endforeach
                </select>

                <input type="text" name="search" class="form-control form-control-sm"
                    placeholder="Search employee..." value="{{ request('search') }}"
                    style="flex: 1; min-width: 200px;">
                
                <button type="submit" class="btn btn-sm btn-outline-secondary">
                    <i class="feather-search me-1"></i> Filter
                </button>
            </form>

            {{-- Alert Messages --}}
            @if ($message = Session::get('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="feather-check-circle me-2"></i>
                    {{ $message }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover w-100 mb-0">
                    <thead>
                        <tr>
                            <th style="width: 13%; white-space: nowrap;">EMPLOYEE</th>
                            <th style="width: 10%; white-space: nowrap;">DEPARTMENT</th>
                            <th style="width: 11%; white-space: nowrap;">LEAVE TYPE</th>
                            <th style="width: 13%; white-space: nowrap;">REQUESTED DATES</th>
                            <th style="width: 6%; white-space: nowrap;">DAYS</th>
                            <th style="width: 10%; white-space: nowrap;">REJECTED BY</th>
                            <th style="width: 12%; white-space: nowrap;">REJECTED DATE</th>
                            <th style="width: 18%; white-space: nowrap;">REJECTION REASON</th>
                            <th style="width: 7%; white-space: nowrap;">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaves as $leave)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white" style="width: 35px; height: 35px; font-size: 14px; font-weight: normal; min-width: 35px;">
                                            {{ substr($leave->employee->first_name ?? 'U', 0, 1) }}{{ substr($leave->employee->last_name ?? '', 0, 1) }}
                                        </div>
                                        <div>
                                            <small><strong>{{ $leave->employee->first_name ?? 'N/A' }} {{ $leave->employee->last_name ?? '' }}</strong></small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $leave->employee->department ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge" style="background-color: #fef2f2; color: #dc2626;">
                                        {{ $leave->leave_type }}
                                    </span>
                                </td>
                                <td>
                                    <small>{{ $leave->start_date->format('M d, Y') }} – {{ $leave->end_date->format('M d, Y') }}</small>
                                </td>
                                <td>
                                    @php $days = $leave->start_date->diffInDays($leave->end_date) + 1; @endphp
                                    <strong>{{ $days }} day{{ $days != 1 ? 's' : '' }}</strong>
                                </td>
                                <td>
                                    @if($leave->approvedBy)
                                    <div class="d-flex align-items-center gap-2">
                                        <div>
                                            <small><strong>{{ strtoupper(str_replace('_', ' ', $leave->approvedBy->role)) }}</strong></small>
                                        </div>
                                    </div>
                                    @else
                                    <small class="text-muted">N/A</small>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ $leave->updated_at->format('M d, Y') }}</small>
                                </td>
                                <td>
                                    <small class="text-danger" title="{{ $leave->rejection_reason }}">{{ Str::limit($leave->rejection_reason ?? 'N/A', 20) }}</small>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('leave.show', $leave) }}" class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="feather-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">
                                    <i class="feather-inbox d-block mb-2" style="font-size: 2rem; opacity: 0.3;"></i>
                                    No rejected leave requests found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if($leaves->hasPages())
            <div class="card-footer">
                {{ $leaves->links() }}
            </div>
        @endif
    </div>
</div>

<style>
.card-statistic {
    border-left: 4px solid #ef4444;
    position: relative;
}

.card-icon {
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
}

.avatar-sm {
    width: 40px;
    height: 40px;
    font-size: 0.875rem;
}
</style>
@endsection
