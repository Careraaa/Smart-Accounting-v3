@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="card-title mb-0">Drivers</span>
            <a href="{{ route('drivers.create') }}" class="btn btn-primary btn-sm">
                <i class="feather-plus me-1"></i> Add Driver
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover w-100 mb-0">
                    <thead>
                        <tr>
                            @php
                                $headers = [
                                    'name' => 'Name',
                                    'contact_number' => 'Contact',
                                    'status' => 'Status'
                                ];
                            @endphp
                            
                            @foreach($headers as $column => $label)
                                <th class="sortable-header @if($column === 'status') text-center @endif" data-column="{{ $column }}">
                                    <a href="{{ route('drivers.index', ['sort_by' => $column, 'sort_order' => ($sortBy === $column && $sortOrder === 'asc') ? 'desc' : 'asc']) }}" 
                                       class="sort-link">
                                        {{ $label }}
                                        @if($sortBy === $column)
                                            <i class="feather-arrow-{{ $sortOrder === 'asc' ? 'up' : 'down' }} ms-1" style="font-size: 0.875rem;"></i>
                                        @else
                                            <i class="feather-arrow-up-down ms-1" style="font-size: 0.875rem; opacity: 0.3;"></i>
                                        @endif
                                    </a>
                                </th>
                            @endforeach
                            
                            <th class="sortable-header text-center"><div class="sort-link justify-content-center">Actions</div></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($drivers as $driver)
                            <tr>
                                <td><strong>{{ $driver->name }}</strong></td>
                                <td class="text-muted">{{ $driver->contact_number }}</td>
                                <td class="text-center">
                                    @if ($driver->status === 'active')
                                        <span class="emp-badge emp-badge-active">Active</span>
                                    @elseif ($driver->status === 'pending')
                                        <span class="emp-badge emp-badge-pending">Pending</span>
                                    @else
                                        <span class="emp-badge emp-badge-inactive">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('drivers.show', $driver) }}"
                                            class="emp-action-btn emp-action-view" title="View">
                                            <i class="feather-eye"></i>
                                        </a>
                                        <a href="{{ route('drivers.edit', $driver) }}"
                                            class="emp-action-btn emp-action-edit" title="Edit">
                                            <i class="feather-edit-2"></i>
                                        </a>
                                        <form action="{{ route('drivers.destroy', $driver) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="emp-action-btn emp-action-danger" title="Delete"
                                                onclick="return confirm('Are you sure you want to delete this driver?')">
                                                <i class="feather-trash-2"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">
                                    <i class="feather-user d-block mb-2" style="font-size:28px; opacity:.3;"></i>
                                    No drivers found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
/* Sortable header styles */
.sortable-header {
    padding: 0 !important;
}

.sort-link {
    display: flex;
    align-items: center;
    padding: 12px;
    text-decoration: none;
    color: inherit;
    cursor: pointer;
    user-select: none;
    transition: background-color 0.2s;
    justify-content: flex-start;
}

.sortable-header.text-center .sort-link {
    justify-content: center;
}

.sortable-header.text-end .sort-link {
    justify-content: flex-end;
}

.sort-link:hover {
    background-color: #f5f5f5;
    color: #0066cc;
}

.sort-link i {
    display: inline-flex;
}

/* Badge styles */
.emp-badge {
    display: inline-block;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    padding: 3px 10px;
    border-radius: 20px;
}
.emp-badge-active   { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
.emp-badge-inactive { background: #fff5f5; color: #c8292a; border: 1px solid #fcd0d0; }
.emp-badge-pending  { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }

/* Action buttons */
.emp-action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    border-radius: 6px;
    background: #f4f5f7;
    border: none;
    color: #9898a8;
    font-size: 13px;
    cursor: pointer;
    text-decoration: none;
    transition: background 0.13s, color 0.13s;
    padding: 0;
}
.emp-action-btn.emp-action-view:hover   { background: #eff6ff; color: #3b82f6; }
.emp-action-btn.emp-action-edit:hover   { background: #fffbeb; color: #d97706; }
.emp-action-btn.emp-action-danger:hover { background: #fff1f2; color: #e11d48; }
</style>
@endsection