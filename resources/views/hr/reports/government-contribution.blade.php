@extends('layouts.layout')

@push('styles')
    @include('accountant._ui-styles')
@endpush

@section('content')
<div class="col-12">
    <div class="remui-page acd-page">
        <div class="remui-backdrop">
            <div class="remui-grid"></div>
        </div>

        <div class="remui-hero mb-3">
            <div>
                <h5 class="remui-title">Government Contribution Summary</h5>
                <p class="remui-subtitle mb-0">Employee and employer contribution breakdown.</p>
            </div>
            <a href="{{ route('reports.government-contribution-print', ['date_from' => request('date_from'), 'date_to' => request('date_to'), 'employee_id' => request('employee_id')]) }}" class="emp-action-btn emp-action-edit" target="_blank" rel="noopener">
                <i class="feather-printer"></i><span>Print</span>
            </a>
        </div>

        <form class="acd-filter-bar" method="get" action="{{ route('reports.government-contribution') }}" id="gc-report-filters">
            <div>
                <label class="form-label">Date From</label>
                <input type="date" name="date_from" class="form-select form-select-sm" value="{{ request('date_from', now()->startOfMonth()->format('Y-m-d')) }}" onchange="this.form.submit()">
            </div>
            <div>
                <label class="form-label">Date To</label>
                <input type="date" name="date_to" class="form-select form-select-sm" value="{{ request('date_to', now()->format('Y-m-d')) }}" onchange="this.form.submit()">
            </div>
            <div>
                <label class="form-label">Employee</label>
                <select name="employee_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Employees</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                            {{ $employee->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('gc-report-filters').reset(); this.form.submit()">Reset</button>
            </div>
        </form>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body">
                        <span class="dash-label">Total Employee Contributions</span>
                        <div class="dash-value mt-2">₱{{ number_format(($summary['employee_sss'] ?? 0) + ($summary['employee_pagibig'] ?? 0) + ($summary['employee_philhealth'] ?? 0), 2) }}</div>
                        <div class="dash-sub">All types combined</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body">
                        <span class="dash-label">Total Employer Contributions</span>
                        <div class="dash-value mt-2">₱{{ number_format(($summary['employer_sss'] ?? 0) + ($summary['employer_pagibig'] ?? 0) + ($summary['employer_philhealth'] ?? 0), 2) }}</div>
                        <div class="dash-sub">All types combined</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body">
                        <span class="dash-label">Total Contributions</span>
                        <div class="dash-value mt-2">₱{{ number_format(($summary['employee_sss'] ?? 0) + ($summary['employee_pagibig'] ?? 0) + ($summary['employee_philhealth'] ?? 0) + ($summary['employer_sss'] ?? 0) + ($summary['employer_pagibig'] ?? 0) + ($summary['employer_philhealth'] ?? 0), 2) }}</div>
                        <div class="dash-sub">Combined total</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body">
                        <span class="dash-label">Reporting Period</span>
                        <div class="dash-value mt-2" style="font-size: 1.1rem;">{{ count($contributions) }}</div>
                        <div class="dash-sub">Records</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card remui-card">
            <div class="card-header">
                <span class="card-title mb-0">Contribution Summary by Type</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover w-100 mb-0 remui-table">
                        <thead>
                            <tr>
                                <th>Contribution Type</th>
                                <th class="text-end">Employee Contribution</th>
                                <th class="text-end">Employer Contribution</th>
                                <th class="text-end">Total Contribution</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>SSS</strong></td>
                                <td class="text-end font-monospace">₱{{ number_format($summary['employee_sss'] ?? 0, 2) }}</td>
                                <td class="text-end font-monospace text-muted">₱{{ number_format($summary['employer_sss'] ?? 0, 2) }}</td>
                                <td class="text-end font-monospace fw-bold" style="color:#15803d;">₱{{ number_format(($summary['employee_sss'] ?? 0) + ($summary['employer_sss'] ?? 0), 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Pag-IBIG</strong></td>
                                <td class="text-end font-monospace">₱{{ number_format($summary['employee_pagibig'] ?? 0, 2) }}</td>
                                <td class="text-end font-monospace text-muted">₱{{ number_format($summary['employer_pagibig'] ?? 0, 2) }}</td>
                                <td class="text-end font-monospace fw-bold" style="color:#15803d;">₱{{ number_format(($summary['employee_pagibig'] ?? 0) + ($summary['employer_pagibig'] ?? 0), 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>PhilHealth</strong></td>
                                <td class="text-end font-monospace">₱{{ number_format($summary['employee_philhealth'] ?? 0, 2) }}</td>
                                <td class="text-end font-monospace text-muted">₱{{ number_format($summary['employer_philhealth'] ?? 0, 2) }}</td>
                                <td class="text-end font-monospace fw-bold" style="color:#15803d;">₱{{ number_format(($summary['employee_philhealth'] ?? 0) + ($summary['employer_philhealth'] ?? 0), 2) }}</td>
                            </tr>
                            <tr style="border-top: 2px solid #e5e7eb; background: #fcfcfd; font-weight: 700;">
                                <td><strong>TOTAL</strong></td>
                                <td class="text-end font-monospace">₱{{ number_format(($summary['employee_sss'] ?? 0) + ($summary['employee_pagibig'] ?? 0) + ($summary['employee_philhealth'] ?? 0), 2) }}</td>
                                <td class="text-end font-monospace">₱{{ number_format(($summary['employer_sss'] ?? 0) + ($summary['employer_pagibig'] ?? 0) + ($summary['employer_philhealth'] ?? 0), 2) }}</td>
                                <td class="text-end font-monospace fw-bold" style="color:#15803d;">₱{{ number_format(($summary['employee_sss'] ?? 0) + ($summary['employee_pagibig'] ?? 0) + ($summary['employee_philhealth'] ?? 0) + ($summary['employer_sss'] ?? 0) + ($summary['employer_pagibig'] ?? 0) + ($summary['employer_philhealth'] ?? 0), 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card remui-card">
            <div class="card-header">
                <span class="card-title mb-0">Detailed Breakdown by Employee</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover w-100 mb-0 remui-table">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Period</th>
                                <th class="text-end">Employee SSS</th>
                                <th class="text-end">Employer SSS</th>
                                <th class="text-end">Employee Pag-IBIG</th>
                                <th class="text-end">Employer Pag-IBIG</th>
                                <th class="text-end">Employee PhilHealth</th>
                                <th class="text-end">Employer PhilHealth</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($contributions as $contribution)
                                <tr>
                                    <td><strong>{{ $contribution['employee_name'] }}</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($contribution['period_start'])->format('M d') }} – {{ \Carbon\Carbon::parse($contribution['period_end'])->format('M d, Y') }}</td>
                                    <td class="text-end font-monospace">₱{{ number_format($contribution['employee_sss'] ?? 0, 2) }}</td>
                                    <td class="text-end font-monospace text-muted">₱{{ number_format($contribution['employer_sss'] ?? 0, 2) }}</td>
                                    <td class="text-end font-monospace">₱{{ number_format($contribution['employee_pagibig'] ?? 0, 2) }}</td>
                                    <td class="text-end font-monospace text-muted">₱{{ number_format($contribution['employer_pagibig'] ?? 0, 2) }}</td>
                                    <td class="text-end font-monospace">₱{{ number_format($contribution['employee_philhealth'] ?? 0, 2) }}</td>
                                    <td class="text-end font-monospace text-muted">₱{{ number_format($contribution['employer_philhealth'] ?? 0, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-5">
                                        <i class="feather-file-text d-block mb-2" style="font-size:28px;opacity:.3;"></i>
                                        No contribution records found for the selected period
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
