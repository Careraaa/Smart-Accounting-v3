@extends('layouts.layout')

@push('styles')
    @include('remittance-clerk._ui-styles')
@endpush

@section('content')
<div class="col-12">
    <div class="remui-page">

        <div class="prl-topbar">
            <div>
                <h1 class="prl-topbar-title">Edit Driver</h1>
                <p class="prl-topbar-sub">Update driver information and status.</p>
            </div>
            <div class="prl-topbar-actions">
                <a href="{{ route('drivers.show', $driver) }}" class="prl-btn-ghost">
                    <i class="feather-eye"></i> View
                </a>
                <a href="{{ route('drivers.index') }}" class="prl-btn-ghost">
                    <i class="feather-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="prl-detail-card">
            <div class="prl-detail-head">
                <h2 class="prl-detail-title">Driver Details</h2>
            </div>
            <div class="prl-detail-body">
                <form action="{{ route('drivers.update', $driver) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $driver->name) }}" required>
                            @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="license_number" class="form-label">License Number <span class="text-danger">*</span></label>
                            <input type="text" name="license_number" id="license_number"
                                class="form-control @error('license_number') is-invalid @enderror"
                                value="{{ old('license_number', $driver->license_number) }}" required>
                            @error('license_number')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="contact_number" class="form-label">Contact Number <span class="text-danger">*</span></label>
                            <input type="tel" name="contact_number" id="contact_number"
                                class="form-control @error('contact_number') is-invalid @enderror"
                                value="{{ old('contact_number', $driver->contact_number) }}"
                                placeholder="09192846375"
                                inputmode="numeric"
                                autocomplete="tel"
                                maxlength="11"
                                pattern="09\d{9}"
                                data-digits-only
                                required>
                            @error('contact_number')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                            <select name="gender" id="gender" class="form-select" required>
                                <option value="">-- Select --</option>
                                <option value="male" {{ old('gender', $driver->gender) === 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $driver->gender) === 'female' ? 'selected' : '' }}>Female</option>
                                <option value="prefer_not_to_say" {{ old('gender', $driver->gender) === 'prefer_not_to_say' ? 'selected' : '' }}>Prefer not to say</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $driver->email) }}" required>
                            @error('email')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="">-- Select Status --</option>
                                <option value="active" {{ old('status', $driver->status) === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $driver->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="date_of_hire" class="form-label">Date of Hire <span class="text-danger">*</span></label>
                        <input type="date" name="date_of_hire" id="date_of_hire"
                            class="form-control @error('date_of_hire') is-invalid @enderror"
                            value="{{ old('date_of_hire', $driver->date_of_hire?->format('Y-m-d')) }}" required>
                        @error('date_of_hire')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    <div class="mb-4">
                        <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                        <textarea name="address" id="address" class="form-control" rows="3" required>{{ old('address', $driver->address) }}</textarea>
                    </div>

                    <div class="d-flex gap-2 pt-3 border-top">
                        <button type="submit" class="prl-btn-add">Update Driver</button>
                        <a href="{{ route('drivers.index') }}" class="prl-btn-ghost">Cancel</a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

@push('scripts')
@endpush
@endsection
