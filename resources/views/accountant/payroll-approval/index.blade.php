@extends('layouts.layout')

@push('styles')
    @include('accountant._ui-styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
        .prl-page { font-family: 'Sora', sans-serif; }

        /* ── Stat grid ── */
        .prl-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 22px; }
        @media (max-width: 1100px) { .prl-stats { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 600px)  { .prl-stats { grid-template-columns: 1fr; } }
        .prl-stat { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; padding: 18px 20px; display: flex; align-items: flex-start; gap: 14px; position: relative; overflow: hidden; transition: box-shadow 0.15s; }
        .prl-stat:hover { box-shadow: 0 4px 20px rgba(0,0,0,.07); }
        .prl-stat::after { content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 3px; border-radius: 0 0 14px 14px; }
        .prl-stat.s-red::after   { background: #c8292a; }
        .prl-stat.s-green::after { background: #16a34a; }
        .prl-stat.s-amber::after { background: #d97706; }
        .prl-stat.s-blue::after  { background: #0284c7; }
        .prl-stat-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .prl-stat.s-red   .prl-stat-icon { background: #fff0f0; color: #c8292a; }
        .prl-stat.s-green .prl-stat-icon { background: #f0fdf4; color: #16a34a; }
        .prl-stat.s-amber .prl-stat-icon { background: #fffbeb; color: #d97706; }
        .prl-stat.s-blue  .prl-stat-icon { background: #f0f9ff; color: #0284c7; }
        .prl-stat-label { font-size: 0.67rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.09em; color: #9ca3af; margin-bottom: 4px; }
        .prl-stat-value { font-size: 1.35rem; font-weight: 800; color: #111827; line-height: 1; font-variant-numeric: tabular-nums; font-family: 'DM Mono', monospace; }
        .prl-stat-sub   { font-size: 0.73rem; color: #9ca3af; margin-top: 4px; }

        /* ── Section label ── */
        .prl-section-label {
            font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.09em;
            color: #9ca3af; margin: 0 0 10px; display: flex; align-items: center; gap: 8px;
        }
        .prl-section-label::after { content: ''; flex: 1; height: 1px; background: #f3f4f6; }
    </style>
@endpush

@section('content')
@php
    $allBatches      = collect($batchData);
    $submittedBatches= $allBatches->where('status', 'submitted');
    $historyBatches  = $allBatches->whereIn('status', ['approved', 'rejected']);

    $totalPending    = $pendingCount;
    $totalEmployees  = $submittedBatches->sum('count');
    $totalGrossAll   = $submittedBatches->sum('total_gross');
    $totalNetAll     = $submittedBatches->sum('total_net');
@endphp

<div class="col-12">
    <div class="remui-page prl-page">
        <div class="remui-backdrop"><div class="remui-grid"></div></div>

        {{-- Hero ──────────────────────────────────────────────────── --}}
        <div class="remui-hero mb-3">
            <div>
                <h5 class="remui-title">Payroll batch approval</h5>
                <p class="remui-subtitle mb-0">Review and approve payroll batches submitted by HR.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('accountant.index') }}" class="emp-action-btn emp-action-view">
                    <i class="feather-home"></i><span>Dashboard</span>
                </a>
            </div>
        </div>

        {{-- Flash ─────────────────────────────────────────────────── --}}
        @if (session('success'))
            <div class="acd-flash success"><i class="feather-check-circle"></i> {{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="acd-flash error"><i class="feather-alert-circle"></i> {{ session('error') }}</div>
        @endif

        {{-- Stats ─────────────────────────────────────────────────── --}}
        <div class="prl-stats">
            <div class="prl-stat s-red">
                <div class="prl-stat-icon"><i class="feather-inbox"></i></div>
                <div>
                    <div class="prl-stat-label">Pending approval</div>
                    <div class="prl-stat-value">{{ $totalPending }}</div>
                    <div class="prl-stat-sub">Awaiting your review</div>
                </div>
            </div>
            <div class="prl-stat s-blue">
                <div class="prl-stat-icon"><i class="feather-users"></i></div>
                <div>
                    <div class="prl-stat-label">Employees</div>
                    <div class="prl-stat-value">{{ $totalEmployees }}</div>
                    <div class="prl-stat-sub">In pending batches</div>
                </div>
            </div>
            <div class="prl-stat s-amber">
                <div class="prl-stat-icon"><i class="feather-dollar-sign"></i></div>
                <div>
                    <div class="prl-stat-label">Total gross</div>
                    <div class="prl-stat-value" style="font-size:1.05rem;">₱{{ number_format($totalGrossAll, 2) }}</div>
                    <div class="prl-stat-sub">Pending batches</div>
                </div>
            </div>
            <div class="prl-stat s-green">
                <div class="prl-stat-icon"><i class="feather-trending-up"></i></div>
                <div>
                    <div class="prl-stat-label">Total net</div>
                    <div class="prl-stat-value" style="font-size:1.05rem;">₱{{ number_format($totalNetAll, 2) }}</div>
                    <div class="prl-stat-sub">Pending batches</div>
                </div>
            </div>
        </div>

        {{-- Pending batches ────────────────────────────────────────── --}}
        <div class="card remui-card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span class="card-title mb-0">
                    <i class="feather-inbox me-2" style="color:#c8292a;"></i>Pending approval
                </span>
                <span class="text-muted small">Click a batch to review and approve</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @forelse($submittedBatches as $batch)
                        @php
                            $startDate = $batch['period_start'];
                            $endDate   = $batch['period_end'];
                            $isFirst   = $startDate->format('d') <= 15;
                            $variant   = $isFirst ? 'a' : 'b';
                        @endphp
                        <div class="col-12">
                            <a href="{{ route('payroll-approval.batch', $batch['batch_id']) }}"
                               class="text-decoration-none text-reset d-block">
                                <div class="acd-batch-card">
                                    <div class="acd-batch-inner">
                                        <div class="acd-batch-icon {{ $variant }}">
                                            <i class="feather-{{ $isFirst ? 'calendar' : 'check-circle' }}"></i>
                                        </div>
                                        <div class="flex-grow-1" style="min-width:200px;">
                                            <h6 class="mb-1 fw-bold" style="color:#111827;font-size:0.95rem;">
                                                {{ $startDate->format('F Y') }} — {{ $isFirst ? '1st' : '2nd' }} half
                                            </h6>
                                            <div class="text-muted small">
                                                {{ $startDate->format('M d') }} – {{ $endDate->format('M d, Y') }}
                                            </div>
                                        </div>
                                        <div class="d-flex flex-wrap gap-4 ms-auto justify-content-end align-items-center">
                                            <span style="display:inline-block;background:#fffbeb;color:#d97706;font-size:0.65rem;font-weight:700;padding:4px 10px;border-radius:20px;text-transform:uppercase;letter-spacing:0.06em;">
                                                Pending
                                            </span>
                                            <div class="acd-batch-stat">
                                                <small>Employees</small>
                                                <strong>{{ $batch['count'] }}</strong>
                                            </div>
                                            <div class="acd-batch-stat">
                                                <small>Gross</small>
                                                <strong>₱{{ number_format($batch['total_gross'], 2) }}</strong>
                                            </div>
                                            <div class="acd-batch-stat">
                                                <small>Net</small>
                                                <strong style="color:#16a34a;">₱{{ number_format($batch['total_net'], 2) }}</strong>
                                            </div>
                                            <div style="color:#d1d5db;font-size:1.1rem;">
                                                <i class="feather-chevron-right"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="acd-empty border rounded-3" style="background:#fafafa;">
                                <i class="feather-check-circle d-block mb-2" style="font-size:36px;opacity:.25;"></i>
                                No batches pending approval.
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- History ────────────────────────────────────────────────── --}}
        @if($historyBatches->isNotEmpty())
        <div class="card remui-card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span class="card-title mb-0">
                    <i class="feather-clock me-2" style="color:#9ca3af;"></i>History
                </span>
                <span class="text-muted small">Previously processed batches</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 remui-table">
                        <thead>
                            <tr>
                                <th>Period</th>
                                <th class="text-center">Employees</th>
                                <th class="text-end">Gross</th>
                                <th class="text-end">Net</th>
                                <th class="text-center">Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($historyBatches as $batch)
                                @php
                                    $startDate   = $batch['period_start'];
                                    $endDate     = $batch['period_end'];
                                    $batchStatus = $batch['status'];
                                    $isFirst     = $startDate->format('d') <= 15;
                                    $statusLabel = $batchStatus === 'approved' ? 'Approved' : 'Rejected';
                                    $statusBg    = $batchStatus === 'approved' ? '#f0fdf4' : '#fff1f2';
                                    $statusColor = $batchStatus === 'approved' ? '#16a34a' : '#c8292a';
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-bold" style="font-size:0.88rem;color:#111827;">
                                            {{ $startDate->format('F Y') }} — {{ $isFirst ? '1st' : '2nd' }} half
                                        </div>
                                        <div class="text-muted" style="font-size:0.75rem;">
                                            {{ $startDate->format('M d') }} – {{ $endDate->format('M d, Y') }}
                                        </div>
                                    </td>
                                    <td class="text-center">{{ $batch['count'] }}</td>
                                    <td class="text-end font-monospace" style="font-size:0.85rem;">₱{{ number_format($batch['total_gross'], 2) }}</td>
                                    <td class="text-end font-monospace fw-bold" style="font-size:0.85rem;color:#15803d;">₱{{ number_format($batch['total_net'], 2) }}</td>
                                    <td class="text-center">
                                        <span style="display:inline-block;background:{{ $statusBg }};color:{{ $statusColor }};font-size:0.65rem;font-weight:700;padding:4px 10px;border-radius:20px;text-transform:uppercase;letter-spacing:0.06em;">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('payroll-approval.batch', $batch['batch_id']) }}"
                                           class="emp-action-btn emp-action-view" style="padding:6px 10px;">
                                            <i class="feather-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
