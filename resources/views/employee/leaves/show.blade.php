@extends('layouts.layout')

@push('styles')
    @include('employee._ui-styles')
@endpush

@section('content')
@php
    $pill = in_array($leave->status, ['pending','approved','rejected'], true) ? $leave->status : 'neutral';
@endphp

<div class="container-fluid empui-page empui-wrap">
    <div class="empui-backdrop"><div class="empui-grid"></div></div>
    <div class="empui-content">

        <div class="empui-hero">
            <div class="empui-hero-left">
                <h1 class="empui-title">Leave Request Details</h1>
                <p class="empui-sub">{{ $leave->leaveType?->name ?? 'N/A' }} · {{ $leave->start_date->format('M d, Y') }} – {{ $leave->end_date->format('M d, Y') }}</p>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    <span class="empui-chip"><i class="feather-clock"></i> Submitted {{ $leave->created_at->diffForHumans() }}</span>
                    <span class="empui-chip"><i class="feather-flag"></i> Status: {{ ucfirst($leave->status) }}</span>
                </div>
            </div>
            <div class="empui-hero-right">
                @if($leave->status === 'pending')
                    <a class="empui-btn" href="{{ route('employee.leaves.edit', $leave) }}">
                        <i class="feather-edit-2"></i>
                        Edit Request
                    </a>
                @endif
                <a class="empui-btn-sec" href="{{ route('employee.leaves.index') }}">
                    <i class="feather-arrow-left"></i>
                    Back
                </a>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-8">
                <div class="empui-card">
                    <div class="empui-card-head">
                        <p class="empui-card-title"><span class="empui-dot"></span> Summary</p>
                        <span class="empui-pill {{ $pill }}">{{ ucfirst($leave->status) }}</span>
                    </div>
                    <div class="empui-card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="empui-muted">Leave Type</div>
                                <div class="fw-bold" style="color:#111827;">{{ $leave->leaveType?->name ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="empui-muted">Submitted On</div>
                                <div class="fw-bold" style="color:#111827;">{{ $leave->created_at->format('F d, Y \a\t h:i A') }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="empui-muted">Start Date</div>
                                <div class="fw-bold" style="color:#111827;">{{ $leave->start_date->format('l, F d, Y') }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="empui-muted">End Date</div>
                                <div class="fw-bold" style="color:#111827;">{{ $leave->end_date->format('l, F d, Y') }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="empui-muted">Duration</div>
                                <div class="fw-bold" style="color:#111827;">
                                    {{ $leave->days }} day(s)
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="empui-muted mb-1">Reason</div>
                        <div style="color:#111827; font-weight:700; line-height:1.7; white-space:pre-wrap;">{{ $leave->reason }}</div>

                        @if($leave->status === 'approved' && $leave->approvedBy)
                            <div class="alert alert-success mt-4 mb-0" role="alert">
                                <i class="feather-check-circle me-2"></i>
                                <strong>Approved</strong> by {{ $leave->approvedBy->name }} on {{ $leave->updated_at->format('F d, Y') }}
                            </div>
                        @elseif($leave->status === 'rejected' && $leave->approvedBy)
                            <div class="alert alert-danger mt-4 mb-0" role="alert">
                                <i class="feather-x-circle me-2"></i>
                                <strong>Rejected</strong> by {{ $leave->approvedBy->name }} on {{ $leave->updated_at->format('F d, Y') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="empui-card">
                    <div class="empui-card-head">
                        <p class="empui-card-title"><span class="empui-dot"></span> Actions</p>
                    </div>
                    <div class="empui-card-body">
                        <div class="d-grid gap-2">
                            @if($leave->status === 'pending')
                                <a href="{{ route('employee.leaves.edit', $leave) }}" class="empui-btn-sec" style="justify-content:center;">
                                    <i class="feather-edit-2"></i>
                                    Edit Request
                                </a>
                                <form action="{{ route('employee.leaves.destroy', $leave) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="empui-btn-sec w-100"
                                        style="justify-content:center;border-color:#fecdd3;color:#e11d48;background:#fff1f2;"
                                        onclick="return confirm('Cancel this leave request?')">
                                        <i class="feather-trash-2"></i>
                                        Cancel Request
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('employee.leaves.index') }}" class="empui-btn" style="justify-content:center;">
                                <i class="feather-list"></i>
                                View All Requests
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
