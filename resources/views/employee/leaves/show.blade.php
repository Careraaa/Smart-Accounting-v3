@extends('layouts.layout')

@push('styles')
<style>
@keyframes ls-show-in { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
.ls-show { animation:ls-show-in 0.35s ease-out; }
</style>
@endpush

@section('content')
@php
    $pillStyles = [
        'pending' => 'bg-amber-100 text-amber-700',
        'approved' => 'bg-emerald-100 text-emerald-700',
        'rejected' => 'bg-rose-100 text-rose-700',
        'neutral' => 'bg-gray-100 text-gray-600',
    ];
    $pill = in_array($leave->status, ['pending','approved','rejected'], true) ? $leave->status : 'neutral';
@endphp

<div class="ls-show min-h-screen bg-gray-50/60">
    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 py-8">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-7">
            <div>
                <p class="text-xs font-semibold tracking-widest text-gray-400 uppercase mb-1">Employee Portal</p>
                <h1 class="text-2xl font-extrabold tracking-tight text-gray-900 leading-tight">Leave Request Details</h1>
                <p class="text-sm text-gray-500 mt-1">{{ $leave->leaveType?->name ?? 'N/A' }} · {{ $leave->start_date->format('M d, Y') }} – {{ $leave->end_date->format('M d, Y') }}</p>
                <div class="flex flex-wrap gap-2 mt-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-gray-200 text-xs font-semibold text-gray-600 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
                        Submitted {{ $leave->created_at->diffForHumans() }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-gray-200 text-xs font-semibold text-gray-600 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                        Status: {{ ucfirst($leave->status) }}
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                @if($leave->status === 'pending')
                    <a href="{{ route('employee.leaves.edit', $leave) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white text-sm font-bold rounded-xl transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit Request
                    </a>
                @endif
                <a href="{{ route('employee.leaves.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-bold rounded-xl transition-all shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5m7-7-7 7 7 7"/></svg>
                    Back
                </a>
            </div>
        </div>

        {{-- Content grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Main: Summary --}}
            <div class="lg:col-span-2">
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <span class="w-2 h-2 rounded-full bg-gray-900"></span>
                            <span class="text-sm font-bold text-gray-800">Summary</span>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $pillStyles[$pill] }}">{{ ucfirst($leave->status) }}</span>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Leave Type</div>
                                <div class="font-bold text-gray-900 mt-0.5">{{ $leave->leaveType?->name ?? 'N/A' }}</div>
                            </div>
                            <div>
                                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Submitted On</div>
                                <div class="font-bold text-gray-900 mt-0.5">{{ $leave->created_at->format('F d, Y \a\t h:i A') }}</div>
                            </div>
                            <div>
                                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Start Date</div>
                                <div class="font-bold text-gray-900 mt-0.5">{{ $leave->start_date->format('l, F d, Y') }}</div>
                            </div>
                            <div>
                                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">End Date</div>
                                <div class="font-bold text-gray-900 mt-0.5">{{ $leave->end_date->format('l, F d, Y') }}</div>
                            </div>
                            <div>
                                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Duration</div>
                                <div class="font-bold text-gray-900 mt-0.5">{{ $leave->days }} day(s)</div>
                            </div>
                        </div>

                        <div class="border-t border-gray-100 my-5"></div>

                        <div>
                            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1.5">Reason</div>
                            <div class="font-bold text-gray-900 text-sm leading-relaxed whitespace-pre-wrap">{{ $leave->reason }}</div>
                        </div>

                        @if($leave->status === 'approved' && $leave->approvedBy)
                            <div class="mt-5 p-4 rounded-xl bg-emerald-50 border border-emerald-200">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-emerald-600 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <div>
                                        <strong class="text-sm text-emerald-800">Approved</strong>
                                        <span class="text-sm text-emerald-700">by {{ $leave->approvedBy->name }} on {{ $leave->updated_at->format('F d, Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        @elseif($leave->status === 'rejected')
                            <div class="mt-5 p-4 rounded-xl bg-rose-50 border border-rose-200">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-rose-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <div>
                                        <strong class="text-sm text-rose-800">Rejected</strong>
                                        @if($leave->approvedBy)
                                            <span class="text-sm text-rose-700">by {{ $leave->approvedBy->name }} on {{ $leave->updated_at->format('F d, Y') }}</span>
                                        @endif
                                        @if($leave->rejection_reason)
                                            <div class="mt-2 text-sm text-rose-700 font-semibold">{{ $leave->rejection_reason }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Sidebar: Actions --}}
            <div>
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100">
                        <span class="w-2 h-2 rounded-full bg-gray-900"></span>
                        <span class="text-sm font-bold text-gray-800">Actions</span>
                    </div>
                    <div class="p-5 flex flex-col gap-2.5">
                        @if($leave->status === 'pending')
                            <a href="{{ route('employee.leaves.edit', $leave) }}" class="inline-flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-bold rounded-xl transition-all">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Edit Request
                            </a>
                            <form action="{{ route('employee.leaves.destroy', $leave) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center justify-center gap-2 w-full px-4 py-2.5 border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 text-sm font-bold rounded-xl transition-all" onclick="return confirm('Cancel this leave request?')">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path stroke-linecap="round" stroke-linejoin="round" d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6m5 0V4a1 1 0 011-1h2a1 1 0 011 1v2"/></svg>
                                    Cancel Request
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('employee.leaves.index') }}" class="inline-flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-gray-900 hover:bg-gray-800 text-white text-sm font-bold rounded-xl transition-all shadow-sm">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                            View All Requests
                        </a>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
