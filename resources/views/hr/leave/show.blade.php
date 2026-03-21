@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="card-title mb-0">Leave Request Details</span>
            <div class="d-flex gap-2">
                <a href="{{ route('leave.edit', $leave) }}" class="btn btn-primary btn-sm">
                    <i class="feather-edit-2 me-1"></i> Edit
                </a>
                <a href="{{ route('leave.index') }}" class="btn btn-secondary btn-sm">
                    <i class="feather-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Employee</label>
                        <p class="text-muted mb-0">
                            {{ $leave->employee->first_name ?? 'N/A' }} {{ $leave->employee->last_name ?? '' }}
                        </p>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Leave Type</label>
                        <p class="text-muted mb-0">{{ $leave->leave_type }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Start Date</label>
                        <p class="text-muted mb-0">{{ $leave->start_date->format('F d, Y') }}</p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-4">
                        <label class="form-label fw-bold">End Date</label>
                        <p class="text-muted mb-0">{{ $leave->end_date->format('F d, Y') }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Duration</label>
                        <p class="text-muted mb-0">{{ $leave->start_date->diffInDays($leave->end_date) + 1 }} day(s)</p>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Status</label>
                        <p class="mb-0">
                            @php
                                $statusMap = [
                                    'pending'  => ['bg' => '#fffbeb', 'color' => '#d97706'],
                                    'approved' => ['bg' => '#f0fdf4', 'color' => '#16a34a'],
                                    'rejected' => ['bg' => '#fff1f2', 'color' => '#e11d48'],
                                ];
                                $st = $statusMap[$leave->status] ?? ['bg' => '#f4f5f7', 'color' => '#9898a8'];
                            @endphp
                            <span style="display:inline-block; background:{{ $st['bg'] }}; color:{{ $st['color'] }}; font-size:0.75rem; font-weight:700; padding:4px 12px; border-radius:20px; letter-spacing:0.4px; text-transform:uppercase;">
                                {{ ucfirst($leave->status) }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Reason</label>
                        <p class="text-muted mb-0">{{ $leave->reason }}</p>
                    </div>
                </div>
            </div>

            @if($leave->status === 'pending')
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex gap-2">
                        <form action="{{ route('leave.approve', $leave) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="feather-check me-1"></i> Approve
                            </button>
                        </form>
                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="feather-x me-1"></i> Reject
                        </button>
                    </div>
                </div>
            </div>
            @endif

            @if($leave->approved_by)
            <div class="row mt-4">
                <div class="col-md-12">
                    <hr>
                    @if($leave->status === 'approved')
                    <div class="mb-4">
                        <label class="form-label fw-bold">Approved By</label>
                        <div class="d-flex align-items-center gap-2">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white" style="width: 40px; height: 40px; font-size: 14px; font-weight: normal; min-width: 40px;">
                                {{ substr($leave->approvedBy->first_name ?? 'U', 0, 1) }}{{ substr($leave->approvedBy->last_name ?? '', 0, 1) }}
                            </div>
                            <div>
                                <p class="mb-0"><strong>{{ $leave->approvedBy->first_name ?? '' }} {{ $leave->approvedBy->last_name ?? '' }}</strong></p>
                                <small class="text-muted">{{ strtoupper(str_replace('_', ' ', $leave->approvedBy->role)) }}</small>
                            </div>
                        </div>
                    </div>
                    @elseif($leave->status === 'rejected')
                    <div class="mb-4">
                        <label class="form-label fw-bold">Rejected By</label>
                        <div class="d-flex align-items-center gap-2">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger text-white" style="width: 40px; height: 40px; font-size: 14px; font-weight: normal; min-width: 40px;">
                                {{ substr($leave->approvedBy->first_name ?? 'U', 0, 1) }}{{ substr($leave->approvedBy->last_name ?? '', 0, 1) }}
                            </div>
                            <div>
                                <p class="mb-0"><strong>{{ $leave->approvedBy->first_name ?? '' }} {{ $leave->approvedBy->last_name ?? '' }}</strong></p>
                                <small class="text-muted">{{ strtoupper(str_replace('_', ' ', $leave->approvedBy->role)) }}</small>
                            </div>
                        </div>
                    </div>
                    @if($leave->rejection_reason)
                    <div class="mb-4">
                        <label class="form-label fw-bold">Rejection Reason</label>
                        <p class="text-danger mb-0">{{ $leave->rejection_reason }}</p>
                    </div>
                    @endif
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@if($leave->status === 'pending')
<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('leave.reject', $leave) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="rejectModalLabel">Reject Leave Request</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Reason for Rejection (Optional)</label>
                        <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
