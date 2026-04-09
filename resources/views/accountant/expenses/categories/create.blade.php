@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Create Expense Category</h5>
                <a href="{{ route('accountant.expenses.categories.index') }}" class="btn btn-secondary btn-sm">
                    <i class="feather-arrow-left"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('accountant.expenses.categories.store') }}" method="POST">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label" for="category_code">Category Code *</label>
                        <input
                            type="text"
                            id="category_code"
                            name="category_code"
                            class="form-control @error('category_code') is-invalid @enderror"
                            value="{{ old('category_code') }}"
                            required
                        >
                        @error('category_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="category_name">Category Name *</label>
                        <input
                            type="text"
                            id="category_name"
                            name="category_name"
                            class="form-control @error('category_name') is-invalid @enderror"
                            value="{{ old('category_name') }}"
                            required
                        >
                        @error('category_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label" for="gl_account_id">GL Account *</label>
                        <select id="gl_account_id" name="gl_account_id" class="form-select @error('gl_account_id') is-invalid @enderror" required>
                            <option value="">Select GL Account</option>
                            @foreach(\App\Models\GLAccount::where('account_type', 'Expense')->where('is_active', true)->get() as $account)
                                <option value="{{ $account->id }}" {{ old('gl_account_id') === (string)$account->id ? 'selected' : '' }}>
                                    {{ $account->account_code }} - {{ $account->account_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('gl_account_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="monthly_budget">Monthly Budget (₱)</label>
                        <input
                            type="number"
                            id="monthly_budget"
                            name="monthly_budget"
                            class="form-control"
                            placeholder="0.00"
                            step="0.01"
                            value="{{ old('monthly_budget', 0) }}"
                        >
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="description">Description</label>
                    <textarea
                        id="description"
                        name="description"
                        class="form-control"
                        rows="3"
                        placeholder="Category description"
                    >{{ old('description') }}</textarea>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="requires_approval" name="requires_approval" value="1" {{ old('requires_approval') ? 'checked' : '' }}>
                        <label class="form-check-label" for="requires_approval">
                            Requires Approval
                        </label>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="feather-save"></i> Create Category
                    </button>
                    <a href="{{ route('accountant.expenses.categories.index') }}" class="btn btn-secondary">
                        <i class="feather-x"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
