{{-- work_experience.blade.php --}}
<p class="emp-section-title-form">Work Experience</p>

@php
    $experiences = old('work_experiences', []);
    if (empty($experiences) && isset($employee) && $employee->exists) {
        $experiences = $employee->workExperiences->toArray();
    }
    if (empty($experiences)) {
        $experiences = [['company_name' => '', 'position' => '', 'duration' => '', 'responsibilities' => '']];
    }
@endphp

<div class="emp-dynamic-list" id="experienceList">
    @foreach($experiences as $i => $we)
    <div class="emp-dynamic-row">
        <button type="button" class="emp-dynamic-remove remove-experience" title="Remove">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <div class="emp-row cols-3" style="margin-bottom:12px;">
            <div class="emp-field">
                <label class="emp-label">Company Name</label>
                <input type="text" name="work_experiences[{{ $i }}][company_name]"
                    class="emp-input" placeholder="e.g. ABC Corporation"
                    value="{{ $we['company_name'] ?? '' }}">
            </div>
            <div class="emp-field">
                <label class="emp-label">Position</label>
                <input type="text" name="work_experiences[{{ $i }}][position]"
                    class="emp-input" placeholder="e.g. Accounting Staff"
                    value="{{ $we['position'] ?? '' }}">
            </div>
            <div class="emp-field">
                <label class="emp-label">Duration</label>
                <input type="text" name="work_experiences[{{ $i }}][duration]"
                    class="emp-input" placeholder="e.g. 2019 – 2022"
                    value="{{ $we['duration'] ?? '' }}">
            </div>
        </div>
        <div class="emp-field">
            <label class="emp-label">Responsibilities</label>
            <textarea name="work_experiences[{{ $i }}][responsibilities]"
                class="emp-textarea" rows="2"
                placeholder="Brief description of responsibilities" style="resize:vertical;">{{ $we['responsibilities'] ?? '' }}</textarea>
        </div>
    </div>
    @endforeach
</div>

<button type="button" id="addExperience" class="emp-dynamic-add">
    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
    Add Experience
</button>

<template id="experienceTemplate">
    <div class="emp-dynamic-row">
        <button type="button" class="emp-dynamic-remove remove-experience" title="Remove">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <div class="emp-row cols-3" style="margin-bottom:12px;">
            <div class="emp-field"><label class="emp-label">Company Name</label><input type="text" name="work_experiences[__INDEX__][company_name]" class="emp-input" placeholder="e.g. ABC Corporation"></div>
            <div class="emp-field"><label class="emp-label">Position</label><input type="text" name="work_experiences[__INDEX__][position]" class="emp-input" placeholder="e.g. Accounting Staff"></div>
            <div class="emp-field"><label class="emp-label">Duration</label><input type="text" name="work_experiences[__INDEX__][duration]" class="emp-input" placeholder="e.g. 2019 – 2022"></div>
        </div>
        <div class="emp-field"><label class="emp-label">Responsibilities</label><textarea name="work_experiences[__INDEX__][responsibilities]" class="emp-textarea" rows="2" placeholder="Brief description of responsibilities" style="resize:vertical;"></textarea></div>
    </div>
</template>