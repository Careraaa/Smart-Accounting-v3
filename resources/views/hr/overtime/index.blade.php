@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
.prl-page{font-family:'Sora',sans-serif}
.prl-topbar{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px;flex-wrap:wrap}
.prl-topbar-title{font-size:1.35rem;font-weight:800;color:#111827;letter-spacing:-0.02em;margin:0 0 2px}
.prl-topbar-sub{font-size:0.78rem;color:#9ca3af;margin:0}
.prl-topbar-actions{display:flex;gap:8px;flex-wrap:wrap;align-items:center}
.prl-btn-primary{display:inline-flex;align-items:center;gap:7px;padding:9px 16px;background:#c8292a;color:#fff;border:none;border-radius:10px;font-family:'Sora',sans-serif;font-size:0.82rem;font-weight:700;text-decoration:none;cursor:pointer;transition:all .15s;box-shadow:0 2px 10px rgba(200,41,42,.3)}
.prl-btn-primary:hover{background:#a81f20;color:#fff}
.prl-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:24px}
@media(max-width:1100px){.prl-stats{grid-template-columns:repeat(2,1fr)}}
@media(max-width:600px){.prl-stats{grid-template-columns:1fr}}
.prl-stat{background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:18px 20px;display:flex;align-items:flex-start;gap:14px;position:relative;overflow:hidden;transition:box-shadow .15s}
.prl-stat:hover{box-shadow:0 4px 20px rgba(0,0,0,.07)}
.prl-stat::after{content:'';position:absolute;bottom:0;left:0;right:0;height:3px;border-radius:0 0 14px 14px}
.prl-stat.s-red::after{background:#c8292a}.prl-stat.s-green::after{background:#16a34a}
.prl-stat.s-amber::after{background:#d97706}.prl-stat.s-blue::after{background:#0284c7}
.prl-stat-icon{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.prl-stat.s-red .prl-stat-icon{background:#fff0f0;color:#c8292a}
.prl-stat.s-green .prl-stat-icon{background:#f0fdf4;color:#16a34a}
.prl-stat.s-amber .prl-stat-icon{background:#fffbeb;color:#d97706}
.prl-stat.s-blue .prl-stat-icon{background:#f0f9ff;color:#0284c7}
.prl-stat-label{font-size:.67rem;font-weight:700;text-transform:uppercase;letter-spacing:.09em;color:#9ca3af;margin-bottom:4px}
.prl-stat-value{font-size:1.6rem;font-weight:800;color:#111827;line-height:1;font-variant-numeric:tabular-nums;font-family:'DM Mono',monospace}
.prl-stat-sub{font-size:.73rem;color:#9ca3af;margin-top:4px}
.prl-filter-bar{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:12px 16px;display:flex;align-items:center;gap:10px;margin-bottom:14px;flex-wrap:wrap}
.prl-search-wrap{position:relative;flex:1;min-width:180px}
.prl-search-wrap svg{position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#9ca3af;pointer-events:none}
.prl-search-input{width:100%;border:1px solid #e5e7eb;border-radius:8px;padding:8px 12px 8px 34px;font-size:.82rem;font-family:'Sora',sans-serif;color:#111827;background:#f9fafb;outline:none;transition:border-color .15s}
.prl-search-input:focus{border-color:#c8292a;background:#fff;box-shadow:0 0 0 3px rgba(200,41,42,.08)}
.prl-filter-select{border:1px solid #e5e7eb;border-radius:8px;padding:8px 12px;font-size:.82rem;font-family:'Sora',sans-serif;color:#374151;background:#f9fafb;outline:none;cursor:pointer}
.prl-filter-select:focus{border-color:#c8292a}
.prl-btn-filter{display:inline-flex;align-items:center;gap:6px;padding:8px 14px;background:#111827;color:#fff;border:none;border-radius:8px;font-family:'Sora',sans-serif;font-size:.82rem;font-weight:600;cursor:pointer;transition:background .15s;text-decoration:none}
.prl-btn-filter:hover{background:#000;color:#fff}
.prl-section-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;flex-wrap:wrap;gap:10px}
.prl-section-title{font-size:.88rem;font-weight:700;color:#111827;margin:0;display:flex;align-items:center;gap:8px}
.prl-dot{width:8px;height:8px;border-radius:50%;background:#c8292a;display:inline-block}
.prl-table-card{background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden}
.prl-table{width:100%;border-collapse:collapse;font-size:.835rem}
.prl-table thead tr{background:#f8f9fb;border-bottom:1px solid #e5e7eb}
.prl-table thead th{padding:11px 16px;font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.09em;color:#6b7280;white-space:nowrap;font-family:'Sora',sans-serif}
.prl-table tbody tr{border-bottom:1px solid #f3f4f6;transition:background .1s}
.prl-table tbody tr:last-child{border-bottom:none}
.prl-table tbody tr:hover{background:#fafafa}
.prl-table tbody td{padding:12px 16px;color:#374151;vertical-align:middle}
.prl-table-scroll{overflow-x:auto}
.prl-table-footer{padding:12px 16px;border-top:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px}
.prl-table-footer-note{font-size:.78rem;color:#9ca3af}
.prl-table-footer-note strong{color:#374151}
.prl-emp-cell{display:flex;align-items:center;gap:10px}
.prl-emp-avatar{width:32px;height:32px;border-radius:50%;background:#f3f4f6;display:flex;align-items:center;justify-content:center;font-size:.72rem;font-weight:700;color:#6b7280;flex-shrink:0;border:1.5px solid #e5e7eb;text-transform:uppercase}
.prl-emp-name{font-weight:600;color:#111827;font-size:.845rem}
.prl-mono{font-family:'DM Mono',monospace;font-size:.82rem;font-variant-numeric:tabular-nums}
.prl-mono.c-bold{color:#111827;font-weight:700}
.prl-status{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;white-space:nowrap}
.prl-status::before{content:'';width:5px;height:5px;border-radius:50%}
.prl-status.s-pending{background:#fffbeb;color:#d97706;border:1px solid #fde68a}.prl-status.s-pending::before{background:#d97706}
.prl-status.s-approved{background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0}.prl-status.s-approved::before{background:#16a34a}
.prl-status.s-rejected{background:#fff0f0;color:#c8292a;border:1px solid #fecaca}.prl-status.s-rejected::before{background:#ef4444}
.prl-type-ot{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:.68rem;font-weight:700;text-transform:uppercase;background:#f0f9ff;color:#0284c7;border:1px solid #bae6fd}
.prl-type-ut{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:.68rem;font-weight:700;text-transform:uppercase;background:#fffbeb;color:#d97706;border:1px solid #fde68a}
.prl-actions{display:flex;align-items:center;gap:5px;justify-content:flex-end}
.prl-action-btn{width:30px;height:30px;border-radius:7px;border:none;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;text-decoration:none;font-size:13px;transition:background .13s,color .13s;background:#f4f5f7;color:#6b7280;padding:0}
.prl-action-btn:hover{background:#eff6ff;color:#3b82f6}
.prl-action-btn.edit:hover{background:#fffbeb;color:#d97706}
.prl-action-btn.danger:hover{background:#fff1f2;color:#e11d48}
.prl-empty{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:56px 24px;text-align:center}
.prl-empty-icon{width:56px;height:56px;background:#f3f4f6;border-radius:16px;display:flex;align-items:center;justify-content:center;margin-bottom:14px;color:#d1d5db}
.prl-empty-title{font-size:.9rem;font-weight:700;color:#374151;margin:0 0 6px}
.prl-empty-sub{font-size:.78rem;color:#9ca3af;margin:0}
</style>
@endpush

@section('content')
<div class="prl-page">

    <div class="prl-topbar">
        <div>
            <h1 class="prl-topbar-title">Overtime / Undertime</h1>
            <p class="prl-topbar-sub">Manage overtime and undertime records</p>
        </div>
        <div class="prl-topbar-actions">
            <a href="{{ route('overtime.create') }}" class="prl-btn-primary">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                New Record
            </a>
        </div>
    </div>

    <div class="prl-stats">
        <div class="prl-stat s-blue">
            <div class="prl-stat-icon"><svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
            <div>
                <div class="prl-stat-label">Total Records</div>
                <div class="prl-stat-value">{{ $totalRecords }}</div>
                <div class="prl-stat-sub">all time</div>
            </div>
        </div>
        <div class="prl-stat s-blue">
            <div class="prl-stat-icon"><svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 6v6l4 2"/></svg></div>
            <div>
                <div class="prl-stat-label">Overtime</div>
                <div class="prl-stat-value">{{ $overtimeCount }}</div>
                <div class="prl-stat-sub">{{ number_format($totalOvertimeHours, 2) }} hrs total</div>
            </div>
        </div>
        <div class="prl-stat s-amber">
            <div class="prl-stat-icon"><svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 8v4l-2 2"/></svg></div>
            <div>
                <div class="prl-stat-label">Undertime</div>
                <div class="prl-stat-value">{{ $undertimeCount }}</div>
                <div class="prl-stat-sub">{{ number_format($totalUndertimeHours, 2) }} hrs total</div>
            </div>
        </div>
        <div class="prl-stat s-red">
            <div class="prl-stat-icon"><svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 6v6l4 2"/></svg></div>
            <div>
                <div class="prl-stat-label">Pending</div>
                <div class="prl-stat-value">{{ $pendingRecords }}</div>
                <div class="prl-stat-sub">awaiting approval</div>
            </div>
        </div>
    </div>

    <div class="prl-section-head">
        <h2 class="prl-section-title"><span class="prl-dot"></span> Records</h2>
    </div>

    <form method="GET" action="{{ route('overtime.index') }}">
        <div class="prl-filter-bar">
            <div class="prl-search-wrap">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
                <input type="text" name="search" class="prl-search-input" placeholder="Search employee…" value="{{ request('search') }}">
            </div>
            <select name="type" class="prl-filter-select">
                <option value="all">All Types</option>
                <option value="overtime"  {{ request('type') === 'overtime'  ? 'selected' : '' }}>Overtime</option>
                <option value="undertime" {{ request('type') === 'undertime' ? 'selected' : '' }}>Undertime</option>
            </select>
            <select name="status" class="prl-filter-select">
                <option value="all">All Statuses</option>
                <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            <button type="submit" class="prl-btn-filter">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                Filter
            </button>
        </div>
    </form>

    <div class="prl-table-card">
        <div class="prl-table-scroll">
            <table class="prl-table">
                <thead><tr>
                    @php
                        $cols = ['user_id'=>'Employee','date'=>'Date','type'=>'Type','hours'=>'Hours','status'=>'Status'];
                    @endphp
                    @foreach($cols as $col => $label)
                    <th>
                        <a href="{{ route('overtime.index', array_merge(request()->all(), ['sort_by'=>$col,'sort_order'=>$sortBy===$col&&$sortOrder==='asc'?'desc':'asc'])) }}"
                           style="display:flex;align-items:center;gap:5px;color:#6b7280;text-decoration:none;font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.09em;">
                            {{ $label }}
                            @if($sortBy===$col)
                                <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    @if($sortOrder==='asc')<path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/>
                                    @else<path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>@endif
                                </svg>
                            @else
                                <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="opacity:.3"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
                            @endif
                        </a>
                    </th>
                    @endforeach
                    <th class="text-end">Actions</th>
                </tr></thead>
                <tbody>
                @forelse($overtimeRecords as $record)
                    @php
                        $initials = strtoupper(substr($record->employee->first_name ?? 'U', 0, 1) . substr($record->employee->last_name ?? '', 0, 1));
                        $sc = match($record->status) { 'approved'=>'s-approved','rejected'=>'s-rejected',default=>'s-pending' };
                    @endphp
                    <tr>
                        <td>
                            <div class="prl-emp-cell">
                                <div class="prl-emp-avatar">{{ $initials }}</div>
                                <div class="prl-emp-name">{{ $record->employee->first_name ?? 'N/A' }} {{ $record->employee->last_name ?? '' }}</div>
                            </div>
                        </td>
                        <td><span style="font-size:.82rem;color:#374151;">{{ $record->date->format('M d, Y') }}</span></td>
                        <td>
                            @if($record->type === 'overtime')
                                <span class="prl-type-ot">Overtime</span>
                            @else
                                <span class="prl-type-ut">Undertime</span>
                            @endif
                        </td>
                        <td><span class="prl-mono c-bold">{{ number_format($record->hours, 2) }} hrs</span></td>
                        <td><span class="prl-status {{ $sc }}">{{ ucfirst($record->status) }}</span></td>
                        <td>
                            <div class="prl-actions">
                                <a href="{{ route('overtime.show', $record) }}" class="prl-action-btn" title="View / Action">
                                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">
                        <div class="prl-empty">
                            <div class="prl-empty-icon"><svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 6v6l4 2"/></svg></div>
                            <p class="prl-empty-title">No records found</p>
                            <p class="prl-empty-sub">Try adjusting your filters.</p>
                        </div>
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($overtimeRecords->hasPages())
        <div class="prl-table-footer">
            <div class="prl-table-footer-note">
                Showing <strong>{{ $overtimeRecords->firstItem() }}</strong> – <strong>{{ $overtimeRecords->lastItem() }}</strong> of <strong>{{ $overtimeRecords->total() }}</strong>
            </div>
            {{ $overtimeRecords->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script src="{{ asset('js/HR/overtime-management.js') }}"></script>
@endpush