@extends('layouts.layout')

@section('content')
<div class="col-md-8 offset-md-2">
    <div class="card">
        <div class="card-header">
            <span class="card-title mb-0">Create Vehicle</span>
        </div>
        <div class="card-body">
            <form action="{{ route('vehicles.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label for="plate_number" class="form-label">Plate Number <span class="text-danger">*</span></label>
                        <input type="text" name="plate_number" id="plate_number"
                            class="form-control @error('plate_number') is-invalid @enderror"
                            value="{{ old('plate_number') }}" placeholder="e.g., ABC-1234" required>
                        @error('plate_number')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="operator" class="form-label">Operator <span class="text-danger">*</span></label>
                        <input type="text" name="operator" id="operator"
                            class="form-control @error('operator') is-invalid @enderror"
                            value="{{ old('operator') }}" placeholder="e.g., John Doe" required>
                        @error('operator')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label for="origin" class="form-label">Origin <span class="text-danger">*</span></label>
                        <input type="text" name="origin" id="origin"
                            class="form-control @error('origin') is-invalid @enderror"
                            value="{{ old('origin') }}" placeholder="e.g., Marikina" required>
                        @error('origin')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="destination" class="form-label">Destination <span class="text-danger">*</span></label>
                        <input type="text" name="destination" id="destination"
                            class="form-control @error('destination') is-invalid @enderror"
                            value="{{ old('destination') }}" placeholder="e.g., Cubao" required>
                        @error('destination')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label for="boundary" class="form-label">Boundary</label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" name="boundary" id="boundary" step="0.01" min="0"
                                class="form-control @error('boundary') is-invalid @enderror"
                                value="{{ old('boundary') }}" placeholder="0.00">
                            @error('boundary')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="">-- Select Status --</option>
                            <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="under_maintenance" {{ old('status') === 'under_maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                        </select>
                        @error('status')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="d-flex gap-2 pt-3 border-top">
                    <button type="submit" class="btn btn-primary btn-sm">Create Vehicle</button>
                    <a href="{{ route('vehicles.index') }}" class="btn btn-secondary btn-sm">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('js/global/global-datepicker.js') }}"></script>
@endpush
@endsection