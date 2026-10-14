@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeSlideUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes scaleIn { 0%{opacity:0;transform:scale(0.92)} 100%{opacity:1;transform:scale(1)} }
.fade-up { animation:fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) both; }
</style>
@endpush

@section('content')
<div class="space-y-5">

    <div class="fade-up">
        <a href="{{ route('payroll.generate-payslip.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl text-xs font-semibold no-underline hover:border-gray-600 hover:text-gray-600 hover:bg-gray-50 transition-colors whitespace-nowrap cursor-pointer">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Pay Slips
        </a>
    </div>

    <div class="fade-up flex items-start justify-between gap-4 flex-wrap">
        <div>
            <h1 class="text-xl font-bold text-gray-900 tracking-tight">13th Month Pay &mdash; {{ $batch->period_start->format('Y') }}</h1>
            <p class="text-xs text-gray-400 mt-0.5 font-mono">{{ $batch->period_start->format('M d, Y') }} &ndash; {{ $batch->period_end->format('M d, Y') }} &middot; {{ $batch->thirteenthMonthPays->count() }} employees</p>
        </div>
    </div>

    <div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-50 bg-gray-50/50">
                        <th class="text-left text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Employee</th>
                        <th class="text-left text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Basic Salary</th>
                        <th class="text-left text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Months Worked</th>
                        <th class="text-left text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">13th Month Pay</th>
                        <th class="text-left text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                @forelse($batch->thirteenthMonthPays as $record)
                    @php
                        $initials = strtoupper(substr($record->user->first_name ?? 'U', 0, 1) . substr($record->user->last_name ?? '', 0, 1));
                        $ps = match($record->status) {
                            'paid' => ['label'=>'Paid','dot'=>'bg-emerald-400'],
                            'partial' => ['label'=>'Partial','dot'=>'bg-blue-400'],
                            default => ['label'=>'Pending','dot'=>'bg-amber-400'],
                        };
                        $payslipUrl = route('payroll.thirteenth-month-pay.batch.payslip', [$batch, $record]);
                    @endphp
                    <tr onclick="window.open('{{ $payslipUrl }}', '_blank')" class="hover:bg-gray-50/40 transition-colors cursor-pointer">
                        <td class="px-4 py-3">
                            <div class="min-w-0">
                                <p class="text-xs font-semibold text-gray-900 truncate">{{ $record->user->last_name }}, {{ $record->user->first_name }}</p>
                                <p class="text-[0.55rem] text-gray-400 truncate font-mono">{{ $record->user->position ?? '—' }}</p>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-left text-xs font-semibold text-gray-900 tabular-nums">₱{{ number_format($record->total_basic_salary_earned ?? 0, 2) }}</td>
                        <td class="px-4 py-3 text-left text-xs font-semibold text-gray-900 tabular-nums">{{ $record->months_worked }}</td>
                        <td class="px-4 py-3 text-left text-xs font-bold text-amber-600 tabular-nums">₱{{ number_format($record->thirteenth_month_pay, 2) }}</td>
                        <td class="px-4 py-3 text-left">
                            <span class="inline-flex items-center gap-1 text-[0.5rem] font-semibold uppercase tracking-wide text-gray-500">
                                <span class="w-1.5 h-1.5 rounded-full {{ $ps['dot'] }}"></span>
                                {{ $ps['label'] }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5">
                        <div class="flex flex-col items-center py-12 text-center">
                            <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-300 mb-3">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-500">No records in this batch</p>
                        </div>
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
