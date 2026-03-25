@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="card-title mb-0">PAO / Conductors</span>
            <a href="{{ route('paos.create') }}" class="btn btn-primary btn-sm">
                <i class="feather-plus me-1"></i> Add PAO
            </a>
        </div>
        <div class="card-body">
            {{-- Statistics Cards --}}
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card card-statistic">
                        <div class="card-body">
                            <div class="stat-label">Total PAOs</div>
                            <h3 class="mb-1" style="color: #0369a1;">{{ $totalPAOs }}</h3>
                            <small class="text-muted">All PAOs</small>
                        </div>
                        <div class="card-icon" style="color: #0ea5e9; opacity: 0.2;">
                            <i class="feather-users" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-statistic">
                        <div class="card-body">
                            <div class="stat-label">Active PAOs</div>
                            <h3 class="mb-1" style="color: #16a34a;">{{ $activePAOs }}</h3>
                            <small class="text-muted">Currently active</small>
                        </div>
                        <div class="card-icon" style="color: #22c55e; opacity: 0.2;">
                            <i class="feather-check-circle" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-statistic">
                        <div class="card-body">
                            <div class="stat-label">Inactive PAOs</div>
                            <h3 class="mb-1" style="color: #dc2626;">{{ $inactivePAOs }}</h3>
                            <small class="text-muted">Currently inactive</small>
                        </div>
                        <div class="card-icon" style="color: #ef4444; opacity: 0.2;">
                            <i class="feather-x-circle" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>

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
                                    <a href="{{ route('paos.index', ['sort_by' => $column, 'sort_order' => ($sortBy === $column && $sortOrder === 'asc') ? 'desc' : 'asc']) }}" 
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
                        @forelse($paos as $pao)
                            <tr>
                                <td><strong>{{ $pao->name }}</strong></td>
                                <td class="text-muted">{{ $pao->contact_number }}</td>
                                <td class="text-center">
                                    @if ($pao->status === 'active')
                                        <span class="emp-badge emp-badge-active">Active</span>
                                    @elseif ($pao->status === 'pending')
                                        <span class="emp-badge emp-badge-pending">Pending</span>
                                    @else
                                        <span class="emp-badge emp-badge-inactive">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('paos.show', $pao) }}"
                                            class="emp-action-btn emp-action-view" title="View">
                                            <i class="feather-eye"></i>
                                        </a>
                                        <a href="{{ route('paos.edit', $pao) }}"
                                            class="emp-action-btn emp-action-edit" title="Edit">
                                            <i class="feather-edit-2"></i>
                                        </a>
                                        <form action="{{ route('paos.destroy', $pao) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="emp-action-btn emp-action-danger" title="Delete"
                                                onclick="return confirm('Are you sure you want to delete this PAO?')">
                                                <i class="feather-trash-2"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">
                                    <i class="feather-users d-block mb-2" style="font-size:28px; opacity:.3;"></i>
                                    No PAOs found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection