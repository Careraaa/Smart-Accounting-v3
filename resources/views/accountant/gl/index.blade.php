@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <span class="card-title mb-0">Chart of Accounts</span>
            </div>
            <a href="{{ route('accountant.gl.create') }}" class="btn btn-primary btn-sm">
                <i class="feather-plus me-1"></i> New Account
            </a>
        </div>
        
        <div class="card-body p-3">
            <div class="row mb-3 g-2">
                <div class="col-md-4">
                    <input type="text" id="search" class="form-control form-control-sm" placeholder="Search code or name...">
                </div>
                <div class="col-md-2">
                    <select id="typeFilter" class="form-select form-select-sm">
                        <option value="">All Types</option>
                        <option value="Asset">Asset</option>
                        <option value="Liability">Liability</option>
                        <option value="Equity">Equity</option>
                        <option value="Revenue">Revenue</option>
                        <option value="Expense">Expense</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="categoryFilter" class="form-select form-select-sm">
                        <option value="">All Categories</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="statusFilter" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover w-100 mb-0">
                    <thead>
                        <tr>
                            <th>
                                <div class="sort-link">Code</div>
                            </th>
                            <th>
                                <div class="sort-link">Account Name</div>
                            </th>
                            <th class="text-center">
                                <div class="sort-link justify-content-center">Type</div>
                            </th>
                            <th>
                                <div class="sort-link">Category</div>
                            </th>
                            <th class="text-end">
                                <div class="sort-link justify-content-end">Current Balance</div>
                            </th>
                            <th class="text-center">
                                <div class="sort-link justify-content-center">Status</div>
                            </th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="gl-tbody">
                        @forelse($accounts as $account)
                            <tr>
                                <td>
                                    <span class="emp-badge" style="background:#f3f4f6; color:#374151; border:1px solid #e5e7eb;">
                                        {{ $account->account_code }}
                                    </span>
                                </td>
                                <td>
                                    <strong>{{ $account->account_name }}</strong>
                                    @if($account->parent_account_id)
                                        <br><small class="text-muted">Sub-account</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @php
                                        $typeColors = [
                                            'Asset' => ['bg' => '#f0fdf4', 'color' => '#16a34a', 'border' => '#bbf7d0'],
                                            'Liability' => ['bg' => '#fef2f2', 'color' => '#dc2626', 'border' => '#fecaca'],
                                            'Equity' => ['bg' => '#f0f9ff', 'color' => '#0284c7', 'border' => '#bae6fd'],
                                            'Revenue' => ['bg' => '#faf5ff', 'color' => '#7c3aed', 'border' => '#ddd6fe'],
                                            'Expense' => ['bg' => '#fff7ed', 'color' => '#ea580c', 'border' => '#fed7aa'],
                                        ];
                                        $t = $typeColors[$account->account_type] ?? ['bg' => '#f4f5f7', 'color' => '#9898a8', 'border' => '#e8e8ef'];
                                    @endphp
                                    <span class="emp-badge" style="background:{{ $t['bg'] }}; color:{{ $t['color'] }}; border:1px solid {{ $t['border'] }};">
                                        {{ $account->account_type }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $account->account_category }}</small>
                                </td>
                                <td class="text-end">
                                    <strong>₱{{ number_format($account->current_balance, 2) }}</strong>
                                </td>
                                <td class="text-center">
                                    @if($account->is_active)
                                        <span class="emp-badge" style="background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0;">
                                            <i class="feather-check-circle me-1" style="font-size:0.7rem;"></i>Active
                                        </span>
                                    @else
                                        <span class="emp-badge" style="background:#fef2f2; color:#dc2626; border:1px solid #fecaca;">
                                            <i class="feather-x-circle me-1" style="font-size:0.7rem;"></i>Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('accountant.gl.show', $account) }}" 
                                           class="emp-action-btn emp-action-view" title="View">
                                            <i class="feather-eye"></i>
                                        </a>
                                        <a href="{{ route('accountant.gl.edit', $account) }}" 
                                           class="emp-action-btn emp-action-edit" title="Edit">
                                            <i class="feather-edit-2"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="feather-inbox d-block mb-2" style="font-size:28px; opacity:.3;"></i>
                                    No accounts found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($accounts->hasPages())
                <div class="d-flex justify-content-between align-items-center px-3 py-2 border-top">
                    <div class="small text-muted">
                        Showing
                        <strong>{{ $accounts->firstItem() }}</strong>
                        to
                        <strong>{{ $accounts->lastItem() }}</strong>
                        of
                        <strong>{{ $accounts->total() }}</strong>
                        entries
                    </div>
                    <div>
                        {{ $accounts->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .emp-badge {
        display: inline-block;
        padding: 0.35rem 0.65rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-weight: 500;
    }
    
    .sort-link {
        display: flex;
        align-items: center;
        cursor: pointer;
        user-select: none;
    }
    
    .sort-link:hover {
        color: #0d6efd;
    }
</style>
@endpush

@push('scripts')
<script>
    // Client-side filtering
    document.getElementById('search').addEventListener('keyup', filterTable);
    document.getElementById('typeFilter').addEventListener('change', filterTable);
    document.getElementById('categoryFilter').addEventListener('change', filterTable);
    document.getElementById('statusFilter').addEventListener('change', filterTable);

    function filterTable() {
        const search = document.getElementById('search').value.toLowerCase();
        const typeFilter = document.getElementById('typeFilter').value;
        const categoryFilter = document.getElementById('categoryFilter').value;
        const statusFilter = document.getElementById('statusFilter').value;

        document.querySelectorAll('#gl-tbody tr').forEach(row => {
            const code = row.cells[0].textContent.toLowerCase();
            const name = row.cells[1].textContent.toLowerCase();
            const type = row.cells[2].textContent.trim();
            const category = row.cells[3].textContent.trim();
            const status = row.cells[5].textContent.includes('Active') ? '1' : '0';

            const matchesSearch = code.includes(search) || name.includes(search);
            const matchesType = !typeFilter || type.includes(typeFilter);
            const matchesCategory = !categoryFilter || category.includes(categoryFilter);
            const matchesStatus = !statusFilter || status === statusFilter;

            row.style.display = matchesSearch && matchesType && matchesCategory && matchesStatus ? '' : 'none';
        });
    }
</script>
@endpush

@endsection
