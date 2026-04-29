@extends('layouts.layout')

@push('styles')
    @include('accountant._ui-styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
        .prl-page { font-family: 'Sora', sans-serif; }
        .pp-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:16px 18px; }
        .pp-item { display:flex;align-items:center;justify-content:space-between;gap:12px;padding:12px 0;border-bottom:1px solid #f3f4f6;text-decoration:none;color:inherit; }
        .pp-item:last-child { border-bottom:0; }
        .pp-title { font-weight:800;color:#111827;margin:0;font-size:0.95rem; }
        .pp-sub { color:#6b7280;font-size:0.8rem;margin:2px 0 0; }
        .pp-mono { font-family:'DM Mono',monospace;font-variant-numeric:tabular-nums; }
        .pp-badge { display:inline-flex;align-items:center;gap:6px;padding:5px 10px;border-radius:999px;font-size:0.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.06em;border:1px solid #e5e7eb; }
        .pp-badge.approved { background:#eff6ff;color:#2563eb;border-color:#bfdbfe; }
        .pp-badge.paid { background:#f0fdf4;color:#15803d;border-color:#bbf7d0; }
    </style>
@endpush

@section('content')
<div class="col-12">
    <div class="remui-page prl-page">
        <div class="remui-backdrop"><div class="remui-grid"></div></div>

        <div class="remui-hero mb-3">
            <div>
                <h5 class="remui-title">Payroll payments</h5>
                <p class="remui-subtitle mb-0">Mark employees as paid per approved batch.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('payroll-approval.index') }}" class="emp-action-btn emp-action-view">
                    <i class="feather-check-circle"></i><span>Approvals</span>
                </a>
                <a href="{{ route('accountant.index') }}" class="emp-action-btn emp-action-view">
                    <i class="feather-home"></i><span>Dashboard</span>
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="acd-flash success"><i class="feather-check-circle"></i> {{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="acd-flash error"><i class="feather-alert-circle"></i> {{ session('error') }}</div>
        @endif

        <div class="pp-card">
            @forelse($batches as $b)
                @php
                    $paidCount = $b->payrolls->where('status','paid')->count();
                    $totalCount = $b->payrolls->count();
                @endphp
                <a class="pp-item" href="{{ route('payroll-payments.show', $b) }}">
                    <div>
                        <p class="pp-title">{{ $b->period_start->format('F Y') }}</p>
                        <p class="pp-sub">
                            Period {{ $b->period_start->format('M d') }} – {{ $b->period_end->format('M d, Y') }}
                            · <span class="pp-mono">{{ $paidCount }}/{{ $totalCount }}</span> paid
                            · Total net <span class="pp-mono">₱{{ number_format($b->total_net_pay, 2) }}</span>
                        </p>
                    </div>
                    <span class="pp-badge {{ $b->status }}">
                        {{ $b->status === 'paid' ? 'Paid' : 'Approved' }}
                    </span>
                </a>
            @empty
                <div class="text-center text-muted py-5">
                    <i class="feather-inbox d-block mb-2" style="font-size:30px;opacity:.25;"></i>
                    No approved batches available for payment marking.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

