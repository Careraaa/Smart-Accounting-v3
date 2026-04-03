<h5 class="mb-4">Government Numbers</h5>

@php $readOnly = $readOnly ?? false; @endphp

<div class="row">
    <div class="col-md-6 mb-3">
        <div class="form-check mb-2">
            <input type="checkbox" name="has_sss" id="has_sss" class="form-check-input gov-toggle" data-target="sss_number"
                {{ old('has_sss', $employee->has_sss ?? false) ? 'checked' : '' }}
                {{ $readOnly ? 'disabled' : '' }}>
            <label class="form-check-label fw-semibold" for="has_sss">Enrolled in SSS</label>
        </div>
        {{-- Format: XX-XXXXXXX-X (12 chars) --}}
        <input type="text" name="sss_number" id="sss_number"
            class="form-control" placeholder="XX-XXXXXXX-X"
            maxlength="12" inputmode="numeric"
            value="{{ old('sss_number', $employee->sss_number ?? '') }}"
            {{ old('has_sss', $employee->has_sss ?? false) ? '' : 'disabled' }}
            {{ $readOnly ? 'readonly' : '' }}>
    </div>
    <div class="col-md-6 mb-3">
        <div class="form-check mb-2">
            <input type="checkbox" name="has_tin" id="has_tin" class="form-check-input gov-toggle"
                data-target="tin_number" {{ old('has_tin', $employee->has_tin ?? false) ? 'checked' : '' }}
                {{ $readOnly ? 'disabled' : '' }}>
            <label class="form-check-label fw-semibold" for="has_tin">Has TIN</label>
        </div>
        {{-- Format: XXX-XXX-XXX (11 chars) --}}
        <input type="text" name="tin_number" id="tin_number"
            class="form-control" placeholder="XXX-XXX-XXX"
            maxlength="11" inputmode="numeric"
            value="{{ old('tin_number', $employee->tin_number ?? '') }}"
            {{ old('has_tin', $employee->has_tin ?? false) ? '' : 'disabled' }}
            {{ $readOnly ? 'readonly' : '' }}>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <div class="form-check mb-2">
            <input type="checkbox" name="has_pagibig" id="has_pagibig" class="form-check-input gov-toggle"
                data-target="pagibig_number" {{ old('has_pagibig', $employee->has_pagibig ?? false) ? 'checked' : '' }}
                {{ $readOnly ? 'disabled' : '' }}>
            <label class="form-check-label fw-semibold" for="has_pagibig">Enrolled in Pag-IBIG</label>
        </div>
        {{-- Format: XXXX-XXXX-XXXX (14 chars) --}}
        <input type="text" name="pagibig_number" id="pagibig_number"
            class="form-control" placeholder="XXXX-XXXX-XXXX"
            maxlength="14" inputmode="numeric"
            value="{{ old('pagibig_number', $employee->pagibig_number ?? '') }}"
            {{ old('has_pagibig', $employee->has_pagibig ?? false) ? '' : 'disabled' }}
            {{ $readOnly ? 'readonly' : '' }}>
    </div>
    <div class="col-md-6 mb-3">
        <div class="form-check mb-2">
            <input type="checkbox" name="has_philhealth" id="has_philhealth" class="form-check-input gov-toggle"
                data-target="philhealth_number" {{ old('has_philhealth', $employee->has_philhealth ?? false) ? 'checked' : '' }}
                {{ $readOnly ? 'disabled' : '' }}>
            <label class="form-check-label fw-semibold" for="has_philhealth">Enrolled in PhilHealth</label>
        </div>
        {{-- Format: XX-XXXXXXXXX-X (12 chars) --}}
        <input type="text" name="philhealth_number" id="philhealth_number"
            class="form-control" placeholder="XX-XXXXXXXXX-X"
            maxlength="12" inputmode="numeric"
            value="{{ old('philhealth_number', $employee->philhealth_number ?? '') }}"
            {{ old('has_philhealth', $employee->has_philhealth ?? false) ? '' : 'disabled' }}
            {{ $readOnly ? 'readonly' : '' }}>
    </div>
</div>

@if($readOnly)
    <div class="alert alert-info mt-3 mb-0">
        <i class="feather-info me-2"></i>
        <strong>Note:</strong> Government ID numbers can only be modified by HR.
    </div>
@endif