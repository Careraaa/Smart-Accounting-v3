@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Bank Accounts</h5>
                <a href="{{ route('accountant.cash.bank-account.create') }}" class="btn btn-primary btn-sm">
                    <i class="feather-plus"></i> New Account
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Bank Name</th>
                            <th>Account Number</th>
                            <th>Account Type</th>
                            <th class="text-end">Balance</th>
                            <th>GL Account</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bankAccounts as $account)
                            <tr>
                                <td><strong>{{ $account->bank_name }}</strong></td>
                                <td>
                                    <code>{{ $account->account_number }}</code>
                                </td>
                                <td>{{ $account->account_type }}</td>
                                <td class="text-end">₱{{ number_format($account->current_balance, 2) }}</td>
                                <td>
                                    {{ $account->glAccount->code }} - {{ $account->glAccount->name }}
                                </td>
                                <td>
                                    <span class="badge bg-{{ $account->is_active ? 'success' : 'danger' }}">
                                        {{ $account->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-info" title="View">
                                        <i class="feather-eye"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="feather-edit-2"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    No bank accounts configured.
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
