@extends('layouts.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Remittance Details Report</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Driver</th>
                                <th>PAO</th>
                                <th>Route</th>
                                <th>Vehicle</th>
                                <th>Collection</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($remittances as $remittance)
                                <tr>
                                    <td>{{ $remittance->remittance_date }}</td>
                                    <td>{{ $remittance->driver->name }}</td>
                                    <td>{{ $remittance->pao->name }}</td>
                                    <td>{{ $remittance->route->route_name }}</td>
                                    <td>{{ $remittance->vehicle->plate_number }}</td>
                                    <td>₱{{ number_format($remittance->total_collection, 2) }}</td>
                                    <td>
                                        @if($remittance->status === 'pending')
                                            <div class="badge bg-soft-warning text-warning">{{ ucfirst($remittance->status) }}</div>
                                        @elseif($remittance->status === 'approved')
                                            <div class="badge bg-soft-success text-success">{{ ucfirst($remittance->status) }}</div>
                                        @else
                                            <div class="badge bg-soft-danger text-danger">{{ ucfirst($remittance->status) }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('remittances.show', $remittance) }}" class="avatar-text avatar-md text-info" title="View">
                                            <i class="feather-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No remittance records found</td>
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
