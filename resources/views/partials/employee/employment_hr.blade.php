@php $readOnly = $readOnly ?? false; @endphp

<div class="emp-divider" style="margin-top:0;">Driver's License <span style="font-weight:400;text-transform:none;letter-spacing:0;color:#9ca3af;">(optional)</span></div>
<div class="emp-row cols-2" style="margin-bottom:20px;">
    <div class="emp-field">
        <label class="emp-label">License Number</label>
        <input type="text" name="driver_license_number" id="driver_license_number"
            class="emp-input"
            placeholder="e.g. N01-12-345678"
            value="{{ old('driver_license_number', $employee->driver_license_number ?? '') }}"
            {{ $readOnly ? 'readonly' : '' }}>
    </div>
    <div class="emp-field">
        <label class="emp-label">License Validity</label>
        <input type="date" name="driver_license_validity" id="driver_license_validity"
            class="emp-input"
            value="{{ old('driver_license_validity', isset($employee->driver_license_validity) ? $employee->driver_license_validity?->format('Y-m-d') : '') }}"
            {{ $readOnly ? 'readonly' : '' }}>
    </div>
</div>