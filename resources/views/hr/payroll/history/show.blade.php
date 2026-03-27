@extends('layouts.layout')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <a href="{{ route('payroll.history.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="feather-chevron-left"></i> Back to History
                        </a>
                    </div>
                    <div>
                        <span class="card-title mb-0">Payroll Details</span>
                        <p class="text-muted small mt-1 mb-0">Period: {{ $payroll->payroll_period_start->format('M d, Y') }} - {{ $payroll->payroll_period_end->format('M d, Y') }}</p>
                    </div>
                </div>
                <div class="card-body">

                    {{-- Employee Information --}}
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="mb-3">Employee Information</h5>
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tr>
                                        <td style="font-weight: 600; width: 30%;">Name:</td>
                                        <td>{{ $payroll->user->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 600;">Position:</td>
                                        <td>{{ $payroll->user->role ? ucfirst(str_replace('_', ' ', $payroll->user->role)) : 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 600;">Email:</td>
                                        <td>{{ $payroll->user->email ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 600;">Department:</td>
                                        <td>{{ $payroll->user->department ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Payroll Status</h5>
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tr>
                                        <td style="font-weight: 600; width: 30%;">Status:</td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'draft' => 'secondary',
                                                    'pending' => 'warning',
                                                    'submitted' => 'info',
                                                    'approved' => 'success',
                                                    'paid' => 'success',
                                                    'rejected' => 'danger',
                                                ];
                                                $statusColor = $statusColors[$payroll->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $statusColor }}">{{ ucfirst($payroll->status) }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 600;">Payment Date:</td>
                                        <td>{{ $payroll->payment_date ? $payroll->payment_date->format('M d, Y') : 'Pending' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 600;">Payment Method:</td>
                                        <td>{{ $payroll->payment_method ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 600;">Approved By:</td>
                                        <td>{{ $payroll->approvedBy?->name ?? 'Pending' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Compensation Breakdown --}}
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="mb-3">Compensation</h5>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                                        <span>Basic Salary:</span>
                                        <strong>₱{{ number_format($payroll->basic_salary ?? 0, 2) }}</strong>
                                    </div>
                                    <h6 class="mb-3">Allowances:</h6>
                                    @if($payroll->allowances->count() > 0)
                                        <div style="margin-left: 15px; margin-bottom: 12px;">
                                            @foreach($payroll->allowances as $allowance)
                                                <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.95rem;">
                                                    <span>{{ $allowance->allowance_type }}</span>
                                                    <span>₱{{ number_format($allowance->amount, 2) }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-muted small" style="margin-left: 15px; margin-bottom: 12px;">No allowances</p>
                                    @endif
                                    <div style="border-top: 2px solid #dee2e6; padding-top: 12px; display: flex; justify-content: space-between;">
                                        <strong>Total Allowances:</strong>
                                        <strong style="color: #55A969;">₱{{ number_format($payroll->allowances->sum('amount') ?? 0, 2) }}</strong>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; margin-top: 12px;">
                                        <strong>Gross Pay:</strong>
                                        <strong style="color: #8B3A62;">₱{{ number_format($payroll->gross_pay ?? 0, 2) }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h5 class="mb-3">Deductions</h5>
                            <div class="card bg-light">
                                <div class="card-body">
                                    @if($payroll->deductions->count() > 0)
                                        <div style="margin-bottom: 12px;">
                                            @foreach($payroll->deductions as $deduction)
                                                <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.95rem;">
                                                    <span>{{ $deduction->deduction_type }}</span>
                                                    <span>(₱{{ number_format($deduction->amount, 2) }})</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-muted small" style="margin-bottom: 12px;">No deductions</p>
                                    @endif
                                    <div style="border-top: 2px solid #dee2e6; padding-top: 12px; display: flex; justify-content: space-between;">
                                        <strong>Total Deductions:</strong>
                                        <strong style="color: #B8860B;">₱{{ number_format($payroll->deductions->sum('amount') ?? 0, 2) }}</strong>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; margin-top: 12px;">
                                        <strong>Net Pay:</strong>
                                        <strong style="color: #55A969; font-size: 1.1rem;">₱{{ number_format($payroll->net_pay ?? 0, 2) }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Attendance & Work Details --}}
                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="mb-3">Work Details</h5>
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tr>
                                        <td style="font-weight: 600; width: 25%;">Days Worked:</td>
                                        <td>{{ $payroll->days_worked ?? 0 }} days</td>
                                        <td style="font-weight: 600; width: 25%;">Hours Worked:</td>
                                        <td>{{ $payroll->hours_worked ?? 0 }} hours</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 600;">SSS:</td>
                                        <td>₱{{ number_format($payroll->sss ?? 0, 2) }}</td>
                                        <td style="font-weight: 600;">PhilHealth:</td>
                                        <td>₱{{ number_format($payroll->pagibig ?? 0, 2) }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <style>
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }

        .table-borderless td {
            padding: 0.75rem;
            border: none;
        }

        .card-body {
            padding: 1.5rem;
        }

        h5 {
            font-weight: 700;
            color: #333;
        }

        a {
            text-decoration: none;
        }

        a:hover {
            opacity: 0.8;
        }
    </style>
@endsection
