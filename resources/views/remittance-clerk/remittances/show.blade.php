@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
.fade-up { animation:fadeUp 0.4s cubic-bezier(0.16,1,0.3,1) both; }
</style>
@endpush

@section('content')
@php
    $rlabel = ucfirst($remittance->status ?? 'pending');
@endphp

<div class="max-w-5xl mx-auto px-4 sm:px-6 py-6 space-y-5">
    <div class="flex items-center justify-between gap-3 flex-wrap fade-up">
        <a href="{{ route('remittances.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold border border-gray-200 text-gray-600 bg-white hover:bg-gray-50 transition-colors no-underline whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Remittances
        </a>
        <div class="flex items-center gap-2">
            @if($remittance->status !== 'approved')
                <a href="{{ route('remittances.edit', $remittance) }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-white bg-gray-900 hover:bg-gray-800 transition-all no-underline whitespace-nowrap">Edit Remittance</a>
            @endif
            @if(!in_array($remittance->status, ['approved', 'rejected'], true))
                <form action="{{ route('remittances.destroy', $remittance) }}" method="POST" class="inline" data-sa-confirm="Delete this remittance?">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-red-600 bg-white border border-red-200 hover:bg-red-50 transition-all cursor-pointer whitespace-nowrap">Delete</button>
                </form>
            @endif
        </div>
    </div>

    <header class="flex items-center justify-between gap-4 fade-up">
        <div class="flex items-start gap-3 min-w-0">
            <div class="w-11 h-11 rounded-xl bg-gray-900 text-white flex items-center justify-center text-base font-bold shrink-0">
                {{ strtoupper(substr($remittance->route->route_name ?? 'R', 0, 1)) }}
            </div>
            <div class="min-w-0">
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight m-0">{{ $remittance->remittance_date?->format('F d, Y') ?? 'Remittance' }}</h1>
                <p class="text-sm text-gray-500 mt-1 mb-0">{{ $remittance->route->route_name ?? '—' }}</p>
            </div>
        </div>
        <span class="app-status-badge shrink-0 {{ in_array($remittance->status, ['pending', 'approved', 'rejected'], true) ? $remittance->status : 'neutral' }}">{{ $rlabel }}</span>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <section class="h-full bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden fade-up">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h2 class="text-sm font-bold text-gray-900 m-0">Personnel &amp; Vehicle</h2>
            </div>
            <dl class="px-5 divide-y divide-gray-100">
                <div class="flex items-center justify-between gap-4 py-3">
                    <dt class="text-xs font-medium text-gray-500">Driver</dt>
                    <dd class="text-xs font-semibold text-gray-900 text-right m-0">{{ $remittance->driver->name ?? '—' }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 py-3">
                    <dt class="text-xs font-medium text-gray-500">PAO</dt>
                    <dd class="text-xs font-semibold text-gray-900 text-right m-0">{{ $remittance->pao->name ?? '—' }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 py-3">
                    <dt class="text-xs font-medium text-gray-500">Vehicle</dt>
                    <dd class="text-xs font-semibold text-gray-900 text-right m-0">{{ $remittance->vehicle->plate_number ?? '—' }}</dd>
                </div>
            </dl>
        </section>

        <section class="h-full bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden fade-up">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h2 class="text-sm font-bold text-gray-900 m-0">Financial Summary</h2>
            </div>
            <dl class="px-5 divide-y divide-gray-100">
                <div class="flex items-center justify-between gap-4 py-3">
                    <dt class="text-xs font-medium text-gray-500">Total Collection</dt>
                    <dd class="text-xs font-bold text-gray-900 tabular-nums m-0">₱{{ number_format($remittance->total_collection, 2) }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 py-3">
                    <dt class="text-xs font-medium text-gray-500">Boundary Rate</dt>
                    <dd class="text-xs font-semibold text-gray-900 tabular-nums m-0">₱{{ number_format($remittance->boundary ?? 0, 2) }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 py-3">
                    <dt class="text-xs font-medium text-gray-500">Total Expenses</dt>
                    <dd class="text-xs font-semibold text-gray-900 tabular-nums m-0">₱{{ number_format($remittance->total_expenses, 2) }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 py-3">
                    <dt class="text-xs font-bold text-gray-900">Net Remittance</dt>
                    <dd class="text-sm font-bold {{ $remittance->is_short_remittance ? 'text-red-600' : 'text-emerald-600' }} tabular-nums m-0">₱{{ number_format($remittance->net_remittance, 2) }}</dd>
                </div>
            </dl>
        </section>
    </div>

    <section class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden fade-up">
        <div class="px-5 py-3.5 border-b border-gray-100 flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14l2-2 2 2 4-4m-9 8h10M5 4h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z"/></svg>
            </div>
            <h2 class="text-sm font-bold text-gray-900 m-0">Expense Breakdown</h2>
        </div>
        <dl class="p-5 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
            @foreach ([['Diesel', 'diesel'], ['Parking', 'parking'], ['Dispatcher', 'dispatcher'], ['Food Allowance', 'food_allowance'], ['Barker', 'barker'], ['Others', 'others']] as [$label, $field])
                <div class="flex items-center justify-between gap-3 rounded-lg bg-gray-50 px-4 py-3">
                    <dt class="text-xs font-medium text-gray-500">{{ $label }}</dt>
                    <dd class="text-xs font-semibold text-gray-900 tabular-nums m-0">₱{{ number_format((float) ($remittance->{$field} ?? 0), 2) }}</dd>
                </div>
            @endforeach
        </dl>
    </section>
</div>
@endsection
