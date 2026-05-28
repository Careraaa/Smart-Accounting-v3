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
$sumGross = collect($batchData)->sum('total_gross');
$sumDed   = collect($batchData)->sum('total_deductions');
$sumNet   = collect($batchData)->sum('total_net');
$periodLabel = $period === 'weekly' ? "Week $week" : ($period === 'monthly' ? date('F', mktime(0,0,0,$month,1)) : "Year $year");
@endphp

{{-- Header --}}
    <div class="flex items-start justify-between mb-6 flex-wrap gap-4 fade-up">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Payroll Reports</h1>
            <p class="text-sm text-gray-500 mt-0.5">Approved payroll batches — gross, deductions, and net pay across periods.</p>
            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-gray-100 text-gray-500 text-[0.55rem] font-semibold mt-2">{{ count($batchData) }} {{ Str::plural('batch', count($batchData)) }} · {{ $periodLabel }}</span>
        </div>
        <a href="{{ route('reports.print.payroll-report', request()->query()) }}" target="_blank" rel="noopener"
           class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-gray-200 text-gray-700 text-xs font-bold rounded-xl hover:border-violet-300 hover:text-violet-600 hover:bg-violet-50 transition-all no-underline">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Print
        </a>
    </div>

    {{-- Filter form --}}
    <div class="fade-up bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-6">
        <form method="get" action="{{ route('reports.payroll') }}" class="flex items-end gap-4 flex-wrap">
            <div>
                <label class="block text-[0.55rem] font-bold uppercase tracking-wider text-gray-400 mb-1">Period</label>
                <select name="period" id="period" onchange="this.form.submit()"
                    class="text-xs border border-gray-200 rounded-lg px-3 py-2 bg-white text-gray-700 outline-none focus:border-gray-400 focus:ring-2 focus:ring-gray-100 cursor-pointer">
                    <option value="weekly" {{ $period === 'weekly' ? 'selected' : '' }}>Weekly</option>
                    <option value="monthly" {{ $period === 'monthly' ? 'selected' : '' }}>Monthly</option>
                    <option value="yearly" {{ $period === 'yearly' ? 'selected' : '' }}>Yearly</option>
                </select>
            </div>
            <div id="weekSelectWrap" style="display:{{ $period === 'weekly' ? 'block' : 'none' }}">
                <label class="block text-[0.55rem] font-bold uppercase tracking-wider text-gray-400 mb-1">Week</label>
                <select name="week" onchange="this.form.submit()"
                    class="text-xs border border-gray-200 rounded-lg px-3 py-2 bg-white text-gray-700 outline-none focus:border-gray-400 focus:ring-2 focus:ring-gray-100 cursor-pointer">
                    @for ($i = 1; $i <= 52; $i++)
                        <option value="{{ $i }}" {{ (int) $week === $i ? 'selected' : '' }}>Week {{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div id="monthSelectWrap" style="display:{{ $period === 'monthly' ? 'block' : 'none' }}">
                <label class="block text-[0.55rem] font-bold uppercase tracking-wider text-gray-400 mb-1">Month</label>
                <select name="month" onchange="this.form.submit()"
                    class="text-xs border border-gray-200 rounded-lg px-3 py-2 bg-white text-gray-700 outline-none focus:border-gray-400 focus:ring-2 focus:ring-gray-100 cursor-pointer">
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ (int) $month === $i ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                        </option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-[0.55rem] font-bold uppercase tracking-wider text-gray-400 mb-1">Year</label>
                <select name="year" onchange="this.form.submit()"
                    class="text-xs border border-gray-200 rounded-lg px-3 py-2 bg-white text-gray-700 outline-none focus:border-gray-400 focus:ring-2 focus:ring-gray-100 cursor-pointer">
                    @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
                        <option value="{{ $i }}" {{ (int) $year === $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
        </form>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        <div class="scale-in stat-card bg-white rounded-xl border border-gray-100 p-3.5 shadow-sm hover:shadow-md hover:border-violet-200 transition-all duration-300">
            <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Batches</p>
            <p class="text-xl font-bold text-violet-600 tabular-nums mt-1">{{ count($batchData) }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">In selected period</p>
        </div>
        <div class="scale-in stat-card bg-white rounded-xl border border-gray-100 p-3.5 shadow-sm hover:shadow-md hover:border-emerald-200 transition-all duration-300">
            <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Total Gross</p>
            <p class="text-lg font-bold text-emerald-600 tabular-nums mt-1">₱{{ number_format($sumGross, 0) }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Gross pay summed</p>
        </div>
        <div class="scale-in stat-card bg-white rounded-xl border border-gray-100 p-3.5 shadow-sm hover:shadow-md hover:border-red-200 transition-all duration-300">
            <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Total Deductions</p>
            <p class="text-lg font-bold text-red-600 tabular-nums mt-1">₱{{ number_format($sumDed, 0) }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Deductions summed</p>
        </div>
        <div class="scale-in stat-card bg-white rounded-xl border border-gray-100 p-3.5 shadow-sm hover:shadow-md hover:border-blue-200 transition-all duration-300">
            <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Total Net</p>
            <p class="text-lg font-bold text-blue-600 tabular-nums mt-1">₱{{ number_format($sumNet, 0) }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Take-home total</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="fade-up bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-50 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-violet-500"></span>
            <span class="text-sm font-semibold text-gray-900">Batch summary</span>
            <span class="text-[0.55rem] text-gray-400">Payroll batches in range</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-50 bg-gray-50/50">
                        <th class="text-left px-5 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Period</th>
                        <th class="text-center px-4 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Employees</th>
                        <th class="text-right px-4 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Gross</th>
                        <th class="text-right px-4 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Deductions</th>
                        <th class="text-right px-5 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Net Pay</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($batchData as $batch)
                        <tr class="transition-colors hover:bg-gray-50/50">
                            <td class="px-5 py-3.5 text-xs font-semibold text-gray-900">
                                {{ \Carbon\Carbon::parse($batch['period_start'])->format('M d') }}
                                –
                                {{ \Carbon\Carbon::parse($batch['period_end'])->format('M d, Y') }}
                            </td>
                            <td class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 tabular-nums">{{ $batch['count'] }}</td>
                            <td class="px-4 py-3.5 text-right text-xs tabular-nums text-gray-600">₱{{ number_format($batch['total_gross'], 0) }}</td>
                            <td class="px-4 py-3.5 text-right text-xs tabular-nums text-gray-400">₱{{ number_format($batch['total_deductions'], 0) }}</td>
                            <td class="px-5 py-3.5 text-right text-xs font-bold tabular-nums text-emerald-600">₱{{ number_format($batch['total_net'], 0) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-12">
                                <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <p class="text-xs text-gray-400">No payroll batches for this filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
(function () {
    var period = document.getElementById('period');
    if (!period) return;
    function toggleFilters() {
        var p = period.value;
        var w = document.getElementById('weekSelectWrap');
        var m = document.getElementById('monthSelectWrap');
        if (w) w.style.display = p === 'weekly' ? 'block' : 'none';
        if (m) m.style.display = p === 'monthly' ? 'block' : 'none';
    }
    period.addEventListener('change', toggleFilters);
    toggleFilters();
})();
</script>
@endsection