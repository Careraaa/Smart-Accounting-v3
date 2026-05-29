@extends('layouts.layout')

@push('styles')
<style>
@keyframes empFadeUp { 0%{opacity:0;transform:translateY(16px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes empScaleIn { 0%{opacity:0;transform:scale(0.92)} 100%{opacity:1;transform:scale(1)} }
@keyframes empSlideRight { 0%{opacity:0;transform:translateX(-12px)} 100%{opacity:1;transform:transform(0)} }
@keyframes empBounceIn {
    0%{opacity:0;transform:scale(0.6)}
    60%{transform:scale(1.05)}
    100%{opacity:1;transform:scale(1)}
}
@keyframes pulseGlow {
    0%,100%{box-shadow:0 0 0 0 rgba(200,41,42,0.3)}
    50%{box-shadow:0 0 0 8px rgba(200,41,42,0)}
}
@keyframes qrScan {
    0%,100%{top:20%}
    50%{top:72%}
}
@keyframes qrPulse {
    0%,100%{box-shadow:0 0 0 0 rgba(244,63,94,0.35)}
    50%{box-shadow:0 0 0 14px rgba(244,63,94,0)}
}
.animate-qr-pulse {
    animation:qrPulse 2.5s ease-out infinite;
}
.emp-fade-up { animation:empFadeUp 0.45s cubic-bezier(0.16,1,0.3,1) both; }
.emp-fade-up:nth-child(1){ animation-delay:.05s; }
.emp-fade-up:nth-child(2){ animation-delay:.1s; }
.emp-fade-up:nth-child(3){ animation-delay:.15s; }
.emp-fade-up:nth-child(4){ animation-delay:.2s; }
.emp-scale-in { animation:empScaleIn 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.emp-slide-right { animation:empSlideRight 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.emp-bounce-in { animation:empBounceIn 0.5s cubic-bezier(0.34,1.56,0.64,1) both; }
</style>
@endpush

@section('content')
@php
$user = auth()->user();
@endphp

<div class="max-w-full">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">

        {{-- LEFT COLUMN (2/3) --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Attendance Status bar --}}
            <div class="emp-fade-up rounded-xl bg-white px-5 py-4 shadow-sm border border-gray-100 flex items-center justify-between transition-all hover:shadow-md hover:-translate-y-0.5">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
                    </div>
                    <div>
                        <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest">Today's Status</span>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span id="dash-status-badge" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500">�</span>
                            <span id="dash-status-time" class="text-[11px] font-mono text-gray-400"></span>
                        </div>
                    </div>
                </div>
                <a href="{{ route('employee.attendance.index') }}" class="btn-uv-pill">View all ?</a>
            </div>

            {{-- Quick Actions --}}
            <div class="emp-scale-in rounded-xl bg-white px-5 py-4 shadow-sm border border-gray-100 transition-all hover:shadow-md hover:-translate-y-0.5" style="animation-delay:0.25s">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <span class="text-xs font-semibold text-gray-900">Quick Actions</span>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('employee.leaves.create') }}" class="group flex flex-col items-center gap-2 px-3 py-3.5 rounded-xl bg-gray-50 border border-gray-100 transition-all hover:bg-blue-50 hover:border-blue-200 hover:shadow-sm no-underline">
                        <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center transition-all group-hover:bg-blue-500 group-hover:text-white group-hover:scale-110 group-hover:shadow-lg group-hover:shadow-blue-200/50">
                            <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <span class="text-[10px] font-semibold text-gray-700 group-hover:text-blue-700 transition-colors">Request Leave</span>
                        @if($pendingLeaves > 0)
                            <span class="inline-flex items-center justify-center min-w-[16px] h-3.5 px-1 rounded-full bg-blue-100 text-blue-700 text-[0.4rem] font-bold">{{ $pendingLeaves }}</span>
                        @endif
                    </a>
                    <a href="{{ route('attendance.scan') }}" class="group flex flex-col items-center gap-2 px-3 py-3.5 rounded-xl bg-gray-50 border border-gray-100 transition-all hover:bg-emerald-50 hover:border-emerald-200 hover:shadow-sm no-underline">
                        <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center transition-all group-hover:bg-emerald-500 group-hover:text-white group-hover:scale-110 group-hover:shadow-lg group-hover:shadow-emerald-200/50">
                            <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.5a1 1 0 010 2H5v3.5a1 1 0 01-2 0V5zM3 19a2 2 0 002 2h3.5a1 1 0 000-2H5v-3.5a1 1 0 00-2 0V19zM21 5a2 2 0 00-2-2h-3.5a1 1 0 000 2H19v3.5a1 1 0 002 0V5zM21 19a2 2 0 01-2 2h-3.5a1 1 0 010-2H19v-3.5a1 1 0 012 0V19z"/></svg>
                        </div>
                        <span class="text-[10px] font-semibold text-gray-700 group-hover:text-emerald-700 transition-colors">Scan QR</span>
                    </a>
                    <a href="{{ route('employee.overtime-undertime.create') }}" class="group flex flex-col items-center gap-2 px-3 py-3.5 rounded-xl bg-gray-50 border border-gray-100 transition-all hover:bg-violet-50 hover:border-violet-200 hover:shadow-sm no-underline">
                        <div class="w-9 h-9 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center transition-all group-hover:bg-violet-500 group-hover:text-white group-hover:scale-110 group-hover:shadow-lg group-hover:shadow-violet-200/50">
                            <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3"/></svg>
                        </div>
                        <span class="text-[10px] font-semibold text-gray-700 group-hover:text-violet-700 transition-colors">OT / UT</span>
                        @if($pendingOT + $pendingUT > 0)
                            <span class="inline-flex items-center justify-center min-w-[16px] h-3.5 px-1 rounded-full bg-violet-100 text-violet-700 text-[0.4rem] font-bold">{{ $pendingOT + $pendingUT }}</span>
                        @endif
                    </a>
                    <a href="{{ route('employee.attendance.index') }}" class="group flex flex-col items-center gap-2 px-3 py-3.5 rounded-xl bg-gray-50 border border-gray-100 transition-all hover:bg-amber-50 hover:border-amber-200 hover:shadow-sm no-underline">
                        <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center transition-all group-hover:bg-amber-500 group-hover:text-white group-hover:scale-110 group-hover:shadow-lg group-hover:shadow-amber-200/50">
                            <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
                        </div>
                        <span class="text-[10px] font-semibold text-gray-700 group-hover:text-amber-700 transition-colors">Attendance</span>
                    </a>
                </div>
            </div>

            {{-- My Leaves --}}
            <a href="{{ route('employee.leaves.index') }}" class="emp-scale-in rounded-xl bg-white px-5 py-3.5 shadow-sm border border-gray-100 flex items-center gap-3.5 no-underline transition-all hover:shadow-md hover:-translate-y-0.5 active:scale-[0.98] group" style="animation-delay:0.05s">
                <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                    <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-gray-900">My Leaves</span>
                        @if($pendingLeaves > 0)
                            <span class="inline-flex items-center justify-center min-w-[18px] h-[18px] rounded-full bg-amber-100 text-amber-700 text-[9px] font-bold px-1">{{ $pendingLeaves }}</span>
                        @endif
                    </div>
                    <div class="text-[10px] text-gray-400 mt-0.5">{{ $totalLeaves }} total � {{ $approvedLeaves }} approved</div>
                </div>
                <svg class="w-3.5 h-3.5 text-gray-300 shrink-0 group-hover:text-gray-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>

            {{-- OT / UT --}}
            <a href="{{ route('employee.overtime-undertime.index') }}" class="emp-scale-in rounded-xl bg-white px-5 py-3.5 shadow-sm border border-gray-100 flex items-center gap-3.5 no-underline transition-all hover:shadow-md hover:-translate-y-0.5 active:scale-[0.98] group" style="animation-delay:0.1s">
                <div class="w-9 h-9 rounded-lg bg-violet-50 text-violet-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                    <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-gray-900">OT / UT</span>
                        @if($pendingOT + $pendingUT > 0)
                            <span class="inline-flex items-center justify-center min-w-[18px] h-[18px] rounded-full bg-amber-100 text-amber-700 text-[9px] font-bold px-1">{{ $pendingOT + $pendingUT }}</span>
                        @endif
                    </div>
                    <div class="text-[10px] text-gray-400 mt-0.5">{{ $pendingOT }} OT � {{ $pendingUT }} UT pending</div>
                </div>
                <svg class="w-3.5 h-3.5 text-gray-300 shrink-0 group-hover:text-gray-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>

            {{-- Cash Advances --}}
            <a href="{{ route('employee.cash-advances.index') }}" class="emp-scale-in rounded-xl bg-white px-5 py-3.5 shadow-sm border border-gray-100 flex items-center gap-3.5 no-underline transition-all hover:shadow-md hover:-translate-y-0.5 active:scale-[0.98] group" style="animation-delay:0.15s">
                <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                    <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path stroke-linecap="round" d="M2 10h20"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-gray-900">Cash Advances</span>
                        @if($pendingCashAdvances > 0)
                            <span class="inline-flex items-center justify-center min-w-[18px] h-[18px] rounded-full bg-amber-100 text-amber-700 text-[9px] font-bold px-1">{{ $pendingCashAdvances }}</span>
                        @endif
                    </div>
                    <div class="text-[10px] text-gray-400 mt-0.5">?{{ number_format($totalBorrowed, 0) }} borrowed total</div>
                </div>
                <svg class="w-3.5 h-3.5 text-gray-300 shrink-0 group-hover:text-gray-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>

            {{-- Salary Loans --}}
            <a href="{{ route('employee.salary-loans.index') }}" class="emp-scale-in rounded-xl bg-white px-5 py-3.5 shadow-sm border border-gray-100 flex items-center gap-3.5 no-underline transition-all hover:shadow-md hover:-translate-y-0.5 active:scale-[0.98] group" style="animation-delay:0.2s">
                <div class="w-9 h-9 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                    <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-gray-900">Salary Loans</span>
                        @if($activeLoans > 0)
                            <span class="inline-flex items-center justify-center min-w-[18px] h-[18px] rounded-full bg-amber-100 text-amber-700 text-[9px] font-bold px-1">{{ $activeLoans }}</span>
                        @endif
                    </div>
                    <div class="text-[10px] text-gray-400 mt-0.5">?{{ number_format($totalLoanRemaining, 0) }} remaining</div>
                </div>
                <svg class="w-3.5 h-3.5 text-gray-300 shrink-0 group-hover:text-gray-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        {{-- RIGHT COLUMN (1/3): Calendar --}}
        <div class="space-y-3 max-w-[280px] justify-self-end">
            <div class="sticky top-24">
                @include('partials.dashboard-calendar')
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
(function () {
    /* Attendance status fetch */
    var statusBadge = document.getElementById('attendance-status-badge');
    var statusTime  = document.getElementById('attendance-status-time');
    if (!statusBadge) return;

    function makeBadge(type) {
        if (type === 'time_in') return '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-gradient-to-r from-emerald-50 to-green-50 text-emerald-700 border border-emerald-200 shadow-sm"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Timed In</span>';
        if (type === 'time_out') return '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-gradient-to-r from-amber-50 to-orange-50 text-amber-700 border border-amber-200 shadow-sm"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Timed Out</span>';
        return '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-gradient-to-r from-gray-50 to-slate-50 text-gray-500 border border-gray-200"><span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> No Record</span>';
    }

    fetch("{{ route('attendance.lastlog') }}", { headers: { 'Accept': 'application/json' } })
        .then(r => r.ok ? r.json() : Promise.reject())
        .then(data => {
            if (data && data.type) {
                statusBadge.innerHTML = makeBadge(data.type);
                statusTime.textContent = data.time ?? '';
            } else {
                statusBadge.innerHTML = makeBadge(null);
            }
        })
        .catch(() => { statusBadge.innerHTML = makeBadge(null); });
})();
</script>
@endpush
@endsection
