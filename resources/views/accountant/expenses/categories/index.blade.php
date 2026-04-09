@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Expense Categories</h5>
            <a href="{{ route('accountant.expenses.categories.create') }}" class="btn btn-primary btn-sm">
                <i class="feather-plus"></i> Add Category
            </a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="feather-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Category Name</th>
                            <th>GL Account</th>
                            <th class="text-end">Monthly Budget</th>
                            <th>Approval</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td><span class="badge bg-light text-dark">{{ $category->category_code }}</span></td>
                                <td>{{ $category->category_name }}</td>
                                <td>{{ $category->glAccount->code }} - {{ $category->glAccount->name }}</td>
                                <td class="text-end">₱{{ number_format($category->monthly_budget, 2) }}</td>
                                <td>
                                    @if($category->requires_approval)
                                        <span class="badge bg-warning text-dark">Required</span>
                                    @else
                                        <span class="badge bg-secondary">Not Required</span>
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
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('accountant.expenses.categories.show', $category) }}" class="btn btn-sm btn-info" title="View">
                                            <i class="feather-eye"></i>
                                        </a>
                                        <a href="{{ route('accountant.expenses.categories.edit', $category) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="feather-edit"></i>
                                        </a>
                                        <form action="{{ route('accountant.expenses.categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this category?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="feather-trash-2"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="feather-inbox d-block mb-2" style="font-size:2rem; opacity:.3;"></i>
                                    No expense categories found. <a href="{{ route('accountant.expenses.categories.create') }}">Create one now</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($categories->hasPages())
                <div class="d-flex justify-content-end mt-3">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
