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
                <h5 class="remui-title">{{ $pao->name }}</h5>
                <p class="remui-subtitle mb-0">PAO / Conductor profile and employment status.</p>
            </div>
            <div class="d-flex flex-wrap gap-2 justify-content-end">
                <a href="{{ route('paos.edit', $pao) }}" class="emp-action-btn emp-action-edit">
                    <i class="feather-edit-2"></i><span>Edit</span>
                </a>
                <form action="{{ route('paos.destroy', $pao) }}" method="POST"
                    onsubmit="return confirm('Are you sure you want to delete this PAO?')" class="d-inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="emp-action-btn emp-action-danger">
                        <i class="feather-trash-2"></i><span>Delete</span>
                    </button>
                </form>
                <a href="{{ route('paos.index') }}" class="emp-action-btn emp-action-back">
                    <i class="feather-arrow-left"></i><span>Back</span>
                </a>
            </div>
        </div>

    {{-- PAO Information --}}
    <div class="card remui-card mb-3">
        <div class="card-header"><span class="card-title mb-0">PAO Information</span></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <span class="emp-field-label">Name</span>
                    <div class="emp-field-value">{{ $pao->name ?? '—' }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <span class="emp-field-label">Contact Number</span>
                    <div class="emp-field-value">{{ $pao->contact_number ?? '—' }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <span class="emp-field-label">Email</span>
                    <div class="emp-field-value">{{ $pao->email ?? '—' }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <span class="emp-field-label">Date of Hire</span>
                    <div class="emp-field-value">{{ $pao->date_of_hire?->format('F d, Y') ?? '—' }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <span class="emp-field-label">Status</span>
                    <div class="mt-1">
                        @if ($pao->status === 'active')
                            <span class="emp-badge emp-badge-active">Active</span>
                        @elseif ($pao->status === 'pending')
                            <span class="emp-badge emp-badge-pending">Pending</span>
                        @else
                            <span class="emp-badge emp-badge-inactive">Inactive</span>
                        @endif
                    </div>
                </div>
                @if ($pao->address)
                <div class="col-md-12 mb-3">
                    <span class="emp-field-label">Address</span>
                    <div class="emp-field-value">{{ $pao->address }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>
    </div>
</div>
@endsection