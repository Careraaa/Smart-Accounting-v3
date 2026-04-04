@php $readOnly = $readOnly ?? false; @endphp

<p class="emp-section-title-form">Account Information</p>
<p class="emp-section-sub">The username is auto-generated from the employee's name. The default password must be changed after first login.</p>

@if(!$isEdit)
{{-- CREATE: preview --}}
<div class="emp-row cols-2" style="margin-bottom:16px;">
    <div class="emp-field">
        <label class="emp-label">Username <span class="opt">(auto-generated)</span></label>
        <div class="emp-input-group">
            <span class="emp-input-group-text">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </span>
            <input type="text" id="usernamePreview" class="emp-input" placeholder="Will be generated from first + last name" readonly>
        </div>
        <p class="emp-hint">Format: <code>firstname.lastname</code> — e.g. <code>juan.delacruz</code></p>
    </div>
    <div class="emp-field">
        <label class="emp-label">Default Password</label>
        <div class="emp-input-group">
            <span class="emp-input-group-text">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M7 11V7a5 5 0 0110 0v4"/></svg>
            </span>
            <input type="text" id="defaultPassword" class="emp-input" readonly>
            <input type="hidden" id="hiddenPassword" name="generated_password">
        </div>
        <p class="emp-hint">Employee must change this after first login.</p>
    </div>
</div>
<div class="emp-alert warning">
    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    Make sure to inform the employee of their username and default password after saving.
</div>

@else
{{-- EDIT: read-only display --}}
<div class="emp-row cols-2" style="margin-bottom:16px;">
    <div class="emp-field">
        <label class="emp-label">Username</label>
        <div class="emp-input-group">
            <span class="emp-input-group-text">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </span>
            <input type="text" class="emp-input" value="{{ $employee->username }}" readonly>
        </div>
        <p class="emp-hint">Username cannot be changed here.</p>
    </div>
    <div class="emp-field">
        <label class="emp-label">Role</label>
        <div class="emp-input-group">
            <span class="emp-input-group-text">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </span>
            <input type="text" class="emp-input" value="{{ ucfirst($employee->role) }}" readonly>
        </div>
    </div>
</div>
<div class="emp-alert info">
    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px;"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
    @if(!$readOnly)
        If you forget your password, please contact HR for assistance.
    @else
        Your username and role cannot be changed. To change your password, use the "Change Password" option in your profile.
    @endif
</div>
@endif