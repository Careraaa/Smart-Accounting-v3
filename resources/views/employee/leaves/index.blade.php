@extends('layouts.layout')

@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="card-title mb-0">My Leave Requests</span>
                <a href="{{ route('employee.leaves.create') }}" class="btn btn-primary btn-sm">
                    <i class="feather-plus me-1"></i> Request New Leave
                </a>
            </div>
            <div class="card-body">

                {{-- Statistics Cards --}}
                <div class="row mb-4">
                    <div class="col-sm-6 col-md-3">
                        <div class="card card-statistic">
                            <div class="card-body">
                                <div class="stat-label">Total</div>
                                <h3 class="mb-0">{{ $totalLeaves }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="card card-statistic">
                            <div class="card-body">
                                <div class="stat-label" style="color:#d97706;">Pending</div>
                                <h3 class="mb-0" style="color:#d97706;">{{ $pendingLeaves }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="card card-statistic">
                            <div class="card-body">
                                <div class="stat-label" style="color:#16a34a;">Approved</div>
                                <h3 class="mb-0" style="color:#16a34a;">{{ $approvedLeaves }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="card card-statistic">
                            <div class="card-body">
                                <div class="stat-label" style="color:#e11d48;">Rejected</div>
                                <h3 class="mb-0" style="color:#e11d48;">{{ $rejectedLeaves }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Status Filter --}}
                <div class="mb-3 d-flex gap-2 flex-wrap">
                    <a href="{{ route('employee.leaves.index') }}" 
                        class="btn btn-sm {{ $status === 'all' ? 'btn-primary' : 'btn-outline-secondary' }}">
                        All
                    </a>
                    <a href="{{ route('employee.leaves.index', ['status' => 'pending']) }}" 
                        class="btn btn-sm {{ $status === 'pending' ? 'btn-primary' : 'btn-outline-secondary' }}">
                        Pending
                    </a>
                    <a href="{{ route('employee.leaves.index', ['status' => 'approved']) }}" 
                        class="btn btn-sm {{ $status === 'approved' ? 'btn-primary' : 'btn-outline-secondary' }}">
                        Approved
                    </a>
                    <a href="{{ route('employee.leaves.index', ['status' => 'rejected']) }}" 
                        class="btn btn-sm {{ $status === 'rejected' ? 'btn-primary' : 'btn-outline-secondary' }}">
                        Rejected
                    </a>
                </div>

            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover w-100 mb-0">
                        <thead>
                            <tr>
                                <th>Leave Type</th>
                                <th>Duration</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leaves as $leave)
                                <tr>
                                    <td>
                                        <strong>{{ $leave->leave_type }}</strong>
                                    </td>
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
                                            <a href="{{ route('employee.leaves.show', $leave) }}"
                                                class="emp-action-btn emp-action-view" title="View">
                                                <i class="feather-eye"></i>
                                            </a>
                                            @if($leave->status === 'pending')
                                                <a href="{{ route('employee.leaves.edit', $leave) }}"
                                                    class="emp-action-btn emp-action-edit" title="Edit">
                                                    <i class="feather-edit-2"></i>
                                                </a>
                                                <form action="{{ route('employee.leaves.destroy', $leave) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="emp-action-btn emp-action-danger"
                                                        title="Cancel" onclick="return confirm('Cancel this leave request?')">
                                                        <i class="feather-trash-2"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-5">
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
                        <div class="small text-muted">
                            Showing
                            <strong>{{ $leaves->firstItem() }}</strong>
                            to
                            <strong>{{ $leaves->lastItem() }}</strong>
                            of
                            <strong>{{ $leaves->total() }}</strong>
                            entries
                        </div>
                        <div>
                            {{ $leaves->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
