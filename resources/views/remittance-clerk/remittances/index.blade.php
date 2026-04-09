@extends('layouts.layout')

@push('styles')
    @include('remittance-clerk._ui-styles')
@endpush

@section('content')
<div class="col-12">
    <div class="remui-page">
        <div class="remui-backdrop"></div>

        <div class="remui-hero mb-3">
            <div>
                <h5 class="remui-title">Remittances</h5>
                <p class="remui-subtitle mb-0">Pending and approved remittances for review and tracking.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('remittances.create') }}" class="emp-action-btn emp-action-edit">
                    <i class="feather-plus"></i><span>Add Remittance</span>
                </a>
            </div>
        </div>

    {{-- Pending Remittances Section --}}
    <div class="card remui-card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="card-title mb-0">Pending Remittances</span>
            <a href="{{ route('remittances.create') }}" class="emp-action-btn emp-action-edit">
                <i class="feather-plus"></i><span>Add</span>
            </a>
        </div>
        <div class="card-body">
            {{-- Statistics Cards --}}
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card card-statistic">
                        <div class="card-body">
                            <div class="stat-label">Total Remittances</div>
                            <h3 class="mb-1" style="color: #0369a1;">{{ $totalRemittances }}</h3>
                            <small class="text-muted">All remittances</small>
                        </div>
                        <div class="card-icon" style="color: #0ea5e9; opacity: 0.2;">
                            <i class="feather-file-text" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-statistic">
                        <div class="card-body">
                            <div class="stat-label">Approved</div>
                            <h3 class="mb-1" style="color: #16a34a;">{{ $approvedCount }}</h3>
                            <small class="text-muted">Approved remittances</small>
                        </div>
                        <div class="card-icon" style="color: #22c55e; opacity: 0.2;">
                            <i class="feather-check-circle" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-statistic">
                        <div class="card-body">
                            <div class="stat-label">Pending</div>
                            <h3 class="mb-1" style="color: #ea580c;">{{ $pendingCount }}</h3>
                            <small class="text-muted">Pending review</small>
                        </div>
                        <div class="card-icon" style="color: #f97316; opacity: 0.2;">
                            <i class="feather-clock" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-statistic">
                        <div class="card-body">
                            <div class="stat-label">Rejected</div>
                            <h3 class="mb-1" style="color: #dc2626;">{{ $rejectedRemittances }}</h3>
                            <small class="text-muted">Rejected remittances</small>
                        </div>
                        <div class="card-icon" style="color: #ef4444; opacity: 0.2;">
                            <i class="feather-x-circle" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover w-100 mb-0 remui-table">
                    <thead>
                        <tr>
                            @php
                                $headers = [
                                    'remittance_date' => 'Date',
                                ];
                            @endphp
                            
                            @foreach($headers as $column => $label)
                                <th class="sortable-header @if($column === 'net_remittance') text-end @elseif($column === 'status') @endif" data-column="{{ $column }}">
                                    <a href="{{ route('remittances.index', ['sort_by' => $column, 'sort_order' => ($sortBy === $column && $sortOrder === 'asc') ? 'desc' : 'asc']) }}" 
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
                            
                            <th class="sortable-header"><div class="sort-link">Route</div></th>
                            <th class="sortable-header"><div class="sort-link">Vehicle</div></th>
                            <th class="sortable-header"><div class="sort-link">Net Remittance</div></th>
                            <th class="sortable-header"><div class="sort-link">Status</div></th>
                            <th class="sortable-header"><div class="sort-link">Actions</div></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingRemittances as $remittance)
                            <tr>
                                <td class="text-muted">{{ $remittance->remittance_date?->format('M d, Y') }}</td>
                                <td><strong>{{ $remittance->route->route_name }}</strong></td>
                                <td>{{ $remittance->vehicle->plate_number }}</td>
                                <td>
                                    @if ($remittance->is_short_remittance)
                                        <strong style="color: #dc2626;">₱{{ number_format($remittance->net_remittance, 2) }}</strong>
                                    @else
                                        <strong style="color: #16a34a;">₱{{ number_format($remittance->net_remittance, 2) }}</strong>
                                    @endif
                                </td>
                                <td>
                                    <span class="emp-badge emp-badge-pending">Pending</span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('remittances.show', $remittance) }}"
                                            class="emp-action-btn emp-action-view" title="View">
                                            <i class="feather-eye"></i>
                                        </a>
                                        <a href="{{ route('remittances.edit', $remittance) }}"
                                            class="emp-action-btn emp-action-edit" title="Edit">
                                            <i class="feather-edit-2"></i>
                                        </a>
                                        <form action="{{ route('remittances.destroy', $remittance) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="emp-action-btn emp-action-danger" title="Delete"
                                                onclick="return confirm('Are you sure you want to delete this remittance?')">
                                                <i class="feather-trash-2"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <i class="feather-file-text d-block mb-2" style="font-size:28px; opacity:.3;"></i>
                                    No pending remittances found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Approved Remittances Section --}}
    <div class="card remui-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="card-title mb-0">Approved Remittances</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover w-100 mb-0 remui-table">
                    <thead>
                        <tr>
                            @php
                                $headers = [
                                    'remittance_date' => 'Date',
                                ];
                            @endphp
                            
                            @foreach($headers as $column => $label)
                                <th class="sortable-header @if($column === 'net_remittance') @endif" data-column="{{ $column }}">
                                    <a href="{{ route('remittances.index', ['sort_by' => $column, 'sort_order' => ($sortBy === $column && $sortOrder === 'asc') ? 'desc' : 'asc']) }}" 
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
                            
                            <th><div class="sort-link">Route</div></th>
                            <th class="sortable-header"><div class="sort-link">Vehicle</div></th>
                            <th class="sortable-header"><div class="sort-link">Net Remittance</div></th>
                            <th class="sortable-header"><div class="sort-link">Status</div></th>
                            <th class="sortable-header"><div class="sort-link">Actions</div></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($approvedRemittances as $remittance)
                            <tr>
                                <td class="text-muted">{{ $remittance->remittance_date?->format('M d, Y') }}</td>
                                <td><strong>{{ $remittance->route->route_name }}</strong></td>
                                <td>{{ $remittance->vehicle->plate_number }}</td>
                                <td>
                                    @if ($remittance->is_short_remittance)
                                        <strong style="color: #dc2626;">₱{{ number_format($remittance->net_remittance, 2) }}</strong>
                                    @else
                                        <strong style="color: #16a34a;">₱{{ number_format($remittance->net_remittance, 2) }}</strong>
                                    @endif
                                </td>
                                <td>
                                    <span class="emp-badge emp-badge-approved">Approved</span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('remittances.show', $remittance) }}"
                                            class="emp-action-btn emp-action-view" title="View">
                                            <i class="feather-eye"></i>
                                        </a>
                                        <a href="{{ route('remittances.edit', $remittance) }}"
                                            class="emp-action-btn emp-action-edit" title="Edit">
                                            <i class="feather-edit-2"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <i class="feather-file-text d-block mb-2" style="font-size:28px; opacity:.3;"></i>
                                    No approved remittances found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
@endsection