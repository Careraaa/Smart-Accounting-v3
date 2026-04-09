@extends('layouts.layout')

@push('styles')
    @include('employee._ui-styles')
@endpush

@section('content')
<div class="container-fluid empui-page empui-wrap">
    <div class="empui-backdrop"><div class="empui-grid"></div></div>
    <div class="empui-content">

        <div class="empui-hero">
            <div class="empui-hero-left">
                <h1 class="empui-title">My Leave Requests</h1>
                <p class="empui-sub">Track approvals, review history, and submit a new request in seconds.</p>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    <span class="empui-chip"><i class="feather-calendar"></i> {{ now()->format('l, F d, Y') }}</span>
                    <span class="empui-chip"><i class="feather-filter"></i> Filter: {{ ucfirst($status ?? 'all') }}</span>
                </div>
            </div>
            <div class="empui-hero-right">
                <a class="empui-btn" href="{{ route('employee.leaves.create') }}">
                    <i class="feather-plus"></i>
                    Request New Leave
                </a>
            </div>
        </div>

        <div class="empui-stats">
            <div class="empui-stat s-blue">
                <div class="empui-ico"><i class="feather-layers"></i></div>
                <div>
                    <div class="empui-lbl">Total</div>
                    <div class="empui-val empui-mono">{{ $totalLeaves }}</div>
                    <div class="empui-muted" style="margin-top:4px;">requests</div>
                </div>
            </div>
            <div class="empui-stat s-amber">
                <div class="empui-ico"><i class="feather-hourglass"></i></div>
                <div>
                    <div class="empui-lbl">Pending</div>
                    <div class="empui-val empui-mono">{{ $pendingLeaves }}</div>
                    <div class="empui-muted" style="margin-top:4px;">awaiting review</div>
                </div>
            </div>
            <div class="empui-stat s-green">
                <div class="empui-ico"><i class="feather-check-circle"></i></div>
                <div>
                    <div class="empui-lbl">Approved</div>
                    <div class="empui-val empui-mono">{{ $approvedLeaves }}</div>
                    <div class="empui-muted" style="margin-top:4px;">granted</div>
                </div>
            </div>
            <div class="empui-stat s-red">
                <div class="empui-ico"><i class="feather-x-circle"></i></div>
                <div>
                    <div class="empui-lbl">Rejected</div>
                    <div class="empui-val empui-mono">{{ $rejectedLeaves }}</div>
                    <div class="empui-muted" style="margin-top:4px;">needs update</div>
                </div>
            </div>
        </div>

        <div class="empui-card">
            <div class="empui-card-head">
                <p class="empui-card-title"><span class="empui-dot"></span> Requests</p>
                <div class="empui-filter">
                    <a href="{{ route('employee.leaves.index') }}" class="{{ ($status === 'all') ? 'is-active' : '' }}">All</a>
                    <a href="{{ route('employee.leaves.index', ['status' => 'pending']) }}" class="{{ ($status === 'pending') ? 'is-active' : '' }}">Pending</a>
                    <a href="{{ route('employee.leaves.index', ['status' => 'approved']) }}" class="{{ ($status === 'approved') ? 'is-active' : '' }}">Approved</a>
                    <a href="{{ route('employee.leaves.index', ['status' => 'rejected']) }}" class="{{ ($status === 'rejected') ? 'is-active' : '' }}">Rejected</a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover empui-table w-100 mb-0">
                    <thead>
                        <tr>
                            <th>Leave Type</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaves as $leave)
                            <tr>
                                <td>
                                    <div class="fw-bold" style="color:#111827;">{{ $leave->leave_type }}</div>
                                    <div class="empui-muted">Submitted {{ $leave->created_at?->diffForHumans() ?? '—' }}</div>
                                </td>
                                <td>
                                    <div class="empui-muted">
                                        {{ $leave->start_date->format('M d, Y') }} – {{ $leave->end_date->format('M d, Y') }}
                                    </div>
                                    @php $days = $leave->start_date->diffInDays($leave->end_date) + 1; @endphp
                                    <div class="fw-bold" style="color:#111827;">{{ $days }} day{{ $days != 1 ? 's' : '' }}</div>
                                </td>
                                <td>
                                    @php
                                        $pill = in_array($leave->status, ['pending','approved','rejected'], true) ? $leave->status : 'neutral';
                                    @endphp
                                    <span class="empui-pill {{ $pill }}">{{ ucfirst($leave->status) }}</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('employee.leaves.show', $leave) }}" class="empui-btn-sec" style="padding:7px 12px;border-radius:9px;">
                                        <i class="feather-eye"></i> View
                                    </a>
                                    @if($leave->status === 'pending')
                                        <a href="{{ route('employee.leaves.edit', $leave) }}" class="empui-btn-sec" style="padding:7px 12px;border-radius:9px;">
                                            <i class="feather-edit-2"></i> Edit
                                        </a>
                                        <form action="{{ route('employee.leaves.destroy', $leave) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="empui-btn-sec"
                                                style="padding:7px 12px;border-radius:9px;border-color:#fecdd3;color:#e11d48;background:#fff1f2;"
                                                onclick="return confirm('Cancel this leave request?')">
                                                <i class="feather-trash-2"></i> Cancel
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">
                                    <i class="feather-file-text d-block mb-2" style="font-size:28px; opacity:.3;"></i>
                                    No leave requests found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($leaves->hasPages())
                <div class="d-flex justify-content-between align-items-center px-3 py-2 flex-wrap gap-2">
                    <div class="small text-muted">
                        Showing
                        <strong>{{ $leaves->firstItem() }}</strong>
                        to
                        <strong>{{ $leaves->lastItem() }}</strong>
                        of
                        <strong>{{ $leaves->total() }}</strong>
                        entries
                    </div>
                    <div>
                        {{ $leaves->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
