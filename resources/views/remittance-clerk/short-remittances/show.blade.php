@extends('layouts.layout')

@push('styles')
    @include('remittance-clerk._ui-styles')
@endpush

@section('content')
<div class="col-12">
    <div class="remui-page">
        <div class="remui-backdrop"></div>

        <div class="remui-hero mb-3">
            <div>
                <h5 class="remui-title">Short Remittance Details</h5>
                <p class="remui-subtitle mb-0">Review shortage breakdown and resolution status.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('short-remittances.index') }}" class="emp-action-btn emp-action-back">
                    <i class="feather-arrow-left"></i><span>Back</span>
                </a>
            </div>
        </div>

        <div class="card remui-card">
            <div class="card-header">
                <span class="card-title mb-0">Summary</span>
            </div>
            <div class="card-body">
            {{-- Header Section --}}
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5 class="text-muted mb-3">Remittance Information</h5>
                    <div class="mb-3">
                        <label class="text-muted small d-block mb-1">Date</label>
                        <p class="mb-0"><strong>{{ $shortRemittance->remittance_date?->format('F d, Y') }}</strong></p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small d-block mb-1">Status</label>
                        <p class="mb-0">
                            @if($shortRemittance->status === 'approved')
                                <span class="badge bg-success">Approved</span>
                            @elseif($shortRemittance->status === 'rejected')
                                <span class="badge bg-danger">Rejected</span>
                            @else
                                <span class="badge bg-warning">Pending</span>
                            @endif
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <h5 class="text-muted mb-3">Vehicle Information</h5>
                    <div class="mb-3">
                        <label class="text-muted small d-block mb-1">Plate Number</label>
                        <p class="mb-0"><strong>{{ $shortRemittance->vehicle->plate_number }}</strong></p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small d-block mb-1">Route</label>
                        <p class="mb-0"><strong>{{ $shortRemittance->route->route_name ?? 'N/A' }}</strong></p>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            {{-- Personnel Section --}}
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5 class="text-muted mb-3">Driver Information</h5>
                    <div class="mb-3">
                        <label class="text-muted small d-block mb-1">Name</label>
                        <p class="mb-0"><strong>{{ $shortRemittance->driver->name ?? 'N/A' }}</strong></p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small d-block mb-1">Liability (50% of Short Amount)</label>
                        <p class="mb-0">
                            <strong style="color: #0369a1; font-size: 1.25rem;">₱{{ number_format($shortRemittance->driver_share, 2) }}</strong>
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <h5 class="text-muted mb-3">PAO Information</h5>
                    <div class="mb-3">
                        <label class="text-muted small d-block mb-1">Name</label>
                        <p class="mb-0"><strong>{{ $shortRemittance->pao->name ?? 'N/A' }}</strong></p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small d-block mb-1">Liability (50% of Short Amount)</label>
                        <p class="mb-0">
                            <strong style="color: #16a34a; font-size: 1.25rem;">₱{{ number_format($shortRemittance->pao_share, 2) }}</strong>
                        </p>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            {{-- Financial Breakdown Section --}}
            <div class="mb-4">
                <h5 class="text-muted mb-3">Financial Breakdown</h5>
                <div class="card bg-light">
                    <div class="card-body">
                        <div class="row text-center mb-2">
                            <div class="col-md-4">
                                <label class="text-muted small d-block mb-1">Total Collection</label>
                                <p class="mb-0"><strong>₱{{ number_format($shortRemittance->total_collection, 2) }}</strong></p>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small d-block mb-1">Total Expenses</label>
                                <p class="mb-0"><strong>₱{{ number_format($shortRemittance->total_expenses, 2) }}</strong></p>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small d-block mb-1">Boundary Rate</label>
                                <p class="mb-0"><strong>₱{{ number_format($shortRemittance->boundary ?? 0, 2) }}</strong></p>
                            </div>
                        </div>
                        
                        <div class="row text-center mt-3 pt-3 border-top">
                            <div class="col-md-6">
                                <label class="text-muted small d-block mb-1">Net Remittance (Recorded)</label>
                                <p class="mb-0">
                                    <strong style="@if($shortRemittance->net_remittance < 0) color: #dc2626; @endif font-size: 1.25rem;">
                                        ₱{{ number_format($shortRemittance->net_remittance, 2) }}
                                    </strong>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small d-block mb-1">Short Amount</label>
                                <p class="mb-0">
                                    <strong style="color: #dc2626; font-size: 1.25rem;">₱{{ number_format($shortRemittance->short_amount, 2) }}</strong>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="d-flex gap-2 pt-3 border-top justify-content-end">
                <a href="{{ route('short-remittances.edit', $shortRemittance) }}" class="btn btn-primary">Mark Resolution</a>
            </div>
        </div>
    </div>
</div>
@endsection
