@php
    $attendanceSource = collect($attendanceTrend ?? []);
    $attendanceDays = $attendanceSource
        ->filter(fn ($day) => \Carbon\Carbon::parse($day['date_iso'])->dayOfWeek !== \Carbon\Carbon::SUNDAY)
        ->values();
    $attendanceToday = $attendanceSource->first(fn ($day) => $day['date_iso'] === now()->toDateString())
        ?? ['present' => 0, 'late' => 0, 'absent' => 0, 'date_iso' => now()->toDateString()];
    $attendanceMax = max(25, (int) $attendanceDays->max(fn ($day) => (int) $day['present']));
    $attendanceFirstDate = $attendanceDays->first()['date_iso'] ?? now()->toDateString();
    $attendanceLastDate = $attendanceDays->last()['date_iso'] ?? now()->toDateString();
@endphp

<div class="sad-fade-up kpi-card overflow-hidden">
    <div class="px-5 pt-5 pb-1 sm:px-7 sm:pt-6">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-[9px] font-semibold tracking-[0.13em] text-gray-400">WEEKLY SNAPSHOT</p>
                <h2 class="mt-1.5 text-base font-medium tracking-tight text-gray-900">Attendance trend</h2>
            </div>
            <span class="inline-flex shrink-0 items-center gap-[7px] whitespace-nowrap rounded-full border border-gray-200 px-[9px] py-[7px] text-[10px] font-medium text-gray-500">
                <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                {{ \Carbon\Carbon::parse($attendanceFirstDate)->format('M j') }}–{{ \Carbon\Carbon::parse($attendanceLastDate)->format('j') }}
            </span>
        </div>
    </div>

    <div class="mx-5 mt-[18px] flex min-h-[91px] items-center justify-between gap-3 border-y border-gray-100 px-0 py-[15px] sm:mx-7">
        <div class="flex flex-col gap-1">
            <span class="text-[9px] font-bold tracking-[0.08em] text-emerald-600">TODAY · {{ \Carbon\Carbon::parse($attendanceToday['date_iso'])->format('D, M j') }}</span>
            <span class="text-sm font-medium text-gray-900">Present today</span>
        </div>
        <div class="flex items-baseline gap-2">
            <span class="text-[38px] font-semibold leading-none tracking-tight text-gray-900">{{ $attendanceToday['present'] }}</span>
            <span class="text-[11px] font-medium text-gray-500">present</span>
            <span class="mb-[3px] h-2 w-2 rounded-full bg-emerald-500 ring-[3px] ring-emerald-500/15"></span>
        </div>
    </div>

    <div class="px-5 pb-4 sm:px-7 sm:pb-[17px]">
        @if($attendanceDays->isNotEmpty())
            <div class="mb-2 mt-[19px] flex items-center justify-between text-[10px] font-medium text-gray-500">
                <span>People present</span>
                <span class="text-[9px] font-normal text-gray-400">count</span>
            </div>

            <div class="grid grid-cols-[26px_minmax(0,1fr)] gap-[6px] max-[360px]:grid-cols-[22px_minmax(0,1fr)] max-[360px]:gap-1">
                <div class="flex h-[120px] flex-col justify-between text-right text-[8px] leading-none text-gray-400" aria-hidden="true">
                    <span>{{ $attendanceMax }}</span><span>{{ (int) floor($attendanceMax / 2) }}</span><span>0</span>
                </div>
                <div class="min-w-0">
                    <div class="relative h-[120px]" role="img" aria-label="People present Monday through Saturday. Sunday is not included.">
                        <div class="pointer-events-none absolute inset-0">
                            <div class="absolute inset-x-0 top-0 border-t border-dashed border-gray-200"></div>
                            <div class="absolute inset-x-0 top-1/2 border-t border-dashed border-gray-200"></div>
                            <div class="absolute inset-x-0 bottom-0 border-t border-gray-200"></div>
                        </div>
                        <div class="absolute inset-0 grid gap-1.5 sm:gap-3" style="grid-template-columns:repeat({{ min($attendanceDays->count(), 6) }}, minmax(0, 1fr));">
                            @foreach($attendanceDays as $day)
                                @php
                                    $present = (int) $day['present'];
                                    $height = $attendanceMax > 0 ? ($present / $attendanceMax) * 100 : 0;
                                    $isToday = $day['date_iso'] === $attendanceToday['date_iso'];
                                @endphp
                                <div class="group relative min-w-0" title="{{ \Carbon\Carbon::parse($day['date_iso'])->format('D, M j') }} — {{ $present }} present">
                                    <span class="absolute inset-x-0 z-10 text-center text-[8px] font-medium leading-none {{ $isToday ? 'font-bold text-emerald-600' : 'text-gray-400 opacity-75 group-hover:opacity-100' }}" style="bottom:calc({{ $height }}% + 6px)">{{ $present }}</span>
                                    <div class="absolute bottom-0 right-1/2 h-full w-[min(27px,58%)] translate-x-1/2 overflow-hidden rounded-t-md rounded-b-[3px] bg-gray-100">
                                        <div class="absolute inset-x-0 bottom-0 rounded-t-md rounded-b-[3px] {{ $isToday ? 'bg-emerald-500' : 'bg-gray-300' }}" style="height:{{ $height }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="mt-[9px] grid gap-1.5 sm:gap-3" style="grid-template-columns:repeat({{ min($attendanceDays->count(), 6) }}, minmax(0, 1fr));">
                        @foreach($attendanceDays as $day)
                            @php $isToday = $day['date_iso'] === $attendanceToday['date_iso']; @endphp
                            <div class="flex items-center justify-center gap-1 text-[9px] leading-none {{ $isToday ? 'font-semibold text-gray-900' : 'font-medium text-gray-400' }}">
                                <span>{{ \Carbon\Carbon::parse($day['date_iso'])->format('D') }}</span>
                                <span class="text-[8px] font-normal {{ $isToday ? 'text-emerald-600' : 'text-gray-400' }}">{{ \Carbon\Carbon::parse($day['date_iso'])->format('j') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <footer class="mt-[17px] flex items-center justify-between border-t border-gray-100 pt-3 text-[9px] text-gray-400">
                <span class="inline-flex items-center gap-1.5"><span class="h-[7px] w-[7px] rounded-sm bg-gray-300"></span>Daily headcount</span>
                <span>Sunday excluded</span>
            </footer>
        @else
            <div class="flex h-[160px] items-center justify-center text-xs text-gray-400">No attendance data yet.</div>
        @endif
    </div>
</div>