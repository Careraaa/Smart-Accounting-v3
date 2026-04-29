@extends('layouts.layout')

@push('styles')
    @include('accountant._ui-styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
        .prl-page { font-family: 'Sora', sans-serif; }
        .pp-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden; }
        .pp-head { padding:16px 18px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap; }
        .pp-title { margin:0;font-weight:900;color:#111827;font-size:1rem; }
        .pp-sub { margin:3px 0 0;color:#6b7280;font-size:0.8rem; }
        .pp-mono { font-family:'DM Mono',monospace;font-variant-numeric:tabular-nums; }
        .pp-table { width:100%;border-collapse:collapse;font-size:0.85rem; }
        .pp-table th { background:#f8f9fb;border-bottom:1px solid #e5e7eb;padding:11px 16px;font-size:0.68rem;font-weight:800;text-transform:uppercase;letter-spacing:.09em;color:#6b7280; }
        .pp-table td { padding:12px 16px;border-bottom:1px solid #f3f4f6;vertical-align:middle; }
        .pp-btn { display:inline-flex;align-items:center;gap:6px;padding:7px 12px;border-radius:9px;font-size:0.78rem;font-weight:800;border:1px solid transparent; }
        .pp-btn.pay { background:#16a34a;color:#fff;border-color:#16a34a; }
        .pp-btn.unpay { background:#fff;color:#c8292a;border-color:#fecaca; }
        .pp-status { display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:999px;font-size:0.68rem;font-weight:900;text-transform:uppercase;letter-spacing:.06em; }
        .pp-status.paid { background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0; }
        .pp-status.unpaid { background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe; }
    </style>
@endpush

@section('content')
<div class="col-12">
    <div class="remui-page prl-page">
        <div class="remui-backdrop"><div class="remui-grid"></div></div>

        <div class="remui-hero mb-3">
            <div>
                <h5 class="remui-title">Batch payments</h5>
                <p class="remui-subtitle mb-0">
                    {{ $batch->period_start->format('F d, Y') }} – {{ $batch->period_end->format('F d, Y') }}
                </p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('payroll-payments.index') }}" class="emp-action-btn emp-action-view">
                    <i class="feather-arrow-left"></i><span>Back</span>
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="acd-flash success"><i class="feather-check-circle"></i> {{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="acd-flash error"><i class="feather-alert-circle"></i> {{ session('error') }}</div>
        @endif

        @php
            $paidCount = $batch->payrolls->where('status','paid')->count();
            $totalCount = $batch->payrolls->count();
        @endphp

        <div class="pp-card">
            <div class="pp-head">
                <div>
                    <p class="pp-title">Employees</p>
                    <p class="pp-sub">
                        Paid <span class="pp-mono">{{ $paidCount }}/{{ $totalCount }}</span>
                        · Total net <span class="pp-mono">₱{{ number_format($batch->total_net_pay, 2) }}</span>
                    </p>
                </div>
                <div class="text-muted small">
                    Batch status: <strong>{{ ucfirst($batch->status) }}</strong>
                </div>
            </div>
            <div class="table-responsive">
                <table class="pp-table">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th class="text-end">Net pay</th>
                            <th class="text-center">Payment</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($batch->payrolls as $p)
                            <tr>
                                <td>
                                    <strong>{{ $p->user->first_name }} {{ $p->user->last_name }}</strong>
                                    <div class="text-muted small">{{ $p->user->position ?? 'N/A' }}</div>
                                </td>
                                <td class="text-end pp-mono fw-bold">₱{{ number_format($p->net_pay, 2) }}</td>
                                <td class="text-center">
                                    @if($p->status === 'paid')
                                        <span class="pp-status paid">Paid</span>
                                    @else
                                        <span class="pp-status unpaid">Unpaid</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($p->status === 'paid')
                                        <form action="{{ route('payroll-payments.mark-unpaid', $p) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="pp-btn unpay" data-sa-confirm="Set this employee back to unpaid?">
                                                <i class="feather-rotate-ccw"></i> Unpay
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('payroll-payments.mark-paid', $p) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="pp-btn pay" data-sa-confirm="Mark this employee as paid?">
                                                <i class="feather-check"></i> Mark paid
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">No payroll rows in this batch.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

