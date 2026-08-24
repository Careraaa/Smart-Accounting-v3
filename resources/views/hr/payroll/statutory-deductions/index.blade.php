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
@php
    $grouped = $deductions->groupBy('name');
    $counts  = $grouped->map->count();
    $wtGrouped = $taxes->groupBy('description');
    $wtCounts = $wtGrouped->map->count();

    $pagibigMax = $deductions->where('name','Pag-IBIG')->max('percentage_employee');
    $sssMax     = $deductions->where('name','SSS')->max('employee_share');
    $philMax    = $deductions->where('name','PhilHealth')->max('percentage_employee');
    $wtMax      = $taxes->max('employee_share');
@endphp

<div class="space-y-5">

    {{-- Header --}}
    <div class="fade-up flex items-start justify-between gap-4 flex-wrap">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Statutory Deductions</h1>
            <p class="text-sm text-gray-400 mt-0.5">Government-mandated contribution schedules for payroll computation</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="https://mpm.ph/pagibig-hdmf-table/" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-600 transition-all hover:border-emerald-400 hover:text-emerald-600 active:scale-[0.97]">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                Pag-IBIG
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
            <a href="https://www.sss.gov.ph/sss-contribution-table/" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-600 transition-all hover:border-blue-400 hover:text-blue-600 active:scale-[0.97]">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                SSS
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
            <a href="https://www.philhealth.gov.ph/advisories/2025/PA2025-0002.pdf" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-600 transition-all hover:border-red-400 hover:text-red-600 active:scale-[0.97]">
                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                PhilHealth
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
            <a href="https://www.bir.gov.ph/WithHoldingTax" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-600 transition-all hover:border-orange-400 hover:text-orange-600 active:scale-[0.97]">
                <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                BIR
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-400 font-medium">Pag-IBIG (HDMF)</p>
                    <p class="text-lg font-bold text-gray-900 tabular-nums mt-0.5">{{ $pagibigMax ?? 2 }}%</p>
                    <p class="text-[0.55rem] text-gray-400 font-mono mt-0.5">ee rate &middot; max ₱100/mo</p>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-400 font-medium">SSS</p>
                    <p class="text-lg font-bold text-gray-900 tabular-nums mt-0.5">₱{{ number_format($sssMax ?? 1000, 2) }}</p>
                    <p class="text-[0.55rem] text-gray-400 font-mono mt-0.5">max ee share &middot; {{ $counts['SSS'] ?? 0 }} brackets</p>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-red-50 text-red-500 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-400 font-medium">PhilHealth (PHIL)</p>
                    <p class="text-lg font-bold text-gray-900 tabular-nums mt-0.5">{{ $philMax ?? 2.5 }}%</p>
                    <p class="text-[0.55rem] text-gray-400 font-mono mt-0.5">ee share &middot; 5% total premium</p>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-400 font-medium">Withholding Tax (BIR)</p>
                    <p class="text-lg font-bold text-gray-900 tabular-nums mt-0.5">₱{{ number_format($wtMax ?? 0, 2) }}</p>
                    <p class="text-[0.55rem] text-gray-400 font-mono mt-0.5">max base &middot; {{ $wtGrouped->count() }} {{ Str::plural('frequency', $wtGrouped->count()) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Main layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_240px] gap-5 items-start">

        {{-- Table column --}}
        <div class="fade-up">
            {{-- Filter bar --}}
            <div class="flex items-center gap-3 mb-3">
                <div class="flex-1 min-w-0">
                    <input type="text" id="sdSearch" placeholder="Search by type…" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-700 bg-white outline-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
                </div>
                <select id="sdFilter" class="border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-700 bg-white outline-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100 cursor-pointer">
                    <option value="">All Types</option>
                    @foreach($grouped->keys() as $gname)
                        <option value="{{ $gname }}">{{ $gname }}</option>
                    @endforeach
                    <option value="Withholding Tax">Withholding Tax</option>
                </select>
            </div>

            {{-- Table --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                @if($deductions->count())
                <div class="overflow-x-auto max-h-[560px] overflow-y-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-50 bg-gray-50/50">
                                <th class="text-left text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Type</th>
                                <th class="text-left text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Salary Range</th>
                                <th class="text-left text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Employee Share</th>
                                <th class="text-left text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Employer Share</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50" id="sdTbody">
                            @foreach($grouped as $gname => $rows)
                            @php
                                $swatch = match(true) { str_contains($gname,'Pag') => 'bg-emerald-500', str_contains($gname,'SSS') => 'bg-blue-500', default => 'bg-red-500' };
                                $iconCls = match(true) { str_contains($gname,'Pag') => 'bg-emerald-50 text-emerald-600', str_contains($gname,'SSS') => 'bg-blue-50 text-blue-600', default => 'bg-red-50 text-red-500' };
                                $abbr = match(true) { str_contains($gname,'Pag') => 'P', str_contains($gname,'SSS') => 'S', default => 'PH' };
                            @endphp
                            <tr class="bg-gray-50/50" data-group="{{ $gname }}">
                                <td colspan="4" class="px-4 py-2">
                                    <div class="flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $swatch }}"></span>
                                        <span class="text-[0.55rem] font-semibold uppercase tracking-wide text-gray-500">{{ $gname }}</span>
                                        <span class="text-[0.5rem] font-mono font-medium text-gray-400 bg-gray-200/60 px-1.5 py-0.5 rounded-full">{{ $rows->count() }} {{ Str::plural('bracket', $rows->count()) }}</span>
                                    </div>
                                </td>
                            </tr>
                            @foreach($rows as $d)
                            @php
                                $isMaxed = $d->max_salary >= 999999;
                                $hasEeFixed = $d->employee_share !== null;
                                $hasErFixed = $d->employer_share !== null;
                                $hasEePct   = $d->percentage_employee !== null;
                                $hasErPct   = $d->percentage_employer !== null;
                                $isLastSss  = $gname === 'SSS' && $isMaxed;
                            @endphp
                            <tr data-type="{{ $gname }}" class="hover:bg-gray-50/40 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-6 h-6 rounded-lg {{ $iconCls }} flex items-center justify-center text-[9px] font-extrabold shrink-0">{{ $abbr }}</span>
                                        <span class="text-xs font-semibold text-gray-900">{{ $gname }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-1 font-mono tabular-nums text-xs text-gray-700">
                                        <span>₱{{ number_format($d->min_salary, 2) }}</span>
                                        <span class="text-gray-300">{{ $isMaxed ? '+' : '–' }}</span>
                                        @unless($isMaxed)
                                            <span>₱{{ number_format($d->max_salary, 2) }}</span>
                                        @endunless
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-col gap-1 items-start">
                                        @if($hasEeFixed)
                                            <span class="font-mono tabular-nums text-xs font-semibold text-gray-900">₱{{ number_format($d->employee_share, 2) }}</span>
                                            @if($isLastSss)
                                                <span class="inline-flex items-center gap-1 text-[0.5rem] font-semibold px-1.5 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-200">MPF applies</span>
                                            @elseif($hasEePct)
                                                <span class="text-[0.5rem] font-mono text-gray-400">or {{ $d->percentage_employee }}% if higher</span>
                                            @endif
                                        @elseif($hasEePct)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[0.55rem] font-semibold bg-red-50 text-red-600">{{ $d->percentage_employee }}% of salary</span>
                                            @if($gname === 'Pag-IBIG' && $d->min_salary > 1500)
                                                <span class="text-[0.5rem] font-mono text-gray-400">capped at ₱100/mo</span>
                                            @endif
                                        @else
                                            <span class="text-xs font-mono text-gray-300">—</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-col gap-1 items-start">
                                        @if($hasErFixed)
                                            <span class="font-mono tabular-nums text-xs font-semibold text-gray-900">₱{{ number_format($d->employer_share, 2) }}</span>
                                            @if($hasErPct)
                                                <span class="text-[0.5rem] font-mono text-gray-400">or {{ $d->percentage_employer }}% if higher</span>
                                            @endif
                                        @elseif($hasErPct)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[0.55rem] font-semibold bg-blue-50 text-blue-600">{{ $d->percentage_employer }}% of salary</span>
                                        @else
                                            <span class="text-xs font-mono text-gray-300">—</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            @endforeach

                            {{-- Withholding Tax Brackets --}}
                            @foreach($wtGrouped as $frequency => $wtRows)
                            <tr class="bg-gray-50/50" data-group="Withholding Tax ({{ $frequency }})">
                                <td colspan="4" class="px-4 py-2">
                                    <div class="flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                                        <span class="text-[0.55rem] font-semibold uppercase tracking-wide text-gray-500">Withholding Tax ({{ $frequency }})</span>
                                        <span class="text-[0.5rem] font-mono font-medium text-gray-400 bg-gray-200/60 px-1.5 py-0.5 rounded-full">{{ $wtRows->count() }} {{ Str::plural('bracket', $wtRows->count()) }}</span>
                                    </div>
                                </td>
                            </tr>
                            @foreach($wtRows as $wt)
                            @php $wtIsMaxed = $wt->max_salary >= 999999; @endphp
                            <tr data-type="Withholding Tax" class="hover:bg-gray-50/40 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-6 h-6 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center text-[9px] font-extrabold shrink-0">WT</span>
                                        <span class="text-xs font-semibold text-gray-900">Withholding Tax</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-1 font-mono tabular-nums text-xs text-gray-700">
                                        <span>₱{{ number_format($wt->min_salary, 2) }}</span>
                                        <span class="text-gray-300">{{ $wtIsMaxed ? '+' : '–' }}</span>
                                        @unless($wtIsMaxed)
                                            <span>₱{{ number_format($wt->max_salary, 2) }}</span>
                                        @endunless
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-col gap-1 items-start">
                                        @if($wt->employee_share)
                                            <span class="font-mono tabular-nums text-xs font-semibold text-gray-900">₱{{ number_format($wt->employee_share, 2) }}</span>
                                        @endif
                                        @if($wt->percentage_employee)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[0.55rem] font-semibold bg-red-50 text-red-600">{{ $wt->percentage_employee }}% + base</span>
                                        @endif
                                        @if(!$wt->employee_share && !$wt->percentage_employee)
                                            <span class="text-xs font-mono text-gray-300">—</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-xs font-mono text-gray-300">—</span>
                                </td>
                            </tr>
                            @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div id="sdNoResults" class="hidden">
                    <div class="flex flex-col items-center py-12 text-center">
                        <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-300 mb-3">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
                        </div>
                        <p class="text-sm font-semibold text-gray-500">No results found</p>
                        <p class="text-xs text-gray-400 mt-0.5">Try a different filter.</p>
                    </div>
                </div>
                @else
                    <div class="flex flex-col items-center py-12 text-center">
                        <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-300 mb-3">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <p class="text-sm font-semibold text-gray-500">No statutory deductions configured</p>
                        <p class="text-xs text-gray-400 mt-0.5">Run the seeder to populate contribution schedules.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="fade-up">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-50">
                    <p class="text-xs font-bold text-gray-900 uppercase tracking-wide">Contribution Types</p>
                </div>
                <div class="divide-y divide-gray-50">
                    <div class="flex items-start gap-3 px-4 py-3">
                        <span class="w-2 h-2 rounded-full bg-blue-500 shrink-0 mt-1"></span>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-gray-900">SSS</p>
                            <p class="text-[0.55rem] text-gray-400 mt-0.5">Fixed amount per bracket &middot; {{ $counts['SSS'] ?? 0 }} salary ranges &middot; max ₱1,000 ee</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 px-4 py-3">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0 mt-1"></span>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-gray-900">Pag-IBIG (HDMF)</p>
                            <p class="text-[0.55rem] text-gray-400 mt-0.5">% of salary &middot; capped at ₱100/mo ee share</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 px-4 py-3">
                        <span class="w-2 h-2 rounded-full bg-red-500 shrink-0 mt-1"></span>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-gray-900">PhilHealth (PHIL)</p>
                            <p class="text-[0.55rem] text-gray-400 mt-0.5">Mixed — fixed floor/ceiling, % in between &middot; 5% total split equally</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 px-4 py-3">
                        <span class="w-2 h-2 rounded-full bg-orange-500 shrink-0 mt-1"></span>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-gray-900">Withholding Tax (BIR)</p>
                            <p class="text-[0.55rem] text-gray-400 mt-0.5">{{ $wtCounts->sum() ?? 0 }} brackets &middot; 4 frequencies (Daily, Weekly, Semi-mo, Monthly)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
(function(){
    var s=document.getElementById('sdSearch'),f=document.getElementById('sdFilter');
    var tbody=document.getElementById('sdTbody'),nr=document.getElementById('sdNoResults');
    if(!s||!tbody) return;
    function run(){
        var q=s.value.toLowerCase().trim(),tp=f.value;
        var dataRows=Array.from(tbody.querySelectorAll('tr[data-type]'));
        var sepRows=Array.from(tbody.querySelectorAll('tr[data-group]'));
        dataRows.forEach(function(r){
            var match=(!q||r.dataset.type.toLowerCase().includes(q))&&(!tp||r.dataset.type===tp);
            r.style.display=match?'':'none';
        });
        sepRows.forEach(function(sep){
            var group=sep.dataset.group;
            var anyVisible=dataRows.some(function(r){return r.dataset.type===group&&r.style.display!=='none';});
            sep.style.display=anyVisible?'':'none';
        });
        if(nr)nr.style.display=dataRows.every(function(r){return r.style.display==='none';})?'':'none';
    }
    s.addEventListener('input',run);
    f.addEventListener('change',run);
})();
</script>
@endpush
