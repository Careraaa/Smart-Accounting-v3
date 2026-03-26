@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="card-title mb-0">Short Remittances</span>
        </div>
        <div class="card-body">
            {{-- Statistics Cards --}}
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card card-statistic">
                        <div class="card-body">
                            <div class="stat-label">Total Short Remittances</div>
                            <h3 class="mb-1" style="color: #dc2626;">{{ $totalShortRemittances }}</h3>
                            <small class="text-muted">Pending resolution</small>
                        </div>
                        <div class="card-icon" style="color: #ef4444; opacity: 0.2;">
                            <i class="feather-alert-circle" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-statistic">
                        <div class="card-body">
                            <div class="stat-label">Total Short Amount</div>
                            <h3 class="mb-1" style="color: #ea580c;">₱{{ number_format($totalShortAmount, 2) }}</h3>
                            <small class="text-muted">Total shortage</small>
                        </div>
                        <div class="card-icon" style="color: #f97316; opacity: 0.2;">
                            <i class="feather-trending-down" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-statistic">
                        <div class="card-body">
                            <div class="stat-label">Driver Shares</div>
                            <h3 class="mb-1" style="color: #0369a1;">₱{{ number_format($totaldriverShares, 2) }}</h3>
                            <small class="text-muted">Total driver liability</small>
                        </div>
                        <div class="card-icon" style="color: #0ea5e9; opacity: 0.2;">
                            <i class="feather-user" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-statistic">
                        <div class="card-body">
                            <div class="stat-label">PAO Shares</div>
                            <h3 class="mb-1" style="color: #16a34a;">₱{{ number_format($totalPaoShares, 2) }}</h3>
                            <small class="text-muted">Total PAO liability</small>
                        </div>
                        <div class="card-icon" style="color: #22c55e; opacity: 0.2;">
                            <i class="feather-users" style="font-size: 2.5rem;"></i>
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
                                    'remittance_date' => 'Date',
                                    'short_amount' => 'Short Amount',
                                ];
                            @endphp
                            
                            @foreach($headers as $column => $label)
                                <th class="sortable-header @if($column === 'short_amount') text-end @endif" data-column="{{ $column }}">
                                    <a href="{{ route('short-remittances.index', ['sort_by' => $column, 'sort_order' => ($sortBy === $column && $sortOrder === 'asc') ? 'desc' : 'asc']) }}" 
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
                            
                            <th class="sortable-header"><div class="sort-link">Driver</div></th>
                            <th class="sortable-header"><div class="sort-link">PAO</div></th>
                            <th class="sortable-header text-end"><div class="sort-link justify-content-end">Driver Share</div></th>
                            <th class="sortable-header text-end"><div class="sort-link justify-content-end">PAO Share</div></th>
                            <th class="sortable-header"><div class="sort-link">Vehicle</div></th>
                            <th class="sortable-header text-center"><div class="sort-link justify-content-center">Actions</div></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shortRemittances as $shortRemittance)
                            <tr>
                                <td class="text-muted">{{ $shortRemittance->remittance_date?->format('M d, Y') }}</td>
                                <td class="text-end"><strong style="color: #dc2626;">₱{{ number_format($shortRemittance->short_amount, 2) }}</strong></td>
                                <td><strong>{{ $shortRemittance->driver->name ?? 'N/A' }}</strong></td>
                                <td><strong>{{ $shortRemittance->pao->name ?? 'N/A' }}</strong></td>
                                <td class="text-end">₱{{ number_format($shortRemittance->driver_share, 2) }}</td>
                                <td class="text-end">₱{{ number_format($shortRemittance->pao_share, 2) }}</td>
                                <td>{{ $shortRemittance->vehicle->plate_number }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('short-remittances.show', $shortRemittance) }}"
                                            class="emp-action-btn emp-action-view" title="View">
                                            <i class="feather-eye"></i>
                                        </a>
                                        <a href="{{ route('short-remittances.edit', $shortRemittance) }}"
                                            class="emp-action-btn emp-action-edit" title="Mark Resolution">
                                            <i class="feather-edit-2"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">
                                    <i class="feather-check-circle d-block mb-2" style="font-size:28px; opacity:.3;"></i>
                                    No short remittances found
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
