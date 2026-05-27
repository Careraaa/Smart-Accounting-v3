@extends('layouts.layout')

@section('title', 'Salary Loan Details')

@section('content')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');

.detail-page { font-family: 'Sora', sans-serif; }
.detail-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 32px; gap: 20px; flex-wrap: wrap; }
.detail-back { display: inline-flex; align-items: center; gap: 8px; padding: 8px 12px; color: #6b7280; text-decoration: none; font-size: 0.82rem; font-weight: 600; border-radius: 8px; transition: all 0.15s; }
.detail-back:hover { color: #111827; background: #f3f4f6; }
.detail-title { font-size: 1.5rem; font-weight: 800; color: #111827; margin: 0; }
.detail-status-badge { display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 8px; font-size: 0.82rem; font-weight: 600; }
.detail-status-badge.s-pending { background: #fef3c7; color: #a16207; }
.detail-status-badge.s-approved { background: #dcfce7; color: #166534; }
.detail-status-badge.s-released { background: #cffafe; color: #0c4a6e; }
.detail-status-badge.s-rejected { background: #fee2e2; color: #991b1b; }

.detail-content { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; }
@media (max-width: 900px) { .detail-content { grid-template-columns: 1fr; } }

.detail-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; padding: 24px; }
.detail-card-title { font-size: 1rem; font-weight: 700; color: #111827; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid #e5e7eb; }

.detail-field { margin-bottom: 18px; }
.detail-label { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #9ca3af; margin-bottom: 6px; }
.detail-value { font-size: 0.95rem; color: #111827; line-height: 1.5; }
.detail-value.mono { font-family: 'DM Mono', monospace; font-weight: 500; }
.detail-value.emphasis { font-weight: 600; color: #1f2937; }

.detail-section-divider { height: 1px; background: #e5e7eb; margin: 24px 0; }

.detail-employee { display: flex; align-items: center; gap: 14px; }
.detail-avatar { width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 0.9rem; font-weight: 600; }

.detail-actions { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 24px; }
.detail-btn { display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 10px 16px; border: none; border-radius: 10px; font-family: 'Sora', sans-serif; font-size: 0.82rem; font-weight: 600; text-decoration: none; cursor: pointer; transition: all 0.15s; white-space: nowrap; }
.detail-btn-primary { background: #16a34a; color: #fff; }
.detail-btn-primary:hover { background: #15803d; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3); }
.detail-btn-danger { background: #dc2626; color: #fff; }
.detail-btn-danger:hover { background: #b91c1c; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3); }
.detail-btn-secondary { background: #fff; border: 1px solid #e5e7eb; color: #374151; }
.detail-btn-secondary:hover { border-color: #c8292a; color: #c8292a; background: #fff5f5; }

.progress-bar-wrapper { margin-top: 8px; }
.progress-bar { height: 8px; background: #e5e7eb; border-radius: 4px; overflow: hidden; }
.progress-bar-fill { height: 100%; background: linear-gradient(90deg, #3b82f6, #1e40af); border-radius: 4px; transition: width 0.3s; }
.progress-text { font-size: 0.82rem; color: #6b7280; margin-top: 4px; }

.flash { display: flex; align-items: center; gap: 10px; padding: 12px 16px; border-radius: 10px; font-size: 0.82rem; font-weight: 500; margin-bottom: 20px; animation: flashIn 0.3s ease; }
.flash.success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }
.flash.error { background: #fff0f0; border: 1px solid #fecaca; color: #c8292a; }
@keyframes flashIn { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: translateY(0); } }

.modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.4); }
.modal.show { display: flex; align-items: center; justify-content: center; }
.modal-content { background-color: #fff; padding: 32px; border-radius: 14px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15); max-width: 400px; width: 100%; animation: slideIn 0.3s ease; }
@keyframes slideIn { from { transform: translateY(-30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
.modal-title { font-size: 1.2rem; font-weight: 700; color: #111827; margin-bottom: 12px; }
.modal-desc { font-size: 0.9rem; color: #6b7280; margin-bottom: 20px; line-height: 1.5; }
.modal-form { display: flex; flex-direction: column; gap: 12px; }
.modal-textarea { width: 100%; padding: 10px 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-family: 'Sora', sans-serif; font-size: 0.9rem; resize: vertical; min-height: 100px; }
.modal-textarea:focus { outline: none; border-color: #dc2626; box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1); }
.modal-buttons { display: flex; gap: 10px; margin-top: 16px; }
.modal-btn { flex: 1; padding: 10px; border: 1px solid #e5e7eb; border-radius: 8px; font-family: 'Sora', sans-serif; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.15s; }
.modal-btn-cancel { background: #fff; color: #374151; }
.modal-btn-cancel:hover { background: #f3f4f6; border-color: #d1d5db; }
.modal-btn-confirm { background: #dc2626; color: #fff; border-color: #dc2626; }
.modal-btn-confirm:hover { background: #b91c1c; border-color: #b91c1c; }
</style>
@endpush

<div class="detail-page px-5 py-4">
    @if ($message = Session::get('success'))
        <div class="flash success">
            <span>✓</span>
            {{ $message }}
        </div>
    @endif
    @if ($message = Session::get('error'))
        <div class="flash error">
            <span>✗</span>
            {{ $message }}
        </div>
    @endif

    <div class="detail-header">
        <div>
            <a href="{{ route('payroll.receivables.index', ['tab' => 'salary_loans']) }}" class="detail-back">
                ← Back to Receivables
            </a>
            <h1 class="detail-title">Salary Loan Request</h1>
        </div>
        <span class="detail-status-badge s-{{ $salaryLoan->status }}">{{ ucfirst($salaryLoan->status) }}</span>
    </div>

    <div class="detail-content">
        <!-- Main Details -->
        <div class="detail-card">
            <!-- Employee Information -->
            <div class="detail-card-title">Employee Information</div>
            <div class="detail-employee">
                <div class="detail-avatar">{{ substr($salaryLoan->user->name ?? '', 0, 2) }}</div>
                <div>
                    <div style="font-weight: 600; color: #111827; margin-bottom: 2px;">{{ $salaryLoan->user->name ?? '—' }}</div>
                    <div style="font-size: 0.82rem; color: #6b7280;">{{ $salaryLoan->user->position ?? '—' }}</div>
                </div>
            </div>

            <div class="detail-section-divider"></div>

            <!-- Loan Details -->
            <div class="detail-card-title">Loan Details</div>
            <div class="detail-field">
                <div class="detail-label">Loan Amount</div>
                <div class="detail-value mono emphasis">₱{{ number_format($salaryLoan->loan_amount, 2) }}</div>
            </div>

            <div class="detail-field">
                <div class="detail-label">Monthly Deduction</div>
                <div class="detail-value mono">₱{{ number_format($salaryLoan->monthly_deduction, 2) }}</div>
            </div>

            <div class="detail-field">
                <div class="detail-label">Total Months</div>
                <div class="detail-value">{{ $salaryLoan->total_months ?? 12 }} months</div>
            </div>

            <div class="detail-field">
                <div class="detail-label">Remaining Balance</div>
                <div class="detail-value mono" style="color: #dc2626; font-weight: 600;">₱{{ number_format($salaryLoan->remaining_balance, 2) }}</div>
            </div>

            <div class="detail-field">
                <div class="detail-label">Repayment Progress</div>
                @php
                    $totalMonths = $salaryLoan->total_months ?? 12;
                    $monthsPaid = $salaryLoan->months_paid ?? 0;
                    $progress = $totalMonths > 0 ? ($monthsPaid / $totalMonths) * 100 : 0;
                @endphp
                <div class="progress-bar-wrapper">
                    <div class="progress-bar">
                        <div class="progress-bar-fill" style="width: {{ $progress }}%;"></div>
                    </div>
                    <div class="progress-text">{{ $monthsPaid }} of {{ $totalMonths }} months paid</div>
                </div>
            </div>

            <div class="detail-field">
                <div class="detail-label">Start Date</div>
                <div class="detail-value">{{ $salaryLoan->start_date ? \Carbon\Carbon::parse($salaryLoan->start_date)->format('F d, Y') : '—' }}</div>
            </div>

            @if ($salaryLoan->rejection_reason)
                <div class="detail-field">
                    <div class="detail-label">Rejection Reason</div>
                    <div class="detail-value" style="background: #fee2e2; padding: 10px 12px; border-radius: 6px; border-left: 3px solid #dc2626;">
                        {{ $salaryLoan->rejection_reason }}
                    </div>
                </div>
            @endif

            <!-- Action Buttons -->
            @if (in_array(auth()->user()->role, ['hr', 'superadmin']))
                @if ($salaryLoan->status === 'pending')
                    <div class="detail-section-divider"></div>
                    <div class="detail-actions">
                        <form action="{{ route('payroll.receivables.salary-loans.approve', $salaryLoan) }}" method="POST" style="flex: 1;">
                            @csrf
                            <button type="submit" class="detail-btn detail-btn-primary" style="width: 100%;">
                                ✓ Approve Request
                            </button>
                        </form>
                        <button type="button" class="detail-btn detail-btn-danger" onclick="openRejectModal()" style="flex: 1;">
                            ✗ Reject Request
                        </button>
                    </div>
                @endif
            @elseif (in_array(auth()->user()->role, ['accountant', 'superadmin']))
                @if ($salaryLoan->status === 'approved')
                    <div class="detail-section-divider"></div>
                    <div class="detail-actions">
                        <form action="{{ route('salary-loans.release', $salaryLoan) }}" method="POST" style="flex: 1;">
                            @csrf
                            <button type="submit" class="detail-btn detail-btn-primary" style="width: 100%;">
                                ✓ Release Loan
                            </button>
                        </form>
                        <button type="button" class="detail-btn detail-btn-danger" onclick="openRejectModal()" style="flex: 1;">
                            ✗ Reject Request
                        </button>
                    </div>
                @endif
            @endif
        </div>

        <!-- Sidebar: Approval Timeline -->
        <div class="detail-card">
            <div class="detail-card-title">Approval Timeline</div>

            <div class="detail-field">
                <div class="detail-label">Submitted By</div>
                <div class="detail-value">{{ $salaryLoan->user->name ?? '—' }}</div>
            </div>

            <div class="detail-field">
                <div class="detail-label">Submitted On</div>
                <div class="detail-value">{{ $salaryLoan->created_at ? $salaryLoan->created_at->format('F d, Y g:i A') : '—' }}</div>
            </div>

            @if ($salaryLoan->approved_at)
                <div class="detail-field">
                    <div class="detail-label">Approved By</div>
                    <div class="detail-value">{{ optional($salaryLoan->approver)->name ?? '—' }}</div>
                </div>

                <div class="detail-field">
                    <div class="detail-label">Approved On</div>
                    <div class="detail-value">{{ $salaryLoan->approved_at->format('F d, Y g:i A') }}</div>
                </div>
            @endif

            @if ($salaryLoan->status === 'rejected')
                <div style="background: #fee2e2; padding: 12px; border-radius: 8px; margin-top: 16px; border-left: 3px solid #dc2626;">
                    <div style="font-size: 0.75rem; font-weight: 700; color: #991b1b; text-transform: uppercase; margin-bottom: 4px;">Rejected</div>
                    <div style="font-size: 0.85rem; color: #7f1d1d; line-height: 1.4;">{{ $salaryLoan->rejection_reason ?? 'No reason provided' }}</div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="modal">
    <div class="modal-content">
        <h3 class="modal-title">Reject Request</h3>
        <p class="modal-desc">Are you sure you want to reject this salary loan request? Please provide a reason.</p>
        
        <form id="rejectForm" method="POST" class="modal-form">
            @csrf
            <textarea name="rejection_reason" class="modal-textarea" placeholder="Enter rejection reason..." required></textarea>
            <div class="modal-buttons">
                <button type="button" class="modal-btn modal-btn-cancel" onclick="closeRejectModal()">Cancel</button>
                <button type="submit" class="modal-btn modal-btn-confirm">Confirm Rejection</button>
            </div>
        </form>
    </div>
</div>

<script>
function openRejectModal() {
    @if (in_array(auth()->user()->role, ['hr', 'superadmin']))
        document.getElementById('rejectForm').action = '{{ route("payroll.receivables.salary-loans.reject", $salaryLoan) }}';
    @else
        document.getElementById('rejectForm').action = '{{ route("salary-loans.reject", $salaryLoan) }}';
    @endif
    document.getElementById('rejectModal').classList.add('show');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.remove('show');
    document.querySelector('#rejectForm textarea').value = '';
}

document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) closeRejectModal();
});
</script>

@endsection
