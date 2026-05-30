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
.stat-card { animation: scaleIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) both; }
.stat-card:nth-child(1) { animation-delay: 0.05s; }
.stat-card:nth-child(2) { animation-delay: 0.1s; }
.fade-up { animation: fadeSlideUp 0.45s cubic-bezier(0.16, 1, 0.3, 1) both; }
.filter-bar { animation: slideInRight 0.4s cubic-bezier(0.16, 1, 0.3, 1) 0.1s both; }
.table-wrap { animation: fadeSlideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) 0.15s both; }
</style>
@endpush

@section('content')
<div class="max-w-full" data-ls>

    <div class="flex items-start justify-between gap-4 mb-6 flex-wrap fade-up">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight m-0">{{ $leaveType->name }}</h1>
            <p class="text-xs text-gray-400 m-0 mt-0.5">
                Leave Type ·
                @if($leaveType->abbreviation)
                    <span class="font-mono">{{ $leaveType->abbreviation }}</span> ·
                @endif
                Created {{ $leaveType->created_at->format('M d, Y') }}
            </p>
        </div>
        <a href="{{ route('leave-type.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl text-xs font-semibold no-underline hover:border-gray-600 hover:text-gray-600 hover:bg-gray-50 transition-colors whitespace-nowrap cursor-pointer">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back
        </a>
    </div>

    <div class="grid grid-cols-1 gap-4">

        {{-- Details card --}}
        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden table-wrap">
            <div class="px-6 py-5 border-b border-gray-50 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gray-100 text-gray-400 flex items-center justify-center shrink-0">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-extrabold text-gray-900 m-0 leading-tight">Leave Type Details</p>
                    <p class="text-xs text-gray-400 m-0">Policy and configuration</p>
                </div>
                <div class="ml-auto">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                        {{ $leaveType->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-gray-50 text-gray-500 border border-gray-200' }}">
                        <span class="w-1.5 h-1.5 rounded-full
                            {{ $leaveType->status === 'active' ? 'bg-emerald-600' : 'bg-gray-400' }}">
                        </span>
                        {{ ucfirst($leaveType->status) }}
                    </span>
                </div>
            </div>
            <div class="p-6">

                <div class="text-[0.68rem] font-bold uppercase tracking-[0.12em] text-gray-400 border-b border-gray-50 pb-2.5 mb-5">General</div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                    <div>
                        <span class="block text-[0.7rem] font-bold uppercase tracking-wide text-gray-400 mb-1.5">Leave Type Name</span>
                        <div class="bg-gray-50/50 border border-gray-200 rounded-lg px-3.5 py-2.5"><span class="text-sm text-gray-900 font-medium">{{ $leaveType->name }}</span></div>
                    </div>
                    <div>
                        <span class="block text-[0.7rem] font-bold uppercase tracking-wide text-gray-400 mb-1.5">Abbreviation</span>
                        <div class="bg-gray-50/50 border border-gray-200 rounded-lg px-3.5 py-2.5 font-mono text-xs"><span class="text-sm text-gray-900 font-medium">{{ $leaveType->abbreviation ?? '—' }}</span></div>
                    </div>
                </div>

                <div class="text-[0.68rem] font-bold uppercase tracking-[0.12em] text-gray-400 border-b border-gray-50 pb-2.5 mb-5">Policy</div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                    <div>
                        <span class="block text-[0.7rem] font-bold uppercase tracking-wide text-gray-400 mb-1.5">Days Allowed</span>
                        <div class="bg-gray-50/50 border border-gray-200 rounded-lg px-3.5 py-2.5"><span class="text-sm text-gray-900 font-medium">{{ $leaveType->days_allowed }} day(s)</span></div>
                    </div>
                    <div>
                        <span class="block text-[0.7rem] font-bold uppercase tracking-wide text-gray-400 mb-1.5">Carry Over</span>
                        <div class="bg-gray-50/50 border border-gray-200 rounded-lg px-3.5 py-2.5">
                            @if($leaveType->carry_over)
                                <span class="inline-flex items-center gap-1.5 text-sm font-medium text-emerald-600">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    Allowed
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-400">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Not allowed
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                @if($leaveType->description)
                <div class="text-[0.68rem] font-bold uppercase tracking-[0.12em] text-gray-400 border-b border-gray-50 pb-2.5 mb-5">Description</div>
                <div class="mb-0">
                    <div class="bg-gray-50/50 border border-gray-200 rounded-lg px-3.5 py-2.5">
                        <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap m-0">{{ $leaveType->description }}</p>
                    </div>
                </div>
                @endif

            </div>

            <div class="px-6 py-4 border-t border-gray-50 bg-gray-50/50 flex items-center gap-2.5">
                <a href="{{ route('leave-type.edit', $leaveType) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 text-white border-none rounded-xl text-xs font-bold cursor-pointer hover:bg-black transition-colors no-underline">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit Leave Type
                </a>
                <form action="{{ route('leave-type.destroy', $leaveType) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this leave type?')" class="inline">
                    @csrf @method('DELETE')
                    <button class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-red-600 border border-red-200 rounded-xl text-xs font-bold cursor-pointer hover:bg-red-50 hover:border-red-300 transition-colors">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Delete
                    </button>
                </form>
            </div>
        </div>

    </div>

</div>
@endsection
