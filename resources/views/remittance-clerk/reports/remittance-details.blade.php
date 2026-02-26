@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <span class="card-title mb-0">Remittance Details Report</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover w-100 mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Driver</th>
                            <th>PAO</th>
                            <th>Route</th>
                            <th>Vehicle</th>
                            <th>Collection</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($remittances as $remittance)
                            <tr>
                                <td class="text-muted">{{ $remittance->remittance_date?->format('M d, Y') ?? 'N/A' }}</td>
                                <td><strong>{{ $remittance->driver->name ?? 'N/A' }}</strong></td>
                                <td>{{ $remittance->pao->name ?? 'N/A' }}</td>
                                <td>{{ $remittance->route->route_name ?? 'N/A' }}</td>
                                <td>{{ $remittance->vehicle->plate_number ?? 'N/A' }}</td>
                                <td style="color:#16a34a;">₱{{ number_format($remittance->total_collection, 2) }}</td>
                                <td class="text-center">
                                    @if ($remittance->status === 'approved')
                                        <span class="emp-badge emp-badge-approved">Approved</span>
                                    @elseif ($remittance->status === 'pending')
                                        <span class="emp-badge emp-badge-pending">Pending</span>
                                    @else
                                        <span class="emp-badge emp-badge-inactive">Rejected</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('remittances.show', $remittance) }}"
                                        class="emp-action-btn emp-action-view" title="View">
                                        <i class="feather-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <i class="feather-file-text d-block mb-2" style="font-size:28px; opacity:.3;"></i>
                                    No remittance records found
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
.emp-badge { display: inline-block; font-size: 0.7rem; font-weight: 700; letter-spacing: 0.5px; padding: 3px 10px; border-radius: 20px; }
.emp-badge-active   { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
.emp-badge-inactive { background: #fff5f5; color: #c8292a; border: 1px solid #fcd0d0; }
.emp-badge-pending  { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
.emp-badge-approved { background: #f0f9ff; color: #0284c7; border: 1px solid #bae6fd; }
.emp-action-btn {
    display: inline-flex; align-items: center; justify-content: center;
    width: 30px; height: 30px; border-radius: 6px;
    background: #f4f5f7; border: none; color: #9898a8;
    font-size: 13px; cursor: pointer; text-decoration: none;
    transition: background 0.13s, color 0.13s; padding: 0;
}
.emp-action-btn.emp-action-view:hover { background: #eff6ff; color: #3b82f6; }
</style>
@endsection