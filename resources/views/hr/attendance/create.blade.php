@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeSlideUp {
    0% { opacity: 0; transform: translateY(12px); }
    100% { opacity: 1; transform: translateY(0); }
}
@keyframes scaleIn {
    0% { opacity: 0; transform: scale(0.92); }
    100% { opacity: 1; transform: scale(1); }
}
.anim-header { animation: fadeSlideUp 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.anim-card { animation: scaleIn 0.4s cubic-bezier(0.16,1,0.3,1) 0.1s both; }
</style>
@endpush

@section('content')
<div class="max-w-full" data-global-datepicker="off">

    {{-- Flash --}}
    @if(session('error'))
    <div class="flex items-center gap-2.5 px-4 py-3 mb-5 rounded-xl text-sm font-medium bg-red-50 border border-red-200 text-red-700" style="animation:fadeSlideUp 0.35s ease both;">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
        {{ session('error') }}
    </div>
    @endif
    @if($errors->any())
    <div class="flex items-center gap-2.5 px-4 py-3 mb-5 rounded-xl text-sm font-medium bg-red-50 border border-red-200 text-red-700" style="animation:fadeSlideUp 0.35s ease both;">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
        <ul class="m-0 pl-4 text-sm">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Topbar --}}
    <div class="anim-header flex items-start justify-between mb-6 flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Manual Attendance Log</h1>
            <p class="text-sm text-gray-500 mt-0.5">Record an attendance entry manually for an employee</p>
        </div>
        <a href="{{ url()->previous() }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:border-rose-300 hover:text-rose-600 hover:bg-rose-50 transition-all duration-200">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back
        </a>
    </div>

    {{-- Form card --}}
    <div class="anim-card max-w-[680px] mx-auto">
        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300">

            <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-100">
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 border border-rose-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 6v6l4 2"/></svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-900">Attendance Entry</p>
                    <p class="text-xs text-gray-400">All fields marked <span class="text-rose-500">*</span> are required</p>
                </div>
            </div>

            <div class="px-6 py-6">
                <form action="{{ route('attendance.store') }}" method="POST" id="attForm">
                    @csrf

                    {{-- Employee --}}
                    <div class="mb-5">
                        <label for="user_id" class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-1.5">Employee <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <select name="user_id" id="user_id"
                                class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 bg-white outline-none appearance-none transition-all duration-200 focus:border-rose-400 focus:ring-2 focus:ring-rose-200/50 @error('user_id') border-red-400 @enderror">
                                <option value="">— Select an employee —</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" @selected(old('user_id') == $employee->id)>
                                        {{ $employee->last_name }}, {{ $employee->first_name }}
                                    </option>
                                @endforeach
                            </select>
                            <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                        @error('user_id')
                            <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                        @enderror

                    {{-- Date --}}
                    <div class="mb-5">
                        <label for="date" class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-1.5">Date <span class="text-rose-500">*</span></label>
                        <input type="date" name="date" id="date"
                            class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 bg-white outline-none transition-all duration-200 focus:border-rose-400 focus:ring-2 focus:ring-rose-200/50 @error('date') border-red-400 @enderror"
                            value="{{ old('date', today()->toDateString()) }}"
                            required>
                        @error('date')
                            <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Time In / Out --}}
                    <div class="text-[11px] font-bold uppercase tracking-widest text-gray-400 border-b border-gray-100 pb-2.5 mb-5">Time</div>
                    <p class="text-xs text-gray-400 mb-3">Leave both fields blank to mark the employee absent for this date.</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                        <div>
                            <label for="time_in" class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-1.5">Time In</label>
                            <input type="time" name="time_in" id="time_in"
                                class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 bg-white outline-none transition-all duration-200 focus:border-rose-400 focus:ring-2 focus:ring-rose-200/50 @error('time_in') border-red-400 @enderror"
                                value="{{ old('time_in') }}">
                            @error('time_in')
                                <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="time_out" class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-1.5">Time Out</label>
                            <input type="time" name="time_out" id="time_out"
                                class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 bg-white outline-none transition-all duration-200 focus:border-rose-400 focus:ring-2 focus:ring-rose-200/50 @error('time_out') border-red-400 @enderror"
                                value="{{ old('time_out') }}">
                            @error('time_out')
                                <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- OT/UT preview hint --}}
                    <div id="otut-hint" class="hidden text-sm font-semibold leading-relaxed"></div>

                </form>
            </div>

            <div class="flex items-center justify-end gap-2.5 px-6 py-4 border-t border-gray-100 bg-white">
                <button type="submit" form="attForm" class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded-xl hover:bg-gray-800 hover:shadow-lg hover:shadow-gray-900/20 active:scale-[0.97] transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Save Attendance
                </button>
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
(function () {
    const timeIn  = document.getElementById('time_in');
    const timeOut = document.getElementById('time_out');
    const hint    = document.getElementById('otut-hint');

    let breakStart   = null;
    let breakEnd     = null;
    let shiftStart   = null;
    let shiftEnd     = null;
    let gracePeriodMin = {{ $gracePeriodMinutes }};

    function toMinutes(hhmm) {
        if (!hhmm) return null;
        const [h, m] = hhmm.split(':').map(Number);
        return h * 60 + m;
    }

    function timeStringToMinutes(hhmmss) {
        if (!hhmmss) return null;
        const [h, m, s] = hhmmss.split(':').map(Number);
        return h * 60 + m;
    }

    function fmt(totalMin) {
        const h = Math.floor(totalMin / 60);
        const m = totalMin % 60;
        if (h > 0 && m > 0) return `${h}h ${m}m`;
        if (h > 0) return `${h}h`;
        return `${m}m`;
    }

    function convertMinutesToHourIncrement(minutes) {
        return Math.floor(minutes / 30) * 0.5;
    }

    function hoursToMinutes(hours) {
        return Math.round(hours * 60);
    }

    function calculateBreakOverlap(inMin, outMin) {
        if (breakStart === null || breakEnd === null) return 0;
        const overlapStart = Math.max(inMin, breakStart);
        const overlapEnd   = Math.min(outMin, breakEnd);
        return Math.max(0, overlapEnd - overlapStart);
    }

    function update() {
        let inMin  = toMinutes(timeIn.value);
        const outMin = toMinutes(timeOut.value);

        if (inMin === null || outMin === null || outMin <= inMin) {
            hint.classList.add('hidden');
            return;
        }

        // ── Shift-start clamping (matches AttendanceService.php) ──
        if (shiftStart !== null) {
            if (inMin < shiftStart) {
                // Early arrival: clamp to shift start so early hours don't create phantom OT
                inMin = shiftStart;
            } else {
                // Grace period: clamp slightly-late arrivals to shift start
                const minutesLate = inMin - shiftStart;
                if (minutesLate >= 0 && minutesLate <= gracePeriodMin) {
                    inMin = shiftStart;
                }
            }
        }

        const breakOverlapMin = calculateBreakOverlap(inMin, outMin);
        const workedMin = Math.max(0, (outMin - inMin) - breakOverlapMin);

        // ── OT/UT detection using shift boundaries (matches server-side logic) ──
        if (shiftStart !== null && shiftEnd !== null && breakStart !== null && breakEnd !== null) {
            const breakDuration = Math.max(0, breakEnd - breakStart);
            const scheduledMin  = Math.max(0, (shiftEnd - shiftStart) - breakDuration);

            // Overtime: only when clock-out is AFTER shift end (actualEnd > expectedEnd)
            if (outMin > shiftEnd) {
                const minutesBeyond = outMin - shiftEnd;
                if (minutesBeyond >= 30) {
                    const otDisplayMin = hoursToMinutes(convertMinutesToHourIncrement(minutesBeyond));
                    showHint(true, otDisplayMin);
                    return;
                }
            }

            // Undertime: only when clock-out is BEFORE shift end AND actual < scheduled
            if (outMin < shiftEnd && workedMin < scheduledMin) {
                const earlyDepartureMin = shiftEnd - outMin;
                // Subtract any break that falls within the early-departure window
                const depBreakOverlap = calculateBreakOverlap(outMin, shiftEnd);
                const undertimeMin = Math.max(0, earlyDepartureMin - depBreakOverlap);
                const utDisplayMin = hoursToMinutes(convertMinutesToHourIncrement(undertimeMin));
                if (utDisplayMin > 0) {
                    showHint(false, utDisplayMin);
                    return;
                }
            }
        } else {
            // Fallback: no shift data — use raw 8h threshold
            const STANDARD_MIN = 480;
            const diff = workedMin - STANDARD_MIN;
            const diffHours = convertMinutesToHourIncrement(Math.abs(diff));
            if (diffHours > 0) {
                showHint(diff > 0, hoursToMinutes(diffHours));
                return;
            }
        }

        hint.classList.add('hidden');
    }

    function showHint(isOT, displayMin) {
        hint.classList.remove('hidden');
        hint.className = 'text-sm font-semibold leading-relaxed px-4 py-3 rounded-xl mt-1 border ' + (isOT
            ? 'bg-emerald-50 border-emerald-200 text-emerald-700'
            : 'bg-amber-50 border-amber-200 text-amber-700');

        hint.innerHTML = isOT
            ? `<svg class="inline align-middle mr-1.5" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z"/></svg> <strong>Overtime detected:</strong> ${fmt(displayMin)} beyond the schedule — an OT record will be auto-created on save.`
            : `<svg class="inline align-middle mr-1.5" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg> <strong>Undertime detected:</strong> ${fmt(displayMin)} short of the schedule — a UT record will be auto-created on save.`;
    }

    fetch('{{ route("api.shift.break-times") }}')
        .then(res => res.json())
        .then(data => {
            breakStart   = timeStringToMinutes(data.break_start);
            breakEnd     = timeStringToMinutes(data.break_end);
            shiftStart   = timeStringToMinutes(data.start_time);
            shiftEnd     = timeStringToMinutes(data.end_time);
            gracePeriodMin = data.grace_period_minutes || 5;
            update();
        })
        .catch(() => {
            breakStart   = 12 * 60;
            breakEnd     = 13 * 60;
            shiftStart   = 8 * 60;
            shiftEnd     = 17 * 60;
            gracePeriodMin = 5;
        });

    timeIn.addEventListener('change', update);
    timeOut.addEventListener('change', update);
})();
</script>
@endpush
