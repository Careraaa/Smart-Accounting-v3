@extends('layouts.layout')

@push('styles')
<style>
@keyframes ouFadeUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes ouScaleIn { 0%{opacity:0;transform:scale(0.93)} 100%{opacity:1;transform:scale(1)} }
.ou-page { animation:ouFadeUp 0.3s ease-out; }
.ou-stat { animation:ouScaleIn 0.35s cubic-bezier(0.16,1,0.3,1) both; }
.ou-stat:nth-child(1) { animation-delay:0.05s; }
.ou-stat:nth-child(2) { animation-delay:0.1s; }
.ou-stat:nth-child(3) { animation-delay:0.15s; }
.ou-stat:nth-child(4) { animation-delay:0.2s; }
.ou-table { animation:ouFadeUp 0.4s ease-out 0.1s both; }
</style>
@endpush

@section('content')
<div class="ou-page min-h-screen bg-gray-50/60">
    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 py-8">

        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-7">
            <div>
                <p class="text-xs font-semibold tracking-widest text-gray-400 uppercase mb-1">Employee Portal</p>
                <h1 class="text-2xl font-extrabold tracking-tight text-gray-900 leading-tight">Overtime &amp; Undertime</h1>
                <p class="text-sm text-gray-500 mt-1">Submit, track, and manage your OT/UT requests.</p>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-gray-200 text-xs font-semibold text-gray-600 shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                    {{ now()->format('l, F d, Y') }}
                </span>
                <a href="{{ route('employee.overtime-undertime.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white text-sm font-semibold rounded-xl transition-all shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Submit Request
                </a>
            </div>
        </div>

        {{-- Tab Navigation --}}
        <div class="border-b border-gray-200 mb-6">
            <nav class="flex gap-1 -mb-px" role="tablist">
                <a href="{{ route('employee.attendance.index') }}" role="tab"
                   class="relative px-5 py-3 text-sm font-medium border-b-2 transition-all duration-200 group inline-flex items-center gap-2
                   {{ request()->routeIs('employee.attendance.index')
                       ? 'border-violet-600 text-violet-700 bg-violet-50/60'
                       : 'border-transparent text-gray-400 hover:text-gray-600 hover:border-gray-300 hover:bg-gray-50/50' }}">
                    <svg class="w-4 h-4 transition-colors {{ request()->routeIs('employee.attendance.index') ? 'text-violet-600' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                    Attendance Calendar
                </a>
                <a href="{{ route('employee.overtime-undertime.index') }}" role="tab"
                   class="relative px-5 py-3 text-sm font-medium border-b-2 transition-all duration-200 group inline-flex items-center gap-2
                   {{ request()->routeIs('employee.overtime-undertime.index')
                       ? 'border-amber-500 text-amber-700 bg-amber-50/60'
                       : 'border-transparent text-gray-400 hover:text-gray-600 hover:border-gray-300 hover:bg-gray-50/50' }}">
                    <svg class="w-4 h-4 transition-colors {{ request()->routeIs('employee.overtime-undertime.index') ? 'text-amber-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
                    OT / UT Requests
                </a>
            </nav>
        </div>

        {{-- Stat cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
            <div class="ou-stat bg-white border border-gray-200 rounded-2xl p-4 flex items-center gap-4 shadow-sm">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div>
                    <div class="text-xs text-gray-400 font-semibold">Total</div>
                    <div class="text-xl font-extrabold text-gray-900 font-mono leading-none">{{ $totalRequests }}</div>
                    <div class="text-xs text-gray-400 mt-0.5">submitted</div>
                </div>
            </div>
            <div class="ou-stat bg-white border border-amber-200 rounded-2xl p-4 flex items-center gap-4 shadow-sm">
                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
                </div>
                <div>
                    <div class="text-xs text-amber-600 font-semibold">Pending</div>
                    <div class="text-xl font-extrabold text-gray-900 font-mono leading-none">{{ $pendingRequests }}</div>
                    <div class="text-xs text-gray-400 mt-0.5">awaiting review</div>
                </div>
            </div>
            <div class="ou-stat bg-white border border-emerald-200 rounded-2xl p-4 flex items-center gap-4 shadow-sm">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="text-xs text-emerald-600 font-semibold">Approved</div>
                    <div class="text-xl font-extrabold text-gray-900 font-mono leading-none">{{ $approvedRequests }}</div>
                    <div class="text-xs text-gray-400 mt-0.5">granted</div>
                </div>
            </div>
            <div class="ou-stat bg-white border border-rose-200 rounded-2xl p-4 flex items-center gap-4 shadow-sm">
                <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="text-xs text-rose-600 font-semibold">Rejected</div>
                    <div class="text-xl font-extrabold text-gray-900 font-mono leading-none">{{ $rejectedRequests }}</div>
                    <div class="text-xs text-gray-400 mt-0.5">not approved</div>
                </div>
            </div>
        </div>

        {{-- Requests Table --}}
        <div class="ou-table bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-gray-900"></div>
                    <span class="text-sm font-bold text-gray-900">Requests</span>
                </div>
                <div class="flex items-center gap-1 bg-gray-50 border border-gray-200 rounded-lg p-0.5">
                    <a href="{{ route('employee.overtime-undertime.index') }}"
                       class="px-3 py-1.5 rounded-md text-xs font-semibold transition-all {{ ($status === 'all') ? 'bg-white shadow text-gray-900' : 'text-gray-400 hover:text-gray-700' }}">All</a>
                    <a href="{{ route('employee.overtime-undertime.index', ['status' => 'pending']) }}"
                       class="px-3 py-1.5 rounded-md text-xs font-semibold transition-all {{ ($status === 'pending') ? 'bg-white shadow text-gray-900' : 'text-gray-400 hover:text-gray-700' }}">Pending</a>
                    <a href="{{ route('employee.overtime-undertime.index', ['status' => 'approved']) }}"
                       class="px-3 py-1.5 rounded-md text-xs font-semibold transition-all {{ ($status === 'approved') ? 'bg-white shadow text-gray-900' : 'text-gray-400 hover:text-gray-700' }}">Approved</a>
                    <a href="{{ route('employee.overtime-undertime.index', ['status' => 'rejected']) }}"
                       class="px-3 py-1.5 rounded-md text-xs font-semibold transition-all {{ ($status === 'rejected') ? 'bg-white shadow text-gray-900' : 'text-gray-400 hover:text-gray-700' }}">Rejected</a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Date</th>
                            <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Type</th>
                            <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Hours</th>
                            <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Reason</th>
                            <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Status</th>
                            <th class="text-right px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($requests as $request)
                            @php
                                $pill = in_array($request->status, ['pending','approved','rejected'], true) ? $request->status : 'neutral';
                                $pillClasses = match($pill) {
                                    'pending'  => 'bg-amber-50 text-amber-700 border border-amber-200',
                                    'approved' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                                    'rejected' => 'bg-rose-50 text-rose-700 border border-rose-200',
                                    default    => 'bg-gray-100 text-gray-600 border border-gray-200',
                                };
                                $typeClasses = $request->type === 'overtime'
                                    ? 'bg-blue-50 text-blue-700 border border-blue-200'
                                    : 'bg-yellow-50 text-yellow-700 border border-yellow-200';
                            @endphp
                            <tr class="hover:bg-gray-50/70 transition-colors">
                                <td class="px-5 py-3.5">
                                    <div class="font-semibold text-gray-900 text-sm">{{ $request->date->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-400 mt-0.5">{{ $request->date->format('l') }}</div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $typeClasses }}">
                                        {{ ucfirst($request->type) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="font-mono font-bold text-gray-900">{{ number_format($request->hours, 2) }}h</span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="text-gray-500 text-sm">{{ Str::limit($request->reason, 60) }}</span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $pillClasses }}">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-1.5 justify-end">
                                        <a href="{{ route('employee.overtime-undertime.show', $request->id) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg text-xs font-semibold text-gray-600 transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            View
                                        </a>
                                        @if($request->status === 'pending')
                                            <a href="{{ route('employee.overtime-undertime.edit', $request->id) }}"
                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-50 hover:bg-blue-50 border border-gray-200 hover:border-blue-200 rounded-lg text-xs font-semibold text-gray-600 hover:text-blue-700 transition-all">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                Edit
                                            </a>
                                            <form action="{{ route('employee.overtime-undertime.destroy', $request->id) }}" method="POST" style="display:inline;" data-sa-confirm="Delete this request?">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-50 hover:bg-rose-50 border border-gray-200 hover:border-rose-200 rounded-lg text-xs font-semibold text-gray-600 hover:text-rose-700 transition-all">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path stroke-linecap="round" stroke-linejoin="round" d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6m5 0V4a1 1 0 011-1h2a1 1 0 011 1v2"/></svg>
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-14 h-14 rounded-2xl bg-gray-50 border border-gray-200 flex items-center justify-center">
                                            <svg class="w-7 h-7 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                        </div>
                                        <p class="text-sm font-semibold text-gray-400">No overtime / undertime requests found.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($requests->hasPages())
                <div class="flex items-center justify-between px-5 py-3.5 border-t border-gray-100 flex-wrap gap-2">
                    <div class="text-xs text-gray-400">
                        Showing <strong class="text-gray-700">{{ $requests->firstItem() }}</strong>–<strong class="text-gray-700">{{ $requests->lastItem() }}</strong> of <strong class="text-gray-700">{{ $requests->total() }}</strong>
                    </div>
                    {{ $requests->links('pagination::tailwind') }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
