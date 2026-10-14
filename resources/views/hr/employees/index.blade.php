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
.stat-card:nth-child(3) { animation-delay: 0.15s; }
.stat-card:nth-child(4) { animation-delay: 0.2s; }
.table-wrap { animation: fadeSlideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) 0.15s both; }
.filter-bar { animation: slideInRight 0.4s cubic-bezier(0.16, 1, 0.3, 1) 0.1s both; }
.em-row-hover:hover { background: rgba(99,102,241,0.03); }
[data-em] input:focus-visible,
[data-em] select:focus-visible,
[data-em] button:focus-visible { outline: none !important; }
</style>
@endpush

@section('content')
<div class="max-w-full" data-em>

    {{-- Flash --}}
    @foreach(['success','error','info'] as $ft)
        @if(session($ft))
        <div class="flex items-center gap-2.5 px-4 py-3 mb-5 rounded-xl text-sm font-medium border
            {{ $ft==='success' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : ($ft==='error' ? 'bg-red-50 border-red-200 text-red-700' : 'bg-blue-50 border-blue-200 text-blue-700') }}"
            style="animation:fadeSlideUp .35s ease both;">
            @if($ft==='success')
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            @else
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
            @endif
            {{ session($ft) }}
        </div>
        @endif
    @endforeach

    {{-- Header --}}
    <div class="flex items-start justify-between mb-6 flex-wrap gap-4" style="animation:fadeSlideUp 0.4s cubic-bezier(0.16,1,0.3,1) both;">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Employees</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage your workforce and employee records</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('hr.reports.print.employee-report') }}" target="_blank"
               class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:border-gray-300 hover:text-gray-900 hover:shadow-sm active:scale-[0.97] transition-all duration-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print
            </a>
            <a href="{{ route('employees.create') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded-xl hover:bg-gray-800 hover:shadow-lg hover:shadow-gray-900/20 active:scale-[0.97] transition-all duration-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Add Employee
            </a>
        </div>
    </div>

    {{-- Color-Coded Tabs --}}
    <div class="border-b border-gray-200 mb-6" style="animation:fadeSlideUp 0.4s cubic-bezier(0.16,1,0.3,1) 0.05s both;">
        <nav class="flex gap-1 -mb-px" role="tablist">
            {{-- All --}}
            <button role="tab" data-tab="all"
                class="tab-btn relative px-5 py-3 text-sm font-medium border-b-2 transition-all duration-200 group
                {{ $tab === 'all'
                    ? 'border-indigo-600 text-indigo-700 bg-indigo-50/60'
                    : 'border-transparent text-gray-400 hover:text-gray-600 hover:border-gray-300 hover:bg-gray-50/50' }}">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 {{ $tab === 'all' ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-500' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                    All Employees
                    <span class="ml-1 inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-[11px] font-bold
                        {{ $tab === 'all' ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-200 text-gray-600' }}
                        transition-colors duration-200">{{ $allCount }}</span>
                </span>
            </button>
            {{-- Active --}}
            <button role="tab" data-tab="active"
                class="tab-btn relative px-5 py-3 text-sm font-medium border-b-2 transition-all duration-200 group
                {{ $tab === 'active'
                    ? 'border-emerald-500 text-emerald-700 bg-emerald-50/60'
                    : 'border-transparent text-gray-400 hover:text-gray-600 hover:border-gray-300 hover:bg-gray-50/50' }}">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 {{ $tab === 'active' ? 'text-emerald-500' : 'text-gray-400 group-hover:text-gray-500' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Active
                    <span class="ml-1 inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-[11px] font-bold
                        {{ $tab === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-200 text-gray-600' }}
                        transition-colors duration-200">{{ $activeCount }}</span>
                </span>
            </button>
            {{-- Inactive --}}
            <button role="tab" data-tab="inactive"
                class="tab-btn relative px-5 py-3 text-sm font-medium border-b-2 transition-all duration-200 group
                {{ $tab === 'inactive'
                    ? 'border-gray-400 text-gray-600 bg-gray-50/60'
                    : 'border-transparent text-gray-400 hover:text-gray-600 hover:border-gray-300 hover:bg-gray-50/50' }}">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 {{ $tab === 'inactive' ? 'text-gray-500' : 'text-gray-400 group-hover:text-gray-500' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 9l-6 6M9 9l6 6"/></svg>
                    Inactive
                    <span class="ml-1 inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-[11px] font-bold
                        {{ $tab === 'inactive' ? 'bg-gray-200 text-gray-700' : 'bg-gray-200 text-gray-600' }}
                        transition-colors duration-200">{{ $inactiveCount }}</span>
                </span>
            </button>
        </nav>
    </div>

    {{-- ===== TAB: ALL ===== --}}
    <div id="tab-all" class="tab-panel {{ $tab !== 'all' ? 'hidden' : '' }} active-panel">
        {{-- Stats --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-5">
            <div class="stat-card bg-white border border-gray-200 rounded-xl px-5 py-4 flex items-start gap-3.5 hover:shadow-md hover:border-indigo-200 transition-all duration-300">
                <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 border border-indigo-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">Total Employees</div>
                    <div class="text-2xl font-bold text-gray-900 tabular-nums mt-0.5">{{ $allCount }}</div>
                </div>
            </div>
            <div class="stat-card bg-white border border-gray-200 rounded-xl px-5 py-4 flex items-start gap-3.5 hover:shadow-md hover:border-emerald-200 transition-all duration-300">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">Active</div>
                    <div class="text-2xl font-bold text-gray-900 tabular-nums mt-0.5">{{ $activeCount }}</div>
                </div>
            </div>
            <div class="stat-card bg-white border border-gray-200 rounded-xl px-5 py-4 flex items-start gap-3.5 hover:shadow-md hover:border-gray-300 transition-all duration-300">
                <div class="w-10 h-10 rounded-lg bg-gray-100 text-gray-500 flex items-center justify-center shrink-0 border border-gray-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 9l-6 6M9 9l6 6"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">Inactive</div>
                    <div class="text-2xl font-bold text-gray-900 tabular-nums mt-0.5">{{ $inactiveCount }}</div>
                </div>
            </div>
            <div class="stat-card bg-white border border-gray-200 rounded-xl px-5 py-4 hover:shadow-md hover:border-violet-200 transition-all duration-300">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">Departments</div>
                    <div class="text-2xl font-bold text-gray-900 tabular-nums mt-0.5">{{ count($departments) }}</div>
                </div>
            </div>
        </div>

        {{-- Filter bar --}}
        <div class="filter-bar flex items-center gap-2.5 mb-4 flex-wrap">
            <div class="relative flex-1 min-w-[200px]">
                <input type="text" id="empSearch" placeholder="Search by name…"
                    class="w-full pl-4 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-white text-gray-900 placeholder-gray-400 outline-none transition-all duration-200 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200/50 hover:border-gray-300">
            </div>
            <select id="empStatusFilter"
                class="px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl bg-white text-gray-700 outline-none transition-all duration-200 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200/50 hover:border-gray-300 cursor-pointer">
                <option value="">All Statuses</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
            <select id="empDeptFilter"
                class="px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl bg-white text-gray-700 outline-none transition-all duration-200 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200/50 hover:border-gray-300 cursor-pointer">
                <option value="">All Departments</option>
                @foreach($departments as $dept)
                    <option value="{{ strtolower($dept) }}">{{ $dept }}</option>
                @endforeach
            </select>
        </div>

        {{-- Table --}}
        <div class="table-wrap bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition-shadow duration-300">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">
                                <a href="{{ route('employees.index', ['tab' => $tab, 'sort_by' => 'last_name', 'sort_order' => ($sortBy==='last_name'&&$sortOrder==='asc')?'desc':'asc']) }}"
                                   class="inline-flex items-center gap-1 hover:text-indigo-600 transition-colors">
                                    Employee
                                    @if($sortBy==='last_name')
                                        @if($sortOrder==='asc')
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
                                        @else
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                        @endif
                                    @else
                                        <svg class="w-3 h-3 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/></svg>
                                    @endif
                                </a>
                            </th>
                            <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 hidden md:table-cell">Gender</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Department</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 hidden sm:table-cell">Contact</th>
                            <th class="text-center px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        </tr>
                    </thead>
                    <tbody id="empTbody">
                        @forelse($employees as $employee)
                            <tr class="em-row-hover border-b border-gray-100 cursor-pointer transition-colors duration-150"
                                data-name="{{ strtolower(($employee->first_name??'').' '.($employee->last_name??'')) }}"
                                data-status="{{ $employee->status }}"
                                data-dept="{{ strtolower($employee->department??'') }}"
                                data-href="{{ route('employees.show', $employee) }}"
                                onclick="if(!event.target.closest('a,button,form'))window.location=this.dataset.href">
                                <td class="px-5 py-3.5">
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $employee->last_name }}, {{ $employee->first_name }}</div>
                                        <div class="text-xs text-gray-400 mt-0.5">{{ $employee->position ?? 'none' }}</div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-gray-500 text-sm hidden md:table-cell">{{ $employee->gender ? ucwords(str_replace('_',' ',$employee->gender)) : 'none' }}</td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">{{ $employee->department ?? 'none' }}</span>
                                </td>
                                <td class="px-5 py-3.5 font-mono text-sm text-gray-600 tabular-nums hidden sm:table-cell">{{ $employee->phone ?? 'none' }}</td>
                                <td class="px-5 py-3.5 text-center">
                                    @if($employee->status === 'active')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-500 border border-gray-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400 inline-block"></span>Inactive
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="100">
                                    <div class="flex flex-col items-center justify-center py-16 text-center">
                                        <p class="text-sm font-semibold text-gray-700">No employees found</p>
                                        <p class="text-xs text-gray-400 mt-1">Add your first employee to get started.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- No-filter-results --}}
            <div id="empNoResults" class="hidden">
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <p class="text-sm font-semibold text-gray-700">No results found</p>
                    <p class="text-xs text-gray-400 mt-1">Try a different search or filter.</p>
                </div>
            </div>

            {{-- Pagination strip --}}
            <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100 bg-gray-50/50 flex-wrap gap-3">
                <div class="text-xs text-gray-400" id="empPaginationInfo">Showing <strong class="text-gray-700">0</strong> employees</div>
                <nav id="empPaginationNav" class="flex items-center gap-1"></nav>
            </div>
        </div>
    </div>

    {{-- ===== TAB: ACTIVE ===== --}}
    <div id="tab-active" class="tab-panel {{ $tab !== 'active' ? 'hidden' : '' }} active-panel">
        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
            <div class="stat-card bg-white border border-gray-200 rounded-xl px-5 py-4 flex items-start gap-3.5 hover:shadow-md hover:border-emerald-200 transition-all duration-300">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">Total Active</div>
                    <div class="text-2xl font-bold text-gray-900 tabular-nums mt-0.5">{{ $activeCount }}</div>
                    <div class="text-xs text-gray-400 mt-1">Currently employed</div>
                </div>
            </div>
            <div class="stat-card bg-white border border-gray-200 rounded-xl px-5 py-4 flex items-start gap-3.5 hover:shadow-md hover:border-emerald-200 transition-all duration-300">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">Departments</div>
                    <div class="text-2xl font-bold text-gray-900 tabular-nums mt-0.5">{{ count($departments) }}</div>
                    <div class="text-xs text-gray-400 mt-1">With active staff</div>
                </div>
            </div>
            <div class="stat-card bg-white border border-gray-200 rounded-xl px-5 py-4 flex items-start gap-3.5 hover:shadow-md hover:border-emerald-200 transition-all duration-300">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">Hired This Month</div>
                    <div class="text-2xl font-bold text-gray-900 tabular-nums mt-0.5">{{ $activeCount }}</div>
                    <div class="text-xs text-gray-400 mt-1">Active employees</div>
                </div>
            </div>
        </div>

        {{-- Filter --}}
        <div class="filter-bar flex items-center gap-2.5 mb-4 flex-wrap">
            <div class="relative flex-1 min-w-[200px]">
                <input type="text" id="empSearchActive" placeholder="Search employee…"
                    class="w-full pl-4 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-white text-gray-900 placeholder-gray-400 outline-none transition-all duration-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-200/50 focus:shadow-md focus:shadow-emerald-100 hover:border-gray-300">
            </div>
            <select id="empDeptFilterActive"
                class="px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl bg-white text-gray-700 outline-none transition-all duration-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-200/50 focus:shadow-md focus:shadow-emerald-100 hover:border-gray-300 cursor-pointer">
                <option value="">All Departments</option>
                @foreach($departments as $dept)
                    <option value="{{ strtolower($dept) }}">{{ $dept }}</option>
                @endforeach
            </select>
        </div>

        {{-- Table --}}
        <div class="table-wrap bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition-shadow duration-300">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Employee</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 hidden md:table-cell">Gender</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Department</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 hidden sm:table-cell">Contact</th>
                            <th class="text-center px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        </tr>
                    </thead>
                    <tbody id="empTbodyActive"></tbody>
                </table>
            </div>
            <div id="empNoResultsActive" class="hidden">
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <p class="text-sm font-semibold text-gray-700">No results found</p>
                    <p class="text-xs text-gray-400 mt-1">Try a different search or filter.</p>
                </div>
            </div>
            <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100 bg-gray-50/50 flex-wrap gap-3">
                <div class="text-xs text-gray-400" id="empPaginationInfoActive">Showing <strong class="text-gray-700">0</strong> employees</div>
                <nav id="empPaginationNavActive" class="flex items-center gap-1"></nav>
            </div>
        </div>
    </div>

    {{-- ===== TAB: INACTIVE ===== --}}
    <div id="tab-inactive" class="tab-panel {{ $tab !== 'inactive' ? 'hidden' : '' }} active-panel">
        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
            <div class="stat-card bg-white border border-gray-200 rounded-xl px-5 py-4 flex items-start gap-3.5 hover:shadow-md hover:border-gray-300 transition-all duration-300">
                <div class="w-10 h-10 rounded-lg bg-gray-100 text-gray-500 flex items-center justify-center shrink-0 border border-gray-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 9l-6 6M9 9l6 6"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">Total Inactive</div>
                    <div class="text-2xl font-bold text-gray-900 tabular-nums mt-0.5">{{ $inactiveCount }}</div>
                    <div class="text-xs text-gray-400 mt-1">No longer employed</div>
                </div>
            </div>
            <div class="stat-card bg-white border border-gray-200 rounded-xl px-5 py-4 flex items-start gap-3.5 hover:shadow-md hover:border-gray-300 transition-all duration-300">
                <div class="w-10 h-10 rounded-lg bg-gray-100 text-gray-500 flex items-center justify-center shrink-0 border border-gray-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">Departments</div>
                    <div class="text-2xl font-bold text-gray-900 tabular-nums mt-0.5">{{ count($departments) }}</div>
                    <div class="text-xs text-gray-400 mt-1">Affected departments</div>
                </div>
            </div>
            <div class="stat-card bg-white border border-gray-200 rounded-xl px-5 py-4 flex items-start gap-3.5 hover:shadow-md hover:border-gray-300 transition-all duration-300">
                <div class="w-10 h-10 rounded-lg bg-gray-100 text-gray-500 flex items-center justify-center shrink-0 border border-gray-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">Inactive Rate</div>
                    <div class="text-2xl font-bold text-gray-900 tabular-nums mt-0.5">{{ $allCount > 0 ? round(($inactiveCount / $allCount) * 100) : 0 }}%</div>
                    <div class="text-xs text-gray-400 mt-1">Of total workforce</div>
                </div>
            </div>
        </div>

        {{-- Filter --}}
        <div class="filter-bar flex items-center gap-2.5 mb-4 flex-wrap">
            <div class="relative flex-1 min-w-[200px]">
                <input type="text" id="empSearchInactive" placeholder="Search employee…"
                    class="w-full pl-4 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-white text-gray-900 placeholder-gray-400 outline-none transition-all duration-200 focus:border-gray-400 focus:ring-2 focus:ring-gray-200/50 focus:shadow-md focus:shadow-gray-100 hover:border-gray-300">
            </div>
            <select id="empDeptFilterInactive"
                class="px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl bg-white text-gray-700 outline-none transition-all duration-200 focus:border-gray-400 focus:ring-2 focus:ring-gray-200/50 focus:shadow-md focus:shadow-gray-100 hover:border-gray-300 cursor-pointer">
                <option value="">All Departments</option>
                @foreach($departments as $dept)
                    <option value="{{ strtolower($dept) }}">{{ $dept }}</option>
                @endforeach
            </select>
        </div>

        {{-- Table --}}
        <div class="table-wrap bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition-shadow duration-300">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Employee</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 hidden md:table-cell">Gender</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Department</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 hidden sm:table-cell">Contact</th>
                            <th class="text-center px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        </tr>
                    </thead>
                    <tbody id="empTbodyInactive"></tbody>
                </table>
            </div>
            <div id="empNoResultsInactive" class="hidden">
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <p class="text-sm font-semibold text-gray-700">No results found</p>
                    <p class="text-xs text-gray-400 mt-1">Try a different search or filter.</p>
                </div>
            </div>
            <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100 bg-gray-50/50 flex-wrap gap-3">
                <div class="text-xs text-gray-400" id="empPaginationInfoInactive">Showing <strong class="text-gray-700">0</strong> employees</div>
                <nav id="empPaginationNavInactive" class="flex items-center gap-1"></nav>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.allEmployeesData = {!! json_encode($allEmployees->map(fn($e) => [
    'id'         => $e->id,
    'firstName'  => $e->first_name,
    'lastName'   => $e->last_name,
    'name'       => strtolower(($e->first_name??'').' '.($e->last_name??'')),
    'gender'     => $e->gender,
    'position'   => $e->position,
    'department' => strtolower($e->department??''),
    'departmentDisplay' => $e->department,
    'phone'      => $e->phone,
    'status'     => $e->status,
])) !!};

(function(){
    var activeTab = '{{ $tab }}';

    var tabBtns = document.querySelectorAll('.tab-btn');
    var panels  = document.querySelectorAll('.tab-panel');

    var tabStyles = {
        all:      { border: 'border-indigo-600',  text: 'text-indigo-700',  bg: 'bg-indigo-50/60',  icon: 'text-indigo-600',  badge: { bg: 'bg-indigo-100',      text: 'text-indigo-800' } },
        active:   { border: 'border-emerald-500', text: 'text-emerald-700', bg: 'bg-emerald-50/60', icon: 'text-emerald-500', badge: { bg: 'bg-emerald-100',    text: 'text-emerald-800' } },
        inactive: { border: 'border-gray-400',    text: 'text-gray-600',    bg: 'bg-gray-50/60',    icon: 'text-gray-500',   badge: { bg: 'bg-gray-200',       text: 'text-gray-700' } },
    };

    var inactiveBtn = ['border-transparent', 'text-gray-400', 'hover:text-gray-600', 'hover:border-gray-300', 'hover:bg-gray-50/50'];
    var inactiveSvg = ['text-gray-400', 'group-hover:text-gray-500'];
    var inactiveBadge = ['bg-gray-200', 'text-gray-600'];

    tabBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            var tab = this.dataset.tab;
            if (tab === activeTab) return;

            var prev = document.getElementById('tab-' + activeTab);
            if (prev) { prev.classList.add('hidden'); prev.classList.remove('active-panel'); }

            var next = document.getElementById('tab-' + tab);
            if (next) { next.classList.remove('hidden'); next.classList.add('active-panel'); }

            tabBtns.forEach(function(b) {
                b.classList.remove('border-indigo-600','text-indigo-700','bg-indigo-50/60',
                    'border-emerald-500','text-emerald-700','bg-emerald-50/60',
                    'border-gray-400','text-gray-600','bg-gray-50/60');
                b.classList.add.apply(b.classList, inactiveBtn);

                var svg = b.querySelector('svg');
                if (svg) {
                    svg.classList.remove('text-indigo-600','text-emerald-500','text-gray-500');
                    svg.classList.add.apply(svg.classList, inactiveSvg);
                }

                var badge = b.querySelector('[class*="ml-1"]');
                if (badge) {
                    badge.classList.remove('bg-indigo-100','text-indigo-800','bg-emerald-100','text-emerald-800','bg-gray-200','text-gray-700');
                    badge.classList.add.apply(badge.classList, inactiveBadge);
                }
            });

            var s = tabStyles[tab];
            this.classList.remove.apply(this.classList, inactiveBtn);
            this.classList.add(s.border, s.text, s.bg);

            var svg = this.querySelector('svg');
            if (svg) {
                svg.classList.remove.apply(svg.classList, inactiveSvg);
                svg.classList.add(s.icon);
            }

            var badge = this.querySelector('[class*="ml-1"]');
            if (badge && s.badge.bg) {
                badge.classList.remove.apply(badge.classList, inactiveBadge);
                badge.classList.add(s.badge.bg, s.badge.text);
            }

            var url = new URL(window.location);
            url.searchParams.set('tab', tab);
            url.searchParams.delete('page');
            history.replaceState(null, '', url.toString());

            activeTab = tab;

            if (['active','inactive'].indexOf(tab) !== -1) initTable(tab);
        });
    });

    // === Table initializer for active/inactive tabs ===
    var initialized = {};

    function initTable(tab) {
        if (initialized[tab]) return;
        initialized[tab] = true;

        var data = window.allEmployeesData.filter(function(e) { return e.status === tab; });
        var PER = 10;
        var page = 1, filtered = [];

        var search = document.getElementById('empSearch' + (tab === 'active' ? 'Active' : 'Inactive'));
        var deptF  = document.getElementById('empDeptFilter' + (tab === 'active' ? 'Active' : 'Inactive'));

        function applyFilters() {
            var q = search ? search.value.toLowerCase().trim() : '';
            var dt = deptF ? deptF.value : '';
            filtered = data.filter(function(r) {
                if (q && !r.name.includes(q)) return false;
                if (dt && r.department !== dt) return false;
                return true;
            });
            page = 1;
            render();
        }

        function render() {
            var tbody = document.getElementById('empTbody' + (tab === 'active' ? 'Active' : 'Inactive'));
            var noRes = document.getElementById('empNoResults' + (tab === 'active' ? 'Active' : 'Inactive'));
            if (!tbody) return;

            var start = (page - 1) * PER;
            var end = Math.min(start + PER, filtered.length);
            var pageData = filtered.slice(start, end);
            tbody.innerHTML = '';

            if (pageData.length === 0) {
                if (noRes) noRes.classList.remove('hidden');
            } else {
                if (noRes) noRes.classList.add('hidden');
                var empRoute = '{{ route("employees.show", ["employee"=>"__ID__"]) }}';
                pageData.forEach(function(r) {
                    var href = empRoute.replace('__ID__', r.id);
                    var dept = r.departmentDisplay || 'none';
                    var gender = r.gender ? r.gender.replace('_',' ').replace(/\b\w/g,function(c){return c.toUpperCase();}) : 'none';
                    var statusBadge = r.status === 'active'
                        ? '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>Active</span>'
                        : '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-500 border border-gray-200"><span class="w-1.5 h-1.5 rounded-full bg-gray-400 inline-block"></span>Inactive</span>';

                    var tr = document.createElement('tr');
                    tr.className = 'em-row-hover border-b border-gray-100 cursor-pointer transition-colors duration-150';
                    tr.dataset.name = r.name;
                    tr.dataset.status = r.status;
                    tr.dataset.dept = r.department;
                    tr.dataset.href = href;
                    tr.onclick = function(e) { if(!e.target.closest('a,button,form')) window.location = href; };
                    tr.innerHTML = [
                        '<td class="px-5 py-3.5"><div><div class="font-semibold text-gray-900">' + r.lastName + ', ' + r.firstName + '</div><div class="text-xs text-gray-400 mt-0.5">' + (r.position||'none') + '</div></div></td>',
                        '<td class="px-5 py-3.5 text-gray-500 text-sm">' + gender + '</td>',
                        '<td class="px-5 py-3.5"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">' + dept + '</span></td>',
                        '<td class="px-5 py-3.5 font-mono text-sm text-gray-600 tabular-nums">' + (r.phone||'none') + '</td>',
                        '<td class="px-5 py-3.5 text-center">' + statusBadge + '</td>'
                    ].join('');
                    tbody.appendChild(tr);
                });
            }
            updatePagination();
        }

        function updatePagination() {
            var total = filtered.length;
            var pages = Math.ceil(total / PER);
            var suffix = tab === 'active' ? 'Active' : 'Inactive';
            var info  = document.getElementById('empPaginationInfo' + suffix);
            var nav   = document.getElementById('empPaginationNav' + suffix);
            if (!info || !nav) return;
            if (total === 0) { info.innerHTML = 'No employees to display'; nav.innerHTML = ''; return; }
            var s = (page - 1) * PER + 1, e = Math.min(page * PER, total);
            info.innerHTML = 'Showing <strong class="text-gray-700">' + s + '</strong>&ndash;<strong class="text-gray-700">' + e + '</strong> of <strong class="text-gray-700">' + total + '</strong>';
            if (pages <= 1) { nav.innerHTML = ''; return; }

            var base = 'flex items-center justify-center w-7 h-7 rounded-lg text-xs font-semibold border transition-all duration-150';
            var act  = base + ' bg-gray-900 text-white border-gray-900';
            var def  = base + ' bg-white text-gray-600 border-gray-200 hover:bg-gray-50 hover:text-gray-900 hover:border-gray-300';
            var dis  = base + ' bg-gray-50 text-gray-300 border-gray-100 cursor-not-allowed pointer-events-none';

            var h = '';
            h += '<button data-p="' + (page - 1) + '" class="' + (page === 1 ? dis : def) + '">‹</button>';
            var half = 2;
            var winStart = Math.max(1, page - half);
            var winEnd = Math.min(pages, winStart + 4);
            if (winEnd - winStart + 1 < 5) { winStart = Math.max(winEnd - 4, 1); }
            for (var i = winStart; i <= winEnd; i++) {
                h += '<button data-p="' + i + '" class="' + (i === page ? act : def) + '">' + i + '</button>';
            }
            h += '<button data-p="' + (page + 1) + '" class="' + (page === pages ? dis : def) + '">›</button>';
            nav.innerHTML = h;
            nav.querySelectorAll('button[data-p]').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    var p = parseInt(btn.dataset.p);
                    if (p < 1 || p > pages) return;
                    page = p;
                    render();
                });
            });
        }

        if (search) search.addEventListener('input', applyFilters);
        if (deptF) deptF.addEventListener('change', applyFilters);
        applyFilters();
    }

    // Initialize main "all" tab
    (function(){
        const search  = document.getElementById('empSearch');
        const statusF = document.getElementById('empStatusFilter');
        const deptF   = document.getElementById('empDeptFilter');
        const tbody   = document.getElementById('empTbody');
        const noRes   = document.getElementById('empNoResults');
        const PER     = 10;
        let page = 1, filtered = [];

        const STATUS_BADGE = {
            active:   '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>Active</span>',
            inactive: '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-500 border border-gray-200"><span class="w-1.5 h-1.5 rounded-full bg-gray-400 inline-block"></span>Inactive</span>',
        };

        function applyFilters(){
            const q  = search.value.toLowerCase().trim();
            const st = statusF.value;
            const dt = deptF.value;
            filtered = window.allEmployeesData.filter(e =>
                (!q  || e.name.includes(q)) &&
                (!st || e.status === st) &&
                (!dt || e.department === dt)
            );
            page = 1;
            render();
        }

        function render(){
            const start   = (page-1)*PER;
            const pageData= filtered.slice(start, start+PER);
            tbody.innerHTML = '';

            if(!pageData.length){
                if(filtered.length === 0 && window.allEmployeesData.length > 0){
                    noRes.classList.remove('hidden');
                } else if(window.allEmployeesData.length === 0){
                    tbody.innerHTML = '<tr><td colspan="100"><div class="flex flex-col items-center justify-center py-16 text-center"><p class="text-sm font-semibold text-gray-700">No employees found</p><p class="text-xs text-gray-400 mt-1">Add your first employee to get started.</p></div></td></tr>';
                }
            } else {
                noRes.classList.add('hidden');
                const empRoute = '{{ route("employees.show", ["employee"=>"__ID__"]) }}';
                pageData.forEach(emp => {
                    const href = empRoute.replace('__ID__', emp.id);
                    const dept = emp.departmentDisplay ? emp.departmentDisplay : 'none';
                    const gender = emp.gender ? emp.gender.replace('_',' ').replace(/\b\w/g,c=>c.toUpperCase()) : 'none';
                    const row = document.createElement('tr');
                    row.className = 'em-row-hover border-b border-gray-100 cursor-pointer transition-colors duration-150';
                    row.dataset.name   = emp.name;
                    row.dataset.status = emp.status;
                    row.dataset.dept   = emp.department;
                    row.dataset.href   = href;
                    row.onclick = e => { if(!e.target.closest('a,button,form')) window.location = href; };
                    row.innerHTML = [
                        '<td class="px-5 py-3.5"><div><div class="font-semibold text-gray-900">' + emp.lastName + ', ' + emp.firstName + '</div><div class="text-xs text-gray-400 mt-0.5">' + (emp.position||'none') + '</div></div></td>',
                        '<td class="px-5 py-3.5 text-gray-500 text-sm">' + gender + '</td>',
                        '<td class="px-5 py-3.5"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">' + dept + '</span></td>',
                        '<td class="px-5 py-3.5 font-mono text-sm text-gray-600 tabular-nums">' + (emp.phone||'none') + '</td>',
                        '<td class="px-5 py-3.5 text-center">' + (STATUS_BADGE[emp.status]||STATUS_BADGE.inactive) + '</td>'
                    ].join('');
                    tbody.appendChild(row);
                });
            }
            updatePagination();
        }

        function updatePagination(){
            const total = filtered.length;
            const pages = Math.ceil(total/PER);
            const info  = document.getElementById('empPaginationInfo');
            const nav   = document.getElementById('empPaginationNav');
            if(!info||!nav) return;
            if(total === 0){ info.innerHTML='No employees to display'; nav.innerHTML=''; return; }
            const s = (page-1)*PER+1, e = Math.min(page*PER, total);
            info.innerHTML = 'Showing <strong class="text-gray-700">' + s + '</strong>&ndash;<strong class="text-gray-700">' + e + '</strong> of <strong class="text-gray-700">' + total + '</strong>';
            if(pages<=1){ nav.innerHTML=''; return; }

            const btnClass = 'flex items-center justify-center w-7 h-7 rounded-lg text-xs font-semibold border transition-all duration-150';
            const activeClass = btnClass + ' bg-gray-900 text-white border-gray-900';
            const defClass    = btnClass + ' bg-white text-gray-600 border-gray-200 hover:bg-gray-50 hover:text-gray-900 hover:border-gray-300';
            const disClass    = btnClass + ' bg-gray-50 text-gray-300 border-gray-100 cursor-not-allowed pointer-events-none';

            let html = '';
            html += '<button data-p="' + (page-1) + '" class="' + (page===1?disClass:defClass) + '">‹</button>';
            const half = 2;
            let winStart = Math.max(1, page - half);
            let winEnd = Math.min(pages, winStart + 4);
            if (winEnd - winStart + 1 < 5) {
                winStart = Math.max(winEnd - 4, 1);
            }
            for(let i=winStart;i<=winEnd;i++){
                html += '<button data-p="' + i + '" class="' + (i===page?activeClass:defClass) + '">' + i + '</button>';
            }
            html += '<button data-p="' + (page+1) + '" class="' + (page===pages?disClass:defClass) + '">›</button>';
            nav.innerHTML = html;
            nav.querySelectorAll('button[data-p]').forEach(btn => {
                btn.addEventListener('click', e => {
                    e.preventDefault();
                    const p = parseInt(btn.dataset.p);
                    if(p<1||p>pages) return;
                    page = p; render();
                });
            });
        }

        search.addEventListener('input', applyFilters);
        statusF.addEventListener('change', applyFilters);
        deptF.addEventListener('change', applyFilters);
        applyFilters();
    })();

    // Initialize active/inactive tabs
    ['active','inactive'].forEach(initTable);
})();
</script>
@endpush
