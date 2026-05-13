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
        .prl-stat { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; padding: 18px 20px; display: flex; align-items: flex-start; gap: 14px; position: relative; overflow: hidden; }
        .prl-stat::after { content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 3px; border-radius: 0 0 14px 14px; }
        .prl-stat.s-blue::after  { background: #0284c7; }
        .prl-stat.s-green::after { background: #16a34a; }
        .prl-stat.s-amber::after { background: #d97706; }
        .prl-stat.s-red::after   { background: #c8292a; }
        .prl-stat-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .prl-stat.s-blue  .prl-stat-icon { background: #f0f9ff; color: #0284c7; }
        .prl-stat.s-green .prl-stat-icon { background: #f0fdf4; color: #16a34a; }
        .prl-stat.s-amber .prl-stat-icon { background: #fffbeb; color: #d97706; }
        .prl-stat.s-red   .prl-stat-icon { background: #fff0f0; color: #c8292a; }
        .prl-stat-label { font-size: 0.67rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.09em; color: #9ca3af; margin-bottom: 4px; }
        .prl-stat-value { font-size: 1.15rem; font-weight: 800; color: #111827; line-height: 1.2; font-variant-numeric: tabular-nums; font-family: 'DM Mono', monospace; }
        .prl-stat-sub   { font-size: 0.73rem; color: #9ca3af; margin-top: 4px; }

        /* ── Batch info bar ── */
        .prl-batch-info {
            background: #fff; border: 1px solid #e5e7eb; border-radius: 12px;
            padding: 14px 18px; margin-bottom: 20px;
            display: flex; flex-wrap: wrap; gap: 20px; align-items: center;
        }
        .prl-batch-info-item { display: flex; flex-direction: column; gap: 2px; }
        .prl-batch-info-label { font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #9ca3af; }
        .prl-batch-info-value { font-size: 0.84rem; font-weight: 600; color: #111827; }

        /* ── Action buttons ── */
        .prl-batch-btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 9px 16px; border-radius: 9px; font-size: 0.82rem; font-weight: 700;
            font-family: 'Sora', sans-serif; border: 1px solid transparent; transition: all .15s ease; cursor: pointer;
        }
        .prl-batch-btn-danger  { background: #fff; border-color: #fecaca; color: #c8292a; }
        .prl-batch-btn-danger:hover  { background: #fff1f2; border-color: #fda4af; color: #a81f20; }
        .prl-batch-btn-primary { background: #c8292a; color: #fff; border-color: #c8292a; box-shadow: 0 4px 18px rgba(200,41,42,.28); }
        .prl-batch-btn-primary:hover { background: #a81f20; border-color: #a81f20; color: #fff; }

        /* ── Employee row avatar ── */
        .emp-av {
            width: 36px; height: 36px; border-radius: 10px; background: #f4f4f6; color: #6b7280;
            font-size: 0.7rem; font-weight: 800; display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; border: 1px solid #ececec; text-transform: uppercase;
        }

        /* ── Rejection note banner ── */
        .prl-rejection-note {
            background: #fff1f2; border: 1px solid #fecaca; border-radius: 10px;
            padding: 12px 16px; margin-bottom: 18px; display: flex; gap: 10px; align-items: flex-start;
        }
        .prl-rejection-note i { color: #c8292a; flex-shrink: 0; margin-top: 1px; }
        .prl-rejection-note-body { font-size: 0.84rem; color: #7f1d1d; }
        .prl-rejection-note-body strong { display: block; font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.07em; color: #c8292a; margin-bottom: 3px; }
    </style>
@endpush

@section('content')
@php
    $status     = $batch->status;
    $startDate  = $batch->period_start;
    $endDate    = $batch->period_end;
    $isFirst    = $startDate->format('d') <= 15;

    $statusLabel = match($status) {
        'submitted' => 'Pending approval',
        'approved'  => 'Approved',
        'rejected'  => 'Rejected',
        default     => ucfirst($status),
    };
    $statusBg    = match($status) {
        'submitted' => '#fffbeb', 'approved' => '#f0fdf4', 'rejected' => '#fff1f2', default => '#f3f4f6',
    };
    $statusColor = match($status) {
        'submitted' => '#d97706', 'approved' => '#16a34a', 'rejected' => '#c8292a', default => '#6b7280',
    };
@endphp

<div class="col-12">
    <div class="remui-page prl-page">
        <div class="remui-backdrop"><div class="remui-grid"></div></div>

        {{-- Hero ──────────────────────────────────────────────────── --}}
        <div class="remui-hero mb-3">
            <div>
                <h5 class="remui-title">
                    {{ $startDate->format('F Y') }} — {{ $isFirst ? '1st' : '2nd' }} half payroll
                </h5>
                <p class="remui-subtitle mb-0">
                    {{ $startDate->format('F d, Y') }} – {{ $endDate->format('F d, Y') }}
                    <span class="badge rounded-pill ms-2"
                          style="background:{{ $statusBg }};color:{{ $statusColor }};font-size:0.65rem;">
                        {{ $statusLabel }}
                    </span>
                </p>
            </div>
            <a href="{{ route('payroll-approval.index') }}" class="emp-action-btn emp-action-view">
                <i class="feather-arrow-left"></i><span>Back</span>
            </a>
        </div>

        {{-- Flash ─────────────────────────────────────────────────── --}}
        @if (session('success'))
            <div class="acd-flash success"><i class="feather-check-circle"></i> {{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="acd-flash error"><i class="feather-alert-circle"></i> {{ session('error') }}</div>
        @endif

        {{-- Rejection note ─────────────────────────────────────────── --}}
        @if($status === 'rejected' && $batch->rejection_note)
            <div class="prl-rejection-note">
                <i class="feather-alert-circle"></i>
                <div class="prl-rejection-note-body">
                    <strong>Rejection reason</strong>
                    {{ $batch->rejection_note }}
                </div>
            </div>
        @endif

        {{-- Stats ─────────────────────────────────────────────────── --}}
        <div class="prl-stats">
            <div class="prl-stat s-blue">
                <div class="prl-stat-icon"><i class="feather-users"></i></div>
                <div>
                    <div class="prl-stat-label">Employees</div>
                    <div class="prl-stat-value">{{ $payrolls->count() }}</div>
                    <div class="prl-stat-sub">In this batch</div>
                </div>
            </div>
            <div class="prl-stat s-green">
                <div class="prl-stat-icon"><i class="feather-dollar-sign"></i></div>
                <div>
                    <div class="prl-stat-label">Total gross</div>
                    <div class="prl-stat-value" style="font-size:1rem;">₱{{ number_format($totalGross, 2) }}</div>
                    <div class="prl-stat-sub">Combined</div>
                </div>
            </div>
            <div class="prl-stat s-amber">
                <div class="prl-stat-icon"><i class="feather-minus-circle"></i></div>
                <div>
                    <div class="prl-stat-label">Total deductions</div>
                    <div class="prl-stat-value" style="font-size:1rem;">₱{{ number_format($totalDeductions, 2) }}</div>
                    <div class="prl-stat-sub">Combined</div>
                </div>
            </div>
            <div class="prl-stat s-red">
                <div class="prl-stat-icon"><i class="feather-trending-up"></i></div>
                <div>
                    <div class="prl-stat-label">Total net pay</div>
                    <div class="prl-stat-value" style="font-size:1rem;">₱{{ number_format($totalNet, 2) }}</div>
                    <div class="prl-stat-sub">Take-home</div>
                </div>
            </div>
        </div>

        {{-- Batch meta info ────────────────────────────────────────── --}}
        <div class="prl-batch-info">
            <div class="prl-batch-info-item">
                <span class="prl-batch-info-label">Batch ID</span>
                <span class="prl-batch-info-value font-monospace">#{{ str_pad($batch->id, 3, '0', STR_PAD_LEFT) }}</span>
            </div>
            @if($batch->generatedBy)
            <div class="prl-batch-info-item">
                <span class="prl-batch-info-label">Generated by</span>
                <span class="prl-batch-info-value">{{ $batch->generatedBy->first_name }} {{ $batch->generatedBy->last_name }}</span>
            </div>
            @endif
            @if($batch->finalizedBy)
            <div class="prl-batch-info-item">
                <span class="prl-batch-info-label">Submitted by</span>
                <span class="prl-batch-info-value">{{ $batch->finalizedBy->first_name }} {{ $batch->finalizedBy->last_name }}</span>
            </div>
            @endif
            @if($batch->finalized_at)
            <div class="prl-batch-info-item">
                <span class="prl-batch-info-label">Submitted on</span>
                <span class="prl-batch-info-value">{{ $batch->finalized_at->format('M d, Y g:i A') }}</span>
            </div>
            @endif
            @if($batch->approved_at)
            <div class="prl-batch-info-item">
                <span class="prl-batch-info-label">Approved on</span>
                <span class="prl-batch-info-value" style="color:#16a34a;">{{ $batch->approved_at->format('M d, Y g:i A') }}</span>
            </div>
            @endif
            @if($batch->rejected_at)
            <div class="prl-batch-info-item">
                <span class="prl-batch-info-label">Rejected on</span>
                <span class="prl-batch-info-value" style="color:#c8292a;">{{ $batch->rejected_at->format('M d, Y g:i A') }}</span>
            </div>
            @endif
        </div>

        {{-- Employee table ─────────────────────────────────────────── --}}
        <div class="card remui-card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span class="card-title mb-0">
                    <i class="feather-users me-2" style="color:#0284c7;"></i>Employees in this batch
                </span>
                <span class="text-muted small">{{ $payrolls->count() }} record(s)</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover w-100 mb-0 remui-table">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Position</th>
                                <th class="text-center">Days</th>
                                <th class="text-end">Basic pay</th>
                                <th class="text-end">Gross pay</th>
                                <th class="text-end">Deductions</th>
                                <th class="text-end">Net pay</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payrolls as $payroll)
                                @php
                                    $user     = $payroll->user;
                                    $initials = strtoupper(substr($user->first_name ?? '', 0, 1) . substr($user->last_name ?? '', 0, 1));
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="emp-av">{{ $initials }}</div>
                                            <div>
                                                <div class="fw-bold" style="font-size:0.88rem;color:#111827;">
                                                    {{ $user->first_name }} {{ $user->last_name }}
                                                </div>
                                                <div class="text-muted" style="font-size:0.74rem;">
                                                    {{ $user->department ?? '' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted" style="font-size:0.82rem;">
                                            {{ $user->position ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="text-center font-monospace" style="font-size:0.85rem;">
                                        {{ $payroll->days_worked ?? '—' }}
                                    </td>
                                    <td class="text-end font-monospace" style="font-size:0.85rem;">
                                        ₱{{ number_format($payroll->basic_salary, 2) }}
                                    </td>
                                    <td class="text-end font-monospace" style="font-size:0.85rem;">
                                        ₱{{ number_format($payroll->gross_pay, 2) }}
                                    </td>
                                    <td class="text-end font-monospace text-muted" style="font-size:0.85rem;">
                                        ₱{{ number_format($payroll->total_deductions, 2) }}
                                    </td>
                                    <td class="text-end font-monospace fw-bold" style="font-size:0.88rem;color:#15803d;">
                                        ₱{{ number_format($payroll->net_pay, 2) }}
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('payroll-approval.show', $payroll) }}"
                                           class="emp-action-btn emp-action-view" style="padding:6px 12px;"
                                           title="View payroll details">
                                            <i class="feather-eye"></i>
                                            <span>View</span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-5">
                                        <i class="feather-inbox d-block mb-2" style="font-size:28px;opacity:.3;"></i>
                                        No payroll records in this batch.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($payrolls->isNotEmpty())
                        <tfoot>
                            <tr style="background:#f9fafb;font-size:0.85rem;">
                                <td colspan="4" class="fw-bold" style="padding:12px 16px;color:#111827;">Totals</td>
                                <td class="text-end font-monospace fw-bold" style="padding:12px 16px;">
                                    ₱{{ number_format($totalGross, 2) }}
                                </td>
                                <td class="text-end font-monospace text-muted" style="padding:12px 16px;">
                                    ₱{{ number_format($totalDeductions, 2) }}
                                </td>
                                <td class="text-end font-monospace fw-bold" style="padding:12px 16px;color:#15803d;">
                                    ₱{{ number_format($totalNet, 2) }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>

            {{-- Approve / Reject footer (only for submitted batches) ── --}}
            @if($status === 'submitted' && $payrolls->isNotEmpty())
                <div class="card-footer bg-white border-top" style="padding:16px 18px;">
                    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center">
                        <p class="text-muted mb-0" style="font-size:0.8rem;">
                            <i class="feather-info me-1"></i>
                            Approving will lock all {{ $payrolls->count() }} payroll record(s) and notify HR.
                        </p>
                        <div class="d-flex gap-2">
                            <button type="button" class="prl-batch-btn prl-batch-btn-danger"
                                    onclick="openRejectModal({{ $batch->id }}, {{ $payrolls->count() }})">
                                <i class="feather-x-circle"></i> Reject batch
                            </button>
                            <form action="{{ route('payroll-approval.approve-batch') }}" method="POST" class="d-inline"
                                  data-sa-confirm="Approve this entire payroll batch? This will approve {{ $payrolls->count() }} payroll record(s).">
                                @csrf
                                <input type="hidden" name="batch_id" value="{{ $batch->id }}">
                                <button type="submit" class="prl-batch-btn prl-batch-btn-primary">
                                    <i class="feather-check-circle"></i> Approve batch
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection

{{-- ── Reject Modal ─────────────────────────────────────────────── --}}
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true" style="font-family:'Sora',sans-serif;">
    <div class="modal-dialog modal-dialog-centered" style="max-width:440px;">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 20px 60px rgba(0,0,0,.15);">
            <form id="rejectForm" action="{{ route('payroll-approval.reject-batch') }}" method="POST">
                @csrf
                <input type="hidden" id="rejectBatchId" name="batch_id">
                <div class="modal-header" style="border-bottom:1px solid #f3f4f6;padding:18px 22px;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:36px;height:36px;border-radius:10px;background:#fff1f2;border:1px solid #fecaca;display:flex;align-items:center;justify-content:center;color:#c8292a;flex-shrink:0;">
                            <i class="feather-x-circle" style="font-size:16px;"></i>
                        </div>
                        <div>
                            <h6 class="modal-title" style="font-size:0.92rem;font-weight:800;color:#111827;margin:0;">Reject payroll batch</h6>
                            <p id="rejectDesc" style="font-size:0.78rem;color:#9ca3af;margin:0;"></p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:20px 22px;">
                    <label style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#9ca3af;display:block;margin-bottom:6px;">
                        Rejection reason <span style="color:#c8292a;">*</span>
                    </label>
                    <textarea name="rejection_note" id="rejectNote" class="form-control" rows="3" required minlength="3"
                              placeholder="Explain why this batch is being rejected…"
                              style="border:1px solid #e5e7eb;border-radius:8px;font-size:0.845rem;font-family:'Sora',sans-serif;color:#111827;padding:9px 12px;resize:vertical;"></textarea>
                    <p style="font-size:0.75rem;color:#9ca3af;margin:6px 0 0;">HR will be notified and can reopen the batch for corrections.</p>
                </div>
                <div class="modal-footer" style="border-top:1px solid #f3f4f6;padding:14px 22px;gap:8px;">
                    <button type="button" data-bs-dismiss="modal"
                            style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:9px;font-family:'Sora',sans-serif;font-size:0.82rem;font-weight:700;border:1px solid #e5e7eb;background:#f3f4f6;color:#374151;cursor:pointer;">
                        Cancel
                    </button>
                    <button type="submit"
                            style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:9px;font-family:'Sora',sans-serif;font-size:0.82rem;font-weight:700;border:none;background:#c8292a;color:#fff;box-shadow:0 2px 8px rgba(200,41,42,.3);cursor:pointer;">
                        <i class="feather-x-circle"></i> Reject batch
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openRejectModal(batchId, count) {
    document.getElementById('rejectBatchId').value = batchId;
    document.getElementById('rejectDesc').textContent = count + ' payroll record(s) will be rejected.';
    document.getElementById('rejectNote').value = '';
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}
</script>
@endpush
