@extends('layouts.layout')

@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="card-title mb-0">Overtime / Undertime Requests</span>
                <a href="{{ route('employee.overtime-undertime.create') }}" class="btn btn-primary btn-sm">
                    <i class="feather-plus me-1"></i> Submit Request
                </a>
            </div>
            <div class="card-body">

                {{-- Statistics Cards --}}
                <div class="row mb-4">
                    <div class="col-sm-6 col-md-3">
                        <div class="card card-statistic">
                            <div class="card-body">
                                <div class="stat-label">Total Requests</div>
                                <h3 class="mb-0">{{ $totalRequests }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="card card-statistic">
                            <div class="card-body">
                                <div class="stat-label" style="color:#d97706;">Pending</div>
                                <h3 class="mb-0" style="color:#d97706;">{{ $pendingRequests }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="card card-statistic">
                            <div class="card-body">
                                <div class="stat-label" style="color:#16a34a;">Approved</div>
                                <h3 class="mb-0" style="color:#16a34a;">{{ $approvedRequests }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="card card-statistic">
                            <div class="card-body">
                                <div class="stat-label" style="color:#e11d48;">Rejected</div>
                                <h3 class="mb-0" style="color:#e11d48;">{{ $rejectedRequests }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Status Filter --}}
                <div class="mb-3 d-flex gap-2 flex-wrap">
                    <a href="{{ route('employee.overtime-undertime.index') }}" 
                        class="btn btn-sm {{ $status === 'all' ? 'btn-primary' : 'btn-outline-secondary' }}">
                        All
                    </a>
                    <a href="{{ route('employee.overtime-undertime.index', ['status' => 'pending']) }}" 
                        class="btn btn-sm {{ $status === 'pending' ? 'btn-primary' : 'btn-outline-secondary' }}">
                        Pending
                    </a>
                    <a href="{{ route('employee.overtime-undertime.index', ['status' => 'approved']) }}" 
                        class="btn btn-sm {{ $status === 'approved' ? 'btn-primary' : 'btn-outline-secondary' }}">
                        Approved
                    </a>
                    <a href="{{ route('employee.overtime-undertime.index', ['status' => 'rejected']) }}" 
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
                                <th>Date</th>
                                <th>Type</th>
                                <th>Hours</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requests as $request)
                                <tr>
                                    <td>
                                        <strong>{{ $request->date->format('M d, Y') }}</strong>
                                    </td>
                                    <td>
                                        @if($request->type === 'overtime')
                                            <span class="badge bg-info text-dark">Overtime</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Undertime</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ number_format($request->hours, 2) }}h</strong>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ Str::limit($request->reason, 50) }}</small>
                                    </td>
                                    <td>
                                        @php
                                            $statusMap = [
                                                'pending' => ['badge' => 'warning', 'label' => 'Pending'],
                                                'approved' => ['badge' => 'success', 'label' => 'Approved'],
                                                'rejected' => ['badge' => 'danger', 'label' => 'Rejected'],
                                            ];
                                            $status_info = $statusMap[$request->status] ?? ['badge' => 'secondary', 'label' => ucfirst($request->status)];
                                        @endphp
                                        <span class="badge bg-{{ $status_info['badge'] }}">{{ $status_info['label'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('employee.overtime-undertime.show', $request->id) }}" 
                                            class="btn btn-icon btn-sm" title="View">
                                            <i class="feather-eye"></i>
                                        </a>
                                        @if($request->status === 'pending')
                                            <a href="{{ route('employee.overtime-undertime.edit', $request->id) }}" 
                                                class="btn btn-icon btn-sm" title="Edit">
                                                <i class="feather-edit"></i>
                                            </a>
                                            <form action="{{ route('employee.overtime-undertime.destroy', $request->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-icon btn-sm" title="Delete" onclick="return confirm('Are you sure you want to delete this request?')">
                                                    <i class="feather-trash-2 text-danger"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="feather-inbox d-block mb-2" style="font-size:28px; opacity:.3;"></i>
                                        <p class="text-muted mb-0" style="font-size:.845rem;">No overtime/undertime requests found.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($requests->hasPages())
                <div class="card-footer">
                    {{ $requests->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>
@endsection
