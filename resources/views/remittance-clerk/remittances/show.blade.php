@extends('layouts.layout')

@push('styles')
    @include('remittance-clerk._ui-styles')
@endpush

@section('content')
<div class="col-12">
<div class="remui-page">
<div class="rem-wrap">

    @php
        $rsc = match($remittance->status) { 'approved' => 's-approved', 'pending' => 's-pending', default => 's-rejected' };
        $rlabel = ucfirst($remittance->status ?? 'pending');
    @endphp

    {{-- Back --}}
    <div style="margin-bottom:12px;">
        <a href="{{ route('remittances.index') }}" class="rem-btn-edit">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back
        </a>
    </div>

    {{-- Hero --}}
    <div class="rem-hero">
        <div class="rem-hero-left">
            <p class="rem-hero-sub">{{ $remittance->route->route_name ?? '—' }}</p>
            <h1 class="rem-hero-title">{{ $remittance->remittance_date?->format('F d, Y') ?? 'Remittance' }}</h1>
        </div>
        <div class="rem-hero-right">
            <div class="rem-hero-chips">
                <div class="rem-hero-chip">
                    <span class="rem-hero-chip-lbl">Vehicle</span>
                    <span class="rem-hero-chip-val">{{ $remittance->vehicle->plate_number ?? '—' }}</span>
                </div>
                <div class="rem-hero-chip">
                    <span class="rem-hero-chip-lbl">Driver</span>
                    <span class="rem-hero-chip-val" style="font-family:'Sora',sans-serif;font-size:0.82rem;">{{ $remittance->driver->name ?? '—' }}</span>
                </div>
                <div class="rem-hero-chip">
                    <span class="rem-hero-chip-lbl">PAO</span>
                    <span class="rem-hero-chip-val" style="font-family:'Sora',sans-serif;font-size:0.82rem;">{{ $remittance->pao->name ?? '—' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Remittance Details Card --}}
    <div class="rem-card">
        <div class="rem-card-head">
            <div class="rem-card-icon blue">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <span class="rem-card-title">Remittance Details</span>
        </div>
        <div class="rem-card-body">
            <div class="rem-brow">
                <span class="rem-brow-lbl">Date</span>
                <span class="rem-brow-val">{{ $remittance->remittance_date?->format('F d, Y') ?? '—' }}</span>
            </div>
            <div class="rem-brow">
                <span class="rem-brow-lbl">Driver</span>
                <span class="rem-brow-val" style="font-family:'Sora',sans-serif;">{{ $remittance->driver->name ?? '—' }}</span>
            </div>
            <div class="rem-brow">
                <span class="rem-brow-lbl">PAO</span>
                <span class="rem-brow-val" style="font-family:'Sora',sans-serif;">{{ $remittance->pao->name ?? '—' }}</span>
            </div>
            <div class="rem-brow">
                <span class="rem-brow-lbl">Vehicle</span>
                <span class="rem-brow-val">{{ $remittance->vehicle->plate_number ?? '—' }}</span>
            </div>
            <div class="rem-brow">
                <span class="rem-brow-lbl">Route</span>
                <span class="rem-brow-val" style="font-family:'Sora',sans-serif;">{{ $remittance->route->route_name ?? '—' }}</span>
            </div>
            <div class="rem-brow">
                <span class="rem-brow-lbl">Status</span>
                <span class="rem-brow-val"><span class="prl-status {{ $rsc }}">{{ $rlabel }}</span></span>
            </div>
        </div>
    </div>

    {{-- Financial Summary Card --}}
    <div class="rem-card">
        <div class="rem-card-head">
            <div class="rem-card-icon green">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <span class="rem-card-title">Financial Summary</span>
        </div>
        <div class="rem-card-body">
            <div class="rem-brow">
                <span class="rem-brow-lbl">Total Collection</span>
                <span class="rem-brow-val green">₱{{ number_format($remittance->total_collection, 2) }}</span>
            </div>
            <div class="rem-brow">
                <span class="rem-brow-lbl">Total Expenses</span>
                <span class="rem-brow-val red">₱{{ number_format($remittance->total_expenses, 2) }}</span>
            </div>
            <div class="rem-brow">
                <span class="rem-brow-lbl">Net Remittance</span>
                <span class="rem-brow-val">₱{{ number_format($remittance->net_remittance, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- Net Box --}}
    <div class="rem-net">
        <span class="rem-net-lbl">Net Remittance</span>
        <span class="rem-net-val">₱{{ number_format($remittance->net_remittance, 2) }}</span>
    </div>

    {{-- Footer --}}
    <div class="rem-footer">
        <a href="{{ route('remittances.edit', $remittance) }}" class="rem-btn-edit">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Edit Remittance
        </a>
        <form action="{{ route('remittances.destroy', $remittance) }}" method="POST" class="d-inline"
            data-sa-confirm="Delete this remittance?">
            @csrf @method('DELETE')
            <button type="submit" class="rem-btn-delete">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Delete
            </button>
        </form>
    </div>

</div>{{-- rem-wrap --}}
</div>{{-- remui-page --}}
</div>{{-- col-12 --}}
@endsection
