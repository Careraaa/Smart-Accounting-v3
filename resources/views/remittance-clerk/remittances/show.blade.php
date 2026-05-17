@extends('layouts.layout')

@push('styles')
    @include('remittance-clerk._ui-styles')
@endpush

@section('content')
<div class="col-12">
    <div class="remui-page">

        <div class="prl-topbar">
            <div>
                <h1 class="prl-topbar-title">{{ $remittance->remittance_date?->format('F d, Y') ?? 'Remittance' }}</h1>
                <p class="prl-topbar-sub">{{ $remittance->route->route_name ?? '—' }}</p>
            </div>
            <div class="prl-topbar-actions">
                <a href="{{ route('remittances.edit', $remittance) }}" class="prl-btn-ghost">
                    <i class="feather-edit-2"></i> Edit
                </a>
                <form action="{{ route('remittances.destroy', $remittance) }}" method="POST" class="d-inline"
                    data-sa-confirm="Delete this remittance?">
                    @csrf @method('DELETE')
                    <button type="submit" class="prl-action-btn danger">
                        <i class="feather-trash-2"></i> Delete
                    </button>
                </form>
                <a href="{{ route('remittances.index') }}" class="prl-btn-ghost">
                    <i class="feather-arrow-left"></i> Back
                </a>
            </div>
        </div>

        {{-- Remittance Details --}}
        <div class="prl-detail-card mb-3">
            <div class="prl-detail-head">
                <h2 class="prl-detail-title">Remittance Details</h2>
            </div>
            <div class="prl-detail-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <span class="prl-field-label">Remittance Date</span>
                        <div class="prl-field-value">{{ $remittance->remittance_date?->format('F d, Y') ?? '—' }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <span class="prl-field-label">Driver</span>
                        <div class="prl-field-value">{{ $remittance->driver->name ?? '—' }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <span class="prl-field-label">PAO</span>
                        <div class="prl-field-value">{{ $remittance->pao->name ?? '—' }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <span class="prl-field-label">Vehicle</span>
                        <div class="prl-field-value">{{ $remittance->vehicle->plate_number ?? '—' }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <span class="prl-field-label">Route</span>
                        <div class="prl-field-value">{{ $remittance->route->route_name ?? '—' }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <span class="prl-field-label">Status</span>
                        <div class="mt-1">
                            @if ($remittance->status === 'approved')
                                <span class="prl-status s-active">Approved</span>
                            @elseif ($remittance->status === 'pending')
                                <span class="prl-status s-pending">Pending</span>
                            @else
                                <span class="prl-status s-inactive">Rejected</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Financial Summary --}}
        <div class="prl-detail-card mb-3">
            <div class="prl-detail-head">
                <h2 class="prl-detail-title">Financial Summary</h2>
            </div>
            <div class="prl-detail-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <span class="prl-field-label">Total Collection</span>
                        <div style="font-size: 1rem; font-weight: 700; color: #16a34a;">₱{{ number_format($remittance->total_collection, 2) }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <span class="prl-field-label">Total Expenses</span>
                        <div style="font-size: 1rem; font-weight: 700; color: #e11d48;">₱{{ number_format($remittance->total_expenses, 2) }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <span class="prl-field-label">Net Remittance</span>
                        <div style="font-size: 1rem; font-weight: 700; color: #1c1c1e;">₱{{ number_format($remittance->net_remittance, 2) }}</div>
                    </div>
                </div>

                <div class="prl-net-box mt-2">
                    <span class="prl-net-label">Net Remittance</span>
                    <span class="prl-net-value">₱{{ number_format($remittance->net_remittance, 2) }}</span>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
