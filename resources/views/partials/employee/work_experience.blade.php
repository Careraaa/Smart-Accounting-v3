<h5 class="mb-3">Work Experience</h5>
<div id="experienceList">
    @php
        $experiences = old('work_experiences', []);
        if (empty($experiences) && isset($employee) && $employee->exists) {
            $experiences = $employee->workExperiences->toArray();
        }
        if (empty($experiences)) {
            $experiences = [['company_name' => '', 'position' => '', 'duration' => '', 'responsibilities' => '']];
        }
    @endphp

    @foreach($experiences as $i => $we)
        <div class="experience-row border rounded p-3 mb-3">
            <div class="row g-2">
                <div class="col-md-5">
                    <label class="form-label">Company Name</label>
                    <input type="text" name="work_experiences[{{ $i }}][company_name]"
                        class="form-control" placeholder="e.g. ABC Corporation"
                        value="{{ $we['company_name'] ?? '' }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Position</label>
                    <input type="text" name="work_experiences[{{ $i }}][position]"
                        class="form-control" placeholder="e.g. Accounting Staff"
                        value="{{ $we['position'] ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Duration</label>
                    <input type="text" name="work_experiences[{{ $i }}][duration]"
                        class="form-control" placeholder="e.g. 2019 - 2022"
                        value="{{ $we['duration'] ?? '' }}">
                </div>
                <div class="col-md-12">
                    <label class="form-label">Responsibilities</label>
                    <textarea name="work_experiences[{{ $i }}][responsibilities]"
                        class="form-control" rows="2"
                        placeholder="Brief description of responsibilities">{{ $we['responsibilities'] ?? '' }}</textarea>
                </div>
            </div>
            <div class="text-end mt-2">
                <button type="button" class="btn btn-sm btn-danger remove-experience">Remove</button>
            </div>
        </div>
    @endforeach
</div>

<button type="button" id="addExperience" class="btn btn-sm btn-primary mt-1">
    <i class="bi bi-plus-circle"></i> Add Experience
</button>

<template id="experienceTemplate">
    <div class="experience-row border rounded p-3 mb-3">
        <div class="row g-2">
            <div class="col-md-5">
                <label class="form-label">Company Name</label>
                <input type="text" name="work_experiences[__INDEX__][company_name]"
                    class="form-control" placeholder="e.g. ABC Corporation">
            </div>
            <div class="col-md-4">
                <label class="form-label">Position</label>
                <input type="text" name="work_experiences[__INDEX__][position]"
                    class="form-control" placeholder="e.g. Accounting Staff">
            </div>
            <div class="col-md-3">
                <label class="form-label">Duration</label>
                <input type="text" name="work_experiences[__INDEX__][duration]"
                    class="form-control" placeholder="e.g. 2019 - 2022">
            </div>
            <div class="col-md-12">
                <label class="form-label">Responsibilities</label>
                <textarea name="work_experiences[__INDEX__][responsibilities]"
                    class="form-control" rows="2"
                    placeholder="Brief description of responsibilities"></textarea>
            </div>
        </div>
        <div class="text-end mt-2">
            <button type="button" class="btn btn-sm btn-danger remove-experience">Remove</button>
        </div>
    </div>
</template>