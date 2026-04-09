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
                            <span class="badge badge-{{ $shortRemittance->status === 'approved' ? 'success' : ($shortRemittance->status === 'rejected' ? 'danger' : 'warning') }}">
                                {{ ucfirst($shortRemittance->status) }}
                            </span>
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
                        <p class="mb-0"><strong>{{ $shortRemittance->route->name ?? 'N/A' }}</strong></p>
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
                        <label class="text-muted small d-block mb-1">Employee ID</label>
                        <p class="mb-0">{{ $shortRemittance->driver->employee_id ?? 'N/A' }}</p>
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
                        <label class="text-muted small d-block mb-1">Employee ID</label>
                        <p class="mb-0">{{ $shortRemittance->pao->employee_id ?? 'N/A' }}</p>
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
                            <div class="col-md-3">
                                <label class="text-muted small d-block mb-1">Total Collection</label>
                                <p class="mb-0"><strong>₱{{ number_format($shortRemittance->total_collection, 2) }}</strong></p>
                            </div>
                            <div class="col-md-3">
                                <label class="text-muted small d-block mb-1">Total Expenses</label>
                                <p class="mb-0"><strong>₱{{ number_format($shortRemittance->total_expenses, 2) }}</strong></p>
                            </div>
                            <div class="col-md-3">
                                <label class="text-muted small d-block mb-1">Boundary Rate</label>
                                <p class="mb-0"><strong>₱{{ number_format($shortRemittance->boundary ?? 0, 2) }}</strong></p>
                            </div>
                            <div class="col-md-3">
                                <label class="text-muted small d-block mb-1">Expected Net</label>
                                <p class="mb-0"><strong>₱{{ number_format($shortRemittance->total_collection - $shortRemittance->total_expenses - ($shortRemittance->boundary ?? 0), 2) }}</strong></p>
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
            <div class="text-end mt-4">
                <a href="{{ route('short-remittances.edit', $shortRemittance) }}" class="btn btn-primary">
                    <i class="feather-edit-2 me-1"></i> Mark Resolution
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Summary Sidebar --}}
<div class="col-md-3">
    <div class="card card-statistic">
        <div class="card-body">
            <h6 class="card-title mb-3">Short Remittance Summary</h6>
            <div class="mb-3">
                <small class="text-muted">Date</small>
                <p class="mb-2"><strong>{{ $shortRemittance->remittance_date?->format('M d, Y') }}</strong></p>
            </div>
            <div class="mb-3">
                <small class="text-muted">Vehicle</small>
                <p class="mb-2"><strong>{{ $shortRemittance->vehicle->plate_number }}</strong></p>
            </div>
            <div class="mb-3">
                <small class="text-muted">Short Amount</small>
                <p class="mb-2"><strong style="color: #dc2626; font-size: 1.1rem;">₱{{ number_format($shortRemittance->short_amount, 2) }}</strong></p>
            </div>
            <div class="mb-3 pb-3 border-bottom">
                <small class="text-muted">Status</small>
                <p class="mb-0">
                    <span class="badge badge-{{ $shortRemittance->status === 'approved' ? 'success' : ($shortRemittance->status === 'rejected' ? 'danger' : 'warning') }} w-100 text-center">
                        {{ ucfirst($shortRemittance->status) }}
                    </span>
                </p>
            </div>
            <div class="mb-2">
                <small class="text-muted d-block mb-2">Liability Breakdown</small>
                <div class="row g-2">
                    <div class="col-6">
                        <small class="text-muted d-block">Driver</small>
                        <p class="mb-0"><strong style="color: #0369a1;">₱{{ number_format($shortRemittance->driver_share, 2) }}</strong></p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">PAO</small>
                        <p class="mb-0"><strong style="color: #16a34a;">₱{{ number_format($shortRemittance->pao_share, 2) }}</strong></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
