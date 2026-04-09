@extends('layouts.layout')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Expense Management Dashboard</h5>
                <div>
                    <a href="{{ route('accountant.expenses.categories.index') }}" class="btn btn-primary btn-sm me-2">
                        <i class="feather-list"></i> Manage Categories
                    </a>
                    <a href="{{ route('accountant.expenses.allocations.index') }}" class="btn btn-info btn-sm">
                        <i class="feather-pie-chart"></i> Allocations
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="feather-tag text-primary" style="font-size: 2rem;"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted mb-1">Total Categories</p>
                                        <h4 class="mb-0">{{ $totalExpenseCategories }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="feather-check-circle text-success" style="font-size: 2rem;"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted mb-1">Active Categories</p>
                                        <h4 class="mb-0">{{ $activeCategories }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="feather-pie-chart text-warning" style="font-size: 2rem;"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted mb-1">Total Allocations</p>
                                        <h4 class="mb-0">{{ $totalAllocations }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <h6 class="mb-3"><strong>Top Expense Categories</strong></h6>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Category Name</th>
                                        <th>GL Account</th>
                                        <th>Monthly Budget</th>
                                        <th>Requires Approval</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topCategories as $category)
                                        <tr>
                                            <td><span class="badge bg-light text-dark">{{ $category->category_code }}</span></td>
                                            <td>{{ $category->category_name }}</td>
                                            <td>{{ $category->glAccount->code }} - {{ $category->glAccount->name }}</td>
                                            <td class="text-end">₱{{ number_format($category->monthly_budget, 2) }}</td>
                                            <td>
                                                @if($category->requires_approval)
                                                    <span class="badge bg-warning">Yes</span>
                                                @else
                                                    <span class="badge bg-secondary">No</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($category->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('accountant.expenses.categories.show', $category) }}" class="btn btn-xs btn-info">
                                                    <i class="feather-eye"></i>
                                                </a>
                                                <a href="{{ route('accountant.expenses.categories.edit', $category) }}" class="btn btn-xs btn-warning">
                                                    <i class="feather-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">No expense categories found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
