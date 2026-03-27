@extends('layouts.layout')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <span class="card-title mb-0">Approve Payroll Batch</span>
                    <p class="text-muted small mt-1 mb-0">
                        Period: {{ $startDate->format('F d, Y') }} – {{ $endDate->format('F d, Y') }}
                    </p>
                </div>
                <a href="{{ route('payroll-approval.index') }}" class="btn btn-sm btn-outline-secondary" style="margin-left: auto;">
                    <i class="feather-arrow-left me-1"></i> Back
                </a>
            </div>
            <div class="card-body">
                <!-- Alert Messages -->
                @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="feather-check-circle me-2"></i>
                        {{ $message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($message = Session::get('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="feather-alert-circle me-2"></i>
                        {{ $message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Batch Summary Statistics -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card card-statistic">
                            <div class="card-body">
                                <div class="stat-label">Total Employees</div>
                                <h3 class="mb-1" style="color: #0284c7;">{{ $payrolls->count() }}</h3>
                                <small class="text-muted">In this batch</small>
                            </div>
                            <div class="card-icon" style="color: #0284c7; opacity: 0.2;">
                                <i class="feather-users" style="font-size: 2.5rem;"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-statistic">
                            <div class="card-body">
                                <div class="stat-label">Total Gross</div>
                                <h3 class="mb-1" style="color: #059669;">₱{{ number_format($totalGross, 2) }}</h3>
                                <small class="text-muted">Total payroll</small>
                            </div>
                            <div class="card-icon" style="color: #059669; opacity: 0.2;">
                                <i class="feather-dollar-sign" style="font-size: 2.5rem;"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-statistic">
                            <div class="card-body">
                                <div class="stat-label">Total Deductions</div>
                                <h3 class="mb-1" style="color: #d97706;">₱{{ number_format(abs($totalGross - $totalNet), 2) }}</h3>
                                <small class="text-muted">Total deductions</small>
                            </div>
                            <div class="card-icon" style="color: #d97706; opacity: 0.2;">
                                <i class="feather-minus-circle" style="font-size: 2.5rem;"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-statistic">
                            <div class="card-body">
                                <div class="stat-label">Total Net</div>
                                <h3 class="mb-1" style="color: #16a34a;">₱{{ number_format($totalNet, 2) }}</h3>
                                <small class="text-muted">Net amount</small>
                            </div>
                            <div class="card-icon" style="color: #16a34a; opacity: 0.2;">
                                <i class="feather-check-circle" style="font-size: 2.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>

                @php
                    $filterLabel = $status === 'pending' ? 'Pending Payroll' : ucfirst($status) . ' Payroll';
                    $tablePayrolls = $payrolls;
                    $statusTag = $status === 'pending' ? 'Pending' : ucfirst($status);
                    $tagBg = $status === 'pending' ? '#fffbeb' : ($status === 'approved' ? '#f0f9ff' : '#fff1f2');
                    $tagColor = $status === 'pending' ? '#d97706' : ($status === 'approved' ? '#0284c7' : '#e11d48');
                @endphp

                <h5 class="mt-4 mb-3">{{ $filterLabel }}</h5>
                <div class="table-responsive mb-5">
                    <table class="table table-hover w-100 mb-0">
                        <thead>
                            <tr style="background: #f8f9fa;">
                                <th style="color: #6b7280; font-weight: 600;">Employee</th>
                                <th style="color: #6b7280; font-weight: 600;">Position</th>
                                <th class="text-right" style="color: #6b7280; font-weight: 600;">Gross Pay</th>
                                <th class="text-right" style="color: #6b7280; font-weight: 600;">Deductions</th>
                                <th class="text-right" style="color: #6b7280; font-weight: 600;">Net Pay</th>
                                <th class="text-center" style="color: #6b7280; font-weight: 600;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tablePayrolls as $payroll)
                                <tr>
                                    <td>
                                        <strong>{{ $payroll->user->first_name }} {{ $payroll->user->last_name }}</strong>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $payroll->user->position ?? 'N/A' }}</small>
                                    </td>
                                    <td class="text-right">
                                        <strong>₱{{ number_format($payroll->gross_pay, 2) }}</strong>
                                    </td>
                                    <td class="text-right">
                                        <span>₱{{ number_format(abs($payroll->gross_pay - $payroll->net_pay), 2) }}</span>
                                    </td>
                                    <td class="text-right">
                                        <strong>₱{{ number_format($payroll->net_pay, 2) }}</strong>
                                    </td>
                                    <td class="text-center">
                                        <span style="display:inline-block; background: {{ $tagBg }}; color: {{ $tagColor }}; font-size:0.7rem; font-weight:700; padding:4px 10px; border-radius:20px; letter-spacing:0.4px; text-transform:uppercase;">
                                            {{ $statusTag }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <i class="feather-inbox d-block mb-2" style="font-size: 28px; opacity: 0.3;"></i>
                                        No {{ strtolower($statusTag) }} payroll records for this batch
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-light">
                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    @if($status === 'pending')
                    <form action="{{ route('payroll-approval.reject-batch') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="start" value="{{ $startDate->format('Y-m-d') }}">
                        <input type="hidden" name="end" value="{{ $endDate->format('Y-m-d') }}">
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to REJECT this entire batch?')">
                            <i class="feather-x-circle me-1"></i> Reject Batch
                        </button>
                    </form>
                    <form action="{{ route('payroll-approval.approve-batch') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="start" value="{{ $startDate->format('Y-m-d') }}">
                        <input type="hidden" name="end" value="{{ $endDate->format('Y-m-d') }}">
                        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to APPROVE this entire batch?')">
                            <i class="feather-check-circle me-1"></i> Approve Batch
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .table-responsive {
        border-radius: 0 0 8px 8px;
    }

    .btn {
        transition: all 0.2s ease;
    }

    .btn:hover {
        transform: translateY(-1px);
    }
</style>
@endsection
