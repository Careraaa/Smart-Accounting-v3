@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Edit Cash Receipt</h5>
                <a href="{{ route('accountant.cash.receipts') }}" class="btn btn-secondary btn-sm">
                    <i class="feather-arrow-left"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('accountant.cash.receipt.update', $receipt) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label" for="receipt_date">Receipt Date *</label>
                        <input
                            type="date"
                            id="receipt_date"
                            name="receipt_date"
                            class="form-control @error('receipt_date') is-invalid @enderror"
                            value="{{ old('receipt_date', $receipt->receipt_date->format('Y-m-d')) }}"
                            required
                        >
                        @error('receipt_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="amount">Amount (₱) *</label>
                        <input
                            type="number"
                            id="amount"
                            name="amount"
                            class="form-control @error('amount') is-invalid @enderror"
                            placeholder="0.00"
                            step="0.01"
                            value="{{ old('amount', $receipt->amount) }}"
                            required
                        >
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label" for="payment_method">Payment Method *</label>
                        <select id="payment_method" name="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                            <option value="">Select Method</option>
                            <option value="Cash" {{ old('payment_method', $receipt->payment_method) === 'Cash' ? 'selected' : '' }}>Cash</option>
                            <option value="Check" {{ old('payment_method', $receipt->payment_method) === 'Check' ? 'selected' : '' }}>Check</option>
                            <option value="Transfer" {{ old('payment_method', $receipt->payment_method) === 'Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="Card" {{ old('payment_method', $receipt->payment_method) === 'Card' ? 'selected' : '' }}>Credit Card</option>
                        </select>
                        @error('payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="payment_reference">Payment Reference</label>
                        <input
                            type="text"
                            id="payment_reference"
                            name="payment_reference"
                            class="form-control"
                            value="{{ old('payment_reference', $receipt->payment_reference) }}"
                            placeholder="Check #, Transaction ID, etc."
                        >
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label" for="bank_account_id">Bank Account</label>
                        <select id="bank_account_id" name="bank_account_id" class="form-select">
                            <option value="">Select Account</option>
                            @foreach(\App\Models\BankAccount::where('is_active', true)->get() as $account)
                                <option value="{{ $account->id }}" {{ old('bank_account_id', $receipt->bank_account_id) === (string)$account->id ? 'selected' : '' }}>
                                    {{ $account->bank_name }} - {{ $account->account_number }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="source_type">Source Type</label>
                        <input
                            type="text"
                            id="source_type"
                            name="source_type"
                            class="form-control"
                            value="{{ old('source_type', $receipt->source_type) }}"
                            placeholder="e.g., Remittance, Collections"
                        >
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="notes">Notes</label>
                    <textarea
                        id="notes"
                        name="notes"
                        class="form-control"
                        rows="3"
                        placeholder="Additional information"
                    >{{ old('notes', $receipt->notes) }}</textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="feather-save"></i> Update Receipt
                    </button>
                    <a href="{{ route('accountant.cash.receipts') }}" class="btn btn-secondary">
                        <i class="feather-x"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
