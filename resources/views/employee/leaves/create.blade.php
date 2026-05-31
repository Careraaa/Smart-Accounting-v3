@extends('layouts.layout')

@push('styles')
<style>
@keyframes ll-form-in { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
.ll-form { animation:ll-form-in 0.35s ease-out; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50/60">
    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 py-8">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-7">
            <div>
                <p class="text-xs font-semibold tracking-widest text-gray-400 uppercase mb-1">Employee Portal</p>
                <h1 class="text-2xl font-extrabold tracking-tight text-gray-900 leading-tight">Request New Leave</h1>
                <p class="text-sm text-gray-500 mt-1">Choose dates, provide a short reason, and submit for approval.</p>
                <div class="flex flex-wrap gap-2 mt-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-gray-200 text-xs font-semibold text-gray-600 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                        {{ now()->format('l, F d, Y') }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-gray-200 text-xs font-semibold text-gray-600 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
                        Tip: double-check end date
                    </span>
                </div>
            </div>
            <div>
                <a href="{{ route('employee.leaves.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl text-xs font-semibold no-underline hover:border-gray-600 hover:text-gray-600 hover:bg-gray-50 transition-colors whitespace-nowrap cursor-pointer">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to Requests
                </a>
            </div>
        </div>

        {{-- Form card --}}
        <div class="ll-form max-w-3xl mx-auto">
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100">
                    <span class="w-2 h-2 rounded-full bg-gray-900"></span>
                    <span class="text-sm font-bold text-gray-800">Leave Details</span>
                </div>
                <div class="p-6">
                    <form action="{{ route('employee.leaves.store') }}" method="POST" id="leaveForm">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="leave_type_id" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Leave Type <span class="text-rose-500">*</span></label>
                                <select name="leave_type_id" id="leave_type_id" class="block w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition-all @error('leave_type_id') border-rose-300 ring-1 ring-rose-300 @enderror" required>
                                    <option value="">Select Leave Type</option>
                                    @foreach($leaveTypes as $type)
                                        <option value="{{ $type->id }}" {{ old('leave_type_id') == $type->id ? 'selected' : '' }}" data-available="{{ $type->balance->remaining_days }}">
                                            {{ $type->name }} ({{ $type->balance->remaining_days }}/{{ $type->balance->total_days }} days available)
                                        </option>
                                    @endforeach
                                </select>
                                @error('leave_type_id')
                                    <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="start_date" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Start Date <span class="text-rose-500">*</span></label>
                                <input type="date" name="start_date" id="start_date" class="block w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition-all @error('start_date') border-rose-300 ring-1 ring-rose-300 @enderror" value="{{ old('start_date') }}" required>
                                @error('start_date')
                                    <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="end_date" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">End Date <span class="text-rose-500">*</span></label>
                                <input type="date" name="end_date" id="end_date" class="block w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition-all @error('end_date') border-rose-300 ring-1 ring-rose-300 @enderror" value="{{ old('end_date') }}" required>
                                <div class="mt-1 text-xs text-gray-400">
                                    Duration: <span id="durationDays" class="font-bold font-mono text-gray-600">0</span> day(s)
                                    <span id="balanceWarning" class="text-rose-500 ml-2 font-semibold" style="display: none;"></span>
                                </div>
                                @error('end_date')
                                    <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="reason" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Reason <span class="text-rose-500">*</span></label>
                                <textarea name="reason" id="reason" rows="3" class="block w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition-all @error('reason') border-rose-300 ring-1 ring-rose-300 @enderror" required placeholder="Brief reason for your leave request...">{{ old('reason') }}</textarea>
                                @error('reason')
                                    <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex items-center gap-3 mt-6 pt-5 border-t border-gray-100">
                            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white text-sm font-bold rounded-xl transition-all shadow-sm">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M22 2L11 13"/><path stroke-linecap="round" stroke-linejoin="round" d="M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                                Submit Leave Request
                            </button>
                            <a href="{{ route('employee.leaves.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-bold rounded-xl transition-all">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M18 6L6 18M6 6l12 12"/></svg>
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    const leaveTypeSelect = document.getElementById('leave_type_id');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const durationSpan = document.getElementById('durationDays');
    const balanceWarning = document.getElementById('balanceWarning');

    function countWorkingDays(startDate, endDate) {
        let current = new Date(startDate.getTime());
        let workingDays = 0;

        while (current <= endDate) {
            const day = current.getDay();
            if (day !== 0 && day !== 6) {
                workingDays += 1;
            }
            current.setDate(current.getDate() + 1);
        }

        return workingDays;
    }

    function calculateDuration() {
        if (startDateInput.value && endDateInput.value) {
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);
            const daysDiff = countWorkingDays(startDate, endDate);
            durationSpan.textContent = daysDiff > 0 ? daysDiff : 0;

            if (daysDiff > 0 && leaveTypeSelect.value) {
                const availableDays = parseInt(leaveTypeSelect.options[leaveTypeSelect.selectedIndex].dataset.available);
                if (daysDiff > availableDays) {
                    balanceWarning.textContent = `⚠ Insufficient balance (need ${daysDiff}, have ${availableDays})`;
                    balanceWarning.style.display = 'inline';
                } else {
                    balanceWarning.style.display = 'none';
                }
            }
        }
    }

    startDateInput.addEventListener('change', calculateDuration);
    endDateInput.addEventListener('change', calculateDuration);
    leaveTypeSelect.addEventListener('change', calculateDuration);

    calculateDuration();
</script>
@endsection
