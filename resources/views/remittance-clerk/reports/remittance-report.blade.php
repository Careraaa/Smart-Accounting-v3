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
        'daily' => \Carbon\Carbon::parse($date)->format('M d, Y'),
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
        'date' => $date ?? date('Y-m-d'),
    ]);
@endphp

{{-- Top bar --}}
    <div class="rc-fade-up flex items-start justify-between gap-4 mb-5 flex-wrap">
        <div>
            <h1 class="text-xl font-extrabold text-gray-900 tracking-tight m-0">Remittance Report</h1>
            <p class="text-xs text-gray-400 max-w-[520px] leading-relaxed m-0 mt-0.5">Filter daily, weekly, monthly, or yearly totals and print a summary for the selected period.</p>
            <span class="inline-block text-[0.65rem] text-gray-400 bg-gray-100 px-2 py-0.5 rounded-md mt-2">{{ $periodLabel }}</span>
        </div>
    </div>

    {{-- Filter bar --}}
    <div class="rc-scale-in bg-white border border-gray-200 rounded-xl p-4 flex items-end gap-3 mb-5 flex-wrap" style="animation-delay:0.05s">
        <div class="flex flex-col gap-1 min-w-[130px] flex-1">
            <label class="text-[0.6rem] font-bold uppercase tracking-wider text-gray-400">Period</label>
            <select id="period" class="border border-gray-200 rounded-lg px-2.5 py-2 text-xs text-gray-800 bg-gray-50 focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all cursor-pointer">
                <option value="daily" {{ $period === 'daily' ? 'selected' : '' }}>Daily</option>
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
        <div class="flex flex-col gap-1 min-w-[130px] flex-1" id="dateSelect" style="display:{{ $period === 'daily' ? 'flex' : 'none' }};">
            <label class="text-[0.6rem] font-bold uppercase tracking-wider text-gray-400">Date</label>
            <input type="date" id="date" value="{{ $date ?? date('Y-m-d') }}"
                class="border border-gray-200 rounded-lg px-2.5 py-2 text-xs text-gray-800 bg-gray-50 focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all cursor-pointer">
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
                <tbody id="remittanceGrid"></tbody>
            </table>
        </div>
        <div id="remittanceNoResults" class="hidden">
            <div class="flex flex-col items-center py-12 text-center">
                <svg class="w-6 h-6 text-gray-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                <p class="text-xs text-gray-400">No approved remittance records yet.</p>
            </div>
        </div>
        <div class="flex items-center justify-between px-4 py-3 border-t border-gray-100 bg-gray-50/50 flex-wrap gap-3">
            <div class="text-[0.65rem] text-gray-400" id="remittanceInfo">Showing <strong class="text-gray-700">0</strong> records</div>
            <nav id="remittanceNav" class="flex items-center gap-1"></nav>
        </div>
    </div>

    {{-- Details Modal --}}
    <div id="detailsModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm" style="animation:fadeSlideUp 0.2s ease both;">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 max-h-[85vh] flex flex-col" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 shrink-0">
                <div>
                    <h3 class="text-sm font-extrabold text-gray-900 m-0">Daily Details</h3>
                    <p class="text-[0.65rem] text-gray-400 m-0 mt-0.5" id="modalDateLabel">—</p>
                </div>
                <button onclick="closeDetailsModal()" class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all cursor-pointer border-none">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="overflow-y-auto p-6" id="modalBody">
                <div class="flex items-center justify-center py-12 text-gray-400">
                    <svg class="w-6 h-6 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    <span class="text-xs ml-2">Loading...</span>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
window.remittanceData = {!! json_encode($grouped->map(fn($r) => [
    'date'       => $r['remittance_date']->format('Y-m-d'),
    'dateLabel'  => $r['remittance_date']->format('M d, Y'),
    'collection' => (float) $r['total_collection'],
    'expenses'   => (float) $r['total_expenses'],
    'net'        => (float) $r['net_remittance'],
    'is_short'   => $r['is_short_remittance'],
])->values()->all()) !!};

(function () {
    var grid   = document.getElementById('remittanceGrid');
    var noRes  = document.getElementById('remittanceNoResults');
    var info   = document.getElementById('remittanceInfo');
    var nav    = document.getElementById('remittanceNav');
    var PER = 10, page = 1;
    var data = window.remittanceData;

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
                tr.className = 'border-b border-gray-100 hover:bg-gray-50/50 transition-colors cursor-pointer';
                tr.setAttribute('data-date', r.date);
                tr.setAttribute('onclick', 'openDetailsModal(this)');
                var netClass = r.is_short ? 'text-red-600' : 'text-emerald-600';
                tr.innerHTML =
                    '<td class="px-4 py-2.5 text-xs font-semibold text-gray-800">' + r.dateLabel + '</td>' +
                    '<td class="px-4 py-2.5 text-right text-xs font-mono font-bold text-emerald-600">\u20b1' + r.collection.toLocaleString('en-US', {minimumFractionDigits:2}) + '</td>' +
                    '<td class="px-4 py-2.5 text-right text-xs font-mono text-gray-400">\u20b1' + r.expenses.toLocaleString('en-US', {minimumFractionDigits:2}) + '</td>' +
                    '<td class="px-4 py-2.5 text-right text-xs font-mono font-bold ' + netClass + '">\u20b1' + r.net.toLocaleString('en-US', {minimumFractionDigits:2}) + '</td>';
                grid.appendChild(tr);
            });
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

function openDetailsModal(row) {
    const date = row.dataset.date;
    const modal = document.getElementById('detailsModal');
    const dateLabel = document.getElementById('modalDateLabel');
    const body = document.getElementById('modalBody');

    dateLabel.textContent = date;
    body.innerHTML = '<div class="flex items-center justify-center py-12 text-gray-400"><svg class="w-6 h-6 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg><span class="text-xs ml-2">Loading...</span></div>';
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    fetch('{{ route('reports.remittance-report.daily-details') }}?date=' + date)
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (data.error) {
                body.innerHTML = '<div class="text-center py-12 text-gray-400 text-xs">' + data.error + '</div>';
                return;
            }
            if (!data.length) {
                body.innerHTML = '<div class="text-center py-12 text-gray-400 text-xs">No remittances found for this date.</div>';
                return;
            }
            var html = '<table class="w-full text-xs"><thead><tr class="bg-gray-50/80 border-b border-gray-200"><th class="text-left px-3 py-2 text-[0.55rem] font-bold uppercase tracking-wider text-gray-500">Driver</th><th class="text-left px-3 py-2 text-[0.55rem] font-bold uppercase tracking-wider text-gray-500">PAO</th><th class="text-left px-3 py-2 text-[0.55rem] font-bold uppercase tracking-wider text-gray-500">Vehicle</th><th class="text-right px-3 py-2 text-[0.55rem] font-bold uppercase tracking-wider text-gray-500">Collection</th><th class="text-right px-3 py-2 text-[0.55rem] font-bold uppercase tracking-wider text-gray-500">Expenses</th><th class="text-right px-3 py-2 text-[0.55rem] font-bold uppercase tracking-wider text-gray-500">Net</th></tr></thead><tbody>';
            data.forEach(function (r) {
                var netClass = r.is_short ? 'text-red-600' : 'text-emerald-600';
                html += '<tr class="border-b border-gray-100 hover:bg-gray-50/50 transition-colors"><td class="px-3 py-2 font-semibold text-gray-800">' + r.driver + '</td><td class="px-3 py-2 text-gray-600">' + r.pao + '</td><td class="px-3 py-2 text-gray-600">' + r.vehicle + '</td><td class="px-3 py-2 text-right font-mono font-bold text-emerald-600">\u20B1' + r.total_collection + '</td><td class="px-3 py-2 text-right font-mono text-gray-400">\u20B1' + r.total_expenses + '</td><td class="px-3 py-2 text-right font-mono font-bold ' + netClass + '">\u20B1' + r.net_remittance + '</td></tr>';
            });
            html += '</tbody></table>';
            body.innerHTML = html;
        })
        .catch(function () {
            body.innerHTML = '<div class="text-center py-12 text-red-400 text-xs">Failed to load details. Please try again.</div>';
        });
}

function closeDetailsModal() {
    var modal = document.getElementById('detailsModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

document.getElementById('detailsModal').addEventListener('click', closeDetailsModal);
</script>
@endpush