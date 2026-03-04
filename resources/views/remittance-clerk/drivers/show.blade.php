@extends('layouts.layout')

@section('content')
<div class="col-md-10 offset-md-1">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-0" style="color:#1c1c1e;">{{ $driver->name }}</h5>
            <span class="emp-view-label">Driver</span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('drivers.edit', $driver) }}" class="emp-action-btn emp-action-edit">
                <i class="feather-edit-2 me-1"></i> Edit
            </a>
            <form action="{{ route('drivers.destroy', $driver) }}" method="POST"
                onsubmit="return confirm('Are you sure you want to delete this driver?')" class="d-inline">
                @csrf @method('DELETE')
                <button type="submit" class="emp-action-btn emp-action-danger">
                    <i class="feather-trash-2 me-1"></i> Delete
                </button>
            </form>
            <a href="{{ route('drivers.index') }}" class="emp-action-btn emp-action-back">
                <i class="feather-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    {{-- Driver Information --}}
    <div class="card mb-3">
        <div class="card-header"><span class="card-title mb-0">Driver Information</span></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <span class="emp-field-label">Name</span>
                    <div class="emp-field-value">{{ $driver->name ?? '—' }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <span class="emp-field-label">License Number</span>
                    <div class="emp-field-value">{{ $driver->license_number ?? '—' }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <span class="emp-field-label">Contact Number</span>
                    <div class="emp-field-value">{{ $driver->contact_number ?? '—' }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <span class="emp-field-label">Email</span>
                    <div class="emp-field-value">{{ $driver->email ?? '—' }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <span class="emp-field-label">Date of Hire</span>
                    <div class="emp-field-value">{{ $driver->date_of_hire?->format('F d, Y') ?? '—' }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <span class="emp-field-label">Status</span>
                    <div class="mt-1">
                        @if ($driver->status === 'active')
                            <span class="emp-badge emp-badge-active">Active</span>
                        @elseif ($driver->status === 'pending')
                            <span class="emp-badge emp-badge-pending">Pending</span>
                        @else
                            <span class="emp-badge emp-badge-inactive">Inactive</span>
                        @endif
                    </div>
                </div>
                @if ($driver->address)
                <div class="col-md-12 mb-3">
                    <span class="emp-field-label">Address</span>
                    <div class="emp-field-value">{{ $driver->address }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>

</div>

<style>
.emp-action-btn {
    height: 30px;
    padding: 0 12px;
    font-size: 0.815rem;
    font-weight: 500;
    white-space: nowrap;
    width: auto;
}
</style>
@endsection