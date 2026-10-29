@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeSlideUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes scaleIn { 0%{opacity:0;transform:scale(0.92)} 100%{opacity:1;transform:scale(1)} }
@keyframes slideInRight { 0%{opacity:0;transform:translateX(16px)} 100%{opacity:1;transform:translateX(0)} }
@keyframes bounceIn { 0%{opacity:0;transform:scale(0.6)} 60%{transform:scale(1.05)} 80%{transform:scale(0.95)} 100%{opacity:1;transform:scale(1)} }
@keyframes countUp { 0%{opacity:0;transform:translateY(8px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes slideUpBounce { 0%{opacity:0;transform:translateY(24px)} 60%{transform:translateY(-4px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes pulseGlow { 0%,100%{box-shadow:0 0 0 0 rgba(200,41,42,0.4)} 50%{box-shadow:0 0 0 8px rgba(200,41,42,0)} }
@keyframes floatSlow { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-4px)} }
@keyframes shimmer { 0%{background-position:-200% 0} 100%{background-position:200% 0} }
@keyframes pulseDot { 0%,100%{opacity:0.4;transform:scale(1)} 50%{opacity:1;transform:scale(1.3)} }
.fade-up { animation:fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.scale-in { animation:scaleIn 0.35s cubic-bezier(0.16,1,0.3,1) both; }
.slide-right { animation:slideInRight 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.rc-bounce { animation:bounceIn 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.rc-count-num { animation:countUp 0.6s cubic-bezier(0.16,1,0.3,1) both; }
.rc-slide-bounce { animation:slideUpBounce 0.6s cubic-bezier(0.16,1,0.3,1) both; }
.rc-float { animation:floatSlow 3s ease-in-out infinite; }
.rc-pulse { animation:pulseGlow 2s ease-in-out infinite; }
@keyframes drawLine { to { stroke-dashoffset: 0; } }
@keyframes fadeInDot { to { opacity: 1; } }
.rc-chart-line { stroke-dasharray: 800; stroke-dashoffset: 800; animation: drawLine 1s cubic-bezier(0.16,1,0.3,1) 0.15s forwards; }
.rc-chart-dot { opacity: 0; animation: fadeInDot 0.25s ease both; }
</style>
@endpush

@section('content')
@php
$margin = $totalCollections > 0 ? round(($totalNetRemittance / $totalCollections) * 100, 1) : 0;
$mctMax = collect($monthlyCollectionTrend)->max('total_collection') ?: 1;
$mctActive = count($monthlyCollectionTrend) - 1;
@endphp

<div class="flex flex-col lg:flex-row gap-5 items-start">

    {{-- LEFT COLUMN --}}
    <div class="flex-1 min-w-0 w-full space-y-5">
            {{-- Quick Actions --}}
            <div class="rc-slide-bounce kpi-card overflow-hidden" style="animation-delay:0.2s">
                <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
                    <span class="text-[13px] font-semibold text-gray-900">Quick Actions</span>
                    <span class="text-[11px] text-gray-400">Remittance modules</span>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4">
                    <a href="{{ route('remittances.create') }}" class="qa-tile group transition-all hover:bg-emerald-50 hover:border-emerald-200 no-underline">
                        <div class="qa-icon bg-emerald-100 text-emerald-600 transition-colors group-hover:bg-emerald-500 group-hover:text-white">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        </div>
                        <span class="qa-label group-hover:text-emerald-700 transition-colors">Record Remittance</span>
                    </a>
                    <a href="{{ route('short-remittances.index') }}" class="qa-tile group transition-all hover:bg-amber-50 hover:border-amber-200 no-underline">
                        <div class="qa-icon bg-amber-100 text-amber-600 transition-colors group-hover:bg-amber-500 group-hover:text-white">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span class="qa-label group-hover:text-amber-700 transition-colors">Short Remittance</span>
                    </a>
                    <a href="{{ route('drivers.index') }}" class="qa-tile group transition-all hover:bg-indigo-50 hover:border-indigo-200 no-underline">
                        <div class="qa-icon bg-indigo-100 text-indigo-600 transition-colors group-hover:bg-indigo-500 group-hover:text-white">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <span class="qa-label group-hover:text-indigo-700 transition-colors">Drivers</span>
                    </a>
                    <a href="{{ route('paos.index') }}" class="qa-tile group transition-all hover:bg-purple-50 hover:border-purple-200 no-underline">
                        <div class="qa-icon bg-purple-100 text-purple-600 transition-colors group-hover:bg-purple-500 group-hover:text-white">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <span class="qa-label group-hover:text-purple-700 transition-colors">PAOs</span>
                    </a>
                </div>
            </div>

            @include('partials.daily-remittance-card')





    </div>

    {{-- RIGHT COLUMN: Calendar --}}
    <div class="w-full lg:w-[280px] lg:shrink-0 space-y-5">
        <div class="lg:sticky lg:top-24 space-y-5">

        @include('partials.dashboard-calendar')
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
(function () {
    /* -- Monthly trend interaction -- */
    const cols = document.querySelectorAll('#mct-bars > div');
    const detMonth = document.getElementById('mct-detail-month');
    const detVal = document.getElementById('mct-detail-val');
    if (cols.length && detMonth) {
        function fmt(n) { return '₱' + Number(n).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
        function activate(el) {
            cols.forEach(c => {
                c.querySelector('div:first-child')?.classList.remove('bg-gray-900');
                c.querySelector('div:first-child')?.classList.add('bg-gray-200');
                c.querySelector('span')?.classList.remove('text-gray-900');
            });
            const bar = el.querySelector('div:first-child');
            if (bar) { bar.classList.remove('bg-gray-200'); bar.classList.add('bg-gray-900'); }
            const lbl = el.querySelector('span');
            if (lbl) lbl.classList.add('text-gray-900');
            const raw = parseFloat(el.dataset.val) || 0;
            detMonth.textContent = el.dataset.month;
            const prev = parseFloat(detVal.textContent.replace(/[^0-9.]/g, '')) || 0;
            if (prev === raw) { detVal.textContent = fmt(raw); return; }
            const steps = 18, dur = 200;
            let cur = 0;
            const inc = (raw - prev) / steps;
            const t = setInterval(() => {
                cur++;
                detVal.textContent = fmt(Math.round(prev + inc * cur));
                if (cur >= steps) { detVal.textContent = fmt(raw); clearInterval(t); }
            }, dur / steps);
        }
        cols.forEach(col => {
            col.addEventListener('mouseenter', () => activate(col));
            col.addEventListener('click', () => activate(col));
        });
    }

    /* -- Count-up animation for stat values -- */
    document.querySelectorAll('.rc-count-num').forEach(el => {
        const text = el.textContent.trim();
        const prefix = text.startsWith('₱') ? '₱' : '';
        const target = parseFloat(text.replace(/[₱?,]/g, '')) || 0;
        if (target === 0) return;
        const steps = 24, dur = 500;
        let cur = 0;
        const inc = target / steps;
        const t = setInterval(() => {
            cur++;
            const val = inc * cur;
            el.textContent = prefix + val.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            if (cur >= steps) { el.textContent = text; clearInterval(t); }
        }, dur / steps);
    });
})();
</script>
@endpush
