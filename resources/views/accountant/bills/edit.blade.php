@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Edit Bill {{ $bill->bill_number }}</h5>
                <a href="{{ route('accountant.bills.index') }}" class="btn btn-secondary btn-sm">
                    <i class="feather-arrow-left"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('accountant.bills.update', $bill) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label" for="supplier_id">Supplier *</label>
                        <select id="supplier_id" name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
                            <option value="">Select Supplier</option>
                            @foreach(\App\Models\Supplier::where('is_active', true)->get() as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id', $bill->supplier_id) === (string)$supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="bill_number">Bill Number *</label>
                        <input
                            type="text"
                            id="bill_number"
                            name="bill_number"
                            class="form-control @error('bill_number') is-invalid @enderror"
                            value="{{ old('bill_number', $bill->bill_number) }}"
                            required
                        >
                        @error('bill_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label" for="bill_date">Bill Date *</label>
                        <input
                            type="date"
                            id="bill_date"
                            name="bill_date"
                            class="form-control @error('bill_date') is-invalid @enderror"
                            value="{{ old('bill_date', $bill->bill_date->format('Y-m-d')) }}"
                            required
                        >
                        @error('bill_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="due_date">Due Date *</label>
                        <input
                            type="date"
                            id="due_date"
                            name="due_date"
                            class="form-control @error('due_date') is-invalid @enderror"
                            value="{{ old('due_date', $bill->due_date->format('Y-m-d')) }}"
                            required
                        >
                        @error('due_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="amount">Amount (₱) *</label>
                        <input
                            type="number"
                            id="amount"
                            name="amount"
                            class="form-control @error('amount') is-invalid @enderror"
                            placeholder="0.00"
                            step="0.01"
                            value="{{ old('amount', $bill->amount) }}"
                            required
                        >
                        @error('amount')
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
                        required
                    >{{ old('description', $bill->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="feather-save"></i> Update Bill
                    </button>
                    <a href="{{ route('accountant.bills.index') }}" class="btn btn-secondary">
                        <i class="feather-x"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
