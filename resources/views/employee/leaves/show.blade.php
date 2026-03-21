@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="card-title mb-0">Leave Request Details</span>
            <div class="d-flex gap-2">
                @if($leave->status === 'pending')
                    <a href="{{ route('employee.leaves.edit', $leave) }}" class="btn btn-primary btn-sm">
                        <i class="feather-edit-2 me-1"></i> Edit
                    </a>
                @endif
                <a href="{{ route('employee.leaves.index') }}" class="btn btn-secondary btn-sm">
                    <i class="feather-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Leave Type</label>
                        <p class="text-muted mb-0">{{ $leave->leave_type }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Start Date</label>
                        <p class="text-muted mb-0">{{ $leave->start_date->format('l, F d, Y') }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Duration</label>
                        <p class="text-muted mb-0">{{ $leave->start_date->diffInDays($leave->end_date) + 1 }} day(s)</p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-4">
                        <label class="form-label fw-bold">End Date</label>
                        <p class="text-muted mb-0">{{ $leave->end_date->format('l, F d, Y') }}</p>
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

                    <div class="mb-4">
                        <label class="form-label fw-bold">Submitted On</label>
                        <p class="text-muted mb-0">{{ $leave->created_at->format('F d, Y \a\t h:i A') }}</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Reason</label>
                        <p class="text-muted mb-0" style="line-height:1.6;">{{ $leave->reason }}</p>
                    </div>
                </div>
            </div>

            @if($leave->status === 'approved' && $leave->approvedBy)
            <div class="row mt-4">
                <div class="col-md-12">
                    <hr>
                    <div class="alert alert-success" role="alert">
                        <i class="feather-check-circle me-2"></i>
                        <strong>Approved</strong> by {{ $leave->approvedBy->name }} on {{ $leave->updated_at->format('F d, Y') }}
                    </div>
                </div>
            </div>
            @elseif($leave->status === 'rejected' && $leave->approvedBy)
            <div class="row mt-4">
                <div class="col-md-12">
                    <hr>
                    <div class="alert alert-danger" role="alert">
                        <i class="feather-x-circle me-2"></i>
                        <strong>Rejected</strong> by {{ $leave->approvedBy->name }} on {{ $leave->updated_at->format('F d, Y') }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
