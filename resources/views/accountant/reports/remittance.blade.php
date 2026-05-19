@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
.rr-page {
    font-family: 'Sora', sans-serif;
    padding-top: 12px;
    margin-top: 8px;
}

.rr-topbar {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 24px;
    flex-wrap: wrap;
}
.rr-topbar-title {
    font-size: 1.35rem;
    font-weight: 800;
    color: #111827;
    letter-spacing: -0.02em;
    margin: 0 0 2px;
}
.rr-topbar-sub {
    font-size: 0.78rem;
    color: #9ca3af;
    margin: 0;
    max-width: 520px;
    line-height: 1.45;
}
.rr-topbar-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}
.rr-btn-sec {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    background: #fff;
    color: #374151;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    font-family: 'Sora', sans-serif;
    font-size: 0.8rem;
    font-weight: 700;
    text-decoration: none;
    white-space: nowrap;
    transition: background 0.15s, border-color 0.15s, color 0.15s;
}
.rr-btn-sec:hover {
    border-color: #c8292a;
    color: #c8292a;
    background: #fff5f5;
}
.rr-btn-sec i { font-size: 15px; line-height: 1; }

.rr-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 22px;
}
@media (max-width: 1100px) { .rr-stats { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 560px)  { .rr-stats { grid-template-columns: 1fr; } }
.rr-stat {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 16px 18px;
    position: relative;
    overflow: hidden;
}
.rr-stat::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 3px;
    border-radius: 0 0 14px 14px;
}
.rr-stat.s-col::after { background: #16a34a; }
.rr-stat.s-exp::after { background: #c8292a; }
.rr-stat.s-net::after { background: #0284c7; }
.rr-stat.s-rec::after { background: #6b7280; }
.rr-stat-label {
    font-size: 0.67rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.09em;
    color: #9ca3af;
    margin-bottom: 6px;
}
.rr-stat-value {
    font-size: 1.25rem;
    font-weight: 800;
    color: #111827;
    font-family: 'DM Mono', monospace;
    line-height: 1.2;
    letter-spacing: -0.02em;
}
.rr-stat-sub {
    font-size: 0.72rem;
    color: #9ca3af;
    margin-top: 4px;
}

.rr-table-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    overflow: hidden;
}
.rr-table-head {
    padding: 14px 18px;
    border-bottom: 1px solid #f3f4f6;
}
.rr-table-title {
    margin: 0;
    font-size: 0.88rem;
    font-weight: 700;
    color: #111827;
    display: flex;
    align-items: center;
    gap: 8px;
}
.rr-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #c8292a;
    flex-shrink: 0;
}
.rr-table-scroll { overflow-x: auto; }
.rr-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.825rem;
}
.rr-table thead tr {
    background: #f8f9fb;
    border-bottom: 1px solid #e5e7eb;
}
.rr-table thead th {
    padding: 10px 14px;
    font-size: 0.66rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.09em;
    color: #6b7280;
    white-space: nowrap;
}
.rr-table tbody tr {
    border-bottom: 1px solid #f3f4f6;
    transition: background 0.1s;
}
.rr-table tbody tr:last-child { border-bottom: none; }
.rr-table tbody tr:hover { background: #fafafa; }
.rr-table tbody td {
    padding: 10px 14px;
    color: #374151;
    vertical-align: middle;
}
.rr-mono {
    font-family: 'DM Mono', monospace;
    font-size: 0.8rem;
    font-variant-numeric: tabular-nums;
}
.rr-mono.muted { color: #9ca3af; }
.rr-mono.net { color: #15803d; font-weight: 700; }
.rr-empty {
    text-align: center;
    padding: 48px 20px;
    color: #9ca3af;
    font-size: 0.84rem;
}
.rr-empty i {
    display: block;
    font-size: 28px;
    opacity: 0.3;
    margin-bottom: 8px;
}
.rr-chip {
    display: inline-block;
    font-size: 0.72rem;
    color: #6b7280;
    background: #f3f4f6;
    padding: 2px 8px;
    border-radius: 6px;
    margin-top: 8px;
}
</style>
@endpush

@section('content')
@php
    $sumCol = $remittances->sum('total_collection');
    $sumExp = $remittances->sum('total_expenses');
    $sumNet = $remittances->sum('net_remittance');
@endphp

<div class="rr-page">
    <div class="rr-topbar">
        <div>
            <h1 class="rr-topbar-title">Remittance Reports</h1>
            <p class="rr-topbar-sub">Approved daily remittances — collections, expenses, and net amounts across all routes.</p>
            <span class="rr-chip">{{ $remittances->count() }} approved {{ Str::plural('record', $remittances->count()) }}</span>
        </div>
        <div class="rr-topbar-actions">
            <a href="{{ route('remittance-approval.index') }}" class="rr-btn-sec">
                <i class="feather-check-square"></i> Remittance approvals
            </a>
        </div>
    </div>

    <div class="rr-stats">
        <div class="rr-stat s-col">
            <div class="rr-stat-label">Total collection</div>
            <div class="rr-stat-value">₱{{ number_format($sumCol, 2) }}</div>
            <div class="rr-stat-sub">Gross collections</div>
        </div>
        <div class="rr-stat s-exp">
            <div class="rr-stat-label">Total expenses</div>
            <div class="rr-stat-value">₱{{ number_format($sumExp, 2) }}</div>
            <div class="rr-stat-sub">Trip and operating costs</div>
        </div>
        <div class="rr-stat s-net">
            <div class="rr-stat-label">Net remittance</div>
            <div class="rr-stat-value">₱{{ number_format($sumNet, 2) }}</div>
            <div class="rr-stat-sub">Collection minus expenses</div>
        </div>
        <div class="rr-stat s-rec">
            <div class="rr-stat-label">Records</div>
            <div class="rr-stat-value">{{ $remittances->count() }}</div>
            <div class="rr-stat-sub">Approved remittances</div>
        </div>
    </div>

    <div class="rr-table-card">
        <div class="rr-table-head">
            <h2 class="rr-table-title"><span class="rr-dot"></span> All approved remittances</h2>
        </div>
        <div class="rr-table-scroll">
            <table class="rr-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Route</th>
                        <th class="text-end">Collection</th>
                        <th class="text-end">Expenses</th>
                        <th class="text-end">Net remittance</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($remittances as $remittance)
                        <tr>
                            <td>{{ $remittance->remittance_date?->format('M d, Y') ?? '—' }}</td>
                            <td><strong>{{ $remittance->route->route_name ?? '—' }}</strong></td>
                            <td class="text-end rr-mono">₱{{ number_format($remittance->total_collection, 2) }}</td>
                            <td class="text-end rr-mono muted">₱{{ number_format($remittance->total_expenses, 2) }}</td>
                            <td class="text-end rr-mono net">₱{{ number_format($remittance->net_remittance, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="rr-empty">
                                    <i class="feather-inbox"></i>
                                    No approved remittance records yet.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
