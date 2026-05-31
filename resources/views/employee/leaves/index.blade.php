@extends('layouts.layout')

@push('styles')
<style>
@keyframes ll-page-in { 0%{opacity:0} 100%{opacity:1} }
@keyframes ll-stat-in { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes ll-balance-in { 0%{opacity:0;transform:translateY(10px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes ll-balance-pop { 0%{transform:scale(0.8)} 100%{transform:scale(1)} }
@keyframes ll-table-in { 0%{opacity:0;transform:translateY(8px)} 100%{opacity:1;transform:translateY(0)} }
.ll-page { animation:ll-page-in 0.3s ease-out; }
.ll-stat { animation:ll-stat-in 0.35s ease-out both; }
.ll-stat:nth-child(1) { animation-delay:0.05s; }
.ll-stat:nth-child(2) { animation-delay:0.1s; }
.ll-stat:nth-child(3) { animation-delay:0.15s; }
.ll-stat:nth-child(4) { animation-delay:0.2s; }
.ll-balance { animation:ll-balance-in 0.3s ease-out both; animation-delay:0.15s; }
.ll-balance-pop { animation:ll-balance-pop 0.4s cubic-bezier(0.34,1.56,0.64,1) both; }
.ll-table { animation:ll-table-in 0.3s ease-out both; animation-delay:0.2s; }
</style>
@endpush

@section('content')
<div class="ll-page min-h-screen bg-gray-50/60">
    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 py-8">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-7">
            <div>
                <p class="text-xs font-semibold tracking-widest text-gray-400 uppercase mb-1">Employee Portal</p>
                <h1 class="text-2xl font-extrabold tracking-tight text-gray-900 leading-tight">My Leave Requests</h1>
                <p class="text-sm text-gray-500 mt-1">Track approvals, review history, and submit a new request.</p>
                <div class="flex flex-wrap gap-2 mt-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-gray-200 text-xs font-semibold text-gray-600 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                        {{ now()->format('l, F d, Y') }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-gray-200 text-xs font-semibold text-gray-600 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                        Filter: {{ ucfirst($status ?? 'all') }}
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('employee.leaves.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white text-sm font-bold rounded-xl transition-all shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Request New Leave
                </a>
            </div>
        </div>

        {{-- Stat cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
            <div class="ll-stat bg-white border border-gray-200 rounded-xl p-4 flex items-center gap-3 shadow-sm">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total</div>
                    <div class="text-lg font-bold text-gray-900 leading-none font-mono">{{ $totalLeaves }}</div>
                    <div class="text-xs text-gray-400 mt-0.5">requests submitted</div>
                </div>
            </div>
            <div class="ll-stat bg-white border border-gray-200 rounded-xl p-4 flex items-center gap-3 shadow-sm">
                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Pending</div>
                    <div class="text-lg font-bold text-gray-900 leading-none font-mono">{{ $pendingLeaves }}</div>
                    <div class="text-xs text-gray-400 mt-0.5">awaiting review</div>
                </div>
            </div>
            <div class="ll-stat bg-white border border-gray-200 rounded-xl p-4 flex items-center gap-3 shadow-sm">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Approved</div>
                    <div class="text-lg font-bold text-gray-900 leading-none font-mono">{{ $approvedLeaves }}</div>
                    <div class="text-xs text-gray-400 mt-0.5">granted</div>
                </div>
            </div>
            <div class="ll-stat bg-white border border-gray-200 rounded-xl p-4 flex items-center gap-3 shadow-sm">
                <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Rejected</div>
                    <div class="text-lg font-bold text-gray-900 leading-none font-mono">{{ $rejectedLeaves }}</div>
                    <div class="text-xs text-gray-400 mt-0.5">not approved</div>
                </div>
            </div>
        </div>

        {{-- Leave balance --}}
        @if($balances->count() > 0)
        <div class="ll-balance bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm mb-6">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100">
                <span class="w-2 h-2 rounded-full bg-gray-900"></span>
                <span class="text-sm font-bold text-gray-800">Leave Balance — {{ now()->year }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Leave Type</th>
                            <th class="text-center px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Total Days</th>
                            <th class="text-center px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Used</th>
                            <th class="text-center px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Remaining</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($balances as $balance)
                            <tr class="border-b border-gray-50 hover:bg-gray-50/50">
                                <td class="px-5 py-3">
                                    <div class="font-bold text-gray-900 text-sm">{{ $balance->leaveType?->name ?? 'N/A' }}</div>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <span class="font-bold text-gray-900 font-mono">{{ $balance->total_days }}</span>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <span class="font-bold text-gray-600 font-mono">{{ $balance->used_days }}</span>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <span class="ll-balance-pop inline-flex items-center justify-center font-bold font-mono text-sm {{ $balance->remaining_days > 0 ? 'text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-lg' : 'text-rose-700 bg-rose-50 px-2 py-0.5 rounded-lg' }}">
                                        {{ $balance->remaining_days }}
                                    </span>
                                </td>
                                <td class="px-5 py-3">
                                    @if($balance->remaining_days > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">Available</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-700">Exhausted</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Requests table --}}
        <div class="ll-table bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <span class="w-2 h-2 rounded-full bg-gray-900"></span>
                    <span class="text-sm font-bold text-gray-800">Requests</span>
                </div>
                <div class="flex items-center gap-1 bg-gray-50 rounded-lg p-0.5">
                    <a href="{{ route('employee.leaves.index') }}" class="px-3 py-1.5 rounded-md text-xs font-bold transition-all {{ ($status === 'all') ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-800' }}">All</a>
                    <a href="{{ route('employee.leaves.index', ['status' => 'pending']) }}" class="px-3 py-1.5 rounded-md text-xs font-bold transition-all {{ ($status === 'pending') ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-800' }}">Pending</a>
                    <a href="{{ route('employee.leaves.index', ['status' => 'approved']) }}" class="px-3 py-1.5 rounded-md text-xs font-bold transition-all {{ ($status === 'approved') ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-800' }}">Approved</a>
                    <a href="{{ route('employee.leaves.index', ['status' => 'rejected']) }}" class="px-3 py-1.5 rounded-md text-xs font-bold transition-all {{ ($status === 'rejected') ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-800' }}">Rejected</a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Leave Type</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Duration</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Status</th>
                            @if($leaves->where('status', 'pending')->count())
                            <th class="text-right px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaves as $leave)
                            @php $pill = in_array($leave->status, ['pending','approved','rejected'], true) ? $leave->status : 'neutral'; @endphp
                            <tr class="border-b border-gray-50 hover:bg-gray-50/50 cursor-pointer" onclick="window.location='{{ route('employee.leaves.show', $leave) }}'">
                                <td class="px-5 py-3">
                                    <div class="font-bold text-gray-900 text-sm">{{ $leave->leaveType?->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-400">Submitted {{ $leave->created_at?->diffForHumans() ?? '—' }}</div>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="text-xs text-gray-500">{{ $leave->start_date->format('M d, Y') }} – {{ $leave->end_date->format('M d, Y') }}</div>
                                    <div class="font-bold text-gray-900 font-mono text-sm">{{ $leave->days }} day{{ $leave->days != 1 ? 's' : '' }}</div>
                                </td>
                                <td class="px-5 py-3">
                                    @php
                                        $pillStyles = [
                                            'pending' => 'bg-amber-100 text-amber-700',
                                            'approved' => 'bg-emerald-100 text-emerald-700',
                                            'rejected' => 'bg-rose-100 text-rose-700',
                                            'neutral' => 'bg-gray-100 text-gray-600',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $pillStyles[$pill] }}">{{ ucfirst($leave->status) }}</span>
                                </td>
                                @if($leaves->where('status', 'pending')->count())
                                <td class="px-5 py-3 text-right" onclick="event.stopPropagation()">
                                    @if($leave->status === 'pending')
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('employee.leaves.edit', $leave) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-gray-600 bg-gray-50 hover:bg-gray-100 hover:text-gray-900 border border-gray-200 transition-all">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                Edit
                                            </a>
                                            <form action="{{ route('employee.leaves.destroy', $leave) }}" method="POST" style="display:inline;" data-sa-confirm="Cancel this leave request?">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition-all">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path stroke-linecap="round" stroke-linejoin="round" d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6m5 0V4a1 1 0 011-1h2a1 1 0 011 1v2"/></svg>
                                                    Cancel
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="flex flex-col items-center justify-center py-12 text-center">
                                        <svg class="w-10 h-10 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                        <p class="text-sm font-semibold text-gray-400">No leave requests found.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($leaves->hasPages())
                <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100">
                    <div class="text-xs text-gray-400">
                        Showing <strong class="text-gray-600">{{ $leaves->firstItem() }}</strong>–<strong class="text-gray-600">{{ $leaves->lastItem() }}</strong> of <strong class="text-gray-600">{{ $leaves->total() }}</strong>
                    </div>
                    {{ $leaves->links('pagination::tailwind') }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
