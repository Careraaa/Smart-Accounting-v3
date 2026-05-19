@extends('layouts.layout')

@push('styles')
    @include('remittance-clerk._ui-styles')
@endpush

@section('content')
<div class="col-12">
<div class="remui-page">
<div class="rem-wrap">

    {{-- Hero --}}
    <div class="rem-hero">
        <div class="rem-hero-left">
            <p class="rem-hero-sub">{{ $route->origin }} → {{ $route->destination }}</p>
            <h1 class="rem-hero-title">{{ $route->route_name }}</h1>
        </div>
        <div class="rem-hero-right">
            <div class="rem-hero-chips">
                <div class="rem-hero-chip">
                    <span class="rem-hero-chip-lbl">Boundary</span>
                    <span class="rem-hero-chip-val">
                        @if($route->boundary) ₱{{ number_format($route->boundary, 2) }} @else — @endif
                    </span>
                </div>
                <div class="rem-hero-chip">
                    <span class="rem-hero-chip-lbl">Total Vehicles</span>
                    <span class="rem-hero-chip-val">{{ $route->vehicles->count() }}</span>
                </div>
                <div class="rem-hero-chip">
                    <span class="rem-hero-chip-lbl">Active Vehicles</span>
                    <span class="rem-hero-chip-val">{{ $route->vehicles->where('status', 'active')->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Route Information Card --}}
    <div class="rem-card">
        <div class="rem-card-head">
            <div class="rem-card-icon amber">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
            </div>
            <span class="rem-card-title">Route Information</span>
        </div>
        <div class="rem-card-body">
            <div class="rem-brow">
                <span class="rem-brow-lbl">Route Name</span>
                <span class="rem-brow-val" style="font-family:'Sora',sans-serif;">{{ $route->route_name ?? '—' }}</span>
            </div>
            <div class="rem-brow">
                <span class="rem-brow-lbl">Origin</span>
                <span class="rem-brow-val" style="font-family:'Sora',sans-serif;">{{ $route->origin ?? '—' }}</span>
            </div>
            <div class="rem-brow">
                <span class="rem-brow-lbl">Destination</span>
                <span class="rem-brow-val" style="font-family:'Sora',sans-serif;">{{ $route->destination ?? '—' }}</span>
            </div>
            <div class="rem-brow">
                <span class="rem-brow-lbl">Boundary</span>
                <span class="rem-brow-val">
                    @if($route->boundary) ₱{{ number_format($route->boundary, 2) }} @else — @endif
                </span>
            </div>

            {{-- Footer --}}
            <div class="rem-footer">
                <a href="{{ route('routes.edit', $route) }}" class="rem-btn-edit">
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit Route
                </a>
                <form action="{{ route('routes.destroy', $route) }}" method="POST" class="d-inline"
                    data-sa-confirm="Delete this route?">
                    @csrf @method('DELETE')
                    <button type="submit" class="rem-btn-delete">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Assigned Vehicles Card --}}
    @if($route->vehicles->count() > 0)
    <div class="rem-card">
        <div class="rem-card-head">
            <div class="rem-card-icon slate">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
            </div>
            <span class="rem-card-title">Assigned Vehicles</span>
            <span class="rem-card-sub" style="margin-left:auto;">{{ $route->vehicles->count() }} vehicle{{ $route->vehicles->count() !== 1 ? 's' : '' }}</span>
        </div>
        <div class="prl-table-scroll">
            <table class="prl-table w-100 mb-0">
                <thead>
                    <tr>
                        <th>Plate Number</th>
                        <th>Operator</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($route->vehicles as $vehicle)
                        @php
                            $vstatus = strtolower($vehicle->status ?? 'active');
                            $vsc = match($vstatus) { 'active' => 's-active', 'under_maintenance' => 's-maintenance', default => 's-inactive' };
                            $vlabel = match($vstatus) { 'under_maintenance' => 'Under Maintenance', default => ucfirst($vstatus) };
                        @endphp
                        <tr class="clickable" onclick="window.location='{{ route('vehicles.show', $vehicle) }}'">
                            <td class="align-middle"><strong class="prl-mono">{{ $vehicle->plate_number }}</strong></td>
                            <td class="align-middle">{{ $vehicle->operator }}</td>
                            <td class="text-center align-middle">
                                <span class="prl-status {{ $vsc }}">{{ $vlabel }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>{{-- rem-wrap --}}
</div>{{-- remui-page --}}
</div>{{-- col-12 --}}
@endsection
