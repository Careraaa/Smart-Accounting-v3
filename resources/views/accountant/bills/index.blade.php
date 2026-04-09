@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Supplier Bills</h5>
                <a href="{{ route('accountant.bills.create') }}" class="btn btn-primary btn-sm">
                    <i class="feather-plus"></i> New Bill
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3">
                    <input type="text" id="search" class="form-control" placeholder="Search bills...">
                </div>
                <div class="col-md-3">
                    <select id="statusFilter" class="form-select">
                        <option value="">All Status</option>
                        <option value="Unpaid">Unpaid</option>
                        <option value="Partial">Partial</option>
                        <option value="Paid">Paid</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" id="dateFilter" class="form-control">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary w-100" onclick="filterBills()">
                        <i class="feather-filter"></i> Filter
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Bill #</th>
                            <th>Supplier</th>
                            <th>Date</th>
                            <th>Due Date</th>
                            <th class="text-end">Amount</th>
                            <th class="text-end">Outstanding</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bills as $bill)
                            <tr>
                                <td><strong>{{ $bill->bill_number }}</strong></td>
                                <td>{{ $bill->supplier->name }}</td>
                                <td>{{ $bill->bill_date->format('M d, Y') }}</td>
                                <td>{{ $bill->due_date->format('M d, Y') }}</td>
                                <td class="text-end">₱{{ number_format($bill->amount, 2) }}</td>
                                <td class="text-end"><strong>₱{{ number_format($bill->outstanding_balance, 2) }}</strong></td>
                                <td>
                                    <span class="badge bg-{{ $bill->payment_status === 'Paid' ? 'success' : ($bill->payment_status === 'Partial' ? 'warning' : 'danger') }}">
                                        {{ $bill->payment_status }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('accountant.bills.show', $bill) }}" class="btn btn-sm btn-info" title="View">
                                            <i class="feather-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    No bills found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-end">
                {{ $bills->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function filterBills() {
        // Implement filtering logic
    }
</script>
@endpush
@endsection
