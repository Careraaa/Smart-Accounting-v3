@extends('layouts.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Remittance Approval</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Driver</th>
                                    <th>PAO</th>
                                    <th>Route</th>
                                    <th>Vehicle</th>
                                    <th>Collection</th>
                                    <th>Expenses</th>
                                    <th>Net Remittance</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($remittances as $remittance)
                                    <tr>
                                        <td>{{ $remittance->remittance_date?->format('Y-m-d') }}</td>
                                        <td>{{ $remittance->driver->name }}</td>
                                        <td>{{ $remittance->pao->name }}</td>
                                        <td>{{ $remittance->route->route_name }}</td>
                                        <td>{{ $remittance->vehicle->plate_number }}</td>
                                        <td>₱{{ number_format($remittance->total_collection, 2) }}</td>
                                        <td>₱{{ number_format($remittance->total_expenses, 2) }}</td>
                                        <td>₱{{ number_format($remittance->net_remittance, 2) }}</td>
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
                                            @if($remittance->status === 'pending')
                                                <div class="d-flex gap-2">
                                                    <form action="{{ route('remittance-approval.approve', $remittance) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm" title="Approve">
                                                            <i class="feather-check"></i> Approve
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('remittance-approval.reject', $remittance) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')" title="Reject">
                                                            <i class="feather-x"></i> Reject
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
