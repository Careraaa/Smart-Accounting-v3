@extends('layouts.layout')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Daily Remittance</h5>
                        <a href="{{ route('remittances.create') }}" class="btn btn-outline-primary border-1 rounded">Add
                            Remittance</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover w-100" style="table-layout: fixed;">
                                <thead>
                                    <tr>
                                        <th style="width: 15%; text-align: left;">Date</th>
                                        <th style="width: 20%; text-align: left;">Route</th>
                                        <th style="width: 15%; text-align: left;">Vehicle</th>
                                        <th style="width: 15%; text-align: right;">Net Remittance</th>
                                        <th style="width: 15%; text-align: center;">Status</th>
                                        <th style="width: 20%; text-align: center;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $statusStyles = [
                                            'pending' => 'bg-soft-warning text-warning',
                                            'approved' => 'bg-soft-success text-success',
                                            'rejected' => 'bg-soft-danger text-danger',
                                        ];
                                    @endphp

                                    @forelse($remittances as $remittance)
                                        <tr>
                                            <td style="text-align: left;">
                                                {{ $remittance->remittance_date?->format('Y-m-d') }}</td>
                                            <td style="text-align: left;">{{ $remittance->route->route_name }}</td>
                                            <td style="text-align: left;">{{ $remittance->vehicle->plate_number }}</td>
                                            <td style="text-align: right;">
                                                ₱{{ number_format($remittance->net_remittance, 2) }}</td>
                                            <td class="text-center">
                                                <span
                                                    class="badge px-3 {{ $statusStyles[$remittance->status] ?? 'bg-secondary text-white' }}">
                                                    {{ ucfirst($remittance->status) }}
                                                </span>
                                            </td>
                                            <td class="text-center align-middle">
                                                <div class="d-flex justify-content-center gap-1">
                                                    <a href="{{ route('remittances.show', $remittance) }}"
                                                        class="btn btn-outline-info btn-sm border-1 rounded" title="View">
                                                        <i class="feather-eye"></i>
                                                    </a>
                                                    <a href="{{ route('remittances.edit', $remittance) }}"
                                                        class="btn btn-outline-warning btn-sm border-1 rounded"
                                                        title="Edit">
                                                        <i class="feather-edit"></i>
                                                    </a>
                                                    <form action="{{ route('remittances.destroy', $remittance) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-outline-danger btn-sm border-1 rounded"
                                                            onclick="return confirm('Are you sure you want to delete this remittance?')"
                                                            title="Delete">
                                                            <i class="feather-trash-2"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-3">No remittances found</td>
                                        </tr>
                                    @endforelse

                                    <!-- Invisible spacer row to fully show bottom button outlines -->
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
