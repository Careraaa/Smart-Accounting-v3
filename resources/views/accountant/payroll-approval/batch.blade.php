@extends('layouts.layout')

@push('styles')
    @include('accountant._ui-styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
        .prl-page { font-family: 'Sora', sans-serif; }
        .prl-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 22px; }
        @media (max-width: 1100px) { .prl-stats { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 600px) { .prl-stats { grid-template-columns: 1fr; } }
        .prl-stat { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; padding: 18px 20px; display: flex; align-items: flex-start; gap: 14px; position: relative; overflow: hidden; }
        .prl-stat::after { content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 3px; border-radius: 0 0 14px 14px; }
        .prl-stat.s-blue::after { background: #0284c7; }
        .prl-stat.s-green::after { background: #16a34a; }
        .prl-stat.s-amber::after { background: #d97706; }
        .prl-stat.s-red::after { background: #c8292a; }
        .prl-stat-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .prl-stat.s-blue .prl-stat-icon { background: #f0f9ff; color: #0284c7; }
        .prl-stat.s-green .prl-stat-icon { background: #f0fdf4; color: #16a34a; }
        .prl-stat.s-amber .prl-stat-icon { background: #fffbeb; color: #d97706; }
        .prl-stat.s-red .prl-stat-icon { background: #fff0f0; color: #c8292a; }
        .prl-stat-label { font-size: 0.67rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.09em; color: #9ca3af; margin-bottom: 4px; }
        .prl-stat-value { font-size: 1.15rem; font-weight: 800; color: #111827; line-height: 1.2; font-variant-numeric: tabular-nums; font-family: 'DM Mono', monospace; }
        .prl-stat-sub { font-size: 0.73rem; color: #9ca3af; margin-top: 4px; }
    </style>
@endpush

@section('content')
@php
    $filterLabel = $status === 'pending' ? 'Pending payroll' : ucfirst($status) . ' payroll';
    $statusTag = $status === 'pending' ? 'Pending' : ucfirst($status);
    $tagBg = $status === 'pending' ? '#fffbeb' : ($status === 'approved' ? '#f0f9ff' : '#fff1f2');
    $tagColor = $status === 'pending' ? '#d97706' : ($status === 'approved' ? '#0284c7' : '#e11d48');
@endphp

<div class="col-12">
    <div class="remui-page prl-page">
        <div class="remui-backdrop">
            <div class="remui-grid"></div>
        </div>

        <div class="remui-hero mb-3">
            <div>
                <h5 class="remui-title">Approve payroll batch</h5>
                <p class="remui-subtitle mb-0">
                    {{ $startDate->format('F d, Y') }} – {{ $endDate->format('F d, Y') }}
                    <span class="badge rounded-pill ms-2" style="background:{{ $tagBg }};color:{{ $tagColor }};font-size:0.65rem;">{{ $statusTag }}</span>
                </p>
            </div>
            <a href="{{ route('payroll-approval.index') }}" class="emp-action-btn emp-action-view">
                <i class="feather-arrow-left"></i><span>Back</span>
            </a>
        </div>

        @if (session('success'))
            <div class="acd-flash success"><i class="feather-check-circle"></i> {{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="acd-flash error"><i class="feather-alert-circle"></i> {{ session('error') }}</div>
        @endif

        <div class="prl-stats">
            <div class="prl-stat s-blue">
                <div class="prl-stat-icon"><i class="feather-users"></i></div>
                <div>
                    <div class="prl-stat-label">Employees</div>
                    <div class="prl-stat-value">{{ $payrolls->count() }}</div>
                    <div class="prl-stat-sub">In this batch</div>
                </div>
            </div>
            <div class="prl-stat s-green">
                <div class="prl-stat-icon"><i class="feather-dollar-sign"></i></div>
                <div>
                    <div class="prl-stat-label">Total gross</div>
                    <div class="prl-stat-value">₱{{ number_format($totalGross, 0) }}</div>
                    <div class="prl-stat-sub">Combined</div>
                </div>
            </div>
            <div class="prl-stat s-amber">
                <div class="prl-stat-icon"><i class="feather-minus-circle"></i></div>
                <div>
                    <div class="prl-stat-label">Total deductions</div>
                    <div class="prl-stat-value">₱{{ number_format($totalDeductions, 0) }}</div>
                    <div class="prl-stat-sub">From payroll rows</div>
                </div>
            </div>
            <div class="prl-stat s-red">
                <div class="prl-stat-icon"><i class="feather-check-circle"></i></div>
                <div>
                    <div class="prl-stat-label">Total net</div>
                    <div class="prl-stat-value">₱{{ number_format($totalNet, 0) }}</div>
                    <div class="prl-stat-sub">Net pay</div>
                </div>
            </div>
        </div>

        <div class="card remui-card mb-3">
            <div class="card-header">
                <span class="card-title mb-0">{{ $filterLabel }}</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover w-100 mb-0 remui-table">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Position</th>
                                <th class="text-end">Gross pay</th>
                                <th class="text-end">Deductions</th>
                                <th class="text-end">Net pay</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payrolls as $payroll)
                                <tr>
                                    <td><strong>{{ $payroll->user->first_name }} {{ $payroll->user->last_name }}</strong></td>
                                    <td><span class="text-muted" style="font-size:0.82rem;">{{ $payroll->user->position ?? 'N/A' }}</span></td>
                                    <td class="text-end font-monospace">₱{{ number_format($payroll->gross_pay, 2) }}</td>
                                    <td class="text-end font-monospace text-muted">₱{{ number_format($payroll->total_deductions ?? abs($payroll->gross_pay - $payroll->net_pay), 2) }}</td>
                                    <td class="text-end font-monospace fw-bold" style="color:#15803d;">₱{{ number_format($payroll->net_pay, 2) }}</td>
                                    <td class="text-center">
                                        <span style="display:inline-block;background:{{ $tagBg }};color:{{ $tagColor }};font-size:0.68rem;font-weight:700;padding:4px 10px;border-radius:20px;text-transform:uppercase;">
                                            {{ $statusTag }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <i class="feather-inbox d-block mb-2" style="font-size:28px;opacity:.3;"></i>
                                        No payroll records in this batch
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($status === 'pending' && $payrolls->isNotEmpty())
                <div class="card-footer bg-white border-top" style="padding:16px 18px;">
                    <div class="d-flex flex-wrap gap-2 justify-content-end">
                        <form action="{{ route('payroll-approval.reject-batch') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="start" value="{{ $startDate->format('Y-m-d') }}">
                            <input type="hidden" name="end" value="{{ $endDate->format('Y-m-d') }}">
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Reject this entire batch?')">
                                <i class="feather-x-circle me-1"></i> Reject batch
                            </button>
                        </form>
                        <form action="{{ route('payroll-approval.approve-batch') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="start" value="{{ $startDate->format('Y-m-d') }}">
                            <input type="hidden" name="end" value="{{ $endDate->format('Y-m-d') }}">
                            <button type="submit" class="btn btn-sm text-white" style="background:#16a34a;border:none;" onclick="return confirm('Approve this entire batch?')">
                                <i class="feather-check-circle me-1"></i> Approve batch
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
