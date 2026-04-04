{{-- ================================================================
     beneficiaries.blade.php
================================================================ --}}
<p class="emp-section-title-form">Beneficiaries</p>

@php
    $beneficiaries = old('beneficiaries', []);
    if (empty($beneficiaries) && isset($employee) && $employee->exists) {
        $beneficiaries = $employee->beneficiaries->toArray();
    }
    if (empty($beneficiaries)) {
        $beneficiaries = [['name' => '', 'date_of_birth' => '', 'relationship' => '']];
    }
@endphp

<div class="emp-dynamic-list" id="beneficiaryList">
    @foreach($beneficiaries as $i => $b)
    <div class="emp-dynamic-row">
        <button type="button" class="emp-dynamic-remove remove-beneficiary" title="Remove">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <div class="emp-row cols-3">
            <div class="emp-field">
                <label class="emp-label">Full Name</label>
                <input type="text" name="beneficiaries[{{ $i }}][name]"
                    class="emp-input" placeholder="e.g. Maria Dela Cruz"
                    value="{{ $b['name'] ?? '' }}">
            </div>
            <div class="emp-field">
                <label class="emp-label">Relationship</label>
                <div class="emp-select-wrap">
                    <select name="beneficiaries[{{ $i }}][relationship]" class="emp-select">
                        <option value="">— Select —</option>
                        @foreach(['spouse','child','parent','sibling','other'] as $rel)
                            <option value="{{ $rel }}" @selected(($b['relationship'] ?? '') == $rel)>{{ ucfirst($rel) }}</option>
                        @endforeach
                    </select>
                    <svg class="emp-chevron" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>
            <div class="emp-field">
                <label class="emp-label">Date of Birth</label>
                <input type="date" name="beneficiaries[{{ $i }}][date_of_birth]"
                    class="emp-input"
                    value="{{ $b['date_of_birth'] ?? '' }}">
            </div>
        </div>
    </div>
    @endforeach
</div>

<button type="button" id="addBeneficiary" class="emp-dynamic-add">
    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
    Add Beneficiary
</button>

<template id="beneficiaryTemplate">
    <div class="emp-dynamic-row">
        <button type="button" class="emp-dynamic-remove remove-beneficiary" title="Remove">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <div class="emp-row cols-3">
            <div class="emp-field">
                <label class="emp-label">Full Name</label>
                <input type="text" name="beneficiaries[__INDEX__][name]" class="emp-input" placeholder="e.g. Maria Dela Cruz">
            </div>
            <div class="emp-field">
                <label class="emp-label">Relationship</label>
                <div class="emp-select-wrap">
                    <select name="beneficiaries[__INDEX__][relationship]" class="emp-select">
                        <option value="">— Select —</option>
                        <option value="spouse">Spouse</option>
                        <option value="child">Child</option>
                        <option value="parent">Parent</option>
                        <option value="sibling">Sibling</option>
                        <option value="other">Other</option>
                    </select>
                    <svg class="emp-chevron" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>
            <div class="emp-field">
                <label class="emp-label">Date of Birth</label>
                <input type="date" name="beneficiaries[__INDEX__][date_of_birth]" class="emp-input">
            </div>
        </div>
    </div>
</template>