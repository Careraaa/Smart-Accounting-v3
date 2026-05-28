@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes scaleIn { 0%{opacity:0;transform:scale(0.93)} 100%{opacity:1;transform:scale(1)} }
@keyframes slideRight { 0%{opacity:0;transform:translateX(-10px)} 100%{opacity:1;transform:translateX(0)} }
.fade-up { animation:fadeUp 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.scale-in { animation:scaleIn 0.35s cubic-bezier(0.16,1,0.3,1) both; }
.slide-right { animation:slideRight 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.stat-card:nth-child(1) { animation-delay:0.05s; }
.stat-card:nth-child(2) { animation-delay:0.1s; }
.stat-card:nth-child(3) { animation-delay:0.15s; }
.stat-card:nth-child(4) { animation-delay:0.2s; }
</style>
@endpush

@section('content')
@php
$totalPending  = $pendingCount;
$historyBatches = collect($historyData);
$histTab = request()->query('hist', 'approved');
@endphp

{{-- Header --}}
    <div class="flex items-start justify-between mb-6 flex-wrap gap-4 fade-up">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Payroll Approval</h1>
            <p class="text-sm text-gray-500 mt-0.5">Review and approve payroll batches submitted by HR</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-amber-50 text-amber-700 text-[0.6rem] font-semibold border border-amber-200">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                {{ $totalPending }} pending
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-500/15 border border-emerald-500/25 rounded-lg text-emerald-700 text-xs font-semibold mb-5">
            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @elseif(session('error'))
        <div class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-500/15 border border-red-500/25 rounded-lg text-red-700 text-xs font-semibold mb-5">
            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        <div class="scale-in stat-card bg-white rounded-xl border border-gray-100 p-3.5 shadow-sm hover:shadow-md hover:border-amber-200 transition-all duration-300">
            <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Pending Batches</p>
            <p class="text-xl font-bold text-amber-600 tabular-nums mt-1">{{ $totalPending }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Awaiting review</p>
        </div>
        <div class="scale-in stat-card bg-white rounded-xl border border-gray-100 p-3.5 shadow-sm hover:shadow-md hover:border-blue-200 transition-all duration-300">
            <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Employees</p>
            <p class="text-xl font-bold text-gray-900 tabular-nums mt-1">{{ $totalEmpInPending }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">In pending batches</p>
        </div>
        <div class="scale-in stat-card bg-white rounded-xl border border-gray-100 p-3.5 shadow-sm hover:shadow-md hover:border-emerald-200 transition-all duration-300">
            <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Total Gross</p>
            <p class="text-lg font-bold text-emerald-600 tabular-nums mt-1">₱{{ number_format($totalGrossAll, 0) }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Across pending</p>
        </div>
        <div class="scale-in stat-card bg-white rounded-xl border border-gray-100 p-3.5 shadow-sm hover:shadow-md hover:border-indigo-200 transition-all duration-300">
            <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Total Net</p>
            <p class="text-lg font-bold text-indigo-600 tabular-nums mt-1">₱{{ number_format($totalNetAll, 0) }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Take-home total</p>
        </div>
    </div>

    {{-- Pending Batches --}}
    <div class="fade-up mb-6">
        <div class="flex items-center gap-2 mb-4">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                </div>
                <span class="text-sm font-semibold text-gray-900">Pending Approval</span>
            </div>
            @if($totalPending > 0)
                <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full bg-amber-100 text-amber-700 text-[0.55rem] font-bold">{{ $totalPending }}</span>
            @endif
        </div>
        @forelse($batchData as $batch)
            @php
                $startDate = $batch['period_start'];
                $endDate   = $batch['period_end'];
                $isFirst   = $startDate->format('d') <= 15;
            @endphp
            <a href="{{ route('payroll-approval.batch', $batch['batch_id']) }}"
               class="slide-right block bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-3 no-underline transition-all duration-200 hover:shadow-md hover:border-amber-200 hover:-translate-y-0.5 group"
               style="animation-delay:{{ 0.05 * $loop->iteration }}s">
                <div class="flex items-start justify-between gap-4 flex-wrap">
                    <div class="min-w-0 flex-1">
                        <h3 class="text-sm font-bold text-gray-900">{{ $startDate->format('F Y') }} — {{ $isFirst ? '1st' : '2nd' }} Half</h3>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $startDate->format('M d') }} – {{ $endDate->format('M d, Y') }}</p>
                    </div>
                    <div class="flex items-center gap-4 flex-wrap">
                        <div class="text-right">
                            <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">{{ $batch['count'] }} employees</p>
                            <p class="text-xs font-bold text-emerald-600 tabular-nums">₱{{ number_format($batch['total_net'], 0) }} net</p>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 text-[0.55rem] font-semibold border border-amber-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Pending
                        </span>
                        <svg class="w-4 h-4 text-gray-300 group-hover:text-gray-500 transition-colors shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </div>
            </a>
        @empty
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-10 text-center">
                <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm font-semibold text-gray-400">No batches pending approval</p>
                <p class="text-xs text-gray-400 mt-1">All caught up!</p>
            </div>
        @endforelse

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $batchData->withQueryString()->links('pagination::tailwind') }}
        </div>
    </div>

    {{-- History Tabs --}}
    @if($historyBatches->isNotEmpty())
    <div class="fade-up bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-50 flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-gray-100 text-gray-500 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <span class="text-sm font-semibold text-gray-900">History</span>
            <span class="text-[0.55rem] text-gray-400">Previously processed batches</span>
        </div>

        @php
            $approvedHistory = $historyBatches->where('status', 'approved');
            $rejectedHistory = $historyBatches->where('status', 'rejected');
            $histTab = request()->query('hist', 'approved');
        @endphp

        <div class="border-b border-gray-100">
            <nav class="flex gap-1 -mb-px px-5" role="tablist">
                <a href="{{ request()->fullUrlWithQuery(['hist' => 'approved']) }}" role="tab"
                   class="relative px-4 py-2.5 text-xs font-medium border-b-2 transition-all duration-200 group inline-flex items-center gap-1.5
                   {{ $histTab === 'approved'
                       ? 'border-emerald-500 text-emerald-700 bg-emerald-50/60'
                       : 'border-transparent text-gray-400 hover:text-gray-600 hover:border-gray-300 hover:bg-gray-50/50' }}">
                    <svg class="w-3.5 h-3.5 transition-colors {{ $histTab === 'approved' ? 'text-emerald-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Approved
                    <span class="ml-0.5 inline-flex items-center justify-center min-w-[16px] h-4 px-1 rounded-full text-[0.45rem] font-bold
                        {{ $histTab === 'approved' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-200 text-gray-600' }}">{{ $approvedHistory->count() }}</span>
                </a>
                <a href="{{ request()->fullUrlWithQuery(['hist' => 'rejected']) }}" role="tab"
                   class="relative px-4 py-2.5 text-xs font-medium border-b-2 transition-all duration-200 group inline-flex items-center gap-1.5
                   {{ $histTab === 'rejected'
                       ? 'border-red-500 text-red-700 bg-red-50/60'
                       : 'border-transparent text-gray-400 hover:text-gray-600 hover:border-gray-300 hover:bg-gray-50/50' }}">
                    <svg class="w-3.5 h-3.5 transition-colors {{ $histTab === 'rejected' ? 'text-red-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 9l-6 6M9 9l6 6"/></svg>
                    Rejected
                    <span class="ml-0.5 inline-flex items-center justify-center min-w-[16px] h-4 px-1 rounded-full text-[0.45rem] font-bold
                        {{ $histTab === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-gray-200 text-gray-600' }}">{{ $rejectedHistory->count() }}</span>
                </a>
            </nav>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-50 bg-gray-50/50">
                        <th class="text-left px-5 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Period</th>
                        <th class="text-center px-4 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Employees</th>
                        <th class="text-right px-4 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Gross</th>
                        <th class="text-right px-4 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Net</th>
                        <th class="text-center px-4 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Status</th>
                        <th class="text-right px-5 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @php $histSource = $histTab === 'rejected' ? $rejectedHistory : $approvedHistory; @endphp
                    @foreach($histSource as $batch)
                        @php
                            $startDate   = $batch['period_start'];
                            $endDate     = $batch['period_end'];
                            $batchStatus = $batch['status'];
                            $isFirst     = $startDate->format('d') <= 15;
                            $isApproved  = $batchStatus === 'approved';
                        @endphp
                        <tr class="transition-colors hover:bg-gray-50/50">
                            <td class="px-5 py-3.5">
                                <p class="text-xs font-semibold text-gray-900">{{ $startDate->format('F Y') }} — {{ $isFirst ? '1st' : '2nd' }} Half</p>
                                <p class="text-[0.6rem] text-gray-400 mt-0.5">{{ $startDate->format('M d') }} – {{ $endDate->format('M d, Y') }}</p>
                            </td>
                            <td class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 tabular-nums">{{ $batch['count'] }}</td>
                            <td class="px-4 py-3.5 text-right text-xs tabular-nums text-gray-600">₱{{ number_format($batch['total_gross'], 0) }}</td>
                            <td class="px-4 py-3.5 text-right text-xs font-bold tabular-nums {{ $isApproved ? 'text-emerald-600' : 'text-red-600' }}">₱{{ number_format($batch['total_net'], 0) }}</td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[0.5rem] font-semibold {{ $isApproved ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $isApproved ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                                    {{ $isApproved ? 'Approved' : 'Rejected' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('payroll-approval.batch', $batch['batch_id']) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-gray-50 text-gray-500 text-[0.55rem] font-bold transition-all hover:bg-indigo-50 hover:text-indigo-600 no-underline">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
@endsection