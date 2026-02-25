<h5 class="mb-3">Beneficiaries</h5>
<div id="beneficiaryList">
    @php
        $beneficiaries = old('beneficiaries', []);
        if(empty($beneficiaries) && isset($employee) && $employee->exists) {
            $beneficiaries = $employee->beneficiaries->toArray();
        }
        if(empty($beneficiaries)) {
            $beneficiaries = [['name' => '', 'date_of_birth' => '', 'relationship' => '']];
        }
    @endphp

    @foreach($beneficiaries as $i => $b)
        <div class="beneficiary-row border rounded p-3 mb-3">
            <div class="row g-2">
                <div class="col-md-5">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="beneficiaries[{{ $i }}][name]"
                        class="form-control"
                        placeholder="e.g. Maria Dela Cruz"
                        value="{{ $b['name'] ?? '' }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Relationship</label>
                    <select name="beneficiaries[{{ $i }}][relationship]" class="form-select">
                        <option value="">-- Select --</option>
                        <option value="spouse"  {{ ($b['relationship'] ?? '') == 'spouse'  ? 'selected' : '' }}>Spouse</option>
                        <option value="child"   {{ ($b['relationship'] ?? '') == 'child'   ? 'selected' : '' }}>Child</option>
                        <option value="parent"  {{ ($b['relationship'] ?? '') == 'parent'  ? 'selected' : '' }}>Parent</option>
                        <option value="sibling" {{ ($b['relationship'] ?? '') == 'sibling' ? 'selected' : '' }}>Sibling</option>
                        <option value="other"   {{ ($b['relationship'] ?? '') == 'other'   ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="beneficiaries[{{ $i }}][date_of_birth]"
                        class="form-control"
                        value="{{ $b['date_of_birth'] ?? '' }}">
                </div>
            </div>
            <div class="text-end mt-2">
                <button type="button" class="btn btn-sm btn-danger remove-beneficiary">Remove</button>
            </div>
        </div>
    @endforeach
</div>

<button type="button" id="addBeneficiary" class="btn btn-sm btn-primary mt-1">
    <i class="bi bi-plus-circle"></i> Add Beneficiary
</button>

<template id="beneficiaryTemplate">
    <div class="beneficiary-row border rounded p-3 mb-3">
        <div class="row g-2">
            <div class="col-md-5">
                <label class="form-label">Full Name</label>
                <input type="text" name="beneficiaries[__INDEX__][name]"
                    class="form-control" placeholder="e.g. Maria Dela Cruz">
            </div>
            <div class="col-md-4">
                <label class="form-label">Relationship</label>
                <select name="beneficiaries[__INDEX__][relationship]" class="form-select">
                    <option value="">-- Select --</option>
                    <option value="spouse">Spouse</option>
                    <option value="child">Child</option>
                    <option value="parent">Parent</option>
                    <option value="sibling">Sibling</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Date of Birth</label>
                <input type="date" name="beneficiaries[__INDEX__][date_of_birth]"
                    class="form-control">
            </div>
        </div>
        <div class="text-end mt-2">
            <button type="button" class="btn btn-sm btn-danger remove-beneficiary">Remove</button>
        </div>
    </div>
</template>