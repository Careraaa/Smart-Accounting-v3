@extends('layouts.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Remittance Reports</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Driver</th>
                                <th>PAO</th>
                                <th>Route</th>
                                <th>Collection</th>
                                <th>Expenses</th>
                                <th>Net Remittance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($remittances as $remittance)
                                <tr>
                                    <td>{{ $remittance->remittance_date }}</td>
                                    <td>{{ $remittance->driver->name }}</td>
                                    <td>{{ $remittance->pao->name }}</td>
                                    <td>{{ $remittance->route->route_name }}</td>
                                    <td>₱{{ number_format($remittance->total_collection, 2) }}</td>
                                    <td>₱{{ number_format($remittance->total_expenses, 2) }}</td>
                                    <td>₱{{ number_format($remittance->net_remittance, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No remittance records found</td>
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
