@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
*, *::before, *::after { box-sizing: border-box; }
.otp-page { font-family: 'Sora', sans-serif; }

/* ── Topbar ── */
.otp-topbar { display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px;flex-wrap:wrap; }
.otp-topbar-title { font-size:1.35rem;font-weight:800;color:#111827;letter-spacing:-0.02em;margin:0 0 2px; }
.otp-topbar-sub   { font-size:0.78rem;color:#9ca3af;margin:0; }
.otp-btn-back {
    display:inline-flex;align-items:center;gap:7px;padding:9px 16px;background:#fff;color:#374151;
    border:1px solid #e5e7eb;border-radius:10px;font-family:'Sora',sans-serif;font-size:0.82rem;
    font-weight:600;text-decoration:none;transition:all 0.15s;white-space:nowrap;
}
.otp-btn-back:hover { border-color:#c8292a;color:#c8292a;background:#fff5f5; }

/* ── Flash ── */
.otp-flash { display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:10px;font-size:0.82rem;font-weight:500;margin-bottom:20px; }
.otp-flash.success { background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d; }
.otp-flash.error   { background:#fff0f0;border:1px solid #fecaca;color:#c8292a; }

/* ── Status tabs ── */
.otp-tabs { display:flex;gap:6px;margin-bottom:20px;flex-wrap:wrap; }
.otp-tab {
    display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:10px;
    font-size:0.78rem;font-weight:700;text-decoration:none;border:1px solid #e5e7eb;
    background:#fff;color:#6b7280;transition:all 0.15s;
}
.otp-tab:hover { border-color:#c8292a;color:#c8292a; }
.otp-tab.active { background:#111827;color:#fff;border-color:#111827; }
.otp-tab-badge {
    display:inline-flex;align-items:center;justify-content:center;
    min-width:20px;height:20px;padding:0 5px;border-radius:999px;
    font-size:0.62rem;font-weight:800;
}
.otp-tab.active .otp-tab-badge { background:rgba(255,255,255,0.2);color:#fff; }
.otp-tab:not(.active) .otp-tab-badge { background:#f3f4f6;color:#6b7280; }
.otp-tab.tab-pending:not(.active) .otp-tab-badge   { background:#fffbeb;color:#d97706; }
.otp-tab.tab-approved:not(.active) .otp-tab-badge  { background:#f0fdf4;color:#16a34a; }
.otp-tab.tab-rejected:not(.active) .otp-tab-badge  { background:#fff0f0;color:#c8292a; }

/* ── Filter bar ── */
.otp-filter-bar { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:10px 14px;display:flex;align-items:center;gap:10px;margin-bottom:16px;flex-wrap:wrap; }
.otp-filter-select { border:1px solid #e5e7eb;border-radius:8px;padding:7px 12px;font-size:0.82rem;font-family:'Sora',sans-serif;color:#374151;background:#f9fafb;outline:none;cursor:pointer; }
.otp-filter-select:focus { border-color:#c8292a; }

/* ── Table card ── */
.otp-table-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden; }
.otp-table-scroll { overflow-x:auto; }
.otp-table { width:100%;border-collapse:collapse;font-size:0.835rem; }
.otp-table thead tr { background:#f8f9fb;border-bottom:1px solid #e5e7eb; }
.otp-table thead th { padding:11px 16px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.09em;color:#6b7280;white-space:nowrap; }
.otp-table tbody tr { border-bottom:1px solid #f3f4f6;transition:background 0.12s; }
.otp-table tbody tr:last-child { border-bottom:none; }
.otp-table tbody tr:hover { background:#fafafa; }
.otp-table tbody td { padding:12px 16px;color:#374151;vertical-align:middle; }

/* Employee cell */
.otp-emp-cell   { display:flex;align-items:center;gap:10px; }
.otp-emp-avatar { width:34px;height:34px;border-radius:50%;background:#f3f4f6;display:flex;align-items:center;justify-content:center;font-size:0.72rem;font-weight:700;color:#6b7280;flex-shrink:0;border:1.5px solid #e5e7eb;text-transform:uppercase; }
.otp-emp-name   { font-weight:600;color:#111827;font-size:0.845rem;line-height:1.2; }
.otp-emp-dept   { font-size:0.72rem;color:#9ca3af;margin-top:1px; }

/* Type pill */
.otp-type { display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:20px;font-size:0.72rem;font-weight:700; }
.otp-type.ot { background:#f0f9ff;color:#0284c7;border:1px solid #bae6fd; }
.otp-type.ut { background:#fffbeb;color:#d97706;border:1px solid #fde68a; }

/* Status badge */
.otp-status { display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em; }
.otp-status.pending  { background:#fffbeb;color:#d97706;border:1px solid #fde68a; }
.otp-status.approved { background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0; }
.otp-status.rejected { background:#fff0f0;color:#c8292a;border:1px solid #fecaca; }

/* Action buttons */
.otp-actions { display:flex;gap:6px;align-items:center; }
.otp-btn-approve {
    display:inline-flex;align-items:center;gap:5px;padding:6px 12px;
    background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;border-radius:8px;
    font-family:'Sora',sans-serif;font-size:0.75rem;font-weight:700;cursor:pointer;transition:all 0.15s;
}
.otp-btn-approve:hover { background:#16a34a;color:#fff;border-color:#16a34a; }
.otp-btn-reject-open {
    display:inline-flex;align-items:center;gap:5px;padding:6px 12px;
    background:#fff0f0;color:#c8292a;border:1px solid #fecaca;border-radius:8px;
    font-family:'Sora',sans-serif;font-size:0.75rem;font-weight:700;cursor:pointer;transition:all 0.15s;
}
.otp-btn-reject-open:hover { background:#c8292a;color:#fff;border-color:#c8292a; }

/* ── Reject modal ── */
.otp-modal-overlay {
    display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:9999;
    align-items:center;justify-content:center;padding:20px;
}
.otp-modal-overlay.open { display:flex; }
.otp-modal {
    background:#fff;border-radius:16px;padding:28px;width:100%;max-width:460px;
    box-shadow:0 20px 60px rgba(0,0,0,0.18);animation:modalIn 0.2s ease;
}
@keyframes modalIn { from{opacity:0;transform:scale(0.96)} to{opacity:1;transform:scale(1)} }
.otp-modal-title { font-size:1rem;font-weight:800;color:#111827;margin:0 0 4px; }
.otp-modal-sub   { font-size:0.78rem;color:#9ca3af;margin:0 0 20px; }
.otp-modal-label { font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#6b7280;display:block;margin-bottom:6px; }
.otp-modal-textarea {
    width:100%;border:1px solid #e5e7eb;border-radius:8px;padding:10px 12px;
    font-size:0.845rem;font-family:'Sora',sans-serif;color:#111827;background:#f9fafb;
    outline:none;resize:vertical;min-height:90px;transition:border-color 0.15s;
}
.otp-modal-textarea:focus { border-color:#c8292a;box-shadow:0 0 0 3px rgba(200,41,42,0.08);background:#fff; }
.otp-modal-footer { display:flex;gap:10px;margin-top:18px; }
.otp-modal-cancel {
    flex:1;padding:10px;background:#f9fafb;color:#374151;border:1px solid #e5e7eb;border-radius:9px;
    font-family:'Sora',sans-serif;font-size:0.845rem;font-weight:600;cursor:pointer;transition:all 0.15s;
}
.otp-modal-cancel:hover { background:#f3f4f6; }
.otp-modal-submit {
    flex:2;padding:10px;background:#c8292a;color:#fff;border:none;border-radius:9px;
    font-family:'Sora',sans-serif;font-size:0.845rem;font-weight:700;cursor:pointer;transition:background 0.15s;
}
.otp-modal-submit:hover { background:#a81f20; }

/* ── Empty state ── */
.otp-empty { display:flex;flex-direction:column;align-items:center;justify-content:center;padding:52px 24px;text-align:center; }
.otp-empty-icon  { width:52px;height:52px;background:#f3f4f6;border-radius:14px;display:flex;align-items:center;justify-content:center;margin-bottom:12px;color:#d1d5db; }
.otp-empty-title { font-size:0.88rem;font-weight:700;color:#374151;margin:0 0 4px; }
.otp-empty-sub   { font-size:0.75rem;color:#9ca3af;margin:0; }

/* ── Pagination ── */
.otp-pagination { padding:14px 18px;border-top:1px solid #f3f4f6;display:flex;justify-content:flex-end; }
</style>
@endpush

@section('content')
<div class="otp-page">

    {{-- Flash --}}
    @foreach(['success','error'] as $t)
        @if(session($t))
        <div class="otp-flash {{ $t }}">
            @if($t==='success')
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            @else
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
            @endif
            {{ session($t) }}
        </div>
        @endif
    @endforeach

    {{-- Topbar --}}
    <div class="otp-topbar">
        <div>
            <h1 class="otp-topbar-title">OT / UT Requests</h1>
            <p class="otp-topbar-sub">Review and act on overtime &amp; undertime requests submitted by employees.</p>
        </div>
        <a href="{{ route('overtime.index') }}" class="otp-btn-back">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Overview
        </a>
    </div>

    {{-- Status tabs --}}
    <div class="otp-tabs">
        <a href="{{ route('overtime.pending', ['status' => 'pending', 'type' => $type]) }}"
           class="otp-tab tab-pending {{ $status === 'pending' ? 'active' : '' }}">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 7v5l3 3"/></svg>
            Pending
            <span class="otp-tab-badge">{{ $pendingCount }}</span>
        </a>
        <a href="{{ route('overtime.pending', ['status' => 'approved', 'type' => $type]) }}"
           class="otp-tab tab-approved {{ $status === 'approved' ? 'active' : '' }}">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            Approved
            <span class="otp-tab-badge">{{ $approvedCount }}</span>
        </a>
        <a href="{{ route('overtime.pending', ['status' => 'rejected', 'type' => $type]) }}"
           class="otp-tab tab-rejected {{ $status === 'rejected' ? 'active' : '' }}">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            Rejected
            <span class="otp-tab-badge">{{ $rejectedCount }}</span>
        </a>
    </div>

    {{-- Filter bar --}}
    <div class="otp-filter-bar">
        <label style="font-size:0.78rem;font-weight:600;color:#6b7280;">Type:</label>
        <select class="otp-filter-select" onchange="applyTypeFilter(this.value)">
            <option value="all"       {{ $type === 'all'       ? 'selected' : '' }}>All Types</option>
            <option value="overtime"  {{ $type === 'overtime'  ? 'selected' : '' }}>Overtime</option>
            <option value="undertime" {{ $type === 'undertime' ? 'selected' : '' }}>Undertime</option>
        </select>
        <span style="font-size:0.78rem;color:#9ca3af;margin-left:auto;">
            {{ $requests->total() }} {{ Str::plural('request', $requests->total()) }}
        </span>
    </div>

    {{-- Table --}}
    <div class="otp-table-card">
        <div class="otp-table-scroll">
            <table class="otp-table">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th class="text-center">Hours</th>
                        <th class="text-end">Amount</th>
                        <th>Reason</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                        @php
                            $emp      = $req->employee;
                            $name     = $emp ? trim(($emp->first_name ?? '') . ' ' . ($emp->last_name ?? '')) : 'Unknown';
                            $initials = strtoupper(substr($emp->first_name ?? 'U', 0, 1) . substr($emp->last_name ?? '', 0, 1));
                        @endphp
                        <tr style="cursor:pointer;" onclick="window.location='{{ route('overtime.show', $req) }}'">
                            <td>
                                <div class="otp-emp-cell">
                                    <div class="otp-emp-avatar">{{ $initials }}</div>
                                    <div>
                                        <div class="otp-emp-name">{{ $name }}</div>
                                        <div class="otp-emp-dept">{{ $emp->department ?? '—' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="otp-type {{ $req->type === 'overtime' ? 'ot' : 'ut' }}">
                                    @if($req->type === 'overtime')
                                        <svg width="9" height="9" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                        Overtime
                                    @else
                                        <svg width="9" height="9" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
                                        Undertime
                                    @endif
                                </span>
                            </td>
                            <td style="font-family:'DM Mono',monospace;font-size:0.82rem;white-space:nowrap;">
                                {{ $req->date->format('M d, Y') }}
                            </td>
                            <td class="text-center" style="font-family:'DM Mono',monospace;font-size:0.82rem;font-weight:700;">
                                {{ number_format($req->hours, 1) }}h
                            </td>
                            <td class="text-end" style="font-family:'DM Mono',monospace;font-size:0.82rem;font-weight:700;color:{{ $req->type === 'overtime' ? '#16a34a' : '#c8292a' }};">
                                {{ $req->type === 'overtime' ? '+' : '' }}₱{{ number_format(abs($req->amount), 2) }}
                            </td>
                            <td style="max-width:200px;">
                                <div style="font-size:0.80rem;color:#6b7280;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $req->reason }}">
                                    {{ $req->reason }}
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="otp-status {{ $req->status }}">{{ ucfirst($req->status) }}</span>
                            </td>
                            <td class="text-center" onclick="event.stopPropagation()">
                                <div class="otp-actions" style="justify-content:center;">
                                    @if($req->status === 'pending')
                                        {{-- Approve --}}
                                        <form action="{{ route('overtime.approve', $req) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="otp-btn-approve" title="Approve">
                                                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                Approve
                                            </button>
                                        </form>
                                        {{-- Reject --}}
                                        <button type="button" class="otp-btn-reject-open"
                                            onclick="event.stopPropagation(); openRejectModal({{ $req->id }}, '{{ addslashes($name) }}')"
                                            title="Reject">
                                            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                            Reject
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="otp-empty">
                                    <div class="otp-empty-icon">
                                        <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <p class="otp-empty-title">
                                        @if($status === 'pending') No pending requests
                                        @elseif($status === 'approved') No approved requests
                                        @else No rejected requests
                                        @endif
                                    </p>
                                    <p class="otp-empty-sub">
                                        @if($status === 'pending') All caught up — no requests awaiting review.
                                        @else Try switching tabs to see other requests.
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($requests->hasPages())
        <div class="otp-pagination">
            {{ $requests->links() }}
        </div>
        @endif
    </div>

</div>

{{-- Reject modal --}}
<div class="otp-modal-overlay" id="rejectModal">
    <div class="otp-modal">
        <h2 class="otp-modal-title">Reject Request</h2>
        <p class="otp-modal-sub" id="rejectModalSub">Provide a reason for rejecting this request.</p>
        <form id="rejectForm" method="POST">
            @csrf
            <label class="otp-modal-label" for="modal_rejection_reason">
                Rejection Reason <span style="color:#c8292a;">*</span>
            </label>
            <textarea
                name="rejection_reason"
                id="modal_rejection_reason"
                class="otp-modal-textarea"
                placeholder="Enter reason for rejection…"
                required
            ></textarea>
            <div class="otp-modal-footer">
                <button type="button" class="otp-modal-cancel" onclick="closeRejectModal()">Cancel</button>
                <button type="submit" class="otp-modal-submit">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="display:inline;margin-right:5px;"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    Confirm Rejection
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function applyTypeFilter(type) {
    const url = new URL(window.location.href);
    url.searchParams.set('type', type);
    window.location.href = url.toString();
}

function openRejectModal(id, name) {
    document.getElementById('rejectModalSub').textContent = 'Provide a reason for rejecting ' + name + '\'s request.';
    document.getElementById('rejectForm').action = '/overtime/' + id + '/reject';
    document.getElementById('modal_rejection_reason').value = '';
    document.getElementById('rejectModal').classList.add('open');
    setTimeout(() => document.getElementById('modal_rejection_reason').focus(), 100);
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.remove('open');
}

// Close on overlay click
document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) closeRejectModal();
});

// Close on Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeRejectModal();
});
</script>
@endpush
