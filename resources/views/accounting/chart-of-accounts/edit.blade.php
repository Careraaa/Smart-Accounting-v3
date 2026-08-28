@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeSlideUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
.fade-up { animation:fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) both; }
</style>
@endpush

@section('content')
<div class="space-y-5 max-w-2xl">

    {{-- Flash --}}
    @foreach(['success','error'] as $t)
        @if(session($t))
        <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl text-sm font-medium animate-[fadeSlideUp_0.3s_ease] @switch($t) @case('success') bg-green-50 text-green-700 @break @case('error') bg-red-50 text-red-700 @break @endswitch">
            <span>{{ session($t) }}</span>
        </div>
        @endif
    @endforeach

    {{-- Header --}}
    <div class="fade-up">
        <a href="{{ route('accounting.chart-of-accounts.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl text-xs font-semibold no-underline hover:border-gray-600 hover:text-gray-600 hover:bg-gray-50 transition-colors whitespace-nowrap cursor-pointer mb-2">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back
        </a>
        <h1 class="text-xl font-bold text-gray-900 tracking-tight">Edit Account</h1>
        <p class="text-xs text-gray-400 mt-0.5">{{ $chartOfAccount->account_code }} – {{ $chartOfAccount->account_name }}</p>
    </div>

    {{-- Form --}}
    <div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form method="POST" action="{{ route('accounting.chart-of-accounts.update', $chartOfAccount) }}">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                {{-- Account Code --}}
                <div>
                    <label for="account_code" class="block text-xs font-bold uppercase tracking-wide text-gray-500 mb-1.5">
                        Account Code <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="account_code" id="account_code" value="{{ old('account_code', $chartOfAccount->account_code) }}" required
                           class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 outline-none transition-all focus:border-gray-400 focus:bg-white @error('account_code') border-red-300 @enderror">
                    @error('account_code')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Account Name --}}
                <div>
                    <label for="account_name" class="block text-xs font-bold uppercase tracking-wide text-gray-500 mb-1.5">
                        Account Name <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="account_name" id="account_name" value="{{ old('account_name', $chartOfAccount->account_name) }}" required
                           class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 outline-none transition-all focus:border-gray-400 focus:bg-white @error('account_name') border-red-300 @enderror">
                    @error('account_name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Account Type --}}
                <div>
                    <label for="account_type" class="block text-xs font-bold uppercase tracking-wide text-gray-500 mb-1.5">
                        Account Type <span class="text-rose-500">*</span>
                    </label>
                    <select name="account_type" id="account_type" required
                            class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 outline-none transition-all focus:border-gray-400 focus:bg-white @error('account_type') border-red-300 @enderror">
                        @foreach(['Asset', 'Liability', 'Equity', 'Revenue', 'Expense'] as $type)
                            <option value="{{ $type }}" {{ old('account_type', $chartOfAccount->account_type) === $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                    @error('account_type')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-xs font-bold uppercase tracking-wide text-gray-500 mb-1.5">
                        Description
                    </label>
                    <textarea name="description" id="description" rows="3"
                              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 resize-none outline-none transition-all focus:border-gray-400 focus:bg-white @error('description') border-red-300 @enderror">{{ old('description', $chartOfAccount->description) }}</textarea>
                    @error('description')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex gap-2 mt-6">
                <a href="{{ route('accounting.chart-of-accounts.index') }}"
                   class="flex-1 py-2.5 bg-white text-gray-600 border border-gray-200 rounded-xl text-sm font-semibold transition-all hover:bg-gray-50 active:scale-[0.97] text-center no-underline">
                    Cancel
                </a>
                <button type="submit"
                        class="flex-1 py-2.5 bg-gray-900 text-white rounded-xl text-sm font-semibold transition-all hover:bg-gray-800 active:scale-[0.97] cursor-pointer border-0">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
