{{-- employment.blade.php --}}
<p class="emp-section-title-form">Employment Information</p>

<div class="emp-row cols-2" style="margin-bottom:16px;">
    <div class="emp-field">
        <label class="emp-label">Date of Hire <span class="req">*</span></label>
        <input type="date" name="date_of_hire" id="date_of_hire"
            class="emp-input @error('date_of_hire') is-invalid @enderror"
            value="{{ old('date_of_hire', isset($employee->date_of_hire) ? $employee->date_of_hire?->format('Y-m-d') : '') }}"
            required>
        @error('date_of_hire')<span class="emp-invalid">{{ $message }}</span>@enderror
    </div>
    <div class="emp-field">
        <label class="emp-label">Position <span class="req">*</span></label>
        <input type="text" name="position" id="position"
            class="emp-input @error('position') is-invalid @enderror"
            placeholder="e.g. Accounting Staff"
            value="{{ old('position', $employee->position ?? '') }}" required>
        @error('position')<span class="emp-invalid">{{ $message }}</span>@enderror
    </div>
</div>

<div class="emp-row cols-2" style="margin-bottom:16px;">
    <div class="emp-field">
        <label class="emp-label">Department <span class="req">*</span></label>
        <div class="emp-select-wrap">
            <select name="department" id="department"
                class="emp-select @error('department') is-invalid @enderror" required>
                <option value="">— Select Department —</option>
                <option value="Admin"        @selected(old('department', $employee->department ?? '') == 'Admin')>Admin</option>
                <option value="Operation"    @selected(old('department', $employee->department ?? '') == 'Operation')>Operation</option>
                <option value="HR"           @selected(old('department', $employee->department ?? '') == 'HR')>HR</option>
                <option value="Accounting"   @selected(old('department', $employee->department ?? '') == 'Accounting')>Accounting</option>
                <option value="Maintenance"  @selected(old('department', $employee->department ?? '') == 'Maintenance')>Maintenance</option>
            </select>
            <svg class="emp-chevron" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </div>
        @error('department')<span class="emp-invalid">{{ $message }}</span>@enderror
    </div>
    <div class="emp-field">
        <label class="emp-label">Status <span class="req">*</span></label>
        <div class="emp-select-wrap">
            <select name="status" id="status"
                class="emp-select @error('status') is-invalid @enderror" required>
                <option value="active"   @selected(old('status', $employee->status ?? 'active') == 'active')>Active</option>
                <option value="inactive" @selected(old('status', $employee->status ?? '') == 'inactive')>Inactive</option>
            </select>
            <svg class="emp-chevron" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </div>
        @error('status')<span class="emp-invalid">{{ $message }}</span>@enderror
    </div>
</div>

<div class="emp-row cols-2" style="margin-bottom:16px;">
    <div class="emp-field">
        <label class="emp-label">Daily Rate (₱ per day) <span class="req">*</span></label>
        <div class="emp-input-group">
            <span class="emp-input-group-text">₱</span>
            <input type="number" name="salary_rate" id="salary_rate"
                class="emp-input @error('salary_rate') is-invalid @enderror"
                placeholder="0.00" step="0.01" min="0" max="999999.99"
                value="{{ old('salary_rate', $employee->salary_rate ?? '') }}" required>
        </div>
        @error('salary_rate')<span class="emp-invalid">{{ $message }}</span>@enderror
        <p class="emp-hint">Employee's daily rate used for payroll, OT, and UT calculations.</p>
    </div>
    <div class="emp-field">
        <label class="emp-label">Salary Type <span class="opt">(optional)</span></label>
        <div class="emp-select-wrap">
            <select name="salary_type" id="salary_type"
                class="emp-select @error('salary_type') is-invalid @enderror">
                <option value="daily"   @selected(old('salary_type', $employee->salary_type ?? 'daily') === 'daily')>Daily</option>
                <option value="monthly" @selected(old('salary_type', $employee->salary_type ?? '') === 'monthly')>Monthly</option>
            </select>
            <svg class="emp-chevron" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </div>
        @error('salary_type')<span class="emp-invalid">{{ $message }}</span>@enderror
        <p class="emp-hint">If monthly, daily rate is calculated as monthly ÷ 22 working days.</p>
    </div>
</div>