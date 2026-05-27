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
.tab-panel.active-panel { animation: fadeSlideUp 0.45s cubic-bezier(0.16, 1, 0.3, 1) both; }
.stat-card { animation: scaleIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) both; }
.stat-card:nth-child(1) { animation-delay: 0.05s; }
.stat-card:nth-child(2) { animation-delay: 0.1s; }
.table-wrap { animation: fadeSlideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) 0.15s both; }
.filter-bar { animation: slideInRight 0.4s cubic-bezier(0.16, 1, 0.3, 1) 0.1s both; }
</style>
@endpush

@section('content')
<div class="max-w-full">

    {{-- Flash --}}
    @foreach(['success','error'] as $t)
        @if(session($t))
        <div class="flex items-center gap-2.5 px-4 py-3 mb-5 rounded-xl text-sm font-medium border {{ $t === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-red-50 border-red-200 text-red-700' }}" style="animation:fadeSlideUp 0.35s ease both;">
            @if($t==='success')
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            @else
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
            @endif
            {{ session($t) }}
        </div>
        @endif
    @endforeach

    {{-- Header --}}
    <div class="flex items-start justify-between mb-6 flex-wrap gap-4" style="animation:fadeSlideUp 0.4s cubic-bezier(0.16,1,0.3,1) both;">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Holiday Management</h1>
            <p class="text-sm text-gray-500 mt-0.5">Configure holidays for payroll computation</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('holiday.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded-xl hover:bg-gray-800 hover:shadow-lg hover:shadow-gray-900/20 active:scale-[0.97] transition-all duration-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Add Holiday
            </a>
        </div>
    </div>

    {{-- Color-Coded Tabs --}}
    <div class="border-b border-gray-200 mb-6" style="animation:fadeSlideUp 0.4s cubic-bezier(0.16,1,0.3,1) 0.05s both;">
        <nav class="flex gap-1 -mb-px" role="tablist">
            {{-- All Holidays --}}
            <button role="tab" data-tab="all"
                class="tab-btn relative px-5 py-3 text-sm font-medium border-b-2 transition-all duration-200 group
                {{ $status === 'all'
                    ? 'border-cyan-600 text-cyan-700 bg-cyan-50/60'
                    : 'border-transparent text-gray-400 hover:text-gray-600 hover:border-gray-300 hover:bg-gray-50/50' }}">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 {{ $status === 'all' ? 'text-cyan-600' : 'text-gray-400 group-hover:text-gray-500' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    All Holidays
                </span>
            </button>
            {{-- Active & Upcoming --}}
            <button role="tab" data-tab="active"
                class="tab-btn relative px-5 py-3 text-sm font-medium border-b-2 transition-all duration-200 group
                {{ $status === 'active'
                    ? 'border-emerald-500 text-emerald-700 bg-emerald-50/60'
                    : 'border-transparent text-gray-400 hover:text-gray-600 hover:border-gray-300 hover:bg-gray-50/50' }}">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 {{ $status === 'active' ? 'text-emerald-500' : 'text-gray-400 group-hover:text-gray-500' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Active &amp; Upcoming
                </span>
            </button>
        </nav>
    </div>

    {{-- ===== TAB: ALL HOLIDAYS ===== --}}
    <div id="tab-all" class="tab-panel {{ $status !== 'all' ? 'hidden' : '' }} active-panel">

        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
            <div class="stat-card bg-white border border-gray-200 rounded-xl px-5 py-4 flex items-start gap-3.5 hover:shadow-md hover:border-cyan-200 transition-all duration-300">
                <div class="w-10 h-10 rounded-lg bg-cyan-50 text-cyan-600 flex items-center justify-center shrink-0 border border-cyan-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">Total Holidays</div>
                    <div class="text-2xl font-bold text-gray-900 tabular-nums mt-0.5">{{ $holidays->total() }}</div>
                </div>
            </div>
            <div class="stat-card bg-white border border-gray-200 rounded-xl px-5 py-4 flex items-start gap-3.5 hover:shadow-md hover:border-emerald-200 transition-all duration-300">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">Regular</div>
                    <div class="text-2xl font-bold text-gray-900 tabular-nums mt-0.5">{{ $holidays->where('type','regular')->count() }}</div>
                </div>
            </div>
            <div class="stat-card bg-white border border-gray-200 rounded-xl px-5 py-4 flex items-start gap-3.5 hover:shadow-md hover:border-amber-200 transition-all duration-300">
                <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 border border-amber-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">Special</div>
                    <div class="text-2xl font-bold text-gray-900 tabular-nums mt-0.5">{{ $holidays->where('type','special')->count() }}</div>
                </div>
            </div>
        </div>

        {{-- Filter bar --}}
        <div class="filter-bar flex items-center gap-2.5 mb-4 flex-wrap">
            <span class="text-xs font-semibold text-gray-500">Year:</span>
            <select onchange="window.location='{{ route('holiday.index') }}?status=all&year=' + this.value"
                class="px-3 py-2 text-sm border border-gray-200 rounded-xl bg-white text-gray-700 outline-none transition-all duration-200 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-200/50 hover:border-gray-300 cursor-pointer">
                @foreach($availableYears as $yr)
                    <option value="{{ $yr }}" @selected($year == $yr)>{{ $yr }}</option>
                @endforeach
            </select>
        </div>

        {{-- Table --}}
        <div class="table-wrap bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition-shadow duration-300">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Holiday Name</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Date</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Type</th>
                            <th class="text-center px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($holidays as $holiday)
                        <tr class="border-b border-gray-100 hover:bg-gray-50/50 transition-colors duration-150 cursor-pointer"
                            onclick="window.location='{{ route('holiday.edit', $holiday) }}'">
                            <td class="px-5 py-3.5">
                                <div class="font-semibold text-gray-900">{{ $holiday->name }}</div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="font-mono text-sm text-gray-500">{{ $holiday->date->format('l, M d, Y') }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold border
                                    {{ $holiday->type === 'regular' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $holiday->type === 'regular' ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                    {{ ucfirst($holiday->type) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('holiday.edit', $holiday) }}"
                                       class="w-7 h-7 rounded-lg bg-gray-100 text-gray-500 hover:bg-blue-50 hover:text-blue-600 flex items-center justify-center transition-all duration-200">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('holiday.destroy', $holiday) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this holiday?')">
                                        @csrf @method('DELETE')
                                        <button class="w-7 h-7 rounded-lg bg-gray-100 text-gray-500 hover:bg-red-50 hover:text-red-600 flex items-center justify-center transition-all duration-200">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="100">
                                <div class="flex flex-col items-center justify-center py-16 text-center">
                                    <div class="w-14 h-14 rounded-xl bg-gray-100 flex items-center justify-center text-gray-300 mb-4">
                                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-700">No holidays found</p>
                                    <p class="text-xs text-gray-400 mt-1">Add a holiday to get started.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if(method_exists($holidays, 'hasPages') && $holidays->hasPages())
            <div class="flex flex-wrap items-center justify-between px-5 py-3 border-t border-gray-100 bg-gray-50/50 gap-3">
                <div class="text-xs text-gray-400">
                    Showing
                    <strong class="text-gray-600">{{ $holidays->firstItem() }}</strong>&ndash;<strong class="text-gray-600">{{ $holidays->lastItem() }}</strong>
                    of
                    <strong class="text-gray-600">{{ $holidays->total() }}</strong> holidays
                </div>
                <nav class="flex items-center gap-1">
                    @if($holidays->onFirstPage())
                        <span class="flex items-center justify-center w-7 h-7 rounded-lg text-xs font-semibold bg-gray-50 text-gray-300 border border-gray-100 cursor-not-allowed">‹</span>
                    @else
                        <a href="{{ $holidays->previousPageUrl() }}" class="flex items-center justify-center w-7 h-7 rounded-lg text-xs font-semibold bg-white text-gray-600 border border-gray-200 hover:bg-gray-100 transition-colors no-underline">‹</a>
                    @endif

                    @foreach($holidays->getUrlRange(max(1, $holidays->currentPage() - 2), min($holidays->lastPage(), $holidays->currentPage() + 2)) as $page => $url)
                        <a href="{{ $url }}"
                           class="flex items-center justify-center w-7 h-7 rounded-lg text-xs font-semibold transition-colors no-underline
                            {{ $page === $holidays->currentPage() ? 'bg-gray-900 text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-100' }}">
                            {{ $page }}
                        </a>
                    @endforeach

                    @if($holidays->hasMorePages())
                        <a href="{{ $holidays->nextPageUrl() }}" class="flex items-center justify-center w-7 h-7 rounded-lg text-xs font-semibold bg-white text-gray-600 border border-gray-200 hover:bg-gray-100 transition-colors no-underline">›</a>
                    @else
                        <span class="flex items-center justify-center w-7 h-7 rounded-lg text-xs font-semibold bg-gray-50 text-gray-300 border border-gray-100 cursor-not-allowed">›</span>
                    @endif
                </nav>
            </div>
            @endif
        </div>
    </div>

    {{-- ===== TAB: ACTIVE & UPCOMING ===== --}}
    <div id="tab-active" class="tab-panel {{ $status !== 'active' ? 'hidden' : '' }} active-panel">

        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
            <div class="stat-card bg-white border border-gray-200 rounded-xl px-5 py-4 flex items-start gap-3.5 hover:shadow-md hover:border-emerald-200 transition-all duration-300">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">Upcoming Holidays</div>
                    <div class="text-2xl font-bold text-gray-900 tabular-nums mt-0.5">{{ $holidays->total() }}</div>
                    <div class="text-xs text-gray-400 mt-1">In {{ $year }}</div>
                </div>
            </div>
            <div class="stat-card bg-white border border-gray-200 rounded-xl px-5 py-4 flex items-start gap-3.5 hover:shadow-md hover:border-gray-300 transition-all duration-300">
                <div class="w-10 h-10 rounded-lg bg-gray-100 text-gray-500 flex items-center justify-center shrink-0 border border-gray-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">Reference Year</div>
                    <div class="text-2xl font-bold text-gray-900 tabular-nums mt-0.5">{{ $year }}</div>
                    <div class="text-xs text-gray-400 mt-1">Selected period</div>
                </div>
            </div>
        </div>

        {{-- Filter bar --}}
        <div class="filter-bar flex items-center gap-2.5 mb-4 flex-wrap">
            <span class="text-xs font-semibold text-gray-500">Year:</span>
            <select onchange="window.location='{{ route('holiday.index') }}?status=active&year=' + this.value"
                class="px-3 py-2 text-sm border border-gray-200 rounded-xl bg-white text-gray-700 outline-none transition-all duration-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-200/50 hover:border-gray-300 cursor-pointer">
                @foreach($availableYears as $yr)
                    <option value="{{ $yr }}" @selected($year == $yr)>{{ $yr }}</option>
                @endforeach
            </select>
        </div>

        {{-- Table --}}
        <div class="table-wrap bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition-shadow duration-300">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Holiday Name</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Date</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Type</th>
                            <th class="text-center px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($holidays as $holiday)
                        <tr class="border-b border-gray-100 hover:bg-emerald-50/30 transition-colors duration-150 cursor-pointer"
                            onclick="window.location='{{ route('holiday.edit', $holiday) }}'">
                            <td class="px-5 py-3.5">
                                <div class="font-semibold text-gray-900">{{ $holiday->name }}</div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="font-mono text-sm text-gray-500">{{ $holiday->date->format('l, M d, Y') }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold border
                                    {{ $holiday->type === 'regular' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $holiday->type === 'regular' ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                    {{ ucfirst($holiday->type) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('holiday.edit', $holiday) }}"
                                       class="w-7 h-7 rounded-lg bg-gray-100 text-gray-500 hover:bg-blue-50 hover:text-blue-600 flex items-center justify-center transition-all duration-200">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('holiday.destroy', $holiday) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this holiday?')">
                                        @csrf @method('DELETE')
                                        <button class="w-7 h-7 rounded-lg bg-gray-100 text-gray-500 hover:bg-red-50 hover:text-red-600 flex items-center justify-center transition-all duration-200">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="100">
                                <div class="flex flex-col items-center justify-center py-16 text-center">
                                    <div class="w-14 h-14 rounded-xl bg-gray-100 flex items-center justify-center text-gray-300 mb-4">
                                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-700">No upcoming holidays</p>
                                    <p class="text-xs text-gray-400 mt-1">All holidays in this period have passed.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if(method_exists($holidays, 'hasPages') && $holidays->hasPages())
            <div class="flex flex-wrap items-center justify-between px-5 py-3 border-t border-gray-100 bg-gray-50/50 gap-3">
                <div class="text-xs text-gray-400">
                    Showing
                    <strong class="text-gray-600">{{ $holidays->firstItem() }}</strong>&ndash;<strong class="text-gray-600">{{ $holidays->lastItem() }}</strong>
                    of
                    <strong class="text-gray-600">{{ $holidays->total() }}</strong> upcoming holidays
                </div>
                <nav class="flex items-center gap-1">
                    @if($holidays->onFirstPage())
                        <span class="flex items-center justify-center w-7 h-7 rounded-lg text-xs font-semibold bg-gray-50 text-gray-300 border border-gray-100 cursor-not-allowed">‹</span>
                    @else
                        <a href="{{ $holidays->previousPageUrl() }}" class="flex items-center justify-center w-7 h-7 rounded-lg text-xs font-semibold bg-white text-gray-600 border border-gray-200 hover:bg-gray-100 transition-colors no-underline">‹</a>
                    @endif

                    @foreach($holidays->getUrlRange(max(1, $holidays->currentPage() - 2), min($holidays->lastPage(), $holidays->currentPage() + 2)) as $page => $url)
                        <a href="{{ $url }}"
                           class="flex items-center justify-center w-7 h-7 rounded-lg text-xs font-semibold transition-colors no-underline
                            {{ $page === $holidays->currentPage() ? 'bg-gray-900 text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-100' }}">
                            {{ $page }}
                        </a>
                    @endforeach

                    @if($holidays->hasMorePages())
                        <a href="{{ $holidays->nextPageUrl() }}" class="flex items-center justify-center w-7 h-7 rounded-lg text-xs font-semibold bg-white text-gray-600 border border-gray-200 hover:bg-gray-100 transition-colors no-underline">›</a>
                    @else
                        <span class="flex items-center justify-center w-7 h-7 rounded-lg text-xs font-semibold bg-gray-50 text-gray-300 border border-gray-100 cursor-not-allowed">›</span>
                    @endif
                </nav>
            </div>
            @endif
        </div>
    </div>

</div>

@push('scripts')
<script>
(function(){
    const tabs = document.querySelectorAll('.tab-btn');
    tabs.forEach(btn => {
        btn.addEventListener('click', function() {
            const tab = this.dataset.tab;
            const url = new URL(window.location);
            url.searchParams.set('status', tab);
            url.searchParams.delete('page');
            window.location.href = url.toString();
        });
    });
})();
</script>
@endpush
@endsection
