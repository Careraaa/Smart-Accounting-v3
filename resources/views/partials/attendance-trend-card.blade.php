@php
    $attendancePoints = collect($attendanceTrend ?? []);
    $attendanceCount = $attendancePoints->count();
    $attendanceToday = $attendancePoints->last() ?? ['present' => 0, 'late' => 0, 'absent' => 0, 'date_iso' => now()->toDateString()];
    $attendanceMax = max(1, (int) $attendancePoints->map(fn($day) => (int) $day['present'] + (int) $day['late'] + (int) $day['absent'])->max());
@endphp

<div class="sad-fade-up kpi-card overflow-hidden">
    <div class="px-5 pt-5 pb-1 flex items-center justify-between gap-3">
        <div>
            <p class="text-[10px] font-semibold uppercase tracking-[0.13em] text-gray-400">Weekly snapshot</p>
            <h2 class="mt-1 text-base font-medium tracking-tight text-gray-900">Attendance trend</h2>
        </div>
        @if($attendanceCount > 0)
            <span class="shrink-0 rounded-full border border-gray-200 px-2.5 py-1 text-[10px] font-medium text-gray-500">
                {{ \Carbon\Carbon::parse($attendancePoints->first()['date_iso'])->format('M j') }}–{{ \Carbon\Carbon::parse($attendancePoints->last()['date_iso'])->format('j') }}
            </span>
        @endif
    </div>

    <div class="mx-5 mt-4 flex min-h-[74px] items-center justify-between gap-3 border-y border-gray-100 py-3">
        <div class="flex flex-col gap-1">
            <span class="text-[9px] font-bold tracking-[0.08em] text-emerald-600">TODAY · {{ \Carbon\Carbon::parse($attendanceToday['date_iso'])->format('D, M j') }}</span>
            <span class="text-sm font-medium text-gray-900">Present today</span>
        </div>
        <div class="flex items-baseline gap-2">
            <span class="text-[32px] font-semibold leading-none tracking-tight text-gray-900">{{ $attendanceToday['present'] }}</span>
            <span class="text-[11px] font-medium text-gray-500">present</span>
            <span class="mb-1 h-2 w-2 rounded-full bg-emerald-500 ring-[3px] ring-emerald-500/15"></span>
        </div>
    </div>

    <div class="px-5 pb-4">
        @if($attendanceCount > 0)
            <div class="mb-2 mt-4 flex items-center justify-between text-[10px] font-medium text-gray-500">
                <span>People present</span><span class="text-[9px] font-normal text-gray-400">count</span>
            </div>
            <div class="grid grid-cols-[24px_minmax(0,1fr)] gap-2">
                <div class="flex h-[116px] flex-col justify-between text-right text-[8px] leading-none text-gray-400">
                    <span>{{ $attendanceMax }}</span><span>{{ (int) floor($attendanceMax / 2) }}</span><span>0</span>
                </div>
                <div class="min-w-0">
                    <div class="relative h-[116px]">
                        <div class="pointer-events-none absolute inset-0">
                            <div class="absolute inset-x-0 top-0 border-t border-dashed border-gray-200"></div>
                            <div class="absolute inset-x-0 top-1/2 border-t border-dashed border-gray-200"></div>
                            <div class="absolute inset-x-0 bottom-0 border-t border-dashed border-gray-200"></div>
                        </div>
                        <div class="absolute inset-0 grid grid-cols-{{ min($attendanceCount, 7) }} gap-1.5 sm:gap-3">
                            @foreach($attendancePoints as $day)
                                @php $total = (int) $day['present'] + (int) $day['late'] + (int) $day['absent']; @endphp
                                <div class="group relative min-w-0">
                                    <span class="absolute inset-x-0 z-10 text-center text-[8px] font-medium leading-none text-gray-400 opacity-80 group-hover:opacity-100" style="bottom:calc({{ $attendanceMax > 0 ? ($total / $attendanceMax) * 100 : 0 }}% + 6px)">{{ $total }}</span>
                                    <div class="absolute bottom-0 right-1/2 h-full w-[min(27px,58%)] translate-x-1/2 overflow-hidden rounded-t-md rounded-b-[3px] bg-gray-100">
                                        <div class="absolute inset-x-0 bottom-0 rounded-t-md rounded-b-[3px] bg-indigo-500" style="height:{{ $attendanceMax > 0 ? ($day['present'] / $attendanceMax) * 100 : 0 }}%"></div>
                                        <div class="absolute inset-x-0 rounded-t-md bg-amber-400" style="bottom:{{ $attendanceMax > 0 ? ($day['present'] / $attendanceMax) * 100 : 0 }}%;height:{{ $attendanceMax > 0 ? ($day['late'] / $attendanceMax) * 100 : 0 }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="mt-2 grid grid-cols-{{ min($attendanceCount, 7) }} gap-1.5 text-[9px] leading-none text-gray-400 sm:gap-3">
                        @foreach($attendancePoints as $day)<span class="text-center">{{ \Carbon\Carbon::parse($day['date_iso'])->format('D') }}</span>@endforeach
                    </div>
                </div>
            </div>
            <div class="mt-3 flex items-center justify-between border-t border-gray-100 pt-3 text-[11px] text-gray-400">
                <span>Daily headcount, 7-day window</span><span>Today: <strong class="font-medium text-gray-700">{{ $attendanceToday['present'] }}</strong> present</span>
            </div>
        @else
            <div class="flex h-[160px] items-center justify-center text-xs text-gray-400">No attendance data yet.</div>
        @endif
    </div>
</div>