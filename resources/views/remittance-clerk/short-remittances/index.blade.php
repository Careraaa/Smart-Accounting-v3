@extends('layouts.layout')

@push('styles')
    @include('remittance-clerk._ui-styles')
@endpush

@section('content')
<div class="col-12">
<div class="remui-page">

    {{-- Flash --}}
    @foreach(['success','error','info'] as $t)
        @if(session($t))
        <div class="prl-flash {{ $t }}">
            @if($t==='success')<svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            @else<svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>@endif
            {{ session($t) }}
        </div>
        @endif
    @endforeach

    {{-- Topbar --}}
    <div class="prl-topbar">
        <div>
            <h1 class="prl-topbar-title">Short Remittances</h1>
            <p class="prl-topbar-sub">Track shortages and resolve liabilities for driver and PAO</p>
        </div>
    </div>

    {{-- Stats --}}
    <div class="prl-stats">
        <div class="prl-stat s-red">
            <div class="prl-stat-icon"><svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg></div>
            <div>
                <div class="prl-stat-label">Pending</div>
                <div class="prl-stat-value">{{ $pendingRemittances->count() }}</div>
                <div class="prl-stat-sub">pending resolution</div>
            </div>
        </div>
        <div class="prl-stat s-amber">
            <div class="prl-stat-icon"><svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg></div>
            <div>
                <div class="prl-stat-label">Total Short Amount</div>
                <div class="prl-stat-value" style="font-size:1.1rem;">₱{{ number_format($pendingRemittances->sum('short_amount'), 0) }}</div>
                <div class="prl-stat-sub">total shortage</div>
            </div>
        </div>
        <div class="prl-stat s-blue">
            <div class="prl-stat-icon"><svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
            <div>
                <div class="prl-stat-label">Driver Shares</div>
                <div class="prl-stat-value" style="font-size:1.1rem;">₱{{ number_format($pendingRemittances->sum('driver_share'), 0) }}</div>
                <div class="prl-stat-sub">total driver liability</div>
            </div>
        </div>
        <div class="prl-stat s-green">
            <div class="prl-stat-icon"><svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
            <div>
                <div class="prl-stat-label">PAO Shares</div>
                <div class="prl-stat-value" style="font-size:1.1rem;">₱{{ number_format($pendingRemittances->sum('pao_share'), 0) }}</div>
                <div class="prl-stat-sub">total PAO liability</div>
            </div>
        </div>
    </div>

    {{-- Pending Section --}}
    <div style="font-size:0.72rem;font-weight:900;text-transform:uppercase;letter-spacing:0.09em;color:#9ca3af;margin-bottom:10px;display:flex;align-items:center;gap:8px;">
        <span style="width:8px;height:8px;border-radius:50%;background:#c8292a;display:inline-block;"></span>
        Pending Resolution
    </div>
    <div class="prl-table-card" style="margin-bottom:24px;">
        <div class="prl-table-scroll">
            <table class="prl-table">
                <thead>
                    <tr>
                        <th>
                            <a href="{{ route('short-remittances.index', ['sort_by' => 'remittance_date', 'sort_order' => ($sortBy === 'remittance_date' && $sortOrder === 'asc') ? 'desc' : 'asc']) }}" class="sort-link">
                                Date @if($sortBy === 'remittance_date')<svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $sortOrder === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>@endif
                            </a>
                        </th>
                        <th>Driver</th>
                        <th>PAO</th>
                        <th>Vehicle</th>
                        <th class="text-end">Short Amount</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($pendingRemittances as $shortRemittance)
                    @php
                        $driverPartial = $shortRemittance->driver_status === 'partial';
                        $paoPartial    = $shortRemittance->pao_status === 'partial';
                        $sc = ($driverPartial || $paoPartial) ? 's-partial' : 's-pending';
                        $label = ($driverPartial || $paoPartial) ? 'Partial' : 'Pending';
                    @endphp
                    <tr class="clickable" onclick="window.location='{{ route('short-remittances.show', $shortRemittance) }}'">
                        <td class="prl-mono muted">{{ $shortRemittance->remittance_date?->format('M d, Y') }}</td>
                        <td><strong>{{ $shortRemittance->driver->name ?? 'N/A' }}</strong></td>
                        <td><strong>{{ $shortRemittance->pao->name ?? 'N/A' }}</strong></td>
                        <td class="prl-mono">{{ $shortRemittance->vehicle->plate_number }}</td>
                        <td class="text-end"><span class="prl-mono red">₱{{ number_format($shortRemittance->short_amount, 2) }}</span></td>
                        <td class="text-center"><span class="prl-status {{ $sc }}">{{ $label }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="6">
                        <div class="prl-empty" style="padding:32px;">
                            <div class="prl-empty-icon"><svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                            <p class="prl-empty-title">No pending short remittances</p>
                        </div>
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Resolved Section --}}
    <div style="font-size:0.72rem;font-weight:900;text-transform:uppercase;letter-spacing:0.09em;color:#9ca3af;margin-bottom:10px;display:flex;align-items:center;gap:8px;">
        <span style="width:8px;height:8px;border-radius:50%;background:#16a34a;display:inline-block;"></span>
        Fully Paid / Resolved
    </div>
    <div class="prl-table-card">
        <div class="prl-table-scroll">
            <table class="prl-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Driver</th>
                        <th>PAO</th>
                        <th>Vehicle</th>
                        <th class="text-end">Short Amount</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($fullyPaidRemittances as $shortRemittance)
                    <tr class="clickable" onclick="window.location='{{ route('short-remittances.show', $shortRemittance) }}'">
                        <td class="prl-mono muted">{{ $shortRemittance->remittance_date?->format('M d, Y') }}</td>
                        <td><strong>{{ $shortRemittance->driver->name ?? 'N/A' }}</strong></td>
                        <td><strong>{{ $shortRemittance->pao->name ?? 'N/A' }}</strong></td>
                        <td class="prl-mono">{{ $shortRemittance->vehicle->plate_number }}</td>
                        <td class="text-end"><span class="prl-mono green">₱{{ number_format($shortRemittance->short_amount, 2) }}</span></td>
                        <td class="text-center"><span class="prl-status s-approved">Fully Paid</span></td>
                    </tr>
                @empty
                    <tr><td colspan="6">
                        <div class="prl-empty" style="padding:32px;">
                            <div class="prl-empty-icon"><svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
                            <p class="prl-empty-title">No fully paid remittances</p>
                        </div>
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>{{-- col-12 --}}
@endsection
