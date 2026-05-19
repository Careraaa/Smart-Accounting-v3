@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
.gc-page {
    font-family: 'Sora', sans-serif;
    padding-top: 12px;
    margin-top: 8px;
}

.gc-topbar {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 24px;
    flex-wrap: wrap;
}
.gc-topbar-title {
    font-size: 1.35rem;
    font-weight: 800;
    color: #111827;
    letter-spacing: -0.02em;
    margin: 0 0 2px;
}
.gc-topbar-sub {
    font-size: 0.78rem;
    color: #9ca3af;
    margin: 0;
    max-width: 520px;
    line-height: 1.45;
}
.gc-topbar-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}
.gc-btn-print {
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
.gc-btn-print:hover {
    background: #a81f20;
    color: #fff;
    box-shadow: 0 8px 22px rgba(200, 41, 42, 0.34);
    transform: translateY(-1px);
}
.gc-btn-print i { font-size: 15px; line-height: 1; }

.gc-filter-bar {
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
.gc-filter-group {
    display: flex;
    flex-direction: column;
    gap: 5px;
    min-width: 150px;
    flex: 1;
}
.gc-filter-group label {
    font-size: 0.67rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #9ca3af;
    margin: 0;
}
.gc-filter-group input,
.gc-filter-group select {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 8px 10px;
    font-size: 0.8rem;
    font-family: 'Sora', sans-serif;
    color: #111827;
    background: #f9fafb;
    outline: none;
    transition: border-color 0.15s, box-shadow 0.15s, background 0.15s;
}
.gc-filter-group input:focus,
.gc-filter-group select:focus {
    border-color: #c8292a;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(200, 41, 42, 0.08);
}
.gc-filter-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-left: auto;
}
.gc-reset-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 8px 14px;
    font-size: 0.8rem;
    font-weight: 600;
    color: #6b7280;
    background: #fff;
    text-decoration: none;
    transition: all 0.14s ease;
    white-space: nowrap;
}
.gc-reset-btn:hover {
    border-color: #c8292a;
    color: #c8292a;
    background: #fff5f5;
}
@media (max-width: 768px) {
    .gc-filter-actions { margin-left: 0; width: 100%; }
    .gc-filter-actions .gc-reset-btn { flex: 1; }
}

.gc-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 22px;
}
@media (max-width: 1100px) { .gc-stats { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 560px)  { .gc-stats { grid-template-columns: 1fr; } }
.gc-stat {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 16px 18px;
    position: relative;
    overflow: hidden;
}
.gc-stat::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 3px;
    border-radius: 0 0 14px 14px;
}
.gc-stat.s-emp::after  { background: #0284c7; }
.gc-stat.s-er::after   { background: #16a34a; }
.gc-stat.s-tot::after  { background: #c8292a; }
.gc-stat.s-rec::after  { background: #6b7280; }
.gc-stat-label {
    font-size: 0.67rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.09em;
    color: #9ca3af;
    margin-bottom: 6px;
}
.gc-stat-value {
    font-size: 1.25rem;
    font-weight: 800;
    color: #111827;
    font-family: 'DM Mono', monospace;
    line-height: 1.2;
    letter-spacing: -0.02em;
}
.gc-stat-sub {
    font-size: 0.72rem;
    color: #9ca3af;
    margin-top: 4px;
}

.gc-table-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    overflow: hidden;
    margin-bottom: 16px;
}
.gc-table-head {
    padding: 14px 18px;
    border-bottom: 1px solid #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    flex-wrap: wrap;
}
.gc-table-title {
    margin: 0;
    font-size: 0.88rem;
    font-weight: 700;
    color: #111827;
    display: flex;
    align-items: center;
    gap: 8px;
}
.gc-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #c8292a;
    flex-shrink: 0;
}
.gc-table-scroll { overflow-x: auto; }
.gc-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.825rem;
}
.gc-table thead tr {
    background: #f8f9fb;
    border-bottom: 1px solid #e5e7eb;
}
.gc-table thead th {
    padding: 10px 14px;
    font-size: 0.66rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.09em;
    color: #6b7280;
    white-space: nowrap;
}
.gc-table tbody tr {
    border-bottom: 1px solid #f3f4f6;
    transition: background 0.1s;
}
.gc-table tbody tr:last-child { border-bottom: none; }
.gc-table tbody tr:hover { background: #fafafa; }
.gc-table tbody td {
    padding: 10px 14px;
    color: #374151;
    vertical-align: middle;
}
.gc-table tbody tr.gc-total-row {
    background: #f8f9fb;
    border-top: 2px solid #e5e7eb;
    font-weight: 700;
}
.gc-table tbody tr.gc-total-row td { color: #111827; }
.gc-mono {
    font-family: 'DM Mono', monospace;
    font-size: 0.8rem;
    font-variant-numeric: tabular-nums;
}
.gc-mono.muted { color: #9ca3af; }
.gc-mono.total { color: #15803d; font-weight: 700; }
.gc-empty {
    text-align: center;
    padding: 48px 20px;
    color: #9ca3af;
    font-size: 0.84rem;
}
.gc-empty i {
    display: block;
    font-size: 28px;
    opacity: 0.3;
    margin-bottom: 8px;
}
.gc-period-chip {
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
    $empTotal = ($summary['employee_sss'] ?? 0) + ($summary['employee_pagibig'] ?? 0) + ($summary['employee_philhealth'] ?? 0);
    $erTotal  = ($summary['employer_sss'] ?? 0) + ($summary['employer_pagibig'] ?? 0) + ($summary['employer_philhealth'] ?? 0);
    $grandTotal = $empTotal + $erTotal;
    $dateFrom = request('date_from', now()->subMonths(3)->startOfMonth()->format('Y-m-d'));
    $dateTo   = request('date_to', now()->format('Y-m-d'));
    $printUrl = route('reports.government-contribution-print', [
        'date_from'   => $dateFrom,
        'date_to'     => $dateTo,
        'employee_id' => request('employee_id'),
    ]);
@endphp

<div class="gc-page">
    <div class="gc-topbar">
        <div>
            <h1 class="gc-topbar-title">Government Contribution Summary</h1>
            <p class="gc-topbar-sub">SSS, Pag-IBIG, and PhilHealth totals from payroll — employee and employer shares for the selected period.</p>
            <span class="gc-period-chip">{{ \Carbon\Carbon::parse($dateFrom)->format('M j, Y') }} – {{ \Carbon\Carbon::parse($dateTo)->format('M j, Y') }}</span>
        </div>
        <div class="gc-topbar-actions">
            <a href="{{ $printUrl }}" class="gc-btn-print" target="_blank" rel="noopener">
                <i class="feather-printer"></i> Print report
            </a>
        </div>
    </div>

    <form class="gc-filter-bar" method="get" action="{{ route('reports.government-contribution') }}" id="gc-report-filters">
        <div class="gc-filter-group">
            <label for="gc-date-from">Date from</label>
            <input type="date" id="gc-date-from" name="date_from" value="{{ $dateFrom }}" onchange="this.form.submit()">
        </div>
        <div class="gc-filter-group">
            <label for="gc-date-to">Date to</label>
            <input type="date" id="gc-date-to" name="date_to" value="{{ $dateTo }}" onchange="this.form.submit()">
        </div>
        <div class="gc-filter-group" style="min-width:200px;flex:1.4;">
            <label for="gc-employee">Employee</label>
            <select id="gc-employee" name="employee_id" onchange="this.form.submit()">
                <option value="">All employees</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                        {{ $employee->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="gc-filter-actions">
            <a href="{{ route('reports.government-contribution') }}" class="gc-reset-btn">Reset filters</a>
        </div>
    </form>

    <div class="gc-stats">
        <div class="gc-stat s-emp">
            <div class="gc-stat-label">Employee share</div>
            <div class="gc-stat-value">₱{{ number_format($empTotal, 2) }}</div>
            <div class="gc-stat-sub">SSS · Pag-IBIG · PhilHealth</div>
        </div>
        <div class="gc-stat s-er">
            <div class="gc-stat-label">Employer share</div>
            <div class="gc-stat-value">₱{{ number_format($erTotal, 2) }}</div>
            <div class="gc-stat-sub">Matching employer totals</div>
        </div>
        <div class="gc-stat s-tot">
            <div class="gc-stat-label">Grand total</div>
            <div class="gc-stat-value">₱{{ number_format($grandTotal, 2) }}</div>
            <div class="gc-stat-sub">Combined contributions</div>
        </div>
        <div class="gc-stat s-rec">
            <div class="gc-stat-label">Records</div>
            <div class="gc-stat-value">{{ count($contributions) }}</div>
            <div class="gc-stat-sub">Payroll rows in range</div>
        </div>
    </div>

    <div class="gc-table-card">
        <div class="gc-table-head">
            <h2 class="gc-table-title"><span class="gc-dot"></span> Summary by contribution type</h2>
        </div>
        <div class="gc-table-scroll">
            <table class="gc-table">
                <thead>
                    <tr>
                        <th>Contribution type</th>
                        <th class="text-end">Employee</th>
                        <th class="text-end">Employer</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>SSS</strong></td>
                        <td class="text-end gc-mono">₱{{ number_format($summary['employee_sss'] ?? 0, 2) }}</td>
                        <td class="text-end gc-mono muted">₱{{ number_format($summary['employer_sss'] ?? 0, 2) }}</td>
                        <td class="text-end gc-mono total">₱{{ number_format(($summary['employee_sss'] ?? 0) + ($summary['employer_sss'] ?? 0), 2) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Pag-IBIG</strong></td>
                        <td class="text-end gc-mono">₱{{ number_format($summary['employee_pagibig'] ?? 0, 2) }}</td>
                        <td class="text-end gc-mono muted">₱{{ number_format($summary['employer_pagibig'] ?? 0, 2) }}</td>
                        <td class="text-end gc-mono total">₱{{ number_format(($summary['employee_pagibig'] ?? 0) + ($summary['employer_pagibig'] ?? 0), 2) }}</td>
                    </tr>
                    <tr>
                        <td><strong>PhilHealth</strong></td>
                        <td class="text-end gc-mono">₱{{ number_format($summary['employee_philhealth'] ?? 0, 2) }}</td>
                        <td class="text-end gc-mono muted">₱{{ number_format($summary['employer_philhealth'] ?? 0, 2) }}</td>
                        <td class="text-end gc-mono total">₱{{ number_format(($summary['employee_philhealth'] ?? 0) + ($summary['employer_philhealth'] ?? 0), 2) }}</td>
                    </tr>
                    <tr class="gc-total-row">
                        <td><strong>Total</strong></td>
                        <td class="text-end gc-mono">₱{{ number_format($empTotal, 2) }}</td>
                        <td class="text-end gc-mono">₱{{ number_format($erTotal, 2) }}</td>
                        <td class="text-end gc-mono total">₱{{ number_format($grandTotal, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="gc-table-card">
        <div class="gc-table-head">
            <h2 class="gc-table-title"><span class="gc-dot"></span> Breakdown by employee</h2>
        </div>
        <div class="gc-table-scroll">
            <table class="gc-table">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Period</th>
                        <th class="text-end">Emp. SSS</th>
                        <th class="text-end">Er. SSS</th>
                        <th class="text-end">Emp. Pag-IBIG</th>
                        <th class="text-end">Er. Pag-IBIG</th>
                        <th class="text-end">Emp. PhilHealth</th>
                        <th class="text-end">Er. PhilHealth</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contributions as $contribution)
                        <tr>
                            <td><strong>{{ $contribution['employee_name'] }}</strong></td>
                            <td>{{ \Carbon\Carbon::parse($contribution['period_start'])->format('M j') }} – {{ \Carbon\Carbon::parse($contribution['period_end'])->format('M j, Y') }}</td>
                            <td class="text-end gc-mono">₱{{ number_format($contribution['employee_sss'] ?? 0, 2) }}</td>
                            <td class="text-end gc-mono muted">₱{{ number_format($contribution['employer_sss'] ?? 0, 2) }}</td>
                            <td class="text-end gc-mono">₱{{ number_format($contribution['employee_pagibig'] ?? 0, 2) }}</td>
                            <td class="text-end gc-mono muted">₱{{ number_format($contribution['employer_pagibig'] ?? 0, 2) }}</td>
                            <td class="text-end gc-mono">₱{{ number_format($contribution['employee_philhealth'] ?? 0, 2) }}</td>
                            <td class="text-end gc-mono muted">₱{{ number_format($contribution['employer_philhealth'] ?? 0, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="gc-empty">
                                    <i class="feather-file-text"></i>
                                    No contribution records found for the selected period.
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
