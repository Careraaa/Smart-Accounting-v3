@extends('layouts.layout')

@section('content')
<div class="min-h-screen bg-gray-50/60">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-8">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-7">
            <div>
                <p class="text-xs font-semibold tracking-widest text-gray-400 uppercase mb-1">Employee Portal</p>
                <h1 class="text-2xl font-extrabold tracking-tight text-gray-900 leading-tight">Edit OT / UT Request</h1>
                <p class="text-sm text-gray-500 mt-1">Update your request details while it's still pending.</p>
                <div class="flex items-center gap-2 mt-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-gray-200 text-xs font-semibold text-gray-600 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                        {{ $overtimeUndertime->date->format('M d, Y') }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-gray-200 text-xs font-semibold text-gray-600 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
                        {{ number_format($overtimeUndertime->hours, 2) }}h
                    </span>
                </div>
            </div>
            <a href="{{ route('employee.overtime-undertime.show', $overtimeUndertime->id) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Back to Details
            </a>
        </div>

        {{-- Form Card --}}
        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-gray-900"></div>
                    <span class="text-sm font-bold text-gray-900">Update Details</span>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">Pending</span>
            </div>
            <div class="p-6">
                <form action="{{ route('employee.overtime-undertime.update', $overtimeUndertime->id) }}" method="POST" id="overtimeForm">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                        {{-- Type --}}
                        <div class="flex flex-col gap-1.5">
                            <label for="type" class="text-xs font-bold text-gray-600 uppercase tracking-wider">
                                Request Type <span class="text-red-500">*</span>
                            </label>
                            <select name="type" id="type"
                                class="w-full bg-gray-50 border @error('type') border-red-400 @else border-gray-200 @enderror rounded-xl px-4 py-2.5 text-sm font-medium text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 focus:border-gray-700 transition appearance-none"
                                required>
                                <option value="">Select Type</option>
                                @foreach($types as $value => $label)
                                    <option value="{{ $value }}" {{ old('type', $overtimeUndertime->type) == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type')
                                <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Date --}}
                        <div class="flex flex-col gap-1.5">
                            <label for="date" class="text-xs font-bold text-gray-600 uppercase tracking-wider">
                                Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="date" id="date"
                                value="{{ old('date', $overtimeUndertime->date->format('Y-m-d')) }}"
                                max="{{ date('Y-m-d') }}"
                                class="w-full bg-gray-50 border @error('date') border-red-400 @else border-gray-200 @enderror rounded-xl px-4 py-2.5 text-sm font-medium text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 focus:border-gray-700 transition"
                                required>
                            @error('date')
                                <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Hours --}}
                        <div class="flex flex-col gap-1.5">
                            <label for="hours" class="text-xs font-bold text-gray-600 uppercase tracking-wider">
                                Hours <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="hours" id="hours"
                                value="{{ old('hours', $overtimeUndertime->hours) }}"
                                min="0.5" max="24" step="0.5"
                                class="w-full bg-gray-50 border @error('hours') border-red-400 @else border-gray-200 @enderror rounded-xl px-4 py-2.5 text-sm font-medium text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 focus:border-gray-700 transition"
                                required>
                            @error('hours')
                                <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Amount (readonly) --}}
                        <div class="flex flex-col gap-1.5">
                            <label for="amount" class="text-xs font-bold text-gray-600 uppercase tracking-wider">Amount</label>
                            <div class="flex items-center bg-gray-50 border border-gray-200 rounded-xl overflow-hidden">
                                <span class="px-3 py-2.5 text-sm font-semibold text-gray-500 border-r border-gray-200 bg-gray-100 flex-shrink-0">₱</span>
                                <input type="text" id="amount"
                                    class="flex-1 bg-gray-50 px-3 py-2.5 text-sm font-medium text-gray-700 focus:outline-none"
                                    readonly>
                            </div>
                            <p class="text-xs text-gray-400">Calculated automatically based on your hourly rate</p>
                        </div>

                        {{-- Reason --}}
                        <div class="flex flex-col gap-1.5 sm:col-span-2">
                            <label for="reason" class="text-xs font-bold text-gray-600 uppercase tracking-wider">
                                Reason <span class="text-red-500">*</span>
                            </label>
                            <textarea name="reason" id="reason" rows="4"
                                class="w-full bg-gray-50 border @error('reason') border-red-400 @else border-gray-200 @enderror rounded-xl px-4 py-2.5 text-sm font-medium text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 focus:border-gray-700 transition resize-none"
                                required>{{ old('reason', $overtimeUndertime->reason) }}</textarea>
                            @error('reason')
                                <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-center gap-3 mt-6 pt-5 border-t border-gray-100">
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white text-sm font-semibold rounded-xl transition-all shadow-sm">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Update Request
                        </button>
                        <a href="{{ route('employee.overtime-undertime.show', $overtimeUndertime->id) }}"
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

<script>
const typeSelect  = document.getElementById('type');
const hoursInput  = document.getElementById('hours');
const amountInput = document.getElementById('amount');

function calculateAmount() {
    const hours      = parseFloat(hoursInput.value) || 0;
    const type       = typeSelect.value;
    const hourlyRate = {{ $overtimeUndertime->hourly_rate_used ?? 0 }};

    if (hours > 0 && hourlyRate > 0) {
        const amount = hours * hourlyRate;
        if (type === 'overtime') {
            amountInput.value = new Intl.NumberFormat('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(amount);
        } else if (type === 'undertime') {
            amountInput.value = '-' + new Intl.NumberFormat('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(amount);
        } else {
            amountInput.value = '';
        }
    } else {
        amountInput.value = '';
    }
}

typeSelect.addEventListener('change', calculateAmount);
hoursInput.addEventListener('input', calculateAmount);
calculateAmount();
</script>
@endsection
