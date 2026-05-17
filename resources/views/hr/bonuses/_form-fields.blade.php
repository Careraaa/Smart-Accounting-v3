@php
    $bonus = $bonus ?? null;
    $isSuperadmin = auth()->user()->role === 'superadmin';
    $selectedEmployees = old('eligible_employees', $bonus?->eligibleEmployees->pluck('id')->all() ?? []);
@endphp

<div class="bn-divider">General Information</div>

<div class="bn-field">
    <label class="bn-label" for="name">Bonus Name <span class="req">*</span></label>
    <input type="text" name="name" id="name" class="bn-input" value="{{ old('name', $bonus?->name) }}" required
        @if($bonus?->is_system_generated && !$isSuperadmin) readonly @endif>
</div>

<div class="bn-field">
    <label class="bn-label" for="description">Description</label>
    <textarea name="description" id="description" class="bn-textarea" placeholder="Brief description…">{{ old('description', $bonus?->description) }}</textarea>
</div>

<div class="bn-row">
    <div class="bn-field">
        <label class="bn-label" for="type">Bonus Type <span class="req">*</span></label>
        <select name="type" id="type" class="bn-select" required @if($bonus?->is_mandatory) disabled @endif>
            @foreach(\App\Models\Bonus::typeOptions() as $value => $label)
                <option value="{{ $value }}" @selected(old('type', $bonus?->type) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @if($bonus?->is_mandatory)
            <input type="hidden" name="type" value="{{ $bonus->type }}">
        @endif
    </div>
    <div class="bn-field">
        <label class="bn-label" for="status">Status <span class="req">*</span></label>
        <select name="status" id="status" class="bn-select" required>
            <option value="active" @selected(old('status', $bonus?->status ?? 'active') === 'active')>Active</option>
            <option value="inactive" @selected(old('status', $bonus?->status) === 'inactive')>Inactive</option>
        </select>
    </div>
</div>

<div class="bn-divider">Computation Settings</div>

<div class="bn-row">
    <div class="bn-field">
        <label class="bn-label" for="computation_method">Computation Method <span class="req">*</span></label>
        <select name="computation_method" id="computation_method" class="bn-select" required>
            @foreach(\App\Models\Bonus::computationMethodOptions() as $value => $label)
                <option value="{{ $value }}" @selected(old('computation_method', $bonus?->computation_method) === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="bn-field" id="fixedAmountField">
        <label class="bn-label" for="fixed_amount">Fixed Amount (₱)</label>
        <input type="number" name="fixed_amount" id="fixed_amount" class="bn-input" step="0.01" min="0"
            value="{{ old('fixed_amount', $bonus?->fixed_amount) }}">
    </div>
</div>

<div class="bn-field" id="percentageField" style="display:none;">
    <label class="bn-label" for="percentage_value">Percentage (%)</label>
    <input type="number" name="percentage_value" id="percentage_value" class="bn-input" step="0.01" min="0" max="100"
        value="{{ old('percentage_value', $bonus?->percentage_value) }}">
</div>

<div class="bn-field" id="formulaField">
    <label class="bn-label" for="formula">Formula / Computation</label>
    <input type="text" name="formula" id="formula" class="bn-input" style="font-family:'DM Mono',monospace;"
        placeholder="e.g. (total_basic_salary / 12)"
        value="{{ old('formula', $bonus?->formula) }}"
        @if($bonus?->is_system_generated && !$isSuperadmin) readonly @endif>
    <p class="bn-hint">Supports variables: basic_salary, total_basic_salary, months_worked, gross_pay, attendance_days</p>
    <div class="bn-vars">
        @foreach($formulaVariables as $var => $desc)
            <button type="button" class="bn-var-tag" data-var="{{ $var }}" title="{{ $desc }}">{{ $var }}</button>
        @endforeach
    </div>
</div>

<div class="bn-divider">Coverage</div>

<div class="bn-row">
    <div class="bn-field">
        <label class="bn-label" for="payroll_period">Payroll Period</label>
        <input type="text" name="payroll_period" id="payroll_period" class="bn-input"
            placeholder="e.g. Annual, 1st Cutoff"
            value="{{ old('payroll_period', $bonus?->payroll_period) }}">
    </div>
    <div class="bn-field">
        <label class="bn-label" for="year">Year</label>
        <input type="number" name="year" id="year" class="bn-input" min="2000" max="2100"
            value="{{ old('year', $bonus?->year ?? now()->year) }}">
    </div>
</div>

<div class="bn-field">
    <label class="bn-label">Eligible Employees</label>
    <p class="bn-hint" style="margin-bottom:8px;">Leave unchecked to include all employees.</p>
    <div class="bn-emp-list">
        @foreach($employees as $employee)
        <label class="bn-emp-item">
            <input type="checkbox" name="eligible_employees[]" value="{{ $employee->id }}"
                @checked(in_array($employee->id, $selectedEmployees))>
            <span>{{ $employee->name }} <small style="color:#9ca3af;">{{ $employee->position }}</small></span>
        </label>
        @endforeach
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const typeSelect = document.getElementById('type');
    const methodSelect = document.getElementById('computation_method');
    const fixedField = document.getElementById('fixedAmountField');
    const pctField = document.getElementById('percentageField');
    const formulaField = document.getElementById('formulaField');

    function refreshFields() {
        const type = typeSelect.value;
        const method = methodSelect.value;
        fixedField.style.display = (type === 'fixed_amount' && method === 'manual_amount') ? 'block' : 'none';
        pctField.style.display = (type === 'percentage_based' && method === 'manual_amount') ? 'block' : 'none';
        if (method === 'automatic_formula' || type === 'formula_based' || type === 'mandatory_bonus') {
            formulaField.style.display = 'block';
        }
    }

    typeSelect?.addEventListener('change', refreshFields);
    methodSelect?.addEventListener('change', refreshFields);
    refreshFields();

    document.querySelectorAll('.bn-var-tag').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const input = document.getElementById('formula');
            const v = btn.dataset.var;
            const start = input.selectionStart ?? input.value.length;
            const end = input.selectionEnd ?? input.value.length;
            input.value = input.value.slice(0, start) + v + input.value.slice(end);
            input.focus();
        });
    });
});
</script>
@endpush

