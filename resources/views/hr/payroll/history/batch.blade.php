@extends('layouts.layout')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <span class="card-title mb-0">Payroll Batch Details</span>
                        <p class="text-muted small mt-1 mb-0">Period: {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <a href="{{ route('payroll.history.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="feather-chevron-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">

                    {{-- Batch Summary Cards --}}
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card card-statistic">
                                <div class="card-body">
                                    <div class="stat-label">Total Employees</div>
                                    <h3 class="mb-1" style="color: #8B3A62;">{{ $payrolls->count() }}</h3>
                                    <small class="text-muted">In this batch</small>
                                </div>
                                <div class="card-icon" style="color: #8B3A62; opacity: 0.2;">
                                    <i class="feather-users" style="font-size: 2.5rem;"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-statistic">
                                <div class="card-body">
                                    <div class="stat-label">Total Gross Pay</div>
                                    <h3 class="mb-1" style="color: #B8860B;">₱{{ number_format($totalGross, 2) }}</h3>
                                    <small class="text-muted">Gross salary amount</small>
                                </div>
                                <div class="card-icon" style="color: #B8860B; opacity: 0.2;">
                                    <i class="feather-dollar-sign" style="font-size: 2.5rem;"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-statistic">
                                <div class="card-body">
                                    <div class="stat-label">Total Deductions</div>
                                    <h3 class="mb-1" style="color: #D4933A;">₱{{ number_format($totalDeductions, 2) }}</h3>
                                    <small class="text-muted">Deductions total</small>
                                </div>
                                <div class="card-icon" style="color: #D4933A; opacity: 0.2;">
                                    <i class="feather-minus-circle" style="font-size: 2.5rem;"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-statistic">
                                <div class="card-body">
                                    <div class="stat-label">Total Net Pay</div>
                                    <h3 class="mb-1" style="color: #55A969;">₱{{ number_format($totalNetPay, 2) }}</h3>
                                    <small class="text-muted">Net amount to release</small>
                                </div>
                                <div class="card-icon" style="color: #55A969; opacity: 0.2;">
                                    <i class="feather-check-circle" style="font-size: 2.5rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Payroll Records Table --}}
                    <h5 class="mb-3">Payroll Records in This Batch</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="fw-semibold text-uppercase fs-6">Employee Name</th>
                                    <th class="fw-semibold text-uppercase fs-6">Department</th>
                                    <th class="fw-semibold text-uppercase fs-6">Position</th>
                                    <th class="fw-semibold text-uppercase fs-6 text-end">Gross Pay</th>
                                    <th class="fw-semibold text-uppercase fs-6 text-end">Deductions</th>
                                    <th class="fw-semibold text-uppercase fs-6 text-end">Net Pay</th>
                                    <th class="fw-semibold text-uppercase fs-6 text-center">Status</th>
                                    <th class="fw-semibold text-uppercase fs-6 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payrolls as $payroll)
                                    <tr>
                                        <td>{{ $payroll->user->first_name }} {{ $payroll->user->last_name }}</td>
                                        <td>
                                            <span class="badge bg-light text-dark">{{ $payroll->user->department ?? 'N/A' }}</span>
                                        </td>
                                        <td>{{ $payroll->user->position ?? 'N/A' }}</td>
                                        <td class="text-end">₱{{ number_format($payroll->gross_pay, 2) }}</td>
                                        <td class="text-end">₱{{ number_format($payroll->total_deductions, 2) }}</td>
                                        <td class="text-end fw-semibold">₱{{ number_format($payroll->net_pay, 2) }}</td>
                                        <td class="text-center">
                                            @php
                                                $statusMap = [
                                                    'draft' => 'badge bg-secondary',
                                                    'pending' => 'badge bg-warning',
                                                    'submitted' => 'badge bg-warning',
                                                    'approved' => 'badge bg-info',
                                                    'paid' => 'badge bg-success',
                                                    'rejected' => 'badge bg-danger',
                                                ];
                                                $statusClass = $statusMap[$payroll->status] ?? $statusMap['draft'];
                                            @endphp
                                            <span class="{{ $statusClass }}">
                                                {{ ucfirst($payroll->status) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="{{ route('payroll.salary-computation.show', $payroll) }}" class="emp-action-btn emp-action-view" title="View">
                                                    <i class="feather-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            No payroll records found in this batch.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Summary Footer --}}
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="text-muted small">
                                <strong>Summary:</strong> {{ $paidCount }} paid, {{ $pendingCount }} pending out of {{ $payrolls->count() }} total employees
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <style>
        .card-statistic {
            position: relative;
            overflow: hidden;
        }

        .card-statistic .card-body {
            padding: 1.5rem;
            position: relative;
            z-index: 1;
        }

        .card-statistic .card-icon {
            position: absolute;
            right: 15px;
            top: 15px;
            z-index: 0;
        }

        .stat-label {
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.8;
            margin-bottom: 8px;
        }

        .table thead th {
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table tbody td {
            vertical-align: middle;
        }

        a {
            text-decoration: none;
        }

        a:hover {
            opacity: 0.8;
        }
    </style>
@endsection
