@extends('layouts.layout')

@php
$inputClass = 'w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-900 bg-gray-50 outline-none transition-all duration-150 focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-200/50';
$labelClass = 'block text-xs font-semibold text-gray-700 mb-1.5';
$errClass   = 'text-xs text-red-500 mt-1 block';
$reqClass   = 'text-red-500 ml-0.5';
@endphp

@section('content')
<div class="max-w-3xl">
    <a href="{{ route('superadmin.accounts.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-400 hover:text-[#c8292a] no-underline transition-colors duration-150 mb-4">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Back to accounts
    </a>
    <h1 class="text-xl font-extrabold text-gray-900 -tracking-[0.02em] mb-1">Add account</h1>
    <p class="text-xs text-gray-400 mb-6">Create a user with role, credentials, and employment details.</p>

    @if ($errors->any())
        <div class="flex items-start gap-2.5 px-4 py-3 mb-5 rounded-xl text-sm font-medium bg-red-50 border border-red-200 text-red-700">
            <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
            <div>
                <strong>Please fix the following:</strong>
                <ul class="mt-1.5 pl-4 list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form action="{{ route('superadmin.accounts.store') }}" method="POST">
        @csrf

        {{-- Basic Information --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden mb-4">
            <div class="px-5 py-4 border-b border-gray-100 flex gap-3 items-start">
                <div class="w-9 h-9 rounded-xl bg-red-50 text-[#c8292a] flex items-center justify-center shrink-0">
                    <svg class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-900">Basic information</p>
                    <p class="text-xs text-gray-400">Legal name and contact</p>
                </div>
            </div>
            <div class="px-5 py-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="{{ $labelClass }}">First name <span class="{{ $reqClass }}">*</span></label>
                        <input type="text" name="first_name" class="{{ $inputClass }} @error('first_name') border-red-400 bg-red-50 @enderror" value="{{ old('first_name') }}" placeholder="Juan" required>
                        @error('first_name')<span class="{{ $errClass }}">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">Last name <span class="{{ $reqClass }}">*</span></label>
                        <input type="text" name="last_name" class="{{ $inputClass }} @error('last_name') border-red-400 bg-red-50 @enderror" value="{{ old('last_name') }}" placeholder="Dela Cruz" required>
                        @error('last_name')<span class="{{ $errClass }}">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="{{ $labelClass }}">Email <span class="{{ $reqClass }}">*</span></label>
                        <input type="email" name="email" class="{{ $inputClass }} @error('email') border-red-400 bg-red-50 @enderror" value="{{ old('email') }}" placeholder="juan.delacruz@example.com" required>
                        @error('email')<span class="{{ $errClass }}">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">Phone</label>
                        <input type="tel" name="phone" class="{{ $inputClass }} @error('phone') border-red-400 bg-red-50 @enderror" value="{{ old('phone') }}" placeholder="09173458216" inputmode="numeric" autocomplete="tel" maxlength="11" pattern="09\d{9}" data-digits-only>
                        @error('phone')<span class="{{ $errClass }}">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Account Access --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden mb-4">
            <div class="px-5 py-4 border-b border-gray-100 flex gap-3 items-start">
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-900">Account access</p>
                    <p class="text-xs text-gray-400">Username, role, and password</p>
                </div>
            </div>
            <div class="px-5 py-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="{{ $labelClass }}">Username <span class="{{ $reqClass }}">*</span></label>
                        <input type="text" name="username" class="{{ $inputClass }} @error('username') border-red-400 bg-red-50 @enderror" value="{{ old('username') }}" placeholder="juandelacruz" required>
                        @error('username')<span class="{{ $errClass }}">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">Role <span class="{{ $reqClass }}">*</span></label>
                        <select name="role" class="{{ $inputClass }} @error('role') border-red-400 bg-red-50 @enderror" required>
                            <option value="">&mdash; Select &mdash;</option>
                            <option value="hr" {{ old('role') === 'hr' ? 'selected' : '' }}>HR</option>
                            <option value="accountant" {{ old('role') === 'accountant' ? 'selected' : '' }}>Accountant</option>
                            <option value="remittance_clerk" {{ old('role') === 'remittance_clerk' ? 'selected' : '' }}>Remittance Clerk</option>
                            <option value="qr_admin" {{ old('role') === 'qr_admin' ? 'selected' : '' }}>QR Admin</option>
                            <option value="employee" {{ old('role') === 'employee' ? 'selected' : '' }}>Employee</option>
                        </select>
                        @error('role')<span class="{{ $errClass }}">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="{{ $labelClass }}">Password <span class="{{ $reqClass }}">*</span></label>
                        <input type="password" name="password" class="{{ $inputClass }} @error('password') border-red-400 bg-red-50 @enderror" required>
                        @error('password')<span class="{{ $errClass }}">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">Confirm password <span class="{{ $reqClass }}">*</span></label>
                        <input type="password" name="password_confirmation" class="{{ $inputClass }}" required>
                    </div>
                </div>
            </div>
        </div>

        {{-- Employment --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden mb-4">
            <div class="px-5 py-4 border-b border-gray-100 flex gap-3 items-start">
                <div class="w-9 h-9 rounded-xl bg-green-50 text-green-600 flex items-center justify-center shrink-0">
                    <svg class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-900">Employment</p>
                    <p class="text-xs text-gray-400">Optional HR fields</p>
                </div>
            </div>
            <div class="px-5 py-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="{{ $labelClass }}">Position</label>
                        <input type="text" name="position" class="{{ $inputClass }} @error('position') border-red-400 bg-red-50 @enderror" value="{{ old('position') }}" placeholder="Accounting Clerk">
                        @error('position')<span class="{{ $errClass }}">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">Department</label>
                        <select name="department" class="{{ $inputClass }} @error('department') border-red-400 bg-red-50 @enderror">
                            <option value="">&mdash; Select &mdash;</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept }}" {{ old('department') === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                            @endforeach
                        </select>
                        @error('department')<span class="{{ $errClass }}">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="{{ $labelClass }}">Date of hire</label>
                        <input type="date" name="date_of_hire" class="{{ $inputClass }} @error('date_of_hire') border-red-400 bg-red-50 @enderror" value="{{ old('date_of_hire') }}">
                        @error('date_of_hire')<span class="{{ $errClass }}">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">Salary rate</label>
                        <input type="number" name="salary_rate" class="{{ $inputClass }} @error('salary_rate') border-red-400 bg-red-50 @enderror" value="{{ old('salary_rate') }}" placeholder="25000.00" inputmode="decimal" step="0.01" min="0" data-decimal-only>
                        @error('salary_rate')<span class="{{ $errClass }}">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 mt-6 pt-5 border-t border-gray-100">
                    <a href="{{ route('superadmin.accounts.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-gray-700 border border-gray-200 rounded-xl text-sm font-semibold hover:border-[#c8292a] hover:text-[#c8292a] hover:bg-red-50 transition-all duration-150 no-underline">Cancel</a>
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#c8292a] text-white rounded-xl text-sm font-bold hover:bg-[#a81f20] transition-all duration-150 shadow-lg shadow-red-700/30">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Create account
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const normalizeDigitsOnly = (el) => {
        const digits = (el.value || '').replace(/[^\d]/g, '');
        if (el.value !== digits) el.value = digits;
    };

    const normalizeDecimalOnly = (el) => {
        const raw = (el.value || '');
        let cleaned = raw.replace(/[^\d.]/g, '');
        const firstDot = cleaned.indexOf('.');
        if (firstDot !== -1) {
            cleaned = cleaned.slice(0, firstDot + 1) + cleaned.slice(firstDot + 1).replace(/\./g, '');
        }
        if (el.value !== cleaned) el.value = cleaned;
    };

    document.addEventListener('input', (e) => {
        const el = e.target;
        if (!(el instanceof HTMLInputElement)) return;
        if (el.hasAttribute('data-digits-only')) normalizeDigitsOnly(el);
        if (el.hasAttribute('data-decimal-only')) normalizeDecimalOnly(el);
    });

    document.addEventListener('keydown', (e) => {
        const el = e.target;
        if (!(el instanceof HTMLInputElement)) return;
        if (!el.hasAttribute('data-decimal-only')) return;
        if (e.key === 'e' || e.key === 'E' || e.key === '+' || e.key === '-') e.preventDefault();
    });

    document.querySelectorAll('input[data-digits-only]').forEach(normalizeDigitsOnly);
    document.querySelectorAll('input[data-decimal-only]').forEach(normalizeDecimalOnly);
})();
</script>
@endpush
