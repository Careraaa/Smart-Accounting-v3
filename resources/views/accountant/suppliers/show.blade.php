@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">{{ $supplier->name }}</h5>
                <div>
                    <a href="{{ route('accountant.suppliers.edit', $supplier) }}" class="btn btn-warning btn-sm">
                        <i class="feather-edit"></i> Edit
                    </a>
                    <a href="{{ route('accountant.suppliers.index') }}" class="btn btn-secondary btn-sm">
                        <i class="feather-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6>Supplier Information</h6>
                    <p class="mb-2">
                        <strong>Contact Person:</strong> {{ $supplier->contact_person }}
                    </p>
                    <p class="mb-2">
                        <strong>Email:</strong> <a href="mailto:{{ $supplier->email }}">{{ $supplier->email }}</a>
                    </p>
                    <p class="mb-2">
                        <strong>Phone:</strong> {{ $supplier->phone }}
                    </p>
                    <p class="mb-2">
                        <strong>Tax ID:</strong> {{ $supplier->tax_id }}
                    </p>
                    <p class="mb-2">
                        <strong>Address:</strong> {{ $supplier->address }}
                    </p>
                </div>
                <div class="col-md-6">
                    <h6>Financial Information</h6>
                    <p class="mb-2">
                        <strong>Outstanding Balance:</strong>
                        <h5>₱{{ number_format($supplier->getOutstandingBalance(), 2) }}</h5>
                    </p>
                    <p class="mb-2">
                        <strong>Payment Terms:</strong> {{ $supplier->payment_terms }}
                    </p>
                    <p class="mb-2">
                        <strong>Bank Name:</strong> {{ $supplier->bank_name }}
                    </p>
                    <p class="mb-2">
                        <strong>Bank Account:</strong> {{ $supplier->bank_account }}
                    </p>
                    <p class="mb-2">
                        <strong>Status:</strong>
                        <span class="badge bg-{{ $supplier->is_active ? 'success' : 'danger' }}">
                            {{ $supplier->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </p>
                </div>
            </div>

            <hr>

            <h6 class="mb-3">Outstanding Bills</h6>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>Bill #</th>
                            <th>Date</th>
                            <th>Description</th>
                            <th class="text-end">Amount</th>
                            <th class="text-end">Paid</th>
                            <th class="text-end">Outstanding</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($supplier->bills()->where('payment_status', '!=', 'Paid')->get() as $bill)
                            <tr>
                                <td>{{ $bill->bill_number }}</td>
                                <td>{{ $bill->bill_date->format('M d, Y') }}</td>
                                <td>{{ $bill->description }}</td>
                                <td class="text-end">₱{{ number_format($bill->amount, 2) }}</td>
                                <td class="text-end">₱{{ number_format($bill->amount - $bill->outstanding_balance, 2) }}</td>
                                <td class="text-end"><strong>₱{{ number_format($bill->outstanding_balance, 2) }}</strong></td>
                                <td>
                                    <span class="badge bg-{{ $bill->payment_status === 'Paid' ? 'success' : 'warning' }}">
                                        {{ $bill->payment_status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    No outstanding bills.
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
