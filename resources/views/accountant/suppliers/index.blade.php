@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Suppliers</h5>
                <a href="{{ route('accountant.suppliers.create') }}" class="btn btn-primary btn-sm">
                    <i class="feather-plus"></i> New Supplier
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <input type="text" id="search" class="form-control" placeholder="Search suppliers...">
                </div>
                <div class="col-md-3">
                    <select id="statusFilter" class="form-select">
                        <option value="">All Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <div class="col-md-3"></div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100" onclick="filterSuppliers()">
                        <i class="feather-filter"></i> Filter
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Supplier Name</th>
                            <th>Contact Person</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th class="text-end">Outstanding Balance</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $supplier)
                            <tr>
                                <td><strong>{{ $supplier->name }}</strong></td>
                                <td>{{ $supplier->contact_person }}</td>
                                <td>{{ $supplier->email }}</td>
                                <td>{{ $supplier->phone }}</td>
                                <td class="text-end">₱{{ number_format($supplier->getOutstandingBalance(), 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $supplier->is_active ? 'success' : 'danger' }}">
                                        {{ $supplier->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('accountant.suppliers.show', $supplier) }}" class="btn btn-sm btn-info" title="View">
                                            <i class="feather-eye"></i>
                                        </a>
                                        <a href="{{ route('accountant.suppliers.edit', $supplier) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="feather-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    No suppliers found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-end">
                {{ $suppliers->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function filterSuppliers() {
        // Implement filtering logic
    }
</script>
@endpush
@endsection
