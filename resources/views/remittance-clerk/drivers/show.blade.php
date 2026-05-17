@extends('layouts.layout')

@push('styles')
    @include('remittance-clerk._ui-styles')
@endpush

@section('content')
<div class="col-12">
    <div class="remui-page">

        <div class="prl-topbar">
            <div>
                <h1 class="prl-topbar-title">{{ $driver->name }}</h1>
                <p class="prl-topbar-sub">Driver profile and current status.</p>
            </div>
            <div class="prl-topbar-actions">
                <a href="{{ route('drivers.edit', $driver) }}" class="prl-btn-ghost">
                    <i class="feather-edit-2"></i> Edit
                </a>
                <form action="{{ route('drivers.destroy', $driver) }}" method="POST" class="d-inline"
                    data-sa-confirm="Are you sure you want to delete this driver?">
                    @csrf @method('DELETE')
                    <button type="submit" class="prl-action-btn danger">
                        <i class="feather-trash-2"></i> Delete
                    </button>
                </form>
                <a href="{{ route('drivers.index') }}" class="prl-btn-ghost">
                    <i class="feather-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="prl-detail-card">
            <div class="prl-detail-head">
                <h2 class="prl-detail-title">Driver Information</h2>
            </div>
            <div class="prl-detail-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <span class="prl-field-label">Name</span>
                        <div class="prl-field-value">{{ $driver->name ?? '—' }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <span class="prl-field-label">License Number</span>
                        <div class="prl-field-value">{{ $driver->license_number ?? '—' }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <span class="prl-field-label">Contact Number</span>
                        <div class="prl-field-value">{{ $driver->contact_number ?? '—' }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <span class="prl-field-label">Email</span>
                        <div class="prl-field-value">{{ $driver->email ?? '—' }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <span class="prl-field-label">Gender</span>
                        <div class="prl-field-value">{{ ucfirst(str_replace('_', ' ', $driver->gender ?? '—')) }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <span class="prl-field-label">Date of Hire</span>
                        <div class="prl-field-value">{{ $driver->date_of_hire?->format('F d, Y') ?? '—' }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <span class="prl-field-label">Status</span>
                        <div class="mt-1">
                            @if ($driver->status === 'active')
                                <span class="prl-status s-active">Active</span>
                            @elseif ($driver->status === 'pending')
                                <span class="prl-status s-pending">Pending</span>
                            @else
                                <span class="prl-status s-inactive">Inactive</span>
                            @endif
                        </div>
                    </div>
                    @if ($driver->address)
                    <div class="col-md-12 mb-3">
                        <span class="prl-field-label">Address</span>
                        <div class="prl-field-value">{{ $driver->address }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
