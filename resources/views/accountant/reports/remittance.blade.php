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
$sumCol = $remittances->sum('total_collection');
$sumExp = $remittances->sum('total_expenses');
$sumNet = $remittances->sum('net_remittance');
$periodLabel = $period === 'daily' ? date('F j, Y', mktime(0,0,0,$month,$day,$year)) : ($period === 'weekly' ? 'Week '.$week.', '.$year : ($period === 'monthly' ? date('F', mktime(0,0,0,$month,1)).' '.$year : $year));
@endphp

{{-- Header --}}
    <div class="flex items-start justify-between mb-6 flex-wrap gap-4 fade-up">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Remittance Reports</h1>
            <p class="text-sm text-gray-500 mt-0.5">Approved daily remittances — collections, expenses, and net amounts across all routes.</p>
            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-gray-100 text-gray-500 text-[0.55rem] font-semibold mt-2">{{ $remittances->count() }} approved {{ Str::plural('record', $remittances->count()) }} · {{ $periodLabel }}</span>
        </div>
        <a href="{{ route('reports.print.remittance-report', request()->query()) }}" target="_blank" rel="noopener"
           class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-gray-200 text-gray-700 text-xs font-bold rounded-xl hover:border-violet-300 hover:text-violet-600 hover:bg-violet-50 transition-all no-underline">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Print
        </a>
    </div>

    {{-- Filter bar --}}
    <div class="fade-up bg-white border border-gray-200 rounded-xl p-4 flex items-end gap-3 mb-5 flex-wrap">
        <div class="flex flex-col gap-1 min-w-[130px] flex-1">
            <label class="text-[0.6rem] font-bold uppercase tracking-wider text-gray-400">Period</label>
            <select id="period" class="border border-gray-200 rounded-lg px-2.5 py-2 text-xs text-gray-800 bg-gray-50 focus:outline-none focus:border-gray-400 focus:ring-2 focus:ring-gray-100 transition-all cursor-pointer">
                <option value="daily" {{ $period === 'daily' ? 'selected' : '' }}>Daily</option>
                <option value="weekly" {{ $period === 'weekly' ? 'selected' : '' }}>Weekly</option>
                <option value="monthly" {{ $period === 'monthly' ? 'selected' : '' }}>Monthly</option>
                <option value="yearly" {{ $period === 'yearly' ? 'selected' : '' }}>Yearly</option>
            </select>
        </div>
        <div class="flex flex-col gap-1 min-w-[130px] flex-1" id="weekSelect" style="display:{{ $period === 'weekly' ? 'flex' : 'none' }}">
            <label class="text-[0.6rem] font-bold uppercase tracking-wider text-gray-400">Week</label>
            <select id="week" class="border border-gray-200 rounded-lg px-2.5 py-2 text-xs text-gray-800 bg-gray-50 focus:outline-none focus:border-gray-400 focus:ring-2 focus:ring-gray-100 transition-all cursor-pointer">
                @for ($i = 1; $i <= 52; $i++)
                    <option value="{{ $i }}" {{ (int) $week === $i ? 'selected' : '' }}>Week {{ $i }}</option>
                @endfor
            </select>
        </div>
        <div class="flex flex-col gap-1 min-w-[130px] flex-1" id="daySelect" style="display:{{ $period === 'daily' ? 'flex' : 'none' }}">
            <label class="text-[0.6rem] font-bold uppercase tracking-wider text-gray-400">Day</label>
            <select id="day" class="border border-gray-200 rounded-lg px-2.5 py-2 text-xs text-gray-800 bg-gray-50 focus:outline-none focus:border-gray-400 focus:ring-2 focus:ring-gray-100 transition-all cursor-pointer">
                @for ($i = 1; $i <= 31; $i++)
                    <option value="{{ $i }}" {{ (int) $day === $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
        </div>
        <div class="flex flex-col gap-1 min-w-[130px] flex-1" id="monthSelect" style="display:{{ in_array($period, ['daily', 'monthly']) ? 'flex' : 'none' }}">
            <label class="text-[0.6rem] font-bold uppercase tracking-wider text-gray-400">Month</label>
            <select id="month" class="border border-gray-200 rounded-lg px-2.5 py-2 text-xs text-gray-800 bg-gray-50 focus:outline-none focus:border-gray-400 focus:ring-2 focus:ring-gray-100 transition-all cursor-pointer">
                @for ($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ (int) $month === $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                @endfor
            </select>
        </div>
        <div class="flex flex-col gap-1 min-w-[130px] flex-1">
            <label class="text-[0.6rem] font-bold uppercase tracking-wider text-gray-400">Year</label>
            <select id="year" class="border border-gray-200 rounded-lg px-2.5 py-2 text-xs text-gray-800 bg-gray-50 focus:outline-none focus:border-gray-400 focus:ring-2 focus:ring-gray-100 transition-all cursor-pointer">
                @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
                    <option value="{{ $i }}" {{ (int) $year === $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        <div class="scale-in stat-card bg-white rounded-xl border border-gray-100 p-3.5 shadow-sm hover:shadow-md hover:border-emerald-200 transition-all duration-300">
            <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Total Collection</p>
            <p class="text-lg font-bold text-emerald-600 tabular-nums mt-1">₱{{ number_format($sumCol, 0) }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Gross collections</p>
        </div>
        <div class="scale-in stat-card bg-white rounded-xl border border-gray-100 p-3.5 shadow-sm hover:shadow-md hover:border-red-200 transition-all duration-300">
            <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Total Expenses</p>
            <p class="text-lg font-bold text-red-600 tabular-nums mt-1">₱{{ number_format($sumExp, 0) }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Trip and operating costs</p>
        </div>
        <div class="scale-in stat-card bg-white rounded-xl border border-gray-100 p-3.5 shadow-sm hover:shadow-md hover:border-blue-200 transition-all duration-300">
            <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Net Remittance</p>
            <p class="text-lg font-bold text-blue-600 tabular-nums mt-1">₱{{ number_format($sumNet, 0) }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Collection minus expenses</p>
        </div>
        <div class="scale-in stat-card bg-white rounded-xl border border-gray-100 p-3.5 shadow-sm hover:shadow-md hover:border-gray-300 transition-all duration-300">
            <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Records</p>
            <p class="text-lg font-bold text-gray-900 tabular-nums mt-1">{{ $remittances->count() }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Approved remittances</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="fade-up bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-50 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            <span class="text-sm font-semibold text-gray-900">All approved remittances</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-50 bg-gray-50/50">
                        <th class="text-left px-5 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Date</th>
                        <th class="text-left px-4 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Driver</th>
                        <th class="text-left px-4 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Route</th>
                        <th class="text-right px-3 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Collection</th>
                        <th class="text-right px-3 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Expenses</th>
                        <th class="text-right px-5 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Net Remittance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50" id="remittanceGrid"></tbody>
            </table>
        </div>
        <div id="remittanceNoResults" class="hidden">
            <div class="flex flex-col items-center py-12 text-center">
                <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                <p class="text-xs text-gray-400">No approved remittance records yet.</p>
            </div>
        </div>
        <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100 bg-gray-50/50 flex-wrap gap-3">
            <div class="text-xs text-gray-400" id="remittanceInfo">Showing <strong class="text-gray-700">0</strong> records</div>
            <nav id="remittanceNav" class="flex items-center gap-1"></nav>
        </div>
    </div>
</div>

<script>
window.remittanceReportData = {!! json_encode($remittances->map(fn($r) => [
    'date'       => $r->remittance_date?->format('M d, Y') ?? '—',
    'driver'     => $r->driver?->name ?? '—',
    'route'      => $r->route?->route_name ?? '—',
    'collection' => (float) $r->total_collection,
    'expenses'   => (float) $r->total_expenses,
    'net'        => (float) $r->net_remittance,
])->values()->all()) !!};
window.remittanceReportTotals = { collection: {{ $sumCol }}, expenses: {{ $sumExp }}, net: {{ $sumNet }} };

(function () {
    const periodSelect = document.getElementById('period');
    const weekSelectEl = document.getElementById('weekSelect');
    const monthSelectEl = document.getElementById('monthSelect');
    const daySelectEl = document.getElementById('daySelect');

    function toggleFilters() {
        const period = periodSelect.value;
        weekSelectEl.style.display = period === 'weekly' ? 'flex' : 'none';
        monthSelectEl.style.display = (period === 'daily' || period === 'monthly') ? 'flex' : 'none';
        daySelectEl.style.display = period === 'daily' ? 'flex' : 'none';
    }

    function updateReport() {
        const period = periodSelect.value;
        const week = document.getElementById('week').value;
        const month = document.getElementById('month').value;
        const year = document.getElementById('year').value;
        const day = document.getElementById('day').value;
        let url = '{{ route('reports.remittance') }}?period=' + period + '&year=' + year;
        if (period === 'weekly') url += '&week=' + week;
        if (period === 'daily') { url += '&month=' + month + '&day=' + day; }
        else if (period === 'monthly') url += '&month=' + month;
        window.location.href = url;
    }

    periodSelect.addEventListener('change', function () {
        toggleFilters();
        updateReport();
    });
    document.getElementById('week').addEventListener('change', updateReport);
    document.getElementById('month').addEventListener('change', updateReport);
    document.getElementById('year').addEventListener('change', updateReport);
    document.getElementById('day').addEventListener('change', updateReport);

    var grid   = document.getElementById('remittanceGrid');
    var noRes  = document.getElementById('remittanceNoResults');
    var info   = document.getElementById('remittanceInfo');
    var nav    = document.getElementById('remittanceNav');
    var PER = 10, page = 1;
    var data = window.remittanceReportData;

    function render() {
        var total = data.length, pages = Math.ceil(total / PER);
        var start = (page - 1) * PER, end = Math.min(start + PER, total);
        var pageData = data.slice(start, end);
        grid.innerHTML = '';

        if (pageData.length === 0) {
            noRes.classList.remove('hidden');
        } else {
            noRes.classList.add('hidden');
            pageData.forEach(function (r) {
                var tr = document.createElement('tr');
                tr.className = 'transition-colors hover:bg-gray-50/50';
                tr.innerHTML =
                    '<td class="px-5 py-3.5 text-xs text-gray-600">' + r.date + '</td>' +
                    '<td class="px-4 py-3.5 text-xs text-gray-600">' + r.driver + '</td>' +
                    '<td class="px-4 py-3.5 text-xs font-semibold text-gray-900">' + r.route + '</td>' +
                    '<td class="px-3 py-3.5 text-right text-xs tabular-nums text-gray-600">\u20b1' + r.collection.toLocaleString('en-US') + '</td>' +
                    '<td class="px-3 py-3.5 text-right text-xs tabular-nums text-gray-400">\u20b1' + r.expenses.toLocaleString('en-US') + '</td>' +
                    '<td class="px-5 py-3.5 text-right text-xs font-bold tabular-nums text-emerald-600">\u20b1' + r.net.toLocaleString('en-US') + '</td>';
                grid.appendChild(tr);
            });
            // totals row
            var tr = document.createElement('tr');
            tr.className = 'border-t border-gray-100 bg-gray-50/50';
            tr.innerHTML =
                '<td class="px-5 py-3.5 text-xs font-bold text-gray-900" colspan="3">Totals (' + total + ' records)</td>' +
                '<td class="px-3 py-3.5 text-right text-xs font-bold text-gray-900 tabular-nums">\u20b1' + window.remittanceReportTotals.collection.toLocaleString('en-US') + '</td>' +
                '<td class="px-3 py-3.5 text-right text-xs font-bold text-amber-600 tabular-nums">\u20b1' + window.remittanceReportTotals.expenses.toLocaleString('en-US') + '</td>' +
                '<td class="px-5 py-3.5 text-right text-xs font-bold text-emerald-700 tabular-nums">\u20b1' + window.remittanceReportTotals.net.toLocaleString('en-US') + '</td>';
            grid.appendChild(tr);
        }

        if (info) {
            if (total === 0) { info.innerHTML = 'No records to display'; } else {
                info.innerHTML = 'Showing <strong class="text-gray-700">' + (start + 1) + '</strong>\u2013<strong class="text-gray-700">' + end + '</strong> of <strong class="text-gray-700">' + total + '</strong>';
            }
        }
        if (!nav) return;
        if (pages <= 1) { nav.innerHTML = ''; return; }

        var base = 'flex items-center justify-center w-7 h-7 rounded-lg text-xs font-semibold border transition-all duration-150';
        var actS = base + ' bg-gray-900 text-white border-gray-900';
        var defS = base + ' bg-white text-gray-600 border-gray-200 hover:bg-gray-50 hover:text-gray-900 hover:border-gray-300';
        var disS = base + ' bg-gray-50 text-gray-300 border-gray-100 cursor-not-allowed pointer-events-none';

        var html = '';
        html += '<button data-p="' + (page - 1) + '" class="' + (page === 1 ? disS : defS) + '">\u2039</button>';
        for (var i = 1; i <= pages; i++) {
            html += '<button data-p="' + i + '" class="' + (i === page ? actS : defS) + '">' + i + '</button>';
        }
        html += '<button data-p="' + (page + 1) + '" class="' + (page === pages ? disS : defS) + '">\u203a</button>';
        nav.innerHTML = html;
        Array.from(nav.querySelectorAll('button[data-p]')).forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                var p = parseInt(btn.dataset.p);
                if (p < 1 || p > pages) return;
                page = p;
                render();
            });
        });
    }

    render();
})();
</script>
@endsection