@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
.prl-page{font-family:'Sora',sans-serif}
.prl-topbar{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px;flex-wrap:wrap}
.prl-topbar-title{font-size:1.35rem;font-weight:800;color:#111827;letter-spacing:-0.02em;margin:0 0 2px}
.prl-topbar-sub{font-size:.78rem;color:#9ca3af;margin:0}
.prl-topbar-actions{display:flex;gap:8px;flex-wrap:wrap;align-items:center}
.prl-btn-sec{display:inline-flex;align-items:center;gap:7px;padding:9px 16px;background:#fff;color:#374151;border:1px solid #e5e7eb;border-radius:10px;font-family:'Sora',sans-serif;font-size:.82rem;font-weight:600;text-decoration:none;cursor:pointer;transition:all .15s;white-space:nowrap}
.prl-btn-sec:hover{border-color:#c8292a;color:#c8292a;background:#fff5f5}
.prl-btn-primary{display:inline-flex;align-items:center;gap:7px;padding:9px 16px;background:#c8292a;color:#fff;border:none;border-radius:10px;font-family:'Sora',sans-serif;font-size:.82rem;font-weight:700;text-decoration:none;cursor:pointer;transition:all .15s}
.prl-btn-primary:hover{background:#a81f20;color:#fff}
.prl-btn-success{display:inline-flex;align-items:center;gap:7px;padding:9px 16px;background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;border-radius:10px;font-family:'Sora',sans-serif;font-size:.82rem;font-weight:700;text-decoration:none;cursor:pointer;transition:all .15s}
.prl-btn-success:hover{background:#16a34a;color:#fff}
.prl-btn-danger{display:inline-flex;align-items:center;gap:7px;padding:9px 16px;background:#fff0f0;color:#c8292a;border:1px solid #fecaca;border-radius:10px;font-family:'Sora',sans-serif;font-size:.82rem;font-weight:700;text-decoration:none;cursor:pointer;transition:all .15s}
.prl-btn-danger:hover{background:#c8292a;color:#fff}
.prl-two-col{display:grid;grid-template-columns:1fr 320px;gap:16px;align-items:start}
@media(max-width:900px){.prl-two-col{grid-template-columns:1fr}}
.prl-detail-card{background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;margin-bottom:16px}
.prl-detail-head{padding:16px 22px;border-bottom:1px solid #f3f4f6;font-size:.82rem;font-weight:700;color:#111827;display:flex;align-items:center;gap:8px}
.prl-detail-body{padding:22px}
.prl-field-label{font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.09em;color:#9ca3af;display:block;margin-bottom:4px}
.prl-field-value{font-size:.875rem;color:#111827}
.prl-dot{width:8px;height:8px;border-radius:50%;background:#c8292a;display:inline-block}
.prl-status{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;white-space:nowrap}
.prl-status::before{content:'';width:5px;height:5px;border-radius:50%}
.prl-status.s-pending{background:#fffbeb;color:#d97706;border:1px solid #fde68a}.prl-status.s-pending::before{background:#d97706}
.prl-status.s-approved{background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0}.prl-status.s-approved::before{background:#16a34a}
.prl-status.s-rejected{background:#fff0f0;color:#c8292a;border:1px solid #fecaca}.prl-status.s-rejected::before{background:#ef4444}
.prl-type-ot{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:.68rem;font-weight:700;text-transform:uppercase;background:#f0f9ff;color:#0284c7;border:1px solid #bae6fd}
.prl-type-ut{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:.68rem;font-weight:700;text-transform:uppercase;background:#fffbeb;color:#d97706;border:1px solid #fde68a}
.prl-mono{font-family:'DM Mono',monospace;font-size:.82rem;font-variant-numeric:tabular-nums}
.prl-mono.c-bold{color:#111827;font-weight:700}
.prl-reason-box{background:#f9fafb;border:1px solid #f3f4f6;border-radius:10px;padding:14px 16px;font-size:.845rem;color:#374151;line-height:1.6}
.prl-action-card{background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden}
.prl-action-card-head{padding:14px 18px;border-bottom:1px solid #f3f4f6;font-size:.78rem;font-weight:700;color:#111827}
.prl-action-card-body{padding:16px 18px;display:flex;flex-direction:column;gap:8px}
.prl-modal .modal-content{border-radius:16px;border:none;box-shadow:0 20px 60px rgba(0,0,0,.15);font-family:'Sora',sans-serif}
.prl-modal .modal-header{border-bottom:1px solid #f3f4f6;padding:18px 22px}
.prl-modal .modal-title{font-size:.92rem;font-weight:800;color:#111827}
.prl-modal .modal-body{padding:20px 22px}
.prl-modal .modal-footer{border-top:1px solid #f3f4f6;padding:14px 22px;gap:8px}
.prl-modal .form-label{font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#9ca3af;margin-bottom:6px}
.prl-modal .form-control{border:1px solid #e5e7eb;border-radius:8px;font-size:.845rem;font-family:'Sora',sans-serif;color:#111827;padding:9px 12px}
.prl-modal .form-control:focus{border-color:#c8292a;box-shadow:0 0 0 3px rgba(200,41,42,.08);outline:none}
.prl-modal-btn{display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:9px;font-family:'Sora',sans-serif;font-size:.82rem;font-weight:700;border:none;cursor:pointer;transition:all .15s}
.prl-modal-btn.cancel{background:#f3f4f6;color:#374151}.prl-modal-btn.cancel:hover{background:#e5e7eb}
.prl-modal-btn.danger-btn{background:#c8292a;color:#fff;box-shadow:0 2px 8px rgba(200,41,42,.3)}.prl-modal-btn.danger-btn:hover{background:#a81f20}
</style>
@endpush

@section('content')
<div class="prl-page">

    <div class="prl-topbar">
        <div>
            <h1 class="prl-topbar-title">Record Details</h1>
            <p class="prl-topbar-sub">Overtime / Undertime record</p>
        </div>
        <div class="prl-topbar-actions">
            <a href="{{ route('overtime.edit', $overtime) }}" class="prl-btn-sec">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit
            </a>
            <a href="{{ route('overtime.index') }}" class="prl-btn-sec">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Back
            </a>
        </div>
    </div>

    <div class="prl-two-col">

        {{-- Main detail --}}
        <div>
            <div class="prl-detail-card">
                <div class="prl-detail-head"><span class="prl-dot"></span> Record Information</div>
                <div class="prl-detail-body">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px 28px;">
                        <div>
                            <span class="prl-field-label">Employee</span>
                            <div class="prl-field-value" style="font-weight:700;">{{ $overtime->employee->first_name ?? 'N/A' }} {{ $overtime->employee->last_name ?? '' }}</div>
                        </div>
                        <div>
                            <span class="prl-field-label">Type</span>
                            <div>
                                @if($overtime->type === 'overtime')
                                    <span class="prl-type-ot">Overtime</span>
                                @else
                                    <span class="prl-type-ut">Undertime</span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <span class="prl-field-label">Date</span>
                            <div class="prl-field-value">{{ $overtime->date->format('F d, Y') }}</div>
                        </div>
                        <div>
                            <span class="prl-field-label">Hours</span>
                            <div><span class="prl-mono c-bold" style="font-size:.95rem;">{{ number_format($overtime->hours, 2) }} hrs</span></div>
                        </div>
                        <div>
                            <span class="prl-field-label">Status</span>
                            @php $sc = match($overtime->status){ 'approved'=>'s-approved','rejected'=>'s-rejected',default=>'s-pending' }; @endphp
                            <span class="prl-status {{ $sc }}">{{ ucfirst($overtime->status) }}</span>
                        </div>
                    </div>

                    <div style="margin-top:22px;">
                        <span class="prl-field-label">Reason</span>
                        <div class="prl-reason-box">{{ $overtime->reason }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Side actions --}}
        <div>
            @if($overtime->status === 'pending')
            <div class="prl-action-card">
                <div class="prl-action-card-head">Approval Actions</div>
                <div class="prl-action-card-body">
                    <form action="{{ route('overtime.approve', $overtime) }}" method="POST">
                        @csrf
                        <button type="submit" class="prl-btn-success" style="width:100%;justify-content:center;">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Approve Record
                        </button>
                    </form>
                    <button type="button" class="prl-btn-danger" style="width:100%;justify-content:center;" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        Reject Record
                    </button>
                </div>
            </div>
            @endif
        </div>

    </div>

</div>

@if($overtime->status === 'pending')
<div class="modal fade prl-modal" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('overtime.reject', $overtime) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title">Reject Record</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Reason for Rejection <span style="font-size:.72rem;color:#9ca3af;">(optional)</span></label>
                    <textarea name="rejection_reason" class="form-control" rows="3" placeholder="Enter reason…"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="prl-modal-btn cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="prl-modal-btn danger-btn">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        Reject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection