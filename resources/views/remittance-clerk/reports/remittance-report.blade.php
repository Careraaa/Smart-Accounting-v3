@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
.rr-page {
    font-family: 'Sora', sans-serif;
    padding-top: 12px;
    margin-top: 8px;
}

.rr-topbar {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 24px;
    flex-wrap: wrap;
}
.rr-topbar-title {
    font-size: 1.35rem;
    font-weight: 800;
    color: #111827;
    letter-spacing: -0.02em;
    margin: 0 0 2px;
}
.rr-topbar-sub {
    font-size: 0.78rem;
    color: #9ca3af;
    margin: 0;
    max-width: 520px;
    line-height: 1.45;
}
.rr-topbar-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}
.rr-btn-print {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    background: #c8292a;
    color: #fff;
    border: none;
    border-radius: 10px;
    font-family: 'Sora', sans-serif;
    font-size: 0.8rem;
    font-weight: 700;
    text-decoration: none;
    cursor: pointer;
    white-space: nowrap;
    box-shadow: 0 4px 14px rgba(200, 41, 42, 0.28);
    transition: background 0.15s, box-shadow 0.15s, transform 0.12s;
}
.rr-btn-print:hover {
    background: #a81f20;
    color: #fff;
    box-shadow: 0 8px 22px rgba(200, 41, 42, 0.34);
    transform: translateY(-1px);
}
.rr-btn-print i { font-size: 15px; line-height: 1; }

.rr-filter-bar {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 14px 16px;
    display: flex;
    align-items: flex-end;
    gap: 12px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}
.rr-filter-group {
    display: flex;
    flex-direction: column;
    gap: 5px;
    min-width: 130px;
    flex: 1;
}
.rr-filter-group label {
    font-size: 0.67rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #9ca3af;
    margin: 0;
}
.rr-filter-group select {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 8px 10px;
    font-size: 0.8rem;
    font-family: 'Sora', sans-serif;
    color: #111827;
    background: #f9fafb;
    outline: none;
    cursor: pointer;
    transition: border-color 0.15s, box-shadow 0.15s, background 0.15s;
}
.rr-filter-group select:focus {
    border-color: #c8292a;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(200, 41, 42, 0.08);
}

.rr-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 22px;
}
@media (max-width: 1100px) { .rr-stats { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 560px)  { .rr-stats { grid-template-columns: 1fr; } }
.rr-stat {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 16px 18px;
    position: relative;
    overflow: hidden;
}
.rr-stat::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 3px;
    border-radius: 0 0 14px 14px;
}
.rr-stat.s-col::after { background: #16a34a; }
.rr-stat.s-exp::after { background: #c8292a; }
.rr-stat.s-net::after { background: #0284c7; }
.rr-stat.s-short::after { background: #d97706; }
.rr-stat-label {
    font-size: 0.67rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.09em;
    color: #9ca3af;
    margin-bottom: 6px;
}
.rr-stat-value {
    font-size: 1.25rem;
    font-weight: 800;
    color: #111827;
    font-family: 'DM Mono', monospace;
    line-height: 1.2;
    letter-spacing: -0.02em;
}
.rr-stat-sub {
    font-size: 0.72rem;
    color: #9ca3af;
    margin-top: 4px;
}

.rr-table-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    overflow: hidden;
}
.rr-table-head {
    padding: 14px 18px;
    border-bottom: 1px solid #f3f4f6;
}
.rr-table-title {
    margin: 0;
    font-size: 0.88rem;
    font-weight: 700;
    color: #111827;
    display: flex;
    align-items: center;
    gap: 8px;
}
.rr-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #c8292a;
    flex-shrink: 0;
}
.rr-table-scroll { overflow-x: auto; }
.rr-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.825rem;
}
.rr-table thead tr {
    background: #f8f9fb;
    border-bottom: 1px solid #e5e7eb;
}
.rr-table thead th {
    padding: 10px 14px;
    font-size: 0.66rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.09em;
    color: #6b7280;
    white-space: nowrap;
}
.rr-table tbody tr {
    border-bottom: 1px solid #f3f4f6;
    transition: background 0.1s;
}
.rr-table tbody tr:last-child { border-bottom: none; }
.rr-table tbody tr:hover { background: #fafafa; }
.rr-table tbody td {
    padding: 10px 14px;
    color: #374151;
    vertical-align: middle;
}
.rr-mono {
    font-family: 'DM Mono', monospace;
    font-size: 0.8rem;
    font-variant-numeric: tabular-nums;
}
.rr-mono.muted { color: #9ca3af; }
.rr-mono.green { color: #15803d; font-weight: 700; }
.rr-mono.red { color: #c8292a; font-weight: 700; }
.rr-empty {
    text-align: center;
    padding: 48px 20px;
    color: #9ca3af;
    font-size: 0.84rem;
}
.rr-empty i {
    display: block;
    font-size: 28px;
    opacity: 0.3;
    margin-bottom: 8px;
}
.rr-chip {
    display: inline-block;
    font-size: 0.72rem;
    color: #6b7280;
    background: #f3f4f6;
    padding: 2px 8px;
    border-radius: 6px;
    margin-top: 8px;
}
</style>
@endpush

@section('content')
@php
    $groupedRemittances = $remittances->groupBy(function ($item) {
        return $item->remittance_date->format('Y-m-d');
    })->map(function ($group) {
        return [
            'remittance_date' => $group->first()->remittance_date,
            'total_collection' => $group->sum('total_collection'),
            'total_expenses' => $group->sum('total_expenses'),
            'net_remittance' => $group->sum('net_remittance'),
            'is_short_remittance' => $group->where('is_short_remittance', true)->count() > 0,
        ];
    })->sortBy('remittance_date')->values();

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

<div class="rr-page">
    <div class="rr-topbar">
        <div>
            <h1 class="rr-topbar-title">Remittance Report</h1>
            <p class="rr-topbar-sub">Filter weekly, monthly, or yearly totals and print a summary for the selected period.</p>
            <span class="rr-chip">{{ $periodLabel }}</span>
        </div>
        <div class="rr-topbar-actions">
            <a href="{{ $printUrl }}" class="rr-btn-print" target="_blank" rel="noopener">
                <i class="feather-printer"></i> Print report
            </a>
        </div>
    </div>

    <div class="rr-filter-bar">
        <div class="rr-filter-group">
            <label for="rr-period">Period</label>
            <select id="period">
                <option value="weekly" {{ $period === 'weekly' ? 'selected' : '' }}>Weekly</option>
                <option value="monthly" {{ $period === 'monthly' ? 'selected' : '' }}>Monthly</option>
                <option value="yearly" {{ $period === 'yearly' ? 'selected' : '' }}>Yearly</option>
            </select>
        </div>
        <div class="rr-filter-group" id="weekSelect" style="display:{{ $period === 'weekly' ? 'flex' : 'none' }};">
            <label for="rr-week">Week</label>
            <select id="week">
                @for ($i = 1; $i <= 52; $i++)
                    <option value="{{ $i }}" {{ (int) $week === $i ? 'selected' : '' }}>Week {{ $i }}</option>
                @endfor
            </select>
        </div>
        <div class="rr-filter-group" id="monthSelect" style="display:{{ $period === 'monthly' ? 'flex' : 'none' }};">
            <label for="rr-month">Month</label>
            <select id="month">
                @for ($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ (int) $month === $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                @endfor
            </select>
        </div>
        <div class="rr-filter-group">
            <label for="rr-year">Year</label>
            <select id="year">
                @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
                    <option value="{{ $i }}" {{ (int) $year === $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
        </div>
    </div>

    <div class="rr-stats">
        <div class="rr-stat s-col">
            <div class="rr-stat-label">Total collection</div>
            <div class="rr-stat-value">₱{{ number_format($totalCollection, 2) }}</div>
            <div class="rr-stat-sub">For selected period</div>
        </div>
        <div class="rr-stat s-exp">
            <div class="rr-stat-label">Total expenses</div>
            <div class="rr-stat-value">₱{{ number_format($totalExpenses, 2) }}</div>
            <div class="rr-stat-sub">Trip and operating costs</div>
        </div>
        <div class="rr-stat s-net">
            <div class="rr-stat-label">Net remittance</div>
            <div class="rr-stat-value">₱{{ number_format($totalNetRemittance, 2) }}</div>
            <div class="rr-stat-sub">Collection minus expenses</div>
        </div>
        <div class="rr-stat s-short">
            <div class="rr-stat-label">Short remittances</div>
            <div class="rr-stat-value">{{ $shortRemittances }}</div>
            <div class="rr-stat-sub">Shortages in period</div>
        </div>
    </div>

    <div class="rr-table-card">
        <div class="rr-table-head">
            <h2 class="rr-table-title"><span class="rr-dot"></span> Daily totals</h2>
        </div>
        <div class="rr-table-scroll">
            <table class="rr-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th class="text-end">Collection</th>
                        <th class="text-end">Expenses</th>
                        <th class="text-end">Net remittance</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($groupedRemittances as $remittance)
                        <tr>
                            <td><strong>{{ $remittance['remittance_date']->format('M d, Y') }}</strong></td>
                            <td class="text-end rr-mono green">₱{{ number_format($remittance['total_collection'], 2) }}</td>
                            <td class="text-end rr-mono muted">₱{{ number_format($remittance['total_expenses'], 2) }}</td>
                            <td class="text-end rr-mono {{ $remittance['is_short_remittance'] ? 'red' : 'green' }}">
                                ₱{{ number_format($remittance['net_remittance'], 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="rr-empty">
                                    <i class="feather-file-text"></i>
                                    No remittances found for this period. Try adjusting the filters.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
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
