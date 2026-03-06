@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="card-title mb-0">Daily Remittances</span>
            <a href="{{ route('remittances.create') }}" class="btn btn-primary btn-sm">
                <i class="feather-plus me-1"></i> Add Remittance
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover w-100 mb-0">
                    <thead>
                        <tr>
                            @php
                                $headers = [
                                    'remittance_date' => 'Date',
                                    'net_remittance' => 'Net Remittance',
                                    'status' => 'Status'
                                ];
                            @endphp
                            
                            @foreach($headers as $column => $label)
                                <th class="sortable-header @if($column === 'net_remittance') text-end @elseif($column === 'status') text-center @endif" data-column="{{ $column }}">
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
                            <th class="sortable-header text-center"><div class="sort-link justify-content-center">Actions</div></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($remittances as $remittance)
                            <tr>
                                <td class="text-muted">{{ $remittance->remittance_date?->format('M d, Y') }}</td>
                                <td class="text-end">₱{{ number_format($remittance->net_remittance, 2) }}</td>
                                <td class="text-center">
                                    @if ($remittance->status === 'approved')
                                        <span class="emp-badge emp-badge-approved">Approved</span>
                                    @elseif ($remittance->status === 'pending')
                                        <span class="emp-badge emp-badge-pending">Pending</span>
                                    @else
                                        <span class="emp-badge emp-badge-inactive">Rejected</span>
                                    @endif
                                </td>
                                <td><strong>{{ $remittance->route->route_name }}</strong></td>
                                <td>{{ $remittance->vehicle->plate_number }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
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
                                    No remittances found
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