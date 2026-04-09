@extends('layouts.layout')

@push('styles')
    @include('employee._ui-styles')
@endpush

@section('content')
@php
    $pill = in_array($overtimeUndertime->status, ['pending','approved','rejected'], true) ? $overtimeUndertime->status : 'neutral';
@endphp

<div class="container-fluid empui-page empui-wrap">
    <div class="empui-backdrop"><div class="empui-grid"></div></div>
    <div class="empui-content">

        <div class="empui-hero">
            <div class="empui-hero-left">
                <h1 class="empui-title">OT / UT Request Details</h1>
                <p class="empui-sub">{{ ucfirst($overtimeUndertime->type) }} · {{ $overtimeUndertime->date->format('M d, Y') }} · {{ number_format($overtimeUndertime->hours, 2) }}h</p>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    <span class="empui-chip"><i class="feather-calendar"></i> {{ $overtimeUndertime->date->format('l') }}</span>
                    <span class="empui-chip"><i class="feather-flag"></i> Status: {{ ucfirst($overtimeUndertime->status) }}</span>
                </div>
            </div>
            <div class="empui-hero-right">
                <a class="empui-btn-sec" href="{{ route('employee.overtime-undertime.index') }}">
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
                        <span class="empui-pill {{ $pill }}">{{ ucfirst($overtimeUndertime->status) }}</span>
                    </div>
                    <div class="empui-card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="empui-muted">Request Type</div>
                                <div class="fw-bold" style="color:#111827;">{{ ucfirst($overtimeUndertime->type) }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="empui-muted">Date</div>
                                <div class="fw-bold" style="color:#111827;">{{ $overtimeUndertime->date->format('l, F d, Y') }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="empui-muted">Hours</div>
                                <div class="fw-bold empui-mono" style="color:#111827;">{{ number_format($overtimeUndertime->hours, 2) }} hours</div>
                            </div>
                            <div class="col-md-6">
                                <div class="empui-muted">Hourly Rate</div>
                                <div class="fw-bold empui-mono" style="color:#111827;">₱ {{ number_format($overtimeUndertime->hourly_rate_used, 2) }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="empui-muted">Amount</div>
                                <div class="fw-bold empui-mono" style="color:#111827;">
                                    @if($overtimeUndertime->type === 'overtime')
                                        <span style="color:#16a34a;">+₱ {{ number_format($overtimeUndertime->amount, 2) }}</span>
                                    @else
                                        <span style="color:#e11d48;">-₱ {{ number_format(abs($overtimeUndertime->amount), 2) }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="empui-muted">Submitted On</div>
                                <div class="fw-bold" style="color:#111827;">{{ $overtimeUndertime->created_at->format('l, F d, Y - g:i A') }}</div>
                            </div>
                            @if($overtimeUndertime->updated_at->diffInSeconds($overtimeUndertime->created_at) > 1)
                                <div class="col-md-6">
                                    <div class="empui-muted">Last Updated</div>
                                    <div class="fw-bold" style="color:#111827;">{{ $overtimeUndertime->updated_at->format('l, F d, Y - g:i A') }}</div>
                                </div>
                            @endif
                        </div>

                        <hr class="my-4">

                        <div class="empui-muted mb-1">Reason</div>
                        <div style="color:#111827; font-weight:700; line-height:1.7; white-space:pre-wrap;">{{ $overtimeUndertime->reason }}</div>

                        <hr class="my-4">

                        <div class="empui-muted">Submitted By</div>
                        <div class="fw-bold" style="color:#111827;">
                            {{ $overtimeUndertime->employee->first_name }} {{ $overtimeUndertime->employee->last_name }}
                        </div>
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
                            @if($overtimeUndertime->status === 'pending')
                                <a href="{{ route('employee.overtime-undertime.edit', $overtimeUndertime->id) }}" class="empui-btn">
                                    <i class="feather-edit-2"></i>
                                    Edit Request
                                </a>
                                <form action="{{ route('employee.overtime-undertime.destroy', $overtimeUndertime->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="empui-btn-sec w-100"
                                        style="justify-content:center;border-color:#fecdd3;color:#e11d48;background:#fff1f2;"
                                        onclick="return confirm('Are you sure you want to delete this request? This action cannot be undone.')">
                                        <i class="feather-trash-2"></i>
                                        Delete
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('employee.overtime-undertime.index') }}" class="empui-btn-sec" style="justify-content:center;">
                                <i class="feather-list"></i>
                                Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
