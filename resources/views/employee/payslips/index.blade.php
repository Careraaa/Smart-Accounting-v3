@extends('layouts.layout')

@section('content')
<div class="w-full max-w-5xl mx-auto space-y-5">
    <div>
        <p class="text-xs font-semibold tracking-widest text-gray-400 uppercase">Employee Portal</p>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">My Payslips</h1>
        <p class="text-sm text-gray-500 mt-1">Payslips are available here after payroll approval.</p>
    </div>

    <section class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <h2 class="px-5 py-4 border-b border-gray-100 text-sm font-semibold text-gray-900">Regular Payroll</h2>
        @forelse($payrolls as $payroll)
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 px-5 py-4 border-b border-gray-50 last:border-0">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-gray-900">
                        {{ $payroll->payroll_period_start->format('M d, Y') }} – {{ $payroll->payroll_period_end->format('M d, Y') }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">Net pay: ₱{{ number_format($payroll->net_pay, 2) }}</p>
                </div>
                <a href="{{ route('employee.payslips.show', $payroll) }}" target="_blank" rel="noopener"
                   class="inline-flex justify-center items-center px-4 py-2 bg-gray-900 text-white rounded-lg text-xs font-semibold no-underline hover:bg-gray-700">
                    View Payslip
                </a>
            </div>
        @empty
            <p class="px-5 py-6 text-sm text-gray-500">No approved regular payslips are available yet.</p>
        @endforelse
    </section>

    <section class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <h2 class="px-5 py-4 border-b border-gray-100 text-sm font-semibold text-gray-900">13th Month Pay</h2>
        @forelse($thirteenthMonthPays as $record)
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 px-5 py-4 border-b border-gray-50 last:border-0">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-gray-900">{{ $record->calendar_year }}</p>
                    <p class="text-xs text-gray-500 mt-1">₱{{ number_format($record->thirteenth_month_pay, 2) }}</p>
                </div>
                <a href="{{ route('employee.payslips.thirteenth-month', ['batch' => $record->batch_id, 'record' => $record]) }}" target="_blank" rel="noopener"
                   class="inline-flex justify-center items-center px-4 py-2 bg-gray-900 text-white rounded-lg text-xs font-semibold no-underline hover:bg-gray-700">
                    View Payslip
                </a>
            </div>
        @empty
            <p class="px-5 py-6 text-sm text-gray-500">No approved 13th-month payslips are available yet.</p>
        @endforelse
    </section>
</div>
@endsection
