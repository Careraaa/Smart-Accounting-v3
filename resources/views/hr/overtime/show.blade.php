@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
.ot-page { font-family: 'Sora', sans-serif; }

.ot-topbar { display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px;flex-wrap:wrap; }
.ot-topbar-title { font-size:1.35rem;font-weight:800;color:#111827;letter-spacing:-0.02em;margin:0 0 2px; }
.ot-topbar-sub   { font-size:0.78rem;color:#9ca3af;margin:0; }

.ot-btn-sec {
    display:inline-flex;align-items:center;gap:7px;padding:9px 16px;background:#fff;color:#374151;
    border:1px solid #e5e7eb;border-radius:10px;font-family:'Sora',sans-serif;font-size:0.82rem;
    font-weight:600;text-decoration:none;cursor:pointer;transition:all 0.15s;white-space:nowrap;
}
.ot-btn-sec:hover { border-color:#c8292a;color:#c8292a;background:#fff5f5; }

/* Single column layout */
.ot-layout { display:grid;grid-template-columns:1fr;gap:16px;align-items:start; }
@media(max-width:900px){ .ot-layout{grid-template-columns:1fr;} }

/* Detail card */
.ot-card { background:#fff;border:1px solid #e5e7eb;border-radius:16px;overflow:hidden; }
.ot-card-header { padding:20px 24px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;gap:12px; }
.ot-card-icon { width:38px;height:38px;border-radius:10px;background:#fff0f0;color:#c8292a;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.ot-card-title { font-size:0.95rem;font-weight:800;color:#111827;margin:0 0 2px; }
.ot-card-sub   { font-size:0.75rem;color:#9ca3af;margin:0; }
.ot-card-body  { padding:24px; }

.ot-divider { font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#9ca3af;border-bottom:1px solid #f3f4f6;padding-bottom:10px;margin-bottom:20px; }

.ot-fields-grid { display:grid;grid-template-columns:1fr 1fr;gap:16px; }
@media(max-width:560px){ .ot-fields-grid{grid-template-columns:1fr;} }
.ot-fields-grid.full { grid-template-columns:1fr; }

.ot-field-label { display:block;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.09em;color:#9ca3af;margin-bottom:5px; }
.ot-field-value { font-size:0.875rem;color:#111827;font-weight:500; }
.ot-field-box { background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:10px 14px; }
.ot-field-box.mono { font-family:'DM Mono',monospace;font-size:0.82rem; }
.ot-field-textarea { background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:12px 14px;font-size:0.845rem;color:#374151;line-height:1.6;white-space:pre-wrap;min-height:80px; }

.ot-status-badge { display:inline-flex;align-items:center;gap:6px;padding:4px 12px;border-radius:20px;font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em; }
.ot-status-badge::before { content:'';width:6px;height:6px;border-radius:50%; }
.ot-status-badge.approved  { background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0; }
.ot-status-badge.approved::before { background:#16a34a; }
.ot-status-badge.rejected  { background:#fff0f0;color:#c8292a;border:1px solid #fecaca; }
.ot-status-badge.rejected::before { background:#c8292a; }
.ot-status-badge.pending   { background:#fffbeb;color:#d97706;border:1px solid #fde68a; }
.ot-status-badge.pending::before   { background:#d97706; }

/* Status info box */
.ot-info-box { border-radius:10px;padding:14px 16px;margin-bottom:12px; }
.ot-info-box.approved { background:#f0fdf4;border:1px solid #bbf7d0; }
.ot-info-box.rejected { background:#fff0f0;border:1px solid #fecaca; }
.ot-info-box-title { font-size:0.82rem;font-weight:700;margin-bottom:8px; }
.ot-info-box.approved .ot-info-box-title { color:#15803d; }
.ot-info-box.rejected .ot-info-box-title { color:#c8292a; }
.ot-info-row { display:flex;justify-content:space-between;align-items:center;font-size:0.78rem;margin-bottom:4px; }
.ot-info-row:last-child { margin-bottom:0; }
.ot-info-key   { color:#9ca3af;font-weight:600; }
.ot-info-val   { color:#111827;font-weight:600; }
.ot-info-reason { font-size:0.78rem;color:#374151;margin-top:8px;padding-top:8px;border-top:1px solid rgba(200,41,42,0.15);line-height:1.5; }

/* Action buttons */
.ot-btn-approve {
    display:flex;align-items:center;justify-content:center;gap:8px;
    padding:11px 16px;background:#16a34a;color:#fff;border:none;border-radius:10px;
    font-family:'Sora',sans-serif;font-size:0.845rem;font-weight:700;cursor:pointer;transition:background 0.15s;white-space:nowrap;
}
.ot-btn-approve:hover { background:#15803d; }

.ot-btn-reject {
    display:flex;align-items:center;justify-content:center;gap:8px;
    padding:11px 16px;background:#c8292a;color:#fff;border:none;border-radius:10px;
    font-family:'Sora',sans-serif;font-size:0.845rem;font-weight:700;cursor:pointer;transition:background 0.15s;white-space:nowrap;
}
.ot-btn-reject:hover { background:#a81f20; }

.ot-reject-label { display:block;font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.09em;color:#6b7280;margin-bottom:6px; }
.ot-reject-textarea { width:100%;border:1px solid #e5e7eb;border-radius:8px;padding:10px 12px;font-size:0.82rem;font-family:'Sora',sans-serif;color:#111827;background:#f9fafb;outline:none;resize:vertical;min-height:80px;transition:border-color 0.15s; }
.ot-reject-textarea:focus { border-color:#c8292a;box-shadow:0 0 0 3px rgba(200,41,42,0.08);background:#fff; }
.ot-invalid { font-size:0.75rem;color:#ef4444;margin-top:4px;display:block; }

/* Action section at bottom */
.ot-action-section { margin-top:24px;padding-top:24px;border-top:1px solid #f3f4f6; }
.ot-action-section-title { font-size:0.82rem;font-weight:700;color:#111827;text-transform:uppercase;letter-spacing:0.09em;margin-bottom:16px;display:flex;align-items:center;gap:8px; }

.ot-btn-group { display:grid;grid-template-columns:1fr 1fr;gap:10px;max-width:400px;margin-left:auto; }
@media(max-width:560px){ .ot-btn-group{grid-template-columns:1fr;margin-left:auto;} }

/* Modal */
.ot-modal-overlay { position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);display:none;align-items:center;justify-content:center;z-index:9999;padding:20px; }
.ot-modal-overlay.active { display:flex; }
.ot-modal { background:#fff;border-radius:16px;box-shadow:0 10px 40px rgba(0,0,0,0.15);max-width:500px;width:100%;padding:28px; }
.ot-modal-header { margin-bottom:20px; }
.ot-modal-title { font-size:1.1rem;font-weight:800;color:#111827;margin:0 0 4px; }
.ot-modal-subtitle { font-size:0.78rem;color:#9ca3af;margin:0; }
.ot-modal-body { margin-bottom:24px; }
.ot-modal-footer { display:flex;gap:10px;justify-content:flex-end; }

.ot-modal-btn { padding:10px 18px;border:none;border-radius:10px;font-family:'Sora',sans-serif;font-size:0.845rem;font-weight:700;cursor:pointer;transition:all 0.15s; }
.ot-modal-btn-cancel { background:#f3f4f6;color:#374151; }
.ot-modal-btn-cancel:hover { background:#e5e7eb; }
.ot-modal-btn-confirm { background:#c8292a;color:#fff; }
.ot-modal-btn-confirm:hover { background:#a81f20; }
</style>
@endpush

@section('content')
<div class="ot-page">

    <div class="ot-topbar">
        <div>
            <h1 class="ot-topbar-title">Overtime / Undertime Request Details</h1>
            <p class="ot-topbar-sub">{{ $overtime->employee->first_name }} {{ $overtime->employee->last_name }} · {{ ucfirst($overtime->type) }}</p>
        </div>
        <a href="{{ url()->previous() }}" class="ot-btn-sec">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back
        </a>
    </div>

    <div class="ot-layout">

        {{-- Left: details --}}
        <div class="ot-card">
            <div class="ot-card-header">
                <div class="ot-card-icon">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="ot-card-title">Overtime / Undertime Details</p>
                    <p class="ot-card-sub">Submitted {{ $overtime->created_at->format('M d, Y') }}</p>
                </div>
                <div style="margin-left:auto;">
                    <span class="ot-status-badge {{ $overtime->status }}">{{ ucfirst($overtime->status) }}</span>
                </div>
            </div>
            <div class="ot-card-body">

                <div class="ot-divider">Employee</div>
                <div class="ot-fields-grid" style="margin-bottom:20px;">
                    <div>
                        <span class="ot-field-label">Full Name</span>
                        <div class="ot-field-box"><span class="ot-field-value">{{ $overtime->employee->first_name }} {{ $overtime->employee->last_name }}</span></div>
                    </div>
                    <div>
                        <span class="ot-field-label">Department</span>
                        <div class="ot-field-box"><span class="ot-field-value">{{ $overtime->employee->department ?? 'N/A' }}</span></div>
                    </div>
                </div>

                <div class="ot-divider">Request Info</div>
                <div class="ot-fields-grid" style="margin-bottom:20px;">
                    <div>
                        <span class="ot-field-label">Type</span>
                        <div class="ot-field-box">
                            <span class="ot-field-value">
                                @if($overtime->type === 'overtime')
                                    <span style="color:#0284c7;font-weight:700;">Overtime</span>
                                @else
                                    <span style="color:#d97706;font-weight:700;">Undertime</span>
                                @endif
                            </span>
                        </div>
                    </div>
                    <div>
                        <span class="ot-field-label">Date</span>
                        <div class="ot-field-box mono"><span class="ot-field-value">{{ $overtime->date->format('F d, Y') }}</span></div>
                    </div>
                    <div>
                        <span class="ot-field-label">Hours</span>
                        <div class="ot-field-box mono"><span class="ot-field-value">{{ number_format($overtime->hours, 2) }} hrs</span></div>
                    </div>
                    <div>
                        <span class="ot-field-label">Hourly Rate</span>
                        <div class="ot-field-box mono"><span class="ot-field-value">₱{{ number_format($overtime->hourly_rate_used, 2) }}</span></div>
                    </div>
                    <div>
                        <span class="ot-field-label">Amount</span>
                        <div class="ot-field-box mono"><span class="ot-field-value" style="font-weight:700;font-size:0.95rem;">₱{{ number_format(abs($overtime->amount), 2) }}</span></div>
                    </div>
                </div>

                <div class="ot-divider">Reason / Remarks</div>
                <div style="margin-bottom:24px;">
                    <span class="ot-field-label">Reason</span>
                    <div class="ot-field-textarea">{{ $overtime->reason }}</div>
                </div>

                {{-- Status Info --}}
                @if($overtime->status === 'approved')
                <div class="ot-info-box approved">
                    <div class="ot-info-box-title">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="display:inline;margin-right:4px;"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Approved
                    </div>
                    <div class="ot-info-row"><span class="ot-info-key">By</span><span class="ot-info-val">{{ $overtime->approvedBy->first_name ?? 'Admin' }} {{ $overtime->approvedBy->last_name ?? '' }}</span></div>
                    <div class="ot-info-row"><span class="ot-info-key">Date</span><span class="ot-info-val">{{ $overtime->updated_at->format('M d, Y') }}</span></div>
                </div>
                @elseif($overtime->status === 'rejected')
                <div class="ot-info-box rejected">
                    <div class="ot-info-box-title">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="display:inline;margin-right:4px;"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        Rejected
                    </div>
                    <div class="ot-info-row"><span class="ot-info-key">By</span><span class="ot-info-val">{{ $overtime->approvedBy->first_name ?? 'Admin' }} {{ $overtime->approvedBy->last_name ?? '' }}</span></div>
                    <div class="ot-info-row"><span class="ot-info-key">Date</span><span class="ot-info-val">{{ $overtime->updated_at->format('M d, Y') }}</span></div>
                    @if($overtime->rejection_reason)
                    <div class="ot-info-reason"><strong>Reason:</strong> {{ $overtime->rejection_reason }}</div>
                    @endif
                </div>
                @endif

                {{-- Action Buttons --}}
                @if($overtime->status === 'pending')
                <div class="ot-action-section">

                    <div class="ot-btn-group">
                        {{-- Reject --}}
                        <button type="button" class="ot-btn-reject" id="rejectBtn">
                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            Reject Request
                        </button>

                         {{-- Approve --}}
                        <button type="button" class="ot-btn-approve" id="approveBtn">
                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Approve Request
                        </button>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>

    {{-- Approval Modal --}}
    <div class="ot-modal-overlay" id="approvalModal">
        <div class="ot-modal">
            <div class="ot-modal-header">
                <h3 class="ot-modal-title">Approve Overtime/Undertime Request</h3>
                <p class="ot-modal-subtitle">Are you sure you want to approve this request?</p>
            </div>
            <form action="{{ route('overtime.approve', $overtime) }}" method="POST" id="approvalForm">
                @csrf
                <div class="ot-modal-body">
                    <p style="color:#374151;font-size:0.875rem;line-height:1.6;margin:0;">
                        <strong>Employee:</strong> {{ $overtime->employee->first_name }} {{ $overtime->employee->last_name }}<br>
                        <strong>Type:</strong> {{ ucfirst($overtime->type) }}<br>
                        <strong>Date:</strong> {{ $overtime->date->format('F d, Y') }}<br>
                        <strong>Hours:</strong> {{ number_format($overtime->hours, 2) }} hrs
                    </p>
                </div>
                <div class="ot-modal-footer">
                    <button type="button" class="ot-modal-btn ot-modal-btn-cancel" id="approvalCancelBtn">Cancel</button>
                    <button type="submit" class="ot-modal-btn ot-modal-btn-confirm" style="background:#16a34a;">Approve Request</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Rejection Modal --}}
    <div class="ot-modal-overlay" id="rejectionModal">
        <div class="ot-modal">
            <div class="ot-modal-header">
                <h3 class="ot-modal-title">Reject Overtime/Undertime Request</h3>
                <p class="ot-modal-subtitle">Please provide a reason for rejecting this request</p>
            </div>
            <form action="{{ route('overtime.reject', $overtime) }}" method="POST" id="rejectionForm">
                @csrf
                <div class="ot-modal-body">
                    <label for="rejection_reason" class="ot-reject-label">Rejection Reason <span style="color:#c8292a;">*</span></label>
                    <textarea name="rejection_reason" id="rejection_reason"
                        class="ot-reject-textarea @error('rejection_reason') is-invalid @enderror"
                        placeholder="Enter reason for rejection…" required></textarea>
                    @error('rejection_reason')
                        <span class="ot-invalid">{{ $message }}</span>
                    @enderror
                </div>
                <div class="ot-modal-footer">
                    <button type="button" class="ot-modal-btn ot-modal-btn-cancel" id="cancelBtn">Cancel</button>
                    <button type="submit" class="ot-modal-btn ot-modal-btn-confirm">Reject Request</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // Approve button and modal
    const approveBtn = document.getElementById('approveBtn');
    const approvalModal = document.getElementById('approvalModal');
    const approvalCancelBtn = document.getElementById('approvalCancelBtn');

    if (approveBtn) {
        approveBtn.addEventListener('click', function() {
            approvalModal.classList.add('active');
        });
    }

    if (approvalCancelBtn) {
        approvalCancelBtn.addEventListener('click', function() {
            approvalModal.classList.remove('active');
        });
    }

    // Reject button and modal
    const rejectBtn = document.getElementById('rejectBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const rejectionModal = document.getElementById('rejectionModal');

    if (rejectBtn) {
        rejectBtn.addEventListener('click', function() {
            rejectionModal.classList.add('active');
            document.querySelector('#rejection_reason').focus();
        });
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            rejectionModal.classList.remove('active');
            document.querySelector('#rejection_reason').value = '';
        });
    }

    // Close modals on overlay click
    const approvalOverlay = document.getElementById('approvalModal');
    const rejectionOverlay = document.getElementById('rejectionModal');

    if (approvalOverlay) {
        approvalOverlay.addEventListener('click', function(e) {
            if (e.target === approvalOverlay) {
                approvalModal.classList.remove('active');
            }
        });
    }

    if (rejectionOverlay) {
        rejectionOverlay.addEventListener('click', function(e) {
            if (e.target === rejectionOverlay) {
                rejectionModal.classList.remove('active');
                document.querySelector('#rejection_reason').value = '';
            }
        });
    }

    // Close modals on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            approvalModal.classList.remove('active');
            rejectionModal.classList.remove('active');
            document.querySelector('#rejection_reason').value = '';
        }
    });
</script>
@endpush