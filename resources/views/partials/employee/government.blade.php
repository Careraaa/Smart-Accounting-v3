<h5 class="mb-4">Government Numbers</h5>

<div class="row">
    <div class="col-md-4 mb-3">
        <div class="form-check mb-2">
            <input type="checkbox" name="has_sss" id="has_sss" class="form-check-input gov-toggle" data-target="sss_number"
                {{ old('has_sss', $employee->has_sss ?? false) ? 'checked' : '' }}>
            <label class="form-check-label fw-semibold" for="has_sss">Enrolled in SSS</label>
        </div>
        <input type="text" name="sss_number" id="sss_number" class="form-control" placeholder="XX-XXXXXXX-X"
            maxlength="12" oninput="this.value = this.value.replace(/[^0-9-]/g, '')"
            value="{{ old('sss_number', $employee->sss_number ?? '') }}"
            {{ old('has_sss', $employee->has_sss ?? false) ? '' : 'disabled' }}>
    </div>
    <div class="col-md-4 mb-3">
        <div class="form-check mb-2">
            <input type="checkbox" name="has_tin" id="has_tin" class="form-check-input gov-toggle"
                data-target="tin_number" {{ old('has_tin', $employee->has_tin ?? false) ? 'checked' : '' }}>
            <label class="form-check-label fw-semibold" for="has_tin">Has TIN</label>
        </div>
        <input type="text" name="tin_number" id="tin_number" class="form-control" placeholder="XXX-XXX-XXX"
            maxlength="11" oninput="this.value = this.value.replace(/[^0-9-]/g, '')"
            value="{{ old('tin_number', $employee->tin_number ?? '') }}"
            {{ old('has_tin', $employee->has_tin ?? false) ? '' : 'disabled' }}>
    </div>
    <div class="col-md-4 mb-3">
        <div class="form-check mb-2">
            <input type="checkbox" name="has_pagibig" id="has_pagibig" class="form-check-input gov-toggle"
                data-target="pagibig_number" {{ old('has_pagibig', $employee->has_pagibig ?? false) ? 'checked' : '' }}>
            <label class="form-check-label fw-semibold" for="has_pagibig">Enrolled in Pag-IBIG</label>
        </div>
        <input type="text" name="pagibig_number" id="pagibig_number" class="form-control"
            placeholder="XXXX-XXXX-XXXX" maxlength="14" oninput="this.value = this.value.replace(/[^0-9-]/g, '')"
            value="{{ old('pagibig_number', $employee->pagibig_number ?? '') }}"
            {{ old('has_pagibig', $employee->has_pagibig ?? false) ? '' : 'disabled' }}>
    </div>
</div>
