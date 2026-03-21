@extends('layouts.layout')

@section('content')
<div class="col-md-8 offset-md-2">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="card-title mb-0">Add Leave Type</span>
            <a href="{{ route('leave-type.index') }}" class="btn btn-secondary btn-sm">
                <i class="feather-arrow-left me-1"></i> Back
            </a>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="feather-alert-circle me-2"></i>
                    <strong>Please fix the errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('leave-type.store') }}" class="needs-validation" novalidate>
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Leave Type Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" placeholder="e.g., Vacation Leave"
                                   value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="abbreviation" class="form-label">Abbreviation</label>
                            <input type="text" class="form-control @error('abbreviation') is-invalid @enderror" 
                                   id="abbreviation" name="abbreviation" placeholder="e.g., VL"
                                   value="{{ old('abbreviation') }}" maxlength="5">
                            @error('abbreviation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="days_allowed" class="form-label">Days Allowed <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('days_allowed') is-invalid @enderror" 
                                   id="days_allowed" name="days_allowed" placeholder="0"
                                   value="{{ old('days_allowed') }}" min="0" required>
                            @error('days_allowed')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                <option value="">Select Status</option>
                                <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="carry_over" class="form-label">Carry Over Settings</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input @error('carry_over') is-invalid @enderror" 
                               type="checkbox" id="carry_over" name="carry_over" value="1"
                               {{ old('carry_over') ? 'checked' : '' }}>
                        <label class="form-check-label" for="carry_over">
                            Allow unused days to carry over to next period
                        </label>
                    </div>
                    @error('carry_over')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="4" 
                              placeholder="Enter description...">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="feather-save me-1"></i> Save Leave Type
                    </button>
                    <a href="{{ route('leave-type.index') }}" class="btn btn-secondary">
                        <i class="feather-x me-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
