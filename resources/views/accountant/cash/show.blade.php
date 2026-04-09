@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Cash Receipt {{ $receipt->receipt_number }}</h5>
                <div>
                    <a href="{{ route('accountant.cash.receipts') }}" class="btn btn-secondary btn-sm">
                        <i class="feather-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-3">
                    <p class="text-muted mb-1">Receipt Date</p>
                    <h5>{{ $receipt->receipt_date->format('M d, Y') }}</h5>
                </div>
                <div class="col-md-3">
                    <p class="text-muted mb-1">Amount</p>
                    <h5>₱{{ number_format($receipt->amount, 2) }}</h5>
                </div>
                <div class="col-md-3">
                    <p class="text-muted mb-1">Payment Method</p>
                    <h5>{{ $receipt->payment_method }}</h5>
                </div>
                <div class="col-md-3">
                    <p class="text-muted mb-1">Status</p>
                    <h5>
                        @if($receipt->is_deposited)
                            <span class="badge bg-success">Deposited</span>
                        @else
                            <span class="badge bg-warning">Pending Deposit</span>
                        @endif
                    </h5>
                </div>
            </div>

            <hr>

            <h6 class="mb-3">Receipt Details</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <p class="mb-2">
                        <strong>Reference #:</strong> {{ $receipt->payment_reference }}
                    </p>
                    <p class="mb-2">
                        <strong>Source Type:</strong> {{ $receipt->source_type }}
                    </p>
                    <p class="mb-2">
                        <strong>Source Reference:</strong> {{ $receipt->source_reference }}
                    </p>
                </div>
                <div class="col-md-6">
                    <p class="mb-2">
                        <strong>Bank Account:</strong> 
                        @if($receipt->bankAccount)
                            {{ $receipt->bankAccount->bank_name }} - {{ $receipt->bankAccount->account_number }}
                        @else
                            N/A
                        @endif
                    </p>
                    <p class="mb-2">
                        <strong>Deposited Date:</strong> 
                        {{ $receipt->deposit_date ? $receipt->deposit_date->format('M d, Y') : 'Not yet deposited' }}
                    </p>
                    <p class="mb-2">
                        <strong>Created By:</strong> {{ $receipt->createdBy->name ?? 'N/A' }}
                    </p>
                </div>
            </div>

            @if($receipt->notes)
                <hr>
                <h6 class="mb-3">Notes</h6>
                <p>{{ $receipt->notes }}</p>
            @endif

            @if($receipt->journalEntry)
                <hr>
                <h6 class="mb-3">Posted GL Entry</h6>
                <p>
                    <a href="{{ route('accountant.journal-entries.show', $receipt->journalEntry) }}">
                        JE-{{ str_pad($receipt->journalEntry->id, 6, '0', STR_PAD_LEFT) }}
                    </a>
                </p>
            @endif

            <hr>

            <div class="d-flex gap-2 flex-wrap">
                @if(!$receipt->is_deposited && auth()->user()->hasRole('accountant'))
                    <form action="{{ route('accountant.cash.deposit') }}" method="POST" style="display: inline;">
                        @csrf
                        <input type="hidden" name="receipt_id" value="{{ $receipt->id }}">
                        <button type="submit" class="btn btn-success">
                            <i class="feather-check-circle"></i> Mark as Deposited
                        </button>
                    </form>
                @endif

                <button onclick="window.print()" class="btn btn-secondary">
                    <i class="feather-printer"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
