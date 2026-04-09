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
                <h1 class="empui-title">Overtime / Undertime</h1>
                <p class="empui-sub">Submit, track, and manage your OT/UT requests.</p>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    <span class="empui-chip"><i class="feather-calendar"></i> {{ now()->format('l, F d, Y') }}</span>
                    <span class="empui-chip"><i class="feather-filter"></i> Filter: {{ ucfirst($status ?? 'all') }}</span>
                </div>
            </div>
            <div class="empui-hero-right">
                <a class="empui-btn" href="{{ route('employee.overtime-undertime.create') }}">
                    <i class="feather-plus"></i>
                    Submit Request
                </a>
            </div>
        </div>

        <div class="empui-stats">
            <div class="empui-stat s-blue">
                <div class="empui-ico"><i class="feather-layers"></i></div>
                <div>
                    <div class="empui-lbl">Total</div>
                    <div class="empui-val empui-mono">{{ $totalRequests }}</div>
                    <div class="empui-muted" style="margin-top:4px;">requests</div>
                </div>
            </div>
            <div class="empui-stat s-amber">
                <div class="empui-ico"><i class="feather-hourglass"></i></div>
                <div>
                    <div class="empui-lbl">Pending</div>
                    <div class="empui-val empui-mono">{{ $pendingRequests }}</div>
                    <div class="empui-muted" style="margin-top:4px;">awaiting review</div>
                </div>
            </div>
            <div class="empui-stat s-green">
                <div class="empui-ico"><i class="feather-check-circle"></i></div>
                <div>
                    <div class="empui-lbl">Approved</div>
                    <div class="empui-val empui-mono">{{ $approvedRequests }}</div>
                    <div class="empui-muted" style="margin-top:4px;">accepted</div>
                </div>
            </div>
            <div class="empui-stat s-red">
                <div class="empui-ico"><i class="feather-x-circle"></i></div>
                <div>
                    <div class="empui-lbl">Rejected</div>
                    <div class="empui-val empui-mono">{{ $rejectedRequests }}</div>
                    <div class="empui-muted" style="margin-top:4px;">needs update</div>
                </div>
            </div>
        </div>

        <div class="empui-card">
            <div class="empui-card-head">
                <p class="empui-card-title"><span class="empui-dot"></span> Requests</p>
                <div class="empui-filter">
                    <a href="{{ route('employee.overtime-undertime.index') }}" class="{{ ($status === 'all') ? 'is-active' : '' }}">All</a>
                    <a href="{{ route('employee.overtime-undertime.index', ['status' => 'pending']) }}" class="{{ ($status === 'pending') ? 'is-active' : '' }}">Pending</a>
                    <a href="{{ route('employee.overtime-undertime.index', ['status' => 'approved']) }}" class="{{ ($status === 'approved') ? 'is-active' : '' }}">Approved</a>
                    <a href="{{ route('employee.overtime-undertime.index', ['status' => 'rejected']) }}" class="{{ ($status === 'rejected') ? 'is-active' : '' }}">Rejected</a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover empui-table w-100 mb-0">
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
                            <tr>
                                <td>
                                    <div class="fw-bold" style="color:#111827;">{{ $request->date->format('M d, Y') }}</div>
                                    <div class="empui-muted">{{ $request->date->format('l') }}</div>
                                </td>
                                <td>
                                    @if($request->type === 'overtime')
                                        <span class="empui-pill active">Overtime</span>
                                    @else
                                        <span class="empui-pill pending">Undertime</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-bold empui-mono" style="color:#111827;">{{ number_format($request->hours, 2) }}h</span>
                                </td>
                                <td>
                                    <span class="empui-muted">{{ Str::limit($request->reason, 60) }}</span>
                                </td>
                                <td>
                                    @php
                                        $pill = in_array($request->status, ['pending','approved','rejected'], true) ? $request->status : 'neutral';
                                    @endphp
                                    <span class="empui-pill {{ $pill }}">{{ ucfirst($request->status) }}</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('employee.overtime-undertime.show', $request->id) }}" class="empui-btn-sec" style="padding:7px 12px;border-radius:9px;">
                                        <i class="feather-eye"></i> View
                                    </a>
                                    @if($request->status === 'pending')
                                        <a href="{{ route('employee.overtime-undertime.edit', $request->id) }}" class="empui-btn-sec" style="padding:7px 12px;border-radius:9px;">
                                            <i class="feather-edit-2"></i> Edit
                                        </a>
                                        <form action="{{ route('employee.overtime-undertime.destroy', $request->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="empui-btn-sec"
                                                style="padding:7px 12px;border-radius:9px;border-color:#fecdd3;color:#e11d48;background:#fff1f2;"
                                                onclick="return confirm('Are you sure you want to delete this request?')">
                                                <i class="feather-trash-2"></i> Delete
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="feather-inbox d-block mb-2" style="font-size:28px; opacity:.3;"></i>
                                    <p class="text-muted mb-0" style="font-size:.845rem;">No overtime/undertime requests found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($requests->hasPages())
                <div class="d-flex justify-content-between align-items-center px-3 py-2 flex-wrap gap-2">
                    <div class="small text-muted">
                        Showing
                        <strong>{{ $requests->firstItem() }}</strong>
                        to
                        <strong>{{ $requests->lastItem() }}</strong>
                        of
                        <strong>{{ $requests->total() }}</strong>
                        entries
                    </div>
                    <div>
                        {{ $requests->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
