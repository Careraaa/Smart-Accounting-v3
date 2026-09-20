@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes scaleIn { 0%{opacity:0;transform:scale(0.93)} 100%{opacity:1;transform:scale(1)} }
.fade-up { animation:fadeUp 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.scale-in { animation:scaleIn 0.35s cubic-bezier(0.16,1,0.3,1) both; }
</style>
@endpush

@section('content')
@php
    $rsc = match($remittance->status) { 'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'pending' => 'bg-amber-50 text-amber-700 border-amber-200', default => 'bg-red-50 text-red-700 border-red-200' };
    $rlabel = ucfirst($remittance->status ?? 'pending');
    $initial = strtoupper(substr($remittance->route->route_name ?? 'R', 0, 1));
@endphp

{{-- Header --}}
    <div class="flex items-start justify-between gap-4 mb-6 flex-wrap fade-up">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-gray-900 text-white flex items-center justify-center text-lg font-bold shrink-0">{{ $initial }}</div>
            <div>
                <div class="flex items-center gap-2.5 mb-0.5">
                    <h1 class="text-2xl font-bold text-gray-900 tracking-tight m-0">{{ $remittance->remittance_date?->format('F d, Y') ?? 'Remittance' }}</h1>

                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[0.55rem] font-bold border {{ $rsc }}">
                        <span class="w-1.5 h-1.5 rounded-full
                            {{ $remittance->status === 'approved' ? 'bg-emerald-600' : '' }}
                            {{ $remittance->status === 'pending' ? 'bg-amber-500' : '' }}
                            {{ $remittance->status === 'rejected' ? 'bg-red-600' : '' }}">
                        </span>
                        {{ $rlabel }}
                    </span>
                </div>
                <p class="text-sm text-gray-500 m-0">{{ $remittance->route->route_name ?? '—' }}</p>
            </div>
        </div>
        <a href="{{ route('remittances.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-bold border border-gray-200 text-gray-600 bg-white hover:bg-gray-50 transition-all no-underline">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back
        </a>
    </div>

    {{-- Stat Chips --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6 fade-up">
        <div class="bg-white rounded-xl border border-gray-100 p-3.5 shadow-sm flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Vehicle</p>
                <p class="text-sm font-bold text-gray-900">{{ $remittance->vehicle->plate_number ?? '—' }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-3.5 shadow-sm flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-500 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div>
                <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Driver</p>
                <p class="text-sm font-bold text-gray-900">{{ $remittance->driver->name ?? '—' }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-3.5 shadow-sm flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-violet-50 text-violet-500 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">PAO</p>
                <p class="text-sm font-bold text-gray-900">{{ $remittance->pao->name ?? '—' }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- Remittance Details Card --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden scale-in">
            <div class="px-5 py-4 border-b border-gray-50 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <p class="text-sm font-bold text-gray-900 m-0">Remittance Details</p>
            </div>
            <div class="p-5 space-y-3.5">
                <div class="flex items-center justify-between">
                    <span class="text-[0.65rem] font-bold uppercase tracking-wide text-gray-400">Date</span>
                    <span class="text-xs font-semibold text-gray-900">{{ $remittance->remittance_date?->format('F d, Y') ?? '—' }}</span>
                </div>
                <div class="border-t border-gray-50"></div>
                <div class="flex items-center justify-between">
                    <span class="text-[0.65rem] font-bold uppercase tracking-wide text-gray-400">Driver</span>
                    <span class="text-xs font-semibold text-gray-900">{{ $remittance->driver->name ?? '—' }}</span>
                </div>
                <div class="border-t border-gray-50"></div>
                <div class="flex items-center justify-between">
                    <span class="text-[0.65rem] font-bold uppercase tracking-wide text-gray-400">PAO</span>
                    <span class="text-xs font-semibold text-gray-900">{{ $remittance->pao->name ?? '—' }}</span>
                </div>
                <div class="border-t border-gray-50"></div>
                <div class="flex items-center justify-between">
                    <span class="text-[0.65rem] font-bold uppercase tracking-wide text-gray-400">Vehicle</span>
                    <span class="text-xs font-semibold text-gray-900">{{ $remittance->vehicle->plate_number ?? '—' }}</span>
                </div>
                <div class="border-t border-gray-50"></div>
                <div class="flex items-center justify-between">
                    <span class="text-[0.65rem] font-bold uppercase tracking-wide text-gray-400">Route</span>
                    <span class="text-xs font-semibold text-gray-900">{{ $remittance->route->route_name ?? '—' }}</span>
                </div>
                <div class="border-t border-gray-50"></div>
                <div class="flex items-center justify-between">
                    <span class="text-[0.65rem] font-bold uppercase tracking-wide text-gray-400">Status</span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[0.55rem] font-bold border {{ $rsc }}">
                        <span class="w-1.5 h-1.5 rounded-full
                            {{ $remittance->status === 'approved' ? 'bg-emerald-600' : '' }}
                            {{ $remittance->status === 'pending' ? 'bg-amber-500' : '' }}
                            {{ $remittance->status === 'rejected' ? 'bg-red-600' : '' }}">
                        </span>
                        {{ $rlabel }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Financial Summary Card --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden scale-in">
            <div class="px-5 py-4 border-b border-gray-50 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-500 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-sm font-bold text-gray-900 m-0">Financial Summary</p>
            </div>
            <div class="p-5 space-y-3.5">
                <div class="flex items-center justify-between">
                    <span class="text-[0.65rem] font-bold uppercase tracking-wide text-gray-400">Total Collection</span>
                    <span class="text-xs font-bold text-emerald-600">₱{{ number_format($remittance->total_collection, 2) }}</span>
                </div>
                <div class="border-t border-gray-50"></div>
                <div class="flex items-center justify-between">
                    <span class="text-[0.65rem] font-bold uppercase tracking-wide text-gray-400">Total Expenses</span>
                    <span class="text-xs font-bold text-red-600">₱{{ number_format($remittance->total_expenses, 2) }}</span>
                </div>
                <div class="border-t border-gray-50"></div>
                <div class="flex items-center justify-between">
                    <span class="text-[0.65rem] font-bold uppercase tracking-wide text-gray-400">Net Remittance</span>
                    <span class="text-xs font-bold text-gray-900">₱{{ number_format($remittance->net_remittance, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Expense Breakdown --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden scale-in">
            <div class="px-5 py-4 border-b border-gray-50 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-red-50 text-red-500 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14l2-2 2 2 4-4m-9 8h10M5 4h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z"/></svg>
                </div>
                <p class="text-sm font-bold text-gray-900 m-0">Expense Breakdown</p>
            </div>
            <div class="p-5 grid grid-cols-2 gap-x-6 gap-y-3">
                @foreach ([['Diesel', 'diesel'], ['Parking', 'parking'], ['Dispatcher', 'dispatcher'], ['Food Allowance', 'food_allowance'], ['Barker', 'barker'], ['Others', 'others']] as [$label, $field])
                    <div class="flex items-center justify-between border-b border-gray-50 pb-2">
                        <span class="text-[0.65rem] font-bold uppercase tracking-wide text-gray-400">{{ $label }}</span>
                        <span class="text-xs font-semibold text-gray-700">₱{{ number_format((float) ($remittance->{$field} ?? 0), 2) }}</span>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    {{-- Net Box --}}
    <div class="mt-4 bg-gray-900 rounded-xl px-6 py-5 flex items-center justify-between fade-up">
        <span class="text-[0.65rem] font-bold uppercase tracking-wider text-gray-400">Net Remittance</span>
        <span class="text-xl font-bold text-white tabular-nums">₱{{ number_format($remittance->net_remittance, 2) }}</span>
    </div>

    {{-- Footer Actions --}}
    <div class="mt-6 flex items-center gap-3 flex-wrap fade-up">
        <a href="{{ route('remittances.edit', $remittance) }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-gray-900 transition-all hover:bg-gray-800 active:scale-[0.97] no-underline">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Edit Remittance
        </a>
        <form action="{{ route('remittances.destroy', $remittance) }}" method="POST" class="inline"
            data-sa-confirm="Delete this remittance?">
            @csrf @method('DELETE')
            <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-bold text-red-600 bg-white border border-red-200 transition-all hover:bg-red-50 hover:border-red-300 active:scale-[0.97] cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Delete
            </button>
        </form>
    </div>@endsection
