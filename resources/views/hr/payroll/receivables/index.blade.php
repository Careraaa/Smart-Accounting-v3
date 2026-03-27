@extends('layouts.layout')

@section('title', 'Payroll Receivables')

@section('content')
<div class="main-content">

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <i class="feather-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <i class="feather-alert-circle me-2"></i> {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ── Tabs ── --}}
    <ul class="nav nav-tabs mb-3" id="receivableTabs">
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'cash_advances' ? 'active' : '' }}"
               href="{{ route('payroll.receivables.index') }}?tab=cash_advances">
                <i class="feather-credit-card me-1"></i> Cash Advances
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'salary_loans' ? 'active' : '' }}"
               href="{{ route('payroll.receivables.index') }}?tab=salary_loans">
                <i class="feather-briefcase me-1"></i> Salary Loans
            </a>
        </li>
    </ul>



    {{-- ══════════════════════════════════════════════════════════
         TAB 2 — CASH ADVANCES
    ══════════════════════════════════════════════════════════ --}}
    @if ($tab === 'cash_advances')
    <div class="card">
        <div class="card-header">
            <span class="card-title">Cash Advance Requests</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Amount</th>
                            <th>Requested</th>
                            <th>Status</th>
                            <th>Approved By</th>
                            <th>Deducted On</th>
                            @if (auth()->user()->role === 'accountant')
                            <th class="text-center">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cashAdvances as $advance)
                        <tr>
                            <td>
                                <div class="fw-semibold" style="font-size:.845rem;">{{ $advance->user->name ?? '—' }}</div>
                                <div class="text-muted" style="font-size:.75rem;">{{ $advance->user->position ?? '' }}</div>
                            </td>
                            <td class="fw-bold" style="font-size:.9rem;">₱{{ number_format($advance->amount, 2) }}</td>
                            <td style="font-size:.845rem;">
                                {{ $advance->request_date ? \Carbon\Carbon::parse($advance->request_date)->format('M d, Y') : '—' }}
                            </td>
                            <td>
                                @php
                                    $statusMap = [
                                        'pending'  => 'emp-badge-pending',
                                        'approved' => 'emp-badge-approved',
                                        'rejected' => 'emp-badge-inactive',
                                        'deducted' => 'emp-badge-active',
                                    ];
                                    $cls = $statusMap[$advance->status] ?? 'bg-secondary';
                                @endphp
                                <span class="emp-badge {{ $cls }}">{{ ucfirst($advance->status) }}</span>
                            </td>
                            <td style="font-size:.845rem;">
                                {{ optional($advance->approver)->name ?? '—' }}
                                @if ($advance->approved_at)
                                    <div class="text-muted" style="font-size:.72rem;">
                                        {{ \Carbon\Carbon::parse($advance->approved_at)->format('M d, Y') }}
                                    </div>
                                @endif
                            </td>
                            <td style="font-size:.845rem;">
                                @if ($advance->deductedPayroll)
                                    Period ending {{ \Carbon\Carbon::parse($advance->deductedPayroll->payroll_period_end)->format('M d, Y') }}
                                @else
                                    —
                                @endif
                            </td>
                            @if (auth()->user()->role === 'accountant')
                            <td class="text-center">
                                @if ($advance->status === 'pending')
                                <form method="POST" action="{{ route('cash-advances.approve', $advance) }}" class="d-inline">
                                    @csrf
                                    <button class="emp-action-btn emp-action-approve" title="Approve">
                                        <i class="feather-check"></i>
                                    </button>
                                </form>
                                <button class="emp-action-btn emp-action-danger" title="Reject"
                                        type="button"
                                        onclick="openRejectModal('ca', {{ $advance->id }})">
                                    <i class="feather-x"></i>
                                </button>
                                @else
                                <span class="text-muted" style="font-size:.75rem;">—</span>
                                @endif
                            </td>
                            @endif
                        </tr>
                        @if ($advance->rejection_reason)
                        <tr class="table-light">
                            <td colspan="{{ auth()->user()->role === 'accountant' ? 7 : 6 }}"
                                class="text-muted" style="font-size:.78rem; padding-top:2px!important; padding-bottom:8px!important;">
                                <i class="feather-message-square me-1"></i>
                                <strong>Rejection reason:</strong> {{ $advance->rejection_reason }}
                            </td>
                        </tr>
                        @endif
                        @empty
                        <tr>
                            <td colspan="{{ auth()->user()->role === 'accountant' ? 7 : 6 }}" class="text-center text-muted py-4">
                                No cash advance requests found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($cashAdvances->hasPages())
        <div class="card-footer d-flex justify-content-end">
            {{ $cashAdvances->links() }}
        </div>
        @endif
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════
         TAB 3 — SALARY LOANS
    ══════════════════════════════════════════════════════════ --}}
    @if ($tab === 'salary_loans')
    <div class="card">
        <div class="card-header">
            <span class="card-title">Salary Loan Applications</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Loan Amount</th>
                            <th>Monthly Deduction</th>
                            <th>Remaining</th>
                            <th>Progress</th>
                            <th>Status</th>
                            <th>Start Date</th>
                            @if (auth()->user()->role === 'accountant')
                            <th class="text-center">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($salaryLoans as $loan)
                        @php
                            $totalMonths = $loan->monthly_deduction > 0
                                ? ceil($loan->loan_amount / $loan->monthly_deduction)
                                : 0;
                            $progress = $totalMonths > 0
                                ? min(100, round(($loan->months_paid / $totalMonths) * 100))
                                : 0;
                        @endphp
                        <tr>
                            <td>
                                <div class="fw-semibold" style="font-size:.845rem;">{{ $loan->user->name ?? '—' }}</div>
                                <div class="text-muted" style="font-size:.75rem;">{{ $loan->user->position ?? '' }}</div>
                            </td>
                            <td class="fw-bold" style="font-size:.9rem;">₱{{ number_format($loan->loan_amount, 2) }}</td>
                            <td style="font-size:.845rem;">₱{{ number_format($loan->monthly_deduction, 2) }}/mo</td>
                            <td style="font-size:.845rem;">₱{{ number_format($loan->remaining_balance, 2) }}</td>
                            <td style="min-width:120px;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height:6px; border-radius:4px;">
                                        <div class="progress-bar bg-success" style="width:{{ $progress }}%"></div>
                                    </div>
                                    <span style="font-size:.72rem; color:#9898a8; white-space:nowrap;">
                                        {{ $loan->months_paid }}/{{ $totalMonths }} mo
                                    </span>
                                </div>
                            </td>
                            <td>
                                @php
                                    $loanStatusMap = [
                                        'pending'  => 'emp-badge-pending',
                                        'active'   => 'emp-badge-approved',
                                        'settled'  => 'emp-badge-active',
                                        'rejected' => 'emp-badge-inactive',
                                    ];
                                    $lcls = $loanStatusMap[$loan->status] ?? 'bg-secondary';
                                @endphp
                                <span class="emp-badge {{ $lcls }}">{{ ucfirst($loan->status) }}</span>
                            </td>
                            <td style="font-size:.845rem;">
                                {{ $loan->start_date ? \Carbon\Carbon::parse($loan->start_date)->format('M d, Y') : '—' }}
                            </td>
                            @if (auth()->user()->role === 'accountant')
                            <td class="text-center">
                                @if ($loan->status === 'pending')
                                <form method="POST" action="{{ route('salary-loans.approve', $loan) }}" class="d-inline">
                                    @csrf
                                    <button class="emp-action-btn emp-action-approve" title="Approve">
                                        <i class="feather-check"></i>
                                    </button>
                                </form>
                                <button class="emp-action-btn emp-action-danger" title="Reject"
                                        type="button"
                                        onclick="openRejectModal('loan', {{ $loan->id }})">
                                    <i class="feather-x"></i>
                                </button>
                                @else
                                <span class="text-muted" style="font-size:.75rem;">—</span>
                                @endif
                            </td>
                            @endif
                        </tr>
                        @if ($loan->rejection_reason)
                        <tr class="table-light">
                            <td colspan="{{ auth()->user()->role === 'accountant' ? 8 : 7 }}"
                                class="text-muted" style="font-size:.78rem; padding-top:2px!important; padding-bottom:8px!important;">
                                <i class="feather-message-square me-1"></i>
                                <strong>Rejection reason:</strong> {{ $loan->rejection_reason }}
                            </td>
                        </tr>
                        @endif
                        @empty
                        <tr>
                            <td colspan="{{ auth()->user()->role === 'accountant' ? 8 : 7 }}" class="text-center text-muted py-4">
                                No salary loan applications found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($salaryLoans->hasPages())
        <div class="card-footer d-flex justify-content-end">
            {{ $salaryLoans->links() }}
        </div>
        @endif
    </div>
    @endif

</div>

{{-- ── Mark Paid Modal (HR / superadmin only) ── --}}
@if (in_array(auth()->user()->role, ['hr', 'superadmin']))
<div class="modal fade" id="payModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <form id="payForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title fw-bold">Mark as Paid</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Payment Date</label>
                    <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- ── Reject Modal (Accountant only) ── --}}
@if (auth()->user()->role === 'accountant')
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title fw-bold">Reject Request</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Reason for Rejection</label>
                    <textarea name="rejection_reason" class="form-control" rows="3"
                              placeholder="Enter reason..." required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
@if (in_array(auth()->user()->role, ['hr', 'superadmin']))
const checkAll    = document.getElementById('checkAll');
const batchPayBtn = document.getElementById('batchPayBtn');

function updateBatchBtn() {
    const checked = document.querySelectorAll('.payroll-check:checked').length;
    if (batchPayBtn) batchPayBtn.disabled = checked === 0;
}

if (checkAll) {
    checkAll.addEventListener('change', function () {
        document.querySelectorAll('.payroll-check').forEach(cb => cb.checked = this.checked);
        updateBatchBtn();
    });
}
document.querySelectorAll('.payroll-check').forEach(cb => {
    cb.addEventListener('change', updateBatchBtn);
});

function openPayModal(payrollId) {
    document.getElementById('payForm').action = `/payroll/receivables/${payrollId}/mark-paid`;
    const modal = new bootstrap.Modal(document.getElementById('payModal'), {
        backdrop: true,
        keyboard: true
    });
    modal.show();
}
@endif

@if (auth()->user()->role === 'accountant')
function openRejectModal(type, id) {
    const routes = {
        ca:   `/cash-advances/${id}/reject`,
        loan: `/salary-loans/${id}/reject`,
    };
    document.getElementById('rejectForm').action = routes[type];
    const modal = new bootstrap.Modal(document.getElementById('rejectModal'), {
        backdrop: true,
        keyboard: true
    });
    modal.show();
}
@endif
</script>
@endpush
@endsection