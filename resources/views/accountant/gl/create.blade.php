@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="card-title mb-0">Create Chart of Account</span>
                <a href="{{ route('accountant.gl.index') }}" class="btn btn-secondary btn-sm">
                    <i class="feather-arrow-left"></i> Back
                </a>
        </div>
        <div class="card-body">
            <form action="{{ route('accountant.gl.store') }}" method="POST">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label" for="account_code">Account Code *</label>
                        <input
                            type="text"
                            id="account_code"
                            name="account_code"
                            class="form-control @error('account_code') is-invalid @enderror"
                            placeholder="1000"
                            value="{{ old('account_code') }}"
                            required
                        >
                        @error('account_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="account_name">Account Name *</label>
                        <input
                            type="text"
                            id="account_name"
                            name="account_name"
                            class="form-control @error('account_name') is-invalid @enderror"
                            placeholder="Cash on Hand"
                            value="{{ old('account_name') }}"
                            required
                        >
                        @error('account_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label" for="account_type">Account Type *</label>
                        <select id="account_type" name="account_type" class="form-select @error('account_type') is-invalid @enderror" required>
                            <option value="">Select Type</option>
                            <option value="Asset" {{ old('account_type') === 'Asset' ? 'selected' : '' }}>Asset</option>
                            <option value="Liability" {{ old('account_type') === 'Liability' ? 'selected' : '' }}>Liability</option>
                            <option value="Equity" {{ old('account_type') === 'Equity' ? 'selected' : '' }}>Equity</option>
                            <option value="Revenue" {{ old('account_type') === 'Revenue' ? 'selected' : '' }}>Revenue</option>
                            <option value="Expense" {{ old('account_type') === 'Expense' ? 'selected' : '' }}>Expense</option>
                        </select>
                        @error('account_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="account_category">Category *</label>
                        <input
                            type="text"
                            id="account_category"
                            name="account_category"
                            class="form-control @error('account_category') is-invalid @enderror"
                            placeholder="Cash"
                            value="{{ old('account_category') }}"
                            required
                        >
                        @error('account_category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label" for="opening_balance">Opening Balance</label>
                        <input
                            type="number"
                            id="opening_balance"
                            name="opening_balance"
                            class="form-control @error('opening_balance') is-invalid @enderror"
                            placeholder="0.00"
                            step="0.01"
                            value="{{ old('opening_balance', 0) }}"
                        >
                        @error('opening_balance')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="parent_account_id">Parent Account (Optional)</label>
                        <select id="parent_account_id" name="parent_account_id" class="form-select">
                            <option value="">None</option>
                            @foreach($parentAccounts as $acc)
                                <option value="{{ $acc->id }}" {{ old('parent_account_id') === (string)$acc->id ? 'selected' : '' }}>
                                    {{ $acc->account_code }} - {{ $acc->account_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="is_active"
                            name="is_active"
                            value="1"
                            @if(old('is_active', true)) checked @endif
                        >
                        <label class="form-check-label" for="is_active">
                            Active
                        </label>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                    <div></div>
                    <div class="d-flex gap-2 ms-auto">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="feather-check me-1"></i> Save Account
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
