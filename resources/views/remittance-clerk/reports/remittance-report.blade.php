@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
@keyframes fadeSlideUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes scaleIn { 0%{opacity:0;transform:scale(0.92)} 100%{opacity:1;transform:scale(1)} }
@keyframes bounceIn { 0%{opacity:0;transform:scale(0.6)} 60%{transform:scale(1.05)} 80%{transform:scale(0.95)} 100%{opacity:1;transform:scale(1)} }
.rc-fade-up { animation:fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.rc-scale-in { animation:scaleIn 0.35s cubic-bezier(0.16,1,0.3,1) both; }
.rc-bounce { animation:bounceIn 0.5s cubic-bezier(0.16,1,0.3,1) both; }
</style>
@endpush

@section('content')
@php
    $periodLabel = match ($period) {
        'weekly' => 'Week ' . $week . ', ' . $year,
        'monthly' => \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y'),
        'yearly' => (string) $year,
        default => '',
    };

    $printUrl = route('reports.print.remittance-report', [
        'period' => $period,
        'week' => $week,
        'month' => $month,
        'year' => $year,
    ]);
@endphp

{{-- Top bar --}}
    <div class="rc-fade-up flex items-start justify-between gap-4 mb-5 flex-wrap">
        <div>
            <h1 class="text-xl font-extrabold text-gray-900 tracking-tight m-0">Remittance Report</h1>
            <p class="text-xs text-gray-400 max-w-[520px] leading-relaxed m-0 mt-0.5">Filter weekly, monthly, or yearly totals and print a summary for the selected period.</p>
            <span class="inline-block text-[0.65rem] text-gray-400 bg-gray-100 px-2 py-0.5 rounded-md mt-2">{{ $periodLabel }}</span>
        </div>
        <a href="{{ $printUrl }}" target="_blank" rel="noopener" class="rc-bounce inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white no-underline cursor-pointer whitespace-nowrap bg-red-600 hover:bg-red-700 shadow-lg hover:shadow-xl transition-all" style="animation-delay:0.2s">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Print report
        </a>
    </div>

    {{-- Filter bar --}}
    <div class="rc-scale-in bg-white border border-gray-200 rounded-xl p-4 flex items-end gap-3 mb-5 flex-wrap" style="animation-delay:0.05s">
        <div class="flex flex-col gap-1 min-w-[130px] flex-1">
            <label class="text-[0.6rem] font-bold uppercase tracking-wider text-gray-400">Period</label>
            <select id="period" class="border border-gray-200 rounded-lg px-2.5 py-2 text-xs text-gray-800 bg-gray-50 focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all cursor-pointer">
                <option value="weekly" {{ $period === 'weekly' ? 'selected' : '' }}>Weekly</option>
                <option value="monthly" {{ $period === 'monthly' ? 'selected' : '' }}>Monthly</option>
                <option value="yearly" {{ $period === 'yearly' ? 'selected' : '' }}>Yearly</option>
            </select>
        </div>
        <div class="flex flex-col gap-1 min-w-[130px] flex-1" id="weekSelect" style="display:{{ $period === 'weekly' ? 'flex' : 'none' }};">
            <label class="text-[0.6rem] font-bold uppercase tracking-wider text-gray-400">Week</label>
            <select id="week" class="border border-gray-200 rounded-lg px-2.5 py-2 text-xs text-gray-800 bg-gray-50 focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all cursor-pointer">
                @for ($i = 1; $i <= 52; $i++)
                    <option value="{{ $i }}" {{ (int) $week === $i ? 'selected' : '' }}>Week {{ $i }}</option>
                @endfor
            </select>
        </div>
        <div class="flex flex-col gap-1 min-w-[130px] flex-1" id="monthSelect" style="display:{{ $period === 'monthly' ? 'flex' : 'none' }};">
            <label class="text-[0.6rem] font-bold uppercase tracking-wider text-gray-400">Month</label>
            <select id="month" class="border border-gray-200 rounded-lg px-2.5 py-2 text-xs text-gray-800 bg-gray-50 focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all cursor-pointer">
                @for ($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ (int) $month === $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                @endfor
            </select>
        </div>
        <div class="flex flex-col gap-1 min-w-[130px] flex-1">
            <label class="text-[0.6rem] font-bold uppercase tracking-wider text-gray-400">Year</label>
            <select id="year" class="border border-gray-200 rounded-lg px-2.5 py-2 text-xs text-gray-800 bg-gray-50 focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all cursor-pointer">
                @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
                    <option value="{{ $i }}" {{ (int) $year === $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
        </div>
    </div>

    {{-- Stats grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 mb-5">
        <div class="rc-bounce bg-white border border-gray-200 rounded-xl p-4 relative overflow-hidden" style="animation-delay:0.08s">
            <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-emerald-500 rounded-b-xl"></div>
            <div class="text-[0.6rem] font-bold uppercase tracking-wider text-gray-400 mb-1.5">Total collection</div>
            <div class="text-lg font-extrabold text-gray-900 font-mono tracking-tight">₱{{ number_format($totalCollection, 2) }}</div>
            <div class="text-[0.65rem] text-gray-400 mt-1">For selected period</div>
        </div>
        <div class="rc-bounce bg-white border border-gray-200 rounded-xl p-4 relative overflow-hidden" style="animation-delay:0.12s">
            <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-red-500 rounded-b-xl"></div>
            <div class="text-[0.6rem] font-bold uppercase tracking-wider text-gray-400 mb-1.5">Total expenses</div>
            <div class="text-lg font-extrabold text-gray-900 font-mono tracking-tight">₱{{ number_format($totalExpenses, 2) }}</div>
            <div class="text-[0.65rem] text-gray-400 mt-1">Trip and operating costs</div>
        </div>
        <div class="rc-bounce bg-white border border-gray-200 rounded-xl p-4 relative overflow-hidden" style="animation-delay:0.16s">
            <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-sky-500 rounded-b-xl"></div>
            <div class="text-[0.6rem] font-bold uppercase tracking-wider text-gray-400 mb-1.5">Net remittance</div>
            <div class="text-lg font-extrabold text-gray-900 font-mono tracking-tight">₱{{ number_format($totalNetRemittance, 2) }}</div>
            <div class="text-[0.65rem] text-gray-400 mt-1">Collection minus expenses</div>
        </div>
        <div class="rc-bounce bg-white border border-gray-200 rounded-xl p-4 relative overflow-hidden" style="animation-delay:0.2s">
            <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-amber-500 rounded-b-xl"></div>
            <div class="text-[0.6rem] font-bold uppercase tracking-wider text-gray-400 mb-1.5">Short remittances</div>
            <div class="text-lg font-extrabold text-gray-900 font-mono tracking-tight">{{ $shortRemittances }}</div>
            <div class="text-[0.65rem] text-gray-400 mt-1">Shortages in period</div>
        </div>
    </div>

    {{-- Table card --}}
    <div class="rc-fade-up bg-white border border-gray-200 rounded-xl overflow-hidden" style="animation-delay:0.3s">
        <div class="px-4 py-3.5 border-b border-gray-100">
            <h2 class="flex items-center gap-2 text-sm font-bold text-gray-900 m-0">
                <span class="w-2 h-2 rounded-full bg-red-600 inline-block"></span>
                Daily totals
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-200">
                        <th class="text-left px-4 py-2.5 text-[0.55rem] font-bold uppercase tracking-wider text-gray-500">Date</th>
                        <th class="text-right px-4 py-2.5 text-[0.55rem] font-bold uppercase tracking-wider text-gray-500">Collection</th>
                        <th class="text-right px-4 py-2.5 text-[0.55rem] font-bold uppercase tracking-wider text-gray-500">Expenses</th>
                        <th class="text-right px-4 py-2.5 text-[0.55rem] font-bold uppercase tracking-wider text-gray-500">Net remittance</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($groupedRemittances as $remittance)
                        <tr class="border-b border-gray-100 hover:bg-gray-50/50 transition-colors">
                            <td class="px-4 py-2.5 text-xs font-semibold text-gray-800">{{ $remittance['remittance_date']->format('M d, Y') }}</td>
                            <td class="px-4 py-2.5 text-right text-xs font-mono font-bold text-emerald-600">₱{{ number_format($remittance['total_collection'], 2) }}</td>
                            <td class="px-4 py-2.5 text-right text-xs font-mono text-gray-400">₱{{ number_format($remittance['total_expenses'], 2) }}</td>
                            <td class="px-4 py-2.5 text-right text-xs font-mono font-bold {{ $remittance['is_short_remittance'] ? 'text-red-600' : 'text-emerald-600' }}">
                                ₱{{ number_format($remittance['net_remittance'], 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-12">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-6 h-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                                    <p class="text-xs text-gray-400">No remittances found for this period. Try adjusting the filters.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Pagination --}}
        @if(method_exists($groupedRemittances, 'hasPages') && $groupedRemittances->hasPages())
        <div class="flex justify-between items-center px-4 py-3 border-t border-gray-100 bg-gray-50/50">
            <div class="text-[0.65rem] text-gray-400">
                Showing
                <strong class="text-gray-700">{{ $groupedRemittances->firstItem() }}</strong>–<strong class="text-gray-700">{{ $groupedRemittances->lastItem() }}</strong>
                of
                <strong class="text-gray-700">{{ $groupedRemittances->total() }}</strong> daily totals
            </div>
            {{ $groupedRemittances->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
(function () {
    const periodSelect = document.getElementById('period');
    const weekSelectEl = document.getElementById('weekSelect');
    const monthSelectEl = document.getElementById('monthSelect');

    function toggleFilters() {
        const period = periodSelect.value;
        weekSelectEl.style.display = period === 'weekly' ? 'flex' : 'none';
        monthSelectEl.style.display = period === 'monthly' ? 'flex' : 'none';
    }

    function updateReport() {
        const period = periodSelect.value;
        const week = document.getElementById('week').value;
        const month = document.getElementById('month').value;
        const year = document.getElementById('year').value;
        let url = '{{ route('reports.remittance-report') }}?period=' + period + '&year=' + year;
        if (period === 'weekly') url += '&week=' + week;
        if (period === 'monthly') url += '&month=' + month;
        window.location.href = url;
    }

    periodSelect.addEventListener('change', function () {
        toggleFilters();
        updateReport();
    });
    document.getElementById('week').addEventListener('change', updateReport);
    document.getElementById('month').addEventListener('change', updateReport);
    document.getElementById('year').addEventListener('change', updateReport);
})();
</script>
@endpush
