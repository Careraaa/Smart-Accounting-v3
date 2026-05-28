@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes scaleIn { 0%{opacity:0;transform:scale(0.93)} 100%{opacity:1;transform:scale(1)} }
.fade-up { animation:fadeUp 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.scale-in { animation:scaleIn 0.35s cubic-bezier(0.16,1,0.3,1) both; }
.stat-card:nth-child(1) { animation-delay:0.05s; }
.stat-card:nth-child(2) { animation-delay:0.1s; }
.stat-card:nth-child(3) { animation-delay:0.15s; }
.stat-card:nth-child(4) { animation-delay:0.2s; }
</style>
@endpush

@section('content')
@php
$sumCol = $remittances->sum('total_collection');
$sumExp = $remittances->sum('total_expenses');
$sumNet = $remittances->sum('net_remittance');
@endphp

{{-- Header --}}
    <div class="flex items-start justify-between mb-6 flex-wrap gap-4 fade-up">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Remittance Reports</h1>
            <p class="text-sm text-gray-500 mt-0.5">Approved daily remittances — collections, expenses, and net amounts across all routes.</p>
            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-gray-100 text-gray-500 text-[0.55rem] font-semibold mt-2">{{ $remittances->count() }} approved {{ Str::plural('record', $remittances->count()) }}</span>
        </div>
        <a href="{{ route('remittance-approval.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-gray-200 text-gray-700 text-xs font-bold rounded-xl hover:border-violet-300 hover:text-violet-600 hover:bg-violet-50 transition-all no-underline">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            Remittance approvals
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        <div class="scale-in stat-card bg-white rounded-xl border border-gray-100 p-3.5 shadow-sm hover:shadow-md hover:border-emerald-200 transition-all duration-300">
            <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Total Collection</p>
            <p class="text-lg font-bold text-emerald-600 tabular-nums mt-1">₱{{ number_format($sumCol, 0) }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Gross collections</p>
        </div>
        <div class="scale-in stat-card bg-white rounded-xl border border-gray-100 p-3.5 shadow-sm hover:shadow-md hover:border-red-200 transition-all duration-300">
            <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Total Expenses</p>
            <p class="text-lg font-bold text-red-600 tabular-nums mt-1">₱{{ number_format($sumExp, 0) }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Trip and operating costs</p>
        </div>
        <div class="scale-in stat-card bg-white rounded-xl border border-gray-100 p-3.5 shadow-sm hover:shadow-md hover:border-blue-200 transition-all duration-300">
            <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Net Remittance</p>
            <p class="text-lg font-bold text-blue-600 tabular-nums mt-1">₱{{ number_format($sumNet, 0) }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Collection minus expenses</p>
        </div>
        <div class="scale-in stat-card bg-white rounded-xl border border-gray-100 p-3.5 shadow-sm hover:shadow-md hover:border-gray-300 transition-all duration-300">
            <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Records</p>
            <p class="text-lg font-bold text-gray-900 tabular-nums mt-1">{{ $remittances->count() }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Approved remittances</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="fade-up bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-50 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            <span class="text-sm font-semibold text-gray-900">All approved remittances</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-50 bg-gray-50/50">
                        <th class="text-left px-5 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Date</th>
                        <th class="text-left px-4 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Route</th>
                        <th class="text-right px-3 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Collection</th>
                        <th class="text-right px-3 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Expenses</th>
                        <th class="text-right px-5 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Net Remittance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($remittances as $remittance)
                        <tr class="transition-colors hover:bg-gray-50/50">
                            <td class="px-5 py-3.5 text-xs text-gray-600">{{ $remittance->remittance_date?->format('M d, Y') ?? '—' }}</td>
                            <td class="px-4 py-3.5 text-xs font-semibold text-gray-900">{{ $remittance->route->route_name ?? '—' }}</td>
                            <td class="px-3 py-3.5 text-right text-xs tabular-nums text-gray-600">₱{{ number_format($remittance->total_collection, 0) }}</td>
                            <td class="px-3 py-3.5 text-right text-xs tabular-nums text-gray-400">₱{{ number_format($remittance->total_expenses, 0) }}</td>
                            <td class="px-5 py-3.5 text-right text-xs font-bold tabular-nums text-emerald-600">₱{{ number_format($remittance->net_remittance, 0) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-12">
                                <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                <p class="text-xs text-gray-400">No approved remittance records yet.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>@endsection