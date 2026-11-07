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
        'weekly' => 'Week ' . $week . ' (' . \Carbon\Carbon::now()->setISODate($year, $week)->startOfWeek()->format('M j') . ' - ' . \Carbon\Carbon::now()->setISODate($year, $week)->endOfWeek()->format('M j') . ')',
        'monthly' => \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y'),
        'yearly' => (string) $year,
        default => '',
    };

    $weekCount = \Carbon\Carbon::create($year, 12, 28)->isoWeek();
    $weekOptions = collect(range(1, $weekCount))->map(function ($weekNumber) use ($year) {
        $startOfWeek = \Carbon\Carbon::now()->setISODate($year, $weekNumber)->startOfWeek();

        return [
            'number' => $weekNumber,
            'label' => sprintf(
                'Week %d (%s - %s)',
                $weekNumber,
                $startOfWeek->format('M j'),
                $startOfWeek->copy()->endOfWeek()->format('M j')
            ),
        ];
    });

    $remittancesJson = $remittances->groupBy(fn($r) => $r->remittance_date->format('Y-m-d'))->map(function ($group) {
        return $group->map(fn($r) => [
            'show_url' => route('remittances.show', $r),
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

<div class="max-w-7xl mx-auto space-y-5">
    <header class="rc-fade-up flex items-start justify-between gap-4 flex-wrap">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-gray-400 m-0 mb-1">Remittance Clerk</p>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight m-0">Remittance Report</h1>
            <p class="text-sm text-gray-500 mt-1 mb-0">Review approved collections, expenses, and daily net remittances.</p>
        </div>
        <div class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white border border-gray-200 text-xs font-semibold text-gray-700 shadow-sm">
            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            {{ $periodLabel }}
        </div>
    </header>

    <section class="rc-scale-in bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden" aria-label="Report filters">
        <div class="px-5 py-3.5 border-b border-gray-100 flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4h18M6 9h12m-9 5h6m-3 0v6"/></svg>
            <h2 class="text-sm font-bold text-gray-900 m-0">Report period</h2>
        </div>
        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="flex flex-col gap-1.5">
                <label for="period" class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Period</label>
                <select id="period" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 bg-gray-50 focus:outline-none focus:border-gray-400 focus:ring-2 focus:ring-gray-100 transition-all cursor-pointer">
                    <option value="daily" {{ $period === 'daily' ? 'selected' : '' }}>Daily</option>
                    <option value="weekly" {{ $period === 'weekly' ? 'selected' : '' }}>Weekly</option>
                    <option value="monthly" {{ $period === 'monthly' ? 'selected' : '' }}>Monthly</option>
                    <option value="yearly" {{ $period === 'yearly' ? 'selected' : '' }}>Yearly</option>
                </select>
            </div>
            <div class="flex flex-col gap-1.5" id="weekSelect" style="display:{{ $period === 'weekly' ? 'flex' : 'none' }};">
                <label for="week" class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Week</label>
                <select id="week" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 bg-gray-50 focus:outline-none focus:border-gray-400 focus:ring-2 focus:ring-gray-100 transition-all cursor-pointer">
                    @foreach ($weekOptions as $weekOption)
                        <option value="{{ $weekOption['number'] }}" {{ (int) $week === $weekOption['number'] ? 'selected' : '' }}>{{ $weekOption['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col gap-1.5" id="daySelect" style="display:{{ $period === 'daily' ? 'flex' : 'none' }};">
                <label for="day" class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Day</label>
                <select id="day" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 bg-gray-50 focus:outline-none focus:border-gray-400 focus:ring-2 focus:ring-gray-100 transition-all cursor-pointer">
                    @for ($i = 1; $i <= 31; $i++)
                        <option value="{{ $i }}" {{ (int) ($day ?? now()->day) === $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="flex flex-col gap-1.5" id="monthSelect" style="display:{{ in_array($period, ['daily', 'monthly']) ? 'flex' : 'none' }};">
                <label for="month" class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Month</label>
                <select id="month" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 bg-gray-50 focus:outline-none focus:border-gray-400 focus:ring-2 focus:ring-gray-100 transition-all cursor-pointer">
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ (int) $month === $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                    @endfor
                </select>
            </div>
            <div class="flex flex-col gap-1.5">
                <label for="year" class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Year</label>
                <select id="year" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 bg-gray-50 focus:outline-none focus:border-gray-400 focus:ring-2 focus:ring-gray-100 transition-all cursor-pointer">
                    @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
                        <option value="{{ $i }}" {{ (int) $year === $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4" aria-label="Remittance totals">
        <article class="scale-in stat-card bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-gray-100 text-gray-700 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500 m-0">Total Collection</p>
                    <p class="text-xl font-bold text-gray-900 tabular-nums mt-1 mb-0">₱{{ number_format($totalCollection, 2) }}</p>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-3 mb-0">Gross collections for this period</p>
        </article>
        <article class="scale-in stat-card bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-gray-100 text-gray-700 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14l2-2 2 2 4-4m-9 8h10M5 4h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500 m-0">Total Expenses</p>
                    <p class="text-xl font-bold text-gray-900 tabular-nums mt-1 mb-0">₱{{ number_format($totalExpenses, 2) }}</p>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-3 mb-0">Trip and operating costs</p>
        </article>
        <article class="scale-in stat-card bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-gray-100 text-gray-700 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 19V5m0 14h16M8 15l3-3 3 2 5-6"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500 m-0">Net Remittance</p>
                    <p class="text-xl font-bold {{ $shortRemittances > 0 ? 'text-red-600' : 'text-emerald-600' }} tabular-nums mt-1 mb-0">₱{{ number_format($totalNetRemittance, 2) }}</p>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-3 mb-0">{{ $shortRemittances > 0 ? 'Short remittance recorded in this period' : 'No short remittances in this period' }}</p>
        </article>
        <article class="scale-in stat-card bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-gray-100 text-gray-700 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.3 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.7 3.86a2 2 0 00-3.4 0z"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500 m-0">Short Remittances</p>
                    <p class="text-xl font-bold text-gray-900 tabular-nums mt-1 mb-0">{{ $shortRemittances }}</p>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-3 mb-0">Shortages recorded in this period</p>
        </article>
    </section>

    <section class="rc-fade-up bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden" style="animation-delay:0.2s">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between gap-3 flex-wrap">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-gray-100 text-gray-700 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-900 m-0">Daily Totals</h2>
                    <p class="text-xs text-gray-500 mt-0.5 mb-0">{{ $periodLabel }} · {{ $groupedRemittances->total() }} {{ \Illuminate\Support\Str::plural('day', $groupedRemittances->total()) }}</p>
                </div>
            </div>
            <p class="text-xs text-gray-500 m-0">Select a row to view its remittance breakdown</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm app-standard-table">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-gray-500">Date</th>
                        <th class="text-right px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-gray-500">Collection</th>
                        <th class="text-right px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-gray-500">Expenses</th>
                        <th class="text-right px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-gray-500">Net Remittance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($groupedRemittances as $remittance)
                        @php $dateKey = $remittance['remittance_date']->format('Y-m-d'); @endphp
                        <tr class="rm-row transition-colors" data-date="{{ $dateKey }}" onclick="openRemittanceModal('{{ $dateKey }}')">
                            <td class="px-5 py-4 text-sm font-semibold text-gray-900">{{ $remittance['remittance_date']->format('M d, Y') }}</td>
                            <td class="px-5 py-4 text-right text-sm tabular-nums font-semibold text-gray-900">₱{{ number_format($remittance['total_collection'], 2) }}</td>
                            <td class="px-5 py-4 text-right text-sm tabular-nums font-semibold text-gray-900">₱{{ number_format($remittance['total_expenses'], 2) }}</td>
                            <td class="px-5 py-4 text-right text-sm tabular-nums font-bold {{ $remittance['is_short_remittance'] ? 'text-red-600' : 'text-emerald-600' }}">₱{{ number_format($remittance['net_remittance'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-14">
                                <div class="flex flex-col items-center gap-3">
                                    <svg class="w-6 h-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                                    <p class="text-sm font-semibold text-gray-800">No remittances for this period</p>
                                    <p class="text-xs text-gray-500">Try choosing a different date range.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="flex items-center justify-between gap-3 flex-wrap px-5 py-3 border-t border-gray-100 bg-gray-50/50">
            <div class="text-xs text-gray-500">
                Showing
                <strong class="text-gray-900">{{ $groupedRemittances->firstItem() ?? 0 }}</strong>–<strong class="text-gray-900">{{ $groupedRemittances->lastItem() ?? 0 }}</strong>
                of
                <strong class="text-gray-900">{{ $groupedRemittances->total() }}</strong> daily totals
            </div>
            @if($groupedRemittances->hasPages())
                @php
                    $currentPage = $groupedRemittances->currentPage();
                    $lastPage = $groupedRemittances->lastPage();
                    $windowStart = max(1, $currentPage - 2);
                    $windowEnd = min($lastPage, $windowStart + 4);
                    $windowStart = max(1, $windowEnd - 4);
                    $pageButtonClass = 'flex items-center justify-center w-8 h-8 rounded-lg text-xs font-semibold border transition-all duration-150';
                @endphp
                <nav class="flex items-center gap-1" aria-label="Pagination">
                    <a href="{{ $groupedRemittances->url(max(1, $currentPage - 1)) }}" aria-label="Previous page" class="{{ $pageButtonClass }} {{ $groupedRemittances->onFirstPage() ? 'bg-gray-50 text-gray-300 border-gray-100 cursor-not-allowed pointer-events-none' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50 hover:text-gray-900 hover:border-gray-300' }}">‹</a>
                    @for($pageNumber = $windowStart; $pageNumber <= $windowEnd; $pageNumber++)
                        <a href="{{ $groupedRemittances->url($pageNumber) }}" aria-current="{{ $pageNumber === $currentPage ? 'page' : 'false' }}" class="{{ $pageButtonClass }} {{ $pageNumber === $currentPage ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50 hover:text-gray-900 hover:border-gray-300' }}">{{ $pageNumber }}</a>
                    @endfor
                    <a href="{{ $groupedRemittances->url(min($lastPage, $currentPage + 1)) }}" aria-label="Next page" class="{{ $pageButtonClass }} {{ !$groupedRemittances->hasMorePages() ? 'bg-gray-50 text-gray-300 border-gray-100 cursor-not-allowed pointer-events-none' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50 hover:text-gray-900 hover:border-gray-300' }}">›</a>
                </nav>
            @endif
        </div>
    </section>
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
            '<td class="px-5 py-3 text-sm text-gray-500 tabular-nums">' + (idx + 1) + '</td>' +
            '<td class="px-5 py-3 text-sm font-semibold text-gray-800">' + esc(r.driver) + '</td>' +
            '<td class="px-5 py-3 text-sm text-gray-500">' + esc(r.pao) + '</td>' +
            '<td class="px-5 py-3 text-sm text-gray-500">' + esc(r.route) + '</td>' +
            '<td class="px-5 py-3 text-sm text-gray-500 tabular-nums">' + esc(r.vehicle) + '</td>' +
            '<td class="px-5 py-3 text-sm text-right tabular-nums font-medium text-emerald-600">₱' + fmt(r.collection) + '</td>' +
            '<td class="px-5 py-3 text-sm text-right tabular-nums text-gray-400">₱' + fmt(r.expenses) + '</td>' +
            '<td class="px-5 py-3 text-sm text-right tabular-nums font-bold ' + netClass + '">₱' + fmt(r.net) + '</td>' +
            '<td class="px-5 py-3 text-sm text-center"><a href="' + r.show_url + '" class="text-blue-600 hover:text-blue-800 font-semibold no-underline">Details</a></td>' +
            '<td class="px-5 py-3 text-sm text-center">' + (r.short ? '<span class="inline-block w-1.5 h-1.5 rounded-full bg-red-500" title="Short"></span>' : '<span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-400" title="OK"></span>') + '</td>' +
        '</tr>';
    });

    overlay.innerHTML = '<div class="rm-modal bg-white rounded-2xl shadow-2xl w-full max-w-4xl mx-4 max-h-[85vh] flex flex-col overflow-hidden" onclick="event.stopPropagation()">' +
        '<div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 shrink-0">' +
            '<div>' +
                '<h3 class="text-sm font-bold text-gray-900 m-0">' + formatted + '</h3>' +
                '<p class="text-[0.65rem] text-gray-400 m-0 mt-0.5">' + items.length + ' remittance record' + (items.length > 1 ? 's' : '') + '</p>' +
            '</div>' +
            '<button onclick="closeRemittanceModal(this.closest(\'.fixed\'))" class="w-7 h-7 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-400 hover:text-gray-600 transition-all border-0 cursor-pointer"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>' +
        '</div>' +
        '<div class="overflow-x-auto overflow-y-auto flex-1">' +
            '<table class="w-full text-sm app-standard-table">' +
                '<thead class="sticky top-0 z-10"><tr class="bg-gray-50 border-b border-gray-200">' +
                    '<th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">#</th>' +
                    '<th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Driver</th>' +
                    '<th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">PAO</th>' +
                    '<th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Route</th>' +
                    '<th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Vehicle</th>' +
                    '<th class="text-right px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Collection</th>' +
                    '<th class="text-right px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Expenses</th>' +
                    '<th class="text-right px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Net</th>' +
                    '<th class="text-center px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Details</th>' +
                    '<th class="text-center px-5 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Sts</th>' +
                '</tr></thead>' +
                '<tbody id="remittanceDetailBody">' + rows.slice(0, 10).join('') +
                '</tbody><tfoot><tr class="bg-gray-50/50 border-t-2 border-gray-200">' +
                        '<td colspan="5" class="px-3 py-2.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Daily total</td>' +
                        '<td class="px-5 py-3 text-sm text-right tabular-nums font-bold text-emerald-600">₱' + fmt(totalCol) + '</td>' +
                        '<td class="px-5 py-3 text-sm text-right tabular-nums text-gray-400">₱' + fmt(totalExp) + '</td>' +
                        '<td class="px-5 py-3 text-sm text-right tabular-nums font-bold ' + (hasShort ? 'text-red-600' : 'text-emerald-600') + '">₱' + fmt(totalNet) + '</td>' +
                        '<td class="px-5 py-3 text-sm text-center text-gray-300">—</td>' +
                        '<td class="px-5 py-3 text-sm text-center">' + (hasShort ? '<span class="inline-flex items-center gap-1 text-[0.5rem] font-bold uppercase tracking-wider text-red-500">Short</span>' : '<span class="inline-flex items-center gap-1 text-[0.5rem] font-bold uppercase tracking-wider text-emerald-500">Clear</span>') + '</td>' +
                    '</tr></tfoot>' +
            '</table>' +
        '</div>' +
        '<div class="flex items-center justify-between px-4 py-3 border-t border-gray-100 bg-gray-50/50">' +
            '<p class="text-xs text-gray-400" id="remittanceDetailInfo"></p>' +
            '<nav class="flex items-center gap-1" id="remittanceDetailNav" aria-label="Remittance detail pagination"></nav>' +
        '</div>' +
    '</div>';

    document.body.appendChild(overlay);

    const detailBody = overlay.querySelector('#remittanceDetailBody');
    const detailInfo = overlay.querySelector('#remittanceDetailInfo');
    const detailNav = overlay.querySelector('#remittanceDetailNav');
    const perPage = 10;
    let page = 1;

    function renderDetailPage() {
        const totalPages = Math.max(1, Math.ceil(rows.length / perPage));
        const start = (page - 1) * perPage;
        const end = Math.min(start + perPage, rows.length);
        detailBody.innerHTML = rows.slice(start, end).join('');
        detailInfo.innerHTML = `Showing <strong class="text-gray-700">${start + 1}</strong>–<strong class="text-gray-700">${end}</strong> of <strong class="text-gray-700">${rows.length}</strong> remittance records`;

        let html = '';
        const buttonClass = 'flex items-center justify-center w-7 h-7 rounded-lg text-xs font-semibold border transition-all duration-150';
        const activeClass = buttonClass + ' bg-gray-900 text-white border-gray-900';
        const defaultClass = buttonClass + ' bg-white text-gray-600 border-gray-200 hover:bg-gray-50 hover:text-gray-900 hover:border-gray-300';
        const disabledClass = buttonClass + ' bg-gray-50 text-gray-300 border-gray-100 cursor-not-allowed pointer-events-none';
        if (totalPages > 1) {
            html += `<button type="button" data-page="${page - 1}" aria-label="Previous page" class="${page === 1 ? disabledClass : defaultClass}">‹</button>`;
            const windowStart = Math.max(1, page - 2);
            const windowEnd = Math.min(totalPages, windowStart + 4);
            const adjustedStart = Math.max(windowEnd - 4, 1);
            for (let current = adjustedStart; current <= windowEnd; current++) {
                html += `<button type="button" data-page="${current}" aria-current="${current === page ? 'page' : 'false'}" class="${current === page ? activeClass : defaultClass}">${current}</button>`;
            }
            html += `<button type="button" data-page="${page + 1}" aria-label="Next page" class="${page === totalPages ? disabledClass : defaultClass}">›</button>`;
        }
        detailNav.innerHTML = html;
    }

    detailNav.addEventListener('click', function(event) {
        const button = event.target.closest('button[data-page]');
        if (!button) return;
        page = Number(button.dataset.page);
        renderDetailPage();
    });
    renderDetailPage();
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
