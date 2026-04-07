@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
.lv-page { font-family: 'Sora', sans-serif; }

.lv-topbar { display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px;flex-wrap:wrap; }
.lv-topbar-title { font-size:1.35rem;font-weight:800;color:#111827;letter-spacing:-0.02em;margin:0 0 2px; }
.lv-topbar-sub   { font-size:0.78rem;color:#9ca3af;margin:0; }

.lv-stats { display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:24px; }
@media(max-width:700px){ .lv-stats{grid-template-columns:1fr;} }
.lv-stat { background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:18px 20px;display:flex;align-items:flex-start;gap:14px;position:relative;overflow:hidden;transition:box-shadow 0.15s; }
.lv-stat:hover { box-shadow:0 4px 20px rgba(0,0,0,0.07); }
.lv-stat::after { content:'';position:absolute;bottom:0;left:0;right:0;height:3px;border-radius:0 0 14px 14px;background:#c8292a; }
.lv-stat-icon { width:40px;height:40px;border-radius:10px;background:#fff0f0;color:#c8292a;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.lv-stat-label { font-size:0.67rem;font-weight:700;text-transform:uppercase;letter-spacing:0.09em;color:#9ca3af;margin-bottom:4px; }
.lv-stat-value { font-size:1.6rem;font-weight:800;color:#111827;line-height:1;font-variant-numeric:tabular-nums;font-family:'DM Mono',monospace; }
.lv-stat-sub   { font-size:0.73rem;color:#9ca3af;margin-top:4px; }

.lv-filter-bar { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:12px 16px;display:flex;align-items:center;gap:10px;margin-bottom:14px;flex-wrap:wrap; }
.lv-search-wrap { position:relative;flex:1;min-width:180px; }
.lv-search-wrap svg { position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#9ca3af;pointer-events:none; }
.lv-search-input { width:100%;border:1px solid #e5e7eb;border-radius:8px;padding:8px 12px 8px 34px;font-size:0.82rem;font-family:'Sora',sans-serif;color:#111827;background:#f9fafb;outline:none;transition:border-color 0.15s; }
.lv-search-input:focus { border-color:#c8292a;background:#fff;box-shadow:0 0 0 3px rgba(200,41,42,0.08); }
.lv-filter-select { border:1px solid #e5e7eb;border-radius:8px;padding:8px 12px;font-size:0.82rem;font-family:'Sora',sans-serif;color:#374151;background:#f9fafb;outline:none;cursor:pointer;flex:1;min-width:140px; }
.lv-filter-select:focus { border-color:#c8292a; }
.lv-filter-btn { display:inline-flex;align-items:center;gap:6px;padding:8px 14px;background:#111827;color:#fff;border:none;border-radius:8px;font-family:'Sora',sans-serif;font-size:0.8rem;font-weight:600;cursor:pointer;white-space:nowrap; }
.lv-filter-btn:hover { background:#000; }

.lv-flash { display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:10px;font-size:0.82rem;font-weight:500;margin-bottom:16px;animation:lvFlash 0.3s ease; }
.lv-flash.success { background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d; }
@keyframes lvFlash { from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)} }

.lv-table-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden; }
.lv-table { width:100%;border-collapse:collapse;font-size:0.835rem; }
.lv-table thead tr { background:#f8f9fb;border-bottom:1px solid #e5e7eb; }
.lv-table thead th { padding:11px 16px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.09em;color:#6b7280;white-space:nowrap;font-family:'Sora',sans-serif; }
.lv-table tbody tr { border-bottom:1px solid #f3f4f6;transition:background 0.1s; }
.lv-table tbody tr:last-child { border-bottom:none; }
.lv-table tbody tr:hover { background:#fafafa; }
.lv-table tbody td { padding:12px 16px;color:#374151;vertical-align:middle; }

.lv-emp-cell { display:flex;align-items:center;gap:10px; }
.lv-emp-avatar { width:32px;height:32px;border-radius:50%;background:#f3f4f6;display:flex;align-items:center;justify-content:center;font-size:0.72rem;font-weight:700;color:#6b7280;flex-shrink:0;border:1.5px solid #e5e7eb;text-transform:uppercase; }
.lv-emp-name { font-weight:600;color:#111827;font-size:0.845rem; }

.lv-badge { display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;white-space:nowrap; }
.lv-badge.rejected { background:#fff0f0;color:#c8292a;border:1px solid #fecaca; }

.lv-date-tag  { font-family:'DM Mono',monospace;font-size:0.78rem;color:#374151; }
.lv-days-tag  { font-weight:700;color:#111827;font-size:0.845rem; }
.lv-muted     { font-size:0.78rem;color:#9ca3af;font-family:'DM Mono',monospace; }
.lv-role-text { font-size:0.72rem;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:0.5px; }
.lv-rejection { font-size:0.78rem;color:#c8292a;max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:block; }

.lv-action-btn { width:30px;height:30px;border-radius:7px;border:none;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;text-decoration:none;font-size:13px;transition:background 0.13s,color 0.13s;background:#f4f5f7;color:#6b7280;padding:0; }
.lv-action-btn:hover { background:#eff6ff;color:#3b82f6; }

.lv-empty { display:flex;flex-direction:column;align-items:center;justify-content:center;padding:56px 24px;text-align:center; }
.lv-empty-icon { width:56px;height:56px;background:#f3f4f6;border-radius:16px;display:flex;align-items:center;justify-content:center;margin-bottom:14px;color:#d1d5db; }
.lv-empty-title { font-size:0.9rem;font-weight:700;color:#374151;margin:0 0 6px; }
.lv-empty-sub   { font-size:0.78rem;color:#9ca3af;margin:0; }
</style>
@endpush

@section('content')
<div class="lv-page">

    @if(session('success'))
    <div class="lv-flash success">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif

    <div class="lv-topbar">
        <div>
            <h1 class="lv-topbar-title">Rejected Leaves</h1>
            <p class="lv-topbar-sub">View rejected leave requests</p>
        </div>
    </div>

    <div class="lv-stats">
        <div class="lv-stat">
            <div class="lv-stat-icon"><svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 9l-6 6M9 9l6 6"/></svg></div>
            <div><div class="lv-stat-label">Total Rejected</div><div class="lv-stat-value">{{ $rejectedLeaves }}</div><div class="lv-stat-sub">Rejected leaves</div></div>
        </div>
        <div class="lv-stat">
            <div class="lv-stat-icon"><svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" d="M16 2v4M8 2v4M3 10h18"/></svg></div>
            <div><div class="lv-stat-label">This Week</div><div class="lv-stat-value">{{ $thisWeekLeaves }}</div><div class="lv-stat-sub">Rejected requests</div></div>
        </div>
        <div class="lv-stat">
            <div class="lv-stat-icon"><svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" d="M16 2v4M8 2v4M3 10h18"/></svg></div>
            <div><div class="lv-stat-label">This Month</div><div class="lv-stat-value">{{ $thisMonthLeaves }}</div><div class="lv-stat-sub">Total rejected</div></div>
        </div>
    </div>

    <form method="GET" action="{{ route('leave.pending') }}">
        <input type="hidden" name="status" value="rejected">
        <div class="lv-filter-bar">
            <select name="department" class="lv-filter-select">
                <option value="">All Departments</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept }}" @selected(request('department') === $dept)>{{ $dept }}</option>
                @endforeach
            </select>
            <select name="leave_type" class="lv-filter-select">
                <option value="">All Leave Types</option>
                @foreach($leaveTypeStats as $stat)
                    <option value="{{ $stat->leave_type }}" @selected(request('leave_type') === $stat->leave_type)>{{ $stat->leave_type }}</option>
                @endforeach
            </select>
            <div class="lv-search-wrap">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
                <input type="text" name="search" class="lv-search-input" placeholder="Search employee…" value="{{ request('search') }}">
            </div>
            <button type="submit" class="lv-filter-btn">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
                Filter
            </button>
        </div>
    </form>

    <div class="lv-table-card">
        <div style="overflow-x:auto;">
            <table class="lv-table">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Leave Type</th>
                        <th>Dates</th>
                        <th>Days</th>
                        <th>Rejected By</th>
                        <th>Rejected Date</th>
                        <th>Reason</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaves as $leave)
                    @php $initials = strtoupper(substr($leave->employee->first_name??'U',0,1).substr($leave->employee->last_name??'',0,1)); @endphp
                    <tr>
                        <td>
                            <div class="lv-emp-cell">
                                <div class="lv-emp-avatar">{{ $initials }}</div>
                                <div class="lv-emp-name">{{ $leave->employee->first_name ?? 'N/A' }} {{ $leave->employee->last_name ?? '' }}</div>
                            </div>
                        </td>
                        <td><span class="lv-muted">{{ $leave->employee->department ?? 'N/A' }}</span></td>
                        <td><span class="lv-badge rejected">{{ $leave->leave_type }}</span></td>
                        <td><span class="lv-date-tag">{{ $leave->start_date->format('M d') }} – {{ $leave->end_date->format('M d, Y') }}</span></td>
                        <td>
                            @php $days = $leave->start_date->diffInDays($leave->end_date) + 1; @endphp
                            <span class="lv-days-tag">{{ $days }}d</span>
                        </td>
                        <td>
                            @if($leave->approvedBy)
                                <span class="lv-role-text">{{ strtoupper(str_replace('_',' ',$leave->approvedBy->role)) }}</span>
                            @else
                                <span class="lv-muted">N/A</span>
                            @endif
                        </td>
                        <td><span class="lv-muted">{{ $leave->updated_at->format('M d, Y') }}</span></td>
                        <td>
                            <span class="lv-rejection" title="{{ $leave->rejection_reason }}">{{ Str::limit($leave->rejection_reason ?? 'N/A', 28) }}</span>
                        </td>
                        <td style="text-align:center;">
                            <a href="{{ route('leave.show', $leave) }}" class="lv-action-btn" title="View">
                                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9">
                        <div class="lv-empty">
                            <div class="lv-empty-icon"><svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg></div>
                            <p class="lv-empty-title">No rejected leave requests</p>
                            <p class="lv-empty-sub">Nothing to show here yet.</p>
                        </div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($leaves->hasPages())
        <div style="padding:14px 16px;border-top:1px solid #f3f4f6;">{{ $leaves->links() }}</div>
        @endif
    </div>

</div>
@endsection