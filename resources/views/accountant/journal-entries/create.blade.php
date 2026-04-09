@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Create Journal Entry</h5>
                <a href="{{ route('accountant.journal-entries.index') }}" class="btn btn-secondary btn-sm">
                    <i class="feather-arrow-left"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('accountant.journal-entries.store') }}" method="POST" id="journalEntryForm">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label" for="je_date">Entry Date *</label>
                        <input
                            type="date"
                            id="je_date"
                            name="je_date"
                            class="form-control @error('je_date') is-invalid @enderror"
                            value="{{ old('je_date', now()->format('Y-m-d')) }}"
                            required
                        >
                        @error('je_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="accounting_period_id">Accounting Period *</label>
                        <select id="accounting_period_id" name="accounting_period_id" class="form-select @error('accounting_period_id') is-invalid @enderror" required>
                            <option value="">Select Period</option>
                            @foreach(\App\Models\AccountingPeriod::where('is_open', true)->orWhere('is_locked', false)->get() as $period)
                                <option value="{{ $period->id }}" {{ old('accounting_period_id') === (string)$period->id ? 'selected' : '' }}>
                                    {{ $period->period_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('accounting_period_id')
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
                        rows="2"
                        placeholder="Enter journal entry description"
                        required
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <hr>

                <h6 class="mb-3">Journal Entry Lines</h6>
                <div id="linesContainer">
                    <div class="line-item mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">GL Account *</label>
                                <select name="lines[0][gl_account_id]" class="form-select gl-account-select" required>
                                    <option value="">Select Account</option>
                                    @foreach(\App\Models\GLAccount::where('is_active', true)->get() as $account)
                                        <option value="{{ $account->id }}" data-type="{{ $account->type }}">
                                            {{ $account->code }} - {{ $account->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Debit (₱)</label>
                                <input type="number" name="lines[0][debit_amount]" class="form-control debit-amount" step="0.01" value="0">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Credit (₱)</label>
                                <input type="number" name="lines[0][credit_amount]" class="form-control credit-amount" step="0.01" value="0">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-danger btn-sm w-100 remove-line" onclick="removeLine(this)">
                                    <i class="feather-trash-2"></i> Remove
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-secondary btn-sm mb-3" onclick="addLine()">
                    <i class="feather-plus"></i> Add Line
                </button>

                <hr>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Total Debit</label>
                        <input type="text" class="form-control" id="totalDebit" readonly value="₱0.00">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Total Credit</label>
                        <input type="text" class="form-control" id="totalCredit" readonly value="₱0.00">
                    </div>
                </div>

                <div id="balanceAlert" class="alert alert-danger" style="display: none;">
                    Journal entry is not balanced. Debit and Credit totals must be equal.
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="feather-save me-2"></i>Save as Draft
                    </button>
                    <a href="{{ route('accountant.journal-entries.index') }}" class="btn btn-secondary">
                        <i class="feather-x me-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let lineCount = 1;

    document.addEventListener('DOMContentLoaded', function() {
        updateTotals();
    });

    function addLine() {
        const container = document.getElementById('linesContainer');
        const newLine = document.createElement('div');
        newLine.className = 'line-item mb-3';
        newLine.innerHTML = `
            <div class="row">
                <div class="col-md-4">
                    <select name="lines[${lineCount}][gl_account_id]" class="form-select gl-account-select" required>
                        <option value="">Select Account</option>
                        @foreach(\App\Models\GLAccount::where('is_active', true)->get() as $account)
                            <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" name="lines[${lineCount}][debit_amount]" class="form-control debit-amount" step="0.01" value="0" onchange="updateTotals()">
                </div>
                <div class="col-md-3">
                    <input type="number" name="lines[${lineCount}][credit_amount]" class="form-control credit-amount" step="0.01" value="0" onchange="updateTotals()">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm w-100" onclick="removeLine(this)">
                        <i class="feather-trash-2"></i> Remove
                    </button>
                </div>
            </div>
        `;
        container.appendChild(newLine);
        lineCount++;
    }

    function removeLine(btn) {
        btn.closest('.line-item').remove();
        updateTotals();
    }

    function updateTotals() {
        const debits = Array.from(document.querySelectorAll('.debit-amount')).map(el => parseFloat(el.value) || 0);
        const credits = Array.from(document.querySelectorAll('.credit-amount')).map(el => parseFloat(el.value) || 0);
        
        const totalDebit = debits.reduce((a, b) => a + b, 0);
        const totalCredit = credits.reduce((a, b) => a + b, 0);
        
        document.getElementById('totalDebit').value = '₱' + totalDebit.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        document.getElementById('totalCredit').value = '₱' + totalCredit.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        
        const balanceAlert = document.getElementById('balanceAlert');
        if (Math.abs(totalDebit - totalCredit) > 0.01) {
            balanceAlert.style.display = 'block';
        } else {
            balanceAlert.style.display = 'none';
        }
    }

    document.getElementById('journalEntryForm').addEventListener('submit', function(e) {
        const debits = Array.from(document.querySelectorAll('.debit-amount')).map(el => parseFloat(el.value) || 0);
        const credits = Array.from(document.querySelectorAll('.credit-amount')).map(el => parseFloat(el.value) || 0);
        
        const totalDebit = debits.reduce((a, b) => a + b, 0);
        const totalCredit = credits.reduce((a, b) => a + b, 0);
        
        if (Math.abs(totalDebit - totalCredit) > 0.01) {
            e.preventDefault();
            alert('Journal entry must be balanced! Debit and Credit totals must be equal.');
        }
    });
</script>
@endpush
@endsection
