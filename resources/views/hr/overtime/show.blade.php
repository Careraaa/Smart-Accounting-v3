@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="card-title mb-0">Overtime / Undertime Record Details</span>
            <div class="d-flex gap-2">
                <a href="{{ route('overtime.edit', $overtime) }}" class="btn btn-primary btn-sm">
                    <i class="feather-edit-2 me-1"></i> Edit
                </a>
                <a href="{{ route('overtime.index') }}" class="btn btn-secondary btn-sm">
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
                            {{ $overtime->employee->first_name ?? 'N/A' }} {{ $overtime->employee->last_name ?? '' }}
                        </p>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Type</label>
                        <p class="mb-0">
                            @php
                                $typeColor = $overtime->type === 'overtime' ? '#3b82f6' : '#f59e0b';
                                $typeBg = $overtime->type === 'overtime' ? '#dbeafe' : '#fef3c7';
                            @endphp
                            <span style="display:inline-block; background:{{ $typeBg }}; color:{{ $typeColor }}; font-size:0.75rem; font-weight:700; padding:4px 12px; border-radius:20px; letter-spacing:0.4px; text-transform:uppercase;">
                                {{ ucfirst($overtime->type) }}
                            </span>
                        </p>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Date</label>
                        <p class="text-muted mb-0">{{ $overtime->date->format('F d, Y') }}</p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Hours</label>
                        <p class="text-muted mb-0">{{ number_format($overtime->hours, 2) }} hours</p>
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
                                $st = $statusMap[$overtime->status] ?? ['bg' => '#f4f5f7', 'color' => '#9898a8'];
                            @endphp
                            <span style="display:inline-block; background:{{ $st['bg'] }}; color:{{ $st['color'] }}; font-size:0.75rem; font-weight:700; padding:4px 12px; border-radius:20px; letter-spacing:0.4px; text-transform:uppercase;">
                                {{ ucfirst($overtime->status) }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Reason</label>
                        <p class="text-muted mb-0">{{ $overtime->reason }}</p>
                    </div>
                </div>
            </div>

            @if($overtime->status === 'pending')
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex gap-2">
                        <form action="{{ route('overtime.approve', $overtime) }}" method="POST" class="d-inline">
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
        </div>
    </div>
</div>

@if($overtime->status === 'pending')
<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('overtime.reject', $overtime) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="rejectModalLabel">Reject Record</h1>
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
