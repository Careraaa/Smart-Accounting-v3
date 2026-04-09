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
                        <label for="route_id" class="form-label">Route <span class="text-danger">*</span></label>
                        <select name="route_id" id="route_id"
                            class="form-select @error('route_id') is-invalid @enderror" required>
                            <option value="">-- Select Route --</option>
                            @foreach($routes as $route)
                                <option value="{{ $route->id }}" data-boundary="{{ $route->boundary }}">
                                    {{ $route->route_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('route_id')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="boundary_display" class="form-label">Boundary Rate</label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="text" id="boundary_display"
                                class="form-control" placeholder="0.00" readonly>
                        </div>
                    </div>
                </div>

                <div class="row">
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const routeSelect = document.getElementById('route_id');
            const boundaryDisplay = document.getElementById('boundary_display');

            // Update boundary when route is selected
            routeSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const boundary = selectedOption.getAttribute('data-boundary');
                
                if (boundary && boundary !== '' && boundary !== 'null') {
                    boundaryDisplay.value = parseFloat(boundary).toFixed(2);
                } else {
                    boundaryDisplay.value = '0.00';
                }
            });

            // Trigger change event on page load if a route is already selected
            if (routeSelect.value) {
                routeSelect.dispatchEvent(new Event('change'));
            }
        });
    </script>
@endpush
@endsection