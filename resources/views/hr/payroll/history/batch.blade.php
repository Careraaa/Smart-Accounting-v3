@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeSlideUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes scaleIn { 0%{opacity:0;transform:scale(0.92)} 100%{opacity:1;transform:scale(1)} }
@keyframes countUp { 0%{opacity:0;transform:translateY(8px)} 100%{opacity:1;transform:translateY(0)} }
.stat-card { animation:scaleIn 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.stat-card:nth-child(1) { animation-delay:0.05s; }
.stat-card:nth-child(2) { animation-delay:0.1s; }
.stat-card:nth-child(3) { animation-delay:0.15s; }
.stat-card:nth-child(4) { animation-delay:0.2s; }
.fade-up { animation:fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.bat-count { animation:countUp 0.6s cubic-bezier(0.16,1,0.3,1) both; }
</style>
@endpush

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="fade-up flex items-start gap-3">
        <a href="{{ route('payroll.history.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-600 transition-all hover:border-gray-400 hover:text-gray-900 active:scale-[0.97] no-underline shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Back
        </a>
        <div>
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Batch Detail</p>
            <h1 class="text-xl font-bold text-gray-900 tracking-tight mt-0.5">{{ $startDate->format('F Y') }} — {{ $startDate->format('d') <= 15 ? '1st' : '2nd' }} Half</h1>
            <p class="text-xs text-gray-400 mt-0.5 font-mono">{{ $startDate->format('M d, Y') }} – {{ $endDate->format('M d, Y') }} · {{ $payrolls->count() }} employees</p>
        </div>
    </div>

    {{-- Filter bar --}}
    <div class="fade-up flex items-center gap-3 flex-wrap">
        <div class="flex-1 min-w-[200px]">
            <input type="text" id="batchSearch" placeholder="Search employee…" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-700 bg-white outline-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
        </div>
    </div>

    {{-- Table --}}
    <div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm app-standard-table">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-5 py-3">Employee Name</th>
                        <th class="text-left text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-5 py-3">Payroll Period</th>
                        <th class="text-left text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-5 py-3">Basic Salary</th>
                        <th class="text-left text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-5 py-3">Allowances</th>
                        <th class="text-left text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-5 py-3">Gross Pay</th>
                        <th class="text-left text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-5 py-3">Deductions</th>
                        <th class="text-left text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-5 py-3">Net Pay</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50" id="batchTbody">
                @forelse($payrolls as $payroll)
                    @php
                        $initials = strtoupper(substr($payroll->user->first_name ?? 'U', 0, 1) . substr($payroll->user->last_name ?? '', 0, 1));
                    @endphp
                    <tr data-name="{{ strtolower(($payroll->user->first_name ?? '') . ' ' . ($payroll->user->last_name ?? '')) }}" data-url="{{ route('payroll.salary-computation.show', $payroll) }}" class="hover:bg-gray-50/40 transition-colors cursor-pointer">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2.5">
                                <span class="w-7 h-7 rounded-lg bg-gray-50 text-gray-500 flex items-center justify-center text-[9px] font-bold shrink-0 border border-gray-200">{{ $initials }}</span>
                                <div class="text-xs font-semibold text-gray-900">{{ $payroll->user->first_name }} {{ $payroll->user->last_name }}</div>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-left text-xs text-gray-500">{{ $payroll->payroll_period_start->format('M d') }} – {{ $payroll->payroll_period_end->format('M d, Y') }}</td>
                        <td class="px-5 py-3 text-left"><span class="font-mono tabular-nums text-xs text-gray-500 bat-count">₱{{ number_format($payroll->basic_salary, 2) }}</span></td>
                        <td class="px-5 py-3 text-left"><span class="font-mono tabular-nums text-xs text-gray-500 bat-count">₱{{ number_format($payroll->total_allowances, 2) }}</span></td>
                        <td class="px-5 py-3 text-left"><span class="font-mono tabular-nums text-xs font-semibold text-gray-900 bat-count">₱{{ number_format($payroll->gross_pay, 2) }}</span></td>
                        <td class="px-5 py-3 text-left"><span class="font-mono tabular-nums text-xs text-gray-500 bat-count">₱{{ number_format($payroll->total_deductions, 2) }}</span></td>
                        <td class="px-5 py-3 text-left"><span class="font-mono tabular-nums text-xs font-semibold text-gray-900 bat-count">₱{{ number_format($payroll->net_pay, 2) }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="7">
                        <div class="flex flex-col items-center py-12 text-center">
                            <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-300 mb-3">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-500">No payroll records</p>
                            <p class="text-xs text-gray-400 mt-0.5">No records found in this batch.</p>
                        </div>
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div id="batchNoResults" class="hidden">
            <div class="flex flex-col items-center py-8 text-center">
                <p class="text-sm font-semibold text-gray-500">No results found</p>
                <p class="text-xs text-gray-400 mt-0.5">Try a different search.</p>
            </div>
        </div>
        <div class="px-5 py-3 border-t border-gray-50 flex items-center justify-between gap-4 flex-wrap">
            <p class="text-[0.55rem] text-gray-400 font-mono">{{ $payrolls->count() }} employees</p>
            <p class="text-[0.55rem] text-gray-400 font-mono">
                <strong class="text-gray-700 bat-count" style="animation-delay:0.2s">₱{{ number_format($totalNetPay, 2) }}</strong> total net pay
            </p>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
(function(){
    var s=document.getElementById('batchSearch');
    var tb=document.getElementById('batchTbody'),nr=document.getElementById('batchNoResults');

    // Row click → navigate to detail page
    tb.addEventListener('click', function(e) {
        var row = e.target.closest('tr[data-url]');
        if (row && row.dataset.url) {
            window.location.href = row.dataset.url;
        }
    });

    function run(){
        var q=s.value.toLowerCase().trim();
        var rows=Array.from(tb.querySelectorAll('tr[data-name]'));
        var vis=rows.filter(function(r){return !q||r.dataset.name.includes(q);});
        rows.forEach(function(r){r.style.display='none';});
        vis.forEach(function(r){r.style.display='';});
        nr.style.display=vis.length===0&&rows.length>0?'':'none';
    }
    s.addEventListener('input',run);
})();
</script>
@endpush
