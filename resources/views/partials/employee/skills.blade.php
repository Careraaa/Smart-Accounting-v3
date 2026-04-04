{{-- skills.blade.php --}}
<p class="emp-section-title-form">Special Skills</p>

@php
    $skills = old('special_skills', []);
    if (empty($skills) && isset($employee) && $employee->exists) {
        $skills = $employee->specialSkills->toArray();
    }
    if (empty($skills)) {
        $skills = [['skill_name' => '', 'proficiency' => '']];
    }
@endphp

<div class="emp-dynamic-list" id="skillList">
    @foreach($skills as $i => $skill)
    <div class="emp-dynamic-row">
        <button type="button" class="emp-dynamic-remove remove-skill" title="Remove">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <div class="emp-row cols-2">
            <div class="emp-field">
                <label class="emp-label">Skill</label>
                <input type="text" name="special_skills[{{ $i }}][skill_name]"
                    class="emp-input" placeholder="e.g. Microsoft Excel, Driving, Welding"
                    value="{{ $skill['skill_name'] ?? '' }}">
            </div>
            <div class="emp-field">
                <label class="emp-label">Proficiency</label>
                <div class="emp-select-wrap">
                    <select name="special_skills[{{ $i }}][proficiency]" class="emp-select">
                        <option value="">— Select —</option>
                        @foreach(['beginner','intermediate','advanced','expert'] as $p)
                            <option value="{{ $p }}" @selected(($skill['proficiency'] ?? '') == $p)>{{ ucfirst($p) }}</option>
                        @endforeach
                    </select>
                    <svg class="emp-chevron" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<button type="button" id="addSkill" class="emp-dynamic-add">
    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
    Add Skill
</button>

<template id="skillTemplate">
    <div class="emp-dynamic-row">
        <button type="button" class="emp-dynamic-remove remove-skill" title="Remove">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <div class="emp-row cols-2">
            <div class="emp-field"><label class="emp-label">Skill</label><input type="text" name="special_skills[__INDEX__][skill_name]" class="emp-input" placeholder="e.g. Microsoft Excel, Driving, Welding"></div>
            <div class="emp-field">
                <label class="emp-label">Proficiency</label>
                <div class="emp-select-wrap">
                    <select name="special_skills[__INDEX__][proficiency]" class="emp-select">
                        <option value="">— Select —</option>
                        <option value="beginner">Beginner</option>
                        <option value="intermediate">Intermediate</option>
                        <option value="advanced">Advanced</option>
                        <option value="expert">Expert</option>
                    </select>
                    <svg class="emp-chevron" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>
        </div>
    </div>
</template>