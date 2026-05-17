@extends('layouts.layout')

@push('styles')
    @include('remittance-clerk._ui-styles')
@endpush

@section('content')
<div class="col-12">
    <div class="remui-page">

        <div class="prl-topbar">
            <div>
                <h1 class="prl-topbar-title">Create Vehicle</h1>
                <p class="prl-topbar-sub">Register a vehicle and link it to a route for boundary tracking.</p>
            </div>
            <div class="prl-topbar-actions">
                <a href="{{ route('vehicles.index') }}" class="prl-btn-ghost">
                    <i class="feather-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="prl-detail-card">
            <div class="prl-detail-head">
                <h2 class="prl-detail-title">Vehicle Details</h2>
            </div>
            <div class="prl-detail-body">
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
                            <select name="operator" id="operator"
                                class="form-select @error('operator') is-invalid @enderror" required>
                                <option value="">-- Select Operator --</option>
                                @foreach($operators as $operator)
                                    <option value="{{ $operator }}" @selected(old('operator') === $operator)>{{ $operator }}</option>
                                @endforeach
                            </select>
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
                        <button type="submit" class="prl-btn-add">Create Vehicle</button>
                        <a href="{{ route('vehicles.index') }}" class="prl-btn-ghost">Cancel</a>
                    </div>
                </form>
            </div>
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
