@extends('layouts.layout')

@push('styles')
    @include('remittance-clerk._ui-styles')
<style>
.rem-filter-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:18px 20px;margin-bottom:20px; }
.rem-filter-row  { display:flex;align-items:flex-end;gap:14px;flex-wrap:wrap; }
.rem-filter-group { display:flex;flex-direction:column;gap:6px;min-width:140px; }
.rem-filter-label { font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#9ca3af; }
</style>
@endpush

@section('content')
<div class="col-12">
<div class="remui-page">

    {{-- Topbar --}}
    <div class="prl-topbar">
        <div>
            <h1 class="prl-topbar-title">Remittance Report</h1>
            <p class="prl-topbar-sub">Filter weekly, monthly, or yearly performance and print summaries</p>
        </div>
        <div class="prl-topbar-actions">
            <a href="{{ route('reports.print.remittance-report', ['period' => $period, 'week' => $week, 'month' => $month, 'year' => $year]) }}"
               class="prl-btn-ghost" target="_blank">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print
            </a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="prl-stats">
        <div class="prl-stat s-green">
            <div class="prl-stat-icon"><svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
            <div>
                <div class="prl-stat-label">Total Collection</div>
                <div class="prl-stat-value" style="font-size:1.15rem;">₱{{ number_format($totalCollection, 0) }}</div>
                <div class="prl-stat-sub">for selected period</div>
            </div>
        </div>
        <div class="prl-stat s-red">
            <div class="prl-stat-icon"><svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg></div>
            <div>
                <div class="prl-stat-label">Total Expenses</div>
                <div class="prl-stat-value" style="font-size:1.15rem;">₱{{ number_format($totalExpenses, 0) }}</div>
                <div class="prl-stat-sub">for selected period</div>
            </div>
        </div>
        <div class="prl-stat s-blue">
            <div class="prl-stat-icon"><svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg></div>
            <div>
                <div class="prl-stat-label">Net Remittance</div>
                <div class="prl-stat-value" style="font-size:1.15rem;">₱{{ number_format($totalNetRemittance, 0) }}</div>
                <div class="prl-stat-sub">collections minus expenses</div>
            </div>
        </div>
        <div class="prl-stat s-amber">
            <div class="prl-stat-icon"><svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg></div>
            <div>
                <div class="prl-stat-label">Short Remittances</div>
                <div class="prl-stat-value">{{ $shortRemittances }}</div>
                <div class="prl-stat-sub">shortages in period</div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="rem-filter-card">
        <div class="rem-filter-row">
            <div class="rem-filter-group">
                <label class="rem-filter-label">Period</label>
                <select id="period" class="prl-filter-select" onchange="updateReport()">
                    <option value="weekly"  {{ $period === 'weekly'  ? 'selected' : '' }}>Weekly</option>
                    <option value="monthly" {{ $period === 'monthly' ? 'selected' : '' }}>Monthly</option>
                    <option value="yearly"  {{ $period === 'yearly'  ? 'selected' : '' }}>Yearly</option>
                </select>
            </div>
            <div class="rem-filter-group" id="weekSelect" style="display:{{ $period === 'weekly' ? 'flex' : 'none' }};">
                <label class="rem-filter-label">Week</label>
                <select id="week" class="prl-filter-select" onchange="updateReport()">
                    @for ($i = 1; $i <= 52; $i++)
                        <option value="{{ $i }}" {{ $week == $i ? 'selected' : '' }}>Week {{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="rem-filter-group" id="monthSelect" style="display:{{ $period === 'monthly' ? 'flex' : 'none' }};">
                <label class="rem-filter-label">Month</label>
                <select id="month" class="prl-filter-select" onchange="updateReport()">
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$i,1)) }}</option>
                    @endfor
                </select>
            </div>
            <div class="rem-filter-group">
                <label class="rem-filter-label">Year</label>
                <select id="year" class="prl-filter-select" onchange="updateReport()">
                    @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
                        <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="prl-table-card">
        <div class="prl-table-scroll">
            <table class="prl-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th class="text-end">Collection</th>
                        <th class="text-end">Expenses</th>
                        <th class="text-end">Net Remittance</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $groupedRemittances = $remittances->groupBy(function($item) {
                            return $item->remittance_date->format('Y-m-d');
                        })->map(function($group) {
                            return [
                                'remittance_date'   => $group->first()->remittance_date,
                                'total_collection'  => $group->sum('total_collection'),
                                'total_expenses'    => $group->sum('total_expenses'),
                                'net_remittance'    => $group->sum('net_remittance'),
                                'is_short_remittance' => $group->where('is_short_remittance', true)->count() > 0,
                            ];
                        })->sortBy('remittance_date')->values();
                    @endphp
                    @forelse($groupedRemittances as $remittance)
                        <tr>
                            <td class="prl-mono" style="color:#374151;">{{ $remittance['remittance_date']->format('M d, Y') }}</td>
                            <td class="text-end"><span class="prl-mono green">₱{{ number_format($remittance['total_collection'], 2) }}</span></td>
                            <td class="text-end"><span class="prl-mono red">₱{{ number_format($remittance['total_expenses'], 2) }}</span></td>
                            <td class="text-end">
                                <span class="prl-mono {{ $remittance['is_short_remittance'] ? 'red' : 'green' }} bold">
                                    ₱{{ number_format($remittance['net_remittance'], 2) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4">
                            <div class="prl-empty">
                                <div class="prl-empty-icon"><svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
                                <p class="prl-empty-title">No remittances found</p>
                                <p class="prl-empty-sub">Try adjusting the period filter.</p>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>{{-- remui-page --}}
</div>{{-- col-12 --}}
@endsection

@push('scripts')
<script>
const periodSelect = document.getElementById('period');
const weekSelectEl  = document.getElementById('weekSelect');
const monthSelectEl = document.getElementById('monthSelect');

function updateReport() {
    const period = periodSelect.value;
    const week   = document.getElementById('week').value;
    const month  = document.getElementById('month').value;
    const year   = document.getElementById('year').value;
    let url = '{{ route("reports.remittance-report") }}?period=' + period + '&year=' + year;
    if (period === 'weekly')  url += '&week='  + week;
    if (period === 'monthly') url += '&month=' + month;
    window.location.href = url;
}

function toggleFilters() {
    const period = periodSelect.value;
    weekSelectEl.style.display  = period === 'weekly'  ? 'flex' : 'none';
    monthSelectEl.style.display = period === 'monthly' ? 'flex' : 'none';
}

periodSelect.addEventListener('change', toggleFilters);
</script>
@endpush
