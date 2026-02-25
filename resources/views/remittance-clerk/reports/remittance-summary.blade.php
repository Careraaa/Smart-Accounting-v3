@extends('layouts.layout')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Remittance Summary Report</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover w-100">
                                <thead>
                                    <tr>
                                        <th style="width:20%">Driver</th>
                                        <th style="width:15%">Total Remittances</th>
                                        <th style="width:15%">Total Collection</th>
                                        <th style="width:15%">Total Expenses</th>
                                        <th style="width:15%">Net Remittance</th>
                                        <th style="width:20%" class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($remittances->groupBy('driver_id') as $driverRemittances)
                                        <tr>
                                            <td>{{ $driverRemittances->first()->driver->name ?? 'N/A' }}</td>
                                            <td>{{ $driverRemittances->count() }}</td>
                                            <td>₱{{ number_format($driverRemittances->sum('total_collection'), 2) }}</td>
                                            <td>₱{{ number_format($driverRemittances->sum('total_expenses'), 2) }}</td>
                                            <td>
                                                <span class="badge bg-soft-primary text-primary px-3">
                                                    ₱{{ number_format($driverRemittances->sum('net_remittance'), 2) }}
                                                </span>
                                            </td>
                                            <td class="text-center align-middle">
                                                <a href="{{ route('remittances.index') }}?driver_id={{ $driverRemittances->first()->driver_id }}"
                                                    class="btn btn-outline-info btn-sm border-1 rounded" title="View Details">
                                                    <i class="feather-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-3">No remittance records
                                                found</td>
                                        </tr>
                                    @endforelse

                                    <!-- Invisible spacer row to fix bottom button clipping -->
                                    <tr style="height: 8px;">
                                        <td colspan="6"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
