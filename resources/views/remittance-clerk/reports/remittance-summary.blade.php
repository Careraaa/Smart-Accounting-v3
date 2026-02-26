@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <span class="card-title mb-0">Remittance Summary Report</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover w-100 mb-0">
                    <thead>
                        <tr>
                            <th>Driver</th>
                            <th>Total Remittances</th>
                            <th>Total Collection</th>
                            <th>Total Expenses</th>
                            <th>Net Remittance</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($remittances->groupBy('driver_id') as $driverRemittances)
                            <tr>
                                <td><strong>{{ $driverRemittances->first()->driver->name ?? 'N/A' }}</strong></td>
                                <td>{{ $driverRemittances->count() }}</td>
                                <td style="color:#16a34a;">₱{{ number_format($driverRemittances->sum('total_collection'), 2) }}</td>
                                <td style="color:#e11d48;">₱{{ number_format($driverRemittances->sum('total_expenses'), 2) }}</td>
                                <td><strong>₱{{ number_format($driverRemittances->sum('net_remittance'), 2) }}</strong></td>
                                <td class="text-center">
                                    <a href="{{ route('remittances.index') }}?driver_id={{ $driverRemittances->first()->driver_id }}"
                                        class="emp-action-btn emp-action-view" title="View Details">
                                        <i class="feather-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
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