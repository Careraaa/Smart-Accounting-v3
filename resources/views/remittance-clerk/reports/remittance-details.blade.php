@extends('layouts.layout')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Remittance Details Report</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover w-100">
                                <thead>
                                    <tr>
                                        <th style="width:10%">Date</th>
                                        <th style="width:15%">Driver</th>
                                        <th style="width:15%">PAO</th>
                                        <th style="width:15%">Route</th>
                                        <th style="width:10%">Vehicle</th>
                                        <th style="width:10%">Collection</th>
                                        <th style="width:10%" class="text-center">Status</th>
                                        <th style="width:15%" class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($remittances as $remittance)
                                        <tr>
                                            <td>{{ $remittance->remittance_date?->format('M d, Y') ?? 'N/A' }}</td>
                                            <td>{{ $remittance->driver->name ?? 'N/A' }}</td>
                                            <td>{{ $remittance->pao->name ?? 'N/A' }}</td>
                                            <td>{{ $remittance->route->route_name ?? 'N/A' }}</td>
                                            <td>{{ $remittance->vehicle->plate_number ?? 'N/A' }}</td>
                                            <td>₱{{ number_format($remittance->total_collection, 2) }}</td>
                                            <td class="text-center">
                                                @php
                                                    $statusStyles = [
                                                        'pending' => 'bg-soft-warning text-warning',
                                                        'approved' => 'bg-soft-success text-success',
                                                        'rejected' => 'bg-soft-danger text-danger',
                                                    ];
                                                @endphp
                                                <span
                                                    class="badge px-3 {{ $statusStyles[$remittance->status] ?? 'bg-secondary text-white' }}">
                                                    {{ ucfirst($remittance->status) }}
                                                </span>
                                            </td>
                                            <td class="text-center align-middle">
                                                <a href="{{ route('remittances.show', $remittance) }}"
                                                    class="btn btn-outline-info btn-sm border-1 rounded" title="View">
                                                    <i class="feather-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-3">No remittance records
                                                found</td>
                                        </tr>
                                    @endforelse

                                    <!-- Invisible spacer row to fix bottom button clipping -->
                                    <tr style="height: 8px;">
                                        <td colspan="8"></td>
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
