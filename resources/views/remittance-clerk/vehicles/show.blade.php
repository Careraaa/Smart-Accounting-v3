@extends('layouts.layout')

@push('styles')
    @include('remittance-clerk._ui-styles')
@endpush

@section('content')
<div class="col-12">
<div class="remui-page">
<div class="rem-wrap">

    @php
        $vstatus = strtolower($vehicle->status ?? 'active');
        $vsc = match($vstatus) { 'active' => 's-active', 'under_maintenance' => 's-maintenance', default => 's-inactive' };
        $vlabel = match($vstatus) { 'under_maintenance' => 'Under Maintenance', default => ucfirst($vstatus) };
    @endphp

    {{-- Back --}}
    <div style="margin-bottom:12px;">
        <a href="{{ route('vehicles.index') }}" class="rem-btn-edit">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back
        </a>
    </div>

    {{-- Hero --}}
    <div class="rem-hero">
        <div class="rem-hero-left">
            <p class="rem-hero-sub">
                {{ $vehicle->route ? ($vehicle->route->origin . ' → ' . $vehicle->route->destination) : 'No route assigned' }}
            </p>
            <h1 class="rem-hero-title">{{ $vehicle->plate_number }}</h1>
        </div>
        <div class="rem-hero-right">
            <div class="rem-hero-chips">
                <div class="rem-hero-chip">
                    <span class="rem-hero-chip-lbl">Operator</span>
                    <span class="rem-hero-chip-val">{{ $vehicle->operator ?? '—' }}</span>
                </div>
                <div class="rem-hero-chip">
                    <span class="rem-hero-chip-lbl">Boundary Rate</span>
                    <span class="rem-hero-chip-val">
                        @if($vehicle->route && $vehicle->route->boundary)
                            ₱{{ number_format($vehicle->route->boundary, 2) }}
                        @else
                            —
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Vehicle Information Card --}}
    <div class="rem-card">
        <div class="rem-card-head">
            <div class="rem-card-icon slate">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
            </div>
            <span class="rem-card-title">Vehicle Information</span>
        </div>
        <div class="rem-card-body">
            <div class="rem-brow">
                <span class="rem-brow-lbl">Plate Number</span>
                <span class="rem-brow-val">{{ $vehicle->plate_number ?? '—' }}</span>
            </div>
            <div class="rem-brow">
                <span class="rem-brow-lbl">Operator</span>
                <span class="rem-brow-val" style="font-family:'Sora',sans-serif;">{{ $vehicle->operator ?? '—' }}</span>
            </div>
            <div class="rem-brow">
                <span class="rem-brow-lbl">Status</span>
                <span class="rem-brow-val"><span class="prl-status {{ $vsc }}">{{ $vlabel }}</span></span>
            </div>
            <div class="rem-brow">
                <span class="rem-brow-lbl">Origin</span>
                <span class="rem-brow-val" style="font-family:'Sora',sans-serif;">{{ $vehicle->route->origin ?? '—' }}</span>
            </div>
            <div class="rem-brow">
                <span class="rem-brow-lbl">Destination</span>
                <span class="rem-brow-val" style="font-family:'Sora',sans-serif;">{{ $vehicle->route->destination ?? '—' }}</span>
            </div>
            <div class="rem-brow">
                <span class="rem-brow-lbl">Boundary Rate</span>
                <span class="rem-brow-val">
                    @if($vehicle->route && $vehicle->route->boundary)
                        ₱{{ number_format($vehicle->route->boundary, 2) }}
                    @else
                        —
                    @endif
                </span>
            </div>

            {{-- Footer --}}
            <div class="rem-footer">
                <a href="{{ route('vehicles.edit', $vehicle) }}" class="rem-btn-edit">
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit Vehicle
                </a>
                <form action="{{ route('vehicles.destroy', $vehicle) }}" method="POST" class="d-inline"
                    data-sa-confirm="Delete this vehicle?">
                    @csrf @method('DELETE')
                    <button type="submit" class="rem-btn-delete">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>{{-- rem-wrap --}}
</div>{{-- remui-page --}}
</div>{{-- col-12 --}}
@endsection
