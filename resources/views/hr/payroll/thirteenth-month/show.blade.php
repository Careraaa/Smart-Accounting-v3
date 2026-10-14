@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeSlideUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes scaleIn { 0%{opacity:0;transform:scale(0.92)} 100%{opacity:1;transform:scale(1)} }
.fade-up { animation:fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) both; }
</style>
@endpush

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

    @foreach(['success','error'] as $t)
        @if(session($t))
        <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl text-sm font-medium animate-[fadeSlideUp_0.3s_ease] @switch($t) @case('success') bg-green-50 text-green-700 @break @case('error') bg-red-50 text-red-700 @break @endswitch">
            <span>{{ session($t) }}</span>
        </div>
        @endif
    @endforeach

    <div class="fade-up flex items-start justify-between gap-4">
        <div class="min-w-0">
            <a href="{{ $thirteenthMonthPay->batch ? route('payroll.thirteenth-month-pay.batch.details', $thirteenthMonthPay->batch) : route('payroll.salary-computation.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl text-xs font-semibold no-underline hover:border-gray-600 hover:text-gray-600 hover:bg-gray-50 transition-colors whitespace-nowrap cursor-pointer mb-2">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to batch
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-gray-300 to-gray-400 flex items-center justify-center text-sm font-bold text-white uppercase shrink-0">
                    {{ substr($thirteenthMonthPay->user?->first_name ?? '?', 0, 1) }}{{ substr($thirteenthMonthPay->user?->last_name ?? '?', 0, 1) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-bold text-gray-900">{{ $thirteenthMonthPay->user?->name ?? 'Deleted User' }}</p>
                    <p class="text-xs text-gray-400">{{ $thirteenthMonthPay->user?->position ?? '' }} &middot; {{ $thirteenthMonthPay->user?->department ?? '' }}</p>
                </div>
                @php
                    $ps = match($thirteenthMonthPay->status) {
                        'paid' => ['label'=>'Paid','dot'=>'bg-emerald-400','text'=>'text-emerald-700','bg'=>'bg-emerald-100'],
                        'partial' => ['label'=>'Partial','dot'=>'bg-blue-400','text'=>'text-blue-700','bg'=>'bg-blue-100'],
                        default => ['label'=>'Pending','dot'=>'bg-amber-400','text'=>'text-amber-700','bg'=>'bg-amber-100'],
                    };
                @endphp
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $ps['bg'] }} {{ $ps['text'] }}">
                    <span class="w-2 h-2 rounded-full {{ $ps['dot'] }}"></span>
                    {{ $ps['label'] }}
                </span>
            </div>

            <div class="border-t border-gray-100 pt-4 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Calendar Year</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $thirteenthMonthPay->calendar_year }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Total Basic Salary Earned</span>
                    <span class="text-sm font-semibold text-gray-900 tabular-nums">₱{{ number_format($thirteenthMonthPay->total_basic_salary_earned, 2) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Months Worked</span>
                    <span class="text-sm font-semibold text-gray-900 tabular-nums">{{ $thirteenthMonthPay->months_worked }}</span>
                </div>
                <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                    <span class="text-sm font-bold text-gray-900">13th Month Pay</span>
                    <span class="text-base font-bold text-emerald-600 tabular-nums">₱{{ number_format($thirteenthMonthPay->thirteenth_month_pay, 2) }}</span>
                </div>

                @if($thirteenthMonthPay->amount_paid > 0)
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Amount Paid</span>
                    <span class="text-sm font-semibold text-emerald-600 tabular-nums">₱{{ number_format($thirteenthMonthPay->amount_paid, 2) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Remaining</span>
                    <span class="text-sm font-semibold text-violet-600 tabular-nums">₱{{ number_format($thirteenthMonthPay->amount_remaining ?? 0, 2) }}</span>
                </div>
                @if($thirteenthMonthPay->payment_date)
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Payment Date</span>
                    <span class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($thirteenthMonthPay->payment_date)->format('M d, Y') }}</span>
                </div>
                @endif
                @endif
            </div>

            @if($thirteenthMonthPay->notes)
            <div class="mt-4 p-3 bg-gray-50 rounded-xl">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Notes</p>
                <p class="text-sm text-gray-700">{{ $thirteenthMonthPay->notes }}</p>
            </div>
            @endif


        </div>
    </div>

    @if(isset($breakdown['payroll_lines']) && count($breakdown['payroll_lines']) > 0)
    <div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-50">
            <span class="text-sm font-semibold text-gray-900">Computation Breakdown</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Period</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Basic Salary</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($breakdown['payroll_lines'] as $line)
                    <tr class="hover:bg-gray-50/40">
                        <td class="px-5 py-3 text-sm text-gray-900">{{ $line['period_start'] }} &mdash; {{ $line['period_end'] }}</td>
                        <td class="px-5 py-3 text-right text-sm font-semibold text-gray-900 tabular-nums">₱{{ number_format($line['basic_salary'], 2) }}</td>
                        <td class="px-5 py-3 text-xs text-gray-400">Basic salary only</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50/50">
                    <tr>
                        <td class="px-5 py-3 text-sm font-bold text-gray-900">Total</td>
                        <td class="px-5 py-3 text-right text-sm font-bold text-gray-900 tabular-nums">₱{{ number_format($breakdown['total_basic_salary'], 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection
