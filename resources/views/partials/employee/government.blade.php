@php $readOnly = $readOnly ?? false; @endphp

<p class="emp-section-title-form">Government Numbers</p>

<div class="emp-row cols-2" style="margin-bottom:16px;">
    {{-- SSS --}}
    <div class="emp-field">
        <div class="emp-check-row" style="margin-bottom:8px;">
            <input type="checkbox" name="has_sss" id="has_sss" class="emp-check-input gov-toggle" data-target="sss_number"
                {{ old('has_sss', $employee->has_sss ?? false) ? 'checked' : '' }}
                {{ $readOnly ? 'disabled' : '' }}>
            <label class="emp-check-label" for="has_sss">Enrolled in SSS</label>
        </div>
        <label class="emp-label">SSS Number</label>
        <input type="text" name="sss_number" id="sss_number"
            class="emp-input"
            placeholder="XX-XXXXXXX-X" maxlength="12" inputmode="numeric"
            value="{{ old('sss_number', $employee->sss_number ?? '') }}"
            {{ old('has_sss', $employee->has_sss ?? false) ? '' : 'disabled' }}
            {{ $readOnly ? 'readonly' : '' }}>
    </div>

    {{-- TIN --}}
    <div class="emp-field">
        <div class="emp-check-row" style="margin-bottom:8px;">
            <input type="checkbox" name="has_tin" id="has_tin" class="emp-check-input gov-toggle" data-target="tin_number"
                {{ old('has_tin', $employee->has_tin ?? false) ? 'checked' : '' }}
                {{ $readOnly ? 'disabled' : '' }}>
            <label class="emp-check-label" for="has_tin">Has TIN</label>
        </div>
        <label class="emp-label">TIN Number</label>
        <input type="text" name="tin_number" id="tin_number"
            class="emp-input"
            placeholder="XXX-XXX-XXX" maxlength="11" inputmode="numeric"
            value="{{ old('tin_number', $employee->tin_number ?? '') }}"
            {{ old('has_tin', $employee->has_tin ?? false) ? '' : 'disabled' }}
            {{ $readOnly ? 'readonly' : '' }}>
    </div>

    {{-- Pag-IBIG --}}
    <div class="emp-field">
        <div class="emp-check-row" style="margin-bottom:8px;">
            <input type="checkbox" name="has_pagibig" id="has_pagibig" class="emp-check-input gov-toggle" data-target="pagibig_number"
                {{ old('has_pagibig', $employee->has_pagibig ?? false) ? 'checked' : '' }}
                {{ $readOnly ? 'disabled' : '' }}>
            <label class="emp-check-label" for="has_pagibig">Enrolled in Pag-IBIG</label>
        </div>
        <label class="emp-label">Pag-IBIG Number</label>
        <input type="text" name="pagibig_number" id="pagibig_number"
            class="emp-input"
            placeholder="XXXX-XXXX-XXXX" maxlength="14" inputmode="numeric"
            value="{{ old('pagibig_number', $employee->pagibig_number ?? '') }}"
            {{ old('has_pagibig', $employee->has_pagibig ?? false) ? '' : 'disabled' }}
            {{ $readOnly ? 'readonly' : '' }}>
    </div>

    {{-- PhilHealth --}}
    <div class="emp-field">
        <div class="emp-check-row" style="margin-bottom:8px;">
            <input type="checkbox" name="has_philhealth" id="has_philhealth" class="emp-check-input gov-toggle" data-target="philhealth_number"
                {{ old('has_philhealth', $employee->has_philhealth ?? false) ? 'checked' : '' }}
                {{ $readOnly ? 'disabled' : '' }}>
            <label class="emp-check-label" for="has_philhealth">Enrolled in PhilHealth</label>
        </div>
        <label class="emp-label">PhilHealth Number</label>
        <input type="text" name="philhealth_number" id="philhealth_number"
            class="emp-input"
            placeholder="XX-XXXXXXXXX-X" maxlength="14" inputmode="numeric"
            value="{{ old('philhealth_number', $employee->philhealth_number ?? '') }}"
            {{ old('has_philhealth', $employee->has_philhealth ?? false) ? '' : 'disabled' }}
            {{ $readOnly ? 'readonly' : '' }}>
    </div>
</div>

@if($readOnly)
<div class="emp-alert info">
    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px;"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
    Government ID numbers can only be modified by HR.
</div>
@endif