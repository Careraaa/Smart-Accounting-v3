@extends('layouts.layout')
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-8 space-y-5">
    {{-- Back --}}
    <div>
        <a href="{{ route('paos.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl text-xs font-semibold no-underline hover:border-gray-600 hover:text-gray-600 hover:bg-gray-50 transition-colors whitespace-nowrap cursor-pointer">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to PAOs
        </a>
    </div>

    {{-- Header Card --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-start justify-between flex-wrap gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">PAO / Conductor profile</p>
                <h1 class="text-xl font-bold text-gray-900">{{ $pao->name }}</h1>

            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('paos.edit', $pao) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white transition-all no-underline">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit
            </a>
            <button type="button" onclick="if(confirm('Delete PAO &quot;{{ $pao->name }}&quot;? This cannot be undone.')) document.getElementById('deleteForm').submit();" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-red-50 text-red-600 hover:bg-red-500 hover:text-white transition-all border-0 cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Delete
            </button>
            <form id="deleteForm" action="{{ route('paos.destroy', $pao) }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
        </div>
    </div>

    {{-- Stat chips --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Date of Hire</p>
                <p class="text-sm font-bold text-gray-900">{{ $pao->date_of_hire?->format('M d, Y') ?? '—' }}</p>
            </div>
        </div>
    </div>

    {{-- PAO Information Card --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-50 flex items-center gap-2.5">
            <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <span class="text-xs font-bold text-gray-700">PAO Information</span>
        </div>
        <div class="p-5">
            <div class="flex items-center justify-between py-3 border-b border-gray-50">
                <span class="text-xs font-semibold text-gray-500">Full Name</span>
                <span class="text-xs text-gray-700 text-right max-w-[60%]">{{ $pao->name ?? '—' }}</span>
            </div>
            <div class="flex items-center justify-between py-3 border-b border-gray-50">
                <span class="text-xs font-semibold text-gray-500">Contact Number</span>
                <span class="text-xs text-gray-700 text-right max-w-[60%]">{{ $pao->contact_number ?? '—' }}</span>
            </div>
            <div class="flex items-center justify-between py-3 border-b border-gray-50">
                <span class="text-xs font-semibold text-gray-500">Email</span>
                <span class="text-xs text-gray-700 text-right max-w-[60%]">{{ $pao->email ?? '—' }}</span>
            </div>
            <div class="flex items-center justify-between py-3 border-b border-gray-50">
                <span class="text-xs font-semibold text-gray-500">Gender</span>
                <span class="text-xs text-gray-700 text-right max-w-[60%]">{{ ucfirst(str_replace('_', ' ', $pao->gender ?? '—')) }}</span>
            </div>
            <div class="flex items-center justify-between py-3 border-b border-gray-50">
                <span class="text-xs font-semibold text-gray-500">Date of Hire</span>
                <span class="text-xs text-gray-700 text-right max-w-[60%]">{{ $pao->date_of_hire?->format('F d, Y') ?? '—' }}</span>
            </div>
            @if($pao->address)
            <div class="flex items-center justify-between py-3 border-b border-gray-50">
                <span class="text-xs font-semibold text-gray-500">Address</span>
                <span class="text-xs text-gray-700 text-right max-w-[60%]">{{ $pao->address }}</span>
            </div>
            @endif
            <div class="flex items-center justify-between py-3">
                <span class="text-xs font-semibold text-gray-500">Status</span>
                <span class="text-xs text-gray-700 text-right">
                    @php $sc = match($pao->status) { 'active' => 'bg-emerald-500/10 text-emerald-600 border-emerald-200/30', 'pending' => 'bg-amber-500/10 text-amber-600 border-amber-200/30', default => 'bg-red-500/10 text-red-600 border-red-200/30' }; @endphp
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold border {{ $sc }}">{{ ucfirst($pao->status) }}</span>
                </span>
            </div>
        </div>
    </div>
</div>
@endsection
