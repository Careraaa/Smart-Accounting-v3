@extends('layouts.layout')

@push('styles')
@include('superadmin.partials.prl-theme')
@endpush

@section('content')
<div class="prl-wrap">
    <a href="{{ route('superadmin.accounts.index') }}" class="prl-back-link">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Back to accounts
    </a>
    <h1 class="prl-page-title">Add account</h1>
    <p class="prl-page-sub">Create a user with role, credentials, and employment details.</p>

    @if ($errors->any())
        <div class="prl-alert error">
            <strong>Please fix the following:</strong>
            <ul style="margin:8px 0 0;padding-left:20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('superadmin.accounts.store') }}" method="POST">
        @csrf

        <div class="prl-card">
            <div class="prl-card-head">
                <div class="prl-card-head-icon red" aria-hidden="true">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div>
                    <p class="prl-card-head-title">Basic information</p>
                    <p class="prl-card-head-sub">Legal name and contact</p>
                </div>
            </div>
            <div class="prl-card-body">
                <div class="prl-form-row">
                    <div class="prl-field">
                        <label class="prl-lbl">First name <span class="req">*</span></label>
                        <input type="text" name="first_name" class="prl-ctrl @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" required>
                        @error('first_name')<span class="prl-err">{{ $message }}</span>@enderror
                    </div>
                    <div class="prl-field">
                        <label class="prl-lbl">Last name <span class="req">*</span></label>
                        <input type="text" name="last_name" class="prl-ctrl @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" required>
                        @error('last_name')<span class="prl-err">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="prl-form-row">
                    <div class="prl-field">
                        <label class="prl-lbl">Email <span class="req">*</span></label>
                        <input type="email" name="email" class="prl-ctrl @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                        @error('email')<span class="prl-err">{{ $message }}</span>@enderror
                    </div>
                    <div class="prl-field">
                        <label class="prl-lbl">Phone</label>
                        <input type="tel" name="phone" class="prl-ctrl @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                        @error('phone')<span class="prl-err">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="prl-card">
            <div class="prl-card-head">
                <div class="prl-card-head-icon blue" aria-hidden="true">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                </div>
                <div>
                    <p class="prl-card-head-title">Account access</p>
                    <p class="prl-card-head-sub">Username, role, and password</p>
                </div>
            </div>
            <div class="prl-card-body">
                <div class="prl-form-row">
                    <div class="prl-field">
                        <label class="prl-lbl">Username <span class="req">*</span></label>
                        <input type="text" name="username" class="prl-ctrl @error('username') is-invalid @enderror" value="{{ old('username') }}" required>
                        @error('username')<span class="prl-err">{{ $message }}</span>@enderror
                    </div>
                    <div class="prl-field">
                        <label class="prl-lbl">Role <span class="req">*</span></label>
                        <select name="role" class="prl-ctrl @error('role') is-invalid @enderror" required>
                            <option value="">— Select —</option>
                            <option value="superadmin" {{ old('role') === 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                            <option value="hr" {{ old('role') === 'hr' ? 'selected' : '' }}>HR</option>
                            <option value="accountant" {{ old('role') === 'accountant' ? 'selected' : '' }}>Accountant</option>
                            <option value="remittance_clerk" {{ old('role') === 'remittance_clerk' ? 'selected' : '' }}>Remittance Clerk</option>
                            <option value="qr_admin" {{ old('role') === 'qr_admin' ? 'selected' : '' }}>QR Admin</option>
                            <option value="employee" {{ old('role') === 'employee' ? 'selected' : '' }}>Employee</option>
                        </select>
                        @error('role')<span class="prl-err">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="prl-form-row">
                    <div class="prl-field">
                        <label class="prl-lbl">Password <span class="req">*</span></label>
                        <input type="password" name="password" class="prl-ctrl @error('password') is-invalid @enderror" required>
                        @error('password')<span class="prl-err">{{ $message }}</span>@enderror
                    </div>
                    <div class="prl-field">
                        <label class="prl-lbl">Confirm password <span class="req">*</span></label>
                        <input type="password" name="password_confirmation" class="prl-ctrl" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="prl-card">
            <div class="prl-card-head">
                <div class="prl-card-head-icon green" aria-hidden="true">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <p class="prl-card-head-title">Employment</p>
                    <p class="prl-card-head-sub">Optional HR fields</p>
                </div>
            </div>
            <div class="prl-card-body">
                <div class="prl-form-row">
                    <div class="prl-field">
                        <label class="prl-lbl">Position</label>
                        <input type="text" name="position" class="prl-ctrl @error('position') is-invalid @enderror" value="{{ old('position') }}">
                        @error('position')<span class="prl-err">{{ $message }}</span>@enderror
                    </div>
                    <div class="prl-field">
                        <label class="prl-lbl">Department</label>
                        <input type="text" name="department" class="prl-ctrl @error('department') is-invalid @enderror" value="{{ old('department') }}">
                        @error('department')<span class="prl-err">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="prl-form-row">
                    <div class="prl-field">
                        <label class="prl-lbl">Date of hire</label>
                        <input type="date" name="date_of_hire" class="prl-ctrl @error('date_of_hire') is-invalid @enderror" value="{{ old('date_of_hire') }}">
                        @error('date_of_hire')<span class="prl-err">{{ $message }}</span>@enderror
                    </div>
                    <div class="prl-field">
                        <label class="prl-lbl">Salary rate</label>
                        <input type="number" name="salary_rate" class="prl-ctrl @error('salary_rate') is-invalid @enderror" value="{{ old('salary_rate') }}" step="0.01" min="0">
                        @error('salary_rate')<span class="prl-err">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="prl-form-footer" style="border-top:none;padding-top:0;margin-top:8px;">
                    <a href="{{ route('superadmin.accounts.index') }}" class="prl-btn-cancel">Cancel</a>
                    <button type="submit" class="prl-btn-generate">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Create account
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
