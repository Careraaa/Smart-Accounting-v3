@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Edit Account</h5>
                <div>
                    <a href="{{ route('accountant.gl.show', $account) }}" class="btn btn-info btn-sm">
                        <i class="feather-eye"></i> View
                    </a>
                    <a href="{{ route('accountant.gl.index') }}" class="btn btn-secondary btn-sm">
                        <i class="feather-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('accountant.gl.update', $account) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label" for="account_code">Account Code (Read-only)</label>
                        <input
                            type="text"
                            id="account_code"
                            class="form-control"
                            value="{{ $account->account_code }}"
                            disabled
                        >
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="account_name">Account Name *</label>
                        <input
                            type="text"
                            id="account_name"
                            name="account_name"
                            class="form-control @error('account_name') is-invalid @enderror"
                            value="{{ old('account_name', $account->account_name) }}"
                            required
                        >
                        @error('account_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label" for="account_type">Account Type (Read-only)</label>
                        <input
                            type="text"
                            id="account_type"
                            class="form-control"
                            value="{{ $account->account_type }}"
                            disabled
                        >
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="account_category">Category *</label>
                        <input
                            type="text"
                            id="account_category"
                            name="account_category"
                            class="form-control @error('account_category') is-invalid @enderror"
                            value="{{ old('account_category', $account->account_category) }}"
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
                            step="0.01"
                            value="{{ old('opening_balance', $account->opening_balance) }}"
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
                                <option value="{{ $acc->id }}" {{ old('parent_account_id', $account->parent_account_id) === (string)$acc->id ? 'selected' : '' }}>
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
                            @if(old('is_active', $account->is_active)) checked @endif
                        >
                        <label class="form-check-label" for="is_active">
                            Active
                        </label>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                    <div></div>
                    <div class="d-flex gap-2 ms-auto">
                        <a href="{{ route('accountant.gl.index') }}" class="btn btn-secondary btn-sm">
                            <i class="feather-x me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="feather-check me-1"></i> Update Account
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
