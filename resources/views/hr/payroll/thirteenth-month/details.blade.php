@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeSlideUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes scaleIn { 0%{opacity:0;transform:scale(0.92)} 100%{opacity:1;transform:scale(1)} }
.fade-up { animation:fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.stat-card { animation:scaleIn 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.stat-card:nth-child(1) { animation-delay:0.05s; }
.stat-card:nth-child(2) { animation-delay:0.1s; }
</style>
@endpush

@section('content')
<div class="space-y-5">

    @php
        $records = $batch->thirteenthMonthPays;
        $totalPayable = $records->sum('thirteenth_month_pay');
        $statusInfo = match($batch->status) {
            'draft' => ['label'=>'Draft','dot'=>'bg-amber-300','text'=>'text-amber-500','bg'=>'bg-amber-50'],
            'submitted' => ['label'=>'Submitted','dot'=>'bg-violet-400','text'=>'text-violet-600','bg'=>'bg-violet-50'],
            'approved' => ['label'=>'Approved','dot'=>'bg-emerald-400','text'=>'text-emerald-600','bg'=>'bg-emerald-50'],
            'rejected' => ['label'=>'Rejected','dot'=>'bg-red-400','text'=>'text-red-600','bg'=>'bg-red-50'],
            default => ['label'=>'Draft','dot'=>'bg-amber-300','text'=>'text-amber-500','bg'=>'bg-amber-50'],
        };
    @endphp

    @if($batch->status === 'rejected' && $batch->rejection_note)
    <div class="fade-up bg-red-50 border border-red-200 rounded-xl px-4 py-3.5 flex items-start gap-3">
        <div class="w-8 h-8 rounded-lg bg-red-100 text-red-500 flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-sm font-bold text-red-700">Batch Rejected</p>
            <p class="text-xs text-red-500 mt-0.5 font-mono">
                @if($batch->rejected_at) {{ $batch->rejected_at->format('M d, Y \a\t h:i A') }} @endif
            </p>
            <p class="text-xs text-red-600 mt-2 bg-red-100/60 rounded-lg px-3 py-2">{{ $batch->rejection_note }}</p>
        </div>
    </div>
    @endif

    <div class="fade-up flex items-start justify-between gap-4">
        <div class="min-w-0">
            <a href="{{ route('payroll.salary-computation.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl text-xs font-semibold no-underline hover:border-gray-600 hover:text-gray-600 hover:bg-gray-50 transition-colors whitespace-nowrap cursor-pointer mb-2">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to batches
            </a>
            <h1 class="text-xl font-bold text-gray-900 tracking-tight">13th Month Pay &mdash; {{ $batch->period_start->format('Y') }}</h1>
            <p class="text-xs text-gray-400 mt-0.5 font-mono">{{ $batch->period_start->format('M d, Y') }} &ndash; {{ $batch->period_end->format('M d, Y') }}</p>
        </div>
        <div class="flex items-center gap-2 shrink-0 flex-wrap">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold uppercase tracking-wide {{ $statusInfo['bg'] }} {{ $statusInfo['text'] }}">
                <span class="w-2 h-2 rounded-full {{ $statusInfo['dot'] }}"></span>
                {{ $statusInfo['label'] }}
            </span>
            @if($batch->status !== 'rejected')
                <a href="{{ route('payroll.thirteenth-month-pay.batch.payslips', $batch) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-600 transition-all hover:bg-gray-50 active:scale-[0.97] no-underline">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Payslips
                </a>
            @endif
            @if(in_array(auth()->user()?->role, ['hr','superadmin','qr_admin','accountant'], true) && $batch->status !== 'approved')
                <a href="{{ route('payroll.thirteenth-month-pay.batch.confirm', $batch) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-600 transition-all hover:bg-gray-50 active:scale-[0.97] no-underline">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit Batch
                </a>
            @endif
            @if(auth()->user()?->role === 'accountant' && $batch->status === 'submitted')
                <form action="{{ route('payroll-approval.approve-batch') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="batch_id" value="{{ $batch->id }}">
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 text-white rounded-lg text-xs font-semibold transition-all hover:bg-emerald-700 active:scale-[0.97] cursor-pointer border-0">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Approve
                    </button>
                </form>
                <form action="{{ route('payroll-approval.reject-batch') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="batch_id" value="{{ $batch->id }}">
                    <div class="flex items-center gap-1">
                        <input type="text" name="rejection_note" required minlength="3" placeholder="Reason (required)" class="w-44 px-2.5 py-1.5 rounded-lg border border-gray-200 text-xs outline-none focus:border-red-300 focus:ring-2 focus:ring-red-100">
                        <button type="submit" class="px-3 py-1.5 bg-red-50 text-red-600 border border-red-200 rounded-lg text-xs font-semibold transition-all hover:bg-red-100 active:scale-[0.97] cursor-pointer">Reject</button>
                    </div>
                </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs text-gray-400 font-medium">Employees</p>
            <p class="text-lg font-bold text-gray-900 tabular-nums mt-1">{{ $records->count() }}</p>
        </div>
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs text-gray-400 font-medium">Total 13th Month Pay</p>
            <p class="text-lg font-bold text-amber-600 tabular-nums mt-1">₱{{ number_format($totalPayable, 2) }}</p>
        </div>
    </div>

    <div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-50 flex items-center justify-between">
            <span class="text-sm font-semibold text-gray-900">Employee Records</span>
            <span class="text-xs text-gray-400 tabular-nums">{{ $records->count() }} records</span>
        </div>

        @if($records->isEmpty())
        <div class="flex flex-col items-center py-12 text-center">
            <p class="text-sm font-semibold text-gray-500">No records in this batch</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-50">
                        <th class="text-left text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Employee</th>
                        <th class="text-right text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Basic Salary</th>
                        <th class="text-right text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Months Worked</th>
                        <th class="text-right text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">13th Month Pay</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($records as $record)
                    @php $u = $record->user; @endphp
                    <tr onclick="window.location='{{ route('payroll.thirteenth-month-pay.show', $record) }}'" class="transition-colors hover:bg-gray-50/40 cursor-pointer">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center text-[11px] font-bold text-gray-600 uppercase shrink-0">
                                    {{ substr($u->first_name ?? '?', 0, 1) }}{{ substr($u->last_name ?? '?', 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-semibold text-gray-900 truncate">{{ $u->last_name ?? '' }}, {{ $u->first_name ?? '' }}</p>
                                    <p class="text-[0.55rem] text-gray-400 truncate font-mono">{{ $u->position ?? '—' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right text-xs font-semibold text-gray-900 tabular-nums">₱{{ number_format($record->total_basic_salary_earned, 2) }}</td>
                        <td class="px-4 py-3 text-right text-xs font-semibold text-gray-900 tabular-nums">{{ $record->months_worked }}</td>
                        <td class="px-4 py-3 text-right text-xs font-bold text-amber-600 tabular-nums">₱{{ number_format($record->thirteenth_month_pay, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                @if($records->isNotEmpty())
                <tfoot>
                    <tr class="border-t border-gray-100 bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-bold text-gray-900">Totals</td>
                        <td class="px-4 py-3 text-right text-xs font-bold text-gray-900 tabular-nums">₱{{ number_format($records->sum('total_basic_salary_earned'), 2) }}</td>
                        <td class="px-4 py-3 text-right text-xs font-bold text-gray-900 tabular-nums">{{ $records->sum('months_worked') }}</td>
                        <td class="px-4 py-3 text-right text-xs font-bold text-amber-600 tabular-nums">₱{{ number_format($totalPayable, 2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
        @endif
    </div>

</div>
@endsection
