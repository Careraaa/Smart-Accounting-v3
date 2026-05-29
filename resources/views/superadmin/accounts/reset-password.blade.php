@extends('layouts.layout')

@section('content')
<div class="max-w-xl">
        <a href="{{ route('superadmin.accounts.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl text-xs font-semibold no-underline hover:border-gray-600 hover:text-gray-600 hover:bg-gray-50 transition-colors whitespace-nowrap cursor-pointer mb-4">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to accounts
        </a>
    <h1 class="text-xl font-extrabold text-gray-900 -tracking-[0.02em] mb-1">Reset password</h1>
    <p class="text-xs text-gray-400 mb-6">Generate a temporary password for {{ $user->first_name }} {{ $user->last_name }}.</p>

    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden mb-4">
        <div class="px-5 py-4 border-b border-gray-100 flex gap-3 items-start">
            <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <svg class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
            </div>
            <div>
                <p class="text-sm font-bold text-gray-900">Confirm reset</p>
                <p class="text-xs text-gray-400">Share the new password securely with the user</p>
            </div>
        </div>
        <div class="px-5 py-4">
            <div class="flex items-start gap-3 px-4 py-3 mb-5 rounded-lg bg-sky-50 border border-sky-200 text-sky-800 text-xs leading-relaxed">
                <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <strong>Email:</strong> <span class="font-mono text-sm">{{ $user->email }}</span><br>
                    <strong>Status:</strong> {{ ucfirst($user->status) }}
                </div>
            </div>

            <p class="text-xs text-gray-500 leading-relaxed mb-5">
                This generates a new temporary password and shows it once. Ask the user to change it after login.
            </p>

            <form action="{{ route('superadmin.accounts.perform-reset-password', $user->id) }}" method="POST">
                @csrf
                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('superadmin.accounts.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-gray-700 border border-gray-200 rounded-xl text-sm font-semibold hover:border-[#c8292a] hover:text-[#c8292a] hover:bg-red-50 transition-all duration-150 no-underline">Cancel</a>
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#c8292a] text-white rounded-xl text-sm font-bold hover:bg-[#a81f20] transition-all duration-150 shadow-lg shadow-red-700/30">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Generate password
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if(session('reset_password'))
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex gap-3 items-start">
                <div class="w-9 h-9 rounded-xl bg-green-50 text-green-600 flex items-center justify-center shrink-0">
                    <svg class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-900">Temporary password</p>
                    <p class="text-xs text-gray-400">Copy and share securely &mdash; it will not be shown again</p>
                </div>
            </div>
            <div class="px-5 py-4">
                <div class="flex items-start gap-2.5 px-4 py-3 mb-4 rounded-lg text-sm font-medium bg-green-50 border border-green-200 text-green-700">Password reset successful.</div>

                <div class="bg-gray-50 border-2 border-dashed border-emerald-500 rounded-xl px-4 py-4 mb-4">
                    <p class="text-[0.68rem] font-bold uppercase tracking-wider text-gray-500 mb-2.5">One-time password</p>
                    <div class="flex items-center gap-3 flex-wrap">
                        <code id="sa-temp-password" class="flex-1 min-w-0 break-all font-mono text-base font-bold text-gray-900 tracking-wider">{{ session('reset_password') }}</code>
                        <button type="button" onclick="copyPassword()" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white text-gray-700 border border-gray-200 rounded-lg text-xs font-semibold hover:border-[#c8292a] hover:text-[#c8292a] hover:bg-red-50 transition-all duration-150 shrink-0">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            Copy
                        </button>
                    </div>
                </div>

                <div class="flex items-start gap-2.5 px-4 py-3 mb-4 rounded-lg bg-amber-50 border border-amber-200 text-amber-900 text-xs leading-relaxed">
                    <strong>Important:</strong> Share this password immediately. The user should change it on next login.
                </div>

                <div class="flex items-center justify-end">
                    <a href="{{ route('superadmin.accounts.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl text-xs font-semibold no-underline hover:border-gray-600 hover:text-gray-600 hover:bg-gray-50 transition-colors whitespace-nowrap cursor-pointer">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Back to accounts
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
function copyPassword() {
    const el = document.getElementById('sa-temp-password');
    if (!el) return;
    const text = el.textContent.trim();
    navigator.clipboard.writeText(text).then(function () {
        alert('Copied to clipboard.');
    }).catch(function () {
        alert('Could not copy.');
    });
}
</script>
@endsection
