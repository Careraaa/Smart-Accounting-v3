<h5 class="mb-4">Personal Information</h5>

<div class="row">
    <div class="col-md-4 mb-3">
        <label for="first_name" class="form-label">First Name *</label>
        <input type="text" name="first_name" id="first_name"
            class="form-control @error('first_name') is-invalid @enderror" placeholder="e.g. Juan"
            value="{{ old('first_name', $employee->first_name ?? '') }}" required>
        @error('first_name')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
    <div class="col-md-4 mb-3">
        <label for="middle_name" class="form-label">Middle Name</label>
        <input type="text" name="middle_name" id="middle_name" class="form-control" placeholder="e.g. Santos"
            value="{{ old('middle_name', $employee->middle_name ?? '') }}">
    </div>
    <div class="col-md-4 mb-3">
        <label for="last_name" class="form-label">Last Name *</label>
        <input type="text" name="last_name" id="last_name"
            class="form-control @error('last_name') is-invalid @enderror" placeholder="e.g. Dela Cruz"
            value="{{ old('last_name', $employee->last_name ?? '') }}" required>
        @error('last_name')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="email" class="form-label">Email *</label>
        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
            placeholder="e.g. juan.delacruz@email.com" value="{{ old('email', $employee->email ?? '') }}" required>
        @error('email')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="phone" class="form-label">Phone *</label>
        <input type="tel" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror"
            placeholder="09XXXXXXXXX or +639XXXXXXXXX" maxlength="13" pattern="(09\d{9}|\+639\d{9})"
            oninput="this.value = this.value.replace(/[^0-9+]/g, '')" value="{{ old('phone', $employee->phone ?? '') }}"
            required>
        <div class="form-text">Format: 09XXXXXXXXX or +639XXXXXXXXX</div>
        @error('phone')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label for="civil_status" class="form-label">Civil Status</label>
        <select name="civil_status" id="civil_status" class="form-select">
            <option value="">-- Select --</option>
            <option value="single"
                {{ old('civil_status', $employee->civil_status ?? '') == 'single' ? 'selected' : '' }}>Single
            </option>
            <option value="married"
                {{ old('civil_status', $employee->civil_status ?? '') == 'married' ? 'selected' : '' }}>Married
            </option>
            <option value="widowed"
                {{ old('civil_status', $employee->civil_status ?? '') == 'widowed' ? 'selected' : '' }}>Widowed
            </option>
            <option value="separated"
                {{ old('civil_status', $employee->civil_status ?? '') == 'separated' ? 'selected' : '' }}>Separated
            </option>
        </select>
    </div>
    <div class="col-md-4 mb-3">
        <label for="spouse_name" class="form-label">Spouse Name</label>
        <input type="text" name="spouse_name" id="spouse_name" class="form-control"
            placeholder="e.g. Maria Dela Cruz" value="{{ old('spouse_name', $employee->spouse_name ?? '') }}">
    </div>
    <div class="col-md-4 mb-3">
        <label for="date_of_birth" class="form-label">Date of Birth</label>
        <input type="date" name="date_of_birth" id="date_of_birth" class="form-control"
            value="{{ old('date_of_birth', isset($employee->date_of_birth) ? $employee->date_of_birth?->format('Y-m-d') : '') }}">
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="place_of_birth" class="form-label">Place of Birth</label>
        <input type="text" name="place_of_birth" id="place_of_birth" class="form-control"
            placeholder="e.g. Manila, Metro Manila"
            value="{{ old('place_of_birth', $employee->place_of_birth ?? '') }}">
    </div>
    <div class="col-md-6 mb-3">
        <label for="educational_attainment" class="form-label">Educational Attainment</label>
        <input type="text" name="educational_attainment" id="educational_attainment" class="form-control"
            placeholder="e.g. Bachelor of Science in Accountancy"
            value="{{ old('educational_attainment', $employee->educational_attainment ?? '') }}">
    </div>
</div>

<div class="mb-3">
    <label for="address" class="form-label">Address</label>
    <textarea name="address" id="address" class="form-control" rows="3"
        placeholder="e.g. 123 Rizal St., Barangay Poblacion, Makati City, Metro Manila">{{ old('address', $employee->address ?? '') }}</textarea>
</div>
