@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <span class="card-title mb-0">Remittance Approval</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover w-100 mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Route</th>
                            <th>Vehicle</th>
                            <th>Collection</th>
                            <th>Expenses</th>
                            <th>Net Remittance</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($remittances as $remittance)
                            <tr>
                                <td class="text-muted" style="font-size:.82rem;">{{ $remittance->remittance_date?->format('M d, Y') }}</td>
                                <td>{{ $remittance->route->route_name }}</td>
                                <td>{{ $remittance->vehicle->plate_number }}</td>
                                <td>₱{{ number_format($remittance->total_collection, 2) }}</td>
                                <td class="text-muted">₱{{ number_format($remittance->total_expenses, 2) }}</td>
                                <td><strong>₱{{ number_format($remittance->net_remittance, 2) }}</strong></td>
                                <td class="text-center">
                                    @php
                                        $statusMap = [
                                            'pending'  => ['bg' => '#fffbeb', 'color' => '#d97706'],
                                            'approved' => ['bg' => '#f0fdf4', 'color' => '#16a34a'],
                                            'rejected' => ['bg' => '#fff1f2', 'color' => '#e11d48'],
                                        ];
                                        $st = $statusMap[$remittance->status] ?? ['bg' => '#f4f5f7', 'color' => '#9898a8'];
                                    @endphp
                                    <span style="display:inline-block; background:{{ $st['bg'] }}; color:{{ $st['color'] }}; font-size:0.7rem; font-weight:700; padding:3px 10px; border-radius:20px; letter-spacing:0.4px; text-transform:uppercase;">
                                        {{ ucfirst($remittance->status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($remittance->status === 'pending')
                                        <div class="d-flex justify-content-center gap-1">
                                            <form action="{{ route('remittance-approval.approve', $remittance) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="emp-action-btn emp-action-approve" title="Approve">
                                                    <i class="feather-check"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('remittance-approval.reject', $remittance) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="emp-action-btn emp-action-danger" title="Reject"
                                                    onclick="return confirm('Reject this remittance?')">
                                                    <i class="feather-x"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-muted">—</span>
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
@endsection