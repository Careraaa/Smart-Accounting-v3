@extends('layouts.layout')

@push('styles')
    @include('employee._ui-styles')
@endpush

@section('content')
<div class="container-fluid empui-page empui-wrap">
    <div class="empui-backdrop"><div class="empui-grid"></div></div>
    <div class="empui-content">

        {{-- Hero --}}
        <div class="empui-hero">
            <div class="empui-hero-left">
                <h1 class="empui-title">My Leave Requests</h1>
                <p class="empui-sub">Track approvals, review history, and submit a new request.</p>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    <span class="empui-chip">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                        {{ now()->format('l, F d, Y') }}
                    </span>
                    <span class="empui-chip">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                        Filter: {{ ucfirst($status ?? 'all') }}
                    </span>
                </div>
            </div>
            <div class="empui-hero-right">
                <a class="empui-btn" href="{{ route('employee.leaves.create') }}">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Request New Leave
                </a>
            </div>
        </div>

        {{-- Stat cards --}}
        <div class="empui-stats">
            <div class="empui-stat s-blue">
                <div class="empui-ico">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div>
                    <div class="empui-lbl">Total</div>
                    <div class="empui-val empui-mono">{{ $totalLeaves }}</div>
                    <div class="empui-muted" style="margin-top:4px;">requests submitted</div>
                </div>
            </div>
            <div class="empui-stat s-amber">
                <div class="empui-ico">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
                </div>
                <div>
                    <div class="empui-lbl">Pending</div>
                    <div class="empui-val empui-mono">{{ $pendingLeaves }}</div>
                    <div class="empui-muted" style="margin-top:4px;">awaiting review</div>
                </div>
            </div>
            <div class="empui-stat s-green">
                <div class="empui-ico">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="empui-lbl">Approved</div>
                    <div class="empui-val empui-mono">{{ $approvedLeaves }}</div>
                    <div class="empui-muted" style="margin-top:4px;">granted</div>
                </div>
            </div>
            <div class="empui-stat s-red">
                <div class="empui-ico">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="empui-lbl">Rejected</div>
                    <div class="empui-val empui-mono">{{ $rejectedLeaves }}</div>
                    <div class="empui-muted" style="margin-top:4px;">not approved</div>
                </div>
            </div>
        </div>

        {{-- Leave balance --}}
        @if($balances->count() > 0)
        <div class="empui-card">
            <div class="empui-card-head">
                <span class="empui-card-title">
                    <span class="empui-dot"></span>
                    Leave Balance — {{ now()->year }}
                </span>
            </div>
            <div class="table-responsive">
                <table class="table empui-table w-100 mb-0">
                    <thead>
                        <tr>
                            <th>Leave Type</th>
                            <th class="text-center">Total Days</th>
                            <th class="text-center">Used</th>
                            <th class="text-center">Remaining</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($balances as $balance)
                            <tr>
                                <td>
                                    <div class="fw-bold" style="color:#111827;font-size:.845rem;">{{ $balance->leaveType?->name ?? 'N/A' }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="empui-mono fw-bold" style="color:#111827;">{{ $balance->total_days }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="empui-mono fw-bold" style="color:#374151;">{{ $balance->used_days }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="empui-mono fw-bold" style="color:{{ $balance->remaining_days > 0 ? '#15803d' : '#be123c' }};">
                                        {{ $balance->remaining_days }}
                                    </span>
                                </td>
                                <td>
                                    @if($balance->remaining_days > 0)
                                        <span class="empui-pill approved">Available</span>
                                    @else
                                        <span class="empui-pill rejected">Exhausted</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Requests table --}}
        <div class="empui-card">
            <div class="empui-card-head">
                <span class="empui-card-title">
                    <span class="empui-dot"></span>
                    Requests
                </span>
                <div class="empui-filter">
                    <a href="{{ route('employee.leaves.index') }}"                              class="{{ ($status === 'all')      ? 'is-active' : '' }}">All</a>
                    <a href="{{ route('employee.leaves.index', ['status' => 'pending']) }}"    class="{{ ($status === 'pending')   ? 'is-active' : '' }}">Pending</a>
                    <a href="{{ route('employee.leaves.index', ['status' => 'approved']) }}"   class="{{ ($status === 'approved')  ? 'is-active' : '' }}">Approved</a>
                    <a href="{{ route('employee.leaves.index', ['status' => 'rejected']) }}"   class="{{ ($status === 'rejected')  ? 'is-active' : '' }}">Rejected</a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table empui-table w-100 mb-0">
                    <thead>
                        <tr>
                            <th>Leave Type</th>
                            <th>Duration</th>
                            <th>Status</th>
                            @if($leaves->where('status', 'pending')->count())
                            <th class="text-end">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaves as $leave)
                            @php $pill = in_array($leave->status, ['pending','approved','rejected'], true) ? $leave->status : 'neutral'; @endphp
                            <tr class="empui-clickable-row" style="cursor:pointer;" onclick="window.location='{{ route('employee.leaves.show', $leave) }}'">
                                <td>
                                    <div class="fw-bold" style="color:#111827;font-size:.845rem;">{{ $leave->leaveType?->name ?? 'N/A' }}</div>
                                    <div class="empui-muted">Submitted {{ $leave->created_at?->diffForHumans() ?? '—' }}</div>
                                </td>
                                <td>
                                    <div class="empui-muted">{{ $leave->start_date->format('M d, Y') }} – {{ $leave->end_date->format('M d, Y') }}</div>
                                    <div class="fw-bold empui-mono" style="color:#111827;">{{ $leave->days }} day{{ $leave->days != 1 ? 's' : '' }}</div>
                                </td>
                                <td>
                                    <span class="empui-pill {{ $pill }}">{{ ucfirst($leave->status) }}</span>
                                </td>
                                @if($leaves->where('status', 'pending')->count())
                                <td class="text-end" onclick="event.stopPropagation()">
                                    <div style="display:flex;gap:5px;justify-content:flex-end;flex-wrap:wrap;">
                                        @if($leave->status === 'pending')
                                            <a href="{{ route('employee.leaves.edit', $leave) }}" class="empui-tbl-btn edit">
                                                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                Edit
                                            </a>
                                            <form action="{{ route('employee.leaves.destroy', $leave) }}" method="POST" style="display:inline;" onsubmit="return confirm('Cancel this leave request?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="empui-tbl-btn del">
                                                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path stroke-linecap="round" stroke-linejoin="round" d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6m5 0V4a1 1 0 011-1h2a1 1 0 011 1v2"/></svg>
                                                    Cancel
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="empui-empty">
                                        <svg class="empui-empty-icon" width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                        <p class="empui-empty-text">No leave requests found.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($leaves->hasPages())
                <div class="d-flex justify-content-between align-items-center px-4 py-3 flex-wrap gap-2" style="border-top:1px solid #f3f4f6;">
                    <div style="font-size:.78rem;color:#9ca3af;">
                        Showing <strong>{{ $leaves->firstItem() }}</strong>–<strong>{{ $leaves->lastItem() }}</strong> of <strong>{{ $leaves->total() }}</strong>
                    </div>
                    {{ $leaves->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
