@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
.gcr-page { font-family: 'Sora', sans-serif; }
.gcr-topbar { display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:22px;flex-wrap:wrap; }
.gcr-title { font-size:1.35rem;font-weight:800;color:#111827;letter-spacing:-0.02em;margin:0 0 2px; }
.gcr-sub { font-size:0.78rem;color:#9ca3af;margin:0; }
.gcr-btn-print { display:inline-flex;align-items:center;gap:8px;padding:10px 16px;background:#c8292a;color:#fff;border:none;border-radius:10px;font-size:0.82rem;font-weight:700;text-decoration:none;box-shadow:0 4px 14px rgba(200,41,42,0.28);transition:background 0.15s,box-shadow 0.15s; }
.gcr-btn-print:hover { background:#a81f20;color:#fff;box-shadow:0 6px 18px rgba(200,41,42,0.35); }
.gcr-filter-bar { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:14px 16px;display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px;align-items:end;margin-bottom:14px; }
@media (max-width:1100px) { .gcr-filter-bar { grid-template-columns:repeat(2,minmax(0,1fr)); } }
@media (max-width:680px)  { .gcr-filter-bar { grid-template-columns:1fr; } }
.gcr-field label { font-size:0.67rem;font-weight:700;text-transform:uppercase;letter-spacing:0.09em;color:#9ca3af;margin-bottom:6px;display:block; }
.gcr-field .form-control, .gcr-field .form-select { border:1px solid #e5e7eb;border-radius:8px;padding:8px 10px;font-size:0.82rem;background:#f9fafb; }
.gcr-field .form-control:focus, .gcr-field .form-select:focus { border-color:#c8292a;box-shadow:0 0 0 3px rgba(200,41,42,0.08);background:#fff; }
.gcr-filter-actions { display:flex;gap:8px; }
.gcr-btn-sec, .gcr-btn-primary { flex:1;display:inline-flex;align-items:center;justify-content:center;padding:9px 12px;border-radius:8px;font-size:0.8rem;font-weight:700;border:1px solid transparent;cursor:pointer; }
.gcr-btn-primary { background:#111827;color:#fff;border-color:#111827; }
.gcr-btn-primary:hover { background:#000;color:#fff; }
.gcr-btn-sec { background:#fff;color:#6b7280;border-color:#e5e7eb; }
.gcr-btn-sec:hover { color:#c8292a;border-color:#fecaca;background:#fff5f5; }
.gcr-stats { display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:18px; }
@media (max-width:950px) { .gcr-stats { grid-template-columns:1fr; } }
.gcr-stat { background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:18px 20px;display:flex;gap:12px;position:relative;overflow:hidden; }
.gcr-stat::after { content:'';position:absolute;left:0;right:0;bottom:0;height:3px; }
.gcr-stat.s-red::after { background:#c8292a; }
.gcr-stat.s-purple::after { background:#7c3aed; }
.gcr-stat.s-green::after { background:#16a34a; }
.gcr-stat-label { font-size:0.67rem;font-weight:700;text-transform:uppercase;letter-spacing:0.09em;color:#9ca3af;margin-bottom:4px; }
.gcr-stat-value { font-size:1.25rem;font-weight:800;color:#111827;line-height:1.2;font-family:'DM Mono',monospace;font-variant-numeric:tabular-nums; }
.gcr-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;margin-bottom:16px; }
.gcr-card-head { padding:13px 16px;border-bottom:1px solid #f3f4f6;font-size:0.83rem;font-weight:700;color:#111827;display:flex;align-items:center;gap:8px; }
.gcr-dot { width:8px;height:8px;border-radius:50%;background:#c8292a;display:inline-block; }
.gcr-table-wrap { overflow-x:auto; }
.gcr-table { width:100%;border-collapse:collapse;font-size:0.835rem; }
.gcr-table thead tr { background:#f8f9fb;border-bottom:1px solid #e5e7eb; }
.gcr-table thead th { padding:11px 16px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.09em;color:#6b7280;white-space:nowrap; }
.gcr-table tbody tr { border-bottom:1px solid #f3f4f6;transition:background 0.1s; }
.gcr-table tbody tr:last-child { border-bottom:none; }
.gcr-table tbody tr:hover { background:#fafafa; }
.gcr-table tbody td { padding:12px 16px;color:#374151;vertical-align:middle; }
.gcr-text-end { text-align:right; }
.gcr-mono { font-family:'DM Mono',monospace;font-variant-numeric:tabular-nums; }
.gcr-emp { font-weight:600;color:#111827; }
.gcr-purple { color:#7c3aed;font-weight:600; }
.gcr-green { color:#16a34a;font-weight:700; }
.gcr-total-row td { border-top:2px solid #e5e7eb;background:#fcfcfd;font-weight:700; }
.gcr-empty { text-align:center;color:#9ca3af;padding:36px 18px;font-size:0.82rem; }
</style>
@endpush

@section('content')
<div class="gcr-page col-md-12">
    <div class="gcr-topbar">
        <div>
            <h1 class="gcr-title">Government Contribution Summary</h1>
            <p class="gcr-sub">Employee and employer contribution breakdown by period</p>
        </div>
        <a href="{{ route('reports.government-contribution-print', ['date_from' => request('date_from'), 'date_to' => request('date_to'), 'employee_id' => request('employee_id')]) }}" class="gcr-btn-print" target="_blank">
            <i class="feather-printer"></i> Print Report
        </a>
    </div>

    <div class="gcr-filter-bar">
        <div class="gcr-field">
            <label>Date From</label>
            <input type="date" id="dateFrom" class="form-control" value="{{ request('date_from', now()->startOfMonth()->format('Y-m-d')) }}">
        </div>
        <div class="gcr-field">
            <label>Date To</label>
            <input type="date" id="dateTo" class="form-control" value="{{ request('date_to', now()->format('Y-m-d')) }}">
        </div>
        <div class="gcr-field">
            <label>Employee</label>
            <select id="employeeId" class="form-select">
                <option value="">All Employees</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                        {{ $employee->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="gcr-filter-actions">
            <button class="gcr-btn-primary" onclick="updateReport()">Search</button>
            <button class="gcr-btn-sec" onclick="resetFilters()">Reset</button>
        </div>
    </div>

    <div class="gcr-stats">
        <div class="gcr-stat s-red">
            <div>
                <div class="gcr-stat-label">Total Employee Contributions</div>
                <div class="gcr-stat-value">₱{{ number_format(($summary['employee_sss'] ?? 0) + ($summary['employee_pagibig'] ?? 0) + ($summary['employee_philhealth'] ?? 0), 2) }}</div>
            </div>
        </div>
        <div class="gcr-stat s-purple">
            <div>
                <div class="gcr-stat-label">Total Employer Contributions</div>
                <div class="gcr-stat-value">₱{{ number_format(($summary['employer_sss'] ?? 0) + ($summary['employer_pagibig'] ?? 0) + ($summary['employer_philhealth'] ?? 0), 2) }}</div>
            </div>
        </div>
        <div class="gcr-stat s-green">
            <div>
                <div class="gcr-stat-label">Total Contributions</div>
                <div class="gcr-stat-value">₱{{ number_format(($summary['employee_sss'] ?? 0) + ($summary['employee_pagibig'] ?? 0) + ($summary['employee_philhealth'] ?? 0) + ($summary['employer_sss'] ?? 0) + ($summary['employer_pagibig'] ?? 0) + ($summary['employer_philhealth'] ?? 0), 2) }}</div>
            </div>
        </div>
    </div>

    <div class="gcr-card">
        <div class="gcr-card-head"><span class="gcr-dot"></span> Contribution Summary by Type</div>
        <div class="gcr-table-wrap">
            <table class="gcr-table">
                <thead>
                    <tr>
                        <th>Contribution Type</th>
                        <th class="gcr-text-end">Employee Contribution</th>
                        <th class="gcr-text-end">Employer Contribution</th>
                        <th class="gcr-text-end">Total Contribution</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>SSS</strong></td>
                        <td class="gcr-text-end gcr-mono">₱{{ number_format($summary['employee_sss'] ?? 0, 2) }}</td>
                        <td class="gcr-text-end gcr-mono gcr-purple">₱{{ number_format($summary['employer_sss'] ?? 0, 2) }}</td>
                        <td class="gcr-text-end gcr-mono gcr-green">₱{{ number_format(($summary['employee_sss'] ?? 0) + ($summary['employer_sss'] ?? 0), 2) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Pag-IBIG</strong></td>
                        <td class="gcr-text-end gcr-mono">₱{{ number_format($summary['employee_pagibig'] ?? 0, 2) }}</td>
                        <td class="gcr-text-end gcr-mono gcr-purple">₱{{ number_format($summary['employer_pagibig'] ?? 0, 2) }}</td>
                        <td class="gcr-text-end gcr-mono gcr-green">₱{{ number_format(($summary['employee_pagibig'] ?? 0) + ($summary['employer_pagibig'] ?? 0), 2) }}</td>
                    </tr>
                    <tr>
                        <td><strong>PhilHealth</strong></td>
                        <td class="gcr-text-end gcr-mono">₱{{ number_format($summary['employee_philhealth'] ?? 0, 2) }}</td>
                        <td class="gcr-text-end gcr-mono gcr-purple">₱{{ number_format($summary['employer_philhealth'] ?? 0, 2) }}</td>
                        <td class="gcr-text-end gcr-mono gcr-green">₱{{ number_format(($summary['employee_philhealth'] ?? 0) + ($summary['employer_philhealth'] ?? 0), 2) }}</td>
                    </tr>
                    <tr class="gcr-total-row">
                        <td><strong>TOTAL</strong></td>
                        <td class="gcr-text-end gcr-mono">₱{{ number_format(($summary['employee_sss'] ?? 0) + ($summary['employee_pagibig'] ?? 0) + ($summary['employee_philhealth'] ?? 0), 2) }}</td>
                        <td class="gcr-text-end gcr-mono">₱{{ number_format(($summary['employer_sss'] ?? 0) + ($summary['employer_pagibig'] ?? 0) + ($summary['employer_philhealth'] ?? 0), 2) }}</td>
                        <td class="gcr-text-end gcr-mono gcr-green">₱{{ number_format(($summary['employee_sss'] ?? 0) + ($summary['employee_pagibig'] ?? 0) + ($summary['employee_philhealth'] ?? 0) + ($summary['employer_sss'] ?? 0) + ($summary['employer_pagibig'] ?? 0) + ($summary['employer_philhealth'] ?? 0), 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="gcr-card">
        <div class="gcr-card-head"><span class="gcr-dot"></span> Detailed Breakdown by Employee</div>
        <div class="gcr-table-wrap">
            <table class="gcr-table">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Period</th>
                        <th class="gcr-text-end">Employee SSS</th>
                        <th class="gcr-text-end">Employer SSS</th>
                        <th class="gcr-text-end">Employee Pag-IBIG</th>
                        <th class="gcr-text-end">Employer Pag-IBIG</th>
                        <th class="gcr-text-end">Employee PhilHealth</th>
                        <th class="gcr-text-end">Employer PhilHealth</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contributions as $contribution)
                        <tr>
                            <td class="gcr-emp">{{ $contribution['employee_name'] }}</td>
                            <td>{{ \Carbon\Carbon::parse($contribution['period_start'])->format('M d') }} – {{ \Carbon\Carbon::parse($contribution['period_end'])->format('M d, Y') }}</td>
                            <td class="gcr-text-end gcr-mono">₱{{ number_format($contribution['employee_sss'] ?? 0, 2) }}</td>
                            <td class="gcr-text-end gcr-mono gcr-purple">₱{{ number_format($contribution['employer_sss'] ?? 0, 2) }}</td>
                            <td class="gcr-text-end gcr-mono">₱{{ number_format($contribution['employee_pagibig'] ?? 0, 2) }}</td>
                            <td class="gcr-text-end gcr-mono gcr-purple">₱{{ number_format($contribution['employer_pagibig'] ?? 0, 2) }}</td>
                            <td class="gcr-text-end gcr-mono">₱{{ number_format($contribution['employee_philhealth'] ?? 0, 2) }}</td>
                            <td class="gcr-text-end gcr-mono gcr-purple">₱{{ number_format($contribution['employer_philhealth'] ?? 0, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="gcr-empty">
                                <i class="feather-file-text d-block mb-2" style="font-size:28px; opacity:.3;"></i>
                                No contribution records found for the selected period
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function updateReport() {
        const dateFrom = document.getElementById('dateFrom').value;
        const dateTo = document.getElementById('dateTo').value;
        const employeeId = document.getElementById('employeeId').value;

        let url = '{{ route("reports.government-contribution") }}?date_from=' + dateFrom + '&date_to=' + dateTo;
        if (employeeId) url += '&employee_id=' + employeeId;

        window.location.href = url;
    }

    function resetFilters() {
        document.getElementById('dateFrom').value = '{{ now()->startOfMonth()->format('Y-m-d') }}';
        document.getElementById('dateTo').value = '{{ now()->format('Y-m-d') }}';
        document.getElementById('employeeId').value = '';
        window.location.href = '{{ route("reports.government-contribution") }}';
    }
</script>

@endsection
