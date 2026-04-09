@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    Bank Reconciliation - {{ $reconciliation->bankAccount->bank_name }}
                </h5>
                <a href="{{ route('accountant.reconciliation.index') }}" class="btn btn-secondary btn-sm">
                    <i class="feather-arrow-left"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-3">
                    <p class="text-muted mb-1">Bank Balance</p>
                    <h5>₱{{ number_format($reconciliation->bank_balance, 2) }}</h5>
                </div>
                <div class="col-md-3">
                    <p class="text-muted mb-1">Book Balance</p>
                    <h5>₱{{ number_format($reconciliation->book_balance, 2) }}</h5>
                </div>
                <div class="col-md-3">
                    <p class="text-muted mb-1">Difference</p>
                    <h5 class="text-{{ abs($reconciliation->bank_balance - $reconciliation->book_balance) < 0.01 ? 'success' : 'danger' }}">
                        ₱{{ number_format(abs($reconciliation->bank_balance - $reconciliation->book_balance), 2) }}
                    </h5>
                </div>
                <div class="col-md-3">
                    <p class="text-muted mb-1">Status</p>
                    <h5>
                        <span class="badge bg-{{ $reconciliation->is_balanced ? 'success' : 'warning' }}">
                            {{ $reconciliation->is_balanced ? 'Balanced' : 'Not Balanced' }}
                        </span>
                    </h5>
                </div>
            </div>

            <hr>

            <h6 class="mb-3">Reconciliation Details</h6>
            <div class="table-responsive">
                <table class="table">
                    <tr>
                        <td><strong>Statement Date:</strong></td>
                        <td>{{ $reconciliation->statement_date->format('M d, Y') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Deposits in Transit:</strong></td>
                        <td>₱{{ number_format($reconciliation->total_deposits_in_transit, 2) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Outstanding Checks:</strong></td>
                        <td>₱{{ number_format($reconciliation->total_outstanding_checks, 2) }}</td>
                    </tr>
                </table>
            </div>

            <hr>

            <h6 class="mb-3">Reconciliation Formula</h6>
            <div class="alert alert-info">
                <p class="mb-2">
                    <strong>Bank Balance:</strong> ₱{{ number_format($reconciliation->bank_balance, 2) }}
                </p>
                <p class="mb-2">
                    <strong>Plus: Deposits in Transit:</strong> + ₱{{ number_format($reconciliation->total_deposits_in_transit, 2) }}
                </p>
                <p class="mb-2">
                    <strong>Less: Outstanding Checks:</strong> - ₱{{ number_format($reconciliation->total_outstanding_checks, 2) }}
                </p>
                <p class="border-top pt-2 mt-2 mb-0">
                    <strong>Calculated Balance:</strong> = ₱{{ number_format($reconciled, 2) }}
                </p>
            </div>

            @if($isBalanced)
                <div class="alert alert-success">
                    ✓ Reconciliation is balanced. Book balance matches calculated balance.
                </div>
            @else
                <div class="alert alert-danger">
                    ✗ Reconciliation is NOT balanced. Please verify deposits and outstanding items.
                </div>
            @endif

            <div class="d-flex gap-2">
                @if(!$isBalanced && auth()->user()->hasRole('accountant'))
                    <form action="{{ route('accountant.reconciliation.complete', $reconciliation) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="feather-check-circle"></i> Mark as Balanced
                        </button>
                    </form>
                @endif
                <button onclick="printReconciliation()" class="btn btn-secondary">
                    <i class="feather-printer"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function printReconciliation() {
        window.print();
    }
</script>
@endpush
@endsection
