@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeSlideUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes scaleIn { 0%{opacity:0;transform:scale(0.92)} 100%{opacity:1;transform:scale(1)} }
.fade-up { animation:fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) both; }
</style>
@endpush

@section('content')
<div class="space-y-5 max-w-3xl">

    @foreach(['success','error'] as $t)
        @if(session($t))
        <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl text-sm font-medium animate-[fadeSlideUp_0.3s_ease] @switch($t) @case('success') bg-green-50 text-green-700 @break @case('error') bg-red-50 text-red-700 @break @endswitch">
            <span>{{ session($t) }}</span>
        </div>
        @endif
    @endforeach

    @php
        $statusColors = [
            'Draft' => 'bg-amber-50 text-amber-700 border-amber-200',
            'Posted' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'Void' => 'bg-red-50 text-red-700 border-red-200',
        ];
    @endphp

    <div class="fade-up flex items-start justify-between gap-4">
        <div class="min-w-0">
            <a href="{{ route('accounting.journal-entries.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl text-xs font-semibold no-underline hover:border-gray-600 hover:text-gray-600 hover:bg-gray-50 transition-colors whitespace-nowrap cursor-pointer mb-2">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back
            </a>
            <h1 class="text-xl font-bold text-gray-900 tracking-tight">{{ $journalEntry->journal_number }}</h1>
            <p class="text-xs text-gray-400 mt-0.5">{{ $journalEntry->transaction_date->format('F d, Y') }} &middot; {{ $journalEntry->description }}</p>
        </div>
        <div class="flex items-center gap-2 shrink-0 flex-wrap">
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[0.65rem] font-semibold uppercase tracking-wide border {{ $statusColors[$journalEntry->status] ?? '' }}">
                {{ $journalEntry->status }}
            </span>
            @if($journalEntry->isEditable())
                <a href="{{ route('accounting.journal-entries.edit', $journalEntry) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-600 transition-all hover:bg-gray-50 active:scale-[0.97] no-underline">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit
                </a>
                <form method="POST" action="{{ route('accounting.journal-entries.post', $journalEntry) }}" class="inline"
                      onsubmit="return confirm('Are you sure you want to post this journal entry? This action cannot be undone.')">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 text-white rounded-lg text-xs font-semibold transition-all hover:bg-emerald-700 active:scale-[0.97] cursor-pointer border-0">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Post
                    </button>
                </form>
            @endif
            @if($journalEntry->status === 'Posted')
                <form method="POST" action="{{ route('accounting.journal-entries.void', $journalEntry) }}" class="inline"
                      onsubmit="return confirm('Are you sure you want to void this journal entry? This will reverse its effect on the General Ledger.')">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-red-200 rounded-lg text-xs font-semibold text-red-600 transition-all hover:bg-red-50 active:scale-[0.97] cursor-pointer">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 9l-6 6M9 9l6 6"/></svg>
                        Void
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Lines Table --}}
    <div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-50">
                        <th class="text-left text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Account</th>
                        <th class="text-left text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Description</th>
                        <th class="text-right text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Debit</th>
                        <th class="text-right text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Credit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($journalEntry->lines as $line)
                        <tr class="transition-colors hover:bg-gray-50/40">
                            <td class="px-4 py-3">
                                <span class="text-xs font-bold text-gray-900 font-mono">{{ $line->account->account_code }}</span>
                                <span class="text-xs text-gray-500 ml-1.5">{{ $line->account->account_name }}</span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">{{ $line->description ?? '—' }}</td>
                            <td class="px-4 py-3 text-right text-xs font-semibold {{ $line->debit > 0 ? 'text-gray-900' : 'text-gray-300' }} tabular-nums">
                                {{ $line->debit > 0 ? '₱' . number_format($line->debit, 2) : '—' }}
                            </td>
                            <td class="px-4 py-3 text-right text-xs font-semibold {{ $line->credit > 0 ? 'text-gray-900' : 'text-gray-300' }} tabular-nums">
                                {{ $line->credit > 0 ? '₱' . number_format($line->credit, 2) : '—' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t border-gray-100 bg-gray-50/50">
                        <td colspan="2" class="px-4 py-3 text-xs font-bold text-gray-900">Totals</td>
                        <td class="px-4 py-3 text-right text-xs font-bold text-gray-900 tabular-nums">₱{{ number_format($journalEntry->total_debit, 2) }}</td>
                        <td class="px-4 py-3 text-right text-xs font-bold text-gray-900 tabular-nums">₱{{ number_format($journalEntry->total_credit, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Balance Check --}}
        <div class="px-5 py-3 border-t border-gray-100 flex items-center justify-between">
            <div class="text-xs text-gray-400">
                @if($journalEntry->reference_type)
                    Reference: <span class="font-semibold text-gray-600">{{ $journalEntry->reference_type }} #{{ $journalEntry->reference_id }}</span>
                @endif
            </div>
            <div class="text-sm font-bold {{ $journalEntry->isBalanced() ? 'text-emerald-600' : 'text-red-600' }}">
                @if($journalEntry->isBalanced())
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        BALANCED
                    </span>
                @else
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 9l-6 6M9 9l6 6"/></svg>
                        NOT BALANCED
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Metadata --}}
    <div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h2 class="text-xs font-bold uppercase tracking-wide text-gray-400 mb-3">Entry Details</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div>
                <p class="text-[0.6rem] font-semibold uppercase tracking-wide text-gray-400 mb-1">Created By</p>
                <p class="text-xs font-semibold text-gray-900">{{ $journalEntry->createdBy?->name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-[0.6rem] font-semibold uppercase tracking-wide text-gray-400 mb-1">Created At</p>
                <p class="text-xs font-semibold text-gray-900">{{ $journalEntry->created_at->format('M d, Y h:i A') }}</p>
            </div>
            @if($journalEntry->postedBy)
            <div>
                <p class="text-[0.6rem] font-semibold uppercase tracking-wide text-gray-400 mb-1">Posted By</p>
                <p class="text-xs font-semibold text-gray-900">{{ $journalEntry->postedBy->name }}</p>
            </div>
            <div>
                <p class="text-[0.6rem] font-semibold uppercase tracking-wide text-gray-400 mb-1">Posted At</p>
                <p class="text-xs font-semibold text-gray-900">{{ $journalEntry->posted_at->format('M d, Y h:i A') }}</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
