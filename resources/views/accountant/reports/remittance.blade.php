@extends('layouts.layout')

@push('styles')
    @include('accountant._ui-styles')
@endpush

@section('content')
@php
    $sumCol = $remittances->sum('total_collection');
    $sumExp = $remittances->sum('total_expenses');
    $sumNet = $remittances->sum('net_remittance');
@endphp

<div class="col-12">
    <div class="remui-page acd-page">
        <div class="remui-backdrop">
            <div class="remui-grid"></div>
        </div>

        <div class="remui-hero mb-3">
            <div>
                <h5 class="remui-title">Remittance reports</h5>
                <p class="remui-subtitle mb-0">All remittance rows in the system — same chrome as remittance clerk screens.</p>
            </div>
            <a href="{{ route('remittance-approval.index') }}" class="emp-action-btn emp-action-view">
                <i class="feather-check-square"></i><span>Approvals</span>
            </a>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="dash-label">Total collection</span>
                            <span class="dash-icon di-green"><i class="feather-arrow-up-circle"></i></span>
                        </div>
                        <div class="dash-value">₱{{ number_format($sumCol, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="dash-label">Total expenses</span>
                            <span class="dash-icon di-red"><i class="feather-arrow-down-circle"></i></span>
                        </div>
                        <div class="dash-value">₱{{ number_format($sumExp, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="dash-label">Net remittance</span>
                            <span class="dash-icon di-blue"><i class="feather-trending-up"></i></span>
                        </div>
                        <div class="dash-value">₱{{ number_format($sumNet, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card remui-card">
            <div class="card-header">
                <span class="card-title mb-0">Records</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover w-100 mb-0 remui-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Route</th>
                                <th>Collection</th>
                                <th>Expenses</th>
                                <th>Net remittance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($remittances as $remittance)
                                <tr>
                                    <td class="text-muted" style="font-size:0.82rem;">{{ $remittance->remittance_date?->format('M d, Y') }}</td>
                                    <td>{{ $remittance->route->route_name ?? '—' }}</td>
                                    <td class="font-monospace">₱{{ number_format($remittance->total_collection, 2) }}</td>
                                    <td class="font-monospace text-muted">₱{{ number_format($remittance->total_expenses, 2) }}</td>
                                    <td class="font-monospace fw-bold" style="color:#15803d;">₱{{ number_format($remittance->net_remittance, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        <i class="feather-inbox d-block mb-2" style="font-size:28px;opacity:.3;"></i>
                                        No remittance records
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
