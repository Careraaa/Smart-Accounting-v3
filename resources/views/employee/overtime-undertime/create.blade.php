@extends('layouts.layout')

@section('content')
<div class="min-h-screen bg-gray-50/60">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-8">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-7">
            <div>
                <p class="text-xs font-semibold tracking-widest text-gray-400 uppercase mb-1">Employee Portal</p>
                <h1 class="text-2xl font-extrabold tracking-tight text-gray-900 leading-tight">New OT / UT Request</h1>
                <p class="text-sm text-gray-500 mt-1">Create an overtime or undertime entry for HR review.</p>
            </div>
            <a href="{{ route('employee.overtime-undertime.index') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl text-xs font-semibold no-underline hover:border-gray-600 hover:text-gray-600 hover:bg-gray-50 transition-colors whitespace-nowrap cursor-pointer">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Requests
            </a>
        </div>

        {{-- Form Card --}}
        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
            <div class="flex items-center gap-2 px-5 py-4 border-b border-gray-100">
                <div class="w-2 h-2 rounded-full bg-gray-900"></div>
                <span class="text-sm font-bold text-gray-900">Record Details</span>
            </div>
            <div class="p-6">
                <form action="{{ route('employee.overtime-undertime.store') }}" method="POST" id="overtimeForm">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                        {{-- Type --}}
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-bold text-gray-600 uppercase tracking-wider">
                                Type <span class="text-red-500">*</span>
                            </label>
                            <select name="type"
                                class="w-full bg-gray-50 border @error('type') border-red-400 @else border-gray-200 @enderror rounded-xl px-4 py-2.5 text-sm font-medium text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 focus:border-gray-700 transition appearance-none"
                                required>
                                <option value="">Select Type</option>
                                <option value="overtime"  {{ old('type') === 'overtime'  ? 'selected' : '' }}>Overtime</option>
                                <option value="undertime" {{ old('type') === 'undertime' ? 'selected' : '' }}>Undertime</option>
                            </select>
                            @error('type')
                                <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Date --}}
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-bold text-gray-600 uppercase tracking-wider">
                                Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="date"
                                value="{{ old('date') }}"
                                class="w-full bg-gray-50 border @error('date') border-red-400 @else border-gray-200 @enderror rounded-xl px-4 py-2.5 text-sm font-medium text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 focus:border-gray-700 transition"
                                required>
                            @error('date')
                                <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Hours --}}
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-bold text-gray-600 uppercase tracking-wider">
                                Hours <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="hours"
                                value="{{ old('hours') }}" placeholder="0.5" step="0.5" min="0.5" max="24"
                                class="w-full bg-gray-50 border @error('hours') border-red-400 @else border-gray-200 @enderror rounded-xl px-4 py-2.5 text-sm font-medium text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 focus:border-gray-700 transition"
                                required>
                            <p class="text-xs text-gray-400">Between 0.5 and 24 hours</p>
                            @error('hours')
                                <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Reason --}}
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-bold text-gray-600 uppercase tracking-wider">
                                Reason <span class="text-red-500">*</span>
                            </label>
                            <textarea name="reason" rows="4"
                                class="w-full bg-gray-50 border @error('reason') border-red-400 @else border-gray-200 @enderror rounded-xl px-4 py-2.5 text-sm font-medium text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 focus:border-gray-700 transition resize-none"
                                required>{{ old('reason') }}</textarea>
                            @error('reason')
                                <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-center gap-3 mt-6 pt-5 border-t border-gray-100">
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white text-sm font-semibold rounded-xl transition-all shadow-sm">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            Create Record
                        </button>
                        <a href="{{ route('employee.overtime-undertime.index') }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 bg-white hover:bg-gray-50 border border-gray-200 text-sm font-semibold text-gray-600 rounded-xl transition-all">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection