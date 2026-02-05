@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="row mb-4">
        <div class="col-xxl-2 col-lg-4 col-md-6">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="fs-12 fw-medium text-muted mb-3">Total Remittances</div>
                    <div class="hstack justify-content-between lh-base">
                        <h3>{{ $totalRemittances }}</h3>
                        <div class="hstack gap-2 fs-11 text-success">
                            <i class="feather-arrow-up-circle fs-12"></i>
                            <span>+12.5%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-2 col-lg-4 col-md-6">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="fs-12 fw-medium text-muted mb-3">Total Collections</div>
                    <div class="hstack justify-content-between lh-base">
                        <h3>₱{{ number_format($totalCollections, 0) }}</h3>
                        <div class="hstack gap-2 fs-11 text-success">
                            <i class="feather-arrow-up-circle fs-12"></i>
                            <span>{{ $collectionGrowth > 0 ? '+' : '' }}{{ number_format($collectionGrowth, 1) }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-2 col-lg-4 col-md-6">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="fs-12 fw-medium text-muted mb-3">Total Expenses</div>
                    <div class="hstack justify-content-between lh-base">
                        <h3>₱{{ number_format($totalExpenses, 0) }}</h3>
                        <div class="hstack gap-2 fs-11 text-danger">
                            <i class="feather-arrow-down-circle fs-12"></i>
                            <span>+5.2%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-2 col-lg-4 col-md-6">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="fs-12 fw-medium text-muted mb-3">Net Remittance</div>
                    <div class="hstack justify-content-between lh-base">
                        <h3>₱{{ number_format($totalNetRemittance, 0) }}</h3>
                        <div class="hstack gap-2 fs-11 text-success">
                            <i class="feather-arrow-up-circle fs-12"></i>
                            <span>+18.3%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-2 col-lg-4 col-md-6">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="fs-12 fw-medium text-muted mb-3">Avg. Collection</div>
                    <div class="hstack justify-content-between lh-base">
                        <h3>₱{{ number_format($averageCollection, 0) }}</h3>
                        <div class="hstack gap-2 fs-11 text-success">
                            <i class="feather-arrow-up-circle fs-12"></i>
                            <span>+8.7%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-2 col-lg-4 col-md-6">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="fs-12 fw-medium text-muted mb-3">Avg. Expenses</div>
                    <div class="hstack justify-content-between lh-base">
                        <h3>₱{{ number_format($averageExpenses, 0) }}</h3>
                        <div class="hstack gap-2 fs-11 text-danger">
                            <i class="feather-arrow-down-circle fs-12"></i>
                            <span>-3.1%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Cards Row 2 -->
<div class="col-md-12">
    <div class="row mb-4">
        <div class="col-xxl-3 col-md-6">
            <div class="card card-body">
                <div class="fs-12 text-muted mb-3">Pending Remittances</div>
                <div class="hstack justify-content-between mt-4">
                    <div class="text-dark fw-bold fs-5">{{ $pendingRemittances }}</div>
                    <span class="badge bg-soft-warning text-warning">
                        <i class="feather-clock fs-10"></i>
                    </span>
                </div>
                <div class="progress mt-3 ht-3">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $totalRemittances > 0 ? ($pendingRemittances/$totalRemittances)*100 : 0 }}%"></div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-md-6">
            <div class="card card-body">
                <div class="fs-12 text-muted mb-3">Completed Remittances</div>
                <div class="hstack justify-content-between mt-4">
                    <div class="text-dark fw-bold fs-5">{{ $completedRemittances }}</div>
                    <span class="badge bg-soft-success text-success">
                        <i class="feather-check-circle fs-10"></i>
                    </span>
                </div>
                <div class="progress mt-3 ht-3">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $totalRemittances > 0 ? ($completedRemittances/$totalRemittances)*100 : 0 }}%"></div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-md-6">
            <div class="card card-body">
                <div class="fs-12 text-muted mb-3">Active Drivers</div>
                <div class="hstack justify-content-between mt-4">
                    <div class="text-dark fw-bold fs-5">{{ $activeDrivers }}</div>
                    <span class="badge bg-soft-primary text-primary">
                        <i class="feather-users fs-10"></i>
                    </span>
                </div>
                <div class="mt-3">
                    <a href="{{ route('drivers.index') }}" class="fs-11 text-primary fw-semibold">View Drivers →</a>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-md-6">
            <div class="card card-body">
                <div class="fs-12 text-muted mb-3">Active Resources</div>
                <div class="hstack justify-content-between mt-4">
                    <div>
                        <div class="text-dark fw-bold fs-5">{{ $activeVehicles }}</div>
                        <span class="fs-11 text-muted">Vehicles</span>
                    </div>
                    <div class="text-end">
                        <div class="text-dark fw-bold fs-5">{{ $activePAOs }}</div>
                        <span class="fs-11 text-muted">PAOs</span>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('management.index') }}" class="fs-11 text-primary fw-semibold">Manage →</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Remittances Table -->
<div class="col-md-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Recent Remittances</h5>
            <a href="{{ route('remittances.index') }}" class="btn btn-primary btn-sm">View All</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Driver</th>
                            <th>Vehicle</th>
                            <th>Route</th>
                            <th>Collection</th>
                            <th>Expenses</th>
                            <th>Net</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentRemittances as $remittance)
                            <tr>
                                <td>{{ $remittance->remittance_date?->format('M d, Y') ?? 'N/A' }}</td>
                                <td><strong>{{ $remittance->driver->name ?? 'N/A' }}</strong></td>
                                <td>{{ $remittance->vehicle->plate_number ?? 'N/A' }}</td>
                                <td>{{ $remittance->route->route_name ?? 'N/A' }}</td>
                                <td>₱{{ number_format($remittance->total_collection, 2) }}</td>
                                <td>₱{{ number_format($remittance->total_expenses, 2) }}</td>
                                <td><strong>₱{{ number_format($remittance->net_remittance, 2) }}</strong></td>
                                <td>
                                    <span class="badge bg-{{ $remittance->status === 'completed' ? 'success' : 'warning' }}">
                                        {{ ucfirst($remittance->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('remittances.show', $remittance) }}" class="btn btn-info btn-sm" title="View">
                                        <i class="feather-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">No remittance records found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
