@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
.lt-page { font-family: 'Sora', sans-serif; }

/* ── Topbar ─────────────────────────────────────────────────── */
.lt-topbar { display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px;flex-wrap:wrap; }
.lt-topbar-title { font-size:1.35rem;font-weight:800;color:#111827;letter-spacing:-0.02em;margin:0 0 2px; }
.lt-topbar-sub   { font-size:0.78rem;color:#9ca3af;margin:0; }
.lt-topbar-actions { display:flex;gap:8px;flex-wrap:wrap;align-items:center; }

.lt-btn-primary {
    display:inline-flex;align-items:center;gap:7px;padding:9px 16px;background:#111827;color:#fff;
    border:none;border-radius:10px;font-family:'Sora',sans-serif;font-size:0.82rem;
    font-weight:600;text-decoration:none;cursor:pointer;transition:all 0.15s;white-space:nowrap;
}
.lt-btn-primary:hover { background:#000;color:#fff; }

.lt-btn-sec {
    display:inline-flex;align-items:center;gap:7px;padding:9px 16px;background:#fff;color:#374151;
    border:1px solid #e5e7eb;border-radius:10px;font-family:'Sora',sans-serif;font-size:0.82rem;
    font-weight:600;text-decoration:none;cursor:pointer;transition:all 0.15s;white-space:nowrap;
}
.lt-btn-sec:hover { border-color:#c8292a;color:#c8292a;background:#fff5f5; }

/* ── Flash ──────────────────────────────────────────────────── */
.lt-flash { display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:10px;font-size:0.82rem;font-weight:500;margin-bottom:20px;animation:ltFlashIn 0.3s ease; }
.lt-flash.success { background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d; }
.lt-flash.error   { background:#fff0f0;border:1px solid #fecaca;color:#c8292a; }
@keyframes ltFlashIn { from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)} }

/* ── Filter bar ─────────────────────────────────────────────── */
.lt-filter-bar { display:flex;align-items:center;gap:8px;margin-bottom:14px;flex-wrap:wrap; }
.lt-filter-btn {
    display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:8px;
    font-family:'Sora',sans-serif;font-size:0.78rem;font-weight:600;text-decoration:none;
    border:1px solid #e5e7eb;background:#fff;color:#6b7280;transition:all 0.15s;
}
.lt-filter-btn:hover { border-color:#c8292a;color:#c8292a;background:#fff5f5; }
.lt-filter-btn.active { background:#111827;border-color:#111827;color:#fff; }

/* ── Table card ─────────────────────────────────────────────── */
.lt-table-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden; }
.lt-table { width:100%;border-collapse:collapse;font-size:0.835rem; }
.lt-table thead tr { background:#f8f9fb;border-bottom:1px solid #e5e7eb; }
.lt-table thead th { padding:11px 16px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.09em;color:#6b7280;white-space:nowrap;font-family:'Sora',sans-serif; }
.lt-table tbody tr { border-bottom:1px solid #f3f4f6;transition:background 0.1s; }
.lt-table tbody tr:last-child { border-bottom:none; }
.lt-table tbody tr:hover { background:#fafafa; }
.lt-table tbody td { padding:12px 16px;color:#374151;vertical-align:middle; }

.lt-name { font-weight:600;color:#111827;font-size:0.845rem; }
.lt-abbr { font-size:0.72rem;color:#9ca3af;margin-top:2px;font-family:'DM Mono',monospace; }

.lt-days-tag {
    display:inline-block;background:#fffbeb;color:#d97706;border:1px solid #fde68a;
    padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:700;
}
.lt-carry-yes { color:#16a34a;font-weight:600;font-size:0.82rem;display:inline-flex;align-items:center;gap:4px; }
.lt-carry-no  { color:#c8292a;font-weight:600;font-size:0.82rem;display:inline-flex;align-items:center;gap:4px; }
.lt-desc { font-size:0.78rem;color:#9ca3af; }

.lt-actions { display:flex;align-items:center;gap:5px;justify-content:center; }
.lt-action-btn { width:30px;height:30px;border-radius:7px;border:none;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;text-decoration:none;font-size:13px;transition:background 0.13s,color 0.13s;background:#f4f5f7;color:#6b7280;padding:0; }
.lt-action-btn:hover         { background:#fffbeb;color:#d97706; }
.lt-action-btn.danger:hover  { background:#fff1f2;color:#e11d48; }

/* ── Empty ──────────────────────────────────────────────────── */
.lt-empty { display:flex;flex-direction:column;align-items:center;justify-content:center;padding:56px 24px;text-align:center; }
.lt-empty-icon { width:56px;height:56px;background:#f3f4f6;border-radius:16px;display:flex;align-items:center;justify-content:center;margin-bottom:14px;color:#d1d5db; }
.lt-empty-title { font-size:0.9rem;font-weight:700;color:#374151;margin:0 0 6px; }
.lt-empty-sub   { font-size:0.78rem;color:#9ca3af;margin:0; }
</style>
@endpush

@section('content')
<div class="lt-page">

    {{-- Flash --}}
    @if(session('success'))
    <div class="lt-flash success">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="lt-flash error">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- Topbar --}}
    <div class="lt-topbar">
        <div>
            <h1 class="lt-topbar-title">Leave Types</h1>
            <p class="lt-topbar-sub">Manage leave types and policies</p>
        </div>
        <div class="lt-topbar-actions">
            <a href="{{ route('leave-type.create') }}" class="lt-btn-primary">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Add Leave Type
            </a>
        </div>
    </div>

    {{-- Filter bar --}}
    <div class="lt-filter-bar">
        <a href="{{ route('leave-type.index', ['status' => 'all']) }}"
           class="lt-filter-btn {{ $status === 'all' ? 'active' : '' }}">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            All Types
        </a>
        <a href="{{ route('leave-type.index', ['status' => 'active']) }}"
           class="lt-filter-btn {{ $status === 'active' ? 'active' : '' }}">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            Active
        </a>
    </div>

    {{-- Table --}}
    <div class="lt-table-card">
        <div style="overflow-x:auto;">
            <table class="lt-table">
                <thead>
                    <tr>
                        <th>Leave Type</th>
                        <th>Days Allowed</th>
                        <th>Carry Over</th>
                        <th>Description</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaveTypes as $leaveType)
                    <tr>
                        <td>
                            <div class="lt-name">{{ $leaveType->name }}</div>
                            @if($leaveType->abbreviation)
                                <div class="lt-abbr">{{ $leaveType->abbreviation }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="lt-days-tag">{{ $leaveType->days_allowed }} days</span>
                        </td>
                        <td>
                            @if($leaveType->carry_over)
                                <span class="lt-carry-yes">
                                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    Yes
                                </span>
                            @else
                                <span class="lt-carry-no">
                                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    No
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="lt-desc">{{ Str::limit($leaveType->description, 60) }}</span>
                        </td>
                        <td>
                            <div class="lt-actions">
                                <a href="{{ route('leave-type.edit', $leaveType) }}" class="lt-action-btn" title="Edit">
                                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form action="{{ route('leave-type.destroy', $leaveType) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this leave type?')">
                                    @csrf @method('DELETE')
                                    <button class="lt-action-btn danger" title="Delete">
                                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">
                            <div class="lt-empty">
                                <div class="lt-empty-icon">
                                    <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <p class="lt-empty-title">No leave types found</p>
                                <p class="lt-empty-sub">Add a leave type to get started.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($leaveTypes->hasPages())
        <div style="padding:14px 16px;border-top:1px solid #f3f4f6;">
            {{ $leaveTypes->links() }}
        </div>
        @endif
    </div>

</div>
@endsection