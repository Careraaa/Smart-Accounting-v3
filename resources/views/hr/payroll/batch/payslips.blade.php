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

    {{-- Back link --}}
    <div class="fade-up">
        <a href="{{ route('payroll.generate-payslip.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl text-xs font-semibold no-underline hover:border-gray-600 hover:text-gray-600 hover:bg-gray-50 transition-colors whitespace-nowrap cursor-pointer">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Pay Slips
        </a>
    </div>

    {{-- Header --}}
    <div class="fade-up flex items-start justify-between gap-4 flex-wrap">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">{{ $batch->display_name }}</h1>
            <p class="text-sm text-gray-400 mt-0.5 font-mono">{{ $batch->period_start->format('M d, Y') }} – {{ $batch->period_end->format('M d, Y') }} · {{ $batch->payrolls->count() }} employees</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-50 bg-gray-50/50">
                        <th class="text-left text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Employee</th>
                        <th class="text-end text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Basic Salary</th>
                        <th class="text-end text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Gross Pay</th>
                        <th class="text-end text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Net Pay</th>
                        <th class="text-center text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                @forelse($batch->payrolls as $payroll)
                    @php
                        $initials = strtoupper(substr($payroll->user->first_name ?? 'U', 0, 1) . substr($payroll->user->last_name ?? '', 0, 1));
                        $badgeCls = match($payroll->status) {
                            'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'released','paid' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'submitted' => 'bg-violet-50 text-violet-700 border-violet-200',
                            'prepared' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            default => 'bg-gray-50 text-gray-600 border-gray-200',
                        };
                        $dotCls = match($payroll->status) {
                            'approved' => 'bg-emerald-500',
                            'released','paid' => 'bg-blue-500',
                            'submitted' => 'bg-violet-500',
                            'prepared' => 'bg-emerald-500',
                            default => 'bg-gray-400',
                        };
                        $payslipUrl = route('payroll.generatePayslip', $payroll);
                    @endphp
                    <tr onclick="window.open('{{ $payslipUrl }}', '_blank')" class="hover:bg-gray-50/40 transition-colors cursor-pointer">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2.5">
                                <span class="w-7 h-7 rounded-lg bg-gray-50 text-gray-500 flex items-center justify-center text-[9px] font-bold shrink-0 border border-gray-200">{{ $initials }}</span>
                                <div>
                                    <div class="text-xs font-semibold text-gray-900">{{ $payroll->user->first_name }} {{ $payroll->user->last_name }}</div>
                                    <div class="text-[0.55rem] text-gray-400">{{ $payroll->user->position ?? '—' }} · {{ $payroll->user->department ?? '—' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-end"><span class="font-mono tabular-nums text-xs text-gray-500">₱{{ number_format($payroll->basic_salary, 2) }}</span></td>
                        <td class="px-4 py-3 text-end"><span class="font-mono tabular-nums text-xs font-semibold text-emerald-600">₱{{ number_format($payroll->gross_pay, 2) }}</span></td>
                        <td class="px-4 py-3 text-end"><span class="font-mono tabular-nums text-xs font-semibold text-gray-900">₱{{ number_format($payroll->net_pay, 2) }}</span></td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[0.55rem] font-semibold border {{ $badgeCls }}">
                                <span class="w-1 h-1 rounded-full {{ $dotCls }}"></span>
                                {{ in_array($payroll->status, ['released', 'paid'], true) ? 'Released' : ucfirst($payroll->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5">
                        <div class="flex flex-col items-center py-12 text-center">
                            <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-300 mb-3">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-500">No payroll records in this batch</p>
                            <p class="text-xs text-gray-400 mt-0.5">Add employees to the batch to generate payslips.</p>
                        </div>
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
