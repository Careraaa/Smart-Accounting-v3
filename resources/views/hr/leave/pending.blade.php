@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div>
                <span class="card-title mb-0">Pending Leaves</span>
                <p class="text-muted small mt-1 mb-0">Review and approve leave requests</p>
            </div>
        </div>
        <div class="card-body">

            {{-- Statistics Cards --}}
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card card-statistic">
                        <div class="card-body">
                            <div class="stat-label">Total Pending</div>
                            <h3 class="mb-1" style="color: #9f1239;">{{ $pendingLeaves }}</h3>
                            <small class="text-muted">Not approved</small>
                        </div>
                        <div class="card-icon" style="color: #f43f5e; opacity: 0.2;">
                            <i class="feather-alert-circle" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-statistic">
                        <div class="card-body">
                            <div class="stat-label">This Week</div>
                            <h3 class="mb-1" style="color: #ca8a04;">{{ $thisWeekLeaves }}</h3>
                            <small class="text-muted">Pending requests</small>
                        </div>
                        <div class="card-icon" style="color: #eab308; opacity: 0.2;">
                            <i class="feather-calendar" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-statistic">
                        <div class="card-body">
                            <div class="stat-label">This Month</div>
                            <h3 class="mb-1" style="color: #be123c;">{{ $thisMonthLeaves }}</h3>
                            <small class="text-muted">Total requests</small>
                        </div>
                        <div class="card-icon" style="color: #f43f5e; opacity: 0.2;">
                            <i class="feather-calendar" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filters --}}
            <form method="GET" action="{{ route('leave.index') }}" class="d-flex gap-2 flex-wrap mb-3">
                <input type="hidden" name="status" value="pending">
                
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
                            <th style="width: 25%;">EMPLOYEE</th>
                            <th style="width: 15%;">DEPARTMENT</th>
                            <th style="width: 15%;">LEAVE TYPE</th>
                            <th style="width: 18%;">DATES</th>
                            <th style="width: 10%;">DAYS</th>
                            <th style="width: 12%;">APPLIED</th>
                            <th style="width: 10%;">ACTIONS</th>
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
                                            <strong>{{ $leave->employee->first_name ?? 'N/A' }} {{ $leave->employee->last_name ?? '' }}</strong><br>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $leave->employee->department ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge" style="background-color: #fee2e2; color: #be123c;">
                                        {{ $leave->leave_type }}
                                    </span>
                                </td>
                                <td>
                                    <small>{{ $leave->start_date->format('M d, Y') }} –<br>{{ $leave->end_date->format('M d, Y') }}</small>
                                </td>
                                <td>
                                    @php $days = $leave->start_date->diffInDays($leave->end_date) + 1; @endphp
                                    <strong>{{ $days }} day{{ $days != 1 ? 's' : '' }}</strong>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $leave->created_at->format('M d, Y') }}</small>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('leave.show', $leave) }}" class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="feather-eye"></i>
                                        </a>
                                        <form action="{{ route('leave.approve', $leave) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="Approve" onclick="return confirm('Approve this leave?')">
                                                <i class="feather-check"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('leave.reject', $leave) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger" title="Reject" onclick="return confirm('Reject this leave?')">
                                                <i class="feather-x"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="feather-inbox d-block mb-2" style="font-size: 2rem; opacity: 0.3;"></i>
                                    No pending leave requests found
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
    border-left: 4px solid #f43f5e;
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
