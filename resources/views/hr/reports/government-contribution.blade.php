@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <span class="card-title mb-0">Government Contribution Summary</span>
                <p class="text-muted small mt-1 mb-0">Employee and employer contributions breakdown</p>
            </div>
            <a href="{{ route('reports.government-contribution-print', ['date_from' => request('date_from'), 'date_to' => request('date_to'), 'employee_id' => request('employee_id')]) }}" class="btn btn-sm btn-primary" target="_blank">
                <i class="feather-printer me-1"></i> Print
            </a>
        </div>
        <div class="card-body">

            {{-- Filters --}}
            <div class="row mb-4 pb-3 border-bottom">
                <div class="col-md-3">
                    <label class="form-label">Date From</label>
                    <input type="date" id="dateFrom" class="form-control" 
                           value="{{ request('date_from', now()->startOfMonth()->format('Y-m-d')) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date To</label>
                    <input type="date" id="dateTo" class="form-control" 
                           value="{{ request('date_to', now()->format('Y-m-d')) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Employee</label>
                    <select id="employeeId" class="form-select">
                        <option value="">All Employees</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2 align-items-end">
                    <button class="btn btn-sm btn-primary w-100" onclick="updateReport()">Search</button>
                    <button class="btn btn-sm btn-outline-secondary w-100" onclick="resetFilters()">Reset</button>
                </div>
            </div>

            {{-- Summary Statistics --}}
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="card bg-light">
                        <div class="card-body">
                            <small class="text-muted">Total Employee Contributions</small>
                            <h5 class="mb-0 mt-2" style="color: #0369a1;">₱{{ number_format(($summary['employee_sss'] ?? 0) + ($summary['employee_pagibig'] ?? 0) + ($summary['employee_philhealth'] ?? 0), 2) }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card bg-light">
                        <div class="card-body">
                            <small class="text-muted">Total Employer Contributions</small>
                            <h5 class="mb-0 mt-2" style="color: #8B3A62;">₱{{ number_format(($summary['employer_sss'] ?? 0) + ($summary['employer_pagibig'] ?? 0) + ($summary['employer_philhealth'] ?? 0), 2) }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card bg-light">
                        <div class="card-body">
                            <small class="text-muted">Total Contributions</small>
                            <h5 class="mb-0 mt-2" style="color: #16a34a;">₱{{ number_format(($summary['employee_sss'] ?? 0) + ($summary['employee_pagibig'] ?? 0) + ($summary['employee_philhealth'] ?? 0) + ($summary['employer_sss'] ?? 0) + ($summary['employer_pagibig'] ?? 0) + ($summary['employer_philhealth'] ?? 0), 2) }}</h5>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Summary by Contribution Type --}}
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <span class="card-title mb-0">Contribution Summary by Type</span>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover w-100 mb-0">
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
                                            <td class="text-end">₱{{ number_format($summary['employee_sss'] ?? 0, 2) }}</td>
                                            <td class="text-end">₱{{ number_format($summary['employer_sss'] ?? 0, 2) }}</td>
                                            <td class="text-end"><strong style="color: #16a34a;">₱{{ number_format(($summary['employee_sss'] ?? 0) + ($summary['employer_sss'] ?? 0), 2) }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Pag-IBIG</strong></td>
                                            <td class="text-end">₱{{ number_format($summary['employee_pagibig'] ?? 0, 2) }}</td>
                                            <td class="text-end">₱{{ number_format($summary['employer_pagibig'] ?? 0, 2) }}</td>
                                            <td class="text-end"><strong style="color: #16a34a;">₱{{ number_format(($summary['employee_pagibig'] ?? 0) + ($summary['employer_pagibig'] ?? 0), 2) }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td><strong>PhilHealth</strong></td>
                                            <td class="text-end">₱{{ number_format($summary['employee_philhealth'] ?? 0, 2) }}</td>
                                            <td class="text-end">₱{{ number_format($summary['employer_philhealth'] ?? 0, 2) }}</td>
                                            <td class="text-end"><strong style="color: #16a34a;">₱{{ number_format(($summary['employee_philhealth'] ?? 0) + ($summary['employer_philhealth'] ?? 0), 2) }}</strong></td>
                                        </tr>
                                        <tr style="border-top: 2px solid #ddd;">
                                            <td><strong>TOTAL</strong></td>
                                            <td class="text-end"><strong>₱{{ number_format(($summary['employee_sss'] ?? 0) + ($summary['employee_pagibig'] ?? 0) + ($summary['employee_philhealth'] ?? 0), 2) }}</strong></td>
                                            <td class="text-end"><strong>₱{{ number_format(($summary['employer_sss'] ?? 0) + ($summary['employer_pagibig'] ?? 0) + ($summary['employer_philhealth'] ?? 0), 2) }}</strong></td>
                                            <td class="text-end"><strong style="color: #16a34a; font-size: 1.1em;">₱{{ number_format(($summary['employee_sss'] ?? 0) + ($summary['employee_pagibig'] ?? 0) + ($summary['employee_philhealth'] ?? 0) + ($summary['employer_sss'] ?? 0) + ($summary['employer_pagibig'] ?? 0) + ($summary['employer_philhealth'] ?? 0), 2) }}</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Detailed Breakdown by Employee --}}
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <span class="card-title mb-0">Detailed Breakdown by Employee</span>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover w-100 mb-0">
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
                                                <td>
                                                    {{ \Carbon\Carbon::parse($contribution['period_start'])->format('M d') }} – 
                                                    {{ \Carbon\Carbon::parse($contribution['period_end'])->format('M d, Y') }}
                                                </td>
                                                <td class="text-end">₱{{ number_format($contribution['employee_sss'] ?? 0, 2) }}</td>
                                                <td class="text-end">
                                                    <span style="color: #8B3A62;">₱{{ number_format($contribution['employer_sss'] ?? 0, 2) }}</span>
                                                </td>
                                                <td class="text-end">₱{{ number_format($contribution['employee_pagibig'] ?? 0, 2) }}</td>
                                                <td class="text-end">
                                                    <span style="color: #8B3A62;">₱{{ number_format($contribution['employer_pagibig'] ?? 0, 2) }}</span>
                                                </td>
                                                <td class="text-end">₱{{ number_format($contribution['employee_philhealth'] ?? 0, 2) }}</td>
                                                <td class="text-end">
                                                    <span style="color: #8B3A62;">₱{{ number_format($contribution['employer_philhealth'] ?? 0, 2) }}</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-5">
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
                </div>
            </div>

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
