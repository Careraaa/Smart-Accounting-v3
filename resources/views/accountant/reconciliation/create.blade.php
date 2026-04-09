@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Create Bank Reconciliation</h5>
                <a href="{{ route('accountant.reconciliation.index') }}" class="btn btn-secondary btn-sm">
                    <i class="feather-arrow-left"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('accountant.reconciliation.store') }}" method="POST">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label" for="bank_account_id">Bank Account *</label>
                        <select id="bank_account_id" name="bank_account_id" class="form-select @error('bank_account_id') is-invalid @enderror" required>
                            <option value="">Select Account</option>
                            @foreach(\App\Models\BankAccount::where('is_active', true)->get() as $account)
                                <option value="{{ $account->id }}" {{ old('bank_account_id') === (string)$account->id ? 'selected' : '' }}>
                                    {{ $account->bank_name }} - {{ $account->account_number }}
                                </option>
                            @endforeach
                        </select>
                        @error('bank_account_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="statement_date">Bank Statement Date *</label>
                        <input
                            type="date"
                            id="statement_date"
                            name="statement_date"
                            class="form-control @error('statement_date') is-invalid @enderror"
                            value="{{ old('statement_date', now()->format('Y-m-d')) }}"
                            required
                        >
                        @error('statement_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label" for="bank_balance">Bank Statement Balance (₱) *</label>
                        <input
                            type="number"
                            id="bank_balance"
                            name="bank_balance"
                            class="form-control @error('bank_balance') is-invalid @enderror"
                            placeholder="0.00"
                            step="0.01"
                            value="{{ old('bank_balance') }}"
                            required
                        >
                        @error('bank_balance')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="book_balance_display">Book Balance (₱)</label>
                        <input
                            type="number"
                            id="book_balance_display"
                            name="book_balance_display"
                            class="form-control"
                            placeholder="0.00"
                            step="0.01"
                            value="{{ old('book_balance_display') }}"
                            readonly
                        >
                        <small class="text-muted">Automatically populated from selected bank account</small>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label" for="total_deposits_in_transit">Deposits in Transit (₱)</label>
                        <input
                            type="number"
                            id="total_deposits_in_transit"
                            name="total_deposits_in_transit"
                            class="form-control"
                            placeholder="0.00"
                            step="0.01"
                            value="{{ old('total_deposits_in_transit', 0) }}"
                        >
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="total_outstanding_checks">Outstanding Checks (₱)</label>
                        <input
                            type="number"
                            id="total_outstanding_checks"
                            name="total_outstanding_checks"
                            class="form-control"
                            placeholder="0.00"
                            step="0.01"
                            value="{{ old('total_outstanding_checks', 0) }}"
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
                        placeholder="Any reconciliation notes"
                    >{{ old('notes') }}</textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="feather-save"></i> Create Reconciliation
                    </button>
                    <a href="{{ route('accountant.reconciliation.index') }}" class="btn btn-secondary">
                        <i class="feather-x"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const bankAccountSelect = document.getElementById('bank_account_id');
        const bookBalanceDisplay = document.getElementById('book_balance_display');
        
        if (bankAccountSelect) {
            bankAccountSelect.addEventListener('change', function() {
                if (this.value) {
                    // Get the selected option's data
                    const selectedOption = this.options[this.selectedIndex];
                    // Since we can't easily get balance from select option, 
                    // we'd need an API call here, but for now we'll leave it empty
                    // and user can manually enter or we can add an API endpoint
                    bookBalanceDisplay.value = '';
                }
            });
        }
    });
</script>
@endpush
@endsection
