<h5 class="mb-3">Special Skills</h5>
<div id="skillList">
    @php
        $skills = old('special_skills', []);
        if(empty($skills) && isset($employee) && $employee->exists) {
            $skills = $employee->specialSkills->toArray();
        }
        if(empty($skills)) {
            $skills = [['skill_name' => '', 'proficiency' => '']];
        }
    @endphp

    @foreach($skills as $i => $skill)
        <div class="skill-row border rounded p-3 mb-3">
            <div class="row g-2">
                <div class="col-md-7">
                    <label class="form-label">Skill</label>
                    <input type="text" name="special_skills[{{ $i }}][skill_name]"
                        class="form-control"
                        placeholder="e.g. Microsoft Excel, Driving, Welding"
                        value="{{ $skill['skill_name'] ?? '' }}">
                </div>
                <div class="col-md-5">
                    <label class="form-label">Proficiency</label>
                    <select name="special_skills[{{ $i }}][proficiency]" class="form-select">
                        <option value="">-- Select --</option>
                        <option value="beginner"     {{ ($skill['proficiency'] ?? '') == 'beginner'     ? 'selected' : '' }}>Beginner</option>
                        <option value="intermediate" {{ ($skill['proficiency'] ?? '') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                        <option value="advanced"     {{ ($skill['proficiency'] ?? '') == 'advanced'     ? 'selected' : '' }}>Advanced</option>
                        <option value="expert"       {{ ($skill['proficiency'] ?? '') == 'expert'       ? 'selected' : '' }}>Expert</option>
                    </select>
                </div>
            </div>
            <div class="text-end mt-2">
                <button type="button" class="btn btn-sm btn-danger remove-skill">Remove</button>
            </div>
        </div>
    @endforeach
</div>

<button type="button" id="addSkill" class="btn btn-sm btn-primary mt-1">
    <i class="bi bi-plus-circle"></i> Add Skill
</button>

<template id="skillTemplate">
    <div class="skill-row border rounded p-3 mb-3">
        <div class="row g-2">
            <div class="col-md-7">
                <label class="form-label">Skill</label>
                <input type="text" name="special_skills[__INDEX__][skill_name]"
                    class="form-control" placeholder="e.g. Microsoft Excel, Driving, Welding">
            </div>
            <div class="col-md-5">
                <label class="form-label">Proficiency</label>
                <select name="special_skills[__INDEX__][proficiency]" class="form-select">
                    <option value="">-- Select --</option>
                    <option value="beginner">Beginner</option>
                    <option value="intermediate">Intermediate</option>
                    <option value="advanced">Advanced</option>
                    <option value="expert">Expert</option>
                </select>
            </div>
        </div>
        <div class="text-end mt-2">
            <button type="button" class="btn btn-sm btn-danger remove-skill">Remove</button>
        </div>
    </div>
</template>