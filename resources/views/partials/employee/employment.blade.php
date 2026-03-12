@php $readOnly = $readOnly ?? false; @endphp

<h5 class="mb-5">Additional Information</h5>

<hr class="my-5">
<h6 class="mb-4">Driver's License <span class="text-muted fw-normal" style="font-size:0.85rem;">(optional)</span></h6>

<div class="row">
    <div class="col-lg-6 mb-4">
        <label for="driver_license_number" class="form-label">License Number</label>
        <input type="text" name="driver_license_number" id="driver_license_number"
            class="form-control" placeholder="e.g. N01-12-345678"
            value="{{ old('driver_license_number', $employee->driver_license_number ?? '') }}"
            {{ $readOnly ? 'readonly' : '' }}>
    </div>
    <div class="col-lg-6 mb-4">
        <label for="driver_license_validity" class="form-label">License Validity</label>
        <input type="date" name="driver_license_validity" id="driver_license_validity"
            class="form-control"
            value="{{ old('driver_license_validity', isset($employee->driver_license_validity) ? $employee->driver_license_validity?->format('Y-m-d') : '') }}"
            {{ $readOnly ? 'readonly' : '' }}>
    </div>
</div>