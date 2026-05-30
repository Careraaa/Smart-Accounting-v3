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
@keyframes pulseGlow {
    0%, 100% { box-shadow: 0 0 0 0 rgba(251, 191, 36, 0.4); }
    50% { box-shadow: 0 0 0 6px rgba(251, 191, 36, 0); }
}
.tab-panel.active-panel { animation: fadeSlideUp 0.45s cubic-bezier(0.16, 1, 0.3, 1) both; }
.stat-card { animation: scaleIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) both; }
.stat-card:nth-child(1) { animation-delay: 0.05s; }
.stat-card:nth-child(2) { animation-delay: 0.1s; }
.stat-card:nth-child(3) { animation-delay: 0.15s; }
.table-wrap { animation: fadeSlideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) 0.15s both; }
.filter-bar { animation: slideInRight 0.4s cubic-bezier(0.16, 1, 0.3, 1) 0.1s both; }

/* Neutralise global overrides.scss red focus-visible — leave mgmt uses its own focus rings */
[data-lm] input:focus-visible,
[data-lm] select:focus-visible,
[data-lm] button:focus-visible,
[data-lm] a:focus-visible {
    outline: none !important;
}
</style>
@endpush

@section('content')
<div class="max-w-full" data-lm>

    {{-- Flash --}}
    @if(session('success'))
    <div class="flex items-center gap-2.5 px-4 py-3 mb-5 rounded-xl text-sm font-medium bg-emerald-50 border border-emerald-200 text-emerald-700" style="animation:fadeSlideUp 0.35s ease both;">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-center gap-2.5 px-4 py-3 mb-5 rounded-xl text-sm font-medium bg-red-50 border border-red-200 text-red-700" style="animation:fadeSlideUp 0.35s ease both;">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- Header --}}
    <div class="flex items-start justify-between mb-6 flex-wrap gap-4" style="animation:fadeSlideUp 0.4s cubic-bezier(0.16,1,0.3,1) both;">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Leave Management</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage leave types, requests, and approvals</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('leave.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded-xl hover:bg-gray-800 hover:shadow-lg hover:shadow-gray-900/20 active:scale-[0.97] transition-all duration-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                New Leave
            </a>
        </div>
    </div>

    {{-- Color-Coded Tabs --}}
    <div class="border-b border-gray-200 mb-6" style="animation:fadeSlideUp 0.4s cubic-bezier(0.16,1,0.3,1) 0.05s both;">
        <nav class="flex gap-1 -mb-px" role="tablist">
            {{-- Types --}}
            <button role="tab" data-tab="types"
                class="tab-btn relative px-5 py-3 text-sm font-medium border-b-2 transition-all duration-200 group
                {{ $tab === 'types'
                    ? 'border-violet-600 text-violet-700 bg-violet-50/60'
                    : 'border-transparent text-gray-400 hover:text-gray-600 hover:border-gray-300 hover:bg-gray-50/50' }}">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 {{ $tab === 'types' ? 'text-violet-600' : 'text-gray-400 group-hover:text-gray-500' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Leave Types
                </span>
            </button>
            {{-- Pending --}}
            <button role="tab" data-tab="pending"
                class="tab-btn relative px-5 py-3 text-sm font-medium border-b-2 transition-all duration-200 group
                {{ $tab === 'pending'
                    ? 'border-amber-500 text-amber-700 bg-amber-50/60'
                    : 'border-transparent text-gray-400 hover:text-gray-600 hover:border-gray-300 hover:bg-gray-50/50' }}">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 {{ $tab === 'pending' ? 'text-amber-500' : 'text-gray-400 group-hover:text-gray-500' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" d="M12 8v4m0 4h.01"/></svg>
                    Pending
                    @if($pendingCount > 0)
                    <span class="ml-1 inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-[11px] font-bold
                        {{ $tab === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-gray-200 text-gray-600' }}
                        transition-colors duration-200">{{ $pendingCount }}</span>
                    @endif
                </span>
            </button>
            {{-- Approved --}}
            <button role="tab" data-tab="approved"
                class="tab-btn relative px-5 py-3 text-sm font-medium border-b-2 transition-all duration-200 group
                {{ $tab === 'approved'
                    ? 'border-emerald-500 text-emerald-700 bg-emerald-50/60'
                    : 'border-transparent text-gray-400 hover:text-gray-600 hover:border-gray-300 hover:bg-gray-50/50' }}">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 {{ $tab === 'approved' ? 'text-emerald-500' : 'text-gray-400 group-hover:text-gray-500' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Approved
                    @if($approvedCount > 0)
                    <span class="ml-1 inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-[11px] font-bold
                        {{ $tab === 'approved' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-200 text-gray-600' }}
                        transition-colors duration-200">{{ $approvedCount }}</span>
                    @endif
                </span>
            </button>
            {{-- Rejected --}}
            <button role="tab" data-tab="rejected"
                class="tab-btn relative px-5 py-3 text-sm font-medium border-b-2 transition-all duration-200 group
                {{ $tab === 'rejected'
                    ? 'border-red-500 text-red-700 bg-red-50/60'
                    : 'border-transparent text-gray-400 hover:text-gray-600 hover:border-gray-300 hover:bg-gray-50/50' }}">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 {{ $tab === 'rejected' ? 'text-red-500' : 'text-gray-400 group-hover:text-gray-500' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 9l-6 6M9 9l6 6"/></svg>
                    Rejected
                </span>
            </button>
        </nav>
    </div>

    {{-- ===== TAB: LEAVE TYPES ===== --}}
    <div id="tab-types" class="tab-panel {{ $tab !== 'types' ? 'hidden' : '' }} active-panel">
        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
            <div class="stat-card bg-white border border-gray-200 rounded-xl px-5 py-4 flex items-start gap-3.5 hover:shadow-md hover:border-violet-200 transition-all duration-300">
                <div class="w-10 h-10 rounded-lg bg-violet-50 text-violet-600 flex items-center justify-center shrink-0 border border-violet-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">Total Types</div>
                    <div class="text-2xl font-bold text-gray-900 tabular-nums mt-0.5">{{ $totalTypes }}</div>
                </div>
            </div>
            <div class="stat-card bg-white border border-gray-200 rounded-xl px-5 py-4 flex items-start gap-3.5 hover:shadow-md hover:border-emerald-200 transition-all duration-300">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">Active</div>
                    <div class="text-2xl font-bold text-gray-900 tabular-nums mt-0.5">{{ $activeTypes }}</div>
                </div>
            </div>
            <div class="stat-card bg-white border border-gray-200 rounded-xl px-5 py-4 flex items-start gap-3.5 hover:shadow-md hover:border-gray-300 transition-all duration-300">
                <div class="w-10 h-10 rounded-lg bg-gray-100 text-gray-500 flex items-center justify-center shrink-0 border border-gray-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">Inactive</div>
                    <div class="text-2xl font-bold text-gray-900 tabular-nums mt-0.5">{{ $inactiveTypes }}</div>
                </div>
            </div>
        </div>

        {{-- Add button --}}
        <div class="flex items-center justify-between mb-4 filter-bar">
            <div></div>
            <a href="{{ route('leave-type.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-violet-600 text-white text-sm font-semibold rounded-xl hover:bg-violet-700 hover:shadow-lg hover:shadow-violet-600/25 active:scale-[0.97] transition-all duration-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Add Leave Type
            </a>
        </div>

        {{-- Table --}}
        <div class="table-wrap bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition-shadow duration-300">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Leave Type</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Days Allowed</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Carry Over</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaveTypes as $leaveType)
                        <tr class="border-b border-gray-100 hover:bg-violet-50/30 hover:cursor-pointer transition-colors duration-150" onclick="window.location='{{ route('leave-type.show', $leaveType) }}'">
                            <td class="px-4 py-3.5">
                                <div class="font-semibold text-gray-900">{{ $leaveType->name }}</div>
                                @if($leaveType->abbreviation)
                                    <div class="text-xs text-gray-400 mt-0.5 font-mono">{{ $leaveType->abbreviation }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-violet-50 text-violet-700 border border-violet-200">{{ $leaveType->days_allowed }} days</span>
                            </td>
                            <td class="px-4 py-3.5">
                                @if($leaveType->carry_over)
                                    <span class="inline-flex items-center gap-1 text-sm font-medium text-emerald-600">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        Yes
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-sm font-medium text-gray-400">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        No
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="text-gray-500 text-xs">{{ Str::limit($leaveType->description, 60) }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4">
                                <div class="flex flex-col items-center justify-center py-16 text-center">
                                    <div class="w-14 h-14 rounded-xl bg-gray-100 flex items-center justify-center text-gray-300 mb-4">
                                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-700">No leave types found</p>
                                    <p class="text-xs text-gray-400 mt-1">Add a leave type to get started.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ===== TAB: PENDING ===== --}}
    <div id="tab-pending" class="tab-panel {{ $tab !== 'pending' ? 'hidden' : '' }} active-panel">
        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
            <div class="stat-card bg-white border border-gray-200 rounded-xl px-5 py-4 flex items-start gap-3.5 hover:shadow-md hover:border-amber-200 transition-all duration-300">
                <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 border border-amber-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" d="M12 8v4m0 4h.01"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">Total Pending</div>
                    <div class="text-2xl font-bold text-gray-900 tabular-nums mt-0.5">{{ $pendingCount }}</div>
                    <div class="text-xs text-gray-400 mt-1">Awaiting review</div>
                </div>
            </div>
            <div class="stat-card bg-white border border-gray-200 rounded-xl px-5 py-4 flex items-start gap-3.5 hover:shadow-md hover:border-amber-200 transition-all duration-300">
                <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 border border-amber-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">This Week</div>
                    <div class="text-2xl font-bold text-gray-900 tabular-nums mt-0.5">{{ $pendingWeekLeaves }}</div>
                    <div class="text-xs text-gray-400 mt-1">Pending requests</div>
                </div>
            </div>
            <div class="stat-card bg-white border border-gray-200 rounded-xl px-5 py-4 flex items-start gap-3.5 hover:shadow-md hover:border-amber-200 transition-all duration-300">
                <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 border border-amber-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">This Month</div>
                    <div class="text-2xl font-bold text-gray-900 tabular-nums mt-0.5">{{ $pendingMonthLeaves }}</div>
                    <div class="text-xs text-gray-400 mt-1">Total requests</div>
                </div>
            </div>
        </div>

        {{-- Filter --}}
        <div class="filter-bar flex items-center gap-2.5 mb-4 flex-wrap">
            <div class="relative flex-1 min-w-[200px]">
                <input type="text" id="lvSearch" placeholder="Search employee…"
                    class="w-full pl-4 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-white text-gray-900 placeholder-gray-400 outline-none transition-all duration-200 focus:border-amber-400 focus:ring-2 focus:ring-amber-200/50 focus:shadow-md focus:shadow-amber-100 hover:border-gray-300">
            </div>
            <select id="lvDeptFilter"
                class="px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl bg-white text-gray-700 outline-none transition-all duration-200 focus:border-amber-400 focus:ring-2 focus:ring-amber-200/50 focus:shadow-md focus:shadow-amber-100 hover:border-gray-300 cursor-pointer">
                <option value="">All Departments</option>
                @foreach($departments as $dept)
                    <option value="{{ strtolower($dept) }}">{{ $dept }}</option>
                @endforeach
            </select>
            <select id="lvTypeFilter"
                class="px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl bg-white text-gray-700 outline-none transition-all duration-200 focus:border-amber-400 focus:ring-2 focus:ring-amber-200/50 focus:shadow-md focus:shadow-amber-100 hover:border-gray-300 cursor-pointer">
                <option value="">All Leave Types</option>
                @foreach($leaveTypeNames as $lt)
                    <option value="{{ strtolower($lt) }}">{{ $lt }}</option>
                @endforeach
            </select>
        </div>

        {{-- Table --}}
        <div class="table-wrap">
            @include('hr.leave._partials.leave-table', [
                'leaves' => $pendingLeaves,
                'columns' => ['employee', 'department', 'leave_type', 'dates', 'days', 'applied'],
                'status' => 'pending',
                'emptyTitle' => 'No pending leave requests',
                'emptySub' => 'All caught up — nothing to review.',
            ])
        </div>
    </div>

    {{-- ===== TAB: APPROVED ===== --}}
    <div id="tab-approved" class="tab-panel {{ $tab !== 'approved' ? 'hidden' : '' }} active-panel">
        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
            <div class="stat-card bg-white border border-gray-200 rounded-xl px-5 py-4 flex items-start gap-3.5 hover:shadow-md hover:border-emerald-200 transition-all duration-300">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">Total Approved</div>
                    <div class="text-2xl font-bold text-gray-900 tabular-nums mt-0.5">{{ $approvedCount }}</div>
                    <div class="text-xs text-gray-400 mt-1">Approved leaves</div>
                </div>
            </div>
            <div class="stat-card bg-white border border-gray-200 rounded-xl px-5 py-4 flex items-start gap-3.5 hover:shadow-md hover:border-emerald-200 transition-all duration-300">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">This Week</div>
                    <div class="text-2xl font-bold text-gray-900 tabular-nums mt-0.5">{{ $approvedWeekLeaves }}</div>
                    <div class="text-xs text-gray-400 mt-1">Approved this week</div>
                </div>
            </div>
            <div class="stat-card bg-white border border-gray-200 rounded-xl px-5 py-4 flex items-start gap-3.5 hover:shadow-md hover:border-emerald-200 transition-all duration-300">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">This Month</div>
                    <div class="text-2xl font-bold text-gray-900 tabular-nums mt-0.5">{{ $approvedMonthLeaves }}</div>
                    <div class="text-xs text-gray-400 mt-1">Approved this month</div>
                </div>
            </div>
        </div>

        {{-- Filter --}}
        <div class="filter-bar flex items-center gap-2.5 mb-4 flex-wrap">
            <div class="relative flex-1 min-w-[200px]">
                <input type="text" id="lvSearch" placeholder="Search employee…"
                    class="w-full pl-4 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-white text-gray-900 placeholder-gray-400 outline-none transition-all duration-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-200/50 focus:shadow-md focus:shadow-emerald-100 hover:border-gray-300">
            </div>
            <select id="lvDeptFilter"
                class="px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl bg-white text-gray-700 outline-none transition-all duration-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-200/50 focus:shadow-md focus:shadow-emerald-100 hover:border-gray-300 cursor-pointer">
                <option value="">All Departments</option>
                @foreach($departments as $dept)
                    <option value="{{ strtolower($dept) }}">{{ $dept }}</option>
                @endforeach
            </select>
            <select id="lvTypeFilter"
                class="px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl bg-white text-gray-700 outline-none transition-all duration-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-200/50 focus:shadow-md focus:shadow-emerald-100 hover:border-gray-300 cursor-pointer">
                <option value="">All Leave Types</option>
                @foreach($leaveTypeNames as $lt)
                    <option value="{{ strtolower($lt) }}">{{ $lt }}</option>
                @endforeach
            </select>
            <a href="{{ route('hr.reports.print.approved-leaves-report') }}" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-xl hover:border-emerald-400 hover:text-emerald-600 hover:bg-emerald-50/50 hover:shadow-sm transition-all duration-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print
            </a>
        </div>

        {{-- Table --}}
        <div class="table-wrap">
            @include('hr.leave._partials.leave-table', [
                'leaves' => $approvedLeaves,
                'columns' => ['employee', 'department', 'leave_type', 'dates', 'days', 'pay_status', 'approved_date'],
                'status' => 'approved',
                'emptyTitle' => 'No approved leave requests',
                'emptySub' => 'Nothing to show here yet.',
            ])
        </div>
    </div>

    {{-- ===== TAB: REJECTED ===== --}}
    <div id="tab-rejected" class="tab-panel {{ $tab !== 'rejected' ? 'hidden' : '' }} active-panel">
        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
            <div class="stat-card bg-white border border-gray-200 rounded-xl px-5 py-4 flex items-start gap-3.5 hover:shadow-md hover:border-red-200 transition-all duration-300">
                <div class="w-10 h-10 rounded-lg bg-red-50 text-red-600 flex items-center justify-center shrink-0 border border-red-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 9l-6 6M9 9l6 6"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">Total Rejected</div>
                    <div class="text-2xl font-bold text-gray-900 tabular-nums mt-0.5">{{ $rejectedCount }}</div>
                    <div class="text-xs text-gray-400 mt-1">Rejected leaves</div>
                </div>
            </div>
            <div class="stat-card bg-white border border-gray-200 rounded-xl px-5 py-4 flex items-start gap-3.5 hover:shadow-md hover:border-red-200 transition-all duration-300">
                <div class="w-10 h-10 rounded-lg bg-red-50 text-red-600 flex items-center justify-center shrink-0 border border-red-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">This Week</div>
                    <div class="text-2xl font-bold text-gray-900 tabular-nums mt-0.5">{{ $rejectedWeekLeaves }}</div>
                    <div class="text-xs text-gray-400 mt-1">Rejected this week</div>
                </div>
            </div>
            <div class="stat-card bg-white border border-gray-200 rounded-xl px-5 py-4 flex items-start gap-3.5 hover:shadow-md hover:border-red-200 transition-all duration-300">
                <div class="w-10 h-10 rounded-lg bg-red-50 text-red-600 flex items-center justify-center shrink-0 border border-red-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">This Month</div>
                    <div class="text-2xl font-bold text-gray-900 tabular-nums mt-0.5">{{ $rejectedMonthLeaves }}</div>
                    <div class="text-xs text-gray-400 mt-1">Rejected this month</div>
                </div>
            </div>
        </div>

        {{-- Filter --}}
        <div class="filter-bar flex items-center gap-2.5 mb-4 flex-wrap">
            <div class="relative flex-1 min-w-[200px]">
                <input type="text" id="lvSearch" placeholder="Search employee…"
                    class="w-full pl-4 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-white text-gray-900 placeholder-gray-400 outline-none transition-all duration-200 focus:border-red-400 focus:ring-2 focus:ring-red-200/50 focus:shadow-md focus:shadow-red-100 hover:border-gray-300">
            </div>
            <select id="lvDeptFilter"
                class="px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl bg-white text-gray-700 outline-none transition-all duration-200 focus:border-red-400 focus:ring-2 focus:ring-red-200/50 focus:shadow-md focus:shadow-red-100 hover:border-gray-300 cursor-pointer">
                <option value="">All Departments</option>
                @foreach($departments as $dept)
                    <option value="{{ strtolower($dept) }}">{{ $dept }}</option>
                @endforeach
            </select>
            <select id="lvTypeFilter"
                class="px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl bg-white text-gray-700 outline-none transition-all duration-200 focus:border-red-400 focus:ring-2 focus:ring-red-200/50 focus:shadow-md focus:shadow-red-100 hover:border-gray-300 cursor-pointer">
                <option value="">All Leave Types</option>
                @foreach($leaveTypeNames as $lt)
                    <option value="{{ strtolower($lt) }}">{{ $lt }}</option>
                @endforeach
            </select>
        </div>

        {{-- Table --}}
        <div class="table-wrap">
            @include('hr.leave._partials.leave-table', [
                'leaves' => $rejectedLeaves,
                'columns' => ['employee', 'department', 'leave_type', 'dates', 'days', 'rejected_date'],
                'status' => 'rejected',
                'emptyTitle' => 'No rejected leave requests',
                'emptySub' => 'Nothing to show here yet.',
            ])
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
    var activeTab = '{{ $tab }}';

    // Tab switching — client-side, no page reload
    var tabBtns = document.querySelectorAll('.tab-btn');
    var panels  = document.querySelectorAll('.tab-panel');

    var tabStyles = {
        types:    { border: 'border-violet-600',  text: 'text-violet-700',  bg: 'bg-violet-50/60',  icon: 'text-violet-600',  badge: { bg: '',                  text: '' } },
        pending:  { border: 'border-amber-500',   text: 'text-amber-700',  bg: 'bg-amber-50/60',  icon: 'text-amber-500',  badge: { bg: 'bg-amber-100',      text: 'text-amber-800' } },
        approved: { border: 'border-emerald-500', text: 'text-emerald-700',bg: 'bg-emerald-50/60', icon: 'text-emerald-500', badge: { bg: 'bg-emerald-100',    text: 'text-emerald-800' } },
        rejected: { border: 'border-red-500',     text: 'text-red-700',    bg: 'bg-red-50/60',    icon: 'text-red-500',    badge: { bg: '',                  text: '' } }
    };

    var inactiveBtn = ['border-transparent', 'text-gray-400', 'hover:text-gray-600', 'hover:border-gray-300', 'hover:bg-gray-50/50'];
    var inactiveSvg = ['text-gray-400', 'group-hover:text-gray-500'];
    var inactiveBadge = ['bg-gray-200', 'text-gray-600'];

    tabBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            var tab = this.dataset.tab;
            if (tab === activeTab) return;

            // Hide previous panel
            var prev = document.getElementById('tab-' + activeTab);
            if (prev) { prev.classList.add('hidden'); prev.classList.remove('active-panel'); }

            // Show target panel
            var next = document.getElementById('tab-' + tab);
            if (next) { next.classList.remove('hidden'); next.classList.add('active-panel'); }

            // Deactivate all buttons
            tabBtns.forEach(function(b) {
                b.classList.remove('border-violet-600','text-violet-700','bg-violet-50/60',
                    'border-amber-500','text-amber-700','bg-amber-50/60',
                    'border-emerald-500','text-emerald-700','bg-emerald-50/60',
                    'border-red-500','text-red-700','bg-red-50/60');
                b.classList.add.apply(b.classList, inactiveBtn);

                var svg = b.querySelector('svg');
                if (svg) {
                    svg.classList.remove('text-violet-600','text-amber-500','text-emerald-500','text-red-500');
                    svg.classList.add.apply(svg.classList, inactiveSvg);
                }

                var badge = b.querySelector('[class*="ml-1"]');
                if (badge) {
                    badge.classList.remove('bg-amber-100','text-amber-800','bg-emerald-100','text-emerald-800');
                    badge.classList.add.apply(badge.classList, inactiveBadge);
                }
            });

            // Activate target button
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

            // Update URL without reload
            var url = new URL(window.location);
            url.searchParams.set('tab', tab);
            url.searchParams.delete('page');
            history.replaceState(null, '', url.toString());

            activeTab = tab;

            if (['pending','approved','rejected'].indexOf(tab) !== -1) initTable(tab);
        });
    });

    // === Table initializer (shared for pending/approved/rejected) ===
    var initialized = {};
    var columnConfig = {
        pending:  { show: ['employee','department','leave_type','dates','days','applied'] },
        approved: { show: ['employee','department','leave_type','dates','days','pay_status','approved_date'] },
        rejected: { show: ['employee','department','leave_type','dates','days','rejected_date'] },
    };

    function initTable(tab) {
        if (initialized[tab]) return;
        initialized[tab] = true;

        var cols = columnConfig[tab] ? columnConfig[tab].show : [];
        if (!cols.length) return;

        var data = window['leaveData_' + tab] || [];
        var PER = 10;
        var page = 1, filtered = [];

        var search = document.querySelector('#tab-' + tab + ' #lvSearch');
        var deptF  = document.querySelector('#tab-' + tab + ' #lvDeptFilter');
        var typeF  = document.querySelector('#tab-' + tab + ' #lvTypeFilter');

        function applyFilters() {
            var q = search ? search.value.toLowerCase().trim() : '';
            var dt = deptF ? deptF.value : '';
            var ty = typeF ? typeF.value : '';
            filtered = data.filter(function(r) {
                if (q && !r.name_lower.includes(q)) return false;
                if (dt && r.dept !== dt) return false;
                if (ty && r.type !== ty) return false;
                return true;
            });
            page = 1;
            render();
        }

        function render() {
            var tbody = document.getElementById('lvTbody_' + tab);
            var noRes = document.getElementById('lvNoRes_' + tab);
            var emptyT = document.getElementById('lvEmptyTitle_' + tab);
            var emptyS = document.getElementById('lvEmptySub_' + tab);
            if (!tbody) return;

            var start = (page - 1) * PER;
            var end = Math.min(start + PER, filtered.length);
            var pageData = filtered.slice(start, end);
            tbody.innerHTML = '';

            if (pageData.length === 0) {
                if (noRes) noRes.classList.remove('hidden');
                if (emptyT && filtered.length === 0) { /* keep */ }
                else if (emptyT) emptyT.textContent = 'No results found';
                if (emptyS && filtered.length === 0) { /* keep */ }
                else if (emptyS) emptyS.textContent = 'Try a different search or filter.';
            } else {
                if (noRes) noRes.classList.add('hidden');
                pageData.forEach(function(r) {
                    var tr = document.createElement('tr');
                    tr.className = 'border-b border-gray-100 hover:bg-gray-50/50 transition-colors cursor-pointer';
                    tr.dataset.name = r.name_lower;
                    tr.dataset.dept = r.dept;
                    tr.dataset.type = r.type;
                    tr.onclick = function() { window.location = r.url; };

                    var html = '';

                    if (cols.indexOf('employee') !== -1) {
                        html += '<td class="px-4 py-3.5"><div class="font-semibold text-gray-900 text-sm">' + r.name + '</div></td>';
                    }
                    if (cols.indexOf('department') !== -1) {
                        html += '<td class="px-4 py-3.5"><span class="text-xs text-gray-400 font-mono">' + r.department_display + '</span></td>';
                    }
                    if (cols.indexOf('leave_type') !== -1) {
                        var tCls = tab === 'pending' ? 'bg-amber-50 text-amber-700 border-amber-200' : (tab === 'approved' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-red-50 text-red-700 border-red-200');
                        html += '<td class="px-4 py-3.5"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border ' + tCls + '">' + r.type_display + '</span></td>';
                    }
                    if (cols.indexOf('dates') !== -1) {
                        html += '<td class="px-4 py-3.5"><span class="text-sm text-gray-700 font-mono">' + r.start_date + ' – ' + r.end_date + '</span></td>';
                    }
                    if (cols.indexOf('days') !== -1) {
                        html += '<td class="px-4 py-3.5"><span class="font-bold text-gray-900">' + r.days + '<span class="text-gray-400 font-normal text-xs">d</span></span></td>';
                    }
                    if (cols.indexOf('pay_status') !== -1) {
                        var psCls = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                        html += '<td class="px-4 py-3.5"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border ' + psCls + '">' + (r.status === 'paid' ? 'Paid' : 'Approved') + '</span></td>';
                    }
                    if (cols.indexOf('applied') !== -1) {
                        html += '<td class="px-4 py-3.5"><span class="text-xs text-gray-400 font-mono">' + r.created_at + '</span></td>';
                    }
                    if (cols.indexOf('approved_date') !== -1) {
                        html += '<td class="px-4 py-3.5"><span class="text-xs text-gray-400 font-mono">' + r.updated_at + '</span></td>';
                    }
                    if (cols.indexOf('rejected_date') !== -1) {
                        html += '<td class="px-4 py-3.5"><span class="text-xs text-gray-400 font-mono">' + r.updated_at + '</span></td>';
                    }

                    tr.innerHTML = html;
                    tbody.appendChild(tr);
                });
            }
            updatePagination();
        }

        function updatePagination() {
            var total = filtered.length;
            var pages = Math.ceil(total / PER);
            var info  = document.getElementById('lvInfo_' + tab);
            var nav   = document.getElementById('lvNav_' + tab);
            if (!info || !nav) return;
            if (total === 0) { info.innerHTML = 'No records to display'; nav.innerHTML = ''; return; }
            var s = (page - 1) * PER + 1, e = Math.min(page * PER, total);
            info.innerHTML = 'Showing <strong class="text-gray-700">' + s + '</strong>&ndash;<strong class="text-gray-700">' + e + '</strong> of <strong class="text-gray-700">' + total + '</strong>';
            if (pages <= 1) { nav.innerHTML = ''; return; }

            var base = 'flex items-center justify-center w-7 h-7 rounded-lg text-xs font-semibold border transition-all duration-150';
            var act  = base + ' bg-gray-900 text-white border-gray-900';
            var def  = base + ' bg-white text-gray-600 border-gray-200 hover:bg-gray-50 hover:text-gray-900 hover:border-gray-300';
            var dis  = base + ' bg-gray-50 text-gray-300 border-gray-100 cursor-not-allowed pointer-events-none';

            var h = '';
            h += '<button data-p="' + (page - 1) + '" class="' + (page === 1 ? dis : def) + '">‹</button>';
            for (var i = 1; i <= pages; i++) {
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
        if (typeF) typeF.addEventListener('change', applyFilters);
        applyFilters();
    }

    // Initialize all three data tabs on page load
    ['pending','approved','rejected'].forEach(initTable);
})();
</script>
@endpush
