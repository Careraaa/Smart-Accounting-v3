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
                <h1 class="empui-title">Overtime / Undertime</h1>
                <p class="empui-sub">Submit, track, and manage your OT/UT requests.</p>
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
                <a class="empui-btn" href="{{ route('employee.overtime-undertime.create') }}">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Submit Request
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
                    <div class="empui-val empui-mono">{{ $totalRequests }}</div>
                    <div class="empui-muted" style="margin-top:4px;">requests submitted</div>
                </div>
            </div>
            <div class="empui-stat s-amber">
                <div class="empui-ico">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
                </div>
                <div>
                    <div class="empui-lbl">Pending</div>
                    <div class="empui-val empui-mono">{{ $pendingRequests }}</div>
                    <div class="empui-muted" style="margin-top:4px;">awaiting review</div>
                </div>
            </div>
            <div class="empui-stat s-green">
                <div class="empui-ico">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="empui-lbl">Approved</div>
                    <div class="empui-val empui-mono">{{ $approvedRequests }}</div>
                    <div class="empui-muted" style="margin-top:4px;">accepted</div>
                </div>
            </div>
            <div class="empui-stat s-red">
                <div class="empui-ico">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="empui-lbl">Rejected</div>
                    <div class="empui-val empui-mono">{{ $rejectedRequests }}</div>
                    <div class="empui-muted" style="margin-top:4px;">not approved</div>
                </div>
            </div>
        </div>

        {{-- Requests table --}}
        <div class="empui-card">
            <div class="empui-card-head">
                <span class="empui-card-title">
                    <span class="empui-dot"></span>
                    Requests
                </span>
                <div class="empui-filter">
                    <a href="{{ route('employee.overtime-undertime.index') }}"                              class="{{ ($status === 'all')      ? 'is-active' : '' }}">All</a>
                    <a href="{{ route('employee.overtime-undertime.index', ['status' => 'pending']) }}"    class="{{ ($status === 'pending')   ? 'is-active' : '' }}">Pending</a>
                    <a href="{{ route('employee.overtime-undertime.index', ['status' => 'approved']) }}"   class="{{ ($status === 'approved')  ? 'is-active' : '' }}">Approved</a>
                    <a href="{{ route('employee.overtime-undertime.index', ['status' => 'rejected']) }}"   class="{{ ($status === 'rejected')  ? 'is-active' : '' }}">Rejected</a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table empui-table w-100 mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Hours</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $request)
                            @php $pill = in_array($request->status, ['pending','approved','rejected'], true) ? $request->status : 'neutral'; @endphp
                            <tr>
                                <td>
                                    <div class="fw-bold" style="color:#111827;font-size:.845rem;">{{ $request->date->format('M d, Y') }}</div>
                                    <div class="empui-muted">{{ $request->date->format('l') }}</div>
                                </td>
                                <td>
                                    <span class="empui-pill {{ $request->type === 'overtime' ? 'overtime' : 'undertime' }}">
                                        {{ ucfirst($request->type) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-bold empui-mono" style="color:#111827;">{{ number_format($request->hours, 2) }}h</span>
                                </td>
                                <td>
                                    <span class="empui-muted">{{ Str::limit($request->reason, 60) }}</span>
                                </td>
                                <td>
                                    <span class="empui-pill {{ $pill }}">{{ ucfirst($request->status) }}</span>
                                </td>
                                <td class="text-end">
                                    <div style="display:flex;gap:5px;justify-content:flex-end;flex-wrap:wrap;">
                                        <a href="{{ route('employee.overtime-undertime.show', $request->id) }}" class="empui-tbl-btn">
                                            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            View
                                        </a>
                                        @if($request->status === 'pending')
                                            <a href="{{ route('employee.overtime-undertime.edit', $request->id) }}" class="empui-tbl-btn edit">
                                                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                Edit
                                            </a>
                                            <form action="{{ route('employee.overtime-undertime.destroy', $request->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this request?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="empui-tbl-btn del">
                                                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path stroke-linecap="round" stroke-linejoin="round" d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6m5 0V4a1 1 0 011-1h2a1 1 0 011 1v2"/></svg>
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empui-empty">
                                        <svg class="empui-empty-icon" width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                        <p class="empui-empty-text">No overtime / undertime requests found.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($requests->hasPages())
                <div class="d-flex justify-content-between align-items-center px-4 py-3 flex-wrap gap-2" style="border-top:1px solid #f3f4f6;">
                    <div style="font-size:.78rem;color:#9ca3af;">
                        Showing <strong>{{ $requests->firstItem() }}</strong>–<strong>{{ $requests->lastItem() }}</strong> of <strong>{{ $requests->total() }}</strong>
                    </div>
                    {{ $requests->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
