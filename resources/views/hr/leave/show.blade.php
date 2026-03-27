@extends('layouts.layout')

@section('content')
<div class="row">
    {{-- Left Column: Details --}}
    <div class="col-md-9">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="card-title mb-0">Leave Request Details</span>
                <a href="{{ route('leave.pending') }}" class="btn btn-sm btn-secondary">
                    <i class="feather-x me-1"></i> Close
                </a>
            </div>
            <div class="card-body">
                {{-- Leave Details Section --}}
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h6 class="text-muted mb-3">
                            <i class="feather-file-text me-2" style="color: #0369a1;"></i> Leave Details
                        </h6>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Employee Name</label>
                            <input type="text" class="form-control" value="{{ $leave->employee->first_name }} {{ $leave->employee->last_name }}" disabled>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Department</label>
                            <input type="text" class="form-control" value="{{ $leave->employee->department ?? 'N/A' }}" disabled>
                        </div>
                    </div>
                    <div class="col-md-6 mt-3">
                        <div class="form-group">
                            <label class="form-label">Leave Type</label>
                            <input type="text" class="form-control" value="{{ $leave->leave_type }}" disabled>
                        </div>
                    </div>
                    <div class="col-md-6 mt-3">
                        <div class="form-group">
                            <label class="form-label">Number of Days</label>
                            <input type="text" class="form-control" value="{{ $leave->start_date->diffInDays($leave->end_date) + 1 }} day(s)" disabled>
                        </div>
                    </div>
                    <div class="col-md-6 mt-3">
                        <div class="form-group">
                            <label class="form-label">Start Date</label>
                            <input type="text" class="form-control" value="{{ $leave->start_date->format('F d, Y') }}" disabled>
                        </div>
                    </div>
                    <div class="col-md-6 mt-3">
                        <div class="form-group">
                            <label class="form-label">End Date</label>
                            <input type="text" class="form-control" value="{{ $leave->end_date->format('F d, Y') }}" disabled>
                        </div>
                    </div>
                    <div class="col-md-12 mt-3">
                        <div class="form-group">
                            <label class="form-label">Reason/Remarks</label>
                            <textarea class="form-control" rows="4" disabled>{{ $leave->reason }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Right Column: Action Sidebar --}}
    <div class="col-md-3">
        {{-- Status Alert --}}
        @if($leave->status === 'approved')
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <h6 class="mb-2"><i class="feather-check-circle me-2"></i> <strong>Approved</strong></h6>
                <p class="mb-1"><small>By: <strong>{{ $leave->approvedBy->first_name ?? 'Admin' }}</strong></small></p>
                <p class="mb-0"><small>Date: <strong>{{ $leave->updated_at->format('M d, Y') }}</strong></small></p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @elseif($leave->status === 'rejected')
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h6 class="mb-2"><i class="feather-alert-circle me-2"></i> <strong>Rejected</strong></h6>
                <p class="mb-1"><small>By: <strong>{{ $leave->approvedBy->first_name ?? 'Admin' }}</strong></small></p>
                <p class="mb-1"><small>Date: <strong>{{ $leave->updated_at->format('M d, Y') }}</strong></small></p>
                @if($leave->rejection_reason)
                <hr class="my-2">
                <p class="mb-0"><small><strong>Reason:</strong> {{ $leave->rejection_reason }}</small></p>
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Action Cards --}}
        @if($leave->status === 'pending')
            {{-- Approve Card --}}
            <div class="card border-success mb-3">
                <div class="card-body">
                    <h6 class="card-title text-success mb-3">
                        <i class="feather-check me-2"></i> Approve
                    </h6>
                    <p class="card-text small text-muted mb-3">Approve this leave request.</p>
                    <form action="{{ route('leave.approve', $leave) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm w-100">
                            <i class="feather-check me-2"></i> Approve
                        </button>
                    </form>
                </div>
            </div>

            {{-- Reject Card --}}
            <div class="card border-danger">
                <div class="card-body">
                    <h6 class="card-title text-danger mb-3">
                        <i class="feather-x me-2"></i> Reject
                    </h6>
                    <form action="{{ route('leave.reject', $leave) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="rejection_reason" class="form-label small">Rejection Reason <span class="text-danger">*</span></label>
                            <textarea class="form-control form-control-sm @error('rejection_reason') is-invalid @enderror" 
                                id="rejection_reason" 
                                name="rejection_reason" 
                                rows="3" 
                                placeholder="Enter reason..."
                                required></textarea>
                            @error('rejection_reason')
                                <small class="text-danger d-block mt-1">{{ $message }}</small>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-danger btn-sm w-100">
                            <i class="feather-x me-2"></i> Reject
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>

@endsection
