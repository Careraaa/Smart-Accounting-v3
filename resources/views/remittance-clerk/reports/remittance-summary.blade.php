@extends('layouts.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Remittance Summary Report</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Driver</th>
                                <th>Total Remittances</th>
                                <th>Total Collection</th>
                                <th>Total Expenses</th>
                                <th>Net Remittance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($remittances->groupBy('driver_id') as $driverRemittances)
                                <tr>
                                    <td>{{ $driverRemittances->first()->driver->name }}</td>
                                    <td>{{ $driverRemittances->count() }}</td>
                                    <td>₱{{ number_format($driverRemittances->sum('total_collection'), 2) }}</td>
                                    <td>₱{{ number_format($driverRemittances->sum('total_expenses'), 2) }}</td>
                                    <td>₱{{ number_format($driverRemittances->sum('net_remittance'), 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No remittance records found</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="table-active fw-bold">
                                <td>TOTAL</td>
                                <td>{{ $remittances->count() }}</td>
                                <td>₱{{ number_format($remittances->sum('total_collection'), 2) }}</td>
                                <td>₱{{ number_format($remittances->sum('total_expenses'), 2) }}</td>
                                <td>₱{{ number_format($remittances->sum('net_remittance'), 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
