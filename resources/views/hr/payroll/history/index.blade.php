@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeSlideUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes scaleIn { 0%{opacity:0;transform:scale(0.92)} 100%{opacity:1;transform:scale(1)} }
.stat-card { animation:scaleIn 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.stat-card:nth-child(1) { animation-delay:0.05s; }
.stat-card:nth-child(2) { animation-delay:0.1s; }
.stat-card:nth-child(3) { animation-delay:0.15s; }
.stat-card:nth-child(4) { animation-delay:0.2s; }
.fade-up { animation:fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) both; }
</style>
@endpush

@section('content')
<div class="space-y-5">

    {{-- Flash messages --}}
    @foreach(['success','error','info'] as $t)
        @if(session($t))
        <div class="fade-up flex items-center gap-2.5 px-4 py-3 rounded-xl text-sm font-medium
            {{ $t === 'success' ? 'bg-emerald-50 border border-emerald-200 text-emerald-700' : '' }}
            {{ $t === 'error' ? 'bg-red-50 border border-red-200 text-red-700' : '' }}
            {{ $t === 'info' ? 'bg-blue-50 border border-blue-200 text-blue-700' : '' }}">
            @if($t === 'success')
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            @else
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
            @endif
            <span class="flex-1">{{ session($t) }}</span>
            <button type="button" onclick="this.parentElement.remove()" class="text-{{ $t === 'success' ? 'emerald' : ($t === 'error' ? 'red' : 'blue') }}-500 hover:text-{{ $t === 'success' ? 'emerald' : ($t === 'error' ? 'red' : 'blue') }}-700 cursor-pointer bg-transparent border-none p-0 leading-none">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        @endif
    @endforeach

    {{-- Header --}}
    <div class="fade-up flex items-start justify-between gap-4 flex-wrap">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Payroll History</h1>
            <p class="text-sm text-gray-400 mt-0.5">View all finalized and released payroll batches</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('hr.reports.print.payroll-history-report') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-600 transition-all hover:border-gray-400 hover:text-gray-900 active:scale-[0.97 no-underline">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                Print Report
            </a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path stroke-linecap="round" d="M8 21h8M12 17v4"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-400 font-medium">Total Batches</p>
                    <p class="text-lg font-bold text-gray-900 tabular-nums mt-0.5">{{ $totalBatches }}</p>
                    <p class="text-[0.55rem] text-gray-400 font-mono mt-0.5">generated periods</p>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-400 font-medium">Total Payroll</p>
                    <p class="text-lg font-bold text-gray-900 tabular-nums mt-0.5">₱{{ number_format($totalPayroll,2) }}</p>
                    <p class="text-[0.55rem] text-gray-400 font-mono mt-0.5">all time</p>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-400 font-medium">Released Payrolls</p>
                    <p class="text-lg font-bold text-gray-900 tabular-nums mt-0.5">{{ $totalReleased }}</p>
                    <p class="text-[0.55rem] text-gray-400 font-mono mt-0.5">across all batches</p>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-red-50 text-red-500 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-400 font-medium">Next Cutoff</p>
                    <p class="text-lg font-bold text-gray-900 tabular-nums mt-0.5" style="font-size:1rem;">{{ $nextCutoffDate ? $nextCutoffDate->format('M d, Y') : 'N/A' }}</p>
                    <p class="text-[0.55rem] text-gray-400 font-mono mt-0.5">{{ $nextCutoffDate ? $nextCutoffDate->format('l') : '—' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Section head --}}
    <div class="fade-up flex items-center gap-2">
        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
        <h2 class="text-sm font-bold text-gray-900">Payroll Batches</h2>
    </div>

    {{-- Filter bar --}}
    <div class="fade-up flex items-center gap-3 flex-wrap">
        <div class="flex-1 min-w-[200px]">
            <input type="text" id="prlSearch" placeholder="Search by period…" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-700 bg-white outline-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
        </div>
        <select id="filterMonth" onchange="applyFilter()" class="border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-700 bg-white outline-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100 cursor-pointer">
            <option value="">All Months</option>
            @for($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" {{ $filterMonth == $i ? 'selected' : '' }}>{{ \Carbon\Carbon::createFromDate(null, $i)->format('F') }}</option>
            @endfor
        </select>
        <select id="filterYear" onchange="applyFilter()" class="border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-700 bg-white outline-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100 cursor-pointer">
            <option value="">All Years</option>
            @for($i = now()->year - 5; $i <= now()->year; $i++)
                <option value="{{ $i }}" {{ $filterYear == $i ? 'selected' : '' }}>{{ $i }}</option>
            @endfor
        </select>
    </div>

    {{-- Batch cards --}}
    <div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="divide-y divide-gray-50" id="prlGrid">
            @forelse($batches as $batch)
                @php
                    $startDate = $batch->period_start;
                    $endDate   = $batch->period_end;
                    $isFirst   = $startDate->format('d') <= 15;
                    $statusColors = [
                        'finalized' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'dot' => 'bg-blue-500'],
                        'submitted' => ['bg' => 'bg-violet-50', 'text' => 'text-violet-700', 'border' => 'border-violet-200', 'dot' => 'bg-violet-500'],
                        'approved'  => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'dot' => 'bg-emerald-500'],
                        'released'  => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'dot' => 'bg-emerald-500'],
                        'paid'      => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'dot' => 'bg-emerald-500'],
                    ];
                    $si = $statusColors[$batch->status ?? 'submitted'] ?? $statusColors['submitted'];
                    $batchUrl = route('payroll.history.batch', ['start' => $batch->period_start->format('Y-m-d'), 'end' => $batch->period_end->format('Y-m-d')]);
                @endphp
                <div class="batch-card">
                    <a href="{{ $batchUrl }}" class="block no-underline text-inherit">
                        <div class="flex items-center gap-4 px-5 py-4 transition-colors hover:bg-gray-50/60">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 {{ $isFirst ? 'bg-gradient-to-br from-red-600 to-red-800' : 'bg-gradient-to-br from-sky-600 to-blue-800' }} text-white">
                                @if($isFirst)
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-gray-900">{{ $startDate->format('F Y') }} — {{ $isFirst ? '1st' : '2nd' }} half</p>
                                <p class="text-[0.55rem] font-mono text-gray-400 mt-0.5">{{ $startDate->format('M d') }} – {{ $endDate->format('M d, Y') }}</p>
                            </div>
                            <div class="flex items-center gap-5 shrink-0">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[0.55rem] font-semibold border {{ $si['bg'] }} {{ $si['text'] }} {{ $si['border'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $si['dot'] }}"></span>
                                    @switch($batch->status)
                                        @case('finalized') Finalized @break
                                        @case('submitted') Submitted @break
                                        @case('approved') Approved @break
                                        @case('released') Released @break
                                        @case('paid') Paid @break
                                        @default {{ ucfirst($batch->status) }}
                                    @endswitch
                                </span>
                                <div class="text-center">
                                    <p class="text-[0.5rem] font-semibold uppercase tracking-wide text-gray-400">Employees</p>
                                    <p class="text-sm font-bold text-gray-900 tabular-nums mt-0.5">{{ $batch->payrolls->count() }}</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-[0.5rem] font-semibold uppercase tracking-wide text-gray-400">Net Pay</p>
                                    <p class="text-sm font-bold text-emerald-600 tabular-nums mt-0.5">₱{{ number_format($batch->total_net_pay ?? $batch->payrolls->sum('net_pay') ?? 0, 2) }}</p>
                                </div>
                                <svg class="w-4 h-4 text-gray-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="flex flex-col items-center py-12 text-center">
                    <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-300 mb-3">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><rect x="2" y="3" width="20" height="14" rx="2"/><path stroke-linecap="round" d="M8 21h8M12 17v4"/></svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-500">No payroll batches yet</p>
                    <p class="text-xs text-gray-400 mt-0.5">Generated batches will appear here.</p>
                </div>
            @endforelse
        </div>
        <div id="prlNoResults" class="hidden">
            <div class="flex flex-col items-center py-8 text-center">
                <p class="text-sm font-semibold text-gray-500">No results found</p>
                <p class="text-xs text-gray-400 mt-0.5">Try a different search or filter.</p>
            </div>
        </div>
        @if($batches->hasPages())
        <div class="px-5 py-3 border-t border-gray-50 flex justify-end text-xs">{{ $batches->appends(request()->query())->links() }}</div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
(function(){
    var s=document.getElementById('prlSearch'),bg=document.getElementById('prlGrid'),nr=document.getElementById('prlNoResults');
    if(!bg)return;
    s.addEventListener('input',function(){
        var q=this.value.toLowerCase().trim(),rows=Array.from(bg.querySelectorAll('.batch-card')),vis=rows.filter(function(r){return !q||r.textContent.toLowerCase().includes(q);});
        rows.forEach(function(r){r.style.display='none';});
        vis.forEach(function(r){r.style.display='';});
        nr.style.display=vis.length===0&&rows.length>0?'':'none';
    });
})();
function applyFilter(){
    var month=document.getElementById('filterMonth').value,year=document.getElementById('filterYear').value,url="{{ route('payroll.history.index') }}",params=[];
    if(month)params.push('filter_month='+month);if(year)params.push('filter_year='+year);
    if(params.length)url+='?'+params.join('&');window.location.href=url;
}
</script>
@endpush
