@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">{{ $category->category_name }}</h5>
                <div>
                    <a href="{{ route('accountant.expenses.categories.edit', $category) }}" class="btn btn-warning btn-sm">
                        <i class="feather-edit"></i> Edit
                    </a>
                    <a href="{{ route('accountant.expenses.categories.index') }}" class="btn btn-secondary btn-sm">
                        <i class="feather-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-3">
                    <p class="text-muted mb-1">Category Code</p>
                    <h5>{{ $category->category_code }}</h5>
                </div>
                <div class="col-md-3">
                    <p class="text-muted mb-1">GL Account</p>
                    <h5>{{ $category->glAccount->account_code }}</h5>
                </div>
                <div class="col-md-3">
                    <p class="text-muted mb-1">Monthly Budget</p>
                    <h5>₱{{ number_format($category->monthly_budget, 2) }}</h5>
                </div>
                <div class="col-md-3">
                    <p class="text-muted mb-1">Status</p>
                    <h5>
                        <span class="badge bg-{{ $category->is_active ? 'success' : 'danger' }}">
                            {{ $category->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </h5>
                </div>
            </div>

            <hr>

            <h6 class="mb-3">Details</h6>
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-2">
                        <strong>GL Account:</strong> {{ $category->glAccount->account_name }}
                    </p>
                    <p class="mb-2">
                        <strong>Requires Approval:</strong> {{ $category->requires_approval ? 'Yes' : 'No' }}
                    </p>
                </div>
                <div class="col-md-6">
                    @if($category->description)
                        <p class="mb-2">
                            <strong>Description:</strong>
                        </p>
                        <p>{{ $category->description }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
