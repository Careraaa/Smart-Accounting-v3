@extends('layouts.layout')

@push('styles')
    @include('remittance-clerk._ui-styles')
@endpush

@section('content')
<div class="col-12">
<div class="remui-page">
<div class="rem-wrap">

    {{-- Back --}}
    <div style="margin-bottom:12px;">
        <a href="{{ route('paos.index') }}" class="rem-btn-edit">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back
        </a>
    </div>

    {{-- Hero --}}
    <div class="rem-hero">
        <div class="rem-hero-left">
            <p class="rem-hero-sub">PAO / Conductor profile</p>
            <h1 class="rem-hero-title">{{ $pao->name }}</h1>
        </div>
        <div class="rem-hero-right">
            <div class="rem-hero-chips">
                <div class="rem-hero-chip">
                    <span class="rem-hero-chip-lbl">Date of Hire</span>
                    <span class="rem-hero-chip-val">{{ $pao->date_of_hire?->format('M d, Y') ?? '—' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- PAO Information Card --}}
    <div class="rem-card">
        <div class="rem-card-head">
            <div class="rem-card-icon blue">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <span class="rem-card-title">PAO Information</span>
        </div>
        <div class="rem-card-body">
            <div class="rem-brow">
                <span class="rem-brow-lbl">Full Name</span>
                <span class="rem-brow-val">{{ $pao->name ?? '—' }}</span>
            </div>
            <div class="rem-brow">
                <span class="rem-brow-lbl">Contact Number</span>
                <span class="rem-brow-val">{{ $pao->contact_number ?? '—' }}</span>
            </div>
            <div class="rem-brow">
                <span class="rem-brow-lbl">Email</span>
                <span class="rem-brow-val">{{ $pao->email ?? '—' }}</span>
            </div>
            <div class="rem-brow">
                <span class="rem-brow-lbl">Gender</span>
                <span class="rem-brow-val">{{ ucfirst(str_replace('_', ' ', $pao->gender ?? '—')) }}</span>
            </div>
            <div class="rem-brow">
                <span class="rem-brow-lbl">Date of Hire</span>
                <span class="rem-brow-val">{{ $pao->date_of_hire?->format('F d, Y') ?? '—' }}</span>
            </div>
            @if($pao->address)
            <div class="rem-brow">
                <span class="rem-brow-lbl">Address</span>
                <span class="rem-brow-val" style="font-family:'Sora',sans-serif;text-align:right;max-width:60%;">{{ $pao->address }}</span>
            </div>
            @endif
            <div class="rem-brow">
                <span class="rem-brow-lbl">Status</span>
                <span class="rem-brow-val">
                    @php $sc = match($pao->status) { 'active' => 's-active', 'pending' => 's-pending', default => 's-inactive' }; @endphp
                    <span class="prl-status {{ $sc }}">{{ ucfirst($pao->status) }}</span>
                </span>
            </div>

            {{-- Footer --}}
            <div class="rem-footer">
                <a href="{{ route('paos.edit', $pao) }}" class="rem-btn-edit">
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit PAO
                </a>
                <form action="{{ route('paos.destroy', $pao) }}" method="POST" class="d-inline"
                    data-sa-confirm="Are you sure you want to delete this PAO?">
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
