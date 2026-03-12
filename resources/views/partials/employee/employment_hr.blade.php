<h5 class="mb-5">Employment Information</h5>

<div class="row">
    <div class="col-lg-6 mb-4">
        <label for="date_of_hire" class="form-label">Date of Hire <span class="text-danger">*</span></label>
        <input type="date" name="date_of_hire" id="date_of_hire"
            class="form-control @error('date_of_hire') is-invalid @enderror"
            value="{{ old('date_of_hire', isset($employee->date_of_hire) ? $employee->date_of_hire?->format('Y-m-d') : '') }}"
            required>
        @error('date_of_hire')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
    <div class="col-lg-6 mb-4">
        <label for="position" class="form-label">Position <span class="text-danger">*</span></label>
        <input type="text" name="position" id="position"
            class="form-control @error('position') is-invalid @enderror"
            placeholder="e.g. Accounting Staff"
            value="{{ old('position', $employee->position ?? '') }}" required>
        @error('position')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="row">
    <div class="col-lg-6 mb-4">
        <label for="department" class="form-label">Department <span class="text-danger">*</span></label>
        <select name="department" id="department"
            class="form-select @error('department') is-invalid @enderror" required>
            <option value="">-- Select Department --</option>
            <option value="admin"     {{ old('department', $employee->department ?? '') == 'admin'     ? 'selected' : '' }}>Admin</option>
            <option value="operation" {{ old('department', $employee->department ?? '') == 'operation' ? 'selected' : '' }}>Operation</option>
        </select>
        @error('department')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
    <div class="col-lg-6 mb-4">
        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
        <select name="status" id="status"
            class="form-select @error('status') is-invalid @enderror" required>
            <option value="active"   {{ old('status', $employee->status ?? 'active') == 'active'   ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ old('status', $employee->status ?? '')       == 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        @error('status')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="row">
    <div class="col-lg-6 mb-4">
        <label for="salary_rate" class="form-label">Salary Rate <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text">₱</span>
            <input type="number" name="salary_rate" id="salary_rate"
                class="form-control @error('salary_rate') is-invalid @enderror"
                placeholder="0.00" step="0.01" min="0"
                value="{{ old('salary_rate', $employee->salary_rate ?? '') }}" required>
            @error('salary_rate')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        @error('salary_rate')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
</div>
