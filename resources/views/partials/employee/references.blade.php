<h5 class="mb-3">Character References</h5>
<div id="referenceList">
    @php
        $references = old('character_references', []);
        if (empty($references) && isset($employee) && $employee->exists) {
            $references = $employee->charRefs->toArray();
        }
        if (empty($references)) {
            $references = [['name' => '', 'address' => '', 'contact_number' => '']];
        }
    @endphp

    @foreach($references as $i => $c)
        <div class="reference-row border rounded p-3 mb-3">
            <div class="row g-2">
                <div class="col-md-4">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="character_references[{{ $i }}][name]"
                        class="form-control" placeholder="e.g. Jose Reyes"
                        value="{{ $c['name'] ?? '' }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Address</label>
                    <input type="text" name="character_references[{{ $i }}][address]"
                        class="form-control" placeholder="e.g. Quezon City, Metro Manila"
                        value="{{ $c['address'] ?? '' }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Contact Number</label>
                    <input type="tel" name="character_references[{{ $i }}][contact_number]"
                        class="form-control" placeholder="09XXXXXXXXX" maxlength="13"
                        oninput="this.value = this.value.replace(/[^0-9+]/g, '')"
                        value="{{ $c['contact_number'] ?? '' }}">
                </div>
            </div>
            <div class="text-end mt-2">
                <button type="button" class="btn btn-sm btn-danger remove-reference">Remove</button>
            </div>
        </div>
    @endforeach
</div>

<button type="button" id="addReference" class="btn btn-sm btn-primary mt-1">
    <i class="bi bi-plus-circle"></i> Add Reference
</button>

<template id="referenceTemplate">
    <div class="reference-row border rounded p-3 mb-3">
        <div class="row g-2">
            <div class="col-md-4">
                <label class="form-label">Full Name</label>
                <input type="text" name="character_references[__INDEX__][name]"
                    class="form-control" placeholder="e.g. Jose Reyes">
            </div>
            <div class="col-md-4">
                <label class="form-label">Address</label>
                <input type="text" name="character_references[__INDEX__][address]"
                    class="form-control" placeholder="e.g. Quezon City, Metro Manila">
            </div>
            <div class="col-md-4">
                <label class="form-label">Contact Number</label>
                <input type="tel" name="character_references[__INDEX__][contact_number]"
                    class="form-control" placeholder="09XXXXXXXXX" maxlength="13"
                    oninput="this.value = this.value.replace(/[^0-9+]/g, '')">
            </div>
        </div>
        <div class="text-end mt-2">
            <button type="button" class="btn btn-sm btn-danger remove-reference">Remove</button>
        </div>
    </div>
</template>