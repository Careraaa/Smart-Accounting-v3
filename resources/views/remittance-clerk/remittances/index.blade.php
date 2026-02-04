@extends('layouts.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Daily Remittance</h5>
                    <a href="{{ route('remittances.create') }}" class="btn btn-primary btn-sm">Add Remittance</a>
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
                                <th>Expenses</th>
                                <th>Net</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($remittances as $remittance)
                                <tr>
                                    <td>{{ $remittance->remittance_date }}</td>
                                    <td>{{ $remittance->driver->name }}</td>
                                    <td>{{ $remittance->pao->name }}</td>
                                    <td>{{ $remittance->route->route_name }}</td>
                                    <td>{{ $remittance->vehicle->plate_number }}</td>
                                    <td>₱{{ number_format($remittance->total_collection, 2) }}</td>
                                    <td>₱{{ number_format($remittance->total_expenses, 2) }}</td>
                                    <td>₱{{ number_format($remittance->net_remittance, 2) }}</td>
                                    <td><span class="badge bg-info">{{ $remittance->status }}</span></td>
                                    <td>
                                        <a href="{{ route('remittances.show', $remittance) }}" class="btn btn-info btn-sm">View</a>
                                        <a href="{{ route('remittances.edit', $remittance) }}" class="btn btn-warning btn-sm">Edit</a>
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
@endsection
