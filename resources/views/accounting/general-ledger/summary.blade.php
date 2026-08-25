@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeSlideUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
.fade-up { animation:fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) both; }
</style>
@endpush

@section('content')
<div class="space-y-5 max-w-4xl">

    @foreach(['success','error'] as $t)
        @if(session($t))
        <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl text-sm font-medium animate-[fadeSlideUp_0.3s_ease] @switch($t) @case('success') bg-green-50 text-green-700 @break @case('error') bg-red-50 text-red-700 @break @endswitch">
            <span>{{ session($t) }}</span>
        </div>
        @endif
    @endforeach

    <div class="fade-up">
        <a href="{{ route('accounting.general-ledger.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl text-xs font-semibold no-underline hover:border-gray-600 hover:text-gray-600 hover:bg-gray-50 transition-colors whitespace-nowrap cursor-pointer mb-2">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to General Ledger
        </a>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Trial Balance</h1>
        <p class="text-sm text-gray-400 mt-0.5">Summary of all account balances as of {{ now()->format('F d, Y') }}</p>
    </div>

    <div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-50">
                        <th class="text-left text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Code</th>
                        <th class="text-left text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Account Name</th>
                        <th class="text-left text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Type</th>
                        <th class="text-right text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Total Debit</th>
                        <th class="text-right text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Total Credit</th>
                        <th class="text-right text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Balance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @php
                        $typeGroups = $accounts->groupBy('account_type');
                        $typeOrder = ['Asset', 'Liability', 'Equity', 'Revenue', 'Expense'];
                        $typeColors = [
                            'Asset'     => 'bg-blue-50 text-blue-700',
                            'Liability' => 'bg-rose-50 text-rose-700',
                            'Equity'    => 'bg-purple-50 text-purple-700',
                            'Revenue'   => 'bg-emerald-50 text-emerald-700',
                            'Expense'   => 'bg-amber-50 text-amber-700',
                        ];
                    @endphp
                    @foreach($typeOrder as $type)
                        @if(isset($typeGroups[$type]) && $typeGroups[$type]->count() > 0)
                            <tr class="bg-gray-50/50">
                                <td colspan="6" class="px-4 py-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[0.6rem] font-semibold uppercase tracking-wide {{ $typeColors[$type] ?? '' }}">{{ $type }}</span>
                                </td>
                            </tr>
                            @foreach($typeGroups[$type] as $account)
                                <tr class="transition-colors hover:bg-gray-50/40">
                                    <td class="px-4 py-3 text-xs font-bold font-mono text-gray-900">{{ $account->account_code }}</td>
                                    <td class="px-4 py-3 text-xs font-semibold text-gray-900">{{ $account->account_name }}</td>
                                    <td class="px-4 py-3 text-xs text-gray-500">{{ $account->account_type }}</td>
                                    <td class="px-4 py-3 text-right text-xs font-semibold tabular-nums {{ $account->total_debit > 0 ? 'text-gray-900' : 'text-gray-300' }}">
                                        {{ $account->total_debit > 0 ? '₱' . number_format($account->total_debit, 2) : '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-xs font-semibold tabular-nums {{ $account->total_credit > 0 ? 'text-gray-900' : 'text-gray-300' }}">
                                        {{ $account->total_credit > 0 ? '₱' . number_format($account->total_credit, 2) : '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-xs font-bold tabular-nums {{ $account->balance >= 0 ? 'text-gray-900' : 'text-red-600' }}">
                                        ₱{{ number_format($account->balance, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-gray-200 bg-gray-50/80">
                        <td colspan="3" class="px-4 py-3 text-xs font-bold text-gray-900">TOTALS</td>
                        <td class="px-4 py-3 text-right text-xs font-bold text-gray-900 tabular-nums">₱{{ number_format($totalDebit, 2) }}</td>
                        <td class="px-4 py-3 text-right text-xs font-bold text-gray-900 tabular-nums">₱{{ number_format($totalCredit, 2) }}</td>
                        <td class="px-4 py-3 text-right">
                            @if(abs($totalDebit - $totalCredit) < 0.01)
                                <span class="text-xs font-bold text-emerald-600 flex items-center justify-end gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    BALANCED
                                </span>
                            @else
                                <span class="text-xs font-bold text-red-600 flex items-center justify-end gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 9l-6 6M9 9l6 6"/></svg>
                                    UNBALANCED
                                </span>
                            @endif
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
