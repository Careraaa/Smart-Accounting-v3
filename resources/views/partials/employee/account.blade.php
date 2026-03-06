<h5 class="mb-4">Account Information</h5>
<p class="text-muted mb-4" style="font-size:0.845rem;">
    This is the login account for the employee. The username is auto-generated from their name.
    The default password is <strong>password123</strong> — the employee can change this after logging in.
</p>

@if(!$isEdit)
{{-- CREATE: show username preview --}}
<div class="row">
    <div class="col-md-6 mb-4">
        <label class="form-label">Username <span class="text-muted fw-normal">(auto-generated)</span></label>
        <div class="input-group">
            <span class="input-group-text"><i class="feather-user" style="font-size:14px;"></i></span>
            <input type="text" id="usernamePreview" class="form-control bg-light"
                placeholder="Will be generated from first + last name" readonly>
        </div>
        <div class="form-text">Format: <code>firstname.lastname</code> — e.g. <code>juan.delacruz</code></div>
    </div>
    <div class="col-md-6 mb-4">
        <label class="form-label">Default Password</label>
        <div class="input-group">
            <span class="input-group-text"><i class="feather-lock" style="font-size:14px;"></i></span>
            <input type="text" class="form-control bg-light" value="password123" readonly>
        </div>
        <div class="form-text">Employee must change this after first login.</div>
    </div>
</div>

<div class="alert alert-warning mt-2" style="font-size:0.845rem;">
    <i class="feather-alert-triangle me-1"></i>
    Make sure to inform the employee of their username and default password after saving.
</div>

@else
{{-- EDIT: show current username read-only --}}
<div class="row">
    <div class="col-md-6 mb-4">
        <label class="form-label">Username</label>
        <div class="input-group">
            <span class="input-group-text"><i class="feather-user" style="font-size:14px;"></i></span>
            <input type="text" class="form-control bg-light" value="{{ $employee->username }}" readonly>
        </div>
        <div class="form-text">Username cannot be changed here.</div>
    </div>
    <div class="col-md-6 mb-4">
        <label class="form-label">Role</label>
        <div class="input-group">
            <span class="input-group-text"><i class="feather-shield" style="font-size:14px;"></i></span>
            <input type="text" class="form-control bg-light" value="{{ ucfirst($employee->role) }}" readonly>
        </div>
    </div>
</div>

<div class="alert alert-info mt-2" style="font-size:0.845rem;">
    <i class="feather-info me-1"></i>
    Password reset requests will be available in a future update. For now, employees can change their own password from their profile.
</div>
@endif