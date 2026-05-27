@extends('layouts.layout')

@push('styles')
<style>
form input:focus-visible,
form select:focus-visible,
form textarea:focus-visible,
form button:focus-visible,
form a:focus-visible {
    outline: none !important;
}
</style>
@endpush

@section('content')
<div>

    {{-- Flash --}}
    @if($errors->any())
    <div class="flex items-start gap-2.5 p-4 rounded-xl text-sm font-medium mb-5 bg-amber-50 border border-amber-200 text-amber-700">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="shrink-0 mt-0.5"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
        <div>
            <strong>Please fix the errors:</strong>
            <ul class="mt-1.5 ml-4 m-0">
                @foreach($errors->all() as $error)
                    <li class="text-xs mb-0.5">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    {{-- Topbar --}}
    <div class="flex items-start justify-between gap-4 mb-6 flex-wrap">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight m-0">Edit Leave Type</h1>
            <p class="text-xs text-gray-400 m-0 mt-0.5">Update the details for <strong>{{ $leaveType->name }}</strong></p>
        </div>
        <a href="{{ route('leave-type.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl text-xs font-semibold no-underline hover:border-gray-600 hover:text-gray-600 hover:bg-gray-50 transition-colors whitespace-nowrap cursor-pointer">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Leave Types
        </a>
    </div>

    {{-- Form card --}}
    <div class="max-w-2xl mx-auto">
        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">

            <div class="px-6 py-5 border-b border-gray-50 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gray-100 text-gray-400 flex items-center justify-center shrink-0">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-extrabold text-gray-900 m-0 leading-tight">Leave Type Details</p>
                    <p class="text-xs text-gray-400 m-0">All fields marked <span class="text-amber-600">*</span> are required</p>
                </div>
            </div>

            <div class="p-6">
                <form method="POST" action="{{ route('leave-type.update', $leaveType) }}" id="ltForm" novalidate>
                    @csrf
                    @method('PUT')

                    {{-- Name & Abbreviation --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                        <div>
                            <label for="name" class="block text-[0.7rem] font-bold uppercase tracking-wide text-gray-500 mb-1.5">Leave Type Name <span class="text-amber-600">*</span></label>
                            <input type="text" name="name" id="name"
                                class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm text-gray-900 bg-white outline-none transition-colors focus:border-gray-400 focus:ring-2 focus:ring-gray-200/50 @error('name') border-amber-500 @enderror"
                                placeholder="e.g., Vacation Leave"
                                value="{{ old('name', $leaveType->name) }}" required>
                            @error('name')
                                <span class="text-xs text-amber-600 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="abbreviation" class="block text-[0.7rem] font-bold uppercase tracking-wide text-gray-500 mb-1.5">Abbreviation</label>
                            <input type="text" name="abbreviation" id="abbreviation"
                                class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm text-gray-900 bg-white outline-none transition-colors focus:border-gray-400 focus:ring-2 focus:ring-gray-200/50 @error('abbreviation') border-amber-500 @enderror"
                                placeholder="e.g., VL"
                                value="{{ old('abbreviation', $leaveType->abbreviation) }}" maxlength="5">
                            @error('abbreviation')
                                <span class="text-xs text-amber-600 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Days & Status --}}
                    <div class="text-[0.68rem] font-bold uppercase tracking-[0.12em] text-gray-400 border-b border-gray-50 pb-2.5 mb-5">Policy</div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                        <div>
                            <label for="days_allowed" class="block text-[0.7rem] font-bold uppercase tracking-wide text-gray-500 mb-1.5">Days Allowed <span class="text-amber-600">*</span></label>
                            <input type="number" name="days_allowed" id="days_allowed"
                                class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm text-gray-900 bg-white outline-none transition-colors focus:border-gray-400 focus:ring-2 focus:ring-gray-200/50 @error('days_allowed') border-amber-500 @enderror"
                                placeholder="0"
                                value="{{ old('days_allowed', $leaveType->days_allowed) }}" min="0" required>
                            @error('days_allowed')
                                <span class="text-xs text-amber-600 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="status" class="block text-[0.7rem] font-bold uppercase tracking-wide text-gray-500 mb-1.5">Status <span class="text-amber-600">*</span></label>
                            <div class="relative">
                                <select name="status" id="status"
                                    class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 pr-9 text-sm text-gray-900 bg-white outline-none transition-colors focus:border-gray-400 focus:ring-2 focus:ring-gray-200/50 appearance-none @error('status') border-amber-500 @enderror"
                                    required>
                                    <option value="">— Select Status —</option>
                                    <option value="active"   @selected(old('status', $leaveType->status) === 'active')>Active</option>
                                    <option value="inactive" @selected(old('status', $leaveType->status) === 'inactive')>Inactive</option>
                                </select>
                                <svg class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                            @error('status')
                                <span class="text-xs text-amber-600 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Carry over --}}
                    <div class="mb-5 last:mb-0">
                        <label class="block text-[0.7rem] font-bold uppercase tracking-wide text-gray-500 mb-1.5">Carry Over Settings</label>
                        <label class="flex items-center gap-3 p-3.5 bg-gray-50/50 border border-gray-200 rounded-lg cursor-pointer">
                            <input type="checkbox" name="carry_over" id="carry_over" value="1"
                                {{ old('carry_over', $leaveType->carry_over) ? 'checked' : '' }}
                                class="w-4 h-4 accent-gray-600 shrink-0">
                            <div>
                                <div class="text-sm text-gray-600 font-medium">Allow carry over</div>
                                <div class="text-xs text-gray-400 mt-0.5">Unused days roll over to the next period</div>
                            </div>
                        </label>
                        @error('carry_over')
                            <span class="text-xs text-amber-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="text-[0.68rem] font-bold uppercase tracking-[0.12em] text-gray-400 border-b border-gray-50 pb-2.5 mb-5">Additional Info</div>
                    <div class="mb-0 last:mb-0">
                        <label for="description" class="block text-[0.7rem] font-bold uppercase tracking-wide text-gray-500 mb-1.5">Description</label>
                        <textarea name="description" id="description"
                            class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm text-gray-900 bg-white outline-none transition-colors focus:border-gray-400 focus:ring-2 focus:ring-gray-200/50 resize-y min-h-[100px] @error('description') border-amber-500 @enderror"
                            placeholder="Enter a brief description…">{{ old('description', $leaveType->description) }}</textarea>
                        @error('description')
                            <span class="text-xs text-amber-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                </form>
            </div>

            <div class="px-6 py-4 border-t border-gray-50 bg-gray-50/50 flex items-center gap-2.5">
                <button type="submit" form="ltForm" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 text-white border-none rounded-xl text-xs font-bold cursor-pointer hover:bg-black transition-colors">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Update Leave Type
                </button>
                <a href="{{ route('leave-type.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-white text-gray-600 border border-gray-200 rounded-xl text-xs font-semibold no-underline hover:border-gray-600 hover:text-gray-600 hover:bg-gray-50 transition-colors cursor-pointer">Cancel</a>
            </div>

        </div>
    </div>

</div>
@endsection
