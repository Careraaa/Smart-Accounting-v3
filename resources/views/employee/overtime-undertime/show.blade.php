@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="card-title mb-0">Overtime / Undertime Request Details</span>
            <a href="{{ route('employee.overtime-undertime.index') }}" class="btn btn-secondary btn-sm">
                <i class="feather-arrow-left me-1"></i> Back
            </a>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Request Type</label>
                        <div>
                            @if($overtimeUndertime->type === 'overtime')
                                <span class="badge bg-info text-dark" style="font-size: 0.9rem; padding: 0.5rem 0.75rem;">Overtime</span>
                            @else
                                <span class="badge bg-warning text-dark" style="font-size: 0.9rem; padding: 0.5rem 0.75rem;">Undertime</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <div>
                            @php
                                $statusMap = [
                                    'pending' => ['badge' => 'warning', 'label' => 'Pending'],
                                    'approved' => ['badge' => 'success', 'label' => 'Approved'],
                                    'rejected' => ['badge' => 'danger', 'label' => 'Rejected'],
                                ];
                                $status_info = $statusMap[$overtimeUndertime->status] ?? ['badge' => 'secondary', 'label' => ucfirst($overtimeUndertime->status)];
                            @endphp
                            <span class="badge bg-{{ $status_info['badge'] }}" style="font-size: 0.9rem; padding: 0.5rem 0.75rem;">{{ $status_info['label'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Date</label>
                        <div class="text-dark">{{ $overtimeUndertime->date->format('l, F d, Y') }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Hours</label>
                        <div class="text-dark">{{ number_format($overtimeUndertime->hours, 2) }} hours</div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Hourly Rate</label>
                        <div class="text-dark">₱ {{ number_format($overtimeUndertime->hourly_rate_used, 2) }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Amount</label>
                        <div class="text-dark">
                            @if($overtimeUndertime->type === 'overtime')
                                <span class="text-success fw-semibold">+₱ {{ number_format($overtimeUndertime->amount, 2) }}</span>
                            @else
                                <span class="text-danger fw-semibold">-₱ {{ number_format(abs($overtimeUndertime->amount), 2) }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Reason</label>
                        <div class="text-dark">
                            <p class="mb-0" style="white-space: pre-wrap;">{{ $overtimeUndertime->reason }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Submitted By</label>
                        <div class="text-dark">{{ $overtimeUndertime->employee->first_name }} {{ $overtimeUndertime->employee->last_name }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Submitted On</label>
                        <div class="text-dark">{{ $overtimeUndertime->created_at->format('l, F d, Y - g:i A') }}</div>
                    </div>
                </div>
            </div>

            @if($overtimeUndertime->updated_at->diffInSeconds($overtimeUndertime->created_at) > 1)
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Last Updated</label>
                        <div class="text-dark">{{ $overtimeUndertime->updated_at->format('l, F d, Y - g:i A') }}</div>
                    </div>
                </div>
            </div>
            @endif

            <div class="d-flex gap-2">
                @if($overtimeUndertime->status === 'pending')
                    <a href="{{ route('employee.overtime-undertime.edit', $overtimeUndertime->id) }}" class="btn btn-warning">
                        <i class="feather-edit me-1"></i> Edit
                    </a>
                    <form action="{{ route('employee.overtime-undertime.destroy', $overtimeUndertime->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this request? This action cannot be undone.')">
                            <i class="feather-trash-2 me-1"></i> Delete
                        </button>
                    </form>
                @endif
                <a href="{{ route('employee.overtime-undertime.index') }}" class="btn btn-secondary">
                    <i class="feather-arrow-left me-1"></i> Back to List
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
