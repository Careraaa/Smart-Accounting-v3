@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Cash Receipts</h5>
                <a href="{{ route('accountant.cash.receipt.create') }}" class="btn btn-primary btn-sm">
                    <i class="feather-plus"></i> New Receipt
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <input type="text" id="search" class="form-control" placeholder="Search receipts...">
                </div>
                <div class="col-md-3">
                    <select id="statusFilter" class="form-select">
                        <option value="">All Status</option>
                        <option value="0">Pending</option>
                        <option value="1">Deposited</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" id="dateFilter" class="form-control">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100" onclick="filterReceipts()">
                        <i class="feather-filter"></i> Filter
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Receipt #</th>
                            <th>Date</th>
                            <th>Description</th>
                            <th class="text-end">Amount</th>
                            <th>Status</th>
                            <th>Bank Account</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($receipts as $receipt)
                            <tr>
                                <td><strong>{{ $receipt->receipt_number }}</strong></td>
                                <td>{{ $receipt->receipt_date->format('M d, Y') }}</td>
                                <td>{{ $receipt->description }}</td>
                                <td class="text-end">₱{{ number_format($receipt->amount, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $receipt->is_deposited ? 'success' : 'warning' }}">
                                        {{ $receipt->is_deposited ? 'Deposited' : 'Pending' }}
                                    </span>
                                </td>
                                <td>{{ $receipt->bankAccount->account_name ?? 'N/A' }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="#" class="btn btn-sm btn-info" title="View">
                                            <i class="feather-eye"></i>
                                        </a>
                                        @if(!$receipt->is_deposited)
                                            <button class="btn btn-sm btn-success" onclick="depositReceipt({{ $receipt->id }})" title="Deposit">
                                                <i class="feather-upload"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    No receipts found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-end mt-3">
                {{ $receipts->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function filterReceipts() {
        // Implement filtering logic
    }

    function depositReceipt(receiptId) {
        if (confirm('Deposit this receipt?')) {
            // Submit deposit action
        }
    }
</script>
@endpush
@endsection
