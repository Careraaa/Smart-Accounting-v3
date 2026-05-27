@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeSlideUp {
    0%  { opacity:0; transform:translateY(14px); }
    100%{ opacity:1; transform:translateY(0); }
}
@keyframes scaleIn {
    0%  { opacity:0; transform:scale(0.93); }
    100%{ opacity:1; transform:scale(1); }
}
.anim-stat { animation: scaleIn 0.38s cubic-bezier(0.16,1,0.3,1) both; }
.anim-stat:nth-child(1){ animation-delay:.08s; }
.anim-stat:nth-child(2){ animation-delay:.14s; }
.anim-card { animation: fadeSlideUp 0.48s cubic-bezier(0.16,1,0.3,1) both; }
.anim-card:nth-child(1){ animation-delay:.2s; }
.anim-card:nth-child(2){ animation-delay:.26s; }
.anim-card:nth-child(3){ animation-delay:.32s; }
@keyframes pulseGlow {
    0%,100%{ box-shadow:0 0 0 0 rgba(200,41,42,0.35); }
    50%{ box-shadow:0 0 0 10px rgba(200,41,42,0); }
}
</style>
@endpush

@section('content')
<div class="max-w-full">



    {{-- Stat cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 mb-4">
        <div class="anim-stat bg-white border border-gray-200 rounded-2xl p-5 flex items-start gap-3.5 relative overflow-hidden hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
            <div class="absolute bottom-0 left-0 right-0 h-[3px] rounded-b-2xl bg-amber-600"></div>
            <div class="w-[42px] h-[42px] rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
            </div>
            <div class="flex-1">
                <div class="text-[0.67rem] font-bold uppercase tracking-[0.09em] text-gray-400 mb-1.5">Current Status</div>
                <div id="current-status" class="text-base font-black text-gray-900 font-mono tabular-nums leading-tight">—</div>
                <div class="text-[0.72rem] text-gray-400 mt-1.5">Based on your most recent log</div>
            </div>
        </div>
        <div class="anim-stat bg-white border border-gray-200 rounded-2xl p-5 flex items-start gap-3.5 relative overflow-hidden hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
            <div class="absolute bottom-0 left-0 right-0 h-[3px] rounded-b-2xl bg-emerald-600"></div>
            <div class="w-[42px] h-[42px] rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14"/></svg>
            </div>
            <div class="flex-1">
                <div class="text-[0.67rem] font-bold uppercase tracking-[0.09em] text-gray-400 mb-1.5">Last Log</div>
                <div class="text-base font-black text-gray-900 font-mono tabular-nums leading-tight">
                    <span id="last-log-badge-stat">—</span>
                    <span id="last-log-time-stat" class="text-sm text-gray-500 ml-1.5 font-mono"></span>
                </div>
                <div class="text-[0.72rem] text-gray-400 mt-1.5">Most recent attendance entry today</div>
            </div>
        </div>
    </div>

    {{-- Single-column layout --}}
    <div class="grid grid-cols-1 gap-4 items-start">
        <div>
            {{-- Profile --}}
            <div class="anim-card bg-white border border-gray-200 rounded-2xl overflow-hidden mb-4">
                <div class="flex items-center gap-2.5 px-4 py-3.5 border-b border-gray-100">
                    <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></span>
                    <span class="text-sm font-extrabold text-gray-900">My Profile</span>
                </div>
                <div class="p-4">
                    @php $u = auth()->user(); @endphp
                    @if($u && $u->salary_rate)
                        <div class="grid grid-cols-[110px_1fr] gap-x-3.5 gap-y-2 items-center min-w-0 overflow-hidden">
                            <span class="text-[0.67rem] font-bold uppercase tracking-[0.09em] text-gray-400">Full Name</span>
                            <span class="text-sm font-bold text-gray-900 truncate">{{ $u->first_name }} {{ $u->last_name }}</span>
                            <span class="text-[0.67rem] font-bold uppercase tracking-[0.09em] text-gray-400">Position</span>
                            <span class="text-sm font-bold text-gray-900 truncate">{{ $u->position ?? '—' }}</span>
                            <span class="text-[0.67rem] font-bold uppercase tracking-[0.09em] text-gray-400">Department</span>
                            <span class="text-sm font-bold text-gray-900 truncate">{{ $u->department ?? '—' }}</span>
                            <span class="text-[0.67rem] font-bold uppercase tracking-[0.09em] text-gray-400">Status</span>
                            <span>
                                @if($u->status === 'active')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-500 border border-gray-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Inactive
                                    </span>
                                @endif
                            </span>
                            <span class="text-[0.67rem] font-bold uppercase tracking-[0.09em] text-gray-400">Date Hired</span>
                            <span class="text-sm font-bold text-gray-900 truncate">{{ $u->date_of_hire ? $u->date_of_hire->format('M d, Y') : '—' }}</span>
                        </div>
                    @else
                        <p class="text-sm text-gray-400 m-0">No employee profile is linked to this account.</p>
                    @endif
                </div>
            </div>

            {{-- Tips --}}
            <div class="anim-card bg-white border border-gray-200 rounded-2xl overflow-hidden">
                <div class="flex items-center gap-2.5 px-4 py-3.5 border-b border-gray-100">
                    <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></span>
                    <span class="text-sm font-extrabold text-gray-900">Attendance Guidelines</span>
                </div>
                <div class="p-4">
                    <ul class="flex flex-col gap-3 list-none p-0 m-0">
                        <li class="flex items-start gap-2.5">
                            <span class="w-[22px] h-[22px] min-w-[22px] rounded-full bg-gray-900 text-white text-[0.68rem] font-extrabold flex items-center justify-center shrink-0 mt-0.5">1</span>
                            <span class="text-sm text-gray-600 leading-relaxed">Scan the QR code upon arrival to record your <strong class="text-gray-900">time in</strong>.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="w-[22px] h-[22px] min-w-[22px] rounded-full bg-gray-900 text-white text-[0.68rem] font-extrabold flex items-center justify-center shrink-0 mt-0.5">2</span>
                            <span class="text-sm text-gray-600 leading-relaxed">Scan again before leaving to record your <strong class="text-gray-900">time out</strong>.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="w-[22px] h-[22px] min-w-[22px] rounded-full bg-gray-900 text-white text-[0.68rem] font-extrabold flex items-center justify-center shrink-0 mt-0.5">3</span>
                            <span class="text-sm text-gray-600 leading-relaxed">Ensure your device camera is accessible and the QR code is clearly visible when scanning.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="w-[22px] h-[22px] min-w-[22px] rounded-full bg-gray-900 text-white text-[0.68rem] font-extrabold flex items-center justify-center shrink-0 mt-0.5">4</span>
                            <span class="text-sm text-gray-600 leading-relaxed"><strong class="text-rose-600">Immediately report</strong> any attendance discrepancies or scanning issues to the HR department.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Floating QR FAB — mobile only --}}
    <a href="{{ route('attendance.scan') }}"
       class="md:hidden fixed bottom-6 right-6 z-50 w-14 h-14 rounded-full bg-gray-900 text-white shadow-xl hover:bg-gray-800 hover:shadow-2xl hover:shadow-gray-900/60 active:scale-90 transition-all duration-200 flex items-center justify-center no-underline"
       style="animation:pulseGlow 2s ease-in-out infinite;">
         <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.5a1 1 0 010 2H5v3.5a1 1 0 01-2 0V5z"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 19a2 2 0 002 2h3.5a1 1 0 000-2H5v-3.5a1 1 0 00-2 0V19z"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 5a2 2 0 00-2-2h-3.5a1 1 0 000 2H19v3.5a1 1 0 002 0V5z"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 19a2 2 0 01-2 2h-3.5a1 1 0 010-2H19v-3.5a1 1 0 012 0V19z"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 8h2v2H8zM12 8h4v2h-4zM8 14h6v2H8zM16 14h2v2h-2zM8 12h2v2H8z"/>
            <circle cx="17" cy="17" r="3" stroke-width="1.5" fill="none"/>
            <circle cx="17" cy="17" r="1.5" fill="currentColor" stroke="none"/>
            <line x1="19.5" y1="19.5" x2="21" y2="21" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
    </a>

</div>

<script>
window.addEventListener('load', async () => {
    const statusEl    = document.getElementById('current-status');
    const badgeEl     = document.getElementById('last-log-badge');
    const timeEl      = document.getElementById('last-log-time');
    const badgeStatEl = document.getElementById('last-log-badge-stat');
    const timeStatEl  = document.getElementById('last-log-time-stat');

    function makeBadge(type) {
        if (!type) return '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-500 border border-gray-200"><span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>No log yet</span>';
        const isIn = type === 'time_in';
        return isIn
            ? '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Time In</span>'
            : '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Time Out</span>';
    }

    try {
        const response = await fetch("{{ route('attendance.lastlog') }}", { headers: { 'Accept': 'application/json' } });
        if (!response.ok) {
            statusEl.innerHTML = '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-500 border border-gray-200"><span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Not Logged</span>';
            badgeStatEl.innerHTML = makeBadge(null);
            badgeEl.innerHTML = makeBadge(null);
            return;
        }
        const contentType = response.headers.get('content-type') ?? '';
        if (!contentType.includes('application/json')) {
            statusEl.innerHTML = '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-500 border border-gray-200"><span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Not Logged</span>';
            badgeStatEl.innerHTML = makeBadge(null);
            badgeEl.innerHTML = makeBadge(null);
            return;
        }
        const data = await response.json();
        if (data && data.type) {
            const isIn = data.type === 'time_in';
            statusEl.innerHTML = isIn
                ? '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Timed In</span>'
                : '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Timed Out</span>';
            badgeStatEl.innerHTML = makeBadge(data.type);
            timeStatEl.textContent = data.time ?? '';
            badgeEl.innerHTML = makeBadge(data.type);
            timeEl.textContent = data.time ?? '';
        } else {
            statusEl.innerHTML = '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-500 border border-gray-200"><span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Not Logged</span>';
            badgeStatEl.innerHTML = makeBadge(null);
            badgeEl.innerHTML = makeBadge(null);
        }
    } catch (err) {
        statusEl.innerHTML = '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-500 border border-gray-200"><span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Not Logged</span>';
        badgeStatEl.innerHTML = makeBadge(null);
        badgeEl.innerHTML = makeBadge(null);
    }
});
</script>
@endsection
