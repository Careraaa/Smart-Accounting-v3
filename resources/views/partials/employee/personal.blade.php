<h5 class="mb-4">Personal Information</h5>

<div class="row">
    <div class="col-md-4 mb-3">
        <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
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
        <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
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
        <label for="email" class="form-label">Email <span class="text-muted fw-normal">(optional)</span></label>
        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
            placeholder="e.g. juan.delacruz@email.com" value="{{ old('email', $employee->email ?? '') }}">
        @error('email')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
        <input type="tel" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror"
            placeholder="09XXXXXXXXX or +639XXXXXXXXX" maxlength="13" pattern="(09\d{9}|\+639\d{9})"
            oninput="this.value = this.value.replace(/[^0-9+]/g, '')"
            value="{{ old('phone', $employee->phone ?? '') }}" required>
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
            <option value="single"    {{ old('civil_status', $employee->civil_status ?? '') == 'single'    ? 'selected' : '' }}>Single</option>
            <option value="married"   {{ old('civil_status', $employee->civil_status ?? '') == 'married'   ? 'selected' : '' }}>Married</option>
            <option value="widowed"   {{ old('civil_status', $employee->civil_status ?? '') == 'widowed'   ? 'selected' : '' }}>Widowed</option>
            <option value="separated" {{ old('civil_status', $employee->civil_status ?? '') == 'separated' ? 'selected' : '' }}>Separated</option>
        </select>
    </div>
    <div class="col-md-4 mb-3">
        <label for="spouse_name" class="form-label">Spouse Name</label>
        <input type="text" name="spouse_name" id="spouse_name" class="form-control"
            placeholder="N/A" value="{{ old('spouse_name', $employee->spouse_name ?? '') }}">
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
        <select name="educational_attainment" id="educational_attainment" class="form-select">
            <option value="">-- Select --</option>
            <option value="Elementary"               {{ old('educational_attainment', $employee->educational_attainment ?? '') == 'Elementary'               ? 'selected' : '' }}>Elementary</option>
            <option value="High School"              {{ old('educational_attainment', $employee->educational_attainment ?? '') == 'High School'              ? 'selected' : '' }}>High School</option>
            <option value="Senior High School / K-12"{{ old('educational_attainment', $employee->educational_attainment ?? '') == 'Senior High School / K-12'? 'selected' : '' }}>Senior High School / K-12</option>
            <option value="Vocational / TESDA"       {{ old('educational_attainment', $employee->educational_attainment ?? '') == 'Vocational / TESDA'       ? 'selected' : '' }}>Vocational / TESDA</option>
            <option value="College (Bachelor's)"     {{ old('educational_attainment', $employee->educational_attainment ?? '') == "College (Bachelor's)"     ? 'selected' : '' }}>College (Bachelor's)</option>
        </select>
    </div>
</div>

{{-- Split address fields --}}
@php
    $addrStreet   = '';
    $addrBarangay = '';
    $addrCity     = '';
    $addrProvince = '';
    $savedAddress = old('address', $employee->address ?? '');
    if ($savedAddress && str_starts_with(trim($savedAddress), '{')) {
        $decoded = json_decode($savedAddress, true);
        if ($decoded) {
            $addrStreet   = $decoded['street']   ?? '';
            $addrBarangay = $decoded['barangay'] ?? '';
            $addrCity     = $decoded['city']     ?? '';
            $addrProvince = $decoded['province'] ?? '';
        }
    } else {
        $addrStreet = $savedAddress; // legacy plain text fallback
    }
@endphp

<div class="row">
    <div class="col-12 mb-2">
        <label class="form-label mb-0">Address <span class="text-danger">*</span></label>
    </div>
    <div class="col-md-6 mb-3">
        <input type="text" name="address_street" id="address_street"
            class="form-control @error('address') is-invalid @enderror"
            placeholder="Street / House No. (e.g. 123 Rizal St.)"
            value="{{ old('address_street', $addrStreet) }}" required>
    </div>
    <div class="col-md-6 mb-3">
        <input type="text" name="address_barangay" id="address_barangay"
            class="form-control @error('address') is-invalid @enderror"
            placeholder="Barangay (e.g. Brgy. Poblacion)"
            value="{{ old('address_barangay', $addrBarangay) }}" required>
    </div>
    <div class="col-md-6 mb-3">
        <input type="text" name="address_city" id="address_city"
            class="form-control @error('address') is-invalid @enderror"
            placeholder="City / Municipality (e.g. Makati City)"
            value="{{ old('address_city', $addrCity) }}" required>
        @error('address')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <input type="text" name="address_province" id="address_province"
            class="form-control @error('address') is-invalid @enderror"
            placeholder="Province (e.g. Metro Manila)"
            value="{{ old('address_province', $addrProvince) }}" required>
    </div>
    <input type="hidden" name="address" id="address_combined">
</div>