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

    <div class="flex items-start justify-between gap-4 mb-6 flex-wrap">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight m-0">Edit Leave Request</h1>
            <p class="text-xs text-gray-400 m-0 mt-0.5">Editing request for <strong>{{ $leave->employee->first_name }} {{ $leave->employee->last_name }}</strong></p>
        </div>
        <a href="{{ route('leave.show', $leave) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl text-xs font-semibold no-underline hover:border-gray-600 hover:text-gray-600 hover:bg-gray-50 transition-colors whitespace-nowrap cursor-pointer">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Details
        </a>
    </div>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">

            <div class="px-6 py-5 border-b border-gray-50 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gray-100 text-gray-400 flex items-center justify-center shrink-0">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-extrabold text-gray-900 m-0 leading-tight">Leave Details</p>
                    <p class="text-xs text-gray-400 m-0">All fields marked <span class="text-amber-600">*</span> are required</p>
                </div>
            </div>

            <div class="p-6">
                <form action="{{ route('leave.update', $leave) }}" method="POST" id="lvForm">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                        <div>
                            <label for="user_id" class="block text-[0.7rem] font-bold uppercase tracking-wide text-gray-500 mb-1.5">Employee <span class="text-amber-600">*</span></label>
                            <div class="relative">
                                <select name="user_id" id="user_id" class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 pr-9 text-sm text-gray-900 bg-white outline-none transition-colors focus:border-gray-400 focus:ring-2 focus:ring-gray-200/50 appearance-none @error('user_id') border-amber-500 @enderror" required>
                                    <option value="">— Select Employee —</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" @selected($leave->user_id == $employee->id)>
                                            {{ $employee->first_name }} {{ $employee->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <svg class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                            @error('user_id')<span class="text-xs text-amber-600 mt-1 block">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label for="leave_type" class="block text-[0.7rem] font-bold uppercase tracking-wide text-gray-500 mb-1.5">Leave Type <span class="text-amber-600">*</span></label>
                            <div class="relative">
                                <select name="leave_type" id="leave_type" class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 pr-9 text-sm text-gray-900 bg-white outline-none transition-colors focus:border-gray-400 focus:ring-2 focus:ring-gray-200/50 appearance-none @error('leave_type') border-amber-500 @enderror" required>
                                    <option value="">— Select Leave Type —</option>
                                    @foreach($leaveTypes as $type)
                                        <option value="{{ $type }}" @selected($leave->leave_type == $type)>{{ $type }}</option>
                                    @endforeach
                                </select>
                                <svg class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                            @error('leave_type')<span class="text-xs text-amber-600 mt-1 block">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="text-[0.68rem] font-bold uppercase tracking-[0.12em] text-gray-400 border-b border-gray-50 pb-2.5 mb-5">Duration</div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                        <div>
                            <label for="start_date" class="block text-[0.7rem] font-bold uppercase tracking-wide text-gray-500 mb-1.5">Start Date <span class="text-amber-600">*</span></label>
                            <input type="date" name="start_date" id="start_date"
                                class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm text-gray-900 bg-white outline-none transition-colors focus:border-gray-400 focus:ring-2 focus:ring-gray-200/50 @error('start_date') border-amber-500 @enderror"
                                value="{{ $leave->start_date->format('Y-m-d') }}" required>
                            @error('start_date')<span class="text-xs text-amber-600 mt-1 block">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label for="end_date" class="block text-[0.7rem] font-bold uppercase tracking-wide text-gray-500 mb-1.5">End Date <span class="text-amber-600">*</span></label>
                            <input type="date" name="end_date" id="end_date"
                                class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm text-gray-900 bg-white outline-none transition-colors focus:border-gray-400 focus:ring-2 focus:ring-gray-200/50 @error('end_date') border-amber-500 @enderror"
                                value="{{ $leave->end_date->format('Y-m-d') }}" required>
                            <p class="text-xs text-gray-400 mt-1.5 font-mono">Duration: <span class="text-gray-900 font-bold" id="durationDays">0</span> day(s)</p>
                            @error('end_date')<span class="text-xs text-amber-600 mt-1 block">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="text-[0.68rem] font-bold uppercase tracking-[0.12em] text-gray-400 border-b border-gray-50 pb-2.5 mb-5">Reason</div>
                    <div class="mb-0 last:mb-0">
                        <label for="reason" class="block text-[0.7rem] font-bold uppercase tracking-wide text-gray-500 mb-1.5">Reason / Remarks <span class="text-amber-600">*</span></label>
                        <textarea name="reason" id="reason"
                            class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm text-gray-900 bg-white outline-none transition-colors focus:border-gray-400 focus:ring-2 focus:ring-gray-200/50 resize-y min-h-[110px] @error('reason') border-amber-500 @enderror"
                            placeholder="Provide a reason for the leave request…" required>{{ $leave->reason }}</textarea>
                        @error('reason')<span class="text-xs text-amber-600 mt-1 block">{{ $message }}</span>@enderror
                    </div>

                </form>
            </div>

            <div class="px-6 py-4 border-t border-gray-50 bg-gray-50/50 flex items-center gap-2.5">
                <button type="submit" form="lvForm" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 text-white border-none rounded-xl text-xs font-bold cursor-pointer hover:bg-black transition-colors">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Update Leave Request
                </button>
                <a href="{{ route('leave.show', $leave) }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-white text-gray-600 border border-gray-200 rounded-xl text-xs font-semibold no-underline hover:border-gray-600 hover:text-gray-600 hover:bg-gray-50 transition-colors cursor-pointer">Cancel</a>
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
(function(){
    const s = document.getElementById('start_date');
    const e = document.getElementById('end_date');
    const d = document.getElementById('durationDays');
    function calc(){
        if(s.value && e.value){
            const diff = Math.ceil(Math.abs(new Date(e.value) - new Date(s.value)) / 86400000) + 1;
            d.textContent = diff > 0 ? diff : 0;
        } else { d.textContent = 0; }
    }
    s.addEventListener('change', calc);
    e.addEventListener('change', calc);
    calc();
})();
</script>
@endpush
