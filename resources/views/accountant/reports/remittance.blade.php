@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <span class="card-title mb-0">Remittance Reports</span>
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
                            <th>Collection</th>
                            <th>Expenses</th>
                            <th>Net Remittance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($remittances as $remittance)
                            <tr>
                                <td class="text-muted" style="font-size:.82rem;">{{ $remittance->remittance_date }}</td>
                                <td><strong>{{ $remittance->driver->name }}</strong></td>
                                <td>{{ $remittance->pao->name }}</td>
                                <td>{{ $remittance->route->route_name }}</td>
                                <td>₱{{ number_format($remittance->total_collection, 2) }}</td>
                                <td class="text-muted">₱{{ number_format($remittance->total_expenses, 2) }}</td>
                                <td><strong>₱{{ number_format($remittance->net_remittance, 2) }}</strong></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
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
@endsection