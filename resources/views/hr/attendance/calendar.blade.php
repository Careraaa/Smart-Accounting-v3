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
@keyframes slideInRight {
    0% { opacity: 0; transform: translateX(-10px); }
    100% { opacity: 1; transform: translateX(0); }
}
.anim-header { animation: fadeSlideUp 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.anim-nav { animation: fadeSlideUp 0.4s cubic-bezier(0.16,1,0.3,1) 0.08s both; }
.anim-legend { animation: slideInRight 0.4s cubic-bezier(0.16,1,0.3,1) 0.12s both; }
.anim-grid { animation: fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) 0.15s both; }
.anim-stats { animation: scaleIn 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.anim-stats:nth-child(1) { animation-delay: 0.25s; }
.anim-stats:nth-child(2) { animation-delay: 0.3s; }
.anim-stats:nth-child(3) { animation-delay: 0.35s; }
.anim-stats:nth-child(4) { animation-delay: 0.4s; }
.anim-stats:nth-child(5) { animation-delay: 0.45s; }
</style>
@endpush

@section('content')
@php
    use Carbon\Carbon;

    $today      = Carbon::today();
    $monthStart = $month->copy()->startOfMonth();
    $monthEnd   = $month->copy()->endOfMonth();

    $firstHalfDays  = collect();
    $secondHalfDays = collect();

    for ($d = 1; $d <= 15 && $d <= $monthEnd->day; $d++) {
        $firstHalfDays->push($monthStart->copy()->setDay($d));
    }
    for ($d = 16; $d <= $monthEnd->day; $d++) {
        $secondHalfDays->push($monthStart->copy()->setDay($d));
    }

    $presentCount = $attendances->where('status', 'present')->count();
    $lateCount    = $attendances->where('status', 'late')->count();
    $absentCount  = $attendances->where('status', 'absent')->count();

    $monthOtHours = $otutRecords->flatten()->where('type', 'overtime')->sum('hours');
    $monthUtHours = $otutRecords->flatten()->where('type', 'undertime')->sum('hours');

    $initials = strtoupper(substr($employee->first_name ?? 'U', 0, 1) . substr($employee->last_name ?? '', 0, 1));
@endphp

<div class="max-w-full">

    {{-- Topbar --}}
    <div class="anim-header flex items-start justify-between mb-7 flex-wrap gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-rose-600 to-rose-400 flex items-center justify-center text-base font-extrabold text-white shrink-0 tracking-tight shadow-sm shadow-rose-200">
                {{ $initials }}
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-900 tracking-tight">{{ $employee->first_name }} {{ $employee->last_name }}</h1>
                <div class="flex items-center gap-2 mt-0.5 text-sm text-gray-500">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        {{ $employee->department ?? 'No Department' }}
                    </span>
                    <span>{{ $employee->position ?? 'No Position' }}</span>
                </div>
            </div>
        </div>
        <a href="{{ route('attendance.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:border-rose-300 hover:text-rose-600 hover:bg-rose-50 transition-all duration-200">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Employees
        </a>
    </div>

    {{-- Month navigation --}}
    <div class="anim-nav flex items-center justify-between bg-white border border-gray-200 rounded-2xl px-5 py-3.5 mb-5">
        <a href="{{ route('attendance.employee.calendar', [$employee->id, 'month' => $prevMonth]) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-semibold text-gray-600 bg-gray-50 border border-gray-200 rounded-xl hover:bg-rose-50 hover:border-rose-200 hover:text-rose-600 transition-all duration-200">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Prev
        </a>
        <div class="text-center">
            <div class="text-lg font-bold text-gray-900 tracking-tight">{{ $month->format('F Y') }}</div>
            <div class="text-xs text-gray-400 mt-0.5">Cutoff: 1–15 &amp; 16–{{ $monthEnd->day }}</div>
        </div>
        <a href="{{ route('attendance.employee.calendar', [$employee->id, 'month' => $nextMonth]) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-semibold text-gray-600 bg-gray-50 border border-gray-200 rounded-xl hover:bg-rose-50 hover:border-rose-200 hover:text-rose-600 transition-all duration-200">
            Next
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    {{-- Legend --}}
    <div class="anim-legend flex items-center gap-4 flex-wrap mb-4">
        <span class="flex items-center gap-1.5 text-[11px] font-semibold text-gray-500">
            <span class="w-3 h-3 rounded-[3px] bg-emerald-100 border border-emerald-300"></span> Present
        </span>
        <span class="flex items-center gap-1.5 text-[11px] font-semibold text-gray-500">
            <span class="w-3 h-3 rounded-[3px] bg-yellow-100 border border-yellow-300"></span> Late
        </span>
        <span class="flex items-center gap-1.5 text-[11px] font-semibold text-gray-500">
            <span class="w-3 h-3 rounded-[3px] bg-red-100 border border-red-300"></span> Absent
        </span>
        <span class="flex items-center gap-1.5 text-[11px] font-semibold text-gray-500">
            <span class="w-3 h-3 rounded-[3px] bg-gray-50 border border-gray-200"></span> Weekend / Holiday
        </span>
        <span class="flex items-center gap-1.5 text-[11px] font-semibold text-gray-500">
            <span class="w-3 h-3 rounded-[3px] bg-white border border-gray-200"></span> No Record
        </span>
        <span class="flex items-center gap-1.5 text-[11px] font-semibold text-gray-500">
            <span class="w-3 h-3 rounded-[3px] bg-blue-100 border border-blue-300"></span> OT Approved
        </span>
        <span class="flex items-center gap-1.5 text-[11px] font-semibold text-gray-500">
            <span class="w-3 h-3 rounded-[3px] bg-violet-100 border border-violet-300"></span> UT Approved
        </span>
    </div>

    {{-- Calendar grid --}}
    <div class="anim-grid bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300">

        @php
            function renderHalf($days, $label) {
                $firstDay = $days->first();
                $dayOfWeek = $firstDay ? $firstDay->dayOfWeekIso : 1;
                $padStart = $dayOfWeek === 7 ? 0 : $dayOfWeek;
                $cells = [];
                if ($firstDay) {
                    for ($p = 0; $p < $padStart; $p++) $cells[] = null;
                }
                foreach ($days as $d) $cells[] = $d;
                $rem = count($cells) % 7;
                if ($rem > 0) for ($p = 0; $p < (7 - $rem); $p++) $cells[] = null;
                return compact('cells', 'label');
            }

            $firstHalf = renderHalf($firstHalfDays, '1st Cutoff · ' . $monthStart->format('M 1') . ' – ' . $monthStart->copy()->setDay(15)->format('M 15'));
            $secondHalf = renderHalf($secondHalfDays, '2nd Cutoff · ' . $monthStart->copy()->setDay(16)->format('M 16') . ' – ' . $monthEnd->format('M j'));
        @endphp

        @foreach([$firstHalf, $secondHalf] as $i => $half)
            <div class="{{ $i === 0 ? 'border-b-2 border-dashed border-gray-100' : '' }} p-5">
                <div class="flex items-center gap-2 mb-3.5">
                    <span class="text-[11px] font-bold uppercase tracking-widest text-gray-400 shrink-0 bg-white pr-2">{{ $half['label'] }}</span>
                    <span class="flex-1 h-px bg-gray-100"></span>
                </div>

                <div class="overflow-x-auto -mx-5 px-5">
                    <div class="min-w-[490px]">
                        <div class="grid grid-cols-7 gap-1.5 mb-2">
                            @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $dow)
                                <div class="text-center text-[10px] font-bold uppercase tracking-wider text-gray-400 py-1">{{ $dow }}</div>
                            @endforeach
                        </div>

                @foreach(array_chunk($half['cells'], 7) as $week)
                <div class="grid grid-cols-7 gap-1.5 mb-1.5">
                    @foreach($week as $day)
                        @if($day === null)
                            <div class="rounded-xl min-h-[82px] border border-dashed border-gray-100 bg-transparent"></div>
                        @else
                            @php
                                $key = $day->format('Y-m-d');
                                $att = $attendances[$key] ?? null;
                                $dayOtut = $otutRecords[$key] ?? collect();
                                $otHours = $dayOtut->where('type', 'overtime')->sum('hours');
                                $utHours = $dayOtut->where('type', 'undertime')->sum('hours');
                                $isToday = $day->isSameDay($today);
                                $isFuture = $day->isAfter($today);
                                $isWeekend = $day->isSaturday() || $day->isSunday();
                                $hasOtut = $otHours > 0 || $utHours > 0;

                                if ($att) {
                                    $bgClass = match($att->status) {
                                        'present' => 'bg-emerald-50/60 border-emerald-200',
                                        'late' => 'bg-yellow-50/60 border-yellow-200',
                                        'absent' => 'bg-red-50/60 border-red-200',
                                        default => 'bg-white border-gray-200',
                                    };
                                    $dotClass = match($att->status) {
                                        'present' => 'bg-emerald-500',
                                        'late' => 'bg-yellow-500',
                                        'absent' => 'bg-red-500',
                                        default => 'bg-gray-300',
                                    };
                                    $statusText = match($att->status) {
                                        'present' => 'Present',
                                        'late' => 'Late',
                                        'absent' => 'Absent',
                                        default => ucfirst($att->status),
                                    };
                                    $statusColor = match($att->status) {
                                        'present' => 'text-emerald-700',
                                        'late' => 'text-yellow-700',
                                        'absent' => 'text-red-700',
                                        default => 'text-gray-500',
                                    };
                                } elseif ($hasOtut) {
                                    // OT/UT without attendance — infer present
                                    $bgClass = 'bg-emerald-50/60 border-emerald-200';
                                    $dotClass = 'bg-emerald-500';
                                    $statusText = 'Present';
                                    $statusColor = 'text-emerald-700';
                                } elseif ($isWeekend || $isFuture) {
                                    $bgClass = 'bg-gray-50 border-gray-200';
                                    $dotClass = '';
                                    $statusText = '';
                                    $statusColor = '';
                                } else {
                                    $bgClass = 'bg-white border-gray-200';
                                    $dotClass = '';
                                    $statusText = '';
                                    $statusColor = '';
                                }
                            @endphp
                            <div class="rounded-xl min-h-[82px] p-2 border-2 flex flex-col gap-0.5 transition-all duration-150 hover:-translate-y-0.5 hover:shadow-md {{ $bgClass }} {{ $isToday ? '!border-rose-500 shadow-md shadow-rose-100' : '' }}">
                                {{-- Top row: day number + status dot/label --}}
                                <div class="flex items-center justify-between gap-0.5">
                                    <span class="text-xs font-extrabold text-gray-700 leading-none {{ $isToday ? '!text-rose-600' : '' }}">{{ $day->day }}</span>
                                    @if(($att || $hasOtut) && !$isFuture)
                                    <span class="inline-flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $dotClass }}"></span>
                                        <span class="text-[8px] font-bold uppercase tracking-wider {{ $statusColor }}">{{ $statusText }}</span>
                                    </span>
                                    @endif
                                </div>

                                {{-- Time range (only when attendance record exists) --}}
                                @if($att && !$isFuture)
                                    @php
                                        $timeIn = $att->time_in ? \Carbon\Carbon::createFromFormat('H:i:s', $att->time_in)->format('g:ia') : null;
                                        $timeOut = $att->time_out ? \Carbon\Carbon::createFromFormat('H:i:s', $att->time_out)->format('g:ia') : null;
                                    @endphp
                                    <div class="text-[9px] font-mono text-gray-500 leading-tight -mt-0.5">
                                        @if($timeIn && $timeOut)
                                            <span>{{ $timeIn }} → {{ $timeOut }}</span>
                                        @elseif($timeIn)
                                            <span>{{ $timeIn }} → —</span>
                                        @elseif($timeOut)
                                            <span>— → {{ $timeOut }}</span>
                                        @else
                                            <span class="text-gray-300">No times</span>
                                        @endif
                                    </div>
                                @elseif(!$att && !$hasOtut && !$isFuture && !$isWeekend)
                                    <div class="text-[8px] text-gray-300 leading-tight -mt-0.5">No record</div>
                                @endif

                                {{-- OT/UT pills --}}
                                @if($hasOtut && !$isFuture)
                                <div class="flex items-center gap-1 mt-auto pt-0.5">
                                    @if($otHours > 0)
                                    <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-[3px] text-[8px] font-bold bg-blue-50 text-blue-700 border border-blue-200 leading-none">
                                        <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                        {{ number_format($otHours, 1) }}h
                                    </span>
                                    @endif
                                    @if($utHours > 0)
                                    <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-[3px] text-[8px] font-bold bg-violet-50 text-violet-700 border border-violet-200 leading-none">
                                        <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
                                        {{ number_format($utHours, 1) }}h
                                    </span>
                                    @endif
                                </div>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>
        @endforeach

    </div>

    {{-- Summary --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mt-5">
        <div class="anim-stats flex items-center gap-3 bg-white border border-gray-200 rounded-xl px-4 py-3.5 hover:shadow-md hover:border-emerald-200 transition-all duration-300">
            <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div>
                <div class="text-xl font-extrabold text-gray-900 tabular-nums leading-none">{{ $presentCount }}</div>
                <div class="text-[11px] text-gray-400 mt-0.5">Present</div>
            </div>
        </div>
        <div class="anim-stats flex items-center gap-3 bg-white border border-gray-200 rounded-xl px-4 py-3.5 hover:shadow-md hover:border-yellow-200 transition-all duration-300">
            <div class="w-9 h-9 rounded-lg bg-yellow-50 text-yellow-600 flex items-center justify-center shrink-0 border border-yellow-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 6v6l3 3"/></svg>
            </div>
            <div>
                <div class="text-xl font-extrabold text-gray-900 tabular-nums leading-none">{{ $lateCount }}</div>
                <div class="text-[11px] text-gray-400 mt-0.5">Late</div>
            </div>
        </div>
        <div class="anim-stats flex items-center gap-3 bg-white border border-gray-200 rounded-xl px-4 py-3.5 hover:shadow-md hover:border-red-200 transition-all duration-300">
            <div class="w-9 h-9 rounded-lg bg-red-50 text-red-600 flex items-center justify-center shrink-0 border border-red-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <div>
                <div class="text-xl font-extrabold text-gray-900 tabular-nums leading-none">{{ $absentCount }}</div>
                <div class="text-[11px] text-gray-400 mt-0.5">Absent</div>
            </div>
        </div>
        <div class="anim-stats flex items-center gap-3 bg-white border border-gray-200 rounded-xl px-4 py-3.5 hover:shadow-md hover:border-blue-200 transition-all duration-300">
            <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 border border-blue-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            </div>
            <div>
                <div class="text-xl font-extrabold text-gray-900 tabular-nums leading-none">{{ number_format($monthOtHours, 1) }}</div>
                <div class="text-[11px] text-gray-400 mt-0.5">OT Hours</div>
            </div>
        </div>
        <div class="anim-stats flex items-center gap-3 bg-white border border-gray-200 rounded-xl px-4 py-3.5 hover:shadow-md hover:border-violet-200 transition-all duration-300">
            <div class="w-9 h-9 rounded-lg bg-violet-50 text-violet-600 flex items-center justify-center shrink-0 border border-violet-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
            </div>
            <div>
                <div class="text-xl font-extrabold text-gray-900 tabular-nums leading-none">{{ number_format($monthUtHours, 1) }}</div>
                <div class="text-[11px] text-gray-400 mt-0.5">UT Hours</div>
            </div>
        </div>
    </div>

</div>
@endsection
