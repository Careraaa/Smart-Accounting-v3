@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Bill {{ $bill->bill_number }}</h5>
                <a href="{{ route('accountant.bills.index') }}" class="btn btn-secondary btn-sm">
                    <i class="feather-arrow-left"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6>Supplier Information</h6>
                    <p class="mb-2">
                        <strong>Supplier:</strong> {{ $bill->supplier->name }}
                    </p>
                    <p class="mb-2">
                        <strong>Contact:</strong> {{ $bill->supplier->contact_person }}
                    </p>
                    <p class="mb-2">
                        <strong>Address:</strong> {{ $bill->supplier->address }}
                    </p>
                </div>
                <div class="col-md-6">
                    <h6>Bill Information</h6>
                    <p class="mb-2">
                        <strong>Bill Number:</strong> {{ $bill->bill_number }}
                    </p>
                    <p class="mb-2">
                        <strong>Bill Date:</strong> {{ $bill->bill_date->format('M d, Y') }}
                    </p>
                    <p class="mb-2">
                        <strong>Due Date:</strong> {{ $bill->due_date->format('M d, Y') }}
                    </p>
                </div>
            </div>

            <hr>

            <div class="row mb-4">
                <div class="col-md-3">
                    <p class="text-muted mb-1">Bill Amount</p>
                    <h5>₱{{ number_format($bill->amount, 2) }}</h5>
                </div>
                <div class="col-md-3">
                    <p class="text-muted mb-1">Paid</p>
                    <h5>₱{{ number_format($bill->amount - $bill->outstanding_balance, 2) }}</h5>
                </div>
                <div class="col-md-3">
                    <p class="text-muted mb-1">Outstanding</p>
                    <h5>₱{{ number_format($bill->outstanding_balance, 2) }}</h5>
                </div>
                <div class="col-md-3">
                    <p class="text-muted mb-1">Status</p>
                    <h5>
                        <span class="badge bg-{{ $bill->payment_status === 'Paid' ? 'success' : ($bill->payment_status === 'Partial' ? 'warning' : 'danger') }}">
                            {{ $bill->payment_status }}
                        </span>
                    </h5>
                </div>
            </div>

            <hr>

            <h6 class="mb-3">Bill Details</h6>
            <p class="mb-3">{{ $bill->description }}</p>

            <p class="mb-3">
                <strong>GL Account:</strong> {{ $bill->glAccount->code }} - {{ $bill->glAccount->name }}
            </p>

            @if($bill->journalEntry)
                <p class="mb-3">
                    <strong>Posted GL Entry:</strong>
                    <a href="{{ route('accountant.journal-entries.show', $bill->journalEntry) }}">
                        JE-{{ str_pad($bill->journalEntry->id, 6, '0', STR_PAD_LEFT) }}
                    </a>
                </p>
            @endif

            <hr>

            <div class="d-flex gap-2 flex-wrap">
                @if($bill->payment_status !== 'Paid')
                    <form action="{{ route('accountant.bills.process-payment', $bill) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="feather-check-circle"></i> Record Payment
                        </button>
                    </form>
                @endif

                @if(!$bill->journalEntry)
                    <form action="{{ route('accountant.bills.post', $bill) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="feather-upload"></i> Post to GL
                        </button>
                    </form>
                @endif

                <button onclick="printBill()" class="btn btn-secondary">
                    <i class="feather-printer"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function printBill() {
        window.print();
    }
</script>
@endpush
@endsection
