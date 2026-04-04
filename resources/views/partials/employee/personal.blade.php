@php $readOnly = $readOnly ?? false; @endphp

<p class="emp-section-title-form">Personal Information</p>

<div class="emp-divider">Full Name</div>
<div class="emp-row cols-3" style="margin-bottom:16px;">
    <div class="emp-field">
        <label class="emp-label">First Name <span class="req">*</span></label>
        <input type="text" name="first_name" id="first_name"
            class="emp-input @error('first_name') is-invalid @enderror"
            placeholder="e.g. Juan"
            value="{{ old('first_name', $employee->first_name ?? '') }}"
            {{ $readOnly ? 'readonly' : 'required' }}>
        @error('first_name')<span class="emp-invalid">{{ $message }}</span>@enderror
    </div>
    <div class="emp-field">
        <label class="emp-label">Middle Name <span class="opt">(optional)</span></label>
        <input type="text" name="middle_name" id="middle_name"
            class="emp-input"
            placeholder="e.g. Santos"
            value="{{ old('middle_name', $employee->middle_name ?? '') }}"
            {{ $readOnly ? 'readonly' : '' }}>
    </div>
    <div class="emp-field">
        <label class="emp-label">Last Name <span class="req">*</span></label>
        <input type="text" name="last_name" id="last_name"
            class="emp-input @error('last_name') is-invalid @enderror"
            placeholder="e.g. Dela Cruz"
            value="{{ old('last_name', $employee->last_name ?? '') }}"
            {{ $readOnly ? 'readonly' : 'required' }}>
        @error('last_name')<span class="emp-invalid">{{ $message }}</span>@enderror
    </div>
</div>

<div class="emp-row cols-2" style="margin-bottom:16px;">
    <div class="emp-field">
        <label class="emp-label">Gender <span class="req">*</span></label>
        <div class="emp-select-wrap">
            <select name="gender" id="gender"
                class="emp-select @error('gender') is-invalid @enderror"
                {{ $readOnly ? 'disabled' : 'required' }}>
                <option value="">— Select —</option>
                <option value="male"              @selected(old('gender', $employee->gender ?? '') == 'male')>Male</option>
                <option value="female"            @selected(old('gender', $employee->gender ?? '') == 'female')>Female</option>
                <option value="prefer_not_to_say" @selected(old('gender', $employee->gender ?? '') == 'prefer_not_to_say')>Prefer not to say</option>
            </select>
            <svg class="emp-chevron" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </div>
        @error('gender')<span class="emp-invalid">{{ $message }}</span>@enderror
    </div>
</div>

<div class="emp-divider">Contact Information</div>
<div class="emp-row cols-2" style="margin-bottom:16px;">
    <div class="emp-field">
        <label class="emp-label">Email <span class="opt">(optional)</span></label>
        <input type="email" name="email" id="email"
            class="emp-input @error('email') is-invalid @enderror"
            placeholder="e.g. juan.delacruz@email.com"
            value="{{ old('email', $employee->email ?? '') }}"
            {{ $readOnly ? 'readonly' : '' }}>
        @error('email')<span class="emp-invalid">{{ $message }}</span>@enderror
    </div>
    <div class="emp-field">
        <label class="emp-label">Phone <span class="req">*</span></label>
        <input type="tel" name="phone" id="phone"
            class="emp-input @error('phone') is-invalid @enderror"
            placeholder="09XXXXXXXXX" maxlength="13"
            pattern="(09\d{9}|\+639\d{9})"
            oninput="this.value = this.value.replace(/[^0-9+]/g, '')"
            value="{{ old('phone', $employee->phone ?? '') }}"
            {{ $readOnly ? 'readonly' : 'required' }}>
        @error('phone')<span class="emp-invalid">{{ $message }}</span>@enderror
    </div>
</div>

<div class="emp-divider">Personal Details</div>
<div class="emp-row cols-3" style="margin-bottom:16px;">
    <div class="emp-field">
        <label class="emp-label">Civil Status <span class="req">*</span></label>
        <div class="emp-select-wrap">
            <select name="civil_status" id="civil_status"
                class="emp-select @error('civil_status') is-invalid @enderror"
                onchange="document.getElementById('spouseField').style.display = this.value === 'married' ? 'block' : 'none'"
                {{ $readOnly ? 'disabled' : 'required' }}>
                <option value="">— Select —</option>
                <option value="single"    @selected(old('civil_status', $employee->civil_status ?? '') == 'single')>Single</option>
                <option value="married"   @selected(old('civil_status', $employee->civil_status ?? '') == 'married')>Married</option>
                <option value="widowed"   @selected(old('civil_status', $employee->civil_status ?? '') == 'widowed')>Widowed</option>
                <option value="separated" @selected(old('civil_status', $employee->civil_status ?? '') == 'separated')>Separated</option>
            </select>
            <svg class="emp-chevron" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </div>
        @error('civil_status')<span class="emp-invalid">{{ $message }}</span>@enderror
    </div>
    <div class="emp-field" id="spouseField" style="display:{{ (old('civil_status', $employee->civil_status ?? '') === 'married') ? 'block' : 'none' }};">
        <label class="emp-label">Spouse Name</label>
        <input type="text" name="spouse_name" id="spouse_name"
            class="emp-input"
            placeholder="Full name of spouse"
            value="{{ old('spouse_name', $employee->spouse_name ?? '') }}"
            {{ $readOnly ? 'readonly' : '' }}>
    </div>
    <div class="emp-field">
        <label class="emp-label">Date of Birth <span class="req">*</span></label>
        <input type="date" name="date_of_birth" id="date_of_birth"
            class="emp-input @error('date_of_birth') is-invalid @enderror"
            value="{{ old('date_of_birth', isset($employee->date_of_birth) ? $employee->date_of_birth?->format('Y-m-d') : '') }}"
            {{ $readOnly ? 'readonly' : 'required' }}>
        @error('date_of_birth')<span class="emp-invalid">{{ $message }}</span>@enderror
    </div>
</div>

<div class="emp-divider">Birth &amp; Education</div>
<div class="emp-row cols-2" style="margin-bottom:16px;">
    <div class="emp-field">
        <label class="emp-label">Place of Birth <span class="req">*</span></label>
        <input type="text" name="place_of_birth" id="place_of_birth"
            class="emp-input @error('place_of_birth') is-invalid @enderror"
            placeholder="e.g. Manila, Metro Manila"
            value="{{ old('place_of_birth', $employee->place_of_birth ?? '') }}"
            {{ $readOnly ? 'readonly' : 'required' }}>
        @error('place_of_birth')<span class="emp-invalid">{{ $message }}</span>@enderror
    </div>
    <div class="emp-field">
        <label class="emp-label">Educational Attainment <span class="req">*</span></label>
        <div class="emp-select-wrap">
            <select name="educational_attainment" id="educational_attainment"
                class="emp-select @error('educational_attainment') is-invalid @enderror"
                {{ $readOnly ? 'disabled' : 'required' }}>
                <option value="">— Select —</option>
                @foreach(['Elementary','High School','Senior High School / K-12','Vocational / TESDA',"College (Bachelor's)"] as $ea)
                    <option value="{{ $ea }}" @selected(old('educational_attainment', $employee->educational_attainment ?? '') == $ea)>{{ $ea }}</option>
                @endforeach
            </select>
            <svg class="emp-chevron" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </div>
        @error('educational_attainment')<span class="emp-invalid">{{ $message }}</span>@enderror
    </div>
</div>

<div class="emp-divider">Address</div>
<div class="emp-row cols-2" style="margin-bottom:4px;">
    <div class="emp-field">
        <label class="emp-label">Street / House No. <span class="req">*</span></label>
        <input type="text" name="address_street" id="address_street"
            class="emp-input @error('address_street') is-invalid @enderror"
            placeholder="e.g. 123 Rizal St."
            value="{{ old('address_street', $employee->address_street ?? '') }}"
            {{ $readOnly ? 'readonly' : 'required' }}>
        @error('address_street')<span class="emp-invalid">{{ $message }}</span>@enderror
    </div>
    <div class="emp-field">
        <label class="emp-label">Barangay <span class="req">*</span></label>
        <input type="text" name="address_barangay" id="address_barangay"
            class="emp-input @error('address_barangay') is-invalid @enderror"
            placeholder="e.g. Brgy. Poblacion"
            value="{{ old('address_barangay', $employee->address_barangay ?? '') }}"
            {{ $readOnly ? 'readonly' : 'required' }}>
        @error('address_barangay')<span class="emp-invalid">{{ $message }}</span>@enderror
    </div>
    <div class="emp-field">
        <label class="emp-label">City / Municipality <span class="req">*</span></label>
        <input type="text" name="address_city" id="address_city"
            class="emp-input @error('address_city') is-invalid @enderror"
            placeholder="e.g. Makati City"
            value="{{ old('address_city', $employee->address_city ?? '') }}"
            {{ $readOnly ? 'readonly' : 'required' }}>
        @error('address_city')<span class="emp-invalid">{{ $message }}</span>@enderror
    </div>
    <div class="emp-field">
        <label class="emp-label">Province <span class="req">*</span></label>
        <input type="text" name="address_province" id="address_province"
            class="emp-input @error('address_province') is-invalid @enderror"
            placeholder="e.g. Metro Manila"
            value="{{ old('address_province', $employee->address_province ?? '') }}"
            {{ $readOnly ? 'readonly' : 'required' }}>
        @error('address_province')<span class="emp-invalid">{{ $message }}</span>@enderror
    </div>
</div>
<input type="hidden" name="address" id="address_combined">