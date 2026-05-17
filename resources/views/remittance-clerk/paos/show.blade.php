@extends('layouts.layout')

@push('styles')
    @include('remittance-clerk._ui-styles')
@endpush

@section('content')
<div class="col-12">
    <div class="remui-page">

        <div class="prl-topbar">
            <div>
                <h1 class="prl-topbar-title">{{ $pao->name }}</h1>
                <p class="prl-topbar-sub">PAO / Conductor profile and employment status.</p>
            </div>
            <div class="prl-topbar-actions">
                <a href="{{ route('paos.edit', $pao) }}" class="prl-btn-ghost">
                    <i class="feather-edit-2"></i> Edit
                </a>
                <form action="{{ route('paos.destroy', $pao) }}" method="POST" class="d-inline"
                    data-sa-confirm="Are you sure you want to delete this PAO?">
                    @csrf @method('DELETE')
                    <button type="submit" class="prl-action-btn danger">
                        <i class="feather-trash-2"></i> Delete
                    </button>
                </form>
                <a href="{{ route('paos.index') }}" class="prl-btn-ghost">
                    <i class="feather-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="prl-detail-card">
            <div class="prl-detail-head">
                <h2 class="prl-detail-title">PAO Information</h2>
            </div>
            <div class="prl-detail-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <span class="prl-field-label">Name</span>
                        <div class="prl-field-value">{{ $pao->name ?? '—' }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <span class="prl-field-label">Contact Number</span>
                        <div class="prl-field-value">{{ $pao->contact_number ?? '—' }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <span class="prl-field-label">Email</span>
                        <div class="prl-field-value">{{ $pao->email ?? '—' }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <span class="prl-field-label">Gender</span>
                        <div class="prl-field-value">{{ ucfirst(str_replace('_', ' ', $pao->gender ?? '—')) }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <span class="prl-field-label">Date of Hire</span>
                        <div class="prl-field-value">{{ $pao->date_of_hire?->format('F d, Y') ?? '—' }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <span class="prl-field-label">Status</span>
                        <div class="mt-1">
                            @if ($pao->status === 'active')
                                <span class="prl-status s-active">Active</span>
                            @elseif ($pao->status === 'pending')
                                <span class="prl-status s-pending">Pending</span>
                            @else
                                <span class="prl-status s-inactive">Inactive</span>
                            @endif
                        </div>
                    </div>
                    @if ($pao->address)
                    <div class="col-md-12 mb-3">
                        <span class="prl-field-label">Address</span>
                        <div class="prl-field-value">{{ $pao->address }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
