@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    {{-- Cash Position Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-xxl-3 col-lg-4 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="dash-label">Cash on Hand</span>
                        <span class="dash-icon"><i class="feather-dollar-sign"></i></span>
                    </div>
                    <div class="dash-value">₱{{ number_format($cashOnHand, 2) }}</div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-lg-4 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="dash-label">Total Bank Accounts</span>
                        <span class="dash-icon di-blue"><i class="feather-activity"></i></span>
                    </div>
                    <div class="dash-value">{{ $totalBankAccounts }}</div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-lg-4 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="dash-label">Undeposited Funds</span>
                        <span class="dash-icon di-amber"><i class="feather-alert-circle"></i></span>
                    </div>
                    <div class="dash-value">₱{{ number_format($undepositedFunds, 2) }}</div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-lg-4 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="dash-label">Pending Receipts</span>
                        <span class="dash-icon di-red"><i class="feather-inbox"></i></span>
                    </div>
                    <div class="dash-value">{{ $pendingReceipts }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="row g-3 mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-3">Quick Actions</h6>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('accountant.cash.receipt.create') }}" class="btn btn-primary">
                            <i class="feather-plus"></i> Record Cash Receipt
                        </a>
                        <a href="{{ route('accountant.cash.receipts') }}" class="btn btn-info">
                            <i class="feather-list"></i> View All Receipts
                        </a>
                        <a href="{{ route('accountant.reconciliation.create') }}" class="btn btn-success">
                            <i class="feather-check-circle"></i> Bank Reconciliation
                        </a>
                        <a href="{{ route('accountant.cash.bank-accounts') }}" class="btn btn-secondary">
                            <i class="feather-settings"></i> Bank Accounts
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Receipts --}}
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Recent Cash Receipts</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Receipt #</th>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentReceipts as $receipt)
                            <tr>
                                <td><strong>{{ $receipt->receipt_number }}</strong></td>
                                <td>{{ $receipt->receipt_date->format('M d, Y') }}</td>
                                <td>{{ $receipt->description }}</td>
                                <td>₱{{ number_format($receipt->amount, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $receipt->is_deposited ? 'success' : 'warning' }}">
                                        {{ $receipt->is_deposited ? 'Deposited' : 'Pending' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-info">
                                        <i class="feather-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    No receipts recorded yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
