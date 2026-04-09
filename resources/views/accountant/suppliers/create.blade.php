@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Create Supplier</h5>
                <a href="{{ route('accountant.suppliers.index') }}" class="btn btn-secondary btn-sm">
                    <i class="feather-arrow-left"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('accountant.suppliers.store') }}" method="POST">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label" for="name">Supplier Name *</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}"
                            required
                        >
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="contact_person">Contact Person *</label>
                        <input
                            type="text"
                            id="contact_person"
                            name="contact_person"
                            class="form-control @error('contact_person') is-invalid @enderror"
                            value="{{ old('contact_person') }}"
                            required
                        >
                        @error('contact_person')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label" for="email">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}"
                        >
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="phone">Phone</label>
                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            class="form-control"
                            value="{{ old('phone') }}"
                        >
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="tax_id">Tax ID</label>
                        <input
                            type="text"
                            id="tax_id"
                            name="tax_id"
                            class="form-control"
                            value="{{ old('tax_id') }}"
                        >
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="payment_terms">Payment Terms</label>
                        <input
                            type="text"
                            id="payment_terms"
                            name="payment_terms"
                            class="form-control"
                            placeholder="e.g., Net 30"
                            value="{{ old('payment_terms') }}"
                        >
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="address">Address</label>
                    <textarea
                        id="address"
                        name="address"
                        class="form-control"
                        rows="2"
                    >{{ old('address') }}</textarea>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label" for="bank_name">Bank Name</label>
                        <input
                            type="text"
                            id="bank_name"
                            name="bank_name"
                            class="form-control"
                            value="{{ old('bank_name') }}"
                        >
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="bank_account">Bank Account Number</label>
                        <input
                            type="text"
                            id="bank_account"
                            name="bank_account"
                            class="form-control"
                            value="{{ old('bank_account') }}"
                        >
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

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="feather-save me-2"></i>Create Supplier
                    </button>
                    <a href="{{ route('accountant.suppliers.index') }}" class="btn btn-secondary">
                        <i class="feather-x me-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
