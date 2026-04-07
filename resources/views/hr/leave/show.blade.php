@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
.lv-page { font-family: 'Sora', sans-serif; }

.lv-topbar { display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px;flex-wrap:wrap; }
.lv-topbar-title { font-size:1.35rem;font-weight:800;color:#111827;letter-spacing:-0.02em;margin:0 0 2px; }
.lv-topbar-sub   { font-size:0.78rem;color:#9ca3af;margin:0; }

.lv-btn-sec {
    display:inline-flex;align-items:center;gap:7px;padding:9px 16px;background:#fff;color:#374151;
    border:1px solid #e5e7eb;border-radius:10px;font-family:'Sora',sans-serif;font-size:0.82rem;
    font-weight:600;text-decoration:none;cursor:pointer;transition:all 0.15s;white-space:nowrap;
}
.lv-btn-sec:hover { border-color:#c8292a;color:#c8292a;background:#fff5f5; }

/* Two-column layout */
.lv-layout { display:grid;grid-template-columns:1fr 300px;gap:16px;align-items:start; }
@media(max-width:900px){ .lv-layout{grid-template-columns:1fr;} }

/* Detail card */
.lv-card { background:#fff;border:1px solid #e5e7eb;border-radius:16px;overflow:hidden; }
.lv-card-header { padding:20px 24px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;gap:12px; }
.lv-card-icon { width:38px;height:38px;border-radius:10px;background:#fff0f0;color:#c8292a;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.lv-card-title { font-size:0.95rem;font-weight:800;color:#111827;margin:0 0 2px; }
.lv-card-sub   { font-size:0.75rem;color:#9ca3af;margin:0; }
.lv-card-body  { padding:24px; }

.lv-divider { font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#9ca3af;border-bottom:1px solid #f3f4f6;padding-bottom:10px;margin-bottom:20px; }

.lv-fields-grid { display:grid;grid-template-columns:1fr 1fr;gap:16px; }
@media(max-width:560px){ .lv-fields-grid{grid-template-columns:1fr;} }
.lv-fields-grid.full { grid-template-columns:1fr; }

.lv-field-label { display:block;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.09em;color:#9ca3af;margin-bottom:5px; }
.lv-field-value { font-size:0.875rem;color:#111827;font-weight:500; }
.lv-field-box { background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:10px 14px; }
.lv-field-box.mono { font-family:'DM Mono',monospace;font-size:0.82rem; }
.lv-field-textarea { background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:12px 14px;font-size:0.845rem;color:#374151;line-height:1.6;white-space:pre-wrap;min-height:80px; }

.lv-status-badge { display:inline-flex;align-items:center;gap:6px;padding:4px 12px;border-radius:20px;font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em; }
.lv-status-badge::before { content:'';width:6px;height:6px;border-radius:50%; }
.lv-status-badge.approved  { background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0; }
.lv-status-badge.approved::before { background:#16a34a; }
.lv-status-badge.rejected  { background:#fff0f0;color:#c8292a;border:1px solid #fecaca; }
.lv-status-badge.rejected::before { background:#c8292a; }
.lv-status-badge.pending   { background:#fffbeb;color:#d97706;border:1px solid #fde68a; }
.lv-status-badge.pending::before   { background:#d97706; }

/* Sidebar cards */
.lv-side-card { background:#fff;border:1px solid #e5e7eb;border-radius:16px;overflow:hidden;margin-bottom:12px; }
.lv-side-head { padding:14px 18px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;gap:8px; }
.lv-side-head-title { font-size:0.82rem;font-weight:700;color:#111827; }
.lv-side-body { padding:16px 18px; }

/* Status info box */
.lv-info-box { border-radius:10px;padding:14px 16px;margin-bottom:12px; }
.lv-info-box.approved { background:#f0fdf4;border:1px solid #bbf7d0; }
.lv-info-box.rejected { background:#fff0f0;border:1px solid #fecaca; }
.lv-info-box-title { font-size:0.82rem;font-weight:700;margin-bottom:8px; }
.lv-info-box.approved .lv-info-box-title { color:#15803d; }
.lv-info-box.rejected .lv-info-box-title { color:#c8292a; }
.lv-info-row { display:flex;justify-content:space-between;align-items:center;font-size:0.78rem;margin-bottom:4px; }
.lv-info-row:last-child { margin-bottom:0; }
.lv-info-key   { color:#9ca3af;font-weight:600; }
.lv-info-val   { color:#111827;font-weight:600; }
.lv-info-reason { font-size:0.78rem;color:#374151;margin-top:8px;padding-top:8px;border-top:1px solid rgba(200,41,42,0.15);line-height:1.5; }

/* Action buttons */
.lv-btn-approve {
    width:100%;display:flex;align-items:center;justify-content:center;gap:8px;
    padding:11px;background:#16a34a;color:#fff;border:none;border-radius:10px;
    font-family:'Sora',sans-serif;font-size:0.845rem;font-weight:700;cursor:pointer;transition:background 0.15s;
}
.lv-btn-approve:hover { background:#15803d; }

.lv-btn-reject {
    width:100%;display:flex;align-items:center;justify-content:center;gap:8px;
    padding:11px;background:#c8292a;color:#fff;border:none;border-radius:10px;
    font-family:'Sora',sans-serif;font-size:0.845rem;font-weight:700;cursor:pointer;transition:background 0.15s;margin-top:8px;
}
.lv-btn-reject:hover { background:#a81f20; }

.lv-reject-label { display:block;font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.09em;color:#6b7280;margin-bottom:6px; }
.lv-reject-textarea { width:100%;border:1px solid #e5e7eb;border-radius:8px;padding:10px 12px;font-size:0.82rem;font-family:'Sora',sans-serif;color:#111827;background:#f9fafb;outline:none;resize:vertical;min-height:80px;transition:border-color 0.15s; }
.lv-reject-textarea:focus { border-color:#c8292a;box-shadow:0 0 0 3px rgba(200,41,42,0.08);background:#fff; }
.lv-invalid { font-size:0.75rem;color:#ef4444;margin-top:4px;display:block; }
</style>
@endpush

@section('content')
<div class="lv-page">

    <div class="lv-topbar">
        <div>
            <h1 class="lv-topbar-title">Leave Request Details</h1>
            <p class="lv-topbar-sub">{{ $leave->employee->first_name }} {{ $leave->employee->last_name }} · {{ $leave->leave_type }}</p>
        </div>
        <a href="{{ route('leave.pending') }}" class="lv-btn-sec">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Leaves
        </a>
    </div>

    <div class="lv-layout">

        {{-- Left: details --}}
        <div class="lv-card">
            <div class="lv-card-header">
                <div class="lv-card-icon">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <p class="lv-card-title">Leave Details</p>
                    <p class="lv-card-sub">Submitted {{ $leave->created_at->format('M d, Y') }}</p>
                </div>
                <div style="margin-left:auto;">
                    <span class="lv-status-badge {{ $leave->status }}">{{ ucfirst($leave->status) }}</span>
                </div>
            </div>
            <div class="lv-card-body">

                <div class="lv-divider">Employee</div>
                <div class="lv-fields-grid" style="margin-bottom:20px;">
                    <div>
                        <span class="lv-field-label">Full Name</span>
                        <div class="lv-field-box"><span class="lv-field-value">{{ $leave->employee->first_name }} {{ $leave->employee->last_name }}</span></div>
                    </div>
                    <div>
                        <span class="lv-field-label">Department</span>
                        <div class="lv-field-box"><span class="lv-field-value">{{ $leave->employee->department ?? 'N/A' }}</span></div>
                    </div>
                </div>

                <div class="lv-divider">Leave Info</div>
                <div class="lv-fields-grid" style="margin-bottom:20px;">
                    <div>
                        <span class="lv-field-label">Leave Type</span>
                        <div class="lv-field-box"><span class="lv-field-value">{{ $leave->leave_type }}</span></div>
                    </div>
                    <div>
                        <span class="lv-field-label">Number of Days</span>
                        <div class="lv-field-box mono"><span class="lv-field-value">{{ $leave->start_date->diffInDays($leave->end_date) + 1 }} day(s)</span></div>
                    </div>
                    <div>
                        <span class="lv-field-label">Start Date</span>
                        <div class="lv-field-box mono"><span class="lv-field-value">{{ $leave->start_date->format('F d, Y') }}</span></div>
                    </div>
                    <div>
                        <span class="lv-field-label">End Date</span>
                        <div class="lv-field-box mono"><span class="lv-field-value">{{ $leave->end_date->format('F d, Y') }}</span></div>
                    </div>
                </div>

                <div class="lv-divider">Reason</div>
                <div>
                    <span class="lv-field-label">Remarks / Reason</span>
                    <div class="lv-field-textarea">{{ $leave->reason }}</div>
                </div>

            </div>
        </div>

        {{-- Right: sidebar --}}
        <div>

            {{-- Status info --}}
            @if($leave->status === 'approved')
            <div class="lv-info-box approved">
                <div class="lv-info-box-title">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="display:inline;margin-right:4px;"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Approved
                </div>
                <div class="lv-info-row"><span class="lv-info-key">By</span><span class="lv-info-val">{{ $leave->approvedBy->first_name ?? 'Admin' }}</span></div>
                <div class="lv-info-row"><span class="lv-info-key">Date</span><span class="lv-info-val">{{ $leave->updated_at->format('M d, Y') }}</span></div>
            </div>
            @elseif($leave->status === 'rejected')
            <div class="lv-info-box rejected">
                <div class="lv-info-box-title">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="display:inline;margin-right:4px;"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    Rejected
                </div>
                <div class="lv-info-row"><span class="lv-info-key">By</span><span class="lv-info-val">{{ $leave->approvedBy->first_name ?? 'Admin' }}</span></div>
                <div class="lv-info-row"><span class="lv-info-key">Date</span><span class="lv-info-val">{{ $leave->updated_at->format('M d, Y') }}</span></div>
                @if($leave->rejection_reason)
                <div class="lv-info-reason"><strong>Reason:</strong> {{ $leave->rejection_reason }}</div>
                @endif
            </div>
            @endif

            {{-- Action panel (pending only) --}}
            @if($leave->status === 'pending')
            <div class="lv-side-card">
                <div class="lv-side-head">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="lv-side-head-title">Take Action</span>
                </div>
                <div class="lv-side-body">

                    {{-- Approve --}}
                    <form action="{{ route('leave.approve', $leave) }}" method="POST">
                        @csrf
                        <button type="submit" class="lv-btn-approve">
                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Approve Leave
                        </button>
                    </form>

                    <div style="margin:16px 0;border-top:1px solid #f3f4f6;"></div>

                    {{-- Reject --}}
                    <form action="{{ route('leave.reject', $leave) }}" method="POST">
                        @csrf
                        <div style="margin-bottom:10px;">
                            <label for="rejection_reason" class="lv-reject-label">Rejection Reason <span style="color:#c8292a;">*</span></label>
                            <textarea name="rejection_reason" id="rejection_reason"
                                class="lv-reject-textarea @error('rejection_reason') is-invalid @enderror"
                                placeholder="Enter reason for rejection…" required></textarea>
                            @error('rejection_reason')
                                <span class="lv-invalid">{{ $message }}</span>
                            @enderror
                        </div>
                        <button type="submit" class="lv-btn-reject">
                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            Reject Leave
                        </button>
                    </form>

                </div>
            </div>
            @endif

        </div>
    </div>

</div>
@endsection