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
                <h5 class="remui-title">Remittance Dashboard</h5>
                <p class="remui-subtitle mb-0">Quick snapshot of collections, expenses, and recent remittances.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('remittances.create') }}" class="emp-action-btn emp-action-edit">
                    <i class="feather-plus"></i><span>New Remittance</span>
                </a>
                <a href="{{ route('remittances.index') }}" class="emp-action-btn emp-action-view">
                    <i class="feather-layers"></i><span>View All</span>
                </a>
            </div>
        </div>

    {{-- Top stat cards --}}
    <div class="row g-3 mb-4">

        <div class="col-xxl-2 col-lg-4 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="dash-label">Total Remittances</span>
                        <span class="dash-icon"><i class="feather-layers"></i></span>
                    </div>
                    <div class="dash-value">{{ $totalRemittances }}</div>
                    <div class="dash-sub">+12.5% this period</div>
                </div>
            </div>
        </div>

        <div class="col-xxl-2 col-lg-4 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="dash-label">Total Collections</span>
                        <span class="dash-icon di-green"><i class="feather-arrow-up-circle"></i></span>
                    </div>
                    <div class="dash-value">₱{{ number_format($totalCollections, 2) }}</div>
                    <div class="dash-sub">{{ $collectionGrowth > 0 ? '+' : '' }}{{ number_format($collectionGrowth, 1) }}% this period</div>
                </div>
            </div>
        </div>

        <div class="col-xxl-2 col-lg-4 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="dash-label">Total Expenses</span>
                        <span class="dash-icon di-red"><i class="feather-arrow-down-circle"></i></span>
                    </div>
                    <div class="dash-value">₱{{ number_format($totalExpenses, 2) }}</div>
                    <div class="dash-sub">+5.2% this period</div>
                </div>
            </div>
        </div>

        <div class="col-xxl-2 col-lg-4 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="dash-label">Net Remittance</span>
                        <span class="dash-icon di-green"><i class="feather-trending-up"></i></span>
                    </div>
                    <div class="dash-value">₱{{ number_format($totalNetRemittance, 2) }}</div>
                    <div class="dash-sub">+18.3% this period</div>
                </div>
            </div>
        </div>

        <div class="col-xxl-2 col-lg-4 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="dash-label">Avg. Collection</span>
                        <span class="dash-icon di-blue"><i class="feather-bar-chart-2"></i></span>
                    </div>
                    <div class="dash-value">₱{{ number_format($averageCollection, 2) }}</div>
                    <div class="dash-sub">+8.7% this period</div>
                </div>
            </div>
        </div>

        <div class="col-xxl-2 col-lg-4 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="dash-label">Avg. Expenses</span>
                        <span class="dash-icon di-amber"><i class="feather-alert-circle"></i></span>
                    </div>
                    <div class="dash-value">₱{{ number_format($averageExpenses, 2) }}</div>
                    <div class="dash-sub">-3.1% this period</div>
                </div>
            </div>
        </div>

    </div>

    {{-- Status cards --}}
    <div class="row g-3 mb-4">

        <div class="col-xxl-3 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <span class="dash-label">Pending Remittances</span>
                    <div class="dash-value mt-3">{{ $pendingRemittances }}</div>
                    <div class="progress dash-progress mt-3">
                        <div class="progress-bar bg-warning" role="progressbar"
                            style="width: {{ $totalRemittances > 0 ? ($pendingRemittances/$totalRemittances)*100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <span class="dash-label">Completed Remittances</span>
                    <div class="dash-value mt-3">{{ $completedRemittances }}</div>
                    <div class="progress dash-progress mt-3">
                        <div class="progress-bar bg-success" role="progressbar"
                            style="width: {{ $totalRemittances > 0 ? ($completedRemittances/$totalRemittances)*100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <span class="dash-label">Active Drivers</span>
                    <div class="dash-value mt-3">{{ $activeDrivers }}</div>
                    <div class="dash-sub mt-1">
                        <a href="{{ route('drivers.index') }}" style="font-size:.8rem; color:#c8292a; font-weight:600; text-decoration:none;">View Drivers →</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <span class="dash-label">Active Resources</span>
                    <div class="d-flex justify-content-between align-items-end mt-3">
                        <div>
                            <div class="dash-value">{{ $activeVehicles }}</div>
                            <div class="dash-sub mt-1">Vehicles</div>
                        </div>
                        <div class="text-end">
                            <div class="dash-value">{{ $activePAOs }}</div>
                            <div class="dash-sub mt-1">PAOs</div>
                        </div>
                    </div>
                    <div class="dash-sub mt-2">
                        <a href="{{ route('vehicles.index') }}" style="font-size:.8rem; color:#c8292a; font-weight:600; text-decoration:none;">Manage →</a>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Collections vs Expenses + Recent Remittances trend --}}
    <div class="row g-3 mb-4">

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <span class="card-title mb-0">Collections vs Expenses</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="dash-label" style="text-transform:none; letter-spacing:0;">Collections</span>
                            <span style="font-size:.82rem; font-weight:700; color:#16a34a;">
                                {{ $totalCollections + $totalExpenses > 0 ? number_format(($totalCollections / ($totalCollections + $totalExpenses)) * 100, 1) : 0 }}%
                            </span>
                        </div>
                        <div class="progress dash-progress">
                            <div class="progress-bar bg-success"
                                style="width: {{ $totalCollections + $totalExpenses > 0 ? ($totalCollections / ($totalCollections + $totalExpenses)) * 100 : 0 }}%">
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="dash-label" style="text-transform:none; letter-spacing:0;">Expenses</span>
                            <span style="font-size:.82rem; font-weight:700; color:#e11d48;">
                                {{ $totalCollections + $totalExpenses > 0 ? number_format(($totalExpenses / ($totalCollections + $totalExpenses)) * 100, 1) : 0 }}%
                            </span>
                        </div>
                        <div class="progress dash-progress">
                            <div class="progress-bar bg-danger"
                                style="width: {{ $totalCollections + $totalExpenses > 0 ? ($totalExpenses / ($totalCollections + $totalExpenses)) * 100 : 0 }}%">
                            </div>
                        </div>
                    </div>
                    <div class="pt-3 border-top">
                        <div class="row text-center">
                            <div class="col">
                                <div class="dash-label">Total Collections</div>
                                <div style="font-size:.95rem; font-weight:700; color:#16a34a; margin-top:4px;">₱{{ number_format($totalCollections, 2) }}</div>
                            </div>
                            <div class="col">
                                <div class="dash-label">Total Expenses</div>
                                <div style="font-size:.95rem; font-weight:700; color:#e11d48; margin-top:4px;">₱{{ number_format($totalExpenses, 2) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <span class="card-title mb-0">Monthly Remittance Trend</span>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <tbody>
                            @foreach($recentRemittances->groupBy(fn($r) => $r->remittance_date?->format('M Y')) as $month => $group)
                                <tr>
                                    <td class="ps-4" style="font-size:.82rem; font-weight:600; color:#4a4a58; width:100px;">
                                        {{ $month }}
                                    </td>
                                    <td>
                                        <div class="progress dash-progress">
                                            <div class="progress-bar" style="background:#c8292a; width: {{ $totalCollections > 0 ? ($group->sum('total_collection') / $totalCollections) * 100 : 0 }}%"></div>
                                        </div>
                                    </td>
                                    <td class="text-end pe-4" style="font-size:.82rem; font-weight:700; color:#1c1c1e; width:110px;">
                                        ₱{{ number_format($group->sum('total_collection'), 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    {{-- Recent Remittances Table --}}
    <div class="card remui-card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="card-title mb-0">Recent Remittances</span>
            <a href="{{ route('remittances.index') }}" class="btn btn-primary btn-sm">View All</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 remui-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Driver</th>
                            <th>Vehicle</th>
                            <th>Route</th>
                            <th>Collection</th>
                            <th>Expenses</th>
                            <th>Net</th>
                            <th class="text-center">Status</th>
                            <th class="text-center"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentRemittances as $remittance)
                            <tr>
                                <td style="font-size:.82rem; color:#4a4a58;">{{ $remittance->remittance_date?->format('M d, Y') ?? 'N/A' }}</td>
                                <td><strong>{{ $remittance->driver->name ?? 'N/A' }}</strong></td>
                                <td style="font-size:.82rem; color:#4a4a58;">{{ $remittance->vehicle->plate_number ?? 'N/A' }}</td>
                                <td style="font-size:.82rem; color:#4a4a58;">{{ $remittance->route->route_name ?? 'N/A' }}</td>
                                <td style="color:#16a34a;">₱{{ number_format($remittance->total_collection, 2) }}</td>
                                <td style="color:#e11d48;">₱{{ number_format($remittance->total_expenses, 2) }}</td>
                                <td><strong>₱{{ number_format($remittance->net_remittance, 2) }}</strong></td>
                                <td class="text-center">
                                    @php
                                        $statusMap = [
                                            'completed' => ['bg' => '#f0fdf4', 'color' => '#16a34a'],
                                            'approved'  => ['bg' => '#f0f9ff', 'color' => '#0284c7'],
                                            'pending'   => ['bg' => '#fffbeb', 'color' => '#d97706'],
                                            'rejected'  => ['bg' => '#fff1f2', 'color' => '#e11d48'],
                                        ];
                                        $st = $statusMap[$remittance->status] ?? ['bg' => '#f4f5f7', 'color' => '#9898a8'];
                                    @endphp
                                    <span style="display:inline-block; background:{{ $st['bg'] }}; color:{{ $st['color'] }}; font-size:0.7rem; font-weight:700; padding:3px 10px; border-radius:20px; letter-spacing:0.4px; text-transform:uppercase;">
                                        {{ ucfirst($remittance->status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('remittances.show', $remittance) }}" class="emp-action-btn emp-action-view" title="View">
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
</div>
@endsection