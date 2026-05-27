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
.table-wrap { animation: fadeSlideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) 0.15s both; }
.filter-bar { animation: slideInRight 0.4s cubic-bezier(0.16, 1, 0.3, 1) 0.1s both; }
</style>
@endpush

@section('content')
<div class="max-w-full">

    {{-- Flash --}}
    @foreach(['success','error'] as $t)
        @if(session($t))
        <div class="flex items-center gap-2.5 px-4 py-3 mb-5 rounded-xl text-sm font-medium {{ $t === 'success' ? 'bg-emerald-50 border border-emerald-200 text-emerald-700' : 'bg-red-50 border border-red-200 text-red-700' }}" style="animation:fadeSlideUp 0.35s ease both;">
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
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Attendance</h1>
            <p class="text-sm text-gray-500 mt-0.5">Employee time-in / time-out and OT/UT records</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('attendance.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded-xl hover:bg-gray-800 hover:shadow-lg hover:shadow-gray-900/20 active:scale-[0.97] transition-all duration-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Manual Log
            </a>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="border-b border-gray-200 mb-6" style="animation:fadeSlideUp 0.4s cubic-bezier(0.16,1,0.3,1) 0.05s both;">
        <nav class="flex gap-1 -mb-px" role="tablist">
            <button role="tab" data-tab="records"
                class="tab-btn relative px-5 py-3 text-sm font-medium border-b-2 transition-all duration-200 group
                {{ (!$tab || $tab === 'records')
                    ? 'border-cyan-600 text-cyan-700 bg-cyan-50/60'
                    : 'border-transparent text-gray-400 hover:text-gray-600 hover:border-gray-300 hover:bg-gray-50/50' }}">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 {{ (!$tab || $tab === 'records') ? 'text-cyan-600' : 'text-gray-400 group-hover:text-gray-500' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Today's Records
                </span>
            </button>
            <button role="tab" data-tab="otut"
                class="tab-btn relative px-5 py-3 text-sm font-medium border-b-2 transition-all duration-200 group
                {{ $tab === 'otut'
                    ? 'border-amber-500 text-amber-700 bg-amber-50/60'
                    : 'border-transparent text-gray-400 hover:text-gray-600 hover:border-gray-300 hover:bg-gray-50/50' }}">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 {{ $tab === 'otut' ? 'text-amber-500' : 'text-gray-400 group-hover:text-gray-500' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" d="M12 8v4m0 4h.01"/></svg>
                    OT / UT Overview
                    @if(($pendingOT + $pendingUT) > 0)
                    <span class="ml-1 inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-[11px] font-bold
                        {{ $tab === 'otut' ? 'bg-amber-100 text-amber-800' : 'bg-gray-200 text-gray-600' }}
                        transition-colors duration-200">{{ $pendingOT + $pendingUT }}</span>
                    @endif
                </span>
            </button>
        </nav>
    </div>

    {{-- ===== TAB: TODAY'S RECORDS ===== --}}
    <div id="tab-records" class="tab-panel {{ ($tab && $tab !== 'records') ? 'hidden' : '' }} active-panel">

        {{-- Filter bar --}}
        <div class="filter-bar grid grid-cols-1 sm:flex items-center gap-2.5 mb-4">
            <input type="text" id="empSearch" placeholder="Search employee…"
                class="w-full sm:flex-1 px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-white text-gray-900 placeholder-gray-400 outline-none transition-all duration-200 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-200/50 focus:shadow-md focus:shadow-cyan-100 hover:border-gray-300">
            <div class="grid grid-cols-2 sm:flex gap-2.5">
                <select id="deptFilter"
                    class="px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl bg-white text-gray-700 outline-none transition-all duration-200 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-200/50 focus:shadow-md focus:shadow-cyan-100 hover:border-gray-300 cursor-pointer">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ strtolower($dept) }}">{{ $dept }}</option>
                    @endforeach
                </select>
                <select id="statusFilter"
                    class="px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl bg-white text-gray-700 outline-none transition-all duration-200 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-200/50 focus:shadow-md focus:shadow-cyan-100 hover:border-gray-300 cursor-pointer">
                    <option value="">All Statuses</option>
                    <option value="in">Timed In</option>
                    <option value="out">Timed Out</option>
                    <option value="late">Late</option>
                    <option value="absent">Not Yet In</option>
                </select>
            </div>
            <span class="text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg px-2.5 py-1.5 hidden sm:inline-flex items-center gap-1 shrink-0">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                {{ now()->format('l, F j, Y') }}
            </span>
        </div>

        {{-- Table --}}
        <div class="table-wrap bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition-shadow duration-300">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Employee</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 hidden md:table-cell">Department</th>
                            <th class="text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Today's Status</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Time In</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 hidden sm:table-cell">Time Out</th>
                        </tr>
                    </thead>
                    <tbody id="empTbody">
                        @forelse($employees as $emp)
                            @php
                                $att = $todayAttendance[$emp->id] ?? null;
                                $initials = strtoupper(substr($emp->first_name ?? 'U', 0, 1) . substr($emp->last_name ?? '', 0, 1));

                                if (!$att) {
                                    $todayStatus = 'absent';
                                    $statusLabel = 'Not Yet In';
                                    $statusDot = 'bg-red-500';
                                    $statusBg = 'bg-red-50 text-red-700 border-red-200';
                                } elseif ($att->time_out) {
                                    $todayStatus = 'out';
                                    $statusLabel = 'Timed Out';
                                    $statusDot = 'bg-slate-400';
                                    $statusBg = 'bg-slate-50 text-slate-600 border-slate-200';
                                } elseif ($att->status === 'late') {
                                    $todayStatus = 'late';
                                    $statusLabel = 'Late';
                                    $statusDot = 'bg-yellow-500';
                                    $statusBg = 'bg-yellow-50 text-yellow-700 border-yellow-200';
                                } else {
                                    $todayStatus = 'in';
                                    $statusLabel = 'Timed In';
                                    $statusDot = 'bg-emerald-500';
                                    $statusBg = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                }
                            @endphp
                            <tr
                                class="border-b border-gray-100 hover:bg-cyan-50/30 transition-colors duration-150 cursor-pointer"
                                data-emp-id="{{ $emp->id }}"
                                data-name="{{ strtolower($emp->first_name . ' ' . $emp->last_name) }}"
                                data-dept="{{ strtolower($emp->department ?? '') }}"
                                data-status="{{ $todayStatus }}"
                                onclick="window.location='{{ route('attendance.employee.calendar', $emp->id) }}'"
                            >
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center text-[10px] font-bold text-gray-500 uppercase shrink-0">{{ $initials }}</div>
                                        <div>
                                            <div class="font-semibold text-gray-900 text-sm">{{ $emp->first_name }} {{ $emp->last_name }}</div>
                                            <div class="text-xs text-gray-400">{{ $emp->position ?? '—' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 hidden md:table-cell">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-600 border border-gray-200">{{ $emp->department ?? '—' }}</span>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $statusBg }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $statusDot }}"></span>
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 font-mono text-sm text-gray-700">
                                    {{ $att && $att->time_in ? \Carbon\Carbon::createFromFormat('H:i:s', $att->time_in)->format('g:i A') : '—' }}
                                </td>
                                <td class="px-4 py-3.5 font-mono text-sm text-gray-700 hidden sm:table-cell">
                                    {{ $att && $att->time_out ? \Carbon\Carbon::createFromFormat('H:i:s', $att->time_out)->format('g:i A') : '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="100">
                                    <div class="flex flex-col items-center justify-center py-16 text-center">
                                        <div class="w-14 h-14 rounded-xl bg-gray-100 flex items-center justify-center text-gray-300 mb-4">
                                            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                                        </div>
                                        <p class="text-sm font-semibold text-gray-700">No employees found</p>
                                        <p class="text-xs text-gray-400 mt-1">Add employees to start tracking attendance.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div id="empNoResults" style="display:none;">
                <div class="flex flex-col items-center justify-center py-10 text-center">
                    <p class="text-sm font-semibold text-gray-700">No results</p>
                    <p class="text-xs text-gray-400 mt-1">Try adjusting your filters.</p>
                </div>
            </div>

            {{-- Pagination --}}
            <div class="flex items-center justify-between px-4 py-3 border-t border-gray-100 bg-gray-50 flex-wrap gap-3">
                <div class="text-xs text-gray-400" id="attPaginationInfo">
                    Showing <strong class="text-gray-600">1</strong>–<strong class="text-gray-600">10</strong> of <strong class="text-gray-600">0</strong> employees
                </div>
                <nav id="attPaginationNav"></nav>
            </div>
        </div>
    </div>

    {{-- ===== TAB: OT / UT OVERVIEW ===== --}}
    <div id="tab-otut" class="tab-panel {{ $tab !== 'otut' ? 'hidden' : '' }} active-panel">

        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
            <div class="stat-card bg-white border border-gray-200 rounded-xl px-5 py-4 flex items-start gap-3.5 hover:shadow-md hover:border-amber-200 transition-all duration-300">
                <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 border border-amber-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">Pending Overtime</div>
                    <div class="text-2xl font-bold text-gray-900 tabular-nums mt-0.5">{{ $pendingOT }}</div>
                    <div class="text-xs text-gray-400 mt-1">Awaiting approval</div>
                </div>
            </div>
            <div class="stat-card bg-white border border-gray-200 rounded-xl px-5 py-4 flex items-start gap-3.5 hover:shadow-md hover:border-amber-200 transition-all duration-300">
                <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 border border-amber-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">Pending Undertime</div>
                    <div class="text-2xl font-bold text-gray-900 tabular-nums mt-0.5">{{ $pendingUT }}</div>
                    <div class="text-xs text-gray-400 mt-1">Awaiting approval</div>
                </div>
            </div>
            <div class="stat-card bg-white border border-gray-200 rounded-xl px-5 py-4 flex items-start gap-3.5 hover:shadow-md hover:border-gray-300 transition-all duration-300">
                <div class="w-10 h-10 rounded-lg bg-gray-100 text-gray-500 flex items-center justify-center shrink-0 border border-gray-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">This Month</div>
                    <div class="text-2xl font-bold text-gray-900 tabular-nums mt-0.5">{{ now()->format('F Y') }}</div>
                    <div class="text-xs text-gray-400 mt-1">Reference period</div>
                </div>
            </div>
        </div>

        {{-- Recent pending requests --}}
        <div class="flex items-center justify-between mb-4 filter-bar">
            <div class="text-sm font-semibold text-gray-700">Pending Requests</div>
            <a href="{{ route('overtime.pending') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-medium text-amber-700 bg-amber-50 border border-amber-200 rounded-xl hover:bg-amber-100 hover:shadow-sm transition-all duration-200">
                View All
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div class="table-wrap bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition-shadow duration-300">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Employee</th>
                            <th class="text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Type</th>
                            <th class="text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Hours</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Date</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOtUt as $req)
                            <tr class="border-b border-gray-100 hover:bg-amber-50/30 transition-colors duration-150">
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center text-[10px] font-bold text-gray-500 uppercase shrink-0">
                                            {{ strtoupper(substr($req->employee->first_name ?? 'U', 0, 1) . substr($req->employee->last_name ?? '', 0, 1)) }}
                                        </div>
                                        <div class="font-semibold text-gray-900 text-sm">{{ $req->employee->first_name ?? 'Unknown' }} {{ $req->employee->last_name ?? '' }}</div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $req->type === 'overtime' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            @if($req->type === 'overtime')
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/>
                                            @endif
                                        </svg>
                                        {{ $req->type === 'overtime' ? 'OT' : 'UT' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-center font-mono text-sm font-semibold text-gray-700">{{ number_format($req->hours ?? 0, 1) }}h</td>
                                <td class="px-4 py-3.5 text-sm text-gray-600">{{ $req->date ? \Carbon\Carbon::parse($req->date)->format('M d, Y') : '—' }}</td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Pending
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="100">
                                    <div class="flex flex-col items-center justify-center py-16 text-center">
                                        <div class="w-14 h-14 rounded-xl bg-gray-100 flex items-center justify-center text-gray-300 mb-4">
                                            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" d="M12 8v4m0 4h.01"/></svg>
                                        </div>
                                        <p class="text-sm font-semibold text-gray-700">No pending OT/UT requests</p>
                                        <p class="text-xs text-gray-400 mt-1">All caught up — nothing to review.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.notificationsCountUrl = "{{ route('notifications.count') }}";
window.attendanceCalendarRoute = "{{ route('attendance.employee.calendar', ['employee' => ':id']) }}";

{{-- Build employee data  --}}
window.allEmployeesData = {!! json_encode($allEmployees->map(fn($e) => [
    'id' => $e->id,
    'firstName' => $e->first_name,
    'lastName' => $e->last_name,
    'name' => strtolower($e->first_name . ' ' . $e->last_name),
    'dept' => strtolower($e->department ?? ''),
    'department' => $e->department,
    'position' => $e->position,
])) !!};

window.todayAttendance = {!! json_encode($todayAttendance->map(fn($a) => [
    'user_id' => $a->user_id,
    'time_in' => $a->time_in,
    'time_out' => $a->time_out,
    'status' => $a->status,
])->keyBy('user_id')) !!};

(function(){
    // ── Tab switching ──
    const tabs = document.querySelectorAll('.tab-btn');
    tabs.forEach(btn => {
        btn.addEventListener('click', function() {
            const tab = this.dataset.tab;
            const url = new URL(window.location);
            if (tab === 'records') {
                url.searchParams.delete('tab');
            } else {
                url.searchParams.set('tab', tab);
            }
            url.searchParams.delete('page');
            window.location.href = url.toString();
        });
    });

    // ── Client-side filter and pagination for attendance table ──
    const search  = document.getElementById('empSearch');
    const deptF   = document.getElementById('deptFilter');
    const statusF = document.getElementById('statusFilter');
    const tbody   = document.getElementById('empTbody');
    const noRes   = document.getElementById('empNoResults');

    if (search && tbody) {
        let currentPage = 1;
        const itemsPerPage = 10;
        let filteredData = [];

        function getEmployeeStatus(emp) {
            const att = window.todayAttendance[emp.id];
            if (!att) return 'absent';
            if (att.time_out) return 'out';
            if (att.status === 'late') return 'late';
            return 'in';
        }

        function applyFilters() {
            const q = search.value.toLowerCase().trim();
            const d = deptF.value;
            const s = statusF.value;

            filteredData = window.allEmployeesData.filter(emp => {
                const matchesSearch = !q || emp.name.includes(q);
                const matchesDept = !d || emp.dept === d;
                const matchesStatus = !s || getEmployeeStatus(emp) === s;
                return matchesSearch && matchesDept && matchesStatus;
            });

            currentPage = 1;
            renderTable();
        }

        function renderTable() {
            const start = (currentPage - 1) * itemsPerPage;
            const end = start + itemsPerPage;
            const pageData = filteredData.slice(start, end);

            tbody.innerHTML = '';

            if (pageData.length === 0) {
                if (filteredData.length === 0 && window.allEmployeesData.length > 0) {
                    noRes.style.display = 'block';
                } else if (window.allEmployeesData.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="100"><div class="flex flex-col items-center justify-center py-16 text-center"><div class="w-14 h-14 rounded-xl bg-gray-100 flex items-center justify-center text-gray-300 mb-4"><svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg></div><p class="text-sm font-semibold text-gray-700">No employees found</p><p class="text-xs text-gray-400 mt-1">Add employees to start tracking attendance.</p></div></td></tr>';
                }
            } else {
                noRes.style.display = 'none';
                pageData.forEach(emp => {
                    const att = window.todayAttendance[emp.id];
                    const status = getEmployeeStatus(emp);
                    let statusLabel, statusDot, statusBg;
                    if (status === 'absent') {
                        statusLabel = 'Not Yet In'; statusDot = 'bg-red-500'; statusBg = 'bg-red-50 text-red-700 border-red-200';
                    } else if (status === 'out') {
                        statusLabel = 'Timed Out'; statusDot = 'bg-slate-400'; statusBg = 'bg-slate-50 text-slate-600 border-slate-200';
                    } else if (status === 'late') {
                        statusLabel = 'Late'; statusDot = 'bg-yellow-500'; statusBg = 'bg-yellow-50 text-yellow-700 border-yellow-200';
                    } else {
                        statusLabel = 'Timed In'; statusDot = 'bg-emerald-500'; statusBg = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                    }

                    const initials = (emp.firstName.charAt(0) + emp.lastName.charAt(0)).toUpperCase();
                    const timeIn = att && att.time_in ? new Date('2000-01-01 ' + att.time_in).toLocaleTimeString('en-US', {hour: 'numeric', minute: '2-digit', hour12: true}) : '—';
                    const timeOut = att && att.time_out ? new Date('2000-01-01 ' + att.time_out).toLocaleTimeString('en-US', {hour: 'numeric', minute: '2-digit', hour12: true}) : '—';
                    const calendarUrl = window.attendanceCalendarRoute.replace(':id', emp.id);

                    const row = document.createElement('tr');
                    row.className = 'border-b border-gray-100 hover:bg-cyan-50/30 transition-colors duration-150 cursor-pointer';
                    row.dataset.empId = emp.id;
                    row.dataset.name = emp.name;
                    row.dataset.dept = emp.dept;
                    row.dataset.status = status;
                    row.onclick = () => window.location = calendarUrl;
                    row.innerHTML = `
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center text-[10px] font-bold text-gray-500 uppercase shrink-0">${initials}</div>
                                <div>
                                    <div class="font-semibold text-gray-900 text-sm">${emp.firstName} ${emp.lastName}</div>
                                    <div class="text-xs text-gray-400">${emp.position || '—'}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3.5 hidden md:table-cell"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-600 border border-gray-200">${emp.department || '—'}</span></td>
                        <td class="px-4 py-3.5 text-center">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold ${statusBg}">
                                <span class="w-1.5 h-1.5 rounded-full ${statusDot}"></span>
                                ${statusLabel}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 font-mono text-sm text-gray-700">${timeIn}</td>
                        <td class="px-4 py-3.5 font-mono text-sm text-gray-700 hidden sm:table-cell">${timeOut}</td>
                    `;
                    tbody.appendChild(row);
                });
            }

            updatePagination();
        }

        function updatePagination() {
            const totalPages = Math.ceil(filteredData.length / itemsPerPage);
            const info = document.getElementById('attPaginationInfo');
            const nav = document.getElementById('attPaginationNav');
            if (!info || !nav) return;

            if (filteredData.length === 0) {
                info.innerHTML = 'No employees to display';
                nav.innerHTML = '';
                return;
            }

            const start = (currentPage - 1) * itemsPerPage + 1;
            const end = Math.min(currentPage * itemsPerPage, filteredData.length);
            info.innerHTML = `Showing <strong class="text-gray-600">${start}</strong>–<strong class="text-gray-600">${end}</strong> of <strong class="text-gray-600">${filteredData.length}</strong> employee${filteredData.length > 1 ? 's' : ''}`;

            if (totalPages <= 1) {
                nav.innerHTML = '';
                return;
            }

            let html = '<div class="flex items-center gap-1">';
            html += `<button class="w-7 h-7 rounded-lg border border-gray-200 bg-white text-gray-500 text-xs font-semibold flex items-center justify-center hover:bg-gray-100 transition-colors ${currentPage === 1 ? 'opacity-40 cursor-not-allowed' : 'cursor-pointer'}" data-page="${currentPage - 1}" ${currentPage === 1 ? 'disabled' : ''}>\u2039</button>`;
            for (let i = 1; i <= totalPages; i++) {
                html += `<button class="w-7 h-7 rounded-lg text-xs font-semibold flex items-center justify-center transition-colors ${i === currentPage ? 'bg-gray-900 text-white' : 'border border-gray-200 bg-white text-gray-500 hover:bg-gray-100'}" data-page="${i}">${i}</button>`;
            }
            html += `<button class="w-7 h-7 rounded-lg border border-gray-200 bg-white text-gray-500 text-xs font-semibold flex items-center justify-center hover:bg-gray-100 transition-colors ${currentPage === totalPages ? 'opacity-40 cursor-not-allowed' : 'cursor-pointer'}" data-page="${currentPage + 1}" ${currentPage === totalPages ? 'disabled' : ''}>\u203a</button>`;
            html += '</div>';

            nav.innerHTML = html;

            nav.querySelectorAll('button[data-page]').forEach(btn => {
                btn.addEventListener('click', function() {
                    const page = parseInt(this.dataset.page);
                    if (page < 1 || page > totalPages) return;
                    currentPage = page;
                    renderTable();
                });
            });
        }

        search.addEventListener('input', applyFilters);
        deptF.addEventListener('change', applyFilters);
        statusF.addEventListener('change', applyFilters);
        applyFilters();
    }

    // ── Notification polling ──
    function pollNotifications() {
        fetch(window.notificationsCountUrl)
            .then(r => r.ok ? r.json() : Promise.reject())
            .then(data => {
                const unread = data.unread_count;
                const dot   = document.getElementById('notif-count');
                const badge = document.getElementById('notif-badge');
                if (dot)   { dot.textContent = unread > 99 ? '99+' : unread; dot.style.display = unread > 0 ? 'inline-block' : 'none'; }
                if (badge) { badge.textContent = unread > 0 ? `${unread} New` : '0 New'; }
            })
            .catch(() => {});
    }
    setInterval(pollNotifications, 30000);
    pollNotifications();
})();
</script>
@endpush
