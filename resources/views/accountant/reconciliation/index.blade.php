@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Bank Reconciliations</h5>
                <a href="{{ route('accountant.reconciliation.create') }}" class="btn btn-primary btn-sm">
                    <i class="feather-plus"></i> New Reconciliation
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Bank Account</th>
                            <th>Period</th>
                            <th>Bank Balance</th>
                            <th>Book Balance</th>
                            <th>Difference</th>
                            <th>Status</th>
                            <th>Reconciled Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reconciliations as $recon)
                            <tr>
                                <td>
                                    <strong>{{ $recon->bankAccount->bank_name }}</strong><br>
                                    <small>{{ $recon->bankAccount->account_number }}</small>
                                </td>
                                <td>{{ $recon->period_start->format('M d, Y') }} - {{ $recon->period_end->format('M d, Y') }}</td>
                                <td>₱{{ number_format($recon->bank_balance, 2) }}</td>
                                <td>₱{{ number_format($recon->book_balance, 2) }}</td>
                                <td>
                                    <strong class="text-{{ abs($recon->bank_balance - $recon->book_balance) < 0.01 ? 'success' : 'danger' }}">
                                        ₱{{ number_format(abs($recon->bank_balance - $recon->book_balance), 2) }}
                                    </strong>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $recon->is_balanced ? 'success' : 'warning' }}">
                                        {{ $recon->is_balanced ? 'Balanced' : 'Not Balanced' }}
                                    </span>
                                </td>
                                <td>{{ $recon->reconciled_at?->format('M d, Y') ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('accountant.reconciliation.show', $recon) }}" class="btn btn-sm btn-info">
                                        <i class="feather-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    No reconciliations found.
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
