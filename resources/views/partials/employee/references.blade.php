{{-- ================================================================
     references.blade.php
================================================================ --}}
<p class="emp-section-title-form">Character References</p>

@php
    $references = old('character_references', []);
    if (empty($references) && isset($employee) && $employee->exists) {
        $references = $employee->charRefs->toArray();
    }
    if (empty($references)) {
        $references = [['name' => '', 'address' => '', 'contact_number' => '']];
    }
@endphp

<div class="emp-dynamic-list" id="referenceList">
    @foreach($references as $i => $c)
    <div class="emp-dynamic-row">
        <button type="button" class="emp-dynamic-remove remove-reference" title="Remove">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <div class="emp-row cols-3">
            <div class="emp-field">
                <label class="emp-label">Full Name</label>
                <input type="text" name="character_references[{{ $i }}][name]"
                    class="emp-input" placeholder="e.g. Jose Reyes"
                    value="{{ $c['name'] ?? '' }}">
            </div>
            <div class="emp-field">
                <label class="emp-label">Address</label>
                <input type="text" name="character_references[{{ $i }}][address]"
                    class="emp-input" placeholder="e.g. Quezon City, Metro Manila"
                    value="{{ $c['address'] ?? '' }}">
            </div>
            <div class="emp-field">
                <label class="emp-label">Contact Number</label>
                <input type="tel" name="character_references[{{ $i }}][contact_number]"
                    class="emp-input" placeholder="09XXXXXXXXX" maxlength="13"
                    oninput="this.value = this.value.replace(/[^0-9+]/g, '')"
                    value="{{ $c['contact_number'] ?? '' }}">
            </div>
        </div>
    </div>
    @endforeach
</div>

<button type="button" id="addReference" class="emp-dynamic-add">
    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
    Add Reference
</button>

<template id="referenceTemplate">
    <div class="emp-dynamic-row">
        <button type="button" class="emp-dynamic-remove remove-reference" title="Remove">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <div class="emp-row cols-3">
            <div class="emp-field"><label class="emp-label">Full Name</label><input type="text" name="character_references[__INDEX__][name]" class="emp-input" placeholder="e.g. Jose Reyes"></div>
            <div class="emp-field"><label class="emp-label">Address</label><input type="text" name="character_references[__INDEX__][address]" class="emp-input" placeholder="e.g. Quezon City, Metro Manila"></div>
            <div class="emp-field"><label class="emp-label">Contact Number</label><input type="tel" name="character_references[__INDEX__][contact_number]" class="emp-input" placeholder="09XXXXXXXXX" maxlength="13" oninput="this.value = this.value.replace(/[^0-9+]/g, '')"></div>
        </div>
    </div>
</template>