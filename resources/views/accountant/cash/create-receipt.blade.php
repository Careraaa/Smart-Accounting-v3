@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Record Cash Receipt</h5>
                <a href="{{ route('accountant.cash.receipts') }}" class="btn btn-secondary btn-sm">
                    <i class="feather-arrow-left"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('accountant.cash.receipt.store') }}" method="POST">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label" for="receipt_date">Receipt Date *</label>
                        <input
                            type="date"
                            id="receipt_date"
                            name="receipt_date"
                            class="form-control @error('receipt_date') is-invalid @enderror"
                            value="{{ old('receipt_date', now()->format('Y-m-d')) }}"
                            required
                        >
                        @error('receipt_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="bank_account_id">Bank Account *</label>
                        <select id="bank_account_id" name="bank_account_id" class="form-select @error('bank_account_id') is-invalid @enderror" required>
                            <option value="">Select Bank Account</option>
                            @foreach(\App\Models\BankAccount::where('is_active', true)->get() as $account)
                                <option value="{{ $account->id }}" {{ old('bank_account_id') === (string)$account->id ? 'selected' : '' }}>
                                    {{ $account->account_name }} ({{ $account->account_number }})
                                </option>
                            @endforeach
                        </select>
                        @error('bank_account_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label" for="amount">Amount (₱) *</label>
                        <input
                            type="number"
                            id="amount"
                            name="amount"
                            class="form-control @error('amount') is-invalid @enderror"
                            placeholder="0.00"
                            step="0.01"
                            value="{{ old('amount') }}"
                            required
                        >
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="payment_method">Payment Method *</label>
                        <select id="payment_method" name="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                            <option value="">Select Method</option>
                            <option value="Cash" {{ old('payment_method') === 'Cash' ? 'selected' : '' }}>Cash</option>
                            <option value="Check" {{ old('payment_method') === 'Check' ? 'selected' : '' }}>Check</option>
                            <option value="Bank Transfer" {{ old('payment_method') === 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="Credit Card" {{ old('payment_method') === 'Credit Card' ? 'selected' : '' }}>Credit Card</option>
                        </select>
                        @error('payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="description">Description *</label>
                    <textarea
                        id="description"
                        name="description"
                        class="form-control @error('description') is-invalid @enderror"
                        rows="3"
                        placeholder="Enter receipt description"
                        required
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label" for="reference_number">Reference Number</label>
                        <input
                            type="text"
                            id="reference_number"
                            name="reference_number"
                            class="form-control"
                            placeholder="Check #, Transaction ID, etc."
                            value="{{ old('reference_number') }}"
                        >
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="payer_name">Payer Name</label>
                        <input
                            type="text"
                            id="payer_name"
                            name="payer_name"
                            class="form-control"
                            placeholder="Source of receipt"
                            value="{{ old('payer_name') }}"
                        >
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="feather-save me-2"></i>Record Receipt
                    </button>
                    <a href="{{ route('accountant.cash.receipts') }}" class="btn btn-secondary">
                        <i class="feather-x me-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
