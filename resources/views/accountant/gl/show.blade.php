@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="card-title mb-0">
                    {{ $account->account_code }} - {{ $account->account_name }}
                </h5>
                <small class="text-muted">{{ $account->account_type }} | {{ $account->account_category }}</small>
            </div>
            <div>
                <a href="{{ route('accountant.gl.edit', $account) }}" class="btn btn-warning btn-sm">
                    <i class="feather-edit"></i> Edit
                </a>
                <a href="{{ route('accountant.gl.index') }}" class="btn btn-secondary btn-sm">
                    <i class="feather-arrow-left"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-3">
                    <p class="text-muted mb-1">Opening Balance</p>
                    <h5>₱{{ number_format($account->opening_balance, 2) }}</h5>
                </div>
                <div class="col-md-3">
                    <p class="text-muted mb-1">Current Balance</p>
                    <h5>₱{{ number_format($account->current_balance, 2) }}</h5>
                </div>
                <div class="col-md-3">
                    <p class="text-muted mb-1">Status</p>
                    <h5>
                        @if($account->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Inactive</span>
                        @endif
                    </h5>
                </div>
                <div class="col-md-3">
                    <p class="text-muted mb-1">Total Transactions</p>
                    <h5>{{ $account->journalEntryLines()->count() }}</h5>
                </div>
            </div>

            <hr>

            <h6 class="mb-3">Account Details</h6>
            <div class="row mb-4">
                <div class="col-md-6">
                    <p class="mb-2">
                        <strong>Code:</strong> {{ $account->account_code }}
                    </p>
                    <p class="mb-2">
                        <strong>Type:</strong> {{ $account->account_type }}
                    </p>
                    <p class="mb-2">
                        <strong>Category:</strong> {{ $account->account_category }}
                    </p>
                </div>
                <div class="col-md-6">
                    @if($account->parentAccount)
                        <p class="mb-2">
                            <strong>Parent Account:</strong>
                            <a href="{{ route('accountant.gl.show', $account->parentAccount) }}">
                                {{ $account->parentAccount->account_code }} - {{ $account->parentAccount->account_name }}
                            </a>
                        </p>
                    @endif
                    <p class="mb-2">
                        <strong>Created:</strong> {{ $account->created_at->format('M d, Y H:i') }}
                    </p>
                    <p class="mb-2">
                        <strong>Updated:</strong> {{ $account->updated_at->format('M d, Y H:i') }}
                    </p>
                </div>
            </div>

            <hr>

            <h6 class="mb-3">Recent Transactions</h6>
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Journal Entry</th>
                            <th>Description</th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($account->journalEntryLines()->with('journalEntry')->orderBy('created_at', 'desc')->take(20)->get() as $line)
                            <tr>
                                <td>{{ $line->journalEntry->je_date->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('accountant.journal-entries.show', $line->journalEntry) }}">
                                        JE-{{ str_pad($line->journalEntry->id, 6, '0', STR_PAD_LEFT) }}
                                    </a>
                                </td>
                                <td>{{ $line->journalEntry->description }}</td>
                                <td>{{ $line->debit_amount > 0 ? '₱' . number_format($line->debit_amount, 2) : '-' }}</td>
                                <td>{{ $line->credit_amount > 0 ? '₱' . number_format($line->credit_amount, 2) : '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ $line->journalEntry->status === 'Posted' ? 'success' : 'warning' }}">
                                        {{ $line->journalEntry->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    No transactions yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="text-center mt-3">
                <a href="{{ route('accountant.gl.ledger', $account) }}" class="btn btn-sm btn-info">
                    View Full Ledger
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
