@extends('layouts.layout')

@section('content')
@php
    $pill = in_array($overtimeUndertime->status, ['pending','approved','rejected'], true) ? $overtimeUndertime->status : 'neutral';
    $pillClasses = match($pill) {
        'pending'  => 'bg-amber-50 text-amber-700 border border-amber-200',
        'approved' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
        'rejected' => 'bg-rose-50 text-rose-700 border border-rose-200',
        default    => 'bg-gray-100 text-gray-600 border border-gray-200',
    };
    $typeClasses = $overtimeUndertime->type === 'overtime'
        ? 'bg-blue-50 text-blue-700 border border-blue-200'
        : 'bg-yellow-50 text-yellow-700 border border-yellow-200';
@endphp

<div class="min-h-screen bg-gray-50/60">
    <div class="max-w-screen-lg mx-auto px-4 sm:px-6 py-8">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-7">
            <div>
                <p class="text-xs font-semibold tracking-widest text-gray-400 uppercase mb-1">Employee Portal</p>
                <h1 class="text-2xl font-extrabold tracking-tight text-gray-900 leading-tight">OT / UT Request Details</h1>
                <p class="text-sm text-gray-500 mt-1">
                    {{ ucfirst($overtimeUndertime->type) }} · {{ $overtimeUndertime->date->format('M d, Y') }} · {{ number_format($overtimeUndertime->hours, 2) }}h
                </p>
                <div class="flex items-center gap-2 mt-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-gray-200 text-xs font-semibold text-gray-600 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                        {{ $overtimeUndertime->date->format('l') }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-gray-200 text-xs font-semibold text-gray-600 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>
                        Status: {{ ucfirst($overtimeUndertime->status) }}
                    </span>
                </div>
            </div>
            <a href="{{ route('employee.overtime-undertime.index') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl text-xs font-semibold no-underline hover:border-gray-600 hover:text-gray-600 hover:bg-gray-50 transition-colors whitespace-nowrap cursor-pointer">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- Main summary card --}}
            <div class="lg:col-span-2">
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-gray-900"></div>
                            <span class="text-sm font-bold text-gray-900">Summary</span>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $pillClasses }}">
                            {{ ucfirst($overtimeUndertime->status) }}
                        </span>
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-2 gap-x-8 gap-y-5">
                            <div>
                                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Request Type</div>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $typeClasses }}">
                                    {{ ucfirst($overtimeUndertime->type) }}
                                </span>
                            </div>
                            <div>
                                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Date</div>
                                <div class="text-sm font-bold text-gray-900">{{ $overtimeUndertime->date->format('l, F d, Y') }}</div>
                            </div>
                            <div>
                                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Hours</div>
                                <div class="text-sm font-bold font-mono text-gray-900">{{ number_format($overtimeUndertime->hours, 2) }} hours</div>
                            </div>
                            <div>
                                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Hourly Rate</div>
                                <div class="text-sm font-bold font-mono text-gray-900">₱ {{ number_format($overtimeUndertime->hourly_rate_used, 2) }}</div>
                            </div>
                            <div>
                                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Amount</div>
                                <div class="text-sm font-bold font-mono">
                                    @if($overtimeUndertime->type === 'overtime')
                                        <span class="text-emerald-600">+₱ {{ number_format($overtimeUndertime->amount, 2) }}</span>
                                    @else
                                        <span class="text-rose-600">-₱ {{ number_format(abs($overtimeUndertime->amount), 2) }}</span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Submitted On</div>
                                <div class="text-sm font-bold text-gray-900">{{ $overtimeUndertime->created_at->format('M d, Y — g:i A') }}</div>
                            </div>
                            @if($overtimeUndertime->updated_at->diffInSeconds($overtimeUndertime->created_at) > 1)
                            <div class="col-span-2">
                                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Last Updated</div>
                                <div class="text-sm font-bold text-gray-900">{{ $overtimeUndertime->updated_at->format('l, F d, Y — g:i A') }}</div>
                            </div>
                            @endif
                        </div>

                        <div class="border-t border-gray-100 my-5"></div>

                        <div>
                            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Reason</div>
                            <div class="text-sm text-gray-900 font-semibold leading-relaxed whitespace-pre-wrap">{{ $overtimeUndertime->reason }}</div>
                        </div>

                        <div class="border-t border-gray-100 my-5"></div>

                        <div>
                            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Submitted By</div>
                            <div class="text-sm font-bold text-gray-900">
                                {{ $overtimeUndertime->employee->first_name }} {{ $overtimeUndertime->employee->last_name }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions sidebar --}}
            <div class="lg:col-span-1">
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
                    <div class="flex items-center gap-2 px-5 py-4 border-b border-gray-100">
                        <div class="w-2 h-2 rounded-full bg-gray-900"></div>
                        <span class="text-sm font-bold text-gray-900">Actions</span>
                    </div>
                    <div class="p-5 flex flex-col gap-2.5">
                        @if($overtimeUndertime->status === 'pending')
                            <a href="{{ route('employee.overtime-undertime.edit', $overtimeUndertime->id) }}"
                               class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-900 hover:bg-gray-800 text-white text-sm font-semibold rounded-xl transition-all shadow-sm">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Edit Request
                            </a>
                            <form action="{{ route('employee.overtime-undertime.destroy', $overtimeUndertime->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-rose-50 hover:bg-rose-100 border border-rose-200 text-rose-700 text-sm font-semibold rounded-xl transition-all"
                                    onclick="return confirm('Are you sure you want to delete this request? This action cannot be undone.')">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path stroke-linecap="round" stroke-linejoin="round" d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6m5 0V4a1 1 0 011-1h2a1 1 0 011 1v2"/></svg>
                                    Delete Request
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('employee.overtime-undertime.index') }}"
                           class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl text-xs font-semibold no-underline hover:border-gray-600 hover:text-gray-600 hover:bg-gray-50 transition-colors whitespace-nowrap cursor-pointer">
                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            Back to List
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
