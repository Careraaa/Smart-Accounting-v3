@extends('layouts.layout')

@push('styles')
    @include('accountant._ui-styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
        .prl-page { font-family: 'Sora', sans-serif; }
        .prl-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 22px; }
        @media (max-width: 1100px) { .prl-stats { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 600px) { .prl-stats { grid-template-columns: 1fr; } }
        .prl-stat { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; padding: 18px 20px; display: flex; align-items: flex-start; gap: 14px; position: relative; overflow: hidden; }
        .prl-stat::after { content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 3px; border-radius: 0 0 14px 14px; }
        .prl-stat.s-blue::after { background: #0284c7; }
        .prl-stat.s-green::after { background: #16a34a; }
        .prl-stat.s-amber::after { background: #d97706; }
        .prl-stat.s-red::after { background: #c8292a; }
        .prl-stat-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .prl-stat.s-blue .prl-stat-icon { background: #f0f9ff; color: #0284c7; }
        .prl-stat.s-green .prl-stat-icon { background: #f0fdf4; color: #16a34a; }
        .prl-stat.s-amber .prl-stat-icon { background: #fffbeb; color: #d97706; }
        .prl-stat.s-red .prl-stat-icon { background: #fff0f0; color: #c8292a; }
        .prl-stat-label { font-size: 0.67rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.09em; color: #9ca3af; margin-bottom: 4px; }
        .prl-stat-value { font-size: 1.15rem; font-weight: 800; color: #111827; line-height: 1.2; font-variant-numeric: tabular-nums; font-family: 'DM Mono', monospace; }
        .prl-stat-sub { font-size: 0.73rem; color: #9ca3af; margin-top: 4px; }
        .prl-batch-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: 9px;
            font-size: 0.8rem;
            font-weight: 700;
            font-family: 'Sora', sans-serif;
            border: 1px solid transparent;
            transition: all .15s ease;
        }
        .prl-batch-btn-danger {
            background: #fff;
            border-color: #fecaca;
            color: #c8292a;
        }
        .prl-batch-btn-danger:hover {
            background: #fff1f2;
            border-color: #fda4af;
            color: #a81f20;
        }
        .prl-batch-btn-primary {
            background: #c8292a;
            color: #fff;
            border-color: #c8292a;
            box-shadow: 0 4px 18px rgba(200, 41, 42, 0.28);
        }
        .prl-batch-btn-primary:hover {
            background: #a81f20;
            border-color: #a81f20;
            color: #fff;
        }
    </style>
@endpush

@section('content')
@php
    $filterLabel = $status === 'submitted' ? 'Submitted payroll' : ucfirst($status) . ' payroll';
    $statusTag = $status === 'submitted' ? 'Submitted' : ucfirst($status);
    $tagBg = $status === 'submitted' ? '#fffbeb' : ($status === 'approved' ? '#f0f9ff' : '#fff1f2');
    $tagColor = $status === 'submitted' ? '#d97706' : ($status === 'approved' ? '#0284c7' : '#e11d48');
@endphp

<div class="col-12">
    <div class="remui-page prl-page">
        <div class="remui-backdrop">
            <div class="remui-grid"></div>
        </div>

        <div class="remui-hero mb-3">
            <div>
                <h5 class="remui-title">Approve payroll batch</h5>
                <p class="remui-subtitle mb-0">
                    {{ $startDate->format('F d, Y') }} – {{ $endDate->format('F d, Y') }}
                    <span class="badge rounded-pill ms-2" style="background:{{ $tagBg }};color:{{ $tagColor }};font-size:0.65rem;">{{ $statusTag }}</span>
                </p>
                @if(isset($batch) && $batch->status === 'rejected' && $batch->rejection_note)
                    <div class="text-muted small mt-1"><strong>Rejection note:</strong> {{ $batch->rejection_note }}</div>
                @endif
            </div>
            <a href="{{ route('payroll-approval.index') }}" class="emp-action-btn emp-action-view">
                <i class="feather-arrow-left"></i><span>Back</span>
            </a>
        </div>

        @if (session('success'))
            <div class="acd-flash success"><i class="feather-check-circle"></i> {{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="acd-flash error"><i class="feather-alert-circle"></i> {{ session('error') }}</div>
        @endif

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
                    <div class="prl-stat-value">₱{{ number_format($totalGross, 2) }}</div>
                    <div class="prl-stat-sub">Combined</div>
                </div>
            </div>
            <div class="prl-stat s-amber">
                <div class="prl-stat-icon"><i class="feather-minus-circle"></i></div>
                <div>
                    <div class="prl-stat-label">Total deductions</div>
                    <div class="prl-stat-value">₱{{ number_format($totalDeductions, 2) }}</div>
                    <div class="prl-stat-sub">From payroll rows</div>
                </div>
            </div>
            <div class="prl-stat s-red">
                <div class="prl-stat-icon"><i class="feather-check-circle"></i></div>
                <div>
                    <div class="prl-stat-label">Total net</div>
                    <div class="prl-stat-value">₱{{ number_format($totalNet, 2) }}</div>
                    <div class="prl-stat-sub">Net pay</div>
                </div>
            </div>
        </div>

        <div class="card remui-card mb-3">
            <div class="card-header">
                <span class="card-title mb-0">{{ $filterLabel }}</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover w-100 mb-0 remui-table">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Position</th>
                                <th class="text-end">Gross pay</th>
                                <th class="text-end">Deductions</th>
                                <th class="text-end">Net pay</th>
                                <th class="text-center">Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payrolls as $payroll)
                                <tr>
                                    <td><strong>{{ $payroll->user->first_name }} {{ $payroll->user->last_name }}</strong></td>
                                    <td><span class="text-muted" style="font-size:0.82rem;">{{ $payroll->user->position ?? 'N/A' }}</span></td>
                                    <td class="text-end font-monospace">₱{{ number_format($payroll->gross_pay, 2) }}</td>
                                    <td class="text-end font-monospace text-muted">₱{{ number_format($payroll->total_deductions ?? abs($payroll->gross_pay - $payroll->net_pay), 2) }}</td>
                                    <td class="text-end font-monospace fw-bold" style="color:#15803d;">₱{{ number_format($payroll->net_pay, 2) }}</td>
                                    <td class="text-center">
                                        <span style="display:inline-block;background:{{ $tagBg }};color:{{ $tagColor }};font-size:0.68rem;font-weight:700;padding:4px 10px;border-radius:20px;text-transform:uppercase;">
                                            {{ $statusTag }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('payroll-approval.show', $payroll) }}" class="emp-action-btn emp-action-view" style="padding:6px 10px;">
                                            <i class="feather-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">
                                        <i class="feather-inbox d-block mb-2" style="font-size:28px;opacity:.3;"></i>
                                        No payroll records in this batch
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($status === 'submitted' && $payrolls->isNotEmpty())
                <div class="card-footer bg-white border-top" style="padding:16px 18px;">
                    <div class="d-flex flex-wrap gap-2 justify-content-end">
                        {{-- Reject: opens modal for reason input --}}
                        <button type="button" class="prl-batch-btn prl-batch-btn-danger"
                                onclick="openBatchRejectModal('{{ $startDate->format('Y-m-d') }}', '{{ $endDate->format('Y-m-d') }}', {{ $payrolls->count() }})">
                            <i class="feather-x-circle me-1"></i> Reject batch
                        </button>
                        {{-- Approve: data-sa-confirm on the form --}}
                        <form action="{{ route('payroll-approval.approve-batch') }}" method="POST" class="d-inline"
                              data-sa-confirm="Approve this entire payroll batch? This will release {{ $payrolls->count() }} payroll record(s) for processing.">
                            @csrf
                            <input type="hidden" name="start" value="{{ $startDate->format('Y-m-d') }}">
                            <input type="hidden" name="end" value="{{ $endDate->format('Y-m-d') }}">
                            <button type="submit" class="prl-batch-btn prl-batch-btn-primary">
                                <i class="feather-check-circle me-1"></i> Approve batch
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

{{-- ── Batch Reject Modal ── --}}
<div class="modal fade" id="batchRejectModal" tabindex="-1" aria-hidden="true"
     style="font-family:'Sora',sans-serif;">
    <div class="modal-dialog modal-dialog-centered" style="max-width:440px;">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 20px 60px rgba(0,0,0,0.15);">
            <form id="batchRejectForm" action="{{ route('payroll-approval.reject-batch') }}" method="POST">
                @csrf
                <input type="hidden" id="batchRejectStart" name="start">
                <input type="hidden" id="batchRejectEnd" name="end">
                <div class="modal-header" style="border-bottom:1px solid #f3f4f6;padding:18px 22px;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:36px;height:36px;border-radius:10px;background:#fff1f2;border:1px solid #fecaca;display:flex;align-items:center;justify-content:center;color:#c8292a;flex-shrink:0;">
                            <i class="feather-x-circle" style="font-size:16px;"></i>
                        </div>
                        <div>
                            <h6 class="modal-title" style="font-size:0.92rem;font-weight:800;color:#111827;margin:0;">Reject Payroll Batch</h6>
                            <p id="batchRejectDesc" style="font-size:0.78rem;color:#9ca3af;margin:0;"></p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:20px 22px;">
                    <label style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#9ca3af;display:block;margin-bottom:6px;">
                        Rejection Note <span style="color:#c8292a;">*</span>
                    </label>
                    <textarea name="rejection_note" id="batchRejectNote" class="form-control" rows="3" required minlength="3"
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
                            style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:9px;font-family:'Sora',sans-serif;font-size:0.82rem;font-weight:700;border:none;background:#c8292a;color:#fff;box-shadow:0 2px 8px rgba(200,41,42,0.3);cursor:pointer;">
                        <i class="feather-x-circle"></i> Reject batch
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openBatchRejectModal(start, end, count) {
    document.getElementById('batchRejectStart').value = start;
    document.getElementById('batchRejectEnd').value   = end;
    document.getElementById('batchRejectDesc').textContent = count + ' payroll record(s) will be rejected.';
    document.getElementById('batchRejectNote').value = '';
    new bootstrap.Modal(document.getElementById('batchRejectModal')).show();
}
</script>
@endpush
