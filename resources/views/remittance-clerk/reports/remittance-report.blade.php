@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
@keyframes fadeSlideUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes scaleIn { 0%{opacity:0;transform:scale(0.92)} 100%{opacity:1;transform:scale(1)} }
@keyframes bounceIn { 0%{opacity:0;transform:scale(0.6)} 60%{transform:scale(1.05)} 80%{transform:scale(0.95)} 100%{opacity:1;transform:scale(1)} }
@keyframes modalIn { 0%{opacity:0;transform:scale(0.96) translateY(8px)} 100%{opacity:1;transform:scale(1) translateY(0)} }
@keyframes overlayIn { 0%{opacity:0} 100%{opacity:1} }
.rc-fade-up { animation:fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.rc-scale-in { animation:scaleIn 0.35s cubic-bezier(0.16,1,0.3,1) both; }
.rc-bounce { animation:bounceIn 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.rm-modal { animation:modalIn 0.3s cubic-bezier(0.16,1,0.3,1) both; }
.rm-overlay { animation:overlayIn 0.2s ease both; }
.rm-row { cursor:pointer; transition:background .15s ease; }
.rm-row:hover { background:#f8fafc; }
.rm-row:active { background:#f1f5f9; }
</style>
@endpush

@section('content')
@php
    $periodLabel = match ($period) {
        'daily' => \Carbon\Carbon::createFromDate($year, $month, $day)->format('F j, Y'),
        'weekly' => 'Week ' . $week . ', ' . $year,
        'monthly' => \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y'),
        'yearly' => (string) $year,
        default => '',
    };

    $remittancesJson = $remittances->groupBy(fn($r) => $r->remittance_date->format('Y-m-d'))->map(function ($group) {
        return $group->map(fn($r) => [
            'driver' => $r->driver?->name ?? ($r->driver?->first_name.' '.$r->driver?->last_name ?? 'N/A'),
            'pao' => $r->pao?->name ?? ($r->pao?->first_name.' '.$r->pao?->last_name ?? 'N/A'),
            'route' => $r->route?->route_name ?? 'N/A',
            'vehicle' => $r->vehicle?->plate_number ?? 'N/A',
            'collection' => (float) $r->total_collection,
            'expenses' => (float) $r->total_expenses,
            'net' => (float) $r->net_remittance,
            'short' => (bool) $r->is_short_remittance,
        ])->values();
    });
@endphp

{{-- Top bar --}}
    <div class="rc-fade-up flex items-start justify-between gap-4 mb-5 flex-wrap">
        <div>
            <h1 class="text-xl font-extrabold text-gray-900 tracking-tight m-0">Remittance Report</h1>
            <p class="text-xs text-gray-400 max-w-[520px] leading-relaxed m-0 mt-0.5">Filter weekly, monthly, or yearly totals and print a summary for the selected period.</p>
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
        <div class="flex flex-col gap-1 min-w-[130px] flex-1" id="daySelect" style="display:{{ $period === 'daily' ? 'flex' : 'none' }};">
            <label class="text-[0.6rem] font-bold uppercase tracking-wider text-gray-400">Day</label>
            <select id="day" class="border border-gray-200 rounded-lg px-2.5 py-2 text-xs text-gray-800 bg-gray-50 focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all cursor-pointer">
                @for ($i = 1; $i <= 31; $i++)
                    <option value="{{ $i }}" {{ (int) ($day ?? now()->day) === $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
        </div>
        <div class="flex flex-col gap-1 min-w-[130px] flex-1" id="monthSelect" style="display:{{ in_array($period, ['daily', 'monthly']) ? 'flex' : 'none' }};">
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
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        <div class="scale-in stat-card bg-white rounded-xl border border-gray-100 p-3.5 shadow-sm hover:shadow-md hover:border-emerald-200 transition-all duration-300">
            <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Total Collection</p>
            <p class="text-lg font-bold text-emerald-600 tabular-nums mt-1">₱{{ number_format($totalCollection, 2) }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Gross collections</p>
        </div>
        <div class="scale-in stat-card bg-white rounded-xl border border-gray-100 p-3.5 shadow-sm hover:shadow-md hover:border-red-200 transition-all duration-300">
            <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Total Expenses</p>
            <p class="text-lg font-bold text-red-600 tabular-nums mt-1">₱{{ number_format($totalExpenses, 2) }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Trip and operating costs</p>
        </div>
        <div class="scale-in stat-card bg-white rounded-xl border border-gray-100 p-3.5 shadow-sm hover:shadow-md hover:border-blue-200 transition-all duration-300">
            <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Net Remittance</p>
            <p class="text-lg font-bold text-blue-600 tabular-nums mt-1">₱{{ number_format($totalNetRemittance, 2) }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Collection minus expenses</p>
        </div>
        <div class="scale-in stat-card bg-white rounded-xl border border-gray-100 p-3.5 shadow-sm hover:shadow-md hover:border-amber-200 transition-all duration-300">
            <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Short Remittances</p>
            <p class="text-lg font-bold text-amber-600 tabular-nums mt-1">{{ $shortRemittances }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Shortages in period</p>
        </div>
    </div>

    {{-- Table card --}}
    <div class="rc-fade-up bg-white border border-gray-200 rounded-xl overflow-hidden" style="animation-delay:0.3s">
        <div class="px-4 py-3.5 border-b border-gray-100 flex items-center justify-between">
            <h2 class="flex items-center gap-2 text-sm font-bold text-gray-900 m-0">
                <span class="w-2 h-2 rounded-full bg-red-600 inline-block"></span>
                Daily totals
            </h2>
            <span class="text-[0.6rem] text-gray-400">Click a row to view breakdown</span>
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
                        @php $dateKey = $remittance['remittance_date']->format('Y-m-d'); @endphp
                        <tr class="rm-row border-b border-gray-100 transition-colors" data-date="{{ $dateKey }}" onclick="openRemittanceModal('{{ $dateKey }}')">
                            <td class="px-4 py-2.5 text-xs font-semibold text-gray-800">{{ $remittance['remittance_date']->format('M d, Y') }}</td>
                            <td class="px-4 py-2.5 text-right text-xs tabular-nums font-bold text-emerald-600">₱{{ number_format($remittance['total_collection'], 2) }}</td>
                            <td class="px-4 py-2.5 text-right text-xs tabular-nums text-gray-400">₱{{ number_format($remittance['total_expenses'], 2) }}</td>
                            <td class="px-4 py-2.5 text-right text-xs tabular-nums font-bold {{ $remittance['is_short_remittance'] ? 'text-red-600' : 'text-emerald-600' }}">
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
const __rmData = {!! $remittancesJson !!};

function openRemittanceModal(dateKey) {
    const items = __rmData[dateKey];
    if (!items || !items.length) return;
    try { window.sndPlay(); } catch(e) {}

    const overlay = document.createElement('div');
    overlay.className = 'rm-overlay fixed inset-0 z-[999] bg-black/40 backdrop-blur-sm flex items-center justify-center';
    overlay.style.animation = 'overlayIn 0.2s ease both';
    overlay.onclick = function(e) { if (e.target === overlay) closeRemittanceModal(overlay); };

    const d = new Date(dateKey + 'T12:00:00');
    const formatted = d.toLocaleDateString('en-US', { weekday:'short', month:'short', day:'numeric', year:'numeric' });

    const totalCol = items.reduce((s, i) => s + i.collection, 0);
    const totalExp = items.reduce((s, i) => s + i.expenses, 0);
    const totalNet = items.reduce((s, i) => s + i.net, 0);
    const hasShort = items.some(i => i.short);

    let rows = items.map(function(r, idx) {
        const netClass = r.short ? 'text-red-600' : 'text-emerald-600';
        return '<tr class="border-b border-gray-50">' +
            '<td class="px-3 py-2 text-xs text-gray-500 tabular-nums">' + (idx + 1) + '</td>' +
            '<td class="px-3 py-2 text-xs font-semibold text-gray-800">' + esc(r.driver) + '</td>' +
            '<td class="px-3 py-2 text-xs text-gray-500">' + esc(r.pao) + '</td>' +
            '<td class="px-3 py-2 text-xs text-gray-500">' + esc(r.route) + '</td>' +
            '<td class="px-3 py-2 text-xs text-gray-500 tabular-nums">' + esc(r.vehicle) + '</td>' +
            '<td class="px-3 py-2 text-xs text-right tabular-nums font-medium text-emerald-600">₱' + fmt(r.collection) + '</td>' +
            '<td class="px-3 py-2 text-xs text-right tabular-nums text-gray-400">₱' + fmt(r.expenses) + '</td>' +
            '<td class="px-3 py-2 text-xs text-right tabular-nums font-bold ' + netClass + '">₱' + fmt(r.net) + '</td>' +
            '<td class="px-3 py-2 text-xs text-center">' + (r.short ? '<span class="inline-block w-1.5 h-1.5 rounded-full bg-red-500" title="Short"></span>' : '<span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-400" title="OK"></span>') + '</td>' +
        '</tr>';
    }).join('');

    overlay.innerHTML = '<div class="rm-modal bg-white rounded-2xl shadow-2xl w-full max-w-4xl mx-4 max-h-[85vh] flex flex-col overflow-hidden" onclick="event.stopPropagation()">' +
        '<div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 shrink-0">' +
            '<div>' +
                '<h3 class="text-sm font-bold text-gray-900 m-0">' + formatted + '</h3>' +
                '<p class="text-[0.65rem] text-gray-400 m-0 mt-0.5">' + items.length + ' remittance record' + (items.length > 1 ? 's' : '') + '</p>' +
            '</div>' +
            '<button onclick="closeRemittanceModal(this.closest(\'.fixed\'))" class="w-7 h-7 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-400 hover:text-gray-600 transition-all border-0 cursor-pointer"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>' +
        '</div>' +
        '<div class="overflow-x-auto overflow-y-auto flex-1">' +
            '<table class="w-full text-xs">' +
                '<thead class="sticky top-0 z-10"><tr class="bg-gray-50/90 border-b border-gray-200">' +
                    '<th class="text-left px-3 py-2 text-[0.5rem] font-bold uppercase tracking-wider text-gray-500">#</th>' +
                    '<th class="text-left px-3 py-2 text-[0.5rem] font-bold uppercase tracking-wider text-gray-500">Driver</th>' +
                    '<th class="text-left px-3 py-2 text-[0.5rem] font-bold uppercase tracking-wider text-gray-500">PAO</th>' +
                    '<th class="text-left px-3 py-2 text-[0.5rem] font-bold uppercase tracking-wider text-gray-500">Route</th>' +
                    '<th class="text-left px-3 py-2 text-[0.5rem] font-bold uppercase tracking-wider text-gray-500">Vehicle</th>' +
                    '<th class="text-right px-3 py-2 text-[0.5rem] font-bold uppercase tracking-wider text-gray-500">Collection</th>' +
                    '<th class="text-right px-3 py-2 text-[0.5rem] font-bold uppercase tracking-wider text-gray-500">Expenses</th>' +
                    '<th class="text-right px-3 py-2 text-[0.5rem] font-bold uppercase tracking-wider text-gray-500">Net</th>' +
                    '<th class="text-center px-3 py-2 text-[0.5rem] font-bold uppercase tracking-wider text-gray-500">Sts</th>' +
                '</tr></thead>' +
                '<tbody>' + rows +
                    '<tr class="bg-gray-50/50 border-t-2 border-gray-200">' +
                        '<td colspan="5" class="px-3 py-2.5 text-[0.6rem] font-bold uppercase tracking-wider text-gray-500 text-right">Daily total</td>' +
                        '<td class="px-3 py-2.5 text-xs text-right tabular-nums font-bold text-emerald-600">₱' + fmt(totalCol) + '</td>' +
                        '<td class="px-3 py-2.5 text-xs text-right tabular-nums text-gray-400">₱' + fmt(totalExp) + '</td>' +
                        '<td class="px-3 py-2.5 text-xs text-right tabular-nums font-bold ' + (hasShort ? 'text-red-600' : 'text-emerald-600') + '">₱' + fmt(totalNet) + '</td>' +
                        '<td class="px-3 py-2.5 text-xs text-center">' + (hasShort ? '<span class="inline-flex items-center gap-1 text-[0.5rem] font-bold uppercase tracking-wider text-red-500">Short</span>' : '<span class="inline-flex items-center gap-1 text-[0.5rem] font-bold uppercase tracking-wider text-emerald-500">Clear</span>') + '</td>' +
                    '</tr>' +
                '</tbody>' +
            '</table>' +
        '</div>' +
    '</div>';

    document.body.appendChild(overlay);
}

function closeRemittanceModal(el) {
    if (el && el.parentNode) el.parentNode.removeChild(el);
}

function esc(str) {
    if (!str) return 'N/A';
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}

function fmt(n) {
    return Number(n).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

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
        let url = '{{ route('reports.remittance-report') }}?period=' + period + '&year=' + year;
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
})();
</script>
@endpush
