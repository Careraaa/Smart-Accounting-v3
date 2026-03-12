<h5 class="mb-4">Personal Information</h5>

@php $readOnly = $readOnly ?? false; @endphp

<!-- Name Section -->
<div class="row g-3">
    <div class="col-md-6">
        <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
        <input type="text" name="first_name" id="first_name"
            class="form-control @error('first_name') is-invalid @enderror"
            placeholder="e.g. Juan"
            value="{{ old('first_name', $employee->first_name ?? '') }}" 
            {{ $readOnly ? 'readonly' : 'required' }}>
        @error('first_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>

    <div class="col-md-6">
        <label for="middle_name" class="form-label">Middle Name <span class="text-muted">(optional)</span></label>
        <input type="text" name="middle_name" id="middle_name"
            class="form-control"
            placeholder="e.g. Santos"
            value="{{ old('middle_name', $employee->middle_name ?? '') }}"
            {{ $readOnly ? 'readonly' : '' }}>
    </div>

    <div class="col-md-6">
        <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
        <input type="text" name="last_name" id="last_name"
            class="form-control @error('last_name') is-invalid @enderror"
            placeholder="e.g. Dela Cruz"
            value="{{ old('last_name', $employee->last_name ?? '') }}" 
            {{ $readOnly ? 'readonly' : 'required' }}>
        @error('last_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>

    <div class="col-md-6">
        <label for="gender" class="form-label">Gender</label>
        <select name="gender" id="gender" class="form-select"
            {{ $readOnly ? 'disabled' : '' }}>
            <option value="">-- Select --</option>
            <option value="male"              {{ old('gender', $employee->gender ?? '') == 'male'              ? 'selected' : '' }}>Male</option>
            <option value="female"            {{ old('gender', $employee->gender ?? '') == 'female'            ? 'selected' : '' }}>Female</option>
            <option value="prefer_not_to_say" {{ old('gender', $employee->gender ?? '') == 'prefer_not_to_say' ? 'selected' : '' }}>Prefer not to say</option>
        </select>
    </div>
</div>

<hr class="my-4">

<!-- Contact Section -->
<h5 class="mb-3">Contact Information</h5>
<div class="row g-3">
    <div class="col-md-6">
        <label for="email" class="form-label">Email <span class="text-muted">(optional)</span></label>
        <input type="email" name="email" id="email"
            class="form-control @error('email') is-invalid @enderror"
            placeholder="e.g. juan.delacruz@email.com"
            value="{{ old('email', $employee->email ?? '') }}"
            {{ $readOnly ? 'readonly' : '' }}>
        @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>

    <div class="col-md-6">
        <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
        <input type="tel" name="phone" id="phone"
            class="form-control @error('phone') is-invalid @enderror"
            placeholder="09XXXXXXXXX or +639XXXXXXXXX"
            maxlength="13"
            pattern="(09\d{9}|\+639\d{9})"
            oninput="this.value = this.value.replace(/[^0-9+]/g, '')"
            value="{{ old('phone', $employee->phone ?? '') }}" 
            {{ $readOnly ? 'readonly' : 'required' }}>
        @error('phone') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>
</div>

<hr class="my-4">

<!-- Personal Details -->
<h5 class="mb-3">Personal Details</h5>
<div class="row g-3">
    <div class="col-md-4">
        <label for="civil_status" class="form-label">Civil Status</label>
        <select name="civil_status" id="civil_status" class="form-select"
            onchange="document.getElementById('spouse_field').style.display = this.value === 'married' ? 'block' : 'none'"
            {{ $readOnly ? 'disabled' : '' }}>
            <option value="">-- Select --</option>
            <option value="single"    {{ old('civil_status', $employee->civil_status ?? '') == 'single'    ? 'selected' : '' }}>Single</option>
            <option value="married"   {{ old('civil_status', $employee->civil_status ?? '') == 'married'   ? 'selected' : '' }}>Married</option>
            <option value="widowed"   {{ old('civil_status', $employee->civil_status ?? '') == 'widowed'   ? 'selected' : '' }}>Widowed</option>
            <option value="separated" {{ old('civil_status', $employee->civil_status ?? '') == 'separated' ? 'selected' : '' }}>Separated</option>
        </select>
    </div>

    <div class="col-md-4" id="spouse_field" style="display: none;">
        <label for="spouse_name" class="form-label">Spouse Name</label>
        <input type="text" name="spouse_name" id="spouse_name"
            class="form-control"
            placeholder="N/A"
            value="{{ old('spouse_name', $employee->spouse_name ?? '') }}"
            {{ $readOnly ? 'readonly' : '' }}>
    </div>

    <div class="col-md-4">
        <label for="date_of_birth" class="form-label">Date of Birth</label>
        <input type="date" name="date_of_birth" id="date_of_birth"
            class="form-control"
            value="{{ old('date_of_birth', isset($employee->date_of_birth) ? $employee->date_of_birth?->format('Y-m-d') : '') }}"
            {{ $readOnly ? 'readonly' : '' }}>
    </div>
</div>

<hr class="my-4">

<!-- Birth & Education -->
<h5 class="mb-3">Birth & Education</h5>
<div class="row g-3">
    <div class="col-md-6">
        <label for="place_of_birth" class="form-label">Place of Birth</label>
        <input type="text" name="place_of_birth" id="place_of_birth"
            class="form-control"
            placeholder="e.g. Manila, Metro Manila"
            value="{{ old('place_of_birth', $employee->place_of_birth ?? '') }}"
            {{ $readOnly ? 'readonly' : '' }}>
    </div>

    <div class="col-md-6">
        <label for="educational_attainment" class="form-label">Educational Attainment</label>
        <select name="educational_attainment" id="educational_attainment" class="form-select"
            {{ $readOnly ? 'disabled' : '' }}>
            <option value="">-- Select --</option>
            <option value="Elementary"               {{ old('educational_attainment', $employee->educational_attainment ?? '') == 'Elementary' ? 'selected' : '' }}>Elementary</option>
            <option value="High School"              {{ old('educational_attainment', $employee->educational_attainment ?? '') == 'High School' ? 'selected' : '' }}>High School</option>
            <option value="Senior High School / K-12"{{ old('educational_attainment', $employee->educational_attainment ?? '') == 'Senior High School / K-12' ? 'selected' : '' }}>Senior High School / K-12</option>
            <option value="Vocational / TESDA"       {{ old('educational_attainment', $employee->educational_attainment ?? '') == 'Vocational / TESDA' ? 'selected' : '' }}>Vocational / TESDA</option>
            <option value="College (Bachelor's)"     {{ old('educational_attainment', $employee->educational_attainment ?? '') == "College (Bachelor's)" ? 'selected' : '' }}>College (Bachelor's)</option>
        </select>
    </div>
</div>

<hr class="my-4">

<!-- Address -->
<h5 class="mb-3">Address</h5>
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Street / House No. <span class="text-danger">*</span></label>
        <input type="text" name="address_street" id="address_street"
            class="form-control @error('address_street') is-invalid @enderror"
            placeholder="e.g. 123 Rizal St."
            value="{{ old('address_street', $employee->address_street ?? '') }}" 
            {{ $readOnly ? 'readonly' : 'required' }}>
        @error('address_street') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Barangay <span class="text-danger">*</span></label>
        <input type="text" name="address_barangay" id="address_barangay"
            class="form-control @error('address_barangay') is-invalid @enderror"
            placeholder="e.g. Brgy. Poblacion"
            value="{{ old('address_barangay', $employee->address_barangay ?? '') }}" 
            {{ $readOnly ? 'readonly' : 'required' }}>
        @error('address_barangay') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">City / Municipality <span class="text-danger">*</span></label>
        <input type="text" name="address_city" id="address_city"
            class="form-control @error('address_city') is-invalid @enderror"
            placeholder="e.g. Makati City"
            value="{{ old('address_city', $employee->address_city ?? '') }}" 
            {{ $readOnly ? 'readonly' : 'required' }}>
        @error('address_city') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Province <span class="text-danger">*</span></label>
        <input type="text" name="address_province" id="address_province"
            class="form-control @error('address_province') is-invalid @enderror"
            placeholder="e.g. Metro Manila"
            value="{{ old('address_province', $employee->address_province ?? '') }}" 
            {{ $readOnly ? 'readonly' : 'required' }}>
        @error('address_province') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>

    <input type="hidden" name="address" id="address_combined">
</div>
