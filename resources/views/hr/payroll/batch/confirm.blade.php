@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
*, *::before, *::after { box-sizing: border-box; }
.prl-page { font-family: 'Sora', sans-serif; }

.prl-back-link { display:inline-flex;align-items:center;gap:8px;font-size:0.84rem;font-weight:700;color:#374151;text-decoration:none;margin-bottom:16px;padding:9px 16px;background:#fff;border:1px solid #e5e7eb;border-radius:10px;cursor:pointer;transition:all 0.13s; }
.prl-back-link:hover { border-color:#c8292a;color:#c8292a;background:#fff5f5; }

/* ── Batch hero ─────────────────────────────────────────────── */
.prl-batch-hero { background:#111827;border-radius:16px;padding:24px 28px;display:flex;align-items:center;justify-content:space-between;gap:20px;margin-bottom:20px;flex-wrap:wrap;position:relative;overflow:hidden; }
.prl-batch-hero::before { content:'';position:absolute;top:-50px;right:-50px;width:180px;height:180px;border-radius:50%;background:rgba(200,41,42,0.12);pointer-events:none; }
.prl-hero-left { position:relative;z-index:1; }
.prl-hero-eyebrow { font-size:0.67rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;margin-bottom:6px; }
.prl-hero-eyebrow.submitted { color:#8b5cf6; }
.prl-hero-eyebrow.rejected  { color:#ef4444; }
.prl-hero-title  { font-size:1.1rem;font-weight:800;color:#fff;margin:0 0 6px;letter-spacing:-0.02em; }
.prl-hero-period { font-family:'DM Mono',monospace;font-size:0.82rem;color:#6b7280; }
.prl-hero-chips  { display:flex;gap:10px;margin-top:14px;flex-wrap:wrap; }
.prl-hero-chip   { background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);border-radius:8px;padding:8px 14px;text-align:center; }
.prl-hero-chip-lbl { font-size:0.62rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#6b7280;display:block;margin-bottom:3px; }
.prl-hero-chip-val { font-family:'DM Mono',monospace;font-size:1rem;font-weight:700;color:#fff;font-variant-numeric:tabular-nums; }
.prl-hero-right  { position:relative;z-index:1;display:flex;flex-direction:column;align-items:flex-end;gap:8px; }

/* ── Buttons ────────────────────────────────────────────────── */
.prl-btn-finalize { display:inline-flex;align-items:center;gap:8px;padding:11px 22px;background:#c8292a;color:#fff;border:none;border-radius:10px;font-family:'Sora',sans-serif;font-size:0.845rem;font-weight:700;cursor:pointer;transition:background 0.15s,box-shadow 0.15s;box-shadow:0 4px 14px rgba(200,41,42,0.3);text-decoration:none;white-space:nowrap; }
.prl-btn-finalize:hover { background:#a81f20;color:#fff; }
.prl-btn-sec { display:inline-flex;align-items:center;gap:7px;padding:9px 16px;background:rgba(255,255,255,0.07);color:#9ca3af;border:1px solid rgba(255,255,255,0.1);border-radius:10px;font-family:'Sora',sans-serif;font-size:0.82rem;font-weight:600;text-decoration:none;cursor:pointer;transition:all 0.15s;white-space:nowrap; }
.prl-btn-sec:hover { background:rgba(255,255,255,0.12);color:#fff; }
.prl-btn-locked { display:inline-flex;align-items:center;gap:7px;padding:9px 16px;background:rgba(255,255,255,0.04);color:#4b5563;border:1px solid rgba(255,255,255,0.07);border-radius:10px;font-family:'Sora',sans-serif;font-size:0.82rem;font-weight:600;cursor:not-allowed; }
.prl-btn-danger { display:inline-flex;align-items:center;gap:7px;padding:9px 16px;background:rgba(225,29,72,0.12);color:#fca5a5;border:1px solid rgba(225,29,72,0.25);border-radius:10px;font-family:'Sora',sans-serif;font-size:0.82rem;font-weight:600;cursor:pointer;transition:all 0.15s;white-space:nowrap; }
.prl-btn-danger:hover { background:#e11d48;color:#fff;border-color:#e11d48; }

/* ── Prepare-all toolbar ────────────────────────────────────── */
.prl-bulk-toolbar {
    background:#fff;border:1px solid #e5e7eb;border-radius:12px;
    padding:12px 16px;display:flex;align-items:center;gap:12px;
    margin-bottom:0;flex-wrap:wrap;
}
.prl-bulk-toolbar-left { display:flex;align-items:center;gap:10px;flex:1; }
.prl-bulk-count { font-size:0.78rem;font-weight:600;color:#6b7280; }
.prl-bulk-count strong { color:#111827; }
.prl-btn-prepare-all {
    display:inline-flex;align-items:center;gap:7px;padding:8px 16px;
    background:#16a34a;color:#fff;border:none;border-radius:9px;
    font-family:'Sora',sans-serif;font-size:0.78rem;font-weight:700;
    cursor:pointer;transition:background 0.15s;white-space:nowrap;
}
.prl-btn-prepare-all:hover { background:#15803d; }
.prl-btn-prepare-sel {
    display:inline-flex;align-items:center;gap:7px;padding:8px 16px;
    background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;border-radius:9px;
    font-family:'Sora',sans-serif;font-size:0.78rem;font-weight:700;
    cursor:pointer;transition:all 0.15s;white-space:nowrap;
}
.prl-btn-prepare-sel:hover { background:#16a34a;color:#fff;border-color:#16a34a; }
.prl-btn-prepare-sel:disabled { opacity:0.4;cursor:not-allowed; }
.prl-btn-delete-sel {
    display:inline-flex;align-items:center;gap:7px;padding:8px 16px;
    background:#fef2f2;color:#e11d48;border:1px solid #fecaca;border-radius:9px;
    font-family:'Sora',sans-serif;font-size:0.78rem;font-weight:700;
    cursor:pointer;transition:all 0.15s;white-space:nowrap;
}
.prl-btn-delete-sel:hover { background:#e11d48;color:#fff;border-color:#e11d48; }
.prl-btn-delete-sel:disabled { opacity:0.4;cursor:not-allowed; }

/* ── Totals bar ─────────────────────────────────────────────── */
.prl-totals-bar { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:14px 20px;display:flex;gap:24px;align-items:center;margin-bottom:16px;flex-wrap:wrap; }
.prl-totals-lbl { font-size:0.67rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#9ca3af; }
.prl-totals-val { font-family:'DM Mono',monospace;font-size:1rem;font-weight:700;color:#111827;font-variant-numeric:tabular-nums; }
.prl-totals-val.green { color:#16a34a; }
.prl-totals-divider { width:1px;height:32px;background:#e5e7eb;flex-shrink:0; }

/* ── Table ──────────────────────────────────────────────────── */
.prl-table-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden; }
.prl-table-scroll { overflow-x:auto; }
.prl-table { width:100%;border-collapse:collapse;font-size:0.835rem; }
.prl-table thead tr { background:#f8f9fb;border-bottom:1px solid #e5e7eb; }
.prl-table thead th { padding:11px 16px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.09em;color:#6b7280;white-space:nowrap; }
.prl-table tbody tr { border-bottom:1px solid #f3f4f6;transition:background 0.12s; }
.prl-table tbody tr:last-child { border-bottom:none; }
.prl-table tbody tr.clickable { cursor:pointer; }
.prl-table tbody tr.clickable:hover { background:#fdf4f4; }
.prl-table tbody td { padding:12px 16px;color:#374151;vertical-align:middle; }

/* Checkbox col */
.prl-cb { width:16px;height:16px;cursor:pointer;accent-color:#c8292a; }

.prl-emp-cell { display:flex;align-items:center;gap:10px; }
.prl-emp-avatar { width:34px;height:34px;border-radius:50%;background:#f3f4f6;display:flex;align-items:center;justify-content:center;font-size:0.74rem;font-weight:700;color:#6b7280;flex-shrink:0;border:1.5px solid #e5e7eb;text-transform:uppercase; }
.prl-emp-name { font-weight:600;color:#111827;font-size:0.845rem; }
.prl-emp-meta { font-size:0.72rem;color:#9ca3af;margin-top:1px; }
.prl-mono { font-family:'DM Mono',monospace;font-size:0.82rem;font-variant-numeric:tabular-nums; }
.prl-mono.c-red   { color:#c8292a;font-weight:500; }
.prl-mono.c-green { color:#16a34a;font-weight:500; }
.prl-mono.c-bold  { color:#111827;font-weight:700; }

/* Status badges */
.prl-status { display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;white-space:nowrap; }
.prl-status::before { content:'';width:5px;height:5px;border-radius:50%; }
.prl-status.s-prepared  { background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0; }
.prl-status.s-prepared::before { background:#16a34a; }
.prl-status.s-submitted { background:#f5f3ff;color:#7c3aed;border:1px solid #ddd6fe; }
.prl-status.s-submitted::before { background:#8b5cf6; }

/* Action buttons */
.prl-actions { display:flex;align-items:center;gap:5px;justify-content:flex-end; }
.prl-action-btn { width:30px;height:30px;border-radius:7px;border:none;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;text-decoration:none;transition:background 0.13s,color 0.13s;background:#f4f5f7;color:#6b7280;padding:0; }
.prl-action-btn.edit:hover   { background:#fffbeb;color:#d97706; }
.prl-action-btn.remove:hover { background:#fff1f2;color:#e11d48; }
.prl-action-btn.locked { cursor:not-allowed;opacity:0.4; }

/* Flash */
.prl-flash { display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:10px;font-size:0.82rem;font-weight:500;margin-bottom:16px;animation:flashIn 0.3s ease; }
.prl-flash.success { background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d; }
.prl-flash.error   { background:#fff0f0;border:1px solid #fecaca;color:#c8292a; }
.prl-flash.info    { background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8; }
@keyframes flashIn { from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)} }

/* Lock notice */
.prl-locked-notice { display:flex;align-items:center;gap:10px;background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;font-size:0.8rem;border-radius:10px;padding:12px 16px;margin-bottom:16px; }

/* Add employee card */
.prl-add-emp-card { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:14px 16px;margin-bottom:16px;display:flex;gap:10px;align-items:center;flex-wrap:wrap; }
.prl-add-emp-card select { min-width:280px;max-width:100%;padding:8px 10px;border:1px solid #e5e7eb;border-radius:8px;font-size:.82rem;color:#374151;background:#f9fafb; }
.prl-add-emp-btn { display:inline-flex;align-items:center;gap:6px;padding:8px 14px;background:#c8292a;color:#fff;border:0;border-radius:8px;font-size:.8rem;font-weight:700;cursor:pointer; }
.prl-add-emp-btn:hover { background:#a81f20; }
.prl-bulk-dept-select { padding:8px 10px;border:1px solid #e5e7eb;border-radius:8px;font-size:.82rem;color:#374151;background:#f9fafb;min-width:180px;font-family:'Sora',sans-serif; }
.prl-bulk-dept-btn { display:inline-flex;align-items:center;gap:6px;padding:8px 14px;background:#1d4ed8;color:#fff;border:0;border-radius:8px;font-size:.8rem;font-weight:700;cursor:pointer;transition:background 0.15s; }
.prl-bulk-dept-btn:hover { background:#1e40af; }
</style>
@endpush

@section('content')
<div class="prl-page">

    @foreach(['success','error','info'] as $t)
        @if(session($t))
        <div class="prl-flash {{ $t }}">
            @if($t==='success')<svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            @else<svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>@endif
            {{ session($t) }}
        </div>
        @endif
    @endforeach

    {{-- Batch hero --}}
    <div class="prl-batch-hero">
        <div class="prl-hero-left">
            <div class="prl-hero-eyebrow {{ $batch->status }}">
                @if($batch->status === 'submitted' && !$batch->finalized_at) ● In progress — Review &amp; edit, then finalize
                @elseif($batch->status === 'submitted') ● Submitted — Edit or awaiting approval
                @elseif($batch->status === 'rejected') ● Rejected — Review note and edit to resubmit
                @else ● {{ ucfirst($batch->status) }}
                @endif
            </div>
            <h1 class="prl-hero-title">{{ $batch->display_name }}</h1>
            <div class="prl-hero-period">
                {{ $batch->period_start->format('F d, Y') }} — {{ $batch->period_end->format('F d, Y') }}
            </div>
            <div class="prl-hero-chips">
                <div class="prl-hero-chip">
                    <span class="prl-hero-chip-lbl">Employees</span>
                    <span class="prl-hero-chip-val">{{ $batch->employee_count }}</span>
                </div>
                <div class="prl-hero-chip">
                    <span class="prl-hero-chip-lbl">Total Net</span>
                    <span class="prl-hero-chip-val">&#8369;{{ number_format($batch->total_net_pay, 2) }}</span>
                </div>
                <div class="prl-hero-chip">
                    <span class="prl-hero-chip-lbl">Created</span>
                    <span class="prl-hero-chip-val" style="font-size:0.78rem;">{{ $batch->created_at->format('M d, h:i A') }}</span>
                </div>
            </div>
        </div>

        <div class="prl-hero-right">
            @if($batch->isEditable())
                <form action="{{ route('payroll.batch.finalize', $batch) }}" method="POST">
                    @csrf
                    <button type="submit" class="prl-btn-finalize">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Finalize &amp; Submit
                    </button>
                </form>
                {{-- Delete batch (destructive) --}}
                <form action="{{ route('payroll.batch.cancel', $batch) }}" method="POST" id="deleteBatchForm">
                    @csrf @method('DELETE')
                </form>
                <button type="button" class="prl-btn-danger" onclick="document.getElementById('deleteBatchModal').style.display='flex'">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Delete Batch
                </button>
            @elseif($batch->status === 'rejected')
                <form action="{{ route('payroll.batch.reopen', $batch) }}" method="POST">
                    @csrf
                    <button type="submit" class="prl-btn-finalize">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Reopen Batch
                    </button>
                </form>
            @else
                <span class="prl-btn-locked">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M7 11V7a5 5 0 0110 0v4"/></svg>
                    {{ ucfirst($batch->status) }}
                </span>
            @endif
        </div>
    </div>

    {{-- Delete confirmation modal --}}
    @if($batch->isEditable() || $batch->status === 'submitted')
    <div id="deleteBatchModal" style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;background:rgba(17,24,39,0.55);backdrop-filter:blur(2px);">
        <div style="background:#fff;border-radius:18px;padding:32px 28px;max-width:420px;width:90%;box-shadow:0 24px 60px rgba(17,24,39,0.22);font-family:'Sora',sans-serif;">
            <div style="display:flex;align-items:center;gap:14px;margin-bottom:18px;">
                <div style="width:44px;height:44px;border-radius:12px;background:#fff1f2;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="#e11d48" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <div>
                    <div style="font-size:1rem;font-weight:800;color:#111827;">Delete this batch?</div>
                    <div style="font-size:0.78rem;color:#9ca3af;margin-top:2px;">{{ $batch->display_name }}</div>
                </div>
            </div>
            <p style="font-size:0.85rem;color:#6b7280;line-height:1.6;margin:0 0 24px;">
                This will permanently delete the batch and all payroll records inside it. This action cannot be undone.
            </p>
            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" onclick="document.getElementById('deleteBatchModal').style.display='none'"
                    style="padding:9px 20px;border-radius:9px;border:1px solid #e5e7eb;background:#fff;color:#374151;font-family:'Sora',sans-serif;font-size:0.82rem;font-weight:700;cursor:pointer;">
                    Never mind
                </button>
                <button type="button" onclick="document.getElementById('deleteBatchForm').submit()"
                    style="padding:9px 20px;border-radius:9px;border:none;background:#e11d48;color:#fff;font-family:'Sora',sans-serif;font-size:0.82rem;font-weight:700;cursor:pointer;box-shadow:0 4px 14px rgba(225,29,72,0.3);">
                    Yes, delete it
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Prepare selected confirmation modal --}}
    <div id="prepareSelectedModal" style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;background:rgba(17,24,39,0.55);backdrop-filter:blur(2px);">
        <div style="background:#fff;border-radius:18px;padding:32px 28px;max-width:420px;width:90%;box-shadow:0 24px 60px rgba(17,24,39,0.22);font-family:'Sora',sans-serif;">
            <div style="display:flex;align-items:center;gap:14px;margin-bottom:18px;">
                <div style="width:44px;height:44px;border-radius:12px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="#16a34a" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <div style="font-size:1rem;font-weight:800;color:#111827;">Prepare selected payrolls?</div>
                    <div style="font-size:0.78rem;color:#9ca3af;margin-top:2px;" id="prepareCountDisplay"></div>
                </div>
            </div>
            <p style="font-size:0.85rem;color:#6b7280;line-height:1.6;margin:0 0 24px;">
                The selected payroll records will be marked as prepared and moved to the next stage.
            </p>
            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" onclick="document.getElementById('prepareSelectedModal').style.display='none'"
                    style="padding:9px 20px;border-radius:9px;border:1px solid #e5e7eb;background:#fff;color:#374151;font-family:'Sora',sans-serif;font-size:0.82rem;font-weight:700;cursor:pointer;">
                    Cancel
                </button>
                <button type="button" onclick="confirmPrepareSelected()"
                    style="padding:9px 20px;border-radius:9px;border:none;background:#16a34a;color:#fff;font-family:'Sora',sans-serif;font-size:0.82rem;font-weight:700;cursor:pointer;box-shadow:0 4px 14px rgba(22,163,74,0.3);">
                    Yes, prepare them
                </button>
            </div>
        </div>
    </div>

    {{-- Remove selected confirmation modal --}}
    <div id="removeSelectedModal" style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;background:rgba(17,24,39,0.55);backdrop-filter:blur(2px);">
        <div style="background:#fff;border-radius:18px;padding:32px 28px;max-width:420px;width:90%;box-shadow:0 24px 60px rgba(17,24,39,0.22);font-family:'Sora',sans-serif;">
            <div style="display:flex;align-items:center;gap:14px;margin-bottom:18px;">
                <div style="width:44px;height:44px;border-radius:12px;background:#fef2f2;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="#e11d48" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <div>
                    <div style="font-size:1rem;font-weight:800;color:#111827;">Remove selected payrolls?</div>
                    <div style="font-size:0.78rem;color:#9ca3af;margin-top:2px;" id="removeCountDisplay"></div>
                </div>
            </div>
            <p style="font-size:0.85rem;color:#6b7280;line-height:1.6;margin:0 0 24px;">
                The selected payroll records will be removed from the batch. This action cannot be undone.
            </p>
            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" onclick="document.getElementById('removeSelectedModal').style.display='none'"
                    style="padding:9px 20px;border-radius:9px;border:1px solid #e5e7eb;background:#fff;color:#374151;font-family:'Sora',sans-serif;font-size:0.82rem;font-weight:700;cursor:pointer;">
                    Cancel
                </button>
                <button type="button" onclick="confirmRemoveSelected()"
                    style="padding:9px 20px;border-radius:9px;border:none;background:#e11d48;color:#fff;font-family:'Sora',sans-serif;font-size:0.82rem;font-weight:700;cursor:pointer;box-shadow:0 4px 14px rgba(225,29,72,0.3);">
                    Yes, remove them
                </button>
            </div>
        </div>
    </div>

    @if($batch->status === 'rejected' && $batch->rejection_note)
        <div class="prl-flash error">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
            <strong>Rejection note:</strong> {{ $batch->rejection_note }}
        </div>
    @endif

    @if(!$batch->isEditable())
    <div class="prl-locked-notice">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M7 11V7a5 5 0 0110 0v4"/></svg>
        This batch is <strong>{{ $batch->status }}</strong> — payrolls are locked.
        @if($batch->finalizedBy) Submitted by {{ $batch->finalizedBy->name ?? 'system' }} on {{ $batch->finalized_at->format('M d, Y h:i A') }}. @endif
    </div>
    @endif

    {{-- Add employee panel --}}
    @if($batch->isEditable())
    <div class="prl-add-emp-card">
        <div style="display:flex;gap:16px;width:100%;flex-wrap:wrap;align-items:flex-end;">

            {{-- Add individual employee --}}
            <div style="flex:1;min-width:220px;">
                <div style="font-size:.72rem;color:#6b7280;font-weight:700;text-transform:uppercase;letter-spacing:.07em;margin-bottom:7px;">Add Employee</div>
                <form action="{{ route('payroll.batch.add-employee', $batch) }}" method="POST" style="display:flex;gap:8px;align-items:center;">
                    @csrf
                    <select name="user_id" required style="flex:1;min-width:0;padding:8px 10px;border:1px solid #e5e7eb;border-radius:8px;font-size:.82rem;color:#374151;background:#f9fafb;max-height:300px;overflow-y:scroll;">
                        <option value="">Select employee...</option>
                        @foreach($availableEmployees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }}{{ $emp->position ? ' — '.$emp->position : '' }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="prl-add-emp-btn" style="flex-shrink:0;">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Add
                    </button>
                </form>
            </div>

            {{-- Vertical divider --}}
            <div style="width:1px;background:#e5e7eb;align-self:stretch;flex-shrink:0;"></div>

            {{-- Add by department --}}
            <div style="flex:1;min-width:220px;">
                <div style="font-size:.72rem;color:#6b7280;font-weight:700;text-transform:uppercase;letter-spacing:.07em;margin-bottom:7px;">Add by Department</div>
                <form action="{{ route('payroll.batch.add-department', $batch) }}" method="POST" style="display:flex;gap:8px;align-items:center;">
                    @csrf
                    <select name="department" required class="prl-bulk-dept-select" style="flex:1;min-width:0;">
                        <option value="">Select department...</option>
                        <option value="all">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept }}">{{ $dept }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="prl-bulk-dept-btn" style="flex-shrink:0;">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Add
                    </button>
                </form>
            </div>

        </div>
        @if($availableEmployees->isEmpty())
            <span style="font-size:.75rem;color:#9ca3af;margin-top:6px;display:block;">All eligible employees already have payroll for this period.</span>
        @endif
    </div>
    @endif

    {{-- Totals bar --}}
    @php
        $totalGross      = $batch->payrolls->sum('gross_pay');
        $totalDeductions = $batch->payrolls->sum('total_deductions');
        $totalBonuses    = $batch->payrolls->sum('total_bonuses');
        $totalNet        = $batch->payrolls->sum('net_pay');
        $preparedCount   = $batch->payrolls->where('status', 'prepared')->count();
        $totalCount      = $batch->payrolls->count();
    @endphp
    <div class="prl-totals-bar">
        <div>
            <div class="prl-totals-lbl">Total Gross</div>
            <div class="prl-totals-val">&#8369;{{ number_format($totalGross, 2) }}</div>
        </div>
        <div class="prl-totals-divider"></div>
        <div>
            <div class="prl-totals-lbl">Total Bonuses</div>
            <div class="prl-totals-val" style="color:#7c3aed;">&#8369;{{ number_format($totalBonuses, 2) }}</div>
        </div>
        <div class="prl-totals-divider"></div>
        <div>
            <div class="prl-totals-lbl">Total Deductions</div>
            <div class="prl-totals-val">&#8369;{{ number_format($totalDeductions, 2) }}</div>
        </div>
        <div class="prl-totals-divider"></div>
        <div>
            <div class="prl-totals-lbl">Total Net Pay</div>
            <div class="prl-totals-val green">&#8369;{{ number_format($totalNet, 2) }}</div>
        </div>
        <div class="prl-totals-divider"></div>
        <div>
            <div class="prl-totals-lbl">Prepared</div>
            <div class="prl-totals-val">{{ $preparedCount }} / {{ $totalCount }}</div>
        </div>
    </div>

    {{-- Prepare-all toolbar (draft only) --}}
    @if($batch->isEditable() && $totalCount > 0)
    <form action="{{ route('payroll.batch.prepare-all', $batch) }}" method="POST" id="prepareAllForm">
        @csrf
        <div id="prepareAllIds"></div>

        <div class="prl-bulk-toolbar" style="margin-bottom:0;border-bottom-left-radius:0;border-bottom-right-radius:0;border-bottom:none;">
            <div class="prl-bulk-toolbar-left">
                <span class="prl-bulk-count">
                    <strong id="selCount">0</strong> selected
                </span>
            </div>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <button type="button" class="prl-btn-delete-sel" id="deleteSelBtn" disabled
                    onclick="submitRemoveSelected()">
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Remove Selected
                </button>
                <button type="button" class="prl-btn-prepare-sel" id="prepareSelBtn" disabled
                    onclick="submitPrepareSelected()">
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Prepare Selected
                </button>
            </div>
        </div>
    </form>
    @endif

    {{-- Employee table --}}
    <div class="prl-table-card" style="{{ ($batch->isEditable() && $totalCount > 0) ? 'border-top-left-radius:0;border-top-right-radius:0;border-top:none;' : '' }}">
        <div class="prl-table-scroll">
            <table class="prl-table">
                <thead>
                    <tr>
                        @if($batch->isEditable())
                        <th style="width:40px;">
                            <input type="checkbox" class="prl-cb" id="selectAll" title="Select all">
                        </th>
                        @endif
                        <th>Employee Name</th>
                        <th class="text-center">Days</th>
                        <th class="text-center">Basic Pay</th>
                        <th class="text-center">Additional Earnings</th>
                        <th class="text-center">Bonuses</th>
                        <th class="text-center">Deductions</th>
                        <th class="text-center">Net Pay</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($batch->payrolls as $i => $payroll)
                    @php
                        $initials    = strtoupper(substr($payroll->user->first_name??'U',0,1).substr($payroll->user->last_name??'',0,1));
                        $sc          = $payroll->status === 'prepared' ? 's-prepared' : ($payroll->status === 'submitted' ? 's-submitted' : 's-pending');
                        $otAllowances = $payroll->total_allowances;
                        $editUrl     = route('payroll.batch.edit-employee', [$batch, $payroll]);
                        // Holiday pay is stored in its own column (not in allowances)
                        $holidayPay = (float)($payroll->holiday_pay ?? 0);
                    @endphp
                    <tr class="{{ $batch->isEditable() ? 'clickable' : '' }}"
                        @if($batch->isEditable()) data-href="{{ $editUrl }}" @endif
                        onclick="{{ $batch->isEditable() ? 'rowClick(event, this)' : '' }}"
                    >
                        @if($batch->isEditable())
                        <td onclick="event.stopPropagation()">
                            <input type="checkbox" class="prl-cb row-cb" value="{{ $payroll->id }}"
                                onchange="updateSelection()">
                        </td>
                        @endif
                        <td>
                            <div class="prl-emp-cell">
                                <div>
                                    <div class="prl-emp-name">{{ $payroll->user->first_name }} {{ $payroll->user->last_name }}</div>
                                    <div class="prl-emp-meta">{{ $payroll->user->position ?? ($payroll->user->department ?? 'N/A') }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            @php
                                $absCount = \App\Models\Attendance::where('user_id', $payroll->user_id)
                                    ->whereBetween('date', [$batch->period_start, $batch->period_end])
                                    ->where('status', 'absent')
                                    ->count();
                            @endphp
                            <span class="prl-mono">{{ $payroll->days_worked }}</span>
                            @if($absCount > 0)
                                <br><span style="font-size:0.68rem;color:#c8292a;font-family:'DM Mono',monospace;">{{ $absCount }} absent</span>
                            @endif
                        </td>
                        <td class="text-center"><span class="prl-mono">&#8369;{{ number_format($payroll->basic_salary, 2) }}</span></td>
                        <td class="text-center">
                            @if($otAllowances > 0)
                                <div style="font-size:0.78rem;">
                                    <span class="prl-mono c-green">&#8369;{{ number_format($otAllowances, 2) }}</span>
                                </div>
                            @else
                                <span style="color:#d1d5db;font-size:0.75rem;">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($payroll->total_bonuses > 0)
                                <span class="prl-mono" style="color:#7c3aed;font-weight:500;">+&#8369;{{ number_format($payroll->total_bonuses, 2) }}</span>
                            @else
                                <span style="color:#d1d5db;font-size:0.75rem;">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($payroll->total_deductions > 0)
                                <span class="prl-mono c-red">&#8369;{{ number_format($payroll->total_deductions, 2) }}</span>
                            @else
                                <span style="color:#d1d5db;font-size:0.75rem;">—</span>
                            @endif
                        </td>
                        <td class="text-center"><span class="prl-mono c-bold">&#8369;{{ number_format($payroll->net_pay, 2) }}</span></td>
                        <td class="text-center">
                            <span class="prl-status {{ $sc }}">
                                {{ ucfirst($payroll->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="{{ $batch->isEditable() ? 10 : 9 }}" style="text-align:center;padding:40px;color:#9ca3af;font-size:0.82rem;">No payroll records in this batch yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
// ── Row click (navigate to show page, skip action cells) ─────────
function rowClick(event, row) {
    if (event.target.closest('a, button, form, input')) return;
    window.location = row.dataset.href;
}

// ── Close modal when clicking outside ────────────────────────────
function setupModalBackdropClose(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
}

// Setup backdrop close for all modals
setupModalBackdropClose('deleteBatchModal');
setupModalBackdropClose('prepareSelectedModal');
setupModalBackdropClose('removeSelectedModal');

// ── Checkbox selection ───────────────────────────────────────────
function updateSelection() {
    const cbs  = Array.from(document.querySelectorAll('.row-cb'));
    const sel  = cbs.filter(c => c.checked);
    const prepareBtn = document.getElementById('prepareSelBtn');
    const deleteBtn = document.getElementById('deleteSelBtn');
    const cnt  = document.getElementById('selCount');
    if (cnt) cnt.textContent = sel.length;
    if (prepareBtn) prepareBtn.disabled = sel.length === 0;
    if (deleteBtn) deleteBtn.disabled = sel.length === 0;
}

const selectAll = document.getElementById('selectAll');
if (selectAll) {
    selectAll.addEventListener('change', function () {
        document.querySelectorAll('.row-cb').forEach(cb => {
            cb.checked = this.checked;
        });
        updateSelection();
    });
}

// ── Prepare selected ─────────────────────────────────────────────
function submitPrepareSelected() {
    const ids = Array.from(document.querySelectorAll('.row-cb:checked')).map(c => c.value);
    if (!ids.length) return;
    document.getElementById('prepareCountDisplay').textContent = ids.length + ' record' + (ids.length !== 1 ? 's' : '');
    document.getElementById('prepareSelectedModal').style.display = 'flex';
    window.prepareIds = ids;
}

function confirmPrepareSelected() {
    const ids = window.prepareIds;
    if (!ids || !ids.length) return;
    const container = document.getElementById('prepareAllIds');
    container.innerHTML = '';
    ids.forEach(id => {
        const inp = document.createElement('input');
        inp.type = 'hidden';
        inp.name = 'payroll_ids[]';
        inp.value = id;
        container.appendChild(inp);
    });
    document.getElementById('prepareSelectedModal').style.display = 'none';
    document.getElementById('prepareAllForm').submit();
}

// ── Remove selected ──────────────────────────────────────────────
function submitRemoveSelected() {
    const ids = Array.from(document.querySelectorAll('.row-cb:checked')).map(c => c.value);
    if (!ids.length) return;
    document.getElementById('removeCountDisplay').textContent = ids.length + ' record' + (ids.length !== 1 ? 's' : '');
    document.getElementById('removeSelectedModal').style.display = 'flex';
    window.deleteIds = ids;
}

function confirmRemoveSelected() {
    const ids = window.deleteIds;
    if (!ids || !ids.length) return;
    const container = document.getElementById('prepareAllIds');
    container.innerHTML = '';
    ids.forEach(id => {
        const inp = document.createElement('input');
        inp.type = 'hidden';
        inp.name = 'payroll_ids[]';
        inp.value = id;
        container.appendChild(inp);
    });
    const form = document.getElementById('prepareAllForm');
    const originalAction = form.action;
    form.action = form.action.replace('prepare-all', 'remove-selected');
    document.getElementById('removeSelectedModal').style.display = 'none';
    form.submit();
    form.action = originalAction;
}
</script>
@endpush
