@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');

.prl-page {
    font-family: 'Sora', sans-serif;
    max-width: 100%;
}

/* Payroll-specific styles (minimal to avoid conflict with overrides.css) */
.prl-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 16px;
    margin-bottom: 28px;
}

.prl-stat {
    background: var(--kt-white, #fff);
    border: 1px solid var(--kt-border, #e8e8ef);
    border-radius: var(--kt-radius, 12px);
    padding: 20px;
    display: flex;
    align-items: flex-start;
    gap: 16px;
    transition: all 0.2s ease;
}

.prl-stat:hover {
    box-shadow: var(--kt-shadow-sm, 0 4px 15px rgba(0,0,0,0.08));
}

.prl-stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.prl-stat-label {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    color: var(--kt-muted, #9898a8);
    margin-bottom: 4px;
}

.prl-stat-value {
    font-size: 1.55rem;
    font-weight: 800;
    color: var(--kt-dark, #1c1c1e);
    font-family: 'DM Mono', monospace;
    line-height: 1;
}

.prl-stat-sub {
    font-size: 0.78rem;
    color: var(--kt-muted, #9898a8);
    margin-top: 4px;
}

/* Status colors for stats */
.prl-stat.s-blue  .prl-stat-icon { background: #f0f9ff; color: #0284c7; }
.prl-stat.s-green .prl-stat-icon { background: #f0fdf4; color: #16a34a; }
.prl-stat.s-amber .prl-stat-icon { background: #fffbeb; color: #d97706; }
.prl-stat.s-red   .prl-stat-icon { background: #fff0f0; color: #c8292a; }

/* Table & Filter */
.prl-filter-bar {
    background: var(--kt-white);
    border: 1px solid var(--kt-border-med, #d4d4de);
    border-radius: var(--kt-radius, 12px);
    padding: 12px 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 20px;
}

.prl-search-wrap {
    position: relative;
    flex: 1;
    min-width: 220px;
}

.prl-search-input {
    width: 100%;
    padding: 10px 12px 10px 40px;
    border: 1px solid var(--kt-border-med);
    border-radius: var(--kt-radius-sm, 8px);
    font-size: 0.85rem;
    background: var(--kt-white);
}

.prl-search-wrap svg {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--kt-muted);
}

.prl-filter-select {
    padding: 10px 14px;
    border: 1px solid var(--kt-border-med);
    border-radius: var(--kt-radius-sm, 8px);
    background: var(--kt-white);
    font-size: 0.85rem;
    min-width: 140px;
}

.prl-table-card {
    background: var(--kt-white);
    border: 1px solid var(--kt-border);
    border-radius: var(--kt-radius, 12px);
    overflow: hidden;
}

.prl-table {
    width: 100%;
    border-collapse: collapse;
}

.prl-table thead th {
    background: #f8f8fc;
    padding: 14px 18px;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    color: var(--kt-muted);
    text-align: left;
}

.prl-table tbody td {
    padding: 14px 18px;
    border-bottom: 1px solid var(--kt-border);
    color: var(--kt-body);
}

.prl-table tbody tr:hover {
    background: #fff5f5;
}

.prl-period-tag {
    font-family: 'DM Mono', monospace;
    font-size: 0.78rem;
    background: #f4f5f7;
    padding: 4px 10px;
    border-radius: 6px;
    color: var(--kt-muted);
}

.prl-mono {
    font-family: 'DM Mono', monospace;
    font-variant-numeric: tabular-nums;
}

.prl-mono.c-bold {
    font-weight: 700;
    color: var(--kt-dark);
}

/* Status badges */
.prl-status {
    padding: 4px 12px;
    border-radius: 9999px;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    white-space: nowrap;
}

.prl-status.s-draft     { background: #f4f5f7; color: #6b7280; }
.prl-status.s-finalized { background: #eff6ff; color: #2563eb; }
.prl-status.s-submitted { background: #f5f3ff; color: #7c3aed; }
.prl-status.s-paid      { background: #f0fdf4; color: #15803d; }

/* Actions */
.prl-actions {
    display: flex;
    justify-content: flex-end;
    gap: 6px;
}

.prl-action-btn {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    background: #f4f5f7;
    color: var(--kt-muted);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.15s;
}

.prl-action-btn:hover {
    background: #fff5f5;
    color: var(--kt-red);
}

/* Empty state */
.prl-empty {
    padding: 60px 20px;
    text-align: center;
    color: var(--kt-muted);
}

.prl-empty-icon {
    font-size: 3rem;
    margin-bottom: 12px;
    opacity: 0.6;
}
</style>
@endpush

@section('content')
<div class="prl-page">

    @foreach(['success','error','info'] as $t)
        @if(session($t))
        <div class="alert alert-{{ $t === 'success' ? 'success' : ($t === 'error' ? 'danger' : 'info') }} mb-4">
            {{ session($t) }}
        </div>
        @endif
    @endforeach

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="kt-page-title">Payroll History</h1>
            <p class="text-muted small">View all finalized and released payroll batches</p>
        </div>
        <a href="{{ route('hr.reports.print.payroll-history-report') }}" class="btn btn-outline-primary" target="_blank">
            <i class="feather-printer"></i> Print Report
        </a>
    </div>

    {{-- Stats Cards --}}
    <div class="prl-stats">
        <div class="prl-stat s-blue">
            <div class="prl-stat-icon"><i class="feather-layers"></i></div>
            <div>
                <div class="prl-stat-label">Total Batches</div>
                <div class="prl-stat-value">{{ $totalBatches }}</div>
                <div class="prl-stat-sub">generated periods</div>
            </div>
        </div>
        <div class="prl-stat s-green">
            <div class="prl-stat-icon"><i class="feather-dollar-sign"></i></div>
            <div>
                <div class="prl-stat-label">Total Payroll</div>
                <div class="prl-stat-value">₱{{ number_format($totalPayroll, 0) }}</div>
                <div class="prl-stat-sub">all time</div>
            </div>
        </div>
        <div class="prl-stat s-amber">
            <div class="prl-stat-icon"><i class="feather-users"></i></div>
            <div>
                <div class="prl-stat-label">Paid Employees</div>
                <div class="prl-stat-value">{{ $totalPaid }}</div>
                <div class="prl-stat-sub">across all batches</div>
            </div>
        </div>
        <div class="prl-stat s-red">
            <div class="prl-stat-icon"><i class="feather-calendar"></i></div>
            <div>
                <div class="prl-stat-label">Next Cutoff</div>
                <div class="prl-stat-value" style="font-size:1.1rem;">
                    {{ $nextCutoffDate ? $nextCutoffDate->format('M d, Y') : 'N/A' }}
                </div>
                <div class="prl-stat-sub">
                    {{ $nextCutoffDate ? $nextCutoffDate->format('l') : '—' }}
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Payroll Batches</h5>
        </div>
        <div class="card-body">

            {{-- Filters --}}
            <div class="prl-filter-bar">
                <div class="prl-search-wrap">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <circle cx="11" cy="11" r="8"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/>
                    </svg>
                    <input type="text" class="prl-search-input" id="prlSearch" placeholder="Search by period...">
                </div>

                <select class="prl-filter-select" id="filterMonth" onchange="applyFilter()">
                    <option value="">All Months</option>
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $filterMonth == $i ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::createFromDate(null, $i)->format('F') }}
                        </option>
                    @endfor
                </select>

                <select class="prl-filter-select" id="filterYear" onchange="applyFilter()">
                    <option value="">All Years</option>
                    @for ($i = now()->year - 5; $i <= now()->year; $i++)
                        <option value="{{ $i }}" {{ $filterYear == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>

            <div class="prl-table-card">
                <div class="table-responsive">
                    <table class="prl-table table">
                        <thead>
                            <tr>
                                <th>Period</th>
                                <th>Employees</th>
                                <th class="text-end">Total Net Pay</th>
                                <th class="text-center">Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody id="prlTbody">
                            @forelse($batches as $batch)
                                @php
                                    $statusClass = match($batch->status ?? 'draft') {
                                        'finalized' => 's-finalized',
                                        'submitted' => 's-submitted',
                                        'paid'      => 's-paid',
                                        default     => 's-draft'
                                    };
                                @endphp
                                <tr data-period="{{ strtolower($batch->period_start->format('M Y')) }}">
                                    <td>
                                        <span class="prl-period-tag">
                                            {{ $batch->period_start->format('M d') }} — {{ $batch->period_end->format('M d, Y') }}
                                        </span>
                                    </td>
                                    <td><strong>{{ $batch->payrolls->count() }}</strong> employees</td>
                                    <td class="text-end">
                                        <span class="prl-mono c-bold">
                                            ₱{{ number_format($batch->total_net_pay ?? $batch->payrolls->sum('net_pay') ?? 0, 0) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="prl-status {{ $statusClass }}">
                                            {{ ucfirst($batch->status ?? 'draft') }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('payroll.history.batch', ['start' => $batch->period_start->format('Y-m-d'), 'end' => $batch->period_end->format('Y-m-d')]) }}" 
                                           class="prl-action-btn" title="View Batch">
                                            <i class="feather-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="prl-empty">
                                            <div class="prl-empty-icon">📭</div>
                                            <p>No payroll batches found yet.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                {{ $batches->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const search = document.getElementById('prlSearch');
    const tbody = document.getElementById('prlTbody');

    search.addEventListener('input', function () {
        const term = this.value.toLowerCase().trim();
        const rows = tbody.querySelectorAll('tr[data-period]');

        rows.forEach(row => {
            const period = row.getAttribute('data-period') || '';
            row.style.display = period.includes(term) ? '' : 'none';
        });
    });
});

function applyFilter() {
    const month = document.getElementById('filterMonth').value;
    const year = document.getElementById('filterYear').value;
    let url = "{{ route('payroll.history.index') }}";
    const params = [];
    if (month) params.push('filter_month=' + month);
    if (year) params.push('filter_year=' + year);
    if (params.length) url += '?' + params.join('&');
    window.location.href = url;
}
</script>
@endpush